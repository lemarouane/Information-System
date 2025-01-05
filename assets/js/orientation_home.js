$(function() {
	"use strict";
//////////////////////////////////////// Chart7 (donut)
    $('#platformActive').on('change', function() {
        $.ajax({ 
            type: "POST", 
            data:{ 'doc' : $('#platformActive').prop('checked')},
            url: 'platformeIsActive',
            error:function(){
                alert('Merci  de selectionner un utilisateur');
            }
        });
    })
    var colors_palette4 = ["#00c6fb", "#ff6a00", "#98ec2d","#C7B446","#CD5C5C"];
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
                fontSize: '14px',
                
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

            var chart = new ApexCharts(document.querySelector("#chart1"), options);
            chart.render();

        },
        error:function(){
        }
    });
    })

