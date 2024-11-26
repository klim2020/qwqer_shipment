const qwqer = window.shipping_qwqer;
const filter ={
            rigaOnly : /r[i|ī]g[a|o]|[Р|р]ига/gmi //regex for Riga
        };

const isStandardPlugin = ()=>{
            if (qwqer.moduleType && qwqer.moduleType == 0){
                return true;
            }
            return false;
        }

        //params {foo: "bar".....}
const forceReboot = (params)=>{
    //reboot logic
   console.log("[qwqer]force Reboot");
   qwqer.reload(params);
}

export  {filter, isStandardPlugin, forceReboot};