<?php

use library\qweqr\QwqerApi;

/**
 *
 * @property QwqerApi $shipping_qwqer
 */
class ModelExtensionShippingQwqer extends Model {


    public function __construct($registry)
    {
        parent::__construct($registry);

        require_once DIR_SYSTEM."library/qwqer/QwqerApi.php";
        new QwqerApi($registry);



    }

	public function getQuote($address) {
		$this->load->language('extension/shipping/qwqer');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('qwqer_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('qwqer_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

         //check if product have restricted stock status
         $statuses = array();
         $statuses = $this->config->get('qwqer_hide_statuses');
         foreach ($this->cart->getProducts() as $product) {
             $status = $this->getProductStatusId($product['product_id']);
             if (in_array($status, $statuses)) {
                 $status = false;
             }

             $error = '';

             $quote_data = array();

             if ($status) {
                 $weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->config->get('qwqer_weight_class_id'));

                 $length = 0;
                 $width = 0;
                 $height = 0;

                 if ($address['iso_code_2'] == 'LV') {

                     $data_orders = $this->shipping_qwqer->generateOrderObjects($address, $this->shipping_qwqer->getDeliveryTypes());
                     if (!count($data_orders)) {
                         return array();
                     }
                     foreach ($data_orders as $key => $data_order) {
                         $params = array();
                         if ($data_order['real_type'] == "OmnivaParcelTerminal") {
                             $params['parcel_size'] = 'L';
                         }
                         $ret = $this->shipping_qwqer->calculatePrice($data_order, $params);
                         if (isset($ret['error'])) {
                             continue;
                         }
                         $prices[$key] = $ret;
                     }
                     //if there are no services available
                     if (!count($prices)) {
                         return array();
                     }

                     $data = array();


                     foreach ($prices as $key => $price) {
                         if (!isset($price['data']["real_type"])) {
                             continue;
                         }
                         $var = $this->language->get('text_title_' . $price['data']["real_type"]);
                         $key_r = mb_strtolower($price['data']["real_type"]);
                         if ($key_r == 'omnivaparcelterminal' && $this->request->get['route'] != 'api/shipping/methods') {
                             $terminals = $this->shipping_qwqer->getParcelTerminals();
                             if (isset($terminals['data']["omniva"]) && $terminals['data']["omniva"]) {
                                 //$this->document->addStyle('catalog/view/stylesheet/qwqer/autocomplete.min.css');
                                 $order_id = false;
                                 if (isset($this->session->data['order_id'])) {
                                     $order_id = $this->session->data['order_id'];
                                 }
                                 $s_id = false;

                                 if (isset($this->request->post["autoComplete"])) {
                                     $autocomplete = $this->request->post["autoComplete"];
                                     $this->session->data["autoComplete"] = $autocomplete;
                                 } else {
                                     $autocomplete = '';
                                 }

                                 if (isset($this->request->post["autoCompleteHidden"])) {
                                     $autocompletehidden = $this->request->post["autoCompleteHidden"];
                                     $this->session->data["autoCompleteHidden"] = $autocompletehidden;
                                 } else {
                                     $autocompletehidden = '';
                                 }


                                 $template = $this->load->view('extension/shipping/qwqer', array(
                                     'text_select_box' => $this->language->get('text_select_box'),
                                     'text_title_order_type' => $var,
                                     'terminals' => $terminals['data']["omniva"],
                                     'order_id' => $order_id,
                                     'session_id' => $s_id,
                                     'autocomplete' => $autocomplete,
                                     'autocompletehidden' => $autocompletehidden));
                             } else {
                                 $template = $this->load->view('extension/shipping/qwqer', array('text_title_order_type' => $var));
                             }
                         } else {
                             $template = $this->load->view('extension/shipping/qwqer', array('text_title_order_type' => $var));
                         }


                         $calculate = $this->currency->convert($price['data']['client_price'] / 100, 'EUR', $this->session->data['currency']);
                         $text = $this->currency->format($calculate,
                             $this->session->data['currency'],
                             $this->config->get('config_tax'));

                         $quote_data[$key_r] = array(
                             'code' => 'qwqer.' . $key_r,
                             'title' => $template,
                             'cost' => $this->currency->convert($price['data']['client_price'] / 100, 'EUR', $this->config->get('config_currency')),
                             'tax_class_id' => $this->config->get('qwqer_tax_class_id'),
                             'text' => $text
                         );
                     }

                     if (!count($quote_data)) {
                         $method_data = array();

                     } else {
                         $method_data = array(
                             'code' => 'qwqer.standart',
                             'title' => $this->language->get('text_title') . '<a href = "https://qwqer.lv/" target="_blank"><img src="catalog/view/images/qwqer.svg" alt="Qwqer service home page" style="margin-left:5px"></a>',
                             'quote' => $quote_data,
                             'sort_order' => $this->config->get('qwqer_sort_order'),
                             'error' => $error,
                         );
                     }


                     return $method_data;

                 }
             }

             $method_data = array();

             return $method_data;

         }


	}

