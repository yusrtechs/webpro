<?php


defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class TaxDeclarationReport extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('purchases_api');
		$this->load->api_model('warehouses_api');

        $this->load->admin_model('reports_model');
        $this->load->admin_model('pos2_model');
        $this->load->admin_model('purchases_model');



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
		$tax_rep =json_decode($json);
		//$purchases=$purchases->JSON3;
		//$id="";
        if($tax_rep->warehouse_code && $tax_rep->warehouse_code!=null){
			$warehouse_id= $this->warehouses_api->getWarehouseIdByCode($tax_rep->warehouse_code);

		}

		//$date="";
		if($tax_rep->start_date && $tax_rep->start_date!=null){
			//$supply_date=$purchases->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'start_date is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}

        if($tax_rep->end_date && $tax_rep->end_date!=null){
			//$supply_date=$purchases->delivery_date;
		}else{
		$this->set_response([
                    'message' => 'end_date is required ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
		}
		//$purchase_status="";
        $sale_tax_local_basic_details=$this->reports_model->getMultiSalesTaxLocalBasicDetails($tax_rep->start_date, $tax_rep->end_date ,$warehouse_id,null,null,null,null,null);


        $total_sales_amount=0;
	    $total_sales_amount_returned=0;
	    $total_sales_tax=0;




	foreach ($sale_tax_local_basic_details as $sale_tax_local_basic_detail) {

		if($tax_rep->start_date && $tax_rep->end_date){
        $sale_tax_local_basic_details_returned     = $this->reports_model->getMultiSalesTaxLocalBasicDetailsReturned($tax_rep->start_date, $tax_rep->end_date ,$warehouse_id,$sale_tax_local_basic_detail->rate,'','','');


        $ret_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($sale_tax_local_basic_details_returned as $sale_tax_local_basic_details_return) {
            $sdate=$this->pos2_model->getSaleByid($sale_tax_local_basic_details_return->sale_id)->date;
			if (($sdate > $tax_rep->start_date && $sdate < $tax_rep->end_date) || (!isset($sale_tax_local_basic_details_return->sale_id)))
				{
					$qTotal=$qTotal+$sale_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$sale_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$sale_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$sale_tax_local_basic_details_return->total_tax;
				}
            //$this->sma->print_arrays($t);
		}
					$ret_data['total']= $qTotal;
					$ret_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		//$this->data['sale_tax_local_basic_details_returned']=$ret_data_amendment;
		}
		//echo $ret_data_amendment['total']."  ".$ret_data_amendment['total_tax']."<br>";
		//echo $sale_tax_local_basic_detail->total."  ".$sale_tax_local_basic_detail->total_tax."<br>";
		$Tret_grand_total=0;
		$Tgrand_total=0;
		$Ttotal_tax=0;


	if($ret_data_amendment['total']){
		$Tgrand_total=($sale_tax_local_basic_detail->total-$ret_data_amendment['total']);//-$Tret_grand_total;
		$Tret_grand_total=($ret_data_amendment['total']);
		$Ttotal_tax=(($sale_tax_local_basic_detail->total_tax));
	}else{
		$Tgrand_total=($sale_tax_local_basic_detail->total);//-$Tret_grand_total;
		$Tret_grand_total=0;
		$Ttotal_tax=($sale_tax_local_basic_detail->total_tax);

	}

        $Sales_subject_to_the_standard=(int)$this->sma->formatNumber($sale_tax_local_basic_detail->rate)."%";
        $Sales_amount=$this->sma->formatDecimal($Tgrand_total);
		$Sales_updatet_amount=$this->sma->formatDecimal(abs($Tret_grand_total));
		$Sales_total_tax=$this->sma->formatDecimal($Ttotal_tax);


	$total_sales_amount=$total_sales_amount+($Tgrand_total);
	$total_sales_amount_returned=$total_sales_amount_returned+abs($Tret_grand_total);
	$total_sales_tax=$total_sales_tax+($Ttotal_tax);
	}


    $qTotal_amendmentZero=0;
	$qTotal_amendmentinternational=0;
	$qTotal_amendmentnt=0;

    if($tax_rep->start_date && $tax_rep->end_date){
		    $sale_tax_local_zero_returned     = $this->reports_model->getMultiSalesTaxLocalZeroReturned($tax_rep->start_date, $tax_rep->end_date,$warehouse_id,null,null,null);
        }

	if($tax_rep->start_date && $tax_rep->end_date){
		foreach ($sale_tax_local_zero_returned as $sale_tax_local_zero_return) {

		$sdate=$this->pos2_model->getSaleByid($sale_tax_local_zero_return->sale_id)->date;
			if ($sdate > $tax_rep->start_date && $sdate < $tax_rep->end_date)
				{
				//	$qTotal_Zero=$qTotal_Zero+abs($sale_tax_local_zero_return->total);
				}
			else
				{
					$qTotal_amendmentZero=$qTotal_amendmentZero+abs($sale_tax_local_zero_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}

    if($tax_rep->start_date && $tax_rep->end_date){
		    $sale_tax_international_returned     = $this->reports_model->getMultiSalesTaxInternationalReturned($tax_rep->start_date, $tax_rep->end_date ,$warehouse_id,null,null,null);
        }

	if($tax_rep->start_date && $tax_rep->end_date){
		foreach ($sale_tax_international_returned as $sale_tax_international_return) {
        $sdate=$this->pos2_model->getSaleByid($sale_tax_international_return->sale_id)->date;
			if ($sdate > $tax_rep->start_date && $sdate < $tax_rep->end_date)
				{
				}
			else
				{
					$qTotal_amendmentinternational=$qTotal_amendmentinternational+abs($sale_tax_international_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}

    $sale_tax_local_nt_returned     = $this->reports_model->getMultiSalesTaxLocalNTReturned($tax_rep->start_date, $tax_rep->end_date,$warehouse_id,null,null,null);

	if($tax_rep->start_date && $tax_rep->end_date){
		foreach ($sale_tax_local_nt_returned as $sale_tax_local_nt_return) {
       $sdate=$this->pos2_model->getSaleByid($sale_tax_local_nt_return->sale_id)->date;
			if ($sdate > $tax_rep->start_date && $sdate < $tax_rep->end_date)
				{
				}
			else
				{
					$qTotal_amendmentnt=$qTotal_amendmentnt+abs($sale_tax_local_nt_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}

    $total_sales_amount=$total_sales_amount+(($sale_tax_local_zero->total) )+(($sale_tax_international->total))+(($sale_tax_local_nt->total));
	$total_sales_amount_returned=$total_sales_amount_returned+abs($qTotal_amendmentZero)+abs($qTotal_amendmentinternational)+abs($qTotal_amendmentnt);



                $sales           = [
                    'Sales_subject_to_the_standard'                 => $Sales_subject_to_the_standard,
                    'Standard_amount'                 => $this->sma->formatDecimal($Sales_amount),
                    'Standard_updatet_amount'                 => $this->sma->formatDecimal($Sales_updatet_amount),
                    'Standard_total_tax'                 => $this->sma->formatDecimal($Sales_total_tax),
                    'Zero_amount'                 => isset($sale_tax_local_zero->total) ? $this->sma->formatDecimal(($sale_tax_local_zero->total)) : '0.00',
                    'Zero_updatet_amount'                 => isset($qTotal_amendmentZero) ? abs($this->sma->formatDecimal($qTotal_amendmentZero)) : '0.00',
                    'Exports_amount'                 => isset($sale_tax_international->total) ? $this->sma->formatDecimal(($sale_tax_international->total)) : '0.00',
                    'Exports_updatet_amount'                 => isset($qTotal_amendmentinternational) ? abs($this->sma->formatDecimal($qTotal_amendmentinternational)) : '0.00',
                    'Free_tax_amount'                 => isset($sale_tax_local_nt->total) ? $this->sma->formatDecimal(($sale_tax_local_nt->total)) : '0.00',
                    'Free_tax_updatet_amount'                 => isset($qTotal_amendmentnt) ? abs($this->sma->formatDecimal($qTotal_amendmentnt)) : '0.00',
                    'Total_amount'                 => $this->sma->formatDecimal($total_sales_amount),
                    'Total_updatet_amount'                 => $this->sma->formatDecimal($total_sales_amount_returned),
                    'Total_tax'                 => $this->sma->formatDecimal($total_sales_tax),
                ];

    $purchase_tax_local_basic_details = $this->reports_model->getMultiPurchasesTaxLocalBasicDetails($tax_rep->start_date, $tax_rep->end_date,$warehouse_id,null,null,null);


    $total_purchases_amount=0;
	$total_purchases_amount_returned=0;
	$total_purchases_tax=0;
	foreach ($purchase_tax_local_basic_details as $purchase_tax_local_basic_detail) {

		if($tax_rep->start_date && $tax_rep->end_date){
		$purchase_tax_local_basic_details_returned     = $this->reports_model->getMultiPurchasesTaxLocalBasicDetailsReturned($tax_rep->start_date, $tax_rep->end_date ,(isset($_POST['warehouse']) ? $_POST['warehouse'] : ''),$purchase_tax_local_basic_detail->rate,(isset($_POST['grpId']) ? $_POST['grpId'] : ''),(isset($_POST['coId']) ? $_POST['coId'] : ''),(isset($_POST['brId']) ? $_POST['brId'] : ''));
        $ret_p_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($purchase_tax_local_basic_details_returned as $purchase_tax_local_basic_details_return) {
            $sdate=$this->purchases_model->getPurchaseByID($purchase_tax_local_basic_details_return->purchase_id)->date;
			if ($sdate > $tax_rep->start_date && $sdate < $tax_rep->end_date)
				{
					$qTotal=$qTotal+$purchase_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$purchase_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$purchase_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$purchase_tax_local_basic_details_return->total_tax;
				}
        }
					$ret_p_data['total']= $qTotal;
					$ret_p_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		}

		$Tret_grand_total=0;
		$Tgrand_total=0;
		$Ttotal_tax=0;





	if($ret_data_amendment['total']){
		$Tgrand_total=($purchase_tax_local_basic_detail->total-$ret_data_amendment['total']);//-$Tret_grand_total;
		$Tret_grand_total=($ret_data_amendment['total']);
		$Ttotal_tax=(($purchase_tax_local_basic_detail->total_tax));
	}else{
		$Tgrand_total=($purchase_tax_local_basic_detail->total);//-$Tret_grand_total;
		$Tret_grand_total=0;
		$Ttotal_tax=($purchase_tax_local_basic_detail->total_tax);

	}




	$total_purchases_amount=$total_purchases_amount+($Tgrand_total);
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($Tret_grand_total);
	$total_purchases_tax=$total_purchases_tax+($Ttotal_tax);

	}

    $Purchases_subject_to_the_standard=(int)$this->sma->formatNumber($purchase_tax_local_basic_detail->rate)."%";

    $purchase_tax_international_basic_details = $this->reports_model->getMultiPurchasesTaxInternationalBasicDetails($tax_rep->start_date, $tax_rep->end_date,$warehouse_id,null,null,null);


    foreach ($purchase_tax_international_basic_details as $purchase_tax_international_basic_detail) {
		if($tax_rep->start_date && $tax_rep->end_date){
		$purchase_tax_international_basic_details_returned     = $this->reports_model->getMultiPurchasesTaxInternationalBasicDetailsReturned($tax_rep->start_date, $tax_rep->end_date ,$warehouse_id,$purchase_tax_local_basic_details->rate,'','','');
        $ret_p_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($purchase_tax_international_basic_details_returned as $purchase_tax_local_basic_details_return) {
            $sdate=$this->purchases_model->getPurchaseByID($purchase_tax_local_basic_details_return->purchase_id)->date;
			if ($sdate > $tax_rep->start_date && $sdate < $tax_rep->end_date)
				{
					$qTotal=$qTotal+$purchase_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$purchase_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$purchase_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$purchase_tax_local_basic_details_return->total_tax;
				}
        }
					$ret_p_data['total']= $qTotal;
					$ret_p_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		}


		$Tret_grand_total=0;
		$Tgrand_total=0;
		$Ttotal_tax=0;






	if($ret_data_amendment['total']){
		$Tgrand_total=((($purchase_tax_international_basic_detail->total)+($purchase_tax_international_basic_detail->customs_fees ) +($purchase_tax_international_basic_detail->Other_fees ))-$ret_data_amendment['total']);//-$Tret_grand_total;
		$Tret_grand_total=($ret_data_amendment['total']);
		$Ttotal_tax=($purchase_tax_international_basic_detail->total_tax)+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100);
		}else{
		$Tgrand_total=(($purchase_tax_international_basic_detail->total)+($purchase_tax_international_basic_detail->customs_fees ) +($purchase_tax_international_basic_detail->Other_fees ));//-$Tret_grand_total;
		$Tret_grand_total=0;
		$Ttotal_tax=($purchase_tax_international_basic_detail->total_tax)+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100);

	}

    $Imports_for_customs_authority=(int)$this->sma->formatNumber($purchase_tax_international_basic_detail->rate)."%";


	$total_purchases_amount=$total_purchases_amount+($Tgrand_total);
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($Tret_grand_total);
	$total_purchases_tax=$total_purchases_tax+($Ttotal_tax);

	}

    $qTotal_amendmentZero=0;
	$qTotal_amendmentinternational=0;
	$qTotal_amendmentnt=0;

	if($tax_rep->start_date && $tax_rep->end_date){
        $purchase_tax_local_zero_returned     = $this->reports_model->getMultiPurchasesTaxLocalZeroReturned($tax_rep->start_date, $tax_rep->end_date,$warehouse_id,null,null,null);

		foreach ($purchase_tax_local_zero_returned as $sale_tax_local_zero_return) {

		$sdate=$this->purchases_model->getPurchaseByID($sale_tax_local_zero_return->sale_id)->date;
			if ($sdate > $tax_rep->start_date && $sdate < $tax_rep->end_date)
				{
				}
			else
				{
					$qTotal_amendmentZero=$qTotal_amendmentZero+abs($sale_tax_local_zero_return->total);
				}
        }
	}


	if($tax_rep->start_date && $tax_rep->end_date){
        $purchase_tax_local_nt_returned     = $this->reports_model->getMultiPurchasesTaxLocalNTReturned($tax_rep->start_date, $tax_rep->end_date,$warehouse_id,null,null,null);

		foreach ($purchase_tax_local_nt_returned as $sale_tax_local_nt_return) {
       $sdate=$this->purchases_model->getPurchaseByID($sale_tax_local_nt_return->sale_id)->date;
			if ($sdate > $tax_rep->start_date && $sdate < $tax_rep->end_date)
				{
				}
			else
				{
					$qTotal_amendmentnt=$qTotal_amendmentnt+abs($sale_tax_local_nt_return->total);
				}
        }
	}

    $total_purchases_amount_returned=$total_purchases_amount_returned+$qTotal_amendmentZero+$qTotal_amendmentnt;
	$total_purchases_amount=$total_purchases_amount+$purchase_tax_local_zero->total+$purchase_tax_local_nt->total;


                $purchases  =[
                    'Purchases_subject_to_the_standard'                 => $Purchases_subject_to_the_standard,
                    'Standard_amount'                 => $this->sma->formatDecimal($Tgrand_total),
                    'Standard_updatet_amount'                 => $this->sma->formatDecimal(abs($Tret_grand_total)),
                    'Standard_total_tax'                 => $this->sma->formatDecimal($Ttotal_tax),
                    'Zero_amount'                 => isset($purchase_tax_local_zero->total) ? $this->sma->formatDecimal(($purchase_tax_local_zero->total) ) : '0.00',
                    'Zero_updatet_amount'                 => isset($qTotal_amendmentZero) ? abs($this->sma->formatDecimal($qTotal_amendmentZero)) : '0.00',
                    'Imports_for_customs_authority'                 => $Imports_for_customs_authority,
                    'Imports_amount'                 => isset($purchase_tax_international_basic_detail->total) ? $this->sma->formatDecimal($purchase_tax_international_basic_detail->total+$purchase_tax_international_basic_detail->customs_fees +$purchase_tax_international_basic_detail->Other_fees) : '0.00',
                    'Imports_updatet_amount'                 => isset($ret_data_amendment['total']) ? $this->sma->formatDecimal(abs($ret_data_amendment['total'])) : '0.00',
                    'Imports_tax'                 => isset($purchase_tax_international_basic_detail->total_tax) ? $this->sma->formatDecimal($purchase_tax_international_basic_detail->total_tax+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100)+$ret_data_amendment['total_tax']) : '0.00',
                    'Free_tax_amount'                 => isset($purchase_tax_local_nt->total) ? $this->sma->formatDecimal(($purchase_tax_local_nt->total)) : '0.00',
                    'Free_tax_updatet_amount'                 => isset($qTotal_amendmentnt) ? abs($this->sma->formatDecimal($qTotal_amendmentnt)) : '0.00',
                    'Total_amount'                 => $this->sma->formatDecimal($total_purchases_amount),
                    'Total_updatet_amount'                 => $this->sma->formatDecimal($total_purchases_amount_returned),
                    'Total_tax'                 => $this->sma->formatDecimal($total_purchases_tax),
                ];

                $tax_declaration  =[
                    'Total_amount'                 => $this->sma->formatDecimal($total_sales_amount-$total_purchases_amount),
                    'Total_updatet_amount'                 => $this->sma->formatDecimal(abs($total_sales_amount_returned)-abs($total_purchases_amount_returned)),
                    'Total_tax'                 => $this->sma->formatDecimal($total_sales_tax-$total_purchases_tax),
                ];




                $data = [
                    'sales'  => $sales,
                    'purchases'  => $purchases,
                    'tax_declaration'  => $tax_declaration,
                    'status'  => true,

                ];
                $this->response($data, REST_Controller::HTTP_OK);
            }







}

