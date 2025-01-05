


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
      url: $('#path-to-counter-cooperation').data("href"),
      success: function(data){
        $(".counter_4").remove();
 
          if(data['diplome']!=null && data['diplome']!=0 )
          {
              $("#coop_diplome").append(' <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_4 counter_i">'+data['diplome']+'</span>') ;
          }
        
          if(data['convention']!=null && data['convention']!=0 )
          {
              $("#coop_convention").append(' <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_4 counter_i">'+data['convention']+'</span>') ;
              $("#coop_convention_fil").append(' <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_4 counter_i">'+data['convention']+'</span>') ;
          }
          if(data['non_inscrit']!=null && data['non_inscrit']!=0 )
          {
              $("#coop_non_ins").append('  <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_4 counter_i">'+data['non_inscrit']+'</span>') ;
              $("#conv_totale").append('  <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_4">'+data['non_inscrit']+'</span>') ;
  
            }
  
          if(data['totale']!=null && data['totale']!=0 ){
            $("#coop_totale").append('  <span style="animation: glow 0.8s infinite alternate;margin-left:5px;" class="badge bg-danger rounded-pill counter_4">'+data['totale']+'</span>') ;
          }
  
  
      },
      error:function(){
        //  alert("er");
      }
    });
  
  


  }





