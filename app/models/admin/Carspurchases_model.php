<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Carspurchases_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addExpense($data = [], $flage = NULL, $payment = [], $payment_type = NULL)
    {
        if ($this->db->insert('expenses', $data)) {
			$expense_id = $this->db->insert_id();

            if ($this->site->getReference('ex') == $data['reference']) {
                $this->site->updateReference('ex');






            }

			if($payment){
				$payment['expense_id']=$expense_id;
				if ($this->db->insert('expense_payments', $payment)) {
            		if ($this->site->getReference('ppay',$payment['brId'] ? $payment['brId']  : null) == $payment['reference_no']) {
                		$this->site->updateReference('ppay',$payment['brId'] ? $payment['brId']  : null);
            		}
            		$this->site->syncExpensePayments($payment['expense_id']);
				}
			}

			if ($this->Settings->accounting && $flage!="1"){
				$entryDescription="expenses#".$expense_id;
				$entry_type="expenses";


				$entryDebitAccount= [];

					$entryDebitAccount[] =  $data['account_id'];

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					if($payment_type){
						$entryCreditAccount=$this->site->getPaymentTypeByID($payment_type)->payment_account;
					}else{
						$entryCreditAccount=$this->site->getAccountsSettings()->expenses_shipping_acct;
					}


					$costCenterDebitId= [];
					$costCenterDebitAmount= [];

					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryDebitAccount as $key => $acc){
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

						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);
					}



				}


            return true;
        }
        return false;
    }

    public function addPaymentApplication($data = [])
    {
        if ($this->db->insert('payment_application', $data)) {
            return true;
        }
        return false;
    }

	public function addPayment($data = [], $payment_type = null)
    {
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('ppay') == $data['reference_no']) {
                $this->site->updateReference('ppay');
            }
            $this->site->syncPurchasePayments($data['purchase_id']);




			if ($this->Settings->accounting){
				$entryDescription="Payments_Purchases_Invoice_No#".$data['purchase_id'];

				$purchase = $this->getPurchaseByID($data['purchase_id']);
				if ($purchase->receiving_status == 'pending' || $purchase->status == 'received' || $purchase->status == 'partial'){



					$entry_type="Purchases_Payments";
						/*if ($purchase->status == 'pending')
						$entry_type="purchase_pending_paid";
						if ($purchase->status == 'partial')
						$entry_type="purchase_partial";
						if ($purchase->status == 'received')
						$entry_type="purchase_receive";
					*/



						$entryDebitAccount= [];
						$entryDebitBalance= [];

						if ($purchase->receiving_status == 'pending'){
						//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryDebitAccount[] =  $entryDebitAcc;
						 $entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']);

						 $entryCreditAccount= [];
						//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryCreditAcc=="")
							$entryCreditAcc=$payment_type;
							 $entryCreditAccount[] =  $entryCreditAcc;


						/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}*/


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);
						/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						$entryCreditBalance[] =  $this->sma->formatDecimal($purchase->grand_total - $data['amount']);

						}*/


						}else{
						if ($purchase->status == 'received' || $purchase->status == 'partial'){
						//$entryDebitAccount[] =  $this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryDebitAccount[] =  $entryDebitAcc;
						//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
						//$amount=$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
						//$entryDebitBalance[] =  $amount;
						if($data['commercial_discount']!=0){
							$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']+$data['commercial_discount']);
						}else{
							$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']);
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
						//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryCreditAcc=="")
							$entryCreditAcc=$payment_type;
							 $entryCreditAccount[] =  $entryCreditAcc;

						/*
						if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}
						*/


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);



						if($data['commercial_discount']!=0){
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_discount_earned;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['commercial_discount']));


						}

						//if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						//$entryCreditBalance[] =  $this->sma->formatDecimal($purchase->grand_total - $data['amount']);

						//}

						}
						//if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total))
						//	$amount=$this->sma->formatDecimal($data['amount']);





						}







						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);








				}




				if ($purchase->status == 'returned'){



					$entry_type="Purchases_Payments";

					if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryDebitAccount[] =  $entryDebitAcc;
						//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
						//$amount=$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
						//$entryDebitBalance[] =  $amount;
						if($data['commercial_discount']!=0){
							$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['amount'])+abs($data['commercial_discount']));
						}else{
							$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['amount']));
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
						//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryCreditAcc=="")
							$entryCreditAcc=$payment_type;
							 $entryCreditAccount[] =  $entryCreditAcc;

						/*
						if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}
						*/


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['amount'])+$commisson_amount);


						if($data['commercial_discount']!=0){
						$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_discount_earned;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['commercial_discount']));


						}


						/*$entryDebitAccount= [];

						$entryWarehouseAcc=$this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
							if($entryWarehouseAcc=="")
							$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

						$entryDebitAccount[] =  $entryWarehouseAcc;
						//$entryDebitAccount[] =  $this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
						$amount=$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
						//if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total))
						//	$amount=$this->sma->formatDecimal($data['amount']);

						$entryDebitBalance= [];
						if(abs($purchase->shipping)>0){
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($purchase->grand_total)-abs($purchase->total_tax)+abs($purchase->shipping));
						}else{
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($purchase->grand_total)-abs($purchase->total_tax));
						}
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($purchase->total_tax));

						$commisson_amount=0;

						$entryCreditAccount= [];
						$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;



						if($this->sma->formatDecimal(abs($data['amount']))!=$this->sma->formatDecimal(abs($purchase->grand_total))){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['amount'])+$commisson_amount);
						if($this->sma->formatDecimal(abs($data['amount']))!=$this->sma->formatDecimal(abs($purchase->grand_total))){
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($purchase->grand_total) - abs($data['amount']));

						}

						if(abs($purchase->shipping)>0){
						$entryCreditAccount[] = $this->site->getAccountsSettings()->accounts_shipping_return_losses;
							$entryCreditBalance[] =  $this->sma->formatDecimal(abs($purchase->shipping));

						}*/





						$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);








				}

				}




            return true;
        }
        return false;
    }

    public function addExpensePayment($data = [], $payment_type = null)
    {
        if ($this->db->insert('expense_payments', $data)) {
            if ($this->site->getReference('ppay') == $data['reference_no']) {
                $this->site->updateReference('ppay');
            }
            $this->site->syncExpensePayments($data['expense_id']);





			if ($this->Settings->accounting){
				$entryDescription="Payments_expenses#".$data['expense_id'];

				$expense = $this->getExpenseByID($data['expense_id']);




					$entry_type="Payments_expenses";
						/*if ($purchase->status == 'pending')
						$entry_type="purchase_pending_paid";
						if ($purchase->status == 'partial')
						$entry_type="purchase_partial";
						if ($purchase->status == 'received')
						$entry_type="purchase_receive";
					*/



						$entryDebitAccount= [];
						$entryDebitBalance= [];


						//$entryDebitAccount[] =  $this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
							$entryDebitAcc=$this->site->getAccountsSettings()->expenses_shipping_acct;

						 $entryDebitAccount[] =  $entryDebitAcc;
						//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
						//$amount=$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
						//$entryDebitBalance[] =  $amount;
						$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']);

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
						//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryCreditAcc=="")
							$entryCreditAcc=$payment_type;
							 $entryCreditAccount[] =  $entryCreditAcc;

						/*
						if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}
						*/


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);
						//if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						//$entryCreditBalance[] =  $this->sma->formatDecimal($purchase->grand_total - $data['amount']);

						//}


						//if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total))
						//	$amount=$this->sma->formatDecimal($data['amount']);













						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);


				}


            return true;
        }
        return false;
    }

    public function addProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('warehouses_products_variants', ['quantity' => $nq], ['option_id' => $option_id, 'warehouse_id' => $warehouse_id])) {
                return true;
            }
        } else {
            if ($this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity])) {
                return true;
            }
        }
        return false;
    }

    public function addPurchase($data, $items,$general_total_net_unit_cost_quantity = null)
    {
        $this->db->trans_off();
		$this->db->trans_start();
		$sh_details=$this->site->getShowroomByID($data['warehouse_id']);
		$newowner_id=$sh_details->default_supplier;
        if($newowner=$this->site->getCompanyByID($newowner_id)->company){

		$data['newowner_id']=$newowner_id;
		$data['newowner']=$newowner;
		}

        if ($this->db->insert('purchases', $data)) {
            $purchase_id = $this->db->insert_id();
            if ($this->site->getReference('po') == $data['reference_no']) {
                $this->site->updateReference('po');
            }
            if ($this->site->getReference('rep') == $data['return_purchase_ref']) {
                $this->site->updateReference('rep');
            }

            foreach ($items as $item) {
                $item['purchase_id'] = $purchase_id;
                $item['option_id']   = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : null;

				if ($data['status'] == 'received') {

					//$this->db->update('cars_vehicles', ['active' => 1], ['vehicle_id' => $item['product_id']]);
					$this->db->update('cars_vehicles', ['active' => 1,'owner_id' => $newowner_id], ['vehicle_id' => $item['product_id']]);




				if($item['unit_quantity'] < $item['quantity']){

						$item['net_unit_cost']=$this->sma->formatDecimal(($item['net_unit_cost']/$item['quantity'])* $item['unit_quantity'],12);
						$item['real_unit_cost']=$this->sma->formatDecimal(($item['real_unit_cost']/$item['quantity'])* $item['unit_quantity'],12);
						//$shipping=$this->sma->formatDecimal(($shipping/$item['quantity'])* $item['unit_quantity'],12);
				}else if($item['unit_quantity'] > $item['quantity']){

					//$item['real_unit_cost']==$item['net_unit_cost'];
						$item['net_unit_cost']=$this->sma->formatDecimal($item['net_unit_cost']*($item['unit_quantity']/ $item['quantity']),12);
						$item['real_unit_cost']=$this->sma->formatDecimal($item['real_unit_cost']*($item['unit_quantity']/ $item['quantity']),12);


				}
				}


				if ($general_total_net_unit_cost_quantity) {

				if ($data['status'] == 'received' || $data['status'] == 'returned') {

					if ($data['order_discount']>0 || $data['order_discount']<0) {
					$original=($item['net_unit_cost'] * $item['quantity']);
					$distribute_value = $this->site->distributeAmountEqually($original,$data['order_discount'],$general_total_net_unit_cost_quantity);
					if($data['status'] == 'returned'){
					if($item['quantity']!=0)
						$item['net_unit_cost']=$this->sma->formatDecimal((((-1 * $original)+$distribute_value)/(-1 * $item['quantity'])),12);
					}else
					$item['net_unit_cost']=$this->sma->formatDecimal((($original-$distribute_value)/$item['quantity']),12);
				}


				$shipping_data=0;
				if($data['status'] == 'returned'){
				$shipping_data =$item['return_shipping'];
				}else{
				if ($data['order_discount']>0 || $data['order_discount']<0) {
				$shipping_amount_data = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				$original_shipping_data=($item['real_unit_cost'] * $item['quantity']);
				$shipping_data =$this->site->distributeAmountEqually($original_shipping_data,$shipping_amount_data,$general_total_net_unit_cost_quantity);
				}else{
				$shipping_amount_data = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				$original_shipping_data=($item['net_unit_cost'] * $item['quantity']);
				$shipping_data =$this->site->distributeAmountEqually($original_shipping_data,$shipping_amount_data,$general_total_net_unit_cost_quantity);

				}
				}
				$item['shipping']=$shipping_data;
				if($data['status'] == 'returned')
				$item['item_cost_value']=$this->sma->formatDecimal(($item['net_unit_cost'] * $item['quantity']) - $shipping_data,12);
				else
				$item['item_cost_value']=$this->sma->formatDecimal(($item['net_unit_cost'] * $item['quantity']) + $shipping_data,12);


                }







				}
                $this->db->insert('purchase_items', $item);
                if ($this->Settings->update_cost) {
                if(isset($data['currency_rate']) && $data['currency_rate']!=1){
				//    $this->db->update('products', ['cost' => $item['net_unit_cost'] * $data['currency_rate']], ['id' => $item['product_id']]);
				}else{
				//    $this->db->update('products', ['cost' => $item['net_unit_cost']], ['id' => $item['product_id']]);
				}
                }
                if ($item['option_id']) {
                  //  $this->db->update('product_variants', ['cost' => $item['net_unit_cost']], ['id' => $item['option_id'], 'product_id' => $item['product_id']]);
                }
                if ($data['status'] == 'received' || $data['status'] == 'returned') {
				if($data['status'] == 'returned'){
				//$shipping_amount = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				//$original_shipping=($item['real_unit_cost'] * $item['quantity']);
				$shipping =$item['return_shipping'];//$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				}else{
				if ($data['order_discount']>0 || $data['order_discount']<0) {
				$shipping_amount = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				$original_shipping=($item['real_unit_cost'] * $item['quantity']);
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				}else{
				$shipping_amount = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				$original_shipping=($item['net_unit_cost'] * $item['quantity']);
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				//$shipping = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);

				}
				}
                //if($data['status'] == 'returned')
				//$data['shipping']=(-1 * $data['shipping']);

				//$this->updateAVCO(['product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['quantity'], 'cost' => $item['net_unit_cost'],'shipping'=>$data['shipping'],'customs_fees'=>$data['customs_fees'],'Other_fees'=>$data['Other_fees']]);
                //if($data['status'] != 'returned')

				$net_unit_cost=$item['net_unit_cost'];
				if(isset($data['currency_rate']) && $data['currency_rate']!=1){
					$net_unit_cost = $net_unit_cost * $data['currency_rate'];
					$shipping = $shipping * $data['currency_rate'];
				}
				$this->updateAVCO(['product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['quantity'], 'cost' => $net_unit_cost,'shipping'=>$shipping,0,0,'status'=>$data['status']]);
                }
            }

            if ($data['status'] == 'returned') {
                $this->db->update('purchases', ['return_purchase_ref' => $data['return_purchase_ref'],'return_purchase_ex_ref' => $data['return_purchase_ex_ref'],'return_surcharge_ex_ref' => $data['return_surcharge_ex_ref'],'return_ex_id' => $data['return_ex_id'],'return_ex_s_id' => $data['return_ex_s_id'], 'surcharge' => $data['surcharge'], 'return_purchase_total' => $data['grand_total'], 'return_id' => $purchase_id], ['id' => $data['purchase_id']]);
            }

            if ($data['status'] == 'received' || $data['status'] == 'returned') {
                $this->site->syncQuantity(null, $purchase_id);
            }


			if ($this->Settings->accounting){
				$entryDescription="Purchase_Invoice_No#".$purchase_id;

				if(isset($data['currency_rate']) && $data['currency_rate']!=1){
					$data['grand_total'] = $data['grand_total'] * $data['currency_rate'];
					$data['total_tax'] = $data['total_tax'] * $data['currency_rate'];
					$data['shipping'] = $data['shipping'] * $data['currency_rate'];


				}


			if ($data['receiving_status'] == 'pending' || $data['status'] == 'received'){

				$entry_type="";
					if ($data['status'] == 'received')
					$entry_type="Purchase_invoice_received";
				if ($data['receiving_status'] == 'pending')
					$entry_type="Purchase_invoice_pendding";



				if($this->site->getCompanyByID($data['supplier_id'])->co_type==1){
					$entryDebitAccount= [];

					if ($data['receiving_status'] == 'pending'){
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
					}else if ($data['status'] == 'received'){
						if($this->Settings->accounting_type=="periodic"){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{
							$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}
					$entryDebitAccount[] =  $entryWarehouseAcc;
					//$entryDebitAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
					}

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryCreditAccount=$this->site->getAccountByRefId($data['supplier_id'],'local_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;



					$costCenterDebitId= [];
					$costCenterDebitAmount= [];

					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryDebitAccount as $key => $acc){
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


					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);

				}

				if($this->site->getCompanyByID($data['supplier_id'])->co_type==2){
					$entryDebitAccount= [];

					if ($data['receiving_status'] == 'pending'){
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
					}else if ($data['status'] == 'received'){
						if($this->Settings->accounting_type=="periodic"){
							$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{
							$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}

					$entryDebitAccount[] =  $entryWarehouseAcc;
					//$entryDebitAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
					}

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryCreditAccount=$this->site->getAccountByRefId($data['supplier_id'],'international_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;

					$costCenterDebitId= [];
					$costCenterDebitAmount= [];
					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryDebitAccount as $key => $acc){
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


					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);

				}




			}


			if ($data['status'] == 'returned' ){

				$entry_type="purchase_returned";


				if($this->site->getCompanyByID($data['supplier_id'])->co_type==1){

					if($this->Settings->accounting_type=="periodic"){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_return_acct;
							}else{

						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}
					$entryDebitAccount[] =  $entryWarehouseAcc;
					//$entryDebitAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					if(abs($data['shipping'])>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax'])+abs($data['shipping']));
					}else{
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax']));
					}
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['total_tax']));


					$entryCreditAccount= [];

					$entryCreditAcc=$this->site->getAccountByRefId($data['supplier_id'],'local_suppliers')->id;
						if($entryCreditAcc=="")
						$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
					$entryCreditAccount[] =  $entryCreditAcc;


					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['grand_total']));

					if(abs($data['shipping'])>0){
					$entryCreditAccount[] = $this->site->getAccountsSettings()->accounts_shipping_return_losses;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['shipping']));

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



				$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),$data['date'],$entryDescription);


				}

				if($this->site->getCompanyByID($data['supplier_id'])->co_type==2){
					$entryDebitAccount= [];
					if($this->Settings->accounting_type=="periodic"){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_return_acct;
							}else{

						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}
					$entryDebitAccount[] =  $entryWarehouseAcc;
					//$entryDebitAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					if(abs($data['shipping'])>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax'])+abs($data['shipping']));
					}else{
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['grand_total'])-abs($data['total_tax']));
					}
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['total_tax']));


					$entryCreditAccount= [];

					$entryCreditAcc=$this->site->getAccountByRefId($data['supplier_id'],'international_suppliers')->id;
						if($entryCreditAcc=="")
						$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
					$entryCreditAccount[] =  $entryCreditAcc;



					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['grand_total']));

					if(abs($data['shipping'])>0){
					$entryCreditAccount[] = $this->site->getAccountsSettings()->accounts_shipping_return_losses;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['shipping']));

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



						$this->site->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$data['reference_no'],$entry_type,null,null,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),$data['date'],$entryDescription);

				}




			}


			}


        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Add:Purchases_model.php)');
        } else {
            return true;
        }
        return false;
    }

    public function calculatePurchaseTotals($id, $return_id, $surcharge)
    {
        $purchase = $this->getPurchaseByID($id);
        $items    = $this->getAllPurchaseItems($id);
        if (!empty($items)) {
            $total            = 0;
            $product_tax      = 0;
            $order_tax        = 0;
            $product_discount = 0;
            $order_discount   = 0;
            foreach ($items as $item) {
                $product_tax      += $item->item_tax;
                $product_discount += $item->item_discount;
                $total            += $item->net_unit_cost * $item->quantity;
            }
            if ($purchase->order_discount_id) {
                $percentage        = '%';
                $order_discount_id = $purchase->order_discount_id;
                $opos              = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods            = explode('%', $order_discount_id);
                    $order_discount = (($total + $product_tax) * (float)($ods[0])) / 100;
                } else {
                    $order_discount = $order_discount_id;
                }
            }
            if ($purchase->order_tax_id) {
                $order_tax_id = $purchase->order_tax_id;
                if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                    if ($order_tax_details->type == 2) {
                        $order_tax = $order_tax_details->rate;
                    }
                    if ($order_tax_details->type == 1) {
                        $order_tax = (($total + $product_tax - $order_discount) * $order_tax_details->rate) / 100;
                    }
                }
            }
            $total_discount = $order_discount + $product_discount;
            $total_tax      = $product_tax    + $order_tax;
            $grand_total    = $total          + $total_tax          + $purchase->shipping - $order_discount          + $surcharge;
            $data           = [
                'total'            => $total,
                'product_discount' => $product_discount,
                'order_discount'   => $order_discount,
                'total_discount'   => $total_discount,
                'product_tax'      => $product_tax,
                'order_tax'        => $order_tax,
                'total_tax'        => $total_tax,
                'grand_total'      => $grand_total,
                'return_id'        => $return_id,
                'surcharge'        => $surcharge,
            ];

            if ($this->db->update('purchases', $data, ['id' => $id])) {
                return true;
            }
        } else {
            $this->db->delete('purchases', ['id' => $id]);
        }
        return false;
    }

    public function deleteExpense($id)
    {
        if ($this->db->delete('expenses', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', ['id' => $id])) {
            $this->site->syncPurchasePayments($opay->purchase_id);
            return true;
        }
        return false;
    }

    public function deletePaymentApplication($id)
    {
        if ($this->db->delete('payment_application', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteExpensePayment($id)
    {
        $opay = $this->getExpensePaymentByID($id);
        if ($this->db->delete('expense_payments', ['id' => $id])) {
            $this->site->syncExpensePayments($opay->expense_id);
            return true;
        }
        return false;
    }

    public function deletePurchase($id)
    {
        $this->db->trans_off();
		$this->db->trans_start();
        $purchase       = $this->getPurchaseByID($id);
        $purchase_items = $this->site->getAllPurchaseItems($id);
        $purchase_items_temp = $this->site->getAllPurchaseItems($id);
		$general_total_net_unit_cost_quantity=0;
        if ($this->db->delete('purchase_items', ['purchase_id' => $id]) && $this->db->delete('purchases', ['id' => $id])) {
            //$this->db->delete('payments', ['purchase_id' => $id]);
            if ($purchase->status == 'received' || $purchase->status == 'partial') {
                /*foreach ($purchase_items_temp as $oitem_temp) {
					if ($purchase->order_discount>0 || $purchase->order_discount<0) {
						$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($oitem_temp->quantity_received*$oitem_temp->real_unit_cost);
					}else{
						$general_total_net_unit_cost_quantity=$general_total_net_unit_cost_quantity+($oitem_temp->quantity_received*$oitem_temp->net_unit_cost);
					}
				}*/
                foreach ($purchase_items as $oitem) {
                    //$this->updateAVCO(['product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => (0 - $oitem->quantity), 'cost' => $oitem->net_unit_cost],'shipping'=>$purchase->shipping,'customs_fees'=>$purchase->customs_fees,'other_fees'=>$purchase->other_fees,'status'=>$purchase->status]);
                /*
				if($purchase->status == 'returned'){
				$shipping =$oitem->return_shipping;//$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				}else{
					//$general_total_net_unit_cost_quantity=($purchase->total+$purchase->product_discount);

				if ($purchase->order_discount>0 || $purchase->order_discount<0) {
				$shipping_amount = ($purchase->shipping ? $purchase->shipping :0) + ($purchase->customs_fees ? $purchase->customs_fees :0) + ($purchase->Other_fees ? $purchase->Other_fees :0);
				$original_shipping=($oitem->net_unit_cost * (0 - $oitem->quantity_received));
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				}else{
				$shipping_amount = ($purchase->shipping ? $purchase->shipping :0) + ($purchase->customs_fees ? $purchase->customs_fees :0) + ($purchase->Other_fees ? $purchase->Other_fees :0);
				$original_shipping=($oitem->net_unit_cost * (0 - $oitem->quantity_received));
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				//$shipping = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);

				}
				}*/

				if($purchase->status == 'returned')
				$quantity=(0-$oitem->unit_quantity);
				else
				$quantity=(0-$oitem->quantity_received);


					$this->updateAVCO(['product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => $quantity, 'cost' => $oitem->net_unit_cost,'shipping'=>$oitem->shipping,0,0,'status'=>'deleted']);
                    /*$received = $oitem->quantity_received ? $oitem->quantity_received : $oitem->quantity;
                    if ($oitem->quantity_balance < $received) {
                        $clause = ['purchase_id' => null, 'transfer_id' => null, 'product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'option_id' => $oitem->option_id];
                        $this->site->setPurchaseItem($clause, ($oitem->quantity_balance - $received));
                    }*/

					$received = $oitem->quantity_received ? $oitem->quantity_received : $oitem->quantity;
                    if ($oitem->quantity_balance < $received) {
                        $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'option_id' => $oitem->option_id);
                        $this->site->setPurchaseItem($clause, ($oitem->quantity_balance - $received));
                    }

					//$this->site->syncQuantity(null, null, null, $oitem->product_id);

                }
            }

			//$this->site->syncQuantity(null, $purchase_id);
			$this->site->syncQuantity(null, null, $purchase_items);

			if ($this->Settings->accounting){

				$entryDescription="Purchase_Invoice_No#".$id;

			$this->site->updateEntryMultiSideWithDelete($purchase->reference_no,$entryDescription);
			}
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Delete:Purchases_model.php)');
        } else {
            return true;
        }
        return false;
    }

    public function getAllProducts()
    {
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllPurchaseItems($purchase_id,$product_code = NULL)
    {
        $this->db->select("purchase_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, '1' as unit, cars_vehicles.code as details, product_variants.name as variant, cars_vehicles.code as hsn_code, cars_vehicles.code as second_name")
            ->join('cars_vehicles', 'cars_vehicles.vehicle_id=purchase_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=purchase_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=purchase_items.tax_rate_id', 'left')
            ->group_by('purchase_items.id')
            ->order_by('id', 'asc');
			if($product_code)
        $q = $this->db->get_where('purchase_items', ['purchase_id' => $purchase_id,'product_code' => $product_code]);
	else
        $q = $this->db->get_where('purchase_items', ['purchase_id' => $purchase_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getPurchaseByPurchase_id($purchase_id)
    {
        $q = $this->db->get_where('purchases', ['purchase_id' => $purchase_id]);
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
			}
        return false;
    }


    public function getAllPurchaseReturnItems($purchase_id,$product_code)
    {
        $this->db->select('SUM(unit_quantity) as unit_quantity')//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')

		//$this->db->select('purchase_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('purchases', 'purchases.id=purchase_items.purchase_id', 'left');
            $this->db->where('purchases.purchase_id', $purchase_id);

		$q = $this->db->get_where('purchase_items', ['product_code' => $product_code]);
	    if ($q->num_rows() > 0) {
               return $q->row();
        }
        return false;
    }

    public function getAllPurchaseItemsReturn($purchase_id,$product_code = NULL)
    {
        $this->db->select('purchase_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
        //$this->db->select('purchase_items.*')//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')
	         ->join('purchases', 'purchases.id=purchase_items.purchase_id', 'left')
            ->join('products', 'products.id=purchase_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=purchase_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=purchase_items.tax_rate_id', 'left')
            ->group_by('purchase_items.id')
            ->order_by('purchase_items.id', 'asc');
			 $this->db->where('purchases.purchase_id', $purchase_id);

		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getAllPurchases()
    {
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
	public function getAllPurchaseItemsToReturned()
    {
        /*$this->db->where('overselling =', 1);
		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }*/
		$this->db->select('purchase_id,warehouses.name as warehouse_name,product_code,product_name,quantity_received')
            //->join('purchases', 'purchases.id=purchase_items.purchase_id', 'left')
            ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
            //->group_by('purchase_id,warehouses.name,product_code,product_name')
            ->order_by('product_name', 'asc');
			$this->db->where('quantity_received !=', '');
			$this->db->where('purchase_id !=', '');
			$this->db->where('unit_quantity !=', 0);
			$this->db->where('status', 'received');
		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
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

    public function getAllReturnItems($return_id)
    {
        $this->db->select('return_purchase_items.*, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('products', 'products.id=return_purchase_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=return_purchase_items.option_id', 'left')
            ->group_by('return_purchase_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('return_purchase_items', ['return_id' => $return_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getExpenseByID($id)
    {
        $q = $this->db->get_where('expenses', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getExpenseByReference($reference)
    {
        $q = $this->db->get_where('expenses', ['reference' => $reference], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getExpenseCategories()
    {
        $q = $this->db->get('expense_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getExpenseCategoryByID($id)
    {
        $q = $this->db->get_where('expense_categories', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('purchase_items', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getOverSoldCosting($product_id)
    {
        $q = $this->db->get_where('costing', ['overselling' => 1]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getOverSoldCostingWP($product_id,$warehouse_id)
    {

		$this->db->select('costing.quantity_balance as quantity_balance,costing.sale_item_id as sale_item_id')
        ->join('sale_items', 'sale_items.id=costing.sale_item_id and sale_items.product_id=costing.product_id', 'left');
		$this->db->where('costing.overselling =', 1);
		$this->db->where('costing.product_id =', $product_id)->where('sale_items.product_id =', $product_id)->where('sale_items.warehouse_id =', $warehouse_id);
        $q = $this->db->get('costing');
        if ($q->num_rows() > 0) {
           foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }

        return false;
    }

    public function getOverSoldCostingSales($product_id,$warehouse_id)
    {


		$this->db->select('*');
        $this->db->where('quantity >', 0);
		$this->db->where('overselling =', 1);
		$this->db->where('product_type !=', 'combo');
		$this->db->where('warehouse_id =', $warehouse_id);
        $this->db->where('product_id =', $product_id);
		$this->db->order_by('id', 'asc');
        $q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
           foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }

        return false;
    }

 public function getOverSoldCostingSalesCombo($product_id,$warehouse_id)
    {


		$this->db->select('sale_items.id as id,sale_items.quantity as quantity,combo_overselling.item_cost_value as item_cost_value,combo_overselling.quantity_overselling as quantity_overselling,combo_overselling.quantity as quantityOS,sale_items.product_id as product_id,sale_items.sale_id as sale_id');
		$this->db->join('combo_overselling', 'combo_overselling.sale_id=sale_items.sale_id and combo_overselling.product_id=sale_items.product_id', 'left');

        $this->db->where('sale_items.quantity >', 0);
		$this->db->where('combo_overselling.quantity_overselling >', 0);
		$this->db->where('sale_items.overselling =', 1);
		$this->db->where('sale_items.product_type =', 'combo');
		$this->db->where('sale_items.warehouse_id =', $warehouse_id);
        $this->db->where('combo_overselling.item_id =', $product_id);
		$this->db->order_by('sale_items.id', 'asc');
        $q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
           foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

    public function getPaymentApplicationByID($id)
    {
        $q = $this->db->get_where('payment_application', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getExpensePaymentByID($id)
    {
        $q = $this->db->get_where('expense_payments', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getPaymentsForPurchase($purchase_id)
    {
        $this->db->select('payments.date, payments.paid_by,payments.cc_type, payments.amount, payments.reference_no, payments.cheque_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', ['purchase_id' => $purchase_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

    public function getProductByID($id)
    {
        $q = $this->db->get_where('products', ['id' => $id], 1);
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


    public function getProductNames($term, $limit = 5)
    {
		$this->db->select('cars_vehicles.vehicle_id as id,cars_vehicles.chesis_no as code,cars_vehicles.name as name,cars_vehicles.*', false);

		// $this->db->where("type = 'standard' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR supplier1_part_no LIKE '%" . $term . "%' OR supplier2_part_no LIKE '%" . $term . "%' OR supplier3_part_no LIKE '%" . $term . "%' OR supplier4_part_no LIKE '%" . $term . "%' OR supplier5_part_no LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
		$this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%'  OR  concat(name, ' (', chesis_no, ')') LIKE '%" . $term . "%')");
		$this->db->where('status', 'available');
		$this->db->where('active', 1);

		//$this->db->where('hide !=', 1);
		$this->db->limit($limit);
        $q = $this->db->get('cars_vehicles');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductOptions($product_id)
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

    public function getProductsByCode($code)
    {
        $this->db->select('*')->from('products')->like('code', $code, 'both');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductVariantByName($name, $product_id)
    {
        $q = $this->db->get_where('product_variants', ['name' => $name, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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

    public function getPurcahseItemByID($id)
    {
        $q = $this->db->get_where('purchase_items', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPurchaseByID($id)
    {
        $q = $this->db->get_where('purchases', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPurchasePayments($purchase_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', ['purchase_id' => $purchase_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPurchasePaymentApplication($purchase_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payment_application', ['purchase_id' => $purchase_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }


	public function getSumPurchasePaymentApplication($purchase_id)
    {
        $this->db->select('SUM(applied_amount) as applied_amount');//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')

        $this->db->where('purchase_id =', $purchase_id);
        $q = $this->db->get('payment_application');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
            return false;
    }


    public function getExpensePayments($expense_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('expense_payments', ['expense_id' => $expense_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
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
        $q = $this->db->get_where('return_purchases', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', ['warehouse_id' => $warehouse_id, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function resetProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', ['quantity' => $nq], ['option_id' => $option_id, 'warehouse_id' => $warehouse_id])) {
                return true;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq])) {
                return true;
            }
        }
        return false;
    }

    public function returnPurchase($data = [], $items = [])
    {
        $purchase_items = $this->site->getAllPurchaseItems($data['purchase_id']);

        if ($this->db->insert('return_purchases', $data)) {
            $return_id = $this->db->insert_id();
            if ($this->site->getReference('rep') == $data['reference_no']) {
                $this->site->updateReference('rep');
            }
            foreach ($items as $item) {
                $item['return_id'] = $return_id;
                $this->db->insert('return_purchase_items', $item);

                if ($purchase_item = $this->getPurcahseItemByID($item['purchase_item_id'])) {
                    if ($purchase_item->quantity == $item['quantity']) {
                        $this->db->delete('purchase_items', ['id' => $item['purchase_item_id']]);
                    } else {
                        $nqty          = $purchase_item->quantity          - $item['quantity'];
                        $bqty          = $purchase_item->quantity_balance  - $item['quantity'];
                        $rqty          = $purchase_item->quantity_received - $item['quantity'];
                        $tax           = $purchase_item->unit_cost         - $purchase_item->net_unit_cost;
                        $discount      = $purchase_item->item_discount / $purchase_item->quantity;
                        $item_tax      = $tax                      * $nqty;
                        $item_discount = $discount                 * $nqty;
                        $subtotal      = $purchase_item->unit_cost * $nqty;
                        $this->db->update('purchase_items', ['quantity' => $nqty, 'quantity_balance' => $bqty, 'quantity_received' => $rqty, 'item_tax' => $item_tax, 'item_discount' => $item_discount, 'subtotal' => $subtotal], ['id' => $item['purchase_item_id']]);
                    }
                }
            }
            $this->calculatePurchaseTotals($data['purchase_id'], $return_id, $data['surcharge']);
            $this->site->syncQuantity(null, null, $purchase_items);
            $this->site->syncQuantity(null, $data['purchase_id']);
            return true;
        }
        return false;
    }

    public function updateAVCO($data)
    {

        //$wp_details = $this->getWarehouseProductQuantity($data['warehouse_id'], $data['product_id']);
		if ($wp_details = $this->getWarehouseProductQuantity($data['warehouse_id'], $data['product_id'])) {
			$old_quantity=$wp_details->quantity;
		    $old_avg_cost=$wp_details->avg_cost;
		    //$wp_details->avg_cost;


			$data['shipping'] = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
            $data['status'] = ($data['status'] ? $data['status'] : '');
			$total_quantity = $wp_details->quantity + $data['quantity'];
            $total_quantity_inv = (0-$wp_details->quantity) + $data['quantity'];
            if($data['status'] == 'returned' || $data['status'] == 'deleted'){
			//$total_cost     = (($total_quantity * ($data['cost']+($data['shipping']/($total_quantity)))));
            $Ptotal     = $this->getSumPurchaseItemsCost($data['warehouse_id'], $data['product_id']);
            $Stotal     = $this->getSumSaleItemsCost($data['warehouse_id'], $data['product_id']);
            $SAVGtotal_item_cost_valueAVG=0;
			$SAVGtotal_quantityAVG=0;
			if($this->getSumSaleItemsCostComboAVG($data['warehouse_id'], $data['product_id'])){
			$SAVGtotals     = $this->getSumSaleItemsCostComboAVG($data['warehouse_id'], $data['product_id']);
			foreach ($SAVGtotals as $SAVGtotal) {
			$SAVGtotal_item_cost_valueAVG=$this->sma->formatDecimal($SAVGtotal_item_cost_valueAVG+$SAVGtotal->item_cost_value,12);
			$SAVGtotal_quantityAVG=$SAVGtotal_quantityAVG+$SAVGtotal->quantity;
			}
			}
            $SOStotal_item_cost_valueOS=0;
			$SOStotal_quantityOS=0;
			if($this->getSumSaleItemsCostComboOS($data['warehouse_id'], $data['product_id'])){
			$SOStotals     = $this->getSumSaleItemsCostComboOS($data['warehouse_id'], $data['product_id']);
            foreach ($SOStotals as $SOStotal) {
			$SOStotal_item_cost_valueOS=$this->sma->formatDecimal($SOStotal_item_cost_valueOS+$SOStotal->item_cost_value,12);
			$SOStotal_quantityOS=$SOStotal_quantityOS+$SOStotal->quantity;
			}
			}
            $total_cost = $this->sma->formatDecimal($Ptotal->item_cost_value - ($Stotal->item_cost_value + $SAVGtotal_item_cost_valueAVG + $SOStotal_item_cost_valueOS),12);
			$total_quantity = $Ptotal->quantity - ($Stotal->quantity + $SAVGtotal_quantityAVG + $SOStotal_quantityOS);
			if($total_cost<0)
				$total_cost=0;


			}else{

				if($wp_details->quantity < 0)
			$total_cost     = (($wp_details->quantity * ($data['cost']+($data['shipping']/$data['quantity']))) + ($data['quantity'] * ($data['cost']+($data['shipping']/$data['quantity']))));
            else
			$total_cost     = (($wp_details->quantity * $wp_details->avg_cost) + ($data['quantity'] * ($data['cost']+($data['shipping']/$data['quantity']))));
            }
			//if (!empty($total_quantity)) {
			if($total_quantity!=0){

					$avg_cost = $this->sma->formatDecimal(($total_cost / $total_quantity),12);

				if(($wp_details->quantity < 0) && $data['status'] != 'deleted' )
				{
					if($total_quantity<0)
						$old_quantity=$old_quantity-$total_quantity;

					$total_qty_prev = ($this->site->getBalanceQuantity_temp($data['product_id'], $data['warehouse_id']) - $data['quantity']) - ($this->site->getBalanceQuantity_avg_cost($data['product_id'], $data['warehouse_id']));



					$this->db->insert('warehouses_products_avg_cost', ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id'], 'total_qty_prev' => $total_qty_prev, 'avg_cost' => $old_avg_cost, 'new_avg_cost' => $avg_cost, 'quantity' => $old_quantity]);





				}
            	//else
					$this->db->update('warehouses_products', ['avg_cost' => $avg_cost], ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']]);
            }else{
				if($wp_details->quantity < 0)
				{
					$avg_cost = $this->sma->formatDecimal((($data['quantity'] * ($data['cost']+($data['shipping']/$data['quantity']))) / $data['quantity']),12);

					$total_qty_prev = ($this->site->getBalanceQuantity_temp($data['product_id'], $data['warehouse_id']) - $data['quantity']) - ($this->site->getBalanceQuantity_avg_cost($data['product_id'], $data['warehouse_id']));

					$this->db->insert('warehouses_products_avg_cost', ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id'], 'total_qty_prev' => $total_qty_prev, 'avg_cost' => $old_avg_cost, 'new_avg_cost' => $avg_cost, 'quantity' => $old_quantity]);
				}
            			$this->db->update('warehouses_products', ['avg_cost' => $avg_cost], ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']]);

			}





			if($wp_details->quantity < 0){
			$old_quantity=(0-$old_quantity);
					if($oversolds=$this->getOverSoldCostingSales($data['product_id'],$data['warehouse_id'])){
						foreach ($oversolds as $oversold) {
							if($old_quantity>0){
							$new_quantity=0;
							$new_item_cost_value=0;
							$new_avg_cost=0;
							if($oversold->quantity_overselling <= $old_quantity){
								$new_quantity=$oversold->quantity_overselling;

								$saleitems=$this->getSaleItemByID($oversold->id);

								$new_item_cost_value=$this->sma->formatDecimal($saleitems->item_cost_value+($new_quantity*$avg_cost),12);
								$new_avg_cost=$this->sma->formatDecimal(($new_item_cost_value/$oversold->quantity),12);

								$this->db->update('sale_items', ['overselling' => 0,'item_cost_value' => $new_item_cost_value,'avg_cost' => $new_avg_cost,'quantity_overselling' => 0], ['id' => $oversold->id]);



							}else{
								$new_quantity=$old_quantity;
								$saleitems=$this->getSaleItemByID($oversold->id);

								$new_item_cost_value=$this->sma->formatDecimal($saleitems->item_cost_value+($new_quantity*$avg_cost),12);
								$new_avg_cost=$this->sma->formatDecimal(($new_item_cost_value/($oversold->quantity- ($oversold->quantity_overselling - $new_quantity))),12);

								$this->db->update('sale_items', ['item_cost_value' => $new_item_cost_value,'avg_cost' => $new_avg_cost,'quantity_overselling' => ($oversold->quantity_overselling - $new_quantity)], ['id' => $oversold->id]);


							}



						$old_quantity=$old_quantity-$new_quantity;
						}
						}

					}

					if($oversolds=$this->getOverSoldCostingSalesCombo($data['product_id'],$data['warehouse_id'])){
						foreach ($oversolds as $oversold) {
							if($old_quantity>0){
							$new_quantity=0;
							$new_item_cost_value=0;
							$new_avg_cost=0;
							if($oversold->quantity_overselling <= $old_quantity){
								$new_quantity=$oversold->quantity_overselling;
								$new_quantityOS=$oversold->quantityOS+$new_quantity;
								$new_item_cost_valueOS=$this->sma->formatDecimal($oversold->item_cost_value+($new_quantity*$avg_cost),12);
								$saleitems=$this->getSaleItemByID($oversold->id);
								$combo_item = $this->site->getProductComboItems_id($oversold->product_id, $data['warehouse_id'],$data['product_id']);

								$new_item_cost_value=$this->sma->formatDecimal($saleitems->item_cost_value+($new_quantity*$avg_cost),12);
								//$new_avg_cost=$this->sma->formatDecimal((($new_item_cost_value/$combo_item->qty)/($oversold->quantity/$combo_item->qty)),12);
								//$new_avg_cost=$this->sma->formatDecimal((($new_item_cost_value/$combo_item->qty)/($oversold->quantity/$combo_item->qty)),12);
								$new_avg_cost=$this->sma->formatDecimal(($new_item_cost_value/$oversold->quantity),12);

								$this->db->update('sale_items', ['item_cost_value' => $new_item_cost_value,'avg_cost' => $new_avg_cost], ['id' => $oversold->id]);
								$this->db->update('combo_overselling', ['quantity_overselling' => 0, 'item_cost_value' => $new_item_cost_valueOS, 'quantity' => $new_quantityOS], ['product_id' => $oversold->product_id,'item_id' => $data['product_id'],'sale_id' => $oversold->sale_id]);



							}else{
								$new_quantity=$old_quantity;
								$new_quantityOS=$oversold->quantityOS+$new_quantity;
								$new_item_cost_valueOS=$this->sma->formatDecimal($oversold->item_cost_value+($new_quantity*$avg_cost),12);

								$saleitems=$this->getSaleItemByID($oversold->id);
								$combo_item = $this->site->getProductComboItems_id($oversold->product_id, $data['warehouse_id'],$data['product_id']);


								$new_item_cost_value=$this->sma->formatDecimal($saleitems->item_cost_value+($new_quantity*$avg_cost),12);
								//$new_avg_cost=$this->sma->formatDecimal((($new_item_cost_value/$combo_item->qty)/(($oversold->quantity- (($oversold->quantity_overselling - $new_quantity)/$combo_item->qty))/$combo_item->qty)),12);
								//$new_avg_cost=$this->sma->formatDecimal(($new_item_cost_value/($oversold->quantity- ($oversold->quantity_overselling - $new_quantity))),12);
								$new_avg_cost=$this->sma->formatDecimal(($new_item_cost_value/$oversold->quantity),12);

								$this->db->update('sale_items', ['item_cost_value' => $new_item_cost_value,'avg_cost' => $new_avg_cost], ['id' => $oversold->id]);
								$this->db->update('combo_overselling', ['quantity_overselling' => ($oversold->quantity_overselling - $new_quantity), 'item_cost_value' => $new_item_cost_valueOS, 'quantity' => $new_quantityOS], ['product_id' => $oversold->product_id,'item_id' => $data['product_id'],'sale_id' => $oversold->sale_id]);


							}



						$old_quantity=$old_quantity-$new_quantity;
						}
						}

					}
			}


        } else {
            $total_cost     = (($data['quantity'] * ($data['cost']+($data['shipping']/($data['quantity'])))));
             $avg_cost = $this->sma->formatDecimal(($total_cost / $data['quantity']),12);

			$this->db->insert('warehouses_products', ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id'], 'avg_cost' => $avg_cost, 'quantity' => 0]);
        }
    }

    public function updateExpense($id, $data = [])
    {
        if ($this->db->update('expenses', $data, ['id' => $id])) {


			if ($this->Settings->accounting){
				$entryDescription="expenses#".$id;
				$entry_type="expenses";


				$entryDebitAccount= [];

					$entryDebitAccount[] =  $data['account_id'];

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryCreditAccount=$this->site->getAccountsSettings()->expenses_shipping_acct;

					$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference'],$entry_type,null,null,null,null,$data['date'],$entryDescription);

				}


            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = [], $payment_type = null)
    {
        if ($this->db->update('payments', $data, ['id' => $id])) {
            $this->site->syncPurchasePayments($data['purchase_id']);


			if ($this->Settings->accounting){

				$entryDescription="Payments_Purchases_Invoice_No#".$data['purchase_id'];

				$purchase = $this->getPurchaseByID($data['purchase_id']);
				if ($purchase->receiving_status == 'pending' || $purchase->status == 'received' || $purchase->status == 'partial'){



					$entry_type="Purchases_Payments";
						/*if ($purchase->status == 'pending')
						$entry_type="purchase_pending_paid";
						if ($purchase->status == 'partial')
						$entry_type="purchase_partial";
						if ($purchase->status == 'received')
						$entry_type="purchase_receive";
					*/



						$entryDebitAccount= [];
						$entryDebitBalance= [];

						if ($purchase->receiving_status == 'pending'){
						//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryDebitAccount[] =  $entryDebitAcc;
						 $entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']);

						 $entryCreditAccount= [];
						//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryCreditAcc=="")
							$entryCreditAcc=$payment_type;
							 $entryCreditAccount[] =  $entryCreditAcc;



						/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}*/


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);
						/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						$entryCreditBalance[] =  $this->sma->formatDecimal($purchase->grand_total - $data['amount']);

						}*/


						}else{
						if ($purchase->status == 'received' || $purchase->status == 'partial'){
						//$entryDebitAccount[] =  $this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryDebitAccount[] =  $entryDebitAcc;
						//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
						//$amount=$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
						//$entryDebitBalance[] =  $amount;
						$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']);

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
						//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryCreditAcc=="")
							$entryCreditAcc=$payment_type;
							 $entryCreditAccount[] =  $entryCreditAcc;

						/*
						if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}
						*/


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);
						//if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						//$entryCreditBalance[] =  $this->sma->formatDecimal($purchase->grand_total - $data['amount']);

						//}

						}
						//if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total))
						//	$amount=$this->sma->formatDecimal($data['amount']);





						}







						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);








				}




				if ($purchase->status == 'returned'){



					$entry_type="Purchases_Payments";

					if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryDebitAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryDebitAcc=="")
							$entryDebitAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryDebitAccount[] =  $entryDebitAcc;
						//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
						//$amount=$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
						//$entryDebitBalance[] =  $amount;
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($data['amount']));

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
						//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
						$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
							if($entryCreditAcc=="")
							$entryCreditAcc=$payment_type;
							 $entryCreditAccount[] =  $entryCreditAcc;

						/*
						if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total)){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}
						*/


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['amount'])+$commisson_amount);

						/*$entryDebitAccount= [];

						$entryWarehouseAcc=$this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
							if($entryWarehouseAcc=="")
							$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

						$entryDebitAccount[] =  $entryWarehouseAcc;
						//$entryDebitAccount[] =  $this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
						$amount=$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
						//if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($purchase->grand_total))
						//	$amount=$this->sma->formatDecimal($data['amount']);

						$entryDebitBalance= [];
						if(abs($purchase->shipping)>0){
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($purchase->grand_total)-abs($purchase->total_tax)+abs($purchase->shipping));
						}else{
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($purchase->grand_total)-abs($purchase->total_tax));
						}
						$entryDebitBalance[] =  $this->sma->formatDecimal(abs($purchase->total_tax));

						$commisson_amount=0;

						$entryCreditAccount= [];
						$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;



						if($this->sma->formatDecimal(abs($data['amount']))!=$this->sma->formatDecimal(abs($purchase->grand_total))){
						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
							$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
						}else{
						$entryCreditAcc=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
							if($entryCreditAcc=="")
							$entryCreditAcc=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;
						}
						 $entryCreditAccount[] =  $entryCreditAcc;
						}


						$entryCreditBalance= [];
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['amount'])+$commisson_amount);
						if($this->sma->formatDecimal(abs($data['amount']))!=$this->sma->formatDecimal(abs($purchase->grand_total))){
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($purchase->grand_total) - abs($data['amount']));

						}

						if(abs($purchase->shipping)>0){
						$entryCreditAccount[] = $this->site->getAccountsSettings()->accounts_shipping_return_losses;
							$entryCreditBalance[] =  $this->sma->formatDecimal(abs($purchase->shipping));

						}*/





						$this->site->updateEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);








				}

				}


            return true;
        }
        return false;
    }

    public function updatePaymentApplication($id, $data = [])
    {
        if ($this->db->update('payment_application', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateExpensePayment($id, $data = [])
    {
        if ($this->db->update('expense_payments', $data, ['id' => $id])) {
            $this->site->syncExpensePayments($data['expense_id']);
            return true;
        }
        return false;
    }

    public function updatePurchase($id, $data, $items = [],$general_total_net_unit_cost_quantity =null,$general_total_net_unit_cost_quantity_discont=null)
    {

		$this->db->trans_off();
		$this->db->trans_start();
        $purchase = $this->getPurchaseByID($id);
        $oitems    = $this->getAllPurchaseItems($id);
		$shipping_received=$data['shipping_received'];
		if($purchase->status == 'received' || $purchase->status == 'partial'){
        $data['shipping_received']=$data['shipping_received']+$purchase->shipping_received;
		$data['total']=$data['total']+$purchase->total;
        $data['product_discount']=$data['product_discount']+$purchase->product_discount;
        $data['total_discount']=$data['total_discount']+$purchase->total_discount;
        $data['product_tax']=$data['product_tax']+$purchase->product_tax;
        $data['order_tax']=$data['order_tax']+$purchase->order_tax;
        $data['total_tax']=$data['total_tax']+$purchase->total_tax;
        $data['grand_total']=$data['grand_total']+$purchase->grand_total;
		}
        if ($this->db->update('purchases', $data, ['id' => $id])){// && $this->db->delete('purchase_items', ['purchase_id' => $id])) {

			if($data['status'] == 'ordered'  || $data['status'] == 'pending'){
				$this->db->delete('purchase_items', ['purchase_id' => $id]);
			}
           //$general_total_net_unit_cost_quantity=-1*($general_total_net_unit_cost_quantity);

			/*if ($purchase->status == 'received' || $purchase->status == 'partial') {
                foreach ($oitems as $oitem) {


				if ($purchase->order_discount>0 || $purchase->order_discount<0) {
				$shipping_amount = ($purchase->shipping ? $purchase->shipping :0) + ($purchase->customs_fees ? $purchase->customs_fees :0) + ($purchase->Other_fees ? $purchase->Other_fees :0);
				$original_shipping=($oitem->real_unit_cost * (0 - $oitem->quantity_received));
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				}else{
				$shipping_amount = ($purchase->shipping ? $purchase->shipping :0) + ($purchase->customs_fees ? $purchase->customs_fees :0) + ($purchase->Other_fees ? $purchase->Other_fees :0);
				$original_shipping=($oitem->net_unit_cost * (0 - $oitem->quantity_received));
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				//$shipping = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);

				}
                    $this->updateAVCO(['product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => (0 - $oitem->quantity_received), 'cost' => $oitem->net_unit_cost,'shipping'=>$shipping,0,0,'status'=>$purchase->status]);
                    //$this->updateAVCO(['product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => (0 - $oitem->quantity), 'cost' => $oitem->real_unit_cost]);
                }


            }
			$this->site->syncQuantity(null, null, $oitems);
            */
			//$general_total_net_unit_cost_quantity=-1*($general_total_net_unit_cost_quantity);

			$purchase_id = $id;
            foreach ($items as $item) {
                $item['purchase_id'] = $id;
                $item['option_id']   = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : null;


				if ($data['status'] == 'received' || $data['status'] == 'partial') {

					$this->db->update('cars_vehicles', ['active' => 1], ['vehicle_id' => $item['product_id']]);

					$unit     = $this->site->getUnitByID($item['product_unit_id']);
					if($unit->operator=='*'){
					$unit_quantity=$this->sma->formatDecimal($item['quantity_received']/$unit->operation_value);
					}else if($unit->operator=='/'){
					$unit_quantity=$this->sma->formatDecimal($item['quantity_received']*$unit->operation_value);
					}else{
					$unit_quantity=	$item['quantity_received'];
					}

				if($unit_quantity < $item['quantity_received']){

						$item['net_unit_cost']=$this->sma->formatDecimal(($item['net_unit_cost']/$item['quantity_received'])* $unit_quantity,12);
						$item['real_unit_cost']=$this->sma->formatDecimal(($item['real_unit_cost']/$item['quantity_received'])* $unit_quantity,12);
				}else if($unit_quantity > $item['quantity_received']){

					//$item['real_unit_cost']==$item['net_unit_cost'];
						$item['net_unit_cost']=$this->sma->formatDecimal($item['net_unit_cost']*($unit_quantity/ $item['quantity_received']),12);
						$item['real_unit_cost']=$this->sma->formatDecimal($item['real_unit_cost']*($unit_quantity/ $item['quantity_received']),12);


				}
				}



				if ($data['order_discount']>0 || $data['order_discount']<0) {
				$original=($item['real_unit_cost'] * $item['quantity_received']);
				$distribute_value = $this->site->distributeAmountEqually($original,$data['order_discount'],$general_total_net_unit_cost_quantity_discont);
					$item['net_unit_cost']=$this->sma->formatDecimal((($original-$distribute_value)/$item['quantity_received']),12);
				}



				$oitems    = $this->site->getAllPurchaseItemsItem($id,$item['product_id']);

				if ($data['status'] == 'received' || $data['status'] == 'partial') {
				$shipping_data=0;

				if ($purchase->status != 'received' || $purchase->status != 'partial') {
				//$oitems_date    = $this->site->getAllPurchaseItemsItem($id,$item['product_id']);



					if ($data['order_discount']>0 || $data['order_discount']<0) {
				$shipping_amount_data = $shipping_received + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				$original_shipping_data=($item['real_unit_cost'] * $item['quantity_received']);
				$shipping_data =$this->site->distributeAmountEqually($original_shipping_data,$shipping_amount_data,$general_total_net_unit_cost_quantity);
				}else{
				$shipping_amount_data = $shipping_received  + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				$original_shipping_data=($item['net_unit_cost'] * $item['quantity_received']);
				$shipping_data =$this->site->distributeAmountEqually($original_shipping_data,$shipping_amount_data,$general_total_net_unit_cost_quantity);
				//$shipping = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);

				}
				//$quantity_received_data=($item['quantity_received']-$oitems_date->quantity_received);

				$item['shipping']=$shipping_data+$oitems->shipping;

				}
				$quantity_received=($item['quantity_received']+$oitems->quantity_received);
				$item['item_cost_value']=$this->sma->formatDecimal(($item['net_unit_cost'] * $quantity_received) + $item['shipping'],12);
				}


				//$item['quantity']=$item['quantity']-$purchase->quantity_received;
				if ($data['status'] == 'received' || $data['status'] == 'partial') {
					if($purchase->status != 'pending' && $purchase->status != 'ordered' ){
				$item['quantity_received']=($item['quantity_received']+$oitems->quantity_received);
				$item['quantity_balance']=($item['quantity_balance']+$oitems->quantity_balance);
				$quantity_received=($item['quantity_received']-$oitems->quantity_received);
				}
				}

				//$item['shipping_received']=($item['shipping_received']+$oitems->shipping_received);
				if($data['status'] == 'ordered' || $data['status'] == 'pending'){
				//$this->db->delete('purchase_items', ['purchase_id' => $id]);
				$this->db->insert('purchase_items', $item);

				}else
					$this->db->update('purchase_items', $item, ['purchase_id' => $id,'product_id'=> $item['product_id']]);
                if ($data['status'] == 'received' || $data['status'] == 'partial') {
					//if ($purchase->status != 'received' || $purchase->status != 'partial') {


					if ($data['order_discount']>0 || $data['order_discount']<0) {
				$shipping_amount = $shipping_received + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				$original_shipping=($item['real_unit_cost'] * $quantity_received);
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				}else{
				$shipping_amount = $shipping_received + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
				$original_shipping=($item['net_unit_cost'] * $quantity_received);
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$general_total_net_unit_cost_quantity);
				//$shipping = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);

				}
					//}
					//if ($purchase->status == 'received' || $purchase->status == 'partial')
					//	$quantity_received=($item['quantity_received']-$oitems->quantity_received);
					//else
						//$quantity_received=($item['quantity_received']);
				$net_unit_cost=$item['net_unit_cost'];
				if(isset($data['currency_rate']) && $data['currency_rate']!=1){
					$net_unit_cost = $net_unit_cost * $data['currency_rate'];
					$shipping = $shipping * $data['currency_rate'];
				}


                  $this->updateAVCO(['product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $quantity_received, 'cost' => $net_unit_cost,'shipping'=>$shipping,0,0,'status'=>$data['status']]);
                //$this->updateAVCO(['product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['quantity_received'], 'cost' => $item['real_unit_cost']]);
                }
            }
			if ($data['status'] == 'received' || $data['status'] == 'partial') {
            $this->site->syncQuantity(null, $purchase_id);
            $this->site->syncPurchasePayments($id);


			}






			if ($this->Settings->accounting){

				$entryDescription="Purchase_Invoice_No#".$purchase_id;

				if(isset($data['currency_rate']) && $data['currency_rate']!=1){
					$data['grand_total'] = $data['grand_total'] * $data['currency_rate'];
					$data['total_tax'] = $data['total_tax'] * $data['currency_rate'];
					$data['shipping'] = $data['shipping'] * $data['currency_rate'];


				}

				if($data['receiving_status'] == 'pending'){

					$entry_type='Purchase_invoice_pendding';
					//if($this->site->getEntryCount($data['reference_no'],'purchase_pending')){

				    if($this->site->getCompanyByID($data['supplier_id'])->co_type==1){
					$entryDebitAccount= [];

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryCreditAccount=$this->site->getAccountByRefId($data['supplier_id'],'local_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;

					if($purchase->status != 'ordered' && $purchase->status != 'pending'){
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type,null,null,null,null,$data['date']);
					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);

					}

				}

				if($this->site->getCompanyByID($data['supplier_id'])->co_type==2){
					$entryDebitAccount= [];

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryCreditAccount=$this->site->getAccountByRefId($data['supplier_id'],'international_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;

					//$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type);
					if($purchase->status != 'ordered' && $purchase->status != 'pending'){
						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type,null,null,null,null,$data['date']);
					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);

					}

				}

					//}


				}else if ($data['status'] == 'partial' || $data['status'] == 'received'){



				   $entry_type="";
					if ($data['status'] == 'partial')
					$entry_type="purchase_partial";
					if ($data['status'] == 'received')
					$entry_type="purchase_receive";

				if($purchase->status != 'ordered' && $purchase->status != 'pending'){
						$entryDebitAccount= [];
					//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
					//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					//$entryDebitBalance= [];
					//$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					//$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

						//if($purchase->payment_status == 'paid'){
						if($this->Settings->accounting_type=="periodic"){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{

						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}

					$entryDebitAccount[]=$entryWarehouseAcc;
					//$entryDebitAccount=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
					$entryCreditAccount=  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;


					$costCenterDebitId= [];
					$costCenterDebitAmount= [];
					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryDebitAccount as $key => $acc){
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


					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']-$data['total_tax']),$this->sma->formatDecimal($data['grand_total']-$data['total_tax']),$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);

					}else{

						if($this->site->getCompanyByID($data['supplier_id'])->co_type==1){
					$entryDebitAccount= [];

					if ($data['receiving_status'] == 'pending'){
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
					}else if ($data['status'] == 'received'){
						if($this->Settings->accounting_type=="periodic"){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{
							$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}
					$entryDebitAccount[] =  $entryWarehouseAcc;
					//$entryDebitAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
					}

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryCreditAccount=$this->site->getAccountByRefId($data['supplier_id'],'local_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;



					$costCenterDebitId= [];
					$costCenterDebitAmount= [];

					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryDebitAccount as $key => $acc){
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


					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);

				}

				if($this->site->getCompanyByID($data['supplier_id'])->co_type==2){
					$entryDebitAccount= [];

					if ($data['receiving_status'] == 'pending'){
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
					}else if ($data['status'] == 'received'){
						if($this->Settings->accounting_type=="periodic"){
							$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{
							$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}

					$entryDebitAccount[] =  $entryWarehouseAcc;
					//$entryDebitAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
					}

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

					$entryCreditAccount=$this->site->getAccountByRefId($data['supplier_id'],'international_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;

					$costCenterDebitId= [];
					$costCenterDebitAmount= [];
					if($data['costCenterId']){
						$costDebitCount=0;
							foreach($entryDebitAccount as $key => $acc){
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


					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$data['reference_no'],$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$data['date'],$entryDescription);

				}
					}







			}

			}







        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Update:Purchases_model.php)');
        } else {
            return true;
        }

        return false;
    }

    public function updateStatus($id, $status, $note,$receiving_status)
    {
        $this->db->trans_off();
		$this->db->trans_start();
        $purchase = $this->getPurchaseByID($id);
        $items    = $this->site->getAllPurchaseItems($id);
        if ($this->db->update('purchases', ['status' => $status, 'note' => $note, 'receiving_status' => $receiving_status], ['id' => $id])) {
            if (($purchase->status != 'received' || $purchase->status != 'partial') && ($status == 'received' || $status == 'partial')) {
                foreach ($items as $item) {
					$this->db->update('cars_vehicles', ['active' => 1], ['vehicle_id' => $item->product_id]);

                    $qb = $status == 'received' ? ($item->quantity_balance + ($item->quantity - $item->quantity_received)) : $item->quantity_balance;
                    $qr = $status == 'received' ? $item->quantity : $item->quantity_received;
                    $this->db->update('purchase_items', ['status' => $status, 'quantity_balance' => $qb, 'quantity_received' => $qr], ['id' => $item->id]);
                    $this->updateAVCO(['product_id' => $item->product_id, 'warehouse_id' => $item->warehouse_id, 'quantity' => $item->quantity, 'cost' => $item->net_unit_cost, 'shipping' => $item->shipping,0,0,'status' => $status]);
                }
                $this->site->syncQuantity(null, null, $items);
            } elseif (($purchase->status == 'received' || $purchase->status == 'partial') && ($status == 'ordered' || $status == 'pending')) {
                foreach ($items as $item) {
                    $qb = 0;
                    $qr = 0;
                    $this->db->update('purchase_items', ['status' => $status, 'quantity_balance' => $qb, 'quantity_received' => $qr], ['id' => $item->id]);
                    $this->updateAVCO(['product_id' => $item->product_id, 'warehouse_id' => $item->warehouse_id, 'quantity' => $item->quantity, 'cost' => $item->net_unit_cost, 'shipping' => $item->shipping,0,0,'status' => 'returned']);
                }
				//$this->updateAVCO(['product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['quantity'], 'cost' => $net_unit_cost,'shipping'=>$shipping,0,0,'status'=>$data['status']]);

                $this->site->syncQuantity(null, null, $items);
            }



			if ($this->Settings->accounting){

				$entryDescription="Purchase_Invoice_No#".$id;

				if(isset($purchase->currency_rate) && $purchase->currency_rate!=1){
					$purchase->grand_total = $purchase->grand_total * $purchase->currency_rate;
					$purchase->total_tax = $purchase->total_tax * $purchase->currency_rate;
					$purchase->shipping = $purchase->shipping * $purchase->currency_rate;


				}

				if ($status == 'partial' || $status == 'received'){
				if($receiving_status == 'pending'){

					$entry_type='Purchase_invoice_pendding';
					//if($this->site->getEntryCount($data['reference_no'],'purchase_pending')){

				    if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
					$entryDebitAccount= [];

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
					$entryDebitBalance[] =  $this->sma->formatDecimal($purchase->total_tax);

					$entryCreditAccount=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;

					if($purchase->status != 'ordered' && $purchase->status != 'pending'){
						$this->site->updateEntryMultiSideWithDelete($purchase->reference_no,$entryDescription);
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($purchase->grand_total),$purchase->reference_no,$entry_type,null,null,null,null,$purchase->date,$entryDescription);
					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($purchase->grand_total),$purchase->reference_no,$entry_type,null,null,null,null,$purchase->date,$entryDescription);

					}

				}

				if($this->site->getCompanyByID($purchase->supplier_id)->co_type==2){
					$entryDebitAccount= [];

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
					$entryDebitBalance[] =  $this->sma->formatDecimal($purchase->total_tax);

					$entryCreditAccount=$this->site->getAccountByRefId($purchase->supplier_id,'international_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;

					if($purchase->status != 'ordered' && $purchase->status != 'pending'){
						$this->site->updateEntryMultiSideWithDelete($purchase->reference_no,$entryDescription);
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($purchase->grand_total),$purchase->reference_no,$entry_type,null,null,null,null,$purchase->date,$entryDescription);
					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($purchase->grand_total),$purchase->reference_no,$entry_type,null,null,null,null,$purchase->date,$entryDescription);

					}

				}

					//}


				}else{

				   $entry_type="";
					if ($status == 'partial')
					$entry_type="purchase_partial";
					if ($status == 'received')
					$entry_type="purchase_receive";


					/*	if($this->Settings->accounting_type=="periodic"){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{

						$entryWarehouseAcc=$this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}

					$entryDebitAccount=$entryWarehouseAcc;
					//$entryDebitAccount=$this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
					$entryCreditAccount=  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;


					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax),$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax),$purchase->reference_no,$entry_type,null,null,null,null,$purchase->date,$entryDescription);
					*/
					if($purchase->status != 'ordered' && $purchase->status != 'pending'){
						$entryDebitAccount= [];
					//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
					//$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					//$entryDebitBalance= [];
					//$entryDebitBalance[] =  $this->sma->formatDecimal($data['grand_total']-$data['total_tax']);
					//$entryDebitBalance[] =  $this->sma->formatDecimal($data['total_tax']);

						//if($purchase->payment_status == 'paid'){
						if($this->Settings->accounting_type=="periodic"){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{

						$entryWarehouseAcc=$this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}

					$entryDebitAccount[]=$entryWarehouseAcc;
					$entryCreditAccount=  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;


					$costCenterDebitId= [];
					$costCenterDebitAmount= [];



					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax),$this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax),$purchase->reference_no,$entry_type,null,null,null,null,$purchase->date,$entryDescription);

					}else{

						if($this->site->getCompanyByID($purchase->supplier_id)->co_type==1){
					$entryDebitAccount= [];

					if ($receiving_status == 'pending'){
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
					}else if ($status == 'received'){
						if($this->Settings->accounting_type=="periodic"){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{
							$entryWarehouseAcc=$this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}
					$entryDebitAccount[] =  $entryWarehouseAcc;
					//$entryDebitAccount[] =  $this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
					}

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
					$entryDebitBalance[] =  $this->sma->formatDecimal($purchase->total_tax);

					$entryCreditAccount=$this->site->getAccountByRefId($purchase->supplier_id,'local_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;



					$costCenterDebitId= [];
					$costCenterDebitAmount= [];




					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($purchase->grand_total),$purchase->reference_no,$entry_type,null,null,null,null,$purchase->date,$entryDescription);

				}

				if($this->site->getCompanyByID($purchase->supplier_id)->co_type==2){
					$entryDebitAccount= [];

					if ($receiving_status == 'pending'){
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_store_goods_by_road_acct;
					}else if ($status == 'received'){
						if($this->Settings->accounting_type=="periodic"){
							$entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
							}else{
							$entryWarehouseAcc=$this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc==""){
								$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
							}
						}

					$entryDebitAccount[] =  $entryWarehouseAcc;
					}

					$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

					$entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($purchase->grand_total-$purchase->total_tax);
					$entryDebitBalance[] =  $this->sma->formatDecimal($purchase->total_tax);

					$entryCreditAccount=$this->site->getAccountByRefId($supplier_id,'international_suppliers')->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_international_suppliers_acct;

					$costCenterDebitId= [];
					$costCenterDebitAmount= [];



					$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($purchase->grand_total),$purchase->reference_no,$entry_type,null,null,null,null,$purchase->date,$entryDescription);

				}
					}







			}
			}else{
						$this->site->updateEntryMultiSideWithDelete($purchase->reference_no,$entryDescription);

			}

			}
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (UpdateStatus:Purchases_model.php)');
        } else {
            return true;
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

	    public function getSumSaleItemsCost($warehouse_id, $product_id)
    {
        //$this->db->select_sum('item_cost_value');
		$this->db->select('SUM(quantity) as quantity')->select('SUM(item_cost_value) as item_cost_value');//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')

        $this->db->where('warehouse_id =', $warehouse_id);
        $this->db->where('product_id =', $product_id);
        $q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getSumSaleItemsCostComboAVG($warehouse_id, $product_id)
    {


		$this->db->select('combo_avg_cost.quantity as quantity,combo_avg_cost.item_cost_value as item_cost_value');
		$this->db->join('combo_avg_cost', 'combo_avg_cost.sale_id=sale_items.sale_id and combo_avg_cost.product_id=sale_items.product_id', 'left');

        $this->db->where('sale_items.product_type =', 'combo');
		$this->db->where('sale_items.warehouse_id =', $warehouse_id);
        $this->db->where('combo_avg_cost.item_id =', $product_id);
		$q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		 }
        return false;

    }

    public function getSumSaleItemsCostComboOS($warehouse_id,$product_id)
    {
        $this->db->select('combo_overselling.quantity as quantity,combo_overselling.item_cost_value as item_cost_value');
		$this->db->join('combo_overselling', 'combo_overselling.sale_id=sale_items.sale_id and combo_overselling.product_id=sale_items.product_id', 'left');

        $this->db->where('sale_items.product_type =', 'combo');
		$this->db->where('sale_items.warehouse_id =', $warehouse_id);
        $this->db->where('combo_overselling.item_id =', $product_id);
		$q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		 }
        return false;

    }


    public function cancelPurchase($id)
    {
            if ($this->db->update('purchases', ['status' => 'canceled'], ['id' => $id])) {
                return true;
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

    public function getInvoicePayments($purchase_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', ['purchase_id' => $purchase_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
	public function getPurchaseByReference($ref)
    {
        $this->db->where('reference_no', $ref);
        $q = $this->db->get('purchases', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }




}
