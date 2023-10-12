<?php
/**
 * @version    N/A, base on qwqer API update on 18 April 2016
 * @link       https://developers.qwqer.com.au/docs/reference
 * @since      2.3.0.2   Update on 21 March 2017
 */

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

    private  $trade_pt;
    private  $weburl = "https://qwqer.hostcream.eu";
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->token    = $this->config->get('shipping_qwqer_api');
        $this->trade_pt = $this->config->get('shipping_qwqer_trade_pt');
    }

	public function getQuote($address) {
		$this->load->language('extension/shipping/qwqer');
        $this->load->model('extension/shipping/qwqer');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_qwqer_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_qwqer_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$error = '';

		$quote_data = array();

		if ($status) {
			$weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->config->get('shipping_qwqer_weight_class_id'));

			$length = 0;
			$width  = 0;
			$height = 0;

			if ($address['iso_code_2'] == 'LV') {

                $data_order = $this->generateOrderObject($address);
                $price = $this->calculatePrice($data_order);
                if (!isset($price['data']['client_price'])){
                    return [];
                }
                $data       = array();
                $template   = $this->load->view('extension/shipping/qwqer', $data);
                $calculate  = $this->currency->convert($price['data']['client_price']/100, 'EUR', $this->session->data['currency']);
                $text       =  $this->currency->format($calculate,
                                                        $this->session->data['currency'],
                                                        $this->config->get('config_tax'));
                $quote_data['standart'] = array(
                    'code'         => 'qwqer.standart',
                    'title'        => $template,
                    'cost'         => $this->currency->convert($price['data']['client_price']/100, 'EUR', $this->config->get('config_currency')),
                    'tax_class_id' => $this->config->get('shipping_qwqer_tax_class_id'),
                    'text'         => $text
                );

                $method_data = array(
                    'code'       => 'qwqer.standart',
                    'title'      => $this->language->get('text_title').'<a href = "https://qwqer.lv/" target="_blank"><img src="catalog/view/images/qwqer.svg" alt="Qwqer service home page"></a>',
                    'quote'      => $quote_data,
                    'sort_order' => $this->config->get('shipping_qwqer_sort_order'),
                    'error'      => $error,
                );

                return $method_data;

			}
		}

		$method_data = array();

        return $method_data;




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
        $response = json_decode($response,true);
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

    public function generateOrderObject($address){

        $api_key  = $this->config->get('shipping_qwqer_api');
        $trade_pt = $this->config->get('shipping_qwqer_trade_pt');

        $store_info  = json_decode( html_entity_decode( stripslashes ($this->config->get('shipping_qwqer_address_object' ) ) ), true );
        $store_phone = $this->config->get('config_telephone');
        $store_phone = '+371'.preg_replace(array('/\s/m','/^\+/m','/^\+371/m','/^371/m'),array('','','',''),$store_phone);
        $store_name = $this->config->get('config_name');

        $shipping_category = $this->options[$this->config->get('shipping_qwqer_trade_cat')];
        $shipping_phone = $this->customer->getTelephone();

        if (isset($this->session->data["guest"]["telephone"])){
            $shipping_phone = $this->session->data["guest"]["telephone"];
        }
        $shipping_phone = '+371'.preg_replace(array('/\s/m','/^\+/m','/^\+371/m','/^371/m'),array('','','',''),$shipping_phone);

        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $api_key,
        );

        if (!isset($store_info['data']))

        $storeOwnerAddress = array();
        if (!isset($store_info['data']['address'])){
            return array();
        }
        $storeOwnerAddress["address"] = $store_info['data']['address'];
        $storeOwnerAddress["coordinates"] = $store_info['data']['coordinates'];

        /*
         * Get client coordinates and address
         */
        $data_info_client =  $address['address_1'] . ' ' . $address['address_2'] . ' ' . $address['city'] . ' '.$address['country'];

        $info_client = $this->getGeoCode($data_info_client);

        //TODO Add handlers for valid client data
        if (!isset($info_client['data']['coordinates'])){
            return array();
        }

        $clientOwnerAddress = array();
        $clientOwnerAddress["address"] = $info_client['data']['address'];
        $clientOwnerAddress["coordinates"] = $info_client['data']['coordinates'];

        /*
         * Create order
         */
        $storeOwnerAddress["name"] = $store_name;
        $storeOwnerAddress["phone"] = $store_phone;

        $clientOwnerAddress["name"] = $address["firstname"]. ' '. $address["lastname"];
        $clientOwnerAddress["phone"] = $shipping_phone;
        $data_order = array(
            'type' => 'Regular',
            'category' => $shipping_category,
            'real_type' => 'ScheduledDelivery',
            'origin' => $storeOwnerAddress,
            'destinations' => [$clientOwnerAddress],
        );
        return  $data_order;

    }

    public function calculatePrice($data_order){



        $curl = $this->getCurlHandle();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->weburl.'/api/v1/clients/auth/trading-points/'.$this->trade_pt.'/delivery-orders/get-price',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data_order),

        ));

        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);
        $response = json_decode($response,true);
        return $response;

    }

    public function createOrder($data_order){
        $curl = $this->getCurlHandle();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->weburl.'/api/v1/clients/auth/trading-points/' . $this->trade_pt . '/delivery-orders',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data_order),

        ));

        $response = curl_exec($curl);
        $r = mb_detect_encoding($response);
        $response = mb_convert_encoding($response,$r,'utf-8');
        curl_close($curl);
        $response = json_decode($response,true);
        return $response;
    }
}