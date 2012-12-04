<?php
/*
  $Id: main.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
 */

echo 'Ext.namespace("Toc.modules_addshoppers");';

include('modules_addshoppers_dialog.php');
?>

var app_desktop = null;

Ext.override(TocDesktop.ModulesAddshoppersWindow, {

  createWindow: function(){
    var desktop = this.app.getDesktop();
    app_desktop = desktop;
    var win = desktop.getWindow('modules_addshoppers-win');
     
    if(!win){
      win = desktop.createWindow({},Toc.modules_addshoppers.AddshoppersDialog);
    }
    
    win.show();
  }

});