<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//ini_set('display_errors', 1);
require APPPATH . '/libraries/BaseController.php';
require APPPATH . '/libraries/cpanel-UAPI-php-class-master/cpaneluapi.class.php';
require APPPATH . '/libraries/PHPWord/bootstrap.php';
require APPPATH . '/libraries/PHPWord/HijriDateTime.php';

/**
 * Class : Admin (AdminController)
 * Admin class to control to authenticate admin credentials and include admin functions.
 * @author : Samet Aydın / sametay153@gmail.com
 * @version : 1.0
 * @since : 27.02.2018
 */
class Admin extends BaseController
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
            redirect('login');
        } else {
            // isAdmin / Admin role control function / This function used admin role control
            if ($this->isAdmin() == TRUE) {
                $this->accesslogincontrol();
            }
        }
    }

    /**
     * This function is used to load the user list
     */
    function userListing()
    {
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;

        $this->load->library('pagination');

        $count = $this->user_model->userListingCount($searchText);

        $returns = $this->paginationCompress("userListing/", $count, 10);
        //echo "<strong>New database</strong> : \n",print_r($returns, true), "\n";

        $data['userRecords'] = $this->user_model->userListing($searchText, $returns["page"], $returns["segment"]);

        $process = 'User Listing';
        $processFunction = 'Admin/userListing';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : User List';

        $this->loadViews("users", $this->global, $data, NULL);
    }


    function clientListing()
    {
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;

        $this->load->library('pagination');

        $count = $this->client_model->clientListingCount($searchText);
        //

        $returns = $this->paginationCompress("clientListing/", $count, 10);
        //echo "<strong>New database</strong> : \n",print_r($returns, true), "\n";

        $data['userRecords'] = $this->client_model->clientListing($searchText, $returns["page"], $returns["segment"]);

        $process = 'Client Listing';
        $processFunction = 'Admin/clientListing';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Client List';

        $this->loadViews("clientsv2", $this->global, $data, NULL);
    }


    function clientListingApi($id = NULL)
    {
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        if (!$id) {
            $this->session->set_flashdata('error', 'Please select client');

            redirect('clientListing');
        }

        $this->load->library('pagination');
        $clientInfo = $this->client_model->getClientInfo($id);
        $company = '';
        if (!empty($clientInfo)) {
            $company = $clientInfo->company;
        } else {
            $this->session->set_flashdata('error', 'Not found any Api-Keys');
            redirect('clientListing');
        }

        $data['userRecords'] = $this->api_model->getApiKeysByCompany($company);
        $data['id'] = $id;
        $data['company'] = $company;

        $process = 'Api List for Client : ' . $company;
        $processFunction = 'Admin/clientListingApi';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Api List for Client : ' . $company;

        $this->loadViews("apiListing", $this->global, $data, NULL);
    }

    function clientListingShopApi($id = NULL)
    {
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        if (!$id) {
            $this->session->set_flashdata('error', 'Please select client');

            redirect('clientListing');
        }

        $this->load->library('pagination');
        $clientInfo = $this->client_model->getClientInfo($id);
        $company = '';
        if (!empty($clientInfo)) {
            $company = $clientInfo->company;
        } else {
            $this->session->set_flashdata('error', 'Not found any Shop Api-Keys');
            redirect('clientListing');
        }



        $data['userRecords'] = $this->client_model->getShopApiKeysByClientId($id);
        $data['id'] = $id;
        $data['company'] = $company;



                    $company1="arkanproshop";
                    $config['dsn'] = '';
                    $config['hostname'] = 'localhost';
                    $config['username'] = 'cpesoft';
                    $config['password'] = 'enter-password';
                    $config['database'] = 'cpesoft_'.$company1;
                    //$config['username'] = 'root';
                    //$config['password'] = '';
                    //$config['database'] = $company1;
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

        $user_id = $this->mynewdb->query("SELECT ID FROM wp_users WHERE user_login = '".$company."'")->result_array()[0]['ID'];



        $data['user_id']       = $user_id;

        $process = 'Shop Api List for Client : ' . $company;
        $processFunction = 'Admin/clientListingShopApi';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Api List for Client : ' . $company;

        $this->loadViews("apiShopListing", $this->global, $data, NULL);





    }


    function clientListingSZatca($id = NULL)
    {
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        if (!$id) {
            $this->session->set_flashdata('error', 'Please select client');

            redirect('clientListing');
        }

        $this->load->library('pagination');
        $clientInfo = $this->client_model->getClientInfo($id);
        $company = '';
        if (!empty($clientInfo)) {
            $company = $clientInfo->company;
        } else {
            $this->session->set_flashdata('error', 'Not found any Zatca Settings');
            redirect('clientListing');
        }



        $data['id'] = $id;
        $data['company'] = $company;



                    $config['dsn'] = '';
                    $config['hostname'] = 'localhost';
                    $config['username'] = 'cpesoft';
                    $config['password'] = 'enter-password';
                    $config['database'] = 'cpesoft_'.$company;
                    /*$config['username'] = 'root';
                    $config['password'] = '';
                    $config['database'] = $company;*/
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

                    $data['zatca'] = $this->mynewdb->query("SELECT binarySecurityToken,secret,requestID FROM sma_zatca_config WHERE id =1")->result_array()[0];
                    
                    $data['zatca2'] = $this->mynewdb->query("SELECT binarySecurityToken,secret,requestID FROM sma_zatca_config WHERE id =2")->result_array()[0];




        $process = 'Zatca OTP List for Client : ' . $company;
        $processFunction = 'Admin/clientListingSZatca';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Zatca OTP List for Client : ' . $company;

        $this->loadViews("zatcaOTPListing", $this->global, $data, NULL);





    }





    function addNewApi($id = NULL)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Please select client');

            redirect('clientListingApi');
        }

        $this->load->library('pagination');
        $clientInfo = $this->client_model->getClientInfo($id);

        $company = '';
        if (!empty($clientInfo)) {
            $company = $clientInfo->company;

            $count = $this->api_model->getCountofKey($company)->count;
            $userInfo = [
                'reference'     => $company . "_" . $count,
                'user_id'       => $this->vendorId,
                'key'           => $this->api_model->generateKey(),
                'level'         => '2',
                'ignore_limits' => '1',
                'ip_addresses'  => '127.0.0.1',
                'company' => $company,
                'date_created'  => time(),
            ];
            //echo "<strong>New database</strong> : \n",print_r($userInfo, true), "\n";


            $result = $this->api_model->addApiKey($userInfo);

            if ($result > 0) {
                $process = 'Add Api for company ' . $company;
                $processFunction = 'Admin/addNewApi';
                $this->logrecord($process, $processFunction);

                $this->session->set_flashdata('success', 'Api successfully created for Client : ' . $company);
            } else {
                $this->session->set_flashdata('error', 'Api creation failed for Client : ' . $company);
            }

            redirect('clientListingApi/' . $id);
        } else {
            $this->session->set_flashdata('error', 'Api creation failed for Client : ' . $company);

            redirect('clientListingApi/' . $id);
        }
    }



    public function callbackShopApi($id = NULL)
    {
        if ($id)
        {

            $json = file_get_contents('php://input');
            //$json='{"key_id":3,"user_id":"5","consumer_key":"ck_ff042fd10e67efc8fec2d67507797ef59b0c224d","consumer_secret":"cs_d4b900e9bc3800eddc84ad8a6448e10e704e565c","key_permissions":"read_write"}';
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

            if ($result > 0) {
                $process = 'Add Shop Api for company ' . $company;
                $processFunction = 'Admin/addNewShopApi';
                $this->logrecord($process, $processFunction);


            } else {

            }



        }
    }


    /**
     * This function is used to load the add new form
     */
    function addNew()
    {
        $data['roles'] = $this->user_model->getUserRoles();

        $this->global['pageTitle'] = 'ArkanPro : Add User';

        $this->loadViews("addNew", $this->global, $data, NULL);
    }

    function addNewClient()
    {
        //$data['roles'] = $this->user_model->getUserRoles();
        $data['classification'] = $this->client_model->getCompanyTypes();
        $data['activities'] = $this->client_model->getActivities();
        $data['agents'] = $this->agents_model->getAgents();
        $this->global['pageTitle'] = 'ArkanPro : Add Client';

        $this->loadViews("addNewClient", $this->global, $data, NULL);
    }


    /**
     * This function is used to add new user to the system
     */
    function addNewUser()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('fname', 'Full Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[20]');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required|matches[password]|max_length[20]');
        $this->form_validation->set_rules('role', 'Role', 'trim|required|numeric');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|min_length[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->addNew();
        } else {
            $name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
            $email = $this->security->xss_clean($this->input->post('email'));
            $password = $this->input->post('password');
            $roleId = $this->input->post('role');
            $mobile = $this->security->xss_clean($this->input->post('mobile'));

            $userInfo = array(
                'email' => $email, 'password' => getHashedPassword($password), 'roleId' => $roleId, 'name' => $name,
                'mobile' => $mobile, 'createdBy' => $this->vendorId, 'createdDtm' => date('Y-m-d H:i:s')
            );

            $result = $this->user_model->addNewUser($userInfo);

            if ($result > 0) {
                $process = 'Add User';
                $processFunction = 'Admin/addNewUser';
                $this->logrecord($process, $processFunction);

                $this->session->set_flashdata('success', 'User successfully created');
            } else {
                $this->session->set_flashdata('error', 'User creation failed');
            }

            redirect('userListing');
        }
    }

    function addNewClientSubmit()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', 'Full Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('name_en', 'Email', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('company', 'Company Name', 'trim|required|is_unique[client.company]|strtolower|max_length[20]|min_length[3]');
        if (empty($_FILES["contract_attachment"]["name"])) {
        $this->form_validation->set_rules('contract_attachment', 'Contract Attachement', 'trim|required');
        }
        if($this->input->post('company')=="default" || $this->input->post('company')=="grocery" || $this->input->post('company')=="restaurants" || $this->input->post('company')=="contracting" || $this->input->post('company')=="lundries" || $this->input->post('company')=="defaultcars" || $this->input->post('company')=="tailoring" || $this->input->post('company')=="defaulthr" || substr($this->input->post('company'),0,3)=="hr_"){
            $this->session->set_flashdata('error', 'Client creation failed....Please change company name');
            redirect('clientListing');
        }







        if ($this->form_validation->run() == FALSE) {

            $this->addNewClient();
        } else {



            $isPOS = 0;
            if ($this->input->post('isPOS'))
                $isPOS = 1;

            $isAccounting = 0;
            if ($this->input->post('isAccounting'))
                $isAccounting = 1;

                $isGrouping = 0;
            if ($this->input->post('isGrouping'))
                $isGrouping = 1;

            $isManufacturing = 0;
            if ($this->input->post('isManufacturing'))
                $isManufacturing = 1;

            $isStore = 0;
            if ($this->input->post('isStore'))
                $isStore = 1;

            $with_products = 0;
            if ($this->input->post('with_products'))
                $with_products = 1;



            $name = $this->input->post('name');
            $name_en = $this->input->post('name_en');
            $company = $this->input->post('company');
            $company_type = $this->input->post('company_type');
            $tel_no = $this->input->post('tel_no');
            $activity_id = $this->input->post('activity_id');
            $address = $this->input->post('address');
            $status = $this->input->post('status');
            $agent_id = $this->input->post('agent_id');
            $inv_no = $this->input->post('inv_no');
            $isPOS = $isPOS;
            $isAccounting = $isAccounting;
            $isGrouping = $isGrouping;
            $isManufacturing = $isManufacturing;
            $isStore = $isStore;

            $companydb=$company;

            $isHR = 0;
            if($company_type==9){
                $isHR = 1;
                $companydb="hr_".$company;
            }





            $data=$this->createDb($companydb);
            $this->mynewdb =$data;
            if(($this->mynewdb->conn_id)){

                }else{
                    $this->session->set_flashdata('error', 'Client creation failed....Please try again');


                    redirect('clientListing');
                }

            $clientInfo = array();

            $clientInfo = array(
                'name' => $name,
                'name_en' => $name_en,
                'company' => $company,
                'company_type' => $company_type,
                'tel_no' => $tel_no,
                'activity_id' => $activity_id,
                'address' => $address,
                'number_of_payment' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'renewal_date' => date('Y-m-d H:i:s'),
                'isPOS' => $isPOS,
                'isAccounting' => $isAccounting,
                'isGrouping' => $isGrouping,
                'isManufacturing' => $isManufacturing,
                'isStore' => $isStore,
                'status' => $status,
                'createdBy' => $this->vendorId,
                'agent_id' => $agent_id ? $agent_id : null,
                'inv_no' => $inv_no ? $inv_no : null,
                'isHR' => $isHR
            );




            $client_id = $this->client_model->addNewClient($clientInfo);
            if ($client_id > 0) {

                $config = array(
                    'upload_path' => "./upload",
                    'allowed_types' => "gif|jpg|png|pdf|doc|docx|xls|xlsx|ppt|pptx",
                    'overwrite' => TRUE,
                    'max_size' => "204800000",
                    'file_name' => time(),
                    'remove_spaces' => true
                );

                $this->load->library('upload', $config);

        $file = null;
        $contract_attachment = $_FILES["contract_attachment"]["name"];
        if (isset($_FILES)) {
            if ($this->upload->do_upload('contract_attachment')) {
                $contract_attachment = $this->upload->data();
                $filename = $contract_attachment['file_name'];
                $file = './upload/' . $filename;
            } else {

            }
        }


                $clientTransaction = [
                    'client_id' => $client_id,
                    'created_by' => $this->vendorId,
                    'company_type' => $company_type,
                    'paid_at' =>  date('Y-m-d H:i:s'),
                    'renewed_at' => date('Y-m-d H:i:s'),
                    'isPOS' => $isPOS,
                    'isAccounting' => $isAccounting,
                    'isGrouping' => $isGrouping,
                    'isManufacturing' => $isManufacturing,
                    'isStore' => $isStore,
                    'automatedEntries' => false,
                    'automatedEntriesOffline' => false,
                    'comments' => null,
                    'contract_attachment' => $file,
                    'isHR' => $isHR
                ];
                $this->client_model->createTransaction($clientTransaction);

                $process = 'Add Client for company '.$company;
                $processFunction = 'Admin/addNewClientSubmit';
                $this->logrecord($process, $processFunction);







			$dbName = "cpesoft_default";
			if($company_type==1)
			$dbName = "cpesoft_default";
			if($company_type==2){
                if($with_products==1){
                    $dbName = "cpesoft_grocery";
                }else{
                    $dbName = "cpesoft_default";
                }
            }
			if($company_type==3)
			$dbName = "cpesoft_restaurants";
			if($company_type==4)
			$dbName = "cpesoft_contracting";
			if($company_type==5)
			$dbName = "cpesoft_default";
			if($company_type==6)
			$dbName = "cpesoft_lundries";
            if($company_type==7)
			$dbName = "cpesoft_defaultcars";
            if($company_type==8)
			$dbName = "cpesoft_tailoring";
            if($company_type==9)
			$dbName = "cpesoft_defaulthr";



			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = $dbName;
            //$config['database'] = 'cpesoft_'.$company;
			//$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $dbName;
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
            $data = $CI->load->database($config, TRUE);
			$this->olddb =$data;
			//$this->dbforge =$data;


			 //$this->sma->print_arrays($this->mydb);
			 //$this->sma->print_arrays($this->dbforge);
			//$otherdb = $this->load->database('info', true);
			// $this->sma->print_arrays($this->myforge);
			$tables = $this->olddb->query("SHOW TABLES")->result_array();
			foreach ($tables as $key => $val) {
   $this->mynewdb->query('CREATE TABLE ' . $val['Tables_in_'.$dbName] . ' LIKE '.$dbName.'.' . $val['Tables_in_'.$dbName]);
   $this->mynewdb->query('INSERT ' . $val['Tables_in_'.$dbName] . ' SELECT * FROM '.$dbName.'.' . $val['Tables_in_'.$dbName]);
  }
   $this->mynewdb->query("UPDATE sma_settings SET site_name='".$name."',site_ename='".$name_en."' ");
   if($company_type==7){
    $this->mynewdb->query("UPDATE companies SET company='".$name."' where id=3 ");
   }

   if($isPOS==1){
	$this->mynewdb->query("UPDATE sma_settings SET pos=1 ");
   }
	if($isAccounting==1){
	$this->mynewdb->query("ALTER TABLE sma_settings ADD preAccounting TINYINT(1) NOT NULL DEFAULT '1' ");
	$this->mynewdb->query("ALTER TABLE sma_settings ADD accounting_type VARCHAR(15) NULL DEFAULT NULL ");

			$accountingdb='cpesoft_accounting';
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = $accountingdb;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $dbName;
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
            $data = $CI->load->database($config, TRUE);
			$this->accdb =$data;

			$tables = $this->accdb->query("SHOW TABLES")->result_array();
			foreach ($tables as $key => $val) {
   $this->mynewdb->query('CREATE TABLE ' . $val['Tables_in_'.$accountingdb] . ' LIKE '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
   $this->mynewdb->query('INSERT ' . $val['Tables_in_'.$accountingdb] . ' SELECT * FROM '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
  }

               $this->mynewdb->query("SET FOREIGN_KEY_CHECKS=0"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_accounts MODIFY COLUMN userID int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_accounts ADD CONSTRAINT sma_gl_accounts_FK FOREIGN KEY (userID) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_accounts_accountID_IDX USING BTREE ON sma_gl_accounts (accountID,account_type)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_accounts_accountName_IDX USING BTREE ON sma_gl_accounts (accountCategory,accountCategorySub,accountStatement,account_type)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_accounting_period MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_accounting_period ADD CONSTRAINT sma_gl_accounting_period_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_catch_receipts MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_catch_receipts ADD CONSTRAINT sma_gl_catch_receipts_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_catch_receipts_bond_no_IDX USING BTREE ON sma_gl_catch_receipts (bond_no,reference_no,created_by,receipt_name)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_catch_receipts_fromAccountID_IDX USING BTREE ON sma_gl_catch_receipts (fromAccountID,toAccountID)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_accounts_categories_categoryNameEn_IDX USING BTREE ON sma_gl_accounts_categories (categoryNameEn,categoryName,categoryID)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_cost_centers MODIFY COLUMN userID int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_cost_centers ADD CONSTRAINT sma_gl_cost_centers_FK FOREIGN KEY (userID) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_entries MODIFY COLUMN userID int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_entries ADD CONSTRAINT sma_gl_entries_FK FOREIGN KEY (userID) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_entries_reference_no_IDX USING BTREE ON sma_gl_entries (reference_no,entryType)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_opening_balance MODIFY COLUMN userID int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_opening_balance ADD CONSTRAINT sma_gl_opening_balance_FK FOREIGN KEY (userID) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_receipts MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_receipts ADD CONSTRAINT sma_gl_receipts_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_refunding_bonds MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_refunding_bonds ADD CONSTRAINT sma_gl_refunding_bonds_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_refunding_bonds_bond_no_IDX USING BTREE ON sma_gl_refunding_bonds (bond_no,reference_no,toAccountID,fromAccountID,receipt_name)"); // blog its refere to Table





   }


   if($isGrouping==1){
	$this->mynewdb->query("ALTER TABLE sma_settings ADD isGrouping TINYINT(1) NOT NULL DEFAULT '1' ");
	$this->mynewdb->query("ALTER TABLE sma_settings ADD enable_grp TINYINT(1) NOT NULL DEFAULT '0' ");
	$this->mynewdb->query("ALTER TABLE sma_settings ADD enable_co TINYINT(1) NOT NULL DEFAULT '0' ");
	$this->mynewdb->query("ALTER TABLE sma_settings ADD enable_br TINYINT(1) NOT NULL DEFAULT '1' ");

			$accountingdb='cpesoft_defaultbranches';
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = $accountingdb;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $dbName;
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
            $data = $CI->load->database($config, TRUE);
			$this->accdb =$data;

			$tables = $this->accdb->query("SHOW TABLES")->result_array();
			foreach ($tables as $key => $val) {
   $this->mynewdb->query('CREATE TABLE ' . $val['Tables_in_'.$accountingdb] . ' LIKE '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
   $this->mynewdb->query('INSERT ' . $val['Tables_in_'.$accountingdb] . ' SELECT * FROM '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
  }

               $this->mynewdb->query("SET FOREIGN_KEY_CHECKS=0");
               $this->mynewdb->query("ALTER TABLE sma_main_companies ADD CONSTRAINT main_groups_grp_FK FOREIGN KEY (grpId) REFERENCES sma_main_groups(id)");
               $this->mynewdb->query("ALTER TABLE sma_main_branches ADD CONSTRAINT main_branches_grp_FK FOREIGN KEY (grpId) REFERENCES sma_main_groups(id)");
               $this->mynewdb->query("ALTER TABLE sma_main_branches ADD CONSTRAINT main_branches_co_FK FOREIGN KEY (coId) REFERENCES sma_main_companies(id)");
               $this->mynewdb->query("ALTER TABLE sma_customers_details ADD CONSTRAINT customers_details_customer_id_FK FOREIGN KEY (customer_id) REFERENCES sma_companies(id)");
               $this->mynewdb->query("INSERT INTO sma_customers_details(customer_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_companies WHERE group_name='customer')");
               $this->mynewdb->query("ALTER TABLE sma_suppliers_details ADD CONSTRAINT suppliers_details_supplier_id_FK FOREIGN KEY (supplier_id) REFERENCES sma_companies(id)");
               $this->mynewdb->query("INSERT INTO sma_suppliers_details(supplier_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_companies WHERE group_name='supplier')");
               $this->mynewdb->query("ALTER TABLE sma_units_details ADD CONSTRAINT units_details_unit_id_FK FOREIGN KEY (unit_id) REFERENCES sma_units(id)");
               $this->mynewdb->query("INSERT INTO sma_units_details(unit_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_units )");
               $this->mynewdb->query("ALTER TABLE sma_categories_details ADD CONSTRAINT categories_details_category_id_FK FOREIGN KEY (category_id) REFERENCES sma_categories(id)");
               $this->mynewdb->query("INSERT INTO sma_categories_details(category_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_categories )");
               $this->mynewdb->query("ALTER TABLE sma_brands_details ADD CONSTRAINT brands_details_brand_id_FK FOREIGN KEY (brand_id) REFERENCES sma_brands(id)");
               $this->mynewdb->query("INSERT INTO sma_brands_details(brand_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_brands )");
               $this->mynewdb->query("ALTER TABLE sma_variants_details ADD CONSTRAINT variants_details_variant_id_FK FOREIGN KEY (variant_id) REFERENCES sma_variants(id)");
               $this->mynewdb->query("INSERT INTO sma_variants_details(variant_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_variants )");
               $this->mynewdb->query("CREATE INDEX sma_warehouses_products_product_id_IDX USING BTREE ON sma_warehouses_products (product_id, warehouse_id, grpId, coId, brId)");
               $this->mynewdb->query("ALTER TABLE sma_taxs_details ADD CONSTRAINT taxs_details_tax_rate_id_FK FOREIGN KEY (tax_rate_id) REFERENCES sma_tax_rates(id)");
               $this->mynewdb->query("INSERT INTO sma_taxs_details(tax_rate_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_tax_rates )");
               $this->mynewdb->query("ALTER TABLE sma_currencies_details ADD CONSTRAINT currencies_details_currency_id_FK FOREIGN KEY (currency_id) REFERENCES sma_currencies(id)");
               $this->mynewdb->query("INSERT INTO sma_currencies_details(currency_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_currencies )");
               $this->mynewdb->query("ALTER TABLE sma_order_ref ADD brId INT(11) NOT NULL DEFAULT '1'");
               $this->mynewdb->query("ALTER TABLE sma_order_ref ADD CONSTRAINT order_ref_br_FK FOREIGN KEY (brId) REFERENCES sma_main_branches(id)");
               $this->mynewdb->query("CREATE INDEX sma_order_ref_brId_IDX USING BTREE ON sma_order_ref (brId)");
               $this->mynewdb->query("ALTER TABLE sma_sales ADD sale_no VARCHAR(30) NULL DEFAULT NULL");
               $this->mynewdb->query("ALTER TABLE sma_purchases ADD purchase_no VARCHAR(30) NULL DEFAULT NULL");

               if($isAccounting==1){
                    $this->mynewdb->query("ALTER TABLE sma_order_ref ADD gl_enrty INT(11) NOT NULL DEFAULT '1'");
                    $this->mynewdb->query("ALTER TABLE sma_gl_entries ADD entry_no VARCHAR(30) NULL DEFAULT NULL");
               }





   }





   if($isManufacturing==1){
	$this->mynewdb->query("ALTER TABLE sma_settings ADD IsManufacturing TINYINT(1) NOT NULL DEFAULT '1' ");
	$this->mynewdb->query("ALTER TABLE sma_transfers ADD man_id INT(11) NULL DEFAULT NULL ");
	$this->mynewdb->query("ALTER TABLE sma_destruction ADD man_id INT(11) NULL DEFAULT NULL ");

			$manufacturingdb='cpesoft_manufacturing';
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = $manufacturingdb;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $dbName;
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
            $data = $CI->load->database($config, TRUE);
			$this->accdb =$data;

			$tables = $this->accdb->query("SHOW TABLES")->result_array();
			foreach ($tables as $key => $val) {
   $this->mynewdb->query('CREATE TABLE ' . $val['Tables_in_'.$manufacturingdb] . ' LIKE '.$manufacturingdb.'.' . $val['Tables_in_'.$manufacturingdb]);
   $this->mynewdb->query('INSERT ' . $val['Tables_in_'.$manufacturingdb] . ' SELECT * FROM '.$manufacturingdb.'.' . $val['Tables_in_'.$manufacturingdb]);
  }


   }



   if($isStore==1){
	$this->mynewdb->query("ALTER TABLE sma_settings ADD isStore TINYINT(1) NOT NULL DEFAULT '1' ");
    $this->mynewdb->query("ALTER TABLE sma_categories ADD shop_id INT(11) NULL DEFAULT NULL; ");
    $this->mynewdb->query("ALTER TABLE sma_products ADD shop_id INT(11) NULL DEFAULT NULL; ");
    $this->mynewdb->query("CREATE TABLE IF NOT EXISTS `sma_shop_settings` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `store_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `password` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `created_by` int(11) DEFAULT NULL,  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `base_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  `store_id` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,  `theme_id` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `latest_sync_order` datetime DEFAULT NULL,  `latest_sync_customers` datetime DEFAULT NULL,  `latest_sync_category` datetime DEFAULT NULL,  `latest_sync_products` datetime DEFAULT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; ");
    $this->mynewdb->query("INSERT INTO `sma_shop_settings` (`id`,`base_url`) VALUES (1,'https://matjariksa.com'); ");



   }



        $sessionId="sessionId-admin";
        if($tel_no){
            $len = strlen($tel_no);
            if($len>8 && is_numeric($tel_no)){


                if(substr($tel_no, 0, 1) === "+"){
                                $recipient="".substr($tel_no, 1);
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="".substr($tel_no, 2);
                                    }else{
                                        $recipient="".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="966".substr($tel_no, 1);
                                    }else{
                                        $recipient="966".$tel_no;
                                    }
                                }
                            }
                    $message="مرحباً بكم في برنامج CPE!\\n\\nنشكر لكم اختياركم شركة أركان الاحتراف للاتصالات وتقنية المعلومات. \\nيسرنا أن نرحب بكم كعميل جديد لبرنامجنا الرائع.\\n\\nللدخول إلى حسابكم، يرجى استخدام المعلومات التالية:\\n\\nالرابط:\\nhttps://www.domain1.com\\nاسم الشركة: ".$company."  \\nاسم المستخدم: admin  \\nكلمة المرور: Admin123\\n\\nنتمنى لكم تجربة ممتعة ومثمرة مع برنامجنا. \\nإذا كان لديكم أي استفسارات أو تحتاجون إلى دعم، لا تترددوا في التواصل معنا.\\n\\nللدعم الفني، نرجو التواصل على الرقم التالي: 0503088707 أو الدخول على الرابط https://www.companypro.com\\n\\nشركة أركان الاحتراف للاتصالات وتقنية المعلومات.\\n\\n نرجو تعبئة الاستبيان لمعرفة انطباعك العام على الرابط\\n  https://wecare.jaltecpro.com/survey/index.php/customer/show/94/125 \\n\\n\\nWelcome to the CPE Program!\\n\\nThank you for choosing Arkan Al-Ihtiraf Communications and Information Technology. \\nWe are pleased to welcome you as a new customer to our fantastic program.\\n\\nTo access your account, please use the following information:\\n\\nLink:\\nhttps://www.domain1.com \\nCompany: ".$company."  \\nUsername: admin  \\nPassword: Admin123\\n\\nWe hope you have an enjoyable and productive experience with our program. \\nIf you have any questions or need support, please do not hesitate to contact us.\\n\\nFor technical support, please call 0503088707 or visit https://www.companypro.com\\n \\nArkan Al-Ihtiraf Communications and Information Technology.\\n\\n Please fill out the questionnaire to know your general impression:\\n\\nLink:\\nhttps://wecare.jaltecpro.com/survey/index.php/customer/show/94/125";
                    $url = "https://iparkanwebsitforwhats/".$sessionId."/messages/send";

                            $curl = curl_init($url);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            $headers = array(
                               "Content-Type: application/json",
                               "accept: application/json",

                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                            $data = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            //print_r($response);

                            $whatsappMsg="";
                            if (isset($response->message) && $response->status=='PENDING'){

                                $whatsappMsg=' WhatsApp Message Status : '.$response->status;

                        $userInfo = $this->user_model->getUserInfoById($this->vendorId);
                        $date=date('Y-m-d H:i:s');

                        $messageInfo = array(
                            'client_name' => $userInfo->name,
                            'company' => $company,
                            'date' => $date,
                            'title' => $message,
                            'user_id' => $this->vendorId
                        );

                        $message_id = $this->user_model->newWhatsAppMessage($messageInfo);
                    }

            }
        }


                $this->session->set_flashdata('success', 'Client successfully created'.$whatsappMsg);
            } else {
                $this->session->set_flashdata('error', 'Client creation failed');
            }

            redirect('clientListing');
        }
    }

    /**
     * This function is used load user edit information
     * @param number $userId : Optional : This is user id
     */
    function editOld($userId = NULL)
    {
        if ($userId == null) {
            redirect('userListing');
        }

        $data['roles'] = $this->user_model->getUserRoles();
        $data['userInfo'] = $this->user_model->getUserInfo($userId);

        $this->global['pageTitle'] = 'ArkanPro : Edit User';

        $this->loadViews("editOld", $this->global, $data, NULL);
    }


    function editOldClient($id = NULL)
    {
        if ($id == null) {
            redirect('clientListing');
        }

        $data['classification'] = $this->client_model->getCompanyTypes();
        $data['activities'] = $this->client_model->getActivities();
        //$data['API_electronic_linking'] = $this->client_model->getAPIElectronicLinking();

        $data['clientInfo'] = $this->client_model->getClientInfo($id);
        $data['clientElectronicContract'] = $this->client_model->getElectronicContract($data['clientInfo']->company);
        $data['agents'] = $this->agents_model->getAgents();



            $company=$data['clientInfo']->company;
            $company_type=$data['clientInfo']->company_type;

            $companydb=$company;
            if($company_type==9){
                $companydb="hr_".$company;
            }

            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = 'cpesoft_'.$companydb;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $companydb;
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
            $data1 = $CI->load->database($config, TRUE);
			$this->mynewdb =$data1;


            if($company_type!=9){
            $data['warehouse_code']  = 'w_'.$this->mynewdb->query("SELECT COALESCE(max(id+1),1) as id from sma_warehouses")->result_array()[0]['id'];
            $data['groups']  = $this->mynewdb->query("SELECT id,name from sma_groups WHERE id!=3 and id!=4")->result_array();
            $data['warehouses']  = $this->mynewdb->query("SELECT id,name from sma_warehouses")->result_array();
            $data['billers']  = $this->mynewdb->query("SELECT id,company from sma_companies where group_name='biller'")->result_array();
            if($data['clientInfo']->isGrouping==1){
                $data['main_branches']  = $this->mynewdb->query("SELECT id,name from sma_main_branches")->result();
                $data['branch_code']  = 'br_'.$this->mynewdb->query("SELECT COALESCE(max(id+1),1) as id from sma_main_branches")->result_array()[0]['id'];
                $data['branch_prefix']  = 'BR'.$this->mynewdb->query("SELECT COALESCE(max(id+1),1) as id from sma_main_branches")->result_array()[0]['id'];
            }
            }





            /*echo '<pre>';
            print_r($data['billers']);
            echo '</pre>';
            exit();*/


        if ($id == null || is_null($data['clientInfo'])) {
            redirect('clientListing');
        }

        $this->global['pageTitle'] = 'ArkanPro : Edit Client';

        $this->loadViews("editOldClient", $this->global, $data, NULL);
    }

    function addClientBranch()
    {
        $client_id = $this->input->post('id');
        $company_type = $this->input->post('company_type');
        $branch_code = $this->input->post('branch_code');
        $branch_name = $this->input->post('branch_name');
        $branch_prefix = $this->input->post('branch_prefix');

        $this->load->library('form_validation');
        $config = array(
            'upload_path' => "./upload",
            'allowed_types' => "gif|jpg|png|pdf|doc|docx|xls|xlsx|ppt|pptx",
            'overwrite' => TRUE,
            'max_size' => "204800000",
            'file_name' => time(),
            'remove_spaces' => true
        );

        $this->load->library('upload', $config);

        $file = null;
        $contract_attachment = $_FILES["contract_attachment"]["name"];
        if (isset($_FILES)) {
            if ($this->upload->do_upload('contract_attachment')) {
                $contract_attachment = $this->upload->data();
                $filename = $contract_attachment['file_name'];
                $file = './upload/' . $filename;
            } else {
               // $this->session->set_flashdata('error', 'Upload Attached Failed');
               // redirect('editOldClient/' . $client_id);
            }
        }

        $data['clientInfo'] = $this->client_model->getClientInfo($client_id);



            $company=$data['clientInfo']->company;

            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
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
            $data1 = $CI->load->database($config, TRUE);
			$this->mynewdb =$data1;


        $this->mynewdb->query("INSERT INTO sma_main_branches (grpId,coId,code,name,address,branch_prefix) VALUES ('1','1','".$branch_code."','".$branch_name."','default','".$branch_prefix."') ");
        $brId = $this->mynewdb->insert_id();
        $clause = ['brId' => $brId, 'date' => date('Y-m-d'), 'so' => 1, 'qu' => 1, 'po' => 1, 'to' => 1, 'pos' => 1, 'do' => 1, 'pay' => 1, 're' => 1, 'rep' => 1, 'ex' => 1, 'ppay' => 1];
        $this->mynewdb->insert('order_ref',$clause);

        $clientTransaction = [
            'client_id' => $client_id,
            'created_by' => $this->vendorId,
            'company_type' => $company_type,
            'paid_at' =>  date('Y-m-d H:i:s'),
            'add_branch' =>1,

            'comments' => $branch_code,
            'renewed_at' => date('Y-m-d H:i:s'),
            'contract_attachment' => $file,
        ];



        $result = $this->client_model->createTransaction($clientTransaction);
        if ($result == true) {
            $process = 'Client Add Branch with code '.$branch_code.' for company '.$company;
            $processFunction = 'Admin/addClientBranch';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Client successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Client update failed');
        }

        redirect('editOldClient/' . $client_id);


    }


    function addClientWarehouse()
    {
        $client_id = $this->input->post('id');
        $company_type = $this->input->post('company_type');
        $warehouse_code = $this->input->post('warehouse_code');
        $warehouse_name = $this->input->post('warehouse_name');
        $brId = $this->input->post('brId') ? $this->input->post('brId') :  1;

        $this->load->library('form_validation');
        $config = array(
            'upload_path' => "./upload",
            'allowed_types' => "gif|jpg|png|pdf|doc|docx|xls|xlsx|ppt|pptx",
            'overwrite' => TRUE,
            'max_size' => "204800000",
            'file_name' => time(),
            'remove_spaces' => true
        );

        $this->load->library('upload', $config);

        $file = null;
        $contract_attachment = $_FILES["contract_attachment"]["name"];
        if (isset($_FILES)) {
            if ($this->upload->do_upload('contract_attachment')) {
                $contract_attachment = $this->upload->data();
                $filename = $contract_attachment['file_name'];
                $file = './upload/' . $filename;
            } else {
               // $this->session->set_flashdata('error', 'Upload Attached Failed');
               // redirect('editOldClient/' . $client_id);
            }
        }

        $data['clientInfo'] = $this->client_model->getClientInfo($client_id);



            $company=$data['clientInfo']->company;

            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
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
            $data1 = $CI->load->database($config, TRUE);
			$this->mynewdb =$data1;


        $this->mynewdb->query("INSERT INTO sma_warehouses (code,name,branch_name,address,price_group_id,brId) VALUES ('".$warehouse_code."','".$warehouse_name."','','default','1','".$brId."') ");


        $clientTransaction = [
            'client_id' => $client_id,
            'created_by' => $this->vendorId,
            'company_type' => $company_type,
            'paid_at' =>  date('Y-m-d H:i:s'),
            'add_warehouse' =>1,

            'comments' => $warehouse_code,
            'renewed_at' => date('Y-m-d H:i:s'),
            'contract_attachment' => $file,
        ];



        $result = $this->client_model->createTransaction($clientTransaction);
        if ($result == true) {
            $process = 'Client Add Warehouse with code '.$warehouse_code.' for company '.$company;
            $processFunction = 'Admin/addClientWarehouse';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Client successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Client update failed');
        }

        redirect('editOldClient/' . $client_id);


    }

    function addClientUser()
    {
        $client_id = $this->input->post('id');
        $company_type = $this->input->post('company_type');
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name') ? $this->input->post('last_name') : $this->input->post('first_name');
        $brId = $this->input->post('brId') ? $this->input->post('brId') :  1;
        $username = $this->input->post('username');
        $group_id = $this->input->post('group_id');
        $warehouse = $this->input->post('warehouse') ? $this->input->post('warehouse') : null;
        $biller = $this->input->post('biller') ? $this->input->post('biller') : null;
        $phone = $this->input->post('phone') ? $this->input->post('phone') : null;
        $email = $this->input->post('email') ? $this->input->post('email') : $username.'@gmail.com';
        $group_name = trim($this->input->post('group_name'));


        if($group_id>4){
            $password ='befe4ed89f04d95af72679aa8b574c1af00dde72';
            $key_p ='1234';
            if(!$warehouse){
                $this->session->set_flashdata('error', 'Warehouse is require');
                redirect('editOldClient/' . $client_id);
            }
            if(!$biller){
                //$this->session->set_flashdata('error', 'Biller is require');
                //redirect('editOldClient/' . $client_id);
            }
        }else{
            $password ='9dfa98d4c6f717c59455d5be4261b3d36942617f';
            $key_p ='Admin123';
        }




        $this->load->library('form_validation');
        $config = array(
            'upload_path' => "./upload",
            'allowed_types' => "gif|jpg|png|pdf|doc|docx|xls|xlsx|ppt|pptx",
            'overwrite' => TRUE,
            'max_size' => "204800000",
            'file_name' => time(),
            'remove_spaces' => true
        );

        $this->load->library('upload', $config);

        $file = null;
        $contract_attachment = $_FILES["contract_attachment"]["name"];
        if (isset($_FILES)) {
            if ($this->upload->do_upload('contract_attachment')) {
                $contract_attachment = $this->upload->data();
                $filename = $contract_attachment['file_name'];
                $file = './upload/' . $filename;
            } else {
               // $this->session->set_flashdata('error', 'Upload Attached Failed');
               // redirect('editOldClient/' . $client_id);
            }
        }

        $data['clientInfo'] = $this->client_model->getClientInfo($client_id);



            $company=$data['clientInfo']->company;

            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
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
            $data1 = $CI->load->database($config, TRUE);
			$this->mynewdb =$data1;

            $count  = $this->mynewdb->query("SELECT count(*) as count from sma_users where username='".$username."' ")->result_array()[0]['count'];



            if($count==0){
                if($group_id>4){
                    if(!$biller){
                        $this->mynewdb->query("INSERT INTO sma_companies (group_name, name, company  , email, phone,logo,brId) VALUES('biller','".$first_name."','".$first_name."','".$email."', '".$phone."','empty.png','".$brId."') ");
                        $biller = $this->mynewdb->insert_id();
                    }
                }
                $this->mynewdb->query("INSERT INTO sma_users (ip_address, username, password  , email, created_on, active, first_name, last_name, company, phone,gender, group_id, warehouse_id, biller_id, key_p,brId) VALUES(0x0000,'".$username."','".$password."','".$email."',  '".time()."', 1, '".$first_name."', '".$last_name."', '".$company."', '".$phone."', 'male', '".$group_id."', '".$warehouse."', '".$biller."', '".$key_p."','".$brId."') ");
            }else{
                $this->session->set_flashdata('error', 'Username is unique');
                redirect('editOldClient/' . $client_id);
            }


        $clientTransaction = [
            'client_id' => $client_id,
            'created_by' => $this->vendorId,
            'company_type' => $company_type,
            'paid_at' =>  date('Y-m-d H:i:s'),
            'add_user' =>1,

            'comments' => $group_name.'/'.$username,
            'renewed_at' => date('Y-m-d H:i:s'),
            'contract_attachment' => $file,
        ];



        $result = $this->client_model->createTransaction($clientTransaction);
        if ($result == true) {
            $process = 'Client Add User '.$group_name.'/'.$username.' for company '.$company;
            $processFunction = 'Admin/addClientUser';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Client successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Client update failed');
        }

        redirect('editOldClient/' . $client_id);


    }



    function addClientNearpay()
    {
        $client_id = $this->input->post('id');
        $company_type = $this->input->post('company_type');
        $biller_id = $this->input->post('biller_jwt');
        $terminal_id = trim($this->input->post('terminal_id'));
        $t_id = trim($this->input->post('t_id'));







        $this->load->library('form_validation');
        $config = array(
            'upload_path' => "./upload",
            'allowed_types' => "gif|jpg|png|pdf|doc|docx|xls|xlsx|ppt|pptx",
            'overwrite' => TRUE,
            'max_size' => "204800000",
            'file_name' => time(),
            'remove_spaces' => true
        );

        $this->load->library('upload', $config);

        $file = null;
        $contract_attachment = $_FILES["contract_attachment"]["name"];
        if (isset($_FILES)) {
            if ($this->upload->do_upload('contract_attachment')) {
                $contract_attachment = $this->upload->data();
                $filename = $contract_attachment['file_name'];
                $file = './upload/' . $filename;
            } else {
               // $this->session->set_flashdata('error', 'Upload Attached Failed');
               // redirect('editOldClient/' . $client_id);
            }
        }



$url = "https://domain1.com/nearpay_jwt/generate.php";
//$url = "http://localhost/nearpay_jwt/generate.php";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Content-Type: application/json",
   "accept: application/json",

);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data1 = '{"client_uuid": "fca8006e-e730-45db-88ef-ea481231e9d5","terminal_id": "'.$terminal_id.'"}';

curl_setopt($curl, CURLOPT_POSTFIELDS, $data1);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resultCurl = json_decode( curl_exec( $curl ) );

curl_close($curl);
if ($resultCurl->token) {




        $data['clientInfo'] = $this->client_model->getClientInfo($client_id);


            $company=$data['clientInfo']->company;

            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
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
            $data1 = $CI->load->database($config, TRUE);
			$this->mynewdb =$data1;


                $this->mynewdb->query("UPDATE `sma_companies` SET `jwt_token` ='".$resultCurl->token."',`terminal_id` ='".$t_id."',`terminal_tid` ='".$terminal_id."'  WHERE `id` = ".$biller_id."; ");


        $clientTransaction = [
            'client_id' => $client_id,
            'created_by' => $this->vendorId,
            'company_type' => $company_type,
            'paid_at' =>  date('Y-m-d H:i:s'),
            'add_nearpay' =>1,

            'comments' => $terminal_id.'/'.$biller_id,
            'renewed_at' => date('Y-m-d H:i:s'),
            'contract_attachment' => $file,
        ];



        $result = $this->client_model->createTransaction($clientTransaction);
        if ($result == true) {
            $process = 'Client Add JWT Nearpay '.$terminal_id.'/'.$biller_id.' for company '.$company;
            $processFunction = 'Admin/addClientNearpay';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Client successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Client update failed');
        }

        }else{
                $this->session->set_flashdata('error', 'token not create');
                redirect('editOldClient/' . $client_id);
            }


        redirect('editOldClient/' . $client_id);


    }

    function editOldClientDT($id = null)
    {
        $this->load->library('datatables');
        $this->datatables->select("
                created_by,
                paid_at,
                renewed_at,
                isPOS,
                isAccounting,
                automatedEntries,
                isManufacturing,
                automatedEntriesOffline,
                renew_subscription,
                comments,
                created_at,
                tbl_users.name,
                contract_attachment,
                isFoodics,
                isStore,
                isFixedAsset,
                isGrouping,
                isSynchronizewithZATCA,
                add_warehouse,
                add_user,
                isAutoSynchronizeZATCA,
                isHR,
                add_whatsapp_settings,
                inv_no,
                isAutoSynchronizeZATCAPOS,
                isQuroosh,
                isNearpay,
                add_nearpay,
                add_branch
            ")
            ->from('client_transactions')
            ->join('tbl_users', 'tbl_users.id=client_transactions.created_by', 'inner')
            ->where('client_id', $id);
        echo $this->datatables->generate();
    }

    /**
     * This function is used to edit the user informations
     */
    function editApiSync($id, $key, $is_private_key)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Please select client');

            redirect('clientListingApi');
        }
        if (!$key) {
            $this->session->set_flashdata('error', 'Please select key');

            redirect('clientListingApi');
        }

        $this->load->library('pagination');

        $result = $this->api_model->updateSynchApiKey($key, $is_private_key);

        if ($result > 0) {
            $process = 'Sync Api for Key ' . $key;
            $processFunction = 'Admin/editApiSync';
            $this->logrecord($process, $processFunction);

            $this->session->set_flashdata('success', 'Api successfully changr Sync status for key : ' . $key);
        } else {
            $this->session->set_flashdata('error', 'Api sync failed for Key : ' . $key);
        }


        redirect('clientListingApi/' . $id);
    }


    function editUser()
    {
        $this->load->library('form_validation');

        $userId = $this->input->post('id');

        $this->form_validation->set_rules('fname', 'Full Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
        $this->form_validation->set_rules('password', 'Password', 'matches[cpassword]|max_length[20]');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'matches[password]|max_length[20]');
        $this->form_validation->set_rules('role', 'Role', 'trim|required|numeric');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|min_length[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->editOld($userId);
        } else {
            $name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
            $email = $this->security->xss_clean($this->input->post('email'));
            $password = $this->input->post('password');
            $roleId = $this->input->post('role');
            $mobile = $this->security->xss_clean($this->input->post('mobile'));
            $status = $this->vendorId==$userId ? 1 : $this->input->post('status');


            $userInfo = array();

            if (empty($password)) {
                $userInfo = array(
                    'email' => $email, 'roleId' => $roleId, 'name' => $name,
                    'mobile' => $mobile, 'status' => $status, 'updatedBy' => $this->vendorId, 'updatedDtm' => date('Y-m-d H:i:s')
                );
            } else {
                $userInfo = array(
                    'email' => $email, 'password' => getHashedPassword($password), 'roleId' => $roleId,
                    'name' => ucwords($name), 'mobile' => $mobile, 'status' => $status, 'updatedBy' => $this->vendorId,
                    'updatedDtm' => date('Y-m-d H:i:s')
                );
            }

            $result = $this->user_model->editUser($userInfo, $userId);

            if ($result == true) {
                $process = 'User Update';
                $processFunction = 'Admin/editUser';
                $this->logrecord($process, $processFunction);

                $this->session->set_flashdata('success', 'User successfully updated');
            } else {
                $this->session->set_flashdata('error', 'User update failed');
            }

            redirect('userListing');
        }
    }

    function editClient()
    {
        $this->load->library('form_validation');

        $id = $this->input->post('id');

        $this->form_validation->set_rules('name', 'Full Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('name_en', 'Email', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('company', 'Company Name', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('syncEveryMinues', 'Sync Every Minutes', 'trim|required|max_length[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->editOldClient($id);
        } else {
            $name = $this->input->post('name');
            $name_en = $this->input->post('name_en');
            $company = $this->input->post('company');
            $syncEveryMinues = $this->input->post('syncEveryMinues');
            $company_type = $this->input->post('company_type');
            $tel_no = $this->input->post('tel_no');
            $activity_id = $this->input->post('activity_id');
            $address = $this->input->post('address');
            $status = $this->input->post('status');
            $agent_id = $this->input->post('agent_id') ? $this->input->post('agent_id') : null;
            $inv_no = $this->input->post('inv_no') ? $this->input->post('inv_no') : null;



            $status2=0;
            if($status==2){
                $status=1;
                $status2=1;
            }

            $clientInfo = array(
                'name' => $name,
                'name_en' => $name_en,
                'company' => $company,
                'syncEveryMinues' => $syncEveryMinues,
                'company_type' => $company_type,
                'tel_no' => $tel_no,
                'activity_id' => $activity_id,
                'address' => $address,
                'status' => $status,
                'agent_id' => $agent_id,
                'inv_no' => $inv_no,
                'updatedBy' => $this->vendorId,
                'updated_at' => date('Y-m-d H:i:s'),

            );

            if($status2==1){
                $renewed_at = date('Y-m-d H:i:s');
                $clientInfo['created_at']=$renewed_at;

            }

            if($status==0){
                $clientInfo['reason']=trim($this->input->post('reason'));
            }


            $result = $this->client_model->editClient($clientInfo, $id);


            if ($result == true) {
                $process = 'Client Update for company '.$company;
                $processFunction = 'Admin/editClient';
                $this->logrecord($process, $processFunction);



                $config['dsn'] = '';
                $config['hostname'] = 'localhost';
                $config['username'] = 'cpesoft';
                $config['password'] = 'enter-password';
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
                $data = $CI->load->database($config, TRUE);
                $this->mynewdb =$data;



       $this->mynewdb->query("UPDATE sma_settings SET site_name='".$name."',site_ename='".$name_en."',syncEveryMinues='".$syncEveryMinues."' ");




                $this->session->set_flashdata('success', 'Client successfully updated');
            } else {
                $this->session->set_flashdata('error', 'Client update failed');
            }

            redirect('clientListing');
        }
    }

    function addClientSubscription()
    {
        $client_id = $this->input->post('id');
        $company_type = $this->input->post('company_type');
        $inv_no = $this->input->post('inv_no') ? $this->input->post('inv_no') : null;
        $renewed_at = $this->input->post('renewed_at') ? $this->input->post('renewed_at') :  date('Y-m-d H:i:s');

        $this->load->library('form_validation');
        $config = array(
            'upload_path' => "./upload",
            'allowed_types' => "gif|jpg|png|pdf|doc|docx|xls|xlsx|ppt|pptx",
            'overwrite' => TRUE,
            'max_size' => "204800000",
            'file_name' => time(),
            'remove_spaces' => true
        );

        $this->load->library('upload', $config);

        $file = null;
        $contract_attachment = $_FILES["contract_attachment"]["name"];
        if (isset($_FILES)) {
            if ($this->upload->do_upload('contract_attachment')) {
                $contract_attachment = $this->upload->data();
                $filename = $contract_attachment['file_name'];
                $file = './upload/' . $filename;
            } else {
               // $this->session->set_flashdata('error', 'Upload Attached Failed');
               // redirect('editOldClient/' . $client_id);
            }
        }

        $clientTransaction = [
            'client_id' => $client_id,
            'created_by' => $this->vendorId,
            'company_type' => $company_type,
            'paid_at' =>  date('Y-m-d H:i:s'),
             'renew_subscription' =>1,

            'comments' => $comments,
            'renewed_at' => date('Y-m-d H:i:s', strtotime($renewed_at)),
            'contract_attachment' => $file,
            'inv_no' => $inv_no,
        ];

        $clientInfo = array(
            'renewal_date' => date('Y-m-d H:i:s', strtotime($renewed_at)),

        );

        $result1 = $this->client_model->editClient($clientInfo, $client_id);

        $result = $this->client_model->createTransaction($clientTransaction);
        if ($result == true) {
            $process = 'Client Update subscription for company '.$company;
            $processFunction = 'Admin/addClientSubscription';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Client successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Client update failed');
        }

        redirect('editOldClient/' . $client_id);


    }


    function addZatcaOTP()
    {
        $client_id = $this->input->post('id');
        $company = $this->input->post('company');
        $otp = $this->input->post('otp');
        $csr = $this->input->post('csr');



        $this->load->library('form_validation');









$host = "https://gw-fatoora.zatca.gov.sa/e-invoicing/core/compliance";




$ch            = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $host );
    //curl_setopt( $ch, CURLOPT_URL, 'https://api-sandbox.foodics.com/oauth/token' );
    $json='{"csr": "'.$csr.'"}';

    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS,  $json  );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array("OTP: ".$otp,"Accept-Version: V2" ,"Content-Type: application/json") );


    $resultCurl = json_decode( curl_exec( $ch ) );

curl_close($ch);
if ($resultCurl->binarySecurityToken) {
$config['dsn'] = '';
                    $config['hostname'] = 'localhost';
                    $config['username'] = 'cpesoft';
                    $config['password'] = 'enter-password';
                    $config['database'] = 'cpesoft_'.$company;
                    /*$config['username'] = 'root';
                    $config['password'] = '';
                    $config['database'] = $company;*/
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

                    $this->mynewdb->query("UPDATE  `sma_zatca_config` set binarySecurityToken='".$resultCurl->binarySecurityToken."',secret='".$resultCurl->secret."',requestID='".$resultCurl->requestID."'  WHERE id =1;");





}


        //$result1 = $this->client_model->editClient($clientInfo, $client_id);

        //$result = $this->client_model->createTransaction($clientTransaction);
        if ($resultCurl->binarySecurityToken) {
            $process = 'Add Zatca OTP for company '.$company;
            $processFunction = 'Admin/addZatcaOTP';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Zatca OTP successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Zatca OTP update failed');
        }

        redirect('clientListingSZatca/' . $client_id);


    }
    
    function addZatcaOTP2()
    {
        $client_id = $this->input->post('id');
        $company = $this->input->post('company');
        $otp = $this->input->post('otp');
        $csr = $this->input->post('csr');



        $this->load->library('form_validation');









$host = "https://gw-fatoora.zatca.gov.sa/e-invoicing/core/compliance";




$ch            = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $host );
    //curl_setopt( $ch, CURLOPT_URL, 'https://api-sandbox.foodics.com/oauth/token' );
    $json='{"csr": "'.$csr.'"}';

    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS,  $json  );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array("OTP: ".$otp,"Accept-Version: V2" ,"Content-Type: application/json") );


    $resultCurl = json_decode( curl_exec( $ch ) );

