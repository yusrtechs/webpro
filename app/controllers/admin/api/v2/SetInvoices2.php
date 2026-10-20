<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetInvoices2 extends REST_Controller
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

			 $warehouse_id= $sale->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sale->warehouse_code) : $sale->warehouse_id;
			 //$sale->biller='كاشير';
			 $biller_id= $sale->biller ? $this->users_api->getBillerIdByUserName($sale->biller) : $sale->biller_id;
			 $sale_id=$sale->sale_id ? $this->pos2_model->get_return_sale_id(trim($sale->sale_id),$warehouse_id,$biller_id)->id : null;
			 $biller= $this->companies_model->getCompanyByID($biller_id)->company ? $this->companies_model->getCompanyByID($biller_id)->company : trim($sale->biller);

			 //$customer_id= $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id;
			 $customer_id=  $this->pos2_model->getCompanyByOfflineIDV2($sale->customer_id,$sale->customer)->id ? $this->pos2_model->getCompanyByOfflineIDV2($sale->customer_id,$sale->customer)->id : ($this->pos2_model->getCompanyByidIDV2($sale->customer_id,$sale->customer)->id ? $this->pos2_model->getCompanyByidIDV2($sale->customer_id,$sale->customer)->id : ($this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id : $sale->customer_id));
             //$customer_id=  $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id : $sale->customer_id;

			//if($this->pos2_model->getInvoiceBy_date_reference_no_biller_id(trim($sale->date),trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id))==0){
			$sync=$this->pos2_model->getInvoiceBy_reference_no_biller_id_V2(trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id));
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
                'reference_no'                        => trim($sale->reference_no),
                'customer_id'                 => $customer_id,
                'customer'                    => trim($sale->customer),
                'biller'                        => $biller,
                'biller_id'                       =>$biller_id,
                'total'            => trim($sale->total),
                'sale_status'            => trim($sale->sale_status),
                'payment_status'            => trim($sale->payment_status),
                'warehouse_id'            => $warehouse_id,
                'grand_total'            => trim($sale->grand_total),
                'pos'            => '1',
                'surcharge'            => trim($sale->surcharge),
                'return_sale_total'            => trim($sale->return_sale_total),
                'paid'            => trim($sale->paid),
                'discount_allowance' =>isset($sale->discount_allowance)? trim($sale->discount_allowance):'0',
				'total_tax'            => trim($sale->total_tax),
                'note'            => trim($sale->note),
                'staff_note'            => trim($sale->staff_note),
                'product_discount'            => trim($sale->product_discount),
                'order_discount_id'            => trim($sale->order_discount_id),
                'order_discount'            => trim($sale->order_discount),
                'total_discount'            => trim($sale->total_discount),
                'product_tax'            => trim($sale->product_tax),
                'order_tax_id'            => trim($sale->order_tax_id),
                'order_tax'            => trim($sale->order_tax),
                'shipping'            => trim($sale->shipping),
                'total_items'            => trim($sale->total_items),
                'payment_term'            => trim($sale->payment_term),
                'rounding'            => trim($sale->rounding),
                'created_by'            => trim($sale->created_by),
                'Offline_Pos_id'            => trim($sale->Offline_Pos_id),
                'sale_id'            => $sale_id,
                'hash'              => $sale->hash ? trim($sale->hash) : hash('sha256', microtime() . mt_rand()),
				'supply_date'          => trim($sale->date),
				'emp_type'            => trim($sale->emp_type),
                'emp_id'            => trim($sale->emp_id),
                'dept'            => trim($sale->dept),
                'table_no'            => trim($sale->table_no),
                'app_name'              => isset($sale->app_name) ? $sale->app_name : null,
				'app_version'              => isset($sale->app_version) ? $sale->app_version : null,
                'salesman_id'        => $salesman_id,
                'commission_type'        => $commission_type ? $commission_type : null,
                'commission'        => $commission ? $commission : null,
                'commission_amount'        => $commission ? ($commission_type==1 ? $this->sma->formatDecimal((($sale->total - $sale->total_discount)) * $commission / 100,4) : 0) : 0,
                'donation'            => isset($sale->donation)? trim($sale->donation):'0',
                'coupon_code'            => isset($sale->coupon_code)? trim($sale->coupon_code) : null,
                'uuid'                => $this->pos2_model->getUniqeUUID()->UUID,
                'is_sub'            => isset($sale->is_sub)? trim($sale->is_sub):'0',

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
					$warehouse_id= $product->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($product->warehouse_code) : $product->warehouse_id;
					$sale_item_id=null;
					$product->product_type="manual";
				}else{
					$product_id=$this->pos2_model->getProductByCode($product->product_code)->id;
					$warehouse_id= $product->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($product->warehouse_code) : $product->warehouse_id;
					$sale_item_id=$sale_id ? $this->pos2_model->get_return_sale_item_id($sale_id,$product_id,$warehouse_id)->id : null;
				}

			$product1 = [
                        'product_id'        => $product_id,
                        'product_name'      => $product->product_name,
                        'product_code'      => $product->product_code,
                        'product_type'            => trim($product->product_type),
						'option_id'            => $product->option_id ? $product->option_id :null,
						'net_unit_price'    => $this->sma->formatDecimal($product->net_unit_price),
                        'unit_price'        => $this->sma->formatDecimal($product->unit_price),
                        'unit_quantity'     => $product->unit_quantity,
						'product_unit_id'     => $product->product_unit_id,
                        'product_unit_code'     => $product->product_unit_code,
                        'quantity'          => $product->quantity,
                        'subtotal'          => $this->sma->formatDecimal($product->subtotal),
						'item_discount'          => $this->sma->formatDecimal($product->item_discount),
                        'warehouse_id'            => $warehouse_id,
						'item_tax'   => $this->sma->formatDecimal($product->item_tax),
                        'tax_rate_id'   => trim($product->tax_rate_id),
                        'tax'   => trim($product->tax),
                        'discount'   => $this->sma->formatDecimal($product->discount),
                        'serial_no'   => trim($product->serial_no),
                        'real_unit_price'   => trim($product->real_unit_price),
						'sale_item_id'   => $sale_item_id,
                        'area'            => isset($product->area) ? $product->area :0,
						'length'            => isset($product->length) ? $product->length :0,
						'width'            => isset($product->width) ? $product->width :0,
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
                        'commercial_discount' =>isset($payment->commercial_discount)? trim($payment->commercial_discount):'0',
                        'commercial_discount_id' =>isset($payment->commercial_discount_id)? trim($payment->commercial_discount_id):NULL,
						'paid_by'      => $payment->paid_by,
                        'cheque_no'      => trim($payment->cheque_no),
                        'cc_no'            => trim($payment->cc_no),
						'cc_holder'            => trim($payment->cc_holder),
						'cc_month'     => trim($payment->cc_month),
						'cc_year'     => trim($payment->cc_year),
                        'cc_type'     => trim($payment->cc_type),
                        'created_by'          => trim($payment->created_by),
                        'type'            => trim($payment->type),
						'note'   => trim($payment->note),
                        'pos_paid'   => $this->sma->formatDecimal($payment->pos_paid),
                        'pos_balance'   => $this->sma->formatDecimal($payment->pos_balance),
						'gl_payment_method_id' =>isset($payment->gl_payment_method_id)? trim($payment->gl_payment_method_id):'0',
                        'donation'            => isset($payment->donation)? trim($payment->donation):'0',



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
				//$this->sma->print_arrays($sale1['Offline_Pos_id']);

			$response["success"] = 1;
			$salse_temp = [
			'Offline_Pos_id'            => trim($sale1['Offline_Pos_id']),
			'status'            => true

			];
            $sale_items2 = [];
			$sale_items = $this->pos2_model->getAllSaleItems($sale2['sale_id']);
			foreach ($sale_items as $item) {

				if ($item->product_type == 'standard' || $item->product_type == 'manufacturing') {

					$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($item->product_id, $item->warehouse_id);

					$sale_items1 = [
						'code'        => $item->product_code,
						'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

					];
                    $sale_items2[] = $sale_items1;

				} elseif ($item->product_type == 'combo') {

					$wh          = null ;
                    $combo_items = $this->pos2_model->getProductComboItems($item->product_id, $wh);

                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard' || $combo_item->type == 'manufacturing') {
							$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($combo_item->id, $item->warehouse_id);
							$sale_items1 = [
								'code'        => $combo_item->code,
								'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

							];
                            $sale_items2[] = $sale_items1;

						}else if ($combo_item->type == 'combo') {
							$combo_items2 = $this->pos2_model->getProductComboItems($combo_item->id, $wh);

							foreach ($combo_items2 as $combo_item2) {


								if ($combo_item2->type == 'standard' || $combo_item2->type == 'manufacturing' ) {
									//return $combo_items2;

									$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($combo_item2->id, $item->warehouse_id);
									$sale_items1 = [
										'code'        => $combo_item2->code,
										'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

							        ];
                                    $sale_items2[] = $sale_items1;
								}
							}
                        }
					}





				}







			}
            $salse_temp['sale_items_balance'] = $sale_items2;
            $flageTrue=1;
			$salse_temp2[]=$salse_temp;



}else{
			$response["success"] = 0;
            $salse_temp = [
                'Offline_Pos_id'            => trim($sale1['Offline_Pos_id']),
                'status'            => false

                ];
                $flageFalse=1;
                $salse_temp2[]=$salse_temp;

			//array_push($failed_sales['sales'],$sale1['Offline_Pos_id']);

		}


			}else{

					if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null)){
				//$this->sma->print_arrays($sale2['sale_id']);


			$response["success"] = 1;
			//array_push($failed_sales['sales'],'1');
			$salse_temp = [
			'Offline_Pos_id'            => trim($sale1['Offline_Pos_id']),
			'status'            => true
			];
            $sale_items2 = [];
			$sale_items = $this->pos2_model->getAllSaleItems($sale2['sale_id']);
			foreach ($sale_items as $item) {

				if ($item->product_type == 'standard' || $item->product_type == 'manufacturing') {

					$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($item->product_id, $item->warehouse_id);

					$sale_items1 = [
						'code'        => $item->product_code,
						'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

					];
                    $sale_items2[] = $sale_items1;

				} elseif ($item->product_type == 'combo') {

					$wh          = null ;
                    $combo_items = $this->pos2_model->getProductComboItems($item->product_id, $wh);

                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard' || $combo_item->type == 'manufacturing') {
							$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($combo_item->id, $item->warehouse_id);
							$sale_items1 = [
								'code'        => $combo_item->code,
								'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

							];
                            $sale_items2[] = $sale_items1;

						}else if ($combo_item->type == 'combo') {
							$combo_items2 = $this->pos2_model->getProductComboItems($combo_item->id, $wh);

							foreach ($combo_items2 as $combo_item2) {


								if ($combo_item2->type == 'standard' || $combo_item2->type == 'manufacturing' ) {
									//return $combo_items2;

									$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($combo_item2->id, $item->warehouse_id);
									$sale_items1 = [
										'code'        => $combo_item2->code,
										'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

							        ];
                                    $sale_items2[] = $sale_items1;
								}
							}
                        }
					}





				}







			}
            $salse_temp['sale_items_balance'] = $sale_items2;
            $flageTrue=1;
			$salse_temp2[]=$salse_temp;


}else{
			$response["success"] = 0;
            $salse_temp = [
                'Offline_Pos_id'            => trim($sale1['Offline_Pos_id']),
                'status'            => false

                ];
                $flageFalse=1;
                $salse_temp2[]=$salse_temp;
		}




			}
		}else{
		$response["success"] = 1;
			$salse_temp = [
			'Offline_Pos_id'            => trim($sale->Offline_Pos_id),
			'status'            => true
			];
            $sale_items2 = [];
			$sale_items = $this->pos2_model->getAllSaleItems($sync->id);
			foreach ($sale_items as $item) {

				if ($item->product_type == 'standard' || $item->product_type == 'manufacturing') {

					$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($item->product_id, $item->warehouse_id);

					$sale_items1 = [
						'code'        => $item->product_code,
						'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

					];
                    $sale_items2[] = $sale_items1;
				} elseif ($item->product_type == 'combo') {

					$wh          = null ;
                    $combo_items = $this->pos2_model->getProductComboItems($item->product_id, $wh);

                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard' || $combo_item->type == 'manufacturing') {
							$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($combo_item->id, $item->warehouse_id);
							$sale_items1 = [
								'code'        => $combo_item->code,
								'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

							];
                            $sale_items2[] = $sale_items1;
						}else if ($combo_item->type == 'combo') {
							$combo_items2 = $this->pos2_model->getProductComboItems($combo_item->id, $wh);

							foreach ($combo_items2 as $combo_item2) {


								if ($combo_item2->type == 'standard' || $combo_item2->type == 'manufacturing' ) {
									//return $combo_items2;

									$wh_balance_qty = $this->pos2_model->getBalanceQuantityV2($combo_item2->id, $item->warehouse_id);
									$sale_items1 = [
										'code'        => $combo_item2->code,
										'quantity'    => $this->sma->formatDecimal($wh_balance_qty),

							        ];
                                    $sale_items2[] = $sale_items1;
								}
							}
                        }
					}





				}







			}

            $salse_temp['sale_items_balance'] = $sale_items2;
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
