(function ($) {
  $( document ).on( 'click', '.single_add_to_cart_button', function(e) {
    e.preventDefault();

    var thisbutton = $(this);
    var thisform = thisbutton.closest('form.cart');
    var id = thisbutton.val();
    var product_qty = thisform.find('input[name=quantity]').val() || 1;
    var product_id = id || thisform.find('input[name=product_id]').val() || thisform.find('input[name="add-to-cart"]').val();
    var variation_id = thisform.find('input[name=variation_id]').val() || 0;

    var data = {
      action: 'woocommerce_mirai_ajax_add_to_cart',
      product_id: product_id,
      product_sku: '',
      quantity: product_qty,
      variation_id: variation_id,
    };


    $.ajax({
      type: 'post',
      url: ajax_url,
      data: data,
      beforeSend: function (response) {
        thisbutton.removeClass('added').addClass('loading');
      },
      complete: function (response) {
        thisbutton.addClass('added').removeClass('loading');
      },
      success: function (response) {

        if (response.error & response.product_url) {
          window.location = response.product_url;
          return;
        } else {
          $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, thisbutton]);
        }
      },
    });


  });



})(jQuery);
