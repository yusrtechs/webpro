<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class UpdateSalesMeasurement extends REST_Controller
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


        $sales=$sales->sales;
        //$this->sma->print_arrays($sales);
        $failed_sales = [];



		foreach ($sales as $sale) {

			 //$this->sma->print_arrays($sale);


			//if($this->pos2_model->getInvoiceBy_date_reference_no_biller_id(trim($sale->date),trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id))==0){
			//if($this->pos2_model->getInvoiceBy_reference_no_biller_id(trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id))==0){

			$sale1           = [
				'id'                 => trim($sale->id),



            ];


			krsort($sale1);



			$Measurements=  $sale->Measurement;
			//$payments=(array) $payments;
			$Measurement2 = [];

			foreach ($Measurements as $Measurement) {

			$Measurement1 = [
                        'sale_id'        => $Measurement->sale_id,
                        'Item_Code'        => $Measurement->Item_Code,
                        'Item_Name'        => $Measurement->Item_Name,
                        'Front_Length'        => $Measurement->Front_Length,
                        'Back_Length'        => $Measurement->Back_Length,
                        'Shoulder_Width'        => $Measurement->Shoulder_Width,
                        'Shoulder_Slope'        => $Measurement->Shoulder_Slope,
                        'Sleeve'        => $Measurement->Sleeve,
                        'Sleeve_Top'        => $Measurement->Sleeve_Top,
                        'Wrist'        => $Measurement->Wrist,
                        'Plain_Cuff_Length'        => $Measurement->Plain_Cuff_Length,
                        'Cuff_Length'        => $Measurement->Cuff_Length,
                        'Cuff_Width'        => $Measurement->Cuff_Width,
                        'Middle_Width'        => $Measurement->Middle_Width,
                        'Chest_Front'        => $Measurement->Chest_Front,
                        'Chest_Back'        => $Measurement->Chest_Back,
                        'Bottom_Height'        => $Measurement->Bottom_Height,
                        'Bottom_Width'        => $Measurement->Bottom_Width,
                        'Collar_Width'        => $Measurement->Collar_Width,
                        'Collar_Height'        => $Measurement->Collar_Height,
                        'Zipper_Height'        => $Measurement->Zipper_Height,
                        'Zipper_Width'        => $Measurement->Zipper_Width,
                        'Chest_Pocket_Height'        => $Measurement->Chest_Pocket_Height,
                        'Chest_Pocket_Width'        => $Measurement->Chest_Pocket_Width,
                        'Mobile_Pocket_Height'        => $Measurement->Mobile_Pocket_Height,
                        'Wallet_Pocket_Height'        => $Measurement->Wallet_Pocket_Height,
                        'Wallet_Pocket_Width'        => $Measurement->Wallet_Pocket_Width,
                        'Haunch_Width'        => $Measurement->Haunch_Width,
                        'Button_No'        => $Measurement->Button_No,
                        'Embroidery_No'        => $Measurement->Embroidery_No,
                        'Estimated_Length'        => $Measurement->Estimated_Length,
                        'Tailor_id'        => $Measurement->Tailor_id,
                        'sample'        => $Measurement->sample,
                        'urgent'        => $Measurement->urgent,
                        'shoulder_chest_length'        => $Measurement->shoulder_chest_length,
                        'sleeve_middle'        => $Measurement->sleeve_middle,
                        'Side_Pocket_Length'        => $Measurement->Side_Pocket_Length,
                        'takhalees'        => $Measurement->takhalees,
                        'Collar_Type_ID'        => $Measurement->Collar_Type_ID,
                        'Cuff_Type_ID'        => $Measurement->Cuff_Type_ID,
                        'Model_Type_ID'        => $Measurement->Model_Type_ID,
                        'Pocket_Type_ID'        => $Measurement->Pocket_Type_ID,
                        'Filling_Type_ID'        => $Measurement->Filling_Type_ID,
                        'Note'        => $Measurement->Note,


                    ];
					krsort($Measurement1);


					$Measurement2[] = $Measurement1;

					}

			//$this->sma->print_arrays($sale1['id']);


				if($this->pos2_model->getSaleByID($sale1['id'])){
					$Measurement1['sale_id']=$sale1['id'];
					if($sale2 = $this->pos2_model->updateSaleTailor($sale1['id'],$Measurement1)){

			$this->set_response([
                    'message' => 'Success',
                    'status'  => true,
                ], REST_Controller::HTTP_OK);
            }else{

                $this->set_response([
                    'message' => 'Failure',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);

            }

		}else{
			$this->set_response([
                    'message' => 'Sale could not be found for sale_id ' . $sale1['id'] . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);

		}






		/*}else{
		$response["success"] = 1;
			$salse_temp = [
			'id'            => trim($sale->Offline_Pos_id),
			'hash'            => trim($sale->hash),
			'online_id'            => ''
			];

			$salse_temp2[]=$salse_temp;

		}*/
		}

		//$this->sma->print_arrays($payments_only);





		/*
		$reference = $this->get('reference');

        $filters = [
            'reference'   => $reference,
            'include'     => $this->get('include') ? explode(',', $this->get('include')) : null,
            'start'       => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'       => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 10,
            'start_date'  => $this->get('start_date') && is_numeric($this->get('start_date')) ? $this->get('start_date') : null,
            'end_date'    => $this->get('end_date') && is_numeric($this->get('end_date')) ? $this->get('end_date') : null,
            'order_by'    => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'decs'],
            'customer_id' => $this->get('customer_id') ? $this->get('customer_id') : null,
            'customer'    => $this->get('customer') ? $this->get('customer') : null,
        ];

        if ($reference === null) {
            if ($sales = $this->sales_api->getSales($filters)) {
                $sl_data = [];
                foreach ($sales as $sale) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'items') {
                                $sale->items = $this->sales_api->getSaleItems($sale->id);
                            }
                            if ($include == 'warehouse') {
                                $sale->warehouse = $this->sales_api->getWarehouseByID($sale->warehouse_id);
                            }
                        }
                    }

                    //$sale->created_by = $this->sales_api->getUser($sale->created_by);
                    $sl_data[]        = $this->setSale($sale);
                }

                $data = [
                    'data'  => $sl_data,
                    'limit' => (int) $filters['limit'],
                    'start' => (int) $filters['start'],
                    'total' => $this->sales_api->countSales($filters),
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No sale record found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            if ($sale = $this->sales_api->getSale($filters)) {
                if (!empty($filters['include'])) {
                    foreach ($filters['include'] as $include) {
                        if ($include == 'items') {
                            $sale->items = $this->sales_api->getSaleItems($sale->id);
                        }
                        if ($include == 'warehouse') {
                            $sale->warehouse = $this->sales_api->getWarehouseByID($sale->warehouse_id);
                        }
                    }
                }

                $sale->created_by = $this->sales_api->getUser($sale->created_by);
                $sale             = $this->setSale($sale);
                $this->set_response($sale, REST_Controller::HTTP_OK);
            } else {
                $this->set_response([
                    'message' => 'Sale could not be found for reference ' . $reference . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
		*/
    }
}
