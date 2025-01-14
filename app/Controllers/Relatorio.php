<?php

namespace App\Controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\I18n\Time;

class Relatorio extends BaseController
{
	public function index() {
			ini_set('memory_limit', '-1');
			$fileName = "";

			switch($_GET['type']) {
					case "perdendo":
							$fileName = "relatorio_{$_GET['type']}_{$_GET['department']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->losing($_GET['department']);
							break;
					case "total_skus":
							$fileName = "relatorio_{$_GET['type']}_{$_GET['curve']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->allSkus('', $_GET['curve']);
							break;
					case "ruptura":
							$fileName = "relatorio_{$_GET['type']}_{$_GET['curve']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->allSkus($_GET['type'], $_GET['curve']);
							break;
					case "abaixo_custo":
							$fileName = "relatorio_{$_GET['type']}_{$_GET['curve']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->allSkus($_GET['type'], $_GET['curve']);
							break;
					case "estoque_exclusivo":
							$fileName = "relatorio_{$_GET['type']}_{$_GET['curve']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->allSkus($_GET['type'], $_GET['curve']);
							break;
					case "perdendo_todos":
							$fileName = "relatorio_{$_GET['type']}_{$_GET['curve']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->allSkus($_GET['type'], $_GET['curve']);
							break;
					case "grupos_produtos":
							$fileName = str_replace("/", "_", "relatorio_{$_GET['type']}_{$_GET['group']}_".date('d-m-Y_h.i', time()).".xlsx");
							$spreadsheet = $this->groups($_GET['group']);
							break;
					case "vendidos":
							$fileName = "relatorio_{$_GET['type']}_{$_GET['department']}_".$_GET['sale_date'].".xlsx";
							$spreadsheet = $this->sales($_GET['department'], $_GET['sale_date']);
							break;
					case "top_produtos":
							$fileName = "relatorio_{$_GET['type']}_{$_GET['department']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->top_products($_GET['department']);
							break;
					case "mgm":
							$fileName = "relatorio_{$_GET['type']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->mgm();
							break;
					case "pbm":
							$fileName = "relatorio_{$_GET['type']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->pbm();
							break;
					case "falteiro":
							$fileName = "relatorio_{$_GET['type']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->falteiro($_GET['initial_date'], $_GET['final_date']);
							break;
					case "sales":
							$fileName = "relatorio_{$_GET['type']}_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->sales_custom($_GET['initial_date'], $_GET['final_date'], $_GET['department'], $_GET['category'], $_GET['action'], $_GET['group'], $_GET['sub_category']);
							break;
					default:
							$fileName = "relatorio_teste_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->teste();
							break;
			}

			$writer = new Xlsx($spreadsheet);
      $writer->save("relatorios/$fileName");
      return $this->response->download("relatorios/$fileName", null);
	}

	public function teste() {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'SKU');
			$sheet->setCellValue('B1', 'NOME');
			$sheet->setCellValue('C1', 'CATEGORIA');
			$sheet->setCellValue('D1', 'DEPARTAMENTO');
			$sheet->setCellValue('E1', 'CUSTO');
			$sheet->setCellValue('F1', 'ESTOQUE');
			$sheet->setCellValue('G1', 'FATURAMENTO');
			$sheet->setCellValue('H1', 'VENDAS');
			$rows = 2;
			$db = \Config\Database::connect();

			$products = $db->query("Select vendas.sku as SKU,
															Products.title as NOME,
															Products.category as CATEGORIA,
															vendas.department as DEPARTAMENTO,
															Products.price_cost as CUSTO,
															Products.qty_stock_rms as ESTOQUE,
															sum(vendas.faturamento) as FATURAMENTO,
															sum(vendas.qtd) as VENDAS
															 from Products left join vendas on vendas.sku=Products.sku WHERE vendas.sku in ('1213687', '1182595', '1174320', '1173804', '1009559', '1093665',
															 '1173715', '1173472', '1173669', '1182510', '1213148', '1009788', '1190776', '1010565', '1177621', '1173537', '1010611', '1116371', '1027719',
															 '1038206', '1165569', '1025740', '1003038', '1000942', '1047671', '1001361', '1117998', '1124110', '1164996', '1128000', '1100548', '1177940',
															 '1025554', '1025694', '1022237', '1121251', '1027433', '1179462', '1100033', '1025180', '1100122', '1003054', '1190016', '1019562', '1004964',
															 '1096850', '1038907', '1118323', '1003356', '1113887', '1126970', '1051385', '1211064', '1026259', '1024078', '1127993', '1151029', '1059629',
															 '1041274', '1094742', '1131087', '1037480', '1027883', '1099850', '1024485', '1114484', '1003780', '1116991', '1017225', '1016903', '1025449',
															 '1012061', '1170406', '1100300', '1047868', '1130269', '1036416', '1032836', '1038680', '1135538', '1017187', '1004298', '1053159', '1097580',
															 '1049097', '1031295', '1007262',
															 '1045520', '1019457', '1130161', '1177958', '1024019', '1018736', '1200283', '1096176', '1051644', '1042734', '1023691', '1121138', '1216520',
															 '1124790', '1124064', '1124072', '1123165', '1134477', '1128035', '1054422', '1025937', '1068911', '1024140', '1028804', '1021419', '1100670',
															 '1121286', '1133861', '1126814', '1022784', '1189794', '1057936', '1035223', '1172816', '1058517', '1122908', '1184083', '1068784', '1121960',
															 '1096443', '1061704', '1181815',
															 '1039229', '1102109', '1176650', '1049364', '1114727', '1033336', '1133721', '1190156', '1101226', '1051571', '1002660', '1124129', '1034391',
															 '1026615', '1094572', '1047833', '1063219', '1165089', '1019465', '1130196', '1033123', '1129015', '1099620', '1051806', '1110500', '1181777',
															 '1201743', '1133217', '1134612', '1049925', '1068334', '1065491', '1113453', '1121588', '1122800', '1030914', '1130200', '1046306', '1128760',
															 '1099221', '1029630', '1098390',
															 '1011723', '1133667', '1133314', '1097288', '1133268', '1165020', '1112937', '1190679')
															 and vendas.data >= '2021-06-16' and vendas.data <= '2021-06-21'
															 group by Products.sku")->getResult();

			foreach ($products as $val){
					$sheet->setCellValue('A' . $rows, $val->SKU);
					$sheet->setCellValue('B' . $rows, $val->NOME);
					$sheet->setCellValue('C' . $rows, $val->CATEGORIA);
					$sheet->setCellValue('D' . $rows, $val->DEPARTAMENTO);
					$sheet->setCellValue('E' . $rows, $val->CUSTO);
					$sheet->setCellValue('F' . $rows, $val->ESTOQUE);
					$sheet->setCellValue('G' . $rows, $val->FATURAMENTO);
					$sheet->setCellValue('H' . $rows, $val->VENDAS);
					$rows++;
			}
			return $spreadsheet;
	}