    public function generateOrderObject($order_info){
        $address = array();
        foreach ($order_info as $key=>$value){
            if (strpos($key,'shipping_') !== false){
                $v = str_replace('shipping_','',$key);
                $address[$v] = $value;
            }
        }
        if($order_info['shipping_code'] == 'qwqer.omnivaparcelterminal'){
            $new_destination = $this->getParcelAddress($order_info);
            $address['new_destination'] = $new_destination;
        }
        $delivery_type = str_replace('qwqer.','',$address['code']);
        $delivery_types = $this->shipping_qwqer->getDeliveryTypes();
        $address['telephone']=$order_info['telephone'];
        $ret = $this->shipping_qwqer->generateOrderObjects($address,array($delivery_types[$delivery_type]));
        if (isset($ret[0])){
            return $ret[0];
        }
    }

    public function createOrder($order){
        if ($order){
            return $this->shipping_qwqer->createOrder($order);
        }
        else{
            return array('message'=>"fail");
        }
    }

    public function  addOrderData($order_info){
        if (isset($this->session->data['autoCompleteHidden'])){
            $order_info['autoCompleteHidden'] = $this->session->data['autoCompleteHidden'];
        }elseif(isset($this->request->data['autoCompleteHidden'])){
            $order_info['autoCompleteHidden'] = $this->request->data['autoCompleteHidden'];
        }
        $data = json_encode($order_info);
        $str = "SELECT COUNT(*) AS  total FROM " . DB_PREFIX . "qwqer_data where `order_id` = " . $order_info['order_id'];
        $isOrderExist = $this->db->query($str)->rows[0]['total'];
        if ($isOrderExist){
            $this->db->query("UPDATE " .DB_PREFIX. "qwqer_data SET `data` = '".$this->db->escape($data)."' where `order_id` = {$order_info['order_id']};");
        }else{
            $this->db->query("INSERT INTO " .DB_PREFIX. "qwqer_data (`key_hash`,`order_id`,`data`) VALUES('', {$order_info['order_id']}, '".$this->db->escape($data)."');");
        }
    }


    public function updateShippingMethod($order_id,$text){
        if ($order_id){
            $str = "UPDATE " . DB_PREFIX . "order SET `shipping_method` = '" . $this->db->escape($text) . "' WHERE `order_id` = " . $order_id;
            $this->db->query($str);
        }

    }

    public function getParcelAddress($order_info){
        $res = $this->db->query("SELECT * FROM " .DB_PREFIX. "qwqer_data WHERE `order_id` =". $order_info['order_id'] )->rows;
        if (count($res)){
            $rawObj = $res[0]['data'];
            $obj = json_decode($rawObj,true);
            $obj = $obj['autoCompleteHidden'];
            $obj = json_decode(html_entity_decode(strip_tags($obj)),true);
            if ($obj) {
                return $obj;
            }
        }
        return false;
    }

    public  function  getProductStatusId($id){
        return $this->db->query("SELECT `stock_status_id` FROM " .DB_PREFIX. "product WHERE `product_id` = {$id} limit 1")->rows[0]['stock_status_id'];
    }


}