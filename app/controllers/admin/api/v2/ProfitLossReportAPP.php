<?php


defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class ProfitLossReportAPP extends REST_Controller
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
        $total_sales = $this->sales_api->getTotalSalesForProfitandLess($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
        $total_cost_goods = $this->sales_api->getTotalSalesCosofGoods($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
        $total_expenses = $this->sales_api->getTotalExpensesForProfitandLess($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
        $total_revenues = $this->sales_api->getTotalRevenues($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);
        $total_purchases = $this->sales_api->getTotalPurchases($sale_rep->start_date,$sale_rep->end_date,$warehouse_id);





                $net_sales=$this->sma->formatDecimal($total_sales->total_amount+$total_sales->rtotal_amount-$total_sales->total_discount-$total_sales->discount_allowance,12);
                $operating_profit_loss=$this->sma->formatDecimal($net_sales - $total_cost_goods->item_cost_value - $total_expenses->total_amount,12);
                $other_expenses=$this->sma->formatDecimal( $total_expenses->other_expenses,12);
                $other_revenues=$this->sma->formatDecimal($total_sales->other_revenues + $total_revenues->total_amount,12);
                $net_profit_loss=$this->sma->formatDecimal($operating_profit_loss - $other_expenses + $other_revenues + $total_purchases->discount_earned,12);

                $sl_data = [];
                $count=1;

                $revenues = [
                    "sales" => $this->sma->formatDecimal($total_sales->total_amount),
                    "return_sales" => $this->sma->formatDecimal($total_sales->rtotal_amount),
                    "discounts" => $this->sma->formatDecimal($total_sales->total_discount),
                    "discount_allowance" => $this->sma->formatDecimal($total_sales->discount_allowance),
                    "net_sales" => $this->sma->formatDecimal($net_sales),
                    "total_cost_of_goods_sold" => $this->sma->formatDecimal($total_cost_goods->item_cost_value),
                    "gross_profit" => $this->sma->formatDecimal(($net_sales - $total_cost_goods->item_cost_value)),
                    "expenses" => $this->sma->formatDecimal($total_expenses->total_amount),
                    "operating_profit_loss" => $this->sma->formatDecimal($operating_profit_loss),
                    "other_expenses" => $this->sma->formatDecimal($other_expenses),
                    "discount_earned" => $this->sma->formatDecimal($total_purchases->discount_earned),
                    "other_revenues" => $this->sma->formatDecimal($other_revenues),
                    "net_profit_loss" => $this->sma->formatDecimal($net_profit_loss),


                ];



                $sl_data[]=$revenues;
                $data = [
                    'data'  => $sl_data,
                    'limit' => $count,
                    'start' => 1,
                    'total' => $count,
                    'message' => "Received ".$count." Records",

                ];
                $this->response($data, REST_Controller::HTTP_OK);








    }
}
