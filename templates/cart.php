<?php
/**
* Cart Page
*
* This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
*
* HOWEVER, on occasion WooCommerce will need to update template files and you
* (the theme developer) will need to copy the new files to your theme to
* maintain compatibility. We try to do this as little as possible, but it does
* happen. When this occurs the version of the template file will be bumped and
* the readme will list any important changes.
*
* @see     https://docs.woocommerce.com/document/template-structure/
* @package WooCommerce/Templates
* @version 3.8.0
*/

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="cart-main-container">
  <div class="cart-suppliers-container">
    <form class="woocommerce-cart-form_deb" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
      <?php do_action( 'woocommerce_before_cart_table' ); ?>

      <?php do_action( 'woocommerce_before_cart_contents' ); ?>

      <?php
      $the_cart = [];
      foreach ( WC()->cart->get_cart() as $k => $item ) {
        $post_obj = get_post( $item['product_id'] );
        $post_author_id = $post_obj->post_author;
        $post_author = get_user_by('id', $post_author_id);
        if(!isset($the_cart[$post_author_id])){
          $the_cart[$post_author_id] = array(
            'supplier' => $post_author,
            'products' => array()
          );
        }
        $the_cart[$post_author_id]['products'][] = $item;
      }

      // usort($the_cart, function($a, $b){
      //   return $a['supplier']['slug'] <=> $b['supplier']['slug'];
      // });

      foreach ( $the_cart as $k => $supplier ) {
        $supplier['n_bottles'] = 0;
        $supplier['min_bottles'] = get_user_meta( $supplier['supplier']->ID, 'min-bottles', true );
        $supplier['products_in_cart'] = array();

        ?>

        <div class="mirai-cart-supplier">
          <h2>
            <div class="supplier_name">Produttore: <?php echo($supplier['supplier']->display_name); ?>
            </h2>
            <?php
            if($supplier['min_bottles']){
              ?>
              <span class="light-text">
                * La spedizione è gratuita se acquisti <?php echo($supplier['min_bottles']); ?> bottiglie dallo stesso produttore
              </span>
              <?php
            }

            foreach ( $supplier["products"] as $kk => $cart_item ){


              $cart_item_key = $cart_item['key'];
              $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
              $_product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
              $supplier['products_in_cart'][] = $_product_id;
              $_product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
              if($_product->get_type() != "bundle"){
                $supplier['n_bottles'] += $cart_item['quantity'];
              }

              // Skip bundled products
              if($cart_item["bundled_by"]){
                continue;
              }

              ?>

              <div class="mirai-cart-product">
                <div class="mirai-cart-thumbnail">
                  <?php echo($_product->get_image()); ?>
                </div>

                <div class="mirai-cart-product-info">
                  <div class="mirai-2-cols">
                    <div class="mirai-left-col">
                      <h5><?php echo($cart_item['data']->name); ?></h5>
                    </div>
                    <div class="mirai-right-col product-remove">
                      <?php
                      echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        'woocommerce_cart_item_remove_link',
                        sprintf(
                          '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                          esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                          esc_html__( 'Remove this item', 'woocommerce' ),
                          esc_attr( $_product_id ),
                          esc_attr( $_product->get_sku() )
                        ),
                        $cart_item_key
                      );
                      ?>
                    </div>
                  </div>

                  <div class="mirai-spacer"></div>

                  <div class="mirai-2-cols light-text">
                    <div class="mirai-left-col">Tot Prodotto Singolo</div>
                    <div class="mirai-right-col">
                      <?php
                      echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                      ?>
                    </div>
                  </div>

                  <?php
                  if($cart_item['quantity'] > 1){
                    ?>
                    <div class="mirai-2-cols light-text">
                      <div class="mirai-left-col">Tot Prodotti</div>
                      <div class="mirai-right-col">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                        ?>
                      </div>
                    </div>
                    <?php
                  }
                  ?>

                  <div class="mirai-spacer"></div>

                  <?php
                  if ( $_product->is_sold_individually() ) {
                    $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                  } else {
                    $product_quantity = woocommerce_quantity_input(
                      array(
                        'input_name'   => "cart[{$cart_item_key}][qty]",
                        'input_value'  => $cart_item['quantity'],
                        'max_value'    => $_product->get_max_purchase_quantity(),
                        'min_value'    => '0',
                        'product_name' => $_product->get_name(),
                      ),
                      $_product,
                      false
                    );
                  }

                  echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
                  ?>


                </div>
              </div>


              <?php
            }


            $shipping_price = get_user_meta( $supplier['supplier']->ID, 'shipping-fee', true );

            if($shipping_price && $supplier['min_bottles'] > $supplier['n_bottles']){
              ?>
              <div class="supplier-shipping-fee">
                Totale costo di spedizione per <?php echo($supplier['supplier']->display_name); ?> €<?php echo($shipping_price); ?>
              </div>
              <?php
            } elseif($shipping_price) {
              ?>
              <div class="supplier-shipping-fee">
                Hai raggiunto la spedizione gratuita per <?php echo($supplier['supplier']->display_name); ?>
              </div>
              <?php
            }

            if($shipping_price){
              ?>

              <div class="bottles-counter-bar">
                <?php
                for($i=0; $i < $supplier['min_bottles']; $i++){
                  if($i < $supplier['n_bottles']){
                    echo('<div class="full"></div>');
                  }else{
                    echo('<div class="empty"></div>');
                  }
                }
                ?>
              </div>
              <?php
            }



            if($shipping_price && $supplier['min_bottles'] > $supplier['n_bottles']){
              ?>
              <div class="supplier-shipping-fee red">
                *Ti mancano <?= $supplier['min_bottles'] - $supplier['n_bottles'] ?> bottiglie per avere la spedizione gratuita su questo produttore
              </div>
              <?php
            }


            ?>

            <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>



            <!-- INIZIO ALTRE BOTTIGLIE -->

            <?php

            // Let's get the bottles
            $query = new WP_Query( $args = array(
              'post_type'             => 'product',
              'post_status'           => 'publish',
              'ignore_sticky_posts'   => 1,
              'posts_per_page'        => 5, // Limit
              'post__not_in'          => $supplier['products_in_cart'], // Excluding current product
              'author'                => $supplier['supplier']->id,
              'meta_query' => array( array(
                'key'     => '_stock_status',
                'value'   => 'outofstock',
                'compare' => '!=',
              ) ),
            ) );

            if($query->have_posts()){
              // Only display if I have bottles

              ?>

              <div class="supplier-selection">
                <br>
                <center>
                  <button class="show-supplier-selection btn-alt-color" data-alt-text="Nascondi altri vini">Aggiungi altri vini</button>
                </center>
                <div class="supplier-bottle-container hidden">
                  <div class="navigator supplier-selection-navigator-prev"></div>

                  <?php

                  $counter = 0;

                  ?>
                  <div class="supplier-bottle-shower">
                    <?php

                    while( $query->have_posts() ){
                      $query->the_post();
                      $product = wc_get_product($query->post);
                      $counter ++;


                      // Test output

                      ?>

                      <div class="supplier-bottle hidden number-<?php echo($counter); ?>">

                        <div class="thumbnail">
                          <?php echo($product->get_image()); ?>
                        </div>
                        <div class="content">
                          <h5><?php echo($product->get_data()["name"]); ?></h5>
                          <div class="mirai-2-cols light-text">
                            <div class="mirai-left-col">Prodotto</div>
                            <div class="mirai-right-col"><?php echo(WC()->cart->get_product_price($product)); ?></div>
                          </div>
                          <div class="mirai-2-cols light-text tot-price-container">
                            <div class="mirai-left-col">Tot Prod.</div>
                            <div class="mirai-right-col tot-price" data-single-price="<?php echo($product->get_price_including_tax()); ?>"></div>
                          </div>
                          <div class="mirai-2-cols light-text quantity">
                            <div class="mirai-left-col">
                              <input type="number" class="input-text mirai-qty text" step="1" min="1" max="" name="dyn" value="1" title="Qtà" size="4" placeholder="" inputmode="numeric" data-product-id="<?php echo($query->post->ID); ?>">
                            </div>
                            <div class="mirai-right-col">
                              <button class="add-supplier-product">Aggiungi</button>
                            </div>
                          </div>
                        </div>

                      </div>

                      <?php

                    } // end while

                    ?>
                  </div>
                  <div class="navigator supplier-selection-navigator-next"></div>
                </div>

                <center>
                  <a href="<?= dokan()->vendor->get( $supplier['supplier']->ID )->get_shop_url(); ?>">
                    <button class="btn-alt-color go-to-supplier-page hidden" type="button">Vai alla pagina del produttore</button>
                  </a>
                </center>


              </div>



              <?php
            } // end of if($query->have_posts())
            ?>


            <!-- FINE ALTRE BOTTIGLIE -->






          </div>


          <?php
        }
        ?>

        <?php do_action( 'woocommerce_cart_contents' ); ?>

        <?php do_action( 'woocommerce_cart_actions' ); ?>

        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

        <?php do_action( 'woocommerce_after_cart_contents' ); ?>

        <?php do_action( 'woocommerce_after_cart_table' ); ?>


        <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

      </div>

      <div class="cart-collaterals mirai-vdp-collaterals">
        <?php
        /**
        * Cart collaterals hook.
        *
        * @hooked woocommerce_cross_sell_display
        * @hooked woocommerce_cart_totals - 10
        */
        do_action( 'woocommerce_cart_collaterals' );
        ?>
      </div>

    </form>
  </div>
  <?php do_action( 'woocommerce_after_cart' ); ?>
