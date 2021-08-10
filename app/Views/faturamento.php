<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Faturamento</h1>
    </div>
    <div class="row">
        <div class="table-responsive" width="100%">
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
                    <td><?=$months[$i]['qtd_orders']?></td>
                    <td><?=$months[$i]['tkm']?></td>
                    <td><?=number_to_amount($months[$i]['comparative_previous_month'], 2, 'pt_BR')."%"?></td>
                    <td><?=number_to_amount($months[$i]['margin'], 2, 'pt_BR')."%"?></td>
                </tr>
                <?php endfor;?>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="card-body">
            <div class="chart-area">
                <canvas id="myAreaChartGrossBilling" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    $(document).ready(function() {
        if(typeof areaChartGrossBilling !== 'undefined') areaChartGrossBilling.destroy();
        areaChartGrossBilling = new Chart(document.getElementById("myAreaChartGrossBilling"), {
          type: 'line',
          data: {
            labels: <?=$dates?>,
            datasets: [{
              label: "Vendas",
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
                  var comp = (datasetLabel === "Vendas") ? chart.datasets[0].data[tooltipItem.index] : parseFloat(tooltipItem.yLabel).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                  return datasetLabel + ': ' + comp;
                }
              }
            }
          }
        });
    })
</script>
<?=$this->endSection(); ?>
