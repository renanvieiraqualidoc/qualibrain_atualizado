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
		public function authenticate() {
				$model = new QualiuserModel();
				$session = session();
				$data_user = $model->where('username', $this->request->getVar('username'))->first();
				if($data_user) {
						$hashed_password = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);
						if(password_verify($data_user['password'], $hashed_password)) {
								// echo "<pre>";
								// print_r($data_user);
								// echo "</pre>";
								// die($data_user['password']);
								echo "loguei";
						}
						else {
							echo "não loguei";
						}
				}
				else {
						$session->setFlashdata('msg', 'Usuário não encontrado!');
						// return redirect()->to('index');
				}
		}
}
