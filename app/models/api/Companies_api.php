<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Companies_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countCompanies($filters = [])
    {
        //$this->db->where('api', 0);

		if ($filters['groupc'] && $filters['groupb']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where("(group_name='biller' or group_name='customer'  or group_name='salesman')", NULL, FALSE);
        }else if ($filters['groups']) {
            $this->db->where("(group_name='supplier')", NULL, FALSE);
        }else if ($filters['groupsa']) {
            $this->db->where("(group_name='salesman')", NULL, FALSE);
        }
		$this->db->from('companies');
        return $this->db->count_all_results();
    }
    public function countCompaniesTailor($filters = [])
    {
        //$this->db->where('api', 0);

		if ($filters['groupc'] && $filters['groupb']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (group_name='biller' or group_name='customer'  or group_name='salesman')", NULL, FALSE);
        }else{

			if ($filters['groupc']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (group_name='customer')", NULL, FALSE);
        }
        if ($filters['groupb']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (group_name='biller')", NULL, FALSE);
        }
        if ($filters['groups']) {
            $this->db->where("(group_name='supplier')", NULL, FALSE);
        }
        if ($filters['groupsa']) {
            $this->db->where("(group_name='salesman')", NULL, FALSE);
        }

		}
		    //$this->db->where("group_name='customer' or group_name='biller'");
		if ($filters['id']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (id=".$filters['id']." )", NULL, FALSE);
        }
		    //$this->db->where("group_name='customer' or group_name='biller'");
            if ($filters['Offline_id']) {
                //$this->db->where('group_name=', 'customer OR group_name=biller' );
                //$this->db->where('group_name=', 'customer', FALSE)
                $this->db->where(" (Offline_id=".$filters['Offline_id']." )", NULL, FALSE);
            }

        if ($filters['vat_no']) {
                $this->db->where(" (vat_no=".$filters['vat_no']." )", NULL, FALSE);
        }
        if ($filters['cr_no']) {
            $this->db->where(" (cr_no=".$filters['cr_no']." )", NULL, FALSE);
        }
        if ($filters['phone']) {
            $this->db->where(" (phone=".$filters['phone']." )", NULL, FALSE);
        }



        if ($filters['name']) {
           // $this->db->where('name', $filters['name']);
        //   $this->db->where("name='".$filters['name']."' or company='".$filters['name']."'");
           $this->db->where("company='".$filters['name']."'");
        } else {
            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
        //    $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }
		$this->db->from('companies');
        return $this->db->count_all_results();
    }

    public function getCompanies($filters = [])
    {
        $uploads_url = base_url('assets/uploads/logos/');
        // $this->db->select("id, code, name, type, slug, price, CONCAT('{$uploads_url}', image) as image_url, tax_method, tax_rate, unit");
		$this->db->select("*,CONCAT('{$uploads_url}', logo) as logo_image_url");
		//$this->db->where('api', 0);

        if (!empty($filters['include'])) {
            foreach ($filters['include'] as $include) {
                if ($include == 'customer') {
                    $this->db->select('brand');
                } elseif ($include == 'biller') {
                    $this->db->select('category_id as category');
                }
            }
        }


		if ($filters['groupc'] && $filters['groupb']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (group_name='biller' or group_name='customer'  or group_name='salesman')", NULL, FALSE);
        }else if ($filters['groups']) {
            $this->db->where("(group_name='supplier')", NULL, FALSE);
        }else if ($filters['groupsa']) {
            $this->db->where("(group_name='salesman')", NULL, FALSE);
        }
		    //$this->db->where("group_name='customer' or group_name='biller'");
		if ($filters['id']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (id=".$filters['id']." )", NULL, FALSE);
        }
		    //$this->db->where("group_name='customer' or group_name='biller'");

            if ($filters['Offline_id']) {
                //$this->db->where('group_name=', 'customer OR group_name=biller' );
                //$this->db->where('group_name=', 'customer', FALSE)
                $this->db->where(" (Offline_id=".$filters['Offline_id']." )", NULL, FALSE);
            }



        if ($filters['name']) {
            $this->db->where('name', $filters['name']);
        } else {
            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
        //    $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }
		$this->db->order_by('id', 'desc');

        return $this->db->get('companies')->result();
    }


	public function getCompaniesTailor($filters = [])
    {
        $uploads_url = base_url('assets/uploads/logos/');
        // $this->db->select("id, code, name, type, slug, price, CONCAT('{$uploads_url}', image) as image_url, tax_method, tax_rate, unit");
		if ($filters['short_fields'] && $filters['groupc']){
            $this->db->select("`id`, `group_id`, `group_name`, `customer_group_id`, `customer_group_name`, `name`, `company`, `vat_no`, `cr_no`, `phone`, `email`, `city`, `state`, `country`, `cf1`, `cf2`, `cf3`, `cf4`, `cf5`, `cf6`, `code`, `building_no`, `additional_no`, `street_name`, `postal_code`, `district_name`, `address`, `award_points`, `deposit_amount`, `price_group_id`, `price_group_name`, `co_type`, `hide_in_pos`,`subscription_duration`, `start_subscription`, `end_subscription`");
        }else{
            $this->db->select("*,CONCAT('{$uploads_url}', logo) as logo_image_url");
        }
		//$this->db->where('api', 0);

        if (!empty($filters['include'])) {
            foreach ($filters['include'] as $include) {
                if ($include == 'customer') {
                    $this->db->select('brand');
                } elseif ($include == 'biller') {
                    $this->db->select('category_id as category');
                }
            }
        }


		if ($filters['groupc'] && $filters['groupb']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (group_name='biller' or group_name='customer'  or group_name='salesman')", NULL, FALSE);
        }else{

			if ($filters['groupc']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (group_name='customer')", NULL, FALSE);
        }

        if ($filters['groupb']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (group_name='biller')", NULL, FALSE);
        }

        if ($filters['groups']) {
            $this->db->where("(group_name='supplier')", NULL, FALSE);
        }

        if ($filters['groupsa']) {
            $this->db->where("(group_name='salesman')", NULL, FALSE);
        }

		}
		    //$this->db->where("group_name='customer' or group_name='biller'");
		if ($filters['id']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (id=".$filters['id']." )", NULL, FALSE);
        }

        if ($filters['Offline_id']) {
            //$this->db->where('group_name=', 'customer OR group_name=biller' );
			//$this->db->where('group_name=', 'customer', FALSE)
            $this->db->where(" (Offline_id=".$filters['Offline_id']." )", NULL, FALSE);
        }
		    //$this->db->where("group_name='customer' or group_name='biller'");

        if ($filters['vat_no']) {
                $this->db->where(" (vat_no=".$filters['vat_no']." )", NULL, FALSE);
        }
        if ($filters['cr_no']) {
            $this->db->where(" (cr_no=".$filters['cr_no']." )", NULL, FALSE);
        }
        if ($filters['phone']) {
            $this->db->where(" (phone=".$filters['phone']." )", NULL, FALSE);
        }




        if ($filters['name']) {
           $this->db->where("company='".$filters['name']."'");
        }
		$this->db->order_by('id', 'desc');

        return $this->db->get('companies')->result();
    }

    public function getCompany($filters)
    {
        if (!empty($companies = $this->getCompanies($filters))) {
            return array_values($companies)[0];
        }
        return false;
    }

    public function getCompanyUsers($company_id)
    {
        return $this->db->get_where('users', ['company_id' => $company_id])->result();
    }

    public function getDeposits($company_id)
    {
        return $this->db->get_where('deposits', ['company_id' => $company_id])->result();
    }

    public function getDepositsCount($company_id)
    {
        $this->db->where(['company_id' => $company_id]);
        $this->db->from('deposits');
        return $this->db->count_all_results();
    }

    public function getCustomerSubscriptions($customer_id)
    {
        return $this->db->get_where('customer_subscriptions', ['customer_id' => $customer_id])->result();
    }

    public function getCustomerSubscriptionsCount($customer_id)
    {
        $this->db->where(['customer_id' => $customer_id]);
        $this->db->from('customer_subscriptions');
        return $this->db->count_all_results();
    }
}
