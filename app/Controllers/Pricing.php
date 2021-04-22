<?php

namespace App\Controllers;
use App\Models\PermissionsModel;

class Pricing extends BaseController
{

	public function index($data = []) {
			$model = new PermissionsModel();
			if(!$model->checkPermissionPage('pricing')) echo view('404');
			else {
					$data['categories'] = $this->dynamicMenu();
					echo view('pricing', $data);
			}
	}
}
