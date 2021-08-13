<div class="modal" id="modal_share" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Share de Faturamento</h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-sm">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary" id="share_gross_billing_category_title">% de Share por Categoria (Últ. 3 Meses)</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" style="cursor: pointer;" onclick="chartOne('antepenultimo');">Antepenúltimo mês</a>
                                            <a class="dropdown-item" style="cursor: pointer;" onclick="chartOne('penultimo');">Penúltimo mês</a>
                                            <a class="dropdown-item" style="cursor: pointer;" onclick="chartOne('ultimo');">Último mês</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" style="cursor: pointer;" onclick="chartOne('Todos');">Últimos 3 meses</a>
                                            <input type="hidden" id="period">
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-pie pt-4 pb-2">
                                     <canvas id="myPieChartFirst"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary" id="share_gross_billing_department_title">% de Share por Departamentos (Últ. 3 Meses)</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Departamentos</div>
                                            <a class="dropdown-item" style="cursor: pointer;" onclick="chartTwo('Medicamentos');">Medicamentos</a>
                                            <a class="dropdown-item" style="cursor: pointer;" onclick="chartTwo('Perfumarias');">Perfumaria</a>
                                            <a class="dropdown-item" style="cursor: pointer;" onclick="chartTwo('Não-Medicamentos');">Não Medicamentos</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" style="cursor: pointer;" onclick="chartTwo('Departamentos');">Geral</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-pie pt-4 pb-2">
                                     <canvas id="myPieChartSecond"></canvas>
                                </div>
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
    function chartOne(period) {
        setTitle(period);
        var text = $('#share_gross_billing_department_title').text();
        var title = text.split(text.substring(text.lastIndexOf(" (")))[0]
        var department = title.substr(title.lastIndexOf(" ")).trim()
        shareDepartment(period, department);
        $.ajax({
            type: "GET",
            url: "faturamento/getGrossBillingCategory",
            data: { period: period },
            success: function (data) {
                obj = JSON.parse(data);
                if(typeof pieChartOne !== 'undefined') pieChartOne.destroy();
                pieChartOne = new Chart(document.getElementById("myPieChartFirst"), {
                    type: 'pie',
                    data: {
                      labels: obj.labels,
                      datasets: [{
                          backgroundColor: ['#4e73df','#1cc88a','#e74a3b','#36b9cc','#f6c23e','#858796','#f8f9fc','#5a5c69', '#582775', '#e6d7ff', '#533012'],
                          borderWidth: 0,
                          data: obj.data
                        }
                      ]
                    },
                    options: {
                      maintainAspectRatio: false,
                      legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}},
                      tooltips: {
                        callbacks: {
                          label (t, d) {
                            return d.labels[t.index].toLowerCase().charAt(0).toUpperCase() + d.labels[t.index].toLowerCase().slice(1) + ": " + parseFloat(d.datasets[0].data[t.index]).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                          }
                        }
                      },
                    }
                });
            },
        });
    }

    function chartTwo(department) {
        setTitle('', department);
        var period = $("#period").val();
        shareDepartment(period, department);
    }

    function shareDepartment(period, type) {
        $.ajax({
            type: "GET",
            url: "faturamento/getGrossBillingDepto",
            data: { period: period, type: type },
            success: function (data) {
                obj = JSON.parse(data);
                if(typeof pieChartTwo !== 'undefined') pieChartTwo.destroy();
                pieChartTwo = new Chart(document.getElementById("myPieChartSecond"), {
                    type: 'pie',
                    data: {
                      labels: obj.labels,
                      datasets: [{
                          backgroundColor: ['#4e73df','#1cc88a','#e74a3b','#36b9cc','#f6c23e','#858796','#f8f9fc','#5a5c69', '#582775', '#e6d7ff', '#533012'],
                          borderWidth: 0,
                          data: obj.data
                        }
                      ]
                    },
                    options: {
                      maintainAspectRatio: false,
                      legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}},
                      tooltips: {
                        callbacks: {
                          label (t, d) {
                            return d.labels[t.index].toLowerCase().charAt(0).toUpperCase() + d.labels[t.index].toLowerCase().slice(1) + ": " + parseFloat(d.datasets[0].data[t.index]).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                          }
                        }
                      },
                    }
                });
            },
        });
    }

    function setTitle(period = '', department = '') {
        if(period == '' && department != '') {
            var text = $('#share_gross_billing_department_title').text();
            var period = text.substring(text.lastIndexOf("("))
            var title = text.split(text.substring(text.lastIndexOf(" (")))[0]
            var new_title = title.substr(0, title.lastIndexOf(" "))
            $('#share_gross_billing_department_title').text(new_title + " " + department + " " + period);
        }
        if(period != '' && department == '') {
            var comp_period = '';
            var antepenultimo = new Date();
            var penultimo = new Date();
            var ultimo = new Date();
            antepenultimo.setMonth(antepenultimo.getMonth() - 2);
            penultimo.setMonth(penultimo.getMonth() - 1);
            if(period == 'antepenultimo') comp_period = "(" + antepenultimo.toLocaleDateString('pt-br', { month: 'long' }) + ")";
            else if(period == 'penultimo') comp_period = "(" + penultimo.toLocaleDateString('pt-br', { month: 'long' }) + ")";
            else if(period == 'ultimo') comp_period = "(" + ultimo.toLocaleDateString('pt-br', { month: 'long' }) + ")";
            else if(period == 'Todos') comp_period = "(Últ. 3 Meses)";
            var text_first_chart = $('#share_gross_billing_category_title').text();
            var title_first_chart = text_first_chart.substr(0, text_first_chart.lastIndexOf("("))
            var text_second_chart = $('#share_gross_billing_department_title').text();
            var title_second_chart = text_second_chart.substr(0, text_second_chart.lastIndexOf("("))
            $('#share_gross_billing_category_title').text(title_first_chart + comp_period);
            $('#share_gross_billing_department_title').text(title_second_chart + comp_period);
            $("#period").val(period.trim());
        }
    }

    function populateShareGrossBilling() {
        chartOne('Todos');
        chartTwo('Departamentos');
    }
</script>
