
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
    url: $('#path-to-counter').data("href"),
    success: function(data){
      var totale = 0 ;
      $(".counter_1").remove();
      if( data['autorisations']!=null && data['autorisations']!=0 )
      {
          $("#sn_autorisation").append('<span style="animation: glow 0.8s infinite alternate; margin-left:5px;" class="badge bg-danger rounded-pill counter_1 counter_i">'+data['autorisations']+'</span>') ;
          totale = totale + data['autorisations'] ;
      }


      if(data['conges']!=null && data['conges']!=0 )
      {
          $("#sn_conge").append('<span style="animation: glow 0.8s infinite alternate; margin-left:5px;" class="badge bg-danger rounded-pill counter_1 counter_i">'+data['conges']+'</span>') ;
          totale = totale + data['conges'] ;
      }

      if(data['attestations']!=null && data['attestations']!=0 )
      {
          $("#sn_att").append('<span style="animation: glow 0.8s infinite alternate; margin-left:5px;" class="badge bg-danger rounded-pill counter_1 counter_i">'+data['attestations']+'</span>') ;
          totale = totale + data['attestations'] ;
      }
      if(data['ordremission']!=null && data['ordremission']!=0 )
      {
          $("#sn_ordremiss").append('<span style="animation: glow 0.8s infinite alternate; margin-left:5px;" class="badge bg-danger rounded-pill counter_1 counter_i">'+data['ordremission']+'</span>') ;
          totale = totale + data['ordremission'] ;
      }

      if(data['reprises']!=null && data['reprises']!=0 )
      {
          $("#sn_rep").append('<span style="animation: glow 0.8s infinite alternate; margin-left:5px;" class="badge bg-danger rounded-pill counter_1 counter_i">'+data["reprises"]+'</span>') ;
          totale = totale + data['reprises'] ;
        
      }

      if(data['ficheheures']!=null && data['ficheheures']!=0 )
      {
          $("#sn_fh").append('<span style="animation: glow 0.8s infinite alternate; margin-left:5px;" class="badge bg-danger rounded-pill counter_1 counter_i">'+data['ficheheures']+'</span>') ;
          totale = totale + data['ficheheures'] ;
        
      }
      if(data['reprises_rh']!=null && data['reprises_rh']!=0 )
      {
          $("#reprises_rh").append('<span style="animation: glow 0.8s infinite alternate;  margin-left:5px;" class="badge bg-danger rounded-pill counter_1 counter_i">'+data['reprises_rh']+'</span>') ;
             // totale = totale + data['reprises_rh'] ;
      }


    if(totale>0){
      $("#sn_traitement").append(' <span style="animation: glow 0.8s infinite alternate; margin-left:5px;" class="badge bg-danger rounded-pill counter_1">'+totale+'</span>') ;
    }



    },
    error:function(){
      //  alert("er");
    }
  });
}
    
  
    
  
   
