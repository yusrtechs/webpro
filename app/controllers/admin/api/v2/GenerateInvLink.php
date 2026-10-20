<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class GenerateInvLink extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('users_api');


		$this->load->admin_model('pos2_model');
		$this->load->admin_model('sales_model');
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
		$sale =json_decode($json);
		//$sales=$sales->JSON3;
		//$id="";

		//$date="";
		if($sale->offline_id && $sale->offline_id!=null){
			//$supply_date=$sales->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'Failure',
                    'description' => 'offline_id is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        if($sale->reference_no && $sale->reference_no!=null){
			//$supply_date=$sales->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'Failure',
                    'description' => 'reference_no is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        if($sale->biller && $sale->biller!=null){
			//$id=$sales->sale_code;
		}else{
		$this->set_response([
                    'message' => 'Failure',
                    'description' => 'biller is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        if($sale->biller_id && $sale->biller_id!=null){
			//$id=$sales->sale_code;
		}else{
		$this->set_response([
                    'message' => 'Failure',
                    'description' => 'biller_id is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

                $biller_id= $sale->biller ? $this->users_api->getBillerIdByUserName($sale->biller) : $sale->biller_id;
                $inv=$this->pos2_model->getInvoiceBy_reference_no_biller_id_V2(trim($sale->reference_no),$biller_id,trim($sale->offline_id));
                if($inv){







                    $dbName=str_replace("cpesoft_","",$this->db->database);

					$this->set_response([
                    'message' => 'Success',
                    'status'  => true,
                    'order_link'       => 'https://tinyurl.com/47f8rxxc/'.$dbName.'/'.$inv->uuid,

                ], REST_Controller::HTTP_OK);





            }else{
                $this->response([
                    'message' => 'Failure',
                    'description' => 'The invoice does not exist',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }







    }
}
