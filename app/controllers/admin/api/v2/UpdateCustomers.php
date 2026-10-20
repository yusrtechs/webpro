<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class UpdateCustomers extends REST_Controller
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
		$id="";
		if($cust->id && $cust->id!=null){
			$id=$cust->id;
		}else{
		$this->set_response([
                    'message' => 'id is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		$company="";
		if($cust->company && $cust->company!=null){
            $company=$cust->company;
		}else{
		$this->set_response([
                    'message' => 'company is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}


		$data           = [
				'company'       => trim($cust->company),



            ];

			if($cust->name && $cust->name!=null){
				$data['name'] = $cust->name;
			}
			if($cust->email && $cust->email!=null){
				$data['email'] = $cust->email;
			}
			if($cust->vat_no && $cust->vat_no!=null){
				$data['vat_no'] = $cust->vat_no;
			}
			if($cust->address && $cust->address!=null){
				$data['address'] = $cust->address;
			}
			if($cust->state && $cust->state!=null){
				$data['state'] = $cust->state;
			}
			if($cust->postal_code && $cust->postal_code!=null){
				$data['postal_code'] = $cust->postal_code;
			}
			if($cust->country && $cust->country!=null){
				$data['country'] = $cust->country;
			}
			if($cust->phone && $cust->phone!=null){
				$data['phone'] = $cust->phone;
			}
            if($cust->subscription_duration && $cust->subscription_duration!=null){
				$data['subscription_duration'] = $cust->subscription_duration;
			}
            if($cust->start_subscription && $cust->start_subscription!=null){
				$data['start_subscription'] = $cust->start_subscription;
			}
            if($cust->end_subscription && $cust->end_subscription!=null){
				$data['end_subscription'] = $cust->end_subscription;
			}

        if (isset($id) && $id!==null && isset($company) && $company!==null) {

            $company_id=  $this->pos2_model->getCompanyByOfflineIDV2($id,$company)->id ? $this->pos2_model->getCompanyByOfflineIDV2($id,$company)->id : ($this->pos2_model->getCompanyByidIDV2($id,$company)->id ? $this->pos2_model->getCompanyByidIDV2($id,$company)->id : ($this->pos2_model->getCompanyByOfflineID($id)->id ? $this->pos2_model->getCompanyByOfflineID($id)->id : $id));
        }else{
            $company_id=$id;
        }

		if($this->companies_model->getCompanyByID($company_id)){
				if($this->companies_model->updateCompany($company_id, $data)){
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

		}else{
			$this->set_response([
                    'message' => 'Customer could not be found for id ' . $company_id . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);

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
