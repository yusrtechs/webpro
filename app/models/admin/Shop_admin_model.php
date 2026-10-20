<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shop_admin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addPage($data)
    {
        if ($this->db->insert('pages', $data)) {
            return true;
        }
        return false;
    }

    public function deletePage($id)
    {
        if ($this->db->delete('pages', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getAllBrands()
    {
        return $this->db->get('brands')->result();
    }

    public function getAllCategories()
    {
        $this->db->where('parent_id', null)->or_where('parent_id', 0)->order_by('name');
        return $this->db->get('categories')->result();
    }

    public function getAllPages()
    {
        $q = $this->db->get('pages');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllProducts()
    {
        $this->db->select('id, LOWER(name) as name, slug');
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPageByID($id)
    {
        $q = $this->db->get_where('pages', ['id' => $id]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPageBySlug($slug)
    {
        $q = $this->db->get_where('pages', ['slug' => $slug]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getShopSettings()
    {
        $q = $this->db->get('shop_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSubCategories($parent_id)
    {
        $this->db->where('parent_id', $parent_id)->order_by('name');
        return $this->db->get('categories')->result();
    }

    public function updatePage($id, $data)
    {
        if ($this->db->update('pages', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateShopSettings($data)
    {
        if ($this->db->update('shop_settings', $data, ['id' => 1])) {
            return true;
        }
        return false;
    }

    public function updateSlider($data)
    {
        if ($this->db->update('shop_settings', ['slider' => json_encode($data)], ['shop_id' => 1])) {
            return true;
        }
        return false;
    }

    public function updateSmsSettings($data)
    {
        if ($this->db->update('sms_settings', $data, ['id' => 1])) {
            return true;
        }
        return false;
    }

    public function getSyncSettings()
    {
            $company=str_replace("cpesoft_","",$this->db->database);
            $otherdb = $this->load->database('info', true);
            $check = $otherdb->where('company', $company)->get('client');


        if ($check->num_rows() > 0) {
            return $check->row();
        }
        return false;
    }

    public function getSyncSettingsApi($client_id)
    {

            $otherdb = $this->load->database('info', true);
            $check = $otherdb->where('client_id', $client_id)->get('shop_api_keys');


        if ($check->num_rows() > 0) {
            return $check->row();
        }
        return false;
    }

    public function getCategories()
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*, CONCAT('{$uploads_url}', image) as image_ul")->limit(100);
        $this->db->where('shop_id', null);

        $this->db->where(" (parent_id =  0 OR parent_id is null) ");
        return $this->db->get('categories')->result();
    }

    public function getCategoriesCount()
    {

        $this->db->select("COALESCE(count(*),0) as total");
        $this->db->where('shop_id', null);
        $this->db->where(" (parent_id =  0 OR parent_id is null) ");
        return $this->db->get('categories')->row();
    }


    public function getProducts()
    {
        $uploads_url = base_url('assets/uploads/');

        $this->db->select("products.*, categories.shop_id as cat_shop_id")->limit(100);
        $this->db->join('categories', 'categories.id=products.category_id', 'left');
        $this->db->where('products.shop_id', null);
        $this->db->where('categories.shop_id !=', null);
        //$this->db->where('products.code', '6281014402101');



        return $this->db->get('products')->result();
    }

    public function getProductsCount()
    {

        $this->db->select("COALESCE(count(*),0) as total");
        $this->db->join('categories', 'categories.id=products.category_id', 'left');
        $this->db->where('products.shop_id', null);
        $this->db->where('categories.shop_id !=', null);
        //$this->db->where('products.code', '6281014402101');
        return $this->db->get('products')->row();
    }

    public function getProductByShopID($shop_id)
    {
        $q = $this->db->get_where('products', ['shop_id' => $shop_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateSyncSettings($date)
    {
            $company=str_replace("cpesoft_","",$this->db->database);
            $otherdb = $this->load->database('info', true);
            return $otherdb->query('update client set latest_sync_order="'.$date.'" where company="'.$company.'";');
           // return $this->otherdb->update('client', ['latest_sync_order' => $date], ['company' => $company]);



    }

    public function updateCategory($id, $data = [])
    {
        if ($this->db->update('categories', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateProducts($id, $data = [])
    {
        if ($this->db->update('products', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }
}
