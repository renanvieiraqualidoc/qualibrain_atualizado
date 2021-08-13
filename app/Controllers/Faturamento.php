<?php

namespace App\Controllers;
use App\Models\SalesModel;

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
}
