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

        foreach ($this->language->all() as $key=>$lang_val){
            $lang[$key] = htmlentities($lang_val);
        }

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
         //$statuses = $this->config->get('qwqer_hide_statuses');
         foreach ($this->cart->getProducts() as $product) {
             $status = $this->getProductStatusId($product['product_id']);
             //if (in_array($status, $statuses)) {
             //    $status = false;
             //}

             $error = '';

             $quote_data = array();

             if ($status) {
                 $weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->config->get('qwqer_weight_class_id'));

                 $length = 0;
                 $width = 0;
                 $height = 0;
                     $data_types = $this->shipping_qwqer->getDeliveryTypes();
                     foreach ($data_types as $type){

                         $key_r = mb_strtolower($type);
                         $price = $this->shipping_qwqer->generateDeliveryCost($key_r);
                         $template = $this->load->view('extension/shipping/qwqer', array('text_title_order_type' => $type, 'langs'=>$lang));
                         $quote_data[$key_r] = array(
                             'code' => 'qwqer.' . $key_r,
                             'title' => $template,
                             'cost' =>  $price/100,//$this->currency->convert(300 / 100, 'EUR', $this->config->get('config_currency')),
                             'tax_class_id' => $this->config->get('qwqer_tax_class_id'),

                             'text' => ($price)?$this->currency->format($price/100,  $this->session->data['currency'], $this->config->get('config_tax')):"",
                         );
                     }

                     $token = rand(100000000,999999999);
                     $this->session->data['qwqer_token']  = $token;

                     if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                         $url = HTTPS_SERVER;
                     } else {
                         $url  = HTTP_SERVER;
                     }
                     if (isset($this->session->data['shipping_method']['code'])){
                         preg_match('/[\s*\.](.*)/m', $this->session->data['shipping_method']['code'], $matches, PREG_OFFSET_CAPTURE);
                         $currentSelection = $matches[1][0];
                     }else{
                         $currentSelection = '';
                     }

                     $price = $this->shipping_qwqer->generateDeliveryCost($currentSelection);
                     $method_data = array(
                         'code' => 'qwqer.standart',
                         'title' => $this->load->view('extension/shipping/qwqer_title', array('current_price'=>$price, 'text_title' => $this->language->get('text_title'), 'token'=>$token, 'langs'=>$lang,'url'=>$url)),//
                         'quote' => $quote_data,
                         'sort_order' => $this->config->get('qwqer_sort_order'),
                         'error' => $error,
                     );
                     return $method_data;
             }
             return [];
         }
	}

    //generates a propper valid object for Qwqer library
    public function generateOrderObject($name,$phone,$address,$type){
        $arr['code'] = $type;
        //if($order_info['shipping_code'] == 'qwqer.omnivaparcelterminal'){
        //    $new_destination = $this->getParcelAddress($order_info);
        //    $address['new_destination'] = $new_destination;
        //}
        $delivery_type = str_replace('qwqer.','',$arr['code']);
        $delivery_types = $this->shipping_qwqer->getDeliveryTypes();

        $arr['telephone']  = $phone;
        $arr['firstname']  = $name;
        $arr['address_1']  = $address['name'];
        $arr['iso_code_2'] = 'LV';
        $arr['address_2'] = '';
        $arr["lastname"] = '';
        $arr['city'] = '';
        if (isset($address['coordinates'])){
            $arr['terminal'] = $address;
        }


        $ret = $this->shipping_qwqer->generateOrderObjects($arr,array($delivery_types[$delivery_type]));
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

        $data['shipping_address'] =  $order_info['shipping_address'];
        $data['payment_method'] =  $order_info['payment_method'];
        $data['order_id'] =  $order_info['order_id'];
        if (isset($order_info['qwqer']) && $order_info['qwqer']) {
            $data['qwqer'] = $order_info['qwqer'];
        }
        if (isset($order_info['qwqer_price']) && $order_info['qwqer_price']){
            $data['qwqer_price'] =  $order_info['qwqer_price'];
        }

        $data['shipping_method'] =  $order_info['shipping_method'];
        $data['date_added']  = date('Y-m-d H:i');
        $data = json_encode($data);
        //$data = trim( addslashes( json_encode( $order_info ) ) );
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