<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sync extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->load->library('form_validation');
        $this->load->admin_model('sync_model');
        $this->load->admin_model('sales_model');
        $this->load->admin_model('db_model');
        $this->load->admin_model('accounts_model');

    }

    public function dashboard()
	{
		//$this->sma->checkPermissions('dashboard_acc',true);
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
		$this->page_construct('syncdb/dashboard', $meta, $this->data);
	}


    public function electronic_linking()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['foodics'] = $this->Settings->isFoodics ? $this->getElectronicLinkingInfo(1) : null;
        $this->data['settings']        = $this->sync_model->getFoodicsSettings();
        //$this->sma->print_arrays($this->data['foodics']);
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('sync'), 'page' => lang('sync')], ['link' => '#', 'page' => lang('Supported_linking_services')]];
        $meta = ['page_title' => lang('Supported_linking_services'), 'bc' => $bc];
        $this->page_construct('syncdb/electronic_linking', $meta, $this->data);
    }

    public function getElectronicLinkingInfo($id= null){


        $otherdb = $this->load->database('info', true);
            //$clients = $otherdb->get('client')->row();
            if($id)
            $otherdb->where('id',$id);
           // $otherdb->where('id <=',748);
			$q = $otherdb->get('electronic_linking');
        if ($q->num_rows() > 0) {
            return $q->row();
            /*foreach (($q->result()) as $row) {
                $clients[] = $row;
            }
            return $clients;*/
		}
        return false;

    }

    public function subscription_settings()
    {


        $this->form_validation->set_rules('client_id', lang('client_id'), 'required');





        if ($this->form_validation->run() == true && !empty($this->input->post('access_token'))) {


            $data = ['name' => $this->input->post('name'),
                'description'      => $this->input->post('description'),
                'client_id'      => $this->input->post('client_id'),
                'secret'      => $this->input->post('secret'),
                'scopes'      => $this->input->post('scopes'),

            ];
        } elseif ($this->input->post('subscription_settings')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('sync/electronic_linking');
        }


        if ($this->form_validation->run() == true && $this->sync_model->updateFoodicsSetting($data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect('sync/electronic_linking');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));


			$this->data['settings']        = $this->sync_model->getFoodicsSettings();
            //$this->sma->print_arrays($this->data['settings']);

            //$this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'syncdb/subscription_settings', $this->data);
        }
    }



    public function delete_extra_tables()
    {
        if ($this->sync_model->deleteExtraTables()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function import_billers()
    {
        if ($this->sync_model->importBillers()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function import_customers()
    {

        $latest_sync_customers=$this->sync_model->getFoodicsSettings()->latest_sync_customers;

        //$latest_sync_customers=$latest_sync_customers->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_customers]);

    if($latest_sync_customers){
        $dateTime = new DateTime($latest_sync_customers);

	    $latest_sync_customers= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = 'https://api-sandbox.foodics.com/v5/customers?filter[is_deleted]=false&filter[updated_after]='.$latest_sync_customers.'&sort=updated_at';
    }else{
        $url = 'https://api-sandbox.foodics.com/v5/customers?filter[is_deleted]=false&sort=updated_at';
    }

        //$url = 'https://api-sandbox.foodics.com/v5/customers?filter[is_deleted]=false';

        /* Init cURL resource */
        $ch = curl_init($url);


        $headers = [];
        $headers[] = 'Content-Type:application/json';
        $headers[] = 'Accept:application/json';
        $token = $this->sync_model->getFoodicsSettings()->access_token;
        $headers[] = "Authorization: Bearer ".$token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


        /* set return type json */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /* execute request */
        $result = json_decode(curl_exec($ch));

        /* close cURL resource */
        curl_close($ch);
        if($result->meta){
        if($result->data){
        $id   = $this->accounts_model->getSettings()->accounts_local_customers_acct;
        $account_type = "local_customers";
        $accountSide = 'Debit';
        $i=0;
              foreach($result->data as $dataInfo){

            $account_count = ($this->accounts_model->getCountAccountByID($id)->count + $i);
            $account_details = $this->accounts_model->getAccountByID($id);
            if (strlen($account_details->accountID) <= 1)
                $accountID = $account_details->accountID . "" . $account_count;
            else
                $accountID = $account_details->accountID . "0" . $account_count;


            $level = ($account_details->level) + 1;




            if (!$this->accounts_model->getAccountByUUID($dataInfo->id)) {
                $updated_at=$dataInfo->updated_at;
                $data = [
                    'accountID'            => $accountID,
                    'accountName'            => $dataInfo->name,
                    'accountNameEn'            => $dataInfo->name,
                    'master_account'            => 0,
                    'accountCategory'            => $account_details->accountCategory,
                    'parent_id'            => $id,
                    'accountSide'            => $accountSide,
                    'accountStatement'            => 'Balance Sheet',
                    'account_type'            => $account_type,
                    'account_ref_uuid'            => $dataInfo->id,
                    'userID'   => $this->session->userdata('user_id'),
                    'costCenterId'            => json_encode((array) $_POST['costCenterId']),
                    'costCenterPercentage'            => json_encode((array) $_POST['costCenterPercentage']),
                    'level'            => $level,


                ];

                $dataFinal[] = $data;
                $i++;
                }



              }

            if ($this->accounts_model->addAccountBatch($dataFinal,"customers",$updated_at) && !empty($dataFinal)) {
                //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
                //$this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
                $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

                //$this->sma->send_json([ 'msg' => $result->data]);
            }
            //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
        }
            $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

//            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
        }


            //$this->sma->send_json([ 'msg' => $dataFinal]);
            die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
          // $this->sma->send_json([ 'msg' => '<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
           //$this->sma->print_arrays('<main><nav class="pagination-container"><button class="pagination-button" id="prev-button" aria-label="Previous page" title="Previous page">&lt;</button><div id="pagination-numbers"></div><button class="pagination-button" id="next-button" aria-label="Next page" title="Next page">&gt;</button></nav></main>');
    }

    public function import_suppliers()
    {


        $latest_sync_suppliers=$this->sync_model->getFoodicsSettings()->latest_sync_suppliers;

        //$latest_sync_suppliers=$latest_sync_suppliers->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_suppliers]);

    if($latest_sync_suppliers){
        $dateTime = new DateTime($latest_sync_suppliers);

	    $latest_sync_suppliers= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = 'https://api-sandbox.foodics.com/v5/suppliers?filter[is_deleted]=false&filter[updated_after]='.$latest_sync_suppliers.'&sort=updated_at';
    }else{
        $url = 'https://api-sandbox.foodics.com/v5/suppliers?filter[is_deleted]=false&sort=updated_at';
    }

        //$url = 'https://api-sandbox.foodics.com/v5/suppliers?filter[is_deleted]=false';

        /* Init cURL resource */
        $ch = curl_init($url);


        $headers = [];
        $headers[] = 'Content-Type:application/json';
        $headers[] = 'Accept:application/json';
        $token = $this->sync_model->getFoodicsSettings()->access_token;
        $headers[] = "Authorization: Bearer ".$token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


        /* set return type json */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /* execute request */
        $result = json_decode(curl_exec($ch));

        /* close cURL resource */
        curl_close($ch);
        if($result->meta){
            if($result->data){
        $id   = $this->accounts_model->getSettings()->accounts_local_suppliers_acct;
        $account_type = "local_suppliers";
        $accountSide = 'Credit';
        $i=0;
              foreach($result->data as $dataInfo){

            $account_count = ($this->accounts_model->getCountAccountByID($id)->count + $i);
            $account_details = $this->accounts_model->getAccountByID($id);
            if (strlen($account_details->accountID) <= 1)
                $accountID = $account_details->accountID . "" . $account_count;
            else
                $accountID = $account_details->accountID . "0" . $account_count;


            $level = ($account_details->level) + 1;




            if (!$this->accounts_model->getAccountByUUID($dataInfo->id)) {
                $updated_at=$dataInfo->updated_at;
                $data = [
                    'accountID'            => $accountID,
                    'accountName'            => $dataInfo->name,
                    'accountNameEn'            => $dataInfo->name,
                    'master_account'            => 0,
                    'accountCategory'            => $account_details->accountCategory,
                    'parent_id'            => $id,
                    'accountSide'            => $accountSide,
                    'accountStatement'            => 'Balance Sheet',
                    'account_type'            => $account_type,
                    'account_ref_uuid'            => $dataInfo->id,
                    'userID'   => $this->session->userdata('user_id'),
                    'costCenterId'            => json_encode((array) $_POST['costCenterId']),
                    'costCenterPercentage'            => json_encode((array) $_POST['costCenterPercentage']),
                    'level'            => $level,


                ];

                $dataFinal[] = $data;
                $i++;
                }



              }

              if ($this->accounts_model->addAccountBatch($dataFinal,"suppliers",$updated_at) && !empty($dataFinal)) {
                //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
                //$this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
                $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

                //$this->sma->send_json([ 'msg' => $result->data]);
            }
            //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
        }
            $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

//            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
        }


            //$this->sma->send_json([ 'msg' => $dataFinal]);
            die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
          // $this->sma->send_json([ 'msg' => '<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
           //$this->sma->print_arrays('<main><nav class="pagination-container"><button class="pagination-button" id="prev-button" aria-label="Previous page" title="Previous page">&lt;</button><div id="pagination-numbers"></div><button class="pagination-button" id="next-button" aria-label="Next page" title="Next page">&gt;</button></nav></main>');
    }

    public function index_old()
    {
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc                  = [['link' => '#', 'page' => 'sync database']];
        $meta                = ['page_title' => 'sync database', 'bc' => $bc];
        $this->page_construct('syncdb/index', $meta, $this->data);
    }

    public function foodics_sync()
    {
        //$this->sma->checkPermissions('dashboard_acc',true);
        $this->data['settings']        = $this->sync_model->getFoodicsSettings();
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc                  = [['link' => '#', 'page' => lang('Synchronize_with_Foodics')]];
        $meta                = ['page_title' => lang('Synchronize_with_Foodics'), 'bc' => $bc];
        $this->page_construct('syncdb/foodics_sync', $meta, $this->data);
    }

    public function reset_damage_products()
    {
        if ($this->sync_model->resetDamageProductsTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function reset_deliveries()
    {
        if ($this->sync_model->resetDeliveriesTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function reset_products()
    {
        if ($this->sync_model->resetProductsTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function reset_purchases()
    {
        if ($this->sync_model->resetPurchasesTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function reset_quotes()
    {
        if ($this->sync_model->resetQuotesTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function reset_sales()
    {
        if ($this->sync_model->resetSalesTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function reset_transfers()
    {
        if ($this->sync_model->resetTransfersTable()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function update_purchases()
    {
        $latest_sync_purchase=$this->sync_model->getFoodicsSettings()->latest_sync_purchase;

        //$latest_sync_purchase=$latest_sync_purchase->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_purchase]);

    if($latest_sync_purchase){
        $dateTime = new DateTime($latest_sync_purchase);

	    $latest_sync_purchase= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = 'https://api-sandbox.foodics.com/v5/inventory_transactions?filter[status]=4&filter[type]=1,4&filter[updated_after]='.$latest_sync_purchase.'&sort=updated_at&include=items,branch,supplier';
    }else{
        $url = 'https://api-sandbox.foodics.com/v5/inventory_transactions?filter[status]=4&filter[type]=1,4&sort=updated_at&include=items,branch,supplier';
    }

    //
    /* Init cURL resource */
    $ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';
    $token = $this->sync_model->getFoodicsSettings()->access_token;
    $headers[] = "Authorization: Bearer ".$token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    /* set return type json */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* execute request */
    $result = json_decode(curl_exec($ch));

    /* close cURL resource */
    curl_close($ch);


    //$this->sma->send_json([ 'msg' => $result->data]);

    if($result->meta){
        if($result->data){
            foreach($result->data as $dataInfo){
            if (!$this->accounts_model->getEntriesByUUID($dataInfo->id)) {
            $entryDescription="Purchase_Invoice_No#".$dataInfo->reference;


        if ($dataInfo->type=="1"){


                $entry_type="Purchase_invoice_received";
                $entryDebitAccount= [];

                if($this->Settings->accounting_type=="periodic"){
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_acct;
                }else{
                    $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->branch->id)->id;
               // $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
            if($entryWarehouseAcc==""){
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                }
            }
        $entryDebitAccount[] =  $entryWarehouseAcc;

        $entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

        $final_item_cost_value=0;
                foreach($dataInfo->items as $product){

                    $final_item_cost_value=($final_item_cost_value+$product->pivot->cost);


                }
                $data['grand_total']=($dataInfo->paid_tax + $final_item_cost_value);

        $entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal($final_item_cost_value);
                    if($dataInfo->paid_tax>0)
					$entryDebitBalance[] =  $this->sma->formatDecimal($dataInfo->paid_tax);

                    $entryCreditAccount=$this->accounts_model->getAccountByUUID($dataInfo->supplier->id)->id;
						if($entryCreditAccount=="")
						$entryCreditAccount=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;


                    $costCenterDebitId= [];
					$costCenterDebitAmount= [];

                    $this->accounts_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$this->sma->formatDecimal($data['grand_total']),$dataInfo->reference,$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id,"purchases");





        }else if ($dataInfo->type=="4"){

            $entryDescription="Purchase_Returned_Invoice_No#".$dataInfo->reference;
                $entry_type="purchase_returned";
                $entryDebitAccount= [];

                if($this->Settings->accounting_type=="periodic"){
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->purchases_return_acct;
                }else{
                    $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->branch->id)->id;
               // $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
            if($entryWarehouseAcc==""){
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                }
            }
        $entryDebitAccount[] =  $entryWarehouseAcc;

        $entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

        $final_item_cost_value=0;
                foreach($dataInfo->items as $product){

                    $final_item_cost_value=($final_item_cost_value+$product->pivot->cost);


                }
                $entryCreditAccount= [];
                $entryCreditBalance= [];


					if(abs($dataInfo->additional_cost)>0){
					$entryCreditAccount[] = $this->site->getAccountsSettings()->accounts_shipping_return_losses;
						$entryCreditBalance[] =  $this->sma->formatDecimal(abs($dataInfo->additional_cost));

					}

        $entryDebitBalance= [];
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($final_item_cost_value) + abs($dataInfo->additional_cost));
                    if(abs($dataInfo->paid_tax)>0)
					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($dataInfo->paid_tax));

                    $entryCreditAcc=$this->accounts_model->getAccountByUUID($dataInfo->supplier->id)->id;
						if($entryCreditAcc=="")
						$entryCreditAcc=$this->site->getAccountsSettings()->accounts_local_suppliers_acct;
                        $entryCreditAccount[] =  $entryCreditAcc;

                        $data['grand_total']=(abs($dataInfo->paid_tax) + $final_item_cost_value);
                $entryCreditBalance[] =  $this->sma->formatDecimal(abs($data['grand_total']));

                    $costCenterDebitId= [];
					$costCenterDebitAmount= [];

                    $this->accounts_model->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$dataInfo->reference,$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id,"purchases");





        }



    }
          }

          $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

          //$this->sma->send_json([ 'msg' => $result->data]);
      }
      $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

      //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
          }


              //$this->sma->send_json([ 'msg' => $dataFinal]);
              die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
      //$this->sma->send_json([ 'msg' => $dataFinal]);
     // die('<i class="fa fa-times"></i> Failed!');

  }

  public function update_deposits()
  {
    $latest_sync_account_transactions=$this->sync_model->getFoodicsSettings()->latest_sync_account_transactions;

    //$latest_sync_account_transactions=$latest_sync_account_transactions->format(DATE_ISO8601);
    //$this->sma->send_json([ 'msg' => $latest_sync_account_transactions]);

if($latest_sync_account_transactions){
    $dateTime = new DateTime($latest_sync_account_transactions);

    $latest_sync_account_transactions= $dateTime->format('Y-m-d\TH:i:s\Z');
    $url = 'https://api-sandbox.foodics.com/v5/house_account_transactions?filter[updated_after]='.$latest_sync_account_transactions.'&sort=updated_at&include=customer,order';
}else{
    $url = 'https://api-sandbox.foodics.com/v5/house_account_transactions?sort=updated_at&include=customer,order';
}


  //
  /* Init cURL resource */
  $ch = curl_init($url);


  $headers = [];
  $headers[] = 'Content-Type:application/json';
  $headers[] = 'Accept:application/json';
  $token = $this->sync_model->getFoodicsSettings()->access_token;
  $headers[] = "Authorization: Bearer ".$token;
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


  /* set return type json */
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  /* execute request */
  $result = json_decode(curl_exec($ch));

  /* close cURL resource */
  curl_close($ch);


  //$this->sma->send_json([ 'msg' => $result]);

  if($result->meta){
      if($result->data){
          foreach($result->data as $dataInfo){
          if (!$this->accounts_model->getEntriesByUUID($dataInfo->id) && empty($dataInfo->order)) {
          $entryDescription="Customer_deposits# - ".$dataInfo->customer->name;




              $entry_type="deposits";

              $entryCreditAccount= [];
			  $entryCreditBalance= [];


              $entryDebitAccount= [];
					$entryDebitAcc1 =  $this->accounts_model->getPaymentMethodsByUUID($dataInfo->payment_method_id)->payment_account;
                    if($entryDebitAcc1=="")
                    $entryDebitAcc1=$this->accounts_model->getSettings()->accounts_main_boxes_acct;
                    $entryDebitAccount[] =  $entryDebitAcc1;
                    $entryDebitBalance= [];

					$entryDebitBalance[] =  $this->sma->formatDecimal(abs($dataInfo->amount));


                    if($dataInfo->customer->id){

                        $entryDebitAcc=$this->accounts_model->getAccountByUUID($dataInfo->customer->id)->id;
                           }else{
                            $entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
                           }

                           $entryCreditAccount[] =  $entryDebitAcc;

					 $entryCreditBalance[] =  $this->sma->formatDecimal(abs($dataInfo->amount));



                     $this->accounts_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$dataInfo->id,$entry_type,null,null,null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id,"deposits");












  }
        }

        $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

        //$this->sma->send_json([ 'msg' => $result->data]);
    }
    $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

    //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
        }


            //$this->sma->send_json([ 'msg' => $dataFinal]);
            die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');

    //$this->sma->send_json([ 'msg' => $dataFinal]);
   // die('<i class="fa fa-times"></i> Failed!');

}


    public function update_quotes()
    {
        if ($this->sync_model->updateQuotes()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

    public function update_sales()
    {

        $latest_sync_order=$this->sync_model->getFoodicsSettings()->latest_sync_order;

        //$latest_sync_order=$latest_sync_order->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_order]);

    if($latest_sync_order){
        $dateTime = new DateTime($latest_sync_order);

	    $latest_sync_order= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = 'https://api-sandbox.foodics.com/v5/orders?filter[status]=4,5&filter[updated_after]='.$latest_sync_order.'&sort=updated_at&include=branch,customer,payments.payment_method,charges,charges.taxes,products,products.taxes';
    }else{
        $url = 'https://api-sandbox.foodics.com/v5/orders?filter[status]=4,5&sort=updated_at&include=branch,customer,payments.payment_method,charges,charges.taxes,products,products.taxes';
    }

    //
    /* Init cURL resource */
    $ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';
    $token = $this->sync_model->getFoodicsSettings()->access_token;
    $headers[] = "Authorization: Bearer ".$token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    /* set return type json */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* execute request */
    $result = json_decode(curl_exec($ch));

    /* close cURL resource */
    curl_close($ch);


    //$this->sma->send_json([ 'msg' => $result->data]);
    if($result->meta){
        if($result->data){
          foreach($result->data as $dataInfo){
            if (!$this->accounts_model->getEntriesByUUID($dataInfo->id)) {
            $entryDescription="Sales_Invoice_No#".$dataInfo->reference;

            $total_shipping=0;
            $total_tax=0;
            foreach($dataInfo->charges as $charge){
                if($charge->taxes)
                $total_tax=($total_tax+$charge->taxes[0]->pivot->amount);
                $total_shipping=($total_shipping+$charge->tax_exclusive_amount);

            }
            $data['shipping'] = $this->sma->formatDecimal($total_shipping ? $total_shipping : 0);
            $data['grand_total'] = $this->sma->formatDecimal($dataInfo->total_price);

            foreach($dataInfo->products as $product){

                $total_tax=($total_tax+$product->taxes[0]->pivot->amount);

            }

            $data['total_tax'] = $this->sma->formatDecimal($total_tax);


            //$this->sma->send_json([ 'msg' => $data]);



        if ($dataInfo->status=="4"){


                $entry_type="sale_completed";





                $entryCreditAccount= [];


                $entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_acct;


                if(abs($data['shipping'])>0)
                $entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_shipping_other_revenues;







                $entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

                $entryCreditBalance= [];

                $entryCreditBalance[] =  $this->sma->formatDecimal((($this->sma->formatDecimal($dataInfo->subtotal_price) + $this->sma->formatDecimal($dataInfo->rounding_amount))  - $this->sma->formatDecimal($dataInfo->tax_exclusive_discount_amount)));

                if(abs($data['shipping'])>0)
                $entryCreditBalance[] =  $this->sma->formatDecimal($data['shipping']);

                $entryCreditBalance[] =  $this->sma->formatDecimal(($this->sma->formatDecimal($data['grand_total'])-((($this->sma->formatDecimal($dataInfo->subtotal_price) + $this->sma->formatDecimal($dataInfo->rounding_amount))  - $this->sma->formatDecimal($dataInfo->tax_exclusive_discount_amount))+$this->sma->formatDecimal($data['shipping']))));
                //$entryCreditBalance[] =  $this->sma->formatDecimal($data['total_tax']);

                if($dataInfo->customer->id){

                $entryDebitAccount=$this->accounts_model->getAccountByUUID($dataInfo->customer->id)->id;
                   }else{
                    $entryDebitAccount=$this->site->getAccountsSettings()->accounts_local_customers_acct;
                   }

                $costCenterCreditId= [];
                $costCenterCreditAmount= [];






                //$this->sma->send_json([ 'msg' => $entryDebitAccount]);





                $this->accounts_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($data['grand_total']),$entryCreditBalance,$dataInfo->reference,$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$dataInfo->updated_at,$entryDescription,$dataInfo->id,"sales");





                    if($dataInfo->payments[0]->payment_method->type!=5){



                $entryDebitAccount= [];
                $entryDebitBalance= [];
                $amount=0;
                $final_commisson_amount=0;




                $entry_type="sale_completed";


                    if($dataInfo->payments[0]->payment_method->type==4)
                     $entryDebitAcc=$this->site->getAccountsSettings()->accounts_gift_card_payments;
                    else
                    $entryDebitAcc=$this->accounts_model->getPaymentMethodsByUUID($dataInfo->payments[0]->payment_method->id)->payment_account;

                    if($entryDebitAcc=="")
                    $entryDebitAcc=$this->accounts_model->getSettings()->accounts_main_boxes_acct;
                     $entryDebitAccount[] =  $entryDebitAcc;




                 //$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);

                 $entryDebitBalance[] =  $this->sma->formatDecimal($dataInfo->payments[0]->amount);
                 $amount=$amount+$dataInfo->payments[0]->amount;

                $commisson_amount=0;

















                $entryCreditAccount= [];
                $entryCreditBalance= [];

                //if($this->sma->formatDecimal($amount)<$this->sma->formatDecimal($data['grand_total'])){


                    if($dataInfo->customer->id){

                        $entryDebitAcc=$this->accounts_model->getAccountByUUID($dataInfo->customer->id)->id;
                           }else{
                            $entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
                           }


                $entryCreditAccount[] =  $entryDebitAcc;
                $entryCreditBalance[] =  $this->sma->formatDecimal($amount);




                //}



                $costCenterCreditId= [];
                $costCenterCreditAmount= [];




                $this->accounts_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$dataInfo->reference,"Sales_Payments",null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$dataInfo->updated_at,"Payments_Sales_Invoice_No#".$dataInfo->reference,$dataInfo->id);
                        }

//$this->sma->send_json([ 'msg' => $entryDebitAccount]);



            //}



                $final_item_cost_value=0;
                foreach($dataInfo->products as $product){

                    $final_item_cost_value=($final_item_cost_value+$product->total_cost);

                }




                if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
                        }else{


                    $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->branch->id)->id;
                    if($entryWarehouseAcc=="")
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                    //$this->sma->send_json([ 'msg' => $entryWarehouseAcc]);

                $this->accounts_model->createEntryMultiSide($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$entryWarehouseAcc,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$dataInfo->reference,"Cost_of_goods_sold",null,null,null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id);


            }



        }else if ($dataInfo->status=="5"){

            $entryDescription="Sales_Returned_Invoice_No#".$dataInfo->reference;
            $entry_type="sale_returned";





            $entryCreditAccount= [];


            $entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_return_acct;


            if(abs($data['shipping'])>0)
            $entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_shipping_other_revenues;







            $entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

            $entryCreditBalance= [];

            $entryCreditBalance[] =  $this->sma->formatDecimal((($this->sma->formatDecimal($dataInfo->subtotal_price) + $this->sma->formatDecimal($dataInfo->rounding_amount))  - $this->sma->formatDecimal($dataInfo->tax_exclusive_discount_amount)));

            if(abs($data['shipping'])>0)
            $entryCreditBalance[] =  $this->sma->formatDecimal($data['shipping']);

            $entryCreditBalance[] =  $this->sma->formatDecimal(($this->sma->formatDecimal($data['grand_total'])-((($this->sma->formatDecimal($dataInfo->subtotal_price) + $this->sma->formatDecimal($dataInfo->rounding_amount))  - $this->sma->formatDecimal($dataInfo->tax_exclusive_discount_amount))+$this->sma->formatDecimal($data['shipping']))));
            //$entryCreditBalance[] =  $this->sma->formatDecimal($data['total_tax']);

            if($dataInfo->customer->id){

            $entryDebitAccount=$this->accounts_model->getAccountByUUID($dataInfo->customer->id)->id;
               }else{
                $entryDebitAccount=$this->site->getAccountsSettings()->accounts_local_customers_acct;
               }

            $costCenterCreditId= [];
            $costCenterCreditAmount= [];






            //$this->sma->send_json([ 'msg' => $entryDebitAccount]);





            $this->accounts_model->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$this->sma->formatDecimal($data['grand_total']),$dataInfo->reference,$entry_type,null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$dataInfo->updated_at,$entryDescription,$dataInfo->id,"sales");









            $entryDebitAccount= [];
            $entryDebitBalance= [];
            $amount=0;
            $final_commisson_amount=0;




            $entry_type="sale_returned";



                 //$entryDebitAccount[] =  $this->site->getPaymentTypeByID($payment_type[$i]['payment_type'])->payment_account;
                 $entryDebitAcc=$this->accounts_model->getPaymentMethodsByUUID($dataInfo->payments[0]->payment_method->id)->payment_account;
                 if($entryDebitAcc=="")
                 $entryDebitAcc=$this->accounts_model->getSettings()->accounts_main_boxes_acct;

                 $entryDebitAccount[] =  $entryDebitAcc;




             //$entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);

             $entryDebitBalance[] =  $this->sma->formatDecimal(abs($dataInfo->payments[0]->amount));
             $amount=$amount+abs($dataInfo->payments[0]->amount);

            $commisson_amount=0;

















            $entryCreditAccount= [];
            $entryCreditBalance= [];

            //if($this->sma->formatDecimal($amount)<$this->sma->formatDecimal($data['grand_total'])){


                if($dataInfo->customer->id){

                    $entryDebitAcc=$this->accounts_model->getAccountByUUID($dataInfo->customer->id)->id;
                       }else{
                        $entryDebitAcc=$this->site->getAccountsSettings()->accounts_local_customers_acct;
                       }


            $entryCreditAccount[] =  $entryDebitAcc;
            $entryCreditBalance[] =  $this->sma->formatDecimal($amount);




            //}



            $costCenterCreditId= [];
            $costCenterCreditAmount= [];




            $this->accounts_model->createEntryMultiSide($entryCreditAccount,$entryDebitAccount,$entryCreditBalance,$entryDebitBalance,$dataInfo->reference,"sale_returned",null,null,json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$dataInfo->updated_at,"Sales_Returned_Invoice_No#".$dataInfo->reference,$dataInfo->id);


//$this->sma->send_json([ 'msg' => $entryDebitAccount]);



        //}



            $final_item_cost_value=0;
            foreach($dataInfo->products as $product){

                $final_item_cost_value=($final_item_cost_value+$product->total_cost);

            }




            if($this->Settings->accounting_type=="periodic" || $final_item_cost_value==0){
                    }else{


                $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->branch->id)->id;
                if($entryWarehouseAcc=="")
                $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                //$this->sma->send_json([ 'msg' => $entryWarehouseAcc]);

            $this->accounts_model->createEntryMultiSide($entryWarehouseAcc,$this->site->getAccountsSettings()->sales_cost_of_good_sold_acct,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$dataInfo->reference,"Cost_of_goods_sold",null,null,null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id);


        }



    }






    }
          }

//            die('<i class="fa fa-check"></i> Success!');
            $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

            //$this->sma->send_json([ 'msg' => $result->data]);
        }
        $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

        //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
            }


                //$this->sma->send_json([ 'msg' => $dataFinal]);
                die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
        //$this->sma->send_json([ 'msg' => $dataFinal]);
       // die('<i class="fa fa-times"></i> Failed!');

    }

    public function import_branches()
    {

        $latest_sync_branches=$this->sync_model->getFoodicsSettings()->latest_sync_branches;

        //$latest_sync_branches=$latest_sync_branches->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_branches]);

    if($latest_sync_branches){
        $dateTime = new DateTime($latest_sync_branches);

	    $latest_sync_branches= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = 'https://api-sandbox.foodics.com/v5/branches?filter[is_deleted]=false&filter[updated_after]='.$latest_sync_branches.'&sort=updated_at';
    }else{
        $url = 'https://api-sandbox.foodics.com/v5/branches?filter[is_deleted]=false&sort=updated_at';
    }


       // $url = 'https://api-sandbox.foodics.com/v5/branches?filter[is_deleted]=false';

    /* Init cURL resource */
    $ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';
    $token = $this->sync_model->getFoodicsSettings()->access_token;
    $headers[] = "Authorization: Bearer ".$token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    /* set return type json */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* execute request */
    $result = json_decode(curl_exec($ch));

    /* close cURL resource */
    curl_close($ch);
    if($result->meta){
        if($result->data){
    $id   = $this->accounts_model->getSettings()->warehouses_acct;
    $account_type = "warehouses";
    $accountSide = 'Debit';
    $i=0;
          foreach($result->data as $dataInfo){

        $account_count = ($this->accounts_model->getCountAccountByID($id)->count + $i);
		$account_details = $this->accounts_model->getAccountByID($id);
		if (strlen($account_details->accountID) <= 1)
			$accountID = $account_details->accountID . "" . $account_count;
		else
			$accountID = $account_details->accountID . "0" . $account_count;


		$level = ($account_details->level) + 1;




        if (!$this->accounts_model->getAccountByUUID($dataInfo->id)) {
            $updated_at=$dataInfo->updated_at;
            $data = [
                'accountID'            => $accountID,
                'accountName'            => $dataInfo->name,
                'accountNameEn'            => $dataInfo->name,
                'master_account'            => 0,
                'accountCategory'            => $account_details->accountCategory,
                'parent_id'            => $id,
                'accountSide'            => $accountSide,
                'accountStatement'            => 'Balance Sheet',
                'account_type'            => $account_type,
                'account_ref_uuid'            => $dataInfo->id,
                'userID'   => $this->session->userdata('user_id'),
                'costCenterId'            => json_encode((array) $_POST['costCenterId']),
                'costCenterPercentage'            => json_encode((array) $_POST['costCenterPercentage']),
                'level'            => $level,


            ];

            $dataFinal[] = $data;
            $i++;
            }



          }

          if ($this->accounts_model->addAccountBatch($dataFinal,"branches",$updated_at) && !empty($dataFinal)) {
            //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
            //$this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
            $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

            //$this->sma->send_json([ 'msg' => $result->data]);
        }
        //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
    }
        $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

//            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
    }


        //$this->sma->send_json([ 'msg' => $dataFinal]);
        die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
      // $this->sma->send_json([ 'msg' => '<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
       //$this->sma->print_arrays('<main><nav class="pagination-container"><button class="pagination-button" id="prev-button" aria-label="Previous page" title="Previous page">&lt;</button><div id="pagination-numbers"></div><button class="pagination-button" id="next-button" aria-label="Next page" title="Next page">&gt;</button></nav></main>');
}


public function import_settings()
    {



        $url = 'https://api-sandbox.foodics.com/v5/settings';

    /* Init cURL resource */
    $ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';
    $token = $this->sync_model->getFoodicsSettings()->access_token;
    $headers[] = "Authorization: Bearer ".$token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    /* set return type json */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* execute request */
    $result = json_decode(curl_exec($ch));

    /* close cURL resource */
    curl_close($ch);

        if($result->data){
            $data = [
                'business_timezone' => $result->data->business_timezone,

        ];
        $data2 = [
            'symbol' => $result->data->business_currency,

    ];
    }


    if ($this->sync_model->updateFoodicsSetting($data) && $this->sync_model->updateSetting($data2) && !empty($data)){

          die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
            //$this->sma->send_json([ 'msg' => $result->data]);

        //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
    }



        //$this->sma->send_json([ 'msg' => $dataFinal]);
        die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
      // $this->sma->send_json([ 'msg' => '<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
       //$this->sma->print_arrays('<main><nav class="pagination-container"><button class="pagination-button" id="prev-button" aria-label="Previous page" title="Previous page">&lt;</button><div id="pagination-numbers"></div><button class="pagination-button" id="next-button" aria-label="Next page" title="Next page">&gt;</button></nav></main>');
}


    public function import_Payment_types()
    {

        $latest_sync_Payment_types=$this->sync_model->getFoodicsSettings()->latest_sync_Payment_types;

        //$latest_sync_Payment_types=$latest_sync_Payment_types->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_Payment_types]);

    if($latest_sync_Payment_types){
        $dateTime = new DateTime($latest_sync_Payment_types);

	    $latest_sync_Payment_types= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = 'https://api-sandbox.foodics.com/v5/payment_methods?filter[type]=1,2,3,7&filter[is_active]=true&filter[is_deleted]=false&filter[updated_after]='.$latest_sync_Payment_types.'&sort=updated_at';
    }else{
        $url = 'https://api-sandbox.foodics.com/v5/payment_methods?filter[type]=1,2,3,7&filter[is_active]=true&filter[is_deleted]=false&sort=updated_at';
    }

       // $url = 'https://api-sandbox.foodics.com/v5/payment_methods?filter[type]=1,2&filter[is_active]=true';

    /* Init cURL resource */
    $ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';
    $token = $this->sync_model->getFoodicsSettings()->access_token;
    $headers[] = "Authorization: Bearer ".$token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    /* set return type json */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* execute request */
    $result = json_decode(curl_exec($ch));

    /* close cURL resource */
    curl_close($ch);
    if($result->meta){
        if($result->data){
          foreach($result->data as $dataInfo){




        if (!$this->accounts_model->getPaymentMethodsByUUID($dataInfo->id)) {

            $updated_at=$dataInfo->updated_at;
            if($dataInfo->type== 1 || $dataInfo->type== 7)
            $payment_type='cash';
            if($dataInfo->type== 2)
            $payment_type='CC';
            if($dataInfo->type== 3)
            $payment_type='bank_transfer';

            $data = [
				'uuid'            => $dataInfo->id,
				'payment_name'            => $dataInfo->name,
				'payment_ename'            => $dataInfo->name,
				'commisson_perc'            => 0,
				'payment_account'            => ($dataInfo->type == 1 || $dataInfo->type== 7) ? $this->accounts_model->getSettings()->accounts_main_boxes_acct : $this->accounts_model->getSettings()->accounts_main_banks_acct,
				'commisson_account'            => 0,
				'description'            => null,
				'payment_type'            => $payment_type,
				'tax_rate'            => 0,
				'payment_cc_id'            => 0,

			];

            $dataFinal[] = $data;

            }



          }

            if ($this->accounts_model->addPaymentMethodsBatch($dataFinal,"Payment_types",$updated_at) && !empty($dataFinal)) {
                //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
                //$this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
                $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

                //$this->sma->send_json([ 'msg' => $result->data]);
            }
            //die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
        }
            $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

//            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
        }


            //$this->sma->send_json([ 'msg' => $dataFinal]);
            die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
          // $this->sma->send_json([ 'msg' => '<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!' ,'per_page'=> $meta->per_page,'total'=> $meta->total ]);
           //$this->sma->print_arrays('<main><nav class="pagination-container"><button class="pagination-button" id="prev-button" aria-label="Previous page" title="Previous page">&lt;</button><div id="pagination-numbers"></div><button class="pagination-button" id="next-button" aria-label="Next page" title="Next page">&gt;</button></nav></main>');
    }

    public function update_transfers()
    {
        $latest_sync_transfer=$this->sync_model->getFoodicsSettings()->latest_sync_transfer;

        //$latest_sync_transfer=$latest_sync_transfer->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_transfer]);

    if($latest_sync_transfer){
        $dateTime = new DateTime($latest_sync_transfer);

	    $latest_sync_transfer= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = 'https://api-sandbox.foodics.com/v5/inventory_transactions?filter[status]=4&filter[type]=6&filter[updated_after]='.$latest_sync_transfer.'&sort=updated_at&include=items,branch,supplier,other_branch';
    }else{
        $url = 'https://api-sandbox.foodics.com/v5/inventory_transactions?filter[status]=4&filter[type]=6&sort=updated_at&include=items,branch,supplier,other_branch';
    }

    //
    /* Init cURL resource */
    $ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';
    $token = $this->sync_model->getFoodicsSettings()->access_token;
    $headers[] = "Authorization: Bearer ".$token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    /* set return type json */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* execute request */
    $result = json_decode(curl_exec($ch));

    /* close cURL resource */
    curl_close($ch);


    //$this->sma->send_json([ 'msg' => $result->data]);
    if($result->meta){
        if($result->data){
          foreach($result->data as $dataInfo){
            if (!$this->accounts_model->getEntriesByUUID($dataInfo->id)) {
            //$entryDescription="Purchase_Invoice_No#".$dataInfo->reference;





                $entry_type="Transfer";

                $entryDebitAccount= [];


                    $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->branch->id)->id;
               // $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
            if($entryWarehouseAcc==""){
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                }

        $entryDebitAccount[] =  $entryWarehouseAcc;


                $entryCreditAccount= [];


                    $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->other_branch->id)->id;
               // $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
            if($entryWarehouseAcc==""){
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                }

        $entryCreditAccount[] =  $entryWarehouseAcc;


        $final_item_cost_value=0;
                foreach($dataInfo->items as $product){

                    $final_item_cost_value=($final_item_cost_value+$product->pivot->cost);


                }
                //$data['grand_total']=($dataInfo->paid_tax + $final_item_cost_value);



                    $costCenterDebitId= [];
					$costCenterDebitAmount= [];

                    $this->accounts_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($final_item_cost_value),$this->sma->formatDecimal($final_item_cost_value),$dataInfo->reference,$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id,"transfer");









    }
          }

          $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

          //$this->sma->send_json([ 'msg' => $result->data]);
      }
      $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

      //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
          }


              //$this->sma->send_json([ 'msg' => $dataFinal]);
              die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
      //$this->sma->send_json([ 'msg' => $dataFinal]);
     // die('<i class="fa fa-times"></i> Failed!');

  }



    public function update_inventory_counts()
    {
        $latest_sync_inventory_counts=$this->sync_model->getFoodicsSettings()->latest_sync_inventory_counts;

        //$latest_sync_inventory_counts=$latest_sync_inventory_counts->format(DATE_ISO8601);
        //$this->sma->send_json([ 'msg' => $latest_sync_inventory_counts]);

    if($latest_sync_inventory_counts){
        $dateTime = new DateTime($latest_sync_inventory_counts);

	    $latest_sync_inventory_counts= $dateTime->format('Y-m-d\TH:i:s\Z');
        $url = 'https://api-sandbox.foodics.com/v5/inventory_counts?filter[status]=2&filter[updated_after]='.$latest_sync_inventory_counts.'&sort=updated_at&include=items,branch';
    }else{
        $url = 'https://api-sandbox.foodics.com/v5/inventory_counts?filter[status]=2&sort=updated_at&include=items,branch';
    }

    //
    /* Init cURL resource */
    $ch = curl_init($url);


    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept:application/json';
    $token = $this->sync_model->getFoodicsSettings()->access_token;
    $headers[] = "Authorization: Bearer ".$token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


    /* set return type json */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* execute request */
    $result = json_decode(curl_exec($ch));

    /* close cURL resource */
    curl_close($ch);


    //$this->sma->send_json([ 'msg' => $result->data]);
    if($result->meta){
        if($result->data){
          foreach($result->data as $dataInfo){
            if (!$this->accounts_model->getEntriesByUUID($dataInfo->id)) {
            //$entryDescription="Purchase_Invoice_No#".$dataInfo->reference;





                $entry_type="Revaluation";

                $value_minus=0;
			    $value_plus=0;

                foreach($dataInfo->items as $product){
                    if($product->pivot->variance_cost>0){
                        $value_plus=($value_plus+$product->pivot->variance_cost);
                    }
                    if($product->pivot->variance_cost<0){
                        $value_minus=($value_minus+abs($product->pivot->variance_cost));
                    }


                }






        $costCenterDebitId= [];
        $costCenterDebitAmount= [];
                //$data['grand_total']=($dataInfo->paid_tax + $final_item_cost_value);

                    if($value_minus>0){
                        $entryDebitAccount= [];
                        $entryCreditAccount= [];

                    $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->branch->id)->id;
               // $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
            if($entryWarehouseAcc==""){
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                }

        $entryCreditAccount[] =  $entryWarehouseAcc;


                        $entryDebitAccount[] =  $this->site->getAccountsSettings()->warehouses_revaluation_losses;

                        $this->accounts_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($value_minus),$this->sma->formatDecimal($value_minus),$dataInfo->reference,$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id,"inventory_counts");


                    }

                    if($value_plus>0){
                        $entryDebitAccount= [];


                    $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->branch->id)->id;
               // $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
            if($entryWarehouseAcc==""){
                    $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
                }

        $entryDebitAccount[] =  $entryWarehouseAcc;

                        $entryCreditAccount= [];
                        $entryCreditAccount[] =  $this->site->getAccountsSettings()->warehouses_revaluation_profite;

                        $this->accounts_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($value_plus),$this->sma->formatDecimal($value_plus),$dataInfo->reference,$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id,"inventory_counts");


                    }












    }
          }

          $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

          //$this->sma->send_json([ 'msg' => $result->data]);
      }
      $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

      //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
          }


              //$this->sma->send_json([ 'msg' => $dataFinal]);
              die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
      //$this->sma->send_json([ 'msg' => $dataFinal]);
     // die('<i class="fa fa-times"></i> Failed!');

  }




  public function update_destruction_inventory()
  {
      $latest_sync_destruction_inventory=$this->sync_model->getFoodicsSettings()->latest_sync_destruction_inventory;

      //$latest_sync_destruction_inventory=$latest_sync_destruction_inventory->format(DATE_ISO8601);
      //$this->sma->send_json([ 'msg' => $latest_sync_destruction_inventory]);

  if($latest_sync_destruction_inventory){
      $dateTime = new DateTime($latest_sync_destruction_inventory);

      $latest_sync_destruction_inventory= $dateTime->format('Y-m-d\TH:i:s\Z');
      $url = 'https://api-sandbox.foodics.com/v5/inventory_transactions?filter[status]=4&filter[type]=3&filter[updated_after]='.$latest_sync_destruction_inventory.'&sort=updated_at&include=items,branch';
  }else{
      $url = 'https://api-sandbox.foodics.com/v5/inventory_transactions?filter[status]=4&filter[type]=3&sort=updated_at&include=items,branch';
  }

  //
  /* Init cURL resource */
  $ch = curl_init($url);


  $headers = [];
  $headers[] = 'Content-Type:application/json';
  $headers[] = 'Accept:application/json';
  $token = $this->sync_model->getFoodicsSettings()->access_token;
  $headers[] = "Authorization: Bearer ".$token;
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');


  /* set return type json */
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  /* execute request */
  $result = json_decode(curl_exec($ch));

  /* close cURL resource */
  curl_close($ch);


  //$this->sma->send_json([ 'msg' => $result->data]);
  if($result->meta){
      if($result->data){
        foreach($result->data as $dataInfo){
          if (!$this->accounts_model->getEntriesByUUID($dataInfo->id)) {
          //$entryDescription="Purchase_Invoice_No#".$dataInfo->reference;





              $entry_type="Destruction";

              $value_minus=0;


              foreach($dataInfo->items as $product){


                      $value_minus=($value_minus+abs($product->pivot->cost));



              }






      $costCenterDebitId= [];
      $costCenterDebitAmount= [];
              //$data['grand_total']=($dataInfo->paid_tax + $final_item_cost_value);


                      $entryDebitAccount= [];
                      $entryCreditAccount= [];

                  $entryWarehouseAcc=$this->accounts_model->getAccountByUUID($dataInfo->branch->id)->id;
             // $entryWarehouseAcc=$this->site->getAccountByRefId($data['warehouse_id'],'warehouses')->id;
          if($entryWarehouseAcc==""){
                  $entryWarehouseAcc=$this->site->getAccountsSettings()->warehouses_acct;
              }

      $entryCreditAccount[] =  $entryWarehouseAcc;


                      $entryDebitAccount[] =  $this->site->getAccountsSettings()->warehouses_destruction;

                      $this->accounts_model->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$this->sma->formatDecimal($value_minus),$this->sma->formatDecimal($value_minus),$dataInfo->reference,$entry_type,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),null,null,$dataInfo->updated_at,$entryDescription,$dataInfo->id,"destruction_inventory");

















  }
        }

        $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' ( '.$result->meta->to.' ) '  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> $result->meta->to ]);

        //$this->sma->send_json([ 'msg' => $result->data]);
    }
    $this->sma->send_json([ 'msg' => '<i class="fa fa-check"></i> '.lang("Synchronization_success").'! '.lang("Entries_NO").' 0'  ,'per_page'=> $result->meta->per_page,'total'=> $result->meta->total,'from'=> $result->meta->from,'to'=> 0 ]);

    //            die('<i class="fa fa-check"></i> '.lang("Synchronization_success").'!');
        }


            //$this->sma->send_json([ 'msg' => $dataFinal]);
            die('<i class="fa fa-times"></i> '.lang("Synchronization_failed").'!');
    //$this->sma->send_json([ 'msg' => $dataFinal]);
   // die('<i class="fa fa-times"></i> Failed!');

}

    public function user_groups()
    {
        if ($this->sync_model->userGroups()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }


	public function generate_xml($id)
    {

		$inv         = $this->sales_model->getInvoiceByID($id);
        $customer    = $this->site->getCompanyByID($inv->customer_id);
        $units       = $this->site->getAllUnits();
        $biller      = $this->site->getCompanyByID($inv->biller_id);
        $created_by  = $this->site->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $warehouse   = $this->site->getWarehouseByID($inv->warehouse_id);
        $inv         = $inv;
        $rows        = $this->sales_model->getAllInvoiceItems($id);
        $return_sale = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : null;
        $return_rows = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : null;
		$payments        = $this->sales_model->getInvoicePayments($inv->id);
		$return_payments = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;
        $active_tax        =$this->sales_model->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;

		$dbName=str_replace("cpesoft_","",$this->db->database);
		$dateTime=str_replace('-',"",$inv->date);
		$dateTime=str_replace(':',"",$dateTime);
		$dateTime=str_replace(' ',"T",$dateTime);
		//	$dateTime = new DateTime($inv->date);

	//$dateTime= $dateTime->format('Y-m-d\TH:i:s\Z');


		$xml_file_path=$dbName.'_'.$warehouse->vat_reg_no.'_'.$id;

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->Settings->site_name;

		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}
			$paymentMeansCode=1;
			if($payments[0]){
				if($payments[0]->paid_by=="cach")
					$paymentMeansCode=10;
				if($payments[0]->paid_by=="CC")
					$paymentMeansCode=30;
				if($payments[0]->paid_by=="bank_transfer" || $payments[0]->paid_by=="Cheque")
					$paymentMeansCode=42;
			}
		//$this->sma->print_arrays($payments);
		//$this->sma->print_arrays($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$inv->id,$rows,$warehouse,$customer,$inv->uuid,'',$this->sma->formatDecimal($return_sale ? ($inv->paid  + $return_sale->paid ) : abs($inv->paid )));

		if($inv->sale_status == 'returned' || $inv->sale_status == 'completed'){
			$this->sma->xmlInvoice($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$inv->id,$rows,$warehouse,$customer,$inv->uuid,'',$this->sma->formatDecimal($return_sale ? ($inv->paid  + $return_sale->paid ) : abs($inv->paid )),$paymentMeansCode);
		    $xml_download=anchor('admin/welcome/downloadXMLUBL/' . $xml_file_path.'.xml', '<i class="fa fa-download"></i> <b>XML UBL File</b>', 'class="btn btn-primary btn-xs"');

			if($this->sync_model->updateSalesXMLFilePathById($id,$xml_file_path)){
			    $this->sma->send_json([ 'msg' => $xml_download.'<i class="fa fa-check"></a> Success!']);

				//die($xml_download.'<i class="fa fa-check"></a> Success!');
			}else{
			    //die('<i class="fa fa-times"></a> Failed!');
				$this->sma->send_json([ 'msg' => '<i class="fa fa-times"></a> Failed!']);

			}

			//$this->sma->print_arrays($xml_download);
			//die();
        }else{
            //die('<i class="fa fa-times"></a> Failed!');
			$this->sma->send_json([ 'msg' => '<i class="fa fa-times"></a> Failed!']);

        }

    }




	public function digital_signature($id)
    {

		$inv         = $this->sales_model->getInvoiceByID($id);
        $customer    = $this->site->getCompanyByID($inv->customer_id);
        $units       = $this->site->getAllUnits();
        $zatcaConfig       = $this->site->getZatcaConfig();
        $biller      = $this->site->getCompanyByID($inv->biller_id);
        $created_by  = $this->site->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $warehouse   = $this->site->getWarehouseByID($inv->warehouse_id);
        $inv         = $inv;
        $rows        = $this->sales_model->getAllInvoiceItems($id);
        $return_sale = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : null;
        $return_rows = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : null;
		$payments        = $this->sales_model->getInvoicePayments($inv->id);
		$return_payments = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;
        $active_tax        =$this->sales_model->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;

		$wh_name=$warehouse->branch_name ? $warehouse->branch_name : $this->Settings->site_name;
		$InvoiceTypeNumber=$customer->vat_no!="" ? 1 : 2;


		if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}

		//$this->sma->print_arrays($InvoiceTypeNumber);

		if($inv->sale_status == 'returned' || $inv->sale_status == 'completed'){

			//$QR=$this->sma->barcodeZATCA($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$inv->stamp_hash,$inv->ECDSA_signature,$inv->stamp_public_key);
			$QR=$this->sma->xmlInvoiceSign($inv->id,$inv->xml_file_path,$wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$zatcaConfig,$InvoiceTypeNumber);
			//$QR=$this->sma->xmlInvoiceSign($inv->id,$inv->xml_file_path,"Ahmed Mohamed AL Ahmady","301121971500003","2022-03-13 14:40:40","1108.9","144.9");
			//$QR="ARdBaG1lZCBNb2hhbWVkIEFMIEFobWFkeQIPMzAxMTIxOTcxNTAwMDAzAxQyMDIyLTAzLTEzVDE0OjQwOjQwWgQGMTEwOC45BQUxNDQuOQYsb2VSUEgvQ0tMUXVsZWRScnJsMmNrY3pMOWJtdG43YzhteEJGdE5aa1RSZz0HYE1FVUNJQ243VExLazFrME94d2Q1VFNaWUtQTlJNbEw5MTcrVU9iM0czOTlCcWoxVkFpRUEyaVlhYWRhR0RRbkdvWG1iSTR3UFc0MWJiYlhZZlk5MjYyYXdBMmhXRFpVPQhYMFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAEYYMMoOaFYAhMO/steotfZyavr6p11SSlwsK9azmsLY7b1b+FLhqMArhB2dqHKboxqKNfvkKDePhpqjui5hcn0QlIMEYCIQDuYdPrKDzmO1AZanczu09PsmTb7Oy9UcazdtTlntgTrwIhAPrR5tBqZiNi915ucWM1/Hhfh2insuwQEUI1KwtjQgVp";
			$xml_download=anchor('admin/welcome/downloadXMLUBL/' . $inv->xml_file_path.'signedWQR.xml', '<i class="fa fa-download"></i> <b>XML File with Stamp</b>', 'class="btn btn-primary btn-xs"');
			//die($this->sma->qrcode('', $QR, 2).''.$xml_download.'<i class="fa fa-check"></a> Success!');
			$invAfterUpdate         = $this->sales_model->getInvoiceByID($id);
			$this->sma->send_json([ 'msg' => $this->sma->qrcode('', $QR, 2).''.$xml_download.'<i class="fa fa-check"></a> Success!','stamp_hash' => $invAfterUpdate->stamp_hash,'uuid' => $invAfterUpdate->uuid,'synch_hash' => $invAfterUpdate->synch_hash]);


			//$this->sma->print_arrays($xml_download);
			//die();
        }else{
           // die('<i class="fa fa-times"></a> Failed!');
			$this->sma->send_json([ 'msg' => '<i class="fa fa-times"></a> Failed!']);

        }

    }

	public function integration_zatca_clearance_temp($inv_id)
	{

		if($_POST['res']['reportingStatus']=="REPORTED"){
			$synch_status=$_POST['res']['reportingStatus'];
		$this->db->update('sales', ['synch' => "1",'synch_status' => $synch_status], ['id' => $inv_id]);

		}
		if($_POST['res']['clearanceStatus']=="CLEARED"){
			$synch_status=$_POST['res']['clearanceStatus'];
		$this->db->update('sales', ['synch' => "1",'synch_status' => $synch_status], ['id' => $inv_id]);

		}
	//$this->sma->print_arrays($this->db);
	$this->sma->print_arrays(($_POST['res']));
	}
	public function integration_zatca_reporting_temp()
	{
		$this->sma->print_arrays(($_POST['res']));
	}
	public function integration_zatca_clearance($id)
    {

		$inv         = $this->sales_model->getInvoiceByID($id);
        $customer    = $this->site->getCompanyByID($inv->customer_id);
        $units       = $this->site->getAllUnits();
        $biller      = $this->site->getCompanyByID($inv->biller_id);
        $created_by  = $this->site->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $warehouse   = $this->site->getWarehouseByID($inv->warehouse_id);
        $inv         = $inv;
        $rows        = $this->sales_model->getAllInvoiceItems($id);
        $return_sale = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : null;
        $return_rows = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : null;
		$payments        = $this->sales_model->getInvoicePayments($inv->id);
		$return_payments = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;
        $active_tax        =$this->sales_model->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;

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
			$username="TUlJQ01EQ0NBZGFnQXdJQkFnSUdBWU1VelpQMk1Bb0dDQ3FHU000OUJBTUNNQlV4RXpBUkJnTlZCQU1NQ21WSmJuWnZhV05wYm1jd0hoY05Nakl3T1RBMk1qRTFOVFUxV2hjTk1qY3dPVEEyTWpFd01EQXdXakJwTVFzd0NRWURWUVFHRXdKVFFURVRNQkVHQTFVRUN3d0tNakExTlRFeU9ERTBPREVUTUJFR0ExVUVDZ3dLTWpBMU5URXlPREUwT0RFd01DNEdBMVVFQXd3bllYSnJZVzV3Y204dVkyOXRMVEl3TlRVeE1qZ3hORGd0TXpFd01qQXpOemM1T0RBd01EQXpNRll3RUFZSEtvWkl6ajBDQVFZRks0RUVBQW9EUWdBRW5SUExmUGpOUUN2NHFVcDBSNkEySWl6eXVtek9acUNwV3VuNmpZS1ErRUl5Y1NiS0U2YStESDE5R1h6OXJIVDRXVzZpZjZoU2ZLKzlpVHA2cHZVSnVLT0J3RENCdlRBTUJnTlZIUk1CQWY4RUFqQUFNSUdzQmdOVkhSRUVnYVF3Z2FHa2daNHdnWnN4UlRCREJnTlZCQVFNUERFdFlYSnJZVzV3Y205OE1pMWhjbXRoYm5CeWIzd3pMV1ZrTWpKbU1XUTRMV1UyWVRJdE1URXhPQzA1WWpVNExXUTVZVGhtTVRGbE5EUTFaakVmTUIwR0NnbVNKb21UOGl4a0FRRU1Eek14TURJd016YzNPVGd3TURBd016RU5NQXNHQTFVRURBd0VNVEF3TURFUE1BMEdBMVVFR2d3R1NuVmlZV2xzTVJFd0R3WURWUVFQREFoQmNtdGhibkJ5YnpBS0JnZ3Foa2pPUFFRREFnTklBREJGQWlFQXU1VXdJT2NBd1NVc1hVNzdlS0VJVXcraW41WUF0NGVHNGZIaW54d0RpMHNDSUNxNzljMnRHWEZVVlVwN2pIay82aUI1amk2d1FtV2VKWXJadmdGblp0bUw=";
			$password="DFtkjHTz5cY2CtIeSQ1AWiK09nNaCWfTiBKihmM877A=";

			$url = "https://gw-apic-gov.gazt.gov.sa/e-invoicing/developer-portal/compliance/invoices";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($curl, CURLOPT_USERAGENT,'PostmanRuntime/7.29.0');
		curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);


		$headers = array(
		   "accept: application/json",
		   "Content-Type: application/json",
		   "Accept-Version: V2",
		   "Accept-Language: en",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = '{"invoiceHash":"'.$inv->stamp_hash.'","uuid":"'.$inv->uuid.'","invoice":"'.$inv->synch_hash.'"}';

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		//print_r(json_decode($resp));
		//$otp_token= json_decode($resp)->otp->otp_token;

		curl_close($curl);

			//$QR=$this->sma->barcodeZATCA($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$inv->stamp_hash,$inv->ECDSA_signature,$inv->stamp_public_key);
			//$QR=$this->sma->xmlInvoiceSign($inv->id,$inv->xml_file_path,$wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2));
			//$xml_download=anchor('admin/welcome/downloadXMLUBL/' . $inv->xml_file_path.'signed.xml', '<i class="fa fa-download"></i> <b>XML File with Stamp</b>', 'class="btn btn-primary btn-xs"');

			$this->sma->print_arrays(json_decode($resp));
			//die(($resp).'<i class="fa fa-check"></a> Success!');


			//$this->sma->print_arrays($xml_download);
			//die();
        }else{
            die('<i class="fa fa-times"></a> Failed!');
        }

    }

	public function integration_zatca_reporting($id)
    {

		$inv         = $this->sales_model->getInvoiceByID($id);
        $customer    = $this->site->getCompanyByID($inv->customer_id);
        $units       = $this->site->getAllUnits();
        $biller      = $this->site->getCompanyByID($inv->biller_id);
        $created_by  = $this->site->getUser($inv->created_by);
        $updated_by  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $warehouse   = $this->site->getWarehouseByID($inv->warehouse_id);
        $inv         = $inv;
        $rows        = $this->sales_model->getAllInvoiceItems($id);
        $return_sale = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : null;
        $return_rows = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : null;
		$payments        = $this->sales_model->getInvoicePayments($inv->id);
		$return_payments = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;
        $active_tax        =$this->sales_model->getTaxRatesActive()->rate;
		$return_payments = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;

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
			$username="TUlJQ01EQ0NBZGFnQXdJQkFnSUdBWU1VelpQMk1Bb0dDQ3FHU000OUJBTUNNQlV4RXpBUkJnTlZCQU1NQ21WSmJuWnZhV05wYm1jd0hoY05Nakl3T1RBMk1qRTFOVFUxV2hjTk1qY3dPVEEyTWpFd01EQXdXakJwTVFzd0NRWURWUVFHRXdKVFFURVRNQkVHQTFVRUN3d0tNakExTlRFeU9ERTBPREVUTUJFR0ExVUVDZ3dLTWpBMU5URXlPREUwT0RFd01DNEdBMVVFQXd3bllYSnJZVzV3Y204dVkyOXRMVEl3TlRVeE1qZ3hORGd0TXpFd01qQXpOemM1T0RBd01EQXpNRll3RUFZSEtvWkl6ajBDQVFZRks0RUVBQW9EUWdBRW5SUExmUGpOUUN2NHFVcDBSNkEySWl6eXVtek9acUNwV3VuNmpZS1ErRUl5Y1NiS0U2YStESDE5R1h6OXJIVDRXVzZpZjZoU2ZLKzlpVHA2cHZVSnVLT0J3RENCdlRBTUJnTlZIUk1CQWY4RUFqQUFNSUdzQmdOVkhSRUVnYVF3Z2FHa2daNHdnWnN4UlRCREJnTlZCQVFNUERFdFlYSnJZVzV3Y205OE1pMWhjbXRoYm5CeWIzd3pMV1ZrTWpKbU1XUTRMV1UyWVRJdE1URXhPQzA1WWpVNExXUTVZVGhtTVRGbE5EUTFaakVmTUIwR0NnbVNKb21UOGl4a0FRRU1Eek14TURJd016YzNPVGd3TURBd016RU5NQXNHQTFVRURBd0VNVEF3TURFUE1BMEdBMVVFR2d3R1NuVmlZV2xzTVJFd0R3WURWUVFQREFoQmNtdGhibkJ5YnpBS0JnZ3Foa2pPUFFRREFnTklBREJGQWlFQXU1VXdJT2NBd1NVc1hVNzdlS0VJVXcraW41WUF0NGVHNGZIaW54d0RpMHNDSUNxNzljMnRHWEZVVlVwN2pIay82aUI1amk2d1FtV2VKWXJadmdGblp0bUw=";
			$password="DFtkjHTz5cY2CtIeSQ1AWiK09nNaCWfTiBKihmM877A=";

			$url = "https://gw-apic-gov.gazt.gov.sa/e-invoicing/developer-portal/invoices/reporting/single";

		$curl_r = curl_init($url1);
		curl_setopt($curl_r, CURLOPT_URL, $url);
		curl_setopt($curl_r, CURLOPT_POST, true);
		curl_setopt($curl_r, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($curl_r, CURLOPT_USERAGENT,'PostmanRuntime/7.29.0');
		curl_setopt($curl_r, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($curl_r, CURLOPT_USERPWD, $username . ":" . $password);


		$headers = array(
		   "accept: application/json",
		   "Content-Type: application/json",
		   "Accept-Version: V2",
		   "Accept-Language: en",
		   "Clearance-Status: 0",
		);
		curl_setopt($curl_r, CURLOPT_HTTPHEADER, $headers);

		$data = '{"invoiceHash":"'.$inv->stamp_hash.'","uuid":"'.$inv->uuid.'","invoice":"'.$inv->synch_hash.'"}';

		curl_setopt($curl_r, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl_r, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_r, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl_r);
		//print_r(json_decode($resp));
		//$otp_token= json_decode($resp)->otp->otp_token;

		curl_close($curl_r);

			//$QR=$this->sma->barcodeZATCA($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$inv->stamp_hash,$inv->ECDSA_signature,$inv->stamp_public_key);
			//$QR=$this->sma->xmlInvoiceSign($inv->id,$inv->xml_file_path,$wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2));
			//$xml_download=anchor('admin/welcome/downloadXMLUBL/' . $inv->xml_file_path.'signed.xml', '<i class="fa fa-download"></i> <b>XML File with Stamp</b>', 'class="btn btn-primary btn-xs"');

			$this->sma->print_arrays(json_decode($resp));
			//die(($resp).'<i class="fa fa-check"></a> Success!');


			//$this->sma->print_arrays($xml_download);
			//die();
        }else{
            die('<i class="fa fa-times"></a> Failed!');
        }

    }
    public function Synchronize_General_Data()
    {
        if ($this->sync_model->updateSales()) {
            die('<i class="fa fa-check"></a> Success!');
        }
        die('<i class="fa fa-times"></a> Failed!');
    }

}
