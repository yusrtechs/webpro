<?php
//ini_set("display_errors", 1);
//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SalesSet3 extends REST_Controller
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
		$sales =json_decode($json);
		//$sales=$sales->JSON3;
		//$this->sma->print_arrays($this->Settings);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;


        $sales=$sales->sales;
        //$this->sma->print_arrays($sales);
        $failed_sales = [];



		foreach ($sales as $sale) {

			 //$this->sma->print_arrays($sale);

			 $warehouse_id= $sale->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sale->warehouse_code) : $sale->warehouse_id;
			 //$sale->biller='كاشير';
			 //$biller_id= $sale->biller ? $this->users_api->getBillerIdByUserName($sale->biller) : $sale->biller_id;
			 //$biller_id= $sale->biller ? $this->users_api->getBillerIdByUserName($sale->biller) : $sale->biller_id;
			 $sale_id=$sale->sale_id ? $this->pos2_model->get_return_sale_id(trim($sale->sale_id),$warehouse_id,$biller_id)->id : null;
			 $biller= $this->companies_model->getCompanyByID($biller_id)->company ? $this->companies_model->getCompanyByID($biller_id)->company : trim($sale->biller);

			 //$customer_id= $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id;
			 $customer_id=  $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id : $sale->customer_id;

			//if($this->pos2_model->getInvoiceBy_date_reference_no_biller_id(trim($sale->date),trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id))==0){
			//if($this->pos2_model->getInvoiceBy_reference_no_biller_id(trim($sale->reference_no),$biller_id,trim($sale->Offline_Pos_id))==0){

			$sale1           = [
				'date' => trim($sale->date),
                'reference_no'                        => trim($sale->reference_no),
                'customer_id'                 => $customer_id,
                'customer'                    => trim($sale->customer),
                'biller'                        => $biller,
                'biller_id'                       =>$biller_id,
                'total'            => trim($sale->total),
                'sale_status'            => trim($sale->sale_status),
                'payment_status'            => trim($sale->payment_status),
                'warehouse_id'            => $warehouse_id,
                'grand_total'            => trim($sale->grand_total),
                'pos'            => trim($sale->pos),
                'surcharge'            => trim($sale->surcharge),
                'return_sale_total'            => trim($sale->return_sale_total),
                'paid'            => trim($sale->paid),
                'discount_allowance' =>isset($sale->discount_allowance)? trim($sale->discount_allowance):'0',
				'total_tax'            => trim($sale->total_tax),
                'note'            => trim($sale->note),
                'staff_note'            => trim($sale->staff_note),
                'product_discount'            => trim($sale->product_discount),
                'order_discount_id'            => trim($sale->order_discount_id),
                'order_discount'            => trim($sale->order_discount),
                'total_discount'            => trim($sale->total_discount),
                'product_tax'            => trim($sale->product_tax),
                'order_tax_id'            => trim($sale->order_tax_id),
                'order_tax'            => trim($sale->order_tax),
                'shipping'            => trim($sale->shipping),
                'total_items'            => trim($sale->total_items),
                'payment_term'            => trim($sale->payment_term),
                'rounding'            => trim($sale->rounding),
                'created_by'            => trim($sale->created_by),
                'Offline_Pos_id'            => trim($sale->Offline_Pos_id),
                'sale_id'            => $sale_id,
                'hash'              => $sale->hash ? trim($sale->hash) : hash('sha256', microtime() . mt_rand()),
				'supply_date'          => trim($sale->date),
				'emp_type'            => trim($sale->emp_type),
                'emp_id'            => trim($sale->emp_id),
                'dept'            => trim($sale->dept),
                'table_no'            => trim($sale->table_no),
                'app_name'              => isset($sale->app_name) ? $sale->app_name : null,
				'app_version'              => isset($sale->app_version) ? $sale->app_version : null,
                'uuid'                => $this->pos2_model->getUniqeUUID()->UUID,



            ];
			krsort($sale1);

			$products=  $sale->items;
			//$products=(array) $products;
			$product2 = [];

			$si_return2 = [];

			foreach ($products as $product) {
			$product_id=$this->pos2_model->getProductByCode($product->product_code)->id;
			$warehouse_id= $product->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($product->warehouse_code) : $product->warehouse_id;
			$sale_item_id=$sale_id ? $this->pos2_model->get_return_sale_item_id($sale_id,$product_id,$warehouse_id)->id : null;

			$product1 = [
                        'product_id'        => $product_id,
                        'product_name'      => $product->product_name,
                        'product_code'      => $product->product_code,
                        'product_type'            => trim($product->product_type),
						'option_id'            => null,
						'net_unit_price'    => $this->sma->formatDecimal($product->net_unit_price),
                        'unit_price'        => $this->sma->formatDecimal($product->unit_price),
                        'unit_quantity'     => $product->unit_quantity,
						'product_unit_id'     => $product->product_unit_id,
                        'product_unit_code'     => $product->product_unit_code,
                        'quantity'          => $product->quantity,
                        'subtotal'          => $this->sma->formatDecimal($product->subtotal),
						'item_discount'          => $this->sma->formatDecimal($product->item_discount),
                        'warehouse_id'            => $warehouse_id,
						'item_tax'   => $this->sma->formatDecimal($product->item_tax),
                        'tax_rate_id'   => trim($product->tax_rate_id),
                        'tax'   => trim($product->tax),
                        'discount'   => $this->sma->formatDecimal($product->discount),
                        'serial_no'   => trim($product->serial_no),
                        'real_unit_price'   => trim($product->real_unit_price),
						'sale_item_id'   => $sale_item_id,
                        'cloth_length'     => $product->cloth_length,
						'cloth_neck'     => $product->cloth_neck,
						'cloth_shoulder'     => $product->cloth_shoulder,
						'cloth_chest'     => $product->cloth_chest,
						'cloth_waist'     => $product->cloth_waist,
						'cloth_seat'     => $product->cloth_seat,
						'cloth_sleasves'     => $product->cloth_sleasves,
						'cloth_sleav_circum'     => $product->cloth_sleav_circum,
                        'modelTypeID'     => $product->modelTypeID,
                        'collerTypeID'     => $product->collerTypeID,
                        'cuffTypeID'     => $product->cuffTypeID,
                        'pocketTypeID'     => $product->pocketTypeID,
                        'fillingTypeID'     => $product->fillingTypeID,
                        'zipperTypeID'     => $product->zipperTypeID,
                        'notes'     => $product->notes,
                        'clothColor'     => $product->clothColor,
                        'tatrezType'     => $product->tatrezType,
                        'area'            => isset($product->area) ? $product->area :0,
						'length'            => isset($product->length) ? $product->length :0,
						'width'            => isset($product->width) ? $product->width :0,

                    ];
					//krsort($product1);
					//$this->sma->print_arrays($product);
					$product2[] = $product1;


					if($sale_id){
					$si_return = [
                        'id'           => $sale_item_id,
                        'sale_id'      => $sale_id,
                        'product_id'   => $product_id,
                        'option_id'    => null,
                        'quantity'     => (0 - $product->quantity),
                        'warehouse_id' => $warehouse_id,

                    ];

					$si_return2[] = $si_return;



					}


					}


			//$product2[] = $products;
			$payments=  $sale->payment;
			//$payments=(array) $payments;
			$payment2 = [];
            //$this->sma->print_arrays($sale);
			foreach ($payments as $payment) {

			$payment1 = [
                        'date'        => $payment->date,
                        'amount'    => $this->sma->formatDecimal($payment->amount),
                        'commercial_discount' =>isset($payment->commercial_discount)? trim($payment->commercial_discount):'0',
                        'commercial_discount_id' =>isset($payment->commercial_discount_id)? trim($payment->commercial_discount_id):NULL,
						'paid_by'      => $payment->paid_by,
                        'cheque_no'      => trim($payment->cheque_no),
                        'cc_no'            => trim($payment->cc_no),
						'cc_holder'            => trim($payment->cc_holder),
						'cc_month'     => trim($payment->cc_month),
						'cc_year'     => trim($payment->cc_year),
                        'cc_type'     => trim($payment->cc_type),
                        'created_by'          => trim($payment->created_by),
                        'type'            => trim($payment->type),
						'note'   => trim($payment->note),
                        'pos_paid'   => $this->sma->formatDecimal($payment->pos_paid),
                        'pos_balance'   => $this->sma->formatDecimal($payment->pos_balance),
						'gl_payment_method_id' =>isset($payment->gl_payment_method_id)? trim($payment->gl_payment_method_id):'0',


                    ];

					$payment2[] = $payment1;

					}
					//$payment2[] = $payments;
					//$this->sma->print_arrays($payments);

					//krsort($product1);
			//$this->sma->print_arrays($payment2);
			//$this->sma->print_arrays($sale1, $products, null, null);
			//$this->sma->print_arrays($sale1,$product2, $payment2, null,$si_return2);



			/*$Measurements=  $sale->Measurement;
			//$payments=(array) $payments;
			$Measurement2 = [];

			foreach ($Measurements as $Measurement) {

			$Measurement1 = [
                        'sale_id'        => $Measurement->sale_id,
                        'Item_Code'        => $Measurement->Item_Code,
                        'Item_Name'        => $Measurement->Item_Name,
                        'Front_Length'        => $Measurement->Front_Length,
                        'Back_Length'        => $Measurement->Back_Length,
                        'Shoulder_Width'        => $Measurement->Shoulder_Width,
                        'Shoulder_Slope'        => $Measurement->Shoulder_Slope,
                        'Sleeve'        => $Measurement->Sleeve,
                        'Sleeve_Top'        => $Measurement->Sleeve_Top,
                        'Wrist'        => $Measurement->Wrist,
                        'Plain_Cuff_Length'        => $Measurement->Plain_Cuff_Length,
                        'Cuff_Length'        => $Measurement->Cuff_Length,
                        'Cuff_Width'        => $Measurement->Cuff_Width,
                        'Middle_Width'        => $Measurement->Middle_Width,
                        'Chest_Front'        => $Measurement->Chest_Front,
                        'Chest_Back'        => $Measurement->Chest_Back,
                        'Bottom_Height'        => $Measurement->Bottom_Height,
                        'Bottom_Width'        => $Measurement->Bottom_Width,
                        'Collar_Width'        => $Measurement->Collar_Width,
                        'Collar_Height'        => $Measurement->Collar_Height,
                        'Zipper_Height'        => $Measurement->Zipper_Height,
                        'Zipper_Width'        => $Measurement->Zipper_Width,
                        'Chest_Pocket_Height'        => $Measurement->Chest_Pocket_Height,
                        'Chest_Pocket_Width'        => $Measurement->Chest_Pocket_Width,
                        'Mobile_Pocket_Height'        => $Measurement->Mobile_Pocket_Height,
                        'Wallet_Pocket_Height'        => $Measurement->Wallet_Pocket_Height,
                        'Wallet_Pocket_Width'        => $Measurement->Wallet_Pocket_Width,
                        'Haunch_Width'        => $Measurement->Haunch_Width,
                        'Button_No'        => $Measurement->Button_No,
                        'Embroidery_No'        => $Measurement->Embroidery_No,
                        'Estimated_Length'        => $Measurement->Estimated_Length,
                        'Tailor_id'        => $Measurement->Tailor_id,
                        'sample'        => $Measurement->sample,
                        'urgent'        => $Measurement->urgent,
                        'shoulder_chest_length'        => $Measurement->shoulder_chest_length,
                        'sleeve_middle'        => $Measurement->sleeve_middle,
                        'Side_Pocket_Length'        => $Measurement->Side_Pocket_Length,
                        'takhalees'        => $Measurement->takhalees,
                        'Collar_Type_ID'        => $Measurement->Collar_Type_ID,
                        'Cuff_Type_ID'        => $Measurement->Cuff_Type_ID,
                        'Model_Type_ID'        => $Measurement->Model_Type_ID,
                        'Pocket_Type_ID'        => $Measurement->Pocket_Type_ID,
                        'Filling_Type_ID'        => $Measurement->Filling_Type_ID,
                        'Note'        => $Measurement->Note,


                    ];
					krsort($Measurement1);


					$Measurement2[] = $Measurement1;

					}*/

			//$this->sma->print_arrays($sale1,$product2, $payment2, null,$si_return2);

		if($sale_id){

		if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null,$si_return2)){
				//$this->sma->print_arrays($sale1['Offline_Pos_id']);

			$response["success"] = 1;
			$salse_temp = [
			'id'            => trim($sale1['Offline_Pos_id']),
			'hash'            => trim($sale1['hash']),
			'online_id'            => trim($sale2['sale_id'])

			];

			$salse_temp2[]=$salse_temp;



}else{
			$response["success"] = 0;
			//array_push($failed_sales['sales'],$sale1['Offline_Pos_id']);

		}


			}else{

			    //$this->sma->print_arrays($sale1,$product2, $payment2, null);

					if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null)){
						//$Measurement1['sale_id']=$sale2['sale_id'];
						//$this->pos2_model->addSaleTailor($Measurement1);
				//$this->sma->print_arrays($sale2['sale_id']);


			$response["success"] = 1;
			//array_push($failed_sales['sales'],'1');
			$salse_temp = [
			'id'            => trim($sale1['Offline_Pos_id']),
			'hash'            => trim($sale1['hash']),
			'online_id'            => trim($sale2['sale_id'])
			];

			$salse_temp2[]=$salse_temp;


}else{
			$response["success"] = 0;
			//array_push($failed_sales['sales'],$sale1['Offline_Pos_id']);


		}




			}
		/*}else{
		$response["success"] = 1;
			$salse_temp = [
			'id'            => trim($sale->Offline_Pos_id),
			'hash'            => trim($sale->hash),
			'online_id'            => ''
			];

			$salse_temp2[]=$salse_temp;

		}*/
		}

		//$this->sma->print_arrays($payments_only);








		 $failed_sales['s_sales']= $this->setArr($salse_temp2);


	//$this->set_response(['success'  =>$payment2], REST_Controller::HTTP_OK);
	$this->set_response($failed_sales, REST_Controller::HTTP_OK);

		/*
		$reference = $this->get('reference');

        $filters = [
            'reference'   => $reference,
            'include'     => $this->get('include') ? explode(',', $this->get('include')) : null,
            'start'       => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'       => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 10,
            'start_date'  => $this->get('start_date') && is_numeric($this->get('start_date')) ? $this->get('start_date') : null,
            'end_date'    => $this->get('end_date') && is_numeric($this->get('end_date')) ? $this->get('end_date') : null,
            'order_by'    => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'decs'],
            'customer_id' => $this->get('customer_id') ? $this->get('customer_id') : null,
            'customer'    => $this->get('customer') ? $this->get('customer') : null,
        ];

        if ($reference === null) {
            if ($sales = $this->sales_api->getSales($filters)) {
                $sl_data = [];
                foreach ($sales as $sale) {
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

                    //$sale->created_by = $this->sales_api->getUser($sale->created_by);
                    $sl_data[]        = $this->setSale($sale);
                }

                $data = [
                    'data'  => $sl_data,
                    'limit' => (int) $filters['limit'],
                    'start' => (int) $filters['start'],
                    'total' => $this->sales_api->countSales($filters),
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No sale record found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
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
            }
        }
		*/
    }
}
