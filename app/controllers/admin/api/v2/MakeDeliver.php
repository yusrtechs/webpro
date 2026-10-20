<?php
//ini_set("display_errors", 1);
//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class MakeDeliver extends REST_Controller
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
		//$this->sma->print_arrays($sales->meta);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;


        $sale=$sales->meta;
        $products=$sales->data;
        if ($sale) {
        //$this->sma->print_arrays($sales);
        $failed_sales = [];

		$flageTrue=0;
		$flageFalse=0;


		//foreach ($sales as $sale){



			 $warehouse_id= $sale->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sale->warehouse_code) : $sale->warehouse_id;

            $sync=$this->pos2_model->getDeliverBy_reference_no_biller_id_V2(trim($sale->reference_no),$sale->biller_id,trim($sale->Offline_id));
            if($sync==0){

             $sale1           = [
				'date' => trim($sale->date),
                'reference_no'                        => trim($sale->reference_no),
                'customer_id'                 => trim($sale->customer_id),
                'customer'                    => trim($sale->customer),
                'biller'                        => trim($sale->biller),
                'biller_id'                       =>trim($sale->biller_id),
                'total'            => 0,
                'status'            => 'pending',
                'warehouse_id'            => $warehouse_id,
                'grand_total'            => 0,
                'total_tax'            => 0,
                'note'            => trim($sale->note),
                'address'            => trim($sale->address),
                'product_discount'            => 0,
                'order_discount'            => 0,
                'total_discount'            => 0,
                'product_tax'            => 0,
                'order_tax_id'            => 1,
                'order_tax'            => 0,
                'shipping'            => 0,
                'payment_term'            => null,
                'created_by'            => trim($sale->created_by),
                'Offline_id'            => trim($sale->Offline_id),
                'hash'              =>  hash('sha256', microtime() . mt_rand()),






            ];
			//krsort($sale1);

			//$products=  $sale->items;
			//$products=(array) $products;
			$product2 = [];

			$si_return2 = [];

			foreach ($products as $product) {
            $pr=$this->pos2_model->getProductByCode($product->product_code);
            //
			$product_id=$pr->id;
			$product_type=$pr->type;

			//$warehouse_id= $product->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($product->warehouse_code) : $product->warehouse_id;
			$units                   = $this->pos2_model->getUnitByID($product->unit_id);
            $taxes                   = $this->pos2_model->getTaxRateByID(1);
            //$this->sma->print_arrays($taxes);
			$product1 = [
                        'product_id'        => $product_id,
                        'product_name'      => $product->product_name,
                        'product_code'      => $product->product_code,
                        'product_type'            => $product_type,
						'option_id'            => null,
						'net_unit_price'    => 0,
                        'unit_price'        => 0,
                        'unit_quantity'     => $product->item_quantity,
						'product_unit_id'     => $product->unit_id,
                        'product_unit_code'     => $units->code,
                        'quantity'          => $product->item_quantity,
                        'subtotal'          => 0,
						'item_discount'          => 0,
                        'warehouse_id'            => $warehouse_id,
						'item_tax'   => 0,
                        'tax_rate_id'   => trim(1),
                        'tax'   => ((int)($taxes->rate))."%",
                        'discount'   => 0,
                        'serial_no'   => null,
                        'real_unit_price'   => 0,

                    ];
					//krsort($product1);

					$product2[] = $product1;





					}



					if($deliver2 = $this->pos2_model->addDeliver($sale1,$product2)){
				//$this->sma->print_arrays($deliver2['sale_id']);

           $response["success"] = 1;
			//array_push($failed_sales['sales'],'1');
			$salse_temp = [
                'id'            => trim($deliver2['deliver_id']),
                'Offline_id'            => trim($sale1['Offline_id']),
                'reference_no'            => trim($sale1['reference_no']),
                'status'            => true
			];
            $flageTrue=1;
			$salse_temp2[]=$salse_temp;


            }else{
			$response["success"] = 0;
            $salse_temp = [
                'reference_no'            => trim($sale1['reference_no']),
			    'status'            => false

            ];
                $flageFalse=1;
                $salse_temp2[]=$salse_temp;
		}








		 $message="";
         if($flageTrue==1 && $flageFalse==0){
            $this->response([
                 'sales' => $salse_temp2,
                 'Cleaned' => 'Cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }else if($flageTrue==1 && $flageFalse==1){
             $this->response([
                 'sales' => $salse_temp2,
                 'Cleaned' => 'Partial cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }else{
             $this->response([
                 'sales' => $salse_temp2,
                 'Cleaned' => 'No cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }
        }else{

            $this->response([
                'message' => 'Deliver is already available.',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        }else{

            $this->response([
                'message' => 'This request does not contain delivers',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
