<?php
namespace Opencart\Admin\Model\Extension\Qwqer\Shipping;

use library\qweqr\QwqerApi;

/**
 *
 * @property QwqerApi $shipping_qwqer
 */
class Qwqer extends \Opencart\System\Engine\Model {




    public function __construct($registry)
    {
        parent::__construct($registry);
        require_once DIR_EXTENSION."qwqer/system/library/qwqer/QwqerApi.php";
        new QwqerApi($registry);
    }

    /**
     * @return string[]
     */
    public function getOptions()
    {
        /*
         * @var library\qweqr\QwqerApi $this->shipping_qwqer
         * */
        return $this->shipping_qwqer->order_categories;
    }

    public function install(){
        $this->uninstall();
        $this->db->query("
			CREATE TABLE `" . DB_PREFIX . "qwqer_data` (
				`qwqer_id` INT(11) NOT NULL AUTO_INCREMENT,
				`key_hash` varchar(255) NULL,
				`order_id` varchar(255) NOT NULL,
				`data`  TEXT NULL,
				`response` TEXT NULL,
				PRIMARY KEY (`qwqer_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
        $this->registerEvents();
    }

    public function uninstall(){
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "qwqer_data`");
        $this->unregisterEvents();
    }

    protected $events = [
        //edit shipping field in frontend
        'qwqer_edit_shipping_field' => [
            'code' => 'qwqer_edit_shipping_field',
            //catalog/model/checkout/order/deleteOrder/before
            'trigger' => 'catalog/controller/checkout/shipping_method.save/after',
            'action' => 'extension/qwqer/shipping/qwqer.onEditShippingField',
            'description' => 'edit shipping field in frontend',
            'sort_order' => 4,
            'status' => true
        ]
        //
    ];
    private function registerEvents()
    {
        $this->load->model('setting/event');
        foreach ($this->events as $event){
            $this->model_setting_event->addEvent($event);
        }
    }

    private function unregisterEvents()
    {
        $this->load->model('setting/event');
        foreach ($this->events as $event){
            $this->model_setting_event->deleteEventByCode($event['code']);
        }
    }

}