<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetMeasure extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('users_api');
        $this->load->api_model('Tailer_api');

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
        $flageTrue=0;
		$flageFalse=0;





        if($sales->type && $sales->type!=null){
			$type=$sales->type;
		}else{
		$this->set_response([
                    'message' => 'type is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        if($sales->data && $sales->data!=null){
			$sales=$sales->data;


            foreach ($sales as $date) {
                $name=$date->name;

                if($type=="model"){


                    if($Model_Type_ID=$this->Tailer_api->insertTR_Model($name)){

                        $products_temp = [
                            'name'            => $name,
                            'Model_Type_ID' => $Model_Type_ID,
                            'status'            => true

                            ];
                            $flageTrue=1;

                            $products_temp2[]=$products_temp;



                    }else{
                        $products_temp = [
                            'name'            => $name,
                            'status'            => false

                            ];
                            $flageFalse=1;

                            $products_temp2[]=$products_temp;


                            }

                }else if($type=="collar"){


                    if($Collar_Type_ID=$this->Tailer_api->insertTR_Collar($name)){
                        $products_temp = [
                            'name'            => $name,
                            'Collar_Type_ID' => $Collar_Type_ID,
                            'status'            => true

                            ];
                            $flageTrue=1;

                            $products_temp2[]=$products_temp;



                    }else{
                        $products_temp = [
                            'name'            => $name,
                            'status'            => false

                            ];
                            $flageFalse=1;

                            $products_temp2[]=$products_temp;


                            }


                }else if($type=="cuff"){


                    if($Cuff_Type_ID=$this->Tailer_api->insertTR_Cuff($name)){

                        $products_temp = [
                            'name'            => $name,
                            'Cuff_Type_ID' => $Cuff_Type_ID,
                            'status'            => true

                            ];
                            $flageTrue=1;

                            $products_temp2[]=$products_temp;



                    }else{
                        $products_temp = [
                            'name'            => $name,
                            'status'            => false

                            ];
                            $flageFalse=1;

                            $products_temp2[]=$products_temp;


                            }


                }else if($type=="filling"){


                    if($Filling_Type_ID=$this->Tailer_api->insertTR_Filling($name)){

                        $products_temp = [
                            'name'            => $name,
                            'Filling_Type_ID' => $Filling_Type_ID,
                            'status'            => true

                            ];
                            $flageTrue=1;

                            $products_temp2[]=$products_temp;



                    }else{
                        $products_temp = [
                            'name'            => $name,
                            'status'            => false

                            ];
                            $flageFalse=1;

                            $products_temp2[]=$products_temp;


                            }

                }else if($type=="pocket"){


                    if($Pocket_Type_ID=$this->Tailer_api->insertTR_Pocket($name)){

                        $products_temp = [
                            'name'            => $name,
                            'Pocket_Type_ID' => $Pocket_Type_ID,
                            'status'            => true

                            ];
                            $flageTrue=1;

                            $products_temp2[]=$products_temp;



                    }else{
                        $products_temp = [
                            'name'            => $name,
                            'status'            => false

                            ];
                            $flageFalse=1;

                            $products_temp2[]=$products_temp;


                            }
                }else if($type=="zipper"){


                    if($Zipper_Type_ID=$this->Tailer_api->insertTR_Zipper($name)){

                        $products_temp = [
                            'name'            => $name,
                            'Zipper_Type_ID' => $Zipper_Type_ID,
                            'status'            => true

                            ];
                            $flageTrue=1;

                            $products_temp2[]=$products_temp;



                    }else{
                        $products_temp = [
                            'name'            => $name,
                            'status'            => false

                            ];
                            $flageFalse=1;

                            $products_temp2[]=$products_temp;


                            }

                }else{

                    $products_temp = [
                        'name'            => $name,
                        'message' => 'type not found',
                        'status'            => false

                        ];
                        $flageFalse=1;

                        $products_temp2[]=$products_temp;


                        }


                }

                $message="";
        if($flageTrue==1 && $flageFalse==0){
           $this->response([
                'data' => $products_temp2,
                'message' => 'added',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else if($flageTrue==1 && $flageFalse==1){
            $this->response([
                'data' => $products_temp2,
                'message' => 'Partial added',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }else{
            $this->response([
                'data' => $products_temp2,
                'message' => 'No added',
                'status'  => true,
            ], REST_Controller::HTTP_OK);

        }



		}else{
		$this->set_response([
                    'message' => 'data is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}






		/*
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
				*/

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

    }
}
