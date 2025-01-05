



(function($) {

    

    $('#attest_validation_from').on("submit", function(event){  
        event.preventDefault();   
   
    var url='reprisesVAL_XXXX',
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
   