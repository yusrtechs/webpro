<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class UpdateSaleItem extends REST_Controller
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
		$Item =json_decode($json);
		//$sales=$sales->JSON3;
		//$this->sma->print_arrays($this->Settings);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;


        //$sales=$sales->sales;
        //$this->sma->print_arrays($sales);
        $failed_sales = [];





			//$Items=  $sale->items;
			//$payments=(array) $payments;



            $Item1 = [

                'sale_id'                 => trim($Item->sale_id),
                'client_type'        => trim($Item->client_type),
                'product_name'        => trim($Item->product_name),
                'product_code'        => trim($Item->product_code),



                    ];



                    $Item11 = [];

                    if($Item->cloth_length)
                        $Item11['cloth_length'] = $Item->cloth_length;
                    if($Item->cloth_neck)
                        $Item11['cloth_neck'] = $Item->cloth_neck;
                    if($Item->cloth_shoulder)
                        $Item11['cloth_shoulder'] = $Item->cloth_shoulder;
                    if($Item->cloth_chest)
                        $Item11['cloth_chest'] = $Item->cloth_chest;
                    if($Item->cloth_waist)
                        $Item11['cloth_waist'] = $Item->cloth_waist;
                    if($Item->cloth_seat)
                        $Item11['cloth_seat'] = $Item->cloth_seat;
                    if($Item->cloth_sleasves)
                        $Item11['cloth_sleasves'] = $Item->cloth_sleasves;
                    if($Item->cloth_sleav_circum)
                        $Item11['cloth_sleav_circum'] = $Item->cloth_sleav_circum;
                    if($Item->modelTypeID)
                        $Item11['modelTypeID'] = $Item->modelTypeID;
                    if($Item->collerTypeID)
                        $Item11['collerTypeID'] = $Item->collerTypeID;
                    if($Item->cuffTypeID)
                        $Item11['cuffTypeID'] = $Item->cuffTypeID;
                    if($Item->pocketTypeID)
                        $Item11['pocketTypeID'] = $Item->pocketTypeID;
                    if($Item->fillingTypeID)
                        $Item11['fillingTypeID'] = $Item->fillingTypeID;
                    if($Item->zipperTypeID)
                        $Item11['zipperTypeID'] = $Item->zipperTypeID;
                    if($Item->notes)
                        $Item11['notes'] = $Item->notes;
                    if($Item->clothColor)
                        $Item11['clothColor'] = $Item->clothColor;
                    if($Item->tatrezType)
                        $Item11['tatrezType'] = $Item->tatrezType;
                    if($Item->cloth_thigh)
                        $Item11['cloth_thigh'] = $Item->cloth_thigh;
                    if($Item->cloth_leg_length)
                        $Item11['cloth_leg_length'] = $Item->cloth_leg_length;
                    if($Item->cloth_shoulder_width)
                        $Item11['cloth_shoulder_width'] = $Item->cloth_shoulder_width;
                    if($Item->cloth_top_length)
                        $Item11['cloth_top_length'] = $Item->cloth_top_length;
                    if($Item->cloth_hand_wrist)
                        $Item11['cloth_hand_wrist'] = $Item->cloth_hand_wrist;
                    if($Item->cloth_leg_width)
                        $Item11['cloth_leg_width'] = $Item->cloth_leg_width;






                        krsort($Item1);
                        krsort($Item11);



			//$this->sma->print_arrays($Item1['sale_id'],$Item11);


				if($this->pos2_model->getSaleByID($Item1['sale_id']) && $this->pos2_model->getSaleItemTailor($Item1['sale_id'],$Item1['client_type'],$Item1['product_name'],$Item1['product_code'])){
					if($this->pos2_model->updateSaleItemTailor($Item1,$Item11)){

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
                    'message' => 'Sale item could not be found for sale_id ' . $Item1['sale_id'] . ' and  client_type ' . $Item1['client_type'] . ' and  product_name ' . $Item1['product_name'] . ' and  product_code ' . $Item1['product_code'] . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);

		}










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
