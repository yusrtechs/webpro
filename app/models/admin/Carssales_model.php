<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Carssales_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addDelivery($data = [])
    {
        if ($this->db->insert('deliveries', $data)) {
            if ($this->site->getReference('do') == $data['do_reference_no']) {
                $this->site->updateReference('do');
            }
            return true;
        }
        return false;
    }

    /* ----------------- Gift Cards --------------------- */

    public function addGiftCard($data = [], $ca_data = [], $sa_data = [], $payment = [], $payment_type = null)
    {

        if ($this->db->insert('gift_cards', $data)) {
			$card_id = $this->db->insert_id();

            if (!empty($ca_data)) {
                $this->db->update('companies', ['award_points' => $ca_data['points']], ['id' => $ca_data['customer']]);
            } elseif (!empty($sa_data)) {
                $this->db->update('users', ['award_points' => $sa_data['points']], ['id' => $sa_data['user']]);
            }

			if($data['paid']==1 && $data['customer_id']){
			$date = date('Y-m-d H:i:s');
            $reference=$this->site->getReference('re');
            $data2 = [
                'card_id'         => $card_id,
                'date'         => $date,
                'reference'    => $reference,
                'amount'       => $data['total_paid_amount'],
                'created_by'   => $data['created_by'],
                'card_no'         => $data['card_no'],
                'customer_id'         => $data['customer_id'],
                'note'         => $data['note'],
                'category_id'  => "0",
                'warehouse_id' => $data['warehouse_id'],
                'paid_by' => $payment['paid_by'],
                'cheque_no' => $payment['cheque_no'],
                'cc_no' => $payment['cc_no'],
                'cc_holder' => $payment['cc_holder'],
                'cc_month' => $payment['cc_month'],
                'cc_year' => $payment['cc_year'],
                'cc_type' => $payment['cc_type'],
                'currency' => $payment['currency'],
                'currency_rate' => $payment['currency_rate'],
            ];
			$this->addRevenue($data2);

			if ($this->Settings->accounting){


				$entryDebitAccount= [];
					$entryDebitBalance= [];
						//$entrybillerAcc=$this->site->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAcc =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						if($entrybillerAcc=="")
						$entrybillerAcc=$payment_type;


					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_paid_amount']);



					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_gift_card_sales;
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['paid_amount']);
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['tax_amount']);



			    $this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference,'Gift_card_sales',$data['user_id']);
				}

			}
            return true;
        }
        return false;
    }

    public function addRevenue($data = [])
    {
        if ($this->db->insert('revenue', $data)) {
            if ($this->site->getReference('re') == $data['reference']) {
                $this->site->updateReference('re');
            }
            return true;
        }
        return false;
    }

    public function updateRevenue($data = [],$card_id)
    {
        if ($this->db->update('revenue', $data, ['card_id' => $card_id])) {
            return true;
        }
        return false;
    }

    public function addOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getProductOptionByID($option_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('product_variants', ['quantity' => $nq], ['id' => $option_id])) {
                return true;
            }
        }
        return false;
    }

    public function addPayment($data = [], $customer_id = null, $payment_type = null, $payment_status = null)
    {
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('pay') == $data['reference_no']) {
                $this->site->updateReference('pay');
            }
            $this->site->syncSalePayments($data['sale_id']);
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['cc_no']);
                $this->db->update('gift_cards', ['balance' => ($gc->balance - $data['amount'])], ['card_no' => $data['cc_no']]);
            } elseif ($customer_id && $data['paid_by'] == 'deposit') {
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $data['amount'])], ['id' => $customer_id]);
            }





			if ($this->Settings->accounting && $data['paid_by']!="deposit"){
				$entryDescription="Payments_Sales_Invoice_No#".$data['sale_id'];

				$sale = $this->site->getSaleByID($data['sale_id']);



				if ($sale->sale_status == 'pending' || $sale->sale_status == 'completed'){



				$amount=$data['amount'];
				$amountPL=0;
				$amountDiff=0;
				$commercial_discount=0;
				//$currencyRate=$this->site->getCurrencyByCode($data['currency'])->rate;


				$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
				if($data['currency_rate'])
					$exchange_rate=$data['currency_rate'];
				else
					$exchange_rate=$default_currencyRate;

				if($sale->currency_rate)
					$currencyRate=$sale->currency_rate;
				else
					$currencyRate=1;







					//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

					$amount=$data['amount'] * $currencyRate;

					//if ($currencyRate != $exchange_rate){
						$amountPL=$data['amount'] * $exchange_rate;
						$amountDiff=$amountPL-$amount;
					//}
					//}
					if($data['commercial_discount']){
						$commercial_discount=$data['commercial_discount'] * $currencyRate;


						}







					$entry_type="Sales_Payments";
						/*if ($sale->sale_status == 'pending')
						$entry_type="sale_pending";
						if ($sale->sale_status == 'completed')
						$entry_type="sale_completed";
					*/


						$entryCreditAccount= [];
						$entryCreditBalance= [];

						if ($sale->sale_status == 'pending'){
						//$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_by_road_acct;










						$entryDebitAccount= [];
						if($data['paid_by']=='gift_card')
							$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_gift_card_payments;
						else{
							$entryDebitAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryDebitAcc=="")
							$entryDebitAcc=$payment_type;
							 $entryDebitAccount[] =  $entryDebitAcc;
							}

						$entryDebitBalance= [];
						if($amountDiff==0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff<0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff>0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						}

						//$amount=$amount+$data['amount'];

						$commisson_amount=0;
						/*if($this->site->getPaymentTypeByID($payment_type)->commisson_perc!=0){

							$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->commisson_account;
							$commisson_amount =  $this->sma->formatDecimal(($data['amount']*$this->site->getPaymentTypeByID($payment_type)->commisson_perc/100));
							if($this->site->getPaymentTypeByID($payment_type)->tax_rate!=0){
								$commisson_amount =  $this->sma->formatDecimal($commisson_amount + ($commisson_amount*$this->site->getPaymentTypeByID($payment_type)->tax_rate /100));
							}
						$entryDebitBalance[] =  $commisson_amount;

						}*/

						$entryCreditAccount= [];
						$entryCreditBalance= [];
						if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
						}
						 $entryCreditAccount[] =  $entryDebitAcc;
						if($amountDiff==0){
						 $entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff>0){
						$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff<0){
						$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						}



						/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($sale->grand_total)){
						$entryDebitBalance[] =  $this->sma->formatDecimal($sale->grand_total - $data['amount']);

						}*/


						}else{
						if ($sale->sale_status == 'completed'){





						 $entryDebitAccount= [];
						 /*if($data['paid_by']=="deposit"){
							if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
						}
						 $entryCreditAccount[] =  $entryDebitAcc;

						 }else{

						 $entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						 }*/

						if($data['paid_by']=='gift_card')
							$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_gift_card_payments;
						else{
							$entryDebitAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryDebitAcc=="")
							$entryDebitAcc=$payment_type;
							 $entryDebitAccount[] =  $entryDebitAcc;
							}



						$entryDebitBalance= [];
						if($amountDiff==0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff<0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff>0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						}

						if($commercial_discount!=0){
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_discount_allowance;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($commercial_discount));


						}

						$commisson_amount=0;
						/*if($this->site->getPaymentTypeByID($payment_type)->commisson_perc!=0){

							$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->commisson_account;
							$commisson_amount =  $this->sma->formatDecimal(($data['amount']*$this->site->getPaymentTypeByID($payment_type)->commisson_perc/100));
							if($this->site->getPaymentTypeByID($payment_type)->tax_rate!=0){
								$commisson_amount =  $this->sma->formatDecimal($commisson_amount + ($commisson_amount*$this->site->getPaymentTypeByID($payment_type)->tax_rate /100));
							}
						$entryDebitBalance[] =  $commisson_amount;

						}*/
						$entryCreditAccount= [];
						$entryCreditBalance= [];

						if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
						}
						 $entryCreditAccount[] =  $entryDebitAcc;


						 if($commercial_discount!=0){
						$amount=$amount+$commercial_discount;

						}

						if($amountDiff==0){
						 $entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff>0){
						$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff<0){
						$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						}






						}





						}







				$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,$entryDescription);








				}




				if ($sale->sale_status == 'returned'){

					$amount=abs($data['amount']);
				$amountPL=0;
				$amountDiff=0;
				$commercial_discount=0;
				//$currencyRate=$this->site->getCurrencyByCode($data['currency'])->rate;


				$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
				if($data['currency_rate'])
					$exchange_rate=$data['currency_rate'];
				else
					$exchange_rate=$default_currencyRate;

				if($sale->currency_rate)
					$currencyRate=$sale->currency_rate;
				else
					$currencyRate=1;







					//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

					$amount=abs($data['amount']) * $currencyRate;

					//if ($currencyRate != $exchange_rate){
						$amountPL=abs($data['amount']) * $exchange_rate;
						$amountDiff=$amountPL-$amount;
					//}
					//}

					if($data['commercial_discount']){
						$commercial_discount=abs($data['commercial_discount']) * $currencyRate;


						}


						//if(isset($payment['currency_rate']) && $payment['currency_rate']!=1){
						//$payment['amount']=$payment['amount']*$payment['currency_rate'];
						//}


					$entry_type="sale_returned";


						$entryDebitAccount= [];
						 if($data['paid_by']=='gift_card')
							$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_gift_card_payments;
						//else
						//	$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						else{
							$entryDebitAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryDebitAcc=="")
							$entryDebitAcc=$payment_type;
							 $entryDebitAccount[] =  $entryDebitAcc;
							}





						$entryDebitBalance= [];
						//$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['amount'])+$commisson_amount);
						if($amountDiff==0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff<0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff>0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						}

						if($commercial_discount!=0){
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_discount_allowance;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($commercial_discount));


						}


						$commisson_amount=0;
						/*if($this->site->getPaymentTypeByID($payment_type)->commisson_perc!=0){

							$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->commisson_account;
							$commisson_amount =  $this->sma->formatDecimal((abs($data['amount'])*$this->site->getPaymentTypeByID($payment_type)->commisson_perc/100));
							if($this->site->getPaymentTypeByID($payment_type)->tax_rate!=0){
								$commisson_amount =  $this->sma->formatDecimal($commisson_amount + ($commisson_amount*$this->site->getPaymentTypeByID($payment_type)->tax_rate /100));
							}
						$entryDebitBalance[] =  $commisson_amount;

						}*/
						$entryCreditAccount= [];
						$entryCreditBalance= [];

						if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
						}
						 $entryCreditAccount[] =  $entryDebitAcc;


						if($commercial_discount!=0){
						$amount=abs($amount)+abs($commercial_discount);

						}

						 $entryCreditBalance[] =  $this->sma->formatDecimal(abs($amount));
						 if($amountDiff>0){
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}








						$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$sale->reference_no,$entry_type);








				}

				}


			$payment_currency=$this->site->getCurrencyByCode($data['currency']);

				if($payment_currency->auto_update == 1 && $payment_currency->rate != $data['currency_rate'] && $sale->currency_code != $this->Settings->default_currency){

					$data_curr = [
                'rate'         => $data['currency_rate'],
					];
					$this->site->updateCurrency($payment_currency->id,$data_curr);
				}



            return true;
        }
        return false;
    }

    public function addSale($data = [], $items = [], $payment = [], $si_return = [], $payment_type = [], $quote_id = null)
    {
        if (empty($si_return)) {
            $cost = $this->site->costing($items);
            // $this->sma->print_arrays($cost);
        }

        $this->db->trans_off();
		$this->db->trans_start();
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();

			if($quote_id){
				$Qpayments=$this->getInvoiceQuotesPayments($quote_id);
				$tot_payments=0;

				foreach ($Qpayments as $Qpayment) {
					$tot_payments=$tot_payments+$Qpayment->amount;



				}
				if($tot_payments!=0){
					$this->db->update('payments', ['sale_id' => $sale_id], ['quote_id' => $quote_id]);
					$this->db->update('sales', ['paid' => $tot_payments], ['id' => $sale_id]);
				}
			}


            if ($this->site->getReference('so') == $data['reference_no']) {
                $this->site->updateReference('so');
            }
            foreach ($items as $item) {





                {

				$wp=$this->getavg_costProduct($item['product_id'],$item['warehouse_id']);
				if($wp){
					if (!empty($si_return)) {
						$sale_item=$this->getSaleItemBysale_id($data['sale_id'],$item['product_id']);
						$qtyOS=0;
						$qtyOS=$sale_item->quantity_overselling;
						$new_qtyOS=0;


						$qty=0;
						if($wp->quantity < 0)
						$qty=$wp->quantity - $item['quantity'];
						else
						$qty=$wp->quantity + $item['quantity'];

					if($wp->quantity < 0){
						if($qty > 0){
							$new_qtyOS=0;
							$item['item_cost_value']=$this->sma->formatDecimal((0 - $qty) * $sale_item->avg_cost,12);
							$item['avg_cost']=$sale_item->avg_cost;


						}else{
							$new_qtyOS=(0 - $qty);
							$item['item_cost_value']=0;
							$item['avg_cost']=0;

						}




					}else{
							$new_qtyOS=0;
							$item['item_cost_value']=$this->sma->formatDecimal($item['quantity'] * $sale_item->avg_cost,12);
							$item['avg_cost']=$sale_item->avg_cost;




					}
					if($qtyOS>0)
						$this->db->update('sale_items', ['quantity_overselling' => $new_qtyOS], ['id' => $sale_item->id]);
					/*
					$overselling_qty=0;
					$new_qty=0;
					if($wp->quantity < 0)
					$overselling_qty=	(0-$qty);
					else
					$overselling_qty=	(0-$item['quantity']);

					if($sale_item->overselling==1){
							if($sale_item->quantity_overselling>0){
								if($sale_item->quantity_overselling >= $overselling_qty){
									$new_qty=$sale_item->quantity_overselling - $overselling_qty;
									if($new_qty<=0)
									$this->db->update('sale_items', ['overselling' => 0,'quantity_overselling' => $new_qty], ['id' => $sale_item->id]);
									else
									$this->db->update('sale_items', ['quantity_overselling' => $new_qty], ['id' => $sale_item->id]);

								}else{
									$this->db->update('sale_items', ['overselling' => 0,'quantity_overselling' => 0], ['id' => $sale_item->id]);

								}
							}else{
									$this->db->update('sale_items', ['overselling' => 0,'quantity_overselling' => 0], ['id' => $sale_item->id]);

							}

							}
							*/



					}else{
					if($item['quantity'] > $wp->quantity){
						if($wp->quantity > 0){
							$item['item_cost_value']=$this->sma->formatDecimal($wp->quantity * $wp->avg_cost,12);
							$item['avg_cost']=$wp->avg_cost;
							$item['overselling']=1;
							$item['quantity_overselling']=($item['quantity']-$wp->quantity);


						}else{
							$item['item_cost_value']=0;
							$item['avg_cost']=0;
							$item['overselling']=1;
							$item['quantity_overselling']=($item['quantity']);


						}

					}else{
						$item['item_cost_value']=$this->sma->formatDecimal($item['quantity'] * $wp->avg_cost,12);
						$item['avg_cost']=$wp->avg_cost;

					}

					}
				}
				}





                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
				if($item['product_type']=='standard')
                if ($data['sale_status'] == 'completed' && empty($si_return)) {
                   // $item_costs = $this->site->item_costing($item);
                   // foreach ($item as $item_cost) {

                        $this->db->update('cars_vehicles', ['status' => 'sold'], ['vehicle_id' => $item['product_id']]);

                        /*if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
                            $item_cost['sale_item_id'] = $sale_item_id;
                            $item_cost['sale_id']      = $sale_id;
                            $item_cost['date']         = date('Y-m-d', strtotime($data['date']));
                            if (!isset($item_cost['pi_overselling'])) {

                                $this->db->insert('costing', $item_cost);
                            }
                        } else {
                            foreach ($item_cost as $ic) {
                                $ic['sale_item_id'] = $sale_item_id;
                                $ic['sale_id']      = $sale_id;
                                $ic['date']         = date('Y-m-d', strtotime($data['date']));
                                if (!isset($ic['pi_overselling'])) {
                                    $this->db->insert('costing', $ic);
                                }
                            }
                        }*/
                   // }
                }else{
					$this->db->update('cars_vehicles', ['status' => 'available'], ['vehicle_id' => $item['product_id']]);

				}
            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }

            if (!empty($si_return)) {
            /*    foreach ($si_return as $return_item) {
                    $product = $this->getVehicleByID($return_item['product_id']);

					//$clause = ['product_id' => $return_item['product_id'], 'warehouse_id' => $return_item['warehouse_id'], 'purchase_id' => null, 'transfer_id' => null, 'option_id' => null];
					//$pi = $this->site->getPurchasedItem($clause);




                    if ($product->type == 'combo') {

                        $combo_items = $this->site->getProductComboItems($return_item['product_id'], $return_item['warehouse_id']);
                        foreach ($combo_items as $combo_item) {
                        $wp_details = $this->getWarehouseProductQuantity($return_item['warehouse_id'], $combo_item->id);
					    $this->UpdateCostingAndPurchaseItem($return_item, $combo_item->id, ($return_item['quantity'] * $combo_item->qty));

							$sale_item=$this->getSaleItemByid_sale_id($return_item['sale_id'],$combo_item->id,$return_item['id']);
					//$this->sma->print_arrays($sale_item);

					if($sale_item->item_cost_value!=0)
					//if ($pi->quantity_balance > 0)
					{

					//if($sale_item->item_cost_value>0)


					$this->site->syncPurchaseItems_returnSales($combo_item->id, $return_item['warehouse_id'],(0-$sale_item->item_cost_value));


					$total_quantity = $wp_details->quantity + (0-$sale_item->quantity);


					$total_cost     = (($wp_details->quantity * $wp_details->avg_cost) + ((0-$sale_item->quantity) * ($sale_item->avg_cost)));
					//$total_cost     = $this->getSumPurchaseItemsCost($return_item['warehouse_id'], $return_item['product_id'])->item_cost_value;
                    //$total_quantity =  $this->getSumPurchaseItemsCost($return_item['warehouse_id'], $return_item['product_id'])->quantity;

					if($total_quantity!=0){
					$avg_cost = $this->sma->formatDecimal(($total_cost / $total_quantity),12);


            	//else
					$this->db->update('warehouses_products', ['avg_cost' => $avg_cost], ['product_id' => $combo_item->id, 'warehouse_id' => $return_item['warehouse_id']]);
                 }

					}


                        }
                    } else {
						$wp_details = $this->getWarehouseProductQuantity($return_item['warehouse_id'], $return_item['product_id']);

                        $this->UpdateCostingAndPurchaseItem($return_item, $return_item['product_id'], $return_item['quantity']);

					$sale_item=$this->getSaleItemByid_sale_id($return_item['sale_id'],$return_item['product_id'],$return_item['id']);
					//$this->sma->print_arrays($sale_item);

					if($sale_item->item_cost_value!=0)
					//if ($pi->quantity_balance > 0)
					{

					//if($sale_item->item_cost_value>0)


					$this->site->syncPurchaseItems_returnSales($return_item['product_id'], $return_item['warehouse_id'],(0-$sale_item->item_cost_value));


					$total_quantity = $wp_details->quantity + (0-$sale_item->quantity);


					$total_cost     = (($wp_details->quantity * $wp_details->avg_cost) + ((0-$sale_item->quantity) * ($sale_item->avg_cost)));
					//$total_cost     = $this->getSumPurchaseItemsCost($return_item['warehouse_id'], $return_item['product_id'])->item_cost_value;
                    //$total_quantity =  $this->getSumPurchaseItemsCost($return_item['warehouse_id'], $return_item['product_id'])->quantity;

					if($total_quantity!=0){
					$avg_cost = $this->sma->formatDecimal(($total_cost / $total_quantity),12);


            	//else
					$this->db->update('warehouses_products', ['avg_cost' => $avg_cost], ['product_id' => $return_item['product_id'], 'warehouse_id' => $return_item['warehouse_id']]);
                 }

					}



					}

					}*/
                $this->db->update('sales', ['return_sale_ref' => $data['return_sale_ref'], 'surcharge' => $data['surcharge'], 'return_sale_total' => $data['grand_total'], 'return_id' => $sale_id], ['id' => $data['sale_id']]);
            }

            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' && !empty($payment)) {
				if (empty($si_return)) {
					 $paid = 0;
                foreach ($payment as $payments) {
                    if (!empty($payments) && isset($payments['amount']) && $payments['amount'] != 0) {
                        $payments['sale_id']      = $sale_id;
                        $payments['reference_no'] = $this->site->getReference('pay');
                        if ($payments['paid_by'] == 'ppp') {
                            $card_info = ['number' => $payments['cc_no'], 'exp_month' => $payments['cc_month'], 'exp_year' => $payments['cc_year'], 'cvc' => $payments['cc_cvv2'], 'type' => $payments['cc_type']];
                            $result    = $this->paypal($payments['amount'], $card_info, '', $sale_id);
                            if (!isset($result['error'])) {
                                $payments['transaction_id'] = $result['transaction_id'];
                                $payments['date']           = $this->sma->fld($result['created_at']);
                                $payments['amount']         = $result['amount'];
                                $payments['currency']       = $result['currency'];
                                unset($payments['cc_cvv2']);
                                $this->db->insert('payments', $payments);
                                $this->site->updateReference('pay');
                                $paid += $payments['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                if (!empty($result['message'])) {
                                    foreach ($result['message'] as $m) {
                                        $msg[] = '<p class="text-danger">' . $m['L_ERRORCODE'] . ': ' . $m['L_LONGMESSAGE'] . '</p>';
                                    }
                                } else {
                                    $msg[] = lang('paypal_empty_error');
                                }
                            }
                        } elseif ($payments['paid_by'] == 'stripe') {
                            $card_info = ['number' => $payments['cc_no'], 'exp_month' => $payments['cc_month'], 'exp_year' => $payments['cc_year'], 'cvc' => $payments['cc_cvv2'], 'type' => $payments['cc_type']];
                            $result    = $this->stripe($payments['amount'], $card_info);
                            if (!isset($result['error'])) {
                                $payments['transaction_id'] = $result['transaction_id'];
                                $payments['date']           = $this->sma->fld($result['created_at']);
                                $payments['amount']         = $result['amount'];
                                $payments['currency']       = $result['currency'];
                                unset($payments['cc_cvv2']);
                                $this->db->insert('payments', $payments);
                                $this->site->updateReference('pay');
                                $paid += $payments['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['code'] . ': ' . $result['message'] . '</p>';
                            }
                        } elseif ($payments['paid_by'] == 'authorize') {
                            $authorize_arr                 = ['x_card_num' => $payments['cc_no'], 'x_exp_date' => ($payments['cc_month'] . '/' . $payments['cc_year']), 'x_card_code' => $payments['cc_cvv2'], 'x_amount' => $payments['amount'], 'x_invoice_num' => $sale_id, 'x_description' => 'Sale Ref ' . $data['reference_no'] . ' and Payment Ref ' . $payments['reference_no']];
                            list($first_name, $last_name)  = explode(' ', $payments['cc_holder'], 2);
                            $authorize_arr['x_first_name'] = $first_name;
                            $authorize_arr['x_last_name']  = $last_name;
                            $result                        = $this->authorize($authorize_arr);
                            if (!isset($result['error'])) {
                                $payments['transaction_id'] = $result['transaction_id'];
                                $payments['approval_code']  = $result['approval_code'];
                                $payments['date']           = $this->sma->fld($result['created_at']);
                                unset($payments['cc_cvv2']);
                                $this->db->insert('payments', $payments);
                                $this->site->updateReference('pay');
                                $paid += $payments['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['msg'] . '</p>';
                            }
                        } else {
                            if ($payments['paid_by'] == 'gift_card') {
                                $this->db->update('gift_cards', ['balance' => $payments['gc_balance']], ['card_no' => $payments['cc_no']]);
                                unset($payments['gc_balance']);
                            } elseif ($payments['paid_by'] == 'deposit') {
                                $customer = $this->site->getCompanyByID($data['customer_id']);
                                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $payments['amount'])], ['id' => $customer->id]);
                            }
                            unset($payments['cc_cvv2']);
                            $this->db->insert('payments', $payments);
                            $this->site->updateReference('pay');
                            $paid += $payments['amount'];
                        }
                    }
                }
				}else{
                if (empty($payment['reference_no'])) {
                    $payment['reference_no'] = $this->site->getReference('pay');
                }
                $payment['sale_id'] = $sale_id;
                if ($payment['paid_by'] == 'gift_card') {
                    $this->db->update('gift_cards', ['balance' => $payment['gc_balance']], ['card_no' => $payment['cc_no']]);
                    unset($payment['gc_balance']);
                    $this->db->insert('payments', $payment);
                } else {
                    if ($payment['paid_by'] == 'deposit') {
                        $customer = $this->site->getCompanyByID($data['customer_id']);
                        $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $payment['amount'])], ['id' => $customer->id]);
                    }
                    $this->db->insert('payments', $payment);
                }
                if ($this->site->getReference('pay') == $payment['reference_no']) {
                    $this->site->updateReference('pay');
                }
				}
                $this->site->syncSalePayments($sale_id);
            }

            $this->site->syncQuantity($sale_id);
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);



			if ($this->Settings->accounting){

				$entryDescription="Sales_Invoice_No#".$sale_id;

				$data['grand_total'] = $this->sma->formatDecimal($data['grand_total']);
				$data['total_tax'] = $this->sma->formatDecimal($data['total_tax']);
				$data['shipping'] = $this->sma->formatDecimal($data['shipping']);
				if(isset($data['currency_rate']) && $data['currency_rate']!=1){
					$data['grand_total'] = $data['grand_total'] * $data['currency_rate'];
					$data['total_tax'] = $data['total_tax'] * $data['currency_rate'];
					$data['shipping'] = $data['shipping'] * $data['currency_rate'];


				}


			if ($data['sale_status'] == 'pending' || $data['sale_status'] == 'completed'){

				$entry_type="";
					if ($data['sale_status'] == 'pending')
					$entry_type="sale_pending";
					if ($data['sale_status'] == 'completed')
					$entry_type="sale_completed";




				//if($data['payment_status'] == 'pending'){
				if($this->site->getCompanyByID($data['customer_id'])->co_type==1){
					$entryCreditAccount= [];

					if ($data['sale_status'] == 'pending')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_receiving_acct;
					if ($data['sale_status'] == 'completed')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_acct;


					if(abs($data['shipping'])>0)
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_shipping_other_revenues;







					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];

					$entryCreditBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']);

					if(abs($data['shipping'])>0)
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['shipping']);

					$entryCreditBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryDebitAccount=$this->site->getAccountByRefId($data['customer_id'],'local_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_local_customers_acct;

					$costCenterCreditId= [];
					$costCenterCreditAmount= [];

					if($data['costCenterId']){
						$costCreditCount=0;
							foreach($entryCreditAccount as $key => $acc){
							if($costCreditCount==0){
								$costCenterCreditId[] =json_decode($data['costCenterId']);
								$costCenterCreditAmount[] =json_decode($data['costCenterAmount']);



							}else{
								$costCenterCreditId[] ="";
								$costCenterCreditAmount[] ="";

								}
								//$this->sma->print_arrays($costCenterCreditAmount);
						      $costCreditCount++;
						}

					}










					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription);

				}

				if($this->site->getCompanyByID($data['customer_id'])->co_type==2){
					$entryCreditAccount= [];

					if ($data['sale_status'] == 'pending')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_receiving_acct;
					if ($data['sale_status'] == 'completed')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_acct;


					if(abs($data['shipping'])>0)
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_shipping_other_revenues;







					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']);

					if(abs($data['shipping'])>0)
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['shipping']);

					$entryCreditBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryDebitAccount=$this->site->getAccountByRefId($data['customer_id'],'international_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_international_customers_acct;



					$costCenterCreditId= [];
					$costCenterCreditAmount= [];

					if($data['costCenterId']){
						$costCreditCount=0;
							foreach($entryCreditAccount as $key => $acc){
							if($costCreditCount==0){
								$costCenterCreditId[] =json_decode($data['costCenterId']);
								$costCenterCreditAmount[] =json_decode($data['costCenterAmount']);



							}else{
								$costCenterCreditId[] ="";
								$costCenterCreditAmount[] ="";

								}
								//$this->sma->print_arrays($costCenterCreditAmount);
						      $costCreditCount++;
						}

					}

					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription);

				}
			//}
			//else{

					if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' && !empty($payment)) {
					$i=0;
					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$amount=0;
					$final_commisson_amount=0;
					foreach ($payment as $payments) {


						$entry_type="";
					if ($data['sale_status'] == 'pending')
					$entry_type="sale_pending";
					if ($data['sale_status'] == 'completed')
					$entry_type="sale_completed";


					 if($payments['paid_by']=="deposit"){
						 if($this->site->getCompanyByID($data['customer_id'])->co_type==1){
							 $entryDebitAcc=$this->site->getAccountByRefId($data['customer_id'],'local_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
						 }else if($this->site->getCompanyByID($data['customer_id'])->co_type==2){
						$entryDebitAcc=$this->site->getAccountByRefId($data['customer_id'],'international_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
					 	 }


						 $entryDebitAccount[] =  $entryDebitAcc;
					 }else{
						 //$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type[$i]['payment_type'])->payment_account;
						 $entryDebitAcc=$this->site->getPaymentTypeByID($payment_type[$i]['payment_type'])->payment_account;
						if($entryDebitAcc=="")
						$entryDebitAcc=$payment_type[$i]['payment_type'];
					 	$entryDebitAccount[] =  $entryDebitAcc;

					 }


					 //$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);

					 $entryDebitBalance[] =  $this->sma->formatDecimal($payments['amount']);
					 $amount=$amount+$payments['amount'];

					$commisson_amount=0;
					/*if($this->site->getPaymentTypeByID($payment_type[$i]['payment_type'])->commisson_perc!=0){

						$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type[$i]['payment_type'])->commisson_account;
						$commisson_amount =  $this->sma->formatDecimal(($payments['amount']*$this->site->getPaymentTypeByID($payment_type[$i]['payment_type'])->commisson_perc/100));
						if($this->site->getPaymentTypeByID($payment_type[$i]['payment_type'])->tax_rate!=0){
							$commisson_amount =  $this->sma->formatDecimal($commisson_amount + ($commisson_amount*$this->site->getPaymentTypeByID($payment_type[$i]['payment_type'])->tax_rate /100));
						}
					$entryDebitBalance[] =  $commisson_amount;
					$final_commisson_amount=$final_commisson_amount+$commisson_amount;

			        }*/














						$i++;
					}

					$entryCreditAccount= [];
					$entryCreditBalance= [];

					//if($this->sma->formatDecimal($amount)<$this->sma->formatDecimal($data['grand_total'])){

					if($this->site->getCompanyByID($data['customer_id'])->co_type==1){
					$entryDebitAcc=$this->site->getAccountByRefId($data['customer_id'],'local_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
					}else{
					$entryDebitAcc=$this->site->getAccountByRefId($data['customer_id'],'international_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
					}
					$entryCreditAccount[] =  $entryDebitAcc;
					$entryCreditBalance[] =  $this->sma->formatDecimal($amount);




					//}



					$costCenterCreditId= [];
					$costCenterCreditAmount= [];

					if($data['costCenterId']){
						$costCreditCount=0;
							foreach($entryCreditAccount as $key => $acc){
							if($costCreditCount==0){
								$costCenterCreditId[] =json_decode($data['costCenterId']);
								$costCenterCreditAmount[] =json_decode($data['costCenterAmount']);



							}else{
								$costCenterCreditId[] ="";
								$costCenterCreditAmount[] ="";

								}
								//$this->sma->print_arrays($costCenterCreditAmount);
						      $costCreditCount++;
						}

					}


					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],"Sales_Payments",null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],"Payments_Sales_Invoice_No#".$sale_id);




			}

				//}


				if($data['sale_status'] == 'completed'){
					$final_item_cost_value=$this->getTotalItemCostValue($sale_id)->item_cost_value;
					if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
							}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


					$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],"Cost_of_goods_sold",null,null,null,null,$data['date'],$entryDescription);
							}
					}


			}









			if ($data['sale_status'] == 'returned'){



				$entry_type="";
					$entry_type="sale_returned";

				if($data['payment_status'] == 'pending'){
				if($this->site->getCompanyByID($data['customer_id'])->co_type==1){
					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_return_acct;

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax']));
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['total_tax']));

					$entryDebitAccount=$this->site->getAccountByRefId($data['customer_id'],'local_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_local_customers_acct;




					$costCenterDebitId= [];
					$costCenterDebitAmount= [];

					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryCreditAccount as $key => $acc){
							if($costDebitCount==0){
								$costCenterDebitId[] =json_decode($data['costCenterId']);
								$costCenterDebitAmount[] =json_decode($data['costCenterAmount']);



							}else{
								$costCenterDebitId[] ="";
								$costCenterDebitAmount[] ="";

								}
								//$this->sma->print_arrays($costCenterCreditAmount);
						      $costDebitCount++;
						}

					}


					$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$this->sma->formatDecimal(abs($data['grand_total'])),$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);

				}

				if($this->site->getCompanyByID($data['customer_id'])->co_type==2){
					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_return_acct;

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax']));
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['total_tax']));

					$entryDebitAccount=$this->site->getAccountByRefId($data['customer_id'],'international_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_international_customers_acct;


					$costCenterDebitId= [];
					$costCenterDebitAmount= [];

					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryCreditAccount as $key => $acc){
							if($costDebitCount==0){
								$costCenterDebitId[] =json_decode($data['costCenterId']);
								$costCenterDebitAmount[] =json_decode($data['costCenterAmount']);



							}else{
								$costCenterDebitId[] ="";
								$costCenterDebitAmount[] ="";

								}
								//$this->sma->print_arrays($costCenterCreditAmount);
						      $costDebitCount++;
						}

					}


					$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$this->sma->formatDecimal(abs($data['grand_total'])),$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);

				}
			}else{

					if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' && !empty($payment)) {


			$amount=abs($payment['amount']);
			$amountPL=0;
			$amountDiff=0;
			//$currencyRate=$this->site->getCurrencyByCode($data['currency'])->rate;


			$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
			if($payment['currency_rate'])
				$exchange_rate=$payment['currency_rate'];
			else
				$exchange_rate=$default_currencyRate;

			if($data['currency_rate'])
				$currencyRate=$data['currency_rate'];
			else
				$currencyRate=1;







				//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

				$amount=abs($payment['amount']) * $currencyRate;

				//if ($currencyRate != $exchange_rate){
					$amountPL=abs($payment['amount']) * $exchange_rate;
					$amountDiff=$amountPL-$amount;
				//}
				//}

					//if(isset($payment['currency_rate']) && $payment['currency_rate']!=1){
					//$payment['amount']=$payment['amount']*$payment['currency_rate'];
					//}

					$entryDebitAccount= [];
					$entryDebitBalance= [];
					//$amount=0;
					$final_commisson_amount=0;






					 //$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type[0]['payment_type'])->payment_account;
					 $entryDebitAcc=$this->site->getPaymentTypeByID($payment_type[0]['payment_type'])->payment_account;
						if($entryDebitAcc=="")
						$entryDebitAcc=$payment_type[0]['payment_type'];
					 	$entryDebitAccount[] =  $entryDebitAcc;


					 //$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);

					 //$entryDebitBalance[] =  $this->sma->formatDecimal(abs($payment['amount']));

					 if($amountDiff==0){
					$entryDebitBalance[] =  $this->sma->formatDecimal($amount);
					}
					if($amountDiff<0){
					$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
					}
					if($amountDiff>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
					}



					 //$amount=$amount+$payment['amount'];

					$commisson_amount=0;
					/*if($this->site->getPaymentTypeByID($payment_type[0]['payment_type'])->commisson_perc!=0){

						$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type[0]['payment_type'])->commisson_account;
						$commisson_amount =  $this->sma->formatDecimal((abs($payment['amount'])*$this->site->getPaymentTypeByID($payment_type[0]['payment_type'])->commisson_perc/100));
						if($this->site->getPaymentTypeByID($payment_type[0]['payment_type'])->tax_rate!=0){
							$commisson_amount =  $this->sma->formatDecimal($commisson_amount + ($commisson_amount*$this->site->getPaymentTypeByID($payment_type[0]['payment_type'])->tax_rate /100));
						}
					$entryDebitBalance[] =  $commisson_amount;
					$final_commisson_amount=$final_commisson_amount+$commisson_amount;

			        }*/















					$entryCreditAccount= [];
					$entryCreditBalance= [];

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_return_acct;

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;
					if(abs($data['shipping'])>0)
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_shipping_other_revenues;


					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax'])-abs($data['shipping'])+$final_commisson_amount);
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['total_tax']));
					if(abs($data['shipping'])>0)
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['shipping']));

					if($amountDiff>0){
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
					}
					if($amountDiff<0){
					//$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
					}

					$costCenterDebitId= [];
					$costCenterDebitAmount= [];

					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryCreditAccount as $key => $acc){
							if($costDebitCount==0){
								$costCenterDebitId[] =json_decode($data['costCenterId']);
								$costCenterDebitAmount[] =json_decode($data['costCenterAmount']);



							}else{
								$costCenterDebitId[] ="";
								$costCenterDebitAmount[] ="";

								}
								//$this->sma->print_arrays($costCenterCreditAmount);
						      $costDebitCount++;
						}

					}



					$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);



			$payment_currency=$this->site->getCurrencyByCode($payment['currency']);

				if($payment_currency->auto_update == 1 && $payment_currency->rate != $payment['currency_rate'] && $data['currency_code'] != $this->Settings->default_currency){

					$data_curr = [
                'rate'         => $payment['currency_rate'],
					];
					$this->site->updateCurrency($payment_currency->id,$data_curr);
				}


			}

				}
				$final_item_cost_value=abs($this->getTotalItemCostValue($sale_id)->item_cost_value);

				if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
								}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


					$this->site->createEntryMultiSide($entryWarehouseAcc,$this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],"Cost_of_goods_sold",null,null,null,null,$data['date'],$entryDescription);
							}


			}




			}


        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Add:Sales_model.php)');
        } else {
            return $sale_id;
        }

        return false;
    }

    public function deleteDelivery($id)
    {
        if ($this->db->delete('deliveries', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteGiftCard($id)
    {
        if ($this->db->delete('gift_cards', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', ['id' => $id])) {
            $this->site->syncSalePayments($opay->sale_id);
            if ($opay->paid_by == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($opay->cc_no);
                $this->db->update('gift_cards', ['balance' => ($gc->balance + $opay->amount)], ['card_no' => $opay->cc_no]);
            } elseif ($opay->paid_by == 'deposit') {
                $sale     = $this->getInvoiceByID($opay->sale_id);
                $customer = $this->site->getCompanyByID($sale->customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount + $opay->amount)], ['id' => $customer->id]);
            }
            return true;
        }
        return false;
    }

    public function deleteSale($id)
    {
        $this->db->trans_off();
		$this->db->trans_start();
        $sale_items = $this->resetSaleActions($id);
        if ($this->db->delete('sale_items', ['sale_id' => $id]) && $this->db->delete('sales', ['id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {
            $this->db->delete('sales', ['sale_id' => $id]);
            $this->db->delete('payments', ['sale_id' => $id]);
            $this->site->syncQuantity(null, null, $sale_items);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Delete:Sales_model.php)');
        } else {
            return true;
        }
        return false;
    }

    public function getAllGCTopups($card_id)
    {
        $this->db->select("{$this->db->dbprefix('gift_card_topups')}.*, {$this->db->dbprefix('users')}.first_name, {$this->db->dbprefix('users')}.last_name, {$this->db->dbprefix('users')}.email")
        ->join('users', 'users.id=gift_card_topups.created_by', 'left')
        ->order_by('id', 'desc')->limit(10);
        $q = $this->db->get_where('gift_card_topups', ['card_id' => $card_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllInvoiceItems($sale_id, $return_id = null)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, cars_vehicles.brand_id,  cars_vehicles.model_id,  cars_vehicles.image, cars_vehicles.cf1 as cf1, cars_vehicles.cf2 as cf2, cars_vehicles.cf3 as cf3')
            ->join('cars_vehicles', 'cars_vehicles.vehicle_id =sale_items.product_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');
        if ($sale_id && !$return_id) {
            $this->db->where('sale_id', $sale_id);
        } elseif ($return_id) {
            $this->db->where('sale_id', $return_id);
        }
        $q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getProductInvoiceItem($sale_id, $pid = null)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, products.cf1 as cf1, products.cf2 as cf2, products.cf3 as cf3, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name, products.unit as base_unit_id, units.code as base_unit_code')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->join('units', 'units.id=products.unit', 'left')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');
            $this->db->where('sale_id', $sale_id);
            $this->db->where('sale_items.product_id', $pid);

		$q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllInvoiceItemsWithDetails($sale_id)
    {
        $this->db->select('sale_items.*, products.details, product_variants.name as variant');
        $this->db->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
        ->group_by('sale_items.id');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('sale_items', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getAllQuoteItems($quote_id)
    {
        $q = $this->db->get_where('quote_items', ['quote_id' => $quote_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
	public function getAllOfferItems($offer_id)
    {
        $q = $this->db->get_where('carsquote_items', ['quote_id' => $offer_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
    public function getCostingLines($sale_item_id, $product_id, $sale_id = null)
    {
        if ($sale_id) {
            $this->db->where('sale_id', $sale_id);
        }
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->order_by('id', $orderby);
        $q = $this->db->get_where('costing', ['sale_item_id' => $sale_item_id, 'product_id' => $product_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getDeliveryByID($id)
    {
        $q = $this->db->get_where('deliveries', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getDeliveryBySaleID($sale_id)
    {
        $q = $this->db->get_where('deliveries', ['sale_id' => $sale_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getInvoiceByID($id)
    {
        $q = $this->db->get_where('sales', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getInvoiceByHash($hash)
    {
		//$this->db->where("$this->bcrypt->verify($password, $hash_password_db->password)", NULL, FALSE);
        $q = $this->db->get_where('sales', ['hash' => $hash],1);
        if ($q->num_rows() > 0) {
			//if($this->bcrypt->verify($hash, $q->row()->hash))
            return $q->row();
        }
        return false;
    }

    public function getInvoicePayments($sale_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
	public function getInvoiceExpenses($sale_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('expenses', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('sale_items', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getItemRack($product_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            $wh = $q->row();
            return $wh->rack;
        }
        return false;
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getPaymentRevenueByCardID($card_id)
    {
        $q = $this->db->get_where('revenue', ['card_id' => $card_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
	public function getPaymentGiftCardsByID($id)
    {
        $q = $this->db->get_where('gift_cards', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPaymentsForSale($sale_id)
    {
        $this->db->select('payments.date, payments.paid_by, payments.amount, payments.cc_no, payments.cheque_no, payments.reference_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPaypalSettings()
    {
        $q = $this->db->get_where('paypal', ['id' => 1]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductByCode($code)
    {
        $q = $this->db->get_where('products', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductByName($name)
    {
        $q = $this->db->get_where('products', ['name' => $name], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductComboItems($pid, $warehouse_id = null)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name,products.type as type, warehouses_products.quantity as quantity, warehouses_products.avg_cost as avg_cost')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('combo_items.id');
        if ($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('combo_items', ['combo_items.product_id' => $pid]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }


    public function getProductNames2($term, $warehouse_id, $pos = false, $limit = 5)
    {
		$term = str_replace([")","(","'"],"",$term);
        $term = strip_tags($term);

        $wp = "( SELECT product_id, warehouse_id, quantity as quantity,avg_cost as avg_cost from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('products.*, FWP.quantity as quantity,FWP.avg_cost as avg_cost,FWP.warehouse_id as warehouse_id, categories.id as category_id, categories.name as category_name', false)
            ->join($wp, 'FWP.product_id=products.id', 'left')
            // ->join('warehouses_products FWP', 'FWP.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('products.id');
        if ($this->Settings->overselling) {
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        }
        // $this->db->order_by('products.name ASC');
        if ($pos) {
            $this->db->where('categories.hide_pos !=', 1);
        }
		$this->db->where('FWP.warehouse_id=', $warehouse_id);
        $this->db->where('hide !=', 1);
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductNames2Combo($term, $warehouse_id, $pos = false, $limit = 5)
    {
		$term = str_replace([")","(","'"],"",$term);
        $term = strip_tags($term);

        $wp = "( SELECT product_id, warehouse_id, quantity as quantity,avg_cost as avg_cost from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('products.*, FWP.quantity as quantity,FWP.avg_cost as avg_cost,FWP.warehouse_id as warehouse_id, categories.id as category_id, categories.name as category_name', false)
            ->join($wp, 'FWP.product_id=products.id', 'left')
            // ->join('warehouses_products FWP', 'FWP.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('products.id');
        //if ($this->Settings->overselling) {
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        /*} else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        */
        // $this->db->order_by('products.name ASC');
        if ($pos) {
            $this->db->where('categories.hide_pos !=', 1);
        }
		$this->db->where('FWP.warehouse_id=', $warehouse_id);
        $this->db->where('hide !=', 1);
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductNames($term, $warehouse_id, $pos = false, $limit = 5)
    {
		$term = str_replace([")","(","'"],"",$term);
        $term = strip_tags($term);



		//////serv
		$this->db->select('*', false);
        //$this->db->select('cars_vehicles.*', false)
            //->join($wp, 'FWP.product_id=products.id', 'left')
            // ->join('warehouses_products FWP', 'FWP.product_id=products.id', 'left')
           // ->group_by('cars_vehicles.id');
        /*if ($this->Settings->overselling) {
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        }
        // $this->db->order_by('products.name ASC');
        if ($pos) {
            $this->db->where('categories.hide_pos !=', 1);
        }*/
        //$this->db->select('*');
		    $this->db->where("({$this->db->dbprefix('cars_services')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('cars_services')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('cars_services')}.name, ' (', {$this->db->dbprefix('cars_services')}.code, ')') LIKE '%" . $term . "%')");

          //  $this->db->where('showroom_id', $warehouse_id);
            //$this->db->where('hide', 'available');
             $this->db->where('hide !=', 1);
        $this->db->limit($limit);
        $qserv = $this->db->get('cars_services');
        if ($qserv->num_rows() > 0) {
            foreach (($qserv->result()) as $row) {
                $data[] = $row;
            }
            //return $data;
        }

		//////end serv
        //$wp = "( SELECT product_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('cars_vehicles.vehicle_id as id,cars_vehicles.chesis_no as code,cars_vehicles.name as name,cars_vehicles.*', false);
        //$this->db->select('cars_vehicles.*', false)
            //->join($wp, 'FWP.product_id=products.id', 'left')
            // ->join('warehouses_products FWP', 'FWP.product_id=products.id', 'left')
           // ->join('cars_categories', 'cars_categories.id=cars_vehicles.category_id', 'left');
           // ->group_by('cars_vehicles.id');
        //$this->db->select('*');
		    $this->db->where("({$this->db->dbprefix('cars_vehicles')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('cars_vehicles')}.chesis_no LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('cars_vehicles')}.name, ' (', {$this->db->dbprefix('cars_vehicles')}.chesis_no, ')') LIKE '%" . $term . "%')");

            $this->db->where('showroom_id', $warehouse_id);
            $this->db->where('status', 'available');
             $this->db->where('active', 1);
        $this->db->limit($limit);
        $q = $this->db->get('cars_vehicles');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
        }
		return $data;

    }


	public function getProductNamesx($term, $warehouse_id, $pos = false, $limit = 5)
    {
		$term = str_replace([")","(","'"],"",$term);
        $term = strip_tags($term);

        $wp = "( SELECT product_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('products.*,FWP.quantity, categories.id as category_id, categories.name as category_name', false)
        //$this->db->select('products.id,categories.name as category_name,FWP.wpwarehouse_id', false)
            ->join($wp, 'FWP.product_id=products.id', 'left')
            // ->join('warehouses_products FWP', 'FWP.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('products.id');
        if ($this->Settings->overselling) {
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        }
        // $this->db->order_by('products.name ASC');
        if ($pos) {
            $this->db->where('categories.hide_pos !=', 1);
        }
		$this->db->where('FWP.warehouse_id=', $warehouse_id);
        $this->db->where('hide !=', 1);
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {

            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }


    public function getProductNamesCombo($term, $warehouse_id, $pos = false, $limit = 5)
    {
		$term = str_replace([")","(","'"],"",$term);
        $term = strip_tags($term);

        $wp = "( SELECT product_id, warehouse_id, quantity as quantity from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('products.*, FWP.quantity as quantity, categories.id as category_id, categories.name as category_name', false)
            ->join($wp, 'FWP.product_id=products.id', 'left')
            // ->join('warehouses_products FWP', 'FWP.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('products.id');
        //if ($this->Settings->overselling) {
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        /*} else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        }*/
        // $this->db->order_by('products.name ASC');
        if ($pos) {
            $this->db->where('categories.hide_pos !=', 1);
        }
		$this->db->where('FWP.warehouse_id=', $warehouse_id);
        $this->db->where('hide !=', 1);
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductOptions($product_id, $warehouse_id, $all = null)
    {
        $wpv = "( SELECT option_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_products_variants')} WHERE product_id = {$product_id}) FWPV";
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity', false)
            ->join($wpv, 'FWPV.option_id=product_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=product_variants.warehouse_id', 'left')
            ->where('product_variants.product_id', $product_id)
            ->group_by('product_variants.id');

        if (!$this->Settings->overselling && !$all) {
            $this->db->where('FWPV.warehouse_id', $warehouse_id);
            $this->db->where('FWPV.quantity >', 0);
        }
        $q = $this->db->get('product_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductQuantity($product_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse], 1);
        if ($q->num_rows() > 0) {
            return $q->row_array(); //$q->row();
        }
        return false;
    }

    public function getProductVariantByName($name, $product_id)
    {
        $q = $this->db->get_where('product_variants', ['name' => $name, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductVariants($product_id)
    {
        $q = $this->db->get_where('product_variants', ['product_id' => $product_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products_variants', ['option_id' => $option_id, 'warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPurchaseItemByID($id)
    {
        $q = $this->db->get_where('purchase_items', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getQuoteByID($id)
    {
        $q = $this->db->get_where('quotes', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getOfferByID($id)
    {
        $q = $this->db->get_where('carsquotes', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getReturnByID($id)
    {
        $q = $this->db->get_where('sales', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getReturnBySID($sale_id)
    {
        $q = $this->db->get_where('sales', ['sale_id' => $sale_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSaleCosting($sale_id)
    {
        $q = $this->db->get_where('costing', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSaleItemByID($id)
    {
        $q = $this->db->get_where('sale_items', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSkrillSettings()
    {
        $q = $this->db->get_where('skrill', ['id' => 1]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getStaff()
    {
        if (!$this->Owner) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getTaxRateByName($name)
    {
        $q = $this->db->get_where('tax_rates', ['name' => $name], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getWarehouseProduct($pid, $wid)
    {
        $this->db->select($this->db->dbprefix('products') . '.*, ' . $this->db->dbprefix('warehouses_products') . '.quantity as quantity')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left');
        $q = $this->db->get_where('products', ['warehouses_products.product_id' => $pid, 'warehouses_products.id' => $wid]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', ['warehouse_id' => $warehouse_id, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function resetSaleActions($id, $return_id = null, $check_return = null)
    {
        if ($sale = $this->getInvoiceByID($id)) {
            if ($check_return && $sale->sale_status == 'returned') {
                $this->session->set_flashdata('warning', lang('sale_x_action'));
                redirect(isset($_SERVER['HTTP_REFERER']) ?$_SERVER['HTTP_REFERER'] : 'welcome');
            }

            if ($sale->sale_status == 'completed') {
                if ($costings = $this->getSaleCosting($id)) {
                    foreach ($costings as $costing) {
                        if ($pi = $this->getPurchaseItemByID($costing->purchase_item_id)) {
                            $this->site->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        // $this->db->update('purchase_items', ['quantity_balance' => ($costing->quantity_balance + $costing->quantity)], ['id' => $pi->id]);
                        } else {
                            // $sale_item = $this->getSaleItemByID($costing->sale_item_id);
                            $pi = $this->site->getPurchasedItem(['product_id' => $costing->product_id, 'option_id' => $costing->option_id ? $costing->option_id : null, 'purchase_id' => null, 'transfer_id' => null, 'warehouse_id' => $sale->warehouse_id]);
                            $this->site->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        }
                    }
                    $this->db->delete('costing', ['id' => $costing->id]);
                }
                $items = $this->getAllInvoiceItems($id);
                $this->site->syncQuantity(null, null, $items);
                $this->sma->update_award_points($sale->grand_total, $sale->customer_id, $sale->created_by, true);
                return $items;
            }
        }
    }

    public function syncQuantity($sale_id)
    {
        if ($sale_items = $this->getAllInvoiceItems($sale_id)) {
            foreach ($sale_items as $item) {
                $this->site->syncProductQty($item->product_id, $item->warehouse_id);
                if (isset($item->option_id) && !empty($item->option_id)) {
                    $this->site->syncVariantQty($item->option_id, $item->warehouse_id);
                }
            }
        }
    }

    public function topupGiftCard($data = [], $card_data = null)
    {
        if ($this->db->insert('gift_card_topups', $data)) {
            $this->db->update('gift_cards', $card_data, ['id' => $data['card_id']]);
            return true;
        }
        return false;
    }

    public function UpdateCostingAndPurchaseItem($return_item, $product_id, $quantity)
    {
        $bln_quantity = $quantity;
        if ($costings = $this->getCostingLines($return_item['id'], $product_id)) {
            foreach ($costings as $costing) {
                if ($costing->quantity > $bln_quantity && $bln_quantity != 0) {
                    $qty = $costing->quantity                                                                                     - $bln_quantity;
                    $bln = $costing->quantity_balance && $costing->quantity_balance >= $bln_quantity ? $costing->quantity_balance - $bln_quantity : 0;
                    $this->db->update('costing', ['quantity' => $qty, 'quantity_balance' => $bln], ['id' => $costing->id]);
                    $bln_quantity = 0;
                    break;
                } elseif ($costing->quantity <= $bln_quantity && $bln_quantity != 0) {
                    $this->db->delete('costing', ['id' => $costing->id]);
                    $bln_quantity = ($bln_quantity - $costing->quantity);
                }
            }
        }
        $clause = ['product_id' => $product_id, 'warehouse_id' => $return_item['warehouse_id'], 'purchase_id' => null, 'transfer_id' => null, 'option_id' => $return_item['option_id']];
        $this->site->setPurchaseItem_return($clause, $quantity);
        $this->site->syncQuantity(null, null, null, $product_id);
    }

    public function updateDelivery($id, $data = [])
    {
        if ($this->db->update('deliveries', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateGiftCard($id, $data = [], $payment = [], $payment_type = null)
    {
        $this->db->where('id', $id);
        if ($this->db->update('gift_cards', $data)) {
				$ogc                  = $this->site->getGiftCardByID($id);
				$opay                  = $this->getPaymentRevenueByCardID($id);



			if($data['paid']==1){
				$reference=$this->site->getReference('re');

			$data2 = [
                'amount'       => $data['total_paid_amount'],
                'card_no'         => $data['card_no'],
                'customer_id'         => $data['customer_id'],
                'note'         => $data['note'],
                'category_id'  => "0",
                'warehouse_id' => $data['warehouse_id'],
                'paid_by' => $payment['paid_by'],
                'cheque_no' => $payment['cheque_no'],
                'cc_no' => $payment['cc_no'],
                'cc_holder' => $payment['cc_holder'],
                'cc_month' => $payment['cc_month'],
                'cc_year' => $payment['cc_year'],
                'cc_type' => $payment['cc_type'],
                'currency' => $payment['currency'],
                'currency_rate' => $payment['currency_rate'],
            ];
			if($this->updateRevenue($data2,$id)){

				if ($this->Settings->accounting){


					$entryDebitAccount= [];
						$entryDebitBalance= [];
							//$entrybillerAcc=$this->site->getAccountsSettings()->accounts_main_boxes_acct;
							$entrybillerAcc =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entrybillerAcc=="")
							$entrybillerAcc=$payment_type;


						$entryDebitAccount[] =  $entrybillerAcc;
						$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_paid_amount']);



						$entryCreditAccount= [];

						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_gift_card_sales;
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal($data['paid_amount']);
						$entryCreditBalance[] =  $this->sma->formatDecimal($data['tax_amount']);


					if($ogc->paid==0)
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference,'Gift_card_sales');
					else
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$opay->reference,'Gift_card_sales');

					}
				}

				}
            return true;
        }
        return false;
    }

    public function updateOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getProductOptionByID($option_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('product_variants', ['quantity' => $nq], ['id' => $option_id])) {
                return true;
            }
        }
        return false;
    }

    public function updatePayment($id, $data = [], $customer_id = null, $payment_type = null, $payment_status = null)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->update('payments', $data, ['id' => $id])) {
            $this->site->syncSalePayments($data['sale_id']);
            if ($opay->paid_by == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($opay->cc_no);
                $this->db->update('gift_cards', ['balance' => ($gc->balance + $opay->amount)], ['card_no' => $opay->cc_no]);
            } elseif ($opay->paid_by == 'deposit') {
                if (!$customer_id) {
                    $sale        = $this->getInvoiceByID($opay->sale_id);
                    $customer_id = $sale->customer_id;
                }
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount + $opay->amount)], ['id' => $customer->id]);
            }
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['cc_no']);
                $this->db->update('gift_cards', ['balance' => ($gc->balance - $data['amount'])], ['card_no' => $data['cc_no']]);
            } elseif ($customer_id && $data['paid_by'] == 'deposit') {
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $data['amount'])], ['id' => $customer_id]);
            }









			if ($this->Settings->accounting){
				$sale = $this->site->getSaleByID($data['sale_id']);



				if ($sale->sale_status == 'pending' || $sale->sale_status == 'completed'){



				$amount=$data['amount'];
				$amountPL=0;
				$amountDiff=0;
				//$currencyRate=$this->site->getCurrencyByCode($data['currency'])->rate;


				$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
				if($data['currency_rate'])
					$exchange_rate=$data['currency_rate'];
				else
					$exchange_rate=$default_currencyRate;

				if($sale->currency_rate)
					$currencyRate=$sale->currency_rate;
				else
					$currencyRate=1;







					//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

					$amount=$data['amount'] * $currencyRate;

					//if ($currencyRate != $exchange_rate){
						$amountPL=$data['amount'] * $exchange_rate;
						$amountDiff=$amountPL-$amount;
					//}
					//}






					$entry_type="Sales_Payments";
						/*if ($sale->sale_status == 'pending')
						$entry_type="sale_pending";
						if ($sale->sale_status == 'completed')
						$entry_type="sale_completed";
						*/


						$entryCreditAccount= [];
						$entryCreditBalance= [];

						if ($sale->sale_status == 'pending'){
						//$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_by_road_acct;









						$entryDebitAccount= [];
						//$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryDebitAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryDebitAcc=="")
							$entryDebitAcc=$payment_type;
							 $entryDebitAccount[] =  $entryDebitAcc;


						$entryDebitBalance= [];
						if($amountDiff==0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff<0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff>0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						}

						//$amount=$amount+$data['amount'];

						$commisson_amount=0;
						/*if($this->site->getPaymentTypeByID($payment_type)->commisson_perc!=0){

							$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->commisson_account;
							$commisson_amount =  $this->sma->formatDecimal(($data['amount']*$this->site->getPaymentTypeByID($payment_type)->commisson_perc/100));
							if($this->site->getPaymentTypeByID($payment_type)->tax_rate!=0){
								$commisson_amount =  $this->sma->formatDecimal($commisson_amount + ($commisson_amount*$this->site->getPaymentTypeByID($payment_type)->tax_rate /100));
							}
						$entryDebitBalance[] =  $commisson_amount;

						}*/

						$entryCreditAccount= [];
						$entryCreditBalance= [];
						if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
						}
						 $entryCreditAccount[] =  $entryDebitAcc;
						if($amountDiff==0){
						 $entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff>0){
						$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff<0){
						$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						}



						/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($sale->grand_total)){
						$entryDebitBalance[] =  $this->sma->formatDecimal($sale->grand_total - $data['amount']);

						}*/


						}else{
						if ($sale->sale_status == 'completed'){





						 $entryDebitAccount= [];
						 //$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryDebitAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryDebitAcc=="")
							$entryDebitAcc=$payment_type;
							 $entryDebitAccount[] =  $entryDebitAcc;






						$entryDebitBalance= [];
						if($amountDiff==0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff<0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff>0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						}

						$commisson_amount=0;
						/*if($this->site->getPaymentTypeByID($payment_type)->commisson_perc!=0){

							$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->commisson_account;
							$commisson_amount =  $this->sma->formatDecimal(($data['amount']*$this->site->getPaymentTypeByID($payment_type)->commisson_perc/100));
							if($this->site->getPaymentTypeByID($payment_type)->tax_rate!=0){
								$commisson_amount =  $this->sma->formatDecimal($commisson_amount + ($commisson_amount*$this->site->getPaymentTypeByID($payment_type)->tax_rate /100));
							}
						$entryDebitBalance[] =  $commisson_amount;

						}*/
						$entryCreditAccount= [];
						$entryCreditBalance= [];

						if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
						}
						 $entryCreditAccount[] =  $entryDebitAcc;
						if($amountDiff==0){
						 $entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff>0){
						$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff<0){
						$entryCreditBalance[] =  $this->sma->formatDecimal($amount);
						}






						}





						}








						if($opay->paid_by == 'deposit')
							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type);
						else
							$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type);










				}




				if ($sale->sale_status == 'returned'){

					$amount=abs($data['amount']);
				$amountPL=0;
				$amountDiff=0;
				//$currencyRate=$this->site->getCurrencyByCode($data['currency'])->rate;


				$default_currencyRate=$this->site->getCurrencyByCode($this->Settings->default_currency)->rate;
				if($data['currency_rate'])
					$exchange_rate=$data['currency_rate'];
				else
					$exchange_rate=$default_currencyRate;

				if($sale->currency_rate)
					$currencyRate=$sale->currency_rate;
				else
					$currencyRate=1;







					//if (isset($data['currency']) || ($this->Settings->default_currency != $data['currency'])){

					$amount=abs($data['amount']) * $currencyRate;

					//if ($currencyRate != $exchange_rate){
						$amountPL=abs($data['amount']) * $exchange_rate;
						$amountDiff=$amountPL-$amount;
					//}
					//}

						//if(isset($payment['currency_rate']) && $payment['currency_rate']!=1){
						//$payment['amount']=$payment['amount']*$payment['currency_rate'];
						//}


					$entry_type="sale_returned";


						$entryDebitAccount= [];
						 //$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryDebitAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryDebitAcc=="")
							$entryDebitAcc=$payment_type;
							 $entryDebitAccount[] =  $entryDebitAcc;






						$entryDebitBalance= [];
						//$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['amount'])+$commisson_amount);
						if($amountDiff==0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amount);
						}
						if($amountDiff<0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}
						if($amountDiff>0){
						$entryDebitBalance[] =  $this->sma->formatDecimal($amountPL);
						}

						$commisson_amount=0;
						/*if($this->site->getPaymentTypeByID($payment_type)->commisson_perc!=0){

							$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->commisson_account;
							$commisson_amount =  $this->sma->formatDecimal((abs($data['amount'])*$this->site->getPaymentTypeByID($payment_type)->commisson_perc/100));
							if($this->site->getPaymentTypeByID($payment_type)->tax_rate!=0){
								$commisson_amount =  $this->sma->formatDecimal($commisson_amount + ($commisson_amount*$this->site->getPaymentTypeByID($payment_type)->tax_rate /100));
							}
						$entryDebitBalance[] =  $commisson_amount;

						}*/
						$entryCreditAccount= [];
						$entryCreditBalance= [];

						if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
						}
						 $entryCreditAccount[] =  $entryDebitAcc;
						 $entryCreditBalance[] =  $this->sma->formatDecimal(abs($amount));
						 if($amountDiff>0){
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}





						$this->site->updateEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$sale->reference_no,$entry_type);








				}

				}


			$payment_currency=$this->site->getCurrencyByCode($data['currency']);

				if($payment_currency->auto_update == 1 && $payment_currency->rate != $data['currency_rate'] && $sale->currency_code != $this->Settings->default_currency){

					$data_curr = [
                'rate'         => $data['currency_rate'],
					];
					$this->site->updateCurrency($payment_currency->id,$data_curr);
				}










            return true;
        }
        return false;
    }

    public function updateProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', ['quantity' => $nq], ['option_id' => $option_id, 'warehouse_id' => $warehouse_id])) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return true;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq])) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return true;
            }
        }
        return false;
    }

    public function updateSale($id, $data, $items = [])
    {
        $this->db->trans_off();
		$this->db->trans_start();
		$Osale = $this->site->getSaleByID($id);

        //$this->resetSaleActions($id, false, true);

        if ($data['sale_status'] == 'completed') {
           // $this->Settings->overselling = true;
            //$cost                        = $this->site->costing($items, true);
        }
        // $this->sma->print_arrays($cost);



        //if ($this->db->update('sales', $data, ['id' => $id]) && $this->db->delete('sale_items', ['sale_id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {
            if ($this->db->update('sales', $data, ['id' => $id]) && $this->db->delete('sale_items', ['sale_id' => $id])) {
                foreach ($items as $item) {



				{

				$wp=$this->getavg_costProduct($item['product_id'],$item['warehouse_id']);
				if($wp){
					if (!empty($si_return)) {
						$sale_item=$this->getSaleItemBysale_id($data['sale_id'],$item['product_id']);
						$qtyOS=0;
						$qtyOS=$sale_item->quantity_overselling;
						$new_qtyOS=0;


						$qty=0;
						if($wp->quantity < 0)
						$qty=$wp->quantity - $item['quantity'];
						else
						$qty=$wp->quantity + $item['quantity'];

					if($wp->quantity < 0){
						if($qty > 0){
							$new_qtyOS=0;
							$item['item_cost_value']=$this->sma->formatDecimal((0 - $qty) * $sale_item->avg_cost,12);
							$item['avg_cost']=$sale_item->avg_cost;


						}else{
							$new_qtyOS=(0 - $qty);
							$item['item_cost_value']=0;
							$item['avg_cost']=0;

						}




					}else{
							$new_qtyOS=0;
							$item['item_cost_value']=$this->sma->formatDecimal($item['quantity'] * $sale_item->avg_cost,12);
							$item['avg_cost']=$sale_item->avg_cost;




					}
					if($qtyOS>0)
						$this->db->update('sale_items', ['quantity_overselling' => $new_qtyOS], ['id' => $sale_item->id]);
					/*
					$overselling_qty=0;
					$new_qty=0;
					if($wp->quantity < 0)
					$overselling_qty=	(0-$qty);
					else
					$overselling_qty=	(0-$item['quantity']);

					if($sale_item->overselling==1){
							if($sale_item->quantity_overselling>0){
								if($sale_item->quantity_overselling >= $overselling_qty){
									$new_qty=$sale_item->quantity_overselling - $overselling_qty;
									if($new_qty<=0)
									$this->db->update('sale_items', ['overselling' => 0,'quantity_overselling' => $new_qty], ['id' => $sale_item->id]);
									else
									$this->db->update('sale_items', ['quantity_overselling' => $new_qty], ['id' => $sale_item->id]);

								}else{
									$this->db->update('sale_items', ['overselling' => 0,'quantity_overselling' => 0], ['id' => $sale_item->id]);

								}
							}else{
									$this->db->update('sale_items', ['overselling' => 0,'quantity_overselling' => 0], ['id' => $sale_item->id]);

							}

							}
							*/



					}else{
					if($item['quantity'] > $wp->quantity){
						if($wp->quantity > 0){
							$item['item_cost_value']=$this->sma->formatDecimal($wp->quantity * $wp->avg_cost,12);
							$item['avg_cost']=$wp->avg_cost;
							$item['overselling']=1;
							$item['quantity_overselling']=($item['quantity']-$wp->quantity);


						}else{
							$item['item_cost_value']=0;
							$item['avg_cost']=0;
							$item['overselling']=1;
							$item['quantity_overselling']=($item['quantity']);


						}

					}else{
						$item['item_cost_value']=$this->sma->formatDecimal($item['quantity'] * $wp->avg_cost,12);
						$item['avg_cost']=$wp->avg_cost;

					}

					}
				}
				}



                $item['sale_id'] = $id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
				if($item['product_type']=='standard'){

                if ($data['sale_status'] == 'completed') {
				    $this->db->update('cars_vehicles', ['status' => 'sold'], ['vehicle_id' => $item['product_id']]);
                }else{
                    $this->db->update('cars_vehicles', ['status' => 'available'], ['vehicle_id' => $item['product_id']]);

                }
			}
                    /* $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
                            $item_cost['sale_item_id'] = $sale_item_id;
                            $item_cost['sale_id']      = $id;
                            $item_cost['date']         = date('Y-m-d', strtotime($data['date']));
                            if (!isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        } else {
                            foreach ($item_cost as $ic) {
                                $ic['sale_item_id'] = $sale_item_id;
                                $ic['sale_id']      = $id;
                                $item_cost['date']  = date('Y-m-d', strtotime($data['date']));
                                if (!isset($ic['pi_overselling'])) {
                                    $this->db->insert('costing', $ic);
                                }
                            }
                        }
                    }
                }*/






            }

            if ($data['sale_status'] == 'completed') {
               // $this->site->syncPurchaseItems($cost);
            }




            $this->site->syncSalePayments($id);
            //$this->site->syncQuantity($id);
            $sale = $this->getInvoiceByID($id);

            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $sale->created_by);




			if ($this->Settings->accounting){


				if(isset($data['currency_rate']) && $data['currency_rate']!=1){
					$data['grand_total'] = $data['grand_total'] * $data['currency_rate'];
					$data['total_tax'] = $data['total_tax'] * $data['currency_rate'];
					$data['shipping'] = $data['shipping'] * $data['currency_rate'];

				}
				$entryDescription="Sales_Invoice_No#".$id;

				if ($data['sale_status'] == 'pending'){

				$entry_type="";
					if ($data['sale_status'] == 'pending')
					$entry_type="sale_pending";



				if($this->site->getCompanyByID($data['customer_id'])->co_type==1){
					$entryCreditAccount= [];

					if ($data['sale_status'] == 'pending')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_receiving_acct;
					if ($data['sale_status'] == 'completed'){
						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

					$entryCreditAccount[] =  $entryWarehouseAcc;
					//$entryCreditAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;

					}

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryDebitAccount=$this->site->getAccountByRefId($data['customer_id'],'local_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_local_customers_acct;

					if($Osale->sale_status == 'pending'){
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date']);
					}else{
					    $this->site->updateEntryMultiSideWithDelete($data['reference_no'],$entryDescription);
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);
					}

				}

				if($this->site->getCompanyByID($data['customer_id'])->co_type==2){
					$entryCreditAccount= [];

					if ($data['sale_status'] == 'pending')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_receiving_acct;
					if ($data['sale_status'] == 'completed'){
						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

					$entryCreditAccount[] =  $entryWarehouseAcc;
					//$entryCreditAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
					}

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryDebitAccount=$this->site->getAccountByRefId($data['customer_id'],'international_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_international_customers_acct;

					//$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type);
					if($Osale->sale_status == 'pending'){
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date']);
					}else{
					    $this->site->updateEntryMultiSideWithDelete($data['reference_no'],$entryDescription);
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);
					}


				}




			}else{
				if ($data['sale_status']  == 'completed' && $Osale->sale_status != 'completed'){



				$entry_type="";
					if ($data['sale_status'] == 'completed')
					$entry_type="sale_completed";



				    $this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_store_goods_receiving_acct,$this->site->getAccountsSettings()->sales_acct,$this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']),$this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']),$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);

					$final_item_cost_value=$this->getTotalItemCostValue($sale->id)->item_cost_value;

					if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
							}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


					$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);
							}

				}else{
					if ($data['sale_status']  == 'completed' && $Osale->sale_status == 'completed'){



				$entry_type="";
					if ($data['sale_status'] == 'completed')
					$entry_type="sale_completed";

				$entryCreditAccount= [];
					$entryCreditBalance= [];


					if ($data['sale_status'] == 'pending')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_receiving_acct;
					if ($data['sale_status'] == 'completed')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_acct;


					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;
					if(abs($data['shipping'])>0)
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_shipping_other_revenues;


					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']+$final_commisson_amount);
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['total_tax']);
					if(abs($data['shipping'])>0)
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['shipping']);

					if($this->sma->formatDecimal($amount)<$this->sma->formatDecimal($data['grand_total'])){

					if($this->site->getCompanyByID($data['customer_id'])->co_type==1){
					$entryDebitAcc=$this->site->getAccountByRefId($data['customer_id'],'local_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
					}else{
					$entryDebitAcc=$this->site->getAccountByRefId($data['customer_id'],'international_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
					}
					$entryDebitAccount[] =  $entryDebitAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$amount);




					}


					$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,$entryDescription);






				$final_item_cost_value=$this->getTotalItemCostValue($sale->id)->item_cost_value;

				if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
								}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


					$this->site->updateEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],'Cost_of_goods_sold',null,null,null,null,$data['date'],$entryDescription);

							}
				}

				}
			}




			}

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Update:Sales_model.php)');
        } else {
            return true;
        }
        return false;
    }

    public function updateStatus($id, $status, $note)
    {
        $this->db->trans_off();
		$this->db->trans_start();
        $sale  = $this->getInvoiceByID($id);
        $items = $this->getAllInvoiceItems($id);
        $cost  = [];
        if ($status == 'completed' && $sale->sale_status != 'completed') {
            foreach ($items as $item) {
                $items_array[] = (array) $item;
            }
            $cost = $this->site->costing($items_array);
        }
        if ($status != 'completed' && $sale->sale_status == 'completed') {
            $this->resetSaleActions($id);
        }

        if ($this->db->update('sales', ['sale_status' => $status, 'note' => $note], ['id' => $id])) {
            //if ($status == 'completed') {
                foreach ($items as $item) {
                    $item = (array) $item;
                    if($item['product_type']=='standard'){

                    if ($status == 'completed') {
                        $this->db->update('cars_vehicles', ['status' => 'sold'], ['vehicle_id' => $item['product_id']]);
                    }else{
                        $this->db->update('cars_vehicles', ['status' => 'available'], ['vehicle_id' => $item['product_id']]);

                    }
				}


                }
            //}

            if (!empty($cost)) {
                $this->site->syncPurchaseItems($cost);
            }
            $this->site->syncQuantity($id);


			if ($this->Settings->accounting){


				if(isset($sale->currency_rate) && $sale->currency_rate!=1){
					$sale->grand_total = $sale->grand_total * $sale->currency_rate;
					$sale->total_tax = $sale->total_tax * $sale->currency_rate;
					$sale->shipping = $sale->shipping * $sale->currency_rate;

				}
				$entryDescription="Sales_Invoice_No#".$sale->id;

				if ($status == 'pending'){

				$entry_type="";
					if ($status == 'pending')
					$entry_type="sale_pending";



				if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
					$entryCreditAccount= [];

					if ($status == 'pending')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_receiving_acct;
					if ($status == 'completed'){
						$entryWarehouseAcc=$this->site->getAccountByRefId($sale->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

					$entryCreditAccount[] =  $entryWarehouseAcc;
					//$entryCreditAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;

					}

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sale->grand_total-$sale->total_tax);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sale->total_tax);

					$entryDebitAccount=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_local_customers_acct;

					if($sale->sale_status == 'pending'){
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,$sale->date);
					}else{
					    $this->site->updateEntryMultiSideWithDelete($sale->reference_no,$entryDescription);
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription);
					}

				}

				if($this->site->getCompanyByID($sale->customer_id)->co_type==2){
					$entryCreditAccount= [];

					if ($status == 'pending')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_receiving_acct;
					if ($status == 'completed'){
						$entryWarehouseAcc=$this->site->getAccountByRefId($sale->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

					$entryCreditAccount[] =  $entryWarehouseAcc;
					//$entryCreditAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
					}

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sale->grand_total-$sale->total_tax);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sale->total_tax);

					$entryDebitAccount=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_international_customers_acct;

					//$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type);
					if($sale->sale_status == 'pending'){
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,$sale->date);
					}else{
					    $this->site->updateEntryMultiSideWithDelete($sale->reference_no,$entryDescription);
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription);
					}


				}




			}else{
				if ($status  == 'completed' && $sale->sale_status != 'completed'){



				$entry_type="";
					if ($status == 'completed')
					$entry_type="sale_completed";
				/*
				$costCenterCreditId= [];
					$costCenterCreditAmount= [];


					if($sale->costCenterId){
						//$costCenterCreditId[] =((Array)$sale->costCenterId);
						//$costCenterCreditAmount[] =((Array)$sale->costCenterAmount);

						$costCenterCreditId[] ="";
								$costCenterCreditAmount[] ="";

					}else	{
								$costCenterCreditId[] ="";
								$costCenterCreditAmount[] ="";

					}
					$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_store_goods_receiving_acct,$this->site->getAccountsSettings()->sales_acct,$this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping),$this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping),$sale->reference_no,$entry_type,null,null,$costCenterCreditId, $costCenterCreditAmount,$sale->date,$entryDescription);
					 */


				    $this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_store_goods_receiving_acct,$this->site->getAccountsSettings()->sales_acct,$this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping),$this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping),$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription);

					$final_item_cost_value=$this->getTotalItemCostValue($sale->id)->item_cost_value;

					if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
							}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($sale->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


					$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription);
							}

				}else{
					if ($status  == 'completed' && $sale->sale_status == 'completed'){



				$entry_type="";
					if ($status == 'completed')
					$entry_type="sale_completed";

				$entryCreditAccount= [];
					$entryCreditBalance= [];


					if ($status == 'pending')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_receiving_acct;
					if ($status == 'completed')
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_acct;


					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;
					if(abs($sale->shipping)>0)
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_shipping_other_revenues;


					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping+$final_commisson_amount);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sale->total_tax);
					if(abs($sale->shipping)>0)
					$entryCreditBalance[] =  $this->sma->formatDecimal($sale->shipping);

					if($this->sma->formatDecimal($amount)<$this->sma->formatDecimal($sale->grand_total)){

					if($this->site->getCompanyByID($sale->customer_id)->co_type==1){
					$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'local_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
					}else{
					$entryDebitAcc=$this->site->getAccountByRefId($sale->customer_id,'international_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_customers_acct;
					}
					$entryDebitAccount[] =  $entryDebitAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($sale->grand_total-$amount);




					}


					$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,null,$entryDescription);






				$final_item_cost_value=$this->getTotalItemCostValue($sale->id)->item_cost_value;

				if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
								}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($sale->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


					$this->site->updateEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$sale->reference_no,'Cost_of_goods_sold',null,null,null,null,$sale->date,$entryDescription);

							}
				}

				}
			}




			}




        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (UpdataStatus:Sales_model.php)');
        } else {
            return true;
        }
        return false;
    }


	public function getavg_costProduct_old($product_id, $warehouse_id)
    {
        $this->db->select('cost as avg_cost')->select('1 as quantity');
		$this->db->where('vehicle_id =', $product_id);
        $this->db->where('showroom_id =', $warehouse_id);
        $q = $this->db->get('cars_vehicles');
        if ($q->num_rows() > 0) {
            return $q->row();
		}
        return false;
    }

	public function getavg_costProduct($product_id, $warehouse_id)
    {
        $this->db->select('avg_cost')->select('quantity');
		$this->db->where('product_id =', $product_id);
        $this->db->where('warehouse_id =', $warehouse_id);
        $q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {
            return $q->row();
		}
        return false;
    }

	public function getSaleItemBysale_id($sale_id, $product_id)
    {
        $q = $this->db->get_where('sale_items', ['sale_id' => $sale_id,'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();

        }
        return false;
    }

	public function getSaleItemByid_sale_id($sale_id, $product_id, $id)
    {

		//$q = $this->db->get_where('sale_items', ['sale_id' => $sale_id,'product_id' => $product_id,'id' => $id], 1);
		$this->db->select('*');
		//$this->db->where('sale_id >', $sale_id);
        $this->db->where('product_id =', $product_id);
		//$this->db->where('quantity <', 0);

        $this->db->where('sale_item_id =', $id);
		$this->db->order_by('id', 'desc');
        $q = $this->db->get('sale_items', 1);

        if ($q->num_rows() > 0) {
            return $q->row();

        }
        return false;
    }

	    public function getSumPurchaseItemsCost($warehouse_id, $product_id)
    {
        //$this->db->select_sum('item_cost_value');
		$this->db->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (quantity_received) ELSE (unit_quantity) END) as quantity')->select('SUM(item_cost_value) as item_cost_value');//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')

        $this->db->where('warehouse_id =', $warehouse_id);
        $this->db->where('product_id =', $product_id);
        $this->db->where('purchase_id >', 0);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


 public function getCombOverselling($item_id,$product_id,$sale_id)
    {


		$this->db->select('*');//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')

        $this->db->where('item_id =', $item_id);
        $this->db->where('product_id =', $product_id);
        $this->db->where('sale_id =', $sale_id);
        $q = $this->db->get('combo_overselling',1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

  public function getCombavg_cost($item_id,$product_id,$sale_id)
    {


		$this->db->select('*');//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')

        $this->db->where('item_id =', $item_id);
        $this->db->where('product_id =', $product_id);
        $this->db->where('sale_id =', $sale_id);
        $q = $this->db->get('combo_avg_cost',1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getUser($id = null)
    {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	    public function getWarehouseByID($id)
    {
        $q = $this->db->get_where('warehouses', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	    public function getSettings()
    {
		$this->db->select('*');
		$this->db->limit(1);

        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getAllUnits()
    {
        $q = $this->db->get('units');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


 public function getUnitByCode($code)
    {
        $q = $this->db->get_where('units', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	public function getBuyGetFreePromoByPId($offer_product,$warehouse_id=null)
    {
        $this->db->where('warehouse_id', $warehouse_id)->or_where('warehouse_id IS NULL');
		$q = $this->db->get_where('promo', ['offer_product' => $offer_product,'free_product' => $offer_product,'promo_type_id' => 3,'status' => 1], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

   public function getDiscountPromoByPId($offer_product,$warehouse_id=null)
    {
        $this->db->where('warehouse_id', $warehouse_id)->or_where('warehouse_id IS NULL');
		$q = $this->db->get_where('promo', ['offer_product' => $offer_product,'free_product' => $offer_product,'promo_type_id' => 3,'status' => 1], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

 public function getTotalItemCostValue($sale_id)
    {
        $this->db->select('SUM(item_cost_value) as item_cost_value');
		$q = $this->db->get_where('sale_items', ['sale_id' => $sale_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getSaleByReference($ref)
    {
        $this->db->where('reference_no', $ref);
        $q = $this->db->get('sales', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getTaxRatesActive()
    {
        $this->db->select('rate');
		$this->db->where('rate >', 0);
        $this->db->where('tax_active =', 1);
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }else{
			return $this->getTaxRatesLast();
		}
        return false;
    }

    public function getTaxRatesLast()
    {
        $this->db->select('rate');
		$this->db->where('rate >', 0)->order_by('id', 'desc')->limit(1);
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getVehicleByID($vehicle_id)
    {
        $q = $this->db->get_where('cars_vehicles', ['vehicle_id' => $vehicle_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	public function getInvoiceQuotesPayments($quote_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', ['quote_id' => $quote_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }


	public function getTotalQuotesPayments($quote_id)
    {
        $this->db->select('COALESCE(SUM(amount),0) as amount');
		$q = $this->db->get_where('payments', ['quote_id' => $quote_id],1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

    }



}
