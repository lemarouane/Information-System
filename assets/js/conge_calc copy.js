$('#conge_Annee_encours').click(function() {
    $('#conge_Annee_encours').prop('checked', true); 
    $('#conge_Annee_precedente').prop('checked', false); 
    annee_conge();
    max_jours_calc();
});

$('#conge_Annee_precedente').click(function() {
    $('#conge_Annee_encours').prop('checked', false); 
    $('#conge_Annee_precedente').prop('checked', true); 
    annee_conge(); 
    max_jours_calc();
});

if($('#conge_annee').val() == new Date().getFullYear() ) {
    $('#conge_Annee_encours').prop('checked', true); 
    $('#conge_Annee_precedente').prop('checked', false); 
   }
   if($('#conge_annee').val() == new Date().getFullYear() - 1 ) {
    $('#conge_Annee_encours').prop('checked', false); 
    $('#conge_Annee_precedente').prop('checked', true); 
   }
 
$('#conge_nbJour').keypress(function(e) {
    return false
});
$('#conge_dateDebut').keypress(function(e) {
    return false
});
$('#conge_dateReprise').keypress(function(e) {
    return false
});
$("#conge_dateDebut").attr("autocomplete", "off");
$("#conge_dateReprise").attr("autocomplete", "off");


annee_conge();
max_jours_calc();



var  pick_debut = 0 ; 
var  pick_fin = 0 ;

$('#conge_typeConge').change(function() {
    max_jours_calc();
});


 $('#conge_dateDebut').change(function() {

    pick_debut =  $('#conge_dateDebut').val() ;
    nbjour = calcDaysWV(new Date(pick_debut),new Date(pick_fin)) ;
    if(nbjour>0 && nbjour<365 && pick_fin!=null && pick_fin!=0){
        $('#conge_nbJour').val(nbjour);
    }else{
        $('#conge_nbJour').val(null);
    }
    $('#conge_dateReprise').datepicker('setStartDate', new Date($('#conge_dateDebut').val()) );
    max_jours_calc();
});
 
$('#conge_dateReprise').change(function() {

    pick_fin =  $('#conge_dateReprise').val() ;
    nbjour = calcDaysWV(new Date(pick_debut),new Date(pick_fin)) ;
    if(nbjour>0 && nbjour<365 && pick_debut!=null && pick_debut!=0){
        $('#conge_nbJour').val(nbjour);
    }else{
        $('#conge_nbJour').val(null);
    }
    $('#conge_dateDebut').datepicker('setEndDate', new Date($('#conge_dateReprise').val()) );
    max_jours_calc();
});

$('.js-datepicker').datepicker.dates['fr-FR'] = {
    days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
    daysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
    daysMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
    months: ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"],
    monthsShort: ["Jan", "Fev", "Mar", "Avr", "Mai", "Jun", "Jul", "Aou", "Sep", "Oct", "Nov", "Dec"],
    today: "Aujourd'hui",
    clear: "Effacer",
    format: "yyyy-mm-dd",
    titleFormat: "yyyy MM",
    weekStart: 1,
};
var annee = new Date().getFullYear().toString() ;
//                 nouvel an  -    manif.indep    - f.travail       - f.trone      - j.oued dahab   - revolution rp     - ann r     -  marche verte -     f.indep
var vacances = [annee+"-01-01" ,,annee+"-01-11" , annee+"-05-01" , annee+"-07-30" , annee+"-08-14", annee+"-08-20" , annee+"-08-21" , annee+"-11-06" , annee+"-08-18" ];
$('.js-datepicker').datepicker({
    daysOfWeekDisabled: [0,6],
    autoclose : true ,
    language : "fr-FR",
    todayHighlight : true,
    orientation: 'right bottom',
    startDate: new Date(),
    datesDisabled: vacances
});
 


function calcDaysWV(dDate1, dDate2) {   
    nb_days = (Math.floor((dDate2.getTime() - dDate1.getTime()) / 86400000)) + 1 ; 
    result = 1 ;
    day_start = dDate1 ;

for (let i = 1; i < nb_days; i++) {
     day_start.setDate(day_start.getDate() + 1) ;
     
     day_str = fixDigit(day_start.getDate()) ;
     month_str = fixDigit(day_start.getMonth() + 1) ;
     year_str = day_start.getFullYear() ;

    if(!vacances.includes(year_str.toString()+"-"+month_str.toString()+"-"+day_str.toString()) && day_start.getDay()!=0  && day_start.getDay()!=6 )     // 6=samedi ,0=dimanche   
    {
     result ++ ;
    }
      } 
     return result ;
}  

function fixDigit(val){
    return val.toString().length === 1 ? "0" + val : val;
  }

function annee_conge() {

 
    if($('#conge_Annee_encours').is(':checked')){
        $('#conge_annee').val(new Date().getFullYear());
        zero_conge_encours();
    }
    if($('#conge_Annee_precedente').is(':checked')){
        $('#conge_annee').val(new Date().getFullYear()-1);
        zero_conge_prec();
    }
  //  alert($('#conge_annee').val()) ;
    }


function max_jours_calc(){

        if($('#conge_Annee_encours').is(':checked') 
         && $('#conge_typeConge').find(":selected").val()=="N"){
            $("#conge_nbJour").attr({
                "max" : parseInt( $('#cn_annee_encours').text() ),   // 
                "min" : 1        
             });
        }

        if($('#conge_Annee_encours').is(':checked') 
        && $('#conge_typeConge').find(":selected").val()=="E"){
           $("#conge_nbJour").attr({
               "max" : parseInt( $('#ce_annee_encours').text() ),   // 
               "min" : 1        
            });
       }
    
        if($('#conge_Annee_precedente').is(':checked')  
        && $('#conge_typeConge').find(":selected").val()=="N"){
            $("#conge_nbJour").attr({
                "max" : parseInt( $('#cn_annee_prec').text() ) ,     // parseInt( $('#cn_annee_prec').text() )
                "min" : 1        
             });
        }

        if($('#conge_Annee_precedente').is(':checked')  
        && $('#conge_typeConge').find(":selected").val()=="E"){
            $("#conge_nbJour").attr({
                "max" : parseInt( $('#ce_annee_prec').text() ) ,     // parseInt( $('#cn_annee_prec').text() )
                "min" : 1        
             });
        }    
    
    }

function zero_conge_encours() {

if( $("#cn_annee_encours").text()=="0" ){
    $('#conge_typeConge option[value="N"]').attr("disabled", true);
}else{
    $('#conge_typeConge option[value="N"]').attr("disabled", false);
}

if( $("#ce_annee_encours").text()=="0" ){
    $('#conge_typeConge option[value="E"]').attr("disabled", true);
}else{
    $('#conge_typeConge option[value="E"]').attr("disabled", false);
}
 

    }

function zero_conge_prec(){

if( $("#ce_annee_prec").text()=="0" ){
    $('#conge_typeConge option[value="E"]').attr("disabled", true);
}else{
    $('#conge_typeConge option[value="E"]').attr("disabled", false);
}

if( $("#cn_annee_prec").text()=="0" ){
    $('#conge_typeConge option[value="N"]').attr("disabled", true);
}else{
    $('#conge_typeConge option[value="N"]').attr("disabled", false);
}

    }