<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Chest_Pocket_Padding extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Tailer_api');
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
            'code'     => $code,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : 'customer',
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($code === null) {
            if ($warehouses = $this->Tailer_api->getTR_Chest_Pocket_Padding($filters)) {
                $TR_Chest_Pocket_Padding = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Chest_Pocket_Padding[] = $this->setObject($warehouse);
                }
                $data = [
                    'data'  => $TR_Chest_Pocket_Padding,
                    'message' => "Success",
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No Chest Pocket Padding were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }
        }
    }
}
