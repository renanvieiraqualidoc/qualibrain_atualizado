<?php namespace App\Models;

use CodeIgniter\Model;

class PermissionsModel extends Model{
    protected $table = 'permission_groups';
    protected $primaryKey = 'id';
    protected $allowedFields = ['group_name'];

    // Função que checa a permissão do usuário para uma página específica
    public function checkPermissionPage($page) {
        return ($this->db->table('qualibrain_permissions')
                         ->select('count(*) as qtd')
                         ->join('functionalities', 'functionalities.id = qualibrain_permissions.functionality_id')
                         ->where('group_id', session('permission_group'))
                         ->where('page', $page)
                         ->get()->getResult()[0]->qtd) > 0 ? true : false;
    }
}
