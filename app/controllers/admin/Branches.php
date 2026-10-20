<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Branches extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if ($this->Owner || $this->Admin || $this->session->userdata('biller_id')) {}else{
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect('admin');
        }
        $this->lang->admin_load('settings', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('branches_model');
        $this->load->admin_model('products_model');

		$this->load->admin_model('db_model');
        $this->upload_path        = 'assets/uploads/';
        $this->thumbs_path        = 'assets/uploads/thumbs/';
        $this->image_types        = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size  = '1024';
    }


    public function dashboard()
	{
		//$this->sma->checkPermissions('dashboard_acc',true);
		if ($this->Settings->version == '2.3') {
			$this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
			admin_redirect('sync');
		}

		$this->data['error']     = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$this->data['sales']     = $this->db_model->getLatestSales();
		$this->data['quotes']    = $this->db_model->getLastestQuotes();
		$this->data['purchases'] = $this->db_model->getLatestPurchases();
		$this->data['transfers'] = $this->db_model->getLatestTransfers();
		$this->data['customers'] = $this->db_model->getLatestCustomers();
		$this->data['suppliers'] = $this->db_model->getLatestSuppliers();
		$this->data['chatDataTrends']  = $this->db_model->getTChartDataTrends();

		$this->data['chatData']  = $this->db_model->getPurchasesLocationChart();
		//$this->data['stock']     = $this->db_model->getStockValue();
		$this->data['bs']        = $this->db_model->getBestPurchaseschart();
		$this->data['ubs']        = $this->db_model->getUnBestPurchaseschart();
		$this->data['bc']        = $this->db_model->getBestCustomerschart();
		$this->data['ubc']        = $this->db_model->getUnBestCustomerschart();
		$lmsdate                 = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
		$lmedate                 = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
		$this->data['lmbs']      = $this->db_model->getBestSeller($lmsdate, $lmedate);
		$bc                      = [['link' => '#', 'page' => lang('dashboard')]];
		$meta                    = ['page_title' => lang('dashboard'), 'bc' => $bc];
		$this->page_construct('branches/dashboard', $meta, $this->data);
	}




    public function main_settings(){
        $this->form_validation->set_rules('update_settings', lang('update_settings'), 'trim|required');



        if ($this->form_validation->run() == true) {


            $enable_co=0;
            if($this->input->post('enable_co')==1){
                $enable_co=1;

            }

            $enable_grp=0;
            if($this->input->post('enable_grp')==1){
                $enable_grp=1;
                $enable_co=1;

            }


            //$this->sma->print_arrays($_POST);
            $data = [
				'enable_grp' => $enable_grp,
                'enable_co' => $enable_co,


            ];











            //$this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->branches_model->updateSetting($data)) {
            if (!DEMO && TIMEZONE != $data['timezone']) {
                if (!$this->write_index($data['timezone'])) {
                    $this->session->set_flashdata('error', lang('setting_updated_timezone_failed'));
                    admin_redirect('branches/main_settings');
                }
            }

            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect('branches/main_settings');
        } else {
            $this->data['error']           = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['billers']         = $this->site->getAllCompanies('biller');
            $this->data['settings']        = $this->branches_model->getSettings();
            $this->data['currencies']      = $this->branches_model->getAllCurrencies();
            $this->data['date_formats']    = $this->branches_model->getDateFormats();
            //$this->data['tax_rates']       = $this->branches_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->branches_model->getAllCustomerGroups();
            $this->data['price_groups']    = $this->branches_model->getAllPriceGroups();
            $this->data['variants']            = $this->products_model->getAllVariants();
            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                $this->data['warehouses']      = $this->site->getAllShowrooms();
            }else{
                $this->data['warehouses']      = $this->branches_model->getAllWarehouses();
            }

            $this->data['categories']          = $this->site->getAllCategories();
            //$this->data['tax_rates']           = $this->site->getAllTaxRates();
           // $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
           $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->data['brands']              = $this->site->getAllBrands();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Branches')]];
            $meta                          = ['page_title' => lang('Data_sharing'), 'bc' => $bc];
            $this->page_construct('branches/main_settings', $meta, $this->data);
        }

    }

    public function groups_settings()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('branches'), 'page' => lang('branches')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Groups_settings'), 'bc' => $bc];
        $this->page_construct('branches/groups_settings', $meta, $this->data);
    }

    public function companies_settings()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('branches'), 'page' => lang('branches')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Companies_settings'), 'bc' => $bc];
        $this->page_construct('branches/companies_settings', $meta, $this->data);
    }

    public function branches_settings()
    {
        //$this->load->library('gst');
		$this->form_validation->set_rules('auto_share_customers', lang('Automatically_share_customers'), 'required');
		$this->form_validation->set_rules('auto_share_suppliers', lang('Automatically_share_suppliers'), 'required');




		if ($this->form_validation->run() == true) {

			//'accounts_suppliers_acct' => $this->input->post('accounts_suppliers_acct'),



			$data = [
				'auto_share_customers' => $this->input->post('auto_share_customers'),
				'auto_share_suppliers' => $this->input->post('auto_share_suppliers'),



			];

		}

		if ($this->form_validation->run() == true && $this->branches_model->updateGroupSetting($data)) {


			$this->session->set_flashdata('message', lang('setting_updated'));
			admin_redirect('branches/branches_settings');
		} else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['group_settings']         = $this->branches_model->getGroupSettings();
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('branches'), 'page' => lang('branches')], ['link' => '#', 'page' => lang('Tabels')]];
            $meta                = ['page_title' => lang('Branches_settings'), 'bc' => $bc];
            $this->page_construct('branches/branches_settings', $meta, $this->data);
        }
    }

    public function branches_list()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('branches'), 'page' => lang('branches')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Branches_list'), 'bc' => $bc];
        $this->page_construct('branches/branches_list', $meta, $this->data);
    }

    public function getGroupsSettings()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select(" id, group_logo, code, name,  nameEn, phone", false)
            ->from('main_groups')
            ->group_by('id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('branches/edit_main_group/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Group') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Group') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('branches/delete_main_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function getCompaniesSettings()
    {

        $this->load->library('datatables');
        $this->datatables
        ->select("{$this->db->dbprefix('main_companies')}.id as id, {$this->db->dbprefix('main_companies')}.company_logo as lo,{$this->db->dbprefix('main_groups')}.name as grp_name, {$this->db->dbprefix('main_companies')}.code, {$this->db->dbprefix('main_companies')}.name,  {$this->db->dbprefix('main_companies')}.nameEn,  {$this->db->dbprefix('main_companies')}.phone", false)
        //->select("{$this->db->dbprefix('main_companies')}.id as id1, {$this->db->dbprefix('main_companies')}.company_logo as lo,1, 1,1,  1", false)
            ->from('main_companies')
            ->join('main_groups', 'main_groups.id=main_companies.grpId', 'left')
            //->group_by('main_companies.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('branches/edit_main_company/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Company') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Company') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('branches/delete_main_company/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function getBranchesSettings()
    {

        $this->load->library('datatables');
        $this->datatables
        ->select("{$this->db->dbprefix('main_branches')}.id as id, {$this->db->dbprefix('main_branches')}.branch_logo as lo,{$this->db->dbprefix('main_groups')}.name as grp_name, {$this->db->dbprefix('main_companies')}.name as co_name, {$this->db->dbprefix('main_branches')}.code, {$this->db->dbprefix('main_branches')}.branch_prefix, {$this->db->dbprefix('main_branches')}.name,  {$this->db->dbprefix('main_branches')}.nameEn,  {$this->db->dbprefix('main_branches')}.phone", false)
        //->select("{$this->db->dbprefix('main_companies')}.id as id1, {$this->db->dbprefix('main_companies')}.company_logo as lo,1, 1,1,  1", false)
            ->from('main_branches')
            ->join('main_groups', 'main_groups.id=main_branches.grpId', 'left')
            ->join('main_companies', 'main_companies.id=main_branches.coId', 'left')
            //->group_by('main_companies.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('branches/edit_main_branch/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Branch') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Branch') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('branches/delete_main_branch/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function add_group()
    {
		/*if(!$this->site->IsPermissions('products-add_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Code_Group'), 'trim|is_unique[main_groups.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('address', lang('address'), 'required');
        $this->form_validation->set_rules('group_logo', lang('Logo'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            if ($_FILES['group_logo']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('group_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('branches/groups_settings');
                }

                $group_logo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $group_logo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $group_logo;
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $group_logo = null;
            }
            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),
                'group_logo'            => $group_logo,
                'created_by'        => $this->session->userdata('user_id'),

            ];


        } elseif ($this->input->post('add_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('branches/groups_settings');
        }

        if ($this->form_validation->run() == true && $this->site->addMainGroup($data)) {
            $this->session->set_flashdata('message', lang('Main_group_added'));
            admin_redirect('branches/groups_settings');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['group_code'] = 'grp_'.$this->site->getGroupsAutoCode()->id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'branches/add_group', $this->data);
        }
		}
    }


    public function add_company()
    {
		/*if(!$this->site->IsPermissions('products-add_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Code_Company'), 'trim|is_unique[main_companies.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('address', lang('address'), 'required');
        $this->form_validation->set_rules('company_logo', lang('Logo'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            if ($_FILES['company_logo']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('company_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('branches/companies_settings');
                }

                $company_logo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $company_logo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $company_logo;
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $company_logo = null;
            }

            $data = ['code'      => $this->input->post('code'),
            'grpId'           => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
            'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),
                'company_logo'            => $company_logo,
                'created_by'        => $this->session->userdata('user_id'),

            ];


        } elseif ($this->input->post('add_company')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('branches/companies_settings');
        }

        if ($this->form_validation->run() == true && $this->site->addMainCompany($data)) {
            $this->session->set_flashdata('message', lang('Main_company_added'));
            admin_redirect('branches/companies_settings');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['company_code'] = 'co_'.$this->site->getCompaniesAutoCode()->id;
            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'branches/add_company', $this->data);
        }
		}
    }


    public function add_branch()
    {
		/*if(!$this->site->IsPermissions('products-add_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Code_Branch'), 'trim|is_unique[main_branches.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('address', lang('address'), 'required');
        $this->form_validation->set_rules('branch_logo', lang('Logo'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            if ($_FILES['branch_logo']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('branch_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('branches/branches_list');
                }

                $branch_logo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $branch_logo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $branch_logo;
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $branch_logo = null;
            }

            $data = ['code'      => $this->input->post('code'),
            'grpId'           => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
            'coId'           => $this->input->post('coId') ? $this->input->post('coId') : 1,
            'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),
                'branch_name'        => $this->input->post('branch_name'),
                'branch_nameEn'        => $this->input->post('branch_nameEn'),
                'branch_logo'            => $branch_logo,
                'created_by'        => $this->session->userdata('user_id'),
                'branch_prefix'        => $this->input->post('branch_prefix'),

            ];


           // $this->sma->print_arrays($data);
        } elseif ($this->input->post('add_branch')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('branches/branches_list');
        }

        if ($this->form_validation->run() == true && $this->site->addMainBranch($data)) {
            $this->session->set_flashdata('message', lang('Main_branch_added'));
            admin_redirect('branches/branches_list');
        } else {



            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['branch_code'] = 'br_'.$this->site->getBranchesAutoCode()->id;
            $this->data['branch_prefix'] = 'BR'.$this->site->getBranchesAutoCode()->id;
            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'branches/add_branch', $this->data);
        }
		}
    }

    public function edit_main_group($id = null)
    {
        //$this->sma->checkPermissions();
		/*if(!$this->site->IsPermissions('products-edit_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{
		$this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Code_Group'), 'trim|required');

        $wh_details = $this->site->getMainGroupsByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('Code_Group'), 'required|is_unique[main_groups.code]');
        }
        $this->form_validation->set_rules('address', lang('address'), 'required');
		$this->form_validation->set_rules('group_logo', lang('Logo'), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),

            ];



			if ($_FILES['group_logo']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('group_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('branches/groups_settings');
                }

                $data['group_logo'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $data['group_logo'];
                $config['new_image']      = 'assets/uploads/thumbs/' . $data['group_logo'];
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }

            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('branches/groups_settings');
        }

        if ($this->form_validation->run() == true && $this->site->updateMainGroup($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Main_group_updated'));
            admin_redirect('branches/groups_settings');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group']    = $this->site->getMainGroupsByID($id);
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'branches/edit_main_group', $this->data);
        }
		}
    }


    public function edit_main_company($id = null)
    {
        //$this->sma->checkPermissions();
		/*if(!$this->site->IsPermissions('products-edit_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{
		$this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Code_Company'), 'trim|required');

        $wh_details = $this->site->getMainCompaniesByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('Code_Company'), 'required|is_unique[main_companies.code]');
        }
        $this->form_validation->set_rules('address', lang('address'), 'required');
		$this->form_validation->set_rules('company_logo', lang('Logo'), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $data = ['code'      => $this->input->post('code'),
            'grpId'           => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
            'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),

            ];



			if ($_FILES['company_logo']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('company_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('branches/companies_settings');
                }

                $data['company_logo'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $data['company_logo'];
                $config['new_image']      = 'assets/uploads/thumbs/' . $data['company_logo'];
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }

            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_company')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('branches/companies_settings');
        }

        if ($this->form_validation->run() == true && $this->site->updateMainCompany($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Main_company_updated'));
            admin_redirect('branches/companies_settings');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['company']    = $this->site->getMainCompaniesByID($id);
            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'branches/edit_main_company', $this->data);
        }
		}
    }

    public function edit_main_branch($id = null)
    {
        //$this->sma->checkPermissions();
		/*if(!$this->site->IsPermissions('products-edit_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{


		$this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Code_Branch'), 'trim|required');

        $wh_details = $this->site->getMainBranchesByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('Code_Branch'), 'required|is_unique[main_branches.code]');
        }
        $this->form_validation->set_rules('address', lang('address'), 'required');
		$this->form_validation->set_rules('branch_logo', lang('Logo'), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $data = ['code'      => $this->input->post('code'),

            'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),
                'branch_name'        => $this->input->post('branch_name'),
                'branch_nameEn'        => $this->input->post('branch_nameEn'),
                'branch_prefix'        => $this->input->post('branch_prefix'),
            ];



			if ($_FILES['branch_logo']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('branch_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('branches/branches_list');
                }

                $data['branch_logo'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $data['branch_logo'];
                $config['new_image']      = 'assets/uploads/thumbs/' . $data['branch_logo'];
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }

        } elseif ($this->input->post('edit_branch')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('branches/branches_list');
        }

        if ($this->form_validation->run() == true && $this->site->updateMainBranch($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Main_branch_updated'));

            admin_redirect('branches/branches_list');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['branch']    = $this->site->getMainBranchesByID($id);
            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'branches/edit_main_branch', $this->data);
        }
		}
    }

    public function delete_main_group($id = null)
    {



        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if($this->site->getAllPurchaseItemsItemCountByGroup($id,'','') || $this->site->getAllSalesItemsItemCountByGroup($id,'','')){

            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_group_There_are_relevant_processes')]);

            }else{

        if ($this->site->deleteMainGroup($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Delete_Group')]);
            }
            $this->session->set_flashdata('message', lang('Delete_Group'));
            admin_redirect('welcome');
        }
    }
    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_group_There_are_relevant_processes')]);


    }

    public function delete_main_company($id = null)
    {


        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if($this->site->getAllPurchaseItemsItemCountByGroup('',$id,'') || $this->site->getAllSalesItemsItemCountByGroup('',$id,'')){

            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_company_There_are_relevant_processes')]);

            }else{

        if ($this->site->deleteMainCompany($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Delete_Company')]);
            }
            $this->session->set_flashdata('message', lang('Delete_Company'));
            admin_redirect('welcome');
        }
    }
    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_company_There_are_relevant_processes')]);



    }

    public function delete_main_branch($id = null)
    {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if($this->site->getAllPurchaseItemsItemCountByGroup('','',$id) || $this->site->getAllSalesItemsItemCountByGroup('','',$id)){

            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_branch_There_are_relevant_processes')]);

            }else{

        if ($this->site->deleteMainBranch($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Delete_Branch')]);
            }
            $this->session->set_flashdata('message', lang('Delete_Branch'));
            admin_redirect('welcome');
        }
    }
    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_branch_There_are_relevant_processes')]);

    }


    public function getCompaniesBygrpId($grpId = null)
    {
    if ($rows = $this->site->getCompaniesBygrpId($grpId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }


    public function getBranchesBycoId($coId = null)
    {
    if ($rows = $this->site->getBranchesBycoId($coId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getBillersBybrId($brId = null)
    {
    if ($rows = $this->site->getBillersBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getWarehousesBybrId($brId = null)
    {
    if ($rows = $this->site->getWarehousesBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getUnitBybrId($brId = null)
    {
    if ($rows = $this->site->getUnitBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }


    public function getCategoryBybrId($brId = null)
    {
    if ($rows = $this->site->getCategoryBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getBrandBybrId($brId = null)
    {
    if ($rows = $this->site->getBrandBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getWarehousesBycoId($coId = null)
    {
    if ($rows = $this->site->getWarehousesBycoId($coId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getCategoryBycoId($coId = null)
    {
    if ($rows = $this->site->getCategoryBycoId($coId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }


    public function getBrandBycoId($coId = null)
    {
    if ($rows = $this->site->getBrandBycoId($coId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }


    public function getWarehousesBygrpId($grpId = null)
    {
    if ($rows = $this->site->getWarehousesBygrpId($grpId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }



    public function getCategoryBygrpId($grpId = null)
    {
    if ($rows = $this->site->getCategoryBygrpId($grpId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }


    public function getBrandBygrpId($grpId = null)
    {
    if ($rows = $this->site->getBrandBygrpId($grpId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function taxs_details($tax_rate_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $tax_rate_id = $this->input->get('id');
        }
        $tax = $this->branches_model->getTaxRateByID($tax_rate_id);

        //$this->sma->print_arrays($_POST);

         $this->form_validation->set_rules('taxs_details', lang('submit'), 'required');

        if ($this->form_validation->run('branches/tax_rates') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 admin_redirect('branches/tax_rates');
             }

            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);





            $additional_data         = [
                'tax_rate_id' => $tax_rate_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $taxs_details_data[] = $additional_data;
             }





        } elseif ($this->input->post('taxs_details')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('branches/tax_rates');
        }


        if ($this->form_validation->run() == true && $this->branches_model->addTaxRatesDetails($taxs_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            admin_redirect('branches/tax_rates');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['tax']  = $tax;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['taxBranches'] = $this->site->getTaxBranches($tax_rate_id);

            $this->load->view($this->theme . 'branches/taxs_details', $this->data);
        }
    }

    public function currencies_details($currency_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $currency_id = $this->input->get('id');
        }

        $currency = $this->branches_model->getCurrencyByID($currency_id);
       // $this->sma->print_arrays($currency);
         $this->form_validation->set_rules('currencies_details', lang('submit'), 'required');

        if ($this->form_validation->run('branches/currencies_details') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 admin_redirect('branches/currencies');
             }
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);





            $additional_data         = [
                'currency_id' => $currency_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $currencies_details_data[] = $additional_data;
             }


            // $this->sma->print_arrays($brId);



        } elseif ($this->input->post('currencies_details')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('branches/currencies');
        }

        if ($this->form_validation->run() == true && $this->branches_model->addCurrenciesDetails($currencies_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            admin_redirect('branches/currencies');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['currency']  = $currency;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['currencyBranches'] = $this->site->getCurrencyBranches($currency_id);
 //$this->sma->print_arrays($currency_id);
            $this->load->view($this->theme . 'branches/currencies_details', $this->data);
        }
    }



    public function write_index($timezone)
    {
        $template_path = FCPATH . 'assets/config_dumps/index.php';
        $output_path   = FCPATH . 'index.php';
        $index_file    = file_get_contents($template_path);
        $new           = str_replace('%TIMEZONE%', $timezone, $index_file);
        $handle        = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable($output_path)) {
            if (fwrite($handle, $new)) {
                @chmod($output_path, 0644);
                return true;
            }
            @chmod($output_path, 0644);
            return false;
        }
        @chmod($output_path, 0644);
        return false;
    }

    public function getCurrenciesBybrId($brId = null)
    {
    if ($rows = $this->site->getCurrenciesBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getUserBygrpId($grpId = null)
    {
    if ($rows = $this->site->getUserBygrpId($grpId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getBillerBygrpId($grpId = null)
    {
    if ($rows = $this->site->getBillerBygrpId($grpId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getUserBycoId($coId = null)
    {
    if ($rows = $this->site->getUserBycoId($coId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getBillerBycoId($coId = null)
    {
    if ($rows = $this->site->getBillerBycoId($coId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getUserBybrId($brId = null)
    {
    if ($rows = $this->site->getUserBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getBillerBybrId($brId = null)
    {
    if ($rows = $this->site->getBillerBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getTaxBybrId($brId = null)
    {
    if ($rows = $this->site->getTaxBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getCustomersBybrId($brId = null)
    {
    if ($rows = $this->site->getCustomersBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

    public function getSuppliersBybrId($brId = null)
    {
    if ($rows = $this->site->getSuppliersBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

}
