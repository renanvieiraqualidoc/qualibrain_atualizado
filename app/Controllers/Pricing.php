<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class Pricing extends BaseController
{

	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			$this->modalPerdendo('medicamento');
			$this->modalPerdendo('perfumaria');
			$this->modalPerdendo('nao_medicamento');
			echo view('pricing', $data);
	}

	public function modalPerdendo($department) {
			$model = new ProductsModel();
			$data['produtos'] = $model->getProductsByDepartment(str_replace("_", " ", $department));
			$data['title'] = ucfirst($department);
			$data['id_data_table'] = $department;
			$data[$department] = count(json_decode($data['produtos']));
			echo view('modals/perdendo', $data);
	}
}
