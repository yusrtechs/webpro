<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SalesSet2 extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('users_api');
        
		$this->load->admin_model('pos2_model');
		$this->load->admin_model('site');
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
		/*if($this->pos2_model->addSale2(json_encode($json)))
		{
			
		}*/
		$sales =json_decode($json);
		//$sales=$sales->JSON3;
		
		//if($this->pos2_model->addSale2(json_encode($sales)))
		{
			
		}
		//$this->sma->print_arrays(json_decode("{\r\n  \"limit\": \"1\",\r\n  \"start\": \"1\",\r\n  \"end\": \"5\",\r\n  \"customer\": [],\r\n  \"category\": [],\r\n  \"pos_register\": [],\r\n  \"sales\": [\r\n    {\r\n      \"Offline_Pos_id\": 3,\r\n      \"date\": \"2021-12-09T19:51:58+03:00\",\r\n      \"reference_no\": \"SALE3\",\r\n      \"customer_id\": 2,\r\n      \"due_date\": \"2021-09-19T00:00:00\",\r\n      \"hash\": \"99cb9e750fc468302c959bdd513284fa446ff6d64bcb86103715e955b1de0044\",\r\n      \"customer\": \"Walk Inn\",\r\n      \"biller\": \"cashier1\",\r\n      \"biller_id\": 2,\r\n      \"total\": 513.0435,\r\n      \"return_sale_ref\": null,\r\n      \"return_id\": null,\r\n      \"sale_status\": \"completed\",\r\n      \"sale_id\": null,\r\n      \"payment_status\": \"paid\",\r\n      \"warehouse_code\": \"102\",\r\n      \"warehouse_id\": 1,\r\n      \"grand_total\": 590.0000,\r\n      \"pos\": true,\r\n      \"surcharge\": 0.0000,\r\n      \"return_sale_total\": 0.0000,\r\n      \"paid\": 590.0000,\r\n      \"total_tax\": 76.9565,\r\n      \"note\": null,\r\n      \"staff_note\": null,\r\n      \"product_discount\": null,\r\n      \"order_discount_id\": null,\r\n      \"order_discount\": 0.0000,\r\n      \"total_discount\": 0.0000,\r\n      \"product_tax\": null,\r\n      \"order_tax_id\": null,\r\n      \"order_tax\": 76.9565,\r\n      \"total_tax\": 76.9565,\r\n      \"shipping\": 0.0000,\r\n      \"total_items\": 5,\r\n      \"sale_status\": \"completed\",\r\n      \"payment_term\": null,\r\n      \"rounding\": null,\r\n      \"created_by\": 2,\r\n      \"order_type\": 1,\r\n      \"table_no\": 0,\r\n      \"dept\": 0,\r\n      \"emp_id\": null,\r\n      \"emp_type\": null,\r\n      \"payment\": [\r\n        {\r\n          \"date\": \"2021-09-19T19:51:57+03:00\",\r\n          \"amount\": 590.0000,\r\n          \"paid_by\": \"cash\",\r\n          \"cheque_no\": null,\r\n          \"cc_no\": null,\r\n          \"cc_holder\": null,\r\n          \"cc_month\": null,\r\n          \"cc_year\": null,\r\n          \"cc_type\": null,\r\n          \"created_by\": 2,\r\n          \"type\": \"received\",\r\n          \"note\": null,\r\n          \"pos_paid\": 590.0000,\r\n          \"pos_balance\": 0.0000\r\n        }\r\n      ],\r\n      \"items\": [\r\n        {\r\n          \"product_id\": 3971,\r\n          \"product_name\": \"\u0644\u0644\u0641\u062a\u064a\u0627\u062a \u0627\u0644\u062c\u0645\u064a\u0644\u0627\u062a\",\r\n          \"product_code\": \"10001002\",\r\n          \"product_type\": \"standard\",\r\n          \"option_id\": null,\r\n          \"net_unit_price\": 102.6087,\r\n          \"unit_price\": 102.6087,\r\n          \"unit_quantity\": 5.0000,\r\n          \"real_unit_price\": 118.0000,\r\n          \"product_unit_id\": 1,\r\n          \"product_unit_code\": \"Pices\",\r\n          \"quantity\": 5.0000,\r\n          \"subtotal\": 590.0000,\r\n          \"warehouse_id\": 1,\r\n          \"warehouse_code\": \"102\",\r\n          \"item_tax\": 76.9565,\r\n          \"tax_rate_id\": 5,\r\n          \"tax\": \"\u0636\u0631\u064a\u0628\u0629 \u0627\u0644\u0646\u0633\u0628\u0629 \u0627\u0644\u0623\u0633\u0627\u0633\u064a\u0629 15%\",\r\n          \"discount\": null,\r\n          \"serial_no\": \"lulu caty\",\r\n          \"real_unit_price\": 118.0000,\r\n          \"item_discount\": 0.0000,\r\n          \"promo_free\": false\r\n        }\r\n      ]\r\n    },\r\n    {\r\n      \"Offline_Pos_id\": 4,\r\n      \"date\": \"2021-09-19T19:52:10+03:00\",\r\n      \"reference_no\": \"SALE4\",\r\n      \"customer_id\": 2,\r\n      \"due_date\": \"2021-09-19T00:00:00\",\r\n      \"hash\": \"8b40aa3e569c8cbb8ce2a2d861b73632fd46b8403e711160f88d010b3bb8e673\",\r\n      \"customer\": \"Walk Inn\",\r\n      \"biller\": \"cashier1\",\r\n      \"biller_id\": 2,\r\n      \"total\": 102.6087,\r\n      \"return_sale_ref\": null,\r\n      \"return_id\": null,\r\n      \"sale_status\": \"completed\",\r\n      \"sale_id\": null,\r\n      \"payment_status\": \"paid\",\r\n      \"warehouse_code\": \"102\",\r\n      \"warehouse_id\": 1,\r\n      \"grand_total\": 118.0000,\r\n      \"pos\": true,\r\n      \"surcharge\": 0.0000,\r\n      \"return_sale_total\": 0.0000,\r\n      \"paid\": 118.0000,\r\n      \"total_tax\": 15.3913,\r\n      \"note\": null,\r\n      \"staff_note\": null,\r\n      \"product_discount\": null,\r\n      \"order_discount_id\": null,\r\n      \"order_discount\": 0.0000,\r\n      \"total_discount\": 0.0000,\r\n      \"product_tax\": null,\r\n      \"order_tax_id\": null,\r\n      \"order_tax\": 15.3913,\r\n      \"total_tax\": 15.3913,\r\n      \"shipping\": 0.0000,\r\n      \"total_items\": 1,\r\n      \"sale_status\": \"completed\",\r\n      \"payment_term\": null,\r\n      \"rounding\": null,\r\n      \"created_by\": 2,\r\n      \"order_type\": 1,\r\n      \"table_no\": 0,\r\n      \"dept\": 0,\r\n      \"emp_id\": null,\r\n      \"emp_type\": null,\r\n      \"payment\": [\r\n        {\r\n          \"date\": \"2021-09-19T19:52:10+03:00\",\r\n          \"amount\": 118.0000,\r\n          \"paid_by\": \"cash\",\r\n          \"cheque_no\": null,\r\n          \"cc_no\": null,\r\n          \"cc_holder\": null,\r\n          \"cc_month\": null,\r\n          \"cc_year\": null,\r\n          \"cc_type\": null,\r\n          \"created_by\": 2,\r\n          \"type\": \"received\",\r\n          \"note\": null,\r\n          \"pos_paid\": 118.0000,\r\n          \"pos_balance\": 0.0000\r\n        }\r\n      ],\r\n      \"items\": [\r\n        {\r\n          \"product_id\": 3971,\r\n          \"product_name\": \"\u0644\u0644\u0641\u062a\u064a\u0627\u062a \u0627\u0644\u062c\u0645\u064a\u0644\u0627\u062a\",\r\n          \"product_code\": \"10001002\",\r\n          \"product_type\": \"standard\",\r\n          \"option_id\": null,\r\n          \"net_unit_price\": 102.6087,\r\n          \"unit_price\": 102.6087,\r\n          \"unit_quantity\": 1.0000,\r\n          \"real_unit_price\": 118.0000,\r\n          \"product_unit_id\": 1,\r\n          \"product_unit_code\": \"Pices\",\r\n          \"quantity\": 1.0000,\r\n          \"subtotal\": 118.0000,\r\n          \"warehouse_id\": 1,\r\n          \"warehouse_code\": \"102\",\r\n          \"item_tax\": 15.3913,\r\n          \"tax_rate_id\": 5,\r\n          \"tax\": \"\u0636\u0631\u064a\u0628\u0629 \u0627\u0644\u0646\u0633\u0628\u0629 \u0627\u0644\u0623\u0633\u0627\u0633\u064a\u0629 15%\",\r\n          \"discount\": null,\r\n          \"serial_no\": \"lulu caty\",\r\n          \"real_unit_price\": 118.0000,\r\n          \"item_discount\": 0.0000,\r\n          \"promo_free\": false\r\n        }\r\n      ]\r\n    }\r\n  ],\r\n  \"expenses\": []\r\n}"));
		//$this->sma->print_arrays($sales);
		
		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);
		
		$response["success"] = 0;
		
        //
		//$sales=krsort($sales); 
        //$sales=(array) $sales;
			
		$expenses=$sales->expenses;
        $customer=$sales->customer;
        $pos_register=$sales->pos_register;
        $sales=$sales->sales;
        //$this->sma->print_arrays($sales);	
        $failed_sales = [];		
		foreach ($sales as $sale) {
			
			 //
			 
			 $warehouse_id= $sale->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sale->warehouse_code) : $sale->warehouse_id;
			 $sale_id=$sale->sale_id ? $this->pos2_model->get_return_sale_id(trim($sale->sale_id),$warehouse_id)->id : null;
			 //$sale->biller='كاشير';
			 $biller_id= $sale->biller ? $this->users_api->getBillerIdByUserName($sale->biller) : $sale->biller_id;
			 
			 //$this->sma->print_arrays($this->pos2_model->getInvoiceBy_date_reference_no_biller_id(trim($sale->date),trim($sale->reference_no),$biller_id)); 
			 
			 if($this->pos2_model->getInvoiceBy_date_reference_no_biller_id(trim($sale->date),trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id))==0){
			 
			
			$sale1           = [
				'date' => trim($sale->date),
                'reference_no'                        => trim($sale->reference_no),
                'customer_id'                 => trim($sale->customer_id),
                'customer'                    => trim($sale->customer),
                'biller'                        => trim($sale->biller),
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
				'supply_date'          => date('Y-m-d H:i:s'),
				'emp_type'            => trim($sale->emp_type),
                'emp_id'            => trim($sale->emp_id),
                'dept'            => trim($sale->dept),
                'table_no'            => trim($sale->table_no),
                
				
                
                
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
                        
                    ];
					//krsort($product1);
					//$this->sma->print_arrays($product);
					$product2[] = $product1;
					
					
					if($sale_id){
					$si_return = [
                        'id'           => $sale_item_id,
                        'sale_id'      => $id,
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
                        
                    ];
					
					$payment2[] = $payment1;

					}
					//$payment2[] = $payments;
					//$this->sma->print_arrays($payments);
			
					//krsort($product1);
			//$this->sma->print_arrays($payment2);
			//$this->sma->print_arrays($sale1, $products, null, null);
			/*if (empty($si_return2)) {
            $cost = $this->pos2_model->costing($product2);
             $this->sma->print_arrays($cost);
        }*/
			//$this->sma->print_arrays($sale1,$product2, $payment2, null,$si_return2);
			
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
			
		}
		
		}
		
		
		foreach ($expenses as $expense) {
			
			$warehouse_id= $expense->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($expense->warehouse_code) : $expense->warehouse_id;
			
			
			$expense1           = [
				'warehouse_id'            => $warehouse_id,
                'date'         => trim($expense->date),
                'reference'    => trim($expense->reference),
                'amount'       => $this->sma->formatDecimal($expense->amount),
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
			if($this->purchases_model->addExpense($expense1)){
			$expenses_temp = [
			'id'            => trim($expense1['Offline_id'])
			];
			
			$expenses_temp2[]=$expenses_temp;
			
			}else{
				//array_push($failed_sales['expenses'],$expense1['Offline_id']);
			

			}
		}
		
		
		foreach ($customer as $cust) {
			
			$customer1           = [
				'group_id'            => trim($cust->group_id),
                'group_name'         => trim($cust->group_name),
                'name'    => trim($cust->name),
                'company'       => trim($cust->company),
                'email'       => trim($cust->email),
                'customer_group_id'       => trim($cust->customer_group_id),
				'customer_group_name'       => trim($cust->customer_group_name),
				'vat_no'   => trim($cust->vat_no),
                'address' => trim($cust->address),
                'state' => trim($cust->state),
                'postal_code' => trim($cust->postal_code),
                'country' => trim($cust->country),
                'phone' => trim($cust->phone),
                'co_type' => 1,
                'api' => 1,
                'Offline_id' => trim($cust->id),
                
                
            ];
			//krsort($cust1);
			if($this->companies_model->addCompany($customer1)){
			$customer_temp = [
			'id'            => trim($customer1['Offline_id'])
			];
			
			$customer_temp2[]=$customer_temp;
			
			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);
			

			}
		}
		
		foreach ($pos_register as $pos_reg) {
			$pos_register1 = [];
			//$warehouse_id= $expense->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($expense->warehouse_code) : $expense->warehouse_id;
			if(trim($pos_reg->status)=="open"){
			
			$pos_register1          = [
				'date'         => trim($pos_reg->date),
                'cash_in_hand'       => $this->sma->formatDecimal($pos_reg->cash_in_hand),
                'user_id'   => trim($pos_reg->user_id),
                'status' => trim($pos_reg->status),
                'Offline_id' => trim($pos_reg->id),
                
                
            ];
			//krsort($expense1);
			if($this->pos2_model->openRegister($pos_register1)){
			$pos_register_temp = [
			'id'            => trim($pos_register1['Offline_id'])
			];
			
			$pos_register_temp2[]=$pos_register_temp;
			
			}else{
				//array_push($failed_sales['expenses'],$expense1['Offline_id']);
			

			}
			
			
			
		}else if(trim($pos_reg->status)=="close"){
			
			
			    $user_id=trim($pos_reg->user_id);
				$user_register = $this->pos2_model->registerData($user_id);
                $rid           = $user_register->id;
                $user_id       = $user_register->user_id;
			
			$pos_register1          = [
				'closed_at'         => trim($pos_reg->closed_at),
                'total_cash'       => $this->sma->formatDecimal($pos_reg->total_cash),
                'total_cheques'   => trim($pos_reg->total_cheques),
                'total_cc_slips' => trim($pos_reg->total_cc_slips),
                'total_cash_submitted' => trim($pos_reg->total_cash_submitted),
                'total_cheques_submitted' => trim($pos_reg->total_cheques_submitted),
                'total_cc_slips_submitted' => trim($pos_reg->total_cc_slips_submitted),
                'note' => trim($pos_reg->note),
                'status' => trim($pos_reg->status),
                'transfer_opened_bills' => trim($pos_reg->transfer_opened_bills),
                'closed_by' => trim($pos_reg->closed_by),
                'Offline_id' => trim($pos_reg->id),
                
                
            ];
			//krsort($expense1);
			if($this->pos2_model->closeRegister($rid, $user_id, $pos_register1)){
			$pos_register_temp = [
			'id'            => trim($pos_register1['Offline_id'])
			];
			
			$pos_register_temp2[]=$pos_register_temp;
			
			}else{
				//array_push($failed_sales['expenses'],$expense1['Offline_id']);
			

			}
			
			
		}
			
			
		}
		
		 $failed_sales['s_sales']= $this->setArr($salse_temp2);
		 $failed_sales['s_expenses']= $this->setArr($expenses_temp2);
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
