<?php
/**
 * TomatoCart Open Source Shopping Cart Solution
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v3 (2007)
 * as published by the Free Software Foundation.
 *
 * @package      TomatoCart
 * @author       TomatoCart Dev Team
 * @copyright    Copyright (c) 2009 - 2012, TomatoCart. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html
 * @link         http://tomatocart.com
 * @since        Version 1.1.8
 * @filesource
*/

/**
 * Get manufacturers filters used in the product listing page
 *
 * @param $categories_ids [array] - the categories ids
 * @return mixed
 */
function get_manufactuers_filters($categories_ids) {
  global $osC_Database, $osC_Language;

  $Qfilterlist = $osC_Database->query('select distinct m.manufacturers_id as id, m.manufacturers_name as name from :table_products p inner join :table_manufacturers m on p.manufacturers_id  = m.manufacturers_id where p.products_id in (select products_id from :table_products_to_categories where categories_id in (:categories_ids)) and p.products_status = 1 order by m.manufacturers_name');
  $Qfilterlist->bindTable(':table_products', TABLE_PRODUCTS);
  $Qfilterlist->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
  $Qfilterlist->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
  $Qfilterlist->bindRaw(':categories_ids', implode(',', $categories_ids));
  $Qfilterlist->execute();

  if ($Qfilterlist->numberOfRows() > 1) {
    $manufacturers = array(array('id' => '', 'text' => $osC_Language->get('filter_all_manufacturers')));

    while ($Qfilterlist->next()) {
      $manufacturers[] = array('id' => $Qfilterlist->valueInt('id'), 'text' => $Qfilterlist->value('name'));
    }

    return $manufacturers;
  }

  return NULL;
}

/**
 * Get Categories filters for the product listing page
 *
 * @param $manufacturers_id [int] - the manufacturers id
 * @return mixed
 */
function get_categories_filters($manufacturers_id) {
  global $osC_Database, $osC_Language;

  $Qfilterlist = $osC_Database->query('select distinct c.categories_id as id, cd.categories_name as name from :table_categories c inner join :table_categories_description cd on (c.categories_id = cd.categories_id and cd.language_id = :language_id) where c.categories_id in (select p2c.categories_id from :table_products p inner join :table_products_to_categories p2c on p.products_id = p2c.products_id where p.manufacturers_id = :manufacturers_id) order by cd.categories_name');
  $Qfilterlist->bindTable(':table_products', TABLE_PRODUCTS);
  $Qfilterlist->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
  $Qfilterlist->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
  $Qfilterlist->bindTable(':table_categories', TABLE_CATEGORIES);
  $Qfilterlist->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
  $Qfilterlist->bindInt(':language_id', $osC_Language->getID());
  $Qfilterlist->bindInt(':manufacturers_id', $manufacturers_id);
  $Qfilterlist->execute();

  if ($Qfilterlist->numberOfRows() > 1) {
    $categories = array(array('id' => '', 'text' => $osC_Language->get('filter_all_categories')));

    while ($Qfilterlist->next()) {
      $categories[] = array('id' => $Qfilterlist->valueInt('id'), 'text' => $Qfilterlist->value('name'));
    }

    return $categories;
  }

  return NULL;
}

/**
 * Return an array of products listing sort parameters
 *
 * @access public
 * @return array
 */
function get_products_listing_view_type() {
  //check view type
  $view_type = 'list';
  $view_types = array('list', 'grid2', 'grid3');
  
  //user change the view type in the product listing page
  if (isset($_GET['view'])) {
    $view_type = $_GET['view'];
    
  //hold the view type in the session
  }else if (isset($_SESSION['view_type'])) {
    $view_type = $_SESSION['view_type'];
  }
  
  //check whether the view type is valid. Otherwise use the default list type
  if (!in_array($view_type, $view_types)) {
    $view_type = 'list';
  }
  
  //set view type to session
  $_SESSION['view_type'] = $view_type;
  
  return $view_type;
}

/**
 * Generate the filters from for the product listing page
 *
 * @access public
 * 
 * @param $action [string] - the action url of filters form
 * @param $filters [array] - the displayed filters
 * @param $sort [bool] - true to display the sorts dropdown menu
 * @param $module [string] - the current page module
 * @return mixed
 */
