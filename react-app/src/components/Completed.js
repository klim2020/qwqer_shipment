import React from 'react';

import Alert from '@mui/material/Alert';
import CheckIcon from '@mui/icons-material/Check';

import Box from "@mui/material/Box";
import PropTypes from 'prop-types';
import { useLanguage } from '../providers/LanguageProvider';
import IconButton from '@mui/material/IconButton';
import CloseIcon from '@mui/icons-material/Close';
import CheckCircleOutlineIcon from '@mui/icons-material/CheckCircleOutline';
import { isStandardPlugin } from './../config/config';

//msg box component

Complete.prototypes={
  //  {inputName:"",
  //  inputAddress:"",
  //  phone:""}
  //
  form:PropTypes.object,
}

export default function Complete({form, onClose}) {

  const { t } = useLanguage();
  
  React.useEffect(() => {
    //console.log(form);
  },[])

    return (
        <Box
          sx={{
            marginTop: 1,
            display: "flex",
            flexDirection: "column",
            alignItems: "left",
          }}
        >
          <Alert sx = {{alignItems:"center", padding:"0px 16px"}} icon={<CheckCircleOutlineIcon fontSize="inherit" />} action={
            <IconButton aria-label="close" onClick={onClose}  color="error">
              <CloseIcon color="error"/>
            </IconButton>
          }
           
          severity="success">
            {t("qw_text_submit_success")}
            </Alert>
          <div style={{textAlign:"left"}}>
            <p>{t("text_name")}:{form.inputName}</p>
            <p>{t("text_address")}:{form.inputAddress.name}</p>
            <p>{t("text_phone")}:{form.phone}</p>
            {isStandardPlugin() 
              && window.shipping_qwqer.getSource() === "qwqer.expressdelivery" 
              && <p>{t("text_price")}:{form.callbackObject.price.client_price/100}</p>}
          </div>
          
        </Box>
    );
}