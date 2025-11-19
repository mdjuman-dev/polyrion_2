//[Dashboard Javascript]

//Project:  Crypto Admin - Responsive Admin Template
//Primary use:   Used only for the  widget inline charts


  var options = {
          series: [{
          name: 'Cash Flow',
          data: [-30, 36, 24, 48, -24, -36, -12, 12, 24
          ]
        }],
          chart: {
          type: 'bar',
          height: 298,
          toolbar: {
            show: false,
          },
        },
        plotOptions: {
          bar: {
            colors: {
              ranges: [{
                from: -100,
                to: -46,
                color: '#F15B46'
              }, {
                from: -45,
                to: 0,
                color: '#F15B46'
              }]
            },
            columnWidth: '80%',
          }
        },
        dataLabels: {
          enabled: false,
        },
        yaxis: {
          title: {
            text: '',
          },
          labels: {
            formatter: function (y) {
              return y.toFixed(0) + " K";
            }
          }
        },
        xaxis: {
          type: 'datetime',
          categories: [
            '2011-01-01', '2011-02-01', '2011-03-01', '2011-04-01', '2011-05-01', '2011-06-01',
            '2011-07-01', '2011-08-01', '2011-09-01',
          ],
          labels: {
            rotate: -90
          }
        }
        };

        var chart = new ApexCharts(document.querySelector("#stock-chart"), options);
        chart.render();