	public function mgm() {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'NOME DO CLIENTE');
			$sheet->setCellValue('B1', 'VALOR DO PEDIDO');
			$sheet->setCellValue('C1', 'DATA DO PEDIDO');
			$sheet->setCellValue('D1', 'QUEM INDICOU');
			$rows = 2;
			$db = \Config\Database::connect();
			$comp = '';
			if($this->request->getVar('initial_date') != '') $comp .= " and order_date >= '".$this->request->getVar('initial_date')."'";
			if($this->request->getVar('final_date') != '') $comp .= " and order_date <= '".$this->request->getVar('final_date')."'";
			$members = $db->query("Select client_name, value, order_date, indicator_name from mgm where 1=1 $comp order by order_date desc")->getResult();
			foreach ($members as $val){
					$sheet->setCellValue('A' . $rows, $val->client_name);
					$sheet->setCellValue('B' . $rows, $val->value);
					$sheet->setCellValue('C' . $rows, date('G:i d/m/Y', strtotime($val->order_date)));
					$sheet->setCellValue('D' . $rows, $val->indicator_name);
					$rows++;
			}
			return $spreadsheet;
	}

	public function pbm() {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'CÓDIGO DO PEDIDO');
			$sheet->setCellValue('B1', 'CÓDIGO DO PRODUTO');
			$sheet->setCellValue('C1', 'NOME DO PRODUTO');
			$sheet->setCellValue('D1', 'NOME DA VAN');
			$sheet->setCellValue('E1', 'PROGRAMA');
			$sheet->setCellValue('F1', 'PREÇO DE CUSTO');
			$sheet->setCellValue('G1', 'PREÇO DE VENDA PBM');
			$sheet->setCellValue('H1', 'PREÇO DE VENDA PBM UNITÁRIO');
			$sheet->setCellValue('I1', 'PAGUE APENAS');
			$sheet->setCellValue('J1', 'QUANTIDADE');
			$sheet->setCellValue('K1', 'DATA DO PEDIDO');
			$rows = 2;
			$db = \Config\Database::connect();
			$comp = '';
			if($this->request->getVar('initial_date') != '') $comp .= " and r.order_date >= '".$this->request->getVar('initial_date')."'";
			if($this->request->getVar('final_date') != '') $comp .= " and r.order_date <= '".$this->request->getVar('final_date')."'";
			$members = $db->query("Select r.id_order, r.sku, r.product_name, pv.van, pv.programa, p.price_cost, r.value, p.price_pay_only, r.order_date, r.quantity
														 from relatorio_pbm r
														 inner join Products p on p.sku = r.sku
														 inner join pbm_van pv on pv.id = r.van_program
														 where 1=1 $comp order by r.order_date desc")->getResult();
			foreach ($members as $val){
					$sheet->setCellValue('A' . $rows, $val->id_order);
					$sheet->setCellValue('B' . $rows, $val->sku);
					$sheet->setCellValue('C' . $rows, $val->product_name);
					$sheet->setCellValue('D' . $rows, $val->van);
					$sheet->setCellValue('E' . $rows, $val->programa);
					$sheet->setCellValue('F' . $rows, $val->price_cost);
					$sheet->setCellValue('G' . $rows, $val->value);
					$sheet->setCellValue('H' . $rows, $val->value/$val->quantity);
					$sheet->setCellValue('I' . $rows, $val->price_pay_only);
					$sheet->setCellValue('J' . $rows, $val->quantity);
					$sheet->setCellValue('K' . $rows, $val->order_date);
					$rows++;
			}
			return $spreadsheet;
	}

	public function falteiro($initial_date, $final_date) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'CÓDIGO DO PRODUTO');
			$sheet->setCellValue('B1', 'NOME DO PRODUTO');
			$sheet->setCellValue('C1', 'EMAIL');
			$sheet->setCellValue('D1', 'DATA DE CADASTRO');
			$rows = 2;
			$db = \Config\Database::connect();
			$comp = '';
			if($initial_date != "") $comp .= " and data_cadastro >= '$initial_date'";
			if($final_date != "") $comp .= " and data_cadastro <= '$final_date'";
			$items = $db->query("Select produto as sku, nome, email, data_cadastro
													 from falteiro
													 where 1=1 $comp order by data_cadastro desc")->getResult();
			foreach ($items as $val){
					$sheet->setCellValue('A' . $rows, $val->sku);
					$sheet->setCellValue('B' . $rows, $val->nome);
					$sheet->setCellValue('C' . $rows, $val->email);
					$sheet->setCellValue('D' . $rows, $val->data_cadastro);
					$rows++;
			}
			return $spreadsheet;
	}

	public function losing($department) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'SKU');
      $sheet->setCellValue('B1', 'VENDA_ACUMULADA');
      $sheet->setCellValue('C1', 'EAN');
      $sheet->setCellValue('D1', 'NOME');
      $sheet->setCellValue('E1', 'PRINCIPIO_ATIVO');
      $sheet->setCellValue('F1', 'APRESENTACAO');
			$sheet->setCellValue('G1', 'DEPARTAMENTO');
			$sheet->setCellValue('H1', 'CATEGORIA');
			$sheet->setCellValue('I1', 'SITUACAO');
			$sheet->setCellValue('J1', 'STATUS');
			$sheet->setCellValue('K1', 'ESTOQUE_RMS');
			$sheet->setCellValue('L1', 'ESTOQUE_OCC');
			$sheet->setCellValue('M1', 'DIFERENCA_OCC_RMS');
			$sheet->setCellValue('N1', 'PRECO_TABELADO');
			$sheet->setCellValue('O1', 'SUGESTAO_TABELADO');
			$sheet->setCellValue('P1', 'MENOR_PRECO');
			$sheet->setCellValue('Q1', 'CONCORRENTE_MENOR_PRECO');
			$sheet->setCellValue('R1', 'QTD_CONCORRENTES');
			$sheet->setCellValue('S1', 'QTD_CONCORRENTES_ATIVOS');
			$sheet->setCellValue('T1', 'CUSTO');
			$sheet->setCellValue('U1', 'MARGEM_BRUTA');
			$sheet->setCellValue('V1', 'PRECO_DE_VENDA');
			$sheet->setCellValue('W1', 'PAGUE_APENAS');
			$sheet->setCellValue('X1', 'DROGASIL');
			$sheet->setCellValue('Y1', 'ULTRAFARMA');
			$sheet->setCellValue('Z1', 'BELEZA_NA_WEB');
			$sheet->setCellValue('AA1', 'DROGARAIA');
			$sheet->setCellValue('AB1', 'DROGARIASP');
			$sheet->setCellValue('AC1', 'ONOFRE');
			$sheet->setCellValue('AD1', 'PAGUE_MENOS');
			$sheet->setCellValue('AE1', 'PANVEL');
			$sheet->setCellValue('AF1', 'MENOR_PRECO_POR_AI');
			$sheet->setCellValue('AG1', 'MARGEM_VALOR');
			$sheet->setCellValue('AH1', 'CASHBACK');
			$sheet->setCellValue('AI1', 'MARGEM_APOS_CASHBACK');
			$sheet->setCellValue('AJ1', 'MARGEM_BRUTA_PORCENTO');
			$sheet->setCellValue('AK1', 'DIFERENCA_PARA_O_MENOR_CONCORRENTE');
			$sheet->setCellValue('AL1', 'CURVA');
			$sheet->setCellValue('AM1', 'PBM');
			$sheet->setCellValue('AN1', 'SITUACAO_DESCONTINUADO');
			$sheet->setCellValue('AO1', 'MARCA');
			$sheet->setCellValue('AP1', 'FABRICANTE');
			$sheet->setCellValue('AQ1', 'OTC');
			$sheet->setCellValue('AR1', 'DESCONTINUADO');
			$sheet->setCellValue('AS1', 'CONTROLADO');
			$sheet->setCellValue('AT1', 'ATIVO');
			$sheet->setCellValue('AU1', 'ACAO');
			$sheet->setCellValue('AV1', 'SUBCATEGORIA');
			$rows = 2;
			$db = \Config\Database::connect();
			$products = $db->query("Select vendas.sku as SKU, sum(vendas.qtd) as VENDA_ACUMULADA,  Products.reference_code as EAN, Products.title as NOME,
															  principio_ativo.principio_ativo as PRINCIPIO_ATIVO, principio_ativo.apresentacao as APRESENTACAO, Products.department as DEPARTAMENTO,
															 Products.category as CATEGORIA, Situation.situation as SITUACAO, Status.status as STATUS, REPLACE(Products.qty_stock_rms,'.',',') as ESTOQUE_RMS,
															 Products.qty_stock as ESTOQUE_OCC, (Products.qty_stock - Products.qty_stock_rms) as DIFERENCA_OCC_RMS,
															 REPLACE(tabulated_price, '.', ',' ) as PRECO_TABELADO, REPLACE(tabulated_price_suggestion, '.', ',' )  as SUGESTAO_TABELADO,
															  REPLACE(lowest_price, '.', ',' ) AS MENOR_PRECO,
															 Products.lowest_price_competitor AS CONCORRENTE_MENOR_PRECO, Products.qty_competitors as QTD_CONCORRENTES,
															 Products.qty_competitors_available as QTD_CONCORRENTES_ATIVOS, REPLACE(Products.price_cost, '.', ',' ) AS CUSTO,
															  REPLACE(Products.margin, '.', ',' ) AS MARGEM_BRUTA,
															 REPLACE(Products.sale_price, '.', ',' ) AS PRECO_DE_VENDA, REPLACE(Products.price_pay_only, '.', ',' ) AS PAGUE_APENAS,
															 REPLACE(Products.drogasil, '.', ',' ) AS DROGASIL,
															 REPLACE(Products.ultrafarma, '.', ',' ) AS ULTRAFARMA,
															 REPLACE(Products.belezanaweb, '.', ',' ) AS BELEZA_NA_WEB,
															 REPLACE(Products.drogaraia, '.', ',' ) AS DROGARAIA,
															 REPLACE(Products.drogariasp, '.', ',' ) AS DROGARIASP,
															 REPLACE(Products.onofre, '.', ',' ) AS ONOFRE,
															 REPLACE(Products.paguemenos, '.', ',' ) AS PAGUE_MENOS,
															 REPLACE(Products.panvel, '.', ',' ) AS PANVEL,
															  REPLACE(Products.current_less_price_around, '.',',') as MENOR_PRECO_POR_AI,
															  REPLACE(Products.margin_value, '.',',') as MARGEM_VALOR, REPLACE(Products.current_cashback, '.',',') as CASHBACK,
															 REPLACE(gross_margin, '.', ',' ) AS MARGEM_APOS_CASHBACK, REPLACE(Products.gross_margin_percent, '.',',') as MARGEM_BRUTA_PORCENTO,
															  REPLACE(Products.diff_pay_only_lowest, '.',',') as DIFERENCA_PARA_O_MENOR_CONCORRENTE,
															 Products.curve as CURVA, Products.pbm as PBM, descontinuado.situation as SITUACAO_DESCONTINUADO,
															 marca.marca as MARCA, marca.fabricante as FABRICANTE, Products.otc as OTC, Products.descontinuado as DESCONTINUADO,
															  Products.controlled_substance as CONTROLADO, Products.active as ATIVO, Products.acao as ACAO,
																Products.sub_category as SUBCATEGORIA
																from vendas inner join Products on Products.sku=vendas.sku
															  INNER JOIN Situation on Products.situation_code_fk = Situation.code INNER JOIN Status on Products.status_code_fk = Status.code
															 LEFT JOIN principio_ativo ON principio_ativo.sku = Products.sku LEFT JOIN descontinuado on Products.sku = descontinuado.sku
															  LEFT JOIN marca on Products.sku = marca.sku WHERE Products.diff_pay_only_lowest < 0 and Products.department = '".str_replace("_", " ", $department)."' group by sku")->getResult();
			foreach ($products as $val){
					$sheet->setCellValue('A' . $rows, $val->SKU);
					$sheet->setCellValue('B' . $rows, $val->VENDA_ACUMULADA);
					$sheet->setCellValue('C' . $rows, $val->EAN);
					$sheet->setCellValue('D' . $rows, $val->NOME);
					$sheet->setCellValue('E' . $rows, $val->PRINCIPIO_ATIVO);
					$sheet->setCellValue('F' . $rows, $val->APRESENTACAO);
					$sheet->setCellValue('G' . $rows, $val->DEPARTAMENTO);
					$sheet->setCellValue('H' . $rows, $val->CATEGORIA);
					$sheet->setCellValue('I' . $rows, $val->SITUACAO);
					$sheet->setCellValue('J' . $rows, $val->STATUS);
					$sheet->setCellValue('K' . $rows, $val->ESTOQUE_RMS);
					$sheet->setCellValue('L' . $rows, $val->ESTOQUE_OCC);
					$sheet->setCellValue('M' . $rows, $val->DIFERENCA_OCC_RMS);
					$sheet->setCellValue('N' . $rows, $val->PRECO_TABELADO);
					$sheet->setCellValue('O' . $rows, $val->SUGESTAO_TABELADO);
					$sheet->setCellValue('P' . $rows, $val->MENOR_PRECO);
					$sheet->setCellValue('Q' . $rows, $val->CONCORRENTE_MENOR_PRECO);
					$sheet->setCellValue('R' . $rows, $val->QTD_CONCORRENTES);
					$sheet->setCellValue('S' . $rows, $val->QTD_CONCORRENTES_ATIVOS);
					$sheet->setCellValue('T' . $rows, $val->CUSTO);
					$sheet->setCellValue('U' . $rows, $val->MARGEM_BRUTA);
					$sheet->setCellValue('V' . $rows, $val->PRECO_DE_VENDA);
					$sheet->setCellValue('W' . $rows, $val->PAGUE_APENAS);
					$sheet->setCellValue('X' . $rows, $val->DROGASIL);
					$sheet->setCellValue('Y' . $rows, $val->ULTRAFARMA);
					$sheet->setCellValue('Z' . $rows, $val->BELEZA_NA_WEB);
					$sheet->setCellValue('AA' . $rows, $val->DROGARAIA);
					$sheet->setCellValue('AB' . $rows, $val->DROGARIASP);
					$sheet->setCellValue('AC' . $rows, $val->ONOFRE);
					$sheet->setCellValue('AD' . $rows, $val->PAGUE_MENOS);
					$sheet->setCellValue('AE' . $rows, $val->PANVEL);
					$sheet->setCellValue('AF' . $rows, $val->MENOR_PRECO_POR_AI);
					$sheet->setCellValue('AG' . $rows, $val->MARGEM_VALOR);
					$sheet->setCellValue('AH' . $rows, $val->CASHBACK);
					$sheet->setCellValue('AI' . $rows, $val->MARGEM_APOS_CASHBACK);
					$sheet->setCellValue('AJ' . $rows, $val->MARGEM_BRUTA_PORCENTO);
					$sheet->setCellValue('AK' . $rows, $val->DIFERENCA_PARA_O_MENOR_CONCORRENTE);
					$sheet->setCellValue('AL' . $rows, $val->CURVA);
					$sheet->setCellValue('AM' . $rows, $val->PBM);
					$sheet->setCellValue('AN' . $rows, $val->SITUACAO_DESCONTINUADO);
					$sheet->setCellValue('AO' . $rows, $val->MARCA);
					$sheet->setCellValue('AP' . $rows, $val->FABRICANTE);
					$sheet->setCellValue('AQ' . $rows, $val->OTC);
					$sheet->setCellValue('AR' . $rows, $val->DESCONTINUADO);
					$sheet->setCellValue('AS' . $rows, $val->CONTROLADO);
					$sheet->setCellValue('AT' . $rows, $val->ATIVO);
					$sheet->setCellValue('AU' . $rows, $val->ACAO);
					$sheet->setCellValue('AV' . $rows, $val->SUBCATEGORIA);
			    $rows++;
			}
			return $spreadsheet;
	}

	public function allSkus($type, $curve) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'SKU');
			$sheet->setCellValue('B1', 'VENDA_ACUMULADA');
			$sheet->setCellValue('C1', 'FATURAMENTO');
			$sheet->setCellValue('D1', 'CUSTO_TOTAL');
			$sheet->setCellValue('E1', 'MARGEM_BRUTA_TOTAL');
			$sheet->setCellValue('F1', 'EAN');
			$sheet->setCellValue('G1', 'NOME');
			$sheet->setCellValue('H1', 'PRINCIPIO_ATIVO');
			$sheet->setCellValue('I1', 'APRESENTACAO');
			$sheet->setCellValue('J1', 'DEPARTAMENTO');
			$sheet->setCellValue('K1', 'CATEGORIA');
			$sheet->setCellValue('L1', 'SITUACAO');
			$sheet->setCellValue('M1', 'STATUS');
			$sheet->setCellValue('N1', 'ESTOQUE_RMS');
			$sheet->setCellValue('O1', 'ESTOQUE_OCC');
			$sheet->setCellValue('P1', 'DIFERENCA_OCC_RMS');
			$sheet->setCellValue('Q1', 'PRECO_TABELADO');
			$sheet->setCellValue('R1', 'SUGESTAO_TABELADO');
			$sheet->setCellValue('S1', 'CONCORRENTE_MENOR_PRECO');
			$sheet->setCellValue('T1', 'QTD_CONCORRENTES');
			$sheet->setCellValue('U1', 'QTD_CONCORRENTES_ATIVOS');
			$sheet->setCellValue('V1', 'MENOR_PRECO');
			$sheet->setCellValue('W1', 'CUSTO');
			$sheet->setCellValue('X1', 'MARGEM_BRUTA');
			$sheet->setCellValue('Y1', 'PRECO_DE_VENDA');
			$sheet->setCellValue('Z1', 'PAGUE_APENAS');
			$sheet->setCellValue('AA1', 'DROGASIL');
			$sheet->setCellValue('AB1', 'ULTRAFARMA');
			$sheet->setCellValue('AC1', 'BELEZA_NA_WEB');
			$sheet->setCellValue('AD1', 'DROGARAIA');
			$sheet->setCellValue('AE1', 'DROGARIASP');
			$sheet->setCellValue('AF1', 'ONOFRE');
			$sheet->setCellValue('AG1', 'PAGUE_MENOS');
			$sheet->setCellValue('AH1', 'PANVEL');
			$sheet->setCellValue('AI1', 'MENOR_PRECO_POR_AI');
			$sheet->setCellValue('AJ1', 'MARGEM_VALOR');
			$sheet->setCellValue('AK1', 'CASHBACK');
			$sheet->setCellValue('AL1', 'MARGEM_APOS_CASHBACK');
			$sheet->setCellValue('AM1', 'MARGEM_BRUTA_PORCENTO');
			$sheet->setCellValue('AN1', 'DIFERENCA_PARA_O_MENOR_CONCORRENTE');
			$sheet->setCellValue('AO1', 'CURVA');
			$sheet->setCellValue('AP1', 'PBM');
			$sheet->setCellValue('AQ1', 'SITUACAO_DESCONTINUADO');
			$sheet->setCellValue('AR1', 'MARCA');
			$sheet->setCellValue('AS1', 'FABRICANTE');
			$sheet->setCellValue('AT1', 'OTC');
			$sheet->setCellValue('AU1', 'DESCONTINUADO');
			$sheet->setCellValue('AV1', 'CONTROLADO');
			$sheet->setCellValue('AW1', 'ATIVO');
			$sheet->setCellValue('AX1', 'PMC');
			$sheet->setCellValue('AY1', 'PRECO_FABRICA');
			$sheet->setCellValue('AZ1', 'ACAO');
			$sheet->setCellValue('BA1', 'SUBCATEGORIA');
			$rows = 2;
			$db = \Config\Database::connect();
			$comp = ($curve != '') ? "and Products.curve = '$curve'" : '';
			$comp_type = '';
			switch($type) {
					case "ruptura":
							$comp_type = "and Products.qty_stock_rms = 0 and Products.active = 1 and Products.descontinuado != 1";
							break;
					case "abaixo_custo":
							$comp_type = "and Products.gross_margin_percent < 0 and Products.active = 1 and Products.descontinuado != 1 and Products.qty_stock_rms > 0";
							break;
					case "estoque_exclusivo":
							$comp_type = "and Products.qty_competitors = 0 and Products.active = 1 and Products.descontinuado != 1 and Products.qty_stock_rms > 0";
							break;
					case "perdendo_todos":
							$comp_type = "and Products.price_pay_only > Products.belezanaweb
														and Products.price_pay_only > Products.drogariasp
														and Products.price_pay_only > Products.ultrafarma
														and Products.price_pay_only > Products.paguemenos
														and Products.price_pay_only > Products.panvel
														and Products.price_pay_only > Products.drogaraia
														and Products.price_pay_only > Products.drogasil
														and Products.price_pay_only > Products.onofre
														and Products.active = 1 and Products.descontinuado != 1 and Products.qty_competitors_available > 0";
							break;
			}
			$products = $db->query("Select Products.sku as SKU,
															sum(vendas.qtd) as VENDA_ACUMULADA,
															format(sum(vendas.faturamento),2,'de_DE') as FATURAMENTO,
															format(Products.price_cost * sum(vendas.qtd),2,'de_DE') as CUSTO_TOTAL,
															 format(sum(vendas.faturamento) - Products.price_cost * sum(vendas.qtd),2,'de_DE') as MARGEM_BRUTA_TOTAL,
															 Products.reference_code as EAN,
															 Products.title as NOME,
															 principio_ativo.principio_ativo as PRINCIPIO_ATIVO,
															 principio_ativo.apresentacao as APRESENTACAO,
															 Products.department as DEPARTAMENTO,
															Products.category as CATEGORIA, Situation.situation as SITUACAO,
															Status.status as STATUS, REPLACE(Products.qty_stock_rms,'.',',') as ESTOQUE_RMS,
															Products.qty_stock as ESTOQUE_OCC, (Products.qty_stock - Products.qty_stock_rms) as DIFERENCA_OCC_RMS,
															REPLACE(tabulated_price, '.', ',' ) as PRECO_TABELADO, REPLACE(tabulated_price_suggestion, '.', ',' )  as SUGESTAO_TABELADO,
															Products.lowest_price_competitor AS CONCORRENTE_MENOR_PRECO, Products.qty_competitors as QTD_CONCORRENTES,
															Products.qty_competitors_available as QTD_CONCORRENTES_ATIVOS,REPLACE(lowest_price, '.', ',' ) AS MENOR_PRECO,
															 REPLACE(Products.price_cost, '.', ',' ) AS CUSTO,
															 REPLACE(Products.margin, '.', ',' ) AS MARGEM_BRUTA,
															REPLACE(Products.sale_price, '.', ',' ) AS PRECO_DE_VENDA, REPLACE(Products.price_pay_only, '.', ',' ) AS PAGUE_APENAS,
															 REPLACE(Products.drogasil, '.', ',' ) AS DROGASIL,
                               REPLACE(Products.ultrafarma, '.', ',' ) AS ULTRAFARMA,
                               REPLACE(Products.belezanaweb, '.', ',' ) AS BELEZA_NA_WEB,
                               REPLACE(Products.drogaraia, '.', ',' ) AS DROGARAIA,
                               REPLACE(Products.drogariasp, '.', ',' ) AS DROGARIASP,
                               REPLACE(Products.onofre, '.', ',' ) AS ONOFRE,
                               REPLACE(Products.paguemenos, '.', ',' ) AS PAGUE_MENOS,
                               REPLACE(Products.panvel, '.', ',' ) AS PANVEL,
															 REPLACE(Products.current_less_price_around, '.',',') as MENOR_PRECO_POR_AI,
															 REPLACE(Products.margin_value, '.',',') as MARGEM_VALOR, REPLACE(Products.current_cashback, '.',',') as CASHBACK,
															REPLACE(gross_margin, '.', ',' ) AS MARGEM_APOS_CASHBACK,
															REPLACE(Products.gross_margin_percent, '.',',') as MARGEM_BRUTA_PORCENTO,
															 REPLACE(Products.diff_pay_only_lowest, '.',',') as DIFERENCA_PARA_O_MENOR_CONCORRENTE,
															Products.curve as CURVA, Products.pbm as PBM, descontinuado.situation as SITUACAO_DESCONTINUADO,
															marca.marca as MARCA, marca.fabricante as FABRICANTE, Products.otc as OTC, Products.descontinuado as DESCONTINUADO,
															 Products.controlled_substance as CONTROLADO, Products.active as ATIVO,
															 pmc.pmc, pmc.preco_fabrica, Products.acao as ACAO,
															 Products.sub_category as SUBCATEGORIA
															 from Products
															 left join vendas on vendas.sku=Products.sku
															 left join pmc on pmc.sku=Products.sku
															 INNER JOIN Situation on Products.situation_code_fk = Situation.code
															 INNER JOIN Status on Products.status_code_fk = Status.code
															 LEFT JOIN principio_ativo ON principio_ativo.sku = Products.sku
															 LEFT JOIN descontinuado on Products.sku = descontinuado.sku
															 LEFT JOIN marca on Products.sku = marca.sku WHERE 1=1 $comp $comp_type group by sku")->getResult();
			foreach ($products as $val){
					$sheet->setCellValue('A' . $rows, $val->SKU);
					$sheet->setCellValue('B' . $rows, $val->VENDA_ACUMULADA);
					$sheet->setCellValue('C' . $rows, $val->FATURAMENTO);
					$sheet->setCellValue('D' . $rows, $val->CUSTO_TOTAL);
					$sheet->setCellValue('E' . $rows, $val->MARGEM_BRUTA_TOTAL);
					$sheet->setCellValue('F' . $rows, $val->EAN);
					$sheet->setCellValue('G' . $rows, $val->NOME);
					$sheet->setCellValue('H' . $rows, $val->PRINCIPIO_ATIVO);
					$sheet->setCellValue('I' . $rows, $val->APRESENTACAO);
					$sheet->setCellValue('J' . $rows, $val->DEPARTAMENTO);
					$sheet->setCellValue('K' . $rows, $val->CATEGORIA);
					$sheet->setCellValue('L' . $rows, $val->SITUACAO);
					$sheet->setCellValue('M' . $rows, $val->STATUS);
					$sheet->setCellValue('N' . $rows, $val->ESTOQUE_RMS);
					$sheet->setCellValue('O' . $rows, $val->ESTOQUE_OCC);
					$sheet->setCellValue('P' . $rows, $val->DIFERENCA_OCC_RMS);
					$sheet->setCellValue('Q' . $rows, $val->PRECO_TABELADO);
					$sheet->setCellValue('R' . $rows, $val->SUGESTAO_TABELADO);
					$sheet->setCellValue('S' . $rows, $val->CONCORRENTE_MENOR_PRECO);
					$sheet->setCellValue('T' . $rows, $val->QTD_CONCORRENTES);
					$sheet->setCellValue('U' . $rows, $val->QTD_CONCORRENTES_ATIVOS);
					$sheet->setCellValue('V' . $rows, $val->MENOR_PRECO);
					$sheet->setCellValue('W' . $rows, $val->CUSTO);
					$sheet->setCellValue('X' . $rows, $val->MARGEM_BRUTA);
					$sheet->setCellValue('Y' . $rows, $val->PRECO_DE_VENDA);
					$sheet->setCellValue('Z' . $rows, $val->PAGUE_APENAS);
					$sheet->setCellValue('AA' . $rows, $val->DROGASIL);
					$sheet->setCellValue('AB' . $rows, $val->ULTRAFARMA);
					$sheet->setCellValue('AC' . $rows, $val->BELEZA_NA_WEB);
					$sheet->setCellValue('AD' . $rows, $val->DROGARAIA);
					$sheet->setCellValue('AE' . $rows, $val->DROGARIASP);
					$sheet->setCellValue('AF' . $rows, $val->ONOFRE);
					$sheet->setCellValue('AG' . $rows, $val->PAGUE_MENOS);
					$sheet->setCellValue('AH' . $rows, $val->PANVEL);
					$sheet->setCellValue('AI' . $rows, $val->MENOR_PRECO_POR_AI);
					$sheet->setCellValue('AJ' . $rows, $val->MARGEM_VALOR);
					$sheet->setCellValue('AK' . $rows, $val->CASHBACK);
					$sheet->setCellValue('AL' . $rows, $val->MARGEM_APOS_CASHBACK);
					$sheet->setCellValue('AM' . $rows, $val->MARGEM_BRUTA_PORCENTO);
					$sheet->setCellValue('AN' . $rows, $val->DIFERENCA_PARA_O_MENOR_CONCORRENTE);
					$sheet->setCellValue('AO' . $rows, $val->CURVA);
					$sheet->setCellValue('AP' . $rows, $val->PBM);
					$sheet->setCellValue('AQ' . $rows, $val->SITUACAO_DESCONTINUADO);
					$sheet->setCellValue('AR' . $rows, $val->MARCA);
					$sheet->setCellValue('AS' . $rows, $val->FABRICANTE);
					$sheet->setCellValue('AT' . $rows, $val->OTC);
					$sheet->setCellValue('AU' . $rows, $val->DESCONTINUADO);
					$sheet->setCellValue('AV' . $rows, $val->CONTROLADO);
					$sheet->setCellValue('AW' . $rows, $val->ATIVO);
					$sheet->setCellValue('AX' . $rows, $val->pmc);
					$sheet->setCellValue('AY' . $rows, $val->preco_fabrica);
					$sheet->setCellValue('AZ' . $rows, $val->ACAO);
					$sheet->setCellValue('BA' . $rows, $val->SUBCATEGORIA);
					$rows++;
			}
			return $spreadsheet;
	}

	public function groups($group) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'SKU');
			$sheet->setCellValue('B1', 'NOME');
			$sheet->setCellValue('C1', 'DEPARTAMENTO');
			$sheet->setCellValue('D1', 'CATEGORIA');
			$sheet->setCellValue('E1', 'QTD');
			$sheet->setCellValue('F1', 'VMD_ULT_7');
			$sheet->setCellValue('G1', 'PERCENTUAL_VMD_ULT_7');
			$sheet->setCellValue('H1', 'VMD_ULT_MES');
			$sheet->setCellValue('I1', 'PERCENTUAL_VMD_ULT_MES');
			$sheet->setCellValue('J1', 'VMD_ULT_3_MESES');
			$sheet->setCellValue('K1', 'FATURAMENTO');
			$sheet->setCellValue('L1', 'SUBCATEGORIA');
			$rows = 2;
			$db = \Config\Database::connect();

			if ($group === "Termolábil") $comp = " and Products.termolabil = 1";
			else if ($group === "OTC") $comp = " and Products.otc = 1";
			else if ($group === "Controlados") $comp = " and Products.controlled_substance = 1";
			else if ($group === "PBM") $comp = " and Products.pbm = 1";
			else if ($group === "Cashback") $comp = " and Products.cashback > 0";
			else if ($group === "Home") $comp = " and Products.home = 1";
			else if ($group === "Autocuidado") $comp = " and Products.category = 'AUTOCUIDADO'";
			else if ($group === "Similar") $comp = " and Products.category = 'SIMILAR'";
			else if ($group === "Marca") $comp = " and Products.category = 'MARCA'";
			else if ($group === "Genérico") $comp = " and Products.category = 'GENERICO'";
			else if ($group === "Higiene e Beleza") $comp = " and Products.category = 'HIGIENE' OR Products.category = 'HIGIENE E BELEZA'";
			else if ($group === "Mamãe e Bebê") $comp = " and Products.category = 'MAMÃE E BEBÊ'";
			else if ($group === "Dermocosmético") $comp = " and Products.category = 'DERMOCOSMETICO'";
			else if ($group === "Beleza") $comp = " and Products.category = 'BELEZA'";
			else if ($group === "Perdendo") $comp = " and Products.diff_pay_only_lowest < 0";
			else if ($group === "0 Cashback") $comp = " and Products.acao = '$group'";
			else if ($group === "5%   5% Progress") $comp = " and Products.acao = '5% + 5% Progress'";
			else if ($group === "Vencimento") $comp = " and Products.acao = '$group'";
			else if ($group === "5% progressivo") $comp = " and Products.acao = '$group'";
			else if ($group === "Aumento TKM") $comp = " and Products.acao = '$group'";
			else if ($group === "PREGO") $comp = " and Products.acao = '$group'";
			else if ($group === "3% Progressivo") $comp = " and Products.acao = '$group'";
			else if ($group === "3%   5% Progressivo") $comp = " and Products.acao = '3% + 5% Progressivo'";
			else if ($group === "AUMENTO FAT 35%") $comp = " and Products.acao = 'AUMENTO FAT 35%'";
			else if ($group === "AUMENTO FAT 16%") $comp = " and Products.acao = 'AUMENTO FAT 16%'";
			else if ($group === "AUMENTO FAT 25%") $comp = " and Products.acao = 'AUMENTO FAT 25%'";
			else if ($group === "CASHBACK 0") $comp = " and Products.acao = 'CASHBACK 0'";
			else if ($group === "CASHBACK 0   20%") $comp = " and Products.acao = 'CASHBACK 0 + 20%'";
			else if ($group === "Regiane Ago") $comp = " and Products.acao = 'Regiane Ago'";
			else if ($group === "genericos 20/07") $comp = " and Products.acao = 'genericos 20/07'";
			else if ($group === "Analytics") $comp = " and Products.acao = 'Analytics'";
			else if ($group === "sem_venda") $comp = " and Products.acao = 'sem_venda'";
			else if ($group === "tabloide") $comp = " and Products.acao = 'tabloide'";
			else if ($group === "Preco Sug") $comp = " and Products.acao = 'Preco Sug'";
			else if ($group === "vencimento") $comp = " and Products.acao = 'vencimento'";
			else if ($group === "MIP") $comp = " and Products.sub_category = 'MIP'";
			else if ($group === "Éticos") $comp = " and Products.sub_category = 'Eticos'";
			else if ($group === "No Medicamentos") $comp = " and Products.sub_category = 'No Medicamentos'";
			else if ($group === "Perfumaria") $comp = " and Products.sub_category = 'Perfumaria'";
			else if ($group === "Genéricos") $comp = " and Products.sub_category = 'Genericos'";
			else if ($group === "Dermocosméticos") $comp = " and Products.sub_category = 'Dermocosmeticos'";
			else if ($group === "Similares") $comp = " and Products.sub_category = 'Similar'";
			else if ($group !== "") $comp = " and Products.marca = '".strtoupper($group)."'";

			$products = $db->query("Select vendas.sku as SKU,
															Products.title as NOME,
															vendas.department as DEPARTAMENTO,
															Products.category as CATEGORIA,
															sum(vendas.qtd) as QTD,
															format(sum(vendas.faturamento),2,'de_DE') as FATURAMENTO,
															Products.sub_category as SUBCATEGORIA
															from vendas inner join Products on vendas.sku=Products.sku WHERE 1=1 $comp group by Products.sku")->getResult();
			$skus = implode("', '", array_map(function ($ar) { return $ar->SKU; }, $products));

			// Últimos 7 dias
			$weekly_query = $db->query("Select sku AS SKU, sum(qtd)/7 as weekly
																	from vendas WHERE data >= '".date('Y-m-d', strtotime("-7 days"))."'
																	and data <= '".date('Y-m-d')."'
																	and sku in ('$skus') group by sku", false)->getResult();

			// Últimos 30 dias
			$last_month_query = $db->query("Select sku AS SKU, sum(qtd)/30 as last_month
																			from vendas WHERE data >= '".date('Y-m-d', strtotime("-30 days"))."'
																			and data <= '".date('Y-m-d')."'
																			and sku in ('$skus') group by sku", false)->getResult();

			// Últimos 90 dias
			$last_3_months_query = $db->query("Select sku AS SKU, sum(qtd)/90 as last_3_months
																				 from vendas WHERE data >= '".date('Y-m-d', strtotime("-90 days"))."'
																				 and data <= '".date('Y-m-d')."'
				 																 and sku in ('$skus') group by sku", false)->getResult();

			foreach ($products as $val){
					$sku = $val->SKU;
					$ar = array_filter($weekly_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$weekly = isset(current((array)$ar)->weekly) ? current((array)$ar)->weekly : 0;

					$ar = array_filter($last_month_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$last_month = isset(current((array)$ar)->last_month) ? current((array)$ar)->last_month : 0;

					$ar = array_filter($last_3_months_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$last_3_months = isset(current((array)$ar)->last_3_months) ? current((array)$ar)->last_3_months : 0;

					if($weekly == 0) $percentual_vmd_ult_7 = 0;
					else $percentual_vmd_ult_7 = ($last_month == 0) ? 0 : number_to_amount((($weekly/$last_month) - 1)*100, 2, 'pt_BR');

					if($last_month == 0) $percentual_vmd_ult_mes = 0;
					else $percentual_vmd_ult_mes = ($last_3_months == 0) ? 0 : number_to_amount((($last_month/$last_3_months) - 1)*100, 2, 'pt_BR');

					$sheet->setCellValue('A' . $rows, $val->SKU);
					$sheet->setCellValue('B' . $rows, $val->NOME);
					$sheet->setCellValue('C' . $rows, $val->DEPARTAMENTO);
					$sheet->setCellValue('D' . $rows, $val->CATEGORIA);
					$sheet->setCellValue('E' . $rows, $val->QTD);
					$sheet->setCellValue('F' . $rows, $weekly);
					$sheet->setCellValue('G' . $rows, $percentual_vmd_ult_7."%");
					$sheet->setCellValue('H' . $rows, $last_month);
					$sheet->setCellValue('I' . $rows, $percentual_vmd_ult_mes."%");
					$sheet->setCellValue('J' . $rows, $last_3_months);
					$sheet->setCellValue('K' . $rows, $val->FATURAMENTO);
					$sheet->setCellValue('L' . $rows, $val->SUBCATEGORIA);
					$rows++;
			}
			return $spreadsheet;
	}

	public function sales($department, $sales_date) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'SKU');
			$sheet->setCellValue('B1', 'NOME');
			$sheet->setCellValue('C1', 'DEPARTAMENTO');
			$sheet->setCellValue('D1', 'CATEGORIA');
			$sheet->setCellValue('E1', 'QTD');
			$sheet->setCellValue('F1', 'VMD_ULT_7');
			$sheet->setCellValue('G1', 'PERCENTUAL_VMD_ULT_7');
			$sheet->setCellValue('H1', 'VMD_ULT_MES');
			$sheet->setCellValue('I1', 'PERCENTUAL_VMD_ULT_MES');
			$sheet->setCellValue('J1', 'VMD_ULT_3_MESES');
			$sheet->setCellValue('K1', 'FATURAMENTO');
			$sheet->setCellValue('L1', 'SUBCATEGORIA');
			$rows = 2;
			$db = \Config\Database::connect();

			$comp = '';
			if ($department !== "geral") $comp = " and vendas.department = '$department'";
			$products = $db->query("Select vendas.sku as SKU,
															Products.title as NOME,
															vendas.department as DEPARTAMENTO,
															Products.category as CATEGORIA,
															sum(vendas.qtd) as QTD,
															format(sum(vendas.faturamento),2,'de_DE') as FATURAMENTO,
															Products.sub_category as SUBCATEGORIA
															 from vendas inner join Products on vendas.sku=Products.sku WHERE 1=1 and vendas.data = '$sales_date' $comp group by Products.sku")->getResult();

			 $skus = implode("', '", array_map(function ($ar) { return $ar->SKU; }, $products));

		  // Últimos 7 dias
		  $weekly_query = $db->query("Select sku AS SKU, sum(qtd)/7 as weekly
		 														  from vendas WHERE data >= '".date('Y-m-d', strtotime("-7 days"))."'
		 														  and data <= '".date('Y-m-d')."'
		 														  and sku in ('$skus') group by sku", false)->getResult();

		  // Últimos 30 dias
		  $last_month_query = $db->query("Select sku AS SKU, sum(qtd)/30 as last_month
		 																  from vendas WHERE data >= '".date('Y-m-d', strtotime("-30 days"))."'
		 																  and data <= '".date('Y-m-d')."'
		 																  and sku in ('$skus') group by sku", false)->getResult();

		  // Últimos 90 dias
		  $last_3_months_query = $db->query("Select sku AS SKU, sum(qtd)/90 as last_3_months
		 																	   from vendas WHERE data >= '".date('Y-m-d', strtotime("-90 days"))."'
		 																	   and data <= '".date('Y-m-d')."'
		 	 																   and sku in ('$skus') group by sku", false)->getResult();
			foreach ($products as $val){
					$sku = $val->SKU;
					$ar = array_filter($weekly_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$weekly = isset(current((array)$ar)->weekly) ? current((array)$ar)->weekly : 0;

					$ar = array_filter($last_month_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$last_month = isset(current((array)$ar)->last_month) ? current((array)$ar)->last_month : 0;

					$ar = array_filter($last_3_months_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$last_3_months = isset(current((array)$ar)->last_3_months) ? current((array)$ar)->last_3_months : 0;

					if($weekly == 0) $percentual_vmd_ult_7 = 0;
					else $percentual_vmd_ult_7 = ($last_month == 0) ? 0 : number_to_amount((($weekly/$last_month) - 1)*100, 2, 'pt_BR');

					if($last_month == 0) $percentual_vmd_ult_mes = 0;
					else $percentual_vmd_ult_mes = ($last_3_months == 0) ? 0 : number_to_amount((($last_month/$last_3_months) - 1)*100, 2, 'pt_BR');

					$sheet->setCellValue('A' . $rows, $val->SKU);
					$sheet->setCellValue('B' . $rows, $val->NOME);
					$sheet->setCellValue('C' . $rows, $val->DEPARTAMENTO);
					$sheet->setCellValue('D' . $rows, $val->CATEGORIA);
					$sheet->setCellValue('E' . $rows, $val->QTD);
					$sheet->setCellValue('F' . $rows, $weekly);
					$sheet->setCellValue('G' . $rows, $percentual_vmd_ult_7."%");
					$sheet->setCellValue('H' . $rows, $last_month);
					$sheet->setCellValue('I' . $rows, $percentual_vmd_ult_mes."%");
					$sheet->setCellValue('J' . $rows, $last_3_months);
					$sheet->setCellValue('K' . $rows, $val->FATURAMENTO);
					$sheet->setCellValue('L' . $rows, $val->SUBCATEGORIA);
					$rows++;
			}
			return $spreadsheet;
	}

	public function top_products($department) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'SKU');
			$sheet->setCellValue('B1', 'NOME');
			$sheet->setCellValue('C1', 'DEPARTAMENTO');
			$sheet->setCellValue('D1', 'CATEGORIA');
			$sheet->setCellValue('E1', 'QTD');
			$sheet->setCellValue('F1', 'VMD_ULT_7');
			$sheet->setCellValue('G1', 'PERCENTUAL_VMD_ULT_7');
			$sheet->setCellValue('H1', 'VMD_ULT_MES');
			$sheet->setCellValue('I1', 'PERCENTUAL_VMD_ULT_MES');
			$sheet->setCellValue('J1', 'VMD_ULT_3_MESES');
			$sheet->setCellValue('K1', 'FATURAMENTO');
			$sheet->setCellValue('L1', 'SUBCATEGORIA');
			$rows = 2;
			$db = \Config\Database::connect();

			$products = $db->query("Select vendas.sku as SKU,
																		 Products.title as NOME,
																		 vendas.department as DEPARTAMENTO,
																		 Products.category as CATEGORIA,
																		 sum(vendas.qtd) as QTD,
																		 format(sum(vendas.faturamento),2,'de_DE') as FATURAMENTO,
																		 Products.sub_category as SUBCATEGORIA
														  from vendas inner join Products on vendas.sku=Products.sku
															WHERE vendas.data >= '".date('Y-m-d', strtotime("-90 days"))."'
															group by Products.sku order by sum(vendas.faturamento) desc LIMIT 2200")->getResult();

			$products = array_filter($products, function($item) use($department) { return $item->DEPARTAMENTO == strtoupper($department); });
			$skus = implode("', '", array_map(function ($ar) { return $ar->SKU; }, $products));

		  // Últimos 7 dias
		  $weekly_query = $db->query("Select sku AS SKU, sum(qtd)/7 as weekly
		 														  from vendas WHERE data >= '".date('Y-m-d', strtotime("-7 days"))."'
		 														  and data <= '".date('Y-m-d')."'
		 														  and sku in ('$skus') group by sku", false)->getResult();

		  // Últimos 30 dias
		  $last_month_query = $db->query("Select sku AS SKU, sum(qtd)/30 as last_month
		 																  from vendas WHERE data >= '".date('Y-m-d', strtotime("-30 days"))."'
		 																  and data <= '".date('Y-m-d')."'
		 																  and sku in ('$skus') group by sku", false)->getResult();

		  // Últimos 90 dias
		  $last_3_months_query = $db->query("Select sku AS SKU, sum(qtd)/90 as last_3_months
		 																	   from vendas WHERE data >= '".date('Y-m-d', strtotime("-90 days"))."'
		 																	   and data <= '".date('Y-m-d')."'
		 	 																   and sku in ('$skus') group by sku", false)->getResult();
			foreach ($products as $val){
					$sku = $val->SKU;
					$ar = array_filter($weekly_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$weekly = isset(current((array)$ar)->weekly) ? current((array)$ar)->weekly : 0;

					$ar = array_filter($last_month_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$last_month = isset(current((array)$ar)->last_month) ? current((array)$ar)->last_month : 0;

					$ar = array_filter($last_3_months_query, function($item) use($sku) {
							return $item->SKU == $sku;
					});
					$last_3_months = isset(current((array)$ar)->last_3_months) ? current((array)$ar)->last_3_months : 0;

					if($weekly == 0) $percentual_vmd_ult_7 = 0;
					else $percentual_vmd_ult_7 = ($last_month == 0) ? 0 : number_to_amount((($weekly/$last_month) - 1)*100, 2, 'pt_BR');

					if($last_month == 0) $percentual_vmd_ult_mes = 0;
					else $percentual_vmd_ult_mes = ($last_3_months == 0) ? 0 : number_to_amount((($last_month/$last_3_months) - 1)*100, 2, 'pt_BR');

					$sheet->setCellValue('A' . $rows, $val->SKU);
					$sheet->setCellValue('B' . $rows, $val->NOME);
					$sheet->setCellValue('C' . $rows, $val->DEPARTAMENTO);
					$sheet->setCellValue('D' . $rows, $val->CATEGORIA);
					$sheet->setCellValue('E' . $rows, $val->QTD);
					$sheet->setCellValue('F' . $rows, $weekly);
					$sheet->setCellValue('G' . $rows, $percentual_vmd_ult_7."%");
					$sheet->setCellValue('H' . $rows, $last_month);
					$sheet->setCellValue('I' . $rows, $percentual_vmd_ult_mes."%");
					$sheet->setCellValue('J' . $rows, $last_3_months);
					$sheet->setCellValue('K' . $rows, $val->FATURAMENTO);
					$sheet->setCellValue('L' . $rows, $val->SUBCATEGORIA);
					$rows++;
			}
			return $spreadsheet;
	}

	public function sales_custom($initial_date, $final_date, $department, $category, $action, $group, $sub_category) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'SKU');
			$sheet->setCellValue('B1', 'NOME DO PRODUTO');
			$sheet->setCellValue('C1', 'DEPARTAMENTO');
			$sheet->setCellValue('D1', 'CATEGORIA');
			$sheet->setCellValue('E1', 'QUANTIDADE');
			$sheet->setCellValue('F1', 'FATURAMENTO');
			$sheet->setCellValue('G1', 'PREÇO DE CUSTO');
			$sheet->setCellValue('H1', 'MARGEM');
			$rows = 2;
			$db = \Config\Database::connect();
			$comp = '';
			if($department != "") $comp .= " and v.department = '$department'";
			if($category != "") $comp .= " and v.category = '$category'";
			if($action != "") $comp .= " and p.acao = '$action'";
			if($sub_category != "") $comp .= " and p.sub_category = '$sub_category'";
			if($group == "cashback") $comp .= " and p.cashback > 0";
			else if($group != "") $comp .= " and p.".$group." = 1";
			$items = $db->query("Select v.sku, p.title, v.department, v.category, sum(v.qtd) as qtd, v.faturamento, v.price_cost, ((v.faturamento - v.price_cost)/v.faturamento)*100 as margin
													 FROM vendas v
													 INNER JOIN Products p ON p.sku = v.sku
													 where v.data >= '$initial_date' and v.data <= '$final_date' $comp
													 group by p.sku order by v.data, p.title desc")->getResult();
			foreach ($items as $val){
					$sheet->setCellValue('A' . $rows, $val->sku);
					$sheet->setCellValue('B' . $rows, $val->title);
					$sheet->setCellValue('C' . $rows, $val->department);
					$sheet->setCellValue('D' . $rows, $val->category);
					$sheet->setCellValue('E' . $rows, $val->qtd);
					$sheet->setCellValue('F' . $rows, $val->faturamento);
					$sheet->setCellValue('G' . $rows, $val->price_cost);
					$sheet->setCellValue('H' . $rows, $val->margin);
					$rows++;
			}
			return $spreadsheet;
	}
}
