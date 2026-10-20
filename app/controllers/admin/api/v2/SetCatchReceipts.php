<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetCatchReceipts extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('users_api');

		$this->load->admin_model('pos2_model');
		$this->load->admin_model('purchases_model');
		$this->load->admin_model('companies_model');



    }

	protected function setArr($warehouse)
    {
        unset($warehouse->price_group_id);
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }


    protected function setSale($sale)
    {
        unset($sale->address_id, $sale->api, $sale->attachment, $sale->hash, $sale->pos, $sale->reserve_id, $sale->return_id, $sale->return_sale_ref, $sale->return_sale_total, $sale->sale_id, $sale->shop, $sale->staff_note, $sale->surcharge, $sale->updated_at, $sale->suspend_note);
        if (isset($sale->items) && !empty($sale->items)) {
            foreach ($sale->items as &$item) {
                if (isset($item->option_id) && !empty($item->option_id)) {
                    if ($variant = $this->sales_api->getProductVariantByID($item->option_id)) {
                        $item->product_variant_id   = $variant->id;
                        $item->product_variant_name = $variant->name;
                    }
                }
                $item->product_unit_quantity = $item->unit_quantity;
                unset($item->id, $item->sale_id, $item->warehouse_id, $item->real_unit_price, $item->sale_item_id, $item->option_id, $item->unit_quantity);
                $item = (array) $item;
                ksort($item);
            }
        }
        $sale = (array) $sale;
        ksort($sale);
        return $sale;
    }

    public function index_post()
    {

		//$sales = urldecode($this->post('sales'));
		$json = file_get_contents('php://input');
		$sales =json_decode($json);
		//$sales=$sales->JSON3;
		//$this->sma->print_arrays($this->Settings);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;

		$catch_receipts_only=$sales->catch_receipts;
        if ($catch_receipts_only) {


        $failed_sales = [];
        $flageTrue=0;
		$flageFalse=0;

		foreach ($catch_receipts_only as $payment) {


			if($payment->amount!=0 && ($this->pos2_model->getEntriesCatchReceiptsBy_date_reference_no(trim($payment->date),trim($payment->bond_no."/".$payment->Offline_id))==0)){





                    $entryDebitAccount= [];
                    $entryDebitBalance= [];

                    $entryCreditAccount= [];
                    $entryCreditBalance= [];


                    if($payment->paid_by!="CC"){

                        $entrybillerAcc=$this->pos2_model->getAccountByRefId($this->pos2_model->getUser($payment->created_by)->biller_id,'billers')->id;
                                    if($entrybillerAcc==""){
                                    $entrybillerAcc=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;
                                    $entrybillerAccCode=$this->pos2_model->getAccountByID($entrybillerAcc)->accountID;
                                    $entrybillerAcc=$this->pos2_model->getAccountByAccountID($entrybillerAccCode."01")->id;
                                    if($entrybillerAcc=="")
                                    $entrybillerAcc=$this->pos2_model->getAccountsSettings()->accounts_main_boxes_acct;

                                    }
                                $entryDebitAccount[] =  $entrybillerAcc;
                                $entryDebitBalance[] =  $this->sma->formatDecimal($payment->amount);

                        }else{
                            if($payment->gl_payment_method_id==0){
                                $entryDebitAccount[]=$this->pos2_model->getPaymentTypeByPaymentType('CC')->payment_account;
                                $entryDebitBalance[] =  $this->sma->formatDecimal($payment->amount);
                            }else{
                                $entryDebitAccount[]=$this->pos2_model->getPaymentTypeById($payment->gl_payment_method_id)->payment_account;
                                $entryDebitBalance[] =  $this->sma->formatDecimal($payment->amount);
                            }
                        }

                        $customer_id=  $this->pos2_model->getCompanyByOfflineIDV2($payment->customer_id,$payment->customer)->id ? $this->pos2_model->getCompanyByOfflineIDV2($payment->customer_id,$payment->customer)->id : ($this->pos2_model->getCompanyByidIDV2($payment->customer_id,$payment->customer)->id ? $this->pos2_model->getCompanyByidIDV2($payment->customer_id,$payment->customer)->id : ($this->pos2_model->getCompanyByOfflineID($payment->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($payment->customer_id)->id : $payment->customer_id));



                        $entryDebitAccountCustomer="";
				if($this->pos2_model->getCompanyByID($customer_id)->co_type==1){
					$entryDebitAccountCustomer=$this->pos2_model->getAccountByRefId($customer_id,'local_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->pos2_model->getAccountsSettings()->accounts_local_customers_acct;


				}

				if($this->pos2_model->getCompanyByID($customer_id)->co_type==2){

					$entryDebitAccountCustomer=$this->pos2_model->getAccountByRefId($customer_id,'international_customers')->id;
						if($entryDebitAccountCustomer=="")
						$entryDebitAccountCustomer=$this->pos2_model->getAccountsSettings()->accounts_international_customers_acct;


				}

			$entryCreditAccount[] =$entryDebitAccountCustomer;
			$entryCreditBalance[] =$this->sma->formatDecimal($payment->amount);


                        $entryDescription="Sales_Payments_Catch_receipt_Bond_No#".$payment->bond_no;
                        $entry_type="Sales_Payments_Catch_receipts";

			//krsort($expense1);
			if($this->pos2_model->createEntryMultiSideV2($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$payment->bond_no."/".$payment->Offline_id,$entry_type,$payment->created_by,$entryDescription,$payment->date)){

            if(isset($payment->the_oldest) && $payment->the_oldest==1){
                            $invoices = array();
							$payments = array();

                            $saleList = $this->pos2_model->getAllSalesDue(NULL, NULL, $payment->date, 1, $customer_id);
							$invoices = array();
							$payments = array();
							$amount=$this->sma->formatDecimal($payment->amount);
							$accounts_settings   = $this->pos2_model->getAccountsSettings();
							$payment_acc=$this->pos2_model->getAccountByID($entrybillerAcc);
								if ($this->pos2_model->IsAccountIsChild($accounts_settings->accounts_main_banks_acct,$payment_acc->parent_id) == '1'){
									$paid_by='bank_transfer';
								}else{
									$paid_by='cash';
								}
							$temp=0;
							foreach ($saleList as $sale){
								if($sale->amount <= $amount){
									array_push($invoices, $sale->id);
									array_push($payments, $sale->amount);
									$amount=$amount-$sale->amount;
								}else{
									if($amount>0){
										array_push($invoices, $sale->id);
										array_push($payments, $amount);
										$amount=0;
									}
									$temp=1;
								}
								if($temp==1)
									break;


							}

                            $i=0;
				foreach ($invoices as $inv_no){

				$sale=$this->pos2_model->getSaleByID($inv_no);
				$payment2 = [
					'date'         => $payment->date,
					'sale_id'      => $inv_no,
					'reference_no' => $this->pos2_model->getReference('pay',(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1) ? $sale->brId : null),
					'amount'       => $payments[$i],
					'paid_by'      => $paid_by,
					'note'         => '',
					'currency'         => 'SR',
					'currency_rate'         => 1,
					'created_by'   => $payment->created_by,
					'type'         => 'received',


				 ];


					if ($this->db->insert('payments', $payment2)) {
						if ($this->pos2_model->getReference('pay',$payment2['brId'] ? $payment2['brId']  : null) == $payment2['reference_no']) {
							$this->pos2_model->updateReference('pay',$payment2['brId'] ? $payment2['brId']  : null);
						}
						$this->pos2_model->syncSalePayments($inv_no);


					}
					$i++;

				}



                        }
			$product_temp = [
            'Offline_id'            => trim($payment->Offline_id),
			'status'            => true
			];
            $flageTrue=1;

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);
                $product_temp = [
                    'Offline_id'            => trim($payment->Offline_id),
			        'status'            => false
                    ];
                    $flageFalse=1;

                    $product_temp2[]=$product_temp;

			}
        }else{
            //array_push($failed_sales['customer'],$customer1['Offline_id']);
            $product_temp = [
                'Offline_id'            => $payment->Offline_id,
			    'status'            => false
                ];
                $flageFalse=1;

                $product_temp2[]=$product_temp;

        }
		}












        $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'catch_receipts' => $product_temp2,
                'Cleaned' => 'Cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'catch_receipts' => $product_temp2,
                'Cleaned' => 'Partial cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'catch_receipts' => $product_temp2,
                'Cleaned' => 'No cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }

        }else{

            $this->response([
                'message' => 'This request does not contain catch receipts',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
