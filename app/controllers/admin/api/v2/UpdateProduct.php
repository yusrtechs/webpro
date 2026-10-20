<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class UpdateProduct extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->admin_model('pos2_model');




    }






    public function index_post()
    {

		//$sales = urldecode($this->post('sales'));
		$json = file_get_contents('php://input');
		$product =json_decode($json);
		//$this->sma->print_arrays($product);
		//$sales=$sales->JSON3;
		$id="";
		if($product->code && $product->code!=null){
			if($product_details = $this->pos2_model->getProductByCode($product->code)){
                $id=$product_details->id;
            }else{
                $this->set_response([
                    'message' => 'Product could not be found for code ' . $product->code . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
		}else{
		$this->set_response([
                    'message' => 'code is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		$name="";
		if($product->name && $product->name!=null){
		}else{
		$this->set_response([
                    'message' => 'name is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}


		    $data           = [
				'name'       => trim($product->name),
            ];


			if($product->category_id && $product->category_id!=null){
				$data['category_id'] = $product->category_id;
			}
			if($product->unit && $product->unit!=null){
				$data['unit'] = $product->unit;
			}
			if($product->price && $product->price!=null){
				$data['price'] = $product->price;
			}
			if($product->tax_rate && $product->tax_rate!=null){
				$data['tax_rate'] = $product->tax_rate;
			}
			if($product->tax_method && $product->tax_method!=null){
				$data['tax_method'] = $product->tax_method;
			}
			if($product->item_no && $product->item_no!=null){
				$data['item_no'] = $product->item_no;
			}




				if($this->pos2_model->updateProduct($id, $data)){
					$this->set_response([
                    'message' => 'Success Update',
                    'status'  => true,
                ], REST_Controller::HTTP_OK);
            }else{

                $this->set_response([
                    'message' => 'Failure',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);

            }






    }
}
