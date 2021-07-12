<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="d-flex justify-content-center">
    <div id="loader" class="spinner-grow text-primary" style="width: 6rem; height: 6rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Precificação</h1>
    </div>
    <div class='row'>
        <div class='col-xl-12 col-md-12 mb-4'>
            <div class="alert" id="precify-alert">
                <button type="button" class="close" data-dismiss="alert">x</button>
            </div>
        </div>
        <div class="col-xl-9 col-md-9 mb-4">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file_precify" lang="en">
                <label class="custom-file-label" for="customFileLang" accept="text/plain, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">Selecionar Arquivo</label>
            </div>
        </div>
        <div class="col-xl-3 col-md-3 mb-4">
            <button id="precify" disabled class="btn btn-primary btn-user btn-block">Precificar</button>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    $(document).ready(function(){
        $('#loader').hide();
        $("#precify-alert").hide();

        $('input[type="file"]').change(function(e) {
            var fileName = e.target.files[0].name;
            var extension = e.target.files[0].type;
            $('.custom-file-label').html(fileName);
            if(fileName != "" && (extension == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || extension == "application/vnd.ms-excel" || extension == "csv")) $('#precify').prop('disabled', false);
        });

        $('#precify').click(function(){
            var formdata = new FormData();
            formdata.append('file', $("#file_precify")[0].files[0]);
            $.ajax({
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                url: "precificacao/updateSkus",
                data: formdata,
                beforeSend: function () {
                    $('#loader').show();
                },
                success: function (data) {
                    $('#loader').show();
                    $('#precify-alert').html(data.msg);
                    $('#precify-alert').addClass('alert-' + ((data.success) ? 'success' : 'danger'));
                    $("#precify-alert").fadeTo(2000, 500).slideUp(500, function() {
                        $("#precify-alert").slideUp(500);
                    });
                },
                complete: function () {
                    $('#loader').hide();
                },
            });
        });
    });
</script>

<style>
    div#collapse_9 { z-index: 3 !important; }
    .custom-file-input:lang(en)~.custom-file-label::after { content: "Procurar"; }
    div#loader {
        width: 100px;
      	height: 100px;
      	position: absolute;
      	top:0;
      	bottom: 0;
      	left: 0;
      	right: 0;
        z-index: 100000000000000000000;
      	margin: auto;
    }
</style>
<?=$this->endSection(); ?>
