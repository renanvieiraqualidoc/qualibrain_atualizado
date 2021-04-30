<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class Pricing extends BaseController
{

	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			$this->modalPerdendo('medicamento');
			$this->modalPerdendo('perfumaria');
			$this->modalPerdendo('não medicamento');
			echo view('pricing', $data);
	}

	public function modalPerdendo($department) {
			$model = new ProductsModel();
			$data['title'] = ucwords($department);
			$department_ = str_replace("ã", "a", str_replace(" ", "_", $department));
			$department = str_replace("ã", "a", $department);
			$data['produtos'] = $model->getProductsByDepartment($department);
			$data['id_data_table'] = $department_;
			$data[$department_] = count(json_decode($data['produtos']));
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
			echo view('modals/perdendo', $data);
	}
}
