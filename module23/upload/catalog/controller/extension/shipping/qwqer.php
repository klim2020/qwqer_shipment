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
            $address =  $this->request->post['qwqer_address'];
            $type  =    $this->request->post['qwqer_type'];
            //$name, $address, $type, $phone
            $ret =      $this->shipping_qwqer->generateSingleOrderObject($name, $address, $type, $phone);
            //$this->shipping_qwqer->

            if ($ret){
                $json = $ret;
                $this->session->data['qwqer'] = $ret;
            }else{
                $json = ['error'=>'checking error'];
            }

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}