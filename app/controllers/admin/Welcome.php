<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();



        if (!$this->loggedIn && $this->router->fetch_method() !== 'language') {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            admin_redirect('login');
        }

        if ($this->Customer || $this->Supplier) {
            redirect('/');
        }

        $this->load->library('form_validation');
        $this->load->admin_model('db_model');
        $this->load->admin_model('products_model');
        $this->load->admin_model('sales_model');
        $this->load->admin_model('purchases_model');
        $this->load->admin_model('accounts_model');
        $this->load->admin_model('reports_model');
        $this->load->admin_model('companies_model');
    }

    public function download($file)
    {
        if(strlen($file) > 13 && substr($file, 0, 12)=='from_offline'){

            $id=substr($file, 13);
            $products = $this->products_model->getStockCountItems($id);
            $this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('manual_inventory');
			$this->excel->getActiveSheet()->SetCellValue('A1', lang('#'));
			$this->excel->getActiveSheet()->SetCellValue('B1', lang('product_code'));
			$this->excel->getActiveSheet()->SetCellValue('C1', lang('product_name'));
			$this->excel->getActiveSheet()->SetCellValue('D1', lang('quantity'));
			$this->excel->getActiveSheet()->SetCellValue('E1', lang('unit_code'));
            $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit'));

			$row = 2;

            $pr       = 0;
            $rw       = 0;
            foreach ($products as $product) {

                    $rw++;



					    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $rw.'');
                        $this->excel->getActiveSheet()->setCellValueExplicit('B' . $row, $product->product_code, PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $product->product_name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $product->counted);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->products_model->getUnitById($product->unit)->code);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->products_model->getUnitById($product->unit)->name);




				$row++;
				$pr++;

            }
			        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
                    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'manual_inventory_' . date('Y_m_d_H_i_s');
                    $filenameV2 =$filename . '.xlsx';
                    $this->load->helper('excel');
			        create_excel($this->excel, $filename);
        }else{

        if (file_exists('./files/' . $file)) {
            $this->load->helper('download');
            force_download('./files/' . $file, null);
            exit();
        }else if (file_exists('./assets/uploads/' . $file)) {
            $this->load->helper('download');
            force_download('./assets/uploads/' . $file, null);
            exit();
        }else{

            //$pdfContent  = file_get_contents('https://domain1.com/assets/uploads/' . $file);
            $url = 'https://domain2.net/files/' . $file;

            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
            ]);

            $pdfContent = curl_exec($ch);


            curl_close($ch);

            $this->load->helper('download');
            force_download($file, $pdfContent);
            exit;
        }
        $this->session->set_flashdata('error', lang('file_x_exist'));
        redirect($_SERVER['HTTP_REFERER']);
        }
    }

	public function downloadXMLUBL($file)
    {
        if (file_exists('./assets/xml/' . $file)) {
            $this->load->helper('download');
            force_download('./assets/xml/' . $file, null);
            exit();
        }
        $this->session->set_flashdata('error', lang('file_x_exist'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function hideNotification($id = null)
    {
        $this->session->set_userdata('hidden' . $id, 1);
        echo true;
    }

    public function image_upload()
    {
        if (DEMO) {
            $error = ['error' => $this->lang->line('disabled_in_demo')];
            $this->sma->send_json($error);
            exit;
        }
        $this->security->csrf_verify();
        if (isset($_FILES['file'])) {
            $this->load->library('upload');
            $config['upload_path']   = 'assets/uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = '1048576';
            $config['max_width']     = $this->Settings->iwidth;
            $config['max_height']    = $this->Settings->iheight;
            $config['encrypt_name']  = true;
            $config['overwrite']     = false;
            $config['max_filename']  = 25;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                $error = $this->upload->display_errors();
                $error = ['error' => $error];
                $this->sma->send_json($error);
                exit;
            }
            $photo = $this->upload->file_name;
            $array = [
                'filelink' => base_url() . 'assets/uploads/images/' . $photo,
            ];
            echo stripslashes(json_encode($array));
            exit;
        } else {
            $error = ['error' => 'No file selected to upload!'];
            $this->sma->send_json($error);
            exit;
        }
    }

    public function index()
    {
        if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            admin_redirect('sync');
        }

        // =====================================================================
        // 1. UNIFIED FILTER CONTEXT DETERMINATION
        // =====================================================================
        $warehouse_id = $this->input->get('warehouse') ? (int)$this->input->get('warehouse') : null;
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = (int)$this->session->userdata('warehouse_id');
        }

        $period = $this->input->get('period') ? trim((string)$this->input->get('period')) : 'this_month';
        $start_date = null;
        $end_date   = null;
        $display_range = '';

        switch ($period) {
            case 'today':
                $start_date = date('Y-m-d 00:00:00');
                $end_date   = date('Y-m-d 23:59:59');
                $display_range = date('Y-m-d');
                break;

            case 'this_week':
                $w = (int)date('w'); // 0 (Sun) to 6 (Sat)
                $days_from_sat = ($w + 1) % 7;
                $sat_ts = strtotime("-$days_from_sat days");
                $fri_ts = strtotime("+" . (6 - $days_from_sat) . " days");
                $start_date = date('Y-m-d 00:00:00', $sat_ts);
                $end_date   = date('Y-m-d 23:59:59', $fri_ts);
                $display_range = date('Y-m-d', $sat_ts) . ' → ' . date('Y-m-d', $fri_ts);
                break;

            case 'this_month':
                $start_date = date('Y-m-01 00:00:00');
                $end_date   = date('Y-m-t 23:59:59');
                $display_range = date('Y-m-01') . ' → ' . date('Y-m-t');
                break;

            case 'this_quarter':
                $month = (int)date('n');
                $quarter = ceil($month / 3);
                $start_m = sprintf('%02d', ($quarter - 1) * 3 + 1);
                $end_m = sprintf('%02d', $quarter * 3);
                $start_date = date("Y-$start_m-01 00:00:00");
                $last_day_q = date('t', strtotime(date("Y-$end_m-01")));
                $end_date   = date("Y-$end_m-$last_day_q 23:59:59");
                $display_range = date("Y-$start_m-01") . ' → ' . date("Y-$end_m-$last_day_q");
                break;

            case 'this_year':
                $start_date = date('Y-01-01 00:00:00');
                $end_date   = date('Y-12-31 23:59:59');
                $display_range = date('Y-01-01') . ' → ' . date('Y-12-31');
                break;

            case 'fiscal_year':
                $q_fiscal = $this->db->get_where('gl_accounting_period', ['period_year_status' => 1], 1);
                if ($q_fiscal && $q_fiscal->num_rows() > 0) {
                    $fiscal_row = $q_fiscal->row();
                    $start_date = $fiscal_row->period_from_date . ' 00:00:00';
                    $end_date   = $fiscal_row->period_to_date . ' 23:59:59';
                    $display_range = $fiscal_row->period_from_date . ' → ' . $fiscal_row->period_to_date;
                } else {
                    $start_date = date('Y-01-01 00:00:00');
                    $end_date   = date('Y-12-31 23:59:59');
                    $display_range = date('Y-01-01') . ' → ' . date('Y-12-31');
                }
                break;

            case 'custom':
                $from_raw = $this->input->get('start_date') ?: ($this->input->get('from') ?: date('Y-m-01'));
                $to_raw   = $this->input->get('end_date') ?: ($this->input->get('to') ?: date('Y-m-d'));
                $from_ts  = strtotime($from_raw) ? strtotime($from_raw) : strtotime(date('Y-m-01'));
                $to_ts    = strtotime($to_raw) ? strtotime($to_raw) : time();
                if ($from_ts > $to_ts) {
                    $tmp = $from_ts;
                    $from_ts = $to_ts;
                    $to_ts = $tmp;
                }
                $start_date = date('Y-m-d 00:00:00', $from_ts);
                $end_date   = date('Y-m-d 23:59:59', $to_ts);
                $display_range = date('Y-m-d', $from_ts) . ' → ' . date('Y-m-d', $to_ts);
                break;

            case 'all':
            default:
                $period = 'all';
                $start_date = null;
                $end_date   = null;
                $display_range = lang('all_time');
                break;
        }

        $this->data['error']          = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['warehouse_id']   = $warehouse_id;
        $this->data['warehouses']     = $this->site->getAllWarehouses();
        $this->data['active_period']  = $period;
        $this->data['date_range']     = $period;
        $this->data['start_date']     = $start_date;
        $this->data['end_date']       = $end_date;
        $this->data['display_date_range'] = $display_range;
        $this->data['from_date_input'] = $start_date ? substr($start_date, 0, 10) : date('Y-m-01');
        $this->data['to_date_input']   = $end_date ? substr($end_date, 0, 10) : date('Y-m-d');

        // =====================================================================
        // 2. DOMAIN STATS (SALES, PURCHASES, INVENTORY, ACCOUNTING)
        // =====================================================================
        $this->data['sales_stats']     = $this->sales_model->getSalesDashboardStats($warehouse_id, $start_date, $end_date);
        $this->data['purchases_stats'] = $this->purchases_model->getPurchasesDashboardStats($warehouse_id, $start_date, $end_date);
        $this->data['inventory_stats'] = $this->products_model->getProductsDashboardStats($warehouse_id);
        $this->data['accounting_stats']= $this->accounts_model->getAccountingDashboardStats($start_date, $end_date, $warehouse_id);

        // Leaderboards & Analytics
        $this->data['top_selling_products']   = $this->sales_model->getTopSellingProducts(5, $warehouse_id, $start_date, $end_date);
        $this->data['top_customers']          = $this->sales_model->getTopCustomers(5, $warehouse_id, $start_date, $end_date);
        $this->data['top_purchased_products'] = $this->purchases_model->getTopPurchasedProducts(5, $warehouse_id, $start_date, $end_date);
        $this->data['top_suppliers']          = $this->purchases_model->getTopSuppliers(5, $warehouse_id, $start_date, $end_date);

        if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
		{
            // Receivables & Payables & Cash/Bank (Strict sma_gl_accounts_settings resolution)
            $this->data['top_receivables']    = $this->accounts_model->getTopReceivables(5, $start_date, $end_date, $warehouse_id);
            $this->data['top_payables']       = $this->accounts_model->getTopPayables(5, $start_date, $end_date, $warehouse_id);
            $this->data['aged_receivables']   = $this->accounts_model->getAgedReceivables($start_date, $end_date, $warehouse_id);
            $this->data['aged_payables']      = $this->accounts_model->getAgedPayables($start_date, $end_date, $warehouse_id);
            $this->data['cash_bank_balances'] = $this->accounts_model->getCashAndBankBalances(6, $end_date, $warehouse_id);
            $this->data['financial_position'] = $this->accounts_model->getFinancialPositionSummary($start_date, $end_date, $warehouse_id);
        }
        // Stock Movement & Warehouse Comparison
        $this->data['stock_movement']       = $this->db_model->getStockMovementSummary($warehouse_id, $start_date, $end_date);
        $this->data['warehouse_comparison'] = $this->db_model->getWarehouseComparisonStats($start_date, $end_date);
        $this->data['stock_alerts']         = $this->products_model->getProductStockAlerts(6, $warehouse_id);

        // Operational Alerts & Department Stats
        $this->data['dashboard_alerts']     = $this->db_model->getDashboardAlertsCounts($warehouse_id);
        $this->data['department_stats']     = $this->db_model->getDepartmentStatsCounts($warehouse_id, $start_date, $end_date);
        //$this->data['accounting_alerts']    = $this->accounts_model->getAccountingAttentionRequired($start_date, $end_date, $warehouse_id);

        // Recent Activity Across Modules
        $this->data['recent_sales']     = $this->sales_model->getRecentSales(8, $warehouse_id);
        $this->data['recent_purchases'] = $this->purchases_model->getRecentPurchases(8, $warehouse_id);
        $this->data['recent_payments']  = $this->db_model->getRecentPayments(8, $warehouse_id, $start_date, $end_date);

        // Chart Trend Series
        $this->data['monthly_sales_trends']     = $this->sales_model->getSalesMonthlyTrends(12, $warehouse_id);
        $this->data['monthly_purchases_trends'] = $this->purchases_model->getPurchasesMonthlyTrends(12, $warehouse_id);

        // =====================================================================
        // 3. BACKWARD COMPATIBILITY VARIABLES PRESERVATION
        // =====================================================================
        
        if($this->Settings->spark_ui == 1){
            $this->data['allSales']     = (object)$this->data['sales_stats'];
            $this->data['allPurchases'] = (object)$this->data['purchases_stats'];
        }else{
            $this->data['sales']        = $this->db_model->getLatestSales();
            $this->data['allSales']     = $this->db_model->getAllSales();
            $this->data['allPurchases']     = $this->db_model->getAllPurchases();
        }
        $this->data['rSales']       = $this->db_model->getReturns();
        $this->data['rPurchases']   = $this->db_model->getPurchasesReturns();
        $this->data['total_sales_canv']     = $this->db_model->getMonthlySales(date('Y'));
        $this->data['total_purchases_canv'] = $this->db_model->getMonthlyPurchases(date('Y'));
        $this->data['quotes']       = $this->db_model->getLastestQuotes();
        $this->data['transfers']    = $this->db_model->getLatestTransfers();

        if($this->Settings->spark_ui == 1){
        }else{
            $this->data['salesForProfitandLess'] = $this->db_model->getTotalSalesForProfitandLess();
            $this->data['purchases']    = $this->db_model->getLatestPurchases();
            $this->data['customers']    = $this->db_model->getLatestCustomers();
            $this->data['suppliers']    = $this->db_model->getLatestSuppliers();
            $this->data['chatData']     = $this->db_model->getChartData();
            $this->data['bs']           = $this->db_model->getBestSeller();
            $lmsdate                    = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
            $lmedate                    = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
            $this->data['lmbs']         = $this->db_model->getBestSeller($lmsdate, $lmedate);
        }

        $bc   = [['link' => '#', 'page' => lang('dashboard')]];
        $meta = ['page_title' => lang('dashboard'), 'bc' => $bc];

        if ($this->Settings->accountingOnly == 0) {
            $this->page_construct('dashboard1', $meta, $this->data);
        } else {
            admin_redirect('accounts/dashboard');
        }
    }
