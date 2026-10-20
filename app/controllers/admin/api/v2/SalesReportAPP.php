<?php


defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SalesReportAPP extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
		$this->load->api_model('warehouses_api');




    }

	protected function setSale($sale)
    {

        $sale = (array) $sale;
        //ksort($sale);
        return $sale;
    }




    public function index_post()
    {

		//$sales = urldecode($this->post('sales'));
		$json = file_get_contents('php://input');
		$sale_rep =json_decode($json);
		//$sales=$sales->JSON3;
		//$id="";
        if($sale_rep->warehouse_code && $sale_rep->warehouse_code!=null){
			$warehouse_id= $this->warehouses_api->getWarehouseIdByCode($sale_rep->warehouse_code);

		}

		//$date="";
		if($sale_rep->start_date && $sale_rep->start_date!=null){
			//$supply_date=$sales->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'start_date is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        if($sale_rep->end_date && $sale_rep->end_date!=null){
			//$supply_date=$sales->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'end_date is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		//$sale_status="";
        $sales = $this->sales_api->getSalesReportAPP($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);

				//$user_register = $this->pos2_model->registerData($sale_rep->user_id);


                $sl_data = [];
                $count=0;
                if ($sales) {
                foreach ($sales as $sale) {
                    $count++;
                    $sl_data[]        = $this->setSale($sale);

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
                    'message' => 'No sale record found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);*/
            }







    }
}
