import React from 'react';

import Alert from '@mui/material/Alert';
import CheckIcon from '@mui/icons-material/Check';

import Box from "@mui/material/Box";
import PropTypes from 'prop-types';
import { useLanguage } from '../providers/LanguageProvider';

//msg box component

Complete.prototypes={
  //  {inputName:"",
  //  inputAddress:"",
  //  phone:""}
  //
  form:PropTypes.object,
}

export default function Complete({form}) {

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
            alignItems: "center",
          }}
        >
          <Alert icon={<CheckIcon fontSize="inherit" />}  severity="success">{t("qw_text_submit_success")}</Alert>
          <div style={{textAlign:"left"}}>
            <p>{t("text_name")}:{form.inputName}</p>
            <p>{t("text_address")}:{form.inputAddress.name}</p>
            <p>{t("text_phone")}:{form.phone}</p>
            <p>{t("text_price")}:{(form.callbackObject.price.client_price / 100).toFixed(2)}</p>
          </div>

        </Box>
    );
}