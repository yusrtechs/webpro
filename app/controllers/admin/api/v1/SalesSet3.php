<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SalesSet3 extends REST_Controller
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

		$products=$sales->product;
		$expenses=$sales->expenses;
        $payments_only=$sales->payment;
        $customer=$sales->customer;
        $pos_register=$sales->pos_register;
        $sales=$sales->sales;
        //$this->sma->print_arrays($sales);
        $failed_sales = [];

		foreach ($products as $product) {
			$slug=trim($product->name);
			$slug= str_replace(" ","-",$slug);

			$product1           = [
				'type'            => 'standard',
				'type2'            => 'primary',
				'quantity'            => '0',
				'cost'            => '0',
				'warehouse'            => trim($product->warehouse),
				'name'            => trim($product->name),
				'slug'            => isset($product->slug)? trim($product->slug):trim($slug),
				'code'            => trim($product->code),
				'tax_rate'            => trim($product->tax_rate),
				'tax_method'            => trim($product->tax_method),
				'category_id'            => trim($product->category_id),
				'unit'            => trim($product->unit),
				'sale_unit'            => trim($product->unit),
				'purchase_unit'            => trim($product->unit),
				'price'            => trim($product->price),
                'item_no'            => isset($product->item_no)? trim($product->item_no) : null,


            ];
			//krsort($cust1);
			if($this->pos2_model->addAjaxProductFromSales($product1)){
			$product_temp = [
			'code'            => trim($product1['code'])
			];

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);


			}
		}


		foreach ($customer as $cust) {

			$customer1           = [
				'group_id'            => 3,
                'group_name'         => "customer",
                'name'    => trim($cust->company),
                'company'       => trim($cust->company),
                'email'       => trim($cust->email),
                'customer_group_id'       => trim($cust->customer_group_id),
				'customer_group_name'       => trim($cust->customer_group_name),
				'vat_no'   => trim($cust->vat_no),
				'cr_no'   => trim($cust->cr_no),
                'address' => trim($cust->address),
                'state' => trim($cust->state),
                'postal_code' => trim($cust->postal_code),
                'country' => trim($cust->country),
                'phone' => trim($cust->phone),
                'co_type' => 1,
                'api' => 0,
                'Offline_id' => trim($cust->id),


            ];
			//krsort($cust1);
			if($customer = $this->companies_model->addCompany($customer1)){
			$customer_temp = [
			'online_id'            => trim($customer)
			];

			$customer_temp2[]=$customer_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);


			}
		}

		foreach ($sales as $sale) {

			 //$this->sma->print_arrays($sale);

			 $warehouse_id= $sale->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sale->warehouse_code) : $sale->warehouse_id;
			 //$sale->biller='كاشير';
			 $biller_id= $sale->biller ? $this->users_api->getBillerIdByUserName($sale->biller) : $sale->biller_id;
			 $sale_id=$sale->sale_id ? $this->pos2_model->get_return_sale_id(trim($sale->sale_id),$warehouse_id,$biller_id)->id : null;
			 $biller= $this->companies_model->getCompanyByID($biller_id)->company ? $this->companies_model->getCompanyByID($biller_id)->company : trim($sale->biller);

			 //$customer_id= $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id;
			 $customer_id=  $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id : $sale->customer_id;

			//if($this->pos2_model->getInvoiceBy_date_reference_no_biller_id(trim($sale->date),trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id))==0){
			//if($this->pos2_model->getInvoiceBy_reference_no_biller_id(trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id))==0){

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
                'pos'            => trim($sale->pos),
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
                'uuid'                => $this->pos2_model->getUniqeUUID()->UUID,




            ];
			krsort($sale1);

			$products=  $sale->items;
			//$products=(array) $products;
			$product2 = [];

			$si_return2 = [];

			foreach ($products as $product) {
			$product_id=$this->pos2_model->getProductByCode($product->product_code)->id;
			$warehouse_id= $product->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($product->warehouse_code) : $product->warehouse_id;
			$sale_item_id=$sale_id ? $this->pos2_model->get_return_sale_item_id($sale_id,$product_id,$warehouse_id)->id : null;

			$product1 = [
                        'product_id'        => $product_id,
                        'product_name'      => $product->product_name,
                        'product_code'      => $product->product_code,
                        'product_type'            => trim($product->product_type),
						'option_id'            => null,
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

                    ];
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


                    ];

					$payment2[] = $payment1;

					}
					//$payment2[] = $payments;
					//$this->sma->print_arrays($payments);

					//krsort($product1);
			//$this->sma->print_arrays($payment2);
			//$this->sma->print_arrays($sale1, $products, null, null);
			//$this->sma->print_arrays($sale1,$product2, $payment2, null,$si_return2);



			$Measurements=  $sale->Measurement;
			//$payments=(array) $payments;
			$Measurement2 = [];

			foreach ($Measurements as $Measurement) {

			$Measurement1 = [
                        'sale_id'        => $Measurement->sale_id,
                        'Item_Code'        => $Measurement->Item_Code,
                        'Item_Name'        => $Measurement->Item_Name,
                        'Front_Length'        => $Measurement->Front_Length,
                        'Back_Length'        => $Measurement->Back_Length,
                        'Shoulder_Width'        => $Measurement->Shoulder_Width,
                        'Shoulder_Slope'        => $Measurement->Shoulder_Slope,
                        'Sleeve'        => $Measurement->Sleeve,
                        'Sleeve_Top'        => $Measurement->Sleeve_Top,
                        'Wrist'        => $Measurement->Wrist,
                        'Plain_Cuff_Length'        => $Measurement->Plain_Cuff_Length,
                        'Cuff_Length'        => $Measurement->Cuff_Length,
                        'Cuff_Width'        => $Measurement->Cuff_Width,
                        'Middle_Width'        => $Measurement->Middle_Width,
                        'Chest_Front'        => $Measurement->Chest_Front,
                        'Chest_Back'        => $Measurement->Chest_Back,
                        'Bottom_Height'        => $Measurement->Bottom_Height,
                        'Bottom_Width'        => $Measurement->Bottom_Width,
                        'Collar_Width'        => $Measurement->Collar_Width,
                        'Collar_Height'        => $Measurement->Collar_Height,
                        'Zipper_Height'        => $Measurement->Zipper_Height,
                        'Zipper_Width'        => $Measurement->Zipper_Width,
                        'Chest_Pocket_Height'        => $Measurement->Chest_Pocket_Height,
                        'Chest_Pocket_Width'        => $Measurement->Chest_Pocket_Width,
                        'Mobile_Pocket_Height'        => $Measurement->Mobile_Pocket_Height,
                        'Wallet_Pocket_Height'        => $Measurement->Wallet_Pocket_Height,
                        'Wallet_Pocket_Width'        => $Measurement->Wallet_Pocket_Width,
                        'Haunch_Width'        => $Measurement->Haunch_Width,
                        'Button_No'        => $Measurement->Button_No,
                        'Embroidery_No'        => $Measurement->Embroidery_No,
                        'Estimated_Length'        => $Measurement->Estimated_Length,
                        'Tailor_id'        => $Measurement->Tailor_id,
                        'sample'        => $Measurement->sample,
                        'urgent'        => $Measurement->urgent,
                        'shoulder_chest_length'        => $Measurement->shoulder_chest_length,
                        'sleeve_middle'        => $Measurement->sleeve_middle,
                        'Side_Pocket_Length'        => $Measurement->Side_Pocket_Length,
                        'takhalees'        => $Measurement->takhalees,
                        'Collar_Type_ID'        => $Measurement->Collar_Type_ID,
                        'Cuff_Type_ID'        => $Measurement->Cuff_Type_ID,
                        'Model_Type_ID'        => $Measurement->Model_Type_ID,
                        'Pocket_Type_ID'        => $Measurement->Pocket_Type_ID,
                        'Filling_Type_ID'        => $Measurement->Filling_Type_ID,
                        'Note'        => $Measurement->Note,


                    ];
					krsort($Measurement1);


					$Measurement2[] = $Measurement1;

					}

			//$this->sma->print_arrays($Measurement1);

		if($sale_id){

		if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null,$si_return2)){
				//$this->sma->print_arrays($sale1['Offline_Pos_id']);

			$response["success"] = 1;
			$salse_temp = [
			'id'            => trim($sale1['Offline_Pos_id']),
			'hash'            => trim($sale1['hash']),
			'online_id'            => trim($sale2['sale_id'])

			];

			$salse_temp2[]=$salse_temp;



}else{
			$response["success"] = 0;
			//array_push($failed_sales['sales'],$sale1['Offline_Pos_id']);

		}


			}else{

					if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null)){
						$Measurement1['sale_id']=$sale2['sale_id'];
						$this->pos2_model->addSaleTailor($Measurement1);
				//$this->sma->print_arrays($sale2['sale_id']);


			$response["success"] = 1;
			//array_push($failed_sales['sales'],'1');
			$salse_temp = [
			'id'            => trim($sale1['Offline_Pos_id']),
			'hash'            => trim($sale1['hash']),
			'online_id'            => trim($sale2['sale_id'])
			];

			$salse_temp2[]=$salse_temp;


}else{
			$response["success"] = 0;
			//array_push($failed_sales['sales'],$sale1['Offline_Pos_id']);


		}




			}
		/*}else{
		$response["success"] = 1;
			$salse_temp = [
			'id'            => trim($sale->Offline_Pos_id),
			'hash'            => trim($sale->hash),
			'online_id'            => ''
			];

			$salse_temp2[]=$salse_temp;

		}*/
		}

		//$this->sma->print_arrays($payments_only);

		foreach ($payments_only as $payment) {

			$sale_id=$payment->sale_id ? $this->pos2_model->get_return_sale_id_for_payment(trim($payment->sale_id),$payment->created_by)->id : null;

			if($this->pos2_model->getPaymentsBy_date_reference_no(trim($payment->date),trim($payment->reference_no),trim($payment->sale_id))==0){


			$payment3 = [
                        'sale_id'        => $sale_id,
                        'reference_no'        => $payment->reference_no,
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
						'note'   => trim($payment->id),
                        'pos_paid'   => $this->sma->formatDecimal($payment->pos_paid),
                        'pos_balance'   => $this->sma->formatDecimal($payment->pos_balance),
						'gl_payment_method_id' =>isset($payment->gl_payment_method_id)? trim($payment->gl_payment_method_id):'0',


                    ];

			//krsort($expense1);
			if($this->pos2_model->addPayment($payment3)){
			$payment3_temp = [
			'id'            => trim($payment3['note'])
			];

			$payment3_temp2[]=$payment3_temp;

			}else{
				//array_push($failed_sales['expenses'],$expense1['Offline_id']);


			}
		}
		}


		foreach ($expenses as $expense) {

			$warehouse_id= $expense->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($expense->warehouse_code) : $expense->warehouse_id;

			if($this->pos2_model->getExpensesBy_date_reference_no(trim($expense->date),trim($expense->reference),trim($expense->id))==0){


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
                'Offline_id' => trim($expense->id),


            ];
			//krsort($expense1);
			if($this->pos2_model->addExpense($expense1)){
			$expenses_temp = [
			'id'            => trim($expense1['Offline_id'])
			];

			$expenses_temp2[]=$expenses_temp;

			}else{
				//array_push($failed_sales['expenses'],$expense1['Offline_id']);


			}
		}
		}



		foreach ($pos_register as $pos_reg) {
			//$this->sma->print_arrays($this->pos2_model->getPosRegisterBy_date_closed_at_user_id_Offline_id($pos_reg->date,$pos_reg->closed_at,$pos_reg->user_id,$pos_reg->id));
			if($this->pos2_model->getPosRegisterBy_date_closed_at_user_id_Offline_id($pos_reg->date,$pos_reg->closed_at,$pos_reg->user_id,$pos_reg->id) == 0){

			if(trim($pos_reg->status)=="close"){



			/*
			if($this->pos2_model->openRegister($pos_register1)){
			$pos_register_temp = [
			'id'            => trim($pos_register1['Offline_id'])
			];

			$pos_register_temp2[]=$pos_register_temp;

			}else{
				//array_push($failed_sales['expenses'],$expense1['Offline_id']);


			}

			*/




			   /* $user_id=trim($pos_reg->user_id);
				$register = $this->pos2_model->registerData($user_id);
                $rid           = $register->id;
                $user_id       = $register->user_id;
			*/
			if(trim($pos_reg->user_id)){
				$pos_register_open         = [
				'date'         => trim($pos_reg->date),
                'cash_in_hand'       => $this->sma->formatDecimal($pos_reg->cash_in_hand),
                'user_id'   => trim($pos_reg->user_id),
                'status' => 'open',
                'Offline_id' => trim($pos_reg->id),
                'cash_in_hand_clearance' =>isset($pos_reg->cash_in_hand_clearance)? trim($pos_reg->cash_in_hand_clearance):'0',


            ];

		//$this->sma->print_arrays($user_id);
			$pos_register_close          = [
				'closed_at'         => trim($pos_reg->closed_at),
                'total_cash'       => $this->sma->formatDecimal($pos_reg->total_cash),
                'total_cheques'   => trim($pos_reg->total_cheques),
                'total_cc_slips' =>isset($pos_reg->total_cc)? $this->sma->formatDecimal($pos_reg->total_cc):'0',
                'total_cash_submitted' => $this->sma->formatDecimal($pos_reg->total_cash_submitted),
                'total_cheques_submitted' => trim($pos_reg->total_cheques_submitted),
                'total_cc_slips_submitted' =>isset($pos_reg->total_cc)? $this->sma->formatDecimal($pos_reg->total_cc):'0',
                'total_cc' =>isset($pos_reg->total_cc)? $this->sma->formatDecimal($pos_reg->total_cc):'0',
                'invoice_count' =>isset($pos_reg->invoice_count)? trim($pos_reg->invoice_count):'0',
                'note' => trim($pos_reg->note),
                'status' => trim($pos_reg->status),
                'transfer_opened_bills' => trim($pos_reg->transfer_opened_bills),
                'closed_by' => trim($pos_reg->closed_by),
                'Offline_id' => trim($pos_reg->id),
                //'user_id' => trim($user_id),
                'user_id' => trim($pos_reg->user_id),


            ];
			//krsort($expense1);

			//$this->sma->print_arrays($this->pos2_model->getToatalSalesPaymentPOSCC($this->pos2_model->getUser($pos_register_close['user_id'])->biller_id,'completed','CC',$pos_register_open['date'],$pos_register_close['closed_at']));
			//$this->sma->print_arrays($this->db->database);

			if($this->pos2_model->closePOSRegisterV2( $pos_register_open, $pos_register_close,str_replace("cpesoft_","",$this->db->database))){
			$pos_register_temp = [
			'id'            => trim($pos_register_close['Offline_id'])
			];

			$pos_register_temp2[]=$pos_register_temp;

			}else{
				//array_push($failed_sales['expenses'],$expense1['Offline_id']);


			}
		}


		}

		}
		}

		 $failed_sales['s_sales']= $this->setArr($salse_temp2);
		 $failed_sales['s_expenses']= $this->setArr($expenses_temp2);
		 $failed_sales['s_payment']= $this->setArr($payment3_temp2);
		 $failed_sales['s_product']= $this->setArr($product_temp2);
		 $failed_sales['s_customer']= $this->setArr($customer_temp2);
		 $failed_sales['s_pos_register']= $this->setArr($pos_register_temp2);

	//$this->set_response(['success'  =>$payment2], REST_Controller::HTTP_OK);
	$this->set_response($failed_sales, REST_Controller::HTTP_OK);

		/*
		$reference = $this->get('reference');

        $filters = [
            'reference'   => $reference,
            'include'     => $this->get('include') ? explode(',', $this->get('include')) : null,
            'start'       => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'       => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 10,
            'start_date'  => $this->get('start_date') && is_numeric($this->get('start_date')) ? $this->get('start_date') : null,
            'end_date'    => $this->get('end_date') && is_numeric($this->get('end_date')) ? $this->get('end_date') : null,
            'order_by'    => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'decs'],
            'customer_id' => $this->get('customer_id') ? $this->get('customer_id') : null,
            'customer'    => $this->get('customer') ? $this->get('customer') : null,
        ];

        if ($reference === null) {
            if ($sales = $this->sales_api->getSales($filters)) {
                $sl_data = [];
                foreach ($sales as $sale) {
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

                    //$sale->created_by = $this->sales_api->getUser($sale->created_by);
                    $sl_data[]        = $this->setSale($sale);
                }

                $data = [
                    'data'  => $sl_data,
                    'limit' => (int) $filters['limit'],
                    'start' => (int) $filters['start'],
                    'total' => $this->sales_api->countSales($filters),
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No sale record found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
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
            }
        }
		*/
    }
}
