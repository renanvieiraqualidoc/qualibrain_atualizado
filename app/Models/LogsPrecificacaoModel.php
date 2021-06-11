<?php namespace App\Models;

use CodeIgniter\Model;

class LogsPrecificacaoModel extends Model{

    // Função que recupera os logs de precificação
    public function getLogs($initial_date, $final_date, $status, $period, $initial_limit, $final_limit, $search) {
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
        $query = $cockpit->table('Log')->select('code, origin, sku, original_data, created_at');
        switch($period) {
            case "last_4_hours":
                $query->where('created_at >=', date('Y-m-d H:i', strtotime("-4 hours")));
                break;
            case "last_day":
                $query->where('created_at >=', date('Y-m-d', strtotime("-1 day")));
                break;
            case "last_7_days":
                $query->where('created_at >=', date('Y-m-d', strtotime("-7 days")));
                break;
            case "last_15_days":
                $query->where('created_at >=', date('Y-m-d', strtotime("-15 days")));
                break;
            case "last_30_days":
                $query->where('created_at >=', date('Y-m-d', strtotime("-30 days")));
                break;
            case "custom":
                if ($initial_date != "") $query->where('created_at >=', $initial_date);
                if ($final_date != "") $query->where('created_at <=', $final_date);
                break;
        }
        if ($search != '') $query->like('sku', $search);
        $query->orderBy('created_at', 'desc');
        $query->limit($final_limit, $initial_limit);
        $results = $query->get()->getResult();

        $query = $cockpit->table('Log')->select('count(1) as qtd');
        switch($period) {
            case "last_4_hours":
                $query->where('created_at >=', date('Y-m-d H:i', strtotime("-4 hours")));
                break;
            case "last_day":
                $query->where('created_at >=', date('Y-m-d', strtotime("-1 day")));
                break;
            case "last_7_days":
                $query->where('created_at >=', date('Y-m-d', strtotime("-7 days")));
                break;
            case "last_15_days":
                $query->where('created_at >=', date('Y-m-d', strtotime("-15 days")));
                break;
            case "last_30_days":
                $query->where('created_at >=', date('Y-m-d', strtotime("-30 days")));
                break;
            case "custom":
                if ($initial_date != "") $query->where('created_at >=', $initial_date);
                if ($final_date != "") $query->where('created_at <=', $final_date);
                break;
        }
        if ($search != '') $query->like('sku', $search);
        $query->orderBy('created_at', 'desc');
        $qtd = $query->get()->getResult()[0]->qtd;
        return json_encode(array('products' => $results,
                                 'qtd' => $qtd));
    }

    // Recupera as respostas dos JSON's de um log específico
    public function getResponse($code) {
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

        $results = $cockpit->table('Log')->select('original_data,
                                                   base_send_data,
                                                   variant_send_data,
                                                   base_result_data,
                                                   variant_result_data')->where('code', $code)->get()->getResult()[0];
        return json_encode(array('original_data' => json_decode($results->original_data),
                                 'base_send_data' => json_decode($results->base_send_data),
                                 'variant_send_data' => json_decode($results->variant_send_data),
                                 'base_result_data' => json_decode($results->base_result_data),
                                 'variant_result_data' => json_decode($results->variant_result_data)));
    }
}
