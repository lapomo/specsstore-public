<?php

/********************
    Init Section
    - include parent theme
    - include other libraries, files and functions

 */

use WooCommerce\PayPalCommerce\WcGateway\Gateway\PayPalGateway;

// get styles from parent Theme
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
});

// load local scripts
add_action('wp_enqueue_scripts', function () {
    // to make color swatches for variations
    wp_deregister_script('wc-add-to-cart-variation');
    wp_register_script('wc-add-to-cart-variation', get_stylesheet_directory_uri() . '/js/add-to-cart-variation.js', array('jquery', 'wp-util'), '2.0.0');
    wp_enqueue_script('wc-add-to-cart-variation');
    // to make menu and filter responsive
    wp_register_script('menu-filter', get_stylesheet_directory_uri() . '/js/menu-filter.js', array('jquery', 'wp-util'), '2.0.0');
    wp_enqueue_script('menu-filter');
});

//* Make Font Awesome available - until now only needed for basket icon
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css');
});

// include local php files located in includes folder
include(get_stylesheet_directory() . '/includes/theme-setup.php');


/********************
    Global Section
    ? Functionality that affects the site globally
    - Admin Dashboard
    - Mega Menu

 */


// make links in nav menu dynamic by prepending the sites url
add_filter('nav_menu_link_attributes', function ($atts) {
    if ('/' == substr($atts['href'], 0, 1)) {
        $atts['href'] = site_url() . $atts['href'];
    } elseif ('#' == substr($atts['href'], 0, 1)) {
        $atts['href'] = "";
    }
    return $atts;
}, 20);

// mega menu population and customisation
add_filter('walker_nav_menu_start_el', function ($item_output, $item) {
    /*
        *   the content of $item->description is crucial. It is used for the links of the menu items.
        *   * men and women get an image inserted which is taken from the term description
        *   * brands are populated according to the category
        *   * frame shapes are populated with the images from the term descriptions
        */

    // insert the description image if item is women or men
    if (in_array($item->title, array("Women", "Men"))) {
        // get all terms associated with the attribute gender
        $terms = get_terms(array('taxonomy' => "pa_gender", 'hide_empty' => false));
        $output = "";
        foreach ($terms as $term) {
            if ($term->name === $item->title) {

                $output = '<li class="menu-item-has-icon menu-item menu-item-type-custom menu-item-object-custom "><a href="' . site_url() . '/category/' . $item->description . '/?wpf=product_filter&wpf_cols=3&wpf_page=1&wpf_cat=' . $item->description . '&wpf_gender=' . $term->slug . '"><img src="' . site_url() . $term->description . '"><span>' . $term->name . '</span></a></li>';
                break;
            }
        }
        $item_output = $output;

        // populate frame shapes and insert description icons
    } elseif ($item->title == "frame-shape") {
        $tax = "pa_" . $item->title;
        // $terms = get_terms(array('taxonomy' => $tax, 'hide_empty' => false));
        // $output = "";

        $args = array(
            'limit' => -1,
            'status' => 'publish',
            'return' => 'ids',
            'category'  => array($item->description)
        );

        $shapes = array();
        foreach (wc_get_products($args) as $product_id) {
            foreach (wc_get_product_terms($product_id, $tax) as $term_shape) {
                $shape_item = ['name' => $term_shape->name, 'slug' => $term_shape->slug, 'description' => $term_shape->description];
                if (!in_array($shape_item, $shapes)) {
                    $shapes[] = $shape_item;
                }
            }
        }

        sort($shapes);
        $output = "";

        foreach ($shapes as $shape) {

            $output .= '<li class="menu-item-has-icon menu-item menu-item-type-custom menu-item-object-frame-shape"><a href="' . site_url() . '/category/' . $item->description . '/?wpf=product_filter&wpf_cols=3&wpf_page=1&wpf_cat=' . $item->description . '&wpf_frame-shapes=' . $shape['slug'] . '">' . $shape['description'] . '<span>' . $shape['name'] . '</span></a></li>';
        }

        $item_output = $output;

        // populate brands for category
    } elseif ($item->title == "brand-items") {
        $args = array(
            'limit' => -1,
            'status' => 'publish',
            'return' => 'ids',
            'category'  => array($item->description)
        );

        $brands = array();
        foreach (wc_get_products($args) as $product_id) {
            foreach (wc_get_product_terms($product_id, "pwb-brand") as $term_brand) {
                $brand_item = ['name' => $term_brand->name, 'slug' => $term_brand->slug];
                if (!in_array($brand_item, $brands)) {
                    $brands[] = $brand_item;
                }
            }
        }

        sort($brands);
        $output = "";
        foreach ($brands as $brand) {
            $output .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-pwb-brand"><a href="' . site_url() . '/category/' . $item->description . '/?wpf=product_filter&wpf_cols=3&wpf_page=1&wpf_cat=' . $item->description . '&wpf_top-brands=' . $brand['slug'] . '">' . $brand['name'] . '</a></li>';
        }
        $item_output = $output;
    }

    return $item_output;
}, 10, 2);

// Remove menus from the WordPress dashboard
add_action('admin_menu', function () {
    remove_menu_page('edit.php'); //Posts
    remove_menu_page('edit.php?post_type=project'); //Projects
});

// disable popup or updates for customised plugins
add_filter('site_transient_update_plugins', function ($value) {
    unset($value->response['themify-wc-product-filter/themify-wc-product-filter.php']);
    // print_r($value);
    unset($value->response['wishsuite/wishsuite.php']);
    return $value;
});


/********************
    WooCommerce Section
    ? Customisations that affect WooCommerce

 */

// Make all products cancelable
add_filter('woocommerce_valid_order_statuses_for_cancel', function ($arr, $order) {
    return array($order->get_status());
}, 10, 2);
// array( 'pending', 'failed' ), $order )

// Remove filters from attribute descriptions, so html can be placed inside (needed to input svgs or images as icons for the attribute terms)
foreach (array('pre_term_description') as $filter) {
    remove_filter($filter, 'wp_filter_kses');
    if (!current_user_can('unfiltered_html')) {
        add_filter($filter, 'wp_filter_post_kses');
    }
}
foreach (array('term_description') as $filter) {
    remove_filter($filter, 'wp_kses_data');
}

// Update Cart Menu Icon on cart change
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();

    $cart_count = WC()->cart->cart_contents_count;
    $cart_url = wc_get_cart_url();

?>
    <a class="cart-contents menu-item" href="<?php echo $cart_url; ?>" title="<?php _e('View your shopping cart'); ?>">
        <span class="cart-contents-counter-icon" style="display: block; line-height: 1;"><svg style="position: relative; top: 1px;" xmlns="http://www.w3.org/2000/svg" height="26" width="26">
                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0020 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
            </svg></span>

        <span class="cart-contents-count"><?php echo $cart_count; ?></span>
    </a>
