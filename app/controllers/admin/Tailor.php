<?php
//ini_set('display_errors', 1);
//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('memory_limit', '-1');

//ini_set('memory_limit','2048M');
class Tailor extends MY_Controller
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
        $this->load->admin_model('tailor_model');
        $this->load->admin_model('products_model');
		$this->load->admin_model('reports_model');
        $this->load->admin_model('settings_model');
		$this->load->admin_model('transfers_model');
		$this->load->admin_model('sales_model');
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


	public function index()
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


        //$this->data['stock']     = $this->db_model->getStockValue();
        $this->data['bs']        = $this->db_model->getBestSeller();
        $lmsdate                 = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate                 = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        $this->data['lmbs']      = $this->db_model->getBestSeller($lmsdate, $lmedate);
        $bc                      = [['link' => '#', 'page' => lang('dashboard')]];
        $meta                    = ['page_title' => lang('dashboard'), 'bc' => $bc];
        $this->page_construct('tailor/dashboard', $meta, $this->data);
    }

    public function add_collar()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Collar_Name', lang('Collar_Name'), 'required');
        $this->form_validation->set_rules('Collar_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Collar_Name'   => $this->input->post('Collar_Name'),


            ];

            if ($_FILES['Collar_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Collar_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Collar_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_collar')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addCollar($data)) {
            $this->session->set_flashdata('message', lang('Collar_added_successfully'));
            admin_redirect('tailor/collar');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_collar', $this->data);
        }
    }

    public function edit_collar($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Collar_Name', lang('Collar_Name'), 'required');
        $this->form_validation->set_rules('Collar_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getCollarsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Collar_Name'   => $this->input->post('Collar_Name'),


            ];


            if ($_FILES['Collar_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Collar_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Collar_Image'] = $photo;
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
                $config = null;
            }
			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_collar')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateCollars($id,$data)) {
            $this->session->set_flashdata('message', lang('Collar_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['collars'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_collar', $this->data);
        }
    }


	public function collar()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Collar')]];
        $meta                   = ['page_title' => lang('Collar'), 'bc' => $bc];
        $this->page_construct('tailor/collar', $meta, $this->data);
    }


    public function getCollar()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Collar_Type_ID as id, Collar_Image as image, Collar_Name as name')
            ->from('TR_Collar')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_collar/$1') . "' class='tip' title='" . lang('Edit_collar') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_collar') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_collar/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_collar($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteCollar($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Collar_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Collar_deleted_successfully'));
            admin_redirect('tailor/collar');
        }

    }




    public function add_cuff()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Cuff_Name', lang('Cuff_Name'), 'required');
        $this->form_validation->set_rules('Cuff_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Cuff_Name'   => $this->input->post('Cuff_Name'),


            ];


            if ($_FILES['Cuff_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Cuff_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Cuff_Image'] = $photo;
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
                $config = null;
            }
			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_cuff')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addCuff($data)) {
            $this->session->set_flashdata('message', lang('Cuff_added_successfully'));
            admin_redirect('tailor/cuff');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_cuff', $this->data);
        }
    }

    public function edit_cuff($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Cuff_Name', lang('Cuff_Name'), 'required');
        $this->form_validation->set_rules('Cuff_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getCuffsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Cuff_Name'   => $this->input->post('Cuff_Name'),


            ];

            if ($_FILES['Cuff_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Cuff_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Cuff_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_cuff')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateCuffs($id,$data)) {
            $this->session->set_flashdata('message', lang('Cuff_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['cuffs'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_cuff', $this->data);
        }
    }


	public function cuff()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Cuff')]];
        $meta                   = ['page_title' => lang('Cuff'), 'bc' => $bc];
        $this->page_construct('tailor/cuff', $meta, $this->data);
    }


    public function getCuff()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Cuff_Type_ID as id, Cuff_Image as image, Cuff_Name as name')
            ->from('TR_Cuff')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_cuff/$1') . "' class='tip' title='" . lang('Edit_cuff') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_cuff') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_cuff/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_cuff($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteCuff($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Cuff_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Cuff_deleted_successfully'));
            admin_redirect('tailor/cuff');
        }

    }















    public function add_filling()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Filling_Name', lang('Filling_Name'), 'required');
        $this->form_validation->set_rules('Filling_Image', lang('image'), 'xss_clean');



        if ($this->form_validation->run() == true) {

            $data     = [
                'Filling_Name'   => $this->input->post('Filling_Name'),


            ];


            if ($_FILES['Filling_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Filling_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Filling_Image'] = $photo;
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
                $config = null;
            }



			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_filling')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addFilling($data)) {
            $this->session->set_flashdata('message', lang('Filling_added_successfully'));
            admin_redirect('tailor/filling');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_filling', $this->data);
        }
    }

    public function edit_filling($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Filling_Name', lang('Filling_Name'), 'required');
        $this->form_validation->set_rules('Filling_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getFillingsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Filling_Name'   => $this->input->post('Filling_Name'),


            ];

            if ($_FILES['Filling_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Filling_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Filling_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_filling')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateFillings($id,$data)) {
            $this->session->set_flashdata('message', lang('Filling_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['fillings'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_filling', $this->data);
        }
    }


	public function filling()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Filling')]];
        $meta                   = ['page_title' => lang('Filling'), 'bc' => $bc];
        $this->page_construct('tailor/filling', $meta, $this->data);
    }


    public function getFilling()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Filling_Type_ID as id, Filling_Image as image, Filling_Name as name')
            ->from('TR_Filling')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_filling/$1') . "' class='tip' title='" . lang('Edit_filling') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_filling') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_filling/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_filling($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteFilling($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Filling_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Filling_deleted_successfully'));
            admin_redirect('tailor/filling');
        }

    }



















    public function add_model()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Model_Name', lang('Model_Name'), 'required');
        $this->form_validation->set_rules('Model_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Model_Name'   => $this->input->post('Model_Name'),


            ];



            if ($_FILES['Model_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Model_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Model_Image'] = $photo;
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
                $config = null;
            }
            //$this->sma->print_arrays($data);


        } elseif ($this->input->post('add_model')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addModel($data)) {
            $this->session->set_flashdata('message', lang('Model_added_successfully'));
            admin_redirect('tailor/model');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_model', $this->data);
        }
    }

    public function edit_model($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Model_Name', lang('Model_Name'), 'required');
        $this->form_validation->set_rules('Model_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getModelsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Model_Name'   => $this->input->post('Model_Name'),


            ];

            if ($_FILES['Model_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Model_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Model_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_model')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateModels($id,$data)) {
            $this->session->set_flashdata('message', lang('Model_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['models'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_model', $this->data);
        }
    }


	public function model()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Model')]];
        $meta                   = ['page_title' => lang('Model'), 'bc' => $bc];
        $this->page_construct('tailor/model', $meta, $this->data);
    }


    public function getModel()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Model_Type_ID as id, Model_Image as image, Model_Name as name')
            ->from('TR_Model')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_model/$1') . "' class='tip' title='" . lang('Edit_model') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_model') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_model/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_model($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteModel($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Model_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Model_deleted_successfully'));
            admin_redirect('tailor/model');
        }

    }



















    public function add_pocket()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Pocket_Name', lang('Pocket_Name'), 'required');
        $this->form_validation->set_rules('Pocket_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Pocket_Name'   => $this->input->post('Pocket_Name'),


            ];

            if ($_FILES['Pocket_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Pocket_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Pocket_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_pocket')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addPocket($data)) {
            $this->session->set_flashdata('message', lang('Pocket_added_successfully'));
            admin_redirect('tailor/pocket');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_pocket', $this->data);
        }
    }

    public function edit_pocket($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Pocket_Name', lang('Pocket_Name'), 'required');
        $this->form_validation->set_rules('Pocket_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getPocketsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Pocket_Name'   => $this->input->post('Pocket_Name'),


            ];

            if ($_FILES['Pocket_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Pocket_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Pocket_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_pocket')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updatePockets($id,$data)) {
            $this->session->set_flashdata('message', lang('Pocket_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['pockets'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_pocket', $this->data);
        }
    }


	public function pocket()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Pocket')]];
        $meta                   = ['page_title' => lang('Pocket'), 'bc' => $bc];
        $this->page_construct('tailor/pocket', $meta, $this->data);
    }


    public function getPocket()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Pocket_Type_ID as id, Pocket_Image as image, Pocket_Name as name')
            ->from('TR_Pocket')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_pocket/$1') . "' class='tip' title='" . lang('Edit_pocket') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_pocket') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_pocket/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_pocket($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deletePocket($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Pocket_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Pocket_deleted_successfully'));
            admin_redirect('tailor/pocket');
        }

    }











    public function add_tailor()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('tailor_name', lang('Tailor_Name'), 'required');



        if ($this->form_validation->run() == true) {

            $data     = [
                'tailor_name'   => $this->input->post('tailor_name'),


            ];



			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_tailor')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addTailor($data)) {
            $this->session->set_flashdata('message', lang('Tailor_added_successfully'));
            admin_redirect('tailor/tailor');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_tailor', $this->data);
        }
    }

    public function edit_tailor($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('tailor_name', lang('Tailor_Name'), 'required');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getTailorsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'tailor_name'   => $this->input->post('tailor_name'),


            ];



			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_tailor')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateTailors($id,$data)) {
            $this->session->set_flashdata('message', lang('Tailor_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['tailors'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_tailor', $this->data);
        }
    }


	public function tailor()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Tailors')]];
        $meta                   = ['page_title' => lang('Tailors'), 'bc' => $bc];
        $this->page_construct('tailor/tailor', $meta, $this->data);
    }


    public function getTailor()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('tailor_id  as id, tailor_name as name')
            ->from('TR_tailor')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_tailor/$1') . "' class='tip' title='" . lang('Edit_tailor') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_tailor') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_tailor/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_tailor($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteTailor($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Tailor_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Tailor_deleted_successfully'));
            admin_redirect('tailor/tailor');
        }

    }









    public function add_zipper()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Zipper_Name', lang('Zipper_Name'), 'required');
        $this->form_validation->set_rules('Zipper_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Zipper_Name'   => $this->input->post('Zipper_Name'),


            ];

            if ($_FILES['Zipper_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Zipper_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Zipper_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_zipper')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addZipper($data)) {
            $this->session->set_flashdata('message', lang('Zipper_added_successfully'));
            admin_redirect('tailor/zipper');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_zipper', $this->data);
        }
    }

    public function edit_zipper($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Zipper_Name', lang('Zipper_Name'), 'required');
        $this->form_validation->set_rules('Zipper_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getZippersByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Zipper_Name'   => $this->input->post('Zipper_Name'),


            ];

            if ($_FILES['Zipper_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Zipper_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Zipper_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_zipper')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateZippers($id,$data)) {
            $this->session->set_flashdata('message', lang('Zipper_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['zippers'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_zipper', $this->data);
        }
    }


	public function zipper()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Zipper')]];
        $meta                   = ['page_title' => lang('Zipper'), 'bc' => $bc];
        $this->page_construct('tailor/zipper', $meta, $this->data);
    }


    public function getZipper()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Zipper_Type_ID as id, Zipper_Image as image, Zipper_Name as name')
            ->from('TR_Zipper')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_zipper/$1') . "' class='tip' title='" . lang('Edit_zipper') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_zipper') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_zipper/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_zipper($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteZipper($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Zipper_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Zipper_deleted_successfully'));
            admin_redirect('tailor/zipper');
        }

    }






    public function add_sleeve()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Sleeve_Name', lang('Sleeve_Name'), 'required');
        $this->form_validation->set_rules('Sleeve_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Sleeve_Name'   => $this->input->post('Sleeve_Name'),


            ];

            if ($_FILES['Sleeve_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Sleeve_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Sleeve_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_sleeve')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addSleeve($data)) {
            $this->session->set_flashdata('message', lang('Sleeve_added_successfully'));
            admin_redirect('tailor/sleeve');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_sleeve', $this->data);
        }
    }

    public function edit_sleeve($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Sleeve_Name', lang('Sleeve_Name'), 'required');
        $this->form_validation->set_rules('Sleeve_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getSleevesByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Sleeve_Name'   => $this->input->post('Sleeve_Name'),


            ];

            if ($_FILES['Sleeve_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Sleeve_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Sleeve_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_sleeve')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateSleeves($id,$data)) {
            $this->session->set_flashdata('message', lang('Sleeve_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['sleeves'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_sleeve', $this->data);
        }
    }


	public function sleeve()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Sleeve')]];
        $meta                   = ['page_title' => lang('Sleeve'), 'bc' => $bc];
        $this->page_construct('tailor/sleeve', $meta, $this->data);
    }


    public function getSleeve()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Sleeve_Type_ID as id, Sleeve_Image as image, Sleeve_Name as name')
            ->from('TR_Sleeve')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_sleeve/$1') . "' class='tip' title='" . lang('Edit_Sleeve') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Sleeve') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_Sleeve/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_sleeve($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteSleeve($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Sleeve_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Sleeve_deleted_successfully'));
            admin_redirect('tailor/sleeve');
        }

    }


    public function add_chest_pocket()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Chest_Pocket_Name', lang('Chest_Pocket_Name'), 'required');
        $this->form_validation->set_rules('Chest_Pocket_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Chest_Pocket_Name'   => $this->input->post('Chest_Pocket_Name'),


            ];

            if ($_FILES['Chest_Pocket_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Chest_Pocket_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Chest_Pocket_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_chest_pocket')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addChest_Pocket($data)) {
            $this->session->set_flashdata('message', lang('Chest_Pocket_added_successfully'));
            admin_redirect('tailor/chest_pocket');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_chest_pocket', $this->data);
        }
    }

    public function edit_chest_pocket($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Chest_Pocket_Name', lang('Chest_Pocket_Name'), 'required');
        $this->form_validation->set_rules('Chest_Pocket_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getChest_PocketsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Chest_Pocket_Name'   => $this->input->post('Chest_Pocket_Name'),


            ];

            if ($_FILES['Chest_Pocket_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Chest_Pocket_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Chest_Pocket_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_chest_pocket')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateChest_Pockets($id,$data)) {
            $this->session->set_flashdata('message', lang('Chest_Pocket_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['chest_pockets'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_chest_pocket', $this->data);
        }
    }


	public function chest_pocket()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Chest_Pocket')]];
        $meta                   = ['page_title' => lang('Chest_Pocket'), 'bc' => $bc];
        $this->page_construct('tailor/chest_pocket', $meta, $this->data);
    }


    public function getChest_Pocket()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Chest_Pocket_Type_ID as id, Chest_Pocket_Image as image, Chest_Pocket_Name as name')
            ->from('TR_Chest_Pocket')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_chest_pocket/$1') . "' class='tip' title='" . lang('Edit_Chest_Pocket') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Chest_Pocket') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_chest_pocket/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_chest_pocket($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteChest_Pocket($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Chest_Pocket_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Chest_Pocket_deleted_successfully'));
            admin_redirect('tailor/chest_pocket');
        }

    }





    public function add_chest_pocket_padding()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Chest_Pocket_Padding_Name', lang('Chest_Pocket_Padding_Name'), 'required');
        $this->form_validation->set_rules('Chest_Pocket_Padding_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Chest_Pocket_Padding_Name'   => $this->input->post('Chest_Pocket_Padding_Name'),


            ];

            if ($_FILES['Chest_Pocket_Padding_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Chest_Pocket_Padding_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Chest_Pocket_Padding_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_chest_pocket_padding')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addChest_Pocket_Padding($data)) {
            $this->session->set_flashdata('message', lang('Chest_Pocket_Padding_added_successfully'));
            admin_redirect('tailor/chest_pocket_padding');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_chest_pocket_padding', $this->data);
        }
    }

    public function edit_chest_pocket_padding($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Chest_Pocket_Padding_Name', lang('Chest_Pocket_Padding_Name'), 'required');
        $this->form_validation->set_rules('Chest_Pocket_Padding_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getChest_Pocket_PaddingsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Chest_Pocket_Padding_Name'   => $this->input->post('Chest_Pocket_Padding_Name'),


            ];

            if ($_FILES['Chest_Pocket_Padding_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Chest_Pocket_Padding_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Chest_Pocket_Padding_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_chest_pocket_padding')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateChest_Pocket_Paddings($id,$data)) {
            $this->session->set_flashdata('message', lang('Chest_Pocket_Padding_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['chest_pocket_paddings'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_chest_pocket_padding', $this->data);
        }
    }


	public function chest_pocket_padding()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Chest_Pocket_Padding')]];
        $meta                   = ['page_title' => lang('Chest_Pocket_Padding'), 'bc' => $bc];
        $this->page_construct('tailor/chest_pocket_padding', $meta, $this->data);
    }


    public function getChest_Pocket_Padding()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Chest_Pocket_Padding_Type_ID as id, Chest_Pocket_Padding_Image as image, Chest_Pocket_Padding_Name as name')
            ->from('TR_Chest_Pocket_Padding')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_chest_pocket_padding/$1') . "' class='tip' title='" . lang('Edit_Chest_Pocket_Padding') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Chest_Pocket_Padding') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_chest_pocket_padding/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_chest_pocket_padding($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteChest_Pocket_Padding($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Chest_Pocket_Padding_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Chest_Pocket_Padding_deleted_successfully'));
            admin_redirect('tailor/chest_pocket_padding');
        }

    }


    public function add_zipper_padding()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Zipper_Padding_Name', lang('Zipper_Padding_Name'), 'required');
        $this->form_validation->set_rules('Zipper_Padding_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Zipper_Padding_Name'   => $this->input->post('Zipper_Padding_Name'),


            ];

            if ($_FILES['Zipper_Padding_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Zipper_Padding_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Zipper_Padding_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_zipper_padding')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addZipper_Padding($data)) {
            $this->session->set_flashdata('message', lang('Zipper_Padding_added_successfully'));
            admin_redirect('tailor/zipper_padding');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_zipper_padding', $this->data);
        }
    }

    public function edit_zipper_padding($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Zipper_Padding_Name', lang('Zipper_Padding_Name'), 'required');
        $this->form_validation->set_rules('Zipper_Padding_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getZipper_PaddingsByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Zipper_Padding_Name'   => $this->input->post('Zipper_Padding_Name'),


            ];

            if ($_FILES['Zipper_Padding_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Zipper_Padding_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Zipper_Padding_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_zipper_padding')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateZipper_Paddings($id,$data)) {
            $this->session->set_flashdata('message', lang('Zipper_Padding_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['zipper_paddings'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_zipper_padding', $this->data);
        }
    }


	public function zipper_padding()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Zipper_Padding')]];
        $meta                   = ['page_title' => lang('Zipper_Padding'), 'bc' => $bc];
        $this->page_construct('tailor/zipper_padding', $meta, $this->data);
    }


    public function getZipper_Padding()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Zipper_Padding_Type_ID as id, Zipper_Padding_Image as image, Zipper_Padding_Name as name')
            ->from('TR_Zipper_Padding')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_zipper_padding/$1') . "' class='tip' title='" . lang('Edit_Zipper_Padding') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Zipper_Padding') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_zipper_padding/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_zipper_padding($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteZipper_Padding($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Zipper_Padding_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Zipper_Padding_deleted_successfully'));
            admin_redirect('tailor/zipper_padding');
        }

    }


    public function add_embroidery()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Embroidery_Name', lang('Embroidery_Name'), 'required');
        $this->form_validation->set_rules('Embroidery_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Embroidery_Name'   => $this->input->post('Embroidery_Name'),


            ];

            if ($_FILES['Embroidery_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Embroidery_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Embroidery_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_embroidery')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addEmbroidery($data)) {
            $this->session->set_flashdata('message', lang('Embroidery_added_successfully'));
            admin_redirect('tailor/embroidery');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_embroidery', $this->data);
        }
    }

    public function edit_embroidery($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Embroidery_Name', lang('Embroidery_Name'), 'required');
        $this->form_validation->set_rules('Embroidery_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getEmbroiderysByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Embroidery_Name'   => $this->input->post('Embroidery_Name'),


            ];

            if ($_FILES['Embroidery_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Embroidery_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Embroidery_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_embroidery')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateEmbroiderys($id,$data)) {
            $this->session->set_flashdata('message', lang('Embroidery_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['embroiderys'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_embroidery', $this->data);
        }
    }


	public function embroidery()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Embroidery')]];
        $meta                   = ['page_title' => lang('Embroidery'), 'bc' => $bc];
        $this->page_construct('tailor/embroidery', $meta, $this->data);
    }


    public function getEmbroidery()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Embroidery_Type_ID as id, Embroidery_Image as image, Embroidery_Name as name')
            ->from('TR_Embroidery')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_embroidery/$1') . "' class='tip' title='" . lang('Edit_Embroidery') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Embroidery') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_embroidery/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_embroidery($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteEmbroidery($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Embroidery_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Embroidery_deleted_successfully'));
            admin_redirect('tailor/embroidery');
        }

    }




    public function add_buttons()
    {
        //$this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('Buttons_Name', lang('Buttons_Name'), 'required');
        $this->form_validation->set_rules('Buttons_Image', lang('image'), 'xss_clean');


        if ($this->form_validation->run() == true) {

            $data     = [
                'Buttons_Name'   => $this->input->post('Buttons_Name'),


            ];

            if ($_FILES['Buttons_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Buttons_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Buttons_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_buttons')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->addButtons($data)) {
            $this->session->set_flashdata('message', lang('Buttons_added_successfully'));
            admin_redirect('tailor/buttons');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/add_buttons', $this->data);
        }
    }

    public function edit_buttons($id)
    {
        //$this->sma->checkPermissions('edit_manufacturing',true);
       // $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('Buttons_Name', lang('Buttons_Name'), 'required');
        $this->form_validation->set_rules('Buttons_Image', lang('image'), 'xss_clean');
        //$this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');
       $man_details = $this->tailor_model->getButtonssByID($id);
        //$this->sma->print_arrays($man_details);




        if ($this->form_validation->run() == true) {





            $data     = [
                'Buttons_Name'   => $this->input->post('Buttons_Name'),


            ];

            if ($_FILES['Buttons_Image']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('Buttons_Image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['Buttons_Image'] = $photo;
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
                $config = null;
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_buttons')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->tailor_model->updateButtonss($id,$data)) {
            $this->session->set_flashdata('message', lang('Buttons_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['buttonss'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'tailor/edit_buttons', $this->data);
        }
    }


	public function buttons()
    {
        //$this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Buttons')]];
        $meta                   = ['page_title' => lang('Buttons'), 'bc' => $bc];
        $this->page_construct('tailor/buttons', $meta, $this->data);
    }


    public function getButtons()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('Buttons_Type_ID as id, Buttons_Image as image, Buttons_Name as name')
            ->from('TR_Buttons')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('tailor/edit_buttons/$1') . "' class='tip' title='" . lang('Edit_Buttons') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Buttons') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tailor/delete_buttons/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_buttons($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->tailor_model->getManufacturingByID($id);



        if ($this->tailor_model->deleteButtons($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Buttons_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Buttons_deleted_successfully'));
            admin_redirect('tailor/buttons');
        }

    }


}