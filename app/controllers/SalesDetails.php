<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SalesDetails extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            //admin_redirect('login');
        }

        if ($this->Customer || $this->Supplier) {
            redirect('/');
        }

        $this->load->library('form_validation');
        $this->load->admin_model('sales_model');
        $this->load->admin_model('quotes_model');
        $this->load->admin_model('pos_model');


    }

        public function language($lang = false)
    {
        if ($this->input->get('lang')) {
            $lang = $this->input->get('lang');
        }

        //$this->load->helper('cookie');
        $folder        = 'app/language/';
        $languagefiles = scandir($folder);
        if (in_array($lang, $languagefiles)) {
            $cookie = [
                'name'   => 'language',
                'value'  => $lang,
                'expire' => '31536000',
                'prefix' => 'sma_',
                'secure' => false,
            ];
            $this->input->set_cookie($cookie);
        }

        //redirect($_SERVER['HTTP_REFERER']);
		//$this->sma->print_arrays("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    }



   public function view(/*$id = null,*/$company = null ,$uuid = null)
    {
        //$this->sma->checkPermissions('index');
		$this->db =$this->site->posdynamicdb($company);

                    $config['dsn'] = '';
                    $config['hostname'] = 'localhost';
                    $config['username'] = 'databaseuser';
                    $config['password'] = 'enter-password-cpeapi';
                    $config['database'] = 'cpesoft_'.$company;
                    //$config['username'] = 'root';
                    //$config['password'] = '';
                    //$config['database'] = $company;
                    $config['dbdriver'] = 'mysqli';
                    $config['dbprefix'] = 'sma_';
                    $config['pconnect'] = FALSE;
                    $config['db_debug'] = FALSE;
                    $config['cache_on'] = FALSE;
                    $config['cachedir'] = '';
                    $config['char_set'] = 'utf8';
                    $config['dbcollat'] = 'utf8_general_ci';
                    $config['cachedir'] = '';
                    $config['swap_pre'] = '';
                    $config['encrypt'] = FALSE;
                    $config['compress'] = FALSE;
                    $config['stricton'] = FALSE;
                    $config['failover'] = array();
                    $config['save_queries'] = FALSE;

                    $CI = &get_instance();
                    $dataArr = $CI->load->database($config, TRUE);
                    $this->mynewdb =$dataArr;

        $id = $this->mynewdb->query("SELECT id FROM sma_sales WHERE uuid = '".$uuid."' limit 1;")->result_array()[0]['id'];

		//$this->sma->print_arrays($id);
		$this->language('arabic');
		//
		//$id = $this->site->getInvoiceByUUID($uuid)->id;
        //$this->sma->print_arrays($id);
		if(!$id){
			$bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('sales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('view')]];
        $meta = ['page_title' => lang('sale_details'), 'bc' => $bc];
        $this->page_construct('sales/page_404', $meta, $this->data);
		}else{

            //$this->db =$this->site->posdynamicdb($company);



        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->sales_model->getInvoiceByID($id);


        $this->data['customer']    = $this->sales_model->getCompanyByID($inv->customer_id);

        //$this->data['payments']    = $this->sales_model->getInvoicePayments($id);
        $this->data['biller']      = $this->sales_model->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->sales_model->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->sales_model->getUser($inv->updated_by) : null;
        $this->data['warehouse']   = $this->sales_model->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['rows']        = $this->sales_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : null;
        $this->data['paypal']      = $this->sales_model->getPaypalSettings();
        $this->data['skrill']      = $this->sales_model->getSkrillSettings();
        $this->data['setting']     = $this->sales_model->getSettings();
		$this->data['units']       = $this->sales_model->getAllUnits();
        $this->data['debit_sale'] = $inv->return_debit_id ? $this->sales_model->getInvoiceByID($inv->return_debit_id) : null;
        $this->data['debit_rows']     = $inv->return_debit_id ? $this->sales_model->getAllsaleItemsDebit_idWithOutZeros($id) : null;
		$this->data['payments']        = $this->sales_model->getInvoicePayments($inv->id);
		$this->data['active_tax']        =$this->sales_model->getTaxRatesActive()->rate;
		$this->data['return_payments'] = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;
        $this->data['debit_payments'] = $inv->return_debit_id ? $this->sales_model->getInvoicePayments($inv->return_debit_id) : null;


        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('sales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('view')]];
        $meta = ['page_title' => lang('sale_details'), 'bc' => $bc];
        $this->page_construct('sales/view_2', $meta, $this->data);
    }
	}


    public function view_quote(/*$id = null,*/$company = null ,$uuid = null)
        {
            //$this->sma->checkPermissions('index');
            $this->db =$this->site->posdynamicdb($company);

                        $config['dsn'] = '';
                        $config['hostname'] = 'localhost';
                        $config['username'] = 'databaseuser';
                        $config['password'] = 'enter-password-cpeapi';
                        $config['database'] = 'cpesoft_'.$company;
                        //$config['username'] = 'root';
                        //$config['password'] = '';
                        //$config['database'] = $company;
                        $config['dbdriver'] = 'mysqli';
                        $config['dbprefix'] = 'sma_';
                        $config['pconnect'] = FALSE;
                        $config['db_debug'] = FALSE;
                        $config['cache_on'] = FALSE;
                        $config['cachedir'] = '';
                        $config['char_set'] = 'utf8';
                        $config['dbcollat'] = 'utf8_general_ci';
                        $config['cachedir'] = '';
                        $config['swap_pre'] = '';
                        $config['encrypt'] = FALSE;
                        $config['compress'] = FALSE;
                        $config['stricton'] = FALSE;
                        $config['failover'] = array();
                        $config['save_queries'] = FALSE;

                        $CI = &get_instance();
                        $dataArr = $CI->load->database($config, TRUE);
                        $this->mynewdb =$dataArr;

            $quote_id = $this->mynewdb->query("SELECT id FROM sma_quotes WHERE uuid = '".$uuid."' limit 1;")->result_array()[0]['id'];

            //$this->sma->print_arrays($id);
            $this->language('arabic');
            //
            //$id = $this->site->getInvoiceByUUID($uuid)->id;
            //$this->sma->print_arrays($id);
            if(!$quote_id){
                $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('sales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('view')]];
            $meta = ['page_title' => lang('quotes_details'), 'bc' => $bc];
            $this->page_construct('sales/page_404', $meta, $this->data);
            }else{

                //$this->db =$this->site->posdynamicdb($company);



            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $inv                 = $this->quotes_model->getQuoteByID($quote_id);


            $this->data['rows']       = $this->quotes_model->getAllQuoteItems($quote_id);
            $this->data['customer']   = $this->sales_model->getCompanyByID($inv->customer_id);
            $this->data['biller']     = $this->sales_model->getCompanyByID($inv->biller_id);
            $this->data['created_by'] = $this->sales_model->getUser($inv->created_by);
            $this->data['updated_by'] = $inv->updated_by ? $this->sales_model->getUser($inv->updated_by) : null;
            $this->data['warehouse']  = $this->sales_model->getWarehouseByID($inv->warehouse_id);
            $this->data['inv']        = $inv;

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('sales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('view')]];
            $meta = ['page_title' => lang('quotes_details'), 'bc' => $bc];
            $this->page_construct('quotes/view_2', $meta, $this->data);
        }
        }





}
