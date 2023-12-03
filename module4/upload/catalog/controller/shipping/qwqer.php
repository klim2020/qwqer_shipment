<?php
namespace Opencart\Catalog\Controller\Extension\Qwqer\Shipping;
use library\qweqr\QwqerApi;

/**
 * Class Cheque
 *
 * @package
 */
class Qwqer extends \Opencart\System\Engine\Controller {

    public function __construct($registry)
    {
        parent::__construct($registry);
        require_once DIR_EXTENSION."qwqer/system/library/qwqer/QwqerApi.php";
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


    public function  onEditShippingField(string &$route, array &$data, mixed &$output){
        $f = "d";
    }

}