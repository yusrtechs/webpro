<?php
//ini_set('max_user_connections', '50');
defined('BASEPATH') or exit('No direct script access allowed');

class Site extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
		$this->db = $this->dynamicdb();
        //$this->db->cachedir='app/cache/'.$this->session->userdata('company').'/'.$this->session->userdata('username');
    }

	public function dynamicdb()
	{
		$company = $this->session->userdata('company');
		if (isset($company) && $this->db->database!=$company)
		{

            /*if (!file_exists('app/cache/'.$company.'/'.$this->session->userdata('username'))) {
                mkdir('app/cache/'.$company.'/'.$this->session->userdata('username'), 0777, true);
            }*/

			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'databaseuser';
            $config['password'] = 'enter-password-cpeapi';
            $config['database'] = 'cpesoft_'.$company;
            /*$config['username'] = 'root';
            $config['password'] = '';
            $config['database'] = $company;*/
            $config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = 'sma_';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = FALSE;
            $config['cache_on'] = FALSE;
			$config['cachedir'] = '';
            //$config['cache_on'] = TRUE;
			//$config['cachedir'] = 'app/cache/'.$company.'/'.$this->session->userdata('username');
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

			return $data;
		}
		else
		return $this->db;
	}

	public function posdynamicdb($company)
	{
		if (isset($company) && $this->db->database!=$company)
		{

            /*if (!file_exists('app/cache/'.$company.'/'.$this->session->userdata('username'))) {
                mkdir('app/cache/'.$company.'/'.$this->session->userdata('username'), 0777, true);
            }*/

			$config['dsn'] = '';
			$config['hostname'] = 'localhost';
			$config['username'] = 'databaseuser';
            $config['password'] = 'enter-password-cpeapi';
            $config['database'] = 'cpesoft_'.$company;
            /*$config['username'] = 'root';
            $config['password'] = '';
            $config['database'] = $company;*/
            $config['dbdriver'] = 'mysqli';
			$config['dbprefix'] = 'sma_';
			$config['pconnect'] = FALSE;
			$config['db_debug'] = FALSE;
			$config['cache_on'] = FALSE;
			$config['cachedir'] = '';
            //$config['cache_on'] = TRUE;
			//$config['cachedir'] = 'app/cache/'.$company.'/'.$this->session->userdata('username');
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
			return $data;
		}
		else
		{
			return $this->db;
		}
	}


    public function apidynamicdb($company)
	{
		if (isset($company) && $this->db->database!=$company)
		{

            /*if (!file_exists('app/cache/'.$company.'/'.$this->session->userdata('username'))) {
                mkdir('app/cache/'.$company.'/'.$this->session->userdata('username'), 0777, true);
            }*/

			$config['dsn'] = '';
			$config['hostname'] = 'localhost';
			$config['username'] = 'databaseuser';
            $config['password'] = 'enter-password-cpeapi';
            $config['database'] = 'cpesoft_'.$company;
            /*$config['username'] = 'root';
            $config['password'] = '';
            $config['database'] = $company;*/
            $config['dbdriver'] = 'mysqli';
			$config['dbprefix'] = 'sma_';
			$config['pconnect'] = FALSE;
			$config['db_debug'] = FALSE;
			$config['cache_on'] = FALSE;
			$config['cachedir'] = '';
            //$config['cache_on'] = TRUE;
			//$config['cachedir'] = 'app/cache/'.$company.'/'.$this->session->userdata('username');
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
			return $data;
		}
		else
		{
			return $this->db;
		}
	}

	public function posdynamicdb_IPAD($company)
	{
			$config['dsn'] = '';
			$config['hostname'] = 'localhost';
			$config['username'] = 'databaseuser';
            $config['password'] = 'enter-password-cpeapi';
            //$config['username'] = 'root';
            //$config['password'] = '';
            $config['database'] = 'cpesoft_'.$company;
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
			return $data;
		}


	public function connectDB($db="empty")
	{

		$config['dsn'] = '';
		$config['hostname'] = 'db';
		$config['username'] = 'cpesoft';
		$config['password'] = 'J@okhYj[k3i]';
		$config['database'] = 'cpesoft_'.$db;
		$config['dbdriver'] = 'mysqli';
		$config['dbprefix'] = 'sma_';
		$config['pconnect'] = FALSE;
		$config['db_debug'] = true;
		$config['cache_on'] = true;
		$config['cachedir'] = 'cache';
		$config['char_set'] = 'utf8';
		$config['dbcollat'] = 'utf8_general_ci';
		$config['cachedir'] = '';
		$config['swap_pre'] = '';
		$config['encrypt'] = FALSE;
		$config['compress'] = FALSE;
		$config['stricton'] = FALSE;
		$config['failover'] = array();
		$config['save_queries'] = true;

		$CI = &get_instance();
		$data = $CI->load->database($config, TRUE);
		return $data;
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
        if ($quantity > 0 && !$this->Settings->overselling) {
            $this->session->set_flashdata('error', sprintf(lang('quantity_out_of_stock_for').' %s', $product_name));
            redirect($_SERVER['HTTP_REFERER']);
        } elseif ($quantity != 0) {
            $cost[] = ['date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => null, 'quantity' => $quantity, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => (0 - $quantity), 'overselling' => 1, 'inventory' => 1];
            $cost[] = ['pi_overselling' => 1, 'product_id' => $product_id, 'quantity_balance' => (0 - $quantity), 'warehouse_id' => $warehouse_id, 'option_id' => $option_id];
        }
        return $cost;
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
            $this->session->set_flashdata('error', sprintf(lang('quantity_out_of_stock_for').' %s', (isset($pi->product_name) ? $pi->product_name : $product_name)));
            redirect($_SERVER['HTTP_REFERER']);
        }
        return $cost;
    }

    public function calculateDiscount($discount = null, $amount, $tobacco = null)
    {
        if ($discount && $this->Settings->product_discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {
                $pds = explode('%', $discount);
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($tobacco ? ($amount/2) : $amount)) * (float) ($pds[0])) / 100), 12);
            }
            return $this->sma->formatDecimal($discount, 12);
        }
        return 0;
    }

    public function calculateOrderTax($order_tax_id = null, $amount)
    {
        if ($this->Settings->tax2 != 0 && $order_tax_id) {
            if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                if ($order_tax_details->type == 1) {
                    return $this->sma->formatDecimal((($amount * $order_tax_details->rate) / 100), 12);
                }
                return $this->sma->formatDecimal($order_tax_details->rate, 12);
            }
        }
        return 0;
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

    public function check_customer_deposit($customer_id, $amount)
    {
        $customer = $this->getCompanyByID($customer_id);
        return $customer->deposit_amount >= $amount;
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

    public function checkPermissions()
    {
        if(isset($this->Settings->isOfflineOnly) && $this->Settings->isOfflineOnly==1){
            $clause[0]['products-barcode']   = 1;
            return $clause;
        }
        $q = $this->db->get_where('permissions', ['group_id' => $this->session->userdata('group_id')], 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return false;
    }
    public function IsPermissions($per)
    {
		if ($this->Owner || $this->Admin)
			return true;
			else{

		$group_id=$this->session->userdata('group_id');
		$this->db->select('*')
        ->where('group_id', $group_id)->where($per.'=', 1);
        $q = $this->db->get('permissions');
        if ($q->num_rows() > 0) {
            /*foreach (($q->result()) as $row) {
                $data[] = $row;
            }*/
            return true;
        }
			}
		return false;


		/*

		$q = $this->db->get_where('permissions', ['group_id' => $this->session->userdata('group_id')], 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return false;
		*/
    }

    public function checkSlug($slug, $type = null)
    {
        if (!$type) {
            return $this->db->get_where('products', ['slug' => $slug], 1)->row();
        } elseif ($type == 'category') {
            return $this->db->get_where('categories', ['slug' => $slug], 1)->row();
        } elseif ($type == 'brand') {
            return $this->db->get_where('brands', ['slug' => $slug], 1)->row();
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

    public function costing($items)
    {
        $citems = [];
        foreach ($items as $item) {
            $option            = (isset($item['option_id']) && !empty($item['option_id']) && $item['option_id'] != 'null' && $item['option_id'] != 'false') ? $item['option_id'] : NULL;
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
                $wh          = $this->Settings->overselling ? null : $item['warehouse_id'];
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
								foreach ($combo_items2 as $combo_item2) {
							if ($combo_item2->type == 'standard' || $combo_item2->type == 'manufacturing' ) {
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
								}
                    }
					}

					}



                }
            }
        }
        // $this->sma->print_arrays($combo_items, $citems);
        $cost = [];
        foreach ($citems as $item) {
            $item['aquantity'] = $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'];
            $cost[]            = $this->item_costing($item, true);
        }
        return $cost;
    }

    public function get_expiring_qty_alerts()
    {
        $date = date('Y-m-d', strtotime('+3 months'));
        $this->db->select('SUM(quantity_balance) as alert_num')
        ->where('expiry !=', null)->where('expiry !=', '0000-00-00')
        ->where('expiry <', $date);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return (int) $res->alert_num;
        }
        return false;
    }

    public function get_setting()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function get_shop_payment_alerts()
    {
        $this->db->where('shop', 1)->where('attachment !=', null)->where('payment_status !=', 'paid');
        return $this->db->count_all_results('sales');
    }

    public function get_shop_sale_alerts()
    {
        $this->db->join('deliveries', 'deliveries.sale_id=sales.id', 'left')
        ->where('sales.shop', 1)->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
        ->group_start()->where('deliveries.status !=', 'delivered')->or_where('deliveries.status IS NULL', null)->group_end();
        return $this->db->count_all_results('sales');
    }

    public function get_total_qty_alerts()
    {
        $this->db->group_start()->where('quantity <= alert_quantity', null, false)->or_where('quantity', null)->group_end()->where('track_quantity', 1);
        return $this->db->count_all_results('products');
    }

    public function getAddressByID($id)
    {
        return $this->db->get_where('addresses', ['id' => $id], 1)->row();
    }

    public function getAllBaseUnits()
    {
        $q = $this->db->get_where('units', ['base_unit' => null]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllBrands()
    {
        $q = $this->db->get('brands');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCategories()
    {
        $this->db->where('parent_id', null)->or_where('parent_id', 0)->order_by('name');
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($this->Settings->user_language != 'arabic'){
                    if($row->nameEn)
                    $row->name=$row->nameEn;

                }
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCategoriesN0HidePOS()
    {
        $this->db->where('hide_pos', 0);
		$this->db->where('parent_id', 0)->order_by('name');
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCompanies($group_name,$grpId = null,$coId = null,$brId = null)
    {
            if($grpId){
            $this->db->where('grpId', $grpId);
           }
           if($coId){
            $this->db->where('coId', $coId);
           }
           if($brId){
            $this->db->where('brId', $brId);
           }
        $q = $this->db->get_where('companies', ['group_name' => $group_name]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCompaniesType($group_name,$co_type)
    {
        $q = $this->db->get_where('companies', ['group_name' => $group_name,'co_type' => $co_type]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getAllResturantEmp($emp_type)
    {
        $q = $this->db->get_where('resturant_emp', ['emp_type' => $emp_type]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCurrencies()
    {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllPurchaseItems($purchase_id)
    {
        $q = $this->db->get_where('purchase_items', ['purchase_id' => $purchase_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllWarehouses($grpId = null,$coId = null,$brId = null)
    {
            if($grpId){
            $this->db->where('grpId', $grpId);
           }
           if($coId){
            $this->db->where('coId', $coId);
           }
           if($brId){
            $this->db->where('brId', $brId);
           }
            $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($this->Settings->user_language != 'arabic'){
                    if($row->nameEn)
                    $row->name=$row->nameEn;
                    if($row->branch_nameEn)
                    $row->branch_name=$row->branch_nameEn;
                }
                $data[] = $row;

            }
            return $data;
        }
        return false;
    }
    public function getAllWarehouses_open_balance()
    {


        //$this->db->select('distinct warehouse_id');
		$q = $this->db->get('adjustments');

        //$q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data1[] = $row;

				//$q1 = $this->db->get_where('warehouses', ['id' => $row->warehouse_id], 1);

            }

			      for($i=0;$i<$q->num_rows();$i++)
				  $this->db->where('warehouses.id <>', $data1[$i]->warehouse_id);

                $q1 = $this->db->get('warehouses');
				if ($q1->num_rows() > 0) {
				foreach (($q1->result()) as $row1) {
                $data[] = $row1;


			    }

            return $data;
				}
				return false;
        }else{
			$q1 = $this->db->get('warehouses');
				//$q1 = $this->db->get_where('warehouses', ['id' => $row->warehouse_id], 1);
				foreach (($q1->result()) as $row1) {
                $data[] = $row1;


			    }
            return $data;
		}
        return false;

    }

    public function getBrandByID($id)
    {
        $q = $this->db->get_where('brands', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCategoryByID($id)
    {
        $q = $this->db->get_where('categories', ['id' => $id], 1);
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

    public function getCurrencyByCode($code)
    {
        $q = $this->db->get_where('currencies', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCustomerGroupByID($id)
    {
        $q = $this->db->get_where('customer_groups', ['id' => $id], 1);
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

    public function getGiftCardByID($id)
    {
        $q = $this->db->get_where('gift_cards', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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

    public function getNotifications()
    {
        $date = date('Y-m-d H:i:s', time());
        $this->db->where('from_date <=', $date);
        $this->db->where('till_date >=', $date);
        if (!$this->Owner) {
            if ($this->Supplier) {
                $this->db->where('scope', 4);
            } elseif ($this->Customer) {
                $this->db->where('scope', 1)->or_where('scope', 3);
            } elseif (!$this->Customer && !$this->Supplier) {
                $this->db->where('scope', 2)->or_where('scope', 3);
            }
        }
        $q = $this->db->get('notifications');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPriceGroupByID($id)
    {
        $q = $this->db->get_where('price_groups', ['id' => $id], 1);
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

    public function getProductComboItems($pid, $warehouse_id = null)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, combo_items.unit_price as unit_price, warehouses_products.quantity as quantity, warehouses_products.avg_cost as avg_cost')
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

    public function getProductComboItems_id($pid, $warehouse_id = null, $id = null)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, combo_items.unit_price as unit_price, warehouses_products.quantity as quantity, warehouses_products.avg_cost as avg_cost')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('combo_items.id');
        if ($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        if ($id) {
            $this->db->where('combo_items.item_id', $id);
        }
        $q = $this->db->get_where('combo_items', ['combo_items.product_id' => $pid]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductComboItemsWhitAVGCost($pid, $warehouse_id = null)
    {



		$this->db->select('warehouses_products.warehouse_id')->select('sum(avg_cost * ' . $this->db->dbprefix('combo_items') . '.quantity) as totalcost')
			->join('combo_items', 'combo_items.item_id=warehouses_products.product_id', 'left');
            $this->db->where('combo_items.product_id =', $pid);
            $this->db->group_by('warehouses_products.warehouse_id');

		if ($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id=', $warehouse_id);
        }
		$q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {

            foreach (($q->result()) as $row) {
                $data[] = $row;

                }
            return $data;
		}
        return false;
    }

    public function getProductGroupPrice($product_id, $group_id)
    {
        $q = $this->db->get_where('product_prices', ['price_group_id' => $group_id, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductGroupPriceByCode($product_code, $group_id)
    {
		$this->db->like('product_code',$product_code);
        $this->db->where('price_group_id',$group_id);
        $q = $this->db->get('product_prices');
        if ($q->num_rows() == 1) {
            return $q->row();
        }
        return false;
    }

    public function getProductGroupPriceByCodeMulti($product_code, $group_id)
    {
		$this->db->like('product_code',$product_code, 'both');
        $this->db->where('price_group_id',$group_id);
        $this->db->limit($limit);
        $q = $this->db->get('product_prices');

        if ($q->num_rows() >0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductGroupPriceWithUnit($product_id, $group_id, $product_unit_id)
    {

        $q = $this->db->get_where('product_prices', ['price_group_id' => $group_id, 'product_id' => $product_id, 'product_unit_id' => $product_unit_id], 1);
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

    public function getPurchaseByID($id)
    {
        $q = $this->db->get_where('purchases', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

     public function getExpenseByID($id)
    {
        $q = $this->db->get_where('expenses', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPurchasedItem($clause)
    {
        $orderby = empty($this->Settings->accounting_method) ? 'asc' : 'desc';
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

    public function getPurchasedItems($product_id, $warehouse_id, $option_id = null, $nonPurchased = false)
    {

        $orderby = empty($this->Settings->accounting_method) ? 'asc' : 'desc';
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

    public function getPurchasePayments($purchase_id)
    {
        $q = $this->db->get_where('payments', ['purchase_id' => $purchase_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getExpensePayments($expense_id)
    {
        $q = $this->db->get_where('expense_payments', ['expense_id' => $expense_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

    public function getReference($field,$brId = null)
    {

        if($brId && (isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1))
            $q = $this->db->get_where('order_ref', ['brId' => $brId], 1);
        else
            $q = $this->db->get_where('order_ref', ['ref_id' => '1'], 1);

        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'so':
                    $prefix = $this->Settings->sales_prefix;
                    break;
                case 'pos':
                    $prefix = isset($this->Settings->sales_prefix) ? $this->Settings->sales_prefix . '/POS' : '';
                    break;
                case 'qu':
                    $prefix = $this->Settings->quote_prefix;
                    break;
                 case 'of':
                    $prefix = $this->Settings->offer_prefix;
                    break;
                case 'po':
                    $prefix = $this->Settings->purchase_prefix;
                    break;
                case 'to':
                    $prefix = $this->Settings->transfer_prefix;
                    break;
                case 'do':
                    $prefix = $this->Settings->delivery_prefix;
                    break;
                case 'pay':
                    $prefix = $this->Settings->payment_prefix;
                    break;
                case 'ppay':
                    $prefix = $this->Settings->ppayment_prefix;
                    break;
                case 'ex':
                    $prefix = $this->Settings->expense_prefix;
                    break;
                case 're':
                    $prefix = $this->Settings->return_prefix;
                    break;
                case 'rep':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                case 'qa':
                    $prefix = $this->Settings->qa_prefix;
                    break;
                case 'rv':
                    $prefix = $this->Settings->revaluation_prefix;
                    break;
                case 'aipay':
                    $prefix = $this->Settings->aipayment_prefix;
                    break;
                default:
                    $prefix = '';
            }

            if($brId){
                if($brId && (isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) && $field == 'gl_enrty'){
                    $ref_no = $this->getMainBranchesByID($brId)->branch_prefix ? $this->getMainBranchesByID($brId)->branch_prefix.'-'.$prefix : $prefix;
                }else{
                    $ref_no = $this->getMainBranchesByID($brId)->branch_prefix ? $this->getMainBranchesByID($brId)->branch_prefix.'/'.$prefix : $prefix;
                }
            }else
               $ref_no = $prefix;

               if($brId && (isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) && $field == 'gl_enrty'){
                return $ref_no.''.$ref->{$field};
               }

            if ($this->Settings->reference_format == 1) {
                $ref_no .= date('Y') . '/' . sprintf('%04s', $ref->{$field});
            } elseif ($this->Settings->reference_format == 2) {
                $ref_no .= date('Y') . '/' . date('m') . '/' . sprintf('%04s', $ref->{$field});
            } elseif ($this->Settings->reference_format == 3) {
                $ref_no .= sprintf('%04s', $ref->{$field});
            } else {
                $ref_no .= $this->getRandomReference();
            }

            return $ref_no;
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

    public function getSaleByReference($ref)
    {
        $this->db->like('reference_no', $ref, 'both');
        $q = $this->db->get('sales', 1);
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

    public function getSmsSettings()
    {
        $q = $this->db->get('sms_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSubCategories($parent_id)
    {
        $this->db->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSubDepartments($parent_id)
    {
        $this->db->where('parent', $parent_id)->order_by('name');
        $q = $this->db->get('dept');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getTaxRateByID($id)
    {
        $q = $this->db->get_where('tax_rates', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTaxRateByIDV2($id)
    {
        $this->db->select('id');
        $q = $this->db->get_where('tax_rates', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getUnitByID($id)
    {
        $q = $this->db->get_where('units', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getUnitByID2($id)
    {
        $q = $this->db->get_where('units', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row()->name;
        }
        return false;
    }

    public function getUnitsByBUID($base_unit)
    {
        $this->db->where('id', $base_unit)->or_where('base_unit', $base_unit)
        ->group_by('id')->order_by('id asc');
        $q = $this->db->get('units');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getUnitsByBUIDV2($base_unit)
    {
        $this->db->select('id,name');
        $this->db->where('id', $base_unit)->or_where('base_unit', $base_unit)
        ->group_by('id')->order_by('id asc');
        $q = $this->db->get('units');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

    public function getUpcomingEvents()
    {
        $dt = date('Y-m-d');
        $this->db->where('start >=', $dt)->order_by('start')->limit(5);
        if ($this->Settings->restrict_calendar) {
            $this->db->where('user_id', $this->session->userdata('user_id'));
        }

        $q = $this->db->get('calendar');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
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

    public function getUserGroup($user_id = false)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $group_id = $this->getUserGroupID($user_id);
        $q        = $this->db->get_where('groups', ['id' => $group_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getUserGroupID($user_id = false)
    {
        $user = $this->getUser($user_id);
        return $user->group_id;
    }

    public function getWarehouseByID($id)
    {
        $q = $this->db->get_where('warehouses', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getWarehouseProduct($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
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

    public function getWarehouseProductsBywarehouse_id($warehouse_id)
    {


        $q = $this->db->get_where('warehouses_products', ['warehouse_id' => $warehouse_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
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

    public function item_costing($item, $pi = null)
    {
        $item_quantity = $pi ? $item['aquantity'] : $item['quantity'];
        if (!isset($item['option_id']) || empty($item['option_id']) || $item['option_id'] == 'null') {
            $item['option_id'] = null;
        }

        if ($this->Settings->accounting_method != 2 && !$this->Settings->overselling) {
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

    public function modal_js()
    {
        return '<script type="text/javascript" src="' . $this->data['assets'] . 'js/modal.js"></script>';
        //return '<script type="text/javascript">' . file_get_contents($this->data['assets'] . 'js/modal.js') . '</script>';
    }

    public function setPurchaseItem($clause, $qty)
    {
        if ($product = $this->getProductByID($clause['product_id'])) {
            if ($product->type == 'standard' || $product->type == 'manufacturing' ) {
            $product->cost=$product->cost ? $product->cost : 0;
            }
            if ($pi = $this->getPurchasedItem($clause)) {

                if ($pi->quantity_balance > 0) {
                    $quantity_balance = $pi->quantity_balance + $qty;
                    log_message('error', 'More than zero: ' . $quantity_balance . ' = ' . $pi->quantity_balance . ' + ' . $qty . ' PI: ' . print_r($pi, true));
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
                $tax_details           = $this->site->getTaxRateByID($product->tax_rate);
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
                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
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
            //return $clause;

            log_message('error', 'Why else: ' . print_r($clause, true));
            return $this->db->insert('purchase_items', $clause);
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
                $tax_details           = $this->site->getTaxRateByID($product->tax_rate);
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
                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
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

    public function setPurchaseItem_temp($clause, $qty,$cost,$expiry)
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
                return $this->db->update('purchase_items', ['quantity_balance' => $quantity_balance], ['id' => $pi->id]);
            }
            $unit                        = $this->getUnitByID($product->unit);
            $clause['product_unit_id']   = $product->unit;
            $clause['product_unit_code'] = $unit->code;
            $clause['product_code']      = $product->code;
            $clause['product_name']      = $product->name;
            $clause['purchase_id']       = $clause['transfer_id']       = $clause['item_tax']       = null;
            $clause['net_unit_cost']     = $clause['real_unit_cost']     = $clause['unit_cost']     = $cost;
            $clause['quantity_balance']  = $clause['quantity']  = $clause['unit_quantity']  = $clause['quantity_received']  = $qty;
            $clause['subtotal']          = ($cost * $qty);
            if (isset($product->tax_rate) && $product->tax_rate != 0) {
                $tax_details           = $this->site->getTaxRateByID($product->tax_rate);
                $ctax                  = $this->calculateTax($product, $tax_details, $cost);
                $item_tax              = $clause['item_tax']              = $ctax['amount'];
                $tax                   = $clause['tax']                   = $ctax['tax'];
                $clause['tax_rate_id'] = $tax_details->id;
                if ($product->tax_method != 1) {
                    $clause['net_unit_cost'] = $cost - $item_tax;
                    $clause['unit_cost']     = $cost;
                } else {
                    $clause['net_unit_cost'] = $cost;
                    $clause['unit_cost']     = $cost + $item_tax;
                }
                $pr_item_tax = $this->sma->formatDecimal($item_tax * $clause['unit_quantity'], 12);
                if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                    $clause['gst']  = $gst_data['gst'];
                    $clause['cgst'] = $gst_data['cgst'];
                    $clause['sgst'] = $gst_data['sgst'];
                    $clause['igst'] = $gst_data['igst'];
                }
                $clause['subtotal'] = (($clause['net_unit_cost'] * $clause['unit_quantity']) + $pr_item_tax);
            }
            $clause['expiry']    = $expiry;
            $clause['status']    = 'received';
            $clause['date']      = date('Y-m-d');
            $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : null;
            log_message('error', 'Why else: ' . print_r($clause, true));
            return $this->db->insert('purchase_items', $clause);
        }
        return false;
    }

    public function syncProductQty($product_id, $warehouse_id)
    {
        $balance_qty    = $this->getBalanceQuantity($product_id);
        $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id);
        if ($this->db->update('products', ['quantity' => $balance_qty], ['id' => $product_id])) {
            if ($this->getWarehouseProducts($product_id, $warehouse_id)) {
                $this->db->update('warehouses_products', ['quantity' => $wh_balance_qty], ['product_id' => $product_id, 'warehouse_id' => $warehouse_id]);
            $productid=$this->getProductByID($product_id)->code;
			if($this->db->update('synch_products', ['api' => 0], ['warehouse_id' => $warehouse_id, 'product_code' => $productid])){

			}


			} else {
                if (!$wh_balance_qty) {
                    $wh_balance_qty = 0;
                }
                $product = $this->site->getProductByID($product_id);
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

    public function syncProductQty_count($product_id, $warehouse_id,$qty)
    {
        $balance_qty    = $this->getBalanceQuantity($product_id);
        $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id);

        //if ($this->db->update('products', ['quantity' => $balance_qty], ['id' => $product_id])) {
            if ($this->getWarehouseProducts($product_id, $warehouse_id)) {
                $this->db->update('warehouses_products', ['quantity' => $qty], ['product_id' => $product_id, 'warehouse_id' => $warehouse_id]);
            $productid=$this->getProductByID($product_id)->code;
			if($this->db->update('synch_products', ['api' => 0], ['warehouse_id' => $warehouse_id, 'product_code' => $productid])){

			}


			return true;
			} else {
                if (!$wh_balance_qty) {
                    $wh_balance_qty = 0;
                }
                $product = $this->site->getProductByID($product_id);
                if ($product) {
                    $this->db->insert('warehouses_products', ['quantity' => $wh_balance_qty, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'avg_cost' => $product->cost]);
                } else {
                    $this->db->insert('warehouses_products', ['quantity' => $wh_balance_qty, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id]);
                }
				return true;
            }

        //}
        return false;
    }

    public function syncProductQty_temp($product_id, $warehouse_id,$cost)
    {
        $balance_qty    = $this->getBalanceQuantity($product_id);
        $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id);
        if ($this->db->update('products', ['quantity' => $balance_qty,'cost' => $cost], ['id' => $product_id])) {
            if ($this->getWarehouseProducts($product_id, $warehouse_id)) {
                $this->db->update('warehouses_products', ['quantity' => $wh_balance_qty,'avg_cost' => $cost], ['product_id' => $product_id, 'warehouse_id' => $warehouse_id]);
            $productid=$this->getProductByID($product_id)->code;
			if($this->db->update('synch_products', ['api' => 0], ['warehouse_id' => $warehouse_id, 'product_code' => $productid])){

			}

			} else {
                if (!$wh_balance_qty) {
                    $wh_balance_qty = 0;
                }
                $product = $this->site->getProductByID($product_id);
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

    public function syncPurchaseItems_returnSales($product_id,$warehouse_id,$item_cost_value)
    {
                        $clause    = ['purchase_id' => null, 'transfer_id' => null, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'option_id' => null];
                        if ($pi = $this->getPurchasedItem($clause)) {
                            $total_item_cost_value  = $this->sma->formatDecimal($pi->item_cost_value  + $item_cost_value,12);
                            $this->db->update('purchase_items', ['item_cost_value' => $total_item_cost_value], ['id' => $pi->id]);

						}else
                    return false;
    }


 public function syncPurchasePayments($id)
    {
        $purchase = $this->getPurchaseByID($id);
        $paid     = 0;
        $discount_earned     = 0;
        if ($payments = $this->getPurchasePayments($id)) {
            foreach ($payments as $payment) {
                $paid += $payment->amount;
                $discount_earned += $payment->commercial_discount;
            }
        }

        $payment_status = $paid <= 0 ? 'pending' : $purchase->payment_status;
        if ($this->sma->formatDecimal($purchase->grand_total) > $this->sma->formatDecimal($paid + $discount_earned) && $paid > 0) {
            $payment_status = 'partial';
        } elseif ($this->sma->formatDecimal($purchase->grand_total) <= $this->sma->formatDecimal($paid + $discount_earned)) {
            $payment_status = 'paid';
        }

        if ($this->db->update('purchases', ['paid' => $paid, 'discount_earned' => $discount_earned, 'payment_status' => $payment_status], ['id' => $id])) {
            return true;
        }

        return false;
    }

    public function syncExpensePayments($id)
    {
        $expense = $this->getExpenseByID($id);
        $paid     = 0;
        if ($payments = $this->getExpensePayments($id)) {
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
        }

        $payment_status = $paid <= 0 ? 'pending' : $expense->payment_status;
        if ($this->sma->formatDecimal($expense->grand_total) > $this->sma->formatDecimal($paid) && $paid > 0) {
            $payment_status = 'partial';
        } elseif ($this->sma->formatDecimal($expense->grand_total) <= $this->sma->formatDecimal($paid)) {
            $payment_status = 'paid';
        }

        if ($this->db->update('expenses', ['paid' => $paid, 'payment_status' => $payment_status], ['id' => $id])) {
            return true;
        }

        return false;
    }

    public function syncQuantity($sale_id = null, $purchase_id = null, $oitems = null, $product_id = null)
    {
        if ($sale_id) {
            $sale_items = $this->getAllSaleItems($sale_id);
            foreach ($sale_items as $item) {
                if ($item->product_type == 'standard' || $item->product_type == 'manufacturing' ) {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }
                } elseif ($item->product_type == 'combo') {
                    $wh          = $this->Settings->overselling ? null : $item->warehouse_id;
                    $combo_items = $this->getProductComboItems($item->product_id, $wh);
                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard'  || $combo_item->type == 'manufacturing') {
                            $this->syncProductQty($combo_item->id, $item->warehouse_id);
                        }else if ($combo_item->type == 'combo') {
							$combo_items2 = $this->getProductComboItems($combo_item->id, $wh);
							foreach ($combo_items2 as $combo_item2) {


								if ($combo_item2->type == 'standard' || $combo_item2->type == 'manufacturing' ) {
									$this->syncProductQty($combo_item2->id, $item->warehouse_id);
								}
							}
                        }
                    }
                }
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
                    if ($item->product_type == 'standard' || $item->product_type == 'manufacturing' ) {
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
                if ($product_variants = $this->getProductVariants($product_id)) {
                    foreach ($product_variants as $pv) {
                        $this->syncVariantQty($pv->id, $warehouse->id, $product_id);
                        $this->checkOverSold($product_id, $warehouse->id, $pv->id);
                    }
                }
            }
        }
    }

    public function syncSalePayments($id)
    {
        $sale = $this->getSaleByID($id);
        if ($payments = $this->getSalePayments($id)) {
            $paid        = 0;
            $discount_allowance        = 0;
            $donation        = 0;
            $grand_total = $sale->grand_total + $sale->rounding;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
                $discount_allowance += $payment->commercial_discount;
                $donation += $payment->donation;
            }

            $payment_status = $paid == 0 ? 'pending' : $sale->payment_status;
            if ($this->sma->formatDecimal($grand_total) == 0 || $this->sma->formatDecimal($grand_total) == $this->sma->formatDecimal($paid + $discount_allowance - $donation)) {
                $payment_status = 'paid';
            } elseif ($sale->due_date <= date('Y-m-d') && !$sale->sale_id) {
                $payment_status = 'due';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }

            if ($this->db->update('sales', ['paid' => $paid, 'donation' => $donation, 'discount_allowance' => $discount_allowance, 'payment_status' => $payment_status], ['id' => $id])) {
                return true;
            }
        } else {
            $payment_status = ($sale->due_date <= date('Y-m-d')) ? 'due' : 'pending';
            if ($this->db->update('sales', ['paid' => 0, 'donation' => 0, 'discount_allowance' => 0, 'payment_status' => $payment_status], ['id' => $id])) {
                return true;
            }
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

    public function updateInvoiceStatus()
    {
        $date = date('Y-m-d');
        $q    = $this->db->get_where('invoices', ['status' => 'unpaid']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($row->due_date < $date) {
                    $this->db->update('invoices', ['status' => 'due'], ['id' => $row->id]);
                }
            }
            $this->db->update('settings', ['update' => $date], ['setting_id' => '1']);
            return true;
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


    private function getBalanceQuantity_old($product_id, $warehouse_id = null)
    {
        $this->db->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (quantity_received) ELSE (unit_quantity) END) as stock', false);
        $this->db->where('product_id', $product_id)->where('unit_quantity !=', 0);
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

     public function getBalanceQuantity_temp($product_id, $warehouse_id = null)
    {
        $this->db->select('SUM(COALESCE(quantity_received, 0)) as stock', false);
        $this->db->where('product_id', $product_id)->where('quantity_received !=', 0);
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

    public function getBalanceQuantity_avg_cost($product_id, $warehouse_id = null)
    {
        $this->db->select('SUM(COALESCE(total_qty_prev, 0)) as stock', false);
        $this->db->where('product_id', $product_id);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('warehouses_products_avg_cost');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

    public function getProduct_avg_cost($product_id, $warehouse_id = null)
    {
        $this->db->select('(COALESCE(avg_cost, 0)) as stock', false);
        $this->db->where('product_id', $product_id);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
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

    public function getAllTaxRatesActive()
    {
        $q = $this->db->get_where('tax_rates',['tax_active' => 1,'category_id' => 1]);

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
    public function distributeAmountEqually($original, $amount, $total)
    {

		    return $this->sma->formatDecimal($original / $total * $amount,12);
    }

    public function getAllPurchaseItemsQuantity($purchase_id)
    {
        $this->db->select_sum('quantity');
		$this->db->where('purchase_id =', $purchase_id);
		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllPurchaseItemQuantityRealCost($purchase_id,$product_id=null)
    {
        $this->db->select_sum('quantity')->select_sum('quantity_received')->select_sum('real_unit_cost')->select_sum('net_unit_cost')->select_sum('shipping');
		$this->db->where('purchase_id =', $purchase_id);
		$this->db->where('product_id =', $product_id);
		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllPurchaseItemsItem($purchase_id,$product_id,$product_unit_id=null)
    {
        $this->db->select('*');
		$this->db->where('purchase_id =', $purchase_id);
		$this->db->where('product_id =', $product_id);
        if($product_unit_id){
            $this->db->where('product_unit_id =', $product_unit_id);

        }
		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getAllPurchaseItemsItemCount($product_id)
    {
        $this->db->select('*');
		$this->db->where('product_id =', $product_id);
		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllSalesItemsItemCount($product_id)
    {
        $this->db->select('*');
		$this->db->where('product_id =', $product_id);
		$q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getPromo_typeByID($id) {
        $q = $this->db->get_where('promo_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

	public function getPromo_ByID($id) {
        $q = $this->db->get_where('promo', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   		 }


	public function getAccountByRefId($account_ref_id,$account_type) {


        if($account_type=='local_suppliers' || $account_type=='international_suppliers'){
            $customer_ref_id           = $this->site->getCompanyByID($account_ref_id)->customer_ref_id;
        }else{
            $customer_ref_id           =null;
        }
        if($customer_ref_id){
            $q = $this->db->get_where('gl_accounts', array('account_ref_id' => $customer_ref_id), 1);
        }else{
            $q = $this->db->get_where('gl_accounts', array('account_ref_id' => $account_ref_id,'account_type' => $account_type), 1);
        }
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   		 }

	public function createEntry($entryDebitAccount,$entryCreditAccount,$balance,$reference_no,$entryType,$currency_code = NULL,$currency_rate = NULL, $entryDebitDescription = NULL, $entryCreditDescription = NULL) {

        if(!$currency_code){
            $currency_code=$this->Settings->default_currency;
        }
        if(!$currency_rate){
            $currency_rate=$this->site->getCurrencyByCode($currency_code)->rate;
        }

        $entryDebitAccount=json_encode((Array) $entryDebitAccount);
		$entryCreditAccount=json_encode((Array) $entryCreditAccount);
		$balance=json_encode((Array) $this->sma->formatDecimal($balance));

        if(!$entryDebitDescription){
			$entryDebitDescription="";
        }else{
            $entryDebitDescription=json_encode((Array) $entryDebitDescription);
        }
        if(!$entryCreditDescription){
			$entryCreditDescription="";
        }else{
            $entryCreditDescription=json_encode((Array) $entryCreditDescription);
        }

		$clause = ['reference_no' => $reference_no, 'created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => 'Automated_Accounting_Entry', 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $balance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $balance, 'entryStatus' => 1, 'currency_code' => $currency_code, 'currency_rate' => $currency_rate, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription];

		 if($this->db->insert('gl_entries', $clause)){
			$entryID = $this->db->insert_id();
			$this->approve($entryID,'Automated_Accounting_Entry');
			return true;
		}
		return false;

   		 }




	public function createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference_no,$entryType,$costCenterDebitId = NULL,$costCenterDebitAmount = NULL,$costCenterCreditId = NULL,$costCenterCreditAmount = NULL,$entryDate = NULL,$entryDescription = NULL,$grpId = NULL,$coId = NULL,$brId = NULL, $entryDebitDescription = NULL, $entryCreditDescription = NULL,$currency_code = NULL,$currency_rate = NULL) {

		if(!$currency_code){
            $currency_code=$this->Settings->default_currency;
        }
        if(!$currency_rate){
            $currency_rate=$this->site->getCurrencyByCode($currency_code)->rate;
        }

        if(!$costCenterDebitId)
			$costCenterDebitId="";
		if(!$costCenterDebitAmount)
			$costCenterDebitAmount="";
		if(!$costCenterCreditId)
			$costCenterCreditId="";
		if(!$costCenterCreditAmount)
			$costCenterCreditAmount="";


		if(!$entryDescription)
			$entryDescription="Automated_Accounting_Entry";



		$entryDebitAccount=json_encode((Array) $entryDebitAccount);
		$entryCreditAccount=json_encode((Array) $entryCreditAccount);
		$entryDebitBalance=json_encode((Array) $entryDebitBalance);
		$entryCreditBalance=json_encode((Array) $entryCreditBalance);
        if(!$entryDebitDescription){
			$entryDebitDescription="";
        }else{
            $entryDebitDescription=json_encode((Array) $entryDebitDescription);
        }
        if(!$entryCreditDescription){
			$entryCreditDescription="";
        }else{
            $entryCreditDescription=json_encode((Array) $entryCreditDescription);
        }

		if($entryDate)
			$clause = ['reference_no' => $reference_no, 'created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryDate' => $entryDate, 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription, 'currency_code' => $currency_code, 'currency_rate' => $currency_rate];
        else
			$clause = ['reference_no' => $reference_no, 'created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription, 'currency_code' => $currency_code, 'currency_rate' => $currency_rate];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
                $clause  ['grpId']=$grpId ? $grpId : 1;
                $clause  ['coId']=$coId ? $coId : 1;
                $clause  ['brId']=$brId ? $brId : 1;
                $brId=$brId ? $brId : 1;
                $clause  ['entry_no']=$this->getReference('gl_enrty',$brId ? $brId  : null);//$this->getMainBranchesByID($brId)->branch_prefix ? $this->getMainBranchesByID($brId)->branch_prefix.'-'.$this->getCountEntriesBybrid($brId)->count : $this->getCountEntriesBybrid($brId)->count;
            }

        if($this->db->insert('gl_entries', $clause)){
			$entryID = $this->db->insert_id();
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
            $this->updateReference('gl_enrty',$brId ? $brId  : null);
            }
			$this->approve($entryID,$entryDescription);
			return true;
		}
		return false;
   		 }

	public function updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference_no,$entryType,$costCenterDebitId = NULL,$costCenterDebitAmount = NULL,$costCenterCreditId = NULL,$costCenterCreditAmount = NULL, $entryDebitDescription = NULL, $entryCreditDescription = NULL, $entryDate = NULL,$currency_code = NULL,$currency_rate = NULL) {

		$entries=$this->getEntriesByReference($reference_no,$entryType);

        if(!$currency_code){
            $currency_code=$this->Settings->default_currency;
        }
        if(!$currency_rate){
            $currency_rate=$this->site->getCurrencyByCode($currency_code)->rate;
        }

		if(!$costCenterDebitId)
			$costCenterDebitId="";
		if(!$costCenterDebitAmount)
			$costCenterDebitAmount="";
		if(!$costCenterCreditId)
			$costCenterCreditId="";
		if(!$costCenterCreditAmount)
			$costCenterCreditAmount="";

			$entryDescription=$this->sma->decode_html($entries->entryDescription);

		$entryDebitAccount=json_encode((Array) $entryDebitAccount);
		$entryCreditAccount=json_encode((Array) $entryCreditAccount);
		$entryDebitBalance=json_encode((Array) $entryDebitBalance);
		$entryCreditBalance=json_encode((Array) $entryCreditBalance);
        if(!$entryDebitDescription){
			$entryDebitDescription="";
        }else{
            $entryDebitDescription=json_encode((Array) $entryDebitDescription);
        }
        if(!$entryCreditDescription){
			$entryCreditDescription="";
        }else{
            $entryCreditDescription=json_encode((Array) $entryCreditDescription);
        }

		if($entryDate)
			$clause = ['reference_no' => $reference_no, 'created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryDate' => $entryDate, 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription, 'currency_code' => $currency_code, 'currency_rate' => $currency_rate];
        else
            $clause = ['reference_no' => $reference_no, 'created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription, 'currency_code' => $currency_code, 'currency_rate' => $currency_rate];


		//return $this->db->insert('gl_entries', $clause);
		$this->unapprove($entries->entryID,$entries->entryDescription);
        if($this->db->update('gl_entries', $clause,['reference_no' => $reference_no,'entryType' => $entryType])){
		$this->approve($entries->entryID,$entries->entryDescription);

			return true;
		}
        return false;
   		 }

	public function updateEntryMultiSideWithDelete($reference_no,$entryDescription) {

		$entries=$this->getEntriesByReferenceToDelete($reference_no,$entryDescription);



		//return $this->db->insert('gl_entries', $clause);
		//$this->unapprove($entries->entryID,$entries->entryDescription);
		if($this->db->delete('gl_entries',['reference_no' => $reference_no,'entryDescription' => $entries->entryDescription])){
		//$this->approve($entries->entryID,$entries->entryDescription);

			return true;
		}
        return false;
   		 }

	public function updateEntry($entryDebitAccount,$entryCreditAccount,$balance,$reference_no,$entryType,$currency_code = NULL,$currency_rate = NULL, $entryDebitDescription = NULL, $entryCreditDescription = NULL) {

        if(!$currency_code){
            $currency_code=$this->Settings->default_currency;
        }
        if(!$currency_rate){
            $currency_rate=$this->site->getCurrencyByCode($currency_code)->rate;
        }

        $entryDebitAccount=json_encode((Array) $entryDebitAccount);
		$entryCreditAccount=json_encode((Array) $entryCreditAccount);
		$balance=json_encode((Array) $this->sma->formatDecimal($balance));

        if(!$entryDebitDescription){
			$entryDebitDescription="";
        }else{
            $entryDebitDescription=json_encode((Array) $entryDebitDescription);
        }
        if(!$entryCreditDescription){
			$entryCreditDescription="";
        }else{
            $entryCreditDescription=json_encode((Array) $entryCreditDescription);
        }


		$clause = ['reference_no' => $reference_no, 'created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => 'Automated_Accounting_Entry', 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $balance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $balance, 'entryStatus' => 1, 'currency_code' => $currency_code, 'currency_rate' => $currency_rate, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription];

		$entries=$this->getEntriesByReference($reference_no,$entryType);
		$this->unapprove($entries->entryID,'Automated_Accounting_Entry');
        if($this->db->update('gl_entries', $clause,['reference_no' => $reference_no,'entryType' => $entryType])){
		$this->approve($entries->entryID,'Automated_Accounting_Entry');
        	return true;
		}
        return false;
   	}

	public function getAccountsSettings()
    {
        $q = $this->db->get('gl_accounts_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
     public function getEntryCount($reference_no,$entryType)
    {
        $q = $this->db->get_where('gl_entries',['reference_no' => $reference_no,'entryType' => $entryType]);
        if ($q->num_rows() > 0) {
            return true;
        }
        return false;
    }

	public function getEntriesByReference($reference_no,$entryType)
	{


        $q = $this->db->get_where('gl_entries',['reference_no' => $reference_no,'entryType' => $entryType]);
        if ($q->num_rows() > 0) {
           return $q->row();
        }
        return false;
		}

	public function getEntriesByReferenceToDelete($reference_no,$entryDescription)
	{


        $q = $this->db->get_where('gl_entries',['reference_no' => $reference_no,'entryDescription' => $entryDescription]);
        if ($q->num_rows() > 0) {
           return $q->row();
        }
        return false;
		}

	public function addOpeningBalance($data, $acounts)
    {
        if ($this->db->insert('gl_opening_balance', $data)) {
            $openingId = $this->db->insert_id();
            foreach ($acounts as $acount) {
                $acount['openingId'] = $openingId;
                $this->db->insert('gl_opening_balance_items', $acount);
                $this->db->update('gl_accounts', ['openingBalance' => $acount['balance']], ['id' => $acount['accountID']]);
            }

            return true;
        }
        return false;
    }


	public function updateOpeningBalance($id, $data, $accounts)
    {
        $this->reverseOpeningBalance($id);
        if ($this->db->update('gl_opening_balance', $data, ['id' => $id]) && $this->db->delete('gl_opening_balance_items', ['openingId' => $id])) {
            foreach ($accounts as $account) {
                $account['openingId'] = $id;
                $this->db->insert('gl_opening_balance_items', $account);
                $this->db->update('gl_accounts', ['openingBalance' => $account['balance']], ['id' => $account['accountID']]);


            }
            return true;
        }
        return false;
    }

		public function reverseOpeningBalance($id)
    {
        if ($OBs = $this->getOpeningBalanceItems($id)) {
            foreach ($OBs as $ob) {

				$this->db->update('gl_accounts', ['openingBalance' => 0], ['id' => $ob->accountID]);

            }
        }
    }

		public function getOpeningBalanceItems($openingId)
    {
          $this->db->select('gl_opening_balance_items.*,gl_accounts.accountSide,gl_accounts.id as account_id , gl_accounts.accountID as account_code, gl_accounts.accountName as accounts_name, gl_accounts.accountNameEn as accounts_nameEn')
            ->join('gl_accounts', 'gl_accounts.id=gl_opening_balance_items.accountID', 'left');
        $this->db->order_by('id', 'asc');

        $this->db->where('openingId', $openingId);

        $q = $this->db->get('gl_opening_balance_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getAllPaymentTypes()
    {

		$this->db->select('gl_payment_methods.*,gl_accounts.accountName, gl_accounts.accountNameEn')
        ->join('gl_accounts', 'gl_accounts.id=gl_payment_methods.payment_account', 'left');
        $q = $this->db->get('gl_payment_methods');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getPaymentTypeByID($id)
    {
        $q = $this->db->get_where('gl_payment_methods', ['id' => $id], 1);

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

	public function unapprove($entryID,$entryDescription){
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

				$this->unapproveEntry($entryID, $entryDebitAccounts, $entryCreditAccounts,$entryDescription);



			return false;
		}


		public function unapproveEntry($entryID, $debitAccounts, $creditAccounts,$entryDescription){
			$entryInfo        = (array) $this->getEntries($entryID)[0];

			foreach ($debitAccounts as $account){
				$balance = $account[key($account)];
				$account = key($account);

				$accountDebitInfo    = (array) $this->getAccounts($account)[0];

				$accountDebitBalance = $accountDebitInfo['accountDebit'];

				$accountInfo = array('accountDebit' => ( $accountDebitBalance- $balance));
				$this->db->where('id', $account);
				$this->db->update('gl_accounts', $accountInfo);
			}

			foreach ($creditAccounts as $account){
				$balance = $account[key($account)];
				$account = key($account);

				$accountCreditInfo    = (array) $this->getAccounts($account)[0];
				$accountCreditBalance = $accountCreditInfo['accountCredit'];

				$accountInfo = array('accountCredit' => ($accountCreditBalance - $balance));
				$this->db->where('id', $account);
				$this->db->update('gl_accounts', $accountInfo);
			}

			$entryInfo = array('entryStatus' => 0,'entryDescription' =>$entryDescription);
			$this->db->where('entryID', $entryID);
			$this->db->update('gl_entries', $entryInfo);

			return true;
		}
   public function getApiKeysByCompany($company)
    {
		$this->db->select('key');
        $q=$this->db->get_where('api_keys',['company' =>$company]);
		if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return false;
    }

   public function IsUserFound($username,$password)
    {
		$q=$this->db->get_where('users',['username' =>$username , 'password' =>$password]);
		if ($q->num_rows() > 0) {
            return true;
        }
        return false;
    }


	public function updateCurrency($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('currencies', $data)) {
            return true;
        }
        return false;
    }

		public function getAllSalesDueAggregate($startDate = NULL,$endDate = NULL, $customer_id = NULL, $not_due = NULL, $the_oldest = NULL, $salesman_id = NULL,$grpid = null ,$coid = null ,$brid = null){

		$this->db->select('customer,customer_id, sum(COALESCE(grand_total-(paid+discount_allowance),0)) as amount');
		$this->db->group_by('customer_id');
		$this->db->order_by('customer', 'asc');


        if($grpid)
		$this->db->where('grpid =', $grpid);
		if($coid)
		$this->db->where('coid =', $coid);
		if($brid)
		$this->db->where('brid =', $brid);


		if ($startDate && $endDate){
			    $this->db->where(" (date BETWEEN '" . $startDate . "' and '" . $endDate . "' )  ");

			}
		if ($startDate==NULL && $endDate){
			    //$this->db->where(" (date <= '" . $endDate . "' )  ");
			   $this->db->where('date  <=',$endDate);

			}



			if($customer_id){
				$this->db->where('customer_id', $customer_id);
			}

            if ($salesman_id){
                $this->db->where('salesman_id', $salesman_id);
            }
			//$this->db->where('payment_term >', 0);
			$this->db->where('ROUND(abs(grand_total-(paid+discount_allowance)),2) >', 0);



        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;



		}


		public function getAllPurchasesDueAggregate($startDate = NULL,$endDate = NULL, $supplier_id = NULL, $not_due = NULL, $the_oldest = NULL,$grpid = null ,$coid = null ,$brid = null){

		$this->db->select('supplier,supplier_id, sum(COALESCE(grand_total-(paid+discount_earned),0)) as amount');
		$this->db->group_by('supplier_id');
		$this->db->order_by('supplier', 'asc');

		if ($startDate && $endDate){
			    $this->db->where(" (date BETWEEN '" . $startDate . "' and '" . $endDate . "' )  ");

			}
		if ($startDate==NULL && $endDate){
			    //$this->db->where(" (date <= '" . $endDate . "' )  ");
			   $this->db->where('date  <=',$endDate);

			}


            if($grpid)
            $this->db->where('grpid =', $grpid);
            if($coid)
            $this->db->where('coid =', $coid);
            if($brid)
            $this->db->where('brid =', $brid);


			if($supplier_id){
				$this->db->where('supplier_id', $supplier_id);
			}
			//$this->db->where('payment_term >', 0);
			$this->db->where('ROUND(abs(grand_total-(paid+discount_earned)),2) >', 0);



        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;



		}




		public function getAllSalesDue($id = NULL,$startDate = NULL,$endDate = NULL,$flage = NULL, $customer_id = NULL, $not_due = NULL, $the_oldest = NULL, $salesman_id = NULL,$grpid = null ,$coid = null ,$brid = null){

		$this->db->select('id, DATE_FORMAT(date, "%Y-%m-%d") as date, reference_no, customer, (grand_total-(paid+discount_allowance)) as amount, COALESCE(payment_term,0) as payment_term,(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END) as due_date,DATEDIFF("'.$endDate.'",(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END)) as outstanding');
		$this->db->order_by('id', 'DEsc');

		if ($startDate && $endDate && $flage){
			    $this->db->where(" (date BETWEEN '" . $startDate . "' and '" . $endDate . "' )  ");

			}
			if ($startDate==NULL && $endDate){
			    //$this->db->where(" (date <= '" . $endDate . "' )  ");
			   $this->db->where('date  <=',$endDate);

			}

            if($grpid)
            $this->db->where('grpid =', $grpid);
            if($coid)
            $this->db->where('coid =', $coid);
            if($brid)
            $this->db->where('brid =', $brid);

			if($id){
				$this->db->where('id', $id);
			}
			if($customer_id){
				$this->db->where('customer_id', $customer_id);
			}
            if ($salesman_id){
                $this->db->where('salesman_id', $salesman_id);
            }
			if($not_due==0){
				$this->db->where('DATEDIFF("'.$endDate.'",(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END)) >=', 0);
			}
			//$this->db->where('payment_term >', 0);
			$this->db->where('ROUND(abs(grand_total-(paid+discount_allowance)),2) >', 0);



        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;



		}


	public function getAllPurchasesDue($id = NULL,$startDate = NULL,$endDate = NULL,$flage = NULL, $supplier_id = NULL, $not_due = NULL, $the_oldest = NULL,$grpid = null ,$coid = null ,$brid = null){

		$this->db->select('id, DATE_FORMAT(date, "%Y-%m-%d") as date, reference_no, supplier, (grand_total-(paid+discount_earned)) as amount, COALESCE(payment_term,0) as payment_term,(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END) as due_date,DATEDIFF("'.$endDate.'",(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END)) as outstanding');
		$this->db->order_by('id', 'DEsc');

		if ($startDate && $endDate && $flage){
			    $this->db->where(" (date BETWEEN '" . $startDate . "' and '" . $endDate . "' )  ");

			}
			if ($startDate==NULL && $endDate){
			    //$this->db->where(" (date <= '" . $endDate . "' )  ");
			   $this->db->where('date  <=',$endDate);

			}

            if($grpid)
            $this->db->where('grpid =', $grpid);
            if($coid)
            $this->db->where('coid =', $coid);
            if($brid)
            $this->db->where('brid =', $brid);

			if($id){
				$this->db->where('id', $id);
			}
			if($supplier_id){
				$this->db->where('supplier_id', $supplier_id);
			}
			if($not_due==0){
				$this->db->where('DATEDIFF("'.$endDate.'",(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END)) >=', 0);
			}
			//$this->db->where('payment_term >', 0);
			$this->db->where('ROUND(abs(grand_total-(paid+discount_earned)),2) >', 0);



        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;



		}
	public function getpaymentType($isCC,$id = NULL)
    {
        if($id){
		$this->db->where('id', $id);
		}
		$q = $this->db->get_where('payment_type', ['isCC' => $isCC,'isActive' => 1]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }





	public function getAllSalesDue_temp(){



				$this->db->where('payment_status', 'due');
				$this->db->where('paid !=', 0);
				$this->db->where('pos', 1);
				$this->db->where('payment_term', 0);



        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

	public function getOpeningBalanceForWarehouses($start_date = null, $end_date = null, $warehouse_id = null,$grpid = null ,$coid = null ,$brid = null)
    {



		$this->db->select('SUM(COALESCE(value, 0)) as first_period_stock')
			->join('adjustment_items', 'adjustment_items.adjustment_id  = adjustments.id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('adjustments.approved =', 1);


		   if ($warehouse_id) {
            $this->db->where('adjustments.warehouse_id', $warehouse_id);
        }

        if($grpid)
		$this->db->where('adjustments.grpid =', $grpid);
		if($coid)
		$this->db->where('adjustments.coid =', $coid);
		if($brid)
		$this->db->where('adjustments.brid =', $brid);


		if ($start_date && $end_date) {
               $this->db->where('adjustments.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('adjustments');
        if ($q->num_rows() > 0) {
        	    /* foreach (($q->result()) as $row) {

        $data[] = $row;

                }
			return $data;*/
			return $q->row();
        }
        return false;
    }


	public function getEndPeriodForWarehouses($start_date = null, $end_date = null, $warehouse = null,$grpid = null ,$coid = null ,$brid = null){

        //$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        $pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_received ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        // WHERE p.status != 'pending' AND p.status != 'ordered'
        $sp = '( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.quantity ) totalSale, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';
        $wp = '( SELECT wp.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp ';
        //$wpcombo = '( SELECT wpcombo.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp JOIN ' . $this->db->dbprefix('combo_items') . '  wpcombo on wpcombo.item_id = wp.product_id ';
        $wpo = '( SELECT wpo.product_id, SUM( (CASE WHEN wpo.quantity> 0 THEN (wpo.avg_cost * wpo.quantity) ELSE 0 END) ) balacneValue, SUM( wpo.quantity ) balacneQty from ' . $this->db->dbprefix('warehouses_products') . ' wpo ';
        if ($start_date || $warehouse) {
            $sp .= ' WHERE ';
            if ($start_date) {
                //$start_date = $this->sma->fld($start_date);
                //$end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " AND p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= "  s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
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

        /*if($grpid)
		$this->db->where('adjustments.grpid =', $grpid);
		if($coid)
		$this->db->where('adjustments.coid =', $coid);
		if($brid)
		$this->db->where('adjustments.brid =', $brid);*/

        $pp .= '  ) PCosts';
        $sp .= '  ) PSales';
        $wp .= '  ) WarehousesProducts';
        //$wpcombo .= ' GROUP BY wpcombo.product_id ) ComboItems';
        $wpo .= '  ) WarehousesProductsOrignal';
		/*
		$this->db
               ->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . ".name,
				COALESCE( PCosts.purchasedQty, 0 ) as purchase_quantity,
                COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
                COALESCE( PSales.soldQty, 0 ) as sales_quantity,
                COALESCE( PSales.totalSale, 0 ) as sold,
                (COALESCE( PSales.totalGoodsCost, 0 ) ) as totalGoodsCost,
                (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  )) as Profit,
                COALESCE( WarehousesProductsOrignal.balacneQty, 0 ) as balance_qty,
				COALESCE( WarehousesProductsOrignal.balacneValue, 0 ) as end_of_period_stock,
				{$this->db->dbprefix('products')}.id as id", false)
				*/
        $this->db
               ->select("sum(COALESCE( PCosts.balacneValue, 0 )-COALESCE( PSales.totalGoodsCost, 0 )) as end_of_period_stock", false)
		       //->select("sum(COALESCE( WarehousesProductsOrignal.balacneValue, 0 )) as end_of_period_stock", false)
		        ->from('products')
                ->join($sp, 'products.id = PSales.product_id', 'left')
                ->join($pp, 'products.id = PCosts.product_id', 'left')
				->join($wp, 'products.id = WarehousesProducts.product_id', 'left')
				//->join($wpcombo, 'products.id = ComboItems.product_id', 'left')
				->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left')	;
				//->group_by('products.code');



            $q = $this->db->get();
			if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
				return $data;
            }
		return false;
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
    
    public function getAllShowrooms()
    {
        $q = $this->db->get('cars_showrooms');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getShowroomByID($id)
{
    $q = $this->db->get_where('cars_showrooms', ['id' => $id], 1);
    if ($q->num_rows() > 0) {
        return $q->row();
    }
    return false;
}



public function chechSupplierShowrooms($default_supplier)
    {
        $this->db->where('default_supplier', $default_supplier);
        $query = $this->db->get('cars_showrooms');

        if ($query->num_rows() > 0) {
			return true;
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


    public function getCountAccountByID($id)
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get_where('gl_accounts', ['parent_id' => $id], 1);
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



    public function getGroupsAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('main_groups');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCompaniesAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('main_companies');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getBranchesAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('main_branches');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function addMainGroup($data)
    {
        if ($this->db->insert('main_groups', $data)) {
            return true;
        }
        return false;
    }

    public function addMainCompany($data)
    {
        if ($this->db->insert('main_companies', $data)) {
            return true;
        }
        return false;
    }


    public function addMainBranch($data)
    {
        if ($this->db->insert('main_branches', $data)) {
            $brId = $this->db->insert_id();
            $clause = ['brId' => $brId, 'date' => date('Y-m-d'), 'so' => 1, 'qu' => 1, 'po' => 1, 'to' => 1, 'pos' => 1, 'do' => 1, 'pay' => 1, 're' => 1, 'rep' => 1, 'ex' => 1, 'ppay' => 1];
            $this->db->insert('order_ref',$clause);

            return true;
        }
        return false;
    }

    public function getMainGroupsByID($id)
    {
        $q = $this->db->get_where('main_groups', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getMainCompaniesByID($id)
    {
        $q = $this->db->get_where('main_companies', ['id' => $id], 1);
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

    public function updateMainGroup($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('main_groups', $data)) {
            return true;
        }
        return false;
    }

    public function updateMainCompany($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('main_companies', $data)) {
            return true;
        }
        return false;
    }

    public function updateMainBranch($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('main_branches', $data)) {
            return true;
        }
        return false;
    }



    public function deleteMainGroup($id)
    {
        if ($this->db->delete('main_groups', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteMainCompany($id)
    {
        if ($this->db->delete('main_companies', ['id' => $id])) {
            return true;
        }
        return false;
    }


    public function deleteMainBranch($id)
    {




        $this->db->trans_off();
		$this->db->trans_start();
        if ($this->db->delete('order_ref', ['brId' => $id])) {
            $this->db->delete('main_branches', ['id' => $id]);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the Branch (Delete:Branchs_model.php)');
        } else {
            return true;
        }

        return false;
    }


    public function getAllPurchaseItemsItemCountByGroup($grpid = null ,$coid = null ,$brid = null )
    {
        $this->db->select('*');
		if($grpid)
		$this->db->where('grpid =', $grpid);
		if($coid)
		$this->db->where('coid =', $coid);
		if($brid)
		$this->db->where('brid =', $brid);
		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllSalesItemsItemCountByGroup($grpid = null ,$coid = null ,$brid = null )
    {
        $this->db->select('*');
        if($grpid)
		$this->db->where('grpid =', $grpid);
		if($coid)
		$this->db->where('coid =', $coid);
		if($brid)
		$this->db->where('brid =', $brid);
		$q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }



    public function getMainGroup()
    {
        $q = $this->db->get('main_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getMainCompanies($grpId = null)
    {

        if($grpId)
        $this->db->where('grpId', $grpId);
        $q = $this->db->get('main_companies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCompaniesBygrpId($grpId)
    {
        $this->db->select('id as id, name as text, name as name')
        ->where('grpId', $grpId);//->order_by('model_name');
        $q = $this->db->get('main_companies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getBranchesBycoId($coId)
    {
        $this->db->select('id as id, name as text, name as name')
        ->where('coId', $coId);//->order_by('model_name');
        $q = $this->db->get('main_branches');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getMainBranches($coId = null)
    {

        if($coId)
        $this->db->where('grpId', $coId);
        $q = $this->db->get('main_branches');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getBillersBybrId($brId)
    {
        $this->db->select('id as id, name as text, name')
        ->where('group_name', 'biller')
        ->where('brId', $brId);//->order_by('model_name');
        $q = $this->db->get('companies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getWarehousesBybrId($brId)
    {
        $this->db->select('id as id, name as text, name')
        ->where('brId', $brId);//->order_by('model_name');
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getUsersBybrId($brId)
    {
        //$this->db->select('users.*, name as text');
        if ($this->Admin) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $this->db->where('brId', $brId);//->order_by('model_name');
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getUserBygrpId($grpId)
    {
        $this->db->select('id, CONCAT(first_name," ",last_name) as text, CONCAT(first_name," ",last_name) as name');
        if ($this->Admin) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $this->db->where('grpId', $grpId);//->order_by('model_name');
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getBillerBygrpId($grpId)
    {
        $this->db->select('id, name as text,  name')
        ->where('group_name', 'biller')
        ->where('grpId', $grpId);//->order_by('model_name');
        $q = $this->db->get('companies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }



    public function getUserBycoId($coId)
    {
        $this->db->select('id, CONCAT(first_name," ",last_name) as text, CONCAT(first_name," ",last_name) as name');
        if ($this->Admin) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $this->db->where('coId', $coId);//->order_by('model_name');
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }



    public function getBillerBycoId($coId)
    {
        $this->db->select('id, name as text,  name')
        ->where('group_name', 'biller')
        ->where('coId', $coId);//->order_by('model_name');
        $q = $this->db->get('companies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getUserBybrId($brId)
    {
        $this->db->select('id, CONCAT(first_name," ",last_name) as text, CONCAT(first_name," ",last_name) as name');
        if ($this->Admin) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $this->db->where('brId', $brId);//->order_by('model_name');
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBillerBybrId($brId)
    {
        $this->db->select('id, name as text,  name,companies.*')
        ->where('group_name', 'biller')
        ->where('brId', $brId);//->order_by('model_name');
        $q = $this->db->get('companies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getCustomerBranches($customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        $q = $this->db->get('customers_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSupplierBranches($supplier_id)
    {
        $this->db->where('supplier_id', $supplier_id);
        $q = $this->db->get('suppliers_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getUnitBranches($unit_id = null)
    {
        if($unit_id)
        $this->db->where('unit_id', $unit_id);

        $q = $this->db->get('units_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCategorieBranches($category_id = null)
    {
        if($category_id)
        $this->db->where('category_id', $category_id);

        $q = $this->db->get('categories_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getUnitBybrId($brId)
    {
        $this->db->select('units_details.unit_id as id, units.name as text, units.name, units.code,units.*')
            ->join('units', 'units.id=units_details.unit_id', 'left')

            ->where('units.base_unit', null) ->where('units_details.brId', $brId)->order_by('units.id');
        $q = $this->db->get('units_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getTaxBybrId($brId)
    {
        $this->db->select('taxs_details.tax_rate_id as id, tax_rates.name as text, tax_rates.name, tax_rates.code,tax_rates.*')
            ->join('tax_rates', 'tax_rates.id=taxs_details.tax_rate_id', 'left')

            ->where('tax_rates.category_id', 1)->where('tax_rates.tax_active', 1)->where('taxs_details.brId', $brId)->order_by('tax_rates.id');
        $q = $this->db->get('taxs_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCurrenciesBybrId($brId)
    {
        $this->db->select('currencies_details.currency_id as id, currencies.name as text, currencies.name, currencies.code,currencies.*')
            ->join('currencies', 'currencies.id=currencies_details.currency_id', 'left')

           ->where('currencies_details.brId', $brId)->order_by('currencies.rate');
        $q = $this->db->get('currencies_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getUnitsByBUIDandbrId($base_unit,$brId)
    {
        $this->db->select('units_details.unit_id as id, units.name as text, units.name, units.code,units.*')
        ->join('units_details', 'units_details.unit_id=units.id', 'left')->where('units_details.brId', $brId)->where(" (units.id =  '".$base_unit."' OR units.base_unit =  '".$base_unit."') ")
        ->group_by('units.id')->order_by('units.id asc');
        $q = $this->db->get('units');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getCategoryBybrId($brId)
    {
        $this->db->select('categories_details.category_id as id, categories.name as text, categories.name,categories.*')
            ->join('categories', 'categories.id=categories_details.category_id', 'left')

        ->where('categories_details.brId', $brId)->order_by('categories.id')->where(" (categories.parent_id =  '0' OR categories.parent_id is null) ");
        $q = $this->db->get('categories_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getWarehousBranches($product_id)
    {
        $this->db->where('product_id', $product_id);
        $q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getBrandBranches($brand_id =null)
    {
        if($brand_id)
        $this->db->where('brand_id', $brand_id);

        $q = $this->db->get('brands_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getVariantBranches($variant_id =null)
    {
        if($variant_id)
        $this->db->where('variant_id', $variant_id);

        $q = $this->db->get('variants_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBrandBybrId($brId)
    {
        $this->db->select('brands_details.brand_id as id, brands.name as text, brands.name')
            ->join('brands', 'brands.id=brands_details.brand_id', 'left')

        ->where('brands_details.brId', $brId)->order_by('brands.id');
        $q = $this->db->get('brands_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getCustomersBybrId($brId)
    {
        $this->db->select('customers_details.customer_id as id, companies.company as text, companies.company')
            ->join('companies', 'companies.id=customers_details.customer_id', 'left')

            ->where('customers_details.brId', $brId)->where('companies.group_name', 'customer')->order_by('companies.id');
        $q = $this->db->get('customers_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getSuppliersBybrId($brId)
    {
        $this->db->select('suppliers_details.supplier_id as id, companies.company as text, companies.company')
            ->join('companies', 'companies.id=suppliers_details.supplier_id', 'left')

            ->where('suppliers_details.brId', $brId)->where('companies.group_name', 'supplier')->order_by('companies.id');
        $q = $this->db->get('suppliers_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getWarehousesBycoId($coId)
    {
        $this->db->select('id as id, name as text, name')
        ->where('coId', $coId);//->order_by('model_name');
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCategoryBycoId($coId)
    {
        $this->db->select('categories_details.category_id as id, categories.name as text, categories.name')
            ->join('categories', 'categories.id=categories_details.category_id', 'left')

        ->where('categories_details.coId', $coId)->group_by('categories_details.category_id')->where(" (categories.parent_id =  '0' OR categories.parent_id is null) ");
        $q = $this->db->get('categories_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBrandBycoId($coId)
    {
        $this->db->select('brands_details.brand_id as id, brands.name as text, brands.name')
            ->join('brands', 'brands.id=brands_details.brand_id', 'left')

        ->where('brands_details.coId', $coId)->group_by('brands_details.brand_id');
        $q = $this->db->get('brands_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getWarehousesBygrpId($grpId)
    {
        $this->db->select('id as id, name as text, name')
        ->where('grpId', $grpId);//->order_by('model_name');
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCategoryBygrpId($grpId)
    {
        $this->db->select('categories_details.category_id as id, categories.name as text, categories.name')
            ->join('categories', 'categories.id=categories_details.category_id', 'left')

        ->where('categories_details.grpId', $grpId)->group_by('categories_details.category_id')->where(" (categories.parent_id =  '0' OR categories.parent_id is null) ");
        $q = $this->db->get('categories_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBrandBygrpId($grpId)
    {
        $this->db->select('brands_details.brand_id as id, brands.name as text, brands.name')
            ->join('brands', 'brands.id=brands_details.brand_id', 'left')

        ->where('brands_details.grpId', $grpId)->group_by('brands_details.brand_id');
        $q = $this->db->get('brands_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getTaxBranches($tax_rate_id = null)
    {
        if($tax_rate_id)
        $this->db->where('tax_rate_id', $tax_rate_id);

        $q = $this->db->get('taxs_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getCurrencyBranches($currency_id = null)
    {
        if($currency_id)
        $this->db->where('currency_id', $currency_id);

        $q = $this->db->get('currencies_details');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }




    public function getCountSalesBybrid($brid)
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get_where('sales', ['brid' => $brid], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}


    public function getCountPurchasesBybrid($brid)
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get_where('purchases', ['brid' => $brid], 1);
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

    public function getAllSalesmanCompanies()
    {
        $q = $this->db->get_where('companies', ['group_name' => 'salesman']);
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


    public function calculateTaxShop($product_details = null, $tax_details, $custom_value = null, $c_on = null)
    {
        $value      = $custom_value ? $custom_value : (($c_on == 'cost') ? $product_details->cost : $product_details->price);
        $tax_amount = 0;
        $tax        = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
            if ($product_details && $product_details->tax_method == 1) {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / 100, 12);
                $tax        = $this->sma->formatDecimal($tax_details->rate, 0) . '%';
            } else {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                //$tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100), 12);
                $tax        = $this->sma->formatDecimal($tax_details->rate, 0) . '%';
            }
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = $this->sma->formatDecimal($tax_details->rate);
            $tax        = $this->sma->formatDecimal($tax_details->rate, 0);
        }
        return ['id' => $tax_details->id, 'tax' => $tax, 'amount' => $tax_amount];
    }

    public function getPOSRegistersByID($id)
    {
         $q = $this->db->get_where('pos_register', ['id' =>$id],1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTaxRatesFirst()
    {
        $this->db->select('id');
		$this->db->order_by('id', 'ASC')->limit(1);
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllProductsUnitCount($unit)
    {
        $this->db->select('*');
		$this->db->where('unit ', $unit);
		$q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateCategory($name, $data = [])
    {
        if ($this->db->update('categories', $data, ['name' => $name])) {
            return true;
        }
        return false;
    }

    public function updateProduct($name, $data = [])
    {
        if ($this->db->update('products', $data, ['name' => $name])) {
            return true;
        }
        return false;
    }

    public function updateProductById($id, $data = [])
    {
        if ($this->db->update('products', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function syncIncreasingPayments($id)
    {
        $increasing = $this->getIncreasingByID($id);
        $paid     = 0;
        if ($payments = $this->getIncreasingPayments($id)) {
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
        }

        $payment_status = $paid <= 0 ? 'pending' : $increasing->payment_status;
        if ($this->sma->formatDecimal($increasing->net) > $this->sma->formatDecimal($paid) && $paid > 0) {
            $payment_status = 'partial';
        } elseif ($this->sma->formatDecimal($increasing->net) <= $this->sma->formatDecimal($paid)) {
            $payment_status = 'paid';
        }

        if ($this->db->update('ast_increasing', ['paid' => $paid, 'payment_status' => $payment_status], ['id' => $id])) {
            return true;
        }

        return false;
    }

    public function getIncreasingByID($id)
    {
        $q = $this->db->get_where('ast_increasing', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getIncreasingPayments($increasing_id)
    {
        $q = $this->db->get_where('ast_increasing_payments', ['increasing_id' => $increasing_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllProducts()
    {
        $this->db->select('id,name,code');
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($this->Settings->user_language != 'arabic'){
                    if($row->second_name)
                    $row->name=$row->second_name;

                }
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCouponsByID($id)
    {
        $q = $this->db->get_where('coupons', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
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

    public function checkUnitsByBUID($unit,$code,$operation_value)
    {
        $q = $this->db->get_where('units', ['base_unit' => $unit,'code' => $code,'operation_value' => $operation_value,'operator' => '*'], 1);
        if ($q->num_rows() > 0) {
			return $q->row();
		}
        return false;
    }

    public function addUnit($data)
    {
        if ($this->db->insert('units', $data)) {

            $uid = $this->db->insert_id();
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {


                $data_units_details = [
                    'unit_id'            => $uid,
                    'grpId'            => $data ['grpId'],
                    'coId'            => $data ['coId'],
                    'brId'            => $data ['brId'],



                ];


                if ($this->db->insert('units_details', $data_units_details)) {

                    //return true;
                }



        }

            return $uid;
        }
        return false;
    }

    public function getAllPriceGroups()
    {
        $q = $this->db->get('price_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllWaiters()
    {
        $q = $this->db->get_where('resturant_emp', ['emp_type' => 2]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

       public function getMultiWarehousesBybrId($brId)
       {
           $this->db->select('id as id, name as text, name')
           ->where_in('brId', $brId);//->order_by('model_name');
           $q = $this->db->get('warehouses');
           if ($q->num_rows() > 0) {
               foreach (($q->result()) as $row) {
                   $data[] = $row;
               }
               return $data;
           }
           return false;
       }

    public function getGroupSettings()
	{
		$q = $this->db->get('group_settings');
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

    public function getCompanyByCustRefId($customer_ref_id) {
        $q = $this->db->get_where('companies', array('customer_ref_id' => $customer_ref_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
   	}

    public function getAllSuppliersWithOutCustRefId() {

        $this->db->where('group_name', 'supplier')->where('customer_ref_id IS NULL', null);
        $q = $this->db->get('companies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
   	}

    public function getSuppliersBybrIdWithOutCustRefId($brId)
       {
           $this->db->select('suppliers_details.supplier_id as id, companies.company as text, companies.company')
               ->join('companies', 'companies.id=suppliers_details.supplier_id', 'left')

               ->where('suppliers_details.brId', $brId)->where('companies.group_name', 'supplier')->where('companies.customer_ref_id IS NULL', null)->order_by('companies.id');
           $q = $this->db->get('suppliers_details');
           if ($q->num_rows() > 0) {
               foreach (($q->result()) as $row) {
                   $data[] = $row;
               }
               return $data;
           }
           return false;
    }

    public function getAllPurchasesDueAggregateWithOutCustRefId($startDate = NULL,$endDate = NULL, $supplier_id = NULL, $not_due = NULL, $the_oldest = NULL,$grpid = null ,$coid = null ,$brid = null){

		$this->db->select('supplier,supplier_id,companies.customer_ref_id, sum(COALESCE(grand_total-(paid+discount_earned),0)) as amount')
		->join('companies', 'companies.id=purchases.supplier_id', 'left');
        $this->db->group_by('purchases.supplier_id');
		$this->db->order_by('purchases.supplier', 'asc');

		if ($startDate && $endDate){
			    $this->db->where(" (purchases.date BETWEEN '" . $startDate . "' and '" . $endDate . "' )  ");

			}
		if ($startDate==NULL && $endDate){
			    //$this->db->where(" (date <= '" . $endDate . "' )  ");
			   $this->db->where('purchases.date  <=',$endDate);

			}


            if($grpid)
            $this->db->where('purchases.grpid =', $grpid);
            if($coid)
            $this->db->where('purchases.coid =', $coid);
            if($brid)
            $this->db->where('purchases.brid =', $brid);


			if($supplier_id){
				$this->db->where('purchases.supplier_id', $supplier_id);
			}
			//$this->db->where('payment_term >', 0);
			$this->db->where('abs(grand_total-(paid+discount_earned)) >', 0);
            $this->db->where('companies.group_name', 'supplier');//->where('companies.customer_ref_id IS NULL', null);



        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}

    public function getModules()
    {
        $this->db->where('status', 1)->order_by('id');

        $q = $this->db->get('modules');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getObjects()
    {
        $this->db->select('objects.id as id, objects.name, objects.name_en, objects.description, objects.directory as directory, modules.name as m_name, modules.name_en as m_name_en, modules.directory as m_directory, modules.icon as m_icon, objects.type, objects.sort_ord, objects.rnk')
        ->join('modules', 'modules.id=objects.module_id', 'left');
        $this->db->where('objects.status', 1);
        $this->db->where('modules.status', 1);
        $this->db->order_by('objects.module_id,objects.sort_ord,objects.rnk', 'asc');

        $q = $this->db->get('objects');

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

    public function getAllActiveTermsConditions()
    {
        $this->db->where('status', '1');

		$q = $this->db->get('sales_termscondi');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				$data[] = $row;
            }
            return $data;
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

    public function getQuoteByID($id)
    {
        $q = $this->db->get_where('quotes', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateQuoteUUID($id)
    {
        if ($this->db->update('quotes', ['uuid' => $this->getUniqeUUID()->UUID], ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getEndPeriodForWarehousesV2($start_date = null, $end_date = null, $warehouse = null,$grpId = null ,$coId = null ,$brId = null){
        //$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        $pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' and p.status != 'returned' ";
        $ppret = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' and p.status = 'returned' ";
        $op = "( SELECT op.product_id,  SUM( op.quantity ) as openQty from {$this->db->dbprefix('adjustment_items')} op LEFT JOIN {$this->db->dbprefix('adjustments')} a on a.id = op.adjustment_id where a.approved=1 ";
        $qa = "( SELECT op.product_id,  SUM( CASE WHEN op.type = 'addition' THEN op.quantity ELSE ((-1)*op.quantity) END) as openQty from {$this->db->dbprefix('adjust_items')} op LEFT JOIN {$this->db->dbprefix('adjusts')} a on a.id = op.adjustment_id where a.approved=0 ";
        $dp = "( SELECT dp.product_id,  SUM( (-1) * dp.quantity ) as destructionQty from {$this->db->dbprefix('destruction_items')} dp LEFT JOIN {$this->db->dbprefix('destruction')} d on d.id = dp.adjustment_id ";
        $stp = "( SELECT stp.product_id,  SUM( stp.counted-stp.expected ) as stockQty from {$this->db->dbprefix('stock_count_items')} stp LEFT JOIN {$this->db->dbprefix('stock_counts')} s on s.id = stp.stock_count_id  where (s.id in (SELECT count_id FROM {$this->db->dbprefix('adjustments_count')} ) or s.id in (SELECT count_id FROM {$this->db->dbprefix('adjustments_store')} ))";
        $tpt = "( SELECT product_id, SUM(pi.quantity) as purchasedQty from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('transfers')} t  on t.id = pi.transfer_id  WHERE t.status = 'completed' and t.to_warehouse_id=pi.warehouse_id ";
        $tpf = "( SELECT product_id, SUM((-1) * pi.quantity) as purchasedQty from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('transfers')} t  on t.id = pi.transfer_id  WHERE t.status = 'completed' and t.to_warehouse_id=pi.warehouse_id ";
        if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
              {
			$tmp = "( SELECT product_id, SUM(mm.confirm_qty) as confirmQty from {$this->db->dbprefix('man_manufacturing')} mm LEFT JOIN {$this->db->dbprefix('man_bom')} mb  on mb.id = mm.bom_id  WHERE mm.status = 'done' ";
              }else{
                $tmp = "( select 0 as product_id,0 as confirmQty ) PTransferMun";

              }
            // WHERE p.status != 'pending' AND p.status != 'ordered'
        $sp = '( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.quantity ) totalSale, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id and s.sale_status ="completed" ';
        $spret = '( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.quantity ) totalSale, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id and s.sale_status ="returned" ';
        $cis = '( SELECT ci.item_id, SUM((-1) * si.quantity * ci.quantity ) soldQty, SUM( si.net_unit_price * si.quantity ) totalSale, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si JOIN ' . $this->db->dbprefix('combo_items') . ' ci on s.id = si.sale_id and (s.sale_status ="completed" or s.sale_status ="returned") and ci.product_id=si.product_id ';
        $cidp = '( SELECT ci.item_id, SUM((-1) * dp.quantity * ci.quantity ) destructionQty from ' . $this->db->dbprefix('destruction') . ' d JOIN ' . $this->db->dbprefix('destruction_items') . ' dp JOIN ' . $this->db->dbprefix('combo_items') . ' ci on d.id = dp.adjustment_id  and ci.product_id=dp.product_id ';
        //$wp = '( SELECT wp.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp ';
        //$wpcombo = '( SELECT wpcombo.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp JOIN ' . $this->db->dbprefix('combo_items') . '  wpcombo on wpcombo.item_id = wp.product_id ';
        //$wpo = '( SELECT wpo.product_id, SUM( (CASE WHEN wpo.quantity> 0 THEN (wpo.avg_cost * wpo.quantity) ELSE 0 END) ) balacneValue, SUM( wpo.quantity ) balacneQty from ' . $this->db->dbprefix('warehouses_products') . ' wpo ';
        $wpo = '( SELECT wpo.product_id, avg(wpo.avg_cost) as avgValue from ' . $this->db->dbprefix('warehouses_products') . ' wpo where wpo.quantity >0 ';
        if ($start_date || $warehouse || $grpId || $coId || $brId) {
            $sp .= ' WHERE ';
            $spret .= ' WHERE ';
            $cis .= ' WHERE ';
            if ($start_date || $warehouse)
            $cidp .= ' WHERE ';
            $op .= ' AND ';
            $qa .= ' AND ';
            $dp .= ' WHERE ';
            //$stp .= ' WHERE ';
            if ($start_date) {
                //$start_date = $this->sma->fld($start_date);
                //$end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " AND p.date between '{$start_date}' AND '{$end_date}' ";
                $ppret .= " AND p.date between '{$start_date}' AND '{$end_date}' ";
                $op .= " a.date between '{$start_date}' AND '{$end_date}' ";
                $qa .= " a.date between '{$start_date}' AND '{$end_date}' ";
                $dp .= " d.date between '{$start_date}' AND '{$end_date}' ";
                $stp .= " AND  s.date between '{$start_date}' AND '{$end_date}' ";
                $tpt .= " AND t.date between '{$start_date}' AND '{$end_date}' ";
                $tpf .= " AND t.date between '{$start_date}' AND '{$end_date}' ";
                if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
                $tmp .= " AND mm.date_done between '{$start_date}' AND '{$end_date}' ";
                $sp .= " s.date between '{$start_date}' AND '{$end_date}' ";
                $spret .= " s.date between '{$start_date}' AND '{$end_date}' ";
                $cis .= " s.date between '{$start_date}' AND '{$end_date}' ";
                $cidp .= " d.date between '{$start_date}' AND '{$end_date}' ";
                if ($warehouse || $grpId || $coId || $brId) {
                    $sp .= ' AND ';
                    $spret .= ' AND ';
                    $cis .= ' AND ';
                    if ($warehouse)
                    $cidp .= ' AND ';
                    $op .= ' AND ';
                    $qa .= ' AND ';
                    $dp .= ' AND ';
                    //$stp .= ' AND ';

                }
            }

            if ($warehouse) {
                $pp .= " AND pi.warehouse_id = '{$warehouse}' ";
                $ppret .= " AND pi.warehouse_id = '{$warehouse}' ";
                $op .= " op.warehouse_id = '{$warehouse}' ";
				$qa .= " op.warehouse_id = '{$warehouse}' ";
				$dp .= " dp.warehouse_id = '{$warehouse}' ";
				$stp .= " AND  s.warehouse_id = '{$warehouse}' ";
				$tpt .= " AND pi.warehouse_id = '{$warehouse}' ";
                $tpf .= " AND t.from_warehouse_id = '{$warehouse}' ";
                if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
                $tmp .= " AND mb.wh_complete = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
				$spret .= " si.warehouse_id = '{$warehouse}' ";
				$cis .= " si.warehouse_id = '{$warehouse}' ";
				$cidp .= " dp.warehouse_id = '{$warehouse}' ";
				//$wp .= " WHERE wp.warehouse_id = '{$warehouse}' ";
            	//$wpcombo .= " WHERE wp.warehouse_id = '{$warehouse}' ";
            	$wpo .= " AND wpo.warehouse_id = '{$warehouse}' ";
                if($grpId || $coId || $brId){
                    $sp .= ' AND ';
                    $spret .= ' AND ';
                    $cis .= ' AND ';
                    $op .= ' AND ';
                    $qa .= ' AND ';
                    $dp .= ' AND ';
                }

            }

            if($grpId){
                $pp .= " AND pi.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
                $ppret .= " AND pi.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
                $op .= " op.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
				$qa .= " op.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
				$dp .= " dp.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
				$stp .= " AND  s.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
				$tpt .= " AND pi.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
                $tpf .= " AND t.from_warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
                if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
                $tmp .= " AND mb.wh_complete in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
                $sp .= " si.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
				$spret .= " si.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
				$cis .= " si.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";
				$wpo .= " AND wpo.warehouse_id in (select warehouse_id from sma_warehouses_products where grpId='{$grpId}' ";

                if($coId){
                    $pp .= " AND coId='{$coId}' ";
                    $ppret .= " AND coId='{$coId}' ";
                    $op .= " AND coId='{$coId}' ";
                    $qa .= " AND coId='{$coId}' ";
                    $dp .= " AND coId='{$coId}' ";
                    $stp .= " AND coId='{$coId}' ";
                    $tpt .= " AND coId='{$coId}' ";
                    $tpf .= " AND coId='{$coId}' ";
                    if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
                    $tmp .= " AND coId='{$coId}' ";
                    $sp .= " AND coId='{$coId}' ";
                    $spret .= " AND coId='{$coId}' ";
                    $cis .= " AND coId='{$coId}' ";
                    $wpo .= " AND coId='{$coId}' ";

                }

                if($brId){
                    $pp .= " AND brId='{$brId}' ";
                    $ppret .= " AND brId='{$brId}' ";
                    $op .= " AND brId='{$brId}' ";
                    $qa .= " AND brId='{$brId}' ";
                    $dp .= " AND brId='{$brId}' ";
                    $stp .= " AND brId='{$brId}' ";
                    $tpt .= " AND brId='{$brId}' ";
                    $tpf .= " AND brId='{$brId}' ";
                    if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
                    $tmp .= " AND brId='{$brId}' ";
                    $sp .= " AND brId='{$brId}' ";
                    $spret .= " AND brId='{$brId}' ";
                    $cis .= " AND brId='{$brId}' ";
                    $wpo .= " AND brId='{$brId}' ";

                }

                    $pp .= " )  ";
                    $ppret .= " )  ";
                    $op .= " )  ";
                    $qa .= " )  ";
                    $dp .= " )  ";
                    $stp .= " )  ";
                    $tpt .= " )  ";
                    $tpf .= " )  ";
                    if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
                    $tmp .= " )  ";
                    $sp .= " )  ";
                    $spret .= " )  ";
                    $cis .= " )  ";
                    $wpo .= " )  ";
            }
        }
        $pp .= ' GROUP BY pi.product_id ) PCosts';
        $ppret .= ' GROUP BY pi.product_id ) PCostsRet';
        $op .= ' GROUP BY op.product_id ) POpen';
        $qa .= ' GROUP BY op.product_id ) QAdj';
        $dp .= ' GROUP BY dp.product_id ) PDestruction';
        $stp .= ' GROUP BY stp.product_id ) PStock';
        $tpt .= ' GROUP BY pi.product_id ) PTransferTo';
        $tpf .= ' GROUP BY pi.product_id ) PTransferFrom';
        if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
        $tmp .= ' GROUP BY mb.product_id ) PTransferMun';
        $sp .= ' GROUP BY si.product_id ) PSales';
        $spret .= ' GROUP BY si.product_id ) PSalesRet';
        $cis .= ' GROUP BY ci.item_id ) PSalesCombo';
        $cidp .= ' GROUP BY ci.item_id ) PDestructionCombo';
        //$this->sma->print_arrays($cidp);
        //$wp .= ' GROUP BY wp.product_id ) WarehousesProducts';
        //$wpcombo .= ' GROUP BY wpcombo.product_id ) ComboItems';
        $wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';
		/*
		$this->db
               ->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . ".name,
				COALESCE( PCosts.purchasedQty, 0 ) as purchase_quantity,
                COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
                COALESCE( PSales.soldQty, 0 ) as sales_quantity,
                COALESCE( PSales.totalSale, 0 ) as sold,
                (COALESCE( PSales.totalGoodsCost, 0 ) ) as totalGoodsCost,
                (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  )) as Profit,
                COALESCE( WarehousesProductsOrignal.balacneQty, 0 ) as balance_qty,
				COALESCE( WarehousesProductsOrignal.balacneValue, 0 ) as end_of_period_stock,
				{$this->db->dbprefix('products')}.id as id", false)
				*/
                $this->db
                ->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . ".name,
				COALESCE( POpen.openQty, 0 ) as open_quantity,
                COALESCE( PCosts.purchasedQty, 0 ) as purchase_quantity,
                COALESCE( PCostsRet.purchasedQty, 0 ) as purchase_quantity_ret,
                COALESCE( PTransferTo.purchasedQty, 0 ) as tr_quantity_to,
                COALESCE( PTransferFrom.purchasedQty, 0 ) as tr_quantity_from,
                COALESCE( PTransferMun.confirmQty, 0 ) as Transfer_mun_quantity,
                (COALESCE( PDestruction.destructionQty, 0 )  + COALESCE( PDestructionCombo.destructionQty, 0 )) as destruction_quantity,
                COALESCE( PSales.soldQty, 0 ) as sales_quantity,
                COALESCE( PSalesRet.soldQty, 0 ) as sales_quantity_ret,
                COALESCE( PSalesCombo.soldQty, 0 ) as combo_quantity,
                (COALESCE( POpen.openQty, 0 ) + COALESCE( PCosts.purchasedQty, 0 ) + COALESCE( PCostsRet.purchasedQty, 0 ) + COALESCE( PTransferTo.purchasedQty, 0 ) + COALESCE( PTransferFrom.purchasedQty, 0 ) + COALESCE( PTransferMun.confirmQty, 0 ) + (COALESCE( PDestruction.destructionQty, 0 )  + COALESCE( PDestructionCombo.destructionQty, 0 )) - COALESCE( PSales.soldQty, 0 ) - COALESCE( PSalesRet.soldQty, 0 )  + COALESCE( PSalesCombo.soldQty, 0 )  )  as total,
                COALESCE( PStock.stockQty, 0 ) as stock_quantity,
                COALESCE( QAdj.openQty, 0 ) as quantity_adj,
                (COALESCE( POpen.openQty, 0 ) + COALESCE( PCosts.purchasedQty, 0 ) + COALESCE( PCostsRet.purchasedQty, 0 ) + COALESCE( PTransferTo.purchasedQty, 0 ) + COALESCE( PTransferFrom.purchasedQty, 0 ) + COALESCE( PTransferMun.confirmQty, 0 ) + (COALESCE( PDestruction.destructionQty, 0 )  + COALESCE( PDestructionCombo.destructionQty, 0 )) - COALESCE( PSales.soldQty, 0 ) - COALESCE( PSalesRet.soldQty, 0 )  + COALESCE( PSalesCombo.soldQty, 0 ) + COALESCE( PStock.stockQty, 0 ) + COALESCE( QAdj.openQty, 0 ) )  as netTotal,
                (CASE WHEN (COALESCE( POpen.openQty, 0 ) + COALESCE( PCosts.purchasedQty, 0 ) + COALESCE( PCostsRet.purchasedQty, 0 ) + COALESCE( PTransferTo.purchasedQty, 0 ) + COALESCE( PTransferFrom.purchasedQty, 0 ) + COALESCE( PTransferMun.confirmQty, 0 ) + (COALESCE( PDestruction.destructionQty, 0 )  + COALESCE( PDestructionCombo.destructionQty, 0 )) - COALESCE( PSales.soldQty, 0 ) - COALESCE( PSalesRet.soldQty, 0 )  + COALESCE( PSalesCombo.soldQty, 0 ) + COALESCE( PStock.stockQty, 0 ) + COALESCE( QAdj.openQty, 0 ) ) > 0 THEN COALESCE( WarehousesProductsOrignal.avgValue, 0 ) ELSE 0 END) as avgValue,
                ((COALESCE( POpen.openQty, 0 ) + COALESCE( PCosts.purchasedQty, 0 ) + COALESCE( PCostsRet.purchasedQty, 0 ) + COALESCE( PTransferTo.purchasedQty, 0 ) + COALESCE( PTransferFrom.purchasedQty, 0 ) + COALESCE( PTransferMun.confirmQty, 0 ) + (COALESCE( PDestruction.destructionQty, 0 )  + COALESCE( PDestructionCombo.destructionQty, 0 )) - COALESCE( PSales.soldQty, 0 ) - COALESCE( PSalesRet.soldQty, 0 )  + COALESCE( PSalesCombo.soldQty, 0 ) + COALESCE( PStock.stockQty, 0 ) + COALESCE( QAdj.openQty, 0 ) ) * (CASE WHEN (COALESCE( POpen.openQty, 0 ) + COALESCE( PCosts.purchasedQty, 0 ) + COALESCE( PCostsRet.purchasedQty, 0 ) + COALESCE( PTransferTo.purchasedQty, 0 ) + COALESCE( PTransferFrom.purchasedQty, 0 ) + COALESCE( PTransferMun.confirmQty, 0 ) + (COALESCE( PDestruction.destructionQty, 0 )  + COALESCE( PDestructionCombo.destructionQty, 0 )) - COALESCE( PSales.soldQty, 0 ) - COALESCE( PSalesRet.soldQty, 0 )  + COALESCE( PSalesCombo.soldQty, 0 ) + COALESCE( PStock.stockQty, 0 ) + COALESCE( QAdj.openQty, 0 ) ) > 0 THEN COALESCE( WarehousesProductsOrignal.avgValue, 0 ) ELSE 0 END))  as balanceValue,
                {$this->db->dbprefix('products')}.id as id", false)
                ->from('products')
                ->join($op, 'products.id = POpen.product_id', 'left')
                ->join($qa, 'products.id = QAdj.product_id', 'left')
                ->join($dp, 'products.id = PDestruction.product_id', 'left')
                ->join($stp, 'products.id = PStock.product_id', 'left')
                ->join($sp, 'products.id = PSales.product_id', 'left')
                ->join($spret, 'products.id = PSalesRet.product_id', 'left')
                ->join($cis, 'products.id = PSalesCombo.item_id', 'left')
                ->join($cidp, 'products.id = PDestructionCombo.item_id', 'left')
                ->join($pp, 'products.id = PCosts.product_id', 'left')
				->join($ppret, 'products.id = PCostsRet.product_id', 'left')
				->join($tpt, 'products.id = PTransferTo.product_id', 'left')
                ->join($tpf, 'products.id = PTransferFrom.product_id', 'left')
				->join($tmp, 'products.id = PTransferMun.product_id', 'left')
				//->join($wpcombo, 'products.id = ComboItems.product_id', 'left')
				->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left');
				if ($grpId || $coId || $brId) {
                    $this->db->join('warehouses_products', 'products.id = warehouses_products.product_id', 'left')->group_by('products.code');
                }else{
                    $this->db->group_by('products.code');
                }

            if ($grpId) {
                $this->db->where($this->db->dbprefix('warehouses_products') . '.grpId', $grpId);
            }
            if ($coId) {
                $this->db->where($this->db->dbprefix('warehouses_products') . '.coId', $coId);
            }
            if ($brId) {
                $this->db->where($this->db->dbprefix('warehouses_products') . '.brId', $brId);
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
				return $data;
            }
		return false;
    }

    public function getLastendPeriodStock($start_date,$end_date,$brId = null){

        $start_date= date('Y-m-d H:i:s', strtotime($start_date. ' - 1 years'));
        $end_date= date('Y-m-d H:i:s', strtotime($end_date. ' - 1 years'));

		$end_of_period_stock_arr = $this->getEndPeriodForWarehousesV2($start_date,$end_date,'',($brId ? 1 : null),($brId ? 1 : null),($brId ? $brId : null));
		$total_end_of_period_stock=0;
		foreach($end_of_period_stock_arr as $end_of_period_stock){
			$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
		}
		$end_of_period_stock = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));

		return $end_of_period_stock;
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

    public function checkCategories($id)
    {
        $q = $this->db->get_where('products', ['category_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllPurchaseItemsItemCountV2($product_id)
    {
        $this->db->select('*');
		$this->db->where('product_id =', $product_id);
		$this->db->where(" (purchase_id is not null OR transfer_id is not null) ");
		$q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getWhatsappTemplates()
    {
        $q = $this->db->get('whatsapp_templates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    
    public function getInvoiceByCustomerID($customer_id)
    {
        $q = $this->db->order_by('id', 'desc')->get_where('sales', ['customer_id' => $customer_id], 1);
        //$this->db->order_by('id', 'desc');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    
    public function getLatestPriceByCustomerID($product_id, $warehouse_id,$customer_id)
    {
        $q = $this->db->select('sale_items.real_unit_price')
		->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
        ->where('sale_items.product_id =', $product_id)
		->where('sales.warehouse_id =', $warehouse_id)
		->where('sales.customer_id =', $customer_id)
		->order_by('sales.id', 'desc')->get('sales', 1);

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

}
