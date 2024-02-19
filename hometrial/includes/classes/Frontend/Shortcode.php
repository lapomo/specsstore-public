<?php
namespace HomeTrial\Frontend;
/**
 * Shortcode handler class
 */
class Shortcode {

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
     * Initializes the class
     */
    function __construct() {
        add_shortcode( 'hometrial_button', [ $this, 'button_shortcode' ] );
        add_shortcode( 'hometrial_table', [ $this, 'table_shortcode' ] );
        add_shortcode( 'hometrial_counter', [ $this, 'counter_shortcode' ] );
    }

    /**
     * [button_shortcode] Button Shortcode callable function
     * @param  [type] $atts 
     * @param  string $content
     * @return [HTML] 
     */
    public function button_shortcode( $atts, $content = '' ){
        wp_enqueue_style( 'hometrial-frontend' );
        wp_enqueue_script( 'hometrial-frontend' );

        global $product;
        $product_id = '';
        if ( $product && is_a( $product, 'WC_Product' ) ) {
            $product_id = $product->get_id();
        }

        $has_product = false;
        if ( Manage_Hometrialist::instance()->is_product_in_hometrialist( $product_id ) ) {
            $has_product = true;
        }
        
        $show_text = false;
        if(isset($atts['show_text']) && $atts['show_text'] === "true") {
            $show_text = true;
        }

        //my account url
        $myaccount_url =  get_permalink( get_option('woocommerce_myaccount_page_id') );

        // Fetch option data
        $button_text        = hometrial_get_option( 'button_text','hometrial_settings_tabs', 'Add to Home Trial' );
        $button_added_text  = hometrial_get_option( 'added_button_text','hometrial_settings_tabs', 'Remove from Home Trial' );
        $button_tooltip_text  = hometrial_get_option( 'tooltip_button_text','hometrial_settings_tabs', 'Home Trial' );
        $shop_page_btn_position     = hometrial_get_option( 'shop_btn_position', 'hometrial_settings_tabs', 'after_cart_btn' );
        $product_page_btn_position  = hometrial_get_option( 'product_btn_position', 'hometrial_settings_tabs', 'after_cart_btn' );
        $button_style               = hometrial_get_option( 'button_style', 'hometrial_style_settings_tabs', 'default' );
        $enable_login_limit = hometrial_get_option( 'enable_login_limit', 'hometrial_general_tabs', 'off' );
        $max_num_of_items = hometrial_get_option( 'max_num_of_items', 'hometrial_general_tabs', '4' );
        $max_num_reached_msg = hometrial_get_option( 'max_num_reached_msg', 'hometrial_general_tabs', 'Your Home Trial List is full. Click here to view it.' );

        
        // lapos edit
        $limit_reached = false;
        // $items_count = \HomeTrial\Manage_Data::instance()->item_count(get_current_user_id()); // this function is never used, but could be more efficient to get list count
        // $items_count = count( \HomeTrial\Frontend\Manage_Hometrialist::instance()->get_products_data() ); // this function is used to display items count on counter
        $items_count = count( \HomeTrial\Frontend\Manage_Hometrialist::instance()->get_hometrialist_products() ); // this is another function which is used inside get_products_data, dont know why this one is not used for the counter
        if ( $items_count >= $max_num_of_items ){
            $limit_reached = true;
        }
        //


        if ( !is_user_logged_in() && $enable_login_limit == 'on' ) {
            $button_text   = hometrial_get_option( 'logout_button','hometrial_general_tabs', 'Please login' );
            $page_url      = $myaccount_url;
            $has_product   = false;
        }else{
            $page_url = hometrial_get_page_url();
        }

        $button_class = array(
            'hometrial-button',
            'hometrial-shop-'.$shop_page_btn_position,
            'hometrial-product-'.$product_page_btn_position,
        );

        if( $button_style === 'themestyle' && $show_text ){
            $button_class[] = 'button';
            $button_class[] = 'alt';
        }

        if( $has_product ) {
            $button_class[] = 'hometrial-btn-exist';
        }

        if ( $limit_reached === true && $has_product === false) {
            $button_class[] = 'hometrial-btn-disabled';
        } else if ($limit_reached === false && $has_product === false ){
            $button_class[] = 'hometrial-btn';
        }


        $button_icon        = $this->icon_generate();
        $added_button_icon  = $this->icon_generate('added');
        
        // if( !empty( $button_text ) ){
        //     $button_text = '<span class="hometrial-btn-text">'.$button_text.'</span>';
        // }
        
        // if( !empty( $button_exist_text ) ){
        //     $button_exist_text = '<span class="hometrial-btn-text">'.$button_exist_text.'</span>';
        // }

        // if( !empty( $button_added_text ) ){
        //     $button_added_text = '<span class="hometrial-btn-text">'.$button_added_text.'</span>';
        // }

        // Shortcode atts
        $default_atts = array(
            'product_id'        => $product_id,
            'button_url'        => $page_url,
            'button_class'      => implode(' ', $button_class ),
            'button_icon'       => $button_icon,
            'button_text'       => $button_text,
            'button_added_icon' => $added_button_icon,
            'button_added_text' => $button_added_text,
            'button_tooltip_text' => $button_tooltip_text,
            'show_text'       => $show_text,
            'added'              => $has_product,
            'max_num_reached_msg' => $max_num_reached_msg,
        );
        $atts = shortcode_atts( $default_atts, $atts, $content );
        return Manage_Hometrialist::instance()->button_html( $atts );

    }

