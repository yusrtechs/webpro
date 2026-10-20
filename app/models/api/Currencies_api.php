<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Currencies_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countCurrencies($filters = [])
    {

        return $this->db->count_all_results();
    }



    public function getCurrencies($filters = [])
    {
        $uploads_url = base_url('assets/images/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*, CONCAT('{$uploads_url}', symbol_logo) as symbol_logo_url");
		//$this->db->where('api', 0);
		//$this->db->where('tax_active', 1);



        /*if ($filters['code']) {
            $this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }*/

        return $this->db->get('currencies')->result();
    }

}
