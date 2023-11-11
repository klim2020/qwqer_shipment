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
		$this->load->language('extension/shipping/qwqer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

        $this->document->addStyle('view/stylesheet/qwqer/autocomplete.min.css');

        $this->document->addScript('view/javascript/qwqer/autocomplete.min.js');


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_qwqer', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/qwqer', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/qwqer', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		if (isset($this->request->post['shipping_qwqer_trade_pt'])) {
			$data['shipping_qwqer_trade_pt'] = $this->request->post['shipping_qwqer_trade_pt'];
		} else {
			$data['shipping_qwqer_trade_pt'] = $this->config->get('shipping_qwqer_trade_pt');
		}

		if (isset($this->request->post['shipping_qwqer_api'])) {
			$data['shipping_qwqer_api'] = $this->request->post['shipping_qwqer_api'];
		} else {
			$data['shipping_qwqer_api'] = $this->config->get('shipping_qwqer_api');
		}

        if (isset($this->request->post['shipping_qwqer_trade_cat'])) {
            $data['shipping_qwqer_trade_cat'] = $this->request->post['shipping_qwqer_trade_cat'];
        } else {
            $data['shipping_qwqer_trade_cat'] = $this->config->get('shipping_qwqer_trade_cat');
        }

        if (isset($this->request->post['shipping_qwqer_telephone'])) {
            $data['shipping_qwqer_telephone'] = $this->request->post['shipping_qwqer_telephone'];
        } else {
            $data['shipping_qwqer_telephone'] = $this->config->get('shipping_qwqer_telephone');
        }

        if (isset($this->request->post['shipping_qwqer_address'])) {
            $data['shipping_qwqer_address'] = $this->request->post['shipping_qwqer_address'];
        } else {
            $data['shipping_qwqer_address'] = $this->config->get('shipping_qwqer_address');
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

        $this->load->model('extension/shipping/qwqer');

        foreach ( $this->shipping_qwqer->getOrderCategories() as $option){
            $data['shipping_qwqer_trade_cat_options'][] = $this->language->get('qwqer_opt_'.$option);
        }



		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['shipping_qwqer_tax_class_id'])) {
			$data['shipping_qwqer_tax_class_id'] = $this->request->post['shipping_qwqer_tax_class_id'];
		} else {
			$data['shipping_qwqer_tax_class_id'] = $this->config->get('shipping_qwqer_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        $data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['shipping_qwqer_geo_zone_id'])) {
			$data['shipping_qwqer_geo_zone_id'] = $this->request->post['shipping_qwqer_geo_zone_id'];
		} else {
			$data['shipping_qwqer_geo_zone_id'] = $this->config->get('shipping_qwqer_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['shipping_qwqer_status'])) {
			$data['shipping_qwqer_address_object'] = $this->request->post['shipping_qwqer_address_object'];
		} else {
			$data['shipping_qwqer_address_object'] = $this->config->get('shipping_qwqer_address_object');
		}
        //HOWTO: decode JSON.stringyfy on php side properly
        $arr = json_decode( html_entity_decode( stripslashes ($data['shipping_qwqer_address_object'] ) ), false );

        if (isset($this->request->post['shipping_qwqer_status'])) {
            $data['shipping_qwqer_status'] = $this->request->post['shipping_qwqer_status'];
        } else {
            $data['shipping_qwqer_status'] = $this->config->get('shipping_qwqer_status');
        }

		if (isset($this->request->post['shipping_qwqer_sort_order'])) {
			$data['shipping_qwqer_sort_order'] = $this->request->post['shipping_qwqer_sort_order'];
		} else {
			$data['shipping_qwqer_sort_order'] = $this->config->get('shipping_qwqer_sort_order');
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

		if (empty($this->request->post['shipping_qwqer_api'])) {
			$this->error['api'] = $this->language->get('error_api');
		}

		if (!preg_match('/^[0-9]{1,4}$/', $this->request->post['shipping_qwqer_trade_pt'])) {
			$this->error['trade_pt'] = $this->language->get('error_trade_pt');
		}

        //shipping_qwqer_trade_cat

        if (empty($this->request->post['shipping_qwqer_trade_cat'])) {
            $this->error['trade_cat'] = $this->language->get('error_trade_cat');
        }

        if (empty($this->request->post['shipping_qwqer_telephone'])) {
            $this->error['error_telephone1'] = $this->language->get('error_telephone');
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



}