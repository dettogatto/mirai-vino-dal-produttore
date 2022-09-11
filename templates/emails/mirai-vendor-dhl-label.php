<?php
/**
* Vendor DHL Label email
*
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$text_align = is_rtl() ? 'right' : 'left';

/**
* @hooked WC_Emails::email_header() Output the email header
*/
do_action( 'woocommerce_email_header', $email_heading, $email );

$order_url = esc_url( add_query_arg( array(
  'order_id'   => $order->get_order_number(),
  '_view_mode' => 'email',
  'permission' => '1',
), dokan_get_navigation_url( 'orders' ) ) );

$shipments = $order->get_meta('wf_dhlexpress_shipment_result')['tracking_info'];

foreach ($shipments as $shipment) {
  $trackingId = $shipment['tracking_id'];
  $download_url = admin_url('/post.php?wf_dhl_viewlabel='.base64_encode($trackingId.'|'.get_post($order->ID)->ID));
  ?>
  <p>
    È stata generata l'etichetta DHL per la spedizione dell'ordine #<?php echo($order->get_order_number()); ?> su Vinodalproduttore.it
    <br>
    <a href="<?php echo($download_url); ?>">Clicca qui</a> per visionarla e scaricarla.
  </p>
  <?php
}

?>

<h2>
  <?php
  $before = '<a class="link" href="' . esc_url( $order_url ) . '">';
  $after  = '</a>';
  /* translators: %s: Order ID. */
  echo wp_kses_post( $before . sprintf( '[Order #%s]' . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
  ?>
</h2>

<div style="margin-bottom: 40px;">
    <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
        <thead>
            <tr>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'dokan-lite' ); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Quantity', 'dokan-lite' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($order->get_items() as $item) {
              $product = wc_get_product( $item->get_product_id() );
              $bundled_by = $item->get_meta("_bundled_by");
              // if($product->get_type() == "bundle"){ // Skip bundles
              //   continue;
              // }
              if($bundled_by){ // Skip bundled items
                continue;
              }
              echo('<tr>');
              echo('<td class="td">'.$item->get_name().' (#'.$product->get_sku().')');
              if($bundled_by){
                echo('<br><b>Parte del bundle</b>: '. $bundle_dictionary[$bundled_by]);
              }
              echo('</td>');
              echo('<td class="td">'.$item->get_quantity().'</td>');
              echo('</tr>');
            }
            ?>
        </tbody>
        <tfoot>
            <?php
            $item_totals = $order->get_order_item_totals();

            if ( $order->get_customer_note() ) {
                ?>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'dokan-lite' ); ?></th>
                    <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
                </tr>
                <?php
            }
            ?>
        </tfoot>
    </table>
</div>

<?php
do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );

/*
* @hooked WC_Emails::order_meta() Shows order meta data.
*/
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
* @hooked WC_Emails::customer_details() Shows customer details
* @hooked WC_Emails::email_address() Shows email address
*/
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
* @hooked WC_Emails::email_footer() Output the email footer
*/
do_action( 'woocommerce_email_footer', $email );
