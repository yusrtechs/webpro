<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class WhiteList extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Settings_api');

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
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 1,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'acs'],
        ];
        $data = [];
        if ($code === null) {
            if ($wlists = $this->Settings_api->getWhiteList($filters)) {
                $whiteList = [];
                foreach ($wlists as $wlist) {

					$whiteList[] = $this->setObject($wlist);
                }
				$data['whitelist']=$whiteList;

			}

            $data['message']="Success";

                $this->response($data, REST_Controller::HTTP_OK);
        }
    }
}
