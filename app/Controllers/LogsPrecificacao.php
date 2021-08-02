<?php

namespace App\Controllers;
use App\Models\LogsPrecificacaoModel;

class LogsPrecificacao extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de logs de precificação
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('logsp', $data);
	}

	// Função principal que monta todos os dados da tela de logs de precificação
	public function search() {
			$model = new LogsPrecificacaoModel();
			$obj = json_decode($model->getLogs($this->request->getVar('initial_date'),
																				 $this->request->getVar('final_date'),
																				 $this->request->getVar('status'),
																				 $this->request->getVar('period'),
																				 $this->request->getVar('sku'),
																				 $this->request->getVar('iDisplayStart'),
																				 $this->request->getVar('iDisplayLength')));
			foreach($obj->products as $item) {
					$item->price = json_decode($item->original_data)->price_pay_only;
					$item->status = "";
			}
			if($this->request->getVar('type') == 'sac') $obj->products = $this->getCompetitorsPrices($obj->products);
			$data['aaData'] = $obj->products;
			$data['iTotalRecords'] = $obj->qtd;
			$data['iTotalDisplayRecords'] = $obj->qtd;
			return json_encode($data);
	}

	// Função para recuperar os responses dos logs
	public function getResponseJSON() {
			$model = new LogsPrecificacaoModel();
			return $model->getResponse($this->request->getVar('code'));
	}

	// Função para recuperar os preços dos concorrentes da tela de precificação
	public function getCompetitorsPrices($products) {
			foreach($products as $product) {
					$product->panvel = array_values(array_filter(json_decode($product->original_data)->scan_last, function($item) { return strpos($item->domain, 'panvel') !== false; }))[0]->offer_price ?? 0;
					$product->drogaraia = array_values(array_filter(json_decode($product->original_data)->scan_last, function($item) { return strpos($item->domain, 'drogaraia') !== false; }))[0]->offer_price ?? 0;
					$product->drogariasp = array_values(array_filter(json_decode($product->original_data)->scan_last, function($item) { return strpos($item->domain, 'drogariasaopaulo') !== false; }))[0]->offer_price ?? 0;
					$product->drogasil = array_values(array_filter(json_decode($product->original_data)->scan_last, function($item) { return strpos($item->domain, 'drogasil') !== false; }))[0]->offer_price ?? 0;
					$product->onofre = array_values(array_filter(json_decode($product->original_data)->scan_last, function($item) { return strpos($item->domain, 'onofre') !== false; }))[0]->offer_price ?? 0;
					$product->paguemenos = array_values(array_filter(json_decode($product->original_data)->scan_last, function($item) { return strpos($item->domain, 'paguemenos') !== false; }))[0]->offer_price ?? 0;
					$product->ultrafarma = array_values(array_filter(json_decode($product->original_data)->scan_last, function($item) { return strpos($item->domain, 'ultrafarma') !== false; }))[0]->offer_price ?? 0;
					$product->beleza_na_web = array_values(array_filter(json_decode($product->original_data)->scan_last, function($item) { return strpos($item->domain, 'belezanaweb') !== false; }))[0]->offer_price ?? 0;
			}
			return $products;
	}
}
