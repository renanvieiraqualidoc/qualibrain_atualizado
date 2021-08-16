<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Faturamento</h1>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-warning text-uppercase mb-1">Meta MTD</div>
                            <div class="table-responsive" width="100%">
                                <table border="0" width="100%" style=" border-collapse: collapse;border-spacing: 0;">
                                    <tr>
                                        <td class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="text-align:right;">Faturamento</td>
                                        <td class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="text-align:center;"><?=number_to_currency(end($months)['gross_billing'], 'BRL', null, 2);?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="text-align:right;">Pedidos</td>
                                        <td class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="text-align:center;"><?=number_to_amount(end($months)['qtd_orders'], 2, 'pt_BR');?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="text-align:right;">Ticket Médio</td>
                                        <td class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="text-align:center;"><?=number_to_currency(end($months)['tkm'], 'BRL', null, 2);?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-success text-uppercase mb-1">Realizado</div>
                            <table border="0" width="100%" style=" border-collapse: collapse;border-spacing: 0;">
                                <tr>
                                    <td class="text-xs font-weight-bold text-success text-uppercase mb-1" style="text-align:right;">Faturamento</td>
                                    <td class="text-xs font-weight-bold text-success text-uppercase mb-1" style="text-align:center;"><?=number_to_currency($months[2]['gross_billing'], 'BRL', null, 2);?></td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bold text-success text-uppercase mb-1" style="text-align:right;">Pedidos</td>
                                    <td class="text-xs font-weight-bold text-success text-uppercase mb-1" style="text-align:center;"><?=number_to_amount($months[2]['qtd_orders'], 2, 'pt_BR');?></td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bold text-success text-uppercase mb-1" style="text-align:right;">Ticket Médio</td>
                                    <td class="text-xs font-weight-bold text-success text-uppercase mb-1" style="text-align:center;"><?=number_to_currency($months[2]['tkm'], 'BRL', null, 2);?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-info text-uppercase mb-1">Meta MTD Vs. Realizado</div>
                            <table border="0" width="100%" style=" border-collapse: collapse;border-spacing: 0;">
                                <tr>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:right;">Faturamento</td>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:center;"><?=number_to_currency($months[2]['gross_billing'] - end($months)['gross_billing'], 'BRL', null, 2);?></td>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:center;"><?=number_to_amount(($months[2]['gross_billing']/end($months)['gross_billing'] - 1)*100, 2, 'pt_BR')."%"?></td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:right;">Pedidos</td>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:center;"><?=number_to_amount($months[2]['qtd_orders'] - end($months)['qtd_orders'], 2, 'pt_BR');?></td>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:center;"><?=number_to_amount(($months[2]['qtd_orders']/end($months)['qtd_orders'] - 1)*100, 2, 'pt_BR')."%"?></td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:right;">Ticket Médio</td>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:center;"><?=number_to_currency($months[2]['tkm'] - end($months)['tkm'], 'BRL', null, 2);?></td>
                                    <td class="text-xs font-weight-bold text-info text-uppercase mb-1" style="text-align:center;"><?=number_to_amount(($months[2]['tkm']/end($months)['tkm'] - 1)*100, 2, 'pt_BR')."%"?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-danger text-uppercase mb-1">Meta de Faturamento (%)</div>
                            <div id="container" style="height: 100px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive mb-3" width="100%">
            <table border="1" width="100%" style=" border-collapse: collapse;border-spacing: 0;text-align:center;"  class="table-hover">
                <thead style="background-color:lightgray">
                    <th><font color="black">Mês</th>
                    <th title="Faturamento Bruto"><font color="black">Fat. Bruto</th>
                    <th><font color="black">Pedidos</th>
                    <th title="Ticket Médio"><font color="black">TKM</th>
                    <th title="Comparativo com o Mês Anterior"><font color="black">Mês Ant.</th>
                    <th><font color="black">Margem</th>
                </thead>
                <?php for($i=0; $i<count($months); $i++):?>
                <tr <?=($i == (count($months)-1) ? 'style="background-color:lightblue;"' : '' );?>>
                    <td><?=($i == (count($months)-1) ? 'Proj. ' : $months[$i]['month']);?></td>
                    <td><?=number_to_currency($months[$i]['gross_billing'], 'BRL', null, 0)?></td>
                    <td><?=number_to_amount($months[$i]['qtd_orders'], 2, 'pt_BR')?></td>
                    <td><?=number_to_currency($months[$i]['tkm'], 'BRL', null, 0)?></td>
                    <td><?=number_to_amount($months[$i]['comparative_previous_month'], 2, 'pt_BR')."%"?></td>
                    <td><?=number_to_amount($months[$i]['margin'], 2, 'pt_BR')."%"?></td>
                </tr>
                <?php endfor;?>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="btn btn-primary btn-user btn-block" data-toggle="modal" data-target="#modal_share"><span class="icon text-white-50"><i class="fas fa-fw fa-chart-pie"></i></span>  Share</a>
        </div>
        <!-- <div class="col-xl-3 col-md-6 mb-4">
            <a class="btn btn-primary btn-user btn-block" data-toggle="modal" data-target="#modal_accumulated_gross_margin"><span class="icon text-white-50"><i class="fas fa-fw fa-chart-bar"></i></span>  Margem Bruta Acumulada</a>
        </div> -->
        <!-- <div class="col-xl-3 col-md-6 mb-4">
            <a class="btn btn-primary btn-user btn-block" data-toggle="modal" data-target="#modal_digital_wallet"><span class="icon text-white-50"><i class="fas fa-fw fa-wallet"></i></span>  Carteira Digital</a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="btn btn-primary btn-user btn-block" data-toggle="modal" data-target="#modal_purchases_sales"><span class="icon text-white-50"><i class="fas fa-fw fa-shopping-bag"></i></span>  Compras x Vendas</a>
        </div> -->
    </div>
    <div class="row">
        <div class="card-body">
            <div class="chart-area">
                <canvas id="myAreaChartGrossBilling" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

