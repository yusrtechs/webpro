<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Carssales extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->lang->admin_load('sales', $this->Settings->user_language);
        $this->lang->admin_load('quotations', $this->Settings->user_language);
        $this->lang->admin_load('returns', $this->Settings->user_language);
        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('carssales_model');
        $this->load->admin_model('pos_model');
        $this->load->admin_model('pos2_model');
        $this->load->admin_model('purchases_model');
        $this->load->admin_model('carspurchases_model');
        $this->load->admin_model('carsquotes_model');
        $this->load->admin_model('carsoffers_model');
        $this->load->admin_model('returns_model');
        $this->load->admin_model('carsreports_model');
        $this->load->admin_model('db_model');
		$this->load->admin_model('settings_model');
		$this->load->admin_model('accounts_model');
		$this->load->admin_model('products_model');
		$this->load->admin_model('cars_model');


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

      //  $this->sma->print_arrays($this);

    }

    /* ------------------------------------------------------------------ */

    public function add($quote_id = null,$mode = null)
    {


        $this->sma->checkPermissions('add',true,'sales');




        $sale_id = $this->input->get('sale_id') ? $this->input->get('sale_id') : null;
        $offer_id = $mode=="offer" ? $quote_id : null;
        $quote_id = $mode=="offer" ? null : null;
           //$this->sma->print_arrays($offer_id."p",$quote_id);

        $this->form_validation->set_message('is_natural_no_zero', lang('no_zero_required'));
        $this->form_validation->set_rules('customer', lang('customer'), 'required');
        $this->form_validation->set_rules('biller', lang('biller'), 'required');
        $this->form_validation->set_rules('sale_status', lang('sale_status'), 'required');
        $this->form_validation->set_rules('payment_status', lang('payment_status'), 'required');

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
            //if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            /*} else {
                $date = date('Y-m-d H:i:s');
            }*/
			if($this->input->post('exit_date')){

			 $exit_date = $this->sma->fld(trim($this->input->post('exit_date')));
            } else {
                $exit_date = date('Y-m-d H:i:s');
            }

            if($this->input->post('final_bayan_date')){

                $final_bayan_date = $this->sma->fld(trim($this->input->post('final_bayan_date')));
               } else {
                   $final_bayan_date = date('Y-m-d H:i:s');
               }

			$plate_no     = $this->input->post('plate_no');
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $total_items      = $this->input->post('total_items');
            $sale_status      = $this->input->post('sale_status');
            $payment_status   = $this->input->post('payment_status');
            $payment_term     = $this->input->post('payment_term');
            $due_date         = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days', strtotime($date))) : null;
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = !empty($customer_details->company) && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = !empty($biller_details->company) && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note             = $this->sma->clear_tags($_POST['note']);
            $staff_note       = $this->sma->clear_tags($_POST['staff_note']);
            //$quote_id         = $this->input->post('quote_id') ? $this->input->post('quote_id') : null;
			$manual_payment         = $this->input->post('manual_payment') ? $this->input->post('manual_payment') : null;
			$po_no         = $this->input->post('po_no') ? $this->input->post('po_no') : null;
			$currencyCode         = $this->input->post('currency') ? $this->input->post('currency') : $this->Settings->default_currency;

			$currencyRate=$this->site->getCurrencyByCode($currencyCode)->rate;

            $declaration_no         = $this->input->post('declaration_no') ? $this->input->post('declaration_no') : null;
			$bayan_no         = $this->input->post('bayan_no') ? $this->input->post('bayan_no') : null;
			$bill_lading_no         = $this->input->post('bill_lading_no') ? $this->input->post('bill_lading_no') : null;

			$supply_date      = null;
			if($sale_status=="completed")
			$supply_date  = date('Y-m-d H:i:s');


            $gtotal            = 0;
            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $digital          = false;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;

			if($this->input->post('order_discount')>0){
				for ($r = 0; $r < $i; $r++) {
					 $item_code          = $_POST['product_code'][$r];
					 $item_type          = $_POST['product_type'][$r];
                $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;

				if($product_details->tax_method != 1)
					$gtotal=$gtotal+($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r]);
				else
					$gtotal=$gtotal+($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r]);

				}
			}


            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' && $_POST['product_option'][$r] != 'null' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);
                $net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);
                $item_unit_quantity = $_POST['quantity'][$r];
				if($freePromo=$this->carssales_model->getBuyGetFreePromoByPId($item_id,$warehouse_id)){
				$item_unit_quantity = $_POST['product_base_quantity'][$r];


				}

                $item_serial        = $_POST['serial'][$r]           ? $_POST['serial'][$r] : '';
                $item_tax_rate      = $_POST['product_tax'][$r]      ?$_POST['product_tax'][$r] : null;
                $item_discount      = $_POST['product_discount'][$r] ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = $_POST['product_base_quantity'][$r];
                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;
                    // $unit_price = $real_unit_price;
                    if ($item_type == 'digital') {
                        $digital = true;
                    }
                    if($gtotal>0){
						$order_product_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($gtotal));
                      if($product_details->tax_method != 1)
						$item_discount=$this->sma->formatDecimal((($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					else
						$item_discount=$this->sma->formatDecimal((($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					}
					//
					//$this->sma->print_arrays($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r]);
					//$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                    if($product_details->tax_method != 1)
					$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
					else
						$pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);

                    //$this->sma->print_arrays($pr_discount);



					$unit_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);

					$item_net_price   = $net_price;


					$pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
						if($product_details->tax_method != 1)
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                        else
						$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $net_price;
                        }else{
							//$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
							$item_net_price=$net_price;
							//$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        //$item_tax    = $ctax['amount'];
                        //$tax         = $ctax['tax'];



						}
						//
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            //$total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                    $unit     = $this->site->getUnitByID($item_unit);

                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($unit_price,12),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $unit ? $unit->id : null,
                        'product_unit_code' => $unit ? $unit->code : null,
                        'unit_quantity'     => $item_unit_quantity,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $subtotal,
                        'serial_no'         => $item_serial,
                        'real_unit_price'   => $real_unit_price,
						'promo_free'   => $unit_price!=0 ? 0 : 1,
                    ];

                    $products[] = ($product + $gst_data);


					if($freePromo=$this->carssales_model->getBuyGetFreePromoByPId($item_id,$warehouse_id)){

						if(isset($freePromo->start_date)){
							$free_date = date('Y-m-d');
							$free_date=date('Y-m-d', strtotime($free_date));

							$contractDateBegin = date('Y-m-d', strtotime($freePromo->start_date));
							$contractDateEnd = date('Y-m-d', strtotime($freePromo->end_date));

							if (($free_date >= $contractDateBegin) && ($free_date <= $contractDateEnd)){

							}else{
							unset($freePromo);
							}

						}

						$freeqty=floor($item_quantity/$freePromo->buy_quantity)*($freePromo->get_quantity);

						if($freeqty>0){

						}else{
						unset($freePromo);
						}

						if($freePromo){
							$fp=$this->site->getProductByID($freePromo->free_product);
							$unit_free     = $this->site->getUnitByID($fp->unit);

						$product_free = [
                        'product_id'        => $freePromo->free_product,
                        'product_code'      => $fp->code,
                        'product_name'      => $fp->name,
                        'product_type'      => $fp->type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => 0,
                        'unit_price'        => 0,
                        'quantity'          => $freeqty,
                        'product_unit_id'   => $unit_free ? $unit_free->id : null,
                        'product_unit_code' => $unit_free ? $unit_free->code : null,
                        'unit_quantity'     => $freeqty,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => 0,
                        'tax_rate_id'       => 0,
                        'tax'               => '',
                        'discount'          => '',
                        'item_discount'     => 0,
                        'subtotal'          => 0,
                        'serial_no'         => '',
                        'real_unit_price'   => 0,
                        'promo_free'   => 1,
                    ];



						$products[] = ($product_free + $gst_data);
						//$this->sma->print_arrays($freePromo);
						//$this->sma->print_arrays($product_free);
						}
					}


					//$this->sma->print_arrays($products);


                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 12);
                    $orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }


            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
			if($gtotal>0)
			$order_discount=0;
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            if($gtotal>0)
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping)), 4);
            else
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);


			if($this->input->post('tax_method_type')==1)
				$ordet_type= 2;
			else
				$ordet_type= 1;

			if($sale_status!='completed'){
				unset($_POST['costCenterId']);
				unset($_POST['costCenterAmount']);
			}


			$data           = ['date' => $date,
                'reference_no'        => $reference,
                'customer_id'         => $customer_id,
                'customer'            => $customer,
                'biller_id'           => $biller_id,
                'biller'              => $biller,
                'warehouse_id'        => $warehouse_id,
                'note'                => $note,
                'staff_note'          => $staff_note,
                'total'               => $orginaltotal,
                'product_discount'    => $product_discount,
                'order_discount_id'   => '0',//$this->input->post('order_discount'),
                'order_discount'      => $order_discount,
                'total_discount'      => $total_discount,
                'product_tax'         => $product_tax,
                'order_tax_id'        => $this->input->post('order_tax'),
                'order_tax'           => $order_tax,
                'total_tax'           => $total_tax,
                'shipping'            => $this->sma->formatDecimal($shipping),
                'grand_total'         => $grand_total,
                'total_items'         => $total_items,
                'sale_status'         => $sale_status,
                'payment_status'      => $payment_status,
                'payment_term'        => $payment_term,
                'due_date'            => $due_date,
                'paid'                => 0,
                'created_by'          => $this->session->userdata('user_id'),
                'hash'                => hash('sha256', microtime() . mt_rand()),
				'supply_date'                => $supply_date,
				'plate_no'                => $plate_no,
				'exit_date'                => $exit_date,
				'currency_code'                => $currencyCode,
				'currency_rate'                => $this->sma->formatDecimal($currencyRate),
				'manual_payment'                => $manual_payment,
				'po_no'                => $po_no,
				'order_type'                => $ordet_type,
				'uuid'                => $this->site->getUniqeUUID()->UUID,
				'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),

                'declaration_no'                => $declaration_no,
                'bayan_no'                => $bayan_no,
                'bill_lading_no'                => $bill_lading_no,
                'final_bayan_date'                => $final_bayan_date,




            ];
            if ($this->Settings->indian_gst) {
                //$data['cgst'] = $total_cgst;
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
								'currency'         => $sale->currency_code ? $sale->currency_code : $this->Settings->default_currency,
								'currency_rate'         => $sale->currency_rate ? $sale->currency_rate : '1',


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
								'currency'         => $sale->currency_code ? $sale->currency_code : $this->Settings->default_currency,
								'currency_rate'         => $sale->currency_rate ? $sale->currency_rate : '1',

                            ];
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



				 //$this->sma->print_arrays($payment[$r]['currency_rate']);




				//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

				$amount=$payment[$r]['amount'] * $currencyRate;

				//if ($currencyRate != $exchange_rate){
					$amountPL=$payment[$r]['amount'] * $exchange_rate;
					$amountDiff=$amountPL-$amount;
				//}
				//}

				//$this->sma->print_arrays($amountDiff);

				if($amountDiff<0){
					$return_sale_ex_ref=$this->site->getReference('ex');


				//$this->sma->print_arrays($total_shipping);
			$data_ex = [
                'date'         => $date,
                'reference'    => $return_sale_ex_ref,
                'amount'       => abs($amountDiff),
                'grand_total'       => abs($amountDiff),
                'total'       => abs($amountDiff),
				'payment_status'       => 'paid',
				'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $sale->warehouse_id,
			    'other' => 1,
			];
			if($this->purchases_model->addExpense($data_ex)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}
				}
				if($amountDiff>0){
			$return_sale_re_ref=$this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay');
            $data_re = [
                'date'         => $date,
                'reference'    => $return_sale_re_ref,
                'amount'       => abs($amountDiff),
                'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $sale->warehouse_id,
			];
			if($this->products_model->addRevenue($data_re)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}

				}


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

			$si_return = [];


			//$this->sma->print_arrays($data, $products, $payment,$si_return,$payment_type_arr,$quote_id);


             //$this->sma->print_arrays($this->carssales_model->getavg_costProduct($products[0]['product_id'],$products[0]['warehouse_id']));
        }

        if ($this->form_validation->run() == true && $this->carssales_model->addSale($data, $products, $payment,$si_return,$payment_type_arr,$quote_id)) {
            $this->session->set_userdata('remove_slls', 1);

            if ($quote_id) {
                $this->db->update('quotes', ['status' => 'completed'], ['id' => $quote_id]);
            }
            $this->session->set_flashdata('message', lang('sale_added'));
            admin_redirect('carssales');
        } else {
            if ($quote_id ||  $sale_id ) {
                if ($quote_id) {
                    $this->data['quote'] = $this->carssales_model->getQuoteByID($quote_id);
                    $items               = $this->carssales_model->getAllQuoteItems($quote_id);

                } elseif ($sale_id) {
                    $this->data['quote'] = $this->carssales_model->getInvoiceByID($sale_id);
                    $items               = $this->carssales_model->getAllInvoiceItems($sale_id);
                }
              /*  if ($sale_id) {
                    $this->data['carsquotes'] = $this->carssales_model->getInvoiceByID($sale_id);
                    $items               = $this->carssales_model->getAllInvoiceItems($sale_id);
                }
                elseif ($quote_id) {

                    $this->data['carsquotes'] = $this->carssales_model->getOfferByID($quote_id);
                    $items               = $this->carssales_model->getAllOfferItems($quote_id);
                } */
                krsort($items);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row             = json_decode('{}');
                        $row->tax_method = 0;
                    } else {
                        unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    }
                    $row->quantity = 0;
                    $pis           = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                    if ($pis) {
                        foreach ($pis as $pi) {
                            $row->quantity += $pi->quantity_balance;
                        }
                    }
                    $row->id              = $item->product_id;
                    $row->code            = $item->product_code;
                    $row->name            = $item->product_name;
                    $row->type            = $item->product_type;
                    $row->qty             = $item->quantity;
                    $row->base_quantity   = $item->quantity;
                    $row->base_unit       = isset($row->unit) ? $row->unit : $item->product_unit_id;
                    $row->base_unit_price = isset($row->price) ? $row->price : $item->unit_price;
                    $row->unit            = $item->product_unit_id;
                    $row->qty             = $item->unit_quantity;
                    $row->discount        = $item->discount ? $item->discount : '0';
                    $row->item_tax        = $item->item_tax      > 0 ? $item->item_tax      / $item->quantity : 0;
                    $row->item_discount   = $item->item_discount > 0 ? $item->item_discount / $item->quantity : 0;
                    $row->price           = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($row->item_discount));
                    $row->unit_price      = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($row->item_discount) + $this->sma->formatDecimal($row->item_tax) : $item->unit_price + ($row->item_discount);
                    $row->real_unit_price = $item->real_unit_price;
                    $row->tax_rate        = $item->tax_rate_id;
                    $row->serial          = '';
                    $row->option          = $item->option_id;
                    $options              = $this->carssales_model->getProductOptions($row->id, $item->warehouse_id);
                    if ($options) {
                        $option_quantity = 0;
                        foreach ($options as $option) {
                            $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                            if ($pis) {
                                foreach ($pis as $pi) {
                                    $option_quantity += $pi->quantity_balance;
                                }
                            }
                            if ($option->quantity > $option_quantity) {
                                $option->quantity = $option_quantity;
                            }
                        }
                    }
                    $combo_items = false;
                    if ($row->type == 'combo') {
                        $combo_items = $this->carssales_model->getProductComboItems($row->id, $item->warehouse_id);
                    }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $ri       = $this->Settings->item_addition ? $row->id : $c;

                    $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                        'row'        => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                    $c++;
                }
                $this->data['quote_items'] = json_encode($pr);
            }
        //if($this->Settings->carsOffer==1)
        {

            if ($offer_id ||  $sale_id ) {
                if ($offer_id) {
                    $this->data['quote'] = $this->carssales_model->getOfferByID($offer_id);
                    $items               = $this->carssales_model->getAllOfferItems($offer_id);

                } elseif ($sale_id) {
                    $this->data['quote'] = $this->carssales_model->getInvoiceByID($sale_id);
                    $items               = $this->carssales_model->getAllInvoiceItems($sale_id);
                }

                krsort($items);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row             = json_decode('{}');
                        $row->tax_method = 0;
                    } else {
                        unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    }
                    $row->quantity = 0;
                    $pis           = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                    if ($pis) {
                        foreach ($pis as $pi) {
                            $row->quantity += $pi->quantity_balance;
                        }
                    }
                    $row->id              = $item->product_id;
                    $row->code            = $item->product_code;
                    $row->name            = $item->product_name;
                    $row->type            = $item->product_type;
                    $row->qty             = $item->quantity;
                    $row->base_quantity   = $item->quantity;
                    $row->base_unit       = isset($row->unit) ? $row->unit : $item->product_unit_id;
                    $row->base_unit_price = isset($row->price) ? $row->price : $item->unit_price;
                    $row->unit            = $item->product_unit_id;
                    $row->qty             = $item->unit_quantity;
                    $row->discount        = $item->discount ? $item->discount : '0';
                    $row->item_tax        = $item->item_tax      > 0 ? $item->item_tax      / $item->quantity : 0;
                    $row->item_discount   = $item->item_discount > 0 ? $item->item_discount / $item->quantity : 0;
                    $row->price           = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($row->item_discount));
                    $row->unit_price      = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($row->item_discount) + $this->sma->formatDecimal($row->item_tax) : $item->unit_price + ($row->item_discount);
                    $row->real_unit_price = $item->real_unit_price;
                    $row->tax_rate        = $item->tax_rate_id;
                    $row->serial          = '';
                    $row->option          = $item->option_id;
                    $options              = $this->carssales_model->getProductOptions($row->id, $item->warehouse_id);
                    if ($options) {
                        $option_quantity = 0;
                        foreach ($options as $option) {
                            $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                            if ($pis) {
                                foreach ($pis as $pi) {
                                    $option_quantity += $pi->quantity_balance;
                                }
                            }
                            if ($option->quantity > $option_quantity) {
                                $option->quantity = $option_quantity;
                            }
                        }
                    }
                    $combo_items = false;
                    if ($row->type == 'combo') {
                        $combo_items = $this->carssales_model->getProductComboItems($row->id, $item->warehouse_id);
                    }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $ri       = $this->Settings->item_addition ? $row->id : $c;

                    $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                        'row'        => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                    $c++;
                }
                $this->data['quote_items'] = json_encode($pr);
            }
            }

			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
            $this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();

            	//

			}

            //$this->sma->print_arrays($this->carssales_model->getTotalQuotesPayments($quote_id)->amount);
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
          //  $this->data['offer_id']   = $offer_id ? $offer_id : $sale_id;
          if($offer_id){
            $this->data['quote_id']   = $offer_id ? $offer_id :  $sale_id;

          }else{
            $this->data['quote_id']   = $quote_id ? $quote_id :  $sale_id;

          }


            $this->data['quote_payment']   = $quote_id ? $this->carssales_model->getTotalQuotesPayments($quote_id)->amount : 0;
            //$this->sma->print_arrays($this->data['quote_payment']);
            $this->data['billers']    = $this->site->getAllCompanies('biller');
            $this->data['warehouses'] = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
			$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['categories'] = $this->site->getAllCategories();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');

            $this->data['units']      = $this->site->getAllBaseUnits();
            $this->data['currencies'] = $this->site->getAllCurrencies();
			//$this->sma->print_arrays($this->data['currencies']);
            $this->data['slnumber']    = ''; //$this->site->getReference('so');
            $this->data['payment_ref'] = ''; //$this->site->getReference('pay');
            $bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('add_sale')]];
            $meta                      = ['page_title' => lang('add_sale'), 'bc' => $bc];
            $this->page_construct('carssales/add', $meta, $this->data);
        }
    }

    public function add_delivery($id = null)
    {
        $this->sma->checkPermissions('add_delivery',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $sale = $this->carssales_model->getInvoiceByID($id);
        /*if ($sale->sale_status != 'completed') {
            $this->session->set_flashdata('error', lang('status_is_x_completed'));
            $this->sma->md();
        }*/

        if ($delivery = $this->carssales_model->getDeliveryBySaleID($id)) {
            $this->edit_delivery($delivery->id);
        } else {
            $this->form_validation->set_rules('sale_reference_no', lang('sale_reference_no'), 'required');
            $this->form_validation->set_rules('customer', lang('customer'), 'required');
            $this->form_validation->set_rules('address', lang('address'), 'required');

            if ($this->form_validation->run() == true) {
                if ($this->Owner || $this->Admin) {
                    $date = $this->sma->fld(trim($this->input->post('date')));
                } else {
                    $date = date('Y-m-d H:i:s');
                }
                $dlDetails = [
                    'date'              => $date,
                    'sale_id'           => $this->input->post('sale_id'),
                    'do_reference_no'   => $this->input->post('do_reference_no') ? $this->input->post('do_reference_no') : $this->site->getReference('do'),
                    'sale_reference_no' => $this->input->post('sale_reference_no'),
                    'customer'          => $this->input->post('customer'),
                    'address'           => $this->input->post('address'),
                    'status'            => $this->input->post('status'),
                    'delivered_by'      => $this->input->post('delivered_by'),
                    'received_by'       => $this->input->post('received_by'),
                    'note'              => $this->sma->clear_tags($this->input->post('note')),
                    'created_by'        => $this->session->userdata('user_id'),
                ];
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
                    $photo                   = $this->upload->file_name;
                    $dlDetails['attachment'] = $photo;
                }
            } elseif ($this->input->post('add_delivery')) {
                if ($sale->shop) {
                    $this->load->library('sms');
                    $this->sms->delivering($sale->id, $dlDetails['do_reference_no']);
                }
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            }

            if ($this->form_validation->run() == true && $this->carssales_model->addDelivery($dlDetails)) {
				if($dlDetails['status']=='delivered')
			$this->carssales_model->updateStatus($dlDetails['sale_id'],'completed','delivered');

                $this->session->set_flashdata('message', lang('delivery_added'));
                admin_redirect('carssales/deliveries');
            } else {
                $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['customer']        = $this->site->getCompanyByID($sale->customer_id);
                $this->data['address']         = $this->site->getAddressByID($sale->address_id);
                $this->data['inv']             = $sale;
                $this->data['do_reference_no'] = ''; //$this->site->getReference('do');
                $this->data['modal_js']        = $this->site->modal_js();

                $this->load->view($this->theme . 'carssales/add_delivery', $this->data);
            }
        }
    }

    public function add_gift_card()
    {
        $this->sma->checkPermissions('add_gift_card',true,'sales');

        $this->form_validation->set_rules('card_no', lang('card_no'), 'trim|is_unique[gift_cards.card_no]|required');
        $this->form_validation->set_rules('value', lang('value'), 'required');

        if ($this->form_validation->run() == true) {
			//$this->sma->print_arrays($_POST);

            $paid = $this->input->post('paid_amount')? $this->input->post('paid') : 0;
            $paid_amount = $paid==1 && $this->input->post('paid_amount')? $this->input->post('paid_amount') : 0;
            $tax_rate = $paid==1 && $this->input->post('tax_rate')? $this->input->post('tax_rate') : 0;
            $tax_amount = $paid==1 && $this->input->post('tax_amount')? $this->input->post('tax_amount') : 0;
            $total_paid_amount = $paid==1 && $this->input->post('total_paid_amount')? $this->input->post('total_paid_amount') : 0;
            $customer_details = $this->input->post('customer') ? $this->site->getCompanyByID($this->input->post('customer')) : null;
            $customer         = $customer_details ? $customer_details->company : null;
            $data             = ['card_no' => $this->input->post('card_no'),
                'value'                    => $this->input->post('value'),
                'customer_id'              => $this->input->post('customer') ? $this->input->post('customer') : null,
                'customer'                 => $customer,
                'paid_amount'                  => $paid_amount,
                'tax_rate'                  => $tax_rate,
                'tax_amount'                  => $tax_amount,
                'balance' => $this->input->post('value'),
                'total_paid_amount'                  => $total_paid_amount,
                'status'                  => 1,
                'paid'                  => $paid,
                'expiry'                   => $this->input->post('expiry') ? $this->sma->fsd($this->input->post('expiry')) : null,
                'note'         => $this->input->post('note'),
				'created_by'               => $this->session->userdata('user_id'),
            ];

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
			//if($this->input->post('paid_by')=="gift_card")
			//$payment_type=$this->input->post('transfer_account');

			}
			if ($paid==1) {

			$payment = [
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'currency'         => $this->Settings->default_currency,
				'currency_rate'         => $this->site->getCurrencyByCode($this->Settings->default_currency)->rate,

             ];
			 } else {
                $payment = [];
            }



			//$this->sma->print_arrays($payment);

            $sa_data = [];
            $ca_data = [];
            if ($this->input->post('staff_points')) {
                $sa_points = $this->input->post('sa_points');
                $user      = $this->site->getUser($this->input->post('user'));
                if ($user->award_points < $sa_points) {
                    $this->session->set_flashdata('error', lang('award_points_wrong'));
                    admin_redirect('carssales/gift_cards');
                }
                $sa_data = ['user' => $user->id, 'points' => ($user->award_points - $sa_points)];
            } elseif ($customer_details && $this->input->post('use_points')) {
                $ca_points = $this->input->post('ca_points');
                if ($customer_details->award_points < $ca_points) {
                    $this->session->set_flashdata('error', lang('award_points_wrong'));
                    admin_redirect('carssales/gift_cards');
                }
                $ca_data = ['customer' => $this->input->post('customer'), 'points' => ($customer_details->award_points - $ca_points)];
            }
             //$this->sma->print_arrays($data, $ca_data, $sa_data, $payment, $payment_type);
        } elseif ($this->input->post('add_gift_card')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('carssales/gift_cards');
        }

        if ($this->form_validation->run() == true && $this->carssales_model->addGiftCard($data, $ca_data, $sa_data, $payment, $payment_type)) {
            $this->session->set_flashdata('message', lang('gift_card_added'));
            admin_redirect('carssales/gift_cards');
        } else {
			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
            $this->data['accounts_gift_card_payments']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_gift_card_payments);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}

            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['users']      = $this->carssales_model->getStaff();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['page_title'] = lang('new_gift_card');
            $this->load->view($this->theme . 'carssales/add_gift_card', $this->data);
        }
    }

    public function add_payment($id = null)
    {
        $this->sma->checkPermissions('add_payment',true,'sales');

        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $sale = $this->carssales_model->getInvoiceByID($id);
        if ($sale->payment_status == 'paid' && $sale->grand_total == $sale->paid) {
            $this->session->set_flashdata('error', lang('sale_already_paid'));
            $this->sma->md();
        }

        //$this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
		$exchange_rate=$this->input->post('exchange_rate');
        $payment_status=$this->input->post('payment_status');
        if ($this->form_validation->run() == true) {
            $sale = $this->carssales_model->getInvoiceByID($this->input->post('sale_id'));
            if ($this->input->post('paid_by') == 'deposit') {
                $customer_id = $sale->customer_id;
                if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                    $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $customer_id = null;
            }
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
			//if(strpos($this->input->post('discount_allowance'),'%')!=-1)
            $payment = [
                'date'         => $date,
                'sale_id'      => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay'),
                //'amount'       => $this->input->post('amount-paid'),
                'amount'       => $this->input->post('amount_after_discount'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->input->post('note'),
				'currency'         => $sale->currency_code ? $sale->currency_code : $this->Settings->default_currency,
				'currency_rate'         => $this->input->post('exchange_rate'),
				'created_by'   => $this->session->userdata('user_id'),
                'type'         => $sale->sale_status == 'returned' ? 'returned' : 'received',
                'commercial_discount'    => $this->input->post('discount_allowance'),
                'commercial_discount_id'    => $this->input->post('discount'),
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
				$payment['gl_payment_method_id'] = $payment_type;

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
			//if($this->input->post('paid_by')=="gift_card")
			//$payment_type=$this->input->post('transfer_account');

			}

			//$currencyRate=$this->site->getCurrencyByCode($currencyCode)->rate;

			//$sale = $this->site->getSaleByID($payment['sale_id']);

			$amount=$payment['amount'];
			$amountPL=0;
			$amountDiff=0;


			$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
			if($payment['currency_rate'])
				$exchange_rate=$payment['currency_rate'];
			else
				$exchange_rate=$default_currencyRate;

			if($sale->currency_rate)
				$currencyRate=$sale->currency_rate;
			else
				$currencyRate=1;



				 //$this->sma->print_arrays($payment['currency_rate']);




				//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

				$amount=$payment['amount'] * $currencyRate;

				//if ($currencyRate != $exchange_rate){
					$amountPL=$payment['amount'] * $exchange_rate;
					$amountDiff=$amountPL-$amount;
				//}
				//}

				//$this->sma->print_arrays($payment_currency);

				if($amountDiff<0){
					$return_sale_ex_ref=$this->site->getReference('ex');


				//$this->sma->print_arrays($total_shipping);
			$data_ex = [
                'date'         => $date,
                'reference'    => $return_sale_ex_ref,
                'amount'       => abs($amountDiff),
                'grand_total'       => abs($amountDiff),
                'total'       => abs($amountDiff),
				'payment_status'       => 'paid',
				'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $sale->warehouse_id,
			    'other' => 1,
			];
			if($this->purchases_model->addExpense($data_ex)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}
				}
				if($amountDiff>0){
			$return_sale_re_ref=$this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay');
            $data_re = [
                'date'         => $date,
                'reference'    => $return_sale_re_ref,
                'amount'       => abs($amountDiff),
                'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $sale->warehouse_id,
			];
			if($this->products_model->addRevenue($data_re)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}

				}


					//$this->sma->print_arrays($_POST);
					//$this->sma->print_arrays($amount," ", $amountPL," ",$amountDiff);







            //$this->sma->print_arrays($payment, $customer_id,$payment_type,$payment_status);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
			if($sale->pos==1)
				admin_redirect('pos/sales');
			else
				redirect($_SERVER['HTTP_REFERER']);

        }

        if ($this->form_validation->run() == true && $this->carssales_model->addPayment($payment, $customer_id,$payment_type,$payment_status)) {
            if ($sale->shop) {
                $this->load->library('sms');
                $this->sms->paymentReceived($sale->id, $payment['reference_no'], $payment['amount']);
            }
            $this->session->set_flashdata('message', lang('payment_added'));
            if($sale->pos==1)
				admin_redirect('pos/sales');
			else
				redirect($_SERVER['HTTP_REFERER']);
        } else {
			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
            $this->data['accounts_gift_card_payments']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_gift_card_payments);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            if ($sale->sale_status == 'returned' && $sale->paid == $sale->grand_total) {
                $this->session->set_flashdata('warning', lang('payment_was_returned'));
                $this->sma->md();
            }
			$this->data['currencies'] = $this->site->getAllCurrencies();

            $this->data['inv']         = $sale;
            $this->data['payment_ref'] = ''; //$this->site->getReference('pay');
            $this->data['modal_js']    = $this->site->modal_js();

            $this->load->view($this->theme . 'carssales/add_payment', $this->data);
        }
    }

    public function combine_pdf($sales_id)
    {
        $this->sma->checkPermissions('pdf',true,'sales');

        foreach ($sales_id as $id) {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $inv                 = $this->carssales_model->getInvoiceByID($id);
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $this->data['barcode']     = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
            $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
            $this->data['payments']    = $this->carssales_model->getPaymentsForSale($id);
            $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
            $this->data['user']        = $this->site->getUser($inv->created_by);
            $this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
            $this->data['inv']         = $inv;
            $this->data['rows']        = $this->carssales_model->getAllInvoiceItems($id);
            $this->data['return_sale'] = $inv->return_id ? $this->carssales_model->getInvoiceByID($inv->return_id) : null;
            $this->data['return_rows'] = $inv->return_id ? $this->carssales_model->getAllInvoiceItems($inv->return_id) : null;
            $html_data                 = $this->load->view($this->theme . 'carssales/pdf', $this->data, true);
            if (!$this->Settings->barcode_img) {
                $html_data = preg_replace("'\<\?xml(.*)\?\>'", '', $html_data);
            }

            $html[] = [
                'content' => $html_data,
                'footer'  => $this->data['biller']->invoice_footer,
            ];

			//$this->sma->print_arrays($html);
        }

        $name = lang('sales') . '.pdf';
        $this->sma->generate_pdf($html, $name);
    }

    /* ------------------------------- */

    public function delete($id = null)
    {
        $this->sma->checkPermissions('delete',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $inv = $this->carssales_model->getInvoiceByID($id);
        if ($inv->sale_status == 'returned') {
            $this->sma->send_json(['error' => 1, 'msg' => lang('sale_x_action')]);
        }

        if ($this->carssales_model->deleteSale($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('sale_deleted')]);
            }
            $this->session->set_flashdata('message', lang('sale_deleted'));
            admin_redirect('welcome');
        }
    }

    public function delete_delivery($id = null)
    {
        $this->sma->checkPermissions('delete_delivery',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->carssales_model->deleteDelivery($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('delivery_deleted')]);
        }
    }

    public function delete_gift_card($id = null)
    {
        $this->sma->checkPermissions('delete_gift_card',true,'sales');

        if ($this->carssales_model->deleteGiftCard($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('gift_card_deleted')]);
        }
    }

    public function delete_payment($id = null)
    {
        $this->sma->checkPermissions('delete_payment',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->carssales_model->deletePayment($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang('payment_deleted'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_return($id = null)
    {

        $this->sma->checkPermissions('return_sales',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->carssales_model->deleteReturn($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('return_sale_deleted')]);
            }
            $this->session->set_flashdata('message', lang('return_sale_deleted'));
            admin_redirect('welcome');
        }
    }

    /* ------------------------------- */

    public function deliveries()
    {
        $this->sma->checkPermissions('deliveries',true,'sales');


        $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc            = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('deliveries')]];
        $meta          = ['page_title' => lang('deliveries'), 'bc' => $bc];
        $this->page_construct('carssales/deliveries', $meta, $this->data);
    }

    public function delivery_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete_delivery',true,'sales');

                    foreach ($_POST['val'] as $id) {
                        $this->carssales_model->deleteDelivery($id);
                    }
                    $this->session->set_flashdata('message', lang('deliveries_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
					if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }

                    $this->excel->getActiveSheet()->setTitle(lang('deliveries'));
                    $row = 1;
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
                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('deliveries'));
                     $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                      $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                         $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                        $row++;
                     $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('do_reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('sale_reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('status'));
                     $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($fillColor);
                     $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                     $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($Font11Bold);

                    $row ++;
                    foreach ($_POST['val'] as $id) {
                        $delivery = $this->carssales_model->getDeliveryByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($delivery->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $delivery->do_reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $delivery->sale_reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $delivery->customer);
                    //   $address = $this->carssales_model->getDeliveryByID($address);

						$address= str_replace("<p>","",$delivery->address."");
						$address= str_replace("</p>","",($address)."");
                        $address= str_replace("<br>","",($address)."");
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row,$address);

                        //$this->excel->getActiveSheet()->SetCellValue('E' . $row,$delivery->address);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang($delivery->status));


                          if($delivery->status=='delivered'){
                        $this->excel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleArrayFillGreen);
                        }else if($delivery->status=='packing'){
                        $this->excel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleArrayFillYellow);
                        }else if($delivery->status=='delivering'){
                        $this->excel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleArrayFillBlu);
                        }

                        $row++;
                    }
                     for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                        }

                   // $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                   // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                   // $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                    $filename = 'deliveries_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_delivery_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /* ------------------------------------------------------------------------ */

    public function edit($id = null)
    {
        $this->sma->checkPermissions('edit',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->carssales_model->getInvoiceByID($id);
        if ($inv->sale_status == 'returned' || $inv->return_id || $inv->return_sale_ref) {
            $this->session->set_flashdata('error', lang('sale_x_action'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->session->userdata('edit_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->form_validation->set_message('is_natural_no_zero', lang('no_zero_required'));
        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('customer', lang('customer'), 'required');
        $this->form_validation->set_rules('biller', lang('biller'), 'required');
        $this->form_validation->set_rules('sale_status', lang('sale_status'), 'required');
        $this->form_validation->set_rules('payment_status', lang('payment_status'), 'required');

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no');
            //if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            /*} else {
                $date = $inv->date;
            }*/
			//$date = $inv->date;
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $total_items      = $this->input->post('total_items');
            $sale_status      = $this->input->post('sale_status');
            $payment_status   = $this->input->post('payment_status');
            $payment_term     = $this->input->post('payment_term');
            $due_date         = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days', strtotime($date))) : null;
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = !empty($customer_details->company) && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = !empty($biller_details->company) && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note             = $this->sma->clear_tags($_POST['note']);
            $staff_note       = $this->sma->clear_tags($_POST['staff_note']);
            $quote_id         = $this->input->post('quote_id') ? $this->input->post('quote_id') : null;
			$currencyCode         = $this->input->post('currency') ? $this->input->post('currency') : $this->Settings->default_currency;
			$manual_payment         = $this->input->post('manual_payment') ? $this->input->post('manual_payment') : null;
			$po_no         = $this->input->post('po_no') ? $this->input->post('po_no') : null;

			$currencyRate=$this->site->getCurrencyByCode($currencyCode)->rate;
            $declaration_no         = $this->input->post('declaration_no') ? $this->input->post('declaration_no') : null;
			$bayan_no         = $this->input->post('bayan_no') ? $this->input->post('bayan_no') : null;
			$bill_lading_no         = $this->input->post('bill_lading_no') ? $this->input->post('bill_lading_no') : null;
            $final_bayan_date      =date('Y-m-d H:i:s');

			$supply_date      = null;
			if($inv->sale_status=="pending" && $sale_status=="completed")
			$supply_date  = date('Y-m-d H:i:s');

            $gtotal            = 0;
            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $digital          = false;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;

			if($this->input->post('order_discount')>0){
				for ($r = 0; $r < $i; $r++) {
					 $item_code          = $_POST['product_code'][$r];
					 $item_type          = $_POST['product_type'][$r];
                $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;

				if($product_details->tax_method != 1)
					$gtotal=$gtotal+($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r]);
				else
					$gtotal=$gtotal+($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r]);

				}
			}


            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' && $_POST['product_option'][$r] != 'null' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);
                $net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_serial        = $_POST['serial'][$r]           ? $_POST['serial'][$r] : '';
                $item_tax_rate      = $_POST['product_tax'][$r]      ?$_POST['product_tax'][$r] : null;
                $item_discount      = $_POST['product_discount'][$r] ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = $_POST['product_base_quantity'][$r];
                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;
                    // $unit_price = $real_unit_price;
                    if ($item_type == 'digital') {
                        $digital = true;
                    }
                    if($gtotal>0){
						$order_product_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($gtotal));
                      if($product_details->tax_method != 1)
						$item_discount=$this->sma->formatDecimal((($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					else
						$item_discount=$this->sma->formatDecimal((($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					}
					//
					//$this->sma->print_arrays($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r]);
					//$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                    if($product_details->tax_method != 1)
					$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
					else
						$pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);

                    //$this->sma->print_arrays($pr_discount);



					$unit_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);

					$item_net_price   = $net_price;


					$pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
						if($product_details->tax_method != 1)
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                        else
						$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $net_price;
                        }else{
							//$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
							$item_net_price=$net_price;
							//$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        //$item_tax    = $ctax['amount'];
                        //$tax         = $ctax['tax'];



						}
						//
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            //$total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                    $unit     = $this->site->getUnitByID($item_unit);

                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($unit_price,12),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $unit ? $unit->id : null,
                        'product_unit_code' => $unit ? $unit->code : null,
                        'unit_quantity'     => $item_unit_quantity,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $subtotal,
                        'serial_no'         => $item_serial,
                        'real_unit_price'   => $real_unit_price,
                    ];

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 12);
                    $orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }


            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
			if($gtotal>0)
			$order_discount=0;
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            if($gtotal>0)
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping)), 2);
            else
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 2);

			$data           = ['date' => $date,
                'reference_no'        => $reference,
                'customer_id'         => $customer_id,
                'customer'            => $customer,
                'biller_id'           => $biller_id,
                'biller'              => $biller,
                'warehouse_id'        => $warehouse_id,
                'note'                => $note,
                'staff_note'          => $staff_note,
                'total'               => $orginaltotal,
                'product_discount'    => $product_discount,
                'order_discount_id'   => '0',//$this->input->post('order_discount'),
                'order_discount'      => $order_discount,
                'total_discount'      => $total_discount,
                'product_tax'         => $product_tax,
                'order_tax_id'        => $this->input->post('order_tax'),
                'order_tax'           => $order_tax,
                'total_tax'           => $total_tax,
                'shipping'            => $this->sma->formatDecimal($shipping),
                'grand_total'         => $grand_total,
                'total_items'         => $total_items,
                'sale_status'         => $sale_status,
                'payment_status'      => $payment_status,
                'payment_term'        => $payment_term,
                'due_date'            => $due_date,
                'paid'                => 0,
                'created_by'          => $this->session->userdata('user_id'),
				'supply_date'          => $supply_date,
				'currency_code'                => $currencyCode,
				'currency_rate'                => $this->sma->formatDecimal($currencyRate),
				'manual_payment'                => $manual_payment,
				'po_no'                => $po_no,
                'declaration_no'                => $declaration_no,
                'bayan_no'                => $bayan_no,
                'bill_lading_no'                => $bill_lading_no,
                'final_bayan_date'                => $final_bayan_date,






            ];
            if ($this->Settings->indian_gst) {
                //$data['cgst'] = $total_cgst;
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
				//$this->sma->print_arrays($_POST['paying_gift_card_no']);
                for ($r = 0; $r < $p; $r++) {
                    if (isset($_POST['amount'][$r]) && !empty($_POST['amount'][$r]) && isset($_POST['paid_by'][$r]) && !empty($_POST['paid_by'][$r])) {
                        $amount = $this->sma->formatDecimal($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);


						if($_POST['paid_by'][$r]=="CC" && $_POST['cc_type'][$r]==''){

							$_POST['cc_type'][$r]="Mada";
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
                            ];
                        }
                    }
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


             //$this->sma->print_arrays($id,$data, $products);
        }

		if($inv->sale_status =='completed' && $inv->paid !=0){
		    $this->session->set_flashdata('error', lang('The_invoice_is_complete_and_prepaid_Please_make_a_return_to_the_invoice'));
                        redirect($_SERVER['HTTP_REFERER']);

		}else{


        if ($this->form_validation->run() == true && $this->carssales_model->updateSale($id, $data, $products)) {
            $this->session->set_userdata('remove_slls', 1);
            $this->session->set_flashdata('message', lang('sale_updated'));
            admin_redirect($inv->pos ? 'pos/carssales' : 'carssales');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $this->carssales_model->getInvoiceByID($id);
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-' . $this->Settings->disable_editing . ' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang('sale_x_edited_older_than_x_days'), $this->Settings->disable_editing));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $inv_items = $this->carssales_model->getAllInvoiceItems($id);
            // krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                 $row = $this->site->getProductByID($item->product_id);
                //$row = $this->carssales_model->getWarehouseProduct($item->product_id, $item->warehouse_id);
                if (!$row) {
                    $row             = json_decode('{}');
                    $row->tax_method = 0;
                    $row->quantity   = 0;
                } else {
                    unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                }
                $pis = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if ($pis) {
                    $row->quantity = 0;
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
				//$row->tax_method=1;
                $row->id              = $item->product_id;
                $row->code            = $item->product_code;
                $row->name            = $item->product_name;
                $row->type            = $item->product_type;
                $row->base_quantity   = $item->quantity;
                $row->base_unit       = !empty($row->unit) ? $row->unit : $item->product_unit_id;
                $row->base_unit_price = !empty($row->price) ? $row->price : $item->unit_price;
                $row->unit            = $item->product_unit_id;
                $row->qty             = $item->unit_quantity;
                $row->quantity += $item->quantity;
                $row->discount        = $item->discount ? $item->discount : '0';
                $row->item_tax        = $item->item_tax      > 0 ? $this->sma->formatDecimal($item->item_tax      / $item->quantity,12) : 0;
                $row->item_discount   = $item->item_discount > 0 ? $this->sma->formatDecimal($item->item_discount / $item->quantity,12) : 0;
                $row->price           = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($row->item_discount,12));
                $row->unit_price      = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($row->item_discount,12) + $this->sma->formatDecimal($row->item_tax,12) : $item->unit_price + $this->sma->formatDecimal($row->item_discount,12);
                $row->real_unit_price = $item->real_unit_price;
                $row->tax_rate        = $item->tax_rate_id;
                $row->serial          = $item->serial_no;
                $row->option          = $item->option_id;
                $options              = $this->carssales_model->getProductOptions($row->id, $item->warehouse_id);

                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        $option_quantity += $item->quantity;
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }

                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->carssales_model->getProductComboItems($row->id, $item->warehouse_id);
                    $te          = $combo_items;
                    foreach ($combo_items as $combo_item) {
                        $combo_item->quantity = $combo_item->qty * $item->quantity;
                    }
                }
                $units    = !empty($row->base_unit) ? $this->site->getUnitsByBUID($row->base_unit) : null;
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $ri       = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $c++;
            }

			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}


            $this->data['inv_items'] = json_encode($pr);
            $this->data['id']        = $id;
            $this->data['currencies'] = $this->site->getAllCurrencies();
            $this->data['billers']    = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->site->getAllCompanies('biller') : null;
            $this->data['units']      = $this->site->getAllBaseUnits();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['warehouses'] = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('edit_sale')]];
            $meta = ['page_title' => lang('edit_sale'), 'bc' => $bc];
            $this->page_construct('carssales/edit', $meta, $this->data);
        }
		}
    }

    public function edit_delivery($id = null)
    {
        $this->sma->checkPermissions('edit_delivery',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('do_reference_no', lang('do_reference_no'), 'required');
        $this->form_validation->set_rules('sale_reference_no', lang('sale_reference_no'), 'required');
        $this->form_validation->set_rules('customer', lang('customer'), 'required');
        $this->form_validation->set_rules('address', lang('address'), 'required');

        if ($this->form_validation->run() == true) {
            $dlDetails = [
                'sale_id'           => $this->input->post('sale_id'),
                'do_reference_no'   => $this->input->post('do_reference_no'),
                'sale_reference_no' => $this->input->post('sale_reference_no'),
                'customer'          => $this->input->post('customer'),
                'address'           => $this->input->post('address'),
                'status'            => $this->input->post('status'),
                'delivered_by'      => $this->input->post('delivered_by'),
                'received_by'       => $this->input->post('received_by'),
                'note'              => $this->sma->clear_tags($this->input->post('note')),
                'created_by'        => $this->session->userdata('user_id'),
            ];

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
                $photo                   = $this->upload->file_name;
                $dlDetails['attachment'] = $photo;
            }

            //if ($this->Owner || $this->Admin) {
                $date              = $this->sma->fld(trim($this->input->post('date')));
                $dlDetails['date'] = $date;
           // }
        } elseif ($this->input->post('edit_delivery')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->carssales_model->updateDelivery($id, $dlDetails)) {
			if($dlDetails['status']=='delivered')
			$this->carssales_model->updateStatus($dlDetails['sale_id'],'completed','delivered');
            $this->session->set_flashdata('message', lang('delivery_updated'));
            admin_redirect('carssales/deliveries');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['delivery'] = $this->carssales_model->getDeliveryByID($id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'carssales/edit_delivery', $this->data);
        }
    }

    public function edit_gift_card($id = null)
    {
        $this->sma->checkPermissions('edit_gift_card',true,'sales');

        $this->form_validation->set_rules('card_no', lang('card_no'), 'trim|required');
        $gc_details = $this->site->getGiftCardByID($id);
        if ($this->input->post('card_no') != $gc_details->card_no) {
            $this->form_validation->set_rules('card_no', lang('card_no'), 'is_unique[gift_cards.card_no]');
        }
        $this->form_validation->set_rules('value', lang('value'), 'required');
        //$this->form_validation->set_rules('customer', lang("customer"), 'xss_clean');

        if ($this->form_validation->run() == true) {
			$paid = $this->input->post('paid_amount')? $this->input->post('paid') : 0;
            $paid_amount = $paid==1 && $this->input->post('paid_amount')? $this->input->post('paid_amount') : 0;
            $tax_rate = $paid==1 && $this->input->post('tax_rate')? $this->input->post('tax_rate') : 0;
            $tax_amount = $paid==1 && $this->input->post('tax_amount')? $this->input->post('tax_amount') : 0;
            $total_paid_amount = $paid==1 && $this->input->post('total_paid_amount')? $this->input->post('total_paid_amount') : 0;

            $gift_card        = $this->site->getGiftCardByID($id);
            $customer_details = $this->input->post('customer') ? $this->site->getCompanyByID($this->input->post('customer')) : null;
            $customer         = $customer_details ? $customer_details->company : null;
            $data             = ['card_no' => $this->input->post('card_no'),
                'value'                    => $this->input->post('value'),
                'customer_id'              => $this->input->post('customer') ? $this->input->post('customer') : null,
                'customer'                 => $customer,
                'balance'                  => ($this->input->post('value') - $gift_card->value) + $gift_card->balance,
                'paid_amount'                  => $paid_amount,
                'tax_rate'                  => $tax_rate,
                'tax_amount'                  => $tax_amount,
                'total_paid_amount'                  => $total_paid_amount,
                'status'                  => $this->input->post('status'),
                'note'                  => $this->input->post('note'),
                'paid'                  => $paid,
                'expiry'                   => $this->input->post('expiry') ? $this->sma->fsd($this->input->post('expiry')) : null,
            ];



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
			//if($this->input->post('paid_by')=="gift_card")
			//$payment_type=$this->input->post('transfer_account');

			}
			if ($paid==1) {

			$payment = [
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'currency'         => $this->Settings->default_currency,
				'currency_rate'         => $this->site->getCurrencyByCode($this->Settings->default_currency)->rate,

             ];
			 } else {
                $payment = [];
            }

        } elseif ($this->input->post('edit_gift_card')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('carssales/gift_cards');
        }

        if ($this->form_validation->run() == true && $this->carssales_model->updateGiftCard($id, $data, $payment, $payment_type)) {
            $this->session->set_flashdata('message', lang('gift_card_updated'));
            admin_redirect('carssales/gift_cards');
        } else {
			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
            $this->data['accounts_gift_card_payments']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_gift_card_payments);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}

            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$payment                  = $this->carssales_model->getPaymentRevenueByCardID($id);
        //$this->sma->print_arrays($payment);
            $this->data['error']     = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['gift_card'] = $this->site->getGiftCardByID($id);
            $this->data['id']        = $id;
            $this->data['modal_js']  = $this->site->modal_js();
            $this->load->view($this->theme . 'carssales/edit_gift_card', $this->data);
        }
    }

    public function edit_payment($id = null)
    {
        $this->sma->checkPermissions('edit_payment',true,'sales');

        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $payment = $this->carssales_model->getPaymentByID($id);
        if ($payment->paid_by == 'ppp' || $payment->paid_by == 'stripe' || $payment->paid_by == 'paypal' || $payment->paid_by == 'skrill') {
            $this->session->set_flashdata('error', lang('x_edit_payment'));
            $this->sma->md();
        }
        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
               $sale        = $this->carssales_model->getInvoiceByID($this->input->post('sale_id'));
            if ($this->input->post('paid_by') == 'deposit') {
                 $customer_id = $sale->customer_id;
                $amount      = $this->input->post('amount-paid') - $payment->amount;
                if (!$this->site->check_customer_deposit($customer_id, $amount)) {
                    $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $customer_id = null;
            }
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = $payment->date;
            }
            $payment = [
                'date'         => $date,
                'sale_id'      => $this->input->post('sale_id'),
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
				'note'         => $this->input->post('note'),
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
			//if($this->input->post('paid_by')=="gift_card")
			//$payment_type=$this->input->post('transfer_account');


			    $payment['gl_payment_method_id'] = $payment_type;


			}

    //$this->sma->print_arrays($sale->pos);

        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            if($sale->pos==1)
				admin_redirect('pos/sales');
			else
				redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->carssales_model->updatePayment($id, $payment, $customer_id,$payment_type)) {
            $this->session->set_flashdata('message', lang('payment_updated'));
			if($sale->pos==1)
				admin_redirect('pos/sales');
			else
			admin_redirect('carssales');
        } else {
            if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
            $this->data['accounts_gift_card_payments']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_gift_card_payments);

            	//$this->sma->print_arrays($this->data['payment_types']);

			}


			$this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['currencies'] = $this->site->getAllCurrencies();
			$this->data['payment']  = $payment;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'carssales/edit_payment', $this->data);
        }
    }

    public function email($id = null)
    {
        $this->sma->checkPermissions('email',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->carssales_model->getInvoiceByID($id);
        $this->form_validation->set_rules('to', lang('to') . ' ' . lang('email'), 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', lang('subject'), 'trim|required');
        $this->form_validation->set_rules('cc', lang('cc'), 'trim|valid_emails');
        $this->form_validation->set_rules('bcc', lang('bcc'), 'trim|valid_emails');
        $this->form_validation->set_rules('note', lang('message'), 'trim');

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
            $customer = $this->site->getCompanyByID($inv->customer_id);
            $biller   = $this->site->getCompanyByID($inv->biller_id);
            $this->load->library('parser');
            $parse_data = [
                'reference_number' => $inv->reference_no,
                'contact_person'   => $customer->name,
                'company'          => $customer->company && $customer->company != '-' ? '(' . $customer->company . ')' : '',
                'order_link'       => $inv->shop ? shop_url('orders/' . $inv->id . '/' . ($this->loggedIn ? '' : $inv->hash)) : base_url(),
                'site_link'        => base_url(),
                'site_name'        => $this->Settings->site_name,
                'logo'             => '<img src="' . base_url() . 'assets/uploads/logos/' . $biller->logo . '" alt="' . ($biller->company && $biller->company != '-' ? $biller->company : $biller->name) . '"/>',
            ];
            $msg      = $this->input->post('note');
            $message  = $this->parser->parse_string($msg, $parse_data);
            $paypal   = $this->carssales_model->getPaypalSettings();
            $skrill   = $this->carssales_model->getSkrillSettings();
            $btn_code = '<div id="payment_buttons" class="text-center margin010">';
            if ($paypal->active == '1' && $inv->grand_total != '0.00') {
                if (trim(strtolower($customer->country)) == $biller->country) {
                    $paypal_fee = $paypal->fixed_charges + ($inv->grand_total * $paypal->extra_charges_my / 100);
                } else {
                    $paypal_fee = $paypal->fixed_charges + ($inv->grand_total * $paypal->extra_charges_other / 100);
                }
                $btn_code .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=' . $paypal->account_email . '&item_name=' . $inv->reference_no . '&item_number=' . $inv->id . '&image_url=' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '&amount=' . (($inv->grand_total - $inv->paid) + $paypal_fee) . '&no_shipping=1&no_note=1&currency_code=' . $this->default_currency->code . '&bn=FC-BuyNow&rm=2&return=' . admin_url('carssales/view/' . $inv->id) . '&cancel_return=' . admin_url('carssales/view/' . $inv->id) . '&notify_url=' . admin_url('payments/paypalipn') . '&custom=' . $inv->reference_no . '__' . ($inv->grand_total - $inv->paid) . '__' . $paypal_fee . '"><img src="' . base_url('assets/images/btn-paypal.png') . '" alt="Pay by PayPal"></a> ';
            }
            if ($skrill->active == '1' && $inv->grand_total != '0.00') {
                if (trim(strtolower($customer->country)) == $biller->country) {
                    $skrill_fee = $skrill->fixed_charges + ($inv->grand_total * $skrill->extra_charges_my / 100);
                } else {
                    $skrill_fee = $skrill->fixed_charges + ($inv->grand_total * $skrill->extra_charges_other / 100);
                }
                $btn_code .= ' <a href="https://www.moneybookers.com/app/payment.pl?method=get&pay_to_email=' . $skrill->account_email . '&language=EN&merchant_fields=item_name,item_number&item_name=' . $inv->reference_no . '&item_number=' . $inv->id . '&logo_url=' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '&amount=' . (($inv->grand_total - $inv->paid) + $skrill_fee) . '&return_url=' . admin_url('carssales/view/' . $inv->id) . '&cancel_url=' . admin_url('carssales/view/' . $inv->id) . '&detail1_description=' . $inv->reference_no . '&detail1_text=Payment for the sale invoice ' . $inv->reference_no . ': ' . $inv->grand_total . '(+ fee: ' . $skrill_fee . ') = ' . $this->sma->formatMoney($inv->grand_total + $skrill_fee) . '&currency=' . $this->default_currency->code . '&status_url=' . admin_url('payments/skrillipn') . '"><img src="' . base_url('assets/images/btn-skrill.png') . '" alt="Pay by Skrill"></a>';
            }

            $btn_code .= '<div class="clearfix"></div></div>';
            $message    = $message . $btn_code;
            $attachment = $this->pdf($id, null, 'S');

            try {
                if ($this->sma->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                    delete_files($attachment);
                    $this->session->set_flashdata('message', lang('email_sent'));
                    admin_redirect('carssales');
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
            if (file_exists('./themes/' . $this->Settings->theme . '/admin/views/email_templates/sale.html')) {
                $sale_temp = file_get_contents('themes/' . $this->Settings->theme . '/admin/views/email_templates/sale.html');
            } else {
                $sale_temp = file_get_contents('./themes/default/admin/views/email_templates/sale.html');
            }

            $this->data['subject'] = ['name' => 'subject',
                'id'                         => 'subject',
                'type'                       => 'text',
                'value'                      => $this->form_validation->set_value('subject', lang('invoice') . ' (' . $inv->reference_no . ') ' . lang('from') . ' ' . $this->Settings->site_name),
            ];
            $this->data['note'] = ['name' => 'note',
                'id'                      => 'note',
                'type'                    => 'text',
                'value'                   => $this->form_validation->set_value('note', $sale_temp),
            ];
            $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);

            $this->data['id']       = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'carssales/email', $this->data);
        }
    }

    public function email_payment($id = null)
    {
        $this->sma->checkPermissions('payments',true,'sales');

        $payment              = $this->carssales_model->getPaymentByID($id);
        $inv                  = $this->carssales_model->getInvoiceByID($payment->sale_id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $customer             = $this->site->getCompanyByID($inv->customer_id);
        if (!$customer->email) {
            $this->sma->send_json(['msg' => lang('update_customer_email')]);
        }
        $this->data['inv']        = $inv;
        $this->data['payment']    = $payment;
        $this->data['customer']   = $customer;
        $this->data['page_title'] = lang('payment_note');
        $html                     = $this->load->view($this->theme . 'carssales/payment_note', $this->data, true);

        $html = str_replace(['<i class="fa fa-2x">&times;</i>', 'modal-', '<p>&nbsp;</p>', '<p style="border-bottom: 1px solid #666;">&nbsp;</p>', '<p>' . lang('stamp_sign') . '</p>'], '', $html);
        $html = preg_replace("/<img[^>]+\>/i", '', $html);
        // $html = '<div style="border:1px solid #DDD; padding:10px; margin:10px 0;">'.$html.'</div>';

        $this->load->library('parser');
        $parse_data = [
            'stylesheet' => '<link href="' . $this->data['assets'] . 'styles/helpers/bootstrap.min.css" rel="stylesheet"/>',
            'name'       => $customer->company && $customer->company != '-' ? $customer->company : $customer->name,
            'email'      => $customer->email,
            'heading'    => lang('payment_note') . '<hr>',
            'msg'        => $html,
            'site_link'  => base_url(),
            'site_name'  => $this->Settings->site_name,
            'logo'       => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>',
        ];
        $msg     = file_get_contents('./themes/' . $this->Settings->theme . '/admin/views/email_templates/email_con.html');
        $message = $this->parser->parse_string($msg, $parse_data);
        $subject = lang('payment_note') . ' - ' . $this->Settings->site_name;

        if ($this->sma->send_email($customer->email, $subject, $message)) {
            $this->sma->send_json(['msg' => lang('email_sent')]);
        } else {
            $this->sma->send_json(['msg' => lang('email_failed')]);
        }
    }

    public function get_award_points($id = null)
    {
        $this->sma->checkPermissions('index',true,'sales');


        $row = $this->site->getUser($id);
        $this->sma->send_json(['sa_points' => $row->award_points]);
    }

    public function getDeliveries()
    {
        $this->sma->checkPermissions('deliveries',true,'sales');


        $detail_link = anchor('admin/carssales/view_delivery/$1', '<i class="fa fa-file-text-o"></i> ' . lang('delivery_details'), 'data-toggle="modal" data-target="#myModal"');
        $email_link  = anchor('admin/carssales/email_delivery/$1', '<i class="fa fa-envelope"></i> ' . lang('email_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link   = anchor('admin/carssales/edit_delivery/$1', '<i class="fa fa-edit"></i> ' . lang('edit_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $pdf_link    = anchor('admin/carssales/pdf_delivery/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $delete_link = "<a href='#' class='po' title='<b>" . lang('delete_delivery') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('carssales/delete_delivery/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_delivery') . '</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
    <ul class="dropdown-menu pull-right" role="menu">
        <li>' . $detail_link . '</li>
        <li>' . $edit_link . '</li>
        <li>' . $pdf_link . '</li>
        <li>' . $delete_link . '</li>
    </ul>
</div></div>';

        $this->load->library('datatables');
        //GROUP_CONCAT(CONCAT('Name: ', sale_items.product_name, ' Qty: ', sale_items.quantity ) SEPARATOR '<br>')
        $this->datatables
            ->select('deliveries.id as id, date, do_reference_no, sale_reference_no, customer, address, status, attachment')
            ->from('deliveries')
            ->join('sale_items', 'sale_items.sale_id=deliveries.sale_id', 'left')
            ->group_by('deliveries.id');
        $this->datatables->add_column('Actions', $action, 'id');

        echo $this->datatables->generate();
    }

    public function getGiftCards()
    {


		$view_gift_card     = anchor('admin/carssales/view_gift_card/$1', '<i class="fa fa-eye"></i> ' . lang('view_gift_card'), 'data-toggle="modal" data-target="#myModal"');
		$topup_gift_card     = anchor('admin/carssales/topup_gift_card/$1', '<i class="fa fa-dollar"></i> ' . lang('topup_gift_card'), 'data-toggle="modal" data-target="#myModal"');
		$edit_gift_card     = anchor('admin/carssales/edit_gift_card/$1', '<i class="fa fa-edit"></i> ' . lang('edit_gift_card'), 'data-toggle="modal" data-target="#myModal"');
		$delete_gift_card       = "<a href='#' class='po' title='<b>" . lang('delete_gift_card') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('carssales/delete_gift_card/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_gift_card') . '</a>';
        $payments_link     = anchor('admin/carssales/payment_note_gift_card/$1', '<i class="fa fa-file-text-o"></i> ' . lang('payment_note'), 'data-toggle="modal" data-target="#myModal"');
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $payments_link . '</li>
            <li>' . $view_gift_card . '</li>
            <li>' . $topup_gift_card . '</li>
            <li>' . $edit_gift_card . '</li>
            <li>' . $delete_gift_card . '</li>

        </ul>
    </div></div>';

		$this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('gift_cards') . '.id as id, CONCAT("XXXXXXXXXXXXXXXX","",SUBSTRING(card_no,-4)) as card_no, value, balance, CONCAT(' . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . '.last_name) as created_by, customer, paid, total_paid_amount, status, expiry', false)
            ->join('users', 'users.id=gift_cards.created_by', 'left')
            ->from('gift_cards')
            //->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('carssales/view_gift_card/$1') . "' class='tip' title='" . lang('view_gift_card') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-eye\"></i></a> <a href='" . admin_url('carssales/topup_gift_card/$1') . "'  class='tip' title='" . lang('topup_gift_card') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-dollar\"></i></a> <a href='" . admin_url('carssales/edit_gift_card/$1') . "' class='tip' title='" . lang('edit_gift_card') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_gift_card') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('carssales/delete_gift_card/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
            ->add_column('Actions', $action, 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function getSales($warehouse_id = null)
    {
        $this->sma->checkPermissions('index',true,'sales');


        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            //$warehouse_id = $user->warehouse_id;
			//$this->sma->print_arrays($warehouse_id);
        }


        //$this->sma->print_arrays($_GET['panel_no'] );

		$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $reference_no    = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $product_id    = $this->input->get('product_id') ? $this->input->get('product_id') : null;
        $panel_no    = $this->input->get('panel_no') ? $this->input->get('panel_no') : null;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : null;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : null;
        $warehouse_id   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $sale_status   = $this->input->get('sale_status') ? $this->input->get('sale_status') : null;
        $payment_status   = $this->input->get('payment_status') ? $this->input->get('payment_status') : null;
        $product_code    = $this->input->get('product_code') ? $this->input->get('product_code') : null;


		if ($start_date && $end_date) {
			$start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
		   }


        $detail_link       = anchor('admin/carssales/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details'));
        $duplicate_link    = anchor('admin/carssales/add?sale_id=$1', '<i class="fa fa-plus-circle"></i> ' . lang('duplicate_sale'));
        $payments_link     = anchor('admin/carssales/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link  = anchor('admin/carssales/add_payment/$1', '<i class="fa fa-dollar"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $expenses_link     = anchor('admin/carssales/expenses/$1', '<i class="fa fa-money"></i> ' . lang('view_expense'), 'data-toggle="modal" data-target="#myModal"');
        $add_expenses_link  = anchor('admin/carspurchases/add_expense/$1', '<i class="fa fa-money"></i> ' . lang('add_expense'), 'data-toggle="modal" data-target="#myModal"');
        $packagink_link    = anchor('admin/carssales/packaging/$1', '<i class="fa fa-archive"></i> ' . lang('packaging'), 'data-toggle="modal" data-target="#myModal"');
        $add_delivery_link = anchor('admin/carssales/add_delivery/$1', '<i class="fa fa-truck"></i> ' . lang('add_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $email_link        = anchor('admin/carssales/email/$1', '<i class="fa fa-envelope"></i> ' . lang('email_sale'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link         = anchor('admin/carssales/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_sale'), 'class="sledit"');
        $pdf_link          = anchor('admin/carssales/pdf/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $return_link       = anchor('admin/carssales/return_sale/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_sale'));

        $delete_link       = "<a href='#' class='po' title='<b>" . lang('delete_sale') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('carssales/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_sale') . '</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $duplicate_link . '</li>
            <li>' . $payments_link . '</li>
            <li>' . $add_payment_link . '</li>
            <li>' . $expenses_link . '</li>
            <li>' . $add_expenses_link . '</li>
            <li>' . $packagink_link . '</li>
            <li>' . $add_delivery_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $email_link . '</li>
            <li>' . $return_link . '</li>

        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';
		//<li>' . $pdf_link . '</li>
		// <li>' . $delete_link . '</li>
            //(CASE WHEN cgst IS NOT NULL THEN ((grand_total * cgst)-paid) ELSE (grand_total-paid) END)  as


        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("{$this->db->dbprefix('sales')}.id as id, DATE_FORMAT({$this->db->dbprefix('sales')}.date, '%Y-%m-%d %T') as date, {$this->db->dbprefix('sales')}.id as inv_id, {$this->db->dbprefix('sales')}.biller, {$this->db->dbprefix('sales')}.customer, {$this->db->dbprefix('sales')}.sale_status, {$this->db->dbprefix('sales')}.grand_total, {$this->db->dbprefix('sales')}.paid, {$this->db->dbprefix('sales')}.discount_allowance, ({$this->db->dbprefix('sales')}.grand_total-({$this->db->dbprefix('sales')}.paid+{$this->db->dbprefix('sales')}.discount_allowance))  as balance, {$this->db->dbprefix('sales')}.payment_status, {$this->db->dbprefix('sales')}.attachment, {$this->db->dbprefix('sales')}.return_id")
                ->from('sales')
                ->where('warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("{$this->db->dbprefix('sales')}.id as id, DATE_FORMAT({$this->db->dbprefix('sales')}.date, '%Y-%m-%d %T') as date, {$this->db->dbprefix('sales')}.id as inv_id, {$this->db->dbprefix('sales')}.biller, {$this->db->dbprefix('sales')}.customer, {$this->db->dbprefix('sales')}.sale_status, {$this->db->dbprefix('sales')}.grand_total, {$this->db->dbprefix('sales')}.paid, {$this->db->dbprefix('sales')}.discount_allowance, ({$this->db->dbprefix('sales')}.grand_total-({$this->db->dbprefix('sales')}.paid+{$this->db->dbprefix('sales')}.discount_allowance)) as balance, {$this->db->dbprefix('sales')}.payment_status, {$this->db->dbprefix('sales')}.attachment, {$this->db->dbprefix('sales')}.return_id")
                ->from('sales');
        }


        /*if ($this->input->get('shop') == 'yes') {
            $this->datatables->where('shop', 1);
        } elseif ($this->input->get('shop') == 'no') {
            $this->datatables->where('shop !=', 1);
        }
        if ($this->input->get('delivery') == 'no') {
            $this->datatables->join('deliveries', 'deliveries.sale_id=sales.id', 'left')
            ->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
            ->where("({$this->db->dbprefix('deliveries')}.status != 'delivered' OR {$this->db->dbprefix('deliveries')}.status IS NULL)", null);
        }
        if ($this->input->get('attachment') == 'yes') {
            $this->datatables->where('payment_status !=', 'paid')->where('attachment !=', null);
        }


        if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Customer) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }
		*/

		$this->datatables->where('pos !=', 1); // ->where('sale_status !=', 'returned');

		if ($start_date && $end_date) {
                 $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
           }

            if ($reference_no) {
                $this->datatables->where($this->db->dbprefix('sales') . '.reference_no', $reference_no);
            }
            if ($product_code)
            {
                //$this->sma->print_arrays($_GET['product_code']);

                $this->datatables->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
               // ->where($this->db->dbprefix('sale_items') . '.product_code ="'.$product_code.'"')
               ->where('sale_items.product_code', trim($product_code));
              // ->like('sale_items.product_code', ($_GET['product_code']), 'both');

                //$this->datatables->where("(sales.id in (select salse_id from sale_items where product_id='2'))");
                //$this->datatables->where("(sales.id in  (select salse_id from sale_items where product_id='2'))");
             }
            if ($panel_no) {
                //$this->sma->print_arrays($_GET['panel_no']);
                $this->datatables->join('sale_items', 'sale_items.sale_id=sales.id', 'left');
                $this->datatables->join('cars_vehicles', 'cars_vehicles.vehicle_id =sale_items.product_id', 'left');
                $this->datatables->where($this->db->dbprefix('cars_vehicles') . '.panel_no', trim($_GET['panel_no']));
            }
            if ($customer) {
                $this->datatables->where($this->db->dbprefix('sales') . '.customer_id', $customer);
            }
            if ($biller) {
                $this->datatables->where($this->db->dbprefix('sales') . '.biller_id', $biller);
            }
              if ($sale_status) {
                $this->datatables->where($this->db->dbprefix('sales') . '.sale_status', $sale_status);
            }
             if ($payment_status) {
                $this->datatables->where($this->db->dbprefix('sales') . '.payment_status', $payment_status);
            }

        $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }

    public function gift_card_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete_gift_card',true,'sales');

                    foreach ($_POST['val'] as $id) {
                        $this->carssales_model->deleteGiftCard($id);
                    }
                    $this->session->set_flashdata('message', lang('gift_cards_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('gift_cards'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('card_no'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('value'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('customer'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->site->getGiftCardByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->card_no);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->value);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->customer);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'gift_cards_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_gift_card_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /* ------------------------------------ Gift Cards ---------------------------------- */

    public function gift_cards()
    {
        $this->sma->checkPermissions('gift_cards',true,'sales');


        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('carssales')], ['link' => '#', 'page' => lang('gift_cards')]];
        $meta = ['page_title' => lang('gift_cards'), 'bc' => $bc];
        $this->page_construct('carssales/gift_cards', $meta, $this->data);
    }
	/*
	public function balance_mun()
    {


		$sales=$this->pos2_model->getAllSalesByDate('2022-09-16 00:00:00','2022-09-16 01:42:00');
		//$sales=$this->pos2_model->getAllSalesByDate('2022-09-15 00:00:00','2022-09-15 23:59:59');

		foreach($sales as $sale){
		$products=$this->pos2_model->getSaleItems($sale->id);
		//$sales = json_decode(json_encode($sales), true);
		$products = json_decode(json_encode($products), true);
		$costing=$this->pos2_model->costing2($products);
		if($costing){
		$this->pos2_model->syncPurchaseItems($costing);
		$this->pos2_model->syncQuantity($sale->id);
		//$this->sma->print_arrays($products);
		}
		}
		//$sales=$this->pos2_model->getInvoiceByID('1722');
		//$products=$this->pos2_model->getSaleItems($sale->id);
		//$sales = json_decode(json_encode($sales), true);
		//$products = json_decode(json_encode($products), true);
		//$costing=$this->pos2_model->costing($products);
		//$this->pos2_model->syncPurchaseItems($costing);
		//$this->pos2_model->syncQuantity($sale->id);
		//$this->sma->print_arrays($costing);

	}
    */

	/*public function dup_reculc()
    {
		    //$clients = $otherdb->get('client')->row();
			 $q = $this->db->get('temp');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $dup_sales[] = $row;
            }
		}

		foreach ($dup_sales as $dup_sale) {
			//$this->sma->print_arrays($dup_sale->id);

			$this->carssales_model->deleteSale($dup_sale->id);
			//$this->sma->print_arrays($dup_sale->id);

		}
	}*/
    public function index($warehouse_id = null)
    {





        $this->sma->checkPermissions('index',true,'sales');


		$warehouse_id   = $this->input->post('warehouse') ? $this->input->post('warehouse') : null;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }
		$this->data['billers']    = $this->site->getAllCompanies('biller');


        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('sales')]];
        $meta = ['page_title' => lang('sales'), 'bc' => $bc];
        $this->page_construct('carssales/index', $meta, $this->data);
    }

	public function modal_view_ByRef($ref = null)
    {
        $this->sma->checkPermissions('index',true,'sales');

        if ($this->input->get('ref')) {
            $ref = $this->input->get('ref');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->carssales_model->getSaleByReference($ref);

		//$this->sma->print_arrays($ref);
		$this->modal_view($inv->id);
    }

    public function modal_view($id = null)
    {
        $this->sma->checkPermissions('index',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->carssales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
      //  $this->data['units']       = $this->site->getAllUnits();
        $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->site->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['warehouse']   = $this->cars_model->getShowroomByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['rows']        = $this->carssales_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->carssales_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->carssales_model->getAllInvoiceItems($inv->return_id) : null;
		$this->data['payments']        = $this->carssales_model->getInvoicePayments($inv->id);
		$this->data['active_tax']        =$this->carssales_model->getTaxRatesActive()->rate;
		$this->data['return_payments'] = $inv->return_id ? $this->carssales_model->getInvoicePayments($inv->return_id) : null;

        //$this->data['AmountInWordsAr']       = $this->sma->getFRACTION($this->sma->formatDecimal($receipt->total_amount,2),'SR','هللة');
		//$this->data['AmountInWordsEn']       = $this->sma->AmountInWords($this->sma->formatDecimal($receipt->total_amount,2),'SR','Halala');


       // $this->sma->print_arrays($this->data['warehouse'] );

        if($this->Settings->invoice_view==2)
			$this->load->view($this->theme . 'carssales/modal_view', $this->data);
		else if($this->Settings->invoice_view==3)
			$this->load->view($this->theme . 'carssales/modal_view_3', $this->data);
		else if($this->Settings->invoice_view==4)
			$this->load->view($this->theme . 'carssales/modal_view_4', $this->data);
		else
			$this->load->view($this->theme . 'carssales/modal_view_2', $this->data);

    }

    public function credit_note_view($id = null)
    {
                $this->sma->checkPermissions('index',true,'sales');


        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->carssales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
        $this->data['units']       = $this->site->getAllUnits();
        $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->site->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['rows']        = $this->carssales_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->carssales_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->carssales_model->getAllInvoiceItems($inv->return_id) : null;
		$this->data['payments']        = $this->carssales_model->getInvoicePayments($inv->id);
		$this->data['return_payments'] = $inv->return_id ? $this->carssales_model->getInvoicePayments($inv->return_id) : null;
        $this->data['active_tax']        =$this->carssales_model->getTaxRatesActive()->rate;
		//$this->data['AmountInWordsAr']       = $this->sma->getFRACTION($this->sma->formatDecimal($receipt->total_amount,2),'SR','هللة');
		//$this->data['AmountInWordsEn']       = $this->sma->AmountInWords($this->sma->formatDecimal($receipt->total_amount,2),'SR','Halala');


		//$this->sma->print_arrays($this->Settings->invoice_view);

        	$this->load->view($this->theme . 'carssales/credit_note_view', $this->data);


    }

    public function modal_view_product($id = null)
    {
                $this->sma->checkPermissions('index',true,'sales');


        if ($this->input->get('id')) {
            $id = $this->input->get('id');


        }


		    $tmp=strpos($id,"_");
			$pid = substr($id, $tmp+1);
			$id = substr($id, 0,$tmp);



        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->carssales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
        $this->data['units']       = $this->site->getAllUnits();
        $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->site->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['rows']        = $this->carssales_model->getProductInvoiceItem($id,$pid);
        $this->data['return_sale'] = $inv->return_id ? $this->carssales_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->carssales_model->getProductInvoiceItem($inv->return_id,$pid) : null;
		$this->data['payments']        = $this->carssales_model->getInvoicePayments($inv->id);
		$this->data['return_payments'] = $inv->return_id ? $this->carssales_model->getInvoicePayments($inv->return_id) : null;




        $this->load->view($this->theme . 'carssales/modal_view_product', $this->data);
    }

    public function packaging($id)
    {
        $sale                   = $this->carssales_model->getInvoiceByID($id);
        $this->data['returned'] = false;
        if ($sale->sale_status == 'returned' || $sale->return_id) {
            $this->data['returned'] = true;
        }
        $this->data['warehouse'] = $this->site->getWarehouseByID($sale->warehouse_id);
        $items                   = $this->carssales_model->getAllInvoiceItems($sale->id);
        foreach ($items as $item) {
            $packaging[] = [
                'name'     => $item->product_code . ' - ' . $item->product_name,
                'quantity' => $item->quantity,
                'unit'     => $item->product_unit_code,
                'rack'     => $this->carssales_model->getItemRack($item->product_id, $sale->warehouse_id),
            ];
        }
        $this->data['packaging'] = $packaging;
        $this->data['sale']      = $sale;

        $this->load->view($this->theme . 'carssales/packaging', $this->data);
    }

    public function payment_note($id = null)
    {
        $this->sma->checkPermissions('payment_note',true,'sales');

        $payment                  = $this->carssales_model->getPaymentByID($id);
        $inv                      = $this->carssales_model->getInvoiceByID($payment->sale_id);
        $this->data['biller']     = $this->site->getCompanyByID($inv->biller_id);
        $this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['customer']   = $this->site->getCompanyByID($inv->customer_id);
        $this->data['inv']        = $inv;
        $this->data['payment']    = $payment;
        $this->data['page_title'] = lang('payment_note');

        $this->load->view($this->theme . 'carssales/payment_note', $this->data);
    }

    public function payment_note_gift_card($id = null)
    {
        $this->sma->checkPermissions('payment_note_gift_card',true,'sales');

        $inv                      = $this->carssales_model->getPaymentGiftCardsByID($id);

		$payment                  = $this->carssales_model->getPaymentRevenueByCardID($inv->id);
        //$this->sma->print_arrays($payment,$inv);
        $this->data['biller']     = $this->site->getCompanyByID($inv->biller_id);
        $this->data['warehouse']   = $this->site->getWarehouseByID($this->Settings->default_warehouse);
        $this->data['customer']   = $this->site->getCompanyByID($payment->customer_id);
        $this->data['inv']        = $inv;
        $this->data['payment']    = $payment;
        $this->data['page_title'] = lang('payment_note');

        $this->load->view($this->theme . 'carssales/payment_note_gift_card', $this->data);
    }

    /* -------------------------------------------------------------------------------- */

    public function payments($id = null)
    {
        $this->sma->checkPermissions('payments',true,'sales');
        $this->data['payments'] = $this->carssales_model->getInvoicePayments($id);
        $this->data['inv']      = $this->carssales_model->getInvoiceByID($id);
        $this->load->view($this->theme . 'carssales/payments', $this->data);
    }
    public function expenses($id = null)
    {
      //  $this->sma->checkPermissions('expenses',true,'purchases');

        $this->data['expenses'] = $this->carssales_model->getInvoiceExpenses($id);
        $this->data['inv']      = $this->carssales_model->getInvoiceByID($id);
        $this->load->view($this->theme . 'carssales/expenses', $this->data);
    }

	public function payments_report()
    {
        $this->sma->checkPermissions('payments_report',true,'sales');
        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']        = $this->carsreports_model->getStaff();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['pos_settings'] = POS ? $this->carsreports_model->getPOSSetting('biller') : false;
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('payments_report')]];
        $meta                       = ['page_title' => lang('payments_report'), 'bc' => $bc];
		//$this->sma->print_arrays($this->data);

		$this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carssales/payments_report', $meta, $this->data);
    }

	public function commercial_discounts_report()
    {
        $this->sma->checkPermissions('commercial_discounts',true,'sales');

        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']        = $this->carsreports_model->getStaff();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['pos_settings'] = POS ? $this->carsreports_model->getPOSSetting('biller') : false;
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('Discount_Allowance')]];
        $meta                       = ['page_title' => lang('Discount_Allowance'), 'bc' => $bc];
		//$this->sma->print_arrays($this->data);

		$this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carssales/commercial_discounts_report', $meta, $this->data);
    }

	public function customers_payments_report()
    {
        $this->sma->checkPermissions('customers_payments',true,'sales');
        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']        = $this->carsreports_model->getStaff();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['pos_settings'] = POS ? $this->carsreports_model->getPOSSetting('biller') : false;
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('Customers_Payments')]];
        $meta                       = ['page_title' => lang('Customers_Payments'), 'bc' => $bc];
		//$this->sma->print_arrays($this->data);

		$this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carssales/customers_payments_report', $meta, $this->data);
    }

    public function getPaymentsReport($pdf = null, $xls = null)
    {

      //  $this->sma->checkPermissions('payments', true,'sales');


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
                //->select('' . $this->db->dbprefix('payments') . '.date, ' . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as purchase_ref, paid_by, amount, type')
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . ".reference_no as sale_ref, paid_by, amount, {$this->db->dbprefix('payments')}.note, type, {$this->db->dbprefix('payments')}.id as id")
                ->from('payments')
                ->join('sales', 'payments.sale_id=sales.id', 'left')
                //->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id')
                ->order_by('sales.reference_no desc');

				$this->db->where('sales.reference_no !=', '');
				$this->db->where('payments.amount !=', 0);


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
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
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
                               $FillGreen = array(

                                 'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => '5cb85c')
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
                $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, lang('payment_reference'));
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('sale_reference'));
                //$this->excel->getActiveSheet()->SetCellValue('D1', lang('purchase_reference'));
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
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                    //$this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->purchase_ref);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->amount);
					$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->decode_html($note));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->type);
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
                $filename = 'payments_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                //->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, type, {$this->db->dbprefix('payments')}.id as id")
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . ".reference_no as sale_ref, paid_by, amount, {$this->db->dbprefix('payments')}.note, type, {$this->db->dbprefix('payments')}.id as id")
                ->from('payments')
                ->join('sales', 'payments.sale_id=sales.id', 'left')
                //->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id');
				//->order_by('payments.id');


		 $this->datatables->where('sales.reference_no !=', '');
		 $this->datatables->where('payments.amount !=', 0);


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
                $this->datatables->where('sales.biller_id', $biller);
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


    public function getCommercialDiscountsReport($pdf = null, $xls = null)
    {
      //  $this->sma->checkPermissions('payments', true,'sales');

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
                //->select('' . $this->db->dbprefix('payments') . '.date, ' . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as purchase_ref, paid_by, amount, type')
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . ".reference_no as sale_ref, commercial_discount, {$this->db->dbprefix('payments')}.note, type, {$this->db->dbprefix('payments')}.id as id")
                ->from('payments')
                ->join('sales', 'payments.sale_id=sales.id', 'left')
                //->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id')
                ->order_by('sales.reference_no desc');

				$this->db->where('sales.reference_no !=', '');
				$this->db->where('payments.commercial_discount !=', 0);


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
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
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

                $this->excel->getActiveSheet()->setTitle(lang('Discount_Allowance'));
                  $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Discount_Allowance'));
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
                               $FillGreen = array(

                                 'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => '5cb85c')
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
				$this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') .' '. lang('Discount_Allowance') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
				$this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A3')->applyFromArray($Font13Bold);

                        $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
                        $row++;
                $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, lang('payment_reference'));
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('sale_reference'));
                //$this->excel->getActiveSheet()->SetCellValue('D1', lang('purchase_reference'));
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, lang('Discount_Allowance'));
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
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->payment_ref);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                    //$this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->purchase_ref);
                    //$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->commercial_discount);
					$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->decode_html($note));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->type);
                    // $this->excel->getActiveSheet()->getStyle('F' . $row)->applyFromArray($FillGreen);
                   /* if ($data_row->type == 'returned' || $data_row->type == 'sent') {
                        $total -= $data_row->amount;
                    } else*/ {
                        $total += $data_row->commercial_discount;
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
                $filename = 'Discount_Allowance';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                //->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, type, {$this->db->dbprefix('payments')}.id as id")
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . ".reference_no as sale_ref, commercial_discount, {$this->db->dbprefix('payments')}.note, type, {$this->db->dbprefix('payments')}.id as id")
                ->from('payments')
                ->join('sales', 'payments.sale_id=sales.id', 'left')
                //->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id');
				//->order_by('payments.id');


		 $this->datatables->where('sales.reference_no !=', '');
		 $this->datatables->where('payments.commercial_discount !=', 0);


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
                $this->datatables->where('sales.biller_id', $biller);
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

    public function pdf($id = null, $view = null, $save_bufffer = null)
    {
        $this->sma->checkPermissions('pdf', true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->carssales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
        $this->data['units']       = $this->site->getAllUnits();
        $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->site->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['rows']        = $this->carssales_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->carssales_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->carssales_model->getAllInvoiceItems($inv->return_id) : null;
		$this->data['payments']        = $this->carssales_model->getInvoicePayments($inv->id);
		$this->data['return_payments'] = $inv->return_id ? $this->carssales_model->getInvoicePayments($inv->return_id) : null;
        //$this->data['paypal'] = $this->carssales_model->getPaypalSettings();
        //$this->data['skrill'] = $this->carssales_model->getSkrillSettings();

        $name = lang('sale') . '_' . str_replace('/', '_', $inv->reference_no) . '.pdf';
        $html = $this->load->view($this->theme . 'carssales/pdf', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }

        if ($view) {
            $this->load->view($this->theme . 'carssales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
        //    $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
            $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer, false,false,5);
        }
    }

    public function pdf_delivery($id = null, $view = null, $save_bufffer = null)
    {
        $this->sma->checkPermissions('pdf_delivery', true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $deli                = $this->carssales_model->getDeliveryByID($id);

        $this->data['delivery'] = $deli;
        $sale                   = $this->carssales_model->getInvoiceByID($deli->sale_id);
        $this->data['biller']   = $this->site->getCompanyByID($sale->biller_id);
        $this->data['rows']     = $this->carssales_model->getAllInvoiceItemsWithDetails($deli->sale_id);
        $this->data['user']     = $this->site->getUser($deli->created_by);

        $name = lang('delivery') . '_' . str_replace('/', '_', $deli->do_reference_no) . '.pdf';
        $html = $this->load->view($this->theme . 'carssales/pdf_delivery', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'carssales/pdf_delivery', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name);
        }
    }

    /* ------------------------------- */
	/*
	public function getClientInfo(){


		$otherdb = $this->load->database('info', true);
            //$clients = $otherdb->get('client')->row();
			 $otherdb->where('isAccounting',1);
			$q = $otherdb->get('client');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $clients[] = $row;
            }
		}







        foreach ($clients as $client) {


			$company = $client->company;
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = 'cpesoft_'.$company;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $company;
			$config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = 'sma_';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = FALSE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = '';
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = FALSE;

            $CI = &get_instance();
            $data = $CI->load->database($config, TRUE);
			$this->mydb =$data;
			//$this->accounts_model->getAccountItemByLevel();
			//$this->sma->print_arrays($this->getAccountItemByLevel($this->mydb));
			$this->getAccountItemByLevel($this->mydb);





        //$this->mydb->from('company_type');
        //$query = $this->mydb->get();
        //return $query->result();
		}


	}
	public function getAccountItemByLevel($db)
    {
        $data = [];
        $parent_key = '0';
        $level = 1;
          $q = $db->get('gl_accounts');
        if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {
				$data = $this->membersTreeByLevel($db,$parent_key,$level);
                //$data[] = $row;
            //}
            return $data;
        }
        return false;
    }


	public function membersTreeByLevel($db,$parent_key,$level)
    {
    $row1 = [];

		 $row = $db->get_where('gl_accounts', ['parent_id' => $parent_key])->result_array();


        //$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

        foreach($row as $key => $value)
        {
           $row = $db->update('gl_accounts', ['level'=>$level], ['id' => $value['id']]);

		   $id = $value['id'];
		   $accountID = $value['accountID'];

           $row1[$key]['id'] = $value['id'];
		   if ($this->Settings->user_language == 'arabic'){

           $row1[$key]['name'] =  $value['accountName'];
		   if($value['parent_id']!=0)
		   $row1[$key]['text'] =  "<input  type='hidden' class='rid' value='".$value['id'] . "'><span class='atname'>  ".$value['accountID'] . " - ". $value['accountName']. " </span> <span style='padding:20px;'><a href='" . admin_url('accounts/add_account/'.$id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_account') . "'><i class=\"fa fa-plus\"></i></a>  <a href='" . admin_url('accounts/edit_account/'.$accountID) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_account') . "'><i class=\"fa fa-edit\"></i></a>   <a href='#' onclick='window.location=\"". admin_url('accounts/delete_account/'.$id) ."\"'  class='tip' title='" . lang('Delete_account') . "'><i class=\"fa fa-trash-o\"></i></a></span>";
           else
		   $row1[$key]['text'] = "  ".$value['accountID'] . " - ". $value['accountName']. "   <span style='padding:20px;'><a href='" . admin_url('accounts/add_account/'.$id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_account') . "'><i class=\"fa fa-plus\"></i></a></span>";
           $row1[$key]['nodes'] = array_values($this->membersTreeByLevel($db,$value['id'],($level+1)));
		   }else{
			$row1[$key]['name'] =$value['accountNameEn'];
		   if($value['parent_id']!=0)
		   $row1[$key]['text'] =  "<input  type='hidden' class='rid' value='".$value['id'] . "'><span class='atname'>  ".$value['accountID'] . " - ". $value['accountNameEn']. "  <span style='padding:20px;'><a href='" . admin_url('accounts/add_account/'.$id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_account') . "'><i class=\"fa fa-plus\"></i></a>  <a href='" . admin_url('accounts/edit_account/'.$accountID) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_account') . "'><i class=\"fa fa-edit\"></i></a>   <a href='#' onclick='window.location=\"". admin_url('accounts/delete_account/'.$id) ."\"'  class='tip' title='" . lang('Delete_account') . "'><i class=\"fa fa-trash-o\"></i></a></span>";
           else
		   $row1[$key]['text'] =  "  ".$value['accountID'] . " - ". $value['accountNameEn']. "  <span style='padding:20px;'><a href='" . admin_url('accounts/add_account/'.$id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_account') . "'><i class=\"fa fa-plus\"></i></a></span>";
           $row1[$key]['nodes'] = array_values($this->membersTreeByLevel($db,$value['id'],($level+1)));


		   }
		}

        return $row1;
    }


	*/


    public function return_sale($id = null)
    {
        $this->sma->checkPermissions('return_sales',true,'sales');


        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $sale = $this->carssales_model->getInvoiceByID($id);
        if ($sale->return_id) {
            $this->session->set_flashdata('error', lang('sale_already_returned'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        //$this->sma->print_arrays($sale);

        $this->form_validation->set_rules('return_surcharge', lang('return_surcharge'), 'required');

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('re');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $return_surcharge = $this->input->post('return_surcharge') ? $this->input->post('return_surcharge') : 0;
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $note             = $this->sma->clear_tags($this->input->post('note'));
            $customer_details = $this->site->getCompanyByID($sale->customer_id);
            $biller_details   = $this->site->getCompanyByID($sale->biller_id);

            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $sale_item_id       = $_POST['sale_item_id'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' && $_POST['product_option'][$r] != 'null' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);


				$net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);
                $item_unit_quantity = (0 - $_POST['quantity'][$r]);
                $item_serial        = $_POST['serial'][$r]           ?$_POST['serial'][$r] : '';
                $item_tax_rate      = $_POST['product_tax'][$r]      ?$_POST['product_tax'][$r] : null;
                $item_discount      = $_POST['product_discount'][$r] ? $_POST['product_discount'][$r]: null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = (0 - $_POST['product_base_quantity'][$r]);

				//$this->sma->print_arrays($pr_discount);



                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;
                    // $unit_price = $real_unit_price;


					//$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                    if($product_details->tax_method != 1)
					$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
					else{
						$pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);
						//$unit_price=$_POST['real_unit_price'][$r];
					}


					/*
					$unit_price       = $this->sma->formatDecimal(($unit_price - $pr_discount), 12);
                    $item_net_price   = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity, 12);
                    $product_discount += $pr_item_discount;
					*/

					//$net_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);


					$item_net_price   = $net_price;


					$pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                    $product_discount += $pr_item_discount;


					/*
					$pr_item_tax = $item_tax = 0;
                    $tax         = '';


                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $unit_price - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax), 4);
                    $unit     = $item_unit ? $this->site->getUnitByID($item_unit) : false;

					*/
					$pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
						if($product_details->tax_method != 1)
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                        else{
						$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $unit_price=$real_unit_price;
						}
						$item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $net_price;
                        }else{
							//$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
							$item_net_price=$net_price;
							//$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        //$item_tax    = $ctax['amount'];
                        //$tax         = $ctax['tax'];



						}
						//
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            //$total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                    $unit     = $this->site->getUnitByID($item_unit);


                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($unit_price,12),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $item_unit,
                        'product_unit_code' => $unit ? $unit->code : null,
                        'unit_quantity'     => $item_unit_quantity,
                        'warehouse_id'      => $sale->warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $this->sma->formatDecimal($subtotal),
                        'serial_no'         => $item_serial,
                        'real_unit_price'   => $real_unit_price,
                        'sale_item_id'      => $sale_item_id,
                    ];


                    $si_return[] = [
                        'id'           => $sale_item_id,
                        'sale_id'      => $id,
                        'product_id'   => $item_id,
                        'option_id'    => $item_option,
                        'quantity'     => (0 - $item_quantity),
                        'warehouse_id' => $sale->warehouse_id,
                    ];

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 12);
					$orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);


                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }



			if($shipping!=0){
			$return_sale_ex_ref=$this->site->getReference('ex');


				//$this->sma->print_arrays($total_shipping);
				if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data_ex = [
                'date'         => $date,
                'reference'    => $return_sale_ex_ref,
                'amount'       => abs($shipping),
                'grand_total'       => abs($shipping),
                'total'       => abs($shipping),
				'payment_status'       => 'paid',
				'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $sale->warehouse_id,
			    'other' => 1,
			];
			if($this->purchases_model->addExpense($data_ex)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}
			}


            $order_discount = (0 - $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax)));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 12);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($return_surcharge) + (0 - $shipping) - $order_discount), 12);



			$data           = [
                'date'              => $date,
                'sale_id'           => $id,
                'reference_no'      => $sale->reference_no,
                'customer_id'       => $sale->customer_id,
                'customer'          => $sale->customer,
                'biller_id'         => $sale->biller_id,
                'biller'            => $sale->biller,
                'warehouse_id'      => $sale->warehouse_id,
                'note'              => $note,
                'total'             => $orginaltotal,
                'product_discount'  => $product_discount,
                'order_discount_id' => $this->input->post('discount') ? $this->input->post('order_discount') : null,
                'order_discount'    => $order_discount,
                'total_discount'    => $total_discount,
                'product_tax'       => $product_tax,
                'order_tax_id'      => $this->input->post('order_tax'),
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'surcharge'         => $this->sma->formatDecimal($return_surcharge),
                'grand_total'       => $grand_total,
                'created_by'        => $this->session->userdata('user_id'),
                'return_sale_ref'   => $reference,
                'shipping'          => $shipping,
                'sale_status'       => 'returned',
                'pos'               => $sale->pos,
                'payment_status'    => $sale->payment_status == 'paid' ? 'due' : 'pending',
				'currency_code'                => $sale->currency_code,
				'currency_rate'                => $sale->currency_rate,
				'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),


            ];
            if ($this->Settings->indian_gst) {
                //$data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($this->input->post('amount-paid') && $this->input->post('amount-paid') > 0) {
                $pay_ref = $this->input->post('payment_reference_no') ? $this->input->post('payment_reference_no') : $this->site->getReference('pay');
                $payment = [
                    'date'         => $date,
                    'reference_no' => $pay_ref,
                    'amount'       => (0 - $this->input->post('amount-paid')),
                    'paid_by'      => $this->input->post('paid_by'),
                    'cheque_no'    => $this->input->post('cheque_no'),
                    'cc_no'        => $this->input->post('pcc_no'),
                    'cc_holder'    => $this->input->post('pcc_holder'),
                    'cc_month'     => $this->input->post('pcc_month'),
                    'cc_year'      => $this->input->post('pcc_year'),
                    'cc_type'      => $this->input->post('pcc_type'),
                    'currency'         => $sale->currency_code,
					'currency_rate'         => $this->input->post('exchange_rate'),
					'created_by'   => $this->session->userdata('user_id'),
                    'type'         => 'returned',
                ];
                $data['payment_status'] = $grand_total == $this->input->post('amount-paid') ? 'paid' : 'partial';


			$amount=abs($payment['amount']);
			$amountPL=0;
			$amountDiff=0;


			$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
			if($payment['currency_rate'])
				$exchange_rate=$payment['currency_rate'];
			else
				$exchange_rate=$default_currencyRate;

			if($sale->currency_rate)
				$currencyRate=$sale->currency_rate;
			else
				$currencyRate=1;



				 //$this->sma->print_arrays($exchange_rate);




				//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

				$amount=abs($payment['amount']) * $currencyRate;

				//if ($currencyRate != $exchange_rate){
					$amountPL=abs($payment['amount']) * $exchange_rate;
					$amountDiff=$amountPL-$amount;
				//}
				//}


				if($amountDiff>0){
					$return_sale_ex_ref=$this->site->getReference('ex');


				//$this->sma->print_arrays($total_shipping);
			$data_ex = [
                'date'         => $date,
                'reference'    => $return_sale_ex_ref,
                'amount'       => abs($amountDiff),
                'grand_total'       => abs($amountDiff),
                'total'       => abs($amountDiff),
				'payment_status'       => 'paid',
				'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $sale->warehouse_id,
			    'other' => 1,
			];
			if($this->purchases_model->addExpense($data_ex)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}
				}
				if($amountDiff<0){
			$return_sale_re_ref=$this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay');
            $data_re = [
                'date'         => $date,
                'reference'    => $return_sale_re_ref,
                'amount'       => abs($amountDiff),
                'created_by'   => $this->session->userdata('user_id'),
                'warehouse_id' => $sale->warehouse_id,
			];
			if($this->products_model->addRevenue($data_re)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}

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
			if($this->input->post('paid_by')=="gift_card")
			$payment_type=$this->input->post('transfer_account');

			}

			$payment_type_arr[]     = [
                                'payment_type' => $payment_type,

                            ];

             //$this->sma->print_arrays( $payment);
             //$this->sma->print_arrays($data, $products, $payment, $si_return,$payment_type_arr);
        }

        if ($this->form_validation->run() == true && $this->carssales_model->addSale($data, $products, $payment, $si_return,$payment_type_arr)) {
            $this->session->set_flashdata('message', lang('return_sale_added'));
            admin_redirect($sale->pos ? 'pos/carssales' : 'carssales');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $sale;
            if ($this->data['inv']->sale_status != 'completed') {
                $this->session->set_flashdata('error', lang('sale_status_x_competed'));
                redirect($_SERVER['HTTP_REFERER']);
            }
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-' . $this->Settings->disable_editing . ' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang('sale_x_edited_older_than_x_days'), $this->Settings->disable_editing));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $inv_items = $this->carssales_model->getAllInvoiceItems($id);
            // krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->site->getProductByID($item->product_id);
                if (!$row) {
                    $row             = json_decode('{}');
                    $row->tax_method = 0;
                    $row->quantity   = 0;
                } else {
                    unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                }
                $pis = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if ($pis) {
                    $row->quantity = 0;
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                $row->id              = $item->product_id;
                $row->sale_item_id    = $item->id;
                $row->code            = $item->product_code;
                $row->name            = $item->product_name;
                $row->type            = $item->product_type;
                $row->unit            = $item->product_unit_id;
                $row->qty             = $item->unit_quantity;
                $row->oqty            = $item->unit_quantity;
                $row->discount        = $item->discount ? $item->discount : '0';
                $row->item_tax        = $item->item_tax      > 0 ? $item->item_tax      / $item->quantity : 0;
                $row->item_discount   = $item->item_discount > 0 ? $item->item_discount / $item->quantity : 0;
                $row->price           = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->unit_quantity));
                $row->unit_price      = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->unit_quantity) - $this->sma->formatDecimal($item->item_tax / $item->unit_quantity) : $item->unit_price + ($item->item_discount / $item->unit_quantity);
                $row->real_unit_price = $item->real_unit_price;
                $row->base_quantity   = $item->quantity;
                $row->base_unit       = isset($row->unit) ? $row->unit : $item->product_unit_id;
                $row->base_unit_price = isset($row->unit_price) ? $row->unit_price : $item->unit_price;
                $row->tax_rate        = $item->tax_rate_id;
                $row->serial          = $item->serial_no;
                $row->option          = $item->option_id;
                $options              = $this->carssales_model->getProductOptions($row->id, $item->warehouse_id, true);
                $units                = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate             = $this->site->getTaxRateByID($row->tax_rate);
                $ri                   = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'row' => $row, 'units' => $units, 'tax_rate' => $tax_rate, 'options' => $options];
                $c++;
            }
			if ($this->Settings->accounting){
			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
		    $this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
            $this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
            $this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();

            	//$this->sma->print_arrays($this->data['payment_types']);

			}
            $this->data['inv_items']   = json_encode($pr);
            $this->data['id']          = $id;
            $this->data['payment_ref'] = '';
            $this->data['reference']   = ''; // $this->site->getReference('re');
            $this->data['currencies'] = $this->site->getAllCurrencies();
			$this->data['tax_rates']   = $this->site->getAllTaxRates();
            $bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('carssales')], ['link' => '#', 'page' => lang('return_sale')]];
            $meta                      = ['page_title' => lang('return_sale'), 'bc' => $bc];
            $this->page_construct('carssales/return_sale', $meta, $this->data);
        }
    }

    public function sale_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }


        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete',true,'sales');

                    foreach ($_POST['val'] as $id) {
                        $this->carssales_model->deleteSale($id);
                    }
                    $this->session->set_flashdata('message', lang('sales_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'combine') {
                    $html = $this->combine_pdf($_POST['val']);
                } elseif ($this->input->post('form_action') == 'export_excel'|| $xls || $pdf) {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                     if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }
                        $this->excel->getActiveSheet()->setTitle(lang('sales'));
                        $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('sales'));
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

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                        $row++;

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('biller'));
                    $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('sale_status'));
                    $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('grand_total'));
                    $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('paid'));
                    $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('balance'));
                    $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('payment_status'));

                     $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($fillColor);
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($Font11Bold);
                        $row++;

                    foreach ($_POST['val'] as $id) {
                        $sale = $this->carssales_model->getInvoiceByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($sale->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sale->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sale->biller);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sale->customer);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sale->sale_status);
                        if($sale->sale_status=='completed'){
                        $this->excel->getActiveSheet()->getStyle('E'.$row)->applyFromArray($styleArrayFillGreen);
                        }else if($sale->sale_status=='pending'){
                        $this->excel->getActiveSheet()->getStyle('E'.$row)->applyFromArray($styleArrayFillYellow);
                        }else if($sale->sale_status=='returned'){
                        $this->excel->getActiveSheet()->getStyle('E'.$row)->applyFromArray($styleArrayFillRed);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sale->grand_total);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale->paid);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($sale->grand_total)-($sale->paid));
                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $sale->payment_status);
                        if($sale->payment_status=='paid'){
                        $this->excel->getActiveSheet()->getStyle('I'.$row)->applyFromArray($styleArrayFillGreen);
                        }else if($sale->payment_status=='pending'){
                        $this->excel->getActiveSheet()->getStyle('I'.$row)->applyFromArray($styleArrayFillYellow);
                        }else if($sale->payment_status=='due'){
                        $this->excel->getActiveSheet()->getStyle('I'.$row)->applyFromArray($styleArrayFillRed);
                        }else if($sale->payment_status=='partial'){
                        $this->excel->getActiveSheet()->getStyle('I'.$row)->applyFromArray($styleArrayFillBlu);
                        }
                        $row++;
                    }

                     for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                        }


                   // $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                   // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'sales_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_sale_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /* -------------------------------------------------------------------------------------- */

    public function sale_by_csv()
    {
        $this->sma->checkPermissions('csv',true,'sales');

        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');
        $this->form_validation->set_message('is_natural_no_zero', lang('no_zero_required'));
        $this->form_validation->set_rules('customer', lang('customer'), 'required');
        $this->form_validation->set_rules('biller', lang('biller'), 'required');
        $this->form_validation->set_rules('sale_status', lang('sale_status'), 'required');
        $this->form_validation->set_rules('payment_status', lang('payment_status'), 'required');

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $total_items      = $this->input->post('total_items');
            $sale_status      = $this->input->post('sale_status');
            $payment_status   = $this->input->post('payment_status');
            $payment_term     = $this->input->post('payment_term');
            $due_date         = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days')) : null;
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = $customer_details->company && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = $biller_details->company && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note             = $this->sma->clear_tags($this->input->post('note'));
            $staff_note       = $this->sma->clear_tags($this->input->post('staff_note'));

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
                    admin_redirect('carssales/sale_by_csv');
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

                $keys  = ['code', 'net_unit_price', 'quantity', 'variant', 'item_tax_rate', 'discount', 'serial'];
                $final = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if (isset($csv_pr['code']) && isset($csv_pr['net_unit_price']) && isset($csv_pr['quantity'])) {
                        if ($product_details = $this->cars_model->getVehiclesByChesis($csv_pr['code'])) {
                            if ($csv_pr['variant']) {
                                $item_option = $this->carssales_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
                                if (!$item_option) {
                                    $this->session->set_flashdata('error', lang('pr_not_found') . ' ( ' . $product_details->name . ' - ' . $csv_pr['variant'] . ' ). ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            } else {
                                $item_option     = json_decode('{}');
                                $item_option->id = null;
                            }

                            $item_id        = $product_details->id;
                            $item_type      = $product_details->type;
                            $item_code      = $product_details->code;
                            $item_name      = $product_details->name;
                            $item_net_price = $this->sma->formatDecimal($csv_pr['net_unit_price']);
                            $item_quantity  = $csv_pr['quantity'];
                            $item_tax_rate  = $csv_pr['item_tax_rate'];
                            $item_discount  = $csv_pr['discount'];
                            $item_serial    = $csv_pr['serial'];

                            if (isset($item_code) && isset($item_net_price) && isset($item_quantity)) {
                                $product_details  = $this->cars_model->getVehiclesByChesis($item_code);
                                $pr_discount      = $this->site->calculateDiscount($item_discount, $item_net_price);
                                $item_net_price   = $this->sma->formatDecimal(($item_net_price - $pr_discount), 4);
                                $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_quantity), 4);
                                $product_discount += $pr_item_discount;

                                $tax         = '';
                                $pr_item_tax = 0;
                                $unit_price  = $item_net_price;
                                $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->carssales_model->getTaxRateByName($item_tax_rate) : $this->site->getTaxRateByID($product_details->tax_rate));
                                if ($tax_details) {
                                    $ctax     = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                                    $item_tax = $ctax['amount'];
                                    $tax      = $ctax['tax'];
                                    if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                                        $item_net_price = $unit_price - $item_tax;
                                    }
                                    $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity, 4);
                                    if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                                       // $total_cgst += $gst_data['cgst'];
                                        $total_sgst += $gst_data['sgst'];
                                        $total_igst += $gst_data['igst'];
                                    }
                                }

                                $product_tax += $pr_item_tax;
                                $subtotal = $this->sma->formatDecimal((($item_net_price * $item_quantity) + $pr_item_tax), 4);
                                $unit     = $this->site->getUnitByID($product_details->unit);

                                $product = [
                                    'product_id'        => $product_details->id,
                                    'product_code'      => $item_code,
                                    'product_name'      => $item_name,
                                    'product_type'      => $item_type,
                                    'option_id'         => $item_option->id,
                                    'net_unit_price'    => $item_net_price,
                                    'quantity'          => $item_quantity,
                                    'product_unit_id'   => $product_details->unit,
                                    'product_unit_code' => $unit->code,
                                    'unit_quantity'     => $item_quantity,
                                    'warehouse_id'      => $warehouse_id,
                                    'item_tax'          => $pr_item_tax,
                                    'tax_rate_id'       => $tax_details ? $tax_details->id : null,
                                    'tax'               => $tax,
                                    'discount'          => $item_discount,
                                    'item_discount'     => $pr_item_discount,
                                    'subtotal'          => $subtotal,
                                    'serial_no'         => $item_serial,
                                    'unit_price'        => $this->sma->formatDecimal(($item_net_price + $item_tax), 4),
                                    'real_unit_price'   => $this->sma->formatDecimal(($item_net_price + $item_tax + $pr_discount), 4),
                                ];

                                $products[] = ($product + $gst_data);
                                $total += $this->sma->formatDecimal(($item_net_price * $item_quantity), 4);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('pr_not_found') . ' ( ' . $csv_pr['code'] . ' ). ' . lang('line_no') . ' ' . $rw);
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                        $rw++;
                    }
                }
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            $data           = ['date' => $date,
                'reference_no'        => $reference,
                'customer_id'         => $customer_id,
                'customer'            => $customer,
                'biller_id'           => $biller_id,
                'biller'              => $biller,
                'warehouse_id'        => $warehouse_id,
                'note'                => $note,
                'staff_note'          => $staff_note,
                'total'               => $total,
                'product_discount'    => $product_discount,
                'order_discount_id'   => $this->input->post('order_discount'),
                'order_discount'      => $order_discount,
                'total_discount'      => $total_discount,
                'product_tax'         => $product_tax,
                'order_tax_id'        => $this->input->post('order_tax'),
                'order_tax'           => $order_tax,
                'total_tax'           => $total_tax,
                'shipping'            => $this->sma->formatDecimal($shipping),
                'grand_total'         => $grand_total,
                'total_items'         => $total_items,
                'sale_status'         => $sale_status,
                'payment_status'      => $payment_status,
                'payment_term'        => $payment_term,
                'due_date'            => $due_date,
                'paid'                => 0,
                'created_by'          => $this->session->userdata('user_id'),
            ];
            if ($this->Settings->indian_gst) {
               // $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($payment_status == 'paid') {
                $payment = [
                    'date'         => $date,
                    'reference_no' => $this->site->getReference('pay'),
                    'amount'       => $grand_total,
                    'paid_by'      => 'cash',
                    'cheque_no'    => '',
                    'cc_no'        => '',
                    'cc_holder'    => '',
                    'cc_month'     => '',
                    'cc_year'      => '',
                    'cc_type'      => '',
                    'created_by'   => $this->session->userdata('user_id'),
                    'note'         => lang('auto_added_for_sale_by_csv') . ' (' . lang('sale_reference_no') . ' ' . $reference . ')',
                    'type'         => 'received',
                ];
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


        }

        if ($this->form_validation->run() == true && $this->carssales_model->addSale($data, $products, $payment)) {
            $this->session->set_userdata('remove_slls', 1);
            $this->session->set_flashdata('message', lang('sale_added'));
            admin_redirect('carssales');
        } else {
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['billers']    = $this->site->getAllCompanies('biller');
            $this->data['slnumber']   = $this->site->getReference('so');

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('add_sale_by_csv')]];
            $meta = ['page_title' => lang('add_sale_by_csv'), 'bc' => $bc];
            $this->page_construct('carssales/sale_by_csv', $meta, $this->data);
        }
    }

    public function sell_gift_card()
    {
        $this->sma->checkPermissions('gift_cards',true,'sales');

        $error  = null;
        $gcData = $this->input->get('gcdata');
        if (empty($gcData[0])) {
            $error = lang('value') . ' ' . lang('is_required');
        }
        if (empty($gcData[1])) {
            $error = lang('card_no') . ' ' . lang('is_required');
        }

        $customer_details = (!empty($gcData[2])) ? $this->site->getCompanyByID($gcData[2]) : null;
        $customer         = $customer_details ? $customer_details->company : null;
        $data             = ['card_no' => $gcData[0],
            'value'                    => $gcData[1],
            'customer_id'              => (!empty($gcData[2])) ? $gcData[2] : null,
            'customer'                 => $customer,
            'balance'                  => $gcData[1],
            'expiry'                   => (!empty($gcData[3])) ? $this->sma->fsd($gcData[3]) : null,
            'created_by'               => $this->session->userdata('user_id'),
        ];

        if (!$error) {
            if ($this->carssales_model->addGiftCard($data)) {
                $this->sma->send_json(['result' => 'success', 'message' => lang('gift_card_added')]);
            }
        } else {
            $this->sma->send_json(['result' => 'failed', 'message' => $error]);
        }
    }

    /* --------------------------------------------------------------------------------------------- */
	public function suggestionsx($pos = 0)
    {
//$rows           = $this->carssales_model->getProductNamesx('1', '1');
$rows = array('image_path' => 'aaa', 'image_path' => 'bbb');


		$this->sma->print_arrays($rows);
	}

    public function suggestions($pos = 0)
    {
        $term         = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $customer_id  = $this->input->get('customer_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        //$this->sma->print_arrays($rows);
        $warehouse      = $this->cars_model->getShowroomByID($warehouse_id);//$this->site->getWarehouseByID($warehouse_id);
        $customer       = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
		$unit_first=null;
		$price_first=null;
		$code_first=null;

		if ($pr_group_price_first = $this->site->getProductGroupPriceByCode($sr, $warehouse->price_group_id,$row->unit)){
			$sr=$this->site->getProductByID($pr_group_price_first->product_id)->code;
			$unit_first=$pr_group_price_first->product_unit_id;
		    $price_first=$pr_group_price_first->price;
		    $code_first=$pr_group_price_first->product_code;
		}

        $rows           = $this->carssales_model->getProductNames($sr, $warehouse_id, $pos);
		//$this->sma->print_arrays($rows);
        if ($rows) {
            $r = 0;
            foreach ($rows as $row) {
                $c = uniqid(mt_rand(), true);
                //unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $option               = false;
                $row->quantity        = 0;
                $row->item_tax_method = $row->tax_method;
                $row->qty             = 1;
				$this->load->admin_model('promo_model');
                /* if($promosdis = $this->promo_model->getPromosByProductDiscount($row->id, $warehouse_id)){
				$row->discount        = $promosdis->percentage_off;
				}else*/{
				$row->discount        = '0';
				}
                $row->serial          = '';
                $options              =  $this->carssales_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->carssales_model->getProductOptionByID($option_id) : $options[0];
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt        = json_decode('{}');
                    $opt->price = 0;
                    $option_id  = false;
                }
                $row->option = $option_id;
                $pis         = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                if ($pis) {
                    $row->quantity = 0;
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }
                //if ($this->sma->isPromo($row)) {

				if ($this->sma->isPromo($row)) {

                    $row->price = $row->promo_price;
                } elseif ($customer->price_group_id) {
                    if ($pr_group_price = $this->site->getProductGroupPriceWithUnit($row->id, $customer->price_group_id,$row->unit)) {
                        $row->price = $pr_group_price->price;
                    }
                } elseif ($warehouse->price_group_id) {
                    if ($pr_group_price = $this->site->getProductGroupPriceWithUnit($row->id, $warehouse->price_group_id,$row->unit)) {
                        $row->price = $pr_group_price->price;
                    }
                }

				if($price_first)
					$row->price       = $price_first;
                    if($row->type=='service'){
					    $row->buying_price       = $row->price;
                    }else{
                        $row->type='standard';
                        $row->code       = $row->chesis_no;

                    }
                $row->price           = $row->buying_price + (($row->buying_price * $customer_group->percent) / 100);
                $row->real_unit_price = $row->buying_price;
                $row->base_quantity   = 1;
                $row->base_unit       = 1;
                $row->base_unit_price = $row->buying_price;
                if($unit_first)
					$row->unit       = $unit_first;
				else
				$row->unit            = 1;
                //if($row->chesis_no)

                $row->comment         = '';
                $combo_items          = false;
               /* if ($row->type == 'combo') {
                    $combo_items = $this->carssales_model->getProductComboItems($row->id, $warehouse_id);
                }*/
                $units    = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

                $pr[] = ['id' => sha1($c . $r), 'item_id' => $row->id,'code_first' => $code_first,'unit_first' => $unit_first,'unit_id' => $row->unit, 'label' => $row->name . ' (' . $row->chesis_no . ')', 'category' => $row->category_id,
                    'row'     => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('No_match_found'), 'value' => $term]]);
        }
    }

    public function topup_gift_card($card_id)
    {
        $this->sma->checkPermissions('charge_gift_card',true,'sales');

        $card = $this->site->getGiftCardByID($card_id);
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|integer|required');

        if ($this->form_validation->run() == true) {
            $data = ['card_id' => $card_id,
                'amount'       => $this->input->post('amount'),
                'date'         => date('Y-m-d H:i:s'),
                'created_by'   => $this->session->userdata('user_id'),
            ];
            $card_data['balance'] = ($this->input->post('amount') + $card->balance);
            // $card_data['value'] = ($this->input->post('amount')+$card->value);
            if ($this->input->post('expiry')) {
                $card_data['expiry'] = $this->sma->fld(trim($this->input->post('expiry')));
            }
        } elseif ($this->input->post('topup')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('carssales/gift_cards');
        }

        if ($this->form_validation->run() == true && $this->carssales_model->topupGiftCard($data, $card_data)) {
            $this->session->set_flashdata('message', lang('topup_added'));
            admin_redirect('carssales/gift_cards');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['card']       = $card;
            $this->data['page_title'] = lang('topup_gift_card');
            $this->load->view($this->theme . 'carssales/topup_gift_card', $this->data);
        }
    }

    public function update_status($id)
    {
        $this->form_validation->set_rules('status', lang('sale_status'), 'required');

        if ($this->form_validation->run() == true) {
            $status = $this->input->post('status');
            $note   = $this->sma->clear_tags($this->input->post('note'));
        } elseif ($this->input->post('update')) {
            $this->session->set_flashdata('error', validation_errors());
             admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'carssales');
        }

        if ($this->form_validation->run() == true && $this->carssales_model->updateStatus($id, $status, $note)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'carssales');
        } else {
            $this->data['inv']      = $this->carssales_model->getInvoiceByID($id);
            $this->data['returned'] = false;
            if ($this->data['inv']->sale_status == 'returned' || $this->data['inv']->return_id) {
                $this->data['returned'] = true;
            }
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'carssales/update_status', $this->data);
        }
    }

    public function validate_gift_card($no)
    {
        //$this->sma->checkPermissions();
        if ($gc = $this->site->getGiftCardByNO($no)) {
            if ($gc->expiry) {
                if ($gc->expiry >= date('Y-m-d')) {
                    $this->sma->send_json($gc);
                } else {
                    $this->sma->send_json(false);
                }
            } else {
                $this->sma->send_json($gc);
            }
        } else {
            $this->sma->send_json(false);
        }
    }

    public function view($id = null)
    {
        $this->sma->checkPermissions('index',true,'sales');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->carssales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
      //  $this->data['units']       = $this->site->getAllUnits();
        $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->site->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['warehouse']   = $this->cars_model->getShowroomByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['rows']        = $this->carssales_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->carssales_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->carssales_model->getAllInvoiceItems($inv->return_id) : null;
		$this->data['payments']        = $this->carssales_model->getInvoicePayments($inv->id);
		$this->data['active_tax']        =$this->carssales_model->getTaxRatesActive()->rate;
		$this->data['return_payments'] = $inv->return_id ? $this->carssales_model->getInvoicePayments($inv->return_id) : null;

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('view')]];
        $meta = ['page_title' => lang('view_sales_details'), 'bc' => $bc];
        $this->page_construct('carssales/view', $meta, $this->data);

    }

    public function view_delivery($id = null)
    {
        $this->sma->checkPermissions('deliveries',true,'sales');

        // $this->sma->checkPermissions('details_delivery');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $deli                = $this->carssales_model->getDeliveryByID($id);
        $sale                = $this->carssales_model->getInvoiceByID($deli->sale_id);
        if (!$sale) {
            $this->session->set_flashdata('error', lang('sale_not_found'));
            $this->sma->md();
        }
        $this->data['delivery']   = $deli;
        $this->data['biller']     = $this->site->getCompanyByID($sale->biller_id);
        $this->data['rows']       = $this->carssales_model->getAllInvoiceItemsWithDetails($deli->sale_id);

		$inv                 = $this->carssales_model->getInvoiceByID($deli->sale_id);

		$this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);


		//$this->sma->print_arrays($deli);


        $this->data['user']       = $this->site->getUser($deli->created_by);
        $this->data['page_title'] = lang('delivery_order');

        $this->load->view($this->theme . 'carssales/view_delivery', $this->data);
    }

    public function view_gift_card($id = null)
    {
        $this->data['page_title'] = lang('gift_card');
        $gift_card                = $this->site->getGiftCardByID($id);
        $this->data['gift_card']  = $this->site->getGiftCardByID($id);
        $this->data['customer']   = $this->site->getCompanyByID($gift_card->customer_id);
        $this->data['topups']     = $this->carssales_model->getAllGCTopups($id);
        $this->load->view($this->theme . 'carssales/view_gift_card', $this->data);
    }


