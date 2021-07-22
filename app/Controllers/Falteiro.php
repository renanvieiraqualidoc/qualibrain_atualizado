<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class Falteiro extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de falteiro eletrônico
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('falteiro', $data);
	}

	// Função que retorna os pedidos de produtos faltantes
	public function getData() {
			$model = new ProductsModel();
			$obj = json_decode($model->getDataFalteiro($this->request->getVar('initial_date'),
																								 $this->request->getVar('final_date'),
																							   $this->request->getVar('iDisplayStart'),
																							   $this->request->getVar('iDisplayLength'),
																							   $this->request->getVar('mDataProp_'.$this->request->getVar('iSortCol_0')),
																							   $this->request->getVar('sSortDir_0'),
																							   $this->request->getVar('sSearch')));
			$data = [];
			$data['aaData'] = $obj->products;
			$data['iTotalRecords'] = $obj->qtd;
			$data['iTotalDisplayRecords'] = $obj->qtd;
			return json_encode($data);
	}
}
