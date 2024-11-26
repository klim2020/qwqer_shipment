//This key was created specifically for the demo in mui.com.
//You need to create a new one for your application.
//const GOOGLE_MAPS_API_KEY = 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';

//loading the array

import {filter} from './../config/config';

const getUrl = ()=>{
  if (typeof window.shipping_qwqer !== "undefined"){
    return window.shipping_qwqer.url;
  }else{
    return '';
  }
}


//todo remove oc23 in prod

function filterValue(opts,value){
  return opts.filter(opt=>`${opt.id} ${opt.name})`.match(value)!== null)
}




const fetchDataTerminals = (val,callback) => {
    if (window.terminals !== undefined){
     //console.log('fetching terminals with preloaded data');
      if ('data' in window.terminals && window.terminals.data === 'key invalid'){
       //console.log('we have error keyt is invalid');
        window.location.reload();
      }
      return new Promise((resolve) => {
        let ret = filterValue(window.terminals,val)
        resolve(ret)})
    }
    return new Promise((resolve) => {
     //console.log('we are about to fetch terminals')
      let token = window.shipping_qwqer.token
      fetch(getUrl()+"index.php?route=extension/shipping/qwqer/get_terminals&qwqer_token="+token, {}).then((response) => {
         return response.json()
      }).then((data)=>{
       //console.log('event after fetching terminals')
         window.terminals = data;
         resolve(data)
      })
    });
  }

  //todo remove oc23 in prod
  const fetchDataAddress = async (val) => {
    let token = window.shipping_qwqer.token
    let formdata = new FormData();
    formdata.append("qwqer_address", val);
  
    var requestOptions = {
      method: 'POST',
      body: formdata,
      redirect: 'follow'
    };
    let ret =  await fetch(getUrl()+"index.php?route=extension/shipping/qwqer/get_adress&qwqer_token="+token, requestOptions).then((response) => {
      return response.json()
   }).then((data)=>{
      return data;
   })
   if ('error' in ret){
    return [{id:"...",name:"..."}]
   }
   ret = ret.map((v,i)=>{
    return {id:i,name:v}
   })
   let out = filterValue(ret,filter.rigaOnly);
   if (out.length>0){
    return out;
   }
   //console.log(ret);
   return ret;
  }


  /** 
   * 
   * inputAddress
   * setPhone
   * inputName
   * 
  */
  const fetchValidate = async (address, phone, name, orderType)=>{
    let token = window.shipping_qwqer.token
    let formdata = new FormData();

    formdata.append("qwqer_address", JSON.stringify(address));
    formdata.append("qwqer_phone", phone);
    formdata.append("qwqer_name", name);
    formdata.append("qwqer_type", orderType);
   
   console.log(address);

    var requestOptions = {
      method: 'POST',
      body: formdata,
      redirect: 'follow'
    };

    let ret =  await fetch(getUrl()+"index.php?route=extension/shipping/qwqer/validate_data&qwqer_token="+token, requestOptions).then((response) => {
      return response.json()
    }).then((data)=>{
        return data;
    })
   console.log("qwqer data recieved validate_data is:",ret)
   console.log(ret);
    if ('price' in ret && 'client_price' in ret.price){
        return ret;
    }
   console.log("qwqer error in validate_data transport function")
    return false;
  }


    /** Removes session price  from session storage
   * 
   * inputAddress
   * setPhone
   * inputName
   * 
  */
  const removeSessionValue = async (selected)=>{
    let token = window.shipping_qwqer.token
    let formdata = new FormData();
    formdata.append("selected", selected);
    var requestOptions = {
      method: 'POST',
      body: formdata,
      redirect: 'follow'
    };
    const response =  await fetch(getUrl()+"index.php?route=extension/shipping/qwqer/remove_session&qwqer_token="+token, requestOptions)
    const data =  await response.json();
   //console.log("printing data from fetch removeSessionValue");
   //console.log(data);
    if (response.status === 200 && data.message === "success"){
      return data;
    }
    return false;
  }

  const fetchWorkingHours = async()=>{
    if (window.shipping_qwqer.workingHours !== undefined){
      return window.shipping_qwqer.workingHours;
    }
    let token = window.shipping_qwqer.token
    var requestOptions = {
      method: 'POST',
    };
    const response =  await fetch(getUrl()+"index.php?route=extension/shipping/qwqer/get_working_hours&qwqer_token="+token, requestOptions)
    const data =  await response.json();
   //console.log("printing data from fetch working hours");
   //console.log(data);
   //console.log(response.status === 200 && data.message === "success" && data.error === undefined);
    if (response.status === 200 && data.message === "success" && data.error === undefined){
      window.shipping_qwqer.workingHours = data;
      return data;
    }
    return false;
  }




  export {fetchDataAddress, fetchDataTerminals, fetchValidate, removeSessionValue, fetchWorkingHours}