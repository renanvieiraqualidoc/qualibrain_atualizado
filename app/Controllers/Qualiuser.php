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
						// Valida os campos de usuário 
						if(!$this->validate([
								'username'      => 'required|min_length[3]|max_length[50]',
								'password'      => 'required|min_length[6]|max_length[255]',
								'confpassword'  => 'required|matches[password]',
								'email'         => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]'
						],
						[   // Errors
								'username' => [
										'required' => 'Preencha o nome do usuário',
										'min_length' => 'O nome do usuário deve conter pelo menos 3 caracteres.',
										'max_length' => 'O nome do usuário não pode ter mais de 50 caracteres.'
								],
								'password' => [
										'required' => 'Preencha a senha',
										'min_length' => 'A senha deve conter pelo menos 6 caracteres.',
										'max_length' => 'A senha não pode ter mais de 50 caracteres.'
								],
								'confpassword' => [
										'required' => 'Preencha a confirmação da senha',
										'matches' => 'A senha digitada não é igual a confirmação.'
								],
								'email' => [
										'required' => 'Preencha seu e-mail',
										'min_length' => 'O e-mail deve conter pelo menos 6 caracteres.',
										'max_length' => 'O e-mail não pode ter mais de 50 caracteres.',
										'valid_email' => 'Digite um endereço de e-mail válido.',
										'is_unique' => 'E-mail existente.'
								]
						])) {
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
