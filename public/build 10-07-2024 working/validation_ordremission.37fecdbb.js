(self.webpackChunk=self.webpackChunk||[]).push([[2549],{59182:(t,e,o)=>{var r,a=o(19755);o(74916),o(15306),o(32564),(r=a)(document).on("click","#odo",(function(){var t=r(this);$vartest=t.data("user");var e=r("#path-to-ordre_m").data("href");e=e.replace("1111",$vartest),r.ajax({url:e,type:"GET",dataType:"JSON",success:function(t){setTimeout((function(){location.reload()}),100)},error:function(){}})})),r("#attest_validation_from").on("submit",(function(t){t.preventDefault();var e="ordre_missionVAL_XXXX".replace("XXXX",r("#n_attest").val());form=r("#attest_validation_from"),r.ajax({type:"POST",data:form.serialize(),url:e,success:function(t){r("#validation_modal").modal("hide"),setTimeout((function(){location.reload()}),100)},error:function(){alert("service denied")}})})),r("#attest_om_date_from").on("submit",(function(t){t.preventDefault();var e="ordremissionPdf_date_XXXX".replace("XXXX",r("#n_om_modal").val());form=r("#attest_om_date_from"),r.ajax({type:"POST",data:form.serialize(),url:e,success:function(t){r("#om_date_modal").modal("hide"),setTimeout((function(){location.reload()}),100)},error:function(){alert("service denied")}})}))},50206:(t,e,o)=>{var r=o(1702);t.exports=r([].slice)},17152:(t,e,o)=>{var r=o(17854),a=o(22104),n=o(60614),i=o(88113),s=o(50206),u=o(48053),l=/MSIE .\./.test(i),c=r.Function,d=function(t){return l?function(e,o){var r=u(arguments.length,1)>2,i=n(e)?e:c(e),l=r?s(arguments,2):void 0;return t(r?function(){a(i,this,l)}:i,o)}:t};t.exports={setTimeout:d(r.setTimeout),setInterval:d(r.setInterval)}},48053:t=>{var e=TypeError;t.exports=function(t,o){if(t<o)throw e("Not enough arguments");return t}},96815:(t,e,o)=>{var r=o(82109),a=o(17854),n=o(17152).setInterval;r({global:!0,bind:!0,forced:a.setInterval!==n},{setInterval:n})},88417:(t,e,o)=>{var r=o(82109),a=o(17854),n=o(17152).setTimeout;r({global:!0,bind:!0,forced:a.setTimeout!==n},{setTimeout:n})},32564:(t,e,o)=>{o(96815),o(88417)}},t=>{t.O(0,[9755,2109,5306],(()=>{return e=59182,t(t.s=e);var e}));t.O()}]);