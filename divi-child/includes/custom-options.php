<?php

// the settings page
function custom_settings_page() {
    ?>
	<div class="wrap">
		<h1>Custom Options</h1>
		<!-- <p>This information is used around the website, so changing these here will update them across the website.<br>Use the shortcode anywhere on the frontend to insert the information entered.
        </p> -->
		<form method="post" action="options.php">
			<?php
			settings_fields("customFields"); // this should match the group name used in register_setting()
			do_settings_sections("custom-options"); // settings page name
			submit_button();
			?>
		</form>
	</div>
	
	<?php
}

// add the settings page to somewhere in the menu, here Settings > Custom Options
function add_custom_menu_item(){
    add_options_page("Custom Options", "Custom Options", "manage_options", "custom-options", "custom_settings_page"); // add submenu page to Settings main menu: add_options_page("page_title", "menu_title", "capability", "menu-slug", "callback", position)
}
add_action("admin_menu", "add_custom_menu_item");

// the content of the page, ie the settings fields
// we need add_settings_section, add_settings_field and register_settings
function display_custom_options_fields() {

    add_settings_section("measurements", "Measurement Tooltips", null, "custom-options"); // add_settings_section("id", "title", "callback between heading and fields", "page")

    $measurements = array(
        "pa_lens-width", 
        "pa_bridge-width", 
        "pa_frame-temple-length", 
        "pa_frame-front-width", 
        "pa_lens-height",
    );
    
    foreach($measurements as $measurement){
        add_settings_field($measurement, wc_attribute_label($measurement), function($args){

            // echo '<label>Text<input style="margin-left: 5px" name="' . $args . '_text" placeholder="Short text" size="10" value=' . get_option($args . "_text") . '></label>';

            echo '<label style="margin-left: 10px">Icon<textarea style="margin-left: 5px" name="' . $args . '_icon" placeholder="Paste svg icon in here">' . get_option($args . "_icon") . '</textarea></label>';

            echo '<label style="margin-left: 10px">Details<textarea style="margin-left: 5px; resize: both" name="' . $args . '_details" placeholder="Enter details to be displayed in tooltip here">' . get_option($args . "_details") . '</textarea></label>';

        }, "custom-options", "measurements", $measurement); // add_settings_field("id", "title", "callback", "page", "section-slug", "args for callback as array()")
        register_setting("customFields", $measurement . "_icon"); // register_setting("option_group", "option_name should be same as <input name=.. "), needed to save in wpdb options table!
        register_setting("customFields", $measurement . "_details");
        // register_setting("customFields", $measurement . "_text");
    }
   

    // register_setting("measurements", "field_id"); // register_setting("option_group", "option_name should be same as <input name=.. "), needed to save in wpdb options table!

    // add_settings_section("section2", "Other Options", null, "custom-options");

    // add_settings_field("field_id", "Field 1 Title", function($args){
    //     echo 'This is field 1s content';
    // }, "custom-options", "section2", array());
}
add_action("admin_init", "display_custom_options_fields");
