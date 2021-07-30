<?php namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model{
    // Função que efetua o tagueamento de um SKU
    public function tagSku($sku, $action) {
        if (!$this->db->simpleQuery("UPDATE Products SET acao = '$action' WHERE sku = '$sku'")) $response = array('msg' => "Não foi possível (des)taguear o SKU $sku.", 'success' => false);
        else $response = array('msg' => "Produto precificado com sucesso!", 'success' => true);
        return json_encode($response);
    }
}
