$(function() {
	"use strict";
    var anneeSelect = $('#anneeSelect').find(":selected").val();
    capaciteAsEtapeNV(anneeSelect);
    capaciteAsDiplome(anneeSelect);
    capaciteAsEtape(anneeSelect);
    evolutionEfectif(anneeSelect);
    getStatScolarite(anneeSelect);
    getStatbyPays(anneeSelect);

    
    $('#anneeSelect').on('change', function() {
               
        var anneeS = $(this).val();
        
        
        capaciteAsDiplome(anneeS);
        capaciteAsEtape(anneeS);
        evolutionEfectif(anneeS);
        getStatScolarite(anneeS);
        capaciteAsEtapeNV(anneeS);
        getStatbyPays(anneeS);
        
      });
   
function getStatScolarite(annee){
    var url = $("#path-to-statScolarite").data("href");
    url = url.replace("1111",  annee );
    $.ajax({
        type: "GET",
        dataType: "json",
        url: url,
        success: function(data){
          
          $("#etudiant_fille").html(data['nbFilles']) ;
          $("#etudiant_garçon").html(data['nbGarçons']) ;
          $("#etudiant_totale").html(data['totales']) ;
          $("#etudiant_totale_nv").html(data['nvIns']) ;
         
    
    
        },
        error:function(){
        }
      });
}
//////////////////////////////////////// Chart6 (donut)

function capaciteAsDiplome(annee){
    var colors_palette1 = ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C"];
    var diplome = [];
    var nombre= [];
    var url = $("#path-to-nbAsDiplome").data("href");
    url = url.replace("1111",  annee );
    $.ajax({
        type: "GET",
        dataType: "json",
        url:  url,
        success: function(data){
            $.each(data, function (index, value) { 
                
                nombre.push( parseInt(value['NOMBRE'])) ;
                diplome.push(value['COD_DIP']);
            });

            var options = {
                series: nombre,
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
            labels: diplome,
            fill: {
                type: 'gradient',
            },
            legend: {
                formatter: function(val, opts) {
                return val + " : " + opts.w.globals.series[opts.seriesIndex]
                },
                fontSize: '10px',
                foreColor: '#373d3f'
            
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

        if (window.chart4)
            window.chart4.destroy();

        window.chart4 = new ApexCharts(document.querySelector("#chart6"), options);
        window.chart4.render();

    },
    error:function(){
    }
  });

}



function getStatbyPays(annee){

   // var $result=[] ;  
    var link_png = window.location.origin + "/e-ensat/public/build/images/flags/4x3/" ;
    var url = $("#path-to-StatbyPays").data("href");
    url = url.replace("1111",  annee );
    $.ajax({
        type: "GET",
        dataType: "json",
        url:  url,
        success: function(data){
            var totale = 0;
            $("#list_country").empty();
            $.each(data, function (index, value) { 
               // $result.push(value) ;
              
                $("#list_country").append("<li class='list-group-item'> <div class='d-flex align-items-center gap-2'> <div><img style='width:30px;height;15px;'  src='"+ link_png + value['code_iso']+".svg" + "'  ></div><div> " + value['code_pays']+ "</div><div class='ms-auto'>" + value['nb_by_pays']+ "</div></div></li>");
                totale = totale + Number(value['nb_by_pays']) ;
            });

            $("#stat_pays_totale").text(data.length);
            $("#stat_totale").text(totale);
    },
    error:function(){
    }
  });

}

//////////////////////// END CHART 6
//////////////////////////////////////// Chart7 (donut)


function capaciteAsEtape(annee){

    var colors_palette4 = ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C",'#008080',
                        '#FF6347',
                        '#FFFF00',
                        '#00FF7F',
                        '#FF0000',
                        '#4169E1',
                        '#FF00FF',
                        '#20B2AA',
                        '#9400D3',
                        '#2F4F4F',
                        '#A52A2A'];
    var etape = [];
    var nombre= [];
    var url = $("#path-to-nbAsEtape").data("href");
    url= url.replace("1111",  annee );
    $.ajax({
        type: "GET",
        dataType: "json",
        url: url,
        success: function(data){
        $.each(data, function (index, value) { 

            nombre.push( parseInt(value['NOMBRE'])) ;
            etape.push(value['COD_ETP']);
        });


        var options = {
            series: nombre,
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
            labels: etape,
            fill: {
                type: 'gradient',
            },
            legend: {
                formatter: function(val, opts) {
                return val + " : " + opts.w.globals.series[opts.seriesIndex]
                } ,
                fontSize: '9px',
                width: 120,
                
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

        if (window.chart3)
            window.chart3.destroy();

        window.chart3 = new ApexCharts(document.querySelector("#chart7"), options);
        window.chart3.render();

        },
        error:function(){
        }

    });
}

  


//////////////////////// END CHART 7

function evolutionEfectif(annee){
    var dataef = [];
    var valeur= [];
    var url = $("#path-to-evolutifEffect").data("href");
    url= url.replace("1111",  annee );
    $.ajax({
        type: "GET",
        dataType: "json",
        url: url,
        success: function(data){
        $.each(data, function (index, value) { 
            dataef.push(value['NOMBRE']);
            valeur.push(value['COD_ANU']);
        });

      var options = {
		series: [ {
			name: 'affectif',
			data: dataef
		}],
		chart: {
			foreColor: '#9ba7b2',
			height: 360,
			type: 'area',
			zoom: {
				enabled: false
			},
			toolbar: {
				show: true
			},
		},
		colors: ["#3461ff", '#0c971a'],
		title: {
			text: 'Effectifs ENSAT',
			align: 'left',
			style: {
				fontSize: "16px",
				color: '#666'
			}
		},
		dataLabels: {
			enabled: false
		},
		stroke: {
			curve: 'smooth'
		},
		xaxis: {
			type: 'datetime',
			categories: valeur,
		},
		tooltip: {
			x: {
				format: 'yyyy'
			},
		},
	};
    if (window.chart2)
            window.chart2.destroy();
    window.chart2 = new ApexCharts(document.querySelector("#chart1"), options);
	window.chart2.render();
        },
            error:function(){
        }
    });

}

   function capaciteAsEtapeNV(annee){

    var colors_palette4 =    ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C",'#008080',
            '#FF6347',
            '#FFFF00',
            '#00FF7F',
            '#FF0000',
            '#4169E1',
            '#FF00FF',
            '#20B2AA',
            '#9400D3',
            '#2F4F4F',
            '#A52A2A'];
        var etapeNV = [];
        var nombre= [];
        var url = $("#path-to-nbAsEtapeNV").data("href");
        url= url.replace("1111",  annee );
        $.ajax({
        type: "GET",
        dataType: "json",
        url: url,
        success: function(data){
            $.each(data, function (index, value) { 
                nombre.push( parseInt(value['NOMBRE'])) ;
                etapeNV.push(value['COD_ETP']);
            });

            var options = {
                series: nombre,
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
                labels: etapeNV,
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

            $("div#chart5").remove();
            $("div.chartJs").append('<div id="chart5"></div>');
            
            
            window.chart1 = new ApexCharts(document.querySelector("#chart5"), options);
            window.chart1.render();
            

            
        },
        error:function(){
        }
        });
   } 


});
