
//window.setTimeout('location.reload()', 100);

var interval = window.setInterval(func,50);
 clearInterval(interval);

function func(){

}

var $collectionHolder;

var $addNewItem= $('<a href="#" class="btn btn-primary bi bi-plus-circle shadow-none" style="width:42px;height:35px;padding-top:7px; margin-left:0%"></a>');

$(document).ready(function(){

	$collectionHolder = $('#article_list');
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
	var $i_minus = 0
	
if(index == 0 ){
	 $i_minus = 0;
}else{
	 $i_minus = index - 1;
}

    if( index == 0 || ( ($("#programme_emploi_element_"+$i_minus+"_rubrique").val()!='' ) && index > 0  ) ){ //  

		


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



	 $("#programme_emploi_element_"+i).addClass("row g-3");
	 $("#programme_emploi_element_"+i+"_rubrique").addClass("rub_selects");
	
	


/// REMOVE SELECTED OPTION ON CHANGE
	 $("#programme_emploi_element_"+i+"_rubrique").change(function(){

		$selected_option = $(this).val() ;
		$select_id = $(this).attr('id') ; 

		//$selected_option = $("#programme_emploi_element_"+$i+"_rubrique").val() ;
        //$select_id = $("#programme_emploi_element_"+$i+"_rubrique").attr('id') ;

		for ($index = 0; $index <= i; $index++) {

			if(!$select_id.includes($index)){

			$("#programme_emploi_element_"+$index+"_rubrique"+" option[value='"+$selected_option+"']").each(function() 
			{$(this).prop('disabled',true);});

			} 
			}

	 });


/// REMOVE SELECTED OPTION ON ADD BUTTON
 if(i > 0 ){

$last = i;
var $selected_options_array = [] ;

 for ($index = 0; $index < i; $index++) {
  $option = $("#programme_emploi_element_"+$index+"_rubrique").val();
  $selected_options_array.push($option);
 }

 for ($index = 0; $index < $selected_options_array.length; $index++) {
	$("#programme_emploi_element_"+$last+"_rubrique"+" option[value='"+$selected_options_array[$index]+"']").each(function() 
	{$(this).prop('disabled',true);});
	}

	//$("#programme_emploi_element_"+$last+"_rubrique"+" option[value='']").remove();

	
	
  }

}
////// try to group options
   /* for ($j = 0; $j < i; $j++) {

	$("#programme_emploi_element_"+$j+"_rubrique").append("<optgroup label='MDD'></optgroup>");
	$("#programme_emploi_element_"+$j+"_rubrique").append("<optgroup label='Exp'></optgroup>");

	var str_mdd = "---Exploitation MDD";
	var str_personel ="---Exploitation Personnel";




	$("#programme_emploi_element_"+$j+"_rubrique > option").each(function() 
		{
			if($(this).text().indexOf(str_mdd) != -1)
			{
				$( "<optgroup label='MDD'>" ).insertBefore(this);
		
			
			}
	
		});




   } */

	
}

/* function removeDuplicateOptions($array_options_first_select,$i)
{
	for ($m = 0; $m <= $i.length; $index++) {

	for ($index = 0; $index < $array_options_first_select.length; $index++) {
		$("#programme_emploi_element_"+$index+"_rubrique").each(function() 
		if()
		{$(this).remove();});
		}
	}
}
 */
//remove them
function addRemoveButton($panel , $i){
	
	//create remove button
	var $removeButton=$('<a href="#" class="btn btn-danger bi bi-dash-circle shadow-none" style="width:42px;height:35px; padding-top:7px; float:right"></a>');

	var $panelFooter= $('<div style="width:100%; height:30px ; margin :10px;"></div>').append($removeButton);

	$removeButton.click(function(e){

		$selected_option = $("#programme_emploi_element_"+$i+"_rubrique").val() ;
		$select_id = $("#programme_emploi_element_"+$i+"_rubrique").attr('id') ;
		$select_txt = $("#programme_emploi_element_"+$i+"_rubrique").find('option:selected').text(); 

		e.preventDefault();

	$k = $('.rub_selects').length;

	for ($index = 0; $index <= $k; $index++) 
	    {

		$("#programme_emploi_element_"+$index+"_rubrique > option").each(function() 
		{
			
			if($(this).text() == $select_txt ){

				$(this).prop('disabled',false);
			}
	
		});
		

		}
 
		$(e.target).parents('.panel').slideUp(1000,function(){
			$(this).remove();
		});
	});

	$panel.append($panelFooter);



 	
	
}



$('#programme_emploi_articlePE').change(function () {
	var articleSelector = $(this);
	$link_ap = $('#path-to-a-active').data("href") ;

	$varap=$link_ap.replace("ac", $(this).prop('value'));

	// Request the neighborhoods of the selected city.
	
   $.ajax({
		url: $varap,
		type: "GET",
		dataType: "JSON",
		data: {
			article: articleSelector.val()
		},
		success: function (paragarphes) {
			
			var paragrapheSelect = $("#programme_emploi_paragraphe");

			// Remove current options
			paragrapheSelect.html('');
			
			// Empty value ...
			paragrapheSelect.append('<option value>------Selectionner un Paragraphe------</option>');

			$.each(paragarphes, function (key, paragraphe) {
				paragrapheSelect.append('<option value="' + paragraphe.id + '">' + paragraphe.designationFr + '</option>');
			});
		},
		error: function (err) {
			alert("An error ocurred while loading data ...");
		}
	});
});


//$('#article_list').hide();

/* $('#programme_emploi_articlePE').on('change', '', function (e) {

	if($('#programme_emploi_articlePE').val()!== null && $('#programme_emploi_articlePE').val()!== ""){
		$('#article_list').show();

		rubriques();

	}else{
		$('#article_list').hide();
	}; 

});
 */

/* 
$('#btn_select_article').on('click', '', function (e) {

	$('.form_article').prop('disabled', true)

	$('#article_list').show();

	rubriques();


	
}); */


/* function rubriques(){

	//alert("entr");

	
	var url = $("#programe-to-validation").data("href");
	url= url.replace("1111", 1);
    //alert(url);
	$.ajax({ 

		type: "POST",
		dataType: "json",
		url: url,//,url_f,
		success: function(data){  
			setTimeout(function(){// wait for 5 secs(2)
			}, 100);
		  
		},
		error:function(){
			alert('service denied');
		}
	});

} */



	 