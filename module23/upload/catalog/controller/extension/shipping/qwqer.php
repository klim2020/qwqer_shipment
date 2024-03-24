<?php
use library\qweqr\QwqerApi;
class ControllerExtensionShippingQwqer extends Controller {

    public function __construct($registry)
    {
        parent::__construct($registry);

        require_once DIR_SYSTEM."library/qwqer/QwqerApi.php";
        new QwqerApi($registry);
    }

    public function save_parcel_terminal(){
        $json = [];
        if (isset($this->request->post['parcel_terminal'])){
            $this->session->data["autoCompleteHidden"] = $this->request->post['parcel_terminal'];
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}