/*-----------------------------Quotes----------------------------*/
    public function quotes_index($warehouse_id = null)
    {

        //$this->sma->checkPermissions('quotes-index');
        $this->sma->checkPermissions('index',true,'quotes');

		if(!$this->site->IsPermissions('quotes-index')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->cars_model->getShowroomByID($warehouse_id) : null;
        } else {
            $this->data['warehouses']   = null;

            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->cars_model->getShowroomByID($this->session->userdata('warehouse_id')) : null;
        }
        //$this->sma->print_arrays($this->data);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Reserved_vehicle')]];
        $meta = ['page_title' => lang('Reserved_vehicle'), 'bc' => $bc];
        $this->page_construct('carsquotes/index', $meta, $this->data);
		}
    }

    public function getQuotes($warehouse_id = null)
    {
      //  $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            //$warehouse_id = $user->warehouse_id;
        }
        $detail_link  = anchor('admin/carssales/view_quote/$1', '<i class="fa fa-file-text-o"></i> ' . lang('Reserve_details'));
        $email_link   = anchor('admin/carssales/email_quote/$1', '<i class="fa fa-envelope"></i> ' . lang('Email_reserve'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link    = anchor('admin/carssales/edit_quote/$1', '<i class="fa fa-edit"></i> ' . lang('Edit_reserve'));
        $convert_link = anchor('admin/carssales/add/$1', '<i class="fa fa-heart"></i> ' . lang('create_sale'));
        $payments_link     = anchor('admin/carsquotes/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link  = anchor('admin/carsquotes/add_payment/$1', '<i class="fa fa-dollar"></i> ' . lang('Add_advance_payment'), 'data-toggle="modal" data-target="#myModal"');
       // $pc_link      = anchor('admin/purchases/add/$1', '<i class="fa fa-star"></i> ' . lang('create_purchase'));
        //$pdf_link     = anchor('admin/carssales/pdf_quote/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $delete_link  = "<a href='#' class='po' title='<b>" . $this->lang->line('Delete_reserve') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('carssales/delete_quote/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('Delete_reserve') . '</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>' . $detail_link . '</li>
                        <li>' . $edit_link . '</li>
                        <li>' . $convert_link . '</li>
                        <li>' . $pc_link . '</li>
                        <li>' . $payments_link . '</li>
                       <li>' . $add_payment_link . '</li>
                        <li>' . $email_link . '</li>
                        <li>' . $delete_link . '</li>
                    </ul>
                </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

        $this->load->library('datatables');
        /*if ($warehouse_id) {
            $this->datatables
                ->select('id, date, reference_no, biller, customer, supplier, grand_total, status, attachment')
                ->from('quotes')
                ->where('warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select('id, date, reference_no, biller, customer, supplier, grand_total, status, attachment')
                ->from('quotes');
        }*/

        $qp = "( SELECT quote_id, SUM(amount) as amount from {$this->db->dbprefix('payments')}  WHERE quote_id is not null group by quote_id ) QuotesPayments";


        if ($warehouse_id) {
            $this->datatables
                ->select('id, date, reference_no, biller, customer, COALESCE( QuotesPayments.amount, 0 ) as amount, grand_total, status, attachment')
                ->from('quotes')
                ->where('warehouse_id', $warehouse_id)
                ->join($qp, 'quotes.id = QuotesPayments.quote_id', 'left');

        } else {
            $this->datatables
                ->select('id, date, reference_no, biller, customer, COALESCE( QuotesPayments.amount, 0 ) as amount, grand_total, status, attachment')
                ->from('quotes')
                ->join($qp, 'quotes.id = QuotesPayments.quote_id', 'left');

        }

        if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Customer) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }
        $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }
    public function quote_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete',true,'quotes');

                    foreach ($_POST['val'] as $id) {
                        $this->carsquotes_model->deleteQuote($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('Reserve_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'combine') {
         //           $html = $this->combine_pdf($_POST['val']);
                } elseif ($this->input->post('form_action') == 'export_excel' || $pdf) {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }
                        $this->excel->getActiveSheet()->setTitle(lang('Reserved_vehicle'));
                        $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':H'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Reserved_vehicle'));
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
                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                          //  $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
                          //  $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);



                        $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':H'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                        $row++;

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('date'));
                        $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                        $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('biller'));
                        $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('customer'));
                      //  $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('supplier'));
                       $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('Advance_payment'));
                        $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('total'));
                        $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('status'));


                        $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->applyFromArray($fillColor);
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->applyFromArray($Font11Bold);
                        $row++;
                        foreach ($_POST['val'] as $id) {
                            $qu = $this->carsquotes_model->getQuoteByID($id);
                            $pay = $this->carsquotes_model->getTotalQuotesPayments($id);


                            //$this->sma->print_arrays($pay);
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($qu->date));
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $qu->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $qu->biller);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $qu->customer);
                          //  $this->excel->getActiveSheet()->SetCellValue('E' . $row, $qu->supplier);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pay->amount);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $qu->grand_total);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $qu->status);
                            $row++;
                        }

                        for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                        }

                      //  $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                       // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $filename = 'Reserved_vehicle_' . date('Y_m_d_H_i_s');
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('No_reserve_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function add_quote()
    {
        $this->sma->checkPermissions('add',true,'quotes');
		if(!$this->site->IsPermissions('quotes-add')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{


        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('customer', $this->lang->line('customer'), 'required');

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qu');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $supplier_id      = $this->input->post('supplier');
            $status           = $this->input->post('status');
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = $customer_details->company && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = $biller_details->company && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            if ($supplier_id) {
                $supplier_details = $this->site->getCompanyByID($supplier_id);
                $supplier         = $supplier_details->company && $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            } else {
                $supplier = null;
            }
            //$note = $this->sma->clear_tags($this->input->post('note'));
			$note = $this->sma->clear_tags($_POST['note']);

            $gtotal            = 0;
            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;

			if($this->input->post('discount')>0){
				for ($r = 0; $r < $i; $r++) {
					 $item_code          = $_POST['product_code'][$r];
					 $item_type          = $_POST['product_type'][$r];
                $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;

				if($product_details->tax_method != 1)
					$gtotal=$gtotal+($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r]);
				else
					$gtotal=$gtotal+($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r]);

				}
			}
            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);
                $net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_tax_rate      = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount      = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = $_POST['product_base_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {



					$product_details = $item_type != 'manual' ? $this->carsquotes_model->getProductByCode($item_code) : null;


					if($gtotal>0){
						$order_product_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($gtotal));
                      if($product_details->tax_method != 1)
						$item_discount=$this->sma->formatDecimal((($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					else
						$item_discount=$this->sma->formatDecimal((($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					}

					// $unit_price = $real_unit_price;
                    //$this->sma->print_arrays($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r]);
					//$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                    if($product_details->tax_method != 1)
					$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
					else
						$pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);

                    //$this->sma->print_arrays($pr_discount);



					$unit_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);

					$item_net_price   = $net_price;


					$pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
						if($product_details->tax_method != 1)
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                        else
						$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $net_price;
                        }else{
							//$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
							$item_net_price=$net_price;
							//$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        //$item_tax    = $ctax['amount'];
                        //$tax         = $ctax['tax'];



						}

						if($product_details->tax_method != 1)
                        $unit_price       = $this->sma->formatDecimal($real_unit_price - $item_tax,12);

						//
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                           // $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                    $unit     = $this->site->getUnitByID($item_unit);

                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($unit_price,12),
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
                        'subtotal'          => $this->sma->formatDecimal($subtotal),
                        'real_unit_price'   => $real_unit_price,
                    ];

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 12);
                    $orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
			if($gtotal>0)
			$order_discount=0;
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            if($gtotal>0)
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping)), 2);
            else
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 2);
            $data           = ['date' => $date,
                'reference_no'        => $reference,
                'customer_id'         => $customer_id,
                'customer'            => $customer,
                'biller_id'           => $biller_id,
                'biller'              => $biller,
                'supplier_id'         => $supplier_id,
                'supplier'            => $supplier,
                'warehouse_id'        => $warehouse_id,
                'note'                => $note,
                'total'               => $orginaltotal,
                'product_discount'    => $product_discount,
                'order_discount_id'   => $this->input->post('discount'),
                'order_discount'      => $order_discount,
                'total_discount'      => $total_discount,
                'product_tax'         => $product_tax,
                'order_tax_id'        => $this->input->post('order_tax'),
                'order_tax'           => $order_tax,
                'total_tax'           => $total_tax,
                'shipping'            => $this->sma->formatDecimal($shipping),
                'grand_total'         => $grand_total,
                'status'              => $status,
                'created_by'          => $this->session->userdata('user_id'),
                'hash'                => hash('sha256', microtime() . mt_rand()),
            ];
            if ($this->Settings->indian_gst) {
               // $data['cgst'] = $total_cgst;
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

             //$this->sma->print_arrays($data, $products,$_POST);
        }

        if ($this->form_validation->run() == true && $this->carsquotes_model->addQuote($data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line('quote_added'));
            admin_redirect('carssales/quotes_index');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->site->getAllCompanies('biller') : null;
            //$this->data['currencies'] = $this->site->getAllCurrencies();
            $this->data['units']      = $this->site->getAllBaseUnits();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();

            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->cars_model->getAllShowrooms() : null;
            $this->data['qunumber']   = ''; //$this->site->getReference('qu');
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('Reserved_vehicle')], ['link' => '#', 'page' => lang('Add_reserve')]];
            $meta                     = ['page_title' => lang('Add_reserve'), 'bc' => $bc];
            $this->page_construct('carsquotes/add', $meta, $this->data);
        }
		}
    }
    public function view_quote($quote_id = null)
    {
        $this->sma->checkPermissions('index',true,'quotes');

        if ($this->input->get('id')) {
            $quote_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->carsquotes_model->getQuoteByID($quote_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['rows']       = $this->carsquotes_model->getAllQuoteItems($quote_id);
        $this->data['customer']   = $this->site->getCompanyByID($inv->customer_id);
        $this->data['biller']     = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['warehouse']  = $this->cars_model->getShowroomByID($inv->warehouse_id);
        $this->data['inv']        = $inv;

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('Reserved_vehicle')], ['link' => '#', 'page' => lang('view')]];
        $meta = ['page_title' => lang('Reserve_details'), 'bc' => $bc];
        $this->page_construct('carsquotes/view', $meta, $this->data);
    }
    public function email_quote($quote_id = null)
    {
        $this->sma->checkPermissions('email',true,'quotes');

        if ($this->input->get('id')) {
            $quote_id = $this->input->get('id');
        }
        $inv = $this->carsquotes_model->getQuoteByID($quote_id);
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
            $customer = $this->site->getCompanyByID($inv->customer_id);
            $biller   = $this->site->getCompanyByID($inv->biller_id);
            $this->load->library('parser');
            $parse_data = [
                'reference_number' => $inv->reference_no,
                'contact_person'   => $customer->name,
                'company'          => $customer->company,
                'site_link'        => base_url(),
                'site_name'        => $this->Settings->site_name,
                'logo'             => '<img src="' . base_url() . 'assets/uploads/logos/' . $biller->logo . '" alt="' . ($biller->company && $biller->company != '-' ? $biller->company : $biller->name) . '"/>',
            ];
            $msg        = $this->input->post('note');
            $message    = $this->parser->parse_string($msg, $parse_data);
            $attachment = $this->pdf($quote_id, null, 'S');

            try {
                if ($this->sma->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                    delete_files($attachment);
                    $this->db->update('quotes', ['status' => 'sent'], ['id' => $quote_id]);
                    $this->session->set_flashdata('message', $this->lang->line('email_sent'));
                    admin_redirect('carssales/quotes_index');
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

            if (file_exists('./themes/' . $this->Settings->theme . '/admin/views/email_templates/quote.html')) {
                $quote_temp = file_get_contents('themes/' . $this->Settings->theme . '/admin/views/email_templates/quote.html');
            } else {
                $quote_temp = file_get_contents('./themes/default/admin/views/email_templates/quote.html');
            }

            $this->data['subject'] = ['name' => 'subject',
                'id'                         => 'subject',
                'type'                       => 'text',
                'value'                      => $this->form_validation->set_value('subject', lang('Reserved_vehicle') . ' (' . $inv->reference_no . ') ' . lang('from') . ' ' . $this->Settings->site_name),
            ];
            $this->data['note'] = ['name' => 'note',
                'id'                      => 'note',
                'type'                    => 'text',
                'value'                   => $this->form_validation->set_value('note', $quote_temp),
            ];
            $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);

            $this->data['id']       = $quote_id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'carsquotes/email', $this->data);
        }
    }
    public function edit_quote($id = null)
    {

        $this->sma->checkPermissions('edit',true,'quotes');
		if(!$this->site->IsPermissions('quotes-edit')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{


        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->carsquotes_model->getQuoteByID($id);
        if ($inv->status == 'completed' || $inv->status == 'canceled') {
            $this->session->set_flashdata('error', lang('Reserve_denied'));
                        redirect($_SERVER['HTTP_REFERER']);
        }

        if (!$this->session->userdata('edit_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('reference_no', $this->lang->line('reference_no'), 'required');
        $this->form_validation->set_rules('customer', $this->lang->line('customer'), 'required');
        //$this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');

        if ($this->form_validation->run() == true) {


            $reference = $this->input->post('reference_no');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $supplier_id      = $this->input->post('supplier');
            $status           = $this->input->post('status');
            $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = $customer_details->company && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = $biller_details->company && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            if ($supplier_id) {
                $supplier_details = $this->site->getCompanyByID($supplier_id);
                $supplier         = $supplier_details->company && $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
            } else {
                $supplier = null;
            }
            //$this->sma->print_arrays($this->input->post('note'));
			$note = $this->sma->clear_tags($_POST['note']);
			//$note = ($this->input->post('note'));
			//$this->sma->print_arrays(str_replace("cpesoft_","",$this->db->database));
			//$note =str_replace("%", "<strong>%</strong>", $this->input->post('note'));
			//$this->sma->print_arrays(str_replace("%", "<strong>%</strong> ", $this->input->post('note')));
			//$json = file_get_contents('php://input');
			//$sales =json_decode($json);

			//$this->sma->print_arrays($note);


            $gtotal            = 0;
            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;

			if($this->input->post('discount')>0){
				for ($r = 0; $r < $i; $r++) {
					 $item_code          = $_POST['product_code'][$r];
					 $item_type          = $_POST['product_type'][$r];
                $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;

				if($product_details->tax_method != 1)
					$gtotal=$gtotal+($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r]);
				else
					$gtotal=$gtotal+($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r]);

				}
			}
			for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);
                $net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_tax_rate      = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount      = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = $_POST['product_base_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {



					$product_details = $item_type != 'manual' ? $this->carsquotes_model->getProductByCode($item_code) : null;


					if($gtotal>0){
						$order_product_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($gtotal));
                      if($product_details->tax_method != 1)
						$item_discount=$this->sma->formatDecimal((($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					else
						$item_discount=$this->sma->formatDecimal((($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
					}

					// $unit_price = $real_unit_price;
                    //$this->sma->print_arrays($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r]);
					//$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                    if($product_details->tax_method != 1)
					$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
					else
						$pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);

                    //$this->sma->print_arrays($pr_discount);



					$unit_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);

					$item_net_price   = $net_price;


					$pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
						if($product_details->tax_method != 1)
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                        else
						$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $net_price;
                        }else{
							//$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
							$item_net_price=$net_price;
							//$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        //$item_tax    = $ctax['amount'];
                        //$tax         = $ctax['tax'];



						}
						if($product_details->tax_method != 1)
                        $unit_price       = $this->sma->formatDecimal($real_unit_price - $item_tax,12);
						//
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            //$total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                    $unit     = $this->site->getUnitByID($item_unit);

                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($unit_price,12),
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
                        'subtotal'          => $this->sma->formatDecimal($subtotal),
                        'real_unit_price'   => $real_unit_price,
                    ];

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 12);
                    $orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
			if($gtotal>0)
			$order_discount=0;
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            if($gtotal>0)
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping)), 2);
            else
			$grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 2);
            $data           = ['date' => $date,
                'reference_no'        => $reference,
                'customer_id'         => $customer_id,
                'customer'            => $customer,
                'biller_id'           => $biller_id,
                'biller'              => $biller,
                'supplier_id'         => $supplier_id,
                'supplier'            => $supplier,
                'warehouse_id'        => $warehouse_id,
                'note'                => $note,
                'total'               => $orginaltotal,
                'product_discount'    => $product_discount,
                'order_discount_id'   => $this->input->post('discount'),
                'order_discount'      => $order_discount,
                'total_discount'      => $total_discount,
                'product_tax'         => $product_tax,
                'order_tax_id'        => $this->input->post('order_tax'),
                'order_tax'           => $order_tax,
                'total_tax'           => $total_tax,
                'shipping'            => $shipping,
                'grand_total'         => $grand_total,
                'status'              => $status,
                'updated_by'          => $this->session->userdata('user_id'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ];
            if ($this->Settings->indian_gst) {
                //$data['cgst'] = $total_cgst;
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

             //$this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->carsquotes_model->updateQuote($id, $data, $products)) {
            $this->session->set_userdata('remove_quls', 1);
            $this->session->set_flashdata('message', $this->lang->line('reserved_updated'));
            admin_redirect('carssales/quotes_index');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $this->carsquotes_model->getQuoteByID($id);
            $inv_items         = $this->carsquotes_model->getAllQuoteItems($id);
            // krsort($inv_items);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->site->getProductByID($item->product_id);
                if (!$row) {
                    $row             = json_decode('{}');
                    $row->tax_method = 0;
                } else {
                    unset($row->details, $row->product_details, $row->cost, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
                }
                $row->quantity = 0;
                $pis           = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                $row->id              = $item->product_id;
                $row->code            = $item->product_code;
                $row->name            = $item->product_name;
                $row->type            = $item->product_type;
                $row->base_quantity   = $item->quantity;
                $row->base_unit       = isset($row->unit) ? $row->unit : $item->product_unit_id;
                $row->base_unit_price = isset($row->price) ? $row->price : $item->unit_price;
                $row->unit            = $item->product_unit_id;
                $row->qty             = $item->unit_quantity;
                $row->discount        = $item->discount ? $item->discount : '0';
                $row->item_tax        = $item->item_tax      > 0 ? $item->item_tax           / $item->quantity : 0;
                $row->item_discount   = $item->item_discount > 0 ? $item->item_discount / $item->quantity : 0;
                $row->unit_price      = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($row->item_discount) + $this->sma->formatDecimal($row->item_tax) : $item->unit_price + ($row->item_discount);
                $row->real_unit_price = $item->real_unit_price;
                $row->tax_rate        = $item->tax_rate_id;
                $row->option          = $item->option_id;
                $options              = $this->carsquotes_model->getProductOptions($row->id, $item->warehouse_id);

                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }

                $combo_items = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->carsquotes_model->getProductComboItems($row->id, $item->warehouse_id);
                    foreach ($combo_items as $combo_item) {
                        $combo_item->quantity = $combo_item->qty * $item->quantity;
                    }
                }
                $units    = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $ri       = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];
                $c++;
            }
            $this->data['inv_items'] = json_encode($pr);
            $this->data['id']        = $id;
            //$this->data['currencies'] = $this->site->getAllCurrencies();
            $this->data['billers']    = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->site->getAllCompanies('biller') : null;
            $this->data['units']      = $this->site->getAllBaseUnits();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->cars_model->getAllShowrooms() : null;

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('Reserved_vehicle')], ['link' => '#', 'page' => lang('Edit_reserve')]];
            $meta = ['page_title' => lang('Edit_reserve'), 'bc' => $bc];
            $this->page_construct('carsquotes/edit', $meta, $this->data);
        }
		}
    }
    public function pdf_quote($quote_id = null, $view = null, $save_bufffer = null)
    {
        $this->sma->checkPermissions('pdf',true,'quotes');

        if ($this->input->get('id')) {
            $quote_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv                 = $this->carsquotes_model->getQuoteByID($quote_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['rows']       = $this->carsquotes_model->getAllQuoteItems($quote_id);
        $this->data['customer']   = $this->site->getCompanyByID($inv->customer_id);
        $this->data['biller']     = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse']  = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']        = $inv;
        $name                     = $this->lang->line('quote') . '_' . str_replace('/', '_', $inv->reference_no) . '.pdf';
        $html                     = $this->load->view($this->theme . 'carsquotes/pdf', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'carsquotes/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name);
        }
    }
    public function delete_quote($id = null)
    {
        $this->sma->checkPermissions('delete',true,'quotes');
		if(!$this->site->IsPermissions('quotes-delete')){
			 //$this->session->set_flashdata('error', lang('access_denied'));
			 $this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
             //   die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->carsquotes_model->getQuoteByID($id);
        if ($inv->status == 'completed' || $inv->status == 'canceled') {
                        $this->sma->send_json(['error' => 1, 'msg' => lang('Delete_reserve_denied')]);

        }

        if ($this->carsquotes_model->deleteQuote($id)) {

            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('quote_deleted')]);
            }
            $this->session->set_flashdata('message', lang('quote_deleted'));
            admin_redirect('welcome');
        }
		}
    }
    public function suggestions_quote()
    {
        $term         = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $customer_id  = $this->input->get('customer_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed       = $this->sma->analyze_term($term);
        $sr             = $analyzed['term'];
        $option_id      = $analyzed['option_id'];
        $warehouse      = $this->site->getWarehouseByID($warehouse_id);
        $customer       = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        $rows           = $this->carssales_model->getProductNames($sr, $warehouse_id);
        if ($rows) {
            $r = 0;
            foreach ($rows as $row) {
                $c = uniqid(mt_rand(), true);
                unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $option               = false;
                $row->quantity        = 0;
                $row->item_tax_method = $row->tax_method;
                $row->qty             = 1;
                $row->discount        = '0';
                $options              = $this->carssales_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->carssales_model->getProductOptionByID($option_id) : $options[0];
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt        = json_decode('{}');
                    $opt->price = 0;
                    $option_id  = false;
                }
                $row->option = $option_id;
                $pis         = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }
                if ($row->promotion) {
                    $row->price = $row->promo_price;
                } elseif ($customer->price_group_id) {
                    if ($pr_group_price = $this->site->getProductGroupPrice($row->id, $customer->price_group_id)) {
                        $row->price = $pr_group_price->price;
                    }
                } elseif ($warehouse->price_group_id) {
                    if ($pr_group_price = $this->site->getProductGroupPrice($row->id, $warehouse->price_group_id)) {
                        $row->price = $pr_group_price->price;
                    }
                }
                $row->price           = $row->price + (($row->price * $customer_group->percent) / 100);
                $row->real_unit_price = $row->price;
                $row->base_quantity   = 1;
                $row->base_unit       = $row->unit;
                $row->base_unit_price = $row->price;
                $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
                $combo_items          = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->carssales_model->getProductComboItems($row->id, $warehouse_id);
                }
                $units    = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

                $pr[] = ['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id,
                    'row'     => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('No_match_found'), 'value' => $term]]);
        }
    }
