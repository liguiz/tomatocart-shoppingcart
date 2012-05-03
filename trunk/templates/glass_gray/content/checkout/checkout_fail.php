<?php
/*
  $Id: checkout_fail.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div>
  <div style="float: left;"><?php echo osc_image('templates/' . $osC_Template->getCode() . '/images/order_process_fail.png', $osC_Template->getPageTitle()); ?></div>

  <div style="padding-top: 20px;padding-left:140px;">
    <p><?php echo $osC_Language->get('order_processed_unsuccessfully'); ?></p>
    <p>
      <?php
        if ($messageStack->size('checkout') > 0) {
          echo $messageStack->output('checkout');
        }
      ?>
    </p>


    <h2><?php echo $osC_Language->get('thanks_for_shopping_with_us'); ?></h2>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'checkout', false, false, true), osc_draw_image_button('button_continue.gif', $osC_Language->get('button_continue'))); ?>
</div>

