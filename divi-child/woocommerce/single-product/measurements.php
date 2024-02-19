<?php

/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

global $product;
$attrs_icons = array(
	"pa_lens-width" => "0 0 256 512",
	"pa_bridge-width" => "181 0 150 512",
	"pa_frame-temple-length" => "0 0 512 512",
	"pa_frame-front-width" => "0 0 512 512",
	"pa_lens-height" => "0 0 256 512",
);

$measurements = array();
foreach ($attrs_icons as $attr => $clipped) {
	$value = $product->get_attribute($attr);
	if ($value != "" && $value != null) {
		$measurements[$attr] = array(
			"name" => wc_attribute_label($attr),
			"value" => $value,
			"icon" => get_option($attr . "_icon"),
			"details" => get_option($attr . "_details"),
			"viewbox" => $clipped
		);
	}
}
if (!empty($measurements)) {
	echo '<div class="product-measurements-container"><span>Measurements:</span><div class="product-measurements">';
	foreach ($measurements as $attr => $measurement) {
		$clipped_icon = str_replace("0 0 512 512", $measurement["viewbox"], $measurement["icon"]);
		echo '<div class="product-measurement">' . $clipped_icon . '<span>' . $measurement["value"] . ' mm</span></div>';
		echo '<div class="product-measurement-tooltip">' . $measurement["icon"] . '<div><span>' . $measurement["name"] . '</span><p>' . $measurement["details"] . '</p></div></div>';
	}
	echo '</div>';
}
