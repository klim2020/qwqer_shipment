<?php echo $text_title ?> <a href = "https://qwqer.lv/" target="_blank"><img src="catalog/view/images/qwqer.svg" alt="Qwqer service home page" style="margin-left:5px"></a>
<div id="shipping_qwqer_mount"></div>

<input name="shipping_qwqerd[address]"  type='hidden'/>
<input name="shipping_qwqerd[name]"  type='hidden'/>
<input name="shipping_qwqerd[phone]"  type='hidden'/>
<input name="shipping_qwqerd[response]"  type='hidden'/>
<input name="shipping_qwqerd[removeprice]"  type='hidden'/>

<script>
//load css and js programaticly
   function loadjscssfile(filename, filetype,id){
            if (filetype=="js"){ //if filename is a external JavaScript file
                var fileref=document.createElement('script')
                fileref.setAttribute("type","text/javascript")
                fileref.setAttribute("src", filename)
            }
            else if (filetype=="css"){ //if filename is an external CSS file
                var fileref=document.createElement("link")
                fileref.setAttribute("rel", "stylesheet")
                fileref.setAttribute("type", "text/css")
                fileref.setAttribute("href", filename)

            }
            if (typeof id !== 'undefined'){
                fileref.setAttribute("id",id)
            }
            let el = document.querySelector("#"+id);
            if (el){
                document.querySelector("#"+id).remove();
            }
            if (typeof fileref!="undefined") {
                document.getElementsByTagName("head")[0].appendChild(fileref);
            }
        }



   loadjscssfile("catalog/view/stylesheet/qwqer/shipping_qwqer.css", "css","accss");
   loadjscssfile("catalog/view/javascript/qwqer/shipping_qwqer.js", "js","acjs");
</script>

<script type="text/javascript">
    window.shipping_qwqer = {}
    window.shipping_qwqer = document.createElement('shipping_qwqer');
    window.shipping_qwqer.allowerarray = ["qwqer.expressdelivery", "qwqer.scheduleddelivery", "qwqer.omnivaparcelterminal"];
    window.shipping_qwqer.token = '<?php echo $token; ?>';
    window.shipping_qwqer.instances = 0;//if widget have been printed
    window.shipping_qwqer.forceRemove = ()=>{
        elements = document.querySelectorAll('.MuiContainer-root');
        if (elements.length > 1){
            elements.forEach((v,i)=>{
                if (i>0){
                    document.querySelectorAll('.MuiContainer-root')[i].remove();
                }
            })
        }
    }

    window.shipping_qwqer.langs = <?php echo json_encode($langs); ?>

    window.shipping_qwqer.getSource = () =>{
        if (window.shipping_qwqer.allowerarray.indexOf(document.querySelector('input[name="shipping_method"]:checked').value) != -1){
            return document.querySelector('input[name="shipping_method"]:checked').value;
        }
        return false;
    }
    window.shipping_qwqer.currentPrice = '<?php echo $current_price;?>';
    window.shipping_qwqer.insertQwqer = (name, phone, address, object) => {
        document.querySelector('input[name="shipping_qwqerd[name]"]').value     = name;
        document.querySelector('input[name="shipping_qwqerd[phone]"]').value    = phone;
        document.querySelector('input[name="shipping_qwqerd[address]"]').value  = address;
        document.querySelector('input[name="shipping_qwqerd[response]"]').value = JSON.stringify(object);
        let qwqer_obj = object;
        qwqer_obj.selecter = window.shipping_qwqer.getSource();
    }
    window.shipping_qwqer.url = '<?php echo $url; ?>';
    //sets the price removal from a back end side on a next rere
    //mount scripts
    window.shipping_qwqer.setRemovePrice = (val)=>{
        document.querySelector('input[name="shipping_qwqerd[removeprice]"]').value = val;
    }

    window.shipping_qwqer.reload = (params)=>{
        //reload logic
        if (params){
            Object.entries(params).forEach((k,v)=>{
                window.shipping_qwqer.insertUrlParam(k,v)
            })
        }
        console.log("reload");
        window.location.reload();
    }

    window.shipping_qwqer.insertUrlParam = (key, value) => {
        if (history.pushState) {
            let searchParams = new URLSearchParams(window.location.search);
            searchParams.set(key, value);
            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
            window.history.pushState({path: newurl}, '', newurl);
        }
    }

    // to remove the specific key
    window.shipping_qwqer.removeUrlParameter = (paramKey) => {
        const url = window.location.href
        var r = new URL(url)
        r.searchParams.delete(paramKey)
        const newUrl = r.href
        window.history.pushState({ path: newUrl }, '', newUrl)
    }

    document.querySelector("#acjs").addEventListener("load",()=>{

        //add events only for the
        if (window.shipping_qwqer.allowerarray.indexOf(document.querySelector('input[name="shipping_method"]:checked').value) != -1){
            window.shipping_qwqer.enabled = true;
            window.shipping_qwqer.value = document.querySelector('input[name="shipping_method"]:checked').value;
            //console.log("hello from correct event")
            window.shipping_qwqer.dispatchEvent(new CustomEvent("select", {detail:document.querySelector('input[name="shipping_method"]:checked').value}));
        }else{
            window.shipping_qwqer.enabled = false;
            window.shipping_qwqer.dispatchEvent(new CustomEvent("select", {detail:false}));
            //console.log("hello from wrong event")
        }

        document.querySelectorAll('input[name="shipping_method"]').forEach((e)=>{
            e.addEventListener('change',(el)=>{
                if (window.shipping_qwqer.allowerarray.indexOf(el.target.value)!=-1) {
                    window.shipping_qwqer.enabled = true;
                    //document.querySelector("#shipping_qwqer_mount").setAttribute("style", "");
                    window.shipping_qwqer.dispatchEvent(new CustomEvent("select", {detail:el.target.value}));
                }else{
                    window.shipping_qwqer.enabled = false;
                    //document.querySelector("#shipping_qwqer_mount").setAttribute("style", "display:none");
                    window.shipping_qwqer.dispatchEvent(new CustomEvent("select", {detail:false}));
                }
            })
        })
    })

    window.shipping_qwqer.prices = JSON.parse(`<?php echo $prices; ?>`);

    window.shipping_qwqer.moduleType = '<?php echo $moduleType; ?>';
</script>
