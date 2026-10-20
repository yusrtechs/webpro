<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Users extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Users_api');
        $this->load->api_model('warehouses_api');
    }

    protected function setUser($user)
    {
        $user = (array) $user;
        ksort($user);
        return $user;
    }

    public function index_get()
    {




        $code = $this->get('code');

        $filters = [
            'code'     => $code,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : 'customer',
            'id'    => $this->get('user_id') ? $this->get('user_id') : '',
            'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $this->Users_api->countUsers(),
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($filters['warehouse_id']) {
            //if ($filters['id']) {


        if ($code === null) {



            if ($users = $this->Users_api->getUsers($filters)) {
                $pr_data = [];
                foreach ($users as $user) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $user->price_group = $this->Users_api->getPriceGroupByID($user->price_group_id);
                            }
                        }
                    }
                    $pr_data[] = $this->setUser($user);
                }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $this->Users_api->countUsers($filters),
                    'start' => $filters['start'],
                    'total' => $this->Users_api->countUsers($filters),
                    'message' => "Success",
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No user were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }
        }

    /*} else {
        $this->response([
            'message' => 'user_id is required',
            'status'  => false,
        ], REST_Controller::HTTP_BAD_REQUEST);
    }*/
} else {
    $this->response([
        'message' => 'Warehouse does not exist ',
        'status'  => false,
    ], REST_Controller::HTTP_BAD_REQUEST);
}
    }
}