/*-----------------------returns---------------------*/
    public function returns_index($warehouse_id = null)
    {
      //  $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->cars_model->getAllShowrooms();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('returns')]];
        $meta = ['page_title' => lang('returns'), 'bc' => $bc];
        $this->page_construct('returns/index', $meta, $this->data);
    }

    public function getReturns($warehouse_id = null)
    {
      //  $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select("{$this->db->dbprefix('returns')}.id as id, DATE_FORMAT({$this->db->dbprefix('returns')}.date, '%Y-%m-%d %T') as date, reference_no, biller, {$this->db->dbprefix('returns')}.customer, grand_total, {$this->db->dbprefix('returns')}.attachment")
                ->from('returns')
                ->where('warehouse_id', $warehouse_id);
        } else {
            $this->datatables
                ->select("{$this->db->dbprefix('returns')}.id as id, DATE_FORMAT({$this->db->dbprefix('returns')}.date, '%Y-%m-%d %T') as date, reference_no, biller, {$this->db->dbprefix('returns')}.customer, grand_total, {$this->db->dbprefix('returns')}.attachment")
                ->from('returns');
        }

        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }
        $this->datatables->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('carssales/edit_return/$1') . "' class='tip' title='" . lang('edit_return') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_return') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('carssales/delete_index_return/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        echo $this->datatables->generate();
    }
    public function edit_return($id = null)
    {
       // $this->sma->checkPermissions();
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->returns_model->getReturnByID($id);
        if (!$this->session->userdata('edit_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->form_validation->set_message('is_natural_no_zero', lang('no_zero_required'));
        $this->form_validation->set_rules('customer', lang('customer'), 'required');
        $this->form_validation->set_rules('biller', lang('biller'), 'required');

        if ($this->form_validation->run() == true) {
            $date             = ($this->Owner || $this->Admin) ? $this->sma->fld(trim($this->input->post('date'))) : $inv->date;
            $reference        = $this->input->post('reference_no');
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $total_items      = $this->input->post('total_items');
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = !empty($customer_details->company) && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = !empty($biller_details->company) && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note             = $this->sma->clear_tags($this->input->post('note'));
            $staff_note       = $this->sma->clear_tags($this->input->post('staff_note'));
            $shipping         = $this->input->post('shipping');

            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' && $_POST['product_option'][$r] != 'null' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_serial        = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate      = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount      = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = $_POST['product_base_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details  = $item_type != 'manual' ? $this->site->getProductByCode($item_code) : null;
                    $pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                    $unit_price       = $this->sma->formatDecimal($unit_price - $pr_discount);
                    $item_net_price   = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $unit_price - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            //$total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);
                    $unit     = $this->site->getUnitByID($item_unit);

                    $product = [
                        'return_id'         => $id,
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($item_net_price + $item_tax),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $unit ? $unit->id : null,
                        'product_unit_code' => $unit ? $unit->code : null,
                        'unit_quantity'     => $item_unit_quantity,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $this->sma->formatDecimal($subtotal),
                        'serial_no'         => $item_serial,
                        'real_unit_price'   => $real_unit_price,
                    ];

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $shipping - $order_discount), 4);
            $data           = [
                'date'              => $date,
                'reference_no'      => $reference,
                'customer_id'       => $customer_id,
                'customer'          => $customer,
                'biller_id'         => $biller_id,
                'biller'            => $biller,
                'warehouse_id'      => $warehouse_id,
                'note'              => $note,
                'staff_note'        => $staff_note,
                'total'             => $total,
                'product_discount'  => $product_discount,
                'order_discount_id' => $this->input->post('order_discount'),
                'order_discount'    => $order_discount,
                'total_discount'    => $total_discount,
                'product_tax'       => $product_tax,
                'order_tax_id'      => $this->input->post('order_tax'),
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'grand_total'       => $grand_total,
                'shipping'          => $shipping,
                'total_items'       => $total_items,
                'updated_by'        => $this->session->userdata('user_id'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ];
            if ($this->Settings->indian_gst) {
                //$data['cgst'] = $total_cgst;
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

        if ($this->form_validation->run() == true && $this->returns_model->updateReturn($id, $data, $products)) {
            $this->session->set_userdata('remove_rels', 1);
            $this->session->set_flashdata('message', lang('return_updated'));
            admin_redirect('carssales/returns_index');
        } else {
            $this->data['inv'] = $inv;
            if ($this->Settings->disable_editing) {
                if ($this->data['inv']->date <= date('Y-m-d', strtotime('-' . $this->Settings->disable_editing . ' days'))) {
                    $this->session->set_flashdata('error', sprintf(lang('return_x_edited_older_than_x_days'), $this->Settings->disable_editing));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $inv_items = $this->returns_model->getReturnItems($id);
            $c         = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->site->getProductByID($item->product_id);
                if (!$row) {
                    $row             = json_decode('{}');
                    $row->tax_method = 0;
                    $row->quantity   = 0;
                } else {
                    unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                }

                $row->id              = $item->product_id;
                $row->code            = $item->product_code;
                $row->name            = $item->product_name;
                $row->type            = $item->product_type;
                $row->item_tax_method = $row->tax_method;
                $options              = $this->returns_model->getProductOptions($row->id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->returns_model->getProductOptionByID($option_id) : current($options);
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt        = json_decode('{}');
                    $opt->price = 0;
                    $option_id  = false;
                }
                if ($row->promotion) {
                    $row->price = $row->promo_price;
                }
                $row->real_unit_price = $row->price;
                $row->base_quantity   = $item->quantity;
                $row->base_unit       = !empty($row->unit) ? $row->unit : $item->product_unit_id;
                $row->base_unit_price = !empty($row->price) ? $row->price : $item->unit_price;
                $row->unit            = $item->product_unit_id;
                $row->qty             = $item->unit_quantity;
                $row->discount        = $item->discount ? $item->discount : '0';
                $row->serial          = $item->serial_no;
                $row->option          = $item->option_id;
                $row->tax_rate        = $item->tax_rate_id;
                $row->comment         = '';
                $combo_items          = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->site->getProductComboItems($row->id);
                }
                $units    = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $ri       = $this->Settings->item_addition ? $row->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $c++;
            }
            $this->data['inv_items']  = json_encode($pr);
            $this->data['id']         = $id;
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['billers']    = $this->site->getAllCompanies('biller');
            $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['units']      = $this->site->getAllBaseUnits();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('returns'), 'page' => lang('returns')], ['link' => '#', 'page' => lang('edit_return')]];
            $meta                     = ['page_title' => lang('edit_return'), 'bc' => $bc];
            $this->page_construct('returns/edit', $meta, $this->data);
        }
    }
    public function delete_index_return($id = null)
    {
      //  $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->returns_model->deleteReturn($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('return_deleted')]);
            }
            $this->session->set_flashdata('message', lang('return_deleted'));
            admin_redirect('welcome');
        }
    }
    public function add_return()
    {
      //  $this->sma->checkPermissions();
        $this->form_validation->set_message('is_natural_no_zero', lang('no_zero_required'));
        $this->form_validation->set_rules('customer', lang('customer'), 'required');
        $this->form_validation->set_rules('biller', lang('biller'), 'required');

        if ($this->form_validation->run() == true) {
            $date             = ($this->Owner || $this->Admin) ? $this->sma->fld(trim($this->input->post('date'))) : date('Y-m-d H:i:s');
            $reference        = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('re');
            $warehouse_id     = $this->input->post('warehouse');
            $customer_id      = $this->input->post('customer');
            $biller_id        = $this->input->post('biller');
            $total_items      = $this->input->post('total_items');
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer         = !empty($customer_details->company) && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details   = $this->site->getCompanyByID($biller_id);
            $biller           = !empty($biller_details->company) && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note             = $this->sma->clear_tags($this->input->post('note'));
            $staff_note       = $this->sma->clear_tags($this->input->post('staff_note'));
            $shipping         = $this->input->post('shipping');

            $total            = 0;
            $product_tax      = 0;
            $product_discount = 0;
            $gst_data         = [];
            $total_cgst       = $total_sgst       = $total_igst       = 0;
            $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id            = $_POST['product_id'][$r];
                $item_type          = $_POST['product_type'][$r];
                $item_code          = $_POST['product_code'][$r];
                $item_name          = $_POST['product_name'][$r];
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' && $_POST['product_option'][$r] != 'null' ? $_POST['product_option'][$r] : null;
                $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
                $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_serial        = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate      = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount      = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = $_POST['product_base_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details  = $item_type != 'manual' ? $this->site->getProductByCode($item_code) : null;
                    $pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                    $unit_price       = $this->sma->formatDecimal($unit_price - $pr_discount);
                    $item_net_price   = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax         = '';

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $unit_price - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                           // $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);
                    $unit     = $this->site->getUnitByID($item_unit);

                    $product = [
                        'product_id'        => $item_id,
                        'product_code'      => $item_code,
                        'product_name'      => $item_name,
                        'product_type'      => $item_type,
                        'option_id'         => $item_option,
                        'net_unit_price'    => $item_net_price,
                        'unit_price'        => $this->sma->formatDecimal($item_net_price + $item_tax),
                        'quantity'          => $item_quantity,
                        'product_unit_id'   => $unit ? $unit->id : null,
                        'product_unit_code' => $unit ? $unit->code : null,
                        'unit_quantity'     => $item_unit_quantity,
                        'warehouse_id'      => $warehouse_id,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'discount'          => $item_discount,
                        'item_discount'     => $pr_item_discount,
                        'subtotal'          => $this->sma->formatDecimal($subtotal),
                        'serial_no'         => $item_serial,
                        'real_unit_price'   => $real_unit_price,
                    ];

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $shipping - $order_discount), 4);
            $data           = [
                'date'              => $date,
                'reference_no'      => $reference,
                'customer_id'       => $customer_id,
                'customer'          => $customer,
                'biller_id'         => $biller_id,
                'biller'            => $biller,
                'warehouse_id'      => $warehouse_id,
                'note'              => $note,
                'staff_note'        => $staff_note,
                'total'             => $total,
                'product_discount'  => $product_discount,
                'order_discount_id' => $this->input->post('order_discount'),
                'order_discount'    => $order_discount,
                'total_discount'    => $total_discount,
                'product_tax'       => $product_tax,
                'order_tax_id'      => $this->input->post('order_tax'),
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'grand_total'       => $grand_total,
                'total_items'       => $total_items,
                'paid'              => 0,
                'created_by'        => $this->session->userdata('user_id'),
                'hash'              => hash('sha256', microtime() . mt_rand()),
            ];
            if ($this->Settings->indian_gst) {
                //$data['cgst'] = $total_cgst;
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

        if ($this->form_validation->run() == true && $this->returns_model->addReturn($data, $products)) {
            $this->session->set_userdata('remove_rels', 1);
            $this->session->set_flashdata('message', lang('return_added'));
            admin_redirect('carssales/returns_index');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['billers']    = $this->site->getAllCompanies('biller');
            $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['units']      = $this->site->getAllBaseUnits();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('returns'), 'page' => lang('returns')], ['link' => '#', 'page' => lang('add_return')]];
            $meta                     = ['page_title' => lang('add_return'), 'bc' => $bc];
            $this->page_construct('returns/add', $meta, $this->data);
        }
    }
    public function daily_sales($warehouse_id = null, $year = null, $month = null, $pdf = null, $user_id = null)
    {
        $this->sma->checkPermissions('daily_sales',true,'sales');

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
            'next_prev_url'  => admin_url('carssales/daily_sales/' . ($warehouse_id ? $warehouse_id : 0)),
            'month_type'     => 'long',
            'day_type'       => 'long',
        ];

        $config['template'] = '{table_open}<div class="row"><div class="col-sm-12 col-md-12"><div class="table-responsive"><table  border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
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
        $sales = $user_id ? $this->carsreports_model->getStaffDailySales($user_id, $year, $month, $warehouse_id) : $this->carsreports_model->getDailySales($year, $month, $warehouse_id);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<div class='row'><div class='col-sm-12 col-md-12'><div class='table-responsive'><table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang('discount') . '</td><td>' . $this->sma->formatMoney($sale->discount) . '</td></tr><tr><td>' . lang('Total_before_VAT') . '</td><td>' . $this->sma->formatMoney($sale->totalBVAT) . '</td></tr><tr><td>' . lang('VAT_Amount') . '</td><td>' . $this->sma->formatMoney($sale->tax1) . '</td></tr><tr><td>' . lang('shipping') . '</td><td>' . $this->sma->formatMoney($sale->shipping) . '</td></tr><tr><td>' . lang('total') . '</td><td>' . $this->sma->formatMoney($sale->total) . '</td></tr></table></div></div></div>';
            }
        } else {
            $daily_sale = [];
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        $this->data['year']     = $year;
        $this->data['month']    = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'carsreports/daily', $this->data, true);
            $name = lang('daily_sales') . '_' . $year . '_' . $month . '.pdf';
            $html = str_replace('<p class="introtext">' . lang('reports_calendar_text') . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->cars_model->getAllShowrooms();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('daily_sales_report')]];
        $meta                        = ['page_title' => lang('daily_sales_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carsreports/daily', $meta, $this->data);
    }
    public function monthly_sales($warehouse_id = null, $year = null, $pdf = null, $user_id = null)
    {
        $this->sma->checkPermissions('monthly_sales',true,'sales');

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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year']  = $year;
        $this->data['sales'] = $user_id ? $this->carsreports_model->getStaffMonthlySales($user_id, $year, $warehouse_id) : $this->carsreports_model->getMonthlySalesItems($year, $warehouse_id);

		  //$this->sma->print_arrays($this->data['sales']);

	   if ($pdf) {
            $html = $this->load->view($this->theme . 'carsreports/monthly', $this->data, true);
            $name = lang('monthly_sales') . '_' . $year . '.pdf';
            $html = str_replace('<p class="introtext">' . lang('reports_calendar_text') . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->cars_model->getAllShowrooms();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('monthly_sales_report')]];
        $meta                        = ['page_title' => lang('monthly_sales_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carsreports/monthly', $meta, $this->data);
    }
    public function sales()
    {
         $this->sma->checkPermissions('transaction_report',true,'sales');
        //$this->sma->checkPermissions('reports');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->carsreports_model->getStaff();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
		$this->data['categories'] = $this->site->getAllCategories();
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('sales_report')]];
        $meta                     = ['page_title' => lang('sales_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carsreports/sales_trans', $meta, $this->data);
    }

    public function sales_report()
    {
        $this->sma->checkPermissions('sales_report',true,'sales');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->carsreports_model->getStaff();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('sales_report')]];
        $meta                     = ['page_title' => lang('sales_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
	    $this->page_construct('carsreports/sales', $meta, $this->data);
    }

	public function sales_cn_report()
    {
        $this->sma->checkPermissions('credit_notes',true,'sales');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->carsreports_model->getStaff();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('Credit_Notes')]];
        $meta                     = ['page_title' => lang('Credit_Notes'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
	    $this->page_construct('carssales/sales_cn', $meta, $this->data);
    }

    public function delivery_report()
    {
        $this->sma->checkPermissions('delivery_report',true,'sales');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->carsreports_model->getStaff();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['stuffs']    = $this->site->getAllResturantEmp('3');

        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('Sales_Delivery_Report')]];
        $meta                     = ['page_title' => lang('Sales_Delivery_Report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carsreports/delivery_report', $meta, $this->data);
    }

    public function staff_report($user_id = null, $year = null, $month = null, $pdf = null, $cal = 0)
    {
        if (!$user_id) {
            $this->session->set_flashdata('error', lang('no_user_selected'));
            admin_redirect('carsreports/users');
        }
        $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['purchases']  = $this->carsreports_model->getStaffPurchases($user_id);
        $this->data['sales']      = $this->carsreports_model->getStaffSales($user_id);
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();

        if (!$year) {
            $year = date('Y');
        }
        if (!$month || $month == '#monthly-con') {
            $month = date('m');
        }
        if ($pdf) {
            if ($cal) {
                $this->monthly_sales($year, $pdf, $user_id);
            } else {
                $this->daily_sales($year, $month, $pdf, $user_id);
            }
        }
        $config = [
            'show_next_prev' => true,
            'next_prev_url'  => admin_url('carsreports/staff_report/' . $user_id),
            'month_type'     => 'long',
            'day_type'       => 'long',
        ];

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable reports-table">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th class="text-center"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th class="text-center" colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th class="text-center"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
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
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $this->carsreports_model->getStaffDailySales($user_id, $year, $month);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang('discount') . '</td><td>' . $this->sma->formatMoney($sale->discount) . '</td></tr><tr><td>' . lang('product_tax') . '</td><td>' . $this->sma->formatMoney($sale->tax1) . '</td></tr><tr><td>' . lang('order_tax') . '</td><td>' . $this->sma->formatMoney($sale->tax2) . '</td></tr><tr><td>' . lang('total') . '</td><td>' . $this->sma->formatMoney($sale->total) . '</td></tr></table>';
            }
        } else {
            $daily_sale = [];
        }
        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        if ($this->input->get('pdf')) {
        }
        $this->data['year']    = $year;
        $this->data['month']   = $month;
        $this->data['msales']  = $this->carsreports_model->getStaffMonthlySales($user_id, $year);
        $this->data['user_id'] = $user_id;
        $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('staff_report')]];
        $meta                  = ['page_title' => lang('staff_report'), 'bc' => $bc];
        $this->page_construct('carsreports/staff_report', $meta, $this->data);
    }
    public function best_sellers($warehouse_id = null)
    {
        $this->sma->checkPermissions('best_sellers',true,'sales');

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $y1                  = date('Y', strtotime('-0 month'));
        $m1                  = date('m', strtotime('-0 month'));
        $m1sdate             = $y1 . '-' . $m1 . '-01 00:00:00';
        $m1edate             = $y1 . '-' . $m1 . '-' . days_in_month($m1, $y1) . ' 23:59:59';
        $this->data['m1']    = date('M Y', strtotime($y1 . '-' . $m1));
		//$this->sma->print_arrays($y1 );
        $this->data['m1bs']  = $this->carsreports_model->getBestSeller($m1sdate, $m1edate, $warehouse_id);
        $y2                  = date('Y', strtotime('-1 months'));
        $m2                  = date('m', strtotime('-1 months'));
        $m2sdate             = $y2 . '-' . $m2 . '-01 00:00:00';
        $m2edate             = $y2 . '-' . $m2 . '-' . days_in_month($m2, $y2) . ' 23:59:59';
        $this->data['m2']    = date('M Y', strtotime($y2 . '-' . $m2));
        $this->data['m2bs']  = $this->carsreports_model->getBestSeller($m2sdate, $m2edate, $warehouse_id);
        $y3                  = date('Y', strtotime('-3 months'));
        $m3                  = date('m', strtotime('-3 months'));
        $m3sdate             = $y3 . '-' . $m3 . '-01 23:59:59';
        $this->data['m3']    = date('M Y', strtotime($y3 . '-' . $m3)) . ' - ' . $this->data['m1'];
        $this->data['m3bs']  = $this->carsreports_model->getBestSeller($m3sdate, $m1edate, $warehouse_id);
        $y4                  = date('Y', strtotime('-12 months'));
        $m4                  = date('m', strtotime('-12 months'));
        $m4sdate             = $y4 . '-' . $m4 . '-01 23:59:59';
        $this->data['m4']    = date('M Y', strtotime($y4 . '-' . $m4)) . ' - ' . $this->data['m1'];
        $this->data['m4bs']  = $this->carsreports_model->getBestSeller($m4sdate, $m1edate, $warehouse_id);
        // $this->sma->print_arrays($this->data['m1bs'], $this->data['m2bs'], $this->data['m3bs'], $this->data['m4bs']);
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('best_sellers')]];
        $meta                     = ['page_title' => lang('best_sellers'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carsreports/best_sellers', $meta, $this->data);
    }


    public function customer_groups()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('customer_groups')]];
        $meta = ['page_title' => lang('customer_groups'), 'bc' => $bc];
        $this->page_construct('settings/customer_groups', $meta, $this->data);
    }

    public function price_groups()
    {
        $this->sma->checkPermissions('price_groups',true,'sales');
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('price_groups')]];
        $meta = ['page_title' => lang('price_groups'), 'bc' => $bc];
        $this->page_construct('settings/price_groups', $meta, $this->data);
    }

    function register()
    {
        $this->sma->checkPermissions('register_report',true,'sales');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->carsreports_model->getStaff();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('carsreports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('register_report')));
        $meta = array('page_title' => lang('register_report'), 'bc' => $bc);
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carsreports/register', $meta, $this->data);
    }

    public function customers_report()
    {
        $this->sma->checkPermissions('customers_report',true,'sales');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('customers_report')]];
        $meta = ['page_title' => lang('customers_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carsreports/customers', $meta, $this->data);
    }

	public function dashboard()
    {
        /*if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            admin_redirect('sync');
        }

        $this->data['error']     = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales']     = $this->db_model->getLatestSales();
        $this->data['quotes']    = $this->db_model->getLastestQuotes();
        $this->data['purchases'] = $this->db_model->getLatestPurchases();
        $this->data['transfers'] = $this->db_model->getLatestTransfers();
        $this->data['customers'] = $this->db_model->getLatestCustomers();
        $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
		$this->data['chatDataTrends']  = $this->db_model->getChartDataTrends();

        $this->data['chatData']  = $this->db_model->getSalesLocationChart();
        $this->data['stock']     = $this->db_model->getStockValue();
        $this->data['bs']        = $this->db_model->getBestSellerchart();
        $this->data['bc']        = $this->db_model->getBestCustomerschart();
        $this->data['ubc']        = $this->db_model->getUnBestCustomerschart();
        $this->data['ubs']        = $this->db_model->getUnBestSellerchart();
        $lmsdate                 = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate                 = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        $this->data['lmbs']      = $this->db_model->getBestSeller($lmsdate, $lmedate);
        $bc                      = [['link' => '#', 'page' => lang('dashboard')]];
        $meta                    = ['page_title' => lang('dashboard'), 'bc' => $bc];
        $this->page_construct('carssales/dashboard', $meta, $this->data);
		*/
		if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            admin_redirect('sync');
        }

        $this->data['error']     = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales']     = $this->db_model->getLatestSales();
        $this->data['allSales']     = $this->db_model->getAllSales();
        $this->data['rSales']     = $this->db_model->getReturns();
		$this->data['total_sales_canv']   = $this->db_model->getMonthlySales(date('Y'));
        $this->data['quotes']    = $this->db_model->getLastestQuotes();
        $this->data['purchases'] = $this->db_model->getLatestPurchases();
        $this->data['transfers'] = $this->db_model->getLatestTransfers();
        $this->data['customers'] = $this->db_model->getLatestCustomers();
        $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
		$this->data['chatDataTrends']  = $this->db_model->getChartDataTrends();

        $this->data['chatData']  = $this->db_model->getChartData();

        $this->data['salesLocation']  = $this->db_model->getSalesLocationChart();
        $this->data['stock']     = $this->db_model->getStockValue();
        $this->data['bs']        = $this->db_model->getBestSellerchart();
        $this->data['bc']        = $this->db_model->getBestCustomerschart();
        $this->data['ubc']        = $this->db_model->getUnBestCustomerschart();
        $this->data['ubs']        = $this->db_model->getUnBestSellerchart();
        $lmsdate                 = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate                 = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        $this->data['lmbs']      = $this->db_model->getBestSeller($lmsdate, $lmedate);
        $bc                      = [['link' => '#', 'page' => lang('dashboard')]];
        $meta                    = ['page_title' => lang('dashboard'), 'bc' => $bc];
        $this->page_construct('carssales/dashboard', $meta, $this->data);
    }

	    public function add_price_group()
    {
        $this->sma->checkPermissions('add_price_group',true,'sales');
        //$this->form_validation->set_rules('name', lang('group_name'), 'trim|is_unique[price_groups.name]|required|alpha_numeric_spaces');
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|is_unique[price_groups.name]|required');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('add_price_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('carssales/price_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addPriceGroup($data)) {
            $this->session->set_flashdata('message', lang('price_group_added'));
            admin_redirect('carssales/price_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_price_group', $this->data);
        }
    }


	    public function getPriceGroups()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name')
            ->from('price_groups')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('carssales/group_product_prices/$1') . "' class='tip' title='" . lang('group_product_prices') . "'><i class=\"fa fa-eye\"></i></a>  <a href='" . admin_url('carssales/edit_price_group/$1') . "' class='tip' title='" . lang('edit_price_group') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_price_group') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('carssales/delete_price_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }


	    public function update_product_group_price($group_id = null)
    {
        //  $this->sma->checkPermissions('update_prices_excel',true,'sales');
        if (!$group_id) {
            $this->sma->send_json(['status' => 0]);
        }

        $product_id = $this->input->post('product_id', true);
        $price      = $this->input->post('price', true);
		$unit      = $this->input->post('unit', true);
		$product_code      = $this->input->post('product_code', true);
		//$this->sma->print_arrays($product_id, $group_id, $price, $unit);

        if (!empty($product_id) && !empty($price)) {
			if ($this->settings_model->setProductPriceForPriceGroup($product_id, $group_id, $price, $unit, $product_code)) {
                $this->sma->send_json(['status' => 1]);
            }
        }

        $this->sma->send_json(['status' => 0]);
    }

	public function get_product_group_price($group_id = null)
    {
        if (!$group_id) {
            $this->sma->send_json(['status' => 0]);
        }

        $product_id = $this->input->post('product_id', true);
        $product_unit_id      = $this->input->post('product_unit_id', true);
		//$this->sma->print_arrays($product_id, $group_id, $price, $unit);


			if ($gp=$this->settings_model->getProductPriceForPriceGroup($product_id, $group_id, $product_unit_id)) {
                $this->sma->send_json($gp);
            }


        $this->sma->send_json(['status' => 0]);
    }


	    public function getProductPrices($group_id = null)
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('carssales/price_groups');
        }

        /*$pp = "( SELECT {$this->db->dbprefix('product_prices')}.product_id as product_id, {$this->db->dbprefix('product_prices')}.price as price FROM {$this->db->dbprefix('product_prices')} WHERE price_group_id = {$group_id} ) PP";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as product_code, {$this->db->dbprefix('products')}.name as product_name, PP.price as price ")
            ->from('products')
            ->join($pp, 'PP.product_id=products.id', 'left')
            ->edit_column('price', '$1__$2', 'id, price')
            ->add_column('Actions', '<div class="text-center"><button class="btn btn-primary btn-xs form-submit" type="button"><i class="fa fa-check"></i></button></div>', 'id');

			*/

		$pp = "( SELECT {$this->db->dbprefix('product_prices')}.product_id as product_id, {$this->db->dbprefix('product_prices')}.product_unit_id as product_unit_id, {$this->db->dbprefix('product_prices')}.price as price, {$this->db->dbprefix('product_prices')}.product_code as product_code FROM {$this->db->dbprefix('product_prices')} WHERE price_group_id = {$group_id} ) PP";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as product_code, {$this->db->dbprefix('products')}.name as product_name, PP.product_unit_id as unit, PP.price as price , PP.product_code as product_code2, {$this->db->dbprefix('products')}.unit as base_unit ")
            ->from('products')
            ->join($pp, 'PP.product_id=products.id', 'left')
            ->edit_column('price', '$1__$2', 'id, price')
            ->edit_column('unit', '$1__$2__$3', 'id, unit, base_unit')
            ->add_column('Actions', '<div class="text-center"><button class="btn btn-primary btn-xs form-submit" type="button"><i class="fa fa-check"></i></button></div>', 'id');


        echo $this->datatables->generate();
    }


    public function product_group_price_actions($group_id)
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('carssales/price_groups');
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {


			//$this->sma->print_arrays($_POST);

			if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'update_price') {
					$cnt=0;
                    $cnt1=0;
					$product_unit_id=0;
					foreach ($_POST['val'] as $id) {
					foreach ($_POST['val1'] as $uid) {
						$product_unit_id=$uid;
						if($cnt1==$cnt)
						break;
						$cnt1++;

					}


						$this->settings_model->setProductPriceForPriceGroup($id, $group_id, $this->input->post('price' . $id), $product_unit_id, $this->input->post('product_code' . $id));
                    $cnt++;
					}
                    $this->session->set_flashdata('message', lang('products_group_price_updated'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'delete') {
					$cnt=0;
                    $cnt1=0;
					$product_unit_id=0;
					foreach ($_POST['val'] as $id) {
					foreach ($_POST['val1'] as $uid) {
						$product_unit_id=$uid;
						if($cnt1==$cnt)
						break;
						$cnt1++;

					}

						//$this->sma->print_arrays($product_unit_id);

						$this->settings_model->deleteProductGroupPrice($id, $group_id,$product_unit_id);
						$cnt++;
                    }
                    $this->session->set_flashdata('message', lang('products_group_price_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('group_name'));
                    $row   = 2;
                    $group = $this->settings_model->getPriceGroupByID($group_id);
                    foreach ($_POST['val'] as $id) {
                        $pgp = $this->settings_model->getProductGroupPriceByPID($id, $group_id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pgp->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pgp->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pgp->price);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $group->name);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'price_groups_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_price_group_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }



    public function update_prices_csv($group_id = null)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('message', lang('disabled_in_demo'));
                admin_redirect('welcome');
            }

            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('carssales/group_product_prices/' . $group_id);
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = ['code', 'price'];

                $final = [];

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if ($product = $this->site->getProductByCode(trim($csv_pr['code']))) {
                        $data[] = [
                            'product_id'     => $product->id,
                            'price'          => $csv_pr['price'],
                            'price_group_id' => $group_id,
                        ];
                    } else {
                        $this->session->set_flashdata('message', lang('check_product_code') . ' (' . $csv_pr['code'] . '). ' . lang('code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('carssales/group_product_prices/' . $group_id);
                    }
                    $rw++;
                }
            }
        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('carssales/group_product_prices/' . $group_id);
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            $this->settings_model->updateGroupPrices($data);
            $this->session->set_flashdata('message', lang('price_updated'));
            admin_redirect('carssales/group_product_prices/' . $group_id);
        } else {
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['group']    = $this->site->getPriceGroupByID($group_id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/update_price', $this->data);
        }
    }

    public function update_prices_excel($group_id = null)
    {
        $this->load->helper('security');
        //$this->form_validation->set_rules('xlsx_file', lang('upload_file'), 'required');
		$this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');


        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('message', lang('disabled_in_demo'));
                admin_redirect('welcome');
            }

            /*if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('carssales/group_product_prices/' . $group_id);
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


                //$data1['attachment'] = $csv;


                /*$arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = ['code', 'price'];

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

				//$this->sma->print_arrays($allDataInSheet);

				$flag = true;
				$i=0;
				foreach ($allDataInSheet as $value) {
				  if($flag){
					$flag =false;
					continue;
				  }
				  //$inserdata[$i]['#'] = $value['A'];$keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];

				  $inserdata[$i]['code'] = trim($value['A']);
				  $inserdata[$i]['price'] = trim($value['B']);
				  $inserdata[$i]['product_unit_id'] = trim($value['C']);
				  $inserdata[$i]['product_code'] = trim($value['D']);
				  $i++;
				}



                $rw = 2;
                foreach ($inserdata as $csv_pr) {
					if ($product = $this->site->getProductByCode(trim($csv_pr['code']))) {
                        $data1[] = [
                            'product_id'     => $product->id,
                            'price'          => $csv_pr['price'],
                            'product_unit_id'          => $this->carssales_model->getUnitByCode(trim($csv_pr['product_unit_id']))->id,
                            'product_code'          => $csv_pr['product_code'],
                            'price_group_id' => $group_id,
                        ];
                    } else {
                        $this->session->set_flashdata('message', lang('check_product_code') . ' (' . $csv_pr['code'] . '). ' . lang('code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('carssales/group_product_prices/' . $group_id);
                    }
                    $rw++;
                }
				//$this->sma->print_arrays($data1);

            }
        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('carssales/group_product_prices/' . $group_id);
        }

        if ($this->form_validation->run() == true && !empty($data1)) {
            $this->settings_model->updateGroupPrices($data1);
            $this->session->set_flashdata('message', lang('price_updated'));
            admin_redirect('carssales/group_product_prices/' . $group_id);
        } else {
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['group']    = $this->site->getPriceGroupByID($group_id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/update_price', $this->data);
        }
    }

    public function group_product_prices($group_id = null)
    {
        $this->sma->checkPermissions('group_product_prices',true,'sales');
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('carssales/price_groups');
        }

        $this->data['price_group'] = $this->settings_model->getPriceGroupByID($group_id);
        $this->data['units'] = json_encode($this->site->getAllUnits());
        $this->data['error']       = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')],  ['link' => admin_url('carssales/price_groups'), 'page' => lang('price_groups')], ['link' => '#', 'page' => lang('group_product_prices')]];
        $meta                      = ['page_title' => lang('group_product_prices'), 'bc' => $bc];
        $this->page_construct('settings/group_product_prices', $meta, $this->data);
    }

	public function edit_price_group($id = null)
    {
        $this->sma->checkPermissions('edit_price_group',true,'sales');
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|required|alpha_numeric_spaces');
        $pg_details = $this->settings_model->getPriceGroupByID($id);
        if ($this->input->post('name') != $pg_details->name) {
            $this->form_validation->set_rules('name', lang('group_name'), 'required|is_unique[price_groups.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('edit_price_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('carssales/price_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePriceGroup($id, $data)) {
            $this->session->set_flashdata('message', lang('price_group_updated'));
            admin_redirect('carssales/price_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['price_group'] = $pg_details;
            $this->data['id']          = $id;
            $this->data['modal_js']    = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_price_group', $this->data);
        }
    }
    public function delete_price_group($id = null)
    {
        $this->sma->checkPermissions('delete_price_group',true,'sales');
        if ($this->settings_model->deletePriceGroup($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('price_group_deleted')]);
        }
    }

	    public function reports()
    {
      //  $this->sma->checkPermissions('reports');
        $data['error']               = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->carsreports_model->getChartData();
        $this->data['stock']         = $this->carsreports_model->getStockValue();
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('reports')]];
        $meta                        = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->page_construct('carssales/reports', $meta, $this->data);
    }

    public function getProductPromo($pId = null, $warehouse_id = null, $item_quantity = null)
    {
       // $this->sma->checkPermissions('index');
        if ($this->input->get('product_id')) {
            $pId = $this->input->get('product_id', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        if ($this->input->get('item_quantity')) {
            $item_quantity = $this->input->get('item_quantity', true);
        }
        $this->load->admin_model('promo_model');
        $promos = $this->promo_model->getPromosByProduct($pId,$warehouse_id);


        if ($promos) {
            foreach ($promos as $promo) {
                $warehouse = $this->site->getWarehouseByID($warehouse_id);
                $row       = $this->pos_model->getWHProductById($promo->free_product, $warehouse_id);
                $option    = false;
				$freeqty=floor($item_quantity/$promo->buy_quantity)*($promo->get_quantity);
				$intfreeqty=($item_quantity/$promo->buy_quantity)*($promo->get_quantity);
				if($freeqty==$intfreeqty)
					$freeqty=1;
				else
					$freeqty=0;

                if ($row && $freeqty>0) {
                    unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    $row->item_tax_method = $row->tax_method;
					$freeqty=floor($item_quantity/$promo->buy_quantity)*($promo->get_quantity);

                    $row->qty             = $freeqty;
                    $row->price           = 0;
                    $row->discount        = '0';
                    $row->serial          = '';
                    $options              = $this->pos_model->getProductOptions($row->id, $warehouse_id);
                    if ($options) {
                        $opt = current($options);
                        if (!$option) {
                            $option = $opt->id;
                        }
                    }
                    $row->option          = $option;
                    $row->real_unit_price = $row->price;
                    $row->base_quantity   = $freeqty;
                    $row->base_unit       = $row->unit;
                    $row->base_unit_price = $row->price;
                    $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
                    $row->comment         = '';
                    $combo_items          = false;
                    // if ($row->type == 'combo') {
                    //     $combo_items = $this->pos_model->getProductComboItems($row->id, $warehouse_id);
                    // }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = false; // $this->site->getTaxRateByID($row->tax_rate);

                    $pr = ['id' => sha1(uniqid(mt_rand(), true)), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];

                    $this->sma->send_json($pr);
                } else {
                    echo null;
                }
            }
        } else {
            echo null;
        }
    }


	public function getProductPromoRquantity($pId = null, $warehouse_id = null, $item_quantity = null)
    {
       // $this->sma->checkPermissions('index');
        if ($this->input->get('product_id')) {
            $pId = $this->input->get('product_id', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        if ($this->input->get('item_quantity')) {
            $item_quantity = $this->input->get('item_quantity', true);
        }
        $this->load->admin_model('promo_model');
        $promos = $this->promo_model->getPromosByProduct($pId,$warehouse_id);


        if ($promos) {
            foreach ($promos as $promo) {
                $warehouse = $this->site->getWarehouseByID($warehouse_id);
                $row       = $this->pos_model->getWHProductById($promo->free_product, $warehouse_id);
                $option    = false;
				$freeqty=floor($item_quantity/$promo->buy_quantity)*($promo->get_quantity);
				if($promo->free_product==$promo->offer_product)
				$freeqty=$freeqty + $item_quantity;

                if ($row) {
                    unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    $row->item_tax_method = $row->tax_method;

                    $row->qty             = $freeqty;
                    $row->price           = 0;
                    $row->discount        = '0';
                    $row->serial          = '';
                    $options              = $this->pos_model->getProductOptions($row->id, $warehouse_id);
                    if ($options) {
                        $opt = current($options);
                        if (!$option) {
                            $option = $opt->id;
                        }
                    }
                    $row->option          = $option;
                    $row->real_unit_price = $row->price;
                    $row->base_quantity   = $freeqty;
                    $row->base_unit       = $row->unit;
                    $row->base_unit_price = $row->price;
                    $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
                    $row->comment         = '';
                    $combo_items          = false;
                    // if ($row->type == 'combo') {
                    //     $combo_items = $this->pos_model->getProductComboItems($row->id, $warehouse_id);
                    // }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = false; // $this->site->getTaxRateByID($row->tax_rate);

                    $pr = ['id' => sha1(uniqid(mt_rand(), true)), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];

                    $this->sma->send_json($pr);
                } else {
                    echo null;
                }
            }
        } else {
            echo null;
        }
    }
		public function getCurrencyByCode($code = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->site->getCurrencyByCode($code);
		$this->sma->send_json($row);
    }


	function getSalesReportCreditNote($pdf = NULL, $xls = NULL)
    {
        $this->sma->checkPermissions('credit_notes', TRUE,'sales');
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
            $end_date = $this->sma->fld($end_date).' 23:59:59';
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('sale_items') . ".product_name, ' (', " . $this->db->dbprefix('sale_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, payment_status", FALSE)
                ->from('sales')
                ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
                ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')
                ->group_by('sales.id')
                ->order_by('sales.date desc');

					$this->db->where('sales.sale_status', 'returned');


            if ($user) {
                $this->db->where('sales.created_by', $user);
            }
            if ($product) {
                $this->db->where('sale_items.product_id', $product);
            }
            if ($serial) {
                $this->db->like('sale_items.serial_no', $serial);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('sales').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                    if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }
                   $this->excel->getActiveSheet()->setTitle(lang('Credit_Notes'));
                        $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Credit_Notes'));
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

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);


                        $row++;
                         $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                $this->excel->getActiveSheet()->SetCellValue('A'.$row, ($this->input->get('start_date') ?  lang('period') .' '. lang('Credit_Notes') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
               $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
                $row++;

                $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('product').' / '.lang('quantity'));
                $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('payment_status'));
                 $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($fillColor);
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($Font11Bold);
                        $row++;
                $total = 0;
                $paid = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($data_row->grand_total - $data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, lang($data_row->payment_status));

                        if($data_row->payment_status=='paid'){
                        $this->excel->getActiveSheet()->getStyle('I'.$row)->applyFromArray($styleArrayFillGreen);
                        }else if($data_row->payment_status=='pending'){
                        $this->excel->getActiveSheet()->getStyle('I'.$row)->applyFromArray($styleArrayFillYellow);
                        }else if($data_row->payment_status=='due'){
                        $this->excel->getActiveSheet()->getStyle('I'.$row)->applyFromArray($styleArrayFillRed);
                        }else if($data_row->payment_status=='partial'){
                        $this->excel->getActiveSheet()->getStyle('I'.$row)->applyFromArray($styleArrayFillBlu);
                        }


                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $balance += ($data_row->grand_total - $data_row->paid);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'Credit_Notes';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $si = "( SELECT sale_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('sale_items')}.product_name, '__', {$this->db->dbprefix('sale_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('sale_items')} ";
            if ($product || $serial) { $si .= " WHERE "; }
            if ($product) {
                $si .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
            }
            if ($product && $serial) { $si .= " AND "; }
            if ($serial) {
                $si .= " {$this->db->dbprefix('sale_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $si .= " GROUP BY {$this->db->dbprefix('sale_items')}.sale_id ) FSI";
            $this->load->library('datatables');
            $this->datatables
                ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, FSI.item_nane as iname, grand_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('sales')}.id as id", FALSE)
                ->from('sales')
                ->join($si, 'FSI.sale_id=sales.id', 'left');
                //->join('cars_showrooms', 'cars_showrooms.id=sales.warehouse_id', 'left');
                // ->group_by('sales.id');
				$this->datatables->where('sales.sale_status', 'returned');

            if ($user) {
                $this->datatables->where('sales.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('sales').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }
    public function pqFormatFWS($x) {
 if ($x != null) {

     $d = "";
         $pqc =explode("___",$x); //x.split('___');

     for ($index = 0; $index < count($pqc); $index++) {

         $pq = $pqc[$index];
         $v = explode("__",$pq);//pq.split('__');
         $d = $d."".$v[0]."\n";

     }
     return $d;
 } else {
     return "";
 }
}


public function pqFormatFWSPriceSplit($x) {

    $total=0;
    $flage=0;

if ($x != null) {
 $d = "";
     $pqc = explode("___",$x);//x.split('___');
 for ($index = 0; $index < count($pqc); $index++) {

     $pq = $pqc[$index];
     $v = explode("__",$pq);//pq.split('__');

     if($index==0){
     $flage=$v[1];
     $total=$this->sma->formatDecimal($v[0]) + $this->sma->formatDecimal($total);

     if(count($pqc)==1)
     $d=$d."".$this->sma->formatDecimal($total)."\n";
     }else{
         if($flage==$v[1]){
             $total=$this->sma->formatDecimal($v[0]) + $this->sma->formatDecimal($total);
            // $this->sma->print_arrays($total);
         }else{
            $d=$d."".$this->sma->formatDecimal($total)."\n";
             $flage=$v[1];
             $total=0;
             $total=$this->sma->formatDecimal($v[0]) + $this->sma->formatDecimal($total);

         }

     }
    }
 if(count($pqc)!=1)
 $d=$d."".$this->sma->formatDecimal($total)."\n";

 return $d;
} else {
 return "";
}
}




public function pqFormatFWSTotalPrice($x) {
    $total=0;
           // alert(x);
           if ($x != null) {

            $d = "";
                $pqc =explode("___",$x); //x.split('___');

            for ($index = 0; $index < count($pqc); $index++) {

                $pq = $pqc[$index];
                $v = explode("__",$pq);//pq.split('__');

          //  $total=parseFloat((1)*$v[0]) + parseFloat($total);
          $total=$this->sma->formatDecimal((1)*$v[0]) + $this->sma->formatDecimal($total);


        }
       // return decimalFormat($total);



        return $total;

    } else {
        return '0';
    }
}


public function pqFormatFWSTotalPriceMinus($x) {
    $total=0;
           // alert(x);
           if ($x != null) {

            $d = "";
                $pqc =explode("___",$x); //x.split('___');

            for ($index = 0; $index < count($pqc); $index++) {

                $pq = $pqc[$index];
                $v = explode("__",$pq);//pq.split('__');
          //  $total=parseFloat((-1)*$v[0]) + parseFloat($total);
          $total=$this->sma->formatDecimal((-1)*$v[0]) + $this->sma->formatDecimal($total);


        }

       return $total;
    } else {
        return '0';
    }
}

function pqFormatFWSPrice($x) {

    if ($x != null) {
        $d = "";
            $pqc = explode("___",$x);//x.split('___');
        for ($index = 0; $index < count($pqc); $index++) {

            $pq = $pqc[$index];
            $v = explode("__",$pq);//pq.split('__');

            $total=0;
            $total=$this->sma->formatDecimal($v[0]) + $this->sma->formatDecimal($total);
            $d=$d."".$this->sma->formatDecimal($total)."\n";

 }
 return $d;
} else {
 return '';
}
}


	public function updateSalePaymentDue()
    {
		$sales=$this->site->getAllSalesDue_temp();
		//$this->sma->print_arrays($sales);
		foreach ($sales as $sale) {
            $grand_total = $sale->grand_total;
            $paid = $sale->paid;
           // $this->sma->print_arrays($this->sma->formatDecimal($grand_total,2));


            if ($this->sma->formatDecimal($grand_total) == $this->sma->formatDecimal($paid)) {

            $this->db->update('sales', ['grand_total' => $this->sma->formatDecimal($grand_total,2), 'payment_status' => 'paid'], ['id' => $sale->id]);

			}
		}
		//$this->sma->print_arrays($sales);
		//$this->site->syncSalePayments_temp();
	}

	public function customer_report($user_id = null)
    {
        $this->sma->checkPermissions('customers_report', true,'sales');
        //$this->sma->checkPermissions('customers', true);
        if (!$user_id) {
            $this->session->set_flashdata('error', lang('no_customer_selected'));
            admin_redirect('carsreports/customers');
        }

        $this->data['sales']         = $this->carsreports_model->getSalesTotals($user_id);
        $this->data['total_sales']   = $this->carsreports_model->getCustomerSales($user_id);
		$this->data['total_sales_canv']   = $this->carsreports_model->getMonthlySales(date('Y'), '' ,$user_id);
        $this->data['total_quotes']  = $this->carsreports_model->getCustomerQuotes($user_id);
        $this->data['total_returns'] = $this->carsreports_model->getCustomerReturns($user_id);
        $this->data['users']         = $this->carsreports_model->getStaff();
        $this->data['warehouses']    = $this->cars_model->getAllShowrooms();
        $this->data['billers']       = $this->site->getAllCompanies('biller');

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('customers_report')]];
        $meta                  = ['page_title' => lang('customers_report'), 'bc' => $bc];
        $this->page_construct('carsreports/customer_report', $meta, $this->data);
    }

    public function products_item_report()
    {
        $this->sma->checkPermissions('products_item_report',true,'sales');
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('Vehicles')], ['link' => '#', 'page' => lang('cars_report')]];
        $meta = ['page_title' => lang('Vehicles_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carssales/products_item', $meta, $this->data);
    }
    public function warehouse_stock($warehouse = null)
    {
        $this->sma->checkPermissions('warehouse_stock',true,'sales');

        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }

        $this->data['stock']        = $warehouse ? $this->reports_model->getWarehouseStockValue($warehouse) : $this->reports_model->getStockValue();
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse;
        $this->data['warehouse']    = $warehouse ? $this->site->getWarehouseByID($warehouse) : null;
        $this->data['totals']       = $this->reports_model->getWarehouseTotals($warehouse);
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('reports')]];
        $meta                       = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carssales/warehouse_stock', $meta, $this->data);

    }
    public function products_report()
    {
       $this->sma->checkPermissions('products_report',true,'carssales');
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('Vehicles')], ['link' => '#', 'page' => lang('Invoice_profit_report')]];
        $meta = ['page_title' => lang('Invoice_profit_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'carssales/reports', null,true);
		$this->page_construct('carssales/products', $meta, $this->data);
    }




/*-----------------------------Offers----------------------------*/
public function offers_index($warehouse_id = null)
{

    $this->sma->checkPermissions('index',true,'offers');

    if(!$this->site->IsPermissions('offers_index')){
         $this->session->set_flashdata('error', lang('access_denied'));
         //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
        //if ($js) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
        //}

    }else{

    $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
    if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();//$this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['warehouse']    = $warehouse_id ? $this->cars_model->getShowroomByID($warehouse_id) : null;
    } else {
        $this->data['warehouses']   = null;

        $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->cars_model->getShowroomByID($this->session->userdata('warehouse_id')) : null;
    }
    //$this->sma->print_arrays($this->data);

    $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('quotes')]];
    $meta = ['page_title' => lang('quotes'), 'bc' => $bc];
    $this->page_construct('carsoffers/index', $meta, $this->data);
    }
}

