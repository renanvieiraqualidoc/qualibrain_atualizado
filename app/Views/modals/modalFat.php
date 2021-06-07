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
 <div class="col">
    </div>
    <div class="col">

    </div>
    <div class="col">

    </div>
  </div>
</div>






<br>


<div class="" width="90%">



<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
 $(document).ready(function(){ /* PREPARE THE SCRIPT */
    $("#vfilial").change(function(){ /* WHEN YOU CHANGE AND SELECT FROM THE SELECT FIELD */
      var vfilial = $(this).val(); /* GET THE VALUE OF THE SELECTED DATA */
     // var dataString = vfilial; /* STORE THAT TO A DATA STRING */
var dataString = vfilial;
//var vdata='2021-04-07';
var vdata=$("#vdata").val();
     $.ajax({ /* THEN THE AJAX CALL */
        type: "POST", /* TYPE OF METHOD TO USE TO PASS THE DATA */
        url: "../../../getsalerms.php", /* PAGE WHERE WE WILL PASS THE DATA */
        data: {vfilial: vfilial, vdata: vdata},
 /* THE DATA WE WILL BE PASSING */
        success: function(result){ /* GET THE TO BE RETURNED DATA */
          $("#showfaturamento").html(result); /* THE RETURNED DATA WILL BE SHOWN IN THIS DIV */
        }
      });

    });
  });
</script>



<script type="text/javascript">
  $(document).ready(function(){ /* PREPARE THE SCRIPT */
 $("#vdata").change(function(){ /* WHEN YOU CHANGE AND SELECT FROM THE SELECT FIELD */

      var vdata = $(this).val(); /* GET THE VALUE OF THE SELECTED DATA */
     // var dataString = vfilial; /* STORE THAT TO A DATA STRING */
var dataString = vfilial;
//var vdata='2021-04-07';
var vfilial=$("#vfilial").val();
     $.ajax({ /* THEN THE AJAX CALL */
        type: "POST", /* TYPE OF METHOD TO USE TO PASS THE DATA */
        url: "../../../getsalerms.php", /* PAGE WHERE WE WILL PASS THE DATA */
        data: {vfilial: vfilial, vdata: vdata},
 /* THE DATA WE WILL BE PASSING */
        success: function(result){ /* GET THE TO BE RETURNED DATA */
          $("#showfaturamento").html(result); /* THE RETURNED DATA WILL BE SHOWN IN THIS DIV */
        }
      });

    });
  });
</script>

<script type="text/javascript">
  $(document).ready(function(){ /* PREPARE THE SCRIPT */
    $("#buttonmodalfaturamento").click(function(){ /* WHEN YOU CHANGE AND SELECT FROM THE SELECT FIELD */

var dataString = vfilial;
var vdata=new Date();

var vfilial=$("#vfilial").val();
     $.ajax({ /* THEN THE AJAX CALL */
        type: "POST", /* TYPE OF METHOD TO USE TO PASS THE DATA */
        url: "../../../getsalerms.php", /* PAGE WHERE WE WILL PASS THE DATA */
        data: {vfilial: vfilial, vdata: vdata},
 /* THE DATA WE WILL BE PASSING */
        success: function(result){ /* GET THE TO BE RETURNED DATA */
          $("#showfaturamento").html(result); /* THE RETURNED DATA WILL BE SHOWN IN THIS DIV */
        }
      });

    });
  });
</script>

<script type="text/javascript">
  $(document).ready(function(){ /* PREPARE THE SCRIPT */
    $("#atualizar").click(function(){ /* WHEN YOU CHANGE AND SELECT FROM THE SELECT FIELD */

var dataString = vfilial;
var vdata=new Date();

var vfilial=$("#vfilial").val();
     $.ajax({ /* THEN THE AJAX CALL */
        type: "POST", /* TYPE OF METHOD TO USE TO PASS THE DATA */
        url: "../../../getsalerms.php", /* PAGE WHERE WE WILL PASS THE DATA */
        data: {vfilial: vfilial, vdata: vdata},
 /* THE DATA WE WILL BE PASSING */
        success: function(result){ /* GET THE TO BE RETURNED DATA */
          $("#showfaturamento").html(result); /* THE RETURNED DATA WILL BE SHOWN IN THIS DIV */
        }
      });

    });
  });
</script>



<br>

<br>
<h5><a href="#"><img src='../../../img/update.png' width=24px height=24px alt='Atualizar' id="atualizar" title="Atualizar"></a>
</h5>
<div id="showfaturamento" width="88%">
  <!-- ITEMS TO BE DISPLAYED HERE -->
</div>
<br>
</div>
      	<div class="modal-footer">

          <button class="btn btn-secondary" type="button" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>


            </div>
        </div>
     </div>
 </div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
  
</script>
