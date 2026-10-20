<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->db = $this->site->dynamicdb();

        $this->Settings = $this->site->get_setting();





        if ($sma_language = $this->input->cookie('sma_language', true)) {
            $this->config->set_item('language', $sma_language);
            $this->lang->admin_load('sma', $sma_language);
            $this->Settings->user_language = $sma_language;
        } else {
            $this->config->set_item('language', $this->Settings->language);
            $this->lang->admin_load('sma', $this->Settings->language);
            $this->Settings->user_language = $this->Settings->language;
        }
        if ($rtl_support = $this->input->cookie('sma_rtl_support', true)) {
            $this->Settings->user_rtl = $rtl_support;
        } else {
            $this->Settings->user_rtl = $this->Settings->rtl;
        }

        // Spark UI Default State (0/false/null = Legacy default, 1 = Spark UI)
        $spark_ui = (!empty($this->Settings->spark_ui) && $this->Settings->spark_ui == 1) ? 1 : 0;
        $this->spark_ui_enabled = $spark_ui;
        $this->data['spark_ui_enabled'] = $spark_ui;
        if($spark_ui){
            $this->Settings->theme="spark";

            // Central Multi-Brand Resolver (Server-Aware & Whitelisted: 'cpe', 'quroosh')
            $this->brand = $this->_resolve_brand();
            $this->data['brand'] = $this->brand;

            // Dynamic Brand Logo — Light / Dark
            if ($this->brand === 'quroosh') {
                if ($this->Settings->user_language == 'arabic') {
                    $this->Settings->logo2 = 'quroosh_ar.png';
                    $this->Settings->logo2_dark = 'quroosh_ar_white.png';
                } else {
                    $this->Settings->logo2 = 'quroosh_en.png';
                    $this->Settings->logo2_dark = 'quroosh_en_white.png';
                }
            } else {
                if ($this->Settings->user_language == 'arabic') {
                    $this->Settings->logo2 = 'cpe_ar.png';
                    $this->Settings->logo2_dark = 'cpe_ar_white.png';
                } else {
                    $this->Settings->logo2 = 'cpe_en.png';
                    $this->Settings->logo2_dark = 'cpe_en_white.png';
                }
            }
        } else {
            $this->Settings->theme = "default";
        }



        $this->theme = $this->Settings->theme . '/admin/views/';
        if (is_dir(VIEWPATH . $this->Settings->theme . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR)) {
            $this->data['assets'] = base_url() . 'themes/' . $this->Settings->theme . '/admin/assets/';
        } else {
            $this->data['assets'] = base_url() . 'themes/default/admin/assets/';
        }

        $this->data['Settings'] = $this->Settings;
        $this->loggedIn         = $this->sma->logged_in();

        if ($this->loggedIn) {
            $this->default_currency         = $this->site->getCurrencyByCode($this->Settings->default_currency);
            $this->data['default_currency'] = $this->default_currency;
            $this->Owner                    = $this->sma->in_group('owner') ? true : null;
            $this->data['Owner']            = $this->Owner;
            $this->Customer                 = $this->sma->in_group('customer') ? true : null;
            $this->data['Customer']         = $this->Customer;
            $this->Supplier                 = $this->sma->in_group('supplier') ? true : null;
            $this->data['Supplier']         = $this->Supplier;
            $this->Admin                    = $this->sma->in_group('admin') ? true : null;
            $this->data['Admin']            = $this->Admin;

            if ($sd = $this->site->getDateFormat($this->Settings->dateformat)) {
                $dateFormats = [
                    'js_sdate'    => $sd->js,
                    'php_sdate'   => $sd->php,
                    'mysq_sdate'  => $sd->sql,
                    'js_ldate'    => $sd->js . ' hh:ii',
                    'php_ldate'   => $sd->php . ' H:i',
                    'mysql_ldate' => $sd->sql . ' %H:%i',
                ];
            } else {
                $dateFormats = [
                    'js_sdate'    => 'mm-dd-yyyy',
                    'php_sdate'   => 'm-d-Y',
                    'mysq_sdate'  => '%m-%d-%Y',
                    'js_ldate'    => 'mm-dd-yyyy hh:ii:ss',
                    'php_ldate'   => 'm-d-Y H:i:s',
                    'mysql_ldate' => '%m-%d-%Y %T',
                ];
            }
            if (file_exists(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'Pos.php')) {
                define('POS', 1);
            } else {
                define('POS', 0);
            }
            //if (file_exists(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'shop' . DIRECTORY_SEPARATOR . 'Shop.php')) {
            if(isset($this->Settings->isStore) && $this->Settings->isStore==1){
				define('SHOP', 1);
            } else {
                define('SHOP', 0);
            }
            if (!$this->Owner && !$this->Admin) {
                $gp               = $this->site->checkPermissions();
                $this->GP         = $gp[0];
                $this->data['GP'] = $gp[0];
            } else {
                $this->data['GP'] = null;
            }
            $this->dateFormats         = $dateFormats;
            $this->data['dateFormats'] = $dateFormats;
            $this->load->language('calendar');
            //$this->default_currency = $this->Settings->currency_code;
            //$this->data['default_currency'] = $this->default_currency;
            $this->m                    = strtolower($this->router->fetch_class());
            $this->v                    = strtolower($this->router->fetch_method());
            $this->data['m']            = $this->m;
            $this->data['v']            = $this->v;
            $this->data['dt_lang']      = json_encode(lang('datatables_lang'));
            $this->data['dp_lang']      = json_encode(['days' => [lang('cal_sunday'), lang('cal_monday'), lang('cal_tuesday'), lang('cal_wednesday'), lang('cal_thursday'), lang('cal_friday'), lang('cal_saturday'), lang('cal_sunday')], 'daysShort' => [lang('cal_sun'), lang('cal_mon'), lang('cal_tue'), lang('cal_wed'), lang('cal_thu'), lang('cal_fri'), lang('cal_sat'), lang('cal_sun')], 'daysMin' => [lang('cal_su'), lang('cal_mo'), lang('cal_tu'), lang('cal_we'), lang('cal_th'), lang('cal_fr'), lang('cal_sa'), lang('cal_su')], 'months' => [lang('cal_january'), lang('cal_february'), lang('cal_march'), lang('cal_april'), lang('cal_may'), lang('cal_june'), lang('cal_july'), lang('cal_august'), lang('cal_september'), lang('cal_october'), lang('cal_november'), lang('cal_december')], 'monthsShort' => [lang('cal_jan'), lang('cal_feb'), lang('cal_mar'), lang('cal_apr'), lang('cal_may'), lang('cal_jun'), lang('cal_jul'), lang('cal_aug'), lang('cal_sep'), lang('cal_oct'), lang('cal_nov'), lang('cal_dec')], 'today' => lang('today'), 'suffix' => [], 'meridiem' => []]);
            $this->Settings->indian_gst = false;
            if ($this->Settings->invoice_view > 100) {
                $this->Settings->indian_gst = $this->Settings->invoice_view == 2 ? true : false;
                $this->Settings->format_gst = true;
                $this->load->library('gst');
            }
        }
    }

    public function page_construct($page, $meta = [], $data = [],$moduleFolder = '')
    {
        $spark_ui = (!empty($this->spark_ui_enabled) && $this->spark_ui_enabled == 1) ? 1 : 0;
        $meta['spark_ui_enabled'] = $spark_ui;
        $data['spark_ui_enabled'] = $spark_ui;

        $brand = $this->brand ?? 'cpe';
        $meta['brand'] = $brand;
        $data['brand'] = $brand;

        $meta['message']             = $data['message'] ?? $this->session->flashdata('message');
        $meta['error']               = $data['error']   ?? $this->session->flashdata('error');
        $meta['warning']             = $data['warning'] ?? $this->session->flashdata('warning');
        $meta['is_new']                = $this->session->userdata('is_new');
        $meta['info']                = $this->site->getNotifications();
        $meta['events']              = $this->site->getUpcomingEvents();
        $meta['ip_address']          = $this->input->ip_address();
        $meta['Owner']               = $data['Owner'];
        $meta['Admin']               = $data['Admin'];
        $meta['Supplier']            = $data['Supplier'];
        $meta['Customer']            = $data['Customer'];
        $meta['Settings']            = $data['Settings'];
        $meta['dateFormats']         = $data['dateFormats'];
        $meta['assets']              = $data['assets'];
        $meta['GP']                  = $data['GP'];
        $meta['qty_alert_num']       = $this->site->get_total_qty_alerts();
        $meta['exp_alert_num']       = $this->site->get_expiring_qty_alerts();
        $meta['shop_sale_alerts']    = SHOP ? $this->site->get_shop_sale_alerts() : 0;
        $meta['shop_payment_alerts'] = SHOP ? $this->site->get_shop_payment_alerts() : 0;
        //$meta['modules']              = $this->site->getModules();
        //$meta['objects']              = $this->site->getObjects();
        //$this->sma->print_arrays($meta['objects']);

        $this->theme = $this->Settings->theme . '/admin/views/' ;

        if($page=='sales/view_2' || $page=='sales/page_404' || $page=='quotes/view_2'){
            if($page=='sales/view_2' || $page=='quotes/view_2'){
                $this->load->view($this->theme . $moduleFolder  .'header_2', $meta);
            }else{
                if($page=='sales/page_404'){
                    $this->load->view($this->theme . $moduleFolder  .'header', $meta);
                }
            }
            $this->load->view($this->theme  . $page, $data,false,$moduleFolder);
            $this->load->view($this->theme . $moduleFolder . 'footer_2');

        }else{
            $this->load->view($this->theme . $moduleFolder  .'header', $meta);
            $this->load->view($this->theme  . $page, $data,false,$moduleFolder);
            $this->load->view($this->theme . $moduleFolder . 'footer');
        }

    }

    /* Media Lib */

    protected function load_view($data, $layout_file = 'layout'){

        $this->load->view($layout_file, $data);
    }

    protected function init_pagination($base_url, $total_rows,$page_record_limit = PAGE_RECORD_LIMIT){
            $this->load->library('pagination');
            $config = array();
            $config['base_url'] = $base_url;
            $config['page_query_string'] = true;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $page_record_limit;
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['first_link'] = false;
            $config['last_link'] = false;
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
            $config['prev_link'] = '&laquo';
            $config['prev_tag_open'] ='<li class="prev">';
            $config['prev_tag_close'] ='</li>';
            $config['next_link'] ='&raquo';
            $config['next_tag_open'] ='<li>';
            $config['next_tag_close'] ='</li>';
            $config['last_tag_open'] ='<li>';
            $config['last_tag_close'] ='</li>';
            $config['cur_tag_open'] ='<li class="active"><a href="#">';
            $config['cur_tag_close'] ='</a></li>';
            $config['num_tag_open'] ='<li>';
            $config['num_tag_close'] ='</li>';
            $this->pagination->initialize($config);
            return $config;
    }


    protected function getQueryString($excludeParams=[]){
        $getVals = $this->input->get();
        $string = [];
        foreach ($getVals as $keys => $vals){
            if($keys != 'per_page'){
                if(!in_array($keys, $excludeParams)){
                    $string[] = $keys.'='.$vals;
                }
            }
        }
        return empty($string)?'':'?'.  implode('&', $string);
    }

    /**
     * Central Multi-Brand Resolver (Server-Aware)
     *
     * Hierarchy:
     * 1. Explicit valid URL parameter ?brand= (updates session)
     * 2. Existing valid Session brand
     * 3. Server/Environment configured default brand (SPARK_DEFAULT_BRAND)
     * 4. Hard fallback = 'cpe'
     *
     * @return string Whitelisted brand ('cpe' | 'quroosh')
     */
    protected function _resolve_brand()
    {
        $allowed_brands = ['cpe', 'quroosh'];

        // Priority 3: Resolve Server Default Brand
        $server_default = 'cpe';
        $configured_brand = null;

        if (defined('SPARK_DEFAULT_BRAND') && SPARK_DEFAULT_BRAND) {
            $configured_brand = SPARK_DEFAULT_BRAND;
        } elseif (!empty($_SERVER['SPARK_DEFAULT_BRAND'])) {
            $configured_brand = $_SERVER['SPARK_DEFAULT_BRAND'];
        } elseif (!empty($_ENV['SPARK_DEFAULT_BRAND'])) {
            $configured_brand = $_ENV['SPARK_DEFAULT_BRAND'];
        } elseif (getenv('SPARK_DEFAULT_BRAND')) {
            $configured_brand = getenv('SPARK_DEFAULT_BRAND');
        } elseif ($this->config->item('spark_default_brand')) {
            $configured_brand = $this->config->item('spark_default_brand');
        }

        if ($configured_brand !== null) {
            $configured_brand = strtolower(trim((string)$configured_brand));
            if (in_array($configured_brand, $allowed_brands, true)) {
                $server_default = $configured_brand;
            }
        }

        $brand = null;

        // Priority 1: Explicit URL parameter ?brand=
        if ($this->input->get('brand') !== null) {
            $req_brand = strtolower(trim((string)$this->input->get('brand')));
            if (in_array($req_brand, $allowed_brands, true)) {
                $brand = $req_brand;
                $this->session->set_userdata('spark_brand', $brand);
            }
            // Invalid URL parameter is safely ignored (does not overwrite session)
        }

        // Priority 2: Existing valid Session brand
        if ($brand === null && $this->session->userdata('spark_brand')) {
            $session_brand = strtolower(trim((string)$this->session->userdata('spark_brand')));
            if (in_array($session_brand, $allowed_brands, true)) {
                $brand = $session_brand;
            } else {
                $this->session->unset_userdata('spark_brand');
            }
        }

        // Priority 3 & 4: Server Default or Hard Fallback ('cpe')
        if ($brand === null) {
            $brand = $server_default;
        }

        return $brand;
    }
}
