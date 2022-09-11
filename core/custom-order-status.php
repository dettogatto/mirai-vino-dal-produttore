<?php

// Register new status
function mirai_vdp_register_shipping_order_status() {
  register_post_status( 'wc-shipping', array(
    'label'                     => 'In spedizione',
    'public'                    => true,
    'exclude_from_search'       => false,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'In spedizione (%s)', 'In spedizione (%s)' )
  ) );
}
add_action( 'init', 'mirai_vdp_register_shipping_order_status' );

// Add to list of WC Order statuses
function mirai_vdp_add_shipping_to_order_statuses( $order_statuses ) {

  $new_order_statuses = array();

  // add new order status after processing
  foreach ( $order_statuses as $key => $status ) {

    $new_order_statuses[ $key ] = $status;

    if ( 'wc-processing' === $key ) {
      $new_order_statuses['wc-shipping'] = 'In spedizione';
    }
  }

  return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'mirai_vdp_add_shipping_to_order_statuses' );


/*
* Add your custom bulk action in dropdown
* @since 3.5.0
*/
add_filter( 'bulk_actions-edit-shop_order', 'mirai_vdp_register_bulk_action' ); // edit-shop_order is the screen ID of the orders page

function mirai_vdp_register_bulk_action( $bulk_actions ) {

  $bulk_actions['mark_shipping'] = 'Modifica lo stato in "In spedizione"'; // <option value="mark_shipping">Mark awaiting shipment</option>
  return $bulk_actions;

}

/*
* Bulk action handler
* Make sure that "action name" in the hook is the same like the option value from the above function
*/
add_action( 'admin_action_mark_shipping', 'mirai_vdp_bulk_process_custom_status' ); // admin_action_{action name}

function mirai_vdp_bulk_process_custom_status() {

  // if an array with order IDs is not presented, exit the function
  if( !isset( $_REQUEST['post'] ) && !is_array( $_REQUEST['post'] ) )
  return;

  foreach( $_REQUEST['post'] as $order_id ) {

    $order = new WC_Order( $order_id );
    $order_note = 'That\'s what happened by bulk edit:';
    $order->update_status( 'wc-shipping', $order_note, true ); // "wc-shipping" is the order status name

  }

  // of course using add_query_arg() is not required, you can build your URL inline
  $location = add_query_arg( array(
    'post_type' => 'shop_order',
    'marked_shipping' => 1, // markED_shipping=1 is just the $_GET variable for notices
    'changed' => count( $_REQUEST['post'] ), // number of changed orders
    'ids' => join( $_REQUEST['post'], ',' ),
    'post_status' => 'all'
  ), 'edit.php' );

  wp_redirect( admin_url( $location ) );
  exit;

}

/*
* Notices
*/
add_action('admin_notices', 'mirai_vdp_custom_order_status_notices');

function mirai_vdp_custom_order_status_notices() {

  global $pagenow, $typenow;

  if( $typenow == 'shop_order'
  && $pagenow == 'edit.php'
  && isset( $_REQUEST['marked_shipping'] )
  && $_REQUEST['marked_shipping'] == 1
  && isset( $_REQUEST['changed'] ) ) {

    $message = sprintf( _n( 'Order status changed.', '%s order statuses changed.', $_REQUEST['changed'] ), number_format_i18n( $_REQUEST['changed'] ) );
    echo "<div class=\"updated\"><p>{$message}</p></div>";

  }

}


// Prevent the status "wc-shipping" when DHL label not present
add_action( 'woocommerce_order_status_changed', 'mirai_prevent_order_status_change_shipping', 10, 4 );
function mirai_prevent_order_status_change_shipping( $order_id, $status_from, $status_to, $order ) {
  global $wpdb;
  $shipment = $order->get_meta('wf_dhlexpress_shipment_result');
  if($status_to == "shipping" && !( is_array($shipment) && is_array($shipment["tracking_info"]) && !empty($shipment["tracking_info"]) ) ){
    $query = "UPDATE $wpdb->posts
    SET post_status = 'wc-$status_from'
    WHERE ID = $order_id";
    $wpdb->query($query);

    $note = "ERRORE: non è possibile cambiare lo stato dell'ordine in 'In spedizione' prima di aver generato l'etichetta DHL. Lo stato è stao reimpostato su '$status_from'";
    $order->add_order_note( $note );
  }
}


// Automatically set the status to "wc-shipping" when DHL label is generated
// add_action( 'added_post_meta', 'mirai_automatic_order_status_shipping', 10, 4 );
// add_action( 'updated_post_meta', 'mirai_automatic_order_status_shipping', 10, 4 );
// function mirai_automatic_order_status_shipping( $meta_id, $post_id, $meta_key, $meta_value )
// {
//   if($meta_key == "wf_dhlexpress_shipment_result"){
//     $order = new WC_Order($post_id);
//     $shipment = $meta_value;
//     if(is_array($shipment) && is_array($shipment["tracking_info"]) && !empty($shipment["tracking_info"])){
//       $order->update_status( 'shipping', "Lo stato dell'ordine è stato impostato su In Spedizione a seguito della generazione dell'etichetta DHL", true );
//     }
//   }
// }