<?php

    $fragments['a.cart-contents'] = ob_get_clean();

    return $fragments;
});


/*
    * Shop Page
    */

// Edit Number of Products to be displayed on shop pages
add_filter('loop_shop_per_page', function () {
    return 18;
}, 20);

// Add action buttons for products on shop pages
add_action('woocommerce_after_shop_loop_item', function () {

    global $product;

?>
    <div class="ls-wooloop-product-buttons">
        <?php
        echo do_shortcode('[wishsuite_button]');
        if (in_array(94, $product->get_category_ids())) {
            echo do_shortcode('[hometrial_button]');
        }
        ?>
        <a href="<?php echo get_permalink($product->get_id()); ?>"><span>Select option</span></a>
    </div>
<?php
});

// Remove default sorting button 
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// Remove the default order by button
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// Edit position of price and rating to next to each other for products on shop pages
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
add_action('woocommerce_after_shop_loop_item_title', function () {
    echo '<div class="loop-pricerating-container">';
}, 8);
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 9);
add_action('woocommerce_after_shop_loop_item_title', function () {
    echo '</div>';
}, 11);
/////

// Add Tooltip for product title on shop pages
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('woocommerce_shop_loop_item_title', function () {
    echo '<h2 class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '" title="' . get_the_title() . '">' . get_the_title() . '</h2>';
}, 10);
/////

/*
    * Product Page
    */

// Change product gallery image size on product page
add_filter('woocommerce_get_image_size_gallery_thumbnail', function ($size) {
    return array(
        'width'  => 200,
        'height' => 150,
        'crop'   => 1,
    );
});

// Edit text on add to cart button without customisation //TODO: make editable by admin
add_filter('woocommerce_product_single_add_to_cart_text', function () {
    return 'Buy Frame Only';
});

// Add categories under product title on product page //TODO: figure out why it's not possible to make it anonymous function
add_action('woocommerce_single_product_summary', 'ls_get_cat', 6);

function ls_get_cat()
{
    wc_get_template('single-product/category.php');
}

// Add measurements before buttons on product page
add_action('woocommerce_before_single_variation', function () {
    wc_get_template('single-product/measurements.php');
}, 9);


// Remove default add to cart button (being replaced in custom templates)
remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20, 0);


