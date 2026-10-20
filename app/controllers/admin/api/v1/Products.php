<?php
//ini_set("display_errors", 1);
//ini_set("pcre.backtrack_limit", "100000000");
//ini_set("pcre.recursion_limit", "100000000");

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Products extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Users_api');
        $this->load->api_model('warehouses_api');
		$this->load->api_model('products_api');





    }

    protected function setProducts($warehouse)
    {
        //unset($warehouse->price_group_id);
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }

    public function index_get()
    {

		if(!$this->get('user_id')){
		//ini_set('memory_limit','2048M');
		}
		//$this->sma->print_arrays($this->db->database);


		//'group'    => $this->get('group') ? $this->get('group') : '',


        $code = $this->get('code');

		$filters_count = [
            'groupc'    => 'customer',
            'groupb'    => 'biller',
            'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'biller_id'    => $this->get('user_id') ? $this->Users_api->getBillerIdByUserId($this->get('user_id')) : '',
			'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],

        ];


        $count_row=0;
		$count_row=($this->products_api->getProductsCount($filters_count)->count);

		$filters = [
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : '',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $count_row,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],
        ];


        //$this->sma->print_arrays($filters_count['warehouse_id']);

        if ($code === null) {

		/*	$dbname=str_replace("cpesoft_","",$this->db->database);
		if($dbname=="mjahli" && empty($this->get('user_id'))){
			ini_set('max_execution_time', 300);
			//$this->sma->print_arrays($dbname);
		ini_set('memory_limit','2048M');
		}*/

			$data = [];



            if ($filters_count['warehouse_id']) {



                $pr_data = [];
			if ($products = $this->products_api->getProducts($filters_count)) {

                foreach ($products as $product) {
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

                    $pr_data[] = $this->setProducts($product);
                }


			}

            $data['products']=$pr_data;












                    $data['limit'] = $filters['limit'];
                    $data['start'] = $filters['start'];
                    $data['total'] = ($this->products_api->getProductsCount($filters_count)->count);
                    $data['message'] = "Received ".($this->products_api->getProductsCount($filters_count)->count)." Products";


                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'Warehouse does not exist ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {

                $this->set_response([
                    'message' => 'No data were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);

        }
    }
}
