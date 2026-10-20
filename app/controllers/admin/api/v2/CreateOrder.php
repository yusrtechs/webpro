<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class CreateOrder extends REST_Controller
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


        $sales=$sales->sales;
        if ($sales) {
        //$this->sma->print_arrays($sales);
        $failed_sales = [];

		$flageTrue=0;
		$flageFalse=0;


		foreach ($sales as $sale) {

			 //$this->sma->print_arrays($sale);

			 $warehouse_id= $this->pos2_model->getSystemSetting()->default_warehouse;
			 $biller_id= $this->pos2_model->getSystemSetting()->default_biller;
			 $sale_id=$sale->sale_id ? $this->pos2_model->get_return_sale_id(trim($sale->sale_id),$warehouse_id,$biller_id)->id : null;
			 $biller= $this->companies_model->getCompanyByID($biller_id)->company;
            if($this->pos2_model->getCompanyByOfflineIDV2($sale->customer_id,$sale->customer)->id){
			    $customer_id=  $this->pos2_model->getCompanyByOfflineIDV2($sale->customer_id,$sale->customer)->id;
            }else{
                $customer1           = [
				'group_id'            => '3',
                'group_name'         => 'customer',
                'name'    => trim($sale->customer),
                'company'       => trim($sale->customer),
                'customer_group_id'       => $this->pos2_model->getSystemSetting()->customer_group,
				'co_type' => 1,
                'api' => 0,
                'Offline_id' => trim($sale->customer_id),

            ];
                $customer_id=  $this->companies_model->addCustomerFromOffline($customer1);
            }

			$sync=$this->pos2_model->getInvoiceBy_date_biller_id_Offline_Pos_id(trim($sale->date),$biller_id,trim($sale->shop_id));
            if($sync==0){

                $salesman_id         = $sale->salesman_id ? $sale->salesman_id : null;
                if($salesman_id){
                $salesman_details=$this->pos2_model->getSalesmanByID($salesman_id);
                    if(!empty($salesman_details)){
                        $commission_type=$salesman_details->cf5;
                        $commission=$salesman_details->cf6;
                    }
                }

                if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
            	{
					$brId=$this->pos2_model->getWarehouse_VATByID($warehouse_id)->brId;
				}else{
					$brId=1;
				}


			$sale1           = [
				'date' => trim($sale->date),
                'reference_no'                        => $this->pos2_model->getReference('pos'),
                'customer_id'                 => $customer_id,
                'customer'                    => trim($sale->customer),
                'biller'                        => $biller,
                'biller_id'                       =>$biller_id,
                'total'            => trim($sale->total),
                'sale_status'            => trim($sale->sale_status),
                'payment_status'            => 'paid',
                'warehouse_id'            => $warehouse_id,
                'grand_total'            => trim($sale->grand_total),
                'pos'            => '1',
                'surcharge'            => '0',
                'return_sale_total'            => '0',
                'paid'            => trim($sale->paid),
                'discount_allowance' =>isset($sale->discount_allowance)? trim($sale->discount_allowance):'0',
				'total_tax'            => trim($sale->total_tax),
                'note'            => trim($sale->note),
                'staff_note'            => '',
                'product_discount'            => '0',
                'order_discount_id'            => '0',
                'order_discount'            => trim($sale->total_discount),
                'total_discount'            => trim($sale->total_discount),
                'product_tax'            => trim($sale->total_tax),
                'order_tax_id'            => '1',
                'order_tax'            => '0',
                'shipping'            => trim($sale->shipping),
                'created_by'            => 1,
                'Offline_Pos_id'            => trim($sale->shop_id),
                'sale_id'            => $sale_id,
                'hash'              => hash('sha256', microtime() . mt_rand()),
				'supply_date'          => trim($sale->date),
				'emp_type'            => '0',
                'emp_id'            => '0',
                'dept'            => '0',
                'table_no'            => '0',
                'app_name'              => 'SHOP',
				'app_version'              => 'V1',
                'salesman_id'        => $salesman_id,
                'commission_type'        => null,
                'commission'        => null,
                'commission_amount'        => 0,
                'donation'            => '0',
                'coupon_code'            => null,
                'uuid'                => $this->pos2_model->getUniqeUUID()->UUID,
                'is_sub'            => '0',

            ];

            if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
            {
				$sale1  ['grpId']= 1;
                $sale1  ['coId']= 1;
                $sale1  ['brId']=$brId ? $brId : 1;
                //$sale1  ['sale_no']=$this->pos2_model->getMainBranchesByID($sale1['brId'])->branch_prefix ? $this->pos2_model->getMainBranchesByID($sale1['brId'])->branch_prefix.'-'.$this->pos2_model->getCountSalesBybrid($sale1['brId'])->count : $this->pos2_model->getCountSalesBybrid($sale1['brId'])->count;
            }

            if(isset($sale->currency_code)){
                $sale1  ['currency_code']= $sale->currency_code;
            }
            if(isset($sale->currency_rate)){
                $sale1  ['currency_rate']= $sale->currency_rate;
            }

			krsort($sale1);

			$products=  $sale->items;
			//$products=(array) $products;
			$product2 = [];

			$si_return2 = [];

			foreach ($products as $product) {

                if($product->product_type=="manual"){
					$product_id="manual".$product->product_code;
					$warehouse_id= $this->pos2_model->getSystemSetting()->default_warehouse;
					$sale_item_id=null;
					$product->product_type="manual";
                    $product_code=$product->product_code;
				    $product_type="manual";
				    $product_unit_id='1';
				}else{
					$pr=$this->pos2_model->getProductsByShopId(trim($product->shop_id));
					$product_id=$pr->id;
					$warehouse_id= $this->pos2_model->getSystemSetting()->default_warehouse;
					$sale_item_id=$sale_id ? $this->pos2_model->get_return_sale_item_id($sale_id,$product_id,$warehouse_id)->id : null;

                    $product_code=$pr->code;
				    $product_type=$pr->product_type;
				    $product_unit_id=$pr->unit;
				}



			$product1 = [
                        'product_id'        => $product_id,
                        'product_name'      => $product->product_name,
                        'product_code'      => $product_code,
                        'product_type'            => $product_type,
						'option_id'            => null,
						'net_unit_price'    => $this->sma->formatDecimal($product->net_unit_price),
                        'unit_price'        => $this->sma->formatDecimal($product->unit_price),
                        'unit_quantity'     => $product->quantity,
						'product_unit_id'     => $product_unit_id,
                        'product_unit_code'     => '',
                        'quantity'          => $product->quantity,
                        'subtotal'          => $this->sma->formatDecimal($product->subtotal),
						'item_discount'          => $this->sma->formatDecimal($product->item_discount),
                        'warehouse_id'            => $warehouse_id,
						'item_tax'   => $this->sma->formatDecimal($product->item_tax),
                        'tax_rate_id'   => '3',
                        'tax'   => '15%',
                        'real_unit_price'   => trim($product->unit_price),
						'sale_item_id'   => $sale_item_id,
                        'area'            => 0,
						'length'            => 0,
						'width'            => 0,
                        'promo_free'   => $product->unit_price!=0 ? 0 : 1,

                    ];

                    if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
					{
						$product1  ['grpId']= 1;
						$product1  ['coId']= 1;
						$product1  ['brId']=$brId ? $brId : 1;
					}
                    if($product->modifiers){
                        $product1  ['modifiers']= $product->modifiers;
                    }
					//krsort($product1);
					//$this->sma->print_arrays($product);
					$product2[] = $product1;


					if($sale_id){
					$si_return = [
                        'id'           => $sale_item_id,
                        'sale_id'      => $sale_id,
                        'product_id'   => $product_id,
                        'option_id'    => null,
                        'quantity'     => (0 - $product->quantity),
                        'warehouse_id' => $warehouse_id,

                    ];

					$si_return2[] = $si_return;



					}


					}


			//$product2[] = $products;
			$payments=  $sale->payment;
			//$payments=(array) $payments;
			$payment2 = [];

			foreach ($payments as $payment) {

			$payment1 = [
                        'date'        => $payment->date,
                        'amount'    => $this->sma->formatDecimal($payment->amount),
                        'commercial_discount' =>'0',
                        'commercial_discount_id' =>NULL,
						'paid_by'      => trim($payment->paid_by),
                        'cheque_no'      => '',
                        'cc_no'            => '',
						'cc_holder'            => '',
						'cc_month'     => '',
						'cc_year'     => '',
                        'cc_type'     => $payment->paid_by!='CC' ? '' : trim($payment->cc_type),
                        'created_by'          => 1,
                        'type'            => 'received',
						'note'   => null,
                        'pos_paid'   => $this->sma->formatDecimal($payment->amount),
                        'pos_balance'   => '0',
						'gl_payment_method_id' =>'0',
                        'donation'            => '0',



                    ];

                    if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
					{
						$payment1  ['grpId']= 1;
						$payment1  ['coId']= 1;
						$payment1  ['brId']=$brId ? $brId : 1;
					}

                    if(isset($payment->currency)){
                        $payment1  ['currency']= $payment->currency;
                    }
                    if(isset($payment->currency_rate)){
                        $payment1  ['currency_rate']= $payment->currency_rate;
                    }

					$payment2[] = $payment1;

					}
					//$payment2[] = $payments;
					//$this->sma->print_arrays($payments);

					//krsort($product1);
			//$this->sma->print_arrays($payment2);
			//$this->sma->print_arrays($sale1, $products, null, null);
			//$this->sma->print_arrays($sale1,$product2, $payment2, null,$si_return2);

		if($sale_id){

		if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null,$si_return2)){
				//$this->sma->print_arrays($sale1['shop_id']);

			$response["success"] = 1;
			$salse_temp = [
			'shop_id'            => trim($sale1['Offline_Pos_id']),
			'status'            => true

			];

            $flageTrue=1;
			$salse_temp2[]=$salse_temp;



}else{
			$response["success"] = 0;
            $salse_temp = [
                'shop_id'            => trim($sale1['Offline_Pos_id']),
                'status'            => false

                ];
                $flageFalse=1;
                $salse_temp2[]=$salse_temp;

			//array_push($failed_sales['sales'],$sale1['shop_id']);

		}


			}else{

					if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null)){
				//$this->sma->print_arrays($sale2['sale_id']);


			$response["success"] = 1;
			//array_push($failed_sales['sales'],'1');
			$salse_temp = [
			'shop_id'            => trim($sale1['Offline_Pos_id']),
			'status'            => true
			];

            $flageTrue=1;
			$salse_temp2[]=$salse_temp;


}else{
			$response["success"] = 0;
            $salse_temp = [
                'shop_id'            => trim($sale1['Offline_Pos_id']),
                'status'            => false

                ];
                $flageFalse=1;
                $salse_temp2[]=$salse_temp;
		}




			}
		}else{
		$response["success"] = 1;
			$salse_temp = [
			'shop_id'            => trim($sale->shop_id),
			'status'            => true
			];

            $flageFalse=1;
            $flageTrue=1;
			$salse_temp2[]=$salse_temp;

		}
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
                'message' => 'This request does not contain sales',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
