;(function($){
"use strict";
    
    var $body = $('body');

    // Add product in hometrialist table
    if( 'on' !== HomeTrial.option_data['btn_limit_login_off'] ){
        $body.on('click', 'a.hometrial-btn', function (e) {
            e.preventDefault();
            
            var $this = $(this),
                id = $this.data('product_id'),
                switchText = $this.data('switch-text'),
                currentText = $this.children('.hometrial-btn-text').text();;

            

            $this.addClass('loading');

            $.ajax({
                url: HomeTrial.ajaxurl,
                data: {
                    action: 'hometrial_add_to_list',
                    id: id,
                },
                dataType: 'json',
                method: 'GET',
                success: function ( response ) {
                    if ( response ) {
                        $this.removeClass('hometrial-btn');
                        $this.removeClass('loading');
                        $this.addClass('hometrial-btn-exist');
                        $this.children('.hometrial-btn-text').text( switchText );
                        $this.data('switch-text', currentText);
                        $body.find('.hometrial-counter').html( response.data.item_count ); 
                        //TODO use item_count here to disable button / add extra class
                        // something like this
                        console.log(response.data.item_count);
                        if(response.data.item_count >=  response.data.max_num_of_items){ // TODO declare the max number of items somewhere more global
                            $body.find('.hometrial-btn').addClass('hometrial-btn-disabled');
                            $body.find('.hometrial-btn').removeClass('hometrial-btn');
                        }
                        // end draft
                    } else {
                        console.log( 'Something wrong loading compare data' );
                    }
                },
                error: function ( response ) {
                    console.log('Something wrong with AJAX response.', response );
                },
                complete: function () {
                    $this.removeClass('loading');
                    $this.children('.hometrial-btn-text').text( switchText );
                    $this.data('switch-text', currentText);
                },
            });

        });

        // give error message when trying to add more than limit home trial items
        $body.on('click', 'a.hometrial-btn-disabled', function(e) {
            // e.preventDefault();
            console.log('maximum number of home trial items reached');
        });

        // remove product from hometrialist in shop
        $body.on('click', 'a.hometrial-btn-exist', function (e) {
            e.preventDefault();
            var $this = $(this),
                id = $this.data('product_id'),
                switchText = $this.data('switch-text'),
                currentText = $this.children('.hometrial-btn-text').text();

            

            $this.addClass('loading');

            $.ajax({
                url: HomeTrial.ajaxurl,
                data: {
                    action: 'hometrial_remove_from_list',
                    id: id,
                },
                dataType: 'json',
                method: 'GET',
                success: function ( response ) {
                    if ( response ) {
                        $this.removeClass('hometrial-btn-exist');
                        $this.removeClass('loading');
                        $this.addClass('hometrial-btn');
                        $this.children('.hometrial-btn-text').text( switchText );
                        $this.data('switch-text', currentText);
                        $body.find('.hometrial-counter').html( response.data.item_count ); 
                        //TODO use item_count here to disable button / add extra class
                        // something like this
                        console.log(response.data.item_count);
                        if(response.data.item_count < response.data.max_num_of_items){ // TODO declare the max number of items somewhere more global
                            $body.find('.hometrial-btn-disabled').addClass('hometrial-btn');
                            $body.find('.hometrial-btn-disabled').removeClass('hometrial-btn-disabled');
                        }
                        // end draft
                    } else {
                        console.log( 'Something wrong loading compare data' );
                    }
                },
                error: function ( response ) {
                    console.log('Something wrong with AJAX response.', response );
                },
                complete: function () {
                    $this.removeClass('loading');
                    $this.children('.hometrial-btn-text').text( switchText );
                    $this.data('switch-text', currentText);
                },
            });
        });
    }

    // Remove data from hometrialist table
    $body.on('click', 'a.hometrial-remove', function (e) {
        var $table = $('.hometrial-table-content');

        e.preventDefault();
        var $this = $(this),
            id = $this.data('product_id');

        $table.addClass('loading');
        $this.addClass('loading');

        $.ajax({
            url: HomeTrial.ajaxurl,
            data: {
                action: 'hometrial_remove_from_list',
                id: id,
            },
            dataType: 'json',
            method: 'GET',
            success: function (response) {
                if ( response ) {

                    var target_row = $this.closest('tr');
                    target_row.hide(400, function() {
                        $(this).remove();
                        var table_row = $('.hometrial-table-content table tbody tr').length;
                        if( table_row == 1 ){
                            $('.hometrial-table-content table tbody tr.hometrial-empty-tr').show();
                        }
                    });
                    $body.find('.hometrial-counter').html( response.data.item_count );

                } else {
                    console.log( 'Something wrong loading compare data' );
                }
            },
            error: function (data) {
                console.log('Something wrong with AJAX response.');
            },
            complete: function () {
                $table.removeClass('loading');
                $this.addClass('loading');
            },
        });

    });

    // Quantity
    $("div.hometrial-table-content").on("change", "input.qty", function() {
        $(this).closest('tr').find( "[data-quantity]" ).attr( "data-quantity", this.value );
    });

    // Delete table row after added to cart
    $(document).on('added_to_cart',function( e, fragments, carthash, button ){
        if( 'on' === HomeTrial.option_data['after_added_to_cart'] ){
            
            var target_row = button.closest('.hometrial_table tr');
            target_row.find('.added_to_cart').remove();

            target_row.hide(400, function() {
                $(this).remove();
                var table_row = $('.hometrial-table-content table tbody tr').length;
                if( table_row == 1 ){
                    $('.hometrial-table-content table tbody tr.hometrial-empty-tr').show();
                }
                $body.find('.hometrial-counter').html( table_row - 1 );
            });

        }
    });

    /**
     * Variation Product Add to cart from hometrial page
     */
    $(document).on( 'click', '.hometrial_table .product_type_variable.add_to_cart_button', function (e) {
        e.preventDefault();

        var $this = $(this),
            $product = $this.parents('.hometrial-product-add_to_cart').first(),
            $content = $product.find('.hometrial-quick-cart-form'),
            id = $this.data('product_id'),
            btn_loading_class = 'loading';

        if ($this.hasClass(btn_loading_class)) return;

        // Show Form
        if ( $product.hasClass('quick-cart-loaded') ) {
            $product.addClass('quick-cart-open');
            return;
        }

        var data = {
            action: 'hometrial_quick_variation_form',
            id: id
        };
        $.ajax({
            type: 'post',
            url: HomeTrial.ajaxurl,
            data: data,
            beforeSend: function (response) {
                $this.addClass(btn_loading_class);
                $product.addClass('loading-quick-cart');
            },
            success: function (response) {
                $content.append( response );
                hometrial_render_variation_data( $product );
                hometrial_inser_to_cart();
            },
            complete: function (response) {
                setTimeout(function () {
                    $this.removeClass(btn_loading_class);
                    $product.removeClass('loading-quick-cart');
                    $product.addClass('quick-cart-open quick-cart-loaded');
                }, 100);
            },
        });

        return false;

    });

    $(document).on('click', '.hometrial-quick-cart-close', function () {
        var $this = $(this),
            $product = $this.parents('.hometrial-product-add_to_cart');
        $product.removeClass('quick-cart-open');
    });

    $(document.body).on('added_to_cart', function ( e, fragments, carthash, button ) {

        var target_row = button.closest('tr');
        target_row.find('.hometrial-addtocart').addClass('added');
        $('.hometrial-product-add_to_cart').removeClass('quick-cart-open');

    });

    /**
     * [hometrial_render_variation_data] show variation data
     * @param  {[selector]} $product
     * @return {[void]} 
     */
    function hometrial_render_variation_data( $product ) {
        $product.find('.variations_form').wc_variation_form().find('.variations select:eq(0)').change();
        $product.find('.variations_form').trigger('wc_variation_form');
    }

    /**
     * [hometrial_inser_to_cart] Add to cart
     * @return {[void]}
     */
    function hometrial_inser_to_cart(){

        $(document).on( 'click', '.hometrial-quick-cart-form .single_add_to_cart_button:not(.disabled)', function (e) {
            e.preventDefault();

            var $this = $(this),
                $form           = $this.closest('form.cart'),
                product_qty     = $form.find('input[name=quantity]').val() || 1,
                product_id      = $form.find('input[name=product_id]').val() || $this.val(),
                variation_id    = $form.find('input[name=variation_id]').val() || 0;

            $this.addClass('loading');

            /* For Variation product */    
            var item = {},
                variations = $form.find( 'select[name^=attribute]' );
                if ( !variations.length) {
                    variations = $form.find( '[name^=attribute]:checked' );
                }
                if ( !variations.length) {
                    variations = $form.find( 'input[name^=attribute]' );
                }

                variations.each( function() {
                    var $thisitem = $( this ),
                        attributeName = $thisitem.attr( 'name' ),
                        attributevalue = $thisitem.val(),
                        index,
                        attributeTaxName;
                        $thisitem.removeClass( 'error' );
                    if ( attributevalue.length === 0 ) {
                        index = attributeName.lastIndexOf( '_' );
                        attributeTaxName = attributeName.substring( index + 1 );
                        $thisitem.addClass( 'required error' );
                    } else {
                        item[attributeName] = attributevalue;
                    }
                });

            var data = {
                action: 'hometrial_insert_to_cart',
                product_id: product_id,
                product_sku: '',
                quantity: product_qty,
                variation_id: variation_id,
                variations: item,
            };

            $( document.body ).trigger('adding_to_cart', [$this, data]);

            $.ajax({
                type: 'post',
                url:  HomeTrial.ajaxurl,
                data: data,

                beforeSend: function (response) {
                    $this.removeClass('added').addClass('loading');
                },

                complete: function (response) {
                    $this.addClass('added').removeClass('loading');
                },

                success: function (response) {
                    if ( response.error & response.product_url ) {
                        window.location = response.product_url;
                        return;
                    } else {
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $this]);
                    }
                },

            });

            return false;
        });

    }

    
    var hometrial_default_data = {
        price_html:'',
        image_html:'',
    };
    $(document).on('show_variation', '.hometrial_table .variations_form', function ( alldata, attributes, status ) {

        var target_row = alldata.target.closest('tr');

        // Get First image data
        if( typeof hometrial_default_data.price_html !== 'undefined' && hometrial_default_data.price_html.length === 0 ){
            hometrial_default_data.price_html = $(target_row).find('.hometrial-product-price').html();
            hometrial_default_data.image_html = $(target_row).find('.hometrial-product-image').html();
        }

        // Set variation data
        $(target_row).find('.hometrial-product-price').html( attributes.price_html );
        hometrial_variation_image_set( target_row, attributes.image );

        // reset data
        hometrial_variation_data_reset( target_row, hometrial_default_data );

    });

    // Reset data
    function hometrial_variation_data_reset( target_row, default_data ){
        $( target_row ).find('.reset_variations').on('click', function(e){
            $(target_row).find('.hometrial-product-price').html( default_data.price_html );
            $(target_row).find('.hometrial-product-image').html( default_data.image_html );
        });
    }

    // variation image set
    function hometrial_variation_image_set( target_row, image ){
        $(target_row).find('.hometrial-product-image img').wc_set_variation_attr('src',image.full_src);
        $(target_row).find('.hometrial-product-image img').wc_set_variation_attr('srcset',image.srcset);
        $(target_row).find('.hometrial-product-image img').wc_set_variation_attr('sizes',image.sizes);
    }


})(jQuery);