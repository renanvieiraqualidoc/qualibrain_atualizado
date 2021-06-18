<?php

namespace App\Controllers;
use App\Models\ProductsModel;
use App\Models\SalesModel;

class Simulador extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de simulador
	public function index($data = []) {
			$model = new ProductsModel();
			$sales = new SalesModel();
			$data['categories'] = $this->dynamicMenu();
			$data['departments'] = $model->getDepartments();
			$data['categories_filter'] = $model->getCategories();
			$data['margin_all_last_months'] = $sales->getMarginDiscAll('')*100;
			$data['margin_all_last_months_a'] = $sales->getMarginDiscAll('A')*100;
			$data['margin_all_last_months_b'] = $sales->getMarginDiscAll('B')*100;
			$data['margin_all_last_months_c'] = $sales->getMarginDiscAll('C')*100;
			$data['margin_all'] = $model->getAvgGrossMarginAll('')*100;
			$data['margin_all_a'] = $model->getAvgGrossMarginAll('A')*100;
			$data['margin_all_b'] = $model->getAvgGrossMarginAll('B')*100;
			$data['margin_all_c'] = $model->getAvgGrossMarginAll('C')*100;
			echo view('simulator', $data);
	}

	// Função para simular os valores de margens baseados nos filtros
	public function simulate($data = []) {
			$department = $this->request->getVar('department');
			$category = $this->request->getVar('category');
			$group = $this->request->getVar('group');
			$margin_from = $this->request->getVar('margin_from');
			$margin_at = $this->request->getVar('margin_at');
			$disc_from = $this->request->getVar('disc_from');
			$disc_at = $this->request->getVar('disc_at');
			$skus = $this->request->getVar('skus');
			$products = new ProductsModel();
			$data['margin_filter'] = $products->getAvgGrossMargin('', $department, $category, $group, $margin_from, $margin_at, $disc_from, $disc_at, $skus)*100;
			$data['margin_filter_a'] = $products->getAvgGrossMargin('A', $department, $category, $group, $margin_from, $margin_at, $disc_from, $disc_at, $skus)*100;
			$data['margin_filter_b'] = $products->getAvgGrossMargin('B', $department, $category, $group, $margin_from, $margin_at, $disc_from, $disc_at, $skus)*100;
			$data['margin_filter_c'] = $products->getAvgGrossMargin('C', $department, $category, $group, $margin_from, $margin_at, $disc_from, $disc_at, $skus)*100;
			return json_encode($data);
	}

	// Função para popular a tabela de simulador de margem
	public function tableMarginSimulator() {
			$model_sales = new SalesModel();
			$obj = json_decode($model_sales->getMarginSimulatorInfo($this->request->getVar('department'),
																														  $this->request->getVar('category'),
																														  $this->request->getVar('group'),
																														  $this->request->getVar('margin_from'),
																														  $this->request->getVar('margin_at'),
																														  $this->request->getVar('disc_from'),
																														  $this->request->getVar('disc_at'),
																														  $this->request->getVar('skus'),
																														  $this->request->getVar('curve'),
																														  $this->request->getVar('iDisplayStart'),
																														  $this->request->getVar('iDisplayLength'),
																														  $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																														  $this->request->getVar('sSortDir_0')));
			$data['aaData'] = $obj->products;
			$data['iTotalRecords'] = $obj->qtd;
			$data['iTotalDisplayRecords'] = $obj->qtd;
			return json_encode($data);
	}
}
