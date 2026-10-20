<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class UpdatePrice extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('users_api');

		$this->load->admin_model('products_model');



    }

	protected function setArr($warehouse)
    {
        unset($warehouse->price_group_id);
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }




    public function index_post()
    {

		//$sales = urldecode($this->post('sales'));
		$json = file_get_contents('php://input');
		$sales =json_decode($json);
		//$sales=$sales->JSON3;
		//$this->sma->print_arrays($sales);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        $products=$sales->product;
        if ($products) {

            $failed_sales = [];
            $flageTrue=0;
            $flageFalse=0;

            foreach ($products as $product) {

                if(empty(trim($product->code))){
                    $products_sync1           = [
                        'code'            => trim($product->code),
                        'price'            => trim($product->price),
                        'id'            => trim($product->id),

                    ];
                }else{

			$products_sync1           = [
				'code'            => trim($product->code),
                'price'            => trim($product->price),

            ];
			//krsort($products_sync1);

        }



					$products_sync_arr[] = $products_sync1;

					//$this->sma->print_arrays($this->products_model->getSynchProductsCount(1,3));

					//krsort($product1);
			//$this->sma->print_arrays($payment2);
			//$this->sma->print_arrays($sale1, $products, null, null);
			//$this->sma->print_arrays($sale1,$product2, $payment2, null,$si_return2);





					if($products_sync2 = $this->products_model->updatePriceV2($products_sync_arr)){
                        $product_temp = [
                            'code'            => trim($products_sync1['code']),
                            'status'            => true
                            ];
                            $flageTrue=1;

                            $product_temp2[]=$product_temp;

                            }else{
                                //array_push($failed_sales['customer'],$customer1['Offline_id']);
                                $product_temp = [
                                    'code'            => trim($products_sync1['code']),
                                    'status'            => false
                                    ];
                                    $flageFalse=1;

                                    $product_temp2[]=$product_temp;

                            }
                        }










                        $message="";
                        if($flageTrue==1 && $flageFalse==0){
                           $this->response([
                                'products' => $product_temp2,
                                'Updated' => 'Updated',
                                'status'  => true,
                            ], REST_Controller::HTTP_OK);

                        }else if($flageTrue==1 && $flageFalse==1){
                            $this->response([
                                'products' => $product_temp2,
                                'Updated' => 'Partial Updated',
                                'status'  => true,
                            ], REST_Controller::HTTP_OK);

                        }else{
                            $this->response([
                                'products' => $product_temp2,
                                'Updated' => 'No Updated',
                                'status'  => true,
                            ], REST_Controller::HTTP_OK);

                        }

                        }else{

                            $this->response([
                                'message' => 'This request does not contain products',
                                'status'  => false,
                            ], REST_Controller::HTTP_BAD_REQUEST);
                        }
                    }
                }