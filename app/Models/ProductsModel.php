<?php namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model{
    public function getProductsQuantityByDepartment($department) {
        return $this->db->table('Products')
                         ->select('count(*) as qtd')
                         ->where('diff_pay_only_lowest <', 0)
                         ->where('active', 1)
                         ->where('descontinuado !=', 1)
                         ->where('department', $department)
                         ->get()->getResult()[0]->qtd;
    }

    public function getProductsQuantityByDepartmentAndCompetitor($department, $competitor) {
        return $this->db->table('Products')
                         ->select('count(*) as qtd')
                         ->where('diff_pay_only_lowest <', 0)
                         ->where('active', 1)
                         ->where('descontinuado !=', 1)
                         ->where('department', str_replace("_", " ", $department))
                         ->like('lowest_price_competitor', $competitor)
                         ->get()->getResult()[0]->qtd;
    }

    public function getProductsByDepartment($department) {
        $fields = ['p.sku','p.title', 'p.department', 'p.category', 'p.qty_stock_rms',
                   'p.qty_competitors_available', 'p.price_cost', 'p.current_price_pay_only',
                   'p.current_less_price_around', 'p.current_gross_margin_percent',
                   'p.diff_current_pay_only_lowest', 'p.curve'];
        $data = $this->db->table('Products p')
                         ->select($fields)
                         ->where('diff_pay_only_lowest <', 0)
                         ->where('active', 1)
                         ->where('descontinuado !=', 1)
                         ->where('department', $department)
                         ->get()->getResult();
        return json_encode($data);
    }

    public function getProductsCategoriesByDepartment($department) {
        $response = [];
        $data = $this->db->table('Products')
                         ->select('category')
                         ->where('diff_pay_only_lowest <', 0)
                         ->where('active', 1)
                         ->where('descontinuado !=', 1)
                         ->where('category !=', $department)
                         ->where('department', str_replace("_", " ", $department))
                         ->groupBy("category")
                         ->get()->getResult();
        foreach($data as $row) {
            array_push($response, $row->category);
        }
        return $response;
    }

    public function getProductsQuantityByDepartmentAndCategories($department, $category) {
        return $this->db->table('Products')
                        ->select('count(*) as qtd')
                        ->where('diff_pay_only_lowest <', 0)
                        ->where('active', 1)
                        ->where('descontinuado !=', 1)
                        ->where('department', str_replace("_", " ", $department))
                        ->where('category', $category)
                        ->get()->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingDrogaraia() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Drogaraia dr
                                 INNER JOIN Products p on p.sku = dr.sku
                                 WHERE dr.valor < p.current_price_pay_only
                                     and p.active = 1
                                     and p.descontinuado != 1
                                     and dr.valor != 0", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingBelezanaweb() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Belezanaweb bw
                                 INNER JOIN Products p on p.sku = bw.sku
                                 WHERE bw.valor < p.current_price_pay_only
                                     and active = 1
                                     and p.descontinuado != 1
                                     and bw.valor != 0", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingDrogariasp() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Drogariasp ds
                                 INNER JOIN Products p on p.sku = ds.sku
                                 WHERE ds.valor < p.current_price_pay_only
                                     and active = 1
                                     and p.descontinuado != 1
                                     and ds.valor != 0", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingDrogasil() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Drogasil dsl
                                 INNER JOIN Products p on p.sku = dsl.sku
                                 WHERE dsl.valor < p.current_price_pay_only
                                     and active = 1
                                     and p.descontinuado != 1
                                     and dsl.valor != 0", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingOnofre() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Onofre o
                                 INNER JOIN Products p on p.sku = o.sku
                                 WHERE o.valor < p.current_price_pay_only
                                     and active = 1
                                     and p.descontinuado != 1
                                     and o.valor != 0", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingPaguemenos() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Paguemenos pm
                                 INNER JOIN Products p on p.sku = pm.sku
                                 WHERE pm.valor < p.current_price_pay_only
                                     and active = 1
                                     and p.descontinuado != 1
                                     and pm.valor != 0", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingUltrafarma() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Ultrafarma uf
                                 INNER JOIN Products p on p.sku = uf.sku
                                 WHERE uf.valor < p.current_price_pay_only
                                     and active = 1
                                     and p.descontinuado != 1
                                     and uf.valor != 0", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingPanvel() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Panvel pvl
                                 INNER JOIN Products p on p.sku = pvl.sku
                                 WHERE pvl.valor < p.current_price_pay_only
                                     and active = 1
                                     and p.descontinuado != 1
                                     and pvl.valor != 0", false)->getResult()[0]->qtd;
    }

    public function getTotalStockRMS() {
        return $this->db->table('Products')
                        ->select('sum(qty_stock_rms) as qtd')
                        ->where('active', 1)
                        ->where('descontinuado !=', 1)
                        ->get()->getResult()[0]->qtd;
    }

    public function getTotalPriceCost() {
        return $this->db->table('Products')
                        ->select('sum(price_cost*qty_stock_rms) as total')
                        ->where('active', 1)
                        ->where('descontinuado !=', 1)
                        ->get()->getResult()[0]->total;
    }

    public function getTotalPricePayOnly() {
        return $this->db->table('Products')
                        ->select('sum(current_price_pay_only*qty_stock_rms) as total')
                        ->where('active', 1)
                        ->where('descontinuado !=', 1)
                        ->get()->getResult()[0]->total;
    }

    public function getTotalCashback() {
        return $this->db->table('Products')
                        ->select('sum(current_cashback*qty_stock_rms) as total')
                        ->where('active', 1)
                        ->where('descontinuado !=', 1)
                        ->get()->getResult()[0]->total;
    }

    public function getAvgGrossMargin($curve = '') {
        $query = $this->db->table('Products')
                          ->select('avg(current_gross_margin_percent) as margin')
                          ->where('active', 1)
                          ->where('descontinuado !=', 1)
                          ->where('qty_stock_rms >', 0);
        if ($curve != '') $query->where('curve', $curve);
        return $query->get()->getResult()[0]->margin;
    }

    public function getAvgDiffMargin($curve = '') {
        $query = $this->db->table('Products')
                          ->select('avg(diff_current_pay_only_lowest) as margin')
                          ->where('active', 1)
                          ->where('descontinuado !=', 1)
                          ->where('qty_stock_rms >', 0);
        if ($curve != '') $query->where('curve', $curve);
        return $query->get()->getResult()[0]->margin;
    }

    public function getTotalSkus($curve = '') {
        $query = $this->db->table('Products')
                          ->select('count(1) as qtd')
                          ->where('active', 1)
                          ->where('descontinuado !=', 1);
        if ($curve != '') $query->where('curve', $curve);
        return $query->get()->getResult()[0]->qtd;
    }

    public function getTotalBreak($curve = '') {
        $query = $this->db->table('Products')
                          ->select('count(1) as qtd')
                          ->where('active', 1)
                          ->where('qty_stock_rms', 0)
                          ->where('descontinuado !=', 1);
        if ($curve != '') $query->where('curve', $curve);
        return $query->get()->getResult()[0]->qtd;
    }

    public function getTotalUnderCost($curve = '') {
        $query = $this->db->table('Products')
                          ->select('count(1) as qtd')
                          ->where('active', 1)
                          ->where('current_gross_margin_percent <', 0)
                          ->where('qty_stock_rms >', 0)
                          ->where('descontinuado !=', 1);
        if ($curve != '') $query->where('curve', $curve);
        return $query->get()->getResult()[0]->qtd;
    }

    public function getTotalExclusiveStock($curve = '') {
        $query = $this->db->table('Products')
                          ->select('count(1) as qtd')
                          ->where('active', 1)
                          ->where('qty_competitors', 0)
                          ->where('qty_stock_rms >', 0)
                          ->where('descontinuado !=', 1);
        if ($curve != '') $query->where('curve', $curve);
        return $query->get()->getResult()[0]->qtd;
    }

    public function getFieldsToMargin($skus) {
        return $this->db->table('Products')
                        ->select('sku as productCode, price_cost, department, category')
                        ->whereIn('sku', $skus)
                        ->get()->getResult();

    }

    public function getQtyCategoriesByDepartment($department) {
        $query = $this->db->table('Products')
                          ->select('category as name, count(1) as qtd')
                          ->where('department !=', '')
                          ->where('category !=', 'AUTOCUIDADO')
                          ->where('category !=', '#N/D');
        if ($department != 'Geral') $query->where('department', $department);
        $query->groupBy('category');
        return $query->get()->getResult();

    }
}
