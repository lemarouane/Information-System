(function($) {

    $('#article_categorie').on('change',function () {
        var categorieSelector = $(this);
        
        // Request the neighborhoods of the selected city.
       $.ajax({
            url: $('#path-to-categorie').data("href"),
            type: "GET",
            dataType: "JSON",
            data: {
                categorieid: categorieSelector.val()
            },
            success: function (souscategories) {
                var souscategorieSelect = $("#article_souscategorie");

                // Remove current options 
                souscategorieSelect.html('');
                
                // Empty value ...
                souscategorieSelect.append('<option value>------Selectionner Sous Categorie 1------</option>');

                $.each(souscategories, function (key, souscategorie) {
                    souscategorieSelect.append('<option value="' + souscategorie.id + '">' + souscategorie.designation + '</option>');
                });
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    });

    
    

})(jQuery);

        