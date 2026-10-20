<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Taxrates extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Taxrates_api');
    }

    protected function setTaxrates($taxrate)
    {
         $taxrate = (array) $taxrate;
        ksort($taxrate);
        return $taxrate;
    }

    public function index_get()
    {




        $code = $this->get('code');

        $filters = [
            'code'     => $code,
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : 'customer',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $this->Taxrates_api->countTaxrates(),
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($code === null) {
            if ($taxrates = $this->Taxrates_api->getTaxrates($filters)) {
                $pr_data = [];
                foreach ($taxrates as $taxrate) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $taxrate->price_group = $this->Taxrates_api->getPriceGroupByID($taxrate->price_group_id);
                            }
                        }
                    }
                    $pr_data[] = $this->setTaxrates($taxrate);
                }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $filters['limit'],
                    'start' => $filters['start'],
                    'total' => $this->Taxrates_api->countTaxrates($filters),
                    'message' => "Received ".($this->Taxrates_api->countTaxrates($filters))." Taxrates",
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No taxrate were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }
        }
    }
}
