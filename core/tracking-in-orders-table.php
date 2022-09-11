<?php

add_filter( 'manage_edit-shop_order_columns', function( $columns ) {
  $columns['tracking'] = 'Tracking';
  return $columns;
}, 999);

add_action( 'manage_shop_order_posts_custom_column', function( $column ) {
  global $post;

  if ( 'tracking' === $column ) {

    $order = wc_get_order( $post->ID );
    $result = [];
    $shipment = $order->get_meta("wf_dhlexpress_shipment_result");
    if($shipment && is_array($shipment["tracking_info"])){
      foreach ($shipment["tracking_info"] as $tracking) {
        $result[] = '<a href="' . $tracking["tracking_link"] . '" target="_blank">'.$tracking["tracking_id"].'</a>';
      }
    }

    if(empty($result)){
      echo("-");
    } else {
      echo(implode('<br>', $result));
    }

  }
} );
