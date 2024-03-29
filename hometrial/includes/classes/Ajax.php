<?php
namespace HomeTrial;
/**
 * Ajax handlers class
 */
class Ajax {

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Ajax]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Initialize the class
     */
    private function __construct() {

        // Add Ajax Callback
        add_action( 'wp_ajax_hometrial_add_to_list', [ $this, 'add_to_hometrialist' ] );
        add_action( 'wp_ajax_nopriv_hometrial_add_to_list', [ $this, 'add_to_hometrialist' ] );

        // Remove Ajax Callback
        add_action( 'wp_ajax_hometrial_remove_from_list', [ $this, 'remove_hometrialist' ] );
        add_action( 'wp_ajax_nopriv_hometrial_remove_from_list', [ $this, 'remove_hometrialist' ] );

        // Variation Quick cart Form Ajax Callback
        add_action( 'wp_ajax_hometrial_quick_variation_form', [ $this, 'variation_form_html' ] );
        add_action( 'wp_ajax_nopriv_hometrial_quick_variation_form', [ $this, 'variation_form_html' ] );

        // For Add to cart
        add_action( 'wp_ajax_hometrial_insert_to_cart', [ $this, 'insert_to_cart' ] );
        add_action( 'wp_ajax_nopriv_hometrial_insert_to_cart', [ $this, 'insert_to_cart' ] );

    }

    /**
     * [add_to_hometrialist] Product add ajax callback
     */
    public function add_to_hometrialist(){
        $id = sanitize_text_field( $_GET['id'] );
        $inserted = \HomeTrial\Frontend\Manage_Hometrialist::instance()->add_product( $id );
        if ( ! $inserted ) {
            wp_send_json_success([
                'message' => __( 'Product do not add!', 'hometrial' )
            ]);
        }else{
            wp_send_json_success([
                'item_count' => count( \HomeTrial\Frontend\Manage_Hometrialist::instance()->get_products_data() ),
                'max_num_of_items' => \HomeTrial\Frontend\Manage_Hometrialist::instance()->max_num_of_items(),
                'message' => __( 'Product successfully added!', 'hometrial' )
            ]);
        }

    }

    /**
     * [remove_hometrialist] Product delete ajax callback
     * @return [void]
     */
    public function remove_hometrialist(){
        $id = sanitize_text_field( $_GET['id'] );
        $deleted = \HomeTrial\Frontend\Manage_Hometrialist::instance()->remove_product( $id );
        if ( ! $deleted ) {
            wp_send_json_success([
                'message' => __( 'Product do not delete!', 'hometrial' )
            ]);
        }else{
            wp_send_json_success([
                'item_count' => count( \HomeTrial\Frontend\Manage_Hometrialist::instance()->get_products_data() ),
                'max_num_of_items' => \HomeTrial\Frontend\Manage_Hometrialist::instance()->max_num_of_items(),
                'message' => __( 'Product successfully deleted!', 'hometrial' )
            ]);
        }

    }

    /**
     * [variation_form_html]
     * @param  boolean $id product id
     * @return [void]
     */
    public function variation_form_html( $id = false ){

        if( isset( $_POST['id'] ) ) {
            $id = sanitize_text_field( (int) $_POST['id'] );
        }
        if( ! $id || ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        global $post;

        $args = array( 
            'post_type' => 'product',
            'post__in' => array( $id ) 
        );

        $get_posts = get_posts( $args );

        foreach( $get_posts as $post ) :
            setup_postdata( $post );
            woocommerce_template_single_add_to_cart();
        endforeach; 

        wp_reset_postdata(); 

        wp_die();

    }

    /**
     * [insert_to_cart] Insert add to cart
     * @return [JSON]
     */
    public function insert_to_cart(){

        // phpcs:disable WordPress.Security.NonceVerification.Missing
        if ( ! isset( $_POST['product_id'] ) ) {
            return;
        }

        $product_id         = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
        $quantity           = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
        $variation_id       = !empty( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
        $variations         = !empty( $_POST['variations'] ) ? array_map( 'sanitize_text_field', $_POST['variations'] ) : array();
        $passed_validation  = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );
        $product_status     = get_post_status( $product_id );

        if ( $passed_validation && \WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) && 'publish' === $product_status ) {
            do_action( 'woocommerce_ajax_added_to_cart', $product_id );
            if ( 'yes' === get_option('woocommerce_cart_redirect_after_add') ) {
                wc_add_to_cart_message( array( $product_id => $quantity ), true );
            }
            \WC_AJAX::get_refreshed_fragments();
        } else {
            $data = array(
                'error' => true,
                'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
            );
            echo wp_send_json( $data );
        }
        wp_send_json_success();
        
    }


}