<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchases_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countPurchases($filters = [], $ref = null)
    {
        if ($filters['supplier_id']) {
            $this->db->where('supplier_id', $filters['supplier_id']);
        }
        if ($filters['start_date']) {
            $this->db->where('date >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $this->db->where('date <=', $filters['end_date']);
        }
        $this->db->from('purchases');
        return $this->db->count_all_results();
    }

    public function getProductVariantByID($id)
    {
        return $this->db->get_where('product_variants', ['id' => $id], 1)->row();
    }

    public function getPurchase($filters)
    {
        if (!empty($purchases = $this->getPurchases($filters))) {
            return array_values($purchases)[0];
        }
        return false;
    }

    public function getPurchaseItems($purchase_id)
    {
        return $this->db->get_where('purchase_items', ['purchase_id' => $purchase_id])->result();
    }

    public function getPurchases($filters = [])
    {
        if ($filters['supplier_id']) {
            $this->db->where('supplier_id', $filters['supplier_id']);
        }
        if ($filters['start_date']) {
            $this->db->where('date >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $this->db->where('date <=', $filters['end_date']);
        }
        if ($filters['reference']) {
            $this->db->where('reference_no', $filters['reference']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'desc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('purchases')->result();
    }

    public function getUser($id)
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("CONCAT('{$uploads_url}', avatar) as avatar_url, email, first_name, gender, id, last_name, username");
        return $this->db->get_where('users', ['id' => $id], 1)->row();
    }

    public function getWarehouseByID($id)
    {
        return $this->db->get_where('warehouses', ['id' => $id], 1)->row();
    }

    public function getPurchasesReportAPP($start_date,$end_date,$warehouse_id = null)
    {
        $this->db
                ->select('' . $this->db->dbprefix('purchases') . '.date, reference_no, ' . $this->db->dbprefix('warehouses') . '.name as wname, supplier, GROUP_CONCAT(CONCAT(' . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '\n') as iname, (total-total_discount) as total, total_tax,(" . $this->db->dbprefix('purchases') . ".shipping+customs_fees+other_fees) as expenses, grand_total, " . $this->db->dbprefix('purchases') . '.status', false)
                ->from('purchases')
                ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
                ->group_by('purchases.id')
                ->order_by('purchases.date desc');
        if ($warehouse_id){
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }
         $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }


		return $data;
    }

    public function getExpensesReportAPP($start_date,$end_date,$warehouse_id = null)
    {
        $this->db
                ->select("date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)
            ->from('expenses')
            ->join('users', 'users.id=expenses.created_by', 'left')
            ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
            ->group_by('expenses.id');
        if ($warehouse_id){
            $this->db->where('expenses.warehouse_id', $warehouse_id);
        }
         $this->db->where('expenses.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }


		return $data;
    }
}
