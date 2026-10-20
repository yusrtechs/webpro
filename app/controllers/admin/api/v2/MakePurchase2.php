<?php
//ini_set("display_errors", 1);
//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class MakePurchase2 extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('sales_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('users_api');

		$this->load->admin_model('pos2_model');
		$this->load->admin_model('products_model');
		$this->load->admin_model('purchases_model');



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

		$json = file_get_contents('php://input');
		$purchases =json_decode($json);


		$response["success"] = 0;





        $purchase=$purchases->meta;
        $products=$purchases->data;
        if ($purchase) {
        //$this->sma->print_arrays($purchases);
        $failed_purchases = [];

		$flageTrue=0;
		$flageFalse=0;





        $warehouse_id= $purchase->warehouse_code ? $this->warehouses_api->getWarehouseIdByCode($purchase->warehouse_code) : $purchase->warehouse_id;
        if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
            	{
					$brId=$this->pos2_model->getWarehouse_VATByID($warehouse_id)->brId;
                    $reference_no=$this->pos2_model->getReference('po',$brId);
				}else{
					$brId=1;
                    $reference_no=$this->pos2_model->getReference('po');
				}











            $product2 = [];
            $total_subtotal=0;
            $total=0;
            $product_tax      = 0;
            $product_discount = 0;

			foreach ($products as $product) {
                $product_details=$this->pos2_model->getProductByCode($product->product_code);
                $item_net_cost    = $this->sma->formatDecimal($product->net_unit_cost);
                $item_quantity    = $product->quantity;
                $unit     = $this->pos2_model->getUnitByID($product_details->unit);
                $item_tax_rate    = $product->item_tax_rate;
                $item_discount    = $product->discount;

                $pr_discount      = $this->pos2_model->calculateDiscount($item_discount, $item_net_cost, 12);
                            $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_quantity), 12);
                            $product_discount += $pr_item_discount;

                            $tax         = '';
                            $pr_item_tax = 0;
                            $unit_cost   = $item_net_cost - $pr_discount;
                            $gst_data    = [];
                            $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->purchases_model->getTaxRateByName($item_tax_rate) : $this->pos2_model->getTaxRateByID($product_details->tax_rate));
                            if ($tax_details) {
                                $ctax     = $this->pos2_model->calculateTax($product_details, $tax_details, $unit_cost);
                                $item_tax = $ctax['amount'];
                                $tax      = $ctax['tax'];
                                if ($product_details->tax_method != 1) {
                                    $item_net_cost = $unit_cost - $item_tax;
                                }
                                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity, 12);

                            }

                            $product_tax += $pr_item_tax;
                            $subtotal = $this->sma->formatDecimal((($item_net_cost * $item_quantity) + $pr_item_tax), 12);

                $product1 = [
                    'product_id'        => $product_details->id,
                    'product_code'      => $product->product_code,
                    'product_name'      => $product_details->name,
                    'option_id'         => null,
                    'net_unit_cost'     => $item_net_cost,
                    'quantity'          => $item_quantity,
                    'product_unit_id'   => $product_details->unit,
                    'product_unit_code' => $unit->code,
                    'unit_quantity'     => $item_quantity,
                    'quantity_balance'  => $item_quantity,
                    'warehouse_id'      => $warehouse_id,
                    'item_tax'          => $pr_item_tax,
                    'tax_rate_id'       => $tax_details ? $tax_details->id : null,
                    'tax'               => $tax,
                    'discount'          => $item_discount,
                    'item_discount'     => $pr_item_discount,
                    'expiry'            => null,
                    'subtotal'          => $subtotal,
                    'date'              => date('Y-m-d', strtotime(trim($purchase->date))),
                    'status'            => 'ordered',
                    'unit_cost'         => $this->sma->formatDecimal(($item_net_cost + $item_tax), 12),
                    'real_unit_cost'    => $this->sma->formatDecimal(($item_net_cost + $item_tax + $pr_discount), 12),

                ];

                if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
                {
                    $product1  ['grpId']= 1;
                    $product1  ['coId']= 1;
                    $product1  ['brId']=$brId ? $brId : 1;
                }
                //krsort($product1);
                //$this->sma->print_arrays($product);
                $product2[] = $product1;
                $total += $this->sma->formatDecimal(($item_net_cost * $item_quantity), 12);
                $total_subtotal=$total_subtotal+$subtotal;


					}

                    $order_discount = $this->pos2_model->calculateDiscount(trim($purchase->total_discount), 12);
                    $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 12);
                    $order_tax      =0;
                    $total_tax      = $this->sma->formatDecimal(($product_tax + $order_tax), 12);
                    $grand_total    = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal(trim($purchase->shipping)) - $order_discount), 12);

                    $data           = ['reference_no' => $reference_no,
                    'date'                        => trim($purchase->date),
                    'supplier_id'                 => $purchase->supplier_id,
                    'supplier'                    => $purchase->supplier,
                    'warehouse_id'                => $warehouse_id,
                    'note'                        => trim($purchase->note),
                    'total'                       => $total,
                    'product_discount'            => $product_discount,
                    'order_discount_id'           => 0,
                    'order_discount'              => $order_discount,
                    'total_discount'              => $total_discount,
                    'product_tax'                 => $product_tax,
                    'order_tax_id'                => 0,
                    'order_tax'                   => $order_tax,
                    'total_tax'                   => $total_tax,
                    'shipping'                    => $this->sma->formatDecimal(trim($purchase->shipping), 12),
                    'grand_total'                 => $grand_total,
                    'status'                      => 'ordered',
                    'created_by'                  => trim($purchase->created_by),
                    'offline_id'            => trim($purchase->id),
                ];

                if(isset($this->pos2_model->getSystemSetting()->isGrouping) && $this->pos2_model->getSystemSetting()->isGrouping==1)
                {
                    $data  ['grpId']= 1;
                    $data  ['coId']= 1;
                    $data  ['brId']=$brId ? $brId : 1;
                    //$purchase1  ['purchase_no']=$this->pos2_model->getMainBranchesByID($purchase1['brId'])->branch_prefix ? $this->pos2_model->getMainBranchesByID($purchase1['brId'])->branch_prefix.'-'.$this->pos2_model->getCountpurchasesBybrid($purchase1['brId'])->count : $this->pos2_model->getCountpurchasesBybrid($purchase1['brId'])->count;
                }


                    //$this->sma->print_arrays($purchase1,$product2);
                   // $this->sma->print_arrays($purchase1,$product2);
			//$product2[] = $products;


            $this->sma->print_arrays($data, $product2);
            $flag=0;

                if($this->purchases_model->addPurchase($data, $product2)){
                    $flag=1;
                }
                //$this->sma->print_arrays($pr);


					if($flag==1){
				//$this->sma->print_arrays($purchase2['purchase_id']);

                //$this->sma->print_arrays($purchase2,$purchase1,$product2, $payment2);
			$response["success"] = 1;
			//array_push($failed_purchases['purchases'],'1');
			$salse_temp = [
                'offline_id'            => trim($data['offline_id']),
                'status'            => true
			];
            $flageTrue=1;
			$salse_temp2[]=$salse_temp;


            }else{
			$response["success"] = 0;
            $salse_temp = [
                'offline_id'            => trim($data['offline_id']),
			    'status'            => false

                ];
                $flageFalse=1;
                $salse_temp2[]=$salse_temp;
		}








		 $message="";
         if($flageTrue==1 && $flageFalse==0){
            $this->response([
                 'data' => $salse_temp2,
                 'Cleaned' => 'Cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }else if($flageTrue==1 && $flageFalse==1){
             $this->response([
                 'data' => $salse_temp2,
                 'Cleaned' => 'Partial cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }else{
             $this->response([
                 'data' => $salse_temp2,
                 'Cleaned' => 'No cleaned',
                 'status'  => true,
             ], REST_Controller::HTTP_OK);

         }
        }else{

            $this->response([
                'message' => 'This request does not contain data',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
