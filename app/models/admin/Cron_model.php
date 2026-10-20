<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cron_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->admin_load('cron');
    }

    public function getSettings()
    {
        $q = $this->db->get_where('settings', ['setting_id' => 1], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function run_cron()
    {
        $m = [];
        if ($this->resetOrderRef()) {
            $m[] = lang('order_ref_updated');
        }
        if ($pendingInvoices = $this->getAllPendingInvoices()) {
            $p = 0;
            foreach ($pendingInvoices as $invoice) {
                $this->updateInvoiceStatus($invoice->id);
                $p++;
            }
            $m[] = sprintf(lang('x_pending_to_due'), $p);
        }
        if ($partialInvoices = $this->getAllPPInvoices()) {
            $pp = 0;
            foreach ($partialInvoices as $invoice) {
                $this->updateInvoiceStatus($invoice->id);
                $pp++;
            }
            $m[] = sprintf(lang('x_partial_to_due'), $pp);
        }
        if ($unpaidpurchases = $this->getUnpaidPuchases()) {
            $up = 0;
            foreach ($unpaidpurchases as $purchase) {
                $this->db->update('purchases', ['payment_status' => 'due'], ['id' => $purchase->id]);
                $up++;
            }
            $m[] = sprintf(lang('x_purchases_changed'), $up);
        }
        if ($pis = $this->get_expired_products()) {
            $e  = 0;
            $ep = 0;
            foreach ($pis as $pi) {
                $this->db->update('purchase_items', ['quantity_balance' => 0], ['id' => $pi->id]);
                $e++;
                $ep += $pi->quantity_balance;
            }
            $this->site->syncQuantity(null, null, $pis);
            $m[] = sprintf(lang('x_products_expired'), $e, $ep);
        }
        if ($promos = $this->getPromoProducts()) {
            $pro = 0;
            foreach ($promos as $pr) {
                $this->db->update('products', ['promotion' => 0], ['id' => $pr->id]);
                $pro++;
            }
            $m[] = sprintf(lang('x_promotions_expired'), $pro);
        }
        $date = date('Y-m-d H:i:s', strtotime('-1 month'));
        if ($this->deleteUserLgoins($date)) {
            $m[] = sprintf(lang('user_login_deleted'), $date);
        }
        if ($this->db_backup()) {
            $m[] = lang('backup_done');
        }
        if ($this->checkUpdate()) {
            $m[] = lang('update_available');
        }
        $r = !empty($m) ? $m : false;
        $this->send_email($r);
        $this->db->truncate('sessions');
        return $r;
    }

    public function send_email($details)
    {
        if ($details)
        {
            $table_html = '';
            $date       = date('Y-m-d', strtotime('-1 day'));
            $sdate      = $date . ' 00:00:00';
            $edate      = $date . ' 23:59:59';

            $tables     = $this->zatca_yesterday_report($sdate,$edate);

            foreach ($tables as $table) {
                $table_html .= $table . '<div style="clear:both"></div>';
            }
            foreach ($details as $detail) {
                $table_html = $table_html . $detail;
            }
            $msg_with_yesterday_report = $table_html;
            //$owners                    = $this->db->get_where('users', ['group_id' => 1])->result();
            $owners                    = $this->db->get_where('users', ['id' => 7])->result();
            $this->load->library('email');
            $config['useragent'] = 'CPE';
            $config['protocol']  = $this->Settings->protocol;
            $config['mailtype']  = 'html';
            $config['crlf']      = "\r\n";
            $config['newline']   = "\r\n";
            if ($this->Settings->protocol == 'sendmail') {
                $config['mailpath'] = $this->Settings->mailpath;
            } elseif ($this->Settings->protocol == 'smtp') {
                $config['smtp_host'] = $this->Settings->smtp_host;
                $config['smtp_user'] = $this->Settings->smtp_user;
                $config['smtp_pass'] = $this->Settings->smtp_pass;
                $config['smtp_port'] = $this->Settings->smtp_port;
                if (!empty($this->Settings->smtp_crypto)) {
                    $config['smtp_crypto'] = $this->Settings->smtp_crypto;
                }
            }
            $this->email->initialize($config);

            foreach ($owners as $owner) {
                $owner->email='alhajw688@gmail.com';
                list($user, $domain) = explode('@', $owner->email);
                if ($domain != 'tecdiary.com') {
                    $this->load->library('parser');
                    $parse_data = [
                        'name'      => $owner->first_name . ' ' . $owner->last_name,
                        'email'     => $owner->email,
                        'msg'       => $msg_with_yesterday_report,
                        'site_link' => base_url(),
                        'site_name' => $this->Settings->site_name,
                        'logo'      => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>',
                    ];
                    $msg     = file_get_contents('./themes/' . $this->Settings->theme . '/admin/views/email_templates/cron.html');
                    $message = $this->parser->parse_string($msg, $parse_data);
                    $subject = lang('cron_job') . ' - ' . $this->Settings->site_name;

                    $this->email->from($this->Settings->default_email, $this->Settings->site_name);
                    $this->email->to($owner->email);
                    $this->email->subject($subject);
                    $this->email->message($message);
                    $this->email->send();
                }
            }
        }
    }

    private function checkUpdate()
    {
        $fields = ['version' => $this->Settings->version, 'code' => $this->Settings->purchase_code, 'username' => $this->Settings->envato_username, 'site' => base_url()];
        $this->load->helper('update');
        $protocol = is_https() ? 'https://' : 'http://';
        $updates  = get_remote_contents($protocol . 'tecdiary.com/api/v1/update/', $fields);
        $response = json_decode($updates);
        if (!empty($response->data->updates)) {
            $this->db->update('settings', ['update' => 1], ['setting_id' => 1]);
            return true;
        }
        return false;
    }

    private function db_backup()
    {
        $this->load->dbutil();
        $prefs = [
            'format'   => 'txt',
            'filename' => 'sma_db_backup.sql',
        ];
        $back    = $this->dbutil->backup($prefs);
        $backup  = &$back;
        $db_name = 'db-backup-on-' . date('Y-m-d-H-i-s') . '.txt';
        $save    = './files/backups/' . $db_name;
        $this->load->helper('file');
        write_file($save, $backup);

        $files = glob('./files/backups/*.txt', GLOB_BRACE);
        $now   = time();
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * 30) {
                    unlink($file);
                }
            }
        }

        return true;
    }

    private function deleteUserLgoins($date)
    {
        $this->db->where('time <', $date);
        if ($this->db->delete('user_logins')) {
            return true;
        }
        return false;
    }

    private function gen_html($costing, $discount, $expenses, $returns, $purchases, $sales, $warehouse = null)
    {
        $html = '<div style="border:1px solid #DDD; padding:10px; margin:10px 0;"><h3>' . ($warehouse ? $warehouse->name . ' (' . $warehouse->code . ')' : lang('all_warehouses')) . '</h3>
        <table width="100%" class="stable">
        <tr>
            <td style="border-bottom: 1px solid #EEE;">' . lang('products_sale') . '</td>
            <td style="text-align:right; border-bottom: 1px solid #EEE;">' . $this->sma->formatMoney($costing->sales) . '</td>
        </tr>';
        if ($discount && $discount->order_discount > 0) {
            $html .= '
            <tr>
                <td style="border-bottom: 1px solid #DDD;">' . lang('order_discount') . '</td>
                <td style="text-align:right;border-bottom: 1px solid #DDD;">' . $this->sma->formatMoney($discount->order_discount) . '</td>
            </tr>';
        }
        $html .= '
        <tr>
            <td style="border-bottom: 1px solid #EEE;">' . lang('products_cost') . '</td>
            <td style="text-align:right; border-bottom: 1px solid #EEE;">' . $this->sma->formatMoney($costing->cost) . '</td>
        </tr>';
        if ($expenses && $expenses->total > 0) {
            $html .= '
            <tr>
                <td style="border-bottom: 1px solid #DDD;">' . lang('expenses') . '</td>
                <td style="text-align:right;border-bottom: 1px solid #DDD;">' . $this->sma->formatMoney($expenses->total) . '</td>
            </tr>';
        }
        $html .= '
        <tr>
            <td width="300px;" style="border-bottom: 1px solid #DDD;"><strong>' . lang('profit') . '</strong></td>
            <td style="text-align:right;border-bottom: 1px solid #DDD;">
                <strong>' . $this->sma->formatMoney($costing->sales - $costing->cost - ($discount ? $discount->order_discount : 0) - ($expenses ? $expenses->total : 0)) . '</strong>
            </td>
        </tr>';
        if (isset($returns->total)) {
            $html .= '
            <tr>
                <td width="300px;" style="border-bottom: 2px solid #DDD;"><strong>' . lang('return_sales') . '</strong></td>
                <td style="text-align:right;border-bottom: 2px solid #DDD;"><strong>' . $this->sma->formatMoney($returns->total) . '</strong></td>
            </tr>';
        }
        $html .= '</table><h4 style="margin-top:15px;">' . lang('general_ledger') . '</h4>
        <table width="100%" class="stable">';
        if ($sales) {
            $html .= '
            <tr>
                <td width="33%" style="border-bottom: 1px solid #DDD;">' . lang('total_sales') . ': <strong>' . $this->sma->formatMoney($sales->total_amount) . '(' . $sales->total . ')</strong></td>
                <td width="33%" style="border-bottom: 1px solid #DDD;">' . lang('received') . ': <strong>' . $this->sma->formatMoney($sales->paid) . '</strong></td>
                <td width="33%" style="border-bottom: 1px solid #DDD;">' . lang('taxes') . ': <strong>' . $this->sma->formatMoney($sales->tax) . '</strong></td>
            </tr>';
        }
        if ($purchases) {
            $html .= '
            <tr>
                <td width="33%">' . lang('total_purchases') . ': <strong>' . $this->sma->formatMoney($purchases->total_amount) . '(' . $purchases->total . ')</strong></td>
                <td width="33%">' . lang('paid') . ': <strong>' . $this->sma->formatMoney($purchases->paid) . '</strong></td>
                <td width="33%">' . lang('taxes') . ': <strong>' . $this->sma->formatMoney($purchases->tax) . '</strong></td>
            </tr>';
        }
        $html .= '</table></div>';
        return $html;
    }

    private function get_expired_products()
    {
        if ($this->Settings->remove_expired) {
            $date = date('Y-m-d');
            $this->db->where('expiry <=', $date)->where('expiry !=', null)->where('expiry !=', '0000-00-00')->where('quantity_balance >', 0);
            $q = $this->db->get('purchase_items');
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                return $data;
            }
        }
        return false;
    }

    private function getAllPendingInvoices()
    {
        $today    = date('Y-m-d');
        $paid     = $this->lang->line('paid');
        $canceled = $this->lang->line('cancelled');
        $q        = $this->db->get_where('sales', ['due_date <=' => $today, 'due_date !=' => '1970-01-01', 'due_date !=' => null, 'payment_status' => 'pending']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    private function getAllPPInvoices()
    {
        $today    = date('Y-m-d');
        $paid     = $this->lang->line('paid');
        $canceled = $this->lang->line('cancelled');
        $q        = $this->db->get_where('sales', ['due_date <=' => $today, 'due_date !=' => '1970-01-01', 'due_date !=' => null, 'payment_status' => 'partial']);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    private function getCosting($date, $warehouse_id = null)
    {
        $this->db->select('SUM( COALESCE( purchase_unit_cost, 0 ) * quantity ) AS cost, SUM( COALESCE( sale_unit_price, 0 ) * quantity ) AS sales, SUM( COALESCE( purchase_net_unit_cost, 0 ) * quantity ) AS net_cost, SUM( COALESCE( sale_net_unit_price, 0 ) * quantity ) AS net_sales', false);
        $this->db->where('costing.date', $date);
        if ($warehouse_id) {
            $this->db->join('sales', 'sales.id=costing.sale_id')
            ->where('sales.warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('costing');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    private function getExpenses($sdate, $edate, $warehouse_id = null)
    {
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', false);
        $this->db->where('date >=', $sdate)->where('date <=', $edate);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    private function getOrderDiscount($sdate, $edate, $warehouse_id = null)
    {
        $this->db->select('SUM( COALESCE( order_discount, 0 ) ) AS order_discount', false);
        $this->db->where('date >=', $sdate)->where('date <=', $edate);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    private function getOrderRef()
    {
        $q = $this->db->get_where('order_ref', ['ref_id' => 1], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    private function getPromoProducts()
    {
        $today = date('Y-m-d');
        $q     = $this->db->get_where('products', ['promotion' => 1, 'end_date !=' => null, 'end_date <=' => $today]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    private function getReturns($sdate, $edate, $warehouse_id = null)
    {
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total', false)
        ->where('sale_status', 'returned');
        $this->db->where('date >=', $sdate)->where('date <=', $edate);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    private function getTotalPurchases($sdate, $edate, $warehouse_id = null)
    {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', false)
            ->where('status !=', 'pending')
            ->where('date >=', $sdate)->where('date <=', $edate);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    private function getTotalSales($sdate, $edate, $warehouse_id = null)
    {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', false)
            ->where('sale_status !=', 'pending')
            ->where('date >=', $sdate)->where('date <=', $edate);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    private function getUnpaidPuchases()
    {
        $today = date('Y-m-d');
        $q     = $this->db->get_where('purchases', ['payment_status !=' => 'paid', 'payment_status !=' => 'due', 'payment_term >' => 0, 'due_date !=' => null, 'due_date <=' => $today]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    private function resetOrderRef()
    {
        if ($this->Settings->reference_format == 1 || $this->Settings->reference_format == 2) {
            $month = date('Y-m') . '-01';
            $year  = date('Y') . '-01-01';
            if ($ref = $this->getOrderRef()) {
                $reset_ref = ['so' => 1, 'qu' => 1, 'po' => 1, 'to' => 1, 'pos' => 1, 'do' => 1, 'pay' => 1, 'ppay' => 1, 're' => 1, 'rep' => 1, 'ex' => 1, 'qa' => 1];
                if ($this->Settings->reference_format == 1 && strtotime($ref->date) < strtotime($year)) {
                    $reset_ref['date'] = $year;
                    $this->db->update('order_ref', $reset_ref, ['ref_id' => 1]);
                    return true;
                } elseif ($this->Settings->reference_format == 2 && strtotime($ref->date) < strtotime($month)) {
                    $reset_ref['date'] = $month;
                    $this->db->update('order_ref', $reset_ref, ['ref_id' => 1]);
                    return true;
                }
            }
        }
        return false;
    }

    private function updateInvoiceStatus($id)
    {
        if ($this->db->update('sales', ['payment_status' => 'due'], ['id' => $id])) {
            return true;
        }
        return false;
    }

    private function yesterday_report()
    {
        $date       = date('Y-m-d', strtotime('-1 day'));
        $sdate      = $date . ' 00:00:00';
        $edate      = $date . ' 23:59:59';
        $warehouses = $this->db->get('warehouses')->result();
        foreach ($warehouses as $warehouse) {
            $costing         = $this->getCosting($date, $warehouse->id);
            $discount        = $this->getOrderDiscount($sdate, $edate, $warehouse->id);
            $expenses        = $this->getExpenses($sdate, $edate, $warehouse->id);
            $returns         = $this->getReturns($sdate, $edate, $warehouse->id);
            $total_purchases = $this->getTotalPurchases($sdate, $edate, $warehouse->id);
            $total_sales     = $this->getTotalSales($sdate, $edate, $warehouse->id);
            $html[]          = $this->gen_html($costing, $discount, $expenses, $returns, $total_purchases, $total_sales, $warehouse);
        }

        $costing         = $this->getCosting($date);
        $discount        = $this->getOrderDiscount($sdate, $edate);
        $expenses        = $this->getExpenses($sdate, $edate);
        $returns         = $this->getReturns($sdate, $edate);
        $total_purchases = $this->getTotalPurchases($sdate, $edate);
        $total_sales     = $this->getTotalSales($sdate, $edate);
        $html[]          = $this->gen_html($costing, $discount, $expenses, $returns, $total_purchases, $total_sales);

        return $html;
    }

    public function run_cronV2()
    {
        $m = [];

        $date       = date('Y-m-d', strtotime('-1 day'));
        $sdate      = $date . ' 00:00:00';
        $edate      = $date . ' 23:59:59';
        if ($zatcaInvoices = $this->getAllZatcaInvoices($sdate,$edate)) {
            //$this->sma->print_arrays($InvoiceTypeNumber);
            $p = 0;
            foreach ($zatcaInvoices as $invoice) {
                $this->generate_xml($invoice->id);
                $p++;
            }
            $m[] = sprintf(lang('x_completing_to_cleared'), $p);
        }




        $r = !empty($m) ? $m : false;
        //$this->send_email($r);
        return $r;
    }



    private function getAllZatcaInvoices($sdate, $edate, $warehouse_id = null)
    {
        $this->db->where('synch', 0)->where(" (sale_status =  'completed' OR sale_status =  'returned') ")->where(" (date  between  '".$sdate."' and '".$edate."') ");//->where('date >=', $sdate)->where('date <=', $edate);
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

    private function zatca_yesterday_report($sdate, $edate, $warehouse_id = null)
    {
        $this->db->where('synch', 1)->where(" (sale_status =  'completed' OR sale_status =  'returned') ")->where('date >=', $sdate)->where('date <=', $edate);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->order_by('id', 'asc');

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            $html[]          = $this->gen_htmlV2($data);

            return $html;
        }
        return false;
    }

    private function generate_xml($id)
    {

		$inv         = $this->getInvoiceByID($id);
        $customer    = $this->site->getCompanyByID($inv->customer_id);
        $units       = $this->site->getAllUnits();
        $biller      = $this->site->getCompanyByID($inv->biller_id);
        $created_by  = $this->site->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $warehouse   = $this->site->getWarehouseByID($inv->warehouse_id);
        $inv         = $inv;
        $rows        = $this->getAllInvoiceItems($id);
        $return_sale = null;//$inv->return_id ? $this->getInvoiceByID($inv->return_id) : null;
        $return_rows = null;//$inv->return_id ? $this->getAllInvoiceItems($inv->return_id) : null;
		$payments        = $this->getInvoicePayments($inv->id);
		$return_payments = $inv->return_id ? $this->getInvoicePayments($inv->return_id) : null;
        $active_tax        =$this->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->getInvoicePayments($inv->return_id) : null;

		$dbName=str_replace("cpesoft_","",$this->db->database);
		$dateTime=str_replace('-',"",$inv->date);
		$dateTime=str_replace(':',"",$dateTime);
		$dateTime=str_replace(' ',"T",$dateTime);
		//	$dateTime = new DateTime($inv->date);

	//$dateTime= $dateTime->format('Y-m-d\TH:i:s\Z');


		$xml_file_path=$dbName.'_'.$warehouse->vat_reg_no.'_'.$id;

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->Settings->site_name;

        if($invPre         = $this->getLastInvoiceSynch()){
            $stamp_hash=$invPre->stamp_hash;
        }else{
            $stamp_hash=null;
        }

		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
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

    private function getInvoiceByID($id)
    {
        $q = $this->db->get_where('sales', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    private function getAllInvoiceItems($sale_id, $return_id = null)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, products.cf1 as cf1, products.cf2 as cf2, products.cf3 as cf3, products.cf4 as cf4, products.cf5 as cf5, products.cf6 as cf6, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name, products.unit as base_unit_id, units.code as base_unit_code')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->join('units', 'units.id=products.unit', 'left')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');
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

    private function getInvoicePayments($sale_id)
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

    private function getTaxRatesActive()
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

    private function updateSalesXMLFilePathById($id,$xml_file_path)
    {
            $this->db->update('sales', ['xml_file_path' => $xml_file_path], ['id' => $id]);

        return true;
    }

    private function digital_signature($id)
    {

		$inv         = $this->getInvoiceByID($id);
        $customer    = $this->site->getCompanyByID($inv->customer_id);
        $units       = $this->site->getAllUnits();
        $zatcaConfig       = $this->site->getZatcaConfig();
        $biller      = $this->site->getCompanyByID($inv->biller_id);
        $created_by  = $this->site->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $warehouse   = $this->site->getWarehouseByID($inv->warehouse_id);
        $inv         = $inv;
        $rows        = $this->getAllInvoiceItems($id);
        $return_sale = null;//$inv->return_id ? $this->getInvoiceByID($inv->return_id) : null;
        $return_rows = null;//$inv->return_id ? $this->getAllInvoiceItems($inv->return_id) : null;
		$payments        = $this->getInvoicePayments($inv->id);
		$return_payments = $inv->return_id ? $this->getInvoicePayments($inv->return_id) : null;
        $active_tax        =$this->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->getInvoicePayments($inv->return_id) : null;

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->Settings->site_name;
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
				$currencyCode= $Settings->default_currency;
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


    private function integration_zatca_clearance($id)
    {

		$inv         = $this->getInvoiceByID($id);
        $customer    = $this->site->getCompanyByID($inv->customer_id);
        $units       = $this->site->getAllUnits();
        $biller      = $this->site->getCompanyByID($inv->biller_id);
        $created_by  = $this->site->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $warehouse   = $this->site->getWarehouseByID($inv->warehouse_id);
        $zatcaConfig       = $this->site->getZatcaConfig();
        $inv         = $inv;
        $rows        = $this->getAllInvoiceItems($id);
        $return_sale = null;//$inv->return_id ? $this->getInvoiceByID($inv->return_id) : null;
        $return_rows = null;//$inv->return_id ? $this->getAllInvoiceItems($inv->return_id) : null;
		$payments        = $this->getInvoicePayments($inv->id);
		$return_payments = $inv->return_id ? $this->getInvoicePayments($inv->return_id) : null;
        $active_tax        =$this->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->getInvoicePayments($inv->return_id) : null;

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->Settings->site_name;

		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
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

		$data = '{"invoiceHash":"'.$inv->stamp_hash.'","uuid":"'.$inv->uuid.'","invoice":"'.$inv->synch_hash.'"}';
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
                $this->db->update('sales', ['clearedInvoice' => $res->clearedInvoice], ['id' => $inv->id]);
                //$xml_download=anchor('admin/welcome/downloadXMLUBL/' . $xml_file_path.'clearedInvoice.xml', '<i class="fa fa-download"></i> <b>XML Cleared Invoice</b>', 'class="btn btn-primary btn-xs"');

            }


            }




        }else{
            //die('<i class="fa fa-times"></a> Failed!');
        }

    }

    private function gen_htmlV2($sales)
    {
        $html = '<div style="border:1px solid #DDD; padding:10px; margin:10px 0;"><h3>' . lang('Synchronize_with_ZATCA') . '</h3>
        <table width="100%" class="stable">';
        foreach ($sales as $inv) {
            $html .= '
            <tr>
                <td style="border-bottom: 1px solid #DDD;">' . lang('Invoice_No') . '</td>
                <td style="text-align:right;border-bottom: 1px solid #DDD;">' . $inv->id . '</td>
            </tr>';
            $html .= '
            <tr>
                <td style="border-bottom: 1px solid #DDD;">' . lang('Status') . '</td>
                <td style="text-align:right;border-bottom: 1px solid #DDD;">' . $inv->synch_status . '</td>
            </tr>';
            if ($inv->synch_status=="CLEARED") {
                $xml_download=anchor('admin/cron/downloadXMLUBL/' . $inv->xml_file_path.'clearedInvoice.xml', '<i class="fa fa-download"></i> <b>XML Cleared Invoice</b>', 'class="btn btn-primary btn-xs"');
                $html .= '
                <tr>
                    <td style="border-bottom: 1px solid #DDD;">' . lang('XML Cleared Invoice') . '</td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;">' . $xml_download . '</td>
                </tr>';
            }
            $html .= '
                <tr>
                    <td style="border-bottom: 1px solid #DDD;">&nbsp;</td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;">&nbsp;</td>
                </tr>';

        }

        $html .= '</table></div>';
        return $html;
    }

    public function getLastInvoiceSynch()
    {
        $this->db->select('stamp_hash');
		$this->db->where('synch', 1)->order_by('signing_time', 'desc')->limit(1);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }



}
