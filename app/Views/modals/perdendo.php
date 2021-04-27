<?php setlocale(LC_MONETARY, 'pt_BR'); ?>
<div id="totalprodutosmodal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
               <h4>Medicamentos</h4> <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
               <div class="container">
                   <br>
                   <div class="row">
                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">
                               Produtos Por Curva e Situação
                               </div>
                           </div>
                       </div>

                       <div class="col-sm">
                           <div class="card mb-4">
                               <div class="card-header">
                               Produtos Por Curva e Situação
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="float-right">
                   <a href="#" class="btn btn-success btn-icon-split">
                       <span class="icon text-white-50">
                           <i class="fas fa-file-excel"></i>
                       </span>
                       <span class="text">Exportar</span>
                   </a>
               </div>
               <br><br>
               <div class="dropdown-divider"></div>
               <br>
               <div class="card shadow mb-4">
                   <div class="card-body">
                       <div class="table-responsive">
                           <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                               <thead>
                                   <tr>
                                       <th>SKU</th>
                                       <th>Título</th>
                                       <th>Departamento</th>
                                       <th>Categoria</th>
                                       <th>Estoque RMS</th>
                                       <th>Conc. Disponíveis</th>
                                       <th>Preço de Custo</th>
                                       <th>Valor de Venda</th>
                                       <th>Menor Preço</th>
                                       <th>Margem %</th>
                                       <th>Discrepancia</th>
                                       <th>Curva</th>
                                       <th>Vendas Acumuladas</th>
                                   </tr>
                               </thead>
                               <tfoot>
                                   <tr>
                                       <th>SKU</th>
                                       <th>Título</th>
                                       <th>Departamento</th>
                                       <th>Categoria</th>
                                       <th>Estoque RMS</th>
                                       <th>Conc. Disponíveis</th>
                                       <th>Preço de Custo</th>
                                       <th>Valor de Venda</th>
                                       <th>Menor Preço</th>
                                       <th>Margem %</th>
                                       <th>Discrepancia</th>
                                       <th>Curva</th>
                                       <th>Vendas Acumuladas</th>
                                   </tr>
                               </tfoot>
                               <tbody>
                                   <?php foreach(json_decode($medicamentos) as $row):?>
                                   <tr>
                                       <td><a target="_blank" href="https://www.qualidoc.com.br/cadastro/product/<?=$row->sku;?>"><?=$row->sku;?></a></td>
                                       <td><?=$row->title;?></td>
                                       <td><?=$row->department;?></td>
                                       <td><?=$row->category;?></td>
                                       <td><?=intval($row->qty_stock_rms);?></td>
                                       <td><?=$row->qty_competitors_available;?></td>
                                       <td><?=number_to_currency($row->price_cost, 'BRL', null, 2);?></td>
                                       <td><?=number_to_currency($row->current_price_pay_only, 'BRL', null, 2);?></td>
                                       <td><?=number_to_currency($row->current_less_price_around, 'BRL', null, 2);?></td>
                                       <td><?=$row->current_gross_margin_percent;?></td>
                                       <td><?=$row->diff_current_pay_only_lowest;?></td>
                                       <td><?=$row->curve;?></td>
                                       <td><?=$row->vendas_acumuladas;?></td>
                                   </tr>
                                   <?php endforeach; ?>
                                   <!-- <tr>
                                       <td>Tiger Nixon</td>
                                       <td>System Architect</td>
                                       <td>Edinburgh</td>
                                       <td>61</td>
                                   </tr>
                                   <tr>
                                       <td>Garrett Winters</td>
                                       <td>Accountant</td>
                                       <td>Tokyo</td>
                                       <td>63</td>
                                   </tr> -->
                               </tbody>
                           </table>
                       </div>
                   </div>
               </div>
            </div>
        </div>
    </div>
</div>
