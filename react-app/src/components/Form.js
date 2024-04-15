import * as React from "react";
import PropTypes from 'prop-types';

import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Grid from "@mui/material/Grid";
import TextField from "@mui/material/TextField";
import { MuiTelInput, matchIsValidTel } from "mui-tel-input";
import CircularProgress from "@mui/material/CircularProgress";
import Alert from '@mui/material/Alert';

import AutoComplete from "./AutoComplete";

import { fetchValidate } from './../transport/transport'
import { useLanguage } from "../providers/LanguageProvider";

import { blue } from '@mui/material/colors';

// TODO remove, this demo shouldn't need to reset the theme.


function filterTerminal(terminals,value){
  return terminals.filter(terminal=>`${terminal.id} ${terminal.name})`.match(value)!== null)
}


Form.prototypes = {
  //runs when form beign set OnSetForm({inputName:inputName,inputAddress:inputAddress,phone:phone});
  OnSetForm: PropTypes.func,
};

export default function Form({ OnSetForm }) {
  //name state
  const [inputName, setInput] = React.useState('');
  //address state
  const [inputAddress, setInputAddress] = React.useState({});
  //phone state
  const [phone, setPhone] = React.useState("");
  //loading state, shows loading indicator
  const [loading, setLoading] = React.useState(false);
  //state that checks if user data is valid
  //const [isSubmit,setSubmit] = React.useState(false);
  const [info, setInfo] = React.useState(false)

  const { t } = useLanguage();

  const primary = blue[500]; 

  //event handlers
  const handleChange = (newPhone) => {
    setPhone(newPhone);
  };

//check button click
  const handleSubmit = (event) => {
    event.preventDefault();
//validates inputs
    if (checkInputs()) {
      setLoading(true);
      //dummy function 

      let type = window.shipping_qwqer.getSource();
      
      fetchValidate(inputAddress, phone, inputName, type).then((ok)=>{
        //console.log(ok);
        if (ok){
          //shows submit message
          //setSubmit(true);
          //hides loading  indicator
          setLoading(false);
          //emits state with OnSetForm prop
          OnSetForm({inputName:inputName,inputAddress:inputAddress,phone:phone,callbackObject:ok});
          //console.log({inputName:inputName,inputAddress:inputAddress,phone:phone});
        }else{
          setLoading(false);
          setInfo(true);
        }
      });

    }
  }

  //changes autocomplete 
  const onAutoCompleteChange = (val) => {
    setInputAddress((v) => (v = val));
  };

//clears form on changing radio button  
  const bindHtmlEvent = (e)=>{
    if (e.detail !== false){
      setInput('');    
      setPhone('');
      setInputAddress({});
      OnSetForm({})
    }
  }

  //bind with html events
  React.useEffect(() => {
    window.shipping_qwqer.addEventListener('select', bindHtmlEvent)
    return () => {
      window.shipping_qwqer.removeEventListener('select', bindHtmlEvent)
    }
  },[]);


  //validators
  const checkInputs = () => {
    if (typeof inputName !== "string" || inputName === "") {
      alert(t("qw_text_name_req"));
      return false;
    }

    if (typeof inputAddress.name !== "string" || inputAddress.name < 1) {
      alert(t("qw_text_address_req"));
      return false;
    }

    if (!matchIsValidTel(phone, { onlyCountryies: ["LV"] })) {
      alert(t("qw_text_phone_req"));
      return false;
    }

    return true;
  };



  return (
    <>
      {loading && (
        <Box
          sx={{
            marginTop: 8,
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
          }}
        >
          <CircularProgress></CircularProgress>
        </Box>
      )}

      {!loading && (
        <Box
          sx={{
            marginTop: 8,
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
          }}
        >
          {info && <Alert severity="warning">{t('qw_text_server_error')}</Alert>}
          <Grid container spacing={2}>

            <Grid item xs={12}>
            <label style={{color:primary}} >{t('text_name')}</label> 
              <TextField
                autoComplete="given-name"
                name="firstName"
                onChange={(e) => {
                  setInput((v) => (v = e.target.value));
                }}
                value={inputName}
                required={true}
                fullWidth
                id="firstName"
              />
            </Grid>

            <Grid item xs={12}>
            <label  style={{color:primary}} >{t('text_phone')}</label> 
              <MuiTelInput
                required={true}
                placeholder={t('qw_enter_phone_label')}
                variant="outlined"
                sx={{ width: "100%" }}
                rules={{
                  validate: (value) =>
                    matchIsValidTel(value, { onlyCountries: ["LV"] }),
                }}
                onlyCountries={["LV"]}
                defaultCountry={"LV"}
                value={phone}
                onChange={handleChange}
                id = "phoneinput"
              />
            </Grid>

            <Grid item xs={12}>
            <label style={{color:primary}} >{t('text_address')}</label> 
              <AutoComplete
                
                sx={{ width: "100%" }}
                style={{ with: "100%" }}
                onValueChange={onAutoCompleteChange}
                value={inputAddress}
              ></AutoComplete>
            </Grid>

          </Grid>

          <Button
            type="submit"
            variant="contained"
            sx={{ mt: 3, mb: 2 }}
            onClick={handleSubmit}
          >
            {t('qw_text_submit')}
          </Button>
        </Box>
      )}
    </>
  );
}
