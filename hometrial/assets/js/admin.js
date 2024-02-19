;(function($){
"use strict";

    // Active settigns menu item
    if ( typeof HomeTrial.is_settings != "undefined" && HomeTrial.is_settings == 1 ){
        $('.toplevel_page_hometrial .wp-first-item').addClass('current');
    }

    // Save value
    hometrialConditionField( HomeTrial.option_data['btn_icon_type'], 'custom', '.button_custom_icon' );
    hometrialConditionField( HomeTrial.option_data['added_btn_icon_type'], 'custom', '.addedbutton_custom_icon' );
    hometrialConditionField( HomeTrial.option_data['shop_btn_position'], 'use_shortcode', '.depend_shop_btn_position_use_shortcode' );
    hometrialConditionField( HomeTrial.option_data['shop_btn_position'], 'custom_position', '.depend_shop_btn_position_custom_hook' );
    hometrialConditionField( HomeTrial.option_data['product_btn_position'], 'use_shortcode', '.depend_product_btn_position_use_shortcode' );
    hometrialConditionField( HomeTrial.option_data['product_btn_position'], 'custom_position', '.depend_product_btn_position_custom_hook' );
    hometrialConditionField( HomeTrial.option_data['button_style'], 'custom', '.button_custom_style' );
    hometrialConditionField( HomeTrial.option_data['table_style'], 'custom', '.table_custom_style' );
    hometrialConditionField( HomeTrial.option_data['enable_social_share'], 'on', '.depend_social_share_enable' );
    hometrialConditionField( HomeTrial.option_data['enable_login_limit'], 'on', '.depend_user_login_enable' );

    // After Select field change Condition Field
    hometrialChangeField( '.button_icon_type select', '.button_custom_icon', 'custom' );
    hometrialChangeField( '.addedbutton_icon_type select', '.addedbutton_custom_icon', 'custom' );
    hometrialChangeField( '.shop_btn_position select', '.depend_shop_btn_position_use_shortcode', 'use_shortcode' );
    hometrialChangeField( '.shop_btn_position select', '.depend_shop_btn_position_custom_hook', 'custom_position' );
    hometrialChangeField( '.product_btn_position select', '.depend_product_btn_position_use_shortcode', 'use_shortcode' );
    hometrialChangeField( '.product_btn_position select', '.depend_product_btn_position_custom_hook', 'custom_position' );
    hometrialChangeField( '.button_style select', '.button_custom_style', 'custom' );
    hometrialChangeField( '.table_style select', '.table_custom_style', 'custom' );
    hometrialChangeField( '.enable_social_share .checkbox', '.depend_social_share_enable', 'on', 'radio' );
    hometrialChangeField( '.enable_login_limit .checkbox', '.depend_user_login_enable', 'on', 'radio' );

    function hometrialChangeField( filedselector, selector, condition_value, fieldtype = 'select' ){
        $(filedselector).on('change',function(){
            var change_value = '';

            if( fieldtype === 'radio' ){
                if( $(this).is(":checked") ){
                    change_value = $(this).val();
                }
            }else{
                change_value = $(this).val();
            }

            hometrialConditionField( change_value, condition_value, selector );
        });
    }

    // Hide || Show
    function hometrialConditionField( value, condition_value, selector ){
        if( value === condition_value ){
            $(selector).show();
        }else{
            $(selector).hide();
        }
    }

})(jQuery);