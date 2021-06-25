<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date("Y-m-d");
set_time_limit(0);

include_once("config/dbconfig.php");

function checkmydate($date) {
    $tempDate = explode('-', $date);
    return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
}

if (isset($_POST['vfilial'])) {
    $vfilial = $_POST['vfilial'];
}
else{
    $vfilial = '1007';
}

if (isset($_POST['vdata'])) {
    $vdata = $_POST['vdata'];
}
else{
    $vdata = $hoje;
}
if ($vdata > $hoje){
    $vdata=$hoje;
}

$ontem = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $vdata) ) ));
$semana = date('Y-m-d',(strtotime ( '-7 day' , strtotime ( $vdata) ) ));

// Iniciamos a função do CURL:
$chA = curl_init("http://ultraclinica.totvscloud.com.br:2000/RMS/RMSSERVICES/ReportWebAPI/api/v1/SaleHistory/GetCompareSales?filial=".$vfilial."&dataVendaInicio=".$vdata."&dataVendaFim=".$vdata);
curl_setopt_array($chA, [
    // Equivalente ao -X:
    CURLOPT_CUSTOMREQUEST => 'GET',
    // Equivalente ao -H:
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/vnd.api+json',
        'Accept: application/vnd.api+json'
    ],
    // Permite obter o resultado
    CURLOPT_RETURNTRANSFER => 1,
]);
$resposta1 = curl_exec($chA);
$arrResp1=array();
$arrResp1 = json_decode($resposta1, true); // o teu array criado a partir do json de resposta
$resposta=($arrResp1["items"]);

echo '<div class="table-responsive">';
echo '<div class="container" width="100%">';
echo '<table width=100% border=0><tr><td width=33%><p class="text-center"><b>' .$vdata. '(Data Escolhida) </b></p></td><td width=33%><b><p class="text-center">' .$ontem. '(Dia Anterior)</p> </b></td><td><b><p class="text-center">' .$semana. '(Semana Passada) </b></p></td></tr></table>';
echo '<table border="1" width="100%"  style=" border-collapse: collapse;border-spacing: 0;text-align:center;"  class="table-hover">';
echo '<thead style="background-color:lightgray">';
echo '<th><font color="black">HORA</th><th><font color="black">QTD NF</th><th><font color="black">VALOR</th><th><font color="black">TKM</th><th style="background-color:black";></th><th><font color="black">QTD NF</th><th><font color="black">VALOR</th><th><font color="black">TKM</th><th><font color="black">FAT. X ATUAL</th><th style="background-color:black"></th><th><font color="black">QTD NF</th><th><font color="black">VALOR</th><th><font color="black">TKM</th><th><font color="black">FAT. X ATUAL</th>';
echo '</thead>';
$totalqtd=0;
$totalvalue=0;
$totaltkm=0;
$totalqtddb=0;
$totalvaluedb=0;
$totaltkmdb=0;
$totalqtdwa=0;
$totalvaluewa=0;
$totaltkmwa=0;

foreach ($resposta as $inf){
echo '<tr>';

echo '<th style="background-color:lightgray"><font color="black">' . $hora= $inf['hour'] . '</font></th>';
$qtd=$inf['quantity'];

$totalqtd=$totalqtd + $qtd;
echo '<td>' . $qtd . '</td>';
$value=$inf['value'];
$totalvalue=$totalvalue + $value;
echo '<td> R$ ' . number_format($value,2,",",".") . '</td>';

$tkm=$inf['avgTicket'];
echo '<td> R$ ' . number_format($tkm,2,",",".") . "</td>";
echo '<td style="background-color:black"></td>';

$totaltkm=($totalvalue / $totalqtd);
$qtddb=$inf['quantityDayBefore'];

$totalqtddb=$totalqtddb + $qtddb;
echo '<td>' . $qtddb . '</td>';
$valuedb=$inf['valueDayBefore'];
$totalvaluedb=$totalvaluedb + $valuedb;
echo '<td> R$ ' . number_format($valuedb,2,",",".") . '</td>';

$tkmdb=$inf['avgTicketDayBefore'];
echo '<td> R$ ' . number_format($tkmdb,2,",",".") . "</td>";
$comp_hj_1d = (($valuedb/$value)*100);
 $comp_hj_1=number_format($comp_hj_1d,0,",",".");
if (is_numeric($comp_hj_1)) {
if ($comp_hj_1d > 100 && $comp_hj_1d < 110){
    echo '<td  style="background-color:yellow">' . $comp_hj_1 . '%</td>';
}
elseif ($comp_hj_1d > 110){
    echo '<td style="background-color:#ffcccb">' . $comp_hj_1 . '%</td>';
}
else{
    echo '<td>' . $comp_hj_1 . '%</td>';
}
}else{
echo '<td>#</td>';
}

echo '<td style="background-color:black"></td>';

$totaltkmdb=($totalvaluedb / $totalqtddb);
$qtdwa=$inf['quantityWeekAgo'];

$totalqtdwa=$totalqtdwa + $qtdwa;
echo '<td>' . $qtdwa . '</td>';
$valuewa=$inf['valueWeekAgo'];
$totalvaluewa=$totalvaluewa + $valuewa;
echo '<td> R$ ' . number_format($valuewa,2,",",".") . '</td>';

$tkmwa=$inf['avgTicketWeekAgo'];
echo '<td> R$ ' . number_format($tkmwa,2,",",".") . "</td>";
//echo '<td>23%</td>';

$comp_hj_1dwa = (($valuewa/$value)*100);
 $comp_hj_1wa=number_format($comp_hj_1dwa,0,",",".");

if ($comp_hj_1dwa > 100 && $comp_hj_1dwa < 110){

echo '<td  style="background-color:yellow">' . $comp_hj_1wa . '%</td>';
}elseif ($comp_hj_1dwa > 110){

echo '<td style="background-color:#ffcccb">' . $comp_hj_1wa . '%</td>';
}else{
echo '<td>' . $comp_hj_1wa . '%</td>';
}
$totaltkmwa=($totalvaluewa / $totalqtdwa);
echo '</tr>';
}
echo '<tr style="background-color:lightblue;boder:0"><td><font color="black"><b>TOTAL</b></font></td><td><font color="black"><b>'
 .$totalqtd. '</b></font></td><td><font color="black"><b>R$ '. number_format($totalvalue,2,",","."). '</b></font></td><td>
<font color="black"><b>R$ ' . number_format($totaltkm,2,",",".") . '</b></font></td><td style="background-color:black"></td>
<td><font color="black"><b>'
 .$totalqtddb. '</b></font></td><td><font color="black"><b>R$ '. number_format($totalvaluedb,2,",","."). '</b></font></td><td>
<font color="black"><b>R$ ' . number_format($totaltkmdb,2,",",".") . '</b></font></td><td style="background-color:white;border:0;"></td><td style="background-color:black"></td>
<td><font color="black"><b>'
 .$totalqtdwa. '</b></font></td><td><font color="black"><b>R$ '. number_format($totalvaluewa,2,",","."). '</b></font></td><td>
<font color="black"><b>R$ ' . number_format($totaltkmwa,2,",",".") . '</b></font></td><td style="background-color:white;display: none;"></td>
</tr>';
echo '</table>';
echo '</div>';
curl_close($chA);
?>
