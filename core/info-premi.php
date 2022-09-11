<?php

function mirai_vdp_add_meta_box_premi() {
  $screens = [ 'product' ];
  foreach ( $screens as $screen ) {
    add_meta_box(
      'mirai_vdp_premi',                 // Unique ID
      'Premi',                           // Box title
      'mirai_vdp_meta_box_premi_html',   // Content callback, must be of type callable
      $screen                            // Post type
    );
  }
}
add_action( 'add_meta_boxes', 'mirai_vdp_add_meta_box_premi' );

function mirai_vdp_meta_box_premi_html($post){
  $attrName = "pa_premi";
  $metaName = "_mirai_premi_info";


  $product = wc_get_product($post->ID);

  $allPremi = get_terms($attrName, array('hide_empty' => false));
  $currentPremi = get_the_terms($product->ID, $attrName);
  $currentPremi = $currentPremi ? $currentPremi : [];
  $currentPremi = array_map(function($e){
    return $e->slug;
  }, $currentPremi);
  $currentMeta = get_post_meta($post->ID, $metaName, true);


  if(!$currentMeta){$currentMeta = array();}

  foreach ($allPremi as $premio) {
    $present = in_array($premio->slug, $currentPremi);
    ?>
    <label>
      <h4><input type="checkbox" value="on" name="mirai_premi[<?= $premio->slug ?>][on]" <?= $present ? "checked" : "" ?>> <?= $premio->name; ?></h4>
    </label>
    <label>
      <span>Testo breve</span>
      <input type="text" name="mirai_premi[<?= $premio->slug ?>][short_text]" value="<?= $currentMeta[$premio->slug]['short_text'] ?>" />
    </label>
    <br>
    <label>
      <span>Testo</span>
      <input type="text" name="mirai_premi[<?= $premio->slug ?>][text]" value="<?= $currentMeta[$premio->slug]['text'] ?>" />
    </label>
    <?php
  }


}



function mirai_vdp_update_premi( $post_id ) {
  $attrName = "pa_premi";
  $metaName = "_mirai_premi_info";


  if ( array_key_exists( 'mirai_premi', $_POST ) ) {


    $newMeta = array();
    $data = $_POST['mirai_premi'];
    $premi = array();

    foreach ($data as $slug => $premio) {
      if($premio['on'] && $premio['on'] == 'on'){
        $premi[] = $slug;
        $newMeta[$slug]["short_text"] = $premio["short_text"];
        $newMeta[$slug]["text"] = $premio["text"];
      }
    }

    // Update pa_premi Attribute
    wp_set_object_terms( $post_id, $premi, $attrName );
    $existing = get_post_meta( $post_id, '_product_attributes', true);
    $thedata = array(
      'name'=>$attrName,
      'value'=>$premi,
      'is_visible' => '1',
      'is_variation' => '0',
      'is_taxonomy' => '1'
    );
    $existing[$attrName] = $thedata;
    update_post_meta( $post_id, '_product_attributes', $existing);

    // Update the meta linked to the attribute
    update_post_meta( $post_id, $metaName, $newMeta);

  }

}
add_action( 'save_post',  'mirai_vdp_update_premi' );



add_shortcode( 'premi_small', function($atts){
  return mirai_vdp_real_premi_shortcode(3, true);
} );


add_shortcode( 'premi', function($atts){
  return mirai_vdp_real_premi_shortcode();
} );

function mirai_vdp_real_premi_shortcode($max = 999, $small = false){
  $attrName = "pa_premi";
  $metaName = "_mirai_premi_info";
  $allPremi = get_terms($attrName, array('hide_empty' => true));
  $currentPremi = get_the_terms($product->ID, $attrName);
  $currentPremi = $currentPremi ? $currentPremi : [];
  $currentPremi = array_map(function($e){
    return $e->slug;
  }, $currentPremi);
  $post = get_post();
  $meta = get_post_meta($post->ID, $metaName, true);

  $text_type = $small ? "short_text" : "text";

  ob_start();

  ?><div class="premi-container <?= $small ? "small" : ""; ?>"><?php

  foreach ($allPremi as $term) {
    if(in_array($term->slug, $currentPremi)){

      // Print the award

      ?>
      <div class="premio">
        <a href="<?= get_term_link($term->slug, $attrName); ?>">
          <div class="premio-img" style="background-image: url(<?= z_taxonomy_image_url($term->term_id); ?>)"></div>
          <p><?= $meta[$term->slug][$text_type] ?></p>
        </a>
      </div>
      <?php

      // Print the award END

      $max--;
      if($max <= 0){
        break; // Break if already printed max elements
      }
    }
  }

  ?></div><?php

  return ob_get_clean();


}
