<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Carsquotes_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addQuote($data = [], $items = [])
    {
        if ($this->db->insert('quotes', $data)) {
            $quote_id = $this->db->insert_id();
            if ($this->site->getReference('qu') == $data['reference_no']) {
                $this->site->updateReference('qu');
            }
            foreach ($items as $item) {
                $item['quote_id'] = $quote_id;
                $this->db->insert('quote_items', $item);
                if($item['product_type']=='standard'){
                if ($data['status'] == 'completed') {

                $this->db->update('cars_vehicles', ['status' => 'reserved'], ['vehicle_id' => $item['product_id']]);
                }
            }


            }
            return true;
        }
        return false;
    }

    public function deleteQuote($id)
    {
        if ($this->db->delete('quote_items', ['quote_id' => $id]) && $this->db->delete('quotes', ['id' => $id])) {
            return true;
        }

        return false;
    }

    public function getAllQuoteItems($quote_id)
    {
        $this->db->select('quote_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.image, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('products', 'products.id=quote_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=quote_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=quote_items.tax_rate_id', 'left')
            ->group_by('quote_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('quote_items', ['quote_id' => $quote_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllQuoteItemsWithDetails($quote_id)
    {
        $this->db->select('quote_items.id, quote_items.product_name, quote_items.product_code, quote_items.quantity, quote_items.serial_no, quote_items.tax, quote_items.unit_price, quote_items.val_tax, quote_items.discount_val, quote_items.gross_total, products.details, products.hsn_code as hsn_code, products.second_name as second_name');
        $this->db->join('products', 'products.id=quote_items.product_id', 'left');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('quotes_items', ['quote_id' => $quote_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('quote_items', ['id' => $id], 1);
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

    public function getProductComboItems($pid, $warehouse_id)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->where('warehouses_products.warehouse_id', $warehouse_id)
            ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', ['combo_items.product_id' => $pid]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

    public function getProductNames($term, $warehouse_id, $limit = 5)
    {
        $this->db->select('products.*')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('products.id');

        $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");

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

    public function getProductOptions($product_id, $warehouse_id)
    {
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, warehouses_products_variants.quantity as quantity')
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=product_variants.warehouse_id', 'left')
            ->where('product_variants.product_id', $product_id)
            ->where('warehouses_products_variants.warehouse_id', $warehouse_id)
            ->where('warehouses_products_variants.quantity >', 0)
            ->group_by('product_variants.id');
        $q = $this->db->get('product_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', ['warehouse_id' => $warehouse_id, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getWHProduct($id)
    {
        $this->db->select('products.id, code, name, warehouses_products.quantity, cost, tax_rate')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('products.id');
        $q = $this->db->get_where('products', ['warehouses_products.product_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateQuote($id, $data, $items = [])
    {
        if ($this->db->update('quotes', $data, ['id' => $id]) && $this->db->delete('quote_items', ['quote_id' => $id])) {
            foreach ($items as $item) {
                $item['quote_id'] = $id;
                $this->db->insert('quote_items', $item);
                if($item['product_type']=='standard'){
                    if ($data['status'] == 'completed') {

                    $this->db->update('cars_vehicles', ['status' => 'reserved'], ['vehicle_id' => $item['product_id']]);
                    }
                }



            }
            return true;
        }
        return false;
    }

    public function updateStatus($id, $status, $note)
    {

        $inv = $this->getQuoteByID($id);
        $items = $this->getAllQuoteItems($id);
        $payments = $this->getInvoicePayments($id);



        if ($this->db->update('quotes', ['status' => $status, 'note' => $note], ['id' => $id])) {


            if($inv->status!=$status){
                $date = date('Y-m-d H:i:s');


                foreach ($payments as $payment) {

                        if ($status == 'canceled') {

                           if($payment->retrieved==1){


                            $return_sale_ex_ref=$this->site->getReference('ex');


				//$this->sma->print_arrays($total_shipping);
			$data_ex = [
                'date'         => $date,
                'reference'    => $return_sale_ex_ref,
                'amount'       => abs($payment->amount),
                'grand_total'       => abs($payment->amount),
                'total'       => abs($payment->amount),
				'payment_status'       => 'paid',
                'created_by'   => $payment->created_by,
                'warehouse_id' => $inv->warehouse_id,
                'other' => 1,
			];
			if($this->addExpense($data_ex)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}

                            $return_sale_re_ref=$this->site->getReference('re');
                            $data_re = [
                                'date'         => $date,
                                'reference'    => $return_sale_re_ref,
                                'amount'       => abs($payment->amount),
                                'created_by'   => $payment->created_by,
                                'warehouse_id' => $inv->warehouse_id,
                            ];
                            if($this->addRevenue($data_re)){
                            //$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
                            //$return_ex_id=$expense->id;
                            }


                           }else{

                            $return_sale_re_ref=$this->site->getReference('re');
            $data_re = [
                'date'         => $date,
                'reference'    => $return_sale_re_ref,
                'amount'       => abs($payment->amount),
                'created_by'   => $payment->created_by,
                'warehouse_id' => $inv->warehouse_id,
			];
			if($this->addRevenue($data_re)){
			//$expense                  = $this->purchases_model->getExpenseByReference($return_sale_ex_ref);
			//$return_ex_id=$expense->id;
			}


                           }


                           // $this->db->update('cars_vehicles', ['status' => 'available'], ['vehicle_id' => $item->product_id,'status' => 'reserved']);




                            }
                    }

            foreach ($items as $item) {

            if($item->product_type=='standard'){
                if ($status == 'completed') {

                $this->db->update('cars_vehicles', ['status' => 'reserved'], ['vehicle_id' => $item->product_id]);
                }else if ($status == 'canceled') {

                    $this->db->update('cars_vehicles', ['status' => 'available'], ['vehicle_id' => $item->product_id,'status' => 'reserved']);




                    }
                }
            }
        }
            return true;
        }
        return false;
    }


    public function addPayment($data = [], $customer_id = null, $payment_type = null, $payment_status = null)
    {
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('pay') == $data['reference_no']) {
                $this->site->updateReference('pay');
            }
            //$this->site->syncSalePayments($data['sale_id']);
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['cc_no']);
                $this->db->update('gift_cards', ['balance' => ($gc->balance - $data['amount'])], ['card_no' => $data['cc_no']]);
            } elseif ($customer_id && $data['paid_by'] == 'deposit') {
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $data['amount'])], ['id' => $customer_id]);
            }





			/*if ($this->Settings->accounting && $data['paid_by']!="deposit"){
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



					$entryCreditAccount= [];
					$entryCreditBalance= [];

					if ($sale->sale_status == 'pending'){
					//$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_by_road_acct;










					$entryDebitAccount= [];
					if($data['paid_by']=='gift_card')
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_gift_card_payments;
					else
						$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;

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




					}else{
					if ($sale->sale_status == 'completed'){





					 $entryDebitAccount= [];


					if($data['paid_by']=='gift_card')
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_gift_card_payments;
					else
						$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;




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
					else
						$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;






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

			}*/


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

    public function getInvoicePayments($quote_id)
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

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalQuotesPayments($quote_id)
    {
        $this->db->select('COALESCE(SUM(amount),0) as amount');
		$q = $this->db->get_where('payments', ['quote_id' => $quote_id],1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

    }


    public function updatePayment($id, $data = [], $customer_id = null, $payment_type = null, $payment_status = null)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->update('payments', $data, ['id' => $id])) {
            //$this->site->syncSalePayments($data['sale_id']);
            if ($opay->paid_by == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($opay->cc_no);
                $this->db->update('gift_cards', ['balance' => ($gc->balance + $opay->amount)], ['card_no' => $opay->cc_no]);
            } elseif ($opay->paid_by == 'deposit') {
                if (!$customer_id) {
                    $sale        = $this->getInvoiceByID($opay->quote_id);
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









			/*if ($this->Settings->accounting){
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



					$entryCreditAccount= [];
					$entryCreditBalance= [];

					if ($sale->sale_status == 'pending'){
					//$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_store_goods_by_road_acct;









					$entryDebitAccount= [];
					$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;

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





					}else{
					if ($sale->sale_status == 'completed'){





					 $entryDebitAccount= [];
					 $entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;






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
					 $entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;






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

			}*/


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



    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', ['id' => $id])) {
            //$this->site->syncSalePayments($opay->sale_id);
            if ($opay->paid_by == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($opay->cc_no);
                $this->db->update('gift_cards', ['balance' => ($gc->balance + $opay->amount)], ['card_no' => $opay->cc_no]);
            } elseif ($opay->paid_by == 'deposit') {
                $sale     = $this->getInvoiceByID($opay->quote_id);
                $customer = $this->site->getCompanyByID($sale->customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount + $opay->amount)], ['id' => $customer->id]);
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
    public function addExpense($data = [], $flage = NULL)
    {
        if ($this->db->insert('expenses', $data)) {
			$expense_id = $this->db->insert_id();

            if ($this->site->getReference('ex') == $data['reference']) {
                $this->site->updateReference('ex');






            }
			/*if ($this->Settings->accounting && $flage!="1"){
				$entryDescription="expenses#".$expense_id;
				$entry_type="expenses";


				$entryDebitAccount= [];

					$entryDebitAccount[] =  $data['account_id'];

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryCreditAccount=$this->site->getAccountsSettings()->expenses_shipping_acct;

					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference'],$entry_type,null,null,null,null,$data['date'],$entryDescription);

				}*/

            return true;
        }
        return false;
    }


}
