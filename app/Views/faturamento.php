<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Faturamento</h1>
    </div>
    <div class="row">
        <div class="table-responsive" width="100%">
            <table border="1" width="100%" style=" border-collapse: collapse;border-spacing: 0;text-align:center;"  class="table-hover">
                <thead style="background-color:lightgray">
                    <th><font color="black">Mês</th>
                    <th title="Faturamento Bruto"><font color="black">Fat. Bruto</th>
                    <th><font color="black">Pedidos</th>
                    <th title="Ticket Médio"><font color="black">TKM</th>
                    <th title="Comparativo com o Mês Anterior"><font color="black">Mês Ant.</th>
                    <th><font color="black">Margem</th>
                </thead>
                <?php for($i=0; $i<count($months); $i++):?>
                <tr <?=($i == (count($months)-1) ? 'style="background-color:lightblue;"' : '' );?>>
                    <td><?=($i == (count($months)-1) ? 'Proj. ' : $months[$i]['month']);?></td>
                    <td><?=number_to_currency($months[$i]['gross_billing'], 'BRL', null, 0)?></td>
                    <td><?=$months[$i]['qtd_orders']?></td>
                    <td><?=$months[$i]['tkm']?></td>
                    <td><?=number_to_amount($months[$i]['comparative_previous_month'], 2, 'pt_BR')."%"?></td>
                    <td><?=number_to_amount($months[$i]['margin'], 2, 'pt_BR')."%"?></td>
                </tr>
                <?php endfor;?>
            </table>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>

<script language='javascript'>
    $(document).ready(function() {

    })
</script>
<?=$this->endSection(); ?>
