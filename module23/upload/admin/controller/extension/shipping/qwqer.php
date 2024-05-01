<?php


use library\qweqr\QwqerApi;

/**
 *
 * @property QwqerApi $shipping_qwqer
 */
class ControllerExtensionShippingQwqer extends Controller {//

    private $error = array();


    //public $shipping_qwqer;
    public function __construct($registry)
    {
        parent::__construct($registry);

        require_once DIR_SYSTEM."library/qwqer/QwqerApi.php";
        new QwqerApi($registry);

    }

	public function index() {


        $this->load->model('extension/shipping/qwqer');

        if($this->shipping_qwqer->healthCheck()==false){
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], true));
        }

		$this->load->language('extension/shipping/qwqer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

        $this->document->addStyle('view/stylesheet/qwqer/autocomplete.min.css');

        $this->document->addScript('view/javascript/qwqer/autocomplete.min.js');


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('qwqer', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

            //deletion process if needed
            $this->delete();

            $tab = 'main';
            if (isset($this->request->get['tab']) && $this->request['tab']=='orders'){
                $tab = 'orders';
            }

            $this->response->redirect($this->url->link('extension/shipping/qwqer', 'token=' . $this->session->data['token'] . '&type=shipping'.'&tab='.$tab, true));
		}

        $data['tab'] = 'main';
        if (isset($this->request->get['tab']) && $this->request->get['tab']=='orders'){
            $data['tab'] = 'orders';
        }


        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (!isset($data['success'])) {
            $data['success'] = '';
        }

		if (isset($this->error['api'])) {
			$data['error_api'] = $this->error['api'];
		} else {
			$data['error_api'] = '';
		}

		if (isset($this->error['trade_pt'])) {
			$data['error_trade_pt'] = $this->error['trade_pt'];
		} else {
			$data['error_trade_pt'] = '';
		}

        if (isset($this->error['error_telephone1'])) {
            $data['error_telephone1'] = $this->error['error_telephone1'];
        } else {
            $data['error_telephone1'] = '';
        }

        if (isset($this->error['trade_cat'])) {
            $data['error_trade_cat'] = $this->error['trade_cat'];
        } else {
            $data['error_trade_cat'] = '';
        }

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/qwqer', 'token=' . $this->session->data['token'], true)
		);

        // v23 language
        $langs_values = array('heading_title',
                        'entry_api',
                        'text_edit',
                        'entry_trade_pt',
                        'entry_trade_cat',
                        'entry_address',
                        'entry_weight_class',
                        'entry_tax_class',
                        'entry_geo_zone',
                        'entry_status',
                        'entry_sort_order',
                        'text_button_validate',
                        'help_address_tooltip',
                        'help_address_city',
                        'button_save',
                        'button_cancel',
                        'help_weight_class',
                        'text_none',
                        'text_all_zones',
                        'text_enabled',
                        'text_disabled',
                        'entry_telephone',
                        'help_telephone',
                        'help_address',
                        'tab_general',
                        'tab_orders',
                        'text_order',
                        'text_remote_order',
                        'text_delivery_type',
                        'text_category',
                        'text_created',
                        'text_finished_at',
                        'text_invoice',
                        'ScheduledDelivery',
                        'OmnivaParcelTerminal',
                        'ExpressDelivery',
                        'text_address',
                        'text_status',
                        'text_create',
                        'text_request',
                        'button_delete',
                        'text_confirm',
                        'text_telephone',
                        'entry_hide_status',
                        'error_not_saved',
                        'tab_info',
                        'text_company_name',
                        'text_company_type',
                        'text_company_address',
                        'text_company_map',
                        'text_working_time',
                        'text_working_day',
                        'text_working_from',
                        'text_working_to',
                        'text_address_link',
                        'text_checkout_type',
                        'help_checkout_type'
            );
        foreach ($langs_values as $lang){
            $data[$lang] = $this->language->get($lang);
        }

		$data['action'] = $this->url->link('extension/shipping/qwqer', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true);

		if (isset($this->request->post['qwqer_trade_pt'])) {
			$data['qwqer_trade_pt'] = $this->request->post['qwqer_trade_pt'];
		} else {
			$data['qwqer_trade_pt'] = $this->config->get('qwqer_trade_pt');
		}

		if (isset($this->request->post['qwqer_api'])) {
			$data['qwqer_api'] = $this->request->post['qwqer_api'];
		} else {
			$data['qwqer_api'] = $this->config->get('qwqer_api');
		}

        if (isset($this->request->post['qwqer_trade_cat'])) {
            $data['qwqer_trade_cat'] = $this->request->post['qwqer_trade_cat'];
        } else {
            $data['qwqer_trade_cat'] = $this->config->get('qwqer_trade_cat');
        }

        if (isset($this->request->post['qwqer_telephone'])) {
            $data['qwqer_telephone'] = $this->request->post['qwqer_telephone'];
        } else {
            $data['qwqer_telephone'] = $this->config->get('qwqer_telephone');
        }

        if (isset($this->request->post['qwqer_address'])) {
            $data['qwqer_address'] = $this->request->post['qwqer_address'];
        } else {
            $data['qwqer_address'] = $this->config->get('qwqer_address');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['config_complete_status'])) {
            $data['config_complete_status'] = $this->request->post['config_complete_status'];
        } elseif ($this->config->get('config_complete_status')) {
            $data['config_complete_status'] = $this->config->get('config_complete_status');
        } else {
            $data['config_complete_status'] = array();
        }

        foreach ( $this->shipping_qwqer->getOrderCategories() as $option){
            $data['qwqer_trade_cat_options'][] = $this->language->get('qwqer_opt_'.$option);
        }

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['qwqer_tax_class_id'])) {
			$data['qwqer_tax_class_id'] = $this->request->post['qwqer_tax_class_id'];
		} else {
			$data['qwqer_tax_class_id'] = $this->config->get('qwqer_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        $data['token']  = $this->session->data['token'];

        $page = 1;
        if(isset($this->request->get['page']) && $this->request->get['page']){
            $page = $this->request->get['page'];
        }

        $limit = $this->config->get('config_limit_admin');


        $return_total = $this->shipping_qwqer->getOrdersCount();

        $pagination = new Pagination();
        $pagination->total = $return_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('shipping/qwqer', 'token=' . $this->session->data['token'] .  '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($return_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($return_total - $this->config->get('config_limit_admin'))) ? $return_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $return_total, ceil($return_total / $this->config->get('config_limit_admin')));

        $results = $this->shipping_qwqer->getOrdersList(array('page'=>$page,'limit'=>$limit));
        $temp = array();

        //generate order tab data
        $data['delete'] = $this->url->link('extension/shipping/qwqer/delete', 'token=' . $this->session->data['token'], 'SSL');
        foreach ($results as $result){
            if (isset($result["response"])
                && is_array($result["response"])){
                if($result["response"]
                    && isset($result["response"]["message"])){
                    continue;
                }
                if (empty($result["response"])){
                    $result["response"]["data"]['status'] = 'Not Created';
                }
            }
            if (isset($result["response"]['data']['id'])){
                $res = $this->shipping_qwqer->getDeliveryOrder($result["response"]['data']['id']);
            }

            if (isset($res['data']['id'])){
                $this->shipping_qwqer->addResponseRecord($res,$result['order_id']);
                $result['response'] = $res;
                $res = null;
            }

            $order_link   = '';
            $invoice_link = '';

            $createlink = false;
            if (isset ($result["response"]["data"]['status']) && ($result["response"]["data"]['status'] == 'Not Created')){
                $createlink = $this->url->link('extension/shipping/qwqer/create', 'token=' . $this->session->data['token'].'&order_id='.$result['order_id'], 'SSL');
            }
            $order_link = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'], 'SSL');
            $invoice_link = false;

            if (isset($result['response']['data']['id']) && $result['response']['data']['id']){
                $invoice_link = $this->shipping_qwqer->getWeburl()."/storage/delivery-order-covers/{$result['response']['data']['id']}.pdf";
            }
            $created_at = 'none';
            if (isset($result['response']['data']['created_at'])){
                $created_at = date("F, d, Y, g:i ",strtotime($result['response']['data']['created_at']));
            }

            $delivery = $result['data']["shipping_method"]["title"];
            if(isset($result['response']['data']['real_type'])){
                $v = $result['response']['data']['real_type'];
                if (isset($data[$v])){
                    $delivery = $data[$v];
                }else{
                    $delivery = 'none';
                }
            }
            //$address = $result['data']['qwqer']['destinations'][0]['address'];
            if (isset($result['data']['qwqer'])){
                $address = $result['data']['qwqer']['destinations'][0]['address'];
            }elseif(isset($result["response"]["data"]["places"][0]["address"])){
                $address = $result["response"]["data"]["places"][0]["address"];
            }else{
                $address = "none";
            }

            if (isset($result['response']["data"]["places"]) &&
                is_array($result['response']["data"]["places"]) &&
                !empty($result['response']["data"]["places"])){

                foreach ($result['response']["data"]["places"] as $val){
                    if (isset($val['type']) && $val['type']=='Destination'){
                        $address =   $val['address'];
                    }
                }
            }

            $temp[] = array(
                'qwqer_id'       => $result['qwqer_id'],
                'key_hash'       => $result['key_hash'],
                'order_id'       => $result['order_id'],
                'response'       => (isset($result['response']['data']))?$result['response']['data']:'',
                'data'           => $result['data'],
                'invoice_link'   => $invoice_link,
                'created_at'     => $created_at,
                'order_link'     => $order_link,
                'createlink'     => $createlink,
                'delivery_type'  => $delivery,
                'address'        => $address,
                'date'           => $result["data"]["date_added"]
            );
        }

        $data['orders'] = $temp;

        $working_time = $this->shipping_qwqer->getInfo();

        if ($working_time){
            usort($working_time['working_hours'], function ($a,$b){
                return date('N', strtotime($a['day_of_week'])) > date('N', strtotime($b['day_of_week']));
            });
        }

        $data['working_time'] = $working_time;

		if (isset($this->request->post['qwqer_geo_zone_id'])) {
			$data['qwqer_geo_zone_id'] = $this->request->post['qwqer_geo_zone_id'];
		} else {
			$data['qwqer_geo_zone_id'] = $this->config->get('qwqer_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['qwqer_status'])) {
			$data['qwqer_address_object'] = $this->request->post['qwqer_address_object'];
		} else {
			$data['qwqer_address_object'] = $this->config->get('qwqer_address_object');
		}
        //HOWTO: decode JSON.stringyfy on php side properly
        $arr = json_decode( html_entity_decode( stripslashes ($data['qwqer_address_object'] ) ), false );

        if (isset($this->request->post['qwqer_status'])) {
            $data['qwqer_status'] = $this->request->post['qwqer_status'];
        } else {
            $data['qwqer_status'] = $this->config->get('qwqer_status');
        }

		if (isset($this->request->post['qwqer_sort_order'])) {
			$data['qwqer_sort_order'] = $this->request->post['qwqer_sort_order'];
		} else {
			$data['qwqer_sort_order'] = $this->config->get('qwqer_sort_order');
		}

        if (isset($this->request->post['qwqer_is_prod'])) {
            $data['qwqer_is_prod'] = $this->request->post['qwqer_is_prod'];
        } else {
            $data['qwqer_is_prod'] = $this->config->get('qwqer_is_prod');
        }

        if (isset($this->request->post['qwqer_checkout_type'])) {
            $data['qwqer_checkout_type'] = $this->request->post['qwqer_checkout_type'];
        } else {
            $data['qwqer_checkout_type'] = $this->config->get('qwqer_checkout_type');
        }

        $checkout_types = array();
        foreach ($this->shipping_qwqer->getCheckoutTypes() as $key=>$type){
            $val = 'checkout_type_'.$type;
            $checkout_types[$key] = $this->language->get($val);
        }
        $data['qwqer_checkout_types'] = $checkout_types;

        //statuses options
        $this->load->model('localisation/stock_status');

        $data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

        if (isset($this->request->post['qwqer_hide_statuses'])) {
            $data['qwqer_hide_statuses'] = $this->request->post['qwqer_hide_statuses'];
        } elseif ($this->config->get('qwqer_hide_statuses')) {
            $data['qwqer_hide_statuses'] = $this->config->get('qwqer_hide_statuses');
        } else {
            $data['qwqer_hide_statuses'] = array();
        }

        $data['help_address_city'] = $this->language->get('help_address_city');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/qwqer', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/qwqer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['qwqer_api'])) {
			$this->error['api'] = $this->language->get('error_api');
		}

		if (!preg_match('/^[0-9]{1,4}$/', $this->request->post['qwqer_trade_pt'])) {
			$this->error['trade_pt'] = $this->language->get('error_trade_pt');
		}

        //qwqer_trade_cat

        if (empty($this->request->post['qwqer_trade_cat'])) {
            $this->error['trade_cat'] = $this->language->get('error_trade_cat');
        }

        if (empty($this->request->post['qwqer_telephone'])) {
            $this->error['error_telephone1'] = $this->language->get('error_telephone');
        }

        if($this->error && !isset($this->error['warning'])){
            $this->error['warning'] = $this->language->get('error_not_saved');
        }

		return !$this->error;
	}

    public function  autocomplete(){
        $error = [];

        if (!isset($this->request->post['input'])){
            array_push($error,"input is not defined");
        }
        if (!isset($this->request->post['api_token'])){
            array_push($error,"api_token is not defined");
        }
        if (!isset($this->request->post['trade_point'])){
            array_push($error,"trade_point is not defined");
        }
        if (count($error)!=0){
            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(array('message'=>$error)));
            return;
        }

        $q              = $this->request->post['input'];
        $token          = $this->request->post['api_token'];
        $trade_point    = $this->request->post['trade_point'];

        $this->shipping_qwqer->setToken($token);
        $this->shipping_qwqer->setTradePt($trade_point);

        $r = $this->shipping_qwqer->placeAutocomplete($q);

        if (isset($r['message'])){
            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($r));

    }

    public function geocode(){
        if (isset($this->request->post['address'])){
            $address = $this->request->post['address'];
            //$city    = $this->request->post['city'];
            //$locality= $this->request->post['locality'];

            $arr = $this->shipping_qwqer->getGeoCode($address);


            if ( $arr && isset($arr['data']['address']) && !isset($arr['data']['message'])){
                $this->response->addHeader('Content-Type: application/json');
            }
            else{
                $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
                $this->response->addHeader('Content-Type: application/json');

            }
            $this->response->setOutput(json_encode($arr));
            return;

        }else{
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(array('error'=>'address is not defined')));
            return;
        }


    }

    public function install(){
        $this->load->model('extension/shipping/qwqer');
        $this->model_extension_shipping_qwqer->install();
    }

    public function uninstall(){
        $this->load->model('extension/shipping/qwqer');
        $this->model_extension_shipping_qwqer->uninstall();
    }

    public function create(){

        $order_id = $this->request->get['order_id'];
        $this->load->model('sale/order');
        $order_info_tmp =  $this->model_sale_order->getOrder($order_id);

        if ($order_info_tmp == null){
            $this->response->redirect($this->url->link('shipping/qwqer', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if (strpos($order_info_tmp["shipping_code"],'qwqer.')!==false){

            $this->load->model('extension/shipping/qwqer');
            $order_info_tmp['qwqer'] = $this->shipping_qwqer->getOrderServerData($order_info_tmp['order_id']);
            //$data_order =  $this->model_extension_shipping_qwqer->generateOrderObject($order_info_tmp);

            $r_data = $this->shipping_qwqer->getOrderServerData($order_info_tmp['order_id']);
            if (isset($r_data['qwqer'])){
                if (isset($r_data['qwqer']['real_type']) &&  $r_data['qwqer']['real_type'] == "OmnivaParcelTerminal"){
                    $r_data['qwqer']['parcel_size'] = "L";
                }
                unset($r_data['qwqer']['pickup_datetime']);

                $response = $this->model_extension_shipping_qwqer->createOrder($r_data['qwqer']);
            }else{
                $data_order =  $this->model_extension_shipping_qwqer->generateOrderObject($order_info_tmp);
                $response = $this->model_extension_shipping_qwqer->createOrder($data_order);
            }

            if (isset($response['data']['id']) && $response['data']['id']){
                $this->shipping_qwqer->addResponseRecord($response, $order_id);
            }

        }

        $this->response->redirect($this->url->link('extension/shipping/qwqer', 'token=' . $this->session->data['token'], 'SSL'));

    }

    public  function delete(){
        $selected = isset($this->request->post['selected'])?$this->request->post['selected']:'';
        if (is_array($selected)){
            foreach ($selected as $item){
                $this->shipping_qwqer->deleteOrder($item);
            }
        }
    }

}