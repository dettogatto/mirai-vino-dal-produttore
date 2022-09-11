<?php

add_filter( 'woocommerce_locate_template', 'woo_adon_plugin_template', 1, 3 );
function woo_adon_plugin_template( $template, $template_name, $template_path ) {
  global $woocommerce;

  if($template_name == "cart/cart.php"){
    $path = __DIR__ . "/../templates/cart.php";
    $template = $path;
  } elseif ($template_name == "cart/cart-totals.php"){
    $path = __DIR__ . "/../templates/cart-totals.php";
    $template = $path;
  } elseif ($template_name == "cart/cart-shipping.php"){
    $path = __DIR__ . "/../templates/cart-shipping.php";
    $template = $path;
  } elseif ($template_name == "checkout/review-order.php"){
    $path = __DIR__ . "/../templates/checkout-review-order.php";
    $template = $path;
  }

  // var_dump($template);
  // echo("<br>");
  // var_dump($template_name);
  // echo("<br>");
  // var_dump($template_path);

  return $template;
}
