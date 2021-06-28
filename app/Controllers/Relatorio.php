<?php

namespace App\Controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\I18n\Time;

class Relatorio extends BaseController
{
	public function index() {
			ini_set('memory_limit', '-1');
			date_default_timezone_set('America/Sao_Paulo');
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
							$fileName = "relatorio_{$_GET['type']}_{$_GET['group']}_".date('d-m-Y_h.i', time()).".xlsx";
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
					default:
							$fileName = "relatorio_teste_".date('d-m-Y_h.i', time()).".xlsx";
							$spreadsheet = $this->teste();
							break;
			}

			$writer = new Xlsx($spreadsheet);
      $writer->save("relatorios/$fileName");
      return $this->response->download("relatorios/$fileName", null);
	}

	public function getAccessToken() {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/mfalogin',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJmZDliODA0Ny0xZTQ0LTQyNDUtOTZjZS0wN2FjZDYxNjM0MTYiLCJpc3MiOiJhcHBsaWNhdGlvbkF1dGgiLCJleHAiOjE2NDc3MTYxNzgsImlhdCI6MTYxNjE4MDE3OH0=.lar5akDJChHi6y5rM4q+52oyBW6ZABaZqxca3l5x8i0=',
					'Content-Type: application/x-www-form-urlencoded',
					'Cookie: ak_bmsc=B0DA7A323CCF7EEEF09BC2F1D06C0DF2~000000000000000000000000000000~YAAQLNlHaG69jep5AQAAna0gPwwW8BmenWN7uhstqDUXMfE3fcfVuMssZJjkNG61PftpoNiTTeCMyTFpeC24UDqsxIGHpcClEvdiZp6a/HUIXybLBDHHrL/YDz65jn+sR1dTE2s8mu9wWoUcnaXVtsaZAHay1iDbudS7Iwbj6LZ7XPdoISobuPL+r1OfiQX74drAbFEKila5nSZbOQh2T5M1i6lGSmr5iTgXlJKAzabUsANrUrpX+iJfbnR5AohVV5txlNJ+CqBB93BYgZBm02alViMIPbe54HEHRPVJjKEZ8l9hxZEvtJG166NdIbcH8VW+Ayb5aNMARW40f3oAvaeo9IRniLCWwqGDo+BFJh2HUUClU/Ff12o9SZ8Vwd8LxVScrZJDnQEG4g==; bm_sv=9ADAA84BDAD604C26EE710AF5AEF492B~JnERepSXDY/oNwTGKqJ4uCPx0znlDyMYjtttdraIgdB+V+5O7oMfu9b3aKVTbKGrpMLHHidBV/thc+lniFPhAy6sMv9S3a6KV8ZGYvAJSaO7w6e1stY55FON0hPkP8wtY+55SiOlNvpyMaSEqvbw+HuGLv+SeH8c73eJJmYv9fQ=; JSESSIONID=7io_KPJFCBsFF2bfFe9n_lqShY7ozRuRlkx7xKlcL2KS64_l9Cp9!949573338; ccadminroute=c883409ac6f3445961d6875f36fb8313'
				),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			return json_decode($response)->access_token;
	}

	public function getOrder($order_id) {
			$access_token = $this->getAccessToken();
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/orders/'.$order_id,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer '.$access_token,
					'Cookie: ak_bmsc=21EF931A4BD8786A02F2A6124CA68CE1~000000000000000000000000000000~YAAQLNlHaBwGnep5AQAA9f00QwwMxXjmNG0ZpUBRj/U5F37DRBvl2oVUWrTWC6wu/8baxDnDuiWP6mimteiKFaPXQQAO2OZHMnILt5K3kwWKzZyrV8NTlWpL/hC5KlMatuUKja+SEPF3pV2tReXZGyot5wv2RgInt82PnNcB1+p0s127opVewA9oPdX69CYojr/cqaMOOdiqpURoYYi0+iY1RvUSTwwfctSXo/i4TY7nrWHxlW+hoLYEF6deZSyHYInvclKsUAXf5XLo/DL0VIFXczd0mcTtJOaE+QrS7m1tTIJKvw6gGULW1kyhiRnUMUZsKghhYdswjKcbdLhM2QWBg4JtmS52hz9/wOtaTtSGf0Y9bHhbpi2t+J5tzwpfhjW/9Zlhrt0GVA==; bm_sv=90ECEE14762C8D4CDA9A121223867D75~X78bdGwB4czS8fCPeSrb8rpw5ahaVkvGHbmT3BOECTUrwkNJdNtmCF7cK/BsJa0E5IVD0Yg+8W1dY+XzUt8BJpawLiuBMYTmlE4w/fgjT1x5J4UqNoSBBNgy6rSjDzmvw3ES50wNSG8R0RYwPPceUaeqPk1bSci25JVP1u1hgxY=; JSESSIONID=5KxDNP2JDiiX_K9mmS0A6IbXabVQZjtW8XMbBF30E-AqkzBh5THU!-308053582; ccadminroute=53756eaba3c2b26cab6435c3a5690773'
				),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			return json_decode($response);
	}

	public function getProfile($profile_id) {
			$access_token = $this->getAccessToken();
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/profiles/'.$profile_id,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer '.$access_token,
					'Cookie: ak_bmsc=21EF931A4BD8786A02F2A6124CA68CE1~000000000000000000000000000000~YAAQLNlHaBwGnep5AQAA9f00QwwMxXjmNG0ZpUBRj/U5F37DRBvl2oVUWrTWC6wu/8baxDnDuiWP6mimteiKFaPXQQAO2OZHMnILt5K3kwWKzZyrV8NTlWpL/hC5KlMatuUKja+SEPF3pV2tReXZGyot5wv2RgInt82PnNcB1+p0s127opVewA9oPdX69CYojr/cqaMOOdiqpURoYYi0+iY1RvUSTwwfctSXo/i4TY7nrWHxlW+hoLYEF6deZSyHYInvclKsUAXf5XLo/DL0VIFXczd0mcTtJOaE+QrS7m1tTIJKvw6gGULW1kyhiRnUMUZsKghhYdswjKcbdLhM2QWBg4JtmS52hz9/wOtaTtSGf0Y9bHhbpi2t+J5tzwpfhjW/9Zlhrt0GVA==; bm_sv=90ECEE14762C8D4CDA9A121223867D75~X78bdGwB4czS8fCPeSrb8rpw5ahaVkvGHbmT3BOECTUrwkNJdNtmCF7cK/BsJa0E5IVD0Yg+8W1dY+XzUt8BJpawLiuBMYTmlE4w/fgjT1x5J4UqNoSBBNgy6rSjDzmvqvo6n0CQY+6fZWzOGeWHpykZvFZlIfIlZWTy0Fr7hr4=; JSESSIONID=h35DQLrTYXDq72ACsJTIM8ZVZyC-Dva6mXwjn6fbm5tnPrpfrnf6!-308053582; ccadminroute=53756eaba3c2b26cab6435c3a5690773'
				),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			return json_decode($response);
	}

	public function mgm() {
			ini_set('memory_limit', '-1');
			date_default_timezone_set('America/Sao_Paulo');
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'NOME DO CLIENTE');
			$sheet->setCellValue('B1', 'VALOR DO PEDIDO');
			$sheet->setCellValue('C1', 'DATA DO PEDIDO');
			$sheet->setCellValue('D1', 'QUEM INDICOU');
			$rows = 2;
			$ar_coupons = ['QUALIDOC10', 'QUALIDOC30'];
			$mgm = [];
			$limit = 250;
			$access_token = $this->getAccessToken();
			$curl = curl_init();
			$initial_date = date("Y-m-d\TH:i:s.000\Z", strtotime('now -1 hour'));
			$final_date = date("Y-m-d\TH:i:s.000\Z", strtotime('now'));
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/orders?limit=1&offset=0&queryFormat=SCIM&q=(state%20eq%20%22PROCESSING%22%20or%20state%20eq%20%22NO_PENDING_ACTION%22)%20and%20submittedDate%20ge%20%22'.$initial_date.'%22%20and%20submittedDate%20le%20%22'.$final_date.'%22%20and%20siteId%20eq%20%22siteUS%22%20and%20x_nota_fiscal%20pr',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer '.$access_token,
					'Cookie: ak_bmsc=B0DA7A323CCF7EEEF09BC2F1D06C0DF2~000000000000000000000000000000~YAAQLNlHaG69jep5AQAAna0gPwwW8BmenWN7uhstqDUXMfE3fcfVuMssZJjkNG61PftpoNiTTeCMyTFpeC24UDqsxIGHpcClEvdiZp6a/HUIXybLBDHHrL/YDz65jn+sR1dTE2s8mu9wWoUcnaXVtsaZAHay1iDbudS7Iwbj6LZ7XPdoISobuPL+r1OfiQX74drAbFEKila5nSZbOQh2T5M1i6lGSmr5iTgXlJKAzabUsANrUrpX+iJfbnR5AohVV5txlNJ+CqBB93BYgZBm02alViMIPbe54HEHRPVJjKEZ8l9hxZEvtJG166NdIbcH8VW+Ayb5aNMARW40f3oAvaeo9IRniLCWwqGDo+BFJh2HUUClU/Ff12o9SZ8Vwd8LxVScrZJDnQEG4g==; bm_sv=9ADAA84BDAD604C26EE710AF5AEF492B~JnERepSXDY/oNwTGKqJ4uCPx0znlDyMYjtttdraIgdB+V+5O7oMfu9b3aKVTbKGrpMLHHidBV/thc+lniFPhAy6sMv9S3a6KV8ZGYvAJSaO7w6e1stY55FON0hPkP8wtFVmHmeyTCnUTjJd1B+LpBXwb1ARJUalpaZeYXgKTjig=; JSESSIONID=cgE_RgSZgDU90XvDmATMkjaGxN3eHM98tS0rQl4bHoDFeGYeaMw_!949573338; ccadminroute=c883409ac6f3445961d6875f36fb8313'
				),
			));
			$response = json_decode(curl_exec($curl));
			curl_close($curl);
			$total_pages = round($response->totalResults/$limit);

			for($i = 0; $i < ($total_pages+1); $i++) {
					$access_token = $this->getAccessToken();
					$curl = curl_init();
					$offset = $limit*$i;
					curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/orders?limit='.$limit.'&offset='.$offset.'&queryFormat=SCIM&q=(state%20eq%20%22PROCESSING%22%20or%20state%20eq%20%22NO_PENDING_ACTION%22)%20and%20submittedDate%20ge%20%22'.$initial_date.'%22%20and%20submittedDate%20le%20%22'.$final_date.'%22%20and%20siteId%20eq%20%22siteUS%22%20and%20x_nota_fiscal%20pr',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'GET',
						CURLOPT_HTTPHEADER => array(
							'Authorization: Bearer '.$access_token,
							'Cookie: ak_bmsc=B0DA7A323CCF7EEEF09BC2F1D06C0DF2~000000000000000000000000000000~YAAQLNlHaG69jep5AQAAna0gPwwW8BmenWN7uhstqDUXMfE3fcfVuMssZJjkNG61PftpoNiTTeCMyTFpeC24UDqsxIGHpcClEvdiZp6a/HUIXybLBDHHrL/YDz65jn+sR1dTE2s8mu9wWoUcnaXVtsaZAHay1iDbudS7Iwbj6LZ7XPdoISobuPL+r1OfiQX74drAbFEKila5nSZbOQh2T5M1i6lGSmr5iTgXlJKAzabUsANrUrpX+iJfbnR5AohVV5txlNJ+CqBB93BYgZBm02alViMIPbe54HEHRPVJjKEZ8l9hxZEvtJG166NdIbcH8VW+Ayb5aNMARW40f3oAvaeo9IRniLCWwqGDo+BFJh2HUUClU/Ff12o9SZ8Vwd8LxVScrZJDnQEG4g==; bm_sv=9ADAA84BDAD604C26EE710AF5AEF492B~JnERepSXDY/oNwTGKqJ4uCPx0znlDyMYjtttdraIgdB+V+5O7oMfu9b3aKVTbKGrpMLHHidBV/thc+lniFPhAy6sMv9S3a6KV8ZGYvAJSaO7w6e1stY55FON0hPkP8wtFVmHmeyTCnUTjJd1B+LpBXwb1ARJUalpaZeYXgKTjig=; JSESSIONID=cgE_RgSZgDU90XvDmATMkjaGxN3eHM98tS0rQl4bHoDFeGYeaMw_!949573338; ccadminroute=c883409ac6f3445961d6875f36fb8313'
						),
					));
					$response = json_decode(curl_exec($curl));
					curl_close($curl);
					foreach($response->items as $item) {
							foreach($item->commerceItems as $price) {
									if(!empty($price->priceInfo->orderDiscountInfos)) {
											foreach($price->priceInfo->orderDiscountInfos as $coupons) {
													if(isset($coupons->couponCodes)) {
															foreach($coupons->couponCodes as $coupon) {
																	if(in_array($coupon, $ar_coupons)) {
																			if(!in_array($item->id, array_column($mgm, 'id_order'))) {
																					array_push($mgm, array('id_order' => $item->id,
																																 'order_date' => $item->submittedDate,
																																 'order_status' => $item->state,
																																 'client_name' => $item->profile->firstName." ".$item->profile->lastName,
																																 'client_email' => $item->profile->email,
																																 'profile_id' => $item->profileId));
																			}
																	}
															}
													}
											}
									}
							}
					}
			}
			$sql = "";
			foreach($mgm as $item) {
					$order = $this->getOrder($item['id_order']);
					$item['value'] = $order->priceInfo->amount;
					$profile = $this->getProfile($item['profile_id']);
					$indicator_profile = $this->getProfile($profile->x_mgm_indicator);
					$item['indicator_name'] = $indicator_profile->firstName." ".$indicator_profile->lastName;
					$item['indicator_email'] = $indicator_profile->email;
					$sheet->setCellValue('A' . $rows, $item['client_name']);
					$sheet->setCellValue('B' . $rows, $item['value']);
					$sheet->setCellValue('C' . $rows, date('G:i d/m/Y', strtotime($item['order_date'])));
					$sheet->setCellValue('D' . $rows, $item['indicator_name']);
					$rows++;
					$sql .= "INSERT INTO mgm VALUES ('{$item['id_order']}',
																					 '{$item['client_name']}',
																					 {$item['value']},
																					 '".date('Y-m-d G:i:s', strtotime($item['order_date']))."',
																					 '{$item['indicator_name']}',
																					 '{$item['order_status']}',
																					 '{$item['client_email']}',
																					 '{$item['indicator_email']}',
																					 {$item['profile_id']});<br/>";
			}
			$writer = new Xlsx($spreadsheet);
			$fileName = "relatorio_mgm.xlsx";
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
															 REPLACE(Products.sale_price, '.', ',' ) AS PRECO_DE_VENDA, REPLACE(Products.current_price_pay_only, '.', ',' ) AS PAGUE_APENAS,
															 REPLACE(Products.drogasil, '.', ',' ) AS DROGASIL,
															 REPLACE(Products.ultrafarma, '.', ',' ) AS ULTRAFARMA,
															 REPLACE(Products.belezanaweb, '.', ',' ) AS BELEZA_NA_WEB,
															 REPLACE(Products.drogaraia, '.', ',' ) AS DROGARAIA,
															 REPLACE(Products.drogariasp, '.', ',' ) AS DROGARIASP,
															 REPLACE(Products.onofre, '.', ',' ) AS ONOFRE,
															 REPLACE(Products.paguemenos, '.', ',' ) AS PAGUE_MENOS,
															 REPLACE(Products.panvel, '.', ',' ) AS PANVEL,
															  REPLACE(Products.current_less_price_around, '.',',') as MENOR_PRECO_POR_AI,
															  REPLACE(Products.current_margin_value, '.',',') as MARGEM_VALOR, REPLACE(Products.current_cashback, '.',',') as CASHBACK,
															 REPLACE(current_gross_margin, '.', ',' ) AS MARGEM_APOS_CASHBACK, REPLACE(Products.current_gross_margin_percent, '.',',') as MARGEM_BRUTA_PORCENTO,
															  REPLACE(Products.diff_current_pay_only_lowest, '.',',') as DIFERENCA_PARA_O_MENOR_CONCORRENTE,
															 Products.curve as CURVA, Products.pbm as PBM, descontinuado.situation as SITUACAO_DESCONTINUADO,
															 marca.marca as MARCA, marca.fabricante as FABRICANTE, Products.otc as OTC, Products.descontinuado as DESCONTINUADO,
															  Products.controlled_substance as CONTROLADO, Products.active as ATIVO, Products.acao as ACAO,
																Products.sub_category as SUBCATEGORIA
																from vendas inner join Products on Products.sku=vendas.sku
															  INNER JOIN Situation on Products.situation_code_fk = Situation.code INNER JOIN Status on Products.status_code_fk = Status.code
															 LEFT JOIN principio_ativo ON principio_ativo.sku = Products.sku LEFT JOIN descontinuado on Products.sku = descontinuado.sku
															  LEFT JOIN marca on Products.sku = marca.sku WHERE Products.diff_current_pay_only_lowest < 0 and Products.department = '".str_replace("_", " ", $department)."' group by sku")->getResult();
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
							$comp_type = "and Products.current_gross_margin_percent < 0 and Products.active = 1 and Products.descontinuado != 1 and Products.qty_stock_rms > 0";
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
															REPLACE(Products.sale_price, '.', ',' ) AS PRECO_DE_VENDA, REPLACE(Products.current_price_pay_only, '.', ',' ) AS PAGUE_APENAS,
															 REPLACE(Products.drogasil, '.', ',' ) AS DROGASIL,
                               REPLACE(Products.ultrafarma, '.', ',' ) AS ULTRAFARMA,
                               REPLACE(Products.belezanaweb, '.', ',' ) AS BELEZA_NA_WEB,
                               REPLACE(Products.drogaraia, '.', ',' ) AS DROGARAIA,
                               REPLACE(Products.drogariasp, '.', ',' ) AS DROGARIASP,
                               REPLACE(Products.onofre, '.', ',' ) AS ONOFRE,
                               REPLACE(Products.paguemenos, '.', ',' ) AS PAGUE_MENOS,
                               REPLACE(Products.panvel, '.', ',' ) AS PANVEL,
															 REPLACE(Products.current_less_price_around, '.',',') as MENOR_PRECO_POR_AI,
															 REPLACE(Products.current_margin_value, '.',',') as MARGEM_VALOR, REPLACE(Products.current_cashback, '.',',') as CASHBACK,
															REPLACE(current_gross_margin, '.', ',' ) AS MARGEM_APOS_CASHBACK,
															REPLACE(Products.current_gross_margin_percent, '.',',') as MARGEM_BRUTA_PORCENTO,
															 REPLACE(Products.diff_current_pay_only_lowest, '.',',') as DIFERENCA_PARA_O_MENOR_CONCORRENTE,
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
			else if ($group === "Perdendo") $comp = " and Products.diff_current_pay_only_lowest < 0";
			else if ($group === "0 Cashback") $comp = " and Products.acao = '$group'";
			else if ($group === "5%   5% Progress") $comp = " and Products.acao = '5% + 5% Progress'";
			else if ($group === "Vencimento") $comp = " and Products.acao = '$group'";
			else if ($group === "5% progressivo") $comp = " and Products.acao = '$group'";
			else if ($group === "Aumento TKM") $comp = " and Products.acao = '$group'";
			else if ($group === "Prego") $comp = " and Products.acao = '$group'";
			else if ($group === "3% Progressivo") $comp = " and Products.acao = '$group'";
			else if ($group === "3%   5% Progressivo") $comp = " and Products.acao = '3% + 5% Progressivo'";
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
}