    /**
     * [table_shortcode] Table List Shortcode callable function
     * @param  [type] $atts
     * @param  string $content
     * @return [HTML] 
     */
    public function table_shortcode( $atts, $content = '' ){
        wp_enqueue_style( 'hometrial-frontend' );
        wp_enqueue_script( 'hometrial-frontend' );

        /* Fetch From option data */
        $empty_text = hometrial_get_option( 'empty_table_text', 'hometrial_table_settings_tabs' );

        /* Product and Field */
        $products   = Manage_Hometrialist::instance()->get_products_data();
        $fields     = Manage_Hometrialist::instance()->get_all_fields();

        $custom_heading = !empty( hometrial_get_option( 'table_heading', 'hometrial_table_settings_tabs' ) ) ? hometrial_get_option( 'table_heading', 'hometrial_table_settings_tabs' ) : array();
        $enable_login_limit = hometrial_get_option( 'enable_login_limit', 'hometrial_general_tabs', 'off' );

        $default_atts = array(
            'hometrial'    => Manage_Hometrialist::instance(),
            'products'     => $products,
            'fields'       => $fields,
            'heading_txt'  => $custom_heading,
            'empty_text'   => !empty( $empty_text ) ? $empty_text : '',
        );

        if ( !is_user_logged_in() && $enable_login_limit == 'on' ) {
            return do_shortcode('[woocommerce_my_account]');
        }else{
            $atts = shortcode_atts( $default_atts, $atts, $content );
            return Manage_Hometrialist::instance()->table_html( $atts );
        }
    }

    /**
     * HomeTriaList Counter Shortcode
     *
     * @param [array] $atts
     * @param string $content
     * @return void
     */
    public function counter_shortcode( $atts, $content = '' ){
        wp_enqueue_style( 'hometrial-frontend' );

        $enable_login_limit = hometrial_get_option( 'enable_login_limit', 'hometrial_general_tabs', 'off' );
        $myaccount_url =  get_permalink( get_option('woocommerce_myaccount_page_id') );

        $products   = Manage_Hometrialist::instance()->get_products_data();
        if ( !is_user_logged_in() && $enable_login_limit == 'on' ) {
            $button_text   = hometrial_get_option( 'logout_button','hometrial_general_tabs', 'Please login' );
            $page_url      = $myaccount_url;
            $has_product   = false;
        }else{
            $button_text = hometrial_get_option( 'button_text','hometrial_settings_tabs', 'Hometrialist' );
            $page_url = hometrial_get_page_url();
        }

        $default_atts = array(
            'products'      => $products,
            'item_count'    => count($products),
            'page_url'      => $page_url,
            'text'          => '',
        );

        $atts = shortcode_atts( $default_atts, $atts, $content );
        return Manage_Hometrialist::instance()->count_html( $atts );

    }

    /**
     * [icon_generate]
     * @param  string $type
     * @return [HTML]
     */
    public function icon_generate( $type = '' ){

        $default_icon   = hometrial_icon_list('default');
        $default_loader = '<span class="hometrial-loader">'.hometrial_icon_list('loading').'</span>';
        
        $button_icon = '';
        $button_text = ( $type === 'added' ) ? hometrial_get_option( 'added_button_text','hometrial_settings_tabs', 'Hometrialist' ) : hometrial_get_option( 'button_text','hometrial_settings_tabs', 'Hometrialist' );
        $button_icon_type  = hometrial_get_option( $type.'button_icon_type', 'hometrial_style_settings_tabs', 'default' );

        if( $button_icon_type === 'custom' ){
            $button_icon = hometrial_get_option( $type.'button_custom_icon','hometrial_style_settings_tabs', '' );
        }else{
            if( $button_icon_type !== 'none' ){
                return $default_icon;
            }
        }

        if( !empty( $button_icon ) ){
            $button_icon = '<img src="'.esc_url( $button_icon ).'" alt="'.esc_attr( $button_text ).'">';
        }

        return $button_icon.$default_loader;

    }


}