<?php echo $this->extend('layouts/default_layout'); ?>
<?php echo $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Simulador de Margem</h1>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4 form-group">
            <select class="form-control" name="department" id="department">
                <option value="">Selecione um Departamento</option>
                <?php foreach($departments as $row):?>
                <option value="<?php echo $row->department;?>"><?php echo ucfirst(strtolower($row->department));?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-xl-3 col-md-6 mb-4 form-group">
            <select class="form-control" name="category" id="category">
                <option value="">Selecione uma Categoria</option>
                <?php foreach($categories_filter as $row):?>
                <option value="<?php echo $row->category;?>"><?php echo ucfirst(strtolower($row->category));?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-xl-3 col-md-6 mb-4 form-group">
            <select class="form-control" name="group" id="group">
                <option value="">Selecione um Grupo</option>
                <option value="termolabil">Termolábil</option>
                <option value="otc">OTC</option>
                <option value="controlados">Controlados</option>
                <option value="pbm">PBM</option>
                <option value="cashback">Cashback</option>
                <option value="home">Home</option>
                <option value="perdendo">Perdendo</option>
                <option value="top">Top Produtos</option>
            </select>
        </div>
        <div class="col-xl-3 col-md-6 mb-4 form-group">
            <select class="form-control" name="curve" id="curve">
                <option value="">Todas as Curvas</option>
                <option value="a">A</option>
                <option value="b">B</option>
                <option value="c">C</option>
            </select>
        </div>
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Margem de (%)</div>
            </div>
            <input type="number" class="form-control form-control-user" min="-100" max="100" name="margin_from" value="5" id="margin_from">
        </div>
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Margem até (%)</div>
            </div>
            <input type="number" class="form-control form-control-user" min="-100" max="100" name="margin_at" value="10" id="margin_at">
        </div>
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Discrepância de (%)</div>
            </div>
            <input type="number" class="form-control form-control-user" min="-100" max="100" name="disc_from" value="5" id="disc_from">
        </div>
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Discrepância até (%)</div>
            </div>
            <input type="number" class="form-control form-control-user" min="-100" max="100" name="disc_at" value="10" id="disc_at">
        </div>
        <div class="col-xl-12 col-md-12 mb-4 form-group">
            <input type="text" class="form-control form-control-user" name="sku" id="sku" placeholder="SKU">
        </div>
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Margem x Discrepância (Filtro)</div>
                            <div class="row no-gutters align-items-center" id="filter">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-dark" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">Todas</div>
                            </div>
                            <div class="row no-gutters align-items-center" id="filter_a">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-primary"></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-primary">Curva A</div>
                            </div>
                            <div class="row no-gutters align-items-center" id="filter_b">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-warning"></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-warning" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-warning">Curva B</div>
                            </div>
                            <div class="row no-gutters align-items-center" id="filter_c">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-danger"></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-danger" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-danger">Curva C</div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Margem x Discrepância (Geral)</div>
                            <div class="row no-gutters align-items-center" id="all_last_months">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?=number_format($margin_all_last_months, 2, ',', '.')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-dark" role="progressbar" style="width: <?=$margin_all_last_months?>%"
                                     aria-valuenow="<?=$margin_all_last_months?>" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">Todas</div>
                            </div>
                            <div class="row no-gutters align-items-center" id="all_last_months_a">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-primary"><?=number_format($margin_all_last_months_a, 2, ',', '.')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-primary" role="progressbar" style="width: <?=$margin_all_last_months_a?>%"
                                     aria-valuenow="<?=$margin_all_last_months_a?>" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-primary">Curva A</div>
                            </div>
                            <div class="row no-gutters align-items-center" id="all_last_months_b">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-warning"><?=number_format($margin_all_last_months_b, 2, ',', '.')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-warning" role="progressbar" style="width: <?=$margin_all_last_months_b?>%"
                                     aria-valuenow="<?=$margin_all_last_months_b?>" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-warning">Curva B</div>
                            </div>
                            <div class="row no-gutters align-items-center" id="all_last_months_c">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-danger"><?=number_format($margin_all_last_months_c, 2, ',', '.')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-danger" role="progressbar" style="width: <?=$margin_all_last_months_c?>%"
                                     aria-valuenow="<?=$margin_all_last_months_c?>" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-danger">Curva C</div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                              Margem Geral
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?=number_format($margin_all, 2, ',', '.')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-dark" role="progressbar" style="width: <?=$margin_all?>%"
                                     aria-valuenow="<?=$margin_all?>" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">Todas</div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-primary"><?=number_format($margin_all_a, 2, ',', '.')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-primary" role="progressbar" style="width: <?=$margin_all_a?>%"
                                     aria-valuenow="<?=$margin_all_a?>" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-primary">Curva A</div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-warning"><?=number_format($margin_all_b, 2, ',', '.')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-warning" role="progressbar" style="width: <?=$margin_all_b?>%"
                                     aria-valuenow="<?=$margin_all_b?>" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-warning">Curva B</div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-danger"><?=number_format($margin_all_c, 2, ',', '.')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-danger" role="progressbar" style="width: <?=$margin_all_c?>%"
                                      aria-valuenow="<?=$margin_all_c?>" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                               </div>
                               <div class="h5 mb-0 mr-3 font-weight-bold text-danger">Curva C</div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display table table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Nome do Produto</th>
                            <th title="VMD dos últimos 7 dias">Últ. Sem.</th>
                            <th title="VMD dos últimos 30 dias">Últ. Mês</th>
                            <th title="VMD dos últimos 90 dias">Últ. 3 meses</th>
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

