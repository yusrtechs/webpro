<?php
//ini_set("display_errors", 1);
//ini_set("pcre.backtrack_limit", "100000000");
//ini_set("pcre.recursion_limit", "100000000");

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Subscriptions extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('subscriptions_api');
    }

    protected function setWarehouse($warehouse)
    {
        //unset($warehouse->price_group_id);
        $warehouse = (array) $warehouse;
        ksort($warehouse);
        return $warehouse;
    }

    public function index_get()
    {

		//if($this->db->database=="cpesoft_myfood" && !$this->get('user_id'))
		//ini_set('memory_limit', '-1');
		//$this->sma->print_arrays(memory_get_usage());
		//$this->sma->print_arrays($this->db->database);


		//'group'    => $this->get('group') ? $this->get('group') : '',


        $code = $this->get('code');



        $count_row=0;

		$filters = [
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'card_no'    => $this->get('card_no') ? $this->get('card_no') : '',
			'group'    => $this->get('group') ? $this->get('group') : '',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $count_row,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],
        ];
		$count_row=$this->subscriptions_api->countSubscriptions($filters);



        if ($code === null) {

			$data = [];



            if ($count_row>0) {

			if ($subscriptions = $this->subscriptions_api->getSubscriptions($filters_count)) {
                $subscriptions_arr = [];
                foreach ($subscriptions as $subscriptions) {




                    $subscriptions_arr[] = $this->setWarehouse($subscriptions);
                }

				$data['subscriptions']=$subscriptions_arr;


            }













                    $data['limit'] = $filters['limit'];
                    $data['start'] = $filters['start'];
                    $data['total'] = $this->subscriptions_api->countSubscriptions($filters);
                    $data['message'] = "Received ".($this->subscriptions_api->countSubscriptions($filters))." Subscriptions";


                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No Giftcard were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }
        }
    }
}
