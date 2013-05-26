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
    $sort_array = get_products_listing_sort();
    $view_type = get_products_listing_view_type();

    if ($Qlisting->numberOfRows() > 0) {
?>
	<div class="products-listing-action">
		<form id="products-filter" class="form-inline" action="<?php echo osc_href_link($_SERVER['SCRIPT_NAME']); ?>" method="get">
			<?php 
			    //drow hidden session id
			    echo osc_draw_hidden_session_id_field();
			    
                //whether the products attributes filter and the category/manufacturer filter is linked
                if (defined('PRODUCT_LINK_FILTER') && (PRODUCT_LINK_FILTER == '1')) {
                    if (isset($_GET['products_attributes']) && is_array($_GET['products_attributes'])) {
                        foreach($_GET['products_attributes'] as $att_value_id => $att_value) {
                            echo osc_draw_hidden_field('products_attributes[' . $att_value_id . ']', $att_value);
                        }
                    }
                }
                
                $keys = array('keywords', 'pfrom', 'pto', 'datefrom_days', 'datefrom_months', 'datefrom_years', 'dateto_days', 'dateto_months', 'dateto_years');
                foreach ($keys as $key) {
                    if (isset($_GET[$key]) && !empty($_GET[$key])) {
                        echo osc_draw_hidden_field($key, $_GET[$key]);
                    }
                }
                
                if (isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])) {
                    echo osc_draw_hidden_field('manufacturers', $_GET['manufacturers']);
                    $options = array(array('id' => '', 'text' => $osC_Language->get('filter_all_categories')));
                } else {
                    echo osc_draw_hidden_field('cPath', $cPath);
                    $options = array(array('id' => '', 'text' => $osC_Language->get('filter_all_manufacturers')));
                }
			?>
			<div class="row-fluid">
				<div class="span2">
            		<div class="btn-group">
            		<?php 
            		    if ($view_type == 'list') {
            		?>
                		<a class="btn btn-small active"><i class="icon-th-list"></i></a> / 
                		<a class="btn btn-small" href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid'); ?>"><i class="icon-th"></i></a>
            		<?php 
            		    } else {
            		?>
                		<a class="btn btn-small" href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=list'); ?>"><i class="icon-th-list"></i></a> / 
                		<a class="btn btn-small active"><i class="icon-th"></i></a>
            		<?php 
            		    }
            		?>
            		</div>
				</div>
				<div class="span5" align="center">
					<?php 
    					if (count($filters) > 0) {
    					    echo osc_draw_pull_down_menu('filter', $filters, (isset($_GET['filter']) ? $_GET['filter'] : null), 'onchange="this.form.submit()"');
    					}
					?>
				</div>
				<div class="span5">
					<div class="pull-right">
                        <?php echo osc_draw_pull_down_menu('sort', $sort_array, $sort, 'onchange="this.form.submit()"'); ?>
					</div>
				</div>
			</div>
        </form>
        <?php 
            if ( ($Qlisting->numberOfRows() > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
        ?>
        <div class="seperator"></div>
        <div class="row-fluid">
        	<div class="span6 total-pages">
        		<?php echo $Qlisting->getBatchTotalPages($osC_Language->get('result_set_number_of_products')); ?>
        	</div>
        	<div class="span6">
                <div class="pagination clearfix">
                	<?php echo $Qlisting->getBatchPageLinks('page', osc_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>
                </div>        	
        	</div>
        </div>
        <?php 
            }
        ?>
	</div>

    <div class="moduleBox">
    	<ul class="products-list <?php echo $view_type; ?> btop clearfix">
        <?php
            while ($Qlisting->next()) {
                //initialize osC_Product object
                $osC_Product = new osC_Product($Qlisting->value('products_id'));
                
                //product type
                $type = $Qlisting->value('products_type');
                
                //short description
                $short_description = $Qlisting->value('products_short_description');
                
                //product link
                $href = osc_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_id') . (isset($_GET['manufacturers']) ? '&manufacturers=' . $_GET['manufacturers'] : ($cPath ? '&cPath=' . $cPath : '')));
                
                //image
                $image = $osC_Image->show($Qlisting->value('image'), $Qlisting->value('products_name'), 'class="thumb productImage"');
                $image_link = osc_link_object($href, $image, 'id="img_ac_productlisting_'. $Qlisting->value('products_id') . '"');
                
                $buy_now_link = osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $Qlisting->value('products_id') . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add');
                $compare_link = osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('action')) . '&cid=' . $Qlisting->value('products_id') . '&action=compare_products_add');
                $wishlist_link = osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $Qlisting->value('products_id') . '&' . osc_get_all_get_params(array('action')) . '&action=wishlist_add');
        ?>
    		<li class="clearfix">
                <div class="left">
                    <?php 
                        echo $image_link;
                    ?> 
                    <h3>
                    	<?php echo osc_link_object($href, $Qlisting->value('products_name')); ?>
                    </h3>
                    <p class="description">
                        <?php echo strip_tags($osC_Product->getDescription()); ?>
                    </p>
                </div>
                <div class="right">
                    <span class="price">
                        <?php echo $osC_Product->getPriceFormated(true); ?></span>
                    <span class="buttons hidden-phone">
                    	<?php 
                    	    if ($Qlisting->value('products_type') == PRODUCT_TYPE_SIMPLE) {
                    	?>
                        <a id="ac_productlisting_<?php echo $Qlisting->value('products_id'); ?>" class="btn btn-small btn-info ajaxAddToCart" href="<?php echo $buy_now_link; ?>">
                    	<?php 
                    	    } else {
                    	?>
                        <a class="btn btn-small btn-info" href="<?php echo $buy_now_link; ?>">
                    	<?php 
                    	    }
                    	?>
                        	<i class="icon-shopping-cart icon-white "></i> 
                        	<?php echo $osC_Language->get('button_buy_now'); ?>
                        </a><br />
                        <?php echo osc_link_object($compare_link, $osC_Language->get('add_to_compare')); ?><br />
                        <?php echo osc_link_object($wishlist_link, $osC_Language->get('add_to_wishlist')); ?>
                    </span>
                </div>
    		</li>
        <?php 
            }
        ?>
        </ul>
    </div>
    
	<div class="products-listing-action">
		<form id="products-filter" class="form-inline" action="<?php echo osc_href_link($_SERVER['SCRIPT_NAME']); ?>" method="get">
			<?php 
			    //drow hidden session id
			    echo osc_draw_hidden_session_id_field();
			    
                //whether the products attributes filter and the category/manufacturer filter is linked
                if (defined('PRODUCT_LINK_FILTER') && (PRODUCT_LINK_FILTER == '1')) {
                    if (isset($_GET['products_attributes']) && is_array($_GET['products_attributes'])) {
                        foreach($_GET['products_attributes'] as $att_value_id => $att_value) {
                            echo osc_draw_hidden_field('products_attributes[' . $att_value_id . ']', $att_value);
                        }
                    }
                }
                
                $keys = array('keywords', 'pfrom', 'pto', 'datefrom_days', 'datefrom_months', 'datefrom_years', 'dateto_days', 'dateto_months', 'dateto_years');
                foreach ($keys as $key) {
                    if (isset($_GET[$key]) && !empty($_GET[$key])) {
                        echo osc_draw_hidden_field($key, $_GET[$key]);
                    }
                }
                
                if (isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])) {
                    echo osc_draw_hidden_field('manufacturers', $_GET['manufacturers']);
                    $options = array(array('id' => '', 'text' => $osC_Language->get('filter_all_categories')));
                } else {
                    echo osc_draw_hidden_field('cPath', $cPath);
                    $options = array(array('id' => '', 'text' => $osC_Language->get('filter_all_manufacturers')));
                }
			?>
			<div class="row-fluid">
				<div class="span2">
            		<div class="btn-group">
            		<?php 
            		    if ($view_type == 'list') {
            		?>
                		<a class="btn btn-small active"><i class="icon-th-list"></i></a> / 
                		<a class="btn btn-small" href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid'); ?>"><i class="icon-th"></i></a>
            		<?php 
            		    } else {
            		?>
                		<a class="btn btn-small" href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=list'); ?>"><i class="icon-th-list"></i></a> / 
                		<a class="btn btn-small active"><i class="icon-th"></i></a>
            		<?php 
            		    }
            		?>
            		</div>
				</div>
				<div class="span5" align="center">
					<?php 
    					if (count($filters) > 0) {
    					    echo osc_draw_pull_down_menu('filter', $filters, (isset($_GET['filter']) ? $_GET['filter'] : null), 'onchange="this.form.submit()"');
    					}
					?>
				</div>
				<div class="span5">
					<div class="pull-right">
                        <?php echo osc_draw_pull_down_menu('sort', $sort_array, $sort, 'onchange="this.form.submit()"'); ?>
					</div>
				</div>
			</div>
        </form>
        <?php 
            if ( ($Qlisting->numberOfRows() > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
        ?>
        <div class="seperator"></div>
        <div class="row-fluid">
        	<div class="span6 total-pages">
        		<?php echo $Qlisting->getBatchTotalPages($osC_Language->get('result_set_number_of_products')); ?>
        	</div>
        	<div class="span6">
                <div class="pagination clearfix">
                	<?php echo $Qlisting->getBatchPageLinks('page', osc_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>
                </div>        	
        	</div>
        </div>
        <?php 
            }
        ?>
	</div>
<?php 
    } else {
        echo $osC_Language->get('no_products_in_category');
    }
?>