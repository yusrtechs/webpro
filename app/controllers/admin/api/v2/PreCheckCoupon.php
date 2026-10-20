<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class PreCheckCoupon extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Coupons_api');


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
		$coupon =json_decode($json);
		//$sales=$sales->JSON3;
		//$id="";
		if($coupon->coupon_code && $coupon->coupon_code!=null){
			//$id=$sales->coupon_code;
		}else{
		$this->set_response([
                    'message' => 'Failure',
                    'description' => 'coupon_code is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		//$date="";
		/*if($coupon->customer_id && $coupon->customer_id!=null){
			//$supply_date=$sales->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'Failure',
                    'description' => 'customer_id is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}*/

        /*if($coupon->customer && $coupon->customer!=null){
			//$supply_date=$sales->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'Failure',
                    'description' => 'customer is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}*/
		//$sale_status="";


				//$user_register = $this->pos2_model->registerData($coupon->coupon_code);

                //$customer_id=  $this->pos2_model->getCompanyByOfflineIDV2($coupon->customer_id,$coupon->customer)->id ? $this->pos2_model->getCompanyByOfflineIDV2($coupon->customer_id,$coupon->customer)->id : ($this->pos2_model->getCompanyByidIDV2($coupon->customer_id,$coupon->customer)->id ? $this->pos2_model->getCompanyByidIDV2($coupon->customer_id,$coupon->customer)->id : ($this->pos2_model->getCompanyByOfflineID($coupon->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($coupon->customer_id)->id : $coupon->customer_id));


//                $coupons=$this->sales_model->getCouponByCode(trim($coupon->coupon_code),trim($customer_id));
                $coupons=$this->sales_model->getCouponByCode(trim($coupon->coupon_code));

            if($coupons && (strlen(trim($coupon->coupon_code))==8)){

                $check=$this->sales_model->checkCouponById($coupons->id);

                if($check){

                    $limitFlage=1;
                    if($check->usage_limit!=0){

                        if($this->sales_model->getAllSalessWithCouponCode(trim($coupon->coupon_code))->count < $check->usage_limit){
                            $limitFlage=1;
                        }else{
                            $limitFlage=0;
                        }

                    }

                    if($limitFlage==1){
            //$this->sma->print_arrays($check);


			//$this->sma->print_arrays($paymentcash);

            $check->coupon_code=trim($coupon->coupon_code);

				//if($this->pos2_model->closePOSRegisterTailor( $pos_register_open, $pos_register_close,str_replace("cpesoft_","",$this->db->database))){
					$this->set_response([
                    'message' => 'Success',
                    'description' => 'The coupon is active',
					'status'  => true,
                    'coupon' => $check,

                ], REST_Controller::HTTP_OK);

            }else{
                $this->response([
                    'message' => 'Failure',
                    'description' => 'The coupon is inactive',
                    'status'  => false,
                    'coupon' => null,
                ], REST_Controller::HTTP_OK);
            }

            }else{
                $this->response([
                    'message' => 'Failure',
                    'description' => 'The coupon is inactive',
                    'status'  => false,
                    'coupon' => null,
                ], REST_Controller::HTTP_OK);
            }

            }else{
                $this->response([
                    'message' => 'Failure',
                    'description' => 'The coupon is invalid',
                    'status'  => false,
                    'coupon' => null,
                ], REST_Controller::HTTP_OK);
            }







    }
}
