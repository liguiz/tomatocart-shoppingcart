/*
  $Id: variants.js $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2010 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

var TocVariants = new Class({
  Implements: [Options],
  options: {
    hasSpecial: 0,
    remoteUrl: 'json.php',
    linkCompareProductsCls: '.compare-products',
    lang: {
      txtInStock: 'In Stock',
      txtOutOfStock: 'Out Of Stock',
      txtNotAvailable: 'Not Available',
      txtTaxText: 'incl. tax'
    }
  },
  
  sendRequest: function(data, fnSuccess) {
    data.module = 'products';
    
    var loadRequest = new Request({
      url: this.options.remoteUrl,
      data: data,
      onSuccess: fnSuccess.bind(this)
    }).send();
  },
  
  initialize: function(options) {
    this.setOptions(options);
    this.checkCompareProducts();
    this.initializeComboBox();
    this.updateView();
  },
  
  initializeComboBox: function() {
    this.options.combVariants.each(function(combobox) {
      combobox.addEvent('change', function() {
        this.updateView();
      }.bind(this));
    }.bind(this));
  },
  
  //Check whether the compare products feature is enabled
  checkCompareProducts: function() {
  	var linkCp = $$(this.options.linkCompareProductsCls);
  	
    if ($chk(linkCp)) {
      this.linkCp = linkCp[0];
      this.linkCpHref = this.linkCp.getProperty('href');
      
      if (this.linkCpHref.search(/cid=/) !== -1) {
        this.linkCpHref = this.linkCpHref.replace(/&cid=\d+/, '');
      }
    }
  },
  
  getProductsIdString: function() {
    var groups = [];
    this.options.combVariants.each(function(combobox) {
      var id = combobox.id.toString();
      var groups_id = id.substring(9, id.indexOf(']'));
      
      groups.push(groups_id + ':' + combobox.value);
    }.bind(this));
    
    return this.options.productsId + '#' + groups.join(';');
  },
    
  updateView: function(choice) {
  	var productsIdString = this.getProductsIdString();
  	
  	//if it is in the product info page and the product have any variants, add the variants into the compare products link
  	if ($chk(this.linkCp)) {
    	var href = this.linkCpHref + '&cid=' + productsIdString.replace(/#/, '_');
    	
    	this.linkCp.setProperty('href', href);
	  }
	  
    var product = this.options.variants[productsIdString];
    
    if (product == undefined || (product['status'] == 0)) {
      $('productInfoAvailable').innerHTML = '<font color="red">' + this.options.lang.txtNotAvailable + '</font>';
      this.disableInfoBox();
    } else {
      if (product['quantity'] > 0) {
        if (this.options.hasSpecial == 0) {
        	// get the formatted price of the variants product by ajax requst
        	this.sendRequest({action: 'get_variants_formatted_price', products_id_string: productsIdString}, function(response) {
            var result = JSON.decode(response);
            
            if (result.success == true) {
              $('productInfoPrice').set('text', result.formatted_price + ' ' + this.options.lang.txtTaxText);
            }else {
              alert(result.feedback);
            }
        	}.bind(this));
        }
        
        $('productInfoSku').set('text', product['sku']);
        if (this.options.displayQty == true) {
          $('productInfoQty').set('text', product['quantity'] + ' ' + this.options.unitClass);
        }
        $('productInfoAvailable').set('text', this.options.lang.txtInStock);
        
        $('shoppingCart').fade('in');
        $('shoppingAction').fade('in');
        
        this.changeImage(product['image']);
      } else {
        $('productInfoAvailable').set('text', this.options.lang.txtOutOfStock);
        $('productInfoQty').set('text', product['quantity'] + ' ' + this.options.unitClass);
        
        //if not allow checkout then disable the info box
        if (!this.options.allowCheckout) {
          this.disableInfoBox();
        }
      }
    }
  },
  
  disableInfoBox: function() {
    $('productInfoPrice').set('text', '--');
    $('productInfoSku').set('text', '--');
    if (this.options.displayQty == true) {
      $('productInfoQty').set('text', '--');
    } 
    $('shoppingCart').fade('out');
    $('shoppingAction').fade('out');
  },
  
  changeImage: function(image) {
    $$('.mini').each(function(link) {
      var href = link.getProperty('href');
      if (href.indexOf(image) > -1) {
        link.fireEvent('mouseover');
      }
    });
  }
});