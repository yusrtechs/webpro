<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Manufacturing_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

	public function addBom($data)
    {
        if ($this->db->insert('man_bom', $data)) {
            return true;
        }
        return false;
    }

	public function addManufacturing($data)
    {
        if ($this->db->insert('man_manufacturing', $data)) {
            return true;
        }
        return false;
    }

	public function updateBom($id,$data)
    {
        if ($this->db->update('man_bom', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

	public function updateManufacturing($id,$data)
    {
        if ($this->db->update('man_manufacturing', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

	public function addBomDetails($data)
    {
        if ($this->db->insert('man_bom_details', $data)) {
            return true;
        }
        return false;
    }

	public function addManufacturingDetail($data = [],$dataTR = [],$productTR = [],$man_id,$confirm_qty,$products_labors = null,$products_expenses = null)
    {
        if ($this->db->insert_batch('man_manufacturing_details', $data)) {

			$this->addTransfer($dataTR,$productTR);
            if($products_labors){
                $this->db->insert_batch('man_manufacturing_labors', $products_labors);
            }

            if($products_expenses){
                $this->db->insert_batch('man_manufacturing_expenses', $products_expenses);
            }
            $date_produce=date('Y-m-d H:i:s');
			$this->db->update('man_manufacturing', ['status' => 'produce','date_produce' => $date_produce,'confirm_qty' => $confirm_qty], ['id' => $man_id]);

            return true;
        }
        return false;
    }

	public function deleteManufacturingDetails($man_id,$req_status)
    {
        if ($this->db->delete('man_manufacturing_details', ['man_id' => $man_id,'req_status' => $req_status])) {
            return true;
        }
        return false;
    }

	public function getDestructionByManId($man_id)
    {
        $q = $this->db->get_where('destruction', ['man_id' => $man_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getTransferByManId($man_id)
    {
        $this->db->order_by('id','desc');
		$q = $this->db->get_where('transfers', ['man_id' => $man_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getCountTransferByManId($man_id)
    {
        $this->db->select('count(*) as count');
		$q = $this->db->get_where('transfers', ['man_id' => $man_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }



	public function addBomRequestDetail($bom_id, $data = [])
	{
		$this->deleteBomDetailsByBomId($bom_id);
		if ($this->db->insert_batch('man_bom_details', $data)) {
	        return true;
        }
	return false;
    }

	public function addManufacturingRequestDetail($data = [],$dataTR = [],$productTR = [],$data_dest = [],$dataDT = [],$productDT = [],$man_id,$confirm_qty)
    {
        if($data && $data_dest){
			$this->deleteManufacturingDetails($man_id,'request');
			$this->deleteManufacturingDetails($man_id,'destruction');
		if ($this->db->insert_batch('man_manufacturing_details', $data) && $this->db->insert_batch('man_manufacturing_details', $data_dest)) {

			if($this->getTransferByManId($man_id) && $this->getCountTransferByManId($man_id)->count >1){
			$this->updateTransfer($this->getTransferByManId($man_id)->id,$dataDT, $productDT);

			}else{
			$this->addTransfer($dataTR,$productTR);
			}

			if($this->getDestructionByManId($man_id)){
			$this->updateDestruction_inventory($this->getDestructionByManId($man_id)->id,$dataDT, $productDT);

			}else{
			$this->destruction_inventory($dataDT, $productDT);
			}
			$date_produce=date('Y-m-d H:i:s');
			$this->db->update('man_manufacturing', ['confirm_qty' => $confirm_qty], ['id' => $man_id]);

            return true;
        }
		}else if($data){
			$this->deleteManufacturingDetails($man_id,'request');
		if ($this->db->insert_batch('man_manufacturing_details', $data)) {

			if($this->getTransferByManId($man_id) && $this->getCountTransferByManId($man_id)->count >1){
			$this->updateTransfer($this->getTransferByManId($man_id)->id,$dataDT, $productDT);

			}else{
			$this->addTransfer($dataTR,$productTR);
			}
			$date_produce=date('Y-m-d H:i:s');
			$this->db->update('man_manufacturing', ['confirm_qty' => $confirm_qty], ['id' => $man_id]);

            return true;
        }
		}else if($data_dest){
			$this->deleteManufacturingDetails($man_id,'destruction');
		if ($this->db->insert_batch('man_manufacturing_details', $data_dest)) {

			if($this->getDestructionByManId($man_id)){
			$this->updateDestruction_inventory($this->getDestructionByManId($man_id)->id,$dataDT, $productDT);

			}else{
			$this->destruction_inventory($dataDT, $productDT);
			}
			$date_produce=date('Y-m-d H:i:s');
			$this->db->update('man_manufacturing', ['confirm_qty' => $confirm_qty], ['id' => $man_id]);

            return true;
        }
		}else{
			$this->db->update('man_manufacturing', ['confirm_qty' => $confirm_qty], ['id' => $man_id]);

		return true;
		}
        return false;
    }

	public function addDone($man_id,$products = [],$products_req = [],$products_dist = [],$wh_manufacturing,$wh_complete,$wh_destruction,$totalQty,$cqty,$product_id,$confirm_qty,$dataTR,$productsTR,$dataTRMain,$productsTRMain,$total_labors,$total_expenses)
    {

			//$this->addTransfer($dataTR,$productTR);
			$date_done=date('Y-m-d H:i:s');
			if($this->db->update('man_manufacturing', ['status' => 'done','date_done' => $date_done], ['id' => $man_id])){
			$cost=0;
			if($products){
			//$Tqty=0;
			foreach ($products as $product) {
				//$clause = ['product_id' => $product->id, 'option_id' => '', 'warehouse_id' => $wh_manufacturing, 'status' => 'received'];
				//$this->site->setPurchaseItem($clause, (0 - $product->qty));
				//$this->site->syncProductQty($product->id, $wh_manufacturing);
            	$cost=$cost+($product->actual_qty*$product->avg_cost);
            	}
				}
            if($products_req){
			foreach ($products_req as $product) {
				//$clause = ['product_id' => $product->id, 'option_id' => '', 'warehouse_id' => $wh_manufacturing, 'status' => 'received'];
				//$this->site->setPurchaseItem($clause, (0 - $product->qty));
				//$this->site->syncProductQty($product->id, $wh_manufacturing);
            	$cost=$cost+($product->qty*$product->avg_cost);
            	}
			}

            if($products || $products_req){
                $this->addTransfer($dataTRMain,$productsTRMain);
                //if($dataTRMain['from_warehouse_id']==$dataTRMain['to_warehouse_id']){

                    $adjust = [
						'date'         => $dataTRMain['date'],
						'reference_no' => $this->site->getReference('qa'),
						'warehouse_id' => $dataTRMain['to_warehouse_id'],
						'note'         => lang('Stock_adjustments_from_manufacture').' ('.$dataTRMain['man_id'].')',
						'created_by'   => $this->session->userdata('user_id'),
						'count_id'     => null,
					];

					foreach($productsTRMain as $product){
						$adjustItems[] = [
							'product_id'   => $product['product_id'],
							'type'         => 'subtraction',
							'quantity'     => $product['quantity'],
							'warehouse_id' => $dataTRMain['to_warehouse_id'],
							'option_id'    => null,
							'serial_no'    => null,
						];
					}

                    $this->addAdjust($adjust, $adjustItems);

                //}
            }

			if($products_dist){
			foreach ($products_dist as $product) {
				//$clause = ['product_id' => $product->id, 'option_id' => '', 'warehouse_id' => $wh_manufacturing, 'status' => 'received'];
				//$this->site->setPurchaseItem($clause, (0 - $product->qty));
				//$this->site->syncProductQty($product->id, $wh_manufacturing);
            	$cost=$cost-($product->qty*$product->avg_cost);
            	}
				$this->addTransfer($dataTR,$productsTR);
			}

            $cost=$cost+($total_labors)+($total_expenses);

				$cost=($cost/$confirm_qty);
				$clause1 = ['product_id' => $product_id, 'option_id' => NULL, 'warehouse_id' => $wh_complete, 'status' => 'received'];
				$this->site->setPurchaseItem($clause1,  $confirm_qty);
				$this->updateAVCO(['product_id' => $product_id, 'warehouse_id' => $wh_complete, 'quantity' => $confirm_qty, 'cost' => $cost,'shipping'=>0]);

				$this->site->syncProductQty($product_id, $wh_complete);



            return true;
        }
        return false;
    }
	public function updateBomDetails($data)
    {
        if ($this->db->update('man_bom_details', ['product_quantity' => $data['product_quantity']], ['bom_id' => $data['bom_id'],'product_id' => $data['product_id']])) {
            return true;
        }
        return false;
    }


	public function deleteBomDetails($data)
    {
        if ($this->db->delete('man_bom_details', ['bom_id' => $data['bom_id'],'product_id' => $data['product_id']])) {
            return true;
        }
        return false;
    }

	public function deleteBomDetailsByBomId($bom_id)
    {
        if ($this->db->delete('man_bom_details', ['bom_id' => $bom_id])) {
            return true;
        }
        return false;
    }

	public function getBomByID($id)
    {
        $q = $this->db->get_where('man_bom', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getManufacturingByID($id)
    {
        $q = $this->db->get_where('man_manufacturing', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getAllBom()
    {
        $q = $this->db->get('man_bom');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

	public function getAllManufacturingDataTable()
    {
        $this->db->select($this->db->dbprefix('man_manufacturing') . '.id as id, DATE_FORMAT(' . $this->db->dbprefix('man_manufacturing') . '.created_date, "%Y-%m-%d %T") as created_date, ' . $this->db->dbprefix('man_bom') . '.name as name, ' . $this->db->dbprefix('man_manufacturing') . '.quantity as quantity, ' . $this->db->dbprefix('man_manufacturing') . '.deadline_start as deadline_start, CONCAT(' . $this->db->dbprefix('users') . '.first_name," ",' . $this->db->dbprefix('users') . '.last_name) as created_by, ' . $this->db->dbprefix('man_manufacturing') . '.description as description, ' . $this->db->dbprefix('man_manufacturing') . '.status as status')
		->join('man_bom', 'man_bom.id=man_manufacturing.bom_id', 'left')
		->join('users', 'users.id=man_manufacturing.created_by', 'left');

		$q = $this->db->get('man_manufacturing');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

	public function getAllConfirmedManufacturingDataTable()
    {
        $this->db->select($this->db->dbprefix('man_manufacturing') . '.id as id, DATE_FORMAT(' . $this->db->dbprefix('man_manufacturing') . '.created_date, "%Y-%m-%d %T") as created_date, ' . $this->db->dbprefix('man_bom') . '.name as name, ' . $this->db->dbprefix('man_manufacturing') . '.quantity as quantity, ' . $this->db->dbprefix('man_manufacturing') . '.deadline_start as deadline_start, CONCAT(' . $this->db->dbprefix('users') . '.first_name," ",' . $this->db->dbprefix('users') . '.last_name) as created_by, ' . $this->db->dbprefix('man_manufacturing') . '.description as description, ' . $this->db->dbprefix('man_manufacturing') . '.status as status')
		->join('man_bom', 'man_bom.id=man_manufacturing.bom_id', 'left')
		->join('users', 'users.id=man_manufacturing.created_by', 'left');
        $this->db->where('man_manufacturing.status','confirmed');
		$q = $this->db->get('man_manufacturing');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

	public function getBomDetails($bom_id)
    {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('man_bom_details') . '.product_quantity as qty, ' . $this->db->dbprefix('man_bom_details') . '.product_unit as unit_id, ' . $this->db->dbprefix('units') . '.name as unit_name, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('man_bom_details') . '.product_avg_cost as price, ' . $this->db->dbprefix('man_bom_details') . '.product_avg_cost as avg_cost')
		->join('products', 'products.code=man_bom_details.product_code', 'left')->group_by('man_bom_details.id')
		->join('units', 'units.id=man_bom_details.product_unit', 'left');
		$q = $this->db->get_where('man_bom_details', ['bom_id' => $bom_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

	public function getBomDetailsProductId($bom_id,$product_id)
    {

		 $q = $this->db->get_where('man_bom_details', ['bom_id' => $bom_id,'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
	}

	public function getManufacturingDetailsProductId($man_id,$bom_id,$product_id)
    {

		 $q = $this->db->get_where('man_manufacturing_details', ['man_id' => $man_id,'bom_id' => $bom_id,'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
	}

	public function getManufacturingDetails($man_id,$req_status = NULL)
    {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('man_manufacturing_details') . '.product_quantity as qty, ' . $this->db->dbprefix('man_manufacturing_details') . '.actual_qty as actual_qty, ' . $this->db->dbprefix('man_manufacturing_details') . '.product_unit as unit_id, ' . $this->db->dbprefix('units') . '.name as unit_name, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('man_manufacturing_details') . '.product_avg_cost as price, ' . $this->db->dbprefix('man_manufacturing_details') . '.product_avg_cost as avg_cost')
		->join('products', 'products.code=man_manufacturing_details.product_code', 'left')->group_by('man_manufacturing_details.id')
		->join('units', 'units.id=man_manufacturing_details.product_unit', 'left');

		if($req_status){
			$this->db->where('man_manufacturing_details.req_status',$req_status);
		}

		$q = $this->db->get_where('man_manufacturing_details', ['man_id' => $man_id]);
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

	public function getProductNames($term, $warehouse_id, $man_id = false, $limit = 5)
    {
		$term = str_replace([")","(","'"],"",$term);
        $term = strip_tags($term);

        $wp = "( SELECT product_id, warehouse_id, quantity as quantity,avg_cost as avg_cost from {$this->db->dbprefix('warehouses_products')} ) FWP";

        $this->db->select('products.*, FWP.quantity as quantity,FWP.avg_cost as avg_cost,FWP.warehouse_id as warehouse_id, categories.id as category_id, categories.name as category_name', false)
            ->join($wp, 'FWP.product_id=products.id', 'left')
            // ->join('warehouses_products FWP', 'FWP.product_id=products.id', 'left')
            ->join('categories', 'categories.id=products.category_id', 'left')
            ->join('man_manufacturing_details', 'man_manufacturing_details.product_id=products.id', 'left')
            ->group_by('products.id');
        //if ($this->Settings->overselling) {
            $this->db->where("({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        /*} else {
            $this->db->where("(products.track_quantity = 0 OR FWP.quantity > 0) AND FWP.warehouse_id = '" . $warehouse_id . "' AND "
                . "({$this->db->dbprefix('products')}.name LIKE '%" . $term . "%' OR {$this->db->dbprefix('products')}.code LIKE '%" . $term . "%' OR  concat({$this->db->dbprefix('products')}.name, ' (', {$this->db->dbprefix('products')}.code, ')') LIKE '%" . $term . "%')");
        */
        // $this->db->order_by('products.name ASC');
        $this->db->where('FWP.warehouse_id=', $warehouse_id);
        $this->db->where('man_manufacturing_details.man_id=', $man_id);
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


	public function deleteBom($id)
    {
        if ($this->db->delete('man_bom', ['id' => $id]) && $this->db->delete('man_bom_details', ['bom_id' => $id])) {
            return true;
        }
        return false;
    }
	public function deleteManufacturing($id)
    {
        if ($this->db->delete('man_manufacturing', ['id' => $id])) {
            return true;
        }
        return false;
    }

	public function getWarehousesWithPQ($product_id,$warehouse_id)
    {
        /*$this->db->select('' . $this->db->dbprefix('warehouses') . '.id as id1,' . $this->db->dbprefix('warehouses') . '.name as name,' . $this->db->dbprefix('warehouses') . '.code as code, ' . $this->db->dbprefix('warehouses_products') . '.quantity as quantity, ' . $this->db->dbprefix('warehouses_products') . '.rack as rack, '. $this->db->dbprefix('warehouses_products') . '.avg_cost as avg_cost,'.$this->db->dbprefix('warehouses_products') . '.id as id2')
        //$this->db->select('warehouses.id,warehouses.name,warehouses.code, warehouses_products.quantity, warehouses_products.rack, warehouses_products.avg_cost')
        //$this->db->select('warehouses.*, warehouses_products.id')
          //$this->db->select('*')
            ->join('warehouses_products', 'warehouses_products.warehouse_id=warehouses.id', 'left')
            ->where('warehouses_products.product_id', $product_id)
            ->group_by('warehouses.id');
        $q = $this->db->get('warehouses');*/

		$myQuery = "SELECT w.*,wp.quantity,wp.rack,wp.avg_cost from  " . $this->db->dbprefix('warehouses') . " w," . $this->db->dbprefix('warehouses_products') . " wp where wp.warehouse_id='".$warehouse_id."' and wp.warehouse_id=w.id and wp.product_id='".$product_id."'";
        $q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function addTransfer($data = [], $items = [])
    {
        $this->db->trans_off();
		$this->db->trans_start();
        $status = $data['status'];
		    //return $data['from_warehouse_id'];

        if ($this->db->insert('transfers', $data)) {

            $transfer_id = $this->db->insert_id();

            if ($this->site->getReference('to') == $data['transfer_no']) {
                $this->site->updateReference('to');
            }
            foreach ($items as $item) {
                $item['transfer_id'] = $transfer_id;
                //$item['option_id']   = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : null;
                $item['option_id']   = null;
                if ($status == 'completed') {
					unset($item['received_quantity']);
                    $item['date']         = date('Y-m-d');
                    $item['warehouse_id'] = $data['to_warehouse_id'];
                    $item['status']       = 'received';
                    $this->db->insert('purchase_items', $item);
                } else {
                    $this->db->insert('transfer_items', $item);
                }

                if ($status == 'completed') {
				$shipping_amount = $data['shipping'] ? $data['shipping'] :0;
				$original_shipping=($item['net_unit_cost'] * $item['quantity']);
				$shipping =$this->site->distributeAmountEqually($original_shipping,$shipping_amount,$data['total']);

                    $this->updateAVCO(['product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['quantity'], 'cost' => $item['net_unit_cost'],'shipping'=>$shipping]);
                }
				if ($status == 'sent' || $status == 'completed') {
                    $this->syncTransderdItem($item['product_id'], $data['from_warehouse_id'], $item['quantity'], $item['option_id']);
                }

            }



            if ($this->Settings->accounting){
				//if ($status == 'sent' || $status == 'completed') {
				if ($status == 'completed') {
						$toentryWarehouseAcc=$this->site->getAccountByRefId($data['to_warehouse_id'],'warehouses')->id;
						if($toentryWarehouseAcc=="")
						$toentryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

						$fromentryWarehouseAcc=$this->site->getAccountByRefId($data['from_warehouse_id'],'warehouses')->id;
						if($fromentryWarehouseAcc=="")
						$fromentryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

				$this->site->createEntry($toentryWarehouseAcc,$fromentryWarehouseAcc,$data['grand_total'],$data['transfer_no'],'Transfer');
				}
			}

			/*if ($this->Settings->accounting){
				//if ($status == 'sent' || $status == 'completed') {
				if ($status == 'completed') {
						$toentryWarehouseAcc=$this->site->getAccountByRefId($data['to_warehouse_id'],'warehouses')->id;
						if($toentryWarehouseAcc=="")
						$toentryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

						$fromentryWarehouseAcc=$this->site->getAccountByRefId($data['from_warehouse_id'],'warehouses')->id;
						if($fromentryWarehouseAcc=="")
						$fromentryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;

				$this->site->createEntry($toentryWarehouseAcc,$fromentryWarehouseAcc,$data['grand_total'],$data['transfer_no'],'Transfer');
				}
			}*/


        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Add:Transfers_model.php)');
        } else {
            return true;
        }
        return false;
    }
    public function updateAVCO($data)
    {
        if ($wp_details = $this->getWarehouseProductQuantity($data['warehouse_id'], $data['product_id'])) {
			$data['shipping'] = ($data['shipping'] ? $data['shipping'] :0) + ($data['customs_fees'] ? $data['customs_fees'] :0) + ($data['Other_fees'] ? $data['Other_fees'] :0);
            $total_cost     = (($wp_details->quantity * $wp_details->avg_cost) + ($data['quantity'] * ($data['cost']+($data['shipping']/$data['quantity']))));
            $total_quantity = $wp_details->quantity + $data['quantity'];
            if (!empty($total_quantity)) {
                $avg_cost = $this->sma->formatDecimal(($total_cost / $total_quantity),12);
                $this->db->update('warehouses_products', ['avg_cost' => $avg_cost], ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']]);
            }
        } else {
			$total_cost     = (($data['quantity'] * ($data['cost']+($data['shipping']/($data['quantity'])))));
             $avg_cost = $this->sma->formatDecimal(($total_cost / $data['quantity']),12);

            $this->db->insert('warehouses_products', ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id'], 'avg_cost' => $avg_cost , 'quantity' => 0]);
        }
    }

	public function syncTransderdItem($product_id, $warehouse_id, $quantity, $option_id = null)
    {
        if ($pis = $this->site->getPurchasedItems($product_id, $warehouse_id, $option_id)) {
            $balance_qty = $quantity;
            foreach ($pis as $pi) {
                if ($balance_qty <= $quantity && $quantity > 0) {
                    if ($pi->quantity_balance >= $quantity) {
                        $balance_qty = $pi->quantity_balance - $quantity;
                        $this->db->update('purchase_items', ['quantity_balance' => $balance_qty], ['id' => $pi->id]);
                        $quantity = 0;
                    } elseif ($quantity > 0) {
                        $quantity    = $quantity - $pi->quantity_balance;
                        $balance_qty = $quantity;
                        $this->db->update('purchase_items', ['quantity_balance' => 0], ['id' => $pi->id]);
                    }
                }
                if ($quantity == 0) {
                    break;
                }
            }
        } else {
            $clause = ['purchase_id' => null, 'transfer_id' => null, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'option_id' => $option_id];
            $this->site->setPurchaseItem($clause, (0 - $quantity));
        }
        $this->site->syncQuantity(null, null, null, $product_id);
    }

	public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', ['warehouse_id' => $warehouse_id, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
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
                $this->syncAdjustment($product);

            }

			$date = date('Y-m-d H:i:s');

            $data1 = [
                'date'         => $date,
                'reference'    => $data['reference_no'],
                'amount'       => $data['value'],
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


            return true;
        }
        return false;
    }

	public function syncAdjustment($data = [])
    {
        if (!empty($data)) {
            $clause = ['product_id' => $data['product_id'],  'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
            $qty    =  0 - $data['quantity'] ;
            $this->site->setPurchaseItem($clause, $qty);

            $this->site->syncProductQty($data['product_id'], $data['warehouse_id']);

        }
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



	 public function updateDestruction_inventory($id, $data, $products)
    {
        $this->reverseDestruction($id);
        if ($this->db->update('destruction', $data, ['id' => $id]) && $this->db->delete('destruction_items', ['adjustment_id' => $id])) {
            foreach ($products as $product) {
                $product['adjustment_id'] = $id;
                $this->db->insert('destruction_items', $product);
                $this->syncAdjustment($product);

            }

            return true;
        }
        return false;
    }

	public function reverseDestruction($id)
    {
        if ($products = $this->getDestructionItems($id)) {
            foreach ($products as $adjustment) {
                $clause = ['product_id' => $adjustment->product_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id];
                $qty    = $adjustment->type == 'subtraction' ? (0 + $adjustment->quantity) : (0 - $adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncProductQty($adjustment->product_id, $adjustment->warehouse_id);

            }
        }
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





  public function updateTransfer($id, $data = [], $items = [])
    {
        $this->db->trans_off();
		$this->db->trans_start();
        $ostatus = $this->resetTransferActions($id);
        $status  = $data['status'];
        if ($this->db->update('transfers', $data, ['id' => $id])) {
            $tbl = $ostatus == 'completed' ? 'purchase_items' : 'transfer_items';
            $this->db->delete($tbl, ['transfer_id' => $id]);

            foreach ($items as $item) {
                $item['transfer_id'] = $id;
                $item['option_id']   = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : null;
                if ($status == 'completed') {
					$item['quantity']=$item['received_quantity'];
					unset($item['received_quantity']);
                    $item['date']         = date('Y-m-d');
                    $item['warehouse_id'] = $data['to_warehouse_id'];
                    $item['status']       = 'received';
                    $this->db->insert('purchase_items', $item);
                } else {
                    $this->db->insert('transfer_items', $item);
                }
				if ($status == 'completed') {
                    $this->updateAVCO(['product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['quantity'], 'cost' => $item['net_unit_cost'],'shipping'=>$data['shipping']]);
                    //$this->updateAVCO(['product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['received_quantity'], 'cost' => $item['net_unit_cost'],'shipping'=>$data['shipping']]);
                }


                if ($data['status'] == 'sent' || $data['status'] == 'completed') {
                    $this->syncTransderdItem($item['product_id'], $data['from_warehouse_id'], $item['quantity'], $item['option_id']);
                }
            }


        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Update:Transfers_model.php)');
        } else {
            return true;
        }

        return false;
    }



	public function resetTransferActions($id, $delete = null)
    {
        $otransfer = $this->getTransferByID($id);
        $oitems    = $this->getAllTransferItems($id, $otransfer->status);
        $ostatus   = $otransfer->status;
        if ($ostatus == 'sent' || $ostatus == 'completed') {
            foreach ($oitems as $item) {
                $option_id = (isset($item->option_id) && !empty($item->option_id)) ? $item->option_id : null;
                $clause    = ['purchase_id' => null, 'transfer_id' => null, 'product_id' => $item->product_id, 'warehouse_id' => $otransfer->from_warehouse_id, 'option_id' => $option_id];
                $this->site->setPurchaseItem($clause, $item->quantity);
                if ($delete) {
                    $option_id = (isset($item->option_id) && !empty($item->option_id)) ? $item->option_id : null;
                    $clause    = ['purchase_id' => null, 'transfer_id' => null, 'product_id' => $item->product_id, 'warehouse_id' => $otransfer->to_warehouse_id, 'option_id' => $option_id];
                    $this->site->setPurchaseItem($clause, ($item->quantity_balance - $item->quantity));
                }
            }
        }
        return $ostatus;
    }


	public function getTransferByID($id)
    {
        $q = $this->db->get_where('transfers', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

	public function getAllTransferItems($transfer_id, $status)
    {
        if ($status == 'completed') {
            $this->db->select('purchase_items.*, product_variants.name as variant, products.unit, products.hsn_code as hsn_code, products.second_name as second_name')
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('product_variants', 'product_variants.id=purchase_items.option_id', 'left')
                ->group_by('purchase_items.id')
                ->where('transfer_id', $transfer_id);
        } else {
            $this->db->select('transfer_items.*, product_variants.name as variant, products.unit, products.hsn_code as hsn_code, products.second_name as second_name')
                ->from('transfer_items')
                ->join('products', 'products.id=transfer_items.product_id', 'left')
                ->join('product_variants', 'product_variants.id=transfer_items.option_id', 'left')
                ->group_by('transfer_items.id')
                ->where('transfer_id', $transfer_id);
        }
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function addBomRequestDetailLC($bom_id, $data = [])
	{
		$this->deleteBomDetailsByBomIdLC($bom_id);
		if ($this->db->insert_batch('man_bom_labors', $data)) {
	        return true;
        }
	return false;
    }

    public function getBomDetailsLC($bom_id)
    {
        $q = $this->db->get_where('man_bom_labors', ['bom_id' => $bom_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

    public function deleteBomDetailsByBomIdLC($bom_id)
    {
        if ($this->db->delete('man_bom_labors', ['bom_id' => $bom_id])) {
            return true;
        }
        return false;
    }



    public function addBomRequestDetailIE($bom_id, $data = [])
	{
		$this->deleteBomDetailsByBomIdIE($bom_id);
		if ($this->db->insert_batch('man_bom_expenses', $data)) {
	        return true;
        }
	return false;
    }

    public function getBomDetailsIE($bom_id)
    {
        $q = $this->db->get_where('man_bom_expenses', ['bom_id' => $bom_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

    public function deleteBomDetailsByBomIdIE($bom_id)
    {
        if ($this->db->delete('man_bom_expenses', ['bom_id' => $bom_id])) {
            return true;
        }
        return false;
    }



    public function getManufacturingDetailsLC($man_id,$req_status = null)
    {
        if($req_status){
			$this->db->where('req_status',$req_status);
		}
        $q = $this->db->get_where('man_manufacturing_labors', ['man_id' => $man_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }

    public function getManufacturingDetailsIE($man_id,$req_status = null)
    {
        if($req_status){
			$this->db->where('req_status',$req_status);
		}
        $q = $this->db->get_where('man_manufacturing_expenses', ['man_id' => $man_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
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

    public function updateManufacturingDetails($man_id,$actual_qty,$product_id)
    {
        if ($this->db->update('man_manufacturing_details', ['actual_qty' => $actual_qty], ['man_id' => $man_id,'product_id' => $product_id,'req_status' => 'normal'])) {
            return true;
        }
        return false;
    }

    public function updateManufacturingDetailsLC($man_id,$actual_hours_no,$id)
    {
        if ($this->db->update('man_manufacturing_labors', ['actual_hours_no' => $actual_hours_no], ['man_id' => $man_id,'id' => $id,'req_status' => 'normal'])) {
            return true;
        }
        return false;
    }

    public function updateManufacturingDetailsIE($man_id,$actual_cost,$id)
    {
        if ($this->db->update('man_manufacturing_expenses', ['actual_cost' => $actual_cost], ['man_id' => $man_id,'id' => $id,'req_status' => 'normal'])) {
            return true;
        }
        return false;
    }


    public function getTransferByManIdASC($man_id)
    {
        $this->db->order_by('id','ASC');
		$q = $this->db->get_where('transfers', ['man_id' => $man_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getDashboardStats($warehouse_id = null)
    {
        $this->db->select("
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_orders,
            SUM(CASE WHEN status = 'produce' THEN 1 ELSE 0 END) as in_progress_orders,
            SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as done_orders,
            COALESCE(SUM(quantity), 0) as total_planned_qty,
            COALESCE(SUM(confirm_qty), 0) as total_produced_qty
        ", false);

        if ($warehouse_id) {
            $this->db->join('man_bom', 'man_bom.id = man_manufacturing.bom_id', 'left');
            $this->db->where('man_bom.wh_manufacturing', $warehouse_id);
        }

        $q = $this->db->get('man_manufacturing');
        $row = ($q && $q->num_rows() > 0) ? $q->row() : null;

        $total_boms = $this->db->count_all_results('man_bom');

        $total_orders = (int)($row->total_orders ?? 0);
        $confirmed_orders = (int)($row->confirmed_orders ?? 0);
        $in_progress_orders = (int)($row->in_progress_orders ?? 0);
        $done_orders = (int)($row->done_orders ?? 0);
        $total_planned_qty = (float)($row->total_planned_qty ?? 0);
        $total_produced_qty = (float)($row->total_produced_qty ?? 0);
        $remaining_qty = max(0, $total_planned_qty - $total_produced_qty);

        $completion_rate = ($total_planned_qty > 0) ? round(($total_produced_qty / $total_planned_qty) * 100, 1) : 0;
        $confirmed_pct = ($total_orders > 0) ? round(($confirmed_orders / $total_orders) * 100, 1) : 0;
        $in_progress_pct = ($total_orders > 0) ? round(($in_progress_orders / $total_orders) * 100, 1) : 0;
        $done_pct = ($total_orders > 0) ? round(($done_orders / $total_orders) * 100, 1) : 0;

        return [
            'total_orders' => $total_orders,
            'confirmed_orders' => $confirmed_orders,
            'in_progress_orders' => $in_progress_orders,
            'done_orders' => $done_orders,
            'total_planned_qty' => $total_planned_qty,
            'total_produced_qty' => $total_produced_qty,
            'remaining_qty' => $remaining_qty,
            'completion_rate' => $completion_rate,
            'confirmed_pct' => $confirmed_pct,
            'in_progress_pct' => $in_progress_pct,
            'done_pct' => $done_pct,
            'total_boms' => (int)$total_boms,
        ];
    }

    public function getCostSummary($warehouse_id = null)
    {
        $prefix = $this->db->dbprefix;
        
        $q_mat = $this->db->query("SELECT COALESCE(SUM(CASE WHEN actual_qty > 0 THEN actual_qty * product_avg_cost ELSE product_quantity * product_avg_cost END), 0) as material_cost FROM {$prefix}man_manufacturing_details");
        $material_cost = ($q_mat && $q_mat->num_rows() > 0) ? (float)$q_mat->row()->material_cost : 0.0;

        $q_lab = $this->db->query("SELECT COALESCE(SUM(CASE WHEN actual_hours_no > 0 THEN actual_hours_no * hours_wage ELSE hours_no * hours_wage END), 0) as labor_cost FROM {$prefix}man_manufacturing_labors");
        $labor_cost = ($q_lab && $q_lab->num_rows() > 0) ? (float)$q_lab->row()->labor_cost : 0.0;

        $q_exp = $this->db->query("SELECT COALESCE(SUM(CASE WHEN actual_cost > 0 THEN actual_cost ELSE cost END), 0) as expense_cost FROM {$prefix}man_manufacturing_expenses");
        $expense_cost = ($q_exp && $q_exp->num_rows() > 0) ? (float)$q_exp->row()->expense_cost : 0.0;

        $total_cost = $material_cost + $labor_cost + $expense_cost;

        $q_qty = $this->db->query("SELECT COALESCE(SUM(confirm_qty), 0) as produced_qty FROM {$prefix}man_manufacturing WHERE status = 'done'");
        $produced_qty = ($q_qty && $q_qty->num_rows() > 0) ? (float)$q_qty->row()->produced_qty : 0.0;

        $avg_unit_cost = ($produced_qty > 0 && $total_cost > 0) ? ($total_cost / $produced_qty) : 0.0;

        $material_pct = ($total_cost > 0) ? round(($material_cost / $total_cost) * 100, 1) : 0;
        $labor_pct = ($total_cost > 0) ? round(($labor_cost / $total_cost) * 100, 1) : 0;
        $expense_pct = ($total_cost > 0) ? round(($expense_cost / $total_cost) * 100, 1) : 0;

        return [
            'material_cost' => $material_cost,
            'labor_cost' => $labor_cost,
            'expense_cost' => $expense_cost,
            'total_cost' => $total_cost,
            'avg_unit_cost' => $avg_unit_cost,
            'material_pct' => $material_pct,
            'labor_pct' => $labor_pct,
            'expense_pct' => $expense_pct,
        ];
    }

    public function getMonthlyTrends($limit = 12)
    {
        $this->db->select("
            DATE_FORMAT(created_date, '%Y-%m') as month,
            COUNT(*) as order_count,
            COALESCE(SUM(quantity), 0) as planned_qty,
            COALESCE(SUM(confirm_qty), 0) as produced_qty,
            SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as done_count
        ", false)
        ->from('man_manufacturing')
        ->where('created_date IS NOT NULL')
        ->group_by("DATE_FORMAT(created_date, '%Y-%m')")
        ->order_by('month', 'DESC')
        ->limit($limit);

        $q = $this->db->get();
        if ($q && $q->num_rows() > 0) {
            $rows = $q->result();
            return array_reverse($rows);
        }
        return [];
    }

    public function getTopManufacturedProducts($limit = 5)
    {
        $this->db->select("
            b.id as bom_id,
            b.name as bom_name,
            p.id as product_id,
            COALESCE(p.name, b.name) as product_name,
            p.code as product_code,
            COUNT(m.id) as order_count,
            COALESCE(SUM(m.quantity), 0) as planned_qty,
            COALESCE(SUM(m.confirm_qty), 0) as produced_qty
        ", false)
        ->from('man_manufacturing m')
        ->join('man_bom b', 'b.id = m.bom_id', 'left')
        ->join('products p', 'p.id = b.product_id', 'left')
        ->group_by('m.bom_id')
        ->order_by('order_count', 'DESC')
        ->order_by('produced_qty', 'DESC')
        ->limit($limit);

        $q = $this->db->get();
        if ($q && $q->num_rows() > 0) {
            return $q->result();
        }
        return [];
    }

    public function getRawMaterialAlerts($limit = 6)
    {
        $prefix = $this->db->dbprefix;
        $subquery = "p.id IN (SELECT DISTINCT product_id FROM {$prefix}man_bom_details UNION SELECT DISTINCT product_id FROM {$prefix}man_manufacturing_details)";
        
        $this->db->select('p.id, p.code, p.name, p.quantity as current_stock, p.alert_quantity, u.name as unit_name')
        ->from('products p')
        ->join('units u', 'u.id = p.unit', 'left')
        ->where($subquery, null, false)
        ->where('(p.quantity <= p.alert_quantity OR p.quantity <= 0)', null, false)
        ->order_by('p.quantity', 'ASC')
        ->limit($limit);

        $q = $this->db->get();
        if ($q && $q->num_rows() > 0) {
            return $q->result();
        }
        return [];
    }

    public function getRecentManufacturingOrders($limit = 8)
    {
        $this->db->select("
            m.id,
            m.created_date,
            m.deadline_start,
            m.quantity as planned_qty,
            m.confirm_qty as produced_qty,
            m.status,
            b.name as bom_name,
            b.id as bom_id,
            CONCAT(u.first_name, ' ', u.last_name) as creator_name
        ", false)
        ->from('man_manufacturing m')
        ->join('man_bom b', 'b.id = m.bom_id', 'left')
        ->join('users u', 'u.id = m.created_by', 'left')
        ->order_by('m.id', 'DESC')
        ->limit($limit);

        $q = $this->db->get();
        if ($q && $q->num_rows() > 0) {
            return $q->result();
        }
        return [];
    }
}

