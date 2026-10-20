<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Gl_payment_methods_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countPayment_type($filters = [])
    {
		if(isset($this->getSettings()[0]->preAccounting) && $this->getSettings()[0]->preAccounting==1)
              {

		$this->db->where('payment_type', 'CC');

        $this->db->from('gl_payment_methods');
        return $this->db->count_all_results();
			  }
			  return 0;
    }

    public function getPriceGroupByID($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id], 1)->row();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getPayment_type($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getPayment_type($filters = [])
    {

		if(isset($this->getSettings()[0]->preAccounting) && $this->getSettings()[0]->preAccounting==1)
              {
		$uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		//$this->db->where('api', 0);

		$this->db->where('payment_type', 'CC');


        if ($filters['code']) {
            //$this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('gl_payment_methods')->result();
			  }else{
			  return false;
			  }
    }

    public function getAccount_payment_type($payment_type)
    {

		if(isset($this->getSettings()[0]->preAccounting) && $this->getSettings()[0]->preAccounting==1)
              {
		$uploads_url = base_url('assets/uploads/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("*");
		//$this->db->where('api', 0);

		$this->db->where('payment_type', $payment_type);




        return $this->db->get('gl_payment_methods')->result();
			  }else{
			  return false;
			  }
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

    public function getFundAccountsBillers()
    {
        $myQuery ="SELECT gl_accounts.id as account_id,gl_accounts.accountName as account_name,companies.id as biller_id,companies.company as biller_company from " . $this->db->dbprefix('gl_accounts') . " gl_accounts," . $this->db->dbprefix('companies') . " companies where gl_accounts.account_ref_id=companies.id and gl_accounts.account_type='billers' and gl_accounts.account_ref_id!=0 order by companies.id";
        $q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
                return $q->result();
        }
        return false;

    }

}
