<div class="modal" id="modal_fat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
               <h4>Faturamento</h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <br>
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="form-group row">
                                <label for="vdata" class="col-sm-2 col-form-label">Data: </label>
                                <div class="col-sm-10">
                                    <input type="date" id="vdata" name="vdata" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-2 col-form-label">Filial: </label>
                                <div class="col-sm-10">
                                    <select class="form-control form-control-sm" name="vfilial" id="vfilial" >
                                        <option selected value="1007">Selecione a Filial</option>
                                        <option value="1007" selected>1007</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col"></div>
                    </div>
                </div>
                <br>
                <div class="" width="90%">
                    <br><br>
                    <h5><a href="#"><img src='<?php echo base_url('img/update.png'); ?>' width=24px height=24px alt='Atualizar' id="atualizar" title="Atualizar"></a></h5>
                    <div id="showfaturamento" width="88%"></div>
                    <br>
                </div>
      	        <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
 </div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    function getSalesRMS(vfilial, vdata) {
        $.ajax({
            type: "POST",
            url: "pricing/getSalesRMS",
            data: {vfilial: vfilial, vdata: vdata},
            success: function(result){
               $("#showfaturamento").html(result);
            }
        });
    }

    $(document).ready(function(){
        $("#buttonmodalfaturamento, #atualizar").click(function() {
            getSalesRMS($("#vfilial").val(), new Date());
        });

        $("#vdata").change(function() {
            getSalesRMS($("#vfilial").val(), $(this).val());
        });
    });
</script>
