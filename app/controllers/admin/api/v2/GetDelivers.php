<?php
//ini_set("display_errors", 1);
ini_set('memory_limit','1024M');

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class GetDelivers extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
        $this->load->api_model('warehouses_api');
        $this->load->api_model('Users_api');
        $this->load->admin_model('pos2_model');

    }

    protected function setSale($sale)
    {
        /*unset($sale->address_id, $sale->api, $sale->attachment, $sale->hash, $sale->pos, $sale->reserve_id, $sale->return_id, $sale->return_sale_ref, $sale->return_sale_total, $sale->sale_id, $sale->shop, $sale->staff_note, $sale->surcharge, $sale->updated_at, $sale->suspend_note);
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
        }*/
        $sale = (array) $sale;
        //ksort($sale);
        return $sale;
    }

    public function index_get()
    {
        $code = $this->get('code');

		$filters1 = [
            'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'biller_id'    => $this->get('user_id') ? $this->Users_api->getBillerIdByUserId($this->get('user_id')) : '',
			'id'    => $this->get('after_id') ? $this->get('after_id') : 0,
			'Offline_id'    => $this->get('Offline_id') ? $this->get('Offline_id') : null,
			'include'     => $this->get('include') ? explode(',', $this->get('include')) : null,
            'start'       => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'       => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $count_row,
            'start_date'  => $this->get('start_date') && is_numeric($this->get('start_date')) ? $this->get('start_date') : null,
            'end_date'    => $this->get('end_date') && is_numeric($this->get('end_date')) ? $this->get('end_date') : null,
            'order_by'    => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'decs'],
            'customer_id' => $this->get('customer_id') ? $this->get('customer_id') : null,
            'customer'    => $this->get('customer') ? $this->get('customer') : null,
        ];
		$count_row=0;
		$count_row=$this->sales_api->countDelivers($filters1);


        $filters = [
            'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'biller_id'    => $this->get('user_id') ? $this->Users_api->getBillerIdByUserId($this->get('user_id')) : '',
			'id'    => $this->get('after_id') ? $this->get('after_id') : 0,
			'Offline_id'    => $this->get('Offline_id') ? $this->get('Offline_id') : null,
			'include'     => $this->get('include') ? explode(',', $this->get('include')) : null,
            'start'       => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'       => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $count_row,
            'start_date'  => $this->get('start_date') && is_numeric($this->get('start_date')) ? $this->get('start_date') : null,
            'end_date'    => $this->get('end_date') && is_numeric($this->get('end_date')) ? $this->get('end_date') : null,
            'order_by'    => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'decs'],
            'customer_id' => $this->get('customer_id') ? $this->get('customer_id') : null,
            'customer'    => $this->get('customer') ? $this->get('customer') : null,
        ];
        $tailorFlag=0;

	$sales = $this->sales_api->getDelivers($filters);
	//$sales = $this->sales_api->getSales($filters);
	//$this->sma->print_arrays($sales);


if ($filters['warehouse_id']) {


        if ($code === null) {

			$sl_data = [];
            if ($sales) {



                foreach ($sales as $sale) {

                        $sale->items = (array)$this->sales_api->getDeliverItems($sale->id);



                    //$sale->created_by = $this->sales_api->getUser($sale->created_by);
                    $sl_data[]        = $this->setSale($sale);
                }
                //$size = mb_strlen(json_encode($sl_data, JSON_NUMERIC_CHECK), '8bit');
                $data = [
                    'data'  => $sl_data,
                    'limit' => (int) $filters['limit'],
                    'start' => (int) $filters['start'],
                    'total' => $this->sales_api->countDelivers($filters),
                    'message' => "Received ".($this->sales_api->countDelivers($filters))." Delivers",

                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
				$this->response([
                    'data'  => $sl_data,
					'message' => 'No Deliver record found.',
                    'status'  => true,
                ], REST_Controller::HTTP_OK);

                /*$this->response([
                    'message' => 'No sale record found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);*/
            }
        }

} else {
    $this->response([
        'message' => 'Warehouse does not exist ',
        'status'  => false,
    ], REST_Controller::HTTP_BAD_REQUEST);
}
    }
}
