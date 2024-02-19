<?php
defined('ABSPATH') || exit;

/********************
 
    This file is for all the shortcodes used in the wordpress site.
    It is included in the functions.php file of the child theme.

 */

add_shortcode('hometrial_login_request', function () {
    // if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
    //     return;
    // }
    ob_start();

    $ht_instance = HomeTrial\Frontend\Manage_Hometrialist::instance();
    $ht_products = $ht_instance->get_hometrialist_products();

    // $stripe_gateway = new WC_Payment_Gateway_Stripe_CC();

    if($ht_products){
        if (!is_user_logged_in()) {
            echo "<div>Please sign in to order your Home Trial. If you don't have an account yet, we will create one for you when you checkout.</div>";
            wp_login_form();
        }else{
            echo "<div>You are logged in.</div>";
            // echo $stripe_gateway->generate_stripe_button_html();
        }
        echo do_shortcode('[ws_form id="7"]');
    }
    return ob_get_clean();
});

// Display global contact information
add_shortcode('ls_global_contact', function ($atts) {

    ob_start();

    if (get_option($atts['field'])) {

        $option = get_option($atts['field']);

        if (str_contains($atts['field'], 'email')) {

            echo '<a href="mailto:' . $option . '">' . $option . '</a>';
        } elseif (str_contains($atts['field'], 'phone')) {

            echo '<a href="tel:' . $option . '">' . $option . '</a>';
        } else {

            echo $option;
        }
    } else {

        echo 'No Info';
    }

    return ob_get_clean();
});

// Display Frame Shapes - used on HomePage
add_shortcode('ls_home_frameshapes', function () {
    ob_start();
    $terms = get_terms(array('taxonomy' => "pa_frame-shape", 'hide_empty' => true));
    if (!$terms) {
        return;
    }
    echo '<div class="ls-frameshape-container">';
    foreach ($terms as $term) {
        echo '<a href="' . wc_get_page_permalink('shop') . '?wpf=product_filter&wpf_page=1&wpf_frame-shapes=' . $term->slug . '" class="ls-frameshape">' . $term->description . '<span>' . $term->name . '</span></a>';
    }
    echo '</div>';
    return ob_get_clean();
});

// Display Basket Menu Icon
add_shortcode('ls_woo_cart_icon', function () {
    ob_start();

    $cart_count = WC()->cart->cart_contents_count; // Set variable for cart item count
    $cart_url = wc_get_cart_url();  // Set Cart URL

?>
    <a class="cart-contents menu-item" href="<?php echo $cart_url; ?>" title="<?php _e('View your shopping cart'); ?>">
        <span class="cart-contents-counter-icon" style="display: block; line-height: 1;"><svg style="position: relative; top: 1px;" xmlns="http://www.w3.org/2000/svg" height="26" width="26">
                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0020 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
            </svg></span>

        <span class="cart-contents-count"><?php echo $cart_count; ?></span>
    </a>
<?php

    return ob_get_clean();
});

// Display Brand banner - used on brand archive page
add_shortcode('ls-archive-page-brand-banner', function () {
    ob_start();
    $term = get_queried_object();
    if ($term) {
        $brand_banner      = get_term_meta($term->term_id, 'pwb_brand_banner', true);
        echo wp_get_attachment_image($brand_banner, 'full', false);
    }
    return ob_get_clean();
});

// Display Brand Title - used on brand archive page
add_shortcode('ls-archive-page-brand-title', function () {
    ob_start();
    $term = get_queried_object();
    echo woocommerce_page_title();
    return ob_get_clean();
});

// Display Brand Description - used on brand archive page
add_shortcode('ls-archive-page-brand-description', function () {
    ob_start();
    $term = get_queried_object();
    if ($term) {
        echo do_shortcode(wpautop($term->description));
    }
    return ob_get_clean();
});

