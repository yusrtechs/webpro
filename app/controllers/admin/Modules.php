<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('memory_limit', '-1');

//ini_set('memory_limit','2048M');
class Modules extends MY_Controller
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
        $this->load->admin_model('modules_model');
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
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('modules'), 'page' => lang('Modules')], ['link' => '#', 'page' => lang('Modules')]];
        $meta                = ['page_title' => lang('Modules'), 'bc' => $bc];
        $this->page_construct('modules/modules', $meta, $this->data);
    }

    public function add_module()
    {
      //  $this->sma->checkPermissions('add_modules', true);
        $this->form_validation->set_rules('name_en', lang('English_name'), 'trim|required|is_unique[modules.name_en]');


        if ($this->form_validation->run() == true) {

            $data = [
                'name'        => $this->input->post('name'),
                'name_en'     => $this->input->post('name_en'),
                'description' => $this->input->post('description'),
                'directory' => $this->input->post('directory'),
                'status' => $this->input->post('status'),
                'icon' => $this->input->post('icon') ? $this->input->post('icon') : 'home',

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
            //$this->sma->print_arrays($data,$_POST);
        } elseif ($this->input->post('add_module')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('modules');
			//redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->modules_model->addModule($data)) {
            $this->session->set_flashdata('message', lang('Module_added_successfully'));
            admin_redirect('modules');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'modules/add_module', $this->data);
        }
       //  $this->sma->print_arrays($data);
    }



    public function edit_module($id = null)
    {
        $this->form_validation->set_rules('name_en', lang('English_name'), 'trim|required');

//        $this->form_validation->set_rules('name_en', lang('module_name'), 'trim|required|alpha_numeric_spaces');
        $module_details = $this->modules_model->getModuleByID($id);
        if ($this->input->post('name_en') != $module_details->name_en) {
            $this->form_validation->set_rules('name_en', lang('Module_name'), 'trim|required|is_unique[modules.name_en]');
        }
      //  $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
  //      $this->form_validation->set_rules('description', lang('description'), 'required|regex_match[/^[a-zA-Z0-9\/\s]+$/]');
    //    if ($this->input->post('description') != $module_details->description) {
      //      $this->form_validation->set_rules('description', lang('description'), 'required|regex_match[/^[a-zA-Z0-9\/\s]+$/]|is_unique[modules.description]');
       // }
       // $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run() == true) {

            $data = [
                'name'        => $this->input->post('name'),
                'name_en'     => $this->input->post('name_en'),
                'description' => $this->input->post('description'),
                'directory' => $this->input->post('directory'),
                'status' => $this->input->post('status'),
                'icon' => $this->input->post('icon') ? $this->input->post('icon') : 'home',

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
            //$this->sma->print_arrays($data,$_POST);
        } elseif ($this->input->post('edit_module')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('modules');
		//	redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->modules_model->updateModule($id, $data)) {
            $this->session->set_flashdata('message', lang('module_updated'));
            admin_redirect('modules');
		//	redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['module']    = $module_details;
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'modules/edit_module', $this->data);
        }
    }


    public function getModules()
    {
        $this->load->library('datatables');
        $this->datatables
        ->select('id, image, name, name_en, description, directory, status, icon')
          // ->select('id, image, name, name_en, description, directory, status, isFree, isPaid')
          // ->select('id, image, name, name_en, description, directory')
            ->from('modules')
            //->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('modules/edit_module/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_module') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_module') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('modules/delete_module/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('modules/objects/$1') . "' class='tip' title='" . lang('Objects') . "'><i class=\"fa fa-tasks\"></i></a> <a href='" . admin_url('modules/edit_module/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_module') . "'><i class=\"fa fa-edit\"></i></a> </div>", 'id');

        echo $this->datatables->generate();
    }

    public function delete_module($id = null)
    {
        //$this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->modules_model->deleteModule($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Module_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Module_deleted_successfully'));
            admin_redirect('modules');
        }

    }




    public function module_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                   // $this->sma->checkPermissions('delete_module');
                    foreach ($_POST['val'] as $id) {
                        $this->modules_model->deleteModule($id);
                    }
                    $this->session->set_flashdata('message', lang('modules_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('modules'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('id'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('English_name'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->modules_model->getModuleByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->id);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->name_en);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'modules_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_module_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }


//---------



public function objects($id = null)
{
    if (!$id) {
        die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
    }

    $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
    $this->data['id']    = $id;
    $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('modules'), 'page' => lang('Objects')], ['link' => '#', 'page' => lang('Objects')]];
    $meta                = ['page_title' => lang('Objects'), 'bc' => $bc];
    $this->page_construct('modules/objects', $meta, $this->data);
}

