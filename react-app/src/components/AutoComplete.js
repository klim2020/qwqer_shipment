import * as React from 'react';

import TextField from '@mui/material/TextField';
import Autocomplete from '@mui/material/Autocomplete';
import CircularProgress from '@mui/material/CircularProgress';
import PropTypes from 'prop-types';
import terminals from './../dummy/terminals';


//This key was created specifically for the demo in mui.com.
//You need to create a new one for your application.
//const GOOGLE_MAPS_API_KEY = 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';

//loading the array
const fetchDataTerminals = (val) => {
  return new Promise((resolve) => {
    setTimeout(() => {
      resolve(filterTerminal(terminals,val));
    }, 2e3);
  });
}

const fetchDataAddress = (val) => {
  return new Promise((resolve) => {
    setTimeout(() => {
      resolve(filterTerminal(terminals,val));
    }, 2e3);
  });
}

//filters terminals
function filterTerminal(terminals,value){
  return terminals.filter(terminal=>`${terminal.id} ${terminal.name})`.match(value)!== null)
}

AutoComplete.prototypes={
  //runs when input value changes
  onValueChange:PropTypes.func,
}

export default function AutoComplete({onValueChange}) {
  const [open, setOpen] = React.useState(false);
  const [options, setOptions] = React.useState([]);
  const loading = open && options.length === 0;
  const [translation, setTranslation] = React.useState([]);
  const [source, setSource] = React.useState("terminals");//terminals address
  const [inputv, setValue] = React.useState('');//terminals address

  //load
  React.useEffect(() => {
    let active = true;

    if (!loading) {
      return undefined;
    }

    (async () => {
      let ret = [];
      if (source === "terminals"){
        ret = await fetchDataTerminals(''); // For demo purposes)
      }else if(source === "address"){
        ret = await fetchDataAddress('');
      }
       // For demo purposes.
      if (active) {
        setOptions([...ret]);
      }
    })();

    return () => {
      active = false;
    };
  }, [loading]);

  React.useEffect(() => {
    if (!open) {
      setOptions([]);
    }
  }, [open]);

  React.useEffect(() => {
    setTranslation((val)=>{return { ...val,label:"select city",loading:"loading"}})
  }, []);

  React.useEffect(() => {
    setTranslation((val)=>{return { ...val,label:"select city",loading:"loading"}})
  }, []);
 

  //bind with html events

  const switchSource = (e)=>{
    if (e.detail !== false){
      if (e.detail === "qwqer_parcel"){
        setSource("terminals")
      }
      if (e.detail === "qwqer_express"){
        setSource("address")
      }
    }else{
      setSource(false)
    }
  }

  React.useEffect(() => {
    window.shipping_qwqer.addEventListener('select', switchSource)
    return () => {
      window.shipping_qwqer.removeEventListener('select', switchSource)
    }
  },[]);

  //ev

  const onInputChange = (event, value, reason) => {
    
    if (value) {

      let ret = [];
      if (source === "terminals"){
        fetchDataTerminals(value).then((res)=>setOptions(()=>filterTerminal(res,value)));
      }else if(source === "address"){
        fetchDataAddress(value).then((res)=>setOptions(()=>filterTerminal(res,value)));
      }

    } else {
      setOptions([]);
    }
  };


  const onChange = (e,v) => {
    onValueChange(v);
  }


  

  return (
    <Autocomplete
      fullWidth
      id="asynchronous-demo"
      sx={{ width: "100%" }}
      open={open}
      onOpen={() => {
        setOpen(true);
      }}
      name="blabla21"
      filterSelectedOptions
      onClose={() => {
        setOpen(false);
      }}
      
      onChange = {onChange}
      getOptionLabel={(option) => `${option.id} - ${option.name}`}
      options={options}
      loading={loading}
      renderInput={(params) => (
        <TextField
          {...params}
          label={translation.label}
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