$(function () {

  'use strict';


 // ----------Shifts Overview-----//
    var option = {
        labels: ["BTC", "ETH", "ADA", "Others"],
        series: [25, 10, 10, 65],
        chart: {
            type: "donut",
            height: 265,
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
        },
        stroke: {
            width: 5,
        },
        plotOptions: {
            pie: {
                expandOnClick: false,
                donut: {
                    size: "75%",
                    labels: {
                        show: true,
                        name: {
                            offsetY: -10,
                            color: '#00000',
                        },
                        total: {
                            show: false,
                            fontSize: "20px",
                            fontWeight: 600,
                            color:"#000000",
                            formatter: () => "",
                            label: "All",
                        },
                    },
                },
            },
        },
        states: {
            normal: {
                filter: {
                    type: "none",
                },
            },
            hover: {
                filter: {
                    type: "none",
                },
            },
            active: {
                allowMultipleDataPointsSelection: false,
                filter: {
                    type: "none",
                },
            },
        },
        colors: ["#ff9920", "#ff562f", "#0052cc", "#00baff", "#04a08b"],
    };

    var chart = new ApexCharts(
        document.querySelector("#balance-overview-2"),
        option
    );
    chart.render();




  $('.owl-carousel').owlCarousel({
      loop: true,
      margin: 15,
      responsiveClass: false,
      autoplay: false,
      responsive: {
        0: {
        items: 1,
        nav: false
        },
        600: {
        items: 3,
        nav: false
        },
        1000: {
        items: 4,
        nav: false,
        margin: 15
        }
      }
    });


      var options = {
          series: [{
          name: 'series1',
          data: [ 20,30,35,32,50,31,20,25,30,27,70]
          }],
          chart: {
          height: 88,
          type: 'area',
          toolbar: {
            show: false,
            },
            offsetY: 0,
        },
        colors: ["#1bc5bd"],
        fill: {
          colors: ["#1bc5bd" ],
          type: "gradient",
          gradient: {
            shade: "light",
            type: "vertical",
            shadeIntensity: 0.4,
            inverseColors: false,
            opacityFrom: 0.7,
            opacityTo: 0.1,
            stops: [0,85,90],
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
        width: [2],
          curve: 'smooth'
        },
        grid: {
          show: false,
          padding: {
            left: -10,
            top: -25,
            right: -0,
          },
        },
        markers: {
            size: 0,
        },
        xaxis: {
          type: 'datetime',
          categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
        },
        legend: {
            show: false,
        },
        tooltip: {
          x: {
            format: 'dd/MM/yy HH:mm'
          },
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
            formatter: function (val) {
              return val + "%";
            }
          },
        },
        xaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
          },
        },
        };

        var chart = new ApexCharts(document.querySelector("#chart-widget"), options);
        chart.render();



        var options = {
          series: [{
          name: 'series1',
          data: [ 20,30,35,32,25,31,20,25,30,27,10]
          }],
          chart: {
          height: 70,
          type: 'area',
          toolbar: {
            show: false,
            },
            offsetY: 0,
        },
        colors: ["#ff3f3f"],
        fill: {
          colors: ["#ff3f3f" ],
          type: "gradient",
          gradient: {
            shade: "light",
            type: "vertical",
            shadeIntensity: 0.4,
            inverseColors: false,
            opacityFrom: 0.7,
            opacityTo: 0.1,
            stops: [0,85,90],
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
        width: [2],
          curve: 'smooth'
        },
        grid: {
          show: false,
          padding: {
            left: -10,
            top: -25,
            right: -0,
          },
        },
        markers: {
            size: 0,
        },
        xaxis: {
          type: 'datetime',
          categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
        },
        legend: {
            show: false,
        },
        tooltip: {
          x: {
            format: 'dd/MM/yy HH:mm'
          },
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
            formatter: function (val) {
              return val + "%";
            }
          },
        },
        xaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
          },
        },
        };

        var chart = new ApexCharts(document.querySelector("#chart-widget2"), options);
        chart.render();





        var options = {
          series: [{
          name: 'series1',
          data: [ 10,20,40,32,10,31,20,15,10,27,50]
          }],
          chart: {
          height: 70,
          type: 'area',
          toolbar: {
            show: false,
            },
            offsetY: 0,
        },
        colors: ["#1bc5bd"],
        fill: {
          colors: ["#1bc5bd" ],
          type: "gradient",
          gradient: {
            shade: "light",
            type: "vertical",
            shadeIntensity: 0.4,
            inverseColors: false,
            opacityFrom: 0.7,
            opacityTo: 0.1,
            stops: [0,85,90],
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
        width: [2],
          curve: 'smooth'
        },
        grid: {
          show: false,
          padding: {
            left: -10,
            top: -25,
            right: -0,
          },
        },
        markers: {
            size: 0,
        },
        xaxis: {
          type: 'datetime',
          categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
        },
        legend: {
            show: false,
        },
        tooltip: {
          x: {
            format: 'dd/MM/yy HH:mm'
          },
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
            formatter: function (val) {
              return val + "%";
            }
          },
        },
        xaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
          },
        },
        };

        var chart = new ApexCharts(document.querySelector("#chart-widget3"), options);
        chart.render();





        var options = {
          series: [{
          name: 'series1',
          data: [ 20,10,35,32,40,31,20,25,30,27,50]
          }],
          chart: {
          height: 70,
          type: 'area',
          toolbar: {
            show: false,
            },
            offsetY: 0,
        },
        colors: ["#1bc5bd"],
        fill: {
          colors: ["#1bc5bd" ],
          type: "gradient",
          gradient: {
            shade: "light",
            type: "vertical",
            shadeIntensity: 0.4,
            inverseColors: false,
            opacityFrom: 0.7,
            opacityTo: 0.1,
            stops: [0,85,90],
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
        width: [2],
          curve: 'smooth'
        },
        grid: {
          show: false,
          padding: {
            left: -10,
            top: -25,
            right: -0,
          },
        },
        markers: {
            size: 0,
        },
        xaxis: {
          type: 'datetime',
          categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
        },
        legend: {
            show: false,
        },
        tooltip: {
          x: {
            format: 'dd/MM/yy HH:mm'
          },
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
            formatter: function (val) {
              return val + "%";
            }
          },
        },
        xaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
          },
        },
        };

        var chart = new ApexCharts(document.querySelector("#chart-widget5"), options);
        chart.render();



                 var options = {
          series: [{
          name: 'series1',
          data: [ 20,15,25,25,30,27,33,30,35,32,25,31,20,25,30,27,33,30,20]
          }],
          chart: {
          height: 88,
          type: 'area',
          toolbar: {
            show: false,
            },
            offsetY: 0,
        },
        colors: ["#1bc5bd"],
        fill: {
          colors: ["#1bc5bd" ],
          type: "gradient",
          gradient: {
            shade: "light",
            type: "vertical",
            shadeIntensity: 0.4,
            inverseColors: false,
            opacityFrom: 0.7,
            opacityTo: 0.1,
            stops: [0,85,90],
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
        width: [2],
          curve: 'smooth'
        },
        grid: {
          show: false,
          padding: {
            left: -10,
            top: -25,
            right: -0,
          },
        },
        markers: {
            size: 0,
        },
        xaxis: {
          type: 'datetime',
          categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
        },
        legend: {
            show: false,
        },
        tooltip: {
          x: {
            format: 'dd/MM/yy HH:mm'
          },
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
            formatter: function (val) {
              return val + "%";
            }
          },
        },
        xaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
          },
        },
        };

        var chart = new ApexCharts(document.querySelector("#chart-widget4"), options);
        chart.render();
  




    
        
    var options = {
          chart: {
            height: 325,
            type: "radialBar"
          },

          series: [77],
            colors: ['#0052cc'],
          plotOptions: {
            radialBar: {
              hollow: {
                margin: 15,
                size: "70%"
              },
              track: {
                background: '#ff9920',
              },

              dataLabels: {
                showOn: "always",
                name: {
                  offsetY: -10,
                  show: false,
                  color: "#888",
                  fontSize: "13px"
                },
                value: {
                  color: "#111",
                  fontSize: "30px",
                  show: true
                }
              }
            }
          },

          stroke: {
            lineCap: "round",
          },
          labels: ["Progress"]
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();

        


         var options = {
          series: [{
          name: 'XYZ MOTORS',
          data: dates
        }],
          chart: {
          type: 'area',
          stacked: false,
          height: 350,
          zoom: {
            type: 'x',
            enabled: true,
            autoScaleYaxis: true
          },
          toolbar: {
            autoSelected: 'zoom'
          }
        },
        dataLabels: {
          enabled: false
        },
        markers: {
          size: 0,
        },
        title: {
          text: 'Stock Price Movement',
          align: 'left'
        },
        fill: {
          type: 'gradient',
          gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.5,
            opacityTo: 0,
            stops: [0, 90, 100]
          },
        },
        yaxis: {
          labels: {
            formatter: function (val) {
              return (val / 1000000).toFixed(0);
            },
          },
          title: {
            text: 'Price'
          },
        },
        xaxis: {
          type: 'datetime',
        },
        tooltip: {
          shared: false,
          y: {
            formatter: function (val) {
              return (val / 1000000).toFixed(0)
            }
          }
        }
        };

        var chart = new ApexCharts(document.querySelector("#revenue5"), options);
        chart.render();
    
    
}); // End of use strict