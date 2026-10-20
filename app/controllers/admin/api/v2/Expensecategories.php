<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Expensecategories extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Expense_categories_api');
    }

    protected function setWarehouse($warehouse)
    {
        unset($warehouse->price_group_id);
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
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $this->Expense_categories_api->countExpense_categories(),
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($code === null) {
            if ($warehouses = $this->Expense_categories_api->getExpense_categories($filters)) {
                $expense_categories = [];
                foreach ($warehouses as $warehouse) {

                    $expense_categories[] = $this->setWarehouse($warehouse);
                }

                $data = [
                    'data'  => $expense_categories,
                    'limit' => $filters['limit'],
                    'start' => $filters['start'],
                    'total' => $this->Expense_categories_api->countExpense_categories($filters),
                    'message' => "Received ".($this->Expense_categories_api->countExpense_categories($filters))." Expense categories",
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No Expense categories were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }
        }
    }
}
