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

    public function getDataSalesTable($sale_date, $department, $group, $initial_limit, $final_limit, $sort_column, $sort_order, $search) {
        $query = $this->db->table('vendas')
                          ->select('vendas.sku,
                                    vendas.department,
                                    vendas.category,
                                    sum(vendas.qtd) as qtd,
                                    Products.title,
                                    sum(vendas.faturamento) as faturamento')
                          ->join('Products', 'vendas.sku = Products.sku')
                          ->where('Products.active', 1)
                          ->where('Products.descontinuado !=', 1)
                          ->orderBy("vendas.$sort_column $sort_order")
                          ->groupBy("Products.sku");
        if ($sale_date != "") $query->where('vendas.data', $sale_date); else $query->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")));
        if ($group === "Termolábil") $query->where('Products.termolabil', 1);
        else if ($group === "OTC") $query->where('Products.otc', 1);
        else if ($group === "Controlados") $query->where('Products.controlled_substance', 1);
        else if ($group === "PBM") $query->where('Products.pbm', 1);
        else if ($group === "Cashback") $query->where('Products.cashback >', 0);
        else if ($group === "Home") $query->where('Products.home', 1);
        else if ($group === "Ação") $query->where('Products.acao !=', '')->where('Products.acao !=', null);
        else if ($group === "Autocuidado") $query->where('Products.category', 'AUTOCUIDADO');
        else if ($group === "Similar") $query->where('Products.category', 'SIMILAR');
        else if ($group === "Marca") $query->where('Products.category', 'MARCA');
        else if ($group === "Genérico") $query->where('Products.category', 'GENERICO');
        else if ($group === "Higiene e Beleza") $query->where('Products.category', 'HIGIENE')->orWhere('Products.category', 'HIGIENE E BELEZA');
        else if ($group === "Mamãe e Bebê") $query->where('Products.category', 'MAMÃE E BEBÊ');
        else if ($group === "Dermocosmético") $query->where('Products.category', 'DERMOCOSMETICO');
        else if ($group === "Beleza") $query->where('Products.category', 'BELEZA');
        else if ($group !== "") $query->where('Products.marca', strtoupper($group));
        if ($search != '') $query->like('vendas.sku', $search);
        if ($department != 'geral') $query->where('vendas.department', $department);
        $query->limit($final_limit, $initial_limit);
        $results = $query->get()->getResult();

        // Faz o cálculo do VMD dos últimos 7 dias, 30 dias e 90 dias
        foreach($results as $row) {
            // Últimos 7 dias
            $query_weekly = $this->db->table('vendas')->select('sum(qtd)/7 as weekly');
            if ($sale_date != "") {
                $query_weekly->where('data >=', date('Y-m-d', strtotime($sale_date."-7 days")));
                $query_weekly->where('data <=', $sale_date);
            }
            else {
                $query_weekly->where('data >=', date('Y-m-d', strtotime("-7 days")));
                $query_weekly->where('data <=', date('Y-m-d'));
            }
            $query_weekly->where('sku', $row->sku);
            $row->weekly = $query_weekly->get()->getResult()[0]->weekly;

            // Últimos 30 dias
            $query_last_month = $this->db->table('vendas')->select('sum(qtd)/30 as last_month');
            if ($sale_date != "") {
                $query_last_month->where('data >=', date('Y-m-d', strtotime($sale_date."-30 days")));
                $query_last_month->where('data <=', $sale_date);
            }
            else {
                $query_last_month->where('data >=', date('Y-m-d', strtotime("-30 days")));
                $query_last_month->where('data <=', date('Y-m-d'));
            }
            $query_last_month->where('sku', $row->sku);
            $row->last_month = $query_last_month->get()->getResult()[0]->last_month;

            // Últimos 90 dias
            $query_last_3_months = $this->db->table('vendas')->select('sum(qtd)/90 as last_3_months');
            if ($sale_date != "") {
                $query_last_3_months->where('data >=', date('Y-m-d', strtotime($sale_date."-90 days")));
                $query_last_3_months->where('data <=', $sale_date);
            }
            else {
                $query_last_3_months->where('data >=', date('Y-m-d', strtotime("-90 days")));
                $query_last_3_months->where('data <=', date('Y-m-d'));
            }
            $query_last_3_months->where('sku', $row->sku);
            $row->last_3_months = $query_last_3_months->get()->getResult()[0]->last_3_months;
        }

        $query_qtd = $this->db->table('vendas')
                              ->select('count(1) as qtd')
                              ->join('Products', 'vendas.sku = Products.sku')
                              ->where('Products.active', 1)
                              ->where('Products.descontinuado !=', 1)
                              ->groupBy("Products.sku");
        if ($sale_date != "") $query_qtd->where('vendas.data', $sale_date); else $query_qtd->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")));
        if ($group === "Termolábil") $query_qtd->where('Products.termolabil', 1);
        else if ($group === "OTC") $query_qtd->where('Products.otc', 1);
        else if ($group === "Controlados") $query_qtd->where('Products.controlled_substance', 1);
        else if ($group === "PBM") $query_qtd->where('Products.pbm', 1);
        else if ($group === "Cashback") $query_qtd->where('Products.cashback >', 0);
        else if ($group === "Home") $query_qtd->where('Products.home', 1);
        else if ($group === "Ação") $query_qtd->where('Products.acao !=', '')->where('Products.acao !=', null);
        else if ($group === "Autocuidado") $query_qtd->where('Products.category', 'AUTOCUIDADO');
        else if ($group === "Similar") $query_qtd->where('Products.category', 'SIMILAR');
        else if ($group === "Marca") $query_qtd->where('Products.category', 'MARCA');
        else if ($group === "Genérico") $query_qtd->where('Products.category', 'GENERICO');
        else if ($group === "Higiene e Beleza") $query_qtd->where('Products.category', 'HIGIENE')->orWhere('Products.category', 'HIGIENE E BELEZA');
        else if ($group === "Mamãe e Bebê") $query_qtd->where('Products.category', 'MAMÃE E BEBÊ');
        else if ($group === "Dermocosmético") $query_qtd->where('Products.category', 'DERMOCOSMETICO');
        else if ($group === "Beleza") $query_qtd->where('Products.category', 'BELEZA');
        else if ($group !== "") $query_qtd->where('Products.marca', strtoupper($group));
        if ($search != '') $query_qtd->like('vendas.sku', $search);
        if ($department != 'geral') $query_qtd->where('vendas.department', $department);
        $qtd = $query_qtd->countAllResults();
        return json_encode(array('products' => $results,
                                 'qtd' => $qtd));
    }

    public function totalFat() {
        return $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-90 days")))->get()->getResult()[0]->total;
    }

    public function totalFatTermolabil() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('termolabil', 1)
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatOTC() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('otc', 1)
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatControlados() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('controlled_substance', 1)
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatCashback() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('cashback >', 0)
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatAcao() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('acao !=', '')
                        ->where('acao !=', null)
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatPBM() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('pbm', '1')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatHome() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('home', '1')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatAutocuidado() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'AUTOCUIDADO')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatSimilar() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'SIMILAR')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatMarca() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'MARCA')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatGenerico() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'GENERICO')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatHigieneBeleza() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'HIGIENE')
                        ->orWhere('Products.category', 'HIGIENE E BELEZA')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatMamaeBebe() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'MAMÃE E BEBÊ')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatDermocosmetico() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'DERMOCOSMETICO')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatBeleza() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'BELEZA')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatMarcas() {
        return $this->db->table('Products')
                        ->select('sum(vendas.faturamento) as total, Products.marca')
                        ->join('vendas', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.category', 'DERMOCOSMETICO')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->groupBy("Products.marca")
                        ->orderBy('sum(vendas.faturamento) desc')
                        ->limit(8)
                        ->get()->getResult();
    }
}
