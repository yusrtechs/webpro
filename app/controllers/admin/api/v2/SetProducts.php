<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetProducts extends REST_Controller
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

		$products=$sales->product;
        if ($products) {
        $warehouse= $sales->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sales->warehouse_code) : '';
        if ($warehouse) {

        $failed_sales = [];
        $flageTrue=0;
		$flageFalse=0;

		foreach ($products as $product) {
			$slug=trim($product->name);
			$slug= str_replace(" ","-",$slug);

			$product1           = [
				'type'            => 'standard',
				'type2'            => 'primary',
				'quantity'            => '0',
				'cost'            => '0',
				'warehouse'            => trim($warehouse),
				'name'            => trim($product->name),
				'slug'            => isset($product->slug)? trim($product->slug):trim($slug),
				'code'            => isset($product->code) ? trim($product->code) : rand(100000, 9999999),
				'tax_rate'            => trim($product->tax_rate),
				'tax_method'            => trim($product->tax_method),
				'category_id'            => isset($product->category_id) ? trim($product->category_id) : $this->pos2_model->getSetting()->default_category,
				'unit'            => trim($product->unit),
				'sale_unit'            => trim($product->unit),
				'purchase_unit'            => trim($product->unit),
				'price'            => trim($product->price),
                'item_no'            => isset($product->item_no)? trim($product->item_no) : null,

            ];
			//krsort($cust1);

            //$this->sma->print_arrays($product1);
			if($this->pos2_model->addAjaxProductFromSales($product1)){
			$product_temp = [
			'code'            => trim($product1['code']),
			'status'            => true
			];
            $flageTrue=1;

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);
                $product_temp = [
                    'code'            => trim($product1['code']),
                    'status'            => false
                    ];
                    $flageFalse=1;

                    $product_temp2[]=$product_temp;

			}
		}












        $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'products' => $product_temp2,
                'Cleaned' => 'Cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'products' => $product_temp2,
                'Cleaned' => 'Partial cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'products' => $product_temp2,
                'Cleaned' => 'No cleaned',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }
}else{

    $this->response([
        'message' => 'This request does not contain warehouse_code',
        'status'  => false,
    ], REST_Controller::HTTP_BAD_REQUEST);
}
        }else{

            $this->response([
                'message' => 'This request does not contain products',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
