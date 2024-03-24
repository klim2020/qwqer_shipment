<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-shipping"  data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php } ?>
    <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo  $text_edit; ?></h3>
      </div>
      <div class="panel-body">

        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
          <li><a href="#tab-orders" data-toggle="tab"><?php echo $tab_orders; ?></a></li>
          <li><a href="#tab-info" data-toggle="tab"><?php echo $tab_info; ?></a></li>
        </ul>
          <form action="<?php echo  $action; ?>" method="post" enctype="multipart/form-data" id="form-shipping" class="form-horizontal">
                <div class="tab-content">

                      <!--general -->
                      <div class="tab-pane active" id="tab-general">

                          <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-trade_pt"><?php echo  $entry_api; ?></label>
                            <div class="col-sm-10">
                              <input type="text" name="qwqer_api" value="<?php echo $qwqer_api; ?>" placeholder="<?php echo $entry_api; ?>" id="input-api" class="form-control" maxlength="40" />
                              <?php if ($error_api) { ?>
                                <div class="text-danger"><?php echo  $error_api; ?></div>
                              <?php } ?>
                            </div>
                          </div>

                          <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-trade_pt"><?php echo  $entry_trade_pt; ?></label>
                            <div class="col-sm-10">
                              <input type="text" name="qwqer_trade_pt" value="<?php echo $qwqer_trade_pt; ?>" placeholder="<?php echo $entry_trade_pt; ?>" id="input-trade_pt" class="form-control" maxlength="4" />
                              <?php if ($error_trade_pt) { ?>
                                <div class="text-danger"><?php echo  $error_trade_pt; ?></div>
                              <?php } ?>
                            </div>
                          </div>

                          <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-trade_cat"><?php echo $entry_trade_cat; ?></label>

                            <div class="col-sm-10">

                              <select name="qwqer_trade_cat" id="input-trade_cat" class="form-control">
                                <?php foreach ($qwqer_trade_cat_options as $index => $cat_option) { ?>

                                  <?php if ($index == $qwqer_trade_cat) { ?>
                                    <option value="<?php echo $index ?>" selected="selected"><?php echo  $cat_option; ?></option>
                                  <?php } else { ?>
                                    <option value="<?php echo $index  ?>"><?php echo $cat_option; ?></option>
                                  <?php } ?>
                                <?php } ?>
                              </select>
                              <?php if ($error_trade_cat) { ?>
                                <div class="text-danger"><?php echo $error_trade_cat; ?></div>
                              <?php } ?>
                            </div>
                          </div>

                          <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-address-city"><span data-toggle="tooltip" title="<?php echo $help_address_city; ?>">Address City</span></label>
                            <div class="col-sm-10">
                              <input name="qwqer_address_city" placeholder="Address city" rows="5" id="input-address-city" class="form-control" value="Riga" disabled></input>
                            </div>
                          </div>

                          <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-address-city"><span data-toggle="tooltip" title="<?php echo  $help_telephone; ?>"><?php echo  $text_telephone; ?></span></label>
                            <div class="col-sm-10">
                              <input name="qwqer_telephone" placeholder="<?php echo $entry_telephone; ?>" rows="5" id="input-address-city" class="form-control" value="<?php echo  $qwqer_telephone; ?>"></input>
                              <?php if ($error_telephone1) { ?>
                                <div class="text-danger"><?php echo  $error_telephone1; ?></div>
                              <?php } ?>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="" data-original-title="<?php echo $help_address_tooltip; ?>"><?php echo $entry_address; ?></span></label>
                            <div class="col-sm-10">
                              <input type="text" name="qwqer_address" value="<?php echo $qwqer_address; ?>" placeholder="<?php echo $help_address; ?>" id="autoComplete" class="form-control" autocomplete="off" style="color: gray;  border: 1px solid grey;">
                              <button id="validationBtn" class="btn btn-primary"><?php echo $text_button_validate; ?></button>
                              <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                  document.querySelector('#validationBtn').addEventListener('click', (e) => {
                                    e.preventDefault();
                                    const address = document.querySelector("input[name=qwqer_address]").value;
                                    const api = document.querySelector("input[name=qwqer_api]").value;
                                    const pt = document.querySelector("input[name=qwqer_trade_pt]").value;
                                    let data = new FormData();
                                    data.append("address", address);
                                    data.append("api_token", api);
                                    data.append("trade_point", pt);
                                    console.log(data)
                                    fetch('index.php?route=extension/shipping/qwqer/geocode&token=<?php echo $token; ?>', {
                                        method: "POST",
                                        body: data
                                      })
                                      .then(res => res.json())
                                      .then((data) => {
                                        if (data.data && data.data.address && data.data.coordinates) {
                                          document.querySelector("input[name=qwqer_address_object]").value = JSON.stringify(data);
                                          document.querySelector(".qwqer_address_object").textContent = 'V address validated';
                                          document.querySelector(".qwqer_address_object").style.color = 'green'

                                        } else {
                                          document.querySelector("input[name=qwqer_address_object]").value = '';
                                          document.querySelector(".qwqer_address_object").textContent = 'X address not validated';
                                          document.querySelector(".qwqer_address_object").style.color = 'red'
                                        }

                                      }).catch(data => {
                                        //alert(data.join(','));
                                      });
                                    return true;
                                  })
                                })
                              </script>
                              <input type="hidden" name="qwqer_address_object" value="<?php echo  $qwqer_address_object; ?>">
                              <?php if ($qwqer_address_object) { ?>
                                <span class="qwqer_address_object" style="color:green">V address validated</span>
                              <?php  } else { ?>
                                <span class="qwqer_address_object" style="color:red">X address not validated</span>
                              <?php  } ?>

                              <script>

                              </script>
                            </div>
                            <script>
                              var token = '<?php echo $token; ?>';
                              const autoCompleteJS = new autoComplete({
                                placeHolder: "<?php echo $help_address; ?>",
                                data: {
                                  src: async (query)=>{
                                      d = await suggest(query);

                                      return d;

                                  },
                                  cache: false,
                                },

                                resultItem: {
                                  highlight: true
                                },

                                trigger: async (query) => {
                                  if (query.length >= autoCompleteJS.threshold - 1) {
                                    return true;
                                  }

                                  return false;
                                  //return query.replace(/ /g, "").length; // Returns "Boolean"
                                },
                                query: function(input) {
                                  return input;
                                },
                                events: {
                                  input: {
                                    selection: function(event) {
                                      const selection = event.detail.selection.value;
                                      //console.log(selection)
                                      this.value = selection;
                                    },
                                    open() {}
                                  }
                                },
                                resultsList: {
                                  element: (list, data) => {
                                    data.results = data.results.filter((el, index) => {
                                      if (el.value.includes('Riga') == false) {
                                        var element = document.getElementById("autoComplete_result_" + index);
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
                              async function suggest(resource) {
                                  let input = document.querySelector("#autoComplete").value
                                  const api = document.querySelector("input[name=qwqer_api]").value;
                                  const pt = document.querySelector("input[name=qwqer_trade_pt]").value;
                                  let data = new FormData()
                                  data.append("api_token", api);
                                  data.append("trade_point", pt);
                                  data.append("input", input);
                                  let d =await fetch('index.php?route=extension/shipping/qwqer/autocomplete&token=<?php echo $token; ?>', {
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
                                      .then((data) => {

                                          return data.data;
                                          //autoCompleteJS.data.keys = ["title"];
                                      }).catch(response => {
                                          return [];
                                          response.json().then((json) => {

                                          })
                                      });
                                  //autoCompleteJS.data.src = d;
                                  return d;

                              }


                            </script>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-weight-class"><span data-toggle="tooltip" title="<?php echo $help_weight_class; ?>"><?php echo $entry_weight_class; ?></span></label>
                            <div class="col-sm-10">
                              <select name="qwqer_weight_class_id" id="input-weight-class" class="form-control">
                                <?php foreach ($weight_classes as $weight_class) {  ?>
                                  <?php if ($weight_class['weight_class_id'] == $qwqer_weight_class_id) { ?>
                                    <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                                  <?php } else { ?>
                                    <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                                  <?php } ?>
                                <?php } ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-tax-class"><?php echo  $entry_tax_class; ?></label>
                            <div class="col-sm-10">
                              <select name="qwqer_tax_class_id" id="input-tax-class" class="form-control">
                                <option value="0"><?php echo  $text_none; ?></option>
                                <?php foreach ($tax_classes as $tax_class) {  ?>
                                  <?php if ($tax_class['tax_class_id'] == $qwqer_tax_class_id) {  ?>
                                    <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                                  <?php } else { ?>
                                    <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                                  <?php } ?>
                                <?php } ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo  $entry_geo_zone; ?></label>
                            <div class="col-sm-10">
                              <select name="qwqer_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo  $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                  <?php if ($geo_zone['geo_zone_id'] == $qwqer_geo_zone_id) {  ?>
                                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                  <?php } else { ?>
                                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                  <?php } ?>
                                <?php } ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-status"><?php echo  $entry_status; ?></label>
                            <div class="col-sm-10">
                              <select name="qwqer_status" id="input-status" class="form-control">
                                <?php if (isset($qwqer_status) && $qwqer_status) { ?>
                                  <option value="1" selected="selected"><?php echo  $text_enabled; ?></option>
                                  <option value="0"><?php echo  $text_disabled; ?></option>
                                <?php } else { ?>
                                  <option value="0" selected="selected"><?php echo  $text_disabled; ?></option>
                                  <option value="1"><?php echo  $text_enabled; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-status">Production Server?</label>
                            <div class="col-sm-10">
                              <select name="qwqer_is_prod" id="input-is-prod" class="form-control">
                                <?php if (isset($qwqer_is_prod) && $qwqer_is_prod) { ?>
                                  <option value="1" selected="selected">On</option>
                                  <option value="0">Off</option>
                                <?php } else { ?>
                                  <option value="1">On</option>
                                  <option value="0" selected="selected">Off</option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo  $entry_sort_order; ?></label>
                            <div class="col-sm-10">
                              <input type="text" name="qwqer_sort_order" value="<?php echo $qwqer_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-complete-status"><span data-toggle="tooltip" title="<?php echo $entry_hide_status; ?>"><?php echo $entry_hide_status; ?></span></label>
                            <div class="col-sm-10">
                              <div class="well well-sm" style="height: 150px; overflow: auto;">
                                <?php foreach ($stock_statuses as $order_status) { ?>
                                  <div class="checkbox">
                                    <label>
                                      <?php if (in_array($order_status['stock_status_id'], $qwqer_hide_statuses)) { ?>
                                        <input type="checkbox" name="qwqer_hide_statuses[]" value="<?php echo $order_status['stock_status_id']; ?>" checked="checked" />
                                        <?php echo $order_status['name']; ?>
                                      <?php } else { ?>
                                        <input type="checkbox" name="qwqer_hide_statuses[]" value="<?php echo $order_status['stock_status_id']; ?>" />
                                        <?php echo $order_status['name']; ?>
                                      <?php } ?>
                                    </label>
                                  </div>
                                <?php } ?>
                              </div>
                            </div>
                          </div>

                      </div>

                      <!-- orderss  -->
                      <div class="tab-pane" id="tab-orders">
                        <div class="page-header">
                          <div class="container-fluid">
                            <div class="pull-right">
                              <button type="button" <?php if (isset($orders) && is_array($orders) && count($orders) == 0) { ?> disabled <?php } ?> data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-shipping').submit() : false;"><i class="fa fa-trash-o"></i></button>
                            </div>
                          </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                              <thead>
                                <tr> <!-- <?php echo $text_remote_order;  ?>  <?php echo $order['order_id']; ?>-->
                                  <td style="width: 1px;" class="text-center"><input disabled="disabled" type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>

                                  <td class="text-left"> <?php echo $text_created;  ?> </td>
                                  <td class="text-left"> <?php echo $text_delivery_type;  ?> </td>

                                  <td class="text-left"> <?php echo $text_address;  ?> </td>
                                  <td class="text-left"> <?php echo $text_status;  ?> </td>
                                  <td class="text-left"><?php echo $text_order;  ?></td>
                                  <td class="text-left"> <?php echo $text_create;  ?></td>
                                  <td class="text-left"> <?php echo $text_invoice;  ?></td>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($orders as $order) { ?>
                                  <tr>
                                    <td class="text-center"> <input type="checkbox" name="selected[]" value="<?php echo $order['qwqer_id']; ?>">
                                    </td>



                                    <td class="text-left"><?php echo  $order['date']; ?> </td>

                                    <td class="text-left"><?php echo $order['delivery_type']; ?></td>



                                    <td class="text-left"><?php if (isset($order['address']) && $order['address']) {
                                                            echo $order['address'];
                                                          } else {
                                                            echo $text_none;
                                                          } ?></td>

                                    <td class="text-left"> <?php echo  $order['response']['status']; ?> </td>
                                    <td class="text-center"> <a href="<?php echo $order['order_link']; ?>" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Edit"><i class="fa fa-reply"></i>
                                    </td>

                                    <td class="text-center">
                                      <?php if ($order['createlink']) { ?> <a href="<?php echo $order['createlink']; ?>" data-toggle="tooltip" title="<?php echo $text_request;  ?>" class="btn btn-success ml-2" data-original-title="Edit"><i class="fa fa-plus"></i><?php } ?>
                                    </td>
                                    <td class="text-center">
                                      <?php if ($order['invoice_link']) { ?><a href="<?php echo $order['invoice_link']; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $text_invoice;  ?>" class="btn btn-primary" data-original-title="Edit"><i class="fa fa fa-print"></i></a><?php } ?>
                                    </td>
                                  </tr>
                                <?php } ?>
                              </tbody>
                            </table>
                          </div>
                        <div class="row">
                          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                        </div>
                      </div>

                      <!-- info  -->
                      <div class="tab-pane" id="tab-info">
                          <div class="form-group required">
                              <div class="col-sm-2"><?php echo  $text_company_name; ?></div>
                              <div class="col-sm-9"><?php echo  $working_time['merchant']['name']??''; ?></div>
                          </div>

                          <div class="form-group required">
                              <div class="col-sm-2"><?php echo  $text_company_type; ?></div>
                              <div class="col-sm-9 "><?php echo  $working_time['type']??''; ?></div>
                          </div>

                          <div class="form-group required">
                              <div class="col-sm-2"><?php echo  $text_company_address; ?></div>
                              <div class="col-sm-9"><?php echo  $working_time['address']??''; ?></div>
                          </div>


                          <div class="form-group required">
                              <div class="col-sm-2"><?php echo  $text_company_map; ?></div>
                              <div class="col-sm-9">
                                  <?php if (isset($working_time['address'])){ ?>
                                    <a target = "_blank" href = "https://www.google.com/maps/place/<?php echo $working_time['address']; ?>/@<?php echo $working_time['coordinates'][0]; ?>,<?php echo $working_time['coordinates'][1]; ?>,19z/data=!4m6!3m5!1s0x46eed05339e7af95:0x22d458bb0ee9a4ef!8m2!3d56.9088952!4d24.082184!16s%2Fg%2F11b8v4vyl2?entry=ttu" > <?php echo $text_address_link; ?></a>
                                  <?php } ?>
                               </div>
                          </div>

                          <div class="form-group required">
                              <div class="col-sm-2"><?php echo  $text_working_time; ?></div>
                              <div class="col-sm-9">    <table class="table table-bordered">
                                      <thead>
                                      <tr>
                                          <td style="width: 50%;"><b><?php echo $text_working_day; ?></b></td>
                                          <td style="width: auto;"><b><?php echo $text_working_from; ?></b></td>
                                          <td style="width: auto;"><b><?php echo $text_working_to; ?></b></td>
                                      </tr>
                                      </thead>
                                      <tbody>

                                      <?php if (isset($working_time['working_hours'])){
                                          foreach ($working_time['working_hours'] as $the_day)  { ?>
                                              <tr>
                                                  <td><?php echo $the_day['day_of_week']; ?></td>
                                                  <td style="width: auto;"><?php echo $the_day['time_from']; ?><br/></td>
                                                  <td><?php echo $the_day['time_to']; ?><br/></td>
                                              </tr>
                                          <?php }
                                      }?>
                                      </tbody>
                                  </table> </div>
                          </div>

                      </div>

                </div>
          </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>