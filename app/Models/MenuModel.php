<?php namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model{
    public function getCategories() {
        return $this->db->table('functionalities')->select('*')->where('parent is null')->get()->getResult();
    }

    public function hasPermission($functionality) {
  			return ($this->db->table('qualibrain_permissions')
                         ->select('count(*) as qtd')
                         ->where('group_id', session('permission_group'))
                         ->where('functionality_id', $functionality)
                         ->get()->getResult()[0]->qtd > 0) ? true : false;
  	}

    public function getSubcategories($id) {
        $data_subs = [];
        foreach($this->db->table('functionalities')->select('*')->where('parent', $id)->get()->getResult() as $subs) {
            array_push($data_subs, [
                'id' => $subs->id,
                'functionality_name' => $subs->functionality_name,
                'parent' => $subs->parent,
                'icon' => $subs->icon,
                'page' => $subs->page,
                'hasPermission' => $this->hasPermission($subs->id),
            ]);
        }
        return $data_subs;
    }
}
