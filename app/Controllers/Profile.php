<?php

namespace App\Controllers;

use App\Models\QualiuserModel;

class Profile extends BaseController
{
	public function index() {

	}

	/*********************************************************************** PÁGINAS HTML ***********************************************************************/
	// Função que chama a página para trocar a senha do usuário logado
	public function change_password($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('change_password', $data);
	}

	/*********************************************************************** ROTAS ***********************************************************************/
	// Função que troca a senha do usuário e loga ele novamente
	public function change() {
			if($this->request->getMethod() == 'post') {
					if(!$this->validate([
									'old_password'      	 => 'required|min_length[6]|max_length[255]',
									'new_password'      	 => 'required|min_length[6]|max_length[255]',
									'new_password_confirm' => 'required|matches[new_password]',
							],
							[   // Errors
									'old_password' => [
											'required' => 'Preencha sua senha antiga',
											'min_length' => 'A senha deve conter pelo menos 6 caracteres.',
											'max_length' => 'A senha não pode ter mais de 50 caracteres.'
									],
									'new_password' => [
											'required' => 'Preencha a nova senha',
											'min_length' => 'A senha nova deve conter pelo menos 6 caracteres.',
											'max_length' => 'A senha nova não pode ter mais de 50 caracteres.'
									],
									'new_password_confirm' => [
											'required' => 'Preencha a confirmação da sua senha nova',
											'matches' => 'A senha nova não é igual a confirmação da senha nova.'
									],
							])) {
							$data['validation'] = $this->validator;
							$this->change_password($data);
					}
					else {
							$model = new QualiuserModel();
							$data_user = $model->where('username', session('username'))->first();
							if($data_user) {
									if(password_verify($this->request->getVar('old_password'), $data_user['password'])) {
											$model->set('password', password_hash($this->request->getVar('new_password'), PASSWORD_DEFAULT));
											$model->where('username', $data_user['username']);
											$model->update();
											session_destroy();
											$session = session();
											$session->set([ 'username' => $data_user['username'], 'permission_group' => $data_user['permission_group']]);
											$this->change_password();
									}
							}
					}
			}
	}
}
