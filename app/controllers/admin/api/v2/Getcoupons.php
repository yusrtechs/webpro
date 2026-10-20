<?php
//ini_set("display_errors", 1);
//ini_set("pcre.backtrack_limit", "100000000");
//ini_set("pcre.recursion_limit", "100000000");

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Getcoupons extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Coupons_api');
        $this->load->admin_model('products_model');




    }

    protected function setWarehouse($warehouse)
    {
        //unset($warehouse->price_group_id);
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }

    public function index_get()
    {

		//if($this->db->database=="cpesoft_myfood" && !$this->get('user_id'))
		//ini_set('memory_limit', '-1');
		//$this->sma->print_arrays(memory_get_usage());
		//$this->sma->print_arrays($this->db->database);


		//'group'    => $this->get('group') ? $this->get('group') : '',


        $code = $this->get('code');



        $count_row=0;

		$filters = [
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'card_no'    => $this->get('card_no') ? $this->get('card_no') : '',
			'group'    => $this->get('group') ? $this->get('group') : '',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $count_row,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],
        ];
		$count_row=$this->Coupons_api->countCoupons($filters);



        if ($code === null) {

			$data = [];



            if ($count_row>0) {

			if ($warehouses = $this->Coupons_api->getCoupons($filters)) {
                $coupons = [];

                foreach ($warehouses as $warehouse) {
                    $coupon_codes_trim = [];
                    $products_code = [];

                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Taxrates_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $coupon_codes = explode("\n", $warehouse->coupon_code);
                    foreach($coupon_codes as $coupon_code){
                        $coupon_codes_trim[]=trim($coupon_code);
                    }
                    $warehouse->coupon_code=$coupon_codes_trim;
                    $warehouse->product_categories = explode("\n", $warehouse->product_categories);

                    $countCat=0;
                    foreach($warehouse->product_categories as $product){

                        if($this->products_model->getCategoryProducts($product))
                        $countCat++;
                    }
                    if($countCat>0){
                    }else{
                        $warehouse->product_categories = [];
                    }

                    $products = explode("\n", $warehouse->products);
                    $count=0;
                    foreach($products as $product){
                        $code=$this->products_model->getProductByID($product)->code;
                        $products_code[]=$code;
                        if($code)
                        $count++;
                    }
                    if($count>0){
                    $warehouse->products_code = $products_code;
                    }else{
                        $warehouse->products_code = [];
                    }
                    unset($warehouse->products);
                    $coupons[] = $this->setWarehouse($warehouse);
                }


				$data['coupons']=$coupons;
			}




			/*
			if ($Order_type = $this->Order_type_api->getOrder_type($filters)) {
                $Order_type_data = [];
                foreach ($Order_type as $product) {
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
					//$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));


                    $Order_type_data[] = $this->setWarehouse($product);
                }
				$data['order_type']=$Order_type_data;
			}




			*/








                    $data['limit'] = $filters['limit'];
                    $data['start'] = $filters['start'];
                    $data['total'] = $this->Coupons_api->countCoupons($filters);
                    $data['message'] = "Received ".($this->Coupons_api->countCoupons($filters))." Coupons";


                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No Giftcard were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }
        }
    }
}
