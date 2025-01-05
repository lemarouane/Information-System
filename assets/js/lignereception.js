var $collectionHolder;

var $addNewItem= $('<a href="#" class="btn btn-primary bi bi-plus-circle shadow-none" style="width:42px;height:35px;padding-top:7px; margin-left:90%"></a>');


$(document).ready(function(){

	$collectionHolder = $('#lignereception_list');
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
		
/* 	$(".js-datepicker").datepicker({
        minDate: 0,
        maxDate: "+50Y",
        numberOfMonths: 1,
        language:'fr',
        dateFormat : 'yy-mm-dd',
        prevText : '<i class="fa fa-chevron-left"></i>',
        nextText : '<i class="fa fa-chevron-right"></i>',
    });

	$('.js-eng-datepicker').datepicker.dates['fr-FR'] = {
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
	
	$('.js-eng-datepicker').datepicker({
	  //  daysOfWeekDisabled: [0,6],
		autoclose : true ,
		language : "fr-FR",
		todayHighlight : true,
		orientation: 'right bottom',
		startDate: new Date(),
	   // datesDisabled: vacances
	});
	  
	$(".js-eng-datepicker").attr("autocomplete", "off");
	$('.js-eng-datepicker').keypress(function(e) {
		return false
	});
    */

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

	 $("#reception_receptionlignes_"+i).addClass("row g-3");
}
//remove them
function addRemoveButton($panel){

	//create remove button
	var $removeButton=$('<a href="#" class="btn btn-danger bi bi-dash-circle shadow-none" style="width:42px;height:35px; padding-top:7px;"></a>');

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



