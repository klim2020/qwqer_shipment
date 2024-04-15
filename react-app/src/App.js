import './App.css';

//import Form from './components/Form';



import Container from '@mui/material/Container';

import CssBaseline from '@mui/material/CssBaseline';

import React from 'react';
import Form from './components/Form';
import Completed from './components/Completed';
import { matchIsValidTel } from "mui-tel-input";

import { LanguageProvider } from "./providers/LanguageProvider";


const validate =(form)=>{
  
  if (typeof form.inputName !== "string" || form.inputName === "") {
    return false;
  }

  if (typeof form.inputAddress.name !== "string" || form.inputAddress.name.length < 1) {
    return false;
  }

  if (typeof form.phone !== "string" && !matchIsValidTel(form.phone, { onlyCountryies: ["LV"] })) {
    return false;
  }

  return true;
}

function App() {
  //Form state that needs to be binded with input fields in html
  const [form, setForm] = React.useState(
    {
      inputName:"",
      inputAddress:"",
      phone:"",
      callbackObject:{}
    }
  );
  //State that shows component
  const [show, setShow] = React.useState()

    //event that hides/show element bassed on an outside event called  
    //window.shipping_qwqer->select
  const bindHtmlEvent = (e)=>{
    //is currently selected radio is actuallly qwqer delivery
    if (e.detail !== false){
      //every time we switch the radio we have to init empty objects
      setShow(true);
      setForm({
      inputName:"",
      inputAddress:"",
      phone:""
    });
    //hides element in case we select other delivery    
    }else{
      setShow(false);  
    }
  }

  //onLoad component
  React.useEffect(() => {
    //at first render, display not working, idk why, so we forcing first render
    if (typeof window.shipping_qwqer.enabled !== "undefined" && window.shipping_qwqer.enabled){
      setShow(true)
      //increasing number of instances
      window.shipping_qwqer.instances++;
    }
    
    //binding out html event
    window.shipping_qwqer.addEventListener('select', bindHtmlEvent)
    return () => {
      window.shipping_qwqer.instances--;
      window.shipping_qwqer.removeEventListener('select', bindHtmlEvent)
    }
  },[]);

  //lift up data to html
  React.useEffect(()=>{
    console.log("form object inside an app have been changed");
    if (validate(form)){
      console.log("form object inside an app have been validated");
      window.shipping_qwqer.insertQwqer(form.inputName, form.phone, form.inputAddress.name, form.callbackObject)
    }
  },[form]);


  //add loading counter
  React.useEffect(()=>{
    window.shipping_qwqer.instances++;
    if (window.shipping_qwqer.instances>1){
      window.shipping_qwqer.forceRemove();
    }
    return () => {
      window.shipping_qwqer.instances--;
    }

  })

  return (
    <LanguageProvider>
        {show && 
          <Container component="main" maxWidth="xs">
          <CssBaseline />
          {validate(form) 
          ?<Completed  form={form}></Completed>
          :<Form OnSetForm={setForm}></Form>}
          
            
          
            
          </Container>
        }
    </LanguageProvider>
    
  );
}

export default App;
