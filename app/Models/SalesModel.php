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
                                    Products.department,
                                    Products.category,
                                    sum(vendas.qtd) as qtd,
                                    Products.title,
                                    Products.curve,
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
        else if ($group === "Autocuidado") $query->where('Products.category', 'AUTOCUIDADO');
        else if ($group === "Similar") $query->where('Products.category', 'SIMILAR');
        else if ($group === "Marca") $query->where('Products.category', 'MARCA');
        else if ($group === "Genérico") $query->where('Products.category', 'GENERICO');
        else if ($group === "Higiene e Beleza") $query->where('Products.category', 'HIGIENE')->orWhere('Products.category', 'HIGIENE E BELEZA');
        else if ($group === "Mamãe e Bebê") $query->where('Products.category', 'MAMÃE E BEBÊ');
        else if ($group === "Dermocosmético") $query->where('Products.category', 'DERMOCOSMETICO');
        else if ($group === "Beleza") $query->where('Products.category', 'BELEZA');
        else if ($group === "Perdendo") $query->where('Products.diff_current_pay_only_lowest <', 0);
        else if ($group === "0 Cashback") $query->where('Products.acao', '0 Cashback');
        else if ($group === "5% + 5% Progress") $query->where('Products.acao', '5% + 5% Progress');
        else if ($group === "Vencimento") $query->where('Products.acao', 'Vencimento');
        else if ($group === "5% progressivo") $query->where('Products.acao', '5% progressivo');
        else if ($group === "Aumento TKM") $query->where('Products.acao', 'Aumento TKM');
        else if ($group === "Prego") $query->where('Products.acao', 'Prego');
        else if ($group === "3% Progressivo") $query->where('Products.acao', '3% Progressivo');
        else if ($group === "3% + 5% Progressivo") $query->where('Products.acao', '3% + 5% Progressivo');
        else if ($group !== "") $query->where('Products.marca', strtoupper($group));
        if ($search != '') $query->like('vendas.sku', $search);
        if ($department != 'geral') $query->where('vendas.department', $department);
        $results = $query->get()->getResult();


        // Pega todos os skus filtrados
        $skus = array_map(function ($ar) {return $ar->sku;}, $results);

        // Calcula o VMD dos últimos 7 dias pra cada SKU
        $query_weekly = $this->db->table('vendas')->select('sku, sum(qtd)/7 as weekly');
        if ($sale_date != "") {
            $query_weekly->where('data >=', date('Y-m-d', strtotime($sale_date."-7 days")));
            $query_weekly->where('data <=', $sale_date);
        }
        else {
            $query_weekly->where('data >=', date('Y-m-d', strtotime("-7 days")));
            $query_weekly->where('data <=', date('Y-m-d'));
        }
        $query_weekly->whereIn('sku', $skus);
        $query_weekly->groupBy('sku');
        $array_weekly = $query_weekly->get()->getResult();

        // Calcula o VMD dos últimos 30 dias pra cada SKU
        $query_last_month = $this->db->table('vendas')->select('sku, sum(qtd)/30 as last_month');
        if ($sale_date != "") {
            $query_last_month->where('data >=', date('Y-m-d', strtotime($sale_date."-30 days")));
            $query_last_month->where('data <=', $sale_date);
        }
        else {
            $query_last_month->where('data >=', date('Y-m-d', strtotime("-30 days")));
            $query_last_month->where('data <=', date('Y-m-d'));
        }
        $query_last_month->whereIn('sku', $skus);
        $query_last_month->groupBy('sku');
        $array_last_month = $query_last_month->get()->getResult();

        // Calcula o VMD dos últimos 90 dias pra cada SKU
        $query_last_3_months = $this->db->table('vendas')->select('sku, sum(qtd)/90 as last_3_months');
        if ($sale_date != "") {
            $query_last_3_months->where('data >=', date('Y-m-d', strtotime($sale_date."-90 days")));
            $query_last_3_months->where('data <=', $sale_date);
        }
        else {
            $query_last_3_months->where('data >=', date('Y-m-d', strtotime("-90 days")));
            $query_last_3_months->where('data <=', date('Y-m-d'));
        }
        $query_last_3_months->whereIn('sku', $skus);
        $query_last_3_months->groupBy('sku');
        $array_last_3_months = $query_last_3_months->get()->getResult();

        // Faz o cálculo do VMD dos últimos 7 dias, 30 dias e 90 dias
        foreach($results as $row) {
            $label = $row->sku;
            $row->weekly = array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'weekly')[0] ?? 0;
            $row->last_month = array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'last_month')[0] ?? 0;
            $row->last_3_months = array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'last_3_months')[0] ?? 0;
        }

        return json_encode(array('products' => array_slice($results, $initial_limit, $final_limit),
                                 'qtd' => count($results),
                                 'up_total_1' => count(array_filter($results, function($i) { if($i->last_month > 0) return ((($i->weekly/$i->last_month) - 1) > 0); })),
                                 'up_a_1' => count(array_filter($results, function($i) { if($i->last_month > 0) return ((($i->weekly/$i->last_month) - 1) > 0) && $i->curve == 'A'; })),
                                 'up_b_1' => count(array_filter($results, function($i) { if($i->last_month > 0) return ((($i->weekly/$i->last_month) - 1) > 0) && $i->curve == 'B'; })),
                                 'up_c_1' => count(array_filter($results, function($i) { if($i->last_month > 0) return ((($i->weekly/$i->last_month) - 1) > 0) && $i->curve == 'C'; })),
                                 'down_total_1' => count(array_filter($results, function($i) { if($i->last_month > 0) return ((($i->weekly/$i->last_month) - 1) < 0); })),
                                 'down_a_1' => count(array_filter($results, function($i) { if($i->last_month > 0) return ((($i->weekly/$i->last_month) - 1) < 0) && $i->curve == 'A'; })),
                                 'down_b_1' => count(array_filter($results, function($i) { if($i->last_month > 0) return ((($i->weekly/$i->last_month) - 1) < 0) && $i->curve == 'B'; })),
                                 'down_c_1' => count(array_filter($results, function($i) { if($i->last_month > 0) return ((($i->weekly/$i->last_month) - 1) < 0) && $i->curve == 'C'; })),
                                 'keep_total_1' => count(array_filter($results, function($i) { return $i->weekly === $i->last_month; })),
                                 'keep_a_1' => count(array_filter($results, function($i) { return $i->weekly === $i->last_month && $i->curve == 'A'; })),
                                 'keep_b_1' => count(array_filter($results, function($i) { return $i->weekly === $i->last_month && $i->curve == 'B'; })),
                                 'keep_c_1' => count(array_filter($results, function($i) { return $i->weekly === $i->last_month && $i->curve == 'C'; })),
                                 'up_total_2' => count(array_filter($results, function($i) { if($i->last_3_months > 0) return ((($i->last_month/$i->last_3_months) - 1) > 0); })),
                                 'up_a_2' => count(array_filter($results, function($i) { if($i->last_3_months > 0) return ((($i->last_month/$i->last_3_months) - 1) > 0) && $i->curve == 'A'; })),
                                 'up_b_2' => count(array_filter($results, function($i) { if($i->last_3_months > 0) return ((($i->last_month/$i->last_3_months) - 1) > 0) && $i->curve == 'B'; })),
                                 'up_c_2' => count(array_filter($results, function($i) { if($i->last_3_months > 0) return ((($i->last_month/$i->last_3_months) - 1) > 0) && $i->curve == 'C'; })),
                                 'down_total_2' => count(array_filter($results, function($i) { if($i->last_3_months > 0) return ((($i->last_month/$i->last_3_months) - 1) < 0); })),
                                 'down_a_2' => count(array_filter($results, function($i) { if($i->last_3_months > 0) return ((($i->last_month/$i->last_3_months) - 1) < 0) && $i->curve == 'A'; })),
                                 'down_b_2' => count(array_filter($results, function($i) { if($i->last_3_months > 0) return ((($i->last_month/$i->last_3_months) - 1) < 0) && $i->curve == 'B'; })),
                                 'down_c_2' => count(array_filter($results, function($i) { if($i->last_3_months > 0) return ((($i->last_month/$i->last_3_months) - 1) < 0) && $i->curve == 'C'; })),
                                 'keep_total_2' => count(array_filter($results, function($i) { return $i->last_month === $i->last_3_months; })),
                                 'keep_a_2' => count(array_filter($results, function($i) { return $i->last_month === $i->last_3_months && $i->curve == 'A'; })),
                                 'keep_b_2' => count(array_filter($results, function($i) { return $i->last_month === $i->last_3_months && $i->curve == 'B'; })),
                                 'keep_c_2' => count(array_filter($results, function($i) { return $i->last_month === $i->last_3_months && $i->curve == 'C'; }))));
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

    public function totalFatPerdendo() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.diff_current_pay_only_lowest <', 0)
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

    public function totalFatCashback0() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', '0 Cashback')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatProgress55() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', '5% + 5% Progress')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatVencimento() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', 'Vencimento')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatProgressivo5() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', '5% progressivo')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatAumentoTKM() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', 'Aumento TKM')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatPrego() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', 'PREGO')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatProgressivo3() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', '3% Progressivo')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalFatProgressivo35() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', '3% + 5% Progressivo')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }
}
