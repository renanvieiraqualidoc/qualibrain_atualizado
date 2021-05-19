<?php namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model{
    public function getProductsQuantityByDepartment($department) {
        return $this->db->table('Products')
                         ->select('count(*) as qtd')
                         ->where('diff_current_pay_only_lowest <', 0)
                         ->where('active', 1)
                         ->where('descontinuado !=', 1)
                         ->where('department', $department)
                         ->get()->getResult()[0]->qtd;
    }

    public function getProductsQuantityByDepartmentAndCompetitor($department, $competitor) {
        return $this->db->table('Products')
                         ->select('count(*) as qtd')
                         ->where('diff_current_pay_only_lowest <', 0)
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
                         ->where('diff_current_pay_only_lowest <', 0)
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
                         ->where('diff_current_pay_only_lowest <', 0)
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
                        ->where('diff_current_pay_only_lowest <', 0)
                        ->where('active', 1)
                        ->where('descontinuado !=', 1)
                        ->where('department', str_replace("_", " ", $department))
                        ->where('category', $category)
                        ->get()->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingDrogaraia() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE drogaraia < current_price_pay_only
                                     and active = 1
                                     and descontinuado != 1
                                     and drogaraia is not null", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingBelezanaweb() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE belezanaweb < current_price_pay_only
                                     and active = 1
                                     and descontinuado != 1
                                     and belezanaweb is not null", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingDrogariasp() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE drogariasp < current_price_pay_only
                                     and active = 1
                                     and descontinuado != 1
                                     and drogariasp is not null", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingDrogasil() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE drogasil < current_price_pay_only
                                     and active = 1
                                     and descontinuado != 1
                                     and drogasil is not null", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingOnofre() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE onofre < current_price_pay_only
                                     and active = 1
                                     and descontinuado != 1
                                     and onofre is not null", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingPaguemenos() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE paguemenos < current_price_pay_only
                                     and active = 1
                                     and descontinuado != 1
                                     and paguemenos is not null", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingUltrafarma() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE ultrafarma < current_price_pay_only
                                     and active = 1
                                     and descontinuado != 1
                                     and ultrafarma is not null", false)->getResult()[0]->qtd;
    }

    public function getQuantityProductsLosingPanvel() {
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE panvel < current_price_pay_only
                                     and active = 1
                                     and descontinuado != 1
                                     and panvel is not null", false)->getResult()[0]->qtd;
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
                          ->where('descontinuado', 1)
                          ->where('qty_stock_rms >', 0);
        if ($curve != '') $query->where('curve', $curve);
        return $query->get()->getResult()[0]->margin;
    }

    public function getTotalSkus($curve = '') {
        $query = $this->db->table('Products')
                          ->select('count(1) as qtd')
                          ->where('active !=', 0)
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

    public function getTotalLosingAll($curve = '') {
        $comp = $curve != '' ? "and curve = '$curve'" : '';
        return $this->db->query("SELECT COUNT(*) AS qtd FROM Products
                                 WHERE (belezanaweb <=> 0) < current_price_pay_only
                                     and (ultrafarma <=> 0) < current_price_pay_only
                                     and (panvel <=> 0) < current_price_pay_only
                                     and (paguemenos <=> 0) < current_price_pay_only
                                     and (drogaraia <=> 0) < current_price_pay_only
                                     and (drogasil <=> 0) < current_price_pay_only
                                     and (onofre <=> 0) < current_price_pay_only
                                     and (drogariasp <=> 0) < current_price_pay_only
                                     and active = 1
                                     $comp
                                     and descontinuado != 1", false)->getResult()[0]->qtd;
    }

    public function getProductFields($skus, $fields = ['*']) {
        return $this->db->table('Products')
                        ->select($fields)
                        ->whereIn('sku', $skus)
                        ->get()->getResult();
    }

    public function getFieldsToMarginAndFat($skus) {
        return $this->db->table('Products')
                        ->select('sku as productCode, price_cost')
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
