<?php
//ini_set("display_errors", 1);
//ini_set("pcre.backtrack_limit", "100000000");
//ini_set("pcre.recursion_limit", "100000000");

//ini_set('memory_limit', '-1');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Getgiftcards extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Giftcards_api');




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
		$count_row=$this->Giftcards_api->countGiftcards($filters);



        if ($code === null) {

			$data = [];



            if ($count_row>0) {

			if ($warehouses = $this->Giftcards_api->getGiftcards($filters)) {
                $giftcards = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Taxrates_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $giftcards[] = $this->setWarehouse($warehouse);
                }


				$data['giftcards']=$giftcards;
			}




			/*
			if ($Order_type = $this->Order_type_api->getOrder_type($filters)) {
                $Order_type_data = [];
                foreach ($Order_type as $product) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'brand') {
                                $product->brand = $this->products_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->products_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->products_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->products_api->getSubUnits($product->unit);
                            }
                        }

                    }
					//$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));


                    $Order_type_data[] = $this->setWarehouse($product);
                }
				$data['order_type']=$Order_type_data;
			}




			*/








                    $data['limit'] = $filters['limit'];
                    $data['start'] = $filters['start'];
                    $data['total'] = $this->Giftcards_api->countGiftcards($filters);
                    $data['message'] = "Received ".($this->Giftcards_api->countGiftcards($filters))." Giftcards";


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
