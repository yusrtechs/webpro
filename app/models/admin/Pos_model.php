<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pos_model extends CI_Model
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
                    $gc = $this->site->getGiftCardByNO($payment['cc_no']);
                    $this->db->update('gift_cards', ['balance' => ($gc->balance - $payment['amount'])], ['card_no' => $payment['cc_no']]);
                } elseif ($customer_id && $payment['paid_by'] == 'deposit') {
                    $customer = $this->site->getCompanyByID($customer_id);
                    $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $payment['amount'])], ['id' => $customer_id]);
                }
                unset($payment['cc_cvv2']);
                $this->db->insert('payments', $payment);
                $paid += $payment['amount'];
            }
            if (!isset($msg)) {
                if ($this->site->getReference('pay') == $data['reference_no']) {
                    $this->site->updateReference('pay');
                }
                $this->site->syncSalePayments($payment['sale_id']);
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
                $this->site->syncProductQty($product_id, $warehouse_id);
                return true;
            }
        } else {
            if ($this->insertQuantity($product_id, $warehouse_id, -$quantity)) {
                $this->site->syncProductQty($product_id, $warehouse_id);
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

    public function addSale($data = [], $items = [], $payments = [], $sid = null, $si_return = [])
    {
        if (empty($si_return)) {
            $cost = $this->site->costing($items);
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


            $this->site->syncQuantity($sale_id);
            if ($sid) {
                $this->deleteBill($sid);
            }






            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            $this->site->updateReference('pos');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Add:Pos_model.php)');
        } else {
            $msg = [];
            if (!empty($payments)) {
                $paid = 0;
                foreach ($payments as $payment) {
                    if (!empty($payment) && isset($payment['amount']) && $payment['amount'] != 0) {
                        $payment['sale_id']      = $sale_id;
                        $payment['reference_no'] = $this->site->getReference('pay');
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
                                $this->site->updateReference('pay');
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
                                $this->site->updateReference('pay');
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
                                $this->site->updateReference('pay');
                                $paid += $payment['amount'];
                            } else {
                                $msg[] = lang('payment_failed');
                                $msg[] = '<p class="text-danger">' . $result['msg'] . '</p>';
                            }
                        } else {
                            if ($payment['paid_by'] == 'gift_card') {
                                $this->db->update('gift_cards', ['balance' => $payment['gc_balance']], ['card_no' => $payment['cc_no']]);
                                unset($payment['gc_balance']);
                            } elseif ($payment['paid_by'] == 'deposit') {
                                $customer = $this->site->getCompanyByID($data['customer_id']);
                                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $payment['amount'])], ['id' => $customer->id]);
                            }
                            unset($payment['cc_cvv2']);
                            $this->db->insert('payments', $payment);
                            $this->site->updateReference('pay');
                            $paid += $payment['amount'];
                        }
                    }
                }
                $this->site->syncSalePayments($sale_id);
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

		$register=$this->registerData($data['user_id']);

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

			$reg_id = $this->db->insert_id();

			if ($this->Settings->accounting){
			$entry_type="Close_biller_register";



			$sales=$this->getToatalSales($this->getUser($data['user_id'])->biller_id,$register->date,$data['closed_at']);
			$paymentcash=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'cash',$register->date,$data['closed_at']);
			$paymentCC=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'CC',$register->date,$data['closed_at']);
			$paymentbank_transfer=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'bank_transfer',$register->date,$data['closed_at']);
			$paymentCheque=$this->getToatalSalesPayment($this->getUser($data['user_id'])->biller_id,'Cheque',$register->date,$data['closed_at']);



					$entryDebitAccount= [];
					$entryDebitBalance= [];
					$entrybillerAcc=$this->site->getAccountByRefId($this->getUser($data['user_id'])->biller_id,'billers')->id;

                    if($entrybillerAcc==""){
                        $entrybillerAcc=$this->site->getAccountByRefId2($this->getUser($data['user_id'])->biller_id,'billers')->id;
                    }
                    if($entrybillerAcc==""){
                        $entrybillerAcc=$this->site->getAccountByRefId3($this->getUser($data['user_id'])->biller_id,'billers')->id;
                    }
                    if($entrybillerAcc==""){
                        $entrybillerAcc=$this->site->getAccountByRefId4($this->getUser($data['user_id'])->biller_id,'billers')->id;
                    }
                    if($entrybillerAcc==""){
                        $entrybillerAcc=$this->site->getAccountByRefId5($this->getUser($data['user_id'])->biller_id,'billers')->id;
                    }

                    if($entrybillerAcc=="")
						$entrybillerAcc=$this->site->getAccountsSettings()->accounts_main_boxes_acct;
						/*if($entrybillerAcc==""){
						$entrybillerAcc=$this->site->getAccountsSettings()->accounts_main_boxes_acct;
						$entrybillerAccCode=$this->getAccountByID($entrybillerAcc)->accountID;
						$entrybillerAcc=$this->getAccountByAccountID($entrybillerAccCode."01")->id;
						if($entrybillerAcc=="")
						$entrybillerAcc=$this->site->getAccountsSettings()->accounts_main_boxes_acct;

						}*/

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

					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_acct;
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total);
					$entryCreditBalance[] =  $this->sma->formatDecimal($sales->total_tax);



			    $this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$register->id,$entry_type);

				       $entryWarehouseAcc=$this->site->getAccountByRefId($this->getUser($data['user_id'])->warehouse_id,'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


					$this->site->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($sales->item_cost_value),$this->sma->formatDecimal($sales->item_cost_value),$register->id,$entry_type);

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
    public function getCompanyByOfflineID($Offline_id)
    {
        $q = $this->db->get_where('companies', ['Offline_id' => $Offline_id], 1);
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

    public function getProductComboItems($pid, $warehouse_id)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, warehouses_products.quantity as quantity, warehouses_products.avg_cost as avg_cost')
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

    public function getSetting()
    {
        $q = $this->db->get('pos_settings');
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

			if ($this->Settings->accounting){
			$entry_type="Open_biller_register";

			$entryDebitAccount= [];



						$entrybillerAcc=$this->site->getAccountByRefId($this->getUser($data['user_id'])->biller_id,'billers')->id;

                        if($entrybillerAcc==""){
                            $entrybillerAcc=$this->site->getAccountByRefId2($this->getUser($data['user_id'])->biller_id,'billers')->id;
                        }
                        if($entrybillerAcc==""){
                            $entrybillerAcc=$this->site->getAccountByRefId3($this->getUser($data['user_id'])->biller_id,'billers')->id;
                        }
                        if($entrybillerAcc==""){
                            $entrybillerAcc=$this->site->getAccountByRefId4($this->getUser($data['user_id'])->biller_id,'billers')->id;
                        }
                        if($entrybillerAcc==""){
                            $entrybillerAcc=$this->site->getAccountByRefId5($this->getUser($data['user_id'])->biller_id,'billers')->id;
                        }

                        if($entrybillerAcc=="")
						$entrybillerAcc=$this->site->getAccountsSettings()->accounts_main_boxes_acct;

					$entryDebitAccount[] =  $entrybillerAcc;

					$entryCreditAccount= [];

					$entryCreditAccount[]=$this->getPaymentTypeByPaymentType('cash')->payment_account;


			    $this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['cash_in_hand']),$this->sma->formatDecimal($data['cash_in_hand']),$reg_id,$entry_type);

			}

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
        $this->site->setPurchaseItem_return($clause, $quantity);
        $this->site->syncQuantity(null, null, null, $product_id);
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

    public function get_return_sale_id($Offline_Pos_id,$warehouse_id)
    {
        $q = $this->db->get_where('sales', ['Offline_Pos_id' => $Offline_Pos_id,'warehouse_id' => $warehouse_id], 1);
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

	public function getSalesTax($biller_id,$sale_status,$start_date = null, $end_date = null)
    {
        $this->db->select_sum('total_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')->select_sum('shipping');
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
		$this->db->where('biller_id', $biller_id);
        $this->db->where('sale_status', $sale_status);
        $this->db->where('sale_status', 'payment_status');
        $this->db->where('pos', '1');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getToatalSales($biller_id,$start_date = null, $end_date = null)
    {

		   $this->db->select('sum(net_unit_price * unit_quantity) as total')->select('sum(item_tax) as total_tax')->select('sum(item_cost_value) as item_cost_value')
			 ->join('sale_items', 'sale_items.sale_id = sales.id', 'left');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
			if ($start_date) {
            $this->db->where('sales.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('sales.date <=', $end_date);
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

		   $this->db->select('sum(amount) as amount')
			 ->join('payments', 'payments.sale_id = sales.id', 'left');
			//$this->db->where('sales.sale_status =', 'completed');

			//$this->db->where('sales.paid !=', 0);
			if ($start_date) {
            $this->db->where('sales.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('sales.date <=', $end_date);
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




}
