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


  })(jQuery);

  var $collectionHolder;

  var $addNewItem= $('<a href="#" class="btn btn-primary bi bi-plus-circle shadow-none" style="width:42px;height:35px;padding-top:7px; margin-left:0%"></a>');
  
  
  $(document).ready(function(){
  
      $collectionHolder = $('#affectation_list');
      // append the add new item to the collectionHolder
      $collectionHolder.append($addNewItem);
  
      //add remove button to existing items
      $collectionHolder.find('.panel').each(function(item){
  
       addRemoveButton($(this));	
      });
  
      
      $addNewItem.click(function(e){
      
          e.preventDefault();
          
          $collectionHolder.data('index',$collectionHolder.find('.panel').length);
          addNewForm();  
      });
  
  
      
  });
  //add new items (engagement forms)
  function addNewForm(){
  
      //create the form
      var prototype= $collectionHolder.data('prototype');
  
      var index = $collectionHolder.data('index');
  
       //create form
       var newForm = prototype;
       var i = index ;
       newForm = newForm.replace(/__name__/g, index);
  
  
  
       $collectionHolder.data('index', index++);
  
       //create panel
  
       var $panel= $('<div class="panel form-group "></div>');
       //creat the panel body
  
       var $label = $('<div class="row panalEngagement"></div>').append(newForm);
  
       $panel.append($label);
  
       
  
       addRemoveButton($panel);
  
       $addNewItem.before($panel);
  
       $("#affectations_"+i).addClass("row g-3");
  }
  //remove them
  function addRemoveButton($panel){
  
      //create remove button
      var $removeButton=$('<a href="#" class="btn btn-danger bi bi-dash-circle shadow-none" style="width:42px;height:35px; padding-top:7px; float:right"></a>');
  
      var $panelFooter= $('<div style="width:100%; height:30px ; margin-top :10px;"></div>').append($removeButton);
  
      $removeButton.click(function(e){
          e.preventDefault();
          $(e.target).parents('.panel').slideUp(1000,function(){
              $(this).remove();
          });
      });
  
      $panel.append($panelFooter);
      //
  }   