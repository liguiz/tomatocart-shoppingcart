<?php
/*
  $Id: checkout.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/address_book.php');

  class osC_Checkout_Checkout extends osC_Template {

/* Private variables */

    var $_module = 'checkout',
        $_group = 'checkout',
        $_page_title,
        $_page_contents = 'checkout.php',
        $_page_image = 'table_background_delivery.gif';

/* Class constructor */

    function osC_Checkout_Checkout() {
      global $osC_ShoppingCart, $osC_Customer, $osC_NavigationHistory, $messageStack;
      
      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }
      
      if ($osC_ShoppingCart->hasContents() === false) {
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
      }else {
        //check the products stock in the cart
        if (STOCK_ALLOW_CHECKOUT == '-1') {
          foreach($osC_ShoppingCart->getProducts() as $product) {
            if ($osC_ShoppingCart->isInStock($product['id']) === false) {
              osc_redirect(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'));
            }
          }
        }
      }
      
      if ($osC_ShoppingCart->hasBillingMethod()) {
          // load selected payment module
        include('includes/classes/payment.php');
        $osC_Payment = new osC_Payment($osC_ShoppingCart->getBillingMethod('id'));
        
        $payment_error = $osC_Payment->get_error();
        
        if (is_array($payment_error) && !empty($payment_error)) {
          $messageStack->add('payment_error_msg', '<strong>' . $payment_error['title'] . '</strong> ' . $payment_error['error']);
        }
      }
      
      $this->addJavascriptFilename('includes/javascript/checkout.js');
    } 
  }
?>
