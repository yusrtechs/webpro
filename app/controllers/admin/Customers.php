<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customers extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->lang->admin_load('customers', $this->Settings->user_language);
        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('companies_model');
		$this->load->admin_model('reports_model');
        $this->load->admin_model('accounts_model');

    }

    public function add()
    {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('email', lang('email_address'), 'is_unique[companies.email]');

        if ($this->form_validation->run('companies/add') == true) {
            $cg   = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $pg   = $this->site->getPriceGroupByID($this->input->post('price_group'));


			$hide_in_pos=0;
		if($this->input->post('hide_in_pos')==1){
			$hide_in_pos=1;

		}

            $data = [
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'email'               => $this->input->post('email'),
                'group_id'            => '3',
                'group_name'          => 'customer',
                'customer_group_id'   => $this->input->post('customer_group'),
                'customer_group_name' => $cg->name,
                'price_group_id'      => $this->input->post('price_group') ? $this->input->post('price_group') : null,
                'price_group_name'    => $this->input->post('price_group') ? $pg->name : null,
                'company'             => $this->input->post('company') ? $this->input->post('company') : $this->input->post('name'),
                'companyEn'             => $this->input->post('companyEn') ? $this->input->post('companyEn') : $this->input->post('nameEn'),
                'address'             => $this->input->post('address'),
                'addressEn'             => $this->input->post('addressEn'),
                'vat_no'              => $this->input->post('vat_no'),
                'cr_no'              => $this->input->post('cr_no'),
                'city'                => $this->input->post('city'),
                'state'               => $this->input->post('state'),
                'postal_code'         => $this->input->post('postal_code'),
                'country'             => $this->input->post('country'),
                'phone'               => $this->input->post('phone'),
                'cf1'                 => $this->input->post('cf1'),
                'cf2'                 => $this->input->post('cf2'),
                'cf3'                 => $this->input->post('cf3'),
                'cf4'                 => $this->input->post('cf4'),
                'cf5'                 => $this->input->post('cf5'),
                'cf6'                 => $this->input->post('cf6'),
                'gst_no'              => $this->input->post('gst_no'),
                'co_type'             => $this->input->post('co_type'),
                'hide_in_pos'         => $hide_in_pos,
				'building_no' => $this->input->post('building_no'),
                'street_name' => $this->input->post('street_name'),
                'district_name' => $this->input->post('district_name'),
                'additional_no' => $this->input->post('additional_no'),
                'co_to_co' => $this->input->post('co_to_co'),
                'street_nameEn' => $this->input->post('street_nameEn'),
                'district_nameEn' => $this->input->post('district_nameEn'),
                'cityEn' => $this->input->post('cityEn'),
                'stateEn' => $this->input->post('stateEn'),
                'countryEn' => $this->input->post('countryEn'),
                'code'      => $this->input->post('code') ? $this->input->post('code') : null,
                'subscription_duration'      => $this->input->post('subscription_duration') ? $this->input->post('subscription_duration') : null,



            ];

            if ($this->input->post('subscription_duration') && $this->input->post('start_subscription')) {
				$data  ['start_subscription'] = $this->sma->fld(trim($this->input->post('start_subscription')));
			}
            if ($this->input->post('subscription_duration') && $this->input->post('end_subscription')) {
				$data  ['end_subscription'] = $this->sma->fld(trim($this->input->post('end_subscription')));
			}

            if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
            {
                $is_auto_account=0;
                if($this->input->post('is_auto_account')==1){
			        $is_auto_account=1;
		        }



                $data  ['is_auto_account']=$is_auto_account;
                $data  ['account_type']=$this->input->post('account_type') ? $this->input->post('account_type') : 0;

                if($this->Settings->IsInsuranceBusiness && $this->Settings->invoice_view ==21)
                {
                    $is_auto_advance_account=0;
                    if($this->input->post('is_auto_advance_account')==1){
			            $is_auto_advance_account=1;
		            }
                    $data  ['is_auto_advance_account']=$is_auto_advance_account;
                }

            }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
            $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
            $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

        }

        if($this->input->post('co_type')==2){
            $data  ['company_tax']=$this->input->post('company_tax');
        }



        //$this->sma->print_arrays($data,$data_supp);

		} elseif ($this->input->post('add_customer')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('customers');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addCompany($data)) {

            if($data  ['account_type']==1){

                $data_supp = [
                    'name'   => $this->input->post('name'),
                    'email'       => $this->input->post('email'),
                    'group_id'    => '4',
                    'group_name'  => 'supplier',
                    'company'             => $this->input->post('company') ? $this->input->post('company') : $this->input->post('name'),
                    'address'     => $this->input->post('address'),
                    'vat_no'      => $this->input->post('vat_no'),
                    'city'        => $this->input->post('city'),
                    'state'       => $this->input->post('state'),
                    'postal_code' => $this->input->post('postal_code'),
                    'country'     => $this->input->post('country'),
                    'phone'       => $this->input->post('phone'),
                    'cf1'         => $this->input->post('cf1'),
                    'cf2'         => $this->input->post('cf2'),
                    'cf3'         => $this->input->post('cf3'),
                    'cf4'         => $this->input->post('cf4'),
                    'cf5'         => $this->input->post('cf5'),
                    'cf6'         => $this->input->post('cf6'),
                    'gst_no'      => $this->input->post('gst_no'),
                    'co_type'      => $this->input->post('co_type'),
                    'co_to_co' => $this->input->post('co_to_co'),
                ];



                if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
                {




            //$data_supp  ['is_auto_account']=1;
            $data_supp  ['customer_ref_id']=$cid;


            }

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data_supp  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data_supp  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data_supp  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }
            $this->companies_model->addCompany($data_supp);

            }
            $this->session->set_flashdata('message', lang('customer_added'));
            $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0] . '?customer=' . $cid);
        } else {
            $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']        = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->data['price_groups']    = $this->companies_model->getAllPriceGroups();
            $this->data['subscriptions']    = $this->companies_model->getAllSubscriptions();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->load->view($this->theme . 'customers/add', $this->data);
        }
    }


	public function addByAjax()
    {
        //$this->sma->checkPermissions(false, true);

        //$this->form_validation->set_rules('email', lang('email_address'), 'is_unique[companies.email]');

		/*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/


//if ($this->input->get('token', true) && $this->input->get('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $customer = $this->input->get('customer', true);
		//$this->sma->print_arrays($customer);



            if ($c_id = $this->companies_model->addAjaxCompany($customer)) {
				$this->sma->send_json(['msg' => 'success', 'result' => $c_id->id]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_add_customer')]));
            }
        /*} else {
            json_encode(['msg' => 'Invalid token']);
        }*/


    }




	public function add_address($company_id = null)
    {
        $this->sma->checkPermissions('add', true);
        $company = $this->companies_model->getCompanyByID($company_id);

        $this->form_validation->set_rules('line1', lang('line1'), 'required');
        $this->form_validation->set_rules('city', lang('city'), 'required');
        $this->form_validation->set_rules('state', lang('state'), 'required');
        $this->form_validation->set_rules('country', lang('country'), 'required');
        $this->form_validation->set_rules('phone', lang('phone'), 'required');

        if ($this->form_validation->run() == true) {
            $data = [
                'line1'       => $this->input->post('line1'),
                'line2'       => $this->input->post('line2'),
                'city'        => $this->input->post('city'),
                'postal_code' => $this->input->post('postal_code'),
                'state'       => $this->input->post('state'),
                'country'     => $this->input->post('country'),
                'phone'       => $this->input->post('phone'),
                'company_id'  => $company->id,
            ];
        } elseif ($this->input->post('add_address')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('customers');
        }

        if ($this->form_validation->run() == true && $this->companies_model->addAddress($data)) {
            $this->session->set_flashdata('message', lang('address_added'));
            admin_redirect('customers');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->load->view($this->theme . 'customers/add_address', $this->data);
        }
    }

    public function add_deposit($company_id = null)
    {
        $this->sma->checkPermissions('deposits', true);

        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }
        $company = $this->companies_model->getCompanyByID($company_id);

        if ($this->Owner || $this->Admin) {
            $this->form_validation->set_rules('date', lang('date'), 'required');
        }
        $this->form_validation->set_rules('amount', lang('amount'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $data = [
                'date'       => $date,
                'amount'     => $this->input->post('amount'),
                'paid_by'    => $this->input->post('paid_by'),
                'note'       => $this->input->post('note'),
                'company_id' => $company->id,
                'created_by' => $this->session->userdata('user_id'),
            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;
            }


            $cdata = [
                'deposit_amount' => ($company->deposit_amount + $this->input->post('amount')),
            ];

			$payment_type="";
			if ($this->Settings->accounting){
			if($this->input->post('paid_by')=="cash")
			$payment_type=$this->input->post('cash_account');
			if($this->input->post('paid_by')=="CC")
			$payment_type=$this->input->post('cc_account');
			if($this->input->post('paid_by')=="Cheque")
			$payment_type=$this->input->post('cheque_account');
			if($this->input->post('paid_by')=="bank_transfer")
			$payment_type=$this->input->post('transfer_account');
			if($this->input->post('paid_by')=="gift_card")
			$payment_type=$this->input->post('transfer_account');

			}

			//$this->sma->print_arrays($data, $cdata,$payment_type);
        } elseif ($this->input->post('add_deposit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('customers');
        }

        if ($this->form_validation->run() == true && $this->companies_model->addDeposit($data, $cdata,$payment_type)) {
            $this->session->set_flashdata('message', lang('deposit_added'));
            admin_redirect('customers');
        } else {

			if ($this->Settings->accounting){
                $accounts_settings   = $this->accounts_model->getSettings();

                if($company->is_auto_advance_account==1 && $this->Settings->IsInsuranceBusiness && $this->Settings->invoice_view ==21)
                {
                    $this->data['accounts_main_boxes_acct']  = $this->site->getAccountByRefId($company->id,'advance');
                    $this->data['accounts_main_banks_acct']  = $this->site->getAccountByRefId($company->id,'advance');
                }else{

			        $this->data['payment_types'] = $this->site->getAllPaymentTypes();

		            $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
                    $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
                }
            	//$this->sma->print_arrays($this->data['payment_types']);

			}

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

        }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->load->view($this->theme . 'customers/add_deposit', $this->data);
        }
    }

    public function add_user($company_id = null)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }
        $company = $this->companies_model->getCompanyByID($company_id);

        $this->form_validation->set_rules('email', lang('email_address'), 'is_unique[users.email]');
        $this->form_validation->set_rules('password', lang('password'), 'required|min_length[8]|max_length[20]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', lang('confirm_password'), 'required');

        if ($this->form_validation->run('companies/add_user') == true) {
            $active                  = $this->input->post('status');
            $notify                  = $this->input->post('notify');
            list($username, $domain) = explode('@', $this->input->post('email'));
            $email                   = strtolower($this->input->post('email'));
            $password                = $this->input->post('password');
            $additional_data         = [
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'phone'      => $this->input->post('phone'),
                'gender'     => $this->input->post('gender'),
                'company_id' => $company->id,
                'company'    => $company->company,
                'group_id'   => 3,
            ];
            $this->load->library('ion_auth');
        } elseif ($this->input->post('add_user')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('customers');
        }

        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $active, $notify)) {
            $this->session->set_flashdata('message', lang('user_added'));
            admin_redirect('customers');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->load->view($this->theme . 'customers/add_user', $this->data);
        }
    }

    public function addresses($company_id = null)
    {
        $this->sma->checkPermissions('index', true);
        $this->data['modal_js']  = $this->site->modal_js();
        $this->data['company']   = $this->companies_model->getCompanyByID($company_id);
        $this->data['addresses'] = $this->companies_model->getCompanyAddresses($company_id);
        $this->load->view($this->theme . 'customers/addresses', $this->data);
    }

    public function customer_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete');
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->companies_model->deleteCustomer($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('customers_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', lang('customers_deleted'));
                    }
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('email'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('phone'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('city'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('state'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('postal_code'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('country'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('vat_no'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('gst_no'));
                    $this->excel->getActiveSheet()->SetCellValue('L1', lang('scf1'));
                    $this->excel->getActiveSheet()->SetCellValue('M1', lang('scf2'));
                    $this->excel->getActiveSheet()->SetCellValue('N1', lang('scf3'));
                    $this->excel->getActiveSheet()->SetCellValue('O1', lang('scf4'));
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('scf5'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('scf6'));
                    $this->excel->getActiveSheet()->SetCellValue('R1', lang('deposit_amount'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $customer = $this->site->getCompanyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $customer->company);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $customer->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $customer->email);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $customer->phone);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $customer->address);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $customer->city);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $customer->state);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $customer->postal_code);
                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $customer->country);
                        $this->excel->getActiveSheet()->SetCellValue('J' . $row, $customer->vat_no);
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $customer->gst_no);
                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $customer->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $customer->cf2);
                        $this->excel->getActiveSheet()->SetCellValue('N' . $row, $customer->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('O' . $row, $customer->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $customer->cf5);
                        $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $customer->cf6);
                        $this->excel->getActiveSheet()->SetCellValue('R' . $row, $customer->deposit_amount);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'customers_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_customer_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->input->get('id') == 1) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('customer_x_deleted')]);
        }

        if ($this->companies_model->deleteCustomer($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('customer_deleted')]);
        } else {
            $this->sma->send_json(['error' => 1, 'msg' => lang('customer_x_deleted_have_sales')]);
        }
    }

    public function delete_address($id)
    {
        $this->sma->checkPermissions('delete', true);

        if ($this->companies_model->deleteAddress($id)) {
            $this->session->set_flashdata('message', lang('address_deleted'));
            admin_redirect('customers');
        }
    }

    public function delete_deposit($id)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->companies_model->deleteDeposit($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('deposit_deleted')]);
        }
    }

    public function deposit_note($id = null)
    {
        $this->sma->checkPermissions('deposits', true);
        $deposit                  = $this->companies_model->getDepositByID($id);
        $this->data['customer']   = $this->companies_model->getCompanyByID($deposit->company_id);
        $this->data['deposit']    = $deposit;
        $this->data['page_title'] = $this->lang->line('deposit_note');
        $this->load->view($this->theme . 'customers/deposit_note', $this->data);
    }

    public function deposits($company_id = null)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }

        $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['modal_js'] = $this->site->modal_js();
        $this->data['company']  = $this->companies_model->getCompanyByID($company_id);
        $this->load->view($this->theme . 'customers/deposits', $this->data);
    }

    public function edit($id = null)
    {
        //$this->sma->checkPermissions(false, true);
		 $this->sma->checkPermissions('edit', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		//$this->form_validation->set_rules('email', lang('email_address'), 'required');
		$this->form_validation->set_rules('company', lang('Company_name_in_Arabic'), 'required');

        $company_details = $this->companies_model->getCompanyByID($id);
        /*if ($this->input->post('email') != $company_details->email) {
            $this->form_validation->set_rules('email', lang('email_address'), 'required');
        }
		*/

        if ($this->form_validation->run() == true) {
            $cg   = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
            $pg   = $this->site->getPriceGroupByID($this->input->post('price_group'));

				$hide_in_pos=0;
		if($this->input->post('hide_in_pos')==1){
			$hide_in_pos=1;

		}


			$data = [
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'email'               => $this->input->post('email'),
                'group_id'            => '3',
                'group_name'          => 'customer',
                'customer_group_id'   => $this->input->post('customer_group'),
                'customer_group_name' => $cg->name,
                'price_group_id'      => $this->input->post('price_group') ? $this->input->post('price_group') : null,
                'price_group_name'    => $this->input->post('price_group') ? $pg->name : null,
                'company'             => $this->input->post('company') ? $this->input->post('company') : $this->input->post('name'),
                'companyEn'             => $this->input->post('companyEn') ? $this->input->post('companyEn') : $this->input->post('nameEn'),
                'address'             => $this->input->post('address'),
                'addressEn'             => $this->input->post('addressEn'),
                'vat_no'              => $this->input->post('vat_no'),
                'cr_no'              => $this->input->post('cr_no'),
                'city'                => $this->input->post('city'),
                'state'               => $this->input->post('state'),
                'postal_code'         => $this->input->post('postal_code'),
                'country'             => $this->input->post('country'),
                'phone'               => $this->input->post('phone'),
                'cf1'                 => $this->input->post('cf1'),
                'cf2'                 => $this->input->post('cf2'),
                'cf3'                 => $this->input->post('cf3'),
                'cf4'                 => $this->input->post('cf4'),
                'cf5'                 => $this->input->post('cf5'),
                'cf6'                 => $this->input->post('cf6'),
                'award_points'        => $this->input->post('award_points'),
                'gst_no'              => $this->input->post('gst_no'),
                'co_type'             => $this->input->post('co_type'),
                'hide_in_pos'             => $this->input->post('hide_in_pos'),
				'building_no' => $this->input->post('building_no'),
                'street_name' => $this->input->post('street_name'),
                'district_name' => $this->input->post('district_name'),
                'additional_no' => $this->input->post('additional_no'),
                'co_to_co' => $this->input->post('co_to_co'),
                'street_nameEn' => $this->input->post('street_nameEn'),
                'district_nameEn' => $this->input->post('district_nameEn'),
                'cityEn' => $this->input->post('cityEn'),
                'stateEn' => $this->input->post('stateEn'),
                'countryEn' => $this->input->post('countryEn'),
                'code'      => $this->input->post('code') ? $this->input->post('code') : null,
                'grpId'           => $company_details->grpId,
                'coId'           => $company_details->coId,
                'brId'           => $company_details->brId,

            ];

            if((($this->input->post('subscription_duration') != $company_details->subscription_duration) || ($this->input->post('start_subscription') ? ($this->input->post('start_subscription') != $this->sma->hrsd($company_details->start_subscription)) : 0) || ($this->input->post('end_subscription') ? ($this->input->post('end_subscription') != $this->sma->hrsd($company_details->end_subscription)) : 0))){

                if ($this->input->post('subscription_duration')) {
				    $data  ['subscription_duration'] = $this->input->post('subscription_duration');
    			}
                if ($this->input->post('subscription_duration') && $this->input->post('start_subscription')) {
		    		$data  ['start_subscription'] = $this->sma->fld(trim($this->input->post('start_subscription')));
			    }
                if ($this->input->post('subscription_duration') && $this->input->post('end_subscription')) {
	    			$data  ['end_subscription'] = $this->sma->fld(trim($this->input->post('end_subscription')));
		    	}
            }




            if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $company_details->is_auto_account==0)
            {
                $is_auto_account=0;
                if($this->input->post('is_auto_account')==1){
		        	$is_auto_account=1;
		        }


                $data  ['account_type']=$this->input->post('account_type') ? $this->input->post('account_type') : 0;
                $data  ['is_auto_account']=$is_auto_account;


            }

            if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $company_details->is_auto_advance_account==0)
            {
                if($this->Settings->IsInsuranceBusiness && $this->Settings->invoice_view ==21)
                {
                    $is_auto_advance_account=0;
                    if($this->input->post('is_auto_advance_account')==1){
			            $is_auto_advance_account=1;
		            }
                    $data  ['is_auto_advance_account']=$is_auto_advance_account;
                }
            }

        if($this->input->post('co_type')==2){
            $data  ['company_tax']=$this->input->post('company_tax');
        }
        //$this->sma->print_arrays($data);
        } elseif ($this->input->post('edit_customer')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateCompany($id, $data)) {

            if($data  ['account_type']==1){

                $data_supp = [
                    'name'   => $this->input->post('name'),
                    'email'       => $this->input->post('email'),
                    'group_id'    => '4',
                    'group_name'  => 'supplier',
                    'company'             => $this->input->post('company') ? $this->input->post('company') : $this->input->post('name'),
                    'address'     => $this->input->post('address'),
                    'vat_no'      => $this->input->post('vat_no'),
                    'city'        => $this->input->post('city'),
                    'state'       => $this->input->post('state'),
                    'postal_code' => $this->input->post('postal_code'),
                    'country'     => $this->input->post('country'),
                    'phone'       => $this->input->post('phone'),
                    'cf1'         => $this->input->post('cf1'),
                    'cf2'         => $this->input->post('cf2'),
                    'cf3'         => $this->input->post('cf3'),
                    'cf4'         => $this->input->post('cf4'),
                    'cf5'         => $this->input->post('cf5'),
                    'cf6'         => $this->input->post('cf6'),
                    'gst_no'      => $this->input->post('gst_no'),
                    'co_type'      => $this->input->post('co_type'),
                    'co_to_co' => $this->input->post('co_to_co'),
                ];



                if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
                {




            //$data_supp  ['is_auto_account']=1;
            $data_supp  ['customer_ref_id']=$id;


            }

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data_supp  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data_supp  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data_supp  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }
            $this->companies_model->addCompany($data_supp);

            }
            $this->session->set_flashdata('message', lang('customer_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['customer']        = $company_details;
            $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

            }

            $this->data['modal_js']        = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->data['price_groups']    = $this->companies_model->getAllPriceGroups();
            $this->data['subscriptions']    = $this->companies_model->getAllSubscriptions();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->load->view($this->theme . 'customers/edit', $this->data);
        }
    }

    public function edit_address($id = null)
    {
        $this->sma->checkPermissions('edit', true);

        $this->form_validation->set_rules('line1', lang('line1'), 'required');
        $this->form_validation->set_rules('city', lang('city'), 'required');
        $this->form_validation->set_rules('state', lang('state'), 'required');
        $this->form_validation->set_rules('country', lang('country'), 'required');
        $this->form_validation->set_rules('phone', lang('phone'), 'required');

        if ($this->form_validation->run() == true) {
            $data = [
                'line1'       => $this->input->post('line1'),
                'line2'       => $this->input->post('line2'),
                'city'        => $this->input->post('city'),
                'postal_code' => $this->input->post('postal_code'),
                'state'       => $this->input->post('state'),
                'country'     => $this->input->post('country'),
                'phone'       => $this->input->post('phone'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        } elseif ($this->input->post('edit_address')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('customers');
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateAddress($id, $data)) {
            $this->session->set_flashdata('message', lang('address_updated'));
            admin_redirect('customers');
        } else {
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['address']  = $this->companies_model->getAddressByID($id);
            $this->load->view($this->theme . 'customers/edit_address', $this->data);
        }
    }

    public function edit_deposit($id = null)
    {
        $this->sma->checkPermissions('deposits', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $deposit = $this->companies_model->getDepositByID($id);
        $company = $this->companies_model->getCompanyByID($deposit->company_id);

        if ($this->Owner || $this->Admin) {
            $this->form_validation->set_rules('date', lang('date'), 'required');
        }
        $this->form_validation->set_rules('amount', lang('amount'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = $deposit->date;
            }
            $data = [
                'date'       => $date,
                'amount'     => $this->input->post('amount'),
                'paid_by'    => $this->input->post('paid_by'),
                'note'       => $this->input->post('note'),
                'company_id' => $deposit->company_id,
                'updated_by' => $this->session->userdata('user_id'),
                'updated_at' => $date = date('Y-m-d H:i:s'),
                'grpId'           => $deposit->grpId,
                'coId'           => $deposit->coId,
                'brId'           => $deposit->brId,
            ];

            $cdata = [
                'deposit_amount' => (($company->deposit_amount - $deposit->amount) + $this->input->post('amount')),
            ];

			$payment_type="";
			if ($this->Settings->accounting){
			if($this->input->post('paid_by')=="cash")
			$payment_type=$this->input->post('cash_account');
			if($this->input->post('paid_by')=="CC")
			$payment_type=$this->input->post('cc_account');
			if($this->input->post('paid_by')=="Cheque")
			$payment_type=$this->input->post('cheque_account');
			if($this->input->post('paid_by')=="bank_transfer")
			$payment_type=$this->input->post('transfer_account');
			if($this->input->post('paid_by')=="gift_card")
			$payment_type=$this->input->post('transfer_account');

			}



        } elseif ($this->input->post('edit_deposit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('customers');
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateDeposit($id, $data, $cdata, $payment_type)) {
            $this->session->set_flashdata('message', lang('deposit_updated'));
            admin_redirect('customers');
        } else {
			if ($this->Settings->accounting){
                $accounts_settings   = $this->accounts_model->getSettings();

                if($company->is_auto_advance_account==1 && $this->Settings->IsInsuranceBusiness && $this->Settings->invoice_view ==21)
                {
                    $this->data['accounts_main_boxes_acct']  = $this->site->getAccountByRefId($company->id,'advance');
                    $this->data['accounts_main_banks_acct']  = $this->site->getAccountByRefId($company->id,'advance');
                }else{
			        $this->data['payment_types'] = $this->site->getAllPaymentTypes();
			        $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
                    $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
                }
            	//$this->sma->print_arrays($this->data['payment_types']);

			}

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->data['deposit']  = $deposit;
            $this->load->view($this->theme . 'customers/edit_deposit', $this->data);
        }
    }

    public function get_award_points($id = null)
    {
        $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
        $this->sma->send_json(['ca_points' => $row->award_points]);
    }

    public function get_customer_details($id = null)
    {
        $this->sma->send_json($this->companies_model->getCompanyByID($id));
    }

    public function get_deposits($company_id = null)
    {
        $this->sma->checkPermissions('deposits');
        $this->load->library('datatables');
        $this->datatables
            ->select("deposits.id as id, date, amount, paid_by, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by", false)
            ->from('deposits')
            ->join('users', 'users.id=deposits.created_by', 'left')
            ->where($this->db->dbprefix('deposits') . '.company_id', $company_id)
            ->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . lang('deposit_note') . "' href='" . admin_url('customers/deposit_note/$1') . "' data-toggle='modal' data-target='#myModal2'><i class=\"fa fa-file-text-o\"></i></a> <a class=\"tip\" title='" . lang('edit_deposit') . "' href='" . admin_url('customers/edit_deposit/$1') . "' data-toggle='modal' data-target='#myModal2'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_deposit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('customers/delete_deposit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id')
        ->unset_column('id');
        echo $this->datatables->generate();
    }

    public function getCustomer($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
        if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $this->Settings->isDisplayCustomersAccountOnInvoice==1)
		    {
	            $accountID = $this->site->getAccountByRefId($row->id,'local_customers') ? $this->site->getAccountByRefId($row->id,'local_customers')->accountID :($this->site->getAccountByRefId($row->id,'international_customers') ? $this->site->getAccountByRefId($row->id,'international_customers')->accountID : '');
                $this->sma->send_json([['id' => $row->id, 'text' => (($row->company && $accountID != '') ? ' ( '.$accountID.' ) '.$row->company : $row->company)]]);
            }else{
                $this->sma->send_json([['id' => $row->id, 'text' => ($row->company && $row->company != '-' ? $row->company : $row->name)]]);
            }

    }

    public function getCustomerco_type($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
		$this->sma->send_json($row);
    }

    public function getCustomers()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            //->select("id, company, name, IF(co_type=1,'".lang('local')."', '".lang('international')."') as co_type, email, phone, price_group_name, customer_group_name, vat_no, gst_no, deposit_amount, award_points")
            ->select("id, code, company, IF(co_type=1,'".lang('local')."', '".lang('international')."') as co_type, email, phone, price_group_name, customer_group_name, vat_no, deposit_amount, award_points")
            ->from('companies')
            ->where('group_name', 'customer');
            /*if(SHOP)
                $this->datatables->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . lang('list_deposits') . "' href='" . admin_url('customers/deposits/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a> <a class=\"tip\" title='" . lang('add_deposit') . "' href='" . admin_url('customers/add_deposit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus\"></i></a> <a class=\"tip\" title='" . lang('list_addresses') . "' href='" . admin_url('customers/addresses/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-location-arrow\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('customers/customers_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a class=\"tip\" title='" . lang('list_users') . "' href='" . admin_url('customers/users/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-users\"></i></a> <a class=\"tip\" title='" . lang('add_user') . "' href='" . admin_url('customers/add_user/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-user-plus\"></i></a> <a class=\"tip\" title='" . lang('edit_customer') . "' href='" . admin_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_customer') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            else */if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
                $this->datatables->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . lang('list_deposits') . "' href='" . admin_url('customers/deposits/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a> <a class=\"tip\" title='" . lang('add_deposit') . "' href='" . admin_url('customers/add_deposit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus\"></i></a> <a class=\"tip\" title='" . lang('list_addresses') . "' href='" . admin_url('customers/addresses/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-location-arrow\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('customers/customers_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a class=\"tip\" title='" . lang('edit_customer') . "' href='" . admin_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_customer') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            }else{
                $this->datatables->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . lang('list_deposits') . "' href='" . admin_url('customers/deposits/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a> <a class=\"tip\" title='" . lang('add_deposit') . "' href='" . admin_url('customers/add_deposit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus\"></i></a> <a class=\"tip\" title='" . lang('list_addresses') . "' href='" . admin_url('customers/addresses/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-location-arrow\"></i></a> <a class=\"tip\" title='" . lang('edit_customer') . "' href='" . admin_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_customer') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            }
        //->unset_column('id');
        echo $this->datatables->generate();
    }

    public function import_csv()
    {
        $this->sma->checkPermissions('add', true);
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            if (isset($_FILES['csv_file'])) {
                $this->load->library('upload');

                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('customers');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5001, ',')) !== false) {
                        $row[0]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[0]);
                        $row[1]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[1]);
                        $row[3]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[3]);
                        $row[4]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[4]);
                        $row[5]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[5]);
                        $row[8]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[8]);
                        $row[16]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[16]);
                        $row[17]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[17]);


                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles         = array_shift($arrResult);
                $rw             = 2;
                $updated        = '';
                $data           = [];
                $customer_group = $this->site->getCustomerGroupByID($this->Settings->customer_group);
                $price_group    = $this->site->getPriceGroupByID($this->Settings->price_group);





                foreach ($arrResult as $key => $value) {
                    $customer = [
                        'company'             => isset($value[0]) ? trim($value[0]) : '',
                        'name'                => isset($value[1]) ? trim($value[1]) : '',
                        'email'               => isset($value[2]) ? trim($value[2]) : '',
                        'phone'               => isset($value[3]) ? trim($value[3]) : '',
                        'address'             => isset($value[4]) ? trim($value[4]) : '',
                        'city'                => isset($value[5]) ? trim($value[5]) : '',
                        'state'               => isset($value[6]) ? trim($value[6]) : '',
                        'postal_code'         => isset($value[7]) ? trim($value[7]) : '',
                        'country'             => isset($value[8]) ? trim($value[8]) : '',
                        'vat_no'              => isset($value[9]) ? trim($value[9]) : '',
                        'gst_no'              => isset($value[10]) ? trim($value[10]) : '',
                        'cf1'                 => isset($value[11]) ? trim($value[11]) : '',
                        'cf2'                 => isset($value[12]) ? trim($value[12]) : '',
                        'cf3'                 => isset($value[13]) ? trim($value[13]) : '',
                        'cf4'                 => isset($value[14]) ? trim($value[14]) : '',
                        'cf5'                 => isset($value[15]) ? trim($value[15]) : '',
                        'companyEn'                 => isset($value[16]) ? trim($value[16]) : '',
                        'nameEn'                 => isset($value[17]) ? trim($value[17]) : '',
                        'is_auto_account'                 => (isset($value[18]) && $value[18]==1 && isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1) ? 1: 0,
                        'co_type'            => (isset($value[19]) && $value[19]=='International') ? 2 : 1,
                        'group_id'            => 3,
                        'group_name'          => 'customer',
                        'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                        'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                        'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                        'price_group_name'    => (!empty($price_group)) ? $price_group->name : null,
                        'code'                 => isset($value[20]) ? trim($value[20]) : '',
                    ];


                    if (empty($customer['company'])){// || empty($customer['name']) || empty($customer['email'])) {
                        //$this->session->set_flashdata('error', lang('company') . ', ' . lang('name') . ', ' . lang('email') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        $this->session->set_flashdata('error', lang('company') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        admin_redirect('customers');
                    } else {
                        /*if ($this->Settings->indian_gst && empty($customer['state'])) {
                            $this->session->set_flashdata('error', lang('state') . ' ' . lang('is_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                            admin_redirect('customers');
                        }
                        if ($customer_details = $this->companies_model->getCompanyByEmail($customer['email'])) {
                            if ($customer_details->group_id == 3) {
                                $updated .= '<p>' . lang('customer_updated') . ' (' . $customer['email'] . ')</p>';
                                $this->companies_model->updateCompany($customer_details->id, $customer);
                            }
                            */
                        //} else {
                            $data[] = $customer;
                        //}
                        $rw++;
                    }
                }
                //$this->sma->print_arrays($data);
                // $this->sma->print_arrays($data, $updated);
            }
        } elseif ($this->input->post('import')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('customers');
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addCompaniesV2($data)) {
                $this->session->set_flashdata('message', lang('customers_added') . $updated);
                admin_redirect('customers');
            }
        } else {
            if (isset($data) && empty($data)) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_customers'));
                }
                admin_redirect('customers');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import', $this->data);
        }
    }

    public function index($action = null)
    {
        $this->sma->checkPermissions();
        //$this->sma->print_arrays($this->session);
        $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc                   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('customers')]];
        $meta                 = ['page_title' => lang('customers'), 'bc' => $bc];
        $this->page_construct('customers/index', $meta, $this->data);
    }

    public function suggestions($term = null, $limit = null, $a = null)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', true);
        }
        if (strlen($term) < 1) {
            return false;
        }
        $limit  = $this->input->get('limit', true);
        $grpId  = $this->input->get('grpId', true);
        $coId  = $this->input->get('coId', true);
        $brId  = $this->input->get('brId', true);
        $result = $this->companies_model->getCustomerSuggestions($term, $limit,$grpId,$coId,$brId);
        if ($a) {
            $this->sma->send_json($result);
        }
        $rows['results'] = $result;
        $this->sma->send_json($rows);
    }

    public function users($company_id = null)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }

        $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['modal_js'] = $this->site->modal_js();
        $this->data['company']  = $this->companies_model->getCompanyByID($company_id);
        $this->data['users']    = $this->companies_model->getCompanyUsers($company_id);
        $this->load->view($this->theme . 'customers/users', $this->data);
    }

    public function customers_details($company_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }
        $company = $this->companies_model->getCompanyByID($company_id);

         $this->form_validation->set_rules('customers_details', lang('submit'), 'required');

        if ($this->form_validation->run('companies/customers_details') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }
             //$this->sma->print_arrays($_POST['br_option']);
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);





                if (!$company_id) {
                    foreach ($this->companies_model->getAllCustomerCompaniesNotInbrId($brId) as $cusArr) {
                        $additional_data         = [
                            'customer_id' => $cusArr->id,
                            'grpId'  => $branch_info->grpId,
                            'coId'  => $branch_info->coId,
                            'brId'  => $brId,

                        ];
                        $customers_details_data[] = $additional_data;
                    }

                }else{

                    $additional_data         = [
                        'customer_id' => $company_id,
                        'grpId'  => $branch_info->grpId,
                        'coId'  => $branch_info->coId,
                        'brId'  => $brId,
                    ];
                    $customers_details_data[] = $additional_data;
                }
                //$this->sma->print_arrays($customers_details_data);
            }





        } elseif ($this->input->post('customers_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->companies_model->addCustomersDetails($customers_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->data['mainBranches'] = $this->site->getMainBranches();
            $this->data['customerBranches'] = $this->site->getCustomerBranches($company_id);
            $this->load->view($this->theme . 'customers/customers_details', $this->data);
        }
    }



    public function view($id = null)
    {
        $this->sma->checkPermissions('index', true);
        $this->data['error']    = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $customer_details = $this->companies_model->getCompanyByID($id);
        $this->data['customer'] = $customer_details;
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
         {

        $this->data['mainGroups'] = $this->site->getMainGroup();
        $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
        $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

         }

         if(isset($this->Settings->isSubscriptions) && $this->Settings->isSubscriptions==1)
            {
                $subscription_duration_details=$this->companies_model->getSubscriptionsByID($customer_details->subscription_duration);
                if ($this->Settings->user_language == 'arabic' || $subscription_duration_details->name_en==''){
                    $subscription_duration=$subscription_duration_details->name;
                }else{
                    $subscription_duration=$subscription_duration_details->name_en;
                }
                $this->data['subscription_duration'] = $subscription_duration;
            }
        $this->load->view($this->theme . 'customers/view', $this->data);
    }


	/*---------------------reports-------------------------------*/

    public function customers_report()
    {
        $this->sma->checkPermissions('customers');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('customers_report')]];
        $meta = ['page_title' => lang('customers_report'), 'bc' => $bc];
        $this->page_construct('reports/customers', $meta, $this->data);
    }

    public function import_excel()
    {
        $this->sma->checkPermissions('add', true);
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');


        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                redirect($_SERVER['HTTP_REFERER']);
            }
            /*
            if (isset($_FILES['csv_file'])) {
                $this->load->library('upload');

                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('customers');
                }

                $csv = $this->upload->file_name;
                */
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                //$config['allowed_types'] = 'csv';
                $config['allowed_types'] = 'xlsx|xls';
                $config['max_size']      = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;


				if (!$this->upload->do_upload('xlsx_file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data1 = array('upload_data' => $this->upload->data());
			}
			if(empty($error)){
			  if (!empty($data1['upload_data']['file_name'])) {
				$import_xls_file = $data1['upload_data']['file_name'];
			} else {
				$import_xls_file = 0;
			}
			$inputFileName = 'files/' . $import_xls_file;


				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);


                //$this->sma->print_arrays($allDataInSheet);

				$i=0;
				foreach ($allDataInSheet as $value) {

                  $mainitems[$i]['company'] = isset($value['A']) ? trim($value['A']) : '';
				  $mainitems[$i]['name'] = isset($value['B']) ? trim($value['B']) : '';
				  $mainitems[$i]['email'] = isset($value['C']) ? trim($value['C']) : '';
				  $mainitems[$i]['phone'] = isset($value['D']) ? trim($value['D']) : '';
				  $mainitems[$i]['address'] = isset($value['E']) ? trim($value['E']) : '';
				  $mainitems[$i]['city'] = isset($value['F']) ? trim($value['F']) : '';
				  $mainitems[$i]['state'] = isset($value['G']) ? trim($value['G']) : '';
				  $mainitems[$i]['postal_code'] = isset($value['H']) ? trim($value['H']) : '';
				  $mainitems[$i]['country'] = isset($value['I']) ? trim($value['I']) : '';
				  $mainitems[$i]['vat_no'] = isset($value['J']) ? trim($value['J']) : '';
				  $mainitems[$i]['gst_no'] = isset($value['K']) ? trim($value['K']) : '';
				  $mainitems[$i]['cf1'] = isset($value['L']) ? trim($value['L']) : '';
				  $mainitems[$i]['cf2'] = isset($value['M'])? trim($value['M']) : '';
				  $mainitems[$i]['cf3'] = isset($value['N'])? trim($value['N']) : '';
				  $mainitems[$i]['cf4'] = isset($value['O'])? trim($value['O']) : '';
				  $mainitems[$i]['cf5'] = isset($value['P'])? trim($value['P']) : '';
				  $mainitems[$i]['companyEn'] = isset($value['Q'])? trim($value['Q']) : '';
				  $mainitems[$i]['nameEn'] = isset($value['R'])? trim($value['R']) : '';
				  $mainitems[$i]['is_auto_account'] = (isset($value['S']) && $value['S']==1 && isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1) ? 1: 0;
				  $mainitems[$i]['co_type'] = (isset($value['T']) && $value['T']=='International') ? 2 : 1;
				  $mainitems[$i]['code'] = isset($value['U'])? trim($value['U']) : '';
				  $i++;
				}
				$rw          = 2;
                $differences = 0;
                $matches     = 0;
				$PW = [];

                //$this->sma->print_arrays($mainitems);
                //$this->sma->print_arrays($this->products_model->getCategoryByCode($mainitems[2]['category_code']));

                $customer_group = $this->site->getCustomerGroupByID($this->Settings->customer_group);
                $price_group    = $this->site->getPriceGroupByID($this->Settings->price_group);

				//foreach ($mainitems as $item) {






                $count=0;
                foreach ($mainitems as $key => $value) {

                    if($count>0){

                    $customer = [
                        'company'             => isset($value['company']) ? trim($value['company']) : '',
                        'name'                => isset($value['name']) ? trim($value['name']) : '',
                        'email'               => isset($value['email']) ? trim($value['email']) : '',
                        'phone'               => isset($value['phone']) ? trim($value['phone']) : '',
                        'address'             => isset($value['address']) ? trim($value['address']) : '',
                        'city'                => isset($value['city']) ? trim($value['city']) : '',
                        'state'               => isset($value['state']) ? trim($value['state']) : '',
                        'postal_code'         => isset($value['postal_code']) ? trim($value['postal_code']) : '',
                        'country'             => isset($value['country']) ? trim($value['country']) : '',
                        'vat_no'              => isset($value['vat_no']) ? trim($value['vat_no']) : '',
                        'gst_no'              => isset($value['gst_no']) ? trim($value['gst_no']) : '',
                        'cf1'                 => isset($value['cf1']) ? trim($value['cf1']) : '',
                        'cf2'                 => isset($value['cf2']) ? trim($value['cf2']) : '',
                        'cf3'                 => isset($value['cf3']) ? trim($value['cf3']) : '',
                        'cf4'                 => isset($value['cf4']) ? trim($value['cf4']) : '',
                        'cf5'                 => isset($value['cf5']) ? trim($value['cf5']) : '',
                        'companyEn'                 => isset($value['companyEn']) ? trim($value['companyEn']) : '',
                        'nameEn'                 => isset($value['nameEn']) ? trim($value['nameEn']) : '',
                        'is_auto_account'                 => $value['is_auto_account'],
                        'co_type'            => $value['co_type'],
                        'group_id'            => 3,
                        'group_name'          => 'customer',
                        'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                        'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                        'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                        'price_group_name'    => (!empty($price_group)) ? $price_group->name : null,
                        'code'                 => isset($value['code']) ? trim($value['code']) : '',
                    ];


                    if (empty($customer['company'])){// || empty($customer['name']) || empty($customer['email'])) {
                        //$this->session->set_flashdata('error', lang('company') . ', ' . lang('name') . ', ' . lang('email') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        $this->session->set_flashdata('error', lang('company') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        admin_redirect('customers');
                    } else {
                        /*if ($this->Settings->indian_gst && empty($customer['state'])) {
                            $this->session->set_flashdata('error', lang('state') . ' ' . lang('is_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                            admin_redirect('customers');
                        }
                        if ($customer_details = $this->companies_model->getCompanyByEmail($customer['email'])) {
                            if ($customer_details->group_id == 3) {
                                $updated .= '<p>' . lang('customer_updated') . ' (' . $customer['email'] . ')</p>';
                                $this->companies_model->updateCompany($customer_details->id, $customer);
                            }
                            */
                        //} else {
                            $data[] = $customer;
                        //}
                        $rw++;
                    }
                    }
                    $count++;
                }
                //$this->sma->print_arrays($data);
                // $this->sma->print_arrays($data, $updated);
            }
        } elseif ($this->input->post('import')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('customers');
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addCompaniesV2($data)) {
                $this->session->set_flashdata('message', lang('customers_added') . $updated);
                admin_redirect('customers');
            }
        } else {
            if (isset($data) && empty($data)) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_customers'));
                }
                admin_redirect('customers');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/import_excel', $this->data);
        }
    }



}
