<?php namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model{
    public function getSalesByDate($initial_date, $final_date, $department) {
        $query = $this->db->table('vendas')
                          ->select('DATE_FORMAT(data, "%Y-%m") as data, faturamento, round((faturamento - price_cost), 2) as margin')
                          ->where('data >=', $initial_date)
                          ->where('data <=', $final_date)
                          ->orderBy('data asc');
        if ($department != 'Geral') $query->where('department', $department);
        return $query->get()->getResult();
    }
}
