<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Samet Aydın / sametay153@gmail.com
 * @version : 1.0
 * @since : 27.02.2018
 */
class Reports extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('client_model');
        $this->load->model('api_model');
        $this->isLoggedIn();
    }

    public function clients()
    {
        $process = 'Reports';
        $processFunction = 'Clients/report';

        $data['userRecords'] = [];
        if (!empty($_POST))
            $data['userRecords'] = $this->client_model->getClientsWithFilters($_POST);

        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Clients Report';

        $this->loadViews("clients_reports", $this->global, $data, NULL);
    }

    public function sales()
    {
        $process = 'Reports';
        $processFunction = 'sales/report';

        $data['userRecords'] = [];
        if (!empty($_POST))
            $data['userRecords'] = $this->client_model->getSalesWithFilters($_POST);

        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Sales Report';

        $this->loadViews("sales_reports", $this->global, $data, NULL);
    }
}
