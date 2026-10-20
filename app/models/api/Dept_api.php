<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dept_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countDept($filters = [])
    {
		
        $this->db->from('dept');
        return $this->db->count_all_results();
    }

    
    
    public function getDept($filters = [])
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

        return $this->db->get('dept')->result();
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
