<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Db_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBestSeller($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59';
        }

        $this->db
            ->select('product_name, product_code')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('date >=', $start_date)
            ->where('date <', $end_date)
            ->group_by('product_name, product_code')
            ->order_by('sum(quantity)', 'desc')
            ->limit(10);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBestSellerchart($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59';
        }

        $this->db
            ->select('product_name, product_code')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('quantity >', 0)
            //->where('date >=', $start_date)
            //->where('date <', $end_date)
            ->group_by('product_name, product_code')
            ->order_by('sum(quantity)', 'desc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getBestPurchaseschart($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59';
        }

        $this->db
            ->select('product_name, product_code')
            ->select_sum('quantity')
            ->from('purchase_items')
            ->join('purchases', 'purchases.id = purchase_items.purchase_id', 'left')
            ->where('quantity >', 0)
            //->where('date >=', $start_date)
            //->where('date <', $end_date)
            ->group_by('product_name, product_code')
            ->order_by('sum(quantity)', 'desc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

      public function getUnBestPurchaseschart($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59';
        }

        $this->db
            ->select('product_name, product_code')
            ->select_sum('quantity')
            ->from('purchase_items')
            ->join('purchases', 'purchases.id = purchase_items.purchase_id', 'left')
            ->where('quantity >', 0)
            //->where('date >=', $start_date)
            //->where('date <', $end_date)
            ->group_by('product_name, product_code')
            ->order_by('sum(quantity)', 'asc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBestCustomerschart($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59';
        }

        $this->db
            ->select('customer')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('quantity >', 0)
            //->where('date >=', $start_date)
            //->where('date <', $end_date)
            ->group_by('customer')
            ->order_by('sum(quantity)', 'desc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getUnBestCustomerschart($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59';
        }

        $this->db
            ->select('customer')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('quantity >', 0)
            //->where('date >=', $start_date)
            //->where('date <', $end_date)
            ->group_by('customer')
            ->order_by('sum(quantity)', 'asc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getUnBestSellerchart($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59';
        }

        $this->db
            ->select('product_name, product_code')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('quantity >', 0)
            //->where('date >=', $start_date)
            //->where('date <', $end_date)
            ->group_by('product_name, product_code')
            ->order_by('sum(quantity)', 'asc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getChartData()
    {
        $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE( P.purchases, 0 ) as purchases,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total * currency_rate) Sales,
                SUM(total_tax * currency_rate) tax1,
                SUM(order_tax * currency_rate) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S
            LEFT JOIN ( SELECT  date_format(date, '%Y-%m') Month,
                        SUM(total_tax * currency_rate) ptax,
                        SUM(order_tax * currency_rate) otax,
                        SUM((total * currency_rate) - (total_discount * currency_rate)) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
                        GROUP BY date_format(date, '%Y-%m')) P
            ON S.Month = P.Month
            ORDER BY S.Month";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getChartDataPurchases()
    {
        $myQuery = "SELECT  date_format(date, '%Y-%m') month,
                        SUM(COALESCE(total_tax * currency_rate,0)) ptax,
                        SUM(COALESCE(order_tax * currency_rate,0)) otax,
                        SUM(COALESCE((total * currency_rate) - (total_discount * currency_rate),0)) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
                        WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH )
                        GROUP BY date_format(date, '%Y-%m')
                        ORDER BY date_format(date, '%Y-%m')";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getChartDataTrends()
    {
        $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales
		FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(total_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date >= date_sub( now( ) , INTERVAL 11 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S

            ORDER BY S.Month";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getChartDataTrendsV2($start = null, $end = null, $warehouse_id = null)
    {
        if ($warehouse_id) {
            $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE(S.tax1, 0) as tax
		FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(total_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date BETWEEN '".$start."' and '".$end."' and warehouse_id=".$warehouse_id." and date >= date_sub( now( ) , INTERVAL 11 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S

            ORDER BY S.Month";
        }else{
            $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE(S.tax1, 0) as tax
		FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(total_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date BETWEEN '".$start."' and '".$end."' and date >= date_sub( now( ) , INTERVAL 11 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S

            ORDER BY S.Month";
        }
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getTotalSalesForProfitandLess($start = null, $end = null, $warehouse_id = null)
    {
        $this->db->select("date_format(date, '%Y-%m')  Month,sum(CASE WHEN total >=0 THEN (total)  END)  as total_amount,sum(CASE WHEN total <0 THEN (total)  END)  as rtotal_amount,max(total) as MaxTotal ,sum(CASE WHEN total <0 THEN (shipping)  END)  as other_expenses,sum(CASE WHEN total >=0 THEN (shipping)  END)  as other_revenues, SUM(COALESCE(total_discount, 0)) as total_discount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax", false)
            ->where('sale_status !=', 'pending');
           $this->db->group_by("date_format(date, '%Y-%m')");
           $this->db->order_by("date_format(date, '%Y-%m') desc");
           $this->db->limit(5);
            // ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($start) {
            $this->db->where('date >=', $start);
        }
        if ($end) {
            $this->db->where('date <=', $end);
        }
        if ($warehouse_id) {
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

     public function getTotalSalesCosofGoods($start = null, $end = null, $warehouse_id = null)
    {
        $this->db->select('sum(item_cost_value)  as item_cost_value')
		->join('sale_items', 'sale_items.sale_id = sales.id', 'left')

            ->where('sales.sale_status !=', 'pending');
            //->where('sales.date BETWEEN ' . $start . ' and ' . $end);
        if ($start) {
            $this->db->where('date >=', $start);
        }
        if ($end) {
            $this->db->where('date <=', $end);
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

 public function getTotalExpensesForProfitandLess($start = null, $end = null, $warehouse_id = null)
    {
        $this->db->select('count(id) as total,sum(CASE WHEN other =0 THEN (amount)  END)  as total_amount, sum(CASE WHEN other =1 THEN (amount)  END) as other_expenses', false);
            //->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($start) {
            $this->db->where('date >=', $start);
        }
        if ($end) {
            $this->db->where('date <=', $end);
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

    public function getTotalRevenues($start = null, $end = null, $warehouse_id = null)
    {
        $this->db->select('count(id) as total, sum(COALESCE(amount, 0)) as total_amount', false);
         //   ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($start) {
            $this->db->where('date >=', $start);
        }
        if ($end) {
            $this->db->where('date <=', $end);
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


      public function getTChartDataTrends()
    {
        $myQuery = "SELECT S.month,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(total_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S
            LEFT JOIN ( SELECT  date_format(date, '%Y-%m') Month,
                        SUM(total_tax) ptax,
                        SUM(order_tax) otax,
                        SUM(total - total_discount) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
                        GROUP BY date_format(date, '%Y-%m')) P
            ON S.Month = P.Month
            ORDER BY S.Month";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPChartDataTrends()
    {
        $myQuery = "SELECT P.month,
        COALESCE(P.purchases, 0) as purchases
		FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Purchases,
                SUM(total_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('purchases') . "
                WHERE date >= date_sub( now( ) , INTERVAL 11 MONTH )
                GROUP BY date_format(date, '%Y-%m')) P

            ORDER BY P.Month";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getInventoryValueChartData()
    {


			$myQuery ="select ROUND(sum(wp.quantity),2) as value ,cat.name as name from " . $this->db->dbprefix('warehouses_products') . " wp," . $this->db->dbprefix('categories') . " cat," . $this->db->dbprefix('products') . " p where p.category_id=cat.id and p.id=wp.product_id group by cat.name";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {

                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getReturns($date=null, $warehouse_id = null, $year = null, $month = null)
    {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('sum(grand_total )  as return_sales,count(id)  as returne_amount', false)
        ->where('sale_status', 'returned');
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year . '-' . $month . '-01 00:00:00');
            $this->db->where('date <=', $year . '-' . $month . '-' . $last_day . ' 23:59:59');
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

	public function getMonthlySales($year, $warehouse_id = null, $customer_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( paid, 0 ) ) AS paid, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        if ($customer_id) {
            $myQuery .= " customer_id = {$customer_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getMonthlyPurchases($year, $warehouse_id = null, $supplier_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( paid, 0 ) ) AS paid, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
		if ($supplier_id) {
            $myQuery .= " supplier_id = {$supplier_id} AND ";
        }

        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPurchasesReturns($date=null, $warehouse_id = null, $year = null, $month = null)
    {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('sum(grand_total )  as return_purchase,count(id)  as returne_amount', false)
        ->where('status', 'returned');
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year . '-' . $month . '-01 00:00:00');
            $this->db->where('date <=', $year . '-' . $month . '-' . $last_day . ' 23:59:59');
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

    public function getPurchasesReturnsV2($start = null, $end = null, $warehouse_id = null)
    {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('sum(grand_total )  as return_purchase,count(id)  as returne_amount', false)
        ->where('status', 'returned');
        if ($start) {
            $this->db->where('date >=', $start);
        }
        if ($end) {
            $this->db->where('date <=', $end);
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

    public function getAllSales($start = null, $end = null, $warehouse_id = null)
    {
        $this->db->select('count(id) as total,sum(grand_total * currency_rate)  as grand_total,sum((paid * currency_rate)+(discount_allowance * currency_rate))  as paid, (sum(grand_total * currency_rate)-sum((paid * currency_rate)+(discount_allowance * currency_rate))) as due', false);
           // ->where('sale_status !=', 'pending');



           // ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($start) {
            $this->db->where('date >=', $start);
        }
        if ($end) {
            $this->db->where('date <=', $end);
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

    public function getReturnsV2($start = null, $end = null, $warehouse_id = null)
    {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('sum(grand_total )  as return_sales,count(id)  as returne_amount', false)
        ->where('sale_status', 'returned');
        if ($start) {
            $this->db->where('date >=', $start);
        }
        if ($end) {
            $this->db->where('date <=', $end);
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

	public function getAllPurchases($start = null, $end = null, $warehouse_id = null)
    {
        $this->db->select('count(id) as total,sum(grand_total * currency_rate)  as grand_total,sum((paid * currency_rate)+(discount_earned * currency_rate))  as paid, (sum(grand_total * currency_rate)-sum((paid * currency_rate)+(discount_earned * currency_rate))) as due, sum(return_purchase_total * currency_rate)  as return_purchase_total ', false);
           // ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($start) {
            $this->db->where('date >=', $start);
        }
        if ($end) {
            $this->db->where('date <=', $end);
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

        public function getTotalReturnSales($start, $end, $warehouse_id = null)
    {
        $this->db->select('sum(grand_total) as total_amount', false);
           // ->where();
         //   ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('returns');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;


    }
	    public function getSalesLocationChart()
    {
        if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
        {
            $myQuery ="select sum(CASE WHEN s.quantity >=0 THEN (s.quantity * s.net_unit_price)  END)  as sales_value ,sum(CASE WHEN s.quantity <0 THEN (s.quantity * s.net_unit_price)  END)  as rsales_value, w.name  as name from " . $this->db->dbprefix('cars_showrooms') . " w," . $this->db->dbprefix('sale_items') . " s where s.warehouse_id=w.id  group by w.name";
        }else{
            $myQuery ="select sum(CASE WHEN s.quantity >=0 THEN (s.quantity * s.net_unit_price * sl.currency_rate)  END)  as sales_value ,sum(CASE WHEN s.quantity <0 THEN (s.quantity * s.net_unit_price * sl.currency_rate)  END)  as rsales_value, w.name  as name from " . $this->db->dbprefix('warehouses') . " w," . $this->db->dbprefix('sale_items') . " s," . $this->db->dbprefix('sales') . " sl where s.warehouse_id=w.id and sl.warehouse_id=w.id and sl.id=s.sale_id  group by w.name";
        }
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSalesLocationChartV2($start = null, $end = null, $warehouse_id = null)
    {


            if ($warehouse_id) {
                $myQuery ="select sum(CASE WHEN s.quantity >=0 THEN (s.quantity * s.net_unit_price * sl.currency_rate)  END)  as sales_value ,sum(CASE WHEN s.quantity <0 THEN (s.quantity * s.net_unit_price * sl.currency_rate) ELSE 0  END)  as rsales_value, w.name  as name from " . $this->db->dbprefix('warehouses') . " w," . $this->db->dbprefix('sale_items') . " s," . $this->db->dbprefix('sales') . " sl where s.warehouse_id=w.id and sl.warehouse_id=w.id and sl.id=s.sale_id and sl.date BETWEEN '".$start."' and '".$end."' and sl.warehouse_id=".$warehouse_id." group by w.name";
            }else{
                $myQuery ="select sum(CASE WHEN s.quantity >=0 THEN (s.quantity * s.net_unit_price * sl.currency_rate)  END)  as sales_value ,sum(CASE WHEN s.quantity <0 THEN (s.quantity * s.net_unit_price * sl.currency_rate) ELSE 0  END)  as rsales_value, w.name  as name from " . $this->db->dbprefix('warehouses') . " w," . $this->db->dbprefix('sale_items') . " s," . $this->db->dbprefix('sales') . " sl where s.warehouse_id=w.id and sl.warehouse_id=w.id and sl.id=s.sale_id and sl.date BETWEEN '".$start."' and '".$end."' group by w.name";
            }

        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


	    public function getPurchasesLocationChart()
    {
        if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
        {
            $myQuery ="select sum(CASE WHEN s.quantity >=0 THEN (s.quantity_received * s.net_unit_cost)  END)  as purchases_value ,sum(CASE WHEN s.quantity <0 THEN (s.quantity * s.net_unit_cost)  END)  as rpurchases_value, w.name  as name from " . $this->db->dbprefix('cars_showrooms') . " w," . $this->db->dbprefix('purchase_items') . " s where s.warehouse_id=w.id  group by w.name";
        }else{
            $myQuery ="select sum(CASE WHEN s.quantity >=0 THEN (s.quantity_received * s.net_unit_cost * p.currency_rate) END) as purchases_value ,sum(CASE WHEN s.quantity <0 THEN (s.quantity * s.net_unit_cost * p.currency_rate) END) as rpurchases_value, w.name as name from " . $this->db->dbprefix('warehouses') . " w," . $this->db->dbprefix('purchase_items') . " s," . $this->db->dbprefix('purchases') . " p where s.warehouse_id=w.id and p.warehouse_id=w.id and p.id=s.purchase_id group by w.name";
        }
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }



    public function getChartData_old()
    {
        $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE( P.purchases, 0 ) as purchases,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE(S.tax2, 0) as tax2,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(product_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S
            LEFT JOIN ( SELECT  date_format(date, '%Y-%m') Month,
                        SUM(product_tax) ptax,
                        SUM(order_tax) otax,
                        SUM(total) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
                        GROUP BY date_format(date, '%Y-%m')) P
            ON S.Month = P.Month
            ORDER BY S.Month";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getLastestQuotes()
    {
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->order_by('id', 'desc');
        $q = $this->db->get('quotes', 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLatestCustomers()
    {
        $this->db->order_by('id', 'desc');
        $q = $this->db->get_where('companies', ['group_name' => 'customer'], 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLatestPurchases()
    {
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->order_by('id', 'desc');
        $q = $this->db->get('purchases', 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLatestSales()
    {
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->order_by('id', 'desc');
        $q = $this->db->get('sales', 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLatestSuppliers()
    {
        $this->db->order_by('id', 'desc');
        $q = $this->db->get_where('companies', ['group_name' => 'supplier'], 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getLatestTransfers()
    {
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $this->db->order_by('id', 'desc');
        $q = $this->db->get('transfers', 5);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getStockValue()
    {
        $q = $this->db->query('SELECT SUM(qty*price) as stock_by_price, SUM(qty*cost) as stock_by_cost
        FROM (
            Select sum(COALESCE(' . $this->db->dbprefix('warehouses_products') . '.quantity, 0)) as qty, price, cost
            FROM ' . $this->db->dbprefix('products') . '
            JOIN ' . $this->db->dbprefix('warehouses_products') . ' ON ' . $this->db->dbprefix('warehouses_products') . '.product_id=' . $this->db->dbprefix('products') . '.id
            GROUP BY ' . $this->db->dbprefix('warehouses_products') . '.id ) a');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getAllOrdersCount()
    {
        $this->db->select('count(*) as total');
        $this->db->from('sma_sales');
        // $this->db->where('status', 'completed');
        $q = $this->db->get();
        return  $q->row()->total;

    }
    public function getTotalSalesValue(){
        $this->db->select('sum(total) as total');
        $this->db->from('sma_sales');
        $this->db->where('sale_status', 'completed');
        $q = $this->db->get();
        return  $q->row()->total;
    }
    public function getTotalProducts(){

        $this->db->select('count(*) as total');
        $this->db->from('sma_products');
        $q = $this->db->get();
        return  $q->row()->total;
    }

    public function getCurrentMonthSalesFromShop(){
        $this->db->select('sum(total) as total');
        $this->db->from('sma_sales');
        $this->db->where('sale_status', 'completed');
        $this->db->where('date >=', date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00');
        $this->db->where('date <', date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59');
       $this->db->where('shop', '1');
        $q = $this->db->get();
        return  $q->row()->total;
    }

    public function getCurrentMonthSalesFromPOS(){
        $this->db->select('sum(total) as total');
        $this->db->from('sma_sales');
        $this->db->where('sale_status', 'completed');
        $this->db->where('date >=', date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00');
        $this->db->where('date <', date('Y-m-d', strtotime('last day of this month')) . ' 23:59:59');
       $this->db->where('pos', '1');
        $q = $this->db->get();
        return  $q->row()->total;
    }


    public function getBestSellerchartV2($start_date = null, $end_date = null, $warehouse_id = null)
    {

        if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

        $this->db
            ->select('product_name, product_code')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('quantity >', 0)
            ->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"')
            ->group_by('product_name, product_code')
            ->order_by('sum(quantity)', 'desc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getUnBestSellerchartV2($start_date = null, $end_date = null, $warehouse_id = null)
    {

        if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

        $this->db
            ->select('product_name, product_code')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('quantity >', 0)
            ->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"')
            ->group_by('product_name, product_code')
            ->order_by('sum(quantity)', 'asc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBestCustomerschartV2($start_date = null, $end_date = null, $warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

        $this->db
            ->select('customer')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('quantity >', 0)
            ->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"')
            ->group_by('customer')
            ->order_by('sum(quantity)', 'desc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getUnBestCustomerschartV2($start_date = null, $end_date = null, $warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

        $this->db
            ->select('customer')
            ->select_sum('quantity')
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('quantity >', 0)
            ->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"')
            ->group_by('customer')
            ->order_by('sum(quantity)', 'asc')
            ->limit(5);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getDashboardAlertsCounts($warehouse_id = null)
    {
        $wh_id = $warehouse_id ? (int)$warehouse_id : ($this->session->userdata('warehouse_id') && !$this->Owner && !$this->Admin ? (int)$this->session->userdata('warehouse_id') : null);

        // 1. Overdue Sales Invoices
        $this->db->from('sales')
                 ->where('payment_status !=', 'paid')
                 ->where('due_date IS NOT NULL')
                 ->where('due_date !=', '0000-00-00')
                 ->where('due_date <', date('Y-m-d'))
                 ->where('sale_status !=', 'returned');
        if ($wh_id) {
            $this->db->where('warehouse_id', $wh_id);
        }
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $overdue_invoices = $this->db->count_all_results();

        // 2. Overdue Supplier Payables
        $this->db->from('purchases')
                 ->where('payment_status !=', 'paid')
                 ->where('due_date IS NOT NULL')
                 ->where('due_date !=', '0000-00-00')
                 ->where('due_date <', date('Y-m-d'))
                 ->where('status !=', 'returned');
        if ($wh_id) {
            $this->db->where('warehouse_id', $wh_id);
        }
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $overdue_payables = $this->db->count_all_results();

        // 3. Pending Purchases
        $this->db->from('purchases')->where('status', 'pending');
        if ($wh_id) {
            $this->db->where('warehouse_id', $wh_id);
        }
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $pending_orders = $this->db->count_all_results();

        // 4. Pending Quotes
        $this->db->from('quotes')->where('status', 'pending');
        if ($wh_id) {
            $this->db->where('warehouse_id', $wh_id);
        }
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $pending_quotes = $this->db->count_all_results();

        /*// 5. Critical / Low Stock Products
        if ($wh_id) {
            $this->db->from('products p')
                     ->join('warehouses_products wp', 'wp.product_id = p.id AND wp.warehouse_id = ' . $wh_id, 'inner')
                     ->where('p.alert_quantity >', 0)
                     ->where('wp.quantity > 0')
                     ->where('wp.quantity <= p.alert_quantity', null, false)
                     ->where('p.track_quantity', 1);
            $critical_stock = $this->db->count_all_results();

            $this->db->from('products p')
                     ->join('warehouses_products wp', 'wp.product_id = p.id AND wp.warehouse_id = ' . $wh_id, 'inner')
                     ->where('wp.quantity <= 0', null, false)
                     ->where('p.track_quantity', 1);
            $out_of_stock = $this->db->count_all_results();
        } else {
            $this->db->from('products')
                     ->where('alert_quantity >', 0)
                     ->where('quantity > 0')
                     ->where('quantity <= alert_quantity', null, false)
                     ->where('track_quantity', 1);
            $critical_stock = $this->db->count_all_results();

            $this->db->from('products')
                     ->where('quantity <= 0', null, false)
                     ->where('track_quantity', 1);
            $out_of_stock = $this->db->count_all_results();
        }*/

        // 6. Unposted GL Entries
        //$this->db->from('gl_entries')->where('entryStatus', 0);
        //$unposted_entries = $this->db->count_all_results();

        // 7. Today's Tasks
        $today_start = date('Y-m-d 00:00:00');
        $today_end   = date('Y-m-d 23:59:59');
        $this->db->from('calendar')
                 ->where('start <=', $today_end)
                 ->group_start()
                 ->where('end >=', $today_start)
                 ->or_where('end IS NULL', null, false)
                 ->or_where('end', '0000-00-00 00:00:00')
                 ->or_where('end', '')
                 ->group_end();
        if ($this->Settings->restrict_calendar && !$this->Owner && !$this->Admin) {
            $this->db->where('user_id', $this->session->userdata('user_id'));
        }
        $today_tasks = $this->db->count_all_results();

        return (object)[
            'overdue_invoices' => $overdue_invoices,
            'overdue_payables' => $overdue_payables,
            'pending_orders'   => $pending_orders,
            'pending_quotes'   => $pending_quotes,
            'critical_stock'   => $critical_stock,
            'out_of_stock'     => $out_of_stock,
            'unposted_entries' => $unposted_entries,
            'today_tasks'      => $today_tasks,
        ];
    }

    public function getDepartmentStatsCounts($warehouse_id = null, $start_date = null, $end_date = null)
    {
        $wh_id = $warehouse_id ? (int)$warehouse_id : ($this->session->userdata('warehouse_id') && !$this->Owner && !$this->Admin ? (int)$this->session->userdata('warehouse_id') : null);

        // 1. Sales Invoices
        $this->db->from('sales');
        if ($wh_id) {
            $this->db->where('warehouse_id', $wh_id);
        }
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $sales_count = $this->db->count_all_results();

        // 2. Purchase Invoices
        $this->db->from('purchases');
        if ($wh_id) {
            $this->db->where('warehouse_id', $wh_id);
        }
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        if ($this->Settings->restrict_user && !$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
        $purchases_count = $this->db->count_all_results();

        // 3. Products
        if ($wh_id) {
            $this->db->from('warehouses_products')->where('warehouse_id', $wh_id);
            $products_count = $this->db->count_all_results();
        } else {
            $products_count = $this->db->count_all_results('products');
        }

        // 4. Customers
        $customers_count = $this->db->where('group_name', 'customer')->count_all_results('companies');

        // 5. Active Users
        $users_count = $this->db->where('active', 1)->count_all_results('users');

        // 6. Suppliers
        $suppliers_count = $this->db->where('group_name', 'supplier')->count_all_results('companies');

        return (object)[
            'sales'     => $sales_count,
            'purchases' => $purchases_count,
            'products'  => $products_count,
            'customers' => $customers_count,
            'users'     => $users_count,
            'suppliers' => $suppliers_count,
        ];
    }

    public function getRecentPayments($limit = 8, $warehouse_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select("
            p.id, p.date, p.reference_no, p.amount, p.paid_by, p.type,
            COALESCE(s.reference_no, pur.reference_no, '') as invoice_ref,
            COALESCE(s.customer, pur.supplier, '') as party_name
        ", false);
        $this->db->from('payments p');
        $this->db->join('sales s', 's.id = p.sale_id', 'left');
        $this->db->join('purchases pur', 'pur.id = p.purchase_id', 'left');
        if ($warehouse_id) {
            $this->db->group_start();
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
            $this->db->or_where('pur.warehouse_id', (int)$warehouse_id);
            $this->db->group_end();
        }
        if ($start_date) {
            $this->db->where('p.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('p.date <=', $end_date);
        }
        $this->db->order_by('p.date', 'DESC');
        $this->db->order_by('p.id', 'DESC');
        $this->db->limit((int)$limit);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : [];
    }

    public function getWarehouseComparisonStats($start_date = null, $end_date = null)
    {
        $warehouses = $this->db->get('warehouses')->result();
        if (empty($warehouses)) {
            return [];
        }

        $comparison = [];
        foreach ($warehouses as $wh) {
            // Stock Qty & Value
            $this->db->select("
                COALESCE(SUM(wp.quantity), 0) as total_qty,
                COALESCE(SUM(wp.quantity * p.cost), 0) as total_cost,
                COALESCE(SUM(wp.quantity * p.price), 0) as total_value
            ", false);
            $this->db->from('warehouses_products wp');
            $this->db->join('products p', 'p.id = wp.product_id', 'inner');
            $this->db->where('wp.warehouse_id', $wh->id);
            $stock_row = $this->db->get()->row();

            // Sales in period
            $this->db->select("
                COUNT(*) as sales_count,
                COALESCE(SUM(grand_total), 0) as sales_total
            ", false);
            $this->db->from('sales');
            $this->db->where('warehouse_id', $wh->id);
            if ($start_date) {
                $this->db->where('date >=', $start_date);
            }
            if ($end_date) {
                $this->db->where('date <=', $end_date);
            }
            $sales_row = $this->db->get()->row();

            // Purchases in period
            $this->db->select("
                COUNT(*) as purchases_count,
                COALESCE(SUM(grand_total), 0) as purchases_total
            ", false);
            $this->db->from('purchases');
            $this->db->where('warehouse_id', $wh->id);
            if ($start_date) {
                $this->db->where('date >=', $start_date);
            }
            if ($end_date) {
                $this->db->where('date <=', $end_date);
            }
            $purchases_row = $this->db->get()->row();

            $comparison[] = (object)[
                'id'              => $wh->id,
                'code'            => $wh->code,
                'name'            => $wh->name,
                'stock_qty'       => (float)($stock_row->total_qty ?? 0),
                'stock_cost'      => (float)($stock_row->total_cost ?? 0),
                'stock_value'     => (float)($stock_row->total_value ?? 0),
                'sales_count'     => (int)($sales_row->sales_count ?? 0),
                'sales_total'     => (float)($sales_row->sales_total ?? 0),
                'purchases_count' => (int)($purchases_row->purchases_count ?? 0),
                'purchases_total' => (float)($purchases_row->purchases_total ?? 0),
            ];
        }

        return $comparison;
    }

    public function getStockMovementSummary($warehouse_id = null, $start_date = null, $end_date = null)
    {
        // 1. Purchases items (Stock In)
        $this->db->select("COALESCE(SUM(pi.quantity), 0) as purchased_qty", false);
        $this->db->from('purchase_items pi');
        $this->db->join('purchases p', 'p.id = pi.purchase_id', 'inner');
        if ($warehouse_id) {
            $this->db->where('p.warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('p.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('p.date <=', $end_date);
        }
        $pur_qty = (float)($this->db->get()->row()->purchased_qty ?? 0);

        // 2. Sales items (Stock Out)
        $this->db->select("COALESCE(SUM(si.quantity), 0) as sold_qty", false);
        $this->db->from('sale_items si');
        $this->db->join('sales s', 's.id = si.sale_id', 'inner');
        if ($warehouse_id) {
            $this->db->where('s.warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('s.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('s.date <=', $end_date);
        }
        $sold_qty = (float)($this->db->get()->row()->sold_qty ?? 0);

        // 3. Sales Returns (Stock In)
        $this->db->select("COALESCE(SUM(CASE WHEN return_sale_total IS NOT NULL THEN 1 ELSE 0 END), 0) as return_sales_count", false);
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
        $ret_sales = (int)($this->db->get()->row()->return_sales_count ?? 0);

        // 4. Purchases Returns (Stock Out)
        $this->db->select("COALESCE(SUM(CASE WHEN return_purchase_total IS NOT NULL THEN 1 ELSE 0 END), 0) as return_purchases_count", false);
        $this->db->from('purchases');
        if ($warehouse_id) {
            $this->db->where('warehouse_id', (int)$warehouse_id);
        }
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $ret_purchases = (int)($this->db->get()->row()->return_purchases_count ?? 0);

        // 5. Transfers In & Out
        $this->db->select("COALESCE(SUM(grand_total), 0) as transfers_total, COUNT(*) as transfers_count", false);
        $this->db->from('transfers');
        if ($warehouse_id) {
            $this->db->group_start();
            $this->db->where('from_warehouse_id', (int)$warehouse_id);
            $this->db->or_where('to_warehouse_id', (int)$warehouse_id);
            $this->db->group_end();
        }
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $trans_row = $this->db->get()->row();

        return (object)[
            'purchased_qty'    => $pur_qty,
            'sold_qty'         => $sold_qty,
            'sales_returns'    => $ret_sales,
            'purchase_returns' => $ret_purchases,
            'transfers_count'  => (int)($trans_row->transfers_count ?? 0),
            'transfers_total'  => (float)($trans_row->transfers_total ?? 0),
        ];
    }
}

