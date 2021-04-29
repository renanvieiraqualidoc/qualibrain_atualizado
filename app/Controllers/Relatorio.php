<?php

namespace App\Controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Relatorio extends BaseController
{
	public function index() {
			$fileName = 'relatorio.xlsx';
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
			$sheet->setCellValue('X1', 'MENOR_PRECO_POR_AI');
			$sheet->setCellValue('Y1', 'MARGEM_VALOR');
			$sheet->setCellValue('Z1', 'CASHBACK');
			$sheet->setCellValue('AA1', 'MARGEM_APOS_CASHBACK');
			$sheet->setCellValue('AB1', 'MARGEM_BRUTA_PORCENTO');
			$sheet->setCellValue('AC1', 'DIFERENCA_PARA_O_MENOR_CONCORRENTE');
			$sheet->setCellValue('AD1', 'CURVA');
			$sheet->setCellValue('AE1', 'PBM');
			$sheet->setCellValue('AF1', 'SITUACAO_DESCONTINUADO');
			$sheet->setCellValue('AG1', 'MARCA');
			$sheet->setCellValue('AH1', 'FABRICANTE');
			$sheet->setCellValue('AI1', 'OTC');
			$sheet->setCellValue('AJ1', 'DESCONTINUADO');
			$sheet->setCellValue('AK1', 'CONTROLADO');
			$sheet->setCellValue('AL1', 'ATIVO');
			$sheet->setCellValue('AM1', 'ACAO');
			$rows = 2;

			foreach ($users as $val){
					$sheet->setCellValue('A' . $rows, $val['SKU']);
					$sheet->setCellValue('B' . $rows, $val['VENDA_ACUMULADA']);
					$sheet->setCellValue('C' . $rows, $val['EAN']);
					$sheet->setCellValue('D' . $rows, $val['NOME']);
					$sheet->setCellValue('E' . $rows, $val['PRINCIPIO_ATIVO']);
					$sheet->setCellValue('F' . $rows, $val['APRESENTACAO']);
					$sheet->setCellValue('G' . $rows, $val['DEPARTAMENTO']);
					$sheet->setCellValue('H' . $rows, $val['CATEGORIA']);
					$sheet->setCellValue('I' . $rows, $val['SITUACAO']);
					$sheet->setCellValue('J' . $rows, $val['STATUS']);
					$sheet->setCellValue('K' . $rows, $val['ESTOQUE_RMS']);
					$sheet->setCellValue('L' . $rows, $val['ESTOQUE_OCC']);
					$sheet->setCellValue('M' . $rows, $val['DIFERENCA_OCC_RMS']);
					$sheet->setCellValue('N' . $rows, $val['PRECO_TABELADO']);
					$sheet->setCellValue('O' . $rows, $val['SUGESTAO_TABELADO']);
					$sheet->setCellValue('P' . $rows, $val['MENOR_PRECO']);
					$sheet->setCellValue('Q' . $rows, $val['CONCORRENTE_MENOR_PRECO']);
					$sheet->setCellValue('R' . $rows, $val['QTD_CONCORRENTES']);
					$sheet->setCellValue('S' . $rows, $val['QTD_CONCORRENTES_ATIVOS']);
					$sheet->setCellValue('T' . $rows, $val['CUSTO']);
					$sheet->setCellValue('U' . $rows, $val['MARGEM_BRUTA']);
					$sheet->setCellValue('V' . $rows, $val['PRECO_DE_VENDA']);
					$sheet->setCellValue('W' . $rows, $val['PAGUE_APENAS']);
					$sheet->setCellValue('X' . $rows, $val['MENOR_PRECO_POR_AI']);
					$sheet->setCellValue('Y' . $rows, $val['MARGEM_VALOR']);
					$sheet->setCellValue('Z' . $rows, $val['CASHBACK']);
					$sheet->setCellValue('AA' . $rows, $val['MARGEM_APOS_CASHBACK']);
					$sheet->setCellValue('AB' . $rows, $val['MARGEM_BRUTA_PORCENTO']);
					$sheet->setCellValue('AC' . $rows, $val['DIFERENCA_PARA_O_MENOR_CONCORRENTE']);
					$sheet->setCellValue('AD' . $rows, $val['CURVA']);
					$sheet->setCellValue('AE' . $rows, $val['PBM']);
					$sheet->setCellValue('AF' . $rows, $val['SITUACAO_DESCONTINUADO']);
					$sheet->setCellValue('AG' . $rows, $val['MARCA']);
					$sheet->setCellValue('AH' . $rows, $val['FABRICANTE']);
					$sheet->setCellValue('AI' . $rows, $val['OTC']);
					$sheet->setCellValue('AJ' . $rows, $val['DESCONTINUADO']);
					$sheet->setCellValue('AK' . $rows, $val['CONTROLADO']);
					$sheet->setCellValue('AL' . $rows, $val['ATIVO']);
					$sheet->setCellValue('AM' . $rows, $val['ACAO']);
			    $rows++;
			}

			$writer = new Xlsx($spreadsheet);
      $writer->save("upload/".$fileName);
      header("Content-Type: application/vnd.ms-excel");
	}
}
