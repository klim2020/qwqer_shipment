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

    /**
     * @return string[]
     */
    public function getOptions()
    {
        /*
         * @var library\qweqr\QwqerApi $this->shipping_qwqer
         * */
        return $this->shipping_qwqer->order_categories;
    }

    public function install(){
        $this->uninstall();
        $this->db->query("
			CREATE TABLE `" . DB_PREFIX . "qwqer_data` (
				`qwqer_id` INT(11) NOT NULL AUTO_INCREMENT,
				`key_hash` varchar(255) NULL,
				`order_id` varchar(255) NOT NULL,
				`data`  TEXT NULL,
                `qwqer_date` DATETIME NULL,
				`response` TEXT NULL,
				`address`  TEXT NULL,
				PRIMARY KEY (`qwqer_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
    }

    public function uninstall(){
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "qwqer_data`");
    }

    //generate a valid object and sends it to a QwQer library object generator
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
        if (isset($order_info['qwqer'])){
            $address['qwqer'] = $order_info['qwqer'];
        }
        $ret = $this->shipping_qwqer->generateOrderObjects($address,array($delivery_types[$delivery_type]));
        if (isset($ret[0])){
            return $ret[0];
        }else{
            return ;
        }
    }

    public function getParcelAddress($order_info){
        $res = $this->db->query("SELECT * FROM " .DB_PREFIX. "qwqer_data WHERE `order_id` =". $order_info['order_id'] )->rows;
        if (count($res)){
            $rawObj = $res[0]['data'];
            $obj = json_decode($rawObj,true);
            $obj = $obj['qwqer'];
            //$obj = json_decode(html_entity_decode(strip_tags($obj)),true);
            if ($obj) {
                return $obj;
            }
        }
        return false;
    }

    public function createOrder($order){
        if ($order){
            return $this->shipping_qwqer->createOrder($order);
        }
        else{
            return array('message'=>"fail");
        }
    }

}