<?php echo view('modals/modalLogs'); ?>
<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>

<script language='javascript'>
    $(document).ready(function() {
        simulate();
        populate();
    })

    $("#sku, #margin_from, #margin_at, #disc_from, #disc_at").blur(function() {
        simulate();
        populate();
    })

    $("#department, #category, #group, #curve").change(function() {
        simulate();
        populate();
    })

    function populate() {
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
            "searching": false,
            // "initComplete": function( settings, json ) {
            //
            // },
            "bProcessing": true,
            "sAjaxSource": "simulador/tableMarginSimulator?true=1&department=" + $("#department").val() +
                                                                "&category=" + $("#category").val() +
                                                                "&group=" + $("#group").val() +
                                                                "&margin_from=" + $("#margin_from").val() +
                                                                "&margin_at=" + $("#margin_at").val() +
                                                                "&disc_from=" + $("#disc_from").val() +
                                                                "&disc_at=" + $("#disc_at").val() +
                                                                "&disc_at=" + $("#disc_at").val() +
                                                                "&skus=" + $("#skus").val() +
                                                                "&curve=" + $("#curve").val(),
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
                    "mData": 'vmd_weekly',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return (value === 0 ? '-' : parseFloat(value).toFixed(2).replace(".", ","));
                    }
                },
                {
                    "aTargets": [3],
                    "mData": 'vmd_last_month',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return (value === 0 ? '-' : parseFloat(value).toFixed(2).replace(".", ","));
                    }
                },
                {
                    "aTargets": [4],
                    "mData": 'vmd_last_3_months',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return value === null ? '-' : parseFloat(value).toFixed(2).replace(".", ",");
                    }
                },
                {
                    "aTargets": [5],
                    "mData": 'pm_weekly',
                    "bSortable": false,
                },
                {
                    "aTargets": [6],
                    "mData": 'pm_last_month',
                    "bSortable": false,
                },
                {
                    "aTargets": [7],
                    "mData": 'pm_last_3_months',
                    "bSortable": false,
                },
            ]
        });
    }

    function simulate() {
        $.ajax({
            type: "GET",
            url: "simulador/simulate",
            data: {
                department: $('#department').val(),
                category: $('#category').val(),
                group: $('#group').val(),
                curve: $('#curve').val(),
                margin_from: $('#margin_from').val(),
                margin_at: $('#margin_at').val(),
                disc_from: $('#disc_from').val(),
                disc_at: $('#disc_at').val(),
                skus: $('#sku').val()
            },
            success: function (data) {
                if($("#margin_from").val() != "" &&
                   $("#margin_at").val() != "" &&
                   $("#disc_from").val() != "" &&
                   $("#disc_at").val() != "" &&
                   parseInt($("#margin_from").val()) <= parseInt($("#margin_at").val()) &&
                   parseInt($("#disc_from").val()) <= parseInt($("#disc_at").val())) {
                    obj = JSON.parse(data);

                    // Todas as curvas da margem dos filtros
                    $("#filter > div.col-auto > div").empty().append(obj.margin_filter.toFixed(2).replace(".", ",") + "%");
                    $("#filter > div.col > div > div").attr('aria-valuenow', obj.margin_filter).width(obj.margin_filter + "%");

                    // Curva A da margem dos filtros
                    $("#filter_a > div.col-auto > div").empty().append(obj.margin_filter_a.toFixed(2).replace(".", ",") + "%");
                    $("#filter_a > div.col > div > div").attr('aria-valuenow', obj.margin_filter_a).width(obj.margin_filter_a + "%");

                    // Curva B da margem dos filtros
                    $("#filter_b > div.col-auto > div").empty().append(obj.margin_filter_b.toFixed(2).replace(".", ",") + "%");
                    $("#filter_b > div.col > div > div").attr('aria-valuenow', obj.margin_filter_b).width(obj.margin_filter_b + "%");

                    // Curva C da margem dos filtros
                    $("#filter_c > div.col-auto > div").empty().append(obj.margin_filter_c.toFixed(2).replace(".", ",") + "%");
                    $("#filter_c > div.col > div > div").attr('aria-valuenow', obj.margin_filter_c).width(obj.margin_filter_c + "%");
                }
            },
        });
    }
</script>
<?php echo $this->endSection(); ?>
