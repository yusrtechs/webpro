<?php

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller_SET.php';
use Restserver\Libraries\REST_Controller_SET;

class GetInfo extends REST_Controller_SET
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        //$this->load->api_model('Users_api');
		//$this->load->api_model('warehouses_api');
		//$this->load->api_model('users_api');
        
		$this->load->admin_model('site');
		//$this->load->admin_model('auth_model');
		$this->load->admin_model('settings_model');
		
		
		
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
		//$this->sma->print_arrays($sales);
		$company=$sales->company;
		//$company="test";
        //$username=$sales->username;
        //$password=$sales->password;
		//if($this->auth_model->IsUserFound($username,$password)===true)
        //$this->sma->print_arrays($this->auth_model->IsUserFound($username,$password));
		
		//$sales=$sales->JSON3;
		$failed_sales = [];		
		//$this->sma->print_arrays($this->db);
		 
		//$apis= $this->setArr($this->site->getApiKeysByCompany($company));
			
		//$this->sma->print_arrays($apis);
		
				if($this->db = $this->site->posdynamicdb_IPAD($company)){//&& $this->auth_model->IsUserFound($username,$password)){
					//$this->sma->print_arrays($this->products_model->getAllWarehouses());
		 
					$customer_temp = [
			'success'            => 'True'
			];
			$customer_temp2[]=$customer_temp;
			
			$failed_sales['result']= $this->setArr($customer_temp2);
			//if($this->db = $this->site->posdynamicdb_IPAD($company)){
				
			$failed_sales['info']= $this->setArr($this->settings_model->getSettingsInfo());
			//$failed_sales['APIs']= $apis;
			//}
				
				}else{
					$customer_temp = [
			'success'            => 'False'
			];
			
			$customer_temp2[]=$customer_temp;
			$failed_sales['result']= $this->setArr($customer_temp2);
		 
				}
				
				
				
	//$this->set_response(['success'  =>$payment2], REST_Controller::HTTP_OK); 
	$this->set_response($failed_sales, REST_Controller_SET::HTTP_OK); 
		
		
    }
}
