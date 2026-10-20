<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pos2_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
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

    public function addPayment($payment = [], $customer_id = null)
    {
        if (isset($payment['sale_id']) && isset($payment['paid_by']) && isset($payment['amount'])) {
            $payment['pos_paid'] = $payment['amount'];
            $inv                 = $this->getInvoiceByID($payment['sale_id']);
            $paid                = $inv->paid + $payment['amount'];
            if ($payment['paid_by'] == 'ppp') {
                $card_info = ['number' => $payment['cc_no'], 'exp_month' => $payment['cc_month'], 'exp_year' => $payment['cc_year'], 'cvc' => $payment['cc_cvv2'], 'type' => $payment['cc_type']];
                $result    = $this->paypal($payment['amount'], $card_info, '', $payment['sale_id']);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['date']           = $this->sma->fld($result['created_at']);
                    $payment['amount']         = $result['amount'];
                    $payment['currency']       = $result['currency'];
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
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
            } elseif ($payment['paid_by'] == 'stripe') {
                $card_info = ['number' => $payment['cc_no'], 'exp_month' => $payment['cc_month'], 'exp_year' => $payment['cc_year'], 'cvc' => $payment['cc_cvv2'], 'type' => $payment['cc_type']];
                $result    = $this->stripe($payment['amount'], $card_info);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['date']           = $this->sma->fld($result['created_at']);
                    $payment['amount']         = $result['amount'];
                    $payment['currency']       = $result['currency'];
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
                } else {
                    $msg[] = lang('payment_failed');
                    $msg[] = '<p class="text-danger">' . $result['code'] . ': ' . $result['message'] . '</p>';
                }
            } elseif ($payment['paid_by'] == 'authorize') {
                $authorize_arr                 = ['x_card_num' => $payment['cc_no'], 'x_exp_date' => ($payment['cc_month'] . '/' . $payment['cc_year']), 'x_card_code' => $payment['cc_cvv2'], 'x_amount' => $payment['amount'], 'x_invoice_num' => $inv->id, 'x_description' => 'Sale Ref ' . $inv->reference_no . ' and Payment Ref ' . $payment['reference_no']];
                list($first_name, $last_name)  = explode(' ', $payment['cc_holder'], 2);
                $authorize_arr['x_first_name'] = $first_name;
                $authorize_arr['x_last_name']  = $last_name;
                $result                        = $this->authorize($authorize_arr);
                if (!isset($result['error'])) {
                    $payment['transaction_id'] = $result['transaction_id'];
                    $payment['approval_code']  = $result['approval_code'];
                    $payment['date']           = $this->sma->fld($result['created_at']);
                    unset($payment['cc_cvv2']);
                    $this->db->insert('payments', $payment);
                    $paid += $payment['amount'];
                } else {
                    $msg[] = lang('payment_failed');
                    $msg[] = '<p class="text-danger">' . $result['msg'] . '</p>';
                }
            } else {
                if ($payment['paid_by'] == 'gift_card') {
                    if($payment['cc_no']!="" && $payment['cc_no']){
                        $gc = $this->getGiftCardByNO($payment['cc_no']);
                        $this->db->update('gift_cards', ['balance' => ($gc->balance - $payment['amount'])], ['card_no' => $payment['cc_no']]);
                    }else{
                        $sale_org = $this->getSaleByID($payment['sale_id']);
                        $this->update_award_pointsV2($payment['amount'], $sale_org->customer_id, $sale_org->created_by, true);
                    }
                } elseif ($customer_id && $payment['paid_by'] == 'deposit') {
                    $customer = $this->site->getCompanyByID($customer_id);
                    $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $payment['amount'])], ['id' => $customer_id]);
                }
                unset($payment['cc_cvv2']);
                $this->db->insert('payments', $payment);
                $paid += $payment['amount'];
            }
            if (!isset($msg)) {
                if ($this->getReference('pay',$data['brId'] ? $data['brId']  : null) == $data['reference_no']) {
                    $this->updateReference('pay');
                }
                $this->syncSalePayments($payment['sale_id']);
                return ['status' => 1, 'msg' => ''];
            }
            return ['status' => 0, 'msg' => $msg];
        }
        return false;
    }

    public function addPrinter($data = [])
    {
        if ($this->db->insert('printers', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function addQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($warehouse_quantity = $this->getProductQuantity($product_id, $warehouse_id)) {
            $new_quantity = $warehouse_quantity['quantity'] - $quantity;
            if ($this->updateQuantity($product_id, $warehouse_id, $new_quantity)) {
                $this->syncProductQty($product_id, $warehouse_id);
                return true;
            }
        } else {
            if ($this->insertQuantity($product_id, $warehouse_id, -$quantity)) {
                $this->syncProductQty($product_id, $warehouse_id);
                return true;
            }
        }
        return false;
    }

	public function addSale2($data)
    {
		if ($this->db->insert('temp2', ['text' => $data])) {
            return true;
        }
        return false;
	}

	public function addSaleTailor($data)
    {
		if ($this->db->insert('TR_Measurement', $data)) {
            return true;
        }
        return false;
	}

    public function updateSaleTailor($sale_id,$data)
    {
		if($this->db->delete('TR_Measurement', ['sale_id' => $sale_id])){
		if ($this->db->insert('TR_Measurement', $data)) {
            return true;
        }
		}
        return false;
	}

    public function updateSaleItemTailor($sale,$data)
    {

		if ($this->db->update('sale_items', $data,$sale)) {
            return true;
        }

        return false;
	}

    public function addSale($data = [], $items = [], $payments = [], $sid = null, $si_return = [])
    {

        if (empty($si_return)) {

            $cost = $this->costing($items);
            // $this->sma->print_arrays($cost);
        }




		$this->db->trans_off();
		$this->db->trans_start();
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();



			foreach ($items as $item) {
				if ($item['product_type'] == 'combo') {
                		$item_cost_value=0;
						$avg_cost=0;
				        $combo_items_qty = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                        /*$total_qty=0;
						foreach ($combo_items_qty as $combo_item) {
						    $total_qty=$total_qty+$combo_item->qty;
                        }
						*/


						$combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
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
                $item['sale_id'] = $sale_id;
                if (!empty($item['modifiers']) && isset($item['modifiers']) && $item['modifiers']) {
                    $modifiers=  $item['modifiers'];

                    unset($item['modifiers']);
                }
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && empty($si_return)) {
                    $item_costs = $this->item_costing($item);
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
                if (!empty($modifiers)) {
                    foreach ($modifiers as $modifier) {
                        if ($this->db->insert('sale_item_modifiers', ['sale_item_id' => $sale_item_id, 'modifier_id' => $modifier->modifier_id, 'price' => $modifier->price, 'quantity' => $modifier->quantity])) {
                        }
                    }
                    unset($modifiers);
                }
            }

            /*foreach ($items as $item) {
                if ($item['product_type'] == 'combo') {
                			$item_cost_value=0;
				        $combo_items = $this->site->getProductComboItems($item['product_id'], $item['warehouse_id']);
                        foreach ($combo_items as $combo_item) {
							$quantity=$item['quantity'] * $combo_item->qty;
                            $wp=$this->getavg_costProduct($combo_item->id,$item['warehouse_id']);
				if($wp){
					if($quantity > $wp->quantity){
						if($wp->quantity > 0)
							$item_cost_value=$item_cost_value+($wp->quantity * $wp->avg_cost);
						else
							$item_cost_value=$item_cost_value;

					}else
						$item_cost_value=$item_cost_value+($quantity * $wp->avg_cost);
				}

						}
						$item['item_cost_value']=$item_cost_value;

                }else{
				$wp=$this->getavg_costProduct($item['product_id'],$item['warehouse_id']);
				if($wp){
					if($item['quantity'] > $wp->quantity){
						if($wp->quantity > 0)
							$item['item_cost_value']=$wp->quantity * $wp->avg_cost;
						else
							$item['item_cost_value']=0;

					}else
						$item['item_cost_value']=$item['quantity'] * $wp->avg_cost;
				}
				}
                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {
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
            }*/

            if ($data['sale_status'] == 'completed') {
                $this->syncPurchaseItems($cost);
            }



			if (!empty($si_return)) {
                foreach ($si_return as $return_item) {
                    $product = $this->getProductByID($return_item['product_id']);

					//$clause = ['product_id' => $return_item['product_id'], 'warehouse_id' => $return_item['warehouse_id'], 'purchase_id' => null, 'transfer_id' => null, 'option_id' => null];
					//$pi = $this->site->getPurchasedItem($clause);




                    if ($product->type == 'combo') {

                        $combo_items = $this->getProductComboItems($return_item['product_id'], $return_item['warehouse_id']);
                        foreach ($combo_items as $combo_item) {
                        $wp_details = $this->getWarehouseProductQuantity($return_item['warehouse_id'], $combo_item->id);
					    $this->UpdateCostingAndPurchaseItem($return_item, $combo_item->id, ($return_item['quantity'] * $combo_item->qty));

							$sale_item=$this->getSaleItemByid_sale_id($return_item['sale_id'],$combo_item->id,$return_item['id']);
					//$this->sma->print_arrays($sale_item);

					if($sale_item->item_cost_value!=0)
					//if ($pi->quantity_balance > 0)
					{

					//if($sale_item->item_cost_value>0)


					$this->syncPurchaseItems_returnSales($combo_item->id, $return_item['warehouse_id'],(0-$sale_item->item_cost_value));


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


					$this->syncPurchaseItems_returnSales($return_item['product_id'], $return_item['warehouse_id'],(0-$sale_item->item_cost_value));


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
            
            if ($data['credit_id']) {
                $this->db->update('sales', ['return_credit_id' => $sale_id], ['id' => $data['credit_id']]);
            }



            $this->syncQuantity($sale_id);
            if ($sid) {
                $this->deleteBill($sid);
            }






            //$this->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);

            if($data['salesman_id'] && $data['commission_type']==2){


                $commission_amount=$this->sma->formatDecimal(((($data['total'] - $data['total_discount'])) - ($this->getTotalItemCostValue($sale_id)->item_cost_value)) * $data['commission'] / 100,4);

                $this->db->update('sales', ['commission_amount' => $commission_amount], ['id' => $sale_id]);


                }
                $this->db->update('sales', [ 'sale_item_cost_value' => $this->getTotalItemCostValue($sale_id)->item_cost_value], ['id' => $sale_id]);

            $this->updateReference('pos',$data['brId'] ? $data['brId']  : null);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Add:Pos_model.php)');
        } else {
            $msg = [];
            $award_points_flag=1;
            if (!empty($payments)) {
                $paid = 0;
                foreach ($payments as $payment) {
                    if (!empty($payment) && isset($payment['amount']) && $payment['amount'] != 0) {
                        $payment['sale_id']      = $sale_id;
                        $payment['reference_no'] = $this->getReference('pay',$payment['brId'] ? $payment['brId']  : null);
                        if ($payment['paid_by'] == 'ppp') {
                            $card_info = ['number' => $payment['cc_no'], 'exp_month' => $payment['cc_month'], 'exp_year' => $payment['cc_year'], 'cvc' => $payment['cc_cvv2'], 'type' => $payment['cc_type']];
                            $result    = $this->paypal($payment['amount'], $card_info, '', $sale_id);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['date']           = $this->sma->fld($result['created_at']);
                                $payment['amount']         = $result['amount'];
                                $payment['currency']       = $result['currency'];
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
                                $paid += $payment['amount'];
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
                        } elseif ($payment['paid_by'] == 'stripe') {
                            $card_info = ['number' => $payment['cc_no'], 'exp_month' => $payment['cc_month'], 'exp_year' => $payment['cc_year'], 'cvc' => $payment['cc_cvv2'], 'type' => $payment['cc_type']];
                            $result    = $this->stripe($payment['amount'], $card_info);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['date']           = $this->sma->fld($result['created_at']);
                                $payment['amount']         = $result['amount'];
                                $payment['currency']       = $result['currency'];
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['code'] . ': ' . $result['message'] . '</p>';
                            }
                        } elseif ($payment['paid_by'] == 'authorize') {
                            $authorize_arr                 = ['x_card_num' => $payment['cc_no'], 'x_exp_date' => ($payment['cc_month'] . '/' . $payment['cc_year']), 'x_card_code' => $payment['cc_cvv2'], 'x_amount' => $payment['amount'], 'x_invoice_num' => $sale_id, 'x_description' => 'Sale Ref ' . $data['reference_no'] . ' and Payment Ref ' . $payment['reference_no']];
                            list($first_name, $last_name)  = explode(' ', $payment['cc_holder'], 2);
                            $authorize_arr['x_first_name'] = $first_name;
                            $authorize_arr['x_last_name']  = $last_name;
                            $result                        = $this->authorize($authorize_arr);
                            if (!isset($result['error'])) {
                                $payment['transaction_id'] = $result['transaction_id'];
                                $payment['approval_code']  = $result['approval_code'];
                                $payment['date']           = $this->sma->fld($result['created_at']);
                                unset($payment['cc_cvv2']);
                                $this->db->insert('payments', $payment);
                                $this->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['msg'] . '</p>';
                            }
                        } else {
                            if ($payment['paid_by'] == 'gift_card') {
                                $award_points_flag=0;
                                if($payment['cc_no']!="" && $payment['cc_no']){
								    $gc = $this->getGiftCardByNO($payment['cc_no']);
                                    $this->db->update('gift_cards', ['balance' => $gc->balance-$payment['amount']], ['card_no' => $payment['cc_no']]);
                                }else{

                                    $sale_org = $this->getSaleByID($sale_id);
                                    $this->update_award_pointsV2($payment['amount'], $sale_org->customer_id, $sale_org->created_by, true);
                                }
                            //    unset($payment['gc_balance']);
                            } elseif ($payment['paid_by'] == 'deposit') {
                                $customer = $this->getCompanyByID($data['customer_id']);
                                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $payment['amount'])], ['id' => $customer->id]);
                            }
                            unset($payment['cc_cvv2']);
                            $this->db->insert('payments', $payment);
                            $this->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
                            $paid += $payment['amount'];
                        }
                    }
                }
                $this->syncSalePayments($sale_id);
            }
            if($award_points_flag==1){

                    $this->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);

            }
            /*$WhatsappSettings=$this->getWhatsappSettings();
            if ($WhatsappSettings->pos==1) {
                $customer = $this->getCompanyByID($data['customer_id']);
                $dbName=str_replace("cpesoft_","",$this->db->database);
                $text=  $this->sma->invSaleText($sale_id, $dbName);

                $this->sma->sendWhatsapp($customer->phone, $text);
            }*/

            $WhatsappSettings=$this->getWhatsappSettings();
            if ($WhatsappSettings->pos==1) {
                $company = $this->getCompanyByID($data['customer_id']);
                $inv         = $this->getInvoiceByID($sale_id);
                $dbName=str_replace("cpesoft_","",$this->db->database);
                $order_link='https://tinyurl.com/47f8rxxc/'.$dbName.'/'.$inv->uuid;
                //$text="عزيزي ".$company->company.", طلبك رقم  (Ref. ".$inv->reference_no.") بمبلغ  ".$this->sma->formatMoney($inv->grand_total)." رابط التحميل ".$order_link;

                $text=$this->getWhatsappTemplates()->sales;

                $text = str_replace("{customer}", $company->company, ($text) . "");
                $text = str_replace("{sale_reference}", $inv->reference_no, ($text) . "");
                $text = str_replace("{grand_total}", $this->sma->formatDecimal($inv->grand_total), ($text) . "");
                $text = str_replace("{order_link}", $order_link, ($text) . "");

                //$phone="0502478081";//$company->phone;
                $phone=$company->phone;
                $WhatsappSettingsConfig=json_decode($WhatsappSettings->config,true);

                if (!empty($WhatsappSettingsConfig['gateway']) && !empty($WhatsappSettingsConfig)) {
                    if($WhatsappSettingsConfig['gateway']=='4whatsapp'){
                        $phone = str_replace(" ", "", $phone);
                        $sent_flag=0;
                        $failed_flag=0;
                        $char = ",";
                        // Using strpos() function

                            $recipient="";
                            $tel_no=trim($phone);
                            $len = strlen($tel_no);
                                if($len>8 && is_numeric($tel_no)){
                                    if(substr($tel_no, 0, 1) === "+"){
                                        $recipient=$tel_no;
                                    }else{
                                        if($len>10){
                                            if(substr($tel_no, 0, 2) === "00"){
                                                $recipient="+".substr($tel_no, 2);
                                            }else{
                                                $recipient="+".$tel_no;
                                            }
                                        }else{
                                            if(substr($tel_no, 0, 1) === "0"){
                                                $recipient="+966".substr($tel_no, 1);
                                            }else{
                                                $recipient="+966".$tel_no;
                                            }
                                        }
                                    }

                                    $message=$text;
                                    $phone=$WhatsappSettingsConfig['4whatsapp']['phone'];
                                    $password=$WhatsappSettingsConfig['4whatsapp']['password'];
                                    $fields = array("phones"=>$phone, "account"=>"0", "doctype"=>"text", "recipient"=>$recipient, "message"=>$message, "token"=>$password);
                                    $files = array();
                                    $url = "http://api.4whatsapp.com/api/Agent_Client_";
                                    $curl = curl_init();
                                    $url_data = http_build_query($data);
                                    $boundary = 'b77a5c561934e089';
                                    $delimiter = '-------------' . $boundary;
                                    $post_data = '';
                                    $eol = "\r\n";

                                    $delimiter = '-------------' . $boundary;

                                    foreach ($fields as $name => $content) {
                                        $post_data .= "--" . $delimiter . $eol
                                        . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                                        . $content . $eol;
                                    }
                                    $post_data .= "--" . $delimiter . "--".$eol;
                                    curl_setopt_array($curl, array(
                                        CURLOPT_URL => $url,
                                        CURLOPT_RETURNTRANSFER => 1,
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 30,
                                        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => "POST",
                                        CURLOPT_POST => 1,
                                        CURLOPT_POSTFIELDS => $post_data,
                                        CURLOPT_HTTPHEADER => array(
                                          //"Authorization: Bearer $TOKEN",
                                          "Content-Type: multipart/form-data; boundary=" . $delimiter,
                                          "Content-Length: " . strlen($post_data)

                                        ),


                                    ));


                                    //$response = json_decode(curl_exec($curl));
                                    //curl_close($curl);
                                    $resp = curl_exec($curl);
                                    $response = json_decode($resp);
                                    curl_close($curl);

                                    $whatsappMsg="";
                                    if (isset($response->data) && !empty(json_decode($response->data)) && json_decode($response->data)->status=='200'){
                                        $sent_flag=1;
                                        try {
                                            $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                        } catch (Exception $e) {
                                        }
                                    }else{
                                        $failed_flag=1;
                                        try {
                                            $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                        } catch (Exception $e) {
                                        }
                                    }

                                }else{
                                    $failed_flag=1;
                                    try {
                                        $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                    } catch (Exception $e) {
                                    }
                                }





                    }else if($WhatsappSettingsConfig['gateway']=='arkanprowhatsapp'){
                        $phone = str_replace(" ", "", $phone);
                        $sent_flag=0;
                        $failed_flag=0;
                        $char = ",";
                        // Using strpos() function

                            $recipient="";
                            $tel_no=trim($phone);
                            $len = strlen($tel_no);
                                if($len>8 && is_numeric($tel_no)){
                                    if(substr($tel_no, 0, 1) === "+"){
                                        $recipient="".substr($tel_no, 1);
                                    }else{
                                        if($len>10){
                                            if(substr($tel_no, 0, 2) === "00"){
                                                $recipient="".substr($tel_no, 2);
                                            }else{
                                                $recipient="".$tel_no;
                                            }
                                        }else{
                                            if(substr($tel_no, 0, 1) === "0"){
                                                $recipient="966".substr($tel_no, 1);
                                            }else{
                                            $recipient="966".$tel_no;
                                            }
                                        }
                                    }

                                    $message=$text;
                                    $sessionId=$WhatsappSettingsConfig['arkanprowhatsapp']['sessionId'];
                                    $files = array();
                                    $url = "https://iparkanwebsitforwhats/".$sessionId."/messages/send";

                                    $curl = curl_init($url);
                                    curl_setopt($curl, CURLOPT_URL, $url);
                                    curl_setopt($curl, CURLOPT_POST, true);
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                                    $headers = array(
                                       "Content-Type: application/json",
                                       "accept: application/json",
                                    );
                                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                                    $data = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                                    //for debug only!
                                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                                    //$response = json_decode(curl_exec($curl));
                                    //curl_close($curl);
                                    $resp = curl_exec($curl);
                                    $response = json_decode($resp);
                                    curl_close($curl);

                                    $whatsappMsg="";
                                    if (isset($response->message) && $response->status=='PENDING'){
                                        $sent_flag=1;
                                        try {
                                            $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                        } catch (Exception $e) {
                                        }
                                    }else{
                                        $failed_flag=1;
                                        try {
                                            $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                        } catch (Exception $e) {
                                        }
                                    }

                                }else{
                                    $failed_flag=1;
                                    try {
                                        $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                    } catch (Exception $e) {
                                    }
                                }
                    }
                }

                /*$this->load->library('sms');
                $text=  $this->sms->invAwardPointsTextV2($company->company, $points, $total_points);

                $this->load->library('whatsapp');
                $this->whatsapp->send($company->phone, $text);*/

            }

            if(isset($this->getSystemSetting()->isAutoSynchronizeZATCAPOS) && $this->getSystemSetting()->isAutoSynchronizeZATCAPOS){
                $this->generate_xml($sale_id);
            }
            return ['sale_id' => $sale_id, 'message' => $msg];
        }
        return false;
    }

    public function authorize($authorize_data)
    {
        $this->load->library('authorize_net');
        // $authorize_data = array( 'x_card_num' => '4111111111111111', 'x_exp_date' => '12/20', 'x_card_code' => '123', 'x_amount' => '25', 'x_invoice_num' => '15454', 'x_description' => 'References');
        $this->authorize_net->setData($authorize_data);

        if ($this->authorize_net->authorizeAndCapture()) {
            $result = [
                'transaction_id' => $this->authorize_net->getTransactionId(),
                'approval_code'  => $this->authorize_net->getApprovalCode(),
                'created_at'     => date($this->dateFormats['php_ldate']),
            ];
            return $result;
        } else {
            return ['error' => 1, 'msg' => $this->authorize_net->getError()];
        }
    }

    public function bills_count()
    {
        if (!$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        return $this->db->count_all_results('suspended_bills');
    }

    public function closeRegister($rid, $user_id, $data)
    {
        $register=$this->registerData($user_id);

		if (!$rid) {
            $rid = $this->session->userdata('register_id');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($data['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', ['created_by' => $user_id]);
        } elseif ($data['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', ['created_by' => $data['transfer_opened_bills']], ['created_by' => $user_id]);
        }
        if ($this->db->update('pos_register', $data, ['id' => $rid, 'user_id' => $user_id])) {

			/*if ($this->getSystemSetting()->accounting){
			$entry_type="Close_biller_register";



			$sales=$this->getToatalSales($this->getUser($data['user_id'])->biller_id,$register->date,$data['closed_at']);
			$paymentcash=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'cash',$register->date,$data['closed_at']);
			$paymentCC=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'CC',$register->date,$data['closed_at']);
			$paymentbank_transfer=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'bank_transfer',$register->date,$data['closed_at']);
			$paymentCheque=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'Cheque',$register->date,$data['closed_at']);



					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$entrybillerAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);

					if($paymentbank_transfer->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('bank_transfer')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentbank_transfer->amount);

					}
					if($paymentCheque->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('Cheque')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCheque->amount);

					}

					if($paymentCC->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCC->amount);

					}


					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_acct;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total_tax);



			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$register->id,$entry_type,$data['user_id']);

				       $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;


					$this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($sales->item_cost_value),$this->sma->formatDecimal($sales->item_cost_value),$register->id,$entry_type,$data['user_id']);

			}*/

            return true;
        }
        return false;
    }


	public function closeRegisterWithOutOpen($data)
    {
        //$register=$this->registerData($data['user_id']);

		$user_id=$data['user_id'];
		if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($data['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', ['created_by' => $user_id]);
        } elseif ($data['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', ['created_by' => $data['transfer_opened_bills']], ['created_by' => $user_id]);
        }
        if ($this->db->insert('pos_register', $data)) {
			$id = $this->db->insert_id();

			if ($this->getSystemSetting()->accounting){
			$entry_type="Close_biller_register";



			$sales=$this->getToatalSales($this->getUser($data['user_id'])->biller_id,$data['date'],$data['closed_at']);
			$paymentcash=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'cash',$data['date'],$data['closed_at']);
			$paymentCC=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'CC',$data['date'],$data['closed_at']);
			$paymentbank_transfer=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'bank_transfer',$data['date'],$data['closed_at']);
			$paymentCheque=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'Cheque',$data['date'],$data['closed_at']);



					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$entrybillerAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);

					if($paymentbank_transfer->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('bank_transfer')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentbank_transfer->amount);

					}
					if($paymentCheque->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('Cheque')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCheque->amount);

					}

					if($paymentCC->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCC->amount);

					}


					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_acct;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total_tax);



			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$id,$entry_type);

				       $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;


					$this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($sales->item_cost_value),$this->sma->formatDecimal($sales->item_cost_value),$id,$entry_type);

			}

            return true;
        }
        return false;
    }


	public function deleteBill($id)
    {
        if ($this->db->delete('suspended_items', ['suspend_id' => $id]) && $this->db->delete('suspended_bills', ['id' => $id])) {
            return true;
        }

        return false;
    }

    public function deletePrinter($id)
    {
        if ($this->db->delete('printers', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function fetch_bills($limit, $start)
    {
        if (!$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->limit($limit, $start);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('suspended_bills');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function fetch_products($category_id, $limit, $start, $subcategory_id = null, $brand_id = null)
    {
        $this->db->limit($limit, $start);
        if ($brand_id) {
            $this->db->where('brand', $brand_id);
        } elseif ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->where('hide_pos !=', 1);
        $this->db->order_by('subcategory_id', 'desc');
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('products');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function fetch_sales($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('sales');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllBillerCompanies()
    {
        $q = $this->db->get_where('companies', ['group_name' => 'biller']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllCustomerCompanies()
    {
        $q = $this->db->get_where('companies', ['group_name' => 'customer']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllInvoiceItems($sale_id)
    {
        if ($this->pos_settings->item_order == 0) {
            $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, product_variants.name as variant, products.details as details, products.cf1 as cf1, products.cf2 as cf2, products.cf3 as cf3,  products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');
        } elseif ($this->pos_settings->item_order == 1) {
            $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, product_variants.name as variant, categories.id as category_id, categories.name as category_name, products.details as details, products.cf1 as cf1, products.cf2 as cf2, products.cf3 as cf3,  products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('categories.id', 'asc');
        }

        $q = $this->db->get_where('sale_items', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllPrinters()
    {
        $q = $this->db->get('printers');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllProducts()
    {
        $q = $this->db->query('SELECT * FROM products ORDER BY id');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllSales()
    {
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getCosting()
    {
        $date = date('Y-m-d');
        $this->db->select('SUM( COALESCE( purchase_unit_cost, 0 ) * quantity ) AS cost, SUM( COALESCE( sale_unit_price, 0 ) * quantity ) AS sales, SUM( COALESCE( purchase_net_unit_cost, 0 ) * quantity ) AS net_cost, SUM( COALESCE( sale_net_unit_price, 0 ) * quantity ) AS net_sales', false)
            ->where('date', $date);

        $q = $this->db->get('costing');
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

    public function getInvoicePayments($sale_id)
    {
        $q = $this->db->get_where('payments', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }

        return false;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('sale_items', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getOpenBillByID($id)
    {
        $q = $this->db->get_where('suspended_bills', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getOpenRegisters()
    {
        $this->db->select('date, user_id, cash_in_hand, CONCAT(' . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' - ', " . $this->db->dbprefix('users') . '.email) as user', false)
            ->join('users', 'users.id=pos_register.user_id', 'left');
        $q = $this->db->get_where('pos_register', ['status' => 'open']);
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPrinterByID($id)
    {
        $q = $this->db->get_where('printers', ['id' => $id], 1);
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

    public function getProductComboItems($pid, $warehouse_id = null)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, warehouses_products.quantity as quantity, warehouses_products.avg_cost as avg_cost')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            //->where('warehouses_products.warehouse_id', $warehouse_id)
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
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, FWPV.quantity as quantity', false)
            ->join($wpv, 'FWPV.option_id=product_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=product_variants.warehouse_id', 'left')
            ->where('product_variants.product_id', $product_id)
            ->group_by('product_variants.id');

        if (!$this->getSystemSetting()->overselling && !$all) {
            //$this->db->where('FWPV.warehouse_id', $warehouse_id);
            //$this->db->where('FWPV.quantity >', 0);
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

    public function getProductsByCode($code)
    {
        $this->db->like('code', $code, 'both')->order_by('code');
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products_variants', ['option_id' => $option_id, 'warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterAuthorizeSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'authorize');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCashRefunds($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', false)
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where('paid_by', 'cash');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCashSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'cash');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCCSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'CC');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterChSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'Cheque');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterExpenses($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', false)
            ->where('date >', $date);
        $this->db->where('created_by', $user_id);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterGCSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'gift_card');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterPPPSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'ppp');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterRefunds($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', false)
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterReturns($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total', false)
        ->where('date >', $date)
        ->where('returns.created_by', $user_id);

        $q = $this->db->get('returns');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        /*$this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date);*/



			$this->db->select('SUM( COALESCE( amount, 0 ) ) AS paid', false)
            //->join('sales', 'sales.id=sale_items.sale_id', 'left')
            //->join('payments', ' payments.sale_id=sales.id', 'left')
             ->where('type', 'received')->where('date >', $date);



        $this->db->where('created_by', $user_id);

        $qp = $this->db->get('payments');
        if ($qp->num_rows() > 0) {
			$paid=$qp->row()->paid;
            //return $qp->row();
        }



	   $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total', false)
           // ->join('sales', 'sales.id=sale_items.sale_id', 'left')
            //->join('payments', ' payments.sale_id=sales.id', 'left')
            ->where('payment_status', 'paid')->where('date >', $date);




        $this->db->where('created_by', $user_id);

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
			$q->row()->paid=$paid;
            return $q->row();
        }
        return false;
    }
public function getRegisterSalesByCatogries($date, $user_id = null)
    {


		$sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');

		//$date = date('Y-m-d 00:00:00');


		$myQuery ="select SUM( COALESCE( s.net_unit_price * s.quantity, 0 ) ) AS total,SUM(  s.subtotal ) AS ftotal,SUM( COALESCE( s.item_cost_value, 0 ) ) AS ctotal,SUM( COALESCE( s.item_tax, 0 ) ) AS ttax, c.name  as name from " . $this->db->dbprefix('sales') . " ss," . $this->db->dbprefix('sale_items') . " s," . $this->db->dbprefix('products') . " pr," . $this->db->dbprefix('categories') . " c where ss.id=s.sale_id and pr.id=s.product_id and c.id=pr.category_id and ss.paid!='0' and ss.date >='".$sdate."' and ss.date <='".$edate."'  group by c.name";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;

        /*$this->db->select('SUM( COALESCE( net_unit_price * quantity, 0 ) ) AS total ,SUM(  subtotal ) AS ftotal,SUM( COALESCE( item_cost_value, 0 ) ) AS ctotal,SUM( COALESCE( item_tax, 0 ) ) AS ttax, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            //->join('sales', 'sales.id=payments.sale_id', 'left')
            ->join('sale_items', 'sale_items.sale_id= payments.sale_id', 'left')
             //->join('categories', 'categories.id=products.category_id', 'left')
            //->join('products', 'products.id=sale_items.product_id', 'left')

			->where('payments.type', 'received')->where('payments.date >=', $sdate)->where('payments.date <=', $edate);



        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
		*/

    }

    public function getRegisterStripeSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'stripe');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSaleItems($id)
    {
        $q = $this->db->get_where('sale_items', ['sale_id' => $id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

	public function getAllSalesByDate($start_date,$end_date)
    {

	   $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            $this->db->where('sale_status', 'completed');

		$q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getSetting()
    {
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSystemSetting()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSuspendedSaleItems($id)
    {
        $q = $this->db->get_where('suspended_items', ['suspend_id' => $id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getSuspendedSales($user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('suspended_bills', ['created_by' => $user_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getTaxRateByID($id)
    {
        $q = $this->db->get_where('tax_rates', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getTodayAuthorizeSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'authorize');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashRefunds()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', false)
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where('paid_by', 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCCSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'CC');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayChSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'Cheque');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayExpenses()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', false)
            ->where('date >', $date);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayPPPSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'ppp');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayRefunds()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', false)
            ->join('sales', 'sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayReturns()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total', false)
            ->where('date >', $date);

        $q = $this->db->get('returns');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodaySales()
    {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');

		//$date = date('Y-m-d 00:00:00');

        $this->db->select('SUM( COALESCE( net_unit_price * quantity, 0 ) ) AS total ,SUM(  subtotal ) AS ftotal,SUM( COALESCE( item_cost_value, 0 ) ) AS ctotal,SUM( COALESCE( item_tax, 0 ) ) AS ttax, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            //->join('sales', 'sales.id=payments.sale_id', 'left')
            ->join('sale_items', 'sale_items.sale_id= payments.sale_id', 'left')
            ->where('payments.type', 'received')->where('payments.date >=', $sdate)->where('payments.date <=', $edate);



        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayStripeSales()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'stripe');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getUsers()
    {
        $q = $this->db->get_where('users', ['company_id' => null]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getWHProduct($code, $warehouse_id)
    {
        $this->db->select('products.*, warehouses_products.quantity, categories.id as category_id, categories.name as category_name')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->where('categories.hide_pos !=', 1)
            ->group_by('products.id');
        $q = $this->db->get_where('products', ['products.code' => $code]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getWHProductById($id, $warehouse_id)
    {
        $this->db->select('products.*, warehouses_products.quantity, categories.id as category_id, categories.name as category_name')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->group_by('products.id');
        $q = $this->db->get_where('products', ['products.id' => $id]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function insertQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity])) {
            return true;
        }
        return false;
    }

    public function openRegister($data)
    {
        if ($this->db->insert('pos_register', $data)) {

			$reg_id = $this->db->insert_id();

			/*if ($this->getSystemSetting()->accounting){
			$entry_type="Open_biller_register";

			$entryDebitAccount= [];



						$entrybillerAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;

					$entryCreditAccount= [];

					$entryCreditAccount[]=$this->getPaymentTypeByPaymentType('cash')->payment_account;


			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['cash_in_hand']),$this->sma->formatDecimal($data['cash_in_hand']),$reg_id,$entry_type,$data['user_id']);

			}*/


            return true;
        }
        return false;
    }

    public function paypal($amount = null, $card_info = [], $desc = '', $sale_id = null)
    {
        $this->load->admin_model('paypal_payments');
        //$card_info = array( "number" => "5522340006063638", "exp_month" => 2, "exp_year" => 2016, "cvc" => "456", 'type' => 'MasterCard' );
        //$amount = $amount ? $amount : 30.00;
        if ($amount && !empty($card_info)) {
            $data = $this->paypal_payments->Do_direct_payment($amount, $this->default_currency->code, $card_info, $desc, $sale_id);
            if (!isset($data['error'])) {
                $result = ['transaction_id' => $data['TRANSACTIONID'],
                    'created_at'            => date($this->dateFormats['php_ldate'], strtotime($data['TIMESTAMP'])),
                    'amount'                => $data['AMT'],
                    'currency'              => strtoupper($data['CURRENCYCODE']),
                ];
                return $result;
            } else {
                return $data;
            }
        }
        return false;
    }

    public function products_count($category_id, $subcategory_id = null, $brand_id = null)
    {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        if ($brand_id) {
            $this->db->where('brand', $brand_id);
        }
        $this->db->where('hide_pos !=', 1);
        $this->db->from('products');
        return $this->db->count_all_results();
    }

    public function registerData($user_id)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
		$this->db->order_by('id', 'desc');

        $q = $this->db->get_where('pos_register', ['user_id' => $user_id, 'status' => 'open'], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function sales_count()
    {
        return $this->db->count_all('sales');
    }

    public function stripe($amount = 0, $card_info = [], $desc = '')
    {
        $this->load->admin_model('stripe_payments');
        //$card_info = array( "number" => "4242424242424242", "exp_month" => 1, "exp_year" => 2016, "cvc" => "314" );
        //$amount = $amount ? $amount*100 : 3000;
        unset($card_info['type']);
        $amount = $amount * 100;
        if ($amount && !empty($card_info)) {
            $token_info = $this->stripe_payments->create_card_token($card_info);
            if (!isset($token_info['error'])) {
                $token = $token_info->id;
                $data  = $this->stripe_payments->insert($token, $desc, $amount, $this->default_currency->code);
                if (!isset($data['error'])) {
                    $result = ['transaction_id' => $data->id,
                        'created_at'            => date($this->dateFormats['php_ldate'], $data->created),
                        'amount'                => ($data->amount / 100),
                        'currency'              => strtoupper($data->currency),
                    ];
                    return $result;
                } else {
                    return $data;
                }
            } else {
                return $token_info;
            }
        }
        return false;
    }

    public function suspendSale($data = [], $items = [], $did = null)
    {
        $sData = [
            'count'             => $data['total_items'],
            'biller_id'         => $data['biller_id'],
            'customer_id'       => $data['customer_id'],
            'warehouse_id'      => $data['warehouse_id'],
            'customer'          => $data['customer'],
            'date'              => $data['date'],
            'suspend_note'      => $data['suspend_note'],
            'total'             => $data['grand_total'],
            'order_tax_id'      => $data['order_tax_id'],
            'order_discount_id' => $data['order_discount_id'],
            'created_by'        => $this->session->userdata('user_id'),
        ];

        if ($did) {
            if ($this->db->update('suspended_bills', $sData, ['id' => $did]) && $this->db->delete('suspended_items', ['suspend_id' => $did])) {
                $addOn = ['suspend_id' => $did];
                end($addOn);
                foreach ($items as &$var) {
                    $var = array_merge($addOn, $var);
                }
                if ($this->db->insert_batch('suspended_items', $items)) {
                    return true;
                }
            }
        } else {
            if ($this->db->insert('suspended_bills', $sData)) {
                $suspend_id = $this->db->insert_id();
                $addOn      = ['suspend_id' => $suspend_id];
                end($addOn);
                foreach ($items as &$var) {
                    $var = array_merge($addOn, $var);
                }
                if ($this->db->insert_batch('suspended_items', $items)) {
                    return true;
                }
            }
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

    public function updatePrinter($id, $data = [])
    {
        if ($this->db->update('printers', $data, ['id' => $id])) {
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

    public function updateProductQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($this->addQuantity($product_id, $warehouse_id, $quantity)) {
            return true;
        }

        return false;
    }

    public function updateQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($this->db->update('warehouses_products', ['quantity' => $quantity], ['product_id' => $product_id, 'warehouse_id' => $warehouse_id])) {
         $productid=$this->getProductByID($product_id)->code;
			if($this->db->update('synch_products', ['api' => 0], ['warehouse_id' => $warehouse_id, 'product_code' => $productid])){

			}
			   return true;
        }
        return false;
    }

    public function updateSetting($data)
    {
        $this->db->where('pos_id', '1');
        if ($this->db->update('pos_settings', $data)) {
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


	public function getWarehouse_VATByID($id)
    {
        $q = $this->db->get_where('warehouses', ['id' => $id], 1);
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

	public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', ['warehouse_id' => $warehouse_id, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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
        $this->setPurchaseItem_return($clause, $quantity);
        $this->syncQuantity(null, null, null, $product_id);
    }

    public function getCostingLines($sale_item_id, $product_id, $sale_id = null)
    {
        if ($sale_id) {
            $this->db->where('sale_id', $sale_id);
        }
        $orderby = ($this->getSystemSetting()->accounting_method == 1) ? 'asc' : 'desc';
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

    public function getInvoiceIDByReference($reference_no)
    {
        $q = $this->db->get_where('sales', ['reference_no' => $reference_no], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function get_return_sale_id_V2($Offline_Pos_id,$warehouse_id,$biller_id,$total_items = null)
    {
        if($total_items)
        $this->db->where('total_items =', $total_items);

        $this->db->order_by('id', 'desc');


        $q = $this->db->get_where('sales', ['Offline_Pos_id' => $Offline_Pos_id,'warehouse_id' => $warehouse_id,'biller_id' => $biller_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function get_return_sale_id($Offline_Pos_id,$warehouse_id,$biller_id)
    {
        $this->db->order_by('id', 'DESC');
        
        $q = $this->db->get_where('sales', ['Offline_Pos_id' => $Offline_Pos_id,'warehouse_id' => $warehouse_id,'biller_id' => $biller_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }
    public function get_return_sale_id_for_payment($Offline_Pos_id,$created_by)
    {
        $q = $this->db->get_where('sales', ['Offline_Pos_id' => $Offline_Pos_id,'created_by' => $created_by], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

     public function get_return_sale_item_id($sale_id, $product_id,$warehouse_id)
    {
        $q = $this->db->get_where('sale_items', ['sale_id' => $sale_id, 'product_id' => $product_id,'warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }


	public function costing($items)
    {

        $citems = [];

        foreach ($items as $item) {

            $option            = (isset($item['option_id']) && !empty($item['option_id']) && $item['option_id'] != 'null' && $item['option_id'] != 'false') ? $item['option_id'] : '';
            $pr                = $this->getProductByID($item['product_id']);
            $item['option_id'] = $option;


            if ($pr && ($pr->type == 'standard' ||  $pr->type == 'manufacturing')) {


                if (isset($citems['p' . $item['product_id'] . 'o' . $item['option_id']])) {
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'] += $item['quantity'];
                } else {
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']]              = $item;
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'] = $item['quantity'];
                }

				} elseif ($pr && $pr->type == 'combo') {
                    //$wh          = $this->getSystemSetting()->overselling ? null : $item['warehouse_id'];
                    $wh          = null;

                $combo_items = $this->getProductComboItems($item['product_id'], $wh);
            foreach ($combo_items as $combo_item) {
                    if ($combo_item->type == 'standard' || $combo_item->type == 'manufacturing') {
                        if (isset($citems['p' . $combo_item->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty * $item['quantity']);
                        } else {
                            $cpr = $this->getProductByID($combo_item->id);
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    //$item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price - $item_tax;
                                    $unit_price     = $combo_item->unit_price;
                                } else {
                                    $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price;
                                    $unit_price     = $combo_item->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price     = $combo_item->unit_price;
                            }
                            $cproduct                                                              = ['product_id' => $combo_item->id, 'product_name' => $cpr->name, 'product_type' => $combo_item->type, 'quantity' => ($combo_item->qty * $item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate . '%' : $cpr_tax->rate), 'option_id' => null, 'product_unit_id' => $cpr->unit];
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]              = $cproduct;
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty * $item['quantity']);
                        }
                    }else{



						if ($combo_item->type == 'combo') {
							$combo_items2 = $this->getProductComboItems($combo_item->id, $wh);
							    //return $combo_items2;

								foreach ($combo_items2 as $combo_item2) {
							if ($combo_item2->type == 'standard' || $combo_item2->type == 'manufacturing') {
                        if (isset($citems['p' . $combo_item2->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item2->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty * $combo_item2->qty * $item['quantity']);
                        } else {
                            $cpr = $this->getProductByID($combo_item2->id);
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    //$item_tax       = $this->sma->formatDecimal((($combo_item2->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $item_tax       = $this->sma->formatDecimal((($combo_item2->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item2->unit_price - $item_tax;
                                    $unit_price     = $combo_item2->unit_price;
                                } else {
                                    $item_tax       = $this->sma->formatDecimal((($combo_item2->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item2->unit_price;
                                    $unit_price     = $combo_item2->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item2->unit_price;
                                $unit_price     = $combo_item2->unit_price;
                            }
                            $cproduct                                                              = ['product_id' => $combo_item2->id, 'product_name' => $cpr->name, 'product_type' => $combo_item2->type, 'quantity' => ($combo_item2->qty * $item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate . '%' : $cpr_tax->rate), 'option_id' => null, 'product_unit_id' => $cpr->unit];
                            $citems['p' . $combo_item2->id . 'o' . $item['option_id']]              = $cproduct;
                            $citems['p' . $combo_item2->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty * $combo_item2->qty * $item['quantity']);
                        }
								//return $citems;

								}
                    }
					}

					}



                }
            }
            /*} elseif ($pr && $pr->type == 'combo') {
                $wh          = $this->getSystemSetting()->overselling ? null : $item['warehouse_id'];
                $combo_items = $this->getProductComboItems($item['product_id'], $wh);
                foreach ($combo_items as $combo_item) {
                    if ($combo_item->type == 'standard') {
                        if (isset($citems['p' . $combo_item->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty * $item['quantity']);
                        } else {
                            $cpr = $this->getProductByID($combo_item->id);
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    //$item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price - $item_tax;
                                    $unit_price     = $combo_item->unit_price;
                                } else {
                                    $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price;
                                    $unit_price     = $combo_item->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price     = $combo_item->unit_price;
                            }
                            $cproduct                                                              = ['product_id' => $combo_item->id, 'product_name' => $cpr->name, 'product_type' => $combo_item->type, 'quantity' => ($combo_item->qty * $item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate . '%' : $cpr_tax->rate), 'option_id' => null, 'product_unit_id' => $cpr->unit];
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]              = $cproduct;
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty * $item['quantity']);
                        }
                    }
                }
            }*/
        }

        // $this->sma->print_arrays($combo_items, $citems);
        $cost = [];
        foreach ($citems as $item) {

            $item['aquantity'] = $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'];

            $cost[]            = $this->item_costing($item, true);

        }
        return $cost;
    }





	public function costing2($items)
    {

        $citems = [];
        foreach ($items as $item) {
            $option            = (isset($item['option_id']) && !empty($item['option_id']) && $item['option_id'] != 'null' && $item['option_id'] != 'false') ? $item['option_id'] : '';
            $pr                = $this->getProductByID($item['product_id']);
            $item['option_id'] = $option;
            if ($pr && ($pr->type == 'manufacturing')) {


                if (isset($citems['p' . $item['product_id'] . 'o' . $item['option_id']])) {
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'] += $item['quantity'];
                } else {
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']]              = $item;
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'] = $item['quantity'];
                }
				} elseif ($pr && $pr->type == 'combo') {
                    //$wh          = $this->getSystemSetting()->overselling ? null : $item['warehouse_id'];
                    $wh          = null;

                $combo_items = $this->getProductComboItems($item['product_id'], $wh);
            foreach ($combo_items as $combo_item) {
                    if ($combo_item->type == 'manufacturing') {
                        if (isset($citems['p' . $combo_item->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty * $item['quantity']);
                        } else {
                            $cpr = $this->getProductByID($combo_item->id);
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    //$item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price - $item_tax;
                                    $unit_price     = $combo_item->unit_price;
                                } else {
                                    $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price;
                                    $unit_price     = $combo_item->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price     = $combo_item->unit_price;
                            }
                            $cproduct                                                              = ['product_id' => $combo_item->id, 'product_name' => $cpr->name, 'product_type' => $combo_item->type, 'quantity' => ($combo_item->qty * $item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate . '%' : $cpr_tax->rate), 'option_id' => null, 'product_unit_id' => $cpr->unit];
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]              = $cproduct;
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty * $item['quantity']);
                        }
                    }else{



						if ($combo_item->type == 'combo') {
							$combo_items2 = $this->getProductComboItems($combo_item->id, $wh);
							    //return $combo_items2;

								foreach ($combo_items2 as $combo_item2) {
							if ($combo_item2->type == 'manufacturing') {
                        if (isset($citems['p' . $combo_item2->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item2->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty * $combo_item2->qty * $item['quantity']);
                        } else {
                            $cpr = $this->getProductByID($combo_item2->id);
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    //$item_tax       = $this->sma->formatDecimal((($combo_item2->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $item_tax       = $this->sma->formatDecimal((($combo_item2->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item2->unit_price - $item_tax;
                                    $unit_price     = $combo_item2->unit_price;
                                } else {
                                    $item_tax       = $this->sma->formatDecimal((($combo_item2->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item2->unit_price;
                                    $unit_price     = $combo_item2->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item2->unit_price;
                                $unit_price     = $combo_item2->unit_price;
                            }
                            $cproduct                                                              = ['product_id' => $combo_item2->id, 'product_name' => $cpr->name, 'product_type' => $combo_item2->type, 'quantity' => ($combo_item2->qty * $item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate . '%' : $cpr_tax->rate), 'option_id' => null, 'product_unit_id' => $cpr->unit];
                            $citems['p' . $combo_item2->id . 'o' . $item['option_id']]              = $cproduct;
                            $citems['p' . $combo_item2->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty * $combo_item2->qty * $item['quantity']);
                        }
								//return $citems;

								}
                    }
					}

					}



                }
            }
            /*} elseif ($pr && $pr->type == 'combo') {
                $wh          = $this->getSystemSetting()->overselling ? null : $item['warehouse_id'];
                $combo_items = $this->getProductComboItems($item['product_id'], $wh);
                foreach ($combo_items as $combo_item) {
                    if ($combo_item->type == 'standard') {
                        if (isset($citems['p' . $combo_item->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty * $item['quantity']);
                        } else {
                            $cpr = $this->getProductByID($combo_item->id);
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    //$item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price - $item_tax;
                                    $unit_price     = $combo_item->unit_price;
                                } else {
                                    $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price;
                                    $unit_price     = $combo_item->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price     = $combo_item->unit_price;
                            }
                            $cproduct                                                              = ['product_id' => $combo_item->id, 'product_name' => $cpr->name, 'product_type' => $combo_item->type, 'quantity' => ($combo_item->qty * $item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate . '%' : $cpr_tax->rate), 'option_id' => null, 'product_unit_id' => $cpr->unit];
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]              = $cproduct;
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty * $item['quantity']);
                        }
                    }
                }
            }*/
        }
        // $this->sma->print_arrays($combo_items, $citems);
        $cost = [];
        foreach ($citems as $item) {
            $item['aquantity'] = $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'];
            $cost[]            = $this->item_costing($item, true);
        }
        return $cost;
    }



	public function item_costing($item, $pi = null)
    {


        $item_quantity = $pi ? $item['aquantity'] : $item['quantity'];
        if (!isset($item['option_id']) || empty($item['option_id']) || $item['option_id'] == 'null') {
            $item['option_id'] = null;
        }

        if ($this->getSystemSetting()->accounting_method != 2 && !$this->getSystemSetting()->overselling) {
            if ($this->getProductByID($item['product_id'])) {
                if ($item['product_type'] == 'standard' || $item['product_type'] == 'manufacturing' ) {
                    $unit                   = $this->getUnitByID($item['product_unit_id']);
                    $item['net_unit_price'] = $this->convertToBase($unit, $item['net_unit_price']);
                    $item['unit_price']     = $this->convertToBase($unit, $item['unit_price']);
                    $cost                   = $this->calculateCost($item['product_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['product_name'], $item['option_id'], $item_quantity);
                } elseif ($item['product_type'] == 'combo') {
                    $combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getProductByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price     = $combo_item->unit_price;
                            } else {
                                $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price     = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price     = $combo_item->unit_price;
                        }
                        if ($pr->type == 'standard' || $pr->type == 'manufacturing') {
                            $cost[] = $this->calculateCost($pr->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $pr->name, null, $item_quantity);
                        } else {
                            $cost[] = [['date' => date('Y-m-d'), 'product_id' => $pr->id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => null, 'quantity' => ($combo_item->qty * $item['quantity']), 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $combo_item->unit_price, 'sale_unit_price' => $combo_item->unit_price, 'quantity_balance' => (0 - $item_quantity), 'inventory' => null]];
                        }
                    }
                } else {
                    $cost = [['date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => null, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => (0 - $item_quantity), 'inventory' => null]];
                }
            } elseif ($item['product_type'] == 'manual') {
                $cost = [['date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => null, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => null, 'inventory' => null]];
            }
        } else {
            if ($this->getProductByID($item['product_id'])) {
                if ($item['product_type'] == 'standard' || $item['product_type'] == 'manufacturing' ) {
                    $cost = $this->calculateAVCost($item['product_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['product_name'], $item['option_id'], $item_quantity);
                } elseif ($item['product_type'] == 'combo') {
                    $combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getProductByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price     = $combo_item->unit_price;
                            } else {
                                $item_tax       = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price     = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price     = $combo_item->unit_price;
                        }
                        $cost[] = $this->calculateAVCost($combo_item->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $item['product_name'], $item['option_id'], $item_quantity);
                    }
                } else {
                    $cost = [['date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => null, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => null, 'inventory' => null]];
                }
            } elseif ($item['product_type'] == 'manual') {
                $cost = [['date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => null, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => null, 'inventory' => null]];
            }
        }
        return $cost;
    }

    public function getUnitByID($id)
    {
        $q = $this->db->get_where('units', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function convertToBase($unit, $value)
    {
        switch ($unit->operator) {
            case '*':
                return $value / $unit->operation_value;
                break;
            case '/':
                return $value * $unit->operation_value;
                break;
            case '+':
                return $value - $unit->operation_value;
                break;
            case '-':
                return $value + $unit->operation_value;
                break;
            default:
                return $value;
        }
    }

     public function calculateCost($product_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $product_name, $option_id, $item_quantity)
    {
        $pis           = $this->getPurchasedItems($product_id, $warehouse_id, $option_id);
        $real_item_qty = $quantity;
        $quantity      = $item_quantity;
        $balance_qty   = $quantity;
        foreach ($pis as $pi) {
            $cost_row = null;
            if (!empty($pi) && $balance_qty <= $quantity && $quantity != 0) {
                $purchase_unit_cost = $pi->unit_cost ? $pi->unit_cost : ($pi->net_unit_cost + ($pi->item_tax / $pi->quantity));
                if ($pi->quantity_balance >= $quantity && $quantity != 0) {
                    $balance_qty = $pi->quantity_balance - $quantity;
                    $cost_row    = ['date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id];
                    $quantity    = 0;
                } elseif ($quantity != 0) {
                    $quantity    = $quantity - $pi->quantity_balance;
                    $balance_qty = $quantity;
                    $cost_row    = ['date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id];
                }
            }
            $cost[] = $cost_row;
            if ($quantity == 0) {
                break;
            }
        }
        if ($quantity > 0) {
            $this->session->set_flashdata('error', sprintf(lang('quantity_out_of_stock_for').'%s', (isset($pi->product_name) ? $pi->product_name : $product_name)));
            redirect($_SERVER['HTTP_REFERER']);
        }
        return $cost;
    }

	public function getPurchasedItems($product_id, $warehouse_id, $option_id = null, $nonPurchased = false)
    {
        $orderby = empty($this->getSystemSetting()->accounting_method) ? 'asc' : 'desc';
        $this->db->select('id, quantity, quantity_balance, net_unit_cost, unit_cost, item_tax');
        $this->db->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('quantity_balance !=', 0);
        if (!isset($option_id) || empty($option_id)) {
            $this->db->group_start()->where('option_id', null)->or_where('option_id', 0)->group_end();
        } else {
            $this->db->where('option_id', $option_id);
        }
        if ($nonPurchased) {
            $this->db->group_start()->where('purchase_id !=', null)->or_where('transfer_id !=', null)->group_end();
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $this->db->group_by('id');
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function calculateAVCost($product_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $product_name, $option_id, $item_quantity)
    {
        $product       = $this->getProductByID($product_id);
        $real_item_qty = $quantity;
        $wp_details    = $this->getWarehouseProduct($warehouse_id, $product_id);
        $con           = $wp_details ? $wp_details->avg_cost : $product->cost;
        $tax_rate      = $this->getTaxRateByID($product->tax_rate);
        $ctax          = $this->calculateTax($product, $tax_rate, $con);
        if ($product->tax_method) {
            $avg_net_unit_cost = $con;
            $avg_unit_cost     = ($con + $ctax['amount']);
        } else {
            $avg_unit_cost     = $con;
            $avg_net_unit_cost = ($con - $ctax['amount']);
        }


        if ($pis = $this->getPurchasedItems($product_id, $warehouse_id, $option_id)) {
            $cost_row    = [];
            $quantity    = $item_quantity;
            $balance_qty = $quantity;
            foreach ($pis as $pi) {
                if (!empty($pi) && $pi->quantity > 0 && $balance_qty <= $quantity && $quantity != 0) {
                    if ($pi->quantity_balance >= $quantity && $quantity != 0) {
                        $balance_qty = $pi->quantity_balance - $quantity;
                        $cost_row    = ['date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id];
                        $quantity    = 0;
                    } elseif ($quantity != 0) {
                        $quantity    = $quantity - $pi->quantity_balance;
                        $balance_qty = $quantity;
                        $cost_row    = ['date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id];
                    }
                }
                if (empty($cost_row)) {
                    break;
                }
                $cost[] = $cost_row;
                if ($quantity == 0) {
                    break;
                }
            }
        }
        /*if ($quantity > 0 && !$this->getSystemSetting()->overselling) {
            $this->session->set_flashdata('error', sprintf(lang('quantity_out_of_stock_for_%s'), $product_name));
            redirect($_SERVER['HTTP_REFERER']);
        } else*/if ($quantity != 0) {
            $cost[] = ['date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => null, 'quantity' => $quantity, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => (0 - $quantity), 'overselling' => 1, 'inventory' => 1];
            $cost[] = ['pi_overselling' => 1, 'product_id' => $product_id, 'quantity_balance' => (0 - $quantity), 'warehouse_id' => $warehouse_id, 'option_id' => $option_id];
        }
        return $cost;
    }

	public function getWarehouseProduct($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function calculateTax($product_details = null, $tax_details, $custom_value = null, $c_on = null)
    {
        $value      = $custom_value ? $custom_value : (($c_on == 'cost') ? $product_details->cost : $product_details->price);
        $tax_amount = 0;
        $tax        = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
            if ($product_details && $product_details->tax_method == 1) {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / 100, 12);
                $tax        = $this->sma->formatDecimal($tax_details->rate, 0) . '%';
            } else {
                //$tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100), 12);
                $tax        = $this->sma->formatDecimal($tax_details->rate, 0) . '%';
            }
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = $this->sma->formatDecimal($tax_details->rate);
            $tax        = $this->sma->formatDecimal($tax_details->rate, 0);
        }
        return ['id' => $tax_details->id, 'tax' => $tax, 'amount' => $tax_amount];
    }

    public function syncPurchaseItems($data = [])
    {
        if (!empty($data)) {
            foreach ($data as $items) {
                foreach ($items as $item) {
                    if (isset($item['pi_overselling'])) {
                        unset($item['pi_overselling']);
                        $option_id = (isset($item['option_id']) && !empty($item['option_id'])) ? $item['option_id'] : null;
                        $clause    = ['purchase_id' => null, 'transfer_id' => null, 'product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'option_id' => $option_id];
                        if ($pi = $this->getPurchasedItem($clause)) {
                            $quantity_balance = $pi->quantity_balance + $item['quantity_balance'];
                            if($quantity_balance>0)
							$this->db->update('purchase_items', ['quantity_balance' => $quantity_balance,'quantity_received' => $quantity_balance], ['id' => $pi->id]);
							else
				            $this->db->update('purchase_items', ['quantity_balance' => $quantity_balance,'quantity_received' => $quantity_balance,'item_cost_value' => 0], ['id' => $pi->id]);

						} else {
                            $clause['quantity']         = 0;
                            $clause['item_tax']         = 0;
                            $clause['quantity_balance'] = $item['quantity_balance'];
                            $clause['status']           = 'received';
                            $clause['option_id']        = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : null;
                            $this->db->insert('purchase_items', $clause);
                        }
                    } elseif (!isset($item['overselling']) || empty($item['overselling'])) {
                        if ($item['inventory']) {
                            $this->db->update('purchase_items', ['quantity_balance' => $item['quantity_balance']], ['id' => $item['purchase_item_id']]);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

	    public function getPurchasedItem($clause)
    {
        $orderby = empty($this->getSystemSetting()->accounting_method) ? 'asc' : 'desc';
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        if (!isset($clause['option_id']) || empty($clause['option_id'])) {
            $this->db->group_start()->where('option_id', null)->or_where('option_id', 0)->group_end();
        }
        $q = $this->db->get_where('purchase_items', $clause);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function syncPurchaseItems_returnSales($product_id,$warehouse_id,$item_cost_value)
    {

                        $clause    = ['purchase_id' => null, 'transfer_id' => null, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'option_id' => null];
                        if ($pi = $this->getPurchasedItem($clause)) {
                            $total_item_cost_value  = $this->sma->formatDecimal($pi->item_cost_value  + $item_cost_value,12);
                            $this->db->update('purchase_items', ['item_cost_value' => $total_item_cost_value], ['id' => $pi->id]);

						}else
                    return false;
    }


	   public function syncQuantity($sale_id = null, $purchase_id = null, $oitems = null, $product_id = null)
    {
        if ($sale_id) {
            $sale_items = $this->getAllSaleItems($sale_id);
			foreach ($sale_items as $item) {
                if ($item->product_type == 'standard' || $item->product_type == 'manufacturing') {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }

				} elseif ($item->product_type == 'combo') {
                    //$wh          = $this->getSystemSetting()->overselling ? null : $item->warehouse_id;
                    $wh          = null ;
                    $combo_items = $this->getProductComboItems($item->product_id, $wh);

                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard' || $combo_item->type == 'manufacturing') {
                            $this->syncProductQty($combo_item->id, $item->warehouse_id);
                        }else if ($combo_item->type == 'combo') {
							$combo_items2 = $this->getProductComboItems($combo_item->id, $wh);

							foreach ($combo_items2 as $combo_item2) {


								if ($combo_item2->type == 'standard' || $combo_item2->type == 'manufacturing' ) {
									//return $combo_items2;

									$this->syncProductQty($combo_item2->id, $item->warehouse_id);
								}
							}
                        }
                    }
                }
				/*} elseif ($item->product_type == 'combo') {
                    $wh          = $this->getSystemSetting()->overselling ? null : $item->warehouse_id;
                    //$wh          = $item->warehouse_id;
                    $combo_items = $this->getProductComboItems($item->product_id, $wh);
                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard') {
                            $this->syncProductQty($combo_item->id, $item->warehouse_id);
                        }
                    }
                }*/
            }
        } elseif ($purchase_id) {
            $purchase_items = $this->getAllPurchaseItems($purchase_id);
            foreach ($purchase_items as $item) {
                $this->syncProductQty($item->product_id, $item->warehouse_id);
                $this->checkOverSold($item->product_id, $item->warehouse_id);
                if (isset($item->option_id) && !empty($item->option_id)) {
                    $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    $this->checkOverSold($item->product_id, $item->warehouse_id, $item->option_id);
                }
            }
        } elseif ($oitems) {
            foreach ($oitems as $item) {
                if (isset($item->product_type)) {
                    if ($item->product_type == 'standard' || $item->product_type == 'manufacturing') {
                        $this->syncProductQty($item->product_id, $item->warehouse_id);
                        if (isset($item->option_id) && !empty($item->option_id)) {
                            $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                        }
                    } elseif ($item->product_type == 'combo') {
                        $combo_items = $this->getProductComboItems($item->product_id, $item->warehouse_id);
                        foreach ($combo_items as $combo_item) {
                            if ($combo_item->type == 'standard' || $combo_item->type == 'manufacturing' ) {
                                $this->syncProductQty($combo_item->id, $item->warehouse_id);
                            }
                        }
                    }
                } else {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }
                }
            }
        } elseif ($product_id) {
            $warehouses = $this->getAllWarehouses();
            foreach ($warehouses as $warehouse) {
                $this->syncProductQty($product_id, $warehouse->id);
                $this->checkOverSold($product_id, $warehouse->id);
                /*if ($product_variants = $this->getProductVariants($product_id)) {
                    foreach ($product_variants as $pv) {
                        $this->syncVariantQty($pv->id, $warehouse->id, $product_id);
                        $this->checkOverSold($product_id, $warehouse->id, $pv->id);
                    }
                }*/
            }
        }
    }

    public function getAllWarehouses()
    {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;

            }
            return $data;
        }
        return false;
    }


	    public function syncProductQty($product_id, $warehouse_id)
    {
        $balance_qty    = $this->getBalanceQuantity($product_id);
        $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id);
		if ($this->db->update('products', ['quantity' => $balance_qty], ['id' => $product_id])) {
            if ($this->getWarehouseProducts($product_id, $warehouse_id)) {
				//return $wh_balance_qty;

                $this->db->update('warehouses_products', ['quantity' => $wh_balance_qty], ['product_id' => $product_id, 'warehouse_id' => $warehouse_id]);
            $productid=$this->getProductByID($product_id)->code;
			if($this->db->update('synch_products', ['api' => 0], ['warehouse_id' => $warehouse_id, 'product_code' => $productid])){

			}

			} else {
                if (!$wh_balance_qty) {
                    $wh_balance_qty = 0;
                }
                $product = $this->getProductByID($product_id);
                if ($product) {
                    $this->db->insert('warehouses_products', ['quantity' => $wh_balance_qty, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'avg_cost' => $product->cost]);
                } else {
                    $this->db->insert('warehouses_products', ['quantity' => $wh_balance_qty, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id]);
                }
            }
            return true;
        }
        return false;
    }

	    private function getBalanceQuantity($product_id, $warehouse_id = null)
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

	    public function getWarehouseProducts($product_id, $warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products', ['product_id' => $product_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function checkOverSold($product_id, $warehouse_id, $option_id = null)
    {
        $clause = ['purchase_id' => null, 'transfer_id' => null, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'option_id' => $option_id];
        if ($os = $this->getPurchasedItem($clause)) {
            if ($os->quantity_balance < 0) {
                if ($pis = $this->getPurchasedItems($product_id, $warehouse_id, $option_id, true)) {
                    $quantity = $os->quantity_balance;
                    foreach ($pis as $pi) {
                        if ($pi->quantity_balance >= (0 - $quantity) && $quantity != 0) {
                            $balance = $pi->quantity_balance + $quantity;
                            $this->db->update('purchase_items', ['quantity_balance' => $balance], ['id' => $pi->id]);
                            $quantity = 0;
                            break;
                        } elseif ($quantity != 0) {
                            $quantity = $quantity + $pi->quantity_balance;
                            $this->db->update('purchase_items', ['quantity_balance' => 0], ['id' => $pi->id]);
                        }
                    }
                    $this->db->update('purchase_items', ['quantity_balance' => $quantity], ['id' => $os->id]);
                }
            }
        }
    }

    public function updateReference($field,$brId = null)
    {

        if($brId)
            $q = $this->db->get_where('order_ref', ['brId' => $brId], 1);
        else
            $q = $this->db->get_where('order_ref', ['ref_id' => '1'], 1);

        if ($q->num_rows() > 0) {
            $ref = $q->row();
            if($brId)
                $this->db->update('order_ref', [$field => $ref->{$field} + 1], ['brId' => $brId]);
            else
                $this->db->update('order_ref', [$field => $ref->{$field} + 1], ['ref_id' => '1']);
            return true;
        }
        return false;
    }


    public function getAllSaleItems($sale_id)
    {
        $q = $this->db->get_where('sale_items', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getInvoiceBy_date_reference_no_biller_id($date,$reference_no,$biller_id,$Offline_Pos_id)
    {
        $q = $this->db->get_where('sales', ['date' => $date,'reference_no' => $reference_no,'Offline_Pos_id' => $Offline_Pos_id,'biller_id' => $biller_id,'pos' => 1]);
        if ($q->num_rows() > 0) {
            return $q->num_rows();
        }

        return 0;
    }

    public function getInvoiceBy_reference_no_biller_id($reference_no,$biller_id,$Offline_Pos_id)
    {
        $q = $this->db->get_where('sales', ['reference_no' => $reference_no,'Offline_Pos_id' => $Offline_Pos_id,'biller_id' => $biller_id,'pos' => 1]);
        if ($q->num_rows() > 0) {
            return $q->num_rows();
        }

        return 0;
    }


    public function getInvoiceBy_reference_no_biller_id_V2($reference_no,$biller_id,$Offline_Pos_id)
    {
        $q = $this->db->get_where('sales', ['reference_no' => $reference_no,'Offline_Pos_id' => $Offline_Pos_id,'biller_id' => $biller_id,'pos' => 1]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return 0;
    }

    public function getInvoiceBy_reference_no_biller_id_V1($date,$reference_no,$biller_id,$Offline_Pos_id)
    {
        $q = $this->db->get_where('sales', ['date' => $date,'reference_no' => $reference_no,'Offline_Pos_id' => $Offline_Pos_id,'biller_id' => $biller_id,'pos' => 1]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return 0;
    }


    public function getExpensesBy_date_reference_no($date,$reference_no,$Offline_Pos_id)
    {
        $q = $this->db->get_where('expenses', ['date' => $date,'reference' => $reference_no,'Offline_id' => $Offline_Pos_id]);
        if ($q->num_rows() > 0) {
            return $q->num_rows();
        }

        return 0;
    }

	public function getPaymentsBy_date_reference_no($date,$reference_no,$sale_id)
    {
        $q = $this->db->get_where('payments', ['date' => $date,'reference_no' => $reference_no,'sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            return $q->num_rows();
        }

        return 0;
    }

    public function getInvoicebyOffline_Pos_id($Offline_Pos_id)
    {
        $this->db->where('Offline_Pos_id',$Offline_Pos_id);
		$this->db->where('pos',1);
		$query = $this->db->get('sales');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


 public function getInvoicebyOffline_Pos_id2()
    {

		$startDate='2021-12-02';
        $endDate='2021-12-03';

	   $this->db->select("count(Offline_Pos_id) as count,reference_no,Offline_Pos_id,date");
		$this->db->where('date >=', DATE($startDate));
		$this->db->where('date <', DATE($endDate));
		$this->db->where('pos',1)
		->group_by('Offline_Pos_id');
		$query = $this->db->get('sales');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
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

	public function getPaymentTypeByPaymentType($payment_type)
    {
        $q = $this->db->get_where('gl_payment_methods', ['payment_type' => $payment_type], 1);

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getPaymentTypeById($id)
    {
        $q = $this->db->get_where('gl_payment_methods', ['id' => $id], 1);

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getAccountByRefId($account_ref_id,$account_type) {
        $q = $this->db->get_where('gl_accounts', array('account_ref_id' => $account_ref_id,'account_type' => $account_type), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   		 }

	public function getAccountsSettings()
    {
        $q = $this->db->get('gl_accounts_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference_no,$entryType,$user_id,$grpId = NULL,$coId = NULL,$brId = NULL) {
		$entryDebitAccount=json_encode((Array) $entryDebitAccount);
		$entryCreditAccount=json_encode((Array) $entryCreditAccount);
		$entryDebitBalance=json_encode((Array) $entryDebitBalance);
		$entryCreditBalance=json_encode((Array) $entryCreditBalance);
		$clause = ['reference_no' => $reference_no, 'created_by' => $user_id, 'userID' => $user_id, 'entryDescription' => 'Automated_Accounting_Entry', 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1];

        if(isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1){
            $clause  ['grpId']=$grpId ? $grpId : 1;
            $clause  ['coId']=$coId ? $coId : 1;
            $clause  ['brId']=$brId ? $brId : 1;
            $brId=$brId ? $brId : 1;
            $clause  ['entry_no']=$this->getReference('gl_enrty',$brId ? $brId  : null);//$this->getMainBranchesByID($brId)->branch_prefix ? $this->getMainBranchesByID($brId)->branch_prefix.'-'.$this->getCountEntriesBybrid($brId)->count : $this->getCountEntriesBybrid($brId)->count;
        }
		if($this->db->insert('gl_entries', $clause)){
			$entryID = $this->db->insert_id();
            if(isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1){
                $this->updateReference('gl_enrty',$brId ? $brId  : null);
                }
			$this->approve($entryID,'Automated_Accounting_Entry');
			return true;
		}
		return false;
   		 }




		 public function approve($entryID,$entryDescription){
			//$data['userData'] = $this->session->userdata();
			$entryInfo        = (array) $this->getEntries($entryID)[0];


		// 	if ($data['userData']['userRole'] < 10){
		// 		$this->session->set_flashdata('danger', 'You do not have this permission.');
		// 		redirect('entries');
		// 	}

		// elseif ($data['userData']['userRole'] !=0){
		// 		$this->session->set_flashdata('danger', 'You do not have this permission.');
		// 		redirect('entries');
		// 	}



				$entryDebitAccounts = array();
				$entryDebitBalances = 0;

				$entryCreditAccounts = array();
				$entryCreditBalances = 0;

				foreach (json_decode($entryInfo['entryDebitAccount']) as $debitAccount){
					array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entryInfo['entryDebitBalance'])[$entryDebitBalances]));
					$entryDebitBalances += 1;
				}

				foreach (json_decode($entryInfo['entryCreditAccount']) as $creditAccount){
					array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entryInfo['entryCreditBalance'])[$entryCreditBalances]));
					$entryCreditBalances += 1;
				}

				$this->approveEntry($entryID, $entryDebitAccounts, $entryCreditAccounts,$entryDescription);



			return false;
		}

		public function getEntries($entryID = NULL){

		$this->db->order_by('entryID', 'DEsc');
			if ($entryID){
			$this->db->where('entryID', $entryID);
			}


        $q = $this->db->get('gl_entries');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;



		}


		public function approveEntry($entryID, $debitAccounts, $creditAccounts,$entryDescription){
			$entryInfo        = (array) $this->getEntries($entryID)[0];

			foreach ($debitAccounts as $account){
				$balance = $account[key($account)];
				$account = key($account);

				$accountDebitInfo    = (array) $this->getAccounts($account)[0];

				$accountDebitBalance = $accountDebitInfo['accountDebit'];

				$accountInfo = array('accountDebit' => ($balance + $accountDebitBalance));
				$this->db->where('id', $account);
				$this->db->update('gl_accounts', $accountInfo);
			}

			foreach ($creditAccounts as $account){
				$balance = $account[key($account)];
				$account = key($account);

				$accountCreditInfo    = (array) $this->getAccounts($account)[0];
				$accountCreditBalance = $accountCreditInfo['accountCredit'];

				$accountInfo = array('accountCredit' => ($balance+$accountCreditBalance));
				$this->db->where('id', $account);
				$this->db->update('gl_accounts', $accountInfo);
			}

			$entryInfo = array('entryStatus' => 1,'entryDescription' =>$entryDescription);
			$this->db->where('entryID', $entryID);
			$this->db->update('gl_entries', $entryInfo);

			return true;
		}



	public function getAccounts($id = null)
    {
		if ($id){
			$this->db->where('id', $id);
			}
		$q = $this->db->get('gl_accounts');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

	public function getToatalSales($biller_id,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(net_unit_price * unit_quantity),0) as total')->select('COALESCE(sum(item_tax),0) as total_tax')->select('COALESCE(sum(item_cost_value),0) as item_cost_value')
			 ->join('sale_items', 'sale_items.sale_id = sales.id', 'left');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
			if ($start_date) {
            //$this->db->where('sales.date >', $start_date);
			    $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        if ($end_date) {
        //    $this->db->where('sales.date <=', $end_date);
        }

		$this->db->where('sales.biller_id', $biller_id);
        $this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");
		$this->db->where('sales.payment_status', 'paid');
        $this->db->where('sales.pos', '1');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getToatalSalesPayment($biller_id,$paid_by,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(amount),0) as amount')
			 ->join('payments', 'payments.sale_id = sales.id', 'left');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
			if ($start_date) {
         //   $this->db->where('sales.date >=', $start_date);
        	    $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
         }
        if ($end_date) {
         //   $this->db->where('sales.date <=', $end_date);
        }

		$this->db->where('sales.biller_id', $biller_id);
        $this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");
		$this->db->where('sales.payment_status', 'paid');
        $this->db->where('paid_by', $paid_by);
        $this->db->where('sales.pos', '1');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	public function addExpense($data = [])
    {
        if ($this->db->insert('expenses', $data)) {
            if ($this->getReference('ex',$data['brId'] ? $data['brId']  : null) == $data['reference']) {
                $this->updateReference('ex',$data['brId'] ? $data['brId']  : null);
            }
            return true;
        }
        return false;
    }


	    public function getReference($field,$brId = null)
    {
        if($brId && (isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1))
            $q = $this->db->get_where('order_ref', ['brId' => $brId], 1);
        else
            $q = $this->db->get_where('order_ref', ['ref_id' => '1'], 1);

        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'so':
                    $prefix = $this->getSystemSetting()->sales_prefix;
                    break;
                case 'pos':
                    $prefix = isset($this->getSystemSetting()->sales_prefix) ? $this->getSystemSetting()->sales_prefix . '/POS' : '';
                    break;
                case 'qu':
                    $prefix = $this->getSystemSetting()->quote_prefix;
                    break;
                case 'po':
                    $prefix = $this->getSystemSetting()->purchase_prefix;
                    break;
                case 'to':
                    $prefix = $this->getSystemSetting()->transfer_prefix;
                    break;
                case 'do':
                    $prefix = $this->getSystemSetting()->delivery_prefix;
                    break;
                case 'pay':
                    $prefix = $this->getSystemSetting()->payment_prefix;
                    break;
                case 'ppay':
                    $prefix = $this->getSystemSetting()->ppayment_prefix;
                    break;
                case 'ex':
                    $prefix = $this->getSystemSetting()->expense_prefix;
                    break;
                case 're':
                    $prefix = $this->getSystemSetting()->return_prefix;
                    break;
                case 'rep':
                    $prefix = $this->getSystemSetting()->returnp_prefix;
                    break;
                case 'qa':
                    $prefix = $this->getSystemSetting()->qa_prefix;
                    break;
                default:
                    $prefix = '';
            }

            if($brId){
                if($brId && (isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1) && $field == 'gl_enrty'){
                    $ref_no = $this->getMainBranchesByID($brId)->branch_prefix ? $this->getMainBranchesByID($brId)->branch_prefix.'-'.$prefix : $prefix;
                }else{
                    $ref_no = $this->getMainBranchesByID($brId)->branch_prefix ? $this->getMainBranchesByID($brId)->branch_prefix.'/'.$prefix : $prefix;
                }
            }else
               $ref_no = $prefix;

               if($brId && (isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1) && $field == 'gl_enrty'){
                return $ref_no.''.$ref->{$field};
               }
            //$ref_no = $prefix;

            if ($this->getSystemSetting()->reference_format == 1) {
                $ref_no .= date('Y') . '/' . sprintf('%04s', $ref->{$field});
            } elseif ($this->getSystemSetting()->reference_format == 2) {
                $ref_no .= date('Y') . '/' . date('m') . '/' . sprintf('%04s', $ref->{$field});
            } elseif ($this->getSystemSetting()->reference_format == 3) {
                $ref_no .= sprintf('%04s', $ref->{$field});
            } else {
                $ref_no .= $this->getRandomReference();
            }

            return $ref_no;
        }
        return false;
    }

	    public function getRandomReference($len = 12)
    {
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= mt_rand(0, 9);
        }

        if ($this->getSaleByReference($result)) {
            $this->getRandomReference();
        }

        return $result;
    }


	    public function getSaleByReference($ref)
    {
        $this->db->like('reference_no', $ref, 'both');
        $q = $this->db->get('sales', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCompanyByOfflineIDV2($Offline_id,$company)
    {
        $q = $this->db->get_where('companies', ['Offline_id' => $Offline_id, 'company' => $company, 'group_name' => 'customer'], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getCompanyByidIDV2($id,$company)
    {
        $q = $this->db->get_where('companies', ['id' => $id, 'company' => $company], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }


	public function getCompanyByOfflineID($Offline_id)
    {
        $q = $this->db->get_where('companies', ['Offline_id' => $Offline_id, 'group_name' => 'customer'], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

	public function syncSalePayments($id)
    {
        $sale = $this->getSaleByID($id);
        if ($payments = $this->getSalePayments($id)) {
            $paid        = 0;
            $discount_allowance        = 0;
            $grand_total = $sale->grand_total + $sale->rounding;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
                $discount_allowance += $payment->commercial_discount;
            }

            if($discount_allowance==0 && $sale->discount_allowance > 0){
                $discount_allowance=$sale->discount_allowance;
            }

            $payment_status = $paid == 0 ? 'pending' : $sale->payment_status;
            if ($this->sma->formatDecimal($grand_total) == 0 || $this->sma->formatDecimal($grand_total) == $this->sma->formatDecimal($paid + $discount_allowance)) {
                $payment_status = 'paid';
            } elseif ($sale->due_date <= date('Y-m-d') && !$sale->sale_id) {
                $payment_status = 'due';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }

            if ($this->db->update('sales', ['paid' => $paid, 'discount_allowance' => $discount_allowance, 'payment_status' => $payment_status], ['id' => $id])) {
                return true;
            }
        } else {
            $payment_status = ($sale->due_date <= date('Y-m-d')) ? 'due' : 'pending';
            if ($this->db->update('sales', ['paid' => 0, 'payment_status' => $payment_status], ['id' => $id])) {
                return true;
            }
        }

        return false;
    }


	    public function getSaleByID($id)
    {
        $q = $this->db->get_where('sales', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSaleItemTailor($sale_id,$client_type,$product_name,$product_code)
    {
        $q = $this->db->get_where('sale_items', ['sale_id' => $sale_id,'client_type' => $client_type,'product_name' => $product_name,'product_code' => $product_code]);
        if ($q->num_rows() > 0) {
            return $q->row();
       }
       return false;
    }

	    public function getSalePayments($sale_id)
    {
        $q = $this->db->get_where('payments', ['sale_id' => $sale_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getPosRegisterBy_date_closed_at_user_id_Offline_id($date,$closed_at,$user_id,$Offline_id)
    {
        $q = $this->db->get_where('pos_register', ['date' => $date,'closed_at' => $closed_at,'user_id' => $user_id,'Offline_id' => $Offline_id,'status' => 'close']);
        if ($q->num_rows() > 0) {
            return $q->num_rows();
        }

        return 0;
    }

	 public function closePOSRegister($data_open,$data_close,$dbname = null)
    {

        if ($this->db->insert('pos_register', $data_open)) {

			$rid = $this->db->insert_id();

			/*if ($this->getSystemSetting()->accounting && $dbname=="accountstest"){
			$entry_type="Open_biller_register";
			$entry_type1="Close_biller_register";

			$entryDebitAccount= [];



						$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_open['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;

					$entryCreditAccount= [];

					//$entryCreditAccount[]=$this->getPaymentTypeByPaymentType('cash')->payment_account;
					$entryCreditAccount[]=$this->getAccountsSettings()->accounts_main_boxes_acct;


			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data_open['cash_in_hand']),$this->sma->formatDecimal($data_open['cash_in_hand']),$rid,$entry_type,$data_open['user_id']);
				if($data_open['cash_in_hand_clearance']==1)
				$this->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$this->sma->formatDecimal($data_open['cash_in_hand']),$this->sma->formatDecimal($data_open['cash_in_hand']),$rid,$entry_type1,$data_open['user_id']);

			}*/


            //return true;

		//$register=$this->registerData($user_id);

		//if (!$user_id) {
            $user_id = $data_open['user_id'];//->session->userdata('user_id');
        //}
        if ($data_close['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', ['created_by' => $user_id]);
        } elseif ($data_close['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', ['created_by' => $data_close['transfer_opened_bills']], ['created_by' => $user_id]);
        }
        if ($this->db->update('pos_register', $data_close, ['id' => $rid, 'user_id' => $user_id])) {

			if ($this->getSystemSetting()->accountingOffline)// || $dbname ="accountstest" || $dbname ="buthaina")
			{
			$entry_type="Close_biller_register";



			$sales=$this->getToatalSalesPOS($this->getUser($data_close['user_id'])->biller_id,'completed',$data_open['date'],$data_close['closed_at']);
			$re_sales=$this->getToatalSalesPOS($this->getUser($data_close['user_id'])->biller_id,'returned',$data_open['date'],$data_close['closed_at']);

			$entryDebitAccount= [];
			$entryDebitBalance= [];

			$entryCreditAccount= [];
			$entryCreditBalance= [];

			$customersAcc= [];
			$customersBal= [];



			$paymentcash=$this->getToatalSalesPaymentPOS($this->getUser($data_close['user_id'])->biller_id,'completed','cash',$data_open['date'],$data_close['closed_at']);
			$paymentCC=$this->getToatalSalesPaymentPOSCC($this->getUser($data_close['user_id'])->biller_id,'completed','CC',$data_open['date'],$data_close['closed_at']);

			$re_paymentcash=$this->getToatalSalesPaymentPOS($this->getUser($data_close['user_id'])->biller_id,'returned','cash',$data_open['date'],$data_close['closed_at']);
			$re_paymentCC=$this->getToatalSalesPaymentPOSCC($this->getUser($data_close['user_id'])->biller_id,'returned','CC',$data_open['date'],$data_close['closed_at']);



			if($paymentcash->amount!=0){

			$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc==""){
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

						}
					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);

			}

			foreach ($paymentCC as $paycc) {

			if($paycc->amount!=0){

				if($paycc->gl_payment_method_id==0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
				}else{
					$entryDebitAccount[]=$this->getPaymentTypeById($paycc->gl_payment_method_id)->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
				}
			}

			}






			$total=0;
			$total_tax=0;
			$shipping=0;
			foreach ($sales as $sale) {
				$entryDebitAccountCustomer="";
				if($this->getCompanyByID($sale->customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->getAccountByRefId($sale->customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->getCompanyByID($sale->customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->getAccountByRefId($sale->customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_international_customers_acct;


				}
				if($sale->balance!=0){
			$entryDebitAccount[] =$entryDebitAccountCustomer;
			$entryDebitBalance[] =$sale->balance;
				}

			$total += $sale->total;
            $total_tax += $sale->total_tax;
            $shipping += $sale->shipping;
            }



			$entryDebitAccountSales=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'sales')->id;
						if($entryDebitAccountSales=="")
					$entryDebitAccountSales =  $this->getAccountsSettings()->sales_acct;

					$entryCreditAccount[] =  $entryDebitAccountSales;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;
					if(abs($shipping)>0)
					$entryCreditAccount[] =  $this->getAccountsSettings()->accounts_shipping_other_revenues;


					$entryCreditBalance[] =  $this->sma->formatDecimal($total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($total_tax);
					if(abs($shipping)>0)
					$entryCreditBalance[] =  $this->sma->formatDecimal($shipping);






				if($re_paymentcash->amount!=0){

			$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
						//if($entrybillerAcc=="")
						//$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

							if($entrybillerAcc==""){
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

						}


					$entryCreditAccount[] =  $entrybillerAcc;
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($re_paymentcash->amount));

			}
			/*if($re_paymentCC->amount!=0){
					$entryCreditAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($re_paymentCC->amount));

					}
					*/

			foreach ($re_paymentCC as $paycc) {

			if($paycc->amount!=0){

				if($paycc->gl_payment_method_id==0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
				}else{
					$entryDebitAccount[]=$this->getPaymentTypeById($paycc->gl_payment_method_id)->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
				}
			}

			}



			$re_total=0;
			$re_total_tax=0;
			$re_shipping=0;
			foreach ($re_sales as $re_sale) {
				$entryDebitAccountCustomer="";
				if($this->getCompanyByID($re_sale->customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->getAccountByRefId($re_sale->customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->getCompanyByID($re_sale->customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->getAccountByRefId($re_sale->customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_international_customers_acct;


				}
				if(abs($re_sale->balance)!=0){
			$entryCreditAccount[] =$entryDebitAccountCustomer;
			$entryCreditBalance[] =abs($re_sale->balance);
				}

			$re_total += abs($re_sale->total);
            $re_total_tax += abs($re_sale->total_tax);
            $re_shipping += abs($re_sale->shipping);
            }





				$entryDebitAccountSales=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'return_sales')->id;
						if($entryDebitAccountSales=="")
					$entryDebitAccountSales =  $this->getAccountsSettings()->sales_return_acct;

					if(abs($re_total)>0)
					$entryDebitAccount[] =  $entryDebitAccountSales;
					if(abs($re_total_tax)>0)
					$entryDebitAccount[] =  $this->getAccountsSettings()->sales_vat_acct;
					if(abs($re_shipping)>0)
					$entryDebitAccount[] =  $this->getAccountsSettings()->accounts_shipping_other_revenues;


					if(abs($re_total)>0)
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_total));
					if(abs($re_total_tax)>0)
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_total_tax));
					if(abs($re_shipping)>0)
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_shipping));



				/*

				$expenses=$this->getToatalSalesExpensePOS($data_close['user_id'],$data_open['date'],$data_close['closed_at']);



			if($expenses->grand_total!=0){


					$entryDebitAccount[] =  $this->getAccountsSettings()->accounts_marketing_expenses_acct;
					$entryDebitBalance[] =  $this->sma->formatDecimal($expenses->grand_total);

					$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryCreditAccount[] =  $entrybillerAcc;
					$entryCreditBalance[] =  $this->sma->formatDecimal($expenses->grand_total);

			}
			*/




				$this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close['user_id']);


				   $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;

				$good_sold=$this->getToatalGoodSold($this->getUser($data_close['user_id'])->biller_id,'completed',$data_open['date'],$data_close['closed_at']);

				if($this->getSystemSetting()->accounting_type=="periodic" || $good_sold->item_cost_value==0){

                }else{
				$this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($good_sold->item_cost_value),$this->sma->formatDecimal($good_sold->item_cost_value),$rid,$entry_type,$data_close['user_id']);
                 }
				$re_good_sold=$this->getToatalGoodSold($this->getUser($data_close['user_id'])->biller_id,'returned',$data_open['date'],$data_close['closed_at']);

				if($this->getSystemSetting()->accounting_type=="periodic" || abs($re_good_sold->item_cost_value)==0){

                }else{
				$this->createEntryMultiSide($entryWarehouseAcc,$this->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$rid,$entry_type,$data_close['user_id']);
                }


                if($this->getSystemSetting()->isDonation==1 && $data_close['donation']!='0'){
                    $entry_type="Donations_due_to_charities";
                    //$entryDescription="Donations_Payments_Sales_Invoice_No#".$sale_id;


                    $entryDebitAccount= [];
                    $entryDebitBalance= [];


                    $entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
                    if($entrybillerAcc==""){
                    $entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;
                    $entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
                    $entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
                    if($entrybillerAcc=="")
                    $entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

                    }
                $entryDebitAccount[] =  $entrybillerAcc;
                $entryDebitBalance[] =  $this->sma->formatDecimal($data_close['donation']);



                $entryCreditAccount= [];
                $entryCreditBalance= [];

                $entryCrediAcc=$this->getAccountsSettings()->donations_acct;
                    if($entryCrediAcc=="")
                    $entryCrediAcc=$this->getAccountsSettings()->expenses_shipping_acct;
                     $entryCreditAccount[] =  $entryCrediAcc;


                $entryCreditBalance[] =  $this->sma->formatDecimal($data_close['donation']);


                $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close['user_id']);




                }







			/*$paymentcash=$this->getToatalSalesPayment($this->getUser($data_close['user_id'])->biller_id,'cash',$data_open['date'],$data_close['closed_at']);
			$paymentCC=$this->getToatalSalesPayment($this->getUser($data_close['user_id'])->biller_id,'CC',$data_open['date'],$data_close['closed_at']);
			$paymentbank_transfer=$this->getToatalSalesPayment($this->getUser($data_close['user_id'])->biller_id,'bank_transfer',$data_open['date'],$data_close['closed_at']);
			$paymentCheque=$this->getToatalSalesPayment($this->getUser($data_close['user_id'])->biller_id,'Cheque',$data_open['date'],$data_close['closed_at']);



					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);

					if($paymentbank_transfer->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('bank_transfer')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentbank_transfer->amount);

					}
					if($paymentCheque->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('Cheque')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCheque->amount);

					}

					if($paymentCC->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCC->amount);

					}


					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_acct;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total_tax);



			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$register->id,$entry_type,$data_close['user_id']);

				       $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;


					$this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($sales->item_cost_value),$this->sma->formatDecimal($sales->item_cost_value),$register->id,$entry_type,$data_close['user_id']);
				*/

			}

            return true;
        }


        }
        return false;














		/*

		$register=$this->registerData($user_id);

		if (!$rid) {
            $rid = $this->session->userdata('register_id');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($data['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', ['created_by' => $user_id]);
        } elseif ($data['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', ['created_by' => $data['transfer_opened_bills']], ['created_by' => $user_id]);
        }
        if ($this->db->update('pos_register', $data, ['id' => $rid, 'user_id' => $user_id])) {

			/*if ($this->getSystemSetting()->accounting){
			$entry_type="Close_biller_register";



			$sales=$this->getToatalSales($this->getUser($data['user_id'])->biller_id,$register->date,$data['closed_at']);
			$paymentcash=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'cash',$register->date,$data['closed_at']);
			$paymentCC=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'CC',$register->date,$data['closed_at']);
			$paymentbank_transfer=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'bank_transfer',$register->date,$data['closed_at']);
			$paymentCheque=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'Cheque',$register->date,$data['closed_at']);



					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$entrybillerAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);

					if($paymentbank_transfer->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('bank_transfer')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentbank_transfer->amount);

					}
					if($paymentCheque->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('Cheque')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCheque->amount);

					}

					if($paymentCC->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCC->amount);

					}


					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_acct;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total_tax);



			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$register->id,$entry_type,$data['user_id']);

				       $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;


					$this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($sales->item_cost_value),$this->sma->formatDecimal($sales->item_cost_value),$register->id,$entry_type,$data['user_id']);

			}

            return true;
        }
        return false;
		*/

    }


	 public function closePOSRegisterTailor($data_open,$data_close,$dbname = null)
    {


			//$rid = $this->db->insert_id();

			/*if ($this->getSystemSetting()->accounting && $dbname=="accountstest"){
			$entry_type="Open_biller_register";
			$entry_type1="Close_biller_register";

			$entryDebitAccount= [];



						$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_open['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;

					$entryCreditAccount= [];

					//$entryCreditAccount[]=$this->getPaymentTypeByPaymentType('cash')->payment_account;
					$entryCreditAccount[]=$this->getAccountsSettings()->accounts_main_boxes_acct;


			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data_open['cash_in_hand']),$this->sma->formatDecimal($data_open['cash_in_hand']),$rid,$entry_type,$data_open['user_id']);
				if($data_open['cash_in_hand_clearance']==1)
				$this->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$this->sma->formatDecimal($data_open['cash_in_hand']),$this->sma->formatDecimal($data_open['cash_in_hand']),$rid,$entry_type1,$data_open['user_id']);

			}*/


            //return true;

		//$register=$this->registerData($user_id);

		//if (!$user_id) {
            $rid = $data_open['id'];//->session->userdata('user_id');
            $user_id = $data_open['user_id'];//->session->userdata('user_id');
        //}
        if ($data_close['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', ['created_by' => $user_id]);
        } elseif ($data_close['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', ['created_by' => $data_close['transfer_opened_bills']], ['created_by' => $user_id]);
        }
        if ($this->db->update('pos_register', $data_close, ['id' => $rid, 'user_id' => $user_id])) {

			if ($this->getSystemSetting()->accountingOffline)// || $dbname ="accountstest" || $dbname ="buthaina")
			{
			$entry_type="Close_biller_register";



			$sales=$this->getToatalSalesPOS($this->getUser($data_close['user_id'])->biller_id,'completed',$data_open['date'],$data_close['closed_at']);
			$re_sales=$this->getToatalSalesPOS($this->getUser($data_close['user_id'])->biller_id,'returned',$data_open['date'],$data_close['closed_at']);

			$entryDebitAccount= [];
			$entryDebitBalance= [];

			$entryCreditAccount= [];
			$entryCreditBalance= [];

			$customersAcc= [];
			$customersBal= [];



			$paymentcash=$this->getToatalSalesPaymentPOS($this->getUser($data_close['user_id'])->biller_id,'completed','cash',$data_open['date'],$data_close['closed_at']);
			$paymentCC=$this->getToatalSalesPaymentPOSCC($this->getUser($data_close['user_id'])->biller_id,'completed','CC',$data_open['date'],$data_close['closed_at']);

			$re_paymentcash=$this->getToatalSalesPaymentPOS($this->getUser($data_close['user_id'])->biller_id,'returned','cash',$data_open['date'],$data_close['closed_at']);
			$re_paymentCC=$this->getToatalSalesPaymentPOSCC($this->getUser($data_close['user_id'])->biller_id,'returned','CC',$data_open['date'],$data_close['closed_at']);



			if($paymentcash->amount!=0){

			$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc==""){
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

						}
					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);

			}

			foreach ($paymentCC as $paycc) {

			if($paycc->amount!=0){

				if($paycc->gl_payment_method_id==0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
				}else{
					$entryDebitAccount[]=$this->getPaymentTypeById($paycc->gl_payment_method_id)->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
				}
			}

			}






			$total=0;
			$total_tax=0;
			$shipping=0;
			foreach ($sales as $sale) {
				$entryDebitAccountCustomer="";
				if($this->getCompanyByID($sale->customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->getAccountByRefId($sale->customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->getCompanyByID($sale->customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->getAccountByRefId($sale->customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_international_customers_acct;


				}
				if($sale->balance!=0){
			$entryDebitAccount[] =$entryDebitAccountCustomer;
			$entryDebitBalance[] =$sale->balance;
				}

			$total += $sale->total;
            $total_tax += $sale->total_tax;
            $shipping += $sale->shipping;
            }



			$entryDebitAccountSales=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'sales')->id;
						if($entryDebitAccountSales=="")
					$entryDebitAccountSales =  $this->getAccountsSettings()->sales_acct;

					$entryCreditAccount[] =  $entryDebitAccountSales;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;
					if(abs($shipping)>0)
					$entryCreditAccount[] =  $this->getAccountsSettings()->accounts_shipping_other_revenues;


					$entryCreditBalance[] =  $this->sma->formatDecimal($total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($total_tax);
					if(abs($shipping)>0)
					$entryCreditBalance[] =  $this->sma->formatDecimal($shipping);






				if($re_paymentcash->amount!=0){

			$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
						//if($entrybillerAcc=="")
						//$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

							if($entrybillerAcc==""){
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

						}


					$entryCreditAccount[] =  $entrybillerAcc;
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($re_paymentcash->amount));

			}
			/*if($re_paymentCC->amount!=0){
					$entryCreditAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($re_paymentCC->amount));

					}
					*/

			foreach ($re_paymentCC as $paycc) {

			if($paycc->amount!=0){

				if($paycc->gl_payment_method_id==0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
				}else{
					$entryDebitAccount[]=$this->getPaymentTypeById($paycc->gl_payment_method_id)->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
				}
			}

			}



			$re_total=0;
			$re_total_tax=0;
			$re_shipping=0;
			foreach ($re_sales as $re_sale) {
				$entryDebitAccountCustomer="";
				if($this->getCompanyByID($re_sale->customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->getAccountByRefId($re_sale->customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->getCompanyByID($re_sale->customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->getAccountByRefId($re_sale->customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_international_customers_acct;


				}
				if(abs($re_sale->balance)!=0){
			$entryCreditAccount[] =$entryDebitAccountCustomer;
			$entryCreditAccount[] =abs($re_sale->balance);
				}

			$re_total += abs($re_sale->total);
            $re_total_tax += abs($re_sale->total_tax);
            $re_shipping += abs($re_sale->shipping);
            }





				$entryDebitAccountSales=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'return_sales')->id;
						if($entryDebitAccountSales=="")
					$entryDebitAccountSales =  $this->getAccountsSettings()->sales_return_acct;

					if(abs($re_total)>0)
					$entryDebitAccount[] =  $entryDebitAccountSales;
					if(abs($re_total_tax)>0)
					$entryDebitAccount[] =  $this->getAccountsSettings()->sales_vat_acct;
					if(abs($re_shipping)>0)
					$entryDebitAccount[] =  $this->getAccountsSettings()->accounts_shipping_other_revenues;


					if(abs($re_total)>0)
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_total));
					if(abs($re_total_tax)>0)
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_total_tax));
					if(abs($re_shipping)>0)
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_shipping));



				/*

				$expenses=$this->getToatalSalesExpensePOS($data_close['user_id'],$data_open['date'],$data_close['closed_at']);



			if($expenses->grand_total!=0){


					$entryDebitAccount[] =  $this->getAccountsSettings()->accounts_marketing_expenses_acct;
					$entryDebitBalance[] =  $this->sma->formatDecimal($expenses->grand_total);

					$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryCreditAccount[] =  $entrybillerAcc;
					$entryCreditBalance[] =  $this->sma->formatDecimal($expenses->grand_total);

			}
			*/




				$this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close['user_id']);


				   $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;

				$good_sold=$this->getToatalGoodSold($this->getUser($data_close['user_id'])->biller_id,'completed',$data_open['date'],$data_close['closed_at']);

				if($this->getSystemSetting()->accounting_type=="periodic" || $good_sold->item_cost_value==0){

                }else{
				$this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($good_sold->item_cost_value),$this->sma->formatDecimal($good_sold->item_cost_value),$rid,$entry_type,$data_close['user_id']);
                }
				$re_good_sold=$this->getToatalGoodSold($this->getUser($data_close['user_id'])->biller_id,'returned',$data_open['date'],$data_close['closed_at']);

				if($this->getSystemSetting()->accounting_type=="periodic" || abs($re_good_sold->item_cost_value)==0){

                }else{
				$this->createEntryMultiSide($entryWarehouseAcc,$this->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$rid,$entry_type,$data_close['user_id']);
                }







			/*$paymentcash=$this->getToatalSalesPayment($this->getUser($data_close['user_id'])->biller_id,'cash',$data_open['date'],$data_close['closed_at']);
			$paymentCC=$this->getToatalSalesPayment($this->getUser($data_close['user_id'])->biller_id,'CC',$data_open['date'],$data_close['closed_at']);
			$paymentbank_transfer=$this->getToatalSalesPayment($this->getUser($data_close['user_id'])->biller_id,'bank_transfer',$data_open['date'],$data_close['closed_at']);
			$paymentCheque=$this->getToatalSalesPayment($this->getUser($data_close['user_id'])->biller_id,'Cheque',$data_open['date'],$data_close['closed_at']);



					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);

					if($paymentbank_transfer->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('bank_transfer')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentbank_transfer->amount);

					}
					if($paymentCheque->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('Cheque')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCheque->amount);

					}

					if($paymentCC->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCC->amount);

					}


					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_acct;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total_tax);



			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$register->id,$entry_type,$data_close['user_id']);

				       $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;


					$this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($sales->item_cost_value),$this->sma->formatDecimal($sales->item_cost_value),$register->id,$entry_type,$data_close['user_id']);
				*/

			}

            return true;
        }



        return false;














		/*

		$register=$this->registerData($user_id);

		if (!$rid) {
            $rid = $this->session->userdata('register_id');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($data['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', ['created_by' => $user_id]);
        } elseif ($data['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', ['created_by' => $data['transfer_opened_bills']], ['created_by' => $user_id]);
        }
        if ($this->db->update('pos_register', $data, ['id' => $rid, 'user_id' => $user_id])) {

			/*if ($this->getSystemSetting()->accounting){
			$entry_type="Close_biller_register";



			$sales=$this->getToatalSales($this->getUser($data['user_id'])->biller_id,$register->date,$data['closed_at']);
			$paymentcash=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'cash',$register->date,$data['closed_at']);
			$paymentCC=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'CC',$register->date,$data['closed_at']);
			$paymentbank_transfer=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'bank_transfer',$register->date,$data['closed_at']);
			$paymentCheque=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'Cheque',$register->date,$data['closed_at']);



					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$entrybillerAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->biller_id,'billers')->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);

					if($paymentbank_transfer->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('bank_transfer')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentbank_transfer->amount);

					}
					if($paymentCheque->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('Cheque')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCheque->amount);

					}

					if($paymentCC->amount!=0){
					$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentCC->amount);

					}


					$entryCreditAccount= [];

					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_acct;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total_tax);



			    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$register->id,$entry_type,$data['user_id']);

				       $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;


					$this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($sales->item_cost_value),$this->sma->formatDecimal($sales->item_cost_value),$register->id,$entry_type,$data['user_id']);

			}

            return true;
        }
        return false;
		*/

    }

 public function getToatalSalesPOS($biller_id,$sale_status,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(total-total_discount),0) as total')->select('COALESCE(sum(total_tax),0) as total_tax')->select('COALESCE(sum(shipping),0) as shipping')->select('COALESCE(sum(grand_total-paid),0) as balance')->select('COALESCE(customer_id) as customer_id');
			 //->join('sale_items', 'sale_items.sale_id = sales.id', 'left');
			$this->db->where('biller_id', $biller_id);
			$this->db->where('sale_status', $sale_status);
			$this->db->where('sales.pos', '1');


			//$this->db->where('sales.paid !=', 0);
			if ($start_date && $end_date) {
               $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
			$this->db->group_by('customer_id');


		//$this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");
		//$this->db->where('sales.payment_status', 'paid');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
		}
        return false;
    }

	public function getToatalSalesPOSTailor($biller_id,$sale_status,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(count(*),0) as invoice_count')->select('COALESCE(sum(total-total_discount),0) as total')->select('COALESCE(sum(total_tax),0) as total_tax')->select('COALESCE(sum(grand_total),0) as grand_total')->select('COALESCE(sum(shipping),0) as shipping')->select('COALESCE(sum(grand_total-paid),0) as balance');
			 //->join('sale_items', 'sale_items.sale_id = sales.id', 'left');
			$this->db->where('biller_id', $biller_id);
			$this->db->where('sale_status', $sale_status);
			$this->db->where('sales.pos', '1');


			//$this->db->where('sales.paid !=', 0);
			if ($start_date && $end_date) {
               $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
			//$this->db->group_by('customer_id');


		//$this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");
		//$this->db->where('sales.payment_status', 'paid');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
		}
        return false;
    }

	public function getToatalSalesPaymentPOS($biller_id,$sale_status,$paid_by,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(amount),0) as amount')
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
        $this->db->where('sales.pos', '1');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getToatalSalesPaymentPOSTailor($biller_id,$paid_by,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(amount),0) as amount')
			 ->join('payments', 'payments.sale_id = sales.id', 'left');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
		if ($start_date && $end_date) {
             //   $this->db->where('sales.date >=', $start_date);
        	    $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
         }

		$this->db->where('sales.biller_id', $biller_id);
        //$this->db->where('sales.sale_status', $sale_status);
        $this->db->where('paid_by', $paid_by);
        $this->db->where('sales.pos', '1');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getToatalSalesPaymentPOSCC($biller_id,$sale_status,$paid_by,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(amount),0) as amount,gl_payment_method_id')
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
        ->group_by('payments.gl_payment_method_id');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
		}
        return false;
    }

	public function getToatalSalesExpensePOS($created_by,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(grand_total),0) as grand_total');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
		if ($start_date && $end_date) {
             //   $this->db->where('sales.date >=', $start_date);
        	    $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
         }

		$this->db->where('created_by', $created_by);
        $this->db->where('Offline_id!=', '');

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	public function getToatalGoodSold($biller_id,$sale_status,$start_date = null, $end_date = null)
    {

		   $this->db->select('COALESCE(sum(item_cost_value),0) as item_cost_value')
			 ->join('sale_items', 'sale_items.sale_id = sales.id', 'left');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
			if ($start_date && $end_date) {
            //$this->db->where('sales.date >', $start_date);
			    $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

		$this->db->where('sales.biller_id', $biller_id);
        $this->db->where('sales.sale_status', $sale_status);
        $this->db->where('sales.pos', '1');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function setPurchaseItem_return($clause, $qty)
    {

        if ($product = $this->getProductByID($clause['product_id'])) {
            if ($pi = $this->getPurchasedItem($clause)) {
                if ($pi->quantity_balance > 0) {
                    $quantity_balance = $pi->quantity_balance + $qty;
                    log_message('error', 'More than zero: ' . $quantity_balance . ' = ' . $pi->quantity_balance . ' + ' . $qty . ' PI: ' . print_r($pi, true));
                } else {
                    $quantity_balance = $pi->quantity_balance + $qty;
                    log_message('error', 'Less than zero: ' . $quantity_balance . ' = ' . $pi->quantity_balance . ' + ' . $qty . ' PI: ' . print_r($pi, true));
                }
				if($quantity_balance>0)
                return $this->db->update('purchase_items', ['quantity_balance' => $quantity_balance,'quantity_received' => $quantity_balance], ['id' => $pi->id]);
                else
				return $this->db->update('purchase_items', ['quantity_balance' => $quantity_balance,'quantity_received' => $quantity_balance,'item_cost_value' => 0], ['id' => $pi->id]);
            }
            $unit                        = $this->getUnitByID($product->unit);
            $clause['product_unit_id']   = $product->unit;
            $clause['product_unit_code'] = $unit->code;
            $clause['product_code']      = $product->code;
            $clause['product_name']      = $product->name;
            $clause['purchase_id']       = $clause['transfer_id']       = $clause['item_tax']       = null;
            $clause['net_unit_cost']     = $clause['real_unit_cost']     = $clause['unit_cost']     = $product->cost;
            $clause['quantity_balance']  = $clause['quantity']  = $clause['unit_quantity']  = $clause['quantity_received']  = $qty;
            $clause['subtotal']          = ($product->cost * $qty);
            if (isset($product->tax_rate) && $product->tax_rate != 0) {
                $tax_details           = $this->getTaxRateByID($product->tax_rate);
                $ctax                  = $this->calculateTax($product, $tax_details, $product->cost);
                $item_tax              = $clause['item_tax']              = $ctax['amount'];
                $tax                   = $clause['tax']                   = $ctax['tax'];
                $clause['tax_rate_id'] = $tax_details->id;
                if ($product->tax_method != 1) {
                    $clause['net_unit_cost'] = $product->cost - $item_tax;
                    $clause['unit_cost']     = $product->cost;
                } else {
                    $clause['net_unit_cost'] = $product->cost;
                    $clause['unit_cost']     = $product->cost + $item_tax;
                }
                $pr_item_tax = $this->sma->formatDecimal($item_tax * $clause['unit_quantity'], 12);
                if ($this->getSystemSetting()->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->getSystemSetting()->state == $supplier_details->state), $tax_details)) {
                    $clause['gst']  = $gst_data['gst'];
                    $clause['cgst'] = $gst_data['cgst'];
                    $clause['sgst'] = $gst_data['sgst'];
                    $clause['igst'] = $gst_data['igst'];
                }
                $clause['subtotal'] = (($clause['net_unit_cost'] * $clause['unit_quantity']) + $pr_item_tax);
            }
            $clause['status']    = 'received';
            $clause['date']      = date('Y-m-d');
            $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : null;
            log_message('error', 'Why else: ' . print_r($clause, true));
            return $this->db->insert('purchase_items', $clause);
        }
        return false;
    }

	public function addAjaxProductFromSales($data)
    {
        if($data['item_no']){
            if($this->getCountProductsByItemNo($data['item_no'])->count!=0){
                return false;
            }
        }

        if ($this->db->insert('products', $data)) {
            $product_id = $this->db->insert_id();
			  $this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $data['warehouse'], 'quantity' => 0, 'avg_cost' => 0]);
			return true;
        }
        return false;
    }

	public function getAccountByID($id)
    {
		$q = $this->db->get_where('gl_accounts', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getAccountByAccountID($accountID)
    {
		$q = $this->db->get_where('gl_accounts', ['accountID' => $accountID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getMaxOffline_Pos_idBywarehouse_id($warehouse_id)
    {
		$this->db->select("COALESCE(max(Offline_Pos_id),0) as Offline_Pos_id");

		$q = $this->db->get_where('sales', ['warehouse_id' => $warehouse_id, 'pos' => '1'], 1);
        if ($q->num_rows() > 0) {
            return $q->row()->Offline_Pos_id;
        }
        return false;
    }

    public function getlogo_image_url()
    {
        $uploads_url = base_url('assets/uploads/logos/');
        //$this->db->select("id, code, name, address, email, phone, CONCAT('{$uploads_url}', map) as map_url, price_group_id");
		$this->db->select("CONCAT('{$uploads_url}', logo) as logo_image_url");
		$this->db->where('api', 0);

       // $this->db->select("COALESCE(max(Offline_Pos_id),0) as Offline_Pos_id");

		$q = $this->db->get('settings', 1);
        if ($q->num_rows() > 0) {
            return $q->row()->logo_image_url;
        }
        return false;
    }


	public function getGiftCardByNO($no)
    {
        $q = $this->db->get_where('gift_cards', ['card_no' => $no], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function updateStatus($id, $status, $supply_date, $note)
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
            $cost = $this->costing($items_array);
        }
        if ($status != 'completed' && $sale->sale_status == 'completed') {
            $this->resetSaleActions($id);
        }

        if ($this->db->update('sales', ['sale_status' => $status, 'supply_date' => $supply_date, 'note' => $note], ['id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {
            if ($status == 'completed' && $sale->sale_status != 'completed') {
                foreach ($items as $item) {
                    $item = (array) $item;
                    if ($this->getProductByID($item['product_id'])) {
                        $item_costs = $this->item_costing($item);
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
                $this->syncPurchaseItems($cost);
            }
            $this->syncQuantity($id);







        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (UpdataStatus:Sales_model.php)');
        } else {
            return true;
        }
        return false;
    }


    public function updateStatusV2($id, $status, $supply_date, $note, $date=null)
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
            $cost = $this->costing($items_array);
        }
        if ($status != 'completed' && $sale->sale_status == 'completed') {
            $this->resetSaleActions($id);
        }

        if ($this->db->update('sales', ['sale_status' => $status, 'supply_date' => $supply_date, 'note' => $note], ['id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {

            if($date){
            $this->db->update('sales', ['date' => $date], ['id' => $id]);
            }

            if ($status == 'completed' && $sale->sale_status != 'completed') {
                foreach ($items as $item) {
                    $item = (array) $item;
                    if ($this->getProductByID($item['product_id'])) {
                        $item_costs = $this->item_costing($item);
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
                $this->syncPurchaseItems($cost);
            }
            $this->syncQuantity($id);







        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (UpdataStatus:Sales_model.php)');
        } else {
            return true;
        }
        return false;
    }


	public function resetSaleActions($id, $return_id = null, $check_return = null)
    {
        if ($sale = $this->getInvoiceByID($id)) {
            if ($check_return && $sale->sale_status == 'returned') {
            	return false;
			}

            if ($sale->sale_status == 'completed') {
                if ($costings = $this->getSaleCosting($id)) {
                    foreach ($costings as $costing) {
                        if ($pi = $this->getPurchaseItemByID($costing->purchase_item_id)) {
                            $this->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        // $this->db->update('purchase_items', ['quantity_balance' => ($costing->quantity_balance + $costing->quantity)], ['id' => $pi->id]);
                        } else {
                            // $sale_item = $this->getSaleItemByID($costing->sale_item_id);
                            $pi = $this->getPurchasedItem(['product_id' => $costing->product_id, 'option_id' => $costing->option_id ? $costing->option_id : null, 'purchase_id' => null, 'transfer_id' => null, 'warehouse_id' => $sale->warehouse_id]);
                            $this->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        }
                    }
                    $this->db->delete('costing', ['id' => $costing->id]);
                }
                $items = $this->getAllInvoiceItems($id);
                $this->syncQuantity(null, null, $items);
                $this->update_award_points($sale->grand_total, $sale->customer_id, $sale->created_by, true);
                return $items;
            }
        }
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


	public function getPurchaseItemByID($id)
    {
        $q = $this->db->get_where('purchase_items', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function setPurchaseItem($clause, $qty)
    {
        if ($product = $this->getProductByID($clause['product_id'])) {
            if ($pi = $this->getPurchasedItem($clause)) {
                if ($pi->quantity_balance > 0) {
					return false;
				} else {
                    $quantity_balance = $pi->quantity_balance + $qty;
                    log_message('error', 'Less than zero: ' . $quantity_balance . ' = ' . $pi->quantity_balance . ' + ' . $qty . ' PI: ' . print_r($pi, true));
                }
                return $this->db->update('purchase_items', ['quantity_balance' => $quantity_balance], ['id' => $pi->id]);
            }
            $unit                        = $this->getUnitByID($product->unit);
            $clause['product_unit_id']   = $product->unit;
            $clause['product_unit_code'] = $unit->code;
            $clause['product_code']      = $product->code;
            $clause['product_name']      = $product->name;
            $clause['purchase_id']       = $clause['transfer_id']       = $clause['item_tax']       = null;
            $clause['net_unit_cost']     = $clause['real_unit_cost']     = $clause['unit_cost']     = $product->cost;
            $clause['quantity_balance']  = $clause['quantity']  = $clause['unit_quantity']  = $clause['quantity_received']  = $qty;
            $clause['subtotal']          = ($product->cost * $qty);
            if (isset($product->tax_rate) && $product->tax_rate != 0) {
                $tax_details           = $this->getTaxRateByID($product->tax_rate);
                $ctax                  = $this->calculateTax($product, $tax_details, $product->cost);
                $item_tax              = $clause['item_tax']              = $ctax['amount'];
                $tax                   = $clause['tax']                   = $ctax['tax'];
                $clause['tax_rate_id'] = $tax_details->id;
                if ($product->tax_method != 1) {
                    $clause['net_unit_cost'] = $product->cost - $item_tax;
                    $clause['unit_cost']     = $product->cost;
                } else {
                    $clause['net_unit_cost'] = $product->cost;
                    $clause['unit_cost']     = $product->cost + $item_tax;
                }
                $pr_item_tax = $this->sma->formatDecimal($item_tax * $clause['unit_quantity'], 12);

                $clause['subtotal'] = (($clause['net_unit_cost'] * $clause['unit_quantity']) + $pr_item_tax);
            }
            $clause['status']    = 'received';
            $clause['date']      = date('Y-m-d');
            $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : null;
            log_message('error', 'Why else: ' . print_r($clause, true));
            return $this->db->insert('purchase_items', $clause);
        }
        return false;
    }
	public function updateSale($id, $data, $items = [])
    {
        $this->db->trans_off();
		$this->db->trans_start();
		$Osale = $this->getSaleByID($id);

        $this->resetSaleActions($id, false, true);

        if ($data['sale_status'] == 'completed') {
            $this->getSystemSetting()->overselling = true;
            $cost                        = $this->costing($items, true);
        }
        // $this->sma->print_arrays($cost);



        if ($this->db->update('sales', $data, ['id' => $id]) && $this->db->delete('sale_items', ['sale_id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {
            foreach ($items as $item) {



				if ($item['product_type'] == 'combo') {
                		$item_cost_value=0;
						$avg_cost=0;
				        $combo_items_qty = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                        /*$total_qty=0;
						foreach ($combo_items_qty as $combo_item) {
						    $total_qty=$total_qty+$combo_item->qty;
                        }
						*/


						$combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
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
                if ($data['sale_status'] == 'completed' && $this->getProductByID($item['product_id'])) {
                    $item_costs = $this->item_costing($item);
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
                $this->syncPurchaseItems($cost);
            }




            $this->syncSalePayments($id);
            $this->syncQuantity($id);
            $sale = $this->getInvoiceByID($id);

            $this->update_award_points($data['grand_total'], $data['customer_id'], $sale->created_by);






        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Update:Sales_model.php)');
        } else {
            return true;
        }
        return false;
    }

    public function syncVariantQty($variant_id, $warehouse_id, $product_id = null)
    {
        $balance_qty    = $this->getBalanceVariantQuantity($variant_id);
        $wh_balance_qty = $this->getBalanceVariantQuantity($variant_id, $warehouse_id);
        if ($this->db->update('product_variants', ['quantity' => $balance_qty], ['id' => $variant_id])) {
            if ($this->getWarehouseProductsVariants($variant_id, $warehouse_id)) {
                $this->db->update('warehouses_products_variants', ['quantity' => $wh_balance_qty], ['option_id' => $variant_id, 'warehouse_id' => $warehouse_id]);
            } else {
                if ($wh_balance_qty) {
                    $this->db->insert('warehouses_products_variants', ['quantity' => $wh_balance_qty, 'option_id' => $variant_id, 'warehouse_id' => $warehouse_id, 'product_id' => $product_id]);
                }
            }
            return true;
        }
        return false;
    }

	private function getBalanceVariantQuantity($variant_id, $warehouse_id = null)
    {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', false);
        $this->db->where('option_id', $variant_id)->where('quantity_balance !=', 0);
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

	public function getWarehouseProductsVariants($option_id, $warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products_variants', ['option_id' => $option_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSalesmanByID($salesman_id)
    {
        $q = $this->db->get_where('companies', ['group_name' => 'salesman','id' => $salesman_id], 1);
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

    public function getRegisterByID($id)
    {
        $q = $this->db->get_where('pos_register', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function createEntryMultiSideV2($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference_no,$entryType,$user_id,$entryDescription=NULL,$entryDate=NULL) {
		$entryDebitAccount=json_encode((Array) $entryDebitAccount);
		$entryCreditAccount=json_encode((Array) $entryCreditAccount);
		$entryDebitBalance=json_encode((Array) $entryDebitBalance);
		$entryCreditBalance=json_encode((Array) $entryCreditBalance);
        if($entryDescription && $entryDate){
            $clause = ['reference_no' => $reference_no, 'created_by' => $user_id, 'userID' => $user_id, 'entryDescription' => $entryDescription, 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1 ,'entryDate' => $entryDate];

        }else{
            $clause = ['reference_no' => $reference_no, 'created_by' => $user_id, 'userID' => $user_id, 'entryDescription' => 'Automated_Accounting_Entry', 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1];

        }

		if($this->db->insert('gl_entries', $clause)){
			$entryID = $this->db->insert_id();
            if(!$entryDescription){
                $entryDescription='Automated_Accounting_Entry';
            }
			$this->approve($entryID,$entryDescription);
			return true;
		}
		return false;
   		 }


public function getEntriesCatchReceiptsBy_date_reference_no($date,$reference_no)
    {
        $q = $this->db->get_where('gl_entries', ['entryDate' => $date,'reference_no' => $reference_no,'entryType' => 'Sales_Payments_Catch_receipts']);
        if ($q->num_rows() > 0) {
            return $q->num_rows();
        }

        return 0;
    }


public function closePOSRegisterV2($data_open,$data_close,$dbname = null)
    {

        if ($this->db->insert('pos_register', $data_open)) {

			$rid = $this->db->insert_id();


		//if (!$user_id) {
            $user_id = $data_open['user_id'];//->session->userdata('user_id');
        //}
        if ($data_close['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_bills', ['created_by' => $user_id]);
        } elseif ($data_close['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_bills', ['created_by' => $data_close['transfer_opened_bills']], ['created_by' => $user_id]);
        }
        if ($this->db->update('pos_register', $data_close, ['id' => $rid, 'user_id' => $user_id])) {

			if ($this->getSystemSetting()->accountingOffline)// || $dbname ="accountstest" || $dbname ="buthaina")
			{
			$entry_type="Close_biller_register";



			$sales=$this->getToatalSalesPOS($this->getUser($data_close['user_id'])->biller_id,'completed',$data_open['date'],$data_close['closed_at']);
			$re_sales=$this->getToatalSalesPOS($this->getUser($data_close['user_id'])->biller_id,'returned',$data_open['date'],$data_close['closed_at']);

			$entryDebitAccount= [];
			$entryDebitBalance= [];
            $entryDebitTotal=0;

			$entryCreditAccount= [];
			$entryCreditBalance= [];
            $entryCreditTotal=0;

			$customersAcc= [];
			$customersBal= [];



			$paymentcash=$this->getToatalSalesPaymentPOS($this->getUser($data_close['user_id'])->biller_id,'completed','cash',$data_open['date'],$data_close['closed_at']);
			$paymentCC=$this->getToatalSalesPaymentPOSCC($this->getUser($data_close['user_id'])->biller_id,'completed','CC',$data_open['date'],$data_close['closed_at']);

			$re_paymentcash=$this->getToatalSalesPaymentPOS($this->getUser($data_close['user_id'])->biller_id,'returned','cash',$data_open['date'],$data_close['closed_at']);
			$re_paymentCC=$this->getToatalSalesPaymentPOSCC($this->getUser($data_close['user_id'])->biller_id,'returned','CC',$data_open['date'],$data_close['closed_at']);



			if($paymentcash->amount!=0){

			$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;

			if($entrybillerAcc==""){
                $entrybillerAcc=$this->getAccountByRefId2($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->getAccountByRefId3($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->getAccountByRefId4($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->getAccountByRefId5($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
            }
						if($entrybillerAcc==""){
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

						}
					$entryDebitAccount[] =  $entrybillerAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paymentcash->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paymentcash->amount);

			}

			foreach ($paymentCC as $paycc) {

			if($paycc->amount!=0){

				if($paycc->gl_payment_method_id==0){
					//$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
                    $entryCCAcc=$this->getPaymentTypeByPaymentType('CC')->payment_account;
                    if($entryCCAcc==""){
                        $entryCCAcc=$this->getAccountsSettings()->accounts_main_banks_acct;
					}
                    $entryDebitAccount[]=$entryCCAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paycc->amount);
				}else{
					$entryDebitAccount[]=$this->getPaymentTypeById($paycc->gl_payment_method_id)->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paycc->amount);
				}
			}

			}






			$total=0;
			$total_tax=0;
			$shipping=0;
			foreach ($sales as $sale) {
				$entryDebitAccountCustomer="";
				if($this->getCompanyByID($sale->customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->getAccountByRefId($sale->customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->getCompanyByID($sale->customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->getAccountByRefId($sale->customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_international_customers_acct;


				}
				if($sale->balance!=0){
			$entryDebitAccount[] =$entryDebitAccountCustomer;
			$entryDebitBalance[] =$sale->balance;
            $entryDebitTotal=$entryDebitTotal+$sale->balance;
				}

			$total += $sale->total;
            $total_tax += $sale->total_tax;
            $shipping += $sale->shipping;
            }



			$entryDebitAccountSales=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'sales')->id;
						if($entryDebitAccountSales=="")
					$entryDebitAccountSales =  $this->getAccountsSettings()->sales_acct;

					$entryCreditAccount[] =  $entryDebitAccountSales;
					$entryCreditAccount[] =  $this->getAccountsSettings()->sales_vat_acct;
					if(abs($shipping)>0)
					$entryCreditAccount[] =  $this->getAccountsSettings()->accounts_shipping_other_revenues;


					$entryCreditBalance[] =  $this->sma->formatDecimal($total);
                    $entryCreditTotal=$entryCreditTotal+$this->sma->formatDecimal($total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($total_tax);
					$entryCreditTotal=$entryCreditTotal+$this->sma->formatDecimal($total_tax);
					if(abs($shipping)>0){
					$entryCreditBalance[] =  $this->sma->formatDecimal($shipping);
                    $entryCreditTotal=$entryCreditTotal+$this->sma->formatDecimal($shipping);
					}






				if($re_paymentcash->amount!=0){

			$entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;

			if($entrybillerAcc==""){
                $entrybillerAcc=$this->getAccountByRefId2($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->getAccountByRefId3($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->getAccountByRefId4($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
            }
            if($entrybillerAcc==""){
                $entrybillerAcc=$this->getAccountByRefId5($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
            }
						//if($entrybillerAcc=="")
						//$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

							if($entrybillerAcc==""){
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

						}


					$entryCreditAccount[] =  $entrybillerAcc;
					$entryCreditBalance[] =  $this->sma->formatDecimal(abs($re_paymentcash->amount));
                    $entryCreditTotal=$entryCreditTotal+$this->sma->formatDecimal(abs($re_paymentcash->amount));

			}


			foreach ($re_paymentCC as $paycc) {

			if($paycc->amount!=0){

				if($paycc->gl_payment_method_id==0){
					//$entryDebitAccount[]=$this->getPaymentTypeByPaymentType('CC')->payment_account;
                    $entryCCAcc=$this->getPaymentTypeByPaymentType('CC')->payment_account;
                    if($entryCCAcc==""){
                        $entryCCAcc=$this->getAccountsSettings()->accounts_main_banks_acct;
					}
                    $entryDebitAccount[]=$entryCCAcc;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paycc->amount);
				}else{
					$entryDebitAccount[]=$this->getPaymentTypeById($paycc->gl_payment_method_id)->payment_account;
					$entryDebitBalance[] =  $this->sma->formatDecimal($paycc->amount);
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal($paycc->amount);
				}
			}

			}



			$re_total=0;
			$re_total_tax=0;
			$re_shipping=0;
			foreach ($re_sales as $re_sale) {
				$entryDebitAccountCustomer="";
				if($this->getCompanyByID($re_sale->customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->getAccountByRefId($re_sale->customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->getCompanyByID($re_sale->customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->getAccountByRefId($re_sale->customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->getAccountsSettings()->accounts_international_customers_acct;


				}
				if(abs($re_sale->balance)!=0){
			$entryCreditAccount[] =$entryDebitAccountCustomer;
			$entryCreditBalance[] =abs($re_sale->balance);
            $entryCreditTotal=$entryCreditTotal+abs($re_sale->balance);
				}

			$re_total += abs($re_sale->total);
            $re_total_tax += abs($re_sale->total_tax);
            $re_shipping += abs($re_sale->shipping);
            }





				$entryDebitAccountSales=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'return_sales')->id;
						if($entryDebitAccountSales=="")
					$entryDebitAccountSales =  $this->getAccountsSettings()->sales_return_acct;

					if(abs($re_total)>0)
					$entryDebitAccount[] =  $entryDebitAccountSales;
					if(abs($re_total_tax)>0)
					$entryDebitAccount[] =  $this->getAccountsSettings()->sales_vat_acct;
					if(abs($re_shipping)>0)
					$entryDebitAccount[] =  $this->getAccountsSettings()->accounts_shipping_other_revenues;


					if(abs($re_total)>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_total));
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal(abs($re_total));
                    }
                    if(abs($re_total_tax)>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_total_tax));
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal(abs($re_total_tax));
                    }
					if(abs($re_shipping)>0){
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($re_shipping));
                    $entryDebitTotal=$entryDebitTotal+$this->sma->formatDecimal(abs($re_shipping));
                    }



                    if($entryDebitTotal!=$entryCreditTotal){
                        if($entryDebitTotal>$entryCreditTotal){
                            $rem=$entryDebitTotal-$entryCreditTotal;
                            $cr_index=0;
                            foreach ($entryCreditAccount as $creditAccount){
                               if ($creditAccount==$this->pos2_model->getAccountsSettings()->sales_acct){
                                    $entryCreditBalance[$cr_index]=$entryCreditBalance[$cr_index]+$this->sma->formatDecimal(abs($rem));
                                }

								$cr_index++;



							}
                        }else{
                            $rem=$entryDebitTotal-$entryCreditTotal;
                            $cr_index=0;
                            foreach ($entryCreditAccount as $creditAccount){
                               if ($creditAccount==$this->pos2_model->getAccountsSettings()->sales_acct){
                                    $entryCreditBalance[$cr_index]=$entryCreditBalance[$cr_index]-$this->sma->formatDecimal(abs($rem));
                                }

								$cr_index++;



							}
                        }
                    }




                    if(isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1){
				        $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close['user_id'],$data_close['grpId'],$data_close['coId'],$data_close['brId']);
                    }else{
                        $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close['user_id']);
                    }

				   $entryWarehouseAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->getAccountsSettings()->warehouses_acct;

				$good_sold=$this->getToatalGoodSold($this->getUser($data_close['user_id'])->biller_id,'completed',$data_open['date'],$data_close['closed_at']);

				if($this->getSystemSetting()->accounting_type=="periodic" || $good_sold->item_cost_value==0){

                }else{
                    if(isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1){
				        $this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($good_sold->item_cost_value),$this->sma->formatDecimal($good_sold->item_cost_value),$rid,$entry_type,$data_close['user_id'],$data_close['grpId'],$data_close['coId'],$data_close['brId']);
                    }else{
                        $this->createEntryMultiSide($this->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($good_sold->item_cost_value),$this->sma->formatDecimal($good_sold->item_cost_value),$rid,$entry_type,$data_close['user_id']);
                    }
                 }
				$re_good_sold=$this->getToatalGoodSold($this->getUser($data_close['user_id'])->biller_id,'returned',$data_open['date'],$data_close['closed_at']);

				if($this->getSystemSetting()->accounting_type=="periodic" || abs($re_good_sold->item_cost_value)==0){

                }else{
                    if(isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1){
				        $this->createEntryMultiSide($entryWarehouseAcc,$this->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$rid,$entry_type,$data_close['user_id'],$data_close['grpId'],$data_close['coId'],$data_close['brId']);
                    }else{
                        $this->createEntryMultiSide($entryWarehouseAcc,$this->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$this->sma->formatDecimal(abs($re_good_sold->item_cost_value)),$rid,$entry_type,$data_close['user_id']);
                    }
                }


                if($this->getSystemSetting()->isDonation==1 && $data_close['donation']!='0'){
                    $entry_type="Donations_due_to_charities";
                    //$entryDescription="Donations_Payments_Sales_Invoice_No#".$sale_id;


                    $entryDebitAccount= [];
                    $entryDebitBalance= [];


                    $entrybillerAcc=$this->getAccountByRefId($this->getUser($data_close['user_id'])->biller_id,'billers')->id;
                    if($entrybillerAcc==""){
                    $entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;
                    $entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
                    $entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
                    if($entrybillerAcc=="")
                    $entrybillerAcc=$this->getAccountsSettings()->accounts_main_boxes_acct;

                    }
                $entryDebitAccount[] =  $entrybillerAcc;
                $entryDebitBalance[] =  $this->sma->formatDecimal($data_close['donation']);



                $entryCreditAccount= [];
                $entryCreditBalance= [];

                $entryCrediAcc=$this->getAccountsSettings()->donations_acct;
                    if($entryCrediAcc=="")
                    $entryCrediAcc=$this->getAccountsSettings()->expenses_shipping_acct;
                     $entryCreditAccount[] =  $entryCrediAcc;


                $entryCreditBalance[] =  $this->sma->formatDecimal($data_close['donation']);

                if(isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1){
                    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close['user_id'],$data_close['grpId'],$data_close['coId'],$data_close['brId']);
                }else{
                    $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$rid,$entry_type,$data_close['user_id']);
                }



                }


			}

            if(isset($this->getSystemSetting()->isAutoSynchronizeZATCAPOS) && $this->getSystemSetting()->isAutoSynchronizeZATCAPOS){
                $zatcaInvoices=$this->getAllZatcaInvoices($data_open['date'],$data_close['closed_at'],$this->getUser($data_close['user_id'])->biller_id);
                foreach ($zatcaInvoices as $invoice) {
                    $this->generate_xml($invoice->id);
                }
            }

            return true;
        }


        }
        return false;
    }

    public function getUniqeUUID()
    {
        $myQuery = "select UUID() as UUID";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getMainBranchesByID($id)
    {
        $q = $this->db->get_where('main_branches', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCountSalesBybrid($brid)
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get_where('sales', ['brid' => $brid,'pos' => 0], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

    public function getCountEntriesBybrid($brid)
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get_where('gl_entries', ['brid' => $brid], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

    public function getBalanceQuantityV2($product_id, $warehouse_id = null)
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

    public function getCountProductsByItemNo($item_no)
	{
		$this->db->select('(count(*)) as count');
		$q = $this->db->get_where('products', ['item_no' => $item_no], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getAccountByRefId2($account_ref_id2,$account_type) {
        $q = $this->db->get_where('gl_accounts', array('account_ref_id2' => $account_ref_id2,'account_type' => $account_type), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   	}

    public function getAccountByRefId3($account_ref_id3,$account_type) {
        $q = $this->db->get_where('gl_accounts', array('account_ref_id3' => $account_ref_id3,'account_type' => $account_type), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   	}

    public function getAccountByRefId4($account_ref_id4,$account_type) {
        $q = $this->db->get_where('gl_accounts', array('account_ref_id4' => $account_ref_id4,'account_type' => $account_type), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   	}

    public function getAccountByRefId5($account_ref_id5,$account_type) {
        $q = $this->db->get_where('gl_accounts', array('account_ref_id5' => $account_ref_id5,'account_type' => $account_type), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   	}

   	public function updateProduct($id, $data = [])
    {
           if ($this->db->update('products', $data, ['id' => $id])) {
               return true;
           }
           return false;
    }

    public function getAllZatcaInvoices($sdate, $edate, $biller_id, $warehouse_id = null)
    {
        $this->db->where('synch', 0)->where('pos', '1')->where('biller_id', $biller_id)->where(" (sale_status =  'completed' OR sale_status =  'returned') ")->where(" (date  between  '".$sdate."' and '".$edate."') ");//->where('date >=', $sdate)->where('date <=', $edate);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->order_by('id', 'asc');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->getSystemSetting()->site_name;

        if($invPre         = $this->getLastInvoiceSynch($inv->warehouse_id)){
            $stamp_hash=$invPre->stamp_hash;
        }else{
            $stamp_hash=null;
        }

		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $this->getSystemSetting()->default_currency;
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
                    if($inv->debit_id)
                        $InvoiceTypeCode= '383';
                    else
                        $InvoiceTypeCode= '388';
                }else if($inv->sale_status == 'returned'){
                    $InvoiceTypeCode= '381';
                }
                $note= str_replace("<p>"," ",$this->sma->decode_html($inv->note));
                $note= str_replace("</p>","",$this->sma->decode_html($note));
                //$note= str_replace(" ","",$this->sma->decode_html($note));

                $this->sma->xmlInvoice($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$inv->id,$rows,$warehouse,$customer,$inv->uuid,$stamp_hash,$this->sma->formatDecimal($return_sale ? ($inv->paid  + $return_sale->paid ) : abs($inv->paid )),$paymentMeansCode,$InvoiceTypeCode,($inv->sale_id ? $inv->sale_id : ($inv->debit_id ? $inv->debit_id : null)),$note);
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

    public function getWarehouseByID($id)
    {
        $q = $this->db->get_where('warehouses', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
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

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->getSystemSetting()->site_name;
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
				$currencyCode= $this->getSystemSetting()->default_currency;
				$currencyRate= 1;
			}

		//$this->sma->print_arrays($InvoiceTypeNumber);

		if($inv->sale_status == 'returned' || $inv->sale_status == 'completed'){

			$QR=$this->sma->xmlInvoiceSign($inv->id,$inv->xml_file_path,$wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$zatcaConfig,$InvoiceTypeNumber);

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

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->getSystemSetting()->site_name;

		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $this->getSystemSetting()->default_currency;
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
					$this->sma->write_log('ERROR', 'An errors has been occurred while Sync the sale (Add:Pos_model.php)','sales','Id ('.$inv->id.') Response '.json_encode($res));
				} catch (Exception $e) {
				}

			}else{
                try {
                    //$dbname=str_replace("cpesoft_","",$this->db->database);
		                //if($dbname!="rawabi"){
        					$xml_file_path         = $this->getInvoiceByID($inv->id)->xml_file_path;
		        			unlink('./assets/xml/'.$xml_file_path.'.xml');
				        	unlink('./assets/xml/'.$xml_file_path.'merged.xml');
        					unlink('./assets/xml/'.$xml_file_path.'signed.xml');
		        			unlink('./assets/xml/'.$xml_file_path.'signedWQR.xml');
                        //}
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

    public function update_award_points($total, $customer, $user = null, $scope = null)
    {
        if (!empty($this->getSystemSetting()->each_spent) && $total >= $this->getSystemSetting()->each_spent) {
            $company      = $this->getCompanyByID($customer);
            $points       = floor(($total / $this->getSystemSetting()->each_spent) * $this->getSystemSetting()->ca_point);
            $total_points = $scope ? $company->award_points - $points : $company->award_points + $points;
            $reverse_points       = !empty($this->getSystemSetting()->reverse_each_spent) ? floor(($total_points / $this->getSystemSetting()->reverse_ca_point) * $this->getSystemSetting()->reverse_each_spent) : null;
            $this->db->update('companies', ['award_points' => $total_points], ['id' => $customer]);
            $WhatsappSettings=$this->getWhatsappSettings();
            if ($WhatsappSettings->award_points==1) {
                if(empty($this->getSystemSetting()->reverse_each_spent)){
                    //$text="عزيزي ".$company->company.", لقد حصلت على  (".$points.") نقطة. رصيدك من النقاط  ".$total_points;
                    $text=$this->getWhatsappTemplates()->reverse_award_points;

                    $text = str_replace("{customer}", $company->company, ($text) . "");
                    $text = str_replace("{points}", $points, ($text) . "");
                    $text = str_replace("{award_points}", $total_points, ($text) . "");
                }else{
                    $currency_name=$this->getCurrencyByCode($this->getSystemSetting()->default_currency)->name;
                //    $text="عزيزي ".$company->company." لديك :\n(".$total_points.") نقطة\n(".$reverse_points.") ".$currency_name."\nبادر بزيارة احد فروع ".$this->getSystemSetting()->site_name." او الموقع الإلكتروني للإستفادة منها دمت بخير";
                    //$text="عزيزي ".$company->company." لديك :\n(".$total_points.") نقطة\n(".$reverse_points.") ".$currency_name."\nبادر بزيارة احد فروع ".$this->getSystemSetting()->site_name." للإستفادة منها دمت بخير";
                    $text=str_replace("\\n","\n", ($this->getWhatsappTemplates()->award_points) . "");

                    $text = str_replace("{customer}", $company->company, ($text) . "");
                    $text = str_replace("{points}", $points, ($text) . "");
                    $text = str_replace("{award_points}", $total_points, ($text) . "");
                    $text = str_replace("{reverse_award_points}", $reverse_points, ($text) . "");
                    $text = str_replace("{default_currency}", $currency_name, ($text) . "");
                    $text = str_replace("{site_name}", $this->getSystemSetting()->site_name, ($text) . "");

                }

                //$phone="0502478081";//$company->phone;
                $phone=$company->phone;
                $WhatsappSettingsConfig=json_decode($WhatsappSettings->config,true);

                if (!empty($WhatsappSettingsConfig['gateway']) && !empty($WhatsappSettingsConfig)) {
                    if($WhatsappSettingsConfig['gateway']=='4whatsapp'){
                        $phone = str_replace(" ", "", $phone);
                        $sent_flag=0;
                        $failed_flag=0;
                        $char = ",";
                        // Using strpos() function

                            $recipient="";
                            $tel_no=trim($phone);
                            $len = strlen($tel_no);
                                if($len>8 && is_numeric($tel_no)){
                                    if(substr($tel_no, 0, 1) === "+"){
                                        $recipient=$tel_no;
                                    }else{
                                        if($len>10){
                                            if(substr($tel_no, 0, 2) === "00"){
                                                $recipient="+".substr($tel_no, 2);
                                            }else{
                                                $recipient="+".$tel_no;
                                            }
                                        }else{
                                            if(substr($tel_no, 0, 1) === "0"){
                                                $recipient="+966".substr($tel_no, 1);
                                            }else{
                                                $recipient="+966".$tel_no;
                                            }
                                        }
                                    }

                                    $message=$text;
                                    $phone=$WhatsappSettingsConfig['4whatsapp']['phone'];
                                    $password=$WhatsappSettingsConfig['4whatsapp']['password'];
                                    $fields = array("phones"=>$phone, "account"=>"0", "doctype"=>"text", "recipient"=>$recipient, "message"=>$message, "token"=>$password);
                                    $files = array();
                                    $url = "http://api.4whatsapp.com/api/Agent_Client_";
                                    $curl = curl_init();
                                    $url_data = http_build_query($data);
                                    $boundary = 'b77a5c561934e089';
                                    $delimiter = '-------------' . $boundary;
                                    $post_data = '';
                                    $eol = "\r\n";

                                    $delimiter = '-------------' . $boundary;

                                    foreach ($fields as $name => $content) {
                                        $post_data .= "--" . $delimiter . $eol
                                        . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                                        . $content . $eol;
                                    }
                                    $post_data .= "--" . $delimiter . "--".$eol;
                                    curl_setopt_array($curl, array(
                                        CURLOPT_URL => $url,
                                        CURLOPT_RETURNTRANSFER => 1,
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 30,
                                        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => "POST",
                                        CURLOPT_POST => 1,
                                        CURLOPT_POSTFIELDS => $post_data,
                                        CURLOPT_HTTPHEADER => array(
                                          //"Authorization: Bearer $TOKEN",
                                          "Content-Type: multipart/form-data; boundary=" . $delimiter,
                                          "Content-Length: " . strlen($post_data)

                                        ),


                                    ));


                                    //$response = json_decode(curl_exec($curl));
                                    //curl_close($curl);
                                    $resp = curl_exec($curl);
                                    $response = json_decode($resp);
                                    curl_close($curl);

                                    $whatsappMsg="";
                                    if (isset($response->data) && !empty(json_decode($response->data)) && json_decode($response->data)->status=='200'){
                                        $sent_flag=1;
                                        try {
                                            $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                        } catch (Exception $e) {
                                        }
                                    }else{
                                        $failed_flag=1;
                                        try {
                                            $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                        } catch (Exception $e) {
                                        }
                                    }

                                }else{
                                    $failed_flag=1;
                                    try {
                                        $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                    } catch (Exception $e) {
                                    }
                                }





                    }else if($WhatsappSettingsConfig['gateway']=='arkanprowhatsapp'){
                        $phone = str_replace(" ", "", $phone);
                        $sent_flag=0;
                        $failed_flag=0;
                        $char = ",";
                        // Using strpos() function

                            $recipient="";
                            $tel_no=trim($phone);
                            $len = strlen($tel_no);
                                if($len>8 && is_numeric($tel_no)){
                                    if(substr($tel_no, 0, 1) === "+"){
                                        $recipient="".substr($tel_no, 1);
                                    }else{
                                        if($len>10){
                                            if(substr($tel_no, 0, 2) === "00"){
                                                $recipient="".substr($tel_no, 2);
                                            }else{
                                                $recipient="".$tel_no;
                                            }
                                        }else{
                                            if(substr($tel_no, 0, 1) === "0"){
                                                $recipient="966".substr($tel_no, 1);
                                            }else{
                                            $recipient="966".$tel_no;
                                            }
                                        }
                                    }

                                    $message=$text;
                                    $sessionId=$WhatsappSettingsConfig['arkanprowhatsapp']['sessionId'];
                                    $files = array();
                                    $url = "https://iparkanwebsitforwhats/".$sessionId."/messages/send";

                                    $curl = curl_init($url);
                                    curl_setopt($curl, CURLOPT_URL, $url);
                                    curl_setopt($curl, CURLOPT_POST, true);
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                                    $headers = array(
                                       "Content-Type: application/json",
                                       "accept: application/json",
                                    );
                                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                                    $data = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                                    //for debug only!
                                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                                    //$response = json_decode(curl_exec($curl));
                                    //curl_close($curl);
                                    $resp = curl_exec($curl);
                                    $response = json_decode($resp);
                                    curl_close($curl);

                                    $whatsappMsg="";
                                    if (isset($response->message) && $response->status=='PENDING'){
                                        $sent_flag=1;
                                        try {
                                            $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                        } catch (Exception $e) {
                                        }
                                    }else{
                                        $failed_flag=1;
                                        try {
                                            $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                        } catch (Exception $e) {
                                        }
                                    }

                                }else{
                                    $failed_flag=1;
                                    try {
                                        $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                    } catch (Exception $e) {
                                    }
                                }
                    }
                }

                /*$this->load->library('sms');
                $text=  $this->sms->invAwardPointsTextV2($company->company, $points, $total_points);

                $this->load->library('whatsapp');
                $this->whatsapp->send($company->phone, $text);*/

            }
        }
        if ($user && !empty($this->getSystemSetting()->each_sale) && !$this->Customer && $total >= $this->getSystemSetting()->each_sale) {
            $staff        = $this->getUser($user);
            $points       = floor(($total / $this->getSystemSetting()->each_sale) * $this->getSystemSetting()->sa_point);
            $total_points = $scope ? $staff->award_points - $points : $staff->award_points + $points;
            $this->db->update('users', ['award_points' => $total_points], ['id' => $user]);
        }
        return true;
    }

    public function update_award_pointsV2($total, $customer, $user = null, $scope = null)
    {
        if (!empty($this->getSystemSetting()->reverse_each_spent) && $total >= $this->getSystemSetting()->reverse_each_spent) {
            $company      = $this->getCompanyByID($customer);
            $points       = floor(($total / $this->getSystemSetting()->reverse_each_spent) * $this->getSystemSetting()->reverse_ca_point);
            $total_points = $scope ? $company->award_points - $points : $company->award_points + $points;
            $reverse_points       = !empty($this->getSystemSetting()->reverse_each_spent) ? floor(($total_points / $this->getSystemSetting()->reverse_ca_point) * $this->getSystemSetting()->reverse_each_spent) : null;
            if($total_points < 0){
                $total_points=0;
            }
            $this->db->update('companies', ['award_points' => $total_points], ['id' => $customer]);
            $WhatsappSettings=$this->getWhatsappSettings();
            if ($WhatsappSettings->award_points==1) {
                $this->load->library('sms');
                $currency_name=$this->getCurrencyByCode($this->getSystemSetting()->default_currency)->name;
                $text=  $this->sms->invAwardPointsText($customer, $points, $total_points,$reverse_points,$this->getSystemSetting()->site_name,$currency_name);

                $this->load->library('whatsapp');
                $this->whatsapp->send($company->phone, $text);
            }
        }
        if ($user && !empty($this->getSystemSetting()->reverse_each_sale) && !$this->Customer && $total >= $this->getSystemSetting()->reverse_each_sale) {
            $staff        = $this->getUser($user);
            $points       = floor(($total / $this->getSystemSetting()->reverse_each_sale) * $this->getSystemSetting()->reverse_sa_point);
            $total_points = $scope ? $staff->award_points - $points : $staff->award_points + $points;
            if($total_points < 0){
                $total_points=0;
            }
            $this->db->update('users', ['award_points' => $total_points], ['id' => $user]);
        }
        return true;
    }


    public function getToatalSalesPaymentPOSV2($biller_id,$sale_status,$paid_by,$start_date = null, $end_date = null, $points = null)
    {

		   $this->db->select('COALESCE(sum(amount),0) as amount')
			 ->join('payments', 'payments.sale_id = sales.id', 'left');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
		if ($start_date && $end_date) {
             //   $this->db->where('sales.date >=', $start_date);
        	    $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
         }

        if($paid_by=='gift_card' && $points==1){
            $this->db->where('paid_by', $paid_by);
        }

		$this->db->where('sales.biller_id', $biller_id);
        $this->db->where('sales.sale_status', $sale_status);
        $this->db->where('paid_by', $paid_by);
        $this->db->where('sales.pos', '1');

        if($paid_by=='gift_card'){
            if($points==1){
                $this->db->where('cc_no', '');
            }else{
                $this->db->where('cc_no !=', '');
            }
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getWhatsappSettings()
    {
        $q = $this->db->get('whatsapp_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCurrencyByCode($code)
    {
        $q = $this->db->get_where('currencies', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function calculateDiscount($discount = null, $amount, $tobacco = null)
    {
        if ($discount && $this->getSystemSetting()->product_discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {
                $pds = explode('%', $discount);
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($tobacco ? ($amount/2) : $amount)) * (float) ($pds[0])) / 100), 12);
            }
            return $this->sma->formatDecimal($discount, 12);
        }
        return 0;
    }

    public function getWhatsappTemplates()
    {
        $q = $this->db->get('whatsapp_templates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function addCategory($data)
    {
        if ($this->db->insert('categories', $data)) {

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $cid = $this->db->insert_id();
                    $data_categories_details = [
                        'category_id'            => $cid,
                        'grpId'            => $data ['grpId'],
                        'coId'            => $data ['coId'],
                        'brId'            => $data ['brId'],



                    ];


                    if ($this->db->insert('categories_details', $data_categories_details)) {

                        //return true;
                    }



            }


            return true;
        }
        return false;
    }

    public function getDepositBy_date_Offline_id($date,$company_id,$Offline_id)
    {
        $q = $this->db->get_where('deposits', ['date' => $date,'company_id' => $company_id,'Offline_id' => $Offline_id]);
        if ($q->num_rows() > 0) {
            return $q->num_rows();
        }

        return 0;
    }

    public function addDeposit($data, $cdata,$payment_type = null)
    {
        if ($this->db->insert('deposits', $data)) {
            $did = $this->db->insert_id();
            $this->db->update('companies', $cdata, ['id' => $data['company_id']]);

			if ($this->getSystemSetting()->accounting){
			$entryDescription="Deposit_No#".$did;








				$entry_type="deposits";


					$entryCreditAccount= [];
					$entryCreditBalance= [];








					$entryDebitAccount= [];

					//$entryDebitAccount[] =  $this->getPaymentTypeByID($payment_type)->payment_account;

                    $entryDebitAcc=$this->getPaymentTypeByID($payment_type)->payment_account;
					if($entryDebitAcc=="")
						$entryDebitAcc=$payment_type;
					$entryDebitAccount[] =  $entryDebitAcc;

					$entryDebitBalance= [];

					$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']);

					//$amount=$amount+$data['amount'];


					$entryCreditAccount= [];
					$entryCreditBalance= [];
					if($this->getCompanyByID($data['company_id'])->co_type==1){
						$entryDebitAcc=$this->getAccountByRefId($data['company_id'],'local_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->getAccountsSettings()->accounts_local_customers_acct;
					}else{
					$entryDebitAcc=$this->getAccountByRefId($data['company_id'],'international_customers')->id;
						if($entryDebitAcc=="")
						$entryDebitAcc=$this->getAccountsSettings()->accounts_international_customers_acct;
					}
					 $entryCreditAccount[] =  $entryDebitAcc;

					 $entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']);




					/*if($this->sma->formatDecimal($data['amount'])!=$this->sma->formatDecimal($sale->grand_total)){
					$entryDebitBalance[] =  $this->sma->formatDecimal($sale->grand_total - $data['amount']);

					}*/








                    if(isset($this->getSystemSetting()->isGrouping) && $this->getSystemSetting()->isGrouping==1)
                    {

                        $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$did,$entry_type,$data['created_by'],$data['grpId'],$data['coId'],$data['brId']);

                    }else{
                        $this->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$did,$entry_type,$data['created_by']);
                    }










			}







			return true;
        }
        return false;
    }

    public function getCategoryByShopId($shop_id)
    {
        $q = $this->db->get_where('categories', ['shop_id' => $shop_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getProductsByShopId($shop_id)
    {
        $q = $this->db->get_where('products', ['shop_id' => $shop_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }


    public function getInvoiceBy_date_biller_id_Offline_Pos_id($date,$biller_id,$Offline_Pos_id)
    {
        $q = $this->db->get_where('sales', ['date' => $date,'Offline_Pos_id' => $Offline_Pos_id,'biller_id' => $biller_id,'pos' => 1]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return 0;
    }

    public function updateCategory($shop_id, $data = [])
    {
        if ($this->db->update('categories', $data, ['shop_id' => $shop_id])) {
            return true;
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
    
    public function getAllSalesDue($id = NULL,$startDate = NULL,$endDate = NULL,$flage = NULL, $customer_id = NULL){

		$this->db->select('id, DATE_FORMAT(date, "%Y-%m-%d") as date, reference_no, customer, (grand_total-(paid+discount_allowance)) as amount, COALESCE(payment_term,0) as payment_term,(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END) as due_date,DATEDIFF("'.$endDate.'",(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END)) as outstanding');
		$this->db->order_by('date', 'ASC');

		if ($startDate && $endDate && $flage){
			    $this->db->where(" (date BETWEEN '" . $startDate . "' and '" . $endDate . "' )  ");

			}
			if ($startDate==NULL && $endDate){
			    //$this->db->where(" (date <= '" . $endDate . "' )  ");
			   $this->db->where('DATE_FORMAT(date, "%Y-%m-%d")  <=',$endDate);

			}


			if($id){
				$this->db->where('id', $id);
			}
			if($customer_id){
				$this->db->where('customer_id', $customer_id);
			}

			$this->db->where('payment_status !=', 'paid');
			$this->db->where('pos', 1);
			$this->db->where('abs(grand_total-(paid+discount_allowance)) >', 0);



        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;

	}

    public function IsAccountIsChild($id,$parent_id)
	{


		$row = $this->db->get_where('gl_accounts', ['id' => $parent_id])->result_array();



		foreach ($row as $key => $value) {
			//return $value;
			if($id == $value['id']){
				return '1';
			}
			if($value['level']==1){
				return '0';
			}
			$data = $this->IsAccountIsChild($id,$value['parent_id']);
		}



		return $data;
	}

    public function getAllZatcaInvoicesV2($sdate, $edate)
    {
        $this->db->where('synch', 0)->where('pos', '1')->where(" (sale_status =  'completed' OR sale_status =  'returned') ")->where(" (date  between  '".$sdate."' and '".$edate."') ");//->where('date >=', $sdate)->where('date <=', $edate);
        $this->db->order_by('id', 'asc');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
    
    public function addDeliver($data = [], $items = [])
    {
        if ($this->db->insert('delivers', $data)) {
            $deliver_id = $this->db->insert_id();
            if ($this->getReference('do') == $data['reference_no']) {
                $this->updateReference('do');
            }
            foreach ($items as $item) {
                $item['deliver_id'] = $deliver_id;
                $this->db->insert('deliver_items', $item);
            }
            return ['deliver_id' => $deliver_id, 'message' => $msg];
        }
        return false;
    }
    
    public function getDeliverBy_reference_no_biller_id_V2($reference_no,$biller_id,$Offline_id)
    {
        $q = $this->db->get_where('delivers', ['reference_no' => $reference_no,'Offline_id' => $Offline_id,'biller_id' => $biller_id]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return 0;
    }

}
