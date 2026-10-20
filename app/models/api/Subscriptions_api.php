<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Subscriptions_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countSubscriptions($filters = [])
    {
        //$this->db->where('api', 0);
		//$this->db->where('tax_active', 1);
		if ($filters['card_no']) {
            //$this->db->where('card_no', $filters['card_no']);
        }
		$this->db->from('subscriptions');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getTaxrates($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getSubscriptions($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		//$this->db->where('api', 0);
		//$this->db->where('tax_active', 1);



        if ($filters['card_no']) {
            //$this->db->where('card_no', $filters['card_no']);
        }
			/*else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }*/

        return $this->db->get('subscriptions')->result();
    }

public function addWarehouse_temp($data = [])
    {
        if ($this->db->insert('warehouses_temp', $data)) {
		//if ($this->db->insert('warehouses_temp', $data)) {
            return true;
        }
        return false;
    }

}