/*
    public function dashboard1()
    {
        if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            admin_redirect('sync');
        }

        $this->data['error']     = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['sales']     = $this->db_model->getLatestSales();
        $this->data['quotes']    = $this->db_model->getLastestQuotes();
        $this->data['purchases'] = $this->db_model->getLatestPurchases();
        $this->data['transfers'] = $this->db_model->getLatestTransfers();
        $this->data['customers'] = $this->db_model->getLatestCustomers();
        $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
        $this->data['chatData']  = $this->db_model->getChartData();
        $this->data['stock']     = $this->db_model->getStockValue();
        $this->data['bs']        = $this->db_model->getBestSeller();
        $lmsdate                 = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate                 = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        $this->data['lmbs']      = $this->db_model->getBestSeller($lmsdate, $lmedate);
        $bc                      = [['link' => '#', 'page' => lang('dashboard')]];
        $meta                    = ['page_title' => lang('dashboard'), 'bc' => $bc];
        $this->page_construct('dashboard1', $meta, $this->data);
    //    $this->load->view($this->theme . 'dashboard1', $this->data);
    }
*/

    public function copilot()
    {
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['allSales']     = $this->db_model->getAllSales();
        $this->data['allPurchases'] = $this->db_model->getAllPurchases();
        $this->data['salesTax']     = $this->db->select('COALESCE(SUM(total_tax), 0) as total_tax', false)->get('sales')->row()->total_tax;
        $this->data['purchasesTax'] = $this->db->select('COALESCE(SUM(total_tax), 0) as total_tax', false)->get('purchases')->row()->total_tax;
        $this->data['monthlySales']     = $this->db_model->getMonthlySales(date('Y'));
        $this->data['monthlyPurchases'] = $this->db_model->getMonthlyPurchases(date('Y'));

        $is_arabic = ($this->Settings->user_language == 'arabic');
        $bc        = [['link' => '#', 'page' => $is_arabic ? 'المحاسب الذكي' : 'Smart Accountant']];
        $meta      = ['page_title' => $is_arabic ? 'المحاسب الذكي' : 'Smart Accountant', 'bc' => $bc];

        if ($this->input->get('modal')) {
            $this->load->view($this->theme . 'copilot/index', $this->data);
        } else {
            $this->page_construct('copilot/index', $meta, $this->data);
        }
    }

    /* ==================================================================
     * Smart Accountant — AI ask endpoint
     * Flow: user text -> Groq (router only) -> { intent, dates, reply }
     *       -> fetch real figures from the existing report models
     *       -> return the exact JSON shape the 3D copilot view expects:
     *          { intent, icon, title, dates[], pills[], text, chart[], link }
     *       plus, for intent=invoices only, an extra { rows[], more } block —
     *       additive, so a renderer that predates it is unaffected.
     * The LLM never touches numbers. On any LLM failure we fall back to
     * deterministic keyword+date parsing, so the endpoint never breaks.
     * ================================================================== */
    public function copilot_ask()
    {
        $this->config->load('copilot', false, true);
        $is_ar = ($this->Settings->user_language == 'arabic');
        $t     = $this->copilotLang($is_ar);
        $curr  = (isset($this->Settings->default_currency) && $this->Settings->default_currency) ? $this->Settings->default_currency : 'SR';

        $raw = json_decode(file_get_contents('php://input'), true);
        $q   = is_array($raw) && isset($raw['q']) ? trim((string) $raw['q']) : trim((string) $this->input->post('q'));

        if ($q === '') {
            $this->copilotJson(['intent' => 'unknown', 'icon' => 'mdi mdi-help-circle-outline', 'title' => $t['unknown_t'], 'dates' => [], 'pills' => [], 'text' => $t['unknown'], 'chart' => null, 'link' => null]);
            return;
        }

        $warehouse_id = $this->copilotResolveWarehouse();

        // 1) Route with the LLM; fall back to deterministic parsing on failure
        $route = $this->copilotLLM($q, $is_ar);
        if (!$route) {
            $route = $this->copilotFallback($q, $is_ar);
        }

        $intent = in_array($route['intent'], ['sales', 'purchases', 'tax', 'invoices', 'chat', 'greet', 'unknown'], true) ? $route['intent'] : 'unknown';
        $reply  = isset($route['reply']) ? trim((string) $route['reply']) : '';

        // Conversational / general questions — the LLM answers within the website's
        // scope only; its reply is shown directly (no report is fetched).
        if ($intent === 'chat') {
            $this->copilotJson(['intent' => 'chat', 'icon' => 'fa fa-diamond', 'title' => $t['chat_t'], 'dates' => [], 'pills' => [], 'text' => ($reply !== '' ? $reply : $t['greet']), 'chart' => null, 'link' => null]);
            return;
        }
        if ($intent === 'greet') {
            $this->copilotJson(['intent' => 'greet', 'icon' => 'fa fa-diamond', 'title' => $t['greet_t'], 'dates' => [], 'pills' => [], 'text' => ($reply !== '' ? $reply : $t['greet']), 'chart' => null, 'link' => null]);
            return;
        }
        if ($intent === 'unknown') {
            $this->copilotJson(['intent' => 'unknown', 'icon' => 'mdi mdi-help-circle-outline', 'title' => $t['unknown_t'], 'dates' => [], 'pills' => [], 'text' => ($reply !== '' ? $reply : $t['unknown']), 'chart' => null, 'link' => null]);
            return;
        }

        // 2) Normalise the date range (defaults to the current month)
        [$start, $end] = $this->copilotRange($route['start_date'] ?? null, $route['end_date'] ?? null);
        $dateChips = [
            ['k' => $t['from'], 'v' => substr($start, 0, 10)],
            ['k' => $t['to'],   'v' => substr($end, 0, 10)],
        ];
        $sameYear = (substr($start, 0, 4) === substr($end, 0, 4));
        $year     = (int) substr($start, 0, 4);

        // 3) Fetch authoritative figures from the existing report models
        if ($intent === 'sales') {
            $this->load->api_model('sales_api');
            $rows  = $this->sales_api->getSalesReportAPP($start, $end, $warehouse_id);
            $total = $paid = $due = 0.0; $count = 0;
            foreach ((array) $rows as $r) {
                $total += (float) $r->total_amount; $paid += (float) $r->paid;
                $due   += (float) $r->balance;      $count += (int) $r->total;
            }
            $pills = [
                ['k' => $t['sales'],    'v' => $this->copilotMoney($total, $curr, $is_ar)],
                ['k' => $t['received'], 'v' => $this->copilotMoney($paid, $curr, $is_ar)],
                ['k' => $t['due'],      'v' => $this->copilotMoney($due, $curr, $is_ar)],
                ['k' => $t['invoices'], 'v' => (string) $count],
            ];
            $text  = ($reply !== '' ? $reply . ' ' : '') . $t['total_is'] . ' ' . $this->copilotMoney($total, $curr, $is_ar) . '.';
            $chart = $sameYear ? [$this->copilotMonthlySeries('sales', $year, $warehouse_id, $start, $end, $t['sales'], '#818cf8')] : null;
            $this->copilotJson(['intent' => 'sales', 'icon' => 'mdi mdi-finance', 'title' => $t['sales_t'], 'dates' => $dateChips, 'pills' => $pills, 'text' => $text, 'chart' => $chart, 'link' => admin_url('sales')]);
            return;
        }

        if ($intent === 'purchases') {
            $this->load->api_model('purchases_api');
            $rows  = $this->purchases_api->getPurchasesReportAPP($start, $end, $warehouse_id);
            $total = $tax = 0.0; $count = 0;
            foreach ((array) $rows as $r) {
                $total += (float) $r->grand_total; $tax += (float) $r->total_tax; $count++;
            }
            $pills = [
                ['k' => $t['purchases'], 'v' => $this->copilotMoney($total, $curr, $is_ar)],
                ['k' => $t['vat_in'],    'v' => $this->copilotMoney($tax, $curr, $is_ar)],
                ['k' => $t['invoices'],  'v' => (string) $count],
            ];
            $text  = ($reply !== '' ? $reply . ' ' : '') . $t['total_is'] . ' ' . $this->copilotMoney($total, $curr, $is_ar) . '.';
            $chart = $sameYear ? [$this->copilotMonthlySeries('purchases', $year, $warehouse_id, $start, $end, $t['purchases'], '#a78bfa')] : null;
            $this->copilotJson(['intent' => 'purchases', 'icon' => 'mdi mdi-chart-areaspline', 'title' => $t['purch_t'], 'dates' => $dateChips, 'pills' => $pills, 'text' => $text, 'chart' => $chart, 'link' => admin_url('purchases')]);
            return;
        }

        /* ---------- invoice list (row-level documents) -----------------------
         * The only copilot intent that returns individual documents instead of
         * aggregates, so it is the only one that needs a permission gate and
         * per-user visibility scoping — both mirrored from Sales::getSales().
         * Each row carries the SAME modal url the invoice list uses, so opening
         * one gives us the existing Print / PDF buttons verbatim: no new
         * document-rendering code, and no second definition of "an invoice".
         * -------------------------------------------------------------------- */
        if ($intent === 'invoices') {
            $f      = $this->copilotInvoiceFilters($route);
            $module = ($f['doc'] === 'purchase') ? 'purchases' : 'sales';

            // The copilot must never surface what the module itself would deny.
            if (!$this->sma->actionPermissions('index', $module)) {
                $this->copilotJson(['intent' => 'invoices', 'icon' => 'mdi mdi-lock-outline', 'title' => $t['inv_t'], 'dates' => [], 'pills' => [], 'rows' => [], 'more' => 0, 'text' => $t['denied'], 'chart' => null, 'link' => null]);
                return;
            }

            /* Totals need a window to mean anything, so those default to the
               current month. Finding documents does not: "invoice SL/2025/001",
               "unpaid invoices" and "the last 5 invoices" are all lookups that a
               silent month filter would answer wrongly — by hiding the very rows
               being asked for. So a period applies here only when the user
               actually named one. Rows come back newest-first either way. */
            $statedRange = !empty($route['start_date']) && !empty($route['end_date']);
            $byRef       = ($f['reference'] !== '');
            $useRange    = !$byRef && $statedRange;

            $r = $this->copilotInvoices($f, $useRange ? $start : null, $useRange ? $end : null, $warehouse_id, $curr, $is_ar, $t);

            if ($byRef) {
                $chips = [['k' => $t['ref'], 'v' => $f['reference'], 'solo' => true]];
            } elseif ($useRange) {
                $chips = $dateChips;
            } else {
                $chips = [['k' => $t['from'], 'v' => $t['all_time'], 'solo' => true]];
            }
            $pills = [
                ['k' => $t['invoices'], 'v' => (string) $r['count']],
                ['k' => ($f['doc'] === 'purchase' ? $t['purchases'] : $t['sales']), 'v' => $this->copilotMoney($r['total'], $curr, $is_ar)],
                ['k' => $t['received'], 'v' => $this->copilotMoney($r['paid'], $curr, $is_ar)],
                ['k' => $t['due'],      'v' => $this->copilotMoney($r['due'], $curr, $is_ar)],
            ];

            if ($r['count'] === 0) {
                $text = ($reply !== '' ? $reply . ' ' : '') . $t['inv_none'];
            } else {
                if (count($r['rows']) < $r['count']) {
                    $line = $t['inv_some'];
                } elseif ($r['count'] === 1) {
                    $line = $t['inv_one'];
                } else {
                    $line = $t['inv_all'];
                }
                $text = ($reply !== '' ? $reply . ' ' : '') . str_replace(
                    ['{shown}', '{count}'],
                    [(string) count($r['rows']), (string) $r['count']],
                    $line
                ) . ' ' . $t['inv_hint'];
            }

            $this->copilotJson([
                'intent' => 'invoices',
                'icon'   => 'mdi mdi-file-document-outline',
                'title'  => ($f['doc'] === 'purchase' ? $t['inv_p_t'] : $t['inv_t']),
                'dates'  => $chips,
                // No matches: totals of zero would read as data. Show none.
                'pills'  => ($r['count'] === 0 ? [] : $pills),
                'rows'   => $r['rows'],
                'more'   => max(0, $r['count'] - count($r['rows'])),
                'text'   => $text,
                'chart'  => null,
                'link'   => admin_url($module),
            ]);
            return;
        }

        // intent === 'tax'
        $this->load->admin_model('reports_model');
        $salesRows = $this->reports_model->getMultiSalesTaxLocalBasicDetails($start, $end, $warehouse_id, null, null, null, null, null);
        $purchRows = $this->reports_model->getMultiPurchasesTaxLocalBasicDetails($start, $end, $warehouse_id, null, null, null);
        $vatOut = $vatIn = 0.0;
        foreach ((array) $salesRows as $r) { $vatOut += (float) $r->total_tax; }
        foreach ((array) $purchRows as $r) { $vatIn  += (float) $r->total_tax; }
        $net   = $vatOut - $vatIn;
        $pills = [
            ['k' => $t['vat_out'], 'v' => $this->copilotMoney($vatOut, $curr, $is_ar)],
            ['k' => $t['vat_in'],  'v' => $this->copilotMoney($vatIn, $curr, $is_ar)],
            ['k' => $t['vat_net'], 'v' => $this->copilotMoney($net, $curr, $is_ar)],
        ];
        $text = ($reply !== '' ? $reply . ' ' : '') . $t['vat_net'] . ': ' . $this->copilotMoney($net, $curr, $is_ar)
              . '. (' . $t['vat_out'] . ' ' . $this->copilotMoney($vatOut, $curr, $is_ar) . ' − ' . $t['vat_in'] . ' ' . $this->copilotMoney($vatIn, $curr, $is_ar) . ')';
        $chart = null;
        if ($sameYear) {
            $out = $this->copilotMonthlySeries('salesTax', $year, $warehouse_id, $start, $end, $t['vat_out'], '#818cf8');
            $in  = $this->copilotMonthlySeries('purchasesTax', $year, $warehouse_id, $start, $end, $t['vat_in'], '#fbbf24');
            $chart = [$out, $in];
        }
        $this->copilotJson(['intent' => 'tax', 'icon' => 'mdi mdi-percent', 'title' => $t['tax_t'], 'dates' => $dateChips, 'pills' => $pills, 'text' => $text, 'chart' => $chart, 'link' => admin_url('reports/tax')]);
    }

    /* Resolve the current user's warehouse id (same logic as the v2 login,
       minus the password — copilot_ask already runs authenticated). */
    private function copilotResolveWarehouse()
    {
        $uid  = $this->session->userdata('user_id');
        if ($uid) {
            $u = $this->db->select('warehouse_id')->get_where('users', ['id' => $uid], 1)->row();
            if ($u && $u->warehouse_id) {
                return (int) $u->warehouse_id;
            }
        }
        $s = $this->db->select('default_warehouse')->get('settings')->row();
        return ($s && $s->default_warehouse) ? (int) $s->default_warehouse : null;
    }

    /* Sanitise the router's invoice fields. EVERYTHING the LLM emits is treated
       as untrusted text: values are whitelisted here and bound by the query
       builder in copilotInvoices() — never concatenated into SQL. */
    private function copilotInvoiceFilters($route)
    {
        $get = function ($k) use ($route) {
            return isset($route[$k]) && is_scalar($route[$k]) ? trim((string) $route[$k]) : '';
        };

        $doc = in_array(strtolower($get('doc')), ['purchase', 'purchases'], true) ? 'purchase' : 'sale';

        // Document status — whitelisted against the values each table actually stores.
        $status  = strtolower($get('status'));
        $allowed = ($doc === 'purchase') ? ['received', 'ordered', 'pending'] : ['completed', 'pending', 'returned'];
        if (!in_array($status, $allowed, true)) {
            $status = '';
        }

        // Payment status — 'unpaid' is a natural-language shorthand the schema
        // has no single value for; it becomes (pending OR partial) downstream.
        $pay = strtolower($get('payment_status'));
        if (in_array($pay, ['due', 'unpaid', 'overdue', 'outstanding'], true)) {
            $pay = 'unpaid';
        }
        if (!in_array($pay, ['paid', 'partial', 'pending', 'unpaid'], true)) {
            $pay = '';
        }

        // Reference numbers are short codes; anything else is a party-name search.
        $ref = $get('reference');
        if ($ref !== '' && !preg_match('/^[A-Za-z0-9\/\-_.]{2,55}$/', $ref)) {
            $ref = '';
        }

        $limit = (int) ($get('limit') !== '' ? $get('limit') : 8);

        return [
            'doc'       => $doc,
            'status'    => $status,
            'pay'       => $pay,
            'reference' => $ref,
            'party'     => mb_substr(preg_replace('/[\x00-\x1F\x7F]/u', '', $get('customer')), 0, 60, 'UTF-8'),
            'limit'     => max(1, min(25, $limit ?: 8)),
        ];
    }

    /* Fetch invoice rows + the aggregates for the WHOLE match (not just the page).
       Column choices, the balance formula and the visibility rules are mirrored
       from Sales::getSales() / Purchases::getPurchases() so a figure here can
       never disagree with the list page the row links to. */
    private function copilotInvoices($f, $start, $end, $warehouse_id, $curr, $is_ar, $t)
    {
        $isP   = ($f['doc'] === 'purchase');
        $table = $isP ? 'purchases' : 'sales';
        $pfx   = $this->db->dbprefix($table);
        $stCol = $isP ? 'status' : 'sale_status';

        $balance = $isP
            ? "({$pfx}.grand_total - ({$pfx}.paid + {$pfx}.discount_earned))"
            : "({$pfx}.grand_total - ({$pfx}.paid + {$pfx}.discount_allowance - {$pfx}.donation))";

        // Displayed number: identical rule to the list page, so the copilot and
        // the invoice list always call the same document by the same name.
        if ($isP) {
            $noCol = "{$pfx}.reference_no";
        } elseif (isset($this->Settings->invoiceNoRef) && $this->Settings->invoiceNoRef == 1) {
            $noCol = (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1) ? "{$pfx}.sale_no" : "{$pfx}.id";
        } else {
            $noCol = "{$pfx}.reference_no";
        }

        /* Applies every filter + visibility rule to the current query builder.
           Called twice (rows, then aggregate) because CI resets state on get(). */
        $scope = function () use ($f, $start, $end, $warehouse_id, $table, $pfx, $stCol) {
            $this->db->from($table);

            if ($start && $end) {
                $this->db->where("{$pfx}.date >=", $start)->where("{$pfx}.date <=", $end);
            }
            if ($warehouse_id) {
                $this->db->where("{$pfx}.warehouse_id", $warehouse_id);
            }
            if ($f['reference'] !== '') {
                $this->db->group_start()
                         ->where("{$pfx}.reference_no", $f['reference'])
                         ->or_where("{$pfx}.id", (int) $f['reference'])
                         ->group_end();
            }
            if ($f['party'] !== '') {
                $this->db->like($pfx . '.' . ($table === 'purchases' ? 'supplier' : 'customer'), $f['party']);
            }
            if ($f['status'] !== '') {
                $this->db->where("{$pfx}.{$stCol}", $f['status']);
            }
            if ($f['pay'] === 'unpaid') {
                $this->db->where_in("{$pfx}.payment_status", ['pending', 'partial']);
            } elseif ($f['pay'] !== '') {
                $this->db->where("{$pfx}.payment_status", $f['pay']);
            }

            // --- visibility: the same rules the list pages enforce -------------
            if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
                $this->db->where("{$pfx}.created_by", $this->session->userdata('user_id'));
            }
            if ($table === 'sales') {
                $this->db->where("{$pfx}.pos !=", 1);
            }
            // Multi-branch installs only: these columns do not exist otherwise.
            if (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1) {
                if ($this->session->userdata('grpId')) {
                    $this->db->where("{$pfx}.grpId", $this->session->userdata('grpId'));
                }
                if ($this->session->userdata('coId')) {
                    $this->db->where("{$pfx}.coId", $this->session->userdata('coId'));
                }
                if (!$this->Owner && !$this->Admin && $this->session->userdata('brId')) {
                    $this->db->where("{$pfx}.brId", $this->session->userdata('brId'));
                }
            }
        };

        // 1) Aggregates over the full match — the pills must describe everything
        //    that matched, not just the handful of rows we display.
        $this->db->select(
            "COUNT({$pfx}.id) as cnt,"
            . " COALESCE(SUM({$pfx}.grand_total), 0) as total,"
            . " COALESCE(SUM({$pfx}.paid), 0) as paid,"
            . " COALESCE(SUM({$balance}), 0) as due",
            false
        );
        $scope();
        $agg = $this->db->get()->row();

        // 2) The visible page, newest first
        $this->db->select(
            "{$pfx}.id as id, {$noCol} as ref, {$pfx}.reference_no as ref2,"
            . " DATE_FORMAT({$pfx}.date, '%Y-%m-%d') as d,"
            . ' ' . ($isP ? "{$pfx}.supplier" : "{$pfx}.customer") . ' as party,'
            . " {$pfx}.{$stCol} as st, {$pfx}.payment_status as pst,"
            . " {$pfx}.grand_total as total, {$balance} as due",
            false
        );
        $scope();
        $this->db->order_by("{$pfx}.date", 'desc')->order_by("{$pfx}.id", 'desc')->limit($f['limit']);
        $list = $this->db->get()->result();

        $rows = [];
        foreach ((array) $list as $r) {
            $ref = trim((string) $r->ref);
            if ($ref === '') {
                $ref = trim((string) $r->ref2) !== '' ? $r->ref2 : ('#' . $r->id);
            }
            /* With invoiceNoRef the list labels invoices by row id, which reads
               like an index in a sentence — so carry the human reference too
               whenever it differs, shown as a subtitle. */
            $ref2 = trim((string) $r->ref2);

            $rows[] = [
                'id'      => (int) $r->id,
                'ref'     => $ref,
                'ref2'    => ($ref2 !== '' && $ref2 !== $ref) ? $ref2 : '',
                'date'    => (string) $r->d,
                'party'   => (string) $r->party,
                /* money is markup by contract (it may carry the Riyal glyph) —
                   every other field on this row is plain text and MUST be
                   escaped by the renderer. */
                'total'   => $this->copilotMoney($r->total, $curr, $is_ar),
                'due'     => $this->copilotMoney($r->due, $curr, $is_ar),
                'status'  => $this->copilotStatusLabel($r->st, $t),
                'pay'     => $this->copilotStatusLabel($r->pst, $t),
                'pay_key' => strtolower((string) $r->pst),
                'url'     => admin_url($table . '/view/' . (int) $r->id),
                /* the invoice list's own modal — this is what carries the
                   existing Print / PDF (PrPDF) buttons into the copilot. */
                'modal'   => admin_url($table . '/modal_view/' . (int) $r->id),
            ];
        }

        return [
            'rows'  => $rows,
            'count' => (int) ($agg ? $agg->cnt : 0),
            'total' => (float) ($agg ? $agg->total : 0),
            'paid'  => (float) ($agg ? $agg->paid : 0),
            'due'   => (float) ($agg ? $agg->due : 0),
        ];
    }

    /* Localise a stored status value; unknown values pass through unchanged. */
    private function copilotStatusLabel($v, $t)
    {
        $k = 'st_' . strtolower(trim((string) $v));
        return isset($t[$k]) ? $t[$k] : (string) $v;
    }

    /* Ask Groq to turn the question into { intent, start_date, end_date, reply }.
       Returns null on any failure so the caller can fall back deterministically. */
    private function copilotLLM($q, $is_ar)
    {
        $key = $this->config->item('copilot_groq_key');
        if (!$key) {
            return null;
        }
        $today = date('Y-m-d');
        $lang  = $is_ar ? 'Arabic' : 'English';
        $system = "You are the Smart Accountant assistant for THIS accounting website only. Today is {$today}. "
            . "Read the user's message and reply with ONLY a JSON object, no prose, of the form: "
            . '{"intent":"sales|purchases|tax|invoices|chat","start_date":"YYYY-MM-DD 00:00:00","end_date":"YYYY-MM-DD 23:59:59","reply":"..."}. '
            . "intent=sales for revenue/sales figures, purchases for purchases/expenses figures, tax for VAT/tax figures. "
            . 'intent=invoices when the user wants to SEE, LIST, OPEN, PRINT or DOWNLOAD individual invoices/bills/documents rather than a total (e.g. "show me the invoices", "list unpaid bills", "invoice SL/2025/001", "last 5 invoices for Ahmed"). '
            . 'For invoices ONLY, add these extra keys: {"doc":"sale|purchase","customer":"party name or empty","reference":"invoice number or empty","status":"","payment_status":"paid|partial|pending|unpaid|empty","limit":8}. '
            . 'doc=purchase only when the user clearly means supplier/purchase invoices, otherwise doc=sale. Put a customer or supplier NAME in "customer" and an invoice NUMBER in "reference" — never both, and never guess one. Use limit for "last N invoices" (max 25, default 8). Leave any key you are unsure about as an empty string. '
            . "Resolve relative periods (today, this month, last month, this quarter, last year, a named month, a year) into concrete start_date/end_date using today's date. "
            . "For sales/purchases/tax: if no period is given use the current month. "
            . 'For invoices: leave start_date and end_date EMPTY unless the user actually named a period — do not invent one. Listing documents is a lookup, not a period total, and a month window would hide the invoice being looked for. '
            . "reply is ONE short friendly sentence in {$lang} that must NOT contain any numbers, amounts or currency (the system fills those in). "
            . "For chat: leave start_date and end_date empty; reply is your actual answer to the user, at most 1-2 short sentences in {$lang}. Stay STRICTLY within your role as this website's accounting assistant (sales, purchases, invoices, and tax/VAT reports and how to use them). Be warm for greetings. If the user asks for anything outside this scope (general knowledge, coding, math puzzles, other topics), politely decline in one sentence and steer them back to sales, purchases or tax. Never provide information or help beyond this website. "
            . "Always output valid JSON only.";

        /* Try the configured model; if Groq rejects the model itself (a retired
           or unknown id — 400/404), try the fallback once so a decommission
           costs quality instead of dropping to keyword parsing. Failures are
           logged: the previous retirement was invisible precisely because the
           fallback is silent. */
        $models = [$this->config->item('copilot_groq_model')];
        $alt    = $this->config->item('copilot_groq_model_fallback');
        if ($alt && $alt !== $models[0]) {
            $models[] = $alt;
        }

        $res = false;
        foreach ($models as $i => $model) {
            $payload = json_encode([
                'model'           => $model,
                'temperature'     => 0,
                'response_format' => ['type' => 'json_object'],
                'messages'        => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user',   'content' => $q],
                ],
            ]);

            $ch = curl_init($this->config->item('copilot_groq_url'));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $key],
                CURLOPT_TIMEOUT        => (int) ($this->config->item('copilot_llm_timeout') ?: 6),
                CURLOPT_CONNECTTIMEOUT => 4,
            ]);
            $res  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($res !== false && $code >= 200 && $code < 300) {
                break;
            }

            log_message('error', 'Smart Accountant: Groq call failed (model ' . $model . ', HTTP ' . $code . '): '
                . substr((string) $res, 0, 300));

            // Only a rejected model is worth retrying — a timeout or a rate
            // limit would just burn the request budget again.
            if ($code !== 400 && $code !== 404) {
                return null;
            }
            $res = false;
            if ($i === count($models) - 1) {
                return null;
            }
        }
        if ($res === false) {
            return null;
        }

        $body = json_decode($res, true);
        $content = $body['choices'][0]['message']['content'] ?? '';
        $obj = json_decode($content, true);
        if (!is_array($obj) || !isset($obj['intent'])) {
            log_message('error', 'Smart Accountant: Groq returned an unusable payload: ' . substr((string) $content, 0, 300));
            return null;
        }
        return $obj;
    }

    /* Deterministic fallback: keyword intent + light date parsing (Arabic/English). */
    private function copilotFallback($q, $is_ar)
    {
        $l = mb_strtolower($q, 'UTF-8');
        $has = function ($needles) use ($l) {
            foreach ($needles as $n) { if (mb_strpos($l, $n) !== false) { return true; } }
            return false;
        };

        $isPurchase = $has(['مشتري', 'مورد', 'purchase', 'supplier']);
        $extra      = [];

        $intent = 'unknown';
        /* Wanting to SEE documents beats wanting a total, so the invoice
           keywords are tested before the aggregate ones ("sales invoices"
           is a list request, not a sales total). */
        if ($has(['فاتور', 'فواتير', 'invoice', 'bill'])) {
            $intent = 'invoices';
            $extra  = [
                'doc'            => $isPurchase ? 'purchase' : 'sale',
                'customer'       => '',
                'reference'      => '',
                'status'         => '',
                'payment_status' => '',
                'limit'          => 8,
            ];
            if ($has(['غير مدفوع', 'غير المدفوع', 'متأخر', 'unpaid', 'due', 'outstanding', 'not paid'])) {
                $extra['payment_status'] = 'unpaid';
            } elseif ($has(['مدفوع', 'paid'])) {
                $extra['payment_status'] = 'paid';
            }
            /* "invoice SALE2026/07/0006" / "الفاتورة رقم 1204": the trailing
               token counts as a reference only if it is a whole code — either
               it carries a separator or it is a bare row id. Matching a loose
               digit run would slice "…/0006" out of a full reference. */
            $parts = preg_split('/\s+/u', trim($q));
            $tok   = (string) end($parts);
            if (preg_match('/^[A-Za-z0-9][A-Za-z0-9\/\-_.]{0,54}$/', $tok)
                && !preg_match('/^20\d{2}$/', $tok)
                && (ctype_digit($tok) || preg_match('#[/\-_.]#', $tok))) {
                $extra['reference'] = $tok;
            }
            if (preg_match('/(?:آخر|اخر|last|latest|top)\s+(\d{1,2})/u', $l, $lm)) {
                $extra['limit'] = (int) $lm[1];
            }
        }
        elseif ($has(['مبيع', 'sales', 'revenue', 'إيراد'])) { $intent = 'sales'; }
        elseif ($has(['مشتري', 'purchase', 'expense', 'مصروف'])) { $intent = 'purchases'; }
        elseif ($has(['ضريب', 'ضرائب', 'tax', 'vat']))            { $intent = 'tax'; }
        elseif ($has(['مرحب', 'اهلا', 'أهلا', 'سلام', 'hello', 'hi ', 'help', 'مساعد'])) { $intent = 'greet'; }

        $y = (int) date('Y'); $m = (int) date('n');
        $start = $end = null;
        if ($has(['اليوم', 'today'])) {
            $start = date('Y-m-d') . ' 00:00:00'; $end = date('Y-m-d') . ' 23:59:59';
        } elseif ($has(['الشهر الماضي', 'last month'])) {
            $p = mktime(0, 0, 0, $m - 1, 1, $y);
            $start = date('Y-m-01', $p) . ' 00:00:00'; $end = date('Y-m-t', $p) . ' 23:59:59';
        } elseif ($has(['العام الماضي', 'السنة الماضية', 'last year'])) {
            $start = ($y - 1) . '-01-01 00:00:00'; $end = ($y - 1) . '-12-31 23:59:59';
        } elseif (preg_match('/\b(20\d{2})\b/', $q, $mm)) {
            $start = $mm[1] . '-01-01 00:00:00'; $end = $mm[1] . '-12-31 23:59:59';
        }
        return $extra + ['intent' => $intent, 'start_date' => $start, 'end_date' => $end, 'reply' => ''];
    }

    /* Normalise/validate a date range; default = current month. Accepts
       'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM:SS'. */
    private function copilotRange($start, $end)
    {
        $norm = function ($v, $endOfDay) {
            if (!$v || !preg_match('/^(\d{4}-\d{2}-\d{2})(\s+\d{2}:\d{2}:\d{2})?$/', trim($v), $m)) {
                return null;
            }
            return $m[1] . (isset($m[2]) && trim($m[2]) !== '' ? $m[2] : ($endOfDay ? ' 23:59:59' : ' 00:00:00'));
        };
        $s = $norm($start, false);
        $e = $norm($end, true);
        if (!$s || !$e) {
            $s = date('Y-m-01') . ' 00:00:00';
            $e = date('Y-m-t') . ' 23:59:59';
        }
        if (strtotime($s) > strtotime($e)) { [$s, $e] = [$e, $s]; }
        return [$s, $e];
    }

    /* Build one chart series (months intersecting the range) from db_model. */
    private function copilotMonthlySeries($kind, $year, $warehouse_id, $start, $end, $label, $color)
    {
        $vals = array_fill(1, 12, 0.0);
        if ($kind === 'sales' || $kind === 'salesTax') {
            foreach ((array) $this->db_model->getMonthlySales($year, $warehouse_id) as $r) {
                $mo = (int) $r->date;
                if ($mo >= 1 && $mo <= 12) { $vals[$mo] = (float) ($kind === 'salesTax' ? $r->tax1 : $r->total); }
            }
        } else {
            foreach ((array) $this->db_model->getMonthlyPurchases($year, $warehouse_id) as $r) {
                $mo = (int) $r->date;
                if ($mo >= 1 && $mo <= 12) { $vals[$mo] = (float) ($kind === 'purchasesTax' ? $r->tax1 : $r->total); }
            }
        }
        $names   = $this->copilotMonthNames();
        $fromMo  = (int) substr($start, 5, 2);
        $toMo    = (int) substr($end, 5, 2);
        $values  = $labels = [];
        for ($i = $fromMo; $i <= $toMo; $i++) {
            $values[] = round($vals[$i], 2);
            $labels[] = $names[$i - 1];
        }
        return ['label' => $label, 'color' => $color, 'values' => $values, 'labels' => $labels];
    }

    private function copilotMoney($n, $curr, $is_ar)
    {
        $s = number_format((float) $n, 2, '.', ',');
        // Saudi Riyal: render the official symbol (SVG), never "SR"/"SAR", in any language.
        if (in_array(strtolower(trim((string) $curr)), ['sr', 'sar', 'ر.س', '﷼'], true)) {
            return $s . '<svg class="cp-riyal" aria-hidden="true" focusable="false"><use href="#cpRiyal"></use></svg>';
        }
        return $is_ar ? ($s . ' ' . $curr) : ($curr . ' ' . $s);
    }

    private function copilotMonthNames()
    {
        return ($this->Settings->user_language == 'arabic')
            ? ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
            : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    }

    private function copilotJson($payload)
    {
        $this->output
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    /* Display labels — mirror of the view's $t (the subset the answer uses). */
    private function copilotLang($is_ar)
    {
        return $is_ar ? [
            'sales' => 'المبيعات', 'purchases' => 'المشتريات', 'received' => 'المستلمة', 'due' => 'المتأخرة',
            'invoices' => 'فاتورة', 'vat_out' => 'ضريبة المبيعات', 'vat_in' => 'ضريبة المشتريات', 'vat_net' => 'صافي الضريبة المستحقة',
            'from' => 'من', 'to' => 'إلى', 'sales_t' => 'تقرير المبيعات', 'purch_t' => 'تقرير المشتريات', 'tax_t' => 'تقرير الضريبة',
            'total_is' => 'الإجمالي خلال الفترة المحددة بلغ', 'greet_t' => 'أهلاً بك 👋', 'chat_t' => 'المحاسب الذكي',
            'greet' => 'أنا محاسبك الذكي. اطلب تقارير المبيعات أو المشتريات أو الضرائب لأي فترة — اكتب طلبك أو المس إحدى البطاقات العائمة حولي.',
            'unknown_t' => 'لم أفهم الطلب بعد',
            'unknown' => 'ما زلت أتعلّم! جرّب: «مبيعات هذا الشهر»، «أرني فواتير هذا الشهر»، «ضريبة الربع الأول».',
            /* invoice list */
            'inv_t' => 'فواتير المبيعات', 'inv_p_t' => 'فواتير المشتريات', 'ref' => 'رقم الفاتورة',
            'inv_none' => 'لا توجد فواتير مطابقة لهذا الطلب.',
            'inv_all'  => 'وجدت {count} فاتورة.',
            'inv_one'  => 'وجدت فاتورة واحدة.',
            'all_time' => 'كامل الفترات',
            'inv_some' => 'أعرض أحدث {shown} من أصل {count} فاتورة.',
            'inv_hint' => 'اضغط على أي فاتورة لعرضها وطباعتها أو تنزيلها PDF.',
            'denied'   => 'لا تملك صلاحية الاطلاع على الفواتير.',
            'st_completed' => 'مكتملة', 'st_pending' => 'معلّقة', 'st_returned' => 'مرتجعة',
            'st_received' => 'مستلمة', 'st_ordered' => 'مطلوبة',
            'st_paid' => 'مدفوعة', 'st_partial' => 'مدفوعة جزئياً',
        ] : [
            'sales' => 'Sales', 'purchases' => 'Purchases', 'received' => 'Received', 'due' => 'Due',
            'invoices' => 'invoices', 'vat_out' => 'Output VAT (sales)', 'vat_in' => 'Input VAT (purchases)', 'vat_net' => 'Net VAT payable',
            'from' => 'From', 'to' => 'To', 'sales_t' => 'Sales Report', 'purch_t' => 'Purchases Report', 'tax_t' => 'Tax Report',
            'total_is' => 'Total for the selected period is', 'greet_t' => 'Welcome 👋', 'chat_t' => 'Smart Accountant',
            'greet' => "I'm your smart accountant. Ask for sales, purchases or tax reports over any period — type a request or touch one of the cards floating around me.",
            'unknown_t' => "I didn't catch that yet",
            'unknown' => 'Still learning! Try: “sales this month”, “show invoices this month”, or “VAT for Q1”.',
            /* invoice list */
            'inv_t' => 'Sales Invoices', 'inv_p_t' => 'Purchase Invoices', 'ref' => 'Invoice no.',
            'inv_none' => 'No invoices match that request.',
            'inv_all'  => 'Found {count} invoices.',
            'inv_one'  => 'Found 1 invoice.',
            'all_time' => 'All time',
            'inv_some' => 'Showing the latest {shown} of {count} invoices.',
            'inv_hint' => 'Tap any invoice to view, print or download it as PDF.',
            'denied'   => "You don't have permission to view invoices.",
            'st_completed' => 'Completed', 'st_pending' => 'Pending', 'st_returned' => 'Returned',
            'st_received' => 'Received', 'st_ordered' => 'Ordered',
            'st_paid' => 'Paid', 'st_partial' => 'Partial',
        ];
    }
    
    public function language($lang = false)
    {
        if ($this->input->get('lang')) {
            $lang = $this->input->get('lang');
        }
        $allowed_languages = ['arabic', 'english'];
        if (is_string($lang) && in_array($lang, $allowed_languages, true)) {
            $folder        = 'app/language/';
            $languagefiles = scandir($folder);
            if (in_array($lang, $languagefiles, true)) {
                $cookie = [
                    'name'   => 'language',
                    'value'  => $lang,
                    'expire' => '31536000',
                    'prefix' => 'sma_',
                    'secure' => false,
                ];
                $this->input->set_cookie($cookie);

                $rtl_cookie = [
                    'name'   => 'rtl_support',
                    'value'  => ($lang === 'arabic' ? '1' : '0'),
                    'expire' => '31536000',
                    'prefix' => 'sma_',
                    'secure' => false,
                ];
                $this->input->set_cookie($rtl_cookie);
            }
        }

        $referrer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url('login');
        $base_host = parse_url(base_url(), PHP_URL_HOST);
        $ref_host  = parse_url($referrer, PHP_URL_HOST);
        if ($ref_host && $base_host && strtolower($ref_host) !== strtolower($base_host)) {
            $referrer = admin_url('login');
        }

        redirect($referrer);
    }

    public function promotions()
    {
        $this->load->view($this->theme . 'promotions', $this->data);
    }

    public function set_data($ud, $value)
    {
        $this->session->set_userdata($ud, $value);
        echo true;
    }

    public function slug()
    {
        echo $this->sma->slug($this->input->get('title', true), $this->input->get('type', true));
        exit();
    }

    public function toggle_rtl()
    {
        $cookie = [
            'name'   => 'rtl_support',
            'value'  => $this->Settings->user_rtl == 1 ? 0 : 1,
            'expire' => '31536000',
            'prefix' => 'sma_',
            'secure' => false,
        ];
        $this->input->set_cookie($cookie);
        redirect($_SERVER['HTTP_REFERER']);
    }
}
