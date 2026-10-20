<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class SetCategory extends REST_Controller
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
		$category =json_decode($json);

        if($category->name && $category->name!=null){
            if(!$this->settings_model->getCategoryByCode(trim($category->name))){

			    $slug=trim($category->name);
    			$slug= str_replace(" ","-",$slug);

                $data = [
                    'name'        => $category->name,
                    'code'        => $category->name,
                    'slug'        => $slug,
                    'description' => $category->name,
                ];

                if($cid=$this->settings_model->addCategoryAPI($data)){
                    $this->response([
                        'name'            => $category->name,
                        'id' => $cid['cid'],
                        'status'  => true,
                        'message' => 'Successfully added.',
                    ], REST_Controller::HTTP_OK);


                }else{
                    $this->set_response([
                        'message' => 'The category was not added.',
                        'status'  => false,
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }

    		}else{
        		$this->set_response([
                    'message' => 'The category already exists.',
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
