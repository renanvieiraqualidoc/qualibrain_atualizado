<?php namespace App\Models;

use CodeIgniter\Model;

class PrecificacaoModel extends Model{

    // Função que efetua a precificação de um SKU
    public function precifySku($sku, $status, $department, $category) {
        $custom = [
          'DSN'      => '',
          'hostname' => $this->db->hostname,
          'username' => $this->db->username,
          'password' => $this->db->password,
          'database' => 'cockpit',
          'DBDriver' => 'MySQLi',
          'DBPrefix' => '',
          'pConnect' => false,
          'DBDebug'  => (ENVIRONMENT !== 'production'),
          'charset'  => 'utf8',
          'DBCollat' => 'utf8_general_ci',
          'swapPre'  => '',
          'encrypt'  => false,
          'compress' => false,
          'strictOn' => false,
          'failover' => [],
          'port'     => 3306,
        ];
        $cockpit = \Config\Database::connect($custom);
        $response = array();
        if($status == 'Novo') { // SKU novo
            if (!$cockpit->simpleQuery("INSERT INTO Products(sku, title) VALUES ('$sku', '')")) {
                $response = array('msg' => "Não foi possível salvar o SKU $sku.", 'success' => false);
            }
            else {
                if (!$cockpit->simpleQuery("INSERT INTO ProductsRules (products_code_fk) SELECT code FROM Products WHERE sku='$sku'")) {
                    $response = array('msg' => "Não foi possível salvar a regra para o SKU $sku.", 'success' => false);
                }
                else {
                    $code = $cockpit->query("SELECT code FROM ProductsRules WHERE rules_code_fk = ''", false)->getResult()[0]->code;
                    if (!$code) {
                        $response = array('msg' => "Não foi possível salvar o código do sku $sku nas regras.", 'success' => false);
                    }
                    else {
                        /* Codificação dos itens
                        2 - Perfumaria
                        3 - Medicamento
                        7 - Dermocosmetico */
                        if($department == 200 && $category == 202) $id_rule = 7;
                        else if($department == 100) $id_rule = 3;
                        else if($department == 200) $id_rule = 2;
                        if (!$cockpit->simpleQuery("UPDATE ProductsRules SET rules_code_fk = $id_rule where rules_code_fk = 0")) {
                            $response = array('msg' => "Produto não se enquadra em nenhuma regra de precificação", 'success' => false);
                        }
                        else {
                            if(trim(shell_exec('/usr/bin/curl '.'"http://127.0.0.1:8080/pricer/change/?sku='."'$sku'".'" 2>&1')) == "") $response = array('msg' => "Produto precificado com sucesso!", 'success' => true);
                            else $response = array('msg' => "Não foi possível precificar seu SKU", 'success' => false);
                        }
                    }
                }
            }
        }
        else { // SKU de reativação
            if(trim(shell_exec('/usr/bin/curl '.'"http://127.0.0.1:8080/pricer/change/?sku='."'$sku'".'" 2>&1')) == "") $response = array('msg' => "Produto precificado com sucesso!", 'success' => true);
            else $response = array('msg' => "Não foi possível precificar seu SKU", 'success' => false);
        }
        return json_encode($response);
    }
}
