<?php echo $text_title_order_type; ?>
<?php if(isset($terminals)){ ?>




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

        loadjscssfile("catalog/view/stylesheet/qwqer/autocomplete.min.css", "css","accss");
        loadjscssfile("catalog/view/javascript/qwqer/autocomplete.min.js", "js","acjs");


        let parentDiv = document.querySelector("#qwqer\\.omnivaparcelterminal").parentElement;
        parentDiv.innerHTML += '<input name="autoComplete" id="autoComplete" type="search" dir="ltr" spellcheck=false autocorrect="off" autocomplete="off" value="<?php echo $autocomplete; ?>" autocapitalize="off">';
        parentDiv.innerHTML += '<input id="autoCompleteHidden" type="hidden" name="autoCompleteHidden" value="<?php echo $autocompletehidden; ?>">';

        document.querySelector("#acjs").addEventListener("load",()=>{
             const autoCompleteJS = new autoComplete({
                        selector: "#autoComplete",
                        placeHolder: "<?php echo $text_select_box; ?>",
                        searchEngine: "loose",
                        data: {
                            src: {{(terminals|json_encode|raw)}},
                            keys:['name','id'],
                            cache: true,
                            filter: (list) => {
                                // Filter duplicates
                                // incase of multiple data keys usage
                                return Array.from(
                                    new Set(list.map((value) => value.match))
                                ).map((food) => {
                                    return list.find((value) => value.match === food);
                                });
                            }
                        },
                         resultsList: {
                             element: (list, data) => {
                                 const info = document.createElement("p");
                                 if (data.results.length > 0) {
                                     info.innerHTML = `Displaying <strong>${data.results.length}</strong> out of <strong>${data.matches.length}</strong> results`;
                                 } else {
                                     info.innerHTML = `Found <strong>${data.matches.length}</strong> matching results for <strong>"${data.query}"</strong>`;
                                 }
                                 list.prepend(info);
                             },
                             noResults: true,
                             maxResults: 15,
                             tabSelect: true
                         },
                         resultItem: {
                             element: (item, data) => {
                                 // Modify Results Item Style
                                 item.style = "display: flex; justify-content: space-between;";
                                 // Modify Results Item Content
                                 item.innerHTML = `
              <span style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                ${data.match}
              </span>
              <span style="display: flex; align-items: center; font-size: 13px; font-weight: 100; text-transform: uppercase; color: rgba(0,0,0,.2);">
                ${data.key}
              </span>`;
                             },
                             highlight: true,
                             selected: "autoComplete_selected"
                         },
                        events: {
                            input: {
                                selection: (event) => {
                                    const selection = event.detail.selection.value;
                                    autoCompleteJS.input.value = selection.name;
                                }
                            }
                        }
                    });
        })

        document.querySelector("#autoComplete").addEventListener("selection", function (event) {
            //event on success selection
            document.querySelector("#autoCompleteHidden").value = JSON.stringify(event.detail.selection.value);

           //sendSaveRequest
           console.log('order_id-<?php echo $order_id; ?>  session_id - <?php echo $session_id; ?>')


        });

    </script>
    
<?php } ?>