<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Promos extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Promo_api');
        $this->load->api_model('warehouses_api');
        $this->load->admin_model('products_model');

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
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : 'customer',
            'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 1,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($filters['warehouse_id']) {
            if ($warehouses = $this->Promo_api->getPromo($filters)) {
                $promo = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Units_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					$warehouse->offer_product_code=$this->products_model->getProductByID($warehouse->offer_product)->code;
					if($warehouse->free_product)
					$warehouse->free_product_code=$this->products_model->getProductByID($warehouse->free_product)->code;
				    else
					$warehouse->free_product_code='';

                    if($warehouse->offer_product_details==1){
					$warehouse->promo_details=$this->Promo_api->getPromoDetailsBypromo_id($warehouse->id);
                    foreach ($warehouse->promo_details as $promo_detail) {
                    $promo_detail->offer_product_code=$this->products_model->getProductByID($promo_detail->offer_product)->code;
                    }
                    }

                    $promo[] = $this->setWarehouse($warehouse);
                }



                $data = [
                    'data'  => $promo,
                    'limit' => $this->Promo_api->countPromo($filters),
                    'start' => $filters['start'],
                    'total' => $this->Promo_api->countPromo($filters),
                    'message' => "Received ".($this->Promo_api->countPromo($filters))." Promos",
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No Promos were found.',
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
