<?php
/**
* @package Mirai-VDP
*/
/*
Plugin Name: Mirai - VDP
Plugin URI: https://mirai-bay.com
Description: Custom checkout and beyond
Version: 0.0.1
Author: Nicola
Author URI: https://cosmo.cat
Text Domain: mirai-vdp
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
  echo 'Hi dear :)';
  exit;
}

include_once(plugin_dir_path( __FILE__ ) . "core/custom-cart-loader.php");
include_once(plugin_dir_path( __FILE__ ) . "core/custom-order-status.php");
include_once(plugin_dir_path( __FILE__ ) . "core/custom-user-meta.php");
include_once(plugin_dir_path( __FILE__ ) . "core/shortcodes.php");
include_once(plugin_dir_path( __FILE__ ) . "core/tracking-in-orders-table.php");
include_once(plugin_dir_path( __FILE__ ) . "core/elementor-query-filters.php");
include_once(plugin_dir_path( __FILE__ ) . "core/info-premi.php");


include_once(plugin_dir_path( __FILE__ ) . "core/email-loader.php");

// FREE SHIPPING
include_once(plugin_dir_path( __FILE__ ) . "core/free-shipping-product.php");
if( is_admin() ){
  add_action('admin_enqueue_scripts', function() {
    wp_enqueue_script( 'mirai-vdp-admin-free-shipping', plugin_dir_url( __FILE__) . '/assets/admin-free-shipping.js', array ( 'jquery' ), 0.16, true);
  });
}

// IMPORTER
include_once(plugin_dir_path( __FILE__ ) . "core/admin-importer-ajax.php");
if( is_admin() ){
  include_once(plugin_dir_path( __FILE__ ) . "core/admin-importer.php");
  add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script( 'mirai-vdp-importer', plugin_dir_url( __FILE__) . '/assets/admin-importer.js', array ( 'jquery' ), 0.16, true);
    wp_localize_script( 'mirai-vdp-importer', 'ajax_url', admin_url( 'admin-ajax.php' ) );
  });
}

// REDIRECTION in product page
if(is_admin()){
  include_once(plugin_dir_path( __FILE__ ) . "core/redirection.php");
  add_action('admin_enqueue_scripts', function(){
    wp_enqueue_script( 'mirai-vdp-redirection', plugin_dir_url( __FILE__) . '/assets/redirection.js', array ( 'jquery' ), 0.16, true);
    wp_localize_script( 'mirai-vdp-redirection', 'apiData', array(
      "url" => get_rest_url(null, '/redirection/v1/'),
      "nonce" =>  wp_create_nonce( 'wp_rest' )
    ) );
  });
}

// enqueue styles
add_action('wp_enqueue_scripts', function() {
  wp_enqueue_style( 'mirai-vdp', plugin_dir_url( __FILE__) . "/assets/style.css", array(), 0.16 );
});
if(is_admin()){
  add_action('admin_enqueue_scripts', function(){
    wp_enqueue_style( 'mirai-vdp-admin', plugin_dir_url( __FILE__) . "/assets/admin-style.css", array(), 0.16 );
    wp_enqueue_script( 'mirai-vdp-admin-bundles', plugin_dir_url( __FILE__) . '/assets/admin-bundles.js', array ( 'jquery' ), 0.16, true);
  });
}

// enqueue script on cart page
add_action('wp_enqueue_scripts', function() {
  wp_enqueue_script( 'mirai-vdp-cart', plugin_dir_url( __FILE__) . '/assets/cart.js', array ( 'jquery' ), 0.16, true);
});

// AJAX add to cart
include_once(plugin_dir_path( __FILE__ ) . "core/ajax-add-to-cart.php");
add_action('wp_enqueue_scripts', function() {
  wp_enqueue_script('woocommerce-mirai-ajax-add-to-cart', plugin_dir_url(__FILE__) . 'assets/ajax-add-to-cart.js', array('jquery'), '', true);
  wp_localize_script( 'woocommerce-mirai-ajax-add-to-cart', 'ajax_url', admin_url( 'admin-ajax.php' ) );
}, 99);

// Remove Yoast from checkout page because of conflicts
add_action( 'plugins_loaded', function(){
  if(isset($_GET['wc-ajax']) && $_GET['wc-ajax'] == "checkout"){
    remove_action( 'plugins_loaded', 'wpseo_init', 14 );
  }
}, 1 );

// remove dokan tax query and show all products in store pages
add_filter('dokan_store_tax_query', function($tax_query){
  return array();
});
