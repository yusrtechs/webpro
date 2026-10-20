<?php
//ini_set('display_errors', 1); 
defined('BASEPATH') or exit('No direct script access allowed');

class Shop_settings extends MY_Controller
{


    public function __construct()
    {
        // echo "ss";die;
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect('admin');
        }
        $this->load->config('rest');
		$this->restdb = $this->load->database($this->config->item('rest_database_group'), true);

        $this->lang->admin_load('front_end', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('shop_admin_model');
        $this->load->admin_model('sales_model');
        $this->load->admin_model('api_model');

        $this->upload_path       = 'assets/uploads/';
        $this->image_types       = 'gif|jpg|jpeg|png';
        $this->allowed_file_size = '1024';

        $this->load->admin_model('db_model');
        $this->data['categories']          = $this->site->getAllCategories();
        $this->data['tax_rates']           = $this->site->getAllTaxRates();
        $this->data['brands']              = $this->site->getAllBrands();
        $this->data['base_units']          = $this->site->getAllBaseUnits();



    }

    public function add_page()
    {
        $this->form_validation->set_rules('name', lang('name'), 'required|max_length[15]');
        $this->form_validation->set_rules('title', lang('title'), 'required|max_length[60]');
        $this->form_validation->set_rules('description', lang('description'), 'required');
        $this->form_validation->set_rules('body', lang('body'), 'required');
        $this->form_validation->set_rules('order_no', lang('order_no'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('slug', lang('slug'), 'trim|required|is_unique[pages.slug]|alpha_dash');
        if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'title'       => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'body'        => $this->input->post('body', true),
                'slug'        => $this->input->post('slug'),
                'order_no'    => $this->input->post('order_no'),
                'active'      => $this->input->post('active') ? $this->input->post('active') : 0,
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->addPage($data)) {
            $this->session->set_flashdata('message', lang('page_added'));
           //  admin_redirect('shop_settings/pages/shop');
            $isShop = $isShop != '' ? '0/' .  $isShop : '';
            admin_redirect('shop_settings/pages/' . $isShop);
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('add_page')]];
            $meta                = ['page_title' => lang('add_page'), 'bc' => $bc];
            $this->page_construct('shop/add_page', $meta, $this->data,'shop');
        }
    }

    public function delete_page($id = null)
    {
        if ($this->shop_admin_model->deletePage($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('page_deleted')]);
        }
    }

    public function edit_page($id = null)
    {
        $page = $this->shop_admin_model->getPageByID($id);
        $this->form_validation->set_rules('name', lang('name'), 'required|max_length[15]');
        $this->form_validation->set_rules('title', lang('title'), 'required|max_length[60]');
        $this->form_validation->set_rules('description', lang('description'), 'required');
        $this->form_validation->set_rules('body', lang('body'), 'required');
        $this->form_validation->set_rules('order_no', lang('order_no'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('slug', lang('slug'), 'trim|required|alpha_dash');
        if ($page->slug != $this->input->post('slug')) {
            $this->form_validation->set_rules('slug', lang('slug'), 'is_unique[pages.slug]');
        }
        if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'title'       => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'body'        => $this->input->post('body', true),
                'slug'        => $this->input->post('slug'),
                'order_no'    => $this->input->post('order_no'),
                'active'      => $this->input->post('active') ? $this->input->post('active') : 0,
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->updatePage($id, $data)) {
            $this->session->set_flashdata('message', lang('page_updated'));
            admin_redirect('shop_settings/pages');
        } else {
            $this->data['page']  = $page;
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('edit_page')]];
            $meta                = ['page_title' => lang('edit_page'), 'bc' => $bc];
            $this->page_construct('shop/edit_page', $meta, $this->data,'shop');
        }
    }

    public function getPages()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name, slug, active, order_no, title')
            ->from('pages')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('shop_settings/edit_page/$1') . "' class='tip' title='" . lang('edit_page') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_page') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('shop_settings/delete_page/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }


    public function index()
    {
        $this->sma->checkPermissions();
        $shop_settings        = $this->shop_admin_model->getShopSettings();

        if(empty($shop_settings->token)){
            $store_name=$this->Settings->site_name;
            $name=$this->session->userdata('username');
            //$this->session->userdata('user_id')

            $user        = $this->site->getUser($this->session->userdata('user_id'));
            if(empty($user->key_p)){
                $password="Admin123";
            }else{
                $password=$user->key_p;
            }
            $db_name=str_replace("cpesoft_","",$this->db->database);
            $apisarr=$this->restdb->query("SELECT `key` FROM `sma_api_keys` WHERE company='".$db_name."' ")->result_array();
            $api_key= $apisarr[0]['key'];
            
            if($api_key){
            }else{
                $new_api_key=$this->generateKey();
                $userInfo = [
                    'reference'     => $db_name . "_1" ,
                    'user_id'       => '1',
                    'key'           => $new_api_key,
                    'level'         => '2',
                    'ignore_limits' => '1',
                    'ip_addresses'  => '127.0.0.1',
                    'company' => $db_name,
                    'date_created'  => time(),
                ];

            
                $result = $this->restdb->insert('api_keys', $userInfo);
                if ($result > 0) {
                    $api_key=$new_api_key;
                }
            }
            
            //$db_name=$db_name.'7';
            $email=$db_name.'@gmail.com';

            $url = $shop_settings->base_url.'/api/register/store';

                $ch = curl_init($url);
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $headers[] = 'Accept:application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
                $data='{"name": "'.$name.'","store_name": "'.$store_name.'","email": "'.$email.'","password": "'.$password.'","password_confirmation": "'.$password.'","api_key": "'.$api_key.'"}';
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                /* execute request */
                $result = json_decode(curl_exec($ch));
                //$result1 = (curl_exec($ch));


                /* close cURL resource */
                curl_close($ch);

                if($result->token){


                $data1 = [
                'name'                 => $name,
                'store_name'          => $store_name,
                'email'            => $email,
                'password'               => $password,
                'created_by'           => $this->session->userdata('user_id'),
                'created_at'           => date('Y-m-d H:i:s'),
                'token'         => $result->token,
                'store_id'         => $result->store->id,
                'theme_id'         => $result->store->theme_id,

            ];

                    $this->shop_admin_model->updateShopSettings($data1);
                    //$this->sma->print_arrays($result,$result->store,$data1);
                    //$this->sma->print_arrays($result);
                }


            //$this->sma->print_arrays($shop_settings);
        }else{

        }

        $this->data['shop_settings'] = $this->shop_admin_model->getShopSettings();
        $db_name=str_replace("cpesoft_","",$this->db->database);
        $apisarr=$this->restdb->query("SELECT `key` FROM `sma_api_keys` WHERE company='".$db_name."' ")->result_array();
        $api_key= $apisarr[0]['key'];
        $this->data['api_key']= $api_key;


        $this->page_construct('shop/auth', $meta, $this->data);
    }


    public function index1()
    {

        $this->form_validation->set_rules('shop_name', lang('shop_name'), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('biller'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('products_description', lang('products_description'), 'trim|required');
        $this->data['chatData']  = $this->db_model->getChartData();
        $this->data['ordersCount'] = $this->db_model->getAllOrdersCount();
        $this->data['TotalSalesValue'] = $this->db_model->getTotalSalesValue();
        $this->data['TotalProducts'] = $this->db_model->getTotalProducts();
        $this->data['CurrentMonthSalesFromShop'] = $this->db_model->getCurrentMonthSalesFromShop();
        $this->data['CurrentMonthSalesFromPOS'] = $this->db_model->getCurrentMonthSalesFromPOS();
        $this->data['sales']     = $this->db_model->getLatestSales();


        if ($this->form_validation->run() == true) {
            $data = ['shop_name'       => DEMO ? 'SMA Shop' : $this->input->post('shop_name'),
                'description'          => DEMO ? 'Stock Manager Advance - SMA Shop - Demo Ecommerce Shop that would help you to sell your products from your site. Locked on demo.' : $this->input->post('description'),
                'products_description' => DEMO ? 'This is products page description and is locked on demo.' : $this->input->post('products_description'),
                'warehouse'            => $this->input->post('warehouse'),
                'biller'               => $this->input->post('biller'),
                'about_link'           => $this->input->post('about_link'),
                'terms_link'           => $this->input->post('terms_link'),
                'privacy_link'         => $this->input->post('privacy_link'),
                'contact_link'         => $this->input->post('contact_link'),
                'payment_text'         => $this->input->post('payment_text'),
                'follow_text'          => $this->input->post('follow_text'),
                'facebook'             => $this->input->post('facebook'),
                'twitter'              => $this->input->post('twitter'),
                'google_plus'          => $this->input->post('google_plus'),
                'instagram'            => $this->input->post('instagram'),
                'phone'                => $this->input->post('phone'),
                'email'                => $this->input->post('email'),
                'cookie_message'       => DEMO ? 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.' : $this->input->post('cookie_message'),
                'cookie_link'          => $this->input->post('cookie_link'),
                'shipping'             => $this->input->post('shipping'),
                'bank_details'         => $this->input->post('bank_details'),
                'products_page'        => $this->input->post('products_page'),
                'hide0'                => $this->input->post('hide0'),
                'hide_price'           => $this->input->post('hide_price'),
                'private'              => $this->input->post('private'),
                'stripe'               => $this->input->post('stripe'),
                'themeColor'           => $this->input->post('themeColor'),
                'textColor'           => $this->input->post('textColor'),

            ];

            if ($_FILES['logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 300;
                $config['max_height']    = 80;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $data['logo'] = $this->upload->file_name;
                }
            }
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->updateShopSettings($data)) {
            $this->session->set_flashdata('message', lang('settings_updated'));
            admin_redirect('shop_settings');
        } else {
            $this->data['warehouses']    = $this->site->getAllWarehouses();
            $this->data['billers']       = $this->site->getAllCompanies('biller');
            $this->data['pages']         = $this->shop_admin_model->getAllPages();
            $this->data['shop_settings'] = $this->shop_admin_model->getShopSettings();
            $this->data['error']         = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('shop_settings')]];
            $meta                        = ['page_title' => lang('shop_settings'), 'bc' => $bc];
        //    print_r( $this->data['shop_settings']);
        //    die;
            $this->page_construct('shop/index', $meta, $this->data, 'shop');
        }
    }

    public function setup()
    {
        $this->form_validation->set_rules('shop_name', lang('shop_name'), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('biller'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('products_description', lang('products_description'), 'trim|required');
        $this->data['chatData']  = $this->db_model->getChartData();
        $this->data['ordersCount'] = $this->db_model->getAllOrdersCount();
        $this->data['TotalSalesValue'] = $this->db_model->getTotalSalesValue();
        $this->data['TotalProducts'] = $this->db_model->getTotalProducts();
        $this->data['CurrentMonthSalesFromShop'] = $this->db_model->getCurrentMonthSalesFromShop();
        $this->data['CurrentMonthSalesFromPOS'] = $this->db_model->getCurrentMonthSalesFromPOS();
        $this->data['sales']     = $this->db_model->getLatestSales();


        if ($this->form_validation->run() == true) {
            $data = ['shop_name'       => DEMO ? 'SMA Shop' : $this->input->post('shop_name'),
                'description'          => DEMO ? 'Stock Manager Advance - SMA Shop - Demo Ecommerce Shop that would help you to sell your products from your site. Locked on demo.' : $this->input->post('description'),
                'products_description' => DEMO ? 'This is products page description and is locked on demo.' : $this->input->post('products_description'),
                'warehouse'            => $this->input->post('warehouse'),
                'biller'               => $this->input->post('biller'),
                'about_link'           => $this->input->post('about_link'),
                'terms_link'           => $this->input->post('terms_link'),
                'privacy_link'         => $this->input->post('privacy_link'),
                'contact_link'         => $this->input->post('contact_link'),
                'payment_text'         => $this->input->post('payment_text'),
                'follow_text'          => $this->input->post('follow_text'),
                'facebook'             => $this->input->post('facebook'),
                'twitter'              => $this->input->post('twitter'),
                'google_plus'          => $this->input->post('google_plus'),
                'instagram'            => $this->input->post('instagram'),
                'phone'                => $this->input->post('phone'),
                'email'                => $this->input->post('email'),
                'cookie_message'       => DEMO ? 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.' : $this->input->post('cookie_message'),
                'cookie_link'          => $this->input->post('cookie_link'),
                'shipping'             => $this->input->post('shipping'),
                'bank_details'         => $this->input->post('bank_details'),
                'products_page'        => $this->input->post('products_page'),
                'hide0'                => $this->input->post('hide0'),
                'hide_price'           => $this->input->post('hide_price'),
                'private'              => $this->input->post('private'),
                'stripe'               => $this->input->post('stripe'),
                'themeColor'           => $this->input->post('themeColor'),
                'textColor'           => $this->input->post('textColor'),

            ];

            if ($_FILES['logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 300;
                $config['max_height']    = 80;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $data['logo'] = $this->upload->file_name;
                }
            }

            //$this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->updateShopSettings($data)) {
            $this->session->set_flashdata('message', lang('settings_updated'));
            admin_redirect('shop_settings');
        } else {
            $this->data['warehouses']    = $this->site->getAllWarehouses();
            $this->data['billers']       = $this->site->getAllCompanies('biller');
            $this->data['pages']         = $this->shop_admin_model->getAllPages();
            $this->data['shop_settings'] = $this->shop_admin_model->getShopSettings();
            $this->data['error']         = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('shop_settings')]];
            $meta                        = ['page_title' => lang('shop_settings'), 'bc' => $bc];
        //    print_r( $this->data['shop_settings']);
        //    die;
            $this->page_construct('shop/setup', $meta, $this->data, 'shop');
        }
    }

    public function getChartData($year = null){
        $year = $year == null ? date('Y') : $year;
        echo json_encode($this->db_model->getChartData($year));die;
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
                admin_redirect('shop_settings/updates');
            }
        }
        $this->db->update('shop_settings', ['version' => $version], ['shop_id' => 1]);
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        admin_redirect('shop_settings/updates');
    }

    public function page_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->shop_admin_model->deletePage($id);
                    }
                    $this->session->set_flashdata('message', lang('pages_deleted'));
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

    public function pages()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['pages']         = $this->shop_admin_model->getAllPages();
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('shop_settings'), 'page' => lang('shop_settings')], ['link' => '#', 'page' => lang('pages')]];
        $meta = ['page_title' => lang('pages'), 'bc' => $bc];
        $this->page_construct('shop/pages', $meta, $this->data,'shop');
    }

    public function send_sms($date = null)
    {
        $this->form_validation->set_rules('mobile', lang('mobile'), 'trim|required');
        $this->form_validation->set_rules('message', lang('message'), 'trim|required');
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        if ($this->form_validation->run() == true) {
            $this->load->library('sms');
            $res = $this->sms->send($this->input->post('mobile'), $this->input->post('message'));
            if (isset($res['error']) && $res['error']) {
                $this->data['error'] = lang('sms_request_failed');
            } else {
                $this->data['message'] = lang('sms_request_sent');
            }
        }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('send_sms')]];
        $meta = ['page_title' => lang('send_sms'), 'bc' => $bc];
        $this->page_construct('shop/send_sms', $meta, $this->data);
    }

    public function sitemap()
    {
        $categories = $this->shop_admin_model->getAllCategories();
        $products   = $this->shop_admin_model->getAllProducts();
        $brands     = $this->shop_admin_model->getAllBrands();
        $pages      = $this->shop_admin_model->getAllPages();
        $map        = '<?xml version="1.0" encoding="UTF-8" ?>';

        $map .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $map .= '<url>';
        $map .= '<loc>' . site_url() . '</loc> ';
        $map .= '<priority>1.0</priority>';
        $map .= '<changefreq>daily</changefreq>';
        // $map .= '<lastmod>'.date('Y-m-d').'</lastmod>';
        $map .= '</url>';

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $map .= '<url>';
                $map .= '<loc>' . site_url('category/' . $category->slug) . '</loc> ';
                $map .= '<priority>0.8</priority>';
                $map .= '</url>';
                $subcategories = $this->shop_admin_model->getSubCategories($category->id);
                if (!empty($subcategories)) {
                    foreach ($subcategories as $subcategory) {
                        $map .= '<url>';
                        $map .= '<loc>' . site_url('category/' . $category->slug . '/' . $subcategory->slug) . '</loc> ';
                        $map .= '<priority>0.8</priority>';
                        $map .= '</url>';
                    }
                }
            }
        }

        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $map .= '<url>';
                $map .= '<loc>' . shop_url($brand->slug) . '</loc> ';
                $map .= '<priority>0.8</priority>';
                $map .= '</url>';
            }
        }

        if (!empty($products)) {
            foreach ($products as $products) {
                $map .= '<url>';
                $map .= '<loc>' . site_url('product/' . $products->slug) . '</loc> ';
                $map .= '<priority>0.6</priority>';
                $map .= '</url>';
            }
        }

        if (!empty($pages)) {
            foreach ($pages as $page) {
                $map .= '<url>';
                $map .= '<loc>' . site_url('page/' . $page->slug) . '</loc> ';
                $map .= '<priority>0.8</priority>';
                $map .= '<changefreq>yearly</changefreq>';
                if ($page->updated_at) {
                    $map .= '<lastmod>' . date('Y-m-d', strtotime($page->updated_at)) . '</lastmod>';
                }
                $map .= '</url>';
            }
        }

        $map .= '</urlset>';
        file_put_contents('sitemap.xml', $map);
        header('Location: ' . base_url('sitemap.xml'));
        exit;
    }

    public function slider($isShop = '')
    {



        // $this->form_validation->set_rules('image1', lang('image1'), 'trim|required');
        $this->form_validation->set_rules('caption1', lang('caption') . ' 1', 'trim|max_length[160]');
        // $this->form_validation->set_rules('image2', lang('image2'), 'trim|required');
        // $this->form_validation->set_rules('caption2', lang('caption2'), 'trim|max_length[160]');

        if ($this->form_validation->run() == true) {
            $uploaded = ['image1' => '', 'image2' => '', 'image3' => '', 'image4' => '', 'image5' => ''];
            if (!DEMO) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);

                $images = ['image1', 'image2', 'image3', 'image4', 'image5'];
                foreach ($images as $image) {
                    if ($_FILES[$image]['size'] > 0) {
                        if (!$this->upload->do_upload($image)) {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                        $uploaded[$image] = $this->upload->file_name;
                    }
                }
            }
            $images =  $this->input->post('images');
            $images =  (is_array($images) ) ? $images  : [];
            $mapped = [];
            if(count(  $images) > 0){
                $imgIds = array_values($images);
                $this->db->where_in('id', $imgIds);
                $dbImgs =  $this->db->get('images')->result();
                   // restructure the array to have the id as the key
                    $mapped = array();
                    foreach($dbImgs as $img){
                        $mapped[$img->id] = $img;
                    }

            }
            $data = [];
                $this->db->where('shop_id', 1);
                $data =  json_decode($this->db->get('shop_settings')->row()->slider);


            for ($i = 1; $i <= 5; $i++) {

                // convert object to array
                $data[$i-1] = (array) $data[$i-1];
                if( count($mapped) > 0 ){
                    if  (!empty($images['imageId'.$i]) ) {
                        $data[$i-1]['image'] = $mapped[$images['imageId'.$i]]->filename ;
                    }
                }

                $data[$i-1]['link'] = $this->input->post('link' . $i) ;
                $data[$i-1]['caption'] = $this->input->post('caption'. $i) ;
                $data[$i-1]['caption_en'] = $this->input->post('caption_en'. $i) ;

            }

            // $data = [
            //     [
            //         // 'image'   => DEMO ? 's1.jpg' : (!empty($uploaded['image1']) ? $uploaded['image1'] : ''),
            //         'image'   => DEMO ? 's1.jpg' : (!empty($images['imageId1']) ?  $mapped[$images['imageId1']]->filename : ''),
            //         'link'    => DEMO ? shop_url('products') : $this->input->post('link1'),
            //         'caption' => DEMO ? '' : $this->input->post('caption1'),
            //     ],
            //     [
            //         // 'image'   => DEMO ? 's2.jpg' : (!empty($uploaded['image2']) ? $uploaded['image2'] : ''),
            //         'image'   => DEMO ? 's2.jpg' : (!empty($images['imageId2']) ?  $mapped[$images['imageId2']]->filename : ''),
            //         'link'    => DEMO ? '' : $this->input->post('link2'),
            //         'caption' => DEMO ? '' : $this->input->post('caption2'),
            //     ],
            //     [
            //         // 'image'   => DEMO ? 's3.jpg' : (!empty($uploaded['image3']) ? $uploaded['image3'] : ''),
            //         'image'   => DEMO ? 's2.jpg' : (!empty($images['imageId3']) ?  $mapped[$images['imageId3']]->filename : ''),
            //         'link'    => DEMO ? '' : $this->input->post('link3'),
            //         'caption' => DEMO ? '' : $this->input->post('caption3'),
            //     ],
            //     [
            //         // 'image'   => DEMO ? '' : (!empty($uploaded['image4']) ? $uploaded['image4'] : ''),
            //         'image'   => DEMO ? 's4.jpg' : (!empty($images['imageId4']) ?  $mapped[$images['imageId4']]->filename : ''),
            //         'link'    => DEMO ? '' : $this->input->post('link4'),
            //         'caption' => DEMO ? '' : $this->input->post('caption4'),
            //     ],
            //     [
            //         // 'image'   => DEMO ? '' : (!empty($uploaded['image5']) ? $uploaded['image5'] : ''),
            //         'image'   => DEMO ? 's5.jpg' : (!empty($images['imageId5']) ?  $mapped[$images['imageId5']]->filename : ''),
            //         'link'    => DEMO ? '' : $this->input->post('link5'),
            //         'caption' => DEMO ? '' : $this->input->post('caption5'),
            //     ],
            // ];

            // foreach ($data as &$img) {
            //     if (empty($img['image'])) {
            //         unset($img['image']);
            //     }
            // }



        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->updateSlider($data)) {
            $this->session->set_flashdata('message', lang('silder_updated'));
            admin_redirect('shop_settings/slider/shop');
        } else {
            $shop_settings                 = $this->shop_admin_model->getShopSettings();
            $this->data['slider_settings'] = json_decode($shop_settings->slider);
            $this->data['error']           = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('shop_settings'), 'page' => lang('shop_settings')], ['link' => '#', 'page' => lang('slider_settings')]];
            $meta                          = ['page_title' => lang('slider_settings'), 'bc' => $bc];
             // get all images from images table
             $this->data['libImgs'] = $this->db->order_by('id','DESC')->get('images')->result();

            // $this->page_construct('shop/slider', $meta, $this->data);
           $this->page_construct('shop/slider', $meta, $this->data,$isShop);
        }
    }
    public  function uploadImages(){



        // if not ajax request return false
        if (!$this->input->is_ajax_request()) {
            // return false;
        }
        // if the user is not logged in return false
        if (!$this->loggedIn) {
            // return false;
        }


        print_r($_FILES);exit;
        // check if we have images to upload
        if (!empty($_FILES['images']['name'])) {
            // set the upload path
            $config['upload_path'] = $this->upload_path;
            // set the allowed file types
            $config['allowed_types'] = $this->image_types;
            // set the max file size
            $config['max_size'] = $this->allowed_file_size;
            // set the max file name size
            $config['max_filename'] = 25;
            // set the file name encryption
            $config['encrypt_name'] = true;
            // set the overwrite to false
            $config['overwrite'] = false;
            // load the upload library
            $this->load->library('upload', $config);
            // set the images array
            $images = [];
            $uploadedImages = [];
            // loop through the images
            foreach ($_FILES['images']['name'] as $key => $image) {
                // set the file name
                $_FILES['images[]']['name'] = $_FILES['images']['name'][$key];
                // set the file type
                $_FILES['images[]']['type'] = $_FILES['images']['type'][$key];
                // set the file tmp name
                $_FILES['images[]']['tmp_name'] = $_FILES['images']['tmp_name'][$key];
                // set the file error
                $_FILES['images[]']['error'] = $_FILES['images']['error'][$key];
                // set the file size
                $_FILES['images[]']['size'] = $_FILES['images']['size'][$key];
                // set the file name
                $config['file_name'] = $_FILES['images[]']['name'];
                // set the upload path
                $config['upload_path'] = $this->upload_path;
                // set the allowed file types
                $config['allowed_types'] = $this->image_types;
                // set the max file size
                $config['max_size'] = $this->allowed_file_size;
                // set the max file name size
                $config['max_filename'] = 25;
                // set the file name encryption
                $config['encrypt_name'] = true;
                // set the overwrite to false
                $config['overwrite'] = false;
                // load the upload library
                $this->upload->initialize($config);
                // if the upload is successful
                if ($this->upload->do_upload('images[]')) {
                    // get the uploaded file data
                    $fileData = $this->upload->data();
                    // set the uploaded file name
                    $filename = $fileData['file_name'];
                    $uploadedImages[$key]['filename'] = $filename;
                    // set the uploaded file path
                    $path = $this->upload_path . $filename;
                    $uploadedImages[$key]['path'] = $path;

                }
                // if the upload is not successful
                else {
                    // set the error message
                    $error = $this->upload->display_errors();
                    // set the response
                    $response = ['status' => 'error', 'message' => $error];
                    // return the response
                    return $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
                // set the image data

            }


            $imageData = [
                'images' => $uploadedImages,
            ];
            // insert the image data
            $this->shop_admin_model->insertImagesArr($imageData);

            // return the response
            $response = ['status' => 'success', 'message' => lang('image_uploaded')];
            // attach the image data
            $response['image'] = $imageData;
            // return the response
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }

}
    public function slugify()
    {
        if ($products = $this->shop_admin_model->getAllProducts()) {
            $this->db->update('products', ['slug' => null]);
            foreach ($products as $product) {
                $slug = $this->sma->slug($product->name);
                $this->db->update('products', ['slug' => $slug], ['id' => $product->id]);
            }
            $this->session->set_flashdata('message', lang('slugs_updated'));
            redirect($_SERVER['HTTP_REFERER'] ?? 'admin/shop_settings');
        }
        $this->session->set_flashdata('error', lang('no_product_found'));
        redirect($_SERVER['HTTP_REFERER'] ?? 'admin/shop_settings');
    }

    public function sms_log($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        $file = APPPATH . 'logs/' . 'sms-' . $date . '.log';
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

        $this->data['log']   = $content;
        $this->data['date']  = $date;
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('sms_log')]];
        $meta                = ['page_title' => lang('sms_log'), 'bc' => $bc];
        $this->page_construct('shop/log', $meta, $this->data);
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
            ];

            $sms_config = [
                'auto_send' => $this->input->post('auto_send'),
                'config'    => json_encode($data),
            ];
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->updateSmsSettings($sms_config)) {
            $this->session->set_flashdata('message', lang('settings_updated'));
            admin_redirect('shop_settings/sms_settings');
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
            $this->db->update('shop_settings', ['purchase_code' => $this->input->post('purchase_code', true), 'envato_username' => $this->input->post('envato_username', true)], ['shop_id' => 1]);
            admin_redirect('shop_settings/updates');
        } else {
            $shop_settings = $this->shop_admin_model->getShopSettings();
            $fields        = ['version' => $shop_settings->version, 'code' => $shop_settings->purchase_code, 'username' => $shop_settings->envato_username, 'site' => base_url()];
            $this->load->helper('update');
            $protocol                    = is_https() ? 'https://' : 'http://';
            $updates                     = get_remote_contents($protocol . 'api.tecdiary.com/v1/update/', $fields);
            $this->data['shop_settings'] = $shop_settings;
            $this->data['updates']       = json_decode($updates);
            $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('updates')]];
            $meta                        = ['page_title' => lang('updates'), 'bc' => $bc];
            $this->page_construct('shop/updates', $meta, $this->data);
        }
    }

    public function shop_sync()
    {
        //$this->sma->checkPermissions('dashboard_acc',true);
        $shop_settings        = $this->shop_admin_model->getShopSettings();

        if(empty($shop_settings->token)){
            $store_name=$this->Settings->site_name;
            $name=$this->session->userdata('username');
            //$this->session->userdata('user_id')

            $user        = $this->site->getUser($this->session->userdata('user_id'));
            if(empty($user->key_p)){
                $password="Admin123";
            }else{
                $password=$user->key_p;
            }
            $db_name=str_replace("cpesoft_","",$this->db->database);

            $apisarr=$this->restdb->query("SELECT `key` FROM `sma_api_keys` WHERE company='".$db_name."' ")->result_array();
            $api_key= $apisarr[0]['key'];
            
            if($api_key){
            }else{
                $new_api_key=$this->generateKey();
                $userInfo = [
                    'reference'     => $db_name . "_1" ,
                    'user_id'       => '1',
                    'key'           => $new_api_key,
                    'level'         => '2',
                    'ignore_limits' => '1',
                    'ip_addresses'  => '127.0.0.1',
                    'company' => $db_name,
                    'date_created'  => time(),
                ];

            
                $result = $this->restdb->insert('api_keys', $userInfo);
                if ($result > 0) {
                    $api_key=$new_api_key;
                }
            }
            
            //$this->sma->print_arrays($api_key);

            //$db_name=$db_name.'7';
            $email=$db_name.'@gmail.com';

            $url = $shop_settings->base_url.'/api/register/store';

                $ch = curl_init($url);
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $headers[] = 'Accept:application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
                $data='{"name": "'.$name.'","store_name": "'.$store_name.'","email": "'.$email.'","password": "'.$password.'","password_confirmation": "'.$password.'","api_key": "'.$api_key.'"}';
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                /* execute request */
                $result = json_decode(curl_exec($ch));
                //$result1 = (curl_exec($ch));


                /* close cURL resource */
                curl_close($ch);

                if($result->token){


                $data1 = [
                'name'                 => $name,
                'store_name'          => $store_name,
                'email'            => $email,
                'password'               => $password,
                'created_by'           => $this->session->userdata('user_id'),
                'created_at'           => date('Y-m-d H:i:s'),
                'token'         => $result->token,
                'store_id'         => $result->store->id,
                'theme_id'         => $result->store->theme_id,

            ];

                    $this->shop_admin_model->updateShopSettings($data1);
                    //$this->sma->print_arrays($result,$result->store,$data1);
                    //$this->sma->print_arrays($result);
                }


            //$this->sma->print_arrays($shop_settings);
        }else{

        }
        $this->data['settings'] = $this->shop_admin_model->getShopSettings();


        //$total        = $this->shop_admin_model->getProductsCount()->total;
        //$products        = $this->shop_admin_model->getProducts();

       // $this->sma->print_arrays($total,$products);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc                  = [['link' => '#', 'page' => lang('Synchronize_Data')]];
        $meta                = ['page_title' => lang('Synchronize_Data'), 'bc' => $bc];
        $this->page_construct('shop/shop_sync', $meta, $this->data);
    }

    public function update_categories()
    {

        $syncSettings        = $this->shop_admin_model->getShopSettings();


        $url = $syncSettings->base_url.'/api/category/'.$syncSettings->store_id.'/store';
        $uploads_url = base_url('assets/uploads/');

        $total        = $this->shop_admin_model->getCategoriesCount()->total;
        $categories        = $this->shop_admin_model->getCategories();
        $to=0;


foreach ($categories as $cat) {

                $image=$uploads_url."".($cat->image ? ($cat->image!="no_image.png" ? $cat->image : 'placeholder.png') : 'placeholder.png');
                $name_en=(isset($cat->nameEn) ? $cat->nameEn : '');

                $ch = curl_init($url);
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $headers[] = 'Accept:application/json';
                $headers[] = "Authorization: Bearer ".$syncSettings->token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
                $data='{"name": "'.$cat->name.'","image_url": "'.$image.'","name_en": "'.$name_en.'"}';
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                /* execute request */
                $result = json_decode(curl_exec($ch));
                //$result1 = (curl_exec($ch));


                /* close cURL resource */
                curl_close($ch);


    /* execute request */





    if($result->data){


            $dataUpdate = [
				'shop_id'            => $result->data->id,

			];
            if ($this->shop_admin_model->updateCategory($cat->id,$dataUpdate)) {
            $to++;
            }



        }

//$this->sma->send_json([ 'msg' => $result,'msg1' => $result->data]);
}
    $this->update_products();
/*
    if($to >0){

//            die('<i class="fa fa-check"></i> Success!');
            $this->sma->send_json([ 'msg' => 'âœ… '.lang("Main_categories_synchronization_success").'! '.lang("Entries_NO").' ( '.$to.' ) '  ,'per_page'=> '100','total'=> $total,'to'=> $to ]);

            //$this->sma->send_json([ 'msg' => $result->data]);
        }

        //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');


        $this->sma->send_json([ 'msg' => 'âœ… '.lang("Main_categories_synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> '100','total'=> 0,'to'=> 0 ]);
                //$this->sma->send_json([ 'msg' => $dataFinal]);
                //die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
        //$this->sma->send_json([ 'msg' => $dataFinal]);
       // die('<i class="fa fa-times"></i> Failed!');
*/
    }

    public function update_products()
    {

        $syncSettings        = $this->shop_admin_model->getShopSettings();


        $url = $syncSettings->base_url.'/api/product/'.$syncSettings->store_id.'/store';

        $uploads_url = base_url('assets/uploads/');
        $total        = $this->shop_admin_model->getProductsCount()->total;
        $products        = $this->shop_admin_model->getProducts();
        $to=0;


foreach ($products as $pr) {
                $image=$uploads_url."".($pr->image ? ($pr->image!="no_image.png" ? $pr->image : 'placeholder.png') : 'placeholder.png');
                $weight=(isset($pr->weight) ? $pr->weight : 0);
                $ch = curl_init($url);
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $headers[] = 'Accept:application/json';
                $headers[] = "Authorization: Bearer ".$syncSettings->token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
                $data='{"name": "'.$pr->name.'","maincategory_id": "'.$pr->cat_shop_id.'","sale_price": "'.$pr->price.'","description": "'.$pr->product_details.'","image_url": "'.$image.'","tax_method": "'.$pr->tax_method.'","product_weight": "'.$weight.'"}';
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                /* execute request */
                $result = json_decode(curl_exec($ch));
                //$result1 = (curl_exec($ch));


                /* close cURL resource */
                curl_close($ch);


    /* execute request */





    if($result->data){


            $dataUpdate = [
				'shop_id'            => $result->data->id,

			];
            if ($this->shop_admin_model->updateProducts($pr->id,$dataUpdate)) {
            $to++;
            }



        }

//$this->sma->send_json([ 'msg' => $result,'msg1' => $result->data]);
}


    if($to >0){

//            die('<i class="fa fa-check"></i> Success!');
            $this->sma->send_json([ 'msg' => 'âœ… '.lang("Products_synchronization_success").'! '.lang("Entries_NO").' ( '.$to.' ) '  ,'per_page'=> '100','total'=> $total,'to'=> $to ]);

            //$this->sma->send_json([ 'msg' => $result->data]);
        }

        //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');


        $this->sma->send_json([ 'msg' => 'âœ… '.lang("Products_synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> '100','total'=> 0,'to'=> 0 ]);
                //$this->sma->send_json([ 'msg' => $dataFinal]);
                //die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
        //$this->sma->send_json([ 'msg' => $dataFinal]);
       // die('<i class="fa fa-times"></i> Failed!');

    }


    public function update_products_old()
    {

        $syncSettings        = $this->shop_admin_model->getSyncSettings();
        $syncSettingsApi        = $this->shop_admin_model->getSyncSettingsApi($syncSettings->id);

       /* $latest_sync_order=$syncSettings->latest_sync_order;



    if($latest_sync_order){
        $dateTime = new DateTime($latest_sync_order);

	    $latest_sync_order= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = $syncSettings->siteurl.'/wp-json/wc/v3/products/products/batch';
    }else{
        $url = $syncSettings->siteurl.'/wp-json/wc/v3/products/products/batch';
    }
*/
    $url = $syncSettings->siteurl.'/wp-json/wc/v3/products/batch';
    //$url = $syncSettings->siteurl.'/wp-json/wc/v3/products/products';
    //
    /* Init cURL resource */
    /*$ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';

    $headers[] = "Authorization: Bearer " . base64_encode( $syncSettingsApi->consumer_key . ':' . $syncSettingsApi->consumer_secret );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');



    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
*/
$uploads_url = base_url('assets/uploads/');
$total        = $this->shop_admin_model->getProductsCount()->total;
$products        = $this->shop_admin_model->getProducts();
$to=0;

$data = '{"create": [';
foreach ($products as $cat) {
    $image=$uploads_url."".$cat->image;
    if (strpos( $cat->name, '"')) {

        $cat->name=str_replace('"',"",$cat->name);

        $dataUpdate1 = [
            'name'            => $cat->name,


        ];
        if ($this->site->updateProductById($cat->id,$dataUpdate1)) {

        }
    }

    if (strpos( $cat->name, "'")) {

        $cat->name=str_replace("'","",$cat->name);

        $dataUpdate2 = [
            'name'            => $cat->name,


        ];
        if ($this->site->updateProductById($cat->id,$dataUpdate2)) {

        }
    }


if($to!=0){
$data .=',';
}
$data .='{"name": "'.$cat->name.'","type": "simple",
        "regular_price": "'.$cat->price.'",
        "description": "'.$cat->name.'",
        "short_description": "'.$cat->name.'",
        "categories": [
          {
            "id": "'.$cat->cat_shop_id.'"
          }
        ],
        "images": [
          {
            "src": "'.$image.'"
          }
        ]
      }';



$productsArr[]=$category;
$to++;
}
$data .=']}';

  //$this->sma->send_json([ 'msg' => $data]);
//$productsStr=json_encode($productsArr, true);

//$this->sma->send_json([ 'msg' => $productsStr]);
/*$data = '{
    "create": "'.$productsStr.'"
    }';
    $this->sma->send_json([ 'msg' => $data]);*/
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

 $headers = array(
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode( $syncSettingsApi->consumer_key . ":" . $syncSettingsApi->consumer_secret ),
 );
 //$syncSettingsApi->consumer_key . ':' . $syncSettingsApi->consumer_secret
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//$data = '{"invoiceHash":"'.$inv->stamp_hash.'","uuid":"'.$inv->uuid.'","invoice":"'.$inv->synch_hash.'"}';

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

$result = json_decode(curl_exec($curl));


    /* execute request */


    /* close cURL resource */
    curl_close($curl);


    //$this->sma->send_json([ 'msg' => $result->create[0]->id,'msg1' => $products[0]->id]);




    if($result->create){
        $loc=0;
          foreach($result->create as $dataInfo){

            if($dataInfo->id==0){
                $dataUpdate = [
                    'image'            => 'no_image.png',



                ];
            }else{

            $dataUpdate = [
				'shop_id'            => $dataInfo->id,
				'slug'            => urldecode($dataInfo->slug),

			];
        }
            if ($this->site->updateProductById($products[$loc]->id,$dataUpdate)) {

            }
            $loc++;
          }

//            die('<i class="fa fa-check"></i> Success!');
            $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$to.' ) '  ,'per_page'=> '100','total'=> $total,'to'=> $to ]);

            //$this->sma->send_json([ 'msg' => $result->data]);
        }

        //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');

        $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> '100','total'=> 0,'to'=> 0 ]);

                //$this->sma->send_json([ 'msg' => $dataFinal]);
                //die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
        //$this->sma->send_json([ 'msg' => $dataFinal]);
       // die('<i class="fa fa-times"></i> Failed!');

    }

    public function update_sales()
    {

        //$latest_sync_order=$this->sync_model->getFoodicsSettings()->latest_sync_order;

        $syncSettings        = $this->shop_admin_model->getSyncSettings();
        $syncSettingsApi        = $this->shop_admin_model->getSyncSettingsApi($syncSettings->id);
        $latest_sync_order=$syncSettings->latest_sync_order;

        //$latest_sync_order=$latest_sync_order->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_order]);
       // $latest_sync_order='2023-11-29T22:15:51';
    if($latest_sync_order){
        $dateTime = new DateTime($latest_sync_order);

	    $latest_sync_order= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = $syncSettings->siteurl.'/wp-json/wc/v3/orders?status=completed&order=asc&orderby=date&per_page=100&modified_after='.$latest_sync_order.'';
        //$url = 'https://api.foodics.com/v5/orders?filter[status]=4,5&filter[updated_after]='.$latest_sync_order.'&sort=updated_at&include=branch,customer,payments.payment_method,charges,charges.taxes,products,products.taxes';
    }else{
        $url = $syncSettings->siteurl.'/wp-json/wc/v3/orders?status=completed&order=asc&orderby=date&per_page=100';
        //$url = 'https://api.foodics.com/v5/orders?filter[status]=4,5&sort=updated_at&include=branch,customer,payments.payment_method,charges,charges.taxes,products,products.taxes';
    }

    //
    /* Init cURL resource */
    $ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';
    //$token = $this->sync_model->getFoodicsSettings()->access_token;
    //$headers[] = "Authorization: Bearer ".$token;

 $headers = array(
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode( $syncSettingsApi->consumer_key . ":" . $syncSettingsApi->consumer_secret ),
 );
 //$syncSettingsApi->consumer_key . ':' . $syncSettingsApi->consumer_secret

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    /* set return type json */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* execute request */
    $result = json_decode(curl_exec($ch));

    /* close cURL resource */
    curl_close($ch);

    //$this->sma->send_json([ 'msg' => $company]);
    $to=0;
    $company = $this->site->getCompanyByID($this->Settings->default_biller);
    //$this->sma->send_json([ 'msg' => $company->id]);
    if($result[0]){

          foreach($result as $dataInfo){


            $to++;
            //$this->sma->send_json([ 'msg' => $dataInfo->billing->first_name]);

            $total_shipping=0;
            $total_tax=0;
            foreach($dataInfo->line_items as $line_item){

                $product_details =  $this->shop_admin_model->getProductByShopID($line_item->product_id) ;

                $unit     = $this->site->getUnitByID($product_details->unit);

            $product = [
                'product_id'        => $product_details->id,
                'product_code'      => $product_details->code,
                'product_name'      => $product_details->name,
                'product_type'      => $product_details->type,
                'option_id'         => null,
                'net_unit_price'    => $line_item->price,
                'unit_price'        => $line_item->price,
                'quantity'          => $line_item->quantity,
                'product_unit_id'   => $unit ? $unit->id : null,
                'product_unit_code' => $unit ? $unit->code : null,
                'unit_quantity'     => $line_item->quantity,
                'warehouse_id'      => $this->Settings->default_warehouse,
                'item_tax'          => 0,
                'tax_rate_id'       => 1,
                'tax'               => $line_item->total_tax,
                //'discount'          => 0,
                'item_discount'     => 0,
                'subtotal'          => $line_item->subtotal,

                'real_unit_price'   => $line_item->price,
                'promo_free'   => 0,
            ];

            $products[] = $product;

        }

            $data           = ['date' => $dataInfo->date_modified,
                'reference_no'        => $dataInfo->order_key,
                'customer_id'         => $dataInfo->customer_id,
                'customer'            => $dataInfo->billing->first_name.' '.$dataInfo->billing->last_name,
                'biller_id'           => $company->id,
                'biller'              => $company->company,
                'warehouse_id'        => $this->Settings->default_warehouse,
                'note'                => $dataInfo->customer_note,
                //'staff_note'          => null,
                'total'               => $dataInfo->total,
                'product_discount'    => $dataInfo->discount_total,
                'order_discount_id'   => '0',//$this->input->post('order_discount'),
                'order_discount'      => $dataInfo->discount_total,
                'total_discount'      => $dataInfo->discount_total,
                'product_tax'         => $dataInfo->total_tax,
                'order_tax'           => $dataInfo->total_tax,
                'total_tax'           => $dataInfo->total_tax,
                'shipping'            => $dataInfo->shipping_total,
                'grand_total'         => $dataInfo->total,
                //'total_items'         => $dataInfo->total_items,
                'sale_status'         => 'completed',
                'payment_status'      => 'pending',
                //'payment_term'        => $payment_term,
                //'due_date'            => $due_date,
                'paid'                => 0,
                'created_by'          => $this->session->userdata('user_id'),
                'hash'                => hash('sha256', microtime() . mt_rand()),
				'supply_date'                => $dataInfo->date_modified,
				//'plate_no'                => $plate_no,
				//'exit_date'                => $exit_date,
				//'currency_code'                => $currencyCode,
				//'currency_rate'                => $this->sma->formatDecimal($currencyRate),
				//'manual_payment'                => $manual_payment,
				//'po_no'                => $po_no,
				//'order_type'                => $ordet_type,
				'uuid'                => $this->site->getUniqeUUID()->UUID,
				//'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				//'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),









            ];
            $payment = [];
            $si_return = [];
            //$this->sma->send_json([ 'msg' => $products]);
            /*if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;
                $data  ['sale_no']=$this->site->getMainBranchesByID($data['brId'])->branch_prefix ? $this->site->getMainBranchesByID($data['brId'])->branch_prefix.'-'.$this->site->getCountSalesBybrid($data['brId'])->count : $this->site->getCountSalesBybrid($data['brId'])->count;

            }*/
            //$this->sma->send_json([ 'msg' => $data]);
            if ($this->sales_model->addSale($data, $products, $payment,$si_return,$payment_type_arr)) {
                $this->shop_admin_model->updateSyncSettings($dataInfo->date_modified);


    }
          }

//            die('<i class="fa fa-check"></i> Success!');
$this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$to.' ) '  ,'per_page'=> '100','total'=> $to,'to'=> $to ]);

            //$this->sma->send_json([ 'msg' => $result->data]);
        }

       // $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$to.' ) '  ,'per_page'=> '100','total'=> $total,'to'=> $to ]);


                //$this->sma->send_json([ 'msg' => $dataFinal]);
                $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> '100','total'=> 0,'to'=> 0 ]);
        //$this->sma->send_json([ 'msg' => $dataFinal]);
       // die('<i class="fa fa-times"></i> Failed!');

    }

    public function get_store_link()
{
    $this->sma->checkPermissions();

    $shop_settings = $this->shop_admin_model->getShopSettings();

    if (empty($shop_settings->token)) {

        $store_name = $this->Settings->site_name;
        $name       = $this->session->userdata('username');

        $user = $this->site->getUser($this->session->userdata('user_id'));
        $password = empty($user->key_p) ? "Admin123" : $user->key_p;

        $db_name = str_replace("cpesoft_", "", $this->db->database);

        $apisarr = $this->restdb
            ->query("SELECT `key` FROM `sma_api_keys` WHERE company='" . $db_name . "' ")
            ->result_array();

        $api_key = $apisarr[0]['key'];

        $email = $db_name . '@' . $db_name . '.com';

        $url = $shop_settings->base_url . '/api/register/store';

        $ch = curl_init($url);

        $headers = [
            'Content-Type:application/json',
            'Accept:application/json'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = json_encode([
            "name" => $name,
            "store_name" => $store_name,
            "email" => $email,
            "password" => $password,
            "password_confirmation" => $password,
            "api_key" => $api_key
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        if (!empty($result->token)) {

            $data1 = [
                'name'       => $name,
                'store_name' => $store_name,
                'email'      => $email,
                'password'   => $password,
                'created_by' => $this->session->userdata('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'token'      => $result->token,
                'store_id'   => $result->store->id,
                'theme_id'   => $result->store->theme_id,
            ];

            $this->shop_admin_model->updateShopSettings($data1);

            $shop_settings->token = $result->token;
        }
    }

    // ðŸ”¥ Ø¬Ù„Ø¨ api_key
    $db_name = str_replace("cpesoft_", "", $this->db->database);

    $apisarr = $this->restdb
        ->query("SELECT `key` FROM `sma_api_keys` WHERE company='" . $db_name . "' ")
        ->result_array();

    $api_key = $apisarr[0]['key'];

    // âœ… ØªÙƒÙˆÙŠÙ† Ø§Ù„Ø±Ø§Ø¨Ø· Ø§Ù„Ù†Ù‡Ø§Ø¦ÙŠ
    $link = $shop_settings->base_url .
        '/login/store/token?token=' . $shop_settings->token .
        '&api_key=' . $api_key;

    // âœ… Ø¥Ø±Ø¬Ø§Ø¹Ù‡ JSON
    echo json_encode([
        'status' => 'success',
        'url'    => $link
    ]);
    }
    
    public function get_store_link_notif()
    {

    $this->db->query("ALTER TABLE sma_settings ADD isStore TINYINT(1) NOT NULL DEFAULT '1' ");
    $this->db->query("ALTER TABLE sma_categories ADD shop_id INT(11) NULL DEFAULT NULL; ");
    $this->db->query("ALTER TABLE sma_products ADD shop_id INT(11) NULL DEFAULT NULL; ");
    $this->db->query("CREATE TABLE IF NOT EXISTS `sma_shop_settings` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `store_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `password` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `created_by` int(11) DEFAULT NULL,  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `base_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  `store_id` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,  `theme_id` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `latest_sync_order` datetime DEFAULT NULL,  `latest_sync_customers` datetime DEFAULT NULL,  `latest_sync_category` datetime DEFAULT NULL,  `latest_sync_products` datetime DEFAULT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; ");
    $this->db->query("INSERT INTO `sma_shop_settings` (`id`,`base_url`) VALUES (1,'https://matjariksa.com'); ");

    $db_name = str_replace("cpesoft_", "", $this->db->database);

    $otherdb = $this->load->database('info', true);
    $otherdb->query("UPDATE `client` SET `isStore` = '1' WHERE `company` = '".$db_name."'; ");

    $this->sma->checkPermissions();

    $shop_settings = $this->shop_admin_model->getShopSettings();


    if (empty($shop_settings->token)) {

        $store_name = $this->Settings->site_name;
        $name       = $this->session->userdata('username');

        $user = $this->site->getUser($this->session->userdata('user_id'));
        $password = empty($user->key_p) ? "Admin123" : $user->key_p;




        $apisarr = $this->restdb
            ->query("SELECT `key` FROM `sma_api_keys` WHERE company='" . $db_name . "' ")
            ->result_array();


        if($apisarr){
            $api_key = $apisarr[0]['key'];
        }else{
            $count = 1;//$this->api_model->getCountofKey($db_name)->count;
            $new_api_key=$this->api_model->generateKey();

            $this->restdb
            ->query("INSERT INTO `sma_api_keys`( `user_id`, `reference`, `key`, `level`, `ignore_limits`, `ip_addresses`, `date_created`, `company`) VALUES ('1','" . $db_name . "_" . $count . "','" . $new_api_key . "','2','1','127.0.0.1','" . time() . "','" . $db_name . "'); ");


            //if ($result > 0) {
                $api_key=$new_api_key;
            //}
        }

        $email = $db_name . '@' . $db_name . '.com';

        $url = $shop_settings->base_url . '/api/register/store';

        $ch = curl_init($url);

        $headers = [
            'Content-Type:application/json',
            'Accept:application/json'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = json_encode([
            "name" => $name,
            "store_name" => $store_name,
            "email" => $email,
            "password" => $password,
            "password_confirmation" => $password,
            "api_key" => $api_key
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        if (!empty($result->token)) {

            $data1 = [
                'name'       => $name,
                'store_name' => $store_name,
                'email'      => $email,
                'password'   => $password,
                'created_by' => $this->session->userdata('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'token'      => $result->token,
                'store_id'   => $result->store->id,
                'theme_id'   => $result->store->theme_id,
            ];

            $this->shop_admin_model->updateShopSettings($data1);

            $shop_settings->token = $result->token;
        }
    }

    // 🔥 جلب api_key
    $db_name = str_replace("cpesoft_", "", $this->db->database);
    $apisarr = $this->restdb
        ->query("SELECT `key` FROM `sma_api_keys` WHERE company='" . $db_name . "' ")
        ->result_array();


        if($apisarr){
            $api_key = $apisarr[0]['key'];
        }else{
            $count = 1;//$this->api_model->getCountofKey($db_name)->count;
            $new_api_key=$this->api_model->generateKey();

            $this->restdb
            ->query("INSERT INTO `sma_api_keys`( `user_id`, `reference`, `key`, `level`, `ignore_limits`, `ip_addresses`, `date_created`, `company`) VALUES ('1','" . $db_name . "_" . $count . "','" . $new_api_key . "','2','1','127.0.0.1','" . time() . "','" . $db_name . "'); ");


            //if ($result > 0) {
                $api_key=$new_api_key;
        }



    // ✅ تكوين الرابط النهائي
    $link = $shop_settings->base_url .
        '/login/store/token?token=' . $shop_settings->token .
        '&api_key=' . $api_key;

    // ✅ إرجاعه JSON
    echo json_encode([
        'status' => 'success',
        'url'    => $link
    ]);
    }
    
    public function generateKey()
    {
        return $this->_generate_key();
    }

    private function _generate_key()
    {
        do {
            $salt = base_convert(bin2hex($this->security->get_random_bytes(64)), 16, 36);

            if ($salt === false) {
                $salt = hash('sha256', time() . mt_rand());
            }

            $new_key = substr($salt, 0, $this->config->item('rest_key_length'));
        } while ($this->_key_exists($new_key));

        return $new_key;
    }

    private function _key_exists($key)
    {
        return $this->restdb
            ->where($this->config->item('rest_key_column'), $key)
            ->count_all_results($this->config->item('rest_keys_table')) > 0;
    } 
    
}
