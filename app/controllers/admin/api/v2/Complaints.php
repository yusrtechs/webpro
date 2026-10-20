<?php
//ini_set("display_errors", 1);
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Complaints extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('warehouses_api');
        $this->load->admin_model('pos2_model');

    }

    protected function setWarehouse($warehouse)
    {
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }

    public function index_get()
    {




        $code = $this->get('code');

        $filters = [
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : 'customer',
            'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'id'    => $this->get('id') ? $this->get('id') : '',
			'user_id'    => $this->get('user_id') ? $this->get('user_id') : '',
			'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($filters['warehouse_code']) {
            //if ($filters['id']) {

                if ($filters['user_id']) {

                    $company= str_replace("cpesoft_","",$this->db->database);

                    $otherdb = $this->load->database('info', true);
                    $otherdb->select('offline_id as id,client_name as company,date,title,details,type,status,respond,respond_by,respond_date');
                    $otherdb->where('warehouse_code',$filters['warehouse_code']);
                    if ($filters['id']) {
                    $otherdb->where('offline_id',$filters['id']);
                    }
                    $otherdb->where('user_id',$filters['user_id']);
                    $otherdb->where('company',$company);
                    $q = $otherdb->get('complaints');

                    if ($q->num_rows() > 0) {

                        $complaint_arr = [];
                foreach (($q->result()) as $row) {
                    $complaint_arr[] = $row;
                }
                /*foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->warehouses_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->Max_Offline_Pos_id=$this->pos2_model->getMaxOffline_Pos_idBywarehouse_id($warehouse->id);
                   // $warehouse->logo_image_url=$this->pos2_model->getlogo_image_url();

                    $warehouses_arr[] = $this->setWarehouse($warehouse);
                }*/


                $data = [
                    'data'  => $complaint_arr,
                    'message' => "Success",
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No complaint were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }

        } else {

            $this->set_response([
                'message' => 'user_id could not be found ',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);

        }

       /* } else {

            $this->set_response([
                'message' => 'id could not be found ',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);

        }*/
        } else {

                $this->set_response([
                    'message' => 'warehouse_code could not be found ',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);

        }
    }
}
