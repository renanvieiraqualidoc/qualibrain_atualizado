<?php

namespace App\Controllers;

class Home extends BaseController
{
	// public function index()
	// {
	// 	return view('welcome_message');
	// }

	public function index()
	{
		return view('login');
	}

	public function page_not_found()
	{
		return view('404');
	}
}
