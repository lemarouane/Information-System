var $collectionHolder;

var $addNewItem= $('<a href="#" class="btn btn-primary bi bi-plus-circle shadow-none" style="width:42px;height:35px;padding-top:7px; margin-left:90%"></a>');


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

	 $("#decharge_affectation_"+i).addClass("row g-3");

	 OnlyConsommable();
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

function OnlyConsommable() {
		// Find all select boxes with IDs matching the pattern
		var selects = document.querySelectorAll('[id^="decharge_c_affectations_"][id$="_article"]');
	
		// Loop through each select box
		selects.forEach(function (select) {
			// Remove options with no text content
			for (var i = select.options.length - 1; i >= 0; i--) {
				if (!select.options[i].textContent.trim()) {
					select.remove(i);
				}
			}
		});
}

 


