(function($) {

    
    langue = $('#langue').val() ;
        if(langue=='ar-AR'){langue='ar'}
        langue_file = "https://cdn.datatables.net/plug-ins/1.13.1/i18n/"+langue+".json" ;
    var table = $('#ensa7').DataTable({
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
                url: $('#path-to-convention').data("href"),
                type: "GET",
                dataType: "JSON",
                data: {
                    annee: $('#etudiant_dd_anneeSoutenance').val(),
                    filiere: $('#etudiant_dd_filiere').val(),
                    convention: $('#etudiant_dd_convention').val(),
                    liste: rowsel.join(",")
                },
                success: function (data) {
                    setTimeout(function(){// wait for 5 secs(2)
                        location.reload(); // then reload the page.(3)
                      }, 100);
                    
                },
                error: function (err) {
                    alert("An error ocurred while loading data ...");
                }
            });
        
    });
})(jQuery);