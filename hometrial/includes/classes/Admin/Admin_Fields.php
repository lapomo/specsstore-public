<?php
namespace HomeTrial\Admin;
/**
 * Admin Page Fields handlers class
 */
class Admin_Fields {

    private $settings_api;

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Admin]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct() {
        $this->settings_api = new Settings_Api();
        add_action( 'admin_init', [ $this, 'admin_init' ] );
    }

    public function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->fields_settings() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    // Options page Section register
    public function get_settings_sections() {
        $sections = array(

            array(
                'id'    => 'hometrial_general_tabs',
                'title' => esc_html__( 'General Settings', 'hometrial' )
            ),

            array(
                'id'    => 'hometrial_settings_tabs',
                'title' => esc_html__( 'Button Settings', 'hometrial' )
            ),
            
            array(
                'id'    => 'hometrial_table_settings_tabs',
                'title' => esc_html__( 'Table Settings', 'hometrial' )
            ),
            
            array(
                'id'    => 'hometrial_style_settings_tabs',
                'title' => esc_html__( 'Style Settings', 'hometrial' )
            ),

        );
        return $sections;
    }

    // Options page field register
    protected function fields_settings() {

        $settings_fields = array(

            'hometrial_general_tabs' => array(
                array(
                    'name'      => 'enable_login_limit',
                    'label'     => __( 'Limit HomeTrial Use', 'hometrial' ),
                    'type'      => 'checkbox',
                    'default'   => 'off',
                    'desc'      => esc_html__( 'Enable this option to allow only the logged-in users to use the Hometrial feature.', 'hometrial' ),
                ),

                array(
                    'name'      => 'logout_button',
                    'label'     => __( 'HomeTrial Icon Tooltip Text', 'hometrial' ),
                    'desc'      => __( 'Enter a text for the tooltip that will be shown when someone hover over the Hometrial icon.', 'hometrial' ),
                    'type'      => 'text',
                    'default'   => __( 'Please login', 'hometrial' ),
                     'class'    => 'depend_user_login_enable'
                ),

                array(
                    'name'      => 'max_num_of_items',
                    'label'     => __( 'Max number of items', 'hometrial' ),
                    'desc'      => __( 'Enter a number for how many products can be added to the HomeTrial List.', 'hometrial' ),
                    'type'      => 'number',
                    'default'   => __( '4', 'hometrial' ),
                ),

                array(
                    'name'      => 'max_num_reached_msg',
                    'label'     => __( 'Max Number reached Message', 'hometrial' ),
                    'desc'      => __( 'Enter a text that will be shown to the user, when he tries to add more products to the HomeTrial List than allowed.', 'hometrial' ),
                    'type'      => 'text',
                    'default'   => __( 'Your Home Trial Basket is full. Click here to view it.', 'hometrial' ),
                ),

            ),

            'hometrial_settings_tabs' => array(

                array(
                    'name'  => 'btn_show_shoppage',
                    'label'  => __( 'Show button in product list', 'hometrial' ),
                    'type'  => 'checkbox',
                    'default' => 'off',
                ),

                array(
                    'name'  => 'btn_show_productpage',
                    'label'  => __( 'Show button in single product page', 'hometrial' ),
                    'type'  => 'checkbox',
                    'default' => 'on',
                ),

                array(
                    'name'    => 'shop_btn_position',
                    'label'   => __( 'Shop page button position', 'hometrial' ),
                    'desc'    => __( 'You can manage hometrialist button position in product list page.', 'hometrial' ),
                    'type'    => 'select',
                    'default' => 'after_cart_btn',
                    'options' => [
                        'before_cart_btn' => __( 'Before Add To Cart', 'hometrial' ),
                        'after_cart_btn'  => __( 'After Add To Cart', 'hometrial' ),
                        'top_thumbnail'   => __( 'Top On Image', 'hometrial' ),
                        'use_shortcode'   => __( 'Use Shortcode', 'hometrial' ),
                        'custom_position' => __( 'Custom Position', 'hometrial' ),
                    ],
                ),

                array(
                    'name'    => 'shop_use_shortcode_message',
                    'headding'=> wp_kses_post('<code>[hometrial_button]</code> Use this shortcode into your theme/child theme to place the hometrialist button.'),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'depend_shop_btn_position_use_shortcode element_section_title_area message-info',
                ),

                array(
                    'name'    => 'shop_custom_hook_message',
                    'headding'=> esc_html__( 'Some themes remove the above positions. In that case, custom position is useful. Here you can place the custom/default hook name & priority to inject & adjust the hometrialist button for the product loop.', 'hometrial' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'depend_shop_btn_position_custom_hook element_section_title_area message-info',
                ),

                array(
                    'name'        => 'shop_custom_hook_name',
                    'label'       => __( 'Hook name', 'hometrial' ),
                    'desc'        => __( 'e.g: woocommerce_after_shop_loop_item_title', 'hometrial' ),
                    'type'        => 'text',
                    'class'       => 'depend_shop_btn_position_custom_hook'
                ),

                array(
                    'name'        => 'shop_custom_hook_priority',
                    'label'       => __( 'Hook priority', 'hometrial' ),
                    'desc'        => __( 'Default: 10', 'hometrial' ),
                    'type'        => 'text',
                    'class'       => 'depend_shop_btn_position_custom_hook'
                ),

                array(
                    'name'    => 'product_btn_position',
                    'label'   => __( 'Product page button position', 'hometrial' ),
                    'desc'    => __( 'You can manage hometrialist button position in single product page.', 'hometrial' ),
                    'type'    => 'select',
                    'default' => 'after_cart_btn',
                    'options' => [
                        'before_cart_btn' => __( 'Before Add To Cart', 'hometrial' ),
                        'after_cart_btn'  => __( 'After Add To Cart', 'hometrial' ),
                        'after_thumbnail' => __( 'After Image', 'hometrial' ),
                        'after_summary'   => __( 'After Summary', 'hometrial' ),
                        'use_shortcode'   => __( 'Use Shortcode', 'hometrial' ),
                        'custom_position' => __( 'Custom Position', 'hometrial' ),
                    ],
                ),

                array(
                    'name'    => 'product_use_shortcode_message',
                    'headding'=> wp_kses_post('<code>[hometrial_button]</code> Use this shortcode into your theme/child theme to place the hometrialist button.'),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'depend_product_btn_position_use_shortcode element_section_title_area message-info',
                ),

                array(
                    'name'    => 'product_custom_hook_message',
                    'headding'=> esc_html__( 'Some themes remove the above positions. In that case, custom position is useful. Here you can place the custom/default hook name & priority to inject & adjust the hometrialist button for the single product page.', 'hometrial' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'depend_product_btn_position_custom_hook element_section_title_area message-info',
                ),

                array(
                    'name'        => 'product_custom_hook_name',
                    'label'       => __( 'Hook name', 'hometrial' ),
                    'desc'        => __( 'e.g: woocommerce_after_single_product_summary', 'hometrial' ),
                    'type'        => 'text',
                    'class'       => 'depend_product_btn_position_custom_hook'
                ),

                array(
                    'name'        => 'product_custom_hook_priority',
                    'label'       => __( 'Hook priority', 'hometrial' ),
                    'desc'        => __( 'Default: 10', 'hometrial' ),
                    'type'        => 'text',
                    'class'       => 'depend_product_btn_position_custom_hook'
                ),

                array(
                    'name'        => 'button_text',
                    'label'       => __( 'Button Text', 'hometrial' ),
                    'desc'        => __( 'Enter your hometrial button text.', 'hometrial' ),
                    'type'        => 'text',
                    'default'     => __( 'Add to Home Trial', 'hometrial' ),
                    'placeholder' => __( 'Add to Home Trial', 'hometrial' ),
                ),

                array(
                    'name'        => 'added_button_text',
                    'label'       => __( 'Product added text', 'hometrial' ),
                    'desc'        => __( 'Enter the product added text.', 'hometrial' ),
                    'type'        => 'text',
                    'default'     => __( 'Remove from Home Trial', 'hometrial' ),
                    'placeholder' => __( 'Remove from Home Trial', 'hometrial' ),
                ),

                array(
                    'name'        => 'tooltip_button_text',
                    'label'       => __( 'Tooltip Text', 'hometrial' ),
                    'desc'        => __( 'Enter your hometrial tooltip text for the button on shop pages', 'hometrial' ),
                    'type'        => 'text',
                    'default'     => __( 'Home Trial', 'hometrial' ),
                    'placeholder' => __( 'Home Trial', 'hometrial' ),
                ),

            ),

            'hometrial_table_settings_tabs' => array(

                array(
                    'name'    => 'hometrialist_page',
                    'label'   => __( 'Hometrialist page', 'hometrial' ),
                    'type'    => 'select',
                    'default' => '0',
                    'options' => hometrial_get_post_list(),
                    'desc'    => wp_kses_post('Select a hometrialist page for hometrialist table. It should contain the shortcode <code>[hometrial_table]</code>'),
                ),

                array(
                    'name'  => 'after_added_to_cart',
                    'label'  => __( 'Remove from the "Hometrialist" after adding to the cart.', 'hometrial' ),
                    'type'  => 'checkbox',
                    'default' => 'on',
                ),

                array(
                    'name' => 'show_fields',
                    'label' => __('Show fields in table', 'hometrial'),
                    'desc' => __('Choose which fields should be presented on the product compare page with table.', 'hometrial'),
                    'type' => 'multicheckshort',
                    'options' => hometrial_get_available_attributes(),
                    'default' => [
                        'remove'        => esc_html__( 'Remove', 'hometrial' ),
                        'image'         => esc_html__( 'Image', 'hometrial' ),
                        'title'         => esc_html__( 'Title', 'hometrial' ),
                        'price'         => esc_html__( 'Price', 'hometrial' ),
                        'quantity'      => esc_html__( 'Quantity', 'hometrial' ),
                        'add_to_cart'   => esc_html__( 'Add To Cart', 'hometrial' ),
                    ],
                ),

                array(
                    'name'    => 'table_heading',
                    'label'   => __( 'Table heading text', 'hometrial' ),
                    'desc'    => __( 'You can change table heading text from here.', 'hometrial' ),
                    'type'    => 'multitext',
                    'options' => hometrial_table_heading()
                ),

                array(
                    'name' => 'empty_table_text',
                    'label' => __('Empty table text', 'hometrial'),
                    'desc' => __('Text will be displayed if the user doesn\'t add any product to  the hometrialist.', 'hometrial'),
                    'type' => 'textarea'
                ),

                array(
                    'name'        => 'image_size',
                    'label'       => __( 'Image size', 'hometrial' ),
                    'desc'        => __( 'Enter your required image size.', 'hometrial' ),
                    'type'        => 'multitext',
                    'options'     =>[
                        'width'  => esc_html__( 'Width', 'hometrial' ),
                        'height' => esc_html__( 'Height', 'hometrial' ),
                    ],
                    'default' => [
                        'width'   => 80,
                        'height'  => 80,
                    ],
                ),

                array(
                    'name'  => 'hard_crop',
                    'label'  => __( 'Image Hard Crop', 'hometrial' ),
                    'type'  => 'checkbox',
                    'default' => 'on',
                ),

                array(
                    'name'    => 'social_share_button_area_title',
                    'headding'=> esc_html__( 'Social share button', 'hometrial' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'element_section_title_area',
                ),

                array(
                    'name'  => 'enable_social_share',
                    'label'  => esc_html__( 'Enable social share button', 'hometrial' ),
                    'type'  => 'checkbox',
                    'default' => 'on',
                    'desc'    => esc_html__( 'Enable social share button.', 'hometrial' ),
                ),

                array(
                    'name'        => 'social_share_button_title',
                    'label'       => esc_html__( 'Social share button title', 'hometrial' ),
                    'desc'        => esc_html__( 'Enter your social share button title.', 'hometrial' ),
                    'type'        => 'text',
                    'default'     => esc_html__( 'Share:', 'hometrial' ),
                    'placeholder' => esc_html__( 'Share', 'hometrial' ),
                    'class' => 'depend_social_share_enable'
                ),

                array(
                    'name' => 'social_share_buttons',
                    'label' => esc_html__('Enable share buttons', 'hometrial'),
                    'desc'    => esc_html__( 'You can manage your social share buttons.', 'hometrial' ),
                    'type' => 'multicheckshort',
                    'options' => [
                        'facebook'      => esc_html__( 'Facebook', 'hometrial' ),
                        'twitter'       => esc_html__( 'Twitter', 'hometrial' ),
                        'pinterest'     => esc_html__( 'Pinterest', 'hometrial' ),
                        'linkedin'      => esc_html__( 'Linkedin', 'hometrial' ),
                        'email'         => esc_html__( 'Email', 'hometrial' ),
                        'reddit'        => esc_html__( 'Reddit', 'hometrial' ),
                        'telegram'      => esc_html__( 'Telegram', 'hometrial' ),
                        'odnoklassniki' => esc_html__( 'Odnoklassniki', 'hometrial' ),
                        'whatsapp'      => esc_html__( 'WhatsApp', 'hometrial' ),
                        'vk'            => esc_html__( 'VK', 'hometrial' ),
                    ],
                    'default' => [
                        'facebook'   => esc_html__( 'Facebook', 'hometrial' ),
                        'twitter'    => esc_html__( 'Twitter', 'hometrial' ),
                        'pinterest'  => esc_html__( 'Pinterest', 'hometrial' ),
                        'linkedin'   => esc_html__( 'Linkedin', 'hometrial' ),
                        'telegram'   => esc_html__( 'Telegram', 'hometrial' ),
                    ],
                    'class' => 'depend_social_share_enable'
                ),

            ),

            'hometrial_style_settings_tabs' => array(

                array(
                    'name'    => 'button_style',
                    'label'   => __( 'Button style', 'hometrial' ),
                    'desc'    => __( 'Choose a style for the hometrialist button from here.', 'hometrial' ),
                    'type'    => 'select',
                    'default' => 'default',
                    'options' => [
                        'default'     => esc_html__( 'Default style', 'hometrial' ),
                        'themestyle'  => esc_html__( 'Theme style', 'hometrial' ),
                        'custom'      => esc_html__( 'Custom style', 'hometrial' ),
                    ]
                ),

                array(
                    'name'    => 'button_icon_type',
                    'label'   => __( 'Button icon type', 'hometrial' ),
                    'desc'    => __( 'Choose an icon for the hometrialist button from here.', 'hometrial' ),
                    'type'    => 'select',
                    'default' => 'default',
                    'options' => [
                        'none'     => esc_html__( 'None', 'hometrial' ),
                        'default'  => esc_html__( 'Default icon', 'hometrial' ),
                        'custom'   => esc_html__( 'Custom icon', 'hometrial' ),
                    ]
                ),

                array(
                    'name'    => 'button_custom_icon',
                    'label'   => __( 'Button custom icon', 'hometrial' ),
                    'type'    => 'image_upload',
                    'options' => [
                        'button_label' => esc_html__( 'Upload', 'hometrial' ),   
                        'button_remove_label' => esc_html__( 'Remove', 'hometrial' ),   
                    ],
                ),

                array(
                    'name'    => 'addedbutton_icon_type',
                    'label'   => __( 'Added Button icon type', 'hometrial' ),
                    'desc'    => __( 'Choose an icon for the hometrialist button from here.', 'hometrial' ),
                    'type'    => 'select',
                    'default' => 'default',
                    'options' => [
                        'none'     => esc_html__( 'None', 'hometrial' ),
                        'default'  => esc_html__( 'Default icon', 'hometrial' ),
                        'custom'   => esc_html__( 'Custom icon', 'hometrial' ),
                    ]
                ),

                array(
                    'name'    => 'addedbutton_custom_icon',
                    'label'   => __( 'Added Button custom icon', 'hometrial' ),
                    'type'    => 'image_upload',
                    'options' => [
                        'button_label' => esc_html__( 'Upload', 'hometrial' ),   
                        'button_remove_label' => esc_html__( 'Remove', 'hometrial' ),   
                    ],
                ),

                array(
                    'name'    => 'table_style',
                    'label'   => __( 'Table style', 'hometrial' ),
                    'desc'    => __( 'Choose a style for the hometrialist table here.', 'hometrial' ),
                    'type'    => 'select',
                    'default' => 'default',
                    'options' => [
                        'default' => esc_html__( 'Default style', 'hometrial' ),
                        'custom'  => esc_html__( 'Custom style', 'hometrial' ),
                    ]
                ),

                array(
                    'name'    => 'button_custom_style_title',
                    'headding'=> __( 'Button custom style', 'hometrial' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'button_custom_style element_section_title_area',
                ),

                array(
                    'name'  => 'button_color',
                    'label' => esc_html__( 'Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the color of the button.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'button_custom_style',
                ),

                array(
                    'name'  => 'button_hover_color',
                    'label' => esc_html__( 'Hover Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the hover color of the button.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'button_custom_style',
                ),

                array(
                    'name'  => 'background_color',
                    'label' => esc_html__( 'Background Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the background color of the button.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'button_custom_style',
                ),

                array(
                    'name'  => 'hover_background_color',
                    'label' => esc_html__( 'Hover Background Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the hover background color of the button.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'button_custom_style',
                ),

                array(
                    'name'    => 'button_custom_padding',
                    'label'   => __( 'Padding', 'hometrial' ),
                    'type'    => 'dimensions',
                    'options' => [
                        'top'   => esc_html__( 'Top', 'hometrial' ),   
                        'right' => esc_html__( 'Right', 'hometrial' ),   
                        'bottom'=> esc_html__( 'Bottom', 'hometrial' ),   
                        'left'  => esc_html__( 'Left', 'hometrial' ),
                        'unit'  => esc_html__( 'Unit', 'hometrial' ),
                    ],
                    'class' => 'button_custom_style',
                ),

                array(
                    'name'    => 'button_custom_margin',
                    'label'   => __( 'Margin', 'hometrial' ),
                    'type'    => 'dimensions',
                    'options' => [
                        'top'   => esc_html__( 'Top', 'hometrial' ),   
                        'right' => esc_html__( 'Right', 'hometrial' ),   
                        'bottom'=> esc_html__( 'Bottom', 'hometrial' ),   
                        'left'  => esc_html__( 'Left', 'hometrial' ),
                        'unit'  => esc_html__( 'Unit', 'hometrial' ),
                    ],
                    'class' => 'button_custom_style',
                ),

                array(
                    'name'    => 'button_custom_border_radius',
                    'label'   => __( 'Border Radius', 'hometrial' ),
                    'type'    => 'dimensions',
                    'options' => [
                        'top'   => esc_html__( 'Top', 'hometrial' ),   
                        'right' => esc_html__( 'Right', 'hometrial' ),   
                        'bottom'=> esc_html__( 'Bottom', 'hometrial' ),   
                        'left'  => esc_html__( 'Left', 'hometrial' ),
                        'unit'  => esc_html__( 'Unit', 'hometrial' ),
                    ],
                    'class' => 'button_custom_style',
                ),

                array(
                    'name'    => 'table_custom_style_title',
                    'headding'=> __( 'Table custom style', 'hometrial' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'table_custom_style element_section_title_area',
                ),

                array(
                    'name'  => 'table_heading_color',
                    'label' => esc_html__( 'Heading Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the heading color of the hometrialist table.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'table_custom_style',
                ),

                array(
                    'name'  => 'table_heading_bg_color',
                    'label' => esc_html__( 'Heading Background Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the heading background color of the hometrialist table.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'table_custom_style',
                ),
                array(
                    'name'  => 'table_heading_border_color',
                    'label' => esc_html__( 'Heading Border Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the heading border color of the hometrialist table.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'table_custom_style',
                ),

                array(
                    'name'  => 'table_border_color',
                    'label' => esc_html__( 'Border Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the border color of the hometrialist table.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'table_custom_style',
                ),

                array(
                    'name'    => 'table_custom_style_add_to_cart',
                    'headding'=> __( 'Add To Cart Button style', 'hometrial' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'table_custom_style element_section_title_area',
                ),

                array(
                    'name'  => 'table_cart_button_color',
                    'label' => esc_html__( 'Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the add to cart button color of the hometrialist table.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'table_custom_style',
                ),
                array(
                    'name'  => 'table_cart_button_bg_color',
                    'label' => esc_html__( 'Background Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the add to cart button background color of the hometrialist table.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'table_custom_style',
                ),
                array(
                    'name'  => 'table_cart_button_hover_color',
                    'label' => esc_html__( 'Hover Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the add to cart button hover color of the hometrialist table.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'table_custom_style',
                ),
                array(
                    'name'  => 'table_cart_button_hover_bg_color',
                    'label' => esc_html__( 'Hover Background Color', 'hometrial' ),
                    'desc'  => wp_kses_post( 'Set the add to cart button hover background color of the hometrialist table.', 'hometrial' ),
                    'type'  => 'color',
                    'class' => 'table_custom_style',
                ),

            ),

        );
        
        return $settings_fields;
    }

    public function plugin_page() {
        echo '<div class="wrap">';
            echo '<h2>'.esc_html__( 'HomeTrial Settings','hometrial' ).'</h2>';
            $this->save_message();
            $this->settings_api->show_navigation();
            $this->settings_api->show_forms();
        echo '</div>';
    }

    public function save_message() {
        if( isset( $_GET['settings-updated'] ) ) {
            ?>
                <div class="updated notice is-dismissible"> 
                    <p><strong><?php esc_html_e('Successfully Settings Saved.', 'hometrial') ?></strong></p>
                </div>
            <?php
        }
    }

}