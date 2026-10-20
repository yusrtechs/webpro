<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('max_execution_time', -1);

class Purchases extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->lang->admin_load('purchases', $this->Settings->user_language);
        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('purchases_model');
        $this->load->admin_model('reports_model');
		$this->load->admin_model('companies_model');
		$this->load->admin_model('db_model');
		$this->load->admin_model('accounts_model');
		$this->load->admin_model('manufacturing_model');
        $this->load->admin_model('products_model');


		$this->data['pb'] = [
            'cash'       => lang('cash'),
            'CC'         => lang('CC'),
            'Cheque'     => lang('Cheque'),
            'paypal_pro' => lang('paypal_pro'),
            'stripe'     => lang('stripe'),
            'gift_card'  => lang('gift_card'),
            'deposit'    => lang('deposit'),
            'authorize'  => lang('authorize'),
        ];


        $this->digital_upload_path = 'files/';
        $this->upload_path         = 'assets/uploads/';
        $this->thumbs_path         = 'assets/uploads/thumbs/';
        $this->image_types         = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types  = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size   = '1024';
        $this->data['logo']        = true;
    }

    /* -------------------------------------------------------------------------------------------------------------------------------- */

    public function add($quote_id = null)
    {
        $this->sma->checkPermissions();

        $purchase_id = $this->input->get('purchase_id') ? $this->input->get('purchase_id') : null;

        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('warehouse', $this->lang->line('warehouse'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line('supplier'), 'required');
        //$this->form_validation->set_rules('payment_status', lang('payment_status'), 'required');
        $this->form_validation->set_rules('status', lang('status'), 'required');

        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {


			$jsonArr_product_id=json_decode($_POST['jsonArr_product_id']);
			$jsonArr_product_id= (array) $jsonArr_product_id;

			$product_id_arr = [];
			foreach ($jsonArr_product_id as $product_arr) {
				$product_id_arr[]=  $product_arr->value;

			}

			$jsonArr_product=json_decode($_POST['jsonArr_product']);
			$jsonArr_product= (array) $jsonArr_product;

			$product_arr = [];
			foreach ($jsonArr_product as $pr_arr) {
				$product_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_name=json_decode($_POST['jsonArr_product_name']);
			$jsonArr_product_name= (array) $jsonArr_product_name;

			$product_name_arr = [];
			foreach ($jsonArr_product_name as $pr_arr) {
				$product_name_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_option=json_decode($_POST['jsonArr_product_option']);
			$jsonArr_product_option= (array) $jsonArr_product_option;

			$product_option_arr = [];
			foreach ($jsonArr_product_option as $pr_arr) {
				$product_option_arr[]=  $pr_arr->value;

			}

			$jsonArr_part_no=json_decode($_POST['jsonArr_part_no']);
			$jsonArr_part_no= (array) $jsonArr_part_no;

			$part_no_arr = [];
			foreach ($jsonArr_part_no as $pr_arr) {
				$part_no_arr[]=  $pr_arr->value;

			}

			$jsonArr_expiry=json_decode($_POST['jsonArr_expiry']);
			$jsonArr_expiry= (array) $jsonArr_expiry;

			$expiry_arr = [];
			foreach ($jsonArr_expiry as $pr_arr) {
				$expiry_arr[]=  $pr_arr->value;

			}

			$jsonArr_net_cost=json_decode($_POST['jsonArr_net_cost']);
			$jsonArr_net_cost= (array) $jsonArr_net_cost;

			$net_cost_arr = [];
			foreach ($jsonArr_net_cost as $pr_arr) {
				$net_cost_arr[]=  $pr_arr->value;

			}

			$jsonArr_unit_cost=json_decode($_POST['jsonArr_unit_cost']);
			$jsonArr_unit_cost= (array) $jsonArr_unit_cost;

			$unit_cost_arr = [];
			foreach ($jsonArr_unit_cost as $pr_arr) {
				$unit_cost_arr[]=  $pr_arr->value;

			}

			$jsonArr_real_unit_cost=json_decode($_POST['jsonArr_real_unit_cost']);
			$jsonArr_real_unit_cost= (array) $jsonArr_real_unit_cost;

			$real_unit_cost_arr = [];
			foreach ($jsonArr_real_unit_cost as $pr_arr) {
				$real_unit_cost_arr[]=  $pr_arr->value;

			}

			$jsonArr_quantity_balance=json_decode($_POST['jsonArr_quantity_balance']);
			$jsonArr_quantity_balance= (array) $jsonArr_quantity_balance;

			$quantity_balance_arr = [];
			foreach ($jsonArr_quantity_balance as $pr_arr) {
				$quantity_balance_arr[]=  $pr_arr->value;

			}

			$jsonArr_quantity=json_decode($_POST['jsonArr_quantity']);
			$jsonArr_quantity= (array) $jsonArr_quantity;

			$quantity_arr = [];
			foreach ($jsonArr_quantity as $pr_arr) {
				$quantity_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_unit=json_decode($_POST['jsonArr_product_unit']);
			$jsonArr_product_unit= (array) $jsonArr_product_unit;

			$product_unit_arr = [];
			foreach ($jsonArr_product_unit as $pr_arr) {
				$product_unit_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_base_quantity=json_decode($_POST['jsonArr_product_base_quantity']);
			$jsonArr_product_base_quantity= (array) $jsonArr_product_base_quantity;

			$product_base_quantity_arr = [];
			foreach ($jsonArr_product_base_quantity as $pr_arr) {
				$product_base_quantity_arr[]=  $pr_arr->value;

			}


			$jsonArr_product_discount=json_decode($_POST['jsonArr_product_discount']);
			$jsonArr_product_discount= (array) $jsonArr_product_discount;

			$product_discount_arr = [];
			foreach ($jsonArr_product_discount as $pr_arr) {
				$product_discount_arr[]=  $pr_arr->value;

			}


			$jsonArr_product_tax=json_decode($_POST['jsonArr_product_tax']);
			$jsonArr_product_tax= (array) $jsonArr_product_tax;

			$product_tax_arr = [];
			foreach ($jsonArr_product_tax as $pr_arr) {
				$product_tax_arr[]=  $pr_arr->value;

			}

			//$this->sma->print_arrays($_POST);
			//$this->sma->print_arrays($_POST['product_tax'],$product_tax_arr);
			//$this->sma->print_arrays($_POST['product_discount'],$product_discount_arr);



            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('po',$this->input->post('brId') ? $this->input->post('brId') : null);
            //if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            //} else {
           //     $date = date('Y-m-d H:i:s');
            //}
			$warehouse_id     = $this->input->post('warehouse');
            $supplier_id      = $this->input->post('supplier');
            $status           = $this->input->post('status');
            if($status=='received1'){
				$status='received';
				$receiving_status='pending';
			}else{
			$receiving_status='received';
			}

            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->site->getCompanyByID($supplier_id);
            $supplier         = $supplier_details->company && $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            $note             = $this->sma->clear_tags($_POST['note']);
            $payment_term     = $this->input->post('payment_term');
            $customs_fees     = $this->input->post('customs_fees') ? $this->input->post('customs_fees') : 0;
            $other_fees       = $this->input->post('Other_fees') ? $this->input->post('Other_fees') : 0;
            $due_date         = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days', strtotime($date))) : null;
			$currencyCode         = $this->input->post('currency') ? $this->input->post('currency') : $this->Settings->default_currency;
            $payment_status   = $this->input->post('payment_status') ? $this->input->post('payment_status') : 'pending';
			$currencyRate=$this->site->getCurrencyByCode($currencyCode)->rate;

			$supply_date      = null;
			if($status=="received" || $status=="partial")
			$supply_date  = date('Y-m-d H:i:s');


            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;







			$general_total_net_unit_cost_quantity=0;
            $i                = sizeof($product_arr);
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id          = $product_id_arr[$r];
                $item_code          = $product_arr[$r];
                $item_name          = $product_name_arr[$r];
                $item_net_cost      = $this->sma->formatDecimal($net_cost_arr[$r], 12);
                $unit_cost          = $this->sma->formatDecimal($unit_cost_arr[$r], 12);
                $real_unit_cost     = $this->sma->formatDecimal($real_unit_cost_arr[$r], 12);
                $item_unit_quantity = $quantity_arr[$r];
                $item_option        = isset($product_option_arr[$r]) && $product_option_arr[$r] != 'false' ? $product_option_arr[$r] : null;
                $item_tax_rate      = $product_tax_arr[$r]      ?$product_tax_arr[$r]: null;
                $item_discount      = $product_discount_arr[$r] ? $product_discount_arr[$r]: null;
                $item_expiry        = (isset($expiry_arr[$r]) && !empty($expiry_arr[$r])) ? $this->sma->fsd($expiry_arr[$r]) : null;
                $supplier_part_no   = (isset($part_no_arr[$r]) && !empty($part_no_arr[$r])) ? $part_no_arr[$r] : null;
                $item_unit          = $product_unit_arr[$r];
                $item_quantity      = $product_base_quantity_arr[$r];
				//$this->sma->print_arrays($item_unit_quantity,$item_quantity);

                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->purchases_model->getProductByCode($item_code);
                    if ($item_expiry) {
                        $today = date('Y-m-d');
                        if ($item_expiry <= $today) {
                            $this->session->set_flashdata('error', lang('product_expiry_date_issue') . ' (' . $product_details->name . ')');
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    // $unit_cost = $real_unit_cost;
                    $pr_discount      = $this->site->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost        = $this->sma->formatDecimal($unit_cost - $pr_discount, 12);
                    $item_net_cost    = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity, 12);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_cost);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        /*if ($product_details->tax_method != 1) {
                            $item_net_cost = $unit_cost - $item_tax;
                        }*/
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_cost * $item_unit_quantity) + $pr_item_tax);
                    $unit     = $this->site->getUnitByID($item_unit);
					if($item_unit_quantity != $item_quantity){

					$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($item_net_cost*$item_unit_quantity);
					/*}else if($item_unit_quantity > $item_quantity){
					$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($item_net_cost*$item_quantity);
					*/
					}else{
					$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($item_net_cost*$item_quantity);
					}


                    if ($this->Settings->isOpen_invoice==1) {
                        $pid=$item_id;
                        $pname=$item_name;
                    }else{
                        $pid=$product_details->id;
                        $pname=$product_details->name;

                    }


                    $product = [
                        'product_id'        => $pid,
                        'product_code'      => $item_code,
                        'product_name'      => $pname,
                        'option_id'         => $item_option,
                        'net_unit_cost'     => $item_net_cost,
                        //'unit_cost'         => $this->sma->formatDecimal($item_net_cost + $item_tax, 12),
                        'unit_cost'         => $this->sma->formatDecimal($item_net_cost, 12),
                        'quantity'          => $status == 'received' ? $item_quantity : $item_unit_quantity,
                        'product_unit_id'   => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity'     => $item_unit_quantity,
                        'quantity_balance'  => $status == 'received' ? $item_quantity : 0,
                        'quantity_received' => $status == 'received' ? $item_quantity : 0,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $this->sma->formatDecimal($subtotal, 12),
                        'expiry'            => $item_expiry,
                        'real_unit_cost'    => $real_unit_cost,
                        'date'              => date('Y-m-d', strtotime($date)),
                        'status'            => $status,
                        'supplier_part_no'  => $supplier_part_no,
                    ];

                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $product  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $product  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $product  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }

					if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
					$receiving_date=NULL;
					if($this->input->post('receiving_date')){
					$receiving_date=$this->sma->fld(trim($this->input->post('receiving_date')));
					}
					$product['receiving_date']=$receiving_date;

					}


                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_cost * $item_unit_quantity), 12);
                    $generaltotal += $this->sma->formatDecimal(($real_unit_cost * $item_unit_quantity), 12);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 12);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax + $customs_fees + $other_fees - $order_discount));
            if($product_tax>0)
			$total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax + (($customs_fees + $other_fees)* $this->purchases_model->getTaxRatesActive()->rate/100)), 12);
            else
			$total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping + $customs_fees + $other_fees, 12) - $order_discount), 12);

			if($status!='received'){
				unset($_POST['costCenterId']);
				unset($_POST['costCenterAmount']);
			}



			$data           = ['reference_no' => $reference,
                'date'                        => $date,
                'supplier_id'                 => $supplier_id,
                'supplier'                    => $supplier,
                'warehouse_id'                => $warehouse_id,
                'note'                        => $note,
                'total'                       => $generaltotal,
                'product_discount'            => $product_discount,
                'order_discount_id'           => $this->input->post('discount'),
                'order_discount'              => $order_discount,
                'total_discount'              => $total_discount,
                'product_tax'                 => $product_tax,
                'order_tax_id'                => $this->input->post('order_tax'),
                'order_tax'                   => $order_tax,
                'total_tax'                   => $total_tax,
                'shipping'                    => $this->sma->formatDecimal($shipping, 12),
                'grand_total'                 => $grand_total,
                'status'                      => $status,
                'receiving_status'            => $receiving_status,
                'created_by'                  => $this->session->userdata('user_id'),
                'payment_term'                => $payment_term,
                'payment_status'      => $payment_status,
                'due_date'                    => $due_date,
                'customs_fees'                    => $this->sma->formatDecimal($customs_fees, 12),
                'Other_fees'                    => $this->sma->formatDecimal($other_fees, 12),
				'tax_active_rate'             => $this->purchases_model->getTaxRatesActive()->rate ? $this->purchases_model->getTaxRatesActive()->rate : 0,
				'supply_date'                => $supply_date,
				'currency_code'                => $currencyCode,
				'currency_rate'                => $this->sma->formatDecimal($currencyRate),
				'uuid'                => $this->site->getUniqeUUID()->UUID,
				'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),




            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;
                $data  ['purchase_no']=$this->site->getMainBranchesByID($data['brId'])->branch_prefix ? $this->site->getMainBranchesByID($data['brId'])->branch_prefix.'-'.$this->site->getCountPurchasesBybrid($data['brId'])->count : $this->site->getCountPurchasesBybrid($data['brId'])->count;


            }

            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }


            if ($payment_status == 'partial' || $payment_status == 'paid') {
                if ($this->input->post('paid_by') == 'deposit') {
                    if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                        $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }
				$p = isset($_POST['amount']) ? sizeof($_POST['amount']) : 0;
                $paid = 0;
				//$this->sma->print_arrays($_POST['amount']);
                for ($r = 0; $r < $p; $r++) {
                    if (isset($_POST['amount'][$r]) && !empty($_POST['amount'][$r]) && isset($_POST['paid_by'][$r]) && !empty($_POST['paid_by'][$r])) {
                        $amount = $this->sma->formatDecimal($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);


						if($_POST['paid_by'][$r]=="CC" && $_POST['cc_type'][$r]==''){

							$_POST['cc_type'][$r]="Mada";
						}

						$payment_type="";
			if ($this->Settings->accounting){
			if($_POST['paid_by'][$r]=="cash")
			$payment_type=$_POST['cash_account'][$r];
			if($_POST['paid_by'][$r]=="CC")
			$payment_type=$_POST['cc_account'][$r];
			if($_POST['paid_by'][$r]=="Cheque")
			$payment_type=$_POST['cheque_account'][$r];
			if($_POST['paid_by'][$r]=="bank_transfer")
			$payment_type=$_POST['transfer_account'][$r];
			//if($_POST['paid_by'][$r]=="gift_card")
			//$payment_type=$_POST['transfer_account'][$r];

			}

                        if ($_POST['paid_by'][$r] == 'gift_card') {
                            $gc            = $this->site->getGiftCardByNO($_POST['paying_gift_card_no'][$r]);
                            $amount_paying = $_POST['amount'][$r] >= $gc->balance ? $gc->balance : $_POST['amount'][$r];
                            $gc_balance    = $gc->balance - $amount_paying;
                            $payment[]     = [
                                'date' => $date,
                                // 'reference_no' => $this->site->getReference('pay'),
                                'amount'      => $amount,
                                'paid_by'     => $_POST['paid_by'][$r],
                                'cheque_no'   => $_POST['cheque_no'][$r],
                                'cc_no'       => $_POST['paying_gift_card_no'][$r],
                                'cc_holder'   => $_POST['cc_holder'][$r],
                                'cc_month'    => $_POST['cc_month'][$r],
                                'cc_year'     => $_POST['cc_year'][$r],
                                'cc_type'     => $_POST['cc_type'][$r],
                                'cc_cvv2'     => $_POST['cc_cvv2'][$r],
                                'created_by'  => $this->session->userdata('user_id'),
                                'type'        => 'received',
                                'note'        => $_POST['payment_note'][$r],
                                'pos_paid'    => $_POST['amount'][$r],
                                'pos_balance' => $_POST['balance_amount'][$r],
                                'gc_balance'  => $gc_balance,
								'currency'                => $currencyCode,
                                'currency_rate'                => $this->sma->formatDecimal($currencyRate),


                            ];



						} else {
                            $payment[] = [
                                'date' => $date,
                                // 'reference_no' => $this->site->getReference('pay'),
                                'amount'      => $amount,
                                'paid_by'     => $_POST['paid_by'][$r],
                                'cheque_no'   => $_POST['cheque_no'][$r],
                                'cc_no'       => $_POST['cc_no'][$r],
                                'cc_holder'   => $_POST['cc_holder'][$r],
                                'cc_month'    => $_POST['cc_month'][$r],
                                'cc_year'     => $_POST['cc_year'][$r],
                                'cc_type'     => $_POST['cc_type'][$r],
                                'cc_cvv2'     => $_POST['cc_cvv2'][$r],
                                'created_by'  => $this->session->userdata('user_id'),
                                'type'        => 'received',
                                'note'        => $_POST['payment_note'][$r],
                                'pos_paid'    => $_POST['amount'][$r],
                                'pos_balance' => $_POST['balance_amount'][$r],
								'currency'                => $currencyCode,
                                'currency_rate'                => $this->sma->formatDecimal($currencyRate),
                            ];

                        }

						if(empty($payment_type) && $this->Settings->accounting){

							$payment_type=$this->site->getPaymentTypeByPaymentType($_POST['paid_by'][$r])->id;
						}
							$payment_type_arr[]     = [
                                'payment_type' => $payment_type,

                            ];

                    }



			$amount=$payment[$r]['amount'];
			$amountPL=0;
			$amountDiff=0;


			$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
			if($payment[$r]['currency_rate'])
				$exchange_rate=$payment[$r]['currency_rate'];
			else
				$exchange_rate=$default_currencyRate;

			if($sale->currency_rate)
				$currencyRate=$sale->currency_rate;
			else
				$currencyRate=1;


                        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1 && $payment[$r]['amount']!=0)
                            {

                                $payment[$r]['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                                $payment[$r]['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                                $payment[$r]['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;
                            }



				 //$this->sma->print_arrays($payment[$r]['currency_rate']);




				//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

				$amount=$payment[$r]['amount'] * $currencyRate;

				//if ($currencyRate != $exchange_rate){
					$amountPL=$payment[$r]['amount'] * $exchange_rate;
					$amountDiff=$amountPL-$amount;
				//}
				//}

				//$this->sma->print_arrays($amountDiff);




                }
            } else {
                $payment = [];
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }





            //$this->sma->print_arrays($data, $products,$general_total_net_unit_cost_quantity,$payment,$payment_type_arr,$_POST);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPurchase($data, $products,$general_total_net_unit_cost_quantity,$payment,$payment_type_arr)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line('purchase_added'));
            admin_redirect('purchases');
        } else {
            if ($quote_id) {
                $this->data['quote_id']   = $quote_id;
				/*
				$this->data['quote'] = $this->purchases_model->getQuoteByID($quote_id);
                $supplier_id         = $this->data['quote']->supplier_id;
                $items               = $this->purchases_model->getAllQuoteItems($quote_id);
                krsort($items);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if ($row->type == 'combo') {
                        $combo_items = $this->site->getProductComboItems($row->id, $item->warehouse_id);
                        foreach ($combo_items as $citem) {
                            $crow = $this->site->getProductByID($citem->id);
                            if (!$crow) {
                                $crow      = json_decode('{}');
                                $crow->qty = $item->quantity;
                            } else {
                                unset($crow->details, $crow->product_details, $crow->price);
                                $crow->qty = $citem->qty * $item->quantity;
                            }
                            $crow->base_quantity  = $item->quantity;
                            $crow->base_unit      = $crow->unit ? $crow->unit : $item->product_unit_id;
                            $crow->base_unit_cost = $crow->cost ? $crow->cost : $item->unit_cost;
                            $crow->unit           = $item->product_unit_id;
                            $crow->discount       = $item->discount ? $item->discount : '0';
                            $supplier_cost        = $supplier_id ? $this->getSupplierCost($supplier_id, $crow) : $crow->cost;
                            $crow->cost           = $supplier_cost ? $supplier_cost : 0;
                            $crow->tax_rate       = $item->tax_rate_id;
                            $crow->real_unit_cost = $crow->cost ? $crow->cost : 0;
                            $crow->expiry         = '';
                            $options              = $this->purchases_model->getProductOptions($crow->id);
                            $units                = $this->site->getUnitsByBUID($row->base_unit);
                            $tax_rate             = $this->site->getTaxRateByID($crow->tax_rate);
                            $ri                   = $this->Settings->item_addition ? $crow->id : $c;

                            $pr[$ri] = ['id' => $c, 'item_id' => $crow->id, 'label' => $crow->name . ' (' . $crow->code . ')', 'row' => $crow, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];
                            $c++;
                        }
                    } elseif ($row->type == 'standard') {
                        if (!$row) {
                            $row           = json_decode('{}');
                            $row->quantity = 0;
                        } else {
                            unset($row->details, $row->product_details);
                        }

                        $row->id             = $item->product_id;
                        $row->code           = $item->product_code;
                        $row->name           = $item->product_name;
                        $row->base_quantity  = $item->quantity;
                        $row->base_unit      = $row->unit ? $row->unit : $item->product_unit_id;
                        $row->base_unit_cost = $row->cost ? $row->cost : $item->unit_cost;
                        $row->unit           = $item->product_unit_id;
                        $row->qty            = $item->unit_quantity;
                        $row->option         = $item->option_id;
                        $row->discount       = $item->discount ? $item->discount : '0';
                        $supplier_cost       = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                        $row->cost           = $supplier_cost ? $supplier_cost : 0;
                        $row->tax_rate       = $item->tax_rate_id;
                        $row->expiry         = '';
                        $row->real_unit_cost = $row->cost ? $row->cost : 0;
                        $options             = $this->purchases_model->getProductOptions($row->id);

                        $units    = $this->site->getUnitsByBUID($row->base_unit);
                        $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                        $ri       = $this->Settings->item_addition ? $row->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                        $c++;
                    }
                }
                $this->data['quote_items'] = json_encode($pr);
				*/




				$man_details = $this->manufacturing_model->getManufacturingByID($quote_id);
				$bom = $this->manufacturing_model->getBomByID($man_details->bom_id);
				$bom_details = $this->manufacturing_model->getBomDetails($bom->id);

				    //$this->sma->print_arrays($bom_details,$bom,$man_details);



                $this->data['quote'] = $bom;
				$this->data['quote']->warehouse_id=$bom->wh_raw;
                $supplier_id         = $this->data['quote']->supplier_id;
                $items               = $bom_details;
                krsort($items);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->site->getProductByID($item->id);
                    if ($row->type == 'standard') {
						$consumed = $item->qty*$man_details->quantity; $sp=$this->manufacturing_model->getWarehousesWithPQ($item->id,$bom->wh_raw)[0]->quantity;
						//$this->sma->print_arrays($sp);
						if ($consumed > $sp) {

                        if (!$row) {
                            $row           = json_decode('{}');
                            $row->quantity = 0;
                        } else {
                            unset($row->details, $row->product_details);
                        }

                        $row->id             = $item->id;
                        $row->code           = $item->code;
                        //$row->name           = $item->product_name;
                        $row->base_quantity  = $item->qty*$man_details->quantity;
                        $row->base_unit      = $row->unit ? $row->unit : $item->unit_id;
                        $row->base_unit_cost = $row->cost ? $row->cost : $item->avg_cost;
                        $row->unit           = $item->unit_id;
                        $row->qty            = $item->qty*$man_details->quantity;
                        $row->option         = $item->option_id;
                        $row->discount       = $item->discount ? $item->discount : '0';
                        $supplier_cost       = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                        $row->cost           = $supplier_cost ? $supplier_cost : 0;
                        $row->tax_rate       = $this->Settings->default_tax_rate;
                        $row->expiry         = '';
                        $row->real_unit_cost = $row->cost ? $row->cost : 0;
                        $options             = $this->purchases_model->getProductOptions($row->id);

                        $units    = $this->site->getUnitsByBUID($row->base_unit);
                        $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                        $ri       = $this->Settings->item_addition ? $row->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                        $c++;
                    }
                    }
                }
                $this->data['quote_items'] = json_encode($pr);
            }else if ($purchase_id) {
                $this->data['quote_id']   = $purchase_id;
				$this->data['quote'] = $this->purchases_model->getPurchaseByID($purchase_id);
                $supplier_id         = $this->data['quote']->supplier_id;
                $items               = $this->purchases_model->getAllPurchaseItems($purchase_id);
                krsort($items);
                //$this->sma->print_arrays($items);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {




                        $quantity_received=0;
				if($item->status=='received')
                $quantity_received=$item->quantity_received;
				$row                   = $this->site->getProductByID($item->product_id);
                //$this->sma->print_arrays($row);
                $row->expiry           = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
                $row->quantity_received    = $quantity_received;
                $row->base_quantity    = $item->quantity;
                $row->base_unit        = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_cost   = $item->unit_cost;//$row->cost ? $row->cost : $item->unit_cost;
                $row->unit             = $item->product_unit_id;
                $row->qty              = $item->unit_quantity;
                $row->oqty             = $item->quantity;
                $row->supplier_part_no = $item->supplier_part_no;
                $row->received         = $item->quantity_received ? $item->quantity_received : $item->quantity;
                $row->quantity_balance = $item->quantity_balance + ($item->quantity - $row->received);
                $row->discount         = $item->discount ? $item->discount : '0';
                $options               = $this->purchases_model->getProductOptions($row->id);
                $row->option           = $item->option_id;
                $row->real_unit_cost   = $item->real_unit_cost;
                $row->cost             = $this->sma->formatDecimal($item->net_unit_cost + ($item->item_discount / $item->quantity));
                $row->tax_rate         = $item->tax_rate_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6);
                //unset($row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5);
                //unset($row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                //unset($row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                //unset($row->alert_quantity, $row->image, $row->subcategory_id, $row->track_quantity, $row->barcode_symbology);
                //unset($row->category_id, $row->warehouse, $row->promotion, $row->promo_price, $row->start_date, $row->end_date);
                //$units    = $this->site->getUnitByID($row->base_unit);
                $units    = $this->site->getUnitsByBUIDV2($row->base_unit);

                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                //$tax_rate = $this->site->getTaxRateByIDV2($row->tax_rate);
                $ri       = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $c++;


                }
                //$this->sma->print_arrays($pr);
                $this->data['quote_items'] = json_encode($pr);



            }

            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			if ($this->Settings->accounting){
			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
            $this->data['payment_types'] = $this->site->getAllPaymentTypes();
            $accounts_settings   = $this->accounts_model->getSettings();
            $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

			}
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }


            $this->data['price_groups'] = $this->site->getAllPriceGroups();
            $this->data['suppliers']  = $this->site->getAllCompanies('supplier');
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['currencies'] = $this->site->getAllCurrencies();
			$this->data['ponumber']   = ''; //$this->site->getReference('po');
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc                 = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('purchases'), 'page' => lang('purchases')], ['link' => '#', 'page' => lang('add_purchase')]];
            $meta               = ['page_title' => lang('add_purchase'), 'bc' => $bc];
            $this->page_construct('purchases/add', $meta, $this->data);
        }
    }

    public function add_expense()
    {
        $this->sma->checkPermissions('add_expense', true);
        $this->load->helper('security');

        //$this->form_validation->set_rules('reference', lang("reference"), 'required');
        $this->form_validation->set_rules('amount', lang('amount'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = [
                'date'         => $date,
                'reference'    => $this->input->post('reference') ? $this->input->post('reference') : $this->site->getReference('ex',$this->input->post('brId') ? $this->input->post('brId') : null),
                'amount'       => $this->input->post('grand_total'),
                'order_tax_id'       => $this->input->post('order_tax'),
                'total_tax'       => $this->input->post('total_tax'),
                'grand_total'       => $this->input->post('grand_total'),
                'total'       => $this->input->post('amount'),
                'created_by'   => $this->session->userdata('user_id'),
                'note'         => $this->input->post('note', true),
                'category_id'  => $this->input->post('category', true),
                'warehouse_id' => $this->input->post('warehouse', true),
                'account_id' => $this->input->post('account_id', true),
                'cost_id' => $this->input->post('cost_id', true),
                'tax_method' => $this->input->post('tax_method', true),
                'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),
            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
            $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
            $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

        }

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            if ($this->input->post('payment_status') == 'paid') {

            $payment = [
                'date'         => $date,
                'reference_no' => $this->site->getReference('ppay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $expense->brId : null),
                'amount'       => $this->input->post('grand_total'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->input->post('note'),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => 'sent',
            ];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {

                                $payment['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                                $payment['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                                $payment['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;


                            }


                $payment_type="";
			if ($this->Settings->accounting){
			if($this->input->post('paid_by')=="cash")
			$payment_type=$this->input->post('cash_account');
			if($this->input->post('paid_by')=="CC")
			$payment_type=$this->input->post('cc_account');
			if($this->input->post('paid_by')=="Cheque")
			$payment_type=$this->input->post('cheque_account');
			if($this->input->post('paid_by')=="bank_transfer")
			$payment_type=$this->input->post('transfer_account');
			}



            }


            //$this->sma->print_arrays($data,$_POST,$payment,$payment_type);

        } elseif ($this->input->post('add_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addExpense($data,'0',$payment,$payment_type)) {
            $this->session->set_flashdata('message', lang('expense_added'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['exnumber']   = ''; //$this->site->getReference('ex');

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

            }

            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['categories'] = $this->purchases_model->getExpenseCategories();
			if ($this->Settings->accounting){

            $this->data['expenses_accounts'] = $this->accounts_model->getAccountsByCategory('المصروفات');
            $this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
            $this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

			}
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'purchases/add_expense', $this->data);
        }
    }

    public function add_payment($id = null)
    {
        $this->sma->checkPermissions('add_payment', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $purchase = $this->purchases_model->getPurchaseByID($id);
        if ($purchase->payment_status == 'paid' && $purchase->grand_total == $purchase->paid) {
            $this->session->set_flashdata('error', lang('purchase_already_paid'));
            $this->sma->md();
        }

        //$this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'purchase_id'  => $this->input->post('purchase_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('ppay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $purchase->brId : null),
                //'amount'       => $this->input->post('amount-paid'),
                'amount'       => $this->input->post('amount_after_discount'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->sma->clear_tags($this->input->post('note')),
                'currency'         => $purchase->currency_code ? $purchase->currency_code : $this->Settings->default_currency,
				'currency_rate'         => $this->input->post('exchange_rate'),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => 'sent',
				'commercial_discount'    => $this->input->post('discount_earned'),
                'commercial_discount_id'    => $this->input->post('discount'),

            ];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {

                                $payment['grpId']=$purchase->grpId;
                                $payment['coId']=$purchase->coId;
                                $payment['brId']=$purchase->brId;


                            }

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

			$payment_type="";
			if ($this->Settings->accounting){
			if($this->input->post('paid_by')=="cash")
			$payment_type=$this->input->post('cash_account');
			if($this->input->post('paid_by')=="CC")
			$payment_type=$this->input->post('cc_account');
			if($this->input->post('paid_by')=="Cheque")
			$payment_type=$this->input->post('cheque_account');
			if($this->input->post('paid_by')=="bank_transfer")
			$payment_type=$this->input->post('transfer_account');
			}


            $amount=$payment['amount'];
			$amountPL=0;
			$amountDiff=0;


			$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
			if($payment['currency_rate'])
				$exchange_rate=$payment['currency_rate'];
			else
				$exchange_rate=$default_currencyRate;

			if($purchase->currency_rate)
				$currencyRate=$purchase->currency_rate;
			else
				$currencyRate=1;



				 //$this->sma->print_arrays($payment['currency_rate']);




				//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

				$amount=$payment['amount'] * $currencyRate;

				//if ($currencyRate != $exchange_rate){
					$amountPL=$payment['amount'] * $exchange_rate;
					$amountDiff=$amountPL-$amount;



                   // $this->sma->print_arrays($amountDiff);

                   if($amountDiff>0){
					$return_purchase_ex_ref=$this->site->getReference('ex',$this->input->post('brId') ? $this->input->post('brId') : null);


				//$this->sma->print_arrays($total_shipping);
			$data_ex = [
                'date'         => $date,
                'reference'    => $return_purchase_ex_ref,
                'amount'       => abs($amountDiff),
                'grand_total'       => abs($amountDiff),
                'total'       => abs($amountDiff),
				'payment_status'       => 'paid',
				'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $purchase->warehouse_id,
			    'other' => 1,
			];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {

                                $data_ex['grpId']=$purchase->grpId;
                                $data_ex['coId']=$purchase->coId;
                                $data_ex['brId']=$purchase->brId;


                            }
			if($this->purchases_model->addExpense($data_ex,"1")){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_purchase_ex_ref);
			//$return_ex_id=$expense->id;
			}
				}
				if($amountDiff<0){
			$return_purchase_re_ref=$this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $purchase->brId : null);
            $data_re = [
                'date'         => $date,
                'reference'    => $return_purchase_re_ref,
                'amount'       => abs($amountDiff),
                'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $purchase->warehouse_id,
			];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {

                                $data_re['grpId']=$purchase->grpId;
                                $data_re['coId']=$purchase->coId;
                                $data_re['brId']=$purchase->brId;


                            }
			if($this->products_model->addRevenue($data_re)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_purchase_ex_ref);
			//$return_ex_id=$expense->id;
			}

				}


                //$this->sma->print_arrays($payment,$payment_type);
            } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPayment($payment,$payment_type)) {
            $this->session->set_flashdata('message', lang('payment_added'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['currencies'] = $purchase->brId ? $this->site->getCurrenciesBybrId($purchase->brId) : $this->site->getAllCurrencies();
            }else{
                $this->data['currencies'] = $this->site->getAllCurrencies();
            }


            $this->data['error']       = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));



            $this->data['inv']         = $purchase;
            $this->data['payment_ref'] = ''; //$this->site->getReference('ppay');
            $this->data['modal_js']    = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/add_payment', $this->data);
        }
    }



	public function add_payment_application($id = null)
    {
        $this->sma->checkPermissions('add_payment', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $purchase = $this->purchases_model->getPurchaseByID($id);

        $this->form_validation->set_rules('applied_amount', lang("Applied_amount"), 'required');
        $this->form_validation->set_rules('code', lang('code'), 'required');
        if ($this->form_validation->run() == true) {
            if ($this->input->post('expected_payment_date')) {
                $expected_payment_date = $this->sma->fld(trim($this->input->post('expected_payment_date')));
            } else {
                $expected_payment_date = null;
            }
            $payment = [
                'code'         => $this->input->post('code'),
                'purchase_id'  => $this->input->post('purchase_id'),
                'payment_schedule' => $this->input->post('payment_schedule'),
                'applied_amount'       => $this->input->post('applied_amount'),
                'applied_schedule'      => $this->input->post('applied_schedule'),
                'remarks'    => $this->input->post('remarks'),
                'expected_payment_date'        => $expected_payment_date,
                'purpose'         => $this->sma->clear_tags($this->input->post('purpose')),

            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {

                                $payment['grpId']=$purchase->grpId;
                                $payment['coId']=$purchase->coId;
                                $payment['brId']=$purchase->brId;


                            }




            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('add_payment_application')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPaymentApplication($payment)) {
            $this->session->set_flashdata('message', lang('Payment_application_has_been_successfully_added'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {

            $this->data['error']       = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv']         = $purchase;
			$this->data['payment_application'] = $this->purchases_model->getSumPurchasePaymentApplication($id);

            $this->data['payment_ref'] = ''; //$this->site->getReference('ppay');
            $this->data['modal_js']    = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/add_payment_application', $this->data);
        }
    }



	public function add_expense_payment($id = null)
    {
        $this->sma->checkPermissions('add_payment', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $expense = $this->purchases_model->getExpenseByID($id);
        if ($expense->payment_status == 'paid' && $expense->grand_total == $expense->paid) {
            $this->session->set_flashdata('error', lang('expense_already_paid'));
            $this->sma->md();
        }

        //$this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'expense_id'  => $this->input->post('expense_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('ppay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $expense->brId : null),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->sma->clear_tags($this->input->post('note')),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => 'sent',
            ];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {

                                $payment['grpId']=$expense->grpId;
                                $payment['coId']=$expense->coId;
                                $payment['brId']=$expense->brId;


                            }

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

			$payment_type="";
			if ($this->Settings->accounting){
			if($this->input->post('paid_by')=="cash")
			$payment_type=$this->input->post('cash_account');
			if($this->input->post('paid_by')=="CC")
			$payment_type=$this->input->post('cc_account');
			if($this->input->post('paid_by')=="Cheque")
			$payment_type=$this->input->post('cheque_account');
			if($this->input->post('paid_by')=="bank_transfer")
			$payment_type=$this->input->post('transfer_account');
			}


            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addExpensePayment($payment,$payment_type)) {
            $this->session->set_flashdata('message', lang('payment_added'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}

            $this->data['error']       = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv']         = $expense;
            $this->data['payment_ref'] = ''; //$this->site->getReference('ppay');
            $this->data['modal_js']    = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/add_expense_payment', $this->data);
        }
    }

    public function combine_pdf($purchases_id)
    {
        $this->sma->checkPermissions('pdf');

        foreach ($purchases_id as $purchase_id) {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $inv                 = $this->purchases_model->getPurchaseByID($purchase_id);
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $this->data['rows']            = $this->purchases_model->getAllPurchaseItems($purchase_id);
            $this->data['supplier']        = $this->site->getCompanyByID($inv->supplier_id);
            $this->data['warehouse']       = $this->site->getWarehouseByID($inv->warehouse_id);
            $this->data['created_by']      = $this->site->getUser($inv->created_by);
            $this->data['inv']             = $inv;
            $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : null;
            $this->data['return_rows']     = $inv->return_id ? $this->purchases_model->getAllPurchaseItems($inv->return_id) : null;
            $inv_html                      = $this->load->view($this->theme . 'purchases/pdf', $this->data, true);
            if (!$this->Settings->barcode_img) {
                $inv_html = preg_replace("'\<\?xml(.*)\?\>'", '', $inv_html);
            }
            $html[] = [
                'content' => $inv_html,
                'footer'  => '',
            ];
        }

        $name = lang('purchases') . '.pdf';
        $this->sma->generate_pdf($html, $name);
    }

    /* --------------------------------------------------------------------------- */

    public function delete($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
					//$general_total_net_unit_cost_quantity=0;

					$purchase       = $this->purchases_model->getPurchaseByID($id);
					/*$purchase_items_temp = $this->site->getAllPurchaseItems($id);
					foreach ($purchase_items_temp as $oitem_temp) {
					if ($purchase->order_discount>0 || $purchase->order_discount<0) {
						$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($oitem_temp->quantity_received*$oitem_temp->real_unit_cost);
					}else{
						$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($oitem_temp->quantity_received*$oitem_temp->net_unit_cost);
					}
					}

					$this->sma->print_arrays($purchase_items_temp,$id, $general_total_net_unit_cost_quantity);
					*/
		$inv = $this->purchases_model->getPurchaseByID($id);
        if ($inv->status == 'returned' || $inv->return_id>0) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('purchase_x_action')]);
        }

        if ($inv->status != 'ordered' &&  $inv->status != 'pending') {
            $this->sma->send_json(['error' => 1, 'msg' => lang('Invoice_received_Please_return_the_invoice')]);
        }


		if($purchase->payment_status=='paid' || $purchase->payment_status=='partial'){
			$this->sma->send_json(['error' => 1, 'msg' => lang('the_invoice_is_paid_you_cannot_delete_it')]);
		}else{

        //if ($this->purchases_model->deletePurchase($id)) {
        if ($this->purchases_model->deletePurchase($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('purchase_deleted')]);
            }
            $this->session->set_flashdata('message', lang('purchase_deleted'));
            admin_redirect('welcome');
        }
		}
    }

    public function cancel($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
					//$general_total_net_unit_cost_quantity=0;

					$purchase       = $this->purchases_model->getPurchaseByID($id);
					/*$purchase_items_temp = $this->site->getAllPurchaseItems($id);
					foreach ($purchase_items_temp as $oitem_temp) {
					if ($purchase->order_discount>0 || $purchase->order_discount<0) {
						$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($oitem_temp->quantity_received*$oitem_temp->real_unit_cost);
					}else{
						$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($oitem_temp->quantity_received*$oitem_temp->net_unit_cost);
					}
					}

					$this->sma->print_arrays($purchase_items_temp,$id, $general_total_net_unit_cost_quantity);
					*/
		if($purchase->payment_status=='paid'){
			$this->sma->send_json(['error' => 1, 'msg' => lang('the_invoice_is_paid_you_cannot_cancel_it')]);
		}else{

        //if ($this->purchases_model->deletePurchase($id)) {
        if ($this->purchases_model->cancelPurchase($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('purchase_canceled')]);
            }
            $this->session->set_flashdata('message', lang('purchase_canceled'));
            admin_redirect('welcome');
        }
		}
    }

    public function delete_expense($id = null)
    {
        $this->sma->checkPermissions('delete_expense', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $expense = $this->purchases_model->getExpenseByID($id);
        if ($this->purchases_model->deleteExpense($id)) {
            if ($expense->attachment) {
                unlink($this->upload_path . $expense->attachment);
            }
            $this->sma->send_json(['error' => 0, 'msg' => lang('expense_deleted')]);
        }
    }

    public function delete_payment($id = null)
    {
        $this->sma->checkPermissions('delete_payment', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchases_model->deletePayment($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang('payment_deleted'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_payment_application($id = null)
    {
        $this->sma->checkPermissions('delete_payment', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchases_model->deletePaymentApplication($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang('Payment_application_has_been_successfully_deleted'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

     public function delete_expense_payment($id = null)
    {
        $this->sma->checkPermissions('delete_payment', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchases_model->deleteExpensePayment($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang('payment_deleted'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /* ------------------------------------------------------------------------------------- */

    public function edit($id = null)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->purchases_model->getPurchaseByID($id);

		if ($inv->status == 'returned' || $inv->return_id || $inv->return_purchase_ref) {
            $this->session->set_flashdata('error', lang('purchase_x_action'));
            admin_redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'welcome');
        }
        if (!$this->session->userdata('edit_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('reference_no', $this->lang->line('ref_no'), 'required');
        $this->form_validation->set_rules('warehouse', $this->lang->line('warehouse'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line('supplier'), 'required');
        $this->form_validation->set_rules('status', lang('status'), 'required');

        $this->session->unset_userdata('csrf_token');

        if ($this->form_validation->run() == true) {



			$jsonArr_product_id=json_decode($_POST['jsonArr_product_id']);
			$jsonArr_product_id= (array) $jsonArr_product_id;

			$product_id_arr = [];
			foreach ($jsonArr_product_id as $product_arr) {
				$product_id_arr[]=  $product_arr->value;

			}

			$jsonArr_product=json_decode($_POST['jsonArr_product']);
			$jsonArr_product= (array) $jsonArr_product;

			$product_arr = [];
			foreach ($jsonArr_product as $pr_arr) {
				$product_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_name=json_decode($_POST['jsonArr_product_name']);
			$jsonArr_product_name= (array) $jsonArr_product_name;

			$product_name_arr = [];
			foreach ($jsonArr_product_name as $pr_arr) {
				$product_name_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_option=json_decode($_POST['jsonArr_product_option']);
			$jsonArr_product_option= (array) $jsonArr_product_option;

			$product_option_arr = [];
			foreach ($jsonArr_product_option as $pr_arr) {
				$product_option_arr[]=  $pr_arr->value;

			}

			$jsonArr_part_no=json_decode($_POST['jsonArr_part_no']);
			$jsonArr_part_no= (array) $jsonArr_part_no;

			$part_no_arr = [];
			foreach ($jsonArr_part_no as $pr_arr) {
				$part_no_arr[]=  $pr_arr->value;

			}

			$jsonArr_expiry=json_decode($_POST['jsonArr_expiry']);
			$jsonArr_expiry= (array) $jsonArr_expiry;

			$expiry_arr = [];
			foreach ($jsonArr_expiry as $pr_arr) {
				$expiry_arr[]=  $pr_arr->value;

			}

			$jsonArr_net_cost=json_decode($_POST['jsonArr_net_cost']);
			$jsonArr_net_cost= (array) $jsonArr_net_cost;

			$net_cost_arr = [];
			foreach ($jsonArr_net_cost as $pr_arr) {
				$net_cost_arr[]=  $pr_arr->value;

			}

			$jsonArr_unit_cost=json_decode($_POST['jsonArr_unit_cost']);
			$jsonArr_unit_cost= (array) $jsonArr_unit_cost;

			$unit_cost_arr = [];
			foreach ($jsonArr_unit_cost as $pr_arr) {
				$unit_cost_arr[]=  $pr_arr->value;

			}

			$jsonArr_real_unit_cost=json_decode($_POST['jsonArr_real_unit_cost']);
			$jsonArr_real_unit_cost= (array) $jsonArr_real_unit_cost;

			$real_unit_cost_arr = [];
			foreach ($jsonArr_real_unit_cost as $pr_arr) {
				$real_unit_cost_arr[]=  $pr_arr->value;

			}

			$jsonArr_quantity_balance=json_decode($_POST['jsonArr_quantity_balance']);
			$jsonArr_quantity_balance= (array) $jsonArr_quantity_balance;

			$quantity_balance_arr = [];
			foreach ($jsonArr_quantity_balance as $pr_arr) {
				$quantity_balance_arr[]=  $pr_arr->value;

			}

			$jsonArr_quantity=json_decode($_POST['jsonArr_quantity']);
			$jsonArr_quantity= (array) $jsonArr_quantity;

			$quantity_arr = [];
			foreach ($jsonArr_quantity as $pr_arr) {
				$quantity_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_unit=json_decode($_POST['jsonArr_product_unit']);
			$jsonArr_product_unit= (array) $jsonArr_product_unit;

			$product_unit_arr = [];
			foreach ($jsonArr_product_unit as $pr_arr) {
				$product_unit_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_base_quantity=json_decode($_POST['jsonArr_product_base_quantity']);
			$jsonArr_product_base_quantity= (array) $jsonArr_product_base_quantity;

			$product_base_quantity_arr = [];
			foreach ($jsonArr_product_base_quantity as $pr_arr) {
				$product_base_quantity_arr[]=  $pr_arr->value;

			}


			$jsonArr_product_discount=json_decode($_POST['jsonArr_product_discount']);
			$jsonArr_product_discount= (array) $jsonArr_product_discount;

			$product_discount_arr = [];
			foreach ($jsonArr_product_discount as $pr_arr) {
				$product_discount_arr[]=  $pr_arr->value;

			}


			$jsonArr_product_tax=json_decode($_POST['jsonArr_product_tax']);
			$jsonArr_product_tax= (array) $jsonArr_product_tax;

			$product_tax_arr = [];
			foreach ($jsonArr_product_tax as $pr_arr) {
				$product_tax_arr[]=  $pr_arr->value;

			}


			$jsonArr_ordered_quantity=json_decode($_POST['jsonArr_ordered_quantity']);
			$jsonArr_ordered_quantity= (array) $jsonArr_ordered_quantity;

			$ordered_quantity_arr = [];
			foreach ($jsonArr_ordered_quantity as $pr_arr) {
				$ordered_quantity_arr[]=  $pr_arr->value;

			}


			$jsonArr_received_base_quantity=json_decode($_POST['jsonArr_received_base_quantity']);
			$jsonArr_received_base_quantity= (array) $jsonArr_received_base_quantity;

			$received_base_quantity_arr = [];
			foreach ($jsonArr_received_base_quantity as $pr_arr) {
				$received_base_quantity_arr[]=  $pr_arr->value;

			}



			//$this->sma->print_arrays($_POST);
			//$this->sma->print_arrays($_POST['received_base_quantity'],$received_base_quantity_arr);



            $reference = $this->input->post('reference_no');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = $inv->date;
            }
            $warehouse_id     = $this->input->post('warehouse');
            $supplier_id      = $this->input->post('supplier');
            $status           = $this->input->post('status');
            $status           = $this->input->post('status');
            if($status=='received1'){
				$status='received';
				$receiving_status='pending';
			}else{
			$receiving_status='received';
			}
			$shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $currencyCode         = $this->input->post('currency') ? $this->input->post('currency') : $this->Settings->default_currency;
			$currencyRate=$this->site->getCurrencyByCode($currencyCode)->rate;

			if ($status == 'received' || $status == 'partial') {
			$shipping_amount         = $this->input->post('shipping_amount') ? $this->input->post('shipping_amount') : 0;
            $shipping_received         = $this->input->post('shipping_amount') ? $this->input->post('shipping_amount') : 0;
            }else{
			$shipping_amount         = 0;
            $shipping_received       = 0;


			}
			$supplier_details = $this->site->getCompanyByID($supplier_id);
            $supplier         = $supplier_details->company && $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            $note             = $this->sma->clear_tags($_POST['note']);
            $payment_term     = $this->input->post('payment_term');
            $customs_fees     = $this->input->post('customs_fees') ? $this->input->post('customs_fees') : 0;
            $other_fees       = $this->input->post('Other_fees') ? $this->input->post('Other_fees') : 0;
            $due_date         = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days', strtotime($date))) : null;


			$supply_date      = null;
			if(($inv->status=="ordered" || $inv->status=="pending") && ($status=="received" || $status=="partial")){
			//if($status=="received" || $status=="partial")
			$supply_date  = date('Y-m-d H:i:s');
			}else{
				if($inv->status=="received" || $inv->status=="partial")
					$supply_date  = $inv->supply_date;

			}




            $total            = 0;
            $general_total            = 0;
            $product_tax      = 0;
            $general_total_net_unit_cost_quantity=0;
			$general_total_net_unit_cost_quantity_discont=0;
			$product_discount = 0;
            $partial          = false;
            $i                = sizeof($product_arr);
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id          = $product_id_arr[$r];
                $item_name          = $product_name_arr[$r];

                $item_code          = $product_arr[$r];
                $item_net_cost      = $this->sma->formatDecimal($net_cost_arr[$r], 12);
                $unit_cost          = $this->sma->formatDecimal($unit_cost_arr[$r], 12);
				//$this->sma->print_arrays($unit_cost);
                $real_unit_cost     = $this->sma->formatDecimal($real_unit_cost_arr[$r], 12);
                $item_unit_quantity = $quantity_arr[$r];
                $quantity_received  = $received_base_quantity_arr[$r];
                $item_option        = isset($product_option_arr[$r]) && $product_option_arr[$r] != 'false' ? $product_option_arr[$r] : null;
                $item_tax_rate      = $product_tax_arr[$r]      ? $product_tax_arr[$r] : null;
                $item_discount      = $product_discount_arr[$r] ? $product_discount_arr[$r] : null;
                $item_expiry        = (isset($expiry_arr[$r]) && !empty($expiry_arr[$r])) ? $this->sma->fsd($expiry_arr[$r]) : null;
                $supplier_part_no   = (isset($part_no_arr[$r]) && !empty($part_no_arr[$r])) ? $part_no_arr[$r] : null;
                $quantity_balance   = $quantity_balance_arr[$r];
                $ordered_quantity   = $ordered_quantity_arr[$r];
                $item_unit          = $product_unit_arr[$r];
                $item_quantity      = $product_base_quantity_arr[$r];

                if ($status == 'received' || $status == 'partial') {
                    /*$product_details = $this->purchases_model->getProductByCode($item_code);
                    if($product_details->unit!=$item_unit){
                        $unit     = $this->site->getUnitByID($item_unit);
                        if($unit->operator=='*'){
                            $item_quantity      =$item_quantity * $unit->operation_value;
                        }
                        if($unit->operator=='/'){
                            $item_quantity      =$item_quantity / $unit->operation_value;
                        }
                        //$this->sma->print_arrays($unit);
                        //
                    }*/

                    if ($quantity_received < $item_quantity) {
                        $partial = 'partial';
                    } elseif ($quantity_received > $item_quantity) {
                        $this->session->set_flashdata('error', lang('received_more_than_ordered'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                    $balance_qty = $quantity_received - ($ordered_quantity - $quantity_balance);
                } else {
                    $balance_qty       = $item_quantity;
                    $quantity_received = $item_quantity;
                    //$balance_qty       = 0;
                    //$quantity_received = 0;
                }
                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity) && isset($quantity_balance)) {
                    $product_details = $this->purchases_model->getProductByCode($item_code);
                    // $unit_cost = $real_unit_cost;
                    $pr_discount      = $this->site->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost        = $this->sma->formatDecimal($unit_cost - $pr_discount, 12);
					$item_net_cost    = $unit_cost;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity, 12);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = 0;
                    $item_tax    = 0;
                    $tax         = '';


                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_cost);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
						//if ($product_details->tax_method != 1) {
                        //    $item_net_cost = $unit_cost - $item_tax;
                        //}
						if($item_unit_quantity != $item_quantity){
							$unit     = $this->site->getUnitByID($item_unit);



					     if($unit->operator=='*'){
						$pr_item_tax = $this->sma->formatDecimal($item_tax * $this->sma->formatDecimal($quantity_received/$unit->operation_value), 12);

						 }else{
						$pr_item_tax = $this->sma->formatDecimal($item_tax * $this->sma->formatDecimal($quantity_received*$unit->operation_value), 12);

						 }
						}else{
						$pr_item_tax = $this->sma->formatDecimal($item_tax * $quantity_received, 12);
                        }
						if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }
                    //if($item_unit==19)
					//$this->sma->print_arrays($_POST);


                    $product_tax += $pr_item_tax;
                    //$subtotal = (($item_net_cost * $quantity_received) + $pr_item_tax);
                    $unit     = $this->site->getUnitByID($item_unit);

					if($item_unit_quantity != $item_quantity){


					if($unit->operator=='*'){

					$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($item_net_cost* $this->sma->formatDecimal($quantity_received/$unit->operation_value));
					$subtotal = (($item_net_cost * $this->sma->formatDecimal($quantity_received/$unit->operation_value)) + $pr_item_tax);
                    $total += $item_net_cost * $this->sma->formatDecimal($quantity_received/$unit->operation_value);
                    $ttotal += $real_unit_cost * $this->sma->formatDecimal($quantity_received/$unit->operation_value);
                    $general_total += $item_net_cost * $item_unit_quantity;
					}else{
					$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($item_net_cost* $this->sma->formatDecimal($quantity_received*$unit->operation_value));
					$subtotal = (($item_net_cost * $this->sma->formatDecimal($quantity_received*$unit->operation_value)) + $pr_item_tax);
                    $total += $item_net_cost * $this->sma->formatDecimal($quantity_received*$unit->operation_value);
                    $ttotal += $real_unit_cost * $this->sma->formatDecimal($quantity_received*$unit->operation_value);
                    $general_total += $item_net_cost * $item_unit_quantity;
					}
					$general_total_net_unit_cost_quantity_discont=$general_total_net_unit_cost_quantity_discont+($item_net_cost*$item_unit_quantity);

					}else{
					$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($item_net_cost*$quantity_received);
					$general_total_net_unit_cost_quantity_discont=$general_total_net_unit_cost_quantity_discont+($item_net_cost*$item_quantity);
					$subtotal = (($item_net_cost * $quantity_received) + $pr_item_tax);
                    $total += $item_net_cost * $quantity_received;
                    $ttotal += $real_unit_cost * $quantity_received;
                    $general_total += $item_net_cost * $item_unit_quantity;

					}

                    if ($this->Settings->isOpen_invoice==1) {
                        $pid=$item_id;
                        $pname=$item_name;
                    }else{
                        $pid=$product_details->id;
                        $pname=$product_details->name;

                    }

                    $item = [
                        'product_id'        => $pid,
                        'product_code'      => $item_code,
                        'product_name'      => $pname,
                        'option_id'         => $item_option,
                        'net_unit_cost'     => $item_net_cost,
                        //'unit_cost'         => $this->sma->formatDecimal($item_net_cost + $item_tax, 12),
                        'unit_cost'         => $this->sma->formatDecimal($item_net_cost, 12),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity'     => $item_unit_quantity,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $this->sma->formatDecimal($subtotal, 12),
                        'expiry'            => $item_expiry,
                        'real_unit_cost'    => $real_unit_cost,
                        'supplier_part_no'  => $supplier_part_no,
                        'date'              => date('Y-m-d', strtotime($date)),
                    ];
					if ($status == 'received' || $status == 'partial') {

					$item['quantity_balance']=$balance_qty;
					$item['quantity_received']=$quantity_received;
					}else{
					$item['quantity_balance']=0;
					$item['quantity_received']=0;
					$item['quantity']=$item_unit_quantity;

					}

					if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
					$receiving_date=NULL;
					if($this->input->post('receiving_date')){
					$receiving_date=$this->sma->fld(trim($this->input->post('receiving_date')));
					}
					if($quantity_received!=0)
					$item['receiving_date']=$receiving_date;


					}


                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {

                        $item  ['grpId']=$inv->grpId ? $inv->grpId : 1;
                        $item  ['coId']=$inv->coId ? $inv->coId : 1;
                        $item  ['brId']=$inv->brId ? $inv->brId : 1;

                    }


                    $items[] = ($item + $gst_data);


                    //$total += $item_net_cost * $item_unit_quantity;

                }
            }

            if (empty($items)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                foreach ($items as $item) {
                    $item['status'] = ($status == 'partial') ? 'received' : $status;
                    $products[]     = $item;
                }
                krsort($products);
            }






            $order_discount = $this->site->calculateDiscount($this->input->post('discount'), ($general_total + $product_tax));
	          if($order_discount>0 || $order_discount<0){
				$partia_ltotal_discount=$this->site->distributeAmountEqually($general_total_net_unit_cost_quantity,$order_discount,$general_total_net_unit_cost_quantity_discont);

			$total_discount = $this->sma->formatDecimal(($partia_ltotal_discount + $product_discount), 12);
			$order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax + $customs_fees + $other_fees - $partia_ltotal_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
			if ($status == 'received' || $status == 'partial')
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping_received + $customs_fees + $other_fees) - $partia_ltotal_discount), 12);
             else
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping + $customs_fees + $other_fees) - $partia_ltotal_discount), 12);
            	 }else{
			$total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 12);
			$order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax + $customs_fees + $other_fees - $order_discount));
            //$total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
            if($product_tax>0)
			$total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax + (($customs_fees + $other_fees)*$this->purchases_model->getTaxRatesActive()->rate/100)), 12);
            else
			$total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);

			if ($status == 'received' || $status == 'partial')
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping_received + $customs_fees + $other_fees) - $order_discount), 12);
            else
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping + $customs_fees + $other_fees) - $order_discount), 12);
            	}


            /*$order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax + $customs_fees + $other_fees - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping + $customs_fees + $other_fees, 12) - $order_discount), 12);
            */

			if($status=='received' || $status=='partial'){
			}else{
				unset($_POST['costCenterId']);
				unset($_POST['costCenterAmount']);
			}


			$data           = ['reference_no' => $reference,
                'supplier_id'                 => $supplier_id,
                'supplier'                    => $supplier,
                'warehouse_id'                => $warehouse_id,
                'note'                        => $note,
                'total'                       => $this->sma->formatDecimal($ttotal, 12),
                'product_discount'            => $product_discount,
                'order_discount_id'           => $this->input->post('discount'),
                'order_discount'              => $order_discount,
                'total_discount'              => $total_discount,
                'product_tax'                 => $product_tax,
                'order_tax_id'                => $this->input->post('order_tax'),
                'order_tax'                   => $order_tax,
                'total_tax'                   => $total_tax,
                'shipping'                    => $this->sma->formatDecimal($shipping, 12),
                'shipping_received'                    => $this->sma->formatDecimal($shipping_received, 12),
                'grand_total'                 => $grand_total,
                'status'                      => $status,
                'receiving_status'                      => $receiving_status,
                'updated_by'                  => $this->session->userdata('user_id'),
                'updated_at'                  => date('Y-m-d H:i:s'),
                'payment_term'                => $payment_term,
                'due_date'                    => $due_date,
				'customs_fees'                    => $this->sma->formatDecimal($customs_fees, 12),
                'Other_fees'                    => $this->sma->formatDecimal($other_fees, 12),
				'supply_date'                => $supply_date,
				'currency_code'                => $currencyCode,
				'currency_rate'                => $this->sma->formatDecimal($currencyRate),
				'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),




            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$inv->grpId ? $inv->grpId : 1;
                $data  ['coId']=$inv->coId ? $inv->coId : 1;
                $data  ['brId']=$inv->brId ? $inv->brId : 1;

            }
            if ($date) {
                $data['date'] = $date;
            }
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }


            //$this->sma->print_arrays($id, $data, $products,$general_total_net_unit_cost_quantity,$general_total_net_unit_cost_quantity_discont);
        }

		//if($inv->status =='received'){
		if($inv->status =='received' && $inv->receiving_status =='received'){
		    $this->session->set_flashdata('error', lang('Invoice_already_received'));
                        redirect($_SERVER['HTTP_REFERER']);

		}else{


        if ($this->form_validation->run() == true && (($inv->receiving_status=='pending' && $status=='received') ? $this->purchases_model->updateStatus($id, $status, $note,$receiving_status) : $this->purchases_model->updatePurchase($id, $data, $products,$general_total_net_unit_cost_quantity,$general_total_net_unit_cost_quantity_discont))) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line('purchase_added'));
            admin_redirect('purchases');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv']   = $inv;
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-' . $this->Settings->disable_editing . ' days')) && $this->data['inv']->date != '0000-00-00 00:00:00') {
                    $this->session->set_flashdata('error', sprintf(lang('purchase_x_edited_older_than_x_days'), $this->Settings->disable_editing));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $inv_items = $this->purchases_model->getAllPurchaseItems($id);
            //$this->sma->print_arrays($inv_items);
            // krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {

				$quantity_received=0;
				if($item->status=='received')
                $quantity_received=$item->quantity_received;
				$row                   = $this->site->getProductByID($item->product_id);

                $unit_first=null;
                $code_first=null;
                $qty_first=null;




                if($row->purchase_unit!=$item->product_unit_id){
                    $warehouse      = $this->site->getWarehouseByID($item->warehouse_id);
                    $price_group_id=$warehouse->price_group_id;


                    if($pr_group_price_first                   = $this->site->getProductGroupPriceWithUnit($item->product_id,$price_group_id,$item->product_unit_id)){
                        $unit_first=$pr_group_price_first->product_unit_id;
                        $code_first=$pr_group_price_first->product_code;
                        $unit     = $this->site->getUnitByID($unit_first);
                        if($unit->operator=='*'){
                            $qty_first=$this->sma->formatDecimal($item->unit_quantity*$unit->operation_value);
                        }else{
                            $qty_first=$this->sma->formatDecimal($item->unit_quantity/$unit->operation_value);
                        }



                    }
                    //$this->sma->print_arrays($row,$item,$pr_group_price_first);
                }
                //$item->product_unit_id;
                if(!$row){
                    $item->product_id=$item->product_code;
                    $row->id              = $item->product_id;
                    $row->code            = $item->product_code;
                    $row->name            = $item->product_name;

                }
                //$this->sma->print_arrays($row);
                $row->expiry           = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
                $row->quantity_received    = $quantity_received;
                $row->base_quantity    = $item->quantity;
                $row->base_unit        = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_cost   = $item->unit_cost;//$row->cost ? $row->cost : $item->unit_cost;
                $row->unit             = $item->product_unit_id;
                $row->qty              = $item->unit_quantity;
                $row->oqty             = $item->quantity;
                $row->supplier_part_no = $item->supplier_part_no;
                $row->received         = $item->quantity_received ? $item->quantity_received : $item->quantity;
                $row->quantity_balance = $item->quantity_balance + ($item->quantity - $row->received);
                $row->discount         = $item->discount ? $item->discount : '0';
                $options               = $this->purchases_model->getProductOptions($row->id);
                $row->option           = $item->option_id;
                $row->real_unit_cost   = $item->real_unit_cost;
                $row->cost             = $this->sma->formatDecimal($item->net_unit_cost + ($item->item_discount / $item->quantity));
                $row->tax_rate         = $item->tax_rate_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6);
                //unset($row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5);
                //unset($row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                //unset($row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                //unset($row->alert_quantity, $row->image, $row->subcategory_id, $row->track_quantity, $row->barcode_symbology);
                //unset($row->category_id, $row->warehouse, $row->promotion, $row->promo_price, $row->start_date, $row->end_date);
                //$units    = $this->site->getUnitByID($row->base_unit);
                $units    = $this->site->getUnitsByBUIDV2($row->base_unit);

                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                //$tax_rate = $this->site->getTaxRateByIDV2($row->tax_rate);
                $ri       = $this->Settings->item_addition ? $row->id : $c;
                if($unit_first){
                $ri=$c;
                $row->unit        = $unit_first;

                }


                $pr[$ri] = ['id' => $c, 'item_id' => $row->id,'code_first' => $code_first,'unit_first' => $unit_first, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $c++;
            }

            $co_type = $this->companies_model->getCompanyByID($inv->supplier_id);

		// $this->sma->print_arrays(strlen(json_encode($pr)));
        //$this->sma->print_arrays($pr);

		 if ($this->Settings->accounting){
			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
			}

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            $this->data['warehouses'] = $this->site->getAllWarehouses($inv->grpId,$inv->coId,$inv->brId);
            $this->data['suppliers']  = $this->site->getAllCompanies('supplier',$inv->grpId,$inv->coId,$inv->brId);
            $this->data['tax_rates']          = $inv->brId ?  $this->site->getTaxBybrId($inv->brId) : $this->site->getAllTaxRatesActive();

        }else{
                $this->data['warehouses'] = $this->site->getAllWarehouses();
                $this->data['suppliers']  = $this->site->getAllCompanies('supplier');
                $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            }


			$this->data['inv_items']  = json_encode($pr);
            //$this->sma->print_arrays($this->data['inv_items']);
           $this->data['co_type']         = $co_type->co_type;
            $this->data['id']         = $id;
            $this->data['purchase']   = $this->purchases_model->getPurchaseByID($id);
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['currencies'] = $this->site->getAllCurrencies();
			//$this->data['tax_rates']  = $this->site->getAllTaxRates();


            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc                 = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('purchases'), 'page' => lang('purchases')], ['link' => '#', 'page' => lang('edit_purchase')]];
            $meta               = ['page_title' => lang('edit_purchase'), 'bc' => $bc];
            $this->page_construct('purchases/edit', $meta, $this->data);
        }
		}
    }

    public function edit_expense($id = null)
    {
        $this->sma->checkPermissions('edit_expense', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference', lang('reference'), 'required');
        $this->form_validation->set_rules('amount', lang('amount'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = [
                'date'         => $date,
                'reference'    => $this->input->post('reference'),
                'amount'       => $this->input->post('grand_total'),
                'order_tax_id'       => $this->input->post('order_tax'),
                'total_tax'       => $this->input->post('total_tax'),
                'grand_total'       => $this->input->post('grand_total'),
                'total'       => $this->input->post('amount'),
                'note'         => $this->input->post('note', true),
                'category_id'  => $this->input->post('category', true),
                'warehouse_id' => $this->input->post('warehouse', true),
                'account_id' => $this->input->post('account_id', true),
                'cost_id' => $this->input->post('cost_id', true),
                'tax_method' => $this->input->post('tax_method', true),
                'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),
            ];
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->sma->print_arrays($data);
        } elseif ($this->input->post('edit_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->updateExpense($id, $data)) {
            $this->session->set_flashdata('message', lang('expense_updated'));
            admin_redirect('purchases/expenses');
        } else {

            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

            }

            $this->data['expense']    = $this->purchases_model->getExpenseByID($id);
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['categories'] = $this->purchases_model->getExpenseCategories();
            if ($this->Settings->accounting){
			$this->data['expenses_accounts'] = $this->accounts_model->getAccountsByCategory('المصروفات');
            $this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
			}
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->load->view($this->theme . 'purchases/edit_expense', $this->data);
        }
    }

    public function edit_payment($id = null)
    {
        $this->sma->checkPermissions('edit_payment', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'purchase_id'  => $this->input->post('purchase_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'currency'         => $this->input->post('currency') ? $this->input->post('currency') : $this->Settings->default_currency,
				'currency_rate'         => $this->input->post('exchange_rate'),
				'note'         => $this->sma->clear_tags($this->input->post('note')),
                'created_by'   => $this->session->userdata('user_id'),
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

			$payment_type="";
			if ($this->Settings->accounting){
			if($this->input->post('paid_by')=="cash")
			$payment_type=$this->input->post('cash_account');
			if($this->input->post('paid_by')=="CC")
			$payment_type=$this->input->post('cc_account');
			if($this->input->post('paid_by')=="Cheque")
			$payment_type=$this->input->post('cheque_account');
			if($this->input->post('paid_by')=="bank_transfer")
			$payment_type=$this->input->post('transfer_account');


			}


            //$this->sma->print_arrays($id, $payment,$payment_type);
        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->updatePayment($id, $payment,$payment_type)) {
            $this->session->set_flashdata('message', lang('payment_updated'));
            admin_redirect('purchases');
        } else {

            $purchase=$this->purchases_model->getPaymentByID($id);

			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['currencies'] = $purchase->brId ? $this->site->getCurrenciesBybrId($purchase->brId) : $this->site->getAllCurrencies();
            }else{
                $this->data['currencies'] = $this->site->getAllCurrencies();
            }



			$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));


            $this->data['payment']  = $purchase;
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/edit_payment', $this->data);
        }
    }


	public function edit_payment_application($id = null)
    {
        $this->sma->checkPermissions('edit_payment', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('applied_amount', lang("Applied_amount"), 'required');
        $this->form_validation->set_rules('code', lang('code'), 'required');
        if ($this->form_validation->run() == true) {
            if ($this->input->post('expected_payment_date')) {
                $expected_payment_date = $this->sma->fld(trim($this->input->post('expected_payment_date')));
            } else {
                $expected_payment_date = null;
            }
            $payment = [
                'code'         => $this->input->post('code'),
                'purchase_id'  => $this->input->post('purchase_id'),
                'payment_schedule' => $this->input->post('payment_schedule'),
                'applied_amount'       => $this->input->post('applied_amount'),
                'applied_schedule'      => $this->input->post('applied_schedule'),
                'remarks'    => $this->input->post('remarks'),
                'expected_payment_date'        => $expected_payment_date,
                'purpose'         => $this->sma->clear_tags($this->input->post('purpose')),

            ];


            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('edit_payment_application')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->updatePaymentApplication($id, $payment)) {
            $this->session->set_flashdata('message', lang('Payment_application_has_been_successfully_updated'));
            admin_redirect('purchases');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['payment_application']  = $this->purchases_model->getPaymentApplicationByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/edit_payment_application', $this->data);
        }
    }


    public function edit_expense_payment($id = null)
    {
        $this->sma->checkPermissions('edit_payment', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'expense_id'  => $this->input->post('expense_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->sma->clear_tags($this->input->post('note')),
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }



            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->updateExpensePayment($id, $payment)) {
            $this->session->set_flashdata('message', lang('payment_updated'));
            admin_redirect('purchases/expenses');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['payment']  = $this->purchases_model->getExpensePaymentByID($id);
            $this->data['modal_js'] = $this->site->modal_js();

            $this->load->view($this->theme . 'purchases/edit_expense_payment', $this->data);
        }
    }

    public function email($purchase_id = null)
    {
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);
        $this->form_validation->set_rules('to', $this->lang->line('to') . ' ' . $this->lang->line('email'), 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', $this->lang->line('subject'), 'trim|required');
        $this->form_validation->set_rules('cc', $this->lang->line('cc'), 'trim|valid_emails');
        $this->form_validation->set_rules('bcc', $this->lang->line('bcc'), 'trim|valid_emails');
        $this->form_validation->set_rules('note', $this->lang->line('message'), 'trim');

        if ($this->form_validation->run() == true) {
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $to      = $this->input->post('to');
            $subject = $this->input->post('subject');
            if ($this->input->post('cc')) {
                $cc = $this->input->post('cc');
            } else {
                $cc = null;
            }
            if ($this->input->post('bcc')) {
                $bcc = $this->input->post('bcc');
            } else {
                $bcc = null;
            }
            $supplier = $this->site->getCompanyByID($inv->supplier_id);
            $this->load->library('parser');
            $parse_data = [
                'reference_number' => $inv->reference_no,
                'contact_person'   => $supplier->name,
                'company'          => $supplier->company,
                'site_link'        => base_url(),
                'site_name'        => $this->Settings->site_name,
                'logo'             => '<img src="' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '" alt="' . $this->Settings->site_name . '"/>',
            ];
            $msg        = $this->input->post('note');
            $message    = $this->parser->parse_string($msg, $parse_data);
            $attachment = $this->pdf($purchase_id, null, 'S');

            try {
                if ($this->sma->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                    delete_files($attachment);
                    $this->db->update('purchases', ['status' => 'ordered'], ['id' => $purchase_id]);
                    $this->session->set_flashdata('message', $this->lang->line('email_sent'));
                    admin_redirect('purchases');
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('error', $e->getMessage());
                redirect($_SERVER['HTTP_REFERER']);
            }
        } elseif ($this->input->post('send_email')) {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->session->set_flashdata('error', $this->data['error']);
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if (file_exists('./themes/' . $this->Settings->theme . '/admin/views/email_templates/purchase.html')) {
                $purchase_temp = file_get_contents('themes/' . $this->Settings->theme . '/admin/views/email_templates/purchase.html');
            } else {
                $purchase_temp = file_get_contents('./themes/default/admin/views/email_templates/purchase.html');
            }
            $this->data['subject'] = ['name' => 'subject',
                'id'                         => 'subject',
                'type'                       => 'text',
                'value'                      => $this->form_validation->set_value('subject', lang('purchase_order') . ' (' . $inv->reference_no . ') ' . lang('from') . ' ' . $this->Settings->site_name),
            ];
            $this->data['note'] = ['name' => 'note',
                'id'                      => 'note',
                'type'                    => 'text',
                'value'                   => $this->form_validation->set_value('note', $purchase_temp),
            ];
            $this->data['supplier'] = $this->site->getCompanyByID($inv->supplier_id);

            $this->data['id']       = $purchase_id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'purchases/email', $this->data);
        }
    }

    public function email_payment($id = null)
    {
        $this->sma->checkPermissions('email_payment', true);
        $payment               = $this->purchases_model->getPaymentByID($id);
        $inv                   = $this->purchases_model->getPurchaseByID($payment->purchase_id);
        $supplier              = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['inv']     = $inv;
        $this->data['payment'] = $payment;
        if (!$supplier->email) {
            $this->sma->send_json(['msg' => lang('update_supplier_email')]);
        }
        $this->data['supplier']   = $supplier;
        $this->data['warehouse']  = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']        = $inv;
        $this->data['payment']    = $payment;
        $this->data['page_title'] = lang('payment_note');
        $html                     = $this->load->view($this->theme . 'purchases/payment_note', $this->data, true);

        $html = str_replace(['<i class="fa fa-2x">&times;</i>', 'modal-', '<p>&nbsp;</p>', '<p style="border-bottom: 1px solid #666;">&nbsp;</p>', '<p>' . lang('stamp_sign') . '</p>'], '', $html);
        $html = preg_replace("/<img[^>]+\>/i", '', $html);
        // $html = '<div style="border:1px solid #DDD; padding:10px; margin:10px 0;">'.$html.'</div>';

        $this->load->library('parser');
        $parse_data = [
            'stylesheet' => '<link href="' . $this->data['assets'] . 'styles/helpers/bootstrap.min.css" rel="stylesheet"/>',
            'name'       => $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name,
            'email'      => $supplier->email,
            'heading'    => lang('payment_note') . '<hr>',
            'msg'        => $html,
            'site_link'  => base_url(),
            'site_name'  => $this->Settings->site_name,
            'logo'       => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>',
        ];
        $msg     = file_get_contents('./themes/' . $this->Settings->theme . '/admin/views/email_templates/email_con.html');
        $message = $this->parser->parse_string($msg, $parse_data);
        $subject = lang('payment_note') . ' - ' . $this->Settings->site_name;

        if ($this->sma->send_email($supplier->email, $subject, $message)) {
            $this->sma->send_json(['msg' => lang('email_sent')]);
        } else {
            $this->sma->send_json(['msg' => lang('email_failed')]);
        }
    }

    public function expense_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->purchases_model->deleteExpense($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('expenses_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->sma->checkPermissions('xls_expense',true);
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('expenses'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('amount'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('note'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('created_by'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $expense = $this->purchases_model->getExpenseByID($id);
                        $user    = $this->site->getUser($expense->created_by);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($expense->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $expense->reference);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatMoney($expense->amount));
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $expense->note);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $user->first_name . ' ' . $user->last_name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'expenses_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_expense_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function expense_note($id = null)
    {

		$expense                  = $this->purchases_model->getExpenseByID($id);
        $this->data['user']       = $this->site->getUser($expense->created_by);
        $this->data['category']   = $expense->category_id ? $this->purchases_model->getExpenseCategoryByID($expense->category_id) : null;
        $this->data['warehouse']  = $expense->warehouse_id ? $this->site->getWarehouseByID($expense->warehouse_id) : null;
        $this->data['expense']    = $expense;
        $this->data['page_title'] = $this->lang->line('expense_note');
        $this->load->view($this->theme . 'purchases/expense_note', $this->data);
    }

     public function expense_note_return($reference = null)
    {
        $expense                  = $this->purchases_model->getExpenseByReference($reference);
        $this->data['user']       = $this->site->getUser($expense->created_by);
        $this->data['category']   = $expense->category_id ? $this->purchases_model->getExpenseCategoryByID($expense->category_id) : null;
        $this->data['warehouse']  = $expense->warehouse_id ? $this->site->getWarehouseByID($expense->warehouse_id) : null;
        $this->data['expense']    = $expense;
        $this->data['page_title'] = $this->lang->line('expense_note');
        $this->load->view($this->theme . 'purchases/expense_note', $this->data);
    }

    /* -------------------------------------------------------------------------------- */

    public function expenses($id = null)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('expenses')]];
        $meta                = ['page_title' => lang('expenses'), 'bc' => $bc];
        $this->page_construct('purchases/expenses', $meta, $this->data);
    }

    public function getExpenses()
    {
        $this->sma->checkPermissions('expenses');


        $detail_link = anchor('admin/purchases/expense_note/$1', '<i class="fa fa-file-text-o"></i> ' . lang('expense_note'), 'data-toggle="modal" data-target="#myModal"');
        $payments_link    = anchor('admin/purchases/expense_payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('admin/purchases/add_expense_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link   = anchor('admin/purchases/edit_expense/$1', '<i class="fa fa-edit"></i> ' . lang('edit_expense'), 'data-toggle="modal" data-target="#myModal"');
        //$attachment_link = '<a href="'.base_url('assets/uploads/$1').'" target="_blank"><i class="fa fa-chain"></i></a>';
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line('delete_expense') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('purchases/delete_expense/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_expense') . '</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $payments_link . '</li>
            <li>' . $add_payment_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';

        $this->load->library('datatables');

		if ($this->Settings->accounting){

        $this->datatables
            //->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment", false)
            ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('gl_accounts')}.accountName as category, grand_total, paid, (grand_total-paid) as balance, payment_status, attachment", false)
            ->from('expenses')
            ->join('users', 'users.id=expenses.created_by', 'left')
            ->join('gl_accounts', 'gl_accounts.id=expenses.account_id', 'left')
            ->group_by('expenses.id');
		}else{

		$this->datatables
            //->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment", false)
            ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, grand_total, paid, (grand_total-paid) as balance, payment_status, attachment", false)
            ->from('expenses')
            ->join('users', 'users.id=expenses.created_by', 'left')
            ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
            ->group_by('expenses.id');

		}

        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }

        //$this->datatables->edit_column("attachment", $attachment_link, "attachment");
        $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }


    public function getPurchases($warehouse_id = null)
    {
       // $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
        //    $warehouse_id = $user->warehouse_id;
        }


		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
        $coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
        $brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
        $start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $reference_no    = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $warehouse_id   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $status   = $this->input->get('status') ? $this->input->get('status') : null;
        $payment_status   = $this->input->get('payment_status') ? $this->input->get('payment_status') : null;

		if ($start_date && $end_date) {
			$start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
		   }



        $detail_link      = anchor('admin/purchases/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('purchase_details'));
        $duplicate_link    = anchor('admin/purchases/add?purchase_id=$1', '<i class="fa fa-plus-circle"></i> ' . lang('Duplicate_purchase'));
        $payments_link    = anchor('admin/purchases/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('admin/purchases/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');

		if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
		$payment_application_link    = anchor('admin/purchases/payment_application/$1', '<i class="fa fa-money"></i> ' . lang('View_payment_application'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_application_link = anchor('admin/purchases/add_payment_application/$1', '<i class="fa fa-money"></i> ' . lang('Add_payment_application'), 'data-toggle="modal" data-target="#myModal"');
        $receiving_receipt_link = anchor('admin/purchases/receiving_receipt/$1', '<i class="fa fa-file-text-o"></i> ' . lang('Receiving_Receipt'), 'data-toggle="modal" data-target="#myModal"');

		}

		$email_link       = anchor('admin/purchases/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_purchase'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link        = anchor('admin/purchases/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_purchase'));
        $pdf_link         = anchor('admin/purchases/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $print_barcode    = anchor('admin/products/print_barcodes/?purchase=$1', '<i class="fa fa-print"></i> ' . lang('print_barcodes'));
        $return_link      = anchor('admin/purchases/return_purchase/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_purchase'));
        $delete_link      = "<a href='#' class='po' title='" . $this->lang->line('delete_purchase') . "' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('purchases/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_purchase') . '</a>';
        $cancel_link      = "<a href='#' class='po' title='<b>" . $this->lang->line('cancel_purchase') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('purchases/cancel/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('cancel_purchase') . '</a>';

		if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){

		$action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $duplicate_link . '</li>
            <li>' . $payments_link . '</li>
            <li>' . $add_payment_link . '</li>
            <li>' . $payment_application_link . '</li>
            <li>' . $add_payment_application_link . '</li>
            <li>' . $receiving_receipt_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $email_link . '</li>
            <li>' . $print_barcode . '</li>
            <li>' . $return_link . '</li>


        </ul>
    </div></div>';
		}else{
		$action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $duplicate_link . '</li>
            <li>' . $payments_link . '</li>
            <li>' . $add_payment_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $email_link . '</li>
            <li>' . $print_barcode . '</li>
            <li>' . $return_link . '</li>


        </ul>
    </div></div>';
		}
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';
		//<li>' . $pdf_link . '</li>
        //<li>' . $delete_link . '</li>

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, supplier, status, grand_total, paid, discount_earned, (grand_total-(paid+discount_earned))  as balance, payment_status, attachment")
                ->from('purchases')
                ->where('warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, supplier, status, grand_total, paid, discount_earned, (grand_total-(paid+discount_earned))  as balance, payment_status, attachment")
                ->from('purchases');
        }
        // $this->datatables->where('status !=', 'returned');

        if ($grpId) {
            $this->datatables->where($this->db->dbprefix('purchases') . '.grpId', $grpId);
        }
        if ($coId) {
          $this->datatables->where($this->db->dbprefix('purchases') . '.coId', $coId);
        }
        if ($brId) {
            $this->datatables->where($this->db->dbprefix('purchases') . '.brId', $brId);
        }
        if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Supplier) {
            $this->datatables->where('supplier_id', $this->session->userdata('user_id'));
        }


		if ($start_date && $end_date) {
                 $this->db->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
           }

          if ($reference_no) {
                $this->datatables->where($this->db->dbprefix('purchases') . '.reference_no', $reference_no);
            }
            if ($supplier) {
                $this->datatables->where($this->db->dbprefix('purchases') . '.supplier_id', $supplier);
            }
              if ($status) {
                $this->datatables->where($this->db->dbprefix('purchases') . '.status', $status);
            }
             if ($payment_status) {
                $this->datatables->where($this->db->dbprefix('purchases') . '.payment_status', $payment_status);
            }


        $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }

    public function getSupplierCost($supplier_id, $product)
    {
        switch ($supplier_id) {
            case $product->supplier1:
                $cost = $product->supplier1price > 0 ? $product->supplier1price : $product->cost;
                break;
            case $product->supplier2:
                $cost = $product->supplier2price > 0 ? $product->supplier2price : $product->cost;
                break;
            case $product->supplier3:
                $cost = $product->supplier3price > 0 ? $product->supplier3price : $product->cost;
                break;
            case $product->supplier4:
                $cost = $product->supplier4price > 0 ? $product->supplier4price : $product->cost;
                break;
            case $product->supplier5:
                $cost = $product->supplier5price > 0 ? $product->supplier5price : $product->cost;
                break;
            default:
                $cost = $product->cost;
        }
        return $cost;
    }

    /* ------------------------------------------------------------------------- */

    public function index($warehouse_id = null)
    {
        $this->sma->checkPermissions();

		$warehouse_id   = $this->input->post('warehouse') ? $this->input->post('warehouse') : null;





        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

             $this->data['billers']    = $this->input->post('brId') ? $this->site->getBillerBybrId($this->input->post('brId')) : $this->site->getBillerBybrId($this->session->userdata('brId'));
             if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
             $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
             $this->data['warehouse_id'] = $warehouse_id;
             $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
                }else{
                $this->data['warehouses']   = $this->site->getWarehouseByID($this->session->userdata('warehouse_id'));
                $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
                $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;

             }


            }else{
                if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
                    $this->data['warehouses']   = $this->site->getAllWarehouses();
                    $this->data['warehouse_id'] = $warehouse_id;
                    $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
                } else {
                    $this->data['warehouses']   = $this->site->getWarehouseByID($this->session->userdata('warehouse_id'));
                    $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
                    $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
                }


            }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('purchases')]];
        $meta = ['page_title' => lang('purchases'), 'bc' => $bc];
        $this->page_construct('purchases/index', $meta, $this->data);
    }

    /* ----------------------------------------------------------------------------- */

    public function modal_view($purchase_id = null)
    {
        $this->sma->checkPermissions('index', true);

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->purchases_model->getPurchaseByID($purchase_id);
		if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows']            = $this->purchases_model->getAllPurchaseItemsWithOutZeros($purchase_id);
        $this->data['units']           = $this->site->getAllUnits();
        $this->data['supplier']        = $this->site->getCompanyByID($inv->supplier_id);


		//$this->sma->print_arrays($this->purchases_model->getAllPurchaseItemsReturn($purchase_id));

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
		{
			$this->data['branch'] = $this->site->getMainBranchesByID($inv->brId);
		}

        $this->data['warehouse']       = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']             = $inv;
        $this->data['payments']        = $this->purchases_model->getPaymentsForPurchase($purchase_id);
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['updated_by']      = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->purchases_model->getAllPurchaseItemsReturnWithOutZeros($purchase_id) : null;
		$this->data['return_payments'] = $inv->return_id ? $this->purchases_model->getInvoicePayments($inv->return_id) : null;


        $this->load->view($this->theme . 'purchases/modal_view', $this->data);
    }

	public function debit_note_view($purchase_id = null)
    {
        $this->sma->checkPermissions('index', true);

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->purchases_model->getPurchaseByID($purchase_id);
		if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows']            = $this->purchases_model->getAllPurchaseItems($purchase_id);
        $this->data['units']           = $this->site->getAllUnits();
        $this->data['supplier']        = $this->site->getCompanyByID($inv->supplier_id);


		//$this->sma->print_arrays($this->purchases_model->getAllPurchaseItemsReturn($purchase_id));

        $this->data['warehouse']       = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']             = $inv;
        $this->data['payments']        = $this->purchases_model->getPaymentsForPurchase($purchase_id);
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['updated_by']      = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->purchases_model->getAllPurchaseItemsReturn($purchase_id) : null;
		$this->data['return_payments'] = $inv->return_id ? $this->purchases_model->getInvoicePayments($inv->return_id) : null;


        $this->load->view($this->theme . 'purchases/debit_note_view', $this->data);
    }

	public function receiving_receipt($purchase_id = null)
    {
        $this->sma->checkPermissions('index', true);

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->purchases_model->getPurchaseByID($purchase_id);
		if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }

        $this->data['rows']            = $this->purchases_model->getAllPurchaseItems($purchase_id);
        $this->data['units']           = $this->site->getAllUnits();
        $this->data['supplier']        = $this->site->getCompanyByID($inv->supplier_id);

		//$this->sma->print_arrays($this->data['supplier']);

        $this->data['warehouse']       = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']             = $inv;
        $this->data['payments']        = $this->purchases_model->getPaymentsForPurchase($purchase_id);
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['updated_by']      = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->purchases_model->getAllPurchaseItems($inv->return_id) : null;
		$this->data['return_payments'] = $inv->return_id ? $this->purchases_model->getInvoicePayments($inv->return_id) : null;


        $this->load->view($this->theme . 'purchases/receiving_receipt', $this->data);
    }


    public function payment_note($id = null)
    {
        $this->sma->checkPermissions('payments', true);
        $payment                  = $this->purchases_model->getPaymentByID($id);
        $inv                      = $this->purchases_model->getPurchaseByID($payment->purchase_id);
        $this->data['supplier']   = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse']  = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']        = $inv;
        $this->data['payment']    = $payment;
        $this->data['page_title'] = $this->lang->line('payment_note');

        $this->load->view($this->theme . 'purchases/payment_note', $this->data);
    }

    public function payment_application_note($id = null)
    {
        $this->sma->checkPermissions('payments', true);
        $payment                  = $this->purchases_model->getPaymentApplicationByID($id);
        $inv                      = $this->purchases_model->getPurchaseByID($payment->purchase_id);
        $this->data['supplier']   = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse']  = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']        = $inv;
        $this->data['payment_application']    = $payment;
        $this->data['page_title'] = $this->lang->line('Payment_application');

        $this->load->view($this->theme . 'purchases/payment_application_note', $this->data);
    }

    public function expense_payment_note($id = null)
    {
        $this->sma->checkPermissions('payments', true);
        $payment                  = $this->purchases_model->getExpensePaymentByID($id);
        $inv                      = $this->purchases_model->getExpenseByID($payment->expense_id);
        $this->data['supplier']   = '';//$this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse']  = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']        = $inv;
        $this->data['payment']    = $payment;
        $this->data['page_title'] = $this->lang->line('payment_note');

        $this->load->view($this->theme . 'purchases/expense_payment_note', $this->data);
    }

    /* -------------------------------------------------------------------------------- */

    public function payments($id = null)
    {
        $this->sma->checkPermissions(false, true);

        $this->data['payments'] = $this->purchases_model->getPurchasePayments($id);
        $this->data['inv']      = $this->purchases_model->getPurchaseByID($id);
        $this->load->view($this->theme . 'purchases/payments', $this->data);
    }

   public function payment_application($id = null)
    {
         $this->sma->checkPermissions('payments', true);

        $this->data['payment_application'] = $this->purchases_model->getPurchasePaymentApplication($id);
        $this->data['inv']      = $this->purchases_model->getPurchaseByID($id);
        $this->load->view($this->theme . 'purchases/payment_application', $this->data);
    }

    public function expense_payments($id = null)
    {
         $this->sma->checkPermissions('payments', true);

        $this->data['payments'] = $this->purchases_model->getExpensePayments($id);
        $this->data['inv']      = $this->purchases_model->getExpenseByID($id);
        $this->load->view($this->theme . 'purchases/expense_payments', $this->data);
    }

    /* ----------------------------------------------------------------------------- */

    //generate pdf and force to download

    public function pdf($purchase_id = null, $view = null, $save_bufffer = null)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->purchases_model->getPurchaseByID($purchase_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['rows']            = $this->purchases_model->getAllPurchaseItems($purchase_id);
        $this->data['supplier']        = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse']       = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['inv']             = $inv;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->purchases_model->getAllPurchaseItems($inv->return_id) : null;
        $this->data['units']           = $this->site->getAllUnits();

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
		{
			$this->data['branch'] = $this->site->getMainBranchesByID($inv->brId);
		}

        $this->data['payments']        = $this->purchases_model->getPaymentsForPurchase($purchase_id);
        $this->data['updated_by']      = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['return_payments'] = $inv->return_id ? $this->purchases_model->getInvoicePayments($inv->return_id) : null;


        $name                          = $this->lang->line('purchase') . '_' . str_replace('/', '_', $inv->reference_no) . '.pdf';
        $html                          = $this->load->view($this->theme . 'purchases/pdf', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }


        //$this->sma->print_arrays($html);
        if ($view) {
            echo $html;
            die();
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        }
        $this->sma->generate_pdf($html, $name);
    }

    /* -------------------------------------------------------------------------------- */

    public function purchase_actions()
    {



		if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete');
                    $general_total_net_unit_cost_quantity=0;
					foreach ($_POST['val'] as $id) {
					/*$purchase_items_temp = $this->site->getAllPurchaseItems($id);
					foreach ($purchase_items_temp as $oitem_temp) {
					if ($purchase->order_discount>0 || $purchase->order_discount<0) {
						$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($oitem_temp->quantity_received*$oitem_temp->real_unit_cost);
					}else{
						$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($oitem_temp->quantity_received*$oitem_temp->net_unit_cost);
					}
					}
					//$this->sma->print_arrays($id, $general_total_net_unit_cost_quantity);
					*/
                        $this->purchases_model->deletePurchase($id,$general_total_net_unit_cost_quantity);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('purchases_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'combine') {
                    $html = $this->combine_pdf($_POST['val']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('purchases'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('supplier'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchase_status'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $purchase = $this->purchases_model->getPurchaseByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($purchase->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $purchase->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $purchase->supplier);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, lang($purchase->status));
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($purchase->grand_total));
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'purchases_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_purchase_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------- */

    public function purchase_by_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('warehouse', $this->lang->line('warehouse'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line('supplier'), 'required');
        $this->form_validation->set_rules('userfile', $this->lang->line('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $quantity  = 'quantity';
            $product   = 'product';
            $unit_cost = 'unit_cost';
            $tax_rate  = 'tax_rate';
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('po',$this->input->post('brId') ? $this->input->post('brId') : null);
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = null;
            }
            $warehouse_id     = $this->input->post('warehouse');
            $supplier_id      = $this->input->post('supplier');
            $status           = $this->input->post('status');
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->site->getCompanyByID($supplier_id);
            $supplier         = $supplier_details->company && $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            $note             = $this->sma->clear_tags($this->input->post('note'));

            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;

            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');

                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('purchases/purchase_by_csv');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }

                $titles = array_shift($arrResult);

                $keys  = ['code', 'net_unit_cost', 'quantity', 'variant', 'item_tax_rate', 'discount', 'expiry'];
                $final = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if (isset($csv_pr['code']) && isset($csv_pr['net_unit_cost']) && isset($csv_pr['quantity'])) {
                        if ($product_details = $this->purchases_model->getProductByCode($csv_pr['code'])) {
                            if ($csv_pr['variant']) {
                                $item_option = $this->purchases_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
                                if (!$item_option) {
                                    $this->session->set_flashdata('error', lang('pr_not_found') . ' ( ' . $product_details->name . ' - ' . $csv_pr['variant'] . ' ). ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            } else {
                                $item_option     = json_decode('{}');
                                $item_option->id = null;
                            }

                            $item_code        = $csv_pr['code'];
                            $item_net_cost    = $this->sma->formatDecimal($csv_pr['net_unit_cost']);
                            $item_quantity    = $csv_pr['quantity'];
                            $quantity_balance = $csv_pr['quantity'];
                            $item_tax_rate    = $csv_pr['item_tax_rate'];
                            $item_discount    = $csv_pr['discount'];
                            $item_expiry      = isset($csv_pr['expiry']) ? $this->sma->fsd($csv_pr['expiry']) : null;

                            $pr_discount      = $this->site->calculateDiscount($item_discount, $item_net_cost, 12);
                            $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_quantity), 12);
                            $product_discount += $pr_item_discount;

                            $tax         = '';
                            $pr_item_tax = 0;
                            $unit_cost   = $item_net_cost - $pr_discount;
                            $gst_data    = [];
                            $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->purchases_model->getTaxRateByName($item_tax_rate) : $this->site->getTaxRateByID($product_details->tax_rate));
                            if ($tax_details) {
                                $ctax     = $this->site->calculateTax($product_details, $tax_details, $unit_cost);
                                $item_tax = $ctax['amount'];
                                $tax      = $ctax['tax'];
                                if ($product_details->tax_method != 1) {
                                    $item_net_cost = $unit_cost - $item_tax;
                                }
                                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity, 12);
                                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                                    $total_cgst += $gst_data['cgst'];
                                    $total_sgst += $gst_data['sgst'];
                                    $total_igst += $gst_data['igst'];
                                }
                            }

                            $product_tax += $pr_item_tax;
                            $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_quantity) + $pr_item_tax), 12);
                            $unit     = $this->site->getUnitByID($product_details->unit);
                            $product  = [
                                'product_id'        => $product_details->id,
                                'product_code'      => $item_code,
                                'product_name'      => $product_details->name,
                                'option_id'         => $item_option->id,
                                'net_unit_cost'     => $item_net_cost,
                                'quantity'          => $item_quantity,
                                'product_unit_id'   => $product_details->unit,
                                'product_unit_code' => $unit->code,
                                'unit_quantity'     => $item_quantity,
                                'quantity_balance'  => $quantity_balance,
                                'warehouse_id'      => $warehouse_id,
                                'item_tax'          => $pr_item_tax,
                                'tax_rate_id'       => $tax_details ? $tax_details->id : null,
                                'tax'               => $tax,
                                'discount'          => $item_discount,
                                'item_discount'     => $pr_item_discount,
                                'expiry'            => $item_expiry,
                                'subtotal'          => $subtotal,
                                'date'              => date('Y-m-d', strtotime($date)),
                                'status'            => $status,
                                'unit_cost'         => $this->sma->formatDecimal(($item_net_cost + $item_tax), 12),
                                'real_unit_cost'    => $this->sma->formatDecimal(($item_net_cost + $item_tax + $pr_discount), 12),
                            ];

                            $products[] = ($product + $gst_data);
                            $total += $this->sma->formatDecimal(($item_net_cost * $item_quantity), 12);
                        } else {
                            $this->session->set_flashdata('error', $this->lang->line('pr_not_found') . ' ( ' . $csv_pr['code'] . ' ). ' . $this->lang->line('line_no') . ' ' . $rw);
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                        $rw++;
                    }
                }
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('discount') ? $this->input->post('order_discount') : null, ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 12);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 12);
            $data           = ['reference_no' => $reference,
                'date'                        => $date,
                'supplier_id'                 => $supplier_id,
                'supplier'                    => $supplier,
                'warehouse_id'                => $warehouse_id,
                'note'                        => $note,
                'total'                       => $total,
                'product_discount'            => $product_discount,
                'order_discount_id'           => $this->input->post('discount'),
                'order_discount'              => $order_discount,
                'total_discount'              => $total_discount,
                'product_tax'                 => $product_tax,
                'order_tax_id'                => $this->input->post('order_tax'),
                'order_tax'                   => $order_tax,
                'total_tax'                   => $total_tax,
                'shipping'                    => $this->sma->formatDecimal($shipping, 12),
                'grand_total'                 => $grand_total,
                'status'                      => $status,
                'created_by'                  => $this->session->userdata('username'),
            ];
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPurchase($data, $products)) {
            $this->session->set_flashdata('message', $this->lang->line('purchase_added'));
            admin_redirect('purchases');
        } else {
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));


            $this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['suppliers']  = $this->site->getAllCompanies('supplier');

            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['ponumber']   = ''; // $this->site->getReference('po');

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('purchases'), 'page' => lang('purchases')], ['link' => '#', 'page' => lang('add_purchase_by_csv')]];
            $meta = ['page_title' => lang('add_purchase_by_csv'), 'bc' => $bc];
            $this->page_construct('purchases/purchase_by_csv', $meta, $this->data);
        }
    }


    public function purchase_by_excel()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('warehouse', $this->lang->line('warehouse'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line('supplier'), 'required');
        $this->form_validation->set_rules('userfile', $this->lang->line('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $quantity  = 'quantity';
            $product   = 'product';
            $unit_cost = 'unit_cost';
            $tax_rate  = 'tax_rate';
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('po',$this->input->post('brId') ? $this->input->post('brId') : null);
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = null;
            }
            $warehouse_id     = $this->input->post('warehouse');
            $supplier_id      = $this->input->post('supplier');
            $status           = $this->input->post('status');
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->site->getCompanyByID($supplier_id);
            $supplier         = $supplier_details->company && $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            $note             = $this->sma->clear_tags($this->input->post('note'));

            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;


            /*if (isset($_FILES['userfile'])) {
                $this->load->library('upload');

                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('purchases/purchase_by_excel');
                }

                $csv = $this->upload->file_name;

                */
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                //$config['allowed_types'] = 'csv';
                $config['allowed_types'] = 'xlsx|xls';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;

                $data1['attachment'] = $csv;

                /*$arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys  = ['code', 'net_unit_cost', 'quantity', 'variant', 'item_tax_rate', 'discount', 'expiry'];
                $final = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                */
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = array('error' => $this->upload->display_errors());
                } else {
                    $data = array('upload_data' => $this->upload->data());
                }
                if(empty($error)){
                  if (!empty($data['upload_data']['file_name'])) {
                    $import_xls_file = $data['upload_data']['file_name'];
                } else {
                    $import_xls_file = 0;
                }
                $inputFileName = $this->digital_upload_path . $import_xls_file;


                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);



                    $flag = true;
                    $i=0;
                    foreach ($allDataInSheet as $value) {
                      if($flag){
                        $flag =false;
                        continue;
                      }
                      //$inserdata[$i]['#'] = $value['A'];$keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];

                      $inserdata[$i]['code'] = trim($value['A']);
                      $inserdata[$i]['net_unit_cost'] = trim($value['B']);
                      $inserdata[$i]['quantity'] = trim($value['C']);
                      $inserdata[$i]['variant'] = trim($value['D']);
                      $inserdata[$i]['item_tax_rate'] = trim($value['E']);
                      $inserdata[$i]['discount'] = trim($value['F']);
                      $inserdata[$i]['expiry'] = trim($value['G']);
                      $i++;
                    }
                $rw = 2;

                foreach ($inserdata as $csv_pr) {
                    if (isset($csv_pr['code']) && isset($csv_pr['net_unit_cost']) && isset($csv_pr['quantity'])) {
                        if ($product_details = $this->purchases_model->getProductByCode($csv_pr['code'])) {
                            if ($csv_pr['variant']) {
                                $item_option = $this->purchases_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
                                if (!$item_option) {
                                    $this->session->set_flashdata('error', lang('pr_not_found') . ' ( ' . $product_details->name . ' - ' . $csv_pr['variant'] . ' ). ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            } else {
                                $item_option     = json_decode('{}');
                                $item_option->id = null;
                            }

                            $item_code        = $csv_pr['code'];
                            $item_net_cost    = $this->sma->formatDecimal($csv_pr['net_unit_cost']);
                            $item_quantity    = $csv_pr['quantity'];
                            $quantity_balance = $csv_pr['quantity'];
                            $item_tax_rate    = $csv_pr['item_tax_rate'];
                            $item_discount    = $csv_pr['discount'];
                            $item_expiry      = isset($csv_pr['expiry']) ? $this->sma->fsd($csv_pr['expiry']) : null;

                            $pr_discount      = $this->site->calculateDiscount($item_discount, $item_net_cost, 12);
                            $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_quantity), 12);
                            $product_discount += $pr_item_discount;

                            $tax         = '';
                            $pr_item_tax = 0;
                            $unit_cost   = $item_net_cost - $pr_discount;
                            $gst_data    = [];
                            $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->purchases_model->getTaxRateByName($item_tax_rate) : $this->site->getTaxRateByID($product_details->tax_rate));
                            if ($tax_details) {
                                $ctax     = $this->site->calculateTax($product_details, $tax_details, $unit_cost);
                                $item_tax = $ctax['amount'];
                                $tax      = $ctax['tax'];
                                if ($product_details->tax_method != 1) {
                                    $item_net_cost = $unit_cost - $item_tax;
                                }
                                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity, 12);
                                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                                    $total_cgst += $gst_data['cgst'];
                                    $total_sgst += $gst_data['sgst'];
                                    $total_igst += $gst_data['igst'];
                                }
                            }

                            $product_tax += $pr_item_tax;
                            $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_quantity) + $pr_item_tax), 12);
                            $unit     = $this->site->getUnitByID($product_details->unit);
                            $product  = [
                                'product_id'        => $product_details->id,
                                'product_code'      => $item_code,
                                'product_name'      => $product_details->name,
                                'option_id'         => $item_option->id,
                                'net_unit_cost'     => $item_net_cost,
                                'quantity'          => $item_quantity,
                                'product_unit_id'   => $product_details->unit,
                                'product_unit_code' => $unit->code,
                                'unit_quantity'     => $item_quantity,
                                'quantity_balance'  => $quantity_balance,
                                'warehouse_id'      => $warehouse_id,
                                'item_tax'          => $pr_item_tax,
                                'tax_rate_id'       => $tax_details ? $tax_details->id : null,
                                'tax'               => $tax,
                                'discount'          => $item_discount,
                                'item_discount'     => $pr_item_discount,
                                'expiry'            => $item_expiry,
                                'subtotal'          => $subtotal,
                                'date'              => date('Y-m-d', strtotime($date)),
                                'status'            => $status,
                                'unit_cost'         => $this->sma->formatDecimal(($item_net_cost + $item_tax), 12),
                                'real_unit_cost'    => $this->sma->formatDecimal(($item_net_cost + $item_tax + $pr_discount), 12),
                            ];



                            $products[] = ($product + $gst_data);
                            $total += $this->sma->formatDecimal(($item_net_cost * $item_quantity), 12);
                        } else {
                            $this->session->set_flashdata('error', $this->lang->line('pr_not_found') . ' ( ' . $csv_pr['code'] . ' ). ' . $this->lang->line('line_no') . ' ' . $rw);
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                        $rw++;
                    }
                }
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('discount') ? $this->input->post('order_discount') : null, ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 12);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 12);
            $data           = ['reference_no' => $reference,
                'date'                        => $date,
                'supplier_id'                 => $supplier_id,
                'supplier'                    => $supplier,
                'warehouse_id'                => $warehouse_id,
                'note'                        => $note,
                'total'                       => $total,
                'product_discount'            => $product_discount,
                'order_discount_id'           => $this->input->post('discount'),
                'order_discount'              => $order_discount,
                'total_discount'              => $total_discount,
                'product_tax'                 => $product_tax,
                'order_tax_id'                => $this->input->post('order_tax'),
                'order_tax'                   => $order_tax,
                'total_tax'                   => $total_tax,
                'shipping'                    => $this->sma->formatDecimal($shipping, 12),
                'grand_total'                 => $grand_total,
                'status'                      => $status,
                'created_by'                  => $this->session->userdata('username'),
            ];
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPurchase($data, $products)) {
            $this->session->set_flashdata('message', $this->lang->line('purchase_added'));
            admin_redirect('purchases');
        } else {
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));


            $this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['suppliers']  = $this->site->getAllCompanies('supplier');

            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['ponumber']   = ''; // $this->site->getReference('po');

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('purchases'), 'page' => lang('purchases')], ['link' => '#', 'page' => lang('add_purchase_by_excel')]];
            $meta = ['page_title' => lang('add_purchase_by_excel'), 'bc' => $bc];
            $this->page_construct('purchases/purchase_by_excel', $meta, $this->data);
        }
    }


	public function returns()
    {


        $this->sma->checkPermissions('return_purchases');


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       $this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['suppliers']  = $this->site->getAllCompanies('supplier');

            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['purchase_items']   = $this->purchases_model->getAllPurchaseItemsToReturned();
			//$this->sma->print_arrays($this->data['purchase_items']);


		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('return_purchase'), 'page' => lang('return_purchase')], ['link' => '#', 'page' => lang('return_purchase')]];
        $meta                = ['page_title' => lang('return_purchase'), 'bc' => $bc];
        $this->page_construct('purchases/returns', $meta, $this->data);

    }


    public function getReturns()
	{
        ini_set('memory_limit','2048M');
		$this->load->library('datatables');
		$this->datatables
			->select('purchase_id,warehouses.name as warehouse_name,product_code,product_name,units.name as Uname,quantity_received,purchase_id as id')
            ->join('units', 'units.id=purchase_items.product_unit_id', 'left')
            ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left');
            //->group_by('purchase_id,warehouses.name,product_code,product_name')
            //->order_by('product_name', 'asc');
			$this->datatables->where('quantity_received !=', '');
			$this->datatables->where('purchase_id !=', '');
			$this->datatables->where('unit_quantity !=', 0);
			$this->datatables->where('status', 'received');



		$this->datatables->from('purchase_items');
		echo $this->datatables->generate();
	}

    public function return_purchase($id = null,$product_code = null)
    {
        $this->sma->checkPermissions('return_purchases');

		//$this->sma->print_arrays($product_code);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $purchase = $this->purchases_model->getPurchaseByID($id);
        $purchase_items_quantity = $this->site->getAllPurchaseItemsQuantity($id);
        /*if ($purchase->return_id) {
            $this->session->set_flashdata('error', lang('purchase_already_returned'));
            redirect($_SERVER['HTTP_REFERER']);
        }*/
        $this->form_validation->set_rules('return_surcharge', lang('return_surcharge'), 'required');

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('rep',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $purchase->brId : null);
			$return_purchase_ex_ref='';
            $return_ex_id='';
            $return_surcharge_ex_ref='';
            $return_ex_s_id='';
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $return_surcharge = $this->input->post('return_surcharge') ? $this->input->post('return_surcharge') : 0;
            $note             = $this->sma->clear_tags($this->input->post('note'));
            $supplier_details = $this->site->getCompanyByID($purchase->supplier_id);

            $total_purchase_value=($purchase->total+$purchase->product_discount);

			$total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $original_shipping=$purchase->shipping;
			if($purchase->shipping_received > 0)
			$original_shipping=$purchase->shipping_received;
			$total_shipping=0;
			$total_quantity=0;
			$general_total_net_unit_cost_quantity=0;
			$gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            for ($r = 0; $r < $i; $r++) {
                    $purchase_items = $this->site->getAllPurchaseItemQuantityRealCost($id,$_POST['product_id'][$r]);
					//$return_shipping=$this->site->distributeAmountEqually($this->sma->formatDecimal($purchase_items->quantity * $purchase_items->real_unit_cost, 12),$original_shipping,$purchase->total);
					if($purchase_items->quantity == $_POST['quantity'][$r])
					$total_purchase_value=$total_purchase_value-$this->sma->formatDecimal($purchase_items->quantity * $purchase_items->real_unit_cost, 12);

			}
            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_code          = $_POST['product'][$r];
                $purchase_item_id   = $_POST['purchase_item_id'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && !empty($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $real_unit_cost     = $this->sma->formatDecimal($_POST['real_unit_cost'][$r], 12);
                $unit_cost          = $this->sma->formatDecimal($_POST['unit_cost'][$r], 12);
                $item_unit_quantity = (0 - $_POST['quantity'][$r]);
                $item_expiry        = $_POST['expiry'][$r]           ?$_POST['expiry'][$r] : '';
                $item_tax_rate      = $_POST['product_tax'][$r]      ?$_POST['product_tax'][$r] : null;
                $item_discount      = $_POST['product_discount'][$r] ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = (0 - $_POST['product_base_quantity'][$r]);
                $item_name          = $_POST['product_name'][$r];

                if($_POST['quantity'][$r]!=0){

                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->purchases_model->getProductByCode($item_code);

                    $item_type        = $product_details->type;
                    $item_name        = $product_details->name ? $product_details->name : $item_name;
                    $pr_discount      = $this->site->calculateDiscount($item_discount, $unit_cost);
                    $unit_cost        = $this->sma->formatDecimal($unit_cost - $pr_discount, 12);
                    $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_unit_quantity), 12);
                    $product_discount += $pr_item_discount;
                    $item_net_cost = $unit_cost;
                    $pr_item_tax   = $item_tax   = 0;
                    $tax           = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_cost);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        //if ($product_details->tax_method != 1) {
                        //    $item_net_cost = $unit_cost - $item_tax;
                        //}
                        $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

					$purchase_items = $this->site->getAllPurchaseItemQuantityRealCost($id,$item_id);
					//$return_shipping=$this->site->distributeAmountEqually($this->sma->formatDecimal($purchase_items->quantity * $purchase_items->real_unit_cost, 12),$original_shipping,$total_purchase_value);
					$return_shipping=$this->site->distributeAmountEqually($this->sma->formatDecimal($item_quantity * $real_unit_cost, 12),$purchase_items->shipping,$this->sma->formatDecimal($purchase_items->quantity_received * $purchase_items->real_unit_cost, 12));
					//$this->sma->print_arrays($purchase_items->quantity_received * $purchase_items->real_unit_cost);
					//$this->sma->print_arrays($this->sma->formatDecimal($purchase_items->quantity * $purchase_items->real_unit_cost, 12));
					//$this->sma->print_arrays($this->sma->formatDecimal($item_quantity * $real_unit_cost, 12));
					//$this->sma->print_arrays($purchase_items->shipping);
					//if($purchase_items->quantity == $_POST['quantity'][$r])
					//	$return_shipping=0;
					$total_shipping=$this->sma->formatDecimal($total_shipping+$return_shipping,12);
					//$this->sma->print_arrays($total_shipping);



                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_unit_quantity) + $pr_item_tax), 12);
                    $unit     = $this->site->getUnitByID($item_unit);
					$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($item_net_cost*$item_quantity);
					$total_quantity=$total_quantity+$item_quantity;
					if($item_quantity==0)
						$item_net_cost=$purchase_items->net_unit_cost;

					//if($rp=$this->purchases_model->getSumPurchaseItemsCost($purchase->warehouse_id, $item_id)){

					//}

                   //$this->sma->print_arrays($this->purchases_model->getSumPurchaseItemsCost($purchase->warehouse_id, $item_id));


                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'option_id'         => $item_option,
                        'net_unit_cost'     => $item_net_cost,
                        'unit_cost'         => $this->sma->formatDecimal($item_net_cost + $item_tax, 12),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity'     => $item_unit_quantity,
                        'quantity_balance'  => $item_quantity,
                        'warehouse_id'      => $purchase->warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $this->sma->formatDecimal($subtotal, 12),
                        'real_unit_cost'    => $real_unit_cost,
                        'purchase_item_id'  => $purchase_item_id,
                        'status'            => 'received',
                        'return_shipping'            => $this->sma->formatDecimal(0-$return_shipping,12),//$this->sma->formatDecimal($purchase_items->shipping + $return_shipping,12),
                    ];

                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {

                        $product  ['grpId']=$purchase->grpId;
                        $product  ['coId']=$purchase->coId;
                        $product  ['brId']=$purchase->brId;


                    }

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_cost * $item_unit_quantity), 12);
					$generaltotal += $this->sma->formatDecimal(($real_unit_cost * $item_unit_quantity), 12);
                }
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }




			if($total_shipping!=0)
			{
				$return_purchase_ex_ref=$this->site->getReference('ex',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $purchase->brId : null);


				//$this->sma->print_arrays($total_shipping);
				if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data_ex = [
                'date'         => $date,
                'reference'    => $return_purchase_ex_ref,
                'amount'       => abs($total_shipping),
                'grand_total'       => abs($total_shipping),
                'total'       => abs($total_shipping),
				'payment_status'       => 'paid',
				'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $purchase->warehouse_id,
			    'other' => 1,
			];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {

                        $data_ex  ['grpId']=$purchase->grpId;
                        $data_ex  ['coId']=$purchase->coId;
                        $data_ex  ['brId']=$purchase->brId;


                    }
			if($this->purchases_model->addExpense($data_ex,"1")){
			$expense                  = $this->purchases_model->getExpenseByReference($return_purchase_ex_ref);
			$return_ex_id=$expense->id;
			}



			//$original_shipping=0;




			}

			if($return_surcharge!=0){

					$return_surcharge_ex_ref=$this->site->getReference('ex',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $purchase->brId : null);



				if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data_ex = [
                'date'         => $date,
                'reference'    => $return_surcharge_ex_ref,
                'amount'       => $return_surcharge,
                'grand_total'       => $return_surcharge,
                'total'       => $return_surcharge,
                'payment_status'       => 'paid',
				'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $purchase->warehouse_id,
				'other' => 1,
			];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {

                        $data_ex  ['grpId']=$purchase->grpId;
                        $data_ex  ['coId']=$purchase->coId;
                        $data_ex  ['brId']=$purchase->brId;


                    }
			if($this->purchases_model->addExpense($data_ex,"1")){
			$expense                  = $this->purchases_model->getExpenseByReference($return_surcharge_ex_ref);
			$return_ex_s_id=$expense->id;
			}


			}

			$order_discount = $this->site->calculateDiscount($this->input->post('order_discount_h'), ($total + $product_tax));
            $order_discount=-1*$order_discount;
			//$order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($general_total_net_unit_cost_quantity + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 12);
            $order_tax      = ($this->input->post('order_tax') ? $this->input->post('order_tax') : 0);//$this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $order_tax=-1*$order_tax;
			$total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
            //$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($return_surcharge) - $order_discount - $original_shipping), 12);
            //$grand_total    = $this->sma->formatDecimal(($total + $total_tax - $this->sma->formatDecimal($return_surcharge) - $order_discount), 12);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax  - $order_discount), 12);
            $data           = ['date' => $date,
                'purchase_id'         => $id,
                'reference_no'        => $purchase->reference_no,
                'supplier_id'         => $purchase->supplier_id,
                'supplier'            => $purchase->supplier,
                'warehouse_id'        => $purchase->warehouse_id,
                'note'                => $note,
                'total'               => $generaltotal,
                'product_discount'    => $product_discount,
                'order_discount_id'   => ($this->input->post('discount') ? $this->input->post('order_discount_h') : null),
                'order_discount'      => $order_discount,
                'total_discount'      => $total_discount,
                'product_tax'         => $product_tax,
                'order_tax_id'        => $purchase->order_tax_id,
                'order_tax'           => $order_tax,
                'total_tax'           => $total_tax,
                'shipping'            => $total_shipping,
                'surcharge'           => $this->sma->formatDecimal($return_surcharge, 12),
                'grand_total'         => $grand_total,
                'created_by'          => $this->session->userdata('user_id'),
                'return_purchase_ref' => $reference,
                'return_purchase_ex_ref' => $return_purchase_ex_ref,
                'return_surcharge_ex_ref' => $return_surcharge_ex_ref,
                'return_ex_id' => $return_ex_id,
                'return_ex_s_id' => $return_ex_s_id,
                'status'              => 'returned',
                'payment_status'      => $purchase->payment_status == 'paid' ? 'due' : 'pending',
				'tax_active_rate'             => $this->purchases_model->getTaxRatesActive()->rate ? $this->purchases_model->getTaxRatesActive()->rate : 0,
                'currency_code'                => $purchase->currency_code,
				'currency_rate'                => $purchase->currency_rate,
				'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),



            ];
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {

                        $data  ['grpId']=$purchase->grpId;
                        $data  ['coId']=$purchase->coId;
                        $data  ['brId']=$purchase->brId;
                        $data  ['purchase_no']=$this->site->getMainBranchesByID($data['brId'])->branch_prefix ? $this->site->getMainBranchesByID($data['brId'])->branch_prefix.'-'.$this->site->getCountPurchasesBybrid($data['brId'])->count : $this->site->getCountPurchasesBybrid($data['brId'])->count;



                    }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }


             //$this->sma->print_arrays($data, $products,$general_total_net_unit_cost_quantity);
			 //$this->sma->print_arrays($this->purchases_model->getSumSaleItemsCostComboOS('5', '318'));

        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPurchase($data, $products,$general_total_net_unit_cost_quantity)) {
            $this->session->set_flashdata('message', lang('return_purchase_added'));
            admin_redirect('purchases');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $purchase;
            if ($this->data['inv']->status != 'received' && $this->data['inv']->status != 'partial') {
                $this->session->set_flashdata('error', lang('purchase_status_x_received'));
                redirect($_SERVER['HTTP_REFERER']);
            }
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-' . $this->Settings->disable_editing . ' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang('purchase_x_edited_older_than_x_days'), $this->Settings->disable_editing));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $inv_items = $this->purchases_model->getAllPurchaseItems($id,$product_code);
            // krsort($inv_items);
			/*$purchase_return = $this->purchases_model->getPurchaseByPurchase_id($id);
			foreach ($purchase_return as $item_return) {
			$inv_items_ret = $this->purchases_model->getAllPurchaseItems($item_return->id,$product_code);

			}
			*/
            //$this->sma->print_arrays($inv_items);

            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
				$return_unit_quantity=0;
                if($this->site->getProductByID($item->product_id)){
                    $row                   = $this->site->getProductByID($item->product_id);

                }else{
                    $row=null;

                }
				$inv_items_ret = $this->purchases_model->getAllPurchaseReturnItems($id,$item->product_code);
				//$this->sma->print_arrays($item->product_code,$inv_items_ret);
            if ($purchase->return_id) {

			}

            if (!$row) {
                    $row             = json_decode('{}');


                        $item->product_id=$item->product_code;
                        $row->id              = $item->product_id;

                        $row->code            = $item->product_code;
                        $row->name            = $item->product_name;
                }


                $row->expiry           = (($item->expiry && $item->expiry != '0000-00-00') ? $this->sma->hrsd($item->expiry) : '');
                $row->base_quantity    = $inv_items_ret ? $item->quantity + $inv_items_ret->unit_quantity : $item->quantity;
                $row->base_unit        = $row->unit ? $row->unit : $item->product_unit_id;
                $row->base_unit_cost   = $row->cost ? $row->cost : $item->unit_cost;
                $row->unit             = $item->product_unit_id;
                $row->qty              = $inv_items_ret ? $item->unit_quantity + $inv_items_ret->unit_quantity : $item->unit_quantity;
                $row->oqty             = $inv_items_ret ? $item->unit_quantity + $inv_items_ret->unit_quantity : $item->unit_quantity;
                $row->purchase_item_id = $item->id;
                $row->supplier_part_no = $item->supplier_part_no;
                $row->received         = $item->quantity_received ? ($inv_items_ret ? $item->quantity_received + $inv_items_ret->unit_quantity : $item->quantity_received) : $item->quantity;
                $row->quantity_balance = $item->quantity_balance + ($item->quantity - $row->received);
                $row->discount         = $item->discount ? $item->discount : '0';
                $options               = $this->purchases_model->getProductOptions($row->id);
                $row->option           = !empty($item->option_id) ? $item->option_id : '';
                $row->real_unit_cost   = $item->real_unit_cost;
                $row->cost             = $this->sma->formatDecimal($item->net_unit_cost + ($item->item_discount / $item->quantity), 12);
                $row->tax_rate         = $item->tax_rate_id;
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);
                $units    = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $ri       = $this->Settings->item_addition ? $row->id : $c;
                if($row->unit){
                    $ri=$c;
                   // $row->unit        = $unit_first;

                    }


                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'row' => $row, 'units' => $units, 'tax_rate' => $tax_rate, 'options' => $options];

                $c++;
            }

			if ($this->Settings->accounting){
			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
			}

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }

            //$this->sma->print_arrays($pr);
            $this->data['inv_items'] = json_encode($pr);
            $this->data['id']        = $id;
            $this->data['reference'] = '';
            //$this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $bc                      = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('purchases'), 'page' => lang('purchases')], ['link' => '#', 'page' => lang('return_purchase')]];
            $meta                    = ['page_title' => lang('return_purchase'), 'bc' => $bc];
            $this->page_construct('purchases/return_purchase', $meta, $this->data);
        }
    }

    /* --------------------------------------------------------------------------- */

    public function suggestions()
    {
        $term        = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $supplier_id = $this->input->get('supplier_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        if($warehouse_id){
            $warehouse      = $this->site->getWarehouseByID($warehouse_id);
            $price_group_id=$warehouse->price_group_id;
        }else{
            $price_group_id=$this->Settings->price_group;
        }

        $unit_first=null;
		$code_first=null;


        if ($pr_group_price_first = $this->site->getProductGroupPriceByCode($term, $price_group_id)){
			$term=$this->site->getProductByID($pr_group_price_first->product_id)->code;
			$unit_first=$pr_group_price_first->product_unit_id;
		    $code_first=$pr_group_price_first->product_code;
		}

        $rows = $this->purchases_model->getProductNames($term );
        if ($rows) {
            $r = 0;
            foreach ($rows as $row) {
                $c                    = uniqid(mt_rand(), true);
                $option               = false;
                $row->item_tax_method = $row->tax_method;
                $options              = $this->purchases_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->purchases_model->getProductOptionByID($option_id) : current($options);
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt       = json_decode('{}');
                    $opt->cost = 0;
                    $option_id = false;
                }
                $row->option           = $option_id;
                $row->supplier_part_no = '';
                if ($row->supplier1 == $supplier_id) {
                    $row->supplier_part_no = $row->supplier1_part_no;
                } elseif ($row->supplier2 == $supplier_id) {
                    $row->supplier_part_no = $row->supplier2_part_no;
                } elseif ($row->supplier3 == $supplier_id) {
                    $row->supplier_part_no = $row->supplier3_part_no;
                } elseif ($row->supplier4 == $supplier_id) {
                    $row->supplier_part_no = $row->supplier4_part_no;
                } elseif ($row->supplier5 == $supplier_id) {
                    $row->supplier_part_no = $row->supplier5_part_no;
                }
                if ($opt->cost != 0) {
                    $row->cost = $opt->cost;
                }
                $row->cost             = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                $row->real_unit_cost   = $row->cost;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->base_unit_cost   = $row->cost;
                if($unit_first)
					$row->unit       = $unit_first;
				else
				$row->unit             = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->new_entry        = 1;
                $row->expiry           = '';
                $row->qty              = 1;
                $row->quantity_balance = '';
                $row->discount         = '0';
                //if($code_first)
				//	$row->code       = $code_first;


                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);

                $units    = $this->site->getUnitsByBUID($row->base_unit);
                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				$tax_rate = '';//$this->site->getTaxRateByID($row->tax_rate);

                $pr[] = ['id' => sha1($c . $r),'code_first' => $code_first,'unit_first' => $unit_first, 'item_id' => $row->id, 'label' => $row->second_name ? $row->name.' - '. $row->second_name . ' (' . $row->code . ')' : $row->name . ' (' . $row->code . ')',
                    'row'     => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    public function update_status($id)
    {
        $this->sma->checkPermissions('update_status',true);
        $this->form_validation->set_rules('status', lang('status'), 'required');

        if ($this->form_validation->run() == true) {




            $status = $this->input->post('status');
			if($status=='received1'){
				$status='received';
				$receiving_status='pending';
			}else{
			$receiving_status='received';
			}
            $note   = $this->sma->clear_tags($this->input->post('note'));


            //$this->sma->print_arrays($id, $status, $note,$receiving_status);


        } elseif ($this->input->post('update')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'sales');
        }

        if ($this->form_validation->run() == true && $this->purchases_model->updateStatus($id, $status, $note,$receiving_status)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'sales');
        } else {
            $this->data['inv']      = $this->purchases_model->getPurchaseByID($id);
			$this->data['warehouse']       = $this->site->getWarehouseByID($this->data['inv']->warehouse_id);

            $this->data['returned'] = false;
            if ($this->data['inv']->status == 'returned' || $this->data['inv']->return_id) {
                $this->data['returned'] = true;
            }
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'purchases/update_status', $this->data);
        }
    }


    public function view($purchase_id = null)
    {
        $this->sma->checkPermissions('index');

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->purchases_model->getPurchaseByID($purchase_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['rows']            = $this->purchases_model->getAllPurchaseItems($purchase_id);
        $this->data['supplier']        = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['warehouse']       = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']             = $inv;
        $this->data['payments']        = $this->purchases_model->getPaymentsForPurchase($purchase_id);
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['updated_by']      = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->purchases_model->getAllPurchaseItems($inv->return_id) : null;

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('purchases'), 'page' => lang('purchases')], ['link' => '#', 'page' => lang('view')]];
        $meta = ['page_title' => lang('view_purchase_details'), 'bc' => $bc];
        $this->page_construct('purchases/view', $meta, $this->data);
    }

    public function view_return($id = null)
    {
        $this->sma->checkPermissions('return_purchases');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->purchases_model->getReturnByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode']   = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['supplier']  = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['payments']  = $this->purchases_model->getPaymentsForPurchase($id);
        $this->data['user']      = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']       = $inv;
        $this->data['rows']      = $this->purchases_model->getAllReturnItems($id);
        $this->data['purchase']  = $this->purchases_model->getPurchaseByID($inv->purchase_id);
        $this->load->view($this->theme . 'purchases/view_return', $this->data);
    }

	/*-------------------------reports-------------------*/
	    public function daily_purchases($warehouse_id = null, $year = null, $month = null, $pdf = null, $user_id = null)
    {
        $this->sma->checkPermissions('daily_purchases',true);
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config              = [
            'show_next_prev' => true,
            'next_prev_url'  => admin_url('reports/daily_purchases/' . ($warehouse_id ? $warehouse_id : 0)),
            'month_type'     => 'long',
            'day_type'       => 'long',
        ];

        $config['template'] = '{table_open}<div class="row"><div class="col-sm-9 col-md-9"><div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div></div></div>{/table_close}';

        $this->load->library('calendar', $config);
        $purchases = $user_id ? $this->reports_model->getStaffDailyPurchases($user_id, $year, $month, $warehouse_id) : $this->reports_model->getDailyPurchases($year, $month, $warehouse_id);

        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $daily_purchase[$purchase->date] = "<div class='row'><div class='col-sm-9 col-md-9'><div class='table-responsive'><table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang('discount') . '</td><td>' . $this->sma->formatMoney($purchase->discount) . '</td></tr><tr><td>' . lang('shipping') . '</td><td>' . $this->sma->formatMoney($purchase->shipping) . '</td></tr><tr><td>' . lang('VAT_Amount') . '</td><td>' . $this->sma->formatMoney($purchase->tax1+$purchase->tax2) . '</td></tr><tr><td>' . lang('total') . '</td><td>' . $this->sma->formatMoney($purchase->total) . '</td></tr></table></div></div></div>';
            }
        } else {
            $daily_purchase = [];
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_purchase);
        $this->data['year']     = $year;
        $this->data['month']    = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/daily', $this->data, true);
            $name = lang('daily_purchases') . '_' . $year . '_' . $month . '.pdf';
            $html = str_replace('<p class="introtext">' . lang('reports_calendar_text') . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->site->getAllWarehouses();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('daily_purchases_report')]];
        $meta                        = ['page_title' => lang('daily_purchases_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('reports/daily_purchases', $meta, $this->data);
    }

    public function daily_purchases_excel($warehouse_id = null, $year = null, $month = null, $user_id = null)
    {
        $this->sma->checkPermissions('daily_purchases', true);
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }

        $purchases = $user_id ? $this->reports_model->getStaffDailyPurchases($user_id, $year, $month, $warehouse_id) : $this->reports_model->getDailyPurchases($year, $month, $warehouse_id);

        $sel_warehouse = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $warehouse_name = $sel_warehouse ? $sel_warehouse->name : lang('all_warehouses');

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('daily_purchases'));

        $is_arabic = ($this->Settings->user_language == 'arabic' || $this->session->userdata('language') == 'arabic');
        if ($is_arabic) {
            $this->excel->getActiveSheet()->setRightToLeft(true);
        }

        // Title row
        $this->excel->getActiveSheet()->mergeCells('A1:E1');
        $this->excel->getActiveSheet()->SetCellValue('A1', lang('daily_purchases') . ' (' . $warehouse_name . ')');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        // Subtitle / Period info
        $this->excel->getActiveSheet()->mergeCells('A2:E2');
        $this->excel->getActiveSheet()->SetCellValue('A2', lang('month') . ': ' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . ' | ' . lang('warehouse') . ': ' . $warehouse_name);
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12)->setBold(false);

        // Table Header
        $header_row = 4;
        $this->excel->getActiveSheet()->SetCellValue('A' . $header_row, lang('date'));
        $this->excel->getActiveSheet()->SetCellValue('B' . $header_row, lang('discount'));
        $this->excel->getActiveSheet()->SetCellValue('C' . $header_row, lang('shipping'));
        $this->excel->getActiveSheet()->SetCellValue('D' . $header_row, lang('VAT_Amount'));
        $this->excel->getActiveSheet()->SetCellValue('E' . $header_row, lang('total'));

        // Header style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '428BCA'],
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];
        $this->excel->getActiveSheet()->getStyle('A' . $header_row . ':E' . $header_row)->applyFromArray($headerStyle);
        $this->excel->getActiveSheet()->getRowDimension($header_row)->setRowHeight(25);

        $row = $header_row + 1;
        $total_discount = 0;
        $total_shipping = 0;
        $total_tax      = 0;
        $total_grand    = 0;

        $borderStyle = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => 'E0E0E0'],
                ],
            ],
        ];

        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $day_num = str_pad($purchase->date, 2, '0', STR_PAD_LEFT);
                $formatted_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $day_num;
                $tax = (float)$purchase->tax1 + (float)($purchase->tax2 ?? 0);
                $discount = (float)($purchase->discount ?? 0);
                $shipping = (float)($purchase->shipping ?? 0);
                $grand_total = (float)($purchase->total ?? 0);

                $this->excel->getActiveSheet()->SetCellValue('A' . $row, $formatted_date);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $discount);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $shipping);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $tax);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $grand_total);

                // Alignments and number formats
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('B' . $row . ':E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->getStyle('B' . $row . ':E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $this->excel->getActiveSheet()->getStyle('A' . $row . ':E' . $row)->applyFromArray($borderStyle);

                $total_discount += $discount;
                $total_shipping += $shipping;
                $total_tax      += $tax;
                $total_grand    += $grand_total;

                $row++;
            }
        }

        // Totals row
        $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('total'));
        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $total_discount);
        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $total_shipping);
        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $total_tax);
        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $total_grand);

        $totalRowStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'F2F2F2'],
            ],
            'borders' => [
                'top' => [
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '333333'],
                ],
                'bottom' => [
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '333333'],
                ],
                'left' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
                'right' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];
        $this->excel->getActiveSheet()->getStyle('A' . $row . ':E' . $row)->applyFromArray($totalRowStyle);
        $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('B' . $row . ':E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('B' . $row . ':E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        // Column widths
        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);

        $filename = 'daily_purchases_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . ($warehouse_id ? '_wh' . $warehouse_id : '');
        $this->load->helper('excel');
        create_excel($this->excel, $filename);
    }

    public function monthly_purchases($warehouse_id = null, $year = null, $pdf = null, $user_id = null)
    {
        $this->sma->checkPermissions('monthly_purchases',true);
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error']     = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year']      = $year;
        $this->data['purchases'] = $user_id ? $this->reports_model->getStaffMonthlyPurchases($user_id, $year, $warehouse_id) : $this->reports_model->getMonthlyPurchases($year, $warehouse_id);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/monthly', $this->data, true);
            $name = lang('monthly_purchases') . '_' . $year . '.pdf';
            $html = str_replace('<p class="introtext">' . lang('reports_calendar_text') . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->site->getAllWarehouses();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('monthly_purchases_report')]];
        $meta                        = ['page_title' => lang('monthly_purchases_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('reports/monthly_purchases', $meta, $this->data);
    }
    public function purchases()
    {
        $this->sma->checkPermissions('purchases',true,'reports');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
            $this->data['users']      = $this->input->post('brId') ? $this->site->getUsersBybrId($this->input->post('brId')) : $this->site->getUsersBybrId($this->session->userdata('brId'));

        }else{
            $this->data['users']      = $this->reports_model->getStaff();
            $this->data['warehouses'] = $this->site->getAllWarehouses();

        }

        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('purchases_report')]];
        $meta                     = ['page_title' => lang('purchases_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('reports/purchases', $meta, $this->data);
    }


    public function purchases_dn()
    {
        $this->sma->checkPermissions('Debit_Notes',true);
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
            $this->data['users']      = $this->input->post('brId') ? $this->site->getUsersBybrId($this->input->post('brId')) : $this->site->getUsersBybrId($this->session->userdata('brId'));

        }else{
            $this->data['users']      = $this->reports_model->getStaff();
            $this->data['warehouses'] = $this->site->getAllWarehouses();

        }


        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('Debit_Notes')]];
        $meta                     = ['page_title' => lang('Debit_Notes'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('purchases/purchases_dn', $meta, $this->data);
    }
    public function expenses_report($id = null)
    {
        $this->sma->checkPermissions('expenses_report',true);
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
            $this->data['users']      = $this->input->post('brId') ? $this->site->getUsersBybrId($this->input->post('brId')) : $this->site->getUsersBybrId($this->session->userdata('brId'));

        }else{
            $this->data['users']      = $this->reports_model->getStaff();
            $this->data['warehouses'] = $this->site->getAllWarehouses();

        }

        $this->data['categories'] = $this->reports_model->getExpenseCategories();
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('expenses')]];
        $meta                     = ['page_title' => lang('expenses_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('reports/expenses', $meta, $this->data);
    }



	public function dashboard()
    {
        if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            admin_redirect('sync');
        }

        // Warehouse filter handling
        $warehouse_id = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }

        // Date range filter handling
        $date_range = $this->input->get('date_range') ?: 'all';
        $start_date = null;
        $end_date   = null;

        if ($date_range == 'today') {
            $start_date = date('Y-m-d 00:00:00');
            $end_date   = date('Y-m-d 23:59:59');
        } elseif ($date_range == 'this_week') {
            $start_date = date('Y-m-d 00:00:00', strtotime('monday this week'));
            $end_date   = date('Y-m-d 23:59:59');
        } elseif ($date_range == 'this_month') {
            $start_date = date('Y-m-01 00:00:00');
            $end_date   = date('Y-m-t 23:59:59');
        } elseif ($date_range == 'this_year') {
            $start_date = date('Y-01-01 00:00:00');
            $end_date   = date('Y-12-31 23:59:59');
        } elseif ($date_range == 'custom') {
            $start_date = $this->input->get('start_date') ? $this->sma->fsd($this->input->get('start_date')) . ' 00:00:00' : null;
            $end_date   = $this->input->get('end_date') ? $this->sma->fsd($this->input->get('end_date')) . ' 23:59:59' : null;
        }

        $this->data['error']          = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['warehouse_id']   = $warehouse_id;
        $this->data['warehouses']     = $this->site->getAllWarehouses();
        $this->data['date_range']     = $date_range;
        $this->data['start_date']     = $start_date;
        $this->data['end_date']       = $end_date;

        // Modern SaaS Purchases Dashboard Analytics Payload
        $this->data['stats']                  = $this->purchases_model->getPurchasesDashboardStats($warehouse_id, $start_date, $end_date);
        $this->data['monthly_trends']         = $this->purchases_model->getPurchasesMonthlyTrends(12, $warehouse_id);
        $this->data['daily_trends']           = $this->purchases_model->getPurchasesDailyTrends($start_date, $end_date, $warehouse_id);
        $this->data['top_products']           = $this->purchases_model->getTopPurchasedProducts(5, $warehouse_id, $start_date, $end_date);
        $this->data['top_suppliers']          = $this->purchases_model->getTopSuppliers(5, $warehouse_id, $start_date, $end_date);
        $this->data['payment_distribution']   = $this->purchases_model->getPaymentMethodDistribution($warehouse_id, $start_date, $end_date);
        $this->data['warehouse_distribution'] = $this->purchases_model->getWarehousePurchaseDistribution($start_date, $end_date);
        $this->data['status_summary']         = $this->purchases_model->getPurchaseStatusSummary($warehouse_id, $start_date, $end_date);
        $this->data['attention_items']        = $this->purchases_model->getPurchasesAttentionRequired($warehouse_id);
        $this->data['recent_purchases']       = $this->purchases_model->getRecentPurchases(8, $warehouse_id);
        $this->data['purchase_reps']          = $this->purchases_model->getPurchaseRepPerformance(5, $warehouse_id, $start_date, $end_date);

        // Backward compatibility support
        $this->data['allPurchases']         = $this->db_model->getAllPurchases();
        $this->data['rPurchases']           = $this->db_model->getPurchasesReturns();
        $this->data['total_purchases_canv'] = $this->db_model->getMonthlyPurchases(date('Y'));
        $this->data['sales']                = $this->db_model->getLatestSales();
        $this->data['quotes']               = $this->db_model->getLastestQuotes();
        $this->data['purchases']            = $this->db_model->getLatestPurchases();
        $this->data['transfers']            = $this->db_model->getLatestTransfers();
        $this->data['customers']            = $this->db_model->getLatestCustomers();
        $this->data['suppliers']            = $this->db_model->getLatestSuppliers();
        $this->data['chatDataTrends']       = $this->db_model->getPChartDataTrends();
        $this->data['chatData']             = $this->db_model->getChartDataPurchases();
        $this->data['purchasesLocation']    = $this->db_model->getPurchasesLocationChart();
        $this->data['bs']                   = $this->db_model->getBestPurchaseschart();
        $this->data['ubs']                  = $this->db_model->getUnBestPurchaseschart();
        $this->data['bc']                   = $this->db_model->getBestCustomerschart();
        $this->data['ubc']                  = $this->db_model->getUnBestCustomerschart();

        $bc   = [['link' => '#', 'page' => lang('purchases_dashboard')]];
        $meta = ['page_title' => lang('purchases_dashboard'), 'bc' => $bc];
        $this->page_construct('purchases/dashboard', $meta, $this->data);
    }

	    public function reports()
    {
       // $this->sma->checkPermissions('reports');
        $data['error']               = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->reports_model->getChartData();
        $this->data['stock']         = $this->reports_model->getStockValue();
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('purchases'), 'page' => lang('purchases')], ['link' => '#', 'page' => lang('reports')]];
        $meta                        = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->page_construct('purchases/reports', $meta, $this->data);
    }

	public function getCurrencyByCode($code = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->site->getCurrencyByCode($code);
		$this->sma->send_json($row);
    }


	public function getPurchasesReportDebitNote($pdf = null, $xls = null)
    {
        //$this->sma->checkPermissions('purchases', true);
        $this->sma->checkPermissions('Debit_Notes',true);

        $grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
        $coId     = $this->input->get('coId') ? $this->input->get('coId') : NULL;
        $brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
        $product      = $this->input->get('product') ? $this->input->get('product') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $supplier     = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {
            $this->db
                ->select('' . $this->db->dbprefix('purchases') . '.date, reference_no, ' . $this->db->dbprefix('warehouses') . '.name as wname, supplier, GROUP_CONCAT(CONCAT(' . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '\n') as iname, (total-total_discount) as total, total_tax,(" . $this->db->dbprefix('purchases') . ".shipping+customs_fees+other_fees) as expenses, grand_total, " . $this->db->dbprefix('purchases') . '.status', false)
                ->from('purchases')
                ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
                ->group_by('purchases.id')
                ->order_by('purchases.date desc');


				$this->db->where('purchases.status', 'returned');


                if ($grpId) {
                    $this->db->where('purchases.grpId', $grpId);
                }
                if ($coId) {
                    $this->db->where('purchases.coId', $coId);
                }
                if ($brId) {
                    $this->db->where('purchases.brId', $brId);
                }

                if ($user) {
                $this->db->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->db->where('purchase_items.product_id', $product);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);

                if ($this->Settings->user_language == 'arabic'){
                    $this->excel->getActiveSheet()->setRightToLeft(true);
                    }
               $this->excel->getActiveSheet()->setTitle(lang('Debit_Notes'));
                    $row = 1;
                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Debit_Notes'));
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $styleArray = array(
                        'font' => array(
                            'bold' => true,
                            'size' => 20,
                            'name'  => 'Cairo-Bold'
                                        )

                                );
                       $fillColor = array(

                                 'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'f9f9f9')
                                    )
                                  );

                          $Font11Bold = array(
                            'font' => array(
                                'bold' => true,
                                'size' => 11,
                                'name'  => 'Cairo-Bold'
                                            )

                          );
            $styleArrayFillGreen = array(

             'fill' => array(
                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '5cb85c')
                ),
                'font' => array(
                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffffff')
                )
             );

          $styleArrayFillRed = array(

             'fill' => array(
               'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'd9534f')
                ),
                'font' => array(
                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffffff')
                )
          );
                $styleArrayFillYellow = array(

                 'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'f0ad4e')
                    ),
                'font' => array(
                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffffff')
                )

              );

               $styleArrayFillBlu = array(

                 'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5bc0de')
                    ),
                'font' => array(
                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffffff')
                )

              );
                      $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                     $row++;

                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($fillColor);
                    $row++;
                    if($grpId){
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_group').' : '.($this->site->getMainGroupsByID($grpId)->name));
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                    $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($fillColor);
                    $row++;
                    }

                    if($coId){
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_company').' : '.($this->site->getMainCompaniesByID($coId)->name));
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                    $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($fillColor);
                     $row++;
                    }

                    if($brId){
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_branch').' : '.($this->site->getMainBranchesByID($brId)->name));
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                    $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($fillColor);
                    $row++;
                    }

                    if($warehouse){
                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('warehouse').' : '.($this->site->getWarehouseByID($warehouse)->name));
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($fillColor);
                    $row++;
                    }
                    if($start_date || $end_date ){
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, ($this->input->get('start_date') ?  lang('period') .' '. lang('Credit_Notes') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($fillColor);
                    $row++;
                   }
                $row++;






                $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('supplier'));
                $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('Total_before_VAT'));
                $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('VAT_Amount'));
                $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('expenses'));
                $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('J'.$row, lang('status'));

                $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($fillColor);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($Font11Bold);





                    $row++;
                $total   = 0;
                $paid    = 0;
                $balance = 0;
                $grand_total = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->supplier);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($data_row->total));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($data_row->total_tax));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($data_row->expenses));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($data_row->grand_total));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, lang($data_row->status));
                    $total   += $data_row->total;
                    $paid    += $data_row->total_tax;
                    $balance += ($data_row->expenses);
                    $grand_total += ($data_row->grand_total);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('F' . $row . ':H' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($total));
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($paid));
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($balance));
				$this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($grand_total));


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('Debit_Notes');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";

            $this->load->library('datatables');
            $this->datatables
                //->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", false)
                ->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, (total-total_discount), total_tax, (shipping+customs_fees+other_fees) as expenses, grand_total , {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", false)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
            // ->group_by('purchases.id');
			$this->datatables->where('purchases.status', 'returned');

            if ($grpId) {
                $this->datatables->where('purchases.grpId', $grpId);
            }
            if ($coId) {
                $this->datatables->where('purchases.coId', $coId);
            }
            if ($brId) {
                $this->datatables->where('purchases.brId', $brId);
            }
            if ($user) {
                $this->datatables->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FPI.product_id', $product, false);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }

	public function payments_report()
    {
        $this->sma->checkPermissions('payments_report',true);
        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            $this->data['billers']    = $this->input->post('brId') ? $this->site->getBillerBybrId($this->input->post('brId')) : $this->site->getBillerBybrId($this->session->userdata('brId'));
            //$this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
            $this->data['users']      = $this->input->post('brId') ? $this->site->getUsersBybrId($this->input->post('brId')) : $this->site->getUsersBybrId($this->session->userdata('brId'));
            //$this->data['categories']          = $this->input->post('brId') ?  $this->site->getCategoryBybrId($this->input->post('brId')) :$this->site->getCategoryBybrId($this->session->userdata('brId'));
        }else{
            $this->data['users']      = $this->reports_model->getStaff();
            //$this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['billers']    = $this->site->getAllCompanies('biller');
            //$this->data['categories'] = $this->site->getAllCategories();

        }

        $this->data['pos_settings'] = POS ? $this->reports_model->getPOSSetting('biller') : false;
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('payments_report')]];
        $meta                       = ['page_title' => lang('payments_report'), 'bc' => $bc];
		//$this->sma->print_arrays($this->data);

		$this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('purchases/payments_report', $meta, $this->data);
    }

	public function getPaymentsReport($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('payments_report',true);

        $grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
        $coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
        $brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
        $user           = $this->input->get('user') ? $this->input->get('user') : null;
        $supplier       = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $customer       = $this->input->get('customer') ? $this->input->get('customer') : null;
        $biller         = $this->input->get('biller') ? $this->input->get('biller') : null;
        $payment_ref    = $this->input->get('payment_ref') ? $this->input->get('payment_ref') : null;
        $paid_by        = $this->input->get('paid_by') ? $this->input->get('paid_by') : null;
        $sale_ref       = $this->input->get('sale_ref') ? $this->input->get('sale_ref') : null;
        $purchase_ref   = $this->input->get('purchase_ref') ? $this->input->get('purchase_ref') : null;
        $card           = $this->input->get('card') ? $this->input->get('card') : null;
        $cheque         = $this->input->get('cheque') ? $this->input->get('cheque') : null;
        $transaction_id = $this->input->get('tid') ? $this->input->get('tid') : null;
        $start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;


        if ($start_date && $end_date) {
			$start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
		   }

		if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }
        if ($pdf || $xls) {
            $this->db
                //->select('' . $this->db->dbprefix('payments') . '.date, ' . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as purchase_ref, paid_by, amount, type')
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, {$this->db->dbprefix('payments')}.note, type, {$this->db->dbprefix('payments')}.id as id")
				->from('payments')
                //->join('purchases', 'payments.sale_id=purchases.id', 'left')
                ->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id')
                ->order_by('payments.date desc');



		 $this->db->where('purchases.reference_no !=', '');
		 $this->db->where('payments.amount !=', 0);


         if ($grpId) {
            $this->db->where('payments.grpId', $grpId);
        }
        if ($coId) {
            $this->db->where('payments.coId', $coId);
        }
        if ($brId) {
            $this->db->where('payments.brId', $brId);
        }
        if ($user) {
                $this->db->where('payments.created_by', $user);
            }
            if ($card) {
                $this->db->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->db->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->db->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->db->where('purchases.biller_id', $biller);
            }

            if ($payment_ref) {
                $this->db->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->db->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->db->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($purchase_ref) {
                $this->db->like('purchases.reference_no', $purchase_ref, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('payments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                 if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }

                $this->excel->getActiveSheet()->setTitle(lang('payments_report'));
                  $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('payments_report'));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $styleArray = array(
                            'font' => array(
                                'bold' => true,
                                'size' => 20,
                                'name'  => 'Cairo-Bold'
                                            )

                                    );
                           $fillColor = array(

                                     'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'f9f9f9')
                                        )
                                      );

                              $Font11Bold = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 11,
                                    'name'  => 'Cairo-Bold'
                                                )

                              );
                                $Font13Bold = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 11,
                                    'name'  => 'Cairo-Bold'
                                                )

                              );
                              $styleArrayFillGreen = array(

                             'fill' => array(
                                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => '5cb85c')
                                ),
                                'font' => array(
                                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'ffffff')
                                )
                             );
                             $styleArrayFillRed = array(

                             'fill' => array(
                               'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'd9534f')
                                ),
                                'font' => array(
                                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'ffffff')
                                )
                          );

                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);

                        $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
                          $row++;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
				$this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') .' '. lang('payments_report') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
				$this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A3')->applyFromArray($Font13Bold);

                        $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
                        $row++;
                        $row++;
                $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, lang('payment_reference'));
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('purchase_ref'));
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, lang('paid_by'));
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang('note'));
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, lang('type'));
                $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($fillColor);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($Font11Bold);


                $row ++;
                $total = 0;
                foreach ($data as $data_row) {
					$note= str_replace("<p>","",($this->sma->decode_html($data_row->note))."");
					$note= str_replace("</p>","",($note)."");

                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->payment_ref);
                    //$this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->purchase_ref);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->decode_html($note));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, lang($data_row->type));

                    /* if($data_row->type=='sent'){
                        $this->excel->getActiveSheet()->getStyle('G'.$row)->applyFromArray($styleArrayFillGreen);
                }else if($data_row->type=='returned'){
                        $this->excel->getActiveSheet()->getStyle('G'.$row)->applyFromArray($styleArrayFillRed);
                }*/

                   // $this->excel->getActiveSheet()->getStyle('F' . $row)->applyFromArray($FillGreen);
                   /* if ($data_row->type == 'returned' || $data_row->type == 'sent') {
                        $total -= $data_row->amount;
                    } else*/ {
                        $total += $data_row->amount;
                    }
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('total'));
				$this->excel->getActiveSheet()->SetCellValue('E' . $row, $total);

                 for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                        }
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('payments_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                //->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, type, {$this->db->dbprefix('payments')}.id as id")
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, {$this->db->dbprefix('payments')}.note, type, {$this->db->dbprefix('payments')}.id as id")
                ->from('payments')
                //->join('sales', 'payments.sale_id=sales.id', 'left')
                ->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id');
				//->order_by('payments.id');


		 $this->datatables->where('purchases.reference_no !=', '');
		 $this->datatables->where('payments.amount !=', 0);


         if ($grpId) {
            $this->datatables->where('payments.grpId', $grpId);
        }
        if ($coId) {
            $this->datatables->where('payments.coId', $coId);
        }
        if ($brId) {
            $this->datatables->where('payments.brId', $brId);
        }
        if ($user) {
                $this->datatables->where('payments.created_by', $user);
            }
            if ($card) {
                $this->datatables->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->datatables->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->datatables->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->datatables->where('purchases.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->datatables->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->datatables->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->datatables->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($purchase_ref) {
                $this->datatables->like('purchases.reference_no', $purchase_ref, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('payments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }

	public function modal_view_ByRef($ref = null)
    {
        $this->sma->checkPermissions('index', true);

        if ($this->input->get('ref')) {
            $ref = $this->input->get('ref');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->purchases_model->getPurchaseByReference($ref);

		//$this->sma->print_arrays($ref);
		$this->modal_view($inv->id);
    }


	public function expense_payments_report()
    {
        $this->sma->checkPermissions('expense_payments_report',true);
        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            $this->data['billers']    = $this->input->post('brId') ? $this->site->getBillerBybrId($this->input->post('brId')) : $this->site->getBillerBybrId($this->session->userdata('brId'));
            //$this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
            $this->data['users']      = $this->input->post('brId') ? $this->site->getUsersBybrId($this->input->post('brId')) : $this->site->getUsersBybrId($this->session->userdata('brId'));
            //$this->data['categories']          = $this->input->post('brId') ?  $this->site->getCategoryBybrId($this->input->post('brId')) :$this->site->getCategoryBybrId($this->session->userdata('brId'));
        }else{
            $this->data['users']      = $this->reports_model->getStaff();
            //$this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['billers']    = $this->site->getAllCompanies('biller');
            //$this->data['categories'] = $this->site->getAllCategories();

        }

        $this->data['pos_settings'] = POS ? $this->reports_model->getPOSSetting('biller') : false;
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('expense_payments_report')]];
        $meta                       = ['page_title' => lang('expense_payments_report'), 'bc' => $bc];
		//$this->sma->print_arrays($this->data);

		$this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('purchases/expense_payments_report', $meta, $this->data);
    }

	public function getExpensePaymentsReport($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('expense_payments_report', true);

        $grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
        $coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
        $brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
        $user           = $this->input->get('user') ? $this->input->get('user') : null;
        $supplier       = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $customer       = $this->input->get('customer') ? $this->input->get('customer') : null;
        $biller         = $this->input->get('biller') ? $this->input->get('biller') : null;
        $payment_ref    = $this->input->get('payment_ref') ? $this->input->get('payment_ref') : null;
        $paid_by        = $this->input->get('paid_by') ? $this->input->get('paid_by') : null;
        $sale_ref       = $this->input->get('sale_ref') ? $this->input->get('sale_ref') : null;
        $purchase_ref   = $this->input->get('purchase_ref') ? $this->input->get('purchase_ref') : null;
        $card           = $this->input->get('card') ? $this->input->get('card') : null;
        $cheque         = $this->input->get('cheque') ? $this->input->get('cheque') : null;
        $transaction_id = $this->input->get('tid') ? $this->input->get('tid') : null;
        $start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;


        if ($start_date && $end_date) {
			$start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
		   }

		if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }
        if ($pdf || $xls) {
            $this->db
                //->select('' . $this->db->dbprefix('expense_payments') . '.date, ' . $this->db->dbprefix('expense_payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as purchase_ref, paid_by, amount, type')
                ->select("DATE_FORMAT({$this->db->dbprefix('expense_payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('expense_payments') . '.reference_no as reference_no, ' . $this->db->dbprefix('expense_payments') . ".expense_id as expense_id, paid_by, amount, note, type, {$this->db->dbprefix('expense_payments')}.id as id1")
				->from('expense_payments')
                //->join('purchases', 'payments.sale_id=purchases.id', 'left')
                //->join('expenses', 'expense_payments.expenses_id=expenses.id', 'left')
                //->group_by('expense_payments.id');
                ->order_by('expense_payments.id desc');




                //$this->db->where('expenses.reference !=', '');
                $this->db->where('expense_payments.amount !=', 0);


                if ($grpId) {
                    $this->db->where('expense_payments.grpId', $grpId);
                }
                if ($coId) {
                    $this->db->where('expense_payments.coId', $coId);
                }
                if ($brId) {
                    $this->db->where('expense_payments.brId', $brId);
                }
                if ($user) {
                $this->db->where('expense_payments.created_by', $user);
            }
            if ($card) {
                $this->db->like('expense_payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->db->where('expense_payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->db->where('expense_payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->db->where('purchases.biller_id', $biller);
            }

            if ($payment_ref) {
                $this->db->like('expense_payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->db->where('expense_payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->db->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($expense_ref) {
                $this->datatables->like('expense_payments.reference_no', $expense_ref, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('expense_payments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                 if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }

                $this->excel->getActiveSheet()->setTitle(lang('expense_payments_report'));
                  $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('expense_payments_report'));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $styleArray = array(
                            'font' => array(
                                'bold' => true,
                                'size' => 20,
                                'name'  => 'Cairo-Bold'
                                            )

                                    );
                           $fillColor = array(

                                     'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'f9f9f9')
                                        )
                                      );

                              $Font11Bold = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 11,
                                    'name'  => 'Cairo-Bold'
                                                )

                              );
                                $Font13Bold = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 11,
                                    'name'  => 'Cairo-Bold'
                                                )

                              );
                              $styleArrayFillGreen = array(

                             'fill' => array(
                                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => '5cb85c')
                                ),
                                'font' => array(
                                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'ffffff')
                                )
                             );
                             $styleArrayFillRed = array(

                             'fill' => array(
                               'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'd9534f')
                                ),
                                'font' => array(
                                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'ffffff')
                                )
                          );

                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);

                        $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
                          $row++;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
				$this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') .' '. lang('expense_payments_report') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
				$this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A3')->applyFromArray($Font13Bold);

                        $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
                        $row++;
                        $row ++;
                $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, lang('payment_reference'));
               // $this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('purchase_ref'));
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('paid_by'));
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, lang('note'));
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang('type'));
                $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($fillColor);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($Font11Bold);


                $row ++;
                $total = 0;
                foreach ($data as $data_row) {
                    $note= str_replace("<p>","",($this->sma->decode_html($data_row->note))."");
					$note= str_replace("</p>","",($note)."");

					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    //$this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                 //   $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->purchase_ref);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $note);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang($data_row->type));

                   // $this->excel->getActiveSheet()->getStyle('F' . $row)->applyFromArray($FillGreen);
                   /* if ($data_row->type == 'returned' || $data_row->type == 'sent') {
                        $total -= $data_row->amount;
                    } else*/ {
                        $total += $data_row->amount;
                    }
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('total'));
				$this->excel->getActiveSheet()->SetCellValue('D' . $row, $total);

                 for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                        }
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('expense_payments_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                //->select('' . $this->db->dbprefix('expense_payments') . '.date, ' . $this->db->dbprefix('expense_payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as purchase_ref, paid_by, amount, type')
                ->select("DATE_FORMAT({$this->db->dbprefix('expense_payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('expense_payments') . ".reference_no as reference_no, paid_by, amount, note, type, {$this->db->dbprefix('expense_payments')}.id as id1")
				->from('expense_payments');
                //->join('purchases', 'payments.sale_id=purchases.id', 'left')
                //->join('expenses', 'expense_payments.expenses_id=expenses.id', 'left')
                //->group_by('expense_payments.id');
                //->order_by('expense_payments.id desc');


		 //$this->datatables->where('expenses.reference !=', '');
		 $this->datatables->where('expense_payments.amount !=', 0);


         if ($grpId) {
            $this->datatables->where('expense_payments.grpId', $grpId);
        }
        if ($coId) {
            $this->datatables->where('expense_payments.coId', $coId);
        }
        if ($brId) {
            $this->datatables->where('expense_payments.brId', $brId);
        }
        if ($user) {
                $this->datatables->where('expense_payments.created_by', $user);
            }
            if ($card) {
                $this->datatables->like('expense_payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->datatables->where('expense_payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->datatables->where('expense_payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->datatables->where('purchases.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->datatables->like('expense_payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->datatables->where('expense_payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->datatables->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($expense_ref) {
                $this->datatables->like('expenses.reference', $expense_ref, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('expense_payments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }

    public function supplier_report($user_id = null)
    {
        $this->sma->checkPermissions('suppliers', true,'reports');
        if (!$user_id) {
            $this->session->set_flashdata('error', lang('no_supplier_selected'));
            admin_redirect('reports/suppliers');
        }

        $this->data['purchases']       = $this->reports_model->getPurchasesTotals($user_id);
        $this->data['total_purchases'] = $this->reports_model->getSupplierPurchases($user_id);
		$this->data['total_purchases_canv']   = $this->reports_model->getMonthlyPurchases(date('Y'), '' ,$user_id);

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
            $this->data['users']      = $this->input->post('brId') ? $this->site->getUsersBybrId($this->input->post('brId')) : $this->site->getUsersBybrId($this->session->userdata('brId'));
            $this->data['pay_users']      = $this->input->post('pay_brId') ? $this->site->getUsersBybrId($this->input->post('pay_brId')) : $this->site->getUsersBybrId($this->session->userdata('brId'));//$this->reports_model->getStaff();
            //$this->data['categories']          = $this->input->post('brId') ?  $this->site->getCategoryBybrId($this->input->post('brId')) :$this->site->getCategoryBybrId($this->session->userdata('brId'));
        }else{
            $this->data['users']      = $this->reports_model->getStaff();
            $this->data['pay_users']      = $this->reports_model->getStaff();
            $this->data['warehouses'] = $this->site->getAllWarehouses();

        }


        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('suppliers_report')]];
        $meta                  = ['page_title' => lang('suppliers_report'), 'bc' => $bc];
        $this->page_construct('reports/supplier_report', $meta, $this->data);
    }


    public function agedopeninvoicessupplier($pdf = null, $xls = null, $start_date = null, $end_date = null, $supplier = null)
	{

		$this->sma->checkPermissions('suppliers', true,'reports');

		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');

		//$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : $this->sma->hrsd(date('Y-m-d'));
		$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$supplier     = $this->input->post('supplier') ? $this->input->post('supplier') : '';
		$not_due     = isset($_POST['not_due'])  ? $_POST['not_due'] : '1';
		$the_oldest     = isset($_POST['the_oldest'])  ? $_POST['the_oldest'] : '1';
		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0') {
			$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_POST['compare_periods']; $i++) {

				//if($i!=$_POST['compare_periods'])
				$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			$start_date = $end_date;
		}




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';


		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0)
			$this->data['purchaseList'] = $this->site->getAllPurchasesDueAggregate($start_date, $end_date, $supplier, $not_due, $the_oldest,$grpId,$coId,$brId);
		else
			$this->data['purchaseList'] = $this->site->getAllPurchasesDueAggregate(NULL, $end_date, $supplier, $not_due, $the_oldest,$grpId,$coId,$brId);


			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 $this->data['suppliers'] = $this->input->post('brId') ? $this->site->getSuppliersBybrId($this->input->post('brId')) : $this->site->getSuppliersBybrId($this->session->userdata('brId'));




            }else{
				$this->data['suppliers']      = $this->site->getAllCompanies('supplier');
			}





		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Supplier_debt_aging_report'), 'page' => lang('Supplier_debt_aging_report')], ['link' => '#', 'page' => lang('Aged_Open_Invoices')]];
		$meta                = ['page_title' => lang('Supplier_debt_aging_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'purchases/reports', null,true);
		$this->page_construct('purchases/agedopeninvoicessupplier_index', $meta, $this->data);
	}


    public function agedopeninvoicesForSupplier($pdf = null, $xls = null, $start_date = null, $end_date = null, $customer = null)
	{
		$this->sma->checkPermissions('suppliers', true,'reports');

		//$this->sma->print_arrays($_GET);

		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
		$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
		$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');

		$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
		$end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$supplier     = $this->input->get('supplier') ? $this->input->get('supplier') : '';
		$not_due     = isset($_GET['due'])  ? $_GET['due'] : '1';
		if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
			$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

				if ($i != $_GET['compare_periods'])
					$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			//$start_date=$end_date;
		}




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';


		if ($_GET['compare_periods'] && $_GET['compared_to'] != '0' && $the_oldest == 0)
			$this->data['saleList'] = $this->site->getAllPurchasesDue(NULL, $start_date, $end_date, 1, $supplier, $not_due, $the_oldest,$grpId,$coId,$brId);
		else
			$this->data['saleList'] = $this->site->getAllPurchasesDue(NULL, $start_date, $end_date, 1, $supplier, $not_due, $the_oldest,$grpId,$coId,$brId);


		//$this->sma->print_arrays($not_due);
		//$this->sma->print_arrays($this->data['saleList']);

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->get('grpId') ? $this->site->getCompaniesBygrpId($this->input->get('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->get('coId') ? $this->site->getBranchesBycoId($this->input->get('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 $this->data['suppliers'] = $this->input->get('brId') ? $this->site->getSuppliersBybrId($this->input->get('brId')) : $this->site->getSuppliersBybrId($this->session->userdata('brId'));




            }else{
				$this->data['suppliers']      = $this->site->getAllCompanies('supplier');
			}


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Supplier_debt_aging_report'), 'page' => lang('Supplier_debt_aging_report')], ['link' => '#', 'page' => lang('Supplier_debt_aging_report')]];
		$meta                = ['page_title' => lang('Supplier_debt_aging_report'), 'bc' => $bc];
		$this->page_construct('purchases/agedopeninvoicessupplier', $meta, $this->data);
	}






}
