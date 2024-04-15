<?php echo $text_title ?> <a href = "https://qwqer.lv/" target="_blank"><img src="catalog/view/images/qwqer.svg" alt="Qwqer service home page" style="margin-left:5px"></a>
<div style="display:none" id="shipping_qwqer_mount"></div>
<script>


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
    window.shipping_qwqer.active = true;
    //mount scripts
    document.querySelector("#acjs").addEventListener("load",()=>{

        //add events only for the
        if (window.shipping_qwqer.allowerarray.indexOf(document.querySelector('input[name="shipping_method"]:checked').value) != -1){
            window.shipping_qwqer.dispatchEvent(new CustomEvent("select", {detail:document.querySelector('input[name="shipping_method"]:checked').value}));
        }else{
            window.shipping_qwqer.dispatchEvent(new CustomEvent("select", {detail:false}));
        }

        document.querySelectorAll('input[name="shipping_method"]').forEach((e)=>{
            e.addEventListener('change',(el)=>{
                if (window.shipping_qwqer.allowerarray.indexOf(el.target.value)!=-1) {
                    //document.querySelector("#shipping_qwqer_mount").setAttribute("style", "");
                    window.shipping_qwqer.dispatchEvent(new CustomEvent("select", {detail:el.target.value}));
                }else{
                    //document.querySelector("#shipping_qwqer_mount").setAttribute("style", "display:none");
                    window.shipping_qwqer.dispatchEvent(new CustomEvent("select", {detail:false}));
                }
            })
        })
    })


</script>
