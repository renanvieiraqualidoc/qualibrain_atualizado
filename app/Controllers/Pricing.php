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
}
