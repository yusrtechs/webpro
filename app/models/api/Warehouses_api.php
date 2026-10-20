<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Warehouses_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countWarehouses($filters = [])
    {
		if ($filters['warehouse_code']) {
            $this->db->where('code', $filters['warehouse_code']);
        }
		$this->db->where('api', 0);


        $this->db->from('warehouses');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouseIdByCode($code)
    {
        $this->db->select("id");
		return $this->db->get_where('warehouses', ['code' => $code], 1)->row()->id;
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getWarehouses($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getWarehouses($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, CONCAT('{$uploads_url}', warehouse_logo) as warehouse_logo_url, price_group_id,vat_reg_no,cr_no,isTobacco,isRestaurant,branch_name");
		$this->db->where('api', 0);


        if ($filters['warehouse_code']) {
            $this->db->where('code', $filters['warehouse_code']);
        } else {
            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            //$this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('warehouses')->result();
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
