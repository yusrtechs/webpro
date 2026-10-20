<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetSupplier extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('users_api');

		$this->load->admin_model('pos2_model');
		$this->load->admin_model('purchases_model');
		$this->load->admin_model('companies_model');



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
		//$this->sma->print_arrays($this->Settings);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;

		$supplier=$sales->supplier;
        if ($supplier) {


        $failed_sales = [];
        $flageTrue=0;
		$flageFalse=0;

		foreach ($supplier as $cust) {

			$supplier1           = [
				'group_id'            => '4',
                'group_name'         => 'supplier',
                'name'    => trim($cust->name),
                'company'       => trim($cust->company),
                'email'       => trim($cust->email),
                'vat_no'   => trim($cust->vat_no),
                'address' => trim($cust->address),
                'state' => trim($cust->state),
                'postal_code' => trim($cust->postal_code),
                'country' => trim($cust->country),
                'phone' => trim($cust->phone),
                'co_type' => 1,
                'api' => 0,
                'Offline_id' => trim($cust->Offline_id),
                'gst_no'            => isset($cust->gst_no)? trim($cust->gst_no) : null,


            ];
			//krsort($cust1);

            //$this->sma->print_arrays($product1);
			if($this->companies_model->addCustomerFromOffline($supplier1)){
			$product_temp = [
			'Offline_id'            => trim($supplier1['Offline_id']),
			'status'            => true
			];
            $flageTrue=1;

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['supplier'],$supplier1['Offline_id']);
                $product_temp = [
                    'Offline_id'            => trim($supplier1['Offline_id']),
			        'status'            => false
                    ];
                    $flageFalse=1;

                    $product_temp2[]=$product_temp;

			}
		}












        $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'suppliers' => $product_temp2,
                'Cleaned' => 'Cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'suppliers' => $product_temp2,
                'Cleaned' => 'Partial cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'suppliers' => $product_temp2,
                'Cleaned' => 'No cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }

        }else{

            $this->response([
                'message' => 'This request does not contain suppliers',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
