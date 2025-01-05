$(function() {
	"use strict";









//////////////////////////////////////// Chart6 (donut)

var colors_palette1 = ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C","#AA5C5D"];
var libelle_dep = [];
var personnel_dep= [];

$.ajax({
    type: "GET",
    dataType: "json",
    url: 'getPremierchoixAffecter',
    success: function(data){
      $.each(data, function (index, value) { 

        personnel_dep.push( parseInt(value)) ;
        libelle_dep.push(index);
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

  var chart = new ApexCharts(document.querySelector("#chart6"), options);
  chart.render();

    },
    error:function(){
    }
  });

//////////////////////// END CHART 6


//////////////////////////////////////// Chart7 (donut)

var colors_palette4 = ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C","#AA5C5D"];
var libelle_service = [];
var personnel_service= [];

$.ajax({
    type: "GET",
    dataType: "json",
    url: 'getCapaciteFiliere',
    success: function(data){
      $.each(data, function (index, value) { 
        
        personnel_service.push( parseInt(value)) ;
        libelle_service.push(index);
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

        var chart = new ApexCharts(document.querySelector("#chart7"), options);
        chart.render();

    },
    error:function(){
    }
  });


//////////////////////// END CHART 7
var nb_etudiant_50_1 = [];
var nb_etudiant_50_2 = [];
var nb_etudiant_50_3 = [];
var nb_etudiant_50_4 = [];
var nb_etudiant_50_5 = [];
var nb_etudiant_50_6 = [];
var filiere= [];
$.ajax({
    type: "GET",
    dataType: "json",
    url: 'getchoixFiliere_50',
    success: function(data){
      $.each(data, function (index, value) { 
        nb_etudiant_50_1.push(value[1]);
        nb_etudiant_50_2.push(value[2]);
        nb_etudiant_50_3.push(value[3]);
        nb_etudiant_50_4.push(value[4]);
        nb_etudiant_50_5.push(value[5]);
        nb_etudiant_50_6.push(value[6]);
        filiere.push(index);
      });

	// chart 10
        var options = {
            series: [{
                name: '1er choix',
                data: nb_etudiant_50_1,
            }, {
                name: '2ème choix',
                data: nb_etudiant_50_2,
            }, {
                name: '3ème choix',
                data: nb_etudiant_50_3,
            }, {
                name: '4ème choix',
                data: nb_etudiant_50_4,
            }, {
                name: '5ème choix',
                data: nb_etudiant_50_5,
            }, {
                name: '6ème choix',
                data: nb_etudiant_50_6,
            }],
            chart: {
                foreColor: '#9ba7b2',
                height: 350,
                type: 'radar',
                dropShadow: {
                    enabled: true,
                    blur: 1,
                    left: 1,
                    top: 1
                }
            },
            colors: ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C","#aa6a5d"],
            title: {
                text: 'Choix/filière(seuls les 50 premiers)'
            },
            stroke: {
                width: 2
            },
            fill: {
                opacity: 0.1
            },
            markers: {
                size: 0
            },
            xaxis: {
                categories: filiere
            }
	    };
        var chart = new ApexCharts(document.querySelector("#chart5"), options);
	    chart.render();
    },
    error:function(){
    }
    });


    var nb_etudiant1 = [];
    var nb_etudiant2 = [];
    var nb_etudiant3 = [];
    var nb_etudiant4 = [];
    var nb_etudiant5 = [];
    var nb_etudiant6 = [];
    var filiere= [];
    $.ajax({
        type: "GET",
        dataType: "json",
        url: 'getchoixFiliere',
        success: function(data){
          $.each(data, function (index, value) { 
            nb_etudiant1.push(value[1]);
            nb_etudiant2.push(value[2]);
            nb_etudiant3.push(value[3]);
            nb_etudiant4.push(value[4]);
            nb_etudiant5.push(value[5]);
            nb_etudiant6.push(value[6]);
            filiere.push(index);
          });
    
        // chart 10
            var options = {
                series: [{
                    name: '1er choix',
                    data: nb_etudiant1,
                }, {
                    name: '2ème choix',
                    data: nb_etudiant2,
                }, {
                    name: '3ème choix',
                    data: nb_etudiant3,
                }, {
                    name: '4ème choix',
                    data: nb_etudiant4,
                }, {
                    name: '5ème choix',
                    data: nb_etudiant5,
                }, {
                    name: '6ème choix',
                    data: nb_etudiant6,
                }],
                chart: {
                    foreColor: '#9ba7b2',
                    height: 350,
                    type: 'radar',
                    dropShadow: {
                        enabled: true,
                        blur: 1,
                        left: 1,
                        top: 1
                    }
                },
                colors: ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C","#AA5C5d"],
                title: {
                    text: 'Choix/filière'
                },
                stroke: {
                    width: 2
                },
                fill: {
                    opacity: 0.1
                },
                markers: {
                    size: 0
                },
                xaxis: {
                    categories: filiere
                }
            };
            var chart1 = new ApexCharts(document.querySelector("#chart1"), options);
            chart1.render();
        },
        error:function(){
        }
        });
    


});
