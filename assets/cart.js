jQuery( function( $ ) {
  $(".add-supplier-product").click(function(e){
    e.preventDefault();
    var qty_elem = $(this).closest(".quantity").find("input.mirai-qty");
    var qty = qty_elem.val();
    var pr_id = qty_elem.attr("data-product-id");
    window.location = '//' + location.host + location.pathname + "?add-to-cart=" + pr_id + "&quantity=" + qty;
  });

  $(".supplier-bottle input.mirai-qty").on("input", function(){
    var tot_container_el = $(this).closest(".content").find(".tot-price-container");
    var qty = $(this).val();
    if(qty > 1){
      console.log("qty > 1");
      var tot_price_el = tot_container_el.find(".tot-price");
      var single_price = tot_price_el.attr("data-single-price");
      var tot_price = (single_price * qty).toFixed(2);
      tot_price_el.html("€" + tot_price);
      tot_container_el.css("opacity", "1");
    } else {
      console.log("qty < 1");
      tot_container_el.css("opacity", "0");
    }
  });

  $(".show-supplier-selection").click(function(e){
    e.preventDefault();
    var supp_el = $(this).closest(".supplier-selection").find(".supplier-bottle-container");
    supp_el.toggleClass("hidden");
    supp_el.find(".supplier-bottle").addClass("hidden");
    supp_el.find(".number-1").removeClass("hidden");
    let altText = $(this).html();
    $(this).html($(this).attr("data-alt-text"));
    $(this).attr("data-alt-text", altText);
    supp_el.closest(".supplier-selection").find(".go-to-supplier-page").toggleClass("hidden");
  });

  $(".supplier-selection-navigator-next").click(function(e){
    var all_els = $(this).closest(".supplier-bottle-container").find(".supplier-bottle").toArray();
    for(let i = 0; i < all_els.length; i ++ ){
      el = $(all_els[i]);
      if(i < all_els.length - 1 && !el.hasClass("hidden")){
        el.addClass("hidden");
        $(all_els[i+1]).removeClass("hidden");
        break;
      }
    }
  });

  $(".supplier-selection-navigator-prev").click(function(e){
    var all_els = $(this).closest(".supplier-bottle-container").find(".supplier-bottle").toArray().reverse();
    for(let i = 0; i < all_els.length; i ++ ){
      el = $(all_els[i]);
      if(i < all_els.length - 1 && !el.hasClass("hidden")){
        el.addClass("hidden");
        $(all_els[i+1]).removeClass("hidden");
        break;
      }
    }
  });


});
