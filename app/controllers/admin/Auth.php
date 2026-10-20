<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->admin_load('auth', $this->Settings->user_language);
        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->load->admin_model('auth_model');
        $this->load->admin_model('reports_model');
        $this->load->library('ion_auth');

    }

    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return [$key => $value];
    }

    public function _render_page($view, $data = null, $render = false)
    {
        $this->viewdata = (empty($data)) ? $this->data : $data;
        $view_html      = $this->load->view('header', $this->viewdata, $render);
        $view_html .= $this->load->view($view, $this->viewdata, $render);
        $view_html = $this->load->view('footer', $this->viewdata, $render);

        if (!$render) {
            return $view_html;
        }
    }

    public function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== false && $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function activate($id, $code = false)
    {
        if ($code !== false) {
            $activation = $this->ion_auth->activate($id, $code);
        } elseif ($this->Owner) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            if ($this->Owner) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                admin_redirect('auth/login');
            }
        } else {
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            admin_redirect('forgot_password');
        }
    }

    public function captcha_check($cap)
    {
        $expiration = time() - 300; // 5 minutes limit
        $this->db->delete('captcha', ['captcha_time <' => $expiration]);

        $this->db->select('COUNT(*) AS count')
            ->where('word', $cap)
            ->where('ip_address', $this->input->ip_address())
            ->where('captcha_time >', $expiration);

        if ($this->db->count_all_results('captcha')) {
            return true;
        } else {
            $this->form_validation->set_message('captcha_check', lang('captcha_wrong'));
            return false;
        }
    }

    public function change_password()
    {
        if (!$this->ion_auth->logged_in()) {
            admin_redirect('login');
        }


        $this->form_validation->set_rules('old_password', lang('old_password'), 'required');
        //$this->form_validation->set_rules('new_password', lang('new_password'), 'required|min_length[8]|max_length[25]');
        $this->form_validation->set_rules('new_password', lang('new_password'), 'required');
        $this->form_validation->set_rules('new_password_confirm', lang('confirm_password'), 'required|matches[new_password]');

        $user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('auth/profile/' . $user->id . '/#cpassword');
        } else {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

            $change = $this->ion_auth->change_password($identity, $this->input->post('old_password'), $this->input->post('new_password'));

            if ($change) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                admin_redirect('auth/profile/' . $user->id . '/#cpassword');
            }
        }
    }

    public function create_user()
    {
        $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);

        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->data['title'] = 'Create User';
        $this->form_validation->set_rules('username', lang('username'), 'trim|is_unique[users.username]');
        $this->form_validation->set_rules('email', lang('email'), 'trim|is_unique[users.email]');
        $this->form_validation->set_rules('status', lang('status'), 'trim|required');
        $this->form_validation->set_rules('group', lang('group'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $username = strtolower($this->input->post('username'));
            $email    = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $notify   = $this->input->post('notify');

            $additional_data = [
                'first_name'     => $this->input->post('first_name'),
                'last_name'      => $this->input->post('last_name'),
                'company'        => $this->input->post('company'),
                'phone'          => $this->input->post('phone'),
                'gender'         => $this->input->post('gender'),
                'group_id'       => $this->input->post('group') ? $this->input->post('group') : '3',
                'biller_id'      => $this->input->post('biller'),
                'warehouse_id'   => $this->input->post('warehouse'),
                'view_right'     => $this->input->post('view_right'),
                'edit_right'     => $this->input->post('edit_right'),
                'show_price'     => $this->input->post('show_price'),
                'allow_discount' => $this->input->post('allow_discount'),
                'edit_price' => $this->input->post('edit_price'),
                'show_sales' => $this->input->post('show_sales'),
                'show_cost' => $this->input->post('show_cost'),
                'max_discount'       => $this->input->post('max_discount') ? $this->input->post('max_discount') : 0,
                'isRecepion'       => $this->input->post('isRecepion') ? $this->input->post('isRecepion') : 0,
                'isPrintInvoices'       => $this->input->post('isPrintInvoices') ? $this->input->post('isPrintInvoices') : 0,
                'isSupervisor'       => $this->input->post('isSupervisor') ? $this->input->post('isSupervisor') : 0,
                'CanPrintCashierClose'       => $this->input->post('CanPrintCashierClose') ? $this->input->post('CanPrintCashierClose') : 0,
                'CanSeeCashierClose'       => $this->input->post('CanSeeCashierClose') ? $this->input->post('CanSeeCashierClose') : 0,
                'showpos_dashboard'       => $this->input->post('showpos_dashboard') ? $this->input->post('showpos_dashboard') : 0,
                'key_p' => $this->input->post('password'),
                'salesman_id'      => $this->input->post('salesman_id'),
                'waiter_id'      => $this->input->post('waiter_id') ?  $this->input->post('waiter_id') : null,


            ];
            $active = $this->input->post('status');
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $additional_data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $additional_data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $additional_data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }

            //$this->sma->print_arrays($additional_data,$_POST);

        }

        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $active, $notify)) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            admin_redirect('auth/users');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error')));
            $this->data['groups']     = $this->ion_auth->groups()->result_array();

            $this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();
            $this->data['allWaiters'] = $this->site->getAllWaiters();


        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);


             if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                $this->data['warehouses']      = $this->site->getAllShowrooms();
            }else{
                $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
            }

            $this->data['billers']    = $this->input->post('brId') ? $this->site->getBillerBybrId($this->input->post('brId')) : $this->site->getBillerBybrId($this->session->userdata('brId'));

        }else{

            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                $this->data['warehouses']      = $this->site->getAllShowrooms();
            }else{
                $this->data['warehouses'] = $this->site->getAllWarehouses();
            }

            $this->data['billers']    = $this->site->getAllCompanies('biller');
        }

            $bc                       = [['link' => admin_url('home'), 'page' => lang('home')], ['link' => admin_url('auth/users'), 'page' => lang('users')], ['link' => '#', 'page' => lang('create_user')]];
            $meta                     = ['page_title' => lang('users'), 'bc' => $bc];
            $this->page_construct('auth/create_user', $meta, $this->data);
        }
    }

    public function deactivate($id = null)
    {
        $this->sma->checkPermissions('users', true);
        $id = $this->config->item('use_mongodb', 'ion_auth') ? (string)$id : (int)$id;
        $this->form_validation->set_rules('confirm', lang('confirm'), 'required');

        if ($this->form_validation->run() == false) {
            if ($this->input->post('deactivate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->data['csrf']     = $this->_get_csrf_nonce();
                $this->data['user']     = $this->ion_auth->user($id)->row();
                $this->data['modal_js'] = $this->site->modal_js();
                $this->load->view($this->theme . 'auth/deactivate_user', $this->data);
            }
        } else {
            if ($this->input->post('confirm') == 'yes') {
                if ($id != $this->input->post('id')) {
                    show_error(lang('error_csrf'));
                }

                if ($this->ion_auth->logged_in() && $this->Owner) {
                    if ($id == $this->session->userdata('user_id')) {
                        $this->session->set_flashdata('error', lang('You_cannot_deactivate_your_account'));
                    }else{
                    $this->ion_auth->deactivate($id);
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    }
                }
            }

            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete($id = null)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if (!$this->Owner || $id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin/welcome');
        }

        if ($this->auth_model->delete_user($id)) {
            //echo lang("user_deleted");
            $this->session->set_flashdata('message', 'user_deleted');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_avatar($id = null, $avatar = null)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group('owner') && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . $_SERVER['HTTP_REFERER'] . "'; }, 0);</script>");
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            unlink('assets/uploads/avatars/' . $avatar);
            unlink('assets/uploads/avatars/thumbs/' . $avatar);
            if ($id == $this->session->userdata('user_id')) {
                $this->session->unset_userdata('avatar');
            }
            $this->db->update('users', ['avatar' => null], ['id' => $id]);
            $this->session->set_flashdata('message', lang('avatar_deleted'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . $_SERVER['HTTP_REFERER'] . "'; }, 0);</script>");
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function edit_user($id = null)
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $this->data['title'] = lang('edit_user');

        if (!$this->loggedIn || !$this->Owner && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $user = $this->ion_auth->user($id)->row();

        if ($user->username != $this->input->post('username')) {
            $this->form_validation->set_rules('username', lang('username'), 'trim|is_unique[users.username]');
        }

        if ($this->input->post('group') > 4) {
            if ($user->biller_id != $this->input->post('biller')) {
                $this->form_validation->set_rules('biller', lang('biller'), 'trim|is_unique[users.biller_id]');
            }
        }

        if ($user->email != $this->input->post('email')) {
            $this->form_validation->set_rules('email', lang('email'), 'trim|is_unique[users.email]');
        }

        if ($this->form_validation->run() === true) {
            if ($this->Owner) {
                if ($id == $this->session->userdata('user_id')) {
                    $data = [
                        'first_name' => $this->input->post('first_name'),
                        'last_name'  => $this->input->post('last_name'),
                        'company'    => $this->input->post('company'),
                        'phone'      => $this->input->post('phone'),
                        'gender'     => $this->input->post('gender'),

                    ];
                } elseif ($this->ion_auth->in_group('customer', $id) || $this->ion_auth->in_group('supplier', $id)) {
                    $data = [
                        'first_name' => $this->input->post('first_name'),
                        'last_name'  => $this->input->post('last_name'),
                        'company'    => $this->input->post('company'),
                        'phone'      => $this->input->post('phone'),
                        'gender'     => $this->input->post('gender'),

                    ];
                } else {
                    $data = [
                        'first_name'     => $this->input->post('first_name'),
                        'last_name'      => $this->input->post('last_name'),
                        'company'        => $this->input->post('company'),
                        'username'       => $this->input->post('username'),
                        'email'          => $this->input->post('email'),
                        'phone'          => $this->input->post('phone'),
                        'gender'         => $this->input->post('gender'),
                        'active'         => $this->input->post('status'),
                        'group_id'       => $this->input->post('group'),
                        'biller_id'      => $this->input->post('biller') ? $this->input->post('biller') : null,
                        'warehouse_id'   => $this->input->post('warehouse') ? $this->input->post('warehouse') : null,
                        'award_points'   => $this->input->post('award_points'),
                        'view_right'     => $this->input->post('view_right'),
                        'edit_right'     => $this->input->post('edit_right'),
                        'show_price'     => $this->input->post('show_price'),
                        'allow_discount' => $this->input->post('allow_discount'),
						'edit_price' => $this->input->post('edit_price'),
						'show_sales' => $this->input->post('show_sales'),
                        'show_cost' => $this->input->post('show_cost'),
                        'max_discount'       => $this->input->post('max_discount') ? $this->input->post('max_discount') : 0,
                        'isRecepion'       => $this->input->post('isRecepion') ? $this->input->post('isRecepion') : 0,
                        'isPrintInvoices'       => $this->input->post('isPrintInvoices') ? $this->input->post('isPrintInvoices') : 0,
                        'isSupervisor'       => $this->input->post('isSupervisor') ? $this->input->post('isSupervisor') : 0,
                        'CanPrintCashierClose'       => $this->input->post('CanPrintCashierClose') ? $this->input->post('CanPrintCashierClose') : 0,
                        'CanSeeCashierClose'       => $this->input->post('CanSeeCashierClose') ? $this->input->post('CanSeeCashierClose') : 0,
                        'showpos_dashboard'       => $this->input->post('showpos_dashboard') ? $this->input->post('showpos_dashboard') : 0,
                        'grpId'           => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
                        'coId'           => $this->input->post('coId') ? $this->input->post('coId') : 1,
                        'brId'           => $this->input->post('brId'),
                        'updated_on'           => time(),
                        'salesman_id'      => $this->input->post('salesman_id') ? $this->input->post('salesman_id') : null,
                        'waiter_id'      => $this->input->post('waiter_id') ?  $this->input->post('waiter_id') : null,



                    ];
                }
            } elseif ($this->Admin) {
                $data = [
                    'first_name'   => $this->input->post('first_name'),
                    'last_name'    => $this->input->post('last_name'),
                    'company'      => $this->input->post('company'),
                    'phone'        => $this->input->post('phone'),
                    'gender'       => $this->input->post('gender'),
                    'active'       => $this->input->post('status'),
                    'award_points' => $this->input->post('award_points'),

                ];
            } else {
                $data = [
                    'first_name' => $this->input->post('first_name'),
                    'last_name'  => $this->input->post('last_name'),
                    'company'    => $this->input->post('company'),
                    'phone'      => $this->input->post('phone'),
                    'gender'     => $this->input->post('gender'),

                ];
            }

            if ($this->Owner) {
                if ($this->input->post('password')) {
                    if (DEMO) {
                        $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                    $this->form_validation->set_rules('password', lang('edit_user_validation_password_label'), 'required|max_length[25]|matches[password_confirm]');
                    $this->form_validation->set_rules('password_confirm', lang('edit_user_validation_password_confirm_label'), 'required');

                    $data['password'] = $this->input->post('password');
					$data['key_p'] = $this->input->post('password');


                }

            }
            //$this->sma->print_arrays($data);
        }



        if ($this->form_validation->run() === true && $this->ion_auth->update($user->id, $data)) {
            $this->session->set_flashdata('message', lang('user_updated'));
            admin_redirect('auth/profile/' . $id);
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function forgot_password()
    {
        $this->form_validation->set_rules('forgot_email', lang('email_address'), 'required|valid_email');

        if ($this->form_validation->run() == false) {
            $error = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $error);
            admin_redirect('login#forgot_password');
        } else {
            $identity = $this->ion_auth->where('email', strtolower($this->input->post('forgot_email')))->users()->row();

            if (empty($identity)) {
                $this->ion_auth->set_message('forgot_password_email_not_found');
                $this->session->set_flashdata('error', $this->ion_auth->messages());
                admin_redirect('login#forgot_password');
            }

            $forgotten = $this->ion_auth->forgotten_password($identity->email);
			//$this->sma->print_arrays($forgotten);


            if ($forgotten) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                admin_redirect('login#forgot_password');
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                admin_redirect('login#forgot_password');
            }
        }
    }

    public function getUserLogins($id = null)
    {
        if (!$this->ion_auth->in_group(['owner', 'admin'])) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->load->library('datatables');
        $this->datatables
            ->select('login, ip_address, time')
            ->from('user_logins')
            ->where('user_id', $id);

        echo $this->datatables->generate();
    }

    public function getUsers()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            $this->sma->md();
        }

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('users') . '.id as id, first_name, last_name, email, company, award_points, ' . $this->db->dbprefix('groups') . '.name, active')
            ->from('users')
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('company_id', null)
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('auth/profile/$1') . "' class='tip' title='" . lang('edit_user') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');

        if (!$this->Owner) {
            $this->datatables->unset_column('id');
        }
        echo $this->datatables->generate();
    }

    public function index()
    {
        if (!$this->loggedIn) {
            admin_redirect('login');
        } else {
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
    public function language($lang = false)
    {
        if ($this->input->get('lang')) {
            $lang = $this->input->get('lang');
        }

        $allowed_languages = ['arabic', 'english'];
        if (is_string($lang) && in_array($lang, $allowed_languages, true)) {
            $folder        = 'app/language/';
            $languagefiles = scandir($folder);
            if (in_array($lang, $languagefiles, true)) {
                $cookie = [
                    'name'   => 'language',
                    'value'  => $lang,
                    'expire' => '31536000',
                    'prefix' => 'sma_',
                    'secure' => false,
                ];
                $this->input->set_cookie($cookie);

                $rtl_cookie = [
                    'name'   => 'rtl_support',
                    'value'  => ($lang === 'arabic' ? '1' : '0'),
                    'expire' => '31536000',
                    'prefix' => 'sma_',
                    'secure' => false,
                ];
                $this->input->set_cookie($rtl_cookie);
            }
        }

        $referrer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url('login');
        $base_host = parse_url(base_url(), PHP_URL_HOST);
        $ref_host  = parse_url($referrer, PHP_URL_HOST);
        if ($ref_host && $base_host && strtolower($ref_host) !== strtolower($base_host)) {
            $referrer = admin_url('login');
        }

        redirect($referrer);
    }

    public function login($m = null)
    {



        if ($this->loggedIn && !isset( $_GET['code']) && !isset($_POST['foodics_access_token'])) {
            $this->session->set_flashdata('error', $this->session->flashdata('error'));
            admin_redirect('welcome');
        }
        $this->data['title'] = lang('login');

        if ($this->Settings->captcha) {
            $this->form_validation->set_rules('captcha', lang('captcha'), 'required|callback_captcha_check');
        }

        if ($this->form_validation->run() == true) {

            $remember = (bool)$this->input->post('remember');
			 $company = $this->input->post('company');
            $otherdb = $this->load->database('info', true);
            $check = $otherdb->where('company', $company)->get('client')->row();

			if ($check)
			{
                if ($check->status == 0) {
                    $x = lang('Your_account_is_deactivated_please_payment_and_login_again');
                    $this->session->set_flashdata('error', $x);
                    admin_redirect('login');
                }


                if ($check->isQuroosh == 1) {
                    redirect("https://domain2.net/admin/login");
                }

                //Database::setDatabase($check->company);
                $this->db = $this->site->posdynamicdb($check->company);

                $newdata = array(
                    'company' => $check->company,
                    'date_of_payment' => $check->date_of_payment,
                    'number_of_payment' => $check->number_of_payment,
                    'client_status' => $check->status,
                    'client_name' => $check->name,

                );
                $this->session->set_userdata($newdata);
                $x = $this->session->userdata('company');
                if ($check->company_type==9 )  {
                    $this->data['post'] = $_POST;
                    $this->load->view($this->theme . 'auth/login_hr', $this->data);
                    //$this->sma->print_arrays($_POST);
                }else{

				if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
					if ($this->Settings->mmode) {
						if (!$this->ion_auth->in_group('owner')) {
							$this->session->set_flashdata('error', lang('site_is_offline_plz_try_later'));
							admin_redirect('auth/logout');
						}
					}
					if ($this->ion_auth->in_group('customer') || $this->ion_auth->in_group('supplier')) {
						if (file_exists(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'shop' . DIRECTORY_SEPARATOR . 'Shop.php')) {
							$this->session->set_flashdata('message', $this->ion_auth->messages());
							redirect(base_url());
						} else {
							admin_redirect('auth/logout/1');
						}
					}

                    if ($this->input->post('foodics_access_token') )  {
                        $access_token = $this->input->post('foodics_access_token');
                        $this->db->query("UPDATE sma_foodics_settings SET access_token='".$access_token."' where id=1 ");

                        $this->session->set_flashdata('message', lang('Subscription_completed_successfully'));
					    admin_redirect('sync/electronic_linking');
                    }else{

                        //$this->db->cachedir='app/cache/'.$check->company.'/'.$this->input->post('identity');
                        //$this->sma->print_arrays($this->db->cachedir);
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        $referrer = ($this->session->userdata('requested_page') && $this->session->userdata('requested_page') != 'admin') ? $this->session->userdata('requested_page') : 'welcome';
                        admin_redirect($referrer);
                    }




				}
				else
				{

                    if($check->isHR==1 && ($this->input->post('identity')!='admin') && ($company!='arkanpro')){

                        $company="hr_".$check->company;
				        $this->db2 = $this->dynamicdbhr($company);
				        $this->db2->select('*');
        		        $this->db2->from('user');
        		        $this->db2->where('username', $this->input->post('identity'));
        		        $user = $this->db2->get()->row();



				        $salt = substr($user->password_hash, 0, 10);
                    	$db_password = $salt . substr(sha1($salt . $this->input->post('password')), 0, -10);

		        		if ($db_password == $user->password_hash) {
				        	$user =  $user;
        				} else {
		        			$user =  array();
				        }
                        //$this->sma->print_arrays($user,$this->input->post('password'));

        				if(!empty($user)){
		        			$this->data['post'] = $_POST;
                            $this->load->view($this->theme . 'auth/login_hr', $this->data);
				        }else{
                            $this->session->set_flashdata('error', $this->ion_auth->errors());
                            admin_redirect('login');
                        }

                    }else{
                        $this->session->set_flashdata('error', $this->ion_auth->errors());
                        admin_redirect('login');
                    }
				}
                }
			}
			else
			{
                $x = lang('Your_Company_name_incorrect');
                $this->session->set_flashdata('error', $x);
                admin_redirect('login');
            }

        } else {
            $this->data['error']   = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['message'] = $this->session->flashdata('message');
            if ($this->Settings->captcha) {
                $this->load->helper('captcha');
                $vals = [
                    'img_path'    => './assets/captcha/',
                    'img_url'     => base_url('assets/captcha/'),
                    'img_width'   => 150,
                    'img_height'  => 34,
                    'word_length' => 5,
                    'colors'      => ['background' => [255, 255, 255], 'border' => [204, 204, 204], 'text' => [102, 102, 102], 'grid' => [204, 204, 204]],
                ];
                $cap     = create_captcha($vals);
                $capdata = [
                    'captcha_time' => $cap['time'],
                    'ip_address'   => $this->input->ip_address(),
                    'word'         => $cap['word'],
                ];

                $query = $this->db->insert_string('captcha', $capdata);
                $this->db->query($query);
                $this->data['image']   = $cap['image'];
                $this->data['captcha'] = ['name' => 'captcha',
                    'id'                         => 'captcha',
                    'type'                       => 'text',
                    'class'                      => 'form-control',
                    'required'                   => 'required',
                    'placeholder'                => lang('type_captcha'),
                ];
            }

            $this->data['identity'] = ['name' => 'identity',
                'id'                          => 'identity',
                'type'                        => 'text',
                'class'                       => 'form-control',
                'placeholder'                 => lang('email'),
                'value'                       => $this->form_validation->set_value('identity'),
            ];
            $this->data['password'] = ['name' => 'password',
                'id'                          => 'password',
                'type'                        => 'password',
                'class'                       => 'form-control',
                'required'                    => 'required',
                'placeholder'                 => lang('password'),
            ];
            $this->data['allow_reg'] = $this->Settings->allow_reg;
            if ($m == 'db') {
                $this->data['message'] = lang('db_restored');
            } elseif ($m) {
                $this->data['error'] = lang('we_are_sorry_as_this_sction_is_still_under_development.');
            }

            $this->load->view($this->theme . 'auth/login', $this->data);
        }
    }

    public function logout($m = null)
    {
        //$this->db->cachedir='app/cache/'.$check->company.'/'.$this->input->post('identity');
        //$this->db->cache_delete_all();
        $logout = $this->ion_auth->logout();
        $this->session->set_flashdata('message', $this->ion_auth->messages());

        admin_redirect('login/' . $m);
    }

    public function profile($id = null)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group('owner') && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin');
        }
        if (!$id || empty($id)) {
            admin_redirect('auth');
        }

        $this->data['title'] = lang('profile');

        $user                     = $this->ion_auth->user($id)->row();
        //$this->sma->print_arrays($user);
        $groups                   = $this->ion_auth->groups()->result_array();
        $this->data['csrf']       = $this->_get_csrf_nonce();
        $this->data['user']       = $user;
        $this->data['groups']     = $groups;


        $this->data['error']    = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['password'] = [
            'name'  => 'password',
            'id'    => 'password',
            'class' => 'form-control',
            'type'  => 'password',
            'value' => '',
        ];
        $this->data['password_confirm'] = [
            'name'  => 'password_confirm',
            'id'    => 'password_confirm',
            'class' => 'form-control',
            'type'  => 'password',
            'value' => '',
        ];
        $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
        $this->data['old_password']        = [
            'name'  => 'old',
            'id'    => 'old',
            'class' => 'form-control',
            'type'  => 'password',
        ];
        $this->data['new_password'] = [
            'name'    => 'new',
            'id'      => 'new',
            'type'    => 'password',
            'class'   => 'form-control',
            'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
        ];
        $this->data['new_password_confirm'] = [
            'name'    => 'new_confirm',
            'id'      => 'new_confirm',
            'type'    => 'password',
            'class'   => 'form-control',
            'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
        ];
        $this->data['user_id'] = [
            'name'  => 'user_id',
            'id'    => 'user_id',
            'type'  => 'hidden',
            'value' => $user->id,
        ];

        $this->data['id'] = $id;

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies();
            $this->data['mainBranches'] = $this->site->getMainBranches();
            $this->data['billers']    = $this->site->getBillersBybrId($user->brId);
            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                $this->data['warehouses']      = $this->site->getAllShowrooms();
            }else{
                $this->data['warehouses'] = $this->site->getWarehousesBybrId($user->brId);
            }

        }else{
            $this->data['billers']    = $this->site->getAllCompanies('biller');
            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                $this->data['warehouses']      = $this->site->getAllShowrooms();
            }else{
                $this->data['warehouses'] = $this->site->getAllWarehouses();
            }

        }
        $this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();
        $this->data['allWaiters'] = $this->site->getAllWaiters();
        $this->data['modal_js']        = $this->site->modal_js();
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('auth/users'), 'page' => lang('users')], ['link' => '#', 'page' => lang('profile')]];
        $meta = ['page_title' => lang('profile'), 'bc' => $bc];
        $this->page_construct('auth/profile', $meta, $this->data);
    }

    public function register()
    {
        $this->data['title'] = 'Register';
        if (!$this->allow_reg) {
            $this->session->set_flashdata('error', lang('registration_is_disabled'));
            admin_redirect('login');
        }

        $this->form_validation->set_message('is_unique', lang('account_exists'));
        $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
        $this->form_validation->set_rules('email', lang('email_address'), 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('usernam', lang('usernam'), 'required|is_unique[users.username]');
        $this->form_validation->set_rules('password', lang('password'), 'required|min_length[8]|max_length[25]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', lang('confirm_password'), 'required');
        if ($this->Settings->captcha) {
            $this->form_validation->set_rules('captcha', lang('captcha'), 'required|callback_captcha_check');
        }

        if ($this->form_validation->run() == true) {
            $username = strtolower($this->input->post('username'));
            $email    = strtolower($this->input->post('email'));
            $password = $this->input->post('password');

            $additional_data = [
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'company'    => $this->input->post('company'),
                'phone'      => $this->input->post('phone'),
            ];
        }
        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data)) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            admin_redirect('login');
        } else {
            $this->data['error']  = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error')));
            $this->data['groups'] = $this->ion_auth->groups()->result_array();

            $this->load->helper('captcha');
            $vals = [
                'img_path'   => './assets/captcha/',
                'img_url'    => admin_url() . 'assets/captcha/',
                'img_width'  => 150,
                'img_height' => 34,
            ];
            $cap     = create_captcha($vals);
            $capdata = [
                'captcha_time' => $cap['time'],
                'ip_address'   => $this->input->ip_address(),
                'word'         => $cap['word'],
            ];

            $query = $this->db->insert_string('captcha', $capdata);
            $this->db->query($query);
            $this->data['image']   = $cap['image'];
            $this->data['captcha'] = ['name' => 'captcha',
                'id'                         => 'captcha',
                'type'                       => 'text',
                'class'                      => 'form-control',
                'placeholder'                => lang('type_captcha'),
            ];

            $this->data['first_name'] = [
                'name'     => 'first_name',
                'id'       => 'first_name',
                'type'     => 'text',
                'class'    => 'form-control',
                'required' => 'required',
                'value'    => $this->form_validation->set_value('first_name'),
            ];
            $this->data['last_name'] = [
                'name'     => 'last_name',
                'id'       => 'last_name',
                'type'     => 'text',
                'required' => 'required',
                'class'    => 'form-control',
                'value'    => $this->form_validation->set_value('last_name'),
            ];
            $this->data['email'] = [
                'name'     => 'email',
                'id'       => 'email',
                'type'     => 'text',
                'required' => 'required',
                'class'    => 'form-control',
                'value'    => $this->form_validation->set_value('email'),
            ];
            $this->data['company'] = [
                'name'     => 'company',
                'id'       => 'company',
                'type'     => 'text',
                'required' => 'required',
                'class'    => 'form-control',
                'value'    => $this->form_validation->set_value('company'),
            ];
            $this->data['phone'] = [
                'name'     => 'phone',
                'id'       => 'phone',
                'type'     => 'text',
                'required' => 'required',
                'class'    => 'form-control',
                'value'    => $this->form_validation->set_value('phone'),
            ];
            $this->data['password'] = [
                'name'     => 'password',
                'id'       => 'password',
                'type'     => 'password',
                'required' => 'required',
                'class'    => 'form-control',
                'value'    => $this->form_validation->set_value('password'),
            ];
            $this->data['password_confirm'] = [
                'name'     => 'password_confirm',
                'id'       => 'password_confirm',
                'type'     => 'password',
                'required' => 'required',
                'class'    => 'form-control',
                'value'    => $this->form_validation->set_value('password_confirm'),
            ];

            $this->load->view('auth/register', $this->data);
        }
    }

    public function reload_captcha()
    {
        $this->load->helper('captcha');
        $vals = [
            'img_path'    => './assets/captcha/',
            'img_url'     => base_url('assets/captcha/'),
            'img_width'   => 150,
            'img_height'  => 34,
            'word_length' => 5,
            'colors'      => ['background' => [255, 255, 255], 'border' => [204, 204, 204], 'text' => [102, 102, 102], 'grid' => [204, 204, 204]],
        ];
        $cap     = create_captcha($vals);
        $capdata = [
            'captcha_time' => $cap['time'],
            'ip_address'   => $this->input->ip_address(),
            'word'         => $cap['word'],
        ];
        $query = $this->db->insert_string('captcha', $capdata);
        $this->db->query($query);
        //$this->data['image'] = $cap['image'];

        echo $cap['image'];
    }

    public function reset_password($code = null)
    {
        if (!$code) {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user) {
            $this->form_validation->set_rules('new', lang('password'), 'required|min_length[8]|max_length[25]|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', lang('confirm_password'), 'required');

            if ($this->form_validation->run() == false) {
                $this->data['error']               = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->data['message']             = $this->session->flashdata('message');
                $this->data['title']               = lang('reset_password');
                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password']        = [
                    'name'                   => 'new',
                    'id'                     => 'new',
                    'type'                   => 'password',
                    'class'                  => 'form-control',
                    'pattern'                => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}',
                    'data-bv-regexp-message' => lang('pasword_hint'),
                    'placeholder'            => lang('new_password'),
                ];
                $this->data['new_password_confirm'] = [
                    'name'                      => 'new_confirm',
                    'id'                        => 'new_confirm',
                    'type'                      => 'password',
                    'class'                     => 'form-control',
                    'data-bv-identical'         => 'true',
                    'data-bv-identical-field'   => 'new',
                    'data-bv-identical-message' => lang('pw_not_same'),
                    'placeholder'               => lang('confirm_password'),
                ];
                $this->data['user_id'] = [
                    'name'  => 'user_id',
                    'id'    => 'user_id',
                    'type'  => 'hidden',
                    'value' => $user->id,
                ];
                $this->data['csrf']           = $this->_get_csrf_nonce();
                $this->data['code']           = $code;
                $this->data['identity_label'] = $user->email;
                //render
                $this->load->view($this->theme . 'auth/reset_password', $this->data);
            } else {
                // do we have a valid request?
                if ($user->id != $this->input->post('user_id')) {
                    //something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($code);
                    show_error(lang('error_csrf'));
                } else {
                    // finally change the password
                    $identity = $user->email;

                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change) {
                        //if the password was successfully changed
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        //$this->logout();
                        admin_redirect('login');
                    } else {
                        $this->session->set_flashdata('error', $this->ion_auth->errors());
                        admin_redirect('auth/reset_password/' . $code);
                    }
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            admin_redirect('login#forgot_password');
        }
    }

    /**
     * @param null $id
     */
    public function update_avatar($id = null)
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        if (!$this->ion_auth->logged_in() || !$this->Owner && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        //validate form input
        $this->form_validation->set_rules('avatar', lang('avatar'), 'trim');

        if ($this->form_validation->run() == true) {
            if ($_FILES['avatar']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/avatars';
                $config['allowed_types'] = 'gif|jpg|png';
                //$config['max_size'] = '500';
                $config['max_width']    = $this->Settings->iwidth;
                $config['max_height']   = $this->Settings->iheight;
                $config['overwrite']    = false;
                $config['encrypt_name'] = true;
                $config['max_filename'] = 25;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('avatar')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/avatars/' . $photo;
                $config['new_image']      = 'assets/uploads/avatars/thumbs/' . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $user = $this->ion_auth->user($id)->row();
            } else {
                $this->form_validation->set_rules('avatar', lang('avatar'), 'required');
            }
        }

        if ($this->form_validation->run() == true && $this->auth_model->updateAvatar($id, $photo)) {
            unlink('assets/uploads/avatars/' . $user->avatar);
            unlink('assets/uploads/avatars/thumbs/' . $user->avatar);
            $this->session->set_userdata('avatar', $photo);
            $this->session->set_flashdata('message', lang('avatar_updated'));
            admin_redirect('auth/profile/' . $id);
        } else {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('auth/profile/' . $id);
        }
    }

    public function user_actions()
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
                        if ($id != $this->session->userdata('user_id')) {
                            $this->auth_model->delete_user($id);
                        }
                    }
                    $this->session->set_flashdata('message', lang('users_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                    }
                    $this->excel->getActiveSheet()->setTitle(lang('users'));
                    $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('users'));
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
                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                          //  $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
                          //  $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);



                        $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                        $row++;
                        $row++;

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('first_name'));
                    $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('last_name'));
                    $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('email'));
                    $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('company'));
                    $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('award_points'));
                    $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('group'));
                    $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('Status'));

                    $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($fillColor);
                    $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($Font11Bold);
                    $row++;


                    foreach ($_POST['val'] as $id) {
                        $user = $this->site->getUser($id);
                        $user_group = $this->site->getUserGroup($id);
                        $user_active="";
                        if($user->active==1){
                            $user_active=lang('active');
                        }else{
                            $user_active=lang('inactive');
                        }

                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $user->first_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $user->last_name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $user->email);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $user->company);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $user->award_points);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $user_group->name);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $user_active);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = lang('users').'_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_user_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function users()
    {
        if (!$this->loggedIn) {
            admin_redirect('login');
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin/welcome');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('users')]];
        $meta = ['page_title' => lang('users'), 'bc' => $bc];
        $this->page_construct('auth/index', $meta, $this->data);
    }
	/*---------------------reports---------------*/
	    public function users_report()
    {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('staff_report')]];
        $meta                = ['page_title' => lang('staff_report'), 'bc' => $bc];
        $this->page_construct('reports/users', $meta, $this->data);
    }

    public function login_hr($warehouse_id = null)
    {
        $user                     = $this->ion_auth->user($id)->row();
        //$this->sma->print_arrays($user);
        if ($this->Owner || $this->Admin) {
            $company="hr_".$this->session->userdata('company');
			$this->db2 = $this->dynamicdbhr($company);
			$this->db2->select('*');
        	$this->db2->from('user');
        	$this->db2->where('username', $user->username);
        	$userHR = $this->db2->get()->row();



        	$salt = substr($userHR->password_hash, 0, 10);
        	$db_password = $salt . substr(sha1($salt . (($user->key_p ? $user->key_p : 'Admin123'))), 0, -10);

		    if ($db_password == $userHR->password_hash) {
        		$userHR =  $userHR;
        	} else {
		        $userHR =  array();
        	}
            if(empty($userHR)){
                $username="admin";
                $key_p="Admin123";
            }else{
                $username=$userHR->username;
                $key_p=$user->key_p ? $user->key_p : 'Admin123';
            }

        }else{
            $username=$userHR->username;
            $key_p=$user->key_p ? $user->key_p : '123456';
        }



        $this->data['post']= array(
            'company' => $this->session->userdata('company'),
            'identity' => $username,
            'password' => $key_p,
        );
        //$this->sma->print_arrays($this->data['post']);

        $this->load->view($this->theme . 'auth/login_hr', $this->data);

    }

    public function dynamicdbhr($company)
	{
		if (isset($company))
		{
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = 'cpesoft_'.$company;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = 'cpesoft_'.$company;
            $config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = '';
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
            $data = $CI->load->database($config, TRUE);

			return $data;
		}
		else
		return $this->db;
	}

    public function login_crm($warehouse_id = null)
    {
        /*$user                     = $this->ion_auth->user($id)->row();
        //$this->sma->print_arrays($user);
        if ($this->Owner || $this->Admin) {
            $company="hr_".$this->session->userdata('company');
			$this->db2 = $this->dynamicdbhr($company);
			$this->db2->select('*');
        	$this->db2->from('user');
        	$this->db2->where('username', $user->username);
        	$userHR = $this->db2->get()->row();



        	$salt = substr($userHR->password_hash, 0, 10);
        	$db_password = $salt . substr(sha1($salt . (($user->key_p ? $user->key_p : 'Admin123'))), 0, -10);

		    if ($db_password == $userHR->password_hash) {
        		$userHR =  $userHR;
        	} else {
		        $userHR =  array();
        	}
            if(empty($userHR)){
                $username="admin";
                $key_p="Admin123";
            }else{
                $username=$userHR->username;
                $key_p=$user->key_p ? $user->key_p : 'Admin123';
            }

        }else{
            $username=$userHR->username;
            $key_p=$user->key_p ? $user->key_p : '123456';
        }


        */
        $user                     = $this->ion_auth->user($id)->row();
        $email=$user->email;//'admin@admin.com';
        $key_p=$user->key_p ? $user->key_p : 'Admin123';
        $this->data['post']= array(
            'company' => $this->session->userdata('company'),
            'email' => $email,
            'password' => $key_p,
        );
        //$this->sma->print_arrays($this->data['post']);

        $this->load->view($this->theme . 'auth/login_crm', $this->data);

    }

}
