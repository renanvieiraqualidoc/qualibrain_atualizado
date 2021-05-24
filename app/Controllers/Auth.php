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

		// Função que chama a página de acesso negado
		public function denied() {
				echo view('denied');
		}

		/*********************************************************************** ROTAS ***********************************************************************/
		// Função que efetua o login do usuário
		public function login() {
				if($this->request->getMethod() == 'post') {
						$model = new QualiuserModel();
						$data_user = $model->where('username', $this->request->getVar('username'))->first();
						if($data_user) {
								if(password_verify($this->request->getVar('password'), $data_user['password'])) {
										$this->session->set([ 'username' => $data_user['username'], 'permission_group' => $data_user['permission_group'] ]);
										// TODO: Verificar para cada grupo qual a página default para carregar ao logar
										return redirect()->to('/pricing');
								}
						}
				}
				return redirect()->to('/');
		}

		// Função que desloga o usuário
		public function logout() {
				session_destroy();
				return redirect()->to(base_url('/'));
		}
}
