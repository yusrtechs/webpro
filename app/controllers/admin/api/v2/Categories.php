<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Categories extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Categories_api');
        $this->load->api_model('warehouses_api');
    }

    protected function setWarehouse($warehouse)
    {
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }

    public function index_get()
    {




        $code = $this->get('code');

        $filters = [
            'code'     => $code,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : 'customer',
            'cat_type'    => $this->get('cat_type') ? $this->get('cat_type') : '',
			'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $this->Categories_api->countCategories(),
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($filters['warehouse_id']) {
            if ($warehouses = $this->Categories_api->getCategories($filters)) {
                $pr_data = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Categories_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $pr_data[] = $this->setWarehouse($warehouse);
                }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $this->Categories_api->countCategories($filters),
                    'start' => $filters['start'],
                    'total' => $this->Categories_api->countCategories($filters),
                    'message' => "Received ".($this->Categories_api->countCategories($filters))." categories",
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No categories were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }
        } else {

                $this->set_response([
                    'message' => 'Warehouse could not be found ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);

        }
    }
}
