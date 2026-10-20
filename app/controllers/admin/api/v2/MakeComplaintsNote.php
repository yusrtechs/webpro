<?php

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class MakeComplaintsNote extends REST_Controller
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
		$complaints =json_decode($json);
		//$sales=$sales->JSON3;
		//$this->sma->print_arrays($this->Settings);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;

		//$payments_only=$sales->payment;
        if ($complaints) {




            if ($complaints->id) {
                if ($complaints->date) {
                    if ($complaints->client_name) {
                        if ($complaints->title) {
                            if ($complaints->warehouse_code) {
                                if ($complaints->user_id) {
                                    if ($complaints->complaint_id) {


            $company=str_replace("cpesoft_","",$this->db->database);
            $otherdb = $this->load->database('info', true);
            $rowdb = $otherdb->query("SELECT `id` FROM `complaints` WHERE `company`= '".$company."' and offline_id=".$complaints->complaint_id." and warehouse_code='".$complaints->warehouse_code."'  and user_id=".$complaints->user_id." ")->result_array();





            if($rowdb){

                $complaint_id=$rowdb[0]['id'];

        $failed_sales = [];
        $flageTrue=0;
		$flageFalse=0;






                $complaint3 = [
                    'offline_id'        => trim($complaints->id),
                    'complaint_id'        => trim($complaint_id),
                    'date' => $complaints->date,
                    'title' => $complaints->title,
                    'client_name' => $complaints->client_name,
                    'company' => $company,
                    'warehouse_code' => trim($complaints->warehouse_code),
                    'user_id' => trim($complaints->user_id),
                    'biller_id' => $complaints->biller_id,




                ];

            //$this->sma->print_arrays($otherdb);
			//krsort($expense1);
            $result = $otherdb->insert('complaints_note', $complaint3);
			if($result>0){

                $this->response([
                    'id'            => $complaints->id,
			        'status'            => true
                    ], REST_Controller::HTTP_OK);
            $flageTrue=1;

			$product_temp2[]=$product_temp;

			}else{
				//array_push($failed_sales['customer'],$customer1['Offline_id']);
                $this->response([
                    'id'            => $complaints->id,
			        'status'            => false
                    ], REST_Controller::HTTP_OK);
                    $flageFalse=1;

                    $product_temp2[]=$product_temp;

			}






        }else{
            $this->response([
                'message' => 'Complaint_id not found',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);

        }


    }else{

        $this->response([
            'message' => 'This request does not contain complaint_id',
            'status'  => false,
        ], REST_Controller::HTTP_BAD_REQUEST);
    }




        }else{

            $this->response([
                'message' => 'This request does not contain user_id',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }




    }else{

        $this->response([
            'message' => 'This request does not contain warehouse code',
            'status'  => false,
        ], REST_Controller::HTTP_BAD_REQUEST);
    }

    }else{

        $this->response([
            'message' => 'This request does not contain title',
            'status'  => false,
        ], REST_Controller::HTTP_BAD_REQUEST);
    }

    }else{

        $this->response([
            'message' => 'This request does not contain client_name',
            'status'  => false,
        ], REST_Controller::HTTP_BAD_REQUEST);
    }

    }else{

    $this->response([
        'message' => 'This request does not contain date',
        'status'  => false,
    ], REST_Controller::HTTP_BAD_REQUEST);
}

    }else{

        $this->response([
            'message' => 'This request does not contain id',
            'status'  => false,
        ], REST_Controller::HTTP_BAD_REQUEST);
    }



        }else{

            $this->response([
                'message' => 'This request does not contain complaints',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
