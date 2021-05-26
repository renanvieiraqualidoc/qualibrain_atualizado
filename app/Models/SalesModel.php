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

    public function getDataSalesTable($initial_date, $final_date, $initial_limit, $final_limit, $sort_column, $sort_order, $search) {
        $query = $this->db->table('vendas')
                          ->select('sku,
                                    department,
                                    category,
                                    qtd,
                                    faturamento as weekly,
                                    faturamento as last_month,
                                    faturamento as last_3_months,
                                    faturamento')
                          ->where('data >=', $initial_date)
                          ->where('data <=', $final_date)
                          ->orderBy("$sort_column $sort_order");
        if ($search != '') $query->like('sku', $search);
        $query->limit($final_limit, $initial_limit);

        $query_qtd = $this->db->table('vendas')
                              ->select('count(1) as qtd')
                              ->where('data >=', $initial_date)
                              ->where('data <=', $final_date);
        if ($search != '') $query_qtd->like('sku', $search);
        $qtd = $query_qtd->get()->getResult()[0]->qtd;
        return json_encode(array('products' => $query->get()->getResult(),
                                 'qtd' => $qtd));
    }
}
