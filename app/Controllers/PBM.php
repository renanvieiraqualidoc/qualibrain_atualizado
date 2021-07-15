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
			$data['ranking'] = $sales_model->getBestSellersPBM($selected_date);
			$data['sales'] = $data_table;
			return json_encode($data);
	}

	public function analysis() {
			$sales_model = new SalesModel();

			// Gráfico de linhas de faturamentos e margens mensais
			$items = $sales_model->getPBMSalesByProgram($this->request->getVar('program'));
			$margins = array();
			$faturamentos = array();
			foreach(array_unique(array_column($items, 'date')) as $period) {
					$skus = array_filter($items, function($item) use($period) { return $item->date == $period; });
					$faturamento_mes = array_sum(array_column($skus, 'faturamento'));
					$price_cost_month = array_sum(array_column($skus, 'price_cost'));
					array_push($faturamentos, $faturamento_mes);
					array_push($margins, ($faturamento_mes - $price_cost_month)/$faturamento_mes*100);
			}

			// Gráfico circular de vans
			$vans = array();
			$labels_van = array_values(array_unique(array_column($items, 'nome_van')));
			foreach($labels_van as $van) {
					array_push($vans, count(array_filter($items, function($item) use($van) { return $item->nome_van == $van; })));
			}

			// Gráfico circular de programas
			$programs = array();
			$labels_program = array_values(array_unique(array_column($items, 'programa')));
			foreach($labels_program as $program) {
					array_push($programs, count(array_filter($items, function($item) use($program) { return $item->programa == $program; })));
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

	public function getVanAndPrograms() {
			$sales_model = new SalesModel();
			$items = $sales_model->getPBMSalesByProgram('Todos');

			// Gráfico circular de vans
			$vans = array();
			$labels_van = array_values(array_unique(array_column($items, 'nome_van')));
			foreach($labels_van as $van) {
					array_push($vans, count(array_filter($items, function($item) use($van) { return $item->nome_van == $van; })));
			}

			// Gráfico circular de programas
			$programs = array();
			$labels_program = array_values(array_unique(array_column($items, 'programa')));
			foreach($labels_program as $program) {
					array_push($programs, count(array_filter($items, function($item) use($program) { return $item->programa == $program; })));
			}

			$data = array('labels_pie_chart_van' => $labels_van,
										'data_pie_chart_van' => $vans,
										'labels_pie_chart_program' => $labels_program,
										'data_pie_chart_program' => $programs);
			return json_encode($data);
	}
}