// Add product attributes in WS Form Fields before rendering
// change number of hook (wsf_pre_render_#) if switching to other form
add_filter('wsf_pre_render_3', function ($form) {

    // 
    // This is the id of the hidden section in the form where all attributes are in
    // Update if section where product attribute fields reside in changes
    // 
    // Important: all fields in this section will only be filled with attributes if the label is exactly the same as the attribute name
    // 


    $attr_section_id = "135";

    global $product;

    // check if form is on a product page and is a variable product
    if (!isset($product)) return $form;
    // if ( !$product->is_type( 'variable' )) return;

    // get all product attributes (variable and non-variable)
    $attributes = $product->get_attributes();
    $form_fields = wsf_form_get_fields($form);

    //     $packages_datagrid = wsf_field_get_datagrid("field_739");
    //     $datagrid_rows = $packages_datagrid['groups'][0]['rows'];

    //     foreach($datagrid_rows as $datagrid_row){
    //         $package_json = json_decode($datagrid_row['data'][2]);
    //         $html = "
    //   <div class='details' style='border: 1px solid rgb(84, 84, 84);
    //   text-align: center;
    //   width: 170px; position: relative;
    //   height: 350px;
    //   margin-bottom: 10px;'>
    //     <div class='triangle' style='width: 100%;
    //     height: 25px;
    //     background: " . $package_json->color . ";
    //     clip-path: polygon(
    //         0 0,
    //         100% 0,
    //         50% 105% 
    //         );'></div>
    //     <div class='title' style='margin-top: 8px;
    //     font-size: 12px;
    //     font-weight: 800;'>" . $package_json->title . "</div>
    //     <div class='picsub standard' style='" . $package_json->width !== 'standard' && 'display:none;' . "margin-top: 5px;'>
    //         <svg style='height: 55px;'
    //         version='1.1' id='prefix__Layer_1' xmlns='http://www.w3.org/2000/svg' x='0' y='0' viewBox='0 0 512 512' xml:space='preserve'><style>.prefix__st0{fill:#dadada}</style><path d='M427.1 86.3l-83.5 6.6s-84.2 188.4 10.9 332.7H431c.1.1-89.5-150.8-3.9-339.3z'/><path class='prefix__st0' d='M261.2 84.5l-50.8 4.1s-85.5 154.3 10.7 338.9h47.5s-93.2-144.8-7.4-343zM126.8 93.3h-21.3s-74.5 125.9 0 330.1h30.3s-82.1-149.6-9-330.1z'/>
    //     </svg>
    //     <div style='position: relative;
    //     top: -5px;
    //     font-size: 12px;'>Standard Lens 1.5</div>
    //     </div>
    //     <div class='picsub thin' style='" . $package_json->width !== 'thin' && 'display:none;' . "margin-top: 5px;'>
    //       <svg style='height: 55px;'
    //         version='1.1'
    //         id='prefix__Layer_1'
    //         xmlns='http://www.w3.org/2000/svg'
    //         x='0'
    //         y='0'
    //         viewBox='0 0 512 512'
    //         xml:space='preserve'
    //       >
    //         <style>
    //           .prefix__st0 {
    //             fill: #dadada;
    //           }
    //         </style>
    //         <path
    //           class='prefix__st0'
    //           d='M431.4 86.3l-83.5 6.6s-84.2 188.4 10.9 332.7h76.5c.1.1-89.6-150.8-3.9-339.3z'
    //         />
    //         <path
    //           d='M265.4 84.5l-50.8 4.1s-85.5 154.3 10.7 338.9h47.5s-93.2-144.8-7.4-343z'
    //         />
    //         <path
    //           class='prefix__st0'
    //           d='M131 93.3h-21.3s-74.5 125.9 0 330.1H140c.1 0-82-149.6-9-330.1z'
    //         />
    //       </svg>
    //       <div style='position: relative;
    //       top: -5px;
    //       font-size: 12px;'>Thin Lens 1.6</div>
    //     </div>
    //     <div class='picsub superthin' style='" . $package_json->width !== 'superthin' && 'display:none;' . "margin-top: 5px;'>
    //         <svg style='height: 55px;'
    //         version='1.1' id='prefix__Layer_1' xmlns='http://www.w3.org/2000/svg' x='0' y='0' viewBox='0 0 512 512' xml:space='preserve'><style>.prefix__st0{fill:#dadada}</style><path class='prefix__st0' d='M430.9 86.3l-83.5 6.6s-84.2 188.4 10.9 332.7h76.5c.1.1-89.6-150.8-3.9-339.3zM264.9 84.5l-50.8 4.1s-85.5 154.3 10.7 338.9h47.5s-93.2-144.8-7.4-343z'/><path d='M130.5 93.3h-21.3s-74.5 125.9 0 330.1h30.3c.1 0-82-149.6-9-330.1z'/>
    //     </svg>
    //         <div style='position: relative;
    //         top: -5px;
    //         font-size: 12px;'>Super Thin Lens 1.67</div>
    //       </div>
    //     <div class='features' style='margin-top: 12px;'>
    //       <div class='feature scratch' style='display: none;'>
    //         <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'>
    //           <path
    //             d='M510.4 160.7C421.8 47 425.6 49.9 420.8 49.4L282.1 30.7c-35-4.7-58.7 1.4-70.9 2.5-4.1.5-6.9 4.3-6.4 8.4.5 4.1 4.3 6.9 8.4 6.4 12.3-1.1 34.4-6.9 66.9-2.5l95.3 12.8c-7.1 1.5-112.3 22.9-119.4 24.3L136.6 58.3l41.3-5.6c4.1-.5 6.9-4.3 6.4-8.4-.5-4.1-4.3-6.9-8.4-6.4L91.2 49.4h-.1c-.1 0-.2 0-.3.1h-.4c-.1 0-.2 0-.3.1-.1 0-.3.1-.4.1-.1 0-.1 0-.2.1h-.1c-.1 0-.2.1-.2.1-.2.1-.3.1-.4.2-.1 0-.1.1-.2.1s-.1.1-.2.1-.1.1-.2.1c-3.2 1.9-1.7 2-86.7 110.3-1.8 2.3-2.1 5.4-.8 7.9 2.9 5.8-4.7-5.4 150.2 200.2 1.5 2 3.1 4.1 4.6 6.1 1.4 1.9 2.9 3.8 4.3 5.8 2.5 3.3 7.1 3.9 10.4 1.5 3.3-2.5 3.9-7.1 1.5-10.4l-144-191.2 116.5 36.7c2.8 7.4 88.1 232.4 89.7 236.6l-39.1-51.8-2-2.6c-2.5-3.3-7.1-3.9-10.4-1.5-3.3 2.5-3.9 7.1-1.5 10.4l.9 1.2c42.2 55.9 46.5 61.7 46.8 62 5.4 6.1 12.9 10 20.1 11.2 1.1.2 13.5.2 14.6 0 9.1-1.8 16.7-6.9 20.3-11.6l145.2-192.4c2.5-3.3 1.8-7.9-1.5-10.4s-7.9-1.8-10.4 1.5L278.1 454c4.4-11.7 86.5-228 89.7-236.6l116.5-36.7-46.4 61.5c-2.5 3.3-1.8 7.9 1.5 10.4s7.9 1.8 10.4-1.5c62.3-82.7 58.9-77.6 61.4-82.5 1.3-2.6 1-5.7-.8-7.9zm-164.7 43.2H166.3L256 101.5l89.7 102.4zM151 196c1 3-9 6-12 3-1.1-.8-2-1.8-2.8-2.9l1.3 3.5-118.4-37.3 70.6-90.5.3.9c.3-.6.6-1.2 1-1.7 2-3 6-3 11-4-5 1-9 1-11 4-.3.6-.6 1.1-1 1.7L136.1 196c.8 1.1 1.8 2.1 2.8 2.9 3.1 3.1 13.1.1 12.1-2.9 0-1.1-.1-2.2-.1-3.3L103.7 66.8C118.4 69.8 224 91.3 242 95l-89.5 102.1-1.6-4.4c0 1.1.1 2.2.1 3.3zm105.8 272.1h-1.6l-94.6-249.4h190.8l-94.6 249.4zm102.7-271L270 94.9c11.9-2.4 120-24.4 138.3-28.2l-48.8 130.4zm15 2.5l47.8-127.8 70.6 90.5-118.4 37.3z'
    //           />
    //           <path
    //             d='M147.9 364.8c18 23.6 35.2 46.6 35.2 46.6l12.7-7.8-36.2-47.9-11.7 9.1zM449.7 226.1c-17.9 23.6-35.4 46.4-35.4 46.4l10.9 10.2 36.4-47.7-11.9-8.9zM168.9 38.8l48.2-6.3 1.6 14.8-49.8 6.6z'
    //           />
    //         </svg>
    //         <span>Scratch Resistant Coating</span>
    //       </div>
    //       <div class='feature case' style='display: none;'>
    //         <svg
    //           version='1.1'
    //           id='prefix__Layer_1'
    //           xmlns='http://www.w3.org/2000/svg'
    //           x='0'
    //           y='0'
    //           viewBox='0 0 512 512'
    //           xml:space='preserve'
    //         >
    //           <style>
    //             .prefix__st0 {
    //               fill: none;
    //               stroke: #000;
    //               stroke-width: 4;
    //               stroke-miterlimit: 10;
    //             }
    //             .prefix__st1 {
    //               display: none;
    //               opacity: 0.64;
    //             }
    //           </style>
    //           <path
    //             class='prefix__st0'
    //             d='M381.5 401h-255C83.7 401 49 366.3 49 323.5S83.7 246 126.5 246h255c42.8 0 77.5 34.7 77.5 77.5S424.3 401 381.5 401zM403 242H105c-30.9 0-56-25.1-56-56s25.1-56 56-56h298c30.9 0 56 25.1 56 56s-25.1 56-56 56z'
    //           />
    //           <path
    //             class='prefix__st1'
    //             d='M400.9 295.1c-.5-3.6-1.1-7.4-1.5-11.3l-1.1.8c-1.4 1-3.1 1.6-4.9 1.6l-170.7 2.1c-1.8.1-3.6.2-5.5.5-10.8 1.7-18.6 3.7-23.9 5.4-4.8 1.5-9.3 3.8-13.4 6.7-10.9 7.6-39.2 26.9-47.6 29.4-3.5 1.1-5.8 2.1-7.2 3-2 1.3-2.3 4.2-.6 5.8 6.3 5.8 14 5.3 14 5.2 6.4.6 20.5-10.5 37.9-28.6 11.7-12.3 26.4-16.7 34.8-18.3 3.3-.6 6.8-.8 10.2-.7l47 2.6c4.5.2 9 .8 13.4 1.7 32.9 6.4 60.8 6.6 60.8 6.6l49.9-.7c2-.1 3.9 1.1 4.6 3 .1.2.2.5.3.7.9 2.5 1.4 5.2 1.4 7.8 2.1-7.4 3.3-15.1 2.1-23.3z'
    //           />
    //           <path
    //             class='prefix__st0'
    //             d='M405 239H107c-30.9 0-56-25.1-56-56s25.1-56 56-56h298c30.9 0 56 25.1 56 56s-25.1 56-56 56z'
    //           />
    //           <path
    //             d='M408.4 275c-10.4-1.7-57.1-17.6-156.6-1.5-98-13.9-146.1-.2-156.6 1.5-10.4 1.7-13.1 6.4-11.9 14.3 1 6.4 3.3 7 9.2 11.4 5.9 4.3 9.3 33 15.6 52.9 6.3 19.9 12.8 27 57 27 26.4 0 41-3.7 50.1-11.5 11.4-9.9 13.7-24.9 15.9-31.8 3.9-12.3 5.9-23.2 8.6-29.5 6.5-14.8 17.9-14.8 24.4 0 2.8 6.3 4.7 17.2 8.6 29.5 2.2 6.9 4.5 21.9 15.9 31.8 9.1 7.8 23.7 11.5 50.1 11.5 44.2 0 50.7-7.1 57-27 6.3-19.9 9.7-48.6 15.6-52.9s8.2-4.9 9.2-11.4c1-7.9-1.6-12.5-12.1-14.3zm-182.6 21.6c-.8 17.1-4 54.4-17 67.2-8.4 8.3-34.2 10.4-54.3 10.1-11.3-.2-20.2-1.5-28.6-7.1-7.1-4.7-8.3-11.4-10.9-21.5-4.3-17.3-6.1-37.6-6.8-48.8-.3-5.7 3.1-10.9 8.4-13 25.4-9.6 75-9 100.6.2 5.4 1.9 8.9 7.1 8.6 12.9zm169.7 0c-.7 11.2-2.4 31.6-6.8 48.8-2.5 10.1-3.8 16.6-10.9 21.5-8.4 5.6-17.3 6.8-28.6 7.1-20.1.4-45.9-1.7-54.3-10.1-13-12.8-16.2-50.2-17-67.2-.3-5.7 3.3-10.9 8.6-12.9 25.6-9.1 75.2-9.8 100.6-.2 5.3 2 8.7 7.2 8.4 13z'
    //             fill='#151515'
    //           />
    //           <path
    //             class='prefix__st1'
    //             d='M101.9 290.3c.5-3.6 1.1-7.4 1.5-11.3l1.1.8c1.4 1 3.1 1.6 4.9 1.6l170.7 2.1c1.8.1 3.6.2 5.5.5 10.8 1.7 18.6 3.7 23.9 5.4 4.8 1.5 9.3 3.8 13.4 6.7 10.9 7.6 39.2 26.9 47.6 29.4 3.5 1.1 5.8 2.1 7.2 3 2 1.3 2.3 4.2.6 5.8-6.3 5.8-14 5.3-14 5.2-6.4.6-20.5-10.5-37.9-28.6-11.7-12.3-26.4-16.7-34.8-18.3-3.3-.6-6.8-.8-10.2-.7l-47 2.6c-4.5.2-9 .8-13.4 1.7-32.9 6.4-60.8 6.6-60.8 6.6l-49.9-.7c-2-.1-3.9 1.1-4.6 3-.1.2-.2.5-.3.7-.9 2.5-1.4 5.2-1.4 7.8-2.1-7.4-3.3-15.1-2.1-23.3z'
    //           />
    //         </svg>
    //         <span>Quality case and cloth</span>
    //       </div>
    //       <div class='feature reflection' style='display: none;'>
    //         <svg
    //           version='1.1'
    //           id='prefix__Layer_1'
    //           xmlns='http://www.w3.org/2000/svg'
    //           x='0'
    //           y='0'
    //           viewBox='0 0 512 512'
    //           xml:space='preserve'
    //         >
    //           <style>
    //             .prefix__st0 {
    //               fill: none;
    //               stroke: #000;
    //               stroke-width: 21;
    //               stroke-miterlimit: 10;
    //             }
    //           </style>
    //           <path
    //             class='prefix__st0'
    //             d='M298.3 111.8c6.9 17.1 25.4 68.7 19.1 115-.6 4.8 3.7 8.7 8.3 7.4 23.2-6.1 80.3-20.6 109.2-22.6 3.5-.2 6.1-3.1 6.2-6.6.4-18-2.8-72.1-48.8-128.8-1.6-2-4.3-2.9-6.8-2.3-11.7 2.8-46.7 12-83.9 29.2-3.1 1.6-4.6 5.4-3.3 8.7zM249.5 121.1c-14.8 5.3-57.6 20.8-85.3 31.9-2.9 1.2-4.6 4.2-4.1 7.2 2 12.8 5.7 47.7-6.4 82.9-1.7 4.9 2.6 9.7 7.7 8.7 22.4-4.5 71.3-13.6 104.6-14.7 3.5-.1 6.2-2.8 6.4-6.3 1.1-18.5 2.6-74-14.8-106.5-1.5-3-5-4.4-8.1-3.2zM139.6 298c-5.3 14.4-25.6 63.7-73.9 113.9-3.7 3.8-1.6 10.3 3.6 11.2 21.2 3.9 69.2 11.4 138.5 13.9 2.1.1 4.2-.9 5.5-2.6 8-10.2 36-51 60.5-150.1 1.2-4.8-3-9.1-7.8-8.2-23.4 4.6-81.4 15.5-120.5 17.5-2.7.2-5 1.9-5.9 4.4zM317.7 280.1c-4.3 20.5-20 89.7-44.9 148.3-1.7 4.1 1 8.8 5.4 9.3 13.9 1.5 49.8 1.1 140.8-14.6 2.3-.4 4.3-2 5.1-4.2 5.6-14.6 25.7-73.1 23.9-152.7-.1-4.3-4.1-7.4-8.3-6.4-21.3 5.2-79.7 18.4-114.9 15.2-3.3-.5-6.4 1.7-7.1 5.1z'
    //           />
    //         </svg>
    //         <span>Anti-reflection Coating</span>
    //       </div>
    //       <div class='feature uv' style='display: none;'>
    //         <svg
    //         version='1.1'
    //         id='prefix__Layer_1'
    //         xmlns='http://www.w3.org/2000/svg'
    //         x='0'
    //         y='0'
    //         viewBox='0 0 512 512'
    //         xml:space='preserve'
    //       >
    //         <style>
    //           .prefix__st0 {
    //             fill: none;
    //             stroke: #000;
    //             stroke-width: 21;
    //             stroke-miterlimit: 10;
    //           }
    //         </style>
    //         <path
    //           class='prefix__st0'
    //           d='M298.3 111.8c6.9 17.1 25.4 68.7 19.1 115-.6 4.8 3.7 8.7 8.3 7.4 23.2-6.1 80.3-20.6 109.2-22.6 3.5-.2 6.1-3.1 6.2-6.6.4-18-2.8-72.1-48.8-128.8-1.6-2-4.3-2.9-6.8-2.3-11.7 2.8-46.7 12-83.9 29.2-3.1 1.6-4.6 5.4-3.3 8.7zM249.5 121.1c-14.8 5.3-57.6 20.8-85.3 31.9-2.9 1.2-4.6 4.2-4.1 7.2 2 12.8 5.7 47.7-6.4 82.9-1.7 4.9 2.6 9.7 7.7 8.7 22.4-4.5 71.3-13.6 104.6-14.7 3.5-.1 6.2-2.8 6.4-6.3 1.1-18.5 2.6-74-14.8-106.5-1.5-3-5-4.4-8.1-3.2zM139.6 298c-5.3 14.4-25.6 63.7-73.9 113.9-3.7 3.8-1.6 10.3 3.6 11.2 21.2 3.9 69.2 11.4 138.5 13.9 2.1.1 4.2-.9 5.5-2.6 8-10.2 36-51 60.5-150.1 1.2-4.8-3-9.1-7.8-8.2-23.4 4.6-81.4 15.5-120.5 17.5-2.7.2-5 1.9-5.9 4.4zM317.7 280.1c-4.3 20.5-20 89.7-44.9 148.3-1.7 4.1 1 8.8 5.4 9.3 13.9 1.5 49.8 1.1 140.8-14.6 2.3-.4 4.3-2 5.1-4.2 5.6-14.6 25.7-73.1 23.9-152.7-.1-4.3-4.1-7.4-8.3-6.4-21.3 5.2-79.7 18.4-114.9 15.2-3.3-.5-6.4 1.7-7.1 5.1z'
    //         />
    //       </svg>
    //       <span>100% UVA/UVB protection</span></div>
    //       <div class='feature hydrophobic' style='display: none;'>
    //         <svg 
    //         version='1.1' id='prefix__Layer_1' xmlns='http://www.w3.org/2000/svg' x='0' y='0' viewBox='0 0 512 512' xml:space='preserve'>
    //             <style>.prefix__st0{fill:#fff}</style><path d='M359.2 236.3c1.2.5 2.6.9 3.9 1.2-1.3-.7-2.6-1.2-3.9-1.2zM363 237.5c.2.1.3.2.5.3l.1-.1c-.2-.1-.4-.1-.6-.2zM423.3 296.8v2.7h.1c0-.9-.1-1.8-.1-2.7z'/><path d='M402.4 249.6l.2-.4c-2.4-.1-5-3-8-4.9-1.1 2.1-3.1 3.3-6.3 2.5 3.3.8 5.2-.4 6.3-2.5-.2-.1-.3-.2-.5-.3-1.3-.3-2.6-.7-3.9-1.2-.1 0-.2-.1-.2-.1h-41.3l-22.6-39.2c-1.4-2.4-3.9-3.9-6.7-3.9h-48.6c-2.2 0-4.3 1-5.8 2.6-11.5-17.8-24.3-34.4-35.6-46.7-15.9-17.3-19.9-17.3-22.4-17.3-2.7 0-6.2.9-16.5 11.2-6.6 6.6-14.4 15.6-21.9 25.2-11.5 14.8-32.9 44.7-46 78.4-1.4 3.5.4 7.4 3.9 8.8 3.5 1.4 7.4-.4 8.8-3.9 8.9-22.7 23.7-48.4 41.8-72.1 13.9-18.2 25.2-29.5 29.9-33.2 8 6.1 30.6 31.1 50.1 62.8l-15.7 27.3h-45.2c-2.8 0-5.3 1.5-6.7 3.9l-24.3 42.1c-1.4 2.4-1.4 5.3 0 7.7l24.3 42.1c1.4 2.4 3.9 3.9 6.7 3.9h48.6c2.8 0 5.3-1.5 6.7-3.9l22.6-39.2H289c.2 2.3.3 4.7.3 7 0 45.3-36.8 82.1-82.1 82.1s-82.1-36.8-82.1-82.1c0-5.8.6-12.1 1.7-18.5.7-3.7-1.8-7.2-5.5-7.9-3.7-.7-7.2 1.8-7.9 5.5-1.3 7.2-2 14.3-2 20.9 0 52.8 42.9 95.7 95.7 95.7 52.8 0 95.7-42.9 95.7-95.7 0-2.3-.1-4.6-.2-7h13.5l.9 1.6c1.9 3.3 6 4.4 9.3 2.5 3.2-1.9 4.4-6 2.5-9.3l-.9-1.6c6.7-11.5 14.3-24.7 20.9-36.2h41.9c6.7 11.5 14.3 24.7 20.9 36.2-6.7 11.5-14.3 24.7-20.9 36.2h-41.9l-4.2-7.2c-1.9-3.3-6-4.4-9.3-2.5-3.2 1.9-4.4 6-2.5 9.3l5.9 10.1c1.4 2.4 3.9 3.9 6.7 3.9h45.2l.3.5c3.1-2.8 7.2-5.2 11.6-7.3 6.7-11.5 14.2-24.6 20.8-36v-2.7c-.2-3.6-.3-7.2-.1-10.5-.2 3.3-.1 6.9.1 10.5v-11c-6.7-11.5-14.3-24.7-20.9-36.2z'/><path d='M241.6 328.8h-41.9l-20.9-36.2 20.9-36.2h41.9l20.9 36.2zM274.3 285.8l-21-36.3 11.9-20.5c10 18.1 18.2 37.7 22 56.8h-12.9zM316.2 285.7h-15.1c-4.5-24.8-15.8-49.6-28-70.4l1.2-2h41.9l20.9 36.2c-6.7 11.5-14.3 24.7-20.9 36.2z'/><path class='prefix__st0' d='M199.7 256.4l-20.9 36.2 20.9 36.2h41.9l20.9-36.2-20.9-36.2zM253.3 249.5l21 36.3h12.9c-3.8-19.1-12-38.7-22-56.8l-11.9 20.5z'/><path d='M274.3 213.3l-1.2 2c12.2 20.8 23.5 45.6 28 70.4h15.1c6.6-11.5 14.2-24.7 20.9-36.2l-20.9-36.2h-41.9z' fill='#fff8f8'/><path d='M169.5 239.4c8-14.6 18.2-30.1 29.8-44.8 2.3-2.9 1.8-7.2-1.1-9.5-2.9-2.3-7.2-1.8-9.5 1.1-11.4 14.4-22.3 30.6-31.1 46.7-1.8 3.3-.6 7.4 2.7 9.2 3.2 1.7 7.4.5 9.2-2.7z'/>
    //         </svg>
    //       <span>Hydrophobic coating</span></div>
    //       <div class='feature aspheric' style='display: none;'>
    //         <svg 
    //         xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M261.8 32.7c-126 0-228.1 102-228.1 228.1 0 126 102 228.1 228.1 228.1 126 0 228.1-102 228.1-228.1S387.9 32.7 261.8 32.7zm188.3 322C332.4 151 341.8 166.7 338.4 162.5h109.4c31.2 58.8 32.8 131.1 2.3 192.2zM327.2 344c-5.4 9.2-15.3 15-26 15h-78.7c-10.7 0-20.7-5.8-26-15l-39.4-68.2c-5.3-9.3-5.3-20.8 0-30l39.4-68.2c5.3-9.3 15.3-15 26-15h78.7c10.7 0 20.7 5.8 26 15l39.4 68.2c5.3 9.3 5.3 20.8 0 30-5 8.6-35.1 60.7-39.4 68.2zM249 50.9l-3.3 5.7c-2.5 4.3-1 9.7 3.3 12.2 4.2 2.4 9.7 1 12.2-3.3l8.5-14.8c70.1 2.6 131.1 39.3 167.5 94.1-.1 0-216.8-.3-222.2.6l10.6-18.3c2.5-4.3 1-9.7-3.3-12.2s-9.7-1-12.2 3.3c-20.9 36.1-69.1 118.5-71.8 125.4l-54.7-94.7c35.6-56.1 96-93.9 165.4-98zM73.6 166.8c54 93.8 107 186.4 111.6 192.2H75.8c-31.1-58.8-32.7-131.1-2.2-192.2zm12.8 210c235.2 0 216.9.2 222.2-.6l-54.5 94.3c-.1.1-.1.2-.2.3-70.1-2.5-131.1-39.2-167.5-94zm188.2 93.8C392.2 267 383.3 282.9 385.2 278l54.7 94.7c-35.5 56.1-95.9 93.8-165.3 97.9z'/><path d='M245.1 57.8l-35.8 61.8 16.9 6.4 37.2-64.3z'/>
    //     </svg>
    //       <span>Aspheric Lens Design</span></div>
    //     </div>
    //     <div class='priceselector'>
    //       <div class='ps-outercircle'>
    //         <div class='ps-innercircle'></div>
    //       </div>
    //       <span class='price'>" . $package_json->price . "</span>
    //     </div>
    //   </div>
    // </label>";
    //           $datagrid_row['data'][2] = $html;
    //     }

    // flag for displaying error message
    $has_attr_section = false;

    // find the fields within the hidden section and populate with corresponding attribute value
    foreach ($form_fields as $field) {
        if ($field->section_id == $attr_section_id) {
            $has_attr_section = true;

            $label = 'pa_' . $field->label;
            // check existence of  the attribute
            if (isset($attributes[$label])) {
                // get value for the attribute
                $value = $attributes[$label]["options"][0];
                //  Change the field default value
                $field->meta->default_value = (int)$value;
                // for debugging purposes
                //echo $label . ' = ' . $value . '<br>';
            } else {
                // echo 'attribute ' . $label . ' not found<br>';
                continue;
            }
        } elseif ($field->label == "Product Price") {
            $field->meta->default_value = (int)$product->get_price();
        }
    }

    if (!$has_attr_section) {
        echo 'please check the id of your attribute section';
    }

    return $form;
});