public function getOffers($warehouse_id = null)
{
  //  $this->sma->checkPermissions('index');

    if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
        $user         = $this->site->getUser();
        //$warehouse_id = $user->warehouse_id;
    }
    $detail_link  = anchor('admin/carssales/view_offer/$1', '<i class="fa fa-file-text-o"></i> ' . lang('quote_details'));
    $email_link   = anchor('admin/carssales/email_offer/$1', '<i class="fa fa-envelope"></i> ' . lang('email_quote'), 'data-toggle="modal" data-target="#myModal"');
    $edit_link    = anchor('admin/carssales/edit_offer/$1', '<i class="fa fa-edit"></i> ' . lang('edit_quote'));
    $convert_link = anchor('admin/carssales/add/$1/offer', '<i class="fa fa-heart"></i> ' . lang('create_sale'));

   // $payments_link     = anchor('admin/carsquotes/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
   // $add_payment_link  = anchor('admin/carsquotes/add_payment/$1', '<i class="fa fa-dollar"></i> ' . lang('Add_advance_payment'), 'data-toggle="modal" data-target="#myModal"');
   // $pc_link      = anchor('admin/purchases/add/$1', '<i class="fa fa-star"></i> ' . lang('create_purchase'));
    //$pdf_link     = anchor('admin/carssales/pdf_offer/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
    $delete_link  = "<a href='#' class='po' title='<b>" . $this->lang->line('delete_quote') . "</b>' data-content=\"<p>"
    . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('carssales/delete_offer/$1') . "'>"
    . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
    . lang('delete_quote') . '</a>';
    $action = '<div class="text-center"><div class="btn-group text-left">'
    . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
    . lang('actions') . ' <span class="caret"></span></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li>' . $detail_link . '</li>
                    <li>' . $edit_link . '</li>
                    <li>' . $convert_link . '</li>
                    <li>' . $email_link . '</li>
                    <li>' . $delete_link . '</li>
                </ul>
            </div></div>';
    //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

    $this->load->library('datatables');
    if ($warehouse_id) {
        $this->datatables
            ->select('id, date, reference_no, biller, customer, supplier, grand_total, status, attachment')
            ->from('carsquotes')
            ->where('warehouse_id', $warehouse_id);
    } else {
        $this->datatables
            ->select('id, date, reference_no, biller, customer, supplier, grand_total, status, attachment')
            ->from('carsquotes');
    }
