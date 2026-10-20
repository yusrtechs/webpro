<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Companies_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addAddress($data)
    {
        if ($this->db->insert('addresses', $data)) {
            return true;
        }
        return false;
    }

    public function addCompanies($data = [])
    {
        if ($this->db->insert_batch('companies', $data)) {
            return true;
        }
        return false;
    }

    public function addCompaniesV2($data = [])
    {


        foreach ($data as $cust) {
            $this->addCompany($cust);
        }

        return true;
    }

    public function addCompany($data = [])
    {
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {
            $group_settings=$this->site->getGroupSettings();
            if($data['group_name']=="customer"){

                if($group_settings->auto_share_customers==1){
                    $branches = $this->site->getMainBranches();
                    foreach ($branches as $branch) {
                        $data_customers_details = [
                            'customer_id'            => $cid,
                            'grpId'            => $data ['grpId'],
                            'coId'            => $data ['coId'],
                            'brId'            => $branch->id,
                        ];

                        if ($this->db->insert('customers_details', $data_customers_details)) {
                        }
                    }
                }else{
                    $data_customers_details = [
                        'customer_id'            => $cid,
                        'grpId'            => $data ['grpId'],
                        'coId'            => $data ['coId'],
                        'brId'            => $data ['brId'],
                    ];

                    if ($this->db->insert('customers_details', $data_customers_details)) {
                    }
                }

            }
            if($data['group_name']=="supplier"){

                if($group_settings->auto_share_suppliers==1){
                    $branches = $this->site->getMainBranches();
                    foreach ($branches as $branch) {
                        $data_suppliers_details = [
                            'supplier_id'            => $cid,
                            'grpId'            => $data ['grpId'],
                            'coId'            => $data ['coId'],
                            'brId'            => $branch->id,
                        ];

                        if ($this->db->insert('suppliers_details', $data_suppliers_details)) {
                        }
                    }
                }else{

                    $data_suppliers_details = [
                        'supplier_id'            => $cid,
                        'grpId'            => $data ['grpId'],
                        'coId'            => $data ['coId'],
                        'brId'            => $data ['brId'],
                    ];

                    if ($this->db->insert('suppliers_details', $data_suppliers_details)) {
                    }
                }

            }

        }

            if(isset($data['is_auto_account']) && $data['is_auto_account']==1){
                $account_type = "";
                $accountSide = "";
                $accountStatement = "";

                if($data['group_name']=="customer"){


            if($data['co_type']==1){
                $id=$this->site->getAccountsSettings()->accounts_local_customers_acct;
                $account_type = "local_customers";
            }else if($data['co_type']==2){
                $id=$this->site->getAccountsSettings()->accounts_international_customers_acct;
                $account_type = "international_customers";
            }

            $accountSide = "Debit";
            $accountStatement = "Balance Sheet";


                } else if($data['group_name']=="supplier"){


                    if($data['co_type']==1){
                        $id=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
                        $account_type = "local_suppliers";
                    }else if($data['co_type']==2){
                        $id=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
                        $account_type = "international_suppliers";
                    }

                    $accountSide = "Credit";
                    $accountStatement = "Balance Sheet";


                        }







        $account_count = $this->site->getCountAccountByID($id);
		$account_details = $this->site->getAccountByID($id);
		if (strlen($account_details->accountID) <= 1)
			$accountID = $account_details->accountID . "" . $account_count->count;
		else
			$accountID = $account_details->accountID . "0" . $account_count->count;


		$level = ($account_details->level) + 1;


        $data_account = [
            'accountID'            => $accountID,
            'accountName'            => $data ['name'] ? $data ['name'] : $data ['company'],
            'accountNameEn'            => $data ['nameEn'] ? $data ['nameEn'] : $data ['companyEn'],
            'master_account'            => 0,
            'accountCategory'            => $account_details->accountCategory,
            'parent_id'            => $id,
            'accountSide'            => $accountSide,
            'accountStatement'            => $accountStatement,
            'account_type'            => $account_type,
            'account_ref_id'            => $cid,
            'userID'   => $this->session->userdata('user_id'),
            'level'            => $level,


        ];


        if ($this->db->insert('gl_accounts', $data_account)) {

			//return true;
		}

        if ($this->Settings->IsInsuranceBusiness && $this->Settings->invoice_view ==21) {
			if($data['group_name']=="customer"){


            $id=$this->site->getAccountsSettings()->business_guarantee_insurance_acct;
            $account_type = "insurance";


            $accountSide = "Debit";
            $accountStatement = "Balance Sheet";


            $account_count = $this->site->getCountAccountByID($id);
		$account_details = $this->site->getAccountByID($id);
		if (strlen($account_details->accountID) <= 1)
			$accountID = $account_details->accountID . "" . $account_count->count;
		else
			$accountID = $account_details->accountID . "0" . $account_count->count;


		$level = ($account_details->level) + 1;


        $data_account = [
            'accountID'            => $accountID,
            'accountName'            => lang('Business_guarantee_insurance').' '. ($data ['name'] ? $data ['name'] : $data ['company']),
            'accountNameEn'            => $data ['nameEn'] ? $data ['nameEn'] : $data ['companyEn'],
            'master_account'            => 0,
            'accountCategory'            => $account_details->accountCategory,
            'parent_id'            => $id,
            'accountSide'            => $accountSide,
            'accountStatement'            => $accountStatement,
            'account_type'            => $account_type,
            'account_ref_id'            => $cid,
            'userID'   => $this->session->userdata('user_id'),
            'level'            => $level,


        ];


        if ($this->db->insert('gl_accounts', $data_account)) {

			//return true;
		}



                }
		}



            }

            if(isset($data['is_auto_advance_account']) && $data['is_auto_advance_account']==1 && $this->Settings->IsInsuranceBusiness && $this->Settings->invoice_view ==21){
                $account_type = "";
                $accountSide = "";
                $accountStatement = "";

                if($data['group_name']=="customer"){



                $id=$this->site->getAccountsSettings()->advance_payments_acct;
                $account_type = "advance";

            $accountSide = "Credit";
            $accountStatement = "Balance Sheet";










        $account_count = $this->site->getCountAccountByID($id);
		$account_details = $this->site->getAccountByID($id);
		if (strlen($account_details->accountID) <= 1)
			$accountID = $account_details->accountID . "" . $account_count->count;
		else
			$accountID = $account_details->accountID . "0" . $account_count->count;


		$level = ($account_details->level) + 1;


        $data_account = [
            'accountID'            => $accountID,
            'accountName'            => lang('Advance_payments').' '. ($data ['name'] ? $data ['name'] : $data ['company']),
            'accountNameEn'            => $data ['nameEn'] ? $data ['nameEn'] : $data ['companyEn'],
            'master_account'            => 0,
            'accountCategory'            => $account_details->accountCategory,
            'parent_id'            => $id,
            'accountSide'            => $accountSide,
            'accountStatement'            => $accountStatement,
            'account_type'            => $account_type,
            'account_ref_id'            => $cid,
            'userID'   => $this->session->userdata('user_id'),
            'level'            => $level,


        ];


        if ($this->db->insert('gl_accounts', $data_account)) {

			//return true;
		}
        }
        }

            if(isset($data['subscription_duration']) && $this->Settings->isSubscriptions==1){
                $subscription_info = $this->getSubscriptionsByID($data['subscription_duration']);
                $data_customer_subscriptions = [
                    'customer_id'            => $cid,
                    'subscription_id'            => $data ['subscription_duration'],
                    'start_subscription'            => $data ['start_subscription'],
                    'end_subscription'            => $data ['end_subscription'],
                    'created_by'   => $this->session->userdata('user_id'),
                    'purchase_amount'            => $subscription_info->amount,
                    'paid_amount'            => $subscription_info->paid_amount,
                ];

                if ($this->db->insert('customer_subscriptions', $data_customer_subscriptions)) {
                }
            }


            return $cid;
        }
        return false;
    }



    public function addCustomerFromOffline($data = [])
    {

        if (isset($this->getSystemSetting()->preAccounting) && $this->getSystemSetting()->preAccounting==1 && $this->getSystemSetting()->isAutoAccountOfflineCustomers==1){
            $data  ['is_auto_account']=1;
        }
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();

            if (isset($this->getSystemSetting()->preAccounting) && $this->getSystemSetting()->preAccounting==1 && $this->getSystemSetting()->isAutoAccountOfflineCustomers==1){

                $account_type = "";
                $accountSide = "";
                $accountStatement = "";

                if($data['group_name']=="customer"){



                $id=$this->getAccountsSettings()->accounts_local_customers_acct;
                $account_type = "local_customers";


            $accountSide = "Debit";
            $accountStatement = "Balance Sheet";

            } else if($data['group_name']=="supplier"){


                            $id=$this->getAccountsSettings()->accounts_local_suppliers_acct;
                            $account_type = "local_suppliers";


                        $accountSide = "Credit";
                        $accountStatement = "Balance Sheet";


                            }

            $account_count = $this->getCountAccountByID($id);
		$account_details = $this->getAccountByID($id);
		if (strlen($account_details->accountID) <= 1)
			$accountID = $account_details->accountID . "" . $account_count->count;
		else
			$accountID = $account_details->accountID . "0" . $account_count->count;


		$level = ($account_details->level) + 1;


        $data_account = [
            'accountID'            => $accountID,
            'accountName'            => $data ['name'] ? $data ['name'] : $data ['company'],
            'accountNameEn'            => $data ['nameEn'] ? $data ['nameEn'] : $data ['companyEn'],
            'master_account'            => 0,
            'accountCategory'            => $account_details->accountCategory,
            'parent_id'            => $id,
            'accountSide'            => $accountSide,
            'accountStatement'            => $accountStatement,
            'account_type'            => $account_type,
            'account_ref_id'            => $cid,
            'userID'   => $this->getUserId()->id,
            'level'            => $level,


        ];


        if ($this->db->insert('gl_accounts', $data_account)) {

			//return true;
		}


                }







            if(isset($data['subscription_duration']) && $this->getSystemSetting()->isSubscriptions==1){
                $subscription_info = $this->getSubscriptionsByID($data['subscription_duration']);
                $data_customer_subscriptions = [
                    'customer_id'            => $cid,
                    'subscription_id'            => $data ['subscription_duration'],
                    'start_subscription'            => $data ['start_subscription'],
                    'end_subscription'            => $data ['end_subscription'],
                    'created_by'   => $this->getUserId()->id,
                    'purchase_amount'            => $subscription_info->amount,
                    'paid_amount'            => $subscription_info->paid_amount,
                ];

                if ($this->db->insert('customer_subscriptions', $data_customer_subscriptions)) {
                }
            }




            return $cid;
        }
        return false;
    }

    public function getUserId()
    {

        $this->db->select('id');
        $this->db->order_by('id');
        $this->db->limit(1);
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCountAccountByID($id)
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get_where('gl_accounts', ['parent_id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

    public function getAccountByID($id)
	{
		$q = $this->db->get_where('gl_accounts', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

    public function getAccountsSettings()
    {
        $q = $this->db->get('gl_accounts_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSystemSetting()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function addAjaxCompany($data)
    {
        if ($this->db->insert('companies', $data)) {
            $c_id = $this->db->insert_id();
            return $this->getCompanyByID($c_id);
        }
        return false;
    }


    public function addDeposit($data, $cdata,$payment_type = null)
    {
        if ($this->db->insert('deposits', $data)) {
            $did = $this->db->insert_id();
            $this->db->update('companies', $cdata, ['id' => $data['company_id']]);

			if ($this->Settings->accounting){
			$entryDescription="Deposit_No#".$did;








				$entry_type="deposits";


					$entryCreditAccount= [];
					$entryCreditBalance= [];








					$entryDebitAccount= [];
                    if($this->site->getCompanyByID($data['company_id'])->is_auto_advance_account==1){
                        $entryDebitAccount[]=$payment_type;
                    }else{
					    $entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
                    }
					$entryDebitBalance= [];

					$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']);

					//$amount=$amount+$data['amount'];


					$entryCreditAccount= [];
					$entryCreditBalance= [];
					if($this->site->getCompanyByID($data['company_id'])->co_type==1){
						$entryDebitAcc=$this->site->getAccountByRefId($data['company_id'],'local_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
					}else{
					$entryDebitAcc=$this->site->getAccountByRefId($data['company_id'],'international_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
					}
					 $entryCreditAccount[] =  $entryDebitAcc;

					 $entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']);




					/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($sale->grand_total)){
					$entryDebitBalance[] =  $this->sma->formatDecimal($sale->grand_total - $data['amount']);

					}*/








                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {

                        $this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$did,$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId']);

                    }else{
                        $this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$did,$entry_type,null,null,null,null,$data['date'],$entryDescription);
                    }










			}







			return true;
        }
        return false;
    }

    public function deleteAddress($id)
    {
        if ($this->db->delete('addresses', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteBiller($id)
    {
        if ($this->getBillerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', ['id' => $id, 'group_name' => 'biller'])) {
            return true;
        }
        return false;
    }

    public function deleteCustomer($id)
    {
        if ($this->getCustomerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', ['id' => $id, 'group_name' => 'customer']) && $this->db->delete('users', ['company_id' => $id])) {
            return true;
        }
        return false;
    }


    public function deleteSalesman($id)
    {
        if ($this->getCustomerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', ['id' => $id, 'group_name' => 'salesman'])) {
            return true;
        }
        return false;
    }

    public function deleteDeposit($id)
    {
        $deposit = $this->getDepositByID($id);
        $company = $this->getCompanyByID($deposit->company_id);
        $cdata   = [
            'deposit_amount' => ($company->deposit_amount - $deposit->amount),
        ];
        if ($this->db->update('companies', $cdata, ['id' => $deposit->company_id]) && $this->db->delete('deposits', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteSupplier($id)
    {
        if ($this->getSupplierPurchases($id)) {
            return false;
        }
        if ($this->db->delete('companies', ['id' => $id, 'group_name' => 'supplier']) && $this->db->delete('users', ['company_id' => $id])) {
            return true;
        }
        return false;
    }

    public function getAddressByID($id)
    {
        $q = $this->db->get_where('addresses', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllBillerCompanies()
    {
        $q = $this->db->get_where('companies', ['group_name' => 'biller']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCustomerCompanies()
    {
        $q = $this->db->get_where('companies', ['group_name' => 'customer']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCustomerGroups()
    {
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllPriceGroups()
    {
        $q = $this->db->get('price_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllSupplierCompanies()
    {
        $q = $this->db->get_where('companies', ['group_name' => 'supplier']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBillerSales($id)
    {
        $this->db->where('biller_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getBillerSuggestions($term, $limit = 10)
    {
        $this->db->select('id, company as text');
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', ['group_name' => 'biller'], $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCompanyAddresses($company_id)
    {
        $q = $this->db->get_where('addresses', ['company_id' => $company_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCompanyByEmail($email)
    {
        $q = $this->db->get_where('companies', ['email' => $email], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCompanyUsers($company_id)
    {
        $q = $this->db->get_where('users', ['company_id' => $company_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCustomerSales($id)
    {
        $this->db->where('customer_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getCustomerSuggestions($term, $limit = 10, $grpId = null, $coId = null, $brId = null)
    {

        if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $this->Settings->isDisplayCustomersAccountOnInvoice==1)
        {
            if($brId && isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                $this->db->select("id, company as text, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as value, phone,brId", false);
            else
                $this->db->select("id, company as text, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as value, phone", false);
        }else{
            if($brId && isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as value, phone,brId", false);
            else
                $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as value, phone", false);
        }

        $this->db->where(" (code LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        if($brId && isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
            $this->db->where(" (id in (select customer_id from sma_customers_details where brId='".$brId."') ) ");
        }
        $q = $this->db->get_where('companies', ['group_name' => 'customer'], $limit);

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $this->Settings->isDisplayCustomersAccountOnInvoice==1)
		            {
			            $accountID = $this->site->getAccountByRefId($row->id,'local_customers') ? $this->site->getAccountByRefId($row->id,'local_customers')->accountID :($this->site->getAccountByRefId($row->id,'international_customers') ? $this->site->getAccountByRefId($row->id,'international_customers')->accountID : '');
                        if($accountID!=''){
                            $row->text=' ( '.$accountID.' ) '.$row->text;
                        }
		            }
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getDepositByID($id)
    {
        $q = $this->db->get_where('deposits', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSupplierPurchases($id)
    {
        $this->db->where('supplier_id', $id)->from('purchases');
        return $this->db->count_all_results();
    }


    public function getSupplierSuggestions($term, $limit = 10, $grpId = null, $coId = null, $brId = null)
    {
        $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as value, phone", false);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        if($grpId && $coId && $brId){
            $this->db->where(" (id in (select supplier_id from sma_suppliers_details where  grpId='".$grpId."' and coId='".$coId."' and brId='".$brId."') ) ");
        }
        $q = $this->db->get_where('companies', ['group_name' => 'supplier'], $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function updateAddress($id, $data)
    {
        if ($this->db->update('addresses', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateCompany($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('companies', $data)) {
            if(isset($data['is_auto_account']) && $data['is_auto_account']==1){

            $account_type = "";
            $accountSide = "";
            $accountStatement = "";

            if($data['group_name']=="customer"){


        if($data['co_type']==1){
            $idacc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
            $account_type = "local_customers";
        }else if($data['co_type']==2){
            $idacc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
            $account_type = "international_customers";
        }

        $accountSide = "Debit";
        $accountStatement = "Balance Sheet";


            } else if($data['group_name']=="supplier"){


                if($data['co_type']==1){
                    $idacc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
                    $account_type = "local_suppliers";
                }else if($data['co_type']==2){
                    $idacc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
                    $account_type = "international_suppliers";
                }

                $accountSide = "Credit";
                $accountStatement = "Balance Sheet";


                    }







    $account_count = $this->site->getCountAccountByID($idacc);
    $account_details = $this->site->getAccountByID($idacc);
    if (strlen($account_details->accountID) <= 1)
        $accountID = $account_details->accountID . "" . $account_count->count;
    else
        $accountID = $account_details->accountID . "0" . $account_count->count;


    $level = ($account_details->level) + 1;


    $data_account = [
        'accountID'            => $accountID,
        'accountName'            => $data ['name'] ? $data ['name'] : $data ['company'],
        'accountNameEn'            => $data ['nameEn'] ? $data ['nameEn'] : $data ['companyEn'],
        'master_account'            => 0,
        'accountCategory'            => $account_details->accountCategory,
        'parent_id'            => $idacc,
        'accountSide'            => $accountSide,
        'accountStatement'            => $accountStatement,
        'account_type'            => $account_type,
        'account_ref_id'            => $id,
        'userID'   => $this->session->userdata('user_id'),
        'level'            => $level,


    ];


    if ($this->db->insert('gl_accounts', $data_account)) {

        //return true;
    }


    if ($this->Settings->IsInsuranceBusiness && $this->Settings->invoice_view ==21) {
			if($data['group_name']=="customer"){


            $idacc=$this->site->getAccountsSettings()->business_guarantee_insurance_acct;
            $account_type = "insurance";


            $accountSide = "Debit";
            $accountStatement = "Balance Sheet";


            $account_count = $this->site->getCountAccountByID($idacc);
		$account_details = $this->site->getAccountByID($idacc);
		if (strlen($account_details->accountID) <= 1)
			$accountID = $account_details->accountID . "" . $account_count->count;
		else
			$accountID = $account_details->accountID . "0" . $account_count->count;


		$level = ($account_details->level) + 1;


        $data_account = [
            'accountID'            => $accountID,
            'accountName'            => lang('Business_guarantee_insurance').' '. ($data ['name'] ? $data ['name'] : $data ['company']),
            'accountNameEn'            => $data ['nameEn'] ? $data ['nameEn'] : $data ['companyEn'],
            'master_account'            => 0,
            'accountCategory'            => $account_details->accountCategory,
            'parent_id'            => $idacc,
            'accountSide'            => $accountSide,
            'accountStatement'            => $accountStatement,
            'account_type'            => $account_type,
            'account_ref_id'            => $id,
            'userID'   => $this->session->userdata('user_id'),
            'level'            => $level,


        ];


        if ($this->db->insert('gl_accounts', $data_account)) {

			//return true;
		}



                }
		}



        }

        if(isset($data['is_auto_advance_account']) && $data['is_auto_advance_account']==1){
                $account_type = "";
                $accountSide = "";
                $accountStatement = "";

                if($data['group_name']=="customer"){



                $idacc=$this->site->getAccountsSettings()->advance_payments_acct;
                $account_type = "advance";

            $accountSide = "Credit";
            $accountStatement = "Balance Sheet";










        $account_count = $this->site->getCountAccountByID($idacc);
		$account_details = $this->site->getAccountByID($idacc);
		if (strlen($account_details->accountID) <= 1)
			$accountID = $account_details->accountID . "" . $account_count->count;
		else
			$accountID = $account_details->accountID . "0" . $account_count->count;


		$level = ($account_details->level) + 1;


        $data_account = [
            'accountID'            => $accountID,
            'accountName'            => lang('Advance_payments').' '. ($data ['name'] ? $data ['name'] : $data ['company']),
            'accountNameEn'            => $data ['nameEn'] ? $data ['nameEn'] : $data ['companyEn'],
            'master_account'            => 0,
            'accountCategory'            => $account_details->accountCategory,
            'parent_id'            => $idacc,
            'accountSide'            => $accountSide,
            'accountStatement'            => $accountStatement,
            'account_type'            => $account_type,
            'account_ref_id'            => $id,
            'userID'   => $this->session->userdata('user_id'),
            'level'            => $level,


        ];


        if ($this->db->insert('gl_accounts', $data_account)) {

			//return true;
		}
        }
        }

            if(isset($data['subscription_duration']) && $this->getSystemSetting()->isSubscriptions==1){

                if ($this->db->update('customer_subscriptions', ['status' => 0] , ['customer_id' => $id])) {
                }
                $subscription_info = $this->getSubscriptionsByID($data['subscription_duration']);
                $data_customer_subscriptions = [
                    'customer_id'            => $id,
                    'subscription_id'            => $data ['subscription_duration'],
                    'start_subscription'            => isset($data['start_subscription']) ? $data ['start_subscription'] : null,
                    'end_subscription'            => isset($data['end_subscription']) ? $data ['end_subscription'] : null,
                    'created_by'   => $this->getUserId()->id,
                    'purchase_amount'            => $subscription_info->amount,
                    'paid_amount'            => $subscription_info->paid_amount,
                ];

                if ($this->db->insert('customer_subscriptions', $data_customer_subscriptions)) {
                }
            }


            return true;
        }
        return false;
    }

    public function updateDeposit($id, $data, $cdata,$payment_type = null)
    {
        if ($this->db->update('deposits', $data, ['id' => $id]) && $this->db->update('companies', $cdata, ['id' => $data['company_id']])) {

			if ($this->Settings->accounting){
			$entryDescription="Deposit_No#".$id;








				$entry_type="deposits";


					$entryCreditAccount= [];
					$entryCreditBalance= [];








					$entryDebitAccount= [];
                    if($this->site->getCompanyByID($data['company_id'])->is_auto_advance_account==1){
                        $entryDebitAccount[]=$payment_type;
                    }else{
					    $entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
                    }
					$entryDebitBalance= [];

					$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']);

					//$amount=$amount+$data['amount'];


					$entryCreditAccount= [];
					$entryCreditBalance= [];
					if($this->site->getCompanyByID($data['company_id'])->co_type==1){
						$entryDebitAcc=$this->site->getAccountByRefId($data['company_id'],'local_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
					}else{
					$entryDebitAcc=$this->site->getAccountByRefId($data['company_id'],'international_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
					}
					 $entryCreditAccount[] =  $entryDebitAcc;

					 $entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']);




					/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($sale->grand_total)){
					$entryDebitBalance[] =  $this->sma->formatDecimal($sale->grand_total - $data['amount']);

					}*/










			$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$id,$entry_type,null,null,null,null,null,$entryDescription,$data['date']);








			}




		return true;
        }
        return false;
    }


    public function addCustomersDetails($customers = [])
    {
        $flage=0;
        foreach ($customers as $customer) {
            if($this->db->insert('customers_details', $customer)){
                $flage=1;
            }
        }
        //if($flage==1)
            return true;
        //else
           // return false;
    }

    public function addSuppliersDetails($suppliers = [])
    {
        $flage=0;
        foreach ($suppliers as $supplier) {
            if($this->db->insert('suppliers_details', $supplier)){
                $flage=1;
            }
        }
        //if($flage==1)
            return true;
        //else
        //    return false;
    }

    public function getSalesmanCustomers($salesman_id)
    {
        $this->db->select('salesman_customers.customer_id as customer_id, companies.company as company, companies.companyEn as companyEn')
            ->join('companies', 'companies.id=salesman_customers.customer_id', 'left');

            $this->db->where('companies.group_name', 'customer');
            $this->db->where('salesman_id', $salesman_id);
        $q = $this->db->get('salesman_customers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getAllCustomerCompaniesWithOutSalesman($salesman_id)
    {
        $myQuery ="select *  from " . $this->db->dbprefix('companies') . "  where group_name='customer' and  id not in (select customer_id from " . $this->db->dbprefix('salesman_customers') . "  where salesman_id = '".$salesman_id."' )";
        $q = $this->db->query($myQuery);
        //$q = $this->db->get_where('companies', ['group_name' => 'customer']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSalesmanCustomerBySalesmanId($customer_id,$salesman_id)
    {
        $q = $this->db->get_where('salesman_customers', ['customer_id' => $customer_id,'salesman_id' => $salesman_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function addSalesmanCustomers($salesman_id,$customers = [])
    {

        $salCus=$this->getAllCustomerCompaniesWithOutSalesman($salesman_id);

        if($this->deleteSalesmanCustomers($salesman_id)){
            if ($this->db->insert_batch('salesman_customers', $customers)  ) {
                return true;
            }else{

                foreach ($salCus as $customer) {
                    if($this->db->insert('salesman_customers', $customer)){
                        $flage=1;
                    }
                }
            }
        }

        return false;

    }

    public function deleteSalesmanCustomers($salesman_id)
    {
        if ($this->db->delete('salesman_customers', ['salesman_id' => $salesman_id])) {
            return true;
        }
        return false;
    }

    public function getSalesmanCustomerByID($customer_id)
    {
        $q = $this->db->get_where('salesman_customers', ['customer_id' => $customer_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllSubscriptions()
    {
        $q = $this->db->get('subscriptions');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSubscriptionsByID($id)
    {
        $q = $this->db->get_where('subscriptions', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllCustomerCompaniesNotInbrId($brId)
    {
        $this->db->select('companies.*');

        $this->db->join(
            'customers_details',
            'customers_details.customer_id = companies.id
             AND customers_details.brId = ' . $this->db->escape($brId),
            'left'
        );

        $this->db->where('companies.group_name', 'customer');
        $this->db->where(" (customers_details.customer_id IS NULL) ");

        $q = $this->db->get('companies');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllSupplierCompaniesNotInbrId($brId)
    {
        $this->db->select('companies.*');

        $this->db->join(
            'suppliers_details',
            'suppliers_details.supplier_id = companies.id
             AND suppliers_details.brId = ' . $this->db->escape($brId),
            'left'
        );

        $this->db->where('companies.group_name', 'supplier');
        $this->db->where(" (suppliers_details.supplier_id IS NULL) ");

        $q = $this->db->get('companies');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


}
