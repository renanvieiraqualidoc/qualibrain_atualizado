<?php

namespace App\Controllers;

class LogsPrecificacaoSAC extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de logs de precificação para o SAC
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('logspsac', $data);
	}
}