/*
    $qp = "( SELECT quote_id, SUM(amount) as amount from {$this->db->dbprefix('payments')}  WHERE quote_id is not null group by quote_id ) QuotesPayments";



    if ($warehouse_id) {
        $this->datatables
            ->select('id, date, reference_no, biller, customer, COALESCE( QuotesPayments.amount, 0 ) as amount, grand_total, status, attachment')
            ->from('carsquotes')
            ->where('warehouse_id', $warehouse_id)
            ->join($qp, 'quotes.id = QuotesPayments.quote_id', 'left');

    } else {
        $this->datatables
            ->select('id, date, reference_no, biller, customer, COALESCE( QuotesPayments.amount, 0 ) as amount, grand_total, status, attachment')
            ->from('carsquotes')
            ->join($qp, 'quotes.id = QuotesPayments.quote_id', 'left');

    }*/

    if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
        $this->datatables->where('created_by', $this->session->userdata('user_id'));
    } elseif ($this->Customer) {
        $this->datatables->where('customer_id', $this->session->userdata('user_id'));
    }
    $this->datatables->add_column('Actions', $action, 'id');
    echo $this->datatables->generate();
}
public function offer_actions()
{
    if (!$this->Owner && !$this->GP['bulk_actions']) {
        $this->session->set_flashdata('warning', lang('access_denied'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

    if ($this->form_validation->run() == true) {
        if (!empty($_POST['val'])) {
            if ($this->input->post('form_action') == 'delete') {
                $this->sma->checkPermissions('delete',true,'offers');

                foreach ($_POST['val'] as $id) {
                    $this->carsoffers_model->deleteQuote($id);
                }
                $this->session->set_flashdata('message', $this->lang->line('quotes_deleted'));
                redirect($_SERVER['HTTP_REFERER']);
            } elseif ($this->input->post('form_action') == 'combine') {
     //           $html = $this->combine_pdf($_POST['val']);
            } elseif ($this->input->post('form_action') == 'export_excel' || $pdf) {
                //$this->sma->checkPermissions('xls',true,'offers');

                $this->load->library('excel');

                $this->excel->setActiveSheetIndex(0);
                if ($this->Settings->user_language == 'arabic'){
                    $this->excel->getActiveSheet()->setRightToLeft(true);
                    }
                    $this->excel->getActiveSheet()->setTitle(lang('quotes'));
                    $row = 1;
                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('quotes'));
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
              $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
              //  $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
              //  $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);



            $row++;

            $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
            $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
            $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
            $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

            $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
            $row++;

            $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('date'));
            $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
            $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('biller'));
            $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('customer'));
            $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('supplier'));
            $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('total'));
            $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('status'));


            $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($fillColor);
            $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
             $this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($Font11Bold);
            $row++;
             foreach ($_POST['val'] as $id) {

                            $qu = $this->carsoffers_model->getQuoteByID($id);

                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($qu->date));
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $qu->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $qu->biller);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $qu->customer);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $qu->supplier);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $qu->grand_total);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $qu->status);
                            $row++;
                        }

                        for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                        }
                     //   $this->sma->print_arrays($qu->grand_total);

                      //  $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                       // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                       // $filename = 'quotations_' . date('Y_m_d_H_i_s');
                        $filename = 'quotations';

                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                }

            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_quote_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
