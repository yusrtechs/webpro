<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class UpdateWhiteList extends REST_Controller
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
	$whitelist =json_decode($json);

        if($whitelist->remoteJid && $whitelist->remoteJid!=null){

			    $data = [
                    'remoteJid'        => $whitelist->remoteJid,
                ];

                if($uid=$this->settings_model->updateSetting($data)){
                    $this->response([
                        'remoteJid'            => $whitelist->remoteJid,
                        'status'  => true,
                        'message' => 'Successfully updated.',
                    ], REST_Controller::HTTP_OK);


                }else{
                    $this->set_response([
                        'message' => 'The whitelist was not updated.',
                        'status'  => false,
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }

        }else{
    		$this->set_response([
                'message' => 'remoteJid is required ',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
}
