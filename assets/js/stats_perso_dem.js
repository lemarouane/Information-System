$(function() {
	"use strict";
  


   //////// END CHART5


   var pers_id = $("#personnel_id").val();
   var pers_type = $("#personnel_type").val();

   var annee_passee = [];
   var count_attest = [];
   var count_conge = [];
   var count_auto= [];
   var count_om= [];
   var count_fh= [];

   var  attest_url = null;
   var  auto_url = null ;
   var  conge_url = null ;
   var  om_url = null ;
   var  fh_url = null ;

   var  redirect_url = null ;

   $.ajax({
    type: "GET",
    dataType: "json",
    url: 'annee_service_personnel_'+pers_id,
    success: function(data){

      if(pers_type == 2){


      $.each(data[0], function (index, value) {  
        annee_passee.push(value) ;
      });
      $.each(data[1], function (index, value) {  
        count_attest.push(value) ;
      });
      $.each(data[2], function (index, value) {  
        count_conge.push(value) ;
      });
      $.each(data[3], function (index, value) {  
        count_auto.push(value) ;
      });
      $.each(data[4], function (index, value) {  
        count_om.push(value) ;
      });

    }else{

$.each(data[0], function (index, value) {  
        annee_passee.push(value) ;
      });
      $.each(data[1], function (index, value) {  
        count_attest.push(value) ;
      });
   
      $.each(data[2], function (index, value) {  
        count_fh.push(value) ;
      });
      $.each(data[3], function (index, value) {  
        count_om.push(value) ;
      });
    }




     /*  $.ajax({
        type: "GET",
        dataType: "json",
        url: 'recrutementDateAd',
        success: function(data){
          $.each(data, function (index, value) {  
      
          });
    
        },
        error:function(){
        }
      });
     */





   var options_adm = {




    series: [{
        name: "Attestations",
        data: count_attest
    } ,{
        name: "Congés",
        data: count_conge
    },{
       name: "Autorisations",
       data: count_auto
    },{
       name: "Ordre de Missions",
       data: count_om
  } ]
  
  

  ,
    chart: {
        foreColor: '#9a9797',
        type: "bar",
        //width: 130,
        height: 280,
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
            color: "#3361ff"
        },
        sparkline: {
            enabled: !1
        },
        
        events: {
          dataPointMouseEnter: function(event) {
            event.target.style.cursor = "pointer";
            // or
            event.fromElement.style.cursor = "pointer";
        },
          dataPointSelection: (event, chartContext, config) => {

           // chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] = y
         //  chartContext.w.globals.labels[config.dataPointIndex] = x
       //  config.seriesIndex = index of bars

           var annee = chartContext.w.globals.labels[config.dataPointIndex] ;     

            switch(config.seriesIndex) {

              case 0:
             
                if( chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] > 0){
                  attest_url =  "/e-ensat/public/detail_dem_attest_"+annee+"_"+pers_id;
                  redirect_url = attest_url;
                }else{
                  redirect_url ="#";
                }

                break;
              case 1:

                if( chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] > 0){
                  conge_url =  "/e-ensat/public/detail_dem_conge_"+annee+"_"+pers_id;
                  redirect_url = conge_url;
                }else{
                  redirect_url ="#";
                }
           
                break;
              case 2:
              

                if( chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] > 0){
                auto_url =  "/e-ensat/public/detail_dem_auto_"+annee+"_"+pers_id;
                redirect_url = auto_url;
                }else{
                  redirect_url ="#";
                }
           

                  break;
              case 3:

              
                if( chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] > 0){
                  om_url =  "/e-ensat/public/detail_dem_om_"+annee+"_"+pers_id;
                  redirect_url = om_url;
                  }else{
                    redirect_url ="#";
                  }
            

                  break;
              default:null
          
            }
        
         
if(redirect_url!='#'){
  window.location = redirect_url;
}
       
       
          }
        }

    },
    markers: {
        size: 0,
        colors: ["#3361ff"],
        strokeColors: "#fff",
        strokeWidth: 2,
        hover: {
            size: 7
        }
    },
    grid: {
      show: true,
       borderColor: '#eee',
       strokeDashArray: 4,
    },
    plotOptions: {
        bar: {
            horizontal: !1,
            columnWidth: "50%",
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
    stroke: {
        colors: ["transparent"],
        show: !0,
        width: 5,
        curve: "smooth"
    },
    fill: {
       // colors: ['#ff0000', '#ff0080','#fe0080','#fe0080'],
   /*      type: 'gradient',
        gradient: {
          shade: 'light',
          type: "vertical",
          shadeIntensity: 0.5,
          gradientToColors: ["#ff0000", "#ff0080",'#fe0080','#fe0080'],
          inverseColors: true,
          opacityFrom: 1,
          opacityTo: 1,
          //stops: [0, 50, 100],
          //colorStops: []
        } */
      },

      legend: {
        show: true,
        showForSingleSeries: false,
        showForNullSeries: true,
        showForZeroSeries: true,
        position: 'bottom',
        horizontalAlign: 'center', 
        floating: false,
        fontSize: '14px',
        fontFamily: 'Helvetica, Arial',
        fontWeight: 400,
        formatter: undefined,
        inverseOrder: false,
        width: undefined,
        height: undefined,
        tooltipHoverFormatter: undefined,
        customLegendItems: [],
        offsetX: 0,
        offsetY: 3,
        labels: {
            colors: undefined,
            useSeriesColors: false
        },
        markers: {
            width: 12,
            height: 12,
            strokeWidth: 0,
            strokeColor: '#fff',
            fillColors: undefined,
            radius: 12,
            customHTML: undefined,
            onClick: undefined,
            offsetX: 0,
            offsetY: 0
        },
        itemMargin: {
            horizontal: 5,
            vertical: 0
        },
        onItemClick: {
            toggleDataSeries: true
        },
        onItemHover: {
            highlightDataSeries: true
        },
    },
    
    

      
    colors: ["#c94d58", "#83df60","#2b70b8",'#ed9121'],
    xaxis: {
        categories: annee_passee //["2010", "2011", "2012", "2013", "2014", "2015", "2016","2017"]
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


  var options_prof = {

    series: [{
        name: "Attestations",
        data: count_attest
    } ,{
       name: "Autorisations Heures-Sup",
       data: count_fh
    },{
       name: "Ordre de Missions",
       data: count_om
  } ]
  
  

  ,
    chart: {
        foreColor: '#9a9797',
        type: "bar",
        //width: 130,
        height: 280,
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
            color: "#3361ff"
        },
        sparkline: {
            enabled: !1
        },
        
        events: {
          dataPointMouseEnter: function(event) {
            event.target.style.cursor = "pointer";
            // or
            event.fromElement.style.cursor = "pointer";
        },
          dataPointSelection: (event, chartContext, config) => {

           // chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] = y
         //  chartContext.w.globals.labels[config.dataPointIndex] = x
       //  config.seriesIndex = index of bars

           var annee = chartContext.w.globals.labels[config.dataPointIndex] ;     

            switch(config.seriesIndex) {

              case 0:
             
                if( chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] > 0){
                  attest_url =  "/e-ensat/public/detail_dem_attest_"+annee+"_"+pers_id;
                  redirect_url = attest_url;
                }else{
                  redirect_url ="#";
                }

                break;
             
              case 1:
              

                if( chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] > 0){
                fh_url =  "/e-ensat/public/detail_dem_fh_"+annee+"_"+pers_id;
                redirect_url = fh_url;
                }else{
                  redirect_url ="#";
                }
           

                  break;
              case 2:

              
                if( chartContext.w.config.series[config.seriesIndex].data[config.dataPointIndex] > 0){
                  om_url =  "/e-ensat/public/detail_dem_om_"+annee+"_"+pers_id;
                  redirect_url = om_url;
                  }else{
                    redirect_url ="#";
                  }
            

                  break;
              default:null
          
            }
        
         
if(redirect_url!='#'){
  window.location = redirect_url;
}
       
       
          }
        }

    },
    markers: {
        size: 0,
        colors: ["#3361ff"],
        strokeColors: "#fff",
        strokeWidth: 2,
        hover: {
            size: 7
        }
    },
    grid: {
      show: true,
       borderColor: '#eee',
       strokeDashArray: 4,
    },
    plotOptions: {
        bar: {
            horizontal: !1,
            columnWidth: "50%",
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
    stroke: {
        colors: ["transparent"],
        show: !0,
        width: 5,
        curve: "smooth"
    },
    fill: {
       // colors: ['#ff0000', '#ff0080','#fe0080','#fe0080'],
   /*      type: 'gradient',
        gradient: {
          shade: 'light',
          type: "vertical",
          shadeIntensity: 0.5,
          gradientToColors: ["#ff0000", "#ff0080",'#fe0080','#fe0080'],
          inverseColors: true,
          opacityFrom: 1,
          opacityTo: 1,
          //stops: [0, 50, 100],
          //colorStops: []
        } */
      },

      legend: {
        show: true,
        showForSingleSeries: false,
        showForNullSeries: true,
        showForZeroSeries: true,
        position: 'bottom',
        horizontalAlign: 'center', 
        floating: false,
        fontSize: '14px',
        fontFamily: 'Helvetica, Arial',
        fontWeight: 400,
        formatter: undefined,
        inverseOrder: false,
        width: undefined,
        height: undefined,
        tooltipHoverFormatter: undefined,
        customLegendItems: [],
        offsetX: 0,
        offsetY: 3,
        labels: {
            colors: undefined,
            useSeriesColors: false
        },
        markers: {
            width: 12,
            height: 12,
            strokeWidth: 0,
            strokeColor: '#fff',
            fillColors: undefined,
            radius: 12,
            customHTML: undefined,
            onClick: undefined,
            offsetX: 0,
            offsetY: 0
        },
        itemMargin: {
            horizontal: 5,
            vertical: 0
        },
        onItemClick: {
            toggleDataSeries: true
        },
        onItemHover: {
            highlightDataSeries: true
        },
    },
    
    

      
    colors: ["#c94d58","#ffccff",'#ed9121'],
    xaxis: {
        categories: annee_passee //["2010", "2011", "2012", "2013", "2014", "2015", "2016","2017"]
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

  $('#spinner_stats_perso').hide();
  if(pers_type == 2){
    var chart = new ApexCharts(document.querySelector("#chart4"), options_adm);
  }else{
    var chart = new ApexCharts(document.querySelector("#chart4"), options_prof);
  }
 

  chart.render();


},
error:function(){
}
});










});