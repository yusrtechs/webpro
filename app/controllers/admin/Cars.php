<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('memory_limit', '-1');

//ini_set('memory_limit','2048M');
class Cars extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->load->library('form_validation');
        $this->load->admin_model('cars_model');

		$this->load->admin_model('reports_model');
        $this->load->admin_model('settings_model');

		$this->load->admin_model('sales_model');
		$this->load->admin_model('accounts_model');
		$this->load->admin_model('companies_model');
        $this->load->admin_model('products_model');

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




    public function brands()
    {
        $this->sma->checkPermissions('brands', true);
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Cars_Showroom')], ['link' => '#', 'page' => lang('brands')]];
        $meta                = ['page_title' => lang('brands'), 'bc' => $bc];
        $this->page_construct('cars/brands', $meta, $this->data);
    }

    public function add_brand()
    {
        $this->sma->checkPermissions('add_brand', true);
        $this->form_validation->set_rules('brand_name', lang('brand_name'), 'trim|required|is_unique[cars_brands.brand_name]');


        if ($this->form_validation->run() == true) {
            $data = [
                'code'        => $this->input->post('code'),
                'brand_name'        => $this->input->post('brand_name'),

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
                $data['brand_logo'] = $photo;
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

        if ($this->form_validation->run() == true && $this->cars_model->addBrand($data)) {
            $this->session->set_flashdata('message', lang('brand_added'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'cars/add_brand', $this->data);
        }
    }



    public function getBrands()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, brand_logo, code, brand_name, description')
            ->from('cars_brands')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('cars/edit_brand/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_brand') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_brand') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('cars/delete_brand/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }


    public function edit_brand($id = null)
    {
        $this->sma->checkPermissions('edit_brand', true);
        $this->form_validation->set_rules('brand_name', lang('brand_name'), 'trim|required');
        $brand_details = $this->cars_model->getBrandByID($id);

        if ($this->form_validation->run() == true) {
            $data = [
                'code'        => $this->input->post('code'),
                'brand_name'        => $this->input->post('brand_name'),
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
                $data['brand_logo'] = $photo;
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

        if ($this->form_validation->run() == true && $this->cars_model->updateBrand($id, $data)) {
            $this->session->set_flashdata('message', lang('brand_updated'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['brand']    = $brand_details;
            $this->load->view($this->theme . 'cars/edit_brand', $this->data);
        }
    }


    public function delete_brand($id = null)
    {
        $this->sma->checkPermissions('delete_brand', true);

     /*   if ($this->settings_model->brandHasProducts($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('brand_has_products')]);
        }
*/
        if ($this->cars_model->deleteBrand($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('brand_deleted')]);
        }
    }



// -----------------------  Models -----------------------

    public function models()
    {
        $this->sma->checkPermissions('models', true);

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Cars_Showroom')], ['link' => '#', 'page' => lang('Models')]];
        $meta                = ['page_title' => lang('models'), 'bc' => $bc];
        $this->page_construct('cars/models', $meta, $this->data);
    }



    public function add_model()
    {
        $this->sma->checkPermissions('add_model', true);
        $this->form_validation->set_rules('model_name', lang('model_name'), 'trim|required|is_unique[cars_models.model_name]');


        if ($this->form_validation->run() == true) {
            $data = [
                'code'        => $this->input->post('code'),
                'brand_id'        => $this->input->post('brand_id'),
                'model_name'        => $this->input->post('model_name'),
                'model_description' => $this->input->post('model_description'),
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
              //  $photo         = $this->upload->file_name;
              // $data['brand_logo'] = $photo;
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
        } elseif ($this->input->post('add_model')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->cars_model->addModel($data)) {
            $this->session->set_flashdata('message', lang('model_added'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['brands'] = $this->cars_model->getAllBrands();
            $this->load->view($this->theme . 'cars/add_model', $this->data);
        }
    }


    public function edit_model($id = null)
    {
        $this->sma->checkPermissions('edit_model', true);

        $this->form_validation->set_rules('model_name', lang('model_name'), 'trim|required');
        $model_details = $this->cars_model->getModelByID($id);

        if ($this->form_validation->run() == true) {
            $data = [
                'code'        => $this->input->post('code'),
                'brand_id'        => $this->input->post('brand_id'),
                'model_name'        => $this->input->post('model_name'),
                'model_description' => $this->input->post('model_description'),

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
              //  $photo         = $this->upload->file_name;
               // $data['brand_logo'] = $photo;
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
        } elseif ($this->input->post('edit_model')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->cars_model->updateModel($id, $data)) {
            $this->session->set_flashdata('message', lang('model_updated'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['model']    = $model_details;
            $this->data['brands'] = $this->cars_model->getAllBrands();
            $this->load->view($this->theme . 'cars/edit_model', $this->data);
        }
    }

    public function getModels()
    {
        $this->load->library('datatables');
        $this->datatables

       ->select("{$this->db->dbprefix('cars_models')}.id as id,{$this->db->dbprefix('cars_models')}.code as m_code, cars_brands.brand_name as brand_name, {$this->db->dbprefix('cars_models')}.model_name, {$this->db->dbprefix('cars_models')}.model_description")

           // ->select('id, code, brand_id, model_name, model_description')
            ->from('cars_models')
            ->join('cars_brands', 'cars_brands.id=cars_models.brand_id', 'left')

            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('cars/edit_model/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_model') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_model') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('cars/delete_model/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }



    public function delete_model($id = null)
    {
        $this->sma->checkPermissions('delete_model', true);

     /*   if ($this->settings_model->brandHasProducts($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('brand_has_products')]);
        }
*/
        if ($this->cars_model->deleteModel($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('model_deleted')]);
        }
    }






//-------------------------------cata



public function categories()
{
    $this->sma->checkPermissions('categories', true);

    $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
    $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Cars_Showroom')], ['link' => '#', 'page' => lang('categories')]];
    $meta                = ['page_title' => lang('categories'), 'bc' => $bc];
    $this->page_construct('cars/categories', $meta, $this->data);
}




public function add_category()
{
    $this->sma->checkPermissions('add_category', true);


   // $this->form_validation->set_rules('categ_name', lang('category_name'), 'trim|required|is_unique[cars_categories.categ_name]');
    $this->form_validation->set_rules('categ_name', lang('category_name'), 'trim|required[cars_categories.categ_name]');

    if ($this->form_validation->run() == true) {
        $data = [
            'code'        => $this->input->post('code'),
            'brand_id'        => $this->input->post('brand_id'),
            'model_id'        => $this->input->post('model_id'),
            'categ_name'        => $this->input->post('categ_name'),
            'description' => $this->input->post('description'),
        ];

       // $this->sma->print_arrays($data);
    } elseif ($this->input->post('add_category')) {
        $this->session->set_flashdata('error', validation_errors());
        //admin_redirect('system_settings/brands');
        redirect($_SERVER['HTTP_REFERER']);
    }

    if ($this->form_validation->run() == true && $this->cars_model->addCategory($data)) {
        $this->session->set_flashdata('message', lang('category_added'));
        //admin_redirect('system_settings/brands');
        redirect($_SERVER['HTTP_REFERER']);
    } else {
        $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['modal_js'] = $this->site->modal_js();
        $this->data['brands'] = $this->cars_model->getAllBrands();
        $this->data['models'] = $this->cars_model->getAllModels();
        $this->load->view($this->theme . 'cars/add_category', $this->data);
    }
}


public function edit_category($id = null)
{
    $this->sma->checkPermissions('edit_category', true);

    $this->form_validation->set_rules('categ_name', lang('category_name'), 'trim|required');
    $category_details = $this->cars_model->getCategoryByID($id);

    if ($this->form_validation->run() == true) {
        $data = [
            'code'        => $this->input->post('code'),
            'brand_id'        => $this->input->post('brand_id'),
            'model_id'        => $this->input->post('model_id'),
            'categ_name'        => $this->input->post('categ_name'),
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
          //  $photo         = $this->upload->file_name;
           // $data['brand_logo'] = $photo;
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
    } elseif ($this->input->post('edit_category')) {
        $this->session->set_flashdata('error', validation_errors());
        //admin_redirect('system_settings/brands');
        redirect($_SERVER['HTTP_REFERER']);
    }

    if ($this->form_validation->run() == true && $this->cars_model->updateCategory($id, $data)) {
        $this->session->set_flashdata('message', lang('category_updated'));
        //admin_redirect('system_settings/brands');
        redirect($_SERVER['HTTP_REFERER']);
    } else {
        $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['modal_js'] = $this->site->modal_js();
        $this->data['category']    = $category_details;
        $this->data['brands'] = $this->cars_model->getAllBrands();
        $this->data['models'] = $this->cars_model->getAllModels();
        $this->load->view($this->theme . 'cars/edit_category', $this->data);
    }
}

public function getCategories()
{
    $this->load->library('datatables');
    $this->datatables

    ->select("{$this->db->dbprefix('cars_categories')}.id as id,{$this->db->dbprefix('cars_categories')}.code as c_code, {$this->db->dbprefix('cars_brands')}.brand_name as brand_name, {$this->db->dbprefix('cars_models')}.model_name as model_name, {$this->db->dbprefix('cars_categories')}.categ_name, {$this->db->dbprefix('cars_categories')}.description")
    //->select("{$this->db->dbprefix('cars_categories')}.id as id,'001','3','2','1','1'")

       // ->select('id, code, brand_id, model_name, model_description')
        ->from('cars_categories')
        ->join('cars_brands', 'cars_brands.id=cars_categories.brand_id', 'left')
        ->join('cars_models', 'cars_models.id=cars_categories.model_id', 'left')

        ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('cars/edit_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_category') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_category') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('cars/delete_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

    echo $this->datatables->generate();
}



public function delete_category($id = null)
{
    $this->sma->checkPermissions('delete_category', true);

 /*   if ($this->settings_model->brandHasProducts($id)) {
        $this->sma->send_json(['error' => 1, 'msg' => lang('brand_has_products')]);
    }
*/
    if ($this->cars_model->deleteCategory($id)) {
        $this->sma->send_json(['error' => 0, 'msg' => lang('category_deleted')]);
    }
}


public function getModelsByBrandId($brand_id = null)
    {
        if ($rows = $this->cars_model->getModelsByBrandId($brand_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
        echo $data;
    }

    public function getCategoryByBrandIdModelId($brand_id = null,$model_id = null)
    {
        if ($rows = $this->cars_model->getCategoryByBrandIdModelId($brand_id,$model_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
        echo $data;
    }




//---------- cars or Vehicles  -----------------------


public function getVehicles($warehouse_id = null)
    {
      //  $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            //$warehouse_id = $user->warehouse_id;
			//$this->sma->print_arrays($warehouse_id);
        }


		$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $chesis_no    = $this->input->get('chesis_no') ? $this->input->get('chesis_no') : null;
        $panel_no    = $this->input->get('panel_no') ? $this->input->get('panel_no') : null;
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $fuel_type = $this->input->get('fuel_type') ? $this->input->get('fuel_type') : null;
        $warehouse_id   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $brand_id   = $this->input->get('brand_id') ? $this->input->get('brand_id') : null;
        $model_id   = $this->input->get('model_id') ? $this->input->get('model_id') : null;
        $category_id   = $this->input->get('category_id') ? $this->input->get('category_id') : null;
        $status   = $this->input->get('status') ? $this->input->get('status') : null;
        $car_status   = $this->input->get('car_status') ? $this->input->get('car_status') : null;
        $payment_status   = $this->input->get('payment_status') ? $this->input->get('payment_status') : null;


		if ($start_date && $end_date) {
			$start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
		   }


        $detail_link       = anchor('admin/cars/vehicle_details/$1', '<i class="fa fa-file-text-o"></i> ' . lang('Details'));

        $payments_link     = anchor('admin/carssales/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link  = anchor('admin/carssales/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $expenses_link     = anchor('admin/cars/expenses/$1', '<i class="fa fa-money"></i> ' . lang('view_expense'), 'data-toggle="modal" data-target="#myModal"');
        $add_expenses_link  = anchor('admin/carspurchases/add_expense/$1/$1', '<i class="fa fa-money"></i> ' . lang('add_expense'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link         = anchor('admin/cars/edit_vehicle/$1', '<i class="fa fa-edit"></i> ' . lang('Edit_Vehicle'), 'class="sledit"');
    /*  $duplicate_link    = anchor('admin/carssales/add?sale_id=$1', '<i class="fa fa-plus-circle"></i> ' . lang('duplicate_sale'));
        $packagink_link    = anchor('admin/carssales/packaging/$1', '<i class="fa fa-archive"></i> ' . lang('packaging'), 'data-toggle="modal" data-target="#myModal"');
        $add_delivery_link = anchor('admin/carssales/add_delivery/$1', '<i class="fa fa-truck"></i> ' . lang('add_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $email_link        = anchor('admin/carssales/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_sale'), 'data-toggle="modal" data-target="#myModal"');

        $pdf_link          = anchor('admin/carssales/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $return_link       = anchor('admin/carssales/return_sale/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_sale'));
       */
        $delete_link       = "<a href='#' class='po' title='<b>" . lang('Delete_Vehicle') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('cars/delete_vehicle/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('Delete_Vehicle') . '</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $edit_link . '</li>

            <li>' . $payments_link . '</li>
            <li>' . $add_payment_link . '</li>
            <li>' . $expenses_link . '</li>
            <li>' . $add_expenses_link . '</li>
            <li>' . $delete_link . '</li>

        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';
		//<li>' . $pdf_link . '</li>

            //(CASE WHEN cgst IS NOT NULL THEN ((grand_total * cgst)-paid) ELSE (grand_total-paid) END)  as


        $this->load->library('datatables');

        //$this->datatables->select("{$this->db->dbprefix('cars_vehicles')}.vehicle_id as id,{$this->db->dbprefix('cars_vehicles')}.image as v_image, DATE_FORMAT({$this->db->dbprefix('cars_vehicles')}.created_date, '%Y-%m-%d %T') as created_date ,{$this->db->dbprefix('cars_vehicles')}.chesis_no  as chesis_no ,{$this->db->dbprefix('cars_showrooms')}.name as showroom_name, {$this->db->dbprefix('cars_brands')}.brand_name as brand_name, {$this->db->dbprefix('cars_models')}.model_name as model_name, {$this->db->dbprefix('cars_categories')}.categ_name as categ_name,{$this->db->dbprefix('cars_vehicles')}.status as status,(CASE WHEN {$this->db->dbprefix('cars_vehicles')}.status!='available' THEN '' ELSE {$this->db->dbprefix('cars_vehicles')}.available_place END) as available_place,{$this->db->dbprefix('cars_vehicles')}.buying_price as buying_price,(CASE WHEN {$this->db->dbprefix('cars_vehicles')}.status='available' THEN 'No_Sold' ELSE DATE_FORMAT({$this->db->dbprefix('cars_vehicles')}.sold_date, '%Y-%m-%d %T') END) as sold_date")
        $this->datatables->select("{$this->db->dbprefix('cars_vehicles')}.vehicle_id as id,{$this->db->dbprefix('cars_vehicles')}.image as v_image, DATE_FORMAT({$this->db->dbprefix('cars_vehicles')}.created_date, '%Y-%m-%d %T') as created_date ,{$this->db->dbprefix('cars_vehicles')}.chesis_no  as chesis_no ,{$this->db->dbprefix('cars_showrooms')}.name as showroom_name, {$this->db->dbprefix('cars_brands')}.brand_name as brand_name, {$this->db->dbprefix('cars_models')}.model_name as model_name, {$this->db->dbprefix('cars_categories')}.categ_name as categ_name,{$this->db->dbprefix('cars_vehicles')}.status as status,{$this->db->dbprefix('cars_vehicles')}.available_place as available_place,{$this->db->dbprefix('cars_vehicles')}.buying_price as buying_price,{$this->db->dbprefix('cars_vehicles')}.sold_date as sold_date")
        ->from('cars_vehicles')
        ->join('cars_showrooms', 'cars_showrooms.id=cars_vehicles.showroom_id', 'left')
        ->join('cars_brands', 'cars_brands.id=cars_vehicles.brand_id', 'left')
        ->join('cars_models', 'cars_models.id=cars_vehicles.model_id', 'left')
        ->join('cars_categories', 'cars_categories.id=cars_vehicles.category_id', 'left');
        //->group_by('cars_vehicles.id')
        //->order_by('cars_vehicles.id', 'asc');
    //$q = $this->db->get('cars_vehicles');

        /*
        if ($warehouse_id) {
            $this->datatables
                ->select("{$this->db->dbprefix('sales')}.id as id, DATE_FORMAT({$this->db->dbprefix('sales')}.date, '%Y-%m-%d %T') as date, id as inv_id, biller, {$this->db->dbprefix('sales')}.customer, sale_status, grand_total, paid, discount_allowance, (grand_total-(paid+discount_allowance))  as balance, payment_status, {$this->db->dbprefix('sales')}.attachment, return_id")
                ->from('sales')
                ->where('warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("{$this->db->dbprefix('sales')}.id as id, DATE_FORMAT({$this->db->dbprefix('sales')}.date, '%Y-%m-%d %T') as date, id as inv_id, biller, {$this->db->dbprefix('sales')}.customer, sale_status, grand_total, paid, discount_allowance, (grand_total-(paid+discount_allowance)) as balance, payment_status, {$this->db->dbprefix('sales')}.attachment, return_id")
                ->from('sales');
        }
        */
        /*if ($this->input->get('shop') == 'yes') {
            $this->datatables->where('shop', 1);
        } elseif ($this->input->get('shop') == 'no') {
            $this->datatables->where('shop !=', 1);
        }
        if ($this->input->get('delivery') == 'no') {
            $this->datatables->join('deliveries', 'deliveries.sale_id=sales.id', 'left')
            ->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
            ->where("({$this->db->dbprefix('deliveries')}.status != 'delivered' OR {$this->db->dbprefix('deliveries')}.status IS NULL)", null);
        }
        if ($this->input->get('attachment') == 'yes') {
            $this->datatables->where('payment_status !=', 'paid')->where('attachment !=', null);
        }


        if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Customer) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }
		*/

	    $this->datatables->where('active', 1); // ->where('sale_status !=', 'returned');

		if ($start_date && $end_date) {
                 $this->db->where($this->db->dbprefix('cars_vehicles') . '.created_date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
           }

           if ($chesis_no) {
            $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.chesis_no', $chesis_no);
            }
            if ($panel_no) {
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.panel_no', $panel_no);
                }
            if ($warehouse_id) {
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.showroom_id', $warehouse_id);
            }
            if ($brand_id) {
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.brand_id', $brand_id);
            }
            if ($model_id) {
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.model_id', $model_id);
            }
            if ($category_id) {
              $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.category_id', $category_id);
            }

        if ($supplier) {
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.owner_id', $supplier);
            }
            if ($fuel_type) {
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.fuel_type', $fuel_type);
            }
              if ($status) {
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.status', $status);
            }

            if ($car_status) {
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.car_status', $car_status);
            }



        $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }


	public function index()
    {
      $this->sma->checkPermissions('index',true);
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
        $this->data['brands'] = $this->cars_model->getAllBrands();
        //$this->data['models'] = $this->cars_model->getAllModels();
        //$this->data['vehicles']   = $this->cars_model->getAllVehicles();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Cars_list')]];
        $meta                   = ['page_title' => lang('Cars_list'), 'bc' => $bc];
        $this->page_construct('cars/index', $meta, $this->data);
    }





    public function add_vehicle()
    {
        $this->sma->checkPermissions('add_vehicle',true);

      //  $this->form_validation->set_rules('model_id', lang('model_name'), 'trim|required|is_unique[cars_brands.brand_name]');

      $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');$this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');

            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['brands'] = $this->cars_model->getAllBrands();
            $this->data['models'] = $this->cars_model->getAllModels();
            $this->data['features_external_equipment'] = $this->cars_model->getFeatureByType('external_equipment');
            $this->data['featured_comfort'] = $this->cars_model->getFeatureByType('comfort');
            $this->data['featured_techniques'] = $this->cars_model->getFeatureByType('techniques');
            $this->data['features_safty'] = $this->cars_model->getFeatureByType('safty');
            $this->data['showroom'] = $this->cars_model->getAllShowrooms();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['zero_tax_rate']  = $this->cars_model->getTaxRateByCode("0")->id;
            $this->page_construct('cars/add_vehicle', $meta, $this->data);

    }


    public function edit_vehicle($vehicle_id)
    {
        $this->sma->checkPermissions('edit_vehicle',true);

      //  $this->form_validation->set_rules('model_id', lang('model_name'), 'trim|required|is_unique[cars_brands.brand_name]');
      $vehicle_details=$this->cars_model->getVehicleByID($vehicle_id);



            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');$this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');

            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['brands'] = $this->cars_model->getAllBrands();
            $this->data['models'] = $this->cars_model->getAllModels();
            $this->data['features_external_equipment'] = $this->cars_model->getFeatureByType('external_equipment');
            $this->data['featured_comfort'] = $this->cars_model->getFeatureByType('comfort');
            $this->data['featured_techniques'] = $this->cars_model->getFeatureByType('techniques');
            $this->data['features_safty'] = $this->cars_model->getFeatureByType('safty');
            $this->data['vehicle'] = $vehicle_details;
            $this->data['showroom'] = $this->cars_model->getAllShowrooms();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->page_construct('cars/edit_vehicle', $meta, $this->data);

    }


    public function vehicle_details($vehicle_id)
    {
        $this->sma->checkPermissions('vehicle_details',true);

      //  $this->form_validation->set_rules('model_id', lang('model_name'), 'trim|required|is_unique[cars_brands.brand_name]');

      $vehicle_details=$this->cars_model->getVehicleByID($vehicle_id);

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');$this->load->helper('string');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['vehicle'] = $vehicle_details;
            $this->data['carowner'] = $this->companies_model->getCompanyByID($vehicle_details->owner_id);
            //$this->sma->print_arrays($this->companies_model->getCompanyByID($vehicle_details->owner_id));

            $this->data['created_by']  = $this->site->getUser($vehicle_details->created_by);
			$this->data['updated_by']  = $vehicle_details->updated_by ? $this->site->getUser($vehicle_details->updated_by) : null;

            $this->data['brands'] = $this->cars_model->getAllBrands();
            $this->data['models'] = $this->cars_model->getAllModels();
            $this->data['features_external_equipment'] = $this->cars_model->getFeatureByType('external_equipment');
            $this->data['featured_comfort'] = $this->cars_model->getFeatureByType('comfort');
            $this->data['featured_techniques'] = $this->cars_model->getFeatureByType('techniques');
            $this->data['features_safty'] = $this->cars_model->getFeatureByType('safty');
            $this->data['showroom'] = $this->cars_model->getAllShowrooms();
            $this->data['tax_rate']         = $vehicle_details->tax_rate ? $this->site->getTaxRateByID($vehicle_details->tax_rate) : null;
            $this->page_construct('cars/vehicle_details', $meta, $this->data);


    }



    public function addVehicleByAjax()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->post('token', true) && $this->input->post('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $showroom_id = $this->input->post('showroom_id', true);
            $brand_id = $this->input->post('brand_id', true);
            $model_id = $this->input->post('model_id', true);
            $category_id = $this->input->post('category_id', true);
            $car_status = $this->input->post('car_status', true);
            $color = $this->input->post('color', true);
            $image = $this->input->post('image', true);
            $mileage = $this->input->post('mileage', true) !=""  ? $this->input->post('mileage', true) : null ;
            $cost = $this->input->post('cost', true) !=""  ? $this->input->post('cost', true) : 0 ;
            $buying_price = $this->input->post('buying_price', true);
            $wd_type = $this->input->post('wd_type', true) !=""  ? $this->input->post('wd_type', true) : null ;
            $registration_year = $this->input->post('registration_year', true) !=""  ? $this->input->post('registration_year', true) : null ;
            $cylinder = $this->input->post('cylinder', true) !=""  ? $this->input->post('cylinder', true) : null ;
            $engine_capacity = $this->input->post('engine_capacity', true) !=""  ? $this->input->post('engine_capacity', true) : null ;
            $fuel_type = $this->input->post('fuel_type', true);
            $specification = $this->input->post('specification', true);
            $shape = $this->input->post('shape', true);
            $gear = $this->input->post('gear', true);
            $doors = $this->input->post('doors', true) !=""  ? $this->input->post('doors', true) : null ;
            $seats = $this->input->post('seats', true) !=""  ? $this->input->post('seats', true) : null ;
            $tank = $this->input->post('tank', true) !=""  ? $this->input->post('tank', true) : null ;
            $chesis_no = $this->input->post('chesis_no', true);
            $panel_no = $this->input->post('panel_no', true);
            $serial_no = $this->input->post('serial_no', true);
            $card_no = $this->input->post('card_no', true);
            $customs_statment = $this->input->post('customs_statment', true);
            $port_entry = $this->input->post('port_entry', true);
            $country_manufacturing = $this->input->post('country_manufacturing', true);
            $owner_id = $this->input->post('owner_id', true);
            $description = $this->input->post('description', true);
            $available_place = $this->input->post('available_place', true);
            $insurance_id = $this->input->post('insurance_id', true) !=""  ? $this->input->post('insurance_id', true) : null ;
            $tax_rate = $this->input->post('tax_rate', true);
            $tax_method = $this->input->post('tax_method', true);
            $date = $this->sma->fld(trim($this->input->post('date'))).' 00:00:00';


          //  shape gear doors seats tank chesis_no panel_no serial_no card_no customs_statment port_entry country_manufacturing owner_id description
			//$updated_date  = date('Y-m-d H:i:s');

            $data     = [

                'date'              => $date,
                'showroom_id'              => $showroom_id,
                'brand_id'              => $brand_id,
                'model_id'              => $model_id,
                'category_id'              => $category_id,
                'name'              => $this->cars_model->getBrandByID($brand_id)->brand_name. ' - ' .$this->cars_model->getModelByID($model_id)->model_name. ' - '  .$this->cars_model->getCategoryByID($category_id)->categ_name,

                'cost'              => $cost,
                'buying_price'              => $buying_price,

                'car_status'              => $car_status,
                'color'              => $color,
                'mileage'              => $mileage,
                'wd_type'              => $wd_type,
                'registration_year'              => $registration_year,
                'cylinder'              => $cylinder,
                'engine_capacity'              => $engine_capacity,
                'fuel_type'              => $fuel_type,
                'specification'              => $specification,
                'shape'              => $shape,
                'gear'              => $gear,
                'doors'              => $doors,
                'seats'              => $seats,
                'tank'              => $tank,
                'chesis_no'              => $chesis_no,
                'panel_no'              => $panel_no,
                'serial_no'              => $serial_no,
                'card_no'              => $card_no,
                'customs_statment'              => $customs_statment,
                'port_entry'              => $port_entry,
                'country_manufacturing'              => $country_manufacturing,
                'owner_id'              => $owner_id,
                'description'              => $description,

                'created_by'          => $this->session->userdata('user_id'),
              //  'updated_date'              => $updated_date,
                'available_place'              => $available_place,
                'insurance_id'              => $insurance_id,
               // 'barcode_symbology'              => $barcode_symbology,
                'tax_rate'              => $tax_rate,
                'tax_method'              => $tax_method,

            ];
           // $this->sma->print_arrays($data);
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
                redirect($_SERVER['HTTP_REFERER']);
            }

            $photo         = $this->upload->file_name;
            $data['image'] = $photo;
            $this->load->helper('file');
            $this->load->library('image_lib');
            $config['image_library']  = 'gd2';
            $config['source_image']   = 'assets/uploads/' . $photo;
            $config['new_image']      = 'assets/uploads/thumbs/' . $photo;
            $config['maintain_ratio'] = true;
            $config['width']          = 300;
            $config['height']         = 80;

            $this->image_lib->clear();
            $this->image_lib->initialize($config);

            if (!$this->image_lib->resize()) {
                echo $this->image_lib->display_errors();
            }
        }
        /*
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
			*/
			//$this->sma->print_arrays($data);
            //json_encode(['msg' => $data]);
            //$this->sma->send_json(['msg' => 'success', 'vehicle_id'=> $vehicle_id, 'res'=> $data]);

        if ($vehicle_id=$this->cars_model->addVehicleMainData($data)) {

                //$this->sma->send_json(['msg' => $_FILES]);
                $this->sma->send_json(['msg' => 'success', 'vehicle_id'=> $vehicle_id]);
            } else {
               // exit(json_encode(['msg' => $data]));
                exit(json_encode(['msg' => lang('failed_to_save')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }

    public function editVehicleByAjax()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->post('token', true) && $this->input->post('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $code = $this->input->post('code', true);
            $vehicle_id = $this->input->post('vehicle_id', true);
            $showroom_id = $this->input->post('showroom_id', true);
            $brand_id = $this->input->post('brand_id', true);
            $model_id = $this->input->post('model_id', true);
            $category_id = $this->input->post('category_id', true);
            $car_status = $this->input->post('car_status', true);
            $color = $this->input->post('color', true);
            $image = $this->input->post('image', true);
            $mileage = $this->input->post('mileage', true);
            $cost = $this->input->post('cost', true);
            $buying_price = $this->input->post('buying_price', true);
            $wd_type = $this->input->post('wd_type', true);
            $registration_year = $this->input->post('registration_year', true);
            $cylinder = $this->input->post('cylinder', true);
            $engine_capacity = $this->input->post('engine_capacity', true);
            $fuel_type = $this->input->post('fuel_type', true);
            $specification = $this->input->post('specification', true);
            $shape = $this->input->post('shape', true);
            $gear = $this->input->post('gear', true);
            $doors = $this->input->post('doors', true);
            $seats = $this->input->post('seats', true);
            $tank = $this->input->post('tank', true);
            $chesis_no = $this->input->post('chesis_no', true);
            $panel_no = $this->input->post('panel_no', true);
            $serial_no = $this->input->post('serial_no', true);
            $card_no = $this->input->post('card_no', true);
            $customs_statment = $this->input->post('customs_statment', true);
            $port_entry = $this->input->post('port_entry', true);
            $country_manufacturing = $this->input->post('country_manufacturing', true);
            $owner_id = $this->input->post('owner_id', true);
            $description = $this->input->post('description', true);

            $available_place = $this->input->post('available_place', true);
            $insurance_id = $this->input->post('insurance_id', true);
          //  $barcode_symbology = $this->input->post('barcode_symbology', true);
            $tax_rate = $this->input->post('tax_rate', true);
            $tax_method = $this->input->post('tax_method', true);

          //  shape gear doors seats tank chesis_no panel_no serial_no card_no customs_statment port_entry country_manufacturing owner_id description
			$updated_date  = date('Y-m-d H:i:s');
            $date = $this->sma->fld(trim($this->input->post('date'))).' 00:00:00';
            $data     = [
                'date'              => $date,
                'code'              => $code,
                'showroom_id'              => $showroom_id,
                'brand_id'              => $brand_id,
                'model_id'              => $model_id,
                'category_id'              => $category_id,
                'name'              => $this->cars_model->getBrandByID($brand_id)->brand_name. ' - ' .$this->cars_model->getModelByID($model_id)->model_name. ' - '  .$this->cars_model->getCategoryByID($category_id)->categ_name,
                'cost'              => $cost,
                'buying_price'              => $buying_price,

                'car_status'              => $car_status,
                'color'              => $color,
                'mileage'              => $mileage,
                'wd_type'              => $wd_type,
                'registration_year'              => $registration_year,
                'cylinder'              => $cylinder,
                'engine_capacity'              => $engine_capacity,
                'fuel_type'              => $fuel_type,
                'specification'              => $specification,
                'shape'              => $shape,
                'gear'              => $gear,
                'doors'              => $doors,
                'seats'              => $seats,
                'tank'              => $tank,
                'chesis_no'              => $chesis_no,
                'panel_no'              => $panel_no,
                'serial_no'              => $serial_no,
                'card_no'              => $card_no,
                'customs_statment'              => $customs_statment,
                'port_entry'              => $port_entry,
                'country_manufacturing'              => $country_manufacturing,
                'owner_id'              => $owner_id,
                'description'              => $description,

                'updated_by'          => $this->session->userdata('user_id'),
                'updated_date'              => $updated_date,
                'available_place'              => $available_place,
                'insurance_id'              => $insurance_id,
              //  'barcode_symbology'              => $barcode_symbology,
                'tax_rate	'              => $tax_rate	,
                'tax_method'              => $tax_method,


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
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
           // $this->sma->print_arrays($data);
			/*
            if ($_FILES['image']['size'] > 0) {
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
            }*/

			//$this->sma->print_arrays($data);
            //json_encode(['msg' => $data]);

        if ($this->cars_model->updateVehicleMainData($vehicle_id,$data)) {

                //$this->sma->send_json(['msg' => $_FILES]);
                $this->sma->send_json(['msg' => 'success', 'vehicle_id'=> $vehicle_id]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_save')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }


    public function delete_vehicle($vehicle_id = null)
    {
        $this->sma->checkPermissions('delete_vehicle',true);

        if ($this->cars_model->deleteVehicle($vehicle_id)) {
            //$this->sma->send_json(['error' => 0, 'msg' => lang('Vehicle_Deleted')]);
            $this->session->set_flashdata('message', lang('Vehicle_Deleted'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);

        }
    }

    public function updateVehicleFeatureByAjax()
    {
        $this->sma->checkPermissions('edit_vehicle',true);

		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->post('token', true) && $this->input->post('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $id = $this->input->post('vehicle_id', true);
            $featureIdsE = $this->input->post('featureIdsE', true);
            $featureIdsC = $this->input->post('featureIdsC', true);
            $featureIdsT = $this->input->post('featureIdsT', true);
            $featureIdsS = $this->input->post('featureIds', true);

            $featuredE_Arr= [];
			foreach($_POST['featureIdsE'] as $key => $acc){
                $featuredE_Arr[] =$acc['value'];
            }
			$featuredC_Arr= [];
			foreach($_POST['featureIdsC'] as $key => $acc){
                $featuredC_Arr[] =$acc['value'];
            }
			$featuredT_Arr= [];
			foreach($_POST['featureIdsT'] as $key => $acc){
                $featuredT_Arr[] =$acc['value'];
            }
			$featured_Arr= [];
			foreach($_POST['featureIds'] as $key => $acc){
                $featured_Arr[] =$acc['value'];
            }
			//$this->sma->print_arrays($data);
            //json_encode(['msg' => $data]);
            $data     = [
                'featured_external_equipment'              => json_encode($featuredE_Arr),
                'featured_comfort'              => json_encode($featuredC_Arr),
                'featured_techniques'              => json_encode($featuredT_Arr),
                'featured'              => json_encode($featured_Arr),
            //    'gear'              => '123',

            ];

        if ($this->cars_model->updateVehicleFeatureByAjax($id,$data)) {

                //$this->sma->send_json(['msg' => $_FILES]);
                $this->sma->send_json(['msg' => 'success','err'=>$costCenterCreditId]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_save'),'err'=>$costCenterCreditId]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }

    // ----------------------------------------   Feature  -------------------------
    public function features()
    {
        $this->sma->checkPermissions('features',true);

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Cars_Showroom')], ['link' => '#', 'page' => lang('Features')]];
        $meta                = ['page_title' => lang('Features'), 'bc' => $bc];
        $this->page_construct('cars/features', $meta, $this->data);
    }


    public function add_feature()
    {
        $this->sma->checkPermissions('add_feature',true);

        $this->form_validation->set_rules('feature_name', lang('Feature_Name'), 'trim|required|is_unique[cars_features.feature_name]');


        if ($this->form_validation->run() == true) {
            $data = [
                'type'        => $this->input->post('type'),
                'feature_name'        => $this->input->post('feature_name'),

            ];
            // $this->sma->print_arrays($data);
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
                //$photo         = $this->upload->file_name;
                //$data['brand_logo'] = $photo;
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
        } elseif ($this->input->post('add_feature')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->cars_model->addFeature($data)) {
            $this->session->set_flashdata('message', lang('Feature_Added'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'cars/add_feature', $this->data);
        }
    }





    public function getFeatures()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, type, feature_name')
            ->from('cars_features')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('cars/edit_feature/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_Feature') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Feature') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('cars/delete_feature/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }


    public function edit_feature($id = null)

    {
        $this->sma->checkPermissions('edit_feature',true);

        $this->form_validation->set_rules('feature_name', lang('Feature_Name'), 'trim|required');
        $feature_details = $this->cars_model->getfeatureByID($id);

        if ($this->form_validation->run() == true) {
            $data = [
                'type'        => $this->input->post('type'),
                'feature_name'        => $this->input->post('feature_name'),

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
                $data['feature_logo'] = $photo;
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
        } elseif ($this->input->post('edit_feature')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/features');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->cars_model->updateFeature($id, $data)) {
            $this->session->set_flashdata('message', lang('Feature_Updated'));
            //admin_redirect('system_settings/features');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['feature']    = $feature_details;
            $this->load->view($this->theme . 'cars/edit_feature', $this->data);
        }
    }


    public function delete_feature($id = null)
    {
        $this->sma->checkPermissions('delete_feature',true);

        if ($this->cars_model->deleteFeature($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('Feature_Deleted')]);
        }
    }


















    public function addcarowner()
    {
        //$this->sma->checkPermissions(false, true);

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
                'group_id'            => '5',
                'group_name'          => 'carowner',
                'customer_group_id'   => $this->input->post('customer_group'),
                'customer_group_name' => $cg->name,
                'price_group_id'      => $this->input->post('price_group') ? $this->input->post('price_group') : null,
                'price_group_name'    => $this->input->post('price_group') ? $pg->name : null,
                'company'             => $this->input->post('company'),
                'companyEn'             => $this->input->post('companyEn'),
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

            ];

		} elseif ($this->input->post('add_carowner')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('cars');
        }

        if ($this->form_validation->run() == true && $cid = $this->companies_model->addCompany($data)) {
            $this->session->set_flashdata('message', lang('car_owner_added'));
            $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0] . '?owner_id=' . $cid);
        } else {
            $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']        = $this->site->modal_js();
            $this->data['customer_groups'] = $this->companies_model->getAllCustomerGroups();
            $this->data['price_groups']    = $this->companies_model->getAllPriceGroups();
            $this->load->view($this->theme . 'cars/add_carowner', $this->data);
        }
    }

    public function getCarOwner($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->companies_model->getCompanyByID($id);
        $this->sma->send_json([['id' => $row->id, 'text' => ($row->company && $row->company != '-' ? $row->company : $row->name)]]);
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
        $result = $this->cars_model->getCarOwnerSuggestions($term, $limit);
        if ($a) {
            $this->sma->send_json($result);
        }
        $rows['results'] = $result;
        $this->sma->send_json($rows);
    }
    public function view_carowner($id = null)
    {
       // $this->sma->checkPermissions('index', true);
        $this->data['error']    = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['customer'] = $this->companies_model->getCompanyByID($id);
        $this->load->view($this->theme . 'cars/view_carowner', $this->data);
    }










		/* --------------------------add showroom----------------------------- */


        public function showrooms()
        {

            $this->sma->checkPermissions('showrooms', true);
            /*if(!$this->site->IsPermissions('showroom-showrooms')){
                 $this->session->set_flashdata('error', lang('access_denied'));
                //if ($js) {
                    die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                //}

            }else{
                */

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Showroom')], ['link' => '#', 'page' => lang('Showrooms')]];
            $meta = ['page_title' => lang('Showrooms'), 'bc' => $bc];
            $this->page_construct('cars/showrooms', $meta, $this->data);
            //}
        }
        public function getShowrooms()
        {
            $this->load->library('datatables');
            $this->datatables
                ->select("{$this->db->dbprefix('cars_showrooms')}.id as id, map, showroom_logo, code, {$this->db->dbprefix('cars_showrooms')}.name as name,{$this->db->dbprefix('cars_showrooms')}.branch_name as branch_name, {$this->db->dbprefix('cars_showrooms')}.vat_reg_no as vat_reg_no, phone, address")
                ->from('cars_showrooms')
                ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('cars/edit_showroom/$1') . "' class='tip' title='" . lang('Edit_Showroom') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Showroom') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('cars/delete_showroom/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

            echo $this->datatables->generate();
        }

        public function add_showroom()
        {
            $this->sma->checkPermissions('add_showroom', true);


            $this->load->helper('security');
            $this->form_validation->set_rules('code', lang('code'), 'trim|is_unique[cars_showrooms.code]|required');
            $this->form_validation->set_rules('name', lang('name'), 'required');
            $this->form_validation->set_rules('address', lang('address'), 'required');
            $this->form_validation->set_rules('street_name', lang('Street_name'), 'required');
            $this->form_validation->set_rules('district_name', lang('District_name'), 'required');
            $this->form_validation->set_rules('city', lang('city'), 'required');
            $this->form_validation->set_rules('state', lang('state'), 'required');
            $this->form_validation->set_rules('country', lang('country'), 'required');
            $this->form_validation->set_rules('building_no', lang('Building_No'), 'required|numeric');
            $this->form_validation->set_rules('postal_code', lang('postal_code'), 'required|numeric');
            $this->form_validation->set_rules('additional_no', lang('Additional_No'), 'required|numeric');
            $this->form_validation->set_rules('vat_reg_no', lang('VAT_Regesteration_Number'), 'required|numeric');
            $this->form_validation->set_rules('cr_no', lang('CR_Number'), 'required|numeric');
            $this->form_validation->set_rules('showroom_logo', lang('Showroom_logo'), 'xss_clean');
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
                        admin_redirect('cars/showrooms');
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
                if ($_FILES['showroom_logo']['size'] > 0) {
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

                    if (!$this->upload->do_upload('showroom_logo')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('message', $error);
                        admin_redirect('cars/showrooms');
                    }

                    $showroom_logo = $this->upload->file_name;

                    $this->load->helper('file');
                    $this->load->library('image_lib');
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = 'assets/uploads/' . $showroom_logo;
                    $config['new_image']      = 'assets/uploads/thumbs/' . $showroom_logo;
                    $config['maintain_ratio'] = true;
                    $config['width']          = 300;
                    $config['height']         = 80;

                    $this->image_lib->clear();
                    $this->image_lib->initialize($config);

                    if (!$this->image_lib->resize()) {
                        echo $this->image_lib->display_errors();
                    }
                } else {
                    $showroom_logo = null;
                }
                $data = ['code'      => $this->input->post('code'),
                    'name'           => $this->input->post('name'),
                    'branch_name'           => $this->input->post('branch_name'),
                    'cr_no'           => $this->input->post('cr_no'),
                    'vat_reg_no'           => $this->input->post('vat_reg_no'),
                    'phone'          => $this->input->post('phone'),
                    'email'          => $this->input->post('email'),
                    'address'        => $this->input->post('address'),
                    'addressEn'        => $this->input->post('addressEn'),

                    'building_no' => $this->input->post('building_no'),
                    'street_name' => $this->input->post('street_name'),
                    'district_name' => $this->input->post('district_name'),
                    'city' => $this->input->post('city'),
                    'postal_code' => $this->input->post('postal_code'),
                    'additional_no' => $this->input->post('additional_no'),
                    'state' => $this->input->post('state'),
                    'country' => $this->input->post('country'),
                    'map'            => $map,
                    'showroom_logo'            => $showroom_logo,
                ];

            } elseif ($this->input->post('add_showroom')) {
                $this->session->set_flashdata('error', validation_errors());
                admin_redirect('cars/showrooms');
            }

            if ($this->form_validation->run() == true && $this->cars_model->addShowroom($data)) {
                $this->session->set_flashdata('message', lang('Showroom_Added'));
           //     admin_redirect('cars/showrooms');
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['showroom_code'] = 'sh_'.$this->cars_model->getShowroomAutoCode()->id;
                $this->data['modal_js']     = $this->site->modal_js();
                $this->load->view($this->theme . 'cars/add_showroom', $this->data);
            }
            }


        public function edit_showroom($id = null)
        {
            $this->sma->checkPermissions('edit_showroom', true);

              $this->load->helper('security');
            $this->form_validation->set_rules('code', lang('code'), 'trim|required');

            $wh_details = $this->cars_model->getShowroomByID($id);
            if ($this->input->post('code') != $wh_details->code) {
                $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[cars_showrooms.code]');
            }
            $this->form_validation->set_rules('address', lang('address'), 'required');
            $this->form_validation->set_rules('street_name', lang('Street_name'), 'required');
            $this->form_validation->set_rules('district_name', lang('District_name'), 'required');
            $this->form_validation->set_rules('city', lang('city'), 'required');
            $this->form_validation->set_rules('state', lang('state'), 'required');
            $this->form_validation->set_rules('country', lang('country'), 'required');
            $this->form_validation->set_rules('building_no', lang('Building_No'), 'required|numeric');
            $this->form_validation->set_rules('postal_code', lang('postal_code'), 'required|numeric');
            $this->form_validation->set_rules('additional_no', lang('Additional_No'), 'required|numeric');
            $this->form_validation->set_rules('vat_reg_no', lang('VAT_Regesteration_Number'), 'required|numeric');
            $this->form_validation->set_rules('cr_no', lang('CR_Number'), 'required|numeric');
            $this->form_validation->set_rules('showroom_logo', lang('showroom_logo'), 'xss_clean');
            $this->form_validation->set_rules('map', lang('map_image'), 'xss_clean');

            if ($this->form_validation->run() == true) {
                $data = ['code'      => $this->input->post('code'),
                    'name'           => $this->input->post('name'),
                    'branch_name'           => $this->input->post('branch_name'),
                    'vat_reg_no'           => $this->input->post('vat_reg_no'),
                    'cr_no'           => $this->input->post('cr_no'),
                    'phone'          => $this->input->post('phone'),
                    'email'          => $this->input->post('email'),
                    'address'        => $this->input->post('address'),
                    'addressEn'        => $this->input->post('addressEn'),

                    'building_no' => $this->input->post('building_no'),
                    'street_name' => $this->input->post('street_name'),
                    'district_name' => $this->input->post('district_name'),
                    'city' => $this->input->post('city'),
                    'postal_code' => $this->input->post('postal_code'),
                    'additional_no' => $this->input->post('additional_no'),
                    'state' => $this->input->post('state'),
                    'country' => $this->input->post('country'),
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
                        admin_redirect('cars/showrooms');
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
                if ($_FILES['showroom_logo']['size'] > 0) {
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

                    if (!$this->upload->do_upload('showroom_logo')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('message', $error);
                        admin_redirect('cars/showrooms');
                    }

                    $data['showroom_logo'] = $this->upload->file_name;

                    $this->load->helper('file');
                    $this->load->library('image_lib');
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = 'assets/uploads/' . $data['showroom_logo'];
                    $config['new_image']      = 'assets/uploads/thumbs/' . $data['showroom_logo'];
                    $config['maintain_ratio'] = true;
                    $config['width']          = 300;
                    $config['height']         = 80;

                    $this->image_lib->clear();
                    $this->image_lib->initialize($config);

                    if (!$this->image_lib->resize()) {
                        echo $this->image_lib->display_errors();
                    }
                }
            } elseif ($this->input->post('edit_showroom')) {
                $this->session->set_flashdata('error', validation_errors());
                admin_redirect('cars/showrooms');
            }

            if ($this->form_validation->run() == true && $this->cars_model->updateShowroom($id, $data)) { //check to see if we are updateing the customer
                $this->session->set_flashdata('message', lang('Showroom_Updated'));
                admin_redirect('cars/showrooms');
            } else {
                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

                $this->data['showroom']    = $this->cars_model->getShowroomByID($id);

                $this->data['id']           = $id;
                $this->data['modal_js']     = $this->site->modal_js();
                $this->load->view($this->theme . 'cars/edit_showroom', $this->data);

            }
        }
        public function sale_actions()
        {
            if (!$this->Owner && !$this->GP['bulk_actions']) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                redirect($_SERVER['HTTP_REFERER']);
            }


        if ($start_date && $end_date) {
			//$start_date = $this->sma->fld($start_date).' 00:00:00';
			//$end_date = $this->sma->fld($end_date).' 23:59:59';
			$start_date = $this->sma->fld($start_date);
			$end_date = $this->sma->fld($end_date);
		   }
         /*   if ($start_date && $end_date) {
                $start_date = $this->sma->fld($start_date).' 00:00:00';
                $end_date = $this->sma->fld($end_date).' 23:59:59';
               }
           */
               $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

            if ($this->form_validation->run() == true) {
                if (!empty($_POST['val'])) {
                    if ($this->input->post('form_action') == 'delete') {
                        $this->sma->checkPermissions('delete');
                        foreach ($_POST['val'] as $vehicle_id) {
                            $this->cars_model->deleteVehicle($vehicle_id);
                        }
                        $this->session->set_flashdata('message', lang('vehicle_deleted'));
                        redirect($_SERVER['HTTP_REFERER']);
                    } elseif ($this->input->post('form_action') == 'combine') {
                        $html = $this->combine_pdf($_POST['val']);
                    } elseif ($this->input->post('form_action') == 'export_excel'|| $xls || $pdf) {
                        $this->sma->checkPermissions('xls', true);
                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                         if ($this->Settings->user_language == 'arabic'){
                            $this->excel->getActiveSheet()->setRightToLeft(true);
                            }
                            $this->excel->getActiveSheet()->setTitle(lang('Cars_list'));
                            $row = 1;
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);

                            $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Cars_list'));
                            $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $styleArray = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 20,
                                    'name'  => 'Cairo-Bold'
                                                )

                                        );
                               $fillColor = array(

                                         'fill' => array(
                                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'f9f9f9')
                                            )
                                          );

                                  $Font11Bold = array(
                                    'font' => array(
                                        'bold' => true,
                                        'size' => 11,
                                        'name'  => 'Cairo-Bold'
                                                    )

                                  );
                    $styleArrayFillGreen = array(

                     'fill' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '5cb85c')
                        ),
                        'font' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ffffff')
                        )
                     );

                  $styleArrayFillRed = array(

                     'fill' => array(
                       'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'd9534f')
                        ),
                        'font' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ffffff')
                        )
                  );
                        $styleArrayFillYellow = array(

                         'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'f0ad4e')
                            ),
                        'font' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ffffff')
                        )

                      );

                       $styleArrayFillBlu = array(

                         'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '5bc0de')
                            ),
                        'font' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ffffff')
                        )

                      );
                              $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                             $row++;

                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                            $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                            $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                            $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                            $row++;
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                            $this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') .' '. lang('Cars_list') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                            $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $this->excel->getActiveSheet()->getStyle('A3')->applyFromArray($Font11Bold);

                             $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
                                    $row++;

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Added_on'));
                        $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('Chesis_no'));
                        $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('Plate_Number'));
                        $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('Showroom'));
                        $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('Car_Type'));
                        $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('Status'));
                        $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('Car_Place'));
                        $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('price'));
                        $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('cost'));
                        $this->excel->getActiveSheet()->SetCellValue('J'.$row, lang('Sold_on'));


                         $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($fillColor);
                            $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                             $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($Font11Bold);
                            $row++;

                        foreach ($_POST['val'] as $vehicle_id) {
                            $sale = $this->cars_model->getVehicleByID($vehicle_id);
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($sale->created_date));
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sale->chesis_no);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sale->panel_no);

                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->cars_model->getShowroomByID($sale->showroom_id)->name);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sale->name);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sale->status);
                            if($sale->status=='available'){
                                $this->excel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleArrayFillGreen);
                                }else if($sale->status=='sold'){
                                $this->excel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleArrayFillYellow);
                                }else if($sale->status=='Reserved'){
                                    $this->excel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleArrayFillYellow);
                                    }
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale->available_place);

                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $sale->buying_price);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $sale->cost);
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $sale->sold_date);


                            $row++;
                        }


                         for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                    $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                            }


                       // $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                       // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $filename = lang('Cars_list').' _' . date('Y_m_d_H_i_s');
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                } else {
                    $this->session->set_flashdata('error', lang('No_car_selected'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        public function showroom_actions()
        {
            $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

            if ($this->form_validation->run() == true) {
                if (!empty($_POST['val'])) {
                    if ($this->input->post('form_action') == 'delete') {
                        foreach ($_POST['val'] as $id) {
                            $this->cars_model->deleteShowroom($id);
                        }
                        $this->session->set_flashdata('message', lang('Showrooms_Deleted'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }

                    if ($this->input->post('form_action') == 'export_excel') {
                        $this->sma->checkPermissions('showroom_xls', true);

                            $this->load->library('excel');

                        $this->excel->setActiveSheetIndex(0);
                        if ($this->Settings->user_language == 'arabic'){
                            $this->excel->getActiveSheet()->setRightToLeft(true);
                            }

                        $this->excel->getActiveSheet()->setTitle(lang('Showrooms'));

                        $row = 1;
                        $styleArray = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 20,
                                    'name'  => 'Cairo-Bold'
                                                )

                                        );
                               $fillColor = array(

                                         'fill' => array(
                                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'f9f9f9')
                                            )
                                          );

                                  $Font11Bold = array(
                                    'font' => array(
                                        'bold' => true,
                                        'size' => 11,
                                        'name'  => 'Cairo-Bold'
                                                    )

                                  );
                    $styleArrayFillGreen = array(

                     'fill' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '5cb85c')
                        ),
                        'font' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ffffff')
                        )
                     );

                  $styleArrayFillRed = array(

                     'fill' => array(
                       'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'd9534f')
                        ),
                        'font' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ffffff')
                        )
                  );
                        $styleArrayFillYellow = array(

                         'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'f0ad4e')
                            ),
                        'font' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ffffff')
                        )

                      );

                       $styleArrayFillBlu = array(

                         'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '5bc0de')
                            ),
                        'font' => array(
                         'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ffffff')
                        )

                      );
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':H'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Showrooms'));
                         $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                             $row++;

                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':H'.$row);
                            $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                            $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                            $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                            $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                            $row++;

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('code'));
                        $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('name'));
                        $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('Branch_name'));
                        $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('VAT_Regesteration_Number'));
                      //  $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('price_group'));
                        $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('phone'));
                        $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('email'));
                        $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('address'));
                       $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->applyFromArray($fillColor);
                            $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                             $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->applyFromArray($Font11Bold);
                            $row++;


                        foreach ($_POST['val'] as $id) {
                            $wh = $this->cars_model->getShowroomByID($id);
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $wh->code);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $wh->name);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $wh->branch_name);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $wh->vat_reg_no);
                           // $this->excel->getActiveSheet()->SetCellValue('E' . $row, $wh->price_group_id);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $wh->phone);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $wh->email);
                              $address= str_replace("<p>","",$wh->address."");
                            $address= str_replace("</p>","",($address)."");
                              $address= str_replace("<br>","/n",$address."");
                            $address= str_replace("</br>","",($address)."");
                              $this->excel->getActiveSheet()->SetCellValue('H' . $row,$address);
                             //$wh = str_replace('<p>', '', $cell->getFormattedValue($wh->address));



                            $row++;
                        }
                         for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                    $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                            }

                        //$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                       // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                       // $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                       // $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $filename = lang('Showrooms').' _ ' . date('Y_m_d_H_i_s');
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                } else {
                    $this->session->set_flashdata('error', lang('No_Showroom_Selected'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        public function delete_showroom($id = null)
        {

            $this->sma->checkPermissions('delete_showroom', true);

            if(!$this->site->IsPermissions('cars-delete_showroom')){
                 //$this->session->set_flashdata('error', lang('access_denied'));
                 $this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
                //if ($js) {
                   // die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                //}

            }else{
            if ($this->cars_model->deleteShowroom($id)) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Showroom_Deleted')]);
            }
            }
        }

		/* --------------------------add Services----------------------------- */


        public function services()
        {
            $this->sma->checkPermissions('services', true);


            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Services')], ['link' => '#', 'page' => lang('Services')]];
            $meta = ['page_title' => lang('Services'), 'bc' => $bc];
            $this->page_construct('cars/services', $meta, $this->data);

        }
        public function getServices()
        {
            $this->load->library('datatables');
            $this->datatables
                ->select("{$this->db->dbprefix('cars_services')}.id as cid, {$this->db->dbprefix('cars_services')}.code, {$this->db->dbprefix('cars_services')}.name as cname,{$this->db->dbprefix('cars_services')}.second_name as second_name,  price,{$this->db->dbprefix('tax_rates')}.name as tax_rate_name, tax_method")
                ->from('cars_services')
                ->join('tax_rates', 'tax_rates.id=cars_services.tax_rate', 'left')


                ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('cars/edit_service/$1') . "' class='tip' title='" . lang('Edit_Service') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_Service') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('cars/delete_service/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'cid');

            echo $this->datatables->generate();
        }

        public function add_service()
        {
            $this->sma->checkPermissions('add_service', true);

            $this->load->helper('security');
            $this->form_validation->set_rules('code', lang('code'), 'trim|is_unique[cars_services.code]|required');
            $this->form_validation->set_rules('name', lang('name'), 'required');
            $this->form_validation->set_rules('price', lang('price'), 'required|numeric');
            $this->form_validation->set_rules('tax_method', lang('tax_method'), 'required|numeric');
            $this->form_validation->set_rules('tax_rate', lang('tax_rate'), 'required|numeric');


           // $this->sma->print_arrays($_POST);

            if ($this->form_validation->run() == true) {
                $data = ['code'      => $this->input->post('code'),
                    'name'           => $this->input->post('name'),
                    'second_name'           => $this->input->post('second_name'),
                    'price'           => $this->input->post('price'),
                    'tax_method'          => $this->input->post('tax_method'),
                    'tax_rate'          => $this->input->post('tax_rate'),
                    'cf1'          => $this->input->post('cf1'),
                    'cf2'          => $this->input->post('cf2'),
                    'cf3'          => $this->input->post('cf3'),
                    'cf4'          => $this->input->post('cf4'),
                    'cf5'          => $this->input->post('cf5'),
                    'cf6'          => $this->input->post('cf6'),
                    'service_details'          => $this->input->post('service_details'),
                    'details'          => $this->input->post('details'),

                ];

               // $this->sma->print_arrays($data);

            } elseif ($this->input->post('add_service')) {
                $this->session->set_flashdata('error', validation_errors());
                admin_redirect('cars/services');
            }

            if ($this->form_validation->run() == true && $this->cars_model->addservice($data)) {
                $this->session->set_flashdata('message', lang('Service_Added'));
           //     admin_redirect('cars/services');
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
               // $this->data['showroom_code'] = 'sh_'.$this->cars_model->getShowroomAutoCode()->id;
               $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();

                $this->data['modal_js']     = $this->site->modal_js();
                $this->load->view($this->theme . 'cars/add_service', $this->data);
            }
            }


            public function edit_service($id = null)
            {
                $this->sma->checkPermissions('edit_service', true);

                $this->load->helper('security');
                $this->form_validation->set_rules('code', lang('code'), 'trim|required');
                $this->form_validation->set_rules('name', lang('name'), 'required');
                $this->form_validation->set_rules('price', lang('price'), 'required|numeric');
               // $this->form_validation->set_rules('tax_method', lang('tax_method'), 'required|numeric');
                $this->form_validation->set_rules('tax_rate', lang('tax_rate'), 'required|numeric');


               // $this->sma->print_arrays($_POST);
               $service_details = $this->cars_model->getServiceByID($id);
                if ($this->form_validation->run() == true) {
                    $data = ['code'      => $this->input->post('code'),
                        'name'           => $this->input->post('name'),
                        'second_name'           => $this->input->post('second_name'),
                        'price'           => $this->input->post('price'),
                        'tax_method'          => $this->input->post('tax_method'),
                        'tax_rate'          => $this->input->post('tax_rate'),
                        'cf1'          => $this->input->post('cf1'),
                        'cf2'          => $this->input->post('cf2'),
                        'cf3'          => $this->input->post('cf3'),
                        'cf4'          => $this->input->post('cf4'),
                        'cf5'          => $this->input->post('cf5'),
                        'cf6'          => $this->input->post('cf6'),
                        'service_details'          => $this->input->post('service_details'),
                        'details'          => $this->input->post('details'),

                    ];

                   // $this->sma->print_arrays($id,$data);


                } elseif ($this->input->post('edit_service')) {

                    $this->session->set_flashdata('error', validation_errors());
                    admin_redirect('cars/services');
                }



                if ($this->form_validation->run() == true && $this->cars_model->updateService($id, $data)) {
                    $this->session->set_flashdata('message', lang('Service_Updated'));
               //     admin_redirect('cars/services');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                   $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();

                   $this->data['service']    = $service_details;
                    $this->data['modal_js']     = $this->site->modal_js();
                    $this->load->view($this->theme . 'cars/edit_service', $this->data);
                }
                }



                public function delete_service($id = null)
                {
                    $this->sma->checkPermissions('delete_service', true);


                    if ($this->cars_model->deleteService($id)) {

                        $this->sma->send_json(['error' => 0, 'msg' => lang('Service_Deleted')]);
                    }
                }



    public function modal_view($vehicle_id = null)
    {

        $this->sma->checkPermissions('modal_view',true);

        //$this->sma->print_arrays($id);

        $vehicle_details=$this->cars_model->getVehicleByID($vehicle_id);

       // $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');$this->load->helper('string');
        $this->data['modal_js'] = $this->site->modal_js();
        $this->data['vehicle'] = $vehicle_details;
        $this->data['carowner'] = $this->companies_model->getCompanyByID($vehicle_details->owner_id);
        //$this->sma->print_arrays($this->companies_model->getCompanyByID($vehicle_details->owner_id));

        $this->data['created_by']  = $this->site->getUser($vehicle_details->created_by);
        $this->data['updated_by']  = $vehicle_details->updated_by ? $this->site->getUser($vehicle_details->updated_by) : null;

        $this->data['brands'] = $this->cars_model->getAllBrands();
        $this->data['models'] = $this->cars_model->getAllModels();
        $this->data['features_external_equipment'] = $this->cars_model->getFeatureByType('external_equipment');
        $this->data['featured_comfort'] = $this->cars_model->getFeatureByType('comfort');
        $this->data['featured_techniques'] = $this->cars_model->getFeatureByType('techniques');
        $this->data['features_safty'] = $this->cars_model->getFeatureByType('safty');
        $this->data['showroom'] = $this->cars_model->getAllShowrooms();
        $this->data['warehouses']  = $this->cars_model->getAllWarehousesWithPQ($vehicle_id);

        $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
        //$this->sma->print_arrays($this->data['warehouses']);

        $this->load->view($this->theme . 'cars/modal_view', $this->data);
    }
    public function expenses($vehicle_id = null)
    {
        $this->sma->checkPermissions(false, true);
        $this->data['expenses'] = $this->cars_model->getCarsExpenses($vehicle_id);
        $this->data['inv']      = $this->cars_model->getVehicleByID($vehicle_id);
        $this->load->view($this->theme . 'cars/expenses', $this->data);
    }

////////////////////////////////transfer
    public function transfers()
    {
        $this->sma->checkPermissions('index',true,'transfers');


        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('transfers')]];
        $meta = ['page_title' => lang('transfers'), 'bc' => $bc];
        $this->page_construct('carstransfers/index', $meta, $this->data);
		//}
    }
    public function transfer_by_csv()
    {
     //   $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_message('is_natural_no_zero', lang('no_zero_required'));
        $this->form_validation->set_rules('to_warehouse', lang('warehouse') . ' (' . lang('to') . ')', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('from_warehouse', lang('warehouse') . ' (' . lang('from') . ')', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run()) {
            $transfer_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('to');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $to_warehouse           = $this->input->post('to_warehouse');
            $from_warehouse         = $this->input->post('from_warehouse');
            $note                   = $this->sma->clear_tags($this->input->post('note'));
            $shipping               = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $status                 = $this->input->post('status');
            $from_warehouse_details = $this->site->getWarehouseByID($from_warehouse);
            $from_warehouse_code    = $from_warehouse_details->code;
            $from_warehouse_name    = $from_warehouse_details->name;
            $to_warehouse_details   = $this->site->getWarehouseByID($to_warehouse);
            $to_warehouse_code      = $to_warehouse_details->code;
            $to_warehouse_name      = $to_warehouse_details->name;

            $total       = 0;
            $product_tax = 0;
            $gst_data    = [];
            $total_cgst  = $total_sgst  = $total_igst  = 0;
            if (isset($_FILES['userfile'])) {
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('transfers/transfer_bt_csv');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys  = ['product', 'unit_cost', 'quantity', 'variant', 'expiry'];
                $final = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                $rw = 2;
                foreach ($final as $csv_pr) {
                    $item_code     = $csv_pr['product'];
                    $unit_cost     = $csv_pr['unit_cost'];
                    $item_quantity = $csv_pr['quantity'];
                    $variant       = $csv_pr['variant'] ? $csv_pr['variant'] : null;
                    $item_expiry   = isset($csv_pr['expiry']) ? $this->sma->fsd($csv_pr['expiry']) : null;

                    if (isset($item_code) && isset($unit_cost) && isset($item_quantity)) {
                        if (!($product_details = $this->transfers_model->getProductByCode($item_code))) {
                            $this->session->set_flashdata('error', lang('pr_not_found') . ' ( ' . $csv_pr['product'] . ' ). ' . lang('line_no') . ' ' . $rw);
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                        if ($variant) {
                            $item_option = $this->transfers_model->getProductVariantByName($variant, $product_details->id);
                            if (!$item_option) {
                                $this->session->set_flashdata('error', lang('pr_not_found') . ' ( ' . $csv_pr['product'] . ' - ' . $csv_pr['variant'] . ' ). ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $item_option     = json_decode('{}');
                            $item_option->id = null;
                        }

                        if (!$this->Settings->overselling) {
                            $warehouse_quantity = $this->transfers_model->getWarehouseProduct($from_warehouse_details->id, $product_details->id, $item_option->id);
                            if ($warehouse_quantity->quantity < $item_quantity) {
                                $this->session->set_flashdata('error', lang('No_match_found') . ' (' . lang('product_name') . ' <strong>' . $product_details->name . '</strong> ' . lang('product_code') . ' <strong>' . $product_details->code . '</strong>) ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }

                        $pr_item_tax   = $item_tax   = 0;
                        $tax           = '';
                        $item_net_cost = $unit_cost;
                        if (isset($product_details->tax_rate) && $product_details->tax_rate != 0) {
                            $tax_details = $this->site->getTaxRateByID($product_details->tax_rate);
                            $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_cost);
                            $item_tax    = $ctax['amount'];
                            $tax         = $ctax['tax'];
                            if (!empty($product_details) && $product_details->tax_method != 1) {
                                $item_net_cost = $unit_cost - $item_tax;
                            }
                            $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_quantity), 4);
                            if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, false, $tax_details)) {
                                $total_cgst += $gst_data['cgst'];
                                $total_sgst += $gst_data['sgst'];
                                $total_igst += $gst_data['igst'];
                            }
                        }

                        $product_tax += $pr_item_tax;
                        $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_quantity) + $pr_item_tax), 4);
                        $unit     = $this->site->getUnitByID($product_details->unit);

                        $product = [
                            'product_id'        => $product_details->id,
                            'product_code'      => $item_code,
                            'product_name'      => $product_details->name,
                            'option_id'         => $item_option->id,
                            'net_unit_cost'     => $item_net_cost,
                            'unit_cost'         => $this->sma->formatDecimal($unit_cost, 4),
                            'quantity'          => $item_quantity,
                            'product_unit_id'   => $unit ? $unit->id : null,
                            'product_unit_code' => $unit ? $unit->code : null,
                            'unit_quantity'     => $item_quantity,
                            'quantity_balance'  => $item_quantity,
                            'warehouse_id'      => $to_warehouse,
                            'item_tax'          => $pr_item_tax,
                            'tax_rate_id'       => $product_details->tax_rate,
                            'tax'               => $tax,
                            'subtotal'          => $subtotal,
                            'expiry'            => $item_expiry,
                            'real_unit_cost'    => $unit_cost,
                            'date'              => date('Y-m-d', strtotime($date)),
                        ];

                        $products[] = ($product + $gst_data);
                        $total += $this->sma->formatDecimal(($item_net_cost * $item_quantity), 4);
                    }
                    $rw++;
                }
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_item'), 'required');
            } else {
                krsort($products);
            }
            $grand_total = $total + $shipping + $product_tax;
            $data        = ['transfer_no' => $transfer_no,
                'date'                    => $date,
                'from_warehouse_id'       => $from_warehouse,
                'from_warehouse_code'     => $from_warehouse_code,
                'from_warehouse_name'     => $from_warehouse_name,
                'to_warehouse_id'         => $to_warehouse,
                'to_warehouse_code'       => $to_warehouse_code,
                'to_warehouse_name'       => $to_warehouse_name,
                'note'                    => $note,
                'total_tax'               => $product_tax,
                'total'                   => $total,
                'grand_total'             => $grand_total,
                'created_by'              => $this->session->userdata('user_id'),
                'status'                  => $status,
                'shipping'                => $shipping,
            ];
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->transfers_model->addTransfer($data, $products)) {
            $this->session->set_userdata('remove_tols', 1);
            $this->session->set_flashdata('message', lang('transfer_added'));
            admin_redirect('transfers');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['name'] = ['name' => 'name',
                'id'                      => 'name',
                'type'                    => 'text',
                'value'                   => $this->form_validation->set_value('name'),
            ];
            $this->data['quantity'] = ['name' => 'quantity',
                'id'                          => 'quantity',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('quantity'),
            ];

            $this->data['warehouses'] = $this->site->getAllWarehouses();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['rnumber']    = $this->site->getReference('to');

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('transfers')], ['link' => '#', 'page' => lang('transfer_by_csv')]];
            $meta = ['page_title' => lang('add_transfer_by_csv'), 'bc' => $bc];
            $this->page_construct('transfers/transfer_by_csv', $meta, $this->data);
        }
    }
    public function transfers_suggestions()
    {
      //  $this->sma->checkPermissions('transfers',true);
        $term         = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->transfers_model->getProductNames($sr, $warehouse_id);
        if ($rows) {
            $r = 0;
            foreach ($rows as $row) {
                $c                     = uniqid(mt_rand(), true);
                $option                = false;
                $row->quantity         = 0;
                $row->item_tax_method  = $row->tax_method;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->base_unit_cost   = $row->cost;
                $row->unit             = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->qty              = 1;
                $row->discount         = '0';
                $row->expiry           = '';
                $row->quantity_balance = 0;
                $row->ordered_quantity = 0;
                $options               = $this->transfers_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->transfers_model->getProductOptionByID($option_id) : $options[0];
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt       = json_decode('{}');
                    $opt->cost = 0;
                    $option_id = false;
                }
                $row->option = $option_id;
                $pis         = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }
                if ($opt->cost != 0) {
                    $row->cost = $opt->cost;
                }
                $row->real_unit_cost = $row->cost;
                $units               = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate            = $this->site->getTaxRateByID($row->tax_rate);

                $pr[] = ['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'     => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('No_match_found'), 'value' => $term]]);
        }
    }
    public function transfer_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->transfers_model->deleteTransfer($id);
                    }
                    $this->session->set_flashdata('message', lang('transfers_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'combine') {
                    $html = $this->combine_pdf($_POST['val']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('transfers'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('from_warehouse'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('to_warehouse'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $tansfer = $this->transfers_model->getTransferByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($tansfer->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $tansfer->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $tansfer->from_warehouse);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $tansfer->to_warehouse);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $tansfer->grand_total);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $tansfer->status);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'tansfers_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_transfer_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function addByAjax()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->get('token', true) && $this->input->get('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product', true);



            /*if (!isset($product['code']) || empty($product['code'])) {
                exit(json_encode(['msg' => lang('product_code_is_required')]));
            }
            if (!isset($product['name']) || empty($product['name'])) {
                exit(json_encode(['msg' => lang('product_name_is_required')]));
            }
            if (!isset($product['category_id']) || empty($product['category_id'])) {
                exit(json_encode(['msg' => lang('product_category_is_required')]));
            }
            if (!isset($product['unit']) || empty($product['unit'])) {
                exit(json_encode(['msg' => lang('product_unit_is_required')]));
            }
            if (!isset($product['price']) || empty($product['price'])) {
                exit(json_encode(['msg' => lang('product_price_is_required')]));
            }
            if (!isset($product['cost']) || empty($product['cost'])) {
                exit(json_encode(['msg' => lang('product_cost_is_required')]));
            }
            if ($this->products_model->getProductByCode($product['code'])) {
                exit(json_encode(['msg' => lang('product_code_already_exist')]));
            }*/
            $product['name']=$this->cars_model->getBrandByID($product['brand_id'])->brand_name. ' - ' .$this->cars_model->getModelByID($product['model_id'])->model_name. ' - '  .$this->cars_model->getCategoryByID($product['category_id'])->categ_name;

            $product['active']=0;
            if ($row = $this->cars_model->addVehicleMainDataFPO($product)) {
                $row->unit=1;
				$row->cost             = $row->cost;
                $row->real_unit_cost   = $row->cost;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->base_unit_cost   = $row->cost;
                $row->unit             = $row->unit;
                $row->new_entry        = 1;
                $row->expiry           = '';
                $row->qty              = 1;
                $row->quantity_balance = '';
                $row->discount         = '0';
                $row->id=$row->vehicle_id;
                //unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);


				$units    = $this->site->getUnitsByBUID($row->base_unit);
				$options              = $this->products_model->getProductOptions($row->id);

                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				$tax_rate = '';//$this->site->getTaxRateByID($row->tax_rate);
                $row->code=$row->chesis_no;
                $row->type='standard';


                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				//['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                //    'row'     => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $pr       = ['row'     => $row, 'units' => $units, 'options' => $options,'item_id' => $row->id,'id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0', 'owner_id' => $row->owner_id ];
                $this->sma->send_json(['msg' => 'success', 'result' => $pr]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_add_product')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }






    public function reports()
    {
        //$this->sma->checkPermissions();
        $data['error']               = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->reports_model->getChartData();
        $this->data['stock']         = $this->reports_model->getStockValue();
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Vehicles')], ['link' => '#', 'page' => lang('reports')]];
        $meta                        = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->page_construct('cars/reports', $meta, $this->data);
    }

     public function products_item_report()
    {
        //$this->sma->checkPermissions('products_item_report',true);
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Vehicles')], ['link' => '#', 'page' => lang('cars_report')]];
        $meta = ['page_title' => lang('Vehicles_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'cars/reports', null,true);
		$this->page_construct('cars/products_item', $meta, $this->data);
    }
    public function warehouse_stock($warehouse = null)
    {
        //$this->sma->checkPermissions('warehouse_stock',true);

        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }

        $this->data['stock']        = $warehouse ? $this->reports_model->getWarehouseStockValue($warehouse) : $this->reports_model->getStockValue();
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse;
        $this->data['warehouse']    = $warehouse ? $this->site->getWarehouseByID($warehouse) : null;
        $this->data['totals']       = $this->reports_model->getWarehouseTotals($warehouse);
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('reports')]];
        $meta                       = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'cars/reports', null,true);
		$this->page_construct('cars/warehouse_stock', $meta, $this->data);

    }
    public function products_report()
    {
       // $this->sma->checkPermissions('products_report',true);
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('cars'), 'page' => lang('Vehicles')], ['link' => '#', 'page' => lang('Vehicles_report_proff_less')]];
        $meta = ['page_title' => lang('Vehicles_report_proff_less'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'cars/reports', null,true);
		$this->page_construct('cars/products', $meta, $this->data);
    }

    public function update_status($id)
    {
        $this->form_validation->set_rules('status', lang('Status'), 'required');

        if ($this->form_validation->run() == true) {
            $status = $this->input->post('status');
        } elseif ($this->input->post('update')) {
            $this->session->set_flashdata('error', validation_errors());
             admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'cars');
        }

        if ($this->form_validation->run() == true && $this->cars_model->updateStatus($id, $status)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'cars');
        } else {
            $this->data['inv']      = $this->cars_model->getVehicleByID($id);
            //$this->sma->print_arrays($this->data['inv']);
            $this->data['returned'] = false;
            if ($this->data['inv']->status == 'sold') {
                $this->data['returned'] = true;
            }
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'cars/update_status', $this->data);
        }
    }



}