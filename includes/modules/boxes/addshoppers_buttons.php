<?php

require_once(DIR_FS_CATALOG . DIR_FS_ADMIN . 'includes/jsons/modules_addshoppers.php');

class osC_Boxes_addshoppers_buttons extends osC_Modules {

    var $_title,
          $_code = 'addshoppers_buttons',
          $_author_name = 'TomatoCart',
          $_author_www = 'http://www.tomatocart.com',
          $_group = 'boxes',
          $_settings;

    function osC_Boxes_addshoppers_buttons() {
        $this->_title = 'Addshoppers';
        $this->_content = 'lalalal';
    }

    function initialize() {
        global $osC_Template;

        $osC_Template->addStylesheet("/admin/includes/extmodules/modules_addshoppers/static/css/shop.css");
        $osC_Template->addStyleDeclaration('div#addshoppers_container {width: 100%;height: auto;margin:10px 0;}');

        $this->_settings = toC_Json_Modules_addshoppers::getSettings();
        $this->_title_link = osc_href_link(FILENAME_DEFAULT, 'index');
    }

    function install() {
        global $osC_Database;

        parent::install();

        $osC_Database->simpleQuery("CREATE TABLE `toc_addshoppers` (`id` int(11) NOT NULL AUTO_INCREMENT,`shopid` varchar(255) DEFAULT NULL,`api_key` varchar(255) DEFAULT NULL,`user_login` varchar(255) DEFAULT NULL,`user_password` varchar(255) DEFAULT NULL,`use_default_buttons` int(11) DEFAULT '1',`use_open_graph_buttons` int(11) DEFAULT '1',PRIMARY KEY (`id`));");
        $osC_Database->simpleQuery("INSERT INTO `tomato`.`toc_addshoppers` (`shopid`, `api_key`, `user_login`, `user_password`, `use_default_buttons`, `use_open_graph_buttons`) VALUES ( '', '', '', '', 0,0);");
    }

    function getKeys() {
        if (!isset($this->_keys)) {
            $this->_keys = array();
        }

        return $this->_keys;
    }

    public function getButtonsCode() {
        $settings = toC_Json_Modules_addshoppers::getSettings();
        $data = array(
            'shopid' => $settings["shopid"],
            'key' => $settings["api_key"]
        );

        return toC_Json_Modules_addshoppers::sendCurlRequest('/account/social-analytics/tracking-codes', $data);
    }

    public function isDefaultAccount() {
        return $this->_settings["shopid"] == null ? true : false;
    }

    public function getSettings() {
        return $this->_settings;
    }

    public function getDefaultShopid() {
        return toC_Json_Modules_addshoppers::$defaultShopId;
    }

    public function getTotal() {
        global $osC_Database, $osC_Customer;
        
        $Qorder_id = $osC_Database->query('select orders_id from :table_orders where customers_id = :customers_id order by date_purchased desc limit 1');
        $Qorder_id->bindTable(':table_orders', TABLE_ORDERS);
        $Qorder_id->bindInt(':customers_id', $osC_Customer->getID());
        $Qorder_id->execute();

        $order_id = $Qorder_id->valueInt('orders_id');

        $Qorder_id->freeResult();

        $Qorder_total = $osC_Database->query('select sum(value) as total from :table_orders_total where class != :class and orders_id = :orders_id');
        $Qorder_total->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
        $Qorder_total->bindValue(':class', 'total');
        $Qorder_total->bindInt(':orders_id', $order_id);
        $Qorder_total->execute();

        $order_total = $Qorder_total->toArray();
        $total = $order_total['total'];
        $Qorder_total->freeResult();
        
        return array($total,$order_id);
    }

}
