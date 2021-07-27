<?php

namespace App\Controllers;
use App\Models\SalesModel;

class PBM extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de relatório de PBM
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('pbm', $data);
	}

	public function populateTable() {
			$selected_date = $this->request->getVar('selected_date');
			$sales_model = new SalesModel();
			$sales = $sales_model->getPBMSales($selected_date);
			$array_today = [];
			$array_last_day = [];
			$array_last_week = [];
			$selected_day_sales = array_filter($sales, function($sale) use($selected_date) { return (strpos($sale->order_date, $selected_date) !== false); });
			$selected_last_day_sales = array_filter($sales, function($sale) use($selected_date) { return (strpos($sale->order_date, date('Y-m-d', strtotime($selected_date."-1 day"))) !== false); });
			$selected_last_week_sales = array_filter($sales, function($sale) use($selected_date) { return (strpos($sale->order_date, date('Y-m-d', strtotime($selected_date."-7 days"))) !== false); });
			date_default_timezone_set('America/Sao_Paulo');
			$data_table = [];
			for($i=0; $i <= date('G'); $i++) {
					$hour_sales_today = array_filter($selected_day_sales, function($sale) use($i) { $hour = ($i < 10) ? "0".$i : $i; return (strpos($sale->order_date, " ".$hour.":") !== false); });
					$hour_sales_last_day = array_filter($selected_last_day_sales, function($sale) use($i) { $hour = ($i < 10) ? "0".$i : $i; return (strpos($sale->order_date, " ".$hour.":") !== false); });
					$hour_sales_last_week = array_filter($selected_last_week_sales, function($sale) use($i) { $hour = ($i < 10) ? "0".$i : $i; return (strpos($sale->order_date, " ".$hour.":") !== false); });
					array_push($data_table, array('qtd_today' => count($hour_sales_today),
																				'value_today' => array_sum(array_column($hour_sales_today, 'value')),
																				'tkm_today' => count($hour_sales_today) > 0 ? array_sum(array_column($hour_sales_today, 'value'))/count($hour_sales_today) : 0,
																				'qtd_yesterday' => count($hour_sales_last_day),
																				'value_yesterday' => array_sum(array_column($hour_sales_last_day, 'value')),
																				'tkm_yesterday' => count($hour_sales_last_day) > 0 ? array_sum(array_column($hour_sales_last_day, 'value'))/count($hour_sales_last_day) : 0,
																				'qtd_last_week' => count($hour_sales_last_week),
																				'value_last_week' => array_sum(array_column($hour_sales_last_week, 'value')),
																				'tkm_last_week' => count($hour_sales_last_week) > 0 ? array_sum(array_column($hour_sales_last_week, 'value'))/count($hour_sales_last_week) : 0));
			}
			$data['sales'] = $data_table;
			$data['ranking'] = $sales_model->getBestSellersPBM($selected_date);
			return json_encode($data);
	}

	public function getDataVanOrProgram() {
			$sales_model = new SalesModel();
			if($this->request->getVar('type') == 'Van') {
					$items = $sales_model->getPBMSalesVans();
					$labels = array_values(array_unique(array_column($items, 'label')));
					$data = array();
					foreach($labels as $label) {
							array_push($data, count(array_filter($items, function($item) use($label) { return $item->label == $label; })));
					}
			}
			else if($this->request->getVar('type') == 'Programa') {
					$items = $sales_model->getPBMSalesPrograms();
					$labels = array_column($items, 'label');
					$data = array_column($items, 'value');
			}
			return json_encode(array('labels' => $labels, 'data' => $data));
	}

	public function perfomancePBM() {
			$sales_model = new SalesModel();
			$period = $this->request->getVar('period');
			$skus_sales = json_decode($sales_model->getPBMSalesWithoutProgram($period));
			$fat_vans = json_decode($sales_model->getPBMSalesWithProgram($period, array_column($skus_sales, 'sku')));
			$labels = array();
			$data = array();
			foreach($fat_vans as $fat_van) {
					array_push($labels, $fat_van->van);
					array_push($data, $fat_van->faturamento);
			}
			array_push($labels, "Sem Programa");
			array_push($data, array_sum(array_column($skus_sales, 'faturamento')) - array_sum(array_column($fat_vans, 'faturamento')));
			return json_encode(array('labels' => $labels, 'data' => $data));
	}

	public function sharePBM() {
			$sales_model = new SalesModel();
			$month1 = date('m', strtotime('-2 months'));
			$month2 = date('m', strtotime('-1 month'));
			$month3 = date('m');
			$fat1 = $sales_model->getSalesMedicationsShare($month1);
			$fat2 = $sales_model->getSalesMedicationsShare($month2);
			$fat3 = $sales_model->getSalesMedicationsShare($month3);
			$salesPBMProgramMonth1 = $sales_model->getSalesMedicationsPBMProgram($month1);
			$fat4 = $salesPBMProgramMonth1->qtd;
			$price_cost4 = $salesPBMProgramMonth1->price_cost;
			$salesPBMProgramMonth2 = $sales_model->getSalesMedicationsPBMProgram($month2);
			$fat5 = $salesPBMProgramMonth2->qtd;
			$price_cost5 = $salesPBMProgramMonth2->price_cost;
			$salesPBMProgramMonth3 = $sales_model->getSalesMedicationsPBMProgram($month3);
			$fat6 = $salesPBMProgramMonth3->qtd;
			$price_cost6 = $salesPBMProgramMonth3->price_cost;
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
			$data = array('labels' => array($months[intval($month1)], $months[intval($month2)], $months[intval($month3)]),
										'medicamentos' => array($fat1, $fat2, $fat3),
										'programa_pbm' => array($fat4, $fat5, $fat6),
										'share' => array($fat1 > 0 ? $fat4/$fat1 : 0, $fat2 > 0 ? $fat5/$fat2 : 0, $fat3 > 0 ? $fat6/$fat3 : 0),
										'margin' => array($fat4 > 0 ? ($fat4 - $price_cost4)/$fat4*100 : 0, $fat5 > 0 ? ($fat5 - $price_cost5)/$fat5*100 : 0, $fat6 > 0 ? ($fat6 - $price_cost6)/$fat6*100 : 0));
			return json_encode($data);
	}

	public function analysis() {
			$sales_model = new SalesModel();
			$items = $sales_model->getPBMSalesLastMonths($this->request->getVar('program'));
			$margins = array();
			$faturamentos = array();
			foreach(array_unique(array_column($items, 'date')) as $period) {
					$skus = array_filter($items, function($item) use($period) { return $item->date == $period; });
					$faturamento_mes = array_sum(array_column($skus, 'faturamento'));
					$price_cost_month = array_sum(array_column($skus, 'price_cost'));
					array_push($faturamentos, $faturamento_mes);
					array_push($margins, ($faturamento_mes - $price_cost_month)/$faturamento_mes*100);
			}
			$data = array('labels_line_chart' => array_values(array_map(function ($ar) {
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
																				   }, array_unique(array_column($items, 'date')))),
									  'data_margin_line_chart' => $margins,
									  'data_fat_line_chart' => $faturamentos);
			return json_encode($data);
	}
}
