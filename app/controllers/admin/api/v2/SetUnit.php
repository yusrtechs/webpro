<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetUnit extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->admin_model('settings_model');

    }

    public function index_post()
    {

	$json = file_get_contents('php://input');
		$unit =json_decode($json);

        if($unit->name && $unit->name!=null){
            if(!$this->settings_model->getUnitByCode(trim($unit->name))){

			    $slug=trim($unit->name);
    			$slug= str_replace(" ","-",$slug);

                $data = [
                    'name'        => $unit->name,
                    'code'        => $unit->name,
                    'base_unit'       => null,
                    'operator'        => null,
                    'operation_value' => null,
                ];

                if($uid=$this->settings_model->addUnitAPI($data)){
                    $this->response([
                        'name'            => $unit->name,
                        'id' => $uid['uid'],
                        'status'  => true,
                        'message' => 'Successfully added.',
                    ], REST_Controller::HTTP_OK);


                }else{
                    $this->set_response([
                        'message' => 'The unit was not added.',
                        'status'  => false,
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }

    		}else{
        		$this->set_response([
                    'message' => 'The unit already exists.',
                    'status'  => false,
                ], REST_Controller::HTTP_BAD_REQUEST);
	    	}
        }else{
    		$this->set_response([
                'message' => 'name is required ',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
}
