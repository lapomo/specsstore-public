<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) : ?>

	<section class="related products">

		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		
		<?php 
		// woocommerce_product_loop_start(); ?>

		<div class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?> divilife-3-col-feature-blurb-slider">

			<?php foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'related-content', 'product' );
					?>

			<?php endforeach; ?>
			

		<?php 
		// woocommerce_product_loop_end(); ?>
		
			</div>

	</section>
	
	<style>
	
	.slick-slider {
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		-webkit-touch-callout: none;
		-khtml-user-select: none;
		ms-touch-action: pan-y;
		touch-action: pan-y;
		-webkit-tap-highlight-color: transparent;
	}
	.slick-list {
		position: relative;
		display: block;
		overflow: hidden;
		margin: 0;
		padding: 0 0 30px;
	}

	.slick-track:before, .slick-track:after {
		display: table;
		content: '';
	}
	
	.slick-slide {
		position: relative;
		float: left;
		/* height: 100%; */
		min-height: 1px;
		margin: 20px;
		width: auto;
		text-align: center;
		padding: 10px 20px;
	}
	
	.divilife-3-col-feature-blurb-slider .slick-arrow, .divilife-3-col-feature-blurb-slider .slick-arrow:hover, .divilife-3-col-feature-blurb-slider .slick-arrow:focus {
		position: absolute;
		font-size: 0;
		line-height: 0;
		padding: 0;
		color: transparent;
		outline: none;
		/* background: rgba(122,105,230,0.3); */
		border: none;
		cursor: pointer;
		top: 50%;
		transform: translateY(-50%);
		z-index: 100;
		height: 50px;
		vertical-align: middle;
		border-radius: 50%;
		width: 50px;
	}
	.divilife-3-col-feature-blurb-slider .slick-prev { left: 0px; }
	.divilife-3-col-feature-blurb-slider .slick-next { right: 0px; }
	
	.divilife-3-col-feature-blurb-slider .slick-arrow:before {
		font-family: ETmodules;
		color: #000;
		background: transparent;
		opacity: 1;
		font-size: 46px;
		vertical-align: middle;
		/* color: #7a69e6; */
		text-align: center;
	}
	.divilife-3-col-feature-blurb-slider .slick-arrow:hover:before { opacity: 0.8; }
	.divilife-3-col-feature-blurb-slider .slick-prev:before { content: '\34'; }
	.divilife-3-col-feature-blurb-slider .slick-next:before { content: '\35'; } 
	
	.entry-content ul.slick-dots {
		position: absolute;
		bottom: 0;
		display: block;
		width: 100%;
		padding: 0;
		margin: 0;
		list-style: none;
		text-align: center;
	}
	.slick-dots {
		text-align: center;
	}
	.slick-dots li {
		position: relative;
		display: inline-block;
		margin: 0 5px;
		padding: 0;
		cursor: pointer;
	}
	.slick-dots li button {
		font-size: 0;
		line-height: 0;
		display: block;
		width: 10px;
		height: 10px;
		padding: 0;
		cursor: pointer;
		color: transparent;
		border: 0;
		outline: none;
		background-color: #B7B7B7;
		border-radius: 10px;
	}
	.slick-dots li.slick-active button { background-color: #f7828e; }
	
	@media(max-width: 980px) {
		.divilife-3-col-feature-blurb-slider .slick-prev { left: -32px; }
		.divilife-3-col-feature-blurb-slider .slick-next { right: -30px; }
	}
	@media(max-width: 499px) {
		.divilife-3-col-feature-blurb-slider .slick-prev { left: -26px; }
		.divilife-3-col-feature-blurb-slider .slick-next { right: -24px; }
	}
		
	</style>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>
	<script>
		jQuery(document).ready(function() {
			jQuery('.divilife-3-col-feature-blurb-slider').slick({
			dots: true,
			slidesToShow: 2,
			slidesToScroll: 1,
			arrows: true,
			autoplay: false,
			responsive: [
			{
				breakpoint: 980,
				settings: {
				slidesToShow: 2
				}
			},
			{
				breakpoint: 767,
				settings: {
				slidesToShow: 1
				}
			}
			]
		}); 
		});
	</script>
	<?php
endif;

wp_reset_postdata();
