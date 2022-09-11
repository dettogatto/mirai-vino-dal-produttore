<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 *  Add a custom email to the list of emails WooCommerce should load
 *
 * @since 0.1
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function mirai_add_vendor_label_email_notification( $email_classes ) {

    // include our custom email class
    require( __DIR__ . '/vendor-dhl-label-email.php' );

    // add the email class to the list of email classes that WooCommerce loads
    $email_classes['VendorDhlLabel'] = new VendorDhlLabel();

    return $email_classes;

}
add_filter( 'woocommerce_email_classes', 'mirai_add_vendor_label_email_notification' );


add_filter( 'woocommerce_locate_template', 'mirai_vdp_vendor_new_order_email', 1, 999 );
function mirai_vdp_vendor_new_order_email( $template, $template_name, $template_path ) {
  global $woocommerce;

  if($template_name == "emails/vendor-new-order.php"){
    $path = __DIR__ . "/../templates/emails/vendor-new-order.php";
    $template = $path;
  }


  return $template;

}
