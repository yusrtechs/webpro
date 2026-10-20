<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Products_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countProducts($filters = [])
    {
        if ($filters['category']) {
            $category = $this->getCategoryByCode($filters['category']);
            $this->db->where('category_id', $category->id);
        }
		if ($filters['warehouse_id']) {
            $this->db->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left');
            $this->db->where("{$this->db->dbprefix('warehouses_products')}.warehouse_id", $filters['warehouse_id']);
        }

        if ($filters['brand']) {
            $brand = $this->getBrandByCode($filters['brand']);
            $this->db->where('brand', $brand->id);
        }


        $this->db->from('products');
        return $this->db->count_all_results();
    }

    public function getBrandByCode($code)
    {
        return $this->db->get_where('brands', ['code' => $code], 1)->row();
    }

    public function getBrandByID($id)
    {
        return $this->db->get_where('brands', ['id' => $id], 1)->row();
    }

    public function getCategoryByCode($code)
    {
        return $this->db->get_where('categories', ['code' => $code], 1)->row();
    }

    public function getCategoryByID($id)
    {
        return $this->db->get_where('categories', ['id' => $id], 1)->row();
    }

    public function getProduct($filters)
    {
        if (!empty($products = $this->getProducts($filters))) {
            return array_values($products)[0];
        }
        return false;
    }

    public function getProductPhotos($product_id)
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("CONCAT('{$uploads_url}', photo) as photo_url");
        return $this->db->get_where('product_photos', ['product_id' => $product_id])->result();
    }

    public function getProducts($filters = [])
    {

        $this->db->query("SET time_zone = '+03:00'");
		$uploads_url = base_url('assets/uploads/');
		if ($filters['biller_id'] && $filters['warehouse_id']) {

			/*$query1 = $this->db->query("select product_code from {$this->db->dbprefix('synch_products')} where warehouse_id = '".$filters['warehouse_id']."' AND biller_id = '".$filters['biller_id']."' and api=1");
            $query1_result = $query1->result();
            $product_code= array();
            foreach($query1_result as $row){
            $product_code[] = $row->product_code;
            }
            $pc = implode(",",$product_code);
            $pcs = explode(",", $pc);

			//$this->db->where_in('products.code', $pcs);
			$this->db->where_not_in('products.code', $pcs);
			*/

            if ($filters['short_fields']){
                $myQuery ="select pr.id,pr.code,pr.name,pr.unit,pr.price,pr.category_id,pr.subcategory_id,pr.warehouse,pr.tax_method,pr.tax_rate, pr.type,pr.sale_unit,pr.brand,pr.hide_pos,pr.minimum_selling_price,pr.second_name,pr.updated_at,CONCAT('{$uploads_url}', pr.image) as image_url,pr.image  from " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and wp.warehouse_id='".$filters['warehouse_id']."'    and trim(pr.code) not in (select trim(sy.product_code) from " . $this->db->dbprefix('synch_products') . " sy where sy.warehouse_id = '".$filters['warehouse_id']."' AND sy.biller_id = '".$filters['biller_id']."' and sy.api=1 )";
            }else{
                $myQuery ="select pr.*,CONCAT('{$uploads_url}', pr.image) as image_url  from " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and wp.warehouse_id='".$filters['warehouse_id']."'    and trim(pr.code) not in (select trim(sy.product_code) from " . $this->db->dbprefix('synch_products') . " sy where sy.warehouse_id = '".$filters['warehouse_id']."' AND sy.biller_id = '".$filters['biller_id']."' and sy.api=1 )";
            }

            if ($filters['product_code'])
                $myQuery .= " and pr.code=".$filters['product_code']." ";



            if ($filters['limit'] && $filters['start'])
                $myQuery .= " LIMIT ".$filters['limit']." OFFSET ".($filters['start'] - 1)." ";


            $q = $this->db->query($myQuery);
            //$this->db->query("SET time_zone = '+00:00'");
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
        return false;

        }else{


        //$this->db->select("{$this->db->dbprefix('products')}.id, {$this->db->dbprefix('products')}.code, {$this->db->dbprefix('products')}.name, {$this->db->dbprefix('products')}.type, {$this->db->dbprefix('products')}.slug, price, CONCAT('{$uploads_url}', {$this->db->dbprefix('products')}.image) as image_url, tax_method, tax_rate, unit");
		if ($filters['short_fields']){
            $this->db->select("products.id,products.code,products.name,products.unit,products.price,products.category_id,products.subcategory_id,products.warehouse,products.tax_method,products.tax_rate,products.type,products.sale_unit,products.brand,products.hide_pos,products.minimum_selling_price,products.second_name,products.updated_at,CONCAT('{$uploads_url}', image) as image_url,image");
        }else{
            $this->db->select("products.*,CONCAT('{$uploads_url}', image) as image_url");
        }


        /*if (!empty($filters['include'])) {
            foreach ($filters['include'] as $include) {
                if ($include == 'brand') {
                    $this->db->select('brand');
                } elseif ($include == 'category') {
                    $this->db->select('category_id as category');
                }
            }
        }

        if ($filters['category']) {
            $this->db->join('categories', 'categories.id=products.category_id', 'left');
            $this->db->where("{$this->db->dbprefix('categories')}.code", $filters['category']);
        }*/
        if ($filters['warehouse_id']) {
            $this->db->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left');
            $this->db->where("{$this->db->dbprefix('warehouses_products')}.warehouse_id", $filters['warehouse_id']);

        }

        if ($filters['product_code']){
            $this->db->where("products.code", $filters['product_code']);
        }


        if ($filters['updated_at']) {
            $this->db->where("products.updated_at > ", $filters['updated_at']);
        }


        /*if ($filters['brand']) {
            $this->db->join('brands', 'brands.id=products.brand', 'left');
            $this->db->where("{$this->db->dbprefix('brands')}.code", $filters['brand']);
        }
        if ($filters['code']) {
            $this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            //$this->db->limit($filters['limit'], ($filters['start'] - 1));
        }
		*/

		//$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
        if ($filters['limit'] && $filters['start'])
        $this->db->limit($filters['limit'], ($filters['start'] - 1));
         //     $this->db->insert('products_temp', $this->db->get('products')->result());

        //$this->db->query("SET time_zone = '+00:00'");
		return $this->db->get('products')->result();

		}
    }

    public function getProductUnit($id)
    {
        return $this->db->get_where('units', ['id' => $id], 1)->row();
    }

    public function getSubUnits($base_unit)
    {
        return $this->db->get_where('units', ['base_unit' => $base_unit])->result();
    }

    public function getTaxRateByID($id)
    {
        return $this->db->get_where('tax_rates', ['id' => $id], 1)->row();
    }
    public function getProductsCount($filters = [])
    {


		if ($filters['biller_id'] && $filters['warehouse_id']) {

			/*$query1 = $this->db->query("select product_code from {$this->db->dbprefix('synch_products')} where warehouse_id = '".$filters['warehouse_id']."' AND biller_id = '".$filters['biller_id']."' and api=1");
            $query1_result = $query1->result();
            $product_code= array();
            foreach($query1_result as $row){
            $product_code[] = $row->product_code;
            }
            $pc = implode(",",$product_code);
            $pcs = explode(",", $pc);

			//$this->db->where_in('products.code', $pcs);
			$this->db->where_not_in('products.code', $pcs);
			*/


			$myQuery ="select count(*) as count from " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and wp.warehouse_id='".$filters['warehouse_id']."'    and trim(pr.code) not in (select trim(sy.product_code) from " . $this->db->dbprefix('synch_products') . " sy where sy.warehouse_id = '".$filters['warehouse_id']."' AND sy.biller_id = '".$filters['biller_id']."' and sy.api=1 )";


        $q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $row;
            }
        //    return $data;
        }
        return false;

        }else{


		$this->db->select('count(*) as count');
		if ($filters['warehouse_id']) {
            $this->db->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left');
            $this->db->where("{$this->db->dbprefix('warehouses_products')}.warehouse_id", $filters['warehouse_id']);

        }

        if ($filters['product_code']){
            $this->db->where("products.code", $filters['product_code']);
        }

        if ($filters['updated_at']) {
            $this->db->where("products.updated_at > ", $filters['updated_at']);
        }


            //$this->db->join('synch_products', 'synch_products.product_code=products.code', 'left');
            //$this->db->where("{$this->db->dbprefix('synch_products')}.warehouse_id", $filters['warehouse_id']);
			//$this->db->where("{$this->db->dbprefix('synch_products')}.biller_id", $filters['biller_id']);
			//$this->db->where("{$this->db->dbprefix('synch_products')}.api !=", 1);



        return $this->db->get('products')->row();
		}



    }
}
