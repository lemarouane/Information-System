$('#ordre_mission_nbJour').keypress(function(e) {
    return false
});
$('#ordre_mission_dateDebut').keypress(function(e) {
    return false
});
$('#ordre_mission_dateFin').keypress(function(e) {
    return false
});
$("#ordre_mission_dateDebut").attr("autocomplete", "off");
$("#ordre_mission_dateFin").attr("autocomplete", "off");


$('#ordre_mission_valeurAutre').prop('value',null);
$('#ordre_mission_valeurAutre').hide();
$("label[for='ordre_mission_valeurAutre']").hide();

$('#ordre_mission_valeurProjet').prop('value',null);
$('#ordre_mission_valeurProjet').hide();
$("label[for='ordre_mission_valeurProjet']").hide();

/////////////////

$('#ordre_mission_valeurfc').prop('value',null);
$('#ordre_mission_valeurfc').hide();
$("label[for='ordre_mission_valeurfc']").hide();


$('#ordre_mission_valeurprojetvg').prop('value',null);
$('#ordre_mission_valeurprojetvg').hide();
$("label[for='ordre_mission_valeurprojetvg']").hide();

$('#ordre_mission_valeurfcvg').prop('value',null);
$('#ordre_mission_valeurfcvg').hide();
$("label[for='ordre_mission_valeurfcvg']").hide();

$('#ordre_mission_valeurautrevg').prop('value',null);
$('#ordre_mission_valeurautrevg').hide();
$("label[for='ordre_mission_valeurautrevg']").hide();





$('#ordre_mission_financementMission_4').on('change',function(){
  if($('#ordre_mission_financementMission_4').is(":checked"))
    {
      $("#ordre_mission_valeurfc").prop('required', true);
      $('#ordre_mission_valeurfc').show();
      $("label[for='ordre_mission_valeurfc']").show();

    }
  else
    {
      $("#ordre_mission_valeurfc").prop('required', false);
      $('#ordre_mission_valeurfc').prop('value',null);
      $('#ordre_mission_valeurfc').hide();
      $("label[for='ordre_mission_valeurfc']").hide();
    }});








    $('#ordre_mission_financementvoyage_3').on('change',function(){
      if($('#ordre_mission_financementvoyage_3').is(":checked"))
        {
          $('#ordre_mission_valeurautrevg').show();
          $("label[for='ordre_mission_valeurautrevg']").show();
          $("#ordre_mission_valeurautrevg").prop('required', true);
        }
      else
        {
          $("#ordre_mission_valeurautrevg").prop('required', false);
          $('#ordre_mission_valeurautrevg').prop('value',null);
          $('#ordre_mission_valeurautrevg').hide();
          $("label[for='ordre_mission_valeurautrevg']").hide();
        }

  });

 $('#ordre_mission_financementvoyage_2').on('change',function(){
      if($('#ordre_mission_financementvoyage_2').is(":checked"))
        {
          $("#ordre_mission_valeurprojetvg").prop('required', true);
          $('#ordre_mission_valeurprojetvg').show();
          $("label[for='ordre_mission_valeurprojetvg']").show();

        }
      else
        {
          $("#ordre_mission_valeurprojetvg").prop('required', false);
          $('#ordre_mission_valeurprojetvg').prop('value',null);
          $('#ordre_mission_valeurprojetvg').hide();
          $("label[for='ordre_mission_valeurprojetvg']").hide();
        }});



        $('#ordre_mission_financementvoyage_4').on('change',function(){
          if($('#ordre_mission_financementvoyage_4').is(":checked"))
            {
              $("#ordre_mission_valeurfcvg").prop('required', true);
              $('#ordre_mission_valeurfcvg').show();
              $("label[for='ordre_mission_valeurfcvg']").show();
        
            }
          else
            {
              $("#ordre_mission_valeurfcvg").prop('required', false);
              $('#ordre_mission_valeurfcvg').prop('value',null);
              $('#ordre_mission_valeurfcvg').hide();
              $("label[for='ordre_mission_valeurfcvg']").hide();
            }});
        
        
        







     


//////////////////////////

