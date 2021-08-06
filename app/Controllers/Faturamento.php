<?php

namespace App\Controllers;
use App\Models\SalesModel;

class Faturamento extends BaseController
{
		public function index($data = []) {
				$data['categories'] = $this->dynamicMenu();
				$fat_data = array();
				for($i=0; $i<3; $i++) {
						array_push($fat_data, $this->getFatByMonth($i));
						$this->debug($fat_data);
				}
				array_push($fat_data, $this->getFatByMonth(0, true));
				$data['months'] = $fat_data;
				echo view('faturamento', $data);
		}

		public function getFatByMonth($index, $projection = false) {
				$sales_model = new SalesModel();
				$data = array();
				$subtract = 2-$index;
				$fat = $sales_model->getFatByMonth(date('m', strtotime("-$subtract months")), date('Y', strtotime("-$subtract months")));
				$price_cost = $fat->price_cost;
				$gross_billing = $fat->faturamento_bruto;
				$obj = $this->getQtyOrdersAndMonth($index);
				$data = array('month' => $obj['month'],
											'gross_billing' => $gross_billing,
											'qtd_orders' => $obj['qtd'],
											'tkm' => $obj['qtd'] != 0 ? $gross_billing/$obj['qtd'] : 0,
											'comparative_previous_month' => 0,
											'margin' => $gross_billing != 0 ? ($gross_billing-$price_cost)/$gross_billing*100 : 0);
				return json_encode($data);
		}

		public function getQtyOrdersAndMonth($index) {
				$access_token = $this->getAccessToken();
				$curl = curl_init();
				$substract = 2 - $index;
				$initial_date = date("Y-m-01", strtotime("-$substract months"));
				$final_date = date("Y-m-t", strtotime($initial_date));
				echo date("Y-m-t", strtotime("2018-01-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-02-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-03-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-04-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-05-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-06-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-07-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-08-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-09-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-10-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-11-23"))."<br/>";
				echo date("Y-m-t", strtotime("2018-12-23"))."<br/>";
				die();
				// echo "$initial_date $final_date<br/>";
				$client = \Config\Services::curlrequest();
				$response = $client->request('GET', "http://ultraclinica.totvscloud.com.br:2000/RMS/RMSSERVICES/ReportWebAPI/api/v1/SaleHistory/GetByDay?filial=1007&dataVendaInicio=".$initial_date."&dataVendaFim=".$final_date, [ 'headers' => ['Content-Type: application/vnd.api+json', 'Accept: application/vnd.api+json'] ])->getBody();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://p7483342c1prd-admin.occa.ocs.oraclecloud.com/ccadmin/v1/orders?limit=0&fields=id&offset=0&queryFormat=SCIM&q=(state%20eq%20%22PROCESSING%22%20or%20state%20eq%20%22NO_PENDING_ACTION%22)%20and%20submittedDate%20ge%20%22'.$initial_date.'T00:00:00.000Z%22%20and%20submittedDate%20le%20%22'.$final_date.'T23:59:59.000Z%22%20and%20siteId%20eq%20%22siteUS%22%20and%20x_nota_fiscal%20pr',
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
				$months = array(1 => "Jan", 2 => "Fev", 3 => "Mar", 4 => "Abr",
												5 => "Mai", 6 => "Jun", 7 => "Jul", 8 => "Ago",
												9 => "Set", 10 => "Out", 11 => "Nov", 12 => "Dez");
				return array('qtd' => $response->totalResults, 'month' => $months[intval(date('m', strtotime($initial_date)))]);
		}
}
