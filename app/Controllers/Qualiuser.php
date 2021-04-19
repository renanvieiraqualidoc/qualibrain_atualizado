<?php

namespace App\Controllers;

use App\Models\QualiuserModel;
use App\Models\PermissionsModel;

class Qualiuser extends BaseController
{
		/*********************************************************************** PÁGINAS HTML ***********************************************************************/
		// Função que chama a página principal de login
		public function index($data = []) {
				$model = new PermissionsModel();
				$permissions = $model->get()->getResult();
				$data['permissions'] = $permissions;
				echo view('register', $data);
		}

		/*********************************************************************** ROTAS ***********************************************************************/
		// Função que insere um novo usuário
		public function register() {
				if($this->request->getMethod() == 'post') {
						// Define as regras de validações dos campos de usuário
						$rules = [
								'username'      => 'required|min_length[3]|max_length[50]',
								'password'      => 'required|min_length[6]|max_length[255]',
								'confpassword'  => 'required|matches[password]',
								'email'         => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]'
						];

						if(!$this->validate($rules)) {
								$data['validation'] = $this->validator;
								$this->index($data);
						}
						else {
							$model = new QualiuserModel();
							$data = [
									'username'     		 => $this->request->getVar('username'),
									'password' 				 => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
									'email'    				 => $this->request->getVar('email'),
									'permission_group' => $this->request->getVar('permission_group')
							];
							$model->insert($data);
							return redirect()->to('/');
						}
				}
		}
}
