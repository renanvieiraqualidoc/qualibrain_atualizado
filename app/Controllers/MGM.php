<?php

namespace App\Controllers;
use App\Models\SalesModel;

class MGM extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de relatório de MGM
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('mgm', $data);
	}

	public function populateTable() {
			$selected_date = $this->request->getVar('selected_date');
			$sales_model = new SalesModel();
			$sales = $sales_model->getMGMSales($selected_date);
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
			$data['ranking'] = $sales_model->getMostlyIndicators($selected_date);
			foreach($data['ranking'] as $indicator) {
					$indicator->indicator_name = mb_convert_case($indicator->indicator_name, MB_CASE_TITLE, "UTF-8");
			}
			$data['sales'] = $data_table;
			return json_encode($data);
	}
}
