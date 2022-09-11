jQuery( function( $ ) {

  function makeid(length) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  }

  var nonce_token = makeid(8);

  $('#import-attr-btn').click(function(){

    $('#import-log').append('<div><strong>Avvio importazione attributi...</strong></div>');
    $('#import-cmds').hide();

    var reqData = {
      "action": "mirai_vdp_importer_attributes",
      "nonce_token": nonce_token
    };

    do_the_page(reqData);
  });


  function do_the_page(reqData, howManyDone = 0){
    $.get(ajax_url, reqData, function(data){
      $.each(data.log, function(i, v){
        $('#import-log').append('<div>'+v+'</div>');
      });
      $('#import-log').append('<div>DONE ' + (howManyDone + data.processed) + ' PRODS</div>');
      if(data.processed > 0){
        do_the_page(reqData, howManyDone + data.processed);
      } else {
        $('#import-log').append('<div><strong><br>ALL DONE</strong></div>');
      }
    }, "json");
  }







  $('#import-tags-to-sellers-btn').click(function(){

    $('#import-log').append('<div><strong>Avvio associazione dei venditori tramite tag...</strong></div>');
    $('#import-cmds').hide();

    var reqData = {
      "action": "mirai_vdp_importer_supplier_from_tags",
      "nonce_token": nonce_token
    };

    do_the_page(reqData);
  });






  $('#assign-sellers-btn').click(function(){

    $('#import-log').append('<div><strong>Avvio assegnazione Vendor...</strong></div>');
    $('#import-cmds').hide();

    var reqData = {
      "action": "mirai_vdp_importer_supplier_assoc",
      "line": 1
    };

    do_the_sellers(reqData);
  });



  function do_the_sellers(reqData){
    $.get(ajax_url, reqData, function(data){
      $.each(data.log, function(i, v){
        $('#import-log').append('<div>'+v+'</div>');
      });
      if(data.valid_line){
        reqData.line ++;
        do_the_sellers(reqData);
      } else {
        $('#import-log').append('<div><strong><br>ALL DONE</strong></div>');
      }
    }, "json");
  }




});
