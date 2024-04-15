import React from 'react';

import Alert from '@mui/material/Alert';
import CheckIcon from '@mui/icons-material/Check';

import Box from "@mui/material/Box";
import PropTypes from 'prop-types';

//msg box component

Complete.prototypes={
  //  {inputName:"",
  //  inputAddress:"",
  //  phone:""}
  //
  form:PropTypes.object,
}

export default function Complete({form}) {
  
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
          <Alert icon={<CheckIcon fontSize="inherit" />}  severity="success">all data is valid</Alert>
          <div style={{textAlign:"left"}}>
            <p>Name:{form.inputName}</p>
            <p>Address:{form.inputAddress.name}</p>
            <p>phone:{form.phone}</p>
            <p>price:{(form.callbackObject.client_price / 100).toFixed(2)}</p>
          </div>

        </Box>
    );
}