<?php

namespace App\Controllers;

class MGM extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de relatório de MGM
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('mgm', $data);
	}
}
