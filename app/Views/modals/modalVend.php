<div class="modal" id="modal_vend" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class="modal-header">
             <h4>Vendidos</h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
              <div class="card shadow mb-4 d-none d-md-block">
                  <div class="card-body">
                      <div class="table-responsive">
                          <table class="display table table-bordered table-sm table-hover" id="salesDataTable" width="100%" cellspacing="0">
                              <thead class="thead-dark">
                                  <tr>
                                      <th>SKU</th>
                                      <th>Departamento</th>
                                      <th>Categoria</th>
                                      <th title="Quantidade de itens vendidos">Qtd.</th>
                                      <th title="VMD dos últimos 7 dias">Últ. Sem.</th>
                                      <th title="VMD dos últimos 30 dias">Últ. Mês</th>
                                      <th title="VMD dos últimos 90 dias">Últ. 3 meses</th>
                                      <th title="Faturamento do dia">Fat.</th>
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
    function populateDataSales(sale_date) {
        $('#salesDataTable').DataTable({
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
                $('#loader').hide();
            },
            "bProcessing": true,
            "sAjaxSource": "pricing/getSalesProducts?date="+sale_date,
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
                    "mData": 'department'
                },
                {
                    "aTargets": [2],
                    "mData": 'category'
                },
                {
                    "aTargets": [3],
                    "mData": 'qtd',
                    "mRender": function ( value, type, full )  {
                        return parseInt(value);
                    }
                },
                {
                    "aTargets": [4],
                    "mData": 'weekly',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [5],
                    "mData": 'last_month',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [6],
                    "mData": 'last_3_months',
                    "bSortable": false,
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                },
                {
                    "aTargets": [7],
                    "mData": 'faturamento',
                    "mRender": function ( value, type, full )  {
                        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }
                }
            ]
        });
    }
</script>

<style type='text/css'>
    th:first-child, td:first-child {
        position:sticky;
    }
</style>
