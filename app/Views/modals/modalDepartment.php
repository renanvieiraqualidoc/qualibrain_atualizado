<div class="modal" id="modal_departments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                               <div class="card-header">Competitividade por concorrente</div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="departmentBarChart"></canvas>
                               </div>
                           </div>
                       </div>
                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">Produtos Por Categoria</div>
                               <div class="chart-pie pt-4 pb-2">
                                    <canvas id="departmentPieChart"></canvas>
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
                           <table class="display table table-bordered table-sm table-hover" id="departmentDataTable" width="100%" cellspacing="0">
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
    function populateDataDepartment(data) {
        var object = JSON.parse(data);
        var products = [];
        JSON.parse(object.produtos).forEach(function(item, index) {
            products.push([ item.sku, item.title, item.department, item.category, item.qty_stock_rms,
                            item.qty_competitors_available, item.price_cost, item.current_price_pay_only,
                            item.current_less_price_around, item.current_gross_margin_percent,
                            item.diff_current_pay_only_lowest, item.curve])
        })
        $('.modal-header > h4').text(object.title); // Seta o título da modal
        $('.float-right > a').attr("href", object.relatorio_url); // Seta o link de exportação da planilha

        $('#departmentDataTable').DataTable().destroy(); // Destrói os dados da tabela anterior

        // Constrói a tabela do departamento escolhido
        $('#departmentDataTable').DataTable({
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
                    "aTargets": [2],
                    "bSortable": false
                },
                {
                    "aTargets": [4],
                    "mRender": function ( value, type, full )  {
                        return parseInt(value);
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
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
            ]
        });

        //Plotagem do gráfico de barras
        new Chart(document.getElementById("departmentBarChart").getContext("2d"), {
          type: 'bar',
          data: {
            labels: ["Concorrentes"],
            datasets: [{
               label: "Onofre",
               backgroundColor: "#4e73df",
               data: [object.onofre]
            }, {
               label: "Drogaraia",
               backgroundColor: "#1cc88a",
               data: [object.drogaraia]
            }, {
               label: "Drogaria SP",
               backgroundColor: "#36b9cc",
               data: [object.drogariasaopaulo]
            }, {
               label: "Pague Menos",
               backgroundColor: "#f6c23e",
               data: [object.paguemenos]
            }, {
               label: "Drogasil",
               backgroundColor: "#e74a3b",
               data: [object.drogasil]
            }, {
               label: "Ultrafarma",
               backgroundColor: "#858796",
               data: [object.ultrafarma]
            }, {
               label: "Beleza na Web",
               backgroundColor: "#f8f9fc",
               data: [object.belezanaweb]
            }, {
               label: "Panvel",
               backgroundColor: "#5a5c69",
               data: [object.panvel]
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
        new Chart(document.getElementById("departmentPieChart"), {
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
        });
    }
</script>