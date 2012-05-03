<?php
/*
  $Id: fail.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Checkout_Fail extends osC_Template {

/* Private variables */

    var $_module = 'fail',
        $_group = 'checkout',
        $_page_title,
        $_page_contents = 'checkout_fail.php';

/* Class constructor */

    function osC_Checkout_Fail() {
      global $osC_Services, $osC_Language, $osC_Customer, $osC_NavigationHistory, $breadcrumb;

      $this->_page_title = $osC_Language->get('fail_heading');

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }
      
      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_checkout_fail'), osc_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

    }
  }
?>
