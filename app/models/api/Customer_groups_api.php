<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customer_groups_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countCustomer_groups($filters = [])
    {
		//$this->db->where('api', 0);

        $this->db->from('customer_groups');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getCustomer_groups($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getCustomer_groups($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		//$this->db->where('api', 0);





        return $this->db->get('customer_groups')->result();
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
