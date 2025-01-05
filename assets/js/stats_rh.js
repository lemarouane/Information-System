$(function() {
	"use strict";
    var annee_pr_c5 = [];
    var annee_ad_c5 = [];
  
    var nb_pr_c5 = [];
    var nb_ad_c5 = [];

    $.ajax({
        type: "GET",
        dataType: "json",
        url: 'recrutementDateAd',
        success: function(data){
          $.each(data, function (index, value) {  
            nb_ad_c5.push(value['nb']) ;
            annee_ad_c5.push(value['year']);
          });
    
        },
        error:function(){
        }
      });
    
     
//////////////////////////////////// PART1 OF CHART2 SEPERATED BECAUSE OF SYNCRONISATION PROBLEME

var nb_act = [];
var act = [];

$.ajax({
  type: "GET",
  dataType: "json",
  url: 'effectifParActivite',
  success: function(data){
    $.each(data, function (index, value) {  
      nb_act.push(value['nb']) ;
      act.push(value['activite']);
    });

    for (let k = 0; k < act.length; k++) {
      if(act[k].toString() == "N"){
        $("#act_n").html(nb_act[k].toString())  ;
      }
      if(act[k].toString() == "R"){
        $("#act_r").html(nb_act[k].toString())  ;
      }
      if(act[k].toString() == "M"){
        $("#act_m").html(nb_act[k].toString())  ;
      }
      if(act[k].toString() == "A"){
        $("#act_a").html(nb_act[k].toString())  ;
      }
  
    }
    
  },
  error:function(){
  }
});




////////////////////////////

    var annee_nbeff = [];
    var nb_nbeff = [];
    var somme=0;
     $.ajax({
       type: "GET",
       dataType: "json",
       url: 'effectifevolution',
       success: function(data){
         $.each(data, function (index, value) {
            
            somme = somme + parseInt(value['nb']);
            nb_nbeff.push(somme) ;
            annee_nbeff.push(value['year']);
          // dataN.push({y: value['year'], a: somme});
         });

         var options = {
            series: [{
                name: "Effectif Totale",
                data: nb_nbeff ,//[0, 650, 440, 160, 350, 414]
            }],
            chart: {
                foreColor: '#9a9797',
                type: "bar",
                //width: 130,
                height: 270,
                toolbar: {
                    show: !1
                },
                zoom: {
                    enabled: !1
                },
                dropShadow: {
                    enabled: 0,
                    top: 3,
                    left: 14,
                    blur: 4,
                    opacity: .12,
                    color: "#3461ff"
                },
                sparkline: {
                    enabled: !1
                }
            },
            markers: {
                size: 0,
                colors: ["#3461ff", "#12bf24"],
                strokeColors: "#fff",
                strokeWidth: 2,
                hover: {
                    size: 7
                }
            },
            plotOptions: {
                bar: {
                    horizontal: !1,
                    columnWidth: "40%",
                    endingShape: "rounded"
                }
            },
            legend: {
                show: false,
                position: 'top',
                horizontalAlign: 'left',
                offsetX: -20
            },
            dataLabels: {
                enabled: !1
            },
            grid: {
                show: false,
                // borderColor: '#eee',
                // strokeDashArray: 4,
            },
            stroke: {
                show: !0,
               // width: 3,
                curve: "smooth"
            },
            colors: ["#12bf24"],
            xaxis: {
                categories: annee_nbeff ,
                labels: {
                    style: {
                        fontSize: '9px'
                    }
               }
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return "" + val + ""
                    }
                }
            }
          };
        
          var chart = new ApexCharts(document.querySelector("#chart1"), options);
          chart.render();
        
       },
       error:function(){


       }
     });


     //END CHART1

////////////////


$.ajax({
  type: "GET",
  dataType: "json",
  url: 'repCorpsAdmin',
  success: function(data){
    $.each(data, function (index, value) {  
    // $("#ul_adm").append("<li class='list-group-item'><div class='d-flex align-items-center gap-2'><div>"+value['designation_fr'].toString()+"</div><div class='ms-auto'>"+value['nb'].toString()+"</div></div></li>");
    $("#ul_adm").append("<tr><td>"+value['designation_fr'].toString()+"</td><td>"+value['nb'].toString()+"</td></tr>");
    });



  },
  error:function(){
  }
});



$.ajax({
  type: "GET",
  dataType: "json",
  url: 'repCorpsEnseignant',
  success: function(data){
    $.each(data, function (index, value) {  
     //$("#ul_ens").append("<li class='list-group-item'><div class='d-flex align-items-center gap-2'><div>"+value['designation_fr'].toString()+"</div><div class='ms-auto'>"+value['nb'].toString()+"</div></div></li>");
      $("#ul_ens").append("<tr><td>"+value['designation_fr'].toString()+"</td><td>"+value['nb'].toString()+"</td></tr>");
    });



  },
  error:function(){
  }
});








