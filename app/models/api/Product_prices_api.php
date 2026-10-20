<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Product_prices_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countProduct_prices($filters = [])
    {

        $this->db->from('product_prices');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('product_prices', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getProduct_prices($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getProduct_prices($filters = [])
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

        return $this->db->get('product_prices')->result();
    }


    public function getProduct_pricesV2($filters = [])
    {



			$myQuery ="select * from " . $this->db->dbprefix('product_prices') . " where product_id in ( select product_id from " . $this->db->dbprefix('warehouses_products') . " where warehouse_id='".$filters['warehouse_id']."'  )";


		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;




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
