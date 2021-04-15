<?php

namespace App\Controllers;

class Login extends BaseController
{

	public function index()
	{
		echo view('scripts');
		echo view('links');
		echo view('metas');
		echo view('login');
	}

	public function authenticate() {
		echo "teste";
	}

	public function forgot_password()
	{
		echo view('forgot_password');
	}
}
