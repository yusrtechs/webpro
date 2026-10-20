<?php

defined('BASEPATH') or exit('No direct script access allowed');

class system_settings extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect('admin');
        }
        $this->lang->admin_load('settings', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('settings_model');
        $this->load->admin_model('branches_model');

        $this->upload_path        = 'assets/uploads/';
        $this->thumbs_path        = 'assets/uploads/thumbs/';
        $this->image_types        = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size  = '1024';
    }

    public function add_brand()
    {
        $this->form_validation->set_rules('name', lang('brand_name'), 'trim|required|is_unique[brands.name]|alpha_numeric_spaces');
        $this->form_validation->set_rules('slug', lang('slug'), 'trim|required|is_unique[brands.slug]|alpha_dash');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
            ];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }
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
        } elseif ($this->input->post('add_brand')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }
        if ($this->form_validation->run() == true && $this->settings_model->addBrand($data)) {
            $this->session->set_flashdata('message', lang('brand_added'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_brand', $this->data);
        }
    }

    public function add_category()
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|is_unique[categories.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required|min_length[3]');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]|alpha_dash');
        $this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]');
        $this->form_validation->set_rules('userfile', lang('category_image'), 'xss_clean');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        $hide_pos=0;
		if($this->input->post('hide_pos')==1){
			$hide_pos=1;

		}

		if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
                'parent_id'   => $this->input->post('parent'),
                'hide_pos'   => $hide_pos,
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
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image']     = $this->upload_path . $photo;
                    $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type']          = 'text';
                    $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                    $wm['quality']          = '100';
                    $wm['wm_font_size']     = '16';
                    $wm['wm_font_color']    = '999999';
                    $wm['wm_shadow_color']  = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding']       = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = null;
            }
        } elseif ($this->input->post('add_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/categories');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCategory($data)) {
            $this->session->set_flashdata('message', lang('category_added'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['categories'] = $this->settings_model->getParentCategories();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_category', $this->data);
        }
    }

    public function add_department()
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Department_code'), 'trim|is_unique[dept.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required|min_length[3]');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]|alpha_dash');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]');
        //$this->form_validation->set_rules('userfile', lang('category_image'), 'xss_clean');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');



		if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'desc' => $this->input->post('description'),
                'parent'   => $this->input->post('parent'),
            ];


        } elseif ($this->input->post('add_department')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/departments');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addDepartment($data)) {
            $this->session->set_flashdata('message', lang('Department_added'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['departments'] = $this->settings_model->getParentDepartments();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_department', $this->data);
        }
    }




    public function add_tabel()
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Tabel_code'), 'trim|is_unique[resturant_tables.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required|min_length[3]');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]|alpha_dash');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]');
        $this->form_validation->set_rules('userfile', lang('image'), 'xss_clean');



		if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'dept'   => $this->input->post('dept'),
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 500;
                $config['max_height']    = 1000;
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
                $data['table_icon'] = $photo;
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


        } elseif ($this->input->post('add_tabel')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/tabels');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addTabel($data)) {
            $this->session->set_flashdata('message', lang('Tabel_added'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['departments'] = $this->settings_model->getDepartments();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_tabel', $this->data);
        }
    }



    public function add_stuff()
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Stuff_code'), 'trim|is_unique[resturant_emp.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required|min_length[3]');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]|alpha_dash');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]');
        //$this->form_validation->set_rules('userfile', lang('category_image'), 'xss_clean');



		if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'dept'   => $this->input->post('dept'),
                'emp_type'   => $this->input->post('emp_type'),
            ];


        } elseif ($this->input->post('add_stuff')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/stuff');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addStuff($data)) {
            $this->session->set_flashdata('message', lang('Stuff_added'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');

			$this->data['stuff_types'] = $this->settings_model->getStuffTypes();
            $this->data['departments'] = $this->settings_model->getDepartments();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_stuff', $this->data);
        }
    }




    public function add_currency()
    {
        $this->form_validation->set_rules('code', lang('currency_code'), 'trim|is_unique[currencies.code]|required|max_length[5]');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('rate', lang('exchange_rate'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['code'   => $this->input->post('code'),
                'name'        => $this->input->post('name'),
                'rate'        => $this->input->post('rate'),
                'symbol'      => $this->input->post('symbol'),
                'fraction_name'      => $this->input->post('fraction_name'),
                'fraction_symbol'      => $this->input->post('fraction_symbol'),
                'auto_update' => $this->input->post('auto_update') ? $this->input->post('auto_update') : 0,
            ];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }
        } elseif ($this->input->post('add_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/currencies');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCurrency($data)) { //check to see if we are creating the customer
            $this->session->set_flashdata('message', lang('currency_added'));
            admin_redirect('system_settings/currencies');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['page_title'] = lang('new_currency');
            $this->load->view($this->theme . 'settings/add_currency', $this->data);
        }
    }

    public function add_credit_card()
    {
        $this->form_validation->set_rules('code', lang('Credit_card_code'), 'trim|is_unique[payment_type.code]|required');
        $this->form_validation->set_rules('name', lang('Credit_card_name'), 'required');

        if ($this->form_validation->run() == true) {
            $data = ['code'   => $this->input->post('code'),
                'name'        => $this->input->post('name'),
                'second_name'        => $this->input->post('second_name'),
                'isCC'      => 1,
                'isActive' => $this->input->post('isActive') ? $this->input->post('isActive') : 0,
            ];




        } elseif ($this->input->post('add_credit_card')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/credit_cards');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addPaymentType($data)) { //check to see if we are creating the customer
            $this->session->set_flashdata('message', lang('Credit_card_has_been_successfully_added'));
            admin_redirect('system_settings/credit_cards');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['page_title'] = lang('Add_credit_card');
            $this->load->view($this->theme . 'settings/add_credit_cards', $this->data);
        }
    }

    public function add_customer_group()
    {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|is_unique[customer_groups.name]|required');
        $this->form_validation->set_rules('percent', lang('group_percentage'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name'),
                'percent'   => $this->input->post('percent'),
            ];
        } elseif ($this->input->post('add_customer_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/customer_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCustomerGroup($data)) {
            $this->session->set_flashdata('message', lang('customer_group_added'));
            admin_redirect('system_settings/customer_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_customer_group', $this->data);
        }
    }

    public function add_expense_category()
    {
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|is_unique[categories.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required|min_length[3]');

        if ($this->form_validation->run() == true) {
            $data = [
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
            ];
        } elseif ($this->input->post('add_expense_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/expense_categories');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addExpenseCategory($data)) {
            $this->session->set_flashdata('message', lang('expense_category_added'));
            admin_redirect('system_settings/expense_categories');
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_expense_category', $this->data);
        }
    }

    public function add_price_group()
    {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|is_unique[price_groups.name]|required|alpha_numeric_spaces');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('add_price_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/price_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addPriceGroup($data)) {
            $this->session->set_flashdata('message', lang('price_group_added'));
            admin_redirect('system_settings/price_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_price_group', $this->data);
        }
    }

    public function add_tax_rate()
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|is_unique[tax_rates.name]|required');
        $this->form_validation->set_rules('type', lang('type'), 'required');
        $this->form_validation->set_rules('rate', lang('tax_rate'), 'required|numeric');
		//$this->form_validation->set_rules('tax_result', lang('tax_result'), 'required');
		//$old_id=$this->settings_model->getTaxRatesActive()->id;

		$tax_active=0;
		if($this->input->post('tax_active')==1){
			//$this->settings_model->updateTaxRateDisactive();
			$tax_active=1;

		}


        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name'),
                'code'      => $this->input->post('code'),
                'type'      => $this->input->post('type'),
                'rate'      => $this->input->post('rate'),
                'category_id'      => $this->input->post('category_id'),
                'tax_active'      => $tax_active,
            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }

            //$this->sma->print_arrays($data);
        } elseif ($this->input->post('add_tax_rate')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/tax_rates');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addTaxRate($data, $old_id)) {

			//if($this->input->post('tax_active')==1){
			//die('<script>bootbox.alert("You Have Successfully updated this Record!");</script>');
			//}
            $this->session->set_flashdata('message', lang('tax_rate_added'));
            admin_redirect('system_settings/tax_rates');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }
			$this->data['tax_categories'] = $this->settings_model->getAllTaxcategory();


            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_tax_rate', $this->data);
        }
    }

    public function add_unit()
    {
        $this->form_validation->set_rules('code', lang('unit_code'), 'trim|is_unique[units.code]|required');
        $this->form_validation->set_rules('name', lang('unit_name'), 'trim|required');
        if ($this->input->post('base_unit')) {
            $this->form_validation->set_rules('operator', lang('operator'), 'required');
            $this->form_validation->set_rules('operation_value', lang('operation_value'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'name'            => $this->input->post('name'),
                'code'            => $this->input->post('code'),
                'base_unit'       => $this->input->post('base_unit') ? $this->input->post('base_unit') : null,
                'operator'        => $this->input->post('base_unit') ? $this->input->post('operator') : null,
                'operation_value' => $this->input->post('operation_value') ? $this->input->post('operation_value') : null,
            ];
        } elseif ($this->input->post('add_unit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/units');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addUnit($data)) {
            $this->session->set_flashdata('message', lang('unit_added'));
			$ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

         //   admin_redirect('system_settings/units');
        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_unit', $this->data);
        }
    }

    public function add_variant()
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|is_unique[variants.name]|required');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }
            //$this->sma->print_arrays($data);
        } elseif ($this->input->post('add_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/variants');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }
        if ($this->form_validation->run() == true && $this->settings_model->addVariant($data)) {
            $this->session->set_flashdata('message', lang('variant_added'));
            admin_redirect('system_settings/variants');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_variant', $this->data);
        }
    }

    public function add_warehouse()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|is_unique[warehouses.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('address', lang('address'), 'required');
        $this->form_validation->set_rules('userfile', lang('map_image'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if ($_FILES['userfile']['size'] > 0) {
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

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('system_settings/warehouses');
                }

                $map = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $map;
                $config['new_image']      = 'assets/uploads/thumbs/' . $map;
                $config['maintain_ratio'] = true;
                $config['width']          = 76;
                $config['height']         = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $map = null;
            }
            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'price_group_id' => $this->input->post('price_group'),
                'map'            => $map,
            ];
        } elseif ($this->input->post('add_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/warehouses');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addWarehouse($data)) {
            $this->session->set_flashdata('message', lang('warehouse_added'));
            admin_redirect('system_settings/warehouses');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['price_groups'] = $this->settings_model->getAllPriceGroups();
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_warehouse', $this->data);
        }
    }

    public function backup_database()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->load->dbutil();
        $prefs = [
            'format'   => 'txt',
            'filename' => 'sma_db_backup.sql',
        ];
        $dbName=str_replace("cpesoft_","",$this->db->database);
        if (!file_exists('./files/backups/'.$dbName)) {
            mkdir('./files/backups/'.$dbName, 0777, true);
        }
        $back    = $this->dbutil->backup($prefs);
        $backup  = &$back;
        $db_name = 'db-backup-on-' . date('Y-m-d-H-i-s') . '.txt';
        $save    = './files/backups/'.$dbName.'/' . $db_name;
        //$this->sma->print_arrays($save);
        $this->load->helper('file');
        write_file($save, $backup);
        $this->session->set_flashdata('messgae', lang('db_saved'));
        admin_redirect('system_settings/backups');
    }

    public function backup_files()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $dbName=str_replace("cpesoft_","",$this->db->database);
        if (!file_exists('./files/backups/'.$dbName)) {
            mkdir('./files/backups/'.$dbName, 0777, true);
        }
        $name = 'file-backup-' . date('Y-m-d-H-i-s');
        $this->sma->zip('./', './files/backups/'.$dbName.'/', $name);
        $this->session->set_flashdata('messgae', lang('backup_saved'));
        admin_redirect('system_settings/backups');
        exit();
    }

    public function backups()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $dbName=str_replace("cpesoft_","",$this->db->database);
        $this->data['files'] = glob('./files/backups/'.$dbName.'/*.zip', GLOB_BRACE);
        $this->data['dbs']   = glob('./files/backups/'.$dbName.'/*.txt', GLOB_BRACE);
        krsort($this->data['files']);
        krsort($this->data['dbs']);
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('backups')]];
        $meta = ['page_title' => lang('backups'), 'bc' => $bc];
        $this->page_construct('settings/backups', $meta, $this->data);
    }

    public function brand_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteBrand($id);
                    }
                    $this->session->set_flashdata('message', lang('brands_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('brands'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('image'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $brand = $this->site->getBrandByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $brand->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $brand->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $brand->image);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'brands_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function brands()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('brands')]];
        $meta                = ['page_title' => lang('brands'), 'bc' => $bc];
        $this->page_construct('settings/brands', $meta, $this->data);
    }

    public function categories()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('categories')]];
        $meta                = ['page_title' => lang('categories'), 'bc' => $bc];
        $this->page_construct('settings/categories', $meta, $this->data);
    }

    public function departments()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Departments')]];
        $meta                = ['page_title' => lang('Departments'), 'bc' => $bc];
        $this->page_construct('settings/departments', $meta, $this->data);
    }

    public function tabels()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Tabels'), 'bc' => $bc];
        $this->page_construct('settings/tabels', $meta, $this->data);
    }

     public function stuff()
    {




        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Stuff')]];
        $meta                = ['page_title' => lang('Stuff'), 'bc' => $bc];
        $this->page_construct('settings/stuff', $meta, $this->data);
    }

    public function category_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCategory($id);
                    }
                    $this->session->set_flashdata('message', lang('categories_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('slug'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('parent_category'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc              = $this->settings_model->getCategoryByID($id);
                        $parent_category = '';
                        if ($sc->parent_id) {
                            $pc              = $this->settings_model->getCategoryByID($sc->parent_id);
                            $parent_category = $pc->code;
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->slug);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sc->image);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $parent_category);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function department_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteDepartment($id);
                    }
                    $this->session->set_flashdata('message', lang('Departments_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }


            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

     public function tabel_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteTabel($id);
                    }
                    $this->session->set_flashdata('message', lang('Tabels_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }


            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }




     public function stuff_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteStuff($id);
                    }
                    $this->session->set_flashdata('message', lang('Stuff_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }


            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }





    public function change_logo()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            $this->sma->md();
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('site_logo', lang('site_logo'), 'xss_clean');
        $this->form_validation->set_rules('company_seal', lang('Company_Seal'), 'xss_clean');
        $this->form_validation->set_rules('bg_invoice_image', lang('watermark'), 'xss_clean');
        $this->form_validation->set_rules('biller_logo', lang('biller_logo'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($_FILES['site_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 350;
                $config['max_height']    = 93;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('site_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $site_logo = $this->upload->file_name;
                $this->db->update('settings', ['logo' => $site_logo], ['setting_id' => 1]);
            }

            if ($_FILES['company_seal']['size'] > 0) {
                $dbName=str_replace("cpesoft_","",$this->db->database);
                if (!file_exists($this->upload_path . 'logos/'.$dbName)) {
                    mkdir($this->upload_path . 'logos/'.$dbName, 0777, true);
                }
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/'.$dbName.'/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 200;
                $config['max_height']    = 200;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('company_seal')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $company_seal = $this->upload->file_name;
                $this->db->update('settings', ['company_seal' => $company_seal], ['setting_id' => 1]);
            }

            if ($_FILES['bg_invoice_image']['size'] > 0) {
                $dbName=str_replace("cpesoft_","",$this->db->database);
                if (!file_exists($this->upload_path . 'logos/'.$dbName)) {
                    mkdir($this->upload_path . 'logos/'.$dbName, 0777, true);
                }
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/'.$dbName.'/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 1700;
                $config['max_height']    = 2400;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('bg_invoice_image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $bg_invoice_image = $this->upload->file_name;
                $this->db->update('settings', ['bg_invoice_image' => $bg_invoice_image], ['setting_id' => 1]);
            }

            if ($_FILES['biller_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 350;
                $config['max_height']    = 93;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('biller_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo = $this->upload->file_name;
            }

            $this->session->set_flashdata('message', lang('logo_uploaded'));
            redirect($_SERVER['HTTP_REFERER']);
        } elseif ($this->input->post('upload_logo')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/change_logo', $this->data);
        }
    }

    public function create_group()
    {
        $this->form_validation->set_rules('group_name', lang('group_name'), 'required|alpha_dash|is_unique[groups.name]');

        if ($this->form_validation->run() == true) {
            $data = ['name' => strtolower($this->input->post('group_name')), 'description' => $this->input->post('description')];
        } elseif ($this->input->post('create_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/user_groups');
        }

        if ($this->form_validation->run() == true && ($new_group_id = $this->settings_model->addGroup($data))) {
            $this->session->set_flashdata('message', lang('group_added'));
            admin_redirect('system_settings/permissions/' . $new_group_id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group_name'] = [
                'name'  => 'group_name',
                'id'    => 'group_name',
                'type'  => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_name'),
            ];
            $this->data['description'] = [
                'name'  => 'description',
                'id'    => 'description',
                'type'  => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('description'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/create_group', $this->data);
        }
    }

    public function currencies()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('currencies')]];
        $meta = ['page_title' => lang('currencies'), 'bc' => $bc];
        $this->page_construct('settings/currencies', $meta, $this->data);
    }

    public function credit_cards()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Credit_Cards')]];
        $meta = ['page_title' => lang('Credit_Cards'), 'bc' => $bc];
        $this->page_construct('settings/credit_cards', $meta, $this->data);
    }

    public function currency_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCurrency($id);
                    }
                    $this->session->set_flashdata('message', lang('currencies_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('currencies'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('rate'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCurrencyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->rate);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'currencies_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function customer_group_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCustomerGroup($id);
                    }
                    $this->session->set_flashdata('message', lang('customer_groups_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('group_name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('group_percentage'));
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $pg = $this->settings_model->getCustomerGroupByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pg->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pg->percent);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'customer_groups_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_customer_group_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function customer_groups()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('customer_groups')]];
        $meta = ['page_title' => lang('customer_groups'), 'bc' => $bc];
        $this->page_construct('settings/customer_groups', $meta, $this->data);
    }

    public function delete_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        unlink('./files/backups/' . $zipfile . '.zip');
        $this->session->set_flashdata('messgae', lang('backup_deleted'));
        admin_redirect('system_settings/backups');
    }

    public function delete_brand($id = null)
    {
        if ($this->settings_model->brandHasProducts($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('brand_has_products')]);
        }

        if ($this->settings_model->deleteBrand($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('brand_deleted')]);
        }
    }

    public function delete_category($id = null)
    {
        if ($this->site->getSubCategories($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('Category_has_Sub_Category')]);
        }

        if ($this->settings_model->deleteCategory($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('category_deleted')]);
        }
    }

    public function delete_department($id = null)
    {
        if ($this->site->getSubDepartments($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('Department_has_Sub_Departmen')]);
        }

        if ($this->settings_model->deleteDepartment($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('Department_deleted')]);
        }
    }

     public function delete_tabel($id = null)
    {

        if ($this->settings_model->deleteTabel($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('Tabel_deleted')]);
        }
    }

     public function delete_stuff($id = null)
    {

        if ($this->settings_model->deleteStuff($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('Stuff_deleted')]);
        }
    }

    public function delete_currency($id = null)
    {
        $code=$this->settings_model->getCurrencyByID($id)->code;
        if($this->settings_model->checkCurrencyOperation($code)){

            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_Currency_There_are_relevant_processes')]);
        }else{
            if ($this->settings_model->deleteCurrency($id)) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('currency_deleted')]);
            }
        }
    }

    public function delete_customer_group($id = null)
    {
        if ($this->settings_model->deleteCustomerGroup($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('customer_group_deleted')]);
        }
    }

    public function delete_database($dbfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $dbName=str_replace("cpesoft_","",$this->db->database);

        unlink('./files/backups/'.$dbName.'/' . $dbfile . '.txt');
        $this->session->set_flashdata('messgae', lang('db_deleted'));
        admin_redirect('system_settings/backups');
    }

    public function delete_expense_category($id = null)
    {
        if ($this->settings_model->hasExpenseCategoryRecord($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('category_has_expenses')]);
        }

        if ($this->settings_model->deleteExpenseCategory($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('expense_category_deleted')]);
        }
    }

    public function delete_group($id = null)
    {
        if ($this->settings_model->checkGroupUsers($id)) {
            $this->session->set_flashdata('error', lang('group_x_b_deleted'));
            admin_redirect('system_settings/user_groups');
        }

        if ($this->settings_model->deleteGroup($id)) {
            $this->session->set_flashdata('message', lang('group_deleted'));
            admin_redirect('system_settings/user_groups');
        }
    }

    public function delete_price_group($id = null)
    {
        if ($this->settings_model->deletePriceGroup($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('price_group_deleted')]);
        }
    }

    public function delete_tax_rate($id = null)
    {
        if ($this->settings_model->chexckSalesPurchasesTaxRateByID($id)) {
		    $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_tax_There_are_operations_associated_with_it')]);
        }else{
		if ($this->settings_model->deleteTaxRate($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('tax_rate_deleted')]);
        }
		}
    }

    public function delete_unit($id = null)
    {
        if ($this->settings_model->getUnitChildren($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('unit_has_subunit')]);
        }

        if ($this->settings_model->deleteUnit($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('unit_deleted')]);
        }
    }

    public function delete_variant($id = null)
    {
        if ($this->settings_model->deleteVariant($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('variant_deleted')]);
        }
    }

    public function delete_warehouse($id = null)
    {
        if ($this->settings_model->deleteWarehouse($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('warehouse_deleted')]);
        }
    }

    public function download_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $dbName=str_replace("cpesoft_","",$this->db->database);



        $this->load->helper('download');
        force_download('./files/backups/'.$dbName.'/' . $zipfile . '.zip', null);
        exit();
    }

    public function download_database($dbfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $dbName=str_replace("cpesoft_","",$this->db->database);


        $this->load->library('zip');
        $this->zip->read_file('./files/backups/'.$dbName.'/' . $dbfile . '.txt');
        $name = $dbfile . '.zip';
        $this->zip->download($name);
        exit();
    }

    public function edit_brand($id = null)
    {
        $this->form_validation->set_rules('name', lang('brand_name'), 'trim|required|alpha_numeric_spaces');
        $brand_details = $this->site->getBrandByID($id);
        if ($this->input->post('name') != $brand_details->name) {
            $this->form_validation->set_rules('name', lang('brand_name'), 'required|is_unique[brands.name]');
        }
        $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
        if ($this->input->post('slug') != $brand_details->slug) {
            $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash|is_unique[brands.slug]');
        }
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
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
        } elseif ($this->input->post('edit_brand')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateBrand($id, $data)) {
            $this->session->set_flashdata('message', lang('brand_updated'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['brand']    = $brand_details;
            $this->load->view($this->theme . 'settings/edit_brand', $this->data);
        }
    }

    public function edit_category($id = null)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|required');
        $pr_details = $this->settings_model->getCategoryByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang('category_code'), 'required|is_unique[categories.code]');
        }
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
        $this->form_validation->set_rules('slug', lang('slug'), 'required');
        if ($this->input->post('slug') != $pr_details->slug) {
        //    $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash|is_unique[categories.slug]');
            $this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]');
        }
        $this->form_validation->set_rules('name', lang('category_name'), 'required|min_length[3]');
        $this->form_validation->set_rules('userfile', lang('category_image'), 'xss_clean');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        $hide_pos=0;
		if($this->input->post('hide_pos')==1){
			$hide_pos=1;

		}

		if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'nameEn'        => $this->input->post('nameEn'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
                'parent_id'   => $this->input->post('parent'),
				'hide_pos'   => $hide_pos,
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
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image']     = $this->upload_path . $photo;
                    $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type']          = 'text';
                    $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                    $wm['quality']          = '100';
                    $wm['wm_font_size']     = '16';
                    $wm['wm_font_color']    = '999999';
                    $wm['wm_shadow_color']  = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding']       = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = null;
            }
        } elseif ($this->input->post('edit_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/categories');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCategory($id, $data)) {
            $this->session->set_flashdata('message', lang('category_updated'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['category']   = $this->settings_model->getCategoryByID($id);
            $this->data['categories'] = $this->settings_model->getParentCategories();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_category', $this->data);
        }
    }

    public function edit_department($id = null)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Department_code'), 'trim|required');
        $pr_details = $this->settings_model->getDepartmentByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang('Department_code'), 'required|is_unique[dept.code]');
        }
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required');

        $this->form_validation->set_rules('name', lang('Department_name'), 'required|min_length[3]');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');



		if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'desc' => $this->input->post('description'),
                'parent'   => $this->input->post('parent'),
			];


        } elseif ($this->input->post('edit_department')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/departments');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateDepartment($id, $data)) {
            $this->session->set_flashdata('message', lang('Department_updated'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['department']   = $this->settings_model->getDepartmentByID($id);
            $this->data['departments'] = $this->settings_model->getParentDepartments();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_department', $this->data);
        }
    }

    public function edit_tabel($id = null)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Tabel_code'), 'trim|required');
        $pr_details = $this->settings_model->getTabelByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang('Tabel_code'), 'required|is_unique[resturant_tables.code]');
        }
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required');

        $this->form_validation->set_rules('name', lang('Tabel_name'), 'required|min_length[3]');

        $this->form_validation->set_rules('userfile', lang('image'), 'xss_clean');


		if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'dept'   => $this->input->post('dept'),
			];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 500;
                $config['max_height']    = 1000;
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
                $data['table_icon'] = $photo;
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


        } elseif ($this->input->post('edit_tabel')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/tabels');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateTabel($id, $data)) {
            $this->session->set_flashdata('message', lang('Tabel_updated'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['tabel']   = $this->settings_model->getTabelByID($id);
            $this->data['departments'] = $this->settings_model->getDepartments();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_tabel', $this->data);
        }
    }

    public function edit_stuff($id = null)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('Stuff_code'), 'trim|required');
        $pr_details = $this->settings_model->getStuffByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang('Stuff_code'), 'required|is_unique[resturant_tables.code]');
        }
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
        //$this->form_validation->set_rules('slug', lang('slug'), 'required');

        $this->form_validation->set_rules('name', lang('Stuff_name'), 'required|min_length[3]');



		if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'dept'   => $this->input->post('dept'),
			    'emp_type'   => $this->input->post('emp_type'),
			];


        } elseif ($this->input->post('edit_tabel')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/stuff');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateStuff($id, $data)) {
            $this->session->set_flashdata('message', lang('Stuff_updated'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['stuff']   = $this->settings_model->getStuffByID($id);
            $this->data['stuff_types'] = $this->settings_model->getStuffTypes();
            $this->data['departments'] = $this->settings_model->getDepartments();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_stuff', $this->data);
        }
    }

    public function edit_currency($id = null)
    {
        $this->form_validation->set_rules('code', lang('currency_code'), 'trim|required');
        $cur_details = $this->settings_model->getCurrencyByID($id);
        if ($this->input->post('code') != $cur_details->code) {
            $this->form_validation->set_rules('code', lang('currency_code'), 'required|is_unique[currencies.code]');
        }
        $this->form_validation->set_rules('name', lang('currency_name'), 'required');
        $this->form_validation->set_rules('rate', lang('exchange_rate'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['code'   => $this->input->post('code'),
                'name'        => $this->input->post('name'),
                'rate'        => $this->input->post('rate'),
                'symbol'      => $this->input->post('symbol'),
                'fraction_name'      => $this->input->post('fraction_name'),
                'fraction_symbol'      => $this->input->post('fraction_symbol'),
                'auto_update' => $this->input->post('auto_update') ? $this->input->post('auto_update') : 0,
            ];
        } elseif ($this->input->post('edit_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/currencies');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCurrency($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('currency_updated'));
            admin_redirect('system_settings/currencies');
        } else {

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['currency'] = $this->settings_model->getCurrencyByID($id);
            $this->data['id']       = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_currency', $this->data);
        }
    }

	public function edit_credit_card($id = null)
    {
        $this->form_validation->set_rules('code', lang('Credit_card_code'), 'trim|required');
        $this->form_validation->set_rules('name', lang('Credit_card_name'), 'required');

        if ($this->form_validation->run() == true) {
            $data = ['code'   => $this->input->post('code'),
                'name'        => $this->input->post('name'),
                'second_name'        => $this->input->post('second_name'),
                'isCC'      => 1,
                'isActive' => $this->input->post('isActive') ? $this->input->post('isActive') : 0,
            ];

        } elseif ($this->input->post('edit_credit_card')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/credit_cards');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePaymentType($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Credit_card_has_been_successfully_updated'));
            admin_redirect('system_settings/credit_cards');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['PaymentType'] = $this->settings_model->getPaymentTypeByID($id);
            $this->data['id']       = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_credit_card', $this->data);
        }
    }

    public function edit_customer_group($id = null)
    {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|required');
        $pg_details = $this->settings_model->getCustomerGroupByID($id);
        if ($this->input->post('name') != $pg_details->name) {
            $this->form_validation->set_rules('name', lang('group_name'), 'required|is_unique[tax_rates.name]');
        }
        $this->form_validation->set_rules('percent', lang('group_percentage'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name'),
                'percent'   => $this->input->post('percent'),
            ];
        } elseif ($this->input->post('edit_customer_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/customer_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCustomerGroup($id, $data)) {
            $this->session->set_flashdata('message', lang('customer_group_updated'));
            admin_redirect('system_settings/customer_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['customer_group'] = $this->settings_model->getCustomerGroupByID($id);

            $this->data['id']       = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_customer_group', $this->data);
        }
    }

    public function edit_expense_category($id = null)
    {
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|required');
        $category = $this->settings_model->getExpenseCategoryByID($id);
        if ($this->input->post('code') != $category->code) {
            $this->form_validation->set_rules('code', lang('category_code'), 'required|is_unique[expense_categories.code]');
        }
        $this->form_validation->set_rules('name', lang('category_name'), 'required|min_length[3]');

        if ($this->form_validation->run() == true) {
            $data = [
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
            ];
        } elseif ($this->input->post('edit_expense_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/expense_categories');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateExpenseCategory($id, $data, $photo)) {
            $this->session->set_flashdata('message', lang('expense_category_updated'));
            admin_redirect('system_settings/expense_categories');
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['category'] = $category;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_expense_category', $this->data);
        }
    }

    public function edit_group($id)
    {
        if (!$id || empty($id)) {
            admin_redirect('system_settings/user_groups');
        }

        $group = $this->settings_model->getGroupByID($id);

        $this->form_validation->set_rules('group_name', lang('group_name'), 'required|alpha_dash');

        if ($this->form_validation->run() === true) {
            $data         = ['name' => strtolower($this->input->post('group_name')), 'description' => $this->input->post('description')];
            $group_update = $this->settings_model->updateGroup($id, $data);

            if ($group_update) {
                $this->session->set_flashdata('message', lang('group_udpated'));
            } else {
                $this->session->set_flashdata('error', lang('attempt_failed'));
            }
            admin_redirect('system_settings/user_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group'] = $group;

            $this->data['group_name'] = [
                'name'  => 'group_name',
                'id'    => 'group_name',
                'type'  => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_name', $group->name),
            ];
            $this->data['group_description'] = [
                'name'  => 'group_description',
                'id'    => 'group_description',
                'type'  => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_description', $group->description),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_group', $this->data);
        }
    }

    public function edit_price_group($id = null)
    {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|required|alpha_numeric_spaces');
        $pg_details = $this->settings_model->getPriceGroupByID($id);
        if ($this->input->post('name') != $pg_details->name) {
            $this->form_validation->set_rules('name', lang('group_name'), 'required|is_unique[price_groups.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('edit_price_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/price_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePriceGroup($id, $data)) {
            $this->session->set_flashdata('message', lang('price_group_updated'));
            admin_redirect('system_settings/price_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['price_group'] = $pg_details;
            $this->data['id']          = $id;
            $this->data['modal_js']    = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_price_group', $this->data);
        }
    }

    public function edit_tax_rate($id = null)
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $tax_details = $this->settings_model->getTaxRateByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang('name'), 'required|is_unique[tax_rates.name]');
        }
        $this->form_validation->set_rules('type', lang('type'), 'required');
        $this->form_validation->set_rules('rate', lang('tax_rate'), 'required|numeric');

		//$old_id=$this->settings_model->getTaxRatesActive()->id;

        $tax_active=0;
		if($this->input->post('tax_active')==1){
			//$this->settings_model->updateTaxRateDisactive();
			$tax_active=1;

		}


        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name'),
                'code'      => $this->input->post('code'),
                'type'      => $this->input->post('type'),
                'rate'      => $this->input->post('rate'),
                'category_id'      => $this->input->post('category_id'),
                'tax_active'      => $tax_active,
            ];
        } elseif ($this->input->post('edit_tax_rate')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/tax_rates');
        }


        if ($this->form_validation->run() == true && $this->settings_model->updateTaxRate($id, $data, $old_id)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('tax_rate_updated'));
            admin_redirect('system_settings/tax_rates');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
         }

            $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($id);
			$this->data['tax_categories'] = $this->settings_model->getAllTaxcategory();


            $this->data['id']       = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_tax_rate', $this->data);
        }
    }

    public function edit_unit($id = null)
    {
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');
        $unit_details = $this->site->getUnitByID($id);
        if ($this->input->post('code') != $unit_details->code) {
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[units.code]');
        }
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        if ($this->input->post('base_unit')) {
            $this->form_validation->set_rules('operator', lang('operator'), 'required');
            $this->form_validation->set_rules('operation_value', lang('operation_value'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'name'            => $this->input->post('name'),
                'code'            => $this->input->post('code'),
                'base_unit'       => $this->input->post('base_unit') ? $this->input->post('base_unit') : null,
                'operator'        => $this->input->post('base_unit') ? $this->input->post('operator') : null,
                'operation_value' => $this->input->post('operation_value') ? $this->input->post('operation_value') : null,
            ];
        } elseif ($this->input->post('edit_unit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/units');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateUnit($id, $data)) {
            $this->session->set_flashdata('message', lang('unit_updated'));
            admin_redirect('system_settings/units');
        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['unit']       = $unit_details;
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->load->view($this->theme . 'settings/edit_unit', $this->data);
        }
    }

    public function edit_variant($id = null)
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $tax_details = $this->settings_model->getVariantByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang('name'), 'required|is_unique[variants.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('edit_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/variants');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }
        if ($this->form_validation->run() == true && $this->settings_model->updateVariant($id, $data)) {
            $this->session->set_flashdata('message', lang('variant_updated'));
            admin_redirect('system_settings/variants');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['variant']  = $tax_details;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_variant', $this->data);
        }
    }

    public function edit_warehouse($id = null)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');
        $wh_details = $this->settings_model->getWarehouseByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[warehouses.code]');
        }
        $this->form_validation->set_rules('address', lang('address'), 'required');
        $this->form_validation->set_rules('map', lang('map_image'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'price_group_id' => $this->input->post('price_group'),
            ];

            if ($_FILES['userfile']['size'] > 0) {
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

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('system_settings/warehouses');
                }

                $data['map'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $data['map'];
                $config['new_image']      = 'assets/uploads/thumbs/' . $data['map'];
                $config['maintain_ratio'] = true;
                $config['width']          = 76;
                $config['height']         = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        } elseif ($this->input->post('edit_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/warehouses');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateWarehouse($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('warehouse_updated'));
            admin_redirect('system_settings/warehouses');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouse']    = $this->settings_model->getWarehouseByID($id);
            $this->data['price_groups'] = $this->settings_model->getAllPriceGroups();
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_warehouse', $this->data);
        }
    }

    public function email_templates($template = 'credentials')
    {
        $this->form_validation->set_rules('mail_body', lang('mail_message'), 'trim|required');
        $this->load->helper('file');
        $temp_path = is_dir('./themes/' . $this->theme . 'email_templates/');
        $theme     = $temp_path ? $this->theme : 'default';
        if ($this->form_validation->run() == true) {
            $data = $_POST['mail_body'];
            if (write_file('./themes/' . $this->theme . 'email_templates/' . $template . '.html', $data)) {
                $this->session->set_flashdata('message', lang('message_successfully_saved'));
                admin_redirect('system_settings/email_templates#' . $template);
            } else {
                $this->session->set_flashdata('error', lang('failed_to_save_message'));
                admin_redirect('system_settings/email_templates#' . $template);
            }
        } else {
            $this->data['credentials']     = file_get_contents('./themes/' . $this->theme . 'email_templates/credentials.html');
            $this->data['sale']            = file_get_contents('./themes/' . $this->theme . 'email_templates/sale.html');
            $this->data['quote']           = file_get_contents('./themes/' . $this->theme . 'email_templates/quote.html');
            $this->data['purchase']        = file_get_contents('./themes/' . $this->theme . 'email_templates/purchase.html');
            $this->data['transfer']        = file_get_contents('./themes/' . $this->theme . 'email_templates/transfer.html');
            $this->data['payment']         = file_get_contents('./themes/' . $this->theme . 'email_templates/payment.html');
            $this->data['forgot_password'] = file_get_contents('./themes/' . $this->theme . 'email_templates/forgot_password.html');
            $this->data['activate_email']  = file_get_contents('./themes/' . $this->theme . 'email_templates/activate_email.html');
            $bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('email_templates')]];
            $meta                          = ['page_title' => lang('email_templates'), 'bc' => $bc];
            $this->page_construct('settings/email_templates', $meta, $this->data);
        }
    }

    public function expense_categories()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('expense_categories')]];
        $meta                = ['page_title' => lang('categories'), 'bc' => $bc];
        $this->page_construct('settings/expense_categories', $meta, $this->data);
    }

    public function expense_category_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCategory($id);
                    }
                    $this->session->set_flashdata('message', lang('categories_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCategoryByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'expense_categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function getBrands()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, image, code, name, slug')
            ->from('brands')
            //->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_brand/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_brand') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_brand') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_brand/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_brand/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_brand') . "'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('products/brands_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_brand') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_brand/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function getCategories()
    {
        $print_barcode = anchor('admin/products/print_barcodes/?category=$1', '<i class="fa fa-print"></i>', 'title="' . lang('print_barcodes') . '" class="tip"');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('categories')}.id as id, {$this->db->dbprefix('categories')}.image, {$this->db->dbprefix('categories')}.code, {$this->db->dbprefix('categories')}.name, {$this->db->dbprefix('categories')}.slug, c.name as parent", false)
            ->from('categories')
            ->join('categories c', 'c.id=categories.parent_id', 'left')
            ->group_by('categories.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('system_settings/edit_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_category') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_category') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

     public function getDepartments()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('dept')}.id as id, {$this->db->dbprefix('dept')}.code, {$this->db->dbprefix('dept')}.name,  {$this->db->dbprefix('dept')}.desc, c.name as parent", false)
            ->from('dept')
            ->join('dept c', 'c.id=dept.parent', 'left')
            ->group_by('dept.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('system_settings/edit_department/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Department') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Department') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_department/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

     public function getTabels()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('resturant_tables')}.id as id, {$this->db->dbprefix('resturant_tables')}.table_icon, {$this->db->dbprefix('resturant_tables')}.code, {$this->db->dbprefix('resturant_tables')}.name,  c.name as dept", false)
            ->from('resturant_tables')
            ->join('dept c', 'c.id=resturant_tables.dept', 'left')
            ->group_by('resturant_tables.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('system_settings/edit_tabel/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Tabel') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Tabel') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_tabel/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

     public function getStuff()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('resturant_emp')}.id as id, {$this->db->dbprefix('resturant_emp')}.code, {$this->db->dbprefix('resturant_emp')}.name,  c.name as dept,  ( CASE WHEN t.id = 2 THEN '" . lang('Waiters') . "' ELSE ( CASE WHEN t.id = 1 THEN '" . lang('salesman') . "' ELSE '" . lang('Drivers') . "' END) END)  as emp_type", false)
            ->from('resturant_emp')
            ->join('dept c', 'c.id=resturant_emp.dept', 'left')
            ->join('resturant_emp_types t', 't.id=resturant_emp.emp_type', 'left')
            ->group_by('resturant_emp.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('system_settings/edit_stuff/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Stuff') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Stuff') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_stuff/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function getCurrencies()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, code, name,fraction_name, rate, symbol,fraction_symbol')
            ->from('currencies')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_currency/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_currency') . "'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('system_settings/currencies_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_currency') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            //->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang('edit_currency') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_currency') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function getCreditCards()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select("id, code, name, second_name, IF(isActive=1,'".lang('active')."', '".lang('inactive')."') as isActive ")
            ->from('payment_type')
			->where('isCC',1)
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_credit_card/$1') . "' class='tip' title='" . lang('Edit_credit_card') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function getCustomerGroups()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name, percent')
            ->from('customer_groups')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_customer_group/$1') . "' class='tip' title='" . lang('edit_customer_group') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_customer_group') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_customer_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function getExpenseCategories()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, code, name')
            ->from('expense_categories')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_expense_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_expense_category') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_expense_category') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_expense_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function getPriceGroups()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name')
            ->from('price_groups')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/group_product_prices/$1') . "' class='tip' title='" . lang('group_product_prices') . "'><i class=\"fa fa-eye\"></i></a>  <a href='" . admin_url('system_settings/edit_price_group/$1') . "' class='tip' title='" . lang('edit_price_group') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_price_group') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_price_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function getProductPrices($group_id = null)
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $pp = "( SELECT {$this->db->dbprefix('product_prices')}.product_id as product_id, {$this->db->dbprefix('product_prices')}.price as price FROM {$this->db->dbprefix('product_prices')} WHERE price_group_id = {$group_id} ) PP";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as product_code, {$this->db->dbprefix('products')}.name as product_name, PP.price as price ")
            ->from('products')
            ->join($pp, 'PP.product_id=products.id', 'left')
            ->edit_column('price', '$1__$2', 'id, price')
            ->add_column('Actions', '<div class="text-center"><button class="btn btn-primary btn-xs form-submit" type="button"><i class="fa fa-check"></i></button></div>', 'id');

        echo $this->datatables->generate();
    }

    public function getTaxRates()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('tax_rates')}.id as id,{$this->db->dbprefix('tax_category')}.name as tax_name, {$this->db->dbprefix('tax_rates')}.name as name, {$this->db->dbprefix('tax_rates')}.code as code, {$this->db->dbprefix('tax_rates')}.rate as rate,IF({$this->db->dbprefix('tax_rates')}.tax_active=1,'".lang('tax_active')."', '') as tax_active, {$this->db->dbprefix('tax_rates')}.type as type")
            ->from('tax_rates')
            ->join('tax_category', 'tax_category.id=tax_rates.category_id', 'left')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_tax_rate/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_tax_rate') . "'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('system_settings/taxs_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> </div>", 'id');
             //->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_tax_rate/$1') . "' class='tip' title='" . lang('edit_tax_rate') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> </div>", 'id');
            //->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_tax_rate/$1') . "' class='tip' title='" . lang('edit_tax_rate') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_tax_rate') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_tax_rate/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function getUnits()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('units')}.id as id, {$this->db->dbprefix('units')}.code, {$this->db->dbprefix('units')}.name, b.name as base_unit, {$this->db->dbprefix('units')}.operator, {$this->db->dbprefix('units')}.operation_value", false)
            ->from('units')
            ->join('units b', 'b.id=units.base_unit', 'left')
            ->group_by('units.id')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_unit/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function getVariants()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name')
            ->from('variants')
            //->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_variant/$1') . "' class='tip' title='" . lang('edit_variant') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_variant') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_variant/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_variant/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_variant') . "'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('products/variants_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_variant') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_variant/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function getWarehouses()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('warehouses')}.id as id, map, code, {$this->db->dbprefix('warehouses')}.name as name, {$this->db->dbprefix('price_groups')}.name as price_group, phone, email, address")
            ->from('warehouses')
            ->join('price_groups', 'price_groups.id=warehouses.price_group_id', 'left')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_warehouse/$1') . "' class='tip' title='" . lang('edit_warehouse') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_warehouse') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_warehouse/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function group_product_prices($group_id = null)
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $this->data['price_group'] = $this->settings_model->getPriceGroupByID($group_id);
        $this->data['error']       = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')],  ['link' => admin_url('system_settings/price_groups'), 'page' => lang('price_groups')], ['link' => '#', 'page' => lang('group_product_prices')]];
        $meta                      = ['page_title' => lang('group_product_prices'), 'bc' => $bc];
        $this->page_construct('settings/group_product_prices', $meta, $this->data);
    }

    public function import_brands()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    //admin_redirect('system_settings/brands');
					redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['name', 'code', 'image'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                foreach ($final as $csv_ct) {
                    if (!$this->settings_model->getBrandByName(trim($csv_ct['name']))) {
                        $data[] = [
                            'code'  => trim($csv_ct['code']),
                            'name'  => trim($csv_ct['name']),
                            'image' => trim($csv_ct['image']),
                        ];
                    }
                }
            }

            // $this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && !empty($data) && $this->settings_model->addBrands($data)) {
            $this->session->set_flashdata('message', lang('brands_added'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_brands', $this->data);
        }
    }

    public function import_categories()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings/categories');
                }
                $csv       = $this->upload->file_name;
                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles     = array_shift($arrResult);
                $updated    = '';
                $categories = $subcategories = [];
                foreach ($arrResult as $key => $value) {
                    $code  = trim($value[0]);
                    $name  = trim($value[1]);
                    $pcode = isset($value[4]) ? trim($value[4]) : null;
                    if ($code && $name && trim($value[2])) {
                        $category = [
                            'code'        => $code,
                            'name'        => $name,
                            'slug'        => isset($value[2]) ? trim($value[2]) : $code,
                            'image'       => isset($value[3]) ? trim($value[3]) : 'no_image.png',
                            'parent_id'   => $pcode,
                            'description' => isset($value[5]) ? trim($value[5]) : null,
                        ];
                        if (!empty($pcode) && ($pcategory = $this->settings_model->getCategoryByCode($pcode))) {
                            $category['parent_id'] = $pcategory->id;
                        }
                        if ($c = $this->settings_model->getCategoryByCode($code)) {
                            $updated .= '<p>' . lang('category_updated') . ' (' . $code . ')</p>';
                            $this->settings_model->updateCategory($c->id, $category);
                        } else {
                            if ($category['parent_id']) {
                                $subcategories[] = $category;
                            } else {
                                $categories[] = $category;
                            }
                        }
                    }
                }
            }

            // $this->sma->print_arrays($categories, $subcategories);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCategories($categories, $subcategories)) {
            $this->session->set_flashdata('message', lang('categories_added') . $updated);
            admin_redirect('system_settings/categories');
        } else {
            if ((isset($categories) && empty($categories)) || (isset($subcategories) && empty($subcategories))) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_categories'));
                }
                admin_redirect('system_settings/categories');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_categories', $this->data);
        }
    }

    public function import_expense_categories()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings/expense_categories');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['code', 'name'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                foreach ($final as $csv_ct) {
                    if (!$this->settings_model->getExpenseCategoryByCode(trim($csv_ct['code']))) {
                        $data[] = [
                            'code' => trim($csv_ct['code']),
                            'name' => trim($csv_ct['name']),
                        ];
                    }
                }
            }

            // $this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addExpenseCategories($data)) {
            $this->session->set_flashdata('message', lang('categories_added'));
            admin_redirect('system_settings/expense_categories');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_expense_categories', $this->data);
        }
    }

    public function import_subcategories()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings/categories');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['code', 'name', 'category_code', 'image'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                $rw = 2;
                foreach ($final as $csv_ct) {
                    if (!$this->settings_model->getSubcategoryByCode(trim($csv_ct['code']))) {
                        if ($parent_actegory = $this->settings_model->getCategoryByCode(trim($csv_ct['category_code']))) {
                            $data[] = [
                                'code'        => trim($csv_ct['code']),
                                'name'        => trim($csv_ct['name']),
                                'image'       => trim($csv_ct['image']),
                                'category_id' => $parent_actegory->id,
                            ];
                        } else {
                            $this->session->set_flashdata('error', lang('check_category_code') . ' (' . $csv_ct['category_code'] . '). ' . lang('category_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                            admin_redirect('system_settings/categories');
                        }
                    }
                    $rw++;
                }
            }

            // $this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addSubCategories($data)) {
            $this->session->set_flashdata('message', lang('subcategories_added'));
            admin_redirect('system_settings/categories');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_subcategories', $this->data);
        }
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

        if ($this->form_validation->run() == true && $this->settings_model->updateSetting($data)) {
            if (!DEMO && TIMEZONE != $data['timezone']) {
                if (!$this->write_index($data['timezone'])) {
                    $this->session->set_flashdata('error', lang('setting_updated_timezone_failed'));
                    admin_redirect('system_settings/main_settings');
                }
            }

            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect('system_settings/main_settings');
        } else {
            $this->data['error']           = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['billers']         = $this->site->getAllCompanies('biller');
            $this->data['settings']        = $this->settings_model->getSettings();
            $this->data['currencies']      = $this->settings_model->getAllCurrencies();
            $this->data['date_formats']    = $this->settings_model->getDateFormats();
            $this->data['tax_rates']       = $this->settings_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
            $this->data['price_groups']    = $this->settings_model->getAllPriceGroups();
            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                $this->data['warehouses']      = $this->site->getAllShowrooms();
            }else{
                $this->data['warehouses']      = $this->settings_model->getAllWarehouses();
            }
            $bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('system_settings')]];
            $meta                          = ['page_title' => lang('system_settings'), 'bc' => $bc];
            $this->page_construct('settings/main_settings', $meta, $this->data);
        }

    }
    public function index()
    {
        $this->load->library('gst');
        $this->form_validation->set_rules('site_name', lang('site_name'), 'trim|required');
        $this->form_validation->set_rules('dateformat', lang('dateformat'), 'trim|required');
        $this->form_validation->set_rules('timezone', lang('timezone'), 'trim|required');
        $this->form_validation->set_rules('mmode', lang('maintenance_mode'), 'trim|required');
        //$this->form_validation->set_rules('logo', lang('logo'), 'trim');
        $this->form_validation->set_rules('iwidth', lang('image_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('iheight', lang('image_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('twidth', lang('thumbnail_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('theight', lang('thumbnail_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('display_all_products', lang('display_all_products'), 'trim|numeric|required');
        $this->form_validation->set_rules('watermark', lang('watermark'), 'trim|required');
        $this->form_validation->set_rules('currency', lang('default_currency'), 'trim|required');
        $this->form_validation->set_rules('email', lang('default_email'), 'trim|required');
        $this->form_validation->set_rules('language', lang('language'), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('default_warehouse'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('default_biller'), 'trim|required');
        $this->form_validation->set_rules('tax_rate', lang('product_tax'), 'trim|required');
        $this->form_validation->set_rules('tax_rate2', lang('invoice_tax'), 'trim|required');
        $this->form_validation->set_rules('sales_prefix', lang('sales_prefix'), 'trim');
        $this->form_validation->set_rules('quote_prefix', lang('quote_prefix'), 'trim');
        //$this->form_validation->set_rules('offer_prefix', lang('offer_prefix'), 'trim');
        $this->form_validation->set_rules('purchase_prefix', lang('purchase_prefix'), 'trim');
        $this->form_validation->set_rules('transfer_prefix', lang('transfer_prefix'), 'trim');
        $this->form_validation->set_rules('delivery_prefix', lang('delivery_prefix'), 'trim');
        $this->form_validation->set_rules('payment_prefix', lang('payment_prefix'), 'trim');
        $this->form_validation->set_rules('return_prefix', lang('return_prefix'), 'trim');
        $this->form_validation->set_rules('expense_prefix', lang('expense_prefix'), 'trim');
        $this->form_validation->set_rules('detect_barcode', lang('detect_barcode'), 'trim|required');
        $this->form_validation->set_rules('theme', lang('theme'), 'trim|required');
        $this->form_validation->set_rules('rows_per_page', lang('rows_per_page'), 'trim|required');
        $this->form_validation->set_rules('accounting_method', lang('accounting_method'), 'trim|required');
        $this->form_validation->set_rules('product_serial', lang('product_serial'), 'trim|required');
        $this->form_validation->set_rules('product_discount', lang('product_discount'), 'trim|required');
        $this->form_validation->set_rules('bc_fix', lang('bc_fix'), 'trim|numeric|required');
        $this->form_validation->set_rules('protocol', lang('email_protocol'), 'trim|required');
        $this->form_validation->set_rules('isOpen_invoice', lang('Open_invoice'), 'trim|required');
        $this->form_validation->set_rules('IsInsuranceBusiness', lang('Insurance_business'), 'trim|required');
        $this->form_validation->set_rules('revaluation_prefix', lang('Revaluation_prefix'), 'trim');
        $this->form_validation->set_rules('tobacco_tax', lang('Tobacco_tax'), 'required|numeric|max_length[3]');
        $this->form_validation->set_rules('MenuNotificationsRepeatTime', lang('Repeat_the_notifications_in_minutes'), 'required|numeric|max_length[3]');


        if ($this->input->post('protocol') == 'smtp') {
            $this->form_validation->set_rules('smtp_host', lang('smtp_host'), 'required');
            $this->form_validation->set_rules('smtp_user', lang('smtp_user'), 'required');
            $this->form_validation->set_rules('smtp_pass', lang('smtp_pass'), 'required');
            $this->form_validation->set_rules('smtp_port', lang('smtp_port'), 'required');
        }
        if ($this->input->post('protocol') == 'sendmail') {
            $this->form_validation->set_rules('mailpath', lang('mailpath'), 'required');
        }
        $this->form_validation->set_rules('decimals', lang('decimals'), 'trim|required');
        $this->form_validation->set_rules('decimals_sep', lang('decimals_sep'), 'trim|required');
        $this->form_validation->set_rules('thousands_sep', lang('thousands_sep'), 'trim|required');
        if ($this->Settings->indian_gst) {
            $this->form_validation->set_rules('state', lang('state'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {
            $language = $this->input->post('language');

            if ((file_exists(APPPATH . 'language' . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'sma_lang.php') && is_dir(APPPATH . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language)) || $language == 'english') {
                $lang = $language;
            } else {
                $this->session->set_flashdata('error', lang('language_x_found'));
                admin_redirect('system_settings');
                $lang = 'english';
            }

            $tax1 = ($this->input->post('tax_rate') != 0) ? 1 : 0;
            $tax2 = ($this->input->post('tax_rate2') != 0) ? 1 : 0;

            $data = [
				'site_name' => $this->input->post('site_name'),
                'site_ename' => $this->input->post('site_ename'),
                'rows_per_page'  => $this->input->post('rows_per_page') ? $this->input->post('rows_per_page') : '-1',
                'dateformat'     => $this->input->post('dateformat'),
                'timezone'       => DEMO ? 'Asia/Kuala_Lumpur' : $this->input->post('timezone'),
                'mmode'          => 0,//trim($this->input->post('mmode')),
                'iwidth'         => $this->input->post('iwidth'),
                'iheight'        => $this->input->post('iheight'),
                'twidth'         => $this->input->post('twidth'),
                'theight'        => $this->input->post('theight'),
                'watermark'      => $this->input->post('watermark'),
                // 'reg_ver' => $this->input->post('reg_ver'),
                // 'allow_reg' => $this->input->post('allow_reg'),
                // 'reg_notification' => $this->input->post('reg_notification'),
                'accounting_method'    => $this->input->post('accounting_method'),
                'default_email'        => DEMO ? 'noreply@tecdiary.com' : $this->input->post('email'),
                'language'             => $lang,
                'default_warehouse'    => $this->input->post('warehouse'),
                'default_tax_rate'     => $this->input->post('tax_rate'),
                'default_tax_rate2'    => $this->input->post('tax_rate2'),
                'sales_prefix'         => $this->input->post('sales_prefix'),
                'quote_prefix'         => $this->input->post('quote_prefix'),
                'purchase_prefix'      => $this->input->post('purchase_prefix'),
                'transfer_prefix'      => $this->input->post('transfer_prefix'),
                'delivery_prefix'      => $this->input->post('delivery_prefix'),
                'payment_prefix'       => $this->input->post('payment_prefix'),
                'ppayment_prefix'      => $this->input->post('ppayment_prefix'),
                'qa_prefix'            => $this->input->post('qa_prefix'),
                'return_prefix'        => $this->input->post('return_prefix'),
                'returnp_prefix'       => $this->input->post('returnp_prefix'),
                'expense_prefix'       => $this->input->post('expense_prefix'),
                'auto_detect_barcode'  => trim($this->input->post('detect_barcode')),
                'theme'                => trim($this->input->post('theme')),
                'product_serial'       => $this->input->post('product_serial'),
                'customer_group'       => $this->input->post('customer_group'),
                'product_expiry'       => $this->input->post('product_expiry'),
                'product_discount'     => $this->input->post('product_discount'),
                'default_currency'     => $this->input->post('currency'),
                'bc_fix'               => $this->input->post('bc_fix'),
                'tax1'                 => $tax1,
                'tax2'                 => $tax2,
                'overselling'          => $this->input->post('restrict_sale'),
                'reference_format'     => $this->input->post('reference_format'),
                'racks'                => $this->input->post('racks'),
                'attributes'           => $this->input->post('attributes'),
                'restrict_calendar'    => $this->input->post('restrict_calendar'),
                'captcha'              => $this->input->post('captcha'),
                'item_addition'        => $this->input->post('item_addition'),
                'protocol'             => DEMO ? 'mail' : $this->input->post('protocol'),
                'mailpath'             => $this->input->post('mailpath'),
                'smtp_host'            => $this->input->post('smtp_host'),
                'smtp_user'            => $this->input->post('smtp_user'),
                'smtp_port'            => $this->input->post('smtp_port'),
                'smtp_crypto'          => $this->input->post('smtp_crypto') ? $this->input->post('smtp_crypto') : null,
                'decimals'             => $this->input->post('decimals'),
                'decimals_sep'         => $this->input->post('decimals_sep'),
                'thousands_sep'        => $this->input->post('thousands_sep'),
                'default_biller'       => $this->input->post('biller'),
                'invoice_view'         => $this->input->post('invoice_view'),
                'rtl'                  => 1,//$this->input->post('rtl'),
                'each_spent'           => $this->input->post('each_spent') ? $this->input->post('each_spent') : null,
                'ca_point'             => $this->input->post('ca_point') ? $this->input->post('ca_point') : null,
                'each_sale'            => $this->input->post('each_sale') ? $this->input->post('each_sale') : null,
                'sa_point'             => $this->input->post('sa_point') ? $this->input->post('sa_point') : null,
                'reverse_each_spent'           => $this->input->post('reverse_each_spent') ? $this->input->post('reverse_each_spent') : null,
                'reverse_ca_point'             => $this->input->post('reverse_ca_point') ? $this->input->post('reverse_ca_point') : null,
                'reverse_each_sale'            => $this->input->post('reverse_each_sale') ? $this->input->post('reverse_each_sale') : null,
                'reverse_sa_point'             => $this->input->post('reverse_sa_point') ? $this->input->post('reverse_sa_point') : null,
                'sac'                  => $this->input->post('sac'),
                'qty_decimals'         => $this->input->post('qty_decimals'),
                'display_all_products' => $this->input->post('display_all_products'),
                'display_symbol'       => $this->input->post('display_symbol'),
                'symbol'               => $this->input->post('symbol'),
                'remove_expired'       => $this->input->post('remove_expired'),
                'barcode_separator'    => $this->input->post('barcode_separator'),
                'set_focus'            => $this->input->post('set_focus'),
                'disable_editing'      => $this->input->post('disable_editing'),
                'price_group'          => $this->input->post('price_group'),
                'barcode_img'          => $this->input->post('barcode_renderer'),
                'terms_renderer'          => $this->input->post('terms_renderer'),
                'update_cost'          => $this->input->post('update_cost'),
                'update_price'          => $this->input->post('update_price'),
                'apis'                 => $this->input->post('apis'),
                'pdf_lib'              => $this->input->post('pdf_lib'),
                'state'                => $this->input->post('state'),
				'IsResturant'                => $this->input->post('IsResturant'),
				'IsServices'                => $this->input->post('IsServices'),
				'IsAutomotiveServices'                => $this->input->post('IsAutomotiveServices'),
				'IsLundry'                => $this->input->post('IsLundry'),
				'invoices_sequence'                => $this->input->post('invoices_sequence'),
				'sales_invoice_prefix'                => $this->input->post('sales_invoice_prefix'),
				'default_tax_method'                => $this->input->post('default_tax_method'),
				'show_product_code'                => $this->input->post('show_product_code'),
				'isStation'                => $this->input->post('isStation'),
				'isOpen_invoice'                => $this->input->post('isOpen_invoice'),
				'IsInsuranceBusiness'                => $this->input->post('IsInsuranceBusiness'),
				'invoiceNoRef'                => $this->input->post('invoiceNoRef'),
                'isLogistic'                => $this->input->post('isLogistic'),
                'account_name'                => $this->input->post('account_name'),
                'bank_name'                => $this->input->post('bank_name'),
                'IBAN'                => $this->input->post('IBAN'),
                'swift_code'                => $this->input->post('swift_code'),
                'branch_name'                => $this->input->post('branch_name'),
                'isShipping'                => $this->input->post('isShipping'),
                'isMinimum'                => $this->input->post('isMinimum'),
                'isTobacco'                => $this->input->post('isTobacco'),
                'revaluation_prefix'         => $this->input->post('revaluation_prefix'),
                'IsRental'                => $this->input->post('IsRental'),
                'IsEquipmentRental'                => $this->input->post('IsEquipmentRental'),
                'isDonation'                => $this->input->post('isDonation'),
                'isDeliveryFromSales'                => $this->input->post('isDeliveryFromSales'),
                'IsCurtains'                => $this->input->post('IsCurtains'),
                'tobacco_tax'                => $this->input->post('tobacco_tax'),
                'MenuNotificationsactive'                => $this->input->post('MenuNotificationsactive'),
                'MenuNotificationsRepeatTime'                => $this->input->post('MenuNotificationsRepeatTime'),
                'IsOneRegisterByBiller'                => $this->input->post('IsOneRegisterByBiller'),
                'isDisplayItemsSold'                => $this->input->post('isDisplayItemsSold'),
                'isSubscriptions'                => $this->input->post('isSubscriptions'),
                'insuranceType'                => $this->input->post('insuranceType'),

            ];

            if ($_FILES['terms_attachment']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('terms_attachment')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['terms_attachment'] = $photo;
            }


            if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
            {
                $data['accounting_type'] = $this->input->post('accounting_type');
                $data['isAutoAccountOfflineCustomers'] = $this->input->post('isAutoAccountOfflineCustomers');
                $data['isDisplayCustomersAccountOnInvoice'] = $this->input->post('isDisplayCustomersAccountOnInvoice');
                $data['isDisplayWarehouseOnInvoice'] = $this->input->post('isDisplayWarehouseOnInvoice');
            }

            if(isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset==1)
            {
                $data['aipayment_prefix'] = $this->input->post('aipayment_prefix');
            }

            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){

				$data['offer_prefix'] = $this->input->post('offer_prefix');
			  }




            if ($this->input->post('smtp_pass')) {
                $data['smtp_pass'] = $this->input->post('smtp_pass');
            }

            //$this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSetting($data)) {
            if (!DEMO && TIMEZONE != $data['timezone']) {
                if (!$this->write_index($data['timezone'])) {
                    $this->session->set_flashdata('error', lang('setting_updated_timezone_failed'));
                    admin_redirect('system_settings');
                }
            }

            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect('system_settings');
        } else {
            $this->data['error']           = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['billers']         = $this->site->getAllCompanies('biller');
            $this->data['settings']        = $this->settings_model->getSettings();
            $this->data['currencies']      = $this->settings_model->getAllCurrencies();
            $this->data['date_formats']    = $this->settings_model->getDateFormats();
            $this->data['tax_rates']       = $this->settings_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
            $this->data['price_groups']    = $this->settings_model->getAllPriceGroups();
            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                $this->data['warehouses']      = $this->site->getAllShowrooms();
            }else{
                $this->data['warehouses']      = $this->settings_model->getAllWarehouses();
            }
            $bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('system_settings')]];
            $meta                          = ['page_title' => lang('system_settings'), 'bc' => $bc];
            $this->page_construct('settings/index', $meta, $this->data);
        }
    }

    public function save_site_settings()
    {
        $this->form_validation->set_rules('site_name', lang('site_name'), 'trim|required');
        $this->form_validation->set_rules('dateformat', lang('dateformat'), 'trim|required');
        $this->form_validation->set_rules('timezone', lang('timezone'), 'trim|required');
        $this->form_validation->set_rules('currency', lang('default_currency'), 'trim|required');
        $this->form_validation->set_rules('email', lang('default_email'), 'trim|required');
        $this->form_validation->set_rules('language', lang('language'), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('default_warehouse'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('default_biller'), 'trim|required');
        $this->form_validation->set_rules('theme', lang('theme'), 'trim|required');
        $this->form_validation->set_rules('rows_per_page', lang('rows_per_page'), 'trim|required');
        $this->form_validation->set_rules('accounting_method', lang('accounting_method'), 'trim|required');
        $this->form_validation->set_rules('tobacco_tax', lang('Tobacco_tax'), 'required|numeric|max_length[3]');
        $this->form_validation->set_rules('MenuNotificationsRepeatTime', lang('Repeat_the_notifications_in_minutes'), 'required|numeric|max_length[3]');

        if ($this->form_validation->run() == true) {
            $language = $this->input->post('language');

            if ((file_exists(APPPATH . 'language' . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'sma_lang.php') && is_dir(APPPATH . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language)) || $language == 'english') {
                $lang = $language;
            } else {
                $this->session->set_flashdata('error', lang('language_x_found'));
                admin_redirect('system_settings');
                $lang = 'english';
            }

            $data = [
                'site_name'                   => $this->input->post('site_name'),
                'site_ename'                  => $this->input->post('site_ename'),
                'rows_per_page'               => $this->input->post('rows_per_page') ? $this->input->post('rows_per_page') : '-1',
                'dateformat'                  => $this->input->post('dateformat'),
                'timezone'                    => DEMO ? 'Asia/Kuala_Lumpur' : $this->input->post('timezone'),
                'mmode'                       => 0,
                'accounting_method'           => $this->input->post('accounting_method'),
                'default_email'               => DEMO ? 'noreply@tecdiary.com' : $this->input->post('email'),
                'language'                    => $lang,
                'default_warehouse'           => $this->input->post('warehouse'),
                'default_biller'              => $this->input->post('biller'),
                'theme'                       => trim($this->input->post('theme')),
                'customer_group'              => $this->input->post('customer_group'),
                'default_currency'            => $this->input->post('currency'),
                'restrict_calendar'           => $this->input->post('restrict_calendar'),
                'captcha'                     => $this->input->post('captcha'),
                'rtl'                         => 1,
                'disable_editing'             => $this->input->post('disable_editing'),
                'price_group'                 => $this->input->post('price_group'),
                'pdf_lib'                     => $this->input->post('pdf_lib'),
                'IsResturant'                 => $this->input->post('IsResturant'),
                'IsServices'                  => $this->input->post('IsServices'),
                'IsAutomotiveServices'        => $this->input->post('IsAutomotiveServices'),
                'IsLundry'                    => $this->input->post('IsLundry'),
                'isStation'                   => $this->input->post('isStation'),
                'isLogistic'                  => $this->input->post('isLogistic'),
                'isShipping'                  => $this->input->post('isShipping'),
                'isTobacco'                   => $this->input->post('isTobacco'),
                'IsRental'                    => $this->input->post('IsRental'),
                'IsEquipmentRental'           => $this->input->post('IsEquipmentRental'),
                'isDonation'                  => $this->input->post('isDonation'),
                'IsCurtains'                  => $this->input->post('IsCurtains'),
                'tobacco_tax'                 => $this->input->post('tobacco_tax'),
                'MenuNotificationsactive'     => $this->input->post('MenuNotificationsactive'),
                'MenuNotificationsRepeatTime' => $this->input->post('MenuNotificationsRepeatTime'),
                'IsOneRegisterByBiller'       => $this->input->post('IsOneRegisterByBiller'),
                'isSubscriptions'             => $this->input->post('isSubscriptions'),
            ];

            if (isset($this->Settings->preAccounting) && $this->Settings->preAccounting == 1) {
                $data['accounting_type']               = $this->input->post('accounting_type');
                $data['isAutoAccountOfflineCustomers'] = $this->input->post('isAutoAccountOfflineCustomers');
            }

            if ($this->settings_model->updateSetting($data)) {
                if (!DEMO && TIMEZONE != $data['timezone']) {
                    if (!$this->write_index($data['timezone'])) {
                        $this->session->set_flashdata('error', lang('setting_updated_timezone_failed'));
                        admin_redirect('system_settings');
                    }
                }
                $this->session->set_flashdata('message', lang('setting_updated'));
            } else {
                $this->session->set_flashdata('error', lang('error_occurred'));
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        admin_redirect('system_settings');
    }

    public function save_product_settings()
    {
        $this->form_validation->set_rules('tax_rate', lang('product_tax'), 'trim|required');
        $this->form_validation->set_rules('default_tax_method', lang('Default_tax_method'), 'trim|required');
        $this->form_validation->set_rules('iwidth', lang('image_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('iheight', lang('image_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('twidth', lang('thumbnail_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('theight', lang('thumbnail_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('display_all_products', lang('display_all_products'), 'trim|numeric|required');
        $this->form_validation->set_rules('watermark', lang('watermark'), 'trim|required');
        $this->form_validation->set_rules('product_serial', lang('product_serial'), 'trim|required');
        $this->form_validation->set_rules('product_discount', lang('product_discount'), 'trim|required');
        $this->form_validation->set_rules('bc_fix', lang('bc_fix'), 'trim|numeric|required');
        $this->form_validation->set_rules('detect_barcode', lang('detect_barcode'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $tax1 = ($this->input->post('tax_rate') != 0) ? 1 : 0;
            $data = [
                'default_tax_rate'     => $this->input->post('tax_rate'),
                'default_tax_method'   => $this->input->post('default_tax_method'),
                'tax1'                 => $tax1,
                'racks'                => $this->input->post('racks'),
                'attributes'           => $this->input->post('attributes'),
                'product_expiry'       => $this->input->post('product_expiry'),
                'remove_expired'       => $this->input->post('remove_expired'),
                'iwidth'               => $this->input->post('iwidth'),
                'iheight'              => $this->input->post('iheight'),
                'twidth'               => $this->input->post('twidth'),
                'theight'              => $this->input->post('theight'),
                'watermark'            => $this->input->post('watermark'),
                'display_all_products' => $this->input->post('display_all_products'),
                'item_addition'        => $this->input->post('item_addition'),
                'product_serial'       => $this->input->post('product_serial'),
                'product_discount'     => $this->input->post('product_discount'),
                'bc_fix'               => $this->input->post('bc_fix'),
                'auto_detect_barcode'  => trim($this->input->post('detect_barcode')),
                'barcode_separator'    => $this->input->post('barcode_separator'),
                'barcode_img'          => $this->input->post('barcode_renderer'),
                'show_product_code'    => $this->input->post('show_product_code'),
            ];

            if ($this->settings_model->updateSetting($data)) {
                $this->session->set_flashdata('message', lang('setting_updated'));
            } else {
                $this->session->set_flashdata('error', lang('error_occurred'));
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        admin_redirect('system_settings');
    }

    public function save_sales_settings()
    {
        $this->load->library('gst');
        $this->form_validation->set_rules('reference_format', lang('reference_format'), 'trim|required');
        $this->form_validation->set_rules('invoices_sequence', lang('Getting_invoices_number_sequence'), 'trim|required');
        $this->form_validation->set_rules('invoice_view', lang('invoice_view'), 'trim|required');
        $this->form_validation->set_rules('isOpen_invoice', lang('Open_invoice'), 'trim|required');
        $this->form_validation->set_rules('IsInsuranceBusiness', lang('Insurance_business'), 'trim|required');
        if ($this->Settings->indian_gst) {
            $this->form_validation->set_rules('state', lang('state'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {
            $tax2 = ($this->input->post('tax_rate2') && $this->input->post('tax_rate2') != 0) ? 1 : 0;
            $data = [
                'overselling'          => $this->input->post('restrict_sale'),
                'reference_format'     => $this->input->post('reference_format'),
                'default_tax_rate2'    => $this->input->post('tax_rate2') ? $this->input->post('tax_rate2') : 0,
                'tax2'                 => $tax2,
                'set_focus'            => $this->input->post('set_focus'),
                'invoices_sequence'    => $this->input->post('invoices_sequence'),
                'invoice_view'         => $this->input->post('invoice_view'),
                'invoiceNoRef'         => $this->input->post('invoiceNoRef'),
                'isOpen_invoice'       => $this->input->post('isOpen_invoice'),
                'IsInsuranceBusiness'  => $this->input->post('IsInsuranceBusiness'),
                'insuranceType'        => $this->input->post('insuranceType'),
                'state'                => $this->input->post('state'),
                'isDeliveryFromSales'  => $this->input->post('isDeliveryFromSales'),
                'isDisplayItemsSold'   => $this->input->post('isDisplayItemsSold'),
                'terms_renderer'       => $this->input->post('terms_renderer'),
            ];

            if (isset($this->Settings->preAccounting) && $this->Settings->preAccounting == 1) {
                $data['isDisplayCustomersAccountOnInvoice'] = $this->input->post('isDisplayCustomersAccountOnInvoice');
                $data['isDisplayWarehouseOnInvoice']        = $this->input->post('isDisplayWarehouseOnInvoice');
            }

            if (isset($_FILES['terms_attachment']) && $_FILES['terms_attachment']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('terms_attachment')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings');
                }
                $photo                    = $this->upload->file_name;
                $data['terms_attachment'] = $photo;
            }

            if ($this->settings_model->updateSetting($data)) {
                $this->session->set_flashdata('message', lang('setting_updated'));
            } else {
                $this->session->set_flashdata('error', lang('error_occurred'));
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        admin_redirect('system_settings');
    }

    public function save_prefix_settings()
    {
        $this->form_validation->set_rules('sales_prefix', lang('sales_prefix'), 'trim');
        $this->form_validation->set_rules('quote_prefix', lang('quote_prefix'), 'trim');
        $this->form_validation->set_rules('purchase_prefix', lang('purchase_prefix'), 'trim');
        $this->form_validation->set_rules('transfer_prefix', lang('transfer_prefix'), 'trim');
        $this->form_validation->set_rules('delivery_prefix', lang('delivery_prefix'), 'trim');
        $this->form_validation->set_rules('payment_prefix', lang('payment_prefix'), 'trim');
        $this->form_validation->set_rules('return_prefix', lang('return_prefix'), 'trim');
        $this->form_validation->set_rules('expense_prefix', lang('expense_prefix'), 'trim');
        $this->form_validation->set_rules('revaluation_prefix', lang('Revaluation_prefix'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = [
                'sales_prefix'       => $this->input->post('sales_prefix'),
                'quote_prefix'       => $this->input->post('quote_prefix'),
                'purchase_prefix'    => $this->input->post('purchase_prefix'),
                'transfer_prefix'    => $this->input->post('transfer_prefix'),
                'delivery_prefix'    => $this->input->post('delivery_prefix'),
                'payment_prefix'     => $this->input->post('payment_prefix'),
                'ppayment_prefix'    => $this->input->post('ppayment_prefix'),
                'qa_prefix'          => $this->input->post('qa_prefix'),
                'return_prefix'      => $this->input->post('return_prefix'),
                'returnp_prefix'     => $this->input->post('returnp_prefix'),
                'expense_prefix'     => $this->input->post('expense_prefix'),
                'revaluation_prefix' => $this->input->post('revaluation_prefix'),
            ];

            if (isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset == 1) {
                $data['aipayment_prefix'] = $this->input->post('aipayment_prefix');
            }

            if (isset($this->Settings->isCars) && $this->Settings->isCars == 1) {
                $data['offer_prefix'] = $this->input->post('offer_prefix');
            }

            if ($this->settings_model->updateSetting($data)) {
                $this->session->set_flashdata('message', lang('setting_updated'));
            } else {
                $this->session->set_flashdata('error', lang('error_occurred'));
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        admin_redirect('system_settings');
    }

    public function save_money_settings()
    {
        $this->form_validation->set_rules('decimals', lang('decimals'), 'trim|required');
        $this->form_validation->set_rules('qty_decimals', lang('qty_decimals'), 'trim|required');
        $this->form_validation->set_rules('decimals_sep', lang('decimals_sep'), 'trim|required');
        $this->form_validation->set_rules('thousands_sep', lang('thousands_sep'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = [
                'decimals'       => $this->input->post('decimals'),
                'qty_decimals'   => $this->input->post('qty_decimals'),
                'decimals_sep'   => $this->input->post('decimals_sep'),
                'thousands_sep'  => $this->input->post('thousands_sep'),
                'display_symbol' => $this->input->post('display_symbol'),
                'symbol'         => $this->input->post('symbol'),
                'sac'            => $this->input->post('sac') ? $this->input->post('sac') : 0,
            ];

            if ($this->settings_model->updateSetting($data)) {
                $this->session->set_flashdata('message', lang('setting_updated'));
            } else {
                $this->session->set_flashdata('error', lang('error_occurred'));
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        admin_redirect('system_settings');
    }

    public function save_bank_settings()
    {
        $this->form_validation->set_rules('account_name', lang('Account_name'), 'trim');
        $this->form_validation->set_rules('bank_name', lang('BANK_NAME'), 'trim');
        $this->form_validation->set_rules('IBAN', lang('IBAN'), 'trim');
        $this->form_validation->set_rules('branch_name', lang('Branch_name'), 'trim');
        $this->form_validation->set_rules('swift_code', lang('Swift_Code'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = [
                'account_name' => $this->input->post('account_name'),
                'bank_name'    => $this->input->post('bank_name'),
                'IBAN'         => $this->input->post('IBAN'),
                'swift_code'   => $this->input->post('swift_code'),
                'branch_name'  => $this->input->post('branch_name'),
            ];

            if ($this->settings_model->updateSetting($data)) {
                $this->session->set_flashdata('message', lang('setting_updated'));
            } else {
                $this->session->set_flashdata('error', lang('error_occurred'));
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        admin_redirect('system_settings');
    }

    public function save_email_settings()
    {
        $this->form_validation->set_rules('protocol', lang('email_protocol'), 'trim|required');

        if ($this->input->post('protocol') == 'smtp') {
            $this->form_validation->set_rules('smtp_host', lang('smtp_host'), 'required');
            $this->form_validation->set_rules('smtp_user', lang('smtp_user'), 'required');
            $this->form_validation->set_rules('smtp_port', lang('smtp_port'), 'required');
        }
        if ($this->input->post('protocol') == 'sendmail') {
            $this->form_validation->set_rules('mailpath', lang('mailpath'), 'required');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'protocol'    => DEMO ? 'mail' : $this->input->post('protocol'),
                'mailpath'    => $this->input->post('mailpath'),
                'smtp_host'   => $this->input->post('smtp_host'),
                'smtp_user'   => $this->input->post('smtp_user'),
                'smtp_port'   => $this->input->post('smtp_port'),
                'smtp_crypto' => $this->input->post('smtp_crypto') ? $this->input->post('smtp_crypto') : null,
            ];

            if ($this->input->post('smtp_pass')) {
                $data['smtp_pass'] = $this->input->post('smtp_pass');
            }

            if ($this->settings_model->updateSetting($data)) {
                $this->session->set_flashdata('message', lang('setting_updated'));
            } else {
                $this->session->set_flashdata('error', lang('error_occurred'));
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        admin_redirect('system_settings');
    }

    public function save_points_settings()
    {
        $data = [
            'each_spent' => $this->input->post('each_spent') ? $this->input->post('each_spent') : null,
            'ca_point'   => $this->input->post('ca_point') ? $this->input->post('ca_point') : null,
            'each_sale'  => $this->input->post('each_sale') ? $this->input->post('each_sale') : null,
            'sa_point'   => $this->input->post('sa_point') ? $this->input->post('sa_point') : null,
        ];

        if ($this->settings_model->updateSetting($data)) {
            $this->session->set_flashdata('message', lang('setting_updated'));
        } else {
            $this->session->set_flashdata('error', lang('error_occurred'));
        }
        admin_redirect('system_settings');
    }

    public function save_reverse_points_settings()
    {
        $data = [
            'reverse_each_spent' => $this->input->post('reverse_each_spent') ? $this->input->post('reverse_each_spent') : null,
            'reverse_ca_point'   => $this->input->post('reverse_ca_point') ? $this->input->post('reverse_ca_point') : null,
            'reverse_each_sale'  => $this->input->post('reverse_each_sale') ? $this->input->post('reverse_each_sale') : null,
            'reverse_sa_point'   => $this->input->post('reverse_sa_point') ? $this->input->post('reverse_sa_point') : null,
        ];

        if ($this->settings_model->updateSetting($data)) {
            $this->session->set_flashdata('message', lang('setting_updated'));
        } else {
            $this->session->set_flashdata('error', lang('error_occurred'));
        }
        admin_redirect('system_settings');
    }

    public function install_update($file, $m_version, $version)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->load->helper('update');
        save_remote_file($file . '.zip');
        $this->sma->unzip('./files/updates/' . $file . '.zip');
        if ($m_version) {
            $this->load->library('migration');
            if (!$this->migration->latest()) {
                $this->session->set_flashdata('error', $this->migration->error_string());
                admin_redirect('system_settings/updates');
            }
        }
        $this->db->update('settings', ['version' => $version, 'update' => 0], ['setting_id' => 1]);
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        admin_redirect('system_settings/updates');
    }

    public function paypal()
    {
        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
        if ($this->input->post('active')) {
            $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'required');
        }
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = ['active'         => $this->input->post('active'),
                'account_email'       => $this->input->post('account_email'),
                'fixed_charges'       => $this->input->post('fixed_charges'),
                'extra_charges_my'    => $this->input->post('extra_charges_my'),
                'extra_charges_other' => $this->input->post('extra_charges_other'),
            ];
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePaypal($data)) {
            $this->session->set_flashdata('message', $this->lang->line('paypal_setting_updated'));
            admin_redirect('system_settings/paypal');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['paypal'] = $this->settings_model->getPaypalSettings();

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('paypal_settings')]];
            $meta = ['page_title' => lang('paypal_settings'), 'bc' => $bc];
            $this->page_construct('settings/paypal', $meta, $this->data);
        }
    }

    public function permissions($id = null)
    {
        $this->form_validation->set_rules('group', lang('group'), 'is_natural_no_zero');
        if ($this->form_validation->run() == true) {
            $data = [
                'products-warehouses'             => $this->input->post('products-warehouses'),
                'products-edit_warehouse'              => $this->input->post('products-edit_warehouse'),
                'products-add_warehouse'               => $this->input->post('products-add_warehouse'),
                'products-delete_warehouse'            => $this->input->post('products-delete_warehouse'),
                'products-adjustments'       => $this->input->post('products-adjustments'),
                'products-approved_adjustments'       => $this->input->post('products-approved_adjustments'),
                'products-destruction_inventory'       => $this->input->post('products-destruction_inventory'),
                'products-reports'       => $this->input->post('products-reports'),
                'products-index'             => $this->input->post('products-index'),
                'products-edit'              => $this->input->post('products-edit'),
                'products-add'               => $this->input->post('products-add'),
                'products-delete'            => $this->input->post('products-delete'),
                'products-cost'              => $this->input->post('products-cost'),
                'products-price'             => $this->input->post('products-price'),
                'products-barcode'           => $this->input->post('products-barcode'),
                'products-stock_count'       => $this->input->post('products-stock_count'),
                'products-manual_inventory'       => $this->input->post('products-manual_inventory'),

                'products-units'       => $this->input->post('products-units'),
                'products-add_unit'       => $this->input->post('products-add_unit'),
                'products-edit_unit'       => $this->input->post('products-edit_unit'),
                'products-delete_unit'       => $this->input->post('products-delete_unit'),
                'products-units_xls'       => $this->input->post('products-units_xls'),

                'products-categories'       => $this->input->post('products-categories'),
                'products-add_category'       => $this->input->post('products-add_category'),
                'products-edit_category'       => $this->input->post('products-edit_category'),
                'products-delete_category'       => $this->input->post('products-delete_category'),
                'products-category_xls'       => $this->input->post('products-category_xls'),

                'products-warehouse_stock'       => $this->input->post('products-warehouse_stock'),
                'products-adjustments_report'       => $this->input->post('products-adjustments_report'),
                'products-products_item_report'       => $this->input->post('products-products_item_report'),
                'products-products_report'       => $this->input->post('products-products_report'),

                'products-quantity_alerts'       => $this->input->post('products-quantity_alerts'),
                'products-expiry_alerts'       => $this->input->post('products-expiry_alerts'),
                'products-warehouse_count'       => $this->input->post('products-warehouse_count'),
                'products-warehouse_store'       => $this->input->post('products-warehouse_store'),
                'products-destruction'       => $this->input->post('products-destruction'),


                'customers-index'            => $this->input->post('customers-index'),
                'customers-edit'             => $this->input->post('customers-edit'),
                'customers-add'              => $this->input->post('customers-add'),
                'customers-delete'           => $this->input->post('customers-delete'),
                'customers-deposits'         => $this->input->post('customers-deposits'),
                'customers-delete_deposit'   => $this->input->post('customers-delete_deposit'),

                'suppliers-index'            => $this->input->post('suppliers-index'),
                'suppliers-edit'             => $this->input->post('suppliers-edit'),
                'suppliers-add'              => $this->input->post('suppliers-add'),
                'suppliers-delete'           => $this->input->post('suppliers-delete'),

                'sales-reports'       => $this->input->post('sales-sales_report'),
                'sales-index'                => $this->input->post('sales-index'),
                'sales-edit'                 => $this->input->post('sales-edit'),
                'sales-add'                  => $this->input->post('sales-add'),
                'sales-delete'               => $this->input->post('sales-delete'),
                'sales-details'               => $this->input->post('sales-details'),
                'sales-email'                => $this->input->post('sales-email'),
                'sales-pdf'                  => $this->input->post('sales-pdf'),
                'sales-csv'                  => $this->input->post('sales-csv'),
                'sales-deliveries'           => $this->input->post('sales-deliveries'),
                'sales-edit_delivery'        => $this->input->post('sales-edit_delivery'),
                'sales-add_delivery'         => $this->input->post('sales-add_delivery'),
                'sales-delete_delivery'      => $this->input->post('sales-delete_delivery'),
                'sales-details_delivery'      => $this->input->post('sales-details_delivery'),
                'sales-email_delivery'       => $this->input->post('sales-email_delivery'),
                'sales-pdf_delivery'         => $this->input->post('sales-pdf_delivery'),
                'sales-gift_cards'           => $this->input->post('sales-gift_cards'),
                'sales-edit_gift_card'       => $this->input->post('sales-edit_gift_card'),
                'sales-add_gift_card'        => $this->input->post('sales-add_gift_card'),
                'sales-delete_gift_card'     => $this->input->post('sales-delete_gift_card'),
                'sales-return_sales'         => $this->input->post('sales-return_sales'),
                'sales-payments'             => $this->input->post('sales-payments'),
                'sales-add_payment'             => $this->input->post('sales-add_payment'),
                'sales-edit_payment'             => $this->input->post('sales-edit_payment'),
                'sales-delete_payment'             => $this->input->post('sales-delete_payment'),
                'sales-payment_note'             => $this->input->post('sales-payment_note'),
                'sales-payment_note_gift_card'             => $this->input->post('sales-payment_note_gift_card'),
                'sales-charge_gift_card'             => $this->input->post('sales-charge_gift_card'),
                'sales-price_groups'             => $this->input->post('sales-price_groups'),
                'sales-add_price_group'             => $this->input->post('sales-add_price_group'),
                'sales-edit_price_group'             => $this->input->post('sales-edit_price_group'),
                'sales-delete_price_group'             => $this->input->post('sales-delete_price_group'),
                'sales-group_product_prices'             => $this->input->post('sales-group_product_prices'),

                'sales-best_sellers'         => $this->input->post('sales-best_sellers'),
                'sales-daily_sales'         => $this->input->post('sales-daily_sales'),
                'sales-monthly_sales'         => $this->input->post('sales-monthly_sales'),
                'sales-sales_report'         => $this->input->post('sales-sales_report'),
                'sales-transaction_report'         => $this->input->post('sales-transaction_report'),
                'sales-delivery_report'         => $this->input->post('sales-delivery_report'),
                'sales-payments_report'         => $this->input->post('sales-payments_report'),
                'sales-customers_report'         => $this->input->post('sales-customers_report'),
                'sales-customers_payments'         => $this->input->post('sales-customers_payments'),
                'sales-register_report'         => $this->input->post('sales-register_report'),
                'sales-credit_notes'         => $this->input->post('sales-credit_notes'),
                'sales-commercial_discounts'         => $this->input->post('sales-commercial_discounts'),

             /*

             group_product_prices
update_prices_excel

             promo-add_promo_type
             edit_promo_type
             update_promo_type

*/
                'quotes-index'               => $this->input->post('quotes-index'),
                'quotes-edit'                => $this->input->post('quotes-edit'),
                'quotes-add'                 => $this->input->post('quotes-add'),
                'quotes-delete'              => $this->input->post('quotes-delete'),
                'quotes-email'               => $this->input->post('quotes-email'),
                'quotes-pdf'                 => $this->input->post('quotes-pdf'),
                'promos-index'                => $this->input->post('promos-index'),
                'promos-edit'                 => $this->input->post('promos-edit'),
                'promos-add'                  => $this->input->post('promos-add'),
                'promos-delete'               => $this->input->post('promos-delete'),
                'purchases-index'            => $this->input->post('purchases-index'),
                'purchases-edit'             => $this->input->post('purchases-edit'),
                'purchases-add'              => $this->input->post('purchases-add'),
                'purchases-delete'           => $this->input->post('purchases-delete'),
                'purchases-email'            => $this->input->post('purchases-email'),
                'purchases-pdf'              => $this->input->post('purchases-pdf'),
                'purchases-payments'         => $this->input->post('purchases-payments'),
                'purchases-expenses'         => $this->input->post('purchases-expenses'),
                'purchases-return_purchases' => $this->input->post('purchases-return_purchases'),
                'purchases-daily_purchases'              => $this->input->post('purchases-daily_purchases'),
                'purchases-monthly_purchases'              => $this->input->post('purchases-monthly_purchases'),
                'purchases-reports'       => $this->input->post('purchases-reports'),
                'purchases-expenses_report'              => $this->input->post('purchases-expenses_report'),

                'purchases-Debit_Notes'              => $this->input->post('purchases-Debit_Notes'),
                'purchases-payments_report'              => $this->input->post('purchases-payments_report'),
                'purchases-expense_payments_report'              => $this->input->post('purchases-expense_payments_report'),
                'purchases-add_expense'         => $this->input->post('purchases-add_expense'),
                'purchases-edit_expense'         => $this->input->post('purchases-edit_expense'),
                'purchases-delete_expense'         => $this->input->post('purchases-delete_expense'),
                'purchases-xls_expense'         => $this->input->post('purchases-xls_expense'),
                'purchases-add_payment'         => $this->input->post('purchases-add_payment'),
                'purchases-edit_payment'         => $this->input->post('purchases-edit_payment'),
                'purchases-delete_payment'         => $this->input->post('purchases-delete_payment'),
                'purchases-email_payment'         => $this->input->post('purchases-email_payment'),
                'purchases-csv'         => $this->input->post('purchases-csv'),
                'purchases-update_status'         => $this->input->post('purchases-update_status'),
                'purchases-ordered'         => $this->input->post('purchases-ordered'),
                'purchases-pending'         => $this->input->post('purchases-pending'),
                'purchases-received'         => $this->input->post('purchases-received'),





                'transfers-index'            => $this->input->post('transfers-index'),
                'transfers-edit'             => $this->input->post('transfers-edit'),
                'transfers-add'              => $this->input->post('transfers-add'),
                'transfers-delete'           => $this->input->post('transfers-delete'),
                'transfers-email'            => $this->input->post('transfers-email'),
                'transfers-pdf'              => $this->input->post('transfers-pdf'),
                'transfers-ordered'              => $this->input->post('transfers-ordered'),
                'transfers-pending'              => $this->input->post('transfers-pending'),
                'transfers-sent'              => $this->input->post('transfers-sent'),
                'transfers-received'              => $this->input->post('transfers-received'),
                'transfers_date'              => $this->input->post('transfers_date'),

                'reports-quantity_alerts'    => $this->input->post('reports-quantity_alerts'),
                'reports-expiry_alerts'      => $this->input->post('reports-expiry_alerts'),
                'reports-products'           => $this->input->post('reports-products'),
                'reports-daily_sales'        => $this->input->post('reports-daily_sales'),
                'reports-monthly_sales'      => $this->input->post('reports-monthly_sales'),
                'reports-payments'           => $this->input->post('reports-payments'),
                'reports-sales'              => $this->input->post('sales-reports'),
                'reports-purchases'          => $this->input->post('reports-purchases'),
                'reports-customers'          => $this->input->post('sales-reports'),
                'reports-suppliers'          => $this->input->post('reports-suppliers'),
                'reports-profit_loss'          => $this->input->post('reports-profit_loss'),
                'reports-expenses'           => $this->input->post('reports-expenses'),
                'reports-daily_purchases'    => $this->input->post('reports-daily_purchases'),
                'reports-monthly_purchases'  => $this->input->post('reports-monthly_purchases'),
                'reports-tax'                => $this->input->post('reports-tax'),
                'reports-users_report'                => $this->input->post('reports-users_report'),

                'bulk_actions'               => $this->input->post('bulk_actions'),


                'edit_price'                 => $this->input->post('edit_price'),
                'returns-index'              => $this->input->post('returns-index'),
                'returns-edit'               => $this->input->post('returns-edit'),
                'returns-add'                => $this->input->post('returns-add'),
                'returns-delete'             => $this->input->post('returns-delete'),
                'returns-email'              => $this->input->post('returns-email'),
                'returns-pdf'                => $this->input->post('returns-pdf'),

				'accounts-accounts_tree'       => $this->input->post('accounts-accounts_tree'),
                'accounts-add_account'       => $this->input->post('accounts-add_account'),
                'accounts-edit_account'       => $this->input->post('accounts-edit_account'),
                'accounts-delete_account'       => $this->input->post('accounts-delete_account'),
                'accounts-tree_modal_view'       => $this->input->post('accounts-tree_modal_view'),

                'accounts-opening_balance'       => $this->input->post('accounts-opening_balance'),
                'accounts-add_opening_balance'       => $this->input->post('accounts-add_opening_balance'),
                'accounts-edit_opening_balance'       => $this->input->post('accounts-edit_opening_balance'),
                'accounts-delete_opening_balance'       => $this->input->post('accounts-delete_opening_balance'),
                'accounts-view_opening_balance'       => $this->input->post('accounts-view_opening_balance'),

                'accounts-entry_index'       => $this->input->post('accounts-entry_index'),
                'accounts-entry_add'       => $this->input->post('accounts-entry_add'),
                'accounts-entry_edit'       => $this->input->post('accounts-entry_edit'),
                'accounts-entry_delete'       => $this->input->post('accounts-entry_delete'),
                'accounts-view_entries'       => $this->input->post('accounts-view_entries'),
                'accounts-update_entries_status'       => $this->input->post('accounts-update_entries_status'),

                'accounts-accounting_period_index'       => $this->input->post('accounts-accounting_period_index'),
                'accounts-add_accounting_period'       => $this->input->post('accounts-add_accounting_period'),
                'accounts-edit_accounting_period'       => $this->input->post('accounts-edit_accounting_period'),
                'accounts-delete_accounting_period'       => $this->input->post('accounts-delete_accounting_period'),
                'accounts-fiscal_year_closing'       => $this->input->post('accounts-fiscal_year_closing'),
                'accounts-update_accounting_period_status'       => $this->input->post('accounts-update_accounting_period_status'),

               'accounts-receipts_index'       => $this->input->post('accounts-receipts_index'),
                'accounts-add_receipt'       => $this->input->post('accounts-add_receipt'),
                'accounts-edit_receipt'       => $this->input->post('accounts-edit_receipt'),
                'accounts-view_receipt'       => $this->input->post('accounts-view_receipt'),

                'accounts-catch_receipts_index'       => $this->input->post('accounts-catch_receipts_index'),
                'accounts-add_catch_receipt'       => $this->input->post('accounts-add_catch_receipt'),
                'accounts-edit_catch_receipt'       => $this->input->post('accounts-edit_catch_receipt'),
                'accounts-view_catch_receipt'       => $this->input->post('accounts-view_catch_receipt'),

                'accounts-refunding_bonds_index'       => $this->input->post('accounts-refunding_bonds_index'),
                'accounts-add_refunding_bonds'       => $this->input->post('accounts-add_refunding_bonds'),
                'accounts-edit_refunding_bonds'       => $this->input->post('accounts-edit_refunding_bonds'),
                'accounts-view_refunding_bond'       => $this->input->post('accounts-view_refunding_bond'),

                'accounts-notice_creditor_index'       => $this->input->post('accounts-notice_creditor_index'),
                'accounts-add_notice_creditor'       => $this->input->post('accounts-add_notice_creditor'),
                'accounts-edit_notice_creditor'       => $this->input->post('accounts-edit_notice_creditor'),
                'accounts-view_notice_creditor'       => $this->input->post('accounts-view_notice_creditor'),

                'accounts-payment_types_index'       => $this->input->post('accounts-payment_types_index'),
                 'accounts-add_payment_type'       => $this->input->post('accounts-add_payment_type'),
                 'accounts-edit_payment_type'       => $this->input->post('accounts-edit_payment_type'),
                 'accounts-view_payment_types'       => $this->input->post('accounts-view_payment_types'),

                'accounts-cost_centers'      => $this->input->post('accounts-cost_centers'),
                'accounts-add_cost_center'   => $this->input->post('accounts-add_cost_center'),
                'accounts-edit_cost_center'  => $this->input->post('accounts-edit_cost_center'),
                'accounts-delete_cost_center' => $this->input->post('accounts-delete_cost_center'),
                'accounts-cost_centers_view_popup' => $this->input->post('accounts-cost_centers_view_popup'),

                'accounts-ledgers'      => $this->input->post('accounts-ledgers'),
                'accounts-trialbalance'      => $this->input->post('accounts-trialbalance'),
                'accounts-entriesreport'      => $this->input->post('accounts-entriesreport'),
                'accounts-incomestatement'      => $this->input->post('accounts-incomestatement'),
                'accounts-balancesheet'      => $this->input->post('accounts-balancesheet'),
                'accounts-accountstatement'      => $this->input->post('accounts-accountstatement'),
                'accounts-distributioncostcenters'      => $this->input->post('accounts-distributioncostcenters'),
                'accounts-agedopeninvoices'      => $this->input->post('accounts-agedopeninvoices'),
                'accounts-dashboard_acc'      => $this->input->post('accounts-dashboard_acc'),

                'manufacturing-bom'      => $this->input->post('manufacturing-bom'),
                'manufacturing-add_bom'      => $this->input->post('manufacturing-add_bom'),
                'manufacturing-bom_update'      => $this->input->post('manufacturing-bom_update'),
                'manufacturing-deletebom'      => $this->input->post('manufacturing-deletebom'),
                'manufacturing-bom_details'      => $this->input->post('manufacturing-bom_details'),

                'manufacturing-index'      => $this->input->post('manufacturing-index'),
                'manufacturing-add_manufacturing'      => $this->input->post('manufacturing-add_manufacturing'),
                'manufacturing-edit_manufacturing'      => $this->input->post('manufacturing-edit_manufacturing'),
                'manufacturing-deletemanufacturing'      => $this->input->post('manufacturing-deletemanufacturing'),
                'manufacturing-done_view'      => $this->input->post('manufacturing-done_view'),

                'manufacturing-confirmed'      => $this->input->post('manufacturing-confirmed'),
                'manufacturing-progress'      => $this->input->post('manufacturing-progress'),
                'manufacturing-done'      => $this->input->post('manufacturing-done'),
                'manufacturing-checkstock'      => $this->input->post('manufacturing-checkstock'),
                'manufacturing-update_status'      => $this->input->post('manufacturing-update_status'),
                'manufacturing-progress_details'      => $this->input->post('manufacturing-progress_details'),

            ];

            if (POS) {
                $data['pos-index'] = $this->input->post('pos-index');

            }

            $data['pos-return'] = $this->input->post('pos-return');


			if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                $data['cars-index'] = $this->input->post('cars-index');
                $data['cars-add_vehicle'] = $this->input->post('cars-add_vehicle');
                $data['cars-edit_vehicle'] = $this->input->post('cars-edit_vehicle');
                $data['cars-delete_vehicle'] = $this->input->post('cars-delete_vehicle');
                $data['cars-vehicle_details'] = $this->input->post('cars-vehicle_details');
                $data['cars-modal_view'] = $this->input->post('cars-modal_view');
                $data['cars-xls'] = $this->input->post('cars-xls');



                $data['cars-brands'] = $this->input->post('cars-brands');
                $data['cars-add_brand'] = $this->input->post('cars-add_brand');
                $data['cars-edit_brand'] = $this->input->post('cars-edit_brand');
                $data['cars-delete_brand'] = $this->input->post('cars-delete_brand');
                $data['cars-models'] = $this->input->post('cars-models');
                $data['cars-add_model'] = $this->input->post('cars-add_model');
                $data['cars-edit_model'] = $this->input->post('cars-edit_model');
                $data['cars-delete_model'] = $this->input->post('cars-delete_model');

                $data['cars-categories'] = $this->input->post('cars-categories');
                $data['cars-add_category'] = $this->input->post('cars-add_category');
                $data['cars-edit_category'] = $this->input->post('cars-edit_category');
                $data['cars-delete_category'] = $this->input->post('cars-delete_category');

                $data['cars-features'] = $this->input->post('cars-features');
                $data['cars-add_feature'] = $this->input->post('cars-add_feature');
                $data['cars-edit_feature'] = $this->input->post('cars-edit_feature');
                $data['cars-delete_feature'] = $this->input->post('cars-delete_feature');

                $data['cars-showrooms'] = $this->input->post('cars-showrooms');
                $data['cars-add_showroom'] = $this->input->post('cars-add_showroom');
                $data['cars-edit_showroom'] = $this->input->post('cars-edit_showroom');
                $data['cars-delete_showroom'] = $this->input->post('cars-delete_showroom');
                $data['cars-showroom_xls'] = $this->input->post('cars-showroom_xls');

                $data['cars-services'] = $this->input->post('cars-services');
                $data['cars-add_service'] = $this->input->post('cars-add_service');
                $data['cars-edit_service'] = $this->input->post('cars-edit_service');
                $data['cars-delete_service'] = $this->input->post('cars-delete_service');
                $data['carssales-products_report'] = $this->input->post('carssales-products_report');
                $data['offers-index'] = $this->input->post('offers-index');
                $data['offers-add'] = $this->input->post('offers-add');
                $data['offers-edit'] = $this->input->post('offers-edit');
                $data['offers-delete'] = $this->input->post('offers-delete');
                $data['offers-email'] = $this->input->post('offers-email');
                $data['offers-pdf'] = $this->input->post('offers-pdf');
                $data['offers-xls'] = $this->input->post('offers-xls');


             }


            //$this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePermissions($id, $data)) {
            $this->session->set_flashdata('message', lang('group_permissions_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['id']    = $id;
            $this->data['p']     = $this->settings_model->getGroupPermissions($id);
            $this->data['group'] = $this->settings_model->getGroupByID($id);

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('group_permissions')]];
            $meta = ['page_title' => lang('group_permissions'), 'bc' => $bc];
            $this->page_construct('settings/permissions', $meta, $this->data);
        }
    }

    public function price_groups()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('price_groups')]];
        $meta = ['page_title' => lang('price_groups'), 'bc' => $bc];
        $this->page_construct('settings/price_groups', $meta, $this->data);
    }

    public function product_group_price_actions($group_id)
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'update_price') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->setProductPriceForPriceGroup($id, $group_id, $this->input->post('price' . $id));
                    }
                    $this->session->set_flashdata('message', lang('products_group_price_updated'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteProductGroupPrice($id, $group_id);
                    }
                    $this->session->set_flashdata('message', lang('products_group_price_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('group_name'));
                    $row   = 2;
                    $group = $this->settings_model->getPriceGroupByID($group_id);
                    foreach ($_POST['val'] as $id) {
                        $pgp = $this->settings_model->getProductGroupPriceByPID($id, $group_id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pgp->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pgp->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pgp->price);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $group->name);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'price_groups_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_price_group_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function restore_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $dbName=str_replace("cpesoft_","",$this->db->database);

        $file = './files/backups/'.$dbName.'/' . $zipfile . '.zip';
        $this->sma->unzip($file, './');
        $this->session->set_flashdata('success', lang('files_restored'));
        admin_redirect('system_settings/backups');
        exit();
    }

    public function restore_database($dbfile)
    {

        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $dbName=str_replace("cpesoft_","",$this->db->database);
        //$this->sma->print_arrays($this->db->conn_id);

        $file = file_get_contents('./files/backups/'.$dbName.'/' . $dbfile . '.txt');
        // $this->db->conn_id->multi_query($file);
        mysqli_multi_query($this->db->conn_id, $file);

        $this->db->conn_id->close();
        admin_redirect('logout/db');
    }

    public function skrill()
    {
        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
        if ($this->input->post('active')) {
            $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'required');
        }
        $this->form_validation->set_rules('secret_word', $this->lang->line('secret_word'), 'trim');
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = ['active'         => $this->input->post('active'),
                'secret_word'         => $this->input->post('secret_word'),
                'account_email'       => $this->input->post('account_email'),
                'fixed_charges'       => $this->input->post('fixed_charges'),
                'extra_charges_my'    => $this->input->post('extra_charges_my'),
                'extra_charges_other' => $this->input->post('extra_charges_other'),
            ];
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSkrill($data)) {
            $this->session->set_flashdata('message', $this->lang->line('skrill_setting_updated'));
            admin_redirect('system_settings/skrill');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['skrill'] = $this->settings_model->getSkrillSettings();

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('skrill_settings')]];
            $meta = ['page_title' => lang('skrill_settings'), 'bc' => $bc];
            $this->page_construct('settings/skrill', $meta, $this->data);
        }
    }

    public function tax_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteTaxRate($id);
                    }
                    $this->session->set_flashdata('message', lang('tax_rates_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('type'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $tax = $this->settings_model->getTaxRateByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $tax->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $tax->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $tax->rate);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($tax->type == 1) ? lang('percentage') : lang('fixed'));
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'tax_rates_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function tax_rates()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('tax_rates')]];
        $meta = ['page_title' => lang('tax_rates'), 'bc' => $bc];
        $this->page_construct('settings/tax_rates', $meta, $this->data);
    }

    public function unit_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteUnit($id);
                    }
                    $this->session->set_flashdata('message', lang('units_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('base_unit'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('operator'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('operation_value'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $unit = $this->site->getUnitByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $unit->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $unit->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $unit->base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $unit->operator);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $unit->operation_value);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'units_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function units()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('units')]];
        $meta = ['page_title' => lang('units'), 'bc' => $bc];
        $this->page_construct('settings/units', $meta, $this->data);
    }

    public function update_prices_csv($group_id = null)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('message', lang('disabled_in_demo'));
                admin_redirect('welcome');
            }

            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings/group_product_prices/' . $group_id);
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = ['code', 'price'];

                $final = [];

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if ($product = $this->site->getProductByCode(trim($csv_pr['code']))) {
                        $data[] = [
                            'product_id'     => $product->id,
                            'price'          => $csv_pr['price'],
                            'price_group_id' => $group_id,
                        ];
                    } else {
                        $this->session->set_flashdata('message', lang('check_product_code') . ' (' . $csv_pr['code'] . '). ' . lang('code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('system_settings/group_product_prices/' . $group_id);
                    }
                    $rw++;
                }
            }
        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/group_product_prices/' . $group_id);
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            $this->settings_model->updateGroupPrices($data);
            $this->session->set_flashdata('message', lang('price_updated'));
            admin_redirect('system_settings/group_product_prices/' . $group_id);
        } else {
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['group']    = $this->site->getPriceGroupByID($group_id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/update_price', $this->data);
        }
    }

    public function update_product_group_price($group_id = null)
    {
        if (!$group_id) {
            $this->sma->send_json(['status' => 0]);
        }

        $product_id = $this->input->post('product_id', true);
        $price      = $this->input->post('price', true);
        if (!empty($product_id) && !empty($price)) {
            if ($this->settings_model->setProductPriceForPriceGroup($product_id, $group_id, $price)) {
                $this->sma->send_json(['status' => 1]);
            }
        }

        $this->sma->send_json(['status' => 0]);
    }

    public function updates()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->form_validation->set_rules('purchase_code', lang('purchase_code'), 'required');
        $this->form_validation->set_rules('envato_username', lang('envato_username'), 'required');
        if ($this->form_validation->run() == true) {
            $this->db->update('settings', ['purchase_code' => $this->input->post('purchase_code', true), 'envato_username' => $this->input->post('envato_username', true)], ['setting_id' => 1]);
            admin_redirect('system_settings/updates');
        } else {
            $fields = ['version' => $this->Settings->version, 'code' => $this->Settings->purchase_code, 'username' => $this->Settings->envato_username, 'site' => base_url()];
            $this->load->helper('update');
            $protocol              = is_https() ? 'https://' : 'http://';
            $updates               = get_remote_contents($protocol . 'api.tecdiary.com/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('updates')]];
            $meta                  = ['page_title' => lang('updates'), 'bc' => $bc];
            $this->page_construct('settings/updates', $meta, $this->data);
        }
    }

    public function user_groups()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('auth');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['groups'] = $this->settings_model->getGroups();
        $bc                   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('groups')]];
        $meta                 = ['page_title' => lang('groups'), 'bc' => $bc];
        $this->page_construct('settings/user_groups', $meta, $this->data);
    }

    public function variants()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('variants')]];
        $meta = ['page_title' => lang('variants'), 'bc' => $bc];
        $this->page_construct('settings/variants', $meta, $this->data);
    }

    public function warehouse_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteWarehouse($id);
                    }
                    $this->session->set_flashdata('message', lang('warehouses_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('warehouses'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('city'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $wh = $this->settings_model->getWarehouseByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $wh->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $wh->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $wh->address);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $wh->city);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'warehouses_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_warehouse_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function warehouses()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('warehouses')]];
        $meta = ['page_title' => lang('warehouses'), 'bc' => $bc];
        $this->page_construct('settings/warehouses', $meta, $this->data);
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

    public function groups_settings()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Groups_settings'), 'bc' => $bc];
        $this->page_construct('settings/groups_settings', $meta, $this->data);
    }

    public function companies_settings()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Companies_settings'), 'bc' => $bc];
        $this->page_construct('settings/companies_settings', $meta, $this->data);
    }

    public function branches_settings()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Tabels')]];
        $meta                = ['page_title' => lang('Branches_settings'), 'bc' => $bc];
        $this->page_construct('settings/branches_settings', $meta, $this->data);
    }

    public function getGroupsSettings()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select(" id, group_logo, code, name,  nameEn, phone", false)
            ->from('main_groups')
            ->group_by('id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('system_settings/edit_main_group/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Group') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Group') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_main_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

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
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('system_settings/edit_main_company/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Company') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Company') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_main_company/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function getBranchesSettings()
    {

        $this->load->library('datatables');
        $this->datatables
        ->select("{$this->db->dbprefix('main_branches')}.id as id, {$this->db->dbprefix('main_branches')}.branch_logo as lo,{$this->db->dbprefix('main_groups')}.name as grp_name, {$this->db->dbprefix('main_companies')}.name as co_name, {$this->db->dbprefix('main_branches')}.code, {$this->db->dbprefix('main_branches')}.name,  {$this->db->dbprefix('main_branches')}.nameEn,  {$this->db->dbprefix('main_branches')}.phone", false)
        //->select("{$this->db->dbprefix('main_companies')}.id as id1, {$this->db->dbprefix('main_companies')}.company_logo as lo,1, 1,1,  1", false)
            ->from('main_branches')
            ->join('main_groups', 'main_groups.id=main_branches.grpId', 'left')
            ->join('main_companies', 'main_companies.id=main_branches.coId', 'left')
            //->group_by('main_companies.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('system_settings/edit_main_branch/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Branch') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Branch') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_main_branch/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

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
                    admin_redirect('system_settings/groups_settings');
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

            ];


        } elseif ($this->input->post('add_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/groups_settings');
        }

        if ($this->form_validation->run() == true && $this->site->addMainGroup($data)) {
            $this->session->set_flashdata('message', lang('Main_group_added'));
            admin_redirect('system_settings/groups_settings');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['group_code'] = 'grp_'.$this->site->getGroupsAutoCode()->id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_group', $this->data);
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
                    admin_redirect('system_settings/companies_settings');
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

            ];


        } elseif ($this->input->post('add_company')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/companies_settings');
        }

        if ($this->form_validation->run() == true && $this->site->addMainCompany($data)) {
            $this->session->set_flashdata('message', lang('Main_company_added'));
            admin_redirect('system_settings/companies_settings');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['company_code'] = 'co_'.$this->site->getCompaniesAutoCode()->id;
            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_company', $this->data);
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
                    admin_redirect('system_settings/branches_settings');
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
            'coId'           => $this->input->post('coId') ? $this->input->post('grpId') : 1,
            'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),
                'branch_logo'            => $branch_logo,

            ];


        } elseif ($this->input->post('add_branch')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/branches_settings');
        }

        if ($this->form_validation->run() == true && $this->site->addMainBranch($data)) {
            $this->session->set_flashdata('message', lang('Main_branch_added'));
            admin_redirect('system_settings/branches_settings');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['branch_code'] = 'br_'.$this->site->getBranchesAutoCode()->id;
            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_branch', $this->data);
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
                    admin_redirect('system_settings/groups_settings');
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
            admin_redirect('system_settings/groups_settings');
        }

        if ($this->form_validation->run() == true && $this->site->updateMainGroup($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Main_group_updated'));
            admin_redirect('system_settings/groups_settings');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group']    = $this->site->getMainGroupsByID($id);
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_main_group', $this->data);
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
                    admin_redirect('system_settings/companies_settings');
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
            admin_redirect('system_settings/companies_settings');
        }

        if ($this->form_validation->run() == true && $this->site->updateMainCompany($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Main_company_updated'));
            admin_redirect('system_settings/companies_settings');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['company']    = $this->site->getMainCompaniesByID($id);
            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_main_company', $this->data);
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
            'grpId'           => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
            'coId'           => $this->input->post('coId') ? $this->input->post('grpId') : 1,
            'name'           => $this->input->post('name'),
                'nameEn'           => $this->input->post('nameEn'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),

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
                    admin_redirect('system_settings/branches_settings');
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

            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_branch')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/branches_settings');
        }

        if ($this->form_validation->run() == true && $this->site->updateMainBranch($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            admin_redirect('system_settings/branches_settings');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['branch']    = $this->site->getMainBranchesByID($id);
            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_main_branch', $this->data);
        }
		}
    }

    public function delete_main_group($id = null)
    {


        if ($this->site->deleteMainGroup($id)) {
            $this->session->set_flashdata('message', lang('Delete_Group'));
            admin_redirect('system_settings/groups_settings');

        }
    }

    public function delete_main_company($id = null)
    {


        if ($this->site->deleteMainCompany($id)) {
            $this->session->set_flashdata('message', lang('Delete_Company'));
            admin_redirect('system_settings/edit_main_company');

        }
    }

    public function delete_main_branch($id = null)
    {


        if ($this->site->deleteMainBranch($id)) {
            $this->session->set_flashdata('message', lang('Delete_Branch'));
            admin_redirect('system_settings/branches_settings');

        }
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



    public function getTaxBybrId($brId = null)
    {
    if ($rows = $this->site->getTaxBybrId($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
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

    public function taxs_details($tax_rate_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $tax_rate_id = $this->input->get('id');
        }
        $tax = $this->settings_model->getTaxRateByID($tax_rate_id);

        //$this->sma->print_arrays($_POST);

         $this->form_validation->set_rules('taxs_details', lang('submit'), 'required');

        if ($this->form_validation->run('system_settings/tax_rates') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }

            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);


                if (!$tax_rate_id) {


                    foreach ($this->site->getAllTaxRatesActive() as $taxArr) {
                        $additional_data         = [
                            'tax_rate_id' => $taxArr->id,
                            'grpId'  => $branch_info->grpId,
                            'coId'  => $branch_info->coId,
                            'brId'  => $brId,

                        ];
                        $taxs_details_data[] = $additional_data;
                    }

                }else{


            $additional_data         = [
                'tax_rate_id' => $tax_rate_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $taxs_details_data[] = $additional_data;
             }
            }





        } elseif ($this->input->post('taxs_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }


        if ($this->form_validation->run() == true && $this->settings_model->addTaxRatesDetails($taxs_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            redirect($_SERVER['HTTP_REFERER']);
            //admin_redirect('system_settings/tax_rates');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['tax']  = $tax;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['taxBranches'] = $this->site->getTaxBranches($tax_rate_id ? $tax_rate_id : null);

            $this->load->view($this->theme . 'settings/taxs_details', $this->data);
        }
    }

    public function currencies_details($currency_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $currency_id = $this->input->get('id');
        }

        $currency = $this->settings_model->getCurrencyByID($currency_id);
       // $this->sma->print_arrays($currency);
         $this->form_validation->set_rules('currencies_details', lang('submit'), 'required');

        if ($this->form_validation->run('system_settings/currencies_details') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);

                if (!$currency_id) {


                    foreach ($this->branches_model->getAllCurrencies() as $currArr) {
                        $additional_data         = [
                            'currency_id' => $currArr->id,
                            'grpId'  => $branch_info->grpId,
                            'coId'  => $branch_info->coId,
                            'brId'  => $brId,

                        ];
                        $currencies_details_data[] = $additional_data;
                    }

                }else{



            $additional_data         = [
                'currency_id' => $currency_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $currencies_details_data[] = $additional_data;
             }
            }

            // $this->sma->print_arrays($brId);



        } elseif ($this->input->post('currencies_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCurrenciesDetails($currencies_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            redirect($_SERVER['HTTP_REFERER']);
            //admin_redirect('system_settings/currencies');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['currency']  = $currency;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['currencyBranches'] = $this->site->getCurrencyBranches($currency_id ? $currency_id : null);
 //$this->sma->print_arrays($currency_id);
            $this->load->view($this->theme . 'settings/currencies_details', $this->data);
        }
    }

    public function sms_settings()
    {
        // $this->form_validation->set_rules('auto_send', lang('auto_send'), 'trim|required');
        $this->form_validation->set_rules('gateway', lang('gateway'), 'trim|required');

        if ($this->input->post('gateway') == 'Custom') {
            $this->form_validation->set_rules('Custom_url', lang('url'), 'trim|required');
            $this->form_validation->set_rules('Custom_send_to_name', lang('send_to_name'), 'trim|required');
            $this->form_validation->set_rules('Custom_msg_name', lang('msg_name'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Clickatell') {
            $this->form_validation->set_rules('Clickatell_apiKey', lang('apiKey'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Gupshup') {
            $this->form_validation->set_rules('Gupshup_userid', lang('userid'), 'trim|required');
            $this->form_validation->set_rules('Gupshup_password', lang('password'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Itexmo') {
            $this->form_validation->set_rules('Itexmo_api_code', lang('api_code'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'MVaayoo') {
            $this->form_validation->set_rules('MVaayoo_user', lang('MVaayoo_user'), 'trim|required');
            $this->form_validation->set_rules('MVaayoo_senderID', lang('senderID'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'SmsAchariya') {
            $this->form_validation->set_rules('SmsAchariya_domain', lang('domain'), 'trim|required');
            $this->form_validation->set_rules('SmsAchariya_uid', lang('uid'), 'trim|required');
            $this->form_validation->set_rules('SmsAchariya_pin', lang('pin'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'SmsCountry') {
            $this->form_validation->set_rules('SmsCountry_user', lang('user'), 'trim|required');
            $this->form_validation->set_rules('SmsCountry_passwd', lang('passwd'), 'trim|required');
            $this->form_validation->set_rules('SmsCountry_sid', lang('sid'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'SmsLane') {
            $this->form_validation->set_rules('SmsLane_user', lang('user'), 'trim|required');
            $this->form_validation->set_rules('SmsLane_password', lang('password'), 'trim|required');
            $this->form_validation->set_rules('SmsLane_sid', lang('sid'), 'trim|required');
            $this->form_validation->set_rules('SmsLane_gwid', lang('gwid'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Nexmo') {
            $this->form_validation->set_rules('Nexmo_api_key', lang('api_key'), 'trim|required');
            $this->form_validation->set_rules('Nexmo_api_secret', lang('api_secret'), 'trim|required');
            $this->form_validation->set_rules('Nexmo_from', lang('from'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Twilio') {
            $this->form_validation->set_rules('Twilio_account_sid', lang('account_sid'), 'trim|required');
            $this->form_validation->set_rules('Twilio_auth_token', lang('auth_token'), 'trim|required');
            $this->form_validation->set_rules('Twilio_from', lang('from'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Mocker') {
            $this->form_validation->set_rules('Mocker_sender_id', lang('sender_id'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Infobip') {
            $this->form_validation->set_rules('Infobip_username', lang('username'), 'trim|required');
            $this->form_validation->set_rules('Infobip_password', lang('password'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Bulksms') {
            $this->form_validation->set_rules('Bulksms_eapi_url', lang('eapi_url'), 'trim|required');
            $this->form_validation->set_rules('Bulksms_username', lang('username'), 'trim|required');
            $this->form_validation->set_rules('Bulksms_password', lang('password'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Smsapi') {
            $this->form_validation->set_rules('Smsapi_access_token', lang('access_token'), 'trim|required');
            $this->form_validation->set_rules('Smsapi_from', lang('from'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Oursms') {
            $this->form_validation->set_rules('Oursms_apiKey', lang('apiKey'), 'trim|required');
            $this->form_validation->set_rules('Oursms_src', lang('src'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Mobishatra') {
            $this->form_validation->set_rules('Mobishatra_user', lang('user'), 'trim|required');
            $this->form_validation->set_rules('Mobishatra_passwd', lang('passwd'), 'trim|required');
            $this->form_validation->set_rules('Mobishatra_sid', lang('sid'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {
            $Custom = [
                'url'    => $this->input->post('Custom_url'),
                'params' => [
                    'send_to_name' => $this->input->post('Custom_send_to_name'),
                    'msg_name'     => $this->input->post('Custom_msg_name'),
                    'keys'         => [
                        'param1' => $this->input->post('Custom_param1_key'),
                        'param2' => $this->input->post('Custom_param2_key'),
                        'param3' => $this->input->post('Custom_param3_key'),
                        'param4' => $this->input->post('Custom_param4_key'),
                        'param5' => $this->input->post('Custom_param5_key'),
                    ],
                    'others' => [
                        $this->input->post('Custom_param1_key') => $this->input->post('Custom_param1_value'),
                        $this->input->post('Custom_param2_key') => $this->input->post('Custom_param2_value'),
                        $this->input->post('Custom_param3_key') => $this->input->post('Custom_param3_value'),
                        $this->input->post('Custom_param4_key') => $this->input->post('Custom_param4_value'),
                        $this->input->post('Custom_param5_key') => $this->input->post('Custom_param5_value'),
                    ],
                ],
            ];

            $data = [
                'gateway'    => DEMO ? 'Log' : $this->input->post('gateway'),
                'Custom'     => $Custom,
                'Clickatell' => [
                    'apiKey' => $this->input->post('Clickatell_apiKey'),
                ],
                'Gupshup' => [
                    'userid'   => $this->input->post('Gupshup_userid'),
                    'password' => $this->input->post('Gupshup_password'),
                ],
                'Itexmo'  => ['api_code' => $this->input->post('Itexmo_api_code')],
                'MVaayoo' => [
                    'user'     => $this->input->post('MVaayoo_user'),
                    'senderID' => $this->input->post('MVaayoo_senderID'),
                ],
                'SmsAchariya' => [
                    'domain' => $this->input->post('SmsAchariya_domain'),
                    'uid'    => $this->input->post('SmsAchariya_uid'),
                    'pin'    => $this->input->post('SmsAchariya_pin'),
                ],
                'SmsCountry' => [
                    'user'   => $this->input->post('SmsCountry_user'),
                    'passwd' => $this->input->post('SmsCountry_passwd'),
                    'sid'    => $this->input->post('SmsCountry_sid'),
                ],
                'SmsLane' => [
                    'user'     => $this->input->post('SmsLane_user'),
                    'password' => $this->input->post('SmsLane_password'),
                    'sid'      => $this->input->post('SmsLane_sid'),
                    'gwid'     => $this->input->post('SmsLane_gwid'),
                ],
                'Nexmo' => [
                    'api_key'    => $this->input->post('Nexmo_api_key'),
                    'api_secret' => $this->input->post('Nexmo_api_secret'),
                    'from'       => $this->input->post('Nexmo_from'),
                ],
                'Twilio' => [
                    'account_sid' => $this->input->post('Twilio_account_sid'),
                    'auth_token'  => $this->input->post('Twilio_auth_token'),
                    'from'        => $this->input->post('Twilio_from'),
                ],
                'Mocker'  => ['sender_id' => $this->input->post('Mocker_sender_id')],
                'Infobip' => [
                    'username' => $this->input->post('Infobip_username'),
                    'password' => $this->input->post('Infobip_password'),
                ],
                'Bulksms' => [
                    'eapi_url' => $this->input->post('Bulksms_eapi_url'),
                    'username' => $this->input->post('Bulksms_username'),
                    'password' => $this->input->post('Bulksms_password'),
                ],
                'Smsapi' => [
                    'access_token' => $this->input->post('Smsapi_access_token'),
                    'from'         => $this->input->post('Smsapi_from'),
                ],
                'Oursms' => [
                    'apiKey' => $this->input->post('Oursms_apiKey'),
                    'src'         => $this->input->post('Oursms_src'),
                ],
                'Mobishatra' => [
                    'user'   => $this->input->post('Mobishatra_user'),
                    'passwd' => $this->input->post('Mobishatra_passwd'),
                    'sid'    => $this->input->post('Mobishatra_sid'),
                ],
            ];

            $sms_config = [
                'auto_send' => $this->input->post('auto_send'),
                'config'    => json_encode($data),
            ];
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSmsSettings($sms_config)) {
            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect('system_settings/sms_settings');
        } else {
            $sms_settings               = $this->site->getSmsSettings();
            $sms_settings->config       = json_decode($sms_settings->config);
            $this->data['sms_settings'] = $sms_settings;
            $this->data['error']        = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('sms_settings')]];
            $meta                       = ['page_title' => lang('sms_settings'), 'bc' => $bc];
            $this->page_construct('shop/sms_settings', $meta, $this->data);
        }
    }

    public function send_sms($date = null)
    {
        $this->form_validation->set_rules('mobile', lang('mobile'), 'trim|required');
        $this->form_validation->set_rules('message', lang('message'), 'trim|required');
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        if ($this->form_validation->run() == true) {
            /*$mobile="";
            if($this->input->post('all_customer')){
                $customers=$this->site->getAllCompanies('customer');
                foreach($customers as $customer){
                    if(strlen(trim($customer->phone))>8){
                        $mobile=$mobile."".trim($customer->phone).", ";
                    }
                }
            }
            if($this->input->post('all_supplier')){
                $customers=$this->site->getAllCompanies('supplier');
                foreach($customers as $customer){
                    if(strlen(trim($customer->phone))>8){
                        $mobile=$mobile."".trim($customer->phone).", ";
                    }
                }
            }

            if($mobile==""){*/
                $mobile=$this->input->post('mobile');
            //}

            $this->load->library('sms');
            $res = $this->sms->send($mobile, $this->input->post('message'));
            if (isset($res['error']) && $res['error']) {
                $this->data['error'] = lang('sms_request_failed');
            } else {
                $this->data['message'] = lang('sms_request_sent');
            }
        }

        $this->load->library('sms');
        if($check_balance=$this->sms->check_balance()){
            $this->data['check_balance'] = $check_balance;
        }

        $mobile_cus="";
        $customers=$this->site->getAllCompanies('customer');
        foreach($customers as $customer){
            if(strlen(trim($customer->phone))>8){
                $mobile_cus=$mobile_cus."".trim($customer->phone).", ";
            }
        }

        $mobile_sup="";
        $customers=$this->site->getAllCompanies('supplier');
        foreach($customers as $customer){
            if(strlen(trim($customer->phone))>8){
                $mobile_sup=$mobile_sup."".trim($customer->phone).", ";
            }
        }

        $this->data['mobile_cus'] = $mobile_cus;
        $this->data['mobile_sup'] = $mobile_sup;

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('send_sms')]];
        $meta = ['page_title' => lang('send_sms'), 'bc' => $bc];
        $this->page_construct('shop/send_sms', $meta, $this->data);
    }

    public function sms_log($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $dbName=str_replace("cpesoft_","",$this->db->database);
        if (!file_exists(APPPATH . 'logs/'.$dbName)) {
            mkdir(APPPATH . 'logs/'.$dbName, 0777, true);
        }

        $file = APPPATH . 'logs/'.$dbName.'/' . 'sms-' . $date . '.log';
        if (file_exists($file)) {
            $log   = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
            $lines = explode("\n", $log);
            array_walk($lines, function (&$item) {
                if (strpos($item, 'SMS.ERROR') !== false) {
                    $item = "<span class='text-danger' style='white-space: normal;'>{$item}</span>";
                } else {
                    $item = "<span style='white-space: normal;'>{$item}</span>";
                }
            });
            $content = implode("\n\n", $lines);
        } else {
            $content = "<span class='text-danger'>" . lang('log_x_exists') . '</span>';
        }

        $this->data['log']   = urldecode($content);
        $this->data['date']  = $date;
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('sms_log')]];
        $meta                = ['page_title' => lang('sms_log'), 'bc' => $bc];
        $this->page_construct('shop/log', $meta, $this->data);
    }

    public function whatsapp_settings()
    {
        // $this->form_validation->set_rules('auto_send', lang('auto_send'), 'trim|required');
        $this->form_validation->set_rules('gateway', lang('gateway'), 'trim|required');

        if ($this->input->post('gateway') == '4whatsapp') {
            $this->form_validation->set_rules('4whatsapp_user', lang('user'), 'trim|required');
            $this->form_validation->set_rules('4whatsapp_password', lang('password'), 'trim|required');
            $this->form_validation->set_rules('4whatsapp_code', lang('Company_code'), 'trim|required');
            $this->form_validation->set_rules('4whatsapp_phone', lang('Mobile_No'), 'trim|required');
        }else if ($this->input->post('gateway') == 'arkanprowhatsapp') {
            $this->form_validation->set_rules('sessionId', lang('Session Id'), 'trim|required|no_spaces');
        }else if ($this->input->post('gateway') == 'chatberry') {
            $this->form_validation->set_rules('authorization_token', lang('YOUR_AUTHORIZATION_TOKEN'), 'trim|required|no_spaces');
        }

        if ($this->form_validation->run() == true) {
            $Custom = [
                'url'    => $this->input->post('Custom_url'),
                'params' => [
                    'send_to_name' => $this->input->post('Custom_send_to_name'),
                    'msg_name'     => $this->input->post('Custom_msg_name'),
                    'keys'         => [
                        'param1' => $this->input->post('Custom_param1_key'),
                        'param2' => $this->input->post('Custom_param2_key'),
                        'param3' => $this->input->post('Custom_param3_key'),
                        'param4' => $this->input->post('Custom_param4_key'),
                        'param5' => $this->input->post('Custom_param5_key'),
                    ],
                    'others' => [
                        $this->input->post('Custom_param1_key') => $this->input->post('Custom_param1_value'),
                        $this->input->post('Custom_param2_key') => $this->input->post('Custom_param2_value'),
                        $this->input->post('Custom_param3_key') => $this->input->post('Custom_param3_value'),
                        $this->input->post('Custom_param4_key') => $this->input->post('Custom_param4_value'),
                        $this->input->post('Custom_param5_key') => $this->input->post('Custom_param5_value'),
                    ],
                ],
            ];

            $data = [
                'gateway'    => DEMO ? 'Log' : $this->input->post('gateway'),
                'Custom'     => $Custom,
                '4whatsapp' => [
                    'user'     => $this->input->post('4whatsapp_user'),
                    'password' => $this->input->post('4whatsapp_password'),
                    'code'      => $this->input->post('4whatsapp_code'),
                    'phone'     => $this->input->post('4whatsapp_phone'),
                ],
                'arkanprowhatsapp' => [
                    'sessionId'     => $this->input->post('sessionId'),

                ],
                'chatberry' => [
                    'authorization_token'     => $this->input->post('authorization_token'),

                ],
            ];

            $whatsapp_config = [
                'auto_send' => $this->input->post('auto_send'),
                'sales'          => $this->input->post('sales') ? 1 : 0,
                'quotes'          => $this->input->post('quotes') ? 1 : 0,
                'pos'          => $this->input->post('pos') ? 1 : 0,
                'award_points'          => $this->input->post('award_points') ? 1 : 0,
                'config'    => json_encode($data),
            ];
            //$this->sma->print_arrays($whatsapp_config);
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateWhatsappSettings($whatsapp_config)) {
            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect('system_settings/whatsapp_settings');
        } else {

            $whatsapp_settings               = $this->site->getWhatsappSettings();
            $whatsapp_settings->config       = json_decode($whatsapp_settings->config);
            $this->data['whatsapp_settings'] = $whatsapp_settings;
            if($whatsapp_settings->config->gateway=="arkanprowhatsapp"){
                $url = "https://iparkanwebsitforwhats/sessions/".$whatsapp_settings->config->{$whatsapp_settings->config->gateway}->sessionId;

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                //for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = json_decode(curl_exec($curl));
                curl_close($curl);
                if($resp->message){
                    $this->data['whatsapp_status']='connect';
                }else if($resp->error){
                    $this->data['whatsapp_status']='disconnect';
                }
                //$this->sma->print_arrays($this->data['whatsapp_status']);
            }


            $this->data['error']        = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Whatsapp_settings')]];
            $meta                       = ['page_title' => lang('Whatsapp_settings'), 'bc' => $bc];
            $this->page_construct('shop/whatsapp_settings', $meta, $this->data);
        }
    }

    public function send_whatsapp($date = null)
    {
        $this->form_validation->set_rules('mobile', lang('mobile'), 'trim|required');
        if($this->input->post('message_type')=="text"){
            $this->form_validation->set_rules('message', lang('message'), 'trim|required|min_length[6]');
        } elseif ($this->input->post('message_type') == "pdf") {
            if (empty($_FILES['pdf_file']['name'])) {
                $this->form_validation->set_rules('pdf_file',lang('PDF_File'),'required');
            }
        }

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        if ($this->form_validation->run() == true) {
                $mobile=$this->input->post('mobile');




            $this->load->library('whatsapp');
            if($this->input->post('message_type')=="text"){
                $res = $this->whatsapp->send($mobile, $this->input->post('message'));
            }else{
                if ($_FILES['pdf_file']['size'] > 0) {
                    $this->load->library('upload');
                    $config['upload_path']   = 'files/';
                    $config['allowed_types'] = 'pdf';
                    $config['max_size']      = '2048';
                    $config['overwrite']     = false;
                    $config['encrypt_name']  = true;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('pdf_file')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                    $pdf              = $this->upload->file_name;

                }
                $res = $this->whatsapp->send_pdf($mobile, base_url().'files/'.$pdf);
            }
            if (isset($res['error']) && $res['error']) {
                $this->data['error'] = lang('Whatsapp_request_failed');
            } else {
                $this->data['message'] = lang('Whatsapp_request_sent');
            }
        } elseif ($this->input->post('send_whatsapp')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }


        $mobile_cus="";
        $customers=$this->site->getAllCompanies('customer');
        foreach($customers as $customer){
            if(strlen(trim($customer->phone))>8){
                $mobile_cus=$mobile_cus."".trim($customer->phone).", ";
            }
        }

        $mobile_sup="";
        $customers=$this->site->getAllCompanies('supplier');
        foreach($customers as $customer){
            if(strlen(trim($customer->phone))>8){
                $mobile_sup=$mobile_sup."".trim($customer->phone).", ";
            }
        }

        $this->data['mobile_cus'] = $mobile_cus;
        $this->data['mobile_sup'] = $mobile_sup;

        $whatsapp_settings               = $this->site->getWhatsappSettings();
        $whatsapp_settings->config       = json_decode($whatsapp_settings->config);
        $this->data['whatsapp_settings'] = $whatsapp_settings;


        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('send_whatsapp')]];
        $meta = ['page_title' => lang('send_whatsapp'), 'bc' => $bc];
        $this->page_construct('shop/send_whatsapp', $meta, $this->data);
    }

    public function whatsapp_log($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $dbName=str_replace("cpesoft_","",$this->db->database);
        if (!file_exists(APPPATH . 'logs/'.$dbName)) {
            mkdir(APPPATH . 'logs/'.$dbName, 0777, true);
        }

        $file = APPPATH . 'logs/'.$dbName.'/' . 'whatsapp-' . $date . '.log';
        if (file_exists($file)) {
            $log   = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
            $lines = explode("\n", $log);
            array_walk($lines, function (&$item) {
                if (strpos($item, 'ERROR') !== false) {
                    $item = "<span class='text-danger' style='white-space: normal;'>{$item}</span>";
                } else {
                    $item = "<span style='white-space: normal;'>{$item}</span>";
                }
            });
            $content = implode("\n\n", $lines);
        } else {
            $content = "<span class='text-danger'>" . lang('log_x_exists') . '</span>';
        }

        $this->data['log']   = urldecode($content);
        $this->data['date']  = $date;
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Whatsapp_log')]];
        $meta                = ['page_title' => lang('Whatsapp_log'), 'bc' => $bc];
        $this->page_construct('shop/whatsapp_log', $meta, $this->data);
    }

    public function whatsapp_templates($template = 'sales')
    {
        $this->form_validation->set_rules('whatsapp_body', lang('Whatsapp_message'), 'trim|required');


        if ($this->form_validation->run() == true) {
            $data = $_POST['whatsapp_body'];
            $data = str_replace("<p>", "", ($data) . "");
            $data = str_replace("</p>", "", ($data) . "");
            if ($this->settings_model->updateWhatsappTemplates($template,$data)) {
                $this->session->set_flashdata('message', lang('message_successfully_saved'));
                admin_redirect('system_settings/whatsapp_templates#' . $template);
            } else {
                $this->session->set_flashdata('error', lang('failed_to_save_message'));
                admin_redirect('system_settings/whatsapp_templates#' . $template);
            }
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $whatsapp_templates               = $this->site->getWhatsappTemplates();
            $this->data['sales']     = $whatsapp_templates->sales;
            $this->data['reverse_award_points']            = $whatsapp_templates->reverse_award_points;
            $this->data['award_points'] = str_replace("\\n","\n", ($whatsapp_templates->award_points) . "");
            $this->data['quotes']  = $whatsapp_templates->quotes;
            $bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Whatsapp_templates')]];
            $meta                          = ['page_title' => lang('Whatsapp_templates'), 'bc' => $bc];
            $this->page_construct('shop/whatsapp_templates', $meta, $this->data);
        }
    }

    public function generate_qr()
    {


        $whatsapp_settings               = $this->site->getWhatsappSettings();
        $whatsapp_settings->config       = json_decode($whatsapp_settings->config);
        //$this->sma->print_arrays($whatsapp_settings->config->{$whatsapp_settings->config->gateway}->sessionId);
        if($whatsapp_settings->config->gateway=="4whatsapp"){
            $url = "http://api.4whatsapp.com/api/Agent_Client_/Getbarcode/".$whatsapp_settings->config->{$whatsapp_settings->config->gateway}->code."/".$whatsapp_settings->config->{$whatsapp_settings->config->gateway}->user."/".$whatsapp_settings->config->{$whatsapp_settings->config->gateway}->password."/".$whatsapp_settings->config->{$whatsapp_settings->config->gateway}->phone."/1";

            /* Init cURL resource */
            $ch = curl_init($url);


            $headers = [];
            $headers[] = 'Content-Type:application/json';
            $headers[] = 'Accept:application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


            /* set return type json */
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            /* execute request */
            $result = json_decode(curl_exec($ch));

            /* close cURL resource */
            curl_close($ch);
            if($result->data){
                $this->sma->send_json([ 'msg' => $result->data]);

            }else{
                die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
            }
        }else if($whatsapp_settings->config->gateway=="arkanprowhatsapp"){

            $url = "https://iparkanwebsitforwhats/sessions/add";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
               "Content-Type: application/json",
               "accept: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = '{"sessionId": "'.$whatsapp_settings->config->{$whatsapp_settings->config->gateway}->sessionId.'"}';

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $result = json_decode(curl_exec($curl));
            curl_close($curl);

            if($result->qr){
                $this->sma->send_json([ 'msg' => $result->qr]);

            }else{
                $this->sma->send_json([ 'err' => $result->error]);
            }

        }

    }

    public function sign_document($date = null)
    {
        $this->form_validation->set_rules('signer_name', lang('Signer_name'), 'trim|required');
        $this->form_validation->set_rules('signer_email', lang('email'), 'trim|valid_email|required');
        //$this->form_validation->set_rules('document', lang('document'), 'trim|required');

        $signerName  = trim($_POST['signer_name']);
        $signerEmail = trim($_POST['signer_email']);
        $authenticationType = $_POST['authenticationType'];
        $nationalId='';
        $phoneNumber='';
        if($authenticationType=="7" || $authenticationType=="9"){
            $this->form_validation->set_rules('nationalId', lang('National_ID_or_iqama'), 'trim|required');
            $nationalId = trim($_POST['nationalId']);
        }

        if($authenticationType=="2" || $authenticationType=="10"){
            $this->form_validation->set_rules('phoneNumber', lang('Mobile_No'), 'trim|required');
            $phoneNumber = trim($_POST['phoneNumber']);
        }

        if ($this->form_validation->run() == true) {


            /***********************
 * 1️⃣ إعدادات Sadq
 ***********************/
$authUrl = "https://sandbox-api.sadq-sa.com/Authentication/Authority/Token";
$initiateUrl = "https://sandbox-api.sadq-sa.com/IntegrationService/Document/Initiate-envelope-Base64";
//$inviteUrl = "https://sandbox-api.sadq-sa.com/IntegrationService/Invitation/Send-Invitation";
$inviteUrl = "https://sandbox-api.sadq-sa.com/api/v1/invitations";

$basicUser = "Integrationclient";
$basicPass = "dvncxzvcdsshbbzavrwidsbdvdgfdhsbcvbdgf";


/***********************
 * 2️⃣ استلام البيانات
 ***********************/


if (!isset($_FILES['document']) || $_FILES['document']['error'] !== 0) {
    $this->data['error'] = lang('Error_uploading_file');
}

$fileTmp  = $_FILES['document']['tmp_name'];
$fileName = $_FILES['document']['name'];
$fileBase64 = base64_encode(file_get_contents($fileTmp));

/***********************
 * 3️⃣ الحصول على Access Token
 ***********************/

$curl = curl_init($authUrl);
curl_setopt($curl, CURLOPT_URL, $authUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Content-Type: application/x-www-form-urlencoded",
   "Authorization: Basic SW50ZWdyYXRpb25jbGllbnQ6ZHZuY3h6dmNkc3NoYmJ6YXZyd2lkc2JkdmRnZmRoc2JjdmJkZ2Y=",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data = "grant_type=integration&username=waleed%40companypro.com.sa&password=YdGb%405TdjhA2j&accountId=72BD8837-2315-433F-88A8-08E94E023EB9&accountSecret=Pt9Lp52JlDKNQhI87h3u9ke9wxid55km";

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$authResponse = curl_exec($curl);
curl_close($curl);

$authData = json_decode($authResponse, true);
if (!isset($authData['access_token'])) {
    $this->data['error'] = lang('Authentication_failed');
}
$accessToken = $authData['access_token'];

/***********************
 * 4️⃣ إنشاء Envelope
 ***********************/

$curl = curl_init($initiateUrl);
curl_setopt($curl, CURLOPT_URL, $initiateUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Content-Type: application/json",
   "accept: application/json",
   "Authorization: Bearer " . $accessToken,

);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


$initPayload = '{"referenceNumber": "contract-'.uniqid().'", "File": { "FileName": "'.$fileName.'","File": "'.$fileBase64.'"}}';

curl_setopt($curl, CURLOPT_POSTFIELDS, $initPayload);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$initResponse = curl_exec($curl);
curl_close($ch);

$initData = json_decode($initResponse, true);
if ($initData['errorCode'] !== 0) {
    $this->data['error'] = lang('Document_creation_failed');
}

$documentId = $initData['data']['documentId'];

/***********************
 * 5️⃣ إرسال دعوة التوقيع
 ***********************/

$AvailableTo=date('Y-m-d', strtotime('+30 days'));
$invitePayload = <<<DATA
{
  "documentId": "$documentId",
  "destinations": [
      {
         "destinationName":"$signerName",
         "destinationEmail":"$signerEmail",
         "destinationPhoneNumber":"$phoneNumber",
         "nationalId":"$nationalId",
          "signeOrder": 0,
          "ConsentOnly":false,
         "signatories":[
            {
               "signatureHigh":80,
               "signatureWidth":160,
               "pageNumber":1,
               "text":"",
               "type":"Signature",
               "positionX":70,
               "positionY":500
            }
         ],

         "availableTo":"$AvailableTo",
         "authenticationType":$authenticationType,
         "InvitationLanguage" : 1 ,
         "RedirectUrl":""

      }
  ],
  "invitationMessage": "يرجى التفضل بتوقيع المستند",
  "invitationSubject": "دعوة توقيع"
}
DATA;

$curl = curl_init($inviteUrl);
curl_setopt($curl, CURLOPT_URL, $inviteUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Content-Type: application/json",
   "accept: application/json",
   "Authorization: Bearer " . $accessToken,

);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

curl_setopt($curl, CURLOPT_POSTFIELDS, $invitePayload);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$inviteResponse = curl_exec($curl);

curl_close($ch);

$inviteData = json_decode($inviteResponse, true);
if ($inviteData['errorCode'] !== 0) {
    $this->data['error'] = lang('Sending_invitation_failed');
}

$this->data['message'] = lang('The_file_was_successfully_sent_for_signature');


        }

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Sign_a_document')]];
        $meta = ['page_title' => lang('Sign_a_document'), 'bc' => $bc];
        $this->page_construct('shop/sign_document', $meta, $this->data);
    }

    public function enable_spark_ui()
    {
        if (!$this->loggedIn) {
            $this->sma->send_json([
                'status'  => false,
                'message' => lang('access_denied'),
            ]);
            return;
        }

        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->sma->send_json([
                'status'  => false,
                'message' => lang('access_denied'),
            ]);
            return;
        }

        if (!$this->Owner) {
            $this->sma->send_json([
                'status'  => false,
                'message' => lang('access_denied'),
            ]);
            return;
        }

        if ($this->settings_model->updateSetting(['spark_ui' => 1])) {
            $this->sma->send_json([
                'status'  => true,
                'message' => lang('spark_ui_updated_success'),
            ]);
        } else {
            $this->sma->send_json([
                'status'  => false,
                'message' => lang('spark_ui_update_failed'),
            ]);
        }
    }

}
