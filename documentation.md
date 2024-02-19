
# Documentation specsstore

edited/added files and folders in the wordpress folder structure:

- [/wp-content/themes/divi-child](#divi-child):
contains the child theme to Divi parent theme

- [/wp-content/plugins/hometrial](#hometrial):
contains functionality of the hometrial plugin

- [/wp-content/plugins/themify-wc-product-filter](#themify-wc-product-filter): 
contains functionality of the product filter plugin

- [/wp-content/plugins/wishsuite](#wishsuite): 
contains functionality of the wishlist plugin

All added or edited folders have been kept under git version control


## divi-child

- [style.css](#stylecss)
- [functions.php](#functionsphp)
- includes
    - [contact-info.php](#contact-infophp)
    - [custom-options.php](#custom-options)
    - [shortcodes.php](#shortcodesphp)
    - [theme-setup.php](#theme-setupphp)
- js
    - [add-to-cart-variation.js](#add-to-cart-variationjs)
    - [menu-filter.js](#menu-filterjs)
- [woocommerce](#woocommerce)
    - archive-product-pwb-brand.php
    - related-product-content.php
    - related-product-content.php
    - taxonomy-pwb-brand.php
    - cart
        - cart.php
    - checkout
        - form-pay.php
        - payment.php
    - loop
        - orderby.php
    - single-product
        - category.php
        - measurements.php
        - related.php
        - related.php
        - add-to-cart
            - cyl-add-to-cart-button.php
            - cyl-form.php
            - cyl-variable.php
            - variation-add-to-cart-button.php
        - tabs
            - tabs.php

### style.css

defines and overrides styling properties

- override some divi properties
    - hide sidebar
- font sizes
    - breadcrumbs
    - mega menu
- menu icons

- mega menu

- landing page
    - frame shapes on landingpage
    - brand carousel on landingpage
    - landingpage footer
- shop page
    - fix number of columns in shop
    - product tiles on shop page
    - filter and sort
- basket page
- product page
    - actions
    - details
    - measurements
    - variations
    - specifications

    - choose your lenses form
        - floating cart
        - use page
        - packages page
        - lens type page
        - popups

### functions.php

loads scripts and parent theme, defines functions, filters and hooks

- general
    - load parent theme (divi)
    - load scripts 
        - js/add-to-cart-variation.js
        - js/menu-filter.js
    - add font awesome
    - include additional php files
        - includes/contact-info.php
        - includes/custom-options.php
        - includes/shortcodes.php

    - mega menu filters
        - dynamic links
        - insert men/woman image
        - insert frame shapes with icons
        - insert brands for the category

    - remove menu items from wordpress dashboard
        - Posts
        - Projects

    - disable update prompt for customised plugins
        - product filter
        - wishsuite

- woocommerce related

    - make all products cancelable by customer
    - update cart icon in menu bar

    - shop page
        - edit number of products per shop page
        - action buttons for products
        - remove default order and sort buttons
        - custom positioning of price and rating per product
        - add tooltip for product title

    - product page
        - image size for product gallery
        - add to cart button text
        - category text under product title
        - add measurements section before action buttons
        - remove default add to cart button
        - add product attributes to ws form
        - populate attribute swatches
    - checkout page
        - custom checkout page for hometrial orders
    - home trial
        - prepopulate ws billing form
        - handle form submit
            - create new user if necessary
            - create order
            - handle payment process
            - send email to customer
    - reglaze page
        - handle for submit
            - create order

### theme-setup.php

includes the other php files in functions.php
    
### contact-info.php

handles global variables that can be sused in the frontend

- add settings page "contact info"
- handle placeholders for woocommerce email templates

### custom-options

holds information like the icon and details for the measurement tooltips, so it can be changed in the dashboard

- add settings page "custom options"
- add section and fields

### shortcodes.php

handles some custom shortcodes used throughout the website

- hometrial_login_request: returns login form and ws form depending on if user is logged in or not

- ls_global_contact: handles the shortcode used for the global contact options that can be set in the settings subpage

- ls_home_frameshapes: returns the frameshapes, used in the homepage

- ls_woo_cart_icon: returns the basket menu icon

- ls-archive-page-brand-banner: returns brand banner

- ls-archive-page-brand-title: returns brand title

- ls-archive-page-brand-description: returns brand description

- ls_product_specifications: returns product specifications, used on the product page

- ls_floating_cart: returns the floating cart, used on the product page

- ls_floating_cyp: returns floating cart, customised for reglaze

- ls_ws_form: returns ws form, used on the product page

- ls_archive_page_url: returns archive page url of a specified category

- customise additional class to support shortcodes in certain divi module fields

### add-to-cart-variation.js

handles the selection of different variations 

### menu-filter.js

handles mobile view of the menu

### woocommerce

in this folder reside all overriden woocommerce template files



## hometrial

Plugin for the hometrial feature. This Plugin is based on WishSuite 1.3.0 by https://hasthemes.com/.

changes from the original plugin:

- renaming and icon change
- make it installable next to wishsuite
- add a maximum number of items
- some minor style adjustments
- adding tooltips to the buttons
- minor functionality adjustements


## themify-wc-product-filter

Plugin for the product filter. This Plugin is based on Themify Product Filter 1.3.9 by https://themify.me.

changes from the original plugin:

- display icons next to the label for specific attributes
- add guide buttons for certain attributes
- major style adjustements
- making it responsive
- minor functionality adjustements


## wishsuite

Plugin for the wishsuite feature. This plugin is based on WishSuite 1.3.0 by https://hasthemes.com/.

- renaming and icon change
- style adjustements
- tooltips for buttons
- minor functionality adjustements