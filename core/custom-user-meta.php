<?php
/**
* The field on the editing screens.
*
* @param $user WP_User user object
*/
function mirai_vdp_usermeta_shipping_fee_fields( $user )
{
  ?>
  <h3>Informazioni sul Produttore</h3>
  <table class="form-table">
    <tr>
      <th>
        <label for="birthday">Regione</label>
      </th>
      <td>
        <select class="postform" id="regione" name="regione">
          <option value=""></option>
          <?php
          $regioni = [
            "Abruzzo",
            "Basilicata",
            "Calabria",
            "Campania",
            "Emilia-Romagna",
            "Friuli-Venezia Giulia",
            "Lazio",
            "Liguria",
            "Lombardia",
            "Marche",
            "Molise",
            "Piemonte",
            "Puglia",
            "Sardegna",
            "Sicilia",
            "Toscana",
            "Trentino-Alto Adige",
            "Umbria",
            "Valle d'Aosta",
            "Veneto",
            "Francia"
          ];

          foreach ($regioni as $regione) {
            echo('<option value="'.$regione.'"');
            if(get_user_meta( $user->ID, 'regione', true ) == $regione){
              echo(' selected');
            }
            echo('>'.$regione.'</option>');
          }
          ?>
        </select>
      </select>
      <p class="description">
        Inserisci la regione di questo Produttore
      </p>
    </td>
  </tr>
  <tr>
    <th>
      <label for="anno-fondazione">Anno di fondazione</label>
    </th>
    <td>
      <input type="number"
      class="postform"
      id="anno-fondazione"
      name="anno-fondazione"
      value="<?= esc_attr( get_user_meta( $user->ID, 'anno-fondazione', true ) ) ?>"
      required>
      <p class="description">
        Inserisci l'anno in cui è stata fondata la cantina di questo Produttore
      </p>
    </td>
  </tr>
  <tr>
    <th>
      <label for="prima-vinificazione">Prima vinificazione</label>
    </th>
    <td>
      <input type="number"
      class="postform"
      id="prima-vinificazione"
      name="prima-vinificazione"
      value="<?= esc_attr( get_user_meta( $user->ID, 'prima-vinificazione', true ) ) ?>"
      required>
      <p class="description">
        Inserisci l'anno in cui questo Produttore ha fatto la sua prima vinificazione
      </p>
    </td>
  </tr>
  <tr>
    <th>
      <label for="ettari-vite">Ettari coltivati a vite</label>
    </th>
    <td>
      <input type="number"
      class="postform"
      id="ettari-vite"
      name="ettari-vite"
      value="<?= esc_attr( get_user_meta( $user->ID, 'ettari-vite', true ) ) ?>"
      required>
      <p class="description">
        Inserisci la superficie coltivata a vite in ettari di questo Produttore
      </p>
    </td>
  </tr>
  <tr>
    <th>
      <label for="vdp-bio">Descrizione</label>
    </th>
    <td>
      <?php
      wp_editor( (get_user_meta( $user->ID, 'vdp-bio', true )), 'vdp-bio', $settings = array('textarea_name'=>'vdp-bio') );
      ?>
    </td>
  </tr>
</table>



<h3>Spedizione e minimo bottiglie</h3>
<table class="form-table">
  <tr>
    <th>
      <label for="shipping-fee">Prezzo di spedizione</label>
    </th>
    <td>
      <input type="number"
      class="postform"
      id="shipping-fee"
      name="shipping-fee"
      value="<?= esc_attr( get_user_meta( $user->ID, 'shipping-fee', true ) ) ?>"
      required>
      <p class="description">
        Inserisci il prezzo di spedizione per questo Produttore
      </p>
    </td>
  </tr>
  <tr>
    <th>
      <label for="min-bottles">Minimo bottiglie</label>
    </th>
    <td>
      <input type="number"
      class="postform"
      id="min-bottles"
      name="min-bottles"
      value="<?= esc_attr( get_user_meta( $user->ID, 'min-bottles', true ) ) ?>"
      required>
      <p class="description">
        Inserisci il numero minimo di bottiglie da acquistare per ottenere la spedizione gratuita da questo venditore
      </p>
    </td>
  </tr>
</table>
<?php
}

