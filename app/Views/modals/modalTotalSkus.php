<div class="modal" id="modal_blister_skus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                           <div class="card mb-8">
                               <div class="card-header">Produtos Por Curva e Situação</div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="skusBarChart"></canvas>
                               </div>
                           </div>
                       </div>
                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">Ranking de Menor Preço por Concorrente</div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="skusPieChart"></canvas>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="float-right">
                   <a href="" class="btn btn-success btn-icon-split">
                       <span class="icon text-white-50">
                           <i class="fas fa-file-excel"></i>
                       </span>
                       <span class="text">Exportar</span>
                   </a>
               </div>
               <br><br>
               <div class="dropdown-divider"></div>
               <br>
               <div class="card shadow mb-4 d-none d-md-block">
                   <div class="card-body">
                       <div class="table-responsive">
                           <table class="display table table-bordered table-sm table-hover" id="skusDataTable" width="100%" cellspacing="0">
                               <thead class="thead-dark">
                                   <tr>
                                       <th>SKU</th>
                                       <th>Título</th>
                                       <th>Departamento</th>
                                       <th>Categoria</th>
                                       <th title="Preço de Custo">Custo</th>
                                       <th title="Valor de Venda">Venda</th>
                                       <th title="Pague Apenas">Apenas</th>
                                       <th title="Menor Preço">Menor</th>
                                       <th title="Concorrente">Conc.</th>
                                       <th title="Margem Operacional">Op.</th>
                                       <th title="Diferença de Menor Preço">Dif. Menor</th>
                                       <th>Curva</th>
                                       <th>Estoque</th>
                                       <th title="Quantidade de Concorrentes Disponíveis">Qtd. Conc.</th>
                                       <th>Marca</th>
                                       <th>Vendas</th>
                                   </tr>
                               </thead>
                               <tfoot class="thead-dark">
                                   <tr>
                                       <th>SKU</th>
                                       <th>Título</th>
                                       <th>Departamento</th>
                                       <th>Categoria</th>
                                       <th title="Preço de Custo">Custo</th>
                                       <th title="Valor de Venda">Venda</th>
                                       <th title="Pague Apenas">Apenas</th>
                                       <th title="Menor Preço">Menor</th>
                                       <th title="Concorrente">Conc.</th>
                                       <th title="Margem Operacional">Op.</th>
                                       <th title="Diferença de Menor Preço">Dif. Menor</th>
                                       <th>Curva</th>
                                       <th>Estoque</th>
                                       <th title="Quantidade de Concorrentes Disponíveis">Qtd. Conc.</th>
                                       <th>Marca</th>
                                       <th>Vendas</th>
                                   </tr>
                               </tfoot>
                               <tbody></tbody>
                           </table>
                       </div>
                   </div>
               </div>
            </div>
        </div>
     </div>
 </div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    function populateDataSkus(data) {
        var object = JSON.parse(data);
        var products = [];
        JSON.parse(object.skus).forEach(function(item, index) {
            products.push([ item.sku, item.title, item.department, item.category, item.price_cost,
                            item.sale_price, item.current_price_pay_only, item.current_less_price_around,
                            item.lowest_price_competitor, item.current_gross_margin_percent,
                            item.diff_current_pay_only_lowest, item.curve, item.qty_stock_rms,
                            item.qty_competitors, item.marca, item.vendas, item.status, item.situation ])
        })
        $('.modal-header > h4').text(object.title); // Seta o título da modal
        $('.float-right > a').attr("href", object.relatorio_url); // Seta o link de exportação da planilha

        // Constrói a tabela do departamento escolhido
        $('#skusDataTable').DataTable().destroy();
        $('#skusDataTable').DataTable({
            language: {
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "Nenhum registro",
                infoFiltered: "(filtrado de _MAX_ registros)",
                infoPostFix: "",
                thousands: ".",
                decimal: "",
                emptyTable: "Não existem registros",
                lengthMenu: "", //"_MENU_ registros por página",
                loadingRecords: "Carregando...",
                processing: "Processando...",
                search: "Buscar por:",
                zeroRecords: "Não foi encontrado nenhum registro",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Próxima",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending:  ": ativado para ordenar por ordem crescente",
                    sortDescending: ": ativado para ordenar por ordem decrescente"
                }
            },
            "aaData": products,
            "aoColumnDefs":[
                {
                    "aTargets": [0],
                    "mRender": function ( url, type, full )  {
                        return  '<a target="_blank" href="https://www.qualidoc.com.br/cadastro/product/' + url + '">' + url + '</a>';
                    }
                },
                {
                    "aTargets": [4],
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [5],
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [6],
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [7],
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [8],
                    "bSortable": false
                },
                {
                    "aTargets": [9],
                    "mRender": function ( value, type, full )  {
                        return parseInt(value) + "%";
                    }
                },
                {
                    "aTargets": [10],
                    "mRender": function ( value, type, full )  {
                        return parseInt(value) + "%";
                    }
                },
            ]
        });

        var total = products.length;
        var total_a = products.filter(function (item) { return item[11] == 'A'; }).length;
        var total_b = products.filter(function (item) { return item[11] == 'B'; }).length;
        var total_c = products.filter(function (item) { return item[11] == 'C'; }).length;
        var break_ = products.filter(function (item) { return item[16] == 3; }).length;
        var break_a = products.filter(function (item) { return item[11] == 'A' && item[16] == 3; }).length;
        var break_b = products.filter(function (item) { return item[11] == 'B' && item[16] == 3; }).length;
        var break_c = products.filter(function (item) { return item[11] == 'C' && item[16] == 3; }).length;
        var under_equal_cost = products.filter(function (item) { return item[17] == 2; }).length;
        var under_equal_cost_a = products.filter(function (item) { return item[11] == 'A' && item[17] == 2; }).length;
        var under_equal_cost_b = products.filter(function (item) { return item[11] == 'B' && item[17] == 2; }).length;
        var under_equal_cost_c = products.filter(function (item) { return item[11] == 'C' && item[17] == 2; }).length;
        var sacrifice_op_margin = products.filter(function (item) { return item[17] == 4; }).length;
        var sacrifice_op_margin_a = products.filter(function (item) { return item[11] == 'A' && item[17] == 4; }).length;
        var sacrifice_op_margin_b = products.filter(function (item) { return item[11] == 'B' && item[17] == 4; }).length;
        var sacrifice_op_margin_c = products.filter(function (item) { return item[11] == 'C' && item[17] == 4; }).length;
        var sacrifice_gain_margin = products.filter(function (item) { return item[17] == 5; }).length;
        var sacrifice_gain_margin_a = products.filter(function (item) { return item[11] == 'A' && item[17] == 5; }).length;
        var sacrifice_gain_margin_b = products.filter(function (item) { return item[11] == 'B' && item[17] == 5; }).length;
        var sacrifice_gain_margin_c = products.filter(function (item) { return item[11] == 'C' && item[17] == 5; }).length;
        var exclusive_stock = products.filter(function (item) { return item[16] == 4; }).length;
        var exclusive_stock_a = products.filter(function (item) { return item[11] == 'A' && item[16] == 4; }).length;
        var exclusive_stock_b = products.filter(function (item) { return item[11] == 'B' && item[16] == 4; }).length;
        var exclusive_stock_c = products.filter(function (item) { return item[11] == 'C' && item[16] == 4; }).length;

        //Plotagem do gráfico de barras
        if(typeof barChart !== 'undefined') barChart.destroy();
        barChart = new Chart(document.getElementById("skusBarChart").getContext("2d"), {
          type: 'bar',
          data: {
            labels: ["Total", "Curva A", "Curva B", "Curva C"],
            datasets: [{
               label: "Total Produtos",
               backgroundColor: "#4e73df",
               data: [total, total_a, total_b, total_c]
            }, {
               label: "Ruptura",
               backgroundColor: "#1cc88a",
               data: [break_, break_a, break_b, break_c]
            }, {
               label: "Abaixo/Igual Custo",
               backgroundColor: "#36b9cc",
               data: [under_equal_cost, under_equal_cost_a, under_equal_cost_b, under_equal_cost_c]
            }, {
               label: "Sacrificando Margem OP.",
               backgroundColor: "#f6c23e",
               data: [sacrifice_op_margin, sacrifice_op_margin_a, sacrifice_op_margin_b, sacrifice_op_margin_c]
            }, {
               label: "Sacrificando Margem Lucro",
               backgroundColor: "#e74a3b",
               data: [sacrifice_gain_margin, sacrifice_gain_margin_a, sacrifice_gain_margin_b, sacrifice_gain_margin_c]
            }, {
               label: "Estoque Exclusivo",
               backgroundColor: "#858796",
               data: [exclusive_stock, exclusive_stock_a, exclusive_stock_b, exclusive_stock_c]
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

        // Plotagem do gráfico circular
        if(typeof pieChart !== 'undefined') pieChart.destroy();
        /*pieChart = new Chart(document.getElementById("skusPieChart"), {
            type: 'pie',
            data: {
              labels: object.products_categories,
              datasets: [{
                  backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#f8f9fc','#5a5c69'],
                  borderWidth: 0,
                  data: object.count_categories
                }
              ]
            },
            options: {
              cutoutPercentage: 85,
              legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
            }
        });*/
    }
</script>
