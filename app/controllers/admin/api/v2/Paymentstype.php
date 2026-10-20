<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Paymentstype extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Payment_type_api');
        $this->load->api_model('Gl_payment_methods_api');
        $this->load->admin_model('accounts_model');


    }

    protected function setObject($obj)
    {
         $obj = (array) $obj;
        ksort($obj);
        return $obj;
    }

    public function index_get()
    {




        $code = $this->get('code');

        $filters = [
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : '',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : ($this->Payment_type_api->countPayment_type() + $this->Gl_payment_methods_api->countPayment_type()),
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],
        ];
        $data = [];
        if ($code === null) {
            if ($warehouses = $this->Payment_type_api->getPayment_type($filters)) {
                $payment_type = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Payment_type_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $payment_type[] = $this->setObject($warehouse);
                }
				$data['payment_type']=$payment_type;
			}

			if ($warehouses = $this->Gl_payment_methods_api->getPayment_type($filters)) {
                $payment_type = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $payment_type[] = $this->setObject($warehouse);
                }
				$data['gl_payment_methods']=$payment_type;
			}
            $data['message']="Success";

                $this->response($data, REST_Controller::HTTP_OK);

        }
    }
}
