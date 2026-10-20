<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetPOSRegister extends REST_Controller
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

		$pos_register=$sales->pos_register;
        if ($pos_register) {


        $failed_sales = [];
        $flageTrue=0;
		$flageFalse=0;

		foreach ($pos_register as $pos_reg) {

			if($this->pos2_model->getPosRegisterBy_date_closed_at_user_id_Offline_id($pos_reg->date,$pos_reg->closed_at,$pos_reg->user_id,$pos_reg->Offline_id) == 0){

                if(trim($pos_reg->status)=="close"){
                    if(trim($pos_reg->user_id)){

                        if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
            	        {
					        $brId=$this->pos2_model->getWarehouse_VATByID($this->pos2_model->getUser(trim($pos_reg->user_id))->warehouse_id)->brId;
				        }else{
					        $brId=1;
				        }


                        $pos_register_open         = [
                        'date'         => trim($pos_reg->date),
                        'cash_in_hand'       => $this->sma->formatDecimal($pos_reg->cash_in_hand),
                        'user_id'   => trim($pos_reg->user_id),
                        'status' => 'open',
                        'Offline_id' => trim($pos_reg->Offline_id),
                        'cash_in_hand_clearance' =>isset($pos_reg->cash_in_hand_clearance)? trim($pos_reg->cash_in_hand_clearance):'0',


                    ];

                    if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
					{
						$pos_register_open  ['grpId']= 1;
						$pos_register_open  ['coId']= 1;
						$pos_register_open  ['brId']=$brId ? $brId : 1;
					}

                //$this->sma->print_arrays($user_id);
                    $pos_register_close          = [
                        'closed_at'         => trim($pos_reg->closed_at),
                        'total_cash'       => $this->sma->formatDecimal($pos_reg->total_cash),
                        'total_cheques'   => trim($pos_reg->total_cheques),
                        'total_cc_slips' =>isset($pos_reg->total_cc)? $this->sma->formatDecimal($pos_reg->total_cc):'0',
                        'total_cash_submitted' => $this->sma->formatDecimal($pos_reg->total_cash_submitted),
                        'total_cheques_submitted' => trim($pos_reg->total_cheques_submitted),
                        'total_cc_slips_submitted' =>isset($pos_reg->total_cc)? $this->sma->formatDecimal($pos_reg->total_cc):'0',
                        'total_cc' =>isset($pos_reg->total_cc)? $this->sma->formatDecimal($pos_reg->total_cc):'0',
                        'invoice_count' =>isset($pos_reg->invoice_count)? trim($pos_reg->invoice_count):'0',
                        'note' => trim($pos_reg->note),
                        'status' => trim($pos_reg->status),
                        'closed_by' => trim($pos_reg->user_id),
                        'Offline_id' => trim($pos_reg->Offline_id),
                        'donation'            => isset($pos_reg->donation)? trim($pos_reg->donation):'0',
                        'RealCash'            => isset($pos_reg->RealCash)? trim($pos_reg->RealCash):'0',
                        'Deficitandexcess'            => isset($pos_reg->Deficitandexcess)? trim($pos_reg->Deficitandexcess):'0',
                        'transfer_opened_bills'            => isset($pos_reg->transfer_opened_bills)? trim($pos_reg->transfer_opened_bills):'',
                        //'user_id' => trim($user_id),
                        'user_id' => trim($pos_reg->user_id),


                    ];

                    if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
					{
						$pos_register_close  ['grpId']= 1;
						$pos_register_close  ['coId']= 1;
						$pos_register_close  ['brId']=$brId ? $brId : 1;
					}
                    //krsort($expense1);

                    //$this->sma->print_arrays($this->pos2_model->getToatalSalesPaymentPOSCC($this->pos2_model->getUser($pos_register_close['user_id'])->biller_id,'completed','CC',$pos_register_open['date'],$pos_register_close['closed_at']));
                    //$this->sma->print_arrays($this->db->database);

                    if($this->pos2_model->closePOSRegisterV2( $pos_register_open, $pos_register_close,str_replace("cpesoft_","",$this->db->database))){


			$product_temp = [
            'Offline_id'            => trim($pos_register_close['Offline_id']),
			'status'            => true
			];
            $flageTrue=1;

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);
                $product_temp = [
                    'Offline_id'            => trim($pos_register_close['Offline_id']),
			        'status'            => false
                    ];
                    $flageFalse=1;

                    $product_temp2[]=$product_temp;

			}
        }else{
            //array_push($failed_sales['customer'],$customer1['Offline_id']);
            $product_temp = [
                'Offline_id'            => $pos_reg->Offline_id,
			    'status'            => false
                ];
                $flageFalse=1;

                $product_temp2[]=$product_temp;

        }
    }else{
        //array_push($failed_sales['customer'],$customer1['Offline_id']);
        $product_temp = [
            'Offline_id'            => $pos_reg->Offline_id,
            'status'            => false
            ];
            $flageFalse=1;

            $product_temp2[]=$product_temp;

    }
}else{
    //array_push($failed_sales['customer'],$customer1['Offline_id']);
    $product_temp = [
        'Offline_id'            => $pos_reg->Offline_id,
        'status'            => true
        ];
        $flageTrue=1;

        $product_temp2[]=$product_temp;

}
		}












        $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'pos_register' => $product_temp2,
                'Cleaned' => 'Cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'pos_register' => $product_temp2,
                'Cleaned' => 'Partial cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'pos_register' => $product_temp2,
                'Cleaned' => 'No cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }

        }else{

            $this->response([
                'message' => 'This request does not contain pos_register',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
