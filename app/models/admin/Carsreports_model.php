<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Carsreports_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBestSeller($start_date, $end_date, $warehouse_id = null)
    {
        $this->db
            ->select('product_name, product_code')->select_sum('quantity')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('date >=', $start_date)->where('date <=', $end_date)
            ->group_by('product_name, product_code')->order_by('sum(quantity)', 'desc')->limit(10);
        if ($warehouse_id) {
            $this->db->where('sale_items.warehouse_id', $warehouse_id);
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

    // public function getmonthlyPurchases()
    // {
    //     $myQuery = "SELECT (CASE WHEN date_format( date, '%b' ) Is Null THEN 0 ELSE date_format( date, '%b' ) END) as month, SUM( COALESCE( total, 0 ) ) AS purchases FROM purchases WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH ) GROUP BY date_format( date, '%b' ) ORDER BY date_format( date, '%m' ) ASC";
    //     $q = $this->db->query($myQuery);
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as $row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return FALSE;
    // }

    public function getChartData()
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

    /*public function getCosting($date, $warehouse_id = null, $year = null, $month = null)
    {
        $this->db->select('SUM( COALESCE( purchase_unit_cost, 0 ) * quantity ) AS cost, SUM( COALESCE( sale_unit_price, 0 ) * quantity ) AS sales, SUM( COALESCE( purchase_net_unit_cost, 0 ) * quantity ) AS net_cost, SUM( COALESCE( sale_net_unit_price, 0 ) * quantity ) AS net_sales', false);
        if ($date) {
            $this->db->where('costing.date', $date);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('costing.date >=', $year . '-' . $month . '-01 00:00:00');
            $this->db->where('costing.date <=', $year . '-' . $month . '-' . $last_day . ' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->join('sales', 'sales.id=costing.sale_id')
            ->where('sales.warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('costing');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }*/


	    public function getCosting($date, $warehouse_id = null, $year = null, $month = null)
    {
        $this->db->select('SUM( COALESCE( net_unit_price * unit_quantity, 0 ) ) AS total ,SUM( COALESCE( item_cost_value, 0 ) ) AS ctotal, SUM( COALESCE( total_discount, 0 ) ) AS dtotal', false)

            ->join('sale_items', 'sale_items.sale_id= sales.id', 'left');


		if ($date) {
            $this->db->where('date>', $date);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('sales.date >=', $year . '-' . $month . '-01 00:00:00');
            $this->db->where('sales.date <=', $year . '-' . $month . '-' . $last_day . ' 23:59:59');
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


    public function getCustomerOpenReturns($customer_id)
    {
        $this->db->from('returns')->where('customer_id', $customer_id);
        return $this->db->count_all_results();
    }

    public function getCustomerQuotes($customer_id)
    {
        $this->db->from('quotes')->where('customer_id', $customer_id);
        return $this->db->count_all_results();
    }

    public function getCustomerReturns($customer_id)
    {
        return $this->getCustomerSaleReturns($customer_id) + $this->getCustomerOpenReturns($customer_id);
    }

    public function getCustomerSaleReturns($customer_id)
    {
        $this->db->from('sales')->where('customer_id', $customer_id)->where('sale_status', 'returned');
        return $this->db->count_all_results();
    }

    public function getCustomerSales($customer_id)
    {
        $this->db->from('sales')->where('customer_id', $customer_id);
        return $this->db->count_all_results();
    }

    public function getDailyPurchases($year, $month, $warehouse_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getDailySales($year, $month, $warehouse_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total-total_discount, 0 ) ) AS totalBVAT, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
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

    public function getExpenses($date, $warehouse_id = null, $year = null, $month = null)
    {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', false);
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

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
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

	public function getMonthlySalesItems($year, $warehouse_id = null, $customer_id = null)
    {



        $myQuery1 = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( shipping, 0 ) ) AS shipping, SUM( COALESCE( grand_total, 0 ) ) AS grand_total
            FROM " . $this->db->dbprefix('sales') . '  WHERE ';
        if ($warehouse_id) {
            $myQuery1 .= " warehouse_id = {$warehouse_id} AND ";
        }
        if ($customer_id) {
            $myQuery1 .= " customer_id = {$customer_id} AND ";
        }
        $myQuery1 .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q1 = $this->db->query($myQuery1, false);
        if ($q1->num_rows() > 0) {
            foreach (($q1->result()) as $row) {
                $data1[] = $row;
            }
		}
            //return $data1[0]->shipping;










	 $myQuery = "SELECT DATE_FORMAT( s.date,  '%c' ) AS date, SUM( COALESCE( si.item_tax, 0 ) ) AS tax1,  SUM( si.net_unit_price * si.unit_quantity ) AS total, SUM( COALESCE( si.item_discount, 0 ) ) AS discount
            FROM " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id WHERE ';
        if ($warehouse_id) {
            $myQuery .= " s.warehouse_id = {$warehouse_id} AND ";
        }
        if ($customer_id) {
            $myQuery .= " s.customer_id = {$customer_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( s.date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( s.date, '%c' ) ORDER BY date_format( s.date, '%c' ) ASC";
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

    public function getOrderDiscount($date, $warehouse_id = null, $year = null, $month = null)
    {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('SUM( COALESCE( order_discount, 0 ) ) AS order_discount', false);
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

    public function getPOSSetting()
    {
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductNames($term, $limit = 5)
    {
        $this->db->select('vehicle_id as id, chesis_no as code, name')
            ->like('name', $term, 'both')->or_like('chesis_no', $term, 'both');
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

    public function getPurchasesTax($start_date = null, $end_date = null, $warehouse_id = null)
    {
        $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')->select_sum('total_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid');
		if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPurchasesTotals($supplier_id)
    {
        $this->db->select('SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', false)
            ->where('supplier_id', $supplier_id);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getReturns($date, $warehouse_id = null, $year = null, $month = null)
    {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total', false)
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

    public function getSalesTax($start_date = null, $end_date = null,$warehouse_id = null)
    {
        $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')->select_sum('total_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid');
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSalesTotals($customer_id)
    {
        $this->db->select('SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', false)
            ->where('customer_id', $customer_id);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getStaff()
    {
        if ($this->Admin) {
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

    public function getStaffDailyPurchases($user_id, $year, $month, $warehouse_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getStaffDailySales($user_id, $year, $month, $warehouse_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getStaffMonthlyPurchases($user_id, $year, $warehouse_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y' ) =  '{$year}'
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

    public function getStaffMonthlySales($user_id, $year, $warehouse_id = null)
    {
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( total_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . ' WHERE ';
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y' ) =  '{$year}'
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

    public function getStaffPurchases($user_id)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', false)
            ->where('created_by', $user_id);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getStaffSales($user_id)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', false)
            ->where('created_by', $user_id);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getStockValue()
    {
        $q = $this->db->query('SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select COALESCE(sum(' . $this->db->dbprefix('warehouses_products') . '.quantity), 0)*price as by_price, COALESCE(sum(' . $this->db->dbprefix('warehouses_products') . '.quantity), 0)*cost as by_cost FROM ' . $this->db->dbprefix('products') . ' JOIN ' . $this->db->dbprefix('warehouses_products') . ' ON ' . $this->db->dbprefix('warehouses_products') . '.product_id=' . $this->db->dbprefix('products') . '.id GROUP BY ' . $this->db->dbprefix('products') . '.id )a');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }



    public function getSupplierPurchases($supplier_id)
    {
        $this->db->from('purchases')->where('supplier_id', $supplier_id);
        return $this->db->count_all_results();
    }

    public function getTotalExpenses($start, $end, $warehouse_id = null)
    {
        $this->db->select('count(id) as total, sum(COALESCE(amount, 0)) as total_amount', false)
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('expenses');
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

    public function getTotalPaidAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', false)
            ->where('type', 'sent')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalPurchases($start, $end, $warehouse_id = null)
    {

        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax, SUM(COALESCE(discount_earned, 0)) as discount_earned', false)
            ->where('status !=', 'pending');


        /*if ($start) {
			$this->db->where('date BETWEEN ' . $start . ' and ' . $end);

		}*/
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

    public function getTotalReceivedAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', false)
            ->where('type', 'received')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalReceivedCashAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', false)
            ->where('type', 'received')->where('paid_by', 'cash')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalReceivedCCAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', false)
            ->where('type', 'received')->where('paid_by', 'CC')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalReceivedChequeAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', false)
            ->where('type', 'received')->where('paid_by', 'Cheque')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalReceivedPPPAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', false)
            ->where('type', 'received')->where('paid_by', 'ppp')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalReceivedStripeAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', false)
            ->where('type', 'received')->where('paid_by', 'stripe')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalReturnedAmount($start, $end)
    {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', false)
            ->where('type', 'returned')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalReturnSales($start, $end, $warehouse_id = null)
    {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', false)
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('returns');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalSales($start, $end, $warehouse_id = null)
    {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', false)
            ->where('sale_status !=', 'pending')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTotalSalesForProfitandLess($start = null, $end = null, $warehouse_id = null)
    {
        $this->db->select('count(id) as total,sum(CASE WHEN total >=0 THEN (total)  END)  as total_amount,sum(CASE WHEN total <0 THEN (total)  END)  as rtotal_amount,sum(CASE WHEN total <0 THEN (shipping)  END)  as other_expenses,sum(CASE WHEN total >=0 THEN (shipping)  END)  as other_revenues, SUM(COALESCE(total_discount, 0)) as total_discount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(discount_allowance, 0)) as discount_allowance, SUM(COALESCE(total_tax, 0)) as tax', false)
            ->where('sale_status !=', 'pending');
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

    public function getWarehouseStockValue($id)
    {
        $q = $this->db->query('SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select sum(COALESCE(' . $this->db->dbprefix('warehouses_products') . '.quantity, 0))*price as by_price, sum(COALESCE(' . $this->db->dbprefix('warehouses_products') . '.quantity, 0))*cost as by_cost FROM ' . $this->db->dbprefix('products') . ' JOIN ' . $this->db->dbprefix('warehouses_products') . ' ON ' . $this->db->dbprefix('warehouses_products') . '.product_id=' . $this->db->dbprefix('products') . '.id WHERE ' . $this->db->dbprefix('warehouses_products') . '.warehouse_id = ? GROUP BY ' . $this->db->dbprefix('products') . '.id )a', [$id]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getWarehouseTotals($warehouse_id = null)
    {
        $this->db->select('sum(quantity) as total_quantity, count(id) as total_items', false);
        $this->db->where('quantity !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSalesTaxLocalNT($start_date = null, $end_date = null, $warehouse_id = null, $sale_status = null)
    {
        $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')
            ->join('companies', 'companies.id = sales.customer_id', 'left')
			->join('tax_rates', 'tax_rates.id = sales.order_tax_id', 'left');
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.code =', 'NT');
			$this->db->where('product_tax =', 0);

			//$this->db->where('sale_status =', 'completed');
			if($sale_status)
				$this->db->where('sales.sale_status =', $sale_status);
			else
				$this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");

			//$this->db->where(" (sale_status='completed' OR sale_status='returned') ");

			//$this->db->where('paid !=', 0);
			if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

            if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSalesTaxLocalZero($start_date = null, $end_date = null, $warehouse_id = null, $sale_status = null)
    {
        $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')
            ->join('companies', 'companies.id = sales.customer_id', 'left')
			->join('tax_rates', 'tax_rates.id = sales.order_tax_id', 'left');
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.code =', '0');
            //$this->db->where('sale_status =', 'completed');
			if($sale_status)
				$this->db->where('sales.sale_status =', $sale_status);
			else
				$this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");
		    //$this->db->where(" (sale_status='completed' OR sale_status='returned') ");

			//$this->db->where('paid !=', 0);
		if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

			if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSalesTaxLocalBasic($start_date = null, $end_date = null)
    {
        /*$this->db->select_sum('sales.igst')->select_sum('sales.cgst')->select_sum('sales.sgst')
            ->select_sum('sales.product_tax')->select_sum('sales.order_tax')->select_sum('sales.total_tax')
            ->select_sum('sales.grand_total')->select_sum('sales.total')->select_sum('sales.paid')
            */
			$this->db->select('sum(net_unit_price * quantity) as total')->select('sum(item_tax) as total_tax')
			->join('sale_items', 'sale_items.sale_id = sales.id', 'left')
			->join('companies', 'companies.id = sales.customer_id', 'left')
			->join('tax_rates', 'tax_rates.id = sales.order_tax_id OR tax_rates.id = sale_items.tax_rate_id', 'left');
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.rate >', 0);
            //$this->db->where('sales.sale_status =', 'completed');
			$this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");
			//$this->db->where('sales.paid !=', 0);
			if ($start_date) {
            $this->db->where('sales.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('sales.date <=', $end_date);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSalesTaxInternational($start_date = null, $end_date = null, $warehouse_id = null, $sale_status = null)
    {
        $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')
            ->join('companies', 'companies.id = sales.customer_id', 'left');
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 2);
            //$this->db->where('sale_status =', 'completed');
			if($sale_status)
				$this->db->where('sales.sale_status =', $sale_status);
			else
				$this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");

			//$this->db->where(" (sale_status='completed' OR sale_status='returned') ");
			//$this->db->where('paid !=', 0);
				if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }


			if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }



    public function getPurchasesTaxLocalNT($start_date = null, $end_date = null, $warehouse_id = null)
    {


		$qExpensesTotal=0;
		$qExpensesTotalTax=0;
		$this->db->select_sum('expenses.total_tax')
            ->select_sum('expenses.grand_total')->select_sum('expenses.total')->select_sum('expenses.paid');
            $this->db->where('expenses.total_tax =', 0);
           //$this->db->where('expenses.paid !=', 0);
		   if ($warehouse_id) {
            $this->db->where('expenses.warehouse_id', $warehouse_id);
        }



		if ($start_date && $end_date) {
               $this->db->where('expenses.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $qExpenses = $this->db->get('expenses');
        if ($qExpenses->num_rows() > 0) {
            //return $q->row();
			$qExpensesTotal=$qExpenses->row()->total;
        	$qExpensesTotalTax=$qExpenses->row()->total_tax;
        }

		$this->db->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')//->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.code =', 'NT');
            $this->db->where('purchases.total_tax =', 0);
			$this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
			//$this->db->where('purchases.paid !=', 0);
			//$this->db->where('purchase_items.tax_rate_id is null OR purchases.order_tax_id = purchase_items.tax_rate_id');

		   if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
        	$q->row()->total=$q->row()->total - $this->getPurchasesTaxLocalZero($start_date,$end_date,'aaa')->total;//$qExpensesTotal=$qExpenses->row()->total;
        	//$qExpensesTotalTax=$qExpenses->row()->total_tax;
            return $q->row();
        }
        return false;
    }


	public function getPurchasesTaxLocalNTReturned($start_date = null, $end_date = null, $warehouse_id = null)
    {



		$this->db->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')->select('' . $this->db->dbprefix('purchases') . '.purchase_id')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.code =', 'NT');
            $this->db->where('purchases.total_tax =', 0);
			$this->db->where('purchases.status =', 'returned');

			$this->db->group_by('purchases.purchase_id');

		   if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
        	     foreach (($q->result()) as $row) {

        $data[] = $row;

                }
			return $data;
			//return $q->row();
        }
        return false;
    }


    public function getPurchasesTaxLocalZero($start_date = null, $end_date = null, $tax_rate_id = null, $warehouse_id = null)
    {
        /*$this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')
           */
		   $this->db->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');

		   // ->join('companies', 'companies.id = purchases.supplier_id', 'left')
			//->join('tax_rates', 'tax_rates.id = purchases.order_tax_id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.code =', '0');
			$this->db->where('purchases.total_tax =', 0);

            $this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");

			//$this->db->where('purchases.paid !=', 0);
		   if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        if ($tax_rate_id)
		{
            $this->db->where('purchase_items.tax_rate_id is not null');
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getPurchasesTaxLocalZeroReturned($start_date = null, $end_date = null, $warehouse_id = null)
    {

		$this->db->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')->select('' . $this->db->dbprefix('purchases') . '.purchase_id')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');

		   // ->join('companies', 'companies.id = purchases.supplier_id', 'left')
			//->join('tax_rates', 'tax_rates.id = purchases.order_tax_id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.code =', '0');
			$this->db->where('purchases.total_tax =', 0);
			$this->db->where('purchases.status =', 'returned');

			$this->db->group_by('purchases.purchase_id');
			if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }

			if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {

        $data[] = $row;

                }
			return $data;
		}
        return false;
    }


    public function getPurchasesTaxLocalBasic($start_date = null, $end_date = null)
    {

		$qExpensesTotal=0;
		$qExpensesTotalTax=0;
		$this->db->select('tax_rates.rate')->select_sum('expenses.total_tax')
            ->select_sum('expenses.grand_total')->select_sum('expenses.total')->select_sum('expenses.paid')
            ->join('tax_rates', 'tax_rates.id = expenses.order_tax_id ', 'left');
			$this->db->where('tax_rates.rate >', 0)->group_by('tax_rates.rate');
           //$this->db->where('expenses.paid !=', 0);



		if ($start_date && $end_date) {
               $this->db->where('expenses.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $qExpenses = $this->db->get('expenses');
        if ($qExpenses->num_rows() > 0) {
            //return $q->row();
			$qExpensesTotal=$qExpenses->row()->total;
        	$qExpensesTotalTax=$qExpenses->row()->total_tax;
        }


		/*$this->db->select_sum('purchases.igst')->select_sum('purchases.cgst')->select_sum('purchases.sgst')
            ->select_sum('purchases.product_tax')->select_sum('purchases.order_tax')->select_sum('purchases.total_tax')
            ->select_sum('purchases.grand_total')->select_sum('purchases.total')->select_sum('purchases.paid')->select_sum('purchase_items.subtotal')->select_sum('purchases.total_discount')
            */
			$this->db->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.rate >', 0);
           $this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
		   //$this->db->where('purchases.paid !=', 0);

		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
			$q->row()->total=$q->row()->total+$qExpensesTotal;
            $q->row()->total_tax=$q->row()->total_tax+$qExpensesTotalTax;
            return $q->row();
        }else if ($qExpenses->num_rows() > 0) {
			return $qExpenses->row();
		}


		return false;
    }



	public function getExpensesTaxLocalBasic($start_date = null, $end_date = null)
    {

	$this->db->select_sum('expenses.total_tax')
            ->select_sum('expenses.grand_total')->select_sum('expenses.total')->select_sum('expenses.paid')
            ->join('tax_rates', 'tax_rates.id = expenses.order_tax_id ', 'left');
			$this->db->where('tax_rates.rate >', 0);
           //$this->db->where('expenses.paid !=', 0);
		if ($start_date && $end_date) {
               $this->db->where('expenses.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }

		return false;
    }


    public function getPurchasesTaxInternationalBasic($start_date = null, $end_date = null, $warehouse_id = null)
    {

		     $customs_fees=0;
             $Other_fees=0;

		 $this->db->distinct();
		 $this->db->select('purchase_items.purchase_id')->select('purchases.customs_fees')->select('purchases.Other_fees')

			//$this->db->select('sum(net_unit_cost * unit_quantity) as total')->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')->select('SUM(customs_fees) as customs_fees')->select('SUM(Other_fees) as Other_fees')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			//->join('tax_rates', 'tax_rates.id = purchases.order_tax_id', 'left');
			$this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate >', 0);
           $this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
		   //$this->db->where('purchases.paid !=', 0);
		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }



		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }


        $q_invoice = $this->db->get('purchases');
        if ($q_invoice->num_rows() > 0) {
			foreach (($q_invoice->result()) as $row) {
                $data[] = $row;
				$customs_fees=$customs_fees+$row->customs_fees;
				$Other_fees=$Other_fees+$row->Other_fees;

                }

        }
		//return $q_invoice->row();

	   /*$this->db->select_sum('purchases.igst')->select_sum('purchases.cgst')->select_sum('purchases.sgst')
            ->select_sum('purchases.product_tax')->select_sum('purchases.order_tax')->select_sum('purchases.total_tax')
            ->select_sum('purchases.grand_total')->select_sum('purchases.total')->select_sum('purchases.paid')->select_sum('purchases.customs_fees')->select_sum('purchases.Other_fees')
            */

			$this->db->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')->select('SUM(customs_fees) as customs_fees')->select('SUM(Other_fees) as Other_fees')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate >', 0);
           $this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
		   //$this->db->where('purchases.paid !=', 0);

          if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }



		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
			 $q->row()->customs_fees=$customs_fees;
             $q->row()->Other_fees=$Other_fees;

            return $q->row();
        }
        return false;
    }

    public function getPurchasesTaxInternationalZero($start_date = null, $end_date = null, $warehouse_id = null)
    {
         $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')

			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate =', 0);
            $this->db->where('product_tax =', 0);
			//$this->db->where('paid !=', 0);
			$this->db->where(" (status='partial' OR status='received' OR status='returned') ");
		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPurchasesTaxInternationalZeroReturned($start_date = null, $end_date = null, $warehouse_id = null)
    {
         $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')->select('' . $this->db->dbprefix('purchases') . '.purchase_id')

			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate =', 0);
            $this->db->where('product_tax =', 0);
			//$this->db->where('paid !=', 0);
			//$this->db->where(" (status='partial' OR status='received' OR status='returned') ");
				$this->db->where('purchases.status =', 'returned');
   		 $this->db->group_by('purchases.purchase_id');

		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTaxRatesDefault()
    {
        $this->db->select('id')->select('name')->select('code')->select('rate');
		$this->db->where('rate >', 0);
        $this->db->where('tax_default =', 1);
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
        $this->db->select('id')->select('name')->select('code')->select('rate');
		$this->db->where('rate >', 0)->order_by('id', 'desc')->limit(1);
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getSalesTaxLocalBasicDetails($start_date = null, $end_date = null, $warehouse_id = null, $sale_status = null, $rate = null)
    {
		$this->db->select('tax_rates.rate')->select('sum(net_unit_price * unit_quantity) as total')->select('sum(item_tax) as total_tax')
			 ->join('sale_items', 'sale_items.sale_id = sales.id', 'left')
			->join('companies', 'companies.id = sales.customer_id', 'left')
			->join('tax_rates', 'tax_rates.id = sale_items.tax_rate_id', 'left');
			//->join('tax_rates', 'tax_rates.id = sales.order_tax_id OR tax_rates.id = sale_items.tax_rate_id', 'left')
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.rate >', 0)->group_by('tax_rates.rate');
            if($rate>0)
				$this->db->where('tax_rates.rate =', $rate);
			if($sale_status){
				$this->db->where('sales.sale_status =', $sale_status);
				if($sale_status=='returned' && $start_date && $end_date){
					 //$this->db->where($this->getSaleByReference($this->db->dbprefix('sales'). '.return_sale_ref')->date .' BETWEEN "' . $start_date . '" and "' . $end_date . '"');

				//$this->db->where('sales.return_sale_ref !=', $sale_status);
				//$this->db->where(" (sales.return_sale_ref !='completed' OR sales.sale_status='returned') ");

				}

			}else{
				$this->db->where(" (sales.sale_status='completed' OR sales.sale_status='returned') ");
		    }
			//$this->db->where('sales.paid !=', 0);
			if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

			if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

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


	 public function getSalesTaxLocalBasicDetailsReturned($start_date = null, $end_date = null, $warehouse_id = null, $rate = null)
    {

		$this->db->select('tax_rates.rate')->select('sum(net_unit_price * unit_quantity) as total')->select('sum(item_tax) as total_tax')->select('' . $this->db->dbprefix('sales') . '.sale_id')
			 ->join('sale_items', 'sale_items.sale_id = sales.id', 'left')
			->join('companies', 'companies.id = sales.customer_id', 'left')
			->join('tax_rates', 'tax_rates.id = sale_items.tax_rate_id', 'left');
			//->join('tax_rates', 'tax_rates.id = sales.order_tax_id OR tax_rates.id = sale_items.tax_rate_id', 'left')
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.rate >', 0)
			->group_by('sales.sale_id');
            if($rate>0)
				$this->db->where('tax_rates.rate =', $rate);
				$this->db->where('sales.sale_status =', 'returned');

			//$this->db->where('sales.paid !=', 0);
			if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

			if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

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

	public function getSalesTaxLocalZeroReturned($start_date = null, $end_date = null, $warehouse_id = null)
    {

		$this->db->select('sum(net_unit_price * unit_quantity) as total')->select('sum(item_tax) as total_tax')->select('' . $this->db->dbprefix('sales') . '.sale_id')
			 ->join('sale_items', 'sale_items.sale_id = sales.id', 'left')
			->join('companies', 'companies.id = sales.customer_id', 'left')
			->join('tax_rates', 'tax_rates.id = sale_items.tax_rate_id', 'left');
			//->join('tax_rates', 'tax_rates.id = sales.order_tax_id OR tax_rates.id = sale_items.tax_rate_id', 'left')
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.code =', '0');
            	$this->db->where('sales.sale_status =', 'returned');

			$this->db->group_by('sales.sale_id');
			if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

			if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

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

	public function getSalesTaxLocalNTReturned($start_date = null, $end_date = null, $warehouse_id = null)
    {

		$this->db->select('sum(net_unit_price * unit_quantity) as total')->select('sum(item_tax) as total_tax')->select('' . $this->db->dbprefix('sales') . '.sale_id')
			 ->join('sale_items', 'sale_items.sale_id = sales.id', 'left')
			->join('companies', 'companies.id = sales.customer_id', 'left')
			->join('tax_rates', 'tax_rates.id = sale_items.tax_rate_id', 'left');
			//->join('tax_rates', 'tax_rates.id = sales.order_tax_id OR tax_rates.id = sale_items.tax_rate_id', 'left')
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.code =', 'NT');
			$this->db->where('product_tax =', 0);
				$this->db->where('sales.sale_status =', 'returned');
			$this->db->group_by('sales.sale_id');

			//$this->db->where('sales.paid !=', 0);
			if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

			if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

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


    public function getSalesTaxInternationalReturned($start_date = null, $end_date = null, $warehouse_id = null)
    {

		$this->db->select('sum(net_unit_price * unit_quantity) as total')->select('sum(item_tax) as total_tax')->select('' . $this->db->dbprefix('sales') . '.sale_id')
			 ->join('sale_items', 'sale_items.sale_id = sales.id', 'left')
			->join('companies', 'companies.id = sales.customer_id', 'left');
			//->join('tax_rates', 'tax_rates.id = sales.order_tax_id OR tax_rates.id = sale_items.tax_rate_id', 'left')
			//->join('payments', 'payments.sale_id = sales.id', 'left');
			$this->db->where('companies.co_type =', 2);
            	$this->db->where('sales.sale_status =', 'returned');

			//$this->db->where('sales.paid !=', 0);
			$this->db->group_by('sales.sale_id');

			if ($warehouse_id) {
            $this->db->where('sales.warehouse_id', $warehouse_id);
        }

			if ($start_date && $end_date) {
            //$this->db->where('sales.date >=', $start_date);
			    $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

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

	public function getPurchasesTaxLocalBasicDetails($start_date = null, $end_date = null, $warehouse_id = null)
    {

		$qExpensesTotal=0;
		$qExpensesTotalTax=0;
		$this->db->select('tax_rates.rate')->select_sum('expenses.total_tax')
            ->select_sum('expenses.grand_total')->select_sum('expenses.total')->select_sum('expenses.paid')
            ->join('tax_rates', 'tax_rates.id = expenses.order_tax_id ', 'left');
			$this->db->where('tax_rates.rate >', 0)->group_by('tax_rates.rate');
           //$this->db->where('expenses.paid !=', 0);
		     if ($warehouse_id) {
            $this->db->where('expenses.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('expenses.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $qExpenses = $this->db->get('expenses');
        if ($qExpenses->num_rows() > 0) {
            //return $q->row();
			$qExpensesTotal=$qExpenses->row()->total;
        	$qExpensesTotalTax=$qExpenses->row()->total_tax;
        }




		/*$this->db->select('tax_rates.rate')->select_sum('purchases.igst')->select_sum('purchases.cgst')->select_sum('purchases.sgst')
            ->select_sum('purchases.product_tax')->select_sum('purchases.order_tax')->select_sum('purchases.total_tax')
            ->select_sum('purchases.grand_total')->select_sum('purchases.total')->select_sum('purchases.paid')->select_sum('purchase_items.subtotal')
            */
			$this->db->select('tax_rates.rate')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('companies.co_type =', 1);
            $this->db->where('tax_rates.rate >', 0)->group_by('tax_rates.rate');
            $this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
			//$this->db->where('purchases.paid !=', 0);
		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            $q->row()->total=$q->row()->total+$qExpensesTotal;
            $q->row()->total_tax=$q->row()->total_tax+$qExpensesTotalTax;
			foreach (($q->result()) as $row) {
                $data[] = $row;
                }
            return $data;
		}else if ($qExpenses->num_rows() > 0) {
			foreach (($qExpenses->result()) as $row) {
                $data[] = $row;
                }
            return $data;
		}

        return false;
    }

	public function getPurchasesTaxLocalBasicDetailsReturned($start_date = null, $end_date = null, $warehouse_id = null, $rate = null)
    {






		/*$this->db->select('tax_rates.rate')->select_sum('purchases.igst')->select_sum('purchases.cgst')->select_sum('purchases.sgst')
            ->select_sum('purchases.product_tax')->select_sum('purchases.order_tax')->select_sum('purchases.total_tax')
            ->select_sum('purchases.grand_total')->select_sum('purchases.total')->select_sum('purchases.paid')->select_sum('purchase_items.subtotal')
            */
			$this->db->select('tax_rates.rate')->select('sum(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('sum(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')->select('' . $this->db->dbprefix('purchases') . '.purchase_id')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('companies.co_type =', 1);
			$this->db->where('tax_rates.rate >', 0);
			if($rate>0)
				$this->db->where('tax_rates.rate =', $rate);
				$this->db->where('purchases.status =', 'returned');

            $this->db->group_by('purchases.purchase_id');
            //$this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
			//$this->db->where('purchases.paid !=', 0);
		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
                }
            return $data;
		}
        return false;
    }


    public function getExpensesTaxLocalBasicDetails($start_date = null, $end_date = null)
    {
        $this->db->select('tax_rates.rate')->select_sum('expenses.total_tax')
            ->select_sum('expenses.grand_total')->select_sum('expenses.total')->select_sum('expenses.paid')
            ->join('tax_rates', 'tax_rates.id = expenses.order_tax_id', 'left');
			$this->db->where('tax_rates.rate >', 0)->group_by('tax_rates.rate');
		if ($start_date && $end_date) {
               $this->db->where('expenses.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
                }
            return $data;
		}
        return false;
    }

    public function getPurchasesTaxInternationalBasicDetails($start_date = null, $end_date = null, $warehouse_id = null)
    {

		     $customs_fees=0;
             $Other_fees=0;

		 $this->db->distinct();
		 $this->db->select('purchase_items.purchase_id')->select('purchases.customs_fees')->select('purchases.Other_fees')

			//$this->db->select('sum(net_unit_cost * unit_quantity) as total')->select('sum(net_unit_cost * unit_quantity * rate /100) as total_tax')->select('SUM(customs_fees) as customs_fees')->select('SUM(Other_fees) as Other_fees')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			//->join('tax_rates', 'tax_rates.id = purchases.order_tax_id', 'left');
			$this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate >', 0);
           $this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
		   //$this->db->where('purchases.paid !=', 0);
		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }




		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q_invoice = $this->db->get('purchases');
        if ($q_invoice->num_rows() > 0) {
			foreach (($q_invoice->result()) as $row) {
                $customs_fees=$customs_fees+$row->customs_fees;
				$Other_fees=$Other_fees+$row->Other_fees;

                }

        }
		//return $q_invoice->row();




			$this->db->select('rate')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')->select('SUM(customs_fees) as customs_fees')->select('SUM(Other_fees) as Other_fees')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');

			$this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate >', 0)->group_by('tax_rates.rate');
          $this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
		  //$this->db->where('purchases.paid !=', 0);

		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }
        	  if ($start_date) {
            $this->db->where('purchases.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('purchases.date <=', $end_date);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
			 $q->row()->customs_fees=$customs_fees;
             $q->row()->Other_fees=$Other_fees;

            foreach (($q->result()) as $row) {
                $data[] = $row;

                }
            return $data;
		}
        return false;
    }

    public function getPurchasesTaxInternationalBasicDetailsReturned($start_date = null, $end_date = null, $warehouse_id = null)
    {



			$this->db->select('rate')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received) ELSE (net_unit_cost * unit_quantity) END) as total')->select('SUM(CASE WHEN quantity_received IS NOT NULL THEN (net_unit_cost * quantity_received * rate /100) ELSE (net_unit_cost * unit_quantity * rate /100) END) as total_tax')->select('SUM(customs_fees) as customs_fees')->select('SUM(Other_fees) as Other_fees')->select('' . $this->db->dbprefix('purchases') . '.purchase_id')
			->join('purchase_items', 'purchase_items.purchase_id = purchases.id', 'left')
			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id OR tax_rates.id = purchase_items.tax_rate_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');

			$this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate >', 0);
         $this->db->where('purchases.status =', 'returned');
		 $this->db->group_by('purchases.purchase_id');

		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }
        	  if ($start_date) {
            $this->db->where('purchases.date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('purchases.date <=', $end_date);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
			 //$q->row()->customs_fees=$customs_fees;
             //$q->row()->Other_fees=$Other_fees;

            foreach (($q->result()) as $row) {
                $data[] = $row;

                }
            return $data;
		}
        return false;
    }

     public function getPurchasesTaxInternationalZeroDetails($start_date = null, $end_date = null, $warehouse_id = null)
    {
         $this->db->select('tax_active_rate')->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')

			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('tax_active_rate >', 0);
            $this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate =', 0)->group_by('tax_active_rate');
            $this->db->where('product_tax =', 0);
			$this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
			//$this->db->where('paid !=', 0);
		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
                }
            return $data;
		}
        return false;
    }

	 public function getPurchasesTaxInternationalZeroDetailsReturned($start_date = null, $end_date = null, $warehouse_id = null,$tax_active_rate = null)
    {
         $this->db->select('tax_active_rate')->select_sum('igst')->select_sum('cgst')->select_sum('sgst')->select('' . $this->db->dbprefix('purchases') . '.purchase_id')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('total')->select_sum('paid')

			->join('companies', 'companies.id = purchases.supplier_id', 'left')
			->join('tax_rates', 'tax_rates.id = purchases.order_tax_id', 'left');
			//->join('payments', 'payments.purchase_id = purchases.id', 'left');
			$this->db->where('tax_active_rate =', $tax_active_rate);
            $this->db->where('companies.co_type =', 2);
            $this->db->where('tax_rates.rate =', 0);
            $this->db->where('product_tax =', 0);
			//$this->db->where(" (purchases.status='partial' OR purchases.status='received' OR purchases.status='returned') ");
			//$this->db->where('paid !=', 0);
			$this->db->where('purchases.status =', 'returned');
   		 $this->db->group_by('purchases.purchase_id');

		  if ($warehouse_id) {
            $this->db->where('purchases.warehouse_id', $warehouse_id);
        }


		if ($start_date && $end_date) {
               $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');

        }

        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
                }
            return $data;
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






}
