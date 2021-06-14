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
}
