<?php

//ini_set('memory_limit', '-1');
//ini_set('max_execution_time', '-1');
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('memory_limit', '-1');

//ini_set('memory_limit','2048M');
class Products extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		//$db_name=str_replace("cpesoft_","",$this->db->database);
		//if($db_name=="marsaalshati"){
		//	ini_set('memory_limit','2048M');
		//}
        $this->lang->admin_load('products', $this->Settings->user_language);
        $this->lang->admin_load('transfers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('products_model');
		$this->load->admin_model('reports_model');
        $this->load->admin_model('settings_model');
		$this->load->admin_model('transfers_model');
		$this->load->admin_model('sales_model');
		$this->load->admin_model('db_model');
        $this->load->admin_model('companies_model');
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

    public function add($id = null,$isShop = '')
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $warehouses = $this->site->getAllWarehouses();
        }else{
            $warehouses[0] =  $this->site->getWarehouseByID($this->session->userdata('warehouse_id'));
        }

        $this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');
        if ($this->input->post('type') == 'standard') {
            //$this->form_validation->set_rules('cost', lang('product_cost'), 'required');
            $this->form_validation->set_rules('unit', lang('product_unit'), 'required');
        }
        $this->form_validation->set_rules('code', lang('product_code'), 'is_unique[products.code]');
        $this->form_validation->set_rules('item_no', lang('Item_no'), 'is_unique[products.item_no]');
        if (SHOP) {
            //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[products.slug]|alpha_dash');
        }
        $this->form_validation->set_rules('weight', lang('weight'), 'numeric');
        $this->form_validation->set_rules('minimum_selling_price', lang('Minimum_selling_price'), 'numeric|less_than_equal_to['.$this->sma->formatDecimal($this->input->post("price")).']');
        $this->form_validation->set_rules('product_image', lang('product_image'), 'xss_clean');
        $this->form_validation->set_rules('digital_file', lang('digital_file'), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang('product_gallery_images'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            $tax_rate = $this->input->post('tax_rate') ? $this->site->getTaxRateByID($this->input->post('tax_rate')) : null;
            $data     = [
                'code'              => $this->input->post('code'),
                'barcode_symbology' => $this->input->post('barcode_symbology') ? $this->input->post('barcode_symbology') : 'code128',
                'name'              => $this->input->post('name'),
                'type'              => $this->input->post('type'),
                'type2'              => $this->input->post('type2'),
                'brand'             => $this->input->post('brand'),
                'category_id'       => $this->input->post('category'),
                'subcategory_id'    => $this->input->post('subcategory') ? $this->input->post('subcategory') : null,
                'cost'              => $this->input->post('cost') ? $this->sma->formatDecimal($this->input->post('cost')) : null,
                'price'             => $this->sma->formatDecimal($this->input->post('price')),
                'unit'              => $this->input->post('unit'),
                'sale_unit'         => $this->input->post('default_sale_unit') ? $this->input->post('default_sale_unit') : $this->input->post('unit'),
                'purchase_unit'     => $this->input->post('default_purchase_unit') ? $this->input->post('default_purchase_unit') : $this->input->post('unit'),
                'tax_rate'          => $this->input->post('tax_rate'),
                'tax_method'        => $this->input->post('tax_method'),
                'alert_quantity'    => $this->input->post('alert_quantity'),
                'track_quantity'    => $this->input->post('track_quantity') ? $this->input->post('track_quantity') : '0',
                'details'           => $this->input->post('details'),
                'product_details'   => $this->input->post('product_details'),
                'supplier1'         => $this->input->post('supplier'),
                'supplier1price'    => $this->sma->formatDecimal($this->input->post('supplier_price')),
                'supplier2'         => $this->input->post('supplier_2'),
                'supplier2price'    => $this->sma->formatDecimal($this->input->post('supplier_2_price')),
                'supplier3'         => $this->input->post('supplier_3'),
                'supplier3price'    => $this->sma->formatDecimal($this->input->post('supplier_3_price')),
                'supplier4'         => $this->input->post('supplier_4'),
                'supplier4price'    => $this->sma->formatDecimal($this->input->post('supplier_4_price')),
                'supplier5'         => $this->input->post('supplier_5'),
                'supplier5price'    => $this->sma->formatDecimal($this->input->post('supplier_5_price')),
                'cf1'               => $this->input->post('cf1'),
                'cf2'               => $this->input->post('cf2'),
                'cf3'               => $this->input->post('cf3'),
                'cf4'               => $this->input->post('cf4'),
                'cf5'               => $this->input->post('cf5'),
                'cf6'               => $this->input->post('cf6'),
                'promotion'         => $this->input->post('promotion'),
                'promo_price'       => $this->sma->formatDecimal($this->input->post('promo_price')),
                'start_date'        => $this->input->post('start_date') ? $this->sma->fsd($this->input->post('start_date')) : null,
                'end_date'          => $this->input->post('end_date') ? $this->sma->fsd($this->input->post('end_date')) : null,
                'supplier1_part_no' => $this->input->post('supplier_part_no'),
                'supplier2_part_no' => $this->input->post('supplier_2_part_no'),
                'supplier3_part_no' => $this->input->post('supplier_3_part_no'),
                'supplier4_part_no' => $this->input->post('supplier_4_part_no'),
                'supplier5_part_no' => $this->input->post('supplier_5_part_no'),
                'file'              => $this->input->post('file_link'),
                'slug'              => $this->input->post('slug') ? $this->input->post('slug') : $this->sma->slug($this->input->post('name')),
                'weight'            => $this->input->post('weight'),
                'featured'          => $this->input->post('featured'),
                'hsn_code'          => $this->input->post('hsn_code'),
                'hide'              => $this->input->post('hide') ? $this->input->post('hide') : 0,
                'second_name'       => $this->input->post('second_name'),
                'product_number'       => $this->input->post('product_number'),
                'expiry'       => $this->input->post('expiry'),
                'minimum'    => $this->input->post('minimum') ? $this->input->post('minimum') : '0',
                'po_quantity'    => $this->input->post('po_quantity') ? $this->input->post('po_quantity') : '0',
                'item_no'    => $this->input->post('item_no') ? $this->input->post('item_no') : null,
                'minimum_selling_price'             => $this->input->post('minimum_selling_price') ? $this->sma->formatDecimal($this->input->post('minimum_selling_price')) : 0,

            ];

			if(isset($this->Settings->isStore) && $this->Settings->isStore==1)
              {
				  // $data['VisibleIn']          = $this->input->post('VisibleIn');

			  }

              if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
              {

                  $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                  $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                  $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

              }


            $warehouse_qty      = null;
            $product_attributes = null;
            $this->load->library('upload');
            if ($this->input->post('type') == 'standard') {
                $wh_total_quantity = 0;
                $pv_total_quantity = 0;
                for ($s = 2; $s > 5; $s++) {
                    $data['suppliers' . $s]           = $this->input->post('supplier_' . $s);
                    $data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                }
                foreach ($warehouses as $warehouse) {
                    if ($this->input->post('wh_qty_' . $warehouse->id)) {
                        $warehouse_qty[] = [
                            'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                            'quantity'     => $this->input->post('wh_qty_' . $warehouse->id),
                            'rack'         => $this->input->post('rack_' . $warehouse->id) ? $this->input->post('rack_' . $warehouse->id) : null,
                        ];
                        $wh_total_quantity += $this->input->post('wh_qty_' . $warehouse->id);
                    }
                }

                if ($this->input->post('attributes')) {
                    $a = sizeof($_POST['attr_name']);
                    for ($r = 0; $r <= $a; $r++) {
                        if (isset($_POST['attr_name'][$r])) {
                            $product_attributes[] = [
                                'name'         => $_POST['attr_name'][$r],
                                'warehouse_id' => $_POST['attr_warehouse'][$r],
                                'quantity'     => $_POST['attr_quantity'][$r],
                                'price'        => $_POST['attr_price'][$r],
                            ];
                            $pv_total_quantity += $_POST['attr_quantity'][$r];
                        }
                    }
                } else {
                    $product_attributes = null;
                }

                if ($wh_total_quantity != $pv_total_quantity && $pv_total_quantity != 0) {
                    $this->form_validation->set_rules('wh_pr_qty_issue', 'wh_pr_qty_issue', 'required');
                    $this->form_validation->set_message('required', lang('wh_pr_qty_issue'));
                }
            }

            if ($this->input->post('type') == 'service') {
                $data['track_quantity'] = 0;
            } elseif ($this->input->post('type') == 'combo') {
                $total_price = 0;
				//$this->sma->print_arrays($_POST);

                $c           = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                        $items[] = [
                            'item_code'  => $_POST['combo_item_code'][$r],
                            'quantity'   => $_POST['combo_item_quantity'][$r],
                            'unit_price' => $_POST['combo_item_price'][$r],
                        ];
                    }
                    $total_price += $_POST['combo_item_price'][$r] * $_POST['combo_item_quantity'][$r];
                }
                /*if ($this->sma->formatDecimal($total_price) != $this->sma->formatDecimal($this->input->post('price'))) {
                    $this->form_validation->set_rules('combo_price', 'combo_price', 'required');
                    $this->form_validation->set_message('required', lang('pprice_not_match_ciprice'));
                }*/
                $data['track_quantity'] = 0;
            } elseif ($this->input->post('type') == 'digital') {
                if ($_FILES['digital_file']['size'] > 0) {
                    $config['upload_path']   = $this->digital_upload_path;
                    $config['allowed_types'] = $this->digital_file_types;
                    $config['max_size']      = $this->allowed_file_size;
                    $config['overwrite']     = false;
                    $config['encrypt_name']  = true;
                    $config['max_filename']  = 25;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('digital_file')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        //admin_redirect('products/add');
                        $isShop = $isShop != '' ? '0/' .  $isShop : '';
                        admin_redirect('products/add/' . $isShop);
                    }
                    $file         = $this->upload->file_name;
                    $data['file'] = $file;
                } else {
                    if (!$this->input->post('file_link')) {
                        $this->form_validation->set_rules('digital_file', lang('digital_file'), 'required');
                    }
                }
                $config                 = null;
                $data['track_quantity'] = 0;
            }
            if (!isset($items)) {
                $items = null;
            }

            if ($_FILES['product_image']['size'] > 0) {
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                $config['encrypt_name']  = true;
				//$this->sma->print_arrays($config);
                $this->upload->initialize($config);
				if (!$this->upload->do_upload('product_image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products/add');
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
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image']     = $this->upload_path . $photo;
                    $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type']          = 'text';
                    $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                    $wm['quality']          = '100';
                    $wm['wm_font_size']     = '16';
                    $wm['wm_font_color']    = '999999';
                    $wm['wm_shadow_color']  = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding']       = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = null;
            }

            if ($_FILES['userfile']['name'][0] != '') {
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $files                   = $_FILES;
                $cpt                     = count($_FILES['userfile']['name']);
                for ($i = 0; $i < $cpt; $i++) {
                    $_FILES['userfile']['name']     = $files['userfile']['name'][$i];
                    $_FILES['userfile']['type']     = $files['userfile']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                    $_FILES['userfile']['error']    = $files['userfile']['error'][$i];
                    $_FILES['userfile']['size']     = $files['userfile']['size'][$i];

                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect('products/add');
                    } else {
                        $pho = $this->upload->file_name;

                        $photos[] = $pho;

                        $this->load->library('image_lib');
                        $config['image_library']  = 'gd2';
                        $config['source_image']   = $this->upload_path . $pho;
                        $config['new_image']      = $this->thumbs_path . $pho;
                        $config['maintain_ratio'] = true;
                        $config['width']          = $this->Settings->twidth;
                        $config['height']         = $this->Settings->theight;

                        $this->image_lib->initialize($config);

                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }

                        if ($this->Settings->watermark) {
                            $this->image_lib->clear();
                            $wm['source_image']     = $this->upload_path . $pho;
                            $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                            $wm['wm_type']          = 'text';
                            $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                            $wm['quality']          = '100';
                            $wm['wm_font_size']     = '16';
                            $wm['wm_font_color']    = '999999';
                            $wm['wm_shadow_color']  = 'CCCCCC';
                            $wm['wm_vrt_alignment'] = 'top';
                            $wm['wm_hor_alignment'] = 'left';
                            $wm['wm_padding']       = '10';
                            $this->image_lib->initialize($wm);
                            $this->image_lib->watermark();
                        }

                        $this->image_lib->clear();
                    }
                }
                $config = null;
            } else {
                $photos = null;
            }
            $data['quantity'] = isset($wh_total_quantity) ? $wh_total_quantity : 0;
			$warehouse_id=$this->input->post('wh_option');
			//if(str_replace("cpesoft_","",$this->db->database)=="test")
             //$this->sma->print_arrays($data, $items, $warehouse_qty, $product_attributes, $photos,$warehouse_id);
             //$this->sma->print_arrays($warehouse_qty);
             //$this->sma->print_arrays($product_attributes);
			 /*foreach ($warehouse_id as $warehouse) {
				 $this->sma->print_arrays($warehouse);
					if($warehouse->id=='all')
						$all=1;


				}*/
            $price_groups2=$this->input->post('price_groups2') ? $this->input->post('price_groups2') : null;
            if($price_groups2==1){
                $product_price_groups = null;
                if ($this->input->post('type') == 'standard') {
                    if($this->input->post('product_unit_id2') && $this->input->post('factor2') && $this->input->post('pricing_code2') && $this->input->post('selling_price2')){
                        $product_price_group = [
                            'product_unit_id'  => $_POST['product_unit_id2'],
                            'factor'   => $_POST['factor2'],
                            'pricing_code' => $_POST['pricing_code2'],
                            'selling_price' => $_POST['selling_price2'],
                            'price_group_id' => $_POST['price_group_id2'],
                        ];
                        $product_price_groups[] =$product_price_group;
                        for ($s = 2; $s <= $this->input->post('gp_count'); $s++) {
                            if($this->input->post('product_unit_id2_' . $s) && $this->input->post('factor2_' . $s) && $this->input->post('pricing_code2_' . $s) && $this->input->post('selling_price2_' . $s)){
                                $product_price_group = [
                                    'product_unit_id'  => $_POST['product_unit_id2_' . $s],
                                    'factor'   => $_POST['factor2_' . $s],
                                    'pricing_code' => $_POST['pricing_code2_' . $s],
                                    'selling_price' => $_POST['selling_price2_' . $s],
                                    'price_group_id' => $_POST['price_group_id2'],
                                ];
                                $product_price_groups[] =$product_price_group;
                            }
                        }
                    }
                }
            }else{
                $product_price_groups = null;

            }

                //$this->sma->print_arrays($data, $items, $warehouse_qty, $product_attributes, $photos,$warehouse_id,$product_price_groups);

        }


        if ($this->form_validation->run() == true && $this->products_model->addProduct($data, $items, $warehouse_qty, $product_attributes, $photos,$warehouse_id,$product_price_groups)) {
            $this->session->set_flashdata('message', lang('product_added'));
            admin_redirect('products');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }
            //$this->sma->print_arrays($warehouses);
            $this->data['price_groups'] = $this->site->getAllPriceGroups();

            $this->data['categories']          = $this->site->getAllCategories();
            //$this->data['tax_rates']           = $this->site->getAllTaxRates();
            $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->data['brands']              = $this->site->getAllBrands();
            $this->data['base_units']          = $this->site->getAllBaseUnits();

            $warehouses_products               = $id ? $this->products_model->getAllWarehousesWithPQ($id) : null;
            $product                           = $id ? $this->products_model->getProductByID($id) : null;
            $whselected = [];
            if ($id && !empty($warehouses_products)) {
                foreach ($warehouses_products as $wp_row) {
                    $whselected[] = (string)$wp_row->id;
                }
            } elseif (!empty($this->Settings->default_warehouse)) {
                $whselected[] = (string)$this->Settings->default_warehouse;
            } elseif (!empty($warehouses)) {
                $whselected[] = (string)$warehouses[0]->id;
            }
            $this->data['whselected']          = $whselected;
            $this->data['warehouses']          = $warehouses;
            $this->data['warehouses_products'] = $warehouses_products;
            $this->data['product']             = $product;
            $this->data['variants']            = $this->products_model->getAllVariants();
            $this->data['max_product_number']            = $this->products_model->getMaxProductNumber();
            $this->data['combo_items']         = ($id && $this->data['product']->type == 'combo') ? $this->products_model->getProductComboItems($id) : null;
            $this->data['product_options']     = $id ? $this->products_model->getProductOptionsWithWH($id) : null;
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_product')]];
            $meta                              = ['page_title' => lang('add_product'), 'bc' => $bc];
            $this->page_construct('products/add', $meta, $this->data, $isShop);
        }
    }

    public function add_adjustment($count_id = null)
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {



			$jsonArr_product_id=json_decode($_POST['jsonArr_product_id']);
			$jsonArr_product_id= (array) $jsonArr_product_id;

			$product_id_arr = [];
			foreach ($jsonArr_product_id as $product_arr) {
				$product_id_arr[]=  $product_arr->value;

			}


			$jsonArr_quantity=json_decode($_POST['jsonArr_quantity']);
			$jsonArr_quantity= (array) $jsonArr_quantity;

			$quantity_arr = [];
			foreach ($jsonArr_quantity as $qty_arr) {
				$quantity_arr[]=  $qty_arr->value;

			}

			$jsonArr_cost=json_decode($_POST['jsonArr_cost']);
			$jsonArr_cost= (array) $jsonArr_cost;

			$cost_arr = [];
			foreach ($jsonArr_cost as $c_arr) {
				$cost_arr[]=  $c_arr->value;

			}




			$jsonArr_expiry=json_decode($_POST['jsonArr_expiry']);
			$jsonArr_expiry= (array) $jsonArr_expiry;

			$expiry_arr = [];
			foreach ($jsonArr_expiry as $e_arr) {
				$expiry_arr[]=  $e_arr->value;

			}


            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
			if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }




            $note         = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($product_id_arr) ? sizeof($product_id_arr) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $product_id_arr[$r];
                //$type       = $_POST['type'][$r];
                //$quantity   = $quantity_arr[$r];
                //$cost   = $cost_arr[$r];
                //$value   = $_POST['value'][$r];
                $type       = "addition";//$_POST['type'][$r];
                $quantity   = $quantity_arr[$r];
                $cost   = $cost_arr[$r];
                $value   = $this->sma->formatDecimal($quantity_arr[$r] * $cost_arr[$r],4);//$_POST['value'][$r]

				//$unit   = $_POST['unit'][$r];
				$unit   = $this->products_model->getProductByID($product_id)->unit ? $this->products_model->getProductByID($product_id)->unit : 0;
                $serial     = '';//$_POST['serial'][$r];
                $item_expiry        = (isset($expiry_arr[$r]) && !empty($expiry_arr[$r])) ? $this->sma->fsd($expiry_arr[$r]) : null;
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;


				if (!$this->Settings->overselling && $type == 'subtraction' && !$count_id) {

					$product_details = $this->products_model->getProductByID($product_id);
                    if ($item_expiry) {
                        $today = date('Y-m-d');
                        if ($item_expiry <= $today) {
                            $this->session->set_flashdata('error', lang('product_expiry_date_issue') . ' (' . $product_details->name . ')');
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }




                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'cost'     => $cost,
                    'value'     => $value,
                    'unit'     => $unit,
                    'expiry'            => $item_expiry,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                    'grpId'    => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
                    'coId'    => $this->input->post('coId') ? $this->input->post('coId') : 1,
                    'brId'    => $this->input->post('brId') ? $this->input->post('brId') : 1,
                ];



				$tvalue=$tvalue+$value;
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }


            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => $this->input->post('count_id') ? $this->input->post('count_id') : null,
            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

             //$this->sma->print_arrays($data, $products,$tvalue);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment($data, $products,$tvalue)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjustments');
        } else {
            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items       = $this->products_model->getStockCountItems($count_id);
                foreach ($items as $item) {
                    $c = sha1(uniqid(mt_rand(), true));
                    if ($item->counted != $item->expected) {
                        $product     = $this->site->getProductByID($item->product_id);
                        $row         = json_decode('{}');
                        $row->id     = $item->product_id;
                        $row->code   = $product->code;
                        $row->name   = $product->name;
                        $row->qty    = $item->counted - $item->expected;
                        $row->type   = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty    = $row->qty > 0 ? $row->qty : (0 - $row->qty);
                        $options     = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $ri          = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'options' => $options, ];
                        $c++;
                    }
                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : false;
            $this->data['warehouse_id']     = $count_id ? $stock_count->warehouse_id : false;
            $this->data['count_id']         = $count_id;
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

             $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));


            }else{
    $this->data['warehouses'] = $this->site->getAllWarehouses();
            }


    //        $this->data['warehouses']       = $this->site->getAllWarehouses();
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment')]];
            $meta                           = ['page_title' => lang('add_adjustment'), 'bc' => $bc];
            $this->page_construct('products/add_adjustment', $meta, $this->data);
        }
    }

    public function add_adjustment_by_csv()
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
			if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }

            $note         = $this->sma->clear_tags($this->input->post('note'));
            $data         = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => null,
            ];

            if ($_FILES['csv_file']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv                = $this->upload->file_name;
                $data['attachment'] = $csv;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                // $this->sma->print_arrays($final);
                $rw = 2;
                foreach ($final as $pr) {
                    if ($product = $this->products_model->getProductByCode(trim($pr['code']))) {
                        $csv_variant = trim($pr['variant']);
                        $variant     = !empty($csv_variant) ? $this->products_model->getProductVariantID($product->id, $csv_variant) : false;

                        $csv_quantity = trim($pr['quantity']);
                        $type         = $csv_quantity > 0 ? 'addition' : 'subtraction';
                        $quantity     = $csv_quantity > 0 ? $csv_quantity : (0 - $csv_quantity);

						$cost = trim($pr['cost']);
                        $value = $cost*$quantity;
                        $unit = $product->unit;
                        $expiry = trim($pr['expiry']);


                        if (!$this->Settings->overselling && $type == 'subtraction') {
                            if ($variant) {
                                if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                                    if ($op_wh_qty->quantity < $quantity) {
                                        $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                        redirect($_SERVER['HTTP_REFERER']);
                                    }
                                } else {
                                    $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            }
                            if ($wh_qty = $this->products_model->getProductQuantity($product->id, $warehouse_id)) {
                                if ($wh_qty['quantity'] < $quantity) {
                                    $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            } else {
                                $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }

                        $products[] = [
                            'product_id'   => $product->id,
                            'type'         => $type,
                            'quantity'     => $quantity,
                            'cost'     => $cost,
							'value'     => $value,
							'unit'     => $unit,
							'expiry'     => $expiry,
							'warehouse_id' => $warehouse_id,
                            'option_id'    => $variant,
                        ];
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                    $rw++;
                }
            } else {
                $this->form_validation->set_rules('csv_file', lang('upload_file'), 'required');
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment($data, $products)) {
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjustments');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment')]];
            $meta                     = ['page_title' => lang('add_adjustment_by_csv'), 'bc' => $bc];
            $this->page_construct('products/add_adjustment_by_csv', $meta, $this->data);
        }
    }



    public function add_adjustment_by_excel()
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
			if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }

            $note         = $this->sma->clear_tags($this->input->post('note'));
            $data1         = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => null,
            ];
            //if ($_FILES['csv_file']['size'] > 0) {
              /*  $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv                = $this->upload->file_name;
				*/
				$this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                //$config['allowed_types'] = 'csv';
                $config['allowed_types'] = 'xlsx|xls';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;

                $data1['attachment'] = $csv;


                /*$arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }*/
					if (!$this->upload->do_upload('xlsx_file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}
			if(empty($error)){
			  if (!empty($data['upload_data']['file_name'])) {
				$import_xls_file = $data['upload_data']['file_name'];
			} else {
				$import_xls_file = 0;
			}
			$inputFileName = $this->digital_upload_path . $import_xls_file;


				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

				//$this->sma->print_arrays($allDataInSheet);

				$flag = true;
				$i=0;
				foreach ($allDataInSheet as $value) {
				  if($flag){
					$flag =false;
					continue;
				  }
				  //$inserdata[$i]['#'] = $value['A'];$keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];

				  $inserdata[$i]['code'] = trim($value['A']);
				  $inserdata[$i]['quantity'] = trim($value['B']);
				  $inserdata[$i]['cost'] = trim($value['C']);
				  $inserdata[$i]['expiry'] = trim($value['D']);
				  $inserdata[$i]['variant'] = trim($value['E']);
				  $i++;
				}
				$rw          = 2;
                $differences = 0;
                $matches     = 0;
				//$this->sma->print_arrays($inserdata);
                foreach ($inserdata as $pr) {



					 if ($product = $this->products_model->getProductByCode(trim($pr['code']))) {
                        $csv_variant = trim($pr['variant']);
                        $variant     = !empty($csv_variant) ? $this->products_model->getProductVariantID($product->id, $csv_variant) : false;

                        $csv_quantity = trim($pr['quantity']);
                        $type         = $csv_quantity > 0 ? 'addition' : 'subtraction';
                        $quantity     = $csv_quantity > 0 ? $csv_quantity : (0 - $csv_quantity);

						$cost = trim($pr['cost']);
                        $value = $cost*$quantity;
                        $unit = $product->unit;
                        $expiry = trim($pr['expiry']);


                        if (!$this->Settings->overselling && $type == 'subtraction') {
                            if ($variant) {
                                if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                                    if ($op_wh_qty->quantity < $quantity) {
                                        $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                        redirect($_SERVER['HTTP_REFERER']);
                                    }
                                } else {
                                    $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            }
                            if ($wh_qty = $this->products_model->getProductQuantity($product->id, $warehouse_id)) {
                                if ($wh_qty['quantity'] < $quantity) {
                                    $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            } else {
                                $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }

                        $products[] = [
                            'product_id'   => $product->id,
                            'type'         => $type,
                            'quantity'     => $quantity,
                            'cost'     => $cost,
							'value'     => $value,
							'unit'     => $unit,
							'expiry'     => $expiry,
							'warehouse_id' => $warehouse_id,
                            'option_id'    => $variant,
                        ];
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect($_SERVER['HTTP_REFERER']);
                    }



                    /*if ($product = $this->products_model->getProductByCode(trim($pr['product_code']))) {
                        $pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
                        $products_expected = $this->products_model->getStockCountProductsQuantity($stock_count->warehouse_id, $stock_count->type, $stock_count->categories, $stock_count->brands,trim($pr['product_code']));
						//$this->sma->print_arrays($products_expected);
			    foreach ($products_expected as $product_expected) {
                if ($variants = $this->products_model->getStockCountProductVariants($stock_count->warehouse_id, $product_expected->id)) {
                    foreach ($variants as $variant) {
                           $pr['expected']=$variant->quantity;
                                            }
                } else {
                        $pr['expected']=$product_expected->quantity;
                }

                }



						if ($pr['expected'] == $pr['counted']) {
                            $matches++;
                        } else {
                            $pr['stock_count_id']     = $id;
                            $pr['product_id']         = $product->id;
                            $pr['cost']               = $product->cost;
                            $pr['unit']               = $product->unit;
                            $pr['product_variant_id'] = "";//empty($pr['product_variant']) ? null : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
                            $products[]               = $pr;
                            $differences++;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('products/finalize_count_temp/' . $id);
                    }
					*/
                    $rw++;


                }
                /*$data1['final_file']  = $csv;
                $data1['differences'] = $differences;
                $data1['matches']     = $matches;
                $data1['missing']     = $stock_count->rows - ($rw - 2);
                $data1['finalized']   = 1;*/
            }
                // $this->sma->print_arrays($final);

            //} else {
            //    $this->form_validation->set_rules('csv_file', lang('upload_file'), 'required');
            //}

             //$this->sma->print_arrays($data1, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment($data1, $products)) {
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjustments');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment')]];
            $meta                     = ['page_title' => lang('add_adjustment_by_excel'), 'bc' => $bc];
            $this->page_construct('products/add_adjustment_by_excel', $meta, $this->data);
        }
    }

    public function addByAjax()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->get('token', true) && $this->input->get('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product', true);
            $product['slug']=$this->sma->slug($product['name']);
            $price_groups_check = $this->input->get('price_groups_check', true);

            if($price_groups_check==1){
                $price_groups = $this->input->get('price_groups', true);
                $unit_name=$this->products_model->getUnitById($price_groups['product_unit_id'])->name;
                $unit_base_name=$this->products_model->getUnitById($price_groups['unit'])->name;
                $unit_code=trim($unit_name)."1x".trim($price_groups['factor'])."".trim($unit_base_name);
                $ch=$this->site->checkUnitsByBUID($price_groups['unit'],$unit_code,$price_groups['factor']);
                if($ch){

                    $product_unit_id=$ch->id;

                }else{


                    $data = [
                        'name'            => $unit_code,
                        'code'            => $unit_code,
                        'base_unit'       => $price_groups['unit'],
                        'operator'        => '*',
                        'operation_value' => $price_groups['factor'],
                    ];
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {

                        $data  ['grpId']=1;
                        $data  ['coId']=1;
                        $data  ['brId']=$product['brId'] ? $product['brId'] : 1;

                    }
                    $product_unit_id=$this->site->addUnit($data);





                }





            }






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
            if ($row = $this->products_model->addAjaxProduct($product)) {
				$row->cost             = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                $row->real_unit_cost   = $row->cost;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->base_unit_cost   = $row->cost;
                $row->unit             = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->new_entry        = 1;
                $row->expiry           = '';
                $row->qty              = 1;
                $row->quantity_balance = '';
                $row->discount         = '0';
                unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);


				$units    = $this->site->getUnitsByBUID($row->base_unit);
				$options              = $this->products_model->getProductOptions($row->id);

                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				$tax_rate = '';//$this->site->getTaxRateByID($row->tax_rate);

                //$tax_rate = $this->site->getTaxRateByID($row->tax_rate);
				//['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                //    'row'     => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];

                if($price_groups_check==1){


                    if ($this->settings_model->setProductPriceForPriceGroup($row->id, $price_groups['price_group_id'], $price_groups['selling_price'], $product_unit_id, $price_groups['pricing_code'])) {
                        //$row->code=$price_groups['pricing_code'];
                        $row->cost=$price_groups['purchasing_price'];
                        $row->real_unit_cost=$price_groups['purchasing_price'];
                        $row->base_unit_cost=($price_groups['purchasing_price'] / $price_groups['factor']);
                        $row->unit             = $product_unit_id;

                    }



                }
                $pr       = ['row'     => $row, 'units' => $units, 'options' => $options,'item_id' => $row->id,'id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0'];
                $this->sma->send_json(['msg' => 'success', 'result' => $pr]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_add_product')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }

    public function addByAjaxFrom_Sales()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->get('token', true) && $this->input->get('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product', true);



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
            if ($row = $this->products_model->addAjaxProductFromSales($product)) {
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

   public function addAjaxProductFromSales_new()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->get('token', true) && $this->input->get('token', true) == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product', true);



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
            if ($row = $this->products_model->addAjaxProductFromSales_new($product)) {
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

    public function adjustment_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->products_model->deleteAdjustment($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('adjustment_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                      if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }


                        $this->excel->getActiveSheet()->setTitle(lang('quantity_adjustments'));
                        $row = 1;
                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);

                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('quantity_adjustments'));
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

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                        $row++;

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('warehouse'));
                    $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('created_by'));
                    $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('note'));
                    $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('items'));

                     $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($fillColor);
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         $this->excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($Font11Bold);
                        $row++;

                    foreach ($_POST['val'] as $id) {
                        $adjustment = $this->products_model->getAdjustmentByID($id);
                        $created_by = $this->site->getUser($adjustment->created_by);
                        $warehouse  = $this->site->getWarehouseByID($adjustment->warehouse_id);
                        $items      = $this->products_model->getAdjustmentItems($id);
                        $products   = '';
                        if ($items) {
                            foreach ($items as $item) {
                                $products .= $item->product_name . '(' . $this->sma->formatQuantity($item->type == 'subtraction' ? -$item->quantity : $item->quantity) . ')' . "\n";
                            }
                        }

                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($adjustment->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $adjustment->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $warehouse->name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $created_by->first_name . ' ' . $created_by->last_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->decode_html($adjustment->note));
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $products);
                        $row++;
                    }
                     for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                        }


                 //   $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                   // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                   // $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                   // $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                   // $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
                   // $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'quantity_adjustments_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function barcode($product_code = null, $bcs = 'code128', $height = 40)
    {
        /*if ($this->Settings->barcode_img) {
            header('Content-Type: image/png');
        } else*/ {
            //header('Content-type: image/svg+xml');
            header('Content-Type: image/png');
        }
        echo $this->sma->barcode($product_code, $bcs, $height, true, false, true);
    }

    public function count_stock($id=null)
    {
		    if(!$id){
			$this->session->set_flashdata('error', lang('no_product_found'));
                //redirect($_SERVER['HTTP_REFERER']);
            	admin_redirect('products/stock_counts');
			}else{
			$stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count->finalized) {
            $this->sma->md('admin/products/finalize_count_temp/' . $id);
        }

        $this->data['stock_count']       = $stock_count;
        $this->data['stock_count_items'] = $this->products_model->getStockCountItems($id);
        $this->data['warehouse']         = $this->site->getWarehouseByID($stock_count->warehouse_id);
        $this->data['adjustment']        = $this->products_model->getAdjustmentByCountID($id);
        //$this->load->view($this->theme . 'products/view_count_temp', $this->data);
		    $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('view_count')]];
            $meta                           = ['page_title' => lang('view_count'), 'bc' => $bc];
            $this->page_construct('products/view_count_temp', $meta, $this->data);

			}

		/*
        $this->sma->checkPermissions('stock_count');
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');
        $this->form_validation->set_rules('type', lang('type'), 'required');

        if ($this->form_validation->run() == true) {
            $warehouse_id = $this->input->post('warehouse');
            $type         = $this->input->post('type');
            $categories   = $this->input->post('category') ? $this->input->post('category') : null;
            $brands       = $this->input->post('brand') ? $this->input->post('brand') : null;
            $this->load->helper('string');
            $name     = random_string('md5') . '.csv';
            $products = $this->products_model->getStockCountProducts($warehouse_id, $type, $categories, $brands);
            $pr       = 0;
            $rw       = 0;
            foreach ($products as $product) {
                if ($variants = $this->products_model->getStockCountProductVariants($warehouse_id, $product->id)) {
                    foreach ($variants as $variant) {
                        $items[] = [
                            'product_code' => $product->code,
                            'product_name' => $product->name,
                            'variant'      => $variant->name,
                            'expected'     => $variant->quantity,
                            'counted'      => '',
                        ];
                        $rw++;
                    }
                } else {
                    $items[] = [
                        'product_code' => $product->code,
                        'product_name' => $product->name,
                        'variant'      => '',
                        'expected'     => $product->quantity,
                        'counted'      => '',
                    ];
                    $rw++;
                }
                $pr++;
            }
            if (!empty($items)) {
                $csv_file = fopen('./files/' . $name, 'w');
                fprintf($csv_file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($csv_file, [lang('product_code'), lang('product_name'), lang('variant'), lang('expected'), lang('counted')]);
                foreach ($items as $item) {
                    fputcsv($csv_file, $item);
                }
                // file_put_contents('./files/'.$name, $csv_file);
                // fwrite($csv_file, $txt);
                fclose($csv_file);
            } else {
                $this->session->set_flashdata('error', lang('no_product_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }
            $category_ids   = '';
            $brand_ids      = '';
            $category_names = '';
            $brand_names    = '';
            if ($categories) {
                $r = 1;
                $s = sizeof($categories);
                foreach ($categories as $category_id) {
                    $category = $this->site->getCategoryByID($category_id);
                    if ($r == $s) {
                        $category_names .= $category->name;
                        $category_ids   .= $category->id;
                    } else {
                        $category_names .= $category->name . ', ';
                        $category_ids   .= $category->id . ', ';
                    }
                    $r++;
                }
            }
            if ($brands) {
                $r = 1;
                $s = sizeof($brands);
                foreach ($brands as $brand_id) {
                    $brand = $this->site->getBrandByID($brand_id);
                    if ($r == $s) {
                        $brand_names .= $brand->name;
                        $brand_ids   .= $brand->id;
                    } else {
                        $brand_names .= $brand->name . ', ';
                        $brand_ids   .= $brand->id . ', ';
                    }
                    $r++;
                }
            }
            $data = [
                'date'           => $date,
                'warehouse_id'   => $warehouse_id,
                'reference_no'   => $this->input->post('reference_no'),
                'type'           => $type,
                'categories'     => $category_ids,
                'category_names' => $category_names,
                'brands'         => $brand_ids,
                'brand_names'    => $brand_names,
                'initial_file'   => $name,
                'products'       => $pr,
                'rows'           => $rw,
                'created_by'     => $this->session->userdata('user_id'),
            ];
        }

        if ($this->form_validation->run() == true && $this->products_model->addStockCount($data)) {
            $this->session->set_flashdata('message', lang('stock_count_intiated'));
            admin_redirect('products/stock_counts');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['brands']     = $this->site->getAllBrands();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('count_stock')]];
            $meta                     = ['page_title' => lang('count_stock'), 'bc' => $bc];
            $this->page_construct('products/count_stock', $meta, $this->data);
        }
		*/
    }

	public function manual_inventory($page = null)
    {

        /*error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        ini_set('max_execution_time', '300');*/
        ini_set('memory_limit','2048M');


        $this->sma->checkPermissions('manual_inventory',true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');
        $this->form_validation->set_rules('type', lang('type'), 'required');

        if ($this->form_validation->run() == true) {
            $warehouse_id = $this->input->post('warehouse');
            $type         = $this->input->post('type');
            $categories   = $this->input->post('category') ? $this->input->post('category') : null;
            $brands       = $this->input->post('brand') ? $this->input->post('brand') : null;
            $this->load->helper('string');
            $name     = random_string('md5') . '.csv';
            $products = $this->products_model->getStockCountProducts($warehouse_id, $type, $categories, $brands);
			//$this->sma->print_arrays($products);
            if (!empty($products)) {

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



                    /*$items[] = [
                        'product_code' => $product->code,
                        'product_name' => $product->name,
                        'quantity'      => '',
						'product_unit' => $product->unit,
                    ];*/
                    $rw++;


					$forSale=$this->products_model->getBalanceQuantityForSales($product->id,$warehouse_id);
                    $forPurchase=$this->products_model->getBalanceQuantityForPurchase($product->id,$warehouse_id);

					    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $rw.'');
                        $this->excel->getActiveSheet()->setCellValueExplicit('B' . $row, $product->code, PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $product->name);
                        //$this->excel->getActiveSheet()->SetCellValue('D' . $row, ($forPurchase-$forSale));
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, '0');
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
            }else{
                $this->session->set_flashdata('error', lang('There_are_no_products'));
            redirect($_SERVER['HTTP_REFERER']);
            }





				/*
            if (!empty($items)) {
                $csv_file = fopen('./files/' . $name, 'w');
                fprintf($csv_file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($csv_file, [lang('product_code'), lang('product_name'),lang('quantity'),lang('product_unit')]);
                foreach ($items as $item) {
                    fputcsv($csv_file, $item);
                }
                // file_put_contents('./files/'.$name, $csv_file);
                // fwrite($csv_file, $txt);
                fclose($csv_file);
            } else {
                $this->session->set_flashdata('error', lang('no_product_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }
             */



			 if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }
            $category_ids   = '';
            $brand_ids      = '';
            $category_names = '';
            $brand_names    = '';
            if ($categories) {
                $r = 1;
                $s = sizeof($categories);
                foreach ($categories as $category_id) {
                    $category = $this->site->getCategoryByID($category_id);
                    if ($r == $s) {
                        $category_names .= $category->name;
                        $category_ids   .= $category->id;
                    } else {
                        $category_names .= $category->name . ', ';
                        $category_ids   .= $category->id . ', ';
                    }
                    $r++;
                }
            }
            if ($brands) {
                $r = 1;
                $s = sizeof($brands);
                foreach ($brands as $brand_id) {
                    $brand = $this->site->getBrandByID($brand_id);
                    if ($r == $s) {
                        $brand_names .= $brand->name;
                        $brand_ids   .= $brand->id;
                    } else {
                        $brand_names .= $brand->name . ', ';
                        $brand_ids   .= $brand->id . ', ';
                    }
                    $r++;
                }
            }
            $data = [
                'date'           => $date,
                'warehouse_id'   => $warehouse_id,
                'reference_no'   => $this->input->post('reference_no'),
                'type'           => $type,
                'categories'     => $category_ids,
                'category_names' => $category_names,
                'brands'         => $brand_ids,
                'brand_names'    => $brand_names,
                'initial_file'   => $filenameV2,
                'products'       => $pr,
                'rows'           => $rw,
                'created_by'     => $this->session->userdata('user_id'),
            ];
         //
        }


        if ($this->form_validation->run() == true && $this->products_model->addStockCount($data)) {
            $this->session->set_flashdata('message', lang('stock_count_intiated'));

            $save    = './files/'.$filenameV2;
			$this->load->helper('excel');
            create_excelV2($this->excel, $filename,$save);




            admin_redirect('products/finalize_count_temp/'.$this->products_model->getStockCountsMaxId());
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['brands']     = $this->site->getAllBrands();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('count_stock')]];
            $meta                     = ['page_title' => lang('manual_inventory'), 'bc' => $bc];
            $this->page_construct('products/manual_inventory', $meta, $this->data);
        }
    }

    /* ------------------------------------------------------------------------------- */

    public function delete($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

		$product             = $this->site->getProductByID($id);
        if ($product->type=="manufacturing") {
        //    $this->session->set_flashdata('error', lang('Manufacturing_product'));
        //    redirect($_SERVER['HTTP_REFERER']);
        $this->sma->send_json(['error' => 1, 'msg' => lang('Manufacturing_product')]);
		}


        $totalQty=0;
        $warehouses=$this->site->getAllWarehouses();

			foreach($warehouses as $warehouses){
                $totalQty .= $this->sma->formatQuantity($this->getBalance($id,$warehouse)[0]->netTotal);
            }

            if($totalQty!=0 || $this->site->getAllPurchaseItemsItemCountV2($id) || $this->site->getAllSalesItemsItemCount($id)){

		$this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_product_There_are_relevant_processes')]);

		}else{

        if ($this->products_model->deleteProduct($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('product_deleted')]);
            }
            $this->session->set_flashdata('message', lang('product_deleted'));
            admin_redirect('welcome');
        }
		}
    }

    public function delete_adjustment($id = null)
    {
        $this->sma->checkPermissions('delete', true);

        if ($this->products_model->deleteAdjustment($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('adjustment_deleted')]);
        }
    }

    public function delete_image($id = null)
    {
        $this->sma->checkPermissions('edit', true);
        if ($id && $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            $this->db->delete('product_photos', ['id' => $id]);
            $this->sma->send_json(['error' => 0, 'msg' => lang('image_deleted')]);
        }
        $this->sma->send_json(['error' => 1, 'msg' => lang('ajax_error')]);
    }

    /* -------------------------------------------------------- */

    public function edit($id = null, $isShop  = '')
    {
        $this->sma->checkPermissions();
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $warehouses = $this->site->getAllWarehouses();
        }else{
            $warehouses[0] =  $this->site->getWarehouseByID($this->session->userdata('warehouse_id'));
        }

        $warehouses_products = $this->products_model->getAllWarehousesWithPQ($id);
        $product             = $this->site->getProductByID($id);
        if (!$id || !$product) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if ($product->type=="manufacturing") {
            $this->session->set_flashdata('error', lang('Manufacturing_product'));
            redirect($_SERVER['HTTP_REFERER']);
        }


        $this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');
        if ($this->input->post('type') == 'standard') {
            //$this->form_validation->set_rules('cost', lang('product_cost'), 'required');
            $this->form_validation->set_rules('unit', lang('product_unit'), 'required');
        }

        //$this->form_validation->set_rules('code', lang('product_code'), 'alpha_dash');
        if ($this->input->post('item_no') !== $product->item_no) {
            $this->form_validation->set_rules('item_no', lang('Item_no'), 'is_unique[products.item_no]');
        }
		if ($this->input->post('code') !== $product->code) {
            $this->form_validation->set_rules('code', lang('product_code'), 'is_unique[products.code]');
        }
		/*
        if (SHOP) {
            $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
            if ($this->input->post('slug') !== $product->slug) {
                $this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[products.slug]|alpha_dash');
            }
        }
		*/
        $this->form_validation->set_rules('weight', lang('weight'), 'numeric');
        $this->form_validation->set_rules('minimum_selling_price', lang('Minimum_selling_price'), 'numeric|less_than_equal_to['.$this->sma->formatDecimal($this->input->post("price")).']');
        $this->form_validation->set_rules('product_image', lang('product_image'), 'xss_clean');
        $this->form_validation->set_rules('digital_file', lang('digital_file'), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang('product_gallery_images'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $data = ['code'         => $this->input->post('code'),
                'barcode_symbology' => $this->input->post('barcode_symbology') ? $this->input->post('barcode_symbology') : 'code128',
                'name'              => $this->input->post('name'),
                'type'              => $this->input->post('type'),
                'type2'              => $this->input->post('type2'),
                'brand'             => $this->input->post('brand'),
                'category_id'       => $this->input->post('category'),
                'subcategory_id'    => $this->input->post('subcategory') ? $this->input->post('subcategory') : null,
                //'cost'              => $this->input->post('cost') ? $this->sma->formatDecimal($this->input->post('cost')) : null,
                'price'             => $this->sma->formatDecimal($this->input->post('price')),
                'unit'              => $this->input->post('unit'),
                'sale_unit'         => $this->input->post('default_sale_unit') ? $this->input->post('default_sale_unit') : $this->input->post('unit'),
                'purchase_unit'     => $this->input->post('default_purchase_unit') ? $this->input->post('default_purchase_unit') : $this->input->post('unit'),
                'tax_rate'          => $this->input->post('tax_rate'),
                'tax_method'        => $this->input->post('tax_method'),
                'alert_quantity'    => $this->input->post('alert_quantity'),
                'track_quantity'    => $this->input->post('track_quantity') ? $this->input->post('track_quantity') : '0',
                'details'           => $this->input->post('details'),
                'product_details'   => $this->input->post('product_details'),
                'supplier1'         => $this->input->post('supplier'),
                'supplier1price'    => $this->sma->formatDecimal($this->input->post('supplier_price')),
                'supplier2'         => $this->input->post('supplier_2'),
                'supplier2price'    => $this->sma->formatDecimal($this->input->post('supplier_2_price')),
                'supplier3'         => $this->input->post('supplier_3'),
                'supplier3price'    => $this->sma->formatDecimal($this->input->post('supplier_3_price')),
                'supplier4'         => $this->input->post('supplier_4'),
                'supplier4price'    => $this->sma->formatDecimal($this->input->post('supplier_4_price')),
                'supplier5'         => $this->input->post('supplier_5'),
                'supplier5price'    => $this->sma->formatDecimal($this->input->post('supplier_5_price')),
                'cf1'               => $this->input->post('cf1'),
                'cf2'               => $this->input->post('cf2'),
                'cf3'               => $this->input->post('cf3'),
                'cf4'               => $this->input->post('cf4'),
                'cf5'               => $this->input->post('cf5'),
                'cf6'               => $this->input->post('cf6'),
                'promotion'         => $this->input->post('promotion'),
                'promo_price'       => $this->sma->formatDecimal($this->input->post('promo_price')),
                'start_date'        => $this->input->post('start_date') ? $this->sma->fsd($this->input->post('start_date')) : null,
                'end_date'          => $this->input->post('end_date') ? $this->sma->fsd($this->input->post('end_date')) : null,
                'supplier1_part_no' => $this->input->post('supplier_part_no'),
                'supplier2_part_no' => $this->input->post('supplier_2_part_no'),
                'supplier3_part_no' => $this->input->post('supplier_3_part_no'),
                'supplier4_part_no' => $this->input->post('supplier_4_part_no'),
                'supplier5_part_no' => $this->input->post('supplier_5_part_no'),
                'slug'              => $this->input->post('slug') ? $this->input->post('slug') : $this->sma->slug($this->input->post('name')),
                'weight'            => $this->input->post('weight'),
                'featured'          => $this->input->post('featured'),
                'hsn_code'          => $this->input->post('hsn_code'),
                'hide'              => $this->input->post('hide') ? $this->input->post('hide') : 0,
                'hide_pos'          => $this->input->post('hide_pos') ? $this->input->post('hide_pos') : 0,
                'second_name'       => $this->input->post('second_name'),
				'product_number'       => $this->input->post('product_number'),
                'expiry'       => $this->input->post('expiry') ? $this->sma->fsd($this->input->post('expiry')) : null,
				'api'                => 0,
                'minimum'    => $this->input->post('minimum') ? $this->input->post('minimum') : '0',
                'po_quantity'    => $this->input->post('po_quantity') ? $this->input->post('po_quantity') : '0',
                'item_no'    => $this->input->post('item_no') ? $this->input->post('item_no') : null,
                'minimum_selling_price'             => $this->input->post('minimum_selling_price') ? $this->sma->formatDecimal($this->input->post('minimum_selling_price')) : 0,


            ];

			if(isset($this->Settings->isStore) && $this->Settings->isStore==1)
              {
				   //$data['VisibleIn']          = $this->input->post('VisibleIn');

			  }

            $warehouse_qty      = null;
            $product_attributes = null;
            $update_variants    = [];
            $this->load->library('upload');
            if ($this->input->post('type') == 'standard') {
                if ($product_variants = $this->products_model->getProductOptions($id)) {
                    foreach ($product_variants as $pv) {
                        $update_variants[] = [
                            'id'    => $this->input->post('variant_id_' . $pv->id),
                            'name'  => $this->input->post('variant_name_' . $pv->id),
                            'price' => $this->input->post('variant_price_' . $pv->id),
                        ];
                    }
                }
				//'cost'  => $this->input->post('variant_cost_' . $pv->id),

                for ($s = 2; $s > 5; $s++) {
                    $data['suppliers' . $s]           = $this->input->post('supplier_' . $s);
                    $data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                }
                foreach ($warehouses as $warehouse) {
                    $warehouse_qty[] = [
                        'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                        'rack'         => $this->input->post('rack_' . $warehouse->id) ? $this->input->post('rack_' . $warehouse->id) : null,
                    ];
                }

                if ($this->input->post('attributes')) {
                    $a = sizeof($_POST['attr_name']);
                    for ($r = 0; $r <= $a; $r++) {
                        if (isset($_POST['attr_name'][$r])) {
                            if ($product_variatnt = $this->products_model->getPrductVariantByPIDandName($id, trim($_POST['attr_name'][$r]))) {
                                $this->form_validation->set_message('required', lang('product_already_has_variant') . ' (' . $_POST['attr_name'][$r] . ')');
                                $this->form_validation->set_rules('new_product_variant', lang('new_product_variant'), 'required');
                            } else {
                                $product_attributes[] = [
                                    'name'         => $_POST['attr_name'][$r],
                                    'warehouse_id' => $_POST['attr_warehouse'][$r],
                                    'price'        => $_POST['attr_price'][$r],
                                ];
                            }
                        }
						//'quantity'     => $_POST['attr_quantity'][$r],

                    }
                } else {
                    $product_attributes = null;
                }
            }

            if ($this->input->post('type') == 'service') {
                $data['track_quantity'] = 0;
            } elseif ($this->input->post('type') == 'combo') {
                $total_price = 0;
                $c           = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                        $items[] = [
                            'item_code'  => $_POST['combo_item_code'][$r],
                            'quantity'   => $_POST['combo_item_quantity'][$r],
                            'unit_price' => $_POST['combo_item_price'][$r],
                        ];
                    }
                    $total_price += $_POST['combo_item_price'][$r] * $_POST['combo_item_quantity'][$r];
                }
                /*if ($this->sma->formatDecimal($total_price) != $this->sma->formatDecimal($this->input->post('price'))) {
                    $this->form_validation->set_rules('combo_price', 'combo_price', 'required');
                    $this->form_validation->set_message('required', lang('pprice_not_match_ciprice'));
                }*/
                $data['track_quantity'] = 0;
            } elseif ($this->input->post('type') == 'digital') {
                if ($this->input->post('file_link')) {
                    $data['file'] = $this->input->post('file_link');
                }
                if ($_FILES['digital_file']['size'] > 0) {
                    $config['upload_path']   = $this->digital_upload_path;
                    $config['allowed_types'] = $this->digital_file_types;
                    $config['max_size']      = $this->allowed_file_size;
                    $config['overwrite']     = false;
                    $config['encrypt_name']  = true;
                    $config['max_filename']  = 25;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('digital_file')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect('products/add');
                    }
                    $file         = $this->upload->file_name;
                    $data['file'] = $file;
                }
                $config                 = null;
                $data['track_quantity'] = 0;
            }
            if (!isset($items)) {
                $items = null;
            }

            if ($_FILES['product_image']['size'] > 0) {
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('product_image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products/edit/' . $id);
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
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image']     = $this->upload_path . $photo;
                    $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type']          = 'text';
                    $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                    $wm['quality']          = '100';
                    $wm['wm_font_size']     = '16';
                    $wm['wm_font_color']    = '999999';
                    $wm['wm_shadow_color']  = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding']       = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = null;
            }

            if ($_FILES['userfile']['name'][0] != '') {
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $files                   = $_FILES;
                $cpt                     = count($_FILES['userfile']['name']);
                for ($i = 0; $i < $cpt; $i++) {
                    $_FILES['userfile']['name']     = $files['userfile']['name'][$i];
                    $_FILES['userfile']['type']     = $files['userfile']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                    $_FILES['userfile']['error']    = $files['userfile']['error'][$i];
                    $_FILES['userfile']['size']     = $files['userfile']['size'][$i];

                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect('products/edit/' . $id);
                    } else {
                        $pho = $this->upload->file_name;

                        $photos[] = $pho;

                        $this->load->library('image_lib');
                        $config['image_library']  = 'gd2';
                        $config['source_image']   = $this->upload_path . $pho;
                        $config['new_image']      = $this->thumbs_path . $pho;
                        $config['maintain_ratio'] = true;
                        $config['width']          = $this->Settings->twidth;
                        $config['height']         = $this->Settings->theight;

                        $this->image_lib->initialize($config);

                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }

                        if ($this->Settings->watermark) {
                            $this->image_lib->clear();
                            $wm['source_image']     = $this->upload_path . $pho;
                            $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                            $wm['wm_type']          = 'text';
                            $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                            $wm['quality']          = '100';
                            $wm['wm_font_size']     = '16';
                            $wm['wm_font_color']    = '999999';
                            $wm['wm_shadow_color']  = 'CCCCCC';
                            $wm['wm_vrt_alignment'] = 'top';
                            $wm['wm_hor_alignment'] = 'left';
                            $wm['wm_padding']       = '10';
                            $this->image_lib->initialize($wm);
                            $this->image_lib->watermark();
                        }

                        $this->image_lib->clear();
                    }
                }
                $config = null;
            } else {
                $photos = null;
            }
            $data['quantity'] = isset($wh_total_quantity) ? $wh_total_quantity : 0;

			$warehouse_id=$this->input->post('wh_option');



            $price_groups2=$this->input->post('price_groups2') ? $this->input->post('price_groups2') : null;
            if($price_groups2==1){
                $product_price_groups = null;
                if ($this->input->post('type') == 'standard') {
                    if($this->input->post('product_unit_id2') && $this->input->post('factor2') && $this->input->post('pricing_code2') && $this->input->post('selling_price2')){
                        $product_price_group = [
                            'product_unit_id'  => $_POST['product_unit_id2'],
                            'factor'   => $_POST['factor2'],
                            'pricing_code' => $_POST['pricing_code2'],
                            'selling_price' => $_POST['selling_price2'],
                            'price_group_id' => $_POST['price_group_id2'],
                        ];
                        $product_price_groups[] =$product_price_group;
                        for ($s = 2; $s <= $this->input->post('gp_count'); $s++) {
                            if($this->input->post('product_unit_id2_' . $s) && $this->input->post('factor2_' . $s) && $this->input->post('pricing_code2_' . $s) && $this->input->post('selling_price2_' . $s)){
                                $product_price_group = [
                                    'product_unit_id'  => $_POST['product_unit_id2_' . $s],
                                    'factor'   => $_POST['factor2_' . $s],
                                    'pricing_code' => $_POST['pricing_code2_' . $s],
                                    'selling_price' => $_POST['selling_price2_' . $s],
                                    'price_group_id' => $_POST['price_group_id2'],
                                ];
                                $product_price_groups[] =$product_price_group;
                            }
                        }
                    }
                }
            }else{
                $product_price_groups = null;

            }

            //$this->sma->print_arrays($product_price_groups);

           // $this->sma->print_arrays($id, $data, $items, $warehouse_qty, $product_attributes, $photos, $update_variants,$warehouse_id);
        }

       if ($this->form_validation->run() == true && $this->products_model->updateProduct($id, $data, $items, $warehouse_qty, $product_attributes, $photos, $update_variants, $warehouse_id,$product_price_groups)) {

			$this->products_model->updateSynchProducts($this->site->getProductByID($id)->code);

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => lang('product_updated')]);
                exit;
            }else{
                $this->session->set_flashdata('message', lang('product_updated'));
            admin_redirect('products');
            }
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                 $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

                 /*$this->data['base_units']          = $this->site->getUnitBybrId($product->brId);
                 $this->data['subunits']            = $this->site->getUnitsByBUIDandbrId($product->unit,$product->brId);
                 $this->data['categories']          = $this->site->getCategoryBybrId($product->brId);
                 $this->data['brands']              = $this->site->getBrandBybrId($product->brId);


                }else{
                    $this->data['base_units']          = $this->site->getAllBaseUnits();
                    $this->data['subunits']            = $this->site->getUnitsByBUID($product->unit);
                    $this->data['categories']          = $this->site->getAllCategories();
                    $this->data['brands']              = $this->site->getAllBrands();

                */
                }
                    $this->data['base_units']          = $this->site->getAllBaseUnits();
                    $this->data['subunits']            = $this->site->getUnitsByBUID($product->unit);
                    $this->data['categories']          = $this->site->getAllCategories();
                    $this->data['brands']              = $this->site->getAllBrands();

             //$this->sma->print_arrays($this->site->getAllBaseUnits(),$this->site->getUnitBybrId($product->brId));

            $whselected = [];
            if (!empty($warehouses_products)) {
                foreach ($warehouses_products as $wp_row) {
                    $whselected[] = (string)$wp_row->id;
                }
            } elseif (!empty($this->Settings->default_warehouse)) {
                $whselected[] = (string)$this->Settings->default_warehouse;
            } elseif (!empty($warehouses)) {
                $whselected[] = (string)$warehouses[0]->id;
            }
            $this->data['whselected']          = $whselected;
            $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
            $this->data['price_groups'] = $this->site->getAllPriceGroups();
            $this->data['categories']          = $this->site->getAllCategories();
            $this->data['brands']              = $this->site->getAllBrands();
            $this->data['base_units']          = $this->site->getAllBaseUnits();
            $this->data['warehouses']          = $warehouses;
            $this->data['warehouses_products'] = $warehouses_products;
            $this->data['product']             = $product;
            $this->data['variants']            = $this->products_model->getAllVariants();

            $this->data['product_variants']    = $this->products_model->getProductOptions($id);
            $this->data['max_product_number']            = $this->products_model->getMaxProductNumber();
            $this->data['combo_items']         = $product->type == 'combo' ? $this->products_model->getProductComboItems2($product->id) : null;
            //$this->data['product_options']     = $id ? $this->products_model->getProductOptionsWithWH($id) : null;
            $this->data['product_options']     = $id ? $this->products_model->getProductOptionsWithWH_new($id) : null;
            $this->data['modal_js']   = $this->site->modal_js();
            $bc                                = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('edit_product')]];
            $meta                              = ['page_title' => lang('edit_product'), 'bc' => $bc];
            $this->page_construct('products/edit', $meta, $this->data,$isShop);
        }
    }


    public function edit_adjustment($id)
    {

		//$json = file_get_contents('php://input');
		    //$sales =json_decode($json);


			//$this->sma->print_arrays($sales);
		//$this->sma->print_arrays($this->input->post(NULL, TRUE));
        $this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{
        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {


			$jsonArr_product_id=json_decode($_POST['jsonArr_product_id']);
			$jsonArr_product_id= (array) $jsonArr_product_id;

			$product_id_arr = [];
			foreach ($jsonArr_product_id as $product_arr) {
				$product_id_arr[]=  $product_arr->value;

			}


			$jsonArr_quantity=json_decode($_POST['jsonArr_quantity']);
			$jsonArr_quantity= (array) $jsonArr_quantity;

			$quantity_arr = [];
			foreach ($jsonArr_quantity as $qty_arr) {
				$quantity_arr[]=  $qty_arr->value;

			}

			$jsonArr_cost=json_decode($_POST['jsonArr_cost']);
			$jsonArr_cost= (array) $jsonArr_cost;

			$cost_arr = [];
			foreach ($jsonArr_cost as $c_arr) {
				$cost_arr[]=  $c_arr->value;

			}




			$jsonArr_expiry=json_decode($_POST['jsonArr_expiry']);
			$jsonArr_expiry= (array) $jsonArr_expiry;

			$expiry_arr = [];
			foreach ($jsonArr_expiry as $e_arr) {
				$expiry_arr[]=  $e_arr->value;

			}


			//$this->sma->print_arrays($product_id_arr);
			//$this->sma->print_arrays($expiry_arr);


			//$this->sma->print_arrays(array_values($jsonArr_product_id));

			//$this->sma->print_arrays(serialize(array_values($product_id_arr)));
			//$jsonData=json_encode($product_id_arr);
			//$this->sma->print_arrays(rawurldecode($product_id_arr));
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = $adjustment->date;
            }

            $reference_no = $this->input->post('reference_no');
            $warehouse_id = $this->input->post('warehouse');
            $approved = $this->input->post('approved');
            $note         = $this->sma->clear_tags($this->input->post('note'));
			$approve_note         = $this->sma->clear_tags($this->input->post('approve_note'));


            $i = isset($product_id_arr) ? sizeof($product_id_arr) : 0;
			//$i = isset($jsonArr_product_id) ? sizeof($jsonArr_product_id) : 0;
			//$this->sma->print_arrays($i);

            for ($r = 0; $r < $i; $r++) {
                $product_id = $product_id_arr[$r];
                $type       = "addition";//$_POST['type'][$r];
                $quantity   = $quantity_arr[$r];
                $cost   = $cost_arr[$r];
                $value   = $this->sma->formatDecimal($quantity_arr[$r] * $cost_arr[$r],4);//$_POST['value'][$r]
                $unit   = $this->products_model->getProductByID($product_id)->unit ? $this->products_model->getProductByID($product_id)->unit : 0;

                $serial     = '';//$_POST['serial'][$r];
                $item_expiry        = (isset($expiry_arr[$r]) && !empty($expiry_arr[$r])) ? $this->sma->fsd($expiry_arr[$r]) : null;
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction') {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'cost'     => $cost,
                    'value'     => $value,
                    'expiry'            => $item_expiry,
                    'unit'     => $unit,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                    'grpId'    => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
                    'coId'    => $this->input->post('coId') ? $this->input->post('coId') : 1,
                    'brId'    => $this->input->post('brId') ? $this->input->post('brId') : 1,
                ];
                }else{
                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'cost'     => $cost,
                    'value'     => $value,
                    'expiry'            => $item_expiry,
                    'unit'     => $unit,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,

                ];
            }
				$tvalue=$tvalue+$value;
            }

			//$this->sma->print_arrays($i);

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
				'approve_note'         => $approve_note,
				'approved'         => $approved,
                'updated_by'   => $this->session->userdata('user_id'),
                'updated_at' => date('Y-m-d H:s:i'),
            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }



        //$this->sma->print_arrays($id, $data, $products,$tvalue,$acc);

        }


        if ($this->form_validation->run() == true && $this->products_model->updateAdjustment($id, $data, $products,$tvalue,$acc)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjustments');
        } else {
            $inv_items = $this->products_model->getAdjustmentItems($id);
            // krsort($inv_items);
            foreach ($inv_items as $item) {
                $c           = sha1(uniqid(mt_rand(), true));
                $product     = $this->site->getProductByID($item->product_id);
                $row         = json_decode('{}');
                $row->id     = $item->product_id;
                $row->code   = $product->code;
                $row->name   = $product->name;
                $row->qty    = $item->quantity;
                $row->cost    = $item->cost;
                $row->value    = $item->value;
                $row->unit     = $this->products_model->getUnitById($item->unit);
				if($item->expiry==null)
				$row->expiry    = "";
                else
                $row->expiry    = $item->expiry;

                $row->type   = $item->type;
                $options     = $this->products_model->getProductOptions($product->id);
                $row->option = $item->option_id ? $item->option_id : 0;
                $row->serial = $item->serial_no ? $item->serial_no : '';
                $ri          = $this->Settings->item_addition ? $product->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'options' => $options, ];
                $c++;
            }

            $this->data['adjustment']       = $adjustment;
            //$this->sma->print_arrays($adjustment);
            $this->data['adjustment_items'] = json_encode($pr);
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

             $this->data['warehouses'] = $adjustment->brId ? $this->site->getWarehousesBybrId($adjustment->brId) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));


            }else{
                $this->data['warehouses'] = $this->site->getAllWarehouses();
            }
            //$this->sma->print_arrays($this->data['warehouses'],$adjustment);
            //$this->data['warehouses']       = $this->site->getAllWarehouses();
			$this->data['approved_adjustments']       = $this->site->IsPermissions('products-approved_adjustments');

            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('edit_adjustment')]];
            $meta                           = ['page_title' => lang('edit_adjustment'), 'bc' => $bc];
            $this->page_construct('products/edit_adjustment', $meta, $this->data);
        }
		}
    }

	public function edit_destruction($id)
    {
        //$this->sma->print_arrays($id);
		$this->sma->checkPermissions('adjustments', true);
        $adjustment = $this->products_model->getDestructionByID($id);
		if ($adjustment->man_id) {
            $this->session->set_flashdata('error', lang('Destruction_warehouse_from_manufacture'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('destruction_not_found'));
            $this->sma->md();
        }
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = $adjustment->date;
            }

            $reference_no = $this->input->post('reference_no');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->sma->clear_tags($this->input->post('note'));

			//$this->sma->print_arrays($_POST);

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $_POST['product_id'][$r];
                $type       = $_POST['type'][$r];
                $quantity   = $_POST['quantity'][$r];
                $cost   = $_POST['product_cost'][$r];
                $value   = $_POST['product_val'][$r];
                $unit   = $_POST['product_unit'][$r];
                $serial     = $_POST['serial'][$r];
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction') {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => 'subtraction',
                    'quantity'     => $quantity,
                    'cost'     => $cost,
                    'value'     => $value,
                    'unit'     => $unit,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                ];
				$allcost = $allcost + $cost;
				$allvalue = $allvalue + $value;
				$allquantity = $allquantity + $quantity;

            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $adjustment->reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
				'cost' => $allcost,
                'value' => $allvalue,
                'quantity' => $allquantity,
                'op_type'     => $this->input->post('op_type') ? $this->input->post('op_type') : 1,
                'op_acct'     => $this->input->post('op_acct') ? $this->input->post('op_acct') : ($this->Settings->accounting ? $this->site->getAccountsSettings()->warehouses_destruction : null),

            ];




            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }




            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

             //$this->sma->print_arrays($id, $data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->updateDestruction_inventory($id, $data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/destruction');
        } else {
            $inv_items = $this->products_model->getDestructionItems($id);
            // krsort($inv_items);
            foreach ($inv_items as $item) {
                $c           = sha1(uniqid(mt_rand(), true));
                $product     = $this->site->getProductByID($item->product_id);
                $row         = json_decode('{}');
                $row->id     = $item->product_id;
                $row->code   = $product->code;
                $row->name   = $product->name;
                $row->qty    = $item->quantity;
                $row->cost    = $item->cost;
                $row->value    = $item->value;
                $row->unit     = $this->products_model->getUnitById($item->unit);

                $row->type   = $item->type;
                $options     = $this->products_model->getProductOptions($product->id);
                $row->option = $item->option_id ? $item->option_id : 0;
                $row->serial = $item->serial_no ? $item->serial_no : '';
                $ri          = $this->Settings->item_addition ? $product->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'options' => $options, ];
                $c++;
            }

            $this->data['count_id']       = $id;
            $this->data['adjustment']       = $adjustment;
            $this->data['adjustment_items'] = json_encode($pr);
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses']       = $this->site->getAllWarehouses();
            if ($this->Settings->accounting){
                $this->data['accounts_settings']   = $this->accounts_model->getSettings();
                $this->data['accounts'] = $this->accounts_model->getAllDetailsAccounts();
            }
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Edit_inventory_operations')]];
            $meta                           = ['page_title' => lang('Edit_inventory_operations'), 'bc' => $bc];
            $this->page_construct('products/edit_destruction_inventory', $meta, $this->data);
        }
    }

    public function finalize_count($id)
    {
        $this->sma->checkPermissions('stock_count');
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count || $stock_count->finalized) {
            $this->session->set_flashdata('error', lang('stock_count_finalized'));
            admin_redirect('products/stock_counts');
        }

        $this->form_validation->set_rules('count_id', lang('count_stock'), 'required');

        if ($this->form_validation->run() == true) {
            if ($_FILES['csv_file']['size'] > 0) {
                $note = $this->sma->clear_tags($this->input->post('note'));
                $data = [
                    'updated_by' => $this->session->userdata('user_id'),
                    'updated_at' => date('Y-m-d H:s:i'),
                    'note'       => $note,
                ];

                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['product_code', 'product_name', 'product_variant', 'expected', 'counted'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                // $this->sma->print_arrays($final);
                $rw          = 2;
                $differences = 0;
                $matches     = 0;
                foreach ($final as $pr) {
                    if ($product = $this->products_model->getProductByCode(trim($pr['product_code']))) {
                        $pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
                        if ($pr['expected'] == $pr['counted']) {
                            $matches++;
                        } else {
                            $pr['stock_count_id']     = $id;
                            $pr['product_id']         = $product->id;
                            $pr['cost']               = $product->cost;
                            $pr['product_variant_id'] = empty($pr['product_variant']) ? null : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
                            $products[]               = $pr;
                            $differences++;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('products/finalize_count/' . $id);
                    }
                    $rw++;
                }

                $data['final_file']  = $csv;
                $data['differences'] = $differences;
                $data['matches']     = $matches;
                $data['missing']     = $stock_count->rows - ($rw - 2);
                $data['finalized']   = 1;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->finalizeStockCount($id, $data, $products)) {
            $this->session->set_flashdata('message', lang('stock_count_finalized'));
            admin_redirect('products/stock_counts');
        } else {
            $this->data['error']       = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stock_count'] = $stock_count;
            $this->data['warehouse']   = $this->site->getWarehouseByID($stock_count->warehouse_id);
            $bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => admin_url('products/stock_counts'), 'page' => lang('stock_counts')], ['link' => '#', 'page' => lang('finalize_count')]];
            $meta                      = ['page_title' => lang('finalize_count'), 'bc' => $bc];
            $this->page_construct('products/finalize_count', $meta, $this->data);
        }
    }
    public function finalize_count_temp($id)
    {
        //ini_set('memory_limit','2048M');
        //ini_set('max_execution_time', 1200);
        $this->sma->checkPermissions('stock_count');
        $stock_count = $this->products_model->getStouckCountByID($id);
		if (!$stock_count || $stock_count->finalized) {
            $this->session->set_flashdata('error', lang('stock_count_finalized'));
            admin_redirect('products/stock_counts');
        }

        $this->form_validation->set_rules('count_id', lang('count_stock'), 'required');

        if ($this->form_validation->run() == true) {

                $note = $this->sma->clear_tags($this->input->post('note'));
                $data = [
                    'updated_by' => $this->session->userdata('user_id'),
                    'updated_at' => date('Y-m-d H:s:i'),
                    'note'       => $note,
                ];

                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                //$config['allowed_types'] = 'csv';
                $config['allowed_types'] = 'xlsx|xls';
                $config['max_size']      = '1073741824';//$this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;


				if (!$this->upload->do_upload('xlsx_file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}
			if(empty($error)){
			  if (!empty($data['upload_data']['file_name'])) {
				$import_xls_file = $data['upload_data']['file_name'];
			} else {
				$import_xls_file = 0;
			}
			$inputFileName = $this->digital_upload_path . $import_xls_file;


				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);


				$flag = true;
				$i=0;
				foreach ($allDataInSheet as $value) {
				  if($flag){
					$flag =false;
					continue;
				  }
				  //$inserdata[$i]['#'] = $value['A'];
				  $inserdata[$i]['product_code'] = trim($value['B']);
				  $inserdata[$i]['product_name'] = trim($value['C']);
				  $inserdata[$i]['counted'] = trim($value['D']);
				  $inserdata[$i]['unit'] = $this->products_model->getUnitByCode(trim($value['E']))->id;

				  $i++;
				}




                /*
				$arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['product_code', 'product_name', 'counted', 'unit'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				*/

                $rw          = 2;
                $differences = 0;
                $matches     = 0;

                foreach ($inserdata as $pr) {
                    if ($product = $this->products_model->getProductByCodeAndUint(trim($pr['product_code']),trim($pr['unit']))) {
						// $this->sma->print_arrays($product);
                        $pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
						$categories=$stock_count->categories=="" ? "" :  explode(",",$stock_count->categories);
						$brands=$stock_count->brands=="" ? "" :  explode(",",$stock_count->brands);
						//$this->sma->print_arrays($categories,$brands);


                        $products_expected = $this->products_model->getStockCountProductsQuantity($stock_count->warehouse_id, $stock_count->type, $categories,$brands,trim($pr['product_code']));



                        foreach ($products_expected as $product_expected) {
                            //$this->sma->print_arrays($product_expected);
                if ($variants = $this->products_model->getStockCountProductVariants($stock_count->warehouse_id, $product_expected->id)) {
                    foreach ($variants as $variant) {
                           $pr['expected']=$variant->quantity;
                                            }
                } else {
                        $pr['expected']=$product_expected->quantity;
                }

                }



						if ($pr['expected'] == $pr['counted']) {
                            $matches++;
                        } else {
                            $pr['stock_count_id']     = $id;
                            $pr['product_id']         = $product->id;
                            $pr['cost']               = $product->cost!='' ? $product->cost : '0';
                            $pr['unit']               = $product->unit;
                            $pr['product_variant_id'] = "";//empty($pr['product_variant']) ? null : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
                            $products[]               = $pr;
                            $differences++;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('products/finalize_count_temp/' . $id);
                    }
                    $rw++;


                }
                $data1['final_file']  = $csv;
                $data1['differences'] = $differences;
                $data1['matches']     = $matches;
                $data1['missing']     = $stock_count->rows - ($rw - 2);
                $data1['finalized']   = 1;
            }

             //$this->sma->print_arrays($data1, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->finalizeStockCount($id, $data1, $products)) {
            $this->session->set_flashdata('message', lang('stock_count_finalized'));
            admin_redirect('products/view_count_temp/'.$id);
        } else {
            $this->data['error']       = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stock_count'] = $stock_count;
            $this->data['warehouse']   = $this->site->getWarehouseByID($stock_count->warehouse_id);
            $bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => admin_url('products/stock_counts'), 'page' => lang('stock_counts')], ['link' => '#', 'page' => lang('finalize_count')]];
            $meta                      = ['page_title' => lang('finalize_count'), 'bc' => $bc];
            $this->page_construct('products/finalize_count_temp', $meta, $this->data);
        }
    }



    public function get_suggestions()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductsForPrinting($term);
        if ($rows) {
            foreach ($rows as $row) {
                $variants = $this->products_model->getProductOptions($row->id);
                $pr[]     = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1, 'variants' => $variants, 'unit' => $row->unit];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    public function getadjustments($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_adjustment') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_adjustment/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('adjustments')}.id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, approve_note, IF({$this->db->dbprefix('adjustments')}.approved=1,'".lang('approved')."', '".lang('unapproved')."') as approved, attachment")
            ->from('adjustments')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
            ->join('users', 'users.id=adjustments.created_by', 'left')
            ->group_by('adjustments.id');
        if ($warehouse_id) {
            $this->datatables->where('adjustments.warehouse_id', $warehouse_id);
        }
        //$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('products/edit_adjustment/$1') . "' class='tip' title='" . lang('edit_adjustment') . "'><i class='fa fa-edit'></i></a> " . $delete_link . '</div>', 'id');
		$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('products/edit_adjustment/$1') . "' class='tip' title='" . lang('edit_adjustment') . "'><i class='fa fa-edit'></i></a> </div>", 'id');


        echo $this->datatables->generate();
    }

 public function getadjustments_store($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_adjustment') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_adjustment/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

		$delete_link = anchor('admin/products/view_count/$1', '<label class="label label-primary pointer">' . lang('details') . '</label>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');


        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('adjustments_store')}.count_id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('adjustments_store')
            ->join('warehouses', 'warehouses.id=adjustments_store.warehouse_id', 'left')
            ->join('users', 'users.id=adjustments_store.created_by', 'left')
            ->group_by('adjustments_store.id');
        if ($warehouse_id) {
            $this->datatables->where('adjustments_store.warehouse_id', $warehouse_id);
        }
        $this->datatables->add_column('Actions', "<div class='text-center'> " . $delete_link . '</div>', 'id');

        echo $this->datatables->generate();
    }

	public function getdestruction($warehouse_id = null)
    {
        $this->sma->checkPermissions('destruction_inventory');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_adjustment') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_adjustment/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

			$delete_link = anchor('admin/products/view_destruction/$1', '<label class="label label-primary pointer">' . lang('details') . '</label>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');


        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('destruction')}.id as id, date, reference_no, warehouses.name as wh_name, op_type, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('destruction')
            ->join('warehouses', 'warehouses.id=destruction.warehouse_id', 'left')
            ->join('users', 'users.id=destruction.created_by', 'left')
            ->group_by('destruction.id');
        if ($warehouse_id) {
            $this->datatables->where('destruction.warehouse_id', $warehouse_id);
        }
        $this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('products/edit_destruction/$1') . "' class='tip' title='" . lang('Edit_inventory_operations') . "'><i class='fa fa-edit'></i></a> " . $delete_link . '</div>', 'id');

        echo $this->datatables->generate();
    }

    public function getCounts($warehouse_id = null)
    {
        $this->sma->checkPermissions('stock_count', true);

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;

        }

        $detail_link = anchor('admin/products/view_count/$1', '<span class="label label-primary pointer">' . lang('details') . '</span>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('stock_counts')}.id as id, date, reference_no, {$this->db->dbprefix('warehouses')}.name as wh_name, type, brand_names, category_names, (CASE WHEN initial_file='from_offline' THEN CONCAT('from_offline','_',{$this->db->dbprefix('stock_counts')}.id) ELSE initial_file END) as initial_file, (CASE WHEN final_file='from_offline' THEN CONCAT('from_offline','_',{$this->db->dbprefix('stock_counts')}.id) ELSE final_file END) as final_file")
            ->from('stock_counts')
            ->join('warehouses', 'warehouses.id=stock_counts.warehouse_id', 'left');
        if ($warehouse_id) {
            $this->datatables->where('warehouse_id', $warehouse_id);
        }

        $this->datatables->add_column('Actions', '<div class="text-center">' . $detail_link . '</div>', 'id');
        echo $this->datatables->generate();
    }
    public function getCounts_temp($warehouse_id = null)
    {
        $this->sma->checkPermissions('stock_count', true);

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }


        $detail_link = anchor('admin/products/view_count_temp/$1', '<label class="label label-primary pointer">' . lang('details') . '</label>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');
        //$detail_link = admin_redirect('products/view_count_temp/$1');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('stock_counts')}.id as id, date, reference_no, {$this->db->dbprefix('warehouses')}.name as wh_name, type, brand_names, category_names, initial_file, final_file")
            ->from('stock_counts')
            ->join('warehouses', 'warehouses.id=stock_counts.warehouse_id', 'left');
        if ($warehouse_id) {
            $this->datatables->where('warehouse_id', $warehouse_id);
        }

        $this->datatables->add_column('Actions', '<div class="text-center">' . $detail_link . '</div>', 'id');

        echo $this->datatables->generate();
    }

    public function getProducts($warehouse_id = null)
    {
        $this->sma->checkPermissions('index', true);
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : null;

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
        //    $warehouse_id = $user->warehouse_id;
        }

		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
        $coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
        $brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
        $product     = $this->input->get('product') ? $this->input->get('product') : null;
        $category    = $this->input->get('category') ? $this->input->get('category') : null;
        $brand       = $this->input->get('brand') ? $this->input->get('brand') : null;
        $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : null;
        $warehouse_id   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $product_type   = $this->input->get('product_type') ? $this->input->get('product_type') : null;



        $detail_link = anchor('admin/products/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('product_details'));
        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_product') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . admin_url('products/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_product') . '</a>';
        $single_barcode = anchor('admin/products/print_barcodes/$1', '<i class="fa fa-print"></i> ' . lang('print_barcode_label'));
        // $single_label = anchor_popup('products/single_label/$1/' . ($warehouse_id ? $warehouse_id : ''), '<i class="fa fa-print"></i> ' . lang('print_label'), $this->popup_attributes);
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li><a href="' . admin_url('products/add/$1') . '"><i class="fa fa-plus-square"></i> ' . lang('duplicate_product') . '</a></li>
            <li><a href="' . admin_url('products/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_product') . '</a></li>';
        if ($warehouse_id) {
            $action .= '<li><a href="' . admin_url('products/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                . lang('set_rack') . '</a></li>';
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
            $action .= '<li><a href="' . admin_url('products/products_details/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-home"></i> '
                . lang('Branches_list') . '</a></li>';
        }
        $action .= '<li><a href="' . base_url() . 'assets/uploads/$2" data-type="image" data-toggle="lightbox"><i class="fa fa-file-photo-o"></i> '
            . lang('view_image') . '</a></li>
            <li>' . $single_barcode . '</li>
            <li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';
        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
            ->select($this->db->dbprefix('products') . ".id as productid,{$this->db->dbprefix('products')}.id as product_number, {$this->db->dbprefix('products')}.image as image,  {$this->db->dbprefix('products')}.code as code,  {$this->db->dbprefix('products')}.item_no as item_no, CONCAT({$this->db->dbprefix('products')}.name, ' ', (CASE WHEN {$this->db->dbprefix('products')}.second_name IS NOT NULL THEN {$this->db->dbprefix('products')}.second_name ELSE '' END)) as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, wp.avg_cost as cost, price as price, COALESCE(wp.quantity, 0) as quantity, {$this->db->dbprefix('units')}.code as unit, wp.rack as rack, alert_quantity", false)
            ->from('products');
            /*if ($this->Settings->display_all_products) {
                $this->datatables->join("( SELECT product_id, quantity, rack from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left');
            } else */{
                $this->datatables->join('warehouses_products wp', 'products.id=wp.product_id', 'left')
                ->where('wp.warehouse_id', $warehouse_id);
                //->where('wp.quantity !=', 0);
            }
            $this->datatables->join('categories', 'products.category_id=categories.id', 'left')
            ->join('units', 'products.unit=units.id', 'left')
            ->join('brands', 'products.brand=brands.id', 'left')
         ->group_by("products.id");
        } else {

			$wpo = '( SELECT distinct wpo.product_id,   SUM(wpo.quantity) as quantity   from ' . $this->db->dbprefix('warehouses_products') . ' wpo';
			$wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';


            $this->datatables
                ->select($this->db->dbprefix('products') . ".id as productid,{$this->db->dbprefix('products')}.id as product_number, {$this->db->dbprefix('products')}.image as image,  {$this->db->dbprefix('products')}.code as code,  {$this->db->dbprefix('products')}.item_no as item_no, CONCAT({$this->db->dbprefix('products')}.name, ' ', (CASE WHEN {$this->db->dbprefix('products')}.second_name IS NOT NULL THEN {$this->db->dbprefix('products')}.second_name ELSE '' END)) as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, cost as cost, price as price, COALESCE( (WarehousesProductsOrignal.quantity), 0 ) as quantity, {$this->db->dbprefix('units')}.name as unit, '' as rack, alert_quantity", false)
                ->from('products')
                ->join('categories', 'products.category_id=categories.id', 'left')
                ->join('units', 'products.unit=units.id', 'left')
                ->join('brands', 'products.brand=brands.id', 'left')
                ->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left')
				->group_by('products.id');
        }
        if (!$this->Owner && !$this->Admin) {
            if (!$this->session->userdata('show_cost')) {
                $this->datatables->unset_column('cost');
            }
            if (!$this->session->userdata('show_price')) {
                $this->datatables->unset_column('price');
            }
        }
        if ($supplier) {
            $this->datatables->where('supplier1', $supplier)
            ->or_where('supplier2', $supplier)
            ->or_where('supplier3', $supplier)
            ->or_where('supplier4', $supplier)
            ->or_where('supplier5', $supplier);
        }
        if (!$warehouse_id && ($grpId || $coId || $brId)) {
           // $this->datatables->join("( SELECT product_id, quantity, rack from {$this->db->dbprefix('warehouses_products')} ) wp", 'products.id=wp.product_id', 'left');
           $this->datatables->join('warehouses_products wp', 'products.id=wp.product_id', 'left');
           if ($grpId) {

            $this->datatables->where('wp.grpId', $grpId);
            //$this->datatables->where('wp.grpId', $grpId);
        }
        if ($coId) {
            $this->datatables->where('wp.coId', $coId);
          //$this->datatables->where($this->db->dbprefix('warehouses_products') . '.coId', $coId);
        }
        if ($brId) {
            $this->datatables->where('wp.brId', $brId);
           // $this->datatables->where($this->db->dbprefix('warehouses_products') . '.brId', $brId);
        }
    }

        if ($product) {
                $this->datatables->where($this->db->dbprefix('products') . '.id', $product);
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

        $this->datatables->add_column('Actions', $action, 'productid, image, code, name');
        echo $this->datatables->generate();
    }


	public function getProductsTest($warehouse_id = null)
    {
		$this->sma->checkPermissions('index', true);
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : null;

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
        //    $warehouse_id = $user->warehouse_id;
        }

		$product     = $this->input->get('product') ? $this->input->get('product') : null;
        $category    = $this->input->get('category') ? $this->input->get('category') : null;
        $brand       = $this->input->get('brand') ? $this->input->get('brand') : null;
        $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : null;
        $warehouse_id   = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        $product_type   = $this->input->get('product_type') ? $this->input->get('product_type') : null;




        $detail_link = anchor('admin/products/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('product_details'));
        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_product') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . admin_url('products/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_product') . '</a>';
        $single_barcode = anchor('admin/products/print_barcodes/$1', '<i class="fa fa-print"></i> ' . lang('print_barcode_label'));
        // $single_label = anchor_popup('products/single_label/$1/' . ($warehouse_id ? $warehouse_id : ''), '<i class="fa fa-print"></i> ' . lang('print_label'), $this->popup_attributes);
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li><a href="' . admin_url('products/add/$1') . '"><i class="fa fa-plus-square"></i> ' . lang('duplicate_product') . '</a></li>
            <li><a href="' . admin_url('products/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_product') . '</a></li>';
        if ($warehouse_id) {
            $action .= '<li><a href="' . admin_url('products/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                . lang('set_rack') . '</a></li>';
        }
        $action .= '<li><a href="' . base_url() . 'assets/uploads/$2" data-type="image" data-toggle="lightbox"><i class="fa fa-file-photo-o"></i> '
            . lang('view_image') . '</a></li>
            <li>' . $single_barcode . '</li>
            <li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';
        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
            ->select($this->db->dbprefix('products') . ".id as productid,{$this->db->dbprefix('products')}.id as product_number, {$this->db->dbprefix('products')}.image as image,  {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, wp.avg_cost as cost, price as price, COALESCE(wp.quantity, 0) as quantity, {$this->db->dbprefix('units')}.code as unit, wp.rack as rack, alert_quantity", false)
            ->from('products');
            if ($this->Settings->display_all_products) {
                $this->datatables->join("( SELECT product_id, quantity, rack from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left');
            } else {
                $this->datatables->join('warehouses_products wp', 'products.id=wp.product_id', 'left')
                ->where('wp.warehouse_id', $warehouse_id);
                //->where('wp.quantity !=', 0);
            }
            $this->datatables->join('categories', 'products.category_id=categories.id', 'left')
            ->join('units', 'products.unit=units.id', 'left')
            ->join('brands', 'products.brand=brands.id', 'left');
        // ->group_by("products.id");
        } else {

			$wpo = '( SELECT distinct wpo.product_id,   SUM(wpo.quantity) as quantity   from ' . $this->db->dbprefix('warehouses_products') . ' wpo';
			$wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';


            $this->datatables
                ->select($this->db->dbprefix('products') . ".id as productid,{$this->db->dbprefix('products')}.id as product_number, {$this->db->dbprefix('products')}.image as image,  {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, cost as cost, price as price, COALESCE( (WarehousesProductsOrignal.quantity), 0 ) as quantity, {$this->db->dbprefix('units')}.name as unit, '' as rack, alert_quantity", false)
                ->from('products')
                ->join('categories', 'products.category_id=categories.id', 'left')
                ->join('units', 'products.unit=units.id', 'left')
                ->join('brands', 'products.brand=brands.id', 'left')
                ->join($wpo, 'products.id = WarehousesProductsOrignal.product_id', 'left')
				->group_by('products.id');
        }
        if (!$this->Owner && !$this->Admin) {
            if (!$this->session->userdata('show_cost')) {
                $this->datatables->unset_column('cost');
            }
            if (!$this->session->userdata('show_price')) {
                $this->datatables->unset_column('price');
            }
        }
        if ($supplier) {
            $this->datatables->where('supplier1', $supplier)
            ->or_where('supplier2', $supplier)
            ->or_where('supplier3', $supplier)
            ->or_where('supplier4', $supplier)
            ->or_where('supplier5', $supplier);
        }

			if ($product) {
                $this->datatables->where($this->db->dbprefix('products') . '.id', $product);
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

        $this->datatables->add_column('Actions', $action, 'productid, image, code, name');
        echo $this->datatables->generate();
    }


    public function getSubCategories($category_id = null,$brId = null)
    {

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1 && $brId)
            {
                if ($rows = $this->products_model->getSubCategoriesBybrId($category_id,$brId)) {
                    $data = json_encode($rows);
                } else {
                    $data = false;
                }
            }else{
        if ($rows = $this->products_model->getSubCategories($category_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
            }
        echo $data;
    }

    public function getSubUnits($unit_id,$brId = null)
    {
        // $unit = $this->site->getUnitByID($unit_id);
        // if ($units = $this->site->getUnitsByBUID($unit_id)) {
        //     array_push($units, $unit);
        // } else {
        //     $units = array($unit);
        // }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
              {
                $brId = $brId  ? $brId  : 1;
                $units = $this->site->getUnitsByBUIDandbrId($unit_id,$brId);
              }else{
                $units = $this->site->getUnitsByBUID($unit_id);
              }

		//$units = $this->site->getAllBaseUnits();
        $this->sma->send_json($units);
    }



     public function getSubUnitsByProductId($product_id)
    {


		$unit_id = $this->site->getProductByID($product_id)->unit;

		// $unit = $this->site->getUnitByID($unit_id);
        // if ($units = $this->site->getUnitsByBUID($unit_id)) {
        //     array_push($units, $unit);
        // } else {
        //     $units = array($unit);
        // }
        $units = $this->site->getUnitsByBUID($unit_id);
		//$units = $this->site->getAllBaseUnits();
        $this->sma->send_json($units);
    }

    public function getSubUnitsForPrinting($product_id)
    {
        // $unit = $this->site->getUnitByID($unit_id);
        // if ($units = $this->site->getUnitsByBUID($unit_id)) {
        //     array_push($units, $unit);
        // } else {
        //     $units = array($unit);
        // }
        //$units = $this->products_model->getUnitsProductPrices($product_id,$this->Settings->price_group);
        //if(!$units){
            $units = $this->products_model->getUnitsProductPricesV2($product_id);
        //}
		//$units = $this->site->getAllBaseUnits();
        $this->sma->send_json($units);
    }

    /* ---------------------------------------------------------------- */

    public function import_csv()
    {
        $this->sma->checkPermissions('add');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products/import_csv');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles  = array_shift($arrResult);
                $updated = 0;
                $items   = [];
                foreach ($arrResult as $key => $value) {
                    $supplier_name = isset($value[24]) ? trim($value[24]) : '';
                    $supplier      = $supplier_name ? $this->products_model->getSupplierByName($supplier_name) : false;
                    $warehouse      = $this->products_model->getWarehouseByCode(trim($value[27]));

                    $item = [
                        'name'              => isset($value[0]) ? iconv( "Windows-1256", "UTF-8",trim($value[0])) : '',
                        'code'              => isset($value[1]) ? trim($value[1]) : '',
                        'barcode_symbology' => isset($value[2]) ? mb_strtolower(trim($value[2]), 'UTF-8') : '',
                        'brand'             => isset($value[3]) ? trim($value[3]) : '',
                        'category_code'     => isset($value[4]) ? trim($value[4]) : '',
                        'unit'              => isset($value[5]) ? trim($value[5]) : '',
                        'sale_unit'         => isset($value[6]) ? trim($value[6]) : '',
                        'purchase_unit'     => isset($value[7]) ? trim($value[7]) : '',
                        'cost'              => isset($value[8]) ? trim($value[8]) : '',
                        'price'             => isset($value[9]) ? trim($value[9]) : '',
                        'alert_quantity'    => isset($value[10]) ? trim($value[10]) : '',
                        'tax_rate'          => isset($value[11]) ? trim($value[11]) : '',
                        'tax_method'        => isset($value[12]) ? (trim($value[12]) == 'exclusive' ? 1 : 0) : '',
                        'image'             => isset($value[13]) ? trim($value[13]) : '',
                        'subcategory_code'  => isset($value[14]) ? trim($value[14]) : '',
                        'variants'          => isset($value[15]) ? trim($value[15]) : '',
                        'cf1'               => isset($value[16]) ? trim($value[16]) : '',
                        'cf2'               => isset($value[17]) ? trim($value[17]) : '',
                        'cf3'               => isset($value[18]) ? trim($value[18]) : '',
                        'cf4'               => isset($value[19]) ? trim($value[19]) : '',
                        'cf5'               => isset($value[20]) ? trim($value[20]) : '',
                        'cf6'               => isset($value[21]) ? trim($value[21]) : '',
                        'hsn_code'          => isset($value[22]) ? trim($value[22]) : '',
                        'second_name'       => isset($value[23]) ? trim($value[23]) : '',
                        'supplier1'         => $supplier ? $supplier->id : null,
                        'supplier1_part_no' => isset($value[25]) ? trim($value[25]) : '',
                        'supplier1price'    => isset($value[26]) ? trim($value[26]) : '',
                        'slug'              => $this->sma->slug(iconv( "Windows-1256", "UTF-8",trim($value[0]))),
                        'warehouse'         => $warehouse->id,
				        'grpId'             => $warehouse->grpId,
				        'coId'              => $warehouse->coId,
				        'brId'              => $warehouse->brId,
                    ];

                    if ($catd = $this->products_model->getCategoryByCode($item['category_code'])) {
                        $tax_details   = $this->products_model->getTaxRateByName($item['tax_rate']);
                        $prsubcat      = $this->products_model->getCategoryByCode($item['subcategory_code']);
                        $brand         = $this->products_model->getBrandByName($item['brand']);
                        $unit          = $this->products_model->getUnitByCode($item['unit']);
                        $base_unit     = $unit ? $unit->id : null;
                        $sale_unit     = $base_unit;
                        $purcahse_unit = $base_unit;
                        if ($base_unit) {
                            $units = $this->site->getUnitsByBUID($base_unit);
                            foreach ($units as $u) {
                                if ($u->code == $item['sale_unit']) {
                                    $sale_unit = $u->id;
                                }
                                if ($u->code == $item['purchase_unit']) {
                                    $purcahse_unit = $u->id;
                                }
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('check_unit') . ' (' . $item['unit'] . '). ' . lang('unit_code_x_exist') . ' ' . lang('line_no') . ' ' . ($key + 1));
                            admin_redirect('products/import_csv');
                        }

                        unset($item['category_code'], $item['subcategory_code']);
                        $item['unit']           = $base_unit;
                        $item['sale_unit']      = $sale_unit;
                        $item['category_id']    = $catd->id;
                        $item['purchase_unit']  = $purcahse_unit;
                        $item['brand']          = $brand ? $brand->id : null;
                        $item['tax_rate']       = $tax_details ? $tax_details->id : null;
                        $item['subcategory_id'] = $prsubcat ? $prsubcat->id : null;

                        if ($product = $this->products_model->getProductByCode($item['code'])) {
                            if ($product->type == 'standard') {
                                if ($item['variants']) {
                                    $vs = explode('|', $item['variants']);
                                    foreach ($vs as $v) {
                                        if (!empty(trim($v))) {
                                            $variants[] = ['product_id' => $product->id, 'name' => trim($v)];
                                        }
                                    }
                                }
                                unset($item['variants']);
                                if ($this->products_model->updateProduct($product->id, $item, null, null, null, null, $variants)) {
                                    $updated++;
                                }
                            }
                            $item = false;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_category_code') . ' (' . $item['category_code'] . '). ' . lang('category_code_x_exist') . ' ' . lang('line_no') . ' ' . ($key + 1));
                        admin_redirect('products/import_csv');
                    }

                    if ($item) {
                        $items[] = $item;
                    }
                }
            }

             //$this->sma->print_arrays($items);
        }

        if ($this->form_validation->run() == true && !empty($items)) {
            if ($this->products_model->add_products($items)) {
                $updated = $updated ? '<p>' . sprintf(lang('products_updated'), $updated) . '</p>' : '';
                $this->session->set_flashdata('message', sprintf(lang('products_added'), count($items)) . $updated);
                admin_redirect('products');
            }
        } else {
            if (isset($items) && empty($items)) {
                if ($updated) {
                    $this->session->set_flashdata('message', sprintf(lang('products_updated'), $updated));
                    admin_redirect('products');
                } else {
                    $this->session->set_flashdata('warning', lang('csv_issue'));
                }
                admin_redirect('products/import_csv');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('import_products_by_csv')]];
            $meta = ['page_title' => lang('import_products_by_csv'), 'bc' => $bc];
            $this->page_construct('products/import_csv', $meta, $this->data);
        }
    }

     public function import_excel()
    {
        $this->sma->checkPermissions('add');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');


        if ($this->form_validation->run() == true) {
			/*if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products/import_csv');
                }

                $csv = $this->upload->file_name;
				*/
				$this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                //$config['allowed_types'] = 'csv';
                $config['allowed_types'] = 'xlsx|xls';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;

                //$data1['attachment'] = $csv;




                /*$arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles  = array_shift($arrResult);
                $updated = 0;
                $items   = [];
				*/


				if (!$this->upload->do_upload('xlsx_file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}
			if(empty($error)){
			  if (!empty($data['upload_data']['file_name'])) {
				$import_xls_file = $data['upload_data']['file_name'];
			} else {
				$import_xls_file = 0;
			}
			$inputFileName = $this->digital_upload_path . $import_xls_file;


				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);


                //$this->sma->print_arrays($allDataInSheet);

				$flag = true;
				$i=0;
				foreach ($allDataInSheet as $value) {
				  if($flag){
					$flag =false;
					continue;
				  }
				  //$inserdata[$i]['#'] = $value['A'];$keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];
				  $supplier_name = isset($value['Y']) ? trim($value['Y']) : '';
                  $supplier      = $supplier_name ? $this->products_model->getSupplierByName($supplier_name) : false;
                  $warehouse      = $this->products_model->getWarehouseByCode(trim($value['AC']));

				  $mainitems[$i]['name'] = trim($value['A']);
				  $mainitems[$i]['code'] = trim($value['B']);
				  $mainitems[$i]['barcode_symbology'] = trim($value['C']);
				  $mainitems[$i]['brand'] = trim($value['D']);
				  $mainitems[$i]['category_code'] = trim($value['E']);
				  $mainitems[$i]['unit'] = trim($value['F']);
				  $mainitems[$i]['sale_unit'] = trim($value['G']);
				  $mainitems[$i]['purchase_unit'] = trim($value['H']);
				  $mainitems[$i]['cost'] = trim($value['I']) ? trim($value['I']) : 0;
				  $mainitems[$i]['price'] = trim($value['J']);
				  $mainitems[$i]['alert_quantity'] = trim($value['K']);
				  $mainitems[$i]['tax_rate'] = $this->products_model->getTaxRateByName(trim($value['L'])) ? $this->products_model->getTaxRateByName(trim($value['L']))->id : $this->site->getTaxRatesFirst()->id;
				  $mainitems[$i]['tax_method'] = trim($value['M'])? (trim($value['M']) == 'exclusive' ? 1 : 0) : '1';
				  $mainitems[$i]['image'] = trim($value['N']);
				  $mainitems[$i]['subcategory_code'] = trim($value['O']);
				  $mainitems[$i]['variants'] = trim($value['P']);
				  $mainitems[$i]['cf1'] = trim($value['Q']);
				  $mainitems[$i]['cf2'] = trim($value['R']);
				  $mainitems[$i]['cf3'] = trim($value['S']);
				  $mainitems[$i]['cf4'] = trim($value['T']);
				  $mainitems[$i]['cf5'] = trim($value['U']);
				  $mainitems[$i]['cf6'] = trim($value['V']);
				  $mainitems[$i]['hsn_code'] = trim($value['W']);
				  $mainitems[$i]['second_name'] = trim($value['X']);
				  $mainitems[$i]['supplier1'] = $supplier ? $supplier->id : null;
				  $mainitems[$i]['supplier1_part_no'] = trim($value['Z']);
				  $mainitems[$i]['supplier1price'] = trim($value['AA']);
				  $mainitems[$i]['details'] = trim($value['AB']);
				  $mainitems[$i]['warehouse'] = $warehouse->id;
				  $mainitems[$i]['slug'] = $this->sma->slug(trim($value['A']));
                  $mainitems[$i]['quantity'] = $value['AD'] ? trim($value['AD']) : 0;
                  $mainitems[$i]['expiry'] = trim($value['AE']) ? $this->sma->fsd(trim($value['AE'])) : null;
                  $mainitems[$i]['item_no'] = trim($value['AF']) ? trim($value['AF']) : null;
                  $mainitems[$i]['promo_price'] = trim($value['AG']) ? (trim($value['M']) == 'exclusive' ? (trim($value['J'])- trim($value['AG'])) : (((trim($value['J'])*100)/115) - ((trim($value['AG'])*100)/115))) : null;
                  $mainitems[$i]['promo_start_date'] = trim($value['AH']) ? $this->sma->fsd(trim($value['AH'])) : null;
                  $mainitems[$i]['promo_end_date'] = trim($value['AI']) ? $this->sma->fsd(trim($value['AI'])) : null;
                  $mainitems[$i]['grpId'] = $warehouse->grpId;
				  $mainitems[$i]['coId'] = $warehouse->coId;
				  $mainitems[$i]['brId'] = $warehouse->brId;

				  $i++;
				}
				$rw          = 2;
                $differences = 0;
                $matches     = 0;
				$PW = [];

                //$this->sma->print_arrays($mainitems,$OpeningBal);
                //$this->sma->print_arrays($this->products_model->getCategoryByCode($mainitems[2]['category_code']));
				foreach ($mainitems as $item) {

					if ($catd = $this->products_model->getCategoryByCode($item['category_code'])) {
                //$tax_details   = $this->products_model->getTaxRateByName($item['tax_rate']);
                        $prsubcat      = $this->products_model->getCategoryByCode($item['subcategory_code']);
                        $brand         = $this->products_model->getBrandByName($item['brand']);
                        $unit          = $this->products_model->getUnitByCode($item['unit']);
                        if(isset($unit->base_unit)){
                            $this->session->set_flashdata('error', lang('check_unit') . ' (' . $item['unit'] . '). ' . lang('Unit_code_is_not_a_base_unit') . ' ' . lang('line_no') . ' ' . ($rw));
                            admin_redirect('products/import_excel');
                        }
                        //$this->sma->print_arrays($unit);
                        $base_unit     = $unit ? $unit->id : null;
                        $sale_unit     = $base_unit;
                        $purcahse_unit = $base_unit;
                        if ($base_unit) {

                            $units = $this->site->getUnitsByBUID($base_unit);
                            foreach ($units as $u) {
                                if ($u->code == $item['sale_unit']) {
                                    $sale_unit = $u->id;
                                }
                                if ($u->code == $item['purchase_unit']) {
                                    $purcahse_unit = $u->id;
                                }
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('check_unit') . ' (' . $item['unit'] . '). ' . lang('unit_code_x_exist') . ' ' . lang('line_no') . ' ' . ($rw));
                            admin_redirect('products/import_excel');
                        }

                        unset($item['category_code'], $item['subcategory_code']);
                        $item['unit']           = $base_unit;
                        $item['sale_unit']      = $sale_unit;
                        $item['category_id']    = $catd->id;
                        $item['purchase_unit']  = $purcahse_unit;
                        $item['brand']          = $brand ? $brand->id : null;
                        //$item['tax_rate']       = $tax_details ? $tax_details->id : null;
                        $item['subcategory_id'] = $prsubcat ? $prsubcat->id : null;






                        if ($product = $this->products_model->getProductByCode($item['code'])) {
							if ($product->type == 'standard') {
                                if ($item['variants']) {
                                    $vs = explode('|', $item['variants']);
                                    foreach ($vs as $v) {
                                        if (!empty(trim($v))) {
                                            $variants[] = ['product_id' => $product->id, 'name' => trim($v)];
                                        }
                                    }
                                }
                                unset($item['variants']);
                                //if ($this->products_model->updateProduct($product->id, $item, null, null, null, null, $variants)) {
                                //    $updated++;
                                //}
                            }
                            $PW[] = ['product_id' => $product->id, 'warehouse_id' => $item['warehouse'], 'quantity' => '0', 'avg_cost' => '0', 'grpId' => $item['grpId'], 'coId' => $item['coId'], 'brId' => $item['brId']];


							$item = false;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_category_code') . ' (' . $item['category_code'] . '). ' . lang('category_code_x_exist') . ' ' . lang('line_no') . ' ' . ($rw));
                        admin_redirect('products/import_excel');
                    }



                    if ($item) {
                        $items[] = $item;
                    }
					$rw++;
				}

                /*foreach ($arrResult as $key => $value) {
                    $supplier_name = isset($value[24]) ? trim($value[24]) : '';
                    $supplier      = $supplier_name ? $this->products_model->getSupplierByName($supplier_name) : false;

                    $item = [
                        'name'              => isset($value[0]) ? trim($value[0]) : '',
                        'code'              => isset($value[1]) ? trim($value[1]) : '',
                        'barcode_symbology' => isset($value[2]) ? mb_strtolower(trim($value[2]), 'UTF-8') : '',
                        'brand'             => isset($value[3]) ? trim($value[3]) : '',
                        'category_code'     => isset($value[4]) ? trim($value[4]) : '',
                        'unit'              => isset($value[5]) ? trim($value[5]) : '',
                        'sale_unit'         => isset($value[6]) ? trim($value[6]) : '',
                        'purchase_unit'     => isset($value[7]) ? trim($value[7]) : '',
                        'cost'              => isset($value[8]) ? trim($value[8]) : '',
                        'price'             => isset($value[9]) ? trim($value[9]) : '',
                        'alert_quantity'    => isset($value[10]) ? trim($value[10]) : '',
                        'tax_rate'          => isset($value[11]) ? trim($value[11]) : '',
                        'tax_method'        => isset($value[12]) ? (trim($value[12]) == 'exclusive' ? 1 : 0) : '',
                        'image'             => isset($value[13]) ? trim($value[13]) : '',
                        'subcategory_code'  => isset($value[14]) ? trim($value[14]) : '',
                        'variants'          => isset($value[15]) ? trim($value[15]) : '',
                        'cf1'               => isset($value[16]) ? trim($value[16]) : '',
                        'cf2'               => isset($value[17]) ? trim($value[17]) : '',
                        'cf3'               => isset($value[18]) ? trim($value[18]) : '',
                        'cf4'               => isset($value[19]) ? trim($value[19]) : '',
                        'cf5'               => isset($value[20]) ? trim($value[20]) : '',
                        'cf6'               => isset($value[21]) ? trim($value[21]) : '',
                        'hsn_code'          => isset($value[22]) ? trim($value[22]) : '',
                        'second_name'       => isset($value[23]) ? trim($value[23]) : '',
                        'supplier1'         => $supplier ? $supplier->id : null,
                        'supplier1_part_no' => isset($value[25]) ? trim($value[25]) : '',
                        'supplier1price'    => isset($value[26]) ? trim($value[26]) : '',
                        'slug'              => $this->sma->slug($value[0]),
                    ];

                    if ($catd = $this->products_model->getCategoryByCode($item['category_code'])) {
                        $tax_details   = $this->products_model->getTaxRateByName($item['tax_rate']);
                        $prsubcat      = $this->products_model->getCategoryByCode($item['subcategory_code']);
                        $brand         = $this->products_model->getBrandByName($item['brand']);
                        $unit          = $this->products_model->getUnitByCode($item['unit']);
                        $base_unit     = $unit ? $unit->id : null;
                        $sale_unit     = $base_unit;
                        $purcahse_unit = $base_unit;
                        if ($base_unit) {
                            $units = $this->site->getUnitsByBUID($base_unit);
                            foreach ($units as $u) {
                                if ($u->code == $item['sale_unit']) {
                                    $sale_unit = $u->id;
                                }
                                if ($u->code == $item['purchase_unit']) {
                                    $purcahse_unit = $u->id;
                                }
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('check_unit') . ' (' . $item['unit'] . '). ' . lang('unit_code_x_exist') . ' ' . lang('line_no') . ' ' . ($key + 1));
                            admin_redirect('products/import_csv');
                        }

                        unset($item['category_code'], $item['subcategory_code']);
                        $item['unit']           = $base_unit;
                        $item['sale_unit']      = $sale_unit;
                        $item['category_id']    = $catd->id;
                        $item['purchase_unit']  = $purcahse_unit;
                        $item['brand']          = $brand ? $brand->id : null;
                        $item['tax_rate']       = $tax_details ? $tax_details->id : null;
                        $item['subcategory_id'] = $prsubcat ? $prsubcat->id : null;

                        if ($product = $this->products_model->getProductByCode($item['code'])) {
                            if ($product->type == 'standard') {
                                if ($item['variants']) {
                                    $vs = explode('|', $item['variants']);
                                    foreach ($vs as $v) {
                                        if (!empty(trim($v))) {
                                            $variants[] = ['product_id' => $product->id, 'name' => trim($v)];
                                        }
                                    }
                                }
                                unset($item['variants']);
                                if ($this->products_model->updateProduct($product->id, $item, null, null, null, null, $variants)) {
                                    $updated++;
                                }
                            }
                            $item = false;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_category_code') . ' (' . $item['category_code'] . '). ' . lang('category_code_x_exist') . ' ' . lang('line_no') . ' ' . ($key + 1));
                        admin_redirect('products/import_csv');
                    }

                    if ($item) {
                        $items[] = $item;
                    }
                }*///foreach

            }


        }




        if ($this->form_validation->run() == true ) {
            //$this->sma->print_arrays($items);
            if (!empty($items)) {
            if ($this->products_model->add_products($items)) {
                $updated = $updated ? '<p>' . sprintf(lang('products_updated'), $updated) . '</p>' : '';
                $this->session->set_flashdata('message', sprintf(lang('products_added'), count($items)) . $updated);
                admin_redirect('products');
            }
			}

			if (!empty($PW)) {
            if ($this->products_model->addProductsToWarehouse($PW)) {
                $updated = $updated ? '<p>' . sprintf(lang('products_updated'), $updated) . '</p>' : '';
                $this->session->set_flashdata('message', sprintf(lang('products_added'), count($PW)) . $updated);
                admin_redirect('products');
            }
			}
        } else {
            if (isset($items) && empty($items)) {
                if ($updated) {
                    $this->session->set_flashdata('message', sprintf(lang('products_updated'), $updated));
                    admin_redirect('products');
                } else {
                    $this->session->set_flashdata('warning', lang('csv_issue'));
                }
                admin_redirect('products/import_excel');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('import_excel')]];
            $meta = ['page_title' => lang('import_excel'), 'bc' => $bc];
            $this->page_construct('products/import_excel', $meta, $this->data);
        }
    }




  public function index($warehouse_id = null,$isShop = '')
    {
        $this->sma->checkPermissions();

		$warehouse_id   = $this->input->post('warehouse') ? $this->input->post('warehouse') : null;

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

             $this->data['billers']    = $this->input->post('brId') ? $this->site->getBillerBybrId($this->input->post('brId')) : $this->site->getBillerBybrId($this->session->userdata('brId'));//$this->site->getAllCompanies('biller');
             if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
             $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));//$this->site->getAllWarehouses();
             $this->data['warehouse_id'] = $warehouse_id;
             $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
                }else{
                $this->data['warehouses']   = $this->site->getWarehouseByID($this->session->userdata('warehouse_id'));
                $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
                $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;

             }
             $this->data['categories']          = $this->input->post('brId') ?  $this->site->getCategoryBybrId($this->input->post('brId')) : $this->site->getCategoryBybrId($this->session->userdata('brId'));//$this->site->getAllCategories();
             $this->data['brands']              = $this->input->post('brId') ? $this->site->getBrandBybrId($this->input->post('brId')) : $this->site->getBrandBybrId($this->session->userdata('brId'));//$this->site->getAllBrands();


            }else{
                if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
                    $this->data['warehouses']   = $this->site->getAllWarehouses();
                    $this->data['warehouse_id'] = $warehouse_id;
                    $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
                } else {
                    $this->data['warehouses']   = $this->site->getWarehouseByID($this->session->userdata('warehouse_id'));
                    $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
                    $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
                }

                $this->data['categories'] = $this->site->getAllCategories();
                $this->data['brands']     = $this->site->getAllBrands();
            }



        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');






        $this->data['tax_rates']           = $this->site->getAllTaxRatesActive();
        $this->data['base_units']          = $this->site->getAllBaseUnits();
        $this->data['supplier'] = $this->input->get('supplier') ? $this->site->getCompanyByID($this->input->get('supplier')) : null;
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('products')]];
        $meta                   = ['page_title' => lang('products'), 'bc' => $bc];
        $this->page_construct('products/index', $meta, $this->data,$isShop);
    }

    /* --------------------------------------------------------------------------------------------- */

    public function modal_view($id = null)
    {
        $this->sma->checkPermissions('index', true);

        $pr_details = $this->site->getProductByID($id);
        if (!$id || !$pr_details) {
            if ($this->input->is_ajax_request()) {
                die("<script type='text/javascript'>alert('".lang('prduct_not_found')."');$('#myModal').modal('hide');</script>");
            }else{
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            $this->sma->md();
            }
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $combo_items = $this->site->getProductComboItems($id);
            $combo_items_avg_cost = $this->site->getProductComboItemsWhitAVGCost($id);

			foreach ($combo_items as $combo_item) {
				if($this->site->getProductByID($combo_item->id)->type == 'combo'){
						foreach ($combo_items_avg_cost as $combo_item_avg_cost) {

							$combo_item_avg_cost->totalcost=$combo_item_avg_cost->totalcost+$this->site->getProductComboItemsWhitAVGCost($combo_item->id,$combo_item_avg_cost->warehouse_id)[0]->totalcost;
						}
				}
			}
			//$this->sma->print_arrays($combo_items,$combo_items_avg_cost);

			$this->data['combo_items'] = $combo_items;
			$this->data['combo_items_avg_cost'] = $combo_items_avg_cost;

        }
        $this->data['product']     = $pr_details;
        $this->data['unit']        = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand']       = $this->site->getBrandByID($pr_details->brand);
        $this->data['images']      = $this->products_model->getProductPhotos($id);
        $this->data['category']    = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : null;
        $this->data['tax_rate']    = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : null;
        $this->data['warehouses']  = $this->products_model->getAllWarehousesWithPQ($id);
        //$this->data['options']     = $this->products_model->getProductOptionsWithWH($id);
        $this->data['options']          = $this->products_model->getProductOptionsWithWH_new($id);
        $this->data['variants']    = $this->products_model->getProductOptions($id);
		$this->data['product_price']        = $this->products_model->getProductPriceByPId($id);

        $this->load->view($this->theme . 'products/modal_view', $this->data);

    }

    public function pdf($id = null, $view = null)
    {
        $this->sma->checkPermissions('index');

        $pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product']          = $pr_details;
        $this->data['unit']             = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand']            = $this->site->getBrandByID($pr_details->brand);
        $this->data['images']           = $this->products_model->getProductPhotos($id);
        $this->data['category']         = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory']      = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : null;
        $this->data['tax_rate']         = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : null;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses']       = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options']          = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants']         = $this->products_model->getProductOptions($id);

        $name = $pr_details->code . '_' . str_replace('/', '_', $pr_details->name) . '.pdf';
        if ($view) {
            $this->load->view($this->theme . 'products/pdf', $this->data);
        } else {
            $html = $this->load->view($this->theme . 'products/pdf', $this->data, true);
            if (!$this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            $this->sma->generate_pdf($html, $name);
        }
    }

    public function print_barcodes($product_id = null)
    {
        $this->sma->checkPermissions('barcode', true);

        $this->form_validation->set_rules('style', lang('style'), 'required');

        if ($this->form_validation->run() == true) {


			$jsonArr_product_id=json_decode($_POST['jsonArr_product_id']);
			$jsonArr_product_id= (array) $jsonArr_product_id;

			$product_id_arr = [];
			foreach ($jsonArr_product_id as $product_arr) {
				$product_id_arr[]=  $product_arr->value;

			}

			$jsonArr_quantity=json_decode($_POST['jsonArr_quantity']);
			$jsonArr_quantity= (array) $jsonArr_quantity;

			$quantity_arr = [];
			foreach ($jsonArr_quantity as $pr_arr) {
				$quantity_arr[]=  $pr_arr->value;

			}

			$jsonArr_productionDate=json_decode($_POST['jsonArr_productionDate']);
			$jsonArr_productionDate= (array) $jsonArr_productionDate;

			$productionDate_arr = [];
			foreach ($jsonArr_productionDate as $pr_arr) {
				$productionDate_arr[]=  $pr_arr->value;

			}

			$jsonArr_product_unit=json_decode($_POST['jsonArr_product_unit']);
			$jsonArr_product_unit= (array) $jsonArr_product_unit;

			$product_unit_arr = [];
			foreach ($jsonArr_product_unit as $pr_arr) {
				$product_unit_arr[]=  $pr_arr->value;

			}



			//$this->sma->print_arrays($_POST['product_tax'],$product_tax_arr);
			//$this->sma->print_arrays($_POST['product_discount'],$product_discount_arr);



            $style      = $this->input->post('style');
			$cf_width      = $this->input->post('cf_width');
			$cf_height      = $this->input->post('cf_height');

            $data = [
				'style' => $this->input->post('style'),
                'width'  => $this->input->post('cf_width') ? $this->input->post('cf_width') : null,
                'height'  => $this->input->post('cf_height') ? $this->input->post('cf_height') : null,
                'orientation'     => $this->input->post('cf_orientation'),
                'brand'   => $this->input->post('brand') ? $this->input->post('brand') : 0,
                'site_name'   => $this->input->post('site_name') ? $this->input->post('site_name') : 0,
                'product_name'   => $this->input->post('product_name') ? $this->input->post('product_name') : 0,
                'production_date'   => $this->input->post('Production_Date') ? $this->input->post('Production_Date') : 0,
                'expiry'   => $this->input->post('expiry') ? $this->input->post('expiry') : 0,
                'price'   => $this->input->post('price') ? $this->input->post('price') : 0,
                'currencies'   => $this->input->post('currencies') ? $this->input->post('currencies') : 0,
                'unit'   => $this->input->post('unit') ? $this->input->post('unit') : 0,
                'category'   => $this->input->post('category') ? $this->input->post('category') : 0,
                'variants'   => $this->input->post('variants') ? $this->input->post('variants') : 0,
                'product_image'   => $this->input->post('product_image') ? $this->input->post('product_image') : 0,
                'check_promo'   => $this->input->post('check_promo') ? $this->input->post('check_promo') : 0,
                'with_tax'   => $this->input->post('with_tax') ? $this->input->post('with_tax') : 0,
            ];

            if ($this->form_validation->run() == true && $this->products_model->updatePrintBarcodeSettings($data)) {
            }

			//$this->sma->print_arrays($_POST);
			//$warehouse_id = $this->input->post('warehouse_id');

            $bci_size   = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            /*if($cf_width>=4 && $cf_height>=2 && $style == 50){
                $bci_size   =50;
            }*/
            $currencies = $this->site->getAllCurrencies();
            //$s          = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
			$s                = sizeof($product_id_arr);
            //$this->sma->print_arrays($product_id_arr);


            if ($s < 1) {
                $this->session->set_flashdata('error', lang('no_product_selected'));
                admin_redirect('products/print_barcodes');
            }
            for ($m = 0; $m < $s; $m++) {
                $pid            = $product_id_arr[$m];
                $quantity       = $quantity_arr[$m];
                $productionDate       = $productionDate_arr[$m];
                //$prunit       = isset($product_unit_arr[$m]) ? $product_unit_arr[$m] : '' ;
				$prunit       = $product_unit_arr[$m] ;


                $product        = $this->products_model->getProductWithCategory($pid);

				$product2        = $this->products_model->getProductByID($pid);
                //$product->price = $this->input->post('check_promo') ? ($product->promotion ? $product->promo_price : $product->price) : $product->price;
                $this->load->admin_model('promo_model');
                 if($promosdis = $this->promo_model->getPromosByProductDiscount($product->id)){

				$product->discount        = $promosdis->percentage_off;
                if(strpos($product->discount."",'%')!== false){
                }else{
                $product->discount =$product->discount."%";
                }
                $product->discount=$this->site->calculateDiscount($product->discount, ($product->price));
				}else if($promosdis = $this->promo_model->getPromosByProductDiscountFixed($product->id)){

                    $product->discount        = $promosdis->fixed_off;

                    $product->discount=$this->site->calculateDiscount($product->discount, ($product->price));
                    }else{
				$product->discount        = '0';
				}
                if($this->input->post('check_promo') && $product->discount){
                  $product->price_after_discount = ($product->price-($product->tax_method==1 ? $product->discount : ($product->discount*115/100)));
                }else{
                    $product->price_after_discount=0;
                }
                $product->price=($this->products_model->getProductGroupPriceWithUnitV2($product_id_arr[$m],$prunit)->price ? $this->sma->formatMoney($this->products_model->getProductGroupPriceWithUnitV2($product_id_arr[$m],$prunit)->price,'none') : $this->sma->formatMoney($product->price,'none'));

                if($this->input->post('with_tax') && $product->tax_method==1){
                    $tax_details = $this->site->getTaxRateByID($product->tax_rate);
                    $ctax        = $this->site->calculateTax($product, $tax_details, $product->price);
                    $product->price    = $product->price + $ctax['amount'];

                }
                //$this->sma->print_arrays($product);
                /*if ($variants = $this->products_model->getProductOptions($pid)) {
                    foreach ($variants as $option) {
                        if ($this->input->post('vt_' . $product->id . '_' . $option->id)) {
                            $barcodes[] = [
                                'site'    => $this->input->post('site_name') ? $this->Settings->site_name : false,
                                'name'    => $this->input->post('product_name') ? $product->name . ' - ' . $option->name : false,
                                'image'   => $this->input->post('product_image') ? $product->image : false,
                                'barcode' => $product->code . $this->Settings->barcode_separator . $option->id,
                                'bcs'     => 'code128',
                                'bcis'    => $bci_size,
                                // 'barcode' => $this->product_barcode($product->code . $this->Settings->barcode_separator . $option->id, 'code128', $bci_size),
                                'price'      => $this->input->post('price') ? $this->sma->formatMoney($option->price != 0 ? ($product->price + $option->price) : $product->price, 'none') : false,
                                'rprice'     => $this->input->post('price') ? ($option->price != 0 ? ($product->price + $option->price) : $product->price) : false,
                                'unit'       => $this->input->post('unit') ? $product->unit : false,
                                'category'   => $this->input->post('category') ? $product->category : false,
                                'currencies' => $this->input->post('currencies'),
                                'variants'   => $this->input->post('variants') ? $variants : false,
                                'quantity'   => $quantity,
                            ];
                        }
                    }
                } else*/ {
                   // $this->sma->print_arrays($productionDate);
                   $barcodes[] = [
                        'site'  => $this->input->post('site_name') ? (($this->Settings->user_language == 'arabic' || $this->Settings->site_ename=='') ? $this->Settings->site_name : $this->Settings->site_ename) : false,
                        'name'  => $this->input->post('product_name') ? $product->name : false,
						'expiry'  => $this->input->post('expiry') ? "E :".$this->sma->hrsd($product->expiry) : false,
						'productionDate'  => $productionDate != '' ? 'P :'.$productionDate : '',
						//'name'  => $this->input->post('product_name') ? $this->site->getBrandByID($product2->brand)->name : false,

                        'image' => $this->input->post('product_image') ? $product->image : false,
                        // 'barcode' => $this->product_barcode($product->code, $product->barcode_symbology, $bci_size),
                        'barcode'    => $product->code,
                        'barcode2'    => $this->products_model->getProductGroupPriceWithUnitV2($product_id_arr[$m],$prunit)->product_code,
                        'bcs'        => $product->barcode_symbology,
                        'bcis'       => $bci_size,
						'prunit'       => $prunit != $product->unit && $prunit != '' ? '_'.$prunit : '',
						'prunitName'       => $this->site->getUnitByID($prunit != $product->unit && $prunit != '' ? $prunit : $product->unit)->name,

                        //'price'      => $this->input->post('price') ? $this->sma->formatMoney($product->price, 'none') : false,
						'price'     => $this->input->post('price') ? $product->price : false,//$this->input->post('price') ? ($this->products_model->getProductGroupPriceWithUnitV2($product_id_arr[$m], $this->Settings->price_group,$prunit)->price ? $this->sma->formatMoney($this->products_model->getProductGroupPriceWithUnitV2($product_id_arr[$m], $this->Settings->price_group,$prunit)->price,'none') : $this->sma->formatMoney($product->price,'none')) :  false,
                        'price_after_discount'     => $product->price_after_discount ? $this->sma->formatMoney($product->price_after_discount,'none') :  false,
                        'rprice'     => $this->input->post('price') ? $product->price : false,
                        'unit'       => $this->input->post('unit') ? $product->unit : false,
                        'category'   => $this->input->post('category') ? $product->category : false,
                        'brand'   => $this->input->post('brand') ? $this->site->getBrandByID($product->brand)->name : false,
                        'currencies' => $this->input->post('currencies'),
                        'currency_code' => $product->currency_code,
                        'variants'   => false,
                        'quantity'   => $quantity,
                    ];

                }


            }

            $this->data['barcodes']   = $barcodes;
            $this->data['currencies'] = $currencies;
            $this->data['style']      = $style;
            $this->data['items']      = false;
			//$this->sma->print_arrays($barcodes);
            $this->data['print_barcode_settings'] = $this->products_model->getPrintBarcodeSettings();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('print_barcodes')]];
            $meta                     = ['page_title' => lang('print_barcodes'), 'bc' => $bc];
            $this->page_construct('products/print_barcodes', $meta, $this->data);
        } else {
            if ($this->input->get('purchase') || $this->input->get('transfer')) {
                if ($this->input->get('purchase')) {
                    $purchase_id = $this->input->get('purchase', true);
                    $items       = $this->products_model->getPurchaseItems($purchase_id);
                } elseif ($this->input->get('transfer')) {
                    $transfer_id = $this->input->get('transfer', true);
                    $items       = $this->products_model->getTransferItems($transfer_id);
                }
                if ($items) {
                    foreach ($items as $item) {
                        if ($row = $this->products_model->getProductByID($item->product_id)) {
                            $selected_variants = false;
                            if ($variants = $this->products_model->getProductOptions($row->id)) {
                                foreach ($variants as $variant) {
                                    $selected_variants[$variant->id] = isset($pr[$row->id]['selected_variants'][$variant->id]) && !empty($pr[$row->id]['selected_variants'][$variant->id]) ? 1 : ($variant->id == $item->option_id ? 1 : 0);
                                }
                            }
                            $pr[$row->id] = ['productionDate'  => $productionDate != '' ? 'P :'.$productionDate : '','id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $item->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];
                        }
                    }
                    $this->data['message'] = lang('products_added_to_list');
                }
            }

            if ($product_id) {
                if ($row = $this->site->getProductByID($product_id)) {
                    $selected_variants = false;
                    if ($variants = $this->products_model->getProductOptions($row->id)) {
                        foreach ($variants as $variant) {
                            $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                        }
                    }
                    $pr[$row->id] = ['productionDate'  => $productionDate != '' ? 'P :'.$productionDate : '','id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'unit' => $row->unit, 'variants' => $variants, 'selected_variants' => $selected_variants];
                    $this->data['message'] = lang('product_added_to_list');
                }
            }

            if ($this->input->get('category')) {
                if ($products = $this->products_model->getCategoryProducts($this->input->get('category'))) {
                    foreach ($products as $row) {
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = ['productionDate'  => $productionDate != '' ? 'P :'.$productionDate : '','id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];
                    }
                    $this->data['message'] = lang('products_added_to_list');
                } else {
                    $pr = [];
                    $this->session->set_flashdata('error', lang('no_product_found'));
                }
            }

            if ($this->input->get('subcategory')) {
                if ($products = $this->products_model->getSubCategoryProducts($this->input->get('subcategory'))) {
                    foreach ($products as $row) {
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = ['productionDate'  => $productionDate != '' ? 'P :'.$productionDate : '','id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];
                    }
                    $this->data['message'] = lang('products_added_to_list');
                } else {
                    $pr = [];
                    $this->session->set_flashdata('error', lang('no_product_found'));
                }
            }
            $this->data['items'] = isset($pr) ? json_encode($pr) : false;
            $this->data['print_barcode_settings'] = $this->products_model->getPrintBarcodeSettings();
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            //$this->sma->print_arrays($this->data['print_barcode_settings']);
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('print_barcodes')]];
            $meta                = ['page_title' => lang('print_barcodes'), 'bc' => $bc];
            $this->page_construct('products/print_barcodes', $meta, $this->data);
        }
    }

    public function product_actions($wh = null)
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'sync_quantity') {
					  // $this->sma->print_arrays($_POST);
					foreach ($_POST['val'] as $id) {


                        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                    $grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : 1;
                    $coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
                    $brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
                        }
                    //$product     = $this->input->get('product') ? $this->input->get('product') : null;
                    $category    = $this->input->get('category') ? $this->input->get('category') : null;
                    $brand       = $this->input->get('brand') ? $this->input->get('brand') : null;
                    $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : null;
                    $warehouse   = $_POST['warehouse'] ? $_POST['warehouse'] : $this->Settings->default_warehouse;
                    $product_type   = $this->input->get('product_type') ? $this->input->get('product_type') : null;
                    //$this->sma->print_arrays($warehouse);


                    //$type   = "combo";
                    $cf1         = $this->input->get('cf1') ? $this->input->get('cf1') : null;
                    $cf2         = $this->input->get('cf2') ? $this->input->get('cf2') : null;
                    $cf3         = $this->input->get('cf3') ? $this->input->get('cf3') : null;
                    $cf4         = $this->input->get('cf4') ? $this->input->get('cf4') : null;
                    $cf5         = $this->input->get('cf5') ? $this->input->get('cf5') : null;
                    $cf6         = $this->input->get('cf6') ? $this->input->get('cf6') : null;
                    $start_date  = $this->input->get('start_date') ? $this->input->get('start_date') : null;
                    $end_date    = $this->input->get('end_date') ? $this->input->get('end_date') : null;




                    $newqQty=$this->getBalance($id,$warehouse)[0]->netTotal;

            //$this->sma->print_arrays($newqQty);
//$json=$this->datatables->generate();

            if($newqQty){

                $qty=$this->site->getWarehouseProduct($warehouse,$id)->quantity;

                if($qty!=$newqQty){


                    $blQty=$newqQty-$qty;

                    $clause = ['product_id' => $id, 'option_id' => null, 'warehouse_id' => $warehouse, 'status' => 'received'];
                    $this->site->setPurchaseItem($clause, ($blQty));

                    $this->site->syncProductQty($id, $warehouse);

                    //$this->sma->print_arrays($blQty,$qty,$newqQty);
                }



           // $this->sma->print_arrays($json,$warehouse);





            //echo $this->datatables->generate();


                            //$this->site->syncProductQty($id, $warehouse->id);
                            //$this->site->checkOverSold($id, $warehouse->id);

                        }







                        //$this->site->syncQuantity(NULL, NULL, NULL, $id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("products_quantity_sync"));
                    redirect($_SERVER["HTTP_REFERER"]);
                } elseif ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete');
                    $delete_flage=0;
                    $non_delete_flage=0;
                    foreach ($_POST['val'] as $id) {

                        $product             = $this->site->getProductByID($id);
        if ($product->type=="manufacturing") {
            $non_delete_flage++;
		}else{


        $totalQty=0;
        $warehouses=$this->site->getAllWarehouses();

			foreach($warehouses as $warehouses){
                $totalQty .= $this->sma->formatQuantity($this->getBalance($id,$warehouse)[0]->netTotal);
            }

            if($totalQty!=0 || $this->site->getAllPurchaseItemsItemCountV2($id) || $this->site->getAllSalesItemsItemCount($id)){
                $non_delete_flage++;
            }else{
                        $this->products_model->deleteProduct($id);
                        $delete_flage++;
            }
                    }
                }
                    if($delete_flage>0){
                        if($non_delete_flage>0){
                            $this->session->set_flashdata('warning', $this->lang->line('Some_products_have_been_successfully_removed'));
                        }else{
                            $this->session->set_flashdata('message', $this->lang->line('products_deleted'));
                        }

                    }else{
                        $this->session->set_flashdata('error', $this->lang->line('You_cannot_delete_these_products_There_are_related_operations'));
                    }

                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'labels') {
                    foreach ($_POST['val'] as $id) {
                        $row               = $this->products_model->getProductByID($id);
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];
                    }

                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['print_barcode_settings'] = $this->products_model->getPrintBarcodeSettings();
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('print_barcodes')]];
                    $meta                = ['page_title' => lang('print_barcodes'), 'bc' => $bc];
                    $this->page_construct('products/print_barcodes', $meta, $this->data);
                } elseif ($this->input->post('form_action') == 'export_excel') {
					redirect($_SERVER['HTTP_REFERER']);
					/*
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Products');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('barcode_symbology'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('brand'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('category_code'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('sale') . ' ' . lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('purchase') . ' ' . lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('cost'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('alert_quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('L1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('M1', lang('tax_method'));
                    $this->excel->getActiveSheet()->SetCellValue('N1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('O1', lang('subcategory_code'));
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('product_variants'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('pcf1'));
                    $this->excel->getActiveSheet()->SetCellValue('R1', lang('pcf2'));
                    $this->excel->getActiveSheet()->SetCellValue('S1', lang('pcf3'));
                    $this->excel->getActiveSheet()->SetCellValue('T1', lang('pcf4'));
                    $this->excel->getActiveSheet()->SetCellValue('U1', lang('pcf5'));
                    $this->excel->getActiveSheet()->SetCellValue('V1', lang('pcf6'));
                    $this->excel->getActiveSheet()->SetCellValue('W1', lang('hsn_code'));
                    $this->excel->getActiveSheet()->SetCellValue('X1', lang('second_name'));
                    $this->excel->getActiveSheet()->SetCellValue('Y1', lang('supplier_name'));
                    $this->excel->getActiveSheet()->SetCellValue('Z1', lang('supplier_part_no'));
                    $this->excel->getActiveSheet()->SetCellValue('AA1', lang('supplier_price'));
                    $this->excel->getActiveSheet()->SetCellValue('AB1', lang('quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('AC1', lang('details'));
                    $this->excel->getActiveSheet()->SetCellValue('AD1', lang('product_details'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $product   = $this->products_model->getProductDetail($id);
                        $brand     = $this->site->getBrandByID($product->brand);
                        $base_unit = $sale_unit = $purchase_unit = '';
                        if ($units = $this->site->getUnitsByBUID($product->unit)) {
                            foreach ($units as $u) {
                                if ($u->id == $product->unit) {
                                    $base_unit = $u->code;
                                }
                                if ($u->id == $product->sale_unit) {
                                    $sale_unit = $u->code;
                                }
                                if ($u->id == $product->purchase_unit) {
                                    $purchase_unit = $u->code;
                                }
                            }
                        }
                        $variants         = $this->products_model->getProductOptions($id);
                        $product_variants = '';
                        if ($variants) {
                            $i = 1;
                            $v = count($variants);
                            foreach ($variants as $variant) {
                                $product_variants .= trim($variant->name) . ($i != $v ? '|' : '');
                                $i++;
                            }
                        }
                        $quantity = $product->quantity;
                        if ($wh) {
                            if ($wh_qty = $this->products_model->getProductQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
                        }
                        $supplier = false;
                        if ($product->supplier1) {
                            $supplier = $this->site->getCompanyByID($product->supplier1);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $product->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $product->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $product->barcode_symbology);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($brand ? $brand->name : ''));
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $product->category_code);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale_unit);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase_unit);
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $product->cost);
                        }
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $product->price);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $product->alert_quantity);
                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $product->tax_rate_name);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $product->tax_method ? lang('exclusive') : lang('inclusive'));
                        $this->excel->getActiveSheet()->SetCellValue('N' . $row, $product->image);
                        $this->excel->getActiveSheet()->SetCellValue('O' . $row, $product->subcategory_code);
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $product_variants);
                        $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $product->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('R' . $row, $product->cf2);
                        $this->excel->getActiveSheet()->SetCellValue('S' . $row, $product->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('T' . $row, $product->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('U' . $row, $product->cf5);
                        $this->excel->getActiveSheet()->SetCellValue('V' . $row, $product->cf6);
                        $this->excel->getActiveSheet()->SetCellValue('W' . $row, $product->hsn_code);
                        $this->excel->getActiveSheet()->SetCellValue('X' . $row, $product->second_name);
                        $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $supplier ? $supplier->name : '');
                        $this->excel->getActiveSheet()->SetCellValue('Z' . $row, $supplier ? $product->supplier1_part_no : '');
                        $this->excel->getActiveSheet()->SetCellValue('AA' . $row, $supplier ? $product->supplier1price : '');
                        $this->excel->getActiveSheet()->SetCellValue('AB' . $row, $quantity);
                        $this->excel->getActiveSheet()->SetCellValue('AC' . $row, $product->details);
                        $this->excel->getActiveSheet()->SetCellValue('AD' . $row, $product->product_details);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('AD')->setWidth(40);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'products_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                */}
            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_product_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'admin/products');
        }
    }

    public function qa_suggestions()
    {
        $term = $this->input->get('term', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->products_model->getQASuggestions($sr);
        if ($rows) {
            foreach ($rows as $row) {
				$row->qty    = 1;
                $row->cost    = 0;
                $row->value    = 0;
                $row->expiry   = '';
                $options     = $this->products_model->getProductOptions($row->id);
                $row->unit     = $this->products_model->getUnitById($row->unit);
                $row->option = $option_id;
                $row->serial = '';
                $c           = sha1(uniqid(mt_rand(), true));
                $pr[]        = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'            => $row, 'options' => $options, ];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    /* ----------------------------------------------------------------------------- */

    public function quantity_adjustments($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments');
		/*if(!$this->site->IsPermissions('adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('quantity_adjustments')]];
        $meta                = ['page_title' => lang('quantity_adjustments'), 'bc' => $bc];
        $this->page_construct('products/quantity_adjustments', $meta, $this->data);
		}
    }
    public function warehouse_store($warehouse_id = null)
    {
        $this->sma->checkPermissions('warehouse_store',true);

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('warehouse_store')]];
        $meta                = ['page_title' => lang('warehouse_store'), 'bc' => $bc];

		$this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		//$this->load->view($this->theme . 'products/set_rack', $this->data);
        $this->page_construct('products/warehouse_store', $meta, $this->data);
    }

	 public function destruction($warehouse_id = null)
    {
        $this->sma->checkPermissions('destruction',true);

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Inventory_operations')]];
        $meta                = ['page_title' => lang('Inventory_operations'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/destruction', $meta, $this->data);
    }

    public function set_rack($product_id = null, $warehouse_id = null)
    {
        $this->sma->checkPermissions('edit', true);

        $this->form_validation->set_rules('rack', lang('rack_location'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = ['rack'    => $this->input->post('rack'),
                'product_id'   => $product_id,
                'warehouse_id' => $warehouse_id,
            ];
        } elseif ($this->input->post('set_rack')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/' . $warehouse_id);
        }

        if ($this->form_validation->run() == true && $this->products_model->setRack($data)) {
            $this->session->set_flashdata('message', lang('rack_set'));
            admin_redirect('products/' . $warehouse_id);
        } else {
            $this->data['error']        = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['product']      = $this->site->getProductByID($product_id);
            $wh_pr                      = $this->products_model->getProductQuantity($product_id, $warehouse_id);
            $this->data['rack']         = $wh_pr['rack'];
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'products/set_rack', $this->data);
        }
    }

    public function stock_counts($warehouse_id = null)
    {
        $this->sma->checkPermissions('stock_count');

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

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('stock_counts')]];
        $meta = ['page_title' => lang('stock_counts'), 'bc' => $bc];
        $this->page_construct('products/stock_counts', $meta, $this->data);
    }

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1,'unit'=>$row->unit];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
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

	public function suggestions3()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductNames2($term);
        if ($rows) {
            foreach ($rows as $row) {
				$unit_name='';
				$rows1=$this->site->getUnitByID($row->unit);
			    $pr[] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->cost, 'qty' => 1,'unit'=>$this->site->getUnitByID2($row->unit)];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    /* ------------------------------------------------------------------ */

    public function update_price()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('message', lang('disabled_in_demo'));
                admin_redirect('welcome');
            }

            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = ['code', 'price'];

                $final = [];

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if (!$this->products_model->getProductByCode(trim($csv_pr['code']))) {
                        $this->session->set_flashdata('message', lang('check_product_code') . ' (' . $csv_pr['code'] . '). ' . lang('code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('products');
                    }
                    $rw++;
                }
            }
        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/group_product_prices/' . $group_id);
        }

        if ($this->form_validation->run() == true && !empty($final)) {
            $this->products_model->updatePrice($final);
            $this->session->set_flashdata('message', lang('price_updated'));
            admin_redirect('products');
        } else {
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'products/update_price', $this->data);
        }
    }

    public function view($id = null)
    {
        $this->sma->checkPermissions('index');

		$pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            //$this->data['combo_items'] = $this->site->getProductComboItems($id);
			//$this->data['combo_items_avg_cost'] = $this->site->getProductComboItemsWhitAVGCost($id);
			$combo_items = $this->site->getProductComboItems($id);
            $combo_items_avg_cost = $this->site->getProductComboItemsWhitAVGCost($id);

			foreach ($combo_items as $combo_item) {
				if($this->site->getProductByID($combo_item->id)->type == 'combo'){
						foreach ($combo_items_avg_cost as $combo_item_avg_cost) {

							$combo_item_avg_cost->totalcost=$combo_item_avg_cost->totalcost+$this->site->getProductComboItemsWhitAVGCost($combo_item->id,$combo_item_avg_cost->warehouse_id)[0]->totalcost;
						}
				}
			}
			//$this->sma->print_arrays($combo_items,$combo_items_avg_cost);

			$this->data['combo_items'] = $combo_items;
			$this->data['combo_items_avg_cost'] = $combo_items_avg_cost;


        }
        $this->data['pid']          = $id;
        $this->data['product']          = $pr_details;
        $this->data['unit']             = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand']            = $this->site->getBrandByID($pr_details->brand);
        $this->data['images']           = $this->products_model->getProductPhotos($id);
        $this->data['category']         = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory']      = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : null;
        $this->data['tax_rate']         = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : null;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses']       = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['all_warehouses']       = $this->site->getAllWarehouses();
        //$this->data['options']          = false;//$this->products_model->getProductOptionsWithWH($id);
        $this->data['options']          = $this->products_model->getProductOptionsWithWH_new($id);
        $this->data['variants']         = $this->products_model->getProductOptions($id);
        $this->data['sold']             = $this->products_model->getSoldQty($id);
        $this->data['purchased']        = $this->products_model->getPurchasedQty($id);
		$this->data['billers']    = $this->site->getAllCompanies('biller');
		$this->data['product_price']        = $this->products_model->getProductPriceByPId($id);

		//$this->sma->print_arrays($this->data['options']);



        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => $pr_details->name]];
        $meta = ['page_title' => $pr_details->name, 'bc' => $bc];
        $this->page_construct('products/view', $meta, $this->data);
    }

    public function view_adjustment($id)
    {
        $this->sma->checkPermissions('adjustments', true);

        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        $this->data['inv']        = $adjustment;
        $this->data['rows']       = $this->products_model->getAdjustmentItems($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse']  = $this->site->getWarehouseByID($adjustment->warehouse_id);
		$this->load->view($this->theme . 'products/view_adjustment', $this->data);
    }
    public function view_destruction($id)
    {
        $this->sma->checkPermissions('destruction', true);

        $adjustment = $this->products_model->getDestructionByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        $this->data['inv']        = $adjustment;
        $this->data['rows']       = $this->products_model->getDestructionItems($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse']  = $this->site->getWarehouseByID($adjustment->warehouse_id);
		$this->load->view($this->theme . 'products/view_destruction', $this->data);
    }
    public function view_adjustment_count($id)
    {
        $this->sma->checkPermissions('adjustments', true);

        $adjustment = $this->products_model->getAdjustmentByID_count($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        $this->data['inv']        = $adjustment;
        $this->data['rows']       = $this->products_model->getAdjustmentItems_count($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse']  = $this->site->getWarehouseByID($adjustment->warehouse_id);
		$this->load->view($this->theme . 'products/view_adjustment_count', $this->data);
    }

    public function view_adjustment_store($id)
    {
        $this->sma->checkPermissions('adjustments', true);

        $adjustment = $this->products_model->getAdjustmentByID_store($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        $this->data['inv']        = $adjustment;
        $this->data['rows']       = $this->products_model->getAdjustmentItems_store($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse']  = $this->site->getWarehouseByID($adjustment->warehouse_id);
		$this->load->view($this->theme . 'products/view_adjustment_store', $this->data);
    }

    public function view_count($id)
    {
        $this->sma->checkPermissions('stock_count', true);
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count->finalized) {
            $this->sma->md('admin/products/finalize_count_temp/' . $id);
        }

        $this->data['stock_count']       = $stock_count;
        $this->data['stock_count_items'] = $this->products_model->getStockCountItems($id);
        $this->data['warehouse']         = $this->site->getWarehouseByID($stock_count->warehouse_id);
        $this->data['adjustment']        = $this->products_model->getAdjustmentByCountID_count($id);
        $this->data['adjustment1']        = $this->products_model->getAdjustmentByCountID_store($id);
        $this->load->view($this->theme . 'products/view_count', $this->data);
    }

    public function view_count_temp($id)
    {
        $this->sma->checkPermissions('stock_count', true);
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count->finalized) {
            $this->sma->md('admin/products/finalize_count_temp/' . $id);
        }

        $this->data['stock_count']       = $stock_count;
        $this->data['stock_count_items'] = $this->products_model->getStockCountItems($id);
        $this->data['warehouse']         = $this->site->getWarehouseByID($stock_count->warehouse_id);
        $this->data['adjustment']        = $this->products_model->getAdjustmentByCountID_count($id);
        $this->data['adjustment1']        = $this->products_model->getAdjustmentByCountID_store($id);
        //$this->load->view($this->theme . 'products/view_count_temp', $this->data);
		    $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('view_count')]];
            $meta                           = ['page_title' => lang('view_count'), 'bc' => $bc];
            $this->page_construct('products/view_count_temp', $meta, $this->data);

    }

	public function destruction_inventory($count_id = null)
    {
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
			$allcost = 0;
			$allvalue = 0;
			$allquantity = 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $_POST['product_id'][$r];
                $type       = 'subtraction';
                $quantity   = $_POST['quantity'][$r];
                $serial     = $_POST['serial'][$r];
                $cost     = $_POST['product_cost'][$r];
                $unit     = $_POST['product_unit'][$r];
                $value     = $_POST['product_val'][$r];
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction' && !$count_id) {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'cost'     => $cost,
                    'unit'     => $unit,
                    'value'     => $value,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                ];
				$allcost = $allcost + $cost;
				$allvalue = $allvalue + $value;
				$allquantity = $allquantity + $quantity;
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'cost' => $allcost,
                'value' => $allvalue,
                'quantity' => $allquantity,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => $this->input->post('count_id') ? $this->input->post('count_id') : null,
                'op_type'     => $this->input->post('op_type') ? $this->input->post('op_type') : 1,
                'op_acct'     => $this->input->post('op_acct') ? $this->input->post('op_acct') : ($this->Settings->accounting ? $this->site->getAccountsSettings()->warehouses_destruction : null),
            ];

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }


            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->destruction_inventory($data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/destruction');
        } else {
            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items       = $this->products_model->getStockCountItems($count_id);
                foreach ($items as $item) {
                    $c = sha1(uniqid(mt_rand(), true));
                    if ($item->counted != $item->expected) {
                        $product     = $this->site->getProductByID($item->product_id);
                        $row         = json_decode('{}');
                        $row->id     = $item->product_id;
                        $row->code   = $product->code;
                        $row->name   = $product->name;
                        $row->qty    = $item->counted - $item->expected;
                        $row->type   = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty    = $row->qty > 0 ? $row->qty : (0 - $row->qty);
                        $options     = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $ri          = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'options' => $options,'cost' =>$row->cost ];
                        $c++;
                    }
                }
            }

            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : false;
            $this->data['warehouse_id']     = $count_id ? $stock_count->warehouse_id : false;
            $this->data['count_id']         = $count_id;
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses']       = $this->site->getAllWarehouses();
            if ($this->Settings->accounting){
                $this->data['accounts_settings']   = $this->accounts_model->getSettings();
                $this->data['accounts'] = $this->accounts_model->getAllDetailsAccounts();
            }

            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Inventory_operations')]];
            $meta                           = ['page_title' => lang('Inventory_operations'), 'bc' => $bc];
            $this->page_construct('products/destruction_inventory', $meta, $this->data);
        }
    }

	public function de_suggestions()
    {
        $term = $this->input->get('term', true);
		$warehouse_id = $this->input->get('warehouse_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];
       // $rows = $this->products_model->getQASuggestions($sr);
		$rows = $this->sales_model->getProductNames($sr, $warehouse_id, $pos);

        if ($rows) {
            foreach ($rows as $row) {
				$row->qty    = 1;
				$row->cost    =$this->products_model->getCostById($row->id,$warehouse_id);
                $row->value    = 0;
                $options     = $this->products_model->getProductOptions($row->id);
                $row->unit     = $this->products_model->getUnitById($row->unit);
                $row->option = $option_id;
                $row->serial = '';
                $c           = sha1(uniqid(mt_rand(), true));
				if($row->cost!=false)
				{
                $pr[]        = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                'row'        => $row, 'options' => $options, ];
				}

            }

            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

		/* --------------------------add warehouse----------------------------- */

    public function warehouses()
    {
		$this->sma->checkPermissions();
		/*if(!$this->site->IsPermissions('warehouse-warehouses')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{
			*/

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('warehouses')]];
        $meta = ['page_title' => lang('warehouses'), 'bc' => $bc];
        $this->page_construct('products/warehouses', $meta, $this->data);
		//}
    }
    public function getWarehouses()
    {
        $this->load->library('datatables');
        $this->datatables
            //->select("{$this->db->dbprefix('warehouses')}.id as id, map, warehouse_logo, code, {$this->db->dbprefix('warehouses')}.name as name,{$this->db->dbprefix('warehouses')}.branch_name as branch_name, {$this->db->dbprefix('warehouses')}.vat_reg_no as vat_reg_no, {$this->db->dbprefix('price_groups')}.name as price_group, phone, email, address")
            ->select("{$this->db->dbprefix('warehouses')}.id as id, map, warehouse_logo, code, {$this->db->dbprefix('warehouses')}.name as name,{$this->db->dbprefix('warehouses')}.branch_name as branch_name, {$this->db->dbprefix('warehouses')}.vat_reg_no as vat_reg_no, {$this->db->dbprefix('price_groups')}.name as price_group, phone, address")
            ->from('warehouses')
            ->join('price_groups', 'price_groups.id=warehouses.price_group_id', 'left')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('products/edit_warehouse/$1') . "' class='tip' title='" . lang('edit_warehouse') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_warehouse') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_warehouse/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }
    public function delete_warehouse($id = null)
    {
        //$this->sma->print_arrays($id);
        //ini_set('display_errors', 1);

		//$this->sma->checkPermissions();
		if(!$this->site->IsPermissions('products-delete_warehouse')){
			 //$this->session->set_flashdata('error', lang('access_denied'));
			 $this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
               // die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{
            if($this->settings_model->checkWarehouseOperation($id)){

                $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_Warehouse_There_are_relevant_processes')]);
            }
        if ($this->settings_model->deleteWarehouse($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('warehouse_deleted')]);
        }
		}
    }

    public function edit_warehouse($id = null)
    {
        //$this->sma->checkPermissions();
		if(!$this->site->IsPermissions('products-edit_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{
		$this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');

        $wh_details = $this->settings_model->getWarehouseByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[warehouses.code]');
        }
        $this->form_validation->set_rules('address', lang('address'), 'required');
		$this->form_validation->set_rules('street_name', lang('Street_name'), 'required');
        $this->form_validation->set_rules('district_name', lang('District_name'), 'required');
        $this->form_validation->set_rules('city', lang('city'), 'required');
        $this->form_validation->set_rules('state', lang('state'), 'required');
        $this->form_validation->set_rules('country', lang('country'), 'required');
        $this->form_validation->set_rules('building_no', lang('Building_No'), 'required|numeric');
        $this->form_validation->set_rules('postal_code', lang('postal_code'), 'required|numeric');
        $this->form_validation->set_rules('additional_no', lang('Additional_No'), 'required|numeric');
        //$this->form_validation->set_rules('vat_reg_no', lang('VAT_Regesteration_Number'), 'required|numeric');
        //$this->form_validation->set_rules('cr_no', lang('CR_Number'), 'required|numeric');
        $this->form_validation->set_rules('warehouse_logo', lang('Warehouse_logo'), 'xss_clean');
		$this->form_validation->set_rules('map', lang('map_image'), 'xss_clean');
        $this->form_validation->set_rules('warehouse_Seal', lang('Warehouse_Seal'), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $isLogistic=0;
            if($this->input->post('isLogistic')==1){
                $isLogistic=1;

            }

            $isTobacco=0;
            if($this->input->post('isTobacco')==1){
                $isTobacco=1;

            }

            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'branch_name'           => $this->input->post('branch_name'),
                'nameEn'           => $this->input->post('nameEn'),
                'branch_nameEn'           => $this->input->post('branch_nameEn'),
                'vat_reg_no'           => $this->input->post('vat_reg_no'),
                'cr_no'           => $this->input->post('cr_no'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $_POST['address'],//$this->input->post('address'),
                'addressEn'        => $_POST['addressEn'],//$this->input->post('addressEn'),
                'price_group_id' => $this->input->post('price_group'),
                'building_no' => $this->input->post('building_no'),
                'street_name' => $this->input->post('street_name'),
                'district_name' => $this->input->post('district_name'),
                'city' => $this->input->post('city'),
                'postal_code' => $this->input->post('postal_code'),
                'additional_no' => $this->input->post('additional_no'),
                'state' => $this->input->post('state'),
                'country' => $this->input->post('country'),
				'tax_method'            => $this->input->post('tax_method'),
                'isLogistic'            => $isLogistic,
                'street_nameEn' => $this->input->post('street_nameEn'),
                'district_nameEn' => $this->input->post('district_nameEn'),
                'cityEn' => $this->input->post('cityEn'),
                'stateEn' => $this->input->post('stateEn'),
                'countryEn' => $this->input->post('countryEn'),
                'isTobacco'            => $isTobacco,
            ];


            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('products/warehouses');
                }

                $data['map'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $data['map'];
                $config['new_image']      = 'assets/uploads/thumbs/' . $data['map'];
                $config['maintain_ratio'] = true;
                $config['width']          = 76;
                $config['height']         = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
			if ($_FILES['warehouse_logo']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = 1024;
                $config['max_width']     = '300';
                $config['max_height']    = '80';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('warehouse_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products/warehouses');
                }

                $data['warehouse_logo'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $data['warehouse_logo'];
                $config['new_image']      = 'assets/uploads/thumbs/' . $data['warehouse_logo'];
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }

            if ($_FILES['warehouse_Seal']['size'] > 0) {
                $dbName=str_replace("cpesoft_","",$this->db->database);
                if (!file_exists($this->upload_path . 'logos/'.$dbName)) {
                    mkdir($this->upload_path . 'logos/'.$dbName, 0777, true);
                }
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/'.$dbName.'/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 200;
                $config['max_height']    = 200;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('warehouse_Seal')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $data['warehouse_Seal'] = $this->upload->file_name;
            }

            //$this->sma->print_arrays($id, $data);
        } elseif ($this->input->post('edit_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/warehouses');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateWarehouse($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('warehouse_updated'));
            admin_redirect('products/warehouses');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }

            $this->data['warehouse']    = $this->settings_model->getWarehouseByID($id);
            $this->data['price_groups'] = $this->settings_model->getAllPriceGroups($id);
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'products/edit_warehouse', $this->data);
        }
		}
    }
    public function warehouse_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        if($this->settings_model->checkWarehouseOperation($id)){

                            $this->session->set_flashdata('error', lang('You_cannot_delete_this_Warehouse_There_are_relevant_processes'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                        $this->settings_model->deleteWarehouse($id);
                    }
                    $this->session->set_flashdata('message', lang('warehouses_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                        $this->load->library('excel');

                    $this->excel->setActiveSheetIndex(0);
                     if ($this->Settings->user_language == 'arabic'){
                        $this->excel->getActiveSheet()->setRightToLeft(true);
                        }
                    $this->excel->getActiveSheet()->setTitle(lang('warehouses'));

                    $row = 1;
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
                    $this->excel->getActiveSheet()->mergeCells('A'.$row.':H'.$row);

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('warehouses'));
                     $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                      $this->excel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
                         $row++;

                        $this->excel->getActiveSheet()->mergeCells('A'.$row.':H'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, $this->Settings->site_name);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A2')->applyFromArray($fillColor);
                        $row++;

                    $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('Branch_name'));
                    $this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('VAT_Regesteration_Number'));
                  //  $this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('price_group'));
                    $this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('phone'));
                    $this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('email'));
                    $this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('address'));
                   $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->applyFromArray($fillColor);
                        $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         $this->excel->getActiveSheet()->getStyle('A'.$row.':H'.$row)->applyFromArray($Font11Bold);
                        $row++;


                    foreach ($_POST['val'] as $id) {
                        $wh = $this->settings_model->getWarehouseByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $wh->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $wh->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $wh->branch_name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $wh->vat_reg_no);
                       // $this->excel->getActiveSheet()->SetCellValue('E' . $row, $wh->price_group_id);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $wh->phone);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $wh->email);
                          $address= str_replace("<p>","",$wh->address."");
						$address= str_replace("</p>","",($address)."");
                          $address= str_replace("<br>","/n",$address."");
						$address= str_replace("</br>","",($address)."");
                          $this->excel->getActiveSheet()->SetCellValue('H' . $row,$address);
                         //$wh = str_replace('<p>', '', $cell->getFormattedValue($wh->address));



                        $row++;
                    }
                     for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
                                $this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                        }

                    //$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                   // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                   // $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                   // $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'warehouses_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_warehouse_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function add_warehouse()
    {
		if(!$this->site->IsPermissions('products-add_warehouse')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|is_unique[warehouses.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('address', lang('address'), 'required');
        $this->form_validation->set_rules('street_name', lang('Street_name'), 'required');
        $this->form_validation->set_rules('district_name', lang('District_name'), 'required');
        $this->form_validation->set_rules('city', lang('city'), 'required');
        $this->form_validation->set_rules('state', lang('state'), 'required');
        $this->form_validation->set_rules('country', lang('country'), 'required');
        $this->form_validation->set_rules('building_no', lang('Building_No'), 'required|numeric');
        $this->form_validation->set_rules('postal_code', lang('postal_code'), 'required|numeric');
        $this->form_validation->set_rules('additional_no', lang('Additional_No'), 'required|numeric');
        //$this->form_validation->set_rules('vat_reg_no', lang('VAT_Regesteration_Number'), 'required|numeric');
        //$this->form_validation->set_rules('cr_no', lang('CR_Number'), 'required|numeric');
        $this->form_validation->set_rules('warehouse_logo', lang('Warehouse_logo'), 'xss_clean');
		$this->form_validation->set_rules('userfile', lang('map_image'), 'xss_clean');


        if ($this->form_validation->run() == true) {
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('products/warehouses');
                }

                $map = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $map;
                $config['new_image']      = 'assets/uploads/thumbs/' . $map;
                $config['maintain_ratio'] = true;
                $config['width']          = 76;
                $config['height']         = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $map = null;
            }
            if ($_FILES['warehouse_logo']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = 1024;
                $config['max_width']     = '300';
                $config['max_height']    = '80';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('warehouse_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products/warehouses');
                }

                $warehouse_logo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $warehouse_logo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $warehouse_logo;
                $config['maintain_ratio'] = true;
                $config['width']          = 300;
                $config['height']         = 80;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $warehouse_logo = null;
            }

            $isLogistic=0;
            if($this->input->post('isLogistic')==1){
                $isLogistic=1;

            }

            $isTobacco=0;
            if($this->input->post('isTobacco')==1){
                $isTobacco=1;

            }

            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'branch_name'           => $this->input->post('branch_name'),
                'nameEn'           => $this->input->post('nameEn'),
                'branch_nameEn'           => $this->input->post('branch_nameEn'),
                'cr_no'           => $this->input->post('cr_no'),
                'vat_reg_no'           => $this->input->post('vat_reg_no'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'addressEn'        => $this->input->post('addressEn'),
                'price_group_id' => $this->input->post('price_group'),
                'building_no' => $this->input->post('building_no'),
                'street_name' => $this->input->post('street_name'),
                'district_name' => $this->input->post('district_name'),
                'city' => $this->input->post('city'),
                'postal_code' => $this->input->post('postal_code'),
                'additional_no' => $this->input->post('additional_no'),
                'state' => $this->input->post('state'),
                'country' => $this->input->post('country'),
                'map'            => $map,
                'warehouse_logo'            => $warehouse_logo,
                'tax_method'            => $this->input->post('tax_method'),
                'isLogistic'            => $isLogistic,
                'street_nameEn' => $this->input->post('street_nameEn'),
                'district_nameEn' => $this->input->post('district_nameEn'),
                'cityEn' => $this->input->post('cityEn'),
                'stateEn' => $this->input->post('stateEn'),
                'countryEn' => $this->input->post('countryEn'),
                'isTobacco'            => $isTobacco,

            ];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }

        } elseif ($this->input->post('add_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/warehouses');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addWarehouse($data)) {
            $this->session->set_flashdata('message', lang('warehouse_added'));
            admin_redirect('products/warehouses');
        } else {


            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['price_groups'] = $this->settings_model->getAllPriceGroups();
            $this->data['warehouse_code'] = 'w_'.$this->products_model->getWarehouseAutoCode()->id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'products/add_warehouse', $this->data);
        }
		}
    }
    public function transfers()
    {
        //$this->sma->checkPermissions();
		if(!$this->site->IsPermissions('transfers-index')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('transfers')]];
        $meta = ['page_title' => lang('transfers'), 'bc' => $bc];
        $this->page_construct('transfers/index', $meta, $this->data);
		}
    }

    public function transfers_add()
    {
        //$this->sma->checkPermissions();
		if(!$this->site->IsPermissions('transfers-add')){
			 $this->session->set_flashdata('error', lang('access_denied'));
			 //$this->sma->send_json(['error' => 1, 'msg' => lang('access_denied')]);
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{


        $this->form_validation->set_message('is_natural_no_zero', lang('no_zero_required'));
        $this->form_validation->set_rules('to_warehouse', lang('warehouse') . ' (' . lang('to') . ')', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('from_warehouse', lang('warehouse') . ' (' . lang('from') . ')', 'required|is_natural_no_zero');

        if ($this->form_validation->run()) {
            $transfer_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('to');
            if ($this->Owner || $this->Admin || $this->GP['transfers_date']) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $to_warehouse           = $this->input->post('to_warehouse');
            $from_warehouse         = $this->input->post('from_warehouse');
            $note                   = $this->sma->clear_tags($this->input->post('note'));
            $shipping               = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $status                 = $this->input->post('status');
            $from_warehouse_details = $this->site->getWarehouseByID($from_warehouse);
            $from_warehouse_code    = $from_warehouse_details->code;
            $from_warehouse_name    = $from_warehouse_details->name;
            $to_warehouse_details   = $this->site->getWarehouseByID($to_warehouse);
            $to_warehouse_code      = $to_warehouse_details->code;
            $to_warehouse_name      = $to_warehouse_details->name;

            $total       = 0;
            $product_tax = 0;
            $gst_data    = [];
            $total_cgst  = $total_sgst  = $total_igst  = 0;
            $i           = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_code          = $_POST['product_code'][$r];
                $item_net_cost      = $this->sma->formatDecimal($_POST['net_cost'][$r],4);
                $unit_cost          = $this->sma->formatDecimal($_POST['unit_cost'][$r],4);
                $real_unit_cost     = $this->sma->formatDecimal($_POST['real_unit_cost'][$r],4);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_tax_rate      = $_POST['product_tax'][$r] ?$_POST['product_tax'][$r] : null;
                $item_expiry        = isset($_POST['expiry'][$r]) ? $this->sma->fsd($_POST['expiry'][$r]) : null;
                $item_option        = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' && $_POST['product_option'][$r] != 'undefined' && $_POST['product_option'][$r] != 'null' ? $_POST['product_option'][$r] : null;
                $item_unit          = $_POST['product_unit'][$r];
                $item_quantity      = $_POST['product_base_quantity'][$r];


                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->transfers_model->getProductByCode($item_code);
                    // if (!$this->Settings->overselling) {
                    $warehouse_quantity = $this->transfers_model->getWarehouseProduct($from_warehouse_details->id, $product_details->id, $item_option);
                    if ($warehouse_quantity->quantity < $item_quantity) {
                        $this->session->set_flashdata('error', lang('no_match_found') . ' (' . lang('product_name') . ' <strong>' . $product_details->name . '</strong> ' . lang('product_code') . ' <strong>' . $product_details->code . '</strong>)');
                        admin_redirect('transfers/add');
                    }
                    // }

                    $pr_item_tax   = $item_tax   = 0;
                    $tax           = '';
                    $item_net_cost = $unit_cost;

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {
                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_cost);
                        $item_tax    = $ctax['amount'];
                        $tax         = $ctax['tax'];
                        if (!empty($product_details) && $product_details->tax_method != 1) {
                            $item_net_cost = $unit_cost - $item_tax;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, false, $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_unit_quantity) + $pr_item_tax), 4);
                    $unit     = $this->site->getUnitByID($item_unit);

                    $product = [
                        'product_id'        => $product_details->id,
                        'product_code'      => $item_code,
                        'product_name'      => $product_details->name,
                        'option_id'         => $item_option,
                        'net_unit_cost'     => $this->sma->formatDecimal($item_net_cost, 4),
                        'unit_cost'         => $this->sma->formatDecimal($item_net_cost + $item_tax, 4),
                        'quantity'          => $item_quantity,
                        'received_quantity'          => $item_quantity,
                        'product_unit_id'   => $item_unit,
                        'product_unit_code' => $unit->code,
                        'unit_quantity'     => $item_unit_quantity,
                        'quantity_balance'  => $item_quantity,
                        'warehouse_id'      => $to_warehouse,
                        'item_tax'          => $pr_item_tax,
                        'tax_rate_id'       => $item_tax_rate,
                        'tax'               => $tax,
                        'subtotal'          => $this->sma->formatDecimal($subtotal),
                        'expiry'            => $item_expiry,
                        'real_unit_cost'    => $real_unit_cost,
                        'date'              => date('Y-m-d', strtotime($date)),
                    ];

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal(($item_net_cost * $item_unit_quantity), 4);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_items'), 'required');
            } else {
                krsort($products);
            }

            $grand_total = $this->sma->formatDecimal(($total + $this->sma->formatDecimal($shipping) + $product_tax), 4);
            $data        = ['transfer_no' => $transfer_no,
                'date'                    => $date,
                'from_warehouse_id'       => $from_warehouse,
                'from_warehouse_code'     => $from_warehouse_code,
                'from_warehouse_name'     => $from_warehouse_name,
                'to_warehouse_id'         => $to_warehouse,
                'to_warehouse_code'       => $to_warehouse_code,
                'to_warehouse_name'       => $to_warehouse_name,
                'note'                    => $note,
                'total_tax'               => $product_tax,
                'total'                   => $total,
                'grand_total'             => $grand_total,
                'created_by'              => $this->session->userdata('user_id'),
                'status'                  => $status,
                'shipping'                => $this->sma->formatDecimal($shipping),
            ];
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

        // $this->sma->print_arrays($data, $products);

        }

        if ($this->form_validation->run() == true && $this->transfers_model->addTransfer($data, $products)) {
            $this->session->set_userdata('remove_tols', 1);
            $this->session->set_flashdata('message', lang('transfer_added'));
            admin_redirect('transfers');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['name'] = ['name' => 'name',
                'id'                      => 'name',
                'type'                    => 'text',
                'value'                   => $this->form_validation->set_value('name'),
            ];
            $this->data['quantity'] = ['name' => 'quantity',
                'id'                          => 'quantity',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('quantity'),
            ];

            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['rnumber']    = ''; //$this->site->getReference('to');

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('add_transfer')]];
            $meta = ['page_title' => lang('transfer_quantity'), 'bc' => $bc];
            $this->page_construct('transfers/add', $meta, $this->data);
        }
    }
	}
    public function transfer_by_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_message('is_natural_no_zero', lang('no_zero_required'));
        $this->form_validation->set_rules('to_warehouse', lang('warehouse') . ' (' . lang('to') . ')', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('from_warehouse', lang('warehouse') . ' (' . lang('from') . ')', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run()) {
            $transfer_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('to');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $to_warehouse           = $this->input->post('to_warehouse');
            $from_warehouse         = $this->input->post('from_warehouse');
            $note                   = $this->sma->clear_tags($this->input->post('note'));
            $shipping               = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $status                 = $this->input->post('status');
            $from_warehouse_details = $this->site->getWarehouseByID($from_warehouse);
            $from_warehouse_code    = $from_warehouse_details->code;
            $from_warehouse_name    = $from_warehouse_details->name;
            $to_warehouse_details   = $this->site->getWarehouseByID($to_warehouse);
            $to_warehouse_code      = $to_warehouse_details->code;
            $to_warehouse_name      = $to_warehouse_details->name;

            $total       = 0;
            $product_tax = 0;
            $gst_data    = [];
            $total_cgst  = $total_sgst  = $total_igst  = 0;
            if (isset($_FILES['userfile'])) {
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('transfers/transfer_bt_csv');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys  = ['product', 'unit_cost', 'quantity', 'variant', 'expiry'];
                $final = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                $rw = 2;
                foreach ($final as $csv_pr) {
                    $item_code     = $csv_pr['product'];
                    $unit_cost     = $csv_pr['unit_cost'];
                    $item_quantity = $csv_pr['quantity'];
                    $variant       = $csv_pr['variant'] ? $csv_pr['variant'] : null;
                    $item_expiry   = isset($csv_pr['expiry']) ? $this->sma->fsd($csv_pr['expiry']) : null;

                    if (isset($item_code) && isset($unit_cost) && isset($item_quantity)) {
                        if (!($product_details = $this->transfers_model->getProductByCode($item_code))) {
                            $this->session->set_flashdata('error', lang('pr_not_found') . ' ( ' . $csv_pr['product'] . ' ). ' . lang('line_no') . ' ' . $rw);
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                        if ($variant) {
                            $item_option = $this->transfers_model->getProductVariantByName($variant, $product_details->id);
                            if (!$item_option) {
                                $this->session->set_flashdata('error', lang('pr_not_found') . ' ( ' . $csv_pr['product'] . ' - ' . $csv_pr['variant'] . ' ). ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $item_option     = json_decode('{}');
                            $item_option->id = null;
                        }

                        if (!$this->Settings->overselling) {
                            $warehouse_quantity = $this->transfers_model->getWarehouseProduct($from_warehouse_details->id, $product_details->id, $item_option->id);
                            if ($warehouse_quantity->quantity < $item_quantity) {
                                $this->session->set_flashdata('error', lang('no_match_found') . ' (' . lang('product_name') . ' <strong>' . $product_details->name . '</strong> ' . lang('product_code') . ' <strong>' . $product_details->code . '</strong>) ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }

                        $pr_item_tax   = $item_tax   = 0;
                        $tax           = '';
                        $item_net_cost = $unit_cost;
                        if (isset($product_details->tax_rate) && $product_details->tax_rate != 0) {
                            $tax_details = $this->site->getTaxRateByID($product_details->tax_rate);
                            $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_cost);
                            $item_tax    = $ctax['amount'];
                            $tax         = $ctax['tax'];
                            if (!empty($product_details) && $product_details->tax_method != 1) {
                                $item_net_cost = $unit_cost - $item_tax;
                            }
                            $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_quantity), 4);
                            if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, false, $tax_details)) {
                                $total_cgst += $gst_data['cgst'];
                                $total_sgst += $gst_data['sgst'];
                                $total_igst += $gst_data['igst'];
                            }
                        }

                        $product_tax += $pr_item_tax;
                        $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_quantity) + $pr_item_tax), 4);
                        $unit     = $this->site->getUnitByID($product_details->unit);

                        $product = [
                            'product_id'        => $product_details->id,
                            'product_code'      => $item_code,
                            'product_name'      => $product_details->name,
                            'option_id'         => $item_option->id,
                            'net_unit_cost'     => $item_net_cost,
                            'unit_cost'         => $this->sma->formatDecimal($unit_cost, 4),
                            'quantity'          => $item_quantity,
                            'product_unit_id'   => $unit ? $unit->id : null,
                            'product_unit_code' => $unit ? $unit->code : null,
                            'unit_quantity'     => $item_quantity,
                            'quantity_balance'  => $item_quantity,
                            'warehouse_id'      => $to_warehouse,
                            'item_tax'          => $pr_item_tax,
                            'tax_rate_id'       => $product_details->tax_rate,
                            'tax'               => $tax,
                            'subtotal'          => $subtotal,
                            'expiry'            => $item_expiry,
                            'real_unit_cost'    => $unit_cost,
                            'date'              => date('Y-m-d', strtotime($date)),
                        ];

                        $products[] = ($product + $gst_data);
                        $total += $this->sma->formatDecimal(($item_net_cost * $item_quantity), 4);
                    }
                    $rw++;
                }
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('order_item'), 'required');
            } else {
                krsort($products);
            }
            $grand_total = $total + $shipping + $product_tax;
            $data        = ['transfer_no' => $transfer_no,
                'date'                    => $date,
                'from_warehouse_id'       => $from_warehouse,
                'from_warehouse_code'     => $from_warehouse_code,
                'from_warehouse_name'     => $from_warehouse_name,
                'to_warehouse_id'         => $to_warehouse,
                'to_warehouse_code'       => $to_warehouse_code,
                'to_warehouse_name'       => $to_warehouse_name,
                'note'                    => $note,
                'total_tax'               => $product_tax,
                'total'                   => $total,
                'grand_total'             => $grand_total,
                'created_by'              => $this->session->userdata('user_id'),
                'status'                  => $status,
                'shipping'                => $shipping,
            ];
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->transfers_model->addTransfer($data, $products)) {
            $this->session->set_userdata('remove_tols', 1);
            $this->session->set_flashdata('message', lang('transfer_added'));
            admin_redirect('transfers');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['name'] = ['name' => 'name',
                'id'                      => 'name',
                'type'                    => 'text',
                'value'                   => $this->form_validation->set_value('name'),
            ];
            $this->data['quantity'] = ['name' => 'quantity',
                'id'                          => 'quantity',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('quantity'),
            ];

            $this->data['warehouses'] = $this->site->getAllWarehouses();
            //$this->data['tax_rates']  = $this->site->getAllTaxRates();
            $this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
            $this->data['rnumber']    = $this->site->getReference('to');

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('transfers')], ['link' => '#', 'page' => lang('transfer_by_csv')]];
            $meta = ['page_title' => lang('add_transfer_by_csv'), 'bc' => $bc];
            $this->page_construct('transfers/transfer_by_csv', $meta, $this->data);
        }
    }
    public function transfers_suggestions()
    {
        $this->sma->checkPermissions('index', true);
        $term         = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->transfers_model->getProductNames($sr, $warehouse_id);
        if ($rows) {
            $r = 0;
            foreach ($rows as $row) {
                $c                     = uniqid(mt_rand(), true);
                $option                = false;
                $row->quantity         = 0;
                $row->item_tax_method  = $row->tax_method;
                $row->base_quantity    = 1;
                $row->base_unit        = $row->unit;
                $row->base_unit_cost   = $row->cost;
                $row->unit             = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $row->qty              = 1;
                $row->discount         = '0';
                $row->expiry           = '';
                $row->quantity_balance = 0;
                $row->ordered_quantity = 0;
                $options               = $this->transfers_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->transfers_model->getProductOptionByID($option_id) : $options[0];
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt       = json_decode('{}');
                    $opt->cost = 0;
                    $option_id = false;
                }
                $row->option = $option_id;
                $pis         = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                if ($pis) {
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }
                if ($opt->cost != 0) {
                    $row->cost = $opt->cost;
                }
                $row->real_unit_cost = $row->cost;
                $units               = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate            = $this->site->getTaxRateByID($row->tax_rate);

                $pr[] = ['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'     => $row, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }
    public function transfer_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->transfers_model->deleteTransfer($id);
                    }
                    $this->session->set_flashdata('message', lang('transfers_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'combine') {
                    $html = $this->combine_pdf($_POST['val']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('transfers'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('from_warehouse'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('to_warehouse'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $tansfer = $this->transfers_model->getTransferByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($tansfer->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $tansfer->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $tansfer->from_warehouse);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $tansfer->to_warehouse);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $tansfer->grand_total);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $tansfer->status);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'tansfers_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_transfer_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function warehouse_stock($warehouse = null)
    {
        $this->sma->checkPermissions('warehouse_stock',true);

        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }

        $this->data['stock']        = $warehouse ? $this->reports_model->getWarehouseStockValue($warehouse) : $this->reports_model->getStockValue();
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse;
        $this->data['warehouse']    = $warehouse ? $this->site->getWarehouseByID($warehouse) : null;
        $this->data['totals']       = $this->reports_model->getWarehouseTotals($warehouse);
        $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('reports')]];
        $meta                       = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/warehouse_stock', $meta, $this->data);

    }
    public function quantity_alerts($warehouse_id = null)
    {
        $this->sma->checkPermissions('quantity_alerts',true);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $user                       = $this->site->getUser();
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse']    = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : null;
        }


        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('product_quantity_alerts')]];
        $meta = ['page_title' => lang('product_quantity_alerts'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/quantity_alerts', $meta, $this->data);
    }
    public function expiry_alerts($warehouse_id = null)
    {
        $this->sma->checkPermissions('expiry_alerts',true);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $user                       = $this->site->getUser();
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse']    = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : null;
        }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('product_expiry_alerts')]];
        $meta = ['page_title' => lang('product_expiry_alerts'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/expiry_alerts', $meta, $this->data);
    }

    public function products_report()
    {
        $this->sma->checkPermissions('products_report',true);
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

             $this->data['categories']          = $this->input->post('brId') ?  $this->site->getCategoryBybrId($this->input->post('brId')) :$this->site->getCategoryBybrId($this->session->userdata('brId'));
             $this->data['brands']              = $this->input->post('brId') ? $this->site->getBrandBybrId($this->input->post('brId')) : $this->site->getBrandBybrId($this->input->post('brId'));//$this->site->getAllBrands();
             $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));


            }else{
    $this->data['categories'] = $this->site->getAllCategories();
    $this->data['brands']     = $this->site->getAllBrands();
    $this->data['warehouses'] = $this->site->getAllWarehouses();
            }
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('products_report_proff_less')]];
        $meta = ['page_title' => lang('products_report_proff_less'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/products', $meta, $this->data);
    }


    public function product_motion_report()
    {
        $this->sma->checkPermissions('products_report',true);
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                 $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

                 $this->data['categories']          = $this->input->post('brId') ?  $this->site->getCategoryBybrId($this->input->post('brId')) :$this->site->getCategoryBybrId($this->session->userdata('brId'));//$this->site->getAllCategories();
                 $this->data['brands']              = $this->input->post('brId') ? $this->site->getBrandBybrId($this->input->post('brId')) : $this->site->getBrandBybrId($this->input->post('brId'));
                 $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));


                }else{
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
                }
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Product_motion_report')]];
        $meta = ['page_title' => lang('Product_motion_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/product_motion_report', $meta, $this->data);
    }


    public function adjustments_report($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments_report',true);
        // $this->sma->checkPermissions('adjustments',true);

        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

        $this->data['mainGroups'] = $this->site->getMainGroup();
        $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
        $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

         $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));
         $this->data['users']      = $this->input->post('brId') ? $this->site->getUsersBybrId($this->input->post('brId')) : $this->site->getUsersBybrId($this->session->userdata('brId'));


        }else{
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['users']      = $this->reports_model->getStaff();
        }
       // $this->data['warehouses'] = $this->site->getAllWarehouses();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('adjustments_report')]];
        $meta                = ['page_title' => lang('adjustments_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/adjustments', $meta, $this->data);
    }

    public function add_adjustment_count($count_id = null)
    {
        ini_set('memory_limit','2048M');
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->sma->clear_tags($this->input->post('note'));

			$jsonArr_product_id=json_decode($_POST['jsonArr_product_id']);
			$jsonArr_product_id= (array) $jsonArr_product_id;

			$product_id_arr = [];
			foreach ($jsonArr_product_id as $product_arr) {
				$product_id_arr[]=  $product_arr->value;

			}


			$jsonArr_quantity=json_decode($_POST['jsonArr_quantity']);
			$jsonArr_quantity= (array) $jsonArr_quantity;

			$quantity_arr = [];
			foreach ($jsonArr_quantity as $qty_arr) {
				$quantity_arr[]=  $qty_arr->value;

			}

			$jsonArr_type=json_decode($_POST['jsonArr_type']);
			$jsonArr_type= (array) $jsonArr_type;
			//$this->sma->print_arrays($product_id_arr,$quantity_arr,$jsonArr_type);


			$type_arr = [];
			foreach ($jsonArr_type as $c_arr) {
				$type_arr[]=  $c_arr->value;

			}






			//$this->sma->print_arrays($product_id_arr,$quantity_arr,$type_arr);

            $i = isset($product_id_arr) ? sizeof($product_id_arr) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $product_id_arr[$r];
                $type       = $type_arr[$r];
                $quantity   = $quantity_arr[$r];
                $serial     = '';//$_POST['serial'][$r];
				//$unit   = $_POST['unit'][$r];
                $variant    = null;//isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction' && !$count_id) {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

				$product     = $this->site->getProductByID($product_id);


                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'unit'     =>  $product->unit,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                ];
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                //krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => $this->input->post('count_id') ? $this->input->post('count_id') : null,
            ];

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

             //$this->sma->print_arrays($data, $products);
             //$this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment_count($data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/stock_counts');
        } else {

            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items       = $this->products_model->getStockCountItems($count_id);

                foreach ($items as $item) {

                    $c = sha1(uniqid(mt_rand(), true));
                    if ($item->counted != $item->expected) {
                        $product     = $this->site->getProductByID($item->product_id);
                        $row         = json_decode('{}');
                        $row->id     = $item->product_id;
                        $row->code   = $product->code;
                        $row->name   = $product->name;
                        $row->qty    = $item->counted - $item->expected;
                        $row->type   = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty    = $row->qty > 0 ? $row->qty : (0 - $row->qty);
                        $options     = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $row->unit  = $this->products_model->getUnitById($product->unit);
					    $ri          = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'options' => $options, ];
                        $c++;
                    }

                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : false;
            $this->data['warehouse_id']     = $count_id ? $stock_count->warehouse_id : false;
            $this->data['count_id']         = $count_id;
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses']       = $this->site->getAllWarehouses();
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment_count')]];
            $meta                           = ['page_title' => lang('add_adjustment_count'), 'bc' => $bc];
            //$this->sma->print_arrays($this->data['adjustment_items']);
			$this->page_construct('products/add_adjustment_count', $meta, $this->data);
        }
    }


    public function add_adjustment_store($count_id = null)
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
			/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/



            $note         = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $_POST['product_id'][$r];
                $type       = $_POST['type'][$r];
                $quantity   = $_POST['quantity'][$r];
                $cost   = $_POST['cost'][$r];
                $value   = $_POST['value'][$r];
                $unit   = $_POST['unit'][$r];
                $serial     = $_POST['serial'][$r];
                $reason   = $_POST['reason'][$r];
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;


				if (!$this->Settings->overselling && $type == 'subtraction' && !$count_id) {

					$product_details = $this->products_model->getProductByID($product_id);


                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'cost'     => $cost,
                    'value'     => $value,
                    'unit'     => $unit,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
					'reason'    => $reason,
                    'serial_no'    => $serial,
                ];
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => $this->input->post('count_id') ? $this->input->post('count_id') : null,
            ];

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment_store($data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/stock_counts');
        } else {
            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items       = $this->products_model->getStockCountItems($count_id);
                foreach ($items as $item) {
                    $c = sha1(uniqid(mt_rand(), true));
                    if ($item->counted != $item->expected) {
                        $product     = $this->site->getProductByID($item->product_id);
                        $row         = json_decode('{}');
                        $row->id     = $item->product_id;
                        $row->code   = $product->code;
                        $row->name   = $product->name;
                        $row->qty    = $item->counted - $item->expected;
                        $row->type   = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty    = $row->qty > 0 ? $row->qty : (0 - $row->qty);
                        $options     = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $row->cost   = $this->products_model->getCostById($item->product_id,$stock_count->warehouse_id);
						$row->value  = $item->cost;
						$row->reason = $item->reason;
						$row->unit   = $this->products_model->getUnitById($product->unit);
					    $ri          = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'options' => $options, ];
                        $c++;
                    }
                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : false;
            $this->data['warehouse_id']     = $count_id ? $stock_count->warehouse_id : false;
            $this->data['count_id']         = $count_id;
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses']       = $this->site->getAllWarehouses();
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('warehouse_store')]];
            $meta                           = ['page_title' => lang('warehouse_store'), 'bc' => $bc];
            $this->page_construct('products/add_adjustment_store', $meta, $this->data);
        }
    }
    public function warehouse_count($warehouse_id = null)
    {
        $this->sma->checkPermissions('warehouse_count',true);

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('warehouse_count')]];
        $meta                = ['page_title' => lang('warehouse_count'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/warehouse_count', $meta, $this->data);
    }


 public function getadjustments_count($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_adjustment') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_adjustment/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

		$delete_link = anchor('admin/products/view_count/$1', '<label class="label label-primary pointer">' . lang('details') . '</label>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');


        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('adjustments_count')}.count_id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('adjustments_count')
            ->join('warehouses', 'warehouses.id=adjustments_count.warehouse_id', 'left')
            ->join('users', 'users.id=adjustments_count.created_by', 'left')
            ->group_by('adjustments_count.id');
        if ($warehouse_id) {
            $this->datatables->where('adjustments_count.warehouse_id', $warehouse_id);
        }
        $this->datatables->add_column('Actions', "<div class='text-center'> " . $delete_link . '</div>', 'id');

        echo $this->datatables->generate();
    }
    public function units()
    {
        $this->sma->checkPermissions('units',true);
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('units')]];
        $meta = ['page_title' => lang('units'), 'bc' => $bc];
        $this->page_construct('products/units', $meta, $this->data);
    }

    public function categories()
    {
        $this->sma->checkPermissions('categories',true);
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('categories')]];
        $meta                = ['page_title' => lang('categories'), 'bc' => $bc];
        $this->page_construct('products/categories', $meta, $this->data);
    }

    public function reports()
    {
        //$this->sma->checkPermissions();
        $data['error']               = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->reports_model->getChartData();
        $this->data['stock']         = $this->reports_model->getStockValue();
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('reports')]];
        $meta                        = ['page_title' => lang('reports'), 'bc' => $bc];
        $this->page_construct('products/reports', $meta, $this->data);
    }

    public function products_item_report()
    {
        $this->sma->checkPermissions('products_item_report',true);
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

             $this->data['categories']          = $this->input->post('brId') ?  $this->site->getCategoryBybrId($this->input->post('brId')) :$this->site->getCategoryBybrId($this->session->userdata('brId'));
             $this->data['brands']              = $this->input->post('brId') ? $this->site->getBrandBybrId($this->input->post('brId')) : $this->site->getBrandBybrId($this->input->post('brId'));
             $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));


            }else{
    $this->data['categories'] = $this->site->getAllCategories();
    $this->data['brands']     = $this->site->getAllBrands();
    $this->data['warehouses'] = $this->site->getAllWarehouses();
            }
        if ($this->input->post('start_date')) {
            $dt = 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
        } else {
            $dt = 'Till ' . $this->input->post('end_date');
        }
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('products_report')]];
        $meta = ['page_title' => lang('products_report'), 'bc' => $bc];
        $this->data['content_report'] = $this->load->view($this->theme . 'products/reports', null,true);
		$this->page_construct('products/products_item', $meta, $this->data);
    }

    public function getTax_Rate($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->site->getTaxRateByID($id);
		$this->sma->send_json($row->rate);
    }

    public function add_category()
    {
        $this->sma->checkPermissions('add_category',true);
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|is_unique[categories.code]|required');
        //$this->form_validation->set_rules('name', lang('name'), 'required|min_length[3]');
        $this->form_validation->set_rules('name', lang('name'), 'required');

        //$this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]|alpha_dash');
        $this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]');
        $this->form_validation->set_rules('userfile', lang('category_image'), 'xss_clean');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        $hide_pos=0;
		if($this->input->post('hide_pos')==1){
			$hide_pos=1;

		}

		if ($this->form_validation->run() == true) {
            $isTobacco=0;
            if($this->input->post('isTobacco')==1){
                $isTobacco=1;

            }

            $data = [
                'name'        => $this->input->post('name'),
                'nameEn'        => $this->input->post('nameEn'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
                'parent_id'   => $this->input->post('parent'),
                'hide_pos'   => $hide_pos,
                'isTobacco'   => $isTobacco,
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
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image']     = $this->upload_path . $photo;
                    $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type']          = 'text';
                    $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                    $wm['quality']          = '100';
                    $wm['wm_font_size']     = '16';
                    $wm['wm_font_color']    = '999999';
                    $wm['wm_shadow_color']  = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding']       = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = null;
            }
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }
        } elseif ($this->input->post('add_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/categories');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCategory($data)) {
            $this->session->set_flashdata('message', lang('category_added'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['categories'] = $this->settings_model->getParentCategories();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'products/add_category', $this->data);
        }
    }

    public function getCategories()
    {
        $print_barcode = anchor('admin/products/print_barcodes/?category=$1', '<i class="fa fa-print"></i>', 'title="' . lang('print_barcodes') . '" class="tip"');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('categories')}.id as id, {$this->db->dbprefix('categories')}.image, {$this->db->dbprefix('categories')}.code, {$this->db->dbprefix('categories')}.name, {$this->db->dbprefix('categories')}.nameEn, {$this->db->dbprefix('categories')}.slug, c.name as parent", false)
            ->from('categories')
            ->join('categories c', 'c.id=categories.parent_id', 'left')
            ->group_by('categories.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a class=\"tip\" title='" . lang('Category_modifiers') . "' href='" . admin_url('products/category_modifiers/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-cogs\"></i></a> <a href='" . admin_url('products/edit_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_category') . "'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('products/categories_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_category') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

     public function category_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete_category',true);
                    foreach ($_POST['val'] as $id) {
                        $this->sma->checkPermissions('delete_category',true);
        if ($this->site->getSubCategories($id)) {
            $this->session->set_flashdata('error', lang('category_has_subcategory'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->site->checkCategories($id)) {
            $this->session->set_flashdata('error', lang('You_cannot_delete_this_Category_There_are_relevant_processes'));
            redirect($_SERVER['HTTP_REFERER']);
        }
                        $this->settings_model->deleteCategory($id);
                    }
                    $this->session->set_flashdata('message', lang('categories_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->sma->checkPermissions('category_xls',true);
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('slug'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('parent_category'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc              = $this->settings_model->getCategoryByID($id);
                        $parent_category = '';
                        if ($sc->parent_id) {
                            $pc              = $this->settings_model->getCategoryByID($sc->parent_id);
                            $parent_category = $pc->code;
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->slug);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sc->image);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $parent_category);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

     public function import_categories()
    {

		$this->sma->checkPermissions('add_category',true);
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products/categories');
                }
                $csv       = $this->upload->file_name;
                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles     = array_shift($arrResult);
                $updated    = '';
                $categories = $subcategories = [];

                foreach ($arrResult as $key => $value) {
                    $code  = trim($value[0]);
                    $name  = trim($value[1]);
					if($value[2]=="")
						$value[2]=$value[1];

                    $pcode = isset($value[4]) ? trim($value[4]) : null;
                    if ($code && $name && trim($value[2])) {
                        $category = [
                            'code'        => $code,
                            'name'        => $name,
                            'slug'        => isset($value[2]) ? trim($value[2]) : $code,
                            'image'       => isset($value[3]) ? trim($value[3]) : 'no_image.png',
                            'parent_id'   => $pcode,
                            'description' => isset($value[5]) ? trim($value[5]) : null,
                        ];
                        if (!empty($pcode) && ($pcategory = $this->settings_model->getCategoryByCode($pcode))) {
                            $category['parent_id'] = $pcategory->id;
                        }
                        if ($c = $this->settings_model->getCategoryByCode($code)) {
                            $updated .= '<p>' . lang('category_updated') . ' (' . $code . ')</p>';
                            $this->settings_model->updateCategory($c->id, $category);
                        } else {
                            if ($category['parent_id']) {
                                $subcategories[] = $category;
                            } else {
                                $categories[] = $category;
                            }
                        }
                    }
                }
            }

            //$this->sma->print_arrays($categories, $subcategories);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCategories($categories, $subcategories)) {
            $this->session->set_flashdata('message', lang('categories_added') . $updated);
            admin_redirect('products/categories');
        } else {
            if ((isset($categories) && empty($categories)) || (isset($subcategories) && empty($subcategories))) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_categories'));
                }
                admin_redirect('products/categories');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'products/import_categories', $this->data);
        }
    }


    public function getUnits()
    {
        $this->sma->checkPermissions('units',true);
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('units')}.id as id, {$this->db->dbprefix('units')}.code, {$this->db->dbprefix('units')}.name, b.name as base_unit, {$this->db->dbprefix('units')}.operator, {$this->db->dbprefix('units')}.operation_value", false)
            ->from('units')
            ->join('units b', 'b.id=units.base_unit', 'left')
            ->group_by('units.id')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('products/edit_unit/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a class=\"tip\" title='" . lang('Branches_list') . "' href='" . admin_url('products/units_details/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-home\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }

    public function unit_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete_unit',true);
                    foreach ($_POST['val'] as $id) {
                        if ($this->settings_model->getUnitChildren($id)) {
                            $this->session->set_flashdata('error', lang('unit_has_subunit'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }

                        if ($this->settings_model->getAllProductsUnitPricesCount($id)) {
                            $this->session->set_flashdata('error', lang('You_cannot_delete_this_unit_There_are_relevant_processes'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }

                        if($this->site->getAllProductsUnitCount($id)){
                            $this->session->set_flashdata('error', lang('You_cannot_delete_this_unit_There_are_relevant_processes'));
                            redirect($_SERVER['HTTP_REFERER']);

                        }else{
                            $this->settings_model->deleteUnit($id);
                        }
                    }
                    $this->session->set_flashdata('message', lang('units_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->sma->checkPermissions('units_xls',true);
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('base_unit'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('operator'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('operation_value'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $unit = $this->site->getUnitByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $unit->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $unit->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $unit->base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $unit->operator);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $unit->operation_value);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'units_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function add_unit()
    {
        $this->sma->checkPermissions('add_unit',true);
        $this->form_validation->set_rules('code', lang('unit_code'), 'trim|is_unique[units.code]|required');
        $this->form_validation->set_rules('name', lang('unit_name'), 'trim|required');
        if ($this->input->post('base_unit')) {
            $this->form_validation->set_rules('operator', lang('operator'), 'required');
            $this->form_validation->set_rules('operation_value', lang('operation_value'), 'trim|required|numeric');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'name'            => $this->input->post('name'),
                'code'            => $this->input->post('code'),
                'base_unit'       => $this->input->post('base_unit') ? $this->input->post('base_unit') : null,
                'operator'        => $this->input->post('base_unit') ? $this->input->post('operator') : null,
                'operation_value' => $this->input->post('operation_value') ? $this->input->post('operation_value') : null,
            ];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }
        } elseif ($this->input->post('add_unit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/units');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
            }
        if ($this->form_validation->run() == true && $this->settings_model->addUnit($data)) {
            $this->session->set_flashdata('message', lang('unit_added'));
			$ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

         //   admin_redirect('system_settings/units');
        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'products/add_unit', $this->data);
        }
    }

    public function delete_unit($id = null)
    {
        $this->sma->checkPermissions('delete_unit',true);
        if ($this->settings_model->getUnitChildren($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('unit_has_subunit')]);
        }
        if ($this->settings_model->getAllProductsUnitPricesCount($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_unit_There_are_relevant_processes')]);
        }
        if($this->site->getAllProductsUnitCount($id)){
            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_unit_There_are_relevant_processes')]);
        }else{
            if ($this->settings_model->deleteUnit($id)) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('unit_deleted')]);
            }
        }
    }

    public function edit_unit($id = null)
    {
        $this->sma->checkPermissions('edit_unit',true);
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');
        $unit_details = $this->site->getUnitByID($id);
        if ($this->input->post('code') != $unit_details->code) {
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[units.code]');
        }
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        if ($this->input->post('base_unit')) {
            $this->form_validation->set_rules('operator', lang('operator'), 'required');
            $this->form_validation->set_rules('operation_value', lang('operation_value'), 'trim|required|numeric');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'name'            => $this->input->post('name'),
                'code'            => $this->input->post('code'),
                'base_unit'       => $this->input->post('base_unit') ? $this->input->post('base_unit') : null,
                'operator'        => $this->input->post('base_unit') ? $this->input->post('operator') : null,
                'operation_value' => $this->input->post('operation_value') ? $this->input->post('operation_value') : null,
            ];
        } elseif ($this->input->post('edit_unit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/units');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateUnit($id, $data)) {
            $this->session->set_flashdata('message', lang('unit_updated'));
            admin_redirect('products/units');
        } else {
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                 $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
             }
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['unit']       = $unit_details;
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->load->view($this->theme . 'settings/edit_unit', $this->data);
        }
    }



    public function edit_category($id = null)
    {
        $this->sma->checkPermissions('edit_category',true);
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|required');
        $pr_details = $this->settings_model->getCategoryByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang('category_code'), 'required|is_unique[categories.code]');
        }
        //$this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
        $this->form_validation->set_rules('slug', lang('slug'), 'required');
        if ($this->input->post('slug') != $pr_details->slug) {
        //    $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash|is_unique[categories.slug]');
            $this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]');
        }
        //$this->form_validation->set_rules('name', lang('category_name'), 'required|min_length[3]');
        $this->form_validation->set_rules('name', lang('category_name'), 'required');
        $this->form_validation->set_rules('userfile', lang('category_image'), 'xss_clean');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        $hide_pos=0;
		if($this->input->post('hide_pos')==1){
			$hide_pos=1;

		}

		if ($this->form_validation->run() == true) {

            $isTobacco=0;
            if($this->input->post('isTobacco')==1){
                $isTobacco=1;

            }

            $data = [
                'name'        => $this->input->post('name'),
                'nameEn'        => $this->input->post('nameEn'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
                'parent_id'   => $this->input->post('parent'),
				'hide_pos'   => $hide_pos,
            	'isTobacco'   => $isTobacco,
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
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image']     = $this->upload_path . $photo;
                    $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type']          = 'text';
                    $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                    $wm['quality']          = '100';
                    $wm['wm_font_size']     = '16';
                    $wm['wm_font_color']    = '999999';
                    $wm['wm_shadow_color']  = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding']       = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = null;
            }
        } elseif ($this->input->post('edit_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/categories');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCategory($id, $data)) {
            $this->session->set_flashdata('message', lang('category_updated'));
        //    admin_redirect('system_settings/categories');
		    $ref = isset($_SERVER['HTTP_REFERER']) ? explode('?', $_SERVER['HTTP_REFERER']) : null;
            admin_redirect($ref[0]);

        } else {
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $this->data['mainGroups'] = $this->site->getMainGroup();
                $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
                 $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
             }
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['category']   = $this->settings_model->getCategoryByID($id);
            $this->data['categories'] = $this->settings_model->getParentCategories();
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_category', $this->data);
        }
    }

 public function delete_category($id = null)
    {
        $this->sma->checkPermissions('delete_category',true);
        if ($this->site->getSubCategories($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('category_has_subcategory')]);
        }

        if ($this->site->checkCategories($id)) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_Category_There_are_relevant_processes')]);
        }

        if ($this->settings_model->deleteCategory($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('category_deleted')]);
        }
    }

	public function dashboard()
    {
        if ($this->Settings->version == '2.3') {
            $this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
            admin_redirect('sync');
        }

        $warehouse_id = $this->input->get('warehouse') ? $this->input->get('warehouse') : null;
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }

        $this->data['error']     = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['warehouses']   = $this->site->getAllWarehouses();

        // Modern SaaS Products & Inventory Dashboard Analytics Payload
        $this->data['stats']                  = $this->products_model->getProductsDashboardStats($warehouse_id);
        $this->data['monthly_trends']         = $this->products_model->getInventoryMonthlyTrends(12);
        $this->data['top_selling']            = $this->products_model->getTopSellingProducts(5, $warehouse_id);
        $this->data['top_stock']              = $this->products_model->getTopStockProducts(5, $warehouse_id);
        $this->data['category_distribution']  = $this->products_model->getCategoryStockDistribution(6);
        $this->data['warehouse_distribution'] = $this->products_model->getWarehouseStockDistribution();
        $this->data['stock_alerts']           = $this->products_model->getProductStockAlerts(6, $warehouse_id);
        $this->data['recent_products']        = $this->products_model->getRecentProducts(8, $warehouse_id);

        // Backward compatibility support
        $this->data['sales']     = $this->db_model->getLatestSales();
        $this->data['quotes']    = $this->db_model->getLastestQuotes();
        $this->data['purchases'] = $this->db_model->getLatestPurchases();
        $this->data['transfers'] = $this->db_model->getLatestTransfers();
        $this->data['customers'] = $this->db_model->getLatestCustomers();
        $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
        $this->data['chatData']  = $this->db_model->getInventoryValueChartData();
        $this->data['stock']     = $warehouse_id ? $this->reports_model->getWarehouseStockValue($warehouse_id) : $this->reports_model->getStockValue();
        $this->data['total_products']   = $this->data['stats']['total_products'];
        $this->data['total_categories'] = $this->data['stats']['categories_count'];
        $this->data['total_warehouses'] = $this->data['stats']['warehouses_count'];
        $this->data['stock_value']      = $this->data['stats']['total_cost_value'];

        $bc                      = [['link' => '#', 'page' => lang('products_dashboard')]];
        $meta                    = ['page_title' => lang('products_dashboard'), 'bc' => $bc];
        $this->page_construct('products/dashboard', $meta, $this->data);
    }


	    public function brands()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('brands')]];
        $meta                = ['page_title' => lang('brands'), 'bc' => $bc];
        $this->page_construct('settings/brands', $meta, $this->data);
    }

	    public function setPrice_tt()
    {
        $products     = $this->products_model->getAlltt_2();
        foreach($products as $product)
        {

		$this->products_model->updatett_2($this->sma->formatDecimal($product->SellingPricewithTax),trim($product->ReferenceCode));
		//$this->sma->print_arrays($product->ReferenceCode);
		}

    }

	public function variants()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('variants')]];
        $meta = ['page_title' => lang('variants'), 'bc' => $bc];
        $this->page_construct('settings/variants', $meta, $this->data);
    }

	public function add_variant()
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|is_unique[variants.name]|required');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }
            //$this->sma->print_arrays($data);
        } elseif ($this->input->post('add_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/variants');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }
        if ($this->form_validation->run() == true && $this->settings_model->addVariant($data)) {
            $this->session->set_flashdata('message', lang('variant_added'));
            admin_redirect('products/variants');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_variant', $this->data);
        }
    }

	public function edit_variant($id = null)
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $tax_details = $this->settings_model->getVariantByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang('name'), 'required|is_unique[variants.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('edit_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/variants');
        }
        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
        }
        if ($this->form_validation->run() == true && $this->settings_model->updateVariant($id, $data)) {
            $this->session->set_flashdata('message', lang('variant_updated'));
            admin_redirect('products/variants');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['variant']  = $tax_details;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_variant', $this->data);
        }
    }

