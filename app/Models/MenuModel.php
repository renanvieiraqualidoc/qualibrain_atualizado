<?php namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model{
    public function getCategories() {
        return $this->db->table('functionalities')->select('*')->where('parent is null')->get()->getResult();
    }

    public function getSubcategories($id) {
        return $this->db->table('functionalities')->select('*')->where('parent', $id)->get()->getResult();
    }
}
