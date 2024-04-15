//This key was created specifically for the demo in mui.com.
//You need to create a new one for your application.
//const GOOGLE_MAPS_API_KEY = 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';

//loading the array
//todo remove oc23 in prod

let prefix = "/ocs23"

const fetchDataTerminals = (val) => {
    return new Promise((resolve) => {
      let token = window.shipping_qwqer.token
      fetch(prefix+"/index.php?route=extension/shipping/qwqer/get_terminals&qwqer_token="+token, {}).then((response) => {
         return response.json()
      }).then((data)=>{
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
    let ret =  await fetch(prefix+"/index.php?route=extension/shipping/qwqer/get_adress&qwqer_token="+token, requestOptions).then((response) => {
      return response.json()
   }).then((data)=>{
      return data;
   })
   if ('error' in ret){
    return [{id:"no data",name:"no data"}]
   }
   ret = ret.map((v,i)=>{
    return {id:i,name:v}
   })
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

    formdata.append("qwqer_address", address.name);
    formdata.append("qwqer_phone", phone);
    formdata.append("qwqer_name", name);
    formdata.append("qwqer_type", orderType);
   
    //console.log(formdata);

    var requestOptions = {
      method: 'POST',
      body: formdata,
      redirect: 'follow'
    };

    let ret =  await fetch(prefix+"/index.php?route=extension/shipping/qwqer/validate_data&qwqer_token="+token, requestOptions).then((response) => {
      return response.json()
    }).then((data)=>{
        return data;
    })
    console.log(ret);
    if ('client_price' in ret){
        return ret;
    }
    
    return false;
  }


  export {fetchDataAddress, fetchDataTerminals, fetchValidate}