import "./App.css";

//import Form from './components/Form';

import Container from "@mui/material/Container";

import CssBaseline from "@mui/material/CssBaseline";

import React from "react";
import Form from "./components/Form";
import Completed from "./components/Completed";
import { matchIsValidTel } from "mui-tel-input";

import { LanguageProvider } from "./providers/LanguageProvider";

import { getStorage, setStorage, removeStorage } from './config/storage';
import { isStandardPlugin, forceReboot } from './config/config';
import { removeSessionValue, fetchValidate } from './transport/transport';
import { isOpen } from './transport/opening';

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
    inputName: "not provided",
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
        inputName: "not provided",
        inputAddress: "",
        phone: "",
      });
      //hides element in case we select other delivery
    } else {
      setShow(false);
    }
    
 
  };

  //set form data if exists in storage on radio selection
  const mountCheckChange = () => {
    if (isStandardPlugin() && getStorage()){
      console.log("trying to set storage cuz storage exists on change")
      let type = window.shipping_qwqer.getSource();
      let f = getStorage()
      fetchValidate(f.inputAddress, f.phone, f.name, type).then((ok)=>{
        console.log(ok);
        if (ok){
          //emits state with OnSetForm prop
          setForm(f);
          //console.log({inputName:inputName,inputAddress:inputAddress,phone:phone});
        }else{

        }
      });

      
    }
  }

  const OnSetForm = (form)=>{
    console.log("OnSetForm in App.js propagated");
     if (validate(form)){
      setStorage(form);  
      console.log("Setting up the form");
      //if user is on a express then go on server for a price
      if (window.shipping_qwqer.getSource() === "qwqer.expressdelivery"){
        if (!isStandardPlugin()){
        window.shipping_qwqer.insertUrlParam('qwqer_show_price','1');
        console.log("forcingreload on express form input");
        forceReboot();
        }else{
          setForm(form);
        }

      }else{
        //if not simply show the prce
        setForm(form);
      }
    }
  }

  //removes signal to show price for back
  const removePriceRequestForBackend = (e)=>{
    
    if (window.shipping_qwqer !== 'qwqer.expressdelivery'){
      window.shipping_qwqer.removeUrlParameter('qwqer_show_price');
    }
  }

 

  //close info block
  const onCompleteClose = (e) => {
    setForm({
      inputName: "not provided",
      inputAddress: "",
      phone: "",
      callbackObject: {},
    });
    let selection =  /[\s*\.](.*)/gm.exec(window.shipping_qwqer.getSource()).length >=2 && /[\s*\.](.*)/gm.exec(window.shipping_qwqer.getSource())[1];
    if (window.shipping_qwqer.getSource() == "qwqer.expressdelivery"){
      removeSessionValue(selection).then(ret=>{
        if (ret){
          removeStorage();
          if(ret.reboot){
            forceReboot()
          }
        }
      });
    }
    else{
      console.log("");
      removeStorage();
    }
  }


//window.shipping_qwqer.currentPrice != 0

  //checking and restore form from storage
  React.useEffect(()=>{
    console.log("checking Localstorage on app loading")
    if (getStorage() &&  window.shipping_qwqer.currentPrice != 0){
      console.log("Localstorage is present, loading it to form")
      setForm(getStorage());
    }else{
      console.log("storage dont exists or currentjprice is 0")
      if (getStorage()){
        console.log(" storage exists")
        if (window.shipping_qwqer.currentPrice == 0){
          console.log("currentprice is 0");
          removeSessionValue(window.shipping_qwqer.getSource());
          removeStorage();
          forceReboot();
        }
      }
    }
    console.log("Finish hecking and restore form from storage")
  },[])

  //clear express price if not express storage is empty
  React.useEffect(()=>{
    if (window.shipping_qwqer.prices !== 'undefined' 
      && typeof(window.shipping_qwqer.prices.expressdelivery) !== 'undefined'
      && window.shipping_qwqer.prices.expressdelivery != 0
      && !getStorage('qwqer.expressdelivery')){
        console.log("price for express delivery have been set but storage object doesnt exist, so we need to clear session data");
        removeSessionValue('qwqer.expressdelivery');
        removeStorage();
        forceReboot();
    }
  },[]);


  //onLoad component binding with inner html events
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
    //window.shipping_qwqer.addEventListener("select", removePriceIfWrongSelection);
    window.shipping_qwqer.addEventListener("select", removePriceRequestForBackend);
    return () => {
      window.shipping_qwqer.instances--;
      window.shipping_qwqer.removeEventListener("select", bindHtmlEvent);
      //window.shipping_qwqer.removeEventListener("select", removePriceIfWrongSelection);
      window.shipping_qwqer.removeEventListener("select", removePriceRequestForBackend);
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
    }
  }, [form]);

  //Reload if needed
  React.useEffect(()=>{
    if (validate(form)) {
    //we get request for reloading from backend
      if (form.callbackObject.forcereload){
        console.log("adding session storage when form changed");
        window.shipping_qwqer.insertUrlParam('qwqer_show_price','1');
        if (window.shipping_qwqer.moduleType == 0){
          forceReboot();
        }
        
      }
    }
          
  },[form])

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

  //disable Express if we are not working
  React.useEffect(()=>{
    isOpen().then((e)=>{
      if (!e){
        console.log("we are open")
      }else{
        console.log("we are closed")
        let rm = [...document.querySelectorAll("input[name='shipping_method']")].filter(e=>e.value == 'qwqer.expressdelivery');
        if (rm.length >0){
          if (isStandardPlugin()){
            rm[0].closest(".radio").remove();
          }else{
            rm[0].closest(".radio-input").remove();
          }
          
        }
      }
    });
  },[]);

  //mount switching storage for standard checkout
  React.useEffect(()=>{
    window.shipping_qwqer.addEventListener("select", mountCheckChange);
    return () => {
      window.shipping_qwqer.removeEventListener("select", mountCheckChange);
    }
  },[]);


  return (
    <LanguageProvider>
      {show && (
        <Container style={{marginLeft:"unset", padding:"0px"}} component="main" maxWidth="xs">
          <CssBaseline />
          {validate(form) ? (
            <Completed style={{alignItems: "start"}} form={form} onClose={onCompleteClose}></Completed>
          ) : (
            <Form OnSetForm={OnSetForm}></Form>
          )}
        </Container>
      )}
    </LanguageProvider>
  );
}

export default App;
