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
}
