<?php

namespace App\Controllers;

class Falteiro extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de falteiro eletrônico
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('falteiro', $data);
	}

	// Função que acessa a API para pegar os pedidos de produtos faltantes baseado em um período
	public function response($initial_date, $final_date) {
			if(base_url() == 'http://qualibrain.local.com') {
					$response = '{
						  "items": [
						    {
						      "filial": 1007,
						      "produto": 12345,
						      "nomeProduto": "NAO FOI POSSIVEL RECUPERAR DESCRICAO",
						      "email": "rafael.figueiredo21@gmail.com",
						      "dataCadastro": "16/07/2021 00:00:00",
						      "dataEntrada": null
						    },
						    {
						      "filial": 1007,
						      "produto": 12345,
						      "nomeProduto": "NAO FOI POSSIVEL RECUPERAR DESCRICAO",
						      "email": "rafael.figueiredo22@gmail.com",
						      "dataCadastro": "16/07/2021 00:00:00",
						      "dataEntrada": null
						    },
								{
						      "filial": 1007,
						      "produto": 12345,
						      "nomeProduto": "NAO FOI POSSIVEL RECUPERAR DESCRICAO",
						      "email": "rafael.figueiredo22@gmail.com",
						      "dataCadastro": "17/07/2021 00:00:00",
						      "dataEntrada": null
						    },
								{
						      "filial": 1007,
						      "produto": 12346,
						      "nomeProduto": "NAO FOI POSSIVEL RECUPERAR DESCRICAO",
						      "email": "rafael.figueiredo22@gmail.com",
						      "dataCadastro": "17/07/2021 00:00:00",
						      "dataEntrada": null
						    },
								{
						      "filial": 1007,
						      "produto": 12345,
						      "nomeProduto": "NAO FOI POSSIVEL RECUPERAR DESCRICAO",
						      "email": "rafael.figueiredo22@gmail.com",
						      "dataCadastro": "18/07/2021 00:00:00",
						      "dataEntrada": null
						    }
						  ],
						  "quantityItems": 5,
						  "item": null
						}';
			}
			else {
					$client = \Config\Services::curlrequest();
					$response = $client->request('GET', "http://ultraclinica.totvscloud.com.br:2000/RMS/RMSSERVICES/ReportWebAPI/api/v1/Falterer/GetByDate?filial=1007&dataVendaInicio=".$initial_date."&dataVendaFim=".$final_date, [ 'headers' => ['Content-Type: application/vnd.api+json', 'Accept: application/vnd.api+json'] ])->getBody();
			}
			return $response;
	}

	// Função que retorna os pedidos de produtos faltantes
	public function getData() {
			$response = json_decode($this->response($this->request->getVar('initial_date'), $this->request->getVar('final_date')));
			$search_sku = $this->request->getVar('sku');
			$items = ($search_sku != "") ? array_filter($response->items, function($i) use($search_sku) { return $i->produto == $search_sku; }) : $response->items;
			$data = [];
			$data_products = [];
			foreach(array_values(array_unique(array_column($items, 'produto'))) as $sku) {
					array_push($data_products, array('sku' => $sku, 'qtd' => count(array_filter($items, function($i) use($sku) { return $i->produto == $sku; }))));
			}
			$data['aaData'] = $data_products;
			$data['iTotalRecords'] = count($data_products);
			$data['iTotalDisplayRecords'] = count($data_products);
			return json_encode($data);
	}
}
