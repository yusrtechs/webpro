<?php
//ini_set("display_errors", 1);
//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class MakeInvoice extends REST_Controller
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
		//$this->sma->print_arrays($sales->meta);

		//$sales=$sales->sales;
		//$sales1 =$sales;
		//$this->sma->print_arrays($warehouse1,$warehouse->data);

		$response["success"] = 0;

        //
		//$sales=krsort($sales);
        //$sales=(array) $sales;


        $sale=$sales->meta;
        $products=$sales->data;
        $payments=$sales->payments;
        if ($sale) {
        //$this->sma->print_arrays($sales);
        $failed_sales = [];

		$flageTrue=0;
		$flageFalse=0;


		//foreach ($sales as $sale){



			 $warehouse_id= $sale->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($sale->warehouse_code) : $sale->warehouse_id;
			 $sale_id=$sale->sale_id ?  $sale->sale_id : null;

             //$sale->biller='كاشير';
			 //$biller_id= $sale->biller ? $this->users_api->getBillerIdByUserName($sale->biller) : $sale->biller_id;
			 //$sale_id=$sale->sale_id ? $this->pos2_model->get_return_sale_id(trim($sale->sale_id),$warehouse_id,$biller_id)->id : null;
			 //$biller= $this->companies_model->getCompanyByID($biller_id)->company ? $this->companies_model->getCompanyByID($biller_id)->company : trim($sale->biller);

			 //$customer_id= $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id;
			 //$customer_id=  $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id ? $this->pos2_model->getCompanyByOfflineID($sale->customer_id)->id : $sale->customer_id;
             $payment_status="pending";
            if($sale->paid==$sale->public_total)
                $payment_status="paid";
            else if($sale->paid==0)
                $payment_status="pending";
            else
                $payment_status="partial";



             $sale1           = [
				'date' => trim($sale->invoice_date),
                'reference_no'                        => $this->pos2_model->getReference('pos'),
                'customer_id'                 => trim($sale->client_id),
                'customer'                    => trim($sale->client_name),
                'biller'                        => trim($sale->employee_name),
                'biller_id'                       =>trim($sale->employee_id),
                'total'            => trim($sale->total),
                'sale_status'            => trim($sale->invoice_status),
                'payment_status'            => $payment_status,
                'warehouse_id'            => $warehouse_id,
                'grand_total'            => trim($sale->public_total),
                'pos'            => 1,
                'surcharge'            => 0,
                'return_sale_total'            => 0,
                'paid'            => trim($sale->paid),
                'discount_allowance' =>isset($sale->discount_allowance)? trim($sale->discount_allowance):'0',
				'total_tax'            => trim($sale->public_tax),
                'note'            => trim($sale->note),
                'staff_note'            => null,
                'product_discount'            => 0,
                'order_discount_id'            => 0,
                'order_discount'            => trim($sale->public_discount),
                'total_discount'            => trim($sale->public_discount),
                'product_tax'            => trim($sale->public_tax),
                'order_tax_id'            => 1,
                'order_tax'            => 0,
                'shipping'            => 0,
                'total_items'            => $sale->total_items,
                'payment_term'            => null,
                'rounding'            => null,
                'created_by'            => trim($sale->created_by),
                'Offline_Pos_id'            => null,
                'sale_id'            => $sale_id,
                'hash'              =>  hash('sha256', microtime() . mt_rand()),
				'supply_date'          => trim($sale->invoice_date),
                'emp_type'            => 0,
                'emp_id'            => 0,
                'dept'            => 0,
                'table_no'            => 0,
                'uuid'                => $this->pos2_model->getUniqeUUID()->UUID,






            ];
			//krsort($sale1);

			//$products=  $sale->items;
			//$products=(array) $products;
			$product2 = [];

			$si_return2 = [];

			foreach ($products as $product) {
            $pr=$this->pos2_model->getProductByCode($product->product_code);
            //
			$product_id=$pr->id;
			$product_type=$pr->type;
			$real_unit_price=$pr->price;

			//$warehouse_id= $product->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($product->warehouse_code) : $product->warehouse_id;
			$sale_item_id=$sale_id ? $this->pos2_model->get_return_sale_item_id($sale_id,$product_id,$warehouse_id)->id : null;
            $units                   = $this->pos2_model->getUnitByID($product->unit_id);
            $taxes                   = $this->pos2_model->getTaxRateByID($product->tax_rate_id);
            //$this->sma->print_arrays($taxes);
			$product1 = [
                        'client_type'        => $product->client_type,
                        'product_id'        => $product_id,
                        'product_name'      => $product->product_name,
                        'product_code'      => $product->product_code,
                        'product_type'            => $product_type,
						'option_id'            => null,
						'net_unit_price'    => $this->sma->formatDecimal($product->item_price),
                        'unit_price'        => $real_unit_price,
                        'unit_quantity'     => $product->item_quantity,
						'product_unit_id'     => $product->unit_id,
                        'product_unit_code'     => $units->code,
                        'quantity'          => $product->item_quantity,
                        'subtotal'          => $this->sma->formatDecimal($product->item_total),
						'item_discount'          => $this->sma->formatDecimal($product->item_discount),
                        'warehouse_id'            => $warehouse_id,
						'item_tax'   => $this->sma->formatDecimal($product->item_tax),
                        'tax_rate_id'   => trim($product->tax_rate_id),
                        'tax'   => ((int)($taxes->rate))."%",
                        'discount'   => $this->sma->formatDecimal($product->discount),
                        'serial_no'   => null,
                        'real_unit_price'   => $real_unit_price,
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
                        'cloth_thigh'     => $product->cloth_thigh,
                        'cloth_leg_length'     => $product->cloth_leg_length,
                        'cloth_shoulder_width'     => $product->cloth_shoulder_width,
                        'cloth_top_length'     => $product->cloth_top_length,
                        'cloth_hand_wrist'     => $product->cloth_hand_wrist,
                        'cloth_leg_width'     => $product->cloth_leg_width,


                    ];
					//krsort($product1);

					$product2[] = $product1;


					if($sale_id){
					$si_return = [
                        'id'           => $sale_item_id,
                        'sale_id'      => $sale_id,
                        'product_id'   => $product_id,
                        'option_id'    => null,
                        'quantity'     => (0 - $product->item_quantity),
                        'warehouse_id' => $warehouse_id,

                    ];

					$si_return2[] = $si_return;



					}


					}

                   // $this->sma->print_arrays($sale1,$product2);
			//$product2[] = $products;
			//$payments=  $sale->payment;
			//$payments=(array) $payments;
			$payment2 = [];

			foreach ($payments as $payment) {

			$payment1 = [
                        'date'        => trim($sale->invoice_date),
                        'amount'    => $this->sma->formatDecimal($payment->paid_amount),
                        'commercial_discount' => '0',
                        'commercial_discount_id' =>null,
						'paid_by'      => $payment->payment_type,
                        'cheque_no'      => trim($payment->cheque_no),
                        'cc_type'     => $payment->cc_type,
                        'cc_holder'            => null,
						'cc_month'     => null,
						'cc_year'     => null,
                        'created_by'          => trim($sale->created_by),
                        'type'            => "received",
						'note'   => null,
                        'pos_paid'   => $this->sma->formatDecimal($payment->paid_amount),
                        'pos_balance'   => 0,
						'gl_payment_method_id' =>'0',


                    ];

					$payment2[] = $payment1;

					}
					//$payment2[] = $payments;
					//$this->sma->print_arrays($payments);

					//krsort($product1);
			//$this->sma->print_arrays($payment2);
			//$this->sma->print_arrays($sale1, $products, null, null);



/*
			$Measurements=  $sale->Measurement;
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

                    if($sale_id){
                        if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null,$si_return2)){
                            //$this->sma->print_arrays($sale2['sale_id']);

                            //$this->sma->print_arrays($sale2,$sale1,$product2, $payment2);
                        $response["success"] = 1;
                        //array_push($failed_sales['sales'],'1');
                        $salse_temp = [
                            'id'            => trim($sale2['sale_id']),
                            'reference_no'            => trim($sale1['reference_no']),
                            'status'            => true
                        ];
                        $flageTrue=1;
                        $salse_temp2[]=$salse_temp;


            }else{
                        $response["success"] = 0;
                        $salse_temp = [
                            'reference_no'            => trim($sale1['reference_no']),
                            'status'            => false

                            ];
                            $flageFalse=1;
                            $salse_temp2[]=$salse_temp;
                    }

                    }else{
            //$this->sma->print_arrays($sale2,$sale1,$product2, $payment2);

					if($sale2 = $this->pos2_model->addSale($sale1,$product2, $payment2, null)){
				//$this->sma->print_arrays($sale2['sale_id']);

                //$this->sma->print_arrays($sale2,$sale1,$product2, $payment2);
			$response["success"] = 1;
			//array_push($failed_sales['sales'],'1');
			$salse_temp = [
                'id'            => trim($sale2['sale_id']),
                'reference_no'            => trim($sale1['reference_no']),
                'status'            => true
			];
            $flageTrue=1;
			$salse_temp2[]=$salse_temp;


}else{
			$response["success"] = 0;
            $salse_temp = [
                'reference_no'            => trim($sale1['reference_no']),
			    'status'            => false

                ];
                $flageFalse=1;
                $salse_temp2[]=$salse_temp;
		}




			}



		 $message="";
         if($flageTrue==1 && $flageFalse==0){
            $this->response([
                 'sales' => $salse_temp2,
                 'Cleaned' => 'Cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }else if($flageTrue==1 && $flageFalse==1){
             $this->response([
                 'sales' => $salse_temp2,
                 'Cleaned' => 'Partial cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }else{
             $this->response([
                 'sales' => $salse_temp2,
                 'Cleaned' => 'No cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }
        }else{

            $this->response([
                'message' => 'This request does not contain sales',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
