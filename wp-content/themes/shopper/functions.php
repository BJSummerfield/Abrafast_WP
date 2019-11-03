<?php
/**
 * Shopper functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Shopper
 */

/**
 * Assign the shopper version to a var
 */
$shopper_theme              = wp_get_theme( 'shopper' );
$shopper_version = $shopper_theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {

	$content_width = 980; /* pixels */
}

$shopper = (object) array(
	'version' => $shopper_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require_once 'inc/class-shopper.php',
	'customizer' => require_once 'inc/customizer/class-shopper-customizer.php',
);


require_once 'inc/shopper-functions.php';
require_once 'inc/shopper-template-hooks.php';
require_once 'inc/shopper-template-functions.php';
require_once 'inc/customizer/include-kirki.php';
require_once  'inc/customizer/class-shopper-pro-kirki.php';

if ( is_admin() ) {

	$shopper->admin = require 'inc/admin/class-shopper-admin.php';
}

/**
 * All for WooCommerce functions
 */
if ( shopper_is_woocommerce_activated() ) {

	$shopper->woocommerce = require_once 'inc/woocommerce/class-shopper-woocommerce.php';

	require_once 'inc/woocommerce/shopper-wc-template-hooks.php';
	require_once 'inc/woocommerce/shopper-wc-template-functions.php';
}

/* Increase Woocommerce Variation Threshold */
function wc_ajax_variation_threshold_modify( $threshold, $product ){
  $threshold = '1111';
  return  $threshold;
}
add_filter( 'woocommerce_ajax_variation_threshold', 'wc_ajax_variation_threshold_modify', 10, 2 );

//Hide Price Range for WooCommerce Variable Products
add_filter( 'woocommerce_variable_sale_price_html',
'lw_variable_product_price', 10, 2 );
add_filter( 'woocommerce_variable_price_html',
'lw_variable_product_price', 10, 2 );

function lw_variable_product_price( $v_price, $v_product ) {

// Product Price
$prod_prices = array( $v_product->get_variation_price( 'min', true ),
                            $v_product->get_variation_price( 'max', true ) );
$prod_price = $prod_prices[0]!==$prod_prices[1] ? sprintf(__('From: %1$s', 'woocommerce'),
                       wc_price( $prod_prices[0] ) ) : wc_price( $prod_prices[0] );

// Regular Price
$regular_prices = array( $v_product->get_variation_regular_price( 'min', true ),
                          $v_product->get_variation_regular_price( 'max', true ) );
sort( $regular_prices );
$regular_price = $regular_prices[0]!==$regular_prices[1] ? sprintf(__('From: %1$s','woocommerce')
                      , wc_price( $regular_prices[0] ) ) : wc_price( $regular_prices[0] );

if ( $prod_price !== $regular_price ) {
$prod_price = '<del>'.$regular_price.$v_product->get_price_suffix() . '</del> <ins>' .
                       $prod_price . $v_product->get_price_suffix() . '</ins>';
}
return $prod_price;
}

//Hide “From:$X”
add_filter('woocommerce_get_price_html', 'lw_hide_variation_price', 10, 2);
function lw_hide_variation_price( $v_price, $v_product ) {
$v_product_types = array( 'variable');
if ( in_array ( $v_product->product_type, $v_product_types ) && !(is_shop()) ) {
return '';
}
// return regular price
return $v_price;
}

/**
  * Edit my account menu order
  */

 function my_account_menu_order() {
  $menuOrder = array(
    'orders'             => __( 'Your Orders', 'woocommerce' ),
    // 'downloads'          => __( 'Download', 'woocommerce' ),
    'edit-address'       => __( 'Addresses', 'woocommerce' ),
    'edit-account'      => __( 'Account Details', 'woocommerce' ),
    'customer-logout'    => __( 'Logout', 'woocommerce' ),
    // 'dashboard'          => __( 'Dashboard', 'woocommerce' )
  );
  return $menuOrder;
 }
 add_filter ( 'woocommerce_account_menu_items', 'my_account_menu_order' );


