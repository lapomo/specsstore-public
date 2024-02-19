<?php
namespace HomeTrial;
/**
 * Assets handlers class
 */
class Assets {

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
     * Class constructor
     */
    private function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    /**
     * All available scripts
     *
     * @return array
     */
    public function get_scripts() {
        return [
            'hometrial-admin' => [
                'src'     => HOMETRIAL_ASSETS . '/js/admin.js',
                'version' => HOMETRIAL_VERSION,
                'deps'    => [ 'jquery' ]
            ],
            'hometrial-frontend' => [
                'src'     => HOMETRIAL_ASSETS . '/js/frontend.js',
                'version' => HOMETRIAL_VERSION,
                'deps'    => [ 'jquery', 'wc-add-to-cart-variation' ]
            ],
        ];
    }

    /**
     * All available styles
     *
     * @return array
     */
    public function get_styles() {
        return [
            'hometrial-admin' => [
                'src'     => HOMETRIAL_ASSETS . '/css/admin.css',
                'version' => HOMETRIAL_VERSION,
            ],
            'hometrial-frontend' => [
                'src'     => HOMETRIAL_ASSETS . '/css/frontend.css',
                'version' => HOMETRIAL_VERSION,
            ],
        ];
    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    public function register_assets() {
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        foreach ( $scripts as $handle => $script ) {
            $deps = isset( $script['deps'] ) ? $script['deps'] : false;
            wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
        }

        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;
            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }

        // Inline CSS
        wp_add_inline_style( 'hometrial-frontend', $this->inline_style() );
        
        // Frontend Localize data
        $option_data = array(
            'after_added_to_cart' => hometrial_get_option( 'after_added_to_cart', 'hometrial_table_settings_tabs', 'on' ),
        );
        
        if( is_user_logged_in() &&  hometrial_get_option( 'enable_login_limit', 'hometrial_general_tabs', 'off' ) === 'on' ){
            $option_data['btn_limit_login_off'] = 'off';
        }else if( !is_user_logged_in() && hometrial_get_option( 'enable_login_limit', 'hometrial_general_tabs', 'off' ) === 'on' ){
            $option_data['btn_limit_login_off'] = 'on';
        }

        $localize_data = array(
            'ajaxurl'     => admin_url( 'admin-ajax.php' ),
            'option_data' => $option_data,
        );

        // Admin Localize data
        $setting_page = 0;
        if( isset( $_GET['page'] ) && $_GET['page'] == 'hometrial' ){
            $setting_page = 1;
        }
        $admin_option_data = array(
            'btn_icon_type'        => hometrial_get_option( 'button_icon_type', 'hometrial_style_settings_tabs', 'default' ),
            'added_btn_icon_type'  => hometrial_get_option( 'addedbutton_icon_type', 'hometrial_style_settings_tabs', 'default' ),
            'shop_btn_position'    => hometrial_get_option( 'shop_btn_position', 'hometrial_settings_tabs', 'after_cart_btn' ),
            'product_btn_position' => hometrial_get_option( 'product_btn_position', 'hometrial_settings_tabs', 'after_cart_btn' ),
            'button_style'         => hometrial_get_option( 'button_style', 'hometrial_style_settings_tabs', 'default' ),
            'table_style'          => hometrial_get_option( 'table_style', 'hometrial_style_settings_tabs', 'default' ),
            'enable_social_share'  => hometrial_get_option( 'enable_social_share','hometrial_table_settings_tabs','on' ),
            'enable_login_limit'   => hometrial_get_option( 'enable_login_limit','hometrial_general_tabs','off' ),
        );
        $admin_localize_data = array(
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'is_settings'=> $setting_page,
            'option_data'=> $admin_option_data,
        );

        wp_localize_script( 'hometrial-frontend', 'HomeTrial', $localize_data );
        wp_localize_script( 'hometrial-admin', 'HomeTrial', $admin_localize_data );
        
    }

    /**
     * [inline_style]
     * @return [CSS String]
     */
    public function inline_style(){

        $button_custom_css = $table_custom_css = '';

        // Button Custom Style
        if( 'custom' === hometrial_get_option( 'button_style', 'hometrial_style_settings_tabs', 'default' ) ){

            $btn_padding = hometrial_dimensions( 'button_custom_padding','hometrial_style_settings_tabs','padding' );
            $btn_margin  = hometrial_dimensions( 'button_custom_margin','hometrial_style_settings_tabs','margin' );
            $btn_border_radius = hometrial_dimensions( 'button_custom_border_radius','hometrial_style_settings_tabs','border-radius' );

            $btn_color    = hometrial_generate_css('button_color','hometrial_style_settings_tabs','color');
            $btn_bg_color = hometrial_generate_css('background_color','hometrial_style_settings_tabs','background-color');

            // Hover
            $btn_hover_color    = hometrial_generate_css('button_hover_color','hometrial_style_settings_tabs','color');
            $btn_hover_bg_color = hometrial_generate_css('hover_background_color','hometrial_style_settings_tabs','background-color');

            $button_custom_css = "
                .hometrial-button{
                    {$btn_padding}
                    {$btn_margin}
                    {$btn_color}
                    {$btn_bg_color}
                    {$btn_border_radius}
                }
                .hometrial-button:hover{
                    {$btn_hover_color}
                    {$btn_hover_bg_color}
                }
            ";
        }

        // Hometrialist table style
        if( 'custom' === hometrial_get_option( 'table_style', 'hometrial_style_settings_tabs', 'default' ) ){

            $heading_color    = hometrial_generate_css('table_heading_color','hometrial_style_settings_tabs','color');
            $heading_bg_color = hometrial_generate_css('table_heading_bg_color','hometrial_style_settings_tabs','background-color');
            $heading_border_color = hometrial_generate_css('table_heading_border_color','hometrial_style_settings_tabs','border-color');

            $border_color = hometrial_generate_css('table_border_color','hometrial_style_settings_tabs','border-color');

            // Add To cart Button
            $button_color = hometrial_generate_css('table_cart_button_color','hometrial_style_settings_tabs','color');
            $button_bg_color = hometrial_generate_css('table_cart_button_bg_color','hometrial_style_settings_tabs','background-color');
            $button_hover_color = hometrial_generate_css('table_cart_button_hover_color','hometrial_style_settings_tabs','color');
            $button_hover_bg_color = hometrial_generate_css('table_cart_button_hover_bg_color','hometrial_style_settings_tabs','background-color');


            $table_custom_css = "
                .hometrial-table-content table thead > tr{
                    {$heading_border_color}
                }
                .hometrial-table-content table thead > tr th{
                    {$heading_color}
                    {$heading_bg_color}
                }
                .hometrial-table-content table,.hometrial-table-content table tbody > tr{
                    {$border_color}
                }
            ";

            if( $button_color || $button_bg_color ){
                $table_custom_css .= "
                    .hometrial-table-content table .hometrial-addtocart{
                        {$button_color}
                        {$button_bg_color}
                    }
                ";
            }
            if( $button_hover_color || $button_hover_bg_color ){
                $table_custom_css .= "
                    .hometrial-table-content table .hometrial-addtocart:hover{
                        {$button_hover_color}
                        {$button_hover_bg_color}
                    }
                ";
            }

        }
        
        return $button_custom_css.$table_custom_css;

    }


}
