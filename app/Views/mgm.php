<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório MGM</h1>
    </div>
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Escolha uma data</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdata" id="vdata" placeholder="Data inicial">
        </div>
    </div>
    <div class="row">
        <div class="col"></div>
        <div class="col"></div>
        <div class="col"></div>
    </div>
    <div class="row">
        <div class="" width="90%">
            <br><br>
            <h5><a href="#"><img src='../../../img/update.png' width=24px height=24px alt='Atualizar' id="atualizar" title="Atualizar"></a></h5>
            <div id="showfaturamento" width="88%"></div><br>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    $(document).ready(function(){
        $("#atualizar").click(function(){
           var vdata=new Date();
           $.ajax({
              type: "POST",
              url: "../../../getsalerms.php",
              data: { vdata: vdata},
              success: function(result){
                  html = '<div class="table-responsive">' +
                            '<div class="container" width="100%">' +
                                '<table width=100% border=0>' +
                                    '<tr>' +
                                        '<td width=33%>' +
                                            '<p class="text-center"><b>' + vdata + '(Data Escolhida) </b></p>' +
                                        '</td>' +
                                        '<td width=33%>' +
                                            '<b><p class="text-center">' .$ontem. '(Dia Anterior)</p> </b>' +
                                        '</td>' +
                                        '<td>' +
                                            '<b><p class="text-center">' .$semana. '(Semana Passada) </b></p>' +
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
                                        '<th><font color="black">FAT. X ATUAL</th>' +
                                        '<th style="background-color:black"></th>' +
                                        '<th><font color="black">QTD NF</th>' +
                                        '<th><font color="black">VALOR</th>' +
                                        '<th><font color="black">TKM</th>' +
                                        '<th><font color="black">FAT. X ATUAL</th>' +
                                    '</thead>';
                  Object.keys(obj).forEach((key, index) => {
                      var comp = '';
                      var comp_hj_1d = (($inf['valueDayBefore']/$inf['value'])*100);
                      var comp_hj_1 = number_format(comp_hj_1d,0,",",".");
                      if (is_numeric($comp_hj_1)) {
                          if (comp_hj_1d > 100 && comp_hj_1d < 110){
                              comp = '<td  style="background-color:yellow">' . $comp_hj_1 . '%</td>';
                          }
                          elseif (comp_hj_1d > 110){
                              comp = '<td style="background-color:#ffcccb">' . $comp_hj_1 . '%</td>';
                          }
                          else{
                              comp = '<td>' . $comp_hj_1 . '%</td>';
                          }
                      }
                      else{
                          comp = '<td>#</td>';
                      }
                      html += '<tr>' +
                                  '<th style="background-color:lightgray"><font color="black">' . $hora= $inf['hour'] . '</font></th>' +
                                      '<td>' . $inf['quantity'] . '</td>' +
                                      '<td> R$ ' . number_format($inf['value'],2,",",".") . '</td>' +
                                      '<td> R$ ' . number_format($inf['avgTicket'],2,",",".") . '</td>' +
                                      '<td style="background-color:black"></td>' +
                                      '<td>' . $inf['quantityDayBefore'] . '</td>' +
                                      '<td> R$ ' . number_format($inf['valueDayBefore'],2,",",".") . '</td>' +
                                      '<td> R$ ' . number_format($inf['avgTicketDayBefore'],2,",",".") . '</td>' +
                                      comp;
                  });
                  // $totalqtd=0;
                  // $totalvalue=0;
                  // $totaltkm=0;
                  // $totalqtddb=0;
                  // $totalvaluedb=0;
                  // $totaltkmdb=0;
                  // $totalqtdwa=0;
                  // $totalvaluewa=0;
                  // $totaltkmwa=0;
                  //
                  // foreach ($resposta as $inf){
                  // $totalqtd=$totalqtd + $qtd;
                  // $totalvalue=$totalvalue + $value;
                  // $totaltkm=($totalvalue / $totalqtd);
                  // $totalqtddb=$totalqtddb + $qtddb;
                  // $totalvaluedb=$totalvaluedb + $valuedb;

                  //
                  // echo '<td style="background-color:black"></td>';
                  //
                  // $totaltkmdb=($totalvaluedb / $totalqtddb);
                  // $qtdwa=$inf['quantityWeekAgo'];
                  //
                  // $totalqtdwa=$totalqtdwa + $qtdwa;
                  // echo '<td>' . $qtdwa . '</td>';
                  // $valuewa=$inf['valueWeekAgo'];
                  // $totalvaluewa=$totalvaluewa + $valuewa;
                  // echo '<td> R$ ' . number_format($valuewa,2,",",".") . '</td>';
                  //
                  // $tkmwa=$inf['avgTicketWeekAgo'];
                  // echo '<td> R$ ' . number_format($tkmwa,2,",",".") . "</td>";
                  // //echo '<td>23%</td>';
                  //
                  // $comp_hj_1dwa = (($valuewa/$value)*100);
                  //  $comp_hj_1wa=number_format($comp_hj_1dwa,0,",",".");
                  //
                  // if ($comp_hj_1dwa > 100 && $comp_hj_1dwa < 110){
                  //
                  // echo '<td  style="background-color:yellow">' . $comp_hj_1wa . '%</td>';
                  // }elseif ($comp_hj_1dwa > 110){
                  //
                  // echo '<td style="background-color:#ffcccb">' . $comp_hj_1wa . '%</td>';
                  // }else{
                  // echo '<td>' . $comp_hj_1wa . '%</td>';
                  // }
                  // $totaltkmwa=($totalvaluewa / $totalqtdwa);
                  // echo '</tr>';
                  // }
                  // echo '<tr style="background-color:lightblue;boder:0"><td><font color="black"><b>TOTAL</b></font></td><td><font color="black"><b>'
                  //  .$totalqtd. '</b></font></td><td><font color="black"><b>R$ '. number_format($totalvalue,2,",","."). '</b></font></td><td>
                  // <font color="black"><b>R$ ' . number_format($totaltkm,2,",",".") . '</b></font></td><td style="background-color:black"></td>
                  // <td><font color="black"><b>'
                  //  .$totalqtddb. '</b></font></td><td><font color="black"><b>R$ '. number_format($totalvaluedb,2,",","."). '</b></font></td><td>
                  // <font color="black"><b>R$ ' . number_format($totaltkmdb,2,",",".") . '</b></font></td><td style="background-color:white;border:0;"></td><td style="background-color:black"></td>
                  // <td><font color="black"><b>'
                  //  .$totalqtdwa. '</b></font></td><td><font color="black"><b>R$ '. number_format($totalvaluewa,2,",","."). '</b></font></td><td>
                  // <font color="black"><b>R$ ' . number_format($totaltkmwa,2,",",".") . '</b></font></td><td style="background-color:white;display: none;"></td>
                  // </tr>';
                  // echo '</table>';
                  // echo '</div>';

                  $("#showfaturamento").html(result);
              }
           });
        });

        $("#vdata").change(function(){
           var vdata = $(this).val();
           $.ajax({
              type: "POST",
              url: "../../../getsalerms.php",
              data: { vdata: vdata},
              success: function(result){
                 $("#showfaturamento").html(result);
              }
           });
        });

        $("#buttonmodalfaturamento").click(function(){
            var vdata=new Date();
            $.ajax({
               type: "POST",
               url: "../../../getsalerms.php",
               data: {vdata: vdata},
               success: function(result){
                  $("#showfaturamento").html(result);
               }
            });
        });
    });
</script>
<?=$this->endSection(); ?>
