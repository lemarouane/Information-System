

(function($) {

    $('#attest_validation_from').on("submit", function(event){  
      event.preventDefault();  
      var url= 'ficheheureVAL_XXXX', //'{{ path('ficheHeure_personnel', {'id': 1111 }) }}',
      jsFormUrl = url.replace("XXXX", $('#n_attest').val()); 
      form = $('#attest_validation_from');
	    var formData = new FormData(this);
      $.ajax({ 
        type: "POST", 
        data: formData,
        url: jsFormUrl,
	      contentType: false,
        processData: false, 
        success: function(data){ 
         // alert(data);
         if(data=="0"){
          alert("Veuillez Uploader un Emploi !");
         }
          $('#validation_modal').modal('hide');   
                setTimeout(function(){// wait for 5 secs(2)
                  location.reload(); // then reload the page.(3)
              }, 500);
              },
              error:function(){
                  alert('service denied');
              }
          });  
/* 
    event.preventDefault();   
    var url='ficheheureVAL_XXXX',
    jsFormUrl = url.replace("XXXX", $('#n_attest').val()); 
     form = $('#attest_validation_from');

    $.ajax({ 
      
        type: "POST", 
        data: form.serialize(),
        url: jsFormUrl, 
      //  contentType: false,
       // processData: false,
        success: function(data){ 
          alert(data);
          $('#validation_modal').modal('hide');   
            setTimeout(function(){// wait for 5 secs(2)
              location.reload(); // then reload the page.(3)
            }, 100);
           
        },
        error:function(){
            alert('service denied');
        }
    });
 */

    });
  })(jQuery);
   