(self.webpackChunk=self.webpackChunk||[]).push([[9501],{78707:(e,r,a)=>{var t=a(19755);a(74916),a(15306),a(83710),$link_pe=t("#path-to-pe_active").data("href"),$link_va=t("#path-to-pe_valide").data("href"),$link_per=t("#path-to-pe_periode").data("href"),t(".platformActive").on("change",(function(){$var1=$link_pe.replace("ac",t(this).prop("checked")).replace("val",t(this).prop("value")),t.ajax({type:"POST",url:$var1,success:function(e){},error:function(){alert("erreur activation")}})})),t(".platformValide").on("change",(function(){$var=$link_va.replace("ac",t(this).prop("checked")).replace("val",t(this).prop("value")),t.ajax({type:"POST",url:$var,success:function(e){},error:function(){alert("erreur modification")}})})),t(".finance_periode").on("click",(function(){$var=$link_per.replace("ac",t(this).prop("checked")).replace("val",t(this).prop("value")),$m1="Est-que vous-etes sure de vouloir ouvrir cette periode ?",$m2="Est-que vous-etes sure de vouloir fermer cette periode ?",$msg="",t(this).prop("checked")?$msg=$m1:$msg=$m2,confirm($msg)?(id="#id_periode_"+t(this).prop("value"),d=new Date,year=d.getFullYear(),$date_str=year+"-"+t(id).text(),$date=new Date($date_str),$now=new Date,$date<$now?t.ajax({type:"POST",url:$var,success:function(e){window.location.href="/e-ensat/public/ProgrammeEmploi"},error:function(){alert("erreur modification")}}):(alert("Vous ne pouvez pas activer cette periode car il n'est pas encore achevé"),window.location.href="/e-ensat/public/ProgrammeEmploi")):window.location.href="/e-ensat/public/ProgrammeEmploi"}))},83710:(e,r,a)=>{var t=a(1702),i=a(98052),o=Date.prototype,n="Invalid Date",c="toString",p=t(o[c]),l=t(o.getTime);String(new Date(NaN))!=n&&i(o,c,(function(){var e=l(this);return e==e?p(this):n}))}},e=>{e.O(0,[9755,2109,5306],(()=>{return r=78707,e(e.s=r);var r}));e.O()}]);