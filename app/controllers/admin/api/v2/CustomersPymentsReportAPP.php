<?php


defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class CustomersPymentsReportAPP extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
		$this->load->admin_model('pos2_model');




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
        if($sale_rep->customer_id && $sale_rep->customer_id!=null && $sale_rep->customer_id!="" && $sale_rep->customer && $sale_rep->customer!=null && $sale_rep->customer!="" ){
			$customer_id=  $this->pos2_model->getCompanyByOfflineIDV2($sale_rep->customer_id,$sale_rep->customer)->id ? $this->pos2_model->getCompanyByOfflineIDV2($sale_rep->customer_id,$sale_rep->customer)->id : ($this->pos2_model->getCompanyByidIDV2($sale_rep->customer_id,$sale_rep->customer)->id ? $this->pos2_model->getCompanyByidIDV2($sale_rep->customer_id,$sale_rep->customer)->id : ($this->pos2_model->getCompanyByOfflineID($sale_rep->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($sale_rep->customer_id)->id : $sale_rep->customer_id));
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
        $sales = $this->sales_api->getCustomersPymentsReportAPP($sale_rep->start_date,$sale_rep->end_date,$customer_id);

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
