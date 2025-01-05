
(function($) {
    emploi('lo'); 
    $(".emploiimage").on('click',null,'co', emploi);  
     
    function emploi($i) {
        Date.prototype.getWeek = function() {
            var onejan = new Date(this.getFullYear(), 0, 1);
            return Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);
        }

        if($i == 'lo') {
            $val='act';
        }else{
           $val =  $(this).val();
        }
        
        $weck=$('#semaine').val();
        
        if($val=='act'){
            $weck=(new Date()).getWeek();
            $annee=(new Date()).getFullYear();
            $('#semaine').val($weck);
            $('#annee').val($annee);
        }else if($val=='add'){
            $weck++;
            $('#semaine').val($weck);
        }else if($val == 'minus'){
            $weck=$weck-1;
            $('#semaine').val($weck);
        }else{
            $weck=$weck;
            $('#semaine').val($weck);
            ;
        }
        $('#image').hide();
        $groupes = [];
        $profs = [];
        $salles = [];
        $('#groupe option').each(function(i) {
            if (this.selected == true) {
                $groupes.push(this.value);
            }
        });
        $('#prof option').each(function(i) {
            if (this.selected == true) {
                $profs.push(this.value);
            }
        });
        $('#salle option').each(function(i) {
            if (this.selected == true) {
                $salles.push(this.value);
            }
        });

        $('.div_imagetranscrits').append('<div class="spinner-border" id="spinner"></div>');
        
            //alert($('#groupe').val());
            $.ajax({
                url:'textToimageedt',
                //dataType: 'json',
                data: {annee: $('#annee').val(), weck: $weck, groupe: $groupes.join(','),profs: $profs.join(','), salles: $salles.join(',')},
                type: "POST",
                //contentType: "image/png",
                success: function(data){

                    $('#spinner').remove();
                    if(data=='0'){
                        $('#image').attr('src','uploads/img/planning.png');
                    }else{
                        $('#image').attr('src',data+ '?' + Math.random());
                    }

                    $('#image').show();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert("Status: " + textStatus); alert("Error: " + errorThrown); 
                } 
        } );
        return false;
    }
})(jQuery);