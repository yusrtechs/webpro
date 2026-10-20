<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Carsreports extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('carsreports_model');
        $this->load->admin_model('db_model');
        $this->load->admin_model('cars_model');
        $this->load->admin_model('carssales_model');
        $this->data['pb'] = [
            'cash'       => lang('cash'),
            'CC'         => lang('CC'),
            'Cheque'     => lang('Cheque'),
            'paypal_pro' => lang('paypal_pro'),
            'stripe'     => lang('stripe'),
            'gift_card'  => lang('gift_card'),
            'deposit'    => lang('deposit'),
            'authorize'  => lang('authorize'),
        ];
    }

    public function adjustments($warehouse_id = null)
    {
        $this->sma->checkPermissions('products',true,'reports');


        $this->data['users']      = $this->carsreports_model->getStaff();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('adjustments_report')]];
        $meta                = ['page_title' => lang('adjustments_report'), 'bc' => $bc];
        $this->page_construct('carsreports/adjustments', $meta, $this->data);
    }

    public function best_sellers($warehouse_id = null)
    {
        $this->sma->checkPermissions('products',true,'reports');

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $y1                  = date('Y', strtotime('-1 month'));
        $m1                  = date('m', strtotime('-1 month'));
        $m1sdate             = $y1 . '-' . $m1 . '-01 00:00:00';
        $m1edate             = $y1 . '-' . $m1 . '-' . days_in_month($m1, $y1) . ' 23:59:59';
        $this->data['m1']    = date('M Y', strtotime($y1 . '-' . $m1));
        $this->data['m1bs']  = $this->carsreports_model->getBestSeller($m1sdate, $m1edate, $warehouse_id);
        $y2                  = date('Y', strtotime('-2 months'));
        $m2                  = date('m', strtotime('-2 months'));
        $m2sdate             = $y2 . '-' . $m2 . '-01 00:00:00';
        $m2edate             = $y2 . '-' . $m2 . '-' . days_in_month($m2, $y2) . ' 23:59:59';
        $this->data['m2']    = date('M Y', strtotime($y2 . '-' . $m2));
        $this->data['m2bs']  = $this->carsreports_model->getBestSeller($m2sdate, $m2edate, $warehouse_id);
        $y3                  = date('Y', strtotime('-3 months'));
        $m3                  = date('m', strtotime('-3 months'));
        $m3sdate             = $y3 . '-' . $m3 . '-01 23:59:59';
        $this->data['m3']    = date('M Y', strtotime($y3 . '-' . $m3)) . ' - ' . $this->data['m1'];
        $this->data['m3bs']  = $this->carsreports_model->getBestSeller($m3sdate, $m1edate, $warehouse_id);
        $y4                  = date('Y', strtotime('-12 months'));
        $m4                  = date('m', strtotime('-12 months'));
        $m4sdate             = $y4 . '-' . $m4 . '-01 23:59:59';
        $this->data['m4']    = date('M Y', strtotime($y4 . '-' . $m4)) . ' - ' . $this->data['m1'];
        $this->data['m4bs']  = $this->carsreports_model->getBestSeller($m4sdate, $m1edate, $warehouse_id);
        // $this->sma->print_arrays($this->data['m1bs'], $this->data['m2bs'], $this->data['m3bs'], $this->data['m4bs']);
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('best_sellers')]];
        $meta                     = ['page_title' => lang('best_sellers'), 'bc' => $bc];
        $this->page_construct('carsreports/best_sellers', $meta, $this->data);
    }

    public function brands()
    {
        $this->sma->checkPermissions('products',true,'reports');
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('brands_report')]];
        $meta = ['page_title' => lang('brands_report'), 'bc' => $bc];
        $this->page_construct('carsreports/brands', $meta, $this->data);
    }

    public function categories()
    {
        $this->sma->checkPermissions('products',true,'reports');

        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('categories_report')]];
        $meta = ['page_title' => lang('categories_report'), 'bc' => $bc];
        $this->page_construct('carsreports/categories', $meta, $this->data);
    }


    public function customers()
    {
        //$this->sma->checkPermissions('customers',true,'reports');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('customers_report')]];
        $meta = ['page_title' => lang('customers_report'), 'bc' => $bc];
        $this->page_construct('carsreports/customers', $meta, $this->data);
    }

    public function daily_purchases($warehouse_id = null, $year = null, $month = null, $pdf = null, $user_id = null)
    {
       // $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config              = [
            'show_next_prev' => true,
            'next_prev_url'  => admin_url('carsreports/daily_purchases/' . ($warehouse_id ? $warehouse_id : 0)),
            'month_type'     => 'long',
            'day_type'       => 'long',
        ];

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $purchases = $user_id ? $this->carsreports_model->getStaffDailyPurchases($user_id, $year, $month, $warehouse_id) : $this->carsreports_model->getDailyPurchases($year, $month, $warehouse_id);

        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $daily_purchase[$purchase->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang('discount') . '</td><td>' . $this->sma->formatMoney($purchase->discount) . '</td></tr><tr><td>' . lang('shipping') . '</td><td>' . $this->sma->formatMoney($purchase->shipping) . '</td></tr><tr><td>' . lang('product_tax') . '</td><td>' . $this->sma->formatMoney($purchase->tax1) . '</td></tr><tr><td>' . lang('order_tax') . '</td><td>' . $this->sma->formatMoney($purchase->tax2) . '</td></tr><tr><td>' . lang('total') . '</td><td>' . $this->sma->formatMoney($purchase->total) . '</td></tr></table>';
            }
        } else {
            $daily_purchase = [];
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_purchase);
        $this->data['year']     = $year;
        $this->data['month']    = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'carsreports/daily', $this->data, true);
            $name = lang('daily_purchases') . '_' . $year . '_' . $month . '.pdf';
            $html = str_replace('<p class="introtext">' . lang('reports_calendar_text') . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->cars_model->getAllShowrooms();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('daily_purchases_report')]];
        $meta                        = ['page_title' => lang('daily_purchases_report'), 'bc' => $bc];
        $this->page_construct('carsreports/daily_purchases', $meta, $this->data);
    }

    public function daily_sales($warehouse_id = null, $year = null, $month = null, $pdf = null, $user_id = null)
    {
        //$this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config              = [
            'show_next_prev' => true,
            'next_prev_url'  => admin_url('carsreports/daily_sales/' . ($warehouse_id ? $warehouse_id : 0)),
            'month_type'     => 'long',
            'day_type'       => 'long',
        ];

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $user_id ? $this->carsreports_model->getStaffDailySales($user_id, $year, $month, $warehouse_id) : $this->carsreports_model->getDailySales($year, $month, $warehouse_id);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang('discount') . '</td><td>' . $this->sma->formatMoney($sale->discount) . '</td></tr><tr><td>' . lang('shipping') . '</td><td>' . $this->sma->formatMoney($sale->shipping) . '</td></tr><tr><td>' . lang('product_tax') . '</td><td>' . $this->sma->formatMoney($sale->tax1) . '</td></tr><tr><td>' . lang('order_tax') . '</td><td>' . $this->sma->formatMoney($sale->tax2) . '</td></tr><tr><td>' . lang('total') . '</td><td>' . $this->sma->formatMoney($sale->total) . '</td></tr></table>';
            }
        } else {
            $daily_sale = [];
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        $this->data['year']     = $year;
        $this->data['month']    = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'carsreports/daily', $this->data, true);
            $name = lang('daily_sales') . '_' . $year . '_' . $month . '.pdf';
            $html = str_replace('<p class="introtext">' . lang('reports_calendar_text') . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->cars_model->getAllShowrooms();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('daily_sales_report')]];
        $meta                        = ['page_title' => lang('daily_sales_report'), 'bc' => $bc];
        $this->page_construct('carsreports/daily', $meta, $this->data);
    }

    public function expenses($id = null)
    {
		$this->sma->checkPermissions('expenses',true,'reports');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['users']      = $this->carsreports_model->getStaff();
        $this->data['categories'] = $this->carsreports_model->getExpenseCategories();
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('expenses')]];
        $meta                     = ['page_title' => lang('expenses'), 'bc' => $bc];
        $this->page_construct('carsreports/expenses', $meta, $this->data);
    }

    public function expiry_alerts($warehouse_id = null)
    {
        $this->sma->checkPermissions('expenses',true,'reports');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->cars_model->getAllShowrooms();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $user                       = $this->site->getUser();
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse']    = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : null;
        }
		//$this->sma->print_arrays($this->data);




        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('product_expiry_alerts')]];
        $meta = ['page_title' => lang('product_expiry_alerts'), 'bc' => $bc];
        $this->page_construct('carsreports/expiry_alerts', $meta, $this->data);
    }

    public function get_deposits($company_id = null)
    {
      //  $this->sma->checkPermissions('customers',true,'reports');

        $this->load->library('datatables');
        $this->datatables
            ->select("date, amount, paid_by, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note", false)
            ->from('deposits')
            ->join('users', 'users.id=deposits.created_by', 'left')
            ->where($this->db->dbprefix('deposits') . '.company_id', $company_id);
        echo $this->datatables->generate();
    }

    public function get_purchase_taxes($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('tax',true,'reports');

        $supplier   = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        if ($start_date) {
           $start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
        }



        if ($pdf || $xls) {
            $this->db
            	->select("reference_no,note,supplier,{$this->db->dbprefix('companies')}.vat_no, status,DATE_FORMAT(date, '%Y-%m-%d %T') as date,DATE_FORMAT(due_date, '%Y-%m-%d %T') as due_date ,,(grand_total-total_tax) grand_total, total_tax, {$this->db->dbprefix('purchases')}.id as id", false)
                ->from('purchases')
                ->join('companies', 'companies.id=purchases.supplier_id', 'left');
            $this->db->where(" (status='partial' OR status='received' OR status='returned') ");

            if ($supplier) {
                $this->db->where('supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
               $this->excel->setActiveSheetIndex(0);
                 if ($this->Settings->user_language == 'arabic'){
                $this->excel->getActiveSheet()->setRightToLeft(true);
                }
                $this->excel->getActiveSheet()->setTitle(lang('purchases_report'));

                $this->excel->getActiveSheet()->mergeCells('A1:J1');
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('VAT_on_purchases'));
                $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
                 $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

                $this->excel->getActiveSheet()->mergeCells('A2:J2');
                $this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
                $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
                $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

                $this->excel->getActiveSheet()->mergeCells('A3:J3');
                $this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') .' '. lang('VAT_on_purchases') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(15);
                $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);

                $row   = 5;
                  $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Invoice_No'));
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(11);
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);




                 $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('description'));
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);


                $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('Car_Owner'));
                $this->excel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('VAT_Regesteration_Number'));
                $this->excel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('E'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('Status'));
                $this->excel->getActiveSheet()->getStyle('F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('Inv_date'));
                $this->excel->getActiveSheet()->getStyle('G'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('Due_date'));
                $this->excel->getActiveSheet()->getStyle('H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('H'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('Total_before_VAT'));
                $this->excel->getActiveSheet()->getStyle('I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('I'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('J'.$row, lang('total_tax'));
                $this->excel->getActiveSheet()->getStyle('J'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('J'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('J'.$row)->getFont()->setBold(true);


                    $styleArray1 = array(

                 'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'f9f9f9')
                    )
                  );
                //$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
                //$this->excel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($styleArray1);
				$this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleArray1);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row.'')->applyFromArray($styleArray1);

                $row ++;
                 $total = $total_tax = $sales_amount_no_tax = $igst = $cgst = $sgst = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->id);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->note);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->supplier);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->vat_no." ");
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang($data_row->status));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->due_date ?$this->sma->hrld($data_row->due_date) : '');
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($data_row->grand_total));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($data_row->total_tax));

					$total_tax   += $this->sma->formatDecimal($data_row->total_tax);
                    $total       += $this->sma->formatDecimal($data_row->grand_total);
                    $row++;
                }
				$this->excel->getActiveSheet()->mergeCells('A' . $row . ':H' . $row.'');
				$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

				$this->excel->getActiveSheet()->SetCellValue('A' . $row , lang('Total'));
				//$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->getStyle('H' . $row . ':J' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($total));
				//$this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($total_tax));
				//$this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(true);


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);













			$this->db
            ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, total, total_tax, ", false)
            ->from('expenses')
            //->join('users', 'users.id=expenses.created_by', 'left')
            ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
            //->where('expenses.payment_status','paid')
			->group_by('expenses.id');

            if ($warehouse) {
                $this->db->where('expenses.warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->db->where('expenses.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data_ex[] = $row;
                }
            } else {
                $data_ex = null;
            }

            if (!empty($data_ex)) {
               $this->excel->createSheet(1);
				$this->excel->setActiveSheetIndex(1);
                 if ($this->Settings->user_language == 'arabic'){
                $this->excel->getActiveSheet()->setRightToLeft(true);
                }
                $this->excel->getActiveSheet()->setTitle(lang('expenses'));

                $this->excel->getActiveSheet()->mergeCells('A1:F1');
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('VAT_on_purchases'));
                $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
                 $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

                $this->excel->getActiveSheet()->mergeCells('A2:F2');
                $this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
                $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
                $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

                $this->excel->getActiveSheet()->mergeCells('A3:F3');
                $this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') .' '. lang('VAT_on_purchases') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(15);
                $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);

                $row   = 5;
                  $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Expense_No'));
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(11);
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);




                 $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('date'));
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);


                $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('category'));
                $this->excel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);


                $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('Total_before_VAT'));
                $this->excel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('E'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('total_tax'));
                $this->excel->getActiveSheet()->getStyle('F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);


                    $styleArray1 = array(

                 'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'f9f9f9')
                    )
                  );
                //$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
                //$this->excel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($styleArray1);
				$this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleArray1);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row.'')->applyFromArray($styleArray1);

                $row ++;
                 $total = $total_tax = $sales_amount_no_tax = $igst = $cgst = $sgst = 0;
                foreach ($data_ex as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->id);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->category);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($data_row->total));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($data_row->total_tax));

					$total_tax   += $this->sma->formatDecimal($data_row->total_tax);
                    $total       += $this->sma->formatDecimal($data_row->total);
                    $row++;
                }
				$this->excel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row.'');
				$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

				$this->excel->getActiveSheet()->SetCellValue('A' . $row , lang('Total'));
				//$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->getStyle('E' . $row . ':F' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($total));
				//$this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($total_tax));
				//$this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);

			}








                 $filename = lang('purchase_tax_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
          // admin_redirect('carsreports/tax/#purchases-con');
        } else {
            $this->load->library('datatables');
            $this->datatables
            /*    ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, status, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, supplier, " . ($this->Settings->indian_gst ? 'igst, cgst, sgst,' : '') . " total, order_tax, grand_total, {$this->db->dbprefix('purchases')}.id as id", false)
                ->from('purchases')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');*/
				->select("{$this->db->dbprefix('purchases')}.id as id,reference_no,note,supplier,{$this->db->dbprefix('companies')}.vat_no, status,DATE_FORMAT(date, '%Y-%m-%d %T') as date,DATE_FORMAT(due_date, '%Y-%m-%d %T') as due_date ,,(grand_total-total_tax) grand_total, total_tax",  false)
                ->from('purchases')
                ->join('companies', 'companies.id=purchases.supplier_id', 'left');
				//$this->db->where(" (status='partial' OR status='received' OR status='returned') ");
				$this->db->where(" (status='partial' OR status='received') ");


            if ($supplier) {
                $this->datatables->where('supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }




	public function get_purchase_taxesDN($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('tax',true,'reports');
        $supplier   = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        if ($start_date) {
           $start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
        }




            $this->load->library('datatables');
            $this->datatables
            /*    ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, status, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, supplier, " . ($this->Settings->indian_gst ? 'igst, cgst, sgst,' : '') . " total, order_tax, grand_total, {$this->db->dbprefix('purchases')}.id as id", false)
                ->from('purchases')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');*/
				->select("{$this->db->dbprefix('purchases')}.id as id,reference_no,note,supplier,{$this->db->dbprefix('companies')}.vat_no, status,DATE_FORMAT(date, '%Y-%m-%d %T') as date,DATE_FORMAT(due_date, '%Y-%m-%d %T') as due_date ,,(grand_total-total_tax) grand_total, total_tax",  false)
                ->from('purchases')
                ->join('companies', 'companies.id=purchases.supplier_id', 'left');
				$this->db->where(" (status='returned') ");
		   if ($start_date && $end_date) {
            	$this->db->where(" (purchase_id   in (select id from {$this->db->dbprefix('purchases')} where (status='partial' OR status='received') and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
				}else{
				$this->db->where(" (purchase_id  not in (select id from {$this->db->dbprefix('purchases')} where (status='partial' OR status='received') and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
				}


            if ($supplier) {
                $this->datatables->where('supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

    }




	public function get_purchase_taxesDNOut($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('tax',true,'reports');

        $supplier   = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        if ($start_date) {
           $start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
        }




            $this->load->library('datatables');
            $this->datatables
            /*    ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, status, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, supplier, " . ($this->Settings->indian_gst ? 'igst, cgst, sgst,' : '') . " total, order_tax, grand_total, {$this->db->dbprefix('purchases')}.id as id", false)
                ->from('purchases')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');*/
				->select("{$this->db->dbprefix('purchases')}.id as id,reference_no,note,supplier,{$this->db->dbprefix('companies')}.vat_no, status,DATE_FORMAT(date, '%Y-%m-%d %T') as date,DATE_FORMAT(due_date, '%Y-%m-%d %T') as due_date ,,(grand_total-total_tax) grand_total, total_tax",  false)
                ->from('purchases')
                ->join('companies', 'companies.id=purchases.supplier_id', 'left');
				$this->db->where(" (status='returned') ");
				if ($start_date && $end_date) {
            	$this->db->where(" (purchase_id  not in (select id from {$this->db->dbprefix('purchases')} where (status='partial' OR status='received') and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
				}else{
				$this->db->where(" (purchase_id   in (select id from {$this->db->dbprefix('purchases')} where (status='partial' OR status='received') and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
				}


            if ($supplier) {
                $this->datatables->where('supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

    }





	public function get_sale_taxes($pdf = null, $xls = null)
    {
      //  $this->sma->checkPermissions('tax',true,'reports');

        $biller     = $this->input->get('biller') ? $this->input->get('biller') : null;
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';

        }

        if ($pdf || $xls) {



         $this->data['warehouses']   = $this->cars_model->getAllShowrooms();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['tax_rates']     = $this->carsreports_model->getTaxRatesDefault();
        $this->data['sale_tax']     = $this->carsreports_model->getSalesTax($start_date, $end_date,$warehouse_id);

		$sale_tax_local_nt    = $this->carsreports_model->getSalesTaxLocalNT($start_date, $end_date,$warehouse_id);

		$sale_tax_local_zero     = $this->carsreports_model->getSalesTaxLocalZero($start_date, $end_date,$warehouse_id);
        //$this->data['sale_tax_local_basic']     = $this->carsreports_model->getSalesTaxLocalBasic($start_date, $end_date);
        $sale_tax_local_basic_details     = $this->carsreports_model->getSalesTaxLocalBasicDetails($start_date, $end_date ,$warehouse_id);
        /*foreach ($sale_tax_local_basic_details as $sale_tax_local_basic_detail) {

        $this->sma->print_arrays($sale_tax_local_basic_detail);
        }*/

		$this->data['sale_tax_international']     = $this->carsreports_model->getSalesTaxInternational($start_date, $end_date ,$warehouse_id);
        $this->data['purchase_tax'] = $this->carsreports_model->getPurchasesTax($start_date, $end_date ,$warehouse_id);
        $this->data['purchase_tax_local_nt'] = $this->carsreports_model->getPurchasesTaxLocalNT($start_date, $end_date ,$warehouse_id);
        $this->data['purchase_tax_local_zero'] = $this->carsreports_model->getPurchasesTaxLocalZero($start_date, $end_date ,NULL,$warehouse_id);
        //$this->data['purchase_tax_local_basic'] = $this->carsreports_model->getPurchasesTaxLocalBasic($start_date, $end_date);
		$this->data['purchase_tax_local_basic_details'] = $this->carsreports_model->getPurchasesTaxLocalBasicDetails($start_date, $end_date,$warehouse_id);
        //$this->data['expenses_tax_local_basic'] = $this->carsreports_model->getExpensesTaxLocalBasic($start_date, $end_date);
        //$this->data['expenses_tax_local_basic_details'] = $this->carsreports_model->getExpensesTaxLocalBasicDetails($start_date, $end_date);
        $this->data['purchase_tax_international_basic'] = $this->carsreports_model->getPurchasesTaxInternationalBasic($start_date, $end_date,$warehouse_id);

		$this->data['purchase_tax_international_basic_details'] = $this->carsreports_model->getPurchasesTaxInternationalBasicDetails($start_date, $end_date,$warehouse_id);
        $this->data['purchase_tax_international_zero'] = $this->carsreports_model->getPurchasesTaxInternationalZero($start_date, $end_date,$warehouse_id);
        $this->data['purchase_tax_international_zero_details'] = $this->carsreports_model->getPurchasesTaxInternationalZeroDetails($start_date, $end_date ,$warehouse_id);









            $this->db
                /*->select("date, reference_no, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, biller, igst, cgst, sgst, product_tax, order_tax, grand_total,total, paid, payment_status")
                ->from('sales')
                ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')*/
				->select(" {$this->db->dbprefix('sales')}.id as id,reference_no,note,customer,{$this->db->dbprefix('companies')}.vat_no, sale_status,DATE_FORMAT(date, '%Y-%m-%d %T') as date,DATE_FORMAT(due_date, '%Y-%m-%d %T') as due_date ,(grand_total-total_tax) grand_total, total_tax", false)
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left');
            //    ->order_by('date desc');
			  //$this->db->where('paid !=', 0);
            $this->db->where(" (sale_status='completed' OR sale_status='returned') ");


            if ($biller) {
                $this->db->where('biller_id', $biller);
            }
            if ($warehouse) {
                $this->db->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
               $this->excel->setActiveSheetIndex(0);
                 if ($this->Settings->user_language == 'arabic'){
                $this->excel->getActiveSheet()->setRightToLeft(true);
                }


                $this->excel->getActiveSheet()->setTitle(lang('Sales_Tax_Rep'));


                $this->excel->getActiveSheet()->mergeCells('A1:J1');
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('VAT_on_sales'));
                $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
                 $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

                $this->excel->getActiveSheet()->mergeCells('A2:J2');
                $this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
                $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
                $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

                $this->excel->getActiveSheet()->mergeCells('A3:J3');
                $this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') .' '. lang('VAT_on_sales') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(15);
                $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);

                $row   = 5;
                  $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Invoice_No'));
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(11);
                 $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                 $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('description'));
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);


                $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('customer'));
                $this->excel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('VAT_Regesteration_Number'));
                $this->excel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('E'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('Status'));
                $this->excel->getActiveSheet()->getStyle('F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('Inv_date'));
                $this->excel->getActiveSheet()->getStyle('G'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('Due_date'));
                $this->excel->getActiveSheet()->getStyle('H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('H'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('Total_before_VAT'));
                $this->excel->getActiveSheet()->getStyle('I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('I'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('J'.$row, lang('total_tax'));
                $this->excel->getActiveSheet()->getStyle('J'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $this->excel->getActiveSheet()->getStyle('J'.$row)->getFont()->setSize(13);
                 $this->excel->getActiveSheet()->getStyle('J'.$row)->getFont()->setBold(true);


                    $styleArray1 = array(

                 'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'f9f9f9')
                    )
                  );
                $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleArray1);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row.'')->applyFromArray($styleArray1);
                $row++;
                 $total = $total_tax = $sales_amount_no_tax = $igst = $cgst = $sgst = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->id);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->note);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->vat_no);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang($data_row->sale_status));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->due_date ?$this->sma->hrld($data_row->due_date) : '');
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($data_row->grand_total));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($data_row->total_tax));

					$total_tax   += $this->sma->formatDecimal($data_row->total_tax);
                    $total       += $this->sma->formatDecimal($data_row->grand_total);
                    $row++;
                }
				$this->excel->getActiveSheet()->mergeCells('A' . $row . ':H' . $row.'');
				$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

				$this->excel->getActiveSheet()->SetCellValue('A' . $row , lang('Total'));
				//$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($total));
				//$this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setBold(true);

                 $this->excel->getActiveSheet()->getStyle('H' . $row . ':J' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($total_tax));
				//$this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setSize(25);
				$this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(true);


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('VAT_on_sales');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
               // ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, sale_status, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, biller, " . ($this->Settings->indian_gst ? 'igst, cgst, sgst,' : '') . " total, order_tax, grand_total, {$this->db->dbprefix('sales')}.id as id", false)
                ->select(" {$this->db->dbprefix('sales')}.id as id,reference_no,note,customer,{$this->db->dbprefix('companies')}.vat_no, sale_status,DATE_FORMAT(date, '%Y-%m-%d %T') as date,DATE_FORMAT(due_date, '%Y-%m-%d %T') as due_date ,(grand_total-total_tax) grand_total, total_tax", false)
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left');
				//$this->db->where('paid !=', 0);
            //$this->db->where(" (sale_status='completed' OR sale_status='returned') ");
            $this->db->where(" (sale_status='completed') ");
			//$this->db->where(" (sale_id  in (select id from {$this->db->dbprefix('sales')} where sale_status='completed' and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");



            if ($biller) {
                $this->datatables->where('biller_id', $biller);
            }
            if ($warehouse) {
                $this->datatables->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }


	public function get_sale_taxesCN($pdf = null, $xls = null)
    {
      //  $this->sma->checkPermissions('tax',true,'reports');

        $biller     = $this->input->get('biller') ? $this->input->get('biller') : null;
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';

        }


		    $this->load->library('datatables');
            $this->datatables
               // ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, sale_status, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, biller, " . ($this->Settings->indian_gst ? 'igst, cgst, sgst,' : '') . " total, order_tax, grand_total, {$this->db->dbprefix('sales')}.id as id", false)
                ->select(" {$this->db->dbprefix('sales')}.id as id,reference_no,note,customer,{$this->db->dbprefix('companies')}.vat_no, sale_status,DATE_FORMAT(date, '%Y-%m-%d %T') as date,DATE_FORMAT(due_date, '%Y-%m-%d %T') as due_date ,(grand_total-total_tax) grand_total, total_tax", false)
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left');
				//$this->db->where('paid !=', 0);
            //$this->db->where(" (sale_status='completed' OR sale_status='returned') ");
            $this->db->where(" (sale_status='returned') ");
            //$this->db->where(" (sale_id  in (select id from {$this->db->dbprefix('sales')} where sale_status='completed' and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
			if ($start_date && $end_date) {
            	$this->db->where(" (sale_id  in (select id from {$this->db->dbprefix('sales')} where sale_status='completed' and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
			}else{
				$this->db->where(" (sale_id not in (select id from {$this->db->dbprefix('sales')} where sale_status='completed' and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
			}
            if ($biller) {
                $this->datatables->where('biller_id', $biller);
            }
            if ($warehouse) {
                $this->datatables->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

    }

public function get_sale_taxesCNOut($pdf = null, $xls = null)
    {
     //   $this->sma->checkPermissions('tax',true,'reports');

        $biller     = $this->input->get('biller') ? $this->input->get('biller') : null;
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';

        }


		    $this->load->library('datatables');
            $this->datatables
               // ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, sale_status, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, biller, " . ($this->Settings->indian_gst ? 'igst, cgst, sgst,' : '') . " total, order_tax, grand_total, {$this->db->dbprefix('sales')}.id as id", false)
                ->select(" {$this->db->dbprefix('sales')}.id as id,reference_no,note,customer,{$this->db->dbprefix('companies')}.vat_no, sale_status,DATE_FORMAT(date, '%Y-%m-%d %T') as date,DATE_FORMAT(due_date, '%Y-%m-%d %T') as due_date ,(grand_total-total_tax) grand_total, total_tax", false)
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left');
				//$this->db->where('paid !=', 0);
            //$this->db->where(" (sale_status='completed' OR sale_status='returned') ");
            $this->db->where(" (sale_status='returned') ");
            //$this->db->where(" (sale_id not in (select id from {$this->db->dbprefix('sales')} where sale_status='completed' and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
			if ($start_date && $end_date) {
            	$this->db->where(" (sale_id not in (select id from {$this->db->dbprefix('sales')} where sale_status='completed' and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
			}else{
				$this->db->where(" (sale_id  in (select id from {$this->db->dbprefix('sales')} where sale_status='completed' and date > '" . $start_date . "'   and date < '" . $end_date . "')) ");
			}

            if ($biller) {
                $this->datatables->where('biller_id', $biller);
            }
            if ($warehouse) {
                $this->datatables->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

    }


    public function getAdjustmentReport($print = null, $xls = null)
    {
        //$this->sma->checkPermissions('reports');
		if(!$this->site->IsPermissions('products-reports')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{

        $product      = $this->input->get('product') ? $this->input->get('product') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $serial       = $this->input->get('serial') ? $this->input->get('serial') : null;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($print || $xls) {
            //$ai = "( SELECT adjustment_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('products')}.name, ' (', (CASE WHEN {$this->db->dbprefix('adjustment_items')}.type  = 'subtraction' THEN (0-{$this->db->dbprefix('adjustment_items')}.quantity) ELSE {$this->db->dbprefix('adjustment_items')}.quantity END), ')') SEPARATOR '\n') as item_nane from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id GROUP BY {$this->db->dbprefix('adjustment_items')}.adjustment_id ) FAI";
            //$ai = "( SELECT adjustment_id, product_id, serial_no, {$this->db->dbprefix('adjustment_items')}.quantity as item_nane, {$this->db->dbprefix('adjustment_items')}.cost as cost, {$this->db->dbprefix('adjustment_items')}.value as value, {$this->db->dbprefix('adjustment_items')}.unit as unit from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id ";
            $ai = "( SELECT adjustment_id, product_id, serial_no, {$this->db->dbprefix('adjustment_items')}.quantity as item_nane, {$this->db->dbprefix('adjustment_items')}.cost as cost, {$this->db->dbprefix('adjustment_items')}.value as value, {$this->db->dbprefix('adjustment_items')}.unit as unit from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id ";
            if ($product || $serial) {
                $ai .= ' WHERE ';
            }
            if ($product) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.product_id = {$product} ";
            }
            if ($product && $serial) {
                $ai .= ' AND ';
            }
            if ($serial) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $ai .= " GROUP BY {$this->db->dbprefix('adjustment_items')}.adjustment_id ) FAI";

            $this->db->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date,reference_no, warehouses.name as wh_name, FAI.item_nane as iname, note, {$this->db->dbprefix('units')}.name as product_unit_code,FAI.cost as cost, FAI.value as value, {$this->db->dbprefix('adjustments')}.id as id", false)
            ->from('adjustments')
            ->join($ai, 'FAI.adjustment_id=adjustments.id', 'left')
            ->join('units', 'units.id=FAI.unit', 'left')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left');

            if ($user) {
                $this->db->where('adjustments.created_by', $user);
            }
            if ($product) {
                $this->db->where('FAI.product_id', $product);
            }
            if ($serial) {
                $this->db->like('FAI.serial_no', $serial);
            }
            if ($warehouse) {
                $this->db->where('adjustments.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('adjustments.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('adjustments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

			if ($print) {

		$this->data['rows']       = $data;
		$this->data['product']       = $this->site->getProductByID($product);
		$this->data['warehouse']       = $warehouse ? $this->site->getWarehouseByID($warehouse)->name : lang('all');

        $this->data['start_date']       = $start_date;
        $this->data['end_date']       = $end_date;
        //$this->sma->print_arrays($this->data);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('adjustments_report')]];
        $meta = ['page_title' => lang('adjustments_report'), 'bc' => $bc];

        $this->page_construct('products/adjustments_report_details_view', $meta, $this->data);


			}elseif ($xls) {

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle($this->site->getProductByID($product)->name);
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('Showroom'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('unit'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit_cost'));
				$this->excel->getActiveSheet()->SetCellValue('G1', lang('Total_Value'));

                $row = 2;
				$value   = 0;

                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wh_name);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->product_unit_code);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->cost);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->value);
                    $value   += $data_row->value;
                    $row++;
                }

				$this->excel->getActiveSheet()->getStyle('G' . $row . ':G' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $value);


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->getStyle('F2:F' . $row)->getAlignment()->setWrapText(true);
                $filename = 'adjustments_report '.$this->site->getProductByID($product)->name;
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
			}
        } else {
            //$ai = "( SELECT adjustment_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('products')}.name, '__', (CASE WHEN {$this->db->dbprefix('adjustment_items')}.type  = 'subtraction' THEN (0-{$this->db->dbprefix('adjustment_items')}.quantity) ELSE {$this->db->dbprefix('adjustment_items')}.quantity END)) SEPARATOR '___') as item_nane from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id ";
            $ai = "( SELECT adjustment_id, product_id, serial_no, {$this->db->dbprefix('adjustment_items')}.quantity as item_nane, {$this->db->dbprefix('adjustment_items')}.cost as cost, {$this->db->dbprefix('adjustment_items')}.value as value, {$this->db->dbprefix('adjustment_items')}.unit as unit from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id ";
            if ($product || $serial) {
                $ai .= ' WHERE ';
            }
            if ($product) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.product_id = {$product} ";
            }
            if ($product && $serial) {
                $ai .= ' AND ';
            }
            if ($serial) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $ai .= " GROUP BY {$this->db->dbprefix('adjustment_items')}.adjustment_id ) FAI";
            $this->load->library('datatables');
            $this->datatables
            ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, warehouses.name as wh_name, FAI.item_nane as iname, {$this->db->dbprefix('units')}.name as product_unit_code, FAI.cost as cost, FAI.value as value, {$this->db->dbprefix('adjustments')}.id as id", false)
            ->from('adjustments')
            ->join($ai, 'FAI.adjustment_id=adjustments.id', 'left')
            ->join('units', 'units.id=FAI.unit', 'left')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left');

            if ($user) {
                $this->datatables->where('adjustments.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FAI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FAI.serial_no', $serial);
            }
            if ($warehouse) {
                $this->datatables->where('adjustments.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('adjustments.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('adjustments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
		}
    }

    public function getBrandsReport($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('products',true,'reports');

        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $brand      = $this->input->get('brand') ? $this->input->get('brand') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        $pp = "( SELECT pp.brand as brand, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . ' pi ON pp.id = pi.product_id
                left join ' . $this->db->dbprefix('purchases') . ' p ON p.id = pi.purchase_id ';
        $sp = "( SELECT sp.brand as brand, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from {$this->db->dbprefix('products')} sp
                left JOIN " . $this->db->dbprefix('sale_items') . ' si ON sp.id = si.product_id
                left join ' . $this->db->dbprefix('sales') . ' s ON s.id = si.sale_id ';
        if ($start_date || $warehouse) {
            $pp .= ' WHERE ';
            $sp .= ' WHERE ';
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= ' AND ';
                    $sp .= ' AND ';
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        $pp .= ' GROUP BY pp.brand ) PCosts';
        $sp .= ' GROUP BY sp.brand ) PSales';

        if ($pdf || $xls) {
            $this->db
                ->select($this->db->dbprefix('brands') . '.name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit', false)
                ->from('brands')
                ->join($sp, 'brands.id = PSales.brand', 'left')
                ->join($pp, 'brands.id = PCosts.brand', 'left')
                ->group_by('brands.id, brands.name')
                ->order_by('brands.code', 'asc');

            if ($brand) {
                $this->db->where($this->db->dbprefix('brands') . '.id', $brand);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('brands_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('brands'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('profit_loss'));

                $row  = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl   = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl   += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('B' . $row . ':F' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('brands_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('brands') . '.id as id, ' . $this->db->dbprefix('brands') . '.name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit', false)
                ->from('brands')
                ->join($sp, 'brands.id = PSales.brand', 'left')
                ->join($pp, 'brands.id = PCosts.brand', 'left');

            if ($brand) {
                $this->datatables->where('brands.id', $brand);
            }
            $this->datatables->group_by('brands.id, brands.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
            $this->datatables->unset_column('id');
            echo $this->datatables->generate();
        }
    }

    public function getCategoriesReport($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('products',true,'reports');
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $category   = $this->input->get('category') ? $this->input->get('category') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        $pp = "( SELECT pp.category_id as category, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . ' pi ON pp.id = pi.product_id
                left join ' . $this->db->dbprefix('purchases') . ' p ON p.id = pi.purchase_id ';
        $sp = "( SELECT sp.category_id as category, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from {$this->db->dbprefix('products')} sp
                left JOIN " . $this->db->dbprefix('sale_items') . ' si ON sp.id = si.product_id
                left join ' . $this->db->dbprefix('sales') . ' s ON s.id = si.sale_id ';
        if ($start_date || $warehouse) {
            $pp .= ' WHERE ';
            $sp .= ' WHERE ';
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= ' AND ';
                    $sp .= ' AND ';
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        $pp .= ' GROUP BY pp.category_id ) PCosts';
        $sp .= ' GROUP BY sp.category_id ) PSales';

        if ($pdf || $xls) {
            $this->db
                ->select($this->db->dbprefix('categories') . '.code, ' . $this->db->dbprefix('categories') . '.name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit', false)
                ->from('categories')
                ->join($sp, 'categories.id = PSales.category', 'left')
                ->join($pp, 'categories.id = PCosts.category', 'left')
                ->group_by('categories.id, categories.code, categories.name')
                ->order_by('categories.code', 'asc');

            if ($category) {
                $this->db->where($this->db->dbprefix('categories') . '.id', $category);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('categories_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('category_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('category_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('profit_loss'));

                $row  = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl   = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl   += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('C' . $row . ':G' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('categories_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('categories') . '.id as cid, ' . $this->db->dbprefix('categories') . '.code, ' . $this->db->dbprefix('categories') . '.name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit', false)
                ->from('categories')
                ->join($sp, 'categories.id = PSales.category', 'left')
                ->join($pp, 'categories.id = PCosts.category', 'left');

            if ($category) {
                $this->datatables->where('categories.id', $category);
            }
            $this->datatables->group_by('categories.id, categories.code, categories.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
            $this->datatables->unset_column('cid');
            echo $this->datatables->generate();
        }
    }

    public function getCustomerLogins($id = null)
    {
        if ($this->input->get('login_start_date')) {
            $login_start_date = $this->input->get('login_start_date');
        } else {
            $login_start_date = null;
        }
        if ($this->input->get('login_end_date')) {
            $login_end_date = $this->input->get('login_end_date');
        } else {
            $login_end_date = null;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date   = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        $this->load->library('datatables');
        $this->datatables
            ->select('login, ip_address, time')
            ->from('user_logins')
            ->where('customer_id', $id);
        if ($login_start_date) {
            $this->datatables->where('time BETWEEN "' . $login_start_date . '" and "' . $login_end_date . '"');
        }
        echo $this->datatables->generate();
    }

    public function getCustomers($pdf = null, $xls = null)
    {
       // $this->sma->checkPermissions('customers',true,'reports');


        if ($pdf || $xls) {
            $this->db
                ->select($this->db->dbprefix('companies') . '.id as id, company, name, phone, email, count(' . $this->db->dbprefix('sales') . '.id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, COALESCE(sum(discount_allowance), 0) as discount_allowance, ( COALESCE(sum(grand_total), 0) - (COALESCE(sum(paid), 0) + COALESCE(sum(discount_allowance), 0))) as balance', false)
                ->from('companies')
                ->join('sales', 'sales.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->order_by('companies.company asc')
                ->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('customers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_sales'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('Discount_Allowance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($data_row->total_amount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney($data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatMoney($data_row->discount_allowance));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatMoney($data_row->balance));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('customers_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $s = '( SELECT customer_id, count(' . $this->db->dbprefix('sales') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, COALESCE(sum(discount_allowance), 0) as discount_allowance, ( COALESCE(sum(grand_total), 0) - (COALESCE(sum(paid), 0) + COALESCE(sum(discount_allowance), 0))) as balance from {$this->db->dbprefix('sales')} GROUP BY {$this->db->dbprefix('sales')}.customer_id ) FS";

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . '.id as id, company, name, phone, email, FS.total, FS.total_amount, FS.paid, FS.discount_allowance, FS.balance', false)
                ->from('companies')
                ->join($s, 'FS.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->group_by('companies.id')
                ->add_column('Actions', "<div class='text-center'><a class=\"tip\" title='" . lang('view_report') . "' href='" . admin_url('carssales/customer_report/$1') . "'><span class='label label-primary'>" . lang('view_report') . '</span></a></div>', 'id')
                ->unset_column('id');
            echo $this->datatables->generate();
        }
    }

    public function getCustomersPyments($pdf = null, $xls = null)
    {
      //  $this->sma->checkPermissions('customers',true,'reports');
		$customer       = $this->input->get('customer') ? $this->input->get('customer') : null;
        $start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;


        if ($start_date) {
                $start_date = $this->sma->fld($start_date).' 00:00:00';
				$end_date = $this->sma->fld($end_date).' 23:59:59';
        }


        if ($pdf || $xls) {
            /*$this->db
                ->select($this->db->dbprefix('companies') . '.id as id, company, name, phone, email, count(' . $this->db->dbprefix('sales') . '.id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance', false)
                ->from('companies')
                ->join('sales', 'sales.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                ->order_by('companies.company asc')
                ->group_by('companies.id');
				*/
			$s = "( SELECT a.customer_id, COALESCE(SUM(CASE WHEN b.paid_by='cash' THEN b.amount ELSE 0 END), 0)  amount_cash, COALESCE(SUM(CASE WHEN b.paid_by='CC' THEN b.amount ELSE 0 END), 0)  amount_CC, COALESCE(SUM(CASE WHEN b.paid_by!='CC' and b.paid_by!='cash' THEN b.amount ELSE 0 END), 0)  amount_other,COALESCE(SUM(commercial_discount),0) as commercial_discount ,((COALESCE(SUM(CASE WHEN b.paid_by='cash' THEN b.amount ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN b.paid_by='CC' THEN b.amount ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN b.paid_by!='CC' and b.paid_by!='cash' THEN b.amount ELSE 0 END), 0)) + COALESCE(SUM(commercial_discount),0)) as  net from {$this->db->dbprefix('sales')} a LEFT JOIN {$this->db->dbprefix('payments')} b on a.id=b.sale_id  ";
			if ($customer || $start_date) {
                $s .= ' WHERE ';
            }
            if ($customer) {
                $s .= " a.customer_id = {$customer} ";
            }
            if ($customer && $start_date) {
                $s .= ' AND ';
            }
            if ($start_date) {
                $s .= " a.date BETWEEN '{$start_date}' and '{$end_date}' ";
			//	$s .= " {$this->db->dbprefix('sales')}.date >= '{$start_date}' AND {$this->db->dbprefix('sales')}.date <= '{$end_date}'  ";
            }

			$s .= " GROUP BY a.customer_id ) FS";




            $this->load->library('datatables');
            $this->db
                ->select($this->db->dbprefix('companies') . '.id as id, company, name, FS.amount_cash, FS.amount_CC, FS.amount_other, FS.commercial_discount, FS.net', false)
				->from('companies')
                ->join($s, 'FS.customer_id=companies.id')
                ->where('companies.group_name', 'customer');
                //->group_by('companies.id')


			if ($customer) {
                $this->db->where('companies.id', $customer);
            }



            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('Customers_Payments'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('cash'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('CC'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('Others'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Discount_Allowance'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('Net'));

                $row = 2;
				$total=0;
                $total_CC=0;
                $total_discount=0;
                $additions=0;
                $total_amount=0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatMoney($data_row->amount_cash));
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatMoney($data_row->amount_CC));
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney($data_row->amount_other));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($data_row->commercial_discount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney($data_row->net));
                    $row++;
					$total += $data_row->amount_cash;
                    $total_CC += $data_row->amount_CC;
                    $additions += $data_row->amount_other;
                    $total_discount += $data_row->commercial_discount;
                    $total_amount += $data_row->net;

                }

				$this->excel->getActiveSheet()->getStyle('C' . $row . ':F' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatMoney($total));
				$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatMoney($total_CC));
				$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney($additions));
				$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($total_discount));
				$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney($total_amount));



                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('customers_payments_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            //$s = '( SELECT customer_id,(CASE WHEN (payment_status ="pending" or payment_status ="due") THEN (CASE WHEN payment_status ="pending" THEN "Deferred" ELSE "Partial_payment" END) ELSE payment_status END) as payment_status, count(' . $this->db->dbprefix('sales') . ".id) as total1, COALESCE(sum(total), 0) as total, COALESCE(sum(total_discount), 0) as total_discount, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(total_tax), 0) + COALESCE(sum(shipping), 0)) as additions from {$this->db->dbprefix('sales')} ";

			$s = "( SELECT a.customer_id, COALESCE(SUM(CASE WHEN b.paid_by='cash' THEN b.amount ELSE 0 END), 0)  amount_cash, COALESCE(SUM(CASE WHEN b.paid_by='CC' THEN b.amount ELSE 0 END), 0)  amount_CC, COALESCE(SUM(CASE WHEN b.paid_by!='CC' and b.paid_by!='cash' THEN b.amount ELSE 0 END), 0)  amount_other,COALESCE(SUM(commercial_discount),0) as commercial_discount ,((COALESCE(SUM(CASE WHEN b.paid_by='cash' THEN b.amount ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN b.paid_by='CC' THEN b.amount ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN b.paid_by!='CC' and b.paid_by!='cash' THEN b.amount ELSE 0 END), 0)) + COALESCE(SUM(commercial_discount),0)) as  net from {$this->db->dbprefix('sales')} a LEFT JOIN {$this->db->dbprefix('payments')} b on a.id=b.sale_id  ";
			if ($customer || $start_date) {
                $s .= ' WHERE ';
            }
            if ($customer) {
                $s .= " a.customer_id = {$customer} ";
            }
            if ($customer && $start_date) {
                $s .= ' AND ';
            }
            if ($start_date) {
                $s .= " a.date BETWEEN '{$start_date}' and '{$end_date}' ";
			//	$s .= " {$this->db->dbprefix('sales')}.date >= '{$start_date}' AND {$this->db->dbprefix('sales')}.date <= '{$end_date}'  ";
            }

			$s .= " GROUP BY a.customer_id ) FS";




            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . '.id as id, company, name, FS.amount_cash, FS.amount_CC, FS.amount_other, FS.commercial_discount, FS.net', false)
                ->from('companies')
                ->join($s, 'FS.customer_id=companies.id')
                ->where('companies.group_name', 'customer')
                //->group_by('companies.id')
                ->add_column('Actions', "<div class='text-center'><a class=\"tip\" title='" . lang('view_report') . "' href='" . admin_url('carssales/customer_report/$1') . "'><span class='label label-primary'>" . lang('view_report') . '</span></a></div>', 'id')
                ->unset_column('id');





            echo $this->datatables->generate();
        }
    }

    public function getExpensesReport($pdf = null, $xls = null)
    {
	//	$this->sma->checkPermissions('expenses',true,'reports');

        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $category     = $this->input->get('category') ? $this->input->get('category') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $note         = $this->input->get('note') ? $this->input->get('note') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {



            if ($this->Settings->accounting){

                $this->db
                    //->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment", false)
                    //->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference, {$this->db->dbprefix('gl_accounts')}.accountName as category, grand_total, paid, (grand_total-paid) as balance, payment_status, attachment", false)
                    ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference, {$this->db->dbprefix('gl_accounts')}.accountName as category, grand_total,note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)
                    ->from('expenses')
                    ->join('users', 'users.id=expenses.created_by', 'left')
                    ->join('gl_accounts', 'gl_accounts.id=expenses.account_id', 'left')
                    ->group_by('expenses.id');
                }else{

                    $this->db
                ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)
                //->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment", false)
                 //   ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, grand_total, paid, (grand_total-paid) as balance, payment_status, attachment", false)
                    ->from('expenses')
                    ->join('users', 'users.id=expenses.created_by', 'left')
                    ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
                    ->group_by('expenses.id');

                }

            if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
                $this->db->where('created_by', $this->session->userdata('user_id'));
            }

            if ($note) {
                $this->db->like('note', $note, 'both');
            }
            if ($reference_no) {
                $this->db->like('reference', $reference_no, 'both');
            }
            if ($category) {
                $this->db->where('category_id', $category);
            }
            if ($warehouse) {
                $this->db->where('expenses.warehouse_id', $warehouse);
            }
            if ($user) {
                $this->db->where('created_by', $user);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('expenses_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                  if ($this->Settings->accounting){
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('Account_type'));
                  }else {
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('category'));

                    }

                $this->excel->getActiveSheet()->SetCellValue('D1', lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('created_by'));

                $row   = 2;
                $total = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->category);
                    if ($this->Settings->accounting){
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->grand_total);
                      }else {
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->amount);

                        }
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->note);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->user);
                    $total += $data_row->amount;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $total);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('expenses_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');


            if ($this->Settings->accounting){

                $this->datatables
                    //->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment", false)
                    //->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference, {$this->db->dbprefix('gl_accounts')}.accountName as category, grand_total, paid, (grand_total-paid) as balance, payment_status, attachment", false)
                    ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference, {$this->db->dbprefix('gl_accounts')}.accountName as category, grand_total,note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)
                    ->from('expenses')
                    ->join('users', 'users.id=expenses.created_by', 'left')
                    ->join('gl_accounts', 'gl_accounts.id=expenses.account_id', 'left')
                    ->group_by('expenses.id');
                }else{

                $this->datatables
                ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)
                //->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment", false)
                 //   ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, grand_total, paid, (grand_total-paid) as balance, payment_status, attachment", false)
                    ->from('expenses')
                    ->join('users', 'users.id=expenses.created_by', 'left')
                    ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
                    ->group_by('expenses.id');

                }

            if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
                $this->datatables->where('created_by', $this->session->userdata('user_id'));
            }

            if ($note) {
                $this->datatables->like('note', $note, 'both');
            }
            if ($reference_no) {
                $this->datatables->like('reference', $reference_no, 'both');
            }
            if ($category) {
                $this->datatables->where('category_id', $category);
            }
            if ($warehouse) {
                $this->datatables->where('expenses.warehouse_id', $warehouse);
            }
            if ($user) {
                $this->datatables->where('created_by', $user);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }

    public function getExpiryAlerts($warehouse_id = null)
    {
        //$this->sma->checkPermissions('expiry_alerts', true);
			if(!$this->site->IsPermissions('products-reports')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{

        $date = date('Y-m-d', strtotime('+3 months'));

        if (!$this->Owner && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select('products.image, purchase_items.product_code, purchase_items.product_name, purchase_items.quantity_balance, warehouses.name, purchase_items.expiry')
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('purchase_items.warehouse_id', $warehouse_id)
                ->where('purchase_items.expiry !=', null)->where('purchase_items.expiry !=', '0000-00-00')
                ->where('purchase_items.quantity_balance >', 0)
                ->where('purchase_items.expiry <', $date);
        } else {
            $this->datatables
                ->select('products.image, purchase_items.product_code, purchase_items.product_name, purchase_items.quantity_balance, warehouses.name, purchase_items.expiry')
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')
                ->where('purchase_items.expiry !=', null)->where('purchase_items.expiry !=', '0000-00-00')
                ->where('purchase_items.quantity_balance >', 0)
                ->where('purchase_items.expiry <', $date);
        }
		//$this->sma->print_arrays($data, $products);
        echo $this->datatables->generate();
		}
    }

    public function getPaymentsReport($pdf = null, $xls = null)
    {
      //  $this->sma->checkPermissions('payments',true,'reports');


        $user           = $this->input->get('user') ? $this->input->get('user') : null;
        $supplier       = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $customer       = $this->input->get('customer') ? $this->input->get('customer') : null;
        $biller         = $this->input->get('biller') ? $this->input->get('biller') : null;
        $payment_ref    = $this->input->get('payment_ref') ? $this->input->get('payment_ref') : null;
        $paid_by        = $this->input->get('paid_by') ? $this->input->get('paid_by') : null;
        $sale_ref       = $this->input->get('sale_ref') ? $this->input->get('sale_ref') : null;
        $purchase_ref   = $this->input->get('purchase_ref') ? $this->input->get('purchase_ref') : null;
        $card           = $this->input->get('card') ? $this->input->get('card') : null;
        $cheque         = $this->input->get('cheque') ? $this->input->get('cheque') : null;
        $transaction_id = $this->input->get('tid') ? $this->input->get('tid') : null;
        $start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
            $end_date = $this->sma->fld($end_date).' 23:59:59';
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }
        if ($pdf || $xls) {
            $this->db
                ->select('' . $this->db->dbprefix('payments') . '.date, ' . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . '.reference_no as purchase_ref, paid_by, amount, type')
                ->from('payments')
                ->join('sales', 'payments.sale_id=sales.id', 'left')
                ->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id')
                ->order_by('payments.date desc');

            if ($user) {
                $this->db->where('payments.created_by', $user);
            }
            if ($card) {
                $this->db->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->db->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->db->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->db->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->db->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->db->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($purchase_ref) {
                $this->db->like('purchases.reference_no', $purchase_ref, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('payments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('payments_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('payment_reference'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sale_reference'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchase_reference'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('paid_by'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('type'));

                $row   = 2;
                $total = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->payment_ref);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->purchase_ref);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->type);
                    if ($data_row->type == 'returned' || $data_row->type == 'sent') {
                        $total -= $data_row->amount;
                    } else {
                        $total += $data_row->amount;
                    }
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('payments_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d %T') as date, " . $this->db->dbprefix('payments') . '.reference_no as payment_ref, ' . $this->db->dbprefix('sales') . '.reference_no as sale_ref, ' . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, type, {$this->db->dbprefix('payments')}.id as id")
                ->from('payments')
                ->join('sales', 'payments.sale_id=sales.id', 'left')
                ->join('purchases', 'payments.purchase_id=purchases.id', 'left')
                ->group_by('payments.id');

            if ($user) {
                $this->datatables->where('payments.created_by', $user);
            }
            if ($card) {
                $this->datatables->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->datatables->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->datatables->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->datatables->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->datatables->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->datatables->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($purchase_ref) {
                $this->datatables->like('purchases.reference_no', $purchase_ref, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('payments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }

    public function getProductsReport($pdf = null, $xls = null)
    {
        //$this->sma->checkPermissions('products', true);
		if(!$this->site->IsPermissions('products-reports')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{

        $product     = $this->input->get('product') ? $this->input->get('product') : null;
        $category    = $this->input->get('category') ? $this->input->get('category') : null;
        $brand       = $this->input->get('brand') ? $this->input->get('brand') : null;
        $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : null;
        $warehouse   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $product_type   = $this->input->get('product_type') ? $this->input->get('product_type') : null;
		//$this->sma->print_arrays($product_type);


        //$type   = "combo";
        $cf1         = $this->input->get('cf1') ? $this->input->get('cf1') : null;
        $cf2         = $this->input->get('cf2') ? $this->input->get('cf2') : null;
        $cf3         = $this->input->get('cf3') ? $this->input->get('cf3') : null;
        $cf4         = $this->input->get('cf4') ? $this->input->get('cf4') : null;
        $cf5         = $this->input->get('cf5') ? $this->input->get('cf5') : null;
        $cf6         = $this->input->get('cf6') ? $this->input->get('cf6') : null;
        $start_date  = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date    = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        //$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        $pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        // WHERE p.status != 'pending' AND p.status != 'ordered'
        $sp = '( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price) totalSale, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';


        $ex = '( SELECT c.vehicle_id,sum(e.total) as expTotal from ' . $this->db->dbprefix('cars_vehicles') . ' c JOIN ' . $this->db->dbprefix('sale_items') . ' s JOIN ' . $this->db->dbprefix('expenses') . ' e on (c.vehicle_id=e.vehicle_id or s.sale_id=e.sale_id) and s.product_id=c.vehicle_id ';


        $wp = '( SELECT wp.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp ';
        //$wpcombo = '( SELECT wpcombo.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp JOIN ' . $this->db->dbprefix('combo_items') . '  wpcombo on wpcombo.item_id = wp.product_id ';
        $wpo = '( SELECT wpo.product_id, SUM( (CASE WHEN wpo.quantity> 0 THEN (wpo.avg_cost * wpo.quantity) ELSE 0 END) ) balacneValue, SUM( wpo.quantity ) balacneQty from ' . $this->db->dbprefix('warehouses_products') . ' wpo ';
        if ($start_date || $warehouse) {
            $sp .= ' WHERE ';
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " AND p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
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
        $pp .= ' GROUP BY pi.product_id ) PCosts';
        $sp .= ' GROUP BY si.product_id ) PSales';
        $ex .= ' GROUP BY c.vehicle_id ) EXP';
        $wp .= ' GROUP BY wp.product_id ) WarehousesProducts';
        //$wpcombo .= ' GROUP BY wpcombo.product_id ) ComboItems';
        $wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';
        if ($pdf || $xls) {
            $this->db
               ->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . ".name,
				COALESCE( PCosts.purchasedQty, 0 ) as purchase_quantity,
                COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
                COALESCE( PSales.soldQty, 0 ) as sales_quantity,
                COALESCE( PSales.totalSale, 0 ) as sold,
                (COALESCE( PSales.totalGoodsCost, 0 ) ) as totalGoodsCost,
                (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  )) as Profit,
                COALESCE( WarehousesProductsOrignal.balacneQty, 0 ) as balance_qty,
				COALESCE( WarehousesProductsOrignal.balacneValue, 0 ) as balance,
				{$this->db->dbprefix('products')}.id as id", false)
                ->from('products')
                ->join($sp, 'products.id = PSales.product_id', 'left')
                ->join($pp, 'products.id = PCosts.product_id', 'left')
				->join($wp, 'products.id = WarehousesProducts.product_id', 'left')
				//->join($wpcombo, 'products.id = ComboItems.product_id', 'left')
				->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left')
				->group_by('products.code');

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
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }
                $this->excel->getActiveSheet()->setTitle(lang('products_report'));
                 $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('products_report'));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $styleArray = array(
                            'font' => array(
                                'bold' => true,
                                'size' => 20,
                                'name'  => 'Cairo-Bold'
                                            )

                                    );
                           $fillColor = array(

                                     'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'f9f9f9')
                                        )
                                      );

                              $Font11Bold = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 11,
                                    'name'  => 'Cairo-Bold'
                                                )

                              );
                $styleArrayFillGreen = array(

                 'fill' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5cb85c')
                    ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )
                 );

              $styleArrayFillRed = array(

                 'fill' => array(
                   'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'd9534f')
                    ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )
              );
                    $styleArrayFillYellow = array(

                     'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'f0ad4e')
                        ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )

                  );

                   $styleArrayFillBlu = array(

                     'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '5bc0de')
                        ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )

                  );
                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                         $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                        $row++;


                $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('purchase_quantity'));
                $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('purchased_total'));
                $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('sales_quantity'));
                $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('sold_total'));
                $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('goods_cost'));
                $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('profit_loss'));
                $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('stock_in_hand_qty'));
                $this->excel->getActiveSheet()->SetCellValue('J'.$row, lang('stock_in_hand'));
                                     $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($fillColor);
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($Font11Bold);


                $row ++;
                $TotalPurchase = 0;
                $purchase_quantity = 0;
                $totalGoodsCost = 0;
                $sold = 0;
                $sales_quantity = 0;
                $balance_qty = 0;
                $balance = 0;
                $pl   = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->purchase_quantity);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->sales_quantity);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->sold);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->totalGoodsCost);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->Profit);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->balance_qty);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->balance);
                    $purchase_quantity += $data_row->purchase_quantity;
                    $TotalPurchase += $data_row->TotalPurchase;
                    $sales_quantity += $data_row->sales_quantity;
                    $sold += $data_row->sold;
                    $totalGoodsCost += $data_row->totalGoodsCost;
                    $balance_qty += $data_row->balance_qty;
                    $balance += $data_row->balance;
                    $pl   += $data_row->Profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('C' . $row . ':I' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $purchase_quantity);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $TotalPurchase);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sales_quantity);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sold);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $totalGoodsCost);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $pl);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $balance_qty);
				$this->excel->getActiveSheet()->SetCellValue('J' . $row, $balance);


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('products_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {

            //COALESCE( WarehousesProductsOrignal.balacneQty, 0 ) as balance_qty,
			//COALESCE( WarehousesProductsOrignal.balacneValue, 0 ) as balance,
            //COALESCE( PCosts.purchasedQty, 0 ) as purchase_quantity,



            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('cars_vehicles') . '.chesis_no as code, ' . $this->db->dbprefix('cars_vehicles') . ".name,
				COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
                COALESCE( EXP.expTotal, 0 ) as sales_quantity,
                COALESCE( PSales.totalSale, 0 ) as sold,
                (COALESCE( PSales.totalGoodsCost, 0 ) ) as totalGoodsCost,
                (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  ) - (COALESCE( EXP.expTotal, 0 )  )) as Profit,
                {$this->db->dbprefix('cars_vehicles')}.vehicle_id as id", false)
                ->from('cars_vehicles')
                ->join($sp, 'cars_vehicles.vehicle_id = PSales.product_id', 'left')
                ->join($ex, 'cars_vehicles.vehicle_id = EXP.vehicle_id', 'left')
                ->join($pp, 'cars_vehicles.vehicle_id = PCosts.product_id', 'left')
				->join($wp, 'cars_vehicles.vehicle_id = WarehousesProducts.product_id', 'left')
				//->join($wpcombo, 'products.id = ComboItems.product_id', 'left')
				->join($wpo, 'cars_vehicles.vehicle_id = WarehousesProductsOrignal.product_id', 'left')
				->group_by('cars_vehicles.chesis_no');

            if ($product) {
                $this->datatables->where($this->db->dbprefix('products') . '.id', $product);
            }
            if ($cf1) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf1', $cf1);
            }
            if ($cf2) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf2', $cf2);
            }
            if ($cf3) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf3', $cf3);
            }
            if ($cf4) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf4', $cf4);
            }
            if ($cf5) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf5', $cf5);
            }
            if ($cf6) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf6', $cf6);
            }
            if ($category) {
                $this->datatables->where($this->db->dbprefix('products') . '.category_id', $category);
            }
            if ($subcategory) {
                $this->datatables->where($this->db->dbprefix('products') . '.subcategory_id', $subcategory);
            }
            if ($product_type) {
                $this->datatables->where($this->db->dbprefix('products') . '.type', $product_type);
            }
            if ($brand) {
                $this->datatables->where($this->db->dbprefix('products') . '.brand', $brand);
            }

            echo $this->datatables->generate();
        }
		}
    }
















    public function getSalesTransReport($print = null, $xls = null)
    {
        //$this->sma->checkPermissions('sales', true);
        $product      = $this->input->get('product') ? $this->input->get('product') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $customer     = $this->input->get('customer') ? $this->input->get('customer') : null;
        $biller       = $this->input->get('biller') ? $this->input->get('biller') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $category    = $this->input->get('category') ? $this->input->get('category') : null;
        $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : null;
		$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $serial       = $this->input->get('serial') ? $this->input->get('serial') : null;

		//$this->sma->print_arrays($reference_no);





        if ($start_date) {
            //$start_date = $this->sma->fld($start_date);
            //$end_date   = $this->sma->fld($end_date);
            $start_date = trim($this->sma->fld($start_date)).' 00:00:00';
            $end_date = trim($this->sma->fld($end_date)).' 23:59:59';
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }
//$this->sma->print_arrays($start_date);
        //$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        //$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        // WHERE p.status != 'pending' AND p.status != 'ordered'
        //$sp = '( SELECT  DATE_FORMAT(date, "%Y-%m-%d %T") as solddate, si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.unit_quantity ) totalSale, SUM( si.item_tax ) totalTax, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';
        $sp = '( SELECT  DATE_FORMAT(date, "%Y-%m-%d %T") as solddate, si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.unit_quantity ) totalSale, SUM( si.item_tax ) totalTax, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';
        //$wp = '( SELECT wp.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp ';
        //$wpcombo = '( SELECT wpcombo.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp JOIN ' . $this->db->dbprefix('combo_items') . '  wpcombo on wpcombo.item_id = wp.product_id ';
        //$wpo = '( SELECT wpo.product_id, SUM( (CASE WHEN wpo.quantity> 0 THEN (wpo.avg_cost * wpo.quantity) ELSE 0 END) ) balacneValue, SUM( wpo.quantity ) balacneQty from ' . $this->db->dbprefix('warehouses_products') . ' wpo ';

		if ($biller) {
            $sp .= " and s.biller_id = '{$biller}' ";
			}
            if ($customer) {
            $sp .= " and s.customer_id = '{$customer}' ";
			}
            if ($warehouse) {
               $sp .= " and si.warehouse_id = '{$warehouse}' ";

            }
            if ($reference_no) {
            $sp .= " and s.reference_no = '{$reference_no}' ";
				}

				if ($user) {
            $sp .= " and s.created_by = '{$user}' ";
			 }

		/*if ($product ) {
                $sp .= ' WHERE ';
            }
            if ($product) {
                $sp .= " si.product_id = {$product} ";
            }*/
            if ($product) {
                $sp .= " and si.product_id = '{$product}' ";
            }

			if ($start_date && $end_date) {
                $sp .= " and s.date BETWEEN '{$start_date}' and  '{$end_date}'";
            }





        //$pp .= ' GROUP BY pi.product_id ) PCosts';
        $sp .= ' GROUP BY si.product_id ) PSales';
		//$sp .= ' GROUP BY solddate,si.product_id ) PSales';
        //$wp .= ' GROUP BY wp.product_id ) WarehousesProducts';
        //$wpcombo .= ' GROUP BY wpcombo.product_id ) ComboItems';
        //$wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';
        if ($print || $xls) {
            $this->db
                ->select($this->db->dbprefix('cars_vehicles') . '.chesis_no as code, ' . $this->db->dbprefix('cars_vehicles') . ".name,
				PSales.solddate as sales_date,
                COALESCE( PSales.soldQty, 0 ) as sales_quantity,
                COALESCE( PSales.totalSale, 0 ) as sold,
                COALESCE( PSales.totalTax, 0 ) as sales_tax,
                (COALESCE( PSales.totalSale, 0 ) + (COALESCE( PSales.totalTax, 0 )  )) as Final_Total,
                (COALESCE( PSales.totalGoodsCost, 0 ) ) as totalGoodsCost,
                (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  )) as Profit,
                {$this->db->dbprefix('cars_vehicles')}.vehicle_id as id", false)
                ->from('cars_vehicles')
                ->join($sp, 'cars_vehicles.vehicle_id = PSales.product_id and PSales.solddate is not null', 'left');


            if ($product) {
                $this->db->where($this->db->dbprefix('cars_vehicles') . '.vehicle_id', $product);
            }


            if ($start_date) {
            //    $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
                $this->db->where('PSales.solddate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
					//if($row->sales_quantity!=0)
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

			//$this->sma->print_arrays($this->data);
			if ($print) {

		$this->data['rows']       = $data;
        $this->data['start_date']       = $start_date;
        $this->data['end_date']       = $end_date;
        //$this->sma->print_arrays($this->data);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('Sales_Transaction_Report')]];
        $meta = ['page_title' => lang('Sales_Transaction_Report'), 'bc' => $bc];

        $this->page_construct('carssales/sales_trans_view', $meta, $this->data);


			}elseif ($xls) {

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);

                    if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }
                         $this->excel->getActiveSheet()->setTitle(lang('Sales_Transaction_Report'));
                         $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Sales_Transaction_Report'));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $styleArray = array(
                            'font' => array(
                                'bold' => true,
                                'size' => 20,
                                'name'  => 'Cairo-Bold'
                                            )

                                    );
                           $fillColor = array(

                                     'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'f9f9f9')
                                        )
                                      );

                              $Font11Bold = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 11,
                                    'name'  => 'Cairo-Bold'
                                                )

                              );
                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                         $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);


                        $row++;
                         $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                $this->excel->getActiveSheet()->SetCellValue('A'.$row, ($this->input->get('start_date') ?  lang('period') .' '. lang('Sales_Transaction_Report') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
               $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
                $row++;



                $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, lang('sales_quantity'));
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, lang('sold_total'));
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang('VAT_Amount'));
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, lang('goods_cost'));
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, lang('profit_loss'));
                $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($fillColor);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($Font11Bold);
                $row++;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $bQty = 0;
                $bAmt = 0;
                $pl   = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sales_date);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->sales_quantity);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->sold);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->sales_tax);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->Final_Total);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->totalGoodsCost);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->Profit);
                    //$pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->sales_quantity;
                    $bQty += $data_row->sold;
                    $pAmt += $data_row->sales_tax;
                    $sAmt += $data_row->Final_Total;
                    $bAmt += $data_row->totalGoodsCost;
                    $pl   += $data_row->Profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
                $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('total'));
                $this->excel->getActiveSheet()->getStyle('A' . $row . ':I' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                //$this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $bQty);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $bAmt);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $pl);


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('Sales_Transaction_Report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
			}
        } else {
//PSales.solddate as sales_date,

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('cars_vehicles') . '.chesis_no, ' . $this->db->dbprefix('cars_vehicles') . ".name,
				COALESCE( PSales.soldQty, 0 ) as sales_quantity,
                COALESCE( PSales.totalSale, 0 ) as sold,
                COALESCE( PSales.totalTax, 0 ) as sales_tax,
                (COALESCE( PSales.totalSale, 0 ) + (COALESCE( PSales.totalTax, 0 )  )) as Final_Total,
                (COALESCE( PSales.totalGoodsCost, 0 ) ) as totalGoodsCost,
                (COALESCE( PSales.totalSale, 0 ) - (COALESCE( PSales.totalGoodsCost, 0 )  )) as Profit,
                {$this->db->dbprefix('cars_vehicles')}.vehicle_id as id", false)
                ->from('cars_vehicles')
                ->join($sp, 'cars_vehicles.vehicle_id = PSales.product_id and PSales.solddate is not null', 'left');
                //->join($pp, 'products.id = PCosts.product_id', 'left')
				//->join($wp, 'products.id = WarehousesProducts.product_id', 'left')
				//->join($wpcombo, 'products.id = ComboItems.product_id', 'left')
				//->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left')
				//->group_by('products.code');








			if ($product) {
                $this->db->where($this->db->dbprefix('cars_vehicles') . '.vehicle_id', $product);
            }


            if ($start_date) {
            //    $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
                $this->datatables->where('PSales.solddate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }



            echo $this->datatables->generate();
        }
    }





















    public function getSalesDeliveryReport($print = null, $xls = null)
    {
      //  $this->sma->checkPermissions('sales',true,'reports');

        $product      = $this->input->get('product') ? $this->input->get('product') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $customer     = $this->input->get('customer') ? $this->input->get('customer') : null;
        $emp_id       = $this->input->get('emp_id') ? $this->input->get('emp_id') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $serial       = $this->input->get('serial') ? $this->input->get('serial') : null;

		//$this->sma->print_arrays($reference_no);





        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        //$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        //$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        // WHERE p.status != 'pending' AND p.status != 'ordered'
        //$sp = '( SELECT  DATE_FORMAT(date, "%Y-%m-%d") as solddate, si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.quantity ) totalSale, SUM( si.item_tax ) totalTax, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';

		$sp = '( SELECT  DATE_FORMAT(date, "%Y-%m-%d") as solddate, emp_id,  reference_no  ,  grand_total , customer from ' . $this->db->dbprefix('sales') . ' where emp_type=3';


		//$wp = '( SELECT wp.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp ';
        //$wpcombo = '( SELECT wpcombo.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp JOIN ' . $this->db->dbprefix('combo_items') . '  wpcombo on wpcombo.item_id = wp.product_id ';
        //$wpo = '( SELECT wpo.product_id, SUM( (CASE WHEN wpo.quantity> 0 THEN (wpo.avg_cost * wpo.quantity) ELSE 0 END) ) balacneValue, SUM( wpo.quantity ) balacneQty from ' . $this->db->dbprefix('warehouses_products') . ' wpo ';

		if ($emp_id) {
            $sp .= " and emp_id = '{$emp_id}' ";
			}
            if ($customer) {
            $sp .= " and customer_id = '{$customer}' ";
			}
            if ($warehouse) {
               $sp .= " and si.warehouse_id = '{$warehouse}' ";

            }
            if ($reference_no) {
            $sp .= " and reference_no = '{$reference_no}' ";
				}

				if ($user) {
            $sp .= " and created_by = '{$user}' ";
			 }

		/*if ($product ) {
                $sp .= ' WHERE ';
            }
            if ($product) {
                $sp .= " si.product_id = {$product} ";
            }*/





        //$pp .= ' GROUP BY pi.product_id ) PCosts';
        $sp .= ' order BY emp_id,solddate,customer ) PSales';
        //$wp .= ' GROUP BY wp.product_id ) WarehousesProducts';
        //$wpcombo .= ' GROUP BY wpcombo.product_id ) ComboItems';
        //$wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';
        if ($print || $xls) {
            $this->db
                ->select($this->db->dbprefix('resturant_emp') . '.code, ' . $this->db->dbprefix('resturant_emp') . ".name,
				PSales.solddate as sales_date,
                COALESCE( PSales.customer, 0 ) as customer,
                COALESCE( PSales.reference_no, 0 ) as reference_no,
                COALESCE( PSales.grand_total, 0 ) as grand_total,
                COALESCE( 1 ) as deviation_count,
                {$this->db->dbprefix('resturant_emp')}.id as id", false)
                ->from('resturant_emp')
                ->join($sp, 'resturant_emp.id = PSales.emp_id ', 'left');


            if ($product) {
                $this->db->where($this->db->dbprefix('products') . '.id', $product);
            }


            if ($start_date) {
            //    $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
                $this->db->where('PSales.solddate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
					//if($row->sales_quantity!=0)
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

			//$this->sma->print_arrays($data);
			if ($print) {

		$this->data['rows']       = $data;
        $this->data['start_date']       = $start_date;
        $this->data['end_date']       = $end_date;
        //$this->sma->print_arrays($this->data);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('Sales_Delivery_Report')]];
        $meta = ['page_title' => lang('Sales_Delivery_Report'), 'bc' => $bc];

        $this->page_construct('carssales/sales_delivery_report', $meta, $this->data);


			}elseif ($xls) {

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('Sales_Delivery_Report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('Stuff_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('Stuff_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('Deviation_count'));

                $row  = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $bQty = 0;
                $bAmt = 0;
                $pl   = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sales_date);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->deviation_count);
                    //$pQty += $data_row->PurchasedQty;
                    //$sQty += $data_row->sales_quantity;
                    //$bQty += $data_row->sold;
                    $pAmt += $data_row->grand_total;
                    $sAmt += $data_row->deviation_count;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('F' . $row . ':G' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                //$this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                //$this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                //$this->excel->getActiveSheet()->SetCellValue('E' . $row, $bQty);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sAmt);
                //$this->excel->getActiveSheet()->SetCellValue('H' . $row, $bAmt);
                //$this->excel->getActiveSheet()->SetCellValue('I' . $row, $pl);


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('F2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('Sales_Delivery_Report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
			}
        } else {

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('resturant_emp') . '.code, ' . $this->db->dbprefix('resturant_emp') . ".name,
				PSales.solddate as sales_date,
                COALESCE( PSales.customer, 0 ) as customer,
                COALESCE( PSales.reference_no, 0 ) as reference_no,
                COALESCE( PSales.grand_total, 0 ) as grand_total,
                COALESCE( 1 ) as يeviation_count,
                {$this->db->dbprefix('resturant_emp')}.id as id", false)
                ->from('resturant_emp')
                ->join($sp, 'resturant_emp.id = PSales.emp_id ', 'left');
                //->join($pp, 'products.id = PCosts.product_id', 'left')
				//->join($wp, 'products.id = WarehousesProducts.product_id', 'left')
				//->join($wpcombo, 'products.id = ComboItems.product_id', 'left')
				//->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left')
				//->group_by('products.code');








			if ($product) {
                $this->db->where($this->db->dbprefix('products') . '.id', $product);
            }


            if ($start_date) {
            //    $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
                $this->datatables->where('PSales.solddate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }


            echo $this->datatables->generate();
        }
    }









    public function getPurchasesReportForSupplier($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('purchases',true,'reports');


        $product      = $this->input->get('product') ? $this->input->get('product') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $supplier     = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        if ($start_date) {
            //$start_date = $this->sma->fld($start_date);
            //$end_date   = $this->sma->fld($end_date);

			$start_date = $this->sma->fld($start_date).' 00:00:00';
            $end_date = $this->sma->fld($end_date).' 23:59:59';

        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {
            $this->db
                ->select('' . $this->db->dbprefix('purchases') . '.date, reference_no, ' . $this->db->dbprefix('warehouses') . '.name as wname, supplier, GROUP_CONCAT(CONCAT(' . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '\n') as iname, (total-total_discount) as total, total_tax,(" . $this->db->dbprefix('purchases') . ".shipping+customs_fees+other_fees) as expenses, grand_total, " . $this->db->dbprefix('purchases') . '.status', false)
                ->from('purchases')
                ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
                ->group_by('purchases.id')
                ->order_by('purchases.date desc');

            if ($user) {
                $this->db->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->db->where('purchase_items.product_id', $product);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('purchase_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('Showroom'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('Car_Owner'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('Vehicles'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Total_before_VAT'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('VAT_Amount'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('expenses'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('status'));

                $row     = 2;
                $total   = 0;
                $paid    = 0;
                $balance = 0;
                $grand_total = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->supplier);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($data_row->total));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($data_row->total_tax));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($data_row->expenses));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($data_row->grand_total));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, lang($data_row->status));
                    $total   += $data_row->total;
                    $paid    += $data_row->total_tax;
                    $balance += ($data_row->expenses);
                    $grand_total += ($data_row->grand_total);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('F' . $row . ':H' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($total));
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($paid));
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($balance));
				$this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($grand_total));


                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('purchases_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            //$pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            $pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";

            $this->load->library('datatables');
            $this->datatables
                //->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", false)
                ->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, (total-total_discount), total_tax, (shipping+customs_fees+other_fees) as expenses, grand_total , {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", false)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
            // ->group_by('purchases.id');

            if ($user) {
                $this->datatables->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FPI.product_id', $product, false);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }







    public function getPurchasesReport($print = null, $xls = null)
    {
        $this->sma->checkPermissions('purchases',true,'reports');

        $product      = $this->input->get('product') ? $this->input->get('product') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $supplier     = $this->input->get('supplier') ? $this->input->get('supplier') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($print || $xls) {

			$pi = "( SELECT p.purchase_id, p.product_id,  (CASE WHEN p.quantity>0 THEN (CASE WHEN u.operator IS NOT NULL THEN (CASE WHEN u.operator='*' THEN (p.quantity_received/u.operation_value) ELSE (p.quantity_received*u.operation_value) END) ELSE p.quantity_received END) ELSE (CASE WHEN u.operator IS NOT NULL THEN (CASE WHEN u.operator='*' THEN (p.quantity/u.operation_value) ELSE (p.quantity*u.operation_value) END) ELSE p.quantity END) END)  as item_nane, u.name as product_unit_code, (CASE WHEN p.quantity>0 THEN (CASE WHEN u.operator IS NOT NULL THEN (CASE WHEN u.operator='*' THEN (p.real_unit_cost*u.operation_value) ELSE (p.real_unit_cost/u.operation_value) END) ELSE p.real_unit_cost END) ELSE (CASE WHEN u.operator IS NOT NULL THEN (CASE WHEN u.operator='*' THEN (p.unit_cost*u.operation_value) ELSE (p.unit_cost/u.operation_value) END) ELSE p.unit_cost END) END) as unit_price, p.item_discount as item_discount, p.item_tax as item_tax, p.subtotal as subtotal from {$this->db->dbprefix('purchase_items')} p LEFT JOIN {$this->db->dbprefix('units')} u on u.id = p.product_unit_id ";

			if ($product) {
                $pi .= " WHERE p.product_id = {$product} ";
            }
            $pi .= " GROUP BY p.purchase_id ) FPI";

            //$this->load->library('datatables');
            /*$this->datatables
                ->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, FPI.product_unit_code, FPI.unit_price, FPI.item_discount, FPI.item_tax, FPI.subtotal, {$this->db->dbprefix('purchases')}.id as id", false)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
            // ->group_by('purchases.id');
			*/


            $this->db
                ->select('' . $this->db->dbprefix('purchases') . '.date, reference_no, ' . $this->db->dbprefix('warehouses') . '.name as wname, supplier,  (FPI.item_nane) as iname, FPI.product_unit_code, FPI.unit_price, FPI.item_discount, FPI.item_tax, FPI.subtotal, ' . $this->db->dbprefix('purchases') . '.status', false)
                ->from('purchases')
                ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
				->join($pi, 'FPI.purchase_id=purchases.id', 'left')

                ->group_by('purchases.id')
                ->order_by('purchases.date desc');

            if ($user) {
                $this->db->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->db->where('purchase_items.product_id', $product);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }



			    if ($print) {

		$this->data['rows']       = $data;
		$this->data['product']       = $this->site->getProductByID($product);
		$this->data['warehouse']       = $warehouse ? $this->site->getWarehouseByID($warehouse)->name : lang('all');


        $this->data['start_date']       = $start_date;
        $this->data['end_date']       = $end_date;
        //$this->sma->print_arrays($this->data);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('purchases'), 'page' => lang('purchases')], ['link' => '#', 'page' => lang('purchases_report')]];
        $meta = ['page_title' => lang('purchases_report'), 'bc' => $bc];

        $this->page_construct('purchases/purchases_details_view', $meta, $this->data);


			}elseif ($xls) {

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle($this->site->getProductByID($product)->name);
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('Showroom'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('Car_Owner'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('Unit_Price'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('Discount'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('VAT_Amount'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('Total_Value'));

                $row     = 2;
                $subtotal   = 0;
                $item_discount    = 0;
                $item_tax = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->supplier);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->product_unit_code);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->unit_price);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->item_discount);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->item_tax);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->subtotal);
                    $subtotal   += $data_row->subtotal;
                    $item_discount    += $data_row->item_discount;
                    $item_tax += $data_row->item_tax;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('H' . $row . ':J' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $item_discount);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $item_tax);
				$this->excel->getActiveSheet()->SetCellValue('J' . $row, $subtotal);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'purchase_report '.$this->site->getProductByID($product)->name;
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
			}
        } else {
            //$pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            $pi = "( SELECT p.purchase_id, p.product_id,  (CASE WHEN p.quantity>0 THEN (CASE WHEN u.operator IS NOT NULL THEN (CASE WHEN u.operator='*' THEN (p.quantity_received/u.operation_value) ELSE (p.quantity_received*u.operation_value) END) ELSE p.quantity_received END) ELSE (CASE WHEN u.operator IS NOT NULL THEN (CASE WHEN u.operator='*' THEN (p.quantity/u.operation_value) ELSE (p.quantity*u.operation_value) END) ELSE p.quantity END) END)  as item_nane, u.name as product_unit_code, (CASE WHEN p.quantity>0 THEN (CASE WHEN u.operator IS NOT NULL THEN (CASE WHEN u.operator='*' THEN (p.real_unit_cost*u.operation_value) ELSE (p.real_unit_cost/u.operation_value) END) ELSE p.real_unit_cost END) ELSE (CASE WHEN u.operator IS NOT NULL THEN (CASE WHEN u.operator='*' THEN (p.unit_cost*u.operation_value) ELSE (p.unit_cost/u.operation_value) END) ELSE p.unit_cost END) END) as unit_price, p.item_discount as item_discount, p.item_tax as item_tax, p.subtotal as subtotal from {$this->db->dbprefix('purchase_items')} p LEFT JOIN {$this->db->dbprefix('units')} u on u.id = p.product_unit_id ";

			if ($product) {
                $pi .= " WHERE p.product_id = {$product} ";
            }
            $pi .= " GROUP BY p.purchase_id ) FPI";

            $this->load->library('datatables');
            $this->datatables
                ->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('warehouses')}.name as wname, supplier, (FPI.item_nane) as iname, FPI.product_unit_code, FPI.unit_price, FPI.item_discount, FPI.item_tax, FPI.subtotal, {$this->db->dbprefix('purchases')}.id as id", false)
                ->from('purchases')
                ->join($pi, 'FPI.purchase_id=purchases.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
            // ->group_by('purchases.id');

            if ($user) {
                $this->datatables->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FPI.product_id', $product, false);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }

    public function getQuantityAlerts($warehouse_id = null, $pdf = null, $xls = null)
    {
        //$this->sma->checkPermissions('quantity_alerts', true);
				if(!$this->site->IsPermissions('products-reports')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{
        if (!$this->Owner && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        if ($pdf || $xls) {
            if ($warehouse_id) {
                $this->db
                    ->select('products.image as image, products.code, products.name, warehouses_products.quantity, alert_quantity')
                    ->from('products')->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
                    ->where('alert_quantity > warehouses_products.quantity', null)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('track_quantity', 1)
                    ->order_by('products.code desc');
            } else {
                $this->db
                    ->select('image, code, name, quantity, alert_quantity')
                    ->from('products')
                    ->where('alert_quantity > quantity', null)
                    ->where('track_quantity', 1)
                    ->order_by('code desc');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('product_quantity_alerts'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('quantity'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('alert_quantity'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->quantity);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->alert_quantity);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('product_quantity_alerts');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            if ($warehouse_id) {
                $this->datatables
                    ->select('image, code, name, wp.quantity, alert_quantity')
                    ->from('products')
                    ->join("( SELECT * from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left')
                    ->where('alert_quantity >= wp.quantity', null)
                    ->or_where('wp.quantity', null)
                    ->where('track_quantity', 1)
                    ->group_by('products.id');
            } else {
                $this->datatables
                    ->select('image, code, name, quantity, alert_quantity')
                    ->from('products')
                    ->where('alert_quantity >= quantity', null)
                    ->or_where('quantity', null)
                    ->where('track_quantity', 1);
            }

            echo $this->datatables->generate();
        }
		}
    }


	public function getQuotesReportForCustomer($pdf = null, $xls = null)
    {
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = null;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = null;
        }
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = null;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = null;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = null;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = null;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = null;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = null;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
            $end_date = $this->sma->fld($end_date).' 23:59:59';
        }
        if ($pdf || $xls) {
            $this->db
                ->select('date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(' . $this->db->dbprefix('quote_items') . ".product_name, ' (', " . $this->db->dbprefix('quote_items') . ".quantity, ')') SEPARATOR '<br>') as iname, grand_total, status", false)
                ->from('quotes')
                ->join('quote_items', 'quote_items.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            if ($user) {
                $this->db->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->db->where('quote_items.product_id', $product);
            }
            if ($biller) {
                $this->db->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('quotes') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('quotes_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->status);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('quotes_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $qi = "( SELECT quote_id, product_id, GROUP_CONCAT(CONCAT({$this->db->dbprefix('quote_items')}.product_name, '__', {$this->db->dbprefix('quote_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('quote_items')} ";
            if ($product) {
                $qi .= " WHERE {$this->db->dbprefix('quote_items')}.product_id = {$product} ";
            }
            $qi .= " GROUP BY {$this->db->dbprefix('quote_items')}.quote_id ) FQI";
            $this->load->library('datatables');
            $this->datatables
                ->select("date, reference_no, biller, customer, FQI.item_nane as iname, grand_total, status, {$this->db->dbprefix('quotes')}.id as id", false)
                ->from('quotes')
                ->join($qi, 'FQI.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            if ($user) {
                $this->datatables->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FQI.product_id', $product, false);
            }
            if ($biller) {
                $this->datatables->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('quotes') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }



    public function getQuotesReport($print = null, $xls = null)
    {
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = null;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = null;
        }
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = null;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = null;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = null;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = null;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = null;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = null;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if ($print || $xls) {
            $this->db
                //->select('date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(' . $this->db->dbprefix('quote_items') . ".product_name, ' (', " . $this->db->dbprefix('quote_items') . ".quantity, ')') SEPARATOR '<br>') as iname, grand_total, status", false)
                ->select('date, reference_no, biller, customer, ' . $this->db->dbprefix('quote_items') . ".unit_quantity as iname," . $this->db->dbprefix('units') . ".name as product_unit_code," . $this->db->dbprefix('quote_items') . ".unit_price as unit_price, " . $this->db->dbprefix('quote_items') . ".item_discount as item_discount, " . $this->db->dbprefix('quote_items') . ".item_tax as item_tax, " . $this->db->dbprefix('quote_items') . ".subtotal as subtotal, status", false)
                ->from('quotes')
                ->join('quote_items', 'quote_items.quote_id=quotes.id', 'left')
                ->join('units', 'units.id = quote_items.product_unit_id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            if ($user) {
                $this->db->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->db->where('quote_items.product_id', $product);
            }
            if ($biller) {
                $this->db->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('quotes') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }




            if ($print) {

		$this->data['rows']       = $data;
		$this->data['product']       = $this->site->getProductByID($product);
		$this->data['warehouse']       = $warehouse ? $this->site->getWarehouseByID($warehouse)->name : lang('all');

        $this->data['start_date']       = $start_date;
        $this->data['end_date']       = $end_date;
        //$this->sma->print_arrays($this->data);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('quotes'), 'page' => lang('quotes')], ['link' => '#', 'page' => lang('quotes_report')]];
        $meta = ['page_title' => lang('quotes'), 'bc' => $bc];

        $this->page_construct('quotes/quotes_details_view', $meta, $this->data);


			}elseif ($xls) {
			if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle($this->site->getProductByID($product)->name);
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('Unit_Price'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('Discount'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('VAT_Amount'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('Total_Value'));

                $row     = 2;
                $subtotal   = 0;
                $item_discount    = 0;
                $item_tax = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->product_unit_code);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->unit_price);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->item_discount);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->item_tax);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->subtotal);
                    $subtotal   += $data_row->subtotal;
                    $item_discount    += $data_row->item_discount;
                    $item_tax += $data_row->item_tax;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('H' . $row . ':J' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                //$this->excel->getActiveSheet()->SetCellValue('G' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $item_discount);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $item_tax);
				$this->excel->getActiveSheet()->SetCellValue('J' . $row, $subtotal);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'quotes_report '.$this->site->getProductByID($product)->name;
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
			$this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
		}
        } else {
            $qi = "( SELECT quote_id, product_id, GROUP_CONCAT(CONCAT({$this->db->dbprefix('quote_items')}.product_name, '__', {$this->db->dbprefix('quote_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('quote_items')} ";
            $qi = "( SELECT q.quote_id, q.product_id, q.unit_quantity as item_nane, u.name as product_unit_code, q.unit_price as unit_price, q.item_discount as item_discount, q.item_tax as item_tax, q.subtotal as subtotal from {$this->db->dbprefix('quote_items')} q LEFT JOIN {$this->db->dbprefix('units')} u on u.id = q.product_unit_id ";

			if ($product) {
                $qi .= " WHERE q.product_id = {$product} ";
            }
            $qi .= " GROUP BY q.quote_id ) FQI";
            $this->load->library('datatables');
            $this->datatables
                ->select("date, reference_no, biller, customer, FQI.item_nane as iname, FQI.product_unit_code, FQI.unit_price, FQI.item_discount, FQI.item_tax, FQI.subtotal, {$this->db->dbprefix('quotes')}.id as id", false)
                ->from('quotes')
                ->join($qi, 'FQI.quote_id=quotes.id', 'left')
                ->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')
                ->group_by('quotes.id');

            if ($user) {
                $this->datatables->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FQI.product_id', $product, false);
            }
            if ($biller) {
                $this->datatables->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('quotes') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }

    public function getRrgisterlogs($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('register',true,'reports');

        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = null;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = null;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = null;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {
            $this->db
                ->select('date, closed_at, CONCAT(' . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' (', users.email, ')') as user, cash_in_hand, total_cc_slips, total_cheques, total_cash, total_cc_slips_submitted, total_cheques_submitted,total_cash_submitted, note", false)
                ->from('pos_register')
                ->join('users', 'users.id=pos_register.user_id', 'left')
                ->order_by('date desc');
            //->where('status', 'close');

            if ($user) {
                $this->db->where('pos_register.user_id', $user);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('register_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('open_time'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('close_time'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('user'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('cash_in_hand'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('cc_slips'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('cheques'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('total_cash'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('cc_slips_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('cheques_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('total_cash_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('K1', lang('note'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->closed_at);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->user);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->cash_in_hand);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total_cc_slips);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->total_cheques);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->total_cash);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->total_cc_slips_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_cheques_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->total_cash_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->note);
                    if ($data_row->total_cash_submitted < $data_row->total_cash || $data_row->total_cheques_submitted < $data_row->total_cheques || $data_row->total_cc_slips_submitted < $data_row->total_cc_slips) {
                        $this->excel->getActiveSheet()->getStyle('A' . $row . ':K' . $row)->applyFromArray(
                            ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'F2DEDE']]]
                                );
                    }
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('register_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select('date, closed_at, CONCAT(' . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, '<br>', " . $this->db->dbprefix('users') . ".email) as user, cash_in_hand, CONCAT(total_cc_slips, ' (', total_cc_slips_submitted, ')'), CONCAT(total_cheques, ' (', total_cheques_submitted, ')'), CONCAT(total_cash, ' (', total_cash_submitted, ')'), note", false)
                ->from('pos_register')
                ->join('users', 'users.id=pos_register.user_id', 'left');

            if ($user) {
                $this->datatables->where('pos_register.user_id', $user);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }


	    public function getSalesReportForCustomer($pdf = null, $xls = null)
    {
       // $this->sma->checkPermissions('sales',true,'reports');

        $product      = $this->input->get('product') ? $this->input->get('product') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $customer     = $this->input->get('customer') ? $this->input->get('customer') : null;
		//$this->sma->print_arrays($customer);
        $biller       = $this->input->get('biller') ? $this->input->get('biller') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $serial       = $this->input->get('serial') ? $this->input->get('serial') : null;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
            $end_date = $this->sma->fld($end_date).' 23:59:59';

        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {
            $this->db
                ->select('date, reference_no, biller, customer, cars_showrooms.name as iname, grand_total, paid, discount_allowance, payment_status', false)
                ->from('sales')
                ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
                ->join('cars_showrooms', 'cars_showrooms.id=sales.warehouse_id', 'left')
                ->group_by('sales.id')
                ->order_by('sales.date desc');

            if ($user) {
                $this->db->where('sales.created_by', $user);
            }
            if ($product) {
                $this->db->where('sale_items.product_id', $product);
            }
            if ($serial) {
                $this->db->like('sale_items.serial_no', $serial);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('Showroom'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('Discount_Allowance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('payment_status'));

                $row     = 2;
                $total   = 0;
                $paid    = 0;
                $discount_allowance = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->discount_allowance);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, ($data_row->grand_total - ($data_row->paid + $data_row->discount_allowance)));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, lang($data_row->payment_status));
                    $total   += $data_row->grand_total;
                    $paid    += $data_row->paid;
                    $discount_allowance    += $data_row->discount_allowance;
                    $balance += ($data_row->grand_total - ($data_row->paid + discount_allowance));
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('F' . $row . ':J' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $discount_allowance);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('sales_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $si = "( SELECT sale_id, product_id, serial_no, warehouse_id as item_nane from {$this->db->dbprefix('sale_items')} ";
            if ($product || $serial) {
                $si .= ' WHERE ';
            }
            if ($product) {
                $si .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
            }
            if ($product && $serial) {
                $si .= ' AND ';
            }
            if ($serial) {
                $si .= " {$this->db->dbprefix('sale_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $si .= " GROUP BY {$this->db->dbprefix('sale_items')}.sale_id ) FSI";
            $this->load->library('datatables');
            $this->datatables
                ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, cars_showrooms.name as iname, grand_total, paid, discount_allowance, (grand_total-(paid + discount_allowance)) as balance, payment_status, {$this->db->dbprefix('sales')}.id as id", false)
                ->from('sales')
                ->join($si, 'FSI.sale_id=sales.id', 'left')
                ->join('cars_showrooms', 'cars_showrooms.id=sales.warehouse_id', 'left');
                //->join('companies', 'companies.id=sales.customer_id', 'left');
            // ->group_by('sales.id');

            if ($user) {
                $this->datatables->where('sales.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }



    public function getSalesReport($print = null, $xls = null)
    {
       // $this->sma->checkPermissions('sales',true,'reports');
        $product      = $this->input->get('product') ? $this->input->get('product') : null;
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $customer     = $this->input->get('customer') ? $this->input->get('customer') : null;
        $biller       = $this->input->get('biller') ? $this->input->get('biller') : null;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $serial       = $this->input->get('serial') ? $this->input->get('serial') : null;


		if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($print || $xls) {
            $this->db
                ->select('date, reference_no, biller, customer, ' . $this->db->dbprefix('sale_items') . ".unit_quantity as iname," . $this->db->dbprefix('units') . ".name as product_unit_code,(" . $this->db->dbprefix('sale_items') . ".net_unit_price + (" . $this->db->dbprefix('sale_items') . ".item_discount / " . $this->db->dbprefix('sale_items') . ".unit_quantity)) as unit_price, " . $this->db->dbprefix('sale_items') . ".item_discount as item_discount, " . $this->db->dbprefix('sale_items') . ".item_tax as item_tax, " . $this->db->dbprefix('sale_items') . ".subtotal as subtotal, payment_status", false)
                ->from('sales')
                ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
                ->join('units', 'units.id = sale_items.product_unit_id', 'left')
                ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')
                //->group_by('sales.id')
				->group_by('sale_items.sale_id,sale_items.product_unit_id')
                ->order_by('sales.date desc');

            if ($user) {
                $this->db->where('sales.created_by', $user);
            }
            if ($product) {
                $this->db->where('sale_items.product_id', $product);
            }
            if ($serial) {
                $this->db->like('sale_items.serial_no', $serial);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }




            if ($print) {

		$this->data['rows']       = $data;
		$this->data['product']       = $this->site->getProductByID($product);
		$this->data['warehouse']       = $warehouse ? $this->site->getWarehouseByID($warehouse)->name : lang('all');

        $this->data['start_date']       = $start_date;
        $this->data['end_date']       = $end_date;
        //$this->sma->print_arrays($this->data);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carssales'), 'page' => lang('sales')], ['link' => '#', 'page' => lang('sales_report')]];
        $meta = ['page_title' => lang('sales_report'), 'bc' => $bc];

        $this->page_construct('carssales/sales_details_view', $meta, $this->data);


			}elseif ($xls) {
			if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle($this->site->getProductByID($product)->name);
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('Unit_Price'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('Discount'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('VAT_Amount'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('Total_Value'));

                $row     = 2;
                $subtotal   = 0;
                $item_discount    = 0;
                $item_tax = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->product_unit_code);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->unit_price);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->item_discount);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->item_tax);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->subtotal);
                    $subtotal   += $data_row->subtotal;
                    $item_discount    += $data_row->item_discount;
                    $item_tax += $data_row->item_tax;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('H' . $row . ':J' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                //$this->excel->getActiveSheet()->SetCellValue('G' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $item_discount);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $item_tax);
				$this->excel->getActiveSheet()->SetCellValue('J' . $row, $subtotal);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'sales_report '.$this->site->getProductByID($product)->name;
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
			$this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
		}

        } else {

            //$si = "( SELECT sale_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('sale_items')}.product_name, '__', {$this->db->dbprefix('sale_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('sale_items')} ";
            $si = "( SELECT s.sale_id, s.product_id, s.serial_no, s.unit_quantity as item_nane, u.name as product_unit_code, (s.net_unit_price + (s.item_discount / s.unit_quantity)) as unit_price, s.item_discount as item_discount, s.item_tax as item_tax, s.subtotal as subtotal from {$this->db->dbprefix('sale_items')} s LEFT JOIN {$this->db->dbprefix('units')} u on u.id = s.product_unit_id ";
            if ($product || $serial) {
                $si .= ' WHERE ';
            }
            if ($product) {
                $si .= " s.product_id = {$product} ";
            }
            if ($product && $serial) {
                $si .= ' AND ';
            }
            if ($serial) {
                $si .= " s.serial_no LIKe '%{$serial}%' ";
            }

            $si .= " GROUP BY s.sale_id,s.product_unit_id ) FSI";
            $this->load->library('datatables');
            $this->datatables
                ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, FSI.item_nane as iname, FSI.product_unit_code, FSI.unit_price, FSI.item_discount, FSI.item_tax, FSI.subtotal, {$this->db->dbprefix('sales')}.id as id", false)
                ->from('sales')
                ->join($si, 'FSI.sale_id=sales.id', 'left')
                ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left');
            // ->group_by('sales.id');

            if ($user) {
                $this->datatables->where('sales.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }


















    public function getSuppliers($pdf = null, $xls = null)
    {
      //  $this->sma->checkPermissions('suppliers',true,'reports');


        if ($pdf || $xls) {
            $this->db
                ->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count({$this->db->dbprefix('purchases')}.id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", false)
                ->from('companies')
                ->join('purchases', 'purchases.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->order_by('companies.company asc')
                ->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('Car_Owners_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_purchases'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Total_amount_purchases'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($data_row->total));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($data_row->total_amount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($data_row->balance));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = lang('suppliers_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $p = '( SELECT supplier_id, count(' . $this->db->dbprefix('purchases') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('purchases')} GROUP BY {$this->db->dbprefix('purchases')}.supplier_id ) FP";

            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('companies') . '.id as id, company, name, phone, email, FP.total, FP.total_amount, FP.paid, FP.balance', false)
                ->from('companies')
                ->join($p, 'FP.supplier_id=companies.id')
                ->where('companies.group_name', 'supplier')
                ->group_by('companies.id')
                ->add_column('Actions', "<div class='text-center'><a class=\"tip\" title='" . lang('view_report') . "' href='" . admin_url('carsreports/supplier_report/$1') . "'><span class='label label-primary'>" . lang('view_report') . '</span></a></div>', 'id')
                ->unset_column('id');
            echo $this->datatables->generate();
        }
    }

    public function getTransfersReport($print = null, $xls = null)
    {
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = null;
        }

        if ($print || $xls) {
            $this->db
                //->select($this->db->dbprefix('transfers') . '.date, transfer_no, (CASE WHEN ' . $this->db->dbprefix('transfers') . ".status = 'completed' THEN  GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '<br>') ELSE GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('transfer_items') . ".product_name, ' (', " . $this->db->dbprefix('transfer_items') . ".quantity, ')') SEPARATOR '<br>') END) as iname, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, grand_total, " . $this->db->dbprefix('transfers') . '.status')
                ->select($this->db->dbprefix('transfers') . '.date, transfer_no, '.$this->db->dbprefix('purchase_items').'.quantity as iname, '.$this->db->dbprefix('units').'.name as product_unit_code, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, '.$this->db->dbprefix('purchase_items').'.unit_cost as unit_cost, '.$this->db->dbprefix('purchase_items').'.subtotal as subtotal, '.$this->db->dbprefix('transfers').'.id as id')
				->from('transfers')
                ->join('transfer_items', 'transfer_items.transfer_id=transfers.id', 'left')
                ->join('purchase_items', 'purchase_items.transfer_id=transfers.id', 'left')
                ->join('units', 'units.id = purchase_items.product_unit_id', 'left')
                ->group_by('transfers.id');
                $this->db->where($this->db->dbprefix('transfers') . '.status ', 'completed');
            if ($product) {
                $this->db->where($this->db->dbprefix('purchase_items') . '.product_id', $product);
                $this->db->or_where($this->db->dbprefix('transfer_items') . '.product_id', $product);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

			if ($print) {

		$this->data['rows']       = $data;
		$this->data['product']       = $this->site->getProductByID($product);
		$this->data['warehouse']       = lang('all');

        $this->data['start_date']       = $start_date;
        $this->data['end_date']       = $end_date;
        //$this->sma->print_arrays($this->data);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('transfers'), 'page' => lang('transfers')], ['link' => '#', 'page' => lang('transfers_report')]];
        $meta = ['page_title' => lang('transfers_report'), 'bc' => $bc];

        $this->page_construct('transfers/transfers_details_view', $meta, $this->data);


			}elseif ($xls) {

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle($this->site->getProductByID($product)->name);
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('transfer_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('unit'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('Showroom') . ' (' . lang('from') . ')');
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Showroom') . ' (' . lang('to') . ')');
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('cost'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('Total_Value'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->transfer_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->product_unit_code);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->fname . ' (' . $data_row->fcode . ')');
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->tname . ' (' . $data_row->tcode . ')');
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->unit_cost);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->subtotal);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                $filename = 'transfers_report '.$this->site->getProductByID($product)->name;
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
			}
        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select("{$this->db->dbprefix('transfers')}.date, transfer_no, {$this->db->dbprefix('purchase_items')}.quantity as iname, {$this->db->dbprefix('units')}.name as product_unit_code, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, {$this->db->dbprefix('purchase_items')}.unit_cost as unit_cost, {$this->db->dbprefix('purchase_items')}.subtotal as subtotal, {$this->db->dbprefix('transfers')}.id as id", false)
                ->from('transfers')
                ->join('transfer_items', 'transfer_items.transfer_id=transfers.id', 'left')
                ->join('purchase_items', 'purchase_items.transfer_id=transfers.id', 'left')
				->join('units', 'units.id = purchase_items.product_unit_id', 'left')

                ->group_by('transfers.id');
                $this->datatables->where(" {$this->db->dbprefix('transfers')}.status = 'completed' ", null, false);

			if ($product) {
                $this->datatables->where(" (({$this->db->dbprefix('purchase_items')}.product_id = {$product}) OR ({$this->db->dbprefix('transfer_items')}.product_id = {$product})) ", null, false);
            }
            $this->datatables->edit_column('fname', '$1 ($2)', 'fname, fcode')
                ->edit_column('tname', '$1 ($2)', 'tname, tcode')
                ->unset_column('fcode')
                ->unset_column('tcode');
            echo $this->datatables->generate();
        }
    }

    public function getUserLogins($id = null, $pdf = null, $xls = null)
    {
        if ($this->input->get('start_date')) {
            $login_start_date = $this->input->get('start_date');
        } else {
            $login_start_date = null;
        }
        if ($this->input->get('end_date')) {
            $login_end_date = $this->input->get('end_date');
        } else {
            $login_end_date = null;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date   = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        if ($pdf || $xls) {
            $this->db
                ->select('login, ip_address, time')
                ->from('user_logins')
                ->where('user_id', $id)
                ->order_by('time desc');
            if ($login_start_date) {
                $this->db->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", null, false);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('staff_login_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('ip_address'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('time'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->login);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->ip_address);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrld($data_row->time));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('staff_login_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select("login, ip_address, DATE_FORMAT(time, '%Y-%m-%d %T') as time")
                ->from('user_logins')
                ->where('user_id', $id);
            if ($login_start_date) {
                $this->datatables->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", null, false);
            }
            echo $this->datatables->generate();
        }
    }

    public function getUsers()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('users') . '.id as id, first_name, last_name, email, company, ' . $this->db->dbprefix('groups') . '.name, active')
            ->from('users')
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('company_id', null);
        if (!$this->Owner) {
            $this->datatables->where('group_id !=', 1);
        }
        $this->datatables
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column('Actions', "<div class='text-center'><a class=\"tip\" title='" . lang('view_report') . "' href='" . admin_url('carsreports/staff_report/$1') . "'><span class='label label-primary'>" . lang('view_report') . '</span></a></div>', 'id')
            ->unset_column('id');
        echo $this->datatables->generate();
    }

    public function index()
    {
        $this->sma->checkPermissions('index',true,'reports');

        $data['error']               = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->carsreports_model->getChartData();
        $this->data['stock']         = $this->carsreports_model->getStockValue();
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('reports')]];
        $meta                        = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->page_construct('carsreports/index', $meta, $this->data);
    }

    public function monthly_profit($year, $month, $warehouse_id = null, $re = null)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['costing']    = $this->carsreports_model->getCosting(null, $warehouse_id, $year, $month);
        $this->data['discount']   = $this->carsreports_model->getOrderDiscount(null, $warehouse_id, $year, $month);
        $this->data['expenses']   = $this->carsreports_model->getExpenses(null, $warehouse_id, $year, $month);
        $this->data['returns']    = $this->carsreports_model->getReturns(null, $warehouse_id, $year, $month);
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['swh']        = $warehouse_id;
        $this->data['year']       = $year;
        $this->data['month']      = $month;
        $this->data['date']       = date('F Y', strtotime($year . '-' . $month . '-' . '01'));
        if ($re) {
            echo $this->load->view($this->theme . 'carsreports/monthly_profit', $this->data, true);
            exit();
        }
        $this->load->view($this->theme . 'carsreports/monthly_profit', $this->data);
    }

    public function monthly_purchases($warehouse_id = null, $year = null, $pdf = null, $user_id = null)
    {
     //   $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error']     = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year']      = $year;
        $this->data['purchases'] = $user_id ? $this->carsreports_model->getStaffMonthlyPurchases($user_id, $year, $warehouse_id) : $this->carsreports_model->getMonthlyPurchases($year, $warehouse_id);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'carsreports/monthly', $this->data, true);
            $name = lang('monthly_purchases') . '_' . $year . '.pdf';
            $html = str_replace('<p class="introtext">' . lang('reports_calendar_text') . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->cars_model->getAllShowrooms();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('monthly_purchases_report')]];
        $meta                        = ['page_title' => lang('monthly_purchases_report'), 'bc' => $bc];
        $this->page_construct('carsreports/monthly_purchases', $meta, $this->data);
    }

    public function monthly_sales($warehouse_id = null, $year = null, $pdf = null, $user_id = null)
    {
        //$this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year']  = $year;
        $this->data['sales'] = $user_id ? $this->carsreports_model->getStaffMonthlySales($user_id, $year, $warehouse_id) : $this->carsreports_model->getMonthlySales($year, $warehouse_id);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'carsreports/monthly', $this->data, true);
            $name = lang('monthly_sales') . '_' . $year . '.pdf';
            $html = str_replace('<p class="introtext">' . lang('reports_calendar_text') . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->cars_model->getAllShowrooms();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('monthly_sales_report')]];
        $meta                        = ['page_title' => lang('monthly_sales_report'), 'bc' => $bc];
        $this->page_construct('carsreports/monthly', $meta, $this->data);
    }

    public function payments()
    {
        $this->sma->checkPermissions('payments',true,'reports');

        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']        = $this->carsreports_model->getStaff();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['pos_settings'] = POS ? $this->carsreports_model->getPOSSetting('biller') : false;
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('payments_report')]];
        $meta                       = ['page_title' => lang('payments_report'), 'bc' => $bc];
        $this->page_construct('carsreports/payments', $meta, $this->data);
    }

    public function products()
    {
        $this->sma->checkPermissions('products',true,'reports');

        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('products_report')]];
        $meta = ['page_title' => lang('products_report'), 'bc' => $bc];
        $this->page_construct('carsreports/products', $meta, $this->data);
    }

    public function profit($date = null, $warehouse_id = null, $re = null)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }
        //if (!$date) {
            $date = date('Y-m-d 00:00:00');
        //}
        $this->data['costing']    = $this->carsreports_model->getCosting($date, $warehouse_id);
        $this->data['discount']   = $this->carsreports_model->getOrderDiscount($date, $warehouse_id);
        $this->data['expenses']   = $this->carsreports_model->getExpenses($date, $warehouse_id);
        $this->data['returns']    = $this->carsreports_model->getReturns($date, $warehouse_id);
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['swh']        = $warehouse_id;
        $this->data['date']       = $date;

		//$this->sma->print_arrays($warehouse_id);
        if ($re) {
            echo $this->load->view($this->theme . 'carsreports/profit', $this->data, true);
            exit();
        }
        $this->load->view($this->theme . 'carsreports/profit', $this->data);
    }















	public function profit_loss_xls( $xls = null, $start_date = null, $end_date = null)
    {
        $this->sma->checkPermissions('profit_loss',true,'reports');


        /*if (!$start_date) {
            $start      = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end      = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));

        }*/


        /*if (!$start_date) {
            $start      = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end      = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }*/
		$warehouse = $this->input->get('wh') ? $this->input->get('wh') : null;
        $start = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end   = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        if ($start) {
            $start = $this->sma->fld($start);
            $end  = $this->sma->fld($end);
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        //$this->data['total_purchases']       = $this->carsreports_model->getTotalPurchases($start, $end);
        //$this->data['total_sales']           = $this->carsreports_model->getTotalSales($start, $end);
        $data['total_sales']           = $this->carsreports_model->getTotalSalesForProfitandLess($start, $end, $warehouse);
        $data['total_cost_goods']           = $this->carsreports_model->getTotalSalesCosofGoods($start, $end, $warehouse);
        //$this->data['total_return_sales']    = $this->carsreports_model->getTotalReturnSales($start, $end);
        //$this->data['total_expenses']        = $this->carsreports_model->getTotalExpenses($start, $end);
        $data['total_expenses']        = $this->carsreports_model->getTotalExpensesForProfitandLess($start, $end, $warehouse);
        $data['total_revenues']        = $this->carsreports_model->getTotalRevenues($start, $end, $warehouse);
        /*$this->data['total_paid']            = $this->carsreports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received']        = $this->carsreports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash']   = $this->carsreports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc']     = $this->carsreports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->carsreports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp']    = $this->carsreports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->carsreports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned']        = $this->carsreports_model->getTotalReturnedAmount($start, $end);
        $this->data['start']                 = urldecode($start_date);
        $this->data['end']                   = urldecode($end_date);*/
        $data['warehouses']                   = $this->cars_model->getAllShowrooms();
        $data['wh']                   = $this->site->getWarehouseByID($warehouse);

		//$this->sma->print_arrays($data);







                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
				if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }

                //$this->excel->getActiveSheet()->setTitle(lang('profit_loss'));


				//$this->excel->getActiveSheet()->getStyle('A1:B3')->getBorders()
                //    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);



			   $this->excel->getActiveSheet()->setTitle(lang('profit_and_loss_report'));
               $this->excel->getActiveSheet()->SetCellValue('A1', lang('revenues'));
               $this->excel->getActiveSheet()->SetCellValue('B1', '');
               $this->excel->getActiveSheet()->SetCellValue('A2', lang('sales'));
               $this->excel->getActiveSheet()->SetCellValue('B2', $this->sma->formatMoney($data['total_sales']->total_amount));
               $this->excel->getActiveSheet()->SetCellValue('A3', lang('return_sales'));
               $this->excel->getActiveSheet()->SetCellValue('B3', $this->sma->formatMoney($data['total_sales']->rtotal_amount));
               $this->excel->getActiveSheet()->SetCellValue('A4', lang('Discounts'));
               $this->excel->getActiveSheet()->SetCellValue('B4', $this->sma->formatMoney($data['total_sales']->total_discount));
			   $this->excel->getActiveSheet()->SetCellValue('A5', lang('Discount_Allowance'));
               $this->excel->getActiveSheet()->SetCellValue('B5', $this->sma->formatMoney($data['total_sales']->discount_allowance));

			   $net_sales=$this->sma->formatDecimal($data['total_sales']->total_amount+$data['total_sales']->rtotal_amount-$data['total_sales']->total_discount-$data['total_sales']->discount_allowance,12);

               $this->excel->getActiveSheet()->SetCellValue('A6', lang('net_sales'));
               $this->excel->getActiveSheet()->SetCellValue('B6', $this->sma->formatMoney($net_sales));


			   $this->excel->getActiveSheet()->SetCellValue('A7', lang('Cost_of_Goods_Sold'));
               $this->excel->getActiveSheet()->SetCellValue('B7', '');

               $this->excel->getActiveSheet()->SetCellValue('A8', lang('Total_Cost_of_Goods_Sold'));
               $this->excel->getActiveSheet()->SetCellValue('B8', $this->sma->formatMoney($data['total_cost_goods']->item_cost_value));
			   $this->excel->getActiveSheet()->SetCellValue('A9', lang('Gross_Profit'));
               $this->excel->getActiveSheet()->SetCellValue('B9', $this->sma->formatMoney($net_sales - $data['total_cost_goods']->item_cost_value));


			   $this->excel->getActiveSheet()->SetCellValue('A10', lang('expenses'));
               $this->excel->getActiveSheet()->SetCellValue('B10', '');
			   $this->excel->getActiveSheet()->SetCellValue('A11', lang('expenses'));
               $this->excel->getActiveSheet()->SetCellValue('B11', $this->sma->formatMoney($data['total_expenses']->total_amount));
			   $this->excel->getActiveSheet()->SetCellValue('A12', lang('Total_Expenses'));
               $this->excel->getActiveSheet()->SetCellValue('B12', $this->sma->formatMoney($data['total_expenses']->total_amount));


			   $Operating_profit_loss=$this->sma->formatDecimal($net_sales - $data['total_cost_goods']->item_cost_value - $data['total_expenses']->total_amount,12);

			   $this->excel->getActiveSheet()->SetCellValue('A13', lang('Operating_profit_loss'));
               $this->excel->getActiveSheet()->SetCellValue('B13', $this->sma->formatMoney($Operating_profit_loss));

			   $Other_Expenses=$this->sma->formatDecimal( $data['total_expenses']->other_expenses,12);

			   $this->excel->getActiveSheet()->SetCellValue('A14', lang('Other_Expenses'));
               $this->excel->getActiveSheet()->SetCellValue('B14', $this->sma->formatMoney($Other_Expenses));

			   $Other_Revenues=$this->sma->formatDecimal($data['total_sales']->other_revenues + $data['total_revenues']->total_amount,12);
			   $this->excel->getActiveSheet()->SetCellValue('A15', lang('Other_Revenues'));
               $this->excel->getActiveSheet()->SetCellValue('B15', $this->sma->formatMoney($Other_Revenues));


			   $Net_profit_loss=$this->sma->formatDecimal($Operating_profit_loss - $Other_Expenses + $Other_Revenues,12);
			   $this->excel->getActiveSheet()->SetCellValue('A16', lang('Net_profit_loss'));
               $this->excel->getActiveSheet()->SetCellValue('B16', $this->sma->formatMoney($Net_profit_loss));






                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);

				$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
				$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(25);
				$this->excel->getActiveSheet()->getRowDimension(10)->setRowHeight(25);
				$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(25);
				$this->excel->getActiveSheet()->getRowDimension(14)->setRowHeight(25);
				$this->excel->getActiveSheet()->getRowDimension(15)->setRowHeight(25);
				$this->excel->getActiveSheet()->getRowDimension(16)->setRowHeight(25);


				$styleArray = array(
					'font' => array(
						'bold' => true
									)

							);

				$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
                $this->excel->getActiveSheet()->getStyle('A6')->applyFromArray($styleArray);
                $this->excel->getActiveSheet()->getStyle('A7')->applyFromArray($styleArray);
                $this->excel->getActiveSheet()->getStyle('A10')->applyFromArray($styleArray);
                $this->excel->getActiveSheet()->getStyle('A13')->applyFromArray($styleArray);
                $this->excel->getActiveSheet()->getStyle('A14')->applyFromArray($styleArray);
                $this->excel->getActiveSheet()->getStyle('A15')->applyFromArray($styleArray);
                $this->excel->getActiveSheet()->getStyle('A16')->applyFromArray($styleArray);

				//$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);


				$styleArray1 = array(

		  'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'dee2e6')
        )
  );

  $styleArray2 = array(
  'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
	  );

			 $this->excel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($styleArray1);
			 $this->excel->getActiveSheet()->getStyle('A7:B7')->applyFromArray($styleArray1);
			 $this->excel->getActiveSheet()->getStyle('A10:B10')->applyFromArray($styleArray1);
			 $this->excel->getActiveSheet()->getStyle('A13:B13')->applyFromArray($styleArray1);
			 $this->excel->getActiveSheet()->getStyle('A14:B14')->applyFromArray($styleArray1);
			 $this->excel->getActiveSheet()->getStyle('A15:B15')->applyFromArray($styleArray1);
			 $this->excel->getActiveSheet()->getStyle('A16:B16')->applyFromArray($styleArray1);

			 $this->excel->getActiveSheet()->getStyle('A1:B16')->applyFromArray($styleArray2);



				//$this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                //$this->excel->getActiveSheet()->getStyle('A1:B14')->getAlignment()->setWrapText(true);
                $filename = lang('profit_loss');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);


    }





	public function profit_loss($start_date = null, $end_date = null)
    {
        $this->sma->checkPermissions('profit_loss',true,'reports');
        /*if (!$start_date) {
            $start      = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end      = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }*/
		$warehouse = $this->input->post('wh') ? $this->input->post('wh') : null;
        $start = $this->input->post('start_date') ? $this->input->post('start_date') : null;
        $end   = $this->input->post('end_date') ? $this->input->post('end_date') : null;
        if ($start) {
            $start = $this->sma->fld($start).' 00:00:00';
            $end = $this->sma->fld($end).' 23:59:59';

        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['total_purchases']       = $this->carsreports_model->getTotalPurchases($start, $end, $warehouse);
        //$this->data['total_sales']           = $this->carsreports_model->getTotalSales($start, $end);
        $this->data['total_sales']           = $this->carsreports_model->getTotalSalesForProfitandLess($start, $end, $warehouse);
        $this->data['total_cost_goods']           = $this->carsreports_model->getTotalSalesCosofGoods($start, $end, $warehouse);
        //$this->data['total_return_sales']    = $this->carsreports_model->getTotalReturnSales($start, $end);
        //$this->data['total_expenses']        = $this->carsreports_model->getTotalExpenses($start, $end);
        $this->data['total_expenses']        = $this->carsreports_model->getTotalExpensesForProfitandLess($start, $end, $warehouse);
        $this->data['total_revenues']        = $this->carsreports_model->getTotalRevenues($start, $end, $warehouse);
        /*$this->data['total_paid']            = $this->carsreports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received']        = $this->carsreports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash']   = $this->carsreports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc']     = $this->carsreports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->carsreports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp']    = $this->carsreports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->carsreports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned']        = $this->carsreports_model->getTotalReturnedAmount($start, $end);
        $this->data['start']                 = urldecode($start_date);
        $this->data['end']                   = urldecode($end_date);*/
        $this->data['warehouses']                   = $this->cars_model->getAllShowrooms();
        $this->data['wh']                   = $this->site->getWarehouseByID($warehouse);

        /*$warehouses = $this->cars_model->getAllShowrooms();
        foreach ($warehouses as $warehouse) {
            $total_purchases     = $this->carsreports_model->getTotalPurchases($start, $end, $warehouse->id);
            $total_sales         = $this->carsreports_model->getTotalSales($start, $end, $warehouse->id);
            $total_returns       = $this->carsreports_model->getTotalReturnSales($start, $end, $warehouse->id);
            $total_expenses      = $this->carsreports_model->getTotalExpenses($start, $end, $warehouse->id);
            $total_revenues      = $this->carsreports_model->getTotalRevenues($start, $end, $warehouse->id);
            $warehouses_report[] = [
                'warehouse'       => $warehouse,
                'total_purchases' => $total_purchases,
                'total_sales'     => $total_sales,
                'total_returns'   => $total_returns,
                'total_expenses'  => $total_expenses,
                'total_revenues'  => $total_revenues,
            ];
        }
        $this->data['warehouses_report'] = $warehouses_report;
		*/

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('profit_loss')]];
        $meta = ['page_title' => lang('profit_loss'), 'bc' => $bc];
        $this->page_construct('carsreports/profit_loss', $meta, $this->data);
    }

    public function profit_loss_pdf($start_date = null, $end_date = null)
    {
        $this->sma->checkPermissions('profit_loss',true,'reports');
        if (!$start_date) {
            $start      = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end      = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }

        $this->data['total_purchases']       = $this->carsreports_model->getTotalPurchases($start, $end);
        $this->data['total_sales']           = $this->carsreports_model->getTotalSales($start, $end);
        $this->data['total_expenses']        = $this->carsreports_model->getTotalExpenses($start, $end);
        $this->data['total_paid']            = $this->carsreports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received']        = $this->carsreports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash']   = $this->carsreports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc']     = $this->carsreports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->carsreports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp']    = $this->carsreports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->carsreports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned']        = $this->carsreports_model->getTotalReturnedAmount($start, $end);
        $this->data['start']                 = urldecode($start_date);
        $this->data['end']                   = urldecode($end_date);

        $warehouses = $this->cars_model->getAllShowrooms();
        foreach ($warehouses as $warehouse) {
            $total_purchases     = $this->carsreports_model->getTotalPurchases($start, $end, $warehouse->id);
            $total_sales         = $this->carsreports_model->getTotalSales($start, $end, $warehouse->id);
            $warehouses_report[] = [
                'warehouse'       => $warehouse,
                'total_purchases' => $total_purchases,
                'total_sales'     => $total_sales,
            ];
        }
        $this->data['warehouses_report'] = $warehouses_report;

        $html = $this->load->view($this->theme . 'carsreports/profit_loss_pdf', $this->data, true);
        $name = lang('profit_loss') . '-' . str_replace(['-', ' ', ':'], '_', $this->data['start']) . '-' . str_replace(['-', ' ', ':'], '_', $this->data['end']) . '.pdf';
        $this->sma->generate_pdf($html, $name, false, false, false, false, false, 'L');
    }

    public function purchases()
    {
        $this->sma->checkPermissions('purchases',true,'reports');

        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->carsreports_model->getStaff();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('purchases_report')]];
        $meta                     = ['page_title' => lang('purchases_report'), 'bc' => $bc];
        $this->page_construct('carsreports/purchases', $meta, $this->data);
    }

    public function quantity_alerts($warehouse_id = null)
    {
        $this->sma->checkPermissions('quantity_alerts',true,'reports');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->cars_model->getAllShowrooms();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $user                       = $this->site->getUser();
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse']    = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : null;
        }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('product_quantity_alerts')]];
        $meta = ['page_title' => lang('product_quantity_alerts'), 'bc' => $bc];
        $this->page_construct('carsreports/quantity_alerts', $meta, $this->data);
    }

    function register()
    {

        $this->sma->checkPermissions('register',true,'reports');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->carsreports_model->getStaff();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('carsreports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('register_report')));
        $meta = array('page_title' => lang('register_report'), 'bc' => $bc);
        $this->page_construct('carsreports/register', $meta, $this->data);
    }

    public function sales()
    {
        $this->sma->checkPermissions('sales',true,'reports');

        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->carsreports_model->getStaff();
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('sales_report')]];
        $meta                     = ['page_title' => lang('sales_report'), 'bc' => $bc];
        $this->page_construct('carsreports/sales', $meta, $this->data);
    }

    public function staff_report($user_id = null, $year = null, $month = null, $pdf = null, $cal = 0)
    {
       //         $this->sma->checkPermissions('staff_report',true,'reports');
        if (!$user_id) {
            $this->session->set_flashdata('error', lang('no_user_selected'));
            admin_redirect('carsreports/users');
        }
        $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['purchases']  = $this->carsreports_model->getStaffPurchases($user_id);
        $this->data['sales']      = $this->carsreports_model->getStaffSales($user_id);
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $this->data['warehouses'] = $this->cars_model->getAllShowrooms();

        if (!$year) {
            $year = date('Y');
        }
        if (!$month || $month == '#monthly-con') {
            $month = date('m');
        }
        if ($pdf) {
            if ($cal) {
                $this->monthly_sales($year, $pdf, $user_id);
            } else {
                $this->daily_sales($year, $month, $pdf, $user_id);
            }
        }
        $config = [
            'show_next_prev' => true,
            'next_prev_url'  => admin_url('carsreports/staff_report/' . $user_id),
            'month_type'     => 'long',
            'day_type'       => 'long',
        ];

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable reports-table">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th class="text-center"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th class="text-center" colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th class="text-center"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $this->carsreports_model->getStaffDailySales($user_id, $year, $month);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang('discount') . '</td><td>' . $this->sma->formatMoney($sale->discount) . '</td></tr><tr><td>' . lang('product_tax') . '</td><td>' . $this->sma->formatMoney($sale->tax1) . '</td></tr><tr><td>' . lang('order_tax') . '</td><td>' . $this->sma->formatMoney($sale->tax2) . '</td></tr><tr><td>' . lang('total') . '</td><td>' . $this->sma->formatMoney($sale->total) . '</td></tr></table>';
            }
        } else {
            $daily_sale = [];
        }
        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        if ($this->input->get('pdf')) {
        }
        $this->data['year']    = $year;
        $this->data['month']   = $month;
        $this->data['msales']  = $this->carsreports_model->getStaffMonthlySales($user_id, $year);
        $this->data['user_id'] = $user_id;
        $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('staff_report')]];
        $meta                  = ['page_title' => lang('staff_report'), 'bc' => $bc];
        $this->page_construct('carsreports/staff_report', $meta, $this->data);
    }

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1) {
            die();
        }

        $rows = $this->carsreports_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')'];
            }
            $this->sma->send_json($pr);
        } else {
            echo false;
        }
    }

    public function supplier_report($user_id = null)
    {

       $this->sma->checkPermissions('suppliers',true,'reports');

        if (!$user_id) {
            $this->session->set_flashdata('error', lang('No_car_Owner_selected'));
            admin_redirect('carsreports/suppliers');
        }

        $this->data['purchases']       = $this->carsreports_model->getPurchasesTotals($user_id);
        $this->data['total_purchases'] = $this->carsreports_model->getSupplierPurchases($user_id);
		$this->data['total_purchases_canv']   = $this->carsreports_model->getMonthlyPurchases(date('Y'), '' ,$user_id);

        $this->data['users']           = $this->carsreports_model->getStaff();
        $this->data['warehouses']      = $this->cars_model->getAllShowrooms();

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('Car_Owners_report')]];
        $meta                  = ['page_title' => lang('Car_Owners_report'), 'bc' => $bc];
        $this->page_construct('carsreports/supplier_report', $meta, $this->data);
    }

    public function suppliers()
    {
        $this->sma->checkPermissions('suppliers',true,'reports');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('Car_Owners_report')]];
        $meta = ['page_title' => lang('Car_Owners_report'), 'bc' => $bc];
        $this->page_construct('carsreports/suppliers', $meta, $this->data);
    }

    public function tax1()
    {
       // $this->sma->checkPermissions();
        $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : null;
        $end_date   = $this->input->post('end_date') ? $this->input->post('end_date') : null;
        $warehouse_id   = $this->input->post('warehouse') ? $this->input->post('warehouse') : null;
		//$this->sma->print_arrays($warehouse_id);

        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
            $end_date = $this->sma->fld($end_date).' 23:59:59';
        }
        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['tax_rates']     = $this->carsreports_model->getTaxRatesDefault();
        $this->data['sale_tax']     = $this->carsreports_model->getSalesTax($start_date, $end_date,$warehouse_id);

		$this->data['sale_tax_local_nt']     = $this->carsreports_model->getSalesTaxLocalNT($start_date, $end_date,$warehouse_id);
		//$this->data['sale_tax_local_nt_completed']     = $this->carsreports_model->getSalesTaxLocalNT($start_date, $end_date,$warehouse_id,'completed');
		//$this->data['sale_tax_local_nt_returned']     = $this->carsreports_model->getSalesTaxLocalNT($start_date, $end_date,$warehouse_id,'returned');

		$this->data['sale_tax_local_zero']     = $this->carsreports_model->getSalesTaxLocalZero($start_date, $end_date,$warehouse_id);
		//$this->data['sale_tax_local_zero_completed']     = $this->carsreports_model->getSalesTaxLocalZero($start_date, $end_date,$warehouse_id,'completed');
		//$this->data['sale_tax_local_zero_returned']     = $this->carsreports_model->getSalesTaxLocalZero($start_date, $end_date,$warehouse_id,'returned');
        //$this->data['sale_tax_local_basic']     = $this->carsreports_model->getSalesTaxLocalBasic($start_date, $end_date);
        $this->data['sale_tax_local_basic_details']     = $this->carsreports_model->getSalesTaxLocalBasicDetails($start_date, $end_date ,$warehouse_id);
        //$this->data['sale_tax_local_basic_details_completed']     = $this->carsreports_model->getSalesTaxLocalBasicDetails($start_date, $end_date ,$warehouse_id,'completed');
        //$this->data['sale_tax_local_basic_details_returned']     = $this->carsreports_model->getSalesTaxLocalBasicDetails($start_date, $end_date ,$warehouse_id,'returned');

		$this->data['sale_tax_international']     = $this->carsreports_model->getSalesTaxInternational($start_date, $end_date ,$warehouse_id);
		//$this->data['sale_tax_international_completed']     = $this->carsreports_model->getSalesTaxInternational($start_date, $end_date ,$warehouse_id,'completed');
		//$this->data['sale_tax_international_returned']     = $this->carsreports_model->getSalesTaxInternational($start_date, $end_date ,$warehouse_id,'returned');
        $this->data['purchase_tax'] = $this->carsreports_model->getPurchasesTax($start_date, $end_date ,$warehouse_id);
        $this->data['purchase_tax_local_nt'] = $this->carsreports_model->getPurchasesTaxLocalNT($start_date, $end_date ,$warehouse_id);
        $this->data['purchase_tax_local_zero'] = $this->carsreports_model->getPurchasesTaxLocalZero($start_date, $end_date ,NULL,$warehouse_id);
        //$this->data['purchase_tax_local_basic'] = $this->carsreports_model->getPurchasesTaxLocalBasic($start_date, $end_date);
		$this->data['purchase_tax_local_basic_details'] = $this->carsreports_model->getPurchasesTaxLocalBasicDetails($start_date, $end_date,$warehouse_id);
        //$this->data['expenses_tax_local_basic'] = $this->carsreports_model->getExpensesTaxLocalBasic($start_date, $end_date);
        //$this->data['expenses_tax_local_basic_details'] = $this->carsreports_model->getExpensesTaxLocalBasicDetails($start_date, $end_date);
        $this->data['purchase_tax_international_basic'] = $this->carsreports_model->getPurchasesTaxInternationalBasic($start_date, $end_date,$warehouse_id);

		$this->data['purchase_tax_international_basic_details'] = $this->carsreports_model->getPurchasesTaxInternationalBasicDetails($start_date, $end_date,$warehouse_id);
        //$this->sma->print_arrays($this->data['purchase_tax_international_basic_details']);
        $this->data['purchase_tax_international_zero'] = $this->carsreports_model->getPurchasesTaxInternationalZero($start_date, $end_date,$warehouse_id);
        $this->data['purchase_tax_international_zero_details'] = $this->carsreports_model->getPurchasesTaxInternationalZeroDetails($start_date, $end_date ,$warehouse_id);
        //$this->sma->print_arrays($this->data['purchase_tax_international_zero_details']);
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('tax_report')]];
        $meta                       = ['page_title' => lang('tax_report'), 'bc' => $bc];
        $this->page_construct('carsreports/tax1', $meta, $this->data);
    }

	public function tax()
    {
        //$this->sma->checkPermissions();
        $this->sma->checkPermissions('tax',true,'reports');

        $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : null;
        $end_date   = $this->input->post('end_date') ? $this->input->post('end_date') : null;
        $warehouse_id   = $this->input->post('warehouse') ? $this->input->post('warehouse') : null;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date).' 00:00:00';
            $end_date = $this->sma->fld($end_date).' 23:59:59';
			/*
			$ts1 = strtotime($start_date);
			if(date('m', $ts1)=='01' && date('d', $ts1)=='01'){
			}else{
				$prev_start_date = date('Y', $ts1).'-01-01 00:00:00';
				$prev_end_date = date('Y-m-d', strtotime('-1 days', strtotime($start_date))).' 23:59:59';


			}*/



        }
		$this->data['start_date']      = isset($start_date) ? $start_date : null;
		$this->data['end_date']      = isset($end_date) ? $end_date : null;
					//	$this->sma->print_arrays($start_date,$end_date, $warehouse_id);

		//$this->sma->print_arrays($prev_start_date,$prev_end_date);
        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['tax_rates']     = $this->carsreports_model->getTaxRatesDefault();
        $this->data['sale_tax']     = $this->carsreports_model->getSalesTax($start_date, $end_date,$warehouse_id);
       // $this->sma->print_arrays($this->data['sale_tax'] );
		$this->data['sale_tax_local_nt']     = $this->carsreports_model->getSalesTaxLocalNT($start_date, $end_date,$warehouse_id);
		//$this->data['sale_tax_local_nt_completed']     = $this->carsreports_model->getSalesTaxLocalNT($start_date, $end_date,$warehouse_id,'completed');
		$this->data['sale_tax_local_nt_returned']     = $this->carsreports_model->getSalesTaxLocalNTReturned($start_date, $end_date,$warehouse_id);

		$this->data['sale_tax_local_zero']     = $this->carsreports_model->getSalesTaxLocalZero($start_date, $end_date,$warehouse_id);
		//$this->data['sale_tax_local_zero_completed']     = $this->carsreports_model->getSalesTaxLocalZero($start_date, $end_date,$warehouse_id,'completed');
		if($start_date && $end_date){
		$this->data['sale_tax_local_zero_returned']     = $this->carsreports_model->getSalesTaxLocalZeroReturned($start_date, $end_date,$warehouse_id);
        }
		//$this->sma->print_arrays($this->data['sale_tax_local_zero_returned']);
		//$this->data['sale_tax_local_basic']     = $this->carsreports_model->getSalesTaxLocalBasic($start_date, $end_date);
        $this->data['sale_tax_local_basic_details']     = $this->carsreports_model->getSalesTaxLocalBasicDetails($start_date, $end_date ,$warehouse_id);
        //$this->data['sale_tax_local_basic_details_completed']     = $this->carsreports_model->getSalesTaxLocalBasicDetails($start_date, $end_date ,$warehouse_id,'completed');
        //$this->data['sale_tax_local_basic_details_returned']     = $this->carsreports_model->getSalesTaxLocalBasicDetailsReturned($start_date, $end_date ,$warehouse_id,15);
        //$this->sma->print_arrays($this->data['sale_tax_local_basic_details_returned']);

		$this->data['sale_tax_international']     = $this->carsreports_model->getSalesTaxInternational($start_date, $end_date ,$warehouse_id);
		//$this->data['sale_tax_international_completed']     = $this->carsreports_model->getSalesTaxInternational($start_date, $end_date ,$warehouse_id,'completed');
		if($start_date && $end_date){
		$this->data['sale_tax_international_returned']     = $this->carsreports_model->getSalesTaxInternationalReturned($start_date, $end_date ,$warehouse_id);
        }

		$this->data['purchase_tax'] = $this->carsreports_model->getPurchasesTax($start_date, $end_date ,$warehouse_id);
        $this->data['purchase_tax_local_nt'] = $this->carsreports_model->getPurchasesTaxLocalNT($start_date, $end_date ,$warehouse_id);
        $this->data['purchase_tax_local_zero'] = $this->carsreports_model->getPurchasesTaxLocalZero($start_date, $end_date ,NULL,$warehouse_id);
        if($start_date && $end_date){
		$this->data['purchase_tax_local_zero_returned']     = $this->carsreports_model->getPurchasesTaxLocalZeroReturned($start_date, $end_date,$warehouse_id);
		$this->data['purchase_tax_local_nt_returned']     = $this->carsreports_model->getPurchasesTaxLocalNTReturned($start_date, $end_date,$warehouse_id);
        }
		 //$this->sma->print_arrays($this->data['purchase_tax_local_zero_returned']);

		//$this->data['purchase_tax_local_basic'] = $this->carsreports_model->getPurchasesTaxLocalBasic($start_date, $end_date);
		$this->data['purchase_tax_local_basic_details'] = $this->carsreports_model->getPurchasesTaxLocalBasicDetails($start_date, $end_date,$warehouse_id);
		//$this->sma->print_arrays($this->data['purchase_tax_local_basic_details']);
		//$this->data['purchase_tax_local_basic_details_returned'] = $this->carsreports_model->getPurchasesTaxLocalBasicDetailsReturned($start_date, $end_date,$warehouse_id,15);

		//$this->sma->print_arrays($this->data['purchase_tax_local_basic_details_returned']);

		//$this->data['expenses_tax_local_basic'] = $this->carsreports_model->getExpensesTaxLocalBasic($start_date, $end_date);
        //$this->data['expenses_tax_local_basic_details'] = $this->carsreports_model->getExpensesTaxLocalBasicDetails($start_date, $end_date);
        $this->data['purchase_tax_international_basic'] = $this->carsreports_model->getPurchasesTaxInternationalBasic($start_date, $end_date,$warehouse_id);

		$this->data['purchase_tax_international_basic_details'] = $this->carsreports_model->getPurchasesTaxInternationalBasicDetails($start_date, $end_date,$warehouse_id);
        //$this->sma->print_arrays($this->data['purchase_tax_international_basic_details']);
        $this->data['purchase_tax_international_zero'] = $this->carsreports_model->getPurchasesTaxInternationalZero($start_date, $end_date,$warehouse_id);
        $this->data['purchase_tax_international_zero_returned'] = $this->carsreports_model->getPurchasesTaxInternationalZeroReturned($start_date, $end_date,$warehouse_id);
        $this->data['purchase_tax_international_zero_details'] = $this->carsreports_model->getPurchasesTaxInternationalZeroDetails($start_date, $end_date ,$warehouse_id);


		//$this->sma->print_arrays($this->carsreports_model->getPurchasesTaxInternationalZeroDetailsReturned($start_date, $end_date ,$warehouse_id,15));
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('tax_report')]];
        $meta                       = ['page_title' => lang('tax_report'), 'bc' => $bc];
        $this->page_construct('carsreports/tax', $meta, $this->data);
    }



    public function users()
    {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('staff_report')]];
        $meta                = ['page_title' => lang('staff_report'), 'bc' => $bc];
        $this->page_construct('carsreports/users', $meta, $this->data);
    }

    public function warehouse_stock($warehouse = null)
    {
       // $this->sma->checkPermissions('index', true);
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }

        $this->data['stock']        = $warehouse ? $this->carsreports_model->getWarehouseStockValue($warehouse) : $this->carsreports_model->getStockValue();
        $this->data['warehouses']   = $this->cars_model->getAllShowrooms();
        $this->data['warehouse_id'] = $warehouse;
        $this->data['warehouse']    = $warehouse ? $this->site->getWarehouseByID($warehouse) : null;
        $this->data['totals']       = $this->carsreports_model->getWarehouseTotals($warehouse);
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('reports')]];
        $meta                       = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->page_construct('carsreports/warehouse_stock', $meta, $this->data);
    }


	    public function getProductsPurchasesReport($pdf = null, $xls = null)
    {
        //$this->sma->checkPermissions('products', true);
		if(!$this->site->IsPermissions('products-reports')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{

        $product     = $this->input->get('product') ? $this->input->get('product') : null;
        $category    = $this->input->get('category') ? $this->input->get('category') : null;
        $brand       = $this->input->get('brand') ? $this->input->get('brand') : null;
        $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : null;
        $warehouse   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $cf1         = $this->input->get('cf1') ? $this->input->get('cf1') : null;
        $cf2         = $this->input->get('cf2') ? $this->input->get('cf2') : null;
        $cf3         = $this->input->get('cf3') ? $this->input->get('cf3') : null;
        $cf4         = $this->input->get('cf4') ? $this->input->get('cf4') : null;
        $cf5         = $this->input->get('cf5') ? $this->input->get('cf5') : null;
        $cf6         = $this->input->get('cf6') ? $this->input->get('cf6') : null;
        $start_date  = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date    = $this->input->get('end_date') ? $this->input->get('end_date') : null;

        /*$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        //$pp = "( SELECT pi.product_id,SUM(wp.avg_cost_over_selling) as avg_cost_over_sellingWP, SUM(wp.avg_cost) as avg_costWP, SUM(wp.quantity_over_selling) as quantity_over_sellingWP, SUM(wp.quantity) as quantityWP, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN pi.quantity ELSE 0 END) as purchasedQty, SUM(pi.quantity_balance) as balacneQty, SUM( pi.unit_cost * pi.quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id LEFT JOIN {$this->db->dbprefix('warehouses_products')} wp on (wp.product_id = pi.product_id and wp.warehouse_id = pi.warehouse_id and wp.warehouse_id = p.warehouse_id) WHERE pi.status = 'received' ";
        // WHERE p.status != 'pending' AND p.status != 'ordered'
        $sp = '( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';
        if ($start_date || $warehouse) {
            $sp .= ' WHERE ';
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " AND p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $sp .= ' AND ';
                }
            }
            if ($warehouse) {
                $pp .= " AND pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        $pp .= ' GROUP BY pi.product_id ) PCosts';
        $sp .= ' GROUP BY si.product_id ) PSales';
		*/



		/*
		$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        // WHERE p.status != 'pending' AND p.status != 'ordered'
        $sp = '( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.quantity ) totalSale, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';
        $wp = '( SELECT wp.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost, SUM( wp.avg_cost * wp.total_qty_prev ) totalAVGCOST, SUM( wp.total_qty_prev ) TotalQtyPrev, SUM( wp.quantity ) TotalQty from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp ';
        $wpo = '( SELECT wpo.product_id, wpo.avg_cost, SUM( (CASE WHEN wpo.quantity> 0 THEN (wpo.avg_cost * wpo.quantity) ELSE 0 END) ) balacneValue, SUM( wpo.quantity ) balacneQty  from ' . $this->db->dbprefix('warehouses_products') . ' wpo ';
        */
		//$pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN (CASE WHEN quantity_received IS NOT NULL THEN quantity_received ELSE quantity END) ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.item_cost_value) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE pi.status = 'received' ";
        // WHERE p.status != 'pending' AND p.status != 'ordered'
        //$sp = '( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.net_unit_price * si.quantity ) totalSale, SUM( item_cost_value ) totalGoodsCost from ' . $this->db->dbprefix('sales') . ' s JOIN ' . $this->db->dbprefix('sale_items') . ' si on s.id = si.sale_id ';
        //$wp = '( SELECT wp.product_id, SUM( wp.new_avg_cost * wp.quantity ) totalGoodsCost, SUM( wp.avg_cost * wp.total_qty_prev ) totalAVGCOST, SUM( wp.total_qty_prev ) TotalQtyPrev, SUM( wp.quantity ) TotalQty from ' . $this->db->dbprefix('warehouses_products_avg_cost') . ' wp ';
        $wpo = '( SELECT distinct wpo.product_id,wpo.warehouse_id, SUM(CASE WHEN wpo.quantity>0  THEN wpo.avg_cost ELSE 0 END) as avg_cost ,  SUM(wpo.quantity) as quantity   from ' . $this->db->dbprefix('warehouses_products') . ' wpo';

		if ($start_date || $warehouse) {
            $sp .= ' WHERE ';
            /*if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " AND p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $sp .= ' AND ';
                }
            }*/
            if ($warehouse) {
                //$pp .= " AND pi.warehouse_id = '{$warehouse}' ";
                //$sp .= " si.warehouse_id = '{$warehouse}' ";
				//$wp .= " WHERE wp.warehouse_id = '{$warehouse}' ";
            	$wpo .= " WHERE wpo.warehouse_id = '{$warehouse}' ";

            }
        }
        //$pp .= ' GROUP BY pi.product_id ) PCosts';
        //$sp .= ' GROUP BY si.product_id ) PSales';
        //$wp .= ' GROUP BY wp.product_id ) WarehousesProducts';
        $wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';

        if ($pdf || $xls) {
            $this->db
                 ->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . ".name,
				COALESCE( (WarehousesProductsOrignal.quantity), 0 ) as quantity,
				COALESCE( (WarehousesProductsOrignal.avg_cost), 0 ) as avg_cost,

				{$this->db->dbprefix('products')}.id as id
				", false)
                ->from('products')
                //->join($sp, 'products.id = PSales.product_id', 'left')
                //->join($pp, 'products.id = PCosts.product_id', 'left')
                //->join($wp, 'products.id = WarehousesProducts.product_id', 'left')
				->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left');
				//->group_by('products.code');

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
            if ($brand) {
                $this->db->where($this->db->dbprefix('products') . '.brand', $brand);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = null;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                    if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }

                $this->excel->getActiveSheet()->setTitle(lang('products_report'));
                                        $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('sales'));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $styleArray = array(
                            'font' => array(
                                'bold' => true,
                                'size' => 20,
                                'name'  => 'Cairo-Bold'
                                            )

                                    );
                           $fillColor = array(

                                     'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'f9f9f9')
                                        )
                                      );

                              $Font11Bold = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 11,
                                    'name'  => 'Cairo-Bold'
                                                )

                              );
                $styleArrayFillGreen = array(

                 'fill' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5cb85c')
                    ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )
                 );

              $styleArrayFillRed = array(

                 'fill' => array(
                   'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'd9534f')
                    ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )
              );
                    $styleArrayFillYellow = array(

                     'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'f0ad4e')
                        ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )

                  );

                   $styleArrayFillBlu = array(

                     'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '5bc0de')
                        ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )

                  );
                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                         $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                        $row++;

                $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('quantity'));
                $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('avg_cost'));

                     $this->excel->getActiveSheet()->getStyle('A'.$row.':D'.$row)->applyFromArray($fillColor);
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         $this->excel->getActiveSheet()->getStyle('A'.$row.':D'.$row)->applyFromArray($Font11Bold);
                        $row++;
                $qty = 0;
                $amt = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->quantity);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->avg_cost);
                    $qty += $data_row->quantity;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle('C' . $row . ':D' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $qty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, "");

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:D' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('products_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select($this->db->dbprefix('products') . '.code, ' . $this->db->dbprefix('products') . ".name,
				COALESCE( (WarehousesProductsOrignal.quantity), 0 ) as quantity,
				COALESCE( (WarehousesProductsOrignal.avg_cost), 0 ) as avg_cost,

				{$this->db->dbprefix('products')}.id as id
				", false)
                ->from('products')
                //->join($sp, 'products.id = PSales.product_id', 'left')
                //->join($pp, 'products.id = PCosts.product_id', 'left')
                //->join($wp, 'products.id = WarehousesProducts.product_id', 'left')
				->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left');
				//->group_by('products.code');

            if ($product) {
                $this->datatables->where($this->db->dbprefix('products') . '.id', $product);
            }
            if ($cf1) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf1', $cf1);
            }
            if ($cf2) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf2', $cf2);
            }
            if ($cf3) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf3', $cf3);
            }
            if ($cf4) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf4', $cf4);
            }
            if ($cf5) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf5', $cf5);
            }
            if ($cf6) {
                $this->datatables->where($this->db->dbprefix('products') . '.cf6', $cf6);
            }
            if ($category) {
                $this->datatables->where($this->db->dbprefix('products') . '.category_id', $category);
            }
            if ($subcategory) {
                $this->datatables->where($this->db->dbprefix('products') . '.subcategory_id', $subcategory);
            }
            if ($brand) {
                $this->datatables->where($this->db->dbprefix('products') . '.brand', $brand);
            }

            echo $this->datatables->generate();
        }
		}
    }

		public function dashboard()
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
		$this->data['chatDataTrends']  = $this->db_model->getTChartDataTrends();

        $this->data['chatData']  = $this->db_model->getPurchasesLocationChart();
        $this->data['stock']     = $this->db_model->getStockValue();
        $this->data['bs']        = $this->db_model->getBestPurchaseschart();
        $this->data['ubs']        = $this->db_model->getUnBestPurchaseschart();
        $this->data['bc']        = $this->db_model->getBestCustomerschart();
        $this->data['ubc']        = $this->db_model->getUnBestCustomerschart();
        $lmsdate                 = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        $lmedate                 = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        $this->data['lmbs']      = $this->db_model->getBestSeller($lmsdate, $lmedate);
        $bc                      = [['link' => '#', 'page' => lang('dashboard')]];
        $meta                    = ['page_title' => lang('dashboard'), 'bc' => $bc];
        $this->page_construct('carsreports/dashboard', $meta, $this->data);
    }

		public function users_report()
    {

        $this->sma->checkPermissions('users_report',true,'reports');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('carsreports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('staff_report')]];
        $meta                = ['page_title' => lang('staff_report'), 'bc' => $bc];
        $this->page_construct('carsreports/users', $meta, $this->data);
    }



	function getSalesReportOrignal($pdf = NULL, $xls = NULL)
    {
        //$this->sma->checkPermissions('sales_report', TRUE);
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            //$start_date = $this->sma->fld($start_date).' 00:00:00';
            //$end_date = $this->sma->fld($end_date).' 23:59:59';
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            //$user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db
                ->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('sale_items') . ".product_name, ' (', " . $this->db->dbprefix('sale_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, discount_allowance, payment_status", FALSE)
                ->from('sales')
                ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
                ->join('cars_showrooms', 'cars_showrooms.id=sales.warehouse_id', 'left')
                ->group_by('sales.id')
                ->order_by('sales.date desc');

            if ($user) {
                $this->db->where('sales.created_by', $user);
            }
            if ($product) {
                $this->db->where('sale_items.product_id', $product);
            }
            if ($serial) {
                $this->db->like('sale_items.serial_no', $serial);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('sales').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                    if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }
                   $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                        $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('sales_report'));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $styleArray = array(
                            'font' => array(
                                'bold' => true,
                                'size' => 20,
                                'name'  => 'Cairo-Bold'
                                            )

                                    );
                           $fillColor = array(

                                     'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'f9f9f9')
                                        )
                                      );

                              $Font11Bold = array(
                                'font' => array(
                                    'bold' => true,
                                    'size' => 11,
                                    'name'  => 'Cairo-Bold'
                                                )

                              );
                $styleArrayFillGreen = array(

                 'fill' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5cb85c')
                    ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )
                 );

              $styleArrayFillRed = array(

                 'fill' => array(
                   'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'd9534f')
                    ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )
              );
                    $styleArrayFillYellow = array(

                     'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'f0ad4e')
                        ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )

                  );

                   $styleArrayFillBlu = array(

                     'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '5bc0de')
                        ),
                    'font' => array(
                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                    )

                  );
                          $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                         $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);


                        $row++;
                         $this->excel->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
                $this->excel->getActiveSheet()->SetCellValue('A'.$row, ($this->input->get('start_date') ?  lang('period') .' '. lang('sales_report') .' '. lang('From') .' '. $this->input->get('start_date') . ' ' . lang('to') .' '. $this->input->get('end_date')  : ''));
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
               $this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
                $row++;

                $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('product').' / '.lang('quantity'));
                $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('Discount_Allowance'));
                $this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('J'.$row, lang('payment_status'));
                 $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($fillColor);
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         $this->excel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($Font11Bold);
                        $row++;
                $total = 0;
                $paid = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->discount_allowance);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, ($data_row->grand_total - ($data_row->paid+$data_row->discount_allowance)));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, lang($data_row->payment_status));
                        if($data_row->payment_status=='paid'){
                        $this->excel->getActiveSheet()->getStyle('J'.$row)->applyFromArray($styleArrayFillGreen);
                        }else if($data_row->payment_status=='pending'){
                        $this->excel->getActiveSheet()->getStyle('J'.$row)->applyFromArray($styleArrayFillYellow);
                        }else if($data_row->payment_status=='due'){
                        $this->excel->getActiveSheet()->getStyle('J'.$row)->applyFromArray($styleArrayFillRed);
                        }else if($data_row->payment_status=='partial'){
                        $this->excel->getActiveSheet()->getStyle('J'.$row)->applyFromArray($styleArrayFillBlu);
                        }


                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $discount_allowance += $data_row->discount_allowance;
                    $balance += ($data_row->grand_total - ($data_row->paid+$data_row->discount_allowance));
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":I" . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $discount_allowance);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = lang('sales_report');
                $this->load->helper('excel');
                create_excel($this->excel, $filename);

            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $si = "( SELECT sale_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('sale_items')}.product_name, '__', {$this->db->dbprefix('sale_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('sale_items')} ";
            if ($product || $serial) { $si .= " WHERE "; }
            if ($product) {
                $si .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
            }
            if ($product && $serial) { $si .= " AND "; }
            if ($serial) {
                $si .= " {$this->db->dbprefix('sale_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $si .= " GROUP BY {$this->db->dbprefix('sale_items')}.sale_id ) FSI";
            $this->load->library('datatables');
            $this->datatables
                ->select("DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, FSI.item_nane as iname, grand_total, paid, discount_allowance, (grand_total-(paid+discount_allowance)) as balance, payment_status, {$this->db->dbprefix('sales')}.id as id", FALSE)
                ->from('sales')
                ->join($si, 'FSI.sale_id=sales.id', 'left')
                ->join('cars_showrooms', 'cars_showrooms.id=sales.warehouse_id', 'left');
                // ->group_by('sales.id');

            if ($user) {
                $this->datatables->where('sales.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('sales').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();

        }

    }


	public function getExpensesTax()
    {
	//	$this->sma->checkPermissions('expenses',true,'reports');


		$warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : null;


		if ($start_date) {
           $start_date = $this->sma->fld($start_date).' 00:00:00';
			$end_date = $this->sma->fld($end_date).' 23:59:59';
        }

		//$this->sma->print_arrays($start_date);


        $detail_link = anchor('admin/purchases/expense_note/$1', '<i class="fa fa-file-text-o"></i> ' . lang('expense_note'), 'data-toggle="modal" data-target="#myModal2"');
        $payments_link    = anchor('admin/purchases/expense_payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link = anchor('admin/purchases/add_expense_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link   = anchor('admin/purchases/edit_expense/$1', '<i class="fa fa-edit"></i> ' . lang('edit_expense'), 'data-toggle="modal" data-target="#myModal"');
        //$attachment_link = '<a href="'.base_url('assets/uploads/$1').'" target="_blank"><i class="fa fa-chain"></i></a>';
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line('delete_expense') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('purchases/delete_expense/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_expense') . '</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $payments_link . '</li>
            <li>' . $add_payment_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';

        $this->load->library('datatables');

        $this->datatables
            //->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment", false)
            ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, {$this->db->dbprefix('expense_categories')}.name as category, total, total_tax, ", false)
            ->from('expenses')
            ->join('users', 'users.id=expenses.created_by', 'left')
            ->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')
            //->where('expenses.payment_status','paid')
			->group_by('expenses.id');

            if ($warehouse) {
                $this->datatables->where('expenses.warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('expenses.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }


		if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }
        //$this->datatables->edit_column("attachment", $attachment_link, "attachment");
       // $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }



}
