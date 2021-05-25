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
    $(document).ready(function() {
        populateDataDepartment('medicamento');
    })

    // Constrói a tabela do departamento escolhido
    function populateDataDepartment(department) {
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
            destroy: true,
            "initComplete": function( settings, json ) {
                $('.modal-header > h4').text(json.title); // Seta o título da modal
                $('.float-right > a').attr("href", json.relatorio_url); // Seta o link de exportação da planilha

                //Plotagem do gráfico de barras
                if(typeof depBarChart !== 'undefined') depBarChart.destroy();
                depBarChart = new Chart(document.getElementById("departmentBarChart").getContext("2d"), {
                  type: 'bar',
                  data: {
                    labels: ["Concorrentes"],
                    datasets: [{
                       label: "Onofre",
                       backgroundColor: "#4e73df",
                       data: [json.onofre]
                    }, {
                       label: "Drogaraia",
                       backgroundColor: "#1cc88a",
                       data: [json.drogaraia]
                    }, {
                       label: "Drogaria SP",
                       backgroundColor: "#36b9cc",
                       data: [json.drogariasaopaulo]
                    }, {
                       label: "Pague Menos",
                       backgroundColor: "#f6c23e",
                       data: [json.paguemenos]
                    }, {
                       label: "Drogasil",
                       backgroundColor: "#e74a3b",
                       data: [json.drogasil]
                    }, {
                       label: "Ultrafarma",
                       backgroundColor: "#858796",
                       data: [json.ultrafarma]
                    }, {
                       label: "Beleza na Web",
                       backgroundColor: "#f8f9fc",
                       data: [json.belezanaweb]
                    }, {
                       label: "Panvel",
                       backgroundColor: "#5a5c69",
                       data: [json.panvel]
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
                if(typeof depPieChart !== 'undefined') depPieChart.destroy();
                depPieChart = new Chart(document.getElementById("departmentPieChart"), {
                    type: 'pie',
                    data: {
                      labels: json.products_categories,
                      datasets: [{
                          backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#f8f9fc','#5a5c69'],
                          borderWidth: 0,
                          data: json.count_categories
                        }
                      ]
                    },
                    options: {
                      cutoutPercentage: 85,
                      legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
                    }
                });

                $('#loader').hide();
            },
            "bProcessing": true,
            "sAjaxSource": "pricing/competitorInfo?department="+department,
            'serverSide': true,
            "aoColumnDefs":[
                {
                    "aTargets": [0],
                    "mData": 'sku',
                    "mRender": function ( url, type, full )  {
                        return  '<a target="_blank" href="https://www.qualidoc.com.br/cadastro/product/' + url + '">' + url + '</a>';
                    }
                },
                {
                    "aTargets": [1],
                    "mData": 'title',
                },
                {
                    "aTargets": [2],
                    "mData": 'department',
                    "bSortable": false
                },
                {
                    "aTargets": [3],
                    "mData": 'category'
                },
                {
                    "aTargets": [4],
                    "mData": 'qty_stock_rms',
                    "mRender": function ( value, type, full )  {
                        return parseInt(value);
                    }
                },
                {
                    "aTargets": [5],
                    "mData": 'qty_competitors_available'
                },
                {
                    "aTargets": [6],
                    "mData": 'price_cost',
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [7],
                    "mData": 'current_price_pay_only',
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [8],
                    "mData": 'current_less_price_around',
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [9],
                    "mData": 'current_gross_margin_percent',
                    "mRender": function ( value, type, full )  {
                        return (value*100).toFixed(2).replace(".", ",") + "%";
                    }
                },
                {
                    "aTargets": [10],
                    "mData": 'diff_current_pay_only_lowest',
                    "mRender": function ( value, type, full )  {
                        return (value*100).toFixed(2).replace(".", ",") + "%";
                    }
                },
                {
                    "aTargets": [11],
                    "mData": 'curve'
                },
            ]
        });
    }
</script>
