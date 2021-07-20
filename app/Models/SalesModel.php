<?php namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model{
    public function getSalesByDepartment($department) {
        $query = $this->db->table('vendas')
                          ->select('CONCAT(MONTH(data), "/", YEAR(data)) as data, sum(faturamento) as faturamento, (sum(faturamento) - sum(price_cost))/sum(faturamento)*100 as margin')
                          ->where('data >=', date('Y-m-01', strtotime("-5 months")))
                          ->groupBy('MONTH(DATA)');
        if ($department != 'Geral') $query->where('department', $department);
        return $query->get()->getResult();
    }

    public function getPBMSalesLastMonths($program) {
        $query = $this->db->table('relatorio_pbm')
                          ->select('CONCAT(MONTH(relatorio_pbm.order_date), "/", YEAR(relatorio_pbm.order_date)) as date, relatorio_pbm.sku, relatorio_pbm.value as faturamento, Products.price_cost, pbm_van.van, pbm_van.programa')
                          ->join('Products', 'relatorio_pbm.sku = Products.sku')
                          ->join('pbm_van', 'relatorio_pbm.van_program = pbm_van.id')
                          ->where('relatorio_pbm.order_date >=', date('Y-m-01', strtotime("-5 months")))
                          ->orderBy('relatorio_pbm.order_date asc');
        if ($program != 'Todos') $query->where('pbm_van.programa', $program);
        return $query->get()->getResult();
    }

    public function getPBMSalesVans() {
        $query = $this->db->table('relatorio_pbm')
                          ->select('pbm_van.van as label')
                          ->join('pbm_van', 'pbm_van.id = relatorio_pbm.van_program');
        return $query->get()->getResult();
    }

    public function getPBMSalesPrograms() {
        $query = $this->db->table('relatorio_pbm')
                          ->distinct()
                          ->select('pbm_van.programa as label, (SELECT COUNT(1) FROM relatorio_pbm WHERE van_program = pbm_van.id) as value')
                          ->join('pbm_van', 'pbm_van.id = relatorio_pbm.van_program');
        return $query->get()->getResult();
    }

    public function getPBMSalesWithoutProgram($period) {
        $query = $this->db->table('vendas')
                          ->select('vendas.sku, sum(faturamento) as faturamento')
                          ->join('Products', 'vendas.sku = Products.sku')
                          ->where('Products.active', 1)
                          ->where('Products.pbm', 1)
                          ->where('Products.descontinuado !=', 1);
        if($period == 'Todos') $query->where('vendas.data >=', date('Y-m-01', strtotime("-90 days")));
        if($period == 'antepenultimo') $query->where('MONTH(vendas.data)', date('m', strtotime("-2 months")));
        if($period == 'penultimo') $query->where('MONTH(vendas.data)', date('m', strtotime("-1 months")));
        if($period == 'ultimo') $query->where('MONTH(vendas.data)', date('m'));
        $query->groupBy('sku');
        $results = $query->get()->getResult();
        return json_encode($results);
    }

    public function getPBMSalesWithProgram($period, $skus) {
        $query = $this->db->table('relatorio_pbm')
                          ->select('sum(relatorio_pbm.value) as faturamento, pbm_van.van')
                          ->join('pbm_van', 'pbm_van.id = relatorio_pbm.van_program')
                          ->whereIn('relatorio_pbm.sku', $skus);
        if($period == 'Todos') $query->where('relatorio_pbm.order_date >=', date('Y-m-01', strtotime("-90 days")));
        if($period == 'antepenultimo') $query->where('MONTH(relatorio_pbm.order_date)', date('m', strtotime("-2 months")));
        if($period == 'penultimo') $query->where('MONTH(relatorio_pbm.order_date)', date('m', strtotime("-1 months")));
        if($period == 'ultimo') $query->where('MONTH(relatorio_pbm.order_date)', date('m'));
        $query->groupBy('van');
        $results = $query->get()->getResult();
        return json_encode($results);
    }

    public function getDataTopProductsTable($department, $initial_limit, $final_limit, $sort_column, $sort_order, $search) {
        $query = $this->db->table('vendas')
                          ->select('vendas.sku,
                                    Products.department,
                                    Products.category,
                                    sum(vendas.qtd) as qtd,
                                    Products.title,
                                    Products.curve,
                                    sum(vendas.faturamento) as faturamento')
                          ->join('Products', 'vendas.sku = Products.sku')
                          ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                          ->orderBy("sum(vendas.faturamento) desc")
                          ->groupBy("Products.sku")
                          ->limit(2200);
        $results = $query->get()->getResult();

        // Pega todos os skus filtrados pelo departamento específico
        $results = array_filter($results, function($item) use($department) { return $item->department == strtoupper($department); });
        $skus = array_map(function ($ar) {return $ar->sku;}, $results);

        // Calcula o VMD dos últimos 7 dias pra cada SKU
        $array_weekly = $this->db->table('vendas')
                                 ->select('sku, sum(qtd)/7 as weekly, sum(faturamento) as fat_weekly')
                                 ->where('data >=', date('Y-m-d', strtotime("-7 days")))
                                 ->where('data <=', date('Y-m-d'))
                                 ->whereIn('sku', $skus)
                                 ->groupBy('sku')->get()->getResult();

        // Calcula o VMD dos últimos 30 dias pra cada SKU
        $array_last_month  = $this->db->table('vendas')
                                      ->select('sku, sum(qtd)/30 as last_month, sum(faturamento) as fat_last_month')
                                      ->where('data >=', date('Y-m-d', strtotime("-30 days")))
                                      ->where('data <=', date('Y-m-d'))
                                      ->whereIn('sku', $skus)
                                      ->groupBy('sku')->get()->getResult();

        // Calcula o VMD dos últimos 90 dias pra cada SKU
        $array_last_3_months = $this->db->table('vendas')
                                        ->select('sku, sum(qtd)/90 as last_3_months, sum(faturamento) as fat_last_3_months')
                                        ->where('data >=', date('Y-m-d', strtotime("-90 days")))
                                        ->where('data <=', date('Y-m-d'))
                                        ->whereIn('sku', $skus)
                                        ->groupBy('sku')->get()->getResult();

        // Traz o faturamento total dos últimos 7 dias
        $total_fat_weekly = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-7 days")))->where('data <=', date('Y-m-d'))->get()->getResult()[0]->total ?? 0;

        // Traz o faturamento total do último mês
        $total_fat_last_month = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-30 days")))->where('data <=', date('Y-m-d'))->get()->getResult()[0]->total ?? 0;

        // Traz o faturamento total dos últimos 3 meses
        $total_fat_last_3_months = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-90 days")))->where('data <=', date('Y-m-d'))->get()->getResult()[0]->total ?? 0;

        // Faz o cálculo do VMD dos últimos 7 dias, 30 dias e 90 dias
        foreach($results as $row) {
            $label = $row->sku;
            $row->weekly = array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'weekly')[0] ?? 0;
            $row->last_month = array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'last_month')[0] ?? 0;
            $row->last_3_months = array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'last_3_months')[0] ?? 0;
            $row->pm_weekly = ($total_fat_weekly !== 0 && count(array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'fat_weekly'))) ? array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'fat_weekly')[0]/$total_fat_weekly : 0;
            $row->pm_last_month = ($total_fat_last_month !== 0 && count(array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'fat_last_month'))) ? array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'fat_last_month')[0]/$total_fat_last_month : 0;
            $row->pm_last_3_months = ($total_fat_last_3_months !== 0 && count(array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'fat_last_3_months'))) ? array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'fat_last_3_months')[0]/$total_fat_last_3_months : 0;
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
        else if ($group === "Perdendo") $query->where('Products.diff_pay_only_lowest <', 0);
        else if ($group === "0 Cashback") $query->where('Products.acao', '0 Cashback');
        else if ($group === "5%   5% Progress") $query->where('Products.acao', '5% + 5% Progress');
        else if ($group === "Vencimento") $query->where('Products.acao', 'Vencimento');
        else if ($group === "5% progressivo") $query->where('Products.acao', '5% progressivo');
        else if ($group === "Aumento TKM") $query->where('Products.acao', 'Aumento TKM');
        else if ($group === "PREGO") $query->where('Products.acao', 'Prego');
        else if ($group === "3% Progressivo") $query->where('Products.acao', '3% Progressivo');
        else if ($group === "3%   5% Progressivo") $query->where('Products.acao', '3% + 5% Progressivo');
        else if ($group === "AUMENTO FAT 35%") $query->where('Products.acao', 'AUMENTO FAT 35%');
  			else if ($group === "AUMENTO FAT 16%") $query->where('Products.acao', 'AUMENTO FAT 16%');
  			else if ($group === "AUMENTO FAT 25%") $query->where('Products.acao', 'AUMENTO FAT 25%');
        else if ($group === "CASHBACK 0") $query->where('Products.acao', 'CASHBACK 0');
        else if ($group === "CASHBACK 0   20%") $query->where('Products.acao', 'CASHBACK 0 + 20%');
        else if ($group === "Regiane Ago") $query->where('Products.acao', 'Regiane Ago');
        else if ($group === "MIP") $query->where('Products.sub_category', 'MIP');
        else if ($group === "Éticos") $query->where('Products.sub_category', 'Eticos');
        else if ($group === "No Medicamentos") $query->where('Products.sub_category', 'No Medicamentos');
        else if ($group === "Perfumaria") $query->where('Products.sub_category', 'Perfumaria');
        else if ($group === "Genéricos") $query->where('Products.sub_category', 'Genericos');
        else if ($group === "Dermocosméticos") $query->where('Products.sub_category', 'Dermocosmeticos');
        else if ($group === "Similares") $query->where('Products.sub_category', 'Similar');
        else if ($group !== "") $query->where('Products.marca', strtoupper($group));
        if ($search != '') $query->like('vendas.sku', $search);
        if ($department != 'geral') $query->where('vendas.department', $department);
        $results = $query->get()->getResult();

        // Pega todos os skus filtrados
        $skus = array_map(function ($ar) {return $ar->sku;}, $results);

        // Calcula o VMD dos últimos 7 dias pra cada SKU
        $query_weekly = $this->db->table('vendas')->select('sku, sum(qtd)/7 as weekly, sum(faturamento) as fat_weekly');
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
        $query_last_month = $this->db->table('vendas')->select('sku, sum(qtd)/30 as last_month, sum(faturamento) as fat_last_month');
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
        $query_last_3_months = $this->db->table('vendas')->select('sku, sum(qtd)/90 as last_3_months, sum(faturamento) as fat_last_3_months');
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

        // Traz o faturamento total dos últimos 7 dias
        $total_fat_weekly = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-7 days")))->where('data <=', date('Y-m-d'))->get()->getResult()[0]->total ?? 0;

        // Traz o faturamento total do último mês
        $total_fat_last_month = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-30 days")))->where('data <=', date('Y-m-d'))->get()->getResult()[0]->total ?? 0;

        // Traz o faturamento total dos últimos 3 meses
        $total_fat_last_3_months = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-90 days")))->where('data <=', date('Y-m-d'))->get()->getResult()[0]->total ?? 0;

        // Faz o cálculo do VMD dos últimos 7 dias, 30 dias e 90 dias
        foreach($results as $row) {
            $label = $row->sku;
            $row->weekly = array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'weekly')[0] ?? 0;
            $row->last_month = array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'last_month')[0] ?? 0;
            $row->last_3_months = array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'last_3_months')[0] ?? 0;
            $row->pm_weekly = ($total_fat_weekly !== 0 && count(array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'fat_weekly')) > 0) ? array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'fat_weekly')[0]/$total_fat_weekly : 0;
            $row->pm_last_month = ($total_fat_last_month !== 0 && count(array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'fat_last_month')) > 0) ? array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'fat_last_month')[0]/$total_fat_last_month : 0;
            $row->pm_last_3_months = ($total_fat_last_3_months !== 0 && count(array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'fat_last_3_months')) > 0) ? array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'fat_last_3_months')[0]/$total_fat_last_3_months : 0;
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
                        ->where('Products.diff_pay_only_lowest <', 0)
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

    public function getRankingActions() {
        return $this->db->query("SELECT acao,
                                        RANK() OVER (ORDER BY COUNT(1) DESC) ranking
                                 FROM Products
                                 WHERE acao != '' and acao != 1
                                 GROUP BY acao
                                 LIMIT 8", false)->getResult();
    }

    public function totalFatActions($action) {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.acao', $action)
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalSubCatMIP() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.sub_category', 'MIP')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalSubCatEticos() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.sub_category', 'Eticos')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalSubCatNoMed() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.sub_category', 'No Medicamentos')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalSubCatPerf() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.sub_category', 'Perfumaria')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalSubCatGen() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.sub_category', 'Genericos')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalSubCatDermo() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.sub_category', 'Dermocosmeticos')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function totalSubCatSimilar() {
        return $this->db->table('vendas')
                        ->select('sum(faturamento) as total')
                        ->join('Products', 'vendas.sku = Products.sku')
                        ->where('Products.active', 1)
                        ->where('Products.descontinuado !=', 1)
                        ->where('Products.sub_category', 'Similar')
                        ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                        ->get()->getResult()[0]->total;
    }

    public function getMarginDiscAll($curve) {
        $query = $this->db->table('Products')
                          ->select('avg(Products.diff_pay_only_lowest) as margin')
                          ->join('vendas', 'vendas.sku = Products.sku')
                          ->where('Products.active', 1)
                          ->where('Products.descontinuado', 1)
                          ->where('vendas.data >=', date('Y-m-d', strtotime("-30 days")))
                          ->where('Products.qty_stock_rms >', 0);
        if ($curve != '') $query->where('Products.curve', $curve);
        return $query->get()->getResult()[0]->margin;
    }

    public function getMarginSimulatorInfo($department, $category, $group, $margin_from, $margin_at, $disc_from, $disc_at, $skus, $curve, $initial_limit, $final_limit, $sort_column, $sort_order) {
        $query = $this->db->table('vendas')
                          ->select('vendas.sku,
                                    Products.title')
                          ->join('Products', 'vendas.sku = Products.sku')
                          ->where('Products.active', 1)
                          ->where('Products.descontinuado !=', 1)
                          ->orderBy("vendas.$sort_column $sort_order")
                          ->groupBy("Products.sku");
        if ($curve != '') $query->where('Products.curve', $curve);
        if ($department != '') $query->where('Products.department', strtoupper($department));
        if ($category != '') $query->where('Products.category', strtoupper($category));
        if ($group == 'perdendo') $query->where('Products.diff_pay_only_lowest <', 0);
        else if($group == 'top') $query->where($group, 1);
        else if($group != '') $query->where($group, 1);
        if($margin_from != "" && $margin_at != "") $query->where('Products.gross_margin_percent >=', floatval($margin_from)/100)->where('Products.gross_margin_percent <=', floatval($margin_at)/100);
        if($disc_from != "" && $disc_at != "") $query->where('Products.diff_pay_only_lowest >=', floatval($disc_from)/100)->where('Products.diff_pay_only_lowest <=', floatval($disc_at)/100);
        if($skus != "undefined") $query->whereIn('Products.sku', explode(",", $skus));
        $results = $query->get()->getResult();

        if(count($results) > 0) {
            // Pega todos os skus filtrados
            $skus = array_map(function ($ar) {return $ar->sku;}, $results);

            // Calcula o VMD dos últimos 7 dias pra cada SKU
            $array_weekly = $this->db->table('vendas')
                                     ->select('sku, sum(qtd)/7 as weekly, sum(faturamento) as fat_weekly')
                                     ->where('data >=', date('Y-m-d', strtotime("-7 days")))
                                     ->where('data <=', date('Y-m-d'))
                                     ->whereIn('sku', $skus)
                                     ->groupBy('sku')->get()->getResult();

            // Calcula o VMD dos últimos 30 dias pra cada SKU
            $array_last_month  = $this->db->table('vendas')
                                          ->select('sku, sum(qtd)/30 as last_month, sum(faturamento) as fat_last_month')
                                          ->where('data >=', date('Y-m-d', strtotime("-30 days")))
                                          ->where('data <=', date('Y-m-d'))
                                          ->whereIn('sku', $skus)
                                          ->groupBy('sku')->get()->getResult();

            // Calcula o VMD dos últimos 90 dias pra cada SKU
            $array_last_3_months = $this->db->table('vendas')
                                            ->select('sku, sum(qtd)/90 as last_3_months, sum(faturamento) as fat_last_3_months')
                                            ->where('data >=', date('Y-m-d', strtotime("-90 days")))
                                            ->where('data <=', date('Y-m-d'))
                                            ->whereIn('sku', $skus)
                                            ->groupBy('sku')->get()->getResult();

            // Traz o faturamento total dos últimos 7 dias
            $total_fat_weekly = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-7 days")))->where('data <=', date('Y-m-d'))->get()->getResult();

            // Traz o faturamento total do último mês
            $total_fat_last_month = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-30 days")))->where('data <=', date('Y-m-d'))->get()->getResult()[0]->total;

            // Traz o faturamento total dos últimos 3 meses
            $total_fat_last_3_months = $this->db->table('vendas')->select('sum(faturamento) as total')->where('data >=', date('Y-m-d', strtotime("-90 days")))->where('data <=', date('Y-m-d'))->get()->getResult()[0]->total;

            // Faz o cálculo do VMD dos últimos 7 dias, 30 dias e 90 dias
            foreach($results as $row) {
                $label = $row->sku;
                $row->vmd_weekly = array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'weekly')[0] ?? 0;
                $row->vmd_last_month = array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'last_month')[0] ?? 0;
                $row->vmd_last_3_months = array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'last_3_months')[0] ?? 0;
                $row->pm_weekly = array_column(array_filter($array_weekly, function($item) use($label) {return $item->sku == $label; }), 'fat_weekly')[0]/$total_fat_weekly ?? 0;
                $row->pm_last_month = array_column(array_filter($array_last_month, function($item) use($label) { return $item->sku == $label; }), 'fat_last_month')[0]/$total_fat_last_month ?? 0;
                $row->pm_last_3_months = array_column(array_filter($array_last_3_months, function($item) use($label) { return $item->sku == $label; }), 'fat_last_3_months')[0]/$total_fat_last_3_months ?? 0;
            }
            $return = array_slice($results, $initial_limit, $final_limit);
        }
        else {
            $return = [];
        }
        return json_encode(array('products' => $return,
                                 'qtd' => count($results)));
    }

    public function getQtyTopProducts($department) {
        $results = $this->db->table('vendas')
                            ->select('Products.department')
                            ->join('Products', 'vendas.sku = Products.sku')
                            ->where('vendas.data >=', date('Y-m-d', strtotime("-90 days")))
                            ->orderBy('sum(vendas.faturamento) desc')
                            ->groupBy('Products.sku')
                            ->limit(2200)->get()->getResult();
        return count(array_filter($results, function($item) use($department) {return $item->department == strtoupper($department); }));
    }

    public function getMGMSales($date) {
        return $this->db->table('mgm')->select('*')->where('order_date >=', date('Y-m-d', strtotime($date."-7 days"))." 00:00:00")->orderBy('order_date desc')->get()->getResult();
    }

    public function getPBMSales($date) {
        return $this->db->table('relatorio_pbm')->select('relatorio_pbm.*, pbm_van.van, pbm_van.programa')->join('pbm_van', 'pbm_van.id = relatorio_pbm.van_program')->where('relatorio_pbm.order_date >=', date('Y-m-d', strtotime($date."-7 days"))." 00:00:00")->orderBy('relatorio_pbm.order_date desc')->get()->getResult();
    }

    public function getMostlyIndicators() {
        return $this->db->query("SELECT distinct m.indicator_name,
                                 (SELECT COUNT(1) FROM mgm WHERE indicator_email = m.indicator_email) as qty_indications
                                 FROM mgm m
                                 ORDER BY qty_indications desc
                                 LIMIT 10", false)->getResult();
    }

    public function getBestSellersPBM() {
        return $this->db->query("SELECT DISTINCT p.programa, (SELECT COUNT(1) FROM relatorio_pbm WHERE van_program = p.id) as qtd
                                 FROM relatorio_pbm r
                                 INNER JOIN pbm_van p on p.id = r.van_program
                                 ORDER BY qtd DESC
                                 LIMIT 10;", false)->getResult();
    }
}
