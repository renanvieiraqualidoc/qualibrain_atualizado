<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class Pricing extends BaseController
{

	public function index($data = []) {
			$model = new ProductsModel();
			$data['categories'] = $this->dynamicMenu();
			$data['medicamentos'] = $model->getProductsByDepartment('MEDICAMENTO');
			$data['perfumaria'] = $model->getProductsByDepartment('PERFUMARIA');
			$data['nao_medicamentos'] = $model->getProductsByDepartment('NAO MEDICAMENTO');
			echo view('pricing', $data);
	}
}
