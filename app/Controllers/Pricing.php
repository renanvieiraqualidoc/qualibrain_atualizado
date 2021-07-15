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

	// Função que acessa a API para acessar as vendas baseado em um período
	public function response($initial_date, $final_date) {
			if(base_url() == 'http://qualibrain.local.com') {
					if($initial_date == $final_date) {
							$response = '{
						"items": [
							{
								"productCode": 1000233,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 11.72
							},
							{
								"productCode": 1000268,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 23.7
							},
							{
								"productCode": 1000292,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.31
							},
							{
								"productCode": 1000306,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 44.57
							},
							{
								"productCode": 1000373,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 91.94
							},
							{
								"productCode": 1000390,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 154.42
							},
							{
								"productCode": 1000403,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 42.42
							},
							{
								"productCode": 1000411,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 172.1
							},
							{
								"productCode": 1000420,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 137.61
							},
							{
								"productCode": 1000470,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.41
							},
							{
								"productCode": 1000489,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 6,
								"salesValue": 12.48
							},
							{
								"productCode": 1000500,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 50.72
							},
							{
								"productCode": 1000527,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 92.46
							},
							{
								"productCode": 1000535,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 12.4
							},
							{
								"productCode": 1000551,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 41.22
							},
							{
								"productCode": 1000560,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 41.97
							},
							{
								"productCode": 1000586,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 7,
								"salesValue": 123.22
							},
							{
								"productCode": 1000608,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 255.95
							},
							{
								"productCode": 1000616,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 4.8
							},
							{
								"productCode": 1000705,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 26.92
							},
							{
								"productCode": 1000713,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.96
							},
							{
								"productCode": 1000730,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 142.07
							},
							{
								"productCode": 1000756,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 78.8
							},
							{
								"productCode": 1000772,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 14.34
							},
							{
								"productCode": 1000780,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 6,
								"salesValue": 205.14
							},
							{
								"productCode": 1000845,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 84.9
							},
							{
								"productCode": 1000870,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 52.94
							},
							{
								"productCode": 1000896,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 113.44
							},
							{
								"productCode": 1000900,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 252.84
							},
							{
								"productCode": 1001000,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 174.2
							},
							{
								"productCode": 1001019,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 52.53
							},
							{
								"productCode": 1001027,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 23.36
							},
							{
								"productCode": 1001086,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 32.54
							},
							{
								"productCode": 1001140,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 55.28
							},
							{
								"productCode": 1001159,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 26.69
							},
							{
								"productCode": 1001183,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 45.07
							},
							{
								"productCode": 1001213,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 43.06
							},
							{
								"productCode": 1001221,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 60.79
							},
							{
								"productCode": 1001302,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 61.92
							},
							{
								"productCode": 1001396,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 79.53
							},
							{
								"productCode": 1001434,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 33.26
							},
							{
								"productCode": 1001485,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 70.28
							},
							{
								"productCode": 1001604,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 23.04
							},
							{
								"productCode": 1001612,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 82.93
							},
							{
								"productCode": 1001701,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 68.52
							},
							{
								"productCode": 1001752,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.96
							},
							{
								"productCode": 1001779,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 55.01
							},
							{
								"productCode": 1001795,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 126.86
							},
							{
								"productCode": 1001876,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 94.98
							},
							{
								"productCode": 1001957,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.53
							},
							{
								"productCode": 1001965,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 12.68
							},
							{
								"productCode": 1002082,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 42.57
							},
							{
								"productCode": 1002104,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.48
							},
							{
								"productCode": 1002120,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.52
							},
							{
								"productCode": 1002147,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.78
							},
							{
								"productCode": 1002210,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 53.56
							},
							{
								"productCode": 1002384,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.39
							},
							{
								"productCode": 1002473,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 43.12
							},
							{
								"productCode": 1002520,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 72.99
							},
							{
								"productCode": 1002546,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 38.52
							},
							{
								"productCode": 1002627,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 48.75
							},
							{
								"productCode": 1002651,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 90.2
							},
							{
								"productCode": 1002724,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 50.87
							},
							{
								"productCode": 1002805,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.8
							},
							{
								"productCode": 1002872,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 54.29
							},
							{
								"productCode": 1002902,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 36.09
							},
							{
								"productCode": 1002929,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.8
							},
							{
								"productCode": 1003020,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 23.65
							},
							{
								"productCode": 1003275,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 6.8
							},
							{
								"productCode": 1003364,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 737.43
							},
							{
								"productCode": 1003453,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 81.33
							},
							{
								"productCode": 1003534,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 43.12
							},
							{
								"productCode": 1003585,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 2.13
							},
							{
								"productCode": 1003615,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 77.85
							},
							{
								"productCode": 1003623,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 21.92
							},
							{
								"productCode": 1003631,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 310.25
							},
							{
								"productCode": 1003666,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 29.4
							},
							{
								"productCode": 1003690,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.35
							},
							{
								"productCode": 1003852,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 267.88
							},
							{
								"productCode": 1003968,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 56.17
							},
							{
								"productCode": 1004018,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 7,
								"salesValue": 133.35
							},
							{
								"productCode": 1004042,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 94.86
							},
							{
								"productCode": 1004077,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 26.65
							},
							{
								"productCode": 1004085,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 73.42
							},
							{
								"productCode": 1004174,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 110.55
							},
							{
								"productCode": 1004182,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 44.45
							},
							{
								"productCode": 1004280,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 45.02
							},
							{
								"productCode": 1004336,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.65
							},
							{
								"productCode": 1004409,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 6,
								"salesValue": 125.34
							},
							{
								"productCode": 1004417,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 88.66
							},
							{
								"productCode": 1004425,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 209.94
							},
							{
								"productCode": 1004565,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 49.09
							},
							{
								"productCode": 1004638,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.41
							},
							{
								"productCode": 1004794,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 14.39
							},
							{
								"productCode": 1004859,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 33.48
							},
							{
								"productCode": 1004875,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 58.25
							},
							{
								"productCode": 1004883,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 35.9
							},
							{
								"productCode": 1004891,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 42.38
							},
							{
								"productCode": 1004921,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.69
							},
							{
								"productCode": 1004980,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 38.6
							},
							{
								"productCode": 1004999,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 58.6
							},
							{
								"productCode": 1005090,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 49.07
							},
							{
								"productCode": 1005251,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 33.77
							},
							{
								"productCode": 1005359,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.68
							},
							{
								"productCode": 1005529,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 45
							},
							{
								"productCode": 1005600,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.16
							},
							{
								"productCode": 1005618,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 58.45
							},
							{
								"productCode": 1005693,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 64.55
							},
							{
								"productCode": 1005715,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.8
							},
							{
								"productCode": 1005901,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.39
							},
							{
								"productCode": 1005952,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 47.33
							},
							{
								"productCode": 1006010,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 28.51
							},
							{
								"productCode": 1006070,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.65
							},
							{
								"productCode": 1006223,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.16
							},
							{
								"productCode": 1006240,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 36.9
							},
							{
								"productCode": 1006320,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 46.76
							},
							{
								"productCode": 1006339,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.15
							},
							{
								"productCode": 1006347,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 27.72
							},
							{
								"productCode": 1006509,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 162.56
							},
							{
								"productCode": 1006533,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 31.49
							},
							{
								"productCode": 1006592,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 32.36
							},
							{
								"productCode": 1006622,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 37.2
							},
							{
								"productCode": 1006649,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.02
							},
							{
								"productCode": 1006720,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 97.04
							},
							{
								"productCode": 1006746,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.16
							},
							{
								"productCode": 1006797,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.05
							},
							{
								"productCode": 1006843,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 42.18
							},
							{
								"productCode": 1006991,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 3.14
							},
							{
								"productCode": 1007009,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 62.57
							},
							{
								"productCode": 1007130,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 48.9
							},
							{
								"productCode": 1007173,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 79.81
							},
							{
								"productCode": 1007181,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 46.04
							},
							{
								"productCode": 1007203,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.13
							},
							{
								"productCode": 1007254,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.8
							},
							{
								"productCode": 1007270,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 6,
								"salesValue": 33.42
							},
							{
								"productCode": 1007300,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 47.21
							},
							{
								"productCode": 1007416,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.07
							},
							{
								"productCode": 1007440,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 24.98
							},
							{
								"productCode": 1007645,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 14.72
							},
							{
								"productCode": 1007750,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.53
							},
							{
								"productCode": 1007815,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 121.56
							},
							{
								"productCode": 1007955,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.2
							},
							{
								"productCode": 1007980,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 12.27
							},
							{
								"productCode": 1007998,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 7.58
							},
							{
								"productCode": 1008013,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 30.6
							},
							{
								"productCode": 1008048,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.49
							},
							{
								"productCode": 1008056,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.46
							},
							{
								"productCode": 1008064,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 8.54
							},
							{
								"productCode": 1008129,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 186.81
							},
							{
								"productCode": 1008188,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.14
							},
							{
								"productCode": 1008196,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 37.44
							},
							{
								"productCode": 1008200,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.99
							},
							{
								"productCode": 1008226,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.96
							},
							{
								"productCode": 1008269,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 104.1
							},
							{
								"productCode": 1008277,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 42.65
							},
							{
								"productCode": 1008374,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 44.73
							},
							{
								"productCode": 1008668,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 34.65
							},
							{
								"productCode": 1008978,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 14
							},
							{
								"productCode": 1009176,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.62
							},
							{
								"productCode": 1009290,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 71.91
							},
							{
								"productCode": 1009397,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 48.6
							},
							{
								"productCode": 1009400,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 26.22
							},
							{
								"productCode": 1009532,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 28.84
							},
							{
								"productCode": 1009575,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.28
							},
							{
								"productCode": 1009664,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.28
							},
							{
								"productCode": 1009672,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 31.91
							},
							{
								"productCode": 1009737,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 337.68
							},
							{
								"productCode": 1009761,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.45
							},
							{
								"productCode": 1010077,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 19.94
							},
							{
								"productCode": 1010158,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 14.05
							},
							{
								"productCode": 1010280,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 36.45
							},
							{
								"productCode": 1010492,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.3
							},
							{
								"productCode": 1010506,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 23.24
							},
							{
								"productCode": 1010824,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 76.02
							},
							{
								"productCode": 1010972,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 63.16
							},
							{
								"productCode": 1011006,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.95
							},
							{
								"productCode": 1011596,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.39
							},
							{
								"productCode": 1011600,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 49.9
							},
							{
								"productCode": 1011685,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.19
							},
							{
								"productCode": 1011723,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 3.74
							},
							{
								"productCode": 1012320,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.36
							},
							{
								"productCode": 1012363,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.97
							},
							{
								"productCode": 1012444,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 156.46
							},
							{
								"productCode": 1012770,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.02
							},
							{
								"productCode": 1012924,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 33.72
							},
							{
								"productCode": 1012959,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 35.25
							},
							{
								"productCode": 1013203,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.97
							},
							{
								"productCode": 1013351,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 9.69
							},
							{
								"productCode": 1014897,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 9.92
							},
							{
								"productCode": 1015184,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 28.49
							},
							{
								"productCode": 1016059,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 165.8
							},
							{
								"productCode": 1016318,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 11,
								"salesValue": 73.76
							},
							{
								"productCode": 1016326,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 17,
								"salesValue": 80.75
							},
							{
								"productCode": 1016377,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 43.91
							},
							{
								"productCode": 1016504,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 48.69
							},
							{
								"productCode": 1016520,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.5
							},
							{
								"productCode": 1016547,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 94.04
							},
							{
								"productCode": 1016601,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 86.27
							},
							{
								"productCode": 1016652,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 155.6
							},
							{
								"productCode": 1016768,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 30.96
							},
							{
								"productCode": 1016865,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 89.29
							},
							{
								"productCode": 1016911,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 76.59
							},
							{
								"productCode": 1016920,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 31.34
							},
							{
								"productCode": 1016946,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.53
							},
							{
								"productCode": 1017020,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 43.5
							},
							{
								"productCode": 1017209,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 17.08
							},
							{
								"productCode": 1017462,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 22.59
							},
							{
								"productCode": 1017489,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 6,
								"salesValue": 88.86
							},
							{
								"productCode": 1017497,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 34.19
							},
							{
								"productCode": 1017616,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 27.06
							},
							{
								"productCode": 1017721,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 100.18
							},
							{
								"productCode": 1017888,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.72
							},
							{
								"productCode": 1017896,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 134.95
							},
							{
								"productCode": 1018051,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.42
							},
							{
								"productCode": 1018388,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.56
							},
							{
								"productCode": 1018728,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 34.57
							},
							{
								"productCode": 1019228,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 23.48
							},
							{
								"productCode": 1019236,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 210.13
							},
							{
								"productCode": 1019260,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 11.49
							},
							{
								"productCode": 1019309,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 6,
								"salesValue": 262.38
							},
							{
								"productCode": 1019490,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 56.84
							},
							{
								"productCode": 1019503,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 27.73
							},
							{
								"productCode": 1019520,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 3.56
							},
							{
								"productCode": 1019678,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.52
							},
							{
								"productCode": 1019732,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 52.72
							},
							{
								"productCode": 1019945,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 8,
								"salesValue": 130.32
							},
							{
								"productCode": 1019996,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 4.94
							},
							{
								"productCode": 1020030,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 43.86
							},
							{
								"productCode": 1020072,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 129.6
							},
							{
								"productCode": 1020196,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.93
							},
							{
								"productCode": 1020200,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.55
							},
							{
								"productCode": 1020218,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 24.5
							},
							{
								"productCode": 1020340,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.56
							},
							{
								"productCode": 1020390,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.09
							},
							{
								"productCode": 1020480,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 12.34
							},
							{
								"productCode": 1020552,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 7,
								"salesValue": 100.59
							},
							{
								"productCode": 1020617,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.66
							},
							{
								"productCode": 1020633,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 18.9
							},
							{
								"productCode": 1020641,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 97.18
							},
							{
								"productCode": 1021060,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.6
							},
							{
								"productCode": 1021117,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 10.36
							},
							{
								"productCode": 1021222,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 93.65
							},
							{
								"productCode": 1021311,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 77.28
							},
							{
								"productCode": 1021435,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 13.65
							},
							{
								"productCode": 1021524,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 21.34
							},
							{
								"productCode": 1021648,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 47.41
							},
							{
								"productCode": 1021826,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.28
							},
							{
								"productCode": 1022385,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.34
							},
							{
								"productCode": 1022571,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 4.29
							},
							{
								"productCode": 1022601,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 28.45
							},
							{
								"productCode": 1022644,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 22.95
							},
							{
								"productCode": 1022679,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 53.99
							},
							{
								"productCode": 1022733,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 153.75
							},
							{
								"productCode": 1022822,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 27.46
							},
							{
								"productCode": 1023152,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 118.2
							},
							{
								"productCode": 1023233,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.99
							},
							{
								"productCode": 1023276,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 9.64
							},
							{
								"productCode": 1023365,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.19
							},
							{
								"productCode": 1023420,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 79.74
							},
							{
								"productCode": 1023608,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 10.42
							},
							{
								"productCode": 1024086,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.59
							},
							{
								"productCode": 1024213,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 50.72
							},
							{
								"productCode": 1024396,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 93.12
							},
							{
								"productCode": 1024400,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 114.96
							},
							{
								"productCode": 1024434,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 27.36
							},
							{
								"productCode": 1024493,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 28.52
							},
							{
								"productCode": 1024507,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 12.86
							},
							{
								"productCode": 1024833,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 64.49
							},
							{
								"productCode": 1025120,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.4
							},
							{
								"productCode": 1026305,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 29.84
							},
							{
								"productCode": 1026984,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.74
							},
							{
								"productCode": 1027034,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 69.82
							},
							{
								"productCode": 1027069,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.07
							},
							{
								"productCode": 1028332,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 258.01
							},
							{
								"productCode": 1028626,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 16.85
							},
							{
								"productCode": 1028685,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 20,
								"salesValue": 68.2
							},
							{
								"productCode": 1029312,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 12.58
							},
							{
								"productCode": 1029355,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 61.65
							},
							{
								"productCode": 1029410,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 39.24
							},
							{
								"productCode": 1030140,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.87
							},
							{
								"productCode": 1030299,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 49.3
							},
							{
								"productCode": 1030400,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 13.92
							},
							{
								"productCode": 1030566,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 36.07
							},
							{
								"productCode": 1030728,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 47.19
							},
							{
								"productCode": 1030744,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 45.02
							},
							{
								"productCode": 1031074,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 4.66
							},
							{
								"productCode": 1031309,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.98
							},
							{
								"productCode": 1031821,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 10.24
							},
							{
								"productCode": 1031988,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 49.66
							},
							{
								"productCode": 1032070,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.64
							},
							{
								"productCode": 1032135,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 13.28
							},
							{
								"productCode": 1032151,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.9
							},
							{
								"productCode": 1032496,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.33
							},
							{
								"productCode": 1032518,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.3
							},
							{
								"productCode": 1032674,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 11.62
							},
							{
								"productCode": 1032747,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 10.92
							},
							{
								"productCode": 1033298,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.95
							},
							{
								"productCode": 1033867,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 36.77
							},
							{
								"productCode": 1034383,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.42
							},
							{
								"productCode": 1034448,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 47.87
							},
							{
								"productCode": 1034731,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 57.96
							},
							{
								"productCode": 1034839,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.29
							},
							{
								"productCode": 1034847,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 110.55
							},
							{
								"productCode": 1035088,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.59
							},
							{
								"productCode": 1035258,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.44
							},
							{
								"productCode": 1035266,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 39.64
							},
							{
								"productCode": 1035525,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 266.36
							},
							{
								"productCode": 1035789,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 21.2
							},
							{
								"productCode": 1035940,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 59.94
							},
							{
								"productCode": 1036220,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.96
							},
							{
								"productCode": 1036483,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 32.1
							},
							{
								"productCode": 1036807,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.57
							},
							{
								"productCode": 1037072,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 48.96
							},
							{
								"productCode": 1037250,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 11.96
							},
							{
								"productCode": 1037692,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 142.32
							},
							{
								"productCode": 1038591,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 151.6
							},
							{
								"productCode": 1039750,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.55
							},
							{
								"productCode": 1039989,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 9.9
							},
							{
								"productCode": 1040200,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 28.87
							},
							{
								"productCode": 1040430,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.1
							},
							{
								"productCode": 1040782,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 15.3
							},
							{
								"productCode": 1040839,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 118
							},
							{
								"productCode": 1041916,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 31.84
							},
							{
								"productCode": 1042742,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.72
							},
							{
								"productCode": 1042882,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 19.16
							},
							{
								"productCode": 1042890,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.33
							},
							{
								"productCode": 1043013,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.49
							},
							{
								"productCode": 1043757,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.46
							},
							{
								"productCode": 1043897,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.6
							},
							{
								"productCode": 1043943,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 109.54
							},
							{
								"productCode": 1044265,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 46.5
							},
							{
								"productCode": 1044290,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 13.16
							},
							{
								"productCode": 1045008,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 154.87
							},
							{
								"productCode": 1045695,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 33.62
							},
							{
								"productCode": 1045865,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.68
							},
							{
								"productCode": 1046047,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 154.99
							},
							{
								"productCode": 1046128,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 61.7
							},
							{
								"productCode": 1046527,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 31.34
							},
							{
								"productCode": 1046640,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 61.74
							},
							{
								"productCode": 1046691,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 42.17
							},
							{
								"productCode": 1047078,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 5.48
							},
							{
								"productCode": 1047230,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.99
							},
							{
								"productCode": 1047450,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 7.22
							},
							{
								"productCode": 1047922,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.39
							},
							{
								"productCode": 1048678,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.65
							},
							{
								"productCode": 1048856,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 21.33
							},
							{
								"productCode": 1049119,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.71
							},
							{
								"productCode": 1049194,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.79
							},
							{
								"productCode": 1053493,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 3.81
							},
							{
								"productCode": 1054244,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 32.1
							},
							{
								"productCode": 1054732,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 15.18
							},
							{
								"productCode": 1055607,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.81
							},
							{
								"productCode": 1055640,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 68.87
							},
							{
								"productCode": 1056808,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.23
							},
							{
								"productCode": 1057537,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.74
							},
							{
								"productCode": 1058614,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 75.91
							},
							{
								"productCode": 1062816,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 16.78
							},
							{
								"productCode": 1064584,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 52.02
							},
							{
								"productCode": 1065807,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 4.31
							},
							{
								"productCode": 1065998,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 2.87
							},
							{
								"productCode": 1066684,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.31
							},
							{
								"productCode": 1067273,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 1.96
							},
							{
								"productCode": 1071653,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 16.1
							},
							{
								"productCode": 1077082,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 24.69
							},
							{
								"productCode": 1078119,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 110.18
							},
							{
								"productCode": 1086626,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 54.91
							},
							{
								"productCode": 1086677,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 41.56
							},
							{
								"productCode": 1086758,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 24,
								"salesValue": 460.21
							},
							{
								"productCode": 1087940,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.39
							},
							{
								"productCode": 1088009,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 40.86
							},
							{
								"productCode": 1090194,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 7.68
							},
							{
								"productCode": 1090208,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 6.8
							},
							{
								"productCode": 1091310,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 2.11
							},
							{
								"productCode": 1091565,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 59.64
							},
							{
								"productCode": 1092669,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 9.86
							},
							{
								"productCode": 1093347,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 270.64
							},
							{
								"productCode": 1093487,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 29.22
							},
							{
								"productCode": 1093576,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 123.39
							},
							{
								"productCode": 1093584,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 150.8
							},
							{
								"productCode": 1093770,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.4
							},
							{
								"productCode": 1093851,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 11,
								"salesValue": 48.51
							},
							{
								"productCode": 1093991,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.49
							},
							{
								"productCode": 1094017,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.8
							},
							{
								"productCode": 1094068,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 14,
								"salesValue": 21.28
							},
							{
								"productCode": 1094220,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.09
							},
							{
								"productCode": 1094416,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 17.68
							},
							{
								"productCode": 1094505,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 6,
								"salesValue": 13.26
							},
							{
								"productCode": 1094599,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 169.95
							},
							{
								"productCode": 1094602,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 508.32
							},
							{
								"productCode": 1094670,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 72.78
							},
							{
								"productCode": 1094734,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 46.74
							},
							{
								"productCode": 1094750,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 162.72
							},
							{
								"productCode": 1094971,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 572.06
							},
							{
								"productCode": 1095021,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 48.1
							},
							{
								"productCode": 1095080,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 9.21
							},
							{
								"productCode": 1095153,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.74
							},
							{
								"productCode": 1095250,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 54.52
							},
							{
								"productCode": 1095323,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.93
							},
							{
								"productCode": 1095366,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 9,
								"salesValue": 323.38
							},
							{
								"productCode": 1095404,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 12.39
							},
							{
								"productCode": 1095455,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 203.68
							},
							{
								"productCode": 1095480,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.3
							},
							{
								"productCode": 1095536,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.86
							},
							{
								"productCode": 1095544,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 8,
								"salesValue": 19.76
							},
							{
								"productCode": 1095617,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 28.47
							},
							{
								"productCode": 1095625,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 57.38
							},
							{
								"productCode": 1096028,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 41.03
							},
							{
								"productCode": 1096087,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 77.13
							},
							{
								"productCode": 1096192,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 36.84
							},
							{
								"productCode": 1096621,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 12.27
							},
							{
								"productCode": 1096648,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 33.49
							},
							{
								"productCode": 1096702,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 279.3
							},
							{
								"productCode": 1096796,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 94.08
							},
							{
								"productCode": 1096907,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 22.71
							},
							{
								"productCode": 1096966,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 63.79
							},
							{
								"productCode": 1097091,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 58.7
							},
							{
								"productCode": 1097199,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 23.27
							},
							{
								"productCode": 1097415,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.45
							},
							{
								"productCode": 1097466,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 18.06
							},
							{
								"productCode": 1097733,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 55.24
							},
							{
								"productCode": 1097741,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.08
							},
							{
								"productCode": 1098250,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.43
							},
							{
								"productCode": 1098373,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 69.1
							},
							{
								"productCode": 1098551,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.4
							},
							{
								"productCode": 1098594,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 22.36
							},
							{
								"productCode": 1098772,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.3
							},
							{
								"productCode": 1098870,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 67.88
							},
							{
								"productCode": 1098977,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 24.55
							},
							{
								"productCode": 1099124,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 71.87
							},
							{
								"productCode": 1099639,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 72.51
							},
							{
								"productCode": 1099906,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 30.89
							},
							{
								"productCode": 1100360,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.49
							},
							{
								"productCode": 1100599,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 15.96
							},
							{
								"productCode": 1100858,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 9.86
							},
							{
								"productCode": 1100912,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 66.93
							},
							{
								"productCode": 1101412,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.29
							},
							{
								"productCode": 1102710,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 27.79
							},
							{
								"productCode": 1108042,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 21.93
							},
							{
								"productCode": 1108298,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 11.68
							},
							{
								"productCode": 1108379,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 36.64
							},
							{
								"productCode": 1108654,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 113.28
							},
							{
								"productCode": 1108751,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 127.14
							},
							{
								"productCode": 1110128,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 37.68
							},
							{
								"productCode": 1110381,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 45.46
							},
							{
								"productCode": 1110730,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 48.06
							},
							{
								"productCode": 1111337,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 38.94
							},
							{
								"productCode": 1111981,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 85.62
							},
							{
								"productCode": 1112007,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.36
							},
							{
								"productCode": 1113186,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 35.85
							},
							{
								"productCode": 1113496,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.94
							},
							{
								"productCode": 1113909,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.71
							},
							{
								"productCode": 1113941,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.61
							},
							{
								"productCode": 1114468,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.64
							},
							{
								"productCode": 1114670,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 21.37
							},
							{
								"productCode": 1115340,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.52
							},
							{
								"productCode": 1115570,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 40.61
							},
							{
								"productCode": 1116029,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 37.9
							},
							{
								"productCode": 1116193,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 30.85
							},
							{
								"productCode": 1118102,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 380.46
							},
							{
								"productCode": 1118692,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 83.58
							},
							{
								"productCode": 1119303,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 28.12
							},
							{
								"productCode": 1119419,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 87.32
							},
							{
								"productCode": 1119451,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 96.9
							},
							{
								"productCode": 1120522,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 33.24
							},
							{
								"productCode": 1120573,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 35.4
							},
							{
								"productCode": 1120581,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 12.41
							},
							{
								"productCode": 1120700,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 18.9
							},
							{
								"productCode": 1120778,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.94
							},
							{
								"productCode": 1121162,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 58.99
							},
							{
								"productCode": 1121324,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 7.04
							},
							{
								"productCode": 1121723,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 6.32
							},
							{
								"productCode": 1121855,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.12
							},
							{
								"productCode": 1121863,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 11.2
							},
							{
								"productCode": 1121901,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.18
							},
							{
								"productCode": 1122053,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 17.48
							},
							{
								"productCode": 1122070,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 20.1
							},
							{
								"productCode": 1122100,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 13,
								"salesValue": 40.95
							},
							{
								"productCode": 1122428,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 54.76
							},
							{
								"productCode": 1122630,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 44.59
							},
							{
								"productCode": 1122762,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 181.36
							},
							{
								"productCode": 1122886,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 66.97
							},
							{
								"productCode": 1123106,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 176.92
							},
							{
								"productCode": 1123319,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 67.64
							},
							{
								"productCode": 1123459,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 138.02
							},
							{
								"productCode": 1123726,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 64.96
							},
							{
								"productCode": 1124846,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 6,
								"salesValue": 23.55
							},
							{
								"productCode": 1125591,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 2.75
							},
							{
								"productCode": 1126237,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 140.26
							},
							{
								"productCode": 1126768,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 4.66
							},
							{
								"productCode": 1127195,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 664
							},
							{
								"productCode": 1127209,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 1691.74
							},
							{
								"productCode": 1127403,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 28.4
							},
							{
								"productCode": 1127438,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 56.96
							},
							{
								"productCode": 1127500,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 22.2
							},
							{
								"productCode": 1127578,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 24.97
							},
							{
								"productCode": 1129058,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 18.7
							},
							{
								"productCode": 1129376,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 35.14
							},
							{
								"productCode": 1129562,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 14.7
							},
							{
								"productCode": 1129813,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 8,
								"salesValue": 23.28
							},
							{
								"productCode": 1129945,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 26.58
							},
							{
								"productCode": 1130331,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 221.4
							},
							{
								"productCode": 1130781,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 130.16
							},
							{
								"productCode": 1131125,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 103.56
							},
							{
								"productCode": 1131133,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 42.23
							},
							{
								"productCode": 1131796,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 34.05
							},
							{
								"productCode": 1131850,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 220.09
							},
							{
								"productCode": 1133063,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.64
							},
							{
								"productCode": 1133349,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.36
							},
							{
								"productCode": 1133535,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.07
							},
							{
								"productCode": 1133586,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.5
							},
							{
								"productCode": 1134035,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 10.58
							},
							{
								"productCode": 1135708,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 75.83
							},
							{
								"productCode": 1150820,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 36.88
							},
							{
								"productCode": 1150839,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 70.66
							},
							{
								"productCode": 1150863,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 30.58
							},
							{
								"productCode": 1165453,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.38
							},
							{
								"productCode": 1166042,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.79
							},
							{
								"productCode": 1167278,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 12.34
							},
							{
								"productCode": 1167294,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.08
							},
							{
								"productCode": 1167391,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.16
							},
							{
								"productCode": 1167766,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.99
							},
							{
								"productCode": 1167987,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 10,
								"salesValue": 123.2
							},
							{
								"productCode": 1169807,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.1
							},
							{
								"productCode": 1169866,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 12.94
							},
							{
								"productCode": 1170244,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 34.17
							},
							{
								"productCode": 1170376,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 33.55
							},
							{
								"productCode": 1170414,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.39
							},
							{
								"productCode": 1170546,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 14.42
							},
							{
								"productCode": 1170562,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.58
							},
							{
								"productCode": 1170570,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.57
							},
							{
								"productCode": 1170708,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.97
							},
							{
								"productCode": 1170864,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 14.74
							},
							{
								"productCode": 1172948,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 21.78
							},
							{
								"productCode": 1173235,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.31
							},
							{
								"productCode": 1173243,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.12
							},
							{
								"productCode": 1173391,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 13,
								"salesValue": 204.38
							},
							{
								"productCode": 1173430,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 14.39
							},
							{
								"productCode": 1173570,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 60.52
							},
							{
								"productCode": 1173804,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 147.21
							},
							{
								"productCode": 1174177,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 33.55
							},
							{
								"productCode": 1174193,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 89.9
							},
							{
								"productCode": 1174215,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 34.99
							},
							{
								"productCode": 1174231,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 52.18
							},
							{
								"productCode": 1174339,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 89.9
							},
							{
								"productCode": 1177354,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 48.68
							},
							{
								"productCode": 1177397,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 102.27
							},
							{
								"productCode": 1178075,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 70.9
							},
							{
								"productCode": 1178482,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 79.6
							},
							{
								"productCode": 1178601,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 44.56
							},
							{
								"productCode": 1178938,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 22.78
							},
							{
								"productCode": 1179357,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 5.64
							},
							{
								"productCode": 1179411,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 9.96
							},
							{
								"productCode": 1179748,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 3.32
							},
							{
								"productCode": 1179845,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.23
							},
							{
								"productCode": 1179977,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 46.54
							},
							{
								"productCode": 1179985,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 9,
								"salesValue": 490.68
							},
							{
								"productCode": 1180134,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 184.29
							},
							{
								"productCode": 1180720,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.86
							},
							{
								"productCode": 1181300,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 36.89
							},
							{
								"productCode": 1181521,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 103.49
							},
							{
								"productCode": 1182013,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 81.84
							},
							{
								"productCode": 1182021,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 100.87
							},
							{
								"productCode": 1182145,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 69.9
							},
							{
								"productCode": 1182501,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 29.9
							},
							{
								"productCode": 1182587,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 49.9
							},
							{
								"productCode": 1182625,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 49.99
							},
							{
								"productCode": 1182986,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 134.99
							},
							{
								"productCode": 1183087,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 77.82
							},
							{
								"productCode": 1183109,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.79
							},
							{
								"productCode": 1183117,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.79
							},
							{
								"productCode": 1183346,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.66
							},
							{
								"productCode": 1183427,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 20.17
							},
							{
								"productCode": 1183702,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.38
							},
							{
								"productCode": 1183729,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 26.97
							},
							{
								"productCode": 1183893,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.1
							},
							{
								"productCode": 1183990,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 259.9
							},
							{
								"productCode": 1184342,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 74.55
							},
							{
								"productCode": 1184555,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 55.99
							},
							{
								"productCode": 1185500,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 45.4
							},
							{
								"productCode": 1185713,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 30.59
							},
							{
								"productCode": 1189212,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.39
							},
							{
								"productCode": 1189557,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.97
							},
							{
								"productCode": 1189824,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 12.99
							},
							{
								"productCode": 1189948,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 26.09
							},
							{
								"productCode": 1189999,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 6.44
							},
							{
								"productCode": 1190334,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 3.59
							},
							{
								"productCode": 1190652,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 32.94
							},
							{
								"productCode": 1193678,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 63.41
							},
							{
								"productCode": 1193945,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 185.35
							},
							{
								"productCode": 1194020,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 90.8
							},
							{
								"productCode": 1194259,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 239.8
							},
							{
								"productCode": 1194542,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 47.91
							},
							{
								"productCode": 1198769,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 11.21
							},
							{
								"productCode": 1198777,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 10.55
							},
							{
								"productCode": 1200496,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 6.48
							},
							{
								"productCode": 1200542,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 5.02
							},
							{
								"productCode": 1200593,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.09
							},
							{
								"productCode": 1201220,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.17
							},
							{
								"productCode": 1202120,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 48.1
							},
							{
								"productCode": 1202138,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 50.72
							},
							{
								"productCode": 1202162,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 176.6
							},
							{
								"productCode": 1202260,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 24.98
							},
							{
								"productCode": 1202561,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 3.84
							},
							{
								"productCode": 1202669,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 8,
								"salesValue": 266.32
							},
							{
								"productCode": 1202677,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 28.49
							},
							{
								"productCode": 1202693,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 161.92
							},
							{
								"productCode": 1202740,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 31.02
							},
							{
								"productCode": 1202944,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 64.52
							},
							{
								"productCode": 1203088,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 24.68
							},
							{
								"productCode": 1203142,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 4,
								"salesValue": 144.96
							},
							{
								"productCode": 1203223,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 5.12
							},
							{
								"productCode": 1203401,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 59.15
							},
							{
								"productCode": 1203452,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 77.89
							},
							{
								"productCode": 1203606,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 33.43
							},
							{
								"productCode": 1203630,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.98
							},
							{
								"productCode": 1203649,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.18
							},
							{
								"productCode": 1203657,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.34
							},
							{
								"productCode": 1204114,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 150.05
							},
							{
								"productCode": 1204130,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 15.36
							},
							{
								"productCode": 1204378,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 193.5
							},
							{
								"productCode": 1204483,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 9.87
							},
							{
								"productCode": 1204505,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 47.22
							},
							{
								"productCode": 1204610,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 31.53
							},
							{
								"productCode": 1204653,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 61.68
							},
							{
								"productCode": 1204696,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 30.32
							},
							{
								"productCode": 1204726,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 5,
								"salesValue": 71.05
							},
							{
								"productCode": 1204807,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 37.54
							},
							{
								"productCode": 1204939,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 22.5
							},
							{
								"productCode": 1205498,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 5.67
							},
							{
								"productCode": 1205803,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 19.02
							},
							{
								"productCode": 1206281,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 59.99
							},
							{
								"productCode": 1210823,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 4.56
							},
							{
								"productCode": 1211560,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 6.29
							},
							{
								"productCode": 1211692,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 106.99
							},
							{
								"productCode": 1212036,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 13.82
							},
							{
								"productCode": 1212087,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 50.91
							},
							{
								"productCode": 1212605,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 124.56
							},
							{
								"productCode": 1213199,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 42.04
							},
							{
								"productCode": 1213520,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 141.01
							},
							{
								"productCode": 1213547,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 17.99
							},
							{
								"productCode": 1213679,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 78.99
							},
							{
								"productCode": 1213687,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 239.8
							},
							{
								"productCode": 1214462,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 41.32
							},
							{
								"productCode": 1215051,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 275.45
							},
							{
								"productCode": 1215434,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 69.9
							},
							{
								"productCode": 1215558,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.63
							},
							{
								"productCode": 1215574,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 5.3
							},
							{
								"productCode": 1215698,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 25.33
							},
							{
								"productCode": 1215817,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.1
							},
							{
								"productCode": 1216228,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 13.94
							},
							{
								"productCode": 1216767,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 114.48
							},
							{
								"productCode": 1217160,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 75.98
							},
							{
								"productCode": 1217348,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 104.1
							},
							{
								"productCode": 1217356,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 3,
								"salesValue": 145.02
							},
							{
								"productCode": 1217488,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 31.53
							},
							{
								"productCode": 1217496,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 21.96
							},
							{
								"productCode": 1219316,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 118.74
							},
							{
								"productCode": 1219561,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 113
							},
							{
								"productCode": 1219669,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 40.38
							},
							{
								"productCode": 1220403,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 7,
								"salesValue": 60.12
							},
							{
								"productCode": 1220586,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 24.23
							},
							{
								"productCode": 1220640,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 15.43
							},
							{
								"productCode": 1222031,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 31.34
							},
							{
								"productCode": 1222163,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 34.32
							},
							{
								"productCode": 1222376,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 71.24
							},
							{
								"productCode": 1222481,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 54.9
							},
							{
								"productCode": 1222600,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 32.49
							},
							{
								"productCode": 1224743,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 24.31
							},
							{
								"productCode": 1224786,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 24.75
							},
							{
								"productCode": 1225189,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 52.62
							},
							{
								"productCode": 1225197,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 7,
								"salesValue": 30.66
							},
							{
								"productCode": 1225588,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 45.96
							},
							{
								"productCode": 1226967,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 8.86
							},
							{
								"productCode": 1227394,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 16.36
							},
							{
								"productCode": 1227467,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.08
							},
							{
								"productCode": 1227505,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 22.24
							},
							{
								"productCode": 1227564,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 1,
								"salesValue": 7.17
							},
							{
								"productCode": 1227718,
								"salesDate": "05/05/2021 00:00:00",
								"salesQuantity": 2,
								"salesValue": 64.12
							}
						],
						"quantityItems": 678,
						"item": null
					}';
					}
			}
			else {
					$client = \Config\Services::curlrequest();
					$response = $client->request('GET', "http://ultraclinica.totvscloud.com.br:2000/RMS/RMSSERVICES/ReportWebAPI/api/v1/SaleHistory/GetByDate?filial=1007&dataVendaInicio={$initial_date}&dataVendaFim={$final_date}", [ 'headers' => ['Content-Type: application/vnd.api+json', 'Accept: application/vnd.api+json'] ])->getBody();
			}
			return $response;
	}

	// Função que popula os dados de margens atuais por departamentos
	public function margin() {
			$data = [];
			$model_products = new ProductsModel();
			$sales_date = $this->request->getVar('date');
			$items = json_decode($this->response($sales_date, $sales_date))->items;
			$skus = [];
			foreach($items as $row) {
					array_push($skus, $row->productCode);
			}
			$fields = $model_products->getProductFields($skus, ['sku as productCode', 'price_cost', 'department', 'category']);

			// Cria um array auxiliar que contém o "inner join" entre a resposta da API e a consulta no banco de dados
			$inner_join = $this->inner_join($items, $fields, 'productCode');

			$margin_views = ['Geral', 'MEDICAMENTO', 'NAO MEDICAMENTO', 'PERFUMARIA'];

			foreach($margin_views as $margin_view) {
					// Variáveis de totais
					$total_value_vendas = 0;
					$total_sales_quantity = 0;
					$total_margin = 0;
					$i=0;
					$labels = [];
					foreach($inner_join as $item) {
							if($margin_view == 'Geral') {
									$total_value_vendas += $item['salesValue'];
									$total_sales_quantity += $item['salesQuantity'];
									$total_margin += $item['salesValue'] - $item['price_cost'] * $item['salesQuantity'];

									// Salva as margens de cada departamento
									$departments = ['MEDICAMENTO', 'NAO MEDICAMENTO', 'PERFUMARIA'];

									// Verifica se o departamento deve ser exibido
									if(in_array($item['department'], $departments)) {
											if(!in_array($item['department'], $labels)) { // Se ainda não foi inserido, insere agora
													array_push($labels, $item['department']);
											}
									}
							}
							else {
									if($item['department'] == $margin_view) {
											$total_value_vendas += $item['salesValue'];
											$total_sales_quantity += $item['salesQuantity'];
											$total_margin += ($item['salesValue'] - $item['price_cost'] * $item['salesQuantity']);

											// Salva as margens de cada categoria
											$categorias_despreziveis = ['', '#N/D'];
											// Verifica se a categoria é diferente das categorias desprezíveis
											if(!in_array($item['category'], $categorias_despreziveis)) {
													// Verifica se a categoria já foi inserida no array de categorias do departamento
													if(!in_array($item['category'], $labels)) { // Se ainda não foi inserido, insere agora
															array_push($labels, $item['category']);
													}
											}
									}
							}
					}

					$labels_data = [];

					// Configura os dados para exibir no gráfico
					foreach($labels as $label) {
							if($margin_view == 'Geral') {
									// Pega todos os departamentos e seta as margens totais de cada uma
									$products_ = array_filter($inner_join, function($item) use($label) {
											return $item['department'] == $label;
									});
							}
							else {
									// Pega todas as categorias e seta as margens totais de cada uma
									$products_ = array_filter($inner_join, function($item) use($label) {
											return $item['category'] == $label;
									});
							}
							$total_price_cost = array_sum(array_map(function ($ar) {return $ar['price_cost'];}, $products_));
							$total_value_vendas_ = array_sum(array_map(function ($ar) {return $ar['salesValue'];}, $products_));
							array_push($labels_data, (($total_value_vendas_ - $total_price_cost) / $total_value_vendas_) * 100);
					}

					$total_margin = ($total_value_vendas != 0) ? ($total_margin / $total_value_vendas) * 100 : 0;
					$margin_view_title = str_replace(" ", "_", strtolower($margin_view));
					$data[$margin_view_title."_margins"] = array('total_margin_day' => number_to_amount($total_margin, 2, 'pt_BR')."%",
																											 'total_sales_value_day' => number_to_currency($total_value_vendas, 'BRL', null, 2),
																											 'total_sales_qtd_day' => $total_sales_quantity,
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
