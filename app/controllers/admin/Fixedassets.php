<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('memory_limit', '-1');

//ini_set('memory_limit','2048M');
class Fixedassets extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('products', $this->Settings->user_language);
        $this->lang->admin_load('transfers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('fixedassets_model');
        $this->load->admin_model('products_model');
		$this->load->admin_model('reports_model');
        $this->load->admin_model('settings_model');
		$this->load->admin_model('transfers_model');
		$this->load->admin_model('sales_model');
        $this->load->admin_model('accounts_model');
		$this->load->admin_model('db_model');
		$this->digital_upload_path = 'files/';
        $this->upload_path         = 'assets/uploads/';
        $this->thumbs_path         = 'assets/uploads/thumbs/';
        $this->image_types         = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types  = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size   = '1048576';
        $this->popup_attributes    = ['width' => '900', 'height' => '600', 'window_name' => 'sma_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0'];
    }

    /* ------------------------------------------------------- */

    public function dashboard()
    {
        if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            admin_redirect('sync');
        }

		if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }


        $this->data['error']     = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales']     = $this->db_model->getLatestSales();
        $this->data['quotes']    = $this->db_model->getLastestQuotes();
        $this->data['purchases'] = $this->db_model->getLatestPurchases();
        $this->data['transfers'] = $this->db_model->getLatestTransfers();
        $this->data['customers'] = $this->db_model->getLatestCustomers();
        $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
        $this->data['chatData']  = $this->db_model->getInventoryValueChartData();
		$this->data['stock']        = $warehouse ? $this->reports_model->getWarehouseStockValue($warehouse) : $this->reports_model->getStockValue();

		/*
		$this->data['sales']     = $this->db_model->getLatestSales();
        $this->data['quotes']    = $this->db_model->getLastestQuotes();
        $this->data['purchases'] = $this->db_model->getLatestPurchases();
        $this->data['transfers'] = $this->db_model->getLatestTransfers();
        $this->data['customers'] = $this->db_model->getLatestCustomers();
        $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
        $this->data['chatData']  = $this->db_model->getInventoryValueChartData();

        $this->data['stock']        = $warehouse ? $this->reports_model->getWarehouseStockValue($warehouse) : $this->reports_model->getStockValue();
       */
        //$this->data['stock']     = $this->db_model->getStockValue();
        $this->data['bs']        = $this->db_model->getBestSeller();
        $lmsdate                 = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate                 = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        $this->data['lmbs']      = $this->db_model->getBestSeller($lmsdate, $lmedate);
        $bc                      = [['link' => '#', 'page' => lang('dashboard')]];
        $meta                    = ['page_title' => lang('dashboard'), 'bc' => $bc];
        $this->page_construct('fixedassets/dashboard', $meta, $this->data);
    }

    public function group()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Fixed_assets')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Fixed_assets_group'), 'bc' => $bc];
        $this->page_construct('fixedassets/group', $meta, $this->data);
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
        $this->form_validation->set_rules('code', lang('Code_Group'), 'trim|is_unique[ast_group.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');



        if ($this->form_validation->run() == true) {


            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'created_by'        => $this->session->userdata('user_id'),

            ];


        } elseif ($this->input->post('add_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/group');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->addGroup($data)) {
            $this->session->set_flashdata('message', lang('Main_group_added'));
            admin_redirect('fixedassets/group');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['group_code'] = 'grp_'.$this->fixedassets_model->getGroupAutoCode()->id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/add_group', $this->data);
        }
		}
    }

    public function edit_group($id = null)
    {
        //$this->sma->checkPermissions();
		{
		$this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Code_Group'), 'trim|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');

        $wh_details = $this->fixedassets_model->getGroupByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('Code_Group'), 'required|is_unique[ast_group.code]');
        }


        if ($this->form_validation->run() == true) {

            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),

            ];





            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/group');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->updateGroup($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Main_group_updated'));
            admin_redirect('fixedassets/group');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group']    = $this->fixedassets_model->getGroupByID($id);
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/edit_group', $this->data);
        }
		}
    }

    public function delete_group($id = null)
    {



        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        /*if($this->site->getAllPurchaseItemsItemCountByGroup($id,'','') || $this->site->getAllSalesItemsItemCountByGroup($id,'','')){

            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_group_There_are_relevant_processes')]);

            }else*/{

        if ($this->fixedassets_model->deleteGroup($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Delete_Group')]);
            }
            $this->session->set_flashdata('message', lang('Delete_Group'));
            admin_redirect('welcome');
        }
    }
    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_group_There_are_relevant_processes')]);


    }

    public function getGroup()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select(" id, code, name,  nameEn", false)
            ->from('ast_group')
            ->group_by('id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('fixedassets/edit_group/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Group') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Group') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('fixedassets/delete_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }


    public function parent_classes()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Fixed_assets')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Parent_classes'), 'bc' => $bc];
        $this->page_construct('fixedassets/parent_classes', $meta, $this->data);
    }

    public function getParentClasses()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select(" id, code, name,  nameEn", false)
            ->from('ast_parent_classes')
            ->group_by('id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('fixedassets/edit_parent_classes/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_parent_classes') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_parent_classes') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('fixedassets/delete_parent_classes/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function add_parent_classes()
    {
		/*if(!$this->site->IsPermissions('products-add_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|is_unique[ast_parent_classes.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');



        if ($this->form_validation->run() == true) {


            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'created_by'        => $this->session->userdata('user_id'),

            ];


        } elseif ($this->input->post('add_parent_classes')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/parent_classes');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->addParentClasses($data)) {
            $this->session->set_flashdata('message', lang('Parent_classes_added'));
            admin_redirect('fixedassets/parent_classes');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['parent_classes_code'] = 'pc_'.$this->fixedassets_model->getParentClassesAutoCode()->id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/add_parent_classes', $this->data);
        }
		}
    }

    public function edit_parent_classes($id = null)
    {
        //$this->sma->checkPermissions();
		{
		$this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');

        $wh_details = $this->fixedassets_model->getParentClassesByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[ast_parent_classes.code]');
        }


        if ($this->form_validation->run() == true) {

            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),

            ];





            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_parent_classes')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/parent_classes');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->updateParentClasses($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Parent_classes_updated'));
            admin_redirect('fixedassets/parent_classes');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['parent_classes']    = $this->fixedassets_model->getParentClassesByID($id);
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/edit_parent_classes', $this->data);
        }
		}
    }

    public function delete_parent_classes($id = null)
    {



        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
       {

        if ($this->fixedassets_model->deleteParentClasses($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Delete_parent_classes')]);
            }
            $this->session->set_flashdata('message', lang('Delete_parent_classes'));
            admin_redirect('welcome');
        }
    }
    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_parent_classes_There_are_relevant_processes')]);


    }

    public function sub_classes()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Fixed_assets')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Sub_classes'), 'bc' => $bc];
        $this->page_construct('fixedassets/sub_classes', $meta, $this->data);
    }

    public function getSubClasses()
    {

        $this->load->library('datatables');
        $this->datatables
            //->select(" id, code, name,  nameEn,ast_parent_classes.name as parentName", false)
            ->select("{$this->db->dbprefix('ast_sub_classes')}.id as id, {$this->db->dbprefix('ast_sub_classes')}.code, {$this->db->dbprefix('ast_sub_classes')}.name,  {$this->db->dbprefix('ast_sub_classes')}.nameEn,{$this->db->dbprefix('ast_parent_classes')}.name as parent_name", false)
            ->from('ast_sub_classes')
            ->join('ast_parent_classes', 'ast_parent_classes.id=ast_sub_classes.parent_id', 'left')
            //->group_by('id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('fixedassets/edit_sub_classes/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_sub_classes') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_sub_classes') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('fixedassets/delete_sub_classes/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function add_sub_classes()
    {
		{
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|is_unique[ast_sub_classes.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('parent_id', lang('Parent_class'), 'required');



        if ($this->form_validation->run() == true) {


            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'created_by'        => $this->session->userdata('user_id'),
                'parent_id'        => $this->input->post('parent_id'),

            ];


        } elseif ($this->input->post('add_sub_classes')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/sub_classes');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->addSubClasses($data)) {
            $this->session->set_flashdata('message', lang('Sub_classes_added'));
            admin_redirect('fixedassets/sub_classes');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['sub_classes_code'] = 'sc_'.$this->fixedassets_model->getSubClassesAutoCode()->id;
            $this->data['parentClasses'] = $this->fixedassets_model->getParentClasses();
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/add_sub_classes', $this->data);
        }
		}
    }

    public function edit_sub_classes($id = null)
    {
        //$this->sma->checkPermissions();
		{
		$this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('parent_id', lang('Parent_class'), 'required');

        $wh_details = $this->fixedassets_model->getSubClassesByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[ast_sub_classes.code]');
        }


        if ($this->form_validation->run() == true) {

            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'parent_id'        => $this->input->post('parent_id'),
            ];





            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_sub_classes')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/sub_classes');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->updateSubClasses($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Sub_classes_updated'));
            admin_redirect('fixedassets/sub_classes');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['sub_classes']    = $this->fixedassets_model->getSubClassesByID($id);
            $this->data['id']           = $id;
            $this->data['parentClasses'] = $this->fixedassets_model->getParentClasses();
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/edit_sub_classes', $this->data);
        }
		}
    }

    public function delete_sub_classes($id = null)
    {



        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
       {

        if ($this->fixedassets_model->deleteSubClasses($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Sub_classes_deleted')]);
            }
            $this->session->set_flashdata('message', lang('Sub_classes_deleted'));
            admin_redirect('welcome');
        }
    }
    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_sub_classes_There_are_relevant_processes')]);


    }

    public function index($warehouse_id = null)
    {
        $accounts_settings         = $this->accounts_model->getAccountsSettings();
        if($accounts_settings->isAutoDepreciation==1 && $accounts_settings->depreciation_period=="monthly"){
            $startDate = new DateTime($accounts_settings->latest_depreciation_date);
            $months = $this->getMonthStartsBetween($startDate->format("Y-m-d"));
            //$months = $this->getMonthStartsBetween("2025-05-15");
            //$this->sma->print_arrays($months);
            if(!empty($months)){
                foreach($months as $month){
                    $old_accounting_period = $this->fixedassets_model->getDepreciationsByYear($month['start'],$month['end']);
                    foreach ($old_accounting_period  as $fixed_asset_details) {
        				if($fixed_asset_details->accumulated_value!=0){

		        			$fixed_asset_details->accountIDEX= $fixed_asset_details->accountIDEX ? $fixed_asset_details->accountIDEX : $this->site->getAccountsSettings()->depreciation_expenses_acct;
				        	$fixed_asset_details->accountID= $fixed_asset_details->accountID ? $fixed_asset_details->accountID : $this->site->getAccountsSettings()->depreciation_allowance_acct;

            				//$this->sma->print_arrays($fixed_asset_details);
			                $entryDescription="Fixed_asset_depreciation#".$fixed_asset_details->code." ".$period_old_year;
                            $entry_type="Fixed_asset_depreciation";

                            $this->site->createEntryMultiSide($fixed_asset_details->accountIDEX,$fixed_asset_details->accountID,$this->sma->formatDecimal($fixed_asset_details->accumulated_value),$this->sma->formatDecimal($fixed_asset_details->accumulated_value),$fixed_asset_details->code,$entry_type,null,null,null,null,$month['end'].' 00:00:00',$entryDescription);
				        }
			        }
                    //$this->sma->print_arrays($old_accounting_period);
                }
                $startDate  = date('Y-m-d H:i:s');
                $startDate   = new DateTime($startDate);

                $startDate->modify('first day of next month');
                $latest_depreciation_date = $startDate->format("Y-m-d H:i:s");
		        $this->accounts_model->updateSetting(['latest_depreciation_date' => $latest_depreciation_date]);
                //$this->sma->print_arrays($months);
            }
        }else if($accounts_settings->isAutoDepreciation==1 && $accounts_settings->depreciation_period=="annual"){
            $startDate = new DateTime($accounts_settings->latest_depreciation_date);
            $months = $this->getYearStartsBetween($startDate->format("Y-m-d"));
            //$months = $this->getYearStartsBetween("2025-05-15");
            //$this->sma->print_arrays($months);
            if(!empty($months)){
                foreach($months as $month){
                    $old_accounting_period = $this->fixedassets_model->getDepreciationsByYear($month['start'],$month['end']);
                    foreach ($old_accounting_period  as $fixed_asset_details) {
        				if($fixed_asset_details->accumulated_value!=0){

		        			$fixed_asset_details->accountIDEX= $fixed_asset_details->accountIDEX ? $fixed_asset_details->accountIDEX : $this->site->getAccountsSettings()->depreciation_expenses_acct;
				        	$fixed_asset_details->accountID= $fixed_asset_details->accountID ? $fixed_asset_details->accountID : $this->site->getAccountsSettings()->depreciation_allowance_acct;

            				//$this->sma->print_arrays($fixed_asset_details);
			                $entryDescription="Fixed_asset_depreciation#".$fixed_asset_details->code." ".$period_old_year;
                            $entry_type="Fixed_asset_depreciation";

                            $this->site->createEntryMultiSide($fixed_asset_details->accountIDEX,$fixed_asset_details->accountID,$this->sma->formatDecimal($fixed_asset_details->accumulated_value),$this->sma->formatDecimal($fixed_asset_details->accumulated_value),$fixed_asset_details->code,$entry_type,null,null,null,null,$month['end'].' 00:00:00',$entryDescription);
				        }
			        }
                    //$this->sma->print_arrays($old_accounting_period);
                }
                $startDate  = date('Y-m-d H:i:s');
                $startDate   = new DateTime($startDate);

                $startDate->modify('first day of next month');
                $latest_depreciation_date = $startDate->format("Y-m-d H:i:s");
                //$this->sma->print_arrays($latest_depreciation_date);
		        $this->accounts_model->updateSetting(['latest_depreciation_date' => $latest_depreciation_date]);
                //$this->sma->print_arrays($months);
            }
        }



        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Fixed_assets_list')]];
        $meta                   = ['page_title' => lang('Fixed_assets_list'), 'bc' => $bc];
        $this->page_construct('fixedassets/index', $meta, $this->data);
    }

    public function getFixedAssets()
    {




        $detail_link = anchor('admin/fixedassets/fixed_asset_details/$1', '<i class="fa fa-file-text-o"></i> ' . lang('Fixed_asset_details'));
        $edit_link = anchor('admin/fixedassets/edit_fixed_asset/$1', '<i class="fa fa-edit"></i> ' . lang('Edit_fixed_asset'));
        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('Delete_fixed_asset') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . admin_url('fixedassets/delete_fixed_asset/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('Delete_fixed_asset') . '</a>';

		$action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $edit_link . '</li>
            <li>' . $detail_link . '</li>';

        $action .= '<li><a href="' . base_url() . 'assets/uploads/$2" data-type="image" data-toggle="lightbox"><i class="fa fa-file-photo-o"></i> '
            . lang('view_image') . '</a></li>
            <li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';

		$this->load->library('datatables');

		//if ($warehouse_id) {
            $this->datatables
            ->select($this->db->dbprefix('ast_fixed_assets') . ".id as astid, {$this->db->dbprefix('ast_fixed_assets')}.id as astid2, {$this->db->dbprefix('ast_fixed_assets')}.image as image,  {$this->db->dbprefix('ast_fixed_assets')}.code as code, {$this->db->dbprefix('ast_fixed_assets')}.name as name, {$this->db->dbprefix('ast_group')}.name as group_name, {$this->db->dbprefix('ast_fixed_assets')}.active as active, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by", false)
            ->from('ast_fixed_assets')
            ->join('ast_group', 'ast_group.id=ast_fixed_assets.group_id', 'left')
            ->join('users', 'users.id=ast_fixed_assets.created_by', 'left');
            $this->datatables->edit_column('active', '$1__$2', 'active, astid');
        $this->datatables->add_column('Actions', $action, 'astid, image');
        echo $this->datatables->generate();
    }

    public function add_fixed_asset()
    {
       // $this->sma->checkPermissions('add_fixed_asset',true);
       $this->form_validation->set_rules('code', lang('code'), 'trim|is_unique[ast_fixed_assets.code]|required');
       $this->form_validation->set_rules('name', lang('name'), 'required');
       $this->form_validation->set_rules('unit_id', lang('unit'), 'required');




        if ($this->form_validation->run() == true) {

			$created_date  = date('Y-m-d H:i:s');

            $data     = [
                'created_at'              => $created_date,
                'code'              => $this->input->post('code'),
                'name'              => $this->input->post('name'),
                'group_id'              => $this->input->post('group_id'),
                'unit_id'              => $this->input->post('unit_id'),
                'location'              => $this->input->post('location'),
                'measurement'              => $this->input->post('measurement'),
                'color'              => $this->input->post('color'),
                'man_company'              => $this->input->post('man_company'),
                'quality'              => $this->input->post('quality'),
                'country_origin'              => $this->input->post('country_origin'),
                'style'              => $this->input->post('style'),
                'specifications'   => $_POST['specifications'],
                'status'    => $this->input->post('status'),
				'active'    => $this->input->post('active'),
				'created_by'          => $this->session->userdata('user_id'),


            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();
            }

			//
            //$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_fixed_asset')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->addFixedAsset($data)) {
            $this->session->set_flashdata('message', lang('Fixed_asset_added_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->data['groups'] = $this->fixedassets_model->getGroups();
            $this->data['fixed_asset_code'] = 'AST'. rand(1000000000,9999999999);
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            //$this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/add_fixed_asset', $this->data);
        }
    }

    public function edit_fixed_asset($id = null)
    {
        //$this->sma->checkPermissions('bom_update',true);
        $this->load->helper('security');
        //$warehouses = $this->site->getAllWarehouses();
        //$this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


		$fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($id);

		if (!$id || !$fixed_asset_details) {
            $this->session->set_flashdata('error', lang('Fixed_asset_not_found'));
            admin_redirect('fixedassets');
        }

        //$this->sma->print_arrays($fixed_asset_details);






            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['fixed_asset_details']     = $fixed_asset_details;
			$this->data['created_by']  = $this->site->getUser($fixed_asset_details->created_by);
			$this->data['updated_by']  = $fixed_asset_details->updated_by ? $this->site->getUser($fixed_asset_details->updated_by) : null;
			//$this->data['warehouses']  = $fixed_asset_details->product_id ? $this->products_model->getAllWarehousesWithPQ($fixed_asset_details->product_id) : null;
			//$this->data['product']  = $fixed_asset_details->product_id ?$this->site->getProductByID($fixed_asset_details->product_id) : null;
			$this->data['parentClasses'] = $this->fixedassets_model->getParentClasses();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->data['units']          = $this->site->getUnitByID($fixed_asset_details->unit_id);
            $this->data['accounts'] = $this->accounts_model->getAccounts();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $this->data['groups'] = $this->fixedassets_model->getGroups();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Fixed_assets_list')], ['link' => '#', 'page' => lang('Edit_fixed_asset')]];
            $meta                              = ['page_title' => lang('Edit_fixed_asset'), 'bc' => $bc];
            $this->page_construct('fixedassets/edit_fixed_asset', $meta, $this->data);




    }


    public function updateFixedAssetByAjax()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
		if ($this->input->post('token', true) && $this->input->post('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $fixedasset = $this->input->post('fixedasset', true);
            $id = $this->input->post('id', true);


			$updated_at  = date('Y-m-d H:i:s');

            $data     = [
                'updated_at'              => $updated_at,
                'code'              => $this->input->post('code', true),
                'name'              => $this->input->post('name', true),
                'group_id'              => $this->input->post('group_id'),
                'location'              => $this->input->post('location'),
                'measurement'              => $this->input->post('measurement'),
                'color'              => $this->input->post('color'),
                'man_company'              => $this->input->post('man_company'),
                'quality'              => $this->input->post('quality'),
                'country_origin'              => $this->input->post('country_origin'),
                'style'              => $this->input->post('style'),
                'specifications'   => $_POST['specifications'],
                'updated_by'          => $this->session->userdata('user_id'),
                'active'              => $this->input->post('active'),
                'unit_id'              => $this->input->post('unit_id'),


            ];

			//$this->sma->send_json(['msg' => 'success','res'=>$data,'res1'=>$_POST]);


            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;


                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();
            }

			//$this->sma->print_arrays($data);


        if ($this->fixedassets_model->updateFixedAsset($id,$data)) {
                $this->sma->send_json(['msg' => 'success']);
            } else {
                exit(json_encode(['msg' => lang('failed_to_save')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }


    public function fixed_asset_details($id = null)
    {
        //$this->sma->checkPermissions('bom_details',true);
        $this->load->helper('security');
        //$warehouses = $this->site->getAllWarehouses();
        //$this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


		$fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($id);

		if (!$id || !$fixed_asset_details) {
            $this->session->set_flashdata('error', lang('Fixed_asset_not_found'));
            admin_redirect('fixedassets');
        }

        //$this->sma->print_arrays($fixed_asset_details);






            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['fixed_asset_details']     = $fixed_asset_details;
			$this->data['created_by']  = $this->site->getUser($fixed_asset_details->created_by);
			$this->data['updated_by']  = $fixed_asset_details->updated_by ? $this->site->getUser($fixed_asset_details->updated_by) : null;
			//$this->data['warehouses']  = $fixed_asset_details->product_id ? $this->products_model->getAllWarehousesWithPQ($fixed_asset_details->product_id) : null;
			//$this->data['product']  = $fixed_asset_details->product_id ?$this->site->getProductByID($fixed_asset_details->product_id) : null;
			$this->data['parentClasses'] = $this->fixedassets_model->getParentClasses();
            $this->data['base_units']          = $this->site->getUnitByID($fixed_asset_details->unit_id);
            $this->data['accounts'] = $this->accounts_model->getAccounts();
            //$this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $this->data['groups'] = $this->fixedassets_model->getGroupByID($fixed_asset_details->group_id);
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['supplier']           = $this->site->getCompanyByID($fixed_asset_details->supplier_id);
            //$this->data['combo_items']         = ($id && $this->data['product']->type == 'combo') ? $this->products_model->getProductComboItems($id) : null;
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Fixed_assets_list')], ['link' => '#', 'page' => lang('Fixed_asset_details')]];
            $meta                              = ['page_title' => lang('Fixed_asset_details'), 'bc' => $bc];
            $this->page_construct('fixedassets/fixed_asset_details', $meta, $this->data);




    }

    public function getSubClassesByParentId($parent_id = null)
    {


        if ($rows = $this->fixedassets_model->getSubClassesByParentId($parent_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }

        echo $data;
    }


    public function updateFixedAssetInfoByAjax()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
		if ($this->input->post('token', true) && $this->input->post('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $fixedasset = $this->input->post('fixedasset', true);
            $id = $this->input->post('id', true);


			$updated_at  = date('Y-m-d H:i:s');

            $data     = [
                'updated_at'              => $updated_at,
                'sub_class_id'              => $this->input->post('sub_class_id', true),
                'parent_class_id'              => $this->input->post('parent_class_id', true),
                'branch_name'              => $this->input->post('branch_name'),
                'accountID'              => $this->input->post('accountID'),
                'accountIDEX'              => $this->input->post('accountIDEX'),


                'updated_by'          => $this->session->userdata('user_id'),


            ];

			//$this->sma->send_json(['msg' => 'success','res'=>$data,'res1'=>$_POST]);




			//$this->sma->print_arrays($data);


        if ($this->fixedassets_model->updateFixedAsset($id,$data)) {
                $this->sma->send_json(['msg' => 'success']);
            } else {
                exit(json_encode(['msg' => lang('failed_to_save')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }


    public function addPurchaseItem()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
		if ($this->input->post('token', true) && $this->input->post('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $fixedasset = $this->input->post('fixedasset', true);
            $id = $this->input->post('id', true);


			$updated_at  = date('Y-m-d H:i:s');
            $purchase_date = $this->sma->fld(trim($this->input->post('purchase_date')));

            $data     = [
                'updated_at'              => $updated_at,
                'purchase_date'              => $purchase_date,
                'supplier_id'              => $this->input->post('supplier_id', true),
                'grand_total'              => $this->input->post('grand_total', true),
                'total_tax'              => $this->input->post('total_tax'),
                'tax_id'              => $this->input->post('tax_id'),
                'value'              => $this->input->post('value'),
                'quantity'              => $this->input->post('quantity'),
                'cost'              => $this->input->post('cost'),
                'updated_by'          => $this->session->userdata('user_id'),


            ];

			//$this->sma->send_json(['msg' => 'success','res'=>$data,'res1'=>$_POST]);




			//$this->sma->print_arrays($data);


        if ($this->fixedassets_model->updateFixedAsset($id,$data)) {
                $this->sma->send_json(['msg' => 'success']);
            } else {
                exit(json_encode(['msg' => lang('failed_to_save')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }

    public function accounts_setting()
	{
		$this->load->library('gst');
		$this->form_validation->set_rules('depreciation_allowance_acct', lang('Depreciation_allowance_account'), 'required');
		$this->form_validation->set_rules('depreciation_expenses_acct', lang('Depreciation_expenses_account'), 'required');
		$this->form_validation->set_rules('profits_sale_fixed_assets_acct', lang('Profits_sale_fixed_assets_account'), 'required');
		$this->form_validation->set_rules('losses_on_fixed_assets_acct', lang('Losses_on_fixed_assets_account'), 'required');
		$this->form_validation->set_rules('revaluation_profite_fixed_assets_acct', lang('Revaluation_profite_fixed_assets_account'), 'required');
		$this->form_validation->set_rules('revaluation_losses_fixed_assets_acct', lang('Revaluation_losses_fixed_assets_account'), 'required');




		if ($this->form_validation->run() == true) {

			//'accounts_suppliers_acct' => $this->input->post('accounts_suppliers_acct'),



			$data = [
				'depreciation_allowance_acct' => $this->input->post('depreciation_allowance_acct'),
				'depreciation_expenses_acct' => $this->input->post('depreciation_expenses_acct'),
				'profits_sale_fixed_assets_acct' => $this->input->post('profits_sale_fixed_assets_acct'),
				'losses_on_fixed_assets_acct' => $this->input->post('losses_on_fixed_assets_acct'),
				'revaluation_profite_fixed_assets_acct' => $this->input->post('revaluation_profite_fixed_assets_acct'),
				'revaluation_losses_fixed_assets_acct' => $this->input->post('revaluation_losses_fixed_assets_acct'),
                'isAutoDepreciation' => $this->input->post('isAutoDepreciation'),
                'depreciation_period' => $this->input->post('depreciation_period'),




			];

		}

		if ($this->form_validation->run() == true && $this->accounts_model->updateSetting($data)) {


			$this->session->set_flashdata('message', lang('setting_updated'));
			admin_redirect('fixedassets/accounts_setting');
		} else {
			$this->data['error']           = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['accounts']         = $this->accounts_model->getAllDetailsAccounts();
			$this->data['masterAccounts']         = $this->accounts_model->getAllMastersAccounts();

			$this->data['accounts_settings']         = $this->accounts_model->getAccountsSettings();

			$bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Accounts_settings')]];
			$meta                          = ['page_title' => lang('Accounts_settings'), 'bc' => $bc];
			$this->page_construct('fixedassets/accounts_setting', $meta, $this->data);
		}
	}

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        $warehouse_id = $this->input->get('warehouse_id', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        //$rows = $this->products_model->getProductNames2($term);
        $rows = $this->sales_model->getProductNames2Combo($term, $warehouse_id);

		if ($rows) {
            foreach ($rows as $row) {


				$rows2=$this->site->getProductByCode($row->code);
				if($this->site->getProduct_avg_cost($rows2->id,$warehouse_id))
				$avg_cost=$this->site->getProduct_avg_cost($rows2->id,$warehouse_id);
			    else{
				$avg_cost=0;
				}


                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1,'unit_id'=>$row->unit,'unit_name'=>$this->site->getUnitByID($row->unit)->name,'avg_cost' => $avg_cost);
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    public function addDepreciation()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
		if ($this->input->post('token', true) && $this->input->post('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $fixedasset = $this->input->post('fixedasset', true);
            $id = $this->input->post('id', true);


			$updated_at  = date('Y-m-d H:i:s');
            $start_date = $this->sma->fld(trim($this->input->post('start_date')));



            $data     = [
                'updated_at'              => $updated_at,
                'start_date'              => $start_date,
                'useful_life'              => $this->input->post('useful_life', true),
                'scrap_value'              => $this->input->post('scrap_value', true),
                'depreciation_ratio'              => $this->sma->formatDecimal($this->input->post('depreciation_ratio'),2),
                'depreciation_amount'              => $this->input->post('depreciation_amount'),
                'depreciation_amount_per_year'              => $this->input->post('depreciation_amount_per_year'),
                'cost'              => $this->input->post('cost', true),
                'quantity'              => $this->input->post('quantity', true),
                'value'              => $this->input->post('value', true),
                'updated_by'          => $this->session->userdata('user_id'),


            ];

			//$this->sma->send_json(['msg' => 'success','res'=>$data,'res1'=>$_POST]);




			//$this->sma->print_arrays($data);


        if ($this->fixedassets_model->updateFixedAsset($id,$data)) {


            $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($id);

            $useful_life=($fixed_asset_details->useful_life * 12);
            $month = date("m",strtotime($start_date));
            $year = date("Y",strtotime($start_date));

            $period_date=$year."-".$month."-01";
            $start_value=$fixed_asset_details->value;
            $amount=(($fixed_asset_details->value - $fixed_asset_details->scrap_value)/$fixed_asset_details->useful_life) / 12;
            $accumulated_value=0;


            for($i=0;$i<$useful_life;$i++){

                $end_value=$start_value-$amount;
                $accumulated_value=$accumulated_value+$amount;

                $depreciation = [
                    'fixed_asset_id'        => $id,
                    'period_date'      => $period_date,
                    'start_value'      => $this->sma->formatDecimal($start_value),
                    'depreciation_ratio'      => $this->sma->formatDecimal($fixed_asset_details->depreciation_ratio),
                    'depreciation_amount'      => $this->sma->formatDecimal($amount),
                    'accumulated_value'      => $this->sma->formatDecimal($accumulated_value),
                    'end_value'      => $this->sma->formatDecimal($end_value),

                ];

                $period_date = date('Y-m-d', strtotime("+1 month", strtotime($period_date)));
                $start_value=$end_value;

                $depreciations[] = $depreciation;

            }

            if($depreciations){
                $this->fixedassets_model->deleteDepreciations($id);
                $this->fixedassets_model->addDepreciations($depreciations);
            }

            /*$fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($id);

            $month = date("m",strtotime($start_date));

            $mounthCount=1;
            if($month=='01'){
                $mounthCount=12;
            }

            if($month=='02'){
                $mounthCount=11;
            }
            if($month=='03'){
                $mounthCount=10;
            }
            if($month=='04'){
                $mounthCount=9;
            }
            if($month=='05'){
                $mounthCount=8;
            }
            if($month=='06'){
                $mounthCount=7;
            }
            if($month=='07'){
                $mounthCount=6;
            }
            if($month=='08'){
                $mounthCount=5;
            }
            if($month=='09'){
                $mounthCount=4;
            }
            if($month=='10'){
                $mounthCount=3;
            }
            if($month=='11'){
                $mounthCount=2;
            }

            $amount=$mounthCount* (($fixed_asset_details->value - $fixed_asset_details->scrap_value)/$fixed_asset_details->useful_life);

            $entryDescription="Fixed_asset_depreciation#".$fixed_asset_details->code;
            $entry_type="Fixed_asset_depreciation";

            $this->site->createEntryMultiSide($fixed_asset_details->accountIDEX,$fixed_asset_details->accountID,$this->sma->formatDecimal($amount),$this->sma->formatDecimal($amount),$fixed_asset_details->code,$entry_type,null,null,null,null,$updated_at,$entryDescription);
        */



                $this->sma->send_json(['msg' => 'success']);
            } else {
                exit(json_encode(['msg' => lang('failed_to_save')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }


    public function delete_fixed_asset($id = null)
    {
        //$this->sma->checkPermissions('deletebom', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($id);


		//if($fixed_asset_details->isHaveEntry==0)
        {
			if ($this->fixedassets_model->delete_fixed_asset($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Fixed_asset_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Fixed_asset_deleted_successfully'));
            admin_redirect('fixedassets');
        }

		}

		$this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_fixed_asset_There_are_relevant_processes')]);


    }

    public function deactivate($id = null)
    {
        //$this->sma->checkPermissions('users', true);
        //$id = $this->config->item('use_mongodb', 'ion_auth') ? (string)$id : (int)$id;
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->form_validation->set_rules('confirm', lang('confirm'), 'required');

        if ($this->form_validation->run() == false) {
            if ($this->input->post('deactivate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                //$this->data['csrf']     = $this->_get_csrf_nonce();
                $this->data['fixedasset']     = $this->fixedassets_model->getFixedAssetByID($id);
                //$this->sma->print_arrays($this->data['fixedasset']);
                $this->data['modal_js'] = $this->site->modal_js();
                $this->load->view($this->theme . 'fixedassets/deactivate_fixedasset', $this->data);
            }
        } else {
            if ($this->input->post('confirm') == 'yes') {
                if ($id != $this->input->post('id')) {
                    show_error(lang('error_csrf'));
                }

                $updated_at  = date('Y-m-d H:i:s');

                $data     = [
                    'updated_at'              => $updated_at,

                    'updated_by'          => $this->session->userdata('user_id'),
                    'active'              => 0,



                ];
                    $this->fixedassets_model->updateFixedAsset($id,$data);
                    $this->session->set_flashdata('message', lang('Fixed_asset_deactivate_successful'));

            }

            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function activate($id = null)
    {
        //$this->sma->checkPermissions('users', true);
        //$id = $this->config->item('use_mongodb', 'ion_auth') ? (string)$id : (int)$id;
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->form_validation->set_rules('confirm', lang('confirm'), 'required');

        if ($this->form_validation->run() == false) {
            if ($this->input->post('activate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                //$this->data['csrf']     = $this->_get_csrf_nonce();
                $this->data['fixedasset']     = $this->fixedassets_model->getFixedAssetByID($id);
                //$this->sma->print_arrays($this->data['fixedasset']);
                $this->data['modal_js'] = $this->site->modal_js();
                $this->load->view($this->theme . 'fixedassets/activate_fixedasset', $this->data);
            }
        } else {
            if ($this->input->post('confirm') == 'yes') {
                if ($id != $this->input->post('id')) {
                    show_error(lang('error_csrf'));
                }

                $updated_at  = date('Y-m-d H:i:s');

                $data     = [
                    'updated_at'              => $updated_at,

                    'updated_by'          => $this->session->userdata('user_id'),
                    'active'              => 1,



                ];
                    $this->fixedassets_model->updateFixedAsset($id,$data);
                    $this->session->set_flashdata('message', lang('Fixed_asset_activate_successful'));

            }

            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function depreciation_report($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		//$this->sma->checkPermissions('depreciation_report', true);

            $this->data['groups'] = $this->fixedassets_model->getGroups();
            $this->data['fixedAssets'] = $this->fixedassets_model->getFixedAssets();




		/*if ($id){
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);

			}
			else {
				$this->data['accountList'] = $this->accounts_model->getAccounts();
			}
			*/
		//$data['userData']   = $this->session->userdata();
		if ($pdf || $xls) {



			$group_id     = $this->input->get('group_id') ? $this->input->get('group_id') : null;
			$fixed_asset_id     = $this->input->get('fixed_asset_id') ? $this->input->get('fixed_asset_id') : null;
			$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;

            if (empty($_GET)){

            }else{
        //$this->sma->print_arrays($_POST);
            $fixedAssetList  = $this->fixedassets_model->getFixedAssets($fixed_asset_id,$group_id);
            if($group_id){
                $groupName  = $this->fixedassets_model->getGroupByID($group_id)->name;
            }
            }

			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';

			}





			//$this->sma->print_arrays($_POST);

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Depreciation_report'));

			$this->excel->getActiveSheet()->mergeCells('A1:F1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('Depreciation_report'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 25,
					'name'  => 'Cairo-Bold'
				)

			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);




			$this->excel->getActiveSheet()->mergeCells('A2:F2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

            $row = 3;
						if($group_id){


			$this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row );
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Fixed_assets_group').' ( '.$groupName.' ) ');
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

            $row++;

                        }

            $this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row );
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Depreciation_report') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);



			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dbdee0')
				)
			);

			/*$styleArray2 = array(
  'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
	  );*/

			$this->excel->getActiveSheet()->getStyle('A1:A' . $row)->applyFromArray($styleArray1);

            $row++;














			foreach ($fixedAssetList as $account){

                $account = (array) $account;




                $id      = $account['id'];







                {










                    $accountEntries=$this->fixedassets_model->getDepreciationsByFAId($account['id'], (isset($_GET['start_date']) ? $_GET['start_date'] : ''), (isset($_GET['end_date']) ? $_GET['end_date'] : ''));


                    if($accountEntries){


						$accountName = $account['name'].' ( '.$account['code'].' )';

                        $this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $accountName);
					//$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);


					$row++;

					$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("Period").' ( '.lang("month").' )');
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("The_book_value").' - '.lang("Start_Period"));
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("Depreciation_percentage"));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("Depreciation_of_fixed_asset"));
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Accumulated_depreciation"));
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang("The_book_value").' - '.lang("End_Period"));
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleArray1);


					$row++;


                    $end_value=0;
                    $accumulated_value=0;





                    foreach ($accountEntries as $entry){
                        $entry = (array) $entry;



                        $accumulated_value=$accumulated_value + $entry['depreciation_amount'];


						$this->excel->getActiveSheet()->SetCellValue('A' . $row, date('Y-m', strtotime($entry['period_date'])));

						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(11);


						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal($entry['start_value']));
						$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatDecimal($entry['depreciation_ratio'],2).'%');
                        $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(11);




						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatDecimal($entry['depreciation_amount']));
                        $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($accumulated_value));
                        $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($entry['end_value']));
                        $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(11);

                        $end_value=$entry['end_value'];

						$row++;
					}




				$total=0;
				$diffDebit=0;
				$diffCredit=0;





						$this->excel->getActiveSheet()->mergeCells('A' . $row . ':E' . $row);

						$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("The_book_value").' - '.lang("End_Period"));
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);




                            $this->excel->getActiveSheet()->getStyle('F' . $row)->getBorders()
							->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($end_value));
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

                            $row++;

                    }
				}
			}








			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);








			$filename = lang('Depreciation_report');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {
			$group_id     = $this->input->post('group_id') ? $this->input->post('group_id') : null;
			$fixed_asset_id     = $this->input->post('fixed_asset_id') ? $this->input->post('fixed_asset_id') : null;
			$start_date     = $this->input->post('start_date') ? $this->input->post('start_date') : null;
			$end_date       = $this->input->post('end_date') ? $this->input->post('end_date') : null;

            if (empty($_POST)){

            }else{
        //$this->sma->print_arrays($_POST);
            $this->data['fixedAssetList']  = $this->fixedassets_model->getFixedAssets($fixed_asset_id,$group_id);
            if($group_id){
                $this->data['groupName']  = $this->fixedassets_model->getGroupByID($group_id)->name;
            }
            }

			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';

			}





			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');



			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Depreciation_report')], ['link' => '#', 'page' => lang('Depreciation_report')]];
			$meta                = ['page_title' => lang('Depreciation_report'), 'bc' => $bc];

			$this->page_construct('fixedassets/depreciation_report', $meta, $this->data);
		}
	}



    public function valuation_report($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		//$this->sma->checkPermissions('Valuation_report', true);

            $this->data['groups'] = $this->fixedassets_model->getGroups();
            $this->data['fixedAssets'] = $this->fixedassets_model->getFixedAssets();
            $this->data['period_year'] = $this->fixedassets_model->getAllYearFromDepreciations();
            //$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : ['0' => (object)['period_year' => date('Y')]];
            if ($_POST)
                $this->data['max_min_period_year'] = (object)['period_from_date' => $this->input->post('period_year') . '-01-01', 'period_to_date' => $this->input->post('period_year') . '-12-31'];
            else
                $this->data['max_min_period_year'] = (object)['period_from_date' => date('Y') . '-01-01', 'period_to_date' => date('Y') . '-12-31'];


                //$this->sma->print_arrays($this->data['period_year']);


		/*if ($id){
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);

			}
			else {
				$this->data['accountList'] = $this->accounts_model->getAccounts();
			}
			*/
		//$data['userData']   = $this->session->userdata();
		if ($pdf || $xls) {



			$group_id     = $this->input->get('group_id') ? $this->input->get('group_id') : null;
			$fixed_asset_id     = $this->input->get('fixed_asset_id') ? $this->input->get('fixed_asset_id') : null;
			$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;

            if (empty($_GET)){

            }else{
        //$this->sma->print_arrays($_POST);
            $fixedAssetList  = $this->fixedassets_model->getFixedAssets($fixed_asset_id,$group_id);
            if($group_id){
                $groupName  = $this->fixedassets_model->getGroupByID($group_id)->name;
            }
            }

			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';

			}





			//$this->sma->print_arrays($_POST);

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Valuation_report'));

			$this->excel->getActiveSheet()->mergeCells('A1:F1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('Valuation_report'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 25,
					'name'  => 'Cairo-Bold'
				)

			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);




			$this->excel->getActiveSheet()->mergeCells('A2:F2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

            $row = 3;
						if($group_id){


			$this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row );
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Fixed_assets_group').' ( '.$groupName.' ) ');
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

            $row++;

                        }

            $this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row );
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Valuation_report') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);



			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dbdee0')
				)
			);

			/*$styleArray2 = array(
  'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
	  );*/

			$this->excel->getActiveSheet()->getStyle('A1:A' . $row)->applyFromArray($styleArray1);

            $row++;














			foreach ($fixedAssetList as $account){

                $account = (array) $account;




                $id      = $account['id'];







                {










                    $accountEntries=$this->fixedassets_model->getDepreciationsByFAId($account['id'], (isset($_GET['start_date']) ? $_GET['start_date'] : ''), (isset($_GET['end_date']) ? $_GET['end_date'] : ''));


                    if($accountEntries){


						$accountName = $account['name'].' ( '.$account['code'].' )';

                        $this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $accountName);
					//$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);


					$row++;

					$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("Period").' ( '.lang("month").' )');
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("value").' - '.lang("Start_Period"));
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("Depreciation_percentage"));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("Depreciation_of_fixed_asset"));
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Accumulated_depreciation"));
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang("value").' - '.lang("End_Period"));
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleArray1);


					$row++;


                    $accumulated_value=0;





                    foreach ($accountEntries as $entry){
                        $entry = (array) $entry;






						$this->excel->getActiveSheet()->SetCellValue('A' . $row, date('Y-m', strtotime($entry['period_date'])));

						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(11);


						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal($entry['start_value']));
						$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatDecimal($entry['depreciation_ratio'],2).'%');
                        $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(11);




						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatDecimal($entry['depreciation_amount']));
                        $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($entry['accumulated_value']));
                        $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($entry['end_value']));
                        $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(11);

                        $accumulated_value=$entry['accumulated_value'];

						$row++;
					}




				$total=0;
				$diffDebit=0;
				$diffCredit=0;





						$this->excel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row);

						$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("The_book_value_of_the_asset"));
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);


							$this->excel->getActiveSheet()->getStyle('E' . $row)->getBorders()
							->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
							$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($accumulated_value));
							$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

                            $this->excel->getActiveSheet()->getStyle('F' . $row)->getBorders()
							->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
							$this->excel->getActiveSheet()->SetCellValue('F' . $row, '');
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

                            $row++;

                    }
				}
			}








			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);








			$filename = lang('Valuation_report');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {
			$group_id     = $this->input->post('group_id') ? $this->input->post('group_id') : null;
			$fixed_asset_id     = $this->input->post('fixed_asset_id') ? $this->input->post('fixed_asset_id') : null;
			$start_date     = $this->input->post('start_date') ? $this->input->post('start_date') : null;
			$end_date       = $this->input->post('end_date') ? $this->input->post('end_date') : null;


        //$this->sma->print_arrays($_POST);
            $this->data['fixedAssetList']  = $this->fixedassets_model->getFixedAssets($fixed_asset_id,$group_id);
            if($group_id){
                $this->data['groupName']  = $this->fixedassets_model->getGroupByID($group_id)->name;
            }


			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';

			}





			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');



			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Valuation_report')], ['link' => '#', 'page' => lang('Valuation_report')]];
			$meta                = ['page_title' => lang('Valuation_report'), 'bc' => $bc];

			$this->page_construct('fixedassets/valuation_report', $meta, $this->data);
		}
	}

    public function revaluation()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Revaluation_of_fixed_asset')], ['link' => '#', 'page' => lang('Revaluation_of_fixed_asset')]];
        $meta                = ['page_title' => lang('Revaluation_of_fixed_asset'), 'bc' => $bc];
        $this->page_construct('fixedassets/revaluation', $meta, $this->data);
    }

    public function increasing()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Increasing_the_fixed_asset')], ['link' => '#', 'page' => lang('Increasing_the_fixed_asset')]];
        $meta                = ['page_title' => lang('Increasing_the_fixed_asset'), 'bc' => $bc];
        $this->page_construct('fixedassets/increasing', $meta, $this->data);
    }

    public function add_revaluation()
    {
		{
        $this->load->helper('security');
        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'trim|is_unique[ast_revaluations.reference_no]|required');
        $this->form_validation->set_rules('fixed_asset_id', lang('Fixed_asset'), 'required');
        $this->form_validation->set_rules('accountID', lang('Fixed_asset_account'), 'required');
        $this->form_validation->set_rules('book_value', lang('The_book_value_of_the_asset'), 'required');
        $this->form_validation->set_rules('revaluation_value', lang('Revaluation_value'), 'required');



        if ($this->form_validation->run() == true) {
            $date = $this->sma->fld(trim($this->input->post('date')));
            $balance =$this->sma->formatDecimal($this->input->post('revaluation_value') - $this->input->post('book_value'));
            $data = [

                'date'      => $date,
                'reference_no'      => $this->input->post('reference_no'),
                'fixed_asset_id'           => $this->input->post('fixed_asset_id'),
                'accountID'           => $this->input->post('accountID'),
                'created_by'        => $this->session->userdata('user_id'),
                'book_value'        => $this->input->post('book_value'),
                'revaluation_value'        => $this->input->post('revaluation_value'),
                'balance'        => $balance,

            ];


        } elseif ($this->input->post('add_revaluation')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/revaluation');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->addRevaluation($data)) {
            $this->session->set_flashdata('message', lang('Revaluation_added'));

            if($balance!=0){

                $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($this->input->post('fixed_asset_id'));

                $entryDescription="Revaluation_of_fixed_asset#".$fixed_asset_details->code;
                $entry_type="Revaluation_of_fixed_asset";

                if($balance<0){
                $revaluation_losses_fixed_assets_acct = $this->site->getAccountsSettings()->revaluation_losses_fixed_assets_acct;
                $this->site->createEntryMultiSide($revaluation_losses_fixed_assets_acct,$this->input->post('accountID'),abs($balance),abs($balance),$this->input->post('reference_no'),$entry_type,null,null,null,null,$date,$entryDescription);
                }else if($balance>0){
                    $revaluation_profite_fixed_assets_acct = $this->site->getAccountsSettings()->revaluation_profite_fixed_assets_acct;
                    $this->site->createEntryMultiSide($this->input->post('accountID'),$revaluation_profite_fixed_assets_acct,$balance,$balance,$this->input->post('reference_no'),$entry_type,null,null,null,null,$date,$entryDescription);
                    }

            }



            admin_redirect('fixedassets/revaluation');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['revaluation_reference_no'] = 'rev_'.$this->fixedassets_model->getRevaluationsAutoCode()->id;
            $this->data['fixedAssetList']  = $this->fixedassets_model->getFixedAssets();
            $accounts_fixed_assets_acct = $this->site->getAccountsSettings()->accounts_fixed_assets_acct;
            $fixed_assets_acct = $this->accounts_model->getAccountByID($accounts_fixed_assets_acct);
            $accountList1=$this->accounts_model->getAccountChildsWithLevels($fixed_assets_acct->accountID);
            //$this->sma->print_arrays($accountList1);
            //$this->data['period_year'] = $this->fixedassets_model->getAllYearFromDepreciations();
            $this->data['accountsList'] = $accountList1;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/add_revaluation', $this->data);
        }
		}
    }


    public function add_increasing()
    {
		{
        $this->load->helper('security');
        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'trim|is_unique[ast_increasing.reference_no]|required');
        $this->form_validation->set_rules('fixed_asset_id', lang('Fixed_asset'), 'required');
        $this->form_validation->set_rules('supplier_id', lang('supplier'), 'required');
        $this->form_validation->set_rules('increasing_type', lang('Type_of_fixed_asset_increase'), 'required');
        $this->form_validation->set_rules('effect_type', lang('The_effect_of_increasing_on'), 'required');

        if($this->input->post('effect_type')==1){
            $this->form_validation->set_rules('cost', lang('cost'), 'trim|numeric|required');
            $this->form_validation->set_rules('tax', lang('tax'), 'trim|numeric|required');
            $this->form_validation->set_rules('net', lang('Net'), 'trim|numeric|required');
        }

        if($this->input->post('effect_type')==2){
            $this->form_validation->set_rules('useful_life', lang('Useful_life'), 'trim|numeric|required|max_length[2]');

        }




        if ($this->form_validation->run() == true) {


            $start_date = $this->sma->fld(trim($this->input->post('start_date')));
            $end_date = $this->sma->fld(trim($this->input->post('end_date')));
            $data = [

                'start_date'      => $start_date,
                'end_date'      => $end_date,
                'reference_no'      => $this->input->post('reference_no'),
                'fixed_asset_id'           => $this->input->post('fixed_asset_id'),
                'supplier_id'           => $this->input->post('supplier_id'),
                'created_by'        => $this->session->userdata('user_id'),
                'increasing_type'        => $this->input->post('increasing_type'),
                'effect_type'        => $this->input->post('effect_type'),
                'description'        => $this->sma->clear_tags($this->input->post('description')),


            ];

            if($this->input->post('effect_type')==1){
                $data['cost']=$this->input->post('cost');
                $data['tax']=$this->input->post('tax');
                $data['tax_id']=$this->input->post('tax_id');
                $data['net']=$this->input->post('net');

            }
            if($this->input->post('effect_type')==2){
                $data['useful_life']=$this->input->post('useful_life');


            }


            //$this->sma->print_arrays($data);


        } elseif ($this->input->post('add_increasing')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/increasing');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->addIncreasing($data)) {


            if($this->input->post('effect_type')==2){

                $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($this->input->post('fixed_asset_id'));


                $date1 = $fixed_asset_details->start_date;
                $date2 = $start_date;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years = floor($diff / (365*60*60*24));
                $useful_life=($fixed_asset_details->useful_life - $years + $data['useful_life']);

                $useful_life=($useful_life * 12);
                $month = date("m",strtotime($start_date));
                $year = date("Y",strtotime($start_date));

                $period_date=$year."-".$month."-01";
                $start_value=$this->fixedassets_model->getDepreciationByID($fixed_asset_details->id,$period_date)->start_value;

                $amount=(($start_value - $fixed_asset_details->scrap_value)/$useful_life);
                $accumulated_value=0;
                $depreciation_ratio=(($amount)*100)/$start_value;
                $prev_period_date=$period_date;
                for($i=0;$i<$useful_life;$i++){

                    $end_value=$start_value-$amount;
                    $accumulated_value=$accumulated_value+$amount;

                    $depreciation = [
                        'fixed_asset_id'        => $fixed_asset_details->id,
                        'period_date'      => $period_date,
                        'start_value'      => $this->sma->formatDecimal($start_value),
                        'depreciation_ratio'      => $this->sma->formatDecimal($depreciation_ratio),
                        'depreciation_amount'      => $this->sma->formatDecimal($amount),
                        'accumulated_value'      => $this->sma->formatDecimal($accumulated_value),
                        'end_value'      => $this->sma->formatDecimal($end_value),

                    ];

                    $period_date = date('Y-m-d', strtotime("+1 month", strtotime($period_date)));
                    $start_value=$end_value;

                    $depreciations[] = $depreciation;

                }
                //$this->sma->print_arrays($depreciations);
                if($depreciations){
                    $this->fixedassets_model->deleteDepreciationsv2($fixed_asset_details->id,$prev_period_date);
                    $this->fixedassets_model->addDepreciations($depreciations);
                }

            }


            $this->session->set_flashdata('message', lang('Increasing_added'));





            admin_redirect('fixedassets/increasing');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['increasing_reference_no'] = 'inc_'.$this->fixedassets_model->getIncreasingAutoCode()->id;
            $this->data['fixedAssetList']  = $this->fixedassets_model->getFixedAssets();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            //$this->data['period_year'] = $this->fixedassets_model->getAllYearFromDepreciations();
            $this->data['suppliers'] = $this->site->getAllCompanies('supplier');
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/add_increasing', $this->data);
        }
		}
    }

    public function getBookValue()
    {
        $fixed_asset_id = $this->input->post('fixed_asset_id');
        $date = $this->sma->fld(trim($this->input->post('date')));
        $year = date("Y",strtotime($date));

        $row = $this->fixedassets_model->getValuationsByFAId($fixed_asset_id,$year."-01-01",$year."-12-31");
		$this->sma->send_json($row[0]->end_value);
    }

    public function getRevaluation()
    {

        $this->load->library('datatables');
        $this->datatables
            //->select(" id, code, name,  nameEn,ast_parent_classes.name as parentName", false)
            ->select("{$this->db->dbprefix('ast_revaluations')}.id as id, {$this->db->dbprefix('ast_revaluations')}.reference_no, {$this->db->dbprefix('ast_revaluations')}.date,  {$this->db->dbprefix('ast_fixed_assets')}.name,{$this->db->dbprefix('gl_accounts')}.accountName, {$this->db->dbprefix('ast_revaluations')}.book_value, {$this->db->dbprefix('ast_revaluations')}.revaluation_value", false)
            ->from('ast_revaluations')
            ->join('gl_accounts', 'gl_accounts.id=ast_revaluations.accountID', 'left')
            ->join('ast_fixed_assets', 'ast_fixed_assets.id=ast_revaluations.fixed_asset_id', 'left')
            //->group_by('id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('fixedassets/edit_revaluation/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_revaluation') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_revaluation') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('fixedassets/delete_revaluation/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function getIncreasing()
    {

        $add_payment_link  = "<a href='" . admin_url('fixedassets/add_payment/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('add_payment') . "'><i class=\"fa fa-plus\"></i></a>";
        $payments_link  = "<a href='" . admin_url('fixedassets/payments/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('view_payments') . "'><i class=\"fa fa-money\"></i></a>";


        $this->load->library('datatables');
        $this->datatables
            //->select(" id, code, name,  nameEn,ast_parent_classes.name as parentName", false)
            ->select("{$this->db->dbprefix('ast_increasing')}.id as id, {$this->db->dbprefix('ast_increasing')}.reference_no, {$this->db->dbprefix('ast_increasing')}.start_date, {$this->db->dbprefix('ast_increasing')}.end_date,  {$this->db->dbprefix('ast_fixed_assets')}.name,{$this->db->dbprefix('companies')}.company, {$this->db->dbprefix('ast_increasing')}.increasing_type, {$this->db->dbprefix('ast_increasing')}.effect_type, {$this->db->dbprefix('ast_increasing')}.cost, {$this->db->dbprefix('ast_increasing')}.useful_life, {$this->db->dbprefix('ast_increasing')}.payment_status", false)
            ->from('ast_increasing')
            ->join('companies', 'companies.id=ast_increasing.supplier_id', 'left')
            ->join('ast_fixed_assets', 'ast_fixed_assets.id=ast_increasing.fixed_asset_id', 'left')
            //->group_by('id')
            ->add_column('Actions', '<div class="text-center">' . $add_payment_link . ' '.$payments_link. " <a href='" . admin_url('fixedassets/edit_increasing/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_increasing') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_increasing') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('fixedassets/delete_increasing/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function edit_revaluation($id = null)
    {
        //$this->sma->checkPermissions();
		{
		$this->load->helper('security');

        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('fixed_asset_id', lang('Fixed_asset'), 'required');
        $this->form_validation->set_rules('accountID', lang('Fixed_asset_account'), 'required');
        $this->form_validation->set_rules('book_value', lang('The_book_value_of_the_asset'), 'required');
        $this->form_validation->set_rules('revaluation_value', lang('Revaluation_value'), 'required');

        $wh_details = $this->fixedassets_model->getRevaluationByID($id);
        if ($this->input->post('reference_no') != $wh_details->reference_no) {
            $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required|is_unique[ast_revaluations.reference_no]');
        }


        if ($this->form_validation->run() == true) {

            $date = $this->sma->fld(trim($this->input->post('date')));

            $updated_at  = date('Y-m-d H:i:s');
            $balance =$this->sma->formatDecimal($this->input->post('revaluation_value') - $this->input->post('book_value'));


            $data     = [
                'updated_at'              => $updated_at,
                'updated_by'          => $this->session->userdata('user_id'),
                'date'      => $date,
                'reference_no'      => $this->input->post('reference_no'),
                'fixed_asset_id'           => $this->input->post('fixed_asset_id'),
                'accountID'           => $this->input->post('accountID'),

                'book_value'        => $this->input->post('book_value'),
                'revaluation_value'        => $this->input->post('revaluation_value'),
                'balance'        => $balance,

            ];





            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_revaluation')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/revaluation');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->updateRevaluation($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Revaluation_updated'));


            $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($this->input->post('fixed_asset_id'));

                $entryDescription="Revaluation_of_fixed_asset#".$fixed_asset_details->code;
                $entry_type="Revaluation_of_fixed_asset";

                if($balance<0){
                $revaluation_losses_fixed_assets_acct = $this->site->getAccountsSettings()->revaluation_losses_fixed_assets_acct;
                $this->site->updateEntryMultiSide($revaluation_losses_fixed_assets_acct,$this->input->post('accountID'),abs($balance),abs($balance),$this->input->post('reference_no'),$entry_type,null,null,null,null,$date,$entryDescription);
                }else if($balance>0){
                    $revaluation_profite_fixed_assets_acct = $this->site->getAccountsSettings()->revaluation_profite_fixed_assets_acct;
                    $this->site->updateEntryMultiSide($this->input->post('accountID'),$revaluation_profite_fixed_assets_acct,$balance,$balance,$this->input->post('reference_no'),$entry_type,null,null,null,null,$date,$entryDescription);
                    }

            admin_redirect('fixedassets/revaluation');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['revaluation']    = $this->fixedassets_model->getRevaluationByID($id);
            $this->data['id']           = $id;
            $this->data['fixedAssetList']  = $this->fixedassets_model->getFixedAssets();
            $accounts_fixed_assets_acct = $this->site->getAccountsSettings()->accounts_fixed_assets_acct;
            $fixed_assets_acct = $this->accounts_model->getAccountByID($accounts_fixed_assets_acct);
            $accountList1=$this->accounts_model->getAccountChildsWithLevels($fixed_assets_acct->accountID);
            //$this->sma->print_arrays($accountList1);
            //$this->data['period_year'] = $this->fixedassets_model->getAllYearFromDepreciations();
            $this->data['accountsList'] = $accountList1;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/edit_revaluation', $this->data);
        }
		}
    }

    public function delete_revaluation($id = null)
    {



        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $revaluation    = $this->fixedassets_model->getRevaluationByID($id);
       {

        if ($this->fixedassets_model->deleteRevaluation($id)) {
            $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($revaluation->fixed_asset_id);

            $entryDescription="Revaluation_of_fixed_asset#".$fixed_asset_details->code;
            $this->site->updateEntryMultiSideWithDelete($revaluation->reference_no,$entryDescription);
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Revaluation_deleted')]);
            }
            $this->session->set_flashdata('message', lang('Revaluation_deleted'));
            admin_redirect('welcome');
        }
    }
    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_revaluation_There_are_relevant_processes')]);


    }



    public function edit_increasing($id = null)
    {
        //$this->sma->checkPermissions();
		{
		$this->load->helper('security');

        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('fixed_asset_id', lang('Fixed_asset'), 'required');
        $this->form_validation->set_rules('supplier_id', lang('supplier'), 'required');
        $this->form_validation->set_rules('increasing_type', lang('Type_of_fixed_asset_increase'), 'required');

        if($this->input->post('effect_type')==1){
            $this->form_validation->set_rules('cost', lang('cost'), 'trim|numeric|required');
            $this->form_validation->set_rules('tax', lang('tax'), 'trim|numeric|required');
            $this->form_validation->set_rules('net', lang('Net'), 'trim|numeric|required');
        }

        if($this->input->post('effect_type')==2){
            $this->form_validation->set_rules('useful_life', lang('Useful_life'), 'trim|numeric|required|max_length[2]');

        }


        $wh_details = $this->fixedassets_model->getIncreasingByID($id);
        if ($this->input->post('reference_no') != $wh_details->reference_no) {
            $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required|is_unique[ast_increasing.reference_no]');
        }


        if ($this->form_validation->run() == true) {


            $start_date = $this->sma->fld(trim($this->input->post('start_date')));
            $end_date = $this->sma->fld(trim($this->input->post('end_date')));

            $updated_at  = date('Y-m-d H:i:s');


            $data     = [
                'updated_at'              => $updated_at,
                'updated_by'          => $this->session->userdata('user_id'),
                'start_date'      => $start_date,
                'end_date'      => $end_date,
                'reference_no'      => $this->input->post('reference_no'),
                'fixed_asset_id'           => $this->input->post('fixed_asset_id'),
                'supplier_id'           => $this->input->post('supplier_id'),
                'increasing_type'        => $this->input->post('increasing_type'),
                'effect_type'        => $this->input->post('effect_type'),
                'description'        => $this->sma->clear_tags($this->input->post('description')),

            ];

            if($this->input->post('effect_type')==1){
                $data['cost']=$this->input->post('cost');
                $data['tax']=$this->input->post('tax');
                $data['tax_id']=$this->input->post('tax_id');
                $data['net']=$this->input->post('net');

                $data['useful_life']=null;

            }
            if($this->input->post('effect_type')==2){
                $data['useful_life']=$this->input->post('useful_life');

                $data['cost']=null;
                $data['tax']=null;
                $data['tax_id']=null;
                $data['net']=null;


            }





            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_increasing')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/increasing');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->updateIncreasing($id, $data)) { //check to see if we are updateing the customer



            if($this->input->post('effect_type')==2){

            $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($this->input->post('fixed_asset_id'));


            $date1 = $fixed_asset_details->start_date;
            $date2 = $start_date;

            $diff = abs(strtotime($date2) - strtotime($date1));

            $years = floor($diff / (365*60*60*24));
            $useful_life=($fixed_asset_details->useful_life - $years + $data['useful_life']);

            $useful_life=($useful_life * 12);
            $month = date("m",strtotime($start_date));
            $year = date("Y",strtotime($start_date));

            $period_date=$year."-".$month."-01";
            $start_value=$this->fixedassets_model->getDepreciationByID($fixed_asset_details->id,$period_date)->start_value;

            $amount=(($start_value - $fixed_asset_details->scrap_value)/$useful_life);
            $accumulated_value=0;
            $depreciation_ratio=(($amount)*100)/$start_value;
            $prev_period_date=$period_date;
            for($i=0;$i<$useful_life;$i++){

                $end_value=$start_value-$amount;
                $accumulated_value=$accumulated_value+$amount;

                $depreciation = [
                    'fixed_asset_id'        => $fixed_asset_details->id,
                    'period_date'      => $period_date,
                    'start_value'      => $this->sma->formatDecimal($start_value),
                    'depreciation_ratio'      => $this->sma->formatDecimal($depreciation_ratio),
                    'depreciation_amount'      => $this->sma->formatDecimal($amount),
                    'accumulated_value'      => $this->sma->formatDecimal($accumulated_value),
                    'end_value'      => $this->sma->formatDecimal($end_value),

                ];

                $period_date = date('Y-m-d', strtotime("+1 month", strtotime($period_date)));
                $start_value=$end_value;

                $depreciations[] = $depreciation;

            }
            //$this->sma->print_arrays($depreciations);
            if($depreciations){
                $this->fixedassets_model->deleteDepreciationsv2($fixed_asset_details->id,$prev_period_date);
                $this->fixedassets_model->addDepreciations($depreciations);
            }

        }













            $this->session->set_flashdata('message', lang('Increasing_updated'));


            $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($this->input->post('fixed_asset_id'));



            admin_redirect('fixedassets/increasing');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['increasing']    = $this->fixedassets_model->getIncreasingByID($id);
            $this->data['id']           = $id;
            $this->data['fixedAssetList']  = $this->fixedassets_model->getFixedAssets();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['suppliers'] = $this->site->getAllCompanies('supplier');
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/edit_increasing', $this->data);
        }
		}
    }

    public function delete_increasing($id = null)
    {



        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $increasing    = $this->fixedassets_model->getIncreasingByID($id);
       {

        if ($this->fixedassets_model->deleteIncreasing($id)) {



            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Increasing_deleted')]);
            }
            $this->session->set_flashdata('message', lang('Increasing_deleted'));
            admin_redirect('welcome');
        }
    }
    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_increasing_There_are_relevant_processes')]);


    }

    public function increasing_report($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		//$this->sma->checkPermissions('increasing_report', true);

            $this->data['groups'] = $this->fixedassets_model->getGroups();
            $this->data['fixedAssets'] = $this->fixedassets_model->getFixedAssets();




		/*if ($id){
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);

			}
			else {
				$this->data['accountList'] = $this->accounts_model->getAccounts();
			}
			*/
		//$data['userData']   = $this->session->userdata();
		if ($pdf || $xls) {



			$group_id     = $this->input->get('group_id') ? $this->input->get('group_id') : null;
			$fixed_asset_id     = $this->input->get('fixed_asset_id') ? $this->input->get('fixed_asset_id') : null;
			$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;

            if (empty($_GET)){

            }else{
        //$this->sma->print_arrays($_POST);
            $fixedAssetList  = $this->fixedassets_model->getFixedAssets($fixed_asset_id,$group_id);
            if($group_id){
                $groupName  = $this->fixedassets_model->getGroupByID($group_id)->name;
            }
            }

			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';

			}





			//$this->sma->print_arrays($_POST);

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Increasing_report'));

			$this->excel->getActiveSheet()->mergeCells('A1:F1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('Increasing_report'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 25,
					'name'  => 'Cairo-Bold'
				)

			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);




			$this->excel->getActiveSheet()->mergeCells('A2:F2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

            $row = 3;
						if($group_id){


			$this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row );
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Fixed_assets_group').' ( '.$groupName.' ) ');
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

            $row++;

                        }

            $this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row );
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Increasing_report') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);



			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dbdee0')
				)
			);

			/*$styleArray2 = array(
  'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
	  );*/

			$this->excel->getActiveSheet()->getStyle('A1:A' . $row)->applyFromArray($styleArray1);

            $row++;














			foreach ($fixedAssetList as $account){

                $account = (array) $account;




                $id      = $account['id'];







                {










                    $accountEntries=$this->fixedassets_model->getDepreciationsByFAId($account['id'], (isset($_GET['start_date']) ? $_GET['start_date'] : ''), (isset($_GET['end_date']) ? $_GET['end_date'] : ''));


                    if($accountEntries){


						$accountName = $account['name'].' ( '.$account['code'].' )';

                        $this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $accountName);
					//$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);


					$row++;

					$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("Period").' ( '.lang("month").' )');
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("The_book_value").' - '.lang("Start_Period"));
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("Depreciation_percentage"));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("Depreciation_of_fixed_asset"));
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Accumulated_depreciation"));
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang("The_book_value").' - '.lang("End_Period"));
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleArray1);


					$row++;


                    $end_value=0;
                    $accumulated_value=0;





                    foreach ($accountEntries as $entry){
                        $entry = (array) $entry;



                        $accumulated_value=$accumulated_value + $entry['depreciation_amount'];


						$this->excel->getActiveSheet()->SetCellValue('A' . $row, date('Y-m', strtotime($entry['period_date'])));

						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(11);


						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal($entry['start_value']));
						$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatDecimal($entry['depreciation_ratio'],2).'%');
                        $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(11);




						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatDecimal($entry['depreciation_amount']));
                        $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($accumulated_value));
                        $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(11);



						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($entry['end_value']));
                        $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(11);

                        $end_value=$entry['end_value'];

						$row++;
					}




				$total=0;
				$diffDebit=0;
				$diffCredit=0;





						$this->excel->getActiveSheet()->mergeCells('A' . $row . ':E' . $row);

						$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("The_book_value").' - '.lang("End_Period"));
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);




                            $this->excel->getActiveSheet()->getStyle('F' . $row)->getBorders()
							->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($end_value));
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

                            $row++;

                    }
				}
			}








			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);








			$filename = lang('Increasing_report');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {
			$group_id     = $this->input->post('group_id') ? $this->input->post('group_id') : null;
			$fixed_asset_id     = $this->input->post('fixed_asset_id') ? $this->input->post('fixed_asset_id') : null;
			$increasing_type     = $this->input->post('increasing_type') ? $this->input->post('increasing_type') : null;
			$start_date     = $this->input->post('start_date') ? $this->input->post('start_date') : null;
			$end_date       = $this->input->post('end_date') ? $this->input->post('end_date') : null;

            if (empty($_POST)){

            }else{
        //$this->sma->print_arrays($_POST);
            $this->data['fixedAssetList']  = $this->fixedassets_model->getFixedAssets($fixed_asset_id,$group_id);
            if($group_id){
                $this->data['groupName']  = $this->fixedassets_model->getGroupByID($group_id)->name;
            }
            }

			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';

			}





			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');



			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('fixedassets'), 'page' => lang('Increasing_report')], ['link' => '#', 'page' => lang('Increasing_report')]];
			$meta                = ['page_title' => lang('Increasing_report'), 'bc' => $bc];

			$this->page_construct('fixedassets/increasing_report', $meta, $this->data);
		}
	}


    public function add_payment($id = null)
    {
        //$this->sma->checkPermissions('deposits', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $wh_details = $this->fixedassets_model->getIncreasingByID($id);

        if($wh_details->effect_type!=1){



        }

        if ($this->Owner || $this->Admin) {
            $this->form_validation->set_rules('date', lang('date'), 'required');
        }
        $this->form_validation->set_rules('amount', lang('amount'), 'required|numeric');


        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        $this->form_validation->set_rules('accountID', lang('Fixed_asset_account'), 'required');


        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'increasing_id'  => $this->input->post('increasing_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('aipay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $expense->brId : null),
                'amount'       => $this->input->post('amount'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->sma->clear_tags($this->input->post('note')),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => 'sent',
                'accountID'           => $this->input->post('accountID'),
            ];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {

                                //$payment['grpId']=$wh_details->grpId;
                                //$payment['coId']=$wh_details->coId;
                                //$payment['brId']=$wh_details->brId;


                            }

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

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
			}

			//$this->sma->print_arrays($payment,$payment_type);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('fixedassets/increasing');
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->addIncreasingPayment($payment,$payment_type)) {
            $this->session->set_flashdata('message', lang('payment_added'));
            admin_redirect('fixedassets/increasing');
        } else {

			//if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

            	//$this->sma->print_arrays($this->data['payment_types']);

			//}
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['increasing']    = $this->fixedassets_model->getIncreasingByID($id);
            $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($wh_details->fixed_asset_id);
            $this->data['fixed_asset_details']           = $fixed_asset_details;
            $this->data['id']           = $id;
            $accounts_fixed_assets_acct = $this->site->getAccountsSettings()->accounts_fixed_assets_acct;
            $fixed_assets_acct = $this->accounts_model->getAccountByID($accounts_fixed_assets_acct);
            $accountList1=$this->accounts_model->getAccountChildsWithLevels($fixed_assets_acct->accountID);
            //$this->sma->print_arrays($accountList1);
            //$this->data['period_year'] = $this->fixedassets_model->getAllYearFromDepreciations();
            $this->data['accountsList'] = $accountList1;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'fixedassets/add_payment', $this->data);
        }
    }

    public function payments($id = null)
    {
         //$this->sma->checkPermissions('payments', true);

        $this->data['payments'] = $this->fixedassets_model->getIncreasingPayments($id);
        $increasing      = $this->fixedassets_model->getIncreasingByID($id);
        $this->data['increasing']      = $increasing;
        $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($increasing->fixed_asset_id);
        $this->data['fixed_asset_details']           = $fixed_asset_details;
        $this->load->view($this->theme . 'fixedassets/payments', $this->data);
    }

    public function payment_note($id = null)
    {
        //$this->sma->checkPermissions('payments', true);
        $payment                  = $this->fixedassets_model->getIncreasingPaymentByID($id);
        $increasing                      = $this->fixedassets_model->getIncreasingByID($payment->increasing_id);
        $this->data['increasing']        = $increasing;
        $this->data['payment']    = $payment;
        $this->data['page_title'] = $this->lang->line('payment_note');

        $this->load->view($this->theme . 'fixedassets/payment_note', $this->data);
    }

    public function edit_payment($id = null)
    {
        //$this->sma->checkPermissions('edit_payment', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('amount', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        $this->form_validation->set_rules('accountID', lang('Fixed_asset_account'), 'required');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'increasing_id'  => $this->input->post('increasing_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount'       => $this->input->post('amount'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->sma->clear_tags($this->input->post('note')),
                'accountID'           => $this->input->post('accountID'),
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }


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


			}


            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->fixedassets_model->updateIncreasingPayment($id, $payment,$payment_type)) {
            $this->session->set_flashdata('message', lang('payment_updated'));
            admin_redirect('fixedassets/increasing');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if ($this->Settings->accounting){
                $this->data['payment_types'] = $this->site->getAllPaymentTypes();
                $accounts_settings   = $this->accounts_model->getSettings();
                $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
                $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

                    //$this->sma->print_arrays($this->data['payment_types']);

                }

            $this->data['payment']  = $this->fixedassets_model->getIncreasingPaymentByID($id);
            $accounts_fixed_assets_acct = $this->site->getAccountsSettings()->accounts_fixed_assets_acct;
            $fixed_assets_acct = $this->accounts_model->getAccountByID($accounts_fixed_assets_acct);
            $accountList1=$this->accounts_model->getAccountChildsWithLevels($fixed_assets_acct->accountID);
            //$this->sma->print_arrays($accountList1);
            //$this->data['period_year'] = $this->fixedassets_model->getAllYearFromDepreciations();
            $this->data['accountsList'] = $accountList1;
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'fixedassets/edit_payment', $this->data);
        }
    }

    public function delete_payment($id = null)
    {
        //$this->sma->checkPermissions('delete_payment', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $payment                  = $this->fixedassets_model->getIncreasingPaymentByID($id);
        if ($this->fixedassets_model->deleteIncreasingPayment($id)) {

            $increasing      = $this->fixedassets_model->getIncreasingByID($payment->increasing_id);
            $fixed_asset_details = $this->fixedassets_model->getFixedAssetByID($increasing->fixed_asset_id);


            $entryDescription="Payments_increasing_asset#".$fixed_asset_details->code;
            $this->site->updateEntryMultiSideWithDelete($payment->reference_no,$entryDescription);
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang('payment_deleted'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function getMonthStartsBetween($dateString, $now = "now") {
        $result = [];

        // تحويل التواريخ إلى DateTime
        $startDate = new DateTime($dateString);
        $endDate   = new DateTime($now);

        // تعديل البداية ليكون أول يوم من الشهر التالي
        //$startDate->modify('first day of next month');

        // التكرار حتى نهاية الفترة
        while ($startDate <= $endDate) {
            $monthStart = $startDate->format("Y-m-d");

            // نهاية نفس الشهر
            $monthEnd = $startDate->format("Y-m-t");

            $result[] = [
                'start' => $monthStart,
                'end'   => $monthEnd
            ];
            $startDate->modify('first day of next month');
        }

        return $result;
    }

    public function getYearStartsBetween($dateString, $now = "now") {
        $result = [];

        // تحويل التواريخ إلى DateTime
        $startDate = new DateTime($dateString);
        $endDate   = new DateTime($now);

        // بداية أول سنة بعد التاريخ المعطى
        //$startDate->modify('first day of January next year');

        // التكرار حتى نهاية الفترة
        while ($startDate <= $endDate) {
            $yearStart = $startDate->format("Y-01-01");
            $yearEnd   = $startDate->format("Y-12-31");
            if($endDate->format("m")=='12'){
                $result[] = [
                    'start' => $yearStart,
                    'end'   => $yearEnd
                ];
            }
            $startDate->modify('first day of January next year');
        }

        return $result;
    }




}