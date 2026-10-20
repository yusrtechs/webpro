<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Wproducts extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Wproducts_api');
		$this->load->admin_model('products_model');
        
    }

    protected function setProduct($product)
    {
        //$product->tax_rate       = $this->products_api->getTaxRateByID($product->tax_rate);
        //$product->unit           = $this->products_api->getProductUnit($product->unit);
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
        $code = $this->get('code');

        $filters = [
            'code'     => $code,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $this->Wproducts_api->getWproductsCount($this->get('warehouse_id') ? $this->get('warehouse_id') : null)->count,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
            'brand'    => $this->get('brand_code') ? $this->get('brand_code') : null,
            'category' => $this->get('category_code') ? $this->get('category_code') : null,
            'warehouse_id' => $this->get('warehouse_id') ? $this->get('warehouse_id') : null,
            'product_id' => $this->get('product_code') ? $this->products_model->getProductByCode($this->get('product_code'))->id : null,
        ];

        if ($code === null) {
            if ($products = $this->Wproducts_api->getWproducts($filters)) {
                $pr_data = [];
                foreach ($products as $product) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
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
					$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));

                    $pr_data[] = $this->setProduct($product);
                }
				
				//$pr_data      = json_decode('{}');
				//$this->products_model->add_products_temp($pr_data);
				
				

                $data = [
                    'data'  => $pr_data,
                    'limit' => $filters['limit'],
                    'start' => $filters['start'],
                    'total' => $this->Wproducts_api->countWproducts($filters),
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No product were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            if ($product = $this->Wproducts_api->getProduct($filters)) {
                if (!empty($filters['include'])) {
                    foreach ($filters['include'] as $include) {
                        if ($include == 'brand') {
                            $product->brand = $this->Wproducts_api->getBrandByID($product->brand);
                        } elseif ($include == 'category') {
                            $product->category = $this->Wproducts_api->getCategoryByID($product->category);
                        } elseif ($include == 'photos') {
                            $product->photos = $this->Wproducts_api->getProductPhotos($product->id);
                        } elseif ($include == 'sub_units') {
                            $product->sub_units = $this->Wproducts_api->getSubUnits($product->unit);
                        }
                    }
                }

                $product = $this->setProduct($product);
				
				
                $this->set_response($product, REST_Controller::HTTP_OK);
            } else {
                $this->set_response([
                    'message' => 'Product could not be found for code ' . $code . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
}
