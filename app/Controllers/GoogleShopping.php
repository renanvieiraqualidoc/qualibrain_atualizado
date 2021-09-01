<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class GoogleShopping extends BaseController
{
	// Função principal que monta o XML de produtos do Google Shopping
	public function index($data = []) {
			$model = new ProductsModel();
			$data['items'] = $model->getProductsGoogleShopping();
			// $this->debug($data['items']);
			echo view('xml/google_shopping', $data);
	}
}