//////////////////////




//////////////////////////////////////// Chart6 (donut)

var colors_palette1 = ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C","#005bea", "#ee0979", "#17ad37"];
var libelle_dep = [];
var personnel_dep= [];

$.ajax({
    type: "GET",
    dataType: "json",
    url: 'effectifParDep',
    success: function(data){
      $.each(data, function (index, value) {  
        personnel_dep.push( parseInt(value['nb'])) ;
        libelle_dep.push(value['libelle_dep']);
      });


var options = {
    series: personnel_dep,
    chart: {
        height: 250,
        type: 'donut',
        foreColor: '#373d3f'
  },
  plotOptions: {
    pie: {
      startAngle: -90,
      endAngle: 270
    }
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val.toFixed(2) + "%"
    },
    dropShadow: {
      
    }
  },
  tooltip: {

    theme: 'dark'
},
  colors: colors_palette1 ,
  labels: libelle_dep,
  fill: {
    type: 'gradient',
  },
  legend: {
    formatter: function(val, opts) {
      return val + " : " + opts.w.globals.series[opts.seriesIndex]
    },
    fontSize: '10px',
   
  },
  title: {
    text: ''
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom',
       
      }
    }
  }]
  };

  var chart = new ApexCharts(document.querySelector("#chart6"), options);
  chart.render();

    },
    error:function(){
    }
  });

//////////////////////// END CHART 6


//////////////////////////////////////// Chart7 (donut)

var colors_palette2 = ["#00c6fb", "#ff6a00","#0226fb", "#f11a00"];
var libelle_type = [];
var personnel_type= [];
var somme_pr = 0;
var somme_adm = 0;
var somme_personnel = 0;
$.ajax({
    type: "GET",
    dataType: "json",
    url: 'effectifParType',
    success: function(data){
      $.each(data, function (index, value) {  
        personnel_type.push( parseInt(value['nb'])) ;
        libelle_type.push(value['libelle_personnel']);

       if(value['id']==2 || value['id']==4){
        somme_adm = parseInt(somme_adm) + parseInt(value['nb']) ;
       }else{
        somme_pr = parseInt(somme_pr) + parseInt(value['nb']) ;
       }
        
      });
      somme_personnel = somme_adm+somme_pr ;
$("#adm_totale").html(somme_adm.toString());
$("#pr_totale").html(somme_pr.toString());
$("#personnel_totale").html(somme_personnel.toString());
var options = {
    series: personnel_type,
    chart: {
        height: 250,
        type: 'donut',
        foreColor: '#fff'
  },
  plotOptions: {
    pie: {
      startAngle: -90,
      endAngle: 270
    }
  },

  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val.toFixed(2) + "%"
    },
    dropShadow: {
   
    }
  },
  tooltip: {

    theme: 'dark'
},
  colors: colors_palette2 ,
  labels: libelle_type,
  fill: {
    type: 'gradient',
  },
  legend: {
    formatter: function(val, opts) {
      return val + " : " + opts.w.globals.series[opts.seriesIndex]
    } ,
    fontSize: '9px'
  },
  title: {
    text: ''
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom',
       
      }
    }
  }]
  };

  var chart = new ApexCharts(document.querySelector("#chart7"), options);
  chart.render();

    },
    error:function(){
    }
  });

//////////////////////// END CHART 7




//////////////////////////////////////// Chart8 (donut)

var colors_palette3 = ["#FF0080", "#00c6fb"];
var libelle_genre = [];
var personnel_genre= [];
//var somme_genre = 0;

$.ajax({
    type: "GET",
    dataType: "json",
    url: 'effectifParGenre',
    success: function(data){
      $.each(data, function (index, value) {  
        personnel_genre.push( parseInt(value['nb'])) ;
        libelle_genre.push(value['genre']);
       // somme_genre = parseInt(somme_genre) + parseInt(value['nb']) ;
      });

      $("#personnel_femme").html(personnel_genre[0].toString()) ;
      $("#personnel_homme").html(personnel_genre[1].toString()) ;
     


    },
    error:function(){
    }
  });

//////////////////////// END CHART 8



//////////////////////////////////////// Chart9 (donut)

var colors_palette4 = ["#00c6fb", "#ff6a00","#0226fb", "#CF3476", "#FF2301","#317F43", "#755C48" ,"#DE4C8A","#B5B8B1", "#A18594" ,"#025669","#BEBD7F", "#7E7B52"];
var libelle_service = [];
var personnel_service= [];