/*
    public function updateSlug(){
        $this->products_model->updateSlug($this->products_model->getAllProductsWithSlugNull());
        $this->sma->print_arrays($this->products_model->getAllProductsWithSlugNull());
    }
    */




    public function units_details($unit_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $unit_id = $this->input->get('id');
        }
        $unit = $this->products_model->getUnitById($unit_id);


         $this->form_validation->set_rules('units_details', lang('submit'), 'required');

        if ($this->form_validation->run('products/units_details') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }
             //$this->sma->print_arrays($_POST['br_option']);
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);




                if (!$unit_id) {


                    foreach ($this->site->getAllBaseUnits() as $unitArr) {
                        $additional_data         = [
                            'unit_id' => $unitArr->id,
                            'grpId'  => $branch_info->grpId,
                            'coId'  => $branch_info->coId,
                            'brId'  => $brId,

                        ];
                        $units_details_data[] = $additional_data;
                    }

                }else{
            $additional_data         = [
                'unit_id' => $unit_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $units_details_data[] = $additional_data;


        }
             }


             //$this->sma->print_arrays($units_details_data);


        } elseif ($this->input->post('units_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->products_model->addUnitsDetails($units_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            //admin_redirect('products/units');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['unit']  = $unit;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['unitBranches'] = $this->site->getUnitBranches($unit_id ? $unit_id : null);
            $this->load->view($this->theme . 'products/units_details', $this->data);
        }
    }



    public function categories_details($category_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $category_id = $this->input->get('id');
        }
        $category = $this->settings_model->getCategoryByID($category_id);

         $this->form_validation->set_rules('categories_details', lang('submit'), 'required');

        if ($this->form_validation->run('products/categories_details') == true) {
            //$this->sma->print_arrays($category);
            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }
            // $this->sma->print_arrays($_POST['br_option']);
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);


                if (!$category_id) {


                    foreach ($this->site->getAllCategories() as $cateArr) {
                        $additional_data         = [
                            'category_id' => $cateArr->id,
                            'grpId'  => $branch_info->grpId,
                            'coId'  => $branch_info->coId,
                            'brId'  => $brId,

                        ];
                        $categories_details_data[] = $additional_data;
                    }

                }else{


            $additional_data         = [
                'category_id' => $category_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $categories_details_data[] = $additional_data;
             }
            }



            //$this->sma->print_arrays($categories_details_data);

        } elseif ($this->input->post('categories_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->products_model->addCategoriesDetails($categories_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            //admin_redirect('products/categories');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['category']  = $category;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['categorieBranches'] = $this->site->getCategorieBranches($category_id ? $category_id : null);
           // $this->sma->print_arrays($this->data['categorieBranches']);
            $this->load->view($this->theme . 'products/categories_details', $this->data);
        }
    }


    public function products_details($product_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $product = $this->site->getProductByID($product_id);

         $this->form_validation->set_rules('products_details', lang('submit'), 'required');

        if ($this->form_validation->run('products/products_details') == true) {
            //$this->sma->print_arrays($category);
            if (empty($_POST['wh_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }

            foreach ($_POST['wh_option'] as $warehouse_id) {
                $warehouse_info=$this->site->getWarehouseByID($warehouse_id);

                if (!$product_id) {


                    foreach ($this->site->getWarehouseProductsBywarehouse_id($_POST['from_warehouse']) as $prodArr) {
                        $additional_data         = [
                            'product_id' => $prodArr->product_id,
                            'warehouse_id' => $warehouse_id,
                            'grpId'  => $warehouse_info->grpId,
                            'coId'  => $warehouse_info->coId,
                            'brId'  => $warehouse_info->brId,
                            'quantity'  => '0',
                            'avg_cost'  => '0',

                        ];
                        $products_details_data[] = $additional_data;
                    }

                }else{





            $additional_data         = [
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
                'grpId'  => $warehouse_info->grpId,
                'coId'  => $warehouse_info->coId,
                'brId'  => $warehouse_info->brId,
                'quantity'  => '0',
                'avg_cost'  => '0',

            ];
            $products_details_data[] = $additional_data;
             }
            }


             //$this->sma->print_arrays($products_details_data);


        } elseif ($this->input->post('products_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->products_model->addProductsToWarehouse($products_details_data)) {
            $this->session->set_flashdata('message', lang('product_updated'));
            redirect($_SERVER['HTTP_REFERER']);
            //admin_redirect('products');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['product']  = $product;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['warehousBranches'] = $this->site->getWarehousBranches($product_id);

            $this->load->view($this->theme . 'products/products_details', $this->data);
        }
    }

    public function brands_details($brand_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $brand_id = $this->input->get('id');
        }
        $brand = $this->site->getBrandById($brand_id);


         $this->form_validation->set_rules('brands_details', lang('submit'), 'required');

        if ($this->form_validation->run('products/brands_details') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }
             //$this->sma->print_arrays($_POST['br_option']);
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);

                if (!$brand_id) {


                    foreach ($this->site->getAllBrands() as $brandArr) {
                        $additional_data         = [
                            'brand_id' => $brandArr->id,
                            'grpId'  => $branch_info->grpId,
                            'coId'  => $branch_info->coId,
                            'brId'  => $brId,

                        ];
                        $brands_details_data[] = $additional_data;
                    }

                }else{

            $additional_data         = [
                'brand_id' => $brand_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $brands_details_data[] = $additional_data;
             }
            }

        } elseif ($this->input->post('brands_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->products_model->addBrandsDetails($brands_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            //admin_redirect('products/brands');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['brand']  = $brand;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['brandBranches'] = $this->site->getBrandBranches($brand_id ? $brand_id : null);
           //    $this->sma->print_arrays($this->data);
            $this->load->view($this->theme . 'products/brands_details', $this->data);
        }
    }


    public function variants_details($variant_id = null)
    {
        $this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $variant_id = $this->input->get('id');
        }
        $variant = $this->settings_model->getVariantByID($variant_id);


         $this->form_validation->set_rules('variants_details', lang('submit'), 'required');

        if ($this->form_validation->run('products/variants_details') == true) {

            if (empty($_POST['br_option'])) {
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
             }
             //$this->sma->print_arrays($_POST['br_option']);
            foreach ($_POST['br_option'] as $brId) {
                $branch_info=$this->site->getMainBranchesByID($brId);


                if (!$variant_id) {


                    foreach ($this->products_model->getAllVariants() as $variantArr) {
                        $additional_data         = [
                            'variant_id' => $variantArr->id,
                            'grpId'  => $branch_info->grpId,
                            'coId'  => $branch_info->coId,
                            'brId'  => $brId,

                        ];
                        $variants_details_data[] = $additional_data;
                    }

                }else{

            $additional_data         = [
                'variant_id' => $variant_id,
                'grpId'  => $branch_info->grpId,
                'coId'  => $branch_info->coId,
                'brId'  => $brId,

            ];
            $variants_details_data[] = $additional_data;
             }
            }

        } elseif ($this->input->post('variants_details')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->products_model->addVariantsDetails($variants_details_data)) {
            $this->session->set_flashdata('message', lang('Main_branch_updated'));
            //admin_redirect('products/variants');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['variant']  = $variant;
            $this->data['mainBranches'] = $this->site->getMainBranches();

            $this->data['variantBranches'] = $this->site->getVariantBranches($variant_id ? $variant_id : null);
           //    $this->sma->print_arrays($this->data);
            $this->load->view($this->theme . 'products/variants_details', $this->data);
        }
    }




        public function getProducts2($warehouse_id = null)
        {


            // // get all the product ids and loop through them
            // $this->db->select('id');
            // $this->db->from('products');
            // $query = $this->db->get();
            // $product_ids = $query->result_array();

            // foreach ($product_ids as $product_id) {

            //   //call calculateProductDataCompleteness($product_id);
            //   $this->products_model->calculateProductDataCompleteness($product_id['id']);

            // }

            // show php errors
            // ini_set('display_errors', 1);
            // ini_set('display_startup_errors', 1);
            // error_reporting(E_ALL);


            $this->sma->checkPermissions('index', true);
            $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : null;

            if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
                $user         = $this->site->getUser();
                $warehouse_id = $user->warehouse_id;
            }
            $detail_link = anchor('admin/products/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('product_details'));
            $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_product') . "</b>' data-content=\"<p>"
                . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . admin_url('products/delete/$1') . "'>"
                . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
                . lang('delete_product') . '</a>';
            $single_barcode = anchor('admin/products/print_barcodes/$1', '<i class="fa fa-print"></i> ' . lang('print_barcode_label'));
            // $single_label = anchor_popup('products/single_label/$1/' . ($warehouse_id ? $warehouse_id : ''), '<i class="fa fa-print"></i> ' . lang('print_label'), $this->popup_attributes);
            $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
                . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $detail_link . '</li>
                <li><a href="' . admin_url('products/add/$1') . '"><i class="fa fa-plus-square"></i> ' . lang('duplicate_product') . '</a></li>
                <li><a href="' . admin_url('products/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_product') . '</a></li>';
            if ($warehouse_id) {
                $action .= '<li><a href="' . admin_url('products/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                    . lang('set_rack') . '</a></li>';
            }
            $action .= '<li><a href="' . base_url() . 'assets/uploads/$2" data-type="image" data-toggle="lightbox"><i class="fa fa-file-photo-o"></i> '
                . lang('view_image') . '</a></li>
                <li>' . $single_barcode . '</li>
                <li class="divider"></li>
                <li>' . $delete_link . '</li>
                </ul>
            </div></div>';
            $this->load->library('datatables2');


            if ($warehouse_id) {
                $this->datatables2
                // ->select($this->db->dbprefix('products') . ".id as productid, {$this->db->dbprefix('products')}.image as image, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, cost as cost, price as price, COALESCE(wp.quantity, 0) as quantity, {$this->db->dbprefix('units')}.code as unit, wp.rack as rack, alert_quantity, {$this->db->dbprefix('products')}.data_completness as data_completness", false)
                ->select([$this->db->dbprefix('categories').'.name as cname' , $this->db->dbprefix('products') . ".id", $this->db->dbprefix('products') . ".code", $this->db->dbprefix('products') . ".name", $this->db->dbprefix('products') . ".name_en", $this->db->dbprefix('products') . ".unit", $this->db->dbprefix('products') . ".cost", $this->db->dbprefix('products') . ".price", $this->db->dbprefix('products') . ".alert_quantity", $this->db->dbprefix('products') . ".image", $this->db->dbprefix('products') . ".category_id", $this->db->dbprefix('products') . ".subcategory_id", $this->db->dbprefix('products') . ".quantity", $this->db->dbprefix('products') . ".tax_rate" ], false)
                ->from('products');
                // order by id desc
                // $this->datatables->order_by('id', 'desc');

                if ($this->Settings->display_all_products) {
                    $this->datatables2->join("( SELECT product_id, quantity, rack from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left');
                } else {
                    $this->datatables2->join('warehouses_products wp', 'products.id=wp.product_id', 'left')
                    ->where('wp.warehouse_id', $warehouse_id)
                    ->where('wp.quantity !=', 0);
                }
                $this->datatables2->join('categories', 'products.category_id=categories.id', 'left')
                ->join('units', 'products.unit=units.id', 'left')
                ->join('brands', 'products.brand=brands.id', 'left');
            // ->group_by("products.id");
            } else {
                $this->datatables2
                // ->select($this->db->dbprefix('products') . ".id as productid, {$this->db->dbprefix('products')}.image as image, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, cost as cost, price as price, COALESCE(quantity, 0) as quantity, {$this->db->dbprefix('units')}.code as unit, '' as rack, alert_quantity, {$this->db->dbprefix('products')}.data_completness as data_completness", false)
                ->select([$this->db->dbprefix('categories').'.name as cname' , $this->db->dbprefix('products') . ".id", $this->db->dbprefix('products') . ".code", $this->db->dbprefix('products') . ".name", $this->db->dbprefix('products') . ".name_en", $this->db->dbprefix('products') . ".unit", $this->db->dbprefix('products') . ".cost", $this->db->dbprefix('products') . ".price", $this->db->dbprefix('products') . ".alert_quantity", $this->db->dbprefix('products') . ".image", $this->db->dbprefix('products') . ".category_id", $this->db->dbprefix('products') . ".subcategory_id", $this->db->dbprefix('products') . ".quantity", $this->db->dbprefix('products') . ".tax_rate" ], false)

                ->from('products')
                    ->join('categories', 'products.category_id=categories.id', 'left')
                    ->join('units', 'products.unit=units.id', 'left')
                    ->join('brands', 'products.brand=brands.id', 'left')
                    ->group_by('products.id');
            }
            if (!$this->Owner && !$this->Admin) {
                if (!$this->session->userdata('show_cost')) {
                    $this->datatables2->unset_column('cost');
                }
                if (!$this->session->userdata('show_price')) {
                    $this->datatables2->unset_column('price');
                }
            }


            $this->datatables2->select( $this->db->dbprefix('products') .'.product_details');
            $this->datatables2->select( $this->db->dbprefix('products') .'.product_details_en');
            $this->datatables2->select( $this->db->dbprefix('products') .'.type');
            $this->datatables2->select( $this->db->dbprefix('products') .'.type2');
            $this->datatables2->select( $this->db->dbprefix('products') .'.promotion');
            $this->datatables2->select( $this->db->dbprefix('products') .'.promo_price');
            $this->datatables2->select( $this->db->dbprefix('products') .'.start_date');
            $this->datatables2->select( $this->db->dbprefix('products') .'.end_date');
            $this->datatables2->select( $this->db->dbprefix('products') .'.sale_unit');
            $this->datatables2->select( $this->db->dbprefix('products') .'.brand');
            $this->datatables2->select( $this->db->dbprefix('products') .'.slug');
            $this->datatables2->select( $this->db->dbprefix('products') .'.featured');
            $this->datatables2->select( $this->db->dbprefix('products') .'.weight');
            $this->datatables2->select( $this->db->dbprefix('products') .'.views');
            $this->datatables2->select( $this->db->dbprefix('products') .'.product_number');
            $this->datatables2->select( $this->db->dbprefix('products') .'.expiry');
            $this->datatables2->select( $this->db->dbprefix('products') .'.currency_code');
            $this->datatables2->select( $this->db->dbprefix('products') .'.live_status');
            $this->datatables2->select( $this->db->dbprefix('products') .'.date_added');
            $this->datatables2->select( $this->db->dbprefix('products') .'.data_completness');
            if ($supplier) {

                $this->datatables2->where('supplier1', $supplier)
                ->or_where('supplier2', $supplier)
                ->or_where('supplier3', $supplier)
                ->or_where('supplier4', $supplier)
                ->or_where('supplier5', $supplier);
            }
            // $this->datatables2->add_column('Actions', $action, 'productid, image, code, name');



                        // order by live_status asc ,date_added desc
            $this->datatables2->order_by('live_status', 'asc');
            $this->datatables2->order_by('products.id', 'desc');


            echo $this->datatables2->generate() ;die;
        }

        public function add_auto_save($id = null,$isShop = 'shop')
        {


            $this->sma->checkPermissions();
            $this->load->helper('security');
            $warehouses = $this->site->getAllWarehouses();
            // $this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');


             // if type isset and not null
            if (isset($_POST['type']) && $_POST['type'] != null) {
                if ($this->input->post('type') == 'standard') {
                    $this->form_validation->set_rules('cost', lang('product_cost'), 'required');
                    $this->form_validation->set_rules('unit', lang('product_unit'), 'required');
                }
            }

            if (isset($_POST['code']) && $_POST['code'] != null) {
                $this->form_validation->set_rules('code', lang('product_code'), 'is_unique[products.code]|alpha_dash');
            }
            if (SHOP) {
                // $this->form_validation->set_rules('name_en', "Name Englisb", 'required');
            }


            if (isset($_POST['weight']) && $_POST['weight'] != null) {
                $this->form_validation->set_rules('weight', lang('weight'), 'numeric');
            }

            if (isset($_POST['product_image']) && $_POST['product_image'] != null) {
                $this->form_validation->set_rules('product_image', lang('product_image'), 'xss_clean');
            }
            if (isset($_POST['digital_file']) && $_POST['digital_file'] != null) {
                $this->form_validation->set_rules('digital_file', lang('digital_file'), 'xss_clean');
            }
            if (isset($_POST['userfile']) && $_POST['userfile'] != null) {
                $this->form_validation->set_rules('userfile', lang('product_gallery_images'), 'xss_clean');
            }


            // if ($this->form_validation->run() == true) {
            if (isset($_POST['tax_rate']) && $_POST['tax_rate'] != null) {
                $tax_rate = $this->input->post('tax_rate') ? $this->site->getTaxRateByID($this->input->post('tax_rate')) : null;
            }

            if (isset($_POST['name_en']) && $_POST['name_en'] != null) {
                // create a slug from the field name_en
               // $slug = url_title($this->input->post('name_en'), 'dash', true);
            }else {
                $this->load->helper('string');

                // generate a random slug if name_en is not set from the date and time and random number
                //$slug = url_title(date('YmdHis') . random_string('alnum', 4), 'dash', true);

            }
                $data     = [
                    'id'              => $this->input->post('id'),
                    'code'              => $this->input->post('code'),
                    'barcode_symbology' => 'code128',
                    'name'              => $this->input->post('name'),
                    'name_en'              => $this->input->post('name_en'),
                    'type'              => $this->input->post('type'),
                    'brand'             => $this->input->post('brand'),
                    'category_id'       => $this->input->post('category'),
                    'subcategory_id'    => $this->input->post('subcategory') ? $this->input->post('subcategory') : null,
                    'cost'              => $this->sma->formatDecimal($this->input->post('cost')),
                    'price'             => $this->sma->formatDecimal($this->input->post('price')),
                    'unit'              => $this->input->post('unit'),
                    'sale_unit'         => $this->input->post('unit'),
                    'purchase_unit'     => $this->input->post('unit'),
                    'tax_rate'          => $this->input->post('tax_rate'),
                    'tax_method'        => $this->input->post('tax_method'),
                    'alert_quantity'    => $this->input->post('alert_quantity'),
                    'track_quantity'    => $this->input->post('track_quantity') ? $this->input->post('track_quantity') : '0',
                    'details'           => $this->input->post('details'),
                    'product_details'   => $this->input->post('product_details'),
                    'supplier1'         => $this->input->post('supplier'),
                    'supplier1price'    => $this->sma->formatDecimal($this->input->post('supplier_price')),
                    'supplier2'         => $this->input->post('supplier_2'),
                    'supplier2price'    => $this->sma->formatDecimal($this->input->post('supplier_2_price')),
                    'supplier3'         => $this->input->post('supplier_3'),
                    'supplier3price'    => $this->sma->formatDecimal($this->input->post('supplier_3_price')),
                    'supplier4'         => $this->input->post('supplier_4'),
                    'supplier4price'    => $this->sma->formatDecimal($this->input->post('supplier_4_price')),
                    'supplier5'         => $this->input->post('supplier_5'),
                    'supplier5price'    => $this->sma->formatDecimal($this->input->post('supplier_5_price')),
                    'cf1'               => $this->input->post('cf1'),
                    'cf2'               => $this->input->post('cf2'),
                    'cf3'               => $this->input->post('cf3'),
                    'cf4'               => $this->input->post('cf4'),
                    'cf5'               => $this->input->post('cf5'),
                    'cf6'               => $this->input->post('cf6'),
                    'promotion'         => $this->input->post('promotion'),
                    'promo_price'       => $this->sma->formatDecimal($this->input->post('promo_price')),
                    'start_date'        => $this->input->post('start_date') ? $this->sma->fsd($this->input->post('start_date')) : null,
                    'end_date'          => $this->input->post('end_date') ? $this->sma->fsd($this->input->post('end_date')) : null,
                    'supplier1_part_no' => $this->input->post('supplier_part_no'),
                    'supplier2_part_no' => $this->input->post('supplier_2_part_no'),
                    'supplier3_part_no' => $this->input->post('supplier_3_part_no'),
                    'supplier4_part_no' => $this->input->post('supplier_4_part_no'),
                    'supplier5_part_no' => $this->input->post('supplier_5_part_no'),
                    'file'              => $this->input->post('file_link'),

                    'slug'              => $this->sma->slug($this->input->post('name')),
                    'weight'            => $this->input->post('weight'),
                    'featured'          => $this->input->post('featured'),
                    'hsn_code'          => $this->input->post('hsn_code'),
                    'second_name'       => $this->input->post('second_name'),
                    'hide_pos'          => 1,
                    'hide'             => 1
                ];

                // loop through all post fields and remove all of the fields in the $data array that are not in the $_POST array
                foreach ($this->input->post() as $key => $value) {
                    if (!array_key_exists($key, $data)) {
                        unset($data[$key]);
                    }
                }

                // loop through all $data fields and remove all of the fields that are empty
                foreach ($data as $key => $value) {
                    if (empty($value)) {
                        unset($data[$key]);
                    }
                }




                $warehouse_qty      = null;
                $product_attributes = null;

                $this->load->library('upload');

                if (isset($_POST['type']) && $_POST['type'] != null) {
                if ($this->input->post('type') == 'standard') {
                    $wh_total_quantity = 0;
                    $pv_total_quantity = 0;
                    for ($s = 2; $s > 5; $s++) {
                        $data['suppliers' . $s]           = $this->input->post('supplier_' . $s);
                        $data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                    }
                    foreach ($warehouses as $warehouse) {
                        if ($this->input->post('wh_qty_' . $warehouse->id)) {
                            $warehouse_qty[] = [
                                'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                                'quantity'     => $this->input->post('wh_qty_' . $warehouse->id),
                                'rack'         => $this->input->post('rack_' . $warehouse->id) ? $this->input->post('rack_' . $warehouse->id) : null,
                            ];
                            $wh_total_quantity += $this->input->post('wh_qty_' . $warehouse->id);
                        }
                    }

                    if ($this->input->post('attributes')) {
                        $a = sizeof($_POST['attr_name']);
                        for ($r = 0; $r <= $a; $r++) {
                            if (isset($_POST['attr_name'][$r])) {
                                $product_attributes[] = [
                                    'name'         => $_POST['attr_name'][$r],
                                    'warehouse_id' => $_POST['attr_warehouse'][$r],
                                    'quantity'     => $_POST['attr_quantity'][$r],
                                    'price'        => $_POST['attr_price'][$r],
                                ];
                                $pv_total_quantity += $_POST['attr_quantity'][$r];
                            }
                        }
                    } else {
                        $product_attributes = null;
                    }

                    if ($wh_total_quantity != $pv_total_quantity && $pv_total_quantity != 0) {
                        $this->form_validation->set_rules('wh_pr_qty_issue', 'wh_pr_qty_issue', 'required');
                        $this->form_validation->set_message('required', lang('wh_pr_qty_issue'));
                    }
                }

                if ($this->input->post('type') == 'service') {
                    $data['track_quantity'] = 0;
                } elseif ($this->input->post('type') == 'combo') {
                    $total_price = 0;
                    $c           = sizeof($_POST['combo_item_code']) - 1;
                    for ($r = 0; $r <= $c; $r++) {
                        if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                            $items[] = [
                                'item_code'  => $_POST['combo_item_code'][$r],
                                'quantity'   => $_POST['combo_item_quantity'][$r],
                                'unit_price' => $_POST['combo_item_price'][$r],
                            ];
                        }
                        $total_price += $_POST['combo_item_price'][$r] * $_POST['combo_item_quantity'][$r];
                    }
                    if ($this->sma->formatDecimal($total_price) != $this->sma->formatDecimal($this->input->post('price'))) {
                        $this->form_validation->set_rules('combo_price', 'combo_price', 'required');
                        $this->form_validation->set_message('required', lang('pprice_not_match_ciprice'));
                    }
                    $data['track_quantity'] = 0;
                } elseif ($this->input->post('type') == 'digital') {
                    if ($_FILES['digital_file']['size'] > 0) {
                        $config['upload_path']   = $this->digital_upload_path;
                        $config['allowed_types'] = $this->digital_file_types;
                        $config['max_size']      = $this->allowed_file_size;
                        $config['overwrite']     = false;
                        $config['encrypt_name']  = true;
                        $config['max_filename']  = 25;
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('digital_file')) {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            $isShop = $isShop != '' ? '0/' .  $isShop : '';
                            admin_redirect('products/add/' . $isShop);
                        }
                        $file         = $this->upload->file_name;
                        $data['file'] = $file;
                    } else {
                        if (!$this->input->post('file_link')) {
                            $this->form_validation->set_rules('digital_file', lang('digital_file'), 'required');
                        }
                    }
                    $config                 = null;
                    $data['track_quantity'] = 0;
                }

                if (!isset($items)) {
                    $items = null;
                }
            }
            if ($_FILES['product_image']['size'] > 0) {
                    $config['upload_path']   = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size']      = $this->allowed_file_size;
                    $config['max_width']     = $this->Settings->iwidth;
                    $config['max_height']    = $this->Settings->iheight;
                    $config['overwrite']     = false;
                    $config['max_filename']  = 25;
                    $config['encrypt_name']  = true;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('product_image')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect('products/add');
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
                    if ($this->Settings->watermark) {
                        $this->image_lib->clear();
                        $wm['source_image']     = $this->upload_path . $photo;
                        $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                        $wm['wm_type']          = 'text';
                        $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                        $wm['quality']          = '100';
                        $wm['wm_font_size']     = '16';
                        $wm['wm_font_color']    = '999999';
                        $wm['wm_shadow_color']  = 'CCCCCC';
                        $wm['wm_vrt_alignment'] = 'top';
                        $wm['wm_hor_alignment'] = 'left';
                        $wm['wm_padding']       = '10';
                        $this->image_lib->initialize($wm);
                        $this->image_lib->watermark();
                    }
                    $this->image_lib->clear();
                    $config = null;
                }

                if ($_FILES['userfile']['name'][0] != '') {
                    $config['upload_path']   = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size']      = $this->allowed_file_size;
                    $config['max_width']     = $this->Settings->iwidth;
                    $config['max_height']    = $this->Settings->iheight;
                    $config['overwrite']     = false;
                    $config['encrypt_name']  = true;
                    $config['max_filename']  = 25;
                    $files                   = $_FILES;
                    $cpt                     = count($_FILES['userfile']['name']);
                    for ($i = 0; $i < $cpt; $i++) {
                        $_FILES['userfile']['name']     = $files['userfile']['name'][$i];
                        $_FILES['userfile']['type']     = $files['userfile']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $files['userfile']['error'][$i];
                        $_FILES['userfile']['size']     = $files['userfile']['size'][$i];

                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload()) {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);


                            admin_redirect('products/add');
                        } else {
                            $pho = $this->upload->file_name;

                            $photos[] = $pho;

                            $this->load->library('image_lib');
                            $config['image_library']  = 'gd2';
                            $config['source_image']   = $this->upload_path . $pho;
                            $config['new_image']      = $this->thumbs_path . $pho;
                            $config['maintain_ratio'] = true;
                            $config['width']          = $this->Settings->twidth;
                            $config['height']         = $this->Settings->theight;

                            $this->image_lib->initialize($config);

                            if (!$this->image_lib->resize()) {
                                echo $this->image_lib->display_errors();
                            }

                            if ($this->Settings->watermark) {
                                $this->image_lib->clear();
                                $wm['source_image']     = $this->upload_path . $pho;
                                $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                                $wm['wm_type']          = 'text';
                                $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                                $wm['quality']          = '100';
                                $wm['wm_font_size']     = '16';
                                $wm['wm_font_color']    = '999999';
                                $wm['wm_shadow_color']  = 'CCCCCC';
                                $wm['wm_vrt_alignment'] = 'top';
                                $wm['wm_hor_alignment'] = 'left';
                                $wm['wm_padding']       = '10';
                                $this->image_lib->initialize($wm);
                                $this->image_lib->watermark();
                            }
                            $this->image_lib->clear();
                        }
                    }
                    $config = null;
                } else {
                    $photos = null;
                }
                $data['quantity'] = $wh_total_quantity ?? 0;
                // $this->sma->print_arrays($data, $warehouse_qty, $product_attributes);
            // }

           $itemId =  $this->products_model->addProduct_autoSave($data, $items, $warehouse_qty, $product_attributes, $photos);
                // last query
                // echo $this->db->last_query();
                // exit;
           if($itemId ){
           // $this->products_model->updateProductSlug($itemId);
            // output ajax response with item id
            echo json_encode(array('status'=> 'success' , 'itemId' => $itemId));
            exit;
           }
              echo json_encode(array('status'=> 'error' , 'itemId' => '0'));
              exit;
        }


        /* New shop fuunctions */

        public function Oldadd_mini($id = null)
        {
            $this->sma->checkPermissions();
            $this->load->helper('security');
            $warehouses = $this->site->getAllWarehouses();
            $this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');

            $this->form_validation->set_rules('code', lang('product_code'), 'required');

            $this->form_validation->set_rules('code', lang('product_code'), 'is_unique[products.code]|alpha_dash');

            $this->form_validation->set_rules('name_en', lang('name_english'), 'required');

            // $this->form_validation->set_rules('weight', lang('weight'), 'numeric');
            $this->form_validation->set_rules('product_image', lang('product_image'), 'xss_clean');
            $this->form_validation->set_rules('digital_file', lang('digital_file'), 'xss_clean');
            $this->form_validation->set_rules('userfile', lang('product_gallery_images'), 'xss_clean');


            if ($this->form_validation->run() == true) {

                // $tax_rate = $this->input->post('tax_rate') ? $this->site->getTaxRateByID($this->input->post('tax_rate')) : null;
               // create a slug from the field name_en
                $slug = url_title($this->input->post('name_en'), 'dash', true);

                // check if the slug already exists in the database
                $slug = $this->products_model->increment_slug($slug);
                $codes = $this->products_model->countCodes($this->input->post('code'));

                if($codes > 0){
                    $this->session->set_flashdata('error', lang("code_already_exist"));
                     // json error lang("code_already_exist")
                    echo json_encode(array('error' => lang("code_already_exist")));
                   exit;
                }

                $data     = [
                    'code'              => $this->input->post('code'),
                    'barcode_symbology' => $this->input->post('barcode_symbology'),
                    'name'              => $this->input->post('name'),
                    'name_en'              => $this->input->post('name_en'),
                    'type'              => $this->input->post('type'),
                     'type2'              => $this->input->post('type'),
                    'brand'             => $this->input->post('brand'),
                    'category_id'       => $this->input->post('category'),

                    'subcategory_id'    => $this->input->post('subcategory') ? $this->input->post('subcategory') : null,
                    'cost'              => 0,
                    'price'             => $this->sma->formatDecimal(10),

                    'product_details_en'           => $this->input->post('desc_en'),
                    'product_details'   => $this->input->post('desc'),


                    'slug'              => $slug,

                    'hide'              => $this->input->post('hide') ? $this->input->post('hide') : 0,
                ];
                $warehouse_qty      = null;
                $product_attributes = null;
                $this->load->library('upload');
                if ($this->input->post('type') == 'standard') {
                    $wh_total_quantity = 0;
                    $pv_total_quantity = 0;
                    for ($s = 2; $s > 5; $s++) {
                        $data['suppliers' . $s]           = $this->input->post('supplier_' . $s);
                        $data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                    }
                    foreach ($warehouses as $warehouse) {
                        if ($this->input->post('wh_qty_' . $warehouse->id)) {
                            $warehouse_qty[] = [
                                'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                                'quantity'     => $this->input->post('wh_qty_' . $warehouse->id),
                                'rack'         => $this->input->post('rack_' . $warehouse->id) ? $this->input->post('rack_' . $warehouse->id) : null,
                            ];
                            $wh_total_quantity += $this->input->post('wh_qty_' . $warehouse->id);
                        }
                    }

                    if ($this->input->post('attributes')) {
                        $a = sizeof($_POST['attr_name']);
                        for ($r = 0; $r <= $a; $r++) {
                            if (isset($_POST['attr_name'][$r])) {
                                $product_attributes[] = [
                                    'name'         => $_POST['attr_name'][$r],
                                    'warehouse_id' => $_POST['attr_warehouse'][$r],
                                    'quantity'     => $_POST['attr_quantity'][$r],
                                    'price'        => $_POST['attr_price'][$r],
                                ];
                                $pv_total_quantity += $_POST['attr_quantity'][$r];
                            }
                        }
                    } else {
                        $product_attributes = null;
                    }

                    if ($wh_total_quantity != $pv_total_quantity && $pv_total_quantity != 0) {
                        $this->form_validation->set_rules('wh_pr_qty_issue', 'wh_pr_qty_issue', 'required');
                        $this->form_validation->set_message('required', lang('wh_pr_qty_issue'));
                    }
                }

                if ($this->input->post('type') == 'service') {
                    $data['track_quantity'] = 0;
                } elseif ($this->input->post('type') == 'combo') {
                    $total_price = 0;
                    $c           = sizeof($_POST['combo_item_code']) - 1;
                    for ($r = 0; $r <= $c; $r++) {
                        if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                            $items[] = [
                                'item_code'  => $_POST['combo_item_code'][$r],
                                'quantity'   => $_POST['combo_item_quantity'][$r],
                                'unit_price' => $_POST['combo_item_price'][$r],
                            ];
                        }
                        $total_price += $_POST['combo_item_price'][$r] * $_POST['combo_item_quantity'][$r];
                    }
                    if ($this->sma->formatDecimal($total_price) != $this->sma->formatDecimal($this->input->post('price'))) {
                        $this->form_validation->set_rules('combo_price', 'combo_price', 'required');
                        $this->form_validation->set_message('required', lang('pprice_not_match_ciprice'));
                    }
                    $data['track_quantity'] = 0;
                } elseif ($this->input->post('type') == 'digital') {
                    if ($_FILES['digital_file']['size'] > 0) {
                        $config['upload_path']   = $this->digital_upload_path;
                        $config['allowed_types'] = $this->digital_file_types;
                        $config['max_size']      = $this->allowed_file_size;
                        $config['overwrite']     = false;
                        $config['encrypt_name']  = true;
                        $config['max_filename']  = 25;
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('digital_file')) {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            admin_redirect('products/index/0/shop');
                        }
                        $file         = $this->upload->file_name;
                        $data['file'] = $file;
                    } else {
                        if (!$this->input->post('file_link')) {
                            $this->form_validation->set_rules('digital_file', lang('digital_file'), 'required');
                        }
                    }
                    $config                 = null;
                    $data['track_quantity'] = 0;
                }
                if (!isset($items)) {
                    $items = null;
                }
                if ($_FILES['product_image']['size'] > 0) {
                    $config['upload_path']   = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size']      = $this->allowed_file_size;
                    $config['max_width']     = $this->Settings->iwidth;
                    $config['max_height']    = $this->Settings->iheight;
                    $config['overwrite']     = false;
                    $config['max_filename']  = 25;
                    $config['encrypt_name']  = true;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('product_image')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect('products/add');
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
                    if ($this->Settings->watermark) {
                        $this->image_lib->clear();
                        $wm['source_image']     = $this->upload_path . $photo;
                        $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                        $wm['wm_type']          = 'text';
                        $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                        $wm['quality']          = '100';
                        $wm['wm_font_size']     = '16';
                        $wm['wm_font_color']    = '999999';
                        $wm['wm_shadow_color']  = 'CCCCCC';
                        $wm['wm_vrt_alignment'] = 'top';
                        $wm['wm_hor_alignment'] = 'left';
                        $wm['wm_padding']       = '10';
                        $this->image_lib->initialize($wm);
                        $this->image_lib->watermark();
                    }
                    $this->image_lib->clear();
                    $config = null;
                }

                if ($_FILES['userfile']['name'][0] != '') {
                    $config['upload_path']   = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size']      = $this->allowed_file_size;
                    $config['max_width']     = $this->Settings->iwidth;
                    $config['max_height']    = $this->Settings->iheight;
                    $config['overwrite']     = false;
                    $config['encrypt_name']  = true;
                    $config['max_filename']  = 25;
                    $files                   = $_FILES;
                    $cpt                     = count($_FILES['userfile']['name']);
                    for ($i = 0; $i < $cpt; $i++) {
                        $_FILES['userfile']['name']     = $files['userfile']['name'][$i];
                        $_FILES['userfile']['type']     = $files['userfile']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $files['userfile']['error'][$i];
                        $_FILES['userfile']['size']     = $files['userfile']['size'][$i];

                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload()) {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);


                            // admin_redirect('products/add');

                            // output error to the ajax request
                            echo json_encode(array('error' => $error));
                            exit;

                        } else {
                            $pho = $this->upload->file_name;

                            $photos[] = $pho;

                            $this->load->library('image_lib');
                            $config['image_library']  = 'gd2';
                            $config['source_image']   = $this->upload_path . $pho;
                            $config['new_image']      = $this->thumbs_path . $pho;
                            $config['maintain_ratio'] = true;
                            $config['width']          = $this->Settings->twidth;
                            $config['height']         = $this->Settings->theight;

                            $this->image_lib->initialize($config);

                            if (!$this->image_lib->resize()) {
                                echo $this->image_lib->display_errors();
                            }

                            if ($this->Settings->watermark) {
                                $this->image_lib->clear();
                                $wm['source_image']     = $this->upload_path . $pho;
                                $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                                $wm['wm_type']          = 'text';
                                $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                                $wm['quality']          = '100';
                                $wm['wm_font_size']     = '16';
                                $wm['wm_font_color']    = '999999';
                                $wm['wm_shadow_color']  = 'CCCCCC';
                                $wm['wm_vrt_alignment'] = 'top';
                                $wm['wm_hor_alignment'] = 'left';
                                $wm['wm_padding']       = '10';
                                $this->image_lib->initialize($wm);
                                $this->image_lib->watermark();
                            }

                            $this->image_lib->clear();
                        }
                    }
                    $config = null;
                } else {
                    $photos = null;
                }
                $data['quantity'] = $wh_total_quantity ?? 0;
                // $this->sma->print_arrays($data, $warehouse_qty, $product_attributes);
            }
            $is_errors = $this->form_validation->run();

            $errors = $this->form_validation->error_array();

            $product_id =  $this->products_model->addProduct($data, $items, $warehouse_qty, $product_attributes, $photos);

            if ($product_id) {
                $this->session->set_flashdata('message', lang('product_added'));
                // output success to the ajax request
                echo json_encode(array('success' => true , 'product_id' => $product_id));
                exit;

            } else {
                $this->session->set_flashdata('error', lang('error_adding_product'));
                // admin_redirect('products/add');
                // output error to the ajax request
                echo json_encode(array('error' => lang('error_adding_product')));
                exit;
            }
        }

        public function add_mini($id = null)
    {

        // show php errors
        // error_reporting(E_ALL);
        // $this->sma->checkPermissions();
        // $this->load->helper('security');

        $warehouses = $this->site->getAllWarehouses();

        $this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('code', lang('product_code'), 'is_unique[products.code]');
        $this->form_validation->set_rules('name_en', lang('name_english'), 'required');
        $this->form_validation->set_rules('product_image', lang('product_image'), 'xss_clean');
        $this->form_validation->set_rules('digital_file', lang('digital_file'), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang('product_gallery_images'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            //$this->sma->print_arrays($data);
            //$slug = url_title($this->input->post('name_en'), 'dash', true);
            //$slug = $this->products_model->increment_slug($slug);

            $data = array(
                'code' => $this->input->post('code'),
                // 'barcode_symbology' => $this->input->post('barcode_symbology'),
                'name' => $this->input->post('name'),
                'name_en' => $this->input->post('name_en'),
                'type' => $this->input->post('type'),
                'type2' => $this->input->post('type'),
                'brand' => $this->input->post('brand'),
                'category_id' => $this->input->post('category'),
                'unit'       => $this->input->post('unit'),
                'sale_unit'       => $this->input->post('unit'),
                'purchase_unit'       => $this->input->post('unit'),
                'tax_rate'       => $this->input->post('tax_rate'),
                'subcategory_id' => $this->input->post('subcategory') ?: null,
                'cost' => 0,
                'price' => $this->sma->formatDecimal(10),
                'product_details_en' => $this->input->post('desc_en'),
                'product_details' => $this->input->post('desc'),
                'slug' => $this->sma->slug($this->input->post('name')),
                'hide' => $this->input->post('hide') ?: 0,
            );

            $warehouse_qty = null;
            $product_attributes = null;

            if ($this->input->post('type') == 'standard') {
                $wh_total_quantity = 0;
                $pv_total_quantity = 0;

                for ($s = 2; $s > 5; $s++) {
                    $data['suppliers' . $s] = $this->input->post('supplier_' . $s);
                    $data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                }

                foreach ($warehouses as $warehouse) {
                    $wh_qty = $this->input->post('wh_qty_' . $warehouse->id);

                    if ($wh_qty) {
                        $warehouse_qty[] = array(
                            'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                            'quantity' => $wh_qty
                        );

                        $wh_total_quantity += $wh_qty;
                    }
                }

                // $data['warehouse_quantity'] = $wh_total_quantity;
            }
    //$this->sma->print_arrays($data);
            // $product_id = $this->products_model->addProduct($data);
            $product_id = $this->products_model->addProduct($data, null, $warehouse_qty, [], null,$wh_id);
            //$product_id = $this->products_model->addAjaxProductFromSales_new($data);
            // last query

            if ($product_id) {
                // Insert product attributes, if any
                if (!empty($product_attributes)) {
                    foreach ($product_attributes as $attribute) {
                        $attribute['product_id'] = $product_id;
                        $this->products_model->addProductAttribute($attribute);
                    }
                }

                // Insert warehouse quantities, if any
                if (!empty($warehouse_qty)) {
                    foreach ($warehouse_qty as $wh_qty) {
                        $wh_qty['product_id'] = $product_id;
                        $this->products_model->addWarehouseQuantity($wh_qty);
                    }
                }

                $this->session->set_flashdata('message', lang("product_added"));
                echo json_encode(array('success' => lang("product_added")));
            } else {
                $error = lang("product_add_failed") . ' ' . lang("error_try_again");
                $this->session->set_flashdata('error', $error);
                echo json_encode(array('error' => $error));
            }
        } else {
            $error = validation_errors();
            $this->session->set_flashdata('error', $error);
            echo json_encode(array('error' => $error));
        }
    }

    public function reverseAdjustment_count($id){

        //$this->sma->print_arrays($id);
        $this->products_model->reverseAdjustment_count($id);
        }


    public function reverseAdjustment($id){
        $this->products_model->reverseAdjustment($id);
    }

    public function getWarehouseById($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->site->getWarehouseByID($id);
		$this->sma->send_json($row);
    }

    public function quantity_adjust($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments');
		/*if(!$this->site->IsPermissions('adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('Quantity_adjustments')]];
        $meta                = ['page_title' => lang('Quantity_adjustments'), 'bc' => $bc];
        $this->page_construct('products/quantity_adjust', $meta, $this->data);
		}
    }

    public function getadjusts($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('Delete_adjustment') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_adjust/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('adjusts')}.id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('adjusts')
            ->join('warehouses', 'warehouses.id=adjusts.warehouse_id', 'left')
            ->join('users', 'users.id=adjusts.created_by', 'left')
            ->group_by('adjusts.id');
        if ($warehouse_id) {
            $this->datatables->where('adjusts.warehouse_id', $warehouse_id);
        }
        $this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('products/edit_adjust/$1') . "' class='tip' title='" . lang('Edit_adjustment') . "'><i class='fa fa-edit'></i></a> " . $delete_link . '</div>', 'id');

        echo $this->datatables->generate();
    }

    public function add_adjust($count_id = null)
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $_POST['product_id'][$r];
                $type       = $_POST['type'][$r];
                $quantity   = $_POST['quantity'][$r];
                $serial     = $_POST['serial'][$r];
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction' && !$count_id) {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }


                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                ];
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => $this->input->post('count_id') ? $this->input->post('count_id') : null,
            ];

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

             //$this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjust($data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjust');
        } else {
            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items       = $this->products_model->getStockCountItems($count_id);
                foreach ($items as $item) {
                    $c = sha1(uniqid(mt_rand(), true));
                    if ($item->counted != $item->expected) {
                        $product     = $this->site->getProductByID($item->product_id);
                        $row         = json_decode('{}');
                        $row->id     = $item->product_id;
                        $row->code   = $product->code;
                        $row->name   = $product->name;
                        $row->qty    = $item->counted - $item->expected;
                        $row->type   = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty    = $row->qty > 0 ? $row->qty : (0 - $row->qty);
                        $options     = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $ri          = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'options' => $options, ];
                        $c++;
                    }
                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : false;
            $this->data['warehouse_id']     = $count_id ? $stock_count->warehouse_id : false;
            $this->data['count_id']         = $count_id;
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses']       = $this->site->getAllWarehouses();
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment_count')]];
            $meta                           = ['page_title' => lang('add_adjustment_count'), 'bc' => $bc];
            $this->page_construct('products/add_adjust', $meta, $this->data);
        }
    }

    public function qadj_suggestions()
    {
        $term = $this->input->get('term', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        $sr        = addslashes($sr);

        $rows = $this->products_model->getQASuggestions($sr);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty    = 1;
                $options     = $this->products_model->getProductOptions($row->id);
                $row->option = $option_id;
                $row->serial = '';
                $c           = sha1(uniqid(mt_rand(), true));
                $pr[]        = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'            => $row, 'options' => $options, ];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    public function view_adjust($id)
    {
        $this->sma->checkPermissions('adjustments', true);

        $adjustment = $this->products_model->getAdjustByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        $this->data['inv']        = $adjustment;
        $this->data['rows']       = $this->products_model->getAdjustItems($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse']  = $this->site->getWarehouseByID($adjustment->warehouse_id);
        $this->load->view($this->theme . 'products/view_adjust', $this->data);
    }

    public function edit_adjust($id)
    {
        $this->sma->checkPermissions('adjustments', true);
        $adjustment = $this->products_model->getAdjustByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        if ($adjustment->notice_creditor_id) {
            $this->session->set_flashdata('error', lang('There_are_accounting_operations_on_this_adjustment'));
            $this->sma->md();
        }

        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = $adjustment->date;
            }

            $reference_no = $this->input->post('reference_no');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $_POST['product_id'][$r];
                $type       = $_POST['type'][$r];
                $quantity   = $_POST['quantity'][$r];
                $equantity  = $_POST['edit_quantity'][$r];
                $serial     = $_POST['serial'][$r];
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction') {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if (($equantity + $op_wh_qty->quantity) < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } elseif ($equantity < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if (($equantity + $wh_qty['quantity']) < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } elseif ($equantity < $quantity) {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                ];
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'updated_by'   => $this->session->userdata('user_id'),
                'updated_at' => date('Y-m-d H:s:i'),
            ];

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->updateAdjust($id, $data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjust');
        } else {
            $inv_items = $this->products_model->getAdjustItems($id);
            // krsort($inv_items);
            foreach ($inv_items as $item) {
                $c           = sha1(uniqid(mt_rand(), true));
                $product     = $this->site->getProductByID($item->product_id);
                $row         = json_decode('{}');
                $row->id     = $item->product_id;
                $row->code   = $product->code;
                $row->name   = $product->name;
                $row->qty    = $item->quantity;
                $row->oqty   = $item->quantity;
                $row->type   = $item->type;
                $options     = $this->products_model->getProductOptions($product->id);
                $row->option = $item->option_id ? $item->option_id : 0;
                $row->serial = $item->serial_no ? $item->serial_no : '';
                $ri          = $this->Settings->item_addition ? $product->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'options' => $options, ];
                $c++;
            }

            $this->data['adjustment']       = $adjustment;
            $this->data['adjustment_items'] = json_encode($pr);
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses']       = $this->site->getAllWarehouses();
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Edit_adjustment')]];
            $meta                           = ['page_title' => lang('Edit_adjustment'), 'bc' => $bc];
            $this->page_construct('products/edit_adjust', $meta, $this->data);
        }
    }

    public function delete_adjust($id = null)
    {
        $this->sma->checkPermissions('adjustments', true);
        if (!$id) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);
        }
        $adjustment = $this->products_model->getAdjustByID($id);
        if ($adjustment->notice_creditor_id) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('There_are_accounting_operations_on_this_adjustment')]);

        }
        if ($this->products_model->deleteAdjust($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('adjustment_deleted')]);
        }
    }

    public function revaluation($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments');
		/*if(!$this->site->IsPermissions('adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/{

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('Re-evaluation_of_cost_prices')]];
        $meta                = ['page_title' => lang('Re-evaluation_of_cost_prices'), 'bc' => $bc];
        $this->page_construct('products/revaluation', $meta, $this->data);
		}
    }

    public function add_revaluation($count_id = null)
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {



			$jsonArr_product_id=json_decode($_POST['jsonArr_product_id']);
			$jsonArr_product_id= (array) $jsonArr_product_id;

			$product_id_arr = [];
			foreach ($jsonArr_product_id as $product_arr) {
				$product_id_arr[]=  $product_arr->value;

			}




			$jsonArr_old_cost=json_decode($_POST['jsonArr_old_cost']);
			$jsonArr_old_cost= (array) $jsonArr_old_cost;

			$old_cost_arr = [];
			foreach ($jsonArr_old_cost as $c_arr) {
				$old_cost_arr[]=  $c_arr->value;

			}


            $jsonArr_cost=json_decode($_POST['jsonArr_cost']);
			$jsonArr_cost= (array) $jsonArr_cost;

			$cost_arr = [];
			foreach ($jsonArr_cost as $c_arr) {
				$cost_arr[]=  $c_arr->value;

			}







            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('rv');
            $warehouse_id = $this->input->post('warehouse');





            $note         = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($product_id_arr) ? sizeof($product_id_arr) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $product_id_arr[$r];
                //$type       = $_POST['type'][$r];
                //$quantity   = $quantity_arr[$r];
                //$cost   = $cost_arr[$r];
                //$value   = $_POST['value'][$r];
                $type       = "addition";//$_POST['type'][$r];
                $old_cost   = $old_cost_arr[$r];
                $cost   = $cost_arr[$r];

				//$unit   = $_POST['unit'][$r];
				$unit   = $this->products_model->getProductByID($product_id)->unit ? $this->products_model->getProductByID($product_id)->unit : 0;
                $serial     = '';//$_POST['serial'][$r];


				if (!$this->Settings->overselling && $type == 'subtraction' && !$count_id) {

					$product_details = $this->products_model->getProductByID($product_id);
                    if ($item_expiry) {
                        $today = date('Y-m-d');
                        if ($item_expiry <= $today) {
                            $this->session->set_flashdata('error', lang('product_expiry_date_issue') . ' (' . $product_details->name . ')');
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }




                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $products[] = [
                    'product_id'   => $product_id,
                    'old_cost'     => $old_cost,
                    'cost'     => $cost,
                    'unit'     => $unit,
                    'warehouse_id' => $warehouse_id,
                    'grpId'    => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
                    'coId'    => $this->input->post('coId') ? $this->input->post('coId') : 1,
                    'brId'    => $this->input->post('brId') ? $this->input->post('brId') : 1,
                ];



			}

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }


            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => $this->input->post('count_id') ? $this->input->post('count_id') : null,
            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

             //$this->sma->print_arrays($data, $products,$_POST);
        }

        if ($this->form_validation->run() == true && $this->products_model->addRevaluation($data, $products)) {
            $this->session->set_userdata('remove_rvls', 1);
            $this->session->set_flashdata('message', lang('Revaluation_added'));
            admin_redirect('products/revaluation');
        } else {
            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items       = $this->products_model->getStockCountItems($count_id);
                foreach ($items as $item) {
                    $c = sha1(uniqid(mt_rand(), true));
                    if ($item->counted != $item->expected) {
                        $product     = $this->site->getProductByID($item->product_id);
                        $row         = json_decode('{}');
                        $row->id     = $item->product_id;
                        $row->code   = $product->code;
                        $row->name   = $product->name;
                        $row->qty    = $item->counted - $item->expected;
                        $row->type   = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty    = $row->qty > 0 ? $row->qty : (0 - $row->qty);
                        $options     = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $ri          = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'options' => $options, ];
                        $c++;
                    }
                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : false;
            $this->data['warehouse_id']     = $count_id ? $stock_count->warehouse_id : false;
            $this->data['count_id']         = $count_id;
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

             $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));


            }else{
    $this->data['warehouses'] = $this->site->getAllWarehouses();
            }


    //        $this->data['warehouses']       = $this->site->getAllWarehouses();
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Add_revaluation_of_cost_prices')]];
            $meta                           = ['page_title' => lang('Add_revaluation_of_cost_prices'), 'bc' => $bc];
            $this->page_construct('products/add_revaluation', $meta, $this->data);
        }
    }

    public function rv_suggestions()
    {
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->products_model->getRVSuggestions($sr, $warehouse_id);
        if ($rows) {
            foreach ($rows as $row) {
				$row->qty    = 1;
                $row->cost    = 0;
                $row->value    = 0;
                $row->expiry   = '';
                $options     = $this->products_model->getProductOptions($row->id);
                $row->unit     = $this->products_model->getUnitById($row->unit);
                $row->option = $option_id;
                $row->serial = '';
                $c           = sha1(uniqid(mt_rand(), true));
                $pr[]        = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'            => $row, 'options' => $options, ];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    public function getRevaluation($warehouse_id = null)
    {
        $this->sma->checkPermissions('adjustments');

        $delete_link = "";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('revaluations')}.id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('revaluations')
            ->join('warehouses', 'warehouses.id=revaluations.warehouse_id', 'left')
            ->join('users', 'users.id=revaluations.created_by', 'left')
            ->group_by('revaluations.id');
        if ($warehouse_id) {
            $this->datatables->where('revaluations.warehouse_id', $warehouse_id);
        }
        $this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('products/edit_revaluation/$1') . "' class='tip' title='" . lang('Edit_revaluation_of_cost_prices') . "'><i class='fa fa-edit'></i></a> " . $delete_link . '</div>', 'id');

        echo $this->datatables->generate();
    }

    public function view_revaluation($id)
    {
        $this->sma->checkPermissions('adjustments', true);

        $revaluation = $this->products_model->getRevaluationByID($id);
        if (!$id || !$revaluation) {
            $this->session->set_flashdata('error', lang('revaluation_not_found'));
            $this->sma->md();
        }

        $this->data['inv']        = $revaluation;
        $this->data['rows']       = $this->products_model->getRevaluations($id);
        $this->data['created_by'] = $this->site->getUser($revaluation->created_by);
        $this->data['updated_by'] = $this->site->getUser($revaluation->updated_by);
        $this->data['warehouse']  = $this->site->getWarehouseByID($revaluation->warehouse_id);
        $this->load->view($this->theme . 'products/view_revaluation', $this->data);
    }


    public function edit_revaluation($id)
    {

		//$json = file_get_contents('php://input');
		    //$sales =json_decode($json);


			//$this->sma->print_arrays($sales);
		//$this->sma->print_arrays($this->input->post(NULL, TRUE));
        $this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else{
        $revaluation = $this->products_model->getRevaluationByID($id);
        if (!$id || !$revaluation) {
            $this->session->set_flashdata('error', lang('revaluation_not_found'));
            $this->sma->md();
        }


        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {


			$jsonArr_product_id=json_decode($_POST['jsonArr_product_id']);
			$jsonArr_product_id= (array) $jsonArr_product_id;

			$product_id_arr = [];
			foreach ($jsonArr_product_id as $product_arr) {
				$product_id_arr[]=  $product_arr->value;

			}

            $jsonArr_old_cost=json_decode($_POST['jsonArr_old_cost']);
			$jsonArr_old_cost= (array) $jsonArr_old_cost;

			$old_cost_arr = [];
			foreach ($jsonArr_old_cost as $c_arr) {
				$old_cost_arr[]=  $c_arr->value;

			}


			$jsonArr_cost=json_decode($_POST['jsonArr_cost']);
			$jsonArr_cost= (array) $jsonArr_cost;

			$cost_arr = [];
			foreach ($jsonArr_cost as $c_arr) {
				$cost_arr[]=  $c_arr->value;

			}






			//$this->sma->print_arrays($product_id_arr);
			//$this->sma->print_arrays($expiry_arr);


			//$this->sma->print_arrays(array_values($jsonArr_product_id));

			//$this->sma->print_arrays(serialize(array_values($product_id_arr)));
			//$jsonData=json_encode($product_id_arr);
			//$this->sma->print_arrays(rawurldecode($product_id_arr));
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = $revaluation->date;
            }

            $reference_no = $this->input->post('reference_no');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->sma->clear_tags($this->input->post('note'));


            $i = isset($product_id_arr) ? sizeof($product_id_arr) : 0;
			//$i = isset($jsonArr_product_id) ? sizeof($jsonArr_product_id) : 0;
			//$this->sma->print_arrays($i);

            for ($r = 0; $r < $i; $r++) {
                $product_id = $product_id_arr[$r];
                $type       = "addition";//$_POST['type'][$r];
                $old_cost   = $old_cost_arr[$r];
                $cost   = $cost_arr[$r];
                $unit   = $this->products_model->getProductByID($product_id)->unit ? $this->products_model->getProductByID($product_id)->unit : 0;


                if (!$this->Settings->overselling && $type == 'subtraction') {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
                $products[] = [
                    'product_id'   => $product_id,
                    'old_cost'     => $old_cost,
                    'cost'     => $cost,
                    'unit'     => $unit,
                    'warehouse_id' => $warehouse_id,
                    'grpId'    => $this->input->post('grpId') ? $this->input->post('grpId') : 1,
                    'coId'    => $this->input->post('coId') ? $this->input->post('coId') : 1,
                    'brId'    => $this->input->post('brId') ? $this->input->post('brId') : 1,
                ];
                }else{
                $products[] = [
                    'product_id'   => $product_id,
                    'old_cost'     => $old_cost,
                    'cost'     => $cost,
                    'unit'     => $unit,
                    'warehouse_id' => $warehouse_id,

                ];
            }
				$tvalue=$tvalue+$value;
            }

			//$this->sma->print_arrays($i);

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
				'updated_by'   => $this->session->userdata('user_id'),
                'updated_at' => date('Y-m-d H:s:i'),
            ];

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $data  ['grpId']=$this->input->post('grpId') ? $this->input->post('grpId') : 1;
                $data  ['coId']=$this->input->post('coId') ? $this->input->post('coId') : 1;
                $data  ['brId']=$this->input->post('brId') ? $this->input->post('brId') : 1;

            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }



        //$this->sma->print_arrays($id, $data, $products);

        }


        if ($this->form_validation->run() == true && $this->products_model->updateRevaluation($id, $data, $products)) {
            $this->session->set_userdata('remove_rvls', 1);
            $this->session->set_flashdata('message', lang('Revaluation_updated'));
            admin_redirect('products/revaluation');
        } else {
            $inv_items = $this->products_model->getRevaluations($id);
            // krsort($inv_items);
            foreach ($inv_items as $item) {
                $c           = sha1(uniqid(mt_rand(), true));
                $product     = $this->site->getProductByID($item->product_id);
                $row         = json_decode('{}');
                $row->id     = $item->product_id;
                $row->code   = $product->code;
                $row->name   = $product->name;
                $row->qty    = $item->quantity;
                $row->cost    = $item->cost;
                $row->avg_cost    = $item->old_cost;
                $row->unit     = $this->products_model->getUnitById($item->unit);
				if($item->expiry==null)
				$row->expiry    = "";
                else
                $row->expiry    = $item->expiry;

                $row->type   = $item->type;
                $options     = $this->products_model->getProductOptions($product->id);
                $row->option = $item->option_id ? $item->option_id : 0;
                $row->serial = $item->serial_no ? $item->serial_no : '';
                $ri          = $this->Settings->item_addition ? $product->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'options' => $options, ];
                $c++;
            }

            $this->data['revaluation']       = $revaluation;
            $this->data['revaluation_items'] = json_encode($pr);
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

            $this->data['mainGroups'] = $this->site->getMainGroup();
            $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
            $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

             $this->data['warehouses'] = $this->input->post('brId') ? $this->site->getWarehousesBybrId($this->input->post('brId')) : $this->site->getWarehousesBybrId($this->session->userdata('brId'));


            }else{
                $this->data['warehouses'] = $this->site->getAllWarehouses();
            }
            //$this->data['warehouses']       = $this->site->getAllWarehouses();
			$this->data['approved_adjustments']       = $this->site->IsPermissions('products-approved_adjustments');

            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('Edit_revaluation_of_cost_prices')]];
            $meta                           = ['page_title' => lang('Edit_revaluation_of_cost_prices'), 'bc' => $bc];
            $this->page_construct('products/edit_revaluation', $meta, $this->data);
        }
		}
    }

    public function add_adjust_by_excel()
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');


            $note         = $this->sma->clear_tags($this->input->post('note'));
            $data1         = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => null,
            ];
            //if ($_FILES['csv_file']['size'] > 0) {
              /*  $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv                = $this->upload->file_name;
				*/
				$this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                //$config['allowed_types'] = 'csv';
                $config['allowed_types'] = 'xlsx|xls';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;

                $data1['attachment'] = $csv;


                /*$arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }*/
					if (!$this->upload->do_upload('xlsx_file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}
			if(empty($error)){
			  if (!empty($data['upload_data']['file_name'])) {
				$import_xls_file = $data['upload_data']['file_name'];
			} else {
				$import_xls_file = 0;
			}
			$inputFileName = $this->digital_upload_path . $import_xls_file;


				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

				//$this->sma->print_arrays($allDataInSheet);

				$flag = true;
				$i=0;
				foreach ($allDataInSheet as $value) {
				  if($flag){
					$flag =false;
					continue;
				  }
				  //$inserdata[$i]['#'] = $value['A'];$keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];

				  $inserdata[$i]['code'] = trim($value['A']);
				  $inserdata[$i]['quantity'] = trim($value['B']);
				  $inserdata[$i]['variant'] = trim($value['C']);
				  $i++;
				}
				$rw          = 2;
                $differences = 0;
                $matches     = 0;
				//$this->sma->print_arrays($inserdata);
                foreach ($inserdata as $pr) {



					 if ($product = $this->products_model->getProductByCode(trim($pr['code']))) {
                        $csv_variant = trim($pr['variant']);

                        $variant     = !empty($csv_variant) ? $this->products_model->getProductVariantID($product->id, $csv_variant) : null;
                        /*if ($wp_details = $this->products_model->getWarehouseProductQuantity($warehouse_id, $product->id)) {
                            if($wp_details->quantity>0)
                            $csv_quantity = (-1)*$wp_details->quantity;
                            if($wp_details->quantity<0)
                            $csv_quantity = abs($wp_details->quantity);
                            if($wp_details->quantity==0)
                            $csv_quantity = trim($pr['quantity']);
                        }else{*/
                        $csv_quantity = trim($pr['quantity']);
                        //}
                        $type         = $csv_quantity > 0 ? 'addition' : 'subtraction';
                        $quantity     = $csv_quantity > 0 ? $csv_quantity : (0 - $csv_quantity);




                        /*if (!$this->Settings->overselling && $type == 'subtraction') {
                            if ($variant) {
                                if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                                    if ($op_wh_qty->quantity < $quantity) {
                                        $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                        redirect($_SERVER['HTTP_REFERER']);
                                    }
                                } else {
                                    $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            }
                            if ($wh_qty = $this->products_model->getProductQuantity($product->id, $warehouse_id)) {
                                if ($wh_qty['quantity'] < $quantity) {
                                    $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            } else {
                                $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }*/

                        $products[] = [
                            'product_id'   => $product->id,
                            'type'         => $type,
                            'quantity'     => $quantity,
                            'warehouse_id' => $warehouse_id,
                            'option_id'    => null,
                            'serial_no'    => null,
                        ];
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect($_SERVER['HTTP_REFERER']);
                    }



                    /*if ($product = $this->products_model->getProductByCode(trim($pr['product_code']))) {
                        $pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
                        $products_expected = $this->products_model->getStockCountProductsQuantity($stock_count->warehouse_id, $stock_count->type, $stock_count->categories, $stock_count->brands,trim($pr['product_code']));
						//$this->sma->print_arrays($products_expected);
			    foreach ($products_expected as $product_expected) {
                if ($variants = $this->products_model->getStockCountProductVariants($stock_count->warehouse_id, $product_expected->id)) {
                    foreach ($variants as $variant) {
                           $pr['expected']=$variant->quantity;
                                            }
                } else {
                        $pr['expected']=$product_expected->quantity;
                }

                }



						if ($pr['expected'] == $pr['counted']) {
                            $matches++;
                        } else {
                            $pr['stock_count_id']     = $id;
                            $pr['product_id']         = $product->id;
                            $pr['cost']               = $product->cost;
                            $pr['unit']               = $product->unit;
                            $pr['product_variant_id'] = "";//empty($pr['product_variant']) ? null : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
                            $products[]               = $pr;
                            $differences++;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('products/finalize_count_temp/' . $id);
                    }
					*/
                    $rw++;


                }
                /*$data1['final_file']  = $csv;
                $data1['differences'] = $differences;
                $data1['matches']     = $matches;
                $data1['missing']     = $stock_count->rows - ($rw - 2);
                $data1['finalized']   = 1;*/
            }
                // $this->sma->print_arrays($final);

            //} else {
            //    $this->form_validation->set_rules('csv_file', lang('upload_file'), 'required');
            //}

             //$this->sma->print_arrays($data1, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjust($data1, $products)) {
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjust');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment')]];
            $meta                     = ['page_title' => lang('Add_adjustment_by_excel'), 'bc' => $bc];
            $this->page_construct('products/add_adjust_by_excel', $meta, $this->data);
        }
    }


    public function addPriceGroupByAjax()
    {
		//$this->sma->print_arrays($product);
        /*if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }*/
        if ($this->input->is_ajax_request()) {


                $price_groups = $this->input->get('price_groups', true);
                $row = $this->products_model->getProductByID($price_groups['product_id']);
                $current_unit= $row->purchase_unit ? $row->purchase_unit : $row->unit;
                $unit_name=$this->products_model->getUnitById($price_groups['product_unit_id'])->name;
                $unit_base_name=$this->products_model->getUnitById($price_groups['unit'])->name;
                $unit_code=trim($unit_name)."1x".trim($price_groups['factor'])."".trim($unit_base_name);
                $ch=$this->site->checkUnitsByBUID($price_groups['unit'],$unit_code,$price_groups['factor']);
                if($ch){

                    $product_unit_id=$ch->id;

                }else{


                    $data = [
                        'name'            => $unit_code,
                        'code'            => $unit_code,
                        'base_unit'       => $price_groups['unit'],
                        'operator'        => '*',
                        'operation_value' => $price_groups['factor'],
                    ];
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {

                        $data  ['grpId']=1;
                        $data  ['coId']=1;
                        $data  ['brId']=$product['brId'] ? $product['brId'] : 1;

                    }
                    $product_unit_id=$this->site->addUnit($data);





                }













            if ($this->settings_model->setProductPriceForPriceGroup($price_groups['product_id'], $price_groups['price_group_id'], $price_groups['selling_price'], $product_unit_id, $price_groups['pricing_code'])) {


                    if($price_groups['add_checkbox']==1){
                        if($price_groups['punit']==$current_unit && $price_groups['su']==1){
                        $units    = $this->site->getUnitsByBUID($price_groups['unit']);
				        $real_unit_cost=$price_groups['purchasing_price'];
                        $base_unit_cost=($price_groups['purchasing_price'] / $price_groups['factor']);

                        $unit             = $product_unit_id;

                            $pr       = ['unit'     => $unit, 'base_unit_cost' => $base_unit_cost, 'real_unit_cost' => $real_unit_cost, 'current_unit' => $current_unit, 'units' => $units];

                        }else{

                            $row->cost             = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                            $row->real_unit_cost   = $row->cost;
                            $row->base_quantity    = 1;
                            $row->base_unit        = $row->unit;
                            $row->base_unit_cost   = $row->cost;
                            $row->unit             = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                            $row->new_entry        = 1;
                            $row->expiry           = '';
                            $row->qty              = 1;
                            $row->quantity_balance = '';
                            $row->discount         = '0';
                            unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);


				            $units    = $this->site->getUnitsByBUID($row->base_unit);
				            $options              = $this->products_model->getProductOptions($row->id);

                            $tax_rate = '';//$this->site->getTaxRateByID($row->tax_rate);





                        //$row->cost=$price_groups['purchasing_price'];
                        $row->real_unit_cost=$price_groups['purchasing_price'];
                        $row->base_unit_cost=($price_groups['purchasing_price'] / $price_groups['factor']);
                        $row->unit             = $product_unit_id;
                        $row->unit_first             = $product_unit_id;
                        $row->code_first             = $price_groups['pricing_code'];









                            $c                    = uniqid(mt_rand(), true);
                            $r = 0;
                            $id=sha1($c . $r);
                            $pr       = ['row'     => $row,'code_first' => $row->code_first,'unit_first' => $row->unit_first, 'current_unit' => $current_unit, 'units' => $units, 'options' => $options,'item_id' => $row->id,'id' => $id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0'];

                        }
                        $this->sma->send_json(['msg' => 'success', 'result' => $pr]);
                    }else{
                        $this->sma->send_json(['msg' => 'success_but_not_adding']);
                    }
            } else {
                exit(json_encode(['msg' => lang('failed_to_add_product')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }

    public function suggestionsV3()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = ['id' => $row->id, 'text' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1,'unit'=>$row->unit];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'text' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    public function getProductV3($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->products_model->getProductByID($id);
        $this->sma->send_json([['id' => $row->id, 'text' => $row->name]]);
    }

    public function getBalance($id,$warehouse){



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
        $start_date = $this->sma->fld($start_date);
        $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
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
//$wp .= ' GROUP BY wp.product_id ) WarehousesProducts';
//$wpcombo .= ' GROUP BY wpcombo.product_id ) ComboItems';
$wpo .= ' GROUP BY wpo.product_id ) WarehousesProductsOrignal';



$this->db
->select("(COALESCE( POpen.openQty, 0 ) + COALESCE( PCosts.purchasedQty, 0 ) + COALESCE( PCostsRet.purchasedQty, 0 ) + COALESCE( PTransferTo.purchasedQty, 0 ) + COALESCE( PTransferFrom.purchasedQty, 0 ) + COALESCE( PTransferMun.confirmQty, 0 ) + (COALESCE( PDestruction.destructionQty, 0 )  + COALESCE( PDestructionCombo.destructionQty, 0 )) - COALESCE( PSales.soldQty, 0 ) - COALESCE( PSalesRet.soldQty, 0 )  + COALESCE( PSalesCombo.soldQty, 0 ) + COALESCE( PStock.stockQty, 0 ) + COALESCE( QAdj.openQty, 0 ) )  as netTotal", false)
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
if ($id) {
$this->db->where($this->db->dbprefix('products') . '.id', $id);
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
return $data;

    }

    public function suggestionsV4()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }
        $rows = $this->products_model->getBaseUnitsNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = ['id' => $row->id, 'text' => $row->name , 'code' => $row->code, 'name' => $row->name];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'text' => lang('Unit_no_match_found'), 'value' => $term]]);
        }
    }

    public function getCategoryById($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->site->getCategoryById($id);
		$this->sma->send_json($row);
    }

    public function suggestionsV5()
    {
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductNamesV5($term, $warehouse_id);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = ['id' => $row->id, 'text' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1,'unit'=>$row->unit];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'text' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    public function reverseDestruction($id = null)
    {
        // $this->sma->checkPermissions('index');
        $row = $this->products_model->reverseDestruction($id);
		$this->sma->send_json($row);
    }

    public function modifiers()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('Modifiers')]];
        $meta = ['page_title' => lang('Modifiers'), 'bc' => $bc];
        $this->page_construct('products/modifiers', $meta, $this->data);
    }

	public function add_modifier()
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|is_unique[modifiers.name]|required');
        $this->form_validation->set_rules('price', lang('price_addition'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = [
                'name' => $this->input->post('name'),
                'price' => $this->input->post('price'),
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
                $config = null;
            }

            //$this->sma->print_arrays($data);
        } elseif ($this->input->post('add_modifier')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/modifiers');
        }

        if ($this->form_validation->run() == true && $this->products_model->addModifier($data)) {
            $this->session->set_flashdata('message', lang('Modifier_added'));
            admin_redirect('products/modifiers');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'products/add_modifier', $this->data);
        }
    }

	public function edit_modifier($id = null)
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $modifier_details = $this->products_model->getModifierByID($id);
        if ($this->input->post('name') != $modifier_details->name) {
            $this->form_validation->set_rules('name', lang('name'), 'required|is_unique[modifiers.name]');
        }
        $this->form_validation->set_rules('price', lang('price_addition'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = [
                'name' => $this->input->post('name'),
                'price' => $this->input->post('price'),
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
                $config = null;
            }
        } elseif ($this->input->post('edit_modifier')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/modifiers');
        }

        if ($this->form_validation->run() == true && $this->products_model->updateModifier($id, $data)) {
            $this->session->set_flashdata('message', lang('Modifier_updated'));
            admin_redirect('products/modifiers');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modifier']  = $modifier_details;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'products/edit_modifier', $this->data);
        }
    }

    public function getModifiers()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id,image, name, price')
            ->from('modifiers')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('products/edit_modifier/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_modifier') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_modifier') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_modifier/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function delete_modifier($id = null)
    {
        if($this->products_model->getAllProductsModifierCount($id)){
            $this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_modifier_There_are_relevant_processes')]);
        }else{
            if ($this->products_model->deleteModifier($id)) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Modifier_deleted')]);
            }
        }
    }

    public function category_modifiers($category_id = null)
    {
        //$this->sma->checkPermissions(false, true);



        if ($this->input->get('id')) {
            $category_id = $this->input->get('id');
        }
        $category = $this->site->getCategoryByID($category_id);

         $this->form_validation->set_rules('category_modifiers', lang('submit'), 'required');

        if ($this->form_validation->run() == true) {

            if (empty($_POST['to'])) {
                if($this->products_model->deleteCategoryModifiers($category_id)){
                    $this->session->set_flashdata('message', lang('category_updated'));
                    redirect($_SERVER['HTTP_REFERER']);
                }else{
                 $this->session->set_flashdata('error', lang('sEmptyTable'));
                 redirect($_SERVER['HTTP_REFERER']);
                }
             }
             $products=$this->products_model->getCategoryProducts($category_id);
            foreach ($_POST['to'] as $modifier_id) {

                    $additional_data         = [
                        'category_id' => $category_id,
                        'modifier_id'  => $modifier_id,
                    ];
                    $category_modifiers_data[] = $additional_data;

                    /*foreach ($products as $product) {
                        $additional_data2         = [
                        'product_id' => $product->id,
                        'modifier_id'  => $modifier_id,
                        'category_id' => $category_id,
                        ];
                        $product_modifiers_data[] = $additional_data2;
                    }*/
             }

             if (empty($category_modifiers_data)) {
                $this->session->set_flashdata('error', lang('sEmptyTable'));
                redirect($_SERVER['HTTP_REFERER']);
            }

             //$this->sma->print_arrays($category_modifiers_data);




        } elseif ($this->input->post('category_modifiers')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->products_model->addCategoryModifiers($category_id,$category_modifiers_data/*,$product_modifiers_data*/)) {
            $this->session->set_flashdata('message', lang('category_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['category']  = $category;
            $this->data['modifierCategories'] = $this->products_model->getAllCategoriesWithOutmodifiers($category_id);
            $this->data['categoryModifiers'] = $this->products_model->getCategoryModifiers($category_id);
            $this->load->view($this->theme . 'products/category_modifiers', $this->data);
        }
    }

    public function exportProductsExcel(){
        ini_set('memory_limit','2048M');
        $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Products');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('barcode_symbology'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('brand'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('category_code'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('sale') . ' ' . lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('purchase') . ' ' . lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('cost'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('alert_quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('L1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('M1', lang('tax_method'));
                    $this->excel->getActiveSheet()->SetCellValue('N1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('O1', lang('subcategory_code'));
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('product_variants'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('pcf1'));
                    $this->excel->getActiveSheet()->SetCellValue('R1', lang('pcf2'));
                    $this->excel->getActiveSheet()->SetCellValue('S1', lang('pcf3'));
                    $this->excel->getActiveSheet()->SetCellValue('T1', lang('pcf4'));
                    $this->excel->getActiveSheet()->SetCellValue('U1', lang('pcf5'));
                    $this->excel->getActiveSheet()->SetCellValue('V1', lang('pcf6'));
                    $this->excel->getActiveSheet()->SetCellValue('W1', lang('hsn_code'));
                    $this->excel->getActiveSheet()->SetCellValue('X1', lang('second_name'));
                    $this->excel->getActiveSheet()->SetCellValue('Y1', lang('supplier_name'));
                    $this->excel->getActiveSheet()->SetCellValue('Z1', lang('supplier_part_no'));
                    $this->excel->getActiveSheet()->SetCellValue('AA1', lang('supplier_price'));
                    $this->excel->getActiveSheet()->SetCellValue('AB1', lang('quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('AC1', lang('details'));
                    $this->excel->getActiveSheet()->SetCellValue('AD1', lang('product_details'));

                    $row = 2;
                    $product=$this->products_model->getAllProducts();
                    foreach ($product as $pr) {
                        $id=$pr->id;
                        $product   = $this->products_model->getProductDetail($id);
                        $brand     = $this->site->getBrandByID($product->brand);
                        $base_unit = $sale_unit = $purchase_unit = '';
                        if ($units = $this->site->getUnitsByBUID($product->unit)) {
                            foreach ($units as $u) {
                                if ($u->id == $product->unit) {
                                    $base_unit = $u->code;
                                }
                                if ($u->id == $product->sale_unit) {
                                    $sale_unit = $u->code;
                                }
                                if ($u->id == $product->purchase_unit) {
                                    $purchase_unit = $u->code;
                                }
                            }
                        }
                        $variants         = $this->products_model->getProductOptions($id);
                        $product_variants = '';
                        if ($variants) {
                            $i = 1;
                            $v = count($variants);
                            foreach ($variants as $variant) {
                                $product_variants .= trim($variant->name) . ($i != $v ? '|' : '');
                                $i++;
                            }
                        }
                        $quantity = $product->quantity;
                        if ($wh) {
                            if ($wh_qty = $this->products_model->getProductQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
                        }
                        $supplier = false;
                        if ($product->supplier1) {
                            $supplier = $this->site->getCompanyByID($product->supplier1);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $product->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $product->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $product->barcode_symbology);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($brand ? $brand->name : ''));
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $product->category_code);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale_unit);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase_unit);
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $product->cost);
                        }
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $product->price);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $product->alert_quantity);
                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $product->tax_rate_name);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $product->tax_method ? lang('exclusive') : lang('inclusive'));
                        $this->excel->getActiveSheet()->SetCellValue('N' . $row, $product->image);
                        $this->excel->getActiveSheet()->SetCellValue('O' . $row, $product->subcategory_code);
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $product_variants);
                        $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $product->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('R' . $row, $product->cf2);
                        $this->excel->getActiveSheet()->SetCellValue('S' . $row, $product->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('T' . $row, $product->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('U' . $row, $product->cf5);
                        $this->excel->getActiveSheet()->SetCellValue('V' . $row, $product->cf6);
                        $this->excel->getActiveSheet()->SetCellValue('W' . $row, $product->hsn_code);
                        $this->excel->getActiveSheet()->SetCellValue('X' . $row, $product->second_name);
                        $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $supplier ? $supplier->name : '');
                        $this->excel->getActiveSheet()->SetCellValue('Z' . $row, $supplier ? $product->supplier1_part_no : '');
                        $this->excel->getActiveSheet()->SetCellValue('AA' . $row, $supplier ? $product->supplier1price : '');
                        $this->excel->getActiveSheet()->SetCellValue('AB' . $row, $quantity);
                        $this->excel->getActiveSheet()->SetCellValue('AC' . $row, $product->details);
                        $this->excel->getActiveSheet()->SetCellValue('AD' . $row, $product->product_details);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('AD')->setWidth(40);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'products_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
    }

    public function add_revaluation_by_excel()
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('rv');
            $warehouse_id = $this->input->post('warehouse');


            $note         = $this->sma->clear_tags($this->input->post('note'));
            $data1         = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => null,
            ];
            	$this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = 'xlsx|xls';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;

                $data1['attachment'] = $csv;


            if (!$this->upload->do_upload('xlsx_file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}
			if(empty($error)){
			  if (!empty($data['upload_data']['file_name'])) {
				$import_xls_file = $data['upload_data']['file_name'];
			} else {
				$import_xls_file = 0;
			}
			$inputFileName = $this->digital_upload_path . $import_xls_file;


				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

				//$this->sma->print_arrays($allDataInSheet);

				$flag = true;
				$i=0;
				foreach ($allDataInSheet as $value) {
				  if($flag){
					$flag =false;
					continue;
				  }
				  //$inserdata[$i]['#'] = $value['A'];$keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];

				  $inserdata[$i]['code'] = trim($value['A']);
				  $inserdata[$i]['cost'] = trim($value['B']);
				  $i++;
				}
				$rw          = 2;
                $differences = 0;
                $matches     = 0;
				//$this->sma->print_arrays($inserdata);
                foreach ($inserdata as $pr) {



					 if ($product = $this->products_model->getProductByCode(trim($pr['code']))) {
                        if(trim($pr['cost'])>0){

                        $cost = trim($pr['cost']);

                        if($this->site->getProduct_avg_cost($product->id,$warehouse_id))
				            $old_cost=$this->site->getProduct_avg_cost($product->id,$warehouse_id);
			            else{
				            $old_cost=0;
				        }

				        $unit   = $this->products_model->getProductByID($product->id)->unit ? $this->products_model->getProductByID($product->id)->unit : 0;




                        $products[] = [
                            'product_id'   => $product->id,
                            'old_cost'     => $old_cost,
                            'cost'     => $cost,
                            'warehouse_id' => $warehouse_id,
                            'unit'    => $unit,
                            'grpId'    => 1,
                            'coId'    =>  1,
                            'brId'    => $this->site->getWarehouseByID($warehouse_id)->brId,
                        ];
                        }else{
                            $this->session->set_flashdata('error', lang('Check_new_cost_value') . ' ' . lang('line_no') . ' ' . $rw);
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect($_SERVER['HTTP_REFERER']);
                    }



                    $rw++;


                }

            }
                // $this->sma->print_arrays($final);


        }

        if ($this->form_validation->run() == true && $this->products_model->addRevaluation($data1, $products)) {
            $this->session->set_flashdata('message', lang('Revaluation_added'));
            admin_redirect('products/revaluation');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment')]];
            $meta                     = ['page_title' => lang('Add_revaluation_of_cost_prices_by_excel'), 'bc' => $bc];
            $this->page_construct('products/add_revaluation_by_excel', $meta, $this->data);
        }
    }



}