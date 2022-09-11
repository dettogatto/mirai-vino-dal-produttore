<?php

function mirai_vdp_add_meta_box_free_shipping() {
  $screens = [ 'product' ];
  foreach ( $screens as $screen ) {
    add_meta_box(
      'mirai_vdp_free_shipping',                 // Unique ID
      'Free Shipping',                           // Box title
      'mirai_vdp_meta_box_free_shipping_html',   // Content callback, must be of type callable
      $screen                            // Post type
    );
  }
}
add_action( 'add_meta_boxes', 'mirai_vdp_add_meta_box_free_shipping' );

function mirai_vdp_meta_box_free_shipping_html($post){
  $metaName = "_mirai_free_shipping";

  $currentMeta = get_post_meta($post->ID, $metaName, true);

  ?>
  <label>
    <h4><input type="checkbox" value="yes" name="mirai_free_shipping" <?= $currentMeta ? "checked" : "" ?>> Spedizione gratuita</h4>
  </label>
  <?php


}


// Save the value
function mirai_vdp_update_free_shipping( $post_id ) {
  $val = $_REQUEST['mirai_free_shipping'];
  $metaName = "_mirai_free_shipping";
  if($val == "yes"){
    update_post_meta( $post_id, $metaName, true);
  } elseif($val == "no" || !$val){
    update_post_meta( $post_id, $metaName, false);
  }
}
add_action( 'save_post',  'mirai_vdp_update_free_shipping' );




// Add custom column to product table
add_filter( 'manage_edit-product_columns', 'mirai_vdp_free_shipping_custom_column',11);
function mirai_vdp_free_shipping_custom_column($columns)
{
  //add columns
  $columns['free_shipping'] = __( 'Free shipping','woocommerce'); // title
  return $columns;
}

// Fill custom column to product table
add_action( 'manage_product_posts_custom_column' , 'mirai_vdp_free_shipping_custom_column_content', 10, 2 );
function mirai_vdp_free_shipping_custom_column_content( $column, $product_id )
{
  global $post;

  $metaName = "_mirai_free_shipping";
  // HERE get the data from your custom field (set the correct meta key below)
  $free_shipping = get_post_meta( $product_id, $metaName, true );

  switch ( $column )
  {
    case 'free_shipping' :
    echo ($free_shipping ? "YES" : "-"); // display the data
    break;
  }
}


// Add field to quick edit
add_action( 'woocommerce_product_quick_edit_end', function(){
  ?>
  <div class="inline-edit-group">
    <label class="alignleft">
      <span class="title"><?php _e('Free shipping', 'woocommerce' ); ?></span>
    </label>
    <input type="checkbox" name="mirai_free_shipping" value="yes" />
  </div>
  <?php

}, 999 );


// Add field to bulk edit
add_action( 'woocommerce_product_bulk_edit_end', function(){
  ?>

  <label>
    <span class="title"><?php _e('Free shipping', 'woocommerce' ); ?></span>
    <span class="input-text-wrap">
      <select class="mirai_free_shipping" name="mirai_free_shipping">
        <option value="no_change">— Nessun Cambiamento —</option>
        <option value="yes">Sì</option>
        <option value="no">No</option>
      </select>
    </span>
  </label>

  <?php
}, 999);
