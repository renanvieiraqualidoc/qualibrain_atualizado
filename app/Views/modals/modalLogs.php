<div class="modal" id="modal_logs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Logs de Precificação</h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="tab">
                  <button class="tablinks" onclick="openCity(event, 'original_data')" id="defaultOpen">original_data</button>
                  <button class="tablinks" onclick="openCity(event, 'base_send_data')">base_send_data</button>
                  <button class="tablinks" onclick="openCity(event, 'variant_send_data')">variant_send_data</button>
                  <button class="tablinks" onclick="openCity(event, 'base_result_data')">base_result_data</button>
                  <button class="tablinks" onclick="openCity(event, 'variant_result_data')">variant_result_data</button>
              </div>
              <div id="original_data" class="tabcontent"></div>
              <div id="base_send_data" class="tabcontent"></div>
              <div id="variant_send_data" class="tabcontent"></div>
              <div id="base_result_data" class="tabcontent"></div>
              <div id="variant_result_data" class="tabcontent"></div>
            </div>
        </div>
     </div>
 </div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    $(document).ready(function() {
        // Get the element with id="defaultOpen" and click on it
        document.getElementById("defaultOpen").click();
    })

    function getResponse(code) {
        $.ajax({
            type: "POST",
            url: "logsprecificacao/response",
            data: { code: code },
            success: function (data) {
                obj = JSON.parse(data);
                $('#original_data').empty().append("<pre>" + JSON.stringify(obj.original_data, null, "\t") + "</pre>");
                $('#base_send_data').empty().append("<pre>" + JSON.stringify(obj.base_send_data, null, "\t") + "</pre>");
                $('#variant_send_data').empty().append("<pre>" + JSON.stringify(obj.variant_send_data, null, "\t") + "</pre>");
                $('#base_result_data').empty().append("<pre>" + JSON.stringify(obj.base_result_data, null, "\t") + "</pre>");
                $('#variant_result_data').empty().append("<pre>" + JSON.stringify(obj.variant_result_data, null, "\t") + "</pre>");
            },
        });
    }
</script>

<style type='text/css'>
    /* Style the tab */
    .tab {
        float: left;
        border: 1px solid #4e73df;
        background-color: #6281da;
        width: 30%;
        height: 600px;
    }

    /* Style the buttons inside the tab */
    .tab button {
        display: block;
        background-color: inherit;
        color: white;
        padding: 22px 16px;
        width: 100%;
        border: none;
        outline: none;
        text-align: left;
        cursor: pointer;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #4e73df;
    }

    /* Create an active/current "tab button" class */
    .tab button.active {
        background-color: #2653d4;
    }

    /* Style the tab content */
    .tabcontent {
        float: left;
        padding: 0px 12px;
        border: 1px solid #4e73df;
        width: 70%;
        border-left: none;
        height: 600px;
        overflow-y: scroll;
    }
</style>
