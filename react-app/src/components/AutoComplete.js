import * as React from 'react';

import TextField from '@mui/material/TextField';
import Autocomplete from '@mui/material/Autocomplete';
import CircularProgress from '@mui/material/CircularProgress';
import PropTypes from 'prop-types';

import { useLanguage } from '../providers/LanguageProvider';

import { fetchDataAddress, fetchDataTerminals } from './../transport/transport';


//filters terminals
function filterTerminal(terminals,value){
  return terminals.filter(terminal=>`${terminal.id} ${terminal.name})`.match(value)!== null)
}

AutoComplete.prototypes={
  //runs when input value changes
  onValueChange:PropTypes.func,
}

export default function AutoComplete({onValueChange}) {
//state dla otkrytija  blocka
  const [open, setOpen] = React.useState(false);
//array with substitution elements  
  const [options, setOptions] = React.useState([]);
//loading state when autocomplete  is loading data
  const [loading,setLoading] = React.useState(open && options.length === 0)
//translations, should change to context  
  const [translation, setTranslation] = React.useState([]);
//source that is responsible for switching fetching functions,    
  const [source, setSource] = React.useState("");//terminals address
 
  const { t } = useLanguage();


  //loading state logic
  React.useEffect(()=>{
    setLoading(open && options.length === 0);
  },[open,options])

//replace with context
  React.useEffect(() => {

    //set translation
    setTranslation((val)=>{return { ...val,label:"select city",loading:"loading"}})
    //set source
  }, []);

 

  //bind with html events
  const bindHtmlEvent = (e)=>{
    
    if (e.detail !== false){
      if (e.detail === "qwqer.omnivaparcelterminal"){
        setSource("terminals")
      }
      if (e.detail === "qwqer.expressdelivery"){
        setSource("address")
      }
      if (e.detail === "qwqer.scheduleddelivery"){
        setSource("address")
      }
    }else{
      setSource(false)
    }
  }

  React.useEffect(() => {
    //console.log(window.shipping_qwqer.value)
    bindHtmlEvent({detail:window.shipping_qwqer.value})
    window.shipping_qwqer.addEventListener('select', bindHtmlEvent)
    return () => {
      window.shipping_qwqer.removeEventListener('select', bindHtmlEvent)
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

//run fetch first time
  React.useEffect(() => {
    (async () => {
      let ret = [];
      //console.log(source);
      if (source === "terminals"){
        ret = await fetchDataTerminals(''); // For demo purposes)
      }else if(source === "address"){
        ret = await fetchDataAddress('');
        ret = filterTerminal(ret,/riga,/gmi);
      }else{
        ret = [];
      }
      setOptions(ret);
    })();
  },[source]);


//element selected 
  const onSelect = (e,v) => {
    //emit value change
    onValueChange(v);
  }


//reload on text input
  const onChangeText = (ev,val)=>{
    
    if(source === "address"){
      //start loading state
      setOptions([]);
      setOpen(true);
      //console.log("refrtching "+val)
      fetchDataAddress(val).then((v)=>{
        let out = filterTerminal(v,/riga/gmi);
        setOptions(out)
      });
    }
  }
  

  return (
    <Autocomplete
      fullWidth
      id="asynchronous-demo"
      sx={{ width: "100%" }}
      open={open}
      required={true}
      onOpen={() => {
        setOpen(true);
      }}
      name="blabla21"
      filterSelectedOptions
      onClose={() => {
        setOpen(false);
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
          {...params}
          
          InputProps={{
            ...params.InputProps,
            endAdornment: (
              <React.Fragment>
                {loading ? <CircularProgress color="inherit" size={20} /> : null}
                {params.InputProps.endAdornment}
              </React.Fragment>
            ),
          }}
        />
      )}
    />
  );

}