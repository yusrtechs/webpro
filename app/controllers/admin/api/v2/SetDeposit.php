<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetDeposit extends REST_Controller
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
		$cust =json_decode($json);
		//$this->sma->print_arrays($cust);
		//$sales=$sales->JSON3;
		$customer_id="";
		if($cust->customer_id && $cust->customer_id!=null){
			$customer_id=$cust->customer_id;
		}else{
		$this->set_response([
                    'message' => 'customer_id is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		$customer="";
		if($cust->customer && $cust->customer!=null){
		}else{
		$this->set_response([
                    'message' => 'customer is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        $Offline_id="";
		if($cust->Offline_id && $cust->Offline_id!=null){
			$Offline_id=$cust->Offline_id;
		}else{
		$this->set_response([
                    'message' => 'Offline_id is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        $date="";
		if($cust->date && $cust->date!=null){
			$date=$cust->date;
		}else{
		$this->set_response([
                    'message' => 'date is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        $amount="";
		if($cust->amount && $cust->amount!=null){
			$amount=$cust->amount;
		}else{
		$this->set_response([
                    'message' => 'amount is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        $purchasing_amount="";
		if($cust->purchasing_amount && $cust->purchasing_amount!=null){
			$purchasing_amount=$cust->purchasing_amount;
		}else{
		    $purchasing_amount=$cust->amount;
		}


        $paid_by="";
		if($cust->paid_by && $cust->paid_by!=null){
			$paid_by=$cust->paid_by;
		}else{
		$this->set_response([
                    'message' => 'paid_by is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        $created_by="";
		if($cust->created_by && $cust->created_by!=null){
			$created_by=$cust->created_by;
		}else{
		$this->set_response([
                    'message' => 'created_by is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        $customer_id=  $this->pos2_model->getCompanyByOfflineIDV2($cust->customer_id,$cust->customer)->id ? $this->pos2_model->getCompanyByOfflineIDV2($cust->customer_id,$cust->customer)->id : ($this->pos2_model->getCompanyByidIDV2($cust->customer_id,$cust->customer)->id ? $this->pos2_model->getCompanyByidIDV2($cust->customer_id,$cust->customer)->id : ($this->pos2_model->getCompanyByOfflineID($cust->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($cust->customer_id)->id : $cust->customer_id));


        if($this->pos2_model->getDepositBy_date_Offline_id(trim($cust->date),trim($cust->customer_id),trim($cust->Offline_id))==0){

            $company=$this->companies_model->getCompanyByID($customer_id);





            $data = [
                'date'       => $date,
                'amount'     => $amount,
                'paid_by'    => $paid_by,
                'note'   => trim($cust->note),
                'company_id' => $customer_id,
                'created_by' => $created_by,
                'Offline_id' => $Offline_id,
            ];

            if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
					{
						$data  ['grpId']= 1;
						$data  ['coId']= 1;
						$data  ['brId']=$company->brId;
					}


		if($company){


            $cdata = [
                'deposit_amount' => ($company->deposit_amount + $purchasing_amount),
            ];



			$payment_type="";
			if ($this->pos2_model->getSystemSetting()->accounting){
    			if($paid_by=="cash"){
	    		    $payment_type=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;
                }else{
                    $gl_payment_method_id =isset($cust->gl_payment_method_id)? trim($cust->gl_payment_method_id):'0';
                    if($gl_payment_method_id==0){
    					$payment_type=$this->pos2_model->getPaymentTypeByPaymentType('CC')->payment_account;
	    			}else{
		    			$payment_type=$this->pos2_model->getPaymentTypeById($gl_payment_method_id)->payment_account;
			    	}
			    }
            }


				if($this->pos2_model->addDeposit($data, $cdata,$payment_type)){
					$this->set_response([
                    'message' => 'Success',
                    'status'  => true,
                ], REST_Controller::HTTP_OK);
            }else{

                $this->set_response([
                    'message' => 'Failure',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);

            }

		}else{
			$this->set_response([
                    'message' => 'Customer could not be found',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);

		}

        }else{
            $this->set_response([
                    'message' => 'Success',
                    'status'  => true,
                ], REST_Controller::HTTP_OK);

        }


		/*
		if ($sale = $this->sales_api->getSale($filters)) {
                if (!empty($filters['include'])) {
                    foreach ($filters['include'] as $include) {
                        if ($include == 'items') {
                            $sale->items = $this->sales_api->getSaleItems($sale->id);
                        }
                        if ($include == 'warehouse') {
                            $sale->warehouse = $this->sales_api->getWarehouseByID($sale->warehouse_id);
                        }
                    }
                }

                $sale->created_by = $this->sales_api->getUser($sale->created_by);
                $sale             = $this->setSale($sale);
                $this->set_response($sale, REST_Controller::HTTP_OK);
            } else {
                $this->set_response([
                    'message' => 'Sale could not be found for reference ' . $reference . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
				*/

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

    }
}
