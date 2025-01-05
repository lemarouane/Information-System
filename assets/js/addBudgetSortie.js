(function($) {

    $("#budget_sortie_type1_type_structure").change(function(){
        var typStruct = $(this);

        $link_ap = $('#pathToType').data("href") ;
    
        $varap = $link_ap.replace("1111", typStruct.val());
        
        // Request the neighborhoods of the selected city.
        $.ajax({
            url: $varap,
            type: "GET",
            dataType: "JSON",
            data: {
                typeStruct : typStruct.val()
            },
        
            success: function (structures) {
                var structuresSelect = $("#budget_sortie_type1_structure");
    
                // Remove current options
                structuresSelect.html('');
                
                // Empty value ...
                structuresSelect.append('<option value>------Selectionner Structure------</option>');
    
                $.each(structures, function (key, structures) {
                    structuresSelect.append('<option value="' + structures.id + '">' + structures.libelle + '</option>');
                });
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    });

    $(document).ready(function(){

       
    
        var arr1 = ['Laboratoire des Technologies Innovantes', 'Laboratoire des Technologies de l\'Information et de la Communication', 'Ingénierie de Données et des Systèmes','Equipe de Recherche en  Mathématiques, Informatique et Applications','Mathématique Appliquée et Système Intelligent'];
    
        if($("#budget_sortie_type1_type_structure").val()==2){
    
            var elementBudget = $("#budget_sortie_type1_structure");
            var valeur = $("#budget_sortie_type1_structure").val();
    
            // Remove current options
             elementBudget.html('');
                
            // Empty value ...
            elementBudget.append('<option value>------Selectionner un Structure------</option>');
    
            for ($j = 1; $j <= arr1.length; $j++) {
                if($j==valeur){
                    elementBudget.append('<option value="' + $j+ '" selected="selected">' + arr1[$j-1] + '</option>');
                }else{
                    elementBudget.append('<option value="' + $j+ '">' + arr1[$j-1] + '</option>');
                }
                    
            }
    
        }     
        
    });
    

})(jQuery);

        