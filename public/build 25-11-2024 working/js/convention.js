!function(e){langue=e("#langue").val(),"ar-AR"==langue&&(langue="ar"),langue_file="https://cdn.datatables.net/plug-ins/1.13.1/i18n/"+langue+".json";e("#exe1").DataTable({language:{url:langue_file},paging:!0,lengthChange:!0,searching:!0,ordering:!0,info:!0,autoWidth:!1});e("#motifText").hide(),e("#ordre").on("change",(function(){1==e("#ordre").val()?e("#motifText").hide():e("#motifText").show()})),e("#ordre_form").on("submit",(function(t){t.preventDefault();var n=e("#path-to-convention").data("href");n=n.replace("1111",e("#iddocument").val()),jsFormUrl=n,form=e("#ordre_form"),e.ajax({type:"POST",data:form.serialize(),url:jsFormUrl,success:function(t){e("#dataModalOrdre").modal("hide"),setTimeout((function(){location.reload()}),100)},error:function(){alert("service denied")}})})),e("a.icons").on("click",(function(t){var n=e(this);document.getElementById("iddocument").value=n.data("user")})),e("a.icons1").on("click",(function(t){var n=e(this),a=new XMLHttpRequest,o=e("#path-to-nbconvention").data("href");a.open("POST",o,!0),a.setRequestHeader("Content-type","application/x-www-form-urlencoded"),a.onreadystatechange=function(){4==a.readyState&&200==a.status&&(document.getElementById("conventionList").innerHTML=a.responseText)},a.send("codeApogee="+n.data("user"))})),e("#attest_validation_from").on("submit",(function(t){t.preventDefault();var n="stageencad_XXXX".replace("XXXX",e("#n_attest").val());form=e("#attest_validation_from"),e.ajax({type:"POST",data:form.serialize(),url:n,success:function(t){e("#validation_modal").modal("hide"),setTimeout((function(){location.reload()}),100)},error:function(){alert("service denied")}})}))}(jQuery);