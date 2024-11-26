import "./App.css";

//import Form from './components/Form';

import Container from "@mui/material/Container";

import CssBaseline from "@mui/material/CssBaseline";

import React from "react";
import Form from "./components/Form";
import Completed from "./components/Completed";
import { matchIsValidTel } from "mui-tel-input";

import { LanguageProvider } from "./providers/LanguageProvider";
import { useQwqer } from "./providers/QwqerProvider";

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
  const { qwqer } = useQwqer(); 
  //Form state that needs to be binded with input fields in html
  const [form, setForm] = React.useState({
    inputName: "not provided",
    inputAddress: "",
    phone: "",
    callbackObject: {},
  });
  //State that shows component
  const [selectedQwqer, setSelectedQwqer] = React.useState(true);
 
  //disable Express if we are not working
  const disableExpressifWeNotWorking = ()=>{
    isOpen().then((e)=>{
      if (e){
       console.log("qwqer we are open")
      }else{
       console.log("qwqer we are closed")
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
  }

  const checkIfWeNeedToShowTheForm = (el)=>{
    
  }

  //event that hides/show element bassed on an outside event called
  //qwqer->select
  const bindHtmlEvent = (e) => {
    //is currently selected radio is actuallly qwqer delivery
      //every time we switch the radio we have to init empty objects
      console.log('qwqer bindHtmlEvent selected element is qwqer ');
      console.log( "qwqer bindHtmlEvent  setSelectedQwqer(true)");
      setSelectedQwqer(true);
      setForm({
        inputName: "not provided",
        inputAddress: "",
        phone: "",
      });
      //hides element in case we select other delivery
  };

  const setHideForm = ( )=>{
    console.log( "qwqer  setSelectedQwqer(false) setHideForm hiding form");
    setSelectedQwqer(false);
  }
  

  //set form data if exists in storage on radio selection
  const mountCheckChange = () => {
    if (isStandardPlugin() && getStorage()){
     console.log("trying to set storage cuz storage exists on change")
      let type = qwqer.getSource();
      let f = getStorage()
      fetchValidate(f.inputAddress, f.phone, f.name, type).then((ok)=>{
       console.log(ok);
        if (ok){
          //emits state with OnSetForm prop
          setForm(f);
          console.log({inputName:inputName,inputAddress:inputAddress,phone:phone});
        }else{

        }
      });

      
    }
  }

  const OnSetForm = (form)=>{
   console.log("OnSetForm in App.js propagated !");
     if (validate(form)){
      setStorage(form);  
     console.log("Setting up the form");
      //if user is on a express then go on server for a price
      if (qwqer.getSource() === "qwqer.expressdelivery"){
        if (!isStandardPlugin()){
        qwqer.insertUrlParam('qwqer_show_price','1');
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
    
    if (qwqer !== 'qwqer.expressdelivery'){
      qwqer.removeUrlParameter('qwqer_show_price');
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
    let selection =  /[\s*\.](.*)/gm.exec(qwqer.getSource()).length >=2 && /[\s*\.](.*)/gm.exec(qwqer.getSource())[1];
    if (qwqer.getSource() == "qwqer.expressdelivery"){
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


//qwqer.currentPrice != 0

  //checking and restore form from storage
  React.useEffect(()=>{
    
    console.log("[qwqer] qwqer provider",qwqer)
    if (getStorage() &&  qwqer.currentPrice != 0){
      console.log("Localstorage is present, loading it to form")
      setForm(getStorage());
    }else{
      console.log("storage dont exists or currentjprice is 0")
      if (getStorage()){
       console.log(" storage exists")
        if (qwqer.currentPrice == 0){
         console.log("currentprice is 0");
          removeSessionValue(qwqer.getSource());
          removeStorage();
          forceReboot();
        }
      }
    }
   console.log("Finish hecking and restore form from storage")
  },[])

  //clear express price if not express storage is empty
  React.useEffect(()=>{
    if (qwqer.prices !== 'undefined' 
      && typeof(qwqer.prices.expressdelivery) !== 'undefined'
      && qwqer.prices.expressdelivery != 0
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
      typeof qwqer.enabled !== "undefined" &&
      qwqer.enabled
    ) {
      qwqer.instances++;
    }
    disableExpressifWeNotWorking();
   
    //binding out html event
    if (qwqer.getSource() === false){
      console.log("qwqer hideForm on useEffect cuz qwqer.getSource() ",qwqer.getSource())
      setHideForm()
    }else{
      bindHtmlEvent();
    }
    
    qwqer.addEventListener(qwqer.EVENT_QWQER_SELECTED, bindHtmlEvent);
    qwqer.addEventListener(qwqer.EVENT_NOT_QWQER_SELECTED, setHideForm);
    qwqer.addEventListener(qwqer.EVENT_QWQER_SELECTED, disableExpressifWeNotWorking);
    qwqer.addEventListener(qwqer.EVENT_NOT_QWQER_SELECTED, disableExpressifWeNotWorking);
    //qwqer.addEventListener("select", removePriceIfWrongSelection);
    qwqer.addEventListener(qwqer.EVENT_QWQER_SELECTED, removePriceRequestForBackend);
    console.log('qwqer register events in app');
    return () => {
      qwqer.instances--;
      console.log('qwqer unregister events in app');
      qwqer.removeEventListener(qwqer.EVENT_QWQER_SELECTED, bindHtmlEvent);
      qwqer.removeEventListener(qwqer.EVENT_NOT_QWQER_SELECTED, setHideForm);
      qwqer.removeEventListener(qwqer.EVENT_QWQER_SELECTED, disableExpressifWeNotWorking);
      qwqer.removeEventListener(qwqer.EVENT_NOT_QWQER_SELECTED, disableExpressifWeNotWorking);
      //qwqer.removeEventListener("select", removePriceIfWrongSelection);
      qwqer.removeEventListener(qwqer.EVENT_QWQER_SELECTED, removePriceRequestForBackend);
    };
  }, []);

  //lift up data to html
  React.useEffect(() => {
   console.log("form object inside an app have been changed");
   console.log(form.callbackObject);
    if (validate(form)) {
     console.log("form object inside an app have been validated");
      qwqer.insertQwqer(
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
        qwqer.insertUrlParam('qwqer_show_price','1');
        if (qwqer.moduleType == 0){
          forceReboot();
        }
        
      }
    }
          
  },[form])

  //add loading counter
  React.useEffect(() => {
    qwqer.instances++;
    if (qwqer.instances > 1) {
      qwqer.forceRemove();
    }
    return () => {
      qwqer.instances--;
    };
  });

  //mount switching storage for standard checkout
  React.useEffect(()=>{
    qwqer.addEventListener(qwqer.EVENT_QWQER_SELECTED, mountCheckChange);
    return () => {
      qwqer.removeEventListener(qwqer.EVENT_QWQER_SELECTED, mountCheckChange);
    }
  },[]);

console.log('qwqer show state is', selectedQwqer);
  return (
    <LanguageProvider>
      {selectedQwqer && (
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
