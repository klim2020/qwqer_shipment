

const setStorage = (form)=>{
    const key = window.shipping_qwqer.getSource();
    localStorage.setItem(key,JSON.stringify(form));
}
const getStorage = (key = false)=>{
    if (key === false){
        key = window.shipping_qwqer.getSource();
    } 
    return JSON.parse(localStorage.getItem(key));
}

const removeStorage = ()=>{
    const key = window.shipping_qwqer.getSource();
   //console.log("removing storage for "+key)
    localStorage.removeItem(key);
}

export { setStorage, removeStorage, getStorage }