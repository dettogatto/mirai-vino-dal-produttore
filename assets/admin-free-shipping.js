/*
* Post Bulk Edit Script
* Hooks into the inline post editor functionality to extend it to our custom metadata
*/

jQuery(document).ready(function($){

  //Prepopulating our quick-edit post info
  var $inline_editor = inlineEditPost.edit;
  inlineEditPost.edit = function(id){

    //call old copy
    $inline_editor.apply( this, arguments);

    //our custom functionality below
    var post_id = 0;
    if( typeof(id) == 'object'){
      post_id = parseInt(this.getId(id));
    }

    //if we have our post
    if(post_id != 0){

      //find our row
      var edit_row = $('#edit-' + post_id);
      var data_row = $('#post-' + post_id);

      var free_shipping = data_row.find("td.column-free_shipping").text().trim();

      if(free_shipping == "YES"){
        edit_row.find('input[type="checkbox"][name="mirai_free_shipping"]').prop('checked', true);
      }


    }

  }

});
