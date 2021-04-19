<?php

namespace App\Controllers;

class Pricing extends BaseController
{

	public function index()
	{
		echo view('scripts');
		echo view('links');
		echo view('metas');
		echo view('pricing');
	}



}
