<?php
use library\qweqr\QwqerApi;
class ControllerExtensionShippingQwqer extends Controller {

    public function __construct($registry)
    {
        parent::__construct($registry);

        require_once DIR_SYSTEM."library/qwqer/QwqerApi.php";
        new QwqerApi($registry);
    }

    public function validate(){
        return isset($this->request->get['qwqer_token']) && $this->request->get['qwqer_token'] == $this->session->data['qwqer_token'];
    }

    public function save_parcel_terminal(){
        $json = [];
        if (isset($this->request->post['parcel_terminal'])){
            $this->session->data["autoCompleteHidden"] = $this->request->post['parcel_terminal'];
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function get_terminals(){
        if (isset($this->request->get['qwqer_address'])){
            $address = $this->request->get['qwqer_address'];
        }

        if (!$this->validate()){
            $json = ['data'=>'key invalid'];
        }else{
            $terminals = $this->shipping_qwqer->getParcelTerminals();
            $json = $terminals['data']['omniva'];
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function get_adress(){
        if (isset($this->request->post['qwqer_address'])){
            $qwqer_address = $this->request->post['qwqer_address'];
        }else{
            $qwqer_address = '';
        }

        if (!$this->validate() || $this->request->server['REQUEST_METHOD'] != 'POST'  ){
            $json = ['error'=>'key invalid'];
        }else{
            $address = $this->shipping_qwqer->placeAutocomplete($qwqer_address);
            if (isset($address['errors'])){
                $json = ['error'=>'key invalid'];
            }else{
                $json = $address['data'];
            }

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function validate_data(){
        if (!$this->validate() || $this->request->server['REQUEST_METHOD'] != 'POST'  ){
            $json = ['error'=>'key invalid'];
        }else{
            $this->session->data['qwqer'] = array();
            $name =     $this->request->post['qwqer_name'];
            $phone =    $this->request->post['qwqer_phone'];
            $address =   json_decode( html_entity_decode( stripslashes ($this->request->post['qwqer_address'] ) ),true );
            $type  =    $this->request->post['qwqer_type'];
            //$name, $address, $type, $phone
            $this->load->model('extension/shipping/qwqer');
            $ret = $this->model_extension_shipping_qwqer->generateOrderObject($name,$phone,$address,$type );
            $price = array(
                'data'=>array('client_price' => $this->currency->convert(300 / 100, 'EUR', $this->config->get('config_currency')) * 100)
            );
            if (isset($ret['real_type']) && $ret['real_type']=="ExpressDelivery"){
                $price = $this->shipping_qwqer->calculatePrice($ret);
            }


            //$this->shipping_qwqer->
            if (isset($address)){
                //unset($ret['destinations'][0]['address']);
                //$ret['destinations'][0]['address'] = $address;
            }

            if ($ret){
                $json['delivery'] = $ret;
                $json['price'] = $price['data'];
                $this->session->data['qwqer'] = $ret;
                $this->session->data['qwqer_price'] = $price;
            }else{
                $json = ['error'=>'checking error'];
            }

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}