<div id="totalprodutosmodal_<?=$id_data_table?>" style="display:none;" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
               <h4><?=$title?></h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
               <div class="container">
                   <br>
                   <div class="row">
                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">
                               Competitividade por concorrente
                               </div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="totalBarChart_<?=$id_data_table?>"></canvas>
                               </div>
                           </div>
                       </div>

                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">
                               Produtos Por Categoria
                               </div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="totalPieChart_<?=$id_data_table?>"></canvas>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="float-right">
                   <a href="#" class="btn btn-success btn-icon-split">
                       <span class="icon text-white-50">
                           <i class="fas fa-file-excel"></i>
                       </span>
                       <span class="text">Exportar</span>
                   </a>
               </div>
               <br><br>
               <div class="dropdown-divider"></div>
               <br>
               <div class="card shadow mb-4">
                   <div class="card-body">
                       <div class="table-responsive">
                           <table class="display table table-bordered table-sm table-hover" id="dataTable_<?=$id_data_table?>" width="100%" cellspacing="0">
                               <thead class="thead-dark">
                                   <tr>
                                       <th>SKU</th>
                                       <th>Título</th>
                                       <th>Departamento</th>
                                       <th>Categoria</th>
                                       <th>Estoque RMS</th>
                                       <th>Conc. Disponíveis</th>
                                       <th>Preço de Custo</th>
                                       <th>Valor de Venda</th>
                                       <th>Menor Preço</th>
                                       <th>Margem %</th>
                                       <th>Discrepância</th>
                                       <th>Curva</th>
                                       <th>Vendas Acumuladas</th>
                                   </tr>
                               </thead>
                               <tfoot class="thead-dark">
                                   <tr>
                                       <th>SKU</th>
                                       <th>Título</th>
                                       <th>Departamento</th>
                                       <th>Categoria</th>
                                       <th>Estoque RMS</th>
                                       <th>Conc. Disponíveis</th>
                                       <th>Preço de Custo</th>
                                       <th>Valor de Venda</th>
                                       <th>Menor Preço</th>
                                       <th>Margem %</th>
                                       <th>Discrepância</th>
                                       <th>Curva</th>
                                       <th>Vendas Acumuladas</th>
                                   </tr>
                               </tfoot>
                               <tbody>
                                   <?php foreach(json_decode($produtos) as $row):?>
                                   <tr>
                                       <td><a target="_blank" href="https://www.qualidoc.com.br/cadastro/product/<?=$row->sku;?>"><?=$row->sku;?></a></td>
                                       <td><?=$row->title;?></td>
                                       <td><?=$row->department;?></td>
                                       <td><?=$row->category;?></td>
                                       <td><?=intval($row->qty_stock_rms);?></td>
                                       <td><?=$row->qty_competitors_available;?></td>
                                       <td><?=number_to_currency($row->price_cost, 'BRL', null, 2);?></td>
                                       <td><?=number_to_currency($row->current_price_pay_only, 'BRL', null, 2);?></td>
                                       <td><?=number_to_currency($row->current_less_price_around, 'BRL', null, 2);?></td>
                                       <td><?=$row->current_gross_margin_percent;?></td>
                                       <td><?=$row->diff_current_pay_only_lowest;?></td>
                                       <td><?=$row->curve;?></td>
                                       <td><?=$row->vendas_acumuladas;?></td>
                                   </tr>
                                   <?php endforeach; ?>
                               </tbody>
                           </table>
                       </div>
                   </div>
               </div>
            </div>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/chart.js/Chart.min.js'); ?>
<script language='javascript'>
    new Chart(document.getElementById("totalBarChart_<?=$id_data_table?>").getContext("2d"), {
      type: 'bar',
      data: {
        labels: ["Concorrentes"],
        datasets: [{
           label: "Onofre",
           backgroundColor: "#4e73df",
           data: [<?=$onofre?>]
        }, {
           label: "Drogaraia",
           backgroundColor: "#1cc88a",
           data: [<?=$drogaraia?>]
        }, {
           label: "Drogaria SP",
           backgroundColor: "#36b9cc",
           data: [<?=$drogariasaopaulo?>]
        }, {
           label: "Pague Menos",
           backgroundColor: "#f6c23e",
           data: [<?=$paguemenos?>]
        }, {
           label: "Drogasil",
           backgroundColor: "#e74a3b",
           data: [<?=$drogasil?>]
        }, {
           label: "Ultrafarma",
           backgroundColor: "#858796",
           data: [<?=$ultrafarma?>]
        }, {
           label: "Beleza na Web",
           backgroundColor: "#f8f9fc",
           data: [<?=$belezanaweb?>]
        }, {
           label: "Panvel",
           backgroundColor: "#5a5c69",
           data: [<?=$panvel?>]
        }]
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
    });

    new Chart(document.getElementById("totalPieChart_<?=$id_data_table?>"), {
        type: 'pie',
        data: {
          labels: <?=json_encode($products_categories)?>,
          datasets: [{
              backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#f8f9fc','#5a5c69'].slice(0,<?=count($products_categories)?>),
              borderWidth: 0,
              data: <?=json_encode($count_categories)?>
            }
          ]
        },
        options: {
          cutoutPercentage: 85,
          legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
        }
    });
</script>
