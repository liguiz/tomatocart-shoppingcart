/*
  $Id: popup_cart.js $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

var PopupCart = new Class({
  Implements: [Options],
  options: {
    remoteUrl: 'json.php',
    sessionName: 'sid',
    sessionId: null,
    isCartExpanded: false,
    triggerEl: $('popupCart'),
    btnClose: 'btnClosePopCart',
    container: $('pageHeader'),
    relativeTop: 20,
    relativeLeft: 242
  },
  
  
  initialize: function(options) {
    this.setOptions(options);
    this.registerEvents();
  },
  
  registerEvents: function() {
    this.options.triggerEl.addEvents({
      'click': function(e) {
        e.stop();
        
        if (this.options.isCartExpanded == false) {
          this.getShoppingCart();
        }else {
          this.cartContainer.fade('out');
          
          this.options.isCartExpanded = false;
        }
      }.bind(this)
    });
  },
  
  getShoppingCart: function() {
    var scope = this;
    
    var data = {
      template: this.options.template,
      module: 'popup_cart', 
      action: 'get_cart_contents'
    };
    data[this.options.sessionName] = this.options.sessionId;
    
    var loadRequest = new Request({
      url: this.options.remoteUrl,
      data: data,
      onSuccess: this.displayCart.bind(scope)
    }).send();
  },
  
  displayCart: function(response) {
    var result = JSON.decode(response);

    if (result.success == true) {
      if (!$defined(this.cartContainer)) {
        var pos = this.options.triggerEl.getCoordinates();
        
        this.cartContainer = new Element('div', {
          'html': result.content,
          'id': 'popupCartContent',
          'class': 'moduleBox',
          'styles': {
            'position': 'absolute',
            'top': pos.top + this.options.relativeTop,
            'left': pos.left - this.options.relativeLeft    
          }
        });
      } else {
        this.cartContainer.set('html', result.content);
      }
      
      this.options.container.adopt(this.cartContainer);
      this.cartContainer.setStyle('opacity', 0).fade('in');
      
      $(this.options.btnClose).addEvent('click', function(e) {
        e.stop();
        
        this.cartContainer.fade('out');
        
        this.options.isCartExpanded = false;
        
      }.bind(this));
      
      this.options.isCartExpanded = true;
    }
  }
});