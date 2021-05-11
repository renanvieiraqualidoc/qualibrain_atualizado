<!-- <div id="totalprodutosmodal" style="display:none;" class="modal fade" role="dialog"> -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
               <h4><?php //echo $title?></h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                                    <canvas id="totalBarChart_<?php //echo $id_data_table?>"></canvas>
                               </div>
                           </div>
                       </div>

                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">
                               Produtos Por Categoria
                               </div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="totalPieChart_<?php //echo $id_data_table?>"></canvas>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="float-right">
                   <a href="<?php //echo base_url().'/relatorio?type='.$id_data_table; ?>" class="btn btn-success btn-icon-split">
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
                           <table class="display table table-bordered table-sm table-hover" id="dataTable_<?php //echo $id_data_table?>" width="100%" cellspacing="0">
                               <thead class="thead-dark">
                                   <tr>
                                       <th>SKU</th>
                                       <th>Título</th>
                                       <th>Departamento</th>
                                       <th>Categoria</th>
                                       <th>Estoque</th>
                                       <th title="Quantidade de Concorrentes Disponíveis">Conc.</th>
                                       <th title="Preço de Custo">Custo</th>
                                       <th title="Valor de Venda">Venda</th>
                                       <th title="Menor Preço">Menor</th>
                                       <th title="Margem %">Margem</th>
                                       <th title="Discrepância">Disc.</th>
                                       <th>Curva</th>
                                   </tr>
                               </thead>
                               <tfoot class="thead-dark">
                                   <tr>
                                       <th>SKU</th>
                                       <th>Título</th>
                                       <th>Departamento</th>
                                       <th>Categoria</th>
                                       <th>Estoque</th>
                                       <th title="Quantidade de Concorrentes Disponíveis">Conc.</th>
                                       <th title="Preço de Custo">Custo</th>
                                       <th title="Valor de Venda">Venda</th>
                                       <th title="Menor Preço">Menor</th>
                                       <th title="Margem %">Margem</th>
                                       <th title="Discrepância">Disc.</th>
                                       <th>Curva</th>
                                   </tr>
                               </tfoot>
                               <tbody>
                                   <?php //foreach(json_decode($produtos) as $row):?>
                                   <tr>
                                       <td><a target="_blank" href="https://www.qualidoc.com.br/cadastro/product/<?php //echo $row->sku;?>"><?php //echo $row->sku;?></a></td>
                                       <td><?php //echo $row->title;?></td>
                                       <td><?php //echo $row->department;?></td>
                                       <td><?php //echo $row->category;?></td>
                                       <td><?php //echo intval($row->qty_stock_rms);?></td>
                                       <td><?php //echo $row->qty_competitors_available;?></td>
                                       <td><?php //echo number_to_currency($row->price_cost, 'BRL', null, 2);?></td>
                                       <td><?php //echo number_to_currency($row->current_price_pay_only, 'BRL', null, 2);?></td>
                                       <td><?php //echo number_to_currency($row->current_less_price_around, 'BRL', null, 2);?></td>
                                       <td><?php //echo $row->current_gross_margin_percent;?></td>
                                       <td><?php //echo $row->diff_current_pay_only_lowest;?></td>
                                       <td><?php //echo $row->curve;?></td>
                                   </tr>
                                   <?php //endforeach; ?>
                               </tbody>
                           </table>
                       </div>
                   </div>
               </div>
            </div>
        </div>
    </div>
<!-- </div> -->

<?php echo script_tag('vendor/chart.js/Chart.min.js'); ?>
<script language='javascript'>
    new Chart(document.getElementById("totalBarChart").getContext("2d"), {
      type: 'bar',
      data: {
        labels: ["Concorrentes"],
        datasets: [{
           label: "Onofre",
           backgroundColor: "#4e73df",
           data: [<?php //echo $onofre?>]
        }, {
           label: "Drogaraia",
           backgroundColor: "#1cc88a",
           data: [<?php //echo $drogaraia?>]
        }, {
           label: "Drogaria SP",
           backgroundColor: "#36b9cc",
           data: [<?php //echo $drogariasaopaulo?>]
        }, {
           label: "Pague Menos",
           backgroundColor: "#f6c23e",
           data: [<?php //echo $paguemenos?>]
        }, {
           label: "Drogasil",
           backgroundColor: "#e74a3b",
           data: [<?php //echo $drogasil?>]
        }, {
           label: "Ultrafarma",
           backgroundColor: "#858796",
           data: [<?php //echo $ultrafarma?>]
        }, {
           label: "Beleza na Web",
           backgroundColor: "#f8f9fc",
           data: [<?php //echo $belezanaweb?>]
        }, {
           label: "Panvel",
           backgroundColor: "#5a5c69",
           data: [<?php //echo $panvel?>]
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

    new Chart(document.getElementById("totalPieChart"), {
        type: 'pie',
        data: {
          labels: <?php //echo json_encode($products_categories)?>,
          datasets: [{
              backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#f8f9fc','#5a5c69'].slice(0,<?php //echo count($products_categories)?>),
              borderWidth: 0,
              data: <?php //echo json_encode($count_categories)?>
            }
          ]
        },
        options: {
          cutoutPercentage: 85,
          legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
        }
    });
</script>
