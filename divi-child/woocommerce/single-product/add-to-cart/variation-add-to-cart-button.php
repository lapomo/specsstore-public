<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

global $product;
	// do_action( 'woocommerce_before_add_to_cart_button' );
	// do_action( 'woocommerce_before_add_to_cart_quantity' );

	// woocommerce_quantity_input(
	// 	array(
	// 		'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
	// 		'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
	// 		'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
	// 	)
	// );

	do_action( 'woocommerce_after_add_to_cart_quantity' );
	?>

	<button type="submit" class="single_add_to_cart_button button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" height="25" width="25"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0020 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<div class="no-form woocommerce-variation-add-to-cart variations_button" style="display: none;">
		<?php 
		do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		<script>
			(function($) {
		// Create wsf-rendered event handler
		$(document).on('wsf-rendered', function(e, form, form_id, instance_id) {
			
			if(document.querySelector(".no-form .wsf-tabs")){
				document.querySelector(".no-form .wsf-tabs").remove();
				document.querySelector(".no-form .wsf-groups").remove();
				// document.querySelector(".choose-your-lenses-btn").style.display = "inline-flex";
			}
			// move color radios right after their corresponding type radios
			$(".ls-wsf-trans-color").insertAfter("#wsf-2-label-743-row-3");
			$(".ls-wsf-tint-color").insertAfter("#wsf-2-label-743-row-4");
		});
	})(jQuery);
		</script>
	</div>
	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />

</div>
