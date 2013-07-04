<?php
/*
  $Id: specials.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2005 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Products_Specials extends osC_Template {

/* Private variables */

    var $_module = 'specials',
        $_group = 'products',
        $_page_title,
        $_page_contents = 'specials.php',
        $_page_image = 'table_background_specials.gif';

/* Class constructor */

    function osC_Products_Specials() {
      global $osC_Services, $osC_Language, $breadcrumb, $frm_filters, $view_type, $Qlisting;

      $this->_page_title = $osC_Language->get('specials_heading');
      
      //load the helper for product listing page
      include('includes/functions/product_listing.php');
      
      //get filters form
      $frm_filters = get_filters_form(osc_href_link(FILENAME_PRODUCTS), null, true, $this->_module);
      
      //get the current view type of the product listing
      $view_type = get_listing_view_type();

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_specials'), osc_href_link(FILENAME_PRODUCTS, $this->_module));
      }
      
      //get the special products
      if (isset($_GET['sort']) && !empty($_GET['sort'])) {
        switch ($_GET['sort']) {
          case 'name':
          case 'name|d':
            $sort_by = 'products_name';
            break;
          case 'price':
          case 'price|d':
            $sort_by = 'specials_new_products_price';
            break;
          default:
            $sort_by = 'specials_new_products_price';
        }
        
        if (strpos($_GET['sort'], '|d') !== false) {
          $Qlisting = osC_Specials::getListing($sort_by, 'desc');
        } else {
          $Qlisting = osC_Specials::getListing($sort_by);
        }
      }else {
        $Qlisting = osC_Specials::getListing();
      }
    }
  }
?>