public function add_object($id = null)
{
  //  $this->sma->checkPermissions('add_objects', true);
    $this->form_validation->set_rules('name_en', lang('English_name'), 'trim|required');


    if ($this->form_validation->run() == true) {

        $data = [
            'name'        => $this->input->post('name'),
            'name_en'     => $this->input->post('name_en'),
            'description' => $this->input->post('description'),
            'directory' => $this->input->post('directory'),
            'status' => $this->input->post('status'),
            'type' => $this->input->post('type'),
            'module_id' => $id,
            'sort_ord' => $this->input->post('sort_ord2') ? $this->input->post('sort_ord2') :$this->input->post('sort_ord'),
            'rnk' => $this->input->post('sort_ord2') ? $this->input->post('rnk') : null,
            'created_by' => $this->session->userdata('user_id'),

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
        //$this->sma->print_arrays($data,$_POST);
    } elseif ($this->input->post('add_object')) {
        $this->session->set_flashdata('error', validation_errors());
        admin_redirect('modules/objects/'.$id);
        //redirect($_SERVER['HTTP_REFERER']);
    }

    if ($this->form_validation->run() == true && $this->modules_model->addObject($data)) {
        $this->session->set_flashdata('message', lang('Object_added_successfully'));
        admin_redirect('modules/objects/'.$id);
        //redirect($_SERVER['HTTP_REFERER']);
    } else {
        $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
        $this->data['sub_menu']    = $this->modules_model->getObjectsByModID($id,2);
        $this->data['modal_js'] = $this->site->modal_js();
        $this->load->view($this->theme . 'modules/add_object', $this->data);
    }
   //  $this->sma->print_arrays($data);
}



public function edit_object($id = null)
{
    $this->form_validation->set_rules('name', lang('name'), 'trim|required');

    $object_details = $this->modules_model->getObjectByID($id);
    if ($this->input->post('name_en') != $object_details->name_en) {
        $this->form_validation->set_rules('name_en', lang('Object_name'), 'trim|required|is_unique[objects.name_en]');
    }

    if ($this->form_validation->run() == true) {
        $data = [
            'name'        => $this->input->post('name'),
            'name_en'     => $this->input->post('name_en'),
            'description' => $this->input->post('description'),
            'directory' => $this->input->post('directory'),
            'status' => $this->input->post('status'),
            'type' => $this->input->post('type'),
            'sort_ord' => $this->input->post('sort_ord2') ? $this->input->post('sort_ord2') :$this->input->post('sort_ord'),
            'rnk' => $this->input->post('sort_ord2') ? $this->input->post('rnk') : null,
            'updated_by' => $this->session->userdata('user_id'),
            'updated_at'          => date('Y-m-d H:i:s'),
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
        //$this->sma->print_arrays($data,$_POST);
    } elseif ($this->input->post('edit_object')) {
        $this->session->set_flashdata('error', validation_errors());
        admin_redirect('modules/objects/'.$object_details->module_id);
    //	redirect($_SERVER['HTTP_REFERER']);
    }

    if ($this->form_validation->run() == true && $this->modules_model->updateObject($id, $data)) {
        $this->session->set_flashdata('message', lang('object_updated'));
        admin_redirect('modules/objects/'.$object_details->module_id);
    //	redirect($_SERVER['HTTP_REFERER']);
    } else {
        $this->data['object']    = $object_details;
        $this->data['sub_menu']    = $this->modules_model->getObjectsByModID($object_details->module_id,2);
        $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['modal_js'] = $this->site->modal_js();
        $this->load->view($this->theme . 'modules/edit_object', $this->data);
    }
}


public function getObjects($id = null)
{
    $this->load->library('datatables');
    $this->datatables
    ->select($this->db->dbprefix('objects') . '.id as id, objects.image, objects.name, objects.name_en, description, directory, status,type,sort_ord,rnk, CONCAT(' . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . '.last_name) as created_by,created_at')
    ->join('users', 'users.id=objects.created_by', 'left')
    ->from('objects')
    ->where('objects.module_id',$id)
    ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('modules/edit_object/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_object') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_object') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('modules/delete_object/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

    echo $this->datatables->generate();
}

public function delete_object($id = null)
{
    //$this->sma->checkPermissions('deletemanufacturing', true);

    if ($this->input->get('id')) {
        $id = $this->input->get('id');
    }

    if ($this->modules_model->deleteObject($id)) {
        if ($this->input->is_ajax_request()) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('Object_deleted_successfully')]);
        }
        $this->session->set_flashdata('message', lang('Object_deleted_successfully'));
        admin_redirect('modules/objects');
    }

}




public function object_actions()
{
    if (!$this->Owner && !$this->GP['bulk_actions']) {
        $this->session->set_flashdata('warning', lang('access_denied'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

    if ($this->form_validation->run() == true) {
        if (!empty($_POST['val'])) {
            if ($this->input->post('form_action') == 'delete') {
               // $this->sma->checkPermissions('delete_object');
                foreach ($_POST['val'] as $id) {
                    $this->modules_model->deleteObject($id);
                }
                $this->session->set_flashdata('message', lang('objects_deleted'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            if ($this->input->post('form_action') == 'export_excel') {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('objects'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('id'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('English_name'));

                $row = 2;
                foreach ($_POST['val'] as $id) {
                    $sc = $this->modules_model->getObjectByID($id);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->id);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->name_en);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'objects_' . date('Y_m_d_H_i_s');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
        } else {
            $this->session->set_flashdata('error', lang('no_object_selected'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    } else {
        $this->session->set_flashdata('error', validation_errors());
        redirect($_SERVER['HTTP_REFERER']);
    }
}












}