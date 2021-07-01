<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório MGM</h1>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data Inicial</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdata" id="vdata">
        </div>
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data Final</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdatafinal" id="vdatafinal">
        </div>
        <div class="col-xl-3 col-md-6 mb-12 float-right">
            <a href="" class="btn btn-success btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-file-excel"></i>
                </span>
                <span class="text">Exportar</span>
            </a>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-xl-12 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data de Análise</div>
            </div>
            <input type="date" class="form-control form-control-user" name="selected_date" value="<?=date('Y-m-d');?>" id="selected_date">
        </div>
    </div>
    <div class="row">
        <div class="col-xl-8 col-md-6 mb-12" width="90%">
            <div id="showfaturamento" width="88%"></div><br>
        </div>
        <div class="col-xl-4 col-md-6 mb-12" width="90%">
            <div id="showranking" width="88%"></div><br>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    $(document).ready(function(){
        $('.float-right > a').attr("href", 'relatorio?type=mgm&initial_date=' + $('#vdata').val() + '&final_date=' + $('#vdatafinal').val());
        populate();

        $("#vdata").change(function(){
            $('.float-right > a').attr("href", 'relatorio?type=mgm&initial_date=' + $('#vdata').val() + '&final_date=' + $('#vdatafinal').val());
        });

        $("#vdatafinal").change(function(){
            $('.float-right > a').attr("href", 'relatorio?type=mgm&initial_date=' + $('#vdata').val() + '&final_date=' + $('#vdatafinal').val());
        });

        $("#selected_date").change(function(){
            populate();
        });

        function populate() {
            $.ajax({
                type: "GET",
                url: "mgm/populateTable",
                data: { selected_date: $('#selected_date').val() },
                success: function(result){
                    var selected_date = new Date($('#selected_date').val());
                    selected_date.setDate(selected_date.getDate() + 1);
                    var last_day = new Date();
                    var actual_time = last_day.getHours();
                    var last_week = new Date();
                    last_day.setDate(selected_date.getDate() - 1);
                    last_week.setDate(selected_date.getDate() - 7);
                    html = '<div class="table-responsive">' +
                               '<div class="container" width="100%">' +
                                   '<table width=100% border=0>' +
                                       '<tr>' +
                                           '<td width=33%>' +
                                               '<p class="text-center"><b>' + selected_date.toLocaleDateString('pt-br') + '(Data Escolhida) </b></p>' +
                                           '</td>' +
                                           '<td width=33%>' +
                                               '<b><p class="text-center">' + last_day.toLocaleDateString('pt-br') + '(Dia Anterior)</p> </b>' +
                                           '</td>' +
                                           '<td>' +
                                               '<b><p class="text-center">' + last_week.toLocaleDateString('pt-br') + '(Semana Passada) </b></p>' +
                                           '</td>' +
                                       '</tr>' +
                                   '</table>' +
                                   '<table border="1" width="100%"  style=" border-collapse: collapse;border-spacing: 0;text-align:center;"  class="table-hover">' +
                                       '<thead style="background-color:lightgray">' +
                                           '<th><font color="black">HORA</th>' +
                                           '<th><font color="black">QTD NF</th>' +
                                           '<th><font color="black">VALOR</th>' +
                                           '<th><font color="black">TKM</th>' +
                                           '<th style="background-color:black";></th>' +
                                           '<th><font color="black">QTD NF</th>' +
                                           '<th><font color="black">VALOR</th>' +
                                           '<th><font color="black">TKM</th>' +
                                           '<th style="background-color:black"></th>' +
                                           '<th><font color="black">QTD NF</th>' +
                                           '<th><font color="black">VALOR</th>' +
                                           '<th><font color="black">TKM</th>' +
                                       '</thead>';
                    obj_sales = JSON.parse(result).sales
                    var total_qtd_today = 0;
                    var total_value_today = 0;
                    var total_tkm_today = 0;
                    var total_qtd_yesterday = 0;
                    var total_value_yesterday = 0;
                    var total_tkm_yesterday = 0;
                    var total_qtd_last_week = 0;
                    var total_value_last_week = 0;
                    var total_tkm_last_week = 0;
                    Object.keys(obj_sales).forEach((key, index) => {
                        html += '<tr>' +
                                     '<th style="background-color:lightgray"><font color="black">' + ((key < 10) ? "0" + key : key) + '</font></th>' +
                                     '<td>' + obj_sales[key].qtd_today + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].value_today).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].tkm_today).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td style="background-color:black"></td>' +
                                     '<td>' + obj_sales[key].qtd_yesterday + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].value_yesterday).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].tkm_yesterday).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td style="background-color:black"></td>' +
                                     '<td>' + obj_sales[key].qtd_last_week + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].value_last_week).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                     '<td>' + parseFloat(obj_sales[key].tkm_last_week).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                '</tr>';
                        total_qtd_today += obj_sales[key].qtd_today;
                        total_value_today += obj_sales[key].value_today;
                        total_tkm_today += obj_sales[key].tkm_today;
                        total_qtd_yesterday = obj_sales[key].qtd_yesterday;
                        total_value_yesterday += obj_sales[key].value_yesterday;
                        total_tkm_yesterday += obj_sales[key].tkm_yesterday;
                        total_qtd_last_week = obj_sales[key].qtd_last_week;
                        total_value_last_week += obj_sales[key].value_last_week;
                        total_tkm_last_week += obj_sales[key].tkm_last_week;
                    });
                    html += '<tr style="background-color:lightblue;border:0">' +
                                '<td><font color="black"><b>TOTAL</b></font></td>' +
                                '<td><font color="black"><b>' + total_qtd_today +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_value_today).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_tkm_today).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td style="background-color:black"></td>' +
                                '<td><font color="black"><b>' + total_qtd_yesterday +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_value_yesterday).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_tkm_yesterday).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td style="background-color:black"></td>' +
                                '<td><font color="black"><b>' + total_qtd_last_week +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_value_last_week).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                                '<td><font color="black"><b>' + parseFloat(total_tkm_last_week).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) +  '</b></font></td>' +
                           '</tr>' +
                        '</table>' +
                    '</div>';
                    $("#showfaturamento").html(html);

                    html = '<div class="table-responsive">' +
                              '<div class="container" width="100%">' +
                                  '<table width=100% border=0>' +
                                      '<tr>' +
                                          '<td>' +
                                              '<b><p class="text-center">Ranking de 10 maiores indicadores</b></p>' +
                                          '</td>' +
                                      '</tr>' +
                                  '</table>' +
                                  '<table border="1" width="100%" style="border-collapse: collapse;border-spacing: 0;text-align:center;"  class="table-hover">' +
                                      '<thead style="background-color:lightgray">' +
                                          '<th><font color="black">Nome do Cliente</th>' +
                                          '<th><font color="black">Indicações</th>' +
                                          '<th style="background-color:black";></th>' +
                                      '</thead>';
                    obj_ranking = JSON.parse(result).ranking
                    Object.keys(obj_ranking).forEach((key, index) => {
                        html += '<tr>' +
                                     '<td>' + obj_ranking[key].indicator_name + '</td>' +
                                     '<td>' + obj_ranking[key].qty_indications + '</td>' +
                                     '<td style="background-color:black"></td>' +
                                '</tr>';
                    });
                    $("#showranking").html(html);
                }
            });
        }
    });
</script>
<?=$this->endSection(); ?>
