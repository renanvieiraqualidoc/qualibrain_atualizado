<?php

namespace App\Controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\TagModel;

class Tag extends BaseController
{
	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função principal que monta todos os dados da tela de tagueamento dos produtos
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('tag', $data);
	}

	// Função que recebe uma planilha de SKU's para serem tagueados
	public function updateSkus() {
			$response = array();
			if($xls_file = $this->request->getFile('file')) {
					if ($xls_file->isValid() && ! $xls_file->hasMoved()) {
							$xls_file->move(WRITEPATH, $xls_file->getName());
							$path_file = WRITEPATH.$xls_file->getName();
							$extension = ucfirst(str_replace(".", "", substr($xls_file->getName(), strrpos($xls_file->getName(), '.'), strlen($xls_file->getName()))));
							$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
							$spreadsheet = $reader->load($path_file);
							$i = 2;
							$model = new TagModel();
							do {
									$sku = $spreadsheet->getActiveSheet()->getCell('A'.$i);
									$action = $spreadsheet->getActiveSheet()->getCell('B'.$i);
									$response = $model->tagSku($sku, $action);
									if(!json_decode($response)->success) return $response;
									$i++;
							} while($sku != "");
							unlink($path_file);
							$response = array('msg' => 'Planilha importada com sucesso.', 'success' => true);
					}
					else $response = array('msg' => 'Arquivo não importado.', 'success' => false);
			}
			else $response = array('msg' => 'Arquivo não importado.', 'success' => false);
			return json_encode($response);
	}
}