// Function to populate product attribute swatches - used in templates
if (!function_exists('print_attribute_radio')) {
    function print_attribute_radio($checked_value, $value, $label, $name, $cyl = "")
    {
        global $product;

        $input_name = 'attribute_' . esc_attr($name);
        $esc_value = esc_attr($value);
        $id = esc_attr($name . '_v_' . $value . $product->get_id() . $cyl); //added product ID at the end of the name to target single products
        $checked = checked($checked_value, $value, false);
        $filtered_label = apply_filters('woocommerce_variation_option_name', $label, esc_attr($name));
        printf('<div><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s">%5$s</label></div>', $input_name, $esc_value, $id, $checked, $filtered_label);
    }
}

/*
    * Checkout Page
    */

// Edit PayPal order to be Capture or Intent
add_filter('ppcp_create_order_request_body_data', function ($data) {
    file_put_contents('ppc_order.txt', json_encode($data));
    // $data["intent"] = "CAPTURE";
    // file_put_contents('ppc_order.txt', json_encode($data), FILE_APPEND);
    return $data;
});

/*
    * Pay Order Page (redirected to for hometrial checkout) and Thank You Page
    */

add_filter('woocommerce_get_order_item_totals', function ($total_rows, $order, $tax_display) {
    // Check if order is hometrial order
    $is_hometrial = get_post_meta($order->get_id(), 'order_type', true) == "HOMETRIAL" ? true : false;
    if ($is_hometrial) {
        // insert text why total amount is 0
        $insert_array = ["hometrial_text" => array('label' => 'Hometrial offer (no costs if sent back in 7 days)', 'value' => '100%'),];
        array_splice($total_rows, 2, 0, $insert_array);
        // remove payment method
        array_splice($total_rows, 1, 1);
    }
    return $total_rows;
}, 10, 3);

