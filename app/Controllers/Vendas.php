<?php

namespace App\Controllers;
use App\Models\SalesModel;

class Vendas extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de relatório de vendas
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			$model = new SalesModel();
			$data['departments'] = $model->getSalesDepartments();
			$data['sales_categories'] = $model->getSalesCategories();
			$data['actions'] = $model->getSalesActions();

			$data['sub_categories'] = $model->getSalesSubCategories();
			echo view('vendas', $data);
	}
}
