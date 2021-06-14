<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Logs de Precificação</h1>
    </div>
    <div class="row">
        <div class="col-xl-2 col-md-6 mb-4 form-group">
            <input type="text" class="form-control form-control-user" name="sku" id="sku" placeholder="SKU">
        </div>
        <div class="col-xl-2 col-md-6 mb-4 form-group">
            <select class="form-control" name="period" id="period">
                <option value="last_4_hours">Últimas 4 horas</option>
                <option value="last_day">Último dia</option>
                <option value="last_7_days">Últimos 7 dias</option>
                <option value="last_15_days">Últimos 15 dias</option>
                <option value="last_30_days">Últimos 30 dias</option>
                <option value="custom">Custom</option>
            </select>
        </div>
        <div class="col-xl-2 col-md-6 mb-4 form-group">
            <input type="date" class="form-control form-control-user" disabled name="initial_date" id="initial_date" placeholder="Data inicial">
        </div>
        <div class="col-xl-2 col-md-6 mb-4 form-group">
            <input type="date" class="form-control form-control-user" disabled name="final_date" id="final_date" placeholder="Data final">
        </div>
        <div class="col-xl-2 col-md-6 mb-4 form-group">
            <select class="form-control" name="status" id="status">
                <option value="">Escolha um status</option>
            </select>
        </div>
        <div class="col-xl-2 col-md-6 mb-4 form-group">
            <button type="button" id="btn_search" class="btn btn-primary btn-user btn-block">Buscar</button>
        </div>
    </div>
    <div class="row">
        <div class="card-body">
            <div class="table-responsive">
                <table class="display table table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Projeto</th>
                            <th>SKU</th>
                            <th>Preço</th>
                            <th>Data</th>
                            <th>Status</th>
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
        populate();

        // Verifica se a opção selecionada é a customizada, se sim, habilita os campos de data
        $("#period").change(function() {
            if($(this).val() === 'custom') $("#initial_date, #final_date").prop("disabled", false);
            else $("#initial_date, #final_date").prop("disabled", true).val('');
        })

        // Clique na linha da tabela
        $('#dataTable tbody').on('click', 'tr', function () {
            getResponse($('#dataTable').DataTable().row(this).data().code);
        });

        // Clique no botão de busca
        $("#btn_search").click(function() {
            populate();
        })
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
                search: "Buscar sku:",
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
            "searching": false,
            "bProcessing": true,
            destroy: true,
            fixedColumns: true,
            "sAjaxSource": "logsprecificacao/search?period="+$("#period").val()+
                                                  "&initial_date="+$("#initial_date").val()+
                                                  "&final_date="+$("#final_date").val()+
                                                  "&sku="+$("#sku").val()+
                                                  "&status="+$("#status").val(),
            'serverSide': true,
            "aoColumnDefs":[
                {
                    "aTargets": [0],
                    "mData": 'origin',
                    "sortable": false,
                },
                {
                    "aTargets": [1],
                    "mData": 'sku',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return '<a href="#" class="alert-link" data-toggle="modal" data-target="#modal_logs" data-id="' + full.code + '">' + value + '</a>';
                    },
                },
                {
                    "aTargets": [2],
                    "mData": 'price',
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    },
                    "sortable": false
                },
                {
                    "aTargets": [3],
                    "mData": 'created_at',
                    "sortable": false
                },
                {
                    "aTargets": [4],
                    "mData": 'status',
                    "sortable": false
                }
            ]
        });
    }
</script>
<?=$this->endSection(); ?>
