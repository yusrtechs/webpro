<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class WarehousesSet extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('warehouses_api');
		$this->load->admin_model('settings_model');
		
    }

    protected function setWarehouse($warehouse)
    {
        unset($warehouse->price_group_id);
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }

    public function index_get()
    {
		
		
		//var_dump($_GET);die();
		//var_dump($_POST);die();
		
		
		//$json = file_get_contents('php://input');
		//$json = (array) {"data":[{"address":"<p>\u0634\u064a\u0634\u064a<\/p>","code":"6","email":"","id":"3","map_url":null,"name":"\u062a\u062c\u0631\u064a\u0628\u064a","phone":""},{"address":"<p>test<\/p>","code":"test","email":"","id":"2","map_url":null,"name":"test","phone":"000"},{"address":"<p>\u062d\u064a \u0627\u0644\u062c\u0633\u0631<\/p>","code":"\u0645\u0624\u0633\u0633\u0629 \u062f\u0627\u0631 \u0627\u0644\u0637\u0628\u064a\u0639\u0629","email":"tradeposerpgmail.com","id":"1","map_url":null,"name":"\u0645\u0624\u0633\u0633\u0629 \u062f\u0627\u0631 \u0627\u0644\u0637\u0628\u064a\u0639\u0629","phone":"00"}],"limit":10,"start":1,"total":3};
        //$new_data = json_decode($json);
		
		//$this->sma->print_arrays($json);
		$warehouse = urldecode($this->get('warehouse'));
		//$this->sma->print_arrays($m);
		//$warehouse = $this->setWarehouse(urldecode($this->get('warehouse')));
		//$warehouse = $this->setWarehouse(urldecode($warehouse));
		$warehouse =json_decode($warehouse);
		$warehouse=$warehouse->data;
		//$warehouse = $this->settings_model->getAllWarehouses_temp();
		//$this->sma->print_arrays($warehouse1,$warehouse->data);
		
		$response["success"] = 0;
		//$warehouse     = json_decode($warehouse);
        //$warehouse     = json_encode('[]');
		
		
		//$data = json_decode($warehouse[0]);
        //$new_data                 = (array) $data->data;
        //$new_data                 = ($data->data);
		
        //$this->sma->print_arrays($new_data);
		
		                    
		foreach ($warehouse as $warehouss) {
			$data           = [
				'code' => trim($warehouss->code),
                'name'                        => trim($warehouss->name),
                'address'                 => trim($warehouss->address),
                'map'                    => trim($warehouss->map),
                'phone'                        => trim($warehouss->phone),
                'email'                       =>trim($warehouss->email),
                'price_group_id'            => trim($warehouss->price_group_id),
                
            ];
			
			$data     = (array) $data;
			//$this->sma->print_arrays($data['code']);
			
		if($this->settings_model->addWarehouse_temp($data)){
				//$this->sma->print_arrays($warehouss);
		
			$response["success"] = 1;
}else{
			$response["success"] = 0;

		}			
		}
	$this->set_response(['success'  =>$response["success"]], REST_Controller::HTTP_OK); 
		
		/*
        $code = $this->get('code');

        $filters = [
            'code'     => $code,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : 'customer',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 10,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($code === null) {
            if ($warehouses = $this->warehouses_api->getWarehouses($filters)) {
                $pr_data = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->warehouses_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $pr_data[] = $this->setWarehouse($warehouse);
                }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $filters['limit'],
                    'start' => $filters['start'],
                    'total' => $this->warehouses_api->countWarehouses($filters),
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No warehouse were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            if ($warehouse = $this->warehouses_api->getWarehouse($filters)) {
                if (!empty($filters['include'])) {
                    foreach ($filters['include'] as $include) {
                        if ($include == 'price_group') {
                            $warehouse->price_group = $this->warehouses_api->getPriceGroupByID($warehouse->price_group_id);
                        }
                    }
                }
                $warehouse = $this->setWarehouse($warehouse);
                $this->set_response($warehouse, REST_Controller::HTTP_OK);
            } else {
                $this->set_response([
                    'message' => 'Warehouse could not be found for name ' . $name . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
		*/
    }
}
