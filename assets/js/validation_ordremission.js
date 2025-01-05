

(function($) {
  $(document).on('click','#odo' , function(){
    var $a = $(this);
    $vartest = $a.data('user');
    var url = $('#path-to-ordre_m').data("href");
    url= url.replace("1111",  $vartest );
     $.ajax({
               url: url,
               type: "GET",
               dataType: "JSON",
               
               success: function (urlToSend) {
                // var req = new XMLHttpRequest();
             /*     
                 req.open("GET", 'http://localhost/pgi-ensa/public/'+urlToSend['dir'], true);
                  req.responseType = "blob";
                  alert("-1");
                  req.onload = function () {
                    alert("-11");
                    var blob = req.response;
                    alert("0");
                    var link=document.createElement('a');
                    alert("1");
                    link.href=window.URL.createObjectURL(blob);
                    alert("2");
                    link.download=urlToSend['name']+'.pdf';
                    alert(link.download);
                    link.click();
                 }; */
            
                // req.send(); 

                 setTimeout(function(){// wait for 5 secs(2)
                      location.reload(); // then reload the page.(3)
                    }, 100);
                  },
               error: function () {
                   //alert("An error ocurred while loading data ...");
               }
           });
     
   });



    $('#attest_validation_from').on("submit", function(event){  
        event.preventDefault();   
   
    var url='ordre_missionVAL_XXXX',
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


    $('#attest_om_date_from').on("submit", function(event){  
      event.preventDefault();   
 
  var url='ordremissionPdf_date_XXXX',
  FormUrl = url.replace("XXXX", $('#n_om_modal').val()); 
   form = $('#attest_om_date_from');

  $.ajax({ 
      type: "POST", 
      data: form.serialize(),
      url: FormUrl, 
      success: function(data){ 
        $('#om_date_modal').modal('hide');   
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
   