<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\PermissionsModel;

class PermissionsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Função que verifica se usuário logado possui acesso a uma página específica
        $model = new PermissionsModel();
        if(!$model->checkPermissionPage($request->uri->getPath()) && strpos($request->uri->getPath(), "cronjob") === false) return redirect()->to(base_url()."/auth/denied");
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
