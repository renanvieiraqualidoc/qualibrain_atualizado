<?php

namespace App\Controllers;

class PBM extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de relatório de PBM
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('pbm', $data);
	}
}
