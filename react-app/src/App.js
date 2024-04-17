import "./App.css";

//import Form from './components/Form';

import Container from "@mui/material/Container";

import CssBaseline from "@mui/material/CssBaseline";

import React from "react";
import Form from "./components/Form";
import Completed from "./components/Completed";
import { matchIsValidTel } from "mui-tel-input";

import { LanguageProvider } from "./providers/LanguageProvider";

const validate = (form) => {
  if (typeof form.inputName !== "string" || form.inputName === "") {
    return false;
  }

  if (
    typeof form.inputAddress.name !== "string" ||
    form.inputAddress.name.length < 1
  ) {
    return false;
  }

  if (
    typeof form.phone !== "string" &&
    !matchIsValidTel(form.phone, { onlyCountryies: ["LV"] })
  ) {
    return false;
  }

  return true;
};

function App() {

  

  //Form state that needs to be binded with input fields in html
  const [form, setForm] = React.useState({
    inputName: "",
    inputAddress: "",
    phone: "",
    callbackObject: {},
  });
  //State that shows component
  const [show, setShow] = React.useState();

  //event that hides/show element bassed on an outside event called
  //window.shipping_qwqer->select
  const bindHtmlEvent = (e) => {
    //is currently selected radio is actuallly qwqer delivery
    if (e.detail !== false) {
      //every time we switch the radio we have to init empty objects
      setShow(true);
      setForm({
        inputName: "",
        inputAddress: "",
        phone: "",
      });
      //hides element in case we select other delivery
    } else {
      setShow(false);
    }
    
 
  };

  //mark price removal for backend
  const removePriceIfWrongSelection = (e) => {
    if(e.detail !== 'qwqer.expressdelivery'){
      window.shipping_qwqer.insertUrlParam('force_remove_price','1');
      window.shipping_qwqer.setRemovePrice(1);
    }else{
      window.shipping_qwqer.setRemovePrice(0);
    }
  }


  //remove unnecessary data on every page load
  React.useEffect(()=>{
    window.shipping_qwqer.removeUrlParameter('force_remove_price')
  },[]);


  //onLoad component
  React.useEffect(() => {
    //at first render, display not working, idk why, so we forcing first render
    if (
      typeof window.shipping_qwqer.enabled !== "undefined" &&
      window.shipping_qwqer.enabled
    ) {
      setShow(true);
      //increasing number of instances
      window.shipping_qwqer.instances++;
    }

    //binding out html event
    window.shipping_qwqer.addEventListener("select", bindHtmlEvent);
    window.shipping_qwqer.addEventListener("select", removePriceIfWrongSelection);
    return () => {
      window.shipping_qwqer.instances--;
      window.shipping_qwqer.removeEventListener("select", bindHtmlEvent);
      window.shipping_qwqer.removeEventListener("select", removePriceIfWrongSelection);
    };
  }, []);

  //lift up data to html
  React.useEffect(() => {
    console.log("form object inside an app have been changed");
    console.log(form.callbackObject);
    if (validate(form)) {
      console.log("form object inside an app have been validated");
      window.shipping_qwqer.insertQwqer(
        form.inputName,
        form.phone,
        form.inputAddress.name,
        form.callbackObject
      );
      //we get request for reloading from backend
      if (form.callbackObject.forcereload){
        console.log("adding session storage when form changed");
        sessionStorage.setItem('qwqer_form',JSON.stringify(form));
        window.shipping_qwqer.insertUrlParam('qwqer_show_price','1');
        window.location.reload();
      }
      
    }
  }, [form]);

  //removing force_remove param from get params
  React.useEffect(()=>{
    window.shipping_qwqer.removeUrlParameter('qwqer_show_price');
  },[])

  //add loading counter
  React.useEffect(() => {
    window.shipping_qwqer.instances++;
    if (window.shipping_qwqer.instances > 1) {
      window.shipping_qwqer.forceRemove();
    }
    return () => {
      window.shipping_qwqer.instances--;
    };
  });

  //filling form if conditions are met after reload
  React.useEffect(()=>{
    try{
      var qwqer_form = JSON.parse(sessionStorage.getItem('qwqer_form'));
      if (typeof form !== 'object'){
        console.log("remove SessionStorage in if");
        sessionStorage.removeItem('qwqer_form');
        return
      }
    }catch{
      console.log("remove SessionStorage in catch");
      sessionStorage.removeItem('qwqer_form');
      return
    }
    if(sessionStorage.getItem('qwqer_form')){
      console.log("reading sessionStorage");
      //check if expressDelivery is selected
      if (window.shipping_qwqer.getSource() === 'qwqer.expressdelivery'){
        console.log(qwqer_form);
        qwqer_form.callbackObject.forcereload = false;
        setForm(qwqer_form);
      }else{
        console.log("remove SessionStorage cuz wrong option selected"+ window.shipping_qwqer.getSource() );
        sessionStorage.removeItem('qwqer_form');
      }

    }

  },[])

  return (
    <LanguageProvider>
      {show && (
        <Container style={{marginLeft:"unset"}} component="main" maxWidth="xs">
          <CssBaseline />
          {validate(form) ? (
            <Completed form={form}></Completed>
          ) : (
            <Form OnSetForm={setForm}></Form>
          )}
        </Container>
      )}
    </LanguageProvider>
  );
}

export default App;
