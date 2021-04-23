<?php

namespace App\Controllers;

class Pricing extends BaseController
{

	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('pricing', $data);
	}
}
