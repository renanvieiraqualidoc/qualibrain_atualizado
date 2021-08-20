<?php

namespace App\Controllers;
use App\Models\SalesModel;
use App\Models\ProductsModel;

class Faturamento extends BaseController
{
		public function index($data = []) {
				$data['categories'] = $this->dynamicMenu();
				$fat_data = array();
				for($i=0; $i<3; $i++) {
						array_push($fat_data, $this->getFatByMonth($i));
				}
				$projection = $this->getFatByMonth(2);
				$current_month_gross_billing = end($fat_data);
				$actual_day = intval(date('d'));
				$qty_days_month = $current_month_gross_billing['qty_days_month'];
				$projection['gross_billing'] = ($current_month_gross_billing['gross_billing']/$actual_day)*$qty_days_month;
				$projection['qtd_orders'] = round(($current_month_gross_billing['qtd_orders']/$actual_day)*$qty_days_month);
				$projection['tkm'] = round($projection['gross_billing']/$projection['qtd_orders']);
				$projection['comparative_previous_month'] = (($projection['gross_billing']/$fat_data[1]['gross_billing'])-1)*100;
				$projection['margin'] = $projection['gross_billing'] != 0 ? ($projection['gross_billing'] - ($current_month_gross_billing['price_cost']/$actual_day)*$qty_days_month)/$projection['gross_billing']*100 : 0;
				array_push($fat_data, $projection);
				$data['months'] = $fat_data;
				$response = json_decode($this->response(date("Y-m-d", strtotime("-1 month")), date('Y-m-d')))->items;
				$data['dates'] = '["'.implode('", "', array_map(function ($ar) { return explode(" ", $ar)[0]; }, array_column($response, 'salesDate'))).'"]';
				$data['sales'] = '['.implode(', ', array_column($response, 'salesQuantity')).']';
				$data['gross_billings'] = '['.implode(', ', array_column($response, 'salesValue')).']';
				echo view('faturamento', $data);
		}

		public function getFatByMonth($index) {
				$sales_model = new SalesModel();
				$data = array();
				$obj = $this->getQtyOrdersAndMonth($index);
				$response_previous_month = json_decode($this->response($obj['initial_date_previous_month'], $obj['final_date_previous_month']))->items;
				$gross_billing_previous_month = array_sum(array_column($response_previous_month, 'salesValue'));
				$data = array('month' => $obj['month'],
											'qty_days_month' => $obj['qty_days_month'],
											'gross_billing' => $obj['gross_billing'],
											'qtd_orders' => $obj['qtd'],
											'tkm' => $obj['tkm'],
											'price_cost' => $obj['price_cost'],
											'comparative_previous_month' => round(($obj['gross_billing']/$gross_billing_previous_month - 1)*100),
											'margin' => $obj['gross_billing'] != 0 ? ($obj['gross_billing'] - $obj['price_cost'])/$obj['gross_billing']*100 : 0);
				return $data;
		}

