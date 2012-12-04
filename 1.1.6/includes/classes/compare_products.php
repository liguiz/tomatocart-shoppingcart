<?php
/*
  $Id: compare_products.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

	class toC_Compare_Products {
	  var $_contents = array();
	      
	  function toC_Compare_Products() {
	    if (!isset($_SESSION['toC_Compare_Products_data'])) {
	      $_SESSION['toC_Compare_Products_data'] = array();
	    }
	    
	    $this->_contents =& $_SESSION['toC_Compare_Products_data'];
	  }
	    
    function exists($products_id) {
      return isset($this->_contents[$products_id]);   
    }
    
    function hasContents() {
      return !empty($this->_contents);
    }
    
	  function reset() {
      $this->_contents = array();
    }
    
	  function addProduct($products_id) {
	    if (!$this->exists($products_id)) {
        $this->_contents[$products_id] = $products_id;
	    }
	  }
	  
	  function deleteProduct($products_id) {
	    if (isset($this->_contents[$products_id])) {
        unset($this->_contents[$products_id]);
      }
	  }
	  
	  function getProducts() {
	    $products = array_keys($this->_contents);
	    
	    return $products;
	  }
	  
	  function outputCompareProductsTable() {
      global $osC_Language, $osC_Image, $osC_Weight, $osC_Currencies;
      
      $content = '';
      
      $products_images = '';
      $products_titles = '';
      $products_price = '';
      $products_weight = '';
      $products_sku = '';
      $products_manufacturers = '';
      $products_desciptions = '';
	    $products_attributes = '';
	    
      if ($this->hasContents()) {
        foreach ($this->getProducts() as $products_id) {
          $osC_Product = new osC_Product($products_id);
          
          $image = $osC_Product->getImages();
          $product_title = $osC_Product->getTitle();
          $product_price = $osC_Product->getPriceFormated(true);
          $product_weight = $osC_Product->getWeight();
          $product_sku = $osC_Product->getSKU();
          
         //if the product have any variants, it means that the $products_id should be a product string such as 1#1:1;2:2
          if ($osC_Product->hasVariants()) {
            $str_variants = '';
             
            $products_variants = $osC_Product->getVariants();
            $variants = array();
            if (preg_match('/^[0-9]+(#?([0-9]+:?[0-9]+)+(;?([0-9]+:?[0-9]+)+)*)+$/', $products_id)) {
              $products_variant = $products_variants[$products_id];
              
              $variants = osc_parse_variants_from_id_string($products_id);
            }else {
              $products_variant = $osC_Product->getDefaultVariant();
            }
            
            //if the product have any variants, get the group_name:value_name string
            $str_variants .= ' -- ';
            foreach($products_variant['groups_name'] as $groups_name => $value_name) {
              $str_variants .= '<strong>' . $groups_name . ': ' . $value_name . '</strong>;';
            }
            
            //clean the last ';'
            if (($pos = strrpos($str_variants, ';')) !== false) {
              $str_variants = substr($str_variants, 0, -1);
            }
            
            $product_title .= $str_variants;
            $product_price = $osC_Currencies->displayPrice($osC_Product->getPrice($variants), $osC_Product->getTaxClassID());
            $product_weight = $products_variant['weight'];
            $product_sku = $products_variant['sku'];
          }
          
          $products_images .= '<td width="120" valign="top" align="center">' . osc_link_object(osc_href_link(FILENAME_PRODUCTS, $products_id), $osC_Image->show($image[0]['image'], $osC_Product->getTitle())) . '<br /><br />' . osc_link_object(osc_href_link(FILENAME_PRODUCTS, $products_id . '&action=cart_add'), osc_draw_image_button('button_in_cart.gif', $osC_Language->get('button_add_to_cart'))) . '</td>';
          $products_titles .= '<td valign="top" align="center">' . $product_title . '</td>';
          $products_price .= '<td valign="top" align="center">' . $product_price . '</td>';
          $products_weight .= '<td valign="top" align="center">' . $osC_Weight->display($product_weight, $osC_Product->getWeightClass()) . '</td>';
          $products_sku .= '<td valign="top" align="center">' . $product_sku . '</td>';
          $products_manufacturers .= '<td valign="top" align="center">' . $osC_Product->getManufacturer() . '</td>';
          $products_desciptions .= '<td valign="top" align="center">' . $osC_Product->getDescription() . '</td>';
          
          if ( $osC_Product->hasAttributes() ) {
            foreach ( $osC_Product->getAttributes() as $attribute) {
              $products_attributes[$attribute['name']][$products_id] = $attribute['value'];
            }
          }
        }
        
        $content .= '<table id="compareProducts" cellspacing="0" cellpadding="2" border="0">';
        $content .= '<tr class="odd"><td width="120">&nbsp;</td>' . $products_images . '</tr>';
        $content .= '<tr class="even"><td valign="top" align="left" class="label">' . $osC_Language->get('field_products_name') . '</td>' . $products_titles . '</tr>';
        $content .= '<tr class="odd"><td valign="top" align="left" class="label">' . $osC_Language->get('field_products_price') . '</td>' . $products_price . '</tr>';
        $content .= '<tr class="even"><td valign="top" align="left" class="label">' . $osC_Language->get('field_products_weight') . '</td>' . $products_weight . '</tr>';
        $content .= '<tr class="odd"><td valign="top" align="left" class="label">' . $osC_Language->get('field_products_sku') . '</td>' . $products_sku . '</tr>';
        $content .= '<tr class="even"><td valign="top" align="left" class="label">' . $osC_Language->get('field_products_manufacturer') . '</td>' . $products_manufacturers . '</tr>';
        
        if(!empty($products_attributes)) {
	        $rows = 0;
	        foreach($products_attributes as $name => $attribute) {
	          $content .= '<tr class="' . ((($rows/2) == floor($rows/2)) ? 'odd' : 'even') . '">';
	          $content .= '<td valign="top" align="left" class="label">' . $name . ':</td>';
	                
	          foreach ($this->getProducts() as $products_id) {
	            if (isset($attribute[$products_id])) {
	              $content .= '<td align = "center">' . $attribute[$products_id] . '</td>';
	            } else {
	              $content .= '<td align = "center"> -- </td>';
	            }
	          }
	
	          $content .= '</tr>';
	          
	          $rows++;
	        }
        }

        $content .= '<tr class="' . ((($rows/2) == floor($rows/2)) ? 'odd' : 'even') . '"><td valign="top" align="left" class="label">' . $osC_Language->get('field_products_description') . '</td>' . $products_desciptions . '</tr>';
        $content .= '</table></div>';
      }
      
      return $content;
	  }
	}
?>