(function ($) {
  'use strict';

   $.ajax({
      url: '/chart/1',
  success: function(data){
    console.log(data);

    var labels = [];
    var accepted = [];
    var orders = [];
    var axisX = [];
    var axisStep = [];

    for( var shop in data ){
      labels[shop] = [];
      accepted[shop] = [];
      orders[shop] = [];
      console.log('shop=' + shop);

      for( var item in data[shop] ){
        console.log(item);
         labels[shop].push(item);
         accepted[shop].push(data[shop][item].approved);
         orders[shop].push(data[shop][item].all);
      }
      axisX[shop] = Math.ceil(Math.max.apply(null, orders[shop]) / 10 ) * 10;
      axisStep[shop] = Math.ceil( axisX[shop] / 5);
    }
console.log(labels);
console.log(orders);
console.log(axisX);
console.log(axisStep);

    // var labels = ["Jan 1", "Jan 7", "Jan 14", "Jan 21", "Jan 28", "Feb 4", "Feb 11", "Feb 18"];          

    // var maxAxisX = Math.ceil(Math.max.apply(null, orders) / 10 ) * 10;
    // console.log(maxAxisX);
    // var axisStep = Math.ceil( maxAxisX / 5);
  var shop = 0;

  $(function () {
    var lineStatsOptions = {
      scales: {
        yAxes: [{
          display: false
        }],
        xAxes: [{
          display: false
        }]
      },
      legend: {
        display: false
      },
      elements: {
        point: {
          radius: 0
        },
        line: {
          tension: 0
        }
      },
      stepsize: axisStep[shop]
    }
    if ($('#sales-statistics-overview').length) {
      var salesChartCanvas = $("#sales-statistics-overview").get(0).getContext("2d");
      var gradientStrokeFill_1 = salesChartCanvas.createLinearGradient(0, 0, 0, 450);
      gradientStrokeFill_1.addColorStop(1, 'rgba(255,255,255, 0.0)');
      gradientStrokeFill_1.addColorStop(0, 'rgba(102,78,235, 0.2)');
      var gradientStrokeFill_2 = salesChartCanvas.createLinearGradient(0, 0, 0, 400);
      gradientStrokeFill_2.addColorStop(1, 'rgba(255, 255, 255, 0.01)');
      gradientStrokeFill_2.addColorStop(0, '#14c671');

      // var labels = ["Jan 1", "Jan 7", "Jan 14", "Jan 21", "Jan 28", "Feb 4", "Feb 11", "Feb 18"];

      console.log('shop=' + shop);
      // var data_1_1 = [60, 75, 65, 130, 130, 145, 110, 145, 155, 149, 370];
      // var data_1_2 = [0, 25, 20, 40, 70, 52, 49, 90, 70, 94, 110, 135];
      var data_1_1 = orders[shop];
      var data_1_2 = accepted[shop];

      // var data_2_1 = [130, 145, 155, 60, 75, 65, 130, 110, 145, 149, 170];
      // var data_2_2 = [0, 70, 52, 90, 25, 20, 40, 70, 49, 94, 110, 135];

      // var data_3_1 = [130, 75, 65, 130, 110, 145, 155, 60, 145, 149, 370];
      // var data_3_2 = [0, 70, 52, 94, 110, 135, 90, 25, 20, 40, 70, 49];

      // var data_4_1 = [130, 145, 65, 130, 75, 145, 149, 170, 110, 155, 60];
      // var data_4_2 = [0, 70, 90, 25, 40, 20, 94, 110, 135, 70, 49, 52];
      var areaData = {
        // labels: ["Jan 1", "Jan 7", "Jan 14", "Jan 21", "Jan 28", "Feb 4", "Feb 11", "Feb 18"],
        labels: labels[shop],
        datasets: [{
          label: 'Показы',
          data: data_1_1,
          borderColor: infoColor,
          backgroundColor: gradientStrokeFill_1,
          borderWidth: 2
        }, {
          label: 'Заказы',
          data: data_1_2,
          borderColor: successColor,
          backgroundColor: gradientStrokeFill_2,
          borderWidth: 2
        }]
      };
      var areaOptions = {
        responsive: true,
        animation: {
          animateScale: true,
          animateRotate: true
        },
        elements: {
          point: {
            radius: 3,
            backgroundColor: "#fff"
          },
          line: {
            tension: 0
          }
        },
        layout: {
          padding: {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0
          }
        },
        legend: false,
        legendCallback: function (chart) {
          var text = [];
          text.push('<div class="chartjs-legend"><ul>');
          for (var i = 0; i < chart.data.datasets.length; i++) {
            console.log(chart.data.datasets[i]); // see what's inside the obj.
            text.push('<li>');
            text.push('<span style="background-color:' + chart.data.datasets[i].borderColor + '">' + '</span>');
            text.push(chart.data.datasets[i].label);
            text.push('</li>');
          }
          text.push('</ul></div>');
          return text.join("");
        },
        scales: {
          xAxes: [{
            display: false,
            ticks: {
              display: false,
              beginAtZero: false
            },
            gridLines: {
              drawBorder: false
            }
          }],
          yAxes: [{
            ticks: {
              max: axisX[shop],
              min: 0,
              stepSize: axisStep[shop],
              fontColor: "#858585",
              beginAtZero: false
            },
            gridLines: {
              color: '#e2e6ec',
              display: true,
              drawBorder: false
            }
          }]
        }
      }
      var salesChart = new Chart(salesChartCanvas, {
        type: 'line',
        data: areaData,
        options: areaOptions
      });
      document.getElementById('sales-statistics-legend').innerHTML = salesChart.generateLegend();

      $("#sales-statistics_switch_1").click(function () {
        var data = salesChart.data;
        shop = 0;
        data.datasets[0].data = orders[0];
        data.datasets[1].data = accepted[0];
        data.labels=labels[0];
        salesChart.update();
console.log('click');
        console.log(salesChart);
      });
      
      $("#sales-statistics_switch_2").click(function () {
        var data = salesChart.data;
        shop=1;
        data.datasets[0].data = orders[1];
        data.datasets[1].data = accepted[1];
        data.labels=labels[1];
        salesChart.update();
      });
      /*
      $("#sales-statistics_switch_3").click(function () {
        var data = salesChart.data;
        data.datasets[0].data = data_3_1;
        data.datasets[1].data = data_3_2;
        salesChart.update();
      });
      $("#sales-statistics_switch_4").click(function () {
        var data = salesChart.data;
        data.datasets[0].data = data_4_1;
        data.datasets[1].data = data_4_2;
        salesChart.update();
      }); */
    }
    if ($("#net-profit").length) {
      var marksCanvas = document.getElementById("net-profit");
      var marksData = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
        datasets: [{
          label: "Sales XX",
          backgroundColor: 'rgba(88, 208, 222,0.8)',
          borderColor: 'rgba(88, 208, 222,0.8)',
          borderWidth: 0,
          fill: true,
          radius: 0,
          pointRadius: 0,
          pointBorderWidth: 0,
          pointBackgroundColor: 'rgba(88, 208, 222,0.8)',
          pointHoverRadius: 10,
          pointHitRadius: 5,
          data: [54, 45, 60, 70, 54, 75, 60, 54]
        }, {
          label: "Orders ZZ",
          backgroundColor: 'rgba(150, 77, 247,1)',
          borderColor: 'rgba(150, 77, 247,1)',
          borderWidth: 0,
          fill: true,
          radius: 0,
          pointRadius: 0,
          pointBorderWidth: 0,
          pointBackgroundColor: 'rgba(150, 77, 247,1)',
          pointHoverRadius: 10,
          pointHitRadius: 5,
          data: [65, 75, 70, 80, 60, 80, 36, 60]
        }]
      };

      var chartOptions = {
        scale: {
          ticks: {
            beginAtZero: true,
            min: 0,
            max: 100,
            stepSize: 20,
            display: false,
          },
          pointLabels: {
            fontSize: 14
          },
          angleLines: {
            color: '#e9ebf1'
          },
          gridLines: {
            color: "#e9ebf1"
          }
        },
        legend: false,
        legendCallback: function (chart) {
          var text = [];
          text.push('<div class="chartjs-legend"><ul>');
          for (var i = 0; i < chart.data.datasets.length; i++) {
            console.log(chart.data.datasets[i]); // see what's inside the obj.
            text.push('<li>');
            text.push('<span style="background-color:' + chart.data.datasets[i].backgroundColor + '">' + '</span>');
            text.push(chart.data.datasets[i].label);
            text.push('</li>');
          }
          text.push('</ul></div>');
          return text.join("");
        },
      };

      var radarChart = new Chart(marksCanvas, {
        type: 'radar',
        data: marksData,
        options: chartOptions
      });
      document.getElementById('net-profit-legend').innerHTML = radarChart.generateLegend();
    }

    if ($('#total-transaction').length) {
      var ctx = document.getElementById('total-transaction').getContext('2d');
      var gradientStrokeFill_1 = ctx.createLinearGradient(0, 100, 200, 0);
      gradientStrokeFill_1.addColorStop(0, '#fa5539');
      gradientStrokeFill_1.addColorStop(1, '#fa3252');
      var areaData = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [{
          label: 'Sessions',
          data: [320, 280, 300, 280, 300, 270, 350],
          backgroundColor: gradientStrokeFill_1,
          borderColor: '#fa394e',
          borderWidth: 0,
          pointBackgroundColor: "#fa394e",
          pointRadius: 7,
          pointBorderWidth: 3,
          pointBorderColor: '#fff',
          pointHoverRadius: 7,
          pointHoverBackgroundColor: "#fa394e",
          pointHoverBorderColor: "#fa394e",
          pointHoverBorderWidth: 2,
          pointHitRadius: 7,
        }]
      };
      var areaOptions = {
        responsive: true,
        animation: {
          animateScale: true,
          animateRotate: true
        },
        elements: {
          point: {
            radius: 0
          }
        },
        layout: {
          padding: {
            left: -10,
            right: 0,
            top: 0,
            bottom: -10
          }
        },
        legend: false,
        scales: {
          xAxes: [{
            gridLines: {
              display: false
            },
            ticks: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            },
            ticks: {
              display: false
            }
          }]
        }
      }
      var revenueChart = new Chart(ctx, {
        type: 'line',
        data: areaData,
        options: areaOptions
      });
    }
    if ($("#market-overview-chart").length) {
      var MarketingChartCanvas = $("#market-overview-chart").get(0).getContext("2d");
      var Marketing_data_1_1 = [145, 238, 148, 293, 242, 235, 256, 334];
      var Marketing_data_1_2 = [330, 380, 230, 400, 309, 430, 340, 310];
      var Marketing_data_1_3 = [375, 440, 284, 450, 386, 480, 400, 365];
      var Marketing_data_1_4 = [425, 480, 324, 490, 426, 520, 440, 405];

      var Marketing_data_2_1 = [125, 138, 108, 193, 102, 200, 290, 204];
      var Marketing_data_2_2 = [330, 380, 230, 400, 309, 430, 340, 310];
      var Marketing_data_2_3 = [375, 440, 284, 450, 386, 480, 400, 365];
      var Marketing_data_2_4 = [425, 480, 324, 490, 426, 520, 440, 405];

      // var Marketing_data_1_1 = [145, 238, 148, 293, 242, 235, 256, 334];
      // var Marketing_data_1_2 = [330, 380, 230, 400, 309, 430, 340, 310];
      // var Marketing_data_1_3 = [375, 440, 284, 450, 386, 480, 400, 365];
      // var Marketing_data_1_4 = [425, 480, 324, 490, 426, 520, 440, 405];

      var MarketingChart = new Chart(MarketingChartCanvas, {
        type: 'bar',
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
          datasets: [{
              label: 'OVERDUE',
              data: Marketing_data_1_1,
              backgroundColor: '#826af9',
              borderColor: '#826af9',
              borderWidth: 0
            }, {
              label: 'SNOOZED',
              data: Marketing_data_1_2,
              backgroundColor: '#9e86ff',
              borderColor: '#9e86ff',
              borderWidth: 0
            },
            {
              label: 'COMPLETED',
              data: Marketing_data_1_3,
              backgroundColor: '#d0aeff',
              borderColor: '#d0aeff',
              borderWidth: 0
            },
            {
              label: 'OVERDUE',
              data: Marketing_data_1_4,
              backgroundColor: '#f7d2ff',
              borderColor: '#f7d2ff',
              borderWidth: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          layout: {
            padding: {
              left: 0,
              right: 0,
              top: 20,
              bottom: 0
            }
          },
          scales: {
            yAxes: [{
              ticks: {
                max: 400,
                display: true,
                beginAtZero: true,
                fontColor: "#212529",
                stepSize: 100
              },
              gridLines: {
                display: false,
              }
            }],
            xAxes: [{
              stacked: true,
              ticks: {
                beginAtZero: true,
                fontColor: "#212529"
              },
              gridLines: {
                color: "#e9ebf1",
                display: true
              },
              barPercentage: 0.2
            }]
          },
          legend: {
            display: false
          },
          elements: {
            point: {
              radius: 0
            }
          }
        }
      });
      $("#market-overview_1").click(function () {
        var data = MarketingChart.data;
        data.datasets[0].data = Marketing_data_1_1;
        data.datasets[1].data = Marketing_data_1_2;
        data.datasets[2].data = Marketing_data_1_2;
        data.datasets[3].data = Marketing_data_1_2;
        MarketingChart.update();
      });
      $("#market-overview_2").click(function () {
        var data = MarketingChart.data;
        data.datasets[0].data = Marketing_data_2_1;
        data.datasets[1].data = Marketing_data_2_2;
        data.datasets[2].data = Marketing_data_2_2;
        data.datasets[3].data = Marketing_data_2_2;
        MarketingChart.update();
      });
      $("#market-overview_3").click(function () {
        var data = MarketingChart.data;
        data.datasets[0].data = Marketing_data_3_1;
        data.datasets[1].data = Marketing_data_3_2;
        data.datasets[2].data = Marketing_data_3_2;
        data.datasets[3].data = Marketing_data_3_2;
        MarketingChart.update();
      });
    }
    if ($("#realtime-statistics").length) {
      var realtimeChartCanvas = $("#realtime-statistics").get(0).getContext("2d");
      var realtimeChart = new Chart(realtimeChartCanvas, {
        type: 'bar',
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
              label: 'Profit',
              data: [330, 380, 230, 400, 309, 530, 340],
              backgroundColor: "#0f5bff",
              borderColor: '#0f5bff',
              borderWidth: 0
            },
            {
              label: 'Target',
              data: [600, 600, 600, 600, 600, 600, 600],
              backgroundColor: '#e5e9f2',
              borderColor: '#e5e9f2',
              borderWidth: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          layout: {
            padding: {
              left: 0,
              right: 25,
              top: 0,
              bottom: 0
            }
          },
          scales: {
            yAxes: [{
              display: false,
              gridLines: {
                display: false
              }
            }],
            xAxes: [{
              stacked: true,
              ticks: {
                display: false,
                beginAtZero: true,
                fontColor: "#354168"
              },
              gridLines: {
                color: "rgba(0, 0, 0, 0)",
                display: false
              },
              barPercentage: 0.5,
            }]
          },
          legend: {
            display: false
          },
          elements: {
            point: {
              radius: 0
            }
          }
        }
      });
    }

  });


    }
  });

})(jQuery);