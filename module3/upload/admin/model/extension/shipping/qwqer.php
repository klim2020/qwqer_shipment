<?php


class ModelExtensionShippingQwqer extends Model {
    //maps language with controller
    public $options = array(
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

    private  $token;
    private  $weburl = "https://qwqer.hostcream.eu";
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->token = $this->config->get('shipping_qwqer_api');
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
    public  function  echo(){
        return 'dd';
    }

    /** Autocomplete for place search
     * @param $q
     * @return bool|string
     */
    public function placeAutocomplete($q,$token,$trade_point)
    {
        $this->token = $token;
        $curl = $this->getCurlHandle();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->weburl.'/api/v1/places/autocomplete/',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS => array('input' => $q),

        ));


        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);

        return $response;
    }

    /**  Geocode for  place
     * @param $adress
     * @return bool|string
     */
    public function getGeoCode($address)
    {
        $curl = $this->getCurlHandle();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->weburl.'/api/v1/places/geocode/',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS => array('address' => $address),

        ));


        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);

        return $response;
    }

    /**
     * @return CurlHandle|false
     */
    public function getCurlHandle()
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->token,
            'Accept: application/json'
        ]);

        return $curl;
    }


    public function install(){
        $this->uninstall();
        $this->db->query("
			CREATE TABLE `" . DB_PREFIX . "qwqer_data` (
				`qwqer_id` INT(11) NOT NULL AUTO_INCREMENT,
				`key_hash` varchar(255) NOT NULL,
				`cart_id` varchar(255) NOT NULL,
				PRIMARY KEY (`qwqer_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
    }

    public function uninstall(){
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "qwqer_data`");
    }

}