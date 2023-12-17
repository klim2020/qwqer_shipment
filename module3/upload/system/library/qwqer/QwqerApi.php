<?php
namespace library\qweqr;


/**
 * class that wraps server connection
 * https://qwqer-api-docs.netlify.app/api/resources/basic-concept
 *
 */
class QwqerApi {


    /**
     * @link https://qwqer-api-docs.netlify.app/api/resources/enumerations#delivery-order-categories
     * @var string[]
     */
    public $order_categories = array(
        'Other',
        'Flowers',
        'Food',
        'Electronics',
        'Cake',
        'Present',
        'Clothes',
        'Document',
        'Jewelry',
    );

    /**
     * @return string[]
     */
    public function getOrderCategories()
    {
        return $this->order_categories;
    }

    /**Order types
     *
     * @var string[]
     */
    public $delivery_types = array(
        'ScheduledDelivery',
        'ExpressDelivery',
        'OmnivaParcelTerminal',
    );

    /**
     * @return string[]
     */
    public function getDeliveryTypes()
    {
        return $this->delivery_types;
    }

    private  $token;

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getTradePt()
    {
        return $this->trade_pt;
    }

    /**
     * @param mixed $trade_pt
     */
    public function setTradePt($trade_pt)
    {
        $this->trade_pt = $trade_pt;
    }
    private $registry;
    private  $trade_pt;

    //Api url
    private  $weburl                = "https://qwqer.hostcream.eu/api/v1/plugins/opencart";
    private  $autocompleteUrl       = '/places/autocomplete';
    private  $geoCodeUrl            = '/places/geocode';
    private  $getPriceUrl           = '/clients/auth/trading-points/{trading_points}/delivery-orders/get-price';
    private  $getParcelMachinesUrl  = '/parcel-machines';
    private  $createOrderUrl;

    public function __construct($registry)
    {

        $this->token    = $registry->get('config')->get('shipping_qwqer_api');
        $this->trade_pt = $registry->get('config')->get('shipping_qwqer_trade_pt');

        $this->autocompleteUrl      = $this->weburl . $this->autocompleteUrl;
        $this->geoCodeUrl           = $this->weburl . $this->geoCodeUrl;
        $this->getPriceUrl          = $this->weburl . $this->getPriceUrl;
        $this->createOrderUrl       = $this->weburl . '/clients/auth/trading-points/' . $this->trade_pt . '/delivery-orders';
        $this->getParcelMachinesUrl = $this->weburl . $this->getParcelMachinesUrl;

        $this->getPriceUrl  = str_replace(['{trading_points}'],[$this->trade_pt],$this->getPriceUrl);
        $registry->set('shipping_qwqer',$this);
        $this->registry = $registry;

        $this->telephone    = $registry->get('config')->get('shipping_qwqer_telephone');

        foreach ($this->delivery_types as $delivery_type){
            $ret[mb_strtolower($delivery_type)] = $delivery_type;
        }
        $this->delivery_types = $ret;
        
    }

