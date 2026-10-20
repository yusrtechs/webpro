<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Resturant_emp_types_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countResturant_emp_types($filters = [])
    {
		
        $this->db->from('resturant_emp_types');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('resturant_emp_types', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getResturant_emp_types($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getResturant_emp_types($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		
		

        if ($filters['code']) {
            $this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            //$this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('resturant_emp_types')->result();
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
