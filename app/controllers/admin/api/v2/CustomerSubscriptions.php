<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class CustomerSubscriptions extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Company_api');
        $this->load->api_model('Companies_api');
        $this->load->admin_model('pos2_model');
    }

    protected function setCompany($company)
    {
        $company = (array) $company;
        ksort($company);
        return $company;
    }

    public function index_get()
    {
        $code = $this->get('code');

        if ($code === null) {

            $customer_id = $this->get('customer_id') ? $this->get('customer_id') : null;
            $customer = $this->get('customer') ? $this->get('customer') : null;


            if (isset($customer_id) && $customer_id!==null) {
			}else{
			    $this->set_response([
                    'message' => 'customer_id is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		    }

            if (isset($customer) && $customer!==null) {
			}else{
		        $this->set_response([
                    'message' => 'customer is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		    }

		    if (isset($customer_id) && $customer_id!==null && isset($customer) && $customer!==null) {

            $company_id=  $this->pos2_model->getCompanyByOfflineIDV2($customer_id,$customer)->id ? $this->pos2_model->getCompanyByOfflineIDV2($customer_id,$customer)->id : ($this->pos2_model->getCompanyByidIDV2($customer_id,$customer)->id ? $this->pos2_model->getCompanyByidIDV2($customer_id,$customer)->id : ($this->pos2_model->getCompanyByOfflineID($customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($customer_id)->id : $customer_id));


            if($company_id){
            $pr_data = [];
            if ($companies = $this->Companies_api->getCustomerSubscriptions($company_id)) {

                foreach ($companies as $company) {

                    $pr_data[] = $this->setCompany($company);
                }
            }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $this->Companies_api->getCustomerSubscriptionsCount($company_id),
                    'start' => 1,
                    'total' => $this->Companies_api->getCustomerSubscriptionsCount($company_id),
                    'message' => "Received ".$this->Companies_api->getCustomerSubscriptionsCount($company_id)." Subscriptions",

                ];
                $this->response($data, REST_Controller::HTTP_OK);

            }else{
			$this->set_response([
                    'message' => 'Customer could not be found',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);

		}
        }
        }
    }
}
