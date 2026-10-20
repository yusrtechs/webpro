<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Modifiers_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countModifiers($filters = [])
    {
		//$this->db->where('api', 0);

        $this->db->from('modifiers');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getModifiers($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getModifiers($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*, CONCAT('{$uploads_url}', image) as image_url");
		//$this->db->where('api', 0);



        if ($filters['code']) {
            //$this->db->where('code', $filters['code']);
        } else {
            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            //$this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('modifiers')->result();
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
