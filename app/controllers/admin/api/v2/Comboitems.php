<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Comboitems extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Comboitems_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('Users_api');
		$this->load->admin_model('products_model');

    }

    protected function setProduct($product)
    {
        //$product->tax_rate       = $this->Comboitems_api->getTaxRateByID($product->tax_rate);
        //$product->unit           = $this->Comboitems_api->getProductUnit($product->unit);
        /*$ctax                    = $this->site->calculateTax($product, $product->tax_rate);
        $product->price          = $this->sma->formatDecimal($product->price);
        $product->net_price      = $this->sma->formatDecimal($product->tax_method ? $product->price : $product->price - $ctax['amount']);
        $product->unit_price     = $this->sma->formatDecimal($product->tax_method ? $product->price + $ctax['amount'] : $product->price);
        $product->tax_method     = $product->tax_method ? 'exclusive' : 'inclusive';
        $product->tax_rate->type = $product->tax_rate->type ? 'percentage' : 'fixed';*/
        $product                 = (array) $product;
        ksort($product);
        return $product;
    }

    public function index_get()
    {
        //$code = $this->get('code');

        $filters_count = [
            'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'pid'    => $this->get('product_code') ? $this->products_model->getProductByCode($this->get('product_code'))->id : null,
			'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],


        ];
        //$this->sma->print_arrays($filters_count);

        $filters = [
            'code'     => $code,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $this->Comboitems_api->getProductsCount($this->get('warehouse_id') ? $this->get('warehouse_id') : null)->count,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
            'brand'    => $this->get('brand_code') ? $this->get('brand_code') : null,
            'category' => $this->get('category_code') ? $this->get('category_code') : null,
            'warehouse_id' => $this->get('warehouse_id') ? $this->get('warehouse_id') : null,
        ];

        if ($code === null) {

            if ($filters_count['warehouse_id']) {
                if (($filters_count['pid'] && $this->get('product_code') ) || !$this->get('product_code')) {

            if ($products = $this->Comboitems_api->getProducts($filters_count)) {
                $pr_data = [];
                foreach ($products as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'brand') {
                                $product->brand = $this->Comboitems_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Comboitems_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Comboitems_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Comboitems_api->getSubUnits($product->unit);
                            }
                        }
                    }

                    $pr_data[] = $this->setProduct($product);
                }

				//$pr_data      = json_decode('{}');
				//$this->products_model->add_products_temp($pr_data);
				//$this->sma->print_arrays($this->Comboitems_api->getProductsCount()->count);


                $data = [
                    'data'  => $pr_data,
                    'limit' => $this->Comboitems_api->getProductsCount($filters_count)->count,
                    'start' => $filters['start'],
                    'total' => $this->Comboitems_api->getProductsCount($filters_count)->count,
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No combo items were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                'message' => 'Product does not exist ',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        } else {
            $this->response([
                'message' => 'Warehouse does not exist ',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        }
    }
}
