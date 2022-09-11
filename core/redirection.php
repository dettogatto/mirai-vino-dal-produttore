<?php

function mirai_vdp_add_meta_box_redirection() {
  $screens = [ 'product' ];
  foreach ( $screens as $screen ) {
    add_meta_box(
      'mirai_vdp_redirection',                 // Unique ID
      'Reindirizzamento',                      // Box title
      'mirai_vdp_meta_box_redirection_html',   // Content callback, must be of type callable
      $screen                                  // Post type
    );
  }
}
add_action( 'add_meta_boxes', 'mirai_vdp_add_meta_box_redirection' );

function mirai_vdp_meta_box_redirection_html($post){
  ?>
  <br>
  <label>
    <span>URL Destinazione</span>
    <input type="hidden" id="mirai-product-redirection-source" name="mirai-product-redirection-source" value="<?= wp_make_link_relative(get_permalink()); ?>">
    <input type="text" id="mirai-product-redirection-field" name="mirai-product-redirection-field" value="" placeholder="Nessun redirect attivo" />
  </label>
  <br>
  <button id="mirai-product-redirection-btn" class="button">Applica</button>
  <br>
  <?php
}
