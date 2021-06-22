<div class="modal" id="qualimodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                <div class="card-header" id="title_first_chart_qualimodal"></div>
                                <div class="chart-pie pt-4 pb-2">
                                     <canvas id="firstChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="card mb-4">
                                <div class="card-header" id="title_second_chart_qualimodal"></div>
                                <div class="chart-pie pt-4 pb-2">
                                     <canvas id="secondChart"></canvas>
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
                            <table class="display table table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Título</th>
                                        <th>Departamento</th>
                                        <th>Categoria</th>
                                        <th title="Quantidade de itens vendidos">Qtd.</th>
                                        <th title="VMD dos últimos 7 dias">Últ. Sem.</th>
                                        <th title="VMD dos últimos 30 dias">Últ. Mês</th>
                                        <th title="VMD dos últimos 90 dias">Últ. 3 meses</th>
                                        <th title="Faturamento do dia">Fat.</th>
                                        <th title="Participação semanal do produto">PM Últ. Sem.</th>
                                        <th title="Participação mensal do produto">PM Últ. Mês</th>
                                        <th title="Participação dos últimos 3 meses do produto">PM Últ. 3 meses</th>
                                    </tr>
                                </thead>
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
    function populate(param_1, sale_date = '', department = '') {
        var path = "pricing/tableInfo?true=1";
        if(param_1 != '') path += "&param_1=" + param_1;
        if(sale_date != '') path += "&sale_date=" + sale_date;
        if(department != '') path += "&department=" + department;
        path = encodeURI(path);
        $('#dataTable').DataTable({
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
                // Seta o título da modal
                var title_modal = '';
                if(department == 'medicamento') title_modal = 'Top Medicamentos';
                else if(department == 'perfumaria') title_modal = 'Top Perfumaria';
                else if(department == 'nao medicamento') title_modal = 'Top Não Medicamentos';
                else if(param_1 !== "") title_modal = param_1;
                else title_modal = "Vendidos";
                $('#qualimodal .modal-header > h4').text(title_modal);
                $('#qualimodal .float-right > a').attr("href", json.relatorio_url); // Seta o link de exportação da planilha

                //Plotagem do gráfico de barras
                $('#title_first_chart_qualimodal').text("VMD Últimos 7 dias x Últimos 30 dias");
                if(typeof barChart1 !== 'undefined') barChart1.destroy();
                barChart1 = new Chart(document.getElementById("firstChart").getContext("2d"), {
                  type: 'bar',
                  data: {
                    labels: ["Total", "Curva A", "Curva B", "Curva C"],
                    datasets: [{
                       label: "Aumentou",
                       backgroundColor: "#1cc88a",
                       data: [json.up_total_1, json.up_a_1, json.up_b_1, json.up_c_1]
                    }, {
                       label: "Diminuiu",
                       backgroundColor: "#e74a3b",
                       data: [json.down_total_1, json.down_a_1, json.down_b_1, json.down_c_1]
                    }, {
                       label: "Manteve",
                       backgroundColor: "#858796",
                       data: [json.keep_total_1, json.keep_a_1, json.keep_b_1, json.keep_c_1]
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
                $('#title_second_chart_qualimodal').text("VMD Últimos 30 dias x Últimos 90 dias");
                if(typeof barChart2 !== 'undefined') barChart2.destroy();
                barChart2 = new Chart(document.getElementById("secondChart").getContext("2d"), {
                  type: 'bar',
                  data: {
                    labels: ["Total", "Curva A", "Curva B", "Curva C"],
                    datasets: [{
                       label: "Aumentou",
                       backgroundColor: "#1cc88a",
                       data: [json.up_total_2, json.up_a_2, json.up_b_2, json.up_c_2]
                    }, {
                       label: "Diminuiu",
                       backgroundColor: "#e74a3b",
                       data: [json.down_total_2, json.down_a_2, json.down_b_2, json.down_c_2]
                    }, {
                       label: "Manteve",
                       backgroundColor: "#858796",
                       data: [json.keep_total_2, json.keep_a_2, json.keep_b_2, json.keep_c_2]
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
                $('#loader').hide();
            },
            "bProcessing": true,
            "sAjaxSource": path,
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
                    "mData": 'title'
                },
                {
                    "aTargets": [2],
                    "mData": 'department'
                },
                {
                    "aTargets": [3],
                    "mData": 'category'
                },
                {
                    "aTargets": [4],
                    "mData": 'qtd',
                    "mRender": function ( value, type, full )  {
                        return parseInt(value);
                    }
                },
                {
                    "aTargets": [5],
                    "mData": 'weekly',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        if(value === 0 ) {
                            comp = ' <i class="fas fa-arrow-down text-danger"></i>';
                        }
                        else {
                            if(full.last_month === 0) {
                                comp = ' <i class="fas fa-arrow-down text-danger"></i>';
                            }
                            else {
                                if((value/full.last_month) - 1 < 0) {
                                    comp = ' <i class="fas fa-arrow-down text-danger">' + Math.abs(parseInt(((value/full.last_month) - 1)*100)) + '%</i>';
                                }
                                else {
                                    comp = ' <i class="fas fa-arrow-up text-success">' + Math.abs(parseInt(((value/full.last_month) - 1)*100)) + '%</i>';
                                }
                            }
                        }
                        return (value === 0 ? '-' : parseFloat(value).toFixed(2).replace(".", ",")) + comp
                    }
                },
                {
                    "aTargets": [6],
                    "mData": 'last_month',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        if(value === 0 ) {
                            comp = ' <i class="fas fa-arrow-down text-danger"></i>';
                        }
                        else {
                            if(full.last_3_months === 0) {
                                comp = ' <i class="fas fa-arrow-down text-danger"></i>';
                            }
                            else {
                                if((value/full.last_3_months) - 1 < 0) {
                                    comp = ' <i class="fas fa-arrow-down text-danger">' + Math.abs(parseInt(((value/full.last_3_months) - 1)*100)) + '%</i>';
                                }
                                else {
                                    comp = ' <i class="fas fa-arrow-up text-success">' + Math.abs(parseInt(((value/full.last_3_months) - 1)*100)) + '%</i>';
                                }
                            }
                        }
                        return (value === 0 ? '-' : parseFloat(value).toFixed(2).replace(".", ",")) + comp
                    }
                },
                {
                    "aTargets": [7],
                    "mData": 'last_3_months',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return value === null ? '-' : parseFloat(value).toFixed(2).replace(".", ",");
                    }
                },
                {
                    "aTargets": [8],
                    "mData": 'faturamento',
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [9],
                    "mData": 'pm_weekly',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return (value*100).toFixed(2).replace(".", ",") + "%";
                    }
                },
                {
                    "aTargets": [10],
                    "mData": 'pm_last_month',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return (value*100).toFixed(2).replace(".", ",") + "%";
                    }
                },
                {
                    "aTargets": [11],
                    "mData": 'pm_last_3_months',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return (value*100).toFixed(2).replace(".", ",") + "%";
                    }
                }
            ]
        });
    }
</script>
