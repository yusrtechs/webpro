<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Promo_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countPromo($filters = [])
    {
		$this->db->where('hide_opt!=', 2);
		if ($filters['warehouse_id']) {
		//$this->db->where('warehouse_id', $filters['warehouse_id'])->or_where('warehouse_id IS NULL');
		}

        $this->db->from('promo');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getUnits($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getPromo($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		$this->db->where('hide_opt!=', 2);

		if ($filters['warehouse_id']) {
		//$this->db->where('warehouse_id', $filters['warehouse_id'])->or_where('warehouse_id IS NULL');
		}


            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
           // $this->db->limit($filters['limit'], ($filters['start'] - 1));


        return $this->db->get('promo')->result();
    }



    public function getPromoDetails($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		//$this->db->where('api', 0);




            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
           // $this->db->limit($filters['limit'], ($filters['start'] - 1));


        return $this->db->get('promo_details')->result();
    }


    public function getPromoDetailsBypromo_id($promo_id = null)
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		//$this->db->where('api', 0);

        if($promo_id){
            $this->db->where('promo_id', $promo_id);

        }




            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
           // $this->db->limit($filters['limit'], ($filters['start'] - 1));


        return $this->db->get('promo_details')->result();
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