<?php echo view('modals/modalShare'); ?>
<?php echo view('modals/modalAccumulatedGrossMargin'); ?>
<?php echo view('modals/modalDigitalWallet'); ?>
<?php echo view('modals/modalPurchasesAndSales'); ?>
<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<?php echo script_tag('https://code.highcharts.com/highcharts.js'); ?>
<?php echo script_tag('https://code.highcharts.com/highcharts-more.js'); ?>
<script language='javascript'>
    $(document).ready(function() {
        // Clique da modal de share
        $("#modal_share").on('show.bs.modal', function(e) {
            populateShareGrossBilling();
        })

        Highcharts.chart('container', {
            chart: {
                renderTo: 'container',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            credits: {
                enabled: false
            },
            title: {
                text: '',
                align: 'center',
                verticalAlign: 'top',
                y: 40
            },
            tooltip: {
                pointFormat: '{point.percentage:.0f}%'
            },
            pane: {
                center: ['50%', '75%'],
                size: '70%',
                startAngle: -90,
                endAngle: 90,
                background: {
                    borderWidth: 0,
                    backgroundColor: 'none',
                    innerRadius: '60%',
                    outerRadius: '100%',
                    shape: 'arc'
                }
            },
            yAxis: [{
                lineWidth: 0,
                min: 0,
                max: 90,
                minorTickLength: 0,
                tickLength: 0,
                tickWidth: 0,
                labels: {
                    enabled: false
                },
                title: {
                    text: '', //'<div class="gaugeFooter">46% Rate</div>',
                    useHTML: true,
                    y: 80
                },
                pane: 0,

            }],
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true,
                        distance: 5,
                        style: {
                            fontWeight: 'bold',
                            color: 'white',
                            textShadow: '0px 1px 2px black'
                        }
                    },
                    startAngle: -90,
                    endAngle: 90,
                    center: ['50%', '90%']
                },
                gauge: {
                    dataLabels: {
                        enabled: true
                    },
                    dial: {
                        radius: '100%'
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Meta de Faturamento',
                innerSize: '50%',
                data: [{
                    name: 'Ruim',
                    y: 25,
                    color: 'Red' // Jane's color
                },
                {
                    name: 'Médio',
                    y: 25,
                    color: 'Orange' // Jane's color
                },
                {
                    name: 'Bom',
                    y: 25,
                    color: 'Yellow' // Jane's color
                },
                {
                    name: 'Ótimo',
                    y: 25,
                    color: 'Green' // Jane's color
                }]
            },{
                type: 'gauge',
                data: [<?=floatval(number_to_amount(($months[2]['gross_billing']/end($months)['gross_billing'])*100, 2, 'pt_BR'));?>],
                dial: {
                    rearLength: 0
                }
            }],
        });

        if(typeof areaChartGrossBilling !== 'undefined') areaChartGrossBilling.destroy();
        areaChartGrossBilling = new Chart(document.getElementById("myAreaChartGrossBilling"), {
          type: 'line',
          data: {
            labels: <?=$dates?>,
            datasets: [{
              label: "Pedidos",
              backgroundColor: "rgba(92, 203, 142, 0.01)",
              borderColor: "rgba(92, 203, 142)",
              yAxisID: 'venda',
              lineTension: 0.3,
              pointRadius: 3,
              pointBackgroundColor: "rgba(92, 203, 142, 1)",
              pointBorderColor: "rgba(92, 203, 142, 1)",
              pointHoverRadius: 3,
              pointHoverBackgroundColor: "rgba(92, 203, 142, 1)",
              pointHoverBorderColor: "rgba(92, 203, 142, 1)",
              pointHitRadius: 10,
              pointBorderWidth: 2,
              data: <?=$sales?>,
              // stack: 'combined'
            },
            {
              label: "Faturado",
              backgroundColor: "rgba(78, 115, 223)",
              borderColor: "#1cc88a",
              yAxisID: 'faturado',
              lineTension: 0.3,
              pointRadius: 3,
              pointBackgroundColor: "#23580e",
              pointBorderColor: "#23580e",
              pointHoverRadius: 3,
              pointHoverBackgroundColor: "#1cc88a",
              pointHoverBorderColor: "#1cc88a",
              pointHitRadius: 10,
              pointBorderWidth: 2,
              data: <?=$gross_billings?>,
              // stack: 'combined',
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
                id: 'faturado',
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
                id: 'venda',
                type: 'linear',
                position: 'right'
              }],
            },
            legend: {
              display: true
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
                  var comp = (datasetLabel === "Pedidos") ? chart.datasets[0].data[tooltipItem.index] : parseFloat(tooltipItem.yLabel).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                  return datasetLabel + ': ' + comp;
                }
              }
            }
          }
        });
    })
</script>
<?=$this->endSection(); ?>
