<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Product_variants_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countproduct_variants($filters = [])
    {

        $this->db->from('product_variants');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('product_variants', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getproduct_variants($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getproduct_variants($filters = [])
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

        return $this->db->get('product_variants')->result();
    }


    public function getproduct_variantsV2($filters = [])
    {


        $myQuery ="select * from " . $this->db->dbprefix('product_variants') . " where product_id in ( select product_id from " . $this->db->dbprefix('warehouses_products') . " where warehouse_id='".$filters['warehouse_id']."'  )";


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

    public function getproduct_variantsV3($filters = [])
    {


        $myQuery ="select product_variants.id,product_variants.product_id,product_variants.name,product_variants.price,warehouses_products_variants.quantity from " . $this->db->dbprefix('product_variants') . " product_variants, " . $this->db->dbprefix('warehouses_products_variants') . " warehouses_products_variants where product_variants.id=warehouses_products_variants.option_id and warehouses_products_variants.warehouse_id='".$filters['warehouse_id']."' and product_variants.product_id in ( select product_id from " . $this->db->dbprefix('warehouses_products') . " where warehouse_id='".$filters['warehouse_id']."'  )";


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

}
