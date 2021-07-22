<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório PBM</h1>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data Inicial</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdata" id="vdata">
        </div>
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data Final</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdatafinal" id="vdatafinal">
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="btn btn-success btn-user btn-block"><span class="icon text-white-50"><i class="fas fa-file-excel"></i></span>  Exportar</a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a class="btn btn-primary btn-user btn-block" data-toggle="modal" data-target="#modal_pbm"><span class="icon text-white-50"><i class="fas fa-fw fa-chart-bar"></i></span>  Performance</a>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-xl-12 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data de Análise</div>
            </div>
            <input type="date" class="form-control form-control-user" name="selected_date" value="<?=date('Y-m-d');?>" id="selected_date">
        </div>
    </div>
    <div class="row">
        <div class="col-xl-8 col-md-6 mb-12" width="90%">
            <div id="showfaturamento" width="88%"></div><br>
        </div>
        <div class="col-xl-4 col-md-6 mb-12" width="90%">
            <div id="showranking" width="88%"></div>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
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

<?php echo view('modals/modalPBM'); ?>
<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    function chartMargin(program) {
        $.ajax({
            type: "GET",
            url: "pbm/analysis",
            data: { program: program },
            success: function (data) {
                $('#margin_pbm_title').text('Performance Mensal do PBM (' + program + ')');
                obj = JSON.parse(data);
                if(typeof areaChartPBM !== 'undefined') areaChartPBM.destroy();
                areaChartPBM = new Chart(document.getElementById("myAreaChartPBM"), {
                  type: 'line',
                  data: {
                    labels: obj.labels_line_chart,
                    datasets: [{
                      label: "Margem",
                      backgroundColor: "rgba(92, 203, 142, 0.01)",
                      borderColor: "rgba(92, 203, 142)",
                      yAxisID: 'margem',
                      lineTension: 0.3,
                      pointRadius: 3,
                      pointBackgroundColor: "rgba(92, 203, 142, 1)",
                      pointBorderColor: "rgba(92, 203, 142, 1)",
                      pointHoverRadius: 3,
                      pointHoverBackgroundColor: "rgba(92, 203, 142, 1)",
                      pointHoverBorderColor: "rgba(92, 203, 142, 1)",
                      pointHitRadius: 10,
                      pointBorderWidth: 2,
                      data: obj.data_margin_line_chart,
                      stack: 'combined'
                    },
                    {
                      label: "Faturamento",
                      backgroundColor: "rgba(78, 115, 223)",
                      borderColor: "#1cc88a",
                      yAxisID: 'faturamento',
                      lineTension: 0.3,
                      pointRadius: 3,
                      pointBackgroundColor: "#23580e",
                      pointBorderColor: "#23580e",
                      pointHoverRadius: 3,
                      pointHoverBackgroundColor: "#1cc88a",
                      pointHoverBorderColor: "#1cc88a",
                      pointHitRadius: 10,
                      pointBorderWidth: 2,
                      data: obj.data_fat_line_chart,
                      stack: 'combined',
                      type: 'bar'
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
                        id: 'faturamento',
                        type: 'linear',
                        position: 'left',
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
                      },
                      {
                        id: 'margem',
                        type: 'linear',
                        position: 'right',
                        ticks: {
                          callback: function(value, index, values) {
                            return (value).toFixed(2).replace(".", ",") + "%";
                          }
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

    $(document).ready(function(){
        $('a.btn-success').attr("href", 'relatorio?type=pbm&initial_date=' + $('#vdata').val() + '&final_date=' + $('#vdatafinal').val());
        populate();
        chartMargin('Todos');

        $("#vdata").change(function(){
            $('a.btn-success').attr("href", 'relatorio?type=pbm&initial_date=' + $('#vdata').val() + '&final_date=' + $('#vdatafinal').val());
        });

        $("#vdatafinal").change(function(){
            $('a.btn-success').attr("href", 'relatorio?type=pbm&initial_date=' + $('#vdata').val() + '&final_date=' + $('#vdatafinal').val());
        });

        $("#selected_date").change(function(){
            populate();
        });

        // Clique na modal de análise do programa de PBM
        $("#modal_pbm").on('show.bs.modal', function(e) {
            populatePBMAnalysis();
        })

        function populate() {
            $.ajax({
                type: "GET",
                url: "pbm/populateTable",
                data: { selected_date: $('#selected_date').val() },
                success: function(result){
                    var selected_date = new Date($('#selected_date').val());
                    selected_date.setDate(selected_date.getDate() + 1);
                    var last_day = new Date();
                    var actual_time = last_day.getHours();
                    var last_week = new Date();
                    last_day.setDate(selected_date.getDate() - 1);
                    last_week.setDate(selected_date.getDate() - 7);
                    html = '<div class="table-responsive">' +
                               '<div class="container" width="100%" style="overflow: scroll; height: 350px;">' +
                                   '<table width=100% border=0>' +
                                       '<tr>' +
                                           '<td width=33%>' +
                                               '<p class="text-center"><b>' + selected_date.toLocaleDateString('pt-br') + '(Data Escolhida) </b></p>' +
                                           '</td>' +
                                           '<td width=33%>' +
                                               '<b><p class="text-center">' + last_day.toLocaleDateString('pt-br') + '(Dia Anterior)</p> </b>' +
                                           '</td>' +
                                           '<td>' +
                                               '<b><p class="text-center">' + last_week.toLocaleDateString('pt-br') + '(Semana Passada) </b></p>' +
                                           '</td>' +
                                       '</tr>' +
                                   '</table>' +
                                   '<table border="1" width="100%"  style=" border-collapse: collapse;border-spacing: 0;text-align:center;"  class="table-hover">' +
                                       '<thead style="background-color:lightgray">' +
                                           '<th><font color="black">HORA</th>' +
                                           '<th><font color="black">QTD NF</th>' +
                                           '<th><font color="black">VALOR</th>' +
                                           '<th><font color="black">TKM</th>' +
                                           '<th style="background-color:black";></th>' +
                                           '<th><font color="black">QTD NF</th>' +
                                           '<th><font color="black">VALOR</th>' +
                                           '<th><font color="black">TKM</th>' +
                                           '<th style="background-color:black"></th>' +
                                           '<th><font color="black">QTD NF</th>' +
                                           '<th><font color="black">VALOR</th>' +
                                           '<th><font color="black">TKM</th>' +
                                       '</thead>';
                    obj_sales = JSON.parse(result).sales
                    var total_qtd_today = 0;
                    var total_value_today = 0;
                    var total_tkm_today = 0;
                    var total_qtd_yesterday = 0;
                    var total_value_yesterday = 0;
                    var total_tkm_yesterday = 0;
                    var total_qtd_last_week = 0;
                    var total_value_last_week = 0;
                    var total_tkm_last_week = 0;
                    Object.keys(obj_sales).forEach((key, index) => {
                        html += '<tr>' +
                                     '<th style="background-color:lightgray"><font color="black">' + ((key < 10) ? "0" + key : key) + '</font></th>' +
                                     '<td>' + obj_sales[key].qtd_today + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].value_today).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].tkm_today).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td style="background-color:black"></td>' +
                                     '<td>' + obj_sales[key].qtd_yesterday + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].value_yesterday).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].tkm_yesterday).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td style="background-color:black"></td>' +
                                     '<td>' + obj_sales[key].qtd_last_week + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].value_last_week).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].tkm_last_week).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                '</tr>';
                        total_qtd_today += obj_sales[key].qtd_today;
                        total_value_today += obj_sales[key].value_today;
                        total_tkm_today += obj_sales[key].tkm_today;
                        total_qtd_yesterday += obj_sales[key].qtd_yesterday;
                        total_value_yesterday += obj_sales[key].value_yesterday;
                        total_tkm_yesterday += obj_sales[key].tkm_yesterday;
                        total_qtd_last_week += obj_sales[key].qtd_last_week;
                        total_value_last_week += obj_sales[key].value_last_week;
                        total_tkm_last_week += obj_sales[key].tkm_last_week;
                    });
                    html += '<tr style="background-color:lightblue;border:0">' +
                                '<td><font color="black"><b>TOTAL</b></font></td>' +
                                '<td><font color="black"><b>' + total_qtd_today +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_value_today).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_tkm_today).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td style="background-color:black"></td>' +
                                '<td><font color="black"><b>' + total_qtd_yesterday +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_value_yesterday).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_tkm_yesterday).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td style="background-color:black"></td>' +
                                '<td><font color="black"><b>' + total_qtd_last_week +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_value_last_week).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_tkm_last_week).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                           '</tr>' +
                        '</table>' +
                    '</div>';
                    $("#showfaturamento").html(html);

                    html = '<div class="table-responsive">' +
                              '<div class="container" width="100%">' +
                                  '<table width=100% border=0>' +
                                      '<tr>' +
                                          '<td>' +
                                              '<b><p class="text-center">Programas mais vendidos</b></p>' +
                                          '</td>' +
                                      '</tr>' +
                                  '</table>' +
                                  '<table border="1" width="100%" style="border-collapse: collapse;border-spacing: 0;text-align:center;"  class="table-hover">' +
                                      '<thead style="background-color:lightgray">' +
                                          '<th><font color="black">Programa</th>' +
                                          '<th><font color="black">Quantidade</th>' +
                                          '<th style="background-color:black";></th>' +
                                      '</thead>';
                    obj_ranking = JSON.parse(result).ranking
                    Object.keys(obj_ranking).forEach((key, index) => {
                        html += '<tr>' +
                                     '<td>' + obj_ranking[key].programa + '</td>' +
                                     '<td>' + obj_ranking[key].qtd + '</td>' +
                                     '<td style="background-color:black"></td>' +
                                '</tr>';
                    });
                    $("#showranking").html(html);
                }
            });
        }
    });
</script>
<?=$this->endSection(); ?>
