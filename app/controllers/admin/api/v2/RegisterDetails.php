<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class RegisterDetails extends REST_Controller
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
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		//$date="";
		if($pos_reg->closed_at && $pos_reg->closed_at!=null){
			//$supply_date=$sales->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'closed_at is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		//$sale_status="";


				$user_register = $this->pos2_model->registerData($pos_reg->user_id);

		if($user_register){
			 //$this->sma->print_arrays($user_register);
			 //$rid           = $user_register ? $user_register->id : $this->session->userdata('register_id');
                //$user_id       = $user_register ? $user_register->user_id : $this->session->userdata('user_id');


				$pos_register_open         = [
				'id'         => trim($user_register->id),
				'date'         => trim($user_register->date),
                'cash_in_hand'       => $this->sma->formatDecimal($user_register->cash_in_hand),
                'user_id'   => trim($user_register->user_id),
                'status' => 'open',
                'Offline_id' => 1,
                'cash_in_hand_clearance' =>'0',


            ];

			$pos_register_close          = [
				'closed_at'         => trim($pos_reg->closed_at),
                'user_id' => trim($pos_reg->user_id),


            ];


			$sales=$this->pos2_model->getToatalSalesPOSTailor($this->pos2_model->getUser(trim($pos_reg->user_id))->biller_id,'completed',trim($user_register->date),trim($pos_reg->closed_at));
			$re_sales=$this->pos2_model->getToatalSalesPOSTailor($this->pos2_model->getUser(trim($pos_reg->user_id))->biller_id,'returned',trim($user_register->date),trim($pos_reg->closed_at));


			//$this->sma->print_arrays(trim($user_register->date),trim($pos_reg->closed_at));
			//$ccsales= $this->pos2_model->getRegisterCCSales(trim($user_register->date), trim($pos_reg->user_id));
			//$cashsales= $this->pos2_model->getRegisterCashSales(trim($user_register->date), trim($pos_reg->user_id));
			/*$total=0;
			$total_tax=0;
			$shipping=0;
			foreach ($sales as $sale) {

				if($sale->balance!=0){
			$entryDebitAccount[] =$entryDebitAccountCustomer;
			$entryDebitBalance[] =$sale->balance;
				}

			$total += $sale->total;
            $total_tax += $sale->total_tax;
            $shipping += $sale->shipping;
            }
			*/
			$paymentcash=$this->pos2_model->getToatalSalesPaymentPOSTailor($this->pos2_model->getUser(trim($pos_reg->user_id))->biller_id,'cash',trim($user_register->date),trim($pos_reg->closed_at));
			$paymentcc=$this->pos2_model->getToatalSalesPaymentPOSTailor($this->pos2_model->getUser(trim($pos_reg->user_id))->biller_id,'CC',trim($user_register->date),trim($pos_reg->closed_at));

			//$this->sma->print_arrays($paymentcash);

				//if($this->pos2_model->closePOSRegisterTailor( $pos_register_open, $pos_register_close,str_replace("cpesoft_","",$this->db->database))){
					$this->set_response([
                    'message' => 'Success',
					'status'  => true,
                    'invoice count' => $sales[0]->invoice_count,
                    'return invoice count' => $re_sales[0]->invoice_count,
                    'cash in hand' => $user_register->cash_in_hand,
                    'open date' => $user_register->date,
                    'close date' => $pos_reg->closed_at,
                    'invoice grand total' => $sales[0]->grand_total,
                    'return invoice grand total' => $re_sales[0]->grand_total,
                    'net sales grand total' => ($sales[0]->grand_total + $re_sales[0]->grand_total),
                    'total Advance' => ($sales[0]->balance + $re_sales[0]->balance),
                    'total cc' => ($paymentcc->amount),
                    'total cash' => ($paymentcash->amount + $user_register->cash_in_hand),

                ], REST_Controller::HTTP_OK);


		}else{
			$this->set_response([
                    'message' => 'There is no open record for user #' . $pos_reg->user_id . '.',
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
