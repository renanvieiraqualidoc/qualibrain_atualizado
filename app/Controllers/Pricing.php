<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class Pricing extends BaseController
{

	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de pricing
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			$model = new ProductsModel();

			// Cria os cards 'Perdendo', 'Demonstração financeira' e de margens
			$this->modalPerdendo('medicamento', $model);
			$this->modalPerdendo('perfumaria', $model);
			$this->modalPerdendo('não medicamento', $model);
			$data['estoque'] = number_to_amount($model->getTotalStockRMS(), 2, 'pt_BR');
			$total_price_cost = $model->getTotalPriceCost();
			$total_price_pay_only = $model->getTotalPricePayOnly();
			$data['custo'] = number_to_currency($total_price_cost, 'BRL', null, 2);
			$data['receita'] = number_to_currency($total_price_pay_only, 'BRL', null, 2);
			$data['lucro_bruto'] = number_to_currency($total_price_pay_only - $total_price_cost, 'BRL', null, 2);
			$data['cashback'] = number_to_currency($model->getTotalCashback(), 'BRL', null, 2);
			$data['margem_bruta_geral'] = $model->getAvgGrossMargin()*100;
			$data['margem_bruta_geral_a'] = $model->getAvgGrossMargin('A')*100;
			$data['margem_bruta_geral_b'] = $model->getAvgGrossMargin('B')*100;
			$data['margem_bruta_geral_c'] = $model->getAvgGrossMargin('C')*100;
			$data['margem_menor_geral'] = $model->getAvgDiffMargin()*100;
			$data['margem_menor_geral_a'] = $model->getAvgDiffMargin('A')*100;
			$data['margem_menor_geral_b'] = $model->getAvgDiffMargin('B')*100;
			$data['margem_menor_geral_c'] = $model->getAvgDiffMargin('C')*100;

			// Cria o gráfico percentual de concorrentes
			$drogaraia = $model->getQuantityProductsLosingDrogaraia();
			$belezanaweb = $model->getQuantityProductsLosingBelezanaweb();
			$drogariasp = $model->getQuantityProductsLosingDrogariasp();
			$drogasil = $model->getQuantityProductsLosingDrogasil();
			$onofre = $model->getQuantityProductsLosingOnofre();
			$paguemenos = $model->getQuantityProductsLosingPaguemenos();
			$ultrafarma = $model->getQuantityProductsLosingUltrafarma();
			$panvel = $model->getQuantityProductsLosingPanvel();
			$total = $drogaraia + $belezanaweb + $drogariasp + $drogasil + $onofre + $paguemenos + $ultrafarma + $panvel;
			$data['losing_drogaraia'] = round(($drogaraia/$total)*100);
			$data['losing_belezanaweb'] = round(($belezanaweb/$total)*100);
			$data['losing_drogariasp'] = round(($drogariasp/$total)*100);
			$data['losing_drogasil'] = round(($drogasil/$total)*100);
			$data['losing_onofre'] = round(($onofre/$total)*100);
			$data['losing_paguemenos'] = round(($paguemenos/$total)*100);
			$data['losing_ultrafarma'] = round(($ultrafarma/$total)*100);
			$data['losing_panvel'] = round(($panvel/$total)*100);

			// Dados de skus, rupturas, produtos abaixo do custo e estoques exclusivos
			$data['skus'] = $model->getTotalSkus();
			$data['skus_a'] = $model->getTotalSkus('A');
			$data['skus_b'] = $model->getTotalSkus('B');
			$data['skus_c'] = $model->getTotalSkus('C');
			$data['break'] = $model->getTotalBreak();
			$data['break_a'] = $model->getTotalBreak('A');
			$data['break_b'] = $model->getTotalBreak('B');
			$data['break_c'] = $model->getTotalBreak('C');
			$data['under_cost'] = $model->getTotalUnderCost();
			$data['under_cost_a'] = $model->getTotalUnderCost('A');
			$data['under_cost_b'] = $model->getTotalUnderCost('B');
			$data['under_cost_c'] = $model->getTotalUnderCost('C');
			$data['exclusive_stock'] = $model->getTotalExclusiveStock();
			$data['exclusive_stock_a'] = $model->getTotalExclusiveStock('A');
			$data['exclusive_stock_b'] = $model->getTotalExclusiveStock('B');
			$data['exclusive_stock_c'] = $model->getTotalExclusiveStock('C');
			echo view('pricing', $data);
	}

	// Função que monta as modais de departamentos
	public function modalPerdendo($department, $model) {
			$data['title'] = ucwords($department);
			$department_ = str_replace("ã", "a", str_replace(" ", "_", $department));
			$department = str_replace("ã", "a", $department);
			$data['produtos'] = $model->getProductsByDepartment($department);
			$data['id_data_table'] = $department_;
			$data[$department_] = count(json_decode($data['produtos']));
			$data['onofre'] = $model->getProductsQuantityByDepartmentAndCompetitor($department, 'onofre');
			$data['drogaraia'] = $model->getProductsQuantityByDepartmentAndCompetitor($department, 'drogaraia');
			$data['drogariasaopaulo'] = $model->getProductsQuantityByDepartmentAndCompetitor($department, 'drogariasaopaulo');
			$data['paguemenos'] = $model->getProductsQuantityByDepartmentAndCompetitor($department, 'paguemenos');
			$data['drogasil'] = $model->getProductsQuantityByDepartmentAndCompetitor($department, 'drogasil');
			$data['ultrafarma'] = $model->getProductsQuantityByDepartmentAndCompetitor($department, 'ultrafarma');
			$data['belezanaweb'] = $model->getProductsQuantityByDepartmentAndCompetitor($department, 'belezanaweb');
			$data['panvel'] = $model->getProductsQuantityByDepartmentAndCompetitor($department, 'panvel');
			$data['products_categories'] = $model->getProductsCategoriesByDepartment($department);
			$data['count_categories'] = [];
			foreach($data['products_categories'] as $category) {
					array_push($data['count_categories'], $model->getProductsQuantityByDepartmentAndCategories($department, $category));
			}
			echo view('modals/detalhamento', $data);
	}
}
