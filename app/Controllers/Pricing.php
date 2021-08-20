<?php

namespace App\Controllers;
use App\Models\ProductsModel;
use App\Models\SalesModel;
use CodeIgniter\I18n\Time;

class Pricing extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de pricing
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			$model = new ProductsModel();
			$sales_model = new SalesModel();

			// Cria os cards 'Top produtos', 'Demonstração financeira' e de margens
			$data['medicamento'] = $sales_model->getQtyTopProducts('medicamento');
			$data['perfumaria'] = $sales_model->getQtyTopProducts('perfumaria');
			$data['nao_medicamento'] = $sales_model->getQtyTopProducts('nao medicamento');
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

			// Dados de skus, rupturas, produtos abaixo do custo e estoques exclusivos
			$data['skus'] = $model->getTotalSkus();
			$data['skus_a'] = $model->getTotalSkus('', 'A');
			$data['skus_b'] = $model->getTotalSkus('', 'B');
			$data['skus_c'] = $model->getTotalSkus('', 'C');
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
			$data['losing_all'] = $model->getTotalLosingAll();
			$data['losing_all_a'] = $model->getTotalLosingAll('A');
			$data['losing_all_b'] = $model->getTotalLosingAll('B');
			$data['losing_all_c'] = $model->getTotalLosingAll('C');

			// Envia os dados de faturamento e margens dos últimos 6 meses para plotar um gráfico de linhas
			$data = $this->sales($data, $model, 'Geral');
			$data = $this->sales($data, $model, 'MEDICAMENTO');
			$data = $this->sales($data, $model, 'PERFUMARIA');
			$data = $this->sales($data, $model, 'NAO MEDICAMENTO');

			echo view('pricing', $data);
	}

	// Função que busca os dados de vendas dos últimos 6 meses partindo da data atual
	public function sales($data, $model, $department) {
			$model_sales = new SalesModel();
			$items = $model_sales->getSalesByDepartment($department);
 			$department = str_replace(" ", "_", strtolower($department));
 			$data[$department.'_sales'] = array('labels_line_chart' => array_map(function ($ar) {
																																							$months = array(1 => "Jan",
																																															2 => "Fev",
																																															3 => "Mar",
																																															4 => "Abr",
																																															5 => "Mai",
																																															6 => "Jun",
																																															7 => "Jul",
																																															8 => "Ago",
																																															9 => "Set",
																																															10 => "Out",
																																															11 => "Nov",
																																															12 => "Dez");
																																							return $months[explode("/", $ar)[0]]."/".explode("/", $ar)[1];
																																				   }, array_column($items, 'data')),
 																				  'data_margin_line_chart' => array_column($items, 'margin'),
 																			 	  'data_fat_line_chart' => array_column($items, 'faturamento'));
			return $data;
	}

	// Função que popula os dados de margens atuais por departamentos
	public function margin() {
			$data = [];
			$model_sales = new SalesModel();
			$margin_views = ['Geral', 'MEDICAMENTO', 'NAO MEDICAMENTO', 'PERFUMARIA'];
			foreach($margin_views as $margin_view) {
					$data_view = $model_sales->getSalesInfoByDate($this->request->getVar('date'), $margin_view);
					$total_value_vendas = array_sum(array_column($data_view, 'faturamento'));
					$total_price_cost = array_sum(array_column($data_view, 'price_cost'));
					$total_qtd = array_sum(array_column($data_view, 'qtd'));
					$total_margin = ($total_value_vendas != '') ? ($total_value_vendas - $total_price_cost)/$total_value_vendas*100 : 0;
					$labels = ($margin_view == 'Geral') ? array_values(array_unique(array_column($data_view, 'department'))) : array_values(array_unique(array_column($data_view, 'category')));
					$labels_data = [];

					// Configura os dados para exibir no gráfico
					foreach($labels as $label) {
							if($margin_view == 'Geral') {
									// Pega todos os departamentos e seta as margens totais de cada uma
									$products = array_filter($data_view, function($item) use($label) {
											return $item->department == $label;
									});
							}
							else {
									// Pega todas as categorias e seta as margens totais de cada uma
									$products = array_filter($data_view, function($item) use($label) {
											return $item->category == $label;
									});
							}
							$total_price_cost_dep_cat = array_sum(array_column($products, 'price_cost'));
							$total_value_vendas_dep_cat = array_sum(array_column($products, 'faturamento'));
							array_push($labels_data, (($total_value_vendas_dep_cat - $total_price_cost_dep_cat) / $total_value_vendas_dep_cat) * 100);
					}
					$margin_view_title = str_replace(" ", "_", strtolower($margin_view));
					$data[$margin_view_title."_margins"] = array('total_margin_day' => number_to_amount($total_margin, 2, 'pt_BR')."%",
																											 'total_sales_value_day' => number_to_currency($total_value_vendas, 'BRL', null, 2),
																											 'total_sales_qtd_day' => $total_qtd,
																											 'labels' => $labels,
																										   'data' => $labels_data);
			}
			return json_encode($data);
	}

	// Função que monta as modais de departamentos
	public function competitorInfo() {
			$model = new ProductsModel();
			$department = $this->request->getVar('department');
			$data['title'] = ucwords($department);
			$department = str_replace("ã", "a", $department);
			$obj = json_decode($model->getProductsByDepartment($department,
																												 $this->request->getVar('iDisplayStart'),
																												 $this->request->getVar('iDisplayLength'),
																												 $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																												 $this->request->getVar('sSortDir_0'),
																												 $this->request->getVar('sSearch')));
			$data['aaData'] = $obj->products;
			$data['iTotalRecords'] = $obj->qtd;
			$data['iTotalDisplayRecords'] = $obj->qtd;
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
			$data['relatorio_url'] = base_url().'/relatorio?type=perdendo&department='.str_replace("ã", "a", str_replace(" ", "_", $department));
			return json_encode($data);
	}

	// Função que monta as modais dos blisters
	public function blistersInfo() {
			$blister = $this->request->getVar('type');
			$curve = $this->request->getVar('curve');
			$model_products = new ProductsModel();
			$obj = [];
			switch($blister) {
					case "sku":
							$data['title'] = "SKU's";
							$data['relatorio_url'] = base_url()."/relatorio?type=total_skus&curve=$curve";
							$obj = json_decode($model_products->getSkus('',
																													$curve,
																													$this->request->getVar('iDisplayStart'),
																													$this->request->getVar('iDisplayLength'),
																													$this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																													$this->request->getVar('sSortDir_0'),
																													$this->request->getVar('sSearch')));
							break;
					case "ruptura":
							$data['title'] = "Produtos em Ruptura";
							$data['relatorio_url'] = base_url()."/relatorio?type=ruptura&curve=$curve";
							$obj = json_decode($model_products->getSkus('break',
																												  $curve,
																												  $this->request->getVar('iDisplayStart'),
																												  $this->request->getVar('iDisplayLength'),
																												  $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																											    $this->request->getVar('sSortDir_0'),
																											 	  $this->request->getVar('sSearch')));
							break;
					case "abaixo":
							$data['title'] = "Produtos Abaixo do Custo";
							$data['relatorio_url'] = base_url()."/relatorio?type=abaixo_custo&curve=$curve";
							$obj = json_decode($model_products->getSkus('under_cost',
																												  $curve,
																												  $this->request->getVar('iDisplayStart'),
																												  $this->request->getVar('iDisplayLength'),
																												  $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																											    $this->request->getVar('sSortDir_0'),
																											 	  $this->request->getVar('sSearch')));
							break;
					case "exclusivo":
							$data['title'] = "Produtos Estoque Exclusivo";
							$data['relatorio_url'] = base_url()."/relatorio?type=estoque_exclusivo&curve=$curve";
							$obj = json_decode($model_products->getSkus('exclusive_stock',
																												  $curve,
																												  $this->request->getVar('iDisplayStart'),
																												  $this->request->getVar('iDisplayLength'),
																												  $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																											    $this->request->getVar('sSortDir_0'),
																											 	  $this->request->getVar('sSearch')));
							break;
					case "perdendo":
							$data['title'] = "Produtos que estamos perdendo para todos os concorrentes";
							$data['relatorio_url'] = base_url()."/relatorio?type=perdendo_todos&curve=$curve";
							$obj = json_decode($model_products->getSkus('losing_all',
																												  $curve,
																												  $this->request->getVar('iDisplayStart'),
																												  $this->request->getVar('iDisplayLength'),
																												  $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																											    $this->request->getVar('sSortDir_0'),
																											 	  $this->request->getVar('sSearch')));
							break;
			}
			if($curve == 'A') {
					if($blister == 'sku') $data['total'] = 0;
					if($blister == 'sku') $data['total_a'] = $model_products->getTotalSkus('', 'A');
					if($blister == 'sku') $data['total_b'] = 0;
					if($blister == 'sku') $data['total_c'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break_a'] = $model_products->getTotalSkus('break', 'A');
					if($blister == 'sku' || $blister == 'ruptura') $data['break_b'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break_c'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_a'] = $model_products->getTotalSkus('under_cost', 'A');
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_b'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_c'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin_a'] = $model_products->getTotalSkus('', 'A', '', 4);
					if($blister == 'sku') $data['sacrifice_op_margin_b'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin_c'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin_a'] = $model_products->getTotalSkus('', 'A', '', 5);
					if($blister == 'sku') $data['sacrifice_gain_margin_b'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin_c'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_a'] = $model_products->getTotalSkus('exclusive_stock', 'A');
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_b'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_c'] = 0;
					if($blister == 'perdendo') $data['medicamento'] = $model_products->getTotalSkus('medicamento', 'A');
					if($blister == 'perdendo') $data['perfumaria'] = $model_products->getTotalSkus('perfumaria', 'A');
					if($blister == 'perdendo') $data['nao_medicamento'] = $model_products->getTotalSkus('nao medicamento', 'A');
			}
			else if($curve == 'B') {
					if($blister == 'sku') $data['total'] = 0;
					if($blister == 'sku') $data['total_a'] = 0;
					if($blister == 'sku') $data['total_b'] = $model_products->getTotalSkus('', 'B');
					if($blister == 'sku') $data['total_c'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break_a'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break_b'] = $model_products->getTotalSkus('break', 'B');
					if($blister == 'sku' || $blister == 'ruptura') $data['break_c'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_a'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_b'] = $model_products->getTotalSkus('under_cost', 'B');
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_c'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin_a'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin_b'] = $model_products->getTotalSkus('', 'B', '', 4);
					if($blister == 'sku') $data['sacrifice_op_margin_c'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin_a'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin_b'] = $model_products->getTotalSkus('', 'B', '', 5);
					if($blister == 'sku') $data['sacrifice_gain_margin_c'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_a'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_b'] = $model_products->getTotalSkus('exclusive_stock', 'B');
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_c'] = 0;
					if($blister == 'perdendo') $data['medicamento'] = $model_products->getTotalSkus('medicamento', 'B');
					if($blister == 'perdendo') $data['perfumaria'] = $model_products->getTotalSkus('perfumaria', 'B');
					if($blister == 'perdendo') $data['nao_medicamento'] = $model_products->getTotalSkus('nao medicamento', 'B');
			}
			else if($curve == 'C') {
					if($blister == 'sku') $data['total'] = 0;
					if($blister == 'sku') $data['total_a'] = 0;
					if($blister == 'sku') $data['total_b'] = 0;
					if($blister == 'sku') $data['total_c'] = $model_products->getTotalSkus('', 'C');
					if($blister == 'sku' || $blister == 'ruptura') $data['break'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break_a'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break_b'] = 0;
					if($blister == 'sku' || $blister == 'ruptura') $data['break_c'] = $model_products->getTotalSkus('break', 'C');
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_a'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_b'] = 0;
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_c'] = $model_products->getTotalSkus('under_cost', 'C');
					if($blister == 'sku') $data['sacrifice_op_margin'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin_a'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin_b'] = 0;
					if($blister == 'sku') $data['sacrifice_op_margin_c'] = $model_products->getTotalSkus('', 'C', '', 4);
					if($blister == 'sku') $data['sacrifice_gain_margin'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin_a'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin_b'] = 0;
					if($blister == 'sku') $data['sacrifice_gain_margin_c'] = $model_products->getTotalSkus('', 'C', '', 5);
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_a'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_b'] = 0;
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_c'] = $model_products->getTotalSkus('exclusive_stock', 'C');
					if($blister == 'perdendo') $data['medicamento'] = $model_products->getTotalSkus('medicamento', 'C');
					if($blister == 'perdendo') $data['perfumaria'] = $model_products->getTotalSkus('perfumaria', 'C');
					if($blister == 'perdendo') $data['nao_medicamento'] = $model_products->getTotalSkus('nao medicamento', 'C');
			}
			else if($curve == '') {
					if($blister == 'sku') $data['total'] = $model_products->getTotalSkus();
					if($blister == 'sku') $data['total_a'] = $model_products->getTotalSkus('', 'A');
					if($blister == 'sku') $data['total_b'] = $model_products->getTotalSkus('', 'B');
					if($blister == 'sku') $data['total_c'] = $model_products->getTotalSkus('', 'C');
					if($blister == 'sku' || $blister == 'ruptura') $data['break'] = $model_products->getTotalSkus('break', '');
					if($blister == 'sku' || $blister == 'ruptura') $data['break_a'] = $model_products->getTotalSkus('break', 'A');
					if($blister == 'sku' || $blister == 'ruptura') $data['break_b'] = $model_products->getTotalSkus('break', 'B');
					if($blister == 'sku' || $blister == 'ruptura') $data['break_c'] = $model_products->getTotalSkus('break', 'C');
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost'] = $model_products->getTotalSkus('under_cost', '');
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_a'] = $model_products->getTotalSkus('under_cost', 'A');
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_b'] = $model_products->getTotalSkus('under_cost', 'B');
					if($blister == 'sku' || $blister == 'abaixo') $data['under_equal_cost_c'] = $model_products->getTotalSkus('under_cost', 'C');
					if($blister == 'sku') $data['sacrifice_op_margin'] = $model_products->getTotalSkus('', '', '', 4);
					if($blister == 'sku') $data['sacrifice_op_margin_a'] = $model_products->getTotalSkus('', 'A', '', 4);
					if($blister == 'sku') $data['sacrifice_op_margin_b'] = $model_products->getTotalSkus('', 'B', '', 4);
					if($blister == 'sku') $data['sacrifice_op_margin_c'] = $model_products->getTotalSkus('', 'C', '', 4);
					if($blister == 'sku') $data['sacrifice_gain_margin'] = $model_products->getTotalSkus('', '', '', 5);
					if($blister == 'sku') $data['sacrifice_gain_margin_a'] = $model_products->getTotalSkus('', 'A', '', 5);
					if($blister == 'sku') $data['sacrifice_gain_margin_b'] = $model_products->getTotalSkus('', 'B', '', 5);
					if($blister == 'sku') $data['sacrifice_gain_margin_c'] = $model_products->getTotalSkus('', 'C', '', 5);
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock'] = $model_products->getTotalSkus('exclusive_stock', '');
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_a'] = $model_products->getTotalSkus('exclusive_stock', 'A');
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_b'] = $model_products->getTotalSkus('exclusive_stock', 'B');
					if($blister == 'sku' || $blister == 'exclusivo') $data['exclusive_stock_c'] = $model_products->getTotalSkus('exclusive_stock', 'C');
					if($blister == 'perdendo') $data['medicamento'] = $model_products->getTotalSkus('medicamento');
					if($blister == 'perdendo') $data['perfumaria'] = $model_products->getTotalSkus('perfumaria');
					if($blister == 'perdendo') $data['nao_medicamento'] = $model_products->getTotalSkus('nao medicamento');
			}
			$data['aaData'] = $obj->products;
			$data['iTotalRecords'] = $obj->qtd;
			$data['iTotalDisplayRecords'] = $obj->qtd;
			return json_encode($data);
	}

	public function productsGroups() {
			$type = $this->request->getVar('type');
			$model_sales = new SalesModel();
			$total = $model_sales->totalFat();
			if($type == 'categoria') { // Cria o gráfico percentual de categorias
					$data['autocuidado'] = array('label' => 'Autocuidado',
																			 'value' => $model_sales->totalFatAutocuidado(),
																			 'data' => round(($model_sales->totalFatAutocuidado()/$total)*100));
					$data['similar'] = array('label' => 'Similar',
																	 'value' => $model_sales->totalFatSimilar(),
															 		 'data' => round(($model_sales->totalFatSimilar()/$total)*100));
					$data['marca'] = array('label' => 'Marca',
															   'value' => $model_sales->totalFatMarca(),
																 'data' => round(($model_sales->totalFatMarca()/$total)*100));
					$data['generico'] = array('label' => 'Genérico',
																	  'value' => $model_sales->totalFatGenerico(),
															 			'data' => round(($model_sales->totalFatGenerico()/$total)*100));
					$data['higiene_e_beleza'] = array('label' => 'Higiene e Beleza',
																						'value' => $model_sales->totalFatHigieneBeleza(),
																						'data' => round(($model_sales->totalFatHigieneBeleza()/$total)*100));
					$data['mamae_e_bebe'] = array('label' => 'Mamãe e Bebê',
																				'value' => $model_sales->totalFatMamaeBebe(),
																				'data' => round(($model_sales->totalFatMamaeBebe()/$total)*100));
					$data['dermocosmetico'] = array('label' => 'Dermocosmético',
																					'value' => $model_sales->totalFatDermocosmetico(),
																					'data' => round(($model_sales->totalFatDermocosmetico()/$total)*100));
					$data['beleza'] = array('label' => 'Beleza',
																	'value' => $model_sales->totalFatBeleza(),
																	'data' => round(($model_sales->totalFatBeleza()/$total)*100));
			}
			else if($type == 'marca') { // Cria o gráfico percentual de categorias
					$samb = $model_sales->totalFatMarcas();
					$i = 0;
					foreach($samb as $row) {
							$data['marca_'.$i] = array('label' => ucfirst(strtolower($row->marca)),
																				 'value' => $row->total,
																				 'data' => number_format(($row->total/$model_sales->totalFatDermocosmetico()*100), 2, ',', '.'));
							$i++;
					}
			}
			else if($type == 'acoes') { // Cria o gráfico percentual de ações
					$actions = $model_sales->getRankingActions();
					$i = 0;
					foreach($actions as $action) {
							$i++;
							$data['action_'.$i] = array('label' => $action->acao,
																					'value' => $model_sales->totalFatActions($action->acao),
																					'data' => number_format(($model_sales->totalFatActions($action->acao)/$total)*100), 2, ',', '.');
					}
			}
			else if($type == 'sub_categorias') { // Cria o gráfico percentual de sub categorias
					$data['sub_cat_mip'] = array('label' => 'MIP',
																			 'value' => $model_sales->totalSubCatMIP(),
																			 'data' => number_format(($model_sales->totalSubCatMIP()/$total)*100), 2, ',', '.');
					$data['sub_cat_eticos'] = array('label' => 'Éticos',
																				  'value' => $model_sales->totalSubCatEticos(),
																		 		  'data' => number_format(($model_sales->totalSubCatEticos()/$total)*100), 2, ',', '.');
					$data['sub_cat_no_medicamentos'] = array('label' => 'No Medicamentos',
																							     'value' => $model_sales->totalSubCatNoMed(),
																								   'data' => number_format(($model_sales->totalSubCatNoMed()/$total)*100), 2, ',', '.');
					$data['sub_cat_perfumaria'] = array('label' => 'Perfumaria',
																					    'value' => $model_sales->totalSubCatPerf(),
																			 			  'data' => number_format(($model_sales->totalSubCatPerf()/$total)*100), 2, ',', '.');
					$data['sub_cat_generico'] = array('label' => 'Genéricos',
																					  'value' => $model_sales->totalSubCatGen(),
																					  'data' => number_format(($model_sales->totalSubCatGen()/$total)*100), 2, ',', '.');
					$data['sub_cat_dermocosmeticos'] = array('label' => 'Dermocosméticos',
																									 'value' => $model_sales->totalSubCatDermo(),
																									 'data' => number_format(($model_sales->totalSubCatDermo()/$total)*100), 2, ',', '.');
					$data['sub_cat_similar'] = array('label' => 'Similares',
																					 'value' => $model_sales->totalSubCatSimilar(),
																					 'data' => number_format(($model_sales->totalSubCatSimilar()/$total)*100), 2, ',', '.');
			}
			else { // Cria o gráfico percentual de grupos de produtos
					$data['termolabil'] = array('label' => 'Termolábil',
																			'value' => $model_sales->totalFatTermolabil(),
																			'data' => round(($model_sales->totalFatTermolabil()/$total)*100));
					$data['otc'] = array('label' => 'OTC',
															 'value' => $model_sales->totalFatOTC(),
															 'data' => round(($model_sales->totalFatOTC()/$total)*100));
				  $data['controlados'] = array('label' => 'Controlados',
																			 'value' => $model_sales->totalFatControlados(),
															 				 'data' => round(($model_sales->totalFatControlados()/$total)*100));
				  $data['pbm'] = array('label' => 'PBM',
															 'value' => $model_sales->totalFatPBM(),
															 'data' => round(($model_sales->totalFatPBM()/$total)*100));
				  $data['cashback_percent'] = array('label' => 'Cashback',
																						'value' => $model_sales->totalFatCashback(),
															 							'data' => round(($model_sales->totalFatCashback()/$total)*100));
					$data['home'] = array('label' => 'Home',
																'value' => $model_sales->totalFatHome(),
															 	'data' => round(($model_sales->totalFatHome()/$total)*100));
					$data['perdendo_grupos'] = array('label' => 'Perdendo',
																					 'value' => $model_sales->totalFatPerdendo(),
																				 	 'data' => round(($model_sales->totalFatPerdendo()/$total)*100));
			}
			return json_encode($data);
	}

	public function tableInfo() {
			$model_sales = new SalesModel();
			if ($this->request->getVar('param_1') !== null) { // Clique das modais de grupos de produtos
					$obj = json_decode($model_sales->getDataSalesTable('',
																														 'geral',
																														 $this->request->getVar('param_1'),
																														 $this->request->getVar('iDisplayStart'),
																														 $this->request->getVar('iDisplayLength'),
																														 $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																														 $this->request->getVar('sSortDir_0'),
																														 $this->request->getVar('sSearch')));
					$data['relatorio_url'] = base_url()."/relatorio?type=grupos_produtos&group=".$this->request->getVar('param_1');
			}
			else if($this->request->getVar('sale_date') !== null && $this->request->getVar('department') !== null) { // Clique das modais de produtos vendidos
					$obj = json_decode($model_sales->getDataSalesTable($this->request->getVar('sale_date'),
																														 $this->request->getVar('department'),
																														 '',
																														 $this->request->getVar('iDisplayStart'),
																														 $this->request->getVar('iDisplayLength'),
																														 $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																														 $this->request->getVar('sSortDir_0'),
																														 $this->request->getVar('sSearch')));
					$data['relatorio_url'] = base_url()."/relatorio?type=vendidos&department=".$this->request->getVar('department')."&sale_date=".$this->request->getVar('sale_date');
			}
			else if($this->request->getVar('department') !== null) {
					$obj = json_decode($model_sales->getDataTopProductsTable($this->request->getVar('department'),
																																	 $this->request->getVar('iDisplayStart'),
																																	 $this->request->getVar('iDisplayLength'),
																																	 $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																																	 $this->request->getVar('sSortDir_0'),
																																	 $this->request->getVar('sSearch')));
					$data['relatorio_url'] = base_url()."/relatorio?type=top_produtos&department=".$this->request->getVar('department');
			}
			$data['up_total_1'] = $obj->up_total_1;
			$data['up_a_1'] = $obj->up_a_1;
			$data['up_b_1'] = $obj->up_b_1;
			$data['up_c_1'] = $obj->up_c_1;
			$data['down_total_1'] = $obj->down_total_1;
			$data['down_a_1'] = $obj->down_a_1;
			$data['down_b_1'] = $obj->down_b_1;
			$data['down_c_1'] = $obj->down_c_1;
			$data['keep_total_1'] = $obj->keep_total_1;
			$data['keep_a_1'] = $obj->keep_a_1;
			$data['keep_b_1'] = $obj->keep_b_1;
			$data['keep_c_1'] = $obj->keep_c_1;
			$data['up_total_2'] = $obj->up_total_2;
			$data['up_a_2'] = $obj->up_a_2;
			$data['up_b_2'] = $obj->up_b_2;
			$data['up_c_2'] = $obj->up_c_2;
			$data['down_total_2'] = $obj->down_total_2;
			$data['down_a_2'] = $obj->down_a_2;
			$data['down_b_2'] = $obj->down_b_2;
			$data['down_c_2'] = $obj->down_c_2;
			$data['keep_total_2'] = $obj->keep_total_2;
			$data['keep_a_2'] = $obj->keep_a_2;
			$data['keep_b_2'] = $obj->keep_b_2;
			$data['keep_c_2'] = $obj->keep_c_2;
			$data['aaData'] = $obj->products;
			$data['iTotalRecords'] = $obj->qtd;
			$data['iTotalDisplayRecords'] = $obj->qtd;
			return json_encode($data);
	}

	public function checkmydate($date) {
			$tempDate = explode('-', $date);
			return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
	}

	public function response($vfilial, $initial_date, $final_date) {
			if(base_url() == 'http://qualibrain.local.com') {
					$response = '{
												  "items": [
												    {
												      "hour": 0,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 15,
												      "value": 1712.98,
												      "avgTicket": 114.2,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 3,
												      "valueDayBefore": 44.72,
												      "avgTicketDayBefore": 14.91,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 5,
												      "valueWeekAgo": 426.97,
												      "avgTicketWeekAgo": 85.39
												    },
												    {
												      "hour": 1,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 17,
												      "value": 1893.5,
												      "avgTicket": 111.38,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 14,
												      "valueDayBefore": 1164.99,
												      "avgTicketDayBefore": 83.21,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 1,
												      "valueWeekAgo": 2.13,
												      "avgTicketWeekAgo": 2.13
												    },
												    {
												      "hour": 2,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 0,
												      "value": 0,
												      "avgTicket": 0,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 5,
												      "valueDayBefore": 508.05,
												      "avgTicketDayBefore": 101.61,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 22,
												      "valueWeekAgo": 1882.06,
												      "avgTicketWeekAgo": 85.55
												    },
												    {
												      "hour": 3,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 7,
												      "value": 354.14,
												      "avgTicket": 50.59,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 3,
												      "valueDayBefore": 88.74,
												      "avgTicketDayBefore": 29.58,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 9,
												      "valueWeekAgo": 619.33,
												      "avgTicketWeekAgo": 68.81
												    },
												    {
												      "hour": 4,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 26,
												      "value": 2502.41,
												      "avgTicket": 96.25,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 3,
												      "valueDayBefore": 113.33,
												      "avgTicketDayBefore": 37.78,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 23,
												      "valueWeekAgo": 2190.58,
												      "avgTicketWeekAgo": 95.24
												    },
												    {
												      "hour": 5,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 7,
												      "value": 1287.68,
												      "avgTicket": 183.95,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 4,
												      "valueDayBefore": 149.75,
												      "avgTicketDayBefore": 37.44,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 12,
												      "valueWeekAgo": 1662.9,
												      "avgTicketWeekAgo": 138.58
												    },
												    {
												      "hour": 6,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 20,
												      "value": 2401.56,
												      "avgTicket": 120.08,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 14,
												      "valueDayBefore": 1294.1,
												      "avgTicketDayBefore": 92.44,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 20,
												      "valueWeekAgo": 1679.07,
												      "avgTicketWeekAgo": 83.95
												    },
												    {
												      "hour": 7,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 16,
												      "value": 1837.08,
												      "avgTicket": 114.82,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 32,
												      "valueDayBefore": 2385.32,
												      "avgTicketDayBefore": 74.54,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 22,
												      "valueWeekAgo": 2552.83,
												      "avgTicketWeekAgo": 116.04
												    },
												    {
												      "hour": 8,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 32,
												      "value": 3204.26,
												      "avgTicket": 100.13,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 75,
												      "valueDayBefore": 6027.82,
												      "avgTicketDayBefore": 80.37,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 48,
												      "valueWeekAgo": 4247.51,
												      "avgTicketWeekAgo": 88.49
												    },
												    {
												      "hour": 9,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 65,
												      "value": 6738.98,
												      "avgTicket": 103.68,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 63,
												      "valueDayBefore": 6257.59,
												      "avgTicketDayBefore": 99.33,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 48,
												      "valueWeekAgo": 4971.91,
												      "avgTicketWeekAgo": 103.58
												    },
												    {
												      "hour": 10,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 102,
												      "value": 10914.42,
												      "avgTicket": 107,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 98,
												      "valueDayBefore": 11060.79,
												      "avgTicketDayBefore": 112.87,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 103,
												      "valueWeekAgo": 11461.56,
												      "avgTicketWeekAgo": 111.28
												    },
												    {
												      "hour": 11,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 84,
												      "value": 10125.81,
												      "avgTicket": 120.55,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 81,
												      "valueDayBefore": 11412.84,
												      "avgTicketDayBefore": 140.9,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 114,
												      "valueWeekAgo": 12865.98,
												      "avgTicketWeekAgo": 112.86
												    },
												    {
												      "hour": 12,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 74,
												      "value": 7498.15,
												      "avgTicket": 101.33,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 72,
												      "valueDayBefore": 6199.37,
												      "avgTicketDayBefore": 86.1,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 96,
												      "valueWeekAgo": 11030.39,
												      "avgTicketWeekAgo": 114.9
												    },
												    {
												      "hour": 13,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 73,
												      "value": 7095.88,
												      "avgTicket": 97.2,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 79,
												      "valueDayBefore": 9977.88,
												      "avgTicketDayBefore": 126.3,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 100,
												      "valueWeekAgo": 11322.4,
												      "avgTicketWeekAgo": 113.22
												    },
												    {
												      "hour": 14,
												      "date": "18/08/2021 00:00:00",
												      "quantity": 97,
												      "value": 15220.41,
												      "avgTicket": 156.91,
												      "dayBefore": "17/08/2021 00:00:00",
												      "quantityDayBefore": 89,
												      "valueDayBefore": 10268.18,
												      "avgTicketDayBefore": 115.37,
												      "weekAgo": "11/08/2021 00:00:00",
												      "quantityWeekAgo": 98,
												      "valueWeekAgo": 8072.1,
												      "avgTicketWeekAgo": 82.37
												    }
												  ],
												  "quantityItems": 15,
												  "item": null
												}';
			}
			else {
					$client = \Config\Services::curlrequest();
					$response = $client->request('GET', "http://ultraclinica.totvscloud.com.br:2000/RMS/RMSSERVICES/ReportWebAPI/api/v1/SaleHistory/GetCompareSales?filial=".$vfilial."&dataVendaInicio=".$initial_date."&dataVendaFim=".$final_date, [ 'headers' => ['Content-Type: application/vnd.api+json', 'Accept: application/vnd.api+json'] ])->getBody();
			}
			return $response;
	}

	public function getSalesRMS() {
			$hoje = date("Y-m-d");
			$vfilial = ($this->request->getVar('vfilial') !== null) ? $this->request->getVar('vfilial') : '1007';
			$vdata = ($this->request->getVar('vdata') !== null) ? $this->request->getVar('vdata') : $hoje;
			if ($vdata > $hoje) $vdata=$hoje;
			$ontem = date('Y-m-d', (strtotime ('-1 day', strtotime($vdata))));
			$semana = date('Y-m-d',(strtotime ('-7 day', strtotime($vdata))));
			$response = json_decode($this->response($vfilial, $vdata, $vdata));
			$resposta = $response->items;
			$totalqtd=0;
			$totalvalue=0;
			$totaltkm=0;
			$totalqtddb=0;
			$totalvaluedb=0;
			$totaltkmdb=0;
			$totalqtdwa=0;
			$totalvaluewa=0;
			$totaltkmwa=0;
			$html =
			'<div class="table-responsive">'.
					'<div class="container" width="100%">'.
							'<table width=100% border=0>'.
									'<tr>'.
											'<td width=33%><p class="text-center"><b>'.$vdata.'(Data Escolhida) </b></p></td>'.
											'<td width=33%><b><p class="text-center">'.$ontem.'(Dia Anterior)</p> </b></td>'.
											'<td><b><p class="text-center">'.$semana.'(Semana Passada) </b></p></td>'.
											// '<td>'.
											// 		'<div class="col-sm-10">'.
											// 				'<select class="form-control form-control-sm" name="vdatasemana" id="vdatasemana">'.
											// 						'<option selected value="'.$semana.'">'.date('d/m/Y', strtotime($semana)).'</option>'.
											// 						'<option value="1007" selected>1007</option>'.
											// 				'</select>'.
											// 		'</div>'.
											// '</td>'.
									'</tr>'.
							'</table>'.
							'<table border="1" width="100%"  style=" border-collapse: collapse;border-spacing: 0;text-align:center;"  class="table-hover">'.
									'<thead style="background-color:lightgray">'.
											'<th><font color="black">HORA</th>'.
											'<th><font color="black">QTD NF</th>'.
											'<th><font color="black">VALOR</th>'.
											'<th><font color="black">TKM</th>'.
											'<th style="background-color:black";></th>'.
											'<th><font color="black">QTD NF</th>'.
											'<th><font color="black">VALOR</th>'.
											'<th><font color="black">TKM</th>'.
											'<th><font color="black">FAT. X ATUAL</th>'.
											'<th style="background-color:black"></th>'.
											'<th><font color="black">QTD NF</th>'.
											'<th><font color="black">VALOR</th>'.
											'<th><font color="black">TKM</th>'.
											'<th><font color="black">FAT. X ATUAL</th>'.
									'</thead>';
			foreach ($resposta as $inf){
					$html .=
									'<tr>'.
											'<th style="background-color:lightgray"><font color="black">'.$hora= $inf->hour.'</font></th>';
					$qtd=$inf->quantity;
					$totalqtd=$totalqtd + $qtd;
					$html .=
											'<td>'.$qtd.'</td>';
					$value=$inf->value;
					$totalvalue=$totalvalue + $value;
					$html .=    '<td>'.number_to_currency($value, 'BRL', null, 2).'</td>';
					$tkm=$inf->avgTicket;
					$html .=    '<td>'.number_to_currency($tkm, 'BRL', null, 2).'</td>'.
											'<td style="background-color:black"></td>';
					$totaltkm=($totalvalue / $totalqtd);
					$qtddb=$inf->quantityDayBefore;
					$totalqtddb=$totalqtddb + $qtddb;
					$html .=    '<td>'.$qtddb.'</td>';
					$valuedb=$inf->valueDayBefore;
					$totalvaluedb=$totalvaluedb + $valuedb;
					$html .=    '<td>'.number_to_currency($valuedb, 'BRL', null, 2).'</td>';
					$tkmdb=$inf->avgTicketDayBefore;
					$html .=    '<td>'.number_to_currency($tkmdb, 'BRL', null, 2).'</td>';
					if ($value > 0) {
							$comp_hj_1d = (($valuedb/$value)*100);
							$comp_hj_1=number_format($comp_hj_1d,0,",",".");
							if ($comp_hj_1d > 100 && $comp_hj_1d < 110) {
									$html .=
										  '<td style="background-color:yellow">'.$comp_hj_1.'%</td>';
							}
							elseif($comp_hj_1d > 110) {
									$html .=
										  '<td style="background-color:#ffcccb">'.$comp_hj_1.'%</td>';
							}
							else {
									$html .=
										  '<td>'.$comp_hj_1.'%</td>';
							}
					}
					else {
							$html .='<td>#</td>';
					}
					$html .=    '<td style="background-color:black"></td>';
					$totaltkmdb=($totalvaluedb / $totalqtddb);
					$qtdwa=$inf->quantityWeekAgo;
					$totalqtdwa=$totalqtdwa + $qtdwa;
					$html .=    '<td>'.$qtdwa.'</td>';
					$valuewa=$inf->valueWeekAgo;
					$totalvaluewa=$totalvaluewa + $valuewa;
					$html .=    '<td>'.number_to_currency($valuewa, 'BRL', null, 2).'</td>';
					$tkmwa=$inf->avgTicketWeekAgo;
					$html .=    '<td>'.number_to_currency($tkmwa, 'BRL', null, 2).'</td>';
					if ($value > 0) {
							$comp_hj_1dwa = (($valuewa/$value)*100);
							$comp_hj_1wa=number_format($comp_hj_1dwa,0,",",".");
							if($comp_hj_1dwa > 100 && $comp_hj_1dwa < 110) {
									$html .=
									    '<td  style="background-color:yellow">'.$comp_hj_1wa.'%</td>';
							}
							elseif($comp_hj_1dwa > 110) {
									$html .=
									    '<td style="background-color:#ffcccb">'.$comp_hj_1wa.'%</td>';
							}
							else {
									$html .=
									    '<td>'.$comp_hj_1wa.'%</td>';
							}
					}
					else {
							$html .='<td>#</td>';
					}
					$totaltkmwa=($totalvaluewa / $totalqtdwa);
					$html .='</tr>';
			}
			$html .= 		'<tr style="background-color:lightblue;border:0">'.
											'<td><font color="black"><b>TOTAL</b></font></td>'.
											'<td><font color="black"><b>'.$totalqtd.'</b></font></td>'.
											'<td><font color="black"><b>'.number_to_currency($totalvalue, 'BRL', null, 2).'</b></font></td>'.
											'<td><font color="black"><b>'.number_to_currency($totaltkm, 'BRL', null, 2).'</b></font></td>'.
											'<td style="background-color:black"></td>'.
											'<td><font color="black"><b>'.$totalqtddb.'</b></font></td>'.
											'<td><font color="black"><b>'.number_to_currency($totalvaluedb, 'BRL', null, 2).'</b></font></td>'.
											'<td><font color="black"><b>'.number_to_currency($totaltkmdb, 'BRL', null, 2).'</b></font></td>'.
											'<td style="background-color:white;border:0;"></td>'.
											'<td style="background-color:black"></td>'.
											'<td><font color="black"><b>'.$totalqtdwa.'</b></font></td>'.
											'<td><font color="black"><b>'.number_to_currency($totalvaluewa, 'BRL', null, 2).'</b></font></td>'.
											'<td><font color="black"><b>'.number_to_currency($totaltkmwa, 'BRL', null, 2).'</b></font></td>'.
											'<td style="background-color:white;display: none;"></td>'.
									'</tr>'.
							'</table>'.
					'</div>'.
			'</div>';
			echo $html;
	}
}