add_filter( 'woocommerce_get_formatted_order_total', function($formatted_total, $order){
    // Check if order is hometrial order
    $is_hometrial = get_post_meta($order->get_id(), 'order_type', true) == "HOMETRIAL" ? true : false;
    if ($is_hometrial) {
        // set total amount to 0
        $formatted_total = wc_price( 0, array( 'currency' => $order->get_currency() ) );
    }
    return $formatted_total;
}, 10, 2 );

// add_action( 'wc_stripe_order_payment_complete', function($charge, $order){
//     var_dump("trigger action stripe payment complete");
//     if(get_post_meta($order->get_id(), 'order_type', true) == "HOMETRIAL"){
//         wc_stripe_restore_cart_after_product_checkout();
//     }
// }, 10, 2 );

add_action( 'wc_stripe_before_process_payment', function($order, $payment_gateway_stripe){
    update_user_meta(wp_get_current_user()->ID, 'cart-temp-ht', WC()->cart->get_cart());
}, 10, 2 );

add_action( 'woocommerce_thankyou_stripe_cc', function($order_id){
    // global $woocommerce;
    $cart_content = get_user_meta(wp_get_current_user()->ID, 'cart-temp-ht', true);
    // print_r($cart_content);
    // foreach ( $cart_content as $cart_item_key => $values ){
    //     $id =$values['product_id'];
    //     $quant=$values['quantity'];
    //     // print_r($id);
    //     // print_r($quant);
    //     // print_r(WC()->cart->add_to_cart( $id, $quant));
        
    //     // $woocommerce->cart->add_to_cart( $id, $quant);
    // }

    WC()->cart->set_cart_contents($cart_content);
    // print_r(WC()->cart->get_cart());
    // $setcookies = array(
    //     'woocommerce_items_in_cart' => '1',
    //     'woocommerce_cart_hash'     => WC()->cart->get_cart_hash(),
    // );
    // wc_print_r($setcookies);
    // foreach ( $setcookies as $name => $value ) {
    //     if ( ! isset( $_COOKIE[ $name ] ) || $_COOKIE[ $name ] !== $value ) {
    //         wc_setcookie( $name, $value );
    //     }
    // }
    WC()->cart->set_session();
} );

