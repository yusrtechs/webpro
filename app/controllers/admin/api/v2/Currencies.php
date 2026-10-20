<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Currencies extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Currencies_api');
    }

    protected function setCurrencies($taxrate)
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
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $this->Currencies_api->countCurrencies(),
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];

        if ($code === null) {
            if ($currencies = $this->Currencies_api->getCurrencies($filters)) {
                $pr_data = [];
                foreach ($currencies as $currency) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $currency->price_group = $this->Currencies_api->getPriceGroupByID($currency->price_group_id);
                            }
                        }
                    }
                    $pr_data[] = $this->setCurrencies($currency);
                }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $filters['limit'],
                    'start' => $filters['start'],
                    'total' => $this->Currencies_api->countCurrencies($filters),
                    'message' => "Received ".($this->Currencies_api->countCurrencies($filters))." Currencies",
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No Currency were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_OK);
            }
        }
    }
}
