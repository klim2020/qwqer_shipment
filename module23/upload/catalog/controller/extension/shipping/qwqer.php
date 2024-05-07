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
        $key = isset($this->request->get['qwqer_token']) && $this->request->get['qwqer_token'] == $this->session->data['qwqer_token'];
        if ($key){
            return $key;
        }else{
            $this->response->addHeader("HTTP/1.1 400 Bad Request");
            return false;
        }

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
        if (isset($address['message']) && $address['message'] == 'Place not found'){
            $json = [];
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
            $key = mb_strtolower($ret['real_type']);
            if ($this->shipping_qwqer->getSession($key)){

            }elseif (isset($ret['real_type']) && $ret['real_type']=="ExpressDelivery"){
                $price  = $this->shipping_qwqer->calculatePrice($ret);
                $key    = mb_strtolower($ret['real_type']);
                $this->session->data['qwqer_price'][$key] = $price['data']['client_price'];
            }

            //$this->shipping_qwqer->
            if (isset($address)){
                //unset($ret['destinations'][0]['address']);
                //$ret['destinations'][0]['address'] = $address;
            }

            if ($ret){
                $json['delivery']             = $ret;
                $json['price']                = $price['data'];
                $this->session->data['qwqer'] = $ret;

                //create session array
                $arr = $this->shipping_qwqer->getSession($key);
                $arr['delivery_object'] = $ret;
                $arr['price_object']    = $price['data'];
                $this->shipping_qwqer->storeSession($key,$arr);
                //$this->session->data['qwqer_price'] = $price;

            }else{
                $json = ['error'=>'checking error'];
            }

            $chk_t = $this->config->get('qwqer_checkout_type');
            if ($chk_t == 1 && $ret['real_type'] == "ExpressDelivery"){
                $json['forcereload'] = true;
            }else{
                $json['forcereload'] = false;
            }

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function remove_session(){
        if (!$this->validate() || $this->request->server['REQUEST_METHOD'] != 'POST'  ){
            $json = ['error'=>'key invalid'];

        }else{
            $selected =     $this->request->post['selected'];
            if ($selected)
            {
                $price =  $this->shipping_qwqer->generateDeliveryCost($selected);
                $selected = str_replace('qwqer.','',$selected);
                if ($this->request->post['selected'] && $price){
                    $this->shipping_qwqer->clearSession($selected);
                    $json = ['message'=>'success'];
                    if ($this->config->get('qwqer_checkout_type') == 0){
                        $json['reboot']=false;
                    }else{
                        $json['reboot']=true;
                    }
            }
            }else{
                $json = ['message'=>'fail'];
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function get_working_hours(){
        if (!$this->validate() || $this->request->server['REQUEST_METHOD'] != 'POST'  ){
            $json['error']='key invalid';
            $json['message'] = 'key invalid';
        }else{
            $this->load->model('extension/shipping/qwqer');
            $working_time = $this->shipping_qwqer->getInfo();
            if ($working_time){
                usort($working_time['working_hours'], function ($a,$b){
                    return date('N', strtotime($a['day_of_week'])) > date('N', strtotime($b['day_of_week']));
                });
            }
            $json['message'] = 'success';
            $json['data'] = $working_time;

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}