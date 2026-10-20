<?php
//ini_set("display_errors", 1);
//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class MakeInventory extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('users_api');

		$this->load->admin_model('pos2_model');
		$this->load->admin_model('products_model');
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

		$json = file_get_contents('php://input');
		$sales =json_decode($json);


		$response["success"] = 0;





        $sale=$sales->meta;
        $products=$sales->data;
        if ($sale) {
        //$this->sma->print_arrays($sales);
        $failed_sales = [];

		$flageTrue=0;
		$flageFalse=0;





			 $warehouse_id= $sale->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sale->warehouse_code) : $sale->warehouse_id;


             $sale1           = [
				'date' => trim($sale->date),
                'type'            => trim($sale->type),
                'reference_no'            => isset($sale->reference_no) ? trim($sale->reference_no) : null,
                'warehouse_id'            => $warehouse_id,
                'created_by'            => trim($sale->created_by),
                'offline_id'            => trim($sale->id),
                'initial_file'            => 'from_offline',
                'final_file'            => 'from_offline',
                'products'            => trim($sale->rows),
                'rows'            => trim($sale->rows),
                'finalized'            => 1,


            ];

            $id = $this->products_model->addStockCountV2($sale1);



            $product2 = [];


			foreach ($products as $pr) {

                if ($product = $this->products_model->getProductByCodeAndUint(trim($pr->product_code),trim($pr->unit_id))) {
                    // $this->sma->print_arrays($product);
                    $pr->counted = !empty($pr->quantity) ? $pr->quantity : 0;
                    $categories=$sale1['categories']=="" ? "" :  explode(",",$sale1['categories']);
                    $brands=$sale1['brands']=="" ? "" :  explode(",",$sale1['brands']);
                    //$this->sma->print_arrays($sale1);


                    $products_expected = $this->products_model->getStockCountProductsQuantity($sale1['warehouse_id'], $sale1['type'], $categories,$brands,trim($pr->product_code));



                    foreach ($products_expected as $product_expected) {

                    $pr->expected= $this->sma->formatDecimal($product_expected->quantity);


            }
            //$this->sma->print_arrays($pr);



                    if ($pr->expected == $pr->quantity) {
                        $matches++;
                    } else {
                        $differences++;
                    }
                        $pr->stock_count_id     = $id;
                        $pr->product_id         = $product->id;
                        $pr->cost               = '0';
                        $pr->unit               = $product->unit;
                        $pr->product_variant_id = "0";//empty($pr['product_variant']) ? null : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
                        $product2[]               = $pr;

                } else {
                    //$this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                    //admin_redirect('products/finalize_count_temp/' . $id);
                }
                $rw++;











					}


                    //$this->sma->print_arrays($sale1,$product2);
                   // $this->sma->print_arrays($sale1,$product2);
			//$product2[] = $products;


            //$this->sma->print_arrays($id, $sale1, $product2);
            $flag=0;
            foreach ($product2 as $product) {
                $pr = ['stock_count_id' => $product->stock_count_id, 'product_id' => $product->product_id, 'cost' => $product->cost, 'unit' => $product->unit, 'product_variant_id' => $product->product_variant_id, 'expected' => $product->expected, 'counted' => $product->counted, 'product_name' => $product->product_name, 'product_code' => $product->product_code];
                if($this->db->insert('stock_count_items', $pr)){
                    $flag=1;
                }
                //$this->sma->print_arrays($pr);
            }

					if($flag==1){
				//$this->sma->print_arrays($sale2['sale_id']);

                //$this->sma->print_arrays($sale2,$sale1,$product2, $payment2);
			$response["success"] = 1;
			//array_push($failed_sales['sales'],'1');
			$salse_temp = [
                'id'            => trim($id),
                'offline_id'            => trim($sale1['offline_id']),
                'status'            => true
			];
            $flageTrue=1;
			$salse_temp2[]=$salse_temp;


            }else{
			$response["success"] = 0;
            $salse_temp = [
                'offline_id'            => trim($sale1['offline_id']),
			    'status'            => false

                ];
                $flageFalse=1;
                $salse_temp2[]=$salse_temp;
		}








		 $message="";
         if($flageTrue==1 && $flageFalse==0){
            $this->response([
                 'data' => $salse_temp2,
                 'Cleaned' => 'Cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }else if($flageTrue==1 && $flageFalse==1){
             $this->response([
                 'data' => $salse_temp2,
                 'Cleaned' => 'Partial cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }else{
             $this->response([
                 'data' => $salse_temp2,
                 'Cleaned' => 'No cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }
        }else{

            $this->response([
                'message' => 'This request does not contain data',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
