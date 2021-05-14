<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class Cronjob extends BaseController
{
		// Cronjob que cruza as informações de vendas da API RMS com as informações de produtos do banco de dados e salvam na tabela de vendas
		public function get_sales() {
				$initial_date = $final_date = date('Y-m-d', strtotime("-1 day"));
				$client = \Config\Services::curlrequest();
				$response = $client->request('GET', "http://ultraclinica.totvscloud.com.br:2000/RMS/RMSSERVICES/ReportWebAPI/api/v1/SaleHistory/GetByDate?filial=1007&dataVendaInicio={$initial_date}&dataVendaFim={$final_date}")->getBody();
				$items = json_decode($response)->items;
				$skus = [];
				foreach($items as $row) {
						array_push($skus, $row->productCode);
				}

				$model = new ProductsModel();
				$fields = $model->getProductFields($skus, ['sku as productCode', 'price_cost', 'department', 'category']);

				// Cria um array auxiliar que contém o "inner join" entre a resposta da API e a consulta no banco de dados
				$inner_join = $this->inner_join($items, $fields, 'productCode');

				$sql = "INSERT INTO vendas(sku, data, qtd, faturamento, price_cost, department, category) VALUES ";
				foreach($inner_join as $item) {
						$price_cost = $item['price_cost'] != '' ? $item['price_cost'] : 0;
						$date = str_replace(" 00:00:00", "", explode("/", $item['salesDate'])[2]."-".explode("/", $item['salesDate'])[1]."-".explode("/", $item['salesDate'])[0]);
						$sql .= "('{$item['productCode']}', '".$date."', {$item['salesQuantity']}, {$item['salesValue']}, $price_cost, '{$item['department']}', '{$item['category']}'),\n";
				}
				$sql = substr($sql, 0, -2).";";
				$file = WRITEPATH."data.txt";
				write_file($file, $sql);
				$host = ($model->db->hostname == "localhost") ? $model->db->hostname : substr($model->db->hostname, 0, strpos($model->db->hostname, ':'));
				if(trim(shell_exec("mysql -h $host -u".$model->db->username." -p'".$model->db->password."' ".$model->db->database." < $file 2>&1"))
										 == "mysql: [Warning] Using a password on the command line interface can be insecure.") {
						$msg = 'Vendas atualizadas com sucesso!';
						$success = true;
				}
				else {
						$msg = 'Não foi possível atualizar o dump!';
						$success = false;
				}
				unlink($file);
				echo json_encode(array('success' => $success, 'msg' => $msg));
		}
}
