<?php
//ini_set("display_errors", 1);
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Modifiers extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('warehouses_api');
        $this->load->api_model('Modifiers_api');
        $this->load->api_model('Product_modifiers_api');
        $this->load->api_model('products_api');
        $this->load->admin_model('products_model');

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

        $filters_count = [
            'groupc'    => 'customer',
            'groupb'    => 'biller',
            'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],

        ];
        $data = [];
        if ($filters_count['warehouse_id']) {
        if ($warehouses = $this->Modifiers_api->getModifiers($filters_count)) {
            $modifiers = [];
            foreach ($warehouses as $warehouse) {
                if (!empty($filters_count['include'])) {
                    foreach ($filters_count['include'] as $include) {
                        if ($include == 'price_group') {
                            //$warehouse->price_group = $this->Units_api->getPriceGroupByID($warehouse->price_group_id);
                        }
                    }
                }
                $modifiers[] = $this->setObject($warehouse);
            }
            $data['modifiers']=$modifiers;
        }
        if ($product_modifiers = $this->Product_modifiers_api->getcategory_modifiersV3($filters_count)) {
            $product_modifiers_data = [];
            foreach ($product_modifiers as $product) {
                if (!empty($filters_count['include'])) {
                    foreach ($filters_count['include'] as $include) {
                        if ($include == 'brand') {
                            $product->brand = $this->products_api->getBrandByID($product->brand);
                        } elseif ($include == 'category') {
                            $product->category = $this->products_api->getCategoryByID($product->category);
                        } elseif ($include == 'photos') {
                            $product->photos = $this->products_api->getProductPhotos($product->id);
                        } elseif ($include == 'sub_units') {
                            $product->sub_units = $this->products_api->getSubUnits($product->unit);
                        }
                    }

                }
                $product->category_code=$this->products_model->getCategoryByID($product->category_id)->code;
                //$this->sma->print_arrays($this->getProductByID($product->id));


                $product_modifiers_data[] = $this->setObject($product);
            }
            $data['category_modifiers']=$product_modifiers_data;
        }
            $data['message']="Success";

                $this->response($data, REST_Controller::HTTP_OK);

        } else {

            $this->set_response([
                'message' => 'Warehouse could not be found ',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);

        }
    }
}
