<?php


use library\qweqr\QwqerApi;

/**
 *
 * @property QwqerApi $shipping_qwqer
 */
class ModelExtensionShippingQwqer extends Model {




    public function __construct($registry)
    {
        parent::__construct($registry);
        require_once DIR_SYSTEM."library/qwqer/QwqerApi.php";
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
    }

    public function uninstall(){
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "qwqer_data`");
    }

}