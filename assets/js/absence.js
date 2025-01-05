(function($) {


    langue = $('#langue').val() ;
    if(langue=='ar-AR'){langue='ar'}
    langue_file = "https://cdn.datatables.net/plug-ins/1.13.1/i18n/"+langue+".json" ;

 
    var table = $('#exemple12').DataTable({
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
                  url: $('#path-to-add-absence').data("href"),
                  type: "GET",
                  dataType: "JSON",
                  data: {
                    etape: $('#absence_etape').val(),
                    module: $('#absence_module').val(),
                    matiere: $('#absence_matiere').val(),
                    date: $('#absence_dateabsence').val(),
                    seance: $('#absence_seance').val(),
                    liste: rowsel.join(","),
                  },
                  success: function (data) {
                    location.reload();
                  },
                  error: function () {
                      alert("An error ocurred while loading data ...");
                  }
              });
        
      });




  

      $('#absence_etape').on('change',function () {
        var etapeSelector = $(this);
        
        // Request the neighborhoods of the selected city.
           $.ajax({
            url: $('#path-to-list_module').data("href"),
            type: "GET",
            dataType: "JSON",
            data: {
                etape: etapeSelector.val()
            },
            success: function (modules) {
                var moduleSelect = $("#absence_module");

                // Remove current options
                moduleSelect.html('');
                
                // Empty value ...
                moduleSelect.append('<option value>------Selectionner Module------</option>');

                $.each(modules, function (key, echlon) {
                    moduleSelect.append('<option value="' + echlon.id + '">' + echlon.id + '</option>');
                });
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    });

    $('#absence_module').on('change',function () {
        var moduleSelector = $(this);
        
        // Request the neighborhoods of the selected city.
           $.ajax({
            url: $('#path-to-list_matiere').data("href"),
            type: "GET",
            dataType: "JSON",
            data: {
                module: moduleSelector.val()
            },
            success: function (matieres) {
                var matiereSelect = $("#absence_matiere");

                // Remove current options
                matiereSelect.html('');
                
                // Empty value ...
                matiereSelect.append('<option value>------Selectionner Matière------</option>');

                $.each(matieres, function (key, echlon) {
                    matiereSelect.append('<option value="' + echlon.id + '">' + echlon.id + '</option>');
                });
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    });
  })(jQuery);

        