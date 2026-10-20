<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Products_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_products($products = [])
    {
        if (!empty($products)) {

                $OpeningBalItem = [];

                $warehouse_id=$products[0]['warehouse'];

            foreach ($products as $product) {


                $cost = $product['cost'];
                $quantity = $product['quantity'];
                $variants = explode('|', $product['variants']);
                $promo_price = $product['promo_price'];
                $promo_start_date = $product['promo_start_date'];
                $promo_end_date = $product['promo_end_date'];
                unset($product['variants'],$product['cost'],$product['quantity'],$product['promo_price'],$product['promo_start_date'],$product['promo_end_date']);
                if ($this->db->insert('products', $product)) {
                    $product_id = $this->db->insert_id();
                    $i=1;
                    if($promo_price  && $promo_start_date && $promo_end_date){


                        if($i==1){
                        $data_org = array(
           'promo_type_id' => 10,
           'promo_name' => 'New Promo',
           'offer_category' => null,
           'offer_product' =>  $product_id,
           'fixed_off' => $promo_price,
           'status' => 1,
           'buy_quantity' => 1,
           'start_date'  => $promo_start_date,
           'end_date'    => $promo_end_date,
           'created_date' => date( 'Y-m-d H:i:s'),
           'revised_date' => date( 'Y-m-d H:i:s'),
           'hide_opt' => 0
           );



                $this->db->insert('promo', $data_org);
                $promo_id = $this->db->insert_id();

        }else{

                        $data_org2 = array(
                    'promo_type_id' => 10,
        'promo_name' => 'New Promo',
        'offer_category' => null,
        'offer_product' => $product_id,
        'buy_quantity' => 1,
        'fixed_off' => $promo_price,
        'status' => 1,
        'start_date'  => $promo_start_date,
        'end_date'    => $promo_end_date,
        'created_date' => date( 'Y-m-d H:i:s'),
        'revised_date' => date( 'Y-m-d H:i:s'),
        'hide_opt' => 1,
        'promo_id' => $promo_id,
        'promo_sr' => $i,
                    );
                    $this->db->insert('promo', $data_org2);







                    }

                    $i++;

                }
                    foreach ($variants as $variant) {
                        $v_arr = explode('#', $variant);
                        if ($v_arr[0] && trim($v_arr[0]) != '') {
                            $vat = ['product_id' => $product_id, 'name' => trim($v_arr[0]), 'price' => trim($v_arr[1])];
                            $this->db->insert('product_variants', $vat);
                        }
                    }

					$wp = ['product_id' => $product_id, 'warehouse_id' => $product['warehouse'], 'quantity' => '0', 'avg_cost' => '0', 'grpId' => $product['grpId'], 'coId' => $product['coId'], 'brId' => $product['brId']];
                    $this->db->insert('warehouses_products', $wp);

                }





                if ((trim($quantity)>0)  && (trim($cost)>0)){
                    $OpeningBalItem[] = [
                        'product_id'   => $product_id,
                        'type'         => 'addition',
                        'quantity'     => trim($quantity),
                        'cost'     => trim($cost),
                        'value'     => (trim($quantity) * trim($cost)),
                        'unit'     => $product['unit'],
                        'expiry'     => $product['expiry'],
                        'warehouse_id' => $product['warehouse'],
                        'grpId' => $product['grpId'],
                        'coId' => $product['coId'],
                        'brId' => $product['brId'],



                    ];

                }
            }
            if(!$this->chechAdjustmentWarehouse($warehouse_id) && isset($warehouse_id)) {

                $brId=$this->site->getWarehouseByID($warehouse_id)->brId;

            $OpeningBal         = [
                'date'         => date('Y-m-d H:s:i'),
                'reference_no' => $this->site->getReference('qa'),
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => null,
                'approved' => 0,
                'brId' => $brId,

            ];

            if($this->addAdjustment($OpeningBal, $OpeningBalItem)){

            }



        }


            return true;
        }
        return false;
    }


	public function addProductsToWarehouse($products = [])
    {
        if (!empty($products)) {
            foreach ($products as $product) {
              if($this->getProductQuantity($product['product_id'], $product['warehouse'])){
			  }else{
               $this->db->insert('warehouses_products', $product);

                }
            }
            return true;
        }
        return false;
    }

    public function add_products_temp($products = [])
    {
        if (!empty($products)) {
            foreach ($products as $product) {
                $variants = explode('|', $product['variants']);
                unset($product['variants']);
                if ($this->db->insert('products_temp', $product)) {
                    $product_id = $this->db->insert_id();
                    foreach ($variants as $variant) {
                        if ($variant && trim($variant) != '') {
                        //    $vat = ['product_id' => $product_id, 'name' => trim($variant)];
                        //    $this->db->insert('product_variants', $vat);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function chechAdjustmentWarehouse($warehouse_id)
    {
        $this->db->where('warehouse_id', $warehouse_id);
        $query = $this->db->get('adjustments');

        if ($query->num_rows() > 0) {
			return true;
        }
        return false;
    }

    public function addAdjustment($data, $products,$tvalue = null)
    {
        if ($this->db->insert('adjustments', $data)) {
            $adjustment_id = $this->db->insert_id();
            foreach ($products as $product) {
                $product['adjustment_id'] = $adjustment_id;
                $this->db->insert('adjustment_items', $product);
                if(isset($data['approved']) && $data['approved']==1)
                $this->syncAdjustment_temp($product);
            }
            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
            }


			if ($this->Settings->accounting){
				$acc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses');
						$entryWarehouseAcc=$acc->id;
						if($entryWarehouseAcc==""){

						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                        $acc=$this->site->getAccountByID($entryWarehouseAcc);
                        }


				if($acc->openingBalance==0){
					if($acc->accountSide=='Debit'){
						$debit=$tvalue;
						$credit=0;
						$balance=$tvalue;


					}else{
					    $debit=0;
						$credit=$tvalue;
						$balance=$tvalue;

					}

				$accounts[] = [
                    'accountID'   => $entryWarehouseAcc,
                    'debit'         => $debit,
                    'credit'     => $credit,
                    'balance'     => $balance,

                ];


				$data1 = [
                'openingDate'         => $data['date'],
                'note'         =>  $data['note'],
                'userID'   => $this->session->userdata('user_id'),
               ];

               if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data1  ['grpId']=$data['grpId'];
                $data1  ['coId']=$data['coId'];
                $data1  ['brId']=$data['brId'];

            }

               if(isset($data['approved']) && $data['approved']==1)
			   $this->site->addOpeningBalance($data1, $accounts);

				//$this->site->createEntry($this->site->getAccountsSettings()->warehouses_destruction,$this->site->getAccountByRefId($data['warehouse_id'])->id,$data['value'],$data['reference_no'],'Destruction');
				}
			}


            return true;
        }
        return false;
    }
    public function addAdjustment_count($data, $products)
    {
        if ($this->db->insert('adjustments_count', $data)) {
            $adjustment_id = $this->db->insert_id();
			$value_minus=0;
			$value_plus=0;
			$avg_cost=0;

            foreach ($products as $product) {
                $product['adjustment_id'] = $adjustment_id;
                $this->db->insert('adjustment_items_count', $product);
                $this->syncAdjustment_count($product);

			if ($wp_details = $this->getWarehouseProductQuantity($data['warehouse_id'], $product['product_id'])) {
			$avg_cost=($wp_details->avg_cost * $product['quantity']);

			   if($product['type']=="addition")
				$value_plus=$value_plus+$avg_cost;
			    if($product['type']=="subtraction")
				$value_minus=$value_minus+$avg_cost;

				}








            }


			if($value_minus>0)
			{



			if ($this->Settings->accounting){

					    $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


				$this->site->createEntry($this->site->getAccountsSettings()->warehouses_revaluation_losses,$entryWarehouseAcc,$value_minus,$data['reference_no'],'Revaluation');
			}
			}

			if($value_plus>0)
			{
		    if ($this->Settings->accounting){
				        $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


				$this->site->createEntry($entryWarehouseAcc,$this->site->getAccountsSettings()->warehouses_revaluation_profite,$value_plus,$data['reference_no'],'Revaluation');
			}
			}



            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
            }
            return true;
        }
        return false;
    }

	public function destruction_inventory($data, $products)
    {
        if ($this->db->insert('destruction', $data)) {



            $adjustment_id = $this->db->insert_id();
            foreach ($products as $product) {
                $product['adjustment_id'] = $adjustment_id;
                $this->db->insert('destruction_items', $product);
                $this->syncAdjustmentDestruction($product);

            }


			$date = date('Y-m-d H:i:s');

            $data1 = [
                'date'         => $date,
                'reference'    => $data['reference_no'],
                'amount'       => $data['value'],
                'total'       => $data['value'],
                'grand_total'       => $data['value'],
                'order_tax_id'       => $this->getTaxRatesNT() ? $this->getTaxRatesNT()->id : null,
                'created_by'   => $data['created_by'],
                'note'         => $data['note'],
                'payment_status'       => 'paid',
				'category_id'  => "0",
                'warehouse_id' => $data['warehouse_id'],
				'other' => 1,
            ];
			$this->addExpense($data1);


            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
            }

			if ($this->Settings->accounting){
                        $entryType='Destruction';
                        if($data['op_type']=="2"){
                            $entryType='Gifts_and_donations';
                        }

                        $entryDescription="";
                        if(!empty($data['note'])){
                            $entryDescription=$this->input->post('note');
                        }

                        $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

                        $entryWarehouseDestructionAcc=$data['op_acct'];
						if($entryWarehouseDestructionAcc=="")
						$entryWarehouseDestructionAcc=$this->site->getAccountsSettings()->warehouses_destruction;

				$this->site->createEntry($entryWarehouseDestructionAcc,$entryWarehouseAcc,$data['value'],$data['reference_no'],$entryType,null,null,$entryDescription,$entryDescription);
			}

            return true;
        }
        return false;
    }
	public function addAdjustment_store($data, $products)
    {

        if ($this->db->insert('adjustments_store', $data)) {
            $adjustment_id = $this->db->insert_id();
			$value_minus=0;
			$value_plus=0;

            foreach ($products as $product) {
				if($product['type']=="addition")
				$value_plus=$value_plus+$product['value'];
			    if($product['type']=="subtraction")
				$value_minus=$value_minus+$product['value'];

                $product['adjustment_id'] = $adjustment_id;
                $this->db->insert('adjustment_items_store', $product);
                $this->syncAdjustment_store($product);
            }

			$final_value=$value_plus-$value_minus;

			if($value_minus>0)
			{
		    $date = date('Y-m-d H:i:s');
            if ($this->Settings->accounting){
            $data1 = [
                'date'         => $date,
                'reference'    => $data['reference_no'],
                'amount'       => $value_minus,
                'total'       => $value_minus,
                'grand_total'       => $value_minus,
                'order_tax_id'       => $this->getTaxRatesNT() ? $this->getTaxRatesNT()->id : null,
                'created_by'   => $data['created_by'],
                'note'         => $data['note'],
                'payment_status'       => 'paid',
				'category_id'  => "0",
                'warehouse_id' => $data['warehouse_id'],
				'other' => 1,
            ];
			$this->addExpense($data1);

			}
            }


			if($final_value<0)
			{
		    if ($this->Settings->accounting){

						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;


				$this->site->createEntry($this->site->getAccountsSettings()->warehouses_adjustment_inability,$entryWarehouseAcc,abs($final_value),$data['reference_no'],'Adjustment');
			}
			}

			if($value_plus>0)
			{
		    $date = date('Y-m-d H:i:s');

            $data2 = [
                'date'         => $date,
                'reference'    => $data['reference_no'],
                'amount'       => $value_plus,
                'created_by'   => $data['created_by'],
                'note'         => $data['note'],
                'category_id'  => "0",
                'warehouse_id' => $data['warehouse_id'],
            ];
			$this->addRevenue($data2);


			}
			if($final_value>0)
			{
			if ($this->Settings->accounting){
						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

				$this->site->createEntry($entryWarehouseAcc,$this->site->getAccountsSettings()->warehouses_adjustment_surplus,$final_value,$data['reference_no'],'Adjustment');
			}
		}


            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
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
   public function addExpense($data = [])
    {
        if ($this->db->insert('expenses', $data)) {
            if ($this->site->getReference('ex') == $data['reference']) {
                $this->site->updateReference('ex');
            }
            return true;
        }
        return false;
    }


    public function addAjaxProduct($data)
    {
        if ($this->db->insert('products', $data)) {
            $product_id = $this->db->insert_id();
            return $this->getProductByID($product_id);
        }
        return false;
    }

	public function addAjaxProductFromSales($data)
    {

        $slug=trim($data['name']);
		$data['slug']= str_replace(" ","-",$slug);

        if ($this->db->insert('products', $data)) {
            $product_id = $this->db->insert_id();
			$brId=$this->site->getWarehouseByID($data['warehouse'])->brId;
			    $this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $data['warehouse'], 'quantity' => 0, 'avg_cost' => 0, 'brId' => $brId]);
			return $this->getProductByID($product_id);
        }
        return false;
    }

	public function addAjaxProductFromSales_new($data)
    {
        $slug=trim($data['name']);
		$data['slug']= str_replace(" ","-",$slug);

        if ($this->db->insert('products', $data)) {
            $product_id = $this->db->insert_id();
            $brId=$this->site->getWarehouseByID($data['warehouse'])->brId;
			    $this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $data['warehouse'], 'quantity' => 0, 'avg_cost' => 0, 'brId' => $brId]);
			return $this->getProductByID($product_id);
        }
        return false;
    }


	public function updateAjaxProductFromSales($product_id,$data)
    {
        if ($this->db->update('products', $data,['id' => $product_id])) {
           return $this->getProductByID($product_id);
        }
        return false;
    }


    public function addProduct($data, $items, $warehouse_qty, $product_attributes, $photos,$wh_id,$product_price_groups = null)
    {
        if ($this->db->insert('products', $data)) {
            $product_id = $this->db->insert_id();

            if ($items) {
                foreach ($items as $item) {
                    $item['product_id'] = $product_id;
					$product_details = $this->getProductByCode($item['item_code']);

                    $item['item_id'] = $product_details->id;
                    $this->db->insert('combo_items', $item);
                }
            }




				    foreach ($wh_id as $warehouse) {
                        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {
                                $this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse, 'quantity' => 0, 'grpId' => $data['grpId'], 'coId' => $data['coId'], 'brId' => $data['brId']]);
                            }else{
                                $this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse, 'quantity' => 0]);
                            }


                }







            if($product_price_groups){
                foreach ($product_price_groups as $price_groups) {
                    $unit_name=$this->getUnitById($price_groups['product_unit_id'])->name;
                    $unit_base_name=$this->getUnitById($data['unit'])->name;
                    $unit_code=trim($unit_name)."1x".trim($price_groups['factor'])."".trim($unit_base_name);
                    $ch=$this->site->checkUnitsByBUID($data['unit'],$unit_code,$price_groups['factor']);

                    if($ch){

                        $product_unit_id=$ch->id;

                    }else{


                        $data1 = [
                            'name'            => $unit_code,
                            'code'            => $unit_code,
                            'base_unit'       => $data['unit'],
                            'operator'        => '*',
                            'operation_value' => $price_groups['factor'],
                        ];
                        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {

                            $data1  ['grpId']=1;
                            $data1  ['coId']=1;
                            $data1  ['brId']=$data['brId'] ? $data['brId'] : 1;

                        }
                        $product_unit_id=$this->site->addUnit($data1);





                    }

                    $this->setProductPriceForPriceGroup($product_id, $price_groups['price_group_id'], $price_groups['selling_price'], $product_unit_id, $price_groups['pricing_code']);

                }
            }

            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);

            if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
                    if (isset($wh_qty['quantity']) && !empty($wh_qty['quantity'])) {
                        $this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $wh_qty['warehouse_id'], 'quantity' => $wh_qty['quantity'], 'rack' => $wh_qty['rack'], 'avg_cost' => $data['cost']]);

                        if (!$product_attributes) {
                            $tax_rate_id = $tax_rate ? $tax_rate->id : null;
                            $tax         = $tax_rate ? (($tax_rate->type == 1) ? $tax_rate->rate . '%' : $tax_rate->rate) : null;
                            $unit_cost   = $data['cost'];
                            if ($tax_rate) {
                                if ($tax_rate->type == 1 && $tax_rate->rate != 0) {
                                    if ($data['tax_method'] == '0') {
                                        $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / (100 + $tax_rate->rate);
                                        $net_item_cost = $data['cost'] - $pr_tax_val;
                                        $item_tax      = $pr_tax_val * $wh_qty['quantity'];
                                    } else {
                                        $net_item_cost = $data['cost'];
                                        $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / 100;
                                        $unit_cost     = $data['cost'] + $pr_tax_val;
                                        $item_tax      = $pr_tax_val * $wh_qty['quantity'];
                                    }
                                } else {
                                    $net_item_cost = $data['cost'];
                                    $item_tax      = $tax_rate->rate;
                                }
                            } else {
                                $net_item_cost = $data['cost'];
                                $item_tax      = 0;
                            }

                            $subtotal = (($net_item_cost * $wh_qty['quantity']) + $item_tax);

                            $item = [
                                'product_id'        => $product_id,
                                'product_code'      => $data['code'],
                                'product_name'      => $data['name'],
                                'net_unit_cost'     => $net_item_cost,
                                'unit_cost'         => $unit_cost,
                                'real_unit_cost'    => $unit_cost,
                                'quantity'          => $wh_qty['quantity'],
                                'quantity_balance'  => $wh_qty['quantity'],
                                'quantity_received' => $wh_qty['quantity'],
                                'item_tax'          => $item_tax,
                                'tax_rate_id'       => $tax_rate_id,
                                'tax'               => $tax,
                                'subtotal'          => $subtotal,
                                'warehouse_id'      => $wh_qty['warehouse_id'],
                                'date'              => date('Y-m-d'),
                                'status'            => 'received',
                                'expiry'            => $data['expiry']?$this->sma->fsd($data['expiry']):Null,
                            ];
                            $this->db->insert('purchase_items', $item);
                            $this->site->syncProductQty($product_id, $wh_qty['warehouse_id']);
                        }
                    }
                }
            }

            if ($product_attributes) {
                foreach ($product_attributes as $pr_attr) {
                    $pr_attr_details = $this->getPrductVariantByPIDandName($product_id, $pr_attr['name']);

                    $pr_attr['product_id'] = $product_id;
                    $variant_warehouse_id  = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    if ($pr_attr_details) {
                        $option_id = $pr_attr_details->id;
                    } else {
                        $this->db->insert('product_variants', $pr_attr);
                        $option_id = $this->db->insert_id();
                    }
                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']]);

                        $tax_rate_id = $tax_rate ? $tax_rate->id : null;
                        $tax         = $tax_rate ? (($tax_rate->type == 1) ? $tax_rate->rate . '%' : $tax_rate->rate) : null;
                        $unit_cost   = $data['cost'];
                        if ($tax_rate) {
                            if ($tax_rate->type == 1 && $tax_rate->rate != 0) {
                                if ($data['tax_method'] == '0') {
                                    $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / (100 + $tax_rate->rate);
                                    $net_item_cost = $data['cost'] - $pr_tax_val;
                                    $item_tax      = $pr_tax_val * $pr_attr['quantity'];
                                } else {
                                    $net_item_cost = $data['cost'];
                                    $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / 100;
                                    $unit_cost     = $data['cost'] + $pr_tax_val;
                                    $item_tax      = $pr_tax_val * $pr_attr['quantity'];
                                }
                            } else {
                                $net_item_cost = $data['cost'];
                                $item_tax      = $tax_rate->rate;
                            }
                        } else {
                            $net_item_cost = $data['cost'];
                            $item_tax      = 0;
                        }

                        $subtotal = (($net_item_cost * $pr_attr['quantity']) + $item_tax);
                        $item     = [
                            'product_id'        => $product_id,
                            'product_code'      => $data['code'],
                            'product_name'      => $data['name'],
                            'net_unit_cost'     => $net_item_cost,
                            'unit_cost'         => $unit_cost,
                            'quantity'          => $pr_attr['quantity'],
                            'option_id'         => $option_id,
                            'quantity_balance'  => $pr_attr['quantity'],
                            'quantity_received' => $pr_attr['quantity'],
                            'item_tax'          => $item_tax,
                            'tax_rate_id'       => $tax_rate_id,
                            'tax'               => $tax,
                            'subtotal'          => $subtotal,
                            'warehouse_id'      => $variant_warehouse_id,
                            'date'              => date('Y-m-d'),
                            'status'            => 'received',
                        ];
                        $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : null;
                        $this->db->insert('purchase_items', $item);
                    }

                    foreach ($warehouses as $warehouse) {
                        if (!$this->getWarehouseProductVariant($warehouse->id, $product_id, $option_id)) {
                            $this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0]);
                        }
                    }

                    $this->site->syncVariantQty($option_id, $variant_warehouse_id);
                }
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('product_photos', ['product_id' => $product_id, 'photo' => $photo]);
                }
            }

            $this->site->syncQuantity(null, null, null, $product_id);

            if(isset($this->Settings->isStore) && $this->Settings->isStore==2)
              {

                $this->load->admin_model('shop_admin_model');
                $syncSettings        = $this->shop_admin_model->getShopSettings();


        $url = $syncSettings->base_url.'/api/product/'.$syncSettings->store_id.'/store';

        $uploads_url = base_url('assets/uploads/');
        $total        = $this->shop_admin_model->getProductsCount()->total;
        $products        = $this->shop_admin_model->getProducts();
        $to=0;


            foreach ($products as $pr) {
                $image=$uploads_url."".($pr->image ? ($pr->image!="no_image.png" ? $pr->image : 'placeholder.png') : 'placeholder.png');
                $weight=(isset($pr->weight) ? $pr->weight : 0);

                $ch = curl_init($url);
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $headers[] = 'Accept:application/json';
                $headers[] = "Authorization: Bearer ".$syncSettings->token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
                $data='{"name": "'.$pr->name.'","maincategory_id": "'.$pr->cat_shop_id.'","sale_price": "'.$pr->price.'","description": "'.$pr->product_details.'","image_url": "'.$image.'","tax_method": "'.$pr->tax_method.'","product_weight": "'.$weight.'"}';
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                /* execute request */
                $result = json_decode(curl_exec($ch));
                //$result1 = (curl_exec($ch));


                /* close cURL resource */
                curl_close($ch);


                if($result->data){


                    $dataUpdate = [
				        'shop_id'            => $result->data->id,

			        ];
                    if ($this->shop_admin_model->updateProducts($pr->id,$dataUpdate)) {
                        $to++;
                    }
                }

            }

			  }
            return true;
        }
        return false;
    }

    public function addQuantity($product_id, $warehouse_id, $quantity, $rack = null)
    {
        if ($this->getProductQuantity($product_id, $warehouse_id)) {
            if ($this->updateQuantity($product_id, $warehouse_id, $quantity, $rack)) {
                return true;
            }
        } else {
            if ($this->insertQuantity($product_id, $warehouse_id, $quantity, $rack)) {
                return true;
            }
        }

        return false;
    }

    public function addStockCount($data)
    {
        if ($this->db->insert('stock_counts', $data)) {
            return true;
        }
        return false;
    }

    public function deleteAdjustment($id)
    {
        $this->reverseAdjustment($id);
        if ($this->db->delete('adjustments', ['id' => $id]) && $this->db->delete('adjustment_items', ['adjustment_id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteProduct($id)
    {
        if ($this->db->delete('products', ['id' => $id]) && $this->db->delete('warehouses_products', ['product_id' => $id])) {
            $this->db->delete('warehouses_products_variants', ['product_id' => $id]);
            $this->db->delete('product_variants', ['product_id' => $id]);
            $this->db->delete('product_photos', ['product_id' => $id]);
            $this->db->delete('product_prices', ['product_id' => $id]);
            return true;
        }
        return false;
    }

    public function fetch_products($category_id, $limit, $start, $subcategory_id = null)
    {
        $this->db->limit($limit, $start);
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('products');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function finalizeStockCount($id, $data, $products)
    {
        if ($this->db->update('stock_counts', $data, ['id' => $id])) {
            foreach ($products as $product) {
                $this->db->insert('stock_count_items', $product);
            }
            return true;
        }
        return false;
    }

    public function getAdjustmentByCountID($count_id)
    {
        $q = $this->db->get_where('adjustments', ['count_id' => $count_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getAdjustmentByCountID_count($count_id)
    {
        $q = $this->db->get_where('adjustments_count', ['count_id' => $count_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAdjustmentByCountID_store($count_id)
    {
        $q = $this->db->get_where('adjustments_store', ['count_id' => $count_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAdjustmentByID($id)
    {
        $q = $this->db->get_where('adjustments', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getDestructionByID($id)
    {
        $q = $this->db->get_where('destruction', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

 public function getAdjustmentByID_count($id)
    {
        $q = $this->db->get_where('adjustments_count', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
 public function getAdjustmentByID_store($id)
    {
        $q = $this->db->get_where('adjustments_store', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAdjustmentItems($adjustment_id)
    {
        $this->db->select('adjustment_items.*, products.code as product_code, products.name as product_name, products.image, products.details as details, product_variants.name as variant, units.name as unit_name')
            ->join('products', 'products.id=adjustment_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=adjustment_items.option_id', 'left')
            ->join('units', 'units.id=adjustment_items.unit', 'left')
            ->group_by('adjustment_items.id')
            ->order_by('product_id', 'desc');

        $this->db->where('adjustment_id', $adjustment_id);

        $q = $this->db->get('adjustment_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
    public function getDestructionItems($adjustment_id)
    {
        $this->db->select('destruction_items.*, products.code as product_code, products.name as product_name, products.image, products.details as details, product_variants.name as variant, units.name as unit_name')
            ->join('products', 'products.id=destruction_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=destruction_items.option_id', 'left')
            ->join('units', 'units.id=destruction_items.unit', 'left')
            ->group_by('destruction_items.id')
            ->order_by('id', 'asc');

        $this->db->where('adjustment_id', $adjustment_id);

        $q = $this->db->get('destruction_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAdjustmentItems_count($adjustment_id)
    {
        $this->db->select('adjustment_items_count.*, products.code as product_code, products.name as product_name, products.image, products.details as details, product_variants.name as variant, units.name as unit_name')
            ->join('products', 'products.id=adjustment_items_count.product_id', 'left')
            ->join('product_variants', 'product_variants.id=adjustment_items_count.option_id', 'left')
            ->join('units', 'units.id=adjustment_items_count.unit', 'left')
            ->group_by('adjustment_items_count.id')
            ->order_by('id', 'asc');

        $this->db->where('adjustment_id', $adjustment_id);

        $q = $this->db->get('adjustment_items_count');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAdjustmentItems_store($adjustment_id)
    {
        $this->db->select('adjustment_items_store.*, products.code as product_code, products.name as product_name, products.image, products.details as details, product_variants.name as variant, units.name as unit_name')
            ->join('products', 'products.id=adjustment_items_store.product_id', 'left')
            ->join('product_variants', 'product_variants.id=adjustment_items_store.option_id', 'left')
            ->join('units', 'units.id=adjustment_items_store.unit', 'left')
            ->group_by('adjustment_items_store.id')
            ->order_by('id', 'asc');

        $this->db->where('adjustment_id', $adjustment_id);

        $q = $this->db->get('adjustment_items_store');
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
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllVariants()
    {
        $q = $this->db->get('variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllWarehousesWithPQ($product_id)
    {
        /*$this->db->select('' . $this->db->dbprefix('warehouses') . '.id as id1,' . $this->db->dbprefix('warehouses') . '.name as name,' . $this->db->dbprefix('warehouses') . '.code as code, ' . $this->db->dbprefix('warehouses_products') . '.quantity as quantity, ' . $this->db->dbprefix('warehouses_products') . '.rack as rack, '. $this->db->dbprefix('warehouses_products') . '.avg_cost as avg_cost,'.$this->db->dbprefix('warehouses_products') . '.id as id2')
        //$this->db->select('warehouses.id,warehouses.name,warehouses.code, warehouses_products.quantity, warehouses_products.rack, warehouses_products.avg_cost')
        //$this->db->select('warehouses.*, warehouses_products.id')
          //$this->db->select('*')
            ->join('warehouses_products', 'warehouses_products.warehouse_id=warehouses.id', 'left')
            ->where('warehouses_products.product_id', $product_id)
            ->group_by('warehouses.id');
        $q = $this->db->get('warehouses');*/

		$myQuery = "SELECT w.*,wp.quantity,wp.rack,wp.avg_cost from  " . $this->db->dbprefix('warehouses') . " w," . $this->db->dbprefix('warehouses_products') . " wp where wp.warehouse_id=w.id and wp.product_id='".$product_id."'";
        $q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBrandByName($name)
    {
        $q = $this->db->get_where('brands', ['name' => $name], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCategoryByCode($code)
    {
        $q = $this->db->get_where('categories', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCategoryProducts($category_id)
    {
        $q = $this->db->get_where('products', ['category_id' => $category_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPrductVariantByPIDandName($product_id, $name)
    {
        $q = $this->db->get_where('product_variants', ['product_id' => $product_id, 'name' => $name], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductByCategoryID($id)
    {
        $q = $this->db->get_where('products', ['category_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return true;
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

    public function getProductPriceByCode($code)
    {
        $q = $this->db->get_where('product_prices', ['product_code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

     public function getProductByCodeAndUint($code,$unit)
    {
        $q = $this->db->get_where('products', ['trim(code)' => $code,'unit' => $unit], 1);
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

    public function getProductComboItems($pid)
    {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('combo_items') . '.quantity as qty, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('combo_items') . '.unit_price as price')->join('products', 'products.code=combo_items.item_code', 'left')->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', ['product_id' => $pid]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

	public function getProductComboItems2($pid)
    {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('combo_items') . '.quantity as qty, ' . $this->db->dbprefix('units') . '.name as unit, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('combo_items') . '.unit_price as price')
		->join('products', 'products.code=combo_items.item_code', 'left')->group_by('combo_items.id')
		->join('units', 'units.id=products.unit', 'left');
        $q = $this->db->get_where('combo_items', ['product_id' => $pid]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

    public function getProductDetail($id)
    {
        $this->db->select($this->db->dbprefix('products') . '.*, ' . $this->db->dbprefix('tax_rates') . '.name as tax_rate_name, ' . $this->db->dbprefix('tax_rates') . '.code as tax_rate_code, c.code as category_code, sc.code as subcategory_code', false)
            ->join('tax_rates', 'tax_rates.id=products.tax_rate', 'left')
            ->join('categories c', 'c.id=products.category_id', 'left')
            ->join('categories sc', 'sc.id=products.subcategory_id', 'left');
        $q = $this->db->get_where('products', ['products.id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductDetails($id)
    {
        $this->db->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . '.name, ' . $this->db->dbprefix('categories') . '.code as category_code, cost, price, quantity, alert_quantity')
            ->join('categories', 'categories.id=products.category_id', 'left');
        $q = $this->db->get_where('products', ['products.id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductNames($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('products') . '.id, code, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('products') . '.price as price, ' . $this->db->dbprefix('product_variants') . '.name as vname')
            ->where("type != 'combo' AND "
                . '(' . $this->db->dbprefix('products') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR item_no LIKE '%" . $term . "%' OR second_name LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('products') . ".name, ' (', code, ')') LIKE '%" . $term . "%')");
        $this->db->join('product_variants', 'product_variants.product_id=products.id', 'left')
            ->where('' . $this->db->dbprefix('product_variants') . '.name', null)
            ->group_by('products.id')->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function getProductNames2($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('products') . '.id, code, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('products') . '.cost as cost, ' . $this->db->dbprefix('products') . '.unit as unit, ' . $this->db->dbprefix('product_variants') . '.name as vname')
            //->where("type != 'combo' AND "
            ->where(""
                . '(' . $this->db->dbprefix('products') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR item_no LIKE '%" . $term . "%' OR second_name LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('products') . ".name, ' (', code, ')') LIKE '%" . $term . "%')");
        $this->db->join('product_variants', 'product_variants.product_id=products.id', 'left')
            ->where('' . $this->db->dbprefix('product_variants') . '.name', null)
            ->group_by('products.id')->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getProductOptions($pid)
    {
        $q = $this->db->get_where('product_variants', ['product_id' => $pid]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductOptionsWithWH($pid)
    {
        /*$this->db->select($this->db->dbprefix('product_variants') . '.*, ' . $this->db->dbprefix('warehouses') . '.name as wh_name, ' . $this->db->dbprefix('warehouses') . '.id as warehouse_id, ' . $this->db->dbprefix('warehouses_products_variants') . '.quantity as wh_qty')
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
            ->join('warehouses', 'warehouses.id=warehouses_products_variants.warehouse_id', 'left')
            ->group_by(['' . $this->db->dbprefix('product_variants') . '.id', '' . $this->db->dbprefix('warehouses_products_variants') . '.warehouse_id'])
            ->order_by('product_variants.id');
        $q = $this->db->get_where('product_variants', ['product_variants.product_id' => $pid, 'warehouses_products_variants.quantity !=' => null]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }*/
		return false;
    }

	public function getProductOptionsWithWH_new($pid)
    {
        $this->db->select($this->db->dbprefix('product_variants') . '.*, ' . $this->db->dbprefix('warehouses') . '.name as wh_name, ' . $this->db->dbprefix('warehouses') . '.id as warehouse_id, ' . $this->db->dbprefix('warehouses_products_variants') . '.quantity as wh_qty,' . $this->db->dbprefix('warehouses_products_variants') . '.avg_cost as wh_avg_cost')
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
            ->join('warehouses', 'warehouses.id=warehouses_products_variants.warehouse_id', 'left')
            ->group_by(['' . $this->db->dbprefix('product_variants') . '.id', '' . $this->db->dbprefix('warehouses_products_variants') . '.warehouse_id'])
            ->order_by('product_variants.id');
        $q = $this->db->get_where('product_variants', ['product_variants.product_id' => $pid, 'warehouses_products_variants.quantity !=' => null]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;
    }

    public function getProductPhotos($id)
    {
        $q = $this->db->get_where('product_photos', ['product_id' => $id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getProductQuantity($product_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse], 1);
        if ($q->num_rows() > 0) {
            return $q->row_array();
        }
        return false;
    }
    public function getProductQuantity_count($product_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse], 1);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
               return $row->quantity;

            }
            return true;
        }
        return false;
    }

    public function getProductsForPrinting($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('products') . '.id, code, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('products') . '.price as price, ' . $this->db->dbprefix('products') . '.unit as unit')
            ->where('(' . $this->db->dbprefix('products') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR item_no LIKE '%" . $term . "%' OR second_name LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('products') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductVariantByID($product_id, $id)
    {
        $q = $this->db->get_where('product_variants', ['product_id' => $product_id, 'id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductVariantByName($product_id, $name)
    {
        $q = $this->db->get_where('product_variants', ['product_id' => $product_id, 'name' => $name], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductVariantID($product_id, $name)
    {
        $q = $this->db->get_where('product_variants', ['product_id' => $product_id, 'name' => $name], 1);
        if ($q->num_rows() > 0) {
            $variant = $q->row();
            return $variant->id;
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

    public function getProductWarehouseOptions($option_id)
    {
        $q = $this->db->get_where('warehouses_products_variants', ['option_id' => $option_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductWithCategory($id)
    {
        $this->db->select($this->db->dbprefix('products') . '.*, ' . $this->db->dbprefix('categories') . '.name as category')
        ->join('categories', 'categories.id=products.category_id', 'left');
        $q = $this->db->get_where('products', ['products.id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPurchasedQty($id)
    {
        $this->db->select('date_format(' . $this->db->dbprefix('purchases') . ".date, '%Y-%M') month, SUM( " . $this->db->dbprefix('purchase_items') . '.quantity ) as purchased, SUM( ' . $this->db->dbprefix('purchase_items') . '.subtotal ) as amount')
            ->from('purchases')
            ->join('purchase_items', 'purchases.id=purchase_items.purchase_id', 'left')
            ->group_by('date_format(' . $this->db->dbprefix('purchases') . ".date, '%Y-%m')")
            ->where($this->db->dbprefix('purchase_items') . '.product_id', $id)
            //->where('DATE(NOW()) - INTERVAL 1 MONTH')
            ->where('DATE_ADD(curdate(), INTERVAL 1 MONTH)')
            ->order_by('date_format(' . $this->db->dbprefix('purchases') . ".date, '%Y-%m') desc")->limit(3);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPurchaseItems($purchase_id)
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

    public function getQASuggestions($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('products') . '.id, code, unit, ' . $this->db->dbprefix('products') . '.name as name')
            ->where("type != 'combo'  AND "
                . '(' . $this->db->dbprefix('products') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR item_no LIKE '%" . $term . "%' OR second_name LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('products') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
		$this->db->where('hide !=', 1);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSoldQty($id)
    {
        $this->db->select('date_format(' . $this->db->dbprefix('sales') . ".date, '%Y-%M') as month, SUM( " . $this->db->dbprefix('sale_items') . '.quantity ) as sold, SUM( ' . $this->db->dbprefix('sale_items') . '.subtotal ) as amount')
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
            ->where($this->db->dbprefix('sale_items') . '.product_id', $id)
            //->where('DATE(NOW()) - INTERVAL 1 MONTH')
            ->where('DATE_ADD(curdate(), INTERVAL 1 MONTH)')
            ->group_by('date_format(' . $this->db->dbprefix('sales') . ".date, '%Y-%m')")
            ->order_by('date_format(' . $this->db->dbprefix('sales') . ".date, '%Y-%m') desc")->limit(3);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getStockCountItems($stock_count_id)
    {

		$this->db->select('stock_count_items.*, units.name as unit_name')
            ->join('units', 'units.id=stock_count_items.unit', 'left');

        $this->db->where('stock_count_id', $stock_count_id);

        $q = $this->db->get('stock_count_items');


		//$q = $this->db->get_where('stock_count_items', ['stock_count_id' => $stock_count_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
    public function getStockCountsMaxId()
    {
		$this->db->select('max(id) as id');
        $q = $this->db->get('stock_counts');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                return $row->id;
				//$data[] = $row;
            }
            //return $data;
        }
        return null;
    }

    public function getStockCountProducts($warehouse_id, $type, $categories = null, $brands = null)
    {
        $this->db->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('warehouses_products')}.quantity as quantity, {$this->db->dbprefix('products')}.unit as unit")
        ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
        ->where('warehouses_products.warehouse_id', $warehouse_id)
        //->where('products.type', 'standard')
        //->or_where('products.type', 'manufacturing')
        ->where(" (products.type='standard' OR products.type='manufacturing') ")
        ->order_by('products.code', 'asc');
        if ($categories) {
            $r = 1;
            $this->db->group_start();
            foreach ($categories as $category) {
                if ($r == 1) {
                    $this->db->where('products.category_id', $category);
                } else {
                    $this->db->or_where('products.category_id', $category);
                }
                $r++;
            }
            $this->db->group_end();
        }
        if ($brands) {
            $r = 1;
            $this->db->group_start();
            foreach ($brands as $brand) {
                if ($r == 1) {
                    $this->db->where('products.brand', $brand);
                } else {
                    $this->db->or_where('products.brand', $brand);
                }
                $r++;
            }
            $this->db->group_end();
        }

        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getStockCountProductsQuantity($warehouse_id, $type, $categories = null, $brands = null, $code = null)
    {
        $this->db->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('warehouses_products')}.quantity as quantity")
        ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
        ->where('warehouses_products.warehouse_id', $warehouse_id)
        //->where('products.type', 'standard')
        ->where(" (products.type='standard' OR products.type='manufacturing') ")
        ->where('products.code', $code)
        ->order_by('products.code', 'asc');
        if ($categories) {
            $r = 1;
            $this->db->group_start();
            foreach ($categories as $category) {
                if ($r == 1) {
                    $this->db->where('products.category_id', $category);
                } else {
                    $this->db->or_where('products.category_id', $category);
                }
                $r++;
            }
            $this->db->group_end();
        }
        if ($brands) {
            $r = 1;
            $this->db->group_start();
            foreach ($brands as $brand) {
                if ($r == 1) {
                    $this->db->where('products.brand', $brand);
                } else {
                    $this->db->or_where('products.brand', $brand);
                }
                $r++;
            }
            $this->db->group_end();
        }

        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getStockCountProductVariants($warehouse_id, $product_id)
    {
        $this->db->select("{$this->db->dbprefix('product_variants')}.name, {$this->db->dbprefix('warehouses_products_variants')}.quantity as quantity")
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left');
        $q = $this->db->get_where('product_variants', ['product_variants.product_id' => $product_id, 'warehouses_products_variants.warehouse_id' => $warehouse_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getStouckCountByID($id)
    {
        $q = $this->db->get_where('stock_counts', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSubCategories($parent_id)
    {
        $this->db->select('id as id, name as text')
        ->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getMaxProductNumber()
    {
        $this->db->select('(MAX(CAST(product_number AS SIGNED))+1) as max_product_number');
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSubCategoryProducts($subcategory_id)
    {
        $q = $this->db->get_where('products', ['subcategory_id' => $subcategory_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSupplierByName($name)
    {
        $q = $this->db->get_where('companies', ['name' => $name, 'group_name' => 'supplier'], 1);
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

    public function getTransferItems($transfer_id)
    {
        $q = $this->db->get_where('purchase_items', ['transfer_id' => $transfer_id]);
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
    public function getUnitById($id)
    {
        $q = $this->db->get_where('units', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getCostById($id,$warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products', ['product_id' => $id,'warehouse_id'=>$warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row()->avg_cost;
        }
        return false;
    }

    public function getWarehouseProductVariant($warehouse_id, $product_id, $option_id = null)
    {
        $q = $this->db->get_where('warehouses_products_variants', ['product_id' => $product_id, 'option_id' => $option_id, 'warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function has_purchase($product_id, $warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('purchase_items', ['product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function insertQuantity($product_id, $warehouse_id, $quantity, $rack = null)
    {
        $product = $this->site->getProductByID($product_id);
        if ($this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity, 'rack' => $rack, 'avg_cost' => $product->cost])) {
            $this->site->syncProductQty($product_id, $warehouse_id);
            return true;
        }
        return false;
    }

    public function products_count($category_id, $subcategory_id = null)
    {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($subcategory_id) {
            $this->db->where('subcategory_id', $subcategory_id);
        }
        $this->db->from('products');
        return $this->db->count_all_results();
    }

    public function reverseAdjustment($id)
    {
        if ($products = $this->getAdjustmentItems($id)) {
            foreach ($products as $adjustment) {
                $clause = ['product_id' => $adjustment->product_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id, 'status' => 'received'];
                $qty    = $adjustment->type == 'subtraction' ? (0 + $adjustment->quantity) : (0 - $adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncProductQty($adjustment->product_id, $adjustment->warehouse_id);
                if ($adjustment->option_id) {
                    $this->site->syncVariantQty($adjustment->option_id, $adjustment->warehouse_id, $adjustment->product_id);
                }
            }
        }
    }



	public function reverseDestruction($id)
    {
        if ($products = $this->getDestructionItems($id)) {
            foreach ($products as $adjustment) {
                $clause = ['product_id' => $adjustment->product_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id];
                $qty    = $adjustment->type == 'subtraction' ? (0 + $adjustment->quantity) : (0 - $adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncProductQty($adjustment->product_id, $adjustment->warehouse_id);
                if ($adjustment->option_id) {
                    $this->site->syncVariantQty($adjustment->option_id, $adjustment->warehouse_id, $adjustment->product_id);
                }
            }
        }
    }

    public function setRack($data)
    {
        if ($this->db->update('warehouses_products', ['rack' => $data['rack']], ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']])) {
            return true;
        }
        return false;
    }

    public function syncAdjustment($data = [])
    {
        if (!empty($data)) {
            $clause = ['product_id' => $data['product_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
            $qty    = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
            $this->site->setPurchaseItem($clause, $qty);

            $this->site->syncProductQty($data['product_id'], $data['warehouse_id']);
            if ($data['option_id']) {
                $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['product_id']);
            }
        }
    }

    public function syncAdjustmentDestruction($data = [])
    {
        if (!empty($data)) {

            $product = $this->site->getProductByID($data['product_id']);

            //return $product;

            if ($product->type == 'standard' || $product->type == 'manufacturing' ) {
                $clause = ['product_id' => $data['product_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
                $qty    = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
                $this->site->setPurchaseItem($clause, $qty);

                $this->site->syncProductQty($data['product_id'], $data['warehouse_id']);
                    if ($data['option_id']) {
                        $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['product_id']);
                    }
                } elseif ($product->type == 'combo') {
                    $wh          = $this->Settings->overselling ? null : $data['warehouse_id'];
                    $combo_items = $this->site->getProductComboItems($data['product_id'], $wh);
                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard'  || $combo_item->type == 'manufacturing') {

                            $clause = ['product_id' => $combo_item->id, 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
                            $qty    =  0 - ($data['quantity'] * $combo_item->qty) ;
                            $this->site->setPurchaseItem($clause, $qty);

                            $this->site->syncProductQty($combo_item->id, $data['warehouse_id']);
                        }else if ($combo_item->type == 'combo') {
							$combo_items2 = $this->site->getProductComboItems($combo_item->id, $wh);
							foreach ($combo_items2 as $combo_item2) {


								if ($combo_item2->type == 'standard' || $combo_item2->type == 'manufacturing' ) {

                                    $clause = ['product_id' => $combo_item2->id, 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
                                    $qty    = $data['type'] ==  0 - ($data['quantity'] * $combo_item2->qty);
                                    $this->site->setPurchaseItem($clause, $qty);

									$this->site->syncProductQty($combo_item2->id, $data['warehouse_id']);
								}
							}
                        }
                    }
                }
        }
    }

    public function syncAdjustment_store($data = [])
    {
        if (!empty($data)) {
            $clause = ['product_id' => $data['product_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
            $qty    = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
            $this->site->setPurchaseItem($clause, $qty);

            $this->site->syncProductQty($data['product_id'], $data['warehouse_id']);
            if ($data['option_id']) {
                $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['product_id']);
            }
        }
    }

    public function syncAdjustment_count($data = [])
    {
        if (!empty($data)) {
            $clause = ['product_id' => $data['product_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
            $qty    = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
            $this->site->setPurchaseItem($clause, $qty);
			$quantity=$this->getProductQuantity_count($data['product_id'], $data['warehouse_id']);
			$quantity=($quantity+$qty);

            $this->site->syncProductQty_count($data['product_id'], $data['warehouse_id'],$quantity);
            if ($data['option_id']) {
                $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['product_id']);
            }
        }
    }

    public function syncAdjustment_temp($data = [])
    {
        if (!empty($data)) {
            $clause = ['product_id' => $data['product_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
            $qty    = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
            $this->site->setPurchaseItem_temp($clause, $qty,$data['cost'],$data['expiry']);

            $this->site->syncProductQty_temp($data['product_id'], $data['warehouse_id'],$data['cost']);
            if ($data['option_id']) {
                $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['product_id']);
            }
        }
    }

    public function syncVariantQty($option_id)
    {
        $wh_pr_vars = $this->getProductWarehouseOptions($option_id);
        $qty        = 0;
        foreach ($wh_pr_vars as $row) {
            $qty += $row->quantity;
        }
        if ($this->db->update('product_variants', ['quantity' => $qty], ['id' => $option_id])) {
            return true;
        }
        return false;
    }

    public function totalCategoryProducts($category_id)
    {
        $q = $this->db->get_where('products', ['category_id' => $category_id]);
        return $q->num_rows();
    }

    public function updateAdjustment($id, $data, $products,$tvalue = null)
    {
        $approved=$this->getAdjustmentByID($id)->approved;
        if($data['approved']==1 && $approved==1)
        $this->reverseAdjustment($id);
        if ($this->db->update('adjustments', $data, ['id' => $id]) && $this->db->delete('adjustment_items', ['adjustment_id' => $id])) {
            foreach ($products as $product) {
                $product['adjustment_id'] = $id;
                $this->db->insert('adjustment_items', $product);
                if($data['approved']==1)
                $this->syncAdjustment_temp($product);

            }

			if ($this->Settings->accounting){
				$acc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses');
						$entryWarehouseAcc=$acc->id;
						if($entryWarehouseAcc==""){

                            $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                            $acc=$this->site->getAccountByID($entryWarehouseAcc);
                            }

				//if($acc->openingBalance==0){
					if($acc->accountSide=='Debit'){
						$debit=$tvalue;
						$credit=0;
						$balance=$tvalue;


					}else{
					    $debit=0;
						$credit=$tvalue;
						$balance=$tvalue;

					}

				$accounts[] = [
                    'accountID'   => $entryWarehouseAcc,
                    'debit'         => $debit,
                    'credit'     => $credit,
                    'balance'     => $balance,

                ];


				$data1 = [
                'openingDate'         => $data['date'],
                'note'         =>  $data['note'],
                'userID'   => $this->session->userdata('user_id'),
               ];

               if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data1  ['grpId']=$data['grpId'];
                $data1  ['coId']=$data['coId'];
                $data1  ['brId']=$data['brId'];

            }


               if($data['approved']==1 && $approved==1){


			   $this->site->updateOpeningBalance($acc->openingId,$data1, $accounts);
               }else{
                if($data['approved']==1)
               $this->site->addOpeningBalance($data1, $accounts);
               }
				//$this->site->createEntry($this->site->getAccountsSettings()->warehouses_destruction,$this->site->getAccountByRefId($data['warehouse_id'])->id,$data['value'],$data['reference_no'],'Destruction');
				//}
			}
            return true;
        }
        return false;
    }

     public function updateDestruction_inventory($id, $data, $products)
    {
        $this->reverseDestruction($id);
        if ($this->db->update('destruction', $data, ['id' => $id]) && $this->db->delete('destruction_items', ['adjustment_id' => $id])) {
            foreach ($products as $product) {
                $product['adjustment_id'] = $id;
                $this->db->insert('destruction_items', $product);
                $this->syncAdjustment($product);

            }
			if ($this->Settings->accounting){
                $entryType='Destruction';
                if($data['op_type']=="2"){
                    $entryType='Gifts_and_donations';
                }

                $entryDescription="";
                if(!empty($data['note'])){
                    $entryDescription=$this->input->post('note');
                }

                if($this->site->getEntryCount($data['reference_no'],$entryType)){
						$entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

                        $entryWarehouseDestructionAcc=$data['op_acct'];
						if($entryWarehouseDestructionAcc=="")
						$entryWarehouseDestructionAcc=$this->site->getAccountsSettings()->warehouses_destruction;

				$this->site->updateEntry($entryWarehouseDestructionAcc,$entryWarehouseAcc,$data['value'],$data['reference_no'],$entryType,null,null,$entryDescription,$entryDescription);
				}else{
                    $entryType='Destruction';
                        if($data['op_type']=="2"){
                            $entryType='Gifts_and_donations';
                        }

                        $entryDescription="";
                        if(!empty($data['note'])){
                            $entryDescription=$this->input->post('note');
                        }

                        $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
						if($entryWarehouseAcc=="")
						$entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

                        $entryWarehouseDestructionAcc=$data['op_acct'];
						if($entryWarehouseDestructionAcc=="")
						$entryWarehouseDestructionAcc=$this->site->getAccountsSettings()->warehouses_destruction;

				$this->site->createEntry($entryWarehouseDestructionAcc,$entryWarehouseAcc,$data['value'],$data['reference_no'],$entryType,null,null,$entryDescription,$entryDescription);
				}

			}
            return true;
        }
        return false;
    }


    public function updatePrice($data = [])
    {
        if ($this->db->update_batch('products', $data, 'code')) {
            return true;
        }
        return false;
    }


    public function updatePriceV2($data = [])
    {

        if(empty($data[0]['code'])){

                    if ($this->db->update('product_prices', ['price' => $data[0]['price']], ['id' => $data[0]['id']] )) {
                        return true;
                    }

        }else{

        if($this->getProductByCode($data[0]['code'])){
        if ($this->db->update_batch('products', $data, 'code')) {
            return true;
        }
        }else if($this->getProductPriceByCode($data[0]['code'])){
            if ($this->db->update('product_prices', ['price' => $data[0]['price']], ['product_code' => $data[0]['code']] )) {
                return true;
            }
        }
    }
        return false;
    }


    public function updateProduct($id, $data, $items, $warehouse_qty, $product_attributes, $photos, $update_variants,$wh_id,$product_price_groups = null)
    {

            $warehouseProducts=$this->site->getWarehouseProducts($id);
                foreach ($warehouseProducts as $warehouseProduct){

                    if(!$this->findObjectById($warehouseProduct->warehouse_id,$wh_id) &&  $warehouseProduct->quantity==0){
                        $this->db->delete('warehouses_products', ['id' => $warehouseProduct->id]);

                    }

                }

		    foreach ($wh_id as $warehouse) {

				if ($wp_details = $this->getWarehouseProductQuantity($warehouse, $id)) {
				}else{
                    $brId=$this->site->getWarehouseByID($warehouse)->brId;
                    $this->db->insert('warehouses_products', ['product_id' => $id, 'warehouse_id' => $warehouse, 'quantity' => 0, 'brId' => $brId]);
				}


                }

		if ($this->db->update('products', $data, ['id' => $id])) {
            if ($items) {
                $this->db->delete('combo_items', ['product_id' => $id]);
                foreach ($items as $item) {
                    $item['product_id'] = $id;
                    $product_details = $this->getProductByCode($item['item_code']);

                    $item['item_id'] = $product_details->id;
                    $this->db->insert('combo_items', $item);
                }
            }

            if($product_price_groups){
                foreach ($product_price_groups as $price_groups) {
                    $unit_name=$this->getUnitById($price_groups['product_unit_id'])->name;
                    $unit_base_name=$this->getUnitById($data['unit'])->name;
                    $unit_code=trim($unit_name)."1x".trim($price_groups['factor'])."".trim($unit_base_name);
                    $ch=$this->site->checkUnitsByBUID($data['unit'],$unit_code,$price_groups['factor']);

                    if($ch){

                        $product_unit_id=$ch->id;

                    }else{


                        $data1 = [
                            'name'            => $unit_code,
                            'code'            => $unit_code,
                            'base_unit'       => $data['unit'],
                            'operator'        => '*',
                            'operation_value' => $price_groups['factor'],
                        ];
                        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {

                            $data1  ['grpId']=1;
                            $data1  ['coId']=1;
                            $data1  ['brId']=$data['brId'] ? $data['brId'] : 1;

                        }
                        $product_unit_id=$this->site->addUnit($data1);





                    }

                    $this->setProductPriceForPriceGroup($id, $price_groups['price_group_id'], $price_groups['selling_price'], $product_unit_id, $price_groups['pricing_code']);

                }
            }

            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);

            if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
                    $this->db->update('warehouses_products', ['rack' => $wh_qty['rack']], ['product_id' => $id, 'warehouse_id' => $wh_qty['warehouse_id']]);
                }
            }

            if (!empty($update_variants)) {
                foreach ($update_variants as $variant) {
                    $vr = $this->getProductVariantByName($id, $variant['name']);
                    if ($vr) {
                        $this->db->update('product_variants', $variant, ['id' => $vr->id]);
                    } else {
                        if ($variant['id']) {
                            $this->db->delete('product_variants', ['id' => $variant['id']]);
                        } else {
                            $this->db->insert('product_variants', $variant);
                        }
                    }
                }
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('product_photos', ['product_id' => $id, 'photo' => $photo]);
                }
            }

            if ($product_attributes) {
                foreach ($product_attributes as $pr_attr) {
                    $pr_attr['product_id'] = $id;
                    $variant_warehouse_id  = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    $this->db->insert('product_variants', $pr_attr);
                    $option_id = $this->db->insert_id();

                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']]);

                        $tax_rate_id = $tax_rate ? $tax_rate->id : null;
                        $tax         = $tax_rate ? (($tax_rate->type == 1) ? $tax_rate->rate . '%' : $tax_rate->rate) : null;
                        $unit_cost   = $data['cost'];
                        if ($tax_rate) {
                            if ($tax_rate->type == 1 && $tax_rate->rate != 0) {
                                if ($data['tax_method'] == '0') {
                                    $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / (100 + $tax_rate->rate);
                                    $net_item_cost = $data['cost'] - $pr_tax_val;
                                    $item_tax      = $pr_tax_val * $pr_attr['quantity'];
                                } else {
                                    $net_item_cost = $data['cost'];
                                    $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / 100;
                                    $unit_cost     = $data['cost'] + $pr_tax_val;
                                    $item_tax      = $pr_tax_val * $pr_attr['quantity'];
                                }
                            } else {
                                $net_item_cost = $data['cost'];
                                $item_tax      = $tax_rate->rate;
                            }
                        } else {
                            $net_item_cost = $data['cost'];
                            $item_tax      = 0;
                        }

                        $subtotal = (($net_item_cost * $pr_attr['quantity']) + $item_tax);
                        $item     = [
                            'product_id'        => $id,
                            'product_code'      => $data['code'],
                            'product_name'      => $data['name'],
                            'net_unit_cost'     => $net_item_cost,
                            'unit_cost'         => $unit_cost,
                            'quantity'          => $pr_attr['quantity'],
                            'option_id'         => $option_id,
                            'quantity_balance'  => $pr_attr['quantity'],
                            'quantity_received' => $pr_attr['quantity'],
                            'item_tax'          => $item_tax,
                            'tax_rate_id'       => $tax_rate_id,
                            'tax'               => $tax,
                            'subtotal'          => $subtotal,
                            'warehouse_id'      => $variant_warehouse_id,
                            'date'              => date('Y-m-d'),
                            'status'            => 'received',
                        ];
                        $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : null;
                        $this->db->insert('purchase_items', $item);
                    }
                }
            }

            //$this->site->syncQuantity(null, null, null, $id);
            return true;
        }
        return false;
    }

    public function updateProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            if ($this->db->update('warehouses_products_variants', ['quantity' => $quantity], ['option_id' => $option_id, 'warehouse_id' => $warehouse_id])) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return true;
            }
        } else {
            if ($this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity])) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return true;
            }
        }
        return false;
    }

    public function updateQuantity($product_id, $warehouse_id, $quantity, $rack = null)
    {
        $data = $rack ? ['quantity' => $quantity, 'rack' => $rack] : $data = ['quantity' => $quantity];
        if ($this->db->update('warehouses_products', $data, ['product_id' => $product_id, 'warehouse_id' => $warehouse_id])) {
            $this->site->syncProductQty($product_id, $warehouse_id);
            return true;
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

    public function getWarehouseAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	    public function getWarehouseByCode($code)
    {
        $q = $this->db->get_where('warehouses', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getUnitsProductPrices($product_id,$price_group_id)
    {
        $this->db->select('units.*')
        ->join('product_prices', 'units.id=product_prices.product_unit_id	', 'left')
        ->where('product_prices.product_id', $product_id)->where('product_prices.price_group_id', $price_group_id)
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

    public function getUnitsProductPricesV2($product_id)
    {
        $this->db->select('units.*')
        ->join('product_prices', 'units.id=product_prices.product_unit_id	', 'left')
        ->where('product_prices.product_id', $product_id)
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

    public function getProductGroupPriceWithUnitV2($product_id, $product_unit_id)
    {

        $q = $this->db->get_where('product_prices', [ 'product_id' => $product_id, 'product_unit_id' => $product_unit_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }



	 public function addSynchProducts($products = [])
    {
        if (!empty($products)) {
			$sp = [];
            foreach ($products as $product) {
				if(!$this->getSynchProductsCount($product['warehouse_id'],$product['biller_id'],$product['product_code'])){
					$product['add']=1;
					$product['edit']=0;
				}

				if($product['add']==1){
				$sp = ['biller_id' => $product['biller_id'], 'warehouse_id' => $product['warehouse_id'], 'product_code' => $product['product_code'], 'api' => $product['api']];

                $this->db->insert('synch_products', $sp);
				}else if($product['edit']==1){

				$sp = ['biller_id' => $product['biller_id'], 'warehouse_id' => $product['warehouse_id'], 'product_code' => $product['product_code'], 'api' => $product['api']];

                $this->db->update('synch_products', ['api' => $product['api']], ['biller_id' => $product['biller_id'], 'warehouse_id' => $product['warehouse_id'], 'product_code' => $product['product_code']]);
				}




            }
            return true;
        }
        return false;
    }


    public function addSynchProduct($product = [])
    {
        if (!empty($product) && !empty($product['product_code']) && $this->getProductByCode($product['product_code'])) {
			$sp = [];

				if(!$this->getSynchProductsCount($product['warehouse_id'],$product['biller_id'],$product['product_code'])){
					//$product['add']=1;
					//$product['edit']=0;
                    $sp = ['biller_id' => $product['biller_id'], 'warehouse_id' => $product['warehouse_id'], 'product_code' => $product['product_code'], 'api' => $product['api']];

                $this->db->insert('synch_products', $sp);
				}else{
                    $sp = ['biller_id' => $product['biller_id'], 'warehouse_id' => $product['warehouse_id'], 'product_code' => $product['product_code'], 'api' => $product['api']];

                    $this->db->update('synch_products', ['api' => $product['api']], ['biller_id' => $product['biller_id'], 'warehouse_id' => $product['warehouse_id'], 'product_code' => $product['product_code']]);

                }







            return true;
        }
        return false;
    }


	public function getSynchProductsCount($warehouse_id,$biller_id,$product_code)
    {
        $q = $this->db->get_where('synch_products', ['warehouse_id' => $warehouse_id, 'biller_id' => $biller_id, 'product_code' => $product_code], 1);
        if ($q->num_rows() > 0) {
            //return $q->row();
			//return $this->db->count_all_results();
			return true;
        }
        return false;
    }


    public function updateSynchProducts($product_code)
    {




				if ($this->db->update('synch_products', ['api' => 0], ['product_code' => $product_code])) {
                 return true;
                 }

        return false;
    }


    public function getProductPriceByPId($id)
    {

		$this->db->select('product_prices.price as product_price , product_prices.product_code as product_code ,price_groups.name as price_groups_name, units.name as unit_name')
            ->join('price_groups', 'price_groups.id=product_prices.price_group_id', 'left')
            ->join('units', 'units.id=product_prices.product_unit_id', 'left')
            ->order_by('product_prices.price_group_id', 'asc');

        $this->db->where('product_id', $id);

        $q = $this->db->get('product_prices');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return false;
    }

	public function getAllWarehousesWithoutPQ()
    {
        $this->db->select('' . $this->db->dbprefix('warehouses_products') . '.*')
            ->join('warehouses', 'warehouses.id=warehouses_products.warehouse_id', 'left')
            //->where('warehouses_products.product_id', $product_id)
            ->group_by('warehouses_products.product_id');
        $q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBalanceQuantityForSales($product_id, $warehouse_id = null)
    {
        $this->db->select('SUM(COALESCE(quantity, 0)) as stock', false);
        $this->db->where('product_id', $product_id)->where('quantity !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        //$this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }


    public function getBalanceQuantityForPurchase($product_id, $warehouse_id = null)
    {
        $this->db->select('SUM(COALESCE(quantity, 0)) as stock', false);
        $this->db->where('product_id', $product_id)->where('quantity !=', 0);
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
	public function getAlltt_2()
    {
        $q = $this->db->get('tt_2');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
	public function updatett_2($price,$code)
    {
        $this->db->update('products', ['price'=>$price], ['code' => $code]);

    }


    public function updateSlug($products = [])
    {
        if (!empty($products)) {
            foreach ($products as $product) {
                $this->db->update('products', ['slug' => $this->sma->slug($product->name)], ['id' => $product->id]);

            }


            return true;
        }


        return false;
    }

    public function getAllProductsWithSlugNull()
    {
        $this->db->where('slug IS NULL', null);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function addUnitsDetails($units = [])
    {
        $flage=0;
        foreach ($units as $unit) {
            if($this->db->insert('units_details', $unit)){
                $flage=1;
            }
        }
        if($flage==1)
            return true;
        else
            return false;
    }

    public function addCategoriesDetails($categories = [])
    {
        $flage=0;
        foreach ($categories as $categorie) {
            if($this->db->insert('categories_details', $categorie)){
                $flage=1;
            }
        }
        if($flage==1)
            return true;
        else
            return false;
    }

    public function getSubCategoriesBybrId($parent_id,$brId)
    {




        $this->db->select('categories_details.category_id as id, categories.name as text')
        ->join('categories_details', 'categories_details.category_id=categories.id', 'left')
       ->where('categories.parent_id', $parent_id)->where('categories_details.brId', $brId)->order_by('categories.name');
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function addBrandsDetails($brands = [])
    {
        $flage=0;
        foreach ($brands as $brand) {
            if($this->db->insert('brands_details', $brand)){
                $flage=1;
            }
        }
        if($flage==1)
            return true;
        else
            return false;
    }


    public function addVariantsDetails($variants = [])
    {
        $flage=0;
        foreach ($variants as $variant) {
            if($this->db->insert('variants_details', $variant)){
                $flage=1;
            }
        }
        if($flage==1)
            return true;
        else
            return false;
    }


    function findObjectById($id,$wh_id){

        foreach ( $wh_id as $element ) {

                    if ( $id == $element) {
                return true;
            }
        }

        return false;
    }



    public function addProductAttribute($attribute){
        $attribute['product_id'] = $id;
        $variant_warehouse_id  = $attribute['warehouse_id'];
        unset($attribute['warehouse_id']);
        $this->db->insert('product_variants', $attribute);
        $option_id = $this->db->insert_id();

        return $option_id;
    }

    public function addWarehouseQuantity($wherehouse){
        $option_id = $wherehouse['option_id'];
        $warehouse_id = $wherehouse['warehouse_id'];
        $quantity = $wherehouse['quantity'];
        $product_id = $wherehouse['product_id'];

        if ($this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity])) {
            $this->site->syncVariantQty($option_id, $warehouse_id);
            return true;
        }
    }

    public function addProduct_autoSave($data, $items, $warehouse_qty, $product_attributes, $photos)
    {

        // if the id is not set, then it means that we are adding a new product, otherwise we are updating an existing product
        $added = false;



        if(array_key_exists('id',$data)){
            $product_id = $data['id'];
            unset($data['id']);
            $this->db->where('id', $product_id);
              $added =   $this->db->update('products',$data);
              $this->calculateProductDataCompleteness($product_id);

        //   echo $this->db->last_query();
        //     die();

        }else{
            // if there is no code set, then we will generate a new code for the product
            if (!isset($data['code']) || $data['code'] == null) {
                $time = time();
                $data['code'] =  $this->countCodes($time) > 0 ?  $time+1  : $time ;
            }

            // if there is no name_en set, then we will generate a new name for the product
            if (!isset($data['name_en']) || $data['name_en'] == null) {
                $data['name_en'] = 'Product EN ' . $data['code'];
            }
            // if there is no name set, then we will generate a new name for the product
            if (!isset($data['name']) || $data['name'] == null) {
                $data['name'] = 'Product ' . $data['code'];
            }


            // if there is no price set, then we will set the price to 0
            if (!isset($data['price']) || $data['price'] == null) {
                $data['price'] = 0;
            }
            // if there is no category_id set, then we will set the cost to 0
            if (!isset($data['category_id']) || $data['category_id'] == null) {
                $data['category_id'] = 0;
            }
            // if there is no type2 set, then we will set the cost to 0
            if (!isset($data['type2']) || $data['type2'] == null) {
                $data['type2'] = 0;
            }
            $added = $this->db->insert('products', $data);
            $product_id = $this->db->insert_id();
            $this->calculateProductDataCompleteness($product_id);
        }


        // // echo last query
        // echo $this->db->last_query();
        // die();

        if ($added) {
            // $product_id = $this->db->insert_id();

            if ($items) {
                foreach ($items as $item) {
                    $item['product_id'] = $product_id;
                    $this->db->insert('combo_items', $item);
                }
            }

            $warehouses = $this->site->getAllWarehouses();
            if (isset($data['type']) && $data['type'] != null) {

            if ($data['type'] != 'standard1') {
                foreach ($warehouses as $warehouse) {
                    $this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0]);
                }
            }
        }
        if (isset($data['tax_rate']) && $data['tax_rate'] != null) {
            $tax_rate = $this->site->getTaxRateByID($data['tax_rate']);
        }

        if ($warehouse_qty && !empty($warehouse_qty)) {
                foreach ($warehouse_qty as $wh_qty) {
                    if (isset($wh_qty['quantity']) && !empty($wh_qty['quantity'])) {
                        $this->db->insert('warehouses_products', ['product_id' => $product_id, 'warehouse_id' => $wh_qty['warehouse_id'], 'quantity' => $wh_qty['quantity'], 'rack' => $wh_qty['rack'], 'avg_cost' => $data['cost']]);

                        if (!$product_attributes) {
                            $tax_rate_id = $tax_rate ? $tax_rate->id : null;
                            $tax         = $tax_rate ? (($tax_rate->type == 1) ? $tax_rate->rate . '%' : $tax_rate->rate) : null;
                            $unit_cost   = $data['cost'];
                            if ($tax_rate) {
                                if ($tax_rate->type == 1 && $tax_rate->rate != 0) {
                                    if ($data['tax_method'] == '0') {
                                        $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / (100 + $tax_rate->rate);
                                        $net_item_cost = $data['cost'] - $pr_tax_val;
                                        $item_tax      = $pr_tax_val * $wh_qty['quantity'];
                                    } else {
                                        $net_item_cost = $data['cost'];
                                        $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / 100;
                                        $unit_cost     = $data['cost'] + $pr_tax_val;
                                        $item_tax      = $pr_tax_val * $wh_qty['quantity'];
                                    }
                                } else {
                                    $net_item_cost = $data['cost'];
                                    $item_tax      = $tax_rate->rate;
                                }
                            } else {
                                $net_item_cost = $data['cost'];
                                $item_tax      = 0;
                            }

                            $subtotal = (($net_item_cost * $wh_qty['quantity']) + $item_tax);

                            $item = [
                                'product_id'        => $product_id,
                                'product_code'      => $data['code'],
                                'product_name'      => $data['name'],
                                'net_unit_cost'     => $net_item_cost,
                                'unit_cost'         => $unit_cost,
                                'real_unit_cost'    => $unit_cost,
                                'quantity'          => $wh_qty['quantity'],
                                'quantity_balance'  => $wh_qty['quantity'],
                                'quantity_received' => $wh_qty['quantity'],
                                'item_tax'          => $item_tax,
                                'tax_rate_id'       => $tax_rate_id,
                                'tax'               => $tax,
                                'subtotal'          => $subtotal,
                                'warehouse_id'      => $wh_qty['warehouse_id'],
                                'date'              => date('Y-m-d'),
                                'status'            => 'received',
                            ];
                            $this->db->insert('purchase_items', $item);
                            $this->site->syncProductQty($product_id, $wh_qty['warehouse_id']);
                        }
                    }
                }
            }

            if ($product_attributes) {
                foreach ($product_attributes as $pr_attr) {
                    $pr_attr_details = $this->getPrductVariantByPIDandName($product_id, $pr_attr['name']);

                    $pr_attr['product_id'] = $product_id;
                    $variant_warehouse_id  = $pr_attr['warehouse_id'];
                    unset($pr_attr['warehouse_id']);
                    if ($pr_attr_details) {
                        $option_id = $pr_attr_details->id;
                    } else {
                        $this->db->insert('product_variants', $pr_attr);
                        $option_id = $this->db->insert_id();
                    }
                    if ($pr_attr['quantity'] != 0) {
                        $this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $variant_warehouse_id, 'quantity' => $pr_attr['quantity']]);

                        $tax_rate_id = $tax_rate ? $tax_rate->id : null;
                        $tax         = $tax_rate ? (($tax_rate->type == 1) ? $tax_rate->rate . '%' : $tax_rate->rate) : null;
                        $unit_cost   = $data['cost'];
                        if ($tax_rate) {
                            if ($tax_rate->type == 1 && $tax_rate->rate != 0) {
                                if ($data['tax_method'] == '0') {
                                    $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / (100 + $tax_rate->rate);
                                    $net_item_cost = $data['cost'] - $pr_tax_val;
                                    $item_tax      = $pr_tax_val * $pr_attr['quantity'];
                                } else {
                                    $net_item_cost = $data['cost'];
                                    $pr_tax_val    = ($data['cost'] * $tax_rate->rate) / 100;
                                    $unit_cost     = $data['cost'] + $pr_tax_val;
                                    $item_tax      = $pr_tax_val * $pr_attr['quantity'];
                                }
                            } else {
                                $net_item_cost = $data['cost'];
                                $item_tax      = $tax_rate->rate;
                            }
                        } else {
                            $net_item_cost = $data['cost'];
                            $item_tax      = 0;
                        }

                        $subtotal = (($net_item_cost * $pr_attr['quantity']) + $item_tax);
                        $item     = [
                            'product_id'        => $product_id,
                            'product_code'      => $data['code'],
                            'product_name'      => $data['name'],
                            'net_unit_cost'     => $net_item_cost,
                            'unit_cost'         => $unit_cost,
                            'quantity'          => $pr_attr['quantity'],
                            'option_id'         => $option_id,
                            'quantity_balance'  => $pr_attr['quantity'],
                            'quantity_received' => $pr_attr['quantity'],
                            'item_tax'          => $item_tax,
                            'tax_rate_id'       => $tax_rate_id,
                            'tax'               => $tax,
                            'subtotal'          => $subtotal,
                            'warehouse_id'      => $variant_warehouse_id,
                            'date'              => date('Y-m-d'),
                            'status'            => 'received',
                        ];
                        $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : null;
                        $this->db->insert('purchase_items', $item);
                    }

                    foreach ($warehouses as $warehouse) {
                        if (!$this->getWarehouseProductVariant($warehouse->id, $product_id, $option_id)) {
                            $this->db->insert('warehouses_products_variants', ['option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse->id, 'quantity' => 0]);
                        }
                    }

                    $this->site->syncVariantQty($option_id, $variant_warehouse_id);
                }
            }

            if ($photos) {
                foreach ($photos as $photo) {
                    $this->db->insert('product_photos', ['product_id' => $product_id, 'photo' => $photo]);
                }
            }

            $this->site->syncQuantity(null, null, null, $product_id);


            return  $product_id;
        }
        return false;
    }

    public function countCodes($code){
        $this->db->where('code', $code);
        $this->db->from('products');
        return $this->db->count_all_results();
    }
    // create new function to calculate the product data completeness based on 1- code 2- name 3- name_en 4- product_details 5- product_details_en 6- category_id 7- brand_id 8- unit 9- tax_rate 10- cost 11- price 12- quantity 13- tax_method 14- type 15- weight 16- currency_code and return the percentage and update the product data_completeness field
    public function calculateProductDataCompleteness($product_id)
    {


        $product = $this->getProductByID($product_id);


        $data_completeness = 0;
        if ($product->code != null) {
            $data_completeness += 1;
        }
        if ($product->name != null) {
            $data_completeness += 1;
        }
        if ($product->name_en != null) {
            $data_completeness += 1;
        }
        if ($product->product_details != null) {
            $data_completeness += 1;
        }
        if ($product->product_details_en != null) {
            $data_completeness += 1;
        }
        if ($product->category_id != null) {
            $data_completeness += 1;
        }
        if ($product->brand_id != null) {
            $data_completeness += 1;
        }
        if ($product->unit != null) {
            $data_completeness += 1;
        }
        if ($product->tax_rate != null) {
            $data_completeness += 1;
        }
        if ($product->cost != null) {
            $data_completeness += 1;
        }
        if ($product->price != null) {
            $data_completeness += 1;
        }
        if ($product->quantity != null) {
            $data_completeness += 1;
        }
        if ($product->tax_method != null) {
            $data_completeness += 1;
        }
        if ($product->type != null) {
            $data_completeness += 1;
        }
        if ($product->weight != null) {
            $data_completeness += 1;
        }
        if ($product->currency_code != null) {
            $data_completeness += 1;
        }
        $data_completeness = ($data_completeness / 16) * 100;
        $this->db->where('id', $product_id);
        $this->db->update('products', ['data_completness' => $data_completeness]);


        return $data_completeness;
    }

    public function reverseAdjustment_count($id)
    {
        if ($products = $this->getAdjustmentItems_count($id)) {
            foreach ($products as $adjustment) {
                $clause = ['product_id' => $adjustment->product_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id, 'status' => 'received'];
                $qty    = $adjustment->type == 'subtraction' ? (0 + $adjustment->quantity) : (0 - $adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncProductQty($adjustment->product_id, $adjustment->warehouse_id);
                if ($adjustment->option_id) {
                    $this->site->syncVariantQty($adjustment->option_id, $adjustment->warehouse_id, $adjustment->product_id);
                }
            }
        }
    }

    public function addAdjust($data, $products)
    {
        if ($this->db->insert('adjusts', $data)) {
            $adjustment_id = $this->db->insert_id();
            foreach ($products as $product) {
                $product['adjustment_id'] = $adjustment_id;
                $this->db->insert('adjust_items', $product);
                $this->syncAdjustment($product);
            }
            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
            }
            return true;
        }
        return false;
    }

    public function getAdjustItems($adjustment_id)
    {
        $this->db->select('adjust_items.*, products.code as product_code, products.name as product_name, products.image, products.details as details, product_variants.name as variant')
            ->join('products', 'products.id=adjust_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=adjust_items.option_id', 'left')
            ->group_by('adjust_items.id')
            ->order_by('id', 'asc');

        $this->db->where('adjustment_id', $adjustment_id);

        $q = $this->db->get('adjust_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAdjustByID($id)
    {
        $q = $this->db->get_where('adjusts', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateAdjust($id, $data, $products)
    {
        $this->reverseAdjust($id);
        if ($this->db->update('adjusts', $data, ['id' => $id]) && $this->db->delete('adjust_items', ['adjustment_id' => $id])) {
            foreach ($products as $product) {
                $product['adjustment_id'] = $id;
                $this->db->insert('adjust_items', $product);
                $this->syncAdjustment($product);
            }
            return true;
        }
        return false;
    }

    public function reverseAdjust($id)
    {
        if ($products = $this->getAdjustItems($id)) {
            foreach ($products as $adjustment) {
                $clause = ['product_id' => $adjustment->product_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id, 'status' => 'received'];
                $qty    = $adjustment->type == 'subtraction' ? (0 + $adjustment->quantity) : (0 - $adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncProductQty($adjustment->product_id, $adjustment->warehouse_id);
                if ($adjustment->option_id) {
                    $this->site->syncVariantQty($adjustment->option_id, $adjustment->warehouse_id, $adjustment->product_id);
                }
            }
        }
    }

    public function deleteAdjust($id)
    {
        $this->reverseAdjust($id);
        //$this->site->log('Quantity adjustment', ['model' => $this->getAdjustByID($id), 'items' => $this->getAdjustItems($id)]);
        if ($this->db->delete('adjusts', ['id' => $id]) && $this->db->delete('adjust_items', ['adjustment_id' => $id])) {
            return true;
        }
        return false;
    }

    public function getRVSuggestions($term, $warehouse_id, $limit = 5)
    {
        $term = str_replace([")","(","'"],"",$term);
        $term = strip_tags($term);

        $wp = "( SELECT product_id, warehouse_id, quantity as quantity,avg_cost as avg_cost from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('' . $this->db->dbprefix('products') . '.id, code, unit, ' . $this->db->dbprefix('products') . '.name as name, FWP.avg_cost as avg_cost')
        ->join($wp, 'FWP.product_id=products.id', 'left')
        ->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND type != 'combo'  AND type != 'service'  AND type != 'digital'  AND "
                . '(' . $this->db->dbprefix('products') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR item_no LIKE '%" . $term . "%' OR second_name LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('products') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
		$this->db->where('hide !=', 1);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function addRevaluation($data, $products)
    {
        if ($this->db->insert('revaluations', $data)) {
            $revaluation_id = $this->db->insert_id();
            foreach ($products as $product) {
                $product['revaluation_id'] = $revaluation_id;
                $this->db->insert('revaluation_items', $product);
                if ($this->Settings->update_cost) {
                    $this->db->update('products', ['cost' => $product['cost']], ['id' => $product['product_id']]);
                }
                $this->db->update('warehouses_products', ['avg_cost' => $product['cost']], ['product_id' => $product['product_id'], 'warehouse_id' => $product['warehouse_id']]);

            }
            if ($this->site->getReference('rv') == $data['reference_no']) {
                $this->site->updateReference('rv');
            }





            return true;
        }
        return false;
    }

    public function getRevaluations($revaluation_id)
    {
        $this->db->select('revaluation_items.*, products.code as product_code, products.name as product_name, products.image, products.details as details, units.name as unit_name')
            ->join('products', 'products.id=revaluation_items.product_id', 'left')
            ->join('units', 'units.id=revaluation_items.unit', 'left')
            ->group_by('revaluation_items.id')
            ->order_by('id', 'asc');

        $this->db->where('revaluation_id', $revaluation_id);

        $q = $this->db->get('revaluation_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getRevaluationByID($id)
    {
        $q = $this->db->get_where('revaluations', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateRevaluation($id, $data, $products,$tvalue = null)
    {
        $approved=$this->getRevaluationByID($id)->approved;
        if ($this->db->update('revaluations', $data, ['id' => $id]) && $this->db->delete('revaluation_items', ['revaluation_id' => $id])) {
            foreach ($products as $product) {
                $product['revaluation_id'] = $id;
                $this->db->insert('revaluation_items', $product);
                if ($this->Settings->update_cost) {
                    $this->db->update('products', ['cost' => $product['cost']], ['id' => $product['product_id']]);
                }
                $this->db->update('warehouses_products', ['avg_cost' => $product['cost']], ['product_id' => $product['product_id'], 'warehouse_id' => $product['warehouse_id']]);

            }


            return true;
        }
        return false;
    }



    public function getBaseUnitsNames($term, $limit = 5)
    {
        $this->db->select('' . $this->db->dbprefix('units') . '.id, code, ' . $this->db->dbprefix('units') . '.name as name')
            ->where("base_unit is null  AND "
                . '(' . $this->db->dbprefix('units') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('units') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")
            ->group_by('units.id')->limit($limit);
        $q = $this->db->get('units');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function setProductPriceForPriceGroup($product_id, $group_id, $price, $unit, $product_code)
    {
        if ($this->getGroupPrice($group_id, $product_id, $unit)) {
            if ($this->db->update('product_prices', ['price' => $price,'product_code' => $product_code], ['price_group_id' => $group_id, 'product_id' => $product_id, 'product_unit_id' => $unit])) {
                return true;
            }
        } else {
            if ($this->db->insert('product_prices', ['product_code' => $product_code, 'price' => $price, 'product_unit_id' => $unit, 'price_group_id' => $group_id, 'product_id' => $product_id])) {
                return true;
            }
        }
        return false;
    }

    public function getGroupPrice($group_id, $product_id , $product_unit_id= null)
    {
        if($product_unit_id)
			$q = $this->db->get_where('product_prices', ['price_group_id' => $group_id, 'product_id' => $product_id, 'product_unit_id' => $product_unit_id], 1);
        else
			$q = $this->db->get_where('product_prices', ['price_group_id' => $group_id, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductNamesV5($term, $warehouse_id, $pos = false, $limit = 5)
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
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getTaxRatesNT()
    {
        $this->db->select('id');
		$this->db->where('rate', 0);
        $this->db->where('type', 2);
        $this->db->where('tax_active', 1);
        $q = $this->db->get('tax_rates',1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addStockCountV2($data)
    {
        if ($this->db->insert('stock_counts', $data)) {
            return $this->db->insert_id();;
        }
        return false;
    }

    public function finalizeStockCountV2($products)
    {
            foreach ($products as $product) {
                $pr = ['stock_count_id' => $product->stock_count_id, 'product_id' => $product->product_id, 'cost' => $product->cost, 'unit' => $product->unit, 'product_variant_id' => $product->product_variant_id, 'expected' => $product->expected, 'quantity' => $product->quantity, 'product_name' => $product->product_name, 'product_code' => $product->product_code];

                $this->db->insert('stock_count_items', $pr);
            }
            return true;

    }

    public function updatePrintBarcodeSettings($data)
    {
        $this->db->where('id', '1');
        if ($this->db->update('print_barcode_settings', $data)) {
            return true;
        }
        return false;
    }

    public function getPrintBarcodeSettings()
    {
        $q = $this->db->get('print_barcode_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addModifier($data)
    {
        if ($this->db->insert('modifiers', $data)) {
            return true;
        }
        return false;
    }

    public function getModifierByID($id)
    {
        $q = $this->db->get_where('modifiers', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateModifier($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('modifiers', $data)) {
            return true;
        }
        return false;
    }

    public function deleteModifier($id)
    {
        if ($this->db->delete('modifiers', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getAllProductsModifierCount($modifier_id)
    {
        $this->db->select('*');
		$this->db->where('modifier_id ', $modifier_id);
		$q = $this->db->get('category_modifiers');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllCategoriesWithOutmodifiers($category_id)
    {
        $myQuery ="select *  from " . $this->db->dbprefix('modifiers') . "  where  id not in (select modifier_id from " . $this->db->dbprefix('category_modifiers') . "  where category_id = '".$category_id."' )";
        $q = $this->db->query($myQuery);
        //$q = $this->db->get_where('companies', ['group_name' => 'customer']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getCategoryModifiers($category_id)
    {
        $this->db->select('category_modifiers.modifier_id as modifier_id, modifiers.name as name')
            ->join('modifiers', 'modifiers.id=category_modifiers.modifier_id', 'left');

            $this->db->where('category_id', $category_id);
        $q = $this->db->get('category_modifiers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function deleteCategoryModifiers($category_id)
    {
        if ($this->db->delete('category_modifiers', ['category_id' => $category_id])) {
            //$this->db->delete('product_modifiers', ['category_id' => $category_id]);
            return true;
        }
        return false;
    }

    public function addCategoryModifiers($category_id,$modifiers = [],$product_modifiers_data = [])
    {

        $catMod=$this->getAllCategoriesWithOutmodifiers($category_id);
        //$proMod=$this->getAllCategoriesWithOutmodifiers($category_id);

        if($this->deleteCategoryModifiers($category_id)){
            if ($this->db->insert_batch('category_modifiers', $modifiers)  ) {
                if ($this->db->insert_batch('product_modifiers', $product_modifiers_data)  ) {
                }
                return true;
            }else{

                foreach ($catMod as $modifier) {
                    if($this->db->insert('category_modifiers', $modifier)){
                        $flage=1;
                    }
                }

                /*foreach ($proMod as $modifier) {
                    if($this->db->insert('product_modifiers', $modifier)){
                        $flage=1;
                    }
                }*/
            }
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

    // =========================================================================
    // Modern SaaS Products & Inventory Dashboard Methods
    // =========================================================================

    public function getProductsDashboardStats($warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->select("
                COUNT(DISTINCT p.id) as total_products,
                SUM(CASE WHEN p.hide = 0 OR p.hide IS NULL THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN p.hide = 1 THEN 1 ELSE 0 END) as inactive_products,
                SUM(CASE WHEN p.track_quantity = 1 AND wp.quantity > 0 AND wp.quantity <= p.alert_quantity THEN 1 ELSE 0 END) as low_stock_products,
                SUM(CASE WHEN p.track_quantity = 1 AND wp.quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock_products,
                COALESCE(SUM(wp.quantity), 0) as total_stock_quantity,
                COALESCE(SUM(wp.quantity * p.cost), 0) as total_cost_value,
                COALESCE(SUM(wp.quantity * p.price), 0) as total_sale_value
            ", false);
            $this->db->from('products p');
            $this->db->join('warehouses_products wp', 'wp.product_id = p.id AND wp.warehouse_id = ' . (int)$warehouse_id, 'inner');
            $q = $this->db->get();
        } else {
            $this->db->select("
                COUNT(*) as total_products,
                SUM(CASE WHEN hide = 0 OR hide IS NULL THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN hide = 1 THEN 1 ELSE 0 END) as inactive_products,
                SUM(CASE WHEN track_quantity = 1 AND quantity > 0 AND quantity <= alert_quantity THEN 1 ELSE 0 END) as low_stock_products,
                SUM(CASE WHEN track_quantity = 1 AND quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock_products,
                COALESCE(SUM(quantity), 0) as total_stock_quantity,
                COALESCE(SUM(quantity * cost), 0) as total_cost_value,
                COALESCE(SUM(quantity * price), 0) as total_sale_value
            ", false);
            $this->db->from('products');
            $q = $this->db->get();
        }

        $row = $q->row_array();

        $total_cost = (float)($row['total_cost_value'] ?? 0);
        $total_sale = (float)($row['total_sale_value'] ?? 0);
        $potential_profit = $total_sale - $total_cost;
        $profit_margin = ($total_sale > 0) ? round(($potential_profit / $total_sale) * 100, 2) : 0;

        $categories_count = $this->db->count_all_results('categories');
        $brands_count = $this->db->count_all_results('brands');
        $warehouses_count = $this->db->count_all_results('warehouses');

        return [
            'total_products'          => (int)($row['total_products'] ?? 0),
            'active_products'         => (int)($row['active_products'] ?? 0),
            'inactive_products'       => (int)($row['inactive_products'] ?? 0),
            'low_stock_products'      => (int)($row['low_stock_products'] ?? 0),
            'out_of_stock_products'   => (int)($row['out_of_stock_products'] ?? 0),
            'total_stock_quantity'    => (float)($row['total_stock_quantity'] ?? 0),
            'total_cost_value'        => $total_cost,
            'total_sale_value'        => $total_sale,
            'potential_profit'        => $potential_profit,
            'potential_profit_margin' => $profit_margin,
            'categories_count'        => (int)$categories_count,
            'brands_count'            => (int)$brands_count,
            'warehouses_count'        => (int)$warehouses_count,
        ];
    }

    public function getTopSellingProducts($limit = 5, $warehouse_id = null)
    {
        $this->db->select("
            si.product_id,
            p.code,
            p.name,
            p.image,
            p.price,
            p.cost,
            u.name as unit_name,
            COALESCE(SUM(si.quantity), 0) as sold_qty,
            COALESCE(SUM(si.subtotal), 0) as sales_amount
        ", false);
        $this->db->from('sale_items si');
        $this->db->join('sales s', 's.id = si.sale_id', 'inner');
        $this->db->join('products p', 'p.id = si.product_id', 'inner');
        $this->db->join('units u', 'u.id = p.unit', 'left');
        if ($warehouse_id) {
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
        }
        $this->db->group_by('si.product_id');
        $this->db->order_by('sold_qty', 'DESC');
        $this->db->limit($limit);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getTopStockProducts($limit = 5, $warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->select("
                p.id,
                p.code,
                p.name,
                p.image,
                wp.quantity as quantity,
                p.cost,
                p.price,
                u.name as unit_name,
                (wp.quantity * p.cost) as stock_value
            ", false);
            $this->db->from('products p');
            $this->db->join('warehouses_products wp', 'wp.product_id = p.id', 'inner');
            $this->db->join('units u', 'u.id = p.unit', 'left');
            $this->db->where('wp.warehouse_id', (int)$warehouse_id);
            $this->db->where('wp.quantity >', 0);
            $this->db->order_by('stock_value', 'DESC');
            $this->db->limit($limit);
        } else {
            $this->db->select("
                p.id,
                p.code,
                p.name,
                p.image,
                p.quantity,
                p.cost,
                p.price,
                u.name as unit_name,
                (p.quantity * p.cost) as stock_value
            ", false);
            $this->db->from('products p');
            $this->db->join('units u', 'u.id = p.unit', 'left');
            $this->db->where('p.quantity >', 0);
            $this->db->order_by('stock_value', 'DESC');
            $this->db->limit($limit);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getCategoryStockDistribution($limit = 6)
    {
        $this->db->select("
            c.id,
            c.name,
            COUNT(p.id) as product_count,
            COALESCE(SUM(p.quantity), 0) as total_qty,
            COALESCE(SUM(p.quantity * p.cost), 0) as total_value
        ", false);
        $this->db->from('categories c');
        $this->db->join('products p', 'p.category_id = c.id', 'left');
        $this->db->group_by('c.id');
        $this->db->order_by('total_value', 'DESC');
        $this->db->limit($limit);
        $q = $this->db->get();
        $categories = ($q->num_rows() > 0) ? $q->result() : [];

        // Compute total value for percentages
        $grand_value = 0;
        foreach ($categories as $cat) {
            $grand_value += max(0, (float)$cat->total_value);
        }

        foreach ($categories as $cat) {
            $cat->pct = ($grand_value > 0) ? round((max(0, (float)$cat->total_value) / $grand_value) * 100, 1) : 0;
        }

        return $categories;
    }

    public function getWarehouseStockDistribution()
    {
        $this->db->select("
            w.id,
            w.name,
            w.code,
            COUNT(DISTINCT wp.product_id) as product_count,
            COALESCE(SUM(wp.quantity), 0) as total_qty,
            COALESCE(SUM(wp.quantity * p.cost), 0) as total_value
        ", false);
        $this->db->from('warehouses w');
        $this->db->join('warehouses_products wp', 'wp.warehouse_id = w.id', 'left');
        $this->db->join('products p', 'p.id = wp.product_id', 'left');
        $this->db->group_by('w.id');
        $this->db->order_by('total_qty', 'DESC');
        $q = $this->db->get();
        $warehouses = ($q->num_rows() > 0) ? $q->result() : [];

        $grand_qty = 0;
        foreach ($warehouses as $wh) {
            $grand_qty += max(0, (float)$wh->total_qty);
        }

        foreach ($warehouses as $wh) {
            $wh->pct = ($grand_qty > 0) ? round((max(0, (float)$wh->total_qty) / $grand_qty) * 100, 1) : 0;
        }

        return $warehouses;
    }

    public function getProductStockAlerts($limit = 6, $warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->select("
                p.id,
                p.code,
                p.name,
                wp.quantity as current_stock,
                p.alert_quantity,
                u.name as unit_name,
                CASE WHEN wp.quantity <= 0 THEN 'critical' ELSE 'warning' END as alert_level
            ", false);
            $this->db->from('products p');
            $this->db->join('warehouses_products wp', 'wp.product_id = p.id AND wp.warehouse_id = ' . (int)$warehouse_id, 'inner');
            $this->db->join('units u', 'u.id = p.unit', 'left');
            $this->db->where('p.track_quantity', 1);
            $this->db->group_start();
            $this->db->where('wp.quantity <=', 0);
            $this->db->or_where('wp.quantity <= p.alert_quantity', null, false);
            $this->db->group_end();
            $this->db->order_by('wp.quantity', 'ASC');
            $this->db->limit($limit);
        } else {
            $this->db->select("
                p.id,
                p.code,
                p.name,
                p.quantity as current_stock,
                p.alert_quantity,
                u.name as unit_name,
                CASE WHEN p.quantity <= 0 THEN 'critical' ELSE 'warning' END as alert_level
            ", false);
            $this->db->from('products p');
            $this->db->join('units u', 'u.id = p.unit', 'left');
            $this->db->where('p.track_quantity', 1);
            $this->db->group_start();
            $this->db->where('p.quantity <=', 0);
            $this->db->or_where('p.quantity <= p.alert_quantity', null, false);
            $this->db->group_end();
            $this->db->order_by('p.quantity', 'ASC');
            $this->db->limit($limit);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getRecentProducts($limit = 8, $warehouse_id = null)
    {
        $this->db->select("
            p.id,
            p.code,
            p.name,
            p.type,
            p.price,
            p.cost,
            p.quantity,
            u.name as unit_name,
            c.name as category_name
        ", false);
        $this->db->from('products p');
        $this->db->join('categories c', 'c.id = p.category_id', 'left');
        $this->db->join('units u', 'u.id = p.unit', 'left');
        $this->db->order_by('p.id', 'DESC');
        $this->db->limit($limit);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getInventoryMonthlyTrends($limit = 12)
    {
        $trends = [];

        // 1. Monthly Sales Volume
        $this->db->select("
            DATE_FORMAT(s.date, '%Y-%m') as month,
            COALESCE(SUM(si.quantity), 0) as sales_qty
        ", false);
        $this->db->from('sale_items si');
        $this->db->join('sales s', 's.id = si.sale_id', 'inner');
        $this->db->where("s.date >= DATE_SUB(CURDATE(), INTERVAL " . (int)$limit . " MONTH)", null, false);
        $this->db->group_by("DATE_FORMAT(s.date, '%Y-%m')");
        $this->db->order_by('month', 'ASC');
        $q_sales = $this->db->get();
        if ($q_sales->num_rows() > 0) {
            foreach ($q_sales->result() as $row) {
                $trends[$row->month] = [
                    'month'        => $row->month,
                    'sales_qty'    => (float)$row->sales_qty,
                    'purchase_qty' => 0,
                ];
            }
        }

        // 2. Monthly Purchases Volume
        $this->db->select("
            DATE_FORMAT(pu.date, '%Y-%m') as month,
            COALESCE(SUM(pi.quantity), 0) as purchase_qty
        ", false);
        $this->db->from('purchase_items pi');
        $this->db->join('purchases pu', 'pu.id = pi.purchase_id', 'inner');
        $this->db->where("pu.date >= DATE_SUB(CURDATE(), INTERVAL " . (int)$limit . " MONTH)", null, false);
        $this->db->group_by("DATE_FORMAT(pu.date, '%Y-%m')");
        $this->db->order_by('month', 'ASC');
        $q_purchases = $this->db->get();
        if ($q_purchases->num_rows() > 0) {
            foreach ($q_purchases->result() as $row) {
                if (!isset($trends[$row->month])) {
                    $trends[$row->month] = [
                        'month'        => $row->month,
                        'sales_qty'    => 0,
                        'purchase_qty' => 0,
                    ];
                }
                $trends[$row->month]['purchase_qty'] = (float)$row->purchase_qty;
            }
        }

        ksort($trends);
        return array_values($trends);
    }

}

