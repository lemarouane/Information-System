(function($) {


    langue = $('#langue').val() ;
    if(langue=='ar-AR'){langue='ar'}
    langue_file = "https://cdn.datatables.net/plug-ins/1.13.1/i18n/"+langue+".json" ;

    var table = $('#exe1').DataTable({
        language: {
        url: langue_file,
        },
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false
        
    });






    $('#motifText').hide();      
    $('#ordre').on('change', function() {
               
      if($('#ordre').val()==1)
      {
        $('#motifText').hide();
      }else{
        $('#motifText').show();
      }
    });


    $('#ordre_form').on("submit", function(event){  
          event.preventDefault();  
         ;
            var url = $("#path-to-validation").data("href");
            url= url.replace("1111",  $("#iddocument").val() );
            jsFormUrl = url;
            form = $('#ordre_form');
          
            $.ajax({ 
              type: "POST", 
              data: form.serialize(),
              url: jsFormUrl, 
              success: function(data){ 
                $('#dataModalOrdre').modal('hide');   
                  setTimeout(function(){// wait for 5 secs(2)
                    location.reload(); // then reload the page.(3)
                  }, 100);
                
              },
              error:function(){
                  alert('service denied');
              }
          });

      });
      $('a.icons').on('click',function (e) {
            var $a = $(this);
            document.getElementById("iddocument").value = $a.data('user');
            
            
         }
     );
  })(jQuery);

        