<?php $settings = $osC_Box->getSettings(); ?>
<?php $buttons = $osC_Box->getButtonsCode(); ?>
<?php $default_account = $osC_Box->isDefaultAccount(); ?>
<?php $shopid = $default_account ? $osC_Box->getDefaultShopid() : $settings["shopid"]; ?>

<div id="addshoppers_container_box">

    <div id="addshoppers_container">
        <div id="addshoppers_buttons" class="addshoppers-enabled">
            <?php if ($default_account): ?>
                <div style="float:left">
                    <div data-style="standard" class="share-buttons share-buttons-fb-like"></div>
                    <div class="share-buttons share-buttons-og" data-action="want" data-counter="false"></div>
                    <div class="share-buttons share-buttons-og" data-action="own" data-counter="false"></div>
                </div>
                <div class="share-buttons share-buttons-panel" data-style="medium" data-counter="true" data-oauth="true" data-hover="true" data-buttons="twitter,facebook,pinterest"></div>
            <?php else: ?>
                <?php if ($settings["use_open_graph_buttons"] == 1): ?>
                    <div style="float:left"><?= $buttons["buttons"]["open-graph"] ?></div>       
                <?php endif; ?>
                <?php if ($settings["use_default_buttons"] == 1): ?>
                    <?= $buttons["buttons"]["button2"] ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        window.addEvent('domready', function() {
            $('addshoppers_container').injectTop('pageWrapper');
        });
        
        AddShoppersTracking = {};
        
        if ($defined($$('meta[name=keywords]'))) {
            var names = $$('meta[name=keywords]');
            
            if (names.length > 0) {
                AddShoppersTracking.name = names[0].getProperty('content');
            }
        }
        
        if ($defined($$('#tabDescription div.content ul'))) {
            var descriptions = $$('#tabDescription div.content ul');
            
            if (descriptions.length > 0) {
                AddShoppersTracking.description = descriptions[0].get('text').substr(0,200);
            }
        }
        
        
        if ($defined($('product_image'))) {
            AddShoppersTracking.image = $('product_image').getProperty('large-img');
        }
        
        if ($defined($('productInfoPrice'))) {
            AddShoppersTracking.price = $('productInfoPrice').get('text');
        }
     
        var js = document.createElement('script'); js.type = 'text/javascript'; js.async = true; js.id = 'AddShoppers';
        js.src = ('https:' == document.location.protocol ? 'https://shop.pe/widget/' : 'http://cdn.shop.pe/widget/') + 'widget_async.js#<?= $shopid ?>';
        document.getElementsByTagName("head")[0].appendChild(js);
        
<?php if ($_SERVER["REQUEST_URI"] == "/checkout.php?success"): ?>   
    <?php list($total, $order_id) = $osC_Box->getTotal(); ?>
            AddShoppersConversion = {
                order_id: '<?= $order_id; ?>',
                value: '<?= $total; ?>'
            };
            var js = document.createElement('script'); js.type = 'text/javascript'; js.async = true; js.id = 'AddShoppers';
            js.src = ('https:' == document.location.protocol ? 'https://shop.pe/widget/' : 'http://cdn.shop.pe/widget/') + 'widget_async.js#<?= $shopid ?>';
            document.getElementsByTagName("head")[0].appendChild(js);
<?php endif; ?>
    </script>
</div>