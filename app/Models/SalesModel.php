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

    public function getDataSalesTable($sale_date, $department, $initial_limit, $final_limit, $sort_column, $sort_order, $search) {
        $query = $this->db->table('vendas')
                          ->select('vendas.sku,
                                    vendas.department,
                                    vendas.category,
                                    vendas.qtd,
                                    Products.title,
                                    vendas.faturamento')
                          ->join('Products', 'vendas.sku = Products.sku')
                          ->where('vendas.data', $sale_date)
                          ->orderBy("vendas.$sort_column $sort_order");
        if ($search != '') $query->like('vendas.sku', $search);
        if ($department != 'geral') $query->where('vendas.department', $department);
        $query->limit($final_limit, $initial_limit);
        $results = $query->get()->getResult();

        // Faz o cálculo do VMD dos últimos 7 dias, 30 dias e 90 dias
        foreach($results as $row) {
            // Últimos 7 dias
            $row->weekly = $this->db->table('vendas')
                                    ->select('sum(qtd)/7 as weekly')
                                    ->where('data >=', date('Y-m-d', strtotime($sale_date."-7 days")))
                                    ->where('data <=', $sale_date)
                                    ->where('sku', $row->sku)
                                    ->get()->getResult()[0]->weekly;

            // Últimos 30 dias
            $row->last_month = $this->db->table('vendas')
                                        ->select('sum(qtd)/30 as last_month')
                                        ->where('data >=', date('Y-m-d', strtotime($sale_date."-30 days")))
                                        ->where('data <=', $sale_date)
                                        ->where('sku', $row->sku)
                                        ->get()->getResult()[0]->last_month;

            // Últimos 90 dias
            $row->last_3_months = $this->db->table('vendas')
                                           ->select('sum(qtd)/90 as last_3_months')
                                           ->where('data >=', date('Y-m-d', strtotime($sale_date."-90 days")))
                                           ->where('data <=', $sale_date)
                                           ->where('sku', $row->sku)
                                           ->get()->getResult()[0]->last_3_months;
        }

        $query_qtd = $this->db->table('vendas')
                              ->select('count(1) as qtd')
                              ->where('data', $sale_date);
        if ($search != '') $query_qtd->like('sku', $search);
        if ($department != 'geral') $query_qtd->where('department', $department);
        $qtd = $query_qtd->get()->getResult()[0]->qtd;
        return json_encode(array('products' => $results,
                                 'qtd' => $qtd));
    }

    public function totalFat() {
        return $this->db->table('vendas')->select('sum(faturamento) as total')->get()->getResult()[0]->total;
    }

    public function totalFatTermolabil() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('termolabil', 1)
                        ->get()->getResult()[0]->total;
    }

    public function totalFatOTC() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('otc', 1)
                        ->get()->getResult()[0]->total;
    }

    public function totalFatControlados() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('controlled_substance', 1)
                        ->get()->getResult()[0]->total;
    }

    public function totalFatCashback() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('cashback >', 0)
                        ->get()->getResult()[0]->total;
    }

    public function totalFatAcao() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('acao !=', '')
                        ->where('acao !=', null)
                        ->get()->getResult()[0]->total;
    }

    public function totalFatPBM() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('pbm', '1')
                        ->get()->getResult()[0]->total;
    }

    public function totalFatHome() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('home', '1')
                        ->get()->getResult()[0]->total;
    }
}
