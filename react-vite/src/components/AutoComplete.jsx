import * as React from 'react';

import TextField from '@mui/material/TextField';
import Autocomplete from '@mui/material/Autocomplete';
import CircularProgress from '@mui/material/CircularProgress';
import PropTypes from 'prop-types';

import { useLanguage } from '../providers/LanguageProvider';

import { fetchDataAddress, fetchDataTerminals } from '../transport/transport';

import { debounce } from '@mui/material/utils';
import { useQwqer } from "../providers/QwqerProvider";


//filters terminals
function filterValue(opts,value){
  return opts.filter(opt=>`${opt.id} ${opt.name})`.match(value)!== null)
}

AutoComplete.prototypes={
  //runs when input value changes
  onValueChange:PropTypes.func,
}

export default function AutoComplete({onValueChange}) {
  const { qwqer } = useQwqer(); 
//state dla otkrytija  blocka
  const [open, setOpen] = React.useState(false);
//array with substitution elements  
  const [options, setOptions] = React.useState([]);

//loading state when autocomplete  is loading data
  const [loading,setLoading] = React.useState(open && options.length === 0)

//loading = false && open == true && opts == []  == no data
//loading == true && open == true && opts == []  == loading .... 


//source that is responsible for switching fetching functions,    
  const [source, setSource] = React.useState("");//terminals address
 
  const { t } = useLanguage();

  //funcion responsible for appearence

  const showSpinner = ()=>{
    setLoading(true);
    setOpen(true);
    setOptions([]);
  }
  
  const showNoData = ()=>{
    setLoading(false);
    setOpen(true);
    setOptions([]);
  }

  const showData = (v)=>{

    if (v){
     console.log("showData - showing data")
     console.log(v)
      setLoading(false);
      setOpen(true);
      setOptions(v);
    }else{
     console.log("showData - showing nodata data")
     console.log(v)
      showNoData();
    }
    
  }

  const hideDropdown = () => {
    setOpen(false);
    setLoading(false)
  }

  //loading state logic

  


  //bind with html events
  const bindHtmlEvent = (e)=>{
    console.log('qwqer bindHtmlEvent called',e)
    if (e !== false){
      if (e === "qwqer.omnivaparcelterminal"){
        setSource("terminals")
      }
      if (e === "qwqer.expressdelivery"){
        setSource("address")
      }
      if (e === "qwqer.scheduleddelivery"){
        setSource("address")
      }
    }else{
      setSource(false)
    }
  }

  React.useEffect(() => {
    console.log(window.shipping_qwqer.value)
    bindHtmlEvent({detail:window.shipping_qwqer.value})
    qwqer.addEventListener(qwqer.EVENT_QWQER_SELECTED, bindHtmlEvent);
    return () => {
     qwqer.removeEventListener(qwqer.EVENT_QWQER_SELECTED, bindHtmlEvent)
    }
  },[])




//close if opened
  React.useEffect(() => {
    if (open){
      setOpen(false);
    }
  },[])

  //check and set source
  React.useEffect(()=>{
    let ret = window.shipping_qwqer.getSource()
    if (ret){
      if (ret === "qwqer.expressdelivery" || ret === "qwqer.scheduleddelivery"){
         setSource("address");
      }
      if (ret === "qwqer.omnivaparcelterminal"){
        setSource("terminals");
      }
    }
  },[])

  //los
  React.useEffect(()=>{
    if (source === "terminals"){
     console.log("fetching terminals data")
      fetchMemo('')
    }
  },[source]);



//element selected 
  const onSelect = (e,v) => {
    //emit value change
   console.log("emit value change")
    onValueChange(v);
    hideDropdown();
  }

  //wrapping debounce effect + fetching data into memo
  const fetchMemo = React.useMemo(
    () =>{
      if(source === "address"){
        return debounce((val) => {
         console.log('we are inside debounce')
          if(source === "address"){
            showSpinner();
           console.log('we are inside debounce->address')
            fetchDataAddress(val).then((v)=>{
             console.log('inside debounce address we fetched');
             console.log(v);
              showData(v);
            })
          }
        }, 400);
      }
      

      if(source === "terminals"){
         console.log('we are inside fetchDataTerminals->terminals')

        return (val)=> fetchDataTerminals(val).then((v)=>{
           console.log('inside debounce fetchDataTerminals we fetched');
           console.log(v);
            setOptions(v)
          })
        }

      },[source]);


//reload on text input
  const onChangeText = (e,val)=>{
   console.log("onChangeText type")
    if (e && e.type != undefined){
     console.log(e && e.type)
    }
    if (val.length >= 1
       && e && e.type !== 'click' 
       && source !== "terminals"){
        //prevent onSelect propagation
     console.log('before  fetching')
      setOptions([]);
      setLoading(true);
      setOpen(true);
      console.log('qwqer be4 fetching fetchMemo',val,fetchMemo )
      fetchMemo(val);
    }
  }
  
  

  return (
    <Autocomplete
      clearOnBlur={false} //vanish on click outside
      
      loadingText = {t('qw_text_loading')}
      noOptionsText={t('qw_text_noopts')}
      sx={{ width: "100%",
          "& .MuiFilledInput-input":{padding:"15px 0px !important"},
          "& .MuiInputBase-root":{paddingTop:"0px !important"}}}
      fullWidth
      id="asynchronous-demo"
    /*  open={open} */
      required={true}
/* hide dropdown on lost focus */
      onBlur =  {(event) => {
       console.log("lost focus autocomplete");
              if (open){
                setOpen(false);
              }
            }}

      onChange = {onSelect}
      onInputChange = {onChangeText}
      getOptionLabel={(option) => {
        if (source==="terminals"){
          return `${option.id} - ${option.name}`
        }else if (source==="address"){
          return `${option.name}`
        }
      }}
      options={options}
      loading={loading}

      renderInput={(params) => (
        <TextField 
        sx = {{marginBottom:"5px",fontSize: '22px'}}
        color="secondary"
        variant="filled"
          {...params}
          
          InputProps={{
            ...params.InputProps,
            endAdornment: (
              <React.Fragment>
                {loading ? <CircularProgress color="secondary" size={20} /> : null}
                {params.InputProps.endAdornment}
              </React.Fragment>
            ),
          }}
        />
      )}

    />
  );

}