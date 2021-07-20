<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class Cronjob extends BaseController
{
		// Cronjob que cruza as informações de vendas da API RMS com as informações de produtos do banco de dados e salvam na tabela de vendas
		public function get_sales_() {
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

		// Cronjob para atualizar os MGM's no banco de dados
		public function mgm() {
				ini_set('memory_limit', '-1');
				date_default_timezone_set('America/Sao_Paulo');
				$db = \Config\Database::connect();
				$ar_coupons = ['QUALIDOC10', 'QUALIDOC30'];
				$limit = 250;
				$access_token = $this->getAccessToken();
				$curl = curl_init();
				// $initial_date = date("Y-m-d\TH:i:s.000\Z", strtotime('now -1 hour'));
				$initial_date = "2021-06-20T00:00:00.000Z";
				// $initial_date = date("Y-m-d\TH:i:s.000\Z", strtotime('now -10 days'));
				$final_date = date("Y-m-d\TH:i:s.000\Z", strtotime('now'));
				$sql = "";
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/orders?limit=250&fields=id,state,profile,commerceItems.priceInfo.orderDiscountInfos,profileId,submittedDate,&offset=0&queryFormat=SCIM&q=(state%20eq%20%22PROCESSING%22%20or%20state%20eq%20%22NO_PENDING_ACTION%22)%20and%20submittedDate%20ge%20%22'.$initial_date.'%22%20and%20submittedDate%20le%20%22'.$final_date.'%22%20and%20siteId%20eq%20%22siteUS%22%20and%20x_nota_fiscal%20pr',
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
				$total_pages = ceil($response->totalResults/$limit);

				for($i = 0; $i < ($total_pages+1); $i++) {
						$access_token = $this->getAccessToken();
						$curl = curl_init();
						$offset = ($i == 0) ? 0 : $limit*$i-1;
						curl_setopt_array($curl, array(
							CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/orders?limit='.$limit.'&fields=id,state,profile,commerceItems.priceInfo.orderDiscountInfos,profileId,submittedDate,&offset='.$offset.'&queryFormat=SCIM&q=(state%20eq%20%22PROCESSING%22%20or%20state%20eq%20%22NO_PENDING_ACTION%22)%20and%20submittedDate%20ge%20%22'.$initial_date.'%22%20and%20submittedDate%20le%20%22'.$final_date.'%22%20and%20siteId%20eq%20%22siteUS%22%20and%20x_nota_fiscal%20pr',
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
																				$order = $this->getOrder($item->id);
																				$profile = $this->getProfile($item->profileId);
																				$indicator_profile = $this->getProfile($profile->x_mgm_indicator);
																				$indicator_name = (isset($indicator_profile->firstName) ? $indicator_profile->firstName : '')." ".(isset($indicator_profile->lastName) ? $indicator_profile->lastName : '');
																				$indicator_email = isset($indicator_profile->email) ? $indicator_profile->email : '';
																				$sql .= "INSERT IGNORE INTO mgm VALUES ('{$item->id}', '".$item->profile->firstName." ".$item->profile->lastName."', {$order->priceInfo->amount}, '".date('Y-m-d G:i:s', strtotime($item->submittedDate))."', '{$indicator_name}', '{$item->state}', '{$item->profile->email}', '{$indicator_email}', {$item->profileId});\n";
																		}
																}
														}
												}
										}
								}
						}
				}
				$file = WRITEPATH."mgm.txt";
				write_file($file, $sql);
				$model = new ProductsModel();
				$host = ($model->db->hostname == "localhost") ? $model->db->hostname : substr($model->db->hostname, 0, strpos($model->db->hostname, ':'));
				if(trim(shell_exec("mysql -h $host -u".$model->db->username." -p'".$model->db->password."' ".$model->db->database." < $file 2>&1"))
										 == "mysql: [Warning] Using a password on the command line interface can be insecure.") {
						$msg = 'MGM atualizado com sucesso!';
						$success = true;
				}
				else {
						$msg = 'Não foi possível atualizar o dump!';
						$success = false;
				}
				unlink($file);
				echo json_encode(array('success' => $success, 'msg' => $msg));
		}

		// Cronjob para atualizar os PBM's no banco de dados
		public function pbm() {
				ini_set('memory_limit', '-1');
				date_default_timezone_set('America/Sao_Paulo');
				$db = \Config\Database::connect();
				$pbm = [];
				$limit = 250;
				$access_token = $this->getAccessToken();
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/orders?limit=250&fields=id,commerceItems,submittedDate&offset=0&queryFormat=SCIM&q=(state%20eq%20%22PROCESSING%22%20or%20state%20eq%20%22NO_PENDING_ACTION%22)%20and%20submittedDate%20ge%20%222021-02-01T00:00:00.000Z%22%20and%20siteId%20eq%20%22siteUS%22%20and%20x_nota_fiscal%20pr%20and%20(x_pbm_confirmation%20eq%202%20or%20x_pbm_confirmation%20eq%201)',
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
				$total_pages = ceil($response->totalResults/$limit) == 1 ? 0 : ceil($response->totalResults/$limit);
				$sql = "";

				for($i = 0; $i < ($total_pages+1); $i++) {
						$access_token = $this->getAccessToken();
						$curl = curl_init();
						$offset = ($i == 0) ? 0 : $limit*$i-1;
						curl_setopt_array($curl, array(
							CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/orders?limit=250&fields=id,commerceItems,submittedDate&offset=0&queryFormat=SCIM&q=(state%20eq%20%22PROCESSING%22%20or%20state%20eq%20%22NO_PENDING_ACTION%22)%20and%20submittedDate%20ge%20%222021-02-01T00:00:00.000Z%22%20and%20siteId%20eq%20%22siteUS%22%20and%20x_nota_fiscal%20pr%20and%20(x_pbm_confirmation%20eq%202%20or%20x_pbm_confirmation%20eq%201)',
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
										if($price->x_pbm != "") {
												$id = $db->query("SELECT id FROM pbm_van WHERE programa = '{$price->x_pbm}'")->getResult()[0]->id ?? 0;
												$sql .= "INSERT IGNORE INTO relatorio_pbm VALUES ('{$item->id}', '{$price->productId}', '{$price->productDisplayName}', {$price->priceInfo->amount}, {$price->quantity}, '".date('Y-m-d G:i:s', strtotime($item->submittedDate))."', $id);\n";
										}
								}
						}
				}
				$file = WRITEPATH."pbm.txt";
				write_file($file, $sql);
				$model = new ProductsModel();
				$host = ($model->db->hostname == "localhost") ? $model->db->hostname : substr($model->db->hostname, 0, strpos($model->db->hostname, ':'));
				if(trim(shell_exec("mysql -h $host -u".$model->db->username." -p'".$model->db->password."' ".$model->db->database." < $file 2>&1"))
										 == "mysql: [Warning] Using a password on the command line interface can be insecure.") {
						$msg = 'PBM atualizado com sucesso!';
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
