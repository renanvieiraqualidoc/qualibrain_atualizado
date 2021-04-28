// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable_medicamento,#dataTable_perfumaria,#dataTable_nao_medicamento').DataTable({
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
      }
  });
});