function get_filters_form($action = null, $filters = array(), $sort = true, $module = null) {
  global $osC_Language;
  
  if (count($filters) < 1 && $sort !== true) {
    return null;
  }
  
  //products listing sort parameters
  $sorts = array(array('id' => 'name', 'text' => $osC_Language->get('product') . ' (ASC)'),
                 array('id' => 'name|d', 'text' => $osC_Language->get('product') . ' (DESC)'),
                 array('id' => 'price', 'text' => $osC_Language->get('listing_price_heading') . ' (ASC)'),
                 array('id' => 'price|d', 'text' => $osC_Language->get('listing_price_heading') . ' (DESC)'));
  
  $filters_form = '<form name="filter" action="' . $action . '" method="get">';
  
  //output the categories or manufacturers filters dropdown menu
  if (count($filters) > 0) {
    $filters_form .= osc_draw_pull_down_menu('filter', $filters, (isset($_GET['filter']) ? $_GET['filter'] : null), 'onchange="this.form.submit()"');
  }
 
  
  //link products attributes filter and the category/manufacturer filter
  if (defined('PRODUCT_LINK_FILTER') && (PRODUCT_LINK_FILTER == '1')) {
    if (isset($_GET['products_attributes']) && is_array($_GET['products_attributes'])) {
      foreach($_GET['products_attributes'] as $att_value_id => $att_value) {
        $filters_form .= osc_draw_hidden_field('products_attributes[' . $att_value_id . ']', $att_value);
      }
    }
  }
  
  //link the search fileds
  $keys = array('keywords', 'pfrom', 'pto', 'datefrom_days', 'datefrom_months', 'datefrom_years', 'dateto_days', 'dateto_months', 'dateto_years');
  foreach ($keys as $key) {
    if (isset($_GET[$key]) && !empty($_GET[$key])) {
      $filters_form .= osc_draw_hidden_field($key, $_GET[$key]);
    }
  }
  
  if ($module !== null) {
    $filters_form .= osc_draw_hidden_field($module);
  }
  
  //output the sorts
  $filters_form .= osc_draw_pull_down_menu('sort', $sorts, (isset($_GET['sort']) ? $_GET['sort'] : null), 'onchange="this.form.submit()"');
  
  $filters_form .= '</form>';
  
  return $filters_form;
}

/**
 * Compare the variants products by product price
 *
 * @access public
 *
 * @param $a [array] - product info array
 * @param $b [array] - product info array
 * @return mixed
 */
function compare_variants_price($a, $b) {
  global $osC_Products, $osC_Database;
   
  $sort_direction = (($osC_Products->_sort_by_direction == '-') ? 'desc' : '');
  
  $product_a_price = $a['final_price'];
  $product_b_price = $b['final_price'];
  
  //check product variants, use the default variant price
  $Qproduct_a_variants = $osC_Database->query('select products_price from :table_products_variants where products_id = :products_id and is_default = 1 limit 1');
  $Qproduct_a_variants->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
  $Qproduct_a_variants->bindInt(':products_id', $a['products_id']);
  $Qproduct_a_variants->execute();
  
  if ($Qproduct_a_variants->numberOfRows() > 0) {
    $product_a_variant = $Qproduct_a_variants->toArray();
    $product_a_price = $product_a_variant['products_price'];
  }
  $Qproduct_a_variants->freeResult();
  
  $Qproduct_b_variants = $osC_Database->query('select products_price from :table_products_variants where products_id = :products_id and is_default = 1 limit 1');
  $Qproduct_b_variants->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
  $Qproduct_b_variants->bindInt(':products_id', $b['products_id']);
  $Qproduct_b_variants->execute();
  
  if ($Qproduct_b_variants->numberOfRows() > 0) {
    $product_b_variant = $Qproduct_b_variants->toArray();
    $product_b_price = $Qproduct_b_variants['products_price'];
  }
  $Qproduct_b_variants->freeResult();
  
  //compare the product price
  
  //same price, order by product name
  if ($product_a_price == $product_b_price) {
    return 0;
  }
  
  //desc sort direction
  if ($sort_direction === 'desc') {
    return ($product_a_price < $product_b_price) ? 1 : -1;
  //asc sort direction
  }else {
    return ($product_a_price < $product_b_price) ? -1 : 1;
  }
}