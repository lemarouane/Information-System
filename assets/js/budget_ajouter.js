
//window.setTimeout('location.reload()', 100);

var interval = window.setInterval(func,50);
 clearInterval(interval);

function func(){

}


var $collectionHolder;
var indexB=0;
var $addNewItem= $('<a href="#" class="btn btn-primary bi bi-plus-circle shadow-none" style="width:42px;height:35px;padding-top:7px; margin-left:0%"></a>');

$(document).ready(function(){

	$collectionHolder = $('#budget_list');
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

	indexB = $collectionHolder.find('.panel').length;

	var arr1 = ['Laboratoire des Technologies Innovantes', 'Laboratoire des Technologies de l\'Information et de la Communication', 'Ingénierie de Données et des Systèmes','Equipe de Recherche en  Mathématiques, Informatique et Applications','Mathématique Appliquée et Système Intelligent'];

	for ($i = 0; $i < indexB; $i++) {
		if($("#budget_type1_budgetEntrees_"+$i+"_temoin").val()=='F'){

			$("#budget_type1_budgetEntrees_"+$i).addClass("disabled");
		}
		if($("#budget_budgetSorties_"+$i+"_type_structure").val()==2){

			var elementBudget = $("#budget_budgetSorties_"+$i+"_structure");
			var valeur = $("#budget_budgetSorties_"+$i+"_structure").val();

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
	
		if($("#budget_budgetSorties_"+$i+"_temoin").val()=='F'){
	

			$("#budget_budgetSorties_"+$i).addClass("disabled");
		}
		
	   }



	
	
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

	 var $label = $('<div class="row panalArticle"></div>').append(newForm);

	 $panel.append($label);

	 

	 addRemoveButton($panel,i);

	 $addNewItem.before($panel);

	 $("#budget_budgetSorties_"+i).addClass("row g-3");


	/// REMOVE SELECTED OPTION ON CHANGE
	$(".typeStruct").change(function(){
		var typStruct = $(this);

		struct= $(this).attr('id').replace('_type_','_');
		$link_ap = $('#pathToTypeStructure').data("href") ;

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
				var structuresSelect = $("#"+struct);

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

}

/// REMOVE SELECTED OPTION ON ADD BUTTON
$(".typeStruct").change(function(){
	var typStruct = $(this);

	struct= $(this).attr('id').replace('_type_','_');
	$link_ap = $('#pathToTypeStructure').data("href") ;

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
			var structuresSelect = $("#"+struct);

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
//remove them
function addRemoveButton($panel , $i){
	
	//create remove button
	var $removeButton=$('<a href="#" class="btn btn-danger bi bi-dash-circle shadow-none" style="width:42px;height:35px; padding-top:7px; float:right"></a>');

	var $panelFooter= $('<div style="width:100%; height:30px ; margin :10px;"></div>').append($removeButton);

	$removeButton.click(function(e){


		e.preventDefault();

 
		$(e.target).parents('.panel').slideUp(1000,function(){
			$(this).remove();
		});
	});

	$panel.append($panelFooter);

}







	 