		public function response($initial_date, $final_date) {
				if(base_url() == 'http://qualibrain.local.com') {
						$response = '{
														  "items": [
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "01/06/2021 00:00:00",
														      "salesQuantity": 1048,
														      "salesValue": 116763.91,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 111.42
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "02/06/2021 00:00:00",
														      "salesQuantity": 872,
														      "salesValue": 94336.31,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 108.18
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "03/06/2021 00:00:00",
														      "salesQuantity": 874,
														      "salesValue": 94010.68,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 107.56
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "04/06/2021 00:00:00",
														      "salesQuantity": 802,
														      "salesValue": 83355.85,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 103.93
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "05/06/2021 00:00:00",
														      "salesQuantity": 694,
														      "salesValue": 63894.87,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 92.07
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "06/06/2021 00:00:00",
														      "salesQuantity": 648,
														      "salesValue": 64273.18,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 99.19
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "07/06/2021 00:00:00",
														      "salesQuantity": 1012,
														      "salesValue": 110354.22,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 109.05
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "08/06/2021 00:00:00",
														      "salesQuantity": 1132,
														      "salesValue": 122871.19,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 108.54
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "09/06/2021 00:00:00",
														      "salesQuantity": 985,
														      "salesValue": 96098.17,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 97.56
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "10/06/2021 00:00:00",
														      "salesQuantity": 992,
														      "salesValue": 105436.53,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 106.29
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "11/06/2021 00:00:00",
														      "salesQuantity": 900,
														      "salesValue": 97868.51,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 108.74
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "12/06/2021 00:00:00",
														      "salesQuantity": 612,
														      "salesValue": 68679.83,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 112.22
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "13/06/2021 00:00:00",
														      "salesQuantity": 632,
														      "salesValue": 69297.85,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 109.65
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "14/06/2021 00:00:00",
														      "salesQuantity": 1082,
														      "salesValue": 113003.3,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 104.44
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "15/06/2021 00:00:00",
														      "salesQuantity": 1084,
														      "salesValue": 115661.75,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 106.7
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "16/06/2021 00:00:00",
														      "salesQuantity": 901,
														      "salesValue": 101345.25,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 112.48
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "17/06/2021 00:00:00",
														      "salesQuantity": 1062,
														      "salesValue": 114669.76,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 107.98
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "18/06/2021 00:00:00",
														      "salesQuantity": 832,
														      "salesValue": 86150.56,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 103.55
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "19/06/2021 00:00:00",
														      "salesQuantity": 569,
														      "salesValue": 63367.84,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 111.37
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "20/06/2021 00:00:00",
														      "salesQuantity": 529,
														      "salesValue": 50352.48,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 95.18
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "21/06/2021 00:00:00",
														      "salesQuantity": 929,
														      "salesValue": 98369.86,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 105.89
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "22/06/2021 00:00:00",
														      "salesQuantity": 967,
														      "salesValue": 102904.57,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 106.42
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "23/06/2021 00:00:00",
														      "salesQuantity": 1046,
														      "salesValue": 107949.53,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 103.2
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "24/06/2021 00:00:00",
														      "salesQuantity": 974,
														      "salesValue": 99866.41,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 102.53
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "25/06/2021 00:00:00",
														      "salesQuantity": 1033,
														      "salesValue": 107705.72,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 104.26
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "26/06/2021 00:00:00",
														      "salesQuantity": 548,
														      "salesValue": 57716.82,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 105.32
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "27/06/2021 00:00:00",
														      "salesQuantity": 760,
														      "salesValue": 77124.81,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 101.48
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "28/06/2021 00:00:00",
														      "salesQuantity": 1231,
														      "salesValue": 139990.34,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 113.72
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "29/06/2021 00:00:00",
														      "salesQuantity": 1197,
														      "salesValue": 123629.62,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 103.28
														    },
														    {
														      "siteOrder": null,
														      "productCode": 0,
														      "salesDate": "30/06/2021 00:00:00",
														      "salesQuantity": 1129,
														      "salesValue": 117780.22,
														      "pmz": 0,
														      "clientDocument": 0,
														      "tkm": 104.32
														    }
														  ],
														  "quantityItems": 30,
														  "item": null
														}';
				}
				else {
						$client = \Config\Services::curlrequest();
						$response = $client->request('GET', "http://ultraclinica.totvscloud.com.br:2000/RMS/RMSSERVICES/ReportWebAPI/api/v1/SaleHistory/GetByDay?filial=1007&dataVendaInicio=".$initial_date."&dataVendaFim=".$final_date, [ 'headers' => ['Content-Type: application/vnd.api+json', 'Accept: application/vnd.api+json'] ])->getBody();
				}
				return $response;
		}

		public function getQtyOrdersAndMonth($index) {
				$substract = 2 - $index;
				$initial_date = date("Y-m-01", strtotime("-$substract months"));
				$final_date = date("Y-m-t", strtotime($initial_date));
				$sales = new SalesModel();
				$price_cost = $sales->getPriceCostSalesByMonth(date("m", strtotime($initial_date)), date("Y", strtotime($initial_date)));
				$response = json_decode($this->response($initial_date, $final_date));
				$items = $response->items;
				$months = array(1 => "Jan", 2 => "Fev", 3 => "Mar", 4 => "Abr",
												5 => "Mai", 6 => "Jun", 7 => "Jul", 8 => "Ago",
												9 => "Set", 10 => "Out", 11 => "Nov", 12 => "Dez");
				return array('gross_billing' => array_sum(array_column($items, 'salesValue')),
										 'price_cost' => $price_cost,
										 'qtd' => array_sum(array_column($items, 'salesQuantity')),
										 'tkm' => round(array_sum(array_column($items, 'tkm'))/$response->quantityItems),
										 'initial_date_previous_month' => date('Y-m-01', strtotime($initial_date."-1 month")),
										 'final_date_previous_month' => date('Y-m-t', strtotime($initial_date."-1 month")),
										 'qty_days_month' => date("t", strtotime($initial_date)),
										 'month' => $months[intval(date('m', strtotime($initial_date)))]);
		}

		public function getGrossBillingDepto() {
				$sales = new SalesModel();
				$array = $sales->getTotalGrossBillingByDepto($this->request->getVar('period'), $this->request->getVar('type'));
				return json_encode(array('labels' => array_column($array, 'labels'),
										 						 'data' => array_column($array, 'data')));
		}

		public function getGrossBillingCategory() {
				$sales = new SalesModel();
				$array = $sales->getTotalGrossBillingByCategory($this->request->getVar('period'));
				return json_encode(array('labels' => array_column($array, 'labels'),
										 						 'data' => array_column($array, 'data')));
		}

		public function getAccumulatedMarginGrossBilling() {
				// $initial_date = date("Y-m-01");
				// $final_date = date("Y-m-t", strtotime($initial_date));
				// if(base_url() == 'http://qualibrain.local.com') {
				// 		$response = '{
				// 											"items": [
				// 												{
				// 								      "codigo": 1066820,
				// 								      "descricao": "Exímia Firmalize Age Complex 30 Sachês",
				// 								      "nroNota": 165473,
				// 								      "dataEmissao": "13/08/2021",
				// 								      "qtdVendida": 1,
				// 								      "prcUnitarioBruto": 120.29,
				// 								      "freteUnitario": 0,
				// 								      "vlrDescontoUnitario": 0,
				// 								      "prcUnitario": 120.29,
				// 								      "prcTotal": 120.29,
				// 								      "vlrIcms": 21.65,
				// 								      "vlrPis": 1.985,
				// 								      "vlrCof": 9.142,
				// 								      "custoUnitario": 76.528,
				// 								      "custoBruto": 109.305,
				// 								      "pmzAtual": 105.19,
				// 								      "percMargem": 3.6,
				// 								      "percMargemPMZ": -6.85,
				// 								      "lucroBruto": 10.985,
				// 								      "lucroBrutoTotal": 10.985
				// 								    },
				// 								    {
				// 								      "codigo": 1066820,
				// 								      "descricao": "Exímia Firmalize Age Complex 30 Sachês",
				// 								      "nroNota": 167516,
				// 								      "dataEmissao": "16/08/2021",
				// 								      "qtdVendida": 2,
				// 								      "prcUnitarioBruto": 120.29,
				// 								      "freteUnitario": 0,
				// 								      "vlrDescontoUnitario": 0,
				// 								      "prcUnitario": 120.29,
				// 								      "prcTotal": 240.58,
				// 								      "vlrIcms": 43.3,
				// 								      "vlrPis": 3.97,
				// 								      "vlrCof": 18.284,
				// 								      "custoUnitario": 76.528,
				// 								      "custoBruto": 142.082,
				// 								      "pmzAtual": 105.19,
				// 								      "percMargem": -29.17,
				// 								      "percMargemPMZ": -6.85,
				// 								      "lucroBruto": -21.792,
				// 								      "lucroBrutoTotal": -43.584
				// 								    },
				// 								    {
				// 								      "codigo": 1067753,
				// 								      "descricao": "Condicionador Amend Pós Progressiva 250ml",
				// 								      "nroNota": 167278,
				// 								      "dataEmissao": "16/08/2021",
				// 								      "qtdVendida": 1,
				// 								      "prcUnitarioBruto": 26.51,
				// 								      "freteUnitario": 0,
				// 								      "vlrDescontoUnitario": 0,
				// 								      "prcUnitario": 26.51,
				// 								      "prcTotal": 26.51,
				// 								      "vlrIcms": 0,
				// 								      "vlrPis": 0,
				// 								      "vlrCof": 0,
				// 								      "custoUnitario": 21.792,
				// 								      "custoBruto": 21.792,
				// 								      "pmzAtual": 21.79,
				// 								      "percMargem": 17.8,
				// 								      "percMargemPMZ": 70.67,
				// 								      "lucroBruto": 4.718,
				// 								      "lucroBrutoTotal": 4.718
				// 								    },
				// 								    {
				// 								      "codigo": 1067770,
				// 								      "descricao": "Creme para Pentear Amend Complete Repair 180g",
				// 								      "nroNota": 167278,
				// 								      "dataEmissao": "16/08/2021",
				// 								      "qtdVendida": 1,
				// 								      "prcUnitarioBruto": 26.51,
				// 								      "freteUnitario": 0,
				// 								      "vlrDescontoUnitario": 0,
				// 								      "prcUnitario": 26.51,
				// 								      "prcTotal": 26.51,
				// 								      "vlrIcms": 0,
				// 								      "vlrPis": 0,
				// 								      "vlrCof": 0,
				// 								      "custoUnitario": 22.417,
				// 								      "custoBruto": 22.417,
				// 								      "pmzAtual": 22.42,
				// 								      "percMargem": 15.44,
				// 								      "percMargemPMZ": 70.04,
				// 								      "lucroBruto": 4.093,
				// 								      "lucroBrutoTotal": 4.093
				// 								    },
				// 								    {
				// 								      "codigo": 1068148,
				// 								      "descricao": "Sabonete em Barra Granado Antisséptico Fresh 90g",
				// 								      "nroNota": 167107,
				// 								      "dataEmissao": "15/08/2021",
				// 								      "qtdVendida": 1,
				// 								      "prcUnitarioBruto": 5.97,
				// 								      "freteUnitario": 0,
				// 								      "vlrDescontoUnitario": 0,
				// 								      "prcUnitario": 5.97,
				// 								      "prcTotal": 5.97,
				// 								      "vlrIcms": 0,
				// 								      "vlrPis": 0,
				// 								      "vlrCof": 0,
				// 								      "custoUnitario": 4.293,
				// 								      "custoBruto": 4.293,
				// 								      "pmzAtual": 4.29,
				// 								      "percMargem": 28.09,
				// 								      "percMargemPMZ": 62.21,
				// 								      "lucroBruto": 1.677,
				// 								      "lucroBrutoTotal": 1.677
				// 								    },
				// 								    {
				// 								      "codigo": 1181866,
				// 								      "descricao": "Desodorante Roll On Nivea Men Invisible for Black & White Fresh 50ml",
				// 								      "nroNota": 163821,
				// 								      "dataEmissao": "12/08/2021",
				// 								      "qtdVendida": 1,
				// 								      "prcUnitarioBruto": 7.19,
				// 								      "freteUnitario": 0,
				// 								      "vlrDescontoUnitario": 0,
				// 								      "prcUnitario": 7.19,
				// 								      "prcTotal": 7.19,
				// 								      "vlrIcms": 0,
				// 								      "vlrPis": 0,
				// 								      "vlrCof": 0,
				// 								      "custoUnitario": 5.134,
				// 								      "custoBruto": 5.134,
				// 								      "pmzAtual": 5.13,
				// 								      "percMargem": 28.6,
				// 								      "percMargemPMZ": 67.05,
				// 								      "lucroBruto": 2.056,
				// 								      "lucroBrutoTotal": 2.056
				// 								    }
				// 								  ],
				// 								  "quantityItems": 6,
				// 								  "item": null
				// 								}';
				// }
				// else {
				// 		$client = \Config\Services::curlrequest();
				// 		$response = $client->request('GET', "http://ultraclinica.totvscloud.com.br:2000/RMS/RMSSERVICES/ReportWebAPI/api/v1/SaleHistory?filial=1007&dataInicial=".$initial_date."&dataFinal=".$final_date, [ 'headers' => ['Content-Type: application/vnd.api+json', 'Accept: application/vnd.api+json'] ])->getBody();
				// }
				// $items = json_decode($response)->items;
				// $model_products = new ProductsModel();
				// $products = $model_products->getProductsByTitle(array_unique(array_column($items, 'descricao')));
				// $categories = array_values(array_unique(array_column($products, 'category')));
				// $data = [];
				// $data['aaData'] = [];
				// foreach($categories as $category) {
				// 		$category_data = [];
				// 		$category_data['category'] = $category;
				// 		$sales = array_filter($products, function($item) use($category) {
				// 				return $item->category == $category;
				// 		});
				// 		$titles_sales = array_column($sales, 'title');
				// 		$total_qtd = 0;
				// 		$total_gross_earnings = 0;
				// 		$total_icms = 0;
				// 		$total_pis = 0;
				// 		$total_cof = 0;
				// 		$total_cost = 0;
				// 		foreach($titles_sales as $title_sale) {
				// 				$sales_item = array_filter($items, function($item) use($title_sale) {
				// 						return $item->descricao == $title_sale;
				// 				});
				// 				$total_qtd += array_sum(array_column($sales_item, 'qtdVendida'));
				// 				$total_gross_earnings += array_sum(array_column($sales_item, 'lucroBrutoTotal'));
				// 				$total_icms += array_sum(array_column($sales_item, 'vlrIcms'));
				// 				$total_pis += array_sum(array_column($sales_item, 'vlrPis'));
				// 				$total_cof += array_sum(array_column($sales_item, 'vlrCof'));
				// 				$total_cost += array_sum(array_column($sales_item, 'custoBruto'));
				//
				// 		}
				// 		$category_data['qtd_un_sales'] = $total_qtd;
				// 		$category_data['gross_earnings'] = $total_gross_earnings;
				// 		$category_data['tax'] = $total_icms + $total_pis + $total_cof;
				// 		$category_data['net_earnings'] = $total_gross_earnings - $category_data['tax'];
				// 		$category_data['cost'] = $total_cost;
				// 		$category_data['gross_margin'] = $total_gross_earnings - $category_data['tax'] - $total_cost;
				// 		$category_data['percent_gross_margin'] = $category_data['gross_margin']/$category_data['net_earnings']*100;
				// 		$category_data['average_value_per_cost'] = $total_gross_earnings/$total_qtd;
				// 		array_push($data['aaData'], $category_data);
				// }
				// $data['iTotalRecords'] = count($categories);
				// $data['iTotalDisplayRecords'] = count($categories);
				// return json_encode($data);
		}
}
