<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class ClearanceProducts extends REST_Controller
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

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;

		//$expenses=$sales->expenses;
        //$customer=$sales->customer;
        $products_sync_to_offline=$sales->products;
        $user_id=$sales->user_id;
        if ($products_sync_to_offline) {
        $biller_id= $user_id ? $this->users_api->getBillerIdByUserId($user_id) : '';
        $warehouse_id= $user_id ? $this->users_api->getWarehouseIdByUserId($user_id) : '' ;


            if ($biller_id) {
                if ($warehouse_id) {



        //$this->sma->print_arrays($sales);
        $failed_sales = [];
        $products_sync_arr = [];
        $flageTrue=0;
		$flageFalse=0;
		foreach ($products_sync_to_offline as $products_sync) {

			 //$this->sma->print_arrays($sale);

			 //$sale_id=$sale->sale_id ? $this->pos_model->get_return_sale_id(trim($sale->sale_id),$warehouse_id)->id : null;
			 //$sale->biller='كاشير';
			 //$api_var=0;
			 //if(trim($products_sync->add)==1 ||trim($products_sync->edit)==1 )
			$api_var=1;


			$products_sync1           = [
				'biller_id'                       =>$biller_id,
                'warehouse_id'            => $warehouse_id,
                'product_code'            => trim($products_sync->code),
                'api'            => $api_var,




            ];
			//krsort($products_sync1);


            if($products_sync2 = $this->products_model->addSynchProduct($products_sync1)){
				//$this->sma->print_arrays($sale2['sale_id']);
            $products_temp = [
			'code'            => trim($products_sync1['product_code']),
			'status'            => true

			];
            $flageTrue=1;

			$products_temp2[]=$products_temp;




}else{
    $products_temp = [
        'code'            => trim($products_sync1['product_code']),
        'status'            => false

        ];
        $flageFalse=1;

        $products_temp2[]=$products_temp;


		}


					//$products_sync_arr[] = $products_sync1;
		}
					//$this->sma->print_arrays($this->products_model->getSynchProductsCount(1,3));

					//krsort($product1);
			//$this->sma->print_arrays($payment2);
			//$this->sma->print_arrays($sale1, $products, null, null);
			//$this->sma->print_arrays($sale1,$product2, $payment2, null,$si_return2);


















           // $data['products']=$products_temp2;



        $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'products' => $products_temp2,
                'Cleaned' => 'Cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'products' => $products_temp2,
                'Cleaned' => 'Partial cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'products' => $products_temp2,
                'Cleaned' => 'No cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }

    }else{

        $this->response([
            'message' => 'This user does not have warehouse',
            'status'  => false,
        ], REST_Controller::HTTP_BAD_REQUEST);
    }
}else{

    $this->response([
        'message' => 'This user does not have biller',
        'status'  => false,
    ], REST_Controller::HTTP_BAD_REQUEST);
}

        }else{

            $this->response([
                'message' => 'This request does not contain products',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }


    }
}
