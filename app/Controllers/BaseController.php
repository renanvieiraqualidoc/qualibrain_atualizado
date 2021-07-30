<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\MenuModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['url', 'html', 'form', 'array', 'number', 'download', 'filesystem'];
	protected $session;

	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		$this->session = \Config\Services::session();
		$this->session->start();
		date_default_timezone_set('America/Sao_Paulo');

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.: $this->session = \Config\Services::session();
	}

	// Função para printar array ou objeto com campos amigáveis
	public function debug($array) {
			echo "<pre>";
			print_r($array);
			echo "</pre>";
			die();
	}

	// Função que popula as opções de menu
	public function dynamicMenu() {
			$model = new MenuModel();
			$data = [];
			$show = 0;
			foreach($model->getCategories() as $category) {
					$subs = $model->getSubcategories($category->id);
					if (count($subs) > 0) {
							$show = array_filter($subs, function($sub) {
									return $sub['hasPermission'] == 1;
							});
					}
					array_push($data, [
							'id' => $category->id,
							'functionality_name' => $category->functionality_name,
							'parent' => $category->parent,
							'icon' => $category->icon,
							'subcategories' => $subs,
							'show' => $show
					]);
			}
			return $data;
	}

	// Função que faz um inner join entre 2 arrays dado o campo de chave estrangeira
	function inner_join($first_array, $second_array, $id) {
			$inner_join = array();
			foreach ($first_array as $first) {
					$first = (array) $first;
					foreach ($second_array as $second) {
							$second = (array) $second;
							if ($first[$id] == $second[$id]) {
									$inner_join[] = array_merge($first, $second);
							}
					}
			}
			return $inner_join;
	}

	// Função que gera um access token para consumo do OCC
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
}
