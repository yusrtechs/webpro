<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Suppliers extends MY_Controller
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
        $this->lang->admin_load('suppliers', $this->Settings->user_language);
        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('companies_model');
		$this->load->admin_model('reports_model');


    }

    public function add()
    {
        $this->sma->checkPermissions(false, true);

        //$this->form_validation->set_rules('company', $this->lang->line('company'), 'required');
		//$this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('email', lang('email_address'), 'is_unique[companies.email]');


        if ($this->form_validation->run('companies/add') == true) {
            $data = [
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
        $is_auto_account=0;
        if($this->input->post('is_auto_account')==1){
			$is_auto_account=1;

		}



        $data  ['is_auto_account']=$is_auto_account;

        }

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
            $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
            $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

        }

        //$this->sma->print_arrays($data);


        } elseif ($this->input->post('add_supplier')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('suppliers');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }

        if ($this->form_validation->run() == true && $sid = $this->companies_model->addCompany($data)) {
            $this->session->set_flashdata('message', $this->lang->line('supplier_added'));
            $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0] . '?supplier=' . $sid);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'suppliers/add', $this->data);
        }
    }

    public function add_user($company_id = null)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }
        $company = $this->companies_model->getCompanyByID($company_id);

        $this->form_validation->set_rules('email', $this->lang->line('email_address'), 'is_unique[users.email]');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|min_length[8]|max_length[20]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('confirm_password'), 'required');

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
            admin_redirect('suppliers');
        }

        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $active, $notify)) {
            $this->session->set_flashdata('message', $this->lang->line('user_added'));
            admin_redirect('suppliers');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->load->view($this->theme . 'suppliers/add_user', $this->data);
        }
    }

    public function delete($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->companies_model->deleteSupplier($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('supplier_deleted')]);
        } else {
            $this->sma->send_json(['error' => 1, 'msg' => lang('supplier_x_deleted_have_purchases')]);
        }
    }

    public function edit($id = null)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $company_details = $this->companies_model->getCompanyByID($id);
        //if ($this->input->post('email') != $company_details->email) {
        //    $this->form_validation->set_rules('code', lang('email_address'), 'is_unique[companies.email]');
        //}

		$this->form_validation->set_rules('company', $this->lang->line('company'), 'required');
		//$this->form_validation->set_rules('name', $this->lang->line('name'), 'required');


        if ($this->form_validation->run('companies/add') == true) {
            $data = [
              //  'grpId'           => $this->input->post('grpId'),
               // 'coId'           => $this->input->post('coId'),
               // 'brId'           => $this->input->post('brId'),
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


            if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $company_details->is_auto_account==0)
            {
        $is_auto_account=0;
        if($this->input->post('is_auto_account')==1){
			$is_auto_account=1;

		}



        $data  ['is_auto_account']=$is_auto_account;

        }


        } elseif ($this->input->post('edit_supplier')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->companies_model->updateCompany($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line('supplier_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['supplier'] = $company_details;
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
          //  $this->data['branch']    = $this->site->getMainBranchesByID($id);
          if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
          {

              $this->data['mainGroups'] = $this->site->getMainGroup();
              $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
               $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
              }


            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'suppliers/edit', $this->data);
        }
    }

    public function getSupplier($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
        $this->sma->send_json([['id' => $row->id, 'text' => $row->company]]);
    }

    public function getSuppliers()
    {
        $this->sma->checkPermissions('index');

        $this->load->library('datatables');
        $this->datatables
            ->select("id, code, company, name, IF(co_type=1,'".lang('local')."', '".lang('international')."') as co_type, email, phone, city, country, vat_no, gst_no")
            ->from('companies')
            ->where('group_name', 'supplier');

        //->unset_column('id');
        /*if(SHOP)
                $this->datatables->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line('list_products') . "' href='" . admin_url('products?supplier=$1') . "'><i class=\"fa fa-list\"></i></a> <a class=\"tip\" title='" . $this->lang->line('list_users') . "' href='" . admin_url('suppliers/users/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-users\"></i></a> <a class=\"tip\" title='" . $this->lang->line('add_user') . "' href='" . admin_url('suppliers/add_user/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a> <a class=\"tip\" title='" . $this->lang->line('edit_supplier') . "' href='" . admin_url('suppliers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line('delete_supplier') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('suppliers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            else*/
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
                $this->datatables->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line('list_products') . "' href='" . admin_url('products?supplier=$1') . "'><i class=\"fa fa-list\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('suppliers/suppliers_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a class=\"tip\" title='" . $this->lang->line('edit_supplier') . "' href='" . admin_url('suppliers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line('delete_supplier') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('suppliers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            }else{
                $this->datatables->add_column('Actions', "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line('list_products') . "' href='" . admin_url('products?supplier=$1') . "'><i class=\"fa fa-list\"></i></a> <a class=\"tip\" title='" . $this->lang->line('edit_supplier') . "' href='" . admin_url('suppliers/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line('delete_supplier') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('suppliers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            }

        echo $this->datatables->generate();
    }

    public function import_csv()
    {
        $this->sma->checkPermissions('add', true);
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', $this->lang->line('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', $this->lang->line('disabled_in_demo'));
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
                    admin_redirect('suppliers');
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
                $titles  = array_shift($arrResult);
                $rw      = 2;
                $updated = '';
                $data    = [];
                foreach ($arrResult as $key => $value) {
                    $supplier = [
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
                        'group_id'    => 4,
                        'group_name'  => 'supplier',
                        'code'                 => isset($value[20]) ? trim($value[20]) : '',
                    ];
                    if (empty($supplier['company']) || empty($supplier['name'])) {
                        $this->session->set_flashdata('error', lang('company') . ', ' . lang('name') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        admin_redirect('suppliers');
                    } else {
                        if ($this->Settings->indian_gst && empty($supplier['state'])) {
                            $this->session->set_flashdata('error', lang('state') . ' ' . lang('is_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                            admin_redirect('suppliers');
                        }
                        /*if ($supplier_details = $this->companies_model->getCompanyByEmail($supplier['email'])) {
                            if ($supplier_details->group_id == 4) {
                                $updated .= '<p>' . lang('supplier_updated') . ' (' . $supplier['email'] . ')</p>';
                                $this->companies_model->updateCompany($supplier_details->id, $supplier);
                            }
                        } else {*/
                            $data[] = $supplier;
                        //}
                        $rw++;
                    }
                }

                // $this->sma->print_arrays($data, $updated);
            }
        } elseif ($this->input->post('import')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('suppliers');
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addCompaniesV2($data)) {
                $this->session->set_flashdata('message', $this->lang->line('suppliers_added') . $updated);
                admin_redirect('suppliers');
            }
        } else {
            if (isset($data) && empty($data)) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_suppliers'));
                }
                admin_redirect('suppliers');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'suppliers/import', $this->data);
        }
    }

    public function index($action = null)
    {
        $this->sma->checkPermissions();

        $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc                   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('suppliers')]];
        $meta                 = ['page_title' => lang('suppliers'), 'bc' => $bc];
        $this->page_construct('suppliers/index', $meta, $this->data);
    }

    public function suggestions($term = null, $limit = null, $a = null)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', true);
        }
        $limit           = $this->input->get('limit', true);
        $grpId  = $this->input->get('grpId', true);
        $coId  = $this->input->get('coId', true);
        $brId  = $this->input->get('brId', true);
        $result = $this->companies_model->getSupplierSuggestions($term, $limit,$grpId,$coId,$brId);
        if ($a) {
            $this->sma->send_json($result);
        }
        $rows['results'] = $result;
        $this->sma->send_json($rows);
    }

    public function supplier_actions()
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
                        if (!$this->companies_model->deleteSupplier($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('suppliers_x_deleted_have_purchases'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line('suppliers_deleted'));
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
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'suppliers_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_supplier_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
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
        $this->load->view($this->theme . 'suppliers/users', $this->data);
    }

    public function suppliers_details($company_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $company_id = $this->input->get('id');
        }
        $company = $this->companies_model->getCompanyByID($company_id);

         $this->form_validation->set_rules('suppliers_details', lang('submit'), 'required');

        if ($this->form_validation->run('companies/suppliers_details') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }
             //$this->sma->print_arrays($_POST['br_option']);
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);


                if (!$company_id) {
                    foreach ($this->companies_model->getAllSupplierCompaniesNotInbrId($brId) as $cusArr) {
                        $additional_data         = [
                            'supplier_id' => $cusArr->id,
                            'grpId'  => $branch_info->grpId,
                            'coId'  => $branch_info->coId,
                            'brId'  => $brId,

                        ];
                        $suppliers_details_data[] = $additional_data;
                    }

                }else{



                    $additional_data         = [
                        'supplier_id' => $company_id,
                        'grpId'  => $branch_info->grpId,
                        'coId'  => $branch_info->coId,
                        'brId'  => $brId,

                    ];
                    $suppliers_details_data[] = $additional_data;

                }
             }





        } elseif ($this->input->post('suppliers_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->companies_model->addSuppliersDetails($suppliers_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['company']  = $company;
            $this->data['mainBranches'] = $this->site->getMainBranches();
            $this->data['supplierBranches'] = $this->site->getSupplierBranches($company_id);
            $this->load->view($this->theme . 'suppliers/suppliers_details', $this->data);
        }
    }



    public function view($id = null)
    {
        $this->sma->checkPermissions('index', true);
        $this->data['error']    = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['supplier'] = $this->companies_model->getCompanyByID($id);

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }

        $this->load->view($this->theme . 'suppliers/view', $this->data);
    }

	/*-----------------------reports--------------*/

    public function supplier_report()
    {
        $this->sma->checkPermissions('suppliers', true,'reports');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('suppliers_report')]];
        $meta = ['page_title' => lang('suppliers_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('reports/suppliers', $meta, $this->data);
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
                    admin_redirect('suppliers');
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

                    $supplier = [
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
                        'group_id'    => 4,
                        'group_name'  => 'supplier',
                        'code'                 => isset($value['code']) ? trim($value['code']) : '',
                    ];


                    if (empty($supplier['company']) || empty($supplier['name'])) {
                        $this->session->set_flashdata('error', lang('company') . ', ' . lang('name') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        admin_redirect('suppliers');
                    } else {
                        if ($this->Settings->indian_gst && empty($supplier['state'])) {
                            $this->session->set_flashdata('error', lang('state') . ' ' . lang('is_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                            admin_redirect('suppliers');
                        }
                        /*if ($supplier_details = $this->companies_model->getCompanyByEmail($supplier['email'])) {
                            if ($supplier_details->group_id == 4) {
                                $updated .= '<p>' . lang('supplier_updated') . ' (' . $supplier['email'] . ')</p>';
                                $this->companies_model->updateCompany($supplier_details->id, $supplier);
                            }
                        } else {*/
                            $data[] = $supplier;
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
            admin_redirect('suppliers');
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addCompaniesV2($data)) {
                $this->session->set_flashdata('message', lang('suppliers_added') . $updated);
                admin_redirect('suppliers');
            }
        } else {
            if (isset($data) && empty($data)) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_suppliers'));
                }
                admin_redirect('suppliers');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'suppliers/import_excel', $this->data);
        }
    }



}
