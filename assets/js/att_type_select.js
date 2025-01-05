
if($("#attestation_type").val() == "AS"){
    $("#attestation_dateFin").val(null);
    $("#attestation_dateDebut").val(null);
    $("#attestation_dateFin").show();
    $("#attestation_dateDebut").show();
    $('label[for="attestation_dateDebut"]').show();
    $('label[for="attestation_dateFin"]').show();

    $("#attestation_dateDebut").prop('required',true);
    $("#attestation_dateFin").prop('required',true);
}else{
    $("#attestation_dateFin").val(null);
    $("#attestation_dateDebut").val(null);
    $("#attestation_dateFin").hide();
    $("#attestation_dateDebut").hide();
    $('label[for="attestation_dateDebut"]').hide();
    $('label[for="attestation_dateFin"]').hide();

    $("#attestation_dateDebut").prop('required',false);
    $("#attestation_dateFin").prop('required',false);
}  


$('#attestation_type').change(function() {
    if($("#attestation_type").val() == "AS"){
        $("#attestation_dateFin").val(null);
        $("#attestation_dateDebut").val(null);
        $("#attestation_dateFin").show();
        $("#attestation_dateDebut").show();
        $('label[for="attestation_dateDebut"]').show();
        $('label[for="attestation_dateFin"]').show();

        $("#attestation_dateDebut").prop('required',true);
        $("#attestation_dateFin").prop('required',true);
    }else{
        $("#attestation_dateFin").val(null);
        $("#attestation_dateDebut").val(null);
        $("#attestation_dateFin").hide();
        $("#attestation_dateDebut").hide();
        $('label[for="attestation_dateDebut"]').hide();
        $('label[for="attestation_dateFin"]').hide();

        $("#attestation_dateDebut").prop('required',false);
        $("#attestation_dateFin").prop('required',false);
    }  
  }


);

if($("#attestation_edit_type").val() == "AS"){
    $("#attestation_edit_dateFin").val(null);
    $("#attestation_edit_dateDebut").val(null);
    $("#attestation_edit_dateFin").show();
    $("#attestation_edit_dateDebut").show();
    $('label[for="attestation_edit_dateDebut"]').show();
    $('label[for="attestation_edit_dateFin"]').show();

    $("#attestation_edit_dateDebut").prop('required',true);
    $("#attestation_edit_dateFin").prop('required',true);
}else{
    $("#attestation_edit_dateFin").val(null);
    $("#attestation_edit_dateDebut").val(null);
    $("#attestation_edit_dateFin").hide();
    $("#attestation_edit_dateDebut").hide();
    $('label[for="attestation_edit_dateDebut"]').hide();
    $('label[for="attestation_edit_dateFin"]').hide();

    $("#attestation_edit_dateDebut").prop('required',false);
    $("#attestation_edit_dateFin").prop('required',false);
}  


$('#attestation_edit_type').change(function() {
    if($("#attestation_edit_type").val() == "AS"){
        $("#attestation_edit_dateFin").val(null);
        $("#attestation_edit_dateDebut").val(null);
        $("#attestation_edit_dateFin").show();
        $("#attestation_edit_dateDebut").show();
        $('label[for="attestation_edit_dateDebut"]').show();
        $('label[for="attestation_edit_dateFin"]').show();

        $("#attestation_edit_dateDebut").prop('required',true);
        $("#attestation_edit_dateFin").prop('required',true);
    }else{
        $("#attestation_edit_dateFin").val(null);
        $("#attestation_edit_dateDebut").val(null);
        $("#attestation_edit_dateFin").hide();
        $("#attestation_edit_dateDebut").hide();
        $('label[for="attestation_edit_dateDebut"]').hide();
        $('label[for="attestation_edit_dateFin"]').hide();
        
        $("#attestation_edit_dateDebut").prop('required',false);
        $("#attestation_edit_dateFin").prop('required',false);
    }  
  }


);
