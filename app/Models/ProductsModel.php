<?php namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model{
    public function getProductsByDepartment($department) {
        $fields = ['sku','title', 'department', 'category', 'qty_stock_rms',
                   'qty_competitors_available', 'price_cost', 'current_price_pay_only',
                   'current_less_price_around', 'current_gross_margin_percent',
                   'diff_current_pay_only_lowest', 'curve', '0 as vendas_acumuladas'];
        $data = $this->db->table('Products')
                         ->select($fields)
                         ->where('diff_pay_only_lowest <', 0)
                         ->where('active', 1)
                         ->where('descontinuado !=', 1)
                         ->where('department', $department)
                         ->get()->getResult();
        return json_encode($data);
    }
}