$.ajax({
    type: "GET",
    dataType: "json",
    url: 'effectifParService',
    success: function(data){
      $.each(data, function (index, value) {  
        personnel_service.push( parseInt(value['nb'])) ;
        libelle_service.push(value['nom_service']);
      });


var options = {
    series: personnel_service,
    chart: {
        height: 250,
        type: 'pie',
        foreColor: '#373d3f',
        offsetX: -40
  },
  plotOptions: {
    pie: {
      startAngle: -90,
      endAngle: 270,
      
    }
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val.toFixed(2) + "%"
    },
    dropShadow: {
   
    }
  },
  tooltip: {

    theme: 'dark'
},
  colors: colors_palette4 ,
  labels: libelle_service,
  fill: {
    type: 'gradient',
  },
  legend: {
    formatter: function(val, opts) {
      return val + " : " + opts.w.globals.series[opts.seriesIndex]
    } ,
    fontSize: '9px',
    
  },
  title: {
    text: ''
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom',
       
      }
    }
  }]
  };

  var chart = new ApexCharts(document.querySelector("#chart9"), options);
  chart.render();

    },
    error:function(){
    }
  });

//////////////////////// END CHART 9






//////////////////////////// PART2 OF CHART5 SEPARATED BECAUSE OF SYNCRONISATION PROBLEM
   $.ajax({
     type: "GET",
     dataType: "json",
     url: 'recrutementDatePr',
     success: function(data){
       $.each(data, function (index, value) {  
         nb_pr_c5.push(value['nb']) ;
         annee_pr_c5.push(value['year']);
       });

     
 var max_annee_pr_c5 = Math.max.apply(Math, annee_pr_c5);
 var min_annee_pr_c5 = Math.min.apply(Math, annee_pr_c5);
 var max_annee_ad_c5 = Math.max.apply(Math, annee_ad_c5);
 var min_annee_ad_c5 = Math.min.apply(Math, annee_ad_c5);

  var max_annee = 0;
  var min_annee = 0;
  var nb_pr_filtred_c5 = [];
  var nb_ad_filtred_c5 = [];
  var annee_filtred_c5 = [];

  if(max_annee_pr_c5>=max_annee_ad_c5){
     max_annee = max_annee_pr_c5 ;
  }else{
     max_annee = max_annee_ad_c5 ;
  }

  if(min_annee_pr_c5<=min_annee_ad_c5){
     min_annee = min_annee_pr_c5 ;
  }else{
     min_annee = min_annee_ad_c5 ;
  }
  var value_pr ;
  var value_ad ;
  for ( let i =  parseInt(min_annee); i <=  parseInt(max_annee); i++) {

     value_pr = annee_pr_c5.indexOf(i.toString()) ;
     if(value_pr!=-1){
         
         nb_pr_filtred_c5.push(nb_pr_c5[value_pr]) ;
     }else{
         nb_pr_filtred_c5.push(0) ;
     }

     value_ad = annee_ad_c5.indexOf(i.toString()) ;
     if(value_ad!=-1){
         nb_ad_filtred_c5.push(nb_ad_c5[value_ad]) ;
     }else{
         nb_ad_filtred_c5.push(0) ;
     }
     annee_filtred_c5.push(i) ;
   }


       var optionsLine = {
         chart: {
             foreColor: '#9ba7b2',
             height: 275,
             type: 'line',
             toolbar: {
                 show: !1
             },
             zoom: {
                 enabled: false
             },
             dropShadow: {
                 enabled: true,
                 top: 3,
                 left: 2,
                 blur: 4,
                 opacity: 0.1,
             }
         },
         tooltip: {

             theme: 'dark'
         },
         stroke: {
             curve: 'smooth',
             width: 3
         },
         colors: ["#32bfff", '#ff6632'],
         series: [{
             name: "Professeurs",
             data: nb_pr_filtred_c5
         }, {
             name: "Administratifs",
             data:  nb_ad_filtred_c5 // [1,2] //
         }],
         markers: {
             size: 4,
             strokeWidth: 0,
             hover: {
                 size: 7
             }
         },
         grid: {
             show: true,
             padding: {
                 bottom: 0
             }
         },
         //labels: ['01/15/2002', '01/16/2002', '01/17/2002', '01/18/2002', '01/19/2002', '01/20/2002'],
         xaxis: {
             //type: 'datetime',
             categories:  annee_filtred_c5,
             labels: {
                 style: {
                     fontSize: '9px'
                 }
            }
         },
         legend: {
             position: 'top',
             horizontalAlign: 'right',
             offsetY: -20
         }
     }
     var chartLine = new ApexCharts(document.querySelector('#chart5'), optionsLine);
     chartLine.render();
     


     },
     error:function(){
     }
   });



   //////// END CHART5








});