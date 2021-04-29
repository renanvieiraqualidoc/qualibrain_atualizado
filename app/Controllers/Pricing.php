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
