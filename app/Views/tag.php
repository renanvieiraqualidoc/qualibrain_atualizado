<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="d-flex justify-content-center">
    <div id="loader" class="spinner-grow text-primary" style="width: 6rem; height: 6rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tag</h1>
    </div>
    <div class='row'>
        <div class='col-xl-12 col-md-12 mb-4'>
            <div class="alert" id="tag-alert">
                <button type="button" class="close" data-dismiss="alert">x</button>
            </div>
        </div>
        <div class="col-xl-9 col-md-9 mb-4">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file_tag" lang="en">
                <label class="custom-file-label" for="customFileLang" accept="text/plain, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">Selecionar Arquivo</label>
            </div>
        </div>
        <div class="col-xl-3 col-md-3 mb-4">
            <button id="tag" disabled class="btn btn-primary btn-user btn-block">Taguear</button>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    $(document).ready(function(){
        $('#loader').hide();
        $("#tag-alert").hide();

        $('input[type="file"]').change(function(e) {
            var fileName = e.target.files[0].name;
            var extension = e.target.files[0].type;
            $('.custom-file-label').html(fileName);
            if(fileName != "" && (extension == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || extension == "application/vnd.ms-excel" || extension == "csv")) $('#tag').prop('disabled', false);
        });

        $('#tag').click(function(){
            var formdata = new FormData();
            formdata.append('file', $("#file_tag")[0].files[0]);
            $.ajax({
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                url: "tag/updateSkus",
                data: formdata,
                beforeSend: function () {
                    $('#loader').show();
                },
                success: function (data) {
                    obj = JSON.parse(data)
                    $('#loader').show();
                    $('#tag-alert').html(obj.msg);
                    $('#tag-alert').addClass('alert-' + ((obj.success) ? 'success' : 'danger'));
                    $("#tag-alert").fadeTo(2000, 500).slideUp(500, function() {
                        $("#tag-alert").slideUp(500);
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
