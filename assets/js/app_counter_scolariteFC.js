


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
        url: $('#path-to-counter-scolariteFC').data("href"),
        success: function(data){
          $(".counter_2").remove();
        
            if( data['attestationFC']!=null && data['attestationFC']!=0 )
            {
                $("#sco_attestationFC").append(' <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_2 counter_i">'+data['attestationFC']+'</span>') ;
            }
    
            if(data['releveFC']!=null && data['releveFC']!=0 )
            {
                $("#sco_releveFC").append(' <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_2 counter_i">'+data['releveFC']+'</span>') ;
    
            }
    
            if(data['carteFC']!=null && data['carteFC']!=0 )
            {
                $("#sco_carteFC").append(' <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_2 counter_i">'+data['carteFC']+'</span>') ;
            }
          
            if(data['reinscriptionFC']!=null && data['reinscriptionFC']!=0 )
            {
                $("#sco_reinscriptionFC").append('<span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_2 counter_i">'+data['reinscriptionFC']+'</span>') ;
            }
    
            if(data['totaleFC']!=null && data['totaleFC']!=0 ){
              $("#sco_totaleFC").append('<span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_2">'+data['totaleFC']+'</span>') ;
            }
    
    
        },
        error:function(){
          //  alert("er");
        }
      });
    
    
    

  }



