<?php
//ini_set("display_errors", 1);
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SalesReportAggregationAPP extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('warehouses_api');

		$this->load->admin_model('db_model');




    }

	protected function setArr($warehouse)
    {
        unset($warehouse->price_group_id);
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
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
            $sale_rep->start_date ='2020-12-01 00:00:00';
		}

        if($sale_rep->end_date && $sale_rep->end_date!=null){
			//$supply_date=$sales->delivery_date;
		}else{
            $sale_rep->end_date =date('Y-m-d H:i:s');
		}
		//$sale_status="";


				//$user_register = $this->pos2_model->registerData($pos_reg->user_id);


                $sales     = $this->db_model->getAllSales($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $purchases     = $this->db_model->getAllPurchases($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $re_sales     = $this->db_model->getReturnsV2($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $re_purchases     = $this->db_model->getPurchasesReturnsV2($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $salesLocation     = $this->db_model->getSalesLocationChartV2($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $chatData     = $this->db_model->getChartDataTrendsV2($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $bs        = $this->db_model->getBestSellerchartV2($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $ubs        = $this->db_model->getUnBestSellerchartV2($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $bc        = $this->db_model->getBestCustomerschartV2($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
                $ubc        = $this->db_model->getUnBestCustomerschartV2($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);

			//$this->sma->print_arrays($paymentcash);

				//if($this->pos2_model->closePOSRegisterTailor( $pos_register_open, $pos_register_close,str_replace("cpesoft_","",$this->db->database))){
					$this->set_response([
                    'message' => 'Success',
					'status'  => true,
                    'total_sales' => $sales->grand_total,
                    'total_paid' => $sales->paid,
                    'due_amount' => $sales->due,
                    'sales_invoices' => $sales->total,
                    'return invoice' => $re_sales->returne_amount,
                    'total_returns' => $re_sales->return_sales,
                    'sales_location' => $salesLocation,
                    'sales_trends' => $chatData,
                    'best_sales' => $bs,
                    'worst_sales' => $ubs,
                    'top_customers' => $bc,
                    'less_customers' => $ubc,
                    'total_purchases' => isset($purchases->grand_total) ? $this->sma->formatDecimal($purchases->grand_total) : '0.00',
                    'total_paid_purchases' => isset($purchases->paid) ? $this->sma->formatDecimal($purchases->paid) : '0.00',
                    'due_amount_purchases' => isset($purchases->due) ? $this->sma->formatDecimal($purchases->due) : '0.00',
                    'return_purchase' => isset($re_purchases->return_purchase) ? $this->sma->formatDecimal($re_purchases->return_purchase) : '0.00',






                ], REST_Controller::HTTP_OK);




    }
}
