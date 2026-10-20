<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('memory_limit', '-1');

//ini_set('memory_limit','2048M');
class Manufacturing extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('products', $this->Settings->user_language);
        $this->lang->admin_load('transfers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('manufacturing_model');
        $this->load->admin_model('products_model');
		$this->load->admin_model('reports_model');
        $this->load->admin_model('settings_model');
		$this->load->admin_model('transfers_model');
		$this->load->admin_model('sales_model');
		$this->load->admin_model('db_model');
        $this->load->admin_model('accounts_model');
		$this->digital_upload_path = 'files/';
        $this->upload_path         = 'assets/uploads/';
        $this->thumbs_path         = 'assets/uploads/thumbs/';
        $this->image_types         = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types  = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size   = '1048576';
        $this->popup_attributes    = ['width' => '900', 'height' => '600', 'window_name' => 'sma_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0'];
    }

    /* ------------------------------------------------------- */



  public function bom($warehouse_id = null)
    {
        $this->sma->checkPermissions('bom',true);

		$warehouse_id   = $this->input->post('warehouse') ? $this->input->post('warehouse') : null;


        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }
		$this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();


        $this->data['supplier'] = $this->input->get('supplier') ? $this->site->getCompanyByID($this->input->get('supplier')) : null;
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Bill_Of_Material')]];
        $meta                   = ['page_title' => lang('Bill_Of_Material'), 'bc' => $bc];
        $this->page_construct('manufacturing/bom', $meta, $this->data);
    }

	public function index($pdf = null, $xls = null)
    {
        $this->sma->checkPermissions('index',true);

        if ($pdf || $xls) {
            $manufacturing   = $this->manufacturing_model->getAllManufacturingDataTable();
            $this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Manufacturing_list'));
            $this->excel->getActiveSheet()->mergeCells('A1:H1');
            $styleArrayFill = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'f9f9f9')
				)

			);
			$styleArrayF13 = array(
				'font' => array(
					'bold' => true,
					'size' => 15,
					'name'  => 'Cairo-Bold'
				)

			);
			$styleArrayF11 = array(
				'font' => array(
					//'bold' => true,
					'size' => 11,
					'name'  => 'Cairo-Bold'
				)

			);
            $styleArrayFillGreen = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => '5cb85c')
				)

			);
			$styleArrayFillYellow = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'f0ad4e')
				)

			);
			$styleArrayF11Bold = array(
				'font' => array(
					'bold' => true,
					'size' => 11,
					'name'  => 'Cairo-Bold'
				)

			);

            $this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, lang('Manufacturing_list'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);



            $this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->mergeCells('A2:H2');

            $row = 3;

            $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($styleArrayFill);

            $row++;

            $this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("Sr#"));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


			$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("date"));
			$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("Bill_Of_Material"));
			$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("Bill_quantity"));
			$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Deadline_Start"));
			$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang("created_by"));
			$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $this->excel->getActiveSheet()->SetCellValue('G' . $row, lang("description"));
			$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $this->excel->getActiveSheet()->SetCellValue('H' . $row, lang("Status"));
			$this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $this->excel->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->applyFromArray($styleArrayFill);
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->applyFromArray($styleArrayF11Bold);

            $row++;
            $i=1;
            foreach ($manufacturing as $value) {
                $this->excel->getActiveSheet()->SetCellValue('A' . $row, $i);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->hrld($value->created_date));
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->name);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->quantity);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->hrsd($value->deadline_start));
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->created_by);
                $entryDescription = str_replace("<p>", "", ($value->description) . "");
				$entryDescription = str_replace("</p>", "", ($entryDescription) . "");
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $entryDescription);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, lang($value->status));



                $i++;
                $row++;
            }


            $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
            $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);



            $filename = lang('Manufacturing_list');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);


            //$this->sma->print_arrays($manufacturing);
        }else{
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['manufacturing']   = $this->manufacturing_model->getAllManufacturingDataTable();
            $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Manufacturing_list')]];
            $meta                   = ['page_title' => lang('Manufacturing_list'), 'bc' => $bc];
            $this->page_construct('manufacturing/index', $meta, $this->data);
        }
    }

	public function confirmed()
    {
        $this->sma->checkPermissions('confirmed',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $this->data['manufacturing']   = $this->manufacturing_model->getAllConfirmedManufacturingDataTable();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Confirmed_products')]];
        $meta                   = ['page_title' => lang('Confirmed_products'), 'bc' => $bc];
        $this->page_construct('manufacturing/confirmed', $meta, $this->data);
    }

	public function progress()
    {
        $this->sma->checkPermissions('progress',true);


		$this->data['warehouses']   = $this->site->getAllWarehouses();
        //$this->data['manufacturing']   = $this->manufacturing_model->getAllConfirmedManufacturingDataTable();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('In_progress_products')]];
        $meta                   = ['page_title' => lang('In_progress_products'), 'bc' => $bc];
        $this->page_construct('manufacturing/progress', $meta, $this->data);
    }

	public function done()
    {
        $this->sma->checkPermissions('done',true);
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        //$this->data['manufacturing']   = $this->manufacturing_model->getAllConfirmedManufacturingDataTable();
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Done_products')]];
        $meta                   = ['page_title' => lang('Done_products'), 'bc' => $bc];
        $this->page_construct('manufacturing/done', $meta, $this->data);
    }

	public function dashboard()
    {
        if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            admin_redirect('sync');
        }

		$warehouse_id = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;

        $this->data['error']          = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['warehouse_id']   = $warehouse_id;
        $this->data['warehouses']     = $this->site->getAllWarehouses();

        // Real manufacturing metrics
        $this->data['stats']           = $this->manufacturing_model->getDashboardStats($warehouse_id);
        $this->data['costs']           = $this->manufacturing_model->getCostSummary($warehouse_id);
        $this->data['monthly_trends']  = $this->manufacturing_model->getMonthlyTrends(12);
        $this->data['top_products']    = $this->manufacturing_model->getTopManufacturedProducts(5);
        $this->data['material_alerts'] = $this->manufacturing_model->getRawMaterialAlerts(6);
        $this->data['recent_orders']   = $this->manufacturing_model->getRecentManufacturingOrders(8);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('manufacturing'), 'page' => lang('Manufacturing')], ['link' => '#', 'page' => lang('dashboard')]];
        $meta = ['page_title' => lang('manufacturing_dashboard'), 'bc' => $bc];
        $this->page_construct('manufacturing/dashboard', $meta, $this->data);
    }


	public function done_view($id = null)
    {
        $this->sma->checkPermissions('done_view', true);
		$man_details = $this->manufacturing_model->getManufacturingByID($id);
		$bom = $this->manufacturing_model->getBomByID($man_details->bom_id);
        //$this->sma->print_arrays($bom->product_id);
        $id=$bom->product_id;
        $pr_details = $this->site->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            $this->sma->md();
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->site->getProductComboItems($id);
			$this->data['combo_items_avg_cost'] = $this->site->getProductComboItemsWhitAVGCost($id);

        }
        $this->data['product']     = $pr_details;
        $this->data['unit']        = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand']       = $this->site->getBrandByID($pr_details->brand);
        $this->data['images']      = $this->products_model->getProductPhotos($id);
        $this->data['category']    = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : null;
        $this->data['tax_rate']    = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : null;
        $this->data['warehouses']  = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options']     = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants']    = $this->products_model->getProductOptions($id);
		$this->data['product_price']        = $this->products_model->getProductPriceByPId($id);

        $this->load->view($this->theme . 'products/modal_view', $this->data);
    }


	public function bom_details($id = null)
    {
        $this->sma->checkPermissions('bom_details',true);
        $this->load->helper('security');
        //$warehouses = $this->site->getAllWarehouses();
        //$this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


		$bom_details = $this->manufacturing_model->getBomByID($id);

		if (!$id || !$bom_details) {
            $this->session->set_flashdata('error', lang('Bill_Of_Material_not_found'));
            admin_redirect('manufacturing/bom');
        }

        //$this->sma->print_arrays($bom_details);






            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['bom_details']     = $bom_details;
			$this->data['created_by']  = $this->site->getUser($bom_details->created_by);
			$this->data['updated_by']  = $bom_details->updated_by ? $this->site->getUser($bom_details->updated_by) : null;
			$this->data['warehouses']  = $bom_details->product_id ? $this->products_model->getAllWarehousesWithPQ($bom_details->product_id) : null;
			$this->data['product']  = $bom_details->product_id ?$this->site->getProductByID($bom_details->product_id) : null;
			$this->data['categories']          = $this->site->getAllCategories();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');

            //$this->data['tax_rates']           = $this->site->getAllTaxRates();
            //$this->data['combo_items']         = ($id && $this->data['product']->type == 'combo') ? $this->products_model->getProductComboItems($id) : null;
            $this->data['bom_items']         = ($id) ? $this->manufacturing_model->getBomDetails($id) : null;
            $this->data['bom_labors']         = ($id) ? $this->manufacturing_model->getBomDetailsLC($id) : null;
            $this->data['bom_expenses']         = ($id) ? $this->manufacturing_model->getBomDetailsIE($id) : null;
            if ($this->Settings->accounting){

                $this->data['expenses_accounts'] = $this->accounts_model->getAccountsByCategory('المصروفات');
            }
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('manufacturing'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Bill_Of_Material_details')]];
            $meta                              = ['page_title' => lang('Bill_Of_Material_details'), 'bc' => $bc];
            $this->page_construct('manufacturing/bom_details', $meta, $this->data);




    }

	public function bom_update($id = null)
    {
        $this->sma->checkPermissions('bom_update',true);
        $this->load->helper('security');
        //$warehouses = $this->site->getAllWarehouses();
        //$this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


		$bom_details = $this->manufacturing_model->getBomByID($id);

		if (!$id || !$bom_details) {
            $this->session->set_flashdata('error', lang('Bill_Of_Material_not_found'));
            admin_redirect('manufacturing/bom');
        }

        //$this->sma->print_arrays($bom_details);






            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['bom_details']     = $bom_details;
			$this->data['created_by']  = $this->site->getUser($bom_details->created_by);
			$this->data['updated_by']  = $bom_details->updated_by ? $this->site->getUser($bom_details->updated_by) : null;
			$this->data['warehouses']  = $bom_details->product_id ? $this->products_model->getAllWarehousesWithPQ($bom_details->product_id) : null;
			$this->data['product']  = $bom_details->product_id ?$this->site->getProductByID($bom_details->product_id) : null;
			$this->data['categories']          = $this->site->getAllCategories();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');

            //$this->data['tax_rates']           = $this->site->getAllTaxRates();
            //$this->data['combo_items']         = ($id && $this->data['product']->type == 'combo') ? $this->products_model->getProductComboItems($id) : null;
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['bom_items']         = ($id) ? $this->manufacturing_model->getBomDetails($id) : null;
            $this->data['bom_labors']         = ($id) ? $this->manufacturing_model->getBomDetailsLC($id) : null;
            $this->data['bom_expenses']         = ($id) ? $this->manufacturing_model->getBomDetailsIE($id) : null;
            if ($this->Settings->accounting){

                $this->data['expenses_accounts'] = $this->accounts_model->getAccountsByCategory('المصروفات');
            }
            //$this->sma->print_arrays($this->data['bom_expenses']);
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('manufacturing'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Edit_Bill_Of_Material')]];
            $meta                              = ['page_title' => lang('Edit_Bill_Of_Material'), 'bc' => $bc];
            $this->page_construct('manufacturing/bom_update', $meta, $this->data);




    }


	public function checkstock($id = null)
    {
        $this->sma->checkPermissions('checkstock',true);
        $this->load->helper('security');
        //$warehouses = $this->site->getAllWarehouses();
        //$this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


		$man_details = $this->manufacturing_model->getManufacturingByID($id);
		//$this->sma->print_arrays($man_details);

	if (!$id || !$man_details || $man_details->status!="confirmed") {
            $this->session->set_flashdata('error', lang('Manufacturing_not_found'));
            admin_redirect('manufacturing/confirmed');
        }

        $bom = $this->manufacturing_model->getBomByID($man_details->bom_id);
        $bom_details = $this->manufacturing_model->getBomDetails($bom->id);
		//$this->sma->print_arrays($bom_details);






            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['man_details']     = $man_details;
            $this->data['bom_details']     = $bom;
            $this->data['bom_detail']     = $bom_details;
			$this->data['created_by']  = $this->site->getUser($man_details->created_by);
			$this->data['warehouses']  = $bom->product_id ? $this->products_model->getAllWarehousesWithPQ($bom->product_id) : null;
			$this->data['product']  = $bom->product_id ?$this->site->getProductByID($bom->product_id) : null;
			$this->data['categories']          = $this->site->getAllCategories();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');

            //$this->data['tax_rates']           = $this->site->getAllTaxRates();
            //$this->data['combo_items']         = ($id && $this->data['product']->type == 'combo') ? $this->products_model->getProductComboItems($id) : null;
            $this->data['bom_items']         = ($bom->id) ? $this->manufacturing_model->getBomDetails($bom->id) : null;
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('manufacturing'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Check_stock')]];
            $meta                              = ['page_title' => lang('Check_stock'), 'bc' => $bc];
            $this->page_construct('manufacturing/checkstock', $meta, $this->data);




    }
	public function progress_details($id = null)
    {
        $this->sma->checkPermissions('progress_details',true);
        $this->load->helper('security');
        //$warehouses = $this->site->getAllWarehouses();
        //$this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


		$man = $this->manufacturing_model->getManufacturingByID($id);
		//$this->sma->print_arrays($man_details);

	if (!$id || !$man || $man->status!="produce") {
            $this->session->set_flashdata('error', lang('Manufacturing_not_found'));
            admin_redirect('manufacturing/progress');
        }

        $bom = $this->manufacturing_model->getBomByID($man->bom_id);
        $bom_details = $this->manufacturing_model->getBomDetails($bom->id);
		$man_details = $this->manufacturing_model->getManufacturingDetails($id,'normal');
		$man_req_details = $this->manufacturing_model->getManufacturingDetails($id,'request');
		$man_dest_details = $this->manufacturing_model->getManufacturingDetails($id,'destruction');

		//$this->sma->print_arrays($bom_details);

        $man_labors = $this->manufacturing_model->getManufacturingDetailsLC($id,'normal');
		$man_expenses = $this->manufacturing_model->getManufacturingDetailsIE($id,'normal');




            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['man_details']     = $man;
            $this->data['bom_details']     = $bom;
            $this->data['bom_detail']     = $man_details;
            $this->data['bom_labors']     = $man_labors;
			$this->data['bom_expenses']     = $man_expenses;
			$this->data['created_by']  = $this->site->getUser($man->created_by);
			$this->data['warehouses']  = $bom->product_id ? $this->products_model->getAllWarehousesWithPQ($bom->product_id) : null;
			$this->data['product']  = $bom->product_id ?$this->site->getProductByID($bom->product_id) : null;
			$this->data['categories']          = $this->site->getAllCategories();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');

            //$this->data['tax_rates']           = $this->site->getAllTaxRates();
            //$this->data['combo_items']         = ($id && $this->data['product']->type == 'combo') ? $this->products_model->getProductComboItems($id) : null;
            $this->data['req_items']         = ($man_req_details) ? $man_req_details : null;
            $this->data['dest_items']         = ($man_dest_details) ? $man_dest_details : null;
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('manufacturing'), 'page' => lang('products')], ['link' => '#', 'page' => lang('In_progress_product_details')]];
            $meta                              = ['page_title' => lang('In_progress_product_details'), 'bc' => $bc];
            $this->page_construct('manufacturing/progress_details', $meta, $this->data);




    }


	public function progress_manufacturing($id = null)
    {
        $this->sma->checkPermissions('update_status',true);
        $this->load->helper('security');
        //$warehouses = $this->site->getAllWarehouses();
        //$this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


		$man = $this->manufacturing_model->getManufacturingByID($id);
		//$this->sma->print_arrays($man_details);

	if (!$id || !$man || $man->status!="produce") {
            $this->session->set_flashdata('error', lang('Manufacturing_not_found'));
            admin_redirect('manufacturing/progress');
        }

        $bom = $this->manufacturing_model->getBomByID($man->bom_id);
        $bom_details = $this->manufacturing_model->getBomDetails($bom->id);
		$man_details = $this->manufacturing_model->getManufacturingDetails($id,'normal');
		$man_req_details = $this->manufacturing_model->getManufacturingDetails($id,'request');
		$man_dest_details = $this->manufacturing_model->getManufacturingDetails($id,'destruction');

		$man_labors = $this->manufacturing_model->getManufacturingDetailsLC($id,'normal');
		$man_expenses = $this->manufacturing_model->getManufacturingDetailsIE($id,'normal');
		//$this->sma->print_arrays($man_labors);






            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['man_details']     = $man;
            $this->data['bom_details']     = $bom;
            $this->data['bom_detail']     = $man_details;
			$this->data['bom_labors']     = $man_labors;
			$this->data['bom_expenses']     = $man_expenses;
			$this->data['created_by']  = $this->site->getUser($man->created_by);
			$this->data['warehouses']  = $bom->product_id ? $this->products_model->getAllWarehousesWithPQ($bom->product_id) : null;
			$this->data['product']  = $bom->product_id ?$this->site->getProductByID($bom->product_id) : null;
			$this->data['categories']          = $this->site->getAllCategories();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');

            //$this->data['tax_rates']           = $this->site->getAllTaxRates();
            //$this->data['combo_items']         = ($id && $this->data['product']->type == 'combo') ? $this->products_model->getProductComboItems($id) : null;
            $this->data['req_items']         = ($man_req_details) ? $man_req_details : null;
            $this->data['dest_items']         = ($man_dest_details) ? $man_dest_details : null;
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('manufacturing'), 'page' => lang('products')], ['link' => '#', 'page' => lang('manufacturing')]];
            $meta                              = ['page_title' => lang('manufacturing'), 'bc' => $bc];
            $this->page_construct('manufacturing/progress_manufacturing', $meta, $this->data);




    }


	public function done_product($id = null)
    {
        $this->sma->checkPermissions('update_status',true);
        $this->load->helper('security');
        //$warehouses = $this->site->getAllWarehouses();
        //$this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


		$man = $this->manufacturing_model->getManufacturingByID($id);
		//$this->sma->print_arrays($man);

	if (!$id || !$man || $man->status!="produce") {
            $this->session->set_flashdata('error', lang('Manufacturing_not_found'));
            admin_redirect('manufacturing/progress');
        }

        $bom = $this->manufacturing_model->getBomByID($man->bom_id);
        $bom_details = $this->manufacturing_model->getBomDetails($bom->id);
		//$man_details = $this->manufacturing_model->getManufacturingDetails($id);
		$man_details = $this->manufacturing_model->getManufacturingDetails($id,'normal');
		$man_req_details = $this->manufacturing_model->getManufacturingDetails($id,'request');
		$man_dest_details = $this->manufacturing_model->getManufacturingDetails($id,'destruction');

        $man_labors = $this->manufacturing_model->getManufacturingDetailsLC($id,'normal');
		$man_expenses = $this->manufacturing_model->getManufacturingDetailsIE($id,'normal');

        $total_labors=0;
        foreach ($man_labors as $item) {

            $total_labors=$total_labors+($item->actual_hours_no*$item->hours_wage);

        }

        $total_expenses=0;
        foreach ($man_expenses as $item) {

            $total_expenses=$total_expenses+($item->actual_cost);

        }

		$totalQty=$bom->quantity*$man->quantity;
		$date = date('Y-m-d H:i:s');

        $total=0;
        $total_main=0;
        foreach ($man_dest_details as $item) {
			$pr=$this->site->getProductByID($item->id);
			$avg_cost=$this->manufacturing_model->getBomDetailsProductId($man->bom_id,$item->id)->product_avg_cost;
			$un=$this->site->getUnitByID($item->unit_id);
			$total=$total+($item->qty*$avg_cost);
		$productTR = [
             			'product_id'      => $item->id,
                        'product_code'      => $item->code,
                        'product_name'      => $pr->name,
                        'option_id'      => NULL,
                        'net_unit_cost'      => $avg_cost,
                        'unit_cost'      => $avg_cost,
                        'real_unit_cost'      => $avg_cost,
                        'quantity'      => $item->qty,
                        'received_quantity'      => $item->qty,
                        'unit_quantity'      => $item->qty,
                        'quantity_balance'      => $item->qty,
                        'product_unit_id'      => $item->unit_id,
                        'product_unit_code'      => $un->code,
                        'warehouse_id'      => $bom->wh_destruction,
                        'item_tax'      => 0,
                        'expiry'      => '0000-00-00',
                        'subtotal'      => $this->sma->formatDecimal($item->qty*$avg_cost,4),
                        'date'      => date('Y-m-d', strtotime($date)),
                    ];

                    $productsTR[] = $productTR;
		}


	$wh_from=$this->site->getWarehouseByID($bom->wh_manufacturing);
	$wh_to=$this->site->getWarehouseByID($bom->wh_destruction);

	$dataTR = [
                        'transfer_no'        => $this->site->getReference('to'),
                        'date'        => $date,
                        'from_warehouse_id'      => $bom->wh_manufacturing,
                        'from_warehouse_code'      => $wh_from->code,
                        'from_warehouse_name'      => $bom->wh_manufacturing_name,
                        'to_warehouse_id'      => $bom->wh_destruction,
                        'to_warehouse_code'      => $wh_to->code,
                        'to_warehouse_name'      => $bom->wh_destruction_name,
                        'total_tax'      => 0,
                        'shipping'      => 0,
                        'total'      => $this->sma->formatDecimal($total,4),
                        'grand_total'      => $this->sma->formatDecimal($total,4),
                        'created_by'          => $this->session->userdata('user_id'),
						'status'      => 'completed',
						'man_id'      => $id,
						'note'      => lang('Transfer_warehouse_from_manufacture').' '.$bom->name,
					];



                    foreach ($man_details as $item) {

                        $pr=$this->site->getProductByID($item->id);
                        $avg_cost=$this->manufacturing_model->getBomDetailsProductId($man->bom_id,$item->id)->product_avg_cost;
                        $un=$this->site->getUnitByID($item->unit_id);
                        $total_main=$total_main+($item->actual_qty*$avg_cost);
                    $productTR = [
                                     'product_id'      => $item->id,
                                    'product_code'      => $item->code,
                                    'product_name'      => $pr->name,
                                    'option_id'      => NULL,
                                    'net_unit_cost'      => $avg_cost,
                                    'unit_cost'      => $avg_cost,
                                    'real_unit_cost'      => $avg_cost,
                                    'quantity'      => $item->actual_qty,
                                    'received_quantity'      => $item->actual_qty,
                                    'unit_quantity'      => $item->actual_qty,
                                    'quantity_balance'      => $item->actual_qty,
                                    'product_unit_id'      => $item->unit_id,
                                    'product_unit_code'      => $un->code,
                                    'warehouse_id'      => $bom->wh_destruction,
                                    'item_tax'      => 0,
                                    'expiry'      => '0000-00-00',
                                    'subtotal'      => $this->sma->formatDecimal($item->actual_qty*$avg_cost,4),
                                    'date'      => date('Y-m-d', strtotime($date)),
                                ];

                                $productsTRMain[] = $productTR;
                    }
                    //$this->sma->print_arrays($productsTRMain);
                    foreach ($man_req_details as $item) {
                        $pr=$this->site->getProductByID($item->id);
                        $avg_cost=$this->manufacturing_model->getManufacturingDetailsProductId($man->id,$man->bom_id,$item->id)->product_avg_cost;
                        $un=$this->site->getUnitByID($item->unit_id);
                        $total_main=$total_main+($item->qty*$avg_cost);
                    $productTR = [
                                     'product_id'      => $item->id,
                                    'product_code'      => $item->code,
                                    'product_name'      => $pr->name,
                                    'option_id'      => NULL,
                                    'net_unit_cost'      => $avg_cost,
                                    'unit_cost'      => $avg_cost,
                                    'real_unit_cost'      => $avg_cost,
                                    'quantity'      => $item->qty,
                                    'received_quantity'      => $item->qty,
                                    'unit_quantity'      => $item->qty,
                                    'quantity_balance'      => $item->qty,
                                    'product_unit_id'      => $item->unit_id,
                                    'product_unit_code'      => $un->code,
                                    'warehouse_id'      => $bom->wh_destruction,
                                    'item_tax'      => 0,
                                    'expiry'      => '0000-00-00',
                                    'subtotal'      => $this->sma->formatDecimal($item->qty*$avg_cost,4),
                                    'date'      => date('Y-m-d', strtotime($date)),
                                ];

                                $productsTRMain[] = $productTR;
                    }


                    $wh_to_main=$this->site->getWarehouseByID($bom->wh_complete);

	$dataTRMain = [
                        'transfer_no'        => $this->site->getReference('to'),
                        'date'        => $date,
                        'from_warehouse_id'      => $bom->wh_manufacturing,
                        'from_warehouse_code'      => $wh_from->code,
                        'from_warehouse_name'      => $bom->wh_manufacturing_name,
                        'to_warehouse_id'      => $bom->wh_complete,
                        'to_warehouse_code'      => $wh_to_main->code,
                        'to_warehouse_name'      => $bom->wh_complete_name,
                        'total_tax'      => 0,
                        'shipping'      => 0,
                        'total'      => $this->sma->formatDecimal($total_main,4),
                        'grand_total'      => $this->sma->formatDecimal($total_main,4),
                        'created_by'          => $this->session->userdata('user_id'),
						'status'      => 'completed',
						'man_id'      => $id,
						'note'      => lang('Transfer_warehouse_from_manufacture').' '.$bom->name,
					];




		//

		/*foreach ($man_details as $product) {
				$clause = ['product_id' => $product->id, 'option_id' => '', 'warehouse_id' => $wh_manufacturing, 'status' => 'received'];
				//$this->site->setPurchaseItem($clause, (0 - $product->qty));
				//$this->site->syncProductQty($product->id, $wh_manufacturing);
            	$cost=$cost+($product->qty*$product->avg_cost);
            	}
				$cost=($cost/$man->confirm_qty);


		$this->sma->print_arrays($cost,$id,$man_details,$man_req_details,$man_dest_details,$bom->wh_manufacturing,$bom->wh_complete,$bom->wh_destruction,$totalQty,$man->quantity,$bom->product_id,$man->confirm_qty,$dataTR,$productsTR);

		*/

        //$clause1 = ['product_id' => $bom->product_id, 'option_id' => NULL, 'warehouse_id' => $bom->wh_complete, 'status' => 'received'];

        //$this->sma->print_arrays($clause1);
       // $this->sma->print_arrays($this->site->getPurchasedItem($clause1));
        //$this->site->setPurchaseItem($clause1,  $man->confirm_qty);
        //$this->sma->print_arrays($id,$man_details,$man_req_details,$man_dest_details,$bom->wh_manufacturing,$bom->wh_complete,$bom->wh_destruction,$totalQty,$man->quantity,$bom->product_id,$man->confirm_qty,$dataTR,$productsTR,$dataTRMain,$productsTRMain);


        //$this->sma->print_arrays($dataTR,$productsTR);

        //$this->sma->print_arrays($id,$man_details,$man_req_details,$man_dest_details,$bom->wh_manufacturing,$bom->wh_complete,$bom->wh_destruction,$totalQty,$man->quantity,$bom->product_id,$man->confirm_qty,$dataTR,$productsTR,$dataTRMain,$productsTRMain);

            if ($this->manufacturing_model->addDone($id,$man_details,$man_req_details,$man_dest_details,$bom->wh_manufacturing,$bom->wh_complete,$bom->wh_destruction,$totalQty,$man->quantity,$bom->product_id,$man->confirm_qty,$dataTR,$productsTR,$dataTRMain,$productsTRMain,$total_labors,$total_expenses)) {
            $this->session->set_flashdata('message', lang('Manufacturing_product_added_successfully'));
            //admin_redirect('system_settings/brands');
			admin_redirect('manufacturing/done');
        } else {
			admin_redirect('manufacturing/progress');
        }



    }


	public function add_bom()
    {
        $this->sma->checkPermissions('add_bom',true);
        $this->form_validation->set_rules('name', lang('name'), 'required');




        if ($this->form_validation->run() == true) {

			$created_date  = date('Y-m-d H:i:s');

            $data     = [
                'created_date'              => $created_date,
                'code'              => $this->input->post('code'),
                'name'              => $this->input->post('name'),
                'wh_raw'              => $this->input->post('wh_raw'),
                'wh_manufacturing'              => $this->input->post('wh_manufacturing'),
                'wh_complete'              => $this->input->post('wh_complete'),
                'wh_destruction'              => $this->input->post('wh_destruction'),
                'wh_raw_name'              => $this->site->getWarehouseByID($this->input->post('wh_raw'))->name,
                'wh_manufacturing_name'              => $this->site->getWarehouseByID($this->input->post('wh_manufacturing'))->name,
                'wh_complete_name'              => $this->site->getWarehouseByID($this->input->post('wh_complete'))->name,
                'wh_destruction_name'              => $this->site->getWarehouseByID($this->input->post('wh_destruction'))->name,
                'description'   => $this->input->post('description'),
                'quantity'    => $this->sma->formatDecimal($this->input->post('quantity')),
				'created_by'          => $this->session->userdata('user_id'),


            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_bom')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->manufacturing_model->addBom($data)) {
            $this->session->set_flashdata('message', lang('Bill_Of_Material_added_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['warehouses'] = $this->site->getAllWarehouses();

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'manufacturing/add_bom', $this->data);
        }
    }

	public function add_manufacturing()
    {
        $this->sma->checkPermissions('add_manufacturing',true);
        $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('quantity', lang('quantity'), 'required');
        $this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');



        if ($this->form_validation->run() == true) {

			$bom_id=$this->input->post('bom_id');
		$bom = $this->manufacturing_model->getBomByID($bom_id);
		$bom_details = $this->manufacturing_model->getBomDetails($bom_id);
		//$this->sma->print_arrays($bom_details);

		if(!$bom->product_id || !$bom_details){
			    $this->session->set_flashdata('error', lang('Bill_Of_Material_not_completed'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);

		}

			$deadline_start = $this->sma->fld(trim($this->input->post('deadline_start')));
            $created_date  = date('Y-m-d H:i:s');

            $data     = [
                'created_date'              => $created_date,
                'deadline_start'              => $deadline_start,
                'bom_id'              => $this->input->post('bom_id'),
                'description'   => $this->input->post('description'),
                'quantity'    => $this->sma->formatDecimal($this->input->post('quantity')),
				'created_by'          => $this->session->userdata('user_id'),
				'status'          => 'confirmed',


            ];



			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('add_manufacturing')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->manufacturing_model->addManufacturing($data)) {
            $this->session->set_flashdata('message', lang('Manufacturing_product_added_successfully'));
            admin_redirect('manufacturing/confirmed');
			//redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['boms'] = $this->manufacturing_model->getAllBom();

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'manufacturing/add_manufacturing', $this->data);
        }
    }


	public function edit_manufacturing($id)
    {
        $this->sma->checkPermissions('edit_manufacturing',true);
        $this->form_validation->set_rules('bom_id', lang('Bill_Of_Material'), 'required');
        $this->form_validation->set_rules('quantity', lang('quantity'), 'required');
        $this->form_validation->set_rules('deadline_start', lang('Deadline_Start'), 'required');

		$man_details = $this->manufacturing_model->getManufacturingByID($id);




        if ($this->form_validation->run() == true) {

				$bom_id=$this->input->post('bom_id');
		$bom = $this->manufacturing_model->getBomByID($bom_id);
		$bom_details = $this->manufacturing_model->getBomDetails($bom_id);
		//$this->sma->print_arrays($bom_details);

		if(!$bom->product_id || !$bom_details){
			    $this->session->set_flashdata('error', lang('Bill_Of_Material_not_completed'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);

		}


			$deadline_start = $this->sma->fld(trim($this->input->post('deadline_start')));
            $created_date  = date('Y-m-d H:i:s');

            $data     = [
                'deadline_start'              => $deadline_start,
                'bom_id'              => $this->input->post('bom_id'),
                'description'   => $this->input->post('description'),
                'quantity'    => $this->sma->formatDecimal($this->input->post('quantity')),
				'status'          => 'confirmed',


            ];



			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_manufacturing')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->manufacturing_model->updateManufacturing($id,$data)) {
            $this->session->set_flashdata('message', lang('Manufacturing_product_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['boms'] = $this->manufacturing_model->getAllBom();
			$this->data['man_details'] = $man_details;

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'manufacturing/edit_manufacturing', $this->data);
        }
    }


	public function edit_bom($id)
    {
        $this->form_validation->set_rules('name', lang('name'), 'required');




        if ($this->form_validation->run() == true) {

			$updated_at  = date('Y-m-d H:i:s');

            $data     = [
                'updated_at'              => $updated_at,
                'code'              => $this->input->post('code'),
                'name'              => $this->input->post('name'),
                'wh_raw'              => $this->input->post('wh_raw'),
                'wh_manufacturing'              => $this->input->post('wh_manufacturing'),
                'wh_complete'              => $this->input->post('wh_complete'),
                'wh_destruction'              => $this->input->post('wh_destruction'),
                'wh_raw_name'              => $this->site->getWarehouseByID($this->input->post('wh_raw'))->name,
                'wh_manufacturing_name'              => $this->site->getWarehouseByID($this->input->post('wh_manufacturing'))->name,
                'wh_complete_name'              => $this->site->getWarehouseByID($this->input->post('wh_complete'))->name,
                'wh_destruction_name'              => $this->site->getWarehouseByID($this->input->post('wh_destruction'))->name,
                'description'   => $this->input->post('description'),
                'quantity'    => $this->sma->formatDecimal($this->input->post('quantity')),
				'updated_by'          => $this->session->userdata('user_id'),


            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();
            }

			//$this->sma->print_arrays($data);

        } elseif ($this->input->post('edit_bom')) {
            $this->session->set_flashdata('error', validation_errors());
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->manufacturing_model->updateBom($id,$data)) {
            $this->session->set_flashdata('message', lang('Bill_Of_Material_updated_successfully'));
            //admin_redirect('system_settings/brands');
			redirect($_SERVER['HTTP_REFERER']);
        } else {
			$this->data['bom_details'] = $this->manufacturing_model->getBomByID($id);

			$this->data['warehouses'] = $this->site->getAllWarehouses();

            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'manufacturing/edit_bom', $this->data);
        }
    }



	function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        $warehouse_id = $this->input->get('warehouse_id', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        //$rows = $this->products_model->getProductNames2($term);
        $rows = $this->sales_model->getProductNames2Combo($term, $warehouse_id);

		if ($rows) {
            foreach ($rows as $row) {


				$rows2=$this->site->getProductByCode($row->code);
				if($this->site->getProduct_avg_cost($rows2->id,$warehouse_id))
				$avg_cost=$this->site->getProduct_avg_cost($rows2->id,$warehouse_id);
			    else{
				$avg_cost=0;
				}


                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1,'unit_id'=>$row->unit,'unit_name'=>$this->site->getUnitByID($row->unit)->name,'avg_cost' => $avg_cost);
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

	function suggestions_dist()
    {
        $term = $this->input->get('term', TRUE);
        $warehouse_id = $this->input->get('warehouse_id', TRUE);
        $man_id = $this->input->get('man_id', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        //$rows = $this->products_model->getProductNames2($term);
        $rows = $this->manufacturing_model->getProductNames($term, $warehouse_id,$man_id);

		if ($rows) {
            foreach ($rows as $row) {


				$rows2=$this->site->getProductByCode($row->code);
				if($this->site->getProduct_avg_cost($rows2->id,$warehouse_id))
				$avg_cost=$this->site->getProduct_avg_cost($rows2->id,$warehouse_id);
			    else{
				$avg_cost=0;
				}


                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1,'unit_id'=>$row->unit,'unit_name'=>$this->site->getUnitByID($row->unit)->name,'avg_cost' => $avg_cost);
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

	public function addBomDetail(){
		$dat = array(
			'bom_id'	=>	$this->input->post('bom_id'),
			'product_id'	=>	$this->input->post('product_id'),
			'product_code'	=>	$this->input->post('product_code'),
			'product_quantity'	=>	$this->input->post('product_quantity'),
			'product_unit'	=>	$this->input->post('product_unit'),
			'product_avg_cost'	=>	$this->input->post('product_avg_cost'),
		);
		$data=$this->manufacturing_model->addBomDetails($dat);
		echo json_encode($data);
	}


	public function addManufacturingDetail(){

		$man_id=$this->input->post('man_id');
		$bom_id=$this->input->post('bom_id');
		$items=$this->input->post('items');

		$bom_details = $this->manufacturing_model->getBomByID($bom_id);
		$man_details = $this->manufacturing_model->getManufacturingByID($man_id);
		$confirm_qty=($bom_details->quantity*$man_details->quantity);
		$date = date('Y-m-d H:i:s');


            $total=0;
        foreach ($items as $item) {
			$pr=$this->site->getProductByID($item['id']);
			$avg_cost=$this->manufacturing_model->getBomDetailsProductId($bom_id,$item['id'])->product_avg_cost;
			$un=$this->site->getUnitByID($item['unit_id']);
			$total=$total+($item['qty']*$avg_cost);
		$product = [
                        'man_id'        => $man_id,
                        'bom_id'        => $bom_id,
                        'product_id'      => $item['id'],
                        'product_code'      => $item['code'],
                        'product_quantity'      => $item['qty'],
                        'actual_qty'      => $item['qty'],
                        'product_unit'      => $item['unit_id'],
                        'product_avg_cost'      => $avg_cost,
                        'req_status'      => 'normal',
                    ];

                    $products[] = $product;

		$productTR = [
             			'product_id'      => $item['id'],
                        'product_code'      => $item['code'],
                        'product_name'      => $pr->name,
                        'option_id'      => NULL,
                        'net_unit_cost'      => $avg_cost,
                        'unit_cost'      => $avg_cost,
                        'real_unit_cost'      => $avg_cost,
                        'quantity'      => $item['qty'],
                        'received_quantity'      => $item['qty'],
                        'unit_quantity'      => $item['qty'],
                        'quantity_balance'      => $item['qty'],
                        'product_unit_id'      => $item['unit_id'],
                        'product_unit_code'      => $un->code,
                        'warehouse_id'      => $bom_details->wh_manufacturing,
                        'item_tax'      => 0,
                        'expiry'      => '0000-00-00',
                        'subtotal'      => $this->sma->formatDecimal($item['qty']*$avg_cost),
                        'date'      => date('Y-m-d', strtotime($date)),
                    ];

                    $productsTR[] = $productTR;
		}


        $bom_labors         = $this->manufacturing_model->getBomDetailsLC($bom_id);
        if($bom_labors){
            foreach($bom_labors as $item){
                $product_labors = [
                    'man_id'        => $man_id,
                    'bom_id'        => $bom_id,
                    'labor_desc'      => $item->labor_desc,
                    'hours_no'      => $item->hours_no,
                    'actual_hours_no'      => $item->hours_no,
                    'hours_wage'      => $item->hours_wage,
                    'req_status'      => 'normal',
                ];

                $products_labors[] = $product_labors;
            }

        }else{
            $products_labors =null;
        }

        $bom_expenses         = $this->manufacturing_model->getBomDetailsIE($bom_id);
        if($bom_expenses){
            foreach($bom_expenses as $item){
                $product_expenses = [
                    'man_id'        => $man_id,
                    'bom_id'        => $bom_id,
                    'expenses_acct'      => $item->expenses_acct,
                    'cost'      => $item->cost,
                    'actual_cost'      => $item->cost,
                    'req_status'      => 'normal',
                ];

                $products_expenses[] = $product_expenses;
            }

        }else{
            $products_expenses =null;
        }





    //$this->sma->print_arrays($products,$dataTR,$productsTR,$man_id,$confirm_qty);


	$wh_from=$this->site->getWarehouseByID($bom_details->wh_raw);
	$wh_to=$this->site->getWarehouseByID($bom_details->wh_manufacturing);

	$dataTR = [
                        'transfer_no'        => $this->site->getReference('to'),
                        'date'        => $date,
                        'from_warehouse_id'      => $bom_details->wh_raw,
                        'from_warehouse_code'      => $wh_from->code,
                        'from_warehouse_name'      => $bom_details->wh_raw_name,
                        'to_warehouse_id'      => $bom_details->wh_manufacturing,
                        'to_warehouse_code'      => $wh_to->code,
                        'to_warehouse_name'      => $bom_details->wh_manufacturing_name,
                        'total_tax'      => 0,
                        'shipping'      => 0,
                        'total'      => $this->sma->formatDecimal($total,4),
                        'grand_total'      => $this->sma->formatDecimal($total,4),
                        'created_by'          => $this->session->userdata('user_id'),
						'status'      => 'completed',
						'man_id'      => $man_id,
						'note'      => lang('Transfer_warehouse_from_manufacture').' '.$bom_details->name,
					];




		$data=$this->manufacturing_model->addManufacturingDetail($products,$dataTR,$productsTR,$man_id,$confirm_qty,$products_labors,$products_expenses);
		//$data=$this->manufacturing_model->addTransfer($dataTR,$productsTR);
		echo json_encode($data);
	}


	public function addManufacturingRequestDetail(){

		$man_id=$this->input->post('man_id');
		$bom_id=$this->input->post('bom_id');
		$items=$this->input->post('items');
		$items_dest=$this->input->post('items_dest');
		$actual_qty_arrs=$this->input->post('actual_qty_arr2');
		$actual_hours_no_arrs=$this->input->post('actual_hours_no_arr2');
		$actual_cost_arrs=$this->input->post('actual_cost_arr2');
		$confirm_qty=$this->input->post('confirm_qty');
        //$this->sma->print_arrays($actual_qty_arr);
		$bom_details = $this->manufacturing_model->getBomByID($bom_id);
		$date = date('Y-m-d H:i:s');

        foreach ($actual_qty_arrs as $actual_qty_arr) {
            $this->manufacturing_model->updateManufacturingDetails($man_id,$actual_qty_arr['actual_qty'],$actual_qty_arr['product_id']);
            //echo json_encode((Array) $actual_qty_arr);
            //$this->sma->send_json([ 'msg' => $man_id]);
        }


            if($trRow=$this->manufacturing_model->getTransferByManIdASC($man_id)){

                $oitems    = $this->manufacturing_model->getAllTransferItems($trRow->id, $trRow->status);

                $total=0;
				if($oitems){
		foreach ($oitems as $item) {
			$pr=$this->site->getProductByID($item->product_id);
			//$avg_cost=$this->manufacturing_model->getBomDetailsProductId($bom_id,$item->product_id)->product_avg_cost;
			$avg_cost=$item->net_unit_cost;

			$un=$this->site->getUnitByID($item->product_unit_id);
            $actual_qty=$this->manufacturing_model->getManufacturingDetailsProductId($man_id,$bom_id,$item->product_id)->actual_qty;

			$total=$total+($actual_qty*$avg_cost);


		$productTRRow = [
             			'product_id'      => $item->product_id,
                        'product_code'      => $item->product_code,
                        'product_name'      => $pr->name,
                        'option_id'      => NULL,
                        'net_unit_cost'      => $avg_cost,
                        'unit_cost'      => $avg_cost,
                        'real_unit_cost'      => $avg_cost,
                        'quantity'      => $actual_qty,
                        'received_quantity'      => $actual_qty,
                        'unit_quantity'      => $actual_qty,
                        'quantity_balance'      => $actual_qty,
                        'product_unit_id'      => $item->product_unit_id,
                        'product_unit_code'      => $un->code,
                        'warehouse_id'      => $bom_details->wh_manufacturing,
                        'item_tax'      => 0,
                        'expiry'      => '0000-00-00',
                        'subtotal'      => $this->sma->formatDecimal($actual_qty*$avg_cost),
                        'date'      => date('Y-m-d', strtotime($date)),
                    ];

                    $productsTRRow[] = $productTRRow;
		}

	$wh_from=$this->site->getWarehouseByID($bom_details->wh_raw);
	$wh_to=$this->site->getWarehouseByID($bom_details->wh_manufacturing);

	$dataTRRow = [
                        'transfer_no'        => $this->site->getReference('to'),
                        'date'        => $trRow->date,
                        'from_warehouse_id'      => $bom_details->wh_raw,
                        'from_warehouse_code'      => $wh_from->code,
                        'from_warehouse_name'      => $bom_details->wh_raw_name,
                        'to_warehouse_id'      => $bom_details->wh_manufacturing,
                        'to_warehouse_code'      => $wh_to->code,
                        'to_warehouse_name'      => $bom_details->wh_manufacturing_name,
                        'total_tax'      => 0,
                        'shipping'      => 0,
                        'total'      => $this->sma->formatDecimal($total,4),
                        'grand_total'      => $this->sma->formatDecimal($total,4),
                        'created_by'          => $this->session->userdata('user_id'),
						'status'      => 'completed',
						'man_id'      => $man_id,
						'note'      => lang('Transfer_warehouse_from_manufacture').' '.$bom_details->name,
					];


				}

                $this->manufacturing_model->updateTransfer($this->manufacturing_model->getTransferByManId($man_id)->id,$dataTRRow, $productsTRRow);
            }




        foreach ($actual_hours_no_arrs as $actual_hours_no_arr) {
            $this->manufacturing_model->updateManufacturingDetailsLC($man_id,$actual_hours_no_arr['actual_hours_no'],$actual_hours_no_arr['id']);
            //echo json_encode((Array) $actual_qty_arr);
            //$this->sma->send_json([ 'msg' => $man_id]);

        }

        foreach ($actual_cost_arrs as $actual_cost_arr) {
            $this->manufacturing_model->updateManufacturingDetailsIE($man_id,$actual_cost_arr['actual_cost'],$actual_cost_arr['id']);
            //echo json_encode((Array) $actual_qty_arr);
            //$this->sma->send_json([ 'msg' => $man_id]);

        }


            $total=0;
				if($items){
		foreach ($items as $item) {
			$pr=$this->site->getProductByID($item['id']);
			//$avg_cost=$this->manufacturing_model->getBomDetailsProductId($bom_id,$item['id'])->product_avg_cost;
			$avg_cost=$item['avg_cost'];

			$un=$this->site->getUnitByID($item['unit_id']);
			$total=$total+($item['qty']*$avg_cost);
		$product = [
                        'man_id'        => $man_id,
                        'bom_id'        => $bom_id,
                        'product_id'      => $item['id'],
                        'product_code'      => $item['code'],
                        'product_quantity'      => $item['qty'],
                        'actual_qty'      => $item['qty'],
                        'product_unit'      => $item['unit_id'],
                        'product_avg_cost'      => $avg_cost,
                        'req_status'      => 'request',
                    ];


                    $products[] = $product;

		$productTR = [
             			'product_id'      => $item['id'],
                        'product_code'      => $item['code'],
                        'product_name'      => $pr->name,
                        'option_id'      => NULL,
                        'net_unit_cost'      => $avg_cost,
                        'unit_cost'      => $avg_cost,
                        'real_unit_cost'      => $avg_cost,
                        'quantity'      => $item['qty'],
                        'received_quantity'      => $item['qty'],
                        'unit_quantity'      => $item['qty'],
                        'quantity_balance'      => $item['qty'],
                        'product_unit_id'      => $item['unit_id'],
                        'product_unit_code'      => $un->code,
                        'warehouse_id'      => $bom_details->wh_manufacturing,
                        'item_tax'      => 0,
                        'expiry'      => '0000-00-00',
                        'subtotal'      => $this->sma->formatDecimal($item['qty']*$avg_cost),
                        'date'      => date('Y-m-d', strtotime($date)),
                    ];

                    $productsTR[] = $productTR;
		}

	$wh_from=$this->site->getWarehouseByID($bom_details->wh_raw);
	$wh_to=$this->site->getWarehouseByID($bom_details->wh_manufacturing);

	$dataTR = [
                        'transfer_no'        => $this->site->getReference('to'),
                        'date'        => $date,
                        'from_warehouse_id'      => $bom_details->wh_raw,
                        'from_warehouse_code'      => $wh_from->code,
                        'from_warehouse_name'      => $bom_details->wh_raw_name,
                        'to_warehouse_id'      => $bom_details->wh_manufacturing,
                        'to_warehouse_code'      => $wh_to->code,
                        'to_warehouse_name'      => $bom_details->wh_manufacturing_name,
                        'total_tax'      => 0,
                        'shipping'      => 0,
                        'total'      => $this->sma->formatDecimal($total,4),
                        'grand_total'      => $this->sma->formatDecimal($total,4),
                        'created_by'          => $this->session->userdata('user_id'),
						'status'      => 'completed',
						'man_id'      => $man_id,
						'note'      => lang('Transfer_warehouse_from_manufacture').' '.$bom_details->name,
					];


				}

		    $value=0;
		    $cost=0;
		    $quantity=0;
				if($items_dest){
		foreach ($items_dest as $item) {
			$pr=$this->site->getProductByID($item['id']);
			$avg_cost=$this->manufacturing_model->getBomDetailsProductId($bom_id,$item['id'])->product_avg_cost;

			$un=$this->site->getUnitByID($item['unit_id']);
			$value=$value+($item['qty']*$avg_cost);
			$cost=$cost+($avg_cost);
			$quantity=$quantity+($item['qty']);
		$product_dest = [
                        'man_id'        => $man_id,
                        'bom_id'        => $bom_id,
                        'product_id'      => $item['id'],
                        'product_code'      => $item['code'],
                        'product_quantity'      => $item['qty'],
                        'actual_qty'      => $item['qty'],
                        'product_unit'      => $item['unit_id'],
                        'product_avg_cost'      => $avg_cost,
                        'req_status'      => 'destruction',
                    ];


                    $products_dest[] = $product_dest;

		$productDT = [
             			'product_id'      => $item['id'],
                        'type'      => 'subtraction',
                        'option_id'      => NULL,
                        'cost'      => $avg_cost,
                        'quantity'      => $item['qty'],
                        'unit'      => $item['unit_id'],
                        'value'      => ($item['qty']*$avg_cost),
                        'warehouse_id'      => $bom_details->wh_manufacturing,
                    ];

                    $productsDT[] = $productDT;
		}


	$wh_from=$this->site->getWarehouseByID($bom_details->wh_raw);
	$wh_to=$this->site->getWarehouseByID($bom_details->wh_manufacturing);

	$dataDT = [
                        'reference_no'        => $this->site->getReference('qa'),
                        'date'        => $date,
                        'warehouse_id'      => $bom_details->wh_manufacturing,
                        'cost'      => $this->sma->formatDecimal($cost,4),
                        'value'      => $this->sma->formatDecimal($value,4),
                        'quantity'      => $this->sma->formatDecimal($quantity,4),
                        'created_by'          => $this->session->userdata('user_id'),
						'man_id'      => $man_id,
						'note'      => lang('Destruction_warehouse_from_manufacture').' '.$bom_details->name,
					];


	}




		$data=$this->manufacturing_model->addManufacturingRequestDetail($products,$dataTR,$productsTR,$products_dest,$dataDT,$productsDT,$man_id,$confirm_qty);
		//$data=$this->manufacturing_model->addTransfer($dataTR,$productsTR);
		echo json_encode($data);
	}


	public function addBomRequestDetail(){

		$bom_id=$this->input->post('bom_id');
		$items=$this->input->post('items');

		//$bom_details = $this->manufacturing_model->getBomByID($bom_id);
		//$date = date('Y-m-d H:i:s');


            $total=0;
				if($items){
		foreach ($items as $item) {
			$pr=$this->site->getProductByID($item['id']);
			//$avg_cost=$this->manufacturing_model->getBomDetailsProductId($bom_id,$item['id'])->product_avg_cost;
			$avg_cost=$item['avg_cost'];

			$un=$this->site->getUnitByID($item['unit_id']);
			$total=$total+($item['qty']*$avg_cost);
		$product = [
                        'bom_id'        => $bom_id,
                        'product_id'      => $item['id'],
                        'product_code'      => $item['code'],
                        'product_quantity'      => $item['qty'],
                        'product_unit'      => $item['unit_id'],
                        'product_avg_cost'      => $avg_cost,
                    ];


                    $products[] = $product;


				}

					$data=$this->manufacturing_model->addBomRequestDetail($bom_id,$products);
					echo json_encode($data);
				}else{
					echo json_encode(false);
				}




		//$data=$this->manufacturing_model->addTransfer($dataTR,$productsTR);

	}


	public function updateBomDetail(){
		$dat = array(
			'bom_id'	=>	$this->input->post('bom_id'),
			'product_id'	=>	$this->input->post('product_id'),
			'product_quantity'	=>	$this->input->post('product_quantity'),
		);
		$data=$this->manufacturing_model->updateBomDetails($dat);
		echo json_encode($data);
	}

	public function deleteBomDetail(){
		$dat = array(
			'bom_id'	=>	$this->input->post('bom_id'),
			'product_id'	=>	$this->input->post('product_id'),
		);
		$data=$this->manufacturing_model->deleteBomDetails($dat);
		echo json_encode($data);
	}



	public function suggestions2()
    {
        $term = $this->input->get('term', true);
        $warehouses = $this->input->get('warehouse_id', true);
		$analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];

		//$this->sma->print_arrays($warehouses);
		if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

		$wp=0;
		//$pr1[];
		if($warehouses)
		foreach ($warehouses as $warehouse_id) {


        $rowswp = $this->sales_model->getProductNamesCombo($sr, $warehouse_id);
		if ($rowswp){
			$wp=1;
			foreach ($rowswp as $first_row) {
			//$wpr[] = ['warehouse_id' => $warehouse_id, 'avg_cost' => $first_row->avg_cost, 'code' => $first_row->code];
			}
		}
		}


		//$rows = $this->products_model->getProductNames2($term);
		$rows = $this->sales_model->getProductNames2Combo($sr, $warehouse_id);

        if ($rows && $wp==1) {
        //if ($rows) {
            foreach ($rows as $row) {

				foreach ($warehouses as $warehouse_id){
				$rows2=$this->site->getProductByCode($row->code);
				if($this->site->getProduct_avg_cost($rows2->id,$warehouse_id))
				$avg_cost=$this->site->getProduct_avg_cost($rows2->id,$warehouse_id);
			    else{
				$avg_cost=0;
				$warehouse_id="no";
				}
			    $wpr[] = ['warehouse_id' => $warehouse_id, 'avg_cost' => $avg_cost, 'code' => $row->code, 'id' => $rows2->id];


				}
				$unit_name='';
				$rows1=$this->site->getUnitByID($row->unit);
				$pr[] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->cost, 'avg_cost' => $row->avg_cost, 'qty' => 1,'unit'=>$this->site->getUnitByID($row->unit),'wpr'=>$wpr];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }


	public function getConfirmed()
    {
        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
        //    $warehouse_id = $user->warehouse_id;
        }



        $detail_link = anchor('admin/manufacturing/checkstock/$1', '<i class="fa fa-file-text-o"></i> ' . lang('Check_stock'));
        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('Delete_a_manufacturing_product') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . admin_url('manufacturing/deletemanufacturing/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('Delete_a_manufacturing_product') . '</a>';

		$action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li><a href="' . admin_url('manufacturing/edit_manufacturing/$1') . '"  data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> ' . lang('Edit_a_manufacturing_product') . '</a></li>';
        $action .= '<li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';

		$this->load->library('datatables');

		//if ($warehouse_id) {
            $this->datatables
            //->select($this->db->dbprefix('man_bom') . ".id as bomid, {$this->db->dbprefix('man_bom')}.id as bomid2, {$this->db->dbprefix('man_bom')}.image as image, DATE_FORMAT({$this->db->dbprefix('man_bom')}.created_date, '%Y-%m-%d %T') as date,  {$this->db->dbprefix('man_bom')}.code as code, {$this->db->dbprefix('man_bom')}.name as name, {$this->db->dbprefix('man_bom')}.quantity as quantity , {$this->db->dbprefix('man_bom')}.wh_raw as wh_raw, {$this->db->dbprefix('man_bom')}.wh_manufacturing as wh_manufacturing, {$this->db->dbprefix('man_bom')}.wh_complete as wh_complete, {$this->db->dbprefix('man_bom')}.wh_destruction as wh_destruction, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, {$this->db->dbprefix('man_bom')}.description as description", false)
            ->select($this->db->dbprefix('man_manufacturing') . '.id as id, DATE_FORMAT(' . $this->db->dbprefix('man_manufacturing') . '.created_date, "%Y-%m-%d %T") as created_date, ' . $this->db->dbprefix('man_bom') . '.name as name, ' . $this->db->dbprefix('man_manufacturing') . '.quantity as quantity, ' . $this->db->dbprefix('man_manufacturing') . '.deadline_start as deadline_start, CONCAT(' . $this->db->dbprefix('users') . '.first_name," ",' . $this->db->dbprefix('users') . '.last_name) as created_by, ' . $this->db->dbprefix('man_manufacturing') . '.description as description, ' . $this->db->dbprefix('man_manufacturing') . '.status as status')
			->from('man_manufacturing')
            ->join('man_bom', 'man_bom.id=man_manufacturing.bom_id', 'left')
			->join('users', 'users.id=man_manufacturing.created_by', 'left')
			->where('man_manufacturing.status','confirmed');


        $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }


	public function getDone()
    {
        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
        //    $warehouse_id = $user->warehouse_id;
        }




		$this->load->library('datatables');

		//if ($warehouse_id) {
            $this->datatables
            //->select($this->db->dbprefix('man_bom') . ".id as bomid, {$this->db->dbprefix('man_bom')}.id as bomid2, {$this->db->dbprefix('man_bom')}.image as image, DATE_FORMAT({$this->db->dbprefix('man_bom')}.created_date, '%Y-%m-%d %T') as date,  {$this->db->dbprefix('man_bom')}.code as code, {$this->db->dbprefix('man_bom')}.name as name, {$this->db->dbprefix('man_bom')}.quantity as quantity , {$this->db->dbprefix('man_bom')}.wh_raw as wh_raw, {$this->db->dbprefix('man_bom')}.wh_manufacturing as wh_manufacturing, {$this->db->dbprefix('man_bom')}.wh_complete as wh_complete, {$this->db->dbprefix('man_bom')}.wh_destruction as wh_destruction, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, {$this->db->dbprefix('man_bom')}.description as description", false)
            ->select($this->db->dbprefix('man_manufacturing') . '.id as id, DATE_FORMAT(' . $this->db->dbprefix('man_manufacturing') . '.date_done, "%Y-%m-%d %T") as created_date, ' . $this->db->dbprefix('man_bom') . '.name as name, ' . $this->db->dbprefix('man_manufacturing') . '.quantity as quantity, ' . $this->db->dbprefix('man_manufacturing') . '.confirm_qty as confirm_qty, ' . $this->db->dbprefix('man_manufacturing') . '.deadline_start as deadline_start, CONCAT(' . $this->db->dbprefix('users') . '.first_name," ",' . $this->db->dbprefix('users') . '.last_name) as created_by, ' . $this->db->dbprefix('man_manufacturing') . '.description as description, ' . $this->db->dbprefix('man_manufacturing') . '.status as status')
			->from('man_manufacturing')
            ->join('man_bom', 'man_bom.id=man_manufacturing.bom_id', 'left')
			->join('users', 'users.id=man_manufacturing.created_by', 'left')
			->where('man_manufacturing.status','done');


        echo $this->datatables->generate();
    }


	public function getProgress()
    {
        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
        //    $warehouse_id = $user->warehouse_id;
        }

		/*
		<a href="<?= admin_url('sales/email_payment/' . $payment->id) ?>" class="email_payment"  title="<?= lang('email_payment')?>"><i class="fa fa-envelope"></i></a>
                                            <a href="<?= admin_url('sales/edit_payment/' . $payment->id) ?>" title="<?= lang('edit_payment')?>"
                                               data-toggle="modal" data-target="#myModal2"><i
                                                    class="fa fa-edit"></i></a>
             */

		$action='<div class="text-center"><a href='. admin_url("manufacturing/progress_manufacturing/$1") .' ><span class="row_status1 btn" style="background-color:#777;color:#fff;">'.lang("manufacturing").'</span></a><a href='. admin_url("manufacturing/progress_details/$1") .' ><span class="row_status1 btn  btn-info">'.lang("details").'</span></a><a href='. admin_url("manufacturing/done_product/$1") .' ><span class="row_status1 btn  btn-success">'.lang("Mark_as_Done").'</span></a></div>';

		$this->load->library('datatables');

		//if ($warehouse_id) {
            $this->datatables
            //->select($this->db->dbprefix('man_bom') . ".id as bomid, {$this->db->dbprefix('man_bom')}.id as bomid2, {$this->db->dbprefix('man_bom')}.image as image, DATE_FORMAT({$this->db->dbprefix('man_bom')}.created_date, '%Y-%m-%d %T') as date,  {$this->db->dbprefix('man_bom')}.code as code, {$this->db->dbprefix('man_bom')}.name as name, {$this->db->dbprefix('man_bom')}.quantity as quantity , {$this->db->dbprefix('man_bom')}.wh_raw as wh_raw, {$this->db->dbprefix('man_bom')}.wh_manufacturing as wh_manufacturing, {$this->db->dbprefix('man_bom')}.wh_complete as wh_complete, {$this->db->dbprefix('man_bom')}.wh_destruction as wh_destruction, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, {$this->db->dbprefix('man_bom')}.description as description", false)
            ->select($this->db->dbprefix('man_manufacturing') . '.id as id, DATE_FORMAT(' . $this->db->dbprefix('man_manufacturing') . '.date_produce, "%Y-%m-%d %T") as created_date, ' . $this->db->dbprefix('man_bom') . '.name as name, ' . $this->db->dbprefix('man_manufacturing') . '.quantity as quantity, ' . $this->db->dbprefix('man_manufacturing') . '.deadline_start as deadline_start, ' . $this->db->dbprefix('man_manufacturing') . '.status as status, CONCAT(' . $this->db->dbprefix('users') . '.first_name," ",' . $this->db->dbprefix('users') . '.last_name) as created_by')
			->from('man_manufacturing')
            ->join('man_bom', 'man_bom.id=man_manufacturing.bom_id', 'left')
			->join('users', 'users.id=man_manufacturing.created_by', 'left')
			->where('man_manufacturing.status','produce');


        $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }



	public function getBom($warehouse_id = null)
    {
        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
        //    $warehouse_id = $user->warehouse_id;
        }



        $detail_link = anchor('admin/manufacturing/bom_details/$1', '<i class="fa fa-file-text-o"></i> ' . lang('Bill_Of_Material_details'));
        $edit_link = anchor('admin/manufacturing/bom_update/$1', '<i class="fa fa-edit"></i> ' . lang('Edit_Bill_Of_Material'));
        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('Delete_Bill_Of_Material') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . admin_url('manufacturing/deletebom/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('Delete_Bill_Of_Material') . '</a>';

		$action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $edit_link . '</li>
            <li>' . $detail_link . '</li>';
           // <li><a href="' . admin_url('manufacturing/edit_bom/$1') . '"  data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> ' . lang('Edit_Bill_Of_Material') . '</a></li>';
        $action .= '<li><a href="' . base_url() . 'assets/uploads/$2" data-type="image" data-toggle="lightbox"><i class="fa fa-file-photo-o"></i> '
            . lang('view_image') . '</a></li>
            <li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';

		$this->load->library('datatables');

		//if ($warehouse_id) {
            $this->datatables
            //->select($this->db->dbprefix('man_bom') . ".id as bomid, {$this->db->dbprefix('man_bom')}.id as bomid2, {$this->db->dbprefix('man_bom')}.image as image, DATE_FORMAT({$this->db->dbprefix('man_bom')}.created_date, '%Y-%m-%d %T') as date,  {$this->db->dbprefix('man_bom')}.code as code, {$this->db->dbprefix('man_bom')}.name as name, {$this->db->dbprefix('man_bom')}.quantity as quantity , {$this->db->dbprefix('man_bom')}.wh_raw as wh_raw, {$this->db->dbprefix('man_bom')}.wh_manufacturing as wh_manufacturing, {$this->db->dbprefix('man_bom')}.wh_complete as wh_complete, {$this->db->dbprefix('man_bom')}.wh_destruction as wh_destruction, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, {$this->db->dbprefix('man_bom')}.description as description", false)
            ->select($this->db->dbprefix('man_bom') . ".id as bomid, {$this->db->dbprefix('man_bom')}.id as bomid2, {$this->db->dbprefix('man_bom')}.image as image, DATE_FORMAT({$this->db->dbprefix('man_bom')}.created_date, '%Y-%m-%d %T') as date,  {$this->db->dbprefix('man_bom')}.code as code, {$this->db->dbprefix('man_bom')}.name as name, {$this->db->dbprefix('man_bom')}.quantity as quantity, {$this->db->dbprefix('man_bom')}.wh_raw_name as wh_raw_name, {$this->db->dbprefix('man_bom')}.wh_manufacturing_name as wh_manufacturing_name, {$this->db->dbprefix('man_bom')}.wh_complete_name as wh_complete_name, {$this->db->dbprefix('man_bom')}.wh_destruction_name as wh_destruction_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by", false)
            ->from('man_bom')
            //->join('warehouses', 'warehouses.id=adjustments_count.warehouse_id', 'left')
            ->join('users', 'users.id=man_bom.created_by', 'left');

        $this->datatables->add_column('Actions', $action, 'bomid, image');
        echo $this->datatables->generate();
    }


	public function addByAjaxFrom_Manufacturing()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->get('token', true) && $this->input->get('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product', true);
            $id = $this->input->get('id', true);



            /*if (!isset($product['code']) || empty($product['code'])) {
                exit(json_encode(['msg' => lang('product_code_is_required')]));
            }
            if (!isset($product['name']) || empty($product['name'])) {
                exit(json_encode(['msg' => lang('product_name_is_required')]));
            }
            if (!isset($product['category_id']) || empty($product['category_id'])) {
                exit(json_encode(['msg' => lang('product_category_is_required')]));
            }
            if (!isset($product['unit']) || empty($product['unit'])) {
                exit(json_encode(['msg' => lang('product_unit_is_required')]));
            }
            if (!isset($product['price']) || empty($product['price'])) {
                exit(json_encode(['msg' => lang('product_price_is_required')]));
            }
            if (!isset($product['cost']) || empty($product['cost'])) {
                exit(json_encode(['msg' => lang('product_cost_is_required')]));
            }
            if ($this->products_model->getProductByCode($product['code'])) {
                exit(json_encode(['msg' => lang('product_code_already_exist')]));
            }*/

			$product['image']=$this->manufacturing_model->getBomByID($id)->image;
            if ($row = $this->products_model->addAjaxProductFromSales($product)) {


				if($this->manufacturing_model->updateBom($id,['product_id' => $row->id])){
				}
				$row->price   = $row->price;
                $row->unit_price   = $row->price;
                $row->real_unit_price   = $row->price;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->unit             = $row->unit;
                $row->tax_rate             = $row->tax_rate;
                $row->tax_method             = $row->tax_method;
                $row->new_entry        = 1;
                $row->expiry           = '';
                $row->qty              = 1;
                $row->quantity_balance = '';
                $row->discount         = '0';
                $row->serial         = '';
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);


				$units    = $this->site->getUnitsByBUID($row->base_unit);
				$options              = $this->products_model->getProductOptions($row->id);

                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				$tax_rate = $this->site->getTaxRateByID($row->tax_rate);

                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				//['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                //    'row'     => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $pr       = ['row'     => $row, 'units' => $units, 'options' => $options,'item_id' => $row->id,'id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0'];
                $this->sma->send_json(['msg' => 'success', 'result' => $pr]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_add_product')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }


	public function updateByAjaxFrom_Manufacturing()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->get('token', true) && $this->input->get('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product', true);
            $id = $this->input->get('id', true);
            $product_id = $this->input->get('product_id', true);



            /*if (!isset($product['code']) || empty($product['code'])) {
                exit(json_encode(['msg' => lang('product_code_is_required')]));
            }
            if (!isset($product['name']) || empty($product['name'])) {
                exit(json_encode(['msg' => lang('product_name_is_required')]));
            }
            if (!isset($product['category_id']) || empty($product['category_id'])) {
                exit(json_encode(['msg' => lang('product_category_is_required')]));
            }
            if (!isset($product['unit']) || empty($product['unit'])) {
                exit(json_encode(['msg' => lang('product_unit_is_required')]));
            }
            if (!isset($product['price']) || empty($product['price'])) {
                exit(json_encode(['msg' => lang('product_price_is_required')]));
            }
            if (!isset($product['cost']) || empty($product['cost'])) {
                exit(json_encode(['msg' => lang('product_cost_is_required')]));
            }
            if ($this->products_model->getProductByCode($product['code'])) {
                exit(json_encode(['msg' => lang('product_code_already_exist')]));
            }*/
			$product['image']=$this->manufacturing_model->getBomByID($id)->image;

            if ($row = $this->products_model->updateAjaxProductFromSales($product_id,$product)) {

				$row->price   = $row->price;
                $row->unit_price   = $row->price;
                $row->real_unit_price   = $row->price;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->unit             = $row->unit;
                $row->tax_rate             = $row->tax_rate;
                $row->tax_method             = $row->tax_method;
                $row->new_entry        = 1;
                $row->expiry           = '';
                $row->qty              = 1;
                $row->quantity_balance = '';
                $row->discount         = '0';
                $row->serial         = '';
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);


				$units    = $this->site->getUnitsByBUID($row->base_unit);
				$options              = $this->products_model->getProductOptions($row->id);

                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				$tax_rate = $this->site->getTaxRateByID($row->tax_rate);

                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				//['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                //    'row'     => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $pr       = ['row'     => $row, 'units' => $units, 'options' => $options,'item_id' => $row->id,'id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0'];
                $this->sma->send_json(['msg' => 'success', 'result' => $pr]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_add_product')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }




	public function updateBomByAjax()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
		if ($this->input->post('token', true) && $this->input->post('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $bom = $this->input->post('bom', true);
            $id = $this->input->post('id', true);


			$updated_at  = date('Y-m-d H:i:s');

            $data     = [
                'updated_at'              => $updated_at,
                'code'              => $this->input->post('code', true),
                'name'              => $this->input->post('name', true),
                'quantity'              => $this->input->post('quantity', true),
                'description'              => $this->input->post('description', true),
                'wh_raw'              => $this->input->post('wh_raw', true),
                'wh_manufacturing'              => $this->input->post('wh_manufacturing', true),
                'wh_complete'              => $this->input->post('wh_complete', true),
                'wh_destruction'              => $this->input->post('wh_destruction', true),
                'wh_raw_name'              => $this->site->getWarehouseByID($this->input->post('wh_raw', true))->name,
                'wh_manufacturing_name'              => $this->site->getWarehouseByID($this->input->post('wh_manufacturing', true))->name,
                'wh_complete_name'              => $this->site->getWarehouseByID($this->input->post('wh_complete', true))->name,
                'wh_destruction_name'              => $this->site->getWarehouseByID($this->input->post('wh_destruction', true))->name,
                'updated_by'          => $this->session->userdata('user_id'),


            ];

			//$this->sma->send_json(['msg' => 'success','res'=>$data,'res1'=>$_POST]);


            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
				$product_id=$this->manufacturing_model->getBomByID($id)->product_id;
				if($product_id!=''){

					$this->db->update('products', ['image' => $photo],['id' => $product_id]);

				}

                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();
            }

			//$this->sma->print_arrays($data);


        if ($this->manufacturing_model->updateBom($id,$data)) {
                $this->sma->send_json(['msg' => 'success']);
            } else {
                exit(json_encode(['msg' => lang('failed_to_save')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }




	public function deletebom($id = null)
    {
        $this->sma->checkPermissions('deletebom', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		$bom_details = $this->manufacturing_model->getBomByID($id);

		if(!$bom_details->product_id){
			if ($this->manufacturing_model->deleteBom($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Bill_Of_Material_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Bill_Of_Material_deleted_successfully'));
            admin_redirect('manufacturing/bom');
        }

		}

		if($this->site->getAllPurchaseItemsItemCount($bom_details->product_id) || $this->site->getAllSalesItemsItemCount($bom_details->product_id) || $this->products_model->getAllWarehousesWithPQ($bom_details->product_id)){

		$this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_product_There_are_relevant_processes')]);

		}else{

        if ($this->manufacturing_model->deleteBom($id) && $this->products_model->deleteProduct($bom_details->product_id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Bill_Of_Material_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Bill_Of_Material_deleted_successfully'));
            admin_redirect('manufacturing/bom');
        }
		}
    }


    public function deletemanufacturing($id = null)
    {
        $this->sma->checkPermissions('deletemanufacturing', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		//$man_details = $this->manufacturing_model->getManufacturingByID($id);



        if ($this->manufacturing_model->deleteManufacturing($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Manufacturing_product_deleted_successfully')]);
            }
            $this->session->set_flashdata('message', lang('Manufacturing_product_deleted_successfully'));
            admin_redirect('manufacturing/bom');
        }

    }


	public function transfers()
    {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('transfers')]];
        $meta = ['page_title' => lang('transfers'), 'bc' => $bc];
        $this->page_construct('manufacturing/transfers', $meta, $this->data);

    }

	public function getTransfers()
    {
        //$this->sma->checkPermissions('index');


        $this->load->library('datatables');

        $this->datatables
            ->select('id, date, transfer_no, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, total, total_tax, grand_total, status, attachment')
            ->from('transfers')
            ->edit_column('fname', '$1 ($2)', 'fname, fcode')
            ->edit_column('tname', '$1 ($2)', 'tname, tcode');
			$this->datatables->where('man_id !=', '');

        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }

        //$this->datatables->add_column('Actions', $action, 'id')
            $this->datatables->unset_column('fcode')
            ->unset_column('tcode');
        echo $this->datatables->generate();
    }


	 public function destructions($warehouse_id = null)
    {
        $this->sma->checkPermissions('reports');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('destruction')]];
        $meta                = ['page_title' => lang('destruction'), 'bc' => $bc];
        $this->page_construct('manufacturing/destructions', $meta, $this->data);
    }



   public function getdestruction($warehouse_id = null)
    {
        //$this->sma->checkPermissions('destruction_inventory');


        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('destruction')}.id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('destruction')
            ->join('warehouses', 'warehouses.id=destruction.warehouse_id', 'left')
            ->join('users', 'users.id=destruction.created_by', 'left')
            ->group_by('destruction.id');
			$this->datatables->where('man_id !=', '');

        if ($warehouse_id) {
            $this->datatables->where('destruction.warehouse_id', $warehouse_id);
        }
        //$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('products/edit_destruction/$1') . "' class='tip' title='" . lang('edit_destruction') . "'><i class='fa fa-edit'></i></a> " . $delete_link . '</div>', 'id');

        echo $this->datatables->generate();
    }


    public function addBomRequestDetailLC(){

		$bom_id=$this->input->post('bom_id');
		$items=$this->input->post('bom_labors_arr');

		//$bom_details = $this->manufacturing_model->getBomByID($bom_id);
		//$date = date('Y-m-d H:i:s');


            $total=0;
				if($items){
		foreach ($items as $item) {

		$product = [
                        'bom_id'        => $bom_id,
                        'labor_desc'      => $item['labor_desc'],
                        'hours_no'      => $item['hours_no'],
                        'hours_wage'      => $item['hours_wage'],
                    ];


                    $products[] = $product;


				}

					$data=$this->manufacturing_model->addBomRequestDetailLC($bom_id,$products);
					echo json_encode($data);
				}else{
					echo json_encode(false);
				}


	}




    public function addBomRequestDetailIE(){

		$bom_id=$this->input->post('bom_id');
		$items=$this->input->post('bom_expensess_arr');

		//$bom_details = $this->manufacturing_model->getBomByID($bom_id);
		//$date = date('Y-m-d H:i:s');


            $total=0;
				if($items){
		foreach ($items as $item) {

		$product = [
                        'bom_id'        => $bom_id,
                        'expenses_acct'      => $item['expenses_acct'],
                        'cost'      => $item['cost'],
                    ];


                    $products[] = $product;


				}

					$data=$this->manufacturing_model->addBomRequestDetailIE($bom_id,$products);
					echo json_encode($data);
				}else{
					echo json_encode(false);
				}


	}








}