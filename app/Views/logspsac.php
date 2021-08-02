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
        <div class="col-xl-4 col-md-12 mb-4 form-group">
            <button type="button" id="btn_search" class="btn btn-primary btn-user btn-block">Buscar</button>
        </div>
    </div>
    <div class="row">
        <div class="card-body">
            <div class="table-responsive">
                <table class="display table table-bordered table-sm table-hover" id="dataTableSAC" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Preço de Venda</th>
                            <th>Cashback</th>
                            <th>Data</th>
                            <th>Drogaraia</th>
                            <th>Onofre</th>
                            <th>Ultrafarma</th>
                            <th>Drogaria SP</th>
                            <th>Pague Menos</th>
                            <th>Beleza na Web</th>
                            <th>Panvel</th>
                            <th>Drogasil</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    $(document).ready(function() {
        // Verifica se a opção selecionada é a customizada, se sim, habilita os campos de data
        $("#period").change(function() {
            if($(this).val() === 'custom') $("#initial_date, #final_date").prop("disabled", false);
            else $("#initial_date, #final_date").prop("disabled", true).val('');
        })

        // Clique no botão de busca
        $("#btn_search").click(function() {
            if($("#sku").val() != '') {
                populate();
            }
            else {
                alert("Digite um SKU para buscar!");
            }
        })
    })

    function populate() {
        $('#dataTableSAC').DataTable({
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
                                                  "&type=sac"+
                                                  "&initial_date="+$("#initial_date").val()+
                                                  "&final_date="+$("#final_date").val()+
                                                  "&sku="+$("#sku").val()+
                                                  "&status="+$("#status").val(),
            'serverSide': true,
            "aoColumnDefs":[
                {
                    "aTargets": [0],
                    "mData": 'sku',
                    "sortable": false,
                },
                {
                    "aTargets": [1],
                    "mData": 'price',
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    },
                    "sortable": false
                },
                {
                    "aTargets": [2],
                    "mData": 'cashback',
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
                    "mData": 'drogaraia',
                    "mRender": function ( value, type, full )  {
                        return (value != '' && value != 0) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                    },
                    "sortable": false
                },
                {
                    "aTargets": [5],
                    "mData": 'onofre',
                    "mRender": function ( value, type, full )  {
                        return (value != '' && value != 0) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                    },
                    "sortable": false
                },
                {
                    "aTargets": [6],
                    "mData": 'ultrafarma',
                    "mRender": function ( value, type, full )  {
                        return (value != '' && value != 0) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                    },
                    "sortable": false
                },
                {
                    "aTargets": [7],
                    "mData": 'drogariasp',
                    "mRender": function ( value, type, full )  {
                        return (value != '' && value != 0) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                    },
                    "sortable": false
                },
                {
                    "aTargets": [8],
                    "mData": 'paguemenos',
                    "mRender": function ( value, type, full )  {
                        return (value != '' && value != 0) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                    },
                    "sortable": false
                },
                {
                    "aTargets": [9],
                    "mData": 'beleza_na_web',
                    "mRender": function ( value, type, full )  {
                        return (value != '' && value != 0) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                    },
                    "sortable": false
                },
                {
                    "aTargets": [10],
                    "mData": 'panvel',
                    "mRender": function ( value, type, full )  {
                        return (value != '' && value != 0) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                    },
                    "sortable": false
                },
                {
                    "aTargets": [11],
                    "mData": 'drogasil',
                    "mRender": function ( value, type, full )  {
                        return (value != '' && value != 0) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                    },
                    "sortable": false
                }
            ]
        });
    }
</script>
<?=$this->endSection(); ?>
