import * as React from "react";
import AutoComplete from "./AutoComplete";
import PropTypes from 'prop-types';

import Box from "@mui/material/Box";
import HomeIcon from "@mui/icons-material/Home";
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import Grid from "@mui/material/Grid";
import TextField from "@mui/material/TextField";
import { MuiTelInput, matchIsValidTel } from "mui-tel-input";
import CircularProgress from "@mui/material/CircularProgress";

// TODO remove, this demo shouldn't need to reset the theme.


function filterTerminal(terminals,value){
  return terminals.filter(terminal=>`${terminal.id} ${terminal.name})`.match(value)!== null)
}


Form.prototypes = {
  //runs when form beign set OnSetForm({inputName:inputName,inputAddress:inputAddress,phone:phone});
  OnSetForm: PropTypes.func,
};

export default function Form({ OnSetForm }) {
  const [inputName, setInput] = React.useState('');
  const [inputAddress, setInputAddress] = React.useState({});
  const [phone, setPhone] = React.useState("");
  const [loading, setLoading] = React.useState(false);
  const [isSubmit,setSubmit] = React.useState(false);

  //event handlers
  const handleChange = (newPhone) => {
    setPhone(newPhone);
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    if (checkInputs()) {
      setLoading(true);
      loadEmulation().then((ok)=>{
        if (ok){
          setSubmit(true);
          setLoading(false);
          OnSetForm({inputName:inputName,inputAddress:inputAddress,phone:phone});
          console.log({inputName:inputName,inputAddress:inputAddress,phone:phone});
        }
      });
    }
  }

  const onAutoCompleteChange = (val) => {
    setInputAddress((v) => (v = val));
  };

  
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
      alert("Name is required");
      return false;
    }

    if (typeof inputAddress.name !== "string" || inputAddress.name < 1) {
      alert("Address is required");
      return false;
    }

    if (!matchIsValidTel(phone, { onlyCountryies: ["LV"] })) {
      alert("Latvian Phone is required");
      return false;
    }

    return true;
  };

  //dummies
  const loadEmulation = async ()=>{
    await new Promise((resolve)=>{
      setTimeout(()=>{resolve()},2000)
    });
    console.log("2 sec");
    return true;
  }

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
          <Avatar sx={{ m: 1, bgcolor: "secondary.main" }}>
            <HomeIcon />
          </Avatar>

          <Grid container spacing={2}>
            <Grid item xs={12}>
              <TextField
                autoComplete="given-name"
                name="firstName"
                onChange={(e) => {
                  setInput((v) => (v = e.target.value));
                }}
                value={inputName}
                required
                fullWidth
                id="firstName"
                label="First Name"
                autoFocus
              />
            </Grid>
            <Grid item xs={12}>
              <MuiTelInput
                sx={{ width: "100%" }}
                rules={{
                  validate: (value) =>
                    matchIsValidTel(value, { onlyCountries: ["LV"] }),
                }}
                onlyCountries={["LV"]}
                defaultCountry={"LV"}
                value={phone}
                onChange={handleChange}
              />
            </Grid>
            <Grid item xs={12}>
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
            Go
          </Button>
        </Box>
      )}
    </>
  );
}
