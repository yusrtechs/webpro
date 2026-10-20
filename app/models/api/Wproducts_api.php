<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wproducts_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countWproducts($filters = [])
    {
        /*if ($filters['category']) {
            $category = $this->getCategoryByCode($filters['category']);
            $this->db->where('category_id', $category->id);
        }*/
		$this->db->where('api', 0);

		if ($filters['warehouse_id']) {
            $this->db->where('warehouse_id', $filters['warehouse_id']);

        }
        /*
        if ($filters['brand']) {
            $brand = $this->getBrandByCode($filters['brand']);
            $this->db->where('brand', $brand->id);
        }*/
        $this->db->from('warehouses_products');
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
        if (!empty($products = $this->getWproducts($filters))) {
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

    public function getWproducts($filters = [])
    {
        $this->db->query("SET time_zone = '+03:00'");
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("{$this->db->dbprefix('products')}.id, {$this->db->dbprefix('products')}.code, {$this->db->dbprefix('products')}.name, {$this->db->dbprefix('products')}.type, {$this->db->dbprefix('products')}.slug, price, CONCAT('{$uploads_url}', {$this->db->dbprefix('products')}.image) as image_url, tax_method, tax_rate, unit");
		//$this->db->select("*");
		//$this->db->where('api', 0);
		if ($filters['biller_id'] && $filters['warehouse_id']) {

            if ($filters['pid']) {
                $myQuery ="select wp.id, wp.product_id, wp.warehouse_id, wp.quantity, wp.avg_cost, wp.updated_at  from " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and wp.warehouse_id='".$filters['warehouse_id']."'  and wp.product_id='".$filters['pid']."'  and pr.code not in (select sy.product_code from " . $this->db->dbprefix('synch_products') . " sy where sy.warehouse_id = '".$filters['warehouse_id']."' AND sy.biller_id = '".$filters['biller_id']."' and sy.api=1 )";
            }else{
                $myQuery ="select wp.id, wp.product_id, wp.warehouse_id, wp.quantity, wp.avg_cost, wp.updated_at  from " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('products') . " pr where wp.product_id=pr.id and wp.warehouse_id='".$filters['warehouse_id']."'    and pr.code not in (select sy.product_code from " . $this->db->dbprefix('synch_products') . " sy where sy.warehouse_id = '".$filters['warehouse_id']."' AND sy.biller_id = '".$filters['biller_id']."' and sy.api=1 )";
            }
        $q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
        return false;

        }else{


        $this->db->select("warehouses_products.id, warehouses_products.product_id, warehouses_products.warehouse_id, warehouses_products.quantity, warehouses_products.avg_cost, warehouses_products.updated_at");

        if ($filters['warehouse_id']) {
            $this->db->join('products', 'warehouses_products.product_id=products.id', 'left');
            $this->db->where("{$this->db->dbprefix('warehouses_products')}.warehouse_id", $filters['warehouse_id']);
            if ($filters['pid']) {
                $this->db->where("{$this->db->dbprefix('warehouses_products')}.product_id", $filters['pid']);
            }
            
            if ($filters['updated_at']) {
                $this->db->where("{$this->db->dbprefix('warehouses_products')}.updated_at > ", $filters['updated_at']);
            }

        }

        $this->db->query("SET time_zone = '+00:00'");
		return $this->db->get('warehouses_products')->result();

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
    public function getWproductsCount($filters = [])
    {
		$this->db->select('count(*) as count');
		if ($filters['warehouse_id']) {
        	$this->db->where('warehouse_id', $filters['warehouse_id']);
            if ($filters['pid']) {
                $this->db->where("product_id", $filters['pid']);
            }
            
            if ($filters['updated_at']) {
                $this->db->where("{$this->db->dbprefix('warehouses_products')}.updated_at > ", $filters['updated_at']);
            }
        }
		//$this->db->where('api', 0);


        return $this->db->get_where('warehouses_products')->row();
    }
}
