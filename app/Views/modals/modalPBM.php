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
                               <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                   <h6 class="m-0 font-weight-bold text-primary" id="van_program_pbm_title">Vendas por Van</h6>
                                   <div class="dropdown no-arrow">
                                       <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                           <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                       </a>
                                       <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                           aria-labelledby="dropdownMenuLink">
                                           <a class="dropdown-item" style="cursor: pointer;" onclick="chartOne('Van');">Van</a>
                                           <a class="dropdown-item" style="cursor: pointer;" onclick="chartOne('Programa');">Programa</a>
                                       </div>
                                   </div>
                               </div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="myPieChartOne"></canvas>
                               </div>
                           </div>
                       </div>
                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                   <h6 class="m-0 font-weight-bold text-primary" id="performance_pbm_title">Performance dos últimos 3 meses</h6>
                                   <div class="dropdown no-arrow">
                                       <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                           <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                       </a>
                                       <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                           aria-labelledby="dropdownMenuLink">
                                           <a class="dropdown-item" style="cursor: pointer;" onclick="chartTwo('antepenultimo');">Antepenúltimo mês</a>
                                           <a class="dropdown-item" style="cursor: pointer;" onclick="chartTwo('penultimo');">Penúltimo mês</a>
                                           <a class="dropdown-item" style="cursor: pointer;" onclick="chartTwo('ultimo');">Último mês</a>
                                           <div class="dropdown-divider"></div>
                                           <a class="dropdown-item" style="cursor: pointer;" onclick="chartTwo('Todos');">Últimos 3 meses</a>
                                       </div>
                                   </div>
                               </div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="myPieChartSecond"></canvas>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <!-- <div class="row">
                   <div class="col-xl-12 col-lg-7">
                       <div class="card shadow mb-0">
                           <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">Share</h6>
                           </div>
                           <div class="card-body">
                               <div class="chart-area">
                                   <canvas id="myAreaChartPBM" height="400"></canvas>
                               </div>
                           </div>
                       </div>
                   </div>
               </div> -->
            </div>
        </div>
     </div>
 </div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    function chartOne(type) {
        $.ajax({
            type: "GET",
            url: "pbm/getDataVanOrProgram",
            data: { type: type },
            success: function (data) {
                $('#van_program_pbm_title').text('Vendas por ' + type);
                obj = JSON.parse(data);
                if(typeof pieChartVanProgram !== 'undefined') pieChartVanProgram.destroy();
                if(type == 'Van') {
                    settings = {
                        type: 'pie',
                        data: {
                          labels: obj.labels,
                          datasets: [{
                              backgroundColor: ['#e74a3b', '#4e73df', '#1cc88a'],
                              borderWidth: 0,
                              data: obj.data
                            }
                          ]
                        },
                        options: {
                          cutoutPercentage: 40,
                          legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
                        }
                    };
                }
                else {
                    const arrayOfObj = obj.labels.map(function (d, i) {
                      return {
                        label: d,
                        data: obj.data[i] || 0,
                      }
                    })

                    const sortedArrayOfObj = arrayOfObj.sort(function (a, b) {
                      return b.data - a.data
                    })
                    datasets = [];
                    colors = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#f8f9fc','#5a5c69', '#582775', '#e6d7ff', '#533012', '#ab6086', '	#650f0f', '#8677e5', '#0cf054', '#00b8ff', '#c1d7f5', '#b28753'];
                    Object.keys(sortedArrayOfObj).forEach((key, index) => {
                        datasets.push({
                           label: sortedArrayOfObj[key].label,
                           backgroundColor: colors[key],
                           data: [sortedArrayOfObj[key].data]
                        });
                    });
                    settings = {
                      type: 'bar',
                      data: {
                        labels: ["Programa"],
                        datasets: datasets
                      },
                      options: {
                        barValueSpacing: 6,
                        scales: {
                          yAxes: [{
                            ticks: {
                              min: 0,
                            }
                          }]
                        }
                      }
                    };
                }
                pieChartVanProgram = new Chart(document.getElementById("myPieChartOne").getContext("2d"), settings);
            },
        });
    }

    function chartTwo(period) {
        var antepenultimo = new Date();
        var penultimo = new Date();
        var ultimo = new Date();
        antepenultimo.setMonth(antepenultimo.getMonth() - 2);
        penultimo.setMonth(penultimo.getMonth() - 1);
        if(period == 'antepenultimo') $('#performance_pbm_title').text('Performance do mês de ' + antepenultimo.toLocaleDateString('pt-br', { month: 'long' }));
        else if(period == 'penultimo') $('#performance_pbm_title').text('Performance do mês de ' + penultimo.toLocaleDateString('pt-br', { month: 'long' }));
        else if(period == 'ultimo') $('#performance_pbm_title').text('Performance do mês de ' + ultimo.toLocaleDateString('pt-br', { month: 'long' }));
        else if(period == 'Todos') $('#performance_pbm_title').text('Performance dos últimos 3 meses');

        $.ajax({
            type: "GET",
            url: "pbm/perfomancePBM",
            data: { period: period },
            success: function (data) {
                obj = JSON.parse(data);
                if(typeof pieChartProgram !== 'undefined') pieChartProgram.destroy();
                if(period != 'Todos') {
                    settings = {
                        type: 'pie',
                        data: {
                          labels: obj.labels,
                          datasets: [{
                              backgroundColor: ['#e74a3b', '#4e73df', '#1cc88a', '#f6c23e'],
                              borderWidth: 0,
                              data: obj.data
                            }
                          ]
                        },
                        options: {
                          legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}},
                          tooltips: {
                            callbacks: {
                              label (t, d) {
                                return d.labels[t.index].toLowerCase().charAt(0).toUpperCase() + d.labels[t.index].toLowerCase().slice(1) + ": " + parseFloat(d.datasets[0].data[t.index]).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                              }
                            }
                          },
                        }
                    }
                }
                else {
                    const arrayOfObj = obj.labels.map(function (d, i) {
                      return {
                        label: d,
                        data: obj.data[i] || 0,
                      }
                    })

                    const sortedArrayOfObj = arrayOfObj.sort(function (a, b) {
                      return b.data - a.data
                    })
                    datasets = [];
                    colors = ['#e74a3b', '#4e73df', '#1cc88a', '#f6c23e'];
                    Object.keys(sortedArrayOfObj).forEach((key, index) => {
                        datasets.push({
                           label: sortedArrayOfObj[key].label,
                           backgroundColor: colors[key],
                           data: [sortedArrayOfObj[key].data]
                        });
                    });
                    settings = {
                      type: 'bar',
                      data: {
                        labels: ["Van"],
                        datasets: datasets
                      },
                      options: {
                        barValueSpacing: 6,
                        scales: {
                          yAxes: [{
                            ticks: {
                              min: 0,
                            }
                          }]
                        },
                        tooltips: {
                          callbacks: {
                            label (t, d) {
                              return d.datasets[t.datasetIndex].label + ": " + parseFloat(d.datasets[t.datasetIndex].data[0]).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                            }
                          }
                        },
                      }
                    };
                }
                pieChartProgram = new Chart(document.getElementById("myPieChartSecond"), settings);
            },
        });
    }

    function populatePBMAnalysis() {
        $('#modal_pbm .modal-header > h4').text("Performance dos produtos de PBM");
        chartOne('Van');
        chartTwo('Todos');
    }
</script>
