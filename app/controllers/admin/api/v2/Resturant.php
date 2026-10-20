<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Resturant extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Resturant_emp_types_api');
        $this->load->api_model('products_api');
        $this->load->api_model('Resturant_emp_api');
        $this->load->api_model('Dept_api');
        $this->load->api_model('Resturant_tables_api');

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
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 1,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];
        $data = [];
        if ($code === null) {
            if ($Resturant_emp_types = $this->Resturant_emp_types_api->getResturant_emp_types($filters_count)) {
                $Resturant_emp_types_data = [];
                foreach ($Resturant_emp_types as $product) {
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
					//$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));


                    $Resturant_emp_types_data[] = $this->setObject($product);
                }
				$data['resturant_emp_types']=$Resturant_emp_types_data;
			}




			if ($Resturant_emp = $this->Resturant_emp_api->getResturant_emp($filters_count)) {
                $Resturant_emp_data = [];
                foreach ($Resturant_emp as $product) {
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
					//$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));


                    $Resturant_emp_data[] = $this->setObject($product);
                }
				$data['resturant_emp']=$Resturant_emp_data;
			}



			if ($Dept = $this->Dept_api->getDept($filters_count)) {
                $Dept_data = [];
                foreach ($Dept as $product) {
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
					//$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));


                    $Dept_data[] = $this->setObject($product);
                }
				$data['dept']=$Dept_data;
			}


			/*
			if ($Order_type = $this->Order_type_api->getOrder_type($filters_count)) {
                $Order_type_data = [];
                foreach ($Order_type as $product) {
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
					//$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));


                    $Order_type_data[] = $this->setObject($product);
                }
				$data['order_type']=$Order_type_data;
			}




			*/


			if ($Resturant_tables = $this->Resturant_tables_api->getResturant_tables($filters_count)) {
                $Resturant_tables_data = [];
                foreach ($Resturant_tables as $product) {
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
					//$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));


                    $Resturant_tables_data[] = $this->setObject($product);
                }
				$data['resturant_tables']=$Resturant_tables_data;
			}
            $data['message']="Success";

                $this->response($data, REST_Controller::HTTP_OK);

        }
    }
}