curl_close($ch);
if ($resultCurl->binarySecurityToken) {
$config['dsn'] = '';
                    $config['hostname'] = 'localhost';
                    $config['username'] = 'cpesoft';
                    $config['password'] = 'enter-password';
                    $config['database'] = 'cpesoft_'.$company;
                    /*$config['username'] = 'root';
                    $config['password'] = '';
                    $config['database'] = $company;*/
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


                    $this->mynewdb->query("INSERT INTO `sma_zatca_config` (`id`, `binarySecurityToken`, `secret`, `CertDigest`, `X509IssuerName`, `X509SerialNumber`, `PrivateKey`, `cert`, `cert_public_key`, `cert_signature`, `requestID`, `api_type`) VALUES ('2', '".$resultCurl->binarySecurityToken."', '".$resultCurl->secret."', '', '', '', '', '', '', '', '".$resultCurl->requestID."', 'core');");



}


        //$result1 = $this->client_model->editClient($clientInfo, $client_id);

        //$result = $this->client_model->createTransaction($clientTransaction);
        if ($resultCurl->binarySecurityToken) {
            $process = 'Add Zatca OTP for company '.$company;
            $processFunction = 'Admin/addZatcaOTP2';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Zatca OTP successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Zatca OTP update failed');
        }

        redirect('clientListingSZatca/' . $client_id);


    }



    function addClientService()
    {
        $client_id = $this->input->post('id');
        $company_type = $this->input->post('company_type');
        $isPOS = $this->input->post('isPOS') == 'on' ? true : false;
        $isQuroosh = $this->input->post('isQuroosh') == 'on' ? true : false;
        $isAccounting = $this->input->post('isAccounting') == 'on' ? true : false;
        $isGrouping = $this->input->post('isGrouping') == 'on' ? true : false;
        $isManufacturing = $this->input->post('isManufacturing') == 'on' ? true : false;
        $isStore = $this->input->post('isStore') == 'on' ? true : false;
        $isSynchronizewithZATCA = $this->input->post('isSynchronizewithZATCA') == 'on' ? true : false;
        $isNearpay = $this->input->post('isNearpay') == 'on' ? true : false;
        $automatedEntries = $this->input->post('automatedEntries') == 'on' ? true : false;
        $automatedEntriesOffline = $this->input->post('automatedEntriesOffline') == 'on' ? true : false;
        $renewed_at = $this->input->post('renewed_at') ? $this->input->post('renewed_at') :  date('Y-m-d H:i:s');
        $comments = $this->input->post('comments');
        $isFoodics = $this->input->post('isFoodics') == 'on' ? true : false;
        $isFixedAsset = $this->input->post('isFixedAsset') == 'on' ? true : false;
        $isAutoSynchronizeZATCA = $this->input->post('isAutoSynchronizeZATCA') == 'on' ? true : false;
        $isAutoSynchronizeZATCAPOS = $this->input->post('isAutoSynchronizeZATCAPOS') == 'on' ? true : false;
        $isHR = $this->input->post('isHR') == 'on' ? true : false;


        $this->load->library('form_validation');
        $config = array(
            'upload_path' => "./upload",
            'allowed_types' => "gif|jpg|png|pdf|doc|docx|xls|xlsx|ppt|pptx",
            'overwrite' => TRUE,
            'max_size' => "204800000",
            'file_name' => time(),
            'remove_spaces' => true
        );

        $this->load->library('upload', $config);

        $file = null;
        $contract_attachment = $_FILES["contract_attachment"]["name"];
        if (isset($_FILES)) {
            if ($this->upload->do_upload('contract_attachment')) {
                $contract_attachment = $this->upload->data();
                $filename = $contract_attachment['file_name'];
                $file = './upload/' . $filename;
            } else {
               // $this->session->set_flashdata('error', 'Upload Attached Failed');
               // redirect('editOldClient/' . $client_id);
            }
        }

        $clientInfoBeforeUpt = $this->client_model->getClientInfo($client_id);
        $clientTransaction = [
            'client_id' => $client_id,
            'created_by' => $this->vendorId,
            'company_type' => $clientInfoBeforeUpt->company_type,
            'paid_at' =>  date('Y-m-d H:i:s'),
            // 'renewed_at' => date('Y-m-d H:i:s'),
            'isPOS' => $isPOS,
            'isQuroosh' => $isQuroosh,
            'isAccounting' => $isAccounting,
            'isGrouping' => $isGrouping,
            'isManufacturing' => $isManufacturing,
            'isStore' => $isStore,
            'isSynchronizewithZATCA' => $isSynchronizewithZATCA,
            'isNearpay' => $isNearpay,
            'automatedEntries' => $automatedEntries,
            'automatedEntriesOffline' => $automatedEntriesOffline,
            'comments' => $comments,
            'renewed_at' => date('Y-m-d H:i:s', strtotime($renewed_at)),
            'isFixedAsset' => $isFixedAsset,
            'isFoodics' => $isFoodics,
            'isElectronicLinking' => $isFoodics,
            'contract_attachment' => $file,
            'isAutoSynchronizeZATCA' => $isAutoSynchronizeZATCA,
            'isAutoSynchronizeZATCAPOS' => $isAutoSynchronizeZATCAPOS,
            'isHR' => $isHR,
            ];



        /*$automatedEntries=0;
        if($this->input->post('automatedEntries'))
        $automatedEntries=1;

        $automatedEntriesOffline=0;
        if($this->input->post('automatedEntriesOffline'))
        $automatedEntriesOffline=1;
*/


        //if($isPOS || $isAccounting || $isManufacturing || $automatedEntries || $automatedEntriesOffline){
            if($isAccounting==1){
                $clientInfo['isAccounting']=1;
            }
            if($isGrouping==1){
                $clientInfo['isGrouping']=1;
            }
            if($isPOS==1){
                $clientInfo['isPOS']=1;
            }
            if($isQuroosh==1){
                $clientInfo['isQuroosh']=1;
            }
            if($isFoodics==1){
                $clientInfo['isFoodics']=1;
                $clientInfo['isElectronicLinking']=1;

            }
            if($isFixedAsset==1){
                $clientInfo['isFixedAsset']=1;
            }
            if($isManufacturing==1){
                $clientInfo['isManufacturing']=1;
            }
            if($isStore==1){
                $clientInfo['isStore']=1;
            }
            if($isSynchronizewithZATCA==1){
                $clientInfo['isSynchronizewithZATCA']=1;
            }
            if($isNearpay==1){
                $clientInfo['isNearpay']=1;
            }
            if($isAutoSynchronizeZATCA==1){
                $clientInfo['isAutoSynchronizeZATCA']=1;
            }
            if($isAutoSynchronizeZATCAPOS==1){
                $clientInfo['isAutoSynchronizeZATCAPOS']=1;
            }
            if($isHR==1){
                $clientInfo['isHR']=1;
            }
             if($automatedEntries==1){
				$clientInfo['automatedEntries']=1;
             }
			if($automatedEntriesOffline){
				$clientInfo['automatedEntriesOffline']=1;
            }
        //print_r($clientInfo);
        //exit;


            $result1 = $this->client_model->editClient($clientInfo, $client_id);



        $result = $this->client_model->createTransaction($clientTransaction);

        $clientInfo = $this->client_model->getClientInfo($client_id);
        $company = '';

if(!empty($clientInfo))
{
//foreach ($clientInfo as $uf)
{
    //$company = $uf->company;
    $company = $clientInfo->company;

}
}else{
        $this->session->set_flashdata('error', 'Not found any Api-Keys');

            redirect('clientListing');
}


        if ($result == true) {
            $process = 'Client Update Service for company '.$company;
            $processFunction = 'Admin/addClientService';
            $this->logrecord($process, $processFunction);




            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
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
            $data = $CI->load->database($config, TRUE);
			$this->mynewdb =$data;



   //$this->mynewdb->query("UPDATE sma_settings SET site_name='".$name."',site_ename='".$name_en."',syncEveryMinues='".$syncEveryMinues."' ");
   $this->mynewdb->query("UPDATE sma_settings SET pos='".$isPOS."' ");
	if(isset($automatedEntries) && $automatedEntries==1){
		$this->mynewdb->query("UPDATE sma_settings SET accounting='".$automatedEntries."' ");
	}
	if(isset($automatedEntriesOffline) && $automatedEntriesOffline==1){
		$this->mynewdb->query("UPDATE sma_settings SET accountingOffline='".$automatedEntriesOffline."' ");
	}


    if($isFixedAsset==1){
        $this->mynewdb->query("ALTER TABLE `sma_settings` ADD `isFixedAsset` TINYINT(1) NOT NULL DEFAULT '1' ");
        $this->mynewdb->query("ALTER TABLE `sma_settings` ADD `aipayment_prefix` VARCHAR(20) NULL DEFAULT NULL ");
        $this->mynewdb->query("ALTER TABLE `sma_order_ref` ADD `aipay` INT(11) NOT NULL DEFAULT '1' ");
        $this->mynewdb->query("ALTER TABLE `sma_gl_accounts_settings` ADD `depreciation_allowance_acct` INT(11) NOT NULL , ADD `depreciation_expenses_acct` INT(11) NOT NULL , ADD `profits_sale_fixed_assets_acct` INT(11) NOT NULL , ADD `losses_on_fixed_assets_acct` INT(11) NOT NULL  ");
        $this->mynewdb->query("ALTER TABLE `sma_gl_accounts_settings` ADD `revaluation_profite_fixed_assets_acct` INT(11) NOT NULL , ADD `revaluation_losses_fixed_assets_acct` INT(11) NOT NULL ;");
        $this->mynewdb->query("ALTER TABLE `sma_gl_accounts_settings` ADD `isAutoDepreciation` TINYINT(1) NOT NULL DEFAULT '0', ADD `depreciation_period` VARCHAR(10) NOT NULL DEFAULT 'annual';");
        $this->mynewdb->query("ALTER TABLE `sma_gl_accounts_settings` ADD `latest_depreciation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");

                $accountingdb='cpesoft_fixedassets';
                $config['dsn'] = '';
                $config['hostname'] = 'localhost';
                $config['username'] = 'cpesoft';
                $config['password'] = 'enter-password';
                $config['database'] = $accountingdb;
                //$config['username'] = 'root';
                //$config['password'] = '';
                //$config['database'] = $accountingdb;
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
                $data = $CI->load->database($config, TRUE);
                $this->accdb =$data;

                $tables = $this->accdb->query("SHOW TABLES")->result_array();
                foreach ($tables as $key => $val) {
       $this->mynewdb->query('CREATE TABLE ' . $val['Tables_in_'.$accountingdb] . ' LIKE '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
       $this->mynewdb->query('INSERT ' . $val['Tables_in_'.$accountingdb] . ' SELECT * FROM '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
      }
                   $this->mynewdb->query("SET FOREIGN_KEY_CHECKS=0"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_group MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_group ADD CONSTRAINT sma_ast_group_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_fixed_assets MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_fixed_assets ADD CONSTRAINT sma_ast_fixed_assets_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_fixed_assets ADD CONSTRAINT ast_fixed_assets_grp_FK FOREIGN KEY (group_id) REFERENCES sma_ast_group(id)"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_parent_classes MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_parent_classes ADD CONSTRAINT sma_ast_parent_classes_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_sub_classes MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_sub_classes ADD CONSTRAINT sma_ast_sub_classes_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_sub_classes ADD CONSTRAINT ast_sub_classes_grp_FK FOREIGN KEY (parent_id) REFERENCES sma_ast_parent_classes(id)"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_revaluations MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
                   $this->mynewdb->query("ALTER TABLE sma_ast_revaluations ADD CONSTRAINT sma_ast_revaluations_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table




       }


       if(isset($clientInfo->isFoodics) && $clientInfo->isFoodics==1){
        $this->mynewdb->query("ALTER TABLE sma_settings ADD isFoodics TINYINT(1) NOT NULL DEFAULT '1' ");
        $this->mynewdb->query("UPDATE sma_settings SET accountingOnly='1' ");
        $this->mynewdb->query("CREATE TABLE IF NOT EXISTS sma_foodics_settings (id int(11)  NOT NULL AUTO_INCREMENT,name varchar(100) COLLATE utf8_unicode_ci  NULL,description varchar(250) COLLATE utf8_unicode_ci  NULL,client_id varchar(100) COLLATE utf8_unicode_ci  NULL,secret varchar(100) COLLATE utf8_unicode_ci  NULL,created_by int(11)  NULL,created_at timestamp  NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"); // blog its refere to Table
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD scopes TEXT  NULL, ADD access_token TEXT  NULL ");
        $this->mynewdb->query("ALTER TABLE sma_gl_accounts ADD account_ref_uuid TEXT NULL");
        $this->mynewdb->query("ALTER TABLE sma_gl_payment_methods ADD uuid TEXT NULL");
        $this->mynewdb->query("ALTER TABLE sma_gl_entries ADD uuid TEXT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_order DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_purchase DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_transfer DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_inventory_counts DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_customers DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_branches DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_Payment_types DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_suppliers DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_account_transactions DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD latest_sync_destruction_inventory DATETIME NULL DEFAULT NULL");
        $this->mynewdb->query("ALTER TABLE sma_foodics_settings ADD business_timezone VARCHAR(20) NULL DEFAULT NULL");
        $this->mynewdb->query("INSERT INTO sma_foodics_settings (name,description,client_id,secret,created_by,scopes) VALUES ('ArkanPro','Accounting Software','99548c9d-cb83-49d2-a6d5-874098079002','xGR1dxBiWJbxdJWyzwF5dgqvdDaqxd7YXlTAhjPA','1','general.read,orders.list,inventory.transactions.read,inventory.settings.read,operations.read,customers.list,customers.accounts.read') ");
        $this->mynewdb->query("ALTER TABLE sma_settings ADD isElectronicLinking TINYINT(1) NOT NULL DEFAULT '1' ");
        }


        if($isSynchronizewithZATCA==1){
            $this->mynewdb->query("CREATE TABLE IF NOT EXISTS `sma_sale_documents` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `sale_id` int(11) UNSIGNED NOT NULL,  `stamp_hash` text,  `stamp_public_key` text,  `stamp_sign` text,  `signing_time` varchar(50) DEFAULT NULL,  `ECDSA_signature` text,  `signed` tinyint(1) NOT NULL DEFAULT '0',  `qr` text,  `synch_hash` LONGTEXT,  `clearedInvoice` LONGTEXT,  PRIMARY KEY (`id`),  KEY `sale_id` (`sale_id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
            $this->mynewdb->query("ALTER TABLE `sma_sales` ADD `synch` TINYINT NOT NULL DEFAULT '0' ; ");
            $this->mynewdb->query("ALTER TABLE `sma_sales` ADD `xml_file_path` VARCHAR(255) NULL DEFAULT NULL ; ");
            $this->mynewdb->query("ALTER TABLE `sma_settings` ADD `isSynchronizewithZATCA` TINYINT(1) NOT NULL DEFAULT '1'; ");
            $this->mynewdb->query("ALTER TABLE `sma_sales` ADD `synch_status` VARCHAR(20) NULL DEFAULT NULL ; ");
            $this->mynewdb->query("CREATE TABLE IF NOT EXISTS `sma_zatca_config` (`id` int(11) NOT NULL AUTO_INCREMENT,`binarySecurityToken` text COLLATE utf8_unicode_ci NOT NULL,`secret` text COLLATE utf8_unicode_ci NOT NULL,`CertDigest` text COLLATE utf8_unicode_ci NOT NULL,`X509IssuerName` text COLLATE utf8_unicode_ci NOT NULL,`X509SerialNumber` text COLLATE utf8_unicode_ci NOT NULL,`PrivateKey` text COLLATE utf8_unicode_ci NOT NULL,`cert` text COLLATE utf8_unicode_ci NOT NULL,`cert_public_key` text COLLATE utf8_unicode_ci NOT NULL,`cert_signature` text COLLATE utf8_unicode_ci NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"); // blog its refere to Table
            $this->mynewdb->query("INSERT INTO `sma_zatca_config` (`id`, `binarySecurityToken`, `secret`, `CertDigest`, `X509IssuerName`, `X509SerialNumber`, `PrivateKey`, `cert`, `cert_public_key`, `cert_signature`) VALUES(1, '', '', '', 'CN=eInvoicing', '', '', '', '', '');");
            $this->mynewdb->query("ALTER TABLE `sma_zatca_config` ADD `requestID` VARCHAR(20) NULL DEFAULT NULL;");
            $this->mynewdb->query("ALTER TABLE `sma_zatca_config` ADD `api_type` VARCHAR(20) NOT NULL DEFAULT 'core';");



       }

       if($isNearpay==1){
            $this->mynewdb->query("ALTER TABLE `sma_companies` ADD `jwt_token` TEXT NULL DEFAULT NULL; ");
            $this->mynewdb->query("ALTER TABLE `sma_companies` ADD `terminal_id` VARCHAR(100) NULL DEFAULT NULL, ADD `terminal_tid` VARCHAR(100) NULL DEFAULT NULL; ");
            $this->mynewdb->query("ALTER TABLE `sma_settings` ADD `isNearpay` TINYINT(1) NOT NULL DEFAULT '1'; ");
            $this->mynewdb->query("INSERT INTO `sma_payment_type` (`name`, `second_name`, `isActive`, `isCredit`, `code`, `icon`, `ischeque`, `isCC`) VALUES ('نيرباي', 'Nearpay', '1', '0', 'Nearpay', '', '0', '1'); ");



       }

        if($isAutoSynchronizeZATCA==1){
            $this->mynewdb->query("ALTER TABLE `sma_settings` ADD `isAutoSynchronizeZATCA` TINYINT(1) NOT NULL DEFAULT '1'; ");
        }
        if($isAutoSynchronizeZATCAPOS==1){
            $this->mynewdb->query("ALTER TABLE `sma_settings` ADD `isAutoSynchronizeZATCAPOS` TINYINT(1) NOT NULL DEFAULT '1'; ");
        }

        if($isHR==1){

            $companydb="hr_".$company;
            $data=$this->createDb($companydb);
            $this->hrdb =$data;
            if(($this->hrdb->conn_id)){
                $this->mynewdb->query("ALTER TABLE `sma_settings` ADD `isHR` TINYINT(1) NOT NULL DEFAULT '1'; ");

                $accountingdb='cpesoft_defaulthr';
                $config['dsn'] = '';
                $config['hostname'] = 'localhost';
                $config['username'] = 'cpesoft';
                $config['password'] = 'enter-password';
                $config['database'] = $accountingdb;
                //$config['username'] = 'root';
                //$config['password'] = '';
                //$config['database'] = $accountingdb;
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
                $data = $CI->load->database($config, TRUE);
                $this->accdb =$data;

                $tables = $this->accdb->query("SHOW TABLES")->result_array();
                foreach ($tables as $key => $val) {
                    $this->hrdb->query('CREATE TABLE ' . $val['Tables_in_'.$accountingdb] . ' LIKE '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
                    $this->hrdb->query('INSERT ' . $val['Tables_in_'.$accountingdb] . ' SELECT * FROM '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
                }

            }else{
                $this->session->set_flashdata('error', 'HR creation failed....Please try again');
                redirect('clientListing');
            }
        }


   if($isAccounting==1){
	$this->mynewdb->query("ALTER TABLE sma_settings ADD preAccounting TINYINT(1) NOT NULL DEFAULT '1' ");
	$this->mynewdb->query("ALTER TABLE sma_settings ADD accounting_type VARCHAR(15) NULL DEFAULT NULL ");

			$accountingdb='cpesoft_accounting';
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = $accountingdb;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $accountingdb;
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
            $data = $CI->load->database($config, TRUE);
			$this->accdb =$data;

			$tables = $this->accdb->query("SHOW TABLES")->result_array();
			foreach ($tables as $key => $val) {
   $this->mynewdb->query('CREATE TABLE ' . $val['Tables_in_'.$accountingdb] . ' LIKE '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
   $this->mynewdb->query('INSERT ' . $val['Tables_in_'.$accountingdb] . ' SELECT * FROM '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
  }
               $this->mynewdb->query("SET FOREIGN_KEY_CHECKS=0"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_accounts MODIFY COLUMN userID int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_accounts ADD CONSTRAINT sma_gl_accounts_FK FOREIGN KEY (userID) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_accounts_accountID_IDX USING BTREE ON sma_gl_accounts (accountID,account_type)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_accounts_accountName_IDX USING BTREE ON sma_gl_accounts (accountCategory,accountCategorySub,accountStatement,account_type)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_accounting_period MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_accounting_period ADD CONSTRAINT sma_gl_accounting_period_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_catch_receipts MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_catch_receipts ADD CONSTRAINT sma_gl_catch_receipts_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_catch_receipts_bond_no_IDX USING BTREE ON sma_gl_catch_receipts (bond_no,reference_no,created_by,receipt_name)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_catch_receipts_fromAccountID_IDX USING BTREE ON sma_gl_catch_receipts (fromAccountID,toAccountID)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_accounts_categories_categoryNameEn_IDX USING BTREE ON sma_gl_accounts_categories (categoryNameEn,categoryName,categoryID)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_cost_centers MODIFY COLUMN userID int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_cost_centers ADD CONSTRAINT sma_gl_cost_centers_FK FOREIGN KEY (userID) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_entries MODIFY COLUMN userID int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_entries ADD CONSTRAINT sma_gl_entries_FK FOREIGN KEY (userID) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_entries_reference_no_IDX USING BTREE ON sma_gl_entries (reference_no,entryType)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_opening_balance MODIFY COLUMN userID int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_opening_balance ADD CONSTRAINT sma_gl_opening_balance_FK FOREIGN KEY (userID) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_receipts MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_receipts ADD CONSTRAINT sma_gl_receipts_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_refunding_bonds MODIFY COLUMN created_by int(11) unsigned NOT NULL"); // blog its refere to Table
               $this->mynewdb->query("ALTER TABLE sma_gl_refunding_bonds ADD CONSTRAINT sma_gl_refunding_bonds_FK FOREIGN KEY (created_by) REFERENCES sma_users(id)"); // blog its refere to Table
               $this->mynewdb->query("CREATE INDEX sma_gl_refunding_bonds_bond_no_IDX USING BTREE ON sma_gl_refunding_bonds (bond_no,reference_no,toAccountID,fromAccountID,receipt_name)"); // blog its refere to Table

               if(isset($clientInfo->isGrouping) && $clientInfo->isGrouping==1){
                    $this->mynewdb->query("ALTER TABLE sma_order_ref ADD gl_enrty INT(11) NOT NULL DEFAULT '1'");
                    $this->mynewdb->query("ALTER TABLE sma_gl_entries ADD entry_no VARCHAR(30) NULL DEFAULT NULL");
                }

   }


   if($isGrouping==1){
	$this->mynewdb->query("ALTER TABLE sma_settings ADD isGrouping TINYINT(1) NOT NULL DEFAULT '1' ");
	$this->mynewdb->query("ALTER TABLE sma_settings ADD enable_grp TINYINT(1) NOT NULL DEFAULT '0' ");
	$this->mynewdb->query("ALTER TABLE sma_settings ADD enable_co TINYINT(1) NOT NULL DEFAULT '0' ");
	$this->mynewdb->query("ALTER TABLE sma_settings ADD enable_br TINYINT(1) NOT NULL DEFAULT '1' ");

			$accountingdb='cpesoft_defaultbranches';
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = $accountingdb;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $dbName;
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
            $data = $CI->load->database($config, TRUE);
			$this->accdb =$data;

			$tables = $this->accdb->query("SHOW TABLES")->result_array();
			foreach ($tables as $key => $val) {
   $this->mynewdb->query('CREATE TABLE ' . $val['Tables_in_'.$accountingdb] . ' LIKE '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
   $this->mynewdb->query('INSERT ' . $val['Tables_in_'.$accountingdb] . ' SELECT * FROM '.$accountingdb.'.' . $val['Tables_in_'.$accountingdb]);
  }

               $this->mynewdb->query("SET FOREIGN_KEY_CHECKS=0");
               $this->mynewdb->query("ALTER TABLE sma_main_companies ADD CONSTRAINT main_groups_grp_FK FOREIGN KEY (grpId) REFERENCES sma_main_groups(id)");
               $this->mynewdb->query("ALTER TABLE sma_main_branches ADD CONSTRAINT main_branches_grp_FK FOREIGN KEY (grpId) REFERENCES sma_main_groups(id)");
               $this->mynewdb->query("ALTER TABLE sma_main_branches ADD CONSTRAINT main_branches_co_FK FOREIGN KEY (coId) REFERENCES sma_main_companies(id)");
               $this->mynewdb->query("ALTER TABLE sma_customers_details ADD CONSTRAINT customers_details_customer_id_FK FOREIGN KEY (customer_id) REFERENCES sma_companies(id)");
               $this->mynewdb->query("INSERT INTO sma_customers_details(customer_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_companies WHERE group_name='customer')");
               $this->mynewdb->query("ALTER TABLE sma_suppliers_details ADD CONSTRAINT suppliers_details_supplier_id_FK FOREIGN KEY (supplier_id) REFERENCES sma_companies(id)");
               $this->mynewdb->query("INSERT INTO sma_suppliers_details(supplier_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_companies WHERE group_name='supplier')");
               $this->mynewdb->query("ALTER TABLE sma_units_details ADD CONSTRAINT units_details_unit_id_FK FOREIGN KEY (unit_id) REFERENCES sma_units(id)");
               $this->mynewdb->query("INSERT INTO sma_units_details(unit_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_units )");
               $this->mynewdb->query("ALTER TABLE sma_categories_details ADD CONSTRAINT categories_details_category_id_FK FOREIGN KEY (category_id) REFERENCES sma_categories(id)");
               $this->mynewdb->query("INSERT INTO sma_categories_details(category_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_categories )");
               $this->mynewdb->query("ALTER TABLE sma_brands_details ADD CONSTRAINT brands_details_brand_id_FK FOREIGN KEY (brand_id) REFERENCES sma_brands(id)");
               $this->mynewdb->query("INSERT INTO sma_brands_details(brand_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_brands )");
               $this->mynewdb->query("ALTER TABLE sma_variants_details ADD CONSTRAINT variants_details_variant_id_FK FOREIGN KEY (variant_id) REFERENCES sma_variants(id)");
               $this->mynewdb->query("INSERT INTO sma_variants_details(variant_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_variants )");
               $this->mynewdb->query("CREATE INDEX sma_warehouses_products_product_id_IDX USING BTREE ON sma_warehouses_products (product_id, warehouse_id, grpId, coId, brId)");
               $this->mynewdb->query("ALTER TABLE sma_taxs_details ADD CONSTRAINT taxs_details_tax_rate_id_FK FOREIGN KEY (tax_rate_id) REFERENCES sma_tax_rates(id)");
               $this->mynewdb->query("INSERT INTO sma_taxs_details(tax_rate_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_tax_rates )");
               $this->mynewdb->query("ALTER TABLE sma_currencies_details ADD CONSTRAINT currencies_details_currency_id_FK FOREIGN KEY (currency_id) REFERENCES sma_currencies(id)");
               $this->mynewdb->query("INSERT INTO sma_currencies_details(currency_id, grpId, coId, brId) (SELECT id,grpId,coId,brId FROM sma_currencies )");
               $this->mynewdb->query("ALTER TABLE sma_order_ref ADD brId INT(11) NOT NULL DEFAULT '1'");
               $this->mynewdb->query("ALTER TABLE sma_order_ref ADD CONSTRAINT order_ref_br_FK FOREIGN KEY (brId) REFERENCES sma_main_branches(id)");
               $this->mynewdb->query("CREATE INDEX sma_order_ref_brId_IDX USING BTREE ON sma_order_ref (brId)");
               $this->mynewdb->query("ALTER TABLE sma_sales ADD sale_no VARCHAR(30) NULL DEFAULT NULL");
               $this->mynewdb->query("ALTER TABLE sma_purchases ADD purchase_no VARCHAR(30) NULL DEFAULT NULL");

               if(isset($clientInfo->isAccounting) && $clientInfo->isAccounting==1){
                    $this->mynewdb->query("ALTER TABLE sma_order_ref ADD gl_enrty INT(11) NOT NULL DEFAULT '1'");
                    $this->mynewdb->query("ALTER TABLE sma_gl_entries ADD entry_no VARCHAR(30) NULL DEFAULT NULL");
               }





   }


   if($isManufacturing==1){
	$this->mynewdb->query("ALTER TABLE sma_settings ADD IsManufacturing TINYINT(1) NOT NULL DEFAULT '1' ");
	$this->mynewdb->query("ALTER TABLE sma_transfers ADD man_id INT(11) NULL DEFAULT NULL ");
	$this->mynewdb->query("ALTER TABLE sma_destruction ADD man_id INT(11) NULL DEFAULT NULL ");

			$manufacturingdb='cpesoft_manufacturing';
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = $manufacturingdb;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $dbName;
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
            $data = $CI->load->database($config, TRUE);
			$this->accdb =$data;

			$tables = $this->accdb->query("SHOW TABLES")->result_array();
			foreach ($tables as $key => $val) {
   $this->mynewdb->query('CREATE TABLE ' . $val['Tables_in_'.$manufacturingdb] . ' LIKE '.$manufacturingdb.'.' . $val['Tables_in_'.$manufacturingdb]);
   $this->mynewdb->query('INSERT ' . $val['Tables_in_'.$manufacturingdb] . ' SELECT * FROM '.$manufacturingdb.'.' . $val['Tables_in_'.$manufacturingdb]);
  }


   }


   if($isStore==1){
	$this->mynewdb->query("ALTER TABLE sma_settings ADD isStore TINYINT(1) NOT NULL DEFAULT '1' ");
    $this->mynewdb->query("ALTER TABLE sma_categories ADD shop_id INT(11) NULL DEFAULT NULL; ");
	$this->mynewdb->query("ALTER TABLE sma_products ADD shop_id INT(11) NULL DEFAULT NULL; ");
    $this->mynewdb->query("CREATE TABLE IF NOT EXISTS `sma_shop_settings` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `store_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `password` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `created_by` int(11) DEFAULT NULL,  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,  `token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,  `base_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  `store_id` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,  `theme_id` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `latest_sync_order` datetime DEFAULT NULL,  `latest_sync_customers` datetime DEFAULT NULL,  `latest_sync_category` datetime DEFAULT NULL,  `latest_sync_products` datetime DEFAULT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; ");
    $this->mynewdb->query("INSERT INTO `sma_shop_settings` (`id`,`base_url`) VALUES (1,'https://matjariksa.com'); ");


   }






            $this->session->set_flashdata('success', 'Client successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Client update failed');
        }

        redirect('editOldClient/' . $client_id);
    }

    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function deleteUser()
    {
        $userId = $this->input->post('userId');
        $userInfo = array('isDeleted' => 1, 'status' => 0, 'updatedBy' => $this->vendorId, 'updatedDtm' => date('Y-m-d H:i:s'));

        $result = $this->user_model->deleteUser($userId, $userInfo);

        if ($result > 0) {
            echo (json_encode(array('status' => TRUE)));

            $process = 'User Deletion';
            $processFunction = 'Admin/deleteUser';
            $this->logrecord($process, $processFunction);
        } else {
            echo (json_encode(array('status' => FALSE)));
        }
    }

    /**
     * This function used to show log history
     * @param number $userId : This is user id
     */
    function logHistory($userId = NULL)
    {
        $data['dbinfo'] = $this->user_model->gettablemb('tbl_log', 'cias');
        if (isset($data['dbinfo']->total_size)) {
            if (($data['dbinfo']->total_size) > 1000) {
                $this->backupLogTable();
            }
        }
        $data['userRecords'] = $this->user_model->logHistory($userId);

        $process = 'Log Viewing';
        $processFunction = 'Admin/logHistory';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : User Login History';

        $this->loadViews("logHistory", $this->global, $data, NULL);
    }

    /**
     * This function used to show specific user log history
     * @param number $userId : This is user id
     */
    function logHistorysingle($userId = NULL)
    {
        $userId = ($userId == NULL ? $this->session->userdata("userId") : $userId);
        $data["userInfo"] = $this->user_model->getUserInfoById($userId);
        $data['userRecords'] = $this->user_model->logHistory($userId);

        $process = 'Single Log Viewing';
        $processFunction = 'Admin/logHistorysingle';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : User Login History';

        $this->loadViews("logHistorysingle", $this->global, $data, NULL);
    }

    /**
     * This function used to backup and delete log table
     */
    function backupLogTable()
    {
        $this->load->dbutil();
        $prefs = array(
            'tables' => array('tbl_log')
        );
        $backup = $this->dbutil->backup($prefs);

        date_default_timezone_set('Asia/Kuwait');
        $date = date('d-m-Y H-i');

        $filename = './backup/' . $date . '.sql.gz';
        $this->load->helper('file');
        write_file($filename, $backup);

        $this->user_model->clearlogtbl();

        if ($backup) {
            $this->session->set_flashdata('success', 'Backup and Table cleanup successful');
            redirect('log-history');
        } else {
            $this->session->set_flashdata('error', 'Backup and Table cleanup fail');
            redirect('log-history');
        }
    }

    /**
     * This function used to open the logHistoryBackup page
     */
    function logHistoryBackup()
    {
        $data['dbinfo'] = $this->user_model->gettablemb('tbl_log_backup', 'cias');
        if (isset($data['dbinfo']->total_size)) {
            if (($data['dbinfo']->total_size) > 1000) {
                $this->backupLogTable();
            }
        }
        $data['userRecords'] = $this->user_model->logHistoryBackup();

        $process = 'Backup Log Viewing';
        $processFunction = 'Admin/logHistoryBackup';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : User Backup Login History';

        $this->loadViews("logHistoryBackup", $this->global, $data, NULL);
    }

    /**
     * This function used to delete backup_log table
     */
    function backupLogTableDelete()
    {
        $backup = $this->user_model->clearlogBackuptbl();

        if ($backup) {
            $this->session->set_flashdata('success', 'Table cleanup successful');
            redirect('log-history-backup');
        } else {
            $this->session->set_flashdata('error', 'Table cleanup failed');
            redirect('log-history-backup');
        }
    }

    /**
     * This function used to open the logHistoryUpload page
     */
    function logHistoryUpload()
    {
        $this->load->helper('directory');
        $map = directory_map('./backup/', FALSE, TRUE);

        $data['backups'] = $map;

        $process = 'Yedek Log Yükleme';
        $processFunction = 'Admin/logHistoryUpload';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : User Log Upload';

        $this->loadViews("logHistoryUpload", $this->global, $data, NULL);
    }

    /**
     * This function used to upload backup for backup_log table
     */
    function logHistoryUploadFile()
    {
        $optioninput = $this->input->post('optionfilebackup');

        if ($optioninput == '0' && $_FILES['filebackup']['name'] != '') {
            $config = array(
                'upload_path' => "./uploads/",
                'allowed_types' => "gz|sql|gzip",
                'overwrite' => TRUE,
                'max_size' => "20048000", // Can be set to particular file size , here it is 20 MB(20048 Kb)
            );

            $this->load->library('upload', $config);
            $upload = $this->upload->do_upload('filebackup');
            $data = $this->upload->data();
            $filepath = $data['full_path'];
            $path_parts = pathinfo($filepath);
            $filetype = $path_parts['extension'];
            if ($filetype == 'gz') {
                // Read entire gz file
                $lines = gzfile($filepath);
                $lines = str_replace('tbl_log', 'tbl_log_backup', $lines);
            } else {
                // Read in entire file
                $lines = file($filepath);
                $lines = str_replace('tbl_log', 'tbl_log_backup', $lines);
            }
        } else if ($optioninput != '0' && $_FILES['filebackup']['name'] == '') {
            $filepath = './backup/' . $optioninput;
            $path_parts = pathinfo($filepath);
            $filetype = $path_parts['extension'];
            if ($filetype == 'gz') {
                // Read entire gz file
                $lines = gzfile($filepath);
                $lines = str_replace('tbl_log', 'tbl_log_backup', $lines);
            } else {
                // Read in entire file
                $lines = file($filepath);
                $lines = str_replace('tbl_log', 'tbl_log_backup', $lines);
            }
        }
        // Set line to collect lines that wrap
        $templine = '';

        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;
            // Add this line to the current templine we are creating
            $templine .= $line;

            // If it has a semicolon at the end, it's the end of the query so can process this templine
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                $this->db->query($templine);

                // Reset temp variable to empty
                $templine = '';
            }
        }
        if (empty($lines) || !isset($lines)) {
            $this->session->set_flashdata('error', 'Backup installation failed');
            redirect('log-history-upload');
        } else {
            $this->session->set_flashdata('success', 'Backup installation successful');
            redirect('log-history-upload');
        }
    }

    function clientProcedures($id = NULL)
    {
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;
			if(!$id){
				    $this->session->set_flashdata('error', 'Please select client');

                redirect('clientListing');
			}

            $this->load->library('pagination');
            $clientInfo = $this->client_model->getClientInfo($id);
			$company = '';
if(!empty($clientInfo))
{
    //foreach ($clientInfo as $uf)
    {
        //$company = $uf->company;
        $company = $clientInfo->company;

    }
}else{
		    $this->session->set_flashdata('error', 'Not found any Api-Keys');

                redirect('clientListing');
}


            //$count = $this->client_model->clientListingCount($searchText);
			//

			//$returns = $this->paginationCompress ( "clientListing/", $count, 10 );
            //echo "<strong>New database</strong> : \n",print_r($returns, true), "\n";

           $data['procedureRecords'] = $this->client_model->getProcedures($company);
            $data['id'] = $id;
            $data['company'] = $company;

            $process = 'Client procedures : '.$company;
            $processFunction = 'Admin/clientProcedures';
            $this->logrecord($process,$processFunction);

            $this->global['pageTitle'] = 'ArkanPro : Client procedures : '.$company;

            $this->loadViews("clientProcedures", $this->global, $data, NULL);
    }


    function addNewProcedure($id = NULL)
    {


        if(!$id){
            $this->session->set_flashdata('error', 'Please select client');

        redirect('clientProcedures');
    }
    $clientInfo = $this->client_model->getClientInfo($id);
			$company = '';
if(!empty($clientInfo))
{
    //foreach ($clientInfo as $uf)
    {
        //$company = $uf->company;
        $company = $clientInfo->company;

    }
}

    $this->load->library('pagination');

            $data['id'] = $id;
            $data['company'] = $company;

            $data['procedures_types'] = $this->client_model->getProceduresTypes();

            $this->global['pageTitle'] = 'ArkanPro : Add New Procedure';

            $this->loadViews("addNewProcedure", $this->global, $data, NULL);
    }

    function addNewProcedures($id = NULL)
    {


        if(!$id){
            $this->session->set_flashdata('error', 'Please select client');

        redirect('clientProcedures');
    }

    $clientInfo = $this->client_model->getClientInfo($id);
    $company = '';
if(!empty($clientInfo))
{
//foreach ($clientInfo as $uf)
{
//$company = $uf->company;
$company = $clientInfo->company;

}
}else{
    $this->session->set_flashdata('error', 'Not found any client');

        redirect('clientProcedures');
}


        $this->load->library('form_validation');

            $this->form_validation->set_rules('request_by','Request By','required');
            $this->form_validation->set_rules('comment','Procedure Description','required');
            if($this->input->post('ptId')==3){

                $this->form_validation->set_rules('copied_company_name','Copied Company Name','required');
                $this->form_validation->set_rules('copy_to_warehouse_code','Copy to Warehouse Codes','required');

            }

            if($this->form_validation->run() == FALSE)
            {
                $this->addNewProcedure($id);
                //redirect('clientProcedures/'.$id);
            }
            else
            {

                $request_by = $this->input->post('request_by');
                $comment = $this->input->post('comment');
                $ptId = $this->input->post('ptId');
                $copied_company_name = $ptId==3 ? $this->input->post('copied_company_name') : null;
                $copy_to_warehouse_code = $ptId==3 ? $this->input->post('copy_to_warehouse_code') : null;





                if($ptId ==3){
                $copiedClientInfo = $this->client_model->getClientInfoByCopmany($copied_company_name);

                if(empty($copiedClientInfo))
                {
                    $this->session->set_flashdata('error', 'No client was found with the company name of '.$copied_company_name);
                    redirect('clientProcedures/'.$id);
                }
                }


                if($ptId ==5 || $ptId ==6){

                    if($clientInfo->isAccounting!=1)
                    {
                        $this->session->set_flashdata('error', 'The client does not have an accounting system');
                        redirect('clientProcedures/'.$id);
                    }
                }



                $procedureInfo = array('request_by'=>$request_by, 'comment'=>$comment, 'ptId'=>$ptId, 'company'=> $company, 'copied_company_name'=> $copied_company_name, 'copy_to_warehouse_code'=> $copy_to_warehouse_code,
                                     'createdBy'=>$this->vendorId, 'createdDtm'=>date('Y-m-d H:i:s'));



                $result = $this->client_model->addNewProcedures($procedureInfo);

                if($result > 0)
                {

                    $processDesc="";
                    if($ptId ==1){
                        $processDesc="Delete all data";
                    }
                    if($ptId ==2){
                        $processDesc="Delete sales and purchases data";
                    }
                    if($ptId ==3){
                        $processDesc="Copy products from ".$copied_company_name." client";
                    }
                    if($ptId ==4){
                        $processDesc="Reset password";
                    }

                    if($ptId ==5){
                        $processDesc="Delete accounting data";
                    }

                    if($ptId ==6){
                        $processDesc="Delete the last accounting period";
                    }

                    $process = 'Adding a Procedure for company '.$company.' ( '.$processDesc.' )';
                    $processFunction = 'Admin/addNewProcedures';
                    $this->logrecord($process,$processFunction);


                    $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
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
            $data = $CI->load->database($config, TRUE);
			$this->mynewdb =$data;

            ini_set('memory_limit','2048M');


            $this->load->dbutil();
            if($ptId==3){
                $prefs['tables'][0]="sma_products";
                $prefs['tables'][1]="sma_combo_items";
                $prefs['tables'][2]="sma_units";
                $prefs['tables'][3]="sma_categories";
                $prefs['tables'][4]="sma_variants";
                $prefs['tables'][5]="sma_brands";
                $prefs['tables'][6]="sma_warehouses_products";
            }else if($ptId==4){
                $prefs['tables'][0]="sma_users";
            }else if($ptId==5 || $ptId==6){
                $prefs['tables'][0]="sma_gl_accounting_period";
                $prefs['tables'][1]="sma_gl_accounts";
                $prefs['tables'][2]="sma_gl_accounts_categories";
                $prefs['tables'][3]="sma_gl_accounts_settings";
                $prefs['tables'][4]="sma_gl_catch_receipts";
                $prefs['tables'][5]="sma_gl_cost_centers";
                $prefs['tables'][6]="sma_gl_entries";
                $prefs['tables'][7]="sma_gl_notice_creditor";
                $prefs['tables'][8]="sma_gl_opening_balance";
                $prefs['tables'][9]="sma_gl_opening_balance_items";
                $prefs['tables'][10]="sma_gl_payment_methods";
                $prefs['tables'][11]="sma_gl_receipts";
                $prefs['tables'][12]="sma_gl_refunding_bonds";
            }else{
                $prefs['tables'] = $this->mynewdb->list_tables();
            }
            $backup=$this->dbutil->backup($prefs,$this->mynewdb) ;

            date_default_timezone_set('Asia/Kuwait');
            $date = date('d-m-Y H-i');

            $filename = './backup/'.$company.'-'.$date.'.sql.gz';
            $this->load->helper('file');
            write_file($filename,$backup);



            if($ptId==1){
                $this->mynewdb->query("TRUNCATE TABLE sma_costing ");
                $this->mynewdb->query("UPDATE sma_order_ref SET so=1,po=1,pay=1,pos=1,ex=1");
                $this->mynewdb->query("TRUNCATE TABLE sma_payments ");
                $this->mynewdb->query("TRUNCATE TABLE sma_purchases ");
                $this->mynewdb->query("TRUNCATE TABLE sma_purchase_items ");
                $this->mynewdb->query("TRUNCATE TABLE sma_sales ");
                $this->mynewdb->query("TRUNCATE TABLE sma_sale_items ");
                $this->mynewdb->query("TRUNCATE TABLE sma_products ");
                $this->mynewdb->query("TRUNCATE TABLE sma_units ");
                $this->mynewdb->query("TRUNCATE TABLE sma_categories ");
                $this->mynewdb->query("TRUNCATE TABLE sma_variants ");
                $this->mynewdb->query("TRUNCATE TABLE sma_brands ");
                $this->mynewdb->query("TRUNCATE TABLE sma_warehouses_products ");
                $this->mynewdb->query("TRUNCATE TABLE sma_synch_products ");
                $this->mynewdb->query("TRUNCATE TABLE sma_combo_items ");
                $this->mynewdb->query("TRUNCATE TABLE sma_expenses ");
                if($clientInfo->isAccounting==1){
                    $this->mynewdb->query("TRUNCATE TABLE sma_gl_accounting_period ");
                    $this->mynewdb->query("TRUNCATE TABLE sma_gl_catch_receipts ");
                    $this->mynewdb->query("TRUNCATE TABLE sma_gl_entries ");
                    $this->mynewdb->query("TRUNCATE TABLE sma_gl_notice_creditor ");
                    $this->mynewdb->query("TRUNCATE TABLE sma_gl_opening_balance ");
                    $this->mynewdb->query("TRUNCATE TABLE sma_gl_opening_balance_items ");
                    $this->mynewdb->query("TRUNCATE TABLE sma_gl_receipts ");
                    $this->mynewdb->query("TRUNCATE TABLE sma_gl_refunding_bonds ");
                    $this->mynewdb->query("UPDATE sma_gl_accounts SET openingBalance=0,accountDebit=0,accountCredit=0 ");
                }

            }else if($ptId==2){
                $this->mynewdb->query("TRUNCATE TABLE sma_costing ");
                $this->mynewdb->query("UPDATE sma_order_ref SET so=1,po=1,pay=1,pos=1");
                $this->mynewdb->query("TRUNCATE TABLE sma_payments ");
                $this->mynewdb->query("TRUNCATE TABLE sma_purchases ");
                $this->mynewdb->query("TRUNCATE TABLE sma_purchase_items ");
                $this->mynewdb->query("TRUNCATE TABLE sma_sales ");
                $this->mynewdb->query("TRUNCATE TABLE sma_sale_items ");
                $this->mynewdb->query("UPDATE sma_products SET quantity=0,cost=0 ");
                $this->mynewdb->query("UPDATE sma_warehouses_products SET quantity=0,avg_cost=0 ");

            }else if($ptId==5){
                $this->mynewdb->query("TRUNCATE TABLE sma_gl_accounting_period ");
                $this->mynewdb->query("TRUNCATE TABLE sma_gl_catch_receipts ");
                $this->mynewdb->query("TRUNCATE TABLE sma_gl_entries ");
                $this->mynewdb->query("TRUNCATE TABLE sma_gl_notice_creditor ");
                $this->mynewdb->query("TRUNCATE TABLE sma_gl_opening_balance ");
                $this->mynewdb->query("TRUNCATE TABLE sma_gl_opening_balance_items ");
                $this->mynewdb->query("TRUNCATE TABLE sma_gl_receipts ");
                $this->mynewdb->query("TRUNCATE TABLE sma_gl_refunding_bonds ");
                $this->mynewdb->query("UPDATE sma_gl_accounts SET openingBalance=0,accountDebit=0,accountCredit=0 ");

            }else if($ptId==6){
                $period_year = $this->mynewdb->query("SELECT `period_year` FROM `sma_gl_accounting_period` ORDER BY `id`  DESC limit 1;")->result_array()[0]['period_year'];
                if($period_year){
                    $this->mynewdb->query("DELETE FROM `sma_gl_accounting_period` WHERE period_year='".$period_year."'; ");
                    $this->mynewdb->query("UPDATE `sma_gl_accounting_period` SET `period_year_status` = '1',`period_status` = '1' WHERE period_year='".($period_year-1)."';");

                    $openingId = $this->mynewdb->query("SELECT `id` FROM `sma_gl_opening_balance` WHERE openingDate='".$period_year."-01-01 00:00:00' ORDER BY `id`  DESC limit 1;")->result_array()[0]['id'];
                    if($openingId){
                        $this->mynewdb->query("DELETE FROM `sma_gl_opening_balance` WHERE id='".$openingId."'; ");
                        $this->mynewdb->query("DELETE FROM `sma_gl_opening_balance_items` WHERE openingId='".$openingId."'; ");
                    }

                    $entryID = $this->mynewdb->query("SELECT `entryID` FROM `sma_gl_entries` WHERE entryDate='".$period_year."-01-01 00:00:00' and entryType='Opening_balance' ORDER BY `entryID`  DESC limit 1;")->result_array()[0]['entryID'];
                    if($entryID){
                        $this->mynewdb->query("UPDATE `sma_gl_entries` SET `entryStatus` = '0' WHERE `entryID` = '".$entryID."';");
                        $this->mynewdb->query("UPDATE `sma_gl_entries` SET `entryStatus` = '0' WHERE entryType='Fiscal_year_closing' and `entryDescription`='Fiscal_year_closing#".($period_year-1)."';");
                    }
                }

            }else if($ptId==3){
                $copied_company_name='cpesoft_'.$copied_company_name;
                $this->mynewdb->query('INSERT INTO sma_products (code, name, unit, cost, price, alert_quantity, image, category_id, subcategory_id, cf1, cf2, cf3, cf4, cf5, cf6, quantity, tax_rate, track_quantity, details, warehouse, barcode_symbology, file, product_details, tax_method, type, type2, supplier1, supplier1price, supplier2, supplier2price, supplier3, supplier3price, supplier4, supplier4price, supplier5, supplier5price, promotion, promo_price, start_date, end_date, supplier1_part_no, supplier2_part_no, supplier3_part_no, supplier4_part_no, supplier5_part_no, sale_unit, purchase_unit, brand, slug, featured, weight, hsn_code, views, hide, second_name, hide_pos, product_number, expiry, api, currency_code) (SELECT code, name, unit, cost, price, alert_quantity, image, category_id, subcategory_id, cf1, cf2, cf3, cf4, cf5, cf6, quantity, tax_rate, track_quantity, details, warehouse, barcode_symbology, file, product_details, tax_method, type, type2, supplier1, supplier1price, supplier2, supplier2price, supplier3, supplier3price, supplier4, supplier4price, supplier5, supplier5price, promotion, promo_price, start_date, end_date, supplier1_part_no, supplier2_part_no, supplier3_part_no, supplier4_part_no, supplier5_part_no, sale_unit, purchase_unit, brand, slug, featured, weight, hsn_code, views, hide, second_name, hide_pos, product_number, expiry, api, currency_code FROM '.$copied_company_name.'.sma_products)');
                //$this->mynewdb->query('INSERT sma_products SELECT * FROM '.$copied_company_name.'.sma_products');
                $this->mynewdb->query('UPDATE sma_products SET quantity=0,cost=0 WHERE code in (SELECT code FROM '.$copied_company_name.'.sma_products)');
                $this->mynewdb->query('INSERT sma_combo_items SELECT * FROM '.$copied_company_name.'.sma_combo_items');
                $this->mynewdb->query('INSERT sma_units SELECT * FROM '.$copied_company_name.'.sma_units where id not in (select id from sma_units)');
                $this->mynewdb->query('INSERT sma_categories SELECT * FROM '.$copied_company_name.'.sma_categories where id not in (select id from sma_categories)');
                $this->mynewdb->query('INSERT sma_variants SELECT * FROM '.$copied_company_name.'.sma_variants where id not in (select id from sma_variants)');
                $this->mynewdb->query('INSERT sma_brands SELECT * FROM '.$copied_company_name.'.sma_brands where id not in (select id from sma_brands)');


                foreach(explode(",",$copy_to_warehouse_code) as $wh){
                    $warehouse_id = $this->mynewdb->query("SELECT id FROM sma_warehouses WHERE code='".$wh."'")->result_array()[0]['id'];
                    if($warehouse_id){

                        $this->mynewdb->query('INSERT INTO sma_warehouses_products(product_id, warehouse_id, quantity, avg_cost) SELECT id,'.$warehouse_id.',0,0 FROM sma_products where code in (SELECT code FROM '.$copied_company_name.'.sma_products) and id not in (select product_id from sma_warehouses_products where warehouse_id='.$warehouse_id.')');


                    }
                    
                     /*echo '<pre>';
				print_r($warehouse_id);
				echo '</pre>';
				exit();*/
                }



            }else if($ptId==4){
                $this->mynewdb->query("UPDATE sma_users SET password = '9dfa98d4c6f717c59455d5be4261b3d36942617f' WHERE id = 1");

            }



                    $this->session->set_flashdata('success', 'Procedure created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Procedure creation failed');
                }



                redirect('clientProcedures/'.$id);
            }
        }

    function complaints()
    {
        $data['taskRecords'] = $this->user_model->getComplaints();

        $process = 'All Suggestions and Complaints';
        $processFunction = 'Admin/complaints';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : All Suggestions and Complaints';

        $this->loadViews("complaints", $this->global, $data, NULL);
    }

    function editOldComplaint($complaintId = NULL)
    {
        if ($complaintId == null) {
            redirect('complaints');
        }

        $data['complaintInfo'] = $this->user_model->getComplaintInfo($complaintId);
        $data['complaints_type'] = $this->user_model->getComplaintsType();
        $data['complaints_situations'] = $this->user_model->getComplaintsSituations();

        $this->global['pageTitle'] = 'ArkanPro : Edit Suggestions and Complaints';

        $this->loadViews("editOldComplaint", $this->global, $data, NULL);
    }


    function editComplaint()
    {
        $this->load->library('form_validation');


        $this->form_validation->set_rules('status', 'Status', 'required');

        $complaintId = $this->input->post('complaintId');

        if ($this->form_validation->run() == FALSE) {
            $this->editOldComplaint($complaintId);
        } else {
            $complaintId = $this->input->post('complaintId');


            $statusId = $this->input->post('status');
            $respond = $statusId==3 ? $this->input->post('respond') : null;


            $complaintInfo = array(
                'status' => $statusId,'respond' => $respond, 'respond_by' => $this->vendorId, 'respond_date' => date('Y-m-d H:i:s')
            );

            $result = $this->user_model->editComplaint($complaintInfo, $complaintId);

            if ($result > 0) {
                $process = 'Complaint Editing';
                $processFunction = 'Admin/editComplaint';
                $this->logrecord($process, $processFunction);
                $this->session->set_flashdata('success', 'Complaint edit successful');
            } else {
                $this->session->set_flashdata('error', 'Complaint edit failed');
            }
            redirect('complaints');
        }
    }

    function getClientListing()
    {
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        $draw = $this->input->post('draw');
        $this->load->library('pagination');

        $count = $this->client_model->clientListingCount($searchText);
        //

        $returns = $this->paginationCompress("clientListing/", $count, 10);
        //echo "<strong>New database</strong> : \n",print_r($returns, true), "\n";

        $userRecords = $this->client_model->clientListingV2($searchText, $returns["page"], $returns["segment"]);
        $i=0;
        foreach ($userRecords as $row) {

            $userRecords[$i]->sr = ($i+1);
            if ($row->status == '0'){
            $userRecords[$i]->status ='<div class="label label-danger">Inactive</div>';
            }else if ($row->status == '1'){
                $userRecords[$i]->status ='<div class="label label-success">Active</div>';
            }



            if ($row->isStore == "11"){
            $userRecords[$i]->actions = '<div class="text-center" style="min-width:270px;"><a class="btn btn-sm btn-primary" href="'. base_url() . 'clientListingApi/' . $row->id.' " title="Api Setting">
                <i class="fa fa-retweet"></i>
            </a> |

            <a class="btn btn-sm btn-danger" href="'. base_url() . 'clientListingShopApi/' . $row->id.'" title="Api Setting">
                <i class="fa fa-shopping-cart menu-icon"></i>
            </a> |

            <a class="btn btn-sm btn-info" href="'. base_url() . 'editOldClient/' . $row->id.'" title="Edit">
                <i class="fa fa-pencil"></i>
            </a> |
            <a class="btn btn-sm btn-warning" href="'. base_url().'clientProcedures/'.$row->id.'" title="Procedures">
                <i class="fa fa-wrench"></i>
            </a>';

        }else{
            $userRecords[$i]->actions = '<div class="text-center" style="min-width:270px;"><a class="btn btn-sm btn-primary" href="'. base_url() . 'clientListingApi/' . $row->id.' " title="Api Setting">
                <i class="fa fa-retweet"></i>
            </a> |



            <a class="btn btn-sm btn-info" href="'. base_url() . 'editOldClient/' . $row->id.'" title="Edit">
                <i class="fa fa-pencil"></i>
            </a> |
            <a class="btn btn-sm btn-warning" href="'. base_url().'clientProcedures/'.$row->id.'" title="Procedures">
                <i class="fa fa-wrench"></i>
            </a> |
            <a class="btn btn-sm btn-warning" href="'. base_url().'clientListingAppVersion/'.$row->id.'" title="App Versions">
                <i class="fa fa-apple fa-lg"></i>
            </a>';

        }
        if ($row->isSynchronizewithZATCA == "1"){
            $userRecords[$i]->actions =$userRecords[$i]->actions .' |

            <a class="btn btn-sm btn-primary" style="padding: 4px 4px;" href="'. base_url() . 'clientListingSZatca/' . $row->id.'" title="Fatoora">
                Fatoora
            </a>';
        }
        $userRecords[$i]->actions =$userRecords[$i]->actions .' |

            <a class="btn btn-sm btn-success" href="'. base_url() . 'whatsAppMessages/' . $row->id.'" title="WhatsApp Messages">
                <i class="fa fa-whatsapp fa-lg"></i>
            </a>';
        $userRecords[$i]->actions =$userRecords[$i]->actions .'</div>';
            $i++;
        }
       /* echo '<pre>';
        print_r($userRecords);
        echo '</pre>';
        exit();*/
       // $data['userRecords']=array($data['userRecords']);
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $count,
            "iTotalDisplayRecords" => $count,
            "aaData" => $userRecords
         );
        echo json_encode($response);

    }

    function complaintsNote($complaintId = NULL)
    {
        if ($complaintId == null) {
            redirect('complaints');
        }

        $data['complaintInfo'] = $this->user_model->getComplaintInfo($complaintId)[0];
        $data['complaintsNoteInfo'] = $this->user_model->getComplaintsNoteInfo($complaintId);
        $data["userInfo"] = $this->user_model->getUserInfoById($this->vendorId);
        $data["complaint_id"] = $complaintId;

        /*echo '<pre>';
        print_r($data['complaintInfo']);
        echo '</pre>';
        exit();*/

        $this->global['pageTitle'] = 'ArkanPro : Chat Suggestions and Complaints';

        $this->loadViews("complaints_note", $this->global, $data, NULL);
    }

    function newMessage()
    {

        $title=$this->input->post('title');
        $complaint_id=$this->input->post('complaint_id');
        $userInfo = $this->user_model->getUserInfoById($this->vendorId);
        $complaintInfo = $this->user_model->getComplaintInfo($complaint_id)[0];
        $date=date('Y-m-d H:i:s');

        $messageInfo = array(
            'complaint_id' => $complaint_id,
            'client_name' => $userInfo->name,
            'company' => $complaintInfo->company,
            'date' => $date,
            'title' => $title,
            'user_id' => $this->vendorId,
            'warehouse_code' => $complaintInfo->warehouse_code
        );




        $message_id = $this->user_model->newMessage($messageInfo);
        if ($message_id > 0) {
            $response = array(
                "status" => true,
                "title" => $title,
                "date" => $date
             );
        }else{
            $response = array(
                "status" => false
             );
        }

        echo json_encode($response);
    }

// =====================================================
// cPanel API Request Function
// =====================================================

public function cpanelApi($host, $user, $token, $module, $function, $params = [])
{
    $url = "https://{$host}:2083/execute/{$module}/{$function}";

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: cpanel {$user}:{$token}",
            "Accept: application/json"
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 60
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'success' => false,
            'error'   => $error
        ];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $json = json_decode($response, true);

    if ($json === null) {
        return [
            'success'   => false,
            'http_code' => $httpCode,
            'error'     => 'Invalid JSON response',
            'raw'       => $response
        ];
    }

    return [
        'success'   => ($json['status'] ?? 0) == 1,
        'http_code' => $httpCode,
        'response'  => $json
    ];
}


    public function createDb($company){


    // =====================================================
// cPanel Configuration
// =====================================================

$cpanelHost = 'cpanel.domain1.com';
$cpanelUser = 'cpesoft';
$apiToken   = 'ZUMTPRPPKCW8T4ZOH5HI1F7W0QYFC78E';

// =====================================================
// Database Configuration
// =====================================================
$database = 'cpesoft_'.$company;
$databaseuser = 'databaseuser';



// cPanel requires the username prefix
$fullDbName = $database;
$fullDbUser = $databaseuser;




// =====================================================
// 1. Create Database
// =====================================================

$result = $this->cpanelApi(
    $cpanelHost,
    $cpanelUser,
    $apiToken,
    'Mysql',
    'create_database',
    [
        'name' => $fullDbName
    ]
);



// =====================================================
// 3. Add User To Database + ALL PRIVILEGES
// =====================================================

$result = $this->cpanelApi(
    $cpanelHost,
    $cpanelUser,
    $apiToken,
    'Mysql',
    'set_privileges_on_database',
    [
        'user'       => $fullDbUser,
        'database'   => $fullDbName,
        'privileges' => 'ALL PRIVILEGES'
    ]
);



/*
//include "cpanel-UAPI-php-class-master/cpaneluapi.class.php"; //include the class file
$uapi = new cpanelAPI('cpesoft', 'enter-password', 'cpanel.domain1.com', 'token'); //instantiate the object

$database = 'cpesoft_'.$company;
$databaseuser = 'databaseuser';

$rez=$uapi->uapi->Mysql->create_database(array('name' => $database)); //Create the database
//echo "<strong>New database</strong> : \n",print_r($rez, true), "\n";

//$uapi->uapi->Mysql->create_user(array('name' => $databaseuser, 'password' => $databasepass)); //create a user for the new database


//After you create the user, you must use the set_privileges_on_database function call to grant access to the
//user for a database.
//add the user, set all privileges - add specific privileges by comma separation. e.g. 'DELETE,UPDATE,CREATE,ALTER'
$uapi->uapi->Mysql->set_privileges_on_database(array('user' => $databaseuser, 'database' => $database, 'privileges' => 'ALL'));
*/


			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
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
            if($data = $CI->load->database($config, TRUE)){
                return $data;
            }
            return false;

    }



    function whatsAppMessages($id = NULL)
    {
        if(!$id){
				    $this->session->set_flashdata('error', 'Please select client');

                redirect('clientListing');
			}

            $clientInfo = $this->client_model->getClientInfo($id);
			$company = '';
if(!empty($clientInfo))
{
    //foreach ($clientInfo as $uf)
    {
        //$company = $uf->company;
        $company = $clientInfo->company;

    }
}else{
		    $this->session->set_flashdata('error', 'Not found any Clients');

                redirect('clientListing');
}

        $data['clientInfo'] = $clientInfo;
        $data["userInfo"] = $this->user_model->getUserInfoById($this->vendorId);



        $data['whatsAppMessagesInfo'] = $this->user_model->getWhatsAppMessagesInfo($company);

        $data['id'] = $id;
        $data['company'] = $company;

        /*echo '<pre>';
        print_r($data['whatsAppMessagesInfo']);
        echo '</pre>';
        exit();*/

        $this->global['pageTitle'] = 'ArkanPro : Chat Suggestions and Complaints';

        $this->loadViews("whatsapp_messages", $this->global, $data, NULL);



    }

    function whatsAppMessagestest($id = NULL)
    {
        $tel_no="+967771988430";
        $company="Waleedtest";
        if($tel_no){
            $len = strlen($tel_no);
            if($len>8 && is_numeric($tel_no)){
                if(substr($tel_no, 0, 1) === "+"){
                    $recipient=$tel_no;
                }else{
                    if($len>10){
                        if(substr($tel_no, 0, 2) === "00"){
                            $recipient="+".substr($tel_no, 2);
                        }else{
                            $recipient="+".$tel_no;
                        }
                    }else{
                        if(substr($tel_no, 0, 1) === "0"){
                            $recipient="+966".substr($tel_no, 1);
                        }else{
                            $recipient="+966".$tel_no;
                        }
                    }
                }
                    $message="مرحباً بكم في برنامج CPE!\n\nنشكر لكم اختياركم شركة أركان الاحتراف للاتصالات وتقنية المعلومات. \nيسرنا أن نرحب بكم كعميل جديد لبرنامجنا الرائع.\n\nللدخول إلى حسابكم، يرجى استخدام المعلومات التالية:\n\nالرابط:\nhttps://www.domain1.com\nاسم الشركة: ".$company."  \nاسم المستخدم: admin  \nكلمة المرور: Admin123\n\nنتمنى لكم تجربة ممتعة ومثمرة مع برنامجنا. \nإذا كان لديكم أي استفسارات أو تحتاجون إلى دعم، لا تترددوا في التواصل معنا.\n\nللدعم الفني، نرجو التواصل على الرقم التالي: 0555555555 أو الدخول على الرابط http://www.companypro.com\n\nشركة أركان الاحتراف للاتصالات وتقنية المعلومات\n\nWelcome to the CPE Program!\n\nThank you for choosing Arkan Al-Ihtiraf Communications and Information Technology. \nWe are pleased to welcome you as a new customer to our fantastic program.\n\nTo access your account, please use the following information:\n\nLink:\nhttps://www.domain1.com \nCompany: ".$company."  \nUsername: admin  \nPassword: Admin123\n\nWe hope you have an enjoyable and productive experience with our program. \nIf you have any questions or need support, please do not hesitate to contact us.\n\nFor technical support, please call 0555555555 or visit /www.companypro.com\n \nArkan Al-Ihtiraf Communications and Information Technology";
                    $fields = array("phones"=>"966535295331", "account"=>"0", "doctype"=>"text", "recipient"=>$recipient, "message"=>$message, "token"=>"721790345");
                    $files = array();
                    $url = "http://api.4whatsapp.com/api/Agent_Client_";
                    $curl = curl_init();
                    $url_data = http_build_query($data);
                    $boundary = 'b77a5c561934e089';
                    $delimiter = '-------------' . $boundary;
                    $post_data = '';
                    $eol = "\r\n";

                    $delimiter = '-------------' . $boundary;

                    foreach ($fields as $name => $content) {
                        $post_data .= "--" . $delimiter . $eol
                        . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                        . $content . $eol;
                    }
                    $post_data .= "--" . $delimiter . "--".$eol;
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POST => 1,
                        CURLOPT_POSTFIELDS => $post_data,
                        CURLOPT_HTTPHEADER => array(
                          //"Authorization: Bearer $TOKEN",
                          "Content-Type: multipart/form-data; boundary=" . $delimiter,
                          "Content-Length: " . strlen($post_data)

                        ),


                    ));


                    $response = json_decode(curl_exec($curl));
                    curl_close($curl);
                    $whatsappMsg="";
                    if ($response->status){
                        $whatsappMsg=' WhatsApp Message Status : '.$response->message;

                        $userInfo = $this->user_model->getUserInfoById($this->vendorId);
                        $date=date('Y-m-d H:i:s');

                        $messageInfo = array(
                            'client_name' => $userInfo->name,
                            'company' => $company,
                            'date' => $date,
                            'title' => $message,
                            'user_id' => $this->vendorId
                        );

                        $message_id = $this->user_model->newWhatsAppMessage($messageInfo);
                    }

            }
        }
    }


    function whatsAppMessagestestV2($id = NULL)
    {
        $tel_no="+967781240047";
        $sessionId="spark";
        $company="spark";

        $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient="".substr($tel_no, 1);
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="".substr($tel_no, 2);
                                    }else{
                                        $recipient="".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="966".substr($tel_no, 1);
                                    }else{
                                        $recipient="966".$tel_no;
                                    }
                                }
                            }

                            $message="مرحباً بكم في برنامج CPE!\\n\\nنشكر لكم اختياركم شركة أركان الاحتراف للاتصالات وتقنية المعلومات. \\nيسرنا أن نرحب بكم كعميل جديد لبرنامجنا الرائع.\\n\\nللدخول إلى حسابكم، يرجى استخدام المعلومات التالية:\\n\\nالرابط:\\nhttps://www.domain1.com\\nاسم الشركة: ".$company."  \\nاسم المستخدم: admin  \\nكلمة المرور: Admin123\\n\\nنتمنى لكم تجربة ممتعة ومثمرة مع برنامجنا. \\nإذا كان لديكم أي استفسارات أو تحتاجون إلى دعم، لا تترددوا في التواصل معنا.\\n\\nللدعم الفني، نرجو التواصل على الرقم التالي: 0555555555 أو الدخول على الرابط http://www.companypro.com\\n\\nشركة أركان الاحتراف للاتصالات وتقنية المعلومات\\n\\nWelcome to the CPE Program!\\n\\nThank you for choosing Arkan Al-Ihtiraf Communications and Information Technology. \\nWe are pleased to welcome you as a new customer to our fantastic program.\\n\\nTo access your account, please use the following information:\\n\\nLink:\\nhttps://www.domain1.com \\nCompany: ".$company."  \\nUsername: admin  \\nPassword: Admin123\\n\\nWe hope you have an enjoyable and productive experience with our program. \\nIf you have any questions or need support, please do not hesitate to contact us.\\n\\nFor technical support, please call 0555555555 or visit /www.companypro.com\\n \\nArkan Al-Ihtiraf Communications and Information Technology";


//echo $message;


                            //$sessionId=$this->settings['sessionId'];
                            $files = array();
                            $url = "https://iparkanwebsitforwhats/".$sessionId."/messages/send";

                            $curl = curl_init($url);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            $headers = array(
                               "Content-Type: application/json",
                               "accept: application/json",

                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                            $data = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            //print_r($response);

                            $whatsappMsg="";
                            if (isset($response->message) && $response->status=='PENDING'){


                                $whatsappMsg=' Recipient ('.$recipient.') Message ('.$message.')';

                        $userInfo = $this->user_model->getUserInfoById($this->vendorId);
                        $date=date('Y-m-d H:i:s');

                        $messageInfo = array(
                            'client_name' => $userInfo->name,
                            'company' => $company,
                            'date' => $date,
                            'title' => $message,
                            'user_id' => $this->vendorId
                        );

                        $message_id = $this->user_model->newWhatsAppMessage($messageInfo);
                            }

                        }
    }


    function addElectronicContract()
    {
        $client_id = $this->input->post('id');
        $company = $this->input->post('company');
        $contract_date = $this->input->post('contract_date') ? $this->input->post('contract_date') :  date('Y-m-d');
        $party2name = $this->input->post('party2name');
        $party2info = $this->input->post('party2info');
        $party2address = $this->input->post('party2address');
        $isAccounting = $this->input->post('isAccounting');
        $tax = $this->input->post('tax');
        $total = $this->input->post('total');
        $subtotal = $this->input->post('subtotal');
        $desc_1 = $this->input->post('desc_1');
        $amount_1 = $this->input->post('amount_1');
        $period_1 = $this->input->post('period_1');
        $desc_2 = $this->input->post('desc_2');
        $amount_2 = $this->input->post('amount_2');
        $period_2 = $this->input->post('period_2');
        $desc_3 = $this->input->post('desc_3');
        $amount_3 = $this->input->post('amount_3');
        $period_3 = $this->input->post('period_3');
        $desc_4 = $this->input->post('desc_4');
        $amount_4 = $this->input->post('amount_4');
        $period_4 = $this->input->post('period_4');
        $desc_5 = $this->input->post('desc_5');
        $amount_5 = $this->input->post('amount_5');
        $period_5 = $this->input->post('period_5');
        $desc_6 = $this->input->post('desc_6');
        $amount_6 = $this->input->post('amount_6');
        $period_6 = $this->input->post('period_6');


        $this->load->library('form_validation');


        $filename = 'Arkan_company_contract_for_'.$company.'.docx';
        $file = './upload/' . $filename;

        $name = $this->client_model->getClientInfo($client_id)->name;


        $timezone = 'Asia/Riyadh';
        date_default_timezone_set($timezone);

        $hijri = new HijriDateTime();


        // Extract day, month, and year
        $day_no = date('d', strtotime($contract_date));
        $month = date('m', strtotime($contract_date));
        $year = date('Y', strtotime($contract_date));

        $date_obj = new DateTime($contract_date);
        // Return the day name
        $day=$date_obj->format('l');


        if($day=='Sunday'){
            $day='الأحد';
        }else if($day=='Monday'){
            $day='الإثنين';
        }else if($day=='Tuesday'){
            $day='الثلاثاء';
        }else if($day=='Wednesday'){
            $day='الأربعاء';
        }else if($day=='Thursday'){
            $day='الخميس';
        }else if($day=='Friday'){
            $day='الجمعة';
        }else if($day=='Saturday'){
            $day='السبت';
        }
        if($isAccounting==1){
            $input        = './upload/عقد_شركة_اركان_محاسبي.docx';
        }else{
            $input        = './upload/عقد_شركة_اركان.docx';
        }

        $output       =$file;
    $replacements = [
        'placeholder1' => $day,
        'placeholder2' => $hijri->strToHijri($contract_date,"Y/m/j", 'ar'),
        'party2contractdate' => $year.'/'.$month.'/'.$day_no,
        'party2company' => $name,
        'party2name' => $party2name,
        'party2address' => $party2address,
        'party2info' => $party2info,
        'party2total' => $total,
        'party2tax' => $tax,
        'party2subtotal' => $subtotal,
        'party2amount_1' => $amount_1,
        'party2amount_2' => $amount_2,
        'party2amount_3' => $amount_3,
        'party2amount_4' => $amount_4,
        'party2amount_5' => $amount_5,
        'party2amount_6' => $amount_6,
        'party2desc_1' => $desc_1,
        'party2desc_2' => $desc_2,
        'party2desc_3' => $desc_3,
        'party2desc_4' => $desc_4,
        'party2desc_5' => $desc_5,
        'party2desc_6' => $desc_6,
        'party2period_1' => $period_1,
        'party2period_2' => $period_2,
        'party2period_3' => $period_3,
        'party2period_4' => $period_4,
        'party2period_5' => $period_5,
        'party2period_6' => $period_6,
    ];



    $successful = $this->searchReplaceWordDocument($input, $output, $replacements);

    //echo $successful ? "Successfully created $output" : 'Failed!';






        $clientElectronicContract = [
            'party2name' => $party2name,
            'created_by' => $this->vendorId,
            'company' => $company,
            'party2info' =>  $party2info,
            'isAccounting' =>$isAccounting,
            'party2address' => $party2address,
            'contract_date' => $contract_date,
            'total' => $total,
            'tax' => $tax,
            'subtotal' => $subtotal,
            'desc_1' => $desc_1,
            'amount_1' => $amount_1,
            'period_1' => $period_1,
            'desc_2' => $desc_2,
            'amount_2' => $amount_2,
            'period_2' => $period_2,
            'desc_3' => $desc_3,
            'amount_3' => $amount_3,
            'period_3' => $period_3,
            'desc_4' => $desc_4,
            'amount_4' => $amount_4,
            'period_4' => $period_4,
            'desc_5' => $desc_5,
            'amount_5' => $amount_5,
            'period_5' => $period_5,
            'desc_6' => $desc_6,
            'amount_6' => $amount_6,
            'period_6' => $period_6,
            'contract_attachment' => $file,
        ];



        if($this->client_model->getElectronicContract($company)){
            $result = $this->client_model->updateElectronicContract($company,$clientElectronicContract);
            $result=1;
        }else{
            $result = $this->client_model->createElectronicContract($clientElectronicContract);
        }

        if ($result == true) {
            $process = 'Client Add Electronic Contract for company '.$company;
            $processFunction = 'Admin/addElectronicContract';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Client successfully added electronic contract');
        } else {
            $this->session->set_flashdata('error', 'Client update failed');
        }

        redirect('editOldClient/' . $client_id);


    }

    function searchReplaceWordDocument(string $input, string $output, array $replacements): bool
    {
        if (copy($input, $output)) {

            // Create the Object.
            $zip = new ZipArchive();

            // Open the Microsoft Word .docx file as if it were a zip file... because it is.
            if ($zip->open($output, ZipArchive::CREATE) !== true) {
                return false;
            }

            // Fetch the document.xml file from the word subdirectory in the archive.
            $xml = $zip->getFromName('word/document.xml');

            // Replace
            $xml = str_replace(array_keys($replacements), array_values($replacements), $xml);

            // Write back to the document and close the object
            if (false === $zip->addFromString('word/document.xml', $xml)) {
                return false;
            }
            $zip->close();

            return true;
        }

        return false;
    }

    function sendWhatsAppDodument($company,$client_id)
    {
        if(!$company){
            $this->session->set_flashdata('error', 'Please select client');
            redirect('clientListing');
        }

        if(!$client_id){
            $this->session->set_flashdata('error', 'Please select client');
            redirect('clientListing');
        }

        $clientInfo= $this->client_model->getClientInfo($client_id);
        $tel_no=$clientInfo->tel_no;


        $clientElectronicContract=$this->client_model->getElectronicContract($company);

        $contract_attachment=$clientElectronicContract->contract_attachment;
        $filename=basename($contract_attachment);
        $binary_data=file_get_contents($contract_attachment);



        if($tel_no){
            $len = strlen($tel_no);
            if($len>8 && is_numeric($tel_no)){
                if(substr($tel_no, 0, 1) === "+"){
                    $recipient=$tel_no;
                }else{
                    if($len>10){
                        if(substr($tel_no, 0, 2) === "00"){
                            $recipient="+".substr($tel_no, 2);
                        }else{
                            $recipient="+".$tel_no;
                        }
                    }else{
                        if(substr($tel_no, 0, 1) === "0"){
                            $recipient="+966".substr($tel_no, 1);
                        }else{
                            $recipient="+966".$tel_no;
                        }
                    }
                }
                    $message="عقد شركة اركان";
                    $fields = array("phones"=>"966535295331", "account"=>"0", "doctype"=>"document", "recipient"=>$recipient, "message"=>$message, "token"=>"721790345");
                    $files = array();
                    $url = "http://api.4whatsapp.com/api/Agent_Client_";
                    $curl = curl_init();
                    $url_data = http_build_query($data);
                    $boundary = 'b77a5c561934e089';
                    $delimiter = '-------------' . $boundary;
                    $post_data = '';
                    $eol = "\r\n";

                    $delimiter = '-------------' . $boundary;

                    foreach ($fields as $name => $content) {
                        $post_data .= "--" . $delimiter . $eol
                        . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                        . $content . $eol;
                    }

                    $post_data .= "--" . $delimiter . $eol
                    . 'Content-Disposition: form-data; name="document_file"; filename="' . $filename . '"' . $eol
                    //. 'Content-Type: image/png'.$eol
                    . 'Content-Transfer-Encoding: binary'.$eol;

                    $post_data .= $eol;
                    $post_data .= $binary_data . $eol;

                    $post_data .= "--" . $delimiter . "--".$eol;
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POST => 1,
                        CURLOPT_POSTFIELDS => $post_data,
                        CURLOPT_HTTPHEADER => array(
                          //"Authorization: Bearer $TOKEN",
                          "Content-Type: multipart/form-data; boundary=" . $delimiter,
                          "Content-Length: " . strlen($post_data)

                        ),


                    ));



                    $resp = curl_exec($curl);
                    $response = json_decode($resp);
                    curl_close($curl);


                    $whatsappMsg="";
                    if (isset($response->data) && !empty(json_decode($response->data)) && json_decode($response->data)->status=='200'){
                        $whatsappMsg=' WhatsApp Message Status : '.$response->message;

                        $userInfo = $this->user_model->getUserInfoById($this->vendorId);
                        $date=date('Y-m-d H:i:s');

                        $messageInfo = array(
                            'client_name' => $userInfo->name,
                            'company' => $company,
                            'date' => $date,
                            'title' => $message,
                            'user_id' => $this->vendorId
                        );

                        $message_id = $this->user_model->newWhatsAppMessage($messageInfo);

                        $this->session->set_flashdata('success', $whatsappMsg);
                        redirect('editOldClient/' . $client_id);
                    }else{
                        $whatsappMsg=' WhatsApp Message Status : '.$response->message;
                        $this->session->set_flashdata('error', $whatsappMsg);
                        redirect('editOldClient/' . $client_id);
                    }



            }else{
                $this->session->set_flashdata('error', 'Mobile not number');
                redirect('editOldClient/' . $client_id);
            }
        }else{
            $this->session->set_flashdata('error', 'Mobile is requier');
            redirect('editOldClient/' . $client_id);
        }
    }


    function addWhatsAppSettings()
    {
        $client_id = $this->input->post('id');
        $company_type = $this->input->post('company_type');
        $gateway = $this->input->post('gateway');
        $whatsapp_user4 = $this->input->post('4whatsapp_user');
        $whatsapp_password4 = $this->input->post('4whatsapp_password');
        $whatsapp_code4 = $this->input->post('4whatsapp_code');
        $whatsapp_phone4 = $this->input->post('4whatsapp_phone');





        $this->load->library('form_validation');


        $data['clientInfo'] = $this->client_model->getClientInfo($client_id);



            $company=$data['clientInfo']->company;

            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
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
            $data1 = $CI->load->database($config, TRUE);
			$this->mynewdb =$data1;

            $add_whatsapp_settings=1;

            if($this->input->post('gateway')=="4whatsapp"){
            $data2 = [
                'gateway'    =>$this->input->post('gateway'),
                'Custom'     => $Custom,
                '4whatsapp' => [
                    'user'     => $this->input->post('4whatsapp_user'),
                    'password' => $this->input->post('4whatsapp_password'),
                    'code'      => $this->input->post('4whatsapp_code'),
                    'phone'     => $this->input->post('4whatsapp_phone'),
                ],
                'arkanprowhatsapp' => [
                    'sessionId'     => '',
                ],
            ];
        }else if($this->input->post('gateway')=="arkanprowhatsapp"){
            $data2 = [
                'gateway'    =>$this->input->post('gateway'),
                'Custom'     => $Custom,
                '4whatsapp' => [
                    'user'     => '',
                    'password' => '',
                    'code'      => '',
                    'phone'     => '',
                ],
                'arkanprowhatsapp' => [
                    'sessionId'     => $company,
                ],
            ];

            if($this->mynewdb->query("ALTER TABLE `sma_settings` ADD `isArkanproWhatsapp` TINYINT(1) NOT NULL DEFAULT '1'; ")){

            }
        }else{

        $add_whatsapp_settings=2;

$url = "https://iparkanwebsitforwhats/sessions/".$company;

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

 $resp = json_decode(curl_exec($curl));

curl_close($curl);
        if($resp->message){

            $data2 = null;
            if($this->mynewdb->query("ALTER TABLE `sma_settings` DROP `isArkanproWhatsapp`; ")){

            }
        }
        }

            $whatsapp_config = [
                'auto_send' => $this->input->post('auto_send'),
                'config'    => json_encode($data2),
            ];




            $this->mynewdb->query("update sma_whatsapp_settings set config='".json_encode($data2)."' where id=1; ");


        $clientTransaction = [
            'client_id' => $client_id,
            'created_by' => $this->vendorId,
            'company_type' => $company_type,
            'paid_at' =>  date('Y-m-d H:i:s'),
            'add_whatsapp_settings' =>$add_whatsapp_settings,

            'comments' => $gateway,
            'renewed_at' => date('Y-m-d H:i:s'),
        ];



        $result = $this->client_model->createTransaction($clientTransaction);
        if ($result == true) {
            $process = 'Client Add WhatsApp Settings '.$gateway.' for company '.$company;
            $processFunction = 'Admin/addWhatsAppSettings';
            $this->logrecord($process, $processFunction);



        $this->session->set_flashdata('success', 'Client successfully updated');
        } else {
            $this->session->set_flashdata('error', 'Client update failed');
        }

        redirect('editOldClient/' . $client_id);


    }
    
    function clientListingAppVersion($id = NULL)
    {
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        if (!$id) {
            $this->session->set_flashdata('error', 'Please select client');

            redirect('clientListing');
        }

        $this->load->library('pagination');
        $clientInfo = $this->client_model->getClientInfo($id);
        $company = '';
        if (!empty($clientInfo)) {
            $company = $clientInfo->company;
        } else {
            $this->session->set_flashdata('error', 'Not found any App Version');
            redirect('clientListing');
        }



        $data['id'] = $id;
        $data['company'] = $company;



                    $config['dsn'] = '';
                    $config['hostname'] = 'localhost';
                    $config['username'] = 'cpesoft';
                    $config['password'] = 'enter-password';
                    $config['database'] = 'cpesoft_'.$company;
                    /*$config['username'] = 'root';
                    $config['password'] = '';
                    $config['database'] = $company;*/
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

                    $data['app_versions'] = $this->mynewdb->query("SELECT DISTINCT w.code,s.app_name,max(s.app_version) as app_version FROM `sma_sales` s, `sma_warehouses` w WHERE w.id=s.warehouse_id and s.app_version is not null group by s.warehouse_id,s.app_name order by s.id;")->result_array();



        $process = 'App Version List for Client : ' . $company;
        $processFunction = 'Admin/clientListingAppVersion';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'App Version List for Client : ' . $company;

        $this->loadViews("appVersionListing", $this->global, $data, NULL);





    }




}
