<?php
/**
 * Plugin Name: Home Trial
 * Description: WooCommerce Product Home Trial Plugin - based on WishSuite 1.3.0 by https://hasthemes.com/
 * Plugin URI: 
 * Author: Lapo
 * Author URI: https://laposolutions.de/
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hometrial
 * Domain Path: /languages
 * WC tested up to: 6.6.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Main Class
 */
final class HomeTrial_Base{

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Base]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * [__construct] Class Constructor
     */
    private function __construct(){
        $this->define_constants();
        $this->includes();
        register_activation_hook( HOMETRIAL_FILE, [ $this, 'activate' ] );
        if( empty( get_option('hometrial_version', '') ) ){
            $this->activate();
        }
        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'HOMETRIAL_VERSION', self::version );
        define( 'HOMETRIAL_FILE', __FILE__ );
        define( 'HOMETRIAL_PATH', __DIR__ );
        define( 'HOMETRIAL_URL', plugins_url( '', HOMETRIAL_FILE ) );
        define( 'HOMETRIAL_DIR', plugin_dir_path( HOMETRIAL_FILE ) );
        define( 'HOMETRIAL_ASSETS', HOMETRIAL_URL . '/assets' );
        define( 'HOMETRIAL_BASE', plugin_basename( HOMETRIAL_FILE ) );
    }

    /**
     * [i18n] Load text domain
     * @return [void]
     */
    public function i18n() {
        load_plugin_textdomain( 'hometrial', false, dirname( plugin_basename( HOMETRIAL_FILE ) ) . '/languages/' );
    }

    /**
     * [includes] Load file
     * @return [void]
     */
    public function includes(){
        require_once HOMETRIAL_PATH . '/vendor/autoload.php';
        if ( ! function_exists('is_plugin_active') ){ include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        HomeTrial\Assets::instance();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            HomeTrial\Ajax::instance();
        }

        if ( is_admin() ) {
            $this->admin_notices();
            HomeTrial\Admin::instance();
        }
        HomeTrial\Frontend::instance();

        // add image size
        $this->set_image_size();

        // let's filter the woocommerce image size
        add_filter( 'woocommerce_get_image_size_hometrial-image', [ $this, 'wc_image_filter_size' ], 10, 1 );
        

    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installer = new HomeTrial\Installer();
        $installer->run();
    }

    /**
     * Admin Notices
     * @return void
     */
    public function admin_notices() {
        $notice = new HomeTrial\Admin\Notices();
        $notice->notice();
    }

    /**
     * [set_image_size] Set Image Size
     */
    public function set_image_size(){

        $image_dimention = hometrial_get_option( 'image_size', 'hometrial_table_settings_tabs', array( 'width'=>80,'height'=>80 ) );
        if( isset( $image_dimention ) && is_array( $image_dimention ) ){
            $hard_crop = !empty( hometrial_get_option( 'hard_crop', 'hometrial_table_settings_tabs' ) ) ? true : false;
            add_image_size( 'hometrial-image', absint( $image_dimention['width'] ), absint( $image_dimention['height'] ), $hard_crop );
        }

    }

    /**
     * [wc_image_filter_size]
     * @return [array]
     */
    public function wc_image_filter_size(){

        $image_dimention = hometrial_get_option( 'image_size', 'hometrial_table_settings_tabs', array( 'width'=>80,'height'=>80 ) );
        $hard_crop = !empty( hometrial_get_option( 'hard_crop', 'hometrial_table_settings_tabs' ) ) ? true : false;

        if( isset( $image_dimention ) && is_array( $image_dimention ) ){
            return array(
                'width'  => isset( $image_dimention['width'] ) ? absint( $image_dimention['width'] ) : 80,
                'height' => isset( $image_dimention['height'] ) ? absint( $image_dimention['height'] ) : 80,
                'crop'   => isset( $hard_crop ) ? 1 : 0,
            );
        }
        
    }

}

/**
 * Initializes the main plugin
 *
 * @return HomeTrial
 */
function HomeTrial() {
    if( ! class_exists('Woolentor_HomeTrial_Base') ){
        return HomeTrial_Base::instance();
    }
}

// Get the plugin running.
HomeTrial();
