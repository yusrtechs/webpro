<?php
//ini_set("display_errors", 1);
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Settings extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Users_api');
        $this->load->api_model('Settings_api');
        $this->load->api_model('Psettings_api');

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
            if ($warehouses = $this->Settings_api->getSettings($filters)) {
                $settings = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Settings_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$this->sma->print_arrays($this->db->database);
					$warehouse->company=str_replace("cpesoft_","",$this->db->database);
                    $otherdb = $this->load->database('info', true);
                    $warehouse->sunmi_version = $otherdb->get('tbl_versions')->row()->sunmi_version;
                    $check = $otherdb->where('company',$warehouse->company)->get('client')->row();

                    //print_r($check->created_at);
                    $warehouse->effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($check->renewal_date)));
                    $warehouse->active = $check->status;

					$settings[] = $this->setObject($warehouse);
                }
				$data['settings']=$settings;

			}

            if ($warehouses = $this->Psettings_api->getPsettings($filters)) {
                $possettings = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Psettings_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $possettings[] = $this->setObject($warehouse);
                }
				$data['possettings']=$possettings;

			}

            if ($warehouses = $this->Users_api->getPermissions($filters)) {
                $permissions = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Users_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $permissions[] = $this->setObject($warehouse);
                }
				$data['permissions']=$permissions;
			}

            if ($warehouses = $this->Settings_api->getWhatsappSettings($filters)) {
                $whatsapp_settings = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Settings_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }


                    if(!$this->Settings_api->isPOSWhatsappSettings()){
					    $warehouse=null;
                    }
					$whatsapp_settings[] = $this->setObject(json_decode($warehouse));
                    //$this->sma->print_arrays(($whatsapp_settings[0]['4whatsapp']));
                }

				$data['whatsapp_settings']=$whatsapp_settings;//[0]['4whatsapp'];

			}

            if ($warehouses = $this->Settings_api->getSmsSettings($filters)) {
                $sms_settings = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Settings_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }

                    //$this->sma->print_arrays(($warehouse));

					$sms_settings[] = $this->setObject(json_decode($warehouse));
                }

				$data['sms_settings']=$sms_settings;

			}

            $data['message']="Success";

                $this->response($data, REST_Controller::HTTP_OK);

        }
    }
}
