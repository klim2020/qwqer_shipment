<?php
class ControllerExtensionShippingQwqer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/qwqer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

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

		if (isset($this->request->post['shipping_qwqer_weight_class_id'])) {
			$data['shipping_qwqer_weight_class_id'] = $this->request->post['shipping_qwqer_weight_class_id'];
		} else {
			$data['shipping_qwqer_weight_class_id'] = $this->config->get('shipping_qwqer_weight_class_id');
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

		if (isset($this->request->post['shipping_qwqer_geo_zone_id'])) {
			$data['shipping_qwqer_geo_zone_id'] = $this->request->post['shipping_qwqer_geo_zone_id'];
		} else {
			$data['shipping_qwqer_geo_zone_id'] = $this->config->get('shipping_qwqer_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

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

		if (!preg_match('/^[0-9]{4}$/', $this->request->post['shipping_qwqer_trade_pt'])) {
			$this->error['trade_pt'] = $this->language->get('error_trade_pt');
		}

		return !$this->error;
	}
}