(function($) {

/*   $('#paiement_tranche').append($('<option>', {
    value: parseInt( $('#tranche_index').val()),
    text: $('#tranche_index').val()
    }));
     */
    $('#paiement_tranche').val($('#tranche_index').val()) ;
/*     paiement();
$("#paiement_modePaiement").on('change', function() {
                paiement();
                return false; 
        });

  function paiement(){

    if($('#paiement_modePaiement').val() == 'Chèque'){
      $('#paiement_numChequeSection').show();
      $('#paiement_dateOperationSection').hide();
      $('#paiement_numOperationSection').hide(); 
    }else if($('#paiement_modePaiement').val() == 'Virement'){
      $('#paiement_dateOperationSection').show();
      $('#paiement_numOperationSection').show(); 
      $('#paiement_numChequeSection').hide();

    }else{
      $('#paiement_dateOperationSection').hide();
      $('#paiement_numOperationSection').hide();
      $('#paiement_numChequeSection').hide();
    }
  } */



  paiement();
  $("#paiementdivers_type").on('change', function() {
                  paiement();
                  return false; 
          }); 

 $("#paiement_etu_non_exist_formation").on('change', function() {
            paiement();
            return false; 
    }); 
  
    function paiement(){
  
      if($('#paiementdivers_type').val() == 'Autres'){
  
        $('#pd_emetteur').show();
  
      }else{
        $('#pd_emetteur').hide();
        $('#paiementdivers_emetteur').val('');
        
      }


      if(   ($('#paiement_etu_non_exist_formation').find(":selected").text()).indexOf('IC') == 0  ){

          $('#paiement_etu_non_exist_etape')
          .empty()
          .append('<option value="1" selected="selected">1ére Annee</option>')
      ;

    
      }

      if(   ($('#paiement_etu_non_exist_formation').find(":selected").text()).indexOf('ID') == 0  ){

          $('#paiement_etu_non_exist_etape')
          .empty()
          .append('<option value="" selected="selected">------------</option>')  
          .append('<option value="1">1ére Annee</option>')
          .append('<option value="2">2éme Annee</option>')
      ;

      }



    } 

})(jQuery);