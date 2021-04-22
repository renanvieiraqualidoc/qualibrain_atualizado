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
	protected $helpers = ['url', 'html', 'form', 'array'];

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
		$this->checkSession();

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

	// Função que verifica constantemente se o usuário está logado
	public function checkSession() {
			if(!session('username')) {
				echo "1";
			}
			else {
				echo "2";
			}
			// TODO: Corrigir direcionamento para usuários não logados
			if(!session('username')) redirect()->to('/');
	}

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
}