/********************
    Home Trial Section
    ? Everything that affects home trial functionality

 */
add_filter('wsf_pre_render_7', function ($form) {
    if (!is_user_logged_in()) return $form;
    $user_id = get_current_user_id();
    $popfields = array(
        'billing_first_name' => '838',
        'billing_last_name'  => '839',
        'billing_email'      => '841',
        'billing_phone'      => '842',
        'billing_address_1'  => '825',
        'billing_address_2'  => '826',
        'billing_city'       => '827',
        'billing_postcode'   => '829',
        'shipping_address_1'  => '832',
        'shipping_address_2'  => '833',
        'shipping_city'       => '834',
        'shipping_postcode'   => '836',
    );
    foreach ($popfields as $metafield => $field_id) {
        $metavalue = get_user_meta($user_id, $metafield, true);
        if ($metavalue) {
            $field = wsf_form_get_field($form, $field_id);
            $field->meta->default_value = $metavalue;
        }
    }
    return $form;
});
add_filter('woocommerce_admin_order_buyer_name', function ($buyer, $order) {
    $order_type = get_post_meta($order->get_id(), 'order_type', true);
    if (isset($order_type) && $order_type) {
        return $buyer . ' - ' . $order_type;
    } else {
        return $buyer;
    }
}, 10, 2);

