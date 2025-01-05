(function($) {

    $('#ligne_articlePE').on('change',function () {
        var articleSelector = $(this);
        
        // Request the neighborhoods of the selected city.
       $.ajax({
            url: $('#path-to-article').data("href"),
            type: "GET",
            dataType: "JSON",
            data: {
                articleid: articleSelector.val()
            },
            success: function (paragraphes) {
                var paragrapheSelect = $("#ligne_paragraphe");

                // Remove current options
                paragrapheSelect.html('');
                
                // Empty value ...
                paragrapheSelect.append('<option value>------Selectionner Paragraphe------</option>');

                $.each(paragraphes, function (key, paragraphe) {
                    paragrapheSelect.append('<option value="' + paragraphe.id + '">' + paragraphe.libelle + '</option>');
                });
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    });

    $('#rubrique_articlePE').on('change',function () {
        var articleSelector = $(this);
        
        // Request the neighborhoods of the selected city.
       $.ajax({
            url: $('#path-to-article').data("href"),
            type: "GET",
            dataType: "JSON",
            data: {
                articleid: articleSelector.val()
            },
            success: function (paragraphes) {
                var paragrapheSelect = $("#rubrique_paragraphe");

                // Remove current options
                paragrapheSelect.html('');
                
                // Empty value ...
                paragrapheSelect.append('<option value>------Selectionner Paragraphe------</option>');

                $.each(paragraphes, function (key, paragraphe) {
                    paragrapheSelect.append('<option value="' + paragraphe.id + '">' + paragraphe.libelle + '</option>');
                });
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    });

    $('#rubrique_paragraphe').on('change',function () {
        var paragrapheSelector = $(this);
        
        // Request the neighborhoods of the selected city.
       $.ajax({
            url: $('#path-to-paragraphe').data("href"),
            type: "GET",
            dataType: "JSON",
            data: {
                paragrapheid: paragrapheSelector.val()
            },
            success: function (lignes) {
                var ligneSelect = $("#rubrique_ligne");

                // Remove current options
                ligneSelect.html('');
                
                // Empty value ...
                ligneSelect.append('<option value>------Selectionner Ligne------</option>');

                $.each(lignes, function (key, ligne) {
                    ligneSelect.append('<option value="' + ligne.id + '">' + ligne.libelle + '</option>');
                });
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    });

})(jQuery);

        