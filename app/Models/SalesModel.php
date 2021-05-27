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

    public function getDataSalesTable($sale_date, $initial_limit, $final_limit, $sort_column, $sort_order, $search) {
        $query = $this->db->table('vendas')
                          ->select('sku,
                                    department,
                                    category,
                                    qtd,
                                    faturamento')
                          ->where('data', $sale_date)
                          ->orderBy("$sort_column $sort_order");
        if ($search != '') $query->like('sku', $search);
        $query->limit($final_limit, $initial_limit);
        $results = $query->get()->getResult();

        // Faz o cálculo do VMD dos últimos 7 dias, 30 dias e 90 dias
        foreach($results as $row) {
            // Últimos 7 dias
            $row->weekly = $this->db->table('vendas')
                                    ->select('sum(faturamento)/7 as weekly')
                                    ->where('data >=', date('Y-m-d', strtotime($sale_date."-7 days")))
                                    ->where('data <=', $sale_date)
                                    ->where('sku', $row->sku)
                                    ->get()->getResult()[0]->weekly;

            // Últimos 30 dias
            $row->last_month = $this->db->table('vendas')
                                        ->select('sum(faturamento)/30 as last_month')
                                        ->where('data >=', date('Y-m-d', strtotime($sale_date."-30 days")))
                                        ->where('data <=', $sale_date)
                                        ->where('sku', $row->sku)
                                        ->get()->getResult()[0]->last_month;

            // Últimos 90 dias
            $row->last_3_months = $this->db->table('vendas')
                                           ->select('sum(faturamento)/90 as last_3_months')
                                           ->where('data >=', date('Y-m-d', strtotime($sale_date."-90 days")))
                                           ->where('data <=', $sale_date)
                                           ->where('sku', $row->sku)
                                           ->get()->getResult()[0]->last_3_months;
        }

        $query_qtd = $this->db->table('vendas')
                              ->select('count(1) as qtd')
                              ->where('data', $sale_date);
        if ($search != '') $query_qtd->like('sku', $search);
        $qtd = $query_qtd->get()->getResult()[0]->qtd;
        return json_encode(array('products' => $results,
                                 'qtd' => $qtd));
    }
}
