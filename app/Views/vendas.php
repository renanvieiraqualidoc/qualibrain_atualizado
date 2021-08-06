<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório de Vendas</h1>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data Inicial</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdata" id="vdata" value="<?=date('Y-m-d', strtotime('-7 days'));?>" placeholder="DD/MM/YYYY">
        </div>
        <div class="col-xl-3 col-md-6 mb-4 input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">Data Final</div>
            </div>
            <input type="date" class="form-control form-control-user" name="vdatafinal" id="vdatafinal" value="<?=date('Y-m-d');?>" placeholder="DD/MM/YYYY">
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <select class="form-control" name="department">
                <option value="">Todos os departamentos</option>
                <?php foreach($departments as $row):?>
                <option value="<?php echo $row->department;?>"><?php echo ucfirst(strtolower($row->department));?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <select class="form-control" name="category">
                <option value="">Todas as categorias</option>
                <?php foreach($sales_categories as $row):?>
                <option value="<?php echo $row->category;?>"><?php echo ucfirst(strtolower($row->category));?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <select class="form-control" name="action">
                <option value="">Todas as ações</option>
                <?php foreach($actions as $row):?>
                <option value="<?php echo $row->acao;?>"><?php echo ucfirst(strtolower($row->acao));?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <select class="form-control" name="group">
                <option value="">Todos os grupos</option>
                <option value="termolabil">Termolábil</option>
                <option value="otc">OTC</option>
                <option value="controlados">Controlados</option>
                <option value="pbm">PBM</option>
                <option value="cashback">Cashback</option>
                <option value="home">Home</option>
                <option value="perdendo">Perdendo</option>
            </select>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <select class="form-control" name="sub_category">
                <option value="">Todas as subcategorias</option>
                <?php foreach($sub_categories as $row):?>
                <option value="<?php echo $row->sub_category;?>"><?php echo ucfirst(strtolower($row->sub_category));?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="btn btn-success btn-user btn-block"><span class="icon text-white-50"><i class="fas fa-file-excel"></i></span>  Exportar</a>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    function generateReportLink() {
        $('a.btn-success').attr("href", 'relatorio?type=sales&initial_date=' + $('#vdata').val() +
                                                            '&final_date=' + $('#vdatafinal').val() +
                                                            '&department=' + $("select[name='department']").val() +
                                                            '&category=' + $("select[name='category']").val() +
                                                            '&action=' + $("select[name='action']").val() +
                                                            '&group=' + $("select[name='group']").val() +
                                                            '&sub_category=' + $("select[name='sub_category']").val());
    }

    $(document).ready(function(){
        generateReportLink();

        $("#vdata, #vdatafinal, select[name='department'], select[name='category'], select[name='action'], select[name='group'], select[name='sub_category']").change(function() {
            generateReportLink();
        });
    });
</script>
<?=$this->endSection(); ?>
