var $collectionHolder,$addNewItem=$('<a href="#" class="btn btn-primary bi bi-plus-circle shadow-none" style="width:42px;height:35px;padding-top:7px; margin-left:0%"></a>');function addNewForm(){var e=$collectionHolder.data("prototype"),i=$collectionHolder.data("index"),t=e,o=i;t=t.replace(/__name__/g,i),$collectionHolder.data("index",i++);var n=$('<div class="panel form-group "></div>'),l=$('<div class="row panalArticle"></div>').append(t);if(n.append(l),addRemoveButton(n,o),$addNewItem.before(n),$("#programme_emploi_element_"+o).addClass("row g-3"),$("#programme_emploi_element_"+o+"_rubrique").addClass("rub_selects"),$("#programme_emploi_element_"+o+"_rubrique").click((function(){for($selected_option=$(this).val(),$select_id=$(this).attr("id"),$index=0;$index<=o;$index++)$select_id.includes($index)||$("#programme_emploi_element_"+$index+"_rubrique option[value='"+$selected_option+"']").each((function(){$(this).remove()}))})),o>0){$last=o;var r=[];for($index=0;$index<o;$index++)$option=$("#programme_emploi_element_"+$index+"_rubrique").val(),r.push($option);for($index=0;$index<r.length;$index++)$("#programme_emploi_element_"+$last+"_rubrique option[value='"+r[$index]+"']").each((function(){$(this).remove()}));$("#programme_emploi_element_"+$last+"_rubrique option[value='']").remove()}}function addRemoveButton(e,i){var t=$('<a href="#" class="btn btn-danger bi bi-dash-circle shadow-none" style="width:42px;height:35px; padding-top:7px; float:right"></a>'),o=$('<div style="width:100%; height:30px ; margin-top :10px;"></div>').append(t);t.click((function(e){for($selected_option=$("#programme_emploi_element_"+i+"_rubrique").val(),$select_id=$("#programme_emploi_element_"+i+"_rubrique").attr("id"),$select_txt=$("#programme_emploi_element_"+i+"_rubrique").find("option:selected").text(),e.preventDefault(),$k=$(".rub_selects").length,$index=0;$index<=$k;$index++){$("#programme_emploi_element_"+$index+"_rubrique").append('<option value="'+$selected_option+'">'+$select_txt+"</option>");break}$(e.target).parents(".panel").slideUp(1e3,(function(){$(this).remove()}))})),e.append(o)}$(document).ready((function(){($collectionHolder=$("#article_list")).append($addNewItem),$collectionHolder.find(".panel").each((function(e){addRemoveButton($(this))})),$addNewItem.click((function(e){e.preventDefault(),$collectionHolder.data("index",$collectionHolder.find(".panel").length),addNewForm()}))})),$("#programme_emploi_articlePE").prop("disabled")?$("#article_list").show():$("#article_list").hide();