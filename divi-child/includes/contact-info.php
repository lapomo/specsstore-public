<?php

/*===================================================================================
* Add global options
* =================================================================================*/

/**
 *
 * The page content surrounding the settings fields. Usually you use this to instruct non-techy people what to do.
 *
 */
function theme_settings_page(){ 
	?>
	<div class="wrap">
		<h1>Contact Info</h1>
		<p>This information is used around the website, so changing these here will update them across the website.<br>Use the shortcode anywhere on the frontend to insert the information entered.
        </p>
		<form method="post" action="options.php">
			<?php
			settings_fields("section");
			do_settings_sections("theme-options");
			submit_button();
			?>
		</form>
        <script>
        jQuery('.populate_option_update').on('click', function(){
            var option_value = jQuery(this).parent().parent().find("input").val();
            var option_names = jQuery(this).data('pop-options');
            var site = "<?php echo site_url(); ?>";

            jQuery.ajax({ 
            method: "POST",
            data:{
                        action:'populate_option_update',
                        options: option_names,
                        value: option_value,
                    },
                    url: site+"/wp-admin/admin-ajax.php",
                    success:function(result){
                        console.log(result);
                    },
                    error:function(error){
                        console.log(error);
                    }
            });
        });
        </script>
 
	</div>
	
	<?php }

function populate_option_update(){
    $option_names = $_POST['options'];
    $option_value = $_POST['value'];
    $success = array();
    foreach($option_names as $option_name){
        $success[] = update_option($option_name, $option_value);
    }
    return $success;
}

add_action('wp_ajax_populate_option_update', 'populate_option_update');
add_action('wp_ajax_nopriv_populate_option_update', 'populate_option_update');

/**
 *
 * Here you tell WP what to enqueue into the <form> area. You need:
 *
 * 1. add_settings_section
 * 2. add_settings_field
 * 3. register_setting
 *
 */

$global_contact_fields = array(
    // id => array( title, type, array( other options to populate to ) )
    'co_name'       =>  array('Company Name', 'text', array()), 
    'co_address1'   =>  array('Company address line 1', 'text', array('woo_address1')), // this is just an option for show, it's not used ever anywhere
    'co_address2'   =>  array('Company address line 2', 'text', array()), 
    'co_city'       =>  array('Company city', 'text', array()), 
    'co_country'    =>  array('Company country', 'text', array()), 
    'co_main_phone' =>  array('Company main phone', 'tel', array()), 
    're_address1'   =>  array('Return address line 1', 'text', array()), 
    're_address2'   =>  array('Return address line 2', 'text', array()), 
    're_city'       =>  array('Return City', 'text', array()), 
    're_country'    =>  array('Return country', 'text', array()), 
    'main_phone'    =>  array('Main Phone', 'tel', array()), 
    'cs_phone'      =>  array('Customer Support phone', 'tel', array()), 
    'sale_phone'    =>  array('Sales phone', 'tel', array()), 
    'admin_email'   =>  array('Admin email address', 'email', array()),
    'cs_email'      =>  array('Customer Support email address', 'email', array()),
    'sale_email'    =>  array('Sales email address', 'email', array()),
);

function display_custom_info_fields(){
	
	add_settings_section("section", "Company Information", null, "theme-options");

    global $global_contact_fields;

    foreach($global_contact_fields as $id => $details){
        add_settings_field($id, 
            $details[0], 
            function($args){
                $id = $args[0]; 
                $title = $args[1][0];
                $type = $args[1][1];
                $pop_options = $args[1][2];

	            echo '<input type="' . $type . '" name="' . $id . '" placeholder="Enter ' . $title . '" value="' . get_option(" $id ") . '" size="35">';

                echo '&emsp;<span>[ls_global_contact field="' . $id . '"]';
                if($pop_options){
                    echo '<br><a class="populate_option_update" style="margin-top: 10px; cursor:pointer;" data-pop-options=' . json_encode($pop_options) . '>Populate to other settings..</a>';
                    echo ' ( ';
                    echo json_encode($pop_options);
                    echo ' )';
                }

            
        }, "theme-options", "section", array($id, $details));
        register_setting("section", $id);

    }
}
add_action("admin_init", "display_custom_info_fields");

/**
 *
 * Tie it all together by adding the settings page to wherever you like. For this example it will appear
 * in Settings > Contact Info
 *
 */

function add_custom_info_menu_item(){
	
	add_options_page("Contact Info", "Contact Info", "manage_options", "contact-info", "theme_settings_page");
	
}
add_action("admin_menu", "add_custom_info_menu_item");

// add placeholders to woocoomerce email templates 
add_filter( 'woocommerce_email_format_string' , function($string, $email){

    global $global_contact_fields;
    $placeholder = array();
    $value = array();

    foreach($global_contact_fields as $id => $details){
        $placeholder[] = '[ls_global_contact field="' . $id . '"]';
        $value[] = get_option($id);
    }

    // $placeholder = '[ls_global_contact field="' . $id . '"]';
    // $order = $email->object;
    // // $value = $order->get_meta($meta_key) ? $order->get_meta($meta_key) : ''; // Get the value from order meta
    // $value = get_option($id);

    return str_replace( $placeholder, $value, $string );
}, 10, 2 );