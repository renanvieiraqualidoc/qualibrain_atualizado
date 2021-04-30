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
}
