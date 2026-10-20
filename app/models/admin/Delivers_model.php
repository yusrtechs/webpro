<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Delivers_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addDeliver($data = [], $items = [])
    {
        if ($this->db->insert('delivers', $data)) {
            $deliver_id = $this->db->insert_id();
            if ($this->site->getReference('do') == $data['reference_no']) {
                $this->site->updateReference('do');
            }
            foreach ($items as $item) {
                $item['deliver_id'] = $deliver_id;
                $this->db->insert('deliver_items', $item);
            }
            return true;
        }
        return false;
    }

    public function getDeliverByID($id)
    {
        $q = $this->db->get_where('delivers', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllDeliverItemsWithDetails($deliver_id)
    {
        $this->db->select('deliver_items.id, deliver_items.product_name, deliver_items.product_code, deliver_items.quantity, deliver_items.serial_no, deliver_items.tax, deliver_items.unit_price, deliver_items.val_tax, deliver_items.discount_val, deliver_items.gross_total, products.details, products.hsn_code as hsn_code, products.second_name as second_name');
        $this->db->join('products', 'products.id=deliver_items.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('deliver_items', ['deliver_id' => $deliver_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getAllDeliverItems($deliver_id)
    {
        $this->db->select('deliver_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.image, products.details as details, products.cf1 as cf1, products.cf2 as cf2, products.cf3 as cf3, products.cf4 as cf4, products.cf5 as cf5, products.cf6 as cf6, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('products', 'products.id=deliver_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=deliver_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=deliver_items.tax_rate_id', 'left')
            ->group_by('deliver_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('deliver_items', ['deliver_id' => $deliver_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function updateDeliver($id, $data, $items = [])
    {
        if ($this->db->update('delivers', $data, ['id' => $id]) && $this->db->delete('deliver_items', ['deliver_id' => $id])) {
            foreach ($items as $item) {
                $item['deliver_id'] = $id;
                $this->db->insert('deliver_items', $item);
            }
            return true;
        }
        return false;
    }

    public function deleteDeliver($id)
    {
        if ($this->db->delete('deliver_items', ['deliver_id' => $id]) && $this->db->delete('delivers', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getDeliverByReference($ref)
    {
        $this->db->where('reference_no', $ref);
        $q = $this->db->get('delivers', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }





}