$('#ordre_mission_financementMission_3').on('change',function(){
      if($('#ordre_mission_financementMission_3').is(":checked"))
        {
          $('#ordre_mission_valeurAutre').show();
          $("label[for='ordre_mission_valeurAutre']").show();
          $("#ordre_mission_valeurAutre").prop('required', true);
        }
      else
        {
          $("#ordre_mission_valeurAutre").prop('required', false);
          $('#ordre_mission_valeurAutre').prop('value',null);
          $('#ordre_mission_valeurAutre').hide();
          $("label[for='ordre_mission_valeurAutre']").hide();
        }

  });

 $('#ordre_mission_financementMission_2').on('change',function(){
      if($('#ordre_mission_financementMission_2').is(":checked"))
        {
          $("#ordre_mission_valeurProjet").prop('required', true);
          $('#ordre_mission_valeurProjet').show();
          $("label[for='ordre_mission_valeurProjet']").show();

        }
      else
        {
          $("#ordre_mission_valeurProjet").prop('required', false);
          $('#ordre_mission_valeurProjet').prop('value',null);
          $('#ordre_mission_valeurProjet').hide();
          $("label[for='ordre_mission_valeurProjet']").hide();
        }});


        $("label[for='ordre_mission_financementMission_0']").hide();
        $('#ordre_mission_financementMission_0').hide();




        

        $('#ordre_mission_typeMission').on('change',function(){

          if($("#ordre_mission_typeMission").val()=="R"){
            $("label[for='ordre_mission_financementMission_0']").show();
            $('#ordre_mission_financementMission_0').show();

  if( $("#ordre_mission_moyenTransport").val()=="Avion"){


   $("#ordre_mission_financementvoyage_0").show();
  $("label[for='ordre_mission_financementvoyage_0']").show();

  $("#ordre_mission_financementvoyage_1").show();
  $("label[for='ordre_mission_financementvoyage_1']").show();

  $("#ordre_mission_financementvoyage_2").show();
  $("label[for='ordre_mission_financementvoyage_2']").show();

  $("#ordre_mission_financementvoyage_3").show();
  $("label[for='ordre_mission_financementvoyage_3']").show();

  $("#ordre_mission_financementvoyage_4").show();
  $("label[for='ordre_mission_financementvoyage_4']").show();

  $("#ordre_mission_financementvoyage_5").show();
  $("label[for='ordre_mission_financementvoyage_5']").show();

  $("label[for='ordre_mission_financementvoyage']").show(); 
}else{

  $("#ordre_mission_financementvoyage_0").hide();
  $("label[for='ordre_mission_financementvoyage_0']").hide();

  $("#ordre_mission_financementvoyage_1").hide();
  $("label[for='ordre_mission_financementvoyage_1']").hide();

  $("#ordre_mission_financementvoyage_2").hide();
  $("label[for='ordre_mission_financementvoyage_2']").hide();

  $("#ordre_mission_financementvoyage_3").hide();
  $("label[for='ordre_mission_financementvoyage_3']").hide();

  $("#ordre_mission_financementvoyage_4").hide();
  $("label[for='ordre_mission_financementvoyage_4']").hide();

  $("#ordre_mission_financementvoyage_5").hide();
  $("label[for='ordre_mission_financementvoyage_5']").hide();

  $("label[for='ordre_mission_financementvoyage']").hide(); 
}

           


    
          }else{
              $("label[for='ordre_mission_financementMission_0']").hide();
              $('#ordre_mission_financementMission_0').hide();
              
          }
    

        });

   /////////////////////////////////////////////

        $('#ordre_mission_marqueauto').prop('value',null);
        $('#ordre_mission_marqueauto').hide();
        $("label[for='ordre_mission_marqueauto']").hide();
        
        $('#ordre_mission_matriculeauto').prop('value',null);
        $('#ordre_mission_matriculeauto').hide();
        $("label[for='ordre_mission_matriculeauto']").hide();




        /* $("#ordre_mission_financementvoyage_0").hide();
        $("label[for='ordre_mission_financementvoyage_0']").hide();

        $("#ordre_mission_financementvoyage_1").hide();
        $("label[for='ordre_mission_financementvoyage_1']").hide();

        $("#ordre_mission_financementvoyage_2").hide();
        $("label[for='ordre_mission_financementvoyage_2']").hide();

        $("#ordre_mission_financementvoyage_3").hide();
        $("label[for='ordre_mission_financementvoyage_3']").hide();

        $("#ordre_mission_financementvoyage_4").hide();
        $("label[for='ordre_mission_financementvoyage_4']").hide();

        $("#ordre_mission_financementvoyage_5").hide();
        $("label[for='ordre_mission_financementvoyage_5']").hide();

        $("label[for='ordre_mission_financementvoyage']").hide(); */





        $('#ordre_mission_moyenTransport').on('change',function(){

           if($('#ordre_mission_moyenTransport').val()=="Voiture Personnelle") 
            {
              $('#ordre_mission_marqueauto').show();
              $("label[for='ordre_mission_marqueauto']").show();
              $("#ordre_mission_marqueauto").prop('required', true);
      
              $('#ordre_mission_matriculeauto').show();
              $("label[for='ordre_mission_matriculeauto']").show();
              $("#ordre_mission_matriculeauto").prop('required', true);
            }
          else
            {
              $('#ordre_mission_marqueauto').hide();
              $("label[for='ordre_mission_marqueauto']").hide();
              $("#ordre_mission_marqueauto").prop('required', false);
              $('#ordre_mission_matriculeauto').prop('value',null);
      
              $('#ordre_mission_matriculeauto').hide();
              $("label[for='ordre_mission_matriculeauto']").hide();
              $("#ordre_mission_matriculeauto").prop('required', false);
              $('#ordre_mission_matriculeauto').prop('value',null);
      
      
            } 


            if( $("#ordre_mission_moyenTransport").val()=="Avion"){


              $("#ordre_mission_financementvoyage_0").show();
             $("label[for='ordre_mission_financementvoyage_0']").show();
           
             $("#ordre_mission_financementvoyage_1").show();
             $("label[for='ordre_mission_financementvoyage_1']").show();
           
             $("#ordre_mission_financementvoyage_2").show();
             $("label[for='ordre_mission_financementvoyage_2']").show();
           
             $("#ordre_mission_financementvoyage_3").show();
             $("label[for='ordre_mission_financementvoyage_3']").show();
           
             $("#ordre_mission_financementvoyage_4").show();
             $("label[for='ordre_mission_financementvoyage_4']").show();
           
             $("#ordre_mission_financementvoyage_5").show();
             $("label[for='ordre_mission_financementvoyage_5']").show();
           
             $("label[for='ordre_mission_financementvoyage']").show(); 
           }else{
           
             $("#ordre_mission_financementvoyage_0").hide();
             $("label[for='ordre_mission_financementvoyage_0']").hide();
           
             $("#ordre_mission_financementvoyage_1").hide();
             $("label[for='ordre_mission_financementvoyage_1']").hide();
           
             $("#ordre_mission_financementvoyage_2").hide();
             $("label[for='ordre_mission_financementvoyage_2']").hide();
           
             $("#ordre_mission_financementvoyage_3").hide();
             $("label[for='ordre_mission_financementvoyage_3']").hide();
           
             $("#ordre_mission_financementvoyage_4").hide();
             $("label[for='ordre_mission_financementvoyage_4']").hide();
           
             $("#ordre_mission_financementvoyage_5").hide();
             $("label[for='ordre_mission_financementvoyage_5']").hide();
           
             $("label[for='ordre_mission_financementvoyage']").hide(); 
           }
           







      
      });




      $('#ordre_mission_typedest').on('change',function(){

        if($('#ordre_mission_typedest').val()!="nationale") 
          {
            $("#ordre_mission_moyenTransport option[value='Voiture Personnelle']").remove();
            $("#ordre_mission_moyenTransport option[value='Transport Public']").remove();




            if(  $("#ordre_mission_moyenTransport").val()=="Avion" ){

              $("#ordre_mission_financementvoyage_0").show();
              $("label[for='ordre_mission_financementvoyage_0']").show();

              $("#ordre_mission_financementvoyage_1").show();
              $("label[for='ordre_mission_financementvoyage_1']").show();

              $("#ordre_mission_financementvoyage_2").show();
              $("label[for='ordre_mission_financementvoyage_2']").show();

              $("#ordre_mission_financementvoyage_3").show();
              $("label[for='ordre_mission_financementvoyage_3']").show();

              $("#ordre_mission_financementvoyage_4").show();
              $("label[for='ordre_mission_financementvoyage_4']").show();

              $("#ordre_mission_financementvoyage_5").show();
              $("label[for='ordre_mission_financementvoyage_5']").show();

              $("label[for='ordre_mission_financementvoyage']").show();
              

              $('#ordre_mission_financementvoyage_0').prop('checked',false);
            }else{

              $("#ordre_mission_financementvoyage_0").hide();
              $("label[for='ordre_mission_financementvoyage_0']").hide();

              $("#ordre_mission_financementvoyage_1").hide();
              $("label[for='ordre_mission_financementvoyage_1']").hide();

              $("#ordre_mission_financementvoyage_2").hide();
              $("label[for='ordre_mission_financementvoyage_2']").hide();

              $("#ordre_mission_financementvoyage_3").hide();
              $("label[for='ordre_mission_financementvoyage_3']").hide();

              $("#ordre_mission_financementvoyage_4").hide();
              $("label[for='ordre_mission_financementvoyage_4']").hide();

              $("#ordre_mission_financementvoyage_5").hide();
              $("label[for='ordre_mission_financementvoyage_5']").hide();

              $("label[for='ordre_mission_financementvoyage']").hide();


              $('#ordre_mission_financementvoyage_0').prop('checked',false);
            }

          /*   $("#ordre_mission_financementvoyage_1").show();
            $("label[for='ordre_mission_financementvoyage_1']").show();

            $("#ordre_mission_financementvoyage_2").show();
            $("label[for='ordre_mission_financementvoyage_2']").show();

            $("#ordre_mission_financementvoyage_3").show();
            $("label[for='ordre_mission_financementvoyage_3']").show();

            $("#ordre_mission_financementvoyage_4").show();
            $("label[for='ordre_mission_financementvoyage_4']").show();

            $("#ordre_mission_financementvoyage_5").show();
            $("label[for='ordre_mission_financementvoyage_5']").show();

            $("label[for='ordre_mission_financementvoyage']").show(); 

            $('#ordre_mission_financementvoyage_0').prop('checked',false);
            $('#ordre_mission_financementvoyage_1').prop('checked',false);
            $('#ordre_mission_financementvoyage_2').prop('checked',false);
            $('#ordre_mission_financementvoyage_3').prop('checked',false);
            $('#ordre_mission_financementvoyage_4').prop('checked',false);
            $('#ordre_mission_financementvoyage_5').prop('checked',false); */
            


           // $("#ordre_mission_moyenTransport option[value=null]").prop('selected', true);

           $('#ordre_mission_marqueauto').hide();
           $("label[for='ordre_mission_marqueauto']").hide();
           $("#ordre_mission_marqueauto").prop('required', false);
           $('#ordre_mission_matriculeauto').prop('value',null);
   
           $('#ordre_mission_matriculeauto').hide();
           $("label[for='ordre_mission_matriculeauto']").hide();
           $("#ordre_mission_matriculeauto").prop('required', false);
           $('#ordre_mission_matriculeauto').prop('value',null);

          }else{

            $("#ordre_mission_moyenTransport option[value='Voiture Personnelle']").remove();
            $("#ordre_mission_moyenTransport option[value='Transport Public']").remove();
            $('#ordre_mission_moyenTransport').append('<option value="Transport Public">Transport public</option>');
            $('#ordre_mission_moyenTransport').append('<option value="Voiture Personnelle">Voiture personnelle</option>');

            if( $("#ordre_mission_moyenTransport").val()=="Avion" ){

      
              $("#ordre_mission_financementvoyage_0").show();
              $("label[for='ordre_mission_financementvoyage_0']").show();

              $("#ordre_mission_financementvoyage_1").show();
              $("label[for='ordre_mission_financementvoyage_1']").show();

              $("#ordre_mission_financementvoyage_2").show();
              $("label[for='ordre_mission_financementvoyage_2']").show();

              $("#ordre_mission_financementvoyage_3").show();
              $("label[for='ordre_mission_financementvoyage_3']").show();

              $("#ordre_mission_financementvoyage_4").show();
              $("label[for='ordre_mission_financementvoyage_4']").show();

              $("#ordre_mission_financementvoyage_5").show();
              $("label[for='ordre_mission_financementvoyage_5']").show();

              $("label[for='ordre_mission_financementvoyage']").show();

              $('#ordre_mission_financementvoyage_0').prop('checked',false);

            }else{
            $("#ordre_mission_financementvoyage_0").hide();
              $("label[for='ordre_mission_financementvoyage_0']").hide();

              $("#ordre_mission_financementvoyage_1").hide();
              $("label[for='ordre_mission_financementvoyage_1']").hide();

              $("#ordre_mission_financementvoyage_2").hide();
              $("label[for='ordre_mission_financementvoyage_2']").hide();

              $("#ordre_mission_financementvoyage_3").hide();
              $("label[for='ordre_mission_financementvoyage_3']").hide();

              $("#ordre_mission_financementvoyage_4").hide();
              $("label[for='ordre_mission_financementvoyage_4']").hide();

              $("#ordre_mission_financementvoyage_5").hide();
              $("label[for='ordre_mission_financementvoyage_5']").hide();

              $("label[for='ordre_mission_financementvoyage']").hide();


              $('#ordre_mission_financementvoyage_0').prop('checked',false);

            }

            if(  $("#ordre_mission_moyenTransport").val()=="Transport Public" ){


              $("#ordre_mission_financementvoyage_0").hide();
              $("label[for='ordre_mission_financementvoyage_0']").hide();
  
              $("#ordre_mission_financementvoyage_1").hide();
              $("label[for='ordre_mission_financementvoyage_1']").hide();
  
              $("#ordre_mission_financementvoyage_2").hide();
              $("label[for='ordre_mission_financementvoyage_2']").hide();
  
              $("#ordre_mission_financementvoyage_3").hide();
              $("label[for='ordre_mission_financementvoyage_3']").hide();
  
              $("#ordre_mission_financementvoyage_4").hide();
              $("label[for='ordre_mission_financementvoyage_4']").hide();
  
              $("#ordre_mission_financementvoyage_5").hide();
              $("label[for='ordre_mission_financementvoyage_5']").hide();
  
              $("label[for='ordre_mission_financementvoyage']").hide();
  
              $('#ordre_mission_financementvoyage_0').prop('value',null);
              $('#ordre_mission_financementvoyage_1').prop('value',null);
              $('#ordre_mission_financementvoyage_2').prop('value',null);
              $('#ordre_mission_financementvoyage_3').prop('value',null);
              $('#ordre_mission_financementvoyage_4').prop('value',null);
              $('#ordre_mission_financementvoyage_5').prop('value',null);


            }else{

              if(  $("#ordre_mission_moyenTransport").val()=="Avion" ){

                $("#ordre_mission_financementvoyage_0").show();
                $("label[for='ordre_mission_financementvoyage_0']").show();

                $("#ordre_mission_financementvoyage_1").show();
                $("label[for='ordre_mission_financementvoyage_1']").show();
  
                $("#ordre_mission_financementvoyage_2").show();
                $("label[for='ordre_mission_financementvoyage_2']").show();
  
                $("#ordre_mission_financementvoyage_3").show();
                $("label[for='ordre_mission_financementvoyage_3']").show();
  
                $("#ordre_mission_financementvoyage_4").show();
                $("label[for='ordre_mission_financementvoyage_4']").show();
  
                $("#ordre_mission_financementvoyage_5").show();
                $("label[for='ordre_mission_financementvoyage_5']").show();
  
                $("label[for='ordre_mission_financementvoyage']").show();
  
                $('#ordre_mission_financementvoyage_0').prop('checked',false);

                $('#ordre_mission_financementvoyage_0').prop('checked',false);
              }else{
        
              $("#ordre_mission_financementvoyage_0").hide();
              $("label[for='ordre_mission_financementvoyage_0']").hide();

              $("#ordre_mission_financementvoyage_1").hide();
              $("label[for='ordre_mission_financementvoyage_1']").hide();

              $("#ordre_mission_financementvoyage_2").hide();
              $("label[for='ordre_mission_financementvoyage_2']").hide();

              $("#ordre_mission_financementvoyage_3").hide();
              $("label[for='ordre_mission_financementvoyage_3']").hide();

              $("#ordre_mission_financementvoyage_4").hide();
              $("label[for='ordre_mission_financementvoyage_4']").hide();

              $("#ordre_mission_financementvoyage_5").hide();
              $("label[for='ordre_mission_financementvoyage_5']").hide();

              $("label[for='ordre_mission_financementvoyage']").hide();
  

              $('#ordre_mission_financementvoyage_0').prop('checked',false);
              }
  
              $("#ordre_mission_financementvoyage_1").show();
              $("label[for='ordre_mission_financementvoyage_1']").show();
  
              $("#ordre_mission_financementvoyage_2").show();
              $("label[for='ordre_mission_financementvoyage_2']").show();
  
              $("#ordre_mission_financementvoyage_3").show();
              $("label[for='ordre_mission_financementvoyage_3']").show();
  
              $("#ordre_mission_financementvoyage_4").show();
              $("label[for='ordre_mission_financementvoyage_4']").show();
  
              $("#ordre_mission_financementvoyage_5").show();
              $("label[for='ordre_mission_financementvoyage_5']").show();
  
              $("label[for='ordre_mission_financementvoyage']").show();
  
              $('#ordre_mission_financementvoyage_0').prop('checked',false);
              $('#ordre_mission_financementvoyage_1').prop('checked',false);
              $('#ordre_mission_financementvoyage_2').prop('checked',false);
              $('#ordre_mission_financementvoyage_3').prop('checked',false);
              $('#ordre_mission_financementvoyage_4').prop('checked',false);
              $('#ordre_mission_financementvoyage_5').prop('checked',false);

            }

          



          //  $("#ordre_mission_moyenTransport option[value=null]").prop('selected', true);
          }





   
   });


   $('#ordre_mission_moyenTransport').on('change',function(){

   if(  ($("#ordre_mission_moyenTransport").val()=="Transport Public" ||  $("#ordre_mission_moyenTransport").val()=="Voiture Personnelle" ) && $("#ordre_mission_typedest").val()=="nationale" ){


    $("#ordre_mission_financementvoyage_0").hide();
    $("label[for='ordre_mission_financementvoyage_0']").hide();

    $("#ordre_mission_financementvoyage_1").hide();
    $("label[for='ordre_mission_financementvoyage_1']").hide();

    $("#ordre_mission_financementvoyage_2").hide();
    $("label[for='ordre_mission_financementvoyage_2']").hide();

    $("#ordre_mission_financementvoyage_3").hide();
    $("label[for='ordre_mission_financementvoyage_3']").hide();

    $("#ordre_mission_financementvoyage_4").hide();
    $("label[for='ordre_mission_financementvoyage_4']").hide();

    $("#ordre_mission_financementvoyage_5").hide();
    $("label[for='ordre_mission_financementvoyage_5']").hide();

    $("label[for='ordre_mission_financementvoyage']").hide();

    $('#ordre_mission_financementvoyage_0').prop('checked',false);
    $('#ordre_mission_financementvoyage_1').prop('checked',false);
    $('#ordre_mission_financementvoyage_2').prop('checked',false);
    $('#ordre_mission_financementvoyage_3').prop('checked',false);
    $('#ordre_mission_financementvoyage_4').prop('checked',false);
    $('#ordre_mission_financementvoyage_5').prop('checked',false);


  }else{

    if( $('#ordre_mission_typeMission').val() =="R" && ($("#ordre_mission_moyenTransport").val()=="Transport Public" ||  $("#ordre_mission_moyenTransport").val()=="Voiture Personnelle" ) && $("#ordre_mission_typedest").val()=="nationale"){

      $("#ordre_mission_financementvoyage_0").show();
      $("label[for='ordre_mission_financementvoyage_0']").show();

      $("#ordre_mission_financementvoyage_1").show();
      $("label[for='ordre_mission_financementvoyage_1']").show();

      $("#ordre_mission_financementvoyage_2").show();
      $("label[for='ordre_mission_financementvoyage_2']").show();

      $("#ordre_mission_financementvoyage_3").show();
      $("label[for='ordre_mission_financementvoyage_3']").show();

      $("#ordre_mission_financementvoyage_4").show();
      $("label[for='ordre_mission_financementvoyage_4']").show();

      $("#ordre_mission_financementvoyage_5").show();
      $("label[for='ordre_mission_financementvoyage_5']").show();

      $("label[for='ordre_mission_financementvoyage']").show();

      $('#ordre_mission_financementvoyage_0').prop('checked',false);

    }else{
      $("#ordre_mission_financementvoyage_0").hide();
      $("label[for='ordre_mission_financementvoyage_0']").hide();

      
      $('#ordre_mission_financementvoyage_0').prop('checked',false);
    }
  

    $("#ordre_mission_financementvoyage_1").show();
    $("label[for='ordre_mission_financementvoyage_1']").show();

    $("#ordre_mission_financementvoyage_2").show();
    $("label[for='ordre_mission_financementvoyage_2']").show();

    $("#ordre_mission_financementvoyage_3").show();
    $("label[for='ordre_mission_financementvoyage_3']").show();

    $("#ordre_mission_financementvoyage_4").show();
    $("label[for='ordre_mission_financementvoyage_4']").show();

    $("#ordre_mission_financementvoyage_5").show();
    $("label[for='ordre_mission_financementvoyage_5']").show();

    $("label[for='ordre_mission_financementvoyage']").show();

    $('#ordre_mission_financementvoyage_0').prop('checked',false);
    $('#ordre_mission_financementvoyage_1').prop('checked',false);
    $('#ordre_mission_financementvoyage_2').prop('checked',false);
    $('#ordre_mission_financementvoyage_3').prop('checked',false);
    $('#ordre_mission_financementvoyage_4').prop('checked',false);
    $('#ordre_mission_financementvoyage_5').prop('checked',false);

  }



   
});







   $("label[for='ordre_mission_financementvoyage_0']").hide();
   $('#ordre_mission_financementvoyage_0').hide();

   if($('#ordre_mission_typeMission').val() =="R" && $("#ordre_mission_moyenTransport").val()=="Avion" && $("#ordre_mission_typedest").val()=="nationale"){


    $("#ordre_mission_financementvoyage_0").show();
   $("label[for='ordre_mission_financementvoyage_0']").show();
 
   $("#ordre_mission_financementvoyage_1").show();
   $("label[for='ordre_mission_financementvoyage_1']").show();
 
   $("#ordre_mission_financementvoyage_2").show();
   $("label[for='ordre_mission_financementvoyage_2']").show();
 
   $("#ordre_mission_financementvoyage_3").show();
   $("label[for='ordre_mission_financementvoyage_3']").show();
 
   $("#ordre_mission_financementvoyage_4").show();
   $("label[for='ordre_mission_financementvoyage_4']").show();
 
   $("#ordre_mission_financementvoyage_5").show();
   $("label[for='ordre_mission_financementvoyage_5']").show();
 
   $("label[for='ordre_mission_financementvoyage']").show(); 
 }else{
 
   $("#ordre_mission_financementvoyage_0").hide();
   $("label[for='ordre_mission_financementvoyage_0']").hide();
 
   $("#ordre_mission_financementvoyage_1").hide();
   $("label[for='ordre_mission_financementvoyage_1']").hide();
 
   $("#ordre_mission_financementvoyage_2").hide();
   $("label[for='ordre_mission_financementvoyage_2']").hide();
 
   $("#ordre_mission_financementvoyage_3").hide();
   $("label[for='ordre_mission_financementvoyage_3']").hide();
 
   $("#ordre_mission_financementvoyage_4").hide();
   $("label[for='ordre_mission_financementvoyage_4']").hide();
 
   $("#ordre_mission_financementvoyage_5").hide();
   $("label[for='ordre_mission_financementvoyage_5']").hide();
 
   $("label[for='ordre_mission_financementvoyage']").hide(); 
 }
 






  $('#ordre_mission_financementvoyage_0').on('click',function(){

  
    $('#ordre_mission_financementvoyage_1').prop('checked',false);
    $('#ordre_mission_financementvoyage_2').prop('checked',false);
    $('#ordre_mission_financementvoyage_3').prop('checked',false);
    $('#ordre_mission_financementvoyage_4').prop('checked',false);
    $('#ordre_mission_financementvoyage_5').prop('checked',false);

    $('#ordre_mission_valeurautrevg').hide();
    $('#ordre_mission_valeurautrevg').prop('value',null);
    $('#ordre_mission_valeurautrevg').val("");
    $("label[for='ordre_mission_valeurautrevg']").hide();

    $('#ordre_mission_valeurprojetvg').hide();
    $('#ordre_mission_valeurprojetvg').prop('value',null);
    $('#ordre_mission_valeurprojetvg').val("");
    $("label[for='ordre_mission_valeurprojetvg']").hide();

    $('#ordre_mission_valeurfcvg').hide();
    $('#ordre_mission_valeurfcvg').prop('value',null);
    $('#ordre_mission_valeurfcvg').val("");
    $("label[for='ordre_mission_valeurfcvg']").hide();

  });

  $('#ordre_mission_financementvoyage_1').on('click',function(){

  
    $('#ordre_mission_financementvoyage_0').prop('checked',false);
    $('#ordre_mission_financementvoyage_2').prop('checked',false);
    $('#ordre_mission_financementvoyage_3').prop('checked',false);
    $('#ordre_mission_financementvoyage_4').prop('checked',false);
    $('#ordre_mission_financementvoyage_5').prop('checked',false);

    
    $('#ordre_mission_valeurautrevg').hide();
    $('#ordre_mission_valeurautrevg').prop('value',null);
    $('#ordre_mission_valeurautrevg').val("");
    $("label[for='ordre_mission_valeurautrevg']").hide();

    $('#ordre_mission_valeurprojetvg').hide();
    $('#ordre_mission_valeurprojetvg').prop('value',null);
    $('#ordre_mission_valeurprojetvg').val("");
    $("label[for='ordre_mission_valeurprojetvg']").hide();

    $('#ordre_mission_valeurfcvg').hide();
    $('#ordre_mission_valeurfcvg').prop('value',null);
    $('#ordre_mission_valeurfcvg').val("");
    $("label[for='ordre_mission_valeurfcvg']").hide();

  });

  $('#ordre_mission_financementvoyage_2').on('click',function(){

  
    $('#ordre_mission_financementvoyage_1').prop('checked',false);
    $('#ordre_mission_financementvoyage_0').prop('checked',false);
    $('#ordre_mission_financementvoyage_3').prop('checked',false);
    $('#ordre_mission_financementvoyage_4').prop('checked',false);
    $('#ordre_mission_financementvoyage_5').prop('checked',false);

    
    $('#ordre_mission_valeurautrevg').hide();
    $('#ordre_mission_valeurautrevg').prop('value',null);
    $('#ordre_mission_valeurautrevg').val("");
    $("label[for='ordre_mission_valeurautrevg']").hide();


    $('#ordre_mission_valeurfcvg').hide();
    $('#ordre_mission_valeurfcvg').prop('value',null);
    $('#ordre_mission_valeurfcvg').val("");
    $("label[for='ordre_mission_valeurfcvg']").hide();

  });

  $('#ordre_mission_financementvoyage_3').on('click',function(){

  
    $('#ordre_mission_financementvoyage_1').prop('checked',false);
    $('#ordre_mission_financementvoyage_2').prop('checked',false);
    $('#ordre_mission_financementvoyage_0').prop('checked',false);
    $('#ordre_mission_financementvoyage_4').prop('checked',false);
    $('#ordre_mission_financementvoyage_5').prop('checked',false);


    $('#ordre_mission_valeurprojetvg').hide();
    $('#ordre_mission_valeurprojetvg').prop('value',null);
    $('#ordre_mission_valeurprojetvg').val("");
    $("label[for='ordre_mission_valeurprojetvg']").hide();

    $('#ordre_mission_valeurfcvg').hide();
    $('#ordre_mission_valeurfcvg').prop('value',null);
    $('#ordre_mission_valeurfcvg').val("");
    $("label[for='ordre_mission_valeurfcvg']").hide();

  });

  $('#ordre_mission_financementvoyage_4').on('click',function(){

  
    $('#ordre_mission_financementvoyage_1').prop('checked',false);
    $('#ordre_mission_financementvoyage_2').prop('checked',false);
    $('#ordre_mission_financementvoyage_3').prop('checked',false);
    $('#ordre_mission_financementvoyage_0').prop('checked',false);
    $('#ordre_mission_financementvoyage_5').prop('checked',false);

    
    $('#ordre_mission_valeurautrevg').hide();
    $('#ordre_mission_valeurautrevg').prop('value',null);
    $('#ordre_mission_valeurautrevg').val("");
    $("label[for='ordre_mission_valeurautrevg']").hide();

    $('#ordre_mission_valeurprojetvg').hide();
    $('#ordre_mission_valeurprojetvg').prop('value',null);
    $('#ordre_mission_valeurprojetvg').val("");
    $("label[for='ordre_mission_valeurprojetvg']").hide();


  });


  $('#ordre_mission_financementvoyage_5').on('click',function(){

  
    $('#ordre_mission_financementvoyage_1').prop('checked',false);
    $('#ordre_mission_financementvoyage_2').prop('checked',false);
    $('#ordre_mission_financementvoyage_3').prop('checked',false);
    $('#ordre_mission_financementvoyage_4').prop('checked',false);
    $('#ordre_mission_financementvoyage_0').prop('checked',false);

    
    $('#ordre_mission_valeurautrevg').hide();
    $('#ordre_mission_valeurautrevg').prop('value',null);
    $('#ordre_mission_valeurautrevg').val("");
    $("label[for='ordre_mission_valeurautrevg']").hide();

    $('#ordre_mission_valeurprojetvg').hide();
    $('#ordre_mission_valeurprojetvg').prop('value',null);
    $('#ordre_mission_valeurprojetvg').val("");
    $("label[for='ordre_mission_valeurprojetvg']").hide();

    $('#ordre_mission_valeurfcvg').hide();
    $('#ordre_mission_valeurfcvg').prop('value',null);
    $('#ordre_mission_valeurfcvg').val("");
    $("label[for='ordre_mission_valeurfcvg']").hide();

  });





  $('#ordre_mission_financementMission_0').on('click',function(){

  
    $('#ordre_mission_financementMission_1').prop('checked',false);
    $('#ordre_mission_financementMission_2').prop('checked',false);
    $('#ordre_mission_financementMission_3').prop('checked',false);
    $('#ordre_mission_financementMission_4').prop('checked',false);
    $('#ordre_mission_financementMission_5').prop('checked',false);

    $('#ordre_mission_valeurAutre').hide();
    $('#ordre_mission_valeurAutre').prop('value',null);
    $('#ordre_mission_valeurAutre').val("");
    $("label[for='ordre_mission_valeurAutre']").hide();

    $('#ordre_mission_valeurProjet').hide();
    $('#ordre_mission_valeurProjet').prop('value',null);
    $('#ordre_mission_valeurProjet').val("");
    $("label[for='ordre_mission_valeurProjet']").hide();

    $('#ordre_mission_valeurfc').hide();
    $('#ordre_mission_valeurfc').prop('value',null);
    $('#ordre_mission_valeurfc').val("");
    $("label[for='ordre_mission_valeurfc']").hide();

    

  });

  $('#ordre_mission_financementMission_1').on('click',function(){

  
    $('#ordre_mission_financementMission_0').prop('checked',false);
    $('#ordre_mission_financementMission_2').prop('checked',false);
    $('#ordre_mission_financementMission_3').prop('checked',false);
    $('#ordre_mission_financementMission_4').prop('checked',false);
    $('#ordre_mission_financementMission_5').prop('checked',false);

    $('#ordre_mission_valeurAutre').hide();
    $('#ordre_mission_valeurAutre').prop('value',null);
    $('#ordre_mission_valeurAutre').val("");
    $("label[for='ordre_mission_valeurAutre']").hide();

    $('#ordre_mission_valeurProjet').hide();
    $('#ordre_mission_valeurProjet').prop('value',null);
    $('#ordre_mission_valeurProjet').val("");
    $("label[for='ordre_mission_valeurProjet']").hide();

    $('#ordre_mission_valeurfc').hide();
    $('#ordre_mission_valeurfc').prop('value',null);
    $('#ordre_mission_valeurfc').val("");
    $("label[for='ordre_mission_valeurfc']").hide();

  });

  $('#ordre_mission_financementMission_2').on('click',function(){

  
    $('#ordre_mission_financementMission_1').prop('checked',false);
    $('#ordre_mission_financementMission_0').prop('checked',false);
    $('#ordre_mission_financementMission_3').prop('checked',false);
    $('#ordre_mission_financementMission_4').prop('checked',false);
    $('#ordre_mission_financementMission_5').prop('checked',false);

    $('#ordre_mission_valeurAutre').hide();
    $('#ordre_mission_valeurAutre').prop('value',null);
    $('#ordre_mission_valeurAutre').val("");
    $("label[for='ordre_mission_valeurAutre']").hide();

    $('#ordre_mission_valeurfc').hide();
    $('#ordre_mission_valeurfc').prop('value',null);
    $('#ordre_mission_valeurfc').val("");
    $("label[for='ordre_mission_valeurfc']").hide();



  });

  $('#ordre_mission_financementMission_3').on('click',function(){

  
    $('#ordre_mission_financementMission_1').prop('checked',false);
    $('#ordre_mission_financementMission_2').prop('checked',false);
    $('#ordre_mission_financementMission_0').prop('checked',false);
    $('#ordre_mission_financementMission_4').prop('checked',false);
    $('#ordre_mission_financementMission_5').prop('checked',false);


    $('#ordre_mission_valeurProjet').hide();
    $('#ordre_mission_valeurProjet').prop('value',null);
    $('#ordre_mission_valeurProjet').val("");
    $("label[for='ordre_mission_valeurProjet']").hide();

    $('#ordre_mission_valeurfc').hide();
    $('#ordre_mission_valeurfc').prop('value',null);
    $('#ordre_mission_valeurfc').val("");
    $("label[for='ordre_mission_valeurfc']").hide();

  });

  $('#ordre_mission_financementMission_4').on('click',function(){

  
    $('#ordre_mission_financementMission_1').prop('checked',false);
    $('#ordre_mission_financementMission_2').prop('checked',false);
    $('#ordre_mission_financementMission_3').prop('checked',false);
    $('#ordre_mission_financementMission_0').prop('checked',false);
    $('#ordre_mission_financementMission_5').prop('checked',false);

    $('#ordre_mission_valeurAutre').hide();
    $('#ordre_mission_valeurAutre').prop('value',null);
    $('#ordre_mission_valeurAutre').val("");
    $("label[for='ordre_mission_valeurAutre']").hide();

    $('#ordre_mission_valeurProjet').hide();
    $('#ordre_mission_valeurProjet').prop('value',null);
    $('#ordre_mission_valeurProjet').val("");
    $("label[for='ordre_mission_valeurProjet']").hide();

  });


  $('#ordre_mission_financementMission_5').on('click',function(){

  
    $('#ordre_mission_financementMission_1').prop('checked',false);
    $('#ordre_mission_financementMission_2').prop('checked',false);
    $('#ordre_mission_financementMission_3').prop('checked',false);
    $('#ordre_mission_financementMission_4').prop('checked',false);
    $('#ordre_mission_financementMission_0').prop('checked',false);

    $('#ordre_mission_valeurAutre').hide();
    $('#ordre_mission_valeurAutre').prop('value',null);
    $('#ordre_mission_valeurAutre').val("");
    $("label[for='ordre_mission_valeurAutre']").hide();

    $('#ordre_mission_valeurProjet').hide();
    $('#ordre_mission_valeurProjet').prop('value',null);
    $('#ordre_mission_valeurProjet').val("");
    $("label[for='ordre_mission_valeurProjet']").hide();

    $('#ordre_mission_valeurfc').hide();
    $('#ordre_mission_valeurfc').prop('value',null);
    $('#ordre_mission_valeurfc').val("");
    $("label[for='ordre_mission_valeurfc']").hide();

  });




