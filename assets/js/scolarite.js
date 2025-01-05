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
    var table = $('#exemple6').DataTable({
        language: {
        url: langue_file,
        },
        "bDestroy": true,
        
        'columnDefs': [
            {
               'targets': 0,
               'checkboxes': {
                  'selectRow': true
               }
            }
         ],
        'select': {
            'style': 'multi'
        },
        'order': [[1, 'asc']]
    });




    $('#ajouter').on('click',function(){
        var rowsel = table.column(0, { page:'current' }).checkboxes.selected();

        $.ajax({
                  url: $('#path-to-controller').data("href"),
                  type: "GET",
                  dataType: "JSON",
                  data: {
                      liste: rowsel.join(",")
                  },
                  success: function (data) {
                    location.reload();
                  },
                  error: function () {
                      alert("An error ocurred while loading data ...");
                  }
              });
        
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


     $('a.icons1').on('click',function (e) {

   
      var $a = $(this);
      document.getElementById("iddocument1").value = $a.data('user');
      var $tab = null;
            //alert($a.data('credit').toString().indexOf(','));
       if($a.data('credit').toString().indexOf(',') != -1){
        $tab = $a.data('credit').toString().split(',');
        $('#version').empty();
        $.each($tab, function (i, item) { 
            $('#version').append($('<option>', { 
                value:item,
                text :item
            }));
        });
  
       // alert('oui');
      }else{
        $tab = $a.data('credit').toString();
      //  alert('non');
      $('#version').empty();
      $('#version').append($('<option>', { 
        value:$tab,
        text :$tab
    }));
      } 
      

     // alert($tab);
  
     
      
    });

    $('#version_form').on("submit", function(event){  
      event.preventDefault();  
     ;
        var url = $("#path-to-validation1").data("href");
        url= url.replace("1111",  $("#iddocument1").val() );
        url= url.replace("2222",  $("#version").val() );
        jsFormUrl = url;
        form = $('#version_form');
      
        $.ajax({ 
          type:"POST", 
          data:form.serialize(),
          url:jsFormUrl, 
          success:function(data){
            $('#versionModal').modal('hide');   
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

        