/**
* The save action.
*
* @param $user_id int the ID of the current user.
*
* @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
*/
function mirai_vdp_usermeta_shipping_fee_update( $user_id )
{
  // check that the current user have the capability to edit the $user_id
  if ( ! current_user_can( 'edit_user', $user_id ) ) {
    return false;
  }

  // create/update user meta for the $user_id
  update_user_meta(
    $user_id,
    'shipping-fee',
    $_POST['shipping-fee']
  );

  update_user_meta(
    $user_id,
    'min-bottles',
    $_POST['min-bottles']
  );

  update_user_meta(
    $user_id,
    'regione',
    $_POST['regione']
  );

  update_user_meta(
    $user_id,
    'anno-fondazione',
    $_POST['anno-fondazione']
  );

  update_user_meta(
    $user_id,
    'prima-vinificazione',
    $_POST['prima-vinificazione']
  );

  update_user_meta(
    $user_id,
    'ettari-vite',
    $_POST['ettari-vite']
  );

  update_user_meta(
    $user_id,
    'vdp-bio',
    $_POST['vdp-bio']
  );
}

// Add the field to user's own profile editing screen.
add_action(
  'show_user_profile',
  'mirai_vdp_usermeta_shipping_fee_fields',
  9999
);

// Add the field to user profile editing screen.
add_action(
  'edit_user_profile',
  'mirai_vdp_usermeta_shipping_fee_fields',
  9999
);

// Add the save action to user's own profile editing screen update.
add_action(
  'personal_options_update',
  'mirai_vdp_usermeta_shipping_fee_update',
  9999
);

// Add the save action to user profile editing screen update.
add_action(
  'edit_user_profile_update',
  'mirai_vdp_usermeta_shipping_fee_update',
  9999
);







// Get shipping rate from product list
function mirai_vdp_get_rate_from_product_list($products){
  $the_cart = [];

  foreach ( $products as $product ) {
    // Skip if bundle
    if(wc_get_product($product['product_id'])->get_type() == "bundle"){
      continue;
    }
    $post_obj = get_post( $product['product_id'] );
    $post_author_id = $post_obj->post_author;
    $post_author = get_user_by('id', $post_author_id);

    if(!isset($the_cart[$post_author_id])){
      $min_bottles = get_user_meta( $post_author_id, 'min-bottles', true );
      $shipping_price = get_user_meta( $post_author_id, 'shipping-fee', true );
      if(!$min_bottles){$min_bottles = 0;}
      $the_cart[$post_author_id] = array(
        'shipping_price' => $shipping_price,
        'min_bottles' => $min_bottles,
        'n_bottles' => 0,
        'n_free_shipping_bottles' => 0
      );
    }
    $the_cart[$post_author_id]['n_bottles'] += $product['quantity'];
    $metaName = "_mirai_free_shipping";
    $free_shipping = get_post_meta($product['product_id'], $metaName, true);
    if($free_shipping){
      $the_cart[$post_author_id]['n_free_shipping_bottles'] += $product['quantity'];
    }
  }

  $tot_shipping = 0;
  foreach ($the_cart as $k => $supplier) {
    if($supplier['shipping_price'] && $supplier['n_bottles'] < $supplier['min_bottles'] && $supplier['n_bottles'] > $supplier['n_free_shipping_bottles']){
      $tot_shipping += $supplier['shipping_price'];
    }
  }

  $rate = new WC_Shipping_Rate(null, "Tariffa produttori", $tot_shipping);
  $rate->set_id("supplier_shipping_fee");
  $rate->set_method_id("flat_rate");
  return $rate;
}



// Apply shipping fee to cart
function mirai_vdp_woocommerce_package_rates( $rates, $secondarg ) {

  $shipping_price = mirai_vdp_get_rate_from_product_list($secondarg["contents"])->cost;

	foreach($rates as $key => $rate ) {
    if($rate->get_method_id() == "flat_rate"){
      $rates[$key]->cost = $shipping_price;
    }
	}

	return $rates;
}
add_filter( 'woocommerce_package_rates', 'mirai_vdp_woocommerce_package_rates', 9999, 2 );


// Remove Dokan filter that disables coupons that are not associated with a vendor
add_filter( 'dokan_ensure_vendor_coupon', function($valid){
  return false;
});
