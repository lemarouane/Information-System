
$link_pe = $('#path-to-pe_active').data("href") ;
$link_per = $('#path-to-pe_periode').data("href") ;


$('.platformActive').on('change', function() {
    

    $var1=$link_pe.replace("ac", $(this).prop('checked')).replace("val", $(this).prop('value'));

   // alert($var1);
    $.ajax({ 
    type: "POST", 
      url: $var1,
      success: function(data){    

      },
      error:function(){
          alert('erreur activation');
      }
  });
  
});

$('.platformValide').on('change', function() {
  $var=$link_va.replace("ac", $(this).prop('checked')).replace("val", $(this).prop('value'));
   //alert($var);
    $.ajax({ 
    type: "POST", 
      url: $var,
      success: function(data){    
      },
      error:function(){
          alert('erreur modification');
      }
  });
  
}); 

$('.finance_periode').on('click', function() {

  $var=$link_per.replace("ac", $(this).prop('checked')).replace("val", $(this).prop('value'));

$m1 = "Est-que vous-etes sure de vouloir ouvrir cette periode ?" ;
$m2 = "Est-que vous-etes sure de vouloir fermer cette periode ?" ;

$msg = "";

if($(this).prop('checked')){
  $msg = $m1;
}else{
  $msg = $m2; 
}

  if (confirm($msg)) {

 
      $.ajax({ 
        type: "POST", 
          url: $var,
          success: function(data){  
            window.location.href = "/e-ensat/public/ProgrammeEmploiBudget";
          },
          error:function(){
              alert('erreur modification');
          }
      });





} else {

  window.location.href = "/e-ensat/public/ProgrammeEmploiBudget";
  
}

 
  
  
}); 

