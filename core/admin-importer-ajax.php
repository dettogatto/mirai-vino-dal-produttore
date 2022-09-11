<?php

add_action('wp_ajax_mirai_vdp_importer_attributes', function(){

  $limit = 10;
  $nonceToken = $_GET["nonce_token"];
  if(!$nonceToken){wp_die();}
  $log = [];

  // Get Ids
  $products_ids = get_posts( array(
    'post_type'        => 'product', // or ['product','product_variation'],
    'numberposts'      => $limit,
    'post_status'      => 'publish',
    'fields'           => 'ids',
    'meta_query'       => array(
      'relation' => 'OR',
      array(
        'key'     => '_mirai_vdp_nonce',
        'compare' => 'NOT EXISTS'
      ),
      array(
        'key'     => '_mirai_vdp_nonce',
        'compare' => '!=',
        'value' => $nonceToken
      )
    )
  ) );

  $allTax = get_object_taxonomies('product');
  $attrList = array_filter($allTax, function($x){ return substr($x, 0, 3) === "pa_"; });
  $allAttrs = [];

  foreach ($attrList as $attr) {
    $allAttrs[$attr] = [];
    $terms = get_terms($attr, array('hide_empty' => false));
    foreach ($terms as $t) {
      $allAttrs[$attr][] = array("slug" => $t->slug, "name" => strtolower($t->name));
    }
  }


  // Loop through product Ids
  foreach ( $products_ids as $product_id ) {

    // Get the WC_Product object
    $product = wc_get_product($product_id);

    $dictionary = array_merge(
      wp_list_pluck( get_the_terms($product_id, 'product_cat'), "slug"),
      wp_list_pluck( get_the_terms($product_id, 'product_cat'), "name"),
      wp_list_pluck( get_the_terms($product_id, 'product_tag'), "slug"),
      wp_list_pluck( get_the_terms($product_id, 'product_tag'), "name"),
      [$product->get_name()]
    );

    $dictionary = array_map(function($dic){
      return strtolower($dic);
    }, $dictionary);

    $dictionary = implode(", ", $dictionary);

    foreach ($allAttrs as $attr => $terms) {
      foreach ($terms as $term) {
        if( strpos($dictionary, $term["slug"]) !== false || strpos($dictionary, $term["name"]) !== false){
          // wp_set_post_terms($product_id, $attr, $term["slug"]);

          // Add the attribute for real
          $term_taxonomy_ids = wp_set_object_terms( $product_id, $term["slug"], $attr, true );
          $thedata = array(
            $attr=>array(
              'name'=>$attr,
              'value'=>$term["slug"],
              'is_visible' => '1',
              'is_variation' => '0',
              'is_taxonomy' => '1'
            )
          );

          $existing = get_post_meta( $product_id, '_product_attributes')[0];
          if($existing){
            $thedata = array_merge($thedata, $existing);
          }

          update_post_meta( $product_id, '_product_attributes', $thedata);

          $log[] = 'Prod #' . $product_id . ' added attribute '.$attr.'[ '.$term["slug"].' ]';
        }
      }
    }


    // DEBUG START
    // echo($product_id . "<br>");
    // echo("<pre>");
    // var_dump($dictionary);
    // echo("</pre>");
    // echo("<hr>");
    // DEBUG END

    // Mark product as updated
    $product->update_meta_data( '_mirai_vdp_nonce', $nonceToken );
    // $log[] = 'Prod #' . $product_id . ' added nonce_token[ '.$nonceToken.' ]';


    $product->save();
  }

  $return = [
    "success" => true,
    "processed" => count($products_ids),
    "log" => $log
  ];

  echo(json_encode($return));


  wp_die();

});
















add_action('wp_ajax_mirai_vdp_importer_supplier_from_tags', function(){
  $limit = 10;
  $nonceToken = $_GET["nonce_token"];
  if(!$nonceToken){wp_die();}
  $log = [];

  // Get Ids
  $products_ids = get_posts( array(
    'post_type'        => 'product', // or ['product','product_variation'],
    'numberposts'      => $limit,
    'post_status'      => 'publish',
    'fields'           => 'ids',
    'meta_query'       => array(
      'relation' => 'OR',
      array(
        'key'     => '_mirai_vdp_nonce',
        'compare' => 'NOT EXISTS'
      ),
      array(
        'key'     => '_mirai_vdp_nonce',
        'compare' => '!=',
        'value' => $nonceToken
      )
    )
  ) );


  // Loop through product Ids
  foreach ( $products_ids as $product_id ) {

    // Get the WC_Product object
    $product = wc_get_product($product_id);

    // Get the tags
    $tags = wp_list_pluck( get_the_terms($product_id, 'product_tag'), "name");

    $found = false;
    foreach ($tags as $tag) {
      $user = get_userdatabylogin($tag);

      if($user && in_array('seller', (array) $user->roles) ){
        $log[] = 'Product #'.$product_id.' assigned to vendor #'.$user->ID.' ('.$product->get_name().' -> '.$tag.' )';

        // Assegnazione del prodotto all'utente
        $arg = array(
          'ID' => $product_id,
          'post_author' => $user->ID,
        );
        wp_update_post( $arg );

        break;
      }

    }
    if(!$found){
      $log[] = '<strong>Product #'.$product_id.' not assigned ('.$product->get_name().')</strong>';
    }

    // Mark product as updated
    $product->update_meta_data( '_mirai_vdp_nonce', $nonceToken );
    // $log[] = 'Prod #' . $product_id . ' added nonce_token[ '.$nonceToken.' ]';

    $product->save();

  }





  $return = [
    "success" => true,
    "processed" => count($products_ids),
    "log" => $log
  ];

  echo(json_encode($return));


  wp_die();
});









add_action('wp_ajax_mirai_vdp_importer_supplier_assoc', function(){
  global $wpdb;

  $log = [];

  $line = $_GET['line'];
  $file = __DIR__ . '/../prod-supp.csv';
  $spl = new SplFileObject($file);
  $spl->seek($line);
  $valid_line = true;

  if($spl->current() === false){

    // Line too high, file too short
    $valid_line = false;

  } else {

    $line = str_getcsv($spl->current());
    $sku = $line[0];
    $product_title = $line[1];
    $supplier = $line[5];
    $p_title_query = '%'.str_replace(" ", "%", $product_title).'%';
    $product_ids = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND post_title LIKE '%s' ",  "%".$product_title."%") );


    $user = get_userdatabylogin($supplier);

    if(is_array($product_ids) && count($product_ids) > 0){

      if($user){


        if( in_array('seller', (array) $user->roles) ){


          foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id->ID);

            // Assegnazione del prodotto all'utente
            $arg = array(
              'ID' => $product->get_id(),
              'post_author' => $user->ID,
            );
            wp_update_post( $arg );
            $log[] = 'Prod #' . $product->get_id() . ' - '.$product->get_name().' assigned to vendor ' . $supplier;
          }


        } else {
          $log[] = '<strong>USER NAMED '.$supplier.' IS NOT SELLER (prod #'.$product_id.')</strong>';
        }

      } else {
        $log[] = '<strong>CAN\'T FIND USER NAMED '.$supplier.' (name: '.$product_title.', cod: '.$sku.')</strong>';
      }

    } else {
      $log[] = '<strong>CAN\'T FIND PRODUCT #'.$product_id.' (name: '.$product_title.', cod: '.$sku.')</strong>';
    }


  } // end else


  $return = array(
    "success" => true,
    "valid_line" => $valid_line,
    "log" => $log
  );

  echo(json_encode($return));

  wp_die();
});
