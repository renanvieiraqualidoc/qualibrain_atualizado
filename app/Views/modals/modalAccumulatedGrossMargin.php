<div class="modal" id="modal_accumulated_gross_margin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Resumo de Margem Bruta Acumulada</h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display table table-bordered table-sm table-hover" id="dataTableAccumulatedMargin" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Categoria</th>
                                        <th title="Quantidade de Unidades Vendidas">Qtd. Un. Vend.</th>
                                        <th>Receita Bruta</th>
                                        <th>Impostos</th>
                                        <th>Receita Líquida</th>
                                        <th>Custos</th>
                                        <th>Margem Bruta</th>
                                        <th>% Margem Bruta</th>
                                        <th title="Valor Médio por Produto">Valor p/ Produto</th>
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
    function populateMarginGrossBilling() {
        $('#dataTableAccumulatedMargin').DataTable({
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
            "sAjaxSource": "faturamento/getAccumulatedMarginGrossBilling?type=category",
            'serverSide': true,
            "aoColumnDefs":[
                {
                    "aTargets": [0],
                    "mData": 'category',
                    "sortable": false
                },
                {
                    "aTargets": [1],
                    "mData": 'qtd_un_sales',
                    "sortable": false
                },
                {
                    "aTargets": [2],
                    "mData": 'gross_earnings',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    },
                },
                {
                    "aTargets": [3],
                    "mData": 'tax',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    },
                },
                {
                    "aTargets": [4],
                    "mData": 'net_earnings',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    },
                },
                {
                    "aTargets": [5],
                    "mData": 'cost',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    },
                },
                {
                    "aTargets": [6],
                    "mData": 'gross_margin',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    },
                },
                {
                    "aTargets": [7],
                    "mData": 'percent_gross_margin',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return value.toFixed(2).replace(".", ",") + "%";
                    },
                },
                {
                    "aTargets": [8],
                    "mData": 'average_value_per_cost',
                    "sortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    },
                }
            ]
        });
    }
</script>
