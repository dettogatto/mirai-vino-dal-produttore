<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

$the_cart = [];
foreach ( WC()->cart->get_cart() as $k => $item ) {
  $post_obj = get_post( $item['product_id'] );
  $post_author_id = $post_obj->post_author;
  $post_author = get_user_by('id', $post_author_id);
  $_product   = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $item['key'] );
  if(!isset($the_cart[$post_author_id])){
    $the_cart[$post_author_id] = array(
      'supplier' => $post_author,
      'products' => array(),
      'subtotal' => 0
    );
  }
  $the_cart[$post_author_id]['products'][] = $item;
  $the_cart[$post_author_id]['subtotal'] += $_product->get_price_including_tax() * $item['quantity'];
}

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 class="cart-totals-title">Riepilogo Ordine</h2>

  <table cellspacing="0" class="shop_table shop_table_responsive">

  <?php foreach ($the_cart as $supplier) : ?>

    <tr>
      <th colspan="2" class="cart_totals-supplier-title">
        <?php echo($supplier['supplier']->display_name); ?>
      </th>
    </tr>

    <tr class="cart-subtotal">
			<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
        <?php echo(wc_price($supplier['subtotal'])); ?>
      </td>
		</tr>

    <tr class="shipping">
      <th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
      <td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>">
        <?php echo(wc_price(mirai_vdp_get_rate_from_product_list($supplier['products'])->cost)); ?>
      </td>
    </tr>

  <?php endforeach; ?>

  <tr>
    <th colspan="2" class="cart_totals-totali-title ">
      - Totali -
    </th>
  </tr>

  <tr class="cart-subtotal">
    <th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
    <td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
  </tr>

  <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

  <?php
  wc_cart_totals_shipping_html();
  ?>

  <tr class="cart-subtotal">
    <th>Spedizioni</th>
    <td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>">
      <?php
      $rate = mirai_vdp_get_rate_from_product_list(WC()->cart->get_cart());
      echo(wc_price($rate->cost));
      ?>
    </td>
  </tr>


  <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>


  <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
    <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
      <th>
        Codice sconto<br>
        <span class="uppercase"><?php echo($coupon->code); ?></span>
      </th>
      <td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
    </tr>
  <?php endforeach; ?>


  <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

  <tr class="order-total">
    <th class="uppercase"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
    <td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
  </tr>

  <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

  </table>


  <?php if ( wc_coupons_enabled() ) { ?>
    <div class="coupon">
      <label for="coupon_code">
        <?php /*esc_html_e( 'Coupon:', 'woocommerce' );*/ ?>
        Hai un Codice Sconto?
      </label>
      <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
      <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
        <?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>
      </button>
      <?php do_action( 'woocommerce_cart_coupon' ); ?>
    </div>
  <?php } ?>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
