(function($){

  window.miraiVdpCurrentPostRedirect = null;
  window.miraiVdpPostRedirectGroup = 3;

  $(document).ready(function(){

    if($("#mirai-product-redirection-field").length){

      var field = $("#mirai-product-redirection-field");
      var btn = $("#mirai-product-redirection-btn");
      var source = $("#mirai-product-redirection-source").val();


      function miraiVdpProductRedirectGetData(source, target){
        return {
          status: "enabled",
          position: 0,
          match_data: {
            source: {
              flag_regex: false,
              flag_query: "ignore",
              flag_case: false,
              flag_trailing: true
            }
          },
          url: source,
          match_type: "url",
          title: "",
          group_id: window.miraiVdpPostRedirectGroup,
          action_type: "url",
          action_code: 301,
          action_data: {url: target},
          filterBy: {
            group: miraiVdpPostRedirectGroup,
            url: source
          }
        };

      }

      function miraiVdpCreateProductRedirect(source, target){

        let url = apiData.url + "redirect";

        $.ajax({
          url: url,
          method: "POST",
          beforeSend: function ( xhr ) { xhr.setRequestHeader( 'X-WP-Nonce', apiData.nonce ); },
          data: miraiVdpProductRedirectGetData(source, target)
        }).done(function(response){
          console.log("Created new redirect");
          console.log(response);
          miraiVdpProductRedirectRefreshView(response);
        });

      }

      function miraiVdpUpdateProductRedirect(id, source, target){

        let url = apiData.url + "redirect/" + id;

        $.ajax({
          url: url,
          method: "POST",
          beforeSend: function ( xhr ) { xhr.setRequestHeader( 'X-WP-Nonce', apiData.nonce ); },
          data: miraiVdpProductRedirectGetData(source, target)
        }).done(function(response){
          console.log("Updated redirect");
          console.log(response);
          miraiVdpProductRedirectRefresh();
        });

      }

      function miraiVdpDeleteProductRedirects(source){
        let url = apiData.url + "bulk/redirect/delete"
        let data = {
          global: true,
          filterBy: {
            url: source,
            group: window.miraiVdpPostRedirectGroup
          }
        };

        $.ajax({
          url: url,
          method: "POST",
          beforeSend: function ( xhr ) { xhr.setRequestHeader( 'X-WP-Nonce', apiData.nonce ); },
          data: data
        }).done(function(response){
          console.log("Deleted redirects");
          console.log(response);
          miraiVdpProductRedirectRefreshView(response);
        });

      }

      // Retrieve current redirect if any
      function miraiVdpProductRedirectRefresh(){
        var url = apiData.url + "redirect";

        $.ajax({
          url: url,
          method: "GET",
          data: {
            filterBy: {
              url: source,
              group: window.miraiVdpPostRedirectGroup
            }
          },
          beforeSend: function ( xhr ) { xhr.setRequestHeader( 'X-WP-Nonce', apiData.nonce ); },
        }).done(function(response){
          console.log(response);
          miraiVdpProductRedirectRefreshView(response);
        });
      }

      // Print current redirect on page
      function miraiVdpProductRedirectRefreshView(response){
        if(response.total === 0){
          window.miraiVdpCurrentPostRedirect = null;
        } else if(response.total === 1){
          window.miraiVdpCurrentPostRedirect = response.items[0];
          field.val(response.items[0].action_data.url);
        } else if(response.total > 1){
          field.val("ERRORE: più di un redirect trovati!");
          //btn.hide();
        }
        btn.removeClass("button-primary");
      }


      // Make button blue on field change
      field.on("input", function(){
        btn.addClass("button-primary");
      });

      // Prevent form from being submitted from the text field
      field.on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
          e.preventDefault();
          return false;
        }
      });


      // Handle button click
      btn.click(function(e){
        e.preventDefault();
        if(field.val() == ""){
          // Delete the redirects
          miraiVdpDeleteProductRedirects(source);
        } else if(window.miraiVdpCurrentPostRedirect){
          // Update the redirect
          miraiVdpUpdateProductRedirect(window.miraiVdpCurrentPostRedirect.id, source, field.val());
        } else {
          // Create the redirect
          miraiVdpCreateProductRedirect(source, field.val());
        }
      });



      miraiVdpProductRedirectRefresh();


    }
  });
})(jQuery);
