<?php

//ini_set("display_errors", 1);
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller_SET.php';
use Restserver\Libraries\REST_Controller_SET;

class SetStore extends REST_Controller_SET
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        //$this->load->api_model('Users_api');
		//$this->load->api_model('warehouses_api');
		//$this->load->api_model('users_api');

		$this->load->admin_model('site');
		$this->load->admin_model('api_model');
		$this->load->admin_model('products_model');
        $this->load->admin_model('shop_admin_model');



    }

	protected function setArr($warehouse)
    {
        unset($warehouse->price_group_id);
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }


    protected function setSale($sale)
    {
        unset($sale->address_id, $sale->api, $sale->attachment, $sale->hash, $sale->pos, $sale->reserve_id, $sale->return_id, $sale->return_sale_ref, $sale->return_sale_total, $sale->sale_id, $sale->shop, $sale->staff_note, $sale->surcharge, $sale->updated_at, $sale->suspend_note);
        if (isset($sale->items) && !empty($sale->items)) {
            foreach ($sale->items as &$item) {
                if (isset($item->option_id) && !empty($item->option_id)) {
                    if ($variant = $this->sales_api->getProductVariantByID($item->option_id)) {
                        $item->product_variant_id   = $variant->id;
                        $item->product_variant_name = $variant->name;
                    }
                }
                $item->product_unit_quantity = $item->unit_quantity;
                unset($item->id, $item->sale_id, $item->warehouse_id, $item->real_unit_price, $item->sale_item_id, $item->option_id, $item->unit_quantity);
                $item = (array) $item;
                ksort($item);
            }
        }
        $sale = (array) $sale;
        ksort($sale);
        return $sale;
    }




    public function index_post()
    {

		//$sales = urldecode($this->post('sales'));
		$json = file_get_contents('php://input');

		$sales =json_decode($json);

		$company=$sales->company;
        if ($company) {

        $user=$sales->user;
        if ($user) {
        $password=$sales->password;

        if ($password) {

		//$sales=$sales->JSON3;
		$failed_sales = [];
		//$this->sma->print_arrays($this->db);

        $otherdb = $this->load->database('info', true);
        $check = $otherdb->where('company', $company)->get('client')->row();


        if($check){

            if ($check->status == 1) {

                $newdb = $this->site->posdynamicdb($check->company);


                $rowdb = $newdb->query("SELECT username, email, id, password, active, last_login, last_ip_address, avatar, gender, group_id, warehouse_id, biller_id, company_id, view_right, edit_right, allow_discount, show_cost, show_price, edit_price FROM sma_users WHERE username='".$newdb->escape_str($user)."' ")->result_array();
                if($rowdb){

                    if($rowdb[0]['active']==1){




                        $salt = substr($rowdb[0]['password'], 0, 10);

                        $db_password = $salt . substr(sha1($salt . $password), 0, -10);

                        if ($db_password == $rowdb[0]['password']) {








                            $apisarr=$this->site->getApiKeysByCompany($company);



        if($apisarr){
            $apis= $this->setArr($apisarr);
           $api_key= $apis[0]['key'];
        }else{

            $count = $this->api_model->getCountofKey($company)->count;
            $new_api_key=$this->api_model->generateKey();
            $userInfo = [
                'reference'     => $company . "_" . $count,
                'user_id'       => '1',
                'key'           => $new_api_key,
                'level'         => '2',
                'ignore_limits' => '1',
                'ip_addresses'  => '127.0.0.1',
                'company' => $company,
                'date_created'  => time(),
            ];

            //$this->sma->print_arrays($new_api_key,$userInfo);

            $result = $this->api_model->addApiKey($userInfo);
            if ($result > 0) {
                $api_key=$new_api_key;
            }

        }
        $warehouse_code='';
        if($rowdb[0]['warehouse_id']){

            $rowdb2 = $newdb->query("SELECT code FROM sma_warehouses WHERE id='".$rowdb[0]['warehouse_id']."' ")->result_array();
            if($rowdb2){
                $warehouse_code=$rowdb2[0]['code'];
            }


        }

    $this->db->query("ALTER TABLE sma_settings ADD isStore TINYINT(1) NOT NULL DEFAULT '1' ");
    $this->db->query("ALTER TABLE sma_categories ADD shop_id INT(11) NULL DEFAULT NULL; ");
    $this->db->query("ALTER TABLE sma_products ADD shop_id INT(11) NULL DEFAULT NULL; ");
    $this->db->query("CREATE TABLE IF NOT EXISTS `sma_shop_settings` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `store_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `password` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `created_by` int(11) DEFAULT NULL,  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `base_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  `store_id` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,  `theme_id` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `latest_sync_order` datetime DEFAULT NULL,  `latest_sync_customers` datetime DEFAULT NULL,  `latest_sync_category` datetime DEFAULT NULL,  `latest_sync_products` datetime DEFAULT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; ");
    $this->db->query("INSERT INTO `sma_shop_settings` (`id`,`base_url`) VALUES (1,'https://matjariksa.com'); ");


    $otherdb = $this->load->database('info', true);
    $otherdb->query("UPDATE `client` SET `isStore` = '1' WHERE `company` = '".$company."'; ");



    $shop_settings = $this->shop_admin_model->getShopSettings();

    if (empty($shop_settings->token)) {

        $store_name = $this->Settings->site_name;
        $name       = $this->session->userdata('username');

        $user = $this->site->getUser($this->session->userdata('user_id'));
        $password = empty($user->key_p) ? "Admin123" : $user->key_p;



        $apisarr=$this->site->getApiKeysByCompany($company);
        if($apisarr){
           $apis= $this->setArr($apisarr);
           $api_key= $apis[0]['key'];
        }else{
            $count = $this->api_model->getCountofKey($company)->count;
            $new_api_key=$this->api_model->generateKey();
            $userInfo = [
                'reference'     => $company . "_" . $count,
                'user_id'       => '1',
                'key'           => $new_api_key,
                'level'         => '2',
                'ignore_limits' => '1',
                'ip_addresses'  => '127.0.0.1',
                'company' => $company,
                'date_created'  => time(),
            ];

            //$this->sma->print_arrays($new_api_key,$userInfo);

            $result = $this->api_model->addApiKey($userInfo);
            if ($result > 0) {
                $api_key=$new_api_key;
            }
        }

        $email = $company . '@' . $company . '.com';

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
    $apisarr=$this->site->getApiKeysByCompany($company);
        if($apisarr){
            $apis= $this->setArr($apisarr);
            $api_key= $apis[0]['key'];
        }else{
            $count = $this->api_model->getCountofKey($company)->count;
            $new_api_key=$this->api_model->generateKey();
            $userInfo = [
                'reference'     => $company . "_" . $count,
                'user_id'       => '1',
                'key'           => $new_api_key,
                'level'         => '2',
                'ignore_limits' => '1',
                'ip_addresses'  => '127.0.0.1',
                'company' => $company,
                'date_created'  => time(),
            ];

            //$this->sma->print_arrays($new_api_key,$userInfo);

            $result = $this->api_model->addApiKey($userInfo);
            if ($result > 0) {
                $api_key=$new_api_key;
            }
        }

    /*$apisarr = $this->restdb
        ->query("SELECT `key` FROM `sma_api_keys` WHERE company='" . $db_name . "' ")
        ->result_array();

    $api_key = $apisarr[0]['key'];*/

    // ✅ تكوين الرابط النهائي
    $link = $shop_settings->base_url .
        '/login/store/token?token=' . $shop_settings->token .
        '&api_key=' . $api_key;



        $this->response([
            'message' => 'Store created successful',
            'link' => $link,
            'status'  => true,
        ], REST_Controller_SET::HTTP_OK);



    }else{

    $this->response([
        'message' => 'Login Failed, Please try again',
        'status'  => false,
    ], REST_Controller_SET::HTTP_BAD_REQUEST);
    }


    }else{

    $this->response([
        'message' => 'login unsuccessful, User is not activate',
        'status'  => false,
    ], REST_Controller_SET::HTTP_BAD_REQUEST);
    }


    }else{

    $this->response([
        'message' => 'Login Failed, Please try again',
        'status'  => false,
    ], REST_Controller_SET::HTTP_BAD_REQUEST);
    }

    }else{

    $this->response([
        'message' => 'Your account is deactivated please payment and login again',
        'status'  => false,
    ], REST_Controller_SET::HTTP_BAD_REQUEST);
    }

    }else{

    $this->response([
        'message' => 'Your Company name incorrect',
        'status'  => false,
    ], REST_Controller_SET::HTTP_BAD_REQUEST);
    }

    }else{

    $this->response([
        'message' => 'This request does not contain Passwoed',
        'status'  => false,
    ], REST_Controller_SET::HTTP_BAD_REQUEST);
    }

    }else{

    $this->response([
        'message' => 'This request does not contain User',
        'status'  => false,
    ], REST_Controller_SET::HTTP_BAD_REQUEST);
    }
    }else{

    $this->response([
        'message' => 'This request does not contain Company',
        'status'  => false,
    ], REST_Controller_SET::HTTP_BAD_REQUEST);
    }

    }
}
