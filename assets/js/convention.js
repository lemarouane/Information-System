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
            var url = $("#path-to-convention").data("href");
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

    $('a.icons1').on('click',function (e) {

        var $a = $(this);
        var http = new XMLHttpRequest();
        var url = $("#path-to-nbconvention").data("href");
        http.open('POST', url, true);

        //Send the proper header information along with the request
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        http.onreadystatechange = function() {//Call a function when the state changes.
            if(http.readyState == 4 && http.status == 200) {
                    
                document.getElementById("conventionList").innerHTML = http.responseText;
            }
         }
        http.send('codeApogee='+$a.data('user'));   
    }
    );





    

    $('#attest_validation_from').on("submit", function(event){  
      event.preventDefault();   
 
  var url='stageencad_XXXX',
  jsFormUrl = url.replace("XXXX", $('#n_attest').val()); 
   form = $('#attest_validation_from');

  $.ajax({ 
      type: "POST", 
      data: form.serialize(),
      url: jsFormUrl, 
      success: function(data){ 
        $('#validation_modal').modal('hide');   
          setTimeout(function(){// wait for 5 secs(2)
            location.reload(); // then reload the page.(3)
          }, 100);
        
      },
      error:function(){
          alert('service denied');
      }
  });


  });






  })(jQuery);

        