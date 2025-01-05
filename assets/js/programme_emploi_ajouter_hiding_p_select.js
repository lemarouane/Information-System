
$("label[for='paragraphes']").hide();
$('#programme_emploi_add_paragraphes_911').hide();
$("#programme_emploi_add_articlePE").change(function(){

	if ($('#programme_emploi_add_articlePE').val()==1)
	{
		$("label[for='paragraphes']").show();
		$('#programme_emploi_add_paragraphes_911').show();
	}else{
		$("label[for='paragraphes']").hide();
		$('#programme_emploi_add_paragraphes_911').hide();
	}

	});
 
	if($('#programme_emploi_articlePE').prop('disabled') ){
		$('#article_list').show();
	}else{
		$('#article_list').hide();
	}