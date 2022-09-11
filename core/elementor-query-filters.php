<?php

add_action( 'elementor/query/sort_price_asc', function( $query ) {
  $authors_to_order_by_title = [];
  $current_author = $query->query["author__in"];

  $query->set( 'post_type', [ 'product' ] );
  if(in_array($current_author, $authors_to_order_by_title)){
    $query->set( 'orderby', 'title' );
  } else {
    $query->set( 'orderby', 'meta_value_num' );
    $query->set( 'meta_key', '_price' );
  }

  $query->set( 'order', 'asc' );
} );

// Only show products in the front-end search results
if(!is_admin()){
  add_filter('pre_get_posts', function ($query){
    if ($query->is_search) {
      $query->set('post_type', 'product');
      $query->set( 'wc_query', 'product_query' );
    }
    return $query;

  });
}
