<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class ItemController extends MY_Controller {
  
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
	   if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->lang->admin_load('sales', $this->Settings->user_language);
        $this->lang->admin_load('quotations', $this->Settings->user_language);
        $this->lang->admin_load('returns', $this->Settings->user_language);
        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('sales_model');
        $this->load->admin_model('pos_model');
        $this->load->admin_model('purchases_model');
        $this->load->admin_model('quotes_model');
        $this->load->admin_model('returns_model');
        $this->load->admin_model('reports_model');
        $this->load->admin_model('db_model');
		$this->load->admin_model('settings_model');
		
       $this->load->database();
    }
  
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index()
    {
		
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('sales')]];
        $meta = ['page_title' => lang('sales'), 'bc' => $bc];
        $this->page_construct('sales/items', $meta, $this->data);
        //$this->load->view('items');
    }
   
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function getItem()
    {
          $data = [];
          $parent_key = '0';
          $row = $this->db->query('SELECT * from sma_gl_accounts');
            
          if($row->num_rows() > 0)
          {
              $data = $this->membersTree($parent_key);
          }else{
              $data=["id"=>"0","name"=>"No Members presnt in list","text"=>"No Members is presnt in list","nodes"=>[]];
          }
   
          echo json_encode(array_values($data));
    }
   
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function membersTree($parent_key)
    {
        $row1 = [];
        $row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();
    
        foreach($row as $key => $value)
        {
           $id = $value['id'];
           $row1[$key]['id'] = $value['id'];
           $row1[$key]['name'] = $value['accountName'];
           $row1[$key]['text'] = $value['accountName'];
           $row1[$key]['nodes'] = array_values($this->membersTree($value['id']));
        }
  
        return $row1;
    }
      
}