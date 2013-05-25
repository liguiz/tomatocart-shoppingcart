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

  $Qspecials = osC_Specials::getListing();
?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
	<h6><?php echo $osC_Template->getPageTitle(); ?></h6>
	
    <?php if ($Qspecials->numberOfRows() > 0) { ?>
    <ul class="products-list grid clearfix">
    
        <?php
            while ($Qspecials->next()) {
                $osC_Product = new osC_Product($Qspecials->value('products_id'));
        ?>
        <li class="clearfix">
            <div class="left">
                <?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qspecials->value('products_id')), $osC_Image->show($Qspecials->value('image'), $Qspecials->value('products_name'))); ?> 
                <h3><?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qspecials->value('products_id')), $Qspecials->value('products_name')); ?></h3>
                <p class="description"><?php echo strip_tags($osC_Product->getDescription()); ?></p>
            </div>
            <div class="right">
                <span class="price"><?php echo $osC_Product->getPriceFormated(true); ?></span>
                <span class="buttons hidden-phone">
                    <a class="btn btn-small btn-info" href="<?php echo osc_href_link(FILENAME_PRODUCTS, $Qspecials->value('products_id') . '&action=cart_add'); ?>">
                    	<i class="icon-shopping-cart icon-white "></i> 
                    	<?php echo $osC_Language->get('button_buy_now'); ?>
                    </a>
                    <br />
                    <?php echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $Qspecials->value('products_id') . '&action=wishlist_add'), $osC_Language->get('add_to_wishlist'), 'class="wishlist"'); ?>
                    <?php
                      if ($osC_Template->isInstalled('compare_products', 'boxes')) {
                          echo  '<br />' . osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params() . '&cid=' . $Qspecials->value('products_id') . '&' . '&action=compare_products_add'), $osC_Language->get('add_to_compare'), 'class="compare"');
                      }
                    ?>
                </span>
            </div>
		</li>
      <?php 
        }
      ?>
      
    </ul>
  
    <div class="listingPageLinks">
      <span style="float: right;"><?php echo $Qspecials->getBatchPageLinks('page', 'specials'); ?></span>
    
      <?php echo $Qspecials->getBatchTotalPages($osC_Language->get('result_set_number_of_products')); ?>
    </div>
	<?php } ?>
</div>