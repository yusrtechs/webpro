<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Tailordata extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Tailer_api');
    }

    protected function setObject($obj)
    {
         $obj = (array) $obj;
        ksort($obj);
        return $obj;
    }
    public function index_get()
    {




        $code = $this->get('code');

        $filters = [
            'code'     => $code,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : 'customer',
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($code === null) {
            if ($warehouses = $this->Tailer_api->getTR_tailor($filters)) {
                $TR_tailor = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_tailor[] = $this->setObject($warehouse);
                }
			}
				$data['TR_tailor']=$TR_tailor;

			if ($warehouses = $this->Tailer_api->getTR_Filling($filters)) {
                $TR_Filling = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Filling[] = $this->setObject($warehouse);
                }
			}
				$data['TR_Filling']=$TR_Filling;

			if ($warehouses = $this->Tailer_api->getTR_Zipper($filters)) {
                $TR_Zipper = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Zipper[] = $this->setObject($warehouse);
                }
			}
				$data['TR_Zipper']=$TR_Zipper;

			if ($warehouses = $this->Tailer_api->getTR_Collar($filters)) {
                $TR_Collar = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Collar[] = $this->setObject($warehouse);
                }
			}
				$data['TR_Collar']=$TR_Collar;

			if ($warehouses = $this->Tailer_api->getTR_Cuff($filters)) {
                $TR_Cuff = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Cuff[] = $this->setObject($warehouse);
                }
			}
				$data['TR_Cuff']=$TR_Cuff;

			if ($warehouses = $this->Tailer_api->getTR_Model($filters)) {
                $TR_Model = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Model[] = $this->setObject($warehouse);
                }
			}
				$data['TR_Model']=$TR_Model;

			if ($warehouses = $this->Tailer_api->getTR_Pocket($filters)) {
                $TR_Pocket = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Pocket[] = $this->setObject($warehouse);
                }
			}
				$data['TR_Pocket']=$TR_Pocket;

                $data['message']="Success";

                $this->response($data, REST_Controller::HTTP_OK);


        }
    }
}
