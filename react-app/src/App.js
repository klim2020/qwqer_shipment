import './App.css';

//import Form from './components/Form';



import Container from '@mui/material/Container';

import CssBaseline from '@mui/material/CssBaseline';

import React from 'react';
import Form from './components/Form';
import Completed from './components/Completed';
import { matchIsValidTel } from "mui-tel-input";


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
  const [form, setForm] = React.useState(
    {
      inputName:"",
      inputAddress:"",
      phone:""
    }
  );

  const [show, setShow] = React.useState()

  const bindHtmlEvent = (e)=>{
    if (e.detail !== false){
      setShow(true);
      setForm({
      inputName:"",
      inputAddress:"",
      phone:""
    });    
    }else{
      setShow(false);  
    }
  }

  //bind with html events
  React.useEffect(() => {
    window.shipping_qwqer.addEventListener('select', bindHtmlEvent)
    return () => {
      window.shipping_qwqer.removeEventListener('select', bindHtmlEvent)
    }
  },[]);
 
  return (
        <>
        {show && 
          <Container component="main" maxWidth="xs">
          <CssBaseline />
          {validate(form) 
          ?<Completed></Completed>
          :<Form OnSetForm={setForm}></Form>}
          
            
          <input name="shipping_qwqer_address"  type='hidden'/>
          <input name="shippingqwqer_name"  type='hidden'/>
          <input name="shipping_qwqer_fone"  type='hidden'/>

            
          </Container>
        }
      </>
    
  );
}

export default App;
