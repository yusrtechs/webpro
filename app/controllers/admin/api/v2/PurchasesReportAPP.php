<?php


defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class PurchasesReportAPP extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('purchases_api');
		$this->load->api_model('warehouses_api');




    }

	protected function setpurchase($purchase)
    {

        $purchase = (array) $purchase;
        //ksort($purchase);
        return $purchase;
    }




    public function index_post()
    {

		//$purchases = urldecode($this->post('purchases'));
		$json = file_get_contents('php://input');
		$purchase_rep =json_decode($json);
		//$purchases=$purchases->JSON3;
		//$id="";
        if($purchase_rep->warehouse_code && $purchase_rep->warehouse_code!=null){
			$warehouse_id= $this->warehouses_api->getWarehouseIdByCode($purchase_rep->warehouse_code);

		}

		//$date="";
		if($purchase_rep->start_date && $purchase_rep->start_date!=null){
			//$supply_date=$purchases->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'start_date is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        if($purchase_rep->end_date && $purchase_rep->end_date!=null){
			//$supply_date=$purchases->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'end_date is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		//$purchase_status="";
        $purchases = $this->purchases_api->getPurchasesReportAPP($purchase_rep->start_date,$purchase_rep->end_date,$warehouse_id);

				//$user_register = $this->pos2_model->registerData($purchase_rep->user_id);


                $sl_data = [];
                $count=0;
                if ($purchases) {
                foreach ($purchases as $purchase) {
                    $count++;
                    $sl_data[]        = $this->setpurchase($purchase);

                }

                $data = [
                    'data'  => $sl_data,
                    'limit' => $count,
                    'start' => 1,
                    'total' => $count,
                    'message' => "Received ".$count." Records",

                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'data'  => $sl_data,
					'message' => 'No records found.',
                    'status'  => true,
                ], REST_Controller::HTTP_OK);
            /*    $this->response([
                    'message' => 'No purchase record found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);*/
            }







    }
}
