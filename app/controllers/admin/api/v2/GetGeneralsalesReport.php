<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class GetGeneralsalesReport extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
        $this->load->api_model('Users_api');
        $this->load->api_model('warehouses_api');
        $this->load->admin_model('db_model');
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
        //$code = $this->get('code');









                $data = [
                    'allSales'  => $this->db_model->getAllSales(),
                    'rSales'  => $this->db_model->getReturns(),
                    'message' => "Success",

                ];
                $this->response($data, REST_Controller::HTTP_OK);



    }
}
