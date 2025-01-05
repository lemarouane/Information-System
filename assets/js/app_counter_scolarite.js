

app_counter();

setInterval(
  function() 
  {
    app_counter();
  }, 300000);


  function app_counter(){

    $.ajax({
        type: "POST",
        dataType: "json",
        url: $('#path-to-counter-scolarite').data("href"),
        success: function(data){

          $(".counter_3").remove();
            if( data['attestation']!=null && data['attestation']!=0 )
            {
                $("#sco_attestation").append(' <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_3 counter_i">'+data['attestation']+'</span>') ;
            }

            if(data['releve']!=null && data['releve']!=0 )
            {
                $("#sco_releve").append('  <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_3 counter_i">'+data['releve']+'</span>') ;

            }

            if(data['carte']!=null && data['carte']!=0 )
            {
                $("#sco_carte").append('  <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_3 counter_i">'+data['carte']+'</span>') ;
            }
          
            if(data['reinscription']!=null && data['reinscription']!=0 )
            {
                $("#sco_reinscription").append('  <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_3 counter_i">'+data['reinscription']+'</span>') ;
            }

            if(data['totale']!=null && data['totale']!=0 ){
              $("#sco_totale").append('  <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_3">'+data['totale']+'</span>') ;
            }


        },
        error:function(){
          //  alert("er");
        }
      });
  


  }



  
    
  
   
