(function($){

  $(document).on("input", '#bundled_product_data .quantity_default input', function(){
    var thiscontainer = $(this).closest('.wc-bundled-item');
    thiscontainer.find('.quantity_max input').val($(this).val());
    thiscontainer.find('.quantity_min input').val($(this).val());
    thiscontainer.find('.optional input.checkbox').prop("checked", false);
    thiscontainer.find('.shipped_individually input.checkbox').prop("checked", true);
  });

  $(document).ready(function(){
    $('#bundled_product_data .quantity_default label').html("Quantity");
  });



  $(document).on("input", 'select#product-type', mirai_vdp_set_shipping_for_bundles);
  $(document).ready(mirai_vdp_set_shipping_for_bundles);

  function mirai_vdp_set_shipping_for_bundles(){
    var selector = $("select#product-type");
    if(selector.val() == "bundle"){
      $('#shipping_product_data .bundle_type input[name="_bundle_type"]').val("assembled");
      $('#shipping_product_data #_weight').val("0");
      $('#shipping_product_data #product_length').val("0");
      $('#shipping_product_data #product_width').val("0");
      $('#shipping_product_data #product_height').val("0");
    }
  }


})(jQuery);
