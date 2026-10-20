<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Promo extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {


            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('promo', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('promo_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->popup_attributes = array('width' => '900', 'height' => '600', 'window_name' => 'sma_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');

    }







    /* ------------------------------------------------------- */
	function add_promo_type($id = NULL)
    {
		$promo_type = $this->site->getPromo_typeByID($id);

        $this->sma->checkPermissions();
        $this->load->helper('security');
		 $this->form_validation->set_rules('promo_type', lang("promo_type"), 'required');
		if ($this->form_validation->run() == true) {
			if(!$this->input->post('status')){
				$status=0;
				}else{
				$status=$this->input->post('status');
					}
            $data = array(
			'promo_type' => $this->input->post('promo_type'),
			'status' => $status,
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);

			$this->db->insert('promo_type', $data);
		}

		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('promo')), array('link' => '#', 'page' => lang('add_promo_type')));
            $meta = array('page_title' => lang('add_promo_type'), 'bc' => $bc);
			$this->data['promo_type'] = $promo_type;
			if(!$this->input->post('promo_type')){
      $this->page_construct('promo/add_promo_type', $meta, $this->data);
			}else{
				redirect(admin_url('promo/promo_type/'));

				}

	}


	function update_promo_type($id = NULL)
    {
		$id=$this->input->post('id');
		$promo_type = $this->site->getPromo_typeByID($id);

        $this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('promo_type_ar', lang("promo_type"), 'required');
        $this->form_validation->set_rules('promo_type', lang("English_name"), 'required');
		if ($this->form_validation->run() == true) {
			if(!$this->input->post('status')){
				$status=0;
				}else{
				$status=$this->input->post('status');
					}
            $data = array(
            'promo_type_ar' => $this->input->post('promo_type_ar'),
            'promo_type' => $this->input->post('promo_type'),
			'status' => $status,
			'revised_date' => date( 'Y-m-d H:i:s')
			);
            //$this->sma->print_arrays($data);
			$this->db->update('promo_type', $data, array('id' => $id));

		}

           redirect(admin_url('promo/promo_type'));

	}

	function edit_promo_type($id = NULL)
    {
		$promo_type = $this->site->getPromo_typeByID($id);



		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('promo')), array('link' => '#', 'page' => lang('Edit_promo_type')));
            $meta = array('page_title' => lang('Edit_promo_type'), 'bc' => $bc);
			$this->data['promo_type'] = $promo_type;
            $this->page_construct('promo/edit_promo_type', $meta, $this->data);

	}






	function promo_type()
    {
		if ( ! $this->loggedIn) {
            admin_redirect('login');
        }
        if ( ! $this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin/welcome');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		//$this->sma->print_arrays( $this->promo_model->getAllpromo_type());

        //$this->data['promo_type'] = $this->promo_model->getAllpromo_type();
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('Promo_type')), array('link' => '#', 'page' => lang('Promo_type')));
            $meta = array('page_title' => lang('Promo_type'), 'bc' => $bc);
            $this->page_construct('promo/promo_type', $meta, $this->data);


    }


	function getPromo_type()
	{

        if ( ! $this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            $this->sma->md();
        }

        $this->load->library('datatables');
        $this->datatables
            ->select("id, promo_type_ar,promo_type,status")
            ->from("promo_type")
            ->where('active',1)
         ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('promo/edit_promo_type/$1') . "' class='tip' title='" . lang("edit_Promo_type") . "'><i class=\"fa fa-edit\"></i></a> </div>", "id");

        if (!$this->Owner) {
            $this->datatables->unset_column('id');
        }
        echo $this->datatables->generate();

	}


	 function promos()
    {
		if ( ! $this->loggedIn) {
            admin_redirect('login');
        }
        if ( ! $this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin/welcome');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

       // $this->data['promo_type'] = $this->promo_model->getAllpromo_type();
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('Promos_list')), array('link' => '#', 'page' => lang('Promos_list')));
            $meta = array('page_title' => lang('Promos_list'), 'bc' => $bc);
            $this->page_construct('promo/promos', $meta, $this->data);


    }


	function getPromos()
	{

        if ( ! $this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            $this->sma->md();
        }

        $this->load->library('datatables');
        if ($this->Settings->user_language == 'arabic'){
        $this->datatables
            ->select($this->db->dbprefix('promo') . ".id as id, {$this->db->dbprefix('promo_type')}.promo_type_ar ,{$this->db->dbprefix('promo')}.promo_name,{$this->db->dbprefix('products')}.code as pcode,{$this->db->dbprefix('products')}.name as pname,{$this->db->dbprefix('promo')}.status,{$this->db->dbprefix('promo')}.end_date as end_date")
            ->from("promo")
            ->join('promo_type', 'promo_type.id=promo.promo_type_id', 'left')
            ->join('products', 'products.id=promo.offer_product', 'left')
         //->edit_column('status', '$1__$2', 'status, id')
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('promo/edit_promo/$1') . "' class='tip' title='" . lang("edit_Promo") . "'><i class=\"fa fa-edit\"></i></a> &nbsp;&nbsp; <a href='" . admin_url('promo/delete_promo/$1') . "' class='tip' title='" . lang("delete_Promo") . "'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
           ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('promo/edit_promo/$1') . "' class='tip' title='" . lang('edit_promo') . "' ><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_promo') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('promo/delete_promo/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        }else{
            $this->datatables
            ->select($this->db->dbprefix('promo') . ".id as id, {$this->db->dbprefix('promo_type')}.promo_type ,{$this->db->dbprefix('promo')}.promo_name,{$this->db->dbprefix('products')}.code as pcode,{$this->db->dbprefix('products')}.name as pname,{$this->db->dbprefix('promo')}.status,{$this->db->dbprefix('promo')}.end_date as end_date")
            ->from("promo")
            ->join('promo_type', 'promo_type.id=promo.promo_type_id', 'left')
            ->join('products', 'products.id=promo.offer_product', 'left')
         //->edit_column('status', '$1__$2', 'status, id')
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('promo/edit_promo/$1') . "' class='tip' title='" . lang("edit_Promo") . "'><i class=\"fa fa-edit\"></i></a> &nbsp;&nbsp; <a href='" . admin_url('promo/delete_promo/$1') . "' class='tip' title='" . lang("delete_Promo") . "'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
           ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('promo/edit_promo/$1') . "' class='tip' title='" . lang('edit_promo') . "' ><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_promo') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('promo/delete_promo/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        }
        $this->datatables->where('hide_opt!=', 1);

        $status_filter = $this->input->post('status_filter') ? $this->input->post('status_filter') : $this->input->get('status_filter');
        if ($status_filter === 'active') {
            $this->datatables->where("({$this->db->dbprefix('promo')}.status = 1 AND ({$this->db->dbprefix('promo')}.end_date IS NULL OR {$this->db->dbprefix('promo')}.end_date = '0000-00-00' OR {$this->db->dbprefix('promo')}.end_date >= CURDATE()))", null, false);
        } elseif ($status_filter === 'inactive') {
            $this->datatables->where("({$this->db->dbprefix('promo')}.status = 0 OR ({$this->db->dbprefix('promo')}.status = 1 AND {$this->db->dbprefix('promo')}.end_date IS NOT NULL AND {$this->db->dbprefix('promo')}.end_date != '0000-00-00' AND {$this->db->dbprefix('promo')}.end_date < CURDATE()))", null, false);
        }

        if (!$this->Owner) {
            $this->datatables->unset_column('id');
        }
        echo $this->datatables->generate();

	}


	function delete_promo($id = NULL)
    {

					$id=$this->uri->segment('4');

                    if ($this->promo_model->deletePromo($id)) {
                        $this->sma->send_json(['error' => 0, 'msg' => lang('promo_deleted')]);
                    //$this->session->set_flashdata('message', $this->lang->line("promo_deleted"));
                    }
                    //redirect($_SERVER["HTTP_REFERER"]);


    }


	function update_promo($id = NULL)
    {
		$id=$this->input->post('id');
        //$this->sma->print_arrays($_POST);
       $promo = $this->site->getPromo_ByID($id);
		$promo_type = $this->promo_model->getActivepromo_type();
		$gift_product = $this->promo_model->getAllProducts();
        $this->sma->checkPermissions();
        $this->load->helper('security');
		 $this->form_validation->set_rules('promo_type_id', lang("promo_type_id"), 'required');
		if ($this->form_validation->run() == true) {

			if($this->input->post('promo_type_id')=='1'){
				if($this->input->post('sel_option')=='cashback'){
					$g_product="";
					$cashback=$this->input->post('cashback');
					}else{
						$cashback="";
					$g_product=$this->input->post('gift_product');
						}
				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'cart_min_value' => $this->input->post('cart_min_value'),
			'sel_option' => $this->input->post('sel_option'),
			'gift_product' => $g_product,
			'cashback' => $cashback,
	    	'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);

				}

			if($this->input->post('promo_type_id')=='2'){

				$data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category2'),
			'gift_product' => $this->input->post('gift_product2'),
			'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);
				}

				if($this->input->post('promo_type_id')=='3'){

				$data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category3'),
			'offer_product' => $this->input->post('offer_product3'),
			'free_product' => $this->input->post('free_product3'),
			'buy_quantity' => $this->input->post('buy_quantity3'),
			'get_quantity' => $this->input->post('get_quantity3'),
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date3') ? $this->sma->fsd(trim($this->input->post('start_date3'))) : null,
            'end_date'    => $this->input->post('end_date3') ? $this->sma->fsd(trim($this->input->post('end_date3'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);


			//$this->sma->print_arrays($data);
				}

			/*if($this->input->post('promo_type_id')=='4'){
				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			//'offer_category' => $this->input->post('offer_category4'),
			'offer_product' =>  $this->input->post('offer_category4') ? null : $this->input->post('offer_product4'),
			'percentage_off' => $this->input->post('percentage_off4'),
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date4') ? $this->sma->fsd(trim($this->input->post('start_date4'))) : null,
            'end_date'    => $this->input->post('end_date4') ? $this->sma->fsd(trim($this->input->post('end_date4'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);

			}*/

            if($this->input->post('promo_type_id')=='4'){


				if($this->input->post('offer_category4')){
					$productsArr=$this->promo_model->getCategoryProducts($this->input->post('offer_category4'));
				foreach($productsArr as $item){

						$product = [
                        'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category4'),
			'offer_product' => $item->id,
			'percentage_off' => $this->input->post('percentage_off4'),
            'buy_quantity' => $this->input->post('buy_quantity4') ? $this->input->post('buy_quantity4') : 1,
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date4') ? $this->sma->fsd(trim($this->input->post('start_date4'))) : null,
            'end_date'    => $this->input->post('end_date4') ? $this->sma->fsd(trim($this->input->post('end_date4'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),
            'hide_opt' => 1,
			'promo_id' => $id
						];

                    $data1[] = ($product);


				}


				}


				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category4'),
			'offer_product' =>  $this->input->post('offer_category4') ? null : $this->input->post('offer_product4'),
			'percentage_off' => $this->input->post('percentage_off4'),
            'buy_quantity' => $this->input->post('buy_quantity4') ? $this->input->post('buy_quantity4') : 1,
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date4') ? $this->sma->fsd(trim($this->input->post('start_date4'))) : null,
            'end_date'    => $this->input->post('end_date4') ? $this->sma->fsd(trim($this->input->post('end_date4'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),


			);
			//}
			}



            if($this->input->post('promo_type_id')=='10'){


				if($this->input->post('offer_category10')){
					$productsArr=$this->promo_model->getCategoryProducts($this->input->post('offer_category10'));
				foreach($productsArr as $item){

						$product = [
                        'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category10'),
			'offer_product' => $item->id,
			'fixed_off' => $this->input->post('fixed_off10'),
            'buy_quantity' => $this->input->post('buy_quantity10') ? $this->input->post('buy_quantity10') : 1,
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date10') ? $this->sma->fsd(trim($this->input->post('start_date10'))) : null,
            'end_date'    => $this->input->post('end_date10') ? $this->sma->fsd(trim($this->input->post('end_date10'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),
            'hide_opt' => 1,
			'promo_id' => $id
						];

                    $data1[] = ($product);


				}


				}else if($this->input->post('promo_type_id')=='10' && $this->input->post('offer_product10_2')){
                    for($i=2;$i<=$this->input->post('su10');$i++){


                    $product = [
                        'promo_type_id' => $this->input->post('promo_type_id'),
            'promo_name' => $this->input->post('promo_name'),
            'offer_category' => $this->input->post('offer_category10'),
            'offer_product' => $this->input->post('offer_product10_'.$i.''),
            'buy_quantity' => $this->input->post('buy_'.$i.'_quantity10') ? $this->input->post('buy_'.$i.'_quantity10') : 1,
            'fixed_off' => $this->input->post('fixed_'.$i.'_off10'),
            'status' => $this->input->post('status'),
            'start_date'  => $this->input->post('start_date10') ? $this->sma->fsd(trim($this->input->post('start_date10'))) : null,
            'end_date'    => $this->input->post('end_date10') ? $this->sma->fsd(trim($this->input->post('end_date10'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
            'revised_date' => date( 'Y-m-d H:i:s'),
            'hide_opt' => 1,
            'promo_id' => $id,
            'promo_sr' => $i,
                        ];
                        $data1[] = ($product);
                    }





                }


				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category10'),
			'offer_product' =>  $this->input->post('offer_category10') ? null : $this->input->post('offer_product10'),
			'fixed_off' => $this->input->post('fixed_off10'),
            'buy_quantity' => $this->input->post('buy_quantity10') ? $this->input->post('buy_quantity10') : 1,
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date10') ? $this->sma->fsd(trim($this->input->post('start_date10'))) : null,
            'end_date'    => $this->input->post('end_date10') ? $this->sma->fsd(trim($this->input->post('end_date10'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),


			);
			//}
			}


			if($this->input->post('promo_type_id')=='5'){
				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'percentage_off' => $this->input->post('percentage_off5'),
			'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);

			}

			if($this->input->post('promo_type_id')=='6'){
				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'percentage_off' => $this->input->post('percentage_off6'),
			'daily_start_time' => $this->input->post('daily_start_time'),
			'daily_end_time' => $this->input->post('daily_end_time'),
			'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);



			}
            if($this->input->post('promo_type_id')=='7'){

				$data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category7'),
			//'offer_product' => $this->input->post('offer_product7')[0],
			'free_product' => $this->input->post('free_product7'),
			'buy_quantity' => $this->input->post('buy_quantity7'),
			'get_quantity' => $this->input->post('get_quantity7'),
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date7') ? $this->sma->fsd(trim($this->input->post('start_date7'))) : null,
            'end_date'    => $this->input->post('end_date7') ? $this->sma->fsd(trim($this->input->post('end_date7'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),
			'offer_product_details' => 1
			);
            //$this->sma->print_arrays($_POST,$data);

			//



				}


                if($this->input->post('promo_type_id')=='9'){

                    $data = array(
                'promo_type_id' => $this->input->post('promo_type_id'),
                'promo_name' => $this->input->post('promo_name'),
                'offer_category' => $this->input->post('offer_category9'),
                //'offer_product' => $this->input->post('offer_product9')[0],
                //'free_product' => $this->input->post('free_product9'),
                'buy_quantity' => $this->input->post('buy_quantity9'),
                'get_quantity' => $this->input->post('get_quantity9'),
                'status' => $this->input->post('status'),
                'start_date'  => $this->input->post('start_date9') ? $this->sma->fsd(trim($this->input->post('start_date9'))) : null,
                'end_date'    => $this->input->post('end_date9') ? $this->sma->fsd(trim($this->input->post('end_date9'))) : null,
                'created_date' => date( 'Y-m-d H:i:s'),
                'revised_date' => date( 'Y-m-d H:i:s'),
                'offer_product_details' => 1
                );
                //$this->sma->print_arrays($_POST,$data);

                //



                    }

                if($this->input->post('promo_type_id')=='8'){
                    $data = array(
               'promo_type_id' => $this->input->post('promo_type_id'),
               'promo_name' => $this->input->post('promo_name'),
               'offer_category' => $this->input->post('offer_category8'),
               //'offer_product' => $this->input->post('offer_product8')[0],
               'percentage_off' => $this->input->post('percentage_off8'),
               'status' => $this->input->post('status'),
               'start_date'  => $this->input->post('start_date8') ? $this->sma->fsd(trim($this->input->post('start_date8'))) : null,
               'end_date'    => $this->input->post('end_date8') ? $this->sma->fsd(trim($this->input->post('end_date8'))) : null,
               'created_date' => date( 'Y-m-d H:i:s'),
               'revised_date' => date( 'Y-m-d H:i:s'),
               'offer_product_details' => 1
               );

               }

               if($this->input->post('promo_type_id')=='11'){
                $data = array(
           'promo_type_id' => $this->input->post('promo_type_id'),
           'promo_name' => $this->input->post('promo_name'),
           'offer_category' => $this->input->post('offer_category11'),
           //'offer_product' => $this->input->post('offer_product11')[0],
           'fixed_off' => $this->input->post('fixed_off11'),
           'status' => $this->input->post('status'),
           'start_date'  => $this->input->post('start_date11') ? $this->sma->fsd(trim($this->input->post('start_date11'))) : null,
           'end_date'    => $this->input->post('end_date11') ? $this->sma->fsd(trim($this->input->post('end_date11'))) : null,
           'created_date' => date( 'Y-m-d H:i:s'),
           'revised_date' => date( 'Y-m-d H:i:s'),
           'offer_product_details' => 1
           );

           }


               //$this->sma->print_arrays($data1,$data);


			$this->db->update('promo', $data, array('id' => $id));

            if($this->input->post('offer_category4')){
                if($this->db->delete('promo', ['promo_id' => $id])){

                    $this->db->insert_batch('promo', $data1);

                }

			}

            if($this->input->post('offer_category10')){
                if($this->db->delete('promo', ['promo_id' => $id])){

                    $this->db->insert_batch('promo', $data1);

                }

			}else if($this->input->post('promo_type_id')=='10' && $this->input->post('offer_product10_2')){

                if($this->db->delete('promo', ['promo_id' => $id])){

                    $this->db->insert_batch('promo', $data1);

                }
            }






                if($this->input->post('promo_type_id')=='7'){
                    $promo_details = $this->promo_model->getPromoDetails($id);
                    foreach ($promo_details as $promo_detail){

                        if(!$this->findObjectById($promo_detail->offer_product,$this->input->post('offer_product7'))){
                            $this->db->delete('promo_details', ['id' => $promo_detail->id]);

                        }

                    }
                foreach ($this->input->post('offer_product7') as $offer_product) {
                    if ($wp_details = $this->promo_model->getPromoDetailsByoffer_product($id, $offer_product)) {
                    }else{
                            $this->db->insert('promo_details', ['promo_id' => $id, 'offer_product' => $offer_product, 'promo_type_id' => $this->input->post('promo_type_id')]);
                    }
                }


            }


            if($this->input->post('promo_type_id')=='9'){
                $promo_details = $this->promo_model->getPromoDetails($id);
                foreach ($promo_details as $promo_detail){

                    if(!$this->findObjectById($promo_detail->offer_product,$this->input->post('offer_product9'))){
                        $this->db->delete('promo_details', ['id' => $promo_detail->id]);

                    }

                }
            foreach ($this->input->post('offer_product9') as $offer_product) {
                if ($wp_details = $this->promo_model->getPromoDetailsByoffer_product($id, $offer_product)) {
                }else{
                        $this->db->insert('promo_details', ['promo_id' => $id, 'offer_product' => $offer_product, 'promo_type_id' => $this->input->post('promo_type_id')]);
                }
            }


        }


            if($this->input->post('promo_type_id')=='8'){
                $promo_details = $this->promo_model->getPromoDetails($id);
                foreach ($promo_details as $promo_detail){

                    if(!$this->findObjectById($promo_detail->offer_product,$this->input->post('offer_product8'))){
                        $this->db->delete('promo_details', ['id' => $promo_detail->id]);

                    }

                }
            foreach ($this->input->post('offer_product8') as $offer_product) {
                if ($wp_details = $this->promo_model->getPromoDetailsByoffer_product($id, $offer_product)) {
                }else{
                        $this->db->insert('promo_details', ['promo_id' => $id, 'offer_product' => $offer_product, 'promo_type_id' => $this->input->post('promo_type_id')]);
                }
            }


        }


        if($this->input->post('promo_type_id')=='11'){
            $promo_details = $this->promo_model->getPromoDetails($id);
            foreach ($promo_details as $promo_detail){

                if(!$this->findObjectById($promo_detail->offer_product,$this->input->post('offer_product11'))){
                    $this->db->delete('promo_details', ['id' => $promo_detail->id]);

                }

            }
        foreach ($this->input->post('offer_product11') as $offer_product) {
            if ($wp_details = $this->promo_model->getPromoDetailsByoffer_product($id, $offer_product)) {
            }else{
                    $this->db->insert('promo_details', ['promo_id' => $id, 'offer_product' => $offer_product, 'promo_type_id' => $this->input->post('promo_type_id')]);
            }
        }


    }


        /*if($this->input->post('offer_category4')){

            if($this->db->delete('promo_details', ['promo_id' => $id, 'promo_type_id' => 4])){
            $productsArr=$this->promo_model->getCategoryProducts($this->input->post('offer_category4'));
            foreach($productsArr as $item){

           // foreach ($this->input->post('offer_product8') as $offer_product) {

                        $this->db->insert('promo_details', ['promo_id' => $id, 'offer_product' => $item->id, 'promo_type_id' => 4]);
            }


        }
    }*/


		}

		redirect(admin_url('promo/promos'));


	}

	function edit_promo($id = NULL)
    {
			$promo = $this->site->getPromo_ByID($id);
            //$this->sma->print_arrays($promo->promo_type_id);
		$promo_type = $this->promo_model->getActivepromo_type();
        $promo_categories = $this->site->getAllCategories();

		$gift_product = $this->promo_model->getAllProducts();
        $promo_details = $this->promo_model->getPromoDetails($id);


		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('promo')), array('link' => '#', 'page' => lang('edit_promo')));
            $meta = array('page_title' => lang('edit_promo'), 'bc' => $bc);
			$this->data['promo'] = $promo;
			$this->data['promo_type'] = $promo_type;
			$this->data['gift_product'] = $gift_product;
            $this->data['promo_details'] = $promo_details;
            $this->data['promo_categories'] = $promo_categories;
            if($promo->promo_type_id==10 && $promo->hide_opt==0){
                $this->data['promo_product_details'] = $this->promo_model->getPromoProductsDetails($id);
                $this->data['su10'] = $this->promo_model->getMaxPromoProductsDetails($id)->su10;
            }
            $this->page_construct('promo/edit_promo', $meta, $this->data);

	}




	function add_promo($id = NULL)
    {

		$promo = $this->site->getPromo_ByID($id);
		$promo_type = $this->promo_model->getActivepromo_type();
		$promo_categories = $this->site->getAllCategories();
		$gift_product = $this->promo_model->getAllProducts();
        $this->sma->checkPermissions();
        $this->load->helper('security');
		 $this->form_validation->set_rules('promo_type_id', lang("promo_type_id"), 'required');
		if ($this->form_validation->run() == true) {

			if($this->input->post('promo_type_id')=='1'){
				if($this->input->post('sel_option')=='cashback'){
					$g_product="";
					$cashback=$this->input->post('cashback');
					}else{
						$cashback="";
					$g_product=$this->input->post('gift_product');
						}
				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'cart_min_value' => $this->input->post('cart_min_value'),
			'sel_option' => $this->input->post('sel_option'),
			'gift_product' => $g_product,
			'cashback' => $cashback,
	    	'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);

				}

			if($this->input->post('promo_type_id')=='2'){

				$data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category2'),
			'gift_product' => $this->input->post('gift_product2'),
			'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);
				}

				if($this->input->post('promo_type_id')=='3'){

				$data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category3'),
			'offer_product' => $this->input->post('offer_product3'),
			'free_product' => $this->input->post('free_product3'),
			'buy_quantity' => $this->input->post('buy_quantity'),
			'get_quantity' => $this->input->post('get_quantity'),
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date3') ? $this->sma->fsd(trim($this->input->post('start_date3'))) : null,
            'end_date'    => $this->input->post('end_date3') ? $this->sma->fsd(trim($this->input->post('end_date3'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);


			//



				}

			if($this->input->post('promo_type_id')=='4'){





				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category4'),
			'offer_product' =>  $this->input->post('offer_category4') ? null : $this->input->post('offer_product4'),
			'percentage_off' => $this->input->post('percentage_off4'),
			'status' => $this->input->post('status'),
            'buy_quantity' => $this->input->post('buy_quantity4') ? $this->input->post('buy_quantity4') : 1,
			'start_date'  => $this->input->post('start_date4') ? $this->sma->fsd(trim($this->input->post('start_date4'))) : null,
            'end_date'    => $this->input->post('end_date4') ? $this->sma->fsd(trim($this->input->post('end_date4'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),
            'hide_opt' => $this->input->post('offer_category4') ? 2 : 0
			);
			//}
			}

            if($this->input->post('promo_type_id')=='10'){





                $data = array(
           'promo_type_id' => $this->input->post('promo_type_id'),
           'promo_name' => $this->input->post('promo_name'),
           'offer_category' => $this->input->post('offer_category10'),
           'offer_product' =>  $this->input->post('offer_category10') ? null : $this->input->post('offer_product10'),
           'fixed_off' => $this->input->post('fixed_off10'),
           'status' => $this->input->post('status'),
           'buy_quantity' => $this->input->post('buy_quantity10') ? $this->input->post('buy_quantity10') : 1,
           'start_date'  => $this->input->post('start_date10') ? $this->sma->fsd(trim($this->input->post('start_date10'))) : null,
           'end_date'    => $this->input->post('end_date10') ? $this->sma->fsd(trim($this->input->post('end_date10'))) : null,
           'created_date' => date( 'Y-m-d H:i:s'),
           'revised_date' => date( 'Y-m-d H:i:s'),
           'hide_opt' => $this->input->post('offer_category10') ? 2 : 0
           );
           //}
           }
			//$this->sma->print_arrays($data,$_POST);



			if($this->input->post('promo_type_id')=='5'){
				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'percentage_off' => $this->input->post('percentage_off5'),
			'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);

			}

			if($this->input->post('promo_type_id')=='6'){
				 $data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'percentage_off' => $this->input->post('percentage_off6'),
			'daily_start_time' => $this->input->post('daily_start_time'),
			'daily_end_time' => $this->input->post('daily_end_time'),
			'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);



			}

            if($this->input->post('promo_type_id')=='7'){

				$data = array(
			'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category7'),
			//'offer_product' => $this->input->post('offer_product7')[0],
			'free_product' => $this->input->post('free_product7'),
			'buy_quantity' => $this->input->post('buy_quantity'),
			'get_quantity' => $this->input->post('get_quantity'),
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date7') ? $this->sma->fsd(trim($this->input->post('start_date7'))) : null,
            'end_date'    => $this->input->post('end_date7') ? $this->sma->fsd(trim($this->input->post('end_date7'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),
			'offer_product_details' => 1
			);
            //$this->sma->print_arrays($_POST,$data);

			//



				}


                if($this->input->post('promo_type_id')=='9'){

                    $data = array(
                'promo_type_id' => $this->input->post('promo_type_id'),
                'promo_name' => $this->input->post('promo_name'),
                'offer_category' => $this->input->post('offer_category9'),
                //'offer_product' => $this->input->post('offer_product9')[0],
                //'free_product' => $this->input->post('free_product9'),
                'buy_quantity' => $this->input->post('buy_quantity'),
                'get_quantity' => $this->input->post('get_quantity'),
                'status' => $this->input->post('status'),
                'start_date'  => $this->input->post('start_date9') ? $this->sma->fsd(trim($this->input->post('start_date9'))) : null,
                'end_date'    => $this->input->post('end_date9') ? $this->sma->fsd(trim($this->input->post('end_date9'))) : null,
                'created_date' => date( 'Y-m-d H:i:s'),
                'revised_date' => date( 'Y-m-d H:i:s'),
                'offer_product_details' => 1
                );
                //$this->sma->print_arrays($_POST,$data);

                //



                    }

                if($this->input->post('promo_type_id')=='8'){



                     $data = array(
                'promo_type_id' => $this->input->post('promo_type_id'),
                'promo_name' => $this->input->post('promo_name'),
                'offer_category' => $this->input->post('offer_category8'),
                //'offer_product' => $this->input->post('offer_product8')[0],
                'percentage_off' => $this->input->post('percentage_off8'),
                'status' => $this->input->post('status'),
                'start_date'  => $this->input->post('start_date8') ? $this->sma->fsd(trim($this->input->post('start_date8'))) : null,
                'end_date'    => $this->input->post('end_date8') ? $this->sma->fsd(trim($this->input->post('end_date8'))) : null,
                'created_date' => date( 'Y-m-d H:i:s'),
                'revised_date' => date( 'Y-m-d H:i:s'),
                'offer_product_details' => 1
                );

                }


                if($this->input->post('promo_type_id')=='11'){



                     $data = array(
                'promo_type_id' => $this->input->post('promo_type_id'),
                'promo_name' => $this->input->post('promo_name'),
                'offer_category' => $this->input->post('offer_category11'),
                //'offer_product' => $this->input->post('offer_product11')[0],
                'fixed_off' => $this->input->post('fixed_off11'),
                'status' => $this->input->post('status'),
                'start_date'  => $this->input->post('start_date11') ? $this->sma->fsd(trim($this->input->post('start_date11'))) : null,
                'end_date'    => $this->input->post('end_date11') ? $this->sma->fsd(trim($this->input->post('end_date11'))) : null,
                'created_date' => date( 'Y-m-d H:i:s'),
                'revised_date' => date( 'Y-m-d H:i:s'),
                'offer_product_details' => 1
                );

                }

                //$this->sma->print_arrays($_POST,$data);


				$this->db->insert('promo', $data);
                $promo_id = $this->db->insert_id();
                if($this->input->post('promo_type_id')=='7'){
                foreach ($this->input->post('offer_product7') as $offer_product) {

                            $this->db->insert('promo_details', ['promo_id' => $promo_id, 'offer_product' => $offer_product, 'promo_type_id' => $this->input->post('promo_type_id')]);
                }


                }

                if($this->input->post('promo_type_id')=='9'){
                    foreach ($this->input->post('offer_product9') as $offer_product) {

                                $this->db->insert('promo_details', ['promo_id' => $promo_id, 'offer_product' => $offer_product, 'promo_type_id' => $this->input->post('promo_type_id')]);
                    }


                    }

            if($this->input->post('promo_type_id')=='8'){
                foreach ($this->input->post('offer_product8') as $offer_product) {

                            $this->db->insert('promo_details', ['promo_id' => $promo_id, 'offer_product' => $offer_product, 'promo_type_id' => $this->input->post('promo_type_id')]);
                }


            }

            if($this->input->post('promo_type_id')=='11'){
                foreach ($this->input->post('offer_product11') as $offer_product) {

                            $this->db->insert('promo_details', ['promo_id' => $promo_id, 'offer_product' => $offer_product, 'promo_type_id' => $this->input->post('promo_type_id')]);
                }


            }

            if($this->input->post('offer_category4')){

                $productsArr=$this->promo_model->getCategoryProducts($this->input->post('offer_category4'));
				foreach($productsArr as $item){

						$product = [
                        'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category4'),
			'offer_product' => $item->id,
            'buy_quantity' => $this->input->post('buy_quantity4') ? $this->input->post('buy_quantity4') : 1,
			'percentage_off' => $this->input->post('percentage_off4'),
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date4') ? $this->sma->fsd(trim($this->input->post('start_date4'))) : null,
            'end_date'    => $this->input->post('end_date4') ? $this->sma->fsd(trim($this->input->post('end_date4'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),
            'hide_opt' => 1,
			'promo_id' => $promo_id
						];

                    $data1[] = ($product);


				}

				$this->db->insert_batch('promo', $data1);
			}


            if($this->input->post('offer_category10')){

                $productsArr=$this->promo_model->getCategoryProducts($this->input->post('offer_category10'));
				foreach($productsArr as $item){

						$product = [
                        'promo_type_id' => $this->input->post('promo_type_id'),
			'promo_name' => $this->input->post('promo_name'),
			'offer_category' => $this->input->post('offer_category10'),
			'offer_product' => $item->id,
            'buy_quantity' => $this->input->post('buy_quantity10') ? $this->input->post('buy_quantity10') : 1,
			'fixed_off' => $this->input->post('fixed_off10'),
			'status' => $this->input->post('status'),
			'start_date'  => $this->input->post('start_date10') ? $this->sma->fsd(trim($this->input->post('start_date10'))) : null,
            'end_date'    => $this->input->post('end_date10') ? $this->sma->fsd(trim($this->input->post('end_date10'))) : null,
            'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s'),
            'hide_opt' => 1,
			'promo_id' => $promo_id
						];

                    $data1[] = ($product);


				}

				$this->db->insert_batch('promo', $data1);
			}else if($this->input->post('promo_type_id')=='10' && $this->input->post('offer_product10_2')){
                for($i=2;$i<=$this->input->post('su10');$i++){
                $product = [
                    'promo_type_id' => $this->input->post('promo_type_id'),
        'promo_name' => $this->input->post('promo_name'),
        'offer_category' => $this->input->post('offer_category10'),
        'offer_product' => $this->input->post('offer_product10_'.$i.''),
        'buy_quantity' => $this->input->post('buy_'.$i.'_quantity10') ? $this->input->post('buy_'.$i.'_quantity10') : 1,
        'fixed_off' => $this->input->post('fixed_'.$i.'_off10'),
        'status' => $this->input->post('status'),
        'start_date'  => $this->input->post('start_date10') ? $this->sma->fsd(trim($this->input->post('start_date10'))) : null,
        'end_date'    => $this->input->post('end_date10') ? $this->sma->fsd(trim($this->input->post('end_date10'))) : null,
        'created_date' => date( 'Y-m-d H:i:s'),
        'revised_date' => date( 'Y-m-d H:i:s'),
        'hide_opt' => 1,
        'promo_id' => $promo_id,
        'promo_sr' => $i,
                    ];
                    $data1[] = ($product);
                }





                $this->db->insert_batch('promo', $data1);
            }
			//}



		}

		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('promo')), array('link' => '#', 'page' => lang('add_promo')));
            $meta = array('page_title' => lang('add_promo'), 'bc' => $bc);
			$this->data['promo'] = $promo;
			$this->data['promo_type'] = $promo_type;
			$this->data['promo_categories'] = $promo_categories;
			$this->data['gift_product'] = $gift_product;

				if(!$this->input->post('promo_name')){
      $this->page_construct('promo/add_promo', $meta, $this->data);
			}else{
				redirect(admin_url('promo/promos'));

				}

	}



	function add_promo_code($id = NULL)
    {
		$promo_code = $this->site->getPromo_codeByID($id);
		$promo_type = $this->promo_model->getActivepromo_type();
		$promos = $this->promo_model->getActivepromo();
        $this->sma->checkPermissions();
        $this->load->helper('security');
		 $this->form_validation->set_rules('promo_codee', lang("promo_codee"), 'required');
		if ($this->form_validation->run() == true) {
			$promo_type_id = $this->promo_model->getpromo_typebyid($this->input->post('promo_id'));
			//$date = date_create_from_format('j-M-Y', $this->input->post('start_date'));
			//$start_date = date_format($date, 'Y-m-d');
            $data = array(
			'promo_type_id' => $promo_type_id,
			'promo_id' => $this->input->post('promo_id'),
			'promo_code' => $this->input->post('promo_codee'),
			'total_uses_limit' => $this->input->post('total_uses_limit'),
			'per_user_uses_limit' => $this->input->post('per_user_uses_limit'),
			'start_date' => $this->input->post('start_date'),
			'end_date' => $this->input->post('end_date'),
			'status' => $this->input->post('status'),
			'created_date' => date( 'Y-m-d H:i:s'),
			'revised_date' => date( 'Y-m-d H:i:s')
			);
			$this->db->insert('promo_code', $data);
		}

		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('promo')), array('link' => '#', 'page' => lang('add_promo_code')));
            $meta = array('page_title' => lang('add_promo_code'), 'bc' => $bc);
			$this->data['promo_type'] = $promo_type;
			$this->data['promo_code'] = $promo_code;
			$this->data['promos'] = $promos;

			if(!$this->input->post('promo_codee')){
      $this->page_construct('promo/add_promo_code', $meta, $this->data);
			}else{
				redirect(admin_url('promo/promocode/'));

				}

	}




function promocode()
    {
		if ( ! $this->loggedIn) {
            admin_redirect('login');
        }
        if ( ! $this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin/welcome');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

       // $this->data['promo_type'] = $this->promo_model->getAllpromo_type();
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('promocode')), array('link' => '#', 'page' => lang('promocode')));
            $meta = array('page_title' => lang('promocode'), 'bc' => $bc);
            $this->page_construct('promo/promocode', $meta, $this->data);


    }


	function getPromocode()
	{

        if ( ! $this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            $this->sma->md();
        }

        $this->load->library('datatables');
        $this->datatables
            ->select("id, (select promo_name from sma_promo where id=promo_id),promo_code,status")
            ->from("promo_code")
         ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"> <a href='" . admin_url('promo/delete_code/$1') . "' class='tip' title='" . lang("delete_code") . "'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        if (!$this->Owner) {
            $this->datatables->unset_column('id');
        }
        echo $this->datatables->generate();

	}

	function delete_code($id = NULL)
    {

					$id=$this->uri->segment('4');
                    $this->promo_model->deletecode($id);
                    $this->session->set_flashdata('message', $this->lang->line("promo_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);


    }


    function addByAjax($category_id = NULL)
    {



    }


/*------------------------------------------------------------------------------- */

    function delete($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $product = $this->promo_model->getProductByID($id)->code;
        $this->promo_model->deleteExistingProductByCode($product);

        if ($this->promo_model->deleteProduct($id)) {

            if($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("promo_deleted")));
            }
            $this->session->set_flashdata('message', lang('promo_deleted'));
            admin_redirect('welcome');
        }

    }

    /* ----------------------------------------------------------------------------- */




    /* --------------------------------------------------------------------------------------------- */



function delete_promo_type($id = NULL)
    {

					$id=$this->uri->segment('4');
                    $this->promo_model->deletePromo_type($id);
                    $this->session->set_flashdata('message', $this->lang->line("promo_type_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);


    }


    function promo_actions($wh = NULL)
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'sync_quantity') {

                    foreach ($_POST['val'] as $id) {
                        $this->site->syncQuantity(NULL, NULL, NULL, $id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("promo_quantity_sync"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->promo_model->deleteProduct($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("promo_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'labels') {

                    foreach ($_POST['val'] as $id) {
                        $row = $this->promo_model->getProductByID($id);
                        $selected_variants = false;
                        if ($variants = $this->promo_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }

                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promo'), 'page' => lang('promo')), array('link' => '#', 'page' => lang('print_barcodes')));
                    $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
                    $this->page_construct('promo/print_barcodes', $meta, $this->data);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('promo');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('barcode_symbology'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('brand'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('category_code'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('sale').' '.lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('purchase').' '.lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('cost'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('alert_quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('L1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('M1', lang('tax_method'));
                    $this->excel->getActiveSheet()->SetCellValue('N1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('O1', lang('subcategory_code'));
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('promo_variants'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('pcf1'));
                    $this->excel->getActiveSheet()->SetCellValue('R1', lang('pcf2'));
                    $this->excel->getActiveSheet()->SetCellValue('S1', lang('pcf3'));
                    $this->excel->getActiveSheet()->SetCellValue('T1', lang('pcf4'));
                    $this->excel->getActiveSheet()->SetCellValue('U1', lang('pcf5'));
                    $this->excel->getActiveSheet()->SetCellValue('V1', lang('pcf6'));
                    $this->excel->getActiveSheet()->SetCellValue('W1', lang('quantity'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $product = $this->promo_model->getProductDetail($id);
                        $brand = $this->site->getBrandByID($product->brand);
                        $base_unit = $sale_unit = $purchase_unit = '';
                        if($units = $this->site->getUnitsByBUID($product->unit)) {
                            foreach($units as $u) {
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
                        $variants = $this->promo_model->getProductOptions($id);
                        $promo_variants = '';
                        if ($variants) {
                            foreach ($variants as $variant) {
                                $promo_variants .= trim($variant->name) . '|';
                            }
                        }
                        $quantity = $product->quantity;
                        if ($wh) {
                            if($wh_qty = $this->promo_model->getProductQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
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
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $promo_variants);
                        $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $product->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('R' . $row, $product->cf2);
                        $this->excel->getActiveSheet()->SetCellValue('S' . $row, $product->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('T' . $row, $product->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('U' . $row, $product->cf5);
                        $this->excel->getActiveSheet()->SetCellValue('V' . $row, $product->cf6);
                        $this->excel->getActiveSheet()->SetCellValue('W' . $row, $quantity);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'promo_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_promo_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin/promo');
        }
    }

    function findObjectById($id,$wh_id){

        foreach ( $wh_id as $element ) {

                    if ( $id == $element) {
                return true;
            }
        }

        return false;
    }

/*
    public function getClientInfo(){
        ini_set('display_errors', 1);

        $otherdb = $this->load->database('info', true);
            //$clients = $otherdb->get('client')->row();
            //$otherdb->where('isAccounting',1);
           // $otherdb->where('id <=',748);
			$q = $otherdb->get('client');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $clients[] = $row;
            }
		}
        foreach ($clients as $client) {


			$company = $client->company;
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = 'cpesoft_'.$company;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $company;
			$config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = 'sma_';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = FALSE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = '';
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = FALSE;

            $CI = &get_instance();
            $data = $CI->load->database($config, TRUE);
			$this->mydb =$data;
			//$this->accounts_model->getAccountItemByLevel();
			//$this->sma->print_arrays($this->getAccountItemByLevel($this->mydb));
			//$this->getAccountItemByLevel($this->mydb);

			//$this->mydb->insert();

			//$this->mydb->update('permissions', ['sales-register_report' => 1]);

            $this->mydb->query("ALTER TABLE sma_promo_type ADD promo_type_ar VARCHAR(255) NOT NULL");
            $this->mydb->query("UPDATE sma_promo_type SET promo_type_ar = 'اشتر منتج واحصل على منتج مجانا' , promo_type='Buy Product Get Product free'  WHERE sma_promo_type.id = 3");
            $this->mydb->query("UPDATE sma_promo_type SET promo_type_ar = 'الخصم بالنسبة' WHERE sma_promo_type.id = 4");
            $this->mydb->query("UPDATE sma_promo_type SET status=0 where id=1 or id=2  or id=5  or id=6");
            $this->mydb->query("ALTER TABLE sma_promo CHANGE offer_product offer_product VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE free_product free_product VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE gift_category gift_category VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE gift_product gift_product VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE percentage_off percentage_off VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE sel_option sel_option VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE cashback cashback VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE daily_start_time daily_start_time VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE promo_image promo_image VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
            $this->mydb->query("INSERT INTO sma_promo_type (id, promo_type, status, active, created_date, revised_date, promo_type_ar) VALUES (NULL, 'Buy some Get Product free', '1', '1', '2021-05-21 06:43:51', '2023-05-15 19:14:27', 'اشتر منتجات واحصل على منتج مجانا'), (NULL, 'Percentage Discount for some Products', '1', '1', '2021-05-21 06:44:16', '2021-05-21 06:44:16', 'الخصم بالنسبة لأكثر من منتج')");
            $this->mydb->query("CREATE TABLE IF NOT EXISTS sma_promo_details (id int(11) NOT NULL AUTO_INCREMENT,promo_id int(11) NOT NULL,offer_product varchar(255) COLLATE utf8_unicode_ci NOT NULL,promo_type_id int(11) NOT NULL,PRIMARY KEY (id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
            $this->mydb->query("ALTER TABLE sma_promo ADD offer_product_details TINYINT(1) NOT NULL DEFAULT '0' ");

        }
    }

*/

}