// Hook to be called on ws form submit //TODO: on hold for client feedback
add_filter('ls_submit_hometrial', function ($form, $submit) {

    //// get form field values
    $address_billing = array(
        'first_name' => $submit->meta['field_838']['value'],
        'last_name'  => $submit->meta['field_839']['value'],
        'email'      => $submit->meta['field_841']['value'],
        'phone'      => $submit->meta['field_842']['value'],
        'address_1'  => $submit->meta['field_825']['value'],
        'address_2'  => $submit->meta['field_826']['value'],
        'city'       => $submit->meta['field_827']['value'],
        'postcode'   => $submit->meta['field_829']['value'],
        // 'country'   => $submit->meta['field_830']['value'],
    );
    if ($submit->meta['field_831']['value'] === false) {
        $address_shipping = array(
            'first_name' =>  $address_billing['first_name'],
            'last_name'  =>  $address_billing['last_name'],
            'address_1'  =>  $address_billing['address_1'],
            'address_2'  =>  $address_billing['address_2'],
            'city'       =>  $address_billing['city'],
            'postcode'   =>  $address_billing['postcode'],
            // 'country'   => $submit->meta['field_837']['value'],
        );
    } else {
        $address_shipping = array(
            'first_name' => $submit->meta['field_838']['value'],
            'last_name'  => $submit->meta['field_839']['value'],
            'address_1'  => $submit->meta['field_832']['value'],
            'address_2'  => $submit->meta['field_833']['value'],
            'city'       => $submit->meta['field_834']['value'],
            'postcode'   => $submit->meta['field_836']['value'],
            // 'country'   => $submit->meta['field_837']['value'],
        );
    }

    //// get products from hometrial list
    $ht_instance = HomeTrial\Frontend\Manage_Hometrialist::instance();
    $ht_products = $ht_instance->get_hometrialist_products();

    //// create wc order and add hometrial list items
    $order = wc_create_order();
    foreach ($ht_products as $ht_product) {
        $order->add_product(wc_get_product($ht_product), 1);
        $ht_instance->remove_product($ht_product);
    }
    foreach ($order->get_items() as $item_id => $item) {
        // $item->set_subtotal(0);
        // $item->set_total(0);
        // wc_update_order_item_meta($item_id,'price',0);
        $item->save();
    }
    //// set order data from form fields
    $order->set_address($address_billing, 'billing');
    $order->set_address($address_shipping, 'shipping');

    //// check if user exists and create new user if not
    if (!is_user_logged_in()) {
        $user_id = false;
        $email = email_exists($order->billing_email);
        $user = username_exists($order->billing_email);
        if ($email) {
            $user_id = $email;
        } else if ($user) {
            $user_id = $user;
        } else {
            $random_password = wp_generate_password();
            $user_id = wp_create_user($order->billing_email, $random_password, $order->billing_email);

            // Send WooCommerce "Customer New Account" email notification with the password
            $mailtemplate = new WC_Email_Customer_New_Account();
            $mailtemplate->trigger($user_id, $random_password, true);
        }
        // Auto login
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
    }

    //// update user metadata
    //user’s billing data
    update_user_meta($user_id, 'first_name', $order->billing_first_name);
    update_user_meta($user_id, 'last_name', $order->billing_last_name);
    update_user_meta($user_id, 'billing_address_1', $order->billing_address_1);
    update_user_meta($user_id, 'billing_address_2', $order->billing_address_2);
    update_user_meta($user_id, 'billing_city', $order->billing_city);
    update_user_meta($user_id, 'billing_company', $order->billing_company);
    update_user_meta($user_id, 'billing_country', $order->billing_country);
    update_user_meta($user_id, 'billing_email', $order->billing_email);
    update_user_meta($user_id, 'billing_first_name', $order->billing_first_name);
    update_user_meta($user_id, 'billing_last_name', $order->billing_last_name);
    update_user_meta($user_id, 'billing_phone', $order->billing_phone);
    update_user_meta($user_id, 'billing_postcode', $order->billing_postcode);
    update_user_meta($user_id, 'billing_state', $order->billing_state);

    // user's shipping data
    update_user_meta($user_id, 'shipping_address_1', $order->shipping_address_1);
    update_user_meta($user_id, 'shipping_address_2', $order->shipping_address_2);
    update_user_meta($user_id, 'shipping_city', $order->shipping_city);
    update_user_meta($user_id, 'shipping_company', $order->shipping_company);
    update_user_meta($user_id, 'shipping_country', $order->shipping_country);
    update_user_meta($user_id, 'shipping_first_name', $order->shipping_first_name);
    update_user_meta($user_id, 'shipping_last_name', $order->shipping_last_name);
    update_user_meta($user_id, 'shipping_method', $order->shipping_method);
    update_user_meta($user_id, 'shipping_postcode', $order->shipping_postcode);
    update_user_meta($user_id, 'shipping_state', $order->shipping_state);

    update_post_meta($order->id, '_customer_user', get_current_user_id());
    update_post_meta($order->id, 'order_type', "HOMETRIAL");

    $order->calculate_totals();
    // $order->set_total(0);

    // $order->set_id($order->id . "-HT");
    $order->save();

    // update_post_meta( $order->id, '_payment_method', 'paypal' );
    // update_post_meta( $order->id, '_payment_method_title', 'PayPal' );
    // update_post_meta( $order->id, 'intent', 'AUTHORIZE' );

    /*
        *
        * PAYMENT PROCESS - Paypal or Stripe
        *
        */

    $payment_gateway_stripe = WC()->payment_gateways->payment_gateways()['stripe_cc'];
    $payment_gateway_stripe->settings['charge_type'] = 'authorize';
    $order->set_payment_method($payment_gateway_stripe);
    $stripe_gateway = new WC_Payment_Gateway_Stripe_CC();
    // $stripe_gateway->process_payment($order->id);
    $order->save();

    /* Put the email sending part and maybe also account creation inside of if successful payment */

    // send email to customer
    global $woocommerce;
    $mailer = $woocommerce->mailer();

    $items = "";

    foreach ($order->get_items() as $item) {
        $items = $items . $item->get_name() . "<br>";
    }

    $checkout_url = $order->get_checkout_payment_url();

    $message_body = sprintf("Hi %s,<br><br>We received your order with the order number %s.<br>The items associated with this order are:<br><br> %s <br>If you haven't done so yet, confirm your order by completing the payment process here: <a href='%s'>%s</a> ", $order->get_formatted_billing_full_name(), $order->get_order_number(), $items, $checkout_url, $checkout_url);
    $message = $mailer->wrap_message(
        // Message head and message body.
        sprintf(__('Specsstore Order created')),
        $message_body
    );

    // Client email, email subject and message.
    $mailer->send($order->get_billing_email(), sprintf(__('Specsstore Order created')), $message);

    $message = array(
        'message' => array(
            'message' => "Your order has been submitted. Please confirm your order by completing the payment process: <a href='" . $checkout_url . "'>" . $checkout_url . "</a> ",
            'type' => 'success',
            'method' => 'before',
            'form_hide' => true,
            'clear' => true,
        )
    );
    $redirect = array(
        'redirect' => array(
            'url' => $checkout_url
        )
    );
    return $redirect;

    /*
        // $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
        // $payment_method = 'ppcp-gateway';
        $order_id = $order->id;
		// if ( ! isset( $available_gateways[ $payment_method ] ) ) {
        //     return;
		// }
        

		// Store Order ID in session so it can be re-used after payment failure.
		// WC()->session->set( 'order_awaiting_payment', $order_id );

		// Process Payment.
        // $paypal_gateway = new WC_Gateway_Paypal();

        $payment_method = "ppcp-gateway";

        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		if ( ! isset( $available_gateways[ $payment_method ] ) ) {
			return;
		}

		// Store Order ID in session so it can be re-used after payment failure.
		// WC()->session->set( 'order_awaiting_payment', $order_id );
        WC()->session_start;

		// Process Payment.
		$result = $available_gateways[ $payment_method ]->process_payment( $order_id );
        try {
            // $result = $paypal_gateway->process_payment($order_id);
            if(isset($result['result']) && 'success' === $result['result']){


                $result['order_id'] = $order_id;

                $result = apply_filters( 'woocommerce_payment_successful_result', $result, $order_id );

                if ( ! wp_doing_ajax() ) {
                    // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
                    wp_redirect( $result['redirect'] );
                    exit;
                }

                wp_send_json( $result );

                // $result = apply_filters( 'woocommerce_payment_successful_result', $result, $order_id );
                // if ( ! wp_doing_ajax() ) {
                //     // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
                //     wp_redirect( $result['redirect'] );
                //     exit;
                // }
    
                // wp_send_json( $result );
                
                
            }else{
                $order->update_status('failed');
                $message = array(
                    'message' => array(
                        'message' => "There was a problem processing your order.",
                        'type' => 'danger',
                        'method' => 'before',
                        'form_hide' => true,
                        'clear' => true,
                    )
                );
            }
            
            return $message;
        } catch (\Throwable $th) {
            $order->update_status('failed',$th);
            $message = array(
                'error' => array(
                    'message' => __("There was a problem processing your order."),
                    'type' => 'danger',
                    'method' => 'before',
                    'form_hide' => true,
                    'clear' => true,
                )
            );
            return $message;
        }
        */
}, 10, 2);


/********************
    Reglaze Section
    ? Everything that affects reglaze functionality

 */

// Hook to be called on ws form submit //TODO: on hold for client feedback
add_filter('ls_submit_reglaze', function ($form, $submit) {

    $address_shipping = array(
        'first_name' => $submit->meta['field_805']['value'],
        'last_name'  => $submit->meta['field_806']['value'],
        'address_1'  => $submit->meta['field_811']['value'],
        'address_2'  => $submit->meta['field_812']['value'],
        'city'       => $submit->meta['field_813']['value'],
        'postcode'   => $submit->meta['field_815']['value'],
    );

    $address_billing = array_merge($address_shipping, array(
        'email'      => $submit->meta['field_807']['value'],
        'phone'      => $submit->meta['field_808']['value'],
    ));

    $quantity = $submit->meta['field_810']['value'];
    $comments = $submit->meta['field_817']['value'];

    $order = wc_create_order();
    // reglaze placeholder product has id 1489
    $order->add_product(wc_get_product('1489'), $quantity);
    $order->set_address($address_billing, 'billing');
    $order->set_address($address_shipping, 'shipping');

    // $order->add_meta_data('Comments', $comments);
    $order->add_order_note($comments, 1, false);
    update_post_meta($order->id, 'order_type', "REGLAZE");
    // print_r($submit);

    // $message = array(
    //     'message' => array(
    //         'message' => json_encode($submit),
    //         'type' => 'success',
    //         'method' => 'before',
    //         'form_hide' => true,
    //         'clear' => true,
    //     )
    // );

    // return '<div>Hi there, this is an injected div</div>';

    // wp_redirect('http://localhost/wordpress/woocommerce1/shop/');
    // exit;
    // <script>
    //     console.log('<?php echo json_encode($submit); ');
    // </script>

    return true;
}, 10, 2);