// Display Product Specifications - used on product page
add_shortcode('ls_product_specifications', function () {
    global $product;
    $img_attrs = array("pa_frame-color", "pa_frame-shape");
    $brand = array("pwb-brand");
    $text_attrs = array("pa_frame-material", "pa_frame-size", "pa_gender");
    $measurement_attrs = array(
        "pa_lens-width",
        "pa_bridge-width",
        "pa_frame-temple-length",
        "pa_frame-front-width",
        "pa_lens-height",
    );
    $measurements = array();
    $specifications = array();
    // $terms = wc_get_product_terms($product->get_id(), "pwb-brand");

    foreach (array_merge($text_attrs, $img_attrs, $brand, $measurement_attrs) as $attr) {
        $terms = wc_get_product_terms($product->get_id(), $attr);
        if (empty($terms)) {
            continue;
        }

        foreach ($terms as $term) {
            if (in_array($attr, $measurement_attrs)) {
                $measurement_arr[$attr] = $term; 
            } else {
                $specifications[$attr] = $term;
            }
        }
    }

    ob_start();
?>
    <div class="specifications">
        <h3>Product Specification</h3>
        <div class="spec-content">
                <?php
                    foreach($specifications as $attr => $term) {
                        $name = in_array($attr, $brand) ? "Brand" : wc_attribute_label($attr);
                        ?> 
                        <div class="product-spec <?php echo $attr ?>"><span><?php echo $name; ?></span>
                            <div>
                                <?php
                            if (in_array($attr, $brand)) {
                                    echo do_shortcode('[pwb-brand product_id="' . $product->get_id() . '" image_size="medium"]');
                                } elseif (in_array($attr, $img_attrs)) {
                                    echo '<div>' . $term->description . '</div>';
                                } elseif (in_array($attr, $measurement_attrs)) {
                                    $measurement_arr[$attr] = $term; 
                                } else {
                                    echo '<span>' . $term->name . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
        </div>
        <?php
        if ($measurement_arr) {
            ?>
            <h3>Product Measurement</h3>
            <div class="measurement-content">
                <?php
            foreach ($measurement_arr as $attr => $term) {
                $name = in_array($attr, $brand) ? "Brand" : wc_attribute_label($attr);
                ?>
                <div class="product-meas <?php echo $attr ?>"><span><?php echo $name; ?></span>
                    <div>
                <?php
                echo '<div class="measurement">' . get_option($attr . "_icon") . '<div><div>' .  $product->get_attribute($attr) . ' mm</div><p>' . get_option($attr . "_details") . '</p></div></div>';
                echo '</div>
            </div>';
            }
            echo '</div>';
        }
        ?>


    </div>
<?php
    return ob_get_clean();
});

// Display floating cart - used on product page
add_shortcode('ls_floating_cyl', function () {

    global $product;

    ob_start();
    echo '
        <div class="ls-cyl-sum-container">
            <h3 class="ls-cyl-sum-banner">Your customised glasses</h3><hr>
            ' . $product->get_image() . '
            <span class="ls-cyl-sum-title">' . $product->get_name() . '</span> 
            <div>
                <div class="row price">
                    <span>Frame and Lenses</span><span>£' . $product->get_price() . '</span>
                </div>
                <div class="row options">
                    <span>Options</span><span class="ls-cyl-sum-options_price">Free</span>
                </div>
                <div class="ls-cyl-sum-options">
                    <div class="ls-cyl-sum-use row">
                        <span class="ls-cyl-sum-use_name">Single vision lenses</span>
                        <span>Free</span>
                    </div>
                    <div class="ls-cyl-sum-type row">
                        <span class="ls-cyl-sum-type_name">Standard lenses</span>
                        <span class="ls-cyl-sum-type_price">Free</span>
                    </div>
                    <div class="ls-cyl-sum-package row">
                        <span class="ls-cyl-sum-package_name"></span>
                        <span class="ls-cyl-sum-package_price"></span>
                    </div>
                </div>
                <div class="total">
                    £<span class="ls-cyl-sum-total_price">' . $product->get_price() . '</span>
                </div>
            </div>
        </div>
        ';
?>

    <script>
        // This function is called by ws form fields on change
        function upd_floating_cart() {
            $form_ids = {
                "use_name": "781",
                "type_name": "782",
                "type_price": "777",
                "package_name": "783",
                "package_price": "780",
                "options_price": "784",
                "total_price": "790"
            };
            for ($key in $form_ids) {
                $val = document.getElementById("wsf-2-field-" + $form_ids[$key]).value;
                if( $val.match(/^ *$/) && $key.split("_")[1] === "name"){
                    continue;
                }
                if ($val == "£0.00" || $val.match(/^ *$/)) {
                    if(["780"].includes($form_ids[$key]) && document.getElementById("wsf-2-field-783").value === ""){
                        $val = ""
                    }else{
                        $val = "Free"
                    }
                }
                
                console.log($key + " " + $val);
                document.querySelector(".ls-cyl-sum-" + $key).innerText = $val;
            }
        };
    </script>

<?php
    return ob_get_clean();
});

// Display floating cart - used on product page
add_shortcode('ls_floating_cyp', function () {

    global $product;

    ob_start();
    echo '
        <div class="ls-cyl-sum-container">
            <h3 class="ls-cyl-sum-banner">Your customised glasses</h3><hr>
            <div>
                <div class="row options">
                    <span>Options</span><span class="ls-cyl-sum-options_price">Free</span>
                </div>
                <div class="ls-cyl-sum-options">
                    <div class="ls-cyl-sum-use row">
                        <span class="ls-cyl-sum-use_name">Single vision lenses</span>
                        <span>Free</span>
                    </div>
                    <div class="ls-cyl-sum-type row">
                        <span class="ls-cyl-sum-type_name">Standard lenses</span>
                        <span class="ls-cyl-sum-type_price">Free</span>
                    </div>
                    <div class="ls-cyl-sum-package row">
                        <span class="ls-cyl-sum-package_name"></span>
                        <span class="ls-cyl-sum-package_price"></span>
                    </div>
                </div>
                <div class="total">
                    £<span class="ls-cyl-sum-total_price">0</span>
                </div>
            </div>
        </div>
        ';
?>

    <!-- <script>
        // handle the wsf popup button clicks
        function ls_handle_wsf_popup(e, who, proceed) {
            if (!proceed) {
                switch (who) {
                    case 'sph':
                        document.querySelector(".ls-field-sph-right").value = "";
                        document.querySelector(".ls-field-sph-left").value = "";
                    case 'cyl':
                        document.querySelector(".ls-field-cyl-right").value = "";
                        document.querySelector(".ls-field-cyl-left").value = "";
                }

            }
            e.closest(".ls-popup-wrapper").style.display = "none";
        }
    </script>
    <script>
        // This function is called by ws form fields on change
        function upd_floating_cart() {
            $form_ids = {
                "use_name": "781",
                "type_name": "782",
                "type_price": "777",
                "package_name": "783",
                "package_price": "780",
                "options_price": "784",
                "total_price": "790"
            };
            for ($key in $form_ids) {
                $val = document.getElementById("wsf-1-field-" + $form_ids[$key]).value;
                if ($val == "£0.00" || $val.match(/^ *$/)) {
                    continue;
                }
                console.log($key);
                document.querySelector(".ls-cyl-sum-" + $key).innerText = $val;
            }
        };
    </script> -->

<?php
    return ob_get_clean();
});

// Display WS Form on product page
add_shortcode('ls_ws_form', function () {

    global $product;

    if (!in_array(117, $product->get_category_ids())) {
        return "";
    }

    ob_start();

    // Enqueue variation scripts.
    wp_enqueue_script('wc-add-to-cart-variation');

    // Get Available variations?
    $get_variations = count($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);

    wc_get_template(
        'single-product/add-to-cart/cyl-form.php',
        array(
            'available_variations' => $get_variations ? $product->get_available_variations() : false,
            'attributes'           => $product->get_variation_attributes(),
            'selected_attributes'  => $product->get_default_attributes(),
        )
    );
?>

    <script>
        // handle the wsf popup button clicks
        function ls_handle_wsf_popup(e, who, proceed) {
            if (!proceed) {
                switch (who) {
                    case 'sph':
                        document.querySelector(".ls-field-sph-right").value = "";
                        document.querySelector(".ls-field-sph-left").value = "";
                    case 'cyl':
                        document.querySelector(".ls-field-cyl-right").value = "";
                        document.querySelector(".ls-field-cyl-left").value = "";
                }

            }
            e.closest(".ls-popup-wrapper").style.display = "none";
        }
    </script>

<?php
    return ob_get_clean();
});

// Return the archive page url of a specified category
add_shortcode('ls_archive_page_url', function ($atts) {
    extract(shortcode_atts(array(
        'slug' => ''
    ), $atts));
    $url = get_term_link($slug, 'product_cat');
    if ($url != false && $url != '') {
        return $url;
    }
    return '';
});

// add support for shortcodes in certain divi module fields
if (!class_exists('DBDSE_EnableShortcodesInModuleFields')) {
    class DBDSE_EnableShortcodesInModuleFields
    {

        static public function supportedFields()
        {
            return apply_filters(
                'dbdsp_fields_to_process',
                array(
                    'et_pb_accordion_item' => array('title'),
                    'et_pb_blurb' => array('title', 'url', 'image', 'alt'),
                    'et_pb_button' => array('button_url', 'button_text'),
                    'et_pb_circle_counter' => array('title', 'number'),
                    'et_pb_cta' => array('title', 'button_text', 'button_url'),
                    'et_pb_image' => array('url', 'src', 'title_text', 'alt'),
                    'et_pb_number_counter' => array('title', 'number'),
                    'et_pb_counter' => array('percent'),
                    'et_pb_pricing_table' => array('title', 'subtitle', 'currency', 'sum', 'button_text', 'button_url'),
                    'et_pb_tab' => array('title'),
                    'et_pb_slide' => array('title', 'text_title', 'heading', 'heading_text', 'button_text', 'button_link', 'image_alt', 'title_text', 'image_link', 'link_option_url', 'image_video', 'image_url', 'image_src', 'src'),
                    'db_pb_slide' => array('button_text_2', 'button_link_2'), // Divi Booster added second slide buttons
                    'et_pb_fullwidth_header' => array('title', 'subhead', 'button_one_text', 'button_two_text', 'button_one_url', 'button_two_url'),
                    'et_pb_fullwidth_image' => array('src', 'title_text', 'alt'),
                    'et_pb_contact_field' => array('field_title')
                )
            );
        }

        public function init()
        {
            add_filter('the_content', array($this, 'processShortcodes')); // Standard content
            add_filter('et_builder_render_layout', array($this, 'processShortcodes')); // Theme builder layout content
            add_filter('dbdse_et_pb_layout_content', array($this, 'processShortcodes')); // Global module content
            add_filter('et_pb_module_shortcode_attributes', array($this, 'preventShortcodeEncodingInModuleSettings'), 11, 3);
        }

        public function processShortcodes($content)
        {
            $modules = self::supportedFields();
            do_action('dbdsp_pre_shortcode_processing');
            foreach ((array) self::supportedFields() as $module => $fields) {
                foreach ($fields as $field) {
                    $regex = '#[' . preg_quote($module) . ' [^]]*?\b' . preg_quote($field) . '="([^"]+)"#';
                    $content = preg_replace_callback($regex, array($this, 'processMatchedAttribute'), $content);
                }
            }
            do_action('dbdsp_post_shortcode_processing');
            return $content;
        }

        protected function processMatchedAttribute($matches)
        {

            // Exit if not properly matched
            if (!is_array($matches) || !isset($matches[0])) {
                return '';
            }
            if (!isset($matches[1])) {
                return $matches[0];
            }

            // Define character replacements
            $encoded = array('%' . '22', '%' . '91', '%' . '93');
            $decoded = array('"', '[', ']');

            // Get the decoded parameter value
            $val = $matches[1];
            $val = str_replace($encoded, $decoded, $val); // decode encoded characters
            $val = do_shortcode($val);
            $val = str_replace($decoded, $encoded, $val); // re-encode

            // Return the replacement value
            $result = str_replace($matches[1], $val, $matches[0]);

            return $result;
        }

        public function preventShortcodeEncodingInModuleSettings($props, $attrs, $render_slug)
        {
            if (!is_array($props)) {
                return $props;
            }
            if (!empty($_GET['et_fb'])) {
                if ($render_slug === 'et_pb_image' && !empty($attrs['url']) && strpos($attrs['url'], '[') !== false && strpos($attrs['url'], ']') !== false) {
                    $props['url'] = $attrs['url'];
                    $props['url'] = str_replace(array('[', ']', '"'), array('[', ']', '"'), $props['url']);
                }
            }
            return $props;
        }
    }
    (new DBDSE_EnableShortcodesInModuleFields)->init();
}