public function add_offer()
    {
        $this->sma->checkPermissions('add',true,'offers');

        if(!$this->site->IsPermissions('quotes-add')){
            $this->session->set_flashdata('error', lang('access_denied'));
            //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
           //if ($js) {
               die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
           //}

       }else{


       $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
       $this->form_validation->set_rules('customer', $this->lang->line('customer'), 'required');

       if ($this->form_validation->run() == true) {
           $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('of');
           if ($this->Owner || $this->Admin) {
               $date = $this->sma->fld(trim($this->input->post('date')));
           } else {
               $date = date('Y-m-d H:i:s');
           }
           $warehouse_id     = $this->input->post('warehouse');
           $customer_id      = $this->input->post('customer');
           $biller_id        = $this->input->post('biller');
           $supplier_id      = $this->input->post('supplier');
           $status           = $this->input->post('status');
           $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
           $customer_details = $this->site->getCompanyByID($customer_id);
           $customer         = $customer_details->company && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
           $biller_details   = $this->site->getCompanyByID($biller_id);
           $biller           = $biller_details->company && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
           if ($supplier_id) {
               $supplier_details = $this->site->getCompanyByID($supplier_id);
               $supplier         = $supplier_details->company && $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
           } else {
               $supplier = null;
           }
           //$note = $this->sma->clear_tags($this->input->post('note'));
           $note = $this->sma->clear_tags($_POST['note']);

           $gtotal            = 0;
           $total            = 0;
           $product_tax      = 0;
           $product_discount = 0;
           $gst_data         = [];
           $total_cgst       = $total_sgst       = $total_igst       = 0;
           $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;

           if($this->input->post('discount')>0){
               for ($r = 0; $r < $i; $r++) {
                    $item_code          = $_POST['product_code'][$r];
                    $item_type          = $_POST['product_type'][$r];
               $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;

               if($product_details->tax_method != 1)
                   $gtotal=$gtotal+($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r]);
               else
                   $gtotal=$gtotal+($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r]);

               }
           }
           for ($r = 0; $r < $i; $r++) {
               $item_id            = $_POST['product_id'][$r];
               $item_type          = $_POST['product_type'][$r];
               $item_code          = $_POST['product_code'][$r];
               $item_name          = $_POST['product_name'][$r];
               $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
               $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
               $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);
               $net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);
               $item_unit_quantity = $_POST['quantity'][$r];
               $item_tax_rate      = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
               $item_discount      = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
               $item_unit          = $_POST['product_unit'][$r];
               $item_quantity      = $_POST['product_base_quantity'][$r];

               if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {



                   $product_details = $item_type != 'manual' ? $this->carsoffers_model->getProductByCode($item_code) : null;


                   if($gtotal>0){
                       $order_product_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($gtotal));
                     if($product_details->tax_method != 1)
                       $item_discount=$this->sma->formatDecimal((($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
                   else
                       $item_discount=$this->sma->formatDecimal((($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
                   }

                   // $unit_price = $real_unit_price;
                   //$this->sma->print_arrays($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r]);
                   //$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                   if($product_details->tax_method != 1)
                   $pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                   else
                       $pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);

                   //$this->sma->print_arrays($pr_discount);



                   $unit_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);

                   $item_net_price   = $net_price;


                   $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                   $product_discount += $pr_item_discount;
                   $pr_item_tax = $item_tax = 0;
                   $tax         = '';

                   if (isset($item_tax_rate) && $item_tax_rate != 0) {
                       $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                       if($product_details->tax_method != 1)
                       $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                       else
                       $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                       $item_tax    = $ctax['amount'];
                       $tax         = $ctax['tax'];
                       if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                           $item_net_price = $net_price;
                       }else{
                           //$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
                           $item_net_price=$net_price;
                           //$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                       //$item_tax    = $ctax['amount'];
                       //$tax         = $ctax['tax'];



                       }

                       if($product_details->tax_method != 1)
                       $unit_price       = $this->sma->formatDecimal($real_unit_price - $item_tax,12);

                       //
                       $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                       if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                          // $total_cgst += $gst_data['cgst'];
                           $total_sgst += $gst_data['sgst'];
                           $total_igst += $gst_data['igst'];
                       }
                   }

                   $product_tax += $pr_item_tax;
                   $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                   $unit     = $this->site->getUnitByID($item_unit);

                   $product = [
                       'product_id'        => $item_id,
                       'product_code'      => $item_code,
                       'product_name'      => $item_name,
                       'product_type'      => $item_type,
                       'option_id'         => $item_option,
                       'net_unit_price'    => $item_net_price,
                       'unit_price'        => $this->sma->formatDecimal($unit_price,12),
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
                       'subtotal'          => $this->sma->formatDecimal($subtotal),
                       'real_unit_price'   => $real_unit_price,
                   ];

                   $products[] = ($product + $gst_data);
                   $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 12);
                   $orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);
               }
           }
           if (empty($products)) {
               $this->form_validation->set_rules('product', lang('order_items'), 'required');
           } else {
               krsort($products);
           }

           $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
           if($gtotal>0)
           $order_discount=0;
           $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
           $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
           $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
           if($gtotal>0)
           $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping)), 2);
           else
           $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 2);
           $data           = ['date' => $date,
               'reference_no'        => $reference,
               'customer_id'         => $customer_id,
               'customer'            => $customer,
               'biller_id'           => $biller_id,
               'biller'              => $biller,
               'supplier_id'         => $supplier_id,
               'supplier'            => $supplier,
               'warehouse_id'        => $warehouse_id,
               'note'                => $note,
               'total'               => $orginaltotal,
               'product_discount'    => $product_discount,
               'order_discount_id'   => $this->input->post('discount'),
               'order_discount'      => $order_discount,
               'total_discount'      => $total_discount,
               'product_tax'         => $product_tax,
               'order_tax_id'        => $this->input->post('order_tax'),
               'order_tax'           => $order_tax,
               'total_tax'           => $total_tax,
               'shipping'            => $this->sma->formatDecimal($shipping),
               'grand_total'         => $grand_total,
               'status'              => $status,
               'created_by'          => $this->session->userdata('user_id'),
               'hash'                => hash('sha256', microtime() . mt_rand()),
           ];
           if ($this->Settings->indian_gst) {
              // $data['cgst'] = $total_cgst;
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

            //$this->sma->print_arrays($data, $products,$_POST);
       }

       if ($this->form_validation->run() == true && $this->carsoffers_model->addQuote($data, $products)) {
           $this->session->set_userdata('remove_quls', 1);
           $this->session->set_flashdata('message', $this->lang->line('quote_added'));
           admin_redirect('carssales/offers_index');
       } else {
           $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

           $this->data['categories'] = $this->site->getAllCategories();
           $this->data['base_units']          = $this->site->getAllBaseUnits();
           $this->load->helper('string');
           $value = random_string('alnum', 20);
           $this->session->set_userdata('user_csrf', $value);
           $this->data['csrf'] = $this->session->userdata('user_csrf');
           $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->site->getAllCompanies('biller') : null;
           //$this->data['currencies'] = $this->site->getAllCurrencies();
           $this->data['units']      = $this->site->getAllBaseUnits();
           //$this->data['tax_rates']  = $this->site->getAllTaxRates();

           $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
           $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->cars_model->getAllShowrooms() : null;
           $this->data['qunumber']   = ''; //$this->site->getReference('qu');
           $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('quotes')], ['link' => '#', 'page' => lang('add_quote')]];
           $meta                     = ['page_title' => lang('add_quote'), 'bc' => $bc];
           $this->page_construct('carsoffers/add', $meta, $this->data);
       }
       }
   }


   public function view_offer($quote_id = null)
   {
    $this->sma->checkPermissions('index',true,'offers');

       if ($this->input->get('id')) {
           $quote_id = $this->input->get('id');
       }
       $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       $inv                 = $this->carsoffers_model->getQuoteByID($quote_id);

       if (!$this->session->userdata('view_right')) {
           $this->sma->view_rights($inv->created_by);
       }
       $this->data['rows']       = $this->carsoffers_model->getAllQuoteItems($quote_id);
       $this->data['customer']   = $this->site->getCompanyByID($inv->customer_id);
       $this->data['biller']     = $this->site->getCompanyByID($inv->biller_id);
       $this->data['created_by'] = $this->site->getUser($inv->created_by);
       $this->data['updated_by'] = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
       $this->data['warehouse']  = $this->cars_model->getShowroomByID($inv->warehouse_id);
       $this->data['inv']        = $inv;
//$this->sma->print_arrays($this->data['rows']);


       $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('quotes')], ['link' => '#', 'page' => lang('view')]];
       $meta = ['page_title' => lang('quote_details'), 'bc' => $bc];
       $this->page_construct('carsoffers/view', $meta, $this->data);
   }
   public function email_offer($quote_id = null)
   {
       $this->sma->checkPermissions('email',true,'quotes');

       if ($this->input->get('id')) {
           $quote_id = $this->input->get('id');
       }
       $inv = $this->carsoffers_model->getQuoteByID($quote_id);
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
           $customer = $this->site->getCompanyByID($inv->customer_id);
           $biller   = $this->site->getCompanyByID($inv->biller_id);
           $this->load->library('parser');
           $parse_data = [
               'reference_number' => $inv->reference_no,
               'contact_person'   => $customer->name,
               'company'          => $customer->company,
               'site_link'        => base_url(),
               'site_name'        => $this->Settings->site_name,
               'logo'             => '<img src="' . base_url() . 'assets/uploads/logos/' . $biller->logo . '" alt="' . ($biller->company && $biller->company != '-' ? $biller->company : $biller->name) . '"/>',
           ];
           $msg        = $this->input->post('note');
           $message    = $this->parser->parse_string($msg, $parse_data);
           $attachment = $this->pdf($quote_id, null, 'S');

           try {
               if ($this->sma->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                   delete_files($attachment);
                   $this->db->update('carsquotes', ['status' => 'sent'], ['id' => $quote_id]);
                   $this->session->set_flashdata('message', $this->lang->line('email_sent'));
                   admin_redirect('carssales/offers_index');
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

           if (file_exists('./themes/' . $this->Settings->theme . '/admin/views/email_templates/quote.html')) {
               $quote_temp = file_get_contents('themes/' . $this->Settings->theme . '/admin/views/email_templates/quote.html');
           } else {
               $quote_temp = file_get_contents('./themes/default/admin/views/email_templates/quote.html');
           }

           $this->data['subject'] = ['name' => 'subject',
               'id'                         => 'subject',
               'type'                       => 'text',
               'value'                      => $this->form_validation->set_value('subject', lang('quotes') . ' (' . $inv->reference_no . ') ' . lang('from') . ' ' . $this->Settings->site_name),
           ];
           $this->data['note'] = ['name' => 'note',
               'id'                      => 'note',
               'type'                    => 'text',
               'value'                   => $this->form_validation->set_value('note', $quote_temp),
           ];
           $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);

           $this->data['id']       = $quote_id;
           $this->data['modal_js'] = $this->site->modal_js();
           $this->load->view($this->theme . 'carsoffers/email', $this->data);
       }
   }
   public function edit_offer($id = null)
   {

       $this->sma->checkPermissions('edit',true,'quotes');
       if(!$this->site->IsPermissions('quotes-edit')){
            $this->session->set_flashdata('error', lang('access_denied'));
            //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
           //if ($js) {
               die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
           //}

       }else{


       if ($this->input->get('id')) {
           $id = $this->input->get('id');
       }
    /* $inv = $this->carsoffers_model->getQuoteByID($id);
       if ($inv->status == 'completed' || $inv->status == 'canceled') {
           $this->session->set_flashdata('error', lang('quotes_denied'));
                       redirect($_SERVER['HTTP_REFERER']);
       }
       */
       if (!$this->session->userdata('edit_right')) {
           $this->sma->view_rights($inv->created_by);
       }
       $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
       $this->form_validation->set_rules('reference_no', $this->lang->line('reference_no'), 'required');
       $this->form_validation->set_rules('customer', $this->lang->line('customer'), 'required');
       //$this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');

       if ($this->form_validation->run() == true) {


           $reference = $this->input->post('reference_no');
           if ($this->Owner || $this->Admin) {
               $date = $this->sma->fld(trim($this->input->post('date')));
           } else {
               $date = date('Y-m-d H:i:s');
           }
           $warehouse_id     = $this->input->post('warehouse');
           $customer_id      = $this->input->post('customer');
           $biller_id        = $this->input->post('biller');
           $supplier_id      = $this->input->post('supplier');
           $status           = $this->input->post('status');
           $shipping         = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
           $customer_details = $this->site->getCompanyByID($customer_id);
           $customer         = $customer_details->company && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
           $biller_details   = $this->site->getCompanyByID($biller_id);
           $biller           = $biller_details->company && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
           if ($supplier_id) {
               $supplier_details = $this->site->getCompanyByID($supplier_id);
               $supplier         = $supplier_details->company && $supplier_details->company != '-' ? $supplier_details->company : $supplier_details->name;
           } else {
               $supplier = null;
           }
           //$this->sma->print_arrays($this->input->post('note'));
           $note = $this->sma->clear_tags($_POST['note']);
           //$note = ($this->input->post('note'));
           //$this->sma->print_arrays(str_replace("cpesoft_","",$this->db->database));
           //$note =str_replace("%", "<strong>%</strong>", $this->input->post('note'));
           //$this->sma->print_arrays(str_replace("%", "<strong>%</strong> ", $this->input->post('note')));
           //$json = file_get_contents('php://input');
           //$sales =json_decode($json);

           //$this->sma->print_arrays($note);


           $gtotal            = 0;
           $total            = 0;
           $product_tax      = 0;
           $product_discount = 0;
           $gst_data         = [];
           $total_cgst       = $total_sgst       = $total_igst       = 0;
           $i                = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;

           if($this->input->post('discount')>0){
               for ($r = 0; $r < $i; $r++) {
                    $item_code          = $_POST['product_code'][$r];
                    $item_type          = $_POST['product_type'][$r];
                    $product_details = $item_type != 'manual' ? $this->cars_model->getVehiclesByChesis($item_code) : null;

               if($product_details->tax_method != 1)
                   $gtotal=$gtotal+($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r]);
               else
                   $gtotal=$gtotal+($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r]);

               }
           }
           for ($r = 0; $r < $i; $r++) {
               $item_id            = $_POST['product_id'][$r];
               $item_type          = $_POST['product_type'][$r];
               $item_code          = $_POST['product_code'][$r];
               $item_name          = $_POST['product_name'][$r];
               $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' ? $_POST['product_option'][$r] : null;
               $real_unit_price    = $this->sma->formatDecimal($_POST['real_unit_price'][$r],12);
               $unit_price         = $this->sma->formatDecimal($_POST['unit_price'][$r],12);
               $net_price         = $this->sma->formatDecimal($_POST['net_price'][$r],12);
               $item_unit_quantity = $_POST['quantity'][$r];
               $item_tax_rate      = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
               $item_discount      = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
               $item_unit          = $_POST['product_unit'][$r];
               $item_quantity      = $_POST['product_base_quantity'][$r];

               if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {



                   $product_details = $item_type != 'manual' ? $this->carsoffers_model->getProductByCode($item_code) : null;


                   if($gtotal>0){
                       $order_product_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($gtotal));
                     if($product_details->tax_method != 1)
                       $item_discount=$this->sma->formatDecimal((($_POST['unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
                   else
                       $item_discount=$this->sma->formatDecimal((($_POST['real_unit_price'][$r]*$_POST['product_base_quantity'][$r])/$gtotal*$order_product_discount)/$_POST['product_base_quantity'][$r],12);
                   }

                   // $unit_price = $real_unit_price;
                   //$this->sma->print_arrays($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r]);
                   //$pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                   if($product_details->tax_method != 1)
                   $pr_discount      = $this->site->calculateDiscount($item_discount, $unit_price);
                   else
                       $pr_discount      = $this->sma->formatDecimal($_POST['real_unit_price'][$r]-$_POST['unit_price'][$r],12);

                   //$this->sma->print_arrays($pr_discount);



                   $unit_price       = $this->sma->formatDecimal($real_unit_price - $pr_discount,12);

                   $item_net_price   = $net_price;


                   $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity,12);
                   $product_discount += $pr_item_discount;
                   $pr_item_tax = $item_tax = 0;
                   $tax         = '';

                   if (isset($item_tax_rate) && $item_tax_rate != 0) {
                       $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                       if($product_details->tax_method != 1)
                       $ctax        = $this->site->calculateTax($product_details, $tax_details, $net_price);
                       else
                       $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                       $item_tax    = $ctax['amount'];
                       $tax         = $ctax['tax'];
                       if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                           $item_net_price = $net_price;
                       }else{
                           //$unit_price=$this->sma->formatDecimal($unit_price+$pr_discount,12);
                           $item_net_price=$net_price;
                           //$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                       //$item_tax    = $ctax['amount'];
                       //$tax         = $ctax['tax'];



                       }
                       if($product_details->tax_method != 1)
                       $unit_price       = $this->sma->formatDecimal($real_unit_price - $item_tax,12);
                       //
                       $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 12);
                       if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                           //$total_cgst += $gst_data['cgst'];
                           $total_sgst += $gst_data['sgst'];
                           $total_igst += $gst_data['igst'];
                       }
                   }

                   $product_tax += $pr_item_tax;
                   $subtotal = $this->sma->formatDecimal((($item_net_price * $item_unit_quantity) + $pr_item_tax),12);
                   $unit     = $this->site->getUnitByID($item_unit);

                   $product = [
                       'product_id'        => $item_id,
                       'product_code'      => $item_code,
                       'product_name'      => $item_name,
                       'product_type'      => $item_type,
                       'option_id'         => $item_option,
                       'net_unit_price'    => $item_net_price,
                       'unit_price'        => $this->sma->formatDecimal($unit_price,12),
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
                       'subtotal'          => $this->sma->formatDecimal($subtotal),
                       'real_unit_price'   => $real_unit_price,
                   ];

                   $products[] = ($product + $gst_data);
                   $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 12);
                   $orginaltotal += $this->sma->formatDecimal((($item_net_price+ ($pr_item_discount/$item_unit_quantity) )* $item_unit_quantity), 12);
               }
           }
           if (empty($products)) {
               $this->form_validation->set_rules('product', lang('order_items'), 'required');
           } else {
               krsort($products);
           }

           $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
           if($gtotal>0)
           $order_discount=0;
           $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
           $order_tax      = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
           $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
           if($gtotal>0)
           $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping)), 2);
           else
           $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 2);
           $data           = ['date' => $date,
               'reference_no'        => $reference,
               'customer_id'         => $customer_id,
               'customer'            => $customer,
               'biller_id'           => $biller_id,
               'biller'              => $biller,
               'supplier_id'         => $supplier_id,
               'supplier'            => $supplier,
               'warehouse_id'        => $warehouse_id,
               'note'                => $note,
               'total'               => $orginaltotal,
               'product_discount'    => $product_discount,
               'order_discount_id'   => $this->input->post('discount'),
               'order_discount'      => $order_discount,
               'total_discount'      => $total_discount,
               'product_tax'         => $product_tax,
               'order_tax_id'        => $this->input->post('order_tax'),
               'order_tax'           => $order_tax,
               'total_tax'           => $total_tax,
               'shipping'            => $shipping,
               'grand_total'         => $grand_total,
               'status'              => $status,
               'updated_by'          => $this->session->userdata('user_id'),
               'updated_at'          => date('Y-m-d H:i:s'),
           ];
           if ($this->Settings->indian_gst) {
               //$data['cgst'] = $total_cgst;
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

            //$this->sma->print_arrays($data, $products);
       }

       if ($this->form_validation->run() == true && $this->carsoffers_model->updateQuote($id, $data, $products)) {
           $this->session->set_userdata('remove_quls', 1);
           $this->session->set_flashdata('message', $this->lang->line('quotes_updated'));
           admin_redirect('carssales/offers_index');
       } else {
           $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

           $this->data['inv'] = $this->carsoffers_model->getQuoteByID($id);
           $inv_items         = $this->carsoffers_model->getAllQuoteItems($id);
           // krsort($inv_items);
           $c = rand(100000, 9999999);
           foreach ($inv_items as $item) {
               $row = $this->site->getProductByID($item->product_id);
               if (!$row) {
                   $row             = json_decode('{}');
                   $row->tax_method = 0;
               } else {
                   unset($row->details, $row->product_details, $row->cost, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price);
               }
               $row->quantity = 0;
               $pis           = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
               if ($pis) {
                   foreach ($pis as $pi) {
                       $row->quantity += $pi->quantity_balance;
                   }
               }
               $row->id              = $item->product_id;
               $row->code            = $item->product_code;
               $row->name            = $item->product_name;
               $row->type            = $item->product_type;
               $row->base_quantity   = $item->quantity;
               $row->base_unit       = isset($row->unit) ? $row->unit : $item->product_unit_id;
               $row->base_unit_price = isset($row->price) ? $row->price : $item->unit_price;
               $row->unit            = $item->product_unit_id;
               $row->qty             = $item->unit_quantity;
               $row->discount        = $item->discount ? $item->discount : '0';
               $row->item_tax        = $item->item_tax      > 0 ? $item->item_tax           / $item->quantity : 0;
               $row->item_discount   = $item->item_discount > 0 ? $item->item_discount / $item->quantity : 0;
               $row->unit_price      = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($row->item_discount) + $this->sma->formatDecimal($row->item_tax) : $item->unit_price + ($row->item_discount);
               $row->real_unit_price = $item->real_unit_price;
               $row->tax_rate        = $item->tax_rate_id;
               $row->option          = $item->option_id;
               $options              = $this->carsoffers_model->getProductOptions($row->id, $item->warehouse_id);

               if ($options) {
                   $option_quantity = 0;
                   foreach ($options as $option) {
                       $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                       if ($pis) {
                           foreach ($pis as $pi) {
                               $option_quantity += $pi->quantity_balance;
                           }
                       }
                       if ($option->quantity > $option_quantity) {
                           $option->quantity = $option_quantity;
                       }
                   }
               }

               $combo_items = false;
               if ($row->type == 'combo') {
                   $combo_items = $this->carsoffers_model->getProductComboItems($row->id, $item->warehouse_id);
                   foreach ($combo_items as $combo_item) {
                       $combo_item->quantity = $combo_item->qty * $item->quantity;
                   }
               }
               $units    = $this->site->getUnitsByBUID($row->base_unit);
               $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
               $ri       = $this->Settings->item_addition ? $row->id : $c;

               $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];
               $c++;
           }
           $this->data['inv_items'] = json_encode($pr);
           $this->data['id']        = $id;
           //$this->data['currencies'] = $this->site->getAllCurrencies();
           $this->data['billers']    = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->site->getAllCompanies('biller') : null;
           $this->data['units']      = $this->site->getAllBaseUnits();
           //$this->data['tax_rates']  = $this->site->getAllTaxRates();
           $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
           $this->data['warehouses'] = ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) ? $this->cars_model->getAllShowrooms() : null;

           $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('quotes')], ['link' => '#', 'page' => lang('edit_quote')]];
           $meta = ['page_title' => lang('edit_quote'), 'bc' => $bc];
           $this->page_construct('carsoffers/edit', $meta, $this->data);
       }
       }
   }
   public function pdf_offer($quote_id = null, $view = null, $save_bufffer = null)
   {
    $this->sma->checkPermissions('pdf',true,'offers');

       if ($this->input->get('id')) {
           $quote_id = $this->input->get('id');
       }
       $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       $inv                 = $this->carsoffers_model->getQuoteByID($quote_id);
       if (!$this->session->userdata('view_right')) {
           $this->sma->view_rights($inv->created_by);
       }
       $this->data['rows']       = $this->carsoffers_model->getAllQuoteItems($quote_id);
       $this->data['customer']   = $this->site->getCompanyByID($inv->customer_id);
       $this->data['biller']     = $this->site->getCompanyByID($inv->biller_id);
       $this->data['created_by'] = $this->site->getUser($inv->created_by);
       $this->data['warehouse']  = $this->site->getWarehouseByID($inv->warehouse_id);
       $this->data['inv']        = $inv;
       $name                     = $this->lang->line('quote') . '_' . str_replace('/', '_', $inv->reference_no) . '.pdf';
       $html                     = $this->load->view($this->theme . 'carsoffers/pdf', $this->data, true);
       if (!$this->Settings->barcode_img) {
           $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
       }
       if ($view) {
           $this->load->view($this->theme . 'carsoffers/pdf', $this->data);
       } elseif ($save_bufffer) {
           return $this->sma->generate_pdf($html, $name, $save_bufffer);
       } else {
           $this->sma->generate_pdf($html, $name);
       }
   }
   public function delete_offer($id = null)
   {
    $this->sma->checkPermissions('delete',true,'offers');
       if(!$this->site->IsPermissions('quotes-delete')){
            //$this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
           //if ($js) {
            //   die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
           //}

       }else{
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }


     /*  $inv = $this->carsoffers_model->getQuoteByID($id);
       if ($inv->status == 'completed' || $inv->status == 'canceled') {
                       $this->sma->send_json(['error' => 1, 'msg' => lang('Delete_denied')]);

       }*/

       if ($this->carsoffers_model->deleteQuote($id)) {

           if ($this->input->is_ajax_request()) {
               $this->sma->send_json(['error' => 0, 'msg' => lang('quote_deleted')]);
           }
           $this->session->set_flashdata('message', lang('quote_deleted'));
           admin_redirect('welcome');
       }
       }
   }
   public function suggestions_offer()
   {
       $term         = $this->input->get('term', true);
       $warehouse_id = $this->input->get('warehouse_id', true);
       $customer_id  = $this->input->get('customer_id', true);

       if (strlen($term) < 1 || !$term) {
           die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
       }

       $analyzed       = $this->sma->analyze_term($term);
       $sr             = $analyzed['term'];
       $option_id      = $analyzed['option_id'];
       $warehouse      = $this->site->getWarehouseByID($warehouse_id);
       $customer       = $this->site->getCompanyByID($customer_id);
       $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
       $rows           = $this->carssales_model->getProductNames($sr, $warehouse_id);
       if ($rows) {
           $r = 0;
           foreach ($rows as $row) {
               $c = uniqid(mt_rand(), true);
               unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
               $option               = false;
               $row->quantity        = 0;
               $row->item_tax_method = $row->tax_method;
               $row->qty             = 1;
               $row->discount        = '0';
               $options              = $this->carssales_model->getProductOptions($row->id, $warehouse_id);
               if ($options) {
                   $opt = $option_id && $r == 0 ? $this->carssales_model->getProductOptionByID($option_id) : $options[0];
                   if (!$option_id || $r > 0) {
                       $option_id = $opt->id;
                   }
               } else {
                   $opt        = json_decode('{}');
                   $opt->price = 0;
                   $option_id  = false;
               }
               $row->option = $option_id;
               $pis         = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
               if ($pis) {
                   foreach ($pis as $pi) {
                       $row->quantity += $pi->quantity_balance;
                   }
               }
               if ($options) {
                   $option_quantity = 0;
                   foreach ($options as $option) {
                       $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                       if ($pis) {
                           foreach ($pis as $pi) {
                               $option_quantity += $pi->quantity_balance;
                           }
                       }
                       if ($option->quantity > $option_quantity) {
                           $option->quantity = $option_quantity;
                       }
                   }
               }
               if ($row->promotion) {
                   $row->price = $row->promo_price;
               } elseif ($customer->price_group_id) {
                   if ($pr_group_price = $this->site->getProductGroupPrice($row->id, $customer->price_group_id)) {
                       $row->price = $pr_group_price->price;
                   }
               } elseif ($warehouse->price_group_id) {
                   if ($pr_group_price = $this->site->getProductGroupPrice($row->id, $warehouse->price_group_id)) {
                       $row->price = $pr_group_price->price;
                   }
               }
               $row->price           = $row->price + (($row->price * $customer_group->percent) / 100);
               $row->real_unit_price = $row->price;
               $row->base_quantity   = 1;
               $row->base_unit       = $row->unit;
               $row->base_unit_price = $row->price;
               $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
               $combo_items          = false;
               if ($row->type == 'combo') {
                   $combo_items = $this->carssales_model->getProductComboItems($row->id, $warehouse_id);
               }
               $units    = $this->site->getUnitsByBUID($row->base_unit);
               $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

               $pr[] = ['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id,
                   'row'     => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
               $r++;
           }
           $this->sma->send_json($pr);
       } else {
           $this->sma->send_json([['id' => 0, 'label' => lang('No_match_found'), 'value' => $term]]);
       }
   }


   public function getProductsReport($pdf = null, $xls = null)
   {
        //      $this->sma->checkPermissions('products_report',true,'carssales');

       if(!$this->site->IsPermissions('products-reports')){
            $this->session->set_flashdata('error', lang('access_denied'));
           //if ($js) {
               die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
           //}

       }else{

       $reference_no     = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
       $category    = $this->input->get('category') ? $this->input->get('category') : null;
       $brand       = $this->input->get('brand') ? $this->input->get('brand') : null;
       $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : null;
       $warehouse   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
       $product_type   = $this->input->get('product_type') ? $this->input->get('product_type') : null;
       $sale_status   = $this->input->get('sale_status') ? $this->input->get('sale_status') : null;
       $customer   = $this->input->get('customer') ? $this->input->get('customer') : null;
       //$this->sma->print_arrays($product_type);


       //$type   = "combo";
       $cf1         = $this->input->get('cf1') ? $this->input->get('cf1') : null;
       $cf2         = $this->input->get('cf2') ? $this->input->get('cf2') : null;
       $cf3         = $this->input->get('cf3') ? $this->input->get('cf3') : null;
       $cf4         = $this->input->get('cf4') ? $this->input->get('cf4') : null;
       $cf5         = $this->input->get('cf5') ? $this->input->get('cf5') : null;
       $cf6         = $this->input->get('cf6') ? $this->input->get('cf6') : null;
       $start_date  = $this->input->get('start_date') ? $this->input->get('start_date') : null;
       $end_date    = $this->input->get('end_date') ? $this->input->get('end_date') : null;

       if ($start_date && $end_date) {
           $start_date = $this->sma->fld($start_date);
           $end_date = $this->sma->fld($end_date);
          }

       //$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
       $pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
       //$this->sma->print_arrays($pp);
       // WHERE p.status != 'pending' AND p.status != 'ordered'
       $sp = '( SELECT si.sale_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price) totalSale, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id';


       //$ex1 = '( SELECT s.sale_id,c.vehicle_id,sum(CASE WHEN e.vehicle_id IS NOT NULL THEN e.total ELSE 0 END ) as expTotalCare,sum(CASE WHEN e.sale_id IS NOT NULL THEN e.total ELSE 0 END ) as expTotalSales from ' . $this->db->dbprefix('cars_vehicles') . ' c JOIN ' . $this->db->dbprefix('sale_items') . ' s JOIN ' . $this->db->dbprefix('expenses') . ' e on (c.vehicle_id=e.vehicle_id or s.sale_id=e.sale_id) and s.product_id=c.vehicle_id ';
       $ex1 = '( SELECT s.sale_id,c.vehicle_id,sum(CASE WHEN e.vehicle_id IS NOT NULL THEN e.total ELSE 0 END ) as expTotalCare from ' . $this->db->dbprefix('cars_vehicles') . ' c JOIN ' . $this->db->dbprefix('sale_items') . ' s JOIN ' . $this->db->dbprefix('expenses') . ' e on (c.vehicle_id=e.vehicle_id or s.sale_id=e.sale_id) and s.product_id=c.vehicle_id ';
       $ex = '( SELECT sale_id,sum(CASE WHEN sale_id IS NOT NULL THEN total ELSE 0 END ) as expTotalSales from  ' . $this->db->dbprefix('expenses') . ' ';


       $wp = '( SELECT wp.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp ';
       //$wpcombo = '( SELECT wpcombo.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp JOIN ' . $this->db->dbprefix('combo_items') . '  wpcombo on wpcombo.item_id = wp.product_id ';
       $wpo = '( SELECT wpo.product_id, SUM(wpo.avg_cost) balacneValue, SUM( wpo.quantity ) balacneQty from ' . $this->db->dbprefix('warehouses_products') . ' wpo ';
       if ($start_date || $warehouse) {
           $sp .= ' WHERE ';
           if ($start_date) {
               //$start_date = $this->sma->fld($start_date);
               //$end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
               $pp .= " AND p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
               $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
               if ($warehouse) {
                   $sp .= ' AND ';
               }
           }


           if ($warehouse) {
               $pp .= " AND pi.warehouse_id = '{$warehouse}' ";
               $sp .= " si.warehouse_id = '{$warehouse}' ";
               $wp .= " WHERE wp.warehouse_id = '{$warehouse}' ";
               //$wpcombo .= " WHERE wp.warehouse_id = '{$warehouse}' ";
               $wpo .= " WHERE wpo.warehouse_id = '{$warehouse}' ";

           }
       }

       $sp .= " and si.product_type = 'standard' ";

       $pp .= ' GROUP BY pi.product_id ) PCosts';
       $sp .= ' GROUP BY si.sale_id ) PSales';
       $ex1 .= ' GROUP BY s.sale_id,c.vehicle_id ) EXP1';
       $ex .= ' GROUP BY sale_id ) EXP';
       $wp .= ' GROUP BY wp.product_id ) WarehousesProducts';
       //$wpcombo .= ' GROUP BY wpcombo.product_id ) ComboItems';
       $wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';

       //sum(CASE WHEN e.vehicle_id IS NOT NULL THEN e.total ELSE 0 END ) as expTotalCare
       $si = "( SELECT sale_id,product_id, GROUP_CONCAT(CONCAT({$this->db->dbprefix('sale_items')}.product_name, '__', {$this->db->dbprefix('sale_items')}.quantity) SEPARATOR '___') as item_nane,{$this->db->dbprefix('sale_items')}.net_unit_price, GROUP_CONCAT(CONCAT({$this->db->dbprefix('sale_items')}.net_unit_price, '__', {$this->db->dbprefix('sale_items')}.quantity) SEPARATOR '___') as item_price from {$this->db->dbprefix('sale_items')}  ";
            //{ $si .= " WHERE product_type = 'standard'"; }if ($start_date && $end_date) {
            if ($product || $serial) { $si .= " WHERE "; }

           if ($product) {
               $si .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
           }
           if ($customer) {
               $this->db->where($this->db->dbprefix('sales') . '.customer_id', $customer);

           }
           if ($product && $serial) { $si .= " AND "; }
           $si .= " GROUP BY {$this->db->dbprefix('sale_items')}.sale_id ) FSI";


           //$ex1 = '( SELECT s.sale_id,c.vehicle_id,sum(CASE WHEN e.vehicle_id IS NOT NULL THEN e.total ELSE 0 END ) as expTotalCare from ' . $this->db->dbprefix('cars_vehicles') . ' c JOIN ' . $this->db->dbprefix('sale_items') . ' s JOIN ' . $this->db->dbprefix('expenses') . ' e on (c.vehicle_id=e.vehicle_id or s.sale_id=e.sale_id) and s.product_id=c.vehicle_id ';

           $ce = "( SELECT s.sale_id,GROUP_CONCAT(CONCAT((CASE WHEN e.vehicle_id IS NOT NULL THEN e.total ELSE 0 END ), '__', e.vehicle_id) SEPARATOR '___') as carexp from {$this->db->dbprefix('cars_vehicles')}  c JOIN {$this->db->dbprefix('sale_items')} s JOIN {$this->db->dbprefix('expenses')} e on (c.vehicle_id=e.vehicle_id) and s.product_id=c.vehicle_id ";
            //{ $si .= " WHERE product_type = 'standard'"; }
           /* if ($product || $serial) { $ce .= " WHERE "; }

           if ($product) {
               $ce .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
           }
           if ($product && $serial) { $ce .= " AND "; }*/
           $ce .= " GROUP BY s.sale_id) CarE";


           $pu = "( SELECT s.sale_id,GROUP_CONCAT(CONCAT((CASE WHEN p.quantity_received IS NOT NULL THEN (p.unit_cost*p.quantity_received) ELSE 0 END ), '__', c.vehicle_id) SEPARATOR '___') as carcost from {$this->db->dbprefix('cars_vehicles')}  c JOIN {$this->db->dbprefix('sale_items')} s JOIN {$this->db->dbprefix('purchase_items')} p on (c.vehicle_id=p.product_id) and s.product_id=c.vehicle_id and s.product_id=p.product_id ";
            //{ $si .= " WHERE product_type = 'standard'"; }
           /* if ($product || $serial) { $ce .= " WHERE "; }

           if ($product) {
               $ce .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
           }
           if ($product && $serial) { $ce .= " AND "; }*/
           $pu .= " GROUP BY s.sale_id) Pur";

           $siMinus = "( SELECT sale_id,product_id,{$this->db->dbprefix('sale_items')}.net_unit_price, GROUP_CONCAT(CONCAT(((-1)*{$this->db->dbprefix('sale_items')}.net_unit_price), '__', {$this->db->dbprefix('sale_items')}.quantity) SEPARATOR '___') as item_price from {$this->db->dbprefix('sale_items')}  ";
           //{ $si .= " WHERE product_type = 'standard'"; }
           if ($product || $serial) { $siMinus .= " WHERE "; }

          if ($product) {
              $siMinus .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
          }
          if ($product && $serial) { $siMinus .= " AND "; }
          $siMinus .= " GROUP BY {$this->db->dbprefix('sale_items')}.sale_id ) FSIMinus";


       //$this->sma->print_arrays($pu);


       if ($pdf || $xls) {
           /*$this->db
              ->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . ".name,
               COALESCE( PCosts.purchasedQty, 0 ) as purchase_quantity,
               COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
               COALESCE( PSales.soldQty, 0 ) as sales_quantity,
               COALESCE( PSales.totalSale, 0 ) as sold,
               (COALESCE( PSales.totalGoodsCost, 0 ) ) as totalGoodsCost,
               (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  )) as Profit,
               COALESCE( WarehousesProductsOrignal.balacneQty, 0 ) as balance_qty,
               COALESCE( WarehousesProductsOrignal.balacneValue, 0 ) as balance,
               {$this->db->dbprefix('products')}.id as id", false)
               ->from('products')
               ->join($sp, 'products.id = PSales.product_id', 'left')
               ->join($pp, 'products.id = PCosts.product_id', 'left')
               ->join($wp, 'products.id = WarehousesProducts.product_id', 'left')
               //->join($wpcombo, 'products.id = ComboItems.product_id', 'left')
               ->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left')
               ->group_by('products.code');

           if ($product) {
               $this->db->where($this->db->dbprefix('products') . '.id', $product);
           }
           if ($cf1) {
               $this->db->where($this->db->dbprefix('products') . '.cf1', $cf1);
           }
           if ($cf2) {
               $this->db->where($this->db->dbprefix('products') . '.cf2', $cf2);
           }
           if ($cf3) {
               $this->db->where($this->db->dbprefix('products') . '.cf3', $cf3);
           }
           if ($cf4) {
               $this->db->where($this->db->dbprefix('products') . '.cf4', $cf4);
           }
           if ($cf5) {
               $this->db->where($this->db->dbprefix('products') . '.cf5', $cf5);
           }
           if ($cf6) {
               $this->db->where($this->db->dbprefix('products') . '.cf6', $cf6);
           }
           if ($category) {
               $this->db->where($this->db->dbprefix('products') . '.category_id', $category);
           }
           if ($subcategory) {
               $this->db->where($this->db->dbprefix('products') . '.subcategory_id', $subcategory);
           }
           if ($product_type) {
               $this->db->where($this->db->dbprefix('products') . '.type', $product_type);
           }
           if ($brand) {
               $this->db->where($this->db->dbprefix('products') . '.brand', $brand);
           }
           */

           $this->db
               ->select($this->db->dbprefix('sales') . '.id as id1, FSI.item_nane as iname,
               COALESCE( Pur.carcost, 0 ) as carcost,
               COALESCE( CarE.carexp, 0 ) as carexp,
               COALESCE( EXP.expTotalSales, 0 ) as expTotalSales1,
               CONCAT(COALESCE(Pur.carcost,0), "___", COALESCE(CarE.carexp,0), "___", COALESCE(EXP.expTotalSales,0)) as totalcost,
               FSI.item_price as item_price,
               FSI.item_price as totalprice,
               CONCAT(COALESCE(Pur.carcost,0), "___", COALESCE(CarE.carexp,0), "___", COALESCE(EXP.expTotalSales,0), "___", COALESCE(FSIMinus.item_price,0)) as prooft,
               sales.id as id', false)
               ->from('sales')
               ->join($sp, 'sales.id = PSales.sale_id', 'left')
               ->join($si, 'FSI.sale_id=sales.id', 'left')
               ->join($ex, 'sales.id = EXP.sale_id', 'left')
               ->join($ce, 'sales.id = CarE.sale_id', 'left')
               ->join($pu, 'sales.id = Pur.sale_id', 'left')
               ->join($siMinus, 'FSIMinus.sale_id=sales.id', 'left');


               //$this->db->where(" (FSI.product_id=CarE.vehicle_id) ");

              // ->join($ex, 'sale_items.product_id = EXP.vehicle_id', 'left')
              //->join($wpo, 'sale_items.product_id = WarehousesProductsOrignal.product_id', 'left')
               //->group_by('sale_items.product_id');

               if ($start_date && $end_date) {
                       $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
                    }


                    if ($reference_no) {
                       $this->db->where($this->db->dbprefix('sales') . '.reference_no', $reference_no);
                   }
                   if ($warehouse) {
                       $this->db->where($this->db->dbprefix('sales') . '.warehouse_id', $warehouse);
                   }
                   if ($sale_status) {
                       $this->db->where($this->db->dbprefix('sales') . '.sale_status', $sale_status);
                   }
                   if ($customer) {
                       $this->db->where($this->db->dbprefix('sales') . '.customer_id', $customer);
                   }

                   if ($product) {
                       $this->db->where($this->db->dbprefix('products') . '.id', $product);
                   }
                   if ($cf1) {
               $this->db->where($this->db->dbprefix('products') . '.cf1', $cf1);
           }
           if ($cf2) {
               $this->db->where($this->db->dbprefix('products') . '.cf2', $cf2);
           }
           if ($cf3) {
               $this->db->where($this->db->dbprefix('products') . '.cf3', $cf3);
           }
           if ($cf4) {
               $this->db->where($this->db->dbprefix('products') . '.cf4', $cf4);
           }
           if ($cf5) {
               $this->db->where($this->db->dbprefix('products') . '.cf5', $cf5);
           }
           if ($cf6) {
               $this->db->where($this->db->dbprefix('products') . '.cf6', $cf6);
           }
           if ($category) {
               $this->db->where($this->db->dbprefix('products') . '.category_id', $category);
           }
           if ($subcategory) {
               $this->db->where($this->db->dbprefix('products') . '.subcategory_id', $subcategory);
           }
           if ($product_type) {
               $this->db->where($this->db->dbprefix('products') . '.type', $product_type);
           }
           if ($brand) {
               $this->db->where($this->db->dbprefix('products') . '.brand', $brand);
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
            $this->excel->getActiveSheet()->setTitle(lang('Invoice_profit_report'));
             $row = 1;
                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Invoice_profit_report'));
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

                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                    $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                    $row++;
                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);

                    $this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') .' '. lang('Invoice_profit_report') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                    $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A3')->applyFromArray($Font11Bold);

                            $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
                            $row++;
            $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Invoice_No'));
            $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('product_name') .' - '.lang('Service'));
            $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('cost'));
            $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('Car_expenses'));
            $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('Invoice_expenses'));
            $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('purchased_total'));
            $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('price'));
            $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('sold_total'));
            $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('profit_loss'));
                                 $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($fillColor);
                    $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                     $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($Font11Bold);


            $row ++;
            $carcost = 0;
            $carexp = 0;
            $expTotalSales1 = 0;
            $totalcost = 0;
            $item_price = 0;
            $totalprice = 0;
            $prooft   = 0;
            foreach ($data as $data_row) {


                $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->id1);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->pqFormatFWS($data_row->iname));
                $this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setWrapText(true);

                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->pqFormatFWSPriceSplit($data_row->carcost));
                $this->excel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setWrapText(true);

                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->pqFormatFWSPriceSplit($data_row->carexp));
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->expTotalSales1);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row,  $this->pqFormatFWSTotalcost($data_row->totalcost));
                $this->excel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($Font11Bold);

                $this->excel->getActiveSheet()->SetCellValue('G' . $row,  $this->pqFormatFWSPrice($data_row->item_price));

                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->pqFormatFWSTotalPrice($data_row->totalprice));
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->pqFormatFWSTotalPriceMinus($data_row->prooft));
                $this->excel->getActiveSheet()->getStyle('H'.$row.':I'.$row)->applyFromArray($Font11Bold);

                $carcost += $data_row->carcost;
                $carexp += $data_row->carexp;
                $expTotalSales1 += $data_row->expTotalSales1;
                $totalcost += $data_row->totalcost;
                $item_price += $data_row->item_price;
                $totalprice += $data_row->totalprice;
                $prooft  += $data_row->prooft;
                $row++;
            }
          //  $this->excel->getActiveSheet()->mergeCells('A'.$row.':B'.$row);
                    $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang("Total")  );

                    $this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setSize(16);

            $this->excel->getActiveSheet()->getStyle('C' . $row . ':I' . $row)->getBorders()
                ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $carcost);
            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $carexp);
            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $expTotalSales1);
            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $totalcost);
            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $item_price);
            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $totalprice);
            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $prooft);
            $this->excel->getActiveSheet()->getStyle('C' . $row . ':I' . $row)->getFont()->setBold(true);


            $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
            $filename = 'Invoice_profit_report';
            $this->load->helper('excel');
            create_excel($this->excel, $filename);
        }
        $this->session->set_flashdata('error', lang('nothing_found'));
        redirect($_SERVER['HTTP_REFERER']);
    } else {

           //COALESCE( WarehousesProductsOrignal.balacneQty, 0 ) as balance_qty,
           //COALESCE( WarehousesProductsOrignal.balacneValue, 0 ) as balance,
           //COALESCE( PCosts.purchasedQty, 0 ) as purchase_quantity,
               /*
               COALESCE( WarehousesProductsOrignal.balacneValue, 0 ) as balance,
               COALESCE( EXP.expTotalCare, 0 ) as expTotalCare1,
               COALESCE( EXP.expTotalSales, 0 ) as expTotalSales1,
               ((COALESCE( WarehousesProductsOrignal.balacneValue, 0 )  ) + (COALESCE( EXP.expTotalCare, 0 )  ) + (COALESCE( EXP.expTotalSales, 0 )  )) as Totalcost,
               COALESCE( PSales.totalSale, 0 ) as sold,
               COALESCE( PSales.totalSale, 0 ) as sold2,
               COALESCE( PSales.totalSale, 0 ) as sold3,
              (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  ) - (COALESCE( EXP.expTotalCare, 0 )  ) - (COALESCE( EXP.expTotalSales, 0 )  )) as Profit,
               */

               //$sep='';
           $this->load->library('datatables');
           $this->datatables
               ->select($this->db->dbprefix('sales') . '.id as id1, FSI.item_nane as iname,
               COALESCE( Pur.carcost, 0 ) as carcost,
               COALESCE( CarE.carexp, 0 ) as carexp,
               COALESCE( EXP.expTotalSales, 0 ) as expTotalSales1,
               CONCAT(COALESCE(Pur.carcost,0), "___", COALESCE(CarE.carexp,0), "___", COALESCE(EXP.expTotalSales,0)) as totalcost,
               FSI.item_price as item_price,
               FSI.item_price as totalprice,
               CONCAT(COALESCE(Pur.carcost,0), "___", COALESCE(CarE.carexp,0), "___", COALESCE(EXP.expTotalSales,0), "___", COALESCE(FSIMinus.item_price,0)) as prooft,
               sales.id as id', false)
               ->from('sales')
               ->join($sp, 'sales.id = PSales.sale_id', 'left')
               ->join($si, 'FSI.sale_id=sales.id', 'left')
               ->join($ex, 'sales.id = EXP.sale_id', 'left')
               ->join($ce, 'sales.id = CarE.sale_id', 'left')
               ->join($pu, 'sales.id = Pur.sale_id', 'left')
               ->join($siMinus, 'FSIMinus.sale_id=sales.id', 'left');

               //$this->datatables->where(" (FSI.product_id=CarE.vehicle_id) ");

              // ->join($ex, 'sale_items.product_id = EXP.vehicle_id', 'left')
              //->join($wpo, 'sale_items.product_id = WarehousesProductsOrignal.product_id', 'left')
               //->group_by('sale_items.product_id');

               if ($start_date && $end_date) {
                       $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
                    }


                    if ($reference_no) {
                       $this->datatables->where($this->db->dbprefix('sales') . '.reference_no', $reference_no);
                   }
                   if ($warehouse) {
                       $this->datatables->where($this->db->dbprefix('sales') . '.warehouse_id', $warehouse);
                   }
                   if ($sale_status) {
                       $this->datatables->where($this->db->dbprefix('sales') . '.sale_status', $sale_status);
                   }
                   if ($customer) {
                       $this->datatables->where($this->db->dbprefix('sales') . '.customer_id', $customer);
                   }

                   if ($product) {
                       $this->datatables->where($this->db->dbprefix('products') . '.id', $product);
                   }
                   if ($cf1) {
               $this->datatables->where($this->db->dbprefix('products') . '.cf1', $cf1);
           }
           if ($cf2) {
               $this->datatables->where($this->db->dbprefix('products') . '.cf2', $cf2);
           }
           if ($cf3) {
               $this->datatables->where($this->db->dbprefix('products') . '.cf3', $cf3);
           }
           if ($cf4) {
               $this->datatables->where($this->db->dbprefix('products') . '.cf4', $cf4);
           }
           if ($cf5) {
               $this->datatables->where($this->db->dbprefix('products') . '.cf5', $cf5);
           }
           if ($cf6) {
               $this->datatables->where($this->db->dbprefix('products') . '.cf6', $cf6);
           }
           if ($category) {
               $this->datatables->where($this->db->dbprefix('products') . '.category_id', $category);
           }
           if ($subcategory) {
               $this->datatables->where($this->db->dbprefix('products') . '.subcategory_id', $subcategory);
           }
           if ($product_type) {
               $this->datatables->where($this->db->dbprefix('products') . '.type', $product_type);
           }
           if ($brand) {
               $this->datatables->where($this->db->dbprefix('products') . '.brand', $brand);
           }


           echo $this->datatables->generate();
       }
       }
   }










}
