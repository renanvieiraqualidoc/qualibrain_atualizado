<?php

namespace App\Controllers;

use App\Models\QualiuserModel;

class Auth extends BaseController
{
		/*********************************************************************** PÁGINAS HTML ***********************************************************************/
		// Função que chama a página principal de login
		public function index() {
				echo view('login');
		}

		// Função que chama a página de recuperação de senha
		public function forgot_password() {
				echo view('forgot_password');
		}

		/*********************************************************************** ROTAS ***********************************************************************/
		// Função que efetua o login do usuário
		public function login() {
				if($this->request->getMethod() == 'post') {
						$model = new QualiuserModel();
						$session = session();
						$data_user = $model->where('username', $this->request->getVar('username'))->first();
						if($data_user) {
								if(password_verify($this->request->getVar('password'), $data_user['password'])) {
										$session->set([ 'username' => $data_user['username'], 'permission_group' => $data_user['permission_group'] ]);
										return redirect()->to('/pricing/index');
								}
						}
				}
				return redirect()->to('/');
		}
}
