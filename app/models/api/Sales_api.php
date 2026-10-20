<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countSales($filters = [], $ref = null)
    {
            $this->db->where('pos', 1);
        if ($filters['warehouse_id']) {
            $this->db->where('warehouse_id', $filters['warehouse_id']);
        }
        if ($filters['biller_id']) {
            $this->db->where('biller_id', $filters['biller_id']);
        }
        if ($filters['customer']) {
            $this->db->where('customer', $filters['customer']);
        }
        if ($filters['customer_id']) {
            $this->db->where('customer_id', $filters['customer_id']);
        }
        if ($filters['start_date']) {
            $this->db->where('date >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $this->db->where('date <=', $filters['end_date']);
        }
        $this->db->from('sales');
        return $this->db->count_all_results();
    }

    public function getProductVariantByID($id)
    {
        return $this->db->get_where('product_variants', ['id' => $id], 1)->row();
    }

    public function getSale($filters)
    {
        if (!empty($sales = $this->getSales($filters))) {
            return array_values($sales)[0];
        }
        return false;
    }

    public function getSaleItems($sale_id)
    {
        return $this->db->get_where('sale_items', ['sale_id' => $sale_id])->result();
    }

	public function getSaleItems_new($sale_id)
    {
      	$myQuery ="select * from " . $this->db->dbprefix('sale_items') . "  where sale_id='".$sale_id."'";


		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
		//return $this->db->get_where('sale_items', ['sale_id' => $sale_id])->result();
    }


    public function getSaleItemsV2_new($sale_id)
    {
        //$myQuery ="select client_type,product_name,product_code,product_unit_id as unit_id,unit_quantity as item_quantity,unit_price as item_price,tax_rate_id,item_tax,discount,item_discount,subtotal as item_total,cloth_length,cloth_neck,cloth_shoulder,cloth_chest,cloth_waist,cloth_seat,cloth_sleasves,cloth_sleav_circum,modelTypeID , collerTypeID  , cuffTypeID  , pocketTypeID , fillingTypeID  ,zipperTypeID ,notes ,clothColor ,tatrezType from " . $this->db->dbprefix('sale_items') . "  where sale_id='".$sale_id."'";
        $myQuery ="select client_type,product_name,product_code,product_unit_id as unit_id,unit_quantity as item_quantity,net_unit_price as item_price,tax_rate_id,item_tax,discount,item_discount,subtotal as item_total,cloth_length,cloth_neck,cloth_shoulder,cloth_chest,cloth_waist,cloth_seat,cloth_sleasves,cloth_sleav_circum,modelTypeID , collerTypeID  , cuffTypeID  , pocketTypeID , fillingTypeID  ,zipperTypeID ,notes ,clothColor ,tatrezType ,cloth_thigh ,cloth_leg_length ,cloth_shoulder_width ,cloth_top_length ,cloth_hand_wrist ,cloth_leg_width from " . $this->db->dbprefix('sale_items') . "  where sale_id='".$sale_id."'";
        //$myQuery ="select client_type,product_name,product_code,product_unit_id as unit_id,unit_quantity as item_quantity,((subtotal - item_discount - item_tax) / unit_quantity) as item_price,tax_rate_id,item_tax,discount,item_discount,subtotal as item_total,cloth_length,cloth_neck,cloth_shoulder,cloth_chest,cloth_waist,cloth_seat,cloth_sleasves,cloth_sleav_circum,modelTypeID , collerTypeID  , cuffTypeID  , pocketTypeID , fillingTypeID  ,zipperTypeID ,notes ,clothColor ,tatrezType from " . $this->db->dbprefix('sale_items') . "  where sale_id='".$sale_id."'";


		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
		//return $this->db->get_where('sale_items', ['sale_id' => $sale_id])->result();
    }

	public function getSalePayments($sale_id)
    {
        return $this->db->get_where('payments', ['sale_id' => $sale_id])->result();
    }

	public function getSalePayments_new($sale_id)
    {
      	$myQuery ="select * from " . $this->db->dbprefix('payments') . "  where sale_id='".$sale_id."'";


		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
		//return $this->db->get_where('sale_items', ['sale_id' => $sale_id])->result();
    }

    public function getSalePaymentsV2_new($sale_id)
    {
      	$myQuery ="select paid_by as payment_type,amount as paid_amount,cc_type,cheque_no from " . $this->db->dbprefix('payments') . "  where sale_id='".$sale_id."'";


		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
		//return $this->db->get_where('sale_items', ['sale_id' => $sale_id])->result();
    }
    public function getSaleTRMeasurement($sale_id)
    {
        return $this->db->get_where('TR_Measurement', ['sale_id' => $sale_id])->result();
    }

    public function getSaleTRMeasurement_new($sale_id)
    {
    $myQuery ="select * from " . $this->db->dbprefix('TR_Measurement') . "  where sale_id='".$sale_id."'";


		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
		}

    public function getSales($filters = [])
    {
            $this->db->where('pos', 1);
        if ($filters['customer']) {
            $this->db->where('customer', $filters['customer']);
        }
        if ($filters['customer_id']) {
            $this->db->where('customer_id', $filters['customer_id']);
        }
        if ($filters['start_date']) {
            $this->db->where('date >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $this->db->where('date <=', $filters['end_date']);
        }
		if ($filters['warehouse_id']) {
            $this->db->where('warehouse_id', $filters['warehouse_id']);
        }
        if ($filters['biller_id']) {
            $this->db->where('biller_id', $filters['biller_id']);
        }
        if ($filters['reference']) {
            $this->db->where('reference_no', $filters['reference']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'desc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('sales')->result();
    }

     public function getSalesTailor($filters = [])
    {
            $this->db->where('pos', 1);
			/*$this->db->select("id, reference_no, customer_id, customer, date, supply_date as delivery_date, sale_status,(grand_total-(paid+discount_allowance))  as balance");
		if ($filters['id']) {
            $this->db->where('id', $filters['id']);
        }if ($filters['customer']) {
            $this->db->where('customer', $filters['customer']);
        }
        if ($filters['customer_id']) {
            $this->db->where('customer_id', $filters['customer_id']);
        }
        if ($filters['start_date']) {
            $this->db->where('date >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $this->db->where('date <=', $filters['end_date']);
        }
		if ($filters['warehouse_id']) {
            $this->db->where('warehouse_id', $filters['warehouse_id']);
        }
        if ($filters['reference']) {
            $this->db->where('reference_no', $filters['reference']);
        } else {
            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'desc');
            //$this->db->limit($filters['limit'], ($filters['start'] - 1));
        }
		$this->db->order_by('id', 'desc');


        return $this->db->get('sales')->result();
		*/
		if ($filters['id']) {

        	$myQuery ="select s.id, s.reference_no, s.customer_id, s.customer,c.phone, s.date, s.supply_date as delivery_date, s.sale_status,(s.grand_total-(s.paid+discount_allowance)) as balance from " . $this->db->dbprefix('sales') . " s," . $this->db->dbprefix('companies') . " c where s.customer_id=c.id and s.id='".$filters['id']."' order by s.id desc";
        }
		else{
			$myQuery ="select s.id, s.reference_no, s.customer_id, s.customer,c.phone, s.date, s.supply_date as delivery_date, s.sale_status,(s.grand_total-(s.paid+discount_allowance)) as balance from " . $this->db->dbprefix('sales') . " s," . $this->db->dbprefix('companies') . " c where s.customer_id=c.id order by s.id desc";
        }

		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
    }


    public function getSalesTailorV2($filters = [])
    {
            $this->db->where('pos', 1);
			/*$this->db->select("id, reference_no, customer_id, customer, date, supply_date as delivery_date, sale_status,(grand_total-(paid+discount_allowance))  as balance");
		if ($filters['id']) {
            $this->db->where('id', $filters['id']);
        }if ($filters['customer']) {
            $this->db->where('customer', $filters['customer']);
        }
        if ($filters['customer_id']) {
            $this->db->where('customer_id', $filters['customer_id']);
        }
        if ($filters['start_date']) {
            $this->db->where('date >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $this->db->where('date <=', $filters['end_date']);
        }
		if ($filters['warehouse_id']) {
            $this->db->where('warehouse_id', $filters['warehouse_id']);
        }
        if ($filters['reference']) {
            $this->db->where('reference_no', $filters['reference']);
        } else {
            //$this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'desc');
            //$this->db->limit($filters['limit'], ($filters['start'] - 1));
        }
		$this->db->order_by('id', 'desc');


        return $this->db->get('sales')->result();
		*/
		if ($filters['id']) {

        	$myQuery ="select s.id, s.reference_no, s.customer_id, s.customer,c.phone, s.date, s.supply_date as delivery_date, s.sale_status,(s.grand_total-(s.paid+discount_allowance)) as balance from " . $this->db->dbprefix('sales') . " s," . $this->db->dbprefix('companies') . " c where s.customer_id=c.id and s.id='".$filters['id']."'  and s.warehouse_id='".$filters['warehouse_id']."'  and s.biller_id='".$filters['biller_id']."' order by s.id desc";
        }
		else{
			$myQuery ="select s.id, s.reference_no, s.customer_id, s.customer,c.phone, s.date, s.supply_date as delivery_date, s.sale_status,(s.grand_total-(s.paid+discount_allowance)) as balance from " . $this->db->dbprefix('sales') . " s," . $this->db->dbprefix('companies') . " c where s.customer_id=c.id and s.warehouse_id='".$filters['warehouse_id']."'  and s.biller_id='".$filters['biller_id']."' order by s.id desc";
        }

		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
    }



    public function getSalesTailorV2_new($filters = [])
    {
            $this->db->where('pos', 1);

		if ($filters['id']) {

        	$myQuery ="select s.id, s.reference_no, s.customer_id as client_id, s.customer as client_name,c.phone, s.date as invoice_date, s.supply_date as delivery_date, s.sale_status as invoice_status,s.grand_total as public_total,s.total_discount as public_discount,s.total_tax as public_tax,(s.grand_total-(s.paid+discount_allowance)) as balance,return_id from " . $this->db->dbprefix('sales') . " s," . $this->db->dbprefix('companies') . " c where s.customer_id=c.id and s.id='".$filters['id']."'  and s.warehouse_id='".$filters['warehouse_id']."'  and s.biller_id='".$filters['biller_id']."' order by s.id desc";
        }
		else{
			$myQuery ="select s.id, s.reference_no, s.customer_id as client_id, s.customer as client_name,c.phone, s.date as invoice_date, s.supply_date as delivery_date, s.sale_status as invoice_status,s.grand_total as public_total,s.total_discount as public_discount,s.total_tax as public_tax,(s.grand_total-(s.paid+discount_allowance)) as balance,return_id from " . $this->db->dbprefix('sales') . " s," . $this->db->dbprefix('companies') . " c where s.customer_id=c.id and s.warehouse_id='".$filters['warehouse_id']."'  and s.biller_id='".$filters['biller_id']."' order by s.id desc";
        }

		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
    }




    public function getSettings()
    {
        return $this->db->get('settings')->result();
    }

    public function getUser($id)
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("CONCAT('{$uploads_url}', avatar) as avatar_url, email, first_name, gender, id, last_name, username");
        return $this->db->get_where('users', ['id' => $id], 1)->row();
    }

    public function getWarehouseByID($id)
    {
        return $this->db->get_where('warehouses', ['id' => $id], 1)->row();
    }

    public function getSalesReportAPP($start_date,$end_date,$warehouse_id = null)
    {
        $this->db
                ->select($this->db->dbprefix('companies') . '.id as id, company, count(' . $this->db->dbprefix('sales') . '.id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, COALESCE(sum(discount_allowance), 0) as discount_allowance, ( COALESCE(sum(grand_total), 0) - (COALESCE(sum(paid), 0) + COALESCE(sum(discount_allowance), 0))) as balance', false)
                ->from('companies')
                ->join('sales', 'sales.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->order_by('companies.company asc')
                ->group_by('companies.id');
        if ($warehouse_id){
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }
         $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }


		return $data;
    }


    public function getSalesTransReportAPP($start_date,$end_date,$warehouse_id = null)
    {


        $sp = '( SELECT  DATE_FORMAT(date, "%Y-%m-%d %T") as solddate, si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.unit_quantity ) totalSale, SUM( (CASE WHEN si.isTobacco = 1 THEN (((si.net_unit_price/2) + ((si.item_discount/2) / si.unit_quantity)) * si.unit_quantity) ELSE 0 END)) totalTobaccoFees, SUM( si.item_tax ) totalTax, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';

            if ($warehouse_id) {
               $sp .= " and si.warehouse_id = '{$warehouse_id}' ";

            }


			if ($start_date && $end_date) {
                $sp .= " and s.date BETWEEN '{$start_date}' and  '{$end_date}'";
            }


        $sp .= ' GROUP BY si.product_id ) PSales';



        $this->db
                ->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . ".name,
				COALESCE( PSales.soldQty, 0 ) as sales_quantity,
                COALESCE( PSales.totalSale, 0 ) as sold,
                COALESCE( PSales.totalTobaccoFees, 0 ) as soldTobaccoFees,
                COALESCE( PSales.totalTax, 0 ) as sales_tax,
                (COALESCE( PSales.totalSale, 0 ) + (COALESCE( PSales.totalTax, 0 )  )) as Final_Total,
                (COALESCE( PSales.totalGoodsCost, 0 ) ) as totalGoodsCost,
                (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  )) as Profit", false)
                ->from('products')
                ->join($sp, 'products.id = PSales.product_id and PSales.solddate is not null', 'left');

        if ($warehouse_id){
         //   $this->db->where('PSales.warehouse_id', $warehouse_id);
        }
        $this->db->where('PSales.solddate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }


		return $data;
    }

    public function getCustomersPymentsReportAPP($start_date,$end_date,$customer_id = null)
    {


        $s = "( SELECT a.customer_id, COALESCE(SUM((a.grand_total-a.paid)), 0) as due_amount, COALESCE(SUM(CASE WHEN b.paid_by='cash' THEN b.amount ELSE 0 END), 0)  amount_cash, COALESCE(SUM(CASE WHEN b.paid_by='CC' THEN b.amount ELSE 0 END), 0)  amount_CC, COALESCE(SUM(CASE WHEN b.paid_by!='CC' and b.paid_by!='cash' THEN b.amount ELSE 0 END), 0)  amount_other,COALESCE(SUM(commercial_discount),0) as commercial_discount ,((COALESCE(SUM(CASE WHEN b.paid_by='cash' THEN b.amount ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN b.paid_by='CC' THEN b.amount ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN b.paid_by!='CC' and b.paid_by!='cash' THEN b.amount ELSE 0 END), 0)) + COALESCE(SUM(commercial_discount),0)) as  net from {$this->db->dbprefix('sales')} a LEFT JOIN {$this->db->dbprefix('payments')} b on a.id=b.sale_id  ";


            if ($customer_id || $start_date) {
                $s .= ' WHERE ';
            }
            if ($customer_id) {
                $s .= " a.customer_id = {$customer_id} ";
            }
            if ($customer_id && $start_date) {
                $s .= ' AND ';
            }

            if ($start_date) {
                $s .= " a.date BETWEEN '{$start_date}' and '{$end_date}' ";
			 }




			$s .= " GROUP BY a.customer_id ) FS";





            $this->db
            ->select($this->db->dbprefix('companies') . '.id as id, company, FS.amount_cash, FS.amount_CC, FS.amount_other, FS.commercial_discount, FS.net, FS.due_amount', false)
            ->from('companies')
            ->join($s, 'FS.customer_id=companies.id')
            ->where('companies.group_name', 'customer');

            if ($customer_id) {
                $this->db->where('companies.id', $customer_id);
                }


        $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }


		return $data;
    }

    public function getDailySalesAPP($year, $month, $warehouse_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total-total_discount, 0 ) ) AS totalBVAT, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' ) ORDER BY CAST(DATE_FORMAT( date, '%c' ) AS UNSIGNED) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getMonthlySalesItemsAPP($year, $warehouse_id = null)
    {



        $myQuery1 = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( shipping, 0 ) ) AS shipping, SUM( COALESCE( grand_total, 0 ) ) AS grand_total
            FROM " . $this->db->dbprefix('sales') . '  WHERE ';
        if ($warehouse_id) {
            $myQuery1 .= " warehouse_id = {$warehouse_id} AND ";
        }

        $myQuery1 .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY CAST(date_format( date, '%c' ) AS UNSIGNED) ASC";
        $q1 = $this->db->query($myQuery1, false);
        if ($q1->num_rows() > 0) {
            foreach (($q1->result()) as $row) {
                $data1[] = $row;
            }
		}
            //return $data1[0]->shipping;










	 $myQuery = "SELECT DATE_FORMAT( s.date,  '%c' ) AS date, SUM( COALESCE( si.item_tax, 0 ) ) AS tax,  SUM( si.net_unit_price * si.unit_quantity ) AS total, SUM( COALESCE( si.item_discount, 0 ) ) AS discount
            FROM " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id WHERE ';
        if ($warehouse_id) {
            $myQuery .= " s.warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( s.date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( s.date, '%c' ) ORDER BY CAST(date_format( s.date, '%c' ) AS UNSIGNED) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
			//$q->row()->total=$q->row()->total+$qExpensesTotal;
            $i=0;
            foreach (($q->result()) as $row) {
				$row->shipping=$data1[$i]->shipping;
            	$row->grand_total=$data1[$i]->grand_total;

                $data[] = $row;
				$i++;
            }
            return $data;
        }
        return false;
    }


    public function getTotalSalesForProfitandLess($start_date = null, $end_date = null, $warehouse_id = null)
    {
        $this->db->select('count(id) as total,sum(CASE WHEN total >=0 THEN (total)  END)  as total_amount,sum(CASE WHEN total <0 THEN (total)  END)  as rtotal_amount,sum(CASE WHEN total <0 THEN (shipping)  END)  as other_expenses,sum(CASE WHEN total >=0 THEN (shipping)  END)  as other_revenues, SUM(COALESCE(total_discount, 0)) as total_discount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(discount_allowance, 0)) as discount_allowance, SUM(COALESCE(total_tax, 0)) as tax', false)
            ->where('sale_status !=', 'pending');
           // ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($start_date && $end_date) {
            $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getTotalSalesCosofGoods($start_date = null, $end_date = null, $warehouse_id = null)
    {
        $this->db->select('sum(item_cost_value)  as item_cost_value')
		->join('sale_items', 'sale_items.sale_id = sales.id', 'left')

            ->where('sales.sale_status !=', 'pending');
            //->where('sales.date BETWEEN ' . $start . ' and ' . $end);
            if ($start_date && $end_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

        if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getTotalExpensesForProfitandLess($start_date = null, $end_date = null, $warehouse_id = null)
    {
        $this->db->select('count(id) as total,sum(CASE WHEN other =0 THEN (amount)  END)  as total_amount, sum(CASE WHEN other =1 THEN (amount)  END) as other_expenses', false);
            //->where('date BETWEEN ' . $start . ' and ' . $end);
            if ($start_date && $end_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getTotalRevenues($start_date = null, $end_date = null, $warehouse_id = null)
    {
        $this->db->select('count(id) as total, sum(COALESCE(amount, 0)) as total_amount', false);
         //   ->where('date BETWEEN ' . $start . ' and ' . $end);
         if ($start_date && $end_date) {
            $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('revenue');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getTotalPurchases($start_date, $end_date, $warehouse_id = null)
    {

        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax, SUM(COALESCE(discount_earned, 0)) as discount_earned', false)
            ->where('status !=', 'pending');


        /*if ($start) {
			$this->db->where('date BETWEEN ' . $start . ' and ' . $end);

		}*/
		if ($start_date && $end_date) {
            $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getDelivers($filters = [])
    {

        if ($filters['Offline_id']) {
            $myQuery ="select s.id, s.reference_no, s.customer_id, s.customer,c.phone, s.date, s.status, biller_id, biller from " . $this->db->dbprefix('delivers') . " s," . $this->db->dbprefix('companies') . " c where s.customer_id=c.id and s.Offline_id = '".$filters['Offline_id']."'  and s.warehouse_id='".$filters['warehouse_id']."' order by s.id asc";
        }else{
            $myQuery ="select s.id, s.reference_no, s.customer_id, s.customer,c.phone, s.date, s.status, biller_id, biller from " . $this->db->dbprefix('delivers') . " s," . $this->db->dbprefix('companies') . " c where s.customer_id=c.id and s.id > '".$filters['id']."'  and s.warehouse_id='".$filters['warehouse_id']."' order by s.id asc";
        }


		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
    }

    public function getDeliverItems($sale_id)
    {
      	$myQuery ="select `product_id`,`product_code`,`product_name`,`product_type`,`quantity`,`product_unit_id` from " . $this->db->dbprefix('deliver_items') . "  where deliver_id='".$sale_id."'";


		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            //foreach (($q->result()) as $row) {

            //    $data[] = $row;
                return $q->result();
            //}
        //    return $data;
        }
		return false;
		//return $this->db->get_where('sale_items', ['sale_id' => $sale_id])->result();
    }

    public function countDelivers($filters = [], $ref = null)
    {
        if ($filters['warehouse_id']) {
            $this->db->where('warehouse_id', $filters['warehouse_id']);
        }

        if ($filters['Offline_id']) {
            $this->db->where('Offline_id', $filters['Offline_id']);
        }else{
            if ($filters['id']) {
                $this->db->where('id > ', $filters['id']);
            }
        }

        $this->db->from('delivers');
        return $this->db->count_all_results();
    }



}
