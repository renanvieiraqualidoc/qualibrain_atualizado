<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="modal" id="totalprodutosmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="d-flex justify-content-center">
    <div id="loader" class="spinner-grow text-primary" style="width: 6rem; height: 6rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">PRICING</h1>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Perdendo</div>
                  <div class="h5 mb-0 font-weight-bold text-danger">
                      <font size=3px>
                        <a href="#" class="alert-link" data-toggle="modal" data-target="#totalprodutosmodal" data-id="medicamento"><?=$medicamento;?></a>
                        Medicamentos
                      </font>
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-danger">
                      <font size=3px>
                        <a href="#" class="alert-link" data-toggle="modal" data-target="#totalprodutosmodal" data-id="perfumaria"><?=$perfumaria;?></a>
                        Perfumaria
                      </font>
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-danger">
                      <font size=3px>
                        <a href="#" class="alert-link" data-toggle="modal" data-target="#totalprodutosmodal" data-id="não medicamento"><?=$nao_medicamento;?></a>
                        Não Medicamentos
                      </font>
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-sort-amount-down fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                              Demonstração Financeira
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$estoque?><font size=2px> (estoque)</font></div>
                            <div class="h5 mb-0 font-weight-bold text-primary"><font size=3px><?=$custo?></font><font size=2px> (custo)</font></div>
                            <div class="h5 mb-0 font-weight-bold text-warning"><font size=3px><?=$receita?></font><font size=2px> (receita)</font></div>
                            <div class="h5 mb-0 font-weight-bold text-success"><font size=3px><?=$lucro_bruto?></font><font size=2px> (lucro bruto)</font></div>
                            <div class="h5 mb-0 font-weight-bold text-info"><font size=3px><?=$cashback?></font><font size=2px> (cashback)</font></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Média Margem de Operação</div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?=number_to_amount($margem_bruta_geral, 2, 'pt_BR')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?=$margem_bruta_geral?>;"
                                        aria-valuenow="<?=$margem_bruta_geral?>" aria-valuemin="0" aria-valuemax="100">
                                   </div>
                                 </div>
                               </div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-primary"><?=number_to_amount($margem_bruta_geral_a, 2, 'pt_BR')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?=$margem_bruta_geral_a?>;"
                                        aria-valuenow="<?=$margem_bruta_geral_a?>" aria-valuemin="0" aria-valuemax="100">
                                   </div>
                                 </div>
                               </div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-warning"><?=number_to_amount($margem_bruta_geral_b, 2, 'pt_BR')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?=$margem_bruta_geral_b?>;"
                                        aria-valuenow="<?=$margem_bruta_geral_b?>" aria-valuemin="0" aria-valuemax="100">
                                   </div>
                                 </div>
                               </div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-danger"><?=number_to_amount($margem_bruta_geral_c, 2, 'pt_BR')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?=$margem_bruta_geral_c?>;"
                                        aria-valuenow="<?=$margem_bruta_geral_c?>" aria-valuemin="0" aria-valuemax="100">
                                   </div>
                                 </div>
                               </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Margem Dif. Menor Preço</div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?=number_to_amount($margem_menor_geral, 2, 'pt_BR')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?=$margem_menor_geral?>;"
                                        aria-valuenow="<?=$margem_menor_geral?>" aria-valuemin="0" aria-valuemax="100">
                                   </div>
                                 </div>
                               </div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-primary"><?=number_to_amount($margem_menor_geral_a, 2, 'pt_BR')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?=$margem_menor_geral_a?>;"
                                        aria-valuenow="<?=$margem_menor_geral_a?>" aria-valuemin="0" aria-valuemax="100">
                                   </div>
                                 </div>
                               </div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-warning"><?=number_to_amount($margem_menor_geral_b, 2, 'pt_BR')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?=$margem_menor_geral_b?>;"
                                        aria-valuenow="<?=$margem_menor_geral_b?>" aria-valuemin="0" aria-valuemax="100">
                                   </div>
                                 </div>
                               </div>
                            </div>
                            <div class="row no-gutters align-items-center">
                               <div class="col-auto">
                                 <div class="h5 mb-0 mr-3 font-weight-bold text-danger"><?=number_to_amount($margem_menor_geral_c, 2, 'pt_BR')."%"?></div>
                               </div>
                               <div class="col">
                                 <div class="progress progress-sm mr-2">
                                   <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?=$margem_menor_geral_c?>;"
                                        aria-valuenow="<?=$margem_menor_geral_c?>" aria-valuemin="0" aria-valuemax="100">
                                   </div>
                                 </div>
                               </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-2">
              <div class="card-header py-3"><h7 class="m-0 font-weight-bold text-primary">Competividade por Concorrente</h6></div>
              <div class="card-body">
                  <div class="row">
                      <div class="col-lg-6 mb-3">
                          <h4 class="small font-weight-bold">Drogaraia<span
                                  class="float-right"><?=$losing_drogaraia?>%</span></h4>
                          <div class="progress mb-4">
                              <div class="progress-bar bg-success" role="progressbar" style="width: <?=$losing_drogaraia?>%"
                                  aria-valuenow="<?=$losing_drogaraia?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                      </div>
                      <div class="col-lg-6 mb-3">
                          <h4 class="small font-weight-bold">Beleza na Web<span
                                  class="float-right"><?=$losing_belezanaweb?>%</span></h4>
                          <div class="progress mb-4">
                              <div class="progress-bar bg-light" role="progressbar" style="width: <?=$losing_belezanaweb?>%"
                                  aria-valuenow="<?=$losing_belezanaweb?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-lg-6 mb-3">
                          <h4 class="small font-weight-bold">Drogaria São Paulo<span
                                  class="float-right"><?=$losing_drogariasp?>%</span></h4>
                          <div class="progress mb-4">
                              <div class="progress-bar bg-info" role="progressbar" style="width: <?=$losing_drogariasp?>%"
                                  aria-valuenow="<?=$losing_drogariasp?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                      </div>
                      <div class="col-lg-6 mb-3">
                          <h4 class="small font-weight-bold">Drogasil<span
                                  class="float-right"><?=$losing_drogasil?>%</span></h4>
                          <div class="progress mb-4">
                              <div class="progress-bar bg-danger" role="progressbar" style="width: <?=$losing_drogasil?>%"
                                  aria-valuenow="<?=$losing_drogasil?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-lg-6 mb-2">
                          <h4 class="small font-weight-bold">Onofre<span
                                  class="float-right"><?=$losing_onofre?>%</span></h4>
                          <div class="progress mb-4">
                              <div class="progress-bar bg-primary" role="progressbar" style="width: <?=$losing_onofre?>%"
                                  aria-valuenow="<?=$losing_onofre?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                      </div>
                      <div class="col-lg-6 mb-2">
                          <h4 class="small font-weight-bold">Pague Menos<span
                                  class="float-right"><?=$losing_paguemenos?>%</span></h4>
                          <div class="progress mb-4">
                              <div class="progress-bar bg-warning" role="progressbar" style="width: <?=$losing_paguemenos?>%"
                                  aria-valuenow="<?=$losing_paguemenos?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-lg-6 mb-2">
                          <h4 class="small font-weight-bold">Ultrafarma<span
                                  class="float-right"><?=$losing_ultrafarma?>%</span></h4>
                          <div class="progress mb-4">
                              <div class="progress-bar bg-secondary" role="progressbar" style="width: <?=$losing_ultrafarma?>%"
                                  aria-valuenow="<?=$losing_ultrafarma?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                      </div>
                      <div class="col-lg-6 mb-2">
                          <h4 class="small font-weight-bold">Panvel<span
                                  class="float-right"><?=$losing_panvel?>%</span></h4>
                          <div class="progress">
                              <div class="progress-bar bg-dark" role="progressbar" style="width: <?=$losing_panvel?>%"
                                  aria-valuenow="<?=$losing_panvel?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
        <div class="col-lg-3 mb-0">
            <div class="row">
                <div class="col-sm">
                    <h4 class="m-0 small font-weight-bold text-success">SKU's</h4>
                    <div class="alert alert-success" role="alert">
                        <a href="#" title="Total de SKU's Geral" class="alert-link"
                           data-toggle="modal" data-target="#totalprodutosmodal"><?=$skus?></a>=<a href="#"
                           title="Total de SKU's da Curva A"
                           class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$skus_a?>(A)</a>+<a href="#"
                           title="Total de SKU's da Curva B" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$skus_b?>(B)</a>+<a href="#"
                           title="Total de SKU's da Curva C" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$skus_c?>(C)</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <h4 class="m-0 small font-weight-bold text-warning">Produtos em Ruptura</h4>
                    <div class="alert alert-warning" role="alert">
                        <a href="#" title="Total de SKU's em Ruptura" class="alert-link"
                           data-toggle="modal" data-target="#totalprodutosmodal"><?=$break?></a> = <a href="#"
                           title="Total de SKU's em Ruptura da Curva A"
                           class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$break_a?>(A)</a> + <a href="#"
                           title="Total de SKU's em Ruptura da Curva B" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$break_b?>(B)</a> + <a href="#"
                           title="Total de SKU's em Ruptura da Curva C" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$break_c?>(C)</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <h4 class="m-0 small font-weight-bold text-danger">Produtos Abaixo do Custo</h4>
                    <div class="alert alert-danger" role="alert">
                        <a href="#" title="Total de SKU's Abaixo do Custo" class="alert-link"
                           data-toggle="modal" data-target="#totalprodutosmodal"><?=$under_cost?></a> = <a href="#"
                           title="Total de SKU's Abaixo do Custo da Curva A"
                           class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$under_cost_a?>(A)</a> + <a href="#"
                           title="Total de SKU's Abaixo do Custo da Curva B" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$under_cost_b?>(B)</a> + <a href="#"
                           title="Total de SKU's Abaixo do Custo da Curva C" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$under_cost_c?>(C)</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <h4 class="m-0 small font-weight-bold text-info">Produtos com Estoque Exclusivo</h4>
                    <div class="alert alert-info" role="alert">
                        <a href="#" title="Total de SKU's com Estoque Exclusivo" class="alert-link"
                           data-toggle="modal" data-target="#totalprodutosmodal"><?=$exclusive_stock?></a> = <a href="#"
                           title="Total de SKU's com Estoque Exclusivo da Curva A"
                           class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$exclusive_stock_a?>(A)</a> + <a href="#"
                           title="Total de SKU's com Estoque Exclusivo da Curva B" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$exclusive_stock_b?>(B)</a> + <a href="#"
                           title="Total de SKU's com Estoque Exclusivo da Curva C" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$exclusive_stock_c?>(C)</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <h4 class="m-0 small font-weight-bold text-danger">Produtos que estamos perdendo para todos</h4>
                    <div class="alert alert-danger" role="alert">
                        <a href="#" title="Total de SKU's que estamos perdendo para todos" class="alert-link"
                           data-toggle="modal" data-target="#totalprodutosmodal"><?=$losing_all?></a> = <a href="#"
                           title="Total de SKU's que estamos perdendo para todos da Curva A"
                           class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$losing_all_a?>(A)</a> + <a href="#"
                           title="Total de SKU's que estamos perdendo para todos da Curva B" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$losing_all_b?>(B)</a> + <a href="#"
                           title="Total de SKU's que estamos perdendo para todos da Curva C" class="alert-link" data-toggle="modal"
                           data-target="#totalprodutosmodal"><?=$losing_all_c?>(C)</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-2">
            <div class="card shadow mb-4">
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary" id="margin_title">Margem</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Departamentos</div>
                            <a class="dropdown-item" style="cursor: pointer;" onclick="changeChart('medicamento');">Medicamentos</a>
                            <a class="dropdown-item" style="cursor: pointer;" onclick="changeChart('perfumaria');">Perfumaria</a>
                            <a class="dropdown-item" style="cursor: pointer;" onclick="changeChart('nao_medicamento');">Não Medicamentos</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" style="cursor: pointer;" onclick="changeChart();">Geral</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="pt-2 pb-2">
                        <canvas id="myPieChart_geral" height="240"></canvas>
                        <canvas id="myPieChart_medicamento" style='display:none;' height="240"></canvas>
                        <canvas id="myPieChart_naomedicamento" style='display:none;' height="240"></canvas>
                        <canvas id="myPieChart_perfumaria" style='display:none;' height="240"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2" id="total_sales_value_day"></span>
                        <span class="mr-2" id="total_sales_qtd_day"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-0">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Faturamento X Margem</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<script language='javascript'>
    $(document).ready(function() {
        $('#loader').hide();
        $("#totalprodutosmodal").on('show.bs.modal', function(e) {
            $.ajax({
                type: "POST",
                url: "pricing/competitorInfo",
                data: { department: e.relatedTarget.dataset.id },
                beforeSend: function () {
                    $('#loader').show();
                },
                success: function (data) {
                    $('#loader').show();
                    var object = JSON.parse(data);
                    $("#totalprodutosmodal").empty();
                    var products = '';
                    JSON.parse(object.produtos).forEach(function(item, index) {
                        products += '<tr>' +
                                        '<td><a target="_blank" href="https://www.qualidoc.com.br/cadastro/product/' + item.sku + '">' + item.sku + '</a></td>' +
                                        '<td>' + item.title + '</td>' +
                                        '<td>' + item.department + '</td>' +
                                        '<td>' + item.category + '</td>' +
                                        '<td>' + parseInt(item.qty_stock_rms) + '</td>' +
                                        '<td>' + item.qty_competitors_available + '</td>' +
                                        '<td>' + parseFloat(item.price_cost).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                        '<td>' + parseFloat(item.current_price_pay_only).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                        '<td>' + parseFloat(item.current_less_price_around).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + '</td>' +
                                        '<td>' + item.current_gross_margin_percent + '</td>' +
                                        '<td>' + item.diff_current_pay_only_lowest + '</td>' +
                                        '<td>' + item.curve + '</td>' +
                                    '</tr>';
                    })
                    var html = '<div class="modal-dialog modal-xl">' +
                                  '<div class="modal-content">' +
                                      '<div class="modal-header">' +
                                         '<h4>' + object.title + '</h4> <button type="button" class="close" data-dismiss="modal">&times;</button>' +
                                      '</div>' +
                                      '<div class="modal-body">' +
                                         '<div class="container">' +
                                             '<br>' +
                                             '<div class="row">' +
                                                 '<div class="col-sm">' +
                                                     '<div class="card mb-4">' +
                                                         '<div class="card-header">Competitividade por concorrente</div>' +
                                                         '<div class="chart-pie pt-4 pb-2">' +
                                                              '<canvas id="totalBarChart"></canvas>' +
                                                         '</div>' +
                                                     '</div>' +
                                                 '</div>' +
                                                 '<div class="col-sm">' +
                                                     '<div class="card mb-4">' +
                                                         '<div class="card-header">Produtos Por Categoria</div>' +
                                                         '<div class="chart-pie pt-4 pb-2">' +
                                                              '<canvas id="totalPieChart"></canvas>' +
                                                         '</div>' +
                                                     '</div>' +
                                                 '</div>' +
                                             '</div>' +
                                         '</div>' +
                                         '<div class="float-right">' +
                                             '<a href="' + object.relatorio_url + '" class="btn btn-success btn-icon-split">' +
                                                 '<span class="icon text-white-50">' +
                                                     '<i class="fas fa-file-excel"></i>' +
                                                 '</span>' +
                                                 '<span class="text">Exportar</span>' +
                                             '</a>' +
                                         '</div>' +
                                         '<br><br>' +
                                         '<div class="dropdown-divider"></div>' +
                                         '<br>' +
                                         '<div class="card shadow mb-4 d-none d-md-block">' +
                                             '<div class="card-body">' +
                                                 '<div class="table-responsive">' +
                                                     '<table class="display table table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">' +
                                                         '<thead class="thead-dark">' +
                                                             '<tr>' +
                                                                 '<th>SKU</th>' +
                                                                 '<th>Título</th>' +
                                                                 '<th>Departamento</th>' +
                                                                 '<th>Categoria</th>' +
                                                                 '<th>Estoque</th>' +
                                                                 '<th title="Quantidade de Concorrentes Disponíveis">Conc.</th>' +
                                                                 '<th title="Preço de Custo">Custo</th>' +
                                                                 '<th title="Valor de Venda">Venda</th>' +
                                                                 '<th title="Menor Preço">Menor</th>' +
                                                                 '<th title="Margem %">Margem</th>' +
                                                                 '<th title="Discrepância">Disc.</th>' +
                                                                 '<th>Curva</th>' +
                                                             '</tr>' +
                                                         '</thead>' +
                                                         '<tfoot class="thead-dark">' +
                                                             '<tr>' +
                                                                 '<th>SKU</th>' +
                                                                 '<th>Título</th>' +
                                                                 '<th>Departamento</th>' +
                                                                 '<th>Categoria</th>' +
                                                                 '<th>Estoque</th>' +
                                                                 '<th title="Quantidade de Concorrentes Disponíveis">Conc.</th>' +
                                                                 '<th title="Preço de Custo">Custo</th>' +
                                                                 '<th title="Valor de Venda">Venda</th>' +
                                                                 '<th title="Menor Preço">Menor</th>' +
                                                                 '<th title="Margem %">Margem</th>' +
                                                                 '<th title="Discrepância">Disc.</th>' +
                                                                 '<th>Curva</th>' +
                                                             '</tr>' +
                                                         '</tfoot>' +
                                                         '<tbody>' + products + '</tbody>' +
                                                     '</table>' +
                                                 '</div>' +
                                             '</div>' +
                                         '</div>' +
                                      '</div>' +
                                  '</div>' +
                               '</div>';
                    $("#totalprodutosmodal").append(html);

                    // Plotagem do gráfico de barras
                    new Chart(document.getElementById("totalBarChart").getContext("2d"), {
                      type: 'bar',
                      data: {
                        labels: ["Concorrentes"],
                        datasets: [{
                           label: "Onofre",
                           backgroundColor: "#4e73df",
                           data: [object.onofre]
                        }, {
                           label: "Drogaraia",
                           backgroundColor: "#1cc88a",
                           data: [object.drogaraia]
                        }, {
                           label: "Drogaria SP",
                           backgroundColor: "#36b9cc",
                           data: [object.drogariasaopaulo]
                        }, {
                           label: "Pague Menos",
                           backgroundColor: "#f6c23e",
                           data: [object.paguemenos]
                        }, {
                           label: "Drogasil",
                           backgroundColor: "#e74a3b",
                           data: [object.drogasil]
                        }, {
                           label: "Ultrafarma",
                           backgroundColor: "#858796",
                           data: [object.ultrafarma]
                        }, {
                           label: "Beleza na Web",
                           backgroundColor: "#f8f9fc",
                           data: [object.belezanaweb]
                        }, {
                           label: "Panvel",
                           backgroundColor: "#5a5c69",
                           data: [object.panvel]
                        }]
                      },
                      options: {
                        barValueSpacing: 6,
                        scales: {
                          yAxes: [{
                            ticks: {
                              min: 0,

                            }
                          }]
                        }
                      }
                    });

                    // Plotagem do gráfico circular
                    new Chart(document.getElementById("totalPieChart"), {
                        type: 'pie',
                        data: {
                          labels: object.products_categories,
                          datasets: [{
                              backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#f8f9fc','#5a5c69'],
                              borderWidth: 0,
                              data: object.count_categories
                            }
                          ]
                        },
                        options: {
                          cutoutPercentage: 85,
                          legend: {position:'bottom', padding:5, labels: {pointStyle:'circle', usePointStyle:true}}
                        }
                    });

                    $('#dataTable').DataTable({
                        language: {
                            info: "Mostrando página _PAGE_ de _PAGES_",
                            infoEmpty: "Nenhum registro",
                            infoFiltered: "(filtrado de _MAX_ registros)",
                            infoPostFix: "",
                            thousands: ".",
                            decimal: "",
                            emptyTable: "Não existem registros",
                            lengthMenu: "", //"_MENU_ registros por página",
                            loadingRecords: "Carregando...",
                            processing: "Processando...",
                            search: "Buscar por:",
                            zeroRecords: "Não foi encontrado nenhum registro",
                            paginate: {
                                first: "Primeira",
                                last: "Última",
                                next: "Próxima",
                                previous: "Anterior"
                            },
                            aria: {
                                sortAscending:  ": ativado para ordenar por ordem crescente",
                                sortDescending: ": ativado para ordenar por ordem decrescente"
                            }
                        }
                    });

                    $('#loader').hide();
                },
                complete: function () {
                    $('#loader').hide();
                },
            });
        })

        var myPieChart;
        var data = []
        var labels = []
        var margin_department = []
        var ctx;
        changeChart();
        chartMargin();
    })

    function chartMargin() {
        var ctx = document.getElementById("myAreaChart");
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
              label: "Earnings",
              lineTension: 0.3,
              backgroundColor: "rgba(78, 115, 223, 0.05)",
              borderColor: "rgba(78, 115, 223, 1)",
              pointRadius: 3,
              pointBackgroundColor: "rgba(78, 115, 223, 1)",
              pointBorderColor: "rgba(78, 115, 223, 1)",
              pointHoverRadius: 3,
              pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
              pointHoverBorderColor: "rgba(78, 115, 223, 1)",
              pointHitRadius: 10,
              pointBorderWidth: 2,
              data: [0, 10000, 5000, 15000, 10000, 20000, 15000, 25000, 20000, 30000, 25000, 40000],
            }],
          },
          options: {
            maintainAspectRatio: false,
            layout: {
              padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
              }
            },
            scales: {
              xAxes: [{
                time: {
                  unit: 'date'
                },
                gridLines: {
                  display: false,
                  drawBorder: false
                },
                ticks: {
                  maxTicksLimit: 7
                }
              }],
              yAxes: [{
                ticks: {
                  maxTicksLimit: 5,
                  padding: 10,
                  // Include a dollar sign in the ticks
                  callback: function(value, index, values) {
                    return '$' + value;
                  }
                },
                gridLines: {
                  color: "rgb(234, 236, 244)",
                  zeroLineColor: "rgb(234, 236, 244)",
                  drawBorder: false,
                  borderDash: [2],
                  zeroLineBorderDash: [2]
                }
              }],
            },
            legend: {
              display: false
            },
            tooltips: {
              backgroundColor: "rgb(255,255,255)",
              bodyFontColor: "#858796",
              titleMarginBottom: 10,
              titleFontColor: '#6e707e',
              titleFontSize: 14,
              borderColor: '#dddfeb',
              borderWidth: 1,
              xPadding: 15,
              yPadding: 15,
              displayColors: false,
              intersect: false,
              mode: 'index',
              caretPadding: 10,
              callbacks: {
                label: function(tooltipItem, chart) {
                  var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                  return datasetLabel + ': $' + tooltipItem.yLabel;
                }
              }
            }
          }
        });
    }

    function changeChart(margin_view = '') {
        if(margin_view == 'medicamento') {
            $('#margin_title').text('Margem (Medicamentos)');
            $('#total_sales_value_day').text('Fat.: <?=$medicamento_margins['total_sales_value_day']?>');
            $('#total_sales_qtd_day').text('Vendidos.: <?=$medicamento_margins['total_sales_qtd_day']?>');
            margin_department = '<?=$medicamento_margins['total_margin_day']?>';
            data = <?='['.implode(',', $medicamento_margins['data']).']';?>;
            labels = <?='["'.implode('","', $medicamento_margins['labels']).'"]';?>;
            ctx = document.getElementById("myPieChart_medicamento");
            $('#myPieChart_medicamento').show();
            $('#myPieChart_naomedicamento').hide();
            $('#myPieChart_perfumaria').hide();
            $('#myPieChart_geral').hide();
        }
        else if(margin_view == 'nao_medicamento') {
            $('#margin_title').text('Margem (Não Medicamentos)');
            $('#total_sales_value_day').text('Fat.: <?=$nao_medicamento_margins['total_sales_value_day']?>');
            $('#total_sales_qtd_day').text('Vendidos.: <?=$nao_medicamento_margins['total_sales_qtd_day']?>');
            margin_department = '<?=$nao_medicamento_margins['total_margin_day']?>';
            data = <?='['.implode(',', $nao_medicamento_margins['data']).']';?>;
            labels = <?='["'.implode('","', $nao_medicamento_margins['labels']).'"]';?>;
            ctx = document.getElementById("myPieChart_naomedicamento");
            $('#myPieChart_medicamento').hide();
            $('#myPieChart_naomedicamento').show();
            $('#myPieChart_perfumaria').hide();
            $('#myPieChart_geral').hide();
        }
        else if(margin_view == 'perfumaria') {
            $('#margin_title').text('Margem (Perfumaria)');
            $('#total_sales_value_day').text('Fat.: <?=$perfumaria_margins['total_sales_value_day']?>');
            $('#total_sales_qtd_day').text('Vendidos.: <?=$perfumaria_margins['total_sales_qtd_day']?>');
            margin_department = '<?=$perfumaria_margins['total_margin_day']?>';
            data = <?='['.implode(',', $perfumaria_margins['data']).']';?>;
            labels = <?='["'.implode('","', $perfumaria_margins['labels']).'"]';?>;
            ctx = document.getElementById("myPieChart_perfumaria");
            $('#myPieChart_medicamento').hide();
            $('#myPieChart_naomedicamento').hide();
            $('#myPieChart_perfumaria').show();
            $('#myPieChart_geral').hide();
        }
        else {
            $('#margin_title').text('Margem (Geral)');
            $('#total_sales_value_day').text('Fat.: <?=$geral_margins['total_sales_value_day']?>');
            $('#total_sales_qtd_day').text('Vendidos.: <?=$geral_margins['total_sales_qtd_day']?>');
            margin_department = '<?=$geral_margins['total_margin_day']?>';
            data = <?='['.implode(',', $geral_margins['data']).']';?>;
            labels = <?='["'.implode('","', $geral_margins['labels']).'"]';?>;
            ctx = document.getElementById("myPieChart_geral");
            $('#myPieChart_medicamento').hide();
            $('#myPieChart_naomedicamento').hide();
            $('#myPieChart_perfumaria').hide();
            $('#myPieChart_geral').show();
        }

        Chart.pluginService.register({
          beforeDraw: function(chart) {
            if (chart.config.options.elements.center) {
              // Get ctx from string
              var ctx = chart.chart.ctx;

              // Get options from the center object in options
              var centerConfig = chart.config.options.elements.center;
              var fontStyle = centerConfig.fontStyle || 'Arial';
              var txt = centerConfig.text;
              var color = centerConfig.color || '#000';
              var maxFontSize = centerConfig.maxFontSize || 75;
              var sidePadding = centerConfig.sidePadding || 20;
              var sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2)
              // Start with a base font of 30px
              ctx.font = "30px " + fontStyle;

              // Get the width of the string and also the width of the element minus 10 to give it 5px side padding
              var stringWidth = ctx.measureText(txt).width;
              var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

              // Find out how much the font can grow in width.
              var widthRatio = elementWidth / stringWidth;
              var newFontSize = Math.floor(30 * widthRatio);
              var elementHeight = (chart.innerRadius * 2);

              // Pick a new font size so it will not be larger than the height of label.
              var fontSizeToUse = Math.min(newFontSize, elementHeight, maxFontSize);
              var minFontSize = centerConfig.minFontSize;
              var lineHeight = centerConfig.lineHeight || 25;
              var wrapText = false;

              if (minFontSize === undefined) {
                minFontSize = 20;
              }

              if (minFontSize && fontSizeToUse < minFontSize) {
                fontSizeToUse = minFontSize;
                wrapText = true;
              }

              // Set font settings to draw it correctly.
              ctx.textAlign = 'center';
              ctx.textBaseline = 'middle';
              var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
              var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
              ctx.font = fontSizeToUse + "px " + fontStyle;
              ctx.fillStyle = color;

              if (!wrapText) {
                ctx.fillText(txt, centerX, centerY);
                return;
              }

              var words = txt.split(' ');
              var line = '';
              var lines = [];

              // Break words up into multiple lines if necessary
              for (var n = 0; n < words.length; n++) {
                var testLine = line + words[n] + ' ';
                var metrics = ctx.measureText(testLine);
                var testWidth = metrics.width;
                if (testWidth > elementWidth && n > 0) {
                  lines.push(line);
                  line = words[n] + ' ';
                } else {
                  line = testLine;
                }
              }

              // Move the center up depending on line height and number of lines
              centerY -= (lines.length / 2) * lineHeight;

              for (var n = 0; n < lines.length; n++) {
                ctx.fillText(lines[n], centerX, centerY);
                centerY += lineHeight;
              }
              //Draw text in center
              ctx.fillText(line, centerX, centerY);
            }
          }
        });
        myPieChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: labels,
            datasets: [{
              data: data,
              backgroundColor: ["#4e73df", "#1cc88a", "#36b9cc", "#f6c23e", "#e74a3b", "#858796", "#f8f9fc", "#5a5c69"],
              hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
          },
          options: {
            elements: {
              center: {
                text: margin_department,
                fontStyle: 'Arial', // Default is Arial
                sidePadding: 20, // Default is 20 (as a percentage)
                minFontSize: 25, // Default is 20 (in px), set to false and text will not wrap.
                lineHeight: 25 // Default is 25 (in px), used for when text wraps
              }
            },
            maintainAspectRatio: false,
            tooltips: {
              backgroundColor: "rgb(255,255,255)",
              bodyFontColor: "#858796",
              borderColor: '#dddfeb',
              borderWidth: 1,
              xPadding: 15,
              yPadding: 15,
              displayColors: false,
              caretPadding: 10,
              callbacks: {
                label (t, d) {
                  var value = d.datasets[0].data[t.index].toFixed(2).replace(".", ",") + "%";
                  var lowercase_label = d.labels[t.index].toLowerCase()
                  var label = lowercase_label.charAt(0).toUpperCase() + lowercase_label.slice(1);
                  return label + ": " + value;
                }
              }
            },
            legend: {
              display: false
            },
            cutoutPercentage: 80,
          },
        });
    }
</script>
<style type='text/css'>
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
