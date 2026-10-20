<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetCustomer extends REST_Controller
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


    protected function setSale($sale)
    {
        unset($sale->address_id, $sale->api, $sale->attachment, $sale->hash, $sale->pos, $sale->reserve_id, $sale->return_id, $sale->return_sale_ref, $sale->return_sale_total, $sale->sale_id, $sale->shop, $sale->staff_note, $sale->surcharge, $sale->updated_at, $sale->suspend_note);
        if (isset($sale->items) && !empty($sale->items)) {
            foreach ($sale->items as &$item) {
                if (isset($item->option_id) && !empty($item->option_id)) {
                    if ($variant = $this->sales_api->getProductVariantByID($item->option_id)) {
                        $item->product_variant_id   = $variant->id;
                        $item->product_variant_name = $variant->name;
                    }
                }
                $item->product_unit_quantity = $item->unit_quantity;
                unset($item->id, $item->sale_id, $item->warehouse_id, $item->real_unit_price, $item->sale_item_id, $item->option_id, $item->unit_quantity);
                $item = (array) $item;
                ksort($item);
            }
        }
        $sale = (array) $sale;
        ksort($sale);
        return $sale;
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

		$customer=$sales->customer;
        if ($customer) {


        $failed_sales = [];
        $flageTrue=0;
		$flageFalse=0;

		foreach ($customer as $cust) {

			$customer1           = [
				'group_id'            => '3',
                'group_name'         => 'customer',
                'name'    => trim($cust->name),
                'company'       => trim($cust->company),
                'email'       => trim($cust->email),
                'customer_group_id'       => trim($cust->customer_group_id),
				'customer_group_name'       => trim($cust->customer_group_name),
				'vat_no'   => trim($cust->vat_no),
                'address' => trim($cust->address),
                'state' => trim($cust->state),
                'postal_code' => trim($cust->postal_code),
                'country' => trim($cust->country),
                'phone' => trim($cust->phone),
                'co_type' => 1,
                'api' => 0,
                'Offline_id' => trim($cust->Offline_id),
                'code'            => isset($cust->code)? trim($cust->code) : null,
                'cr_no'            => isset($cust->cr_no)? trim($cust->cr_no) : null,
                'building_no'            => isset($cust->building_no)? trim($cust->building_no) : null,
                'street_name'            => isset($cust->street_name)? trim($cust->street_name) : null,
                'district_name'            => isset($cust->district_name)? trim($cust->district_name) : null,
                'additional_no'            => isset($cust->additional_no)? trim($cust->additional_no) : null,
                'city'            => isset($cust->city)? trim($cust->city) : null,
                'subscription_duration'            => isset($cust->subscription_duration)? trim($cust->subscription_duration) : null,
                'start_subscription'            => isset($cust->start_subscription)? trim($cust->start_subscription) : null,
				'end_subscription'            => isset($cust->end_subscription)? trim($cust->end_subscription) : null,




            ];
			//krsort($cust1);

            //$this->sma->print_arrays($product1);
			if($this->companies_model->addCustomerFromOffline($customer1)){
			$product_temp = [
			'Offline_id'            => trim($customer1['Offline_id']),
			'status'            => true
			];
            $flageTrue=1;

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);
                $product_temp = [
                    'Offline_id'            => trim($customer1['Offline_id']),
			        'status'            => false
                    ];
                    $flageFalse=1;

                    $product_temp2[]=$product_temp;

			}
		}












        $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'customers' => $product_temp2,
                'Cleaned' => 'Cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'customers' => $product_temp2,
                'Cleaned' => 'Partial cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'customers' => $product_temp2,
                'Cleaned' => 'No cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }

        }else{

            $this->response([
                'message' => 'This request does not contain customers',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
