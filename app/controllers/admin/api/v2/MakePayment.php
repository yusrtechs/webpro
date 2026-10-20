<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class MakePayment extends REST_Controller
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
		$payment =json_decode($json);
		//$sales=$sales->JSON3;
		//$this->sma->print_arrays($this->Settings);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;

		//$payments_only=$sales->payment;
        if ($payment) {


        $failed_sales = [];
        $flageTrue=0;
		$flageFalse=0;



			$sale_id=$payment->sale_id;// ? $this->pos2_model->get_return_sale_id_for_payment(trim($payment->sale_id),$payment->created_by)->id : null;

			    if($this->pos2_model->getSaleByID($sale_id)){

                $payment3 = [
                    'sale_id'        => $sale_id,
                    'reference_no' => $this->pos2_model->getReference('pay'),
                    'amount'    => $this->sma->formatDecimal($payment->paid_amount),
                    'commercial_discount' => '0',
                    'commercial_discount_id' =>null,
                    'paid_by'      => $payment->payment_type,
                    'cheque_no'      => trim($payment->cheque_no),
                    'cc_type'     => $payment->cc_type,
                    'cc_holder'            => null,
                    'cc_month'     => null,
                    'cc_year'     => null,
                    'created_by'          => trim($payment->created_by),
                    'type'            => "received",
                    'note'   => null,
                    'pos_paid'   => $this->sma->formatDecimal($payment->paid_amount),
                    'pos_balance'   => 0,
                    'gl_payment_method_id' =>'0',


                ];


			//krsort($expense1);
			if($this->pos2_model->addPayment($payment3)){

			$product_temp = [
            'Amount'            => trim($payment3['amount']),
			'status'            => true
			];
            $flageTrue=1;

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);
                $product_temp = [
                    'Amount'            => trim($payment3['amount']),
			        'status'            => false
                    ];
                    $flageFalse=1;

                    $product_temp2[]=$product_temp;

			}
        }else{
            //array_push($failed_sales['customer'],$customer1['Offline_id']);
            $product_temp = [
                'message' => 'Sale item could not be found for sale_id ' . $sale_id . ' ',
                    'status'            => false
                ];
                $flageFalse=1;

                $product_temp2[]=$product_temp;

        }













        $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'payments' => $product_temp2,
                'Cleaned' => 'Cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'payments' => $product_temp2,
                'Cleaned' => 'Partial cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'payments' => $product_temp2,
                'Cleaned' => 'No cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }

        }else{

            $this->response([
                'message' => 'This request does not contain payments',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
