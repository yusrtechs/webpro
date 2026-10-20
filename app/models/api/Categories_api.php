<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Categories_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countCategories($filters = [])
    {
		$this->db->where('api', 0);

        $this->db->from('categories');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getCategories($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getCategories($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		/*$this->db->select("*, CONCAT('{$uploads_url}', image) as image_ul");
		$this->db->where('api', 0);


z
        if ($filters['code']) {
            $this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('categories')->result();
		*/
			//$myQuery ="select cat.*,CONCAT('{$uploads_url}', cat.image) as image_ul  from " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('categories') . " cat," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and pr.category_id=cat.id  and wp.warehouse_id='".$filters['warehouse_id']."'    and cat.id  in (select pr.category_id from " . $this->db->dbprefix('products') . " pr where sy.warehouse_id = '".$filters['warehouse_id']."' AND sy.biller_id = '".$filters['biller_id']."' and sy.api=1 )";
				//$myQuery ="select *, CONCAT('{$uploads_url}', image) as image_url  from " . $this->db->dbprefix('categories') . "     where id in (select pr.category_id from  " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and wp.warehouse_id='".$filters['warehouse_id']."')";

                if ($filters['cat_type']=='all') {
                    $this->db->select("*, CONCAT('{$uploads_url}', image) as image_url");
                    return $this->db->get('categories')->result();
                } else {
                $myQuery ="select *, CONCAT('{$uploads_url}', image) as image_url  from " . $this->db->dbprefix('categories') . "     where id in (select  pr.category_id from  " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and wp.warehouse_id='".$filters['warehouse_id']."') or id in (select  pr.subcategory_id from  " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and wp.warehouse_id='".$filters['warehouse_id']."')";

        $q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
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
