<?php
/*
  $Id: product_listing.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<?php 
  //put the products into an array
  $products = array();
  if ($Qlisting->numberOfRows() > 0) {
    while($Qlisting->next()) {
      $products[] = $Qlisting->toArray();
    }
  }
  
  //fix the bug: sort the products by price for the variants products
  if ($osC_Products->_sort_by == 'final_price') {
    usort($products, 'compare_variants_price');
  }

  //verify whether the products are existed
  if (count($products) > 0) {
?>

    <?php 
    //whether the product attributes filter is enabled
    if (defined('PRODUCT_ATTRIBUTES_FILTER') && (PRODUCT_ATTRIBUTES_FILTER == '1')) {
      require('includes/modules/products_attributes.php');
    }
    ?>
    
    <!-- Tools Bar -->
    <div class="toolsWrapper clearfix">
      <!-- tools -->
      <div class="tools clearfix">
         <!-- filters -->
        <?php if ($frm_filters !== null) { ?>
          <div class="filters"><?php echo $frm_filters; ?></div>
        <?php } ?>
       
        <!-- tab navs -->
        <div class="tabNavs">
          <?php if ($view_type == 'list') { ?>
            <button class="listView list-actived">List</button>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid2'); ?>" class="gridTwo">Grid 2</a>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid3'); ?>" class="gridThree">Grid 3</a>
          <?php }else if ($view_type == 'grid2') { ?>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=list'); ?>" class="listView">list</a>
            <button class="gridTwo gridTwo-actived">List</button>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid3'); ?>" class="gridThree">Grid 3</a>
          <?php }else if ($view_type == 'grid3') { ?>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=list'); ?>" class="listView">list</a>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid2'); ?>" class="gridTwo">Grid 2</a>
            <button class="gridThree gridThree-actived">List</button>
          <?php }?>  
        </div>
        <!-- End: tab navs -->
      </div>
      <!-- End: tools -->
      
      <!-- pagination -->
      <?php if ( ($Qlisting->numberOfRows() > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) { ?>
      <div class="seperator"></div>
      
      <div class="listingPageLinks clearfix">
        <?php echo $Qlisting->getBatchPageLinks('page', osc_get_all_get_params(array('page', 'info', 'x', 'y')), false); ?>
      
        <div class="totalPages"><?php echo $Qlisting->getBatchTotalPages($osC_Language->get('result_set_number_of_products')); ?></div>
      </div>
      <?php } ?>
      <!-- End: pagination -->
    </div>
    <!-- End: Tools Bar -->
     
    <div class="clearfix productListingWrapper">
      <!-- List Style -->
      <?php if ($view_type == 'list') { ?>
      <div class="productList">
        <?php
          $rows = 0;
          foreach($products as $product) {
            $rows++;
            
            if ($rows / 2 == floor($rows / 2)) {
              $row_class = 'even';
            }else {
              $row_class = 'odd';
            }
          ?>
          <div class="productListingRow clearfix <?php echo $row_class; ?>">
            <!-- Spacer -->
            <div class="spacer clearfix">
              <!-- Column 1 -->
              <div class="col1">
                <div class="productImageContainer">
                  <?php
                    if ($product['products_type'] == PRODUCT_TYPE_SIMPLE) {
                      echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $osC_Image->show($product['image'], $product['products_name']), 'id="img_ac_productlisting_' . $product['products_id'] . '"');
                    }else {
                      echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $osC_Image->show($product['image'], $product['products_name']));
                    }    
                  ?>
                </div>
              </div>
              <!-- End: Column 1 -->
              
              <!-- Column 2 -->
              <div class="col2">
                <div class="productName">
                  <h2><?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $product['products_name']); ?></h2>
                </div>
                <p class="productShortDescription"><?php echo $product['products_short_description']; ?></p>
              </div>
              <!-- End: Column 2 -->
              
              <!-- Column 3 -->
              <div class="col3">
                <div class="productPrice">
                  <?php
                    $osC_Product = new osC_Product($product['products_id']);
                    echo $osC_Product->getPriceFormated(true);
                  ?>
                </div>
                
                <!-- Cart Areas -->
                <div class="addtocartArea">
                  <div class="cartActions">
                    <div class="addAction">
                    <?php
                        if ($product['products_type'] == PRODUCT_TYPE_SIMPLE) {
                          echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add'), osc_draw_image_button('button_buy_now.gif', $osC_Language->get('button_buy_now'), 'class="ajaxAddToCart" id="ac_productlisting_' . $product['products_id'] . '"'));
                        }else {
                          echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add'), osc_draw_image_button('button_buy_now.gif', $osC_Language->get('button_buy_now')));
                        }
                    ?>
                    </div>
                    
                    <div class="otherActions">
                      <?php
                        if ($osC_Template->isInstalled('compare_products', 'boxes')) {
                          echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), 'cid=' . $product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=compare_products_add'), $osC_Language->get('add_to_compare'));
                        }  
                  
                        echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=wishlist_add'), $osC_Language->get('add_to_wishlist')); 
                      ?>
                    </div>
                  </div>
                </div>
                <!-- End: Cart Areas -->
              </div>
              <!-- End: Column 3 -->  
            </div>
            <!-- End: Spacer -->
          </div>
          <?php      
              }
          ?>
        </div>
        <!-- End: List Style -->
        
        <!-- Two Column - Grid Style -->
        <?php } else if ($view_type == 'grid2') { ?>
        <div class="productGridTwo">
          <?php
            //get the two products for each row
            $row_grid_two = 0;
            while(count(array_slice($products, $row_grid_two * 2, 2)) > 0) {
              $row_products = array_slice($products, $row_grid_two * 2, 2);
              
          ?>
          
          <!-- Product Grid Row -->
          <div class="productGridTwoRow clearfix">
          <?php      
          foreach($row_products as $key => $row_product) {
          ?>
            <!-- Grid Item -->
            <div class="productGridTwoItem<?php echo $key == 0 ? ' odd' : ' even'; echo $key % 2 == 0 ? ' firstItem' : ''; ?>">
              <!-- Spacer -->
              <div class="spacer">
                <!-- column 1 -->
                <div class="col1">
                  <div class="productImageContainer">
                    <?php
                      if ($row_product['products_type'] == PRODUCT_TYPE_SIMPLE) {
                        echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $row_product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $osC_Image->show($row_product['image'], $row_product['products_name']), 'id="img_ac_productlisting_' . $row_product['products_id'] . '"');
                      }else {
                        echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $row_product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $osC_Image->show($row_product['image'], $row_product['products_name']));
                      }    
                    ?>
                  </div>
                </div>
                <!-- End: column 1 -->
                
                <!-- column 2 -->
                <div class="col2">
                  <div class="productName">
                    <h2><?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $row_product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $row_product['products_name']); ?></h2>
                  </div>
                  
                  <div class="productPrice">
                    <?php
                      $osC_Product = new osC_Product($row_product['products_id']);
                      echo $osC_Product->getPriceFormated(true);
                    ?>
                  </div>
                  
                  <!-- Cart Areas -->
                  <div class="addtocartArea">
                    <div class="cartActions">
                      <div class="addAction">
                      <?php
                          if ($row_product['products_type'] == PRODUCT_TYPE_SIMPLE) {
                            echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $row_product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add'), osc_draw_image_button('button_buy_now.gif', $osC_Language->get('button_buy_now'), 'class="ajaxAddToCart" id="ac_productlisting_' . $row_product['products_id'] . '"'));
                          }else {
                            echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $row_product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add'), osc_draw_image_button('button_buy_now.gif', $osC_Language->get('button_buy_now')));
                          }
                      ?>
                      </div>
                      
                      <div class="otherActions">
                        <?php
                          if ($osC_Template->isInstalled('compare_products', 'boxes')) {
                            echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), 'cid=' . $row_product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=compare_products_add'), $osC_Language->get('add_to_compare'));
                          }  
                    
                          echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $row_product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=wishlist_add'), $osC_Language->get('add_to_wishlist')); 
                        ?>
                      </div>
                    </div>
                  </div>
                  <!-- End: Cart Areas -->
                </div>
                <!-- End: column 2 -->
              </div>
              <!-- End: Spacer -->
            </div>
            <!-- End: Grid Item -->
           <?php   
           }
           ?>
           </div>
            <!-- End: Product Grid Row -->
            
           <?php
              $row_grid_two++;
            }
          ?>
        </div>
        <!-- End: Two Column - Grid Style -->
        
        <!-- Three Column - Grid Style -->  
        <?php } else if ($view_type == 'grid3') { ?>
        <div class="productGridThree">
          <?php
            //get the two products for each row
            $row_grid_three = 0;
            while(count(array_slice($products, $row_grid_three * 3, 3)) > 0) {
              $row_col3_products = array_slice($products, $row_grid_three * 3, 3);
              
          ?>
          
          <!-- Product Grid Row -->
          <div class="productGridThreeRow clearfix">
          
          <?php      
              foreach($row_col3_products as $col3_key => $row_col3_product) {
           ?>
                  <!-- Grid Item -->
                  <div class="productGridThreeItem<?php echo ($col3_key + 1) / 2 == 1 ? ' even' : ' odd'; echo $col3_key % 3 == 0 ? ' firstItem' : ''; ?>">
                    <!-- Spacer -->
                    <div class="spacer">
                      <div class="productImageContainer">
                        <?php
                          if ($row_col3_product['products_type'] == PRODUCT_TYPE_SIMPLE) {
                            echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $row_col3_product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $osC_Image->show($row_col3_product['image'], $row_col3_product['products_name']), 'id="img_ac_productlisting_' . $row_col3_product['products_id'] . '"');
                          }else {
                            echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $row_col3_product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $osC_Image->show($row_col3_product['image'], $row_col3_product['products_name']));
                          }    
                        ?>
                      </div>
                      
                      <div class="productName">
                        <h2><?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $row_col3_product['products_id'] . ($cPath ? '&cPath=' . $cPath : '')), $row_col3_product['products_name']); ?></h2>
                      </div>
                      
                      <div class="productPrice">
                        <?php
                          $osC_Product = new osC_Product($row_col3_product['products_id']);
                          echo $osC_Product->getPriceFormated(true);
                        ?>
                      </div>
                      
                      <!-- Cart Areas -->
                      <div class="addtocartArea">
                        <div class="cartActions">
                          <div class="addAction">
                          <?php
                              if ($row_col3_product['products_type'] == PRODUCT_TYPE_SIMPLE) {
                                echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $row_col3_product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add'), osc_draw_image_button('button_buy_now.gif', $osC_Language->get('button_buy_now'), 'class="ajaxAddToCart" id="ac_productlisting_' . $row_col3_product['products_id'] . '"'));
                              }else {
                                echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $row_col3_product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add'), osc_draw_image_button('button_buy_now.gif', $osC_Language->get('button_buy_now')));
                              }
                          ?>
                          </div>
                          
                          <div class="otherActions">
                            <?php
                              if ($osC_Template->isInstalled('compare_products', 'boxes')) {
                                echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), 'cid=' . $row_col3_product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=compare_products_add'), $osC_Language->get('add_to_compare'));
                              }  
                        
                              echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $row_col3_product['products_id'] . '&' . osc_get_all_get_params(array('action')) . '&action=wishlist_add'), $osC_Language->get('add_to_wishlist')); 
                            ?>
                          </div>
                        </div>
                      </div>
                      <!-- End: Cart Areas -->
                    </div>
                    <!-- End: Spacer -->
                  </div>
                  <!-- End: Grid Item -->
           <?php   
              }
           ?>
           </div>
            <!-- End: Product Grid Row -->
           <?php
              $row_grid_three++;
            }
          ?>
        </div>
      <?php } ?>
      <!-- End: Three Column - Grid Style -->
    </div>
    
    <!-- Tools Bar -->
    <div class="toolsWrapper clearfix">
      <!-- tools -->
      <div class="tools clearfix">
        <!-- filters -->
        <?php if ($frm_filters !== null): ?>
          <div class="filters"><?php echo $frm_filters; ?></div>
        <?php endif; ?>
        
        <!-- tab navs -->
        <div class="tabNavs">
          <?php if ($view_type == 'list') { ?>
            <button class="listView list-actived">List</button>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid2'); ?>" class="gridTwo">Grid 2</a>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid3'); ?>" class="gridThree">Grid 3</a>
          <?php }else if ($view_type == 'grid2') { ?>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=list'); ?>" class="listView">list</a>
            <button class="gridTwo gridTwo-actived">List</button>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid3'); ?>" class="gridThree">Grid 3</a>
          <?php }else if ($view_type == 'grid3') { ?>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=list'); ?>" class="listView">list</a>
            <a href="<?php echo osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), osc_get_all_get_params(array('view')) . '&view=grid2'); ?>" class="gridTwo">Grid 2</a>
            <button class="gridThree gridThree-actived">List</button>
          <?php }?>  
        </div>
        <!-- End: tab navs -->
      </div>
      <!-- End: tools -->
      
       <!-- pagination -->
      <?php if ( ($Qlisting->numberOfRows() > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ): ?>
      <div class="seperator"></div>
      
      <div class="listingPageLinks clearfix">
        <?php echo $Qlisting->getBatchPageLinks('page', osc_get_all_get_params(array('page', 'info', 'x', 'y')), false); ?>
      
        <div class="totalPages"><?php echo $Qlisting->getBatchTotalPages($osC_Language->get('result_set_number_of_products')); ?></div>
      </div>
      <?php endif; ?>
      <!-- End: pagination -->
    </div>
    <!-- End: Tools Bar -->
<?php
  } else {
    echo '<p>' . $osC_Language->get('no_products_in_category') . '</p>';
  }
?>