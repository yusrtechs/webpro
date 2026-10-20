<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//ini_set('display_errors', 1);
require APPPATH . '/libraries/BaseController.php';
require APPPATH . '/libraries/cpanel-UAPI-php-class-master/cpaneluapi.class.php';
/**
 * Class : Admin (AdminController)
 * Admin class to control to authenticate admin credentials and include admin functions.
 * @author : Samet Aydın / sametay153@gmail.com
 * @version : 1.0
 * @since : 27.02.2018
 */
class Shop extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('user_model');
        $this->load->model('client_model');
        $this->load->model('agents_model');
        $this->load->model('api_model');
        // Datas -> libraries ->BaseController / This function used load user sessions
        $this->datas();
        // isLoggedIn / Login control function /  This function used login control
        $isLoggedIn = $this->session->userdata('isLoggedIn');
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            //redirect('login');
        } else {
            // isAdmin / Admin role control function / This function used admin role control
            if ($this->isAdmin() == TRUE) {
               // $this->accesslogincontrol();
            }
        }
    }

    /**
     * This function is used to load the user list
     */

    public function callbackShopApi($id = NULL)
    {
        if ($id)
        {

            $json = file_get_contents('php://input');
            $callbackjson =json_decode($json);




        $clientInfo = $this->client_model->getClientInfo($id);

        $company = '';
        if (!empty($clientInfo)) {
            $company = $clientInfo->company;
        }

            $userInfo = [
                'key_id'     => $callbackjson->key_id,
                'client_id'       => $id,
                'user_id'       => $callbackjson->user_id,
                'consumer_key'       => $callbackjson->consumer_key,
                'consumer_secret'       => $callbackjson->consumer_secret,
                'permissions'       => $callbackjson->key_permissions,

            ];
            //echo "<strong>New database</strong> : \n",print_r($userInfo, true), "\n";


            $result = $this->client_model->addShopApiKey($userInfo);





        }
    }


}
