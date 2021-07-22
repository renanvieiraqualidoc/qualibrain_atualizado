<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Falteiro Eletrônico</h1>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data Inicial</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdata" id="vdata" value="<?=date('Y-m-d');?>" placeholder="DD/MM/YYYY">
        </div>
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data Final</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdatafinal" id="vdatafinal" value="<?=date('Y-m-d');?>" placeholder="DD/MM/YYYY">
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <input type="text" class="form-control form-control-user" name="sku" id="sku" placeholder="SKU">
        </div>
        <div class="col-xl-3 col-md-6">
            <a class="btn btn-primary btn-user btn-block"><span class="icon text-white-50"><i class="fas fa-fw fa-search"></i></span>  Buscar</a>
        </div>
    </div>
    <div class="row">
        <div class="card-body">
            <div class="table-responsive">
                <table class="display table table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Qtd</th>
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
            "sAjaxSource": "falteiro/getData?sku="+$("#sku").val()+
                            "&initial_date="+$("#vdata").val()+
                            "&final_date="+$("#vdatafinal").val(),
            'serverSide': true,
            "aoColumnDefs":[
                {
                    "aTargets": [0],
                    "mData": 'sku',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return '<a href="#" class="alert-link" data-toggle="modal" data-target="#modal_logs" data-id="' + full.code + '">' + value + '</a>';
                    },
                },
                {
                    "aTargets": [1],
                    "mData": 'qtd',
                    "sortable": false
                }
            ]
        });
    }

    $(document).ready(function(){
        populate();

        $(".btn-primary").click(function(){
            populate();
        });
    });
</script>
<?=$this->endSection(); ?>
