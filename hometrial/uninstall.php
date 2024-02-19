<?php
/*
 * Hometrial Uninstall plugin
 * Uninstalling Hometrial deletes tables, and options.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit; // Exit if accessed directly

if( ! isset( get_option('woolentor_others_tabs')['hometrialist'] ) ){

	include_once dirname( __FILE__ ) . '/includes/classes/Installer.php';

	function hometrial_uninstall(){

		if( !empty( get_option('hometrial_version', '') ) ){

			// Delete page created for this plugin
			$option_data = get_option( 'hometrial_table_settings_tabs' );
			if( isset($option_data['hometrialist_page'])){
				wp_delete_post( $option_data['hometrialist_page'], true );
			}

			// Option delete
			delete_option( 'hometrial_version' );
			delete_option( 'hometrial_settings_tabs' );
			delete_option( 'hometrial_table_settings_tabs' );
			delete_option( 'hometrial_style_settings_tabs' );

			// Delete table
			if( class_exists( '\HomeTrial\Installer' ) ){
				\HomeTrial\Installer::drop_tables();
			}

		}
		
	}
	
	hometrial_uninstall();
}