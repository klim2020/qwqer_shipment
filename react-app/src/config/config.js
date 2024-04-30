 const conf = {
        filter :{
            rigaOnly : /r[i|ī]g[a|o]|[Р|р]ига/gmi //regex for Riga
        },
        isStandardPlugin:()=>{
            if (window.shipping_qwqer.moduleType && window.shipping_qwqer.moduleType==0){
                return true;
            }
            return false;
        }
        
    }

export default conf;