<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Users_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countUsers($filters = [])
    {

		$this->db->where('api', 0);

		if ($filters['warehouse_id']) {
            $this->db->where('warehouse_id', $filters['warehouse_id']);

        }
        if ($filters['id']) {
            $this->db->where('id', $filters['id']);

        }

		$this->db->from('users');
        return $this->db->count_all_results();
    }

	public function countPermissions($filters = [])
    {
		//$this->db->where('api', 0);

        $this->db->from('permissions');
        return $this->db->count_all_results();
    }


    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getUsers($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

	public function getBillerIdByUserName($username)
    {
        $this->db->select("biller_id");
		return $this->db->get_where('users', ['username' => $username], 1)->row()->biller_id;
    }

	public function getWarehouseIdByUserId($id)
    {
        $this->db->select("warehouse_id");
		return $this->db->get_where('users', ['id' => $id], 1)->row()->warehouse_id;
    }

    public function getBillerIdByUserId($id)
    {
        $this->db->select("biller_id");
		return $this->db->get_where('users', ['id' => $id], 1)->row()->biller_id;
    }


    public function getPermissions($filters = [])
    {
        $this->db->select("id,group_id,IFNULL(`sales-reports`,0) as sales_reports,IFNULL(`sales-register_report`,0) as reports_register,IFNULL(`pos-return`,0) as pos_return,IFNULL(`accounts-dashboard_acc`,0) as pos_dashboard");




        return $this->db->get('permissions')->result();
    }

    public function getUsers($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		$this->db->where('api', 0);


        /*if ($filters['code']) {
            $this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }*/

		if ($filters['warehouse_id']) {
			$this->db->where('warehouse_id', $filters['warehouse_id']);
        }
        if ($filters['id']) {
            $this->db->where('id', $filters['id']);

        }


        return $this->db->get('users')->result();
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
