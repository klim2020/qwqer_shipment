<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            ::<a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>

    <?php if ($error_warning) { ?>
        <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>

    <div class="box">
        <div class="heading">
            <h1><img src="view/image/shipping.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
        </div>

        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><span class="required">*</span><?php echo $entry_api; ?></td>
                        <td>
                            <input type="text" name="qwqer_api" value="<?php echo $qwqer_api; ?>" placeholder="<?php echo $entry_api; ?>" id="input-api"class="form-control" maxlength="40" />
                            <?php if($error_api){ ?>
                                <div class="text-danger"><?php echo  $error_api ; ?></div>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td><span class="required">*</span><?php echo $entry_trade_pt; ?></td>
                        <td>
                            <input type="text" name="qwqer_trade_pt" value="<?php echo $qwqer_trade_pt; ?>" placeholder="<?php echo $entry_trade_pt; ?>" id="input-trade_pt" class="form-control" maxlength="4" />
                            <?php if($error_trade_pt){ ?>
                                <div class="text-danger"><?php echo  $error_trade_pt; ?></div>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td><span class="required">*</span><?php echo $entry_trade_cat;?></td>
                        <td>
                            <select name="qwqer_trade_cat" id="input-trade_cat" class="form-control">
                                <?php foreach ($qwqer_trade_cat_options as $index => $cat_option) { ?>

                                    <?php if ($index == $qwqer_trade_cat){ ?>
                                        <option value="<?php echo $index ?>" selected="selected"><?php echo  $cat_option; ?></option>
                                    <?php }else{ ?>
                                        <option value="<?php echo $index  ?>"><?php echo $cat_option; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php if($error_trade_cat){ ?>
                                <div class="text-danger"><?php echo $error_trade_cat; ?></div>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td><span class="required">*</span>Address City <span class="help"><?php  echo $help_address_city; ?></span></td>
                        <td>
                            <input name="qwqer_address_city" placeholder="Address city" rows="5" id="input-address-city" class="form-control"  value = "Riga" disabled></input>
                        </td>
                    </tr>

                    <tr>
                        <td><span class="required">*</span><?php echo  $text_telephone; ?><span class="help"><?php echo  $help_telephone; ?></span></td>
                        <td>
                            <input name="qwqer_telephone" placeholder="<?php echo $entry_telephone; ?>" rows="5" id="input-address-city" class="form-control"  value="<?php echo  $qwqer_telephone; ?>"></input>
                            <?php  if ($error_telephone1) {?>
                                <div class="text-danger"><?php echo  $error_telephone1; ?></div>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td><span class="required">*</span><?php echo $entry_address;?><span class="help"><?php echo $help_address_tooltip;?></span></td>
                        <td>
                            <input type="text" name="qwqer_address" value="<?php echo $qwqer_address;?>" placeholder="<?php echo $help_address;?>" id="autoComplete" class="form-control" autocomplete="off" style="color: gray;  border: 1px solid grey;">
                            <button id = "validationBtn" class="btn btn-primary"><?php echo $text_button_validate;?></button>
                            <script>
                                document.addEventListener('DOMContentLoaded',()=>{
                                    document.querySelector('#validationBtn').addEventListener('click',(e)=>{
                                        e.preventDefault();
                                        const address = document.querySelector("input[name=qwqer_address]").value;
                                        const api     = document.querySelector("input[name=qwqer_api]").value;
                                        const pt      = document.querySelector("input[name=qwqer_trade_pt]").value;
                                        let data =  new FormData();
                                        data.append("address",address);
                                        data.append("api_token",api);
                                        data.append("trade_point",pt);
                                       //console.log(data);
                                        fetch('index.php?route=shipping/qwqer/geocode&token=<?php echo $token;?>',
                                            {
                                                method: "POST",
                                                body: data
                                            })
                                            .then(res => res.json())
                                            .then((data)=>{
                                                if (data.data && data.data.address && data.data.coordinates){
                                                    document.querySelector("input[name=qwqer_address_object]").value = JSON.stringify(data);
                                                    document.querySelector(".qwqer_address_object").textContent = 'V address validated';
                                                    document.querySelector(".qwqer_address_object").style.color = 'green'

                                                }else{
                                                    document.querySelector("input[name=qwqer_address_object]").value = '';
                                                    document.querySelector(".qwqer_address_object").textContent = 'X address not validated';
                                                    document.querySelector(".qwqer_address_object").style.color = 'red'
                                                }

                                            }).catch(data=>{
                                            //alert(data.join(','));
                                        });
                                        return true;
                                    })
                                })
                            </script>
                            <input type="hidden" name = "qwqer_address_object" value="<?php echo  $qwqer_address_object; ?>">
                            <?php  if ($qwqer_address_object){ ?>
                                <span class = "qwqer_address_object" style="color:green">V address validated</span>
                            <?php  }else{ ?>
                                <span  class = "qwqer_address_object" style="color:red">X address not validated</span>
                            <?php  } ?>
                        </td>
                    </tr>

                    <tr>
                        <td><?php echo $entry_tax_class; ?></td>
                        <td><select name="qwqer_tax_class_id">
                                <option value="0"><?php echo $text_none; ?></option>
                                <?php foreach ($tax_classes as $tax_class) { ?>
                                    <?php if ($tax_class['tax_class_id'] == $qwqer_tax_class_id) { ?>
                                        <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select></td>
                    </tr>

                    <tr>
                        <td><?php echo $entry_geo_zone; ?></td>
                        <td><select name="qwqer_geo_zone_id">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                    <?php if ($geo_zone['geo_zone_id'] == $qwqer_geo_zone_id) { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select></td>
                    </tr>

                    <tr>
                        <td><?php echo $entry_status; ?></td>
                        <td><select name="qwqer_status">
                                <?php if ($qwqer_status) { ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>

                    <tr>
                        <td><?php echo $entry_sort_order; ?></td>
                        <td><input type="text" name="qwqer_sort_order" value="<?php echo $qwqer_sort_order; ?>" size="1" /></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

    <script>

        var token = '<?php echo $token;?>';
        const autoCompleteJS = new autoComplete({
            placeHolder: "<?php echo $help_address; ?>",
            data: {
                src: [],
                cache: false,
            },

            resultItem: {
                highlight: true
            },

            trigger: (query) => {
                if (query.length >=  autoCompleteJS.threshold-1){
                    let input = document.querySelector("#autoComplete").value
                    const api     = document.querySelector("input[name=qwqer_api]").value;
                    const pt      = document.querySelector("input[name=qwqer_trade_pt]").value;
                    let data = new FormData()
                    data.append("api_token",api);
                    data.append("trade_point",pt);
                    data.append("input", input);
                    fetch('index.php?route=shipping/qwqer/autocomplete&token=<?php echo $token;?>',
                        {
                            method: "POST",
                            body: data,
                        })
                        .then((response) => {
                            // 1. check response.ok
                            if (response.ok) {
                                return response.json();
                            }
                            return Promise.reject(response); // 2. reject instead of throw
                        })
                        .then((data)=>{

                            autoCompleteJS.data.src = data.data;
                            //autoCompleteJS.data.keys = ["title"];
                        }).catch(response =>{
                        response.json().then((json) => {
                           //console.log(json.message);
                        })
                    });
                    return true;
                }

                return false;
                //return query.replace(/ /g, "").length; // Returns "Boolean"
            },
            query: function(input){//
                return input;
            },
            events: {
                input: {
                    selection: function(event){
                        const selection = event.detail.selection.value;
                        //console.log(selection)
                        this.value = selection;
                    }
                }
            },
            resultsList: {
                element: (list, data) => {
                    data.results = data.results.filter((el,index)=>{
                        if (el.value.includes('Riga') == false){
                            var element = document.getElementById("autoComplete_result_"+index);
                            list.removeChild(element);
                            return false;
                        }
                        return true;
                    })


                    if (!data.results.length) {
                        // Create "No Results" message list element
                        const message = document.createElement("div");
                        message.setAttribute("class", "no_result");
                        // Add message text content
                        message.innerHTML = `<span>Found No Results for "${data.query}"</span>`;
                        // Add message list element to the list
                        list.appendChild(message);
                    }
                },
                noResults: true,
                maxResults: 20,
            },

            threshold: 3,
            debounce: 200,
        });
    </script>
<?php echo $footer; ?>