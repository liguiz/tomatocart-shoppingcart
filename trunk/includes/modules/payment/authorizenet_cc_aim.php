<?php
/*
  $Id: authorizenet_cc_aim.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Payment_authorizenet_cc_aim extends osC_Payment {
    var $_title,
        $_code = 'authorizenet_cc_aim',
        $_status = false,
        $_sort_order,
        $_order_status,
        $_order_id;
        
    // class constructor
    function osC_Payment_authorizenet_cc_aim() {
      global $osC_Database, $osC_Language, $osC_ShoppingCart;

      $this->_title = $osC_Language->get('payment_authorizenet_cc_aim_title');
      $this->_method_title = $osC_Language->get('payment_authorizenet_cc_aim_method_title');
      $this->_sort_order = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_SORT_ORDER;
      $this->_status = ((MODULE_PAYMENT_AUTHORIZENET_CC_AIM_STATUS == '1') ? true : false);
      
      $this->_order_status = (int)MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ORDER_STATUS_ID > 0 ? (int)MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ORDER_STATUS_ID : (int)ORDERS_STATUS_PAID;

      if ($this->_status === true) {
        if ((int)MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ZONE > 0) {
          $check_flag = false;

          $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
          $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
          $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ZONE);
          $Qcheck->bindInt(':zone_country_id', $osC_ShoppingCart->getBillingAddress('country_id'));
          $Qcheck->execute();

          while ($Qcheck->next()) {
            if ($Qcheck->valueInt('zone_id') < 1) {
              $check_flag = true;
              break;
            } elseif ($Qcheck->valueInt('zone_id') == $osC_ShoppingCart->getBillingAddress('zone_id')) {
              $check_flag = true;
              break;
            }
          }

          if ($check_flag == false) {
            $this->_status = false;
          }
        }
      }
    }
    
    function selection() {
      return array('id' => $this->_code,
                   'module' => $this->_method_title);
    }
    
    function pre_confirmation_check() {
      return false;
    }
    
    function confirmation() {
      global $osC_Language, $osC_ShoppingCart;
      
      for ($i=1; $i<13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
      }
      
      $today = getdate();
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }
      
      $confirmation = array('fields' => array(array('title' =>  $osC_Language->get('payment_authorizenet_cc_aim_credit_card_owner'),
                                                    'field' => osc_draw_input_field('cc_owner', $osC_ShoppingCart->getBillingAddress('firstname') . ' ' . $osC_ShoppingCart->getBillingAddress('lastname'))),
                                              array('title' => $osC_Language->get('payment_authorizenet_cc_aim_credit_card_number'),
                                                    'field' => osc_draw_input_field('cc_number_nh-dns')),
                                              array('title' => $osC_Language->get('payment_authorizenet_cc_aim_credit_card_expires'),
                                                    'field' => osc_draw_pull_down_menu('cc_expires_month', $expires_month) . '&nbsp;' . osc_draw_pull_down_menu('cc_expires_year', $expires_year)),
                                              array('title' => $osC_Language->get('payment_authorizenet_cc_aim_credit_card_cvc'),
                                                    'field' => osc_draw_input_field('cc_cvc_nh-dns', '', 'size="5" maxlength="4"'))));
                                              
      return $confirmation;                                               
    }
    
    function process() {
      global $osC_Currencies, $osC_ShoppingCart, $messageStack, $osC_Customer, $osC_Tax;
      
      $params = array('x_login' => substr(MODULE_PAYMENT_AUTHORIZENET_CC_AIM_API_LOGIN_ID, 0, 20), 
                      'x_tran_key' => substr(MODULE_PAYMENT_AUTHORIZENET_CC_AIM_API_TRANSACTION_KEY, 0, 16), 
                      'x_version' => '3.1', 
                      'x_delim_data' => 'TRUE', 
                      'x_delim_char' => ',', 
                      'x_encap_char' => '"', 
                      'x_relay_response' => 'FALSE', 
                      'x_first_name' => substr($osC_ShoppingCart->getBillingAddress('firstname'), 0, 50), 
                      'x_last_name' => substr($osC_ShoppingCart->getBillingAddress('lastname'), 0, 50), 
                      'x_company' => substr($osC_ShoppingCart->getBillingAddress('company'), 0, 50), 
                      'x_address' => substr($osC_ShoppingCart->getBillingAddress('street_address'), 0, 60), 
                      'x_city' => substr($osC_ShoppingCart->getBillingAddress('city'), 0, 40), 
                      'x_state' => substr($osC_ShoppingCart->getBillingAddress('state'), 0, 40), 
                      'x_zip' => substr($osC_ShoppingCart->getBillingAddress('postcode'), 0, 20), 
                      'x_country' => substr($osC_ShoppingCart->getBillingAddress('country_iso_code_2'), 0, 60), 
                      'x_phone' => substr($osC_ShoppingCart->getBillingAddress('telephone_number'), 0, 25), 
                      'x_cust_id' => substr($osC_Customer->getID(), 0, 20), 
                      'x_customer_ip' => osc_get_ip_address(), 
                      'x_email' => substr($osC_Customer->getEmailAddress(), 0, 255), 
                      'x_description' => substr(STORE_NAME, 0, 255), 
                      'x_amount' => substr($osC_Currencies->formatRaw($osC_ShoppingCart->getTotal()), 0, 15), 
                      'x_currency_code' => substr($osC_Currencies->getCode(), 0, 3), 
                      'x_method' => 'CC', 
                      'x_type' => (MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_METHOD == 'Capture') ? 'AUTH_CAPTURE' : 'AUTH_ONLY', 
                      'x_card_num' => substr($_POST['cc_number_nh-dns'], 0, 22), 
                      'x_exp_date' => $_POST['cc_expires_month'] . $_POST['cc_expires_year'], 
                      'x_card_code' => substr($_POST['cc_cvc_nh-dns'], 0, 4));
      
      if ($osC_ShoppingCart->hasShippingAddress()) {
        $params['x_ship_to_first_name'] = substr($osC_ShoppingCart->getShippingAddress('firstname'), 0, 50);
        $params['x_ship_to_last_name'] = substr($osC_ShoppingCart->getShippingAddress('lastname'), 0, 50);
        $params['x_ship_to_company'] = substr($osC_ShoppingCart->getShippingAddress('company'), 0, 50);
        $params['x_ship_to_address'] = substr($osC_ShoppingCart->getShippingAddress('street_address'), 0, 60);
        $params['x_ship_to_city'] = substr($osC_ShoppingCart->getShippingAddress('city'), 0, 40);
        $params['x_ship_to_state'] = substr($osC_ShoppingCart->getShippingAddress('zone_code'), 0, 40);
        $params['x_ship_to_zip'] = substr($osC_ShoppingCart->getShippingAddress('postcode'), 0, 20);
        $params['x_ship_to_country'] = substr($osC_ShoppingCart->getShippingAddress('country_iso_code_2'), 0, 60);
      }
      
      if (MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_MODE == 'Test') {
        $params['x_test_request'] = 'TRUE';
      }
      
      $shipping_tax = ($osC_ShoppingCart->getShippingMethod('cost')) * ($osC_Tax->getTaxRate($osC_ShoppingCart->getShippingMethod('tax_class_id'), $osC_ShoppingCart->getTaxingAddress('country_id'), $osC_ShoppingCart->getTaxingAddress('zone_id')) / 100);
      $total_tax = $osC_ShoppingCart->getTax() - $shipping_tax;
      
      if ($total_tax > 0) {
        $params['x_tax'] = $this->format_raw($tax_value);
      }
      
      $params['x_freight'] = $osC_Currencies->formatRaw($osC_ShoppingCart->getShippingMethod('cost'));
      
      $post_string = '';
      foreach ($params as $key => $value) {
        $post_string .= $key . '=' . urlencode(trim($value)) . '&';
      }
      $post_string = substr($post_string, 0, -1);
      
      if ($osC_ShoppingCart->hasContents()) {
        foreach($osC_ShoppingCart->getProducts() as $key => $product) {
          $post_string .= '&x_line_item=' . urlencode($key+1) . '<|>' . urlencode(substr($product['name'], 0, 31)) . '<|>' . urlencode(substr($product['name'], 0, 255)) . '<|>' . urlencode($product['quantity']) . '<|>' . urlencode($osC_Currencies->formatRaw($product['final_price'])) . '<|>' . urlencode($product['tax_class_id'] > 0 ? 'YES' : 'NO');
        }
      }
      
      switch (MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_SERVER) {
        case 'Live':
          $gateway_url = 'https://secure.authorize.net/gateway/transact.dll';
          break;

        default:
          $gateway_url = 'https://test.authorize.net/gateway/transact.dll';
          break;
      }
      
      $transaction_response = $this->sendTransactionToGateway($gateway_url, $post_string);
      
      if (!empty($transaction_response)) {
        $regs = preg_split("/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/", $transaction_response);

        foreach ($regs as $key => $value) {
          $regs[$key] = substr($value, 1, -1); // remove double quotes
        }
      } else {
        $regs = array('-1', '-1', '-1');
      }
      
      $error = false;
      
      if ($regs[0] == '1') {
        if (MODULE_PAYMENT_AUTHORIZENET_CC_AIM_MD5_HASH != null) {
          if (strtoupper($regs[37]) != strtoupper(md5(MODULE_PAYMENT_AUTHORIZENET_CC_AIM_MD5_HASH . MODULE_PAYMENT_AUTHORIZENET_CC_AIM_API_LOGIN_ID . $regs[6] . $osC_Currencies->formatRaw($osC_ShoppingCart->getTotal())))) {
            $error = 'general';
          }
        }
      }else {
        switch ($regs[2]) {
          case '7':
            $error = 'invalid_expiration_date';
            break;

          case '8':
            $error = 'expired';
            break;

          case '6':
          case '17':
          case '28':
            $error = 'declined';
            break;

          case '78':
            $error = 'cvc';
            break;

          default:
            $error = 'general';
            break;
        }
      }
      
      if ($error != false) {
        $error = $this->get_error($error);
        
        if (!empty($error)) {
          $messageStack->add_session('checkout', stripslashes($error['title'] . ': ' . $error['error']), 'error');
        }
        
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'checkout&view=orderConfirmationForm', 'SSL'));
      }else {
        $orders_id = osC_Order::insert();
        
        osC_Order::process($orders_id, $this->_order_status);
      }
    }
    
    
    function process_button() {
      return false;
    }
    
    function get_error($error) {
      global $osC_Language;
      
      $error_message = $osC_Language->get('payment_authorizenet_cc_aim_error_general');

      switch ($error) {
        case 'invalid_expiration_date':
          $error_message = $osC_Language->get('payment_authorizenet_cc_aim_error_invalid_exp_date');
          break;

        case 'expired':
          $error_message = $osC_Language->get('payment_authorizenet_cc_aim_error_expired');
          break;
          
        case 'declined':
          $error_message = $osC_Language->get('payment_authorizenet_cc_aim_error_declined');
          break;
          
        case 'cvc':
          $error_message = $osC_Language->get('payment_authorizenet_cc_aim_error_cvc');
          break;

        default:
          $error_message = $osC_Language->get('payment_authorizenet_cc_aim_error_general');
          break;
      }

      $error = array('title' => $osC_Language->get('payment_authorizenet_cc_aim_error_title'),
                     'error' => $error_message);

      return $error;
    }
  }
?>