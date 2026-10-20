<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetExpenses extends REST_Controller
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

		$expenses=$sales->expenses;
        if ($expenses) {
            $warehouse_id= $sales->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sales->warehouse_code) : '';
            if ($warehouse_id) {


        $failed_sales = [];
        $flageTrue=0;
		$flageFalse=0;

		foreach ($expenses as $expense) {


			if($this->pos2_model->getExpensesBy_date_reference_no(trim($expense->date),trim($expense->reference),trim($expense->Offline_id))==0){



                $expense1           = [
                    'warehouse_id'            => $warehouse_id,
                    'date'         => trim($expense->date),
                    'reference'    => trim($expense->reference),
                    'amount'       => $this->sma->formatDecimal($expense->amount),
                    'paid'       => $this->sma->formatDecimal($expense->paid),
                    'grand_total'       => $this->sma->formatDecimal($expense->grand_total),
                    'total'       => $this->sma->formatDecimal($expense->total),
                    'payment_status'       => trim($expense->payment_status),
                    'created_by'   => trim($expense->created_by),
                    'other' => trim($expense->other),
                    'note' => trim($expense->note),
                    'category_id' => trim($expense->category_id),
                    'order_tax_id' => trim($expense->order_tax_id),
                    'total_tax' => $this->sma->formatDecimal($expense->total_tax),
                    'Offline_id' => trim($expense->Offline_id),


                ];
                //krsort($expense1);
                if($this->pos2_model->addExpense($expense1)){

			$product_temp = [
            'Offline_id'            =>  trim($expense1['Offline_id']),
			'status'            => true
			];
            $flageTrue=1;

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);
                $product_temp = [
                    'Offline_id'            =>  trim($expense1['Offline_id']),
			        'status'            => false
                    ];
                    $flageFalse=1;

                    $product_temp2[]=$product_temp;

			}
        }else{
            //array_push($failed_sales['customer'],$customer1['Offline_id']);
            $product_temp = [
                'Offline_id'            => $expense->Offline_id,
			    'status'            => false
                ];
                $flageFalse=1;

                $product_temp2[]=$product_temp;

        }
		}












        $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'expenses' => $product_temp2,
                'Cleaned' => 'Cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'expenses' => $product_temp2,
                'Cleaned' => 'Partial cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'expenses' => $product_temp2,
                'Cleaned' => 'No cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }


    }else{

        $this->response([
            'message' => 'This request does not contain warehouse_code',
            'status'  => false,
        ], REST_Controller::HTTP_BAD_REQUEST);
    }

        }else{

            $this->response([
                'message' => 'This request does not contain expenses',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