    /**
     * @return CurlHandle|false
     */
    private function getCurlHandle()
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->token,
            'Accept: application/json'
        ]);

        return $curl;
    }

    //!!!Api

    /**  Geocode for  place
     * @link https://qwqer-api-docs.netlify.app/api/endpoints/places#address-geocoding
     * @param $adress
     * @return array
     */
    public function getGeoCode($address,$city='riga',$locality='lv')
    {
        $curl = $this->getCurlHandle();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->geoCodeUrl,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS => array('address' => $address, 'country'=>$locality, 'locality'=>$city),

        ));


        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);
        $response = json_decode($response,true);
        return $response;
    }


    /** Generate order object this is array for all delivery options, take a look at $delivery_types array
     * @param $address
     * @return array
     */
    public function generateOrderObjects($address, $delivery_objects=array()){

        $api_key  = $this->token;
        $trade_pt = $this->trade_pt;

        $store_info  = json_decode( html_entity_decode( stripslashes ($this->registry->get('config')->get('shipping_qwqer_address_object' ) ) ), true );
        //Pickup address wasnt added in admin dashboard
        if (!isset($store_info['data']['address'])){
            return array();
        }
        //Store data
        $store_phone = $this->telephone;
        $store_phone = $this->formatAndValidatePhone($store_phone);

        $store_name = $this->registry->get('config')->get('config_name');
        //Order Shipping data
        $shipping_category = $this->order_categories[$this->registry->get('config')->get('shipping_qwqer_trade_cat')];

        if(isset($address['telephone'])){
            $shipping_phone = $address['telephone'];
        }elseif((isset($this->registry->get('request')->post["customer"]["telephone"])) && $this->registry->get('request')->post["customer"]["telephone"]){
            $shipping_phone = $this->registry->get('request')->post["customer"]["telephone"];
        }elseif((isset($this->registry->get('session')->data["guest"]["telephone"]))){
            $shipping_phone = $this->registry->get('session')->data["guest"]["telephone"];
        }else{
            $shipping_phone = $this->registry->get('customer')->getTelephone();
        }


        $shipping_phone = $this->formatAndValidatePhone($shipping_phone);
        if ($shipping_phone == false){
            return array();
        }

        /* Get client coordinates and address */
        $data_info_client =  $address['address_1'] . ' ' . $address['address_2'];

        $address_city = mb_strtolower($address['city']);
        $address_country = mb_strtolower($address['iso_code_2']);


        if (isset($address['new_destination'])){
            $info_client['data'] = $address['new_destination'];
            $info_client['data']['address'] = $address['new_destination']['name'];
        }else{
            $info_client = $this->getGeoCode($data_info_client,$address_city,$address_country);
        }


        //Client data not valid
        if (!isset($info_client['data']['coordinates'])){
            return array();
        }

        $storeOwnerAddress["address"] = $store_info['data']['address'];
        $storeOwnerAddress["coordinates"] = $store_info['data']['coordinates'];
        $storeOwnerAddress["name"] = $store_name;
        $storeOwnerAddress["phone"] = $store_phone;

        $clientOwnerAddress = array();
        $clientOwnerAddress["address"] = $info_client['data']['address'];
        $clientOwnerAddress["coordinates"] = $info_client['data']['coordinates'];
        $clientOwnerAddress["name"] = $address["firstname"]. ' '. $address["lastname"];
        $clientOwnerAddress["phone"] = $shipping_phone;

        //Creating all needed objects
        foreach ($delivery_objects as $delivery_type){
            $data_orders[] = array(
                'type' => 'Regular',
                'category' => $shipping_category,
                'real_type' => $delivery_type,
                'origin' => $storeOwnerAddress,
                'destinations' => [$clientOwnerAddress],
            );
        }

        return  $data_orders;

    }

    /** calculate price for a delivery
     * @link  https://qwqer-api-docs.netlify.app/api/endpoints/delivery-orders#calculate-price-of-order-before-create-them
     * @param $data_order - order ready object
     * @return mixed|null
     */
    public function calculatePrice($data_order,$params=array()){

        if (count($params)){
            $data_order = array_merge($data_order,$params);
        }
        $qq = http_build_query($data_order);
        $curl = $this->getCurlHandle();
        $data = http_build_query($data_order);
        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->getPriceUrl,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $data,

        ));

        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);
        $response = json_decode($response,true);
        return $response;

    }

    /** Creates an order
     * @link https://qwqer-api-docs.netlify.app/api/endpoints/delivery-orders#create-delivery-order
     * @param $data_order
     * @return mixed|null
     */
    public function createOrder($data_order){
        if($data_order['real_type'] == 'OmnivaParcelTerminal'){
            $data_order['parcel_size']='L';
            //$data_order['destinations'][0]['phone'] = '+37167224273';
        }

        $curl = $this->getCurlHandle();
        $data = http_build_query($data_order);
        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->createOrderUrl,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $data,

        ));

        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);
        $response = json_decode($response,true);
        return $response;
    }

    /** Autocomplete for place search
     * @link https://qwqer-api-docs.netlify.app/api/endpoints/places#addresses-list-by-prompt
     * @param $q
     * @return bool|string
     */
    public function placeAutocomplete($q,$country = 'lv')
    {

        $curl = $this->getCurlHandle();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->autocompleteUrl,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS => array('input' => $q,'country'=>$country),

        ));


        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);
        $response = json_decode($response,true);
        return $response;
    }

    /** Get parcel machine terminals
     * @return array
     */
    public function getParcelTerminals(){

        $curl = $this->getCurlHandle();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getParcelMachinesUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);
        $response = json_decode($response,true);
        return $response;

    }

    public function addResponseRecord($response,$order_id){
        if (isset($response) && $response){
            $data = json_encode($response);
            $str = "UPDATE " . DB_PREFIX . "qwqer_data set `response` = '" . $this->registry->get('db')->escape($data) . "' where `order_id` = {$order_id};";
            $this->registry->get('db')->query($str);
        }

    }

    public function getResponse($order_id){
        $str = "SELECT `response` FROM " .DB_PREFIX. "qwqer_data WHERE `order_id` =". $order_id;
        $ret = $this->registry->get('db')->query($str );
        if ($ret->rows && isset($ret->rows[0]['response'])){
            $ret = $ret->rows[0]['response'];
            $ret = json_decode($ret,true);
        }else{
            $ret = [];
        };

        return $ret;
    }

    //Adds additional hidden fields if terminal payment have been chosen
    public  function  addHiddenField(){
        $s = 1;
    }

    /*
    * Checkers and validators
    */


    /**
     * @param mixed $shipping_phone
     */
    public function formatAndValidatePhone($shipping_phone)
    {
        $shipping_phone = '+371' . preg_replace(array('/\s/m', '/^\+/m', '/^\+371/m', '/^371/m', '/-/m'), array('', '', '', '', ''), $shipping_phone);
        if (preg_match('/^(\+371)?[\d]{7,8}$/', $shipping_phone) == false) {
            return false;
        }
        return $shipping_phone;
    }

    /** Checks if city is Riga
     * @param mixed $shipping_phone
     */
    public function checkCity($city)
    {
        $cities   = array('riga','рига','rīga');
        $raw_city = preg_replace("/[^A-Za-z0-9 ]/", '', $city);
        $raw_city = strtolower($raw_city);
        if (in_array($raw_city,$cities)){
            return true;
        }
        return false;
    }


}