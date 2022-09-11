<?php
add_shortcode( 'print_vendors', function($atts){
  $users = get_users( array(
    'role' => 'seller',
    'meta_key' => 'regione',
    'meta_value' => $atts["regione"]
  ) );
  ob_start();
  ?>
  <div class="mirai-vdp-vendor-list-container">
    <?php
    foreach ($users as $vendor){
      ?>
      <div class="mirai-vdp-vendor">
        <a href="<?= dokan()->vendor->get( $vendor->ID )->get_shop_url(); ?>">
          <?= $vendor->display_name ?>
        </a>
      </div>
      <?php
    }
    ?>
  </div>
  <?php
  return ob_get_clean();
} );


add_shortcode( 'tax_image', function($atts){
  $cat_id = $atts["id"];
  ob_start();
  ?>
  <img src="<?php echo z_taxonomy_image_url($cat_id); ?>" class="mirai-tax-image" />
  <?php
  return ob_get_clean();
} );

add_shortcode( 'tax_image_single', function($atts){
  $term_id = $atts["id"];
  $tax_id = ((array)get_term($term_id))["taxonomy"];
  if(has_term($term_id, $tax_id)){
    ob_start();
    ?>
    <img src="<?php echo z_taxonomy_image_url($term_id); ?>" class="mirai-tax-image" / >
    <?php
    return ob_get_clean();
  } else {
    return NULL;
  }
} );
