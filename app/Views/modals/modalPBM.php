<div class="modal" id="modal_pbm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
               <h4></h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
               <div class="container">
                   <br>
                   <div class="row">
                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">Vendas por Nome da Van</div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="myPieChartVan"></canvas>
                               </div>
                           </div>
                       </div>
                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">Vendas por Programa</div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="myPieChartProgram"></canvas>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-xl-12 col-lg-7">
                       <div class="card shadow mb-0">
                           <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                               <h6 class="m-0 font-weight-bold text-primary" id="margin_pbm_title">Performance Mensal do PBM (Todos)</h6>
                               <div class="dropdown no-arrow">
                                   <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                   </a>
                                   <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                       aria-labelledby="dropdownMenuLink">
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('EMS SAUDE');">EMS SAUDE</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('VIVER MAIS');">VIVER MAIS</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('SAUDE FACIL');">SAUDE FACIL</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('CUIDADOS PELA VIDA');">CUIDADOS PELA VIDA</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('VIVA BEM ');">VIVA BEM</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('NOVO DIA');">NOVO DIA</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('VALE MAIS SAUDE');">VALE MAIS SAUDE</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('ACESSAR');">ACESSAR</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('SAUDE EM EVOLUCAO');">SAUDE EM EVOLUCAO</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('NOVODIA');">NOVODIA</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('BAYER PARA VOCE');">BAYER PARA VOCE</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('FAZ BEM');">FAZ BEM</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('LONGEVIDADE');">LONGEVIDADE</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('SEMPRE CUIDANDO');">SEMPRE CUIDANDO</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('MAIS PFIZER');">MAIS PFIZER</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('RECEITA DE VIDA');">RECEITA DE VIDA</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('SOU MAIS VIDA');">SOU MAIS VIDA</a>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('SE CUIDA');">SE CUIDA</a>
                                       <div class="dropdown-divider"></div>
                                       <a class="dropdown-item" style="cursor: pointer;" onclick="chartMargin('Todos');">Todos</a>
                                   </div>
                               </div>
                           </div>
                           <div class="card-body">
                               <div class="chart-area">
                                   <canvas id="myAreaChartPBM" height="400"></canvas>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
        </div>
     </div>
 </div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    function chartMargin(program) {
        $.ajax({
            type: "GET",
            url: "pbm/analysis",
            data: { program: program },
            success: function (data) {
                obj = JSON.parse(data);
                if(typeof areaChartPBM !== 'undefined') areaChartPBM.destroy();
                areaChartPBM = new Chart(document.getElementById("myAreaChartPBM"), {
                  type: 'line',
                  data: {
                    labels: obj.labels_line_chart,
                    datasets: [{
                      label: "Margem",
                      lineTension: 0.3,
                      backgroundColor: "rgba(78, 115, 223, 0.05)",
                      borderColor: "rgba(78, 115, 223, 1)",
                      pointRadius: 3,
                      pointBackgroundColor: "rgba(78, 115, 223, 1)",
                      pointBorderColor: "rgba(78, 115, 223, 1)",
                      pointHoverRadius: 3,
                      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                      pointHitRadius: 10,
                      pointBorderWidth: 2,
                      data: obj.data_margin_line_chart,
                    },
                    {
                      label: "Faturamento",
                      lineTension: 0.3,
                      backgroundColor: "rgba(78, 115, 223, 0.05)",
                      borderColor: "#1cc88a",
                      pointRadius: 3,
                      pointBackgroundColor: "#23580e",
                      pointBorderColor: "#23580e",
                      pointHoverRadius: 3,
                      pointHoverBackgroundColor: "#1cc88a",
                      pointHoverBorderColor: "#1cc88a",
                      pointHitRadius: 10,
                      pointBorderWidth: 2,
                      data: obj.data_fat_line_chart,
                    }],
                  },
                  options: {
                    maintainAspectRatio: false,
                    layout: {
                      padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                      }
                    },
                    scales: {
                      xAxes: [{
                        time: {
                          unit: 'date'
                        },
                        gridLines: {
                          display: false,
                          drawBorder: false
                        },
                        ticks: {
                          maxTicksLimit: 7
                        }
                      }],
                      yAxes: [{
                        ticks: {
                          maxTicksLimit: 5,
                          padding: 10,
                          // Include a dollar sign in the ticks
                          callback: function(value, index, values) {
                            return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                          }
                        },
                        gridLines: {
                          color: "rgb(234, 236, 244)",
                          zeroLineColor: "rgb(234, 236, 244)",
                          drawBorder: false,
                          borderDash: [2],
                          zeroLineBorderDash: [2]
                        }
                      }],
                    },
                    legend: {
                      display: false
                    },
                    tooltips: {
                      backgroundColor: "rgb(255,255,255)",
                      bodyFontColor: "#858796",
                      titleMarginBottom: 10,
                      titleFontColor: '#6e707e',
                      titleFontSize: 14,
                      borderColor: '#dddfeb',
                      borderWidth: 1,
                      xPadding: 15,
                      yPadding: 15,
                      displayColors: false,
                      intersect: false,
                      mode: 'index',
                      caretPadding: 10,
                      callbacks: {
                        label: function(tooltipItem, chart) {
                          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                          var comp = (datasetLabel === "Margem") ? (chart.datasets[0].data[tooltipItem.index]).toFixed(2).replace(".", ",") + "%" : parseFloat(tooltipItem.yLabel).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                          return datasetLabel + ': ' + comp;
                        }
                      }
                    }
                  }
                });
            },
        });
    }

    function populatePBMAnalysis() {
        $('#modal_pbm .modal-header > h4').text("Performance dos produtos de PBM");
        chartMargin('Todos');
        $.ajax({
            type: "GET",
            url: "pbm/getVanAndPrograms",
            success: function (data) {
                obj = JSON.parse(data);
                if(typeof pieChartVan !== 'undefined') pieChartVan.destroy();
                pieChartVan = new Chart(document.getElementById("myPieChartVan"), {
                    type: 'pie',
                    data: {
                      labels: obj.labels_pie_chart_van,
                      datasets: [{
                          backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#f8f9fc','#5a5c69'],
                          borderWidth: 0,
                          data: obj.data_pie_chart_van
                        }
                      ]
                    },
                    options: {
                      cutoutPercentage: 85,
                      legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
                    }
                });

                if(typeof pieChartProgram !== 'undefined') pieChartProgram.destroy();
                pieChartProgram = new Chart(document.getElementById("myPieChartProgram"), {
                    type: 'pie',
                    data: {
                      labels: obj.labels_pie_chart_program,
                      datasets: [{
                          backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#f8f9fc','#5a5c69', '#582775', '#e6d7ff', '#533012', '#ab6086', '	#650f0f', '#8677e5', '#0cf054', '#00b8ff', '#c1d7f5', '#b28753'],
                          borderWidth: 0,
                          data: obj.data_pie_chart_program
                        }
                      ]
                    },
                    options: {
                      cutoutPercentage: 85,
                      legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
                    }
                });
            },
        });
    }
</script>
