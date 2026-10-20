<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Resturant_tables_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countResturant_tables($filters = [])
    {

        $this->db->from('resturant_tables');
        return $this->db->count_all_results();
    }



    public function getResturant_tables($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("id, code, name, dept, isActive, , CONCAT('{$uploads_url}', table_icon) as table_icon");
		//$this->db->select("*");



        if ($filters['code']) {
            $this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            //$this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('resturant_tables')->result();
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
