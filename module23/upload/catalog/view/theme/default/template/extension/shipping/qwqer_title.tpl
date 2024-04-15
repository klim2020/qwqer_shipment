<?php echo $text_title ?> <a href = "https://qwqer.lv/" target="_blank"><img src="catalog/view/images/qwqer.svg" alt="Qwqer service home page" style="margin-left:5px"></a>
<div id="shipping_qwqer_mount"></div>

<input name="shipping_qwqerd[address]"  type='hidden'/>
<input name="shipping_qwqerd[name]"  type='hidden'/>
<input name="shipping_qwqerd[phone]"  type='hidden'/>
<input name="shipping_qwqerd[response]"  type='hidden'/>

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
    window.shipping_qwqer.insertQwqer = (name, phone, address, object) => {
        document.querySelector('input[name="shipping_qwqerd[name]"]').value = name;
        document.querySelector('input[name="shipping_qwqerd[phone]"]').value = phone;
        document.querySelector('input[name="shipping_qwqerd[address]"]').value = address;
        document.querySelector('input[name="shipping_qwqerd[response]"]').value = JSON.stringify(object);
    }
    window.shipping_qwqer.url = '<?php echo $url; ?>';
    //mount scripts

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


</script>
