<?php namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model{
    public function getSalesByDate($initial_date, $final_date) {
        return $this->db->table('vendas')
                        ->select('DATE_FORMAT(data, "%Y-%m") as data, faturamento, round((faturamento - price_cost * qtd), 2) as margin')
                        ->where('data >=', $initial_date)
                        ->where('data <=', $final_date)
                        ->orderBy('data asc')
                        ->get()->getResult();
    }
}
