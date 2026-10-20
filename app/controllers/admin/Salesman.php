<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Salesman extends MY_Controller
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
        $this->lang->admin_load('salesman_lang', $this->Settings->user_language);
        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('companies_model');
		$this->load->admin_model('reports_model');
        $this->load->admin_model('accounts_model');

    }

    public function add()
    {
        $this->sma->checkPermissions(false, true);
        //$this->sma->print_arrays($_POST);
        //$this->form_validation->set_rules('email', lang('email_address'), 'is_unique[companies.email]');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('cf6', lang('Commission_%'), 'required');

        if ($this->form_validation->run('companies/add') == true) {



            $data = [
                'name'           => $this->input->post('name'),
                'email'               => $this->input->post('email'),
                'group_id'            => '5',
                'group_name'          => 'salesman',
                'company'             => $this->input->post('name'),
                'address'             => $this->input->post('address'),
                'addressEn'             => $this->input->post('addressEn'),
                'city'                => $this->input->post('city'),
                'state'               => $this->input->post('state'),
                'country'             => $this->input->post('country'),
                'phone'               => $this->input->post('phone'),
                'cf1'                 => $this->input->post('cf1'),
                'cf2'                 => $this->input->post('cf2'),
                'cf3'                 => $this->input->post('cf3'),
                'cf4'                 => $this->input->post('cf4'),
                'cf5'                 => $this->input->post('cf5'),
                'cf6'                 => $this->input->post('cf6'),

            ];


        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
            $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
            $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

        }

        //$this->sma->print_arrays($data);

		} elseif ($this->input->post('add_salesman')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('salesman');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addCompany($data)) {
            $this->session->set_flashdata('message', lang('salesman_added'));
            $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0] . '?salesman=' . $cid);
        } else {
            $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']        = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->data['price_groups']    = $this->companies_model->getAllPriceGroups();
            $this->load->view($this->theme . 'salesman/add', $this->data);
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
            $salesman = $this->input->get('salesman', true);
		//$this->sma->print_arrays($salesman);



            if ($c_id = $this->companies_model->addAjaxCompany($salesman)) {
				$this->sma->send_json(['msg' => 'success', 'result' => $c_id->id]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_add_salesman')]));
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
            admin_redirect('salesman');
        }

        if ($this->form_validation->run() == true && $this->companies_model->addAddress($data)) {
            $this->session->set_flashdata('message', lang('address_added'));
            admin_redirect('salesman');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->load->view($this->theme . 'salesman/add_address', $this->data);
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
            admin_redirect('salesman');
        }

        if ($this->form_validation->run() == true && $this->companies_model->addDeposit($data, $cdata,$payment_type)) {
            $this->session->set_flashdata('message', lang('deposit_added'));
            admin_redirect('salesman');
        } else {

			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->load->view($this->theme . 'salesman/add_deposit', $this->data);
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
                'group_id'   => 5,
            ];
            $this->load->library('ion_auth');
        } elseif ($this->input->post('add_user')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('salesman');
        }

        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $active, $notify)) {
            $this->session->set_flashdata('message', lang('user_added'));
            admin_redirect('salesman');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->load->view($this->theme . 'salesman/add_user', $this->data);
        }
    }

    public function addresses($company_id = null)
    {
        $this->sma->checkPermissions('index', true);
        $this->data['modal_js']  = $this->site->modal_js();
        $this->data['company']   = $this->companies_model->getCompanyByID($company_id);
        $this->data['addresses'] = $this->companies_model->getCompanyAddresses($company_id);
        $this->load->view($this->theme . 'salesman/addresses', $this->data);
    }

    public function salesman_actions()
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
                        if (!$this->companies_model->deleteSalesman($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('salesman_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', lang('salesman_deleted'));
                    }
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('salesman'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Commission_calculation'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Commission_%'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('phone'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('city'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('state'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('email'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('country'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('ID_Number'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('Nationality'));
                    $this->excel->getActiveSheet()->SetCellValue('L1', lang('cf1'));
                    $this->excel->getActiveSheet()->SetCellValue('M1', lang('cf2'));


                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $salesman = $this->site->getCompanyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $salesman->name);
                        $Commission_calculation="";
					if($salesman->cf5=="1")
						$Commission_calculation=lang('sales');
					if($salesman->cf5=="2")
						$Commission_calculation=lang('profit');
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $Commission_calculation);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $salesman->cf6);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $salesman->phone);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $salesman->address);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $salesman->city);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $salesman->state);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $salesman->email);
                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $salesman->country);
                        $this->excel->getActiveSheet()->SetCellValue('J' . $row, $salesman->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $salesman->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $salesman->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $salesman->cf2);


                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'salesman_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_salesman_selected'));
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
            $this->sma->send_json(['error' => 1, 'msg' => lang('salesman_x_deleted')]);
        }

        if ($this->companies_model->deleteSalesman($id)) {

            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('salesman_deleted')]);
            }
            $this->sma->send_json(['error' => 0, 'msg' => lang('salesman_deleted')]);
            admin_redirect('welcome');
        } else {
            $this->sma->send_json(['error' => 1, 'msg' => lang('salesman_x_deleted_have_sales')]);
        }
    }

    public function delete_address($id)
    {
        $this->sma->checkPermissions('delete', true);

        if ($this->companies_model->deleteAddress($id)) {
            $this->session->set_flashdata('message', lang('address_deleted'));
            admin_redirect('salesman');
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
        $this->load->view($this->theme . 'salesman/deposit_note', $this->data);
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
        $this->load->view($this->theme . 'salesman/deposits', $this->data);
    }

    public function edit($id = null)
    {
        //$this->sma->checkPermissions(false, true);
		 $this->sma->checkPermissions('edit', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
		//$this->form_validation->set_rules('email', lang('email_address'), 'required');
		$this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('cf6', lang('Commission_%'), 'required');

        $company_details = $this->companies_model->getCompanyByID($id);
        /*if ($this->input->post('email') != $company_details->email) {
            $this->form_validation->set_rules('email', lang('email_address'), 'required');
        }
		*/

        if ($this->form_validation->run() == true) {


			$data = [
                'name'           => $this->input->post('name'),
                'email'               => $this->input->post('email'),
                'group_id'            => '5',
                'group_name'          => 'salesman',
                'company'             => $this->input->post('name'),
                'address'             => $this->input->post('address'),
                'addressEn'             => $this->input->post('addressEn'),
                'city'                => $this->input->post('city'),
                'state'               => $this->input->post('state'),
                'country'             => $this->input->post('country'),
                'phone'               => $this->input->post('phone'),
                'cf1'                 => $this->input->post('cf1'),
                'cf2'                 => $this->input->post('cf2'),
                'cf3'                 => $this->input->post('cf3'),
                'cf4'                 => $this->input->post('cf4'),
                'cf5'                 => $this->input->post('cf5'),
                'cf6'                 => $this->input->post('cf6'),

            ];



        //$this->sma->print_arrays($data);
        } elseif ($this->input->post('edit_salesman')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateCompany($id, $data)) {
            $this->session->set_flashdata('message', lang('salesman_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['salesman']        = $company_details;
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
            $this->load->view($this->theme . 'salesman/edit', $this->data);
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
            admin_redirect('salesman');
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateAddress($id, $data)) {
            $this->session->set_flashdata('message', lang('address_updated'));
            admin_redirect('salesman');
        } else {
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['address']  = $this->companies_model->getAddressByID($id);
            $this->load->view($this->theme . 'salesman/edit_address', $this->data);
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
            admin_redirect('salesman');
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateDeposit($id, $data, $cdata, $payment_type)) {
            $this->session->set_flashdata('message', lang('deposit_updated'));
            admin_redirect('salesman');
        } else {
			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->data['deposit']  = $deposit;
            $this->load->view($this->theme . 'salesman/edit_deposit', $this->data);
        }
    }

    public function get_award_points($id = null)
    {
        $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
        $this->sma->send_json(['ca_points' => $row->award_points]);
    }

    public function get_salesman_details($id = null)
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
            ->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . lang('deposit_note') . "' href='" . admin_url('salesman/deposit_note/$1') . "' data-toggle='modal' data-target='#myModal2'><i class=\"fa fa-file-text-o\"></i></a> <a class=\"tip\" title='" . lang('edit_deposit') . "' href='" . admin_url('salesman/edit_deposit/$1') . "' data-toggle='modal' data-target='#myModal2'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_deposit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('salesman/delete_deposit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id')
        ->unset_column('id');
        echo $this->datatables->generate();
    }

    public function getCustomer($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
        $this->sma->send_json([['id' => $row->id, 'text' => ($row->company && $row->company != '-' ? $row->company : $row->name)]]);
    }

    public function getSalesmanCustomer($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->companies_model->getSalesmanCustomerByID($id);
		$this->sma->send_json($row);
    }

    public function getSalesman()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            //->select("id, company, name, IF(co_type=1,'".lang('local')."', '".lang('international')."') as co_type, email, phone, price_group_name, customer_group_name, vat_no, gst_no, deposit_amount, award_points")
            ->select("id, company, IF(cf5=1,'".lang('sales')."', '".lang('profit')."') as Commission_calculation, cf6 ,email, phone, cf3, cf4")
            ->from('companies')
            ->where('group_name', 'salesman');

            $this->datatables->add_column('Actions', "<div class=\"text-center\"> <a class=\"tip\" title='" . lang('Salesman_customers') . "' href='" . admin_url('salesman/salesman_customers/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-users\"></i></a> <a class=\"tip\" title='" . lang('edit_salesman') . "' href='" . admin_url('salesman/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_salesman') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('salesman/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            //$this->datatables->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . lang('list_deposits') . "' href='" . admin_url('salesman/deposits/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a> <a class=\"tip\" title='" . lang('add_deposit') . "' href='" . admin_url('salesman/add_deposit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus\"></i></a> <a class=\"tip\" title='" . lang('list_addresses') . "' href='" . admin_url('salesman/addresses/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-location-arrow\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('salesman/salesman_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a class=\"tip\" title='" . lang('edit_salesman') . "' href='" . admin_url('salesman/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_salesman') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('salesman/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

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
                    admin_redirect('salesman');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5001, ',')) !== false) {
                        $row[0]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[0]);
                        $row[1]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[1]);


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

                //$this->sma->print_arrays($arrResult);



                foreach ($arrResult as $key => $value) {
                    $salesman = [
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
                        'cf6'                 => isset($value[16]) ? trim($value[16]) : '',
                        'co_type'            => 1,
                        'group_id'            => 5,
                        'group_name'          => 'salesman',
                        'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                        'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                        'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                        'price_group_name'    => (!empty($price_group)) ? $price_group->name : null,
                    ];
                    if (empty($salesman['company']) || empty($salesman['name']) || empty($salesman['email'])) {
                        $this->session->set_flashdata('error', lang('company') . ', ' . lang('name') . ', ' . lang('email') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        admin_redirect('salesman');
                    } else {
                        if ($this->Settings->indian_gst && empty($salesman['state'])) {
                            $this->session->set_flashdata('error', lang('state') . ' ' . lang('is_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                            admin_redirect('salesman');
                        }
                        if ($salesman_details = $this->companies_model->getCompanyByEmail($salesman['email'])) {
                            if ($salesman_details->group_id == 5) {
                                $updated .= '<p>' . lang('salesman_updated') . ' (' . $salesman['email'] . ')</p>';
                                $this->companies_model->updateCompany($salesman_details->id, $salesman);
                            }
                        } else {
                            $data[] = $salesman;
                        }
                        $rw++;
                    }
                }

                // $this->sma->print_arrays($data, $updated);
            }
        } elseif ($this->input->post('import')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('salesman');
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addCompanies($data)) {
                $this->session->set_flashdata('message', lang('salesman_added') . $updated);
                admin_redirect('salesman');
            }
        } else {
            if (isset($data) && empty($data)) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_salesman'));
                }
                admin_redirect('salesman');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'salesman/import', $this->data);
        }
    }
    public function index1($warehouse_id = null)
    {


		$this->sma->print_arrays(is_php('5.4'));


    }
    public function index($action = null)
    {
        $this->sma->checkPermissions();
        //$this->sma->print_arrays($this->session);
        $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc                   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('salesman')]];
        $meta                 = ['page_title' => lang('salesman'), 'bc' => $bc];
        $this->page_construct('salesman/index', $meta, $this->data);
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
        $this->load->view($this->theme . 'salesman/users', $this->data);
    }

    public function salesman_details($company_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }
        $company = $this->companies_model->getCompanyByID($company_id);

         $this->form_validation->set_rules('salesmans_details', lang('submit'), 'required');

        if ($this->form_validation->run('companies/salesman_details') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }
             //$this->sma->print_arrays($_POST['br_option']);
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);





            $additional_data         = [
                'salesman_id' => $company_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $salesman_details_data[] = $additional_data;
             }





        } elseif ($this->input->post('salesman_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->companies_model->addCustomersDetails($salesman_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->data['mainBranches'] = $this->site->getMainBranches();
            $this->data['customerBranches'] = $this->site->getCustomerBranches($company_id);
            $this->load->view($this->theme . 'salesman/salesman_details', $this->data);
        }
    }



    public function view($id = null)
    {
        $this->sma->checkPermissions('index', true);
        $this->data['error']    = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['salesman'] = $this->companies_model->getCompanyByID($id);
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
         {

        $this->data['mainGroups'] = $this->site->getMainGroup();
        $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

         }
        $this->load->view($this->theme . 'salesman/view', $this->data);
    }


	/*---------------------reports-------------------------------*/

    public function salesman_report()
    {
        $this->sma->checkPermissions('customers');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('salesman_report')]];
        $meta = ['page_title' => lang('salesman_report'), 'bc' => $bc];
        $this->page_construct('reports/salesman', $meta, $this->data);
    }

    /*---------------------reports-------------------------------*/

    public function salesman_customers($company_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }
        $company = $this->companies_model->getCompanyByID($company_id);

         $this->form_validation->set_rules('salesman_customers', lang('submit'), 'required');

        if ($this->form_validation->run('companies/salesman_customers') == true) {

            if (empty($_POST['to'])) {
                if($this->companies_model->deleteSalesmanCustomers($company_id)){
                    $this->session->set_flashdata('message', lang('Salesman_updated'));
                    redirect($_SERVER['HTTP_REFERER']);
                }else{
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
                }
             }

            foreach ($_POST['to'] as $customer_id) {

                    $additional_data         = [
                        'salesman_id' => $company_id,
                        'customer_id'  => $customer_id,


                    ];
                    $salesman_customers_data[] = $additional_data;







             }

             if (empty($salesman_customers_data)) {
                $this->session->set_flashdata('error', lang('sEmptyTable'));
                redirect($_SERVER['HTTP_REFERER']);
            }
             //$this->sma->print_arrays($salesman_customers_data);




        } elseif ($this->input->post('salesman_customers')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->companies_model->addSalesmanCustomers($company_id,$salesman_customers_data)) {
            $this->session->set_flashdata('message', lang('Salesman_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->data['customerCompanies'] = $this->companies_model->getAllCustomerCompaniesWithOutSalesman($company_id);
            $this->data['salesmanCustomers'] = $this->companies_model->getSalesmanCustomers($company_id);
            $this->load->view($this->theme . 'salesman/salesman_customers', $this->data);
        }
    }

}
