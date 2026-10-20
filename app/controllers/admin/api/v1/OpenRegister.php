<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class OpenRegister extends REST_Controller
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
		$pos_reg =json_decode($json);
		//$sales=$sales->JSON3;
		//$id="";
		if($pos_reg->user_id && $pos_reg->user_id!=null){
			//$id=$sales->user_id;
		}else{
		$this->set_response([
                    'message' => 'user_id is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
		}
		//$date="";
		if($pos_reg->date && $pos_reg->date!=null){
			//$supply_date=$sales->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'date is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
		}
		//$sale_status="";
		if($pos_reg->cash_in_hand && $pos_reg->cash_in_hand!=null){
			//$sale_status=$sales->sale_status;
		}else{
		$this->set_response([
                    'message' => 'cash_in_hand is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
		}
		
		$pos_register_open         = [
				'date'         => trim($pos_reg->date),
                'cash_in_hand'       => $this->sma->formatDecimal($pos_reg->cash_in_hand),
                'user_id'   => trim($pos_reg->user_id),
                'status' => 'open',
                'Offline_id' => 1,
                'cash_in_hand_clearance' =>'0',
                
                
            ];
			
		if($this->pos2_model->getUser($pos_reg->user_id)){
				if($this->pos2_model->openRegister($pos_register_open)){
					$this->set_response([
                    'message' => 'Success',
                    'status'  => true,
                ], REST_Controller::HTTP_OK);
				}else{
					
					$this->set_response([
                    'message' => 'Register could not be Opened',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
					
				}
			
		}else{
			$this->set_response([
                    'message' => 'user could not be found for user_id ' . $pos_reg->user_id . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
			
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
