<?php

//ini_set('memory_limit', '-1');
//ini_set('max_execution_time', '-1');
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('memory_limit', '-1');
ini_set('memory_limit','2048M');

class Pos extends MY_Controller
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
		/*$db_name=str_replace("cpesoft_","",$this->db->database);
		if($db_name=="marsaalshati" || $db_name=="oildrop"){
			ini_set('memory_limit','2048M');
		}*/

        $this->load->admin_model('pos_model');
        $this->load->admin_model('pos2_model');
        $this->load->admin_model('purchases_model');
		$this->load->admin_model('sales_model');

        $this->load->helper('text');
        $this->pos_settings           = $this->pos_model->getSetting();
        $this->pos_settings->pin_code = $this->pos_settings->pin_code ? md5($this->pos_settings->pin_code) : null;
        $this->data['pos_settings']   = $this->pos_settings;
        $this->session->set_userdata('last_activity', now());
        $this->lang->admin_load('pos', $this->Settings->user_language);
        $this->load->library('form_validation');
    }

    public function active()
    {
        $this->session->set_userdata('last_activity', now());
        if ((now() - $this->session->userdata('last_activity')) <= 20) {
            die('Successfully updated the last activity.');
        }
        die('Failed to update last activity.');
    }

    public function add_payment($id = null)
    {
        $this->sma->checkPermissions('payments', true, 'sales');
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            $sale = $this->pos_model->getInvoiceByID($this->input->post('sale_id'));
            if ($this->input->post('paid_by') == 'deposit') {
                $customer_id = $sale->customer_id;
                if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                    $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $customer_id = null;
            }
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'sale_id'      => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'cc_cvv2'      => $this->input->post('pcc_ccv'),
                'note'         => $this->input->post('note'),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => $sale->sale_status == 'returned' ? 'returned' : 'received',
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

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $msg = $this->pos_model->addPayment($payment, $customer_id)) {
            if ($msg) {
                if ($msg['status'] == 0) {
                    unset($msg['status']);
                    $error = '';
                    foreach ($msg as $m) {
                        if (is_array($m)) {
                            foreach ($m as $e) {
                                $error .= '<br>' . $e;
                            }
                        } else {
                            $error .= '<br>' . $m;
                        }
                    }
                    $this->session->set_flashdata('error', '<pre>' . $error . '</pre>');
                } else {
                    $this->session->set_flashdata('message', lang('payment_added'));
                }
            } else {
                $this->session->set_flashdata('error', lang('payment_failed'));
            }
            admin_redirect('pos/sales');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $sale                      = $this->pos_model->getInvoiceByID($id);
            $this->data['inv']         = $sale;
            $this->data['payment_ref'] = $this->site->getReference('pay');
            $this->data['modal_js']    = $this->site->modal_js();

            $this->load->view($this->theme . 'pos/add_payment', $this->data);
        }
    }

    public function add_printer()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('pos');
        }

        $this->form_validation->set_rules('title', $this->lang->line('title'), 'required');
        $this->form_validation->set_rules('type', $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line('profile'), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line('char_per_line'), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line('ip_address'), 'required|is_unique[printers.ip_address]');
            $this->form_validation->set_rules('port', $this->lang->line('port'), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line('path'), 'required|is_unique[printers.path]');
        }

        if ($this->form_validation->run() == true) {
            $data = ['title'    => $this->input->post('title'),
                'type'          => $this->input->post('type'),
                'profile'       => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path'          => $this->input->post('path'),
                'ip_address'    => $this->input->post('ip_address'),
                'port'          => ($this->input->post('type') == 'network') ? $this->input->post('port') : null,
            ];
        }

        if ($this->form_validation->run() == true && $cid = $this->pos_model->addPrinter($data)) {
            $this->session->set_flashdata('message', $this->lang->line('printer_added'));
            admin_redirect('pos/printers');
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'failed', 'msg' => validation_errors()]);
                die();
            }

            $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('add_printer');
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => admin_url('pos/printers'), 'page' => lang('printers')], ['link' => '#', 'page' => lang('add_printer')]];
            $meta                     = ['page_title' => lang('add_printer'), 'bc' => $bc];
            $this->page_construct('pos/add_printer', $meta, $this->data);
        }
    }

    public function ajaxbranddata($brand_id = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('brand_id')) {
            $brand_id = $this->input->get('brand_id');
        }

        $products = $this->ajaxproducts(false, $brand_id);

        if (!($tcp = $this->pos_model->products_count(false, false, $brand_id))) {
            $tcp = 0;
        }

        $this->sma->send_json(['products' => $products, 'tcp' => $tcp]);
    }

    public function ajaxcategorydata($category_id = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }

        $subcategories = $this->site->getSubCategories($category_id);
        $scats         = '';
        if ($subcategories) {
            foreach ($subcategories as $category) {
                $scats .= '<button id="subcategory-' . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory\" ><img src=\"" . base_url() . 'assets/uploads/thumbs/' . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded img-thumbnail' /><span>" . $category->name . '</span></button>';
            }
        }

        $products = $this->ajaxproducts($category_id);

        if (!($tcp = $this->pos_model->products_count($category_id))) {
            $tcp = 0;
        }

        $this->sma->send_json(['products' => $products, 'subcategories' => $scats, 'tcp' => $tcp]);
    }

    public function ajaxcategorydataAll()
    {
        $this->sma->checkPermissions('index');
		$categories    = $this->site->getAllCategoriesN0HidePOS();
		$categories_data = [];
		foreach ($categories as $cat) {
            $category_id=$cat->id;
			//$products =null;
            /*
        if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }
		*/

        $subcategories = $this->site->getSubCategories($category_id);
        $scats         = '';
        if ($subcategories) {
            foreach ($subcategories as $category) {


                if($this->pos_settings->categories_tab==0)
				$scats .= '<button id="subcategory-' . $category->id . "\" type=\"button\" value='" . $category->id . "' class=\"btn-prni subcategory\" ><img src=\"" . base_url() . 'assets/uploads/thumbs/' . ($category->image ? $category->image : 'no_image.png') . "\" class='img-rounded img-thumbnail' /><span>" . $category->name . '</span></button>';
                else
				$scats .= "<li class='nav-item ' > <a style='min-width:80px;color:#212529;'  class='nav-link subcategory-tab' id='subcategory-tab" . $category->id . "' value='" . $category->id . "'   data-toggle='pill' href='#".$category->name ."' role='tab'>" . $category->name . "</a></li>";
            }
        }

        $products = $this->ajaxproductsAll($category_id);

        if (!($tcp = $this->pos_model->products_count($category_id))) {
            $tcp = 0;
        }

		$categories_data[$category_id] =['products' => $products, 'subcategories' => $scats, 'tcp' => $tcp];
		}

    //    $this->sma->send_json(['products' => $products, 'subcategories' => $scats, 'tcp' => $tcp]);
        $this->sma->send_json($categories_data);
    }

    public function ajaxproducts($category_id = null, $brand_id = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('brand_id')) {
            $brand_id = $this->input->get('brand_id');
        }

		if ($this->input->get('category_id')) {
            $category_id = $this->input->get('category_id');
        } else {
            $category_id = $this->pos_settings->default_category;
        }
        if ($this->input->get('subcategory_id')) {
            $subcategory_id = $this->input->get('subcategory_id');
        } else {
            $subcategory_id = null;
        }
        if (empty($this->input->get('per_page')) || $this->input->get('per_page') == 'n') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }

        $this->load->library('pagination');

        $config                  = [];
        $config['base_url']      = base_url() . 'pos/ajaxproducts';
        $config['total_rows']    = $this->pos_model->products_count($category_id, $subcategory_id, $brand_id);
        $config['per_page']      = $this->pos_settings->pro_limit;
        $config['prev_link']     = false;
        $config['next_link']     = false;
        $config['display_pages'] = false;
        $config['first_link']    = false;
        $config['last_link']     = false;

        $this->pagination->initialize($config);

        $products = $this->pos_model->fetch_products($category_id, $config['per_page'], $page, $subcategory_id, $brand_id);
        $pro      = 1;
        $prods    = '<div>';
        if (!empty($products)) {
            foreach ($products as $product) {
                $count = $product->id;
                if ($count < 10) {
                    $count = '0' . ($count / 100) * 100;
                }
                if ($category_id < 10) {
                    $category_id = '0' . ($category_id / 100) * 100;
                }

                $prods .= '<button id="product-' . $category_id . $count . "\" type=\"button\" value='" . $product->code . "' title=\"" . $product->name.'" class="btn-prni btn-' . $this->pos_settings->product_button_color . ' product pos-tip" data-container="body"><img src="' . base_url() . 'assets/uploads/thumbs/' . $product->image . '" alt="' . $product->name . "\" class='img-rounded' /><span>" . character_limiter($product->name . '</br>' .$product->second_name, 40) . '</span></button>';

                $pro++;
            }
        }
        $prods .= '</div>';

        if ($this->input->get('per_page')) {
            echo $prods;
        } else {
            return $prods;
        }
    }


    public function ajaxproductsAll($category_id = null, $brand_id = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('brand_id')) {
            $brand_id = $this->input->get('brand_id');
        }


        if ($this->input->get('subcategory_id')) {
            $subcategory_id = $this->input->get('subcategory_id');
        } else {
            $subcategory_id = null;
        }
        if (empty($this->input->get('per_page')) || $this->input->get('per_page') == 'n') {
            $page = 0;
        } else {
            $page = $this->input->get('per_page');
        }

        $this->load->library('pagination');

        $config                  = [];
        $config['base_url']      = base_url() . 'pos/ajaxproducts';
        $config['total_rows']    = $this->pos_model->products_count($category_id, $subcategory_id, $brand_id);
        $config['per_page']      = $this->pos_settings->pro_limit;
        $config['prev_link']     = false;
        $config['next_link']     = false;
        $config['display_pages'] = false;
        $config['first_link']    = false;
        $config['last_link']     = false;

        $this->pagination->initialize($config);

        $products = $this->pos_model->fetch_products($category_id, $config['per_page'], $page, $subcategory_id, $brand_id);
        $pro      = 1;
        $prods    = '<div>';
        if (!empty($products)) {
            foreach ($products as $product) {
                $count = $product->id;
                if ($count < 10) {
                    $count = '0' . ($count / 100) * 100;
                }
                if ($category_id < 10) {
                    $category_id = '0' . ($category_id / 100) * 100;
                }

                $prods .= '<button id="product-' . $category_id . $count . "\" type=\"button\" value='" . $product->code . "' title=\"" . $product->name . '" class="btn-prni btn-' . $this->pos_settings->product_button_color . ' product pos-tip" data-container="body"><img src="' . base_url() . 'assets/uploads/thumbs/' . $product->image . '" alt="' . $product->name . "\" class='img-rounded' /><span>" . character_limiter($product->name, 40) . '</span></button>';

                $pro++;
            }
        }
        $prods .= '</div>';

        if ($this->input->get('per_page')) {
            echo $prods;
        } else {
            return $prods;
        }
    }

    public function barcode($text = null, $bcs = 'code128', $height = 50)
    {
        return admin_url('products/gen_barcode/' . $text . '/' . $bcs . '/' . $height);
    }

    public function check_pin()
    {
        $pin = $this->input->post('pw', true);
        if ($pin == $this->pos_pin) {
            $this->sma->send_json(['res' => 1]);
        }
        $this->sma->send_json(['res' => 0]);
    }

    public function close_register($user_id = null)
    {
        $this->sma->checkPermissions('index');
        if (!$this->Owner && !$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->form_validation->set_rules('total_cash', lang('total_cash'), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cheques', lang('total_cheques'), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cc_slips', lang('total_cc_slips'), 'trim|required|numeric');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : null;
                $rid           = $user_register ? $user_register->id : $this->session->userdata('register_id');
                $user_id       = $user_register ? $user_register->user_id : $this->session->userdata('user_id');
            } else {
                $rid     = $this->session->userdata('register_id');
                $user_id = $this->session->userdata('user_id');
            }
            $data = [
                'closed_at'                => date('Y-m-d H:i:s'),
                'total_cash'               => $this->input->post('total_cash'),
                'total_cheques'            => $this->input->post('total_cheques'),
                'total_cc_slips'           => $this->input->post('total_cc_slips'),
                'total_cash_submitted'     => $this->input->post('total_cash_submitted'),
                'total_cheques_submitted'  => $this->input->post('total_cheques_submitted'),
                'total_cc_slips_submitted' => $this->input->post('total_cc_slips_submitted'),
                'note'                     => $this->input->post('note'),
                'status'                   => 'close',
                'transfer_opened_bills'    => $this->input->post('transfer_opened_bills'),
                'closed_by'                => $this->session->userdata('user_id'),
            ];
        } elseif ($this->input->post('close_register')) {
            $this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
            admin_redirect('pos');
        }

        if ($this->form_validation->run() == true && $this->pos_model->closeRegister($rid, $user_id, $data)) {
            $this->session->set_flashdata('message', lang('register_closed'));
            admin_redirect('welcome');
        } else {
            if ($this->Owner || $this->Admin) {
                $user_register                    = $user_id ? $this->pos_model->registerData($user_id) : null;
                $register_open_time               = $user_register ? $user_register->date : null;
                $this->data['cash_in_hand']       = $user_register ? $user_register->cash_in_hand : null;
                $this->data['register_open_time'] = $user_register ? $register_open_time : null;
            } else {
                $register_open_time               = $this->session->userdata('register_open_time');
                $this->data['cash_in_hand']       = null;
                $this->data['register_open_time'] = null;
            }
            $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['ccsales']         = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
            $this->data['cashsales']       = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
            $this->data['chsales']         = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
            $this->data['gcsales']         = $this->pos_model->getRegisterGCSales($register_open_time);
            $this->data['pppsales']        = $this->pos_model->getRegisterPPPSales($register_open_time, $user_id);
            $this->data['stripesales']     = $this->pos_model->getRegisterStripeSales($register_open_time, $user_id);
            $this->data['authorizesales']  = $this->pos_model->getRegisterAuthorizeSales($register_open_time, $user_id);
            $this->data['totalsales']      = $this->pos_model->getRegisterSales($register_open_time, $user_id);
            $this->data['refunds']         = $this->pos_model->getRegisterRefunds($register_open_time, $user_id);
            $this->data['returns']         = $this->pos_model->getRegisterReturns($register_open_time, $user_id);
            $this->data['cashrefunds']     = $this->pos_model->getRegisterCashRefunds($register_open_time, $user_id);
            $this->data['expenses']        = $this->pos_model->getRegisterExpenses($register_open_time, $user_id);
            $this->data['users']           = $this->pos_model->getUsers($user_id);
            $this->data['suspended_bills'] = $this->pos_model->getSuspendedsales($user_id);
            $this->data['user_id']         = $user_id;
            $this->data['modal_js']        = $this->site->modal_js();
            $this->load->view($this->theme . 'pos/close_register', $this->data);
        }
    }

    public function delete($id = null)
    {
        $this->sma->checkPermissions('index');

        if ($this->pos_model->deleteBill($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('suspended_sale_deleted')]);
        }
    }

     public function signing($request)
    {
		$request=$this->input->post('request');
		$data = [
                'request'                =>$request
	];
       $this->load->view($this->theme . 'pos/signing/sign-message', $this->data);
    }

    public function delete_printer($id = null)
    {
        if (DEMO) {
            $this->session->set_flashdata('error', $this->lang->line('disabled_in_demo'));
            $this->sma->md();
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id', true);
        }

        if ($this->pos_model->deletePrinter($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('printer_deleted')]);
			$this->session->set_flashdata('message', $this->lang->line('printer_deleted'));
            admin_redirect('pos/printers');

        }

    }

    public function edit_printer($id = null)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id', true);
        }

        $printer = $this->pos_model->getPrinterByID($id);
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'required');
        $this->form_validation->set_rules('type', $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line('profile'), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line('char_per_line'), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line('ip_address'), 'required');
            if ($this->input->post('ip_address') != $printer->ip_address) {
                $this->form_validation->set_rules('ip_address', $this->lang->line('ip_address'), 'is_unique[printers.ip_address]');
            }
            $this->form_validation->set_rules('port', $this->lang->line('port'), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line('path'), 'required');
            if ($this->input->post('path') != $printer->path) {
                $this->form_validation->set_rules('path', $this->lang->line('path'), 'is_unique[printers.path]');
            }
        }

        if ($this->form_validation->run() == true) {
            $data = ['title'    => $this->input->post('title'),
                'type'          => $this->input->post('type'),
                'profile'       => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path'          => $this->input->post('path'),
                'ip_address'    => $this->input->post('ip_address'),
                'port'          => ($this->input->post('type') == 'network') ? $this->input->post('port') : null,
            ];
        }

        if ($this->form_validation->run() == true && $this->pos_model->updatePrinter($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line('printer_updated'));
            admin_redirect('pos/printers');
        } else {
            $this->data['printer']    = $printer;
            $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('edit_printer');
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => admin_url('pos/printers'), 'page' => lang('printers')], ['link' => '#', 'page' => lang('edit_printer')]];
            $meta                     = ['page_title' => lang('edit_printer'), 'bc' => $bc];
            $this->page_construct('pos/edit_printer', $meta, $this->data);
        }
    }

    public function email_receipt($sale_id = null, $view = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->post('id')) {
            $sale_id = $this->input->post('id');
        }
        if (!$sale_id) {
            die('No sale selected.');
        }
        if ($this->input->post('email')) {
            $to = $this->input->post('email');
        }
        $this->data['error']   = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['rows']            = $this->pos_model->getAllInvoiceItems($sale_id);
        $inv                           = $this->pos_model->getInvoiceByID($sale_id);
        $biller_id                     = $inv->biller_id;
        $customer_id                   = $inv->customer_id;
        $this->data['biller']          = $this->pos_model->getCompanyByID($biller_id);
        $this->data['customer']        = $this->pos_model->getCompanyByID($customer_id);
        $this->data['payments']        = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['pos']             = $this->pos_model->getSetting();
        $this->data['barcode']         = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['return_sale']     = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
        $this->data['inv']             = $inv;
        $this->data['sid']             = $sale_id;
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['page_title']      = $this->lang->line('invoice');

        $receipt = $this->load->view($this->theme . 'pos/email_receipt', $this->data, true);
        if ($view) {
            echo $receipt;
            die();
        }

        if (!$to) {
            $to = $this->data['customer']->email;
        }
        if (!$to) {
            $this->sma->send_json(['msg' => $this->lang->line('no_meil_provided')]);
        }

        try {
            if ($this->sma->send_email($to, lang('receipt_from') . ' ' . $this->data['biller']->company, $receipt)) {
                $this->sma->send_json(['msg' => $this->lang->line('email_sent')]);
            } else {
                $this->sma->send_json(['msg' => $this->lang->line('email_failed')]);
            }
        } catch (Exception $e) {
            $this->sma->send_json(['msg' => $e->getMessage()]);
        }
    }

    public function get_printers()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->load->library('datatables');
        $this->datatables
        ->select('id, title, type, profile, path, ip_address, port')
        ->from('printers')
        ->add_column('Actions', "<div class='text-center'> <a href='" . admin_url('pos/edit_printer/$1') . "' class='btn-warning btn-xs tip' title='" . lang('edit_printer') . "'><i class='fa fa-edit'></i></a> <a href='#' class='btn-danger btn-xs tip po' title='<b>" . lang('delete_printer') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('pos/delete_printer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id')
        ->unset_column('id');
        echo $this->datatables->generate();
    }

    public function getProductDataByCode($code = null, $warehouse_id = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('code')) {
            $code = $this->input->get('code', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        if ($this->input->get('customer_id')) {
            $customer_id = $this->input->get('customer_id', true);
        }
        if (!$code) {
            echo null;
            die();
        }
        $warehouse      = $this->site->getWarehouseByID($warehouse_id);
        $customer       = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        $row            = $this->pos_model->getWHProduct($code, $warehouse_id);
        $option         = false;
        if ($row) {
            unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
            $row->item_tax_method = $row->tax_method;
            $row->qty             = 1;
            $row->discount        = '0';
            $row->serial          = '';
            $options              = $this->pos_model->getProductOptions($row->id, $warehouse_id);
            if ($options) {
                $opt = current($options);
                if (!$option) {
                    $option = $opt->id;
                }
            } else {
                $opt        = json_decode('{}');
                $opt->price = 0;
            }
            $row->option   = $option;
            $row->quantity = 0;
            $pis           = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
            if ($pis) {
                foreach ($pis as $pi) {
                    $row->quantity += $pi->quantity_balance;
                }
            }
            if ($row->type == 'standard' && (!$this->Settings->overselling && $row->quantity < 1)) {
                echo null;
                die();
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

            if ($this->sma->isPromo($row)) {
                $row->price = $row->promo_price;
            } elseif ($customer->price_group_id) {
                if ($pr_group_price = $this->site->getProductGroupPrice($row->id, $customer->price_group_id)) {
                    $row->price = $pr_group_price->price;
                }
            } elseif ($warehouse->price_group_id) {
                if ($pr_group_price = $this->site->getProductGroupPrice($row->id, $warehouse->price_group_id)) {
                    $row->price = $pr_group_price->price;
                }
            }
            $row->price           = $row->price + (($row->price * $customer_group->percent) / 100);
            $row->real_unit_price = $row->price;
            $row->base_quantity   = 1;
            $row->base_unit       = $row->unit;
            $row->base_unit_price = $row->price;
            $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
            $row->comment         = '';
            $combo_items          = false;
            if ($row->type == 'combo') {
                $combo_items = $this->pos_model->getProductComboItems($row->id, $warehouse_id);
            }
            $units    = $this->site->getUnitsByBUID($row->base_unit);
            $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

            $pr = ['id' => sha1(uniqid(mt_rand(), true)), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];

            $this->sma->send_json($pr);
        } else {
            echo null;
        }
    }

    public function getProductPromo($pId = null, $warehouse_id = null, $item_quantity = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('product_id')) {
            $pId = $this->input->get('product_id', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        if ($this->input->get('item_quantity')) {
            $item_quantity = $this->input->get('item_quantity', true);
        }
        $this->load->admin_model('promo_model');
        $promos = $this->promo_model->getPromosByProduct($pId,$warehouse_id);


        if ($promos) {
            foreach ($promos as $promo) {
                $warehouse = $this->site->getWarehouseByID($warehouse_id);
                $row       = $this->pos_model->getWHProductById($promo->free_product, $warehouse_id);
                $option    = false;
				$freeqty=floor($item_quantity/$promo->buy_quantity)*($promo->get_quantity);
				$intfreeqty=($item_quantity/$promo->buy_quantity)*($promo->get_quantity);
				if($freeqty==$intfreeqty)
					$freeqty=1;
				else
					$freeqty=0;

                if ($row && $freeqty>0) {
                    unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    $row->item_tax_method = $row->tax_method;
					$freeqty=floor($item_quantity/$promo->buy_quantity)*($promo->get_quantity);

                    $row->qty             = $freeqty;
                    $row->price           = 0;
                    $row->discount        = '0';
                    $row->serial          = '';
                    $options              = $this->pos_model->getProductOptions($row->id, $warehouse_id);
                    if ($options) {
                        $opt = current($options);
                        if (!$option) {
                            $option = $opt->id;
                        }
                    }
                    $row->option          = $option;
                    $row->real_unit_price = $row->price;
                    $row->base_quantity   = $freeqty;
                    $row->base_unit       = $row->unit;
                    $row->base_unit_price = $row->price;
                    $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
                    $row->comment         = '';
                    $combo_items          = false;
                    // if ($row->type == 'combo') {
                    //     $combo_items = $this->pos_model->getProductComboItems($row->id, $warehouse_id);
                    // }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = false; // $this->site->getTaxRateByID($row->tax_rate);

                    $pr = ['id' => sha1(uniqid(mt_rand(), true)), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];

                    $this->sma->send_json($pr);
                } else {
                    echo null;
                }
            }
        } else {
            echo null;
        }
    }


	public function getProductPromoRquantity($pId = null, $warehouse_id = null, $item_quantity = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('product_id')) {
            $pId = $this->input->get('product_id', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        if ($this->input->get('item_quantity')) {
            $item_quantity = $this->input->get('item_quantity', true);
        }
        $this->load->admin_model('promo_model');
        $promos = $this->promo_model->getPromosByProduct($pId,$warehouse_id);


        if ($promos) {
            foreach ($promos as $promo) {
                $warehouse = $this->site->getWarehouseByID($warehouse_id);
                $row       = $this->pos_model->getWHProductById($promo->free_product, $warehouse_id);
                $option    = false;
				$freeqty=floor($item_quantity/$promo->buy_quantity)*($promo->get_quantity);
				if($promo->free_product==$promo->offer_product)
				$freeqty=$freeqty + $item_quantity;


                if ($row) {
                    unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    $row->item_tax_method = $row->tax_method;
					//$freeqty=floor($item_quantity/$promo->buy_quantity)*($promo->get_quantity);

                    $row->qty             = $freeqty;
                    $row->price           = 0;
                    $row->discount        = '0';
                    $row->serial          = '';
                    $options              = $this->pos_model->getProductOptions($row->id, $warehouse_id);
                    if ($options) {
                        $opt = current($options);
                        if (!$option) {
                            $option = $opt->id;
                        }
                    }
                    $row->option          = $option;
                    $row->real_unit_price = $row->price;
                    $row->base_quantity   = $freeqty;
                    $row->base_unit       = $row->unit;
                    $row->base_unit_price = $row->price;
                    $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
                    $row->comment         = '';
                    $combo_items          = false;
                    // if ($row->type == 'combo') {
                    //     $combo_items = $this->pos_model->getProductComboItems($row->id, $warehouse_id);
                    // }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = false; // $this->site->getTaxRateByID($row->tax_rate);

                    $pr = ['id' => sha1(uniqid(mt_rand(), true)), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];

                    $this->sma->send_json($pr);
                } else {
                    echo null;
                }
            }
        } else {
            echo null;
        }
    }
    /*public function getcustomerinfo()
    {
        $customer_id=  $this->pos2_model->getCompanyByOfflineIDV2(14,'.عميل نقدي')->id ? $this->pos2_model->getCompanyByOfflineIDV2(14,'.عميل نقدي')->id : ($this->pos2_model->getCompanyByidIDV2(14,'.عميل نقدي')->id ? $this->pos2_model->getCompanyByidIDV2(14,'.عميل نقدي')->id : ($this->pos2_model->getCompanyByOfflineID(14)->id ? $this->pos2_model->getCompanyByOfflineID(14)->id : 14));
        $this->sma->print_arrays($customer_id);
    }*/

    public function getSales($warehouse_id = null)
    {
        $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
         //   $warehouse_id = $user->warehouse_id;
        }

		$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $reference_no    = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : null;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : null;
        $warehouse_id   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $sale_status   = $this->input->get('sale_status') ? $this->input->get('sale_status') : null;
        $payment_status   = $this->input->get('payment_status') ? $this->input->get('payment_status') : null;
        $salesman_id = $this->input->get('salesman_id') ? $this->input->get('salesman_id') : null;

		if ($start_date && $end_date) {
			$start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
		   }


        $duplicate_link    = anchor('admin/pos/?duplicate=$1', '<i class="fa fa-plus-square"></i> ' . lang('duplicate_sale'), 'class="duplicate_pos"');
        $detail_link       = anchor('admin/pos/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('view_receipt'));
        $detail_link2      = anchor('admin/sales/modal_view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details_modal'), 'data-toggle="modal" data-target="#myModal"');
        $detail_link3      = anchor('admin/sales/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details'));
        $payments_link     = anchor('admin/sales/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link  = anchor('admin/pos/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $packagink_link    = anchor('admin/sales/packaging/$1', '<i class="fa fa-archive"></i> ' . lang('packaging'), 'data-toggle="modal" data-target="#myModal"');
        $add_delivery_link = anchor('admin/sales/add_delivery/$1', '<i class="fa fa-truck"></i> ' . lang('add_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $email_link        = anchor('admin/#', '<i class="fa fa-envelope"></i> ' . lang('email_sale'), 'class="email_receipt" data-id="$1" data-email-address="$2"');
        $sms_whatsapp_link = anchor('admin/sales/sms_whatsapp/$1', '<i class="fa fa-send"></i> ' . lang('Send_by_sms_whatsapp'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link         = anchor('admin/sales/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_sale'), 'class="sledit"');
        $return_link       = anchor('admin/sales/return_sale/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_sale'));
        $debit_link       = anchor('admin/sales/debit_note?debit_id=$1', '<i class="fa fa-angle-double-right"></i> ' . lang('Debit_note'));
        $delete_link       = "<a href='#' class='po' title='<b>" . lang('delete_sale') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('sales/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_sale') . '</a>';

            if(isset($this->Settings->isSynchronizewithZATCA) && $this->Settings->isSynchronizewithZATCA==1)
              {
			$Synchronize_with_ZATCA_link  = anchor('admin/sales/SynchronizewithZATCA/$1', '<i class="fa fa-refresh"></i> ' . lang('Synchronize_with_ZATCA'), 'data-toggle="modal" data-target="#myModal"');
            $action = '<div class="text-center"><div class="btn-group text-left">'
                  . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
                  . lang('actions') . ' <span class="caret"></span></button>
                  <ul class="dropdown-menu pull-right" role="menu">
                      <li>' . $Synchronize_with_ZATCA_link . '</li>
                      <li>' . $detail_link2 . '</li>
                      <li>' . $detail_link3 . '</li>
                      <li>' . $payments_link . '</li>
                      <li>' . $add_payment_link . '</li>
                      <li>' . $packagink_link . '</li>
                      <li>' . $add_delivery_link . '</li>
                      <li>' . $email_link . '</li>
                      <li>' . $sms_whatsapp_link . '</li>
                      <li>' . $return_link . '</li>
                      <li>' . $debit_link . '</li>
                  </ul>
              </div></div>';

                }else{
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $detail_link2 . '</li>
                <li>' . $detail_link3 . '</li>
                <li>' . $payments_link . '</li>
                <li>' . $add_payment_link . '</li>
                <li>' . $packagink_link . '</li>
                <li>' . $add_delivery_link . '</li>
                <li>' . $email_link . '</li>
				<li>' . $sms_whatsapp_link . '</li>
                <li>' . $return_link . '</li>
                <li>' . $debit_link . '</li>
            </ul>
        </div></div>';
              }

		/*
		<li>' . $detail_link . '</li>

		<li>' . $duplicate_link . '</li>
        <li>' . $return_link . '</li>
        <li>' . $delete_link . '</li>
        <li>' . $edit_link . '</li>


		*/

        $this->load->library('datatables');
        if(isset($this->Settings->isSynchronizewithZATCA) && $this->Settings->isSynchronizewithZATCA==1)
            {
                if ($warehouse_id) {
                    $this->datatables
                        ->select($this->db->dbprefix('sales') . ".id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, grand_total, paid, discount_allowance, (grand_total-(paid+discount_allowance)) as balance, (CASE WHEN debit_id THEN 'debit_note' else (CASE WHEN credit_id THEN 'credit_note' else sale_status END) END) as sale_status, payment_status,synch")
                        ->from('sales')
                        //->join('companies', 'companies.id=sales.customer_id', 'left')
                        ->where('warehouse_id', $warehouse_id)
                        ->group_by('sales.id');
                } else {
                    $this->datatables
                     ->select("id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, grand_total, paid, discount_allowance, (grand_total-(paid+discount_allowance)) as balance, (CASE WHEN debit_id THEN 'debit_note' else (CASE WHEN credit_id THEN 'credit_note' else sale_status END) END) as sale_status, payment_status,synch")
                        ->from('sales')
                        //->join('companies', 'companies.id=sales.customer_id', 'left')
                        ->group_by('sales.id');
                }
            }else{
        if ($warehouse_id) {
            $this->datatables
                ->select($this->db->dbprefix('sales') . ".id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, grand_total, paid, discount_allowance, (grand_total-(paid+discount_allowance)) as balance, (CASE WHEN debit_id THEN 'debit_note' else (CASE WHEN credit_id THEN 'credit_note' else sale_status END) END) as sale_status, payment_status,0")
                ->from('sales')
                //->join('companies', 'companies.id=sales.customer_id', 'left')
                ->where('warehouse_id', $warehouse_id)
                ->group_by('sales.id');
        } else {
            $this->datatables
             ->select("id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, grand_total, paid, discount_allowance, (grand_total-(paid+discount_allowance)) as balance, (CASE WHEN debit_id THEN 'debit_note' else (CASE WHEN credit_id THEN 'credit_note' else sale_status END) END) as sale_status, payment_status,0")
                ->from('sales')
                //->join('companies', 'companies.id=sales.customer_id', 'left')
                ->group_by('sales.id');
        }
        }
        $this->datatables->where('pos', 1);
        /*if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Customer) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }
		*/

        if(!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }

		if ($start_date && $end_date) {
                 $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
           }

            if ($reference_no) {
                $this->datatables->where($this->db->dbprefix('sales') . '.reference_no', $reference_no);
            }
            if ($customer) {
                $this->datatables->where($this->db->dbprefix('sales') . '.customer_id', $customer);
            }
            if ($biller) {
                $this->datatables->where($this->db->dbprefix('sales') . '.biller_id', $biller);
            }
            if ($salesman_id) {
                $this->datatables->where($this->db->dbprefix('sales') . '.salesman_id', $salesman_id);
            }
              if ($sale_status) {
                $this->datatables->where($this->db->dbprefix('sales') . '.sale_status', $sale_status);
            }
             if ($payment_status) {
                $this->datatables->where($this->db->dbprefix('sales') . '.payment_status', $payment_status);
            }




        //$this->datatables->add_column('Actions', $action, 'id, cemail')->unset_column('cemail');
		$this->datatables->add_column('Actions', $action, 'id');

        echo $this->datatables->generate();
    }

    /* ---------------------------------------------------------------------------------------------------- */

    public function getWarehouse_VAT($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->pos_model->getWarehouse_VATByID($id);
		$this->sma->send_json($row);
    }


	public function index2()
    {


		$this->pos2_model->syncQuantity(18);
		$this->pos2_model->syncQuantity(16);
		$this->pos2_model->syncQuantity(15);
		$this->pos2_model->syncQuantity(14);
		$this->pos2_model->syncQuantity(13);
		$this->pos2_model->syncQuantity(11);
		$this->pos2_model->syncQuantity(10);
		$this->pos2_model->syncQuantity(8);
		$this->pos2_model->syncQuantity(7);
		$this->pos2_model->syncQuantity(6);
		$this->pos2_model->syncQuantity(5);
		$this->pos2_model->syncQuantity(2);
		$this->pos2_model->syncQuantity(1);

		/*$sales=$this->pos_model->getToatalSales('3','2021-12-16 20:50:04','2021-12-16 23:50:04');
		$paymentcash=$this->pos_model->getToatalSalesPayment('3','cash','2021-12-16 20:50:04','2021-12-16 23:50:04');
		$paymentCC=$this->pos_model->getToatalSalesPayment('3','CC','2021-12-16 20:50:04','2021-12-16 23:50:04');

			$this->sma->print_arrays($paymentcash,$paymentCC);

		$arrrs=$this->pos2_model->getInvoicebyOffline_Pos_id2();
		foreach ($arrrs as $arrr) {
        if($arrr->count>4){
		//$this->sma->print_arrays($arrr->Offline_Pos_id);

		$arrs=$this->pos2_model->getInvoicebyOffline_Pos_id($arrr->Offline_Pos_id);

		$temp=0;
		foreach ($arrs as $arr) {
             if($temp!=0){

        $inv = $this->sales_model->getInvoiceByID($arr->id);
		//$this->sma->print_arrays($inv);
        if ($inv->sale_status == 'returned') {
           // $this->sma->send_json(['error' => 1, 'msg' => lang('sale_x_action')]);
			//$this->session->set_flashdata('warning', lang('sale_x_action'));

        }

        if ($this->sales_model->deleteSale($arr->id)) {
            //if ($this->input->is_ajax_request()) {
                //$this->sma->send_json(['error' => 0, 'msg' => lang('sale_deleted')]);
				//$this->session->set_flashdata('warning', lang('sale_deleted'));

            //}
            //$this->session->set_flashdata('message', lang('sale_deleted'));
            //admin_redirect('welcome');
        }
			 }
			 $temp++;
		}
		}
		}*/
	}

	public function index1($sid = null, $return_sale = null)
    {
        if($return_sale)
		$this->sma->checkPermissions('return');
	    else
		$this->sma->checkPermissions('index');




        if (!$this->pos_settings->default_biller || !$this->pos_settings->default_customer || !$this->pos_settings->default_category) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            admin_redirect('pos/settings');
        }

        if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
            $register_data = ['register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date];
            $this->session->set_userdata($register_data);
        } else {
            $this->session->set_flashdata('error', lang('register_not_open'));
            admin_redirect('pos/open_register');
        }

        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;
        $did               = $this->input->post('delete_id') ? $this->input->post('delete_id') : null;
        $suspend           = $this->input->post('suspend') ? true : false;
        $count             = $this->input->post('count') ? $this->input->post('count') : null;

        $duplicate_sale = $this->input->get('duplicate') ? $this->input->get('duplicate') : null;
		//$return_sale = $this->input->get('return_sale') ? $this->input->get('return_sale') : null;


        //validate form input
		if(!$return_sale){
        $this->form_validation->set_rules('customer', $this->lang->line('customer'), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line('warehouse'), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line('biller'), 'required');
		}

        if ($this->form_validation->run() == true) {

            $date             = date('Y-m-d H:i:s');
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $total_items      = $this->input->post('total_items');
            $sale_status      = 'completed';
            $payment_term     = 0;
            $due_date         = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = $customer_details->company && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = $biller_details->company && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note             = $this->sma->clear_tags($this->input->post('pos_note'));
            $staff_note       = $this->sma->clear_tags($this->input->post('staff_note'));
            $reference        = $this->site->getReference('pos');

            $gtotal            = 0;
			$total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $digital          = false;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;

			if($this->input->post('discount')>0){
				for ($r = 0; $r < $i; $r++) {
					 $item_code          = $_POST['product_code'][$r];
					 $item_type          = $_POST['product_type'][$r];
                $product_details = $item_type != 'manual' ? $this->pos_model->getProductByCode($item_code) : null;

				if($product_details->tax_method != 1)
					$gtotal=$gtotal+($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r]);
				else
					$gtotal=$gtotal+($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r]);

				}
			}

            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $item_comment       = $_POST['product_comment'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);
				$net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);

                $item_unit_quantity = $_POST['quantity'][$r];
				if($freePromo=$this->sales_model->getBuyGetFreePromoByPId($item_id,$warehouse_id)){
				$item_unit_quantity = $_POST['product_base_quantity'][$r];


				}
                $item_serial        = $_POST['serial'][$r]           ?$_POST['serial'][$r] : '';
                $item_tax_rate      = $_POST['product_tax'][$r]      ? $_POST['product_tax'][$r] : null;
                $item_discount      = $_POST['product_discount'][$r] ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = $_POST['product_base_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->pos_model->getProductByCode($item_code) : null;
                    // $unit_price = $real_unit_price;
                    if ($item_type == 'digital') {
                        $digital = true;
                    }

					if($gtotal>0){
						$order_product_discount = $this->site->calculateDiscount($this->input->post('discount'), ($gtotal));

					 if($product_details->tax_method != 1)
						$item_discount=$this->sma->formatDecimal((($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					else
						$item_discount=$this->sma->formatDecimal((($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					}




                   //$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                    if($product_details->tax_method != 1)
					$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
					else
						$pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);


					//$this->sma->print_arrays($_POST['unit_price'][$r]);


					$unit_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);
                    $item_net_price   = $net_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
						if($product_details->tax_method != 1)
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                        else
						$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $net_price;
                        }else{
							//$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
							$item_net_price=$net_price;
							//$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        //$item_tax    = $ctax['amount'];
                        //$tax         = $ctax['tax'];



						}
						//
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                    $unit     = $this->site->getUnitByID($item_unit);

                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($unit_price,12),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $unit ? $unit->id : null,
                        'product_unit_code' => $unit ? $unit->code : null,
                        'unit_quantity'     => $item_unit_quantity,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $this->sma->formatDecimal($subtotal),
                        'serial_no'         => $item_serial,
                        'real_unit_price'   => $real_unit_price,
                        'comment'           => $item_comment,
						'promo_free'   => $unit_price!=0 ? 0 : 1,
                    ];

                    $products[] = ($product + $gst_data);


					if($freePromo=$this->sales_model->getBuyGetFreePromoByPId($item_id,$warehouse_id)){

						if(isset($freePromo->start_date)){
							$free_date = date('Y-m-d');
							$free_date=date('Y-m-d', strtotime($free_date));

							$contractDateBegin = date('Y-m-d', strtotime($freePromo->start_date));
							$contractDateEnd = date('Y-m-d', strtotime($freePromo->end_date));

							if (($free_date >= $contractDateBegin) && ($free_date <= $contractDateEnd)){

							}else{
							unset($freePromo);
							}

						}

						$freeqty=floor($item_quantity/$freePromo->buy_quantity)*($freePromo->get_quantity);

						if($freeqty>0){

						}else{
						unset($freePromo);
						}

						if($freePromo){
							$fp=$this->site->getProductByID($freePromo->free_product);
							$unit_free     = $this->site->getUnitByID($fp->unit);

						$product_free = [
                        'product_id'        => $freePromo->free_product,
                        'product_code'      => $fp->code,
                        'product_name'      => $fp->name,
                        'product_type'      => $fp->type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => 0,
                        'unit_price'        => 0,
                        'quantity'          => $freeqty,
                        'product_unit_id'   => $unit_free ? $unit_free->id : null,
                        'product_unit_code' => $unit_free ? $unit_free->code : null,
                        'unit_quantity'     => $freeqty,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => 0,
                        'tax_rate_id'       => 0,
                        'tax'               => '',
                        'discount'          => '',
                        'item_discount'     => 0,
                        'subtotal'          => 0,
                        'serial_no'         => '',
                        'real_unit_price'   => 0,
                        'promo_free'   => 1,
                    ];



						$products[] = ($product_free + $gst_data);
						//$this->sma->print_arrays($freePromo);
						//$this->sma->print_arrays($product_free);
						}
					}


					//$this->sma->print_arrays($products);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
					$orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);

                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } elseif ($this->pos_settings->item_order == 0) {
                krsort($products);
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('discount'), ($total + $product_tax));
            if($gtotal>0)
			$order_discount=0;
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            //$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            if($gtotal>0)
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping)), 2);
            else
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 2);

			$rounding       = 0;
            if ($this->pos_settings->rounding) {
                $round_total = $this->sma->roundNumber($grand_total, $this->pos_settings->rounding);
                $rounding    = $this->sma->formatMoney($round_total - $grand_total);
            }
            $data = ['date'         => $date,
                'reference_no'      => $reference,
                'customer_id'       => $customer_id,
                'customer'          => $customer,
                'biller_id'         => $biller_id,
                'biller'            => $biller,
                'warehouse_id'      => $warehouse_id,
                'note'              => $note,
                'staff_note'        => $staff_note,
                'total'             => $orginaltotal,
                'product_discount'  => $product_discount,
                'order_discount_id' => $this->input->post('order_discount'),
                'order_discount'    => $order_discount,
                'total_discount'    => $total_discount,
                'product_tax'       => $product_tax,
                'order_tax_id'      => $this->input->post('order_tax'),
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'shipping'          => $this->sma->formatDecimal($shipping),
                'grand_total'       => $grand_total,
                'total_items'       => $total_items,
                'sale_status'       => $sale_status,
                'payment_status'    => $grand_total > 0 ? 'due' : 'paid',
                'payment_term'      => $payment_term,
                'rounding'          => $rounding,
                'suspend_note'      => $this->input->post('suspend_note'),
                'pos'               => 1,
                'paid'              => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
                'created_by'        => $this->session->userdata('user_id'),
                'hash'              => hash('sha256', microtime() . mt_rand()),
                'order_type'      => $this->input->post('order_type'),
				'supply_date'          => $date,
				'uuid'                => $this->site->getUniqeUUID()->UUID,

                ];
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if (!$suspend) {


                $p    = isset($_POST['amount']) ? sizeof($_POST['amount']) : 0;
				$paid = 0;
                for ($r = 0; $r < $p; $r++) {
                    if (isset($_POST['amount'][$r]) && !empty($_POST['amount'][$r]) && isset($_POST['paid_by_'.($r+1)]) && !empty($_POST['paid_by_'.($r+1)])) {
                        $amount = $this->sma->formatDecimal($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);
                        if ($_POST['paid_by_'.($r+1)] == 'deposit') {
                            if (!$this->site->check_customer_deposit($customer_id, $amount)) {
                                $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }

						if($_POST['paid_by_'.($r+1)]=='Mada'){

							$_POST['paid_by_'.($r+1)]="CC";
							$_POST['cc_type'][$r]="Mada";
						}
                        if($_POST['paid_by_'.($r+1)]=='Visa'){

							$_POST['paid_by_'.($r+1)]="CC";
							$_POST['cc_type'][$r]="Visa";
						}
                        if($_POST['paid_by_'.($r+1)]=='MasterCard'){

							$_POST['paid_by_'.($r+1)]="CC";
							$_POST['cc_type'][$r]="MasterCard";
						}
                        if($_POST['paid_by_'.($r+1)]=='STC Pay'){

							$_POST['paid_by_'.($r+1)]="CC";
							$_POST['cc_type'][$r]="STC Pay";
						}
                        if ($_POST['paid_by_'.($r+1)] == 'gift_card') {
                            $gc            = $this->site->getGiftCardByNO($_POST['paying_gift_card_no'][$r]);
                            $amount_paying = $_POST['amount'][$r] >= $gc->balance ? $gc->balance : $_POST['amount'][$r];
                            $gc_balance    = $gc->balance - $amount_paying;
                            $payment[]     = [
                                'date' => $date,
                                // 'reference_no' => $this->site->getReference('pay'),
                                'amount'      => $amount,
                                'paid_by'     => $_POST['paid_by_'.($r+1)],
                                'cheque_no'   => $_POST['cheque_no'][$r],
                                'cc_no'       => $_POST['paying_gift_card_no'][$r],
                                'cc_holder'   => $_POST['cc_holder'][$r],
                                'cc_month'    => $_POST['cc_month'][$r],
                                'cc_year'     => $_POST['cc_year'][$r],
                                'cc_type'     => $_POST['cc_type'][$r],
                                'cc_cvv2'     => $_POST['cc_cvv2'][$r],
                                'created_by'  => $this->session->userdata('user_id'),
                                'type'        => 'received',
                                'note'        => $_POST['payment_note'][$r],
                                'pos_paid'    => $_POST['amount'][$r],
                                'pos_balance' => $_POST['balance_amount'][$r],
                                'gc_balance'  => $gc_balance,
                            ];
                        } else {
                            $payment[] = [
                                'date' => $date,
                                // 'reference_no' => $this->site->getReference('pay'),
                                'amount'      => $amount,
                                'paid_by'     => $_POST['paid_by_'.($r+1)],
                                'cheque_no'   => $_POST['cheque_no'][$r],
                                'cc_no'       => $_POST['cc_no'][$r],
                                'cc_holder'   => $_POST['cc_holder'][$r],
                                'cc_month'    => $_POST['cc_month'][$r],
                                'cc_year'     => $_POST['cc_year'][$r],
                                'cc_type'     => $_POST['cc_type'][$r],
                                'cc_cvv2'     => $_POST['cc_cvv2'][$r],
                                'created_by'  => $this->session->userdata('user_id'),
                                'type'        => 'received',
                                'note'        => $_POST['payment_note'][$r],
                                'pos_paid'    => $_POST['amount'][$r],
                                'pos_balance' => $_POST['balance_amount'][$r],
                            ];
                        }
                    }
                }
            }
            if (!isset($payment) || empty($payment)) {
                $payment = [];
            }
			//$this->site->syncProductQty('135');

             //$this->sma->print_arrays($data, $products, $payment);
			 //     $this->sma->print_arrays($this->site->costing($products));

        }

        if ($this->form_validation->run() == true && !empty($products) && !empty($data)) {
            if ($suspend) {
                if ($this->pos_model->suspendSale($data, $products, $did)) {
                    $this->session->set_userdata('remove_posls', 1);
                    $this->session->set_flashdata('message', $this->lang->line('sale_suspended'));
                    admin_redirect('pos');
                }
            } else {

                if ($sale = $this->pos_model->addSale($data, $products, $payment, $did)) {
                    $this->session->set_userdata('remove_posls', 1);
                    $msg = $this->lang->line('sale_added');
                    if (!empty($sale['message'])) {
                        foreach ($sale['message'] as $m) {
                            $msg .= '<br>' . $m;
                        }
                    }
                    $this->session->set_flashdata('message', $msg);
                    $redirect_to = $this->pos_settings->after_sale_page ? 'pos' : 'pos/view_slip/' . $sale['sale_id'];
                    if ($this->pos_settings->auto_print) {
                        if ($this->Settings->remote_printing != 1) {
                            $redirect_to .= '?print=' . $sale['sale_id'];
                        }
                    }
                    admin_redirect($redirect_to);
                }
            }
        } else {
            $this->data['old_sale'] = null;
            $this->data['oid']      = null;
            if ($duplicate_sale) {
                if ($old_sale = $this->pos_model->getInvoiceByID($duplicate_sale)) {
                    $inv_items              = $this->pos_model->getSaleItems($duplicate_sale);
                    $this->data['oid']      = $duplicate_sale;
                    $this->data['old_sale'] = $old_sale;
                    $this->data['message']  = lang('old_sale_loaded');
                    $this->data['customer'] = $this->pos_model->getCompanyByID($old_sale->customer_id);
                } else {
                    $this->session->set_flashdata('error', lang('bill_x_found'));
                    admin_redirect('pos');
                }
            }
			$this->data['return_sale'] = null;
            $this->data['oid']      = null;
            if ($return_sale) {
                if ($old_sale = $this->pos_model->getInvoiceByID($return_sale)) {
                    $inv_items              = $this->pos_model->getSaleItems($return_sale);
                    $this->data['rid']      = $return_sale;
                    $this->data['return_sale'] = $old_sale;
                    $this->data['message']  = lang('return_sale_loaded');
                    $this->data['customer'] = $this->pos_model->getCompanyByID($old_sale->customer_id);
                } else {
                    $this->session->set_flashdata('error', lang('bill_x_found'));
                    admin_redirect('pos');
                }
            }

            $this->data['suspend_sale'] = null;
            if ($sid) {
                if ($suspended_sale = $this->pos_model->getOpenBillByID($sid)) {
                    $inv_items                    = $this->pos_model->getSuspendedSaleItems($sid);
                    $this->data['sid']            = $sid;
                    $this->data['suspend_sale']   = $suspended_sale;
                    $this->data['message']        = lang('suspended_sale_loaded');
                    $this->data['customer']       = $this->pos_model->getCompanyByID($suspended_sale->customer_id);
                    $this->data['reference_note'] = $suspended_sale->suspend_note;
                } else {
                    $this->session->set_flashdata('error', lang('bill_x_found'));
                    admin_redirect('pos');
                }
            }

            if (($sid || $duplicate_sale || $return_sale) && $inv_items) {
                // krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row             = json_decode('{}');
                        $row->tax_method = 0;
                        $row->quantity   = 0;
                    } else {
                        $category           = $this->site->getCategoryByID($row->category_id);
                        $row->category_name = $category->name;
                        unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    }
                    $pis = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                    if ($pis) {
                        foreach ($pis as $pi) {
                            $row->quantity += $pi->quantity_balance;
                        }
                    }
                    $row->id   = $item->product_id;
                    $row->sale_item_id    = $item->id;
					$row->code = $item->product_code;
                    $row->name = $item->product_name;
                    $row->type = $item->product_type;
                    $row->quantity += $item->quantity;
                    $row->discount        = $item->discount ? $item->discount : '0';
                    $row->price           = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                    $row->unit_price      = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                    $row->real_unit_price = $item->real_unit_price;
                    $row->base_quantity   = $item->quantity;
                    $row->base_unit       = $row->unit ? $row->unit : $item->product_unit_id;
                    // $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                    $row->base_unit_price = $item->real_unit_price;
                    $row->unit            = $item->product_unit_id;
                    $row->qty             = $item->unit_quantity;
                    $row->oqty            = $item->unit_quantity;
					$row->tax_rate        = $item->tax_rate_id;
                    $row->serial          = $item->serial_no;
                    $row->option          = $item->option_id;
                    $options              = $this->pos_model->getProductOptions($row->id, $item->warehouse_id);

                    if ($options) {
                        $option_quantity = 0;
                        foreach ($options as $option) {
                            $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
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

                    $row->comment = isset($item->comment) ? $item->comment : '';
                    $row->ordered = 1;
                    $combo_items  = false;
                    if ($row->type == 'combo') {
                        $combo_items = $this->pos_model->getProductComboItems($row->id, $item->warehouse_id);
                    }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $ri       = $this->Settings->item_addition ? $row->id : $c;

                    $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                        'row'        => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                    $c++;
                }

                $this->data['items'] = json_encode($pr);
            // $this->sma->print_arrays($this->data['items']);
            } else {
                $this->data['customer']       = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
                $this->data['reference_note'] = null;
            }

            $this->data['error']   = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

            // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
            $this->data['billers']       = $this->site->getAllCompanies('biller');
            $this->data['warehouses']    = $this->site->getAllWarehouses();
            $this->data['tax_rates']     = $this->site->getAllTaxRates();
            $this->data['user']          = $this->site->getUser();
            $this->data['tcp']           = $this->pos_model->products_count($this->pos_settings->default_category);
            $this->data['products']      = $this->ajaxproducts($this->pos_settings->default_category);
            $this->data['categories']    = $this->site->getAllCategoriesN0HidePOS();
            $this->data['brands']        = $this->site->getAllBrands();
            $this->data['subcategories'] = $this->site->getSubCategories($this->pos_settings->default_category);
            $this->data['printer']       = $this->pos_model->getPrinterByID($this->pos_settings->printer);
            $order_printers              = json_decode($this->pos_settings->order_printers);
            $printers                    = [];
            if (!empty($order_printers)) {
                foreach ($order_printers as $printer_id) {
                    $printers[] = $this->pos_model->getPrinterByID($printer_id);
                }
            }
            $this->data['order_printers'] = $printers;
            $this->data['pos_settings']   = $this->pos_settings;

            if ($this->pos_settings->after_sale_page && $saleid = $this->input->get('print', true)) {
                if ($inv = $this->pos_model->getInvoiceByID($saleid)) {
                    $this->load->helper('pos');
                    if (!$this->session->userdata('view_right')) {
                        $this->sma->view_rights($inv->created_by, true);
                    }
                    $this->data['rows']            = $this->pos_model->getAllInvoiceItems($inv->id);
                    $this->data['biller']          = $this->pos_model->getCompanyByID($inv->biller_id);
                    $this->data['customer']        = $this->pos_model->getCompanyByID($inv->customer_id);
                    $this->data['payments']        = $this->pos_model->getInvoicePayments($inv->id);
                    $this->data['return_sale']     = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
                    $this->data['return_rows']     = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
                    $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
                    $this->data['inv']             = $inv;
                    $this->data['print']           = $inv->id;
                    $this->data['created_by']      = $this->site->getUser($inv->created_by);
                }
            }



            if($return_sale)
				$this->load->view($this->theme . 'pos/return', $this->data);
			else
				$this->load->view($this->theme . 'pos/add', $this->data);

        }
    }





	public function return_pos($sid = null)
    {
        $this->sma->checkPermissions();

        if (!$this->pos_settings->default_biller || !$this->pos_settings->default_customer || !$this->pos_settings->default_category) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            admin_redirect('pos/settings');
        }
        if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
            $register_data = ['register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date];
            $this->session->set_userdata($register_data);
        } else {
            $this->session->set_flashdata('error', lang('register_not_open'));
            admin_redirect('pos/open_register');
        }

        $this->data['sid'] = $this->input->get('suspend_id') ? $this->input->get('suspend_id') : $sid;
        $did               = $this->input->post('delete_id') ? $this->input->post('delete_id') : null;
        $suspend           = $this->input->post('suspend') ? true : false;
        $count             = $this->input->post('count') ? $this->input->post('count') : null;

        $duplicate_sale = $this->input->get('duplicate') ? $this->input->get('duplicate') : null;
		$return_sale = $this->input->post('return_sale') ? $this->input->post('return_sale') : null;

		$pos = $this->pos_model->getInvoiceByID($return_sale);
        if ($pos->return_id) {
            $this->session->set_flashdata('error', lang('sale_already_returned'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        //validate form input
        $this->form_validation->set_rules('customer', $this->lang->line('customer'), 'trim|required');
        $this->form_validation->set_rules('warehouse', $this->lang->line('warehouse'), 'required');
        $this->form_validation->set_rules('biller', $this->lang->line('biller'), 'required');

        if ($this->form_validation->run() == true) {

            $date             = date('Y-m-d H:i:s');
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $total_items      = $this->input->post('total_items');
            $sale_status      = 'returned';
            $payment_term     = 0;
            $due_date         = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            /*
			$customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = $customer_details->company && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = $biller_details->company && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            */
			$customer_details = $this->site->getCompanyByID($pos->customer_id);
            $biller_details   = $this->site->getCompanyByID($pos->biller_id);

			$note             = $this->sma->clear_tags($this->input->post('pos_note'));
            $staff_note       = $this->sma->clear_tags($this->input->post('staff_note'));
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('re');

            $gtotal            = 0;
			$total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $digital          = false;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
			/*
			if($this->input->post('discount')>0){
				for ($r = 0; $r < $i; $r++) {
				$gtotal=$gtotal+($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r]);

				}
			}
            */
            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $item_comment       = $_POST['product_comment'][$r];
				$sale_item_id       = $_POST['sale_item_id'][$r];

                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);
                $net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);

                $item_unit_quantity = (0-$_POST['quantity'][$r]);
                $item_serial        = $_POST['serial'][$r]           ?$_POST['serial'][$r] : '';
                $item_tax_rate      = $_POST['product_tax'][$r]      ? $_POST['product_tax'][$r] : null;
                $item_discount      = $_POST['product_discount'][$r] ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = (0-$_POST['product_base_quantity'][$r]);

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->pos_model->getProductByCode($item_code) : null;
                    // $unit_price = $real_unit_price;
                    if ($item_type == 'digital') {
                        $digital = true;
                    }

					/*if($gtotal>0){
						$order_product_discount = $this->site->calculateDiscount($this->input->post('discount'), ($gtotal));

						$item_discount=$this->sma->formatDecimal((($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					}*/




                    //$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);

					if($product_details->tax_method != 1)
					$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
					else
						$pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);

					/*
                    $unit_price       = $this->sma->formatDecimal($unit_price - $pr_discount);
                    $item_net_price   = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $product_discount += $pr_item_discount;


					*/
					//$unit_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);

					$item_net_price   = $net_price;


					$pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                    $product_discount += $pr_item_discount;


					/*
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';


                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $unit_price - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);
                    $unit     = $this->site->getUnitByID($item_unit);

					*/

					$pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
						if($product_details->tax_method != 1)
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                        else{
						$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $unit_price=$real_unit_price;
						}
						$item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $net_price;
                        }else{
							//$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
							$item_net_price=$net_price;
							//$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        //$item_tax    = $ctax['amount'];
                        //$tax         = $ctax['tax'];



						}
						//
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                    $unit     = $this->site->getUnitByID($item_unit);


                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($unit_price,12),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $unit ? $unit->id : null,
                        'product_unit_code' => $unit ? $unit->code : null,
                        'unit_quantity'     => $item_unit_quantity,
                        'warehouse_id'      => $pos->warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $this->sma->formatDecimal($subtotal),
                        'serial_no'         => $item_serial,
                        'real_unit_price'   => $real_unit_price,
                        'comment'           => $item_comment,
						'sale_item_id'      => $sale_item_id,
                    ];

					$si_return[] = [
                        'id'           => $sale_item_id,
                        'sale_id'      => $id,
                        'product_id'   => $item_id,
                        'option_id'    => $item_option,
                        'quantity'     => (0 - $item_quantity),
                        'warehouse_id' => $pos->warehouse_id,
                    ];


                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
					$orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);

                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } elseif ($this->pos_settings->item_order == 0) {
                krsort($products);
            }

            /*$order_discount = (0 - $this->site->calculateDiscount($this->input->post('discount'), ($total + $product_tax)));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            //$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal(0-$shipping) - $order_discount), 4);
			*/


			if($shipping!=0){
			$return_sale_ex_ref=$this->site->getReference('ex');


				//$this->sma->print_arrays($total_shipping);

                $date = date('Y-m-d H:i:s');

            $data_ex = [
                'date'         => $date,
                'reference'    => $return_sale_ex_ref,
                'amount'       => abs($shipping),
                'grand_total'       => abs($shipping),
                'total'       => abs($shipping),
				'payment_status'       => 'paid',
				'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $pos->warehouse_id,
			    'other' => 1,
			];
			if($this->purchases_model->addExpense($data_ex)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}
			}

			$order_discount = (0 - $this->site->calculateDiscount($this->input->post('discount'), ($total + $product_tax)));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 12);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($return_surcharge) + (0 - $shipping) - $order_discount), 12);


			$rounding       = 0;
            if ($this->pos_settings->rounding) {
                $round_total = $this->sma->roundNumber($grand_total, $this->pos_settings->rounding);
                $rounding    = $this->sma->formatMoney($round_total - $grand_total);
            }
            $data = ['date'         => $date,
                'sale_id'           => $return_sale,
                'reference_no'      => $pos->reference_no,
                'customer_id'       => $pos->customer_id,
                'customer'          => $pos->customer,
                'biller_id'         => $pos->biller_id,
                'biller'            => $pos->biller,
                'warehouse_id'      => $pos->warehouse_id,
                'note'              => $note,
                'staff_note'        => $staff_note,
                'total'             => $orginaltotal,
                'product_discount'  => $product_discount,
                'order_discount_id' => $this->input->post('discount') ? $this->input->post('order_discount') : null,
                'order_discount'    => $order_discount,
                'total_discount'    => $total_discount,
                'product_tax'       => $product_tax,
                'order_tax_id'      => $this->input->post('order_tax'),
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'shipping'          => $this->sma->formatDecimal($shipping),
                'grand_total'       => $grand_total,
                'total_items'       => $total_items,
                'sale_status'       => $sale_status,
                'payment_status'    => $pos->payment_status == 'paid' ? 'due' : 'pending',
                'rounding'          => $rounding,
                'suspend_note'      => $this->input->post('suspend_note'),
                'pos'               => 1,
                'return_sale_ref'   => $reference,
                'paid'              => $this->input->post('amount-paid') ? $this->input->post('amount-paid') : 0,
                'created_by'        => $this->session->userdata('user_id'),
                'hash'              => hash('sha256', microtime() . mt_rand()),
				'order_type'      => $pos->order_type,
				'supply_date'          => $date,


            ];
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if (!$suspend) {


                $p    = isset($_POST['amount']) ? sizeof($_POST['amount']) : 0;
				$paid = 0;
                for ($r = 0; $r < $p; $r++) {
                    if (isset($_POST['amount'][$r]) && !empty($_POST['amount'][$r]) && isset($_POST['paid_by_'.($r+1)]) && !empty($_POST['paid_by_'.($r+1)])) {
                        $amount = $this->sma->formatDecimal($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);
                        if ($_POST['paid_by_'.($r+1)] == 'deposit') {
                            if (!$this->site->check_customer_deposit($customer_id, $amount)) {
                                $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }

						if($_POST['paid_by_'.($r+1)]=='Mada'){

							$_POST['paid_by_'.($r+1)]="CC";
							$_POST['cc_type'][$r]="Mada";
						}
                        if($_POST['paid_by_'.($r+1)]=='Visa'){

							$_POST['paid_by_'.($r+1)]="CC";
							$_POST['cc_type'][$r]="Visa";
						}
                        if($_POST['paid_by_'.($r+1)]=='MasterCard'){

							$_POST['paid_by_'.($r+1)]="CC";
							$_POST['cc_type'][$r]="MasterCard";
						}
                        if ($_POST['paid_by_'.($r+1)] == 'gift_card') {
                            $gc            = $this->site->getGiftCardByNO($_POST['paying_gift_card_no'][$r]);
                            $amount_paying = $_POST['amount'][$r] >= $gc->balance ? $gc->balance : $_POST['amount'][$r];
                            $gc_balance    = $gc->balance - $amount_paying;
                            $payment[]     = [
                                'date' => $date,
                                // 'reference_no' => $this->site->getReference('pay'),
                                'amount'      => (0-$amount),
                                'paid_by'     => $_POST['paid_by_'.($r+1)],
                                'cheque_no'   => $_POST['cheque_no'][$r],
                                'cc_no'       => $_POST['paying_gift_card_no'][$r],
                                'cc_holder'   => $_POST['cc_holder'][$r],
                                'cc_month'    => $_POST['cc_month'][$r],
                                'cc_year'     => $_POST['cc_year'][$r],
                                'cc_type'     => $_POST['cc_type'][$r],
                                'cc_cvv2'     => $_POST['cc_cvv2'][$r],
                                'created_by'  => $this->session->userdata('user_id'),
                                'type'        => 'received',
                                'note'        => $_POST['payment_note'][$r],
                                'pos_paid'    => $_POST['amount'][$r],
                                'pos_balance' => $_POST['balance_amount'][$r],
                                'gc_balance'  => $gc_balance,
                            ];
                        } else {
                            $payment[] = [
                                'date' => $date,
                                // 'reference_no' => $this->site->getReference('pay'),
                                'amount'      => (0-$amount),
                                'paid_by'     => $_POST['paid_by_'.($r+1)],
                                'cheque_no'   => $_POST['cheque_no'][$r],
                                'cc_no'       => $_POST['cc_no'][$r],
                                'cc_holder'   => $_POST['cc_holder'][$r],
                                'cc_month'    => $_POST['cc_month'][$r],
                                'cc_year'     => $_POST['cc_year'][$r],
                                'cc_type'     => $_POST['cc_type'][$r],
                                'cc_cvv2'     => $_POST['cc_cvv2'][$r],
                                'created_by'  => $this->session->userdata('user_id'),
                                'type'        => 'received',
                                'note'        => $_POST['payment_note'][$r],
                                'pos_paid'    => $_POST['amount'][$r],
                                'pos_balance' => $_POST['balance_amount'][$r],
                            ];
                        }
                    }
                }
            }
            if (!isset($payment) || empty($payment)) {
                $payment = [];
            }

			//$this->sma->print_arrays($this->pos_model->get_return_sale_item_id('1181','235')->id);

             //$this->sma->print_arrays($data, $products, $payment, $si_return);
        }

        if ($this->form_validation->run() == true && !empty($products) && !empty($data)) {
            if ($suspend) {
                if ($this->pos_model->suspendSale($data, $products, $did)) {
                    $this->session->set_userdata('remove_posls', 1);
                    $this->session->set_flashdata('message', $this->lang->line('sale_suspended'));
                    admin_redirect('pos');
                }
            } else {
                if ($sale = $this->pos_model->addSale($data, $products, $payment, $did, $si_return)) {
                    $this->session->set_userdata('remove_posls', 1);
                    $msg = $this->lang->line('sale_added');
                    if (!empty($sale['message'])) {
                        foreach ($sale['message'] as $m) {
                            $msg .= '<br>' . $m;
                        }
                    }
                    $this->session->set_flashdata('message', $msg);
                    $redirect_to = $this->pos_settings->after_sale_page ? 'pos' : 'pos/view_slip/' . $sale['sale_id'];
                    if ($this->pos_settings->auto_print) {
                        if ($this->Settings->remote_printing != 1) {
                            $redirect_to .= '?print=' . $sale['sale_id'];
                        }
                    }
                    admin_redirect($redirect_to);
                }
            }
        } else {

            $inv_items              = $this->pos_model->getSaleItems($return_sale);


            if (($sid || $duplicate_sale || $return_sale) && $inv_items) {
                // krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row             = json_decode('{}');
                        $row->tax_method = 0;
                        $row->quantity   = 0;
                    } else {
                        $category           = $this->site->getCategoryByID($row->category_id);
                        $row->category_name = $category->name;
                        unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    }
                    $pis = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                    if ($pis) {
                        foreach ($pis as $pi) {
                            $row->quantity += $pi->quantity_balance;
                        }
                    }
                    $row->id   = $item->product_id;
					$row->sale_item_id    = $item->id;

                    $row->code = $item->product_code;
                    $row->name = $item->product_name;
                    $row->type = $item->product_type;
                    $row->quantity += $item->quantity;
                    $row->discount        = $item->discount ? $item->discount : '0';
                    $row->price           = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                    $row->unit_price      = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                    $row->real_unit_price = $item->real_unit_price;
                    $row->base_quantity   = $item->quantity;
                    $row->base_unit       = $row->unit ? $row->unit : $item->product_unit_id;
                    // $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                    $row->base_unit_price = $item->real_unit_price;
                    $row->unit            = $item->product_unit_id;
                    $row->qty             = $item->unit_quantity;
                    $row->oqty            = $item->unit_quantity;
					$row->tax_rate        = $item->tax_rate_id;
                    $row->serial          = $item->serial_no;
                    $row->option          = $item->option_id;
                    $options              = $this->pos_model->getProductOptions($row->id, $item->warehouse_id);

                    if ($options) {
                        $option_quantity = 0;
                        foreach ($options as $option) {
                            $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
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

                    $row->comment = isset($item->comment) ? $item->comment : '';
                    $row->ordered = 1;
                    $combo_items  = false;
                    if ($row->type == 'combo') {
                        $combo_items = $this->pos_model->getProductComboItems($row->id, $item->warehouse_id);
                    }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $ri       = $this->Settings->item_addition ? $row->id : $c;

                    $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                        'row'        => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                    $c++;
                }

                $this->data['items'] = json_encode($pr);
            // $this->sma->print_arrays($this->data['items']);
            } else {
                $this->data['customer']       = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
                $this->data['reference_note'] = null;
            }

            $this->data['error']   = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');

            // $this->data['biller'] = $this->site->getCompanyByID($this->pos_settings->default_biller);
            $this->data['billers']       = $this->site->getAllCompanies('biller');
            $this->data['warehouses']    = $this->site->getAllWarehouses();
            $this->data['tax_rates']     = $this->site->getAllTaxRates();
            $this->data['user']          = $this->site->getUser();
            $this->data['tcp']           = $this->pos_model->products_count($this->pos_settings->default_category);
            $this->data['products']      = $this->ajaxproducts($this->pos_settings->default_category);
            $this->data['categories']    = $this->site->getAllCategoriesN0HidePOS();
            $this->data['brands']        = $this->site->getAllBrands();
            $this->data['subcategories'] = $this->site->getSubCategories($this->pos_settings->default_category);
            $this->data['printer']       = $this->pos_model->getPrinterByID($this->pos_settings->printer);
            $order_printers              = json_decode($this->pos_settings->order_printers);
            $printers                    = [];
            if (!empty($order_printers)) {
                foreach ($order_printers as $printer_id) {
                    $printers[] = $this->pos_model->getPrinterByID($printer_id);
                }
            }
            $this->data['order_printers'] = $printers;
            $this->data['pos_settings']   = $this->pos_settings;

            if ($this->pos_settings->after_sale_page && $saleid = $this->input->get('print', true)) {
                if ($inv = $this->pos_model->getInvoiceByID($saleid)) {
                    $this->load->helper('pos');
                    if (!$this->session->userdata('view_right')) {
                        $this->sma->view_rights($inv->created_by, true);
                    }
                    $this->data['rows']            = $this->pos_model->getAllInvoiceItems($inv->id);
                    $this->data['biller']          = $this->pos_model->getCompanyByID($inv->biller_id);
                    $this->data['customer']        = $this->pos_model->getCompanyByID($inv->customer_id);
                    $this->data['payments']        = $this->pos_model->getInvoicePayments($inv->id);
                    $this->data['return_sale']     = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
                    $this->data['return_rows']     = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
                    $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
                    $this->data['inv']             = $inv;
                    $this->data['print']           = $inv->id;
                    $this->data['created_by']      = $this->site->getUser($inv->created_by);
                }
            }



            /*if($return_sale)
				$this->load->view($this->theme . 'pos/return', $this->data);
			else*/
				$this->load->view($this->theme . 'pos/add', $this->data);

        }
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
                admin_redirect('pos/updates');
            }
        }
        $this->db->update('pos_settings', ['version' => $version], ['pos_id' => 1]);
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        admin_redirect('pos/updates');
    }

    public function open_drawer()
    {
        $data = json_decode($this->input->get('data'));
        $this->load->library('escpos');
        $this->escpos->load($data->printer);
        $this->escpos->open_drawer();
    }

    public function open_register()
    {
        $this->sma->checkPermissions('index');
        $this->form_validation->set_rules('cash_in_hand', lang('cash_in_hand'), 'trim|required|numeric');
        if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
            $register_data = ['register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date];
            $this->session->set_userdata($register_data);
            admin_redirect('pos');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'date'         => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id'      => $this->session->userdata('user_id'),
                'status'       => 'open',
            ];
        }
        if ($this->form_validation->run() == true && $this->pos_model->openRegister($data)) {
            $this->session->set_flashdata('message', lang('welcome_to_pos'));
            admin_redirect('pos');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('open_register')]];
            $meta                = ['page_title' => lang('open_register'), 'bc' => $bc];
            $this->page_construct('pos/open_register', $meta, $this->data);
        }
    }

    public function opened_bills($per_page = 0)
    {
        $this->load->library('pagination');

        //$this->table->set_heading('Id', 'The Title', 'The Content');
        if ($this->input->get('per_page')) {
            $per_page = $this->input->get('per_page');
        }

        $config['base_url']   = admin_url('pos/opened_bills');
        $config['total_rows'] = $this->pos_model->bills_count();
        $config['per_page']   = 6;
        $config['num_links']  = 3;

        $config['full_tag_open']   = '<ul class="pagination pagination-sm">';
        $config['full_tag_close']  = '</ul>';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li class="active"><a>';
        $config['cur_tag_close']   = '</a></li>';

        $this->pagination->initialize($config);
        $data['r'] = true;
        $bills     = $this->pos_model->fetch_bills($config['per_page'], $per_page);
        if (!empty($bills)) {
            $html = '';
            $html .= '<ul class="ob">';
            foreach ($bills as $bill) {
                $html .= '<li><button type="button" class="btn btn-info sus_sale" id="' . $bill->id . '"><p>' . $bill->suspend_note . '</p><strong>' . $bill->customer . '</strong><br>' . lang('date') . ': ' . $bill->date . '<br>' . lang('items') . ': ' . $bill->count . '<br>' . lang('total') . ': ' . $this->sma->formatMoney($bill->total) . '</button></li>';
            }
            $html .= '</ul>';
        } else {
            $html      = '<h3>' . lang('no_opeded_bill') . '</h3><p>&nbsp;</p>';
            $data['r'] = false;
        }

        $data['html'] = $html;

        $data['page'] = $this->pagination->create_links();
        echo $this->load->view($this->theme . 'pos/opened', $data, true);
    }

    public function opened_return($per_page = 0)
    {
        $this->load->library('pagination');

        //$this->table->set_heading('Id', 'The Title', 'The Content');
        if ($this->input->get('per_page')) {
            $per_page = $this->input->get('per_page');
        }

        $config['base_url']   = admin_url('pos/opened_return');
        $config['total_rows'] = $this->pos_model->bills_count();
        $config['per_page']   = 6;
        $config['num_links']  = 3;

        $config['full_tag_open']   = '<ul class="pagination pagination-sm">';
        $config['full_tag_close']  = '</ul>';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li class="active"><a>';
        $config['cur_tag_close']   = '</a></li>';

        $this->pagination->initialize($config);
        $data['r'] = true;
        $bills     = $this->pos_model->fetch_bills($config['per_page'], $per_page);
        if (!empty($bills)) {
            $html = '';
            $html .= '<ul class="ob">';
            foreach ($bills as $bill) {
                $html .= '<li><button type="button" class="btn btn-info sus_sale" id="' . $bill->id . '"><p>' . $bill->suspend_note . '</p><strong>' . $bill->customer . '</strong><br>' . lang('date') . ': ' . $bill->date . '<br>' . lang('items') . ': ' . $bill->count . '<br>' . lang('total') . ': ' . $this->sma->formatMoney($bill->total) . '</button></li>';
            }
            $html .= '</ul>';
        } else {
            $html      = '<h3>' . lang('no_opeded_bill') . '</h3><p>&nbsp;</p>';
            $data['r'] = false;
        }


		$this->data['pos_settings']   = $this->pos_settings;




        $data['html'] = $html;

        $data['page'] = $this->pagination->create_links();
        echo $this->load->view($this->theme . 'pos/opened_return', $data, true);
    }



    public function p()
    {
        $data = json_decode($this->input->get('data'));
        $this->load->library('escpos');
        $this->escpos->load($data->printer);
        $this->escpos->print_receipt($data);
    }

    public function paypal_balance()
    {
        if (!$this->Owner) {
            return false;
        }
        $this->load->admin_model('paypal_payments');

        return $this->paypal_payments->get_balance();
    }

    public function printers()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('pos');
        }
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('printers');
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => '#', 'page' => lang('printers')]];
        $meta                     = ['page_title' => lang('list_printers'), 'bc' => $bc];
        $this->page_construct('pos/printers', $meta, $this->data);
    }

    public function register_details()
    {
        $this->sma->checkPermissions('index');
        $register_open_time           = $this->session->userdata('register_open_time');
        $this->data['error']          = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales']        = $this->pos_model->getRegisterCCSales($register_open_time);
        $this->data['cashsales']      = $this->pos_model->getRegisterCashSales($register_open_time);
        $this->data['chsales']        = $this->pos_model->getRegisterChSales($register_open_time);
        $this->data['gcsales']        = $this->pos_model->getRegisterGCSales($register_open_time);
        $this->data['pppsales']       = $this->pos_model->getRegisterPPPSales($register_open_time);
        $this->data['stripesales']    = $this->pos_model->getRegisterStripeSales($register_open_time);
        $this->data['authorizesales'] = $this->pos_model->getRegisterAuthorizeSales($register_open_time);
        $this->data['totalsales']     = $this->pos_model->getRegisterSales($register_open_time);
        $this->data['refunds']        = $this->pos_model->getRegisterRefunds($register_open_time);
        $this->data['returns']        = $this->pos_model->getRegisterReturns($register_open_time);
        $this->data['expenses']       = $this->pos_model->getRegisterExpenses($register_open_time);
        $this->load->view($this->theme . 'pos/register_details', $this->data);
    }

    public function registers()
    {
        $this->sma->checkPermissions();

        $this->data['error']     = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['registers'] = $this->pos_model->getOpenRegisters();
        $bc                      = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => '#', 'page' => lang('open_registers')]];
        $meta                    = ['page_title' => lang('open_registers'), 'bc' => $bc];
        $this->page_construct('pos/registers', $meta, $this->data);
    }

    public function sales($warehouse_id = null)
    {
        $this->sma->checkPermissions('index');
		$warehouse_id   = $this->input->post('warehouse') ? $this->input->post('warehouse') : null;


        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $user                       = $this->site->getUser();
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }
		$this->data['billers']    = $this->site->getAllCompanies('biller');
        $this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();


        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => '#', 'page' => lang('pos_sales')]];
        $meta = ['page_title' => lang('pos_sales'), 'bc' => $bc];
        $this->page_construct('pos/sales', $meta, $this->data);
    }

    public function settings()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('pro_limit', $this->lang->line('pro_limit'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('pin_code', $this->lang->line('delete_code'), 'numeric');
        $this->form_validation->set_rules('report_pin_code', $this->lang->line('Report_pin_code'), 'numeric');
        $this->form_validation->set_rules('category', $this->lang->line('default_category'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('customer', $this->lang->line('default_customer'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('biller', $this->lang->line('default_biller'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('warehouse_receipt_print_copy_count', lang('Number_of_copies_of_the_warehouse_receipt'), 'required|numeric|max_length[3]');

        if ($this->form_validation->run() == true) {
            $data = [
                'pro_limit'                 => $this->input->post('pro_limit'),
                'pin_code'                  => $this->input->post('pin_code') ? $this->input->post('pin_code') : null,
                'report_pin_code'                  => $this->input->post('report_pin_code') ? $this->input->post('report_pin_code') : null,
                'slip_pin_code'                  => $this->input->post('slip_pin_code') ? $this->input->post('slip_pin_code') : null,
                'default_category'          => $this->input->post('category'),
                'default_customer'          => $this->input->post('customer'),
                'default_biller'            => $this->input->post('biller'),
                'display_time'              => $this->input->post('display_time'),
                'receipt_printer'           => $this->input->post('receipt_printer'),
                'cash_drawer_codes'         => $this->input->post('cash_drawer_codes'),
                'cf_title1'                 => $this->input->post('cf_title1'),
                'cf_title2'                 => $this->input->post('cf_title2'),
                'cf_value1'                 => $this->input->post('cf_value1'),
                'cf_value2'                 => $this->input->post('cf_value2'),
                'focus_add_item'            => $this->input->post('focus_add_item'),
                'add_manual_product'        => $this->input->post('add_manual_product'),
                'customer_selection'        => $this->input->post('customer_selection'),
                'add_customer'              => $this->input->post('add_customer'),
                'toggle_category_slider'    => $this->input->post('toggle_category_slider'),
                'toggle_subcategory_slider' => $this->input->post('toggle_subcategory_slider'),
                'toggle_brands_slider'      => $this->input->post('toggle_brands_slider'),
                'cancel_sale'               => $this->input->post('cancel_sale'),
                'suspend_sale'              => $this->input->post('suspend_sale'),
                'print_items_list'          => $this->input->post('print_items_list'),
                'finalize_sale'             => $this->input->post('finalize_sale'),
                'today_sale'                => $this->input->post('today_sale'),
                'open_hold_bills'           => $this->input->post('open_hold_bills'),
                'close_register'            => $this->input->post('close_register'),
                'tooltips'                  => $this->input->post('tooltips'),
                'keyboard'                  => $this->input->post('keyboard'),
                'pos_printers'              => $this->input->post('pos_printers'),
                'java_applet'               => $this->input->post('enable_java_applet'),
                'product_button_color'      => $this->input->post('product_button_color'),
                'paypal_pro'                => $this->input->post('paypal_pro'),
                'stripe'                    => $this->input->post('stripe'),
                'authorize'                 => $this->input->post('authorize'),
                'rounding'                  => $this->input->post('rounding'),
                'item_order'                => $this->input->post('item_order'),
                'after_sale_page'           => $this->input->post('after_sale_page'),
                'printer'                   => $this->input->post('receipt_printer'),
                'order_printers'            => json_encode($this->input->post('order_printers')),
                'auto_print'                => $this->input->post('auto_print'),
                'remote_printing'           => DEMO ? 1 : $this->input->post('remote_printing'),
                'customer_details'          => $this->input->post('customer_details'),
                'local_printers'            => $this->input->post('local_printers'),
                'tax_method_type'            => $this->input->post('tax_method_type'),
                'order_type'            => $this->input->post('order_type'),
				'categories_tab'      => $this->input->post('categories_tab'),
            	'pos_reciept_a4_page_size'      => $this->input->post('pos_reciept_a4_page_size'),
            	'pos_screen_style'      => $this->input->post('pos_screen_style'),
                'screen_style'      => $this->input->post('screen_style'),
                'RestInsurance'      => $this->input->post('RestInsurance'),
                'AnyUserCanDeleteFromBasket'      => $this->input->post('AnyUserCanDeleteFromBasket'),
                'DeleteFromBaskerPinCode'      => $this->input->post('DeleteFromBaskerPinCode'),
                'AnyUserCanPlusMinusFromBasket'      => $this->input->post('AnyUserCanPlusMinusFromBasket'),
                'ShowShippingInSales'      => $this->input->post('ShowShippingInSales'),
                'ShowInsuranceInSales'      => $this->input->post('ShowInsuranceInSales'),
                'searchbyitemname'      => $this->input->post('searchbyitemname'),
                'Opensetting'      => $this->input->post('Opensetting'),
                'Opentodaysales'      => $this->input->post('Opentodaysales'),
                'OpenExpensiveView'      => $this->input->post('OpenExpensiveView'),
                'OpentQuotesView'      => $this->input->post('OpentQuotesView'),
                'OpentCouponsInSales'      => $this->input->post('OpentCouponsInSales'),
                'show_product_code_on_invoice'      => $this->input->post('show_product_code_on_invoice'),
                'send_receipt_to_warehouse'      => $this->input->post('send_receipt_to_warehouse'),
                'warehouse_receipt_print_copy_count'      => $this->input->post('warehouse_receipt_print_copy_count'),
                'expenses_pin_code'      => $this->input->post('expenses_pin_code'),
                'collections_pin_code'      => $this->input->post('collections_pin_code'),
                'add_product_pin_code'      => $this->input->post('add_product_pin_code'),
                'customers_pin_code'      => $this->input->post('customers_pin_code'),
                'nfc_enable'      => $this->input->post('nfc_enable'),
                'product_name_display'      => $this->input->post('product_name_display'),

            ];
            $payment_config = [
                'APIUsername'            => $this->input->post('APIUsername'),
                'APIPassword'            => $this->input->post('APIPassword'),
                'APISignature'           => $this->input->post('APISignature'),
                'stripe_secret_key'      => $this->input->post('stripe_secret_key'),
                'stripe_publishable_key' => $this->input->post('stripe_publishable_key'),
                'api_login_id'           => $this->input->post('api_login_id'),
                'api_transaction_key'    => $this->input->post('api_transaction_key'),
            ];
        } elseif ($this->input->post('update_settings')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('pos/settings');
        }

        if ($this->form_validation->run() == true && $this->pos_model->updateSetting($data)) {
            if (DEMO) {
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect('pos/settings');
            }
            if ($this->write_payments_config($payment_config)) {
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect('pos/settings');
            } else {
                $this->session->set_flashdata('error', $this->lang->line('pos_setting_updated_payment_failed'));
                admin_redirect('pos/settings');
            }
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['pos']        = $this->pos_model->getSetting();
            $this->data['categories'] = $this->site->getAllCategoriesN0HidePOS();
            //$this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
            $this->data['billers'] = $this->pos_model->getAllBillerCompanies();
            $this->config->load('payment_gateways');
            $this->data['stripe_secret_key']      = $this->config->item('stripe_secret_key');
            $this->data['stripe_publishable_key'] = $this->config->item('stripe_publishable_key');
            $authorize                            = $this->config->item('authorize');
            $this->data['api_login_id']           = $authorize['api_login_id'];
            $this->data['api_transaction_key']    = $authorize['api_transaction_key'];
            $this->data['APIUsername']            = $this->config->item('APIUsername');
            $this->data['APIPassword']            = $this->config->item('APIPassword');
            $this->data['APISignature']           = $this->config->item('APISignature');
            $this->data['printers']               = $this->pos_model->getAllPrinters();
            $this->data['paypal_balance']         = null; // $this->pos_settings->paypal_pro ? $this->paypal_balance() : NULL;
            $this->data['stripe_balance']         = null; // $this->pos_settings->stripe ? $this->stripe_balance() : NULL;
            $bc                                   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('pos_settings')]];
            $meta                                 = ['page_title' => lang('pos_settings'), 'bc' => $bc];
            $this->page_construct('pos/settings', $meta, $this->data);
        }
    }

    public function save_pos_configuration()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('pro_limit', $this->lang->line('pro_limit'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('pin_code', $this->lang->line('delete_code'), 'numeric');
        $this->form_validation->set_rules('report_pin_code', $this->lang->line('Report_pin_code'), 'numeric');
        $this->form_validation->set_rules('category', $this->lang->line('default_category'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('customer', $this->lang->line('default_customer'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('biller', $this->lang->line('default_biller'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('warehouse_receipt_print_copy_count', lang('Number_of_copies_of_the_warehouse_receipt'), 'required|numeric|max_length[3]');

        if ($this->form_validation->run() == true) {
            $data = [
                'pro_limit'                          => $this->input->post('pro_limit'),
                'pin_code'                           => $this->input->post('pin_code') ? $this->input->post('pin_code') : null,
                'report_pin_code'                    => $this->input->post('report_pin_code') ? $this->input->post('report_pin_code') : null,
                'slip_pin_code'                      => $this->input->post('slip_pin_code') ? $this->input->post('slip_pin_code') : null,
                'default_category'                   => $this->input->post('category'),
                'default_customer'                   => $this->input->post('customer'),
                'default_biller'                     => $this->input->post('biller'),
                'display_time'                       => $this->input->post('display_time'),
                'tooltips'                           => $this->input->post('tooltips'),
                'keyboard'                           => $this->input->post('keyboard'),
                'product_button_color'               => $this->input->post('product_button_color'),
                'paypal_pro'                         => $this->input->post('paypal_pro'),
                'stripe'                             => $this->input->post('stripe'),
                'authorize'                          => $this->input->post('authorize'),
                'rounding'                           => $this->input->post('rounding'),
                'item_order'                         => $this->input->post('item_order'),
                'after_sale_page'                    => $this->input->post('after_sale_page'),
                'customer_details'                   => $this->input->post('customer_details'),
                'tax_method_type'                    => $this->input->post('tax_method_type'),
                'order_type'                         => $this->input->post('order_type'),
                'categories_tab'                     => $this->input->post('categories_tab'),
                'pos_reciept_a4_page_size'           => $this->input->post('pos_reciept_a4_page_size'),
                'pos_screen_style'                   => $this->input->post('pos_screen_style'),
                'screen_style'                       => $this->input->post('screen_style'),
                'RestInsurance'                      => $this->input->post('RestInsurance'),
                'AnyUserCanDeleteFromBasket'         => $this->input->post('AnyUserCanDeleteFromBasket'),
                'DeleteFromBaskerPinCode'            => $this->input->post('DeleteFromBaskerPinCode'),
                'AnyUserCanPlusMinusFromBasket'      => $this->input->post('AnyUserCanPlusMinusFromBasket'),
                'ShowShippingInSales'                => $this->input->post('ShowShippingInSales'),
                'ShowInsuranceInSales'               => $this->input->post('ShowInsuranceInSales'),
                'searchbyitemname'                   => $this->input->post('searchbyitemname'),
                'Opensetting'                        => $this->input->post('Opensetting'),
                'Opentodaysales'                     => $this->input->post('Opentodaysales'),
                'OpenExpensiveView'                  => $this->input->post('OpenExpensiveView'),
                'OpentQuotesView'                    => $this->input->post('OpentQuotesView'),
                'OpentCouponsInSales'                => $this->input->post('OpentCouponsInSales'),
                'show_product_code_on_invoice'       => $this->input->post('show_product_code_on_invoice'),
                'send_receipt_to_warehouse'          => $this->input->post('send_receipt_to_warehouse'),
                'warehouse_receipt_print_copy_count' => $this->input->post('warehouse_receipt_print_copy_count'),
                'expenses_pin_code'                  => $this->input->post('expenses_pin_code'),
                'collections_pin_code'               => $this->input->post('collections_pin_code'),
                'add_product_pin_code'               => $this->input->post('add_product_pin_code'),
                'customers_pin_code'                 => $this->input->post('customers_pin_code'),
                'nfc_enable'                         => $this->input->post('nfc_enable'),
                'product_name_display'               => $this->input->post('product_name_display'),
            ];

            $payment_config = [
                'APIUsername'            => $this->input->post('APIUsername'),
                'APIPassword'            => $this->input->post('APIPassword'),
                'APISignature'           => $this->input->post('APISignature'),
                'stripe_secret_key'      => $this->input->post('stripe_secret_key'),
                'stripe_publishable_key' => $this->input->post('stripe_publishable_key'),
                'api_login_id'           => $this->input->post('api_login_id'),
                'api_transaction_key'    => $this->input->post('api_transaction_key'),
            ];

            if ($this->pos_model->updateSetting($data)) {
                if (!DEMO && ($this->input->post('paypal_pro') || $this->input->post('stripe') || $this->input->post('authorize'))) {
                    $this->write_payments_config($payment_config);
                }
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
            } else {
                $this->session->set_flashdata('error', lang('error_occurred'));
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        admin_redirect('pos/settings');
    }

    public function save_pos_printing()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }

        $data = [
            'remote_printing'   => DEMO ? 1 : $this->input->post('remote_printing'),
            'auto_print'        => $this->input->post('auto_print'),
            'local_printers'    => $this->input->post('local_printers'),
            'printer'           => $this->input->post('receipt_printer'),
            'receipt_printer'   => $this->input->post('receipt_printer'),
            'order_printers'    => json_encode($this->input->post('order_printers')),
            'cash_drawer_codes' => $this->input->post('cash_drawer_codes'),
            'pos_printers'      => $this->input->post('pos_printers'),
            'java_applet'       => $this->input->post('enable_java_applet'),
        ];

        if ($this->pos_model->updateSetting($data)) {
            $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
        } else {
            $this->session->set_flashdata('error', lang('error_occurred'));
        }
        admin_redirect('pos/settings');
    }

    public function save_receiving_fields()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }

        $data = [
            'cf_title1' => $this->input->post('cf_title1'),
            'cf_value1' => $this->input->post('cf_value1'),
            'cf_title2' => $this->input->post('cf_title2'),
            'cf_value2' => $this->input->post('cf_value2'),
        ];

        if ($this->pos_model->updateSetting($data)) {
            $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
        } else {
            $this->session->set_flashdata('error', lang('error_occurred'));
        }
        admin_redirect('pos/settings');
    }

    public function save_pos_shortcuts()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }

        $data = [
            'focus_add_item'            => $this->input->post('focus_add_item'),
            'add_manual_product'        => $this->input->post('add_manual_product'),
            'customer_selection'        => $this->input->post('customer_selection'),
            'add_customer'              => $this->input->post('add_customer'),
            'toggle_category_slider'    => $this->input->post('toggle_category_slider'),
            'toggle_subcategory_slider' => $this->input->post('toggle_subcategory_slider'),
            'toggle_brands_slider'      => $this->input->post('toggle_brands_slider'),
            'cancel_sale'               => $this->input->post('cancel_sale'),
            'suspend_sale'              => $this->input->post('suspend_sale'),
            'print_items_list'          => $this->input->post('print_items_list'),
            'finalize_sale'             => $this->input->post('finalize_sale'),
            'today_sale'                => $this->input->post('today_sale'),
            'open_hold_bills'           => $this->input->post('open_hold_bills'),
            'close_register'            => $this->input->post('close_register'),
        ];

        if ($this->pos_model->updateSetting($data)) {
            $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
        } else {
            $this->session->set_flashdata('error', lang('error_occurred'));
        }
        admin_redirect('pos/settings');
    }


public function return_check()
    {
    /*$this->form_validation->set_rules('pin_code', $this->lang->line('delete_code'), 'numeric');
      if ($this->form_validation->run() == true) {
            $data = [
                'reference_no'                 => $this->input->post('reference_no'),
                'pin_code'                  => $this->input->post('pin_code') ? $this->input->post('pin_code') : null,

            ];


        } elseif ($this->input->post('update_settings')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
		*/

		//$id=$this->pos_model->getInvoiceIDByReference($reference_no)->id;
			$reference_no=$this->input->post('reference_no', true);
			//$this->sma->print_arrays($reference_no);

        if ($id=$this->pos_model->getInvoiceIDByReference($reference_no)->id) {
                //$this->sma->print_arrays($id);

                $this->index('',$id);

        } else {
            $this->session->set_flashdata('message', $this->lang->line('bill_x_found'));

             redirect($_SERVER['HTTP_REFERER']);
        }



	}

    public function stripe_balance()
    {
        if (!$this->Owner) {
            return false;
        }
        $this->load->admin_model('stripe_payments');

        return $this->stripe_payments->get_balance();
    }

    public function today_sale()
    {
        if (!$this->Owner && !$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['error']          = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales']        = $this->pos_model->getTodayCCSales();
        $this->data['cashsales']      = $this->pos_model->getTodayCashSales();
        $this->data['chsales']        = $this->pos_model->getTodayChSales();
        $this->data['pppsales']       = $this->pos_model->getTodayPPPSales();
        $this->data['stripesales']    = $this->pos_model->getTodayStripeSales();
        $this->data['authorizesales'] = $this->pos_model->getTodayAuthorizeSales();
        $this->data['totalsales_by_catogries']      = $this->pos_model->getRegisterSalesByCatogries($register_open_time, $user_id);
        $this->data['totalsales']     = $this->pos_model->getTodaySales();
        $this->data['refunds']        = $this->pos_model->getTodayRefunds();
        $this->data['returns']        = $this->pos_model->getTodayReturns();
        $this->data['expenses']       = $this->pos_model->getTodayExpenses();
        $this->load->view($this->theme . 'pos/today_sale', $this->data);
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
            $this->db->update('pos_settings', ['purchase_code' => $this->input->post('purchase_code', true), 'envato_username' => $this->input->post('envato_username', true)], ['pos_id' => 1]);
            admin_redirect('pos/updates');
        } else {
            $fields = ['version' => $this->pos_settings->version, 'code' => $this->pos_settings->purchase_code, 'username' => $this->pos_settings->envato_username, 'site' => base_url()];
            $this->load->helper('update');
            $protocol              = is_https() ? 'https://' : 'http://';
            $updates               = get_remote_contents($protocol . 'api.tecdiary.com/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('updates')]];
            $meta                  = ['page_title' => lang('updates'), 'bc' => $bc];
            $this->page_construct('pos/updates', $meta, $this->data);
        }
    }

    /* ------------------------------------------------------------------------------------ */

    public function view($sale_id = null, $modal = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('id')) {
            $sale_id = $this->input->get('id');
        }
        $this->load->helper('pos');
        $this->data['error']   = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv                   = $this->pos_model->getInvoiceByID($sale_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows']            = $this->pos_model->getAllInvoiceItems($sale_id);
        $biller_id                     = $inv->biller_id;
        $customer_id                   = $inv->customer_id;
        $this->data['biller']          = $this->pos_model->getCompanyByID($biller_id);
        $this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['customer']        = $this->pos2_model->getCompanyByOfflineIDV2($customer_id,$inv->customer) ? $this->pos2_model->getCompanyByOfflineIDV2($customer_id,$inv->customer) : ($this->pos2_model->getCompanyByidIDV2($customer_id,$inv->customer) ? $this->pos2_model->getCompanyByidIDV2($customer_id,$inv->customer) : ($this->pos2_model->getCompanyByOfflineID($customer_id) ? $this->pos2_model->getCompanyByOfflineID($customer_id) : $this->pos_model->getCompanyByID($customer_id)));//$this->pos_model->getCompanyByOfflineID($customer_id) ? $this->pos_model->getCompanyByOfflineID($customer_id) : $this->pos_model->getCompanyByID($customer_id);
        $this->data['payments']        = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['pos']             = $this->pos_model->getSetting();
        $this->data['barcode']         = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['return_sale']     = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
        $this->data['debit_sale'] = $inv->return_debit_id ? $this->sales_model->getInvoiceByID($inv->return_debit_id) : null;
        $this->data['debit_rows']     = $inv->return_debit_id ? $this->sales_model->getAllsaleItemsDebit_idWithOutZeros($id) : null;
		$this->data['inv']             = $inv;
        $this->data['sid']             = $sale_id;
        $this->data['modal']           = $modal;
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['printer']         = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['page_title']      = $this->lang->line('invoice');
		$this->data['active_tax']        =$this->sales_model->getTaxRatesActive()->rate;

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
		{
			$this->data['branch'] = $this->site->getMainBranchesByID($inv->brId);
		}

        if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $this->Settings->isDisplayCustomersAccountOnInvoice==1)
		{
			$this->data['accountID'] = $this->site->getAccountByRefId($inv->customer_id,'local_customers') ? $this->site->getAccountByRefId($inv->customer_id,'local_customers')->accountID :($this->site->getAccountByRefId($inv->customer_id,'international_customers') ? $this->site->getAccountByRefId($inv->customer_id,'international_customers')->accountID : null);
		}

        if(isset($this->Settings->isSynchronizewithZATCA) && $this->Settings->isSynchronizewithZATCA==1 && $inv->synch==1)
        {
            $this->data['invQR']        = $this->sales_model->getInvoiceDocBySaleID($sale_id)->qr;
        }

		if($inv->isTobacco==1){
            $this->load->view($this->theme . 'sales/modal_view_tobacco', $this->data);
         }else if($this->Settings->IsCurtains){
            $this->load->view($this->theme . 'sales/modal_view_2', $this->data);
         }else{
         if(str_replace("cpesoft_","",$this->db->database)=="hamoud" || str_replace("cpesoft_","",$this->db->database)=="kan"){
            $this->load->view($this->theme . 'sales/modal_view_2_zatca', $this->data);
        }else if($this->Settings->invoice_view==2)
			$this->load->view($this->theme . 'pos/modal_view', $this->data);
        else if($this->Settings->invoice_view==14)
			$this->load->view($this->theme . 'sales/modal_view_14', $this->data);
        else if($this->Settings->invoice_view==15)
			$this->load->view($this->theme . 'sales/modal_view_15', $this->data);
        else
			$this->load->view($this->theme . 'pos/modal_view_2', $this->data);
         }

    }

    public function view_slip($sale_id = null, $modal = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('id')) {
            $sale_id = $this->input->get('id');
        }
        $this->load->helper('pos');
        $this->data['error']   = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv                   = $this->pos_model->getInvoiceByID($sale_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows']            = $this->pos_model->getAllInvoiceItems($sale_id);
        $biller_id                     = $inv->biller_id;
        $customer_id                   = $inv->customer_id;
        $this->data['biller']          = $this->pos_model->getCompanyByID($biller_id);
        $this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['customer']        = $this->pos_model->getCompanyByID($customer_id);
        $this->data['payments']        = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['pos']             = $this->pos_model->getSetting();
        $this->data['barcode']         = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['return_sale']     = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
        $this->data['inv']             = $inv;
        $this->data['sid']             = $sale_id;
        $this->data['modal']           = $modal;
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['printer']         = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['active_tax']        =$this->sales_model->getTaxRatesActive()->rate;

        $order_printers              = json_decode($this->pos_settings->order_printers);
            $printers                    = [];
            if (!empty($order_printers)) {
                foreach ($order_printers as $printer_id) {
                    $printers[] = $this->pos_model->getPrinterByID($printer_id);
                }
            }
            $this->data['order_printers'] = $printers;

		$this->data['page_title']      = $this->lang->line('invoice');
        $this->load->view($this->theme . 'pos/view2', $this->data);
    }

    public function view_bill()
    {
        $this->sma->checkPermissions('index');
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->load->view($this->theme . 'pos/view_bill', $this->data);
    }

    public function write_payments_config($config)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        if (DEMO) {
            return true;
        }
        $file_contents = file_get_contents('./assets/config_dumps/payment_gateways.php');
        $output_path   = APPPATH . 'config/payment_gateways.php';
        $this->load->library('parser');
        $parse_data = [
            'APIUsername'            => $config['APIUsername'],
            'APIPassword'            => $config['APIPassword'],
            'APISignature'           => $config['APISignature'],
            'stripe_secret_key'      => $config['stripe_secret_key'],
            'stripe_publishable_key' => $config['stripe_publishable_key'],
            'api_login_id'           => $config['api_login_id'],
            'api_transaction_key'    => $config['api_transaction_key'],
        ];
        $new_config = $this->parser->parse_string($file_contents, $parse_data);

        $handle = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable($output_path)) {
            if (fwrite($handle, $new_config)) {
                @chmod($output_path, 0644);
                return true;
            }
            @chmod($output_path, 0644);
            return false;
        }
        @chmod($output_path, 0644);
        return false;
    }

    public function setCloseEntry($id)
    {
        $data_close=$this->pos2_model->getRegisterByID($id);



			$rid = $data_close->id;





			if ($this->pos2_model->getSystemSetting()->accountingOffline)// || $dbname ="accountstest" || $dbname ="buthaina")
			{
			$entry_type="Close_biller_register";



			$sales=$this->pos2_model->getToatalSalesPOS($this->pos2_model->getUser($data_close->user_id)->biller_id,'completed',$data_close->date,$data_close->closed_at);
			$re_sales=$this->pos2_model->getToatalSalesPOS($this->pos2_model->getUser($data_close->user_id)->biller_id,'returned',$data_close->date,$data_close->closed_at);

			$entryDebitAccount= [];
			$entryDebitBalance= [];

            $entryDebitTotal=0;

			$entryCreditAccount= [];
			$entryCreditBalance= [];
            $entryCreditTotal=0;


			$customersAcc= [];
			$customersBal= [];



			$paymentcash=$this->pos2_model->getToatalSalesPaymentPOS($this->pos2_model->getUser($data_close->user_id)->biller_id,'completed','cash',$data_close->date,$data_close->closed_at);
			$paymentCC=$this->pos2_model->getToatalSalesPaymentPOSCC($this->pos2_model->getUser($data_close->user_id)->biller_id,'completed','CC',$data_close->date,$data_close->closed_at);

			$re_paymentcash=$this->pos2_model->getToatalSalesPaymentPOS($this->pos2_model->getUser($data_close->user_id)->biller_id,'returned','cash',$data_close->date,$data_close->closed_at);
			$re_paymentCC=$this->pos2_model->getToatalSalesPaymentPOSCC($this->pos2_model->getUser($data_close->user_id)->biller_id,'returned','CC',$data_close->date,$data_close->closed_at);

            //$this->sma->print_arrays($paymentCC);

			if($paymentcash->amount!=0){

			$entrybillerAcc=$this->pos2_model->getAccountByRefId($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;

            if($entrybillerAcc==""){
                $entrybillerAcc=$this->pos2_model->getAccountByRefId2($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->pos2_model->getAccountByRefId3($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->pos2_model->getAccountByRefId4($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->pos2_model->getAccountByRefId5($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
            }
            //$this->sma->print_arrays($entrybillerAcc);
						if($entrybillerAcc==""){
						$entrybillerAcc=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->pos2_model->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->pos2_model->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;

						}
					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paymentcash->amount);

			}

			foreach ($paymentCC as $paycc) {

			if($paycc->amount!=0){

				if($paycc->gl_payment_method_id==0){
					$entryCCAcc=$this->pos2_model->getPaymentTypeByPaymentType('CC')->payment_account;
                    if($entryCCAcc==""){
                        $entryCCAcc=$this->pos2_model->getAccountsSettings()->accounts_main_banks_acct;
					}
                    $entryDebitAccount[]=$entryCCAcc;
                    $entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paycc->amount);
				}else{
					$entryDebitAccount[]=$this->pos2_model->getPaymentTypeById($paycc->gl_payment_method_id)->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paycc->amount);
				}
			}

			}


            //$this->sma->print_arrays($sales);



			$total=0;
			$total_tax=0;
			$shipping=0;
			foreach ($sales as $sale) {
				$entryDebitAccountCustomer="";
				if($this->pos2_model->getCompanyByID($sale->customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->pos2_model->getAccountByRefId($sale->customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->pos2_model->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->pos2_model->getCompanyByID($sale->customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->pos2_model->getAccountByRefId($sale->customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->pos2_model->getAccountsSettings()->accounts_international_customers_acct;


				}
				if($sale->balance!=0){
			$entryDebitAccount[] =$entryDebitAccountCustomer;
			$entryDebitBalance[] =$sale->balance;
            $entryDebitTotal=$entryDebitTotal+$sale->balance;
				}

			$total += $sale->total;
            $total_tax += $sale->total_tax;
            $shipping += $sale->shipping;
            }



			$entryDebitAccountSales=$this->pos2_model->getAccountByRefId($this->pos2_model->getUser($data_close->user_id)->warehouse_id,'sales')->id;
						if($entryDebitAccountSales=="")
					$entryDebitAccountSales =  $this->pos2_model->getAccountsSettings()->sales_acct;

					$entryCreditAccount[] =  $entryDebitAccountSales;
					$entryCreditAccount[] =  $this->pos2_model->getAccountsSettings()->sales_vat_acct;
					if(abs($shipping)>0)
					$entryCreditAccount[] =  $this->pos2_model->getAccountsSettings()->accounts_shipping_other_revenues;


					$entryCreditBalance[] =  $this->sma->formatDecimal($total);
                    $entryCreditTotal=$entryCreditTotal+$this->sma->formatDecimal($total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($total_tax);
					$entryCreditTotal=$entryCreditTotal+$this->sma->formatDecimal($total_tax);
					if(abs($shipping)>0){
					$entryCreditBalance[] =  $this->sma->formatDecimal($shipping);
                    $entryCreditTotal=$entryCreditTotal+$this->sma->formatDecimal($shipping);
					}






				if($re_paymentcash->amount!=0){

			$entrybillerAcc=$this->pos2_model->getAccountByRefId($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
						//if($entrybillerAcc=="")
						//$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

                        if($entrybillerAcc==""){
                            $entrybillerAcc=$this->pos2_model->getAccountByRefId2($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
                        }
                        if($entrybillerAcc==""){
                            $entrybillerAcc=$this->pos2_model->getAccountByRefId3($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
                        }
                        if($entrybillerAcc==""){
                            $entrybillerAcc=$this->pos2_model->getAccountByRefId4($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
                        }
                        if($entrybillerAcc==""){
                            $entrybillerAcc=$this->pos2_model->getAccountByRefId5($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
                        }


							if($entrybillerAcc==""){
						$entrybillerAcc=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->pos2_model->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->pos2_model->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;

						}


					$entryCreditAccount[] =  $entrybillerAcc;
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($re_paymentcash->amount));
                    $entryCreditTotal=$entryCreditTotal+$this->sma->formatDecimal(abs($re_paymentcash->amount));


			}


			foreach ($re_paymentCC as $paycc) {

			if($paycc->amount!=0){

				if($paycc->gl_payment_method_id==0){
					//$entryDebitAccount[]=$this->pos2_model->getPaymentTypeByPaymentType('CC')->payment_account;
                    $entryCCAcc=$this->pos2_model->getPaymentTypeByPaymentType('CC')->payment_account;
                    if($entryCCAcc==""){
                        $entryCCAcc=$this->pos2_model->getAccountsSettings()->accounts_main_banks_acct;
					}
                    $entryDebitAccount[]=$entryCCAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paycc->amount);
				}else{
					$entryDebitAccount[]=$this->pos2_model->getPaymentTypeById($paycc->gl_payment_method_id)->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paycc->amount);
				}
			}

			}



			$re_total=0;
			$re_total_tax=0;
			$re_shipping=0;
			foreach ($re_sales as $re_sale) {
				$entryDebitAccountCustomer="";
				if($this->pos2_model->getCompanyByID($re_sale->customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->pos2_model->getAccountByRefId($re_sale->customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->pos2_model->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->pos2_model->getCompanyByID($re_sale->customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->pos2_model->getAccountByRefId($re_sale->customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->pos2_model->getAccountsSettings()->accounts_international_customers_acct;


				}
				if(abs($re_sale->balance)!=0){
			$entryCreditAccount[] =$entryDebitAccountCustomer;
			$entryCreditBalance[] =abs($re_sale->balance);
            $entryCreditTotal=$entryCreditTotal+abs($re_sale->balance);

				}

			$re_total += abs($re_sale->total);
            $re_total_tax += abs($re_sale->total_tax);
            $re_shipping += abs($re_sale->shipping);
            }





				$entryDebitAccountSales=$this->pos2_model->getAccountByRefId($this->pos2_model->getUser($data_close->user_id)->warehouse_id,'return_sales')->id;
						if($entryDebitAccountSales=="")
					$entryDebitAccountSales =  $this->pos2_model->getAccountsSettings()->sales_return_acct;

					if(abs($re_total)>0)
					$entryDebitAccount[] =  $entryDebitAccountSales;
					if(abs($re_total_tax)>0)
					$entryDebitAccount[] =  $this->pos2_model->getAccountsSettings()->sales_vat_acct;
					if(abs($re_shipping)>0)
					$entryDebitAccount[] =  $this->pos2_model->getAccountsSettings()->accounts_shipping_other_revenues;


					if(abs($re_total)>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_total));
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal(abs($re_total));
                    }
                    if(abs($re_total_tax)>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_total_tax));
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal(abs($re_total_tax));
                    }
					if(abs($re_shipping)>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_shipping));
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal(abs($re_shipping));
                    }






                    if($entryDebitTotal!=$entryCreditTotal){
                        if($entryDebitTotal>$entryCreditTotal){
                            $rem=$entryDebitTotal-$entryCreditTotal;
                            $cr_index=0;
                            foreach ($entryCreditAccount as $creditAccount){
                               if ($creditAccount==$this->pos2_model->getAccountsSettings()->sales_acct){
                                    $entryCreditBalance[$cr_index]=$entryCreditBalance[$cr_index]+$this->sma->formatDecimal(abs($rem));
                                }

								$cr_index++;



							}
                        }else{
                            $rem=$entryDebitTotal-$entryCreditTotal;
                            $cr_index=0;
                            foreach ($entryCreditAccount as $creditAccount){
                               if ($creditAccount==$this->pos2_model->getAccountsSettings()->sales_acct){
                                    $entryCreditBalance[$cr_index]=$entryCreditBalance[$cr_index]-$this->sma->formatDecimal(abs($rem));
                                }

								$cr_index++;



							}
                        }
                    }



                    //$this->sma->print_arrays($entryDebitAccount,$entryDebitBalance,$entryCreditAccount,$entryCreditBalance,$entryDebitTotal,$entryCreditTotal);


				$this->pos2_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close->user_id);


				   $entryWarehouseAcc=$this->pos2_model->getAccountByRefId($this->pos2_model->getUser($data_close->user_id)->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->pos2_model->getAccountsSettings()->warehouses_acct;

				$good_sold=$this->pos2_model->getToatalGoodSold($this->pos2_model->getUser($data_close->user_id)->biller_id,'completed',$data_close->date,$data_close->closed_at);

				if($this->pos2_model->getSystemSetting()->accounting_type=="periodic" || $good_sold->item_cost_value==0){

                }else{
				$this->pos2_model->createEntryMultiSide($this->pos2_model->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($good_sold->item_cost_value),$this->sma->formatDecimal($good_sold->item_cost_value),$rid,$entry_type,$data_close->user_id);
                 }
				$re_good_sold=$this->pos2_model->getToatalGoodSold($this->pos2_model->getUser($data_close->user_id)->biller_id,'returned',$data_close->date,$data_close->closed_at);

				if($this->pos2_model->getSystemSetting()->accounting_type=="periodic" || abs($re_good_sold->item_cost_value)==0){

                }else{
				$this->pos2_model->createEntryMultiSide($entryWarehouseAcc,$this->pos2_model->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$rid,$entry_type,$data_close->user_id);
                }


                if($this->pos2_model->getSystemSetting()->isDonation==1 && $data_close->donation!='0'){
                    $entry_type="Donations_due_to_charities";
                    //$entryDescription="Donations_Payments_Sales_Invoice_No#".$sale_id;


                    $entryDebitAccount= [];
                    $entryDebitBalance= [];


                    $entrybillerAcc=$this->pos2_model->getAccountByRefId($this->pos2_model->getUser($data_close->user_id)->biller_id,'billers')->id;
                    if($entrybillerAcc==""){
                    $entrybillerAcc=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;
                    $entrybillerAccCode=$this->pos2_model->getAccountByID($entrybillerAcc)->accountID;
                    $entrybillerAcc=$this->pos2_model->getAccountByAccountID($entrybillerAccCode."01")->id;
                    if($entrybillerAcc=="")
                    $entrybillerAcc=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;

                    }
                $entryDebitAccount[] =  $entrybillerAcc;
                $entryDebitBalance[] =  $this->sma->formatDecimal($data_close['donation']);



                $entryCreditAccount= [];
                $entryCreditBalance= [];

                $entryCrediAcc=$this->pos2_model->getAccountsSettings()->donations_acct;
                    if($entryCrediAcc=="")
                    $entryCrediAcc=$this->pos2_model->getAccountsSettings()->expenses_shipping_acct;
                     $entryCreditAccount[] =  $entryCrediAcc;


                $entryCreditBalance[] =  $this->sma->formatDecimal($data_close['donation']);


                $this->pos2_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close->user_id);




                }








			}






    }

    public function setZatcaInvoices()
    {
        $start_date='2026-01-04 00:00:00';
        $end_date='2026-01-04 23:59:59';

        $zatcaInvoices=$this->pos2_model->getAllZatcaInvoicesV2($start_date,$end_date);
        $this->sma->print_arrays($zatcaInvoices);
        foreach ($zatcaInvoices as $invoice) {
            $this->pos2_model->generate_xml($invoice->id);
        }
    }
}
