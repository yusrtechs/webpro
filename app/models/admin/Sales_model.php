<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addDelivery($data = [])
    {
        if ($this->db->insert('deliveries', $data)) {
            if ($this->site->getReference('do',$data['brId'] ? $data['brId']  : null) == $data['do_reference_no']) {
                $this->site->updateReference('do',$data['brId'] ? $data['brId']  : null);
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
            $reference=$this->site->getReference('re',$data['brId'] ? $data['brId']  : null);
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

					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference,'Gift_card_sales',$data['user_id'],null,null,null,null,null,$data['grpId'],$data['coId'],$data['brId']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference,'Gift_card_sales',$data['user_id']);
					}


				}

			}
            return true;
        }
        return false;
    }

    public function getGiftCardTypeStats()
    {
        $this->db->select('gift_card_type, COUNT(*) as total, SUM(value) as total_val')
            ->where_in('gift_card_type', ['classic', 'golden', 'elegant', 'modern'])
            ->group_by('gift_card_type');
        $q = $this->db->get('gift_cards');

        $stats = [
            'classic' => ['count' => 0, 'value' => 0, 'percentage' => 0],
            'golden'  => ['count' => 0, 'value' => 0, 'percentage' => 0],
            'elegant' => ['count' => 0, 'value' => 0, 'percentage' => 0],
            'modern'  => ['count' => 0, 'value' => 0, 'percentage' => 0],
        ];

        $total_typed = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $type = strtolower($row->gift_card_type);
                if (isset($stats[$type])) {
                    $stats[$type]['count'] = (int) $row->total;
                    $stats[$type]['value'] = (float) $row->total_val;
                    $total_typed += (int) $row->total;
                }
            }
        }

        if ($total_typed > 0) {
            foreach ($stats as $k => $v) {
                $stats[$k]['percentage'] = round(($v['count'] / $total_typed) * 100, 1);
            }
        }

        return $stats;
    }

    public function addRevenue($data = [])
    {
        if ($this->db->insert('revenue', $data)) {
            if ($this->site->getReference('re',$data['brId'] ? $data['brId']  : null) == $data['reference']) {
                $this->site->updateReference('re',$data['brId'] ? $data['brId']  : null);
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
            if ($this->site->getReference('pay',$data['brId'] ? $data['brId']  : null) == $data['reference_no']) {
                $this->site->updateReference('pay',$data['brId'] ? $data['brId']  : null);
            }
            $this->site->syncSalePayments($data['sale_id']);
            if ($data['paid_by'] == 'gift_card') {
                //if($data['cc_no']){
					$gc = $this->site->getGiftCardByNO($data['cc_no']);
					$this->db->update('gift_cards', ['balance' => ($gc->balance - $data['amount'])], ['card_no' => $data['cc_no']]);
				/*}else{
					$sale_org = $this->site->getSaleByID($data['sale_id']);
					$this->sma->update_award_pointsV2($data['amount'], $sale_org->customer_id, $sale_org->created_by, true);
				}*/

            } elseif ($customer_id && $data['paid_by'] == 'deposit') {
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $data['amount'])], ['id' => $customer_id]);
            }





			if ($this->Settings->accounting && $data['paid_by']!="deposit"){
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					if($sale_no=$this->getInvoiceSaleNoByById($data['sale_id'])){
						$entryDescription="Payments_Sales_Invoice_No#".$sale_no;
					}else{
						$entryDescription="Payments_Sales_Invoice_No#".$data['sale_id'];
					}
				}else{
					$entryDescription="Payments_Sales_Invoice_No#".$data['sale_id'];
				}


			$sale = $this->site->getSaleByID($data['sale_id']);



			if ($sale->sale_status == 'pending' || $sale->sale_status == 'completed'){



			if($data['donation']!=0){
				$data['amount']=$data['amount']-$data['donation'];
			}

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







					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency'],$data['currency_rate']);
					}



					if($data['donation']!=0){
						$entry_type="Donations_due_to_charities";
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
							if($sale_no=$this->getInvoiceSaleNoByById($data['sale_id'])){
								$entryDescription="Donations_Payments_Sales_Invoice_No#".$sale_no;
							}else{
								$entryDescription="Donations_Payments_Sales_Invoice_No#".$data['sale_id'];
							}
						}else{
							$entryDescription="Donations_Payments_Sales_Invoice_No#".$data['sale_id'];
						}

						$donation=$data['donation'] * $currencyRate;






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

					$entryDebitBalance[] =  $this->sma->formatDecimal($donation);

					$entryCreditAccount= [];
					$entryCreditBalance= [];

					$entryCrediAcc=$this->site->getAccountsSettings()->donations_acct;
						if($entryCrediAcc=="")
						$entryCrediAcc=$this->site->getAccountsSettings()->expenses_shipping_acct;
					 	$entryCreditAccount[] =  $entryCrediAcc;


					$entryCreditBalance[] =  $this->sma->formatDecimal($donation);


					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency'],$data['currency_rate']);
					}



					}




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






					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$sale->reference_no,$entry_type,null,null,null,null,null,$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$sale->reference_no,$entry_type,null,null,null,null,null,null,null,null,null,null,null,$data['currency'],$data['currency_rate']);
					}










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

    public function addSale($data = [], $items = [], $payment = [], $si_return = [], $payment_type = [])
    {
        if (empty($si_return)) {
            $cost = $this->site->costing($items);
            // $this->sma->print_arrays($cost);
        }


        $this->db->trans_off();
		$this->db->trans_start();
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            if ($this->site->getReference('so',$data['brId'] ? $data['brId']  : null) == $data['reference_no']) {
                $this->site->updateReference('so',$data['brId'] ? $data['brId']  : null);
            }
            foreach ($items as $item) {
				if ($item['product_type'] == 'combo') {
                		$item_cost_value=0;
						$avg_cost=0;
				        $combo_items_qty = $this->site->getProductComboItems($item['product_id'], $item['warehouse_id']);
                        /*$total_qty=0;
						foreach ($combo_items_qty as $combo_item) {
						    $total_qty=$total_qty+$combo_item->qty;
                        }
						*/


						$combo_items = $this->site->getProductComboItems($item['product_id'], $item['warehouse_id']);
                        foreach ($combo_items as $combo_item) {
							$quantity=$item['quantity'] * $combo_item->qty;
                            $wp=$this->getavg_costProduct($combo_item->id,$item['warehouse_id']);
				if($wp){
					if (!empty($si_return)) {
						$sale_item=$this->getSaleItemBysale_id($data['sale_id'],$item['product_id']);
						$cos_quantity=0;
						$cos_quantities=0;
						if($this->getCombOverselling($combo_item->id,$item['product_id'],$data['sale_id'])){
						$cos=$this->getCombOverselling($combo_item->id,$item['product_id'],$data['sale_id']);
						$cos_quantity=$cos->quantity_overselling;
						}
				        $cavg=$this->getCombavg_cost($combo_item->id,$item['product_id'],$data['sale_id']);
				        $new_avg_cost=$cavg->avg_cost;
						$qty=0;
						if($wp->quantity < 0)
						$qty=$wp->quantity - $quantity;
						else
						$qty=$wp->quantity + $quantity;

					if($wp->quantity < 0){
					if($qty > 0){
							$actual_quantity=$this->sma->formatDecimal((0 - $qty)+$cavg->quantity);
							if($cos_quantity>0)
							$cos_quantities=0;//$this->sma->formatDecimal((0 - $qty)+$cos_quantity);
							$actual_item_cost_value=$this->sma->formatDecimal(((0 - $qty) * ($new_avg_cost))+$cavg->item_cost_value,12);
							$item_cost_value=$this->sma->formatDecimal($item_cost_value+(((0 - $qty) * ($new_avg_cost))),12);
							//$avg_cost=$this->sma->formatDecimal($avg_cost+$sale_item->avg_cost,12);


					}else{
							$actual_quantity=$cavg->quantity;
							if($cos_quantity>0)
							$cos_quantities=$this->sma->formatDecimal((0 - $qty));
							$actual_item_cost_value=$cavg->item_cost_value;
							$item_cost_value=$item_cost_value;
							//$avg_cost=$avg_cost;

					}

					}else{
							$actual_quantity=$this->sma->formatDecimal($quantity+$cavg->quantity);
							if($cos_quantity>0)
							$cos_quantities=0;//$this->sma->formatDecimal($quantity+$cos_quantity);
							$actual_item_cost_value=$this->sma->formatDecimal(($quantity * ($new_avg_cost))+$cavg->item_cost_value,12);
							$item_cost_value = $this->sma->formatDecimal($item_cost_value+($quantity * ($new_avg_cost)),12);
							//$avg_cost		 = $this->sma->formatDecimal($avg_cost+$sale_item->avg_cost,12);



					}


						$avg_cost=$this->sma->formatDecimal($sale_item->avg_cost,12);
						$this->db->update('combo_avg_cost', ['quantity' => $actual_quantity,'item_cost_value' => $actual_item_cost_value], ['id' => $cavg->id]);
						if($cos_quantity>0){
							if($cos_quantities<0)
								$cos_quantities=0;
						$this->db->update('combo_overselling', ['quantity_overselling' => $cos_quantities], ['id' => $cos->id]);
						}



					}else{
					if($quantity > $wp->quantity){
						if($wp->quantity > 0){
							$actual_quantity=$wp->quantity;
							$actual_item_cost_value=$this->sma->formatDecimal($wp->quantity * $wp->avg_cost,12);
							$item_cost_value=$this->sma->formatDecimal($item_cost_value+($wp->quantity * $wp->avg_cost),12);
							$avg_cost=$this->sma->formatDecimal($avg_cost+($wp->avg_cost* $combo_item->qty),12);
							$item['overselling']=1;
							$this->db->insert('combo_overselling', ['quantity_overselling' => ($quantity-$wp->quantity), 'product_id' => $item['product_id'], 'item_id' => $combo_item->id, 'sale_id' => $sale_id]);




						}else{
							$actual_quantity=0;
							$actual_item_cost_value=0;
							$item_cost_value=$item_cost_value;
							$avg_cost=$avg_cost;
							$item['overselling']=1;
							$this->db->insert('combo_overselling', ['quantity_overselling' => $quantity, 'product_id' => $item['product_id'], 'item_id' => $combo_item->id, 'sale_id' => $sale_id]);



						}

					}else{
						$actual_quantity=$quantity;
						$actual_item_cost_value=$this->sma->formatDecimal($quantity * $wp->avg_cost,12);
						$item_cost_value=$this->sma->formatDecimal($item_cost_value+($quantity * $wp->avg_cost),12);
						$avg_cost=$this->sma->formatDecimal($avg_cost+($wp->avg_cost* $combo_item->qty),12);


					}
						$this->db->insert('combo_avg_cost', ['quantity' => $actual_quantity,'item_cost_value' => $actual_item_cost_value,'avg_cost' => ($wp->avg_cost), 'product_id' => $item['product_id'], 'item_id' => $combo_item->id, 'sale_id' => $sale_id]);

					}
				}

						}
						$item['item_cost_value']=$item_cost_value;
						$item['avg_cost']=$avg_cost;

                }else{

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
				if (!empty($si_return) && $item['product_type'] == 'manual') {
					$item['avg_cost'] = 0;
				}
                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && empty($si_return)) {
                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
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
                        }
                    }
                }
				if ($data['sale_status'] == 'completed' && $item['product_type'] != 'manual') {
                	if ($this->Settings->update_price) {
                		if(isset($data['currency_rate']) && $data['currency_rate']!=1){
				    		$this->db->update('products', ['price' => $item['unit_price'] * $data['currency_rate']], ['id' => $item['product_id']]);
						}else{
				    		$this->db->update('products', ['price' => $item['unit_price']], ['id' => $item['product_id']]);
						}
						$this->db->update('synch_products', ['api' => 0], ['product_code' => $item['product_code']]);
                	}
            	}
            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }

            if (!empty($si_return)) {
                foreach ($si_return as $return_item) {
                    $product = $this->site->getProductByID($return_item['product_id']);

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

					}
                $this->db->update('sales', ['return_sale_ref' => $data['return_sale_ref'], 'surcharge' => $data['surcharge'], 'return_sale_total' => $data['grand_total'], 'return_id' => $sale_id], ['id' => $data['sale_id']]);
            }

			if ($data['debit_id']) {
                $this->db->update('sales', ['return_debit_id' => $sale_id], ['id' => $data['debit_id']]);
            }

			if ($data['credit_id']) {
                $this->db->update('sales', ['return_credit_id' => $sale_id], ['id' => $data['credit_id']]);
            }

            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' && !empty($payment)) {
				if (empty($si_return)) {
					 $paid = 0;
                foreach ($payment as $payments) {
                    if (!empty($payments) && isset($payments['amount']) && $payments['amount'] != 0) {
                        $payments['sale_id']      = $sale_id;
                        $payments['reference_no'] = $this->site->getReference('pay',$data['brId'] ? $data['brId']  : null);
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
                                $this->site->updateReference('pay',$data['brId'] ? $data['brId']  : null);
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
                                $this->site->updateReference('pay',$data['brId'] ? $data['brId']  : null);
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
                                $this->site->updateReference('pay',$data['brId'] ? $data['brId']  : null);
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
                            $this->site->updateReference('pay',$data['brId'] ? $data['brId']  : null);
                            $paid += $payments['amount'];
                        }
                    }
                }
				}else{
                if (empty($payment['reference_no'])) {
                    $payment['reference_no'] = $this->site->getReference('pay',$data['brId'] ? $data['brId']  : null);
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
                if ($this->site->getReference('pay',$data['brId'] ? $data['brId']  : null) == $payment['reference_no']) {
                    $this->site->updateReference('pay',$data['brId'] ? $data['brId']  : null);
                }
				}
                $this->site->syncSalePayments($sale_id);
            }

            $this->site->syncQuantity($sale_id);
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);


			if($data['salesman_id'] && $data['commission_type']==2){


			$commission_amount=$this->sma->formatDecimal(((($data['total'] - $data['total_discount']) * $data['currency_rate']) - ($this->getTotalItemCostValue($sale_id)->item_cost_value)) * $data['commission'] / 100,4);

			$this->db->update('sales', ['commission_amount' => $commission_amount], ['id' => $sale_id]);


			}
			$this->db->update('sales', [ 'sale_item_cost_value' => $this->getTotalItemCostValue($sale_id)->item_cost_value], ['id' => $sale_id]);




			if ($this->Settings->accounting){

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					$entryDescription="Sales_Invoice_No#".$data['sale_no'];
				}else{
					$entryDescription="Sales_Invoice_No#".$sale_id;
				}



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
					$entryDebitAccountV2= [];
					$entryDebitBalanceV2= [];

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

					$entryDebitAccountV2[]=$entryDebitAccount;

					if ($this->Settings->IsInsuranceBusiness && $data['insurance_business'] > 0  && $this->Settings->invoice_view ==21){
						$entryDebitAccountInsurance=$this->site->getAccountByRefId($data['customer_id'],'insurance')->id;

						if($entryDebitAccountInsurance=="")
							$entryDebitAccountInsurance=$this->site->getAccountsSettings()->business_guarantee_insurance_acct;

						$entryDebitAccountV2[]=$entryDebitAccountInsurance;
						$insurance_business=(abs($data['total'] - $data['total_discount']) * $data['insurance_business'] / 100);
						$entryDebitBalanceV2[]=$this->sma->formatDecimal($data['grand_total'] - $insurance_business);
						$entryDebitBalanceV2[]=$this->sma->formatDecimal($insurance_business);
					}else{
						$entryDebitBalanceV2[]=$this->sma->formatDecimal($data['grand_total']);

					}


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







					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccountV2,$entryCreditAccount,$entryDebitBalanceV2,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccountV2,$entryCreditAccount,$entryDebitBalanceV2,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
					}




				}

				if($this->site->getCompanyByID($data['customer_id'])->co_type==2){
					$entryCreditAccount= [];
					$entryDebitAccountV2= [];
					$entryDebitBalanceV2= [];

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

					$entryDebitAccountV2[]=$entryDebitAccount;

					if ($this->Settings->IsInsuranceBusiness && $data['insurance_business'] > 0 && $this->Settings->invoice_view ==21){
						$entryDebitAccountInsurance=$this->site->getAccountByRefId($data['customer_id'],'insurance')->id;

						if($entryDebitAccountInsurance=="")
							$entryDebitAccountInsurance=$this->site->getAccountsSettings()->business_guarantee_insurance_acct;

						$entryDebitAccountV2[]=$entryDebitAccountInsurance;
						$insurance_business=(abs($data['total'] - $data['total_discount']) * $data['insurance_business'] / 100);
						$entryDebitBalanceV2[]=$this->sma->formatDecimal($data['grand_total'] - $insurance_business);
						$entryDebitBalanceV2[]=$this->sma->formatDecimal($insurance_business);
					}else{
						$entryDebitBalanceV2[]=$this->sma->formatDecimal($data['grand_total']);

					}

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

					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccountV2,$entryCreditAccount,$entryDebitBalanceV2,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccountV2,$entryCreditAccount,$entryDebitBalanceV2,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
					}



				}
			//}
			//else{

					if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' && !empty($payment)) {
					$i=0;
					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$entryDebitAccountDonation= [];
					$entryDebitBalanceDonation= [];
					$amount=0;
					$amountDonation=0;
					$final_commisson_amount=0;
					foreach ($payment as $payments) {
						$payments['amount']=$payments['amount'] * $data['currency_rate'];

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
						$entryDebitAccountDonation[] =  $entryDebitAcc;

					 }

					 if($payments['donation']!=0){
						$payments['amount']=$payments['amount']-$payments['donation'];
					}


					 //$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);

					 $entryDebitBalance[] =  $this->sma->formatDecimal($payments['amount']);
					 $entryDebitBalanceDonation[] =  $this->sma->formatDecimal($payments['donation']);
					 $amount=$amount+$payments['amount'];
					 $amountDonation=$amountDonation+$payments['donation'];


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

					/*if($data['costCenterId']){
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


					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription);
					}*/

					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
					}





					if($payments['donation']!=0){
						$entry_type="Donations_due_to_charities";
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
							if($sale_no=$this->getInvoiceSaleNoByById($sale_id)){
								$entryDescription="Donations_Payments_Sales_Invoice_No#".$sale_no;
							}else{
								$entryDescription="Donations_Payments_Sales_Invoice_No#".$sale_id;
							}
						}else{
							$entryDescription="Donations_Payments_Sales_Invoice_No#".$sale_id;
						}









					$entryCreditAccount= [];
					$entryCreditBalance= [];

					$entryCrediAcc=$this->site->getAccountsSettings()->donations_acct;
						if($entryCrediAcc=="")
						$entryCrediAcc=$this->site->getAccountsSettings()->expenses_shipping_acct;
					 	$entryCreditAccount[] =  $entryCrediAcc;


					$entryCreditBalance[] =  $this->sma->formatDecimal($amountDonation);


					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccountDonation,$entryCreditAccount,$entryDebitBalanceDonation,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccountDonation,$entryCreditAccount,$entryDebitBalanceDonation,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
					}



					}







			}

				//}


				if($data['sale_status'] == 'completed'){
					$final_item_cost_value=$this->getTotalItemCostValue($sale_id)->item_cost_value;
					if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
							}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],"Cost_of_goods_sold",null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

						}else{
							$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],"Cost_of_goods_sold",null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
						}


							}
					}


			}









			if ($data['sale_status'] == 'returned'){

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					if($sale_no=$this->getInvoiceSaleNoByById($sale_id)){
						$entryDescription="Sales_Returned_Invoice_No#".$sale_no;
					}else{
						$entryDescription="Sales_Returned_Invoice_No#".$sale_id;
					}
				}else{
					$entryDescription="Sales_Returned_Invoice_No#".$sale_id;
				}

				$entry_type="";
					$entry_type="sale_returned";

				if($data['payment_status'] == 'pending' || $payment['amount']==0){
				if($this->site->getCompanyByID($data['customer_id'])->co_type==1){
					$entryCreditAccount= [];
					$entryDebitAccountV2= [];
					$entryDebitBalanceV2= [];

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_return_acct;

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax']));
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['total_tax']));

					$entryDebitAccount=$this->site->getAccountByRefId($data['customer_id'],'local_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_local_customers_acct;

						$entryDebitAccountV2[]=$entryDebitAccount;

					if ($this->Settings->IsInsuranceBusiness && abs($data['insurance_business']) > 0  && $this->Settings->invoice_view ==21){
						$entryDebitAccountInsurance=$this->site->getAccountByRefId($data['customer_id'],'insurance')->id;

						if($entryDebitAccountInsurance=="")
							$entryDebitAccountInsurance=$this->site->getAccountsSettings()->business_guarantee_insurance_acct;

						$entryDebitAccountV2[]=$entryDebitAccountInsurance;
						$insurance_business=((abs($data['total']) - abs($data['total_discount'])) * abs($data['insurance_business']) / 100);
						$entryDebitBalanceV2[]=$this->sma->formatDecimal(abs($data['grand_total']) - $insurance_business);
						$entryDebitBalanceV2[]=$this->sma->formatDecimal($insurance_business);
					}else{
						$entryDebitBalanceV2[]=$this->sma->formatDecimal(abs($data['grand_total']));

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

					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccountV2,$entryCreditBalance,$entryDebitBalanceV2,$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

						}else{
							$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccountV2,$entryCreditBalance,$entryDebitBalanceV2,$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
						}



				}

				if($this->site->getCompanyByID($data['customer_id'])->co_type==2){
					$entryCreditAccount= [];
					$entryDebitAccountV2= [];
					$entryDebitBalanceV2= [];

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_return_acct;

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax']));
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['total_tax']));

					$entryDebitAccount=$this->site->getAccountByRefId($data['customer_id'],'international_customers')->id;
						if($entryDebitAccount=="")
						$entryDebitAccount=$this->site->getAccountsSettings()->accounts_international_customers_acct;

						$entryDebitAccountV2[]=$entryDebitAccount;

					if ($this->Settings->IsInsuranceBusiness && abs($data['insurance_business']) > 0  && $this->Settings->invoice_view ==21){
						$entryDebitAccountInsurance=$this->site->getAccountByRefId($data['customer_id'],'insurance')->id;

						if($entryDebitAccountInsurance=="")
							$entryDebitAccountInsurance=$this->site->getAccountsSettings()->business_guarantee_insurance_acct;

						$entryDebitAccountV2[]=$entryDebitAccountInsurance;
						$insurance_business=((abs($data['total']) - abs($data['total_discount'])) * abs($data['insurance_business']) / 100);
						$entryDebitBalanceV2[]=$this->sma->formatDecimal(abs($data['grand_total']) - $insurance_business);
						$entryDebitBalanceV2[]=$this->sma->formatDecimal($insurance_business);
					}else{
						$entryDebitBalanceV2[]=$this->sma->formatDecimal(abs($data['grand_total']));

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


					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccountV2,$entryCreditBalance,$entryDebitBalanceV2,$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

						}else{
							$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccountV2,$entryCreditBalance,$entryDebitBalanceV2,$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
						}




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

					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$payment['currency'],$payment['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription,null,null,null,null,null,$payment['currency'],$payment['currency_rate']);
					}





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


						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryWarehouseAcc,$this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],"Cost_of_goods_sold",null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

						}else{
							$this->site->createEntryMultiSide($entryWarehouseAcc,$this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],"Cost_of_goods_sold",null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
						}


							}


			}




			}


        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            //log_message('error', 'An errors has been occurred while adding the sale (Add:Sales_model.php)');
			try {
				$this->sma->write_log('ERROR', 'An errors has been occurred while adding the sale (Add:Sales_model.php)','sales','Data '.json_encode($data).' Items '.json_encode($items).' Payment '.json_encode($payment));
			} catch (Exception $e) {
			}
        } else {

			$WhatsappSettings=$this->site->getWhatsappSettings();
            if ($WhatsappSettings->sales==1) {
                $customer = $this->site->getCompanyByID($data['customer_id']);
                $dbName=str_replace("cpesoft_","",$this->db->database);
                $this->load->library('sms');
                $text=  $this->sms->invSaleText($sale_id, $dbName);

                $this->load->library('whatsapp');
                $this->whatsapp->send($customer->phone, $text);
            }

            if(isset($this->Settings->isAutoSynchronizeZATCA) && $this->Settings->isAutoSynchronizeZATCA){
                if (($data['sale_status'] == 'returned' || $data['sale_status'] == 'completed')){
                    $inv         = $this->getInvoiceByID($sale_id);
					$org_return_sale = $inv->sale_id ? $this->getInvoiceByID($inv->sale_id) : ( $inv->debit_id ? $this->getInvoiceByID($inv->debit_id) : ( $inv->credit_id ? $this->getInvoiceByID($inv->credit_id) : null));
        			if($org_return_sale){
            			if($org_return_sale->synch==1){
                			$this->generate_xml($sale_id);
			            }
        			}else{
						$this->generate_xml($sale_id);
					}
                }
            }
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
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, products.cf1 as cf1, products.cf2 as cf2, products.cf3 as cf3, products.cf4 as cf4, products.cf5 as cf5, products.cf6 as cf6, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name, products.unit as base_unit_id, units.code as base_unit_code')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->join('units', 'units.id=products.unit', 'left')
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
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.item_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%'  OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.item_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
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
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.item_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
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

        $wp = "( SELECT product_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('products.*, categories.id as category_id, categories.name as category_name', false)
            ->join($wp, 'FWP.product_id=products.id', 'left')
            // ->join('warehouses_products FWP', 'FWP.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('products.id');
        if ($this->Settings->overselling) {
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.item_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.item_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
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
                $row->name = mb_substr($row->name, 0, 120, 'UTF-8');
                $data[] = $row;
            }
            return $data;
        }
    }

	public function getProductNamesMulti($term, $warehouse_id, $pos = false, $limit = 5)
    {
		$term = str_replace([")","(","'"],"",$term);
        $term = strip_tags($term);

        $wp = "( SELECT product_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('products.*, categories.id as category_id, categories.name as category_name,product_prices.price as price_first,product_prices.product_unit_id as unit_first,product_prices.product_code as code_first', false)
            ->join($wp, 'FWP.product_id=products.id', 'left')
             ->join('product_prices', 'product_prices.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('product_prices.product_id');
        if ($this->Settings->overselling) {
            $this->db->where("({$this->db->dbprefix('product_prices')}.product_code LIKE '%" . $term . "%' )");
        } else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('product_prices')}.product_code LIKE '%" . $term . "%' )");
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
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.item_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.item_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
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
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.item_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.second_name LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
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
				$reference=$this->site->getReference('re',$data['brId'] ? $data['brId']  : null);

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


				if($ogc->paid==0){
					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference,'Gift_card_sales',null,null,null,null,null,null,$data['grpId'],$data['coId'],$data['brId']);

						}else{
							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference,'Gift_card_sales');
						}


				}else{

					//if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					//	{

							//$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$opay->reference,'Gift_card_sales',null,null,null,null,null,null,$data['grpId'],$data['coId'],$data['brId']);

						//}else{
							$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$opay->reference,'Gift_card_sales');
						//}


				}

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








					if($opay->paid_by == 'deposit'){

						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,null,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency'],$data['currency_rate']);

						}else{
							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,null,null,null,null,null,null,$data['currency'],$data['currency_rate']);
						}


					}else{

						//if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						//{

							//$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,null,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

						//}else{
							$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,null,null,$data['currency'],$data['currency_rate']);
						//}


					}









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





					$this->site->updateEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$sale->reference_no,$entry_type,null,null,null,null,null,null,null,$data['currency'],$data['currency_rate']);








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

        $this->resetSaleActions($id, false, true);

        if ($data['sale_status'] == 'completed') {
            $this->Settings->overselling = true;
            $cost                        = $this->site->costing($items, true);
        }
        // $this->sma->print_arrays($cost);



        if ($this->db->update('sales', $data, ['id' => $id]) && $this->db->delete('sale_items', ['sale_id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {
            foreach ($items as $item) {



				if ($item['product_type'] == 'combo') {
                		$item_cost_value=0;
						$avg_cost=0;
				        $combo_items_qty = $this->site->getProductComboItems($item['product_id'], $item['warehouse_id']);
                        /*$total_qty=0;
						foreach ($combo_items_qty as $combo_item) {
						    $total_qty=$total_qty+$combo_item->qty;
                        }
						*/


						$combo_items = $this->site->getProductComboItems($item['product_id'], $item['warehouse_id']);
                        foreach ($combo_items as $combo_item) {
							$quantity=$item['quantity'] * $combo_item->qty;
                            $wp=$this->getavg_costProduct($combo_item->id,$item['warehouse_id']);
				if($wp){
					if (!empty($si_return)) {
						$sale_item=$this->getSaleItemBysale_id($data['sale_id'],$item['product_id']);
						$cos_quantity=0;
						$cos_quantities=0;
						if($this->getCombOverselling($combo_item->id,$item['product_id'],$data['sale_id'])){
						$cos=$this->getCombOverselling($combo_item->id,$item['product_id'],$data['sale_id']);
						$cos_quantity=$cos->quantity_overselling;
						}
				        $cavg=$this->getCombavg_cost($combo_item->id,$item['product_id'],$data['sale_id']);
				        $new_avg_cost=$cavg->avg_cost;
						$qty=0;
						if($wp->quantity < 0)
						$qty=$wp->quantity - $quantity;
						else
						$qty=$wp->quantity + $quantity;

					if($wp->quantity < 0){
					if($qty > 0){
							$actual_quantity=$this->sma->formatDecimal((0 - $qty)+$cavg->quantity);
							if($cos_quantity>0)
							$cos_quantities=0;//$this->sma->formatDecimal((0 - $qty)+$cos_quantity);
							$actual_item_cost_value=$this->sma->formatDecimal(((0 - $qty) * ($new_avg_cost))+$cavg->item_cost_value,12);
							$item_cost_value=$this->sma->formatDecimal($item_cost_value+(((0 - $qty) * ($new_avg_cost))),12);
							//$avg_cost=$this->sma->formatDecimal($avg_cost+$sale_item->avg_cost,12);


					}else{
							$actual_quantity=$cavg->quantity;
							if($cos_quantity>0)
							$cos_quantities=$this->sma->formatDecimal((0 - $qty));
							$actual_item_cost_value=$cavg->item_cost_value;
							$item_cost_value=$item_cost_value;
							//$avg_cost=$avg_cost;

					}

					}else{
							$actual_quantity=$this->sma->formatDecimal($quantity+$cavg->quantity);
							if($cos_quantity>0)
							$cos_quantities=0;//$this->sma->formatDecimal($quantity+$cos_quantity);
							$actual_item_cost_value=$this->sma->formatDecimal(($quantity * ($new_avg_cost))+$cavg->item_cost_value,12);
							$item_cost_value = $this->sma->formatDecimal($item_cost_value+($quantity * ($new_avg_cost)),12);
							//$avg_cost		 = $this->sma->formatDecimal($avg_cost+$sale_item->avg_cost,12);



					}


						$avg_cost=$this->sma->formatDecimal($sale_item->avg_cost,12);
						$this->db->update('combo_avg_cost', ['quantity' => $actual_quantity,'item_cost_value' => $actual_item_cost_value], ['id' => $cavg->id]);
						if($cos_quantity>0){
							if($cos_quantities<0)
								$cos_quantities=0;
						$this->db->update('combo_overselling', ['quantity_overselling' => $cos_quantities], ['id' => $cos->id]);
						}



					}else{
					if($quantity > $wp->quantity){
						if($wp->quantity > 0){
							$actual_quantity=$wp->quantity;
							$actual_item_cost_value=$this->sma->formatDecimal($wp->quantity * $wp->avg_cost,12);
							$item_cost_value=$this->sma->formatDecimal($item_cost_value+($wp->quantity * $wp->avg_cost),12);
							$avg_cost=$this->sma->formatDecimal($avg_cost+($wp->avg_cost* $combo_item->qty),12);
							$item['overselling']=1;
							$this->db->insert('combo_overselling', ['quantity_overselling' => ($quantity-$wp->quantity), 'product_id' => $item['product_id'], 'item_id' => $combo_item->id, 'sale_id' => $sale_id]);




						}else{
							$actual_quantity=0;
							$actual_item_cost_value=0;
							$item_cost_value=$item_cost_value;
							$avg_cost=$avg_cost;
							$item['overselling']=1;
							$this->db->insert('combo_overselling', ['quantity_overselling' => $quantity, 'product_id' => $item['product_id'], 'item_id' => $combo_item->id, 'sale_id' => $sale_id]);



						}

					}else{
						$actual_quantity=$quantity;
						$actual_item_cost_value=$this->sma->formatDecimal($quantity * $wp->avg_cost,12);
						$item_cost_value=$this->sma->formatDecimal($item_cost_value+($quantity * $wp->avg_cost),12);
						$avg_cost=$this->sma->formatDecimal($avg_cost+($wp->avg_cost* $combo_item->qty),12);


					}
						$this->db->insert('combo_avg_cost', ['quantity' => $actual_quantity,'item_cost_value' => $actual_item_cost_value,'avg_cost' => ($wp->avg_cost), 'product_id' => $item['product_id'], 'item_id' => $combo_item->id, 'sale_id' => $sale_id]);

					}
				}

						}
						$item['item_cost_value']=$item_cost_value;
						$item['avg_cost']=$avg_cost;

                }else{

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
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {
                    $item_costs = $this->site->item_costing($item);
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
                }






            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }




            $this->site->syncSalePayments($id);
            $this->site->syncQuantity($id);
            $sale = $this->getInvoiceByID($id);

            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $sale->created_by);


			if($data['salesman_id'] && $data['commission_type']==2){


				$commission_amount=$this->sma->formatDecimal(((($data['total'] - $data['total_discount']) * $data['currency_rate']) - ($this->getTotalItemCostValue($id)->item_cost_value)) * $data['commission'] / 100,4);

				$this->db->update('sales', ['commission_amount' => $commission_amount], ['id' => $id]);


			}
			$this->db->update('sales', [ 'sale_item_cost_value' => $this->getTotalItemCostValue($id)->item_cost_value], ['id' => $id]);









			if ($this->Settings->accounting){


				if(isset($data['currency_rate']) && $data['currency_rate']!=1){
					$data['grand_total'] = $data['grand_total'] * $data['currency_rate'];
					$data['total_tax'] = $data['total_tax'] * $data['currency_rate'];
					$data['shipping'] = $data['shipping'] * $data['currency_rate'];

				}
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					if($sale_no=$this->getInvoiceSaleNoByById($id)){
						$entryDescription="Sales_Invoice_No#".$sale_no;
					}else{
						$entryDescription="Sales_Invoice_No#".$id;
					}
				}else{
					$entryDescription="Sales_Invoice_No#".$id;
				}

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
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,null,$data['date'],$data['currency_code'],$data['currency_rate']);
					}else{
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					    	$this->site->updateEntryMultiSideWithDelete($sale->reference_no,$entryDescription);
							$this->site->updateEntryMultiSideWithDelete($sale->reference_no,"Sales_Invoice_No#".$sale->id);

						}else{
					    	$this->site->updateEntryMultiSideWithDelete($data['reference_no'],$entryDescription);
						}

						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

						}else{
							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
						}


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
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,null,$data['date'],$data['currency_code'],$data['currency_rate']);
					}else{
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					    	$this->site->updateEntryMultiSideWithDelete($sale->reference_no,$entryDescription);
							$this->site->updateEntryMultiSideWithDelete($sale->reference_no,"Sales_Invoice_No#".$sale->id);

						}else{
					    	$this->site->updateEntryMultiSideWithDelete($data['reference_no'],$entryDescription);
						}

						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

						}else{
							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
						}


					}


				}




			}else{
				if ($data['sale_status']  == 'completed' && $Osale->sale_status != 'completed'){



				$entry_type="";
					if ($data['sale_status'] == 'completed')
					$entry_type="sale_completed";



					$costCenterCreditId= [];
					$costCenterCreditAmount= [];

					if($data['costCenterId']){
								$costCenterCreditId[] =json_decode($data['costCenterId']);
								$costCenterCreditAmount[] =json_decode($data['costCenterAmount']);




								//$this->sma->print_arrays($costCenterCreditAmount);


					}else{

								$costCenterCreditId[] ="";
								$costCenterCreditAmount[] ="";


					}









					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_store_goods_receiving_acct,$this->site->getAccountsSettings()->sales_acct,$this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']),$this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']),$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_store_goods_receiving_acct,$this->site->getAccountsSettings()->sales_acct,$this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']),$this->sma->formatDecimal($data['grand_total']-$data['total_tax']-$data['shipping']),$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
					}




					$final_item_cost_value=$this->getTotalItemCostValue($sale->id)->item_cost_value;

					if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
							}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

					}else{
						$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
					}



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


					$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,null,$entryDescription,null,$data['currency_code'],$data['currency_rate']);






				$final_item_cost_value=$this->getTotalItemCostValue($sale->id)->item_cost_value;

				if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
								}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


					$this->site->updateEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$data['reference_no'],'Cost_of_goods_sold',null,null,null,null,null,$entryDescription,$data['date'],$data['currency_code'],$data['currency_rate']);

							}
				}

				}
			}




			}

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            //log_message('error', 'An errors has been occurred while adding the sale (Update:Sales_model.php)');
			try{
				$this->sma->write_log('ERROR', 'An errors has been occurred while update the sale (Update:Sales_model.php)','sales','Id ('.$id.') Data '.json_encode($data).' Items '.json_encode($items));
			} catch (Exception $e) {
			}

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

        if ($this->db->update('sales', ['sale_status' => $status, 'note' => $note], ['id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {
            if ($status == 'completed' && $sale->sale_status != 'completed') {
                foreach ($items as $item) {
                    $item = (array) $item;
                    if ($this->site->getProductByID($item['product_id'])) {
                        $item_costs = $this->site->item_costing($item);
                        foreach ($item_costs as $item_cost) {
                            $item_cost['sale_item_id'] = $item['id'];
                            $item_cost['sale_id']      = $id;
                            $item_cost['date']         = date('Y-m-d', strtotime($sale->date));
                            if (!isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        }
                    }
                }
            }

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
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					if($sale_no=$this->getInvoiceSaleNoByById($sale->id)){
						$entryDescription="Sales_Invoice_No#".$sale_no;
					}else{
						$entryDescription="Sales_Invoice_No#".$sale->id;
					}
				}else{
					$entryDescription="Sales_Invoice_No#".$sale->id;
				}

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
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,null,null,$sale->date,$sale->currency_code,$sale->currency_rate);
					}else{
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					    	$this->site->updateEntryMultiSideWithDelete($sale->reference_no,$entryDescription);
							$this->site->updateEntryMultiSideWithDelete($sale->reference_no,"Sales_Invoice_No#".$sale->id);

						}else{
							$this->site->updateEntryMultiSideWithDelete($sale->reference_no,$entryDescription);
						}

						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription,$sale->grpId,$sale->coId,$sale->brId,null,null,$sale->currency_code,$sale->currency_rate);

						}else{
							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription,null,null,null,null,null,$sale->currency_code,$sale->currency_rate);
						}


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
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,null,null,$sale->date,$sale->currency_code,$sale->currency_rate);
					}else{
					    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					    	$this->site->updateEntryMultiSideWithDelete($sale->reference_no,$entryDescription);
							$this->site->updateEntryMultiSideWithDelete($sale->reference_no,"Sales_Invoice_No#".$sale->id);

						}else{
							$this->site->updateEntryMultiSideWithDelete($sale->reference_no,$entryDescription);
						}

						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription,$sale->grpId,$sale->coId,$sale->brId,null,null,$sale->currency_code,$sale->currency_rate);

						}else{
							$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($sale->grand_total),$entryCreditBalance,$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription,null,null,null,null,null,$sale->currency_code,$sale->currency_rate);
						}


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

					 if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					 {

						$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_store_goods_receiving_acct,$this->site->getAccountsSettings()->sales_acct,$this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping),$this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping),$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription,$sale->grpId,$sale->coId,$sale->brId,null,null,$sale->currency_code,$sale->currency_rate);

					 }else{
						$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_store_goods_receiving_acct,$this->site->getAccountsSettings()->sales_acct,$this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping),$this->sma->formatDecimal($sale->grand_total-$sale->total_tax-$sale->shipping),$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription,null,null,null,null,null,$sale->currency_code,$sale->currency_rate);
					 }



					$final_item_cost_value=$this->getTotalItemCostValue($sale->id)->item_cost_value;

					if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
							}else{


						$entryWarehouseAcc=$this->site->getAccountByRefId($sale->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{

							$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription,$sale->grpId,$sale->coId,$sale->brId,null,null,$sale->currency_code,$sale->currency_rate);

						}else{
							$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$sale->reference_no,$entry_type,null,null,null,null,$sale->date,$entryDescription,null,null,null,null,null,$sale->currency_code,$sale->currency_rate);
						}


							}

				}else{
					/*if ($status  == 'completed' && $sale->sale_status == 'completed'){



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
				}*/

				}
			}




			}




        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            //log_message('error', 'An errors has been occurred while adding the sale (UpdataStatus:Sales_model.php)');
			try{
				$this->sma->write_log('ERROR', 'An errors has been occurred while update a status of sale (UpdateaStatus:Sales_model.php)','sales','Id ('.$id.') Status ('.$status.') Note ('.$note.')');
			} catch (Exception $e) {
			}

        } else {
			return true;
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
		$this->db->where('category_id =', 1);
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


    public function getAllSaleReturnItems($sale_id,$product_code)
    {
        $this->db->select('SUM(unit_quantity) as unit_quantity')//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')

		//$this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('sales', 'sales.id=sale_items.sale_id', 'left');
            $this->db->where('sales.sale_id', $sale_id);

		$q = $this->db->get_where('sale_items', ['product_code' => $product_code]);
	    if ($q->num_rows() > 0) {
               return $q->row();
        }
        return false;
    }


	public function getAllsaleItemsReturnWithOutZeros($sale_id,$product_code = NULL)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
        //$this->db->select('sale_items.*')//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')
	         ->join('sales', 'sales.id=sale_items.sale_id', 'left')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('sale_items.id', 'asc');
			 $this->db->where('sales.sale_id', $sale_id);
			$this->db->where('sale_items.quantity !=', 0);
		$q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


	public function getBalanceQuantity($product_id, $warehouse_id = null)
    {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', false);
        $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

	public function getPromosWithHaveDetails()
    {
        $today = date('Y-m-d');

		$this->db->select('id,promo_name');
        $this->db->where('"' . $today . '"  BETWEEN start_date and end_date');

        //$this->db->where(" (promo_type_id =  '7' OR promo_type_id =  '8' OR promo_type_id =  '9') ");
        $this->db->where('promo_type_id', 9);
		$this->db->where('status', 1);


		$q = $this->db->get('promo');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


	public function addCoupons($data = [])
    {

        if ($this->db->insert('coupons', $data)) {

            return true;
        }
        return false;
    }

	public function updateCoupons($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('coupons', $data)) {

            return true;
        }
        return false;
    }

	public function deleteCoupons($id)
    {
        if ($this->db->delete('coupons', ['id' => $id])) {
            return true;
        }
        return true;
    }

	public function getCouponByCode($coupon_code, $customer_id=null)
    {
		$this->db->like('coupon_code',$coupon_code);
		//$this->db->where('LENGTH(coupon_code)', 8);
		if($customer_id)
        $this->db->where('customer_id',$customer_id);

        $q = $this->db->get('coupons');
        if ($q->num_rows() == 1) {
            return $q->row();
        }
        return false;
    }

	public function checkCouponById($id)
    {

        $today = date('Y-m-d');

		$this->db->where('id',$id);
        $this->db->where('status',1);
        $this->db->where('"' . $today . '"  BETWEEN coupon_start_date and coupon_expiry_date');
        $q = $this->db->get('coupons');
        if ($q->num_rows() == 1) {
            return $q->row();
        }
        return false;
    }

	public function getAllSalessWithCouponCode($coupon_code)
    {
        $this->db->select('(count(*)) as count');
		$q = $this->db->get_where('sales', ['coupon_code' => $coupon_code], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
    }

	public function getAllPOSSales($start = null, $end = null, $warehouse_id = null, $biller_id = null, $sale_status = null)
    {
        $this->db->select('count(id) as total,sum(grand_total)  as grand_total,sum(paid+discount_allowance)  as paid, (sum(grand_total)-sum(paid+discount_allowance)) as due', false);

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        if ($biller_id) {
            $this->db->where('biller_id', $biller_id);
        }
		if ($biller_id) {
            $this->db->where('sale_status', $sale_status);
        }
		$this->db->where('sales.pos', '1');


			//$this->db->where('sales.paid !=', 0);
			if ($start && $end) {
               $this->db->where('date BETWEEN "' . $start . '" and "' . $end . '"');
            }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getToatalSalesPaymentPOSCC($biller_id,$sale_status,$paid_by,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(amount),0) as amount,cc_type')
			 ->join('payments', 'payments.sale_id = sales.id', 'left');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
		if ($start_date && $end_date) {
             //   $this->db->where('sales.date >=', $start_date);
        	    $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
         }

		$this->db->where('sales.biller_id', $biller_id);
        $this->db->where('sales.sale_status', $sale_status);
        $this->db->where('paid_by', $paid_by);
        $this->db->where('sales.pos', '1')
        ->group_by('payments.cc_type');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
		}
        return false;
    }


	public function updateSaleV2($id, $data, $items = [])
    {
        $this->db->trans_off();
		$this->db->trans_start();



        if ($this->db->update('sales', $data, ['id' => $id]) ) {
            foreach ($items as $item) {










                $item['sale_id'] = $id;
                $this->db->update('sale_items', $item, ['id' => $item['id']]);
				$this->db->update('payments', ['commercial_discount' => 0], ['sale_id' => $id]);






            }









		}
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Add:Sales_model.php)');
        } else {
            return $id;
        }

        return false;
    }

	public function getAllDuplicateSaleNo()
    {
        $this->db->select('count(*) as count,sale_no')
		->group_by('sale_no')
        ->order_by('count', 'DESC');
		$this->db->where(" (sale_no is not null) ");
		$this->db->where('brId', '1');

		$q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				if($row->count >1)
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getInvoiceBySaleNo($sale_no)
    {

		$this->db->select('id')
		->order_by('id', 'desc');
		$q = $this->db->get_where('sales', ['sale_no' => $sale_no], 1);

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getCountDuplicateSaleNo($id)
    {
        $this->db->select(' (count(*)+1) as count');
		$this->db->where('brId', '1');
        $this->db->where('id <', $id);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
			return $q->row();
        }
        return false;
    }

	public function getLastInvoiceSynch($warehouse_id = null)
    {
        $this->db->select('sale_documents.stamp_hash')
		->join('sales', 'sales.id=sale_documents.sale_id', 'left');
		$this->db->where('sales.synch', 1)->where('sales.warehouse_id', $warehouse_id)->order_by('sale_documents.signing_time', 'desc')->limit(1);
        $q = $this->db->get('sale_documents');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getAllsaleItemsDebit_idWithOutZeros($debit_id,$product_code = NULL)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
        //$this->db->select('sale_items.*')//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')
	         ->join('sales', 'sales.id=sale_items.sale_id', 'left')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('sale_items.id', 'asc');
			 $this->db->where('sales.debit_id', $debit_id);
			$this->db->where('sale_items.quantity !=', 0);
		$q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getAllsaleItemsCredit_idWithOutZeros($credit_id,$product_code = NULL)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
        //$this->db->select('sale_items.*')//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')
	         ->join('sales', 'sales.id=sale_items.sale_id', 'left')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('sale_items.id', 'asc');
			 $this->db->where('sales.credit_id', $credit_id);
			$this->db->where('sale_items.quantity !=', 0);
		$q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getInvoiceByUUID($uuid)
    {
		//$this->db->where("$this->bcrypt->verify($password, $hash_password_db->password)", NULL, FALSE);
        $q = $this->db->get_where('sales', ['uuid' => $uuid],1);
        if ($q->num_rows() > 0) {
			//if($this->bcrypt->verify($hash, $q->row()->hash))
            return $q->row();
        }
        return false;
    }

	public function addTermsConditions($data)
    {
        if ($this->db->insert('sales_termscondi', $data)) {
			$tid = $this->db->insert_id();
            return $tid;
        }
        return false;
    }

	public function updateTermsConditions($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('sales_termscondi', $data)) {
            return true;
        }
        return false;
    }

	public function getTermsConditionsByID($id)
    {
        $q = $this->db->get_where('sales_termscondi', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function deleteTermsConditions($id)
    {
        if ($this->db->delete('sales_termscondi', ['id' => $id])) {
            return true;
        }
        return false;
    }

	public function generate_xml($id)
    {

		$inv         = $this->getInvoiceByID($id);
        $customer    = $this->getCompanyByID($inv->customer_id);
        $units       = $this->getAllUnits();
        $biller      = $this->getCompanyByID($inv->biller_id);
        $created_by  = $this->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->getUser($inv->updated_by) : null;
        $warehouse   = $this->getWarehouseByID($inv->warehouse_id);
        $inv         = $inv;
        $rows        = $this->getAllInvoiceItemsV2($id);
        $return_sale = null;
        $return_rows = null;
		$payments        = $this->getInvoicePaymentsV2($inv->id);
		$return_payments = $inv->return_id ? $this->getInvoicePaymentsV2($inv->return_id) : null;
        $active_tax        =$this->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->getInvoicePaymentsV2($inv->return_id) : null;

		$dbName=str_replace("cpesoft_","",$this->db->database);
		$dateTime=str_replace('-',"",$inv->date);
		$dateTime=str_replace(':',"",$dateTime);
		$dateTime=str_replace(' ',"T",$dateTime);
		//	$dateTime = new DateTime($inv->date);

	//$dateTime= $dateTime->format('Y-m-d\TH:i:s\Z');


		$xml_file_path=$dbName.'_'.$warehouse->vat_reg_no.'_'.$id;

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->Settings->site_name;

        if($invPre         = $this->getLastInvoiceSynch($inv->warehouse_id)){
            $stamp_hash=$invPre->stamp_hash;
        }else{
            $stamp_hash=null;
        }

		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $this->Settings->default_currency;
				$currencyRate= 1;
			}

            $paymentMeansCode=1;
			if($payments[0]){
				if($payments[0]->paid_by=="cash")
					$paymentMeansCode=10;
				if($payments[0]->paid_by=="CC")
					$paymentMeansCode=30;
				if($payments[0]->paid_by=="bank_transfer" || $payments[0]->paid_by=="Cheque")
					$paymentMeansCode=42;
			}



            if($inv->sale_status == 'returned' || $inv->sale_status == 'completed'){
                if($inv->sale_status == 'completed'){
                    if($inv->debit_id){
                        $InvoiceTypeCode= '383';
					}else if($inv->credit_id){
                        $InvoiceTypeCode= '381';
					}else{
                        $InvoiceTypeCode= '388';
					}
                }else if($inv->sale_status == 'returned'){
                    $InvoiceTypeCode= '381';
                }
                $note= str_replace("<p>"," ",$this->sma->decode_html($inv->note));
                $note= str_replace("</p>","",$this->sma->decode_html($note));
                //$note= str_replace(" ","",$this->sma->decode_html($note));

                $this->sma->xmlInvoice($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$inv->id,$rows,$warehouse,$customer,$inv->uuid,$stamp_hash,$this->sma->formatDecimal($return_sale ? ($inv->paid  + $return_sale->paid ) : abs($inv->paid )),$paymentMeansCode,$InvoiceTypeCode,($inv->sale_id ? $inv->sale_id : ($inv->debit_id ? $inv->debit_id : ($inv->credit_id ? $inv->credit_id : null))),$note,$currencyRate);
                //$xml_download=anchor('admin/welcome/downloadXMLUBL/' . $xml_file_path.'.xml', '<i class="fa fa-download"></i> <b>XML UBL File</b>', 'class="btn btn-primary btn-xs"');

			if($this->updateSalesXMLFilePathById($id,$xml_file_path)){
                $this->digital_signature($id);
			    //$this->sma->send_json([ 'msg' => $xml_download.'<i class="fa fa-check"></a> Success!']);

			}else{
				//$this->sma->send_json([ 'msg' => '<i class="fa fa-times"></a> Failed!']);

			}

        }else{
            //$this->sma->send_json([ 'msg' => '<i class="fa fa-times"></a> Failed!']);

        }

    }

    public function getAllInvoiceItemsV2($sale_id, $return_id = null)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, products.cf1 as cf1, products.cf2 as cf2, products.cf3 as cf3, products.cf4 as cf4, products.cf5 as cf5, products.cf6 as cf6, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name, products.unit as base_unit_id, units.code as base_unit_code')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->join('units', 'units.id=products.unit', 'left')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');
		$this->db->where('promo_free !=', 1);
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

    public function getInvoicePaymentsV2($sale_id)
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

    public function updateSalesXMLFilePathById($id,$xml_file_path)
    {
            $this->db->update('sales', ['xml_file_path' => $xml_file_path], ['id' => $id]);

        return true;
    }

    public function digital_signature($id)
    {

		$inv         = $this->getInvoiceByID($id);
        $customer    = $this->getCompanyByID($inv->customer_id);
        $units       = $this->getAllUnits();

        if($inv->warehouse_id != '312579837900003')
            $zatcaConfig       = $this->getZatcaConfig();
        else
            $zatcaConfig       = $this->getZatcaConfigV2();

        $biller      = $this->getCompanyByID($inv->biller_id);
        $created_by  = $this->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->getUser($inv->updated_by) : null;
        $warehouse   = $this->getWarehouseByID($inv->warehouse_id);
        $inv         = $inv;
        $rows        = $this->getAllInvoiceItemsV2($id);
        $return_sale = null;
        $return_rows = null;
		$payments        = $this->getInvoicePaymentsV2($inv->id);
		$return_payments = $inv->return_id ? $this->getInvoicePaymentsV2($inv->return_id) : null;
        $active_tax        =$this->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->getInvoicePaymentsV2($inv->return_id) : null;

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->Settings->site_name;
		$InvoiceTypeNumber=1;
        if($customer->vat_no!=""){
            $InvoiceTypeNumber=1;
        }else{
            $InvoiceTypeNumber=2;
        }


		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $this->Settings->default_currency;
				$currencyRate= 1;
			}

		//$this->sma->print_arrays($InvoiceTypeNumber);

		if($inv->sale_status == 'returned' || $inv->sale_status == 'completed'){

			$QR=$this->sma->xmlInvoiceSign($inv->id,$inv->xml_file_path,$wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$zatcaConfig,$InvoiceTypeNumber,$currencyRate);

            if($QR){

                $this->integration_zatca_clearance($inv->id);

            }
            //$xml_download=anchor('admin/welcome/downloadXMLUBL/' . $inv->xml_file_path.'signedWQR.xml', '<i class="fa fa-download"></i> <b>XML File with Stamp</b>', 'class="btn btn-primary btn-xs"');
			//$this->sma->send_json([ 'msg' => $this->sma->qrcode('', $QR, 2).''.$xml_download.'<i class="fa fa-check"></a> Success!','stamp_hash' => $invAfterUpdate->stamp_hash,'uuid' => $invAfterUpdate->uuid,'synch_hash' => $invAfterUpdate->synch_hash]);



        }else{
           //$this->sma->send_json([ 'msg' => '<i class="fa fa-times"></a> Failed!']);

        }

    }


    public function integration_zatca_clearance($id)
    {

		$inv         = $this->getInvoiceByID($id);
        $invDoc         = $this->getInvoiceDocBySaleID($id);
        $customer    = $this->getCompanyByID($inv->customer_id);
        $units       = $this->getAllUnits();
        $biller      = $this->getCompanyByID($inv->biller_id);
        $created_by  = $this->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->getUser($inv->updated_by) : null;
        $warehouse   = $this->getWarehouseByID($inv->warehouse_id);

        if($inv->warehouse_id != '312579837900003')
            $zatcaConfig       = $this->getZatcaConfig();
        else
            $zatcaConfig       = $this->getZatcaConfigV2();

        $inv         = $inv;
        $rows        = $this->getAllInvoiceItemsV2($id);
        $return_sale = null;
        $return_rows = null;
		$payments        = $this->getInvoicePaymentsV2($inv->id);
		$return_payments = $inv->return_id ? $this->getInvoicePaymentsV2($inv->return_id) : null;
        $active_tax        =$this->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->getInvoicePaymentsV2($inv->return_id) : null;

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->Settings->site_name;

		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $this->Settings->default_currency;
				$currencyRate= 1;
			}

		//$this->sma->print_arrays($inv);

		if($inv->sale_status == 'returned' || $inv->sale_status == 'completed'){

        	$username=$zatcaConfig->binarySecurityToken;
			$password=$zatcaConfig->secret;
            $api_type=$zatcaConfig->api_type;


            if($customer->vat_no!=""){

			    $url = "https://gw-fatoora.zatca.gov.sa/e-invoicing/".$api_type."/invoices/clearance/single";
                $headers = array(
                    "Content-Type: application/json",
                    "Accept-Version: V2",
                    "Accept-Language: en",
                    "Authorization: Basic " . base64_encode( $username . ":" . $password),
                 );

            }else{
                $url = "https://gw-fatoora.zatca.gov.sa/e-invoicing/".$api_type."/invoices/reporting/single";
                $headers = array(
                    "Content-Type: application/json",
                    "Accept-Version: V2",
                    "Accept-Language: en",
                    "Clearance-Status: 0",
                    "Authorization: Basic " . base64_encode( $username . ":" . $password),
                 );

            }


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
    	// That needs to be set; content will spill to STDOUT otherwise
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	// Set more options
    	curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$data = '{"invoiceHash":"'.$invDoc->stamp_hash.'","uuid":"'.$inv->uuid.'","invoice":"'.$invDoc->synch_hash.'"}';
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

     	//for debug only!
     	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


    	$content = curl_exec($ch);

		$httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);






        //$this->sma->print_arrays($content);




			$res=json_decode($content);
            //$this->sma->print_arrays($res);


            if($res->reportingStatus=="REPORTED"){
                $synch_status=$res->reportingStatus;
            $this->db->update('sales', ['synch' => "1",'synch_status' => $synch_status], ['id' => $inv->id]);

            }
            if($res->clearanceStatus=="CLEARED"){
                $synch_status=$res->clearanceStatus;
            $this->db->update('sales', ['synch' => "1",'synch_status' => $synch_status], ['id' => $inv->id]);

            if(!empty($res->clearedInvoice)){
                $xml_file_path         = $this->getInvoiceByID($inv->id)->xml_file_path;
                $dom = new DOMDocument('1.0', 'UTF-8');
                $XML=base64_decode($res->clearedInvoice);
                $dom->loadXML($XML);

                $dom->save('./assets/xml/'.$xml_file_path.'clearedInvoice.xml');
                $this->db->update('sale_documents', ['clearedInvoice' => $res->clearedInvoice], ['sale_id' => $inv->id]);
                //$xml_download=anchor('admin/welcome/downloadXMLUBL/' . $xml_file_path.'clearedInvoice.xml', '<i class="fa fa-download"></i> <b>XML Cleared Invoice</b>', 'class="btn btn-primary btn-xs"');

            }


            }

			if($res->reportingStatus!="REPORTED" && $res->clearanceStatus!="CLEARED"){

				try {
					$this->sma->write_log('ERROR', 'An errors has been occurred while Sync the sale (Add:Sales_model.php)','sales','Id ('.$inv->id.') Response '.json_encode($res));
				} catch (Exception $e) {
				}

			}else{
				try {
					$xml_file_path         = $this->getInvoiceByID($inv->id)->xml_file_path;
					unlink('./assets/xml/'.$xml_file_path.'.xml');
					unlink('./assets/xml/'.$xml_file_path.'merged.xml');
					unlink('./assets/xml/'.$xml_file_path.'signed.xml');
					unlink('./assets/xml/'.$xml_file_path.'signedWQR.xml');
				} catch (Exception $e) {
				}
			}




        }else{
            //die('<i class="fa fa-times"></a> Failed!');
        }

    }

    public function getZatcaConfig()
    {
        $q = $this->db->get_where('zatca_config', ['id' => 1], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getZatcaConfigV2()
    {
        $q = $this->db->get_where('zatca_config', ['id' => 2], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getDateFormat($id)
    {
        $q = $this->db->get_where('date_format', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getInvoiceSaleNoByById($id)
    {

		$this->db->select('sale_no');
		$this->db->where(" (sale_no is not null) ");
		$q = $this->db->get_where('sales', ['id' => $id], 1);

        if ($q->num_rows() > 0) {
            return $q->row()->sale_no;
        }
        return false;
    }


	public function addSubscriptions($data)
    {
        if ($this->db->insert('subscriptions', $data)) {
			$tid = $this->db->insert_id();
            return $tid;
        }
        return false;
    }

	public function getSubscriptionsByID($id)
    {
        $q = $this->db->get_where('subscriptions', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function deleteSubscriptions($id)
    {
        if ($this->db->delete('subscriptions', ['id' => $id])) {
            return true;
        }
        return false;
    }

	public function updateSubscriptions($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('subscriptions', $data)) {
            return true;
        }
        return false;
    }

	public function getCustomersWithSubscriptionDuration($subscription_duration)
    {
        $this->db->select('(count(*)) as count');
		$q = $this->db->get_where('companies', ['subscription_duration' => $subscription_duration], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
    }

	public function getInvoiceDocBySaleID($sale_id)
    {
        $q = $this->db->get_where('sale_documents', ['sale_id' => $sale_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getInvoiceByIDForDubpicate($date,$customer_id,$biller_id,$warehouse_id,$total,$grand_total,$sale_status)
    {
		$cdate=date('Y-m-d');
        $q = $this->db->get_where('sales', ['date' => $date,'customer_id' => $customer_id,'biller_id' => $biller_id,'warehouse_id' => $warehouse_id,'total' => $total,'grand_total' => $grand_total,'sale_status' => $sale_status,'pos' => '0','Date(supply_date)' => $cdate], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getReturnBySIDV2($sale_id)
    {
		$this->db->select('sum(total) as total,sum(product_discount) as product_discount,sum(order_discount) as order_discount,sum(total_discount) as total_discount,sum(product_tax) as product_tax,sum(order_tax) as order_tax,sum(total_tax) as total_tax,sum(discount_allowance) as discount_allowance,sum(donation) as donation,sum(paid) as paid,sum(grand_total) as grand_total,sum(shipping) as shipping');
        $q = $this->db->get_where('sales', ['sale_id' => $sale_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getAllInvoice($warehouse_id = null)
    {
		$this->db->select('id');
		$this->db->where('sale_status', 'pending');
        $this->db->where('id !=', '4274');
        $this->db->where('id !=', '4271');
        $this->db->where('id !=', '4059');
        $this->db->where('id !=', '2841');
        $this->db->where('id !=', '2842');
        $this->db->where('id !=', '2942');
        if($warehouse_id){
            $this->db->where('warehouse_id', $warehouse_id);

        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
		}
        return false;
    }

	public function deleteSaleV2(){
		$references = [
    'SALE2026/06/3613',
    'SALE2026/06/3619',
    'SALE2026/06/3622',
    'SALE2026/06/3628',
    'SALE2026/06/3633',
    'SALE2026/06/3634',
    'SALE2026/06/3643',
    'SALE2026/06/3644',
    'SALE2026/06/3647',
    'SALE2026/06/3650',
    'SALE2026/06/3651',
    'SALE2026/06/3677',
    'SALE2026/06/3680',
    'SALE2026/06/3682',
    'SALE2026/06/3683',
    'SALE2026/06/3684',
    'SALE2026/06/3685',
    'SALE2026/06/3687',
    'SALE2026/06/3689',
    'SALE2026/06/3690',
    'SALE2026/06/3694',
    'SALE2026/06/3695',
    'SALE2026/06/3729',
    'SALE2026/06/3730',
    'SALE2026/06/3731',
    'SALE2026/06/3732',
    'SALE2026/06/3733',
    'SALE2026/06/3734',
    'SALE2026/06/3735',
    'SALE2026/06/3736',
    'SALE2026/06/3737',
    'SALE2026/06/3738',
    'SALE2026/06/3739',
    'SALE2026/06/3743',
    'SALE2026/06/3744',
    'SALE2026/06/3748',
    'SALE2026/06/3806',
    'SALE2026/06/3807',
    'SALE2026/06/3809',
    'SALE2026/06/3810',
    'SALE2026/06/3811',
    'SALE2026/06/3812',
    'SALE2026/06/3814',
    'SALE2026/06/3816',
    'SALE2026/06/3817',
    'SALE2026/06/3819',
    'SALE2026/06/3820',
    'SALE2026/06/3821',
    'SALE2026/06/3822',
    'SALE2026/06/3823',
    'SALE2026/06/3824',
    'SALE2026/06/3825',
    'SALE2026/06/3826',
    'SALE2026/06/3827',
    'SALE2026/06/3829',
    'SALE2026/06/3880',
    'SALE2026/06/3881',
    'SALE2026/06/3882',
    'SALE2026/06/3883',
    'SALE2026/06/3899',
    'SALE2026/06/3900',
    'SALE2026/06/3901',
    'SALE2026/06/3902',
    'SALE2026/06/3903',
    'SALE2026/06/3904',
    'SALE2026/06/3905',
    'SALE2026/06/3907',
    'SALE2026/06/3908',
    'SALE2026/06/4012',
    'SALE2026/06/4013',
    'SALE2026/06/4014',
    'SALE2026/06/4015',
    'SALE2026/06/4018',
    'SALE2026/06/4019',
    'SALE2026/06/4020',
    'SALE2026/06/4021',
    'SALE2026/06/4022',
    'SALE2026/06/4023',
    'SALE2026/06/4024',
    'SALE2026/06/4026',
    'SALE2026/06/4027',
    'SALE2026/06/4029',
    'SALE2026/06/4061',
    'SALE2026/06/4093',
    'SALE2026/06/4094',
    'SALE2026/06/4096',
    'SALE2026/06/4097',
    'SALE2026/06/4098',
    'SALE2026/06/4099',
    'SALE2026/06/4103',
    'SALE2026/06/4104',
    'SALE2026/06/4105',
    'SALE2026/06/4106',
    'SALE2026/06/4109',
    'SALE2026/06/4110',
    'SALE2026/06/4111',
    'SALE2026/06/4160',
    'SALE2026/06/4161',
    'SALE2026/06/4162',
    'SALE2026/06/4163',
    'SALE2026/06/4164',
    'SALE2026/06/4165',
    'SALE2026/06/4167',
    'SALE2026/06/4170',
    'SALE2026/06/4171',
    'SALE2026/06/4172',
    'SALE2026/06/4186',
    'SALE2026/06/4194'
];
$this->db->select('id');
$this->db->where_in('reference_no', $references);
$q = $this->db->get('sales');

if ($q->num_rows() > 0) {
    foreach ($q->result() as $row) {
        $data[] = $row;
    }
    return $data;
}

		return false;
	}

    // =========================================================================
    // Modern SaaS Sales & Revenue Dashboard Methods
    // =========================================================================

    public function getSalesDashboardStats($warehouse_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select("
            COUNT(*) as total_invoices,
            COALESCE(SUM(grand_total), 0) as total_sales,
            COALESCE(SUM(total), 0) as net_sales,
            COALESCE(SUM(paid), 0) as total_paid,
            COALESCE(SUM(GREATEST(0, grand_total - paid)), 0) as total_due,
            COALESCE(SUM(total_tax), 0) as total_tax,
            COALESCE(SUM(total_discount), 0) as total_discount,
            COUNT(DISTINCT customer_id) as total_customers,
            SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_invoices,
            SUM(CASE WHEN payment_status = 'due' OR payment_status = 'unpaid' THEN 1 ELSE 0 END) as unpaid_invoices,
            SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) as partial_invoices,
            SUM(CASE WHEN payment_status = 'paid' THEN grand_total ELSE 0 END) as cash_sales,
            SUM(CASE WHEN payment_status != 'paid' THEN grand_total ELSE 0 END) as credit_sales,
            SUM(CASE WHEN sale_status = 'returned' OR (return_sale_ref IS NOT NULL AND return_sale_ref != '') THEN 1 ELSE 0 END) as returned_invoices,
            COALESCE(SUM(CASE WHEN return_sale_total IS NOT NULL THEN return_sale_total ELSE 0 END), 0) as returned_amount
        ", false);
        $this->db->from('sales');
        if ($warehouse_id) {
            $this->db->where('warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $q = $this->db->get();
        $stats = $q->row_array();

        $total_invoices = (int)($stats['total_invoices'] ?? 0);
        $total_sales = (float)($stats['total_sales'] ?? 0);
        $stats['average_invoice_value'] = ($total_invoices > 0) ? round($total_sales / $total_invoices, 2) : 0;

        // Profit calculation
        $this->db->select("
            COALESCE(SUM(si.subtotal), 0) as item_sales,
            COALESCE(SUM(si.quantity * p.cost), 0) as item_cost
        ", false);
        $this->db->from('sale_items si');
        $this->db->join('sales s', 's.id = si.sale_id', 'inner');
        $this->db->join('products p', 'p.id = si.product_id', 'inner');
        if ($warehouse_id) {
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('s.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('s.date <=', $end_date);
        }
        $q_profit = $this->db->get();
        $p_row = $q_profit->row_array();

        $item_sales = (float)($p_row['item_sales'] ?? 0);
        $item_cost = (float)($p_row['item_cost'] ?? 0);
        $total_profit = $item_sales - $item_cost;
        $profit_margin = ($item_sales > 0) ? round(($total_profit / $item_sales) * 100, 2) : 0;

        $stats['total_profit'] = $total_profit;
        $stats['profit_margin'] = $profit_margin;

        return $stats;
    }

    public function getSalesMonthlyTrends($limit = 12, $warehouse_id = null)
    {
        $this->db->select("
            DATE_FORMAT(date, '%Y-%m') as month,
            COUNT(*) as invoice_count,
            COALESCE(SUM(grand_total), 0) as total_sales,
            COALESCE(SUM(total), 0) as net_sales,
            COALESCE(SUM(paid), 0) as total_paid,
            COALESCE(SUM(GREATEST(0, grand_total - paid)), 0) as total_due
        ", false);
        $this->db->from('sales');
        if ($warehouse_id) {
            $this->db->where('warehouse_id', (int)$warehouse_id);
        }
        $this->db->where("date >= DATE_SUB(CURDATE(), INTERVAL " . (int)$limit . " MONTH)", null, false);
        $this->db->group_by("DATE_FORMAT(date, '%Y-%m')");
        $this->db->order_by('month', 'ASC');
        $q = $this->db->get();
        $trends = ($q->num_rows() > 0) ? $q->result() : [];

        // Attach monthly profit
        foreach ($trends as $t) {
            $this->db->select("
                COALESCE(SUM(si.subtotal - (si.quantity * p.cost)), 0) as month_profit
            ", false);
            $this->db->from('sale_items si');
            $this->db->join('sales s', 's.id = si.sale_id', 'inner');
            $this->db->join('products p', 'p.id = si.product_id', 'inner');
            $this->db->where("DATE_FORMAT(s.date, '%Y-%m') =", $t->month);
            if ($warehouse_id) {
                $this->db->where('s.warehouse_id', (int)$warehouse_id);
            }
            $qp = $this->db->get();
            $t->profit = (float)($qp->row()->month_profit ?? 0);
        }

        return $trends;
    }

    public function getSalesDailyTrends($start_date = null, $end_date = null, $warehouse_id = null)
    {
        $this->db->select("
            DATE(date) as day,
            COUNT(*) as invoice_count,
            COALESCE(SUM(grand_total), 0) as total_sales,
            COALESCE(SUM(paid), 0) as total_paid,
            COALESCE(SUM(GREATEST(0, grand_total - paid)), 0) as total_due
        ", false);
        $this->db->from('sales');
        if ($warehouse_id) {
            $this->db->where('warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        } else {
            $this->db->where("date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)", null, false);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $this->db->group_by("DATE(date)");
        $this->db->order_by('day', 'ASC');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getTopSellingProducts($limit = 5, $warehouse_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select("
            p.id,
            p.code,
            p.name,
            u.name as unit_name,
            COALESCE(SUM(si.quantity), 0) as sold_qty,
            COALESCE(SUM(si.subtotal), 0) as sales_amount,
            COALESCE(SUM(si.subtotal - (si.quantity * p.cost)), 0) as profit
        ", false);
        $this->db->from('sale_items si');
        $this->db->join('sales s', 's.id = si.sale_id', 'inner');
        $this->db->join('products p', 'p.id = si.product_id', 'inner');
        $this->db->join('units u', 'u.id = p.unit', 'left');
        if ($warehouse_id) {
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('s.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('s.date <=', $end_date);
        }
        $this->db->group_by('si.product_id');
        $this->db->order_by('sales_amount', 'DESC');
        $this->db->limit($limit);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getTopCustomers($limit = 5, $warehouse_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select("
            c.id,
            c.name,
            c.company,
            c.phone,
            COUNT(s.id) as invoice_count,
            COALESCE(SUM(s.grand_total), 0) as total_sales,
            COALESCE(SUM(s.paid), 0) as total_paid,
            COALESCE(SUM(GREATEST(0, s.grand_total - s.paid)), 0) as total_due
        ", false);
        $this->db->from('sales s');
        $this->db->join('companies c', 'c.id = s.customer_id', 'inner');
        if ($warehouse_id) {
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('s.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('s.date <=', $end_date);
        }
        $this->db->group_by('s.customer_id');
        $this->db->order_by('total_sales', 'DESC');
        $this->db->limit($limit);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getPaymentMethodDistribution($warehouse_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select("
            COALESCE(p.paid_by, 'other') as payment_method,
            COUNT(*) as payment_count,
            COALESCE(SUM(p.amount), 0) as total_amount
        ", false);
        $this->db->from('payments p');
        $this->db->join('sales s', 's.id = p.sale_id', 'inner');
        $this->db->where('p.sale_id IS NOT NULL', null, false);
        $this->db->where('p.sale_id >', 0);
        if ($warehouse_id) {
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('s.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('s.date <=', $end_date);
        }
        $this->db->group_by('p.paid_by');
        $this->db->order_by('total_amount', 'DESC');
        $q = $this->db->get();
        $methods = ($q->num_rows() > 0) ? $q->result() : [];

        $grand_amount = 0;
        foreach ($methods as $m) {
            $grand_amount += max(0, (float)$m->total_amount);
        }

        foreach ($methods as $m) {
            $m->pct = ($grand_amount > 0) ? round((max(0, (float)$m->total_amount) / $grand_amount) * 100, 1) : 0;
        }

        return $methods;
    }

    public function getWarehouseSalesDistribution($start_date = null, $end_date = null)
    {
        $this->db->select("
            w.id,
            w.name,
            w.code,
            COUNT(s.id) as invoice_count,
            COALESCE(SUM(s.grand_total), 0) as total_sales,
            COALESCE(SUM(s.paid), 0) as total_paid,
            COALESCE(SUM(GREATEST(0, s.grand_total - s.paid)), 0) as total_due
        ", false);
        $this->db->from('warehouses w');
        $this->db->join('sales s', 's.warehouse_id = w.id', 'left');
        if ($start_date) {
            $this->db->where('s.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('s.date <=', $end_date);
        }
        $this->db->group_by('w.id');
        $this->db->order_by('total_sales', 'DESC');
        $q = $this->db->get();
        $warehouses = ($q->num_rows() > 0) ? $q->result() : [];

        $grand_sales = 0;
        foreach ($warehouses as $wh) {
            $grand_sales += max(0, (float)$wh->total_sales);
        }

        foreach ($warehouses as $wh) {
            $wh->pct = ($grand_sales > 0) ? round((max(0, (float)$wh->total_sales) / $grand_sales) * 100, 1) : 0;
        }

        return $warehouses;
    }

    public function getSalesStatusSummary($warehouse_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select("
            COUNT(*) as total_invoices,
            SUM(CASE WHEN sale_status = 'completed' THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN sale_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN sale_status = 'returned' OR (return_sale_ref IS NOT NULL AND return_sale_ref != '') THEN 1 ELSE 0 END) as returned_count,
            SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
            SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) as partial_count,
            SUM(CASE WHEN payment_status = 'due' OR payment_status = 'unpaid' THEN 1 ELSE 0 END) as due_count
        ", false);
        $this->db->from('sales');
        if ($warehouse_id) {
            $this->db->where('warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $q = $this->db->get();
        $row = $q->row_array();

        $total = max(1, (int)($row['total_invoices'] ?? 0));

        return [
            'completed_count' => (int)($row['completed_count'] ?? 0),
            'completed_pct'   => round(((int)($row['completed_count'] ?? 0) / $total) * 100, 1),
            'pending_count'   => (int)($row['pending_count'] ?? 0),
            'pending_pct'     => round(((int)($row['pending_count'] ?? 0) / $total) * 100, 1),
            'returned_count'  => (int)($row['returned_count'] ?? 0),
            'returned_pct'    => round(((int)($row['returned_count'] ?? 0) / $total) * 100, 1),
            'paid_count'      => (int)($row['paid_count'] ?? 0),
            'paid_pct'        => round(((int)($row['paid_count'] ?? 0) / $total) * 100, 1),
            'partial_count'   => (int)($row['partial_count'] ?? 0),
            'partial_pct'     => round(((int)($row['partial_count'] ?? 0) / $total) * 100, 1),
            'due_count'       => (int)($row['due_count'] ?? 0),
            'due_pct'         => round(((int)($row['due_count'] ?? 0) / $total) * 100, 1),
            'total_invoices'  => (int)($row['total_invoices'] ?? 0),
        ];
    }

    public function getSalesAttentionRequired($warehouse_id = null)
    {
        $this->db->select("
            COUNT(CASE WHEN (due_date IS NOT NULL AND due_date < CURDATE() AND (grand_total - paid) > 0) THEN 1 END) as overdue_count,
            COALESCE(SUM(CASE WHEN (due_date IS NOT NULL AND due_date < CURDATE() AND (grand_total - paid) > 0) THEN (grand_total - paid) ELSE 0 END), 0) as overdue_amount,
            COUNT(CASE WHEN payment_status = 'due' OR payment_status = 'unpaid' THEN 1 END) as unpaid_count,
            COALESCE(SUM(CASE WHEN payment_status = 'due' OR payment_status = 'unpaid' THEN (grand_total - paid) ELSE 0 END), 0) as unpaid_amount,
            COUNT(CASE WHEN payment_status = 'partial' THEN 1 END) as partial_count,
            COALESCE(SUM(CASE WHEN payment_status = 'partial' THEN (grand_total - paid) ELSE 0 END), 0) as partial_amount
        ", false);
        $this->db->from('sales');
        if ($warehouse_id) {
            $this->db->where('warehouse_id', (int)$warehouse_id);
        }
        $q = $this->db->get();
        return $q->row_array() ?? [
            'overdue_count'  => 0,
            'overdue_amount' => 0,
            'unpaid_count'   => 0,
            'unpaid_amount'  => 0,
            'partial_count'  => 0,
            'partial_amount' => 0,
        ];
    }

    public function getRecentSales($limit = 8, $warehouse_id = null)
    {
        $this->db->select("
            s.id,
            s.reference_no,
            s.date,
            s.customer,
            s.grand_total,
            s.paid,
            (s.grand_total - s.paid) as due,
            s.sale_status,
            s.payment_status,
            u.first_name,
            u.last_name,
            w.name as warehouse_name
        ", false);
        $this->db->from('sales s');
        $this->db->join('users u', 'u.id = s.created_by', 'left');
        $this->db->join('warehouses w', 'w.id = s.warehouse_id', 'left');
        if ($warehouse_id) {
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
        }
        $this->db->order_by('s.id', 'DESC');
        $this->db->limit($limit);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getSalesRepPerformance($limit = 5, $warehouse_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select("
            u.id,
            CONCAT(u.first_name, ' ', u.last_name) as rep_name,
            u.email,
            COUNT(s.id) as invoice_count,
            COALESCE(SUM(s.grand_total), 0) as total_sales,
            COALESCE(SUM(s.paid), 0) as total_paid,
            COALESCE(SUM(GREATEST(0, s.grand_total - s.paid)), 0) as total_due
        ", false);
        $this->db->from('sales s');
        $this->db->join('users u', 'u.id = s.created_by', 'inner');
        if ($warehouse_id) {
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('s.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('s.date <=', $end_date);
        }
        $this->db->group_by('s.created_by');
        $this->db->order_by('total_sales', 'DESC');
        $this->db->limit($limit);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

}

