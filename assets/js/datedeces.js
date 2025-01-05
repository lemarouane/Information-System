if ($('#personnel_activite').val() =='D'){
    $('#div_deces').show();
}else{
    $('#div_deces').hide();
}

$('#personnel_activite').on( "change", function() {

if ($('#personnel_activite').val() =='D'){
    $('#div_deces').show();
}else{
    $('#div_deces').hide();
}
  
  } );

