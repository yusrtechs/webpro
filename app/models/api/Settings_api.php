<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countSettings($filters = [])
    {
		$this->db->where('api', 0);

        $this->db->from('settings');
        return $this->db->count_all_results();
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getSettings($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getSettings($filters = [])
    {
        $uploads_url = base_url('assets/uploads/logos/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*,CONCAT('{$uploads_url}', logo) as logo_image_url");
		$this->db->where('api', 0);


        /*if ($filters['code']) {
            $this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }*/

        return $this->db->get('settings')->result();
    }

    public function addWarehouse_temp($data = [])
    {
        if ($this->db->insert('warehouses_temp', $data)) {
		//if ($this->db->insert('warehouses_temp', $data)) {
            return true;
        }
        return false;
    }

    public function getWhatsappSettings()
    {
        if($this->db->get('whatsapp_settings')){
            $this->db->select("config");
            $q = $this->db->get('whatsapp_settings');
            if ($q->num_rows() > 0) {
                return $q->row();
            }
            return false;
        }else{
            return false;
        }
    }
    
    public function isPOSWhatsappSettings()
    {
        if($this->db->get('whatsapp_settings')){
            $this->db->where('pos', 1);
            $q = $this->db->get('whatsapp_settings');
            if ($q->num_rows() > 0) {
                return true;
            }
            return false;
        }else{
            return false;
        }
    }

    public function getSmsSettings()
    {
        if($this->db->get('sms_settings')){
            $this->db->select("config");
            $q = $this->db->get('sms_settings');
            if ($q->num_rows() > 0) {
                return $q->row();
            }
            return false;
        }else{
            return false;
        }
    }
    
    public function getWhiteList($filters = [])
    {
        $this->db->select("remoteJid");
        return $this->db->get('settings')->result();
    }

}
