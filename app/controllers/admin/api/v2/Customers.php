<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Customers extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Company_api');
        $this->load->api_model('Companies_api');
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

        $filters_count = [
            'group'    => $this->get('group') ? $this->get('group') : '',
			'company'    => $this->get('company') ? $this->get('company') : '',
			'id'    => $this->get('online_id') ? $this->get('online_id') : null,
			'Offline_id'    => $this->get('offline_id') ? $this->get('offline_id') : null,
			'name'    => $this->get('name') ? $this->get('name') : null,
			'vat_no'    => $this->get('vat_no') ? $this->get('vat_no') : null,
			'cr_no'    => $this->get('cr_no') ? $this->get('cr_no') : null,
			'phone'    => $this->get('phone') ? $this->get('phone') : null,
			'groupc'    => 'customer',
            'short_fields'    => $this->get('short_fields') ? $this->get('short_fields') : 0,

        ];
		$filters = [
            'groupc'    => 'customer',
            'name'     => $name,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : '',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $this->Companies_api->countCompaniesTailor($filters_count),
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['company', 'acs'],
        ];

        if ($code === null) {
            if ($companies = $this->Companies_api->getCompaniesTailor($filters_count)) {
                $pr_data = [];
                foreach ($companies as $company) {

                    $pr_data[] = $this->setCompany($company);
                }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $filters['limit'],
                    'start' => $filters['start'],
                    'total' => $this->Companies_api->countCompaniesTailor($filters_count),
                    'message' => "Received ".$this->Companies_api->countCompaniesTailor($filters_count)." Customers",

                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No customer were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }
}
