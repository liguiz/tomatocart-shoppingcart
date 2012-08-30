<?php
/*
  $Id: modules_addshoppers_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
 */

include(dirname(__FILE__) . '/../../jsons/modules_addshoppers.php');
?>//<script>
    var addshoppers_window_handle;

    Toc.modules_addshoppers.AddshoppersDialog = function(config) {  
        config = config || {};
        config.id = 'modules_addshoppers-win';
        config.title = '<?php echo $osC_Language->get('heading_title'); ?>';
        config.width = 800;
        config.height = 600;
        config.iconCls = 'icon-modules_addshoppers-win';
        config.layout = 'fit';
        config.buttons = [
            {
                text: TocLanguage.btnClear,
                handler: function() { 
                    Ext.Msg.show({
                        title:'Clear account settings?',
                        msg: 'Are you shure you want to remove your credentials from database?',
                        buttons: Ext.Msg.OKCANCEL,
                        animEl: 'elId',
                        icon: Ext.MessageBox.QUESTION,
                        fn:  function(btn){
                            if(btn=='ok'){
                                Ext.Ajax.request({
                                    url: Toc.CONF.CONN_URL,
                                    success: function(data){
                                        addshoppers_window_handle.removeAll();
                                        addshoppers_window_handle.add(addshoppers_window_handle.buildFormLoginRegister());
                                        addshoppers_window_handle.doLayout();
                                    },
                                    failure: function(){},
                                    params: { 
                                        module : "modules_addshoppers",
                                        action : "wipe_data"
                                    }
                                });  
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            },
            {
                text: TocLanguage.btnClose,
                handler: function() { 
                    this.close();
                },
                scope: this
            },
            {
                text: 'About',
                handler: function() { 
                    var info = app_desktop.getWindow('modules_addshoppersabout-win');
   
                    if(!info){
                      info = app_desktop.createWindow({},Toc.modules_addshoppers.AddshoppersAboutDialog);
                    }

                    info.show();
                },
                scope: this
            },
        ];      

        addshoppers_window_handle = this;

        var Settings = <?php echo json_encode(toC_Json_Modules_addshoppers::getSettings()); ?>;

        if(Settings != false && Settings.user_login != '' && Settings.user_login != null && Settings.shopid != '' && Settings.shopid != null && Settings.api_key != '' && Settings.api_key != null)
        {
            config.items = this.buildSettingsForm(Settings);
        } else {
            config.items = this.buildFormLoginRegister();
        }

        Toc.modules_addshoppers.AddshoppersDialog.superclass.constructor.call(this,config); 
    };
    
    Toc.modules_addshoppers.AddshoppersAboutDialog = function (config)
    {
        var string = '<?php $str = <<< EOD
<div style="padding: 20px; margin: 0 auto; text-align: center;">
    <div>
        <img src="/admin/includes/extmodules/modules_addshoppers/static/img/feeds.png" />
    </div>
    <div class="big-black">
        100's of button styles available to match your site's look &amp; feel.
        Place social buttons anywhere. <a target="_blank" href="http://help.addshoppers.com/knowledgebase/articles/98896-social-sharing-button-placement-examples">learn more</a>
    </div>
    <div style="margin-top: 2em;">
        <h2 style="font-size: 20px;">Need help?</h2>
        <span class="url"><a target="_blank" href="http://forums.addshoppers.com">http://forums.addshoppers.com</a></span>
    </div>
    <!-- div style="margin-top: 1em">
        <h2 style="font-size: 20px">Advanced integration instruction</h2>
        <p>To change button types or positioning on any theme:</p>
        <ol>
            <li>1. Login to your <a target="_blank" href="https://www.addshoppers.com/merchants">AddShoppers Merchant Admin</a>.</li>
            <li>2. From the left navigation, go to <i>Get Apps -&gt; Sharing Buttons</i></li>
            <li>3. Select the button you want and copy the div code.</li>
            <li>4. Find file <i>product.tpl</i> in <i>themes/prestashop</i>.</li>
            <li>5. Paste our code where you want the buttons to appear.</li>
            <li>6. Don't forget to create canonical links for products or install the appropriate PrestaShop module.</li>
        </ol>
    </div -->
    <div style="text-align: left;">
        <h2 style="font-size: 20px; margin: 1em auto 1em auto; text-align: center">About AddShoppers</h2>
        AddShoppers is a free social sharing and analytics platform built for eCommerce.
        We make it easy to add social sharing buttons to your site, measure the ROI of social at the SKU level,
        and increase sharing by rewarding social actions. You'll discover the value of social sharing, identify
        influencers, and decrease shopping cart abandonment by adding AddShoppers social apps to your store.
        <div class="get-started">
            <a target="_blank" href="http://www.addshoppers.com">Get started with your free account at AddShoppers.com.</a>
        </div>
    </div>
    <div style="margin-top: 2em">
        If you're a large enterprise retailer who needs a more custom solution,
        <a target="_blank" href="http://www.addshoppers.com/enterprise">learn more</a>.
    </div>
</div>
EOD;
        echo addslashes(preg_replace( '/\s+/', ' ', trim($str))); ?>';
            
        config = {};
        config.id = 'modules_addshoppersabout-win';
        config.title = '<?php echo $osC_Language->get('heading_title'); ?> - About';
        config.width = 700;
        config.height = 550;
        config.iconCls = 'icon-modules_addshoppers-win';
        config.layout = 'fit';
        config.modal = true;
        config.buttons = [
            {
                text: TocLanguage.btnClose,
                handler: function() { 
                    this.close();
                },
                scope: this
            },
        ];      

        config.items = [
            {
                xtype: 'statictextfield', 
                value: string,
                hideLabel: true
            },
        ];

        Toc.modules_addshoppers.AddshoppersAboutDialog.superclass.constructor.call(this,config); 
    };

    Ext.extend(Toc.modules_addshoppers.AddshoppersAboutDialog, Ext.Window, {});
    
    Ext.extend(Toc.modules_addshoppers.AddshoppersDialog, Ext.Window, {

        buildFormLoginRegister: function() {

            this.fsLoginForm = new Ext.form.FieldSet({
                title: 'Login',
                autoHeight: true,
                items: [
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Email Address', 
                        value: "",
                        name: "login_email"
                    },
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Password', 
                        inputType: "password",
                        value: "",
                        name: "login_password"
                    }],
                buttons: [
                    new Ext.Button({
                        text: 'Login',
                        handler: function(){
                            this.submitLoginForm();
                        },
                        scope:this
                    })
                ]
            });

            var store_categories  = new Ext.data.SimpleStore({
                fields: ['value'],
                data : [                              
                    ['Select Category'],
                    ['Apparel & Clothing'],
                    ['Arts & Antiques'],
                    ['Automotive & Vehicles'],
                    ['Collectibles'],
                    ['Crafts & Hobbies'],
                    ['Baby & Children'],
                    ['Business & Industrial'],
                    ['Cameras & Optics'],
                    ['Electronics'],
                    ['Entertainment & Media'],
                    ['Food, Beverages, & Tobacco'],
                    ['Furniture'],
                    ['General Merchandise'],
                    ['Gifts'],
                    ['Hardware'],
                    ['Health & Beauty'],
                    ['Holiday'],
                    ['Home & Garden'],
                    ['Jewelry'],
                    ['Luggage & Bags'],
                    ['Mature / Adult'],
                    ['Music'],
                    ['Novelty'],
                    ['Office Supplies'],
                    ['Pets & Animals'],
                    ['Software'],
                    ['Sporting Goods & Outdoors'],
                    ['Toys & Games'],
                    ['Travel'],
                    ['Other']
                ]
            }); 


            this.fsRegisterForm = new Ext.form.FieldSet({
                title: 'Create New Account',
                autoHeight: true,
                items: [
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Email Address', 
                        value: "",
                        name: 'register_email'
                    },
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Password', 
                        inputType: "password",
                        value: "",
                        name: 'register_password_1'
                    },
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Confirm Password', 
                        inputType: "password",
                        value: "",
                        name: 'register_password_2'
                    },
                    {
                        xtype: 'combo', 
                        fieldLabel: 'Category', 
                        value: 'Select Category',
                        mode: "local",
                        store: store_categories,
                        displayField: 'value',
                        valueField: 'value',
                        allowBlank: false,
                        forceSelection: true,
                        editable: false,
                        typeAhead: false,
                        triggerAction: 'all',
                        name: 'register_category'

                    },
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Phone (optional)', 
                        value: "",
                        name: 'register_phone'
                    }
                ],
                buttons: [
                    new Ext.Button({
                        text: 'Create Free Account',
                        handler: function(){
                            this.submitRegisterForm();
                        },
                        scope:this
                    })
                ]
            });

            this.formLoginRegister = new Ext.form.FormPanel({
                url: Toc.CONF.CONN_URL,
                style: 'padding: 10px',
                border: false,
                items: [this.fsLoginForm, this.fsRegisterForm]
            });

            return this.formLoginRegister;
        },

        buildSettingsForm : function(settings) {       
            this.fsAccountSettingsForm = new Ext.form.FieldSet({
                title: 'Account Settings',
                autoHeight: true,
                items: [
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Email Address', 
                        value: settings.user_login,
                        name: 'login_email'
                    },
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Password', 
                        inputType: "password",
                        value: settings.password,
                        name: 'login_password'
                    },
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'API Key', 
                        value: settings.api_key,
                        name: 'api_key'
                    },
                    {
                        xtype: 'textfield', 
                        fieldLabel: 'Shop ID', 
                        value: settings.shopid,
                        name: 'shopid'
                    }
                ],
                buttons: [
                    new Ext.Button({
                        text: 'Save',
                        handler: function(){
                            this.submitAccountSettingsForm();
                        },
                        scope:this
                    })
                ]
            });

            this.fsDefaultAppsForm = new Ext.form.FieldSet({
                title: 'Default Social Apps',
                autoHeight: true,
                items: [
                    {
                        xtype: 'statictextfield', 
                        value: 'These Apps are designed to work with default theme. If you have another theme or would like further customizations, please <a href="http://help.addshoppers.com/" target="_blank">follow the instructions here.</a>',
                        hideLabel: true
                    },
                    {
                        xtype: 'checkbox', 
                        checked: parseInt(settings.use_open_graph_buttons) ? true : false,
                        value: 1,
                        fieldLabel: 'Use default social buttons',
                        name: 'use_default_buttons'
                    },
                    {
                        xtype: 'checkbox',
                        checked: parseInt(settings.use_open_graph_buttons) ? true : false,
                        value: 1,
                        fieldLabel: 'Use Facebook Open Graph Buttons',
                        name: 'use_open_graph_buttons'
                    },
                    {
                        xtype: 'statictextfield', 
                        value: '<?= '<strong>Follow us for updates on new features:</strong><center class="social-links"><iframe scrolling="no" frameborder="0" allowtransparency="true" src="http://platform.twitter.com/widgets/follow_button.1340179658.html#_=1344513230302&amp;id=twitter-widget-0&amp;lang=en&amp;screen_name=addshoppers&amp;show_count=false&amp;show_screen_name=true&amp;size=l" class="twitter-follow-button" style="width: 178px; height: 28px;" title="Twitter Follow Button"></iframe><div data-show-faces="true" data-layout="button_count" data-send="false" data-href="https://www.facebook.com/addshoppers" class="fb-like fb_edge_widget_with_comment fb_iframe_widget"><span style="height: 20px; width: 90px;"><iframe scrolling="no" id="f185a1172071" name="f62e17eb9856ca" style="border: medium none; overflow: hidden; height: 20px; width: 90px;" title="Like this content on Facebook." class="fb_ltr" src="http://www.facebook.com/plugins/like.php?channel_url=http://static.ak.facebook.com/connect/xd_arbiter.php?version=9#cb=ff3adc17b4f0ba&origin=<?= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&domain=<?= HTTP_SERVER; ?>&relation=parent.parent&amp;extended_social_context=false&amp;href=https://www.facebook.com/addshoppers&amp;layout=button_count&amp;locale=en_US&amp;node_type=link&amp;sdk=joey&amp;send=false&amp;show_faces=false&amp;width=90"></iframe></span></div><div id="___plusone_0" style="height: 20px; width: 90px; display: inline-block; text-indent: 0px; margin: 0px; padding: 0px; background: none repeat scroll 0% 0% transparent; border-style: none; float: none; line-height: normal; font-size: 1px; vertical-align: baseline;"><iframe width="100%" scrolling="no" frameborder="0" allowtransparency="true" hspace="0" marginheight="0" marginwidth="0" style="position: static; top: 0px; width: 90px; margin: 0px; border-style: none; left: 0px; visibility: visible; height: 20px;" tabindex="0" vspace="0" id="I0_1344513230011" name="I0_1344513230011" src="https://plusone.google.com/_/+1/fastbutton?bsv=pr&amp;url=http%3A%2F%2Fplus.google.com%2F112540297435892482797%3Frel%3Dpublisher&amp;size=medium&amp;count=true&amp;origin=http%3A%2F%2Fwnc.dev.clearcode.cc&amp;hl=en-US&amp;jsh=m%3B%2F_%2Fapps-static%2F_%2Fjs%2Fgapi%2F__features__%2Frt%3Dj%2Fver%3DRGzHszIAbCc.pl.%2Fsv%3D1%2Fam%3D!9WWg3hgFJrKAVXxNQA%2Fd%3D1%2Frs%3DAItRSTNpkCTXXPW8MjynzGTaS6RRxI0E-g#_methods=onPlusOne%2C_ready%2C_close%2C_open%2C_resizeMe%2C_renderstart%2Conload&amp;id=I0_1344513230011&amp;parent=http%3A%2F%2Fwnc.dev.clearcode.cc" title="+1"></iframe></div><script type="text/javascript">(function() {var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;po.src = "https://apis.google.com/js/plusone.js";var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);})();</script><a type="application/rss+xml" title="Subscribe to my feed" rel="alternate" href="http://feeds.feedburner.com/addshoppers" style="vertical-align:middle"><img style="vertical-align:middle;margin-top:-2px;" src="http://www.feedburner.com/fb/images/pub/feed-icon16x16.png" alt=""></a></center>'; ?>',
                        hideLabel: true
                    },
                ],
                buttons: [
                    new Ext.Button({
                        style: 'position: relative; top: -10px;',
                        text: 'Save',
                        handler: function(){
                            this.submitAccountSettingsForm();
                        },
                        scope:this
                    })
                ]
            });

            this.formSettings = new Ext.form.FormPanel({
                url: Toc.CONF.CONN_URL,
                style: 'padding: 10px',
                border: false,
                items: [this.fsAccountSettingsForm,this.fsDefaultAppsForm]
            });

            return this.formSettings;
        },

        submitLoginForm: function() {
            this.formLoginRegister.form.submit({
                params: {
                    module: 'modules_addshoppers',
                    action: 'try_login'
                },
                waitMsg: TocLanguage.formSubmitWaitMsg,
                success: function(form, action) {
                    this.removeAll();
                    this.add(this.buildSettingsForm(action.result.feedback));
                    this.doLayout();
                    this.fireEvent('saveSuccess', action.result.feedback);
                },    
                failure: function(form, action) {
                    if (action.failureType != 'client') {
                        Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
                    }
                },  
                scope: this
            });  
        },
        submitRegisterForm: function() {
            var select_category = this.formLoginRegister.form.items.get(5);

            if(select_category.value == "Select Category")
            {
                Ext.MessageBox.alert(TocLanguage.msgErrTitle, "Please select category!");
                return;
            }
        
            this.formLoginRegister.form.submit({
                params: {
                    module: 'modules_addshoppers',
                    action: 'try_register'
                },
                waitMsg: TocLanguage.formSubmitWaitMsg,
                success: function(form, action) {
                    this.removeAll();
                    this.add(this.buildSettingsForm(action.result.feedback));
                    this.doLayout();
                    this.fireEvent('saveSuccess', action.result.feedback);
                },    
                failure: function(form, action) {
                    if (action.failureType != 'client') {
                        Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
                    }
                },  
                scope: this
            });  
        },
        submitAccountSettingsForm: function() {
            this.formSettings.form.submit({
                params: {
                    module: 'modules_addshoppers',
                    action: 'update_settings'
                },
                waitMsg: TocLanguage.formSubmitWaitMsg,
                success: function(form, action) {
                    this.fireEvent('saveSuccess', action.result.feedback);
                },    
                failure: function(form, action) {
                    if (action.failureType != 'client') {
                        Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
                    }
                },  
                scope: this
            });  
        },
        events : {
            saveSuccess : function()
            {
                alert("OK");
            }
        }
    });