<?php

/**
 * Ten plik sprawia, że pojawia się odpowiednia pozycja w menu
 */

class osC_Access_Modules_addshoppers extends osC_Access {

    var $_module = 'modules_addshoppers',
            $_group = 'modules',
            $_icon = 'favicon.png',
            $_title,
            $_sort_order = 90;

    function osC_Access_Modules_addshoppers() {
        global $osC_Template;
//        $osC_Template->addStylesheet('/admin/includes/extmodules/modules_addshoppers/static/css/backend.css');
        
        $this->_title = 'AddShoppers';
    }

}