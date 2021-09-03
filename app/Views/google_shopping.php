<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Google Shopping</h1>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="btn btn-info btn-user btn-block" href="googleshopping/xml" target="_blank"><span class="icon text-white-50"><i class="fas fa-file-code"></i></span>  Gerar XML</a>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>

<script language='javascript'>
    $(document).ready(function() {

    })
</script>
<?=$this->endSection(); ?>
