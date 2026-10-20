<?php
//ini_set("display_errors", 1);
//ini_set("pcre.backtrack_limit", "100000000");
//ini_set("pcre.recursion_limit", "100000000");

//ini_set('memory_limit', '-1');
//ini_set('memory_limit','2048M');
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Data extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Taxrates_api');
		$this->load->api_model('Currencies_api');
		$this->load->api_model('Users_api');
        $this->load->api_model('companies_api');
        $this->load->api_model('subscriptions_api');
		$this->load->api_model('Settings_api');
		$this->load->api_model('Psettings_api');
		$this->load->api_model('Categories_api');
		$this->load->api_model('Units_api');
		$this->load->api_model('Variants_api');
		$this->load->api_model('Giftcards_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('products_api');
		$this->load->api_model('Wproducts_api');
		$this->load->api_model('Expense_categories_api');
		$this->load->api_model('Price_groups_api');
		$this->load->api_model('Product_prices_api');
		$this->load->api_model('Product_variants_api');
		$this->load->api_model('Resturant_emp_types_api');
		$this->load->api_model('Resturant_emp_api');
		$this->load->api_model('Dept_api');
		$this->load->api_model('Promo_api');
		$this->load->api_model('Resturant_tables_api');
		$this->load->api_model('Customer_groups_api');
		$this->load->api_model('Payment_type_api');
		$this->load->api_model('Gl_payment_methods_api');
		$this->load->api_model('Tailer_api');
		$this->load->admin_model('products_model');
		$this->load->admin_model('accounts_model');
		$this->load->admin_model('pos2_model');




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

		if(!$this->get('user_id')){
		//ini_set('memory_limit','2048M');
		}
		//$this->sma->print_arrays(memory_get_usage());
		//$this->sma->print_arrays($this->db->database);


		//'group'    => $this->get('group') ? $this->get('group') : '',


        $code = $this->get('code');

		$filters_count = [
            'groupc'    => 'customer',
            'groupb'    => 'biller',
            'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'biller_id'    => $this->get('user_id') ? $this->Users_api->getBillerIdByUserId($this->get('user_id')) : '',
			'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],

        ];


        $count_row=0;
		$count_row=($this->Resturant_tables_api->countResturant_tables()+ $this->Dept_api->countDept() + $this->Resturant_emp_api->countResturant_emp() + $this->Resturant_emp_types_api->countResturant_emp_types() + $this->Product_variants_api->countproduct_variants() + $this->Product_prices_api->countProduct_prices() + $this->Price_groups_api->countPrice_groups() + $this->Expense_categories_api->countExpense_categories() + $this->Giftcards_api->countGiftcards() + $this->Taxrates_api->countTaxrates() + $this->Currencies_api->countCurrencies() + $this->Users_api->countUsers($filters_count) + $this->companies_api->countCompanies($filters_count) + $this->subscriptions_api->countSubscriptions($filters_count) + $this->Settings_api->countSettings() + $this->Psettings_api->countPsettings() + $this->Categories_api->countCategories() + $this->Customer_groups_api->countCustomer_groups() + $this->Payment_type_api->countPayment_type() + $this->Gl_payment_methods_api->countPayment_type() + $this->Variants_api->countVariants()  + $this->Units_api->countUnits() + $this->Users_api->countPermissions()+ $this->Promo_api->countPromo($filters_count) + $this->warehouses_api->countWarehouses($filters_count)  );

		$filters = [
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : '',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $count_row,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],
        ];



        if ($code === null) {

		/*	$dbname=str_replace("cpesoft_","",$this->db->database);
		if($dbname=="mjahli" && empty($this->get('user_id'))){
			ini_set('max_execution_time', 300);
			//$this->sma->print_arrays($dbname);
		ini_set('memory_limit','2048M');
		}*/

			$data = [];


            if ($filters_count['warehouse_id']) {

            if ($count_row>0) {
            if ($warehouses = $this->Taxrates_api->getTaxrates($filters)) {
                $taxrates = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Taxrates_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $taxrates[] = $this->setWarehouse($warehouse);
                }


				$data['taxrates']=$taxrates;
			}

			if ($warehouses = $this->Currencies_api->getCurrencies($filters)) {
                $currencies = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Currencies_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $currencies[] = $this->setWarehouse($warehouse);
                }


				$data['currencies']=$currencies;
			}

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


				if ($warehouses = $this->Users_api->getUsers($filters_count)) {
                $users = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Users_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->password='';
					$warehouse->warehouse_code=$this->get('warehouse_code');

                    $users[] = $this->setWarehouse($warehouse);
                }

				$data['users']=$users;

            }



			if ($companies = $this->companies_api->getCompanies($filters_count)) {
                $companies_arr = [];
                foreach ($companies as $company) {
					if($company->group_name=='biller')
						$company->invoice_footer='<center>'.$company->invoice_footer.'</center>';
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'user') {
                                $company->users = $this->companies_api->getCompanyUser($company->id);
                            }
                        }
                    }

					if($company->hide_in_pos==NULL)
						$company->hide_in_pos="0";

                    if($company->subscription_duration==NULL)
						$company->subscription_duration="0";
					if($company->start_subscription==NULL)
						$company->start_subscription="";
                    if($company->end_subscription==NULL)
						$company->end_subscription="";


                    $companies_arr[] = $this->setWarehouse($company);
                }

				$data['companies']=$companies_arr;


            }


            if ($subscriptions = $this->subscriptions_api->getSubscriptions($filters_count)) {
                $subscriptions_arr = [];
                foreach ($subscriptions as $subscriptions) {




                    $subscriptions_arr[] = $this->setWarehouse($subscriptions);
                }

				$data['subscriptions']=$subscriptions_arr;


            }


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
                    if($check ){
                        $warehouse->effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($check->renewal_date)));
                        $warehouse->active = $check->status;
                        }else{

                            $otherdb = $this->load->database('onlineinfo', true);
                            $check = $otherdb->where('company',$warehouse->company)->get('client')->row();
                            $warehouse->effectiveEndDate = date('Y-m-d', strtotime("+14 days", strtotime($check->renewal_date)));
                            $warehouse->active = $check->status;
                        }

					$settings[] = $this->setWarehouse($warehouse);
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
                    $possettings[] = $this->setWarehouse($warehouse);
                }
				$data['possettings']=$possettings;

			}

			if ($warehouses = $this->Categories_api->getCategories($filters_count)) {
                $categories = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Categories_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $categories[] = $this->setWarehouse($warehouse);
                }
				$data['categories']=$categories;

			}
			if ($warehouses = $this->Units_api->getUnits($filters)) {
                $units = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Units_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $units[] = $this->setWarehouse($warehouse);
                }
				$data['units']=$units;
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
                    $permissions[] = $this->setWarehouse($warehouse);
                }
				$data['permissions']=$permissions;
			}

			if ($warehouses = $this->Customer_groups_api->getCustomer_groups($filters)) {
                $customer_groups = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Customer_groups_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $customer_groups[] = $this->setWarehouse($warehouse);
                }
				$data['customer_groups']=$customer_groups;
			}



			if ($warehouses = $this->Payment_type_api->getPayment_type($filters)) {
                $payment_type = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Payment_type_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $payment_type[] = $this->setWarehouse($warehouse);
                }
				$data['payment_type']=$payment_type;
			}

			if ($warehouses = $this->Gl_payment_methods_api->getPayment_type($filters)) {
                $payment_type = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $payment_type[] = $this->setWarehouse($warehouse);
                }
				$data['gl_payment_methods']=$payment_type;
			}




			//$this->sma->print_arrays($this->Settings_api->getSettings($filters)[0]->display_symbol);
			if(isset($this->Settings_api->getSettings($filters)[0]->isTailer) && $this->Settings_api->getSettings($filters)[0]->isTailer==1){


			  /*
			  if ($warehouses = $this->Tailer_api->getTR_Measurement($filters)) {
                $TR_Measurement = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Measurement[] = $this->setWarehouse($warehouse);
                }
			}
				$data['TR_Measurement']=$TR_Measurement;
			*/
			if ($warehouses = $this->Tailer_api->getTR_tailor($filters)) {
                $TR_tailor = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_tailor[] = $this->setWarehouse($warehouse);
                }
			}
				$data['TR_tailor']=$TR_tailor;

			if ($warehouses = $this->Tailer_api->getTR_Filling($filters)) {
                $TR_Filling = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Filling[] = $this->setWarehouse($warehouse);
                }
			}
				$data['TR_Filling']=$TR_Filling;

			if ($warehouses = $this->Tailer_api->getTR_Zipper($filters)) {
                $TR_Zipper = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Zipper[] = $this->setWarehouse($warehouse);
                }
			}
				$data['TR_Zipper']=$TR_Zipper;


                if ($warehouses = $this->Tailer_api->getTR_Zipper_Padding($filters)) {
                    $TR_Zipper_Padding = [];
                    foreach ($warehouses as $warehouse) {
                        if (!empty($filters['include'])) {
                            foreach ($filters['include'] as $include) {
                                if ($include == 'price_group') {
                                   // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                                }
                            }
                        }
                        //$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                        $TR_Zipper_Padding[] = $this->setWarehouse($warehouse);
                    }
                }
                    $data['TR_Zipper_Padding']=$TR_Zipper_Padding;

                    if ($warehouses = $this->Tailer_api->getTR_Embroidery($filters)) {
                        $TR_Embroidery = [];
                        foreach ($warehouses as $warehouse) {
                            if (!empty($filters['include'])) {
                                foreach ($filters['include'] as $include) {
                                    if ($include == 'price_group') {
                                       // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                                    }
                                }
                            }
                            //$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                            $TR_Embroidery[] = $this->setWarehouse($warehouse);
                        }
                    }
                        $data['TR_Embroidery']=$TR_Embroidery;


                        if ($warehouses = $this->Tailer_api->getTR_Buttons($filters)) {
                            $TR_Buttons = [];
                            foreach ($warehouses as $warehouse) {
                                if (!empty($filters['include'])) {
                                    foreach ($filters['include'] as $include) {
                                        if ($include == 'price_group') {
                                           // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                                        }
                                    }
                                }
                                //$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                                $TR_Buttons[] = $this->setWarehouse($warehouse);
                            }
                        }
                            $data['TR_Buttons']=$TR_Buttons;


                    if ($warehouses = $this->Tailer_api->getTR_Sleeve($filters)) {
                    $TR_Sleeve = [];
                    foreach ($warehouses as $warehouse) {
                        if (!empty($filters['include'])) {
                            foreach ($filters['include'] as $include) {
                                if ($include == 'price_group') {
                                   // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                                }
                            }
                        }
                        //$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                        $TR_Sleeve[] = $this->setWarehouse($warehouse);
                    }
                }
                    $data['TR_Sleeve']=$TR_Sleeve;


                    if ($warehouses = $this->Tailer_api->getTR_Chest_Pocket($filters)) {
                        $TR_Chest_Pocket = [];
                        foreach ($warehouses as $warehouse) {
                            if (!empty($filters['include'])) {
                                foreach ($filters['include'] as $include) {
                                    if ($include == 'price_group') {
                                       // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                                    }
                                }
                            }
                            //$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                            $TR_Chest_Pocket[] = $this->setWarehouse($warehouse);
                        }
                    }
                        $data['TR_Chest_Pocket']=$TR_Chest_Pocket;



            if ($warehouses = $this->Tailer_api->getTR_Chest_Pocket_Padding($filters)) {
                        $TR_Chest_Pocket_Padding = [];
                        foreach ($warehouses as $warehouse) {
                            if (!empty($filters['include'])) {
                                foreach ($filters['include'] as $include) {
                                    if ($include == 'price_group') {
                                       // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                                    }
                                }
                            }
                            //$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                            $TR_Chest_Pocket_Padding[] = $this->setWarehouse($warehouse);
                        }
                    }
                        $data['TR_Chest_Pocket_Padding']=$TR_Chest_Pocket_Padding;

			if ($warehouses = $this->Tailer_api->getTR_Collar($filters)) {
                $TR_Collar = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Collar[] = $this->setWarehouse($warehouse);
                }
			}
				$data['TR_Collar']=$TR_Collar;

			if ($warehouses = $this->Tailer_api->getTR_Cuff($filters)) {
                $TR_Cuff = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Cuff[] = $this->setWarehouse($warehouse);
                }
			}
				$data['TR_Cuff']=$TR_Cuff;

			if ($warehouses = $this->Tailer_api->getTR_Model($filters)) {
                $TR_Model = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Model[] = $this->setWarehouse($warehouse);
                }
			}
				$data['TR_Model']=$TR_Model;

			if ($warehouses = $this->Tailer_api->getTR_Pocket($filters)) {
                $TR_Pocket = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                               // $warehouse->price_group = $this->Gl_payment_methods_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					//$warehouse->payment_account_name=$this->accounts_model->getAccounts($warehouse->payment_account)[0]->accountName;

                    $TR_Pocket[] = $this->setWarehouse($warehouse);
                }
			}
				$data['TR_Pocket']=$TR_Pocket;


			}


			if ($warehouses = $this->Promo_api->getPromo($filters)) {
                $promo = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Units_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					$warehouse->offer_product_code=$this->products_model->getProductByID($warehouse->offer_product)->code;
					if($warehouse->free_product)
					$warehouse->free_product_code=$this->products_model->getProductByID($warehouse->free_product)->code;
				    else
					$warehouse->free_product_code='';

                    $promo[] = $this->setWarehouse($warehouse);
                }
				$data['promo']=$promo;
			}



            if ($warehouses = $this->Promo_api->getPromoDetails($filters)) {
                $promo_details = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->Units_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					$warehouse->offer_product_code=$this->products_model->getProductByID($warehouse->offer_product)->code;


                    $promo_details[] = $this->setWarehouse($warehouse);
                }
				$data['promo_details']=$promo_details;
			}

			if ($warehouses = $this->Expense_categories_api->getExpense_categories($filters)) {
                $expense_categories = [];
                foreach ($warehouses as $warehouse) {

                    $expense_categories[] = $this->setWarehouse($warehouse);
                }
				$data['expense_categories']=$expense_categories;
			}


			if ($warehouses = $this->warehouses_api->getWarehouses($filters_count)) {
                $warehouses_arr = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'price_group') {
                                $warehouse->price_group = $this->warehouses_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
					$warehouse->Max_Offline_Pos_id=$this->pos2_model->getMaxOffline_Pos_idBywarehouse_id($warehouse->id);
                    $warehouse->logo_image_url=$this->pos2_model->getlogo_image_url();

                    $warehouses_arr[] = $this->setWarehouse($warehouse);
                }

				$data['warehouses']=$warehouses_arr;

			}


















			if ($price_groups = $this->Price_groups_api->getPrice_groups($filters_count)) {
                $price_groups_data = [];
                foreach ($price_groups as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
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


                    $price_groups_data[] = $this->setWarehouse($product);
                }
				$data['price_groups']=$price_groups_data;
			}


			if ($product_prices = $this->Product_prices_api->getProduct_prices($filters_count)) {
                $product_prices_data = [];
                foreach ($product_prices as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
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


                    $product_prices_data[] = $this->setWarehouse($product);
                }
				$data['product_prices']=$product_prices_data;
			}


			if ($warehouses = $this->Variants_api->getVariants($filters)) {
                $variants = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'price_group') {
                                //$warehouse->price_group = $this->Units_api->getPriceGroupByID($warehouse->price_group_id);
                            }
                        }
                    }
                    $variants[] = $this->setWarehouse($warehouse);
                }
				$data['variants']=$variants;
			}
			if ($product_variants = $this->Product_variants_api->getproduct_variants($filters_count)) {
                $product_variants_data = [];
                foreach ($product_variants as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
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
					$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));


                    $product_variants_data[] = $this->setWarehouse($product);
                }
				$data['product_variants']=$product_variants_data;
			}



			if ($Resturant_emp_types = $this->Resturant_emp_types_api->getResturant_emp_types($filters_count)) {
                $Resturant_emp_types_data = [];
                foreach ($Resturant_emp_types as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
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


                    $Resturant_emp_types_data[] = $this->setWarehouse($product);
                }
				$data['resturant_emp_types']=$Resturant_emp_types_data;
			}




			if ($Resturant_emp = $this->Resturant_emp_api->getResturant_emp($filters_count)) {
                $Resturant_emp_data = [];
                foreach ($Resturant_emp as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
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


                    $Resturant_emp_data[] = $this->setWarehouse($product);
                }
				$data['resturant_emp']=$Resturant_emp_data;
			}



			if ($Dept = $this->Dept_api->getDept($filters_count)) {
                $Dept_data = [];
                foreach ($Dept as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
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


                    $Dept_data[] = $this->setWarehouse($product);
                }
				$data['dept']=$Dept_data;
			}


			/*
			if ($Order_type = $this->Order_type_api->getOrder_type($filters_count)) {
                $Order_type_data = [];
                foreach ($Order_type as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
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


			if ($Resturant_tables = $this->Resturant_tables_api->getResturant_tables($filters_count)) {
                $Resturant_tables_data = [];
                foreach ($Resturant_tables as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
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


                    $Resturant_tables_data[] = $this->setWarehouse($product);
                }
				$data['resturant_tables']=$Resturant_tables_data;
			}





                    $data['limit'] = $filters['limit'];
                    $data['start'] = $filters['start'];
                    $data['total'] = ($this->Resturant_tables_api->countResturant_tables()+ $this->Dept_api->countDept() + $this->Resturant_emp_api->countResturant_emp() + $this->Resturant_emp_types_api->countResturant_emp_types() + $this->Product_variants_api->countproduct_variants()  + $this->Product_prices_api->countProduct_prices() + $this->Price_groups_api->countPrice_groups() + $this->Expense_categories_api->countExpense_categories($filters) + $this->Giftcards_api->countGiftcards() + $this->Taxrates_api->countTaxrates($filters) + $this->Currencies_api->countCurrencies($filters) + $this->Users_api->countUsers($filters_count) + $this->companies_api->countCompanies($filters_count) + $this->subscriptions_api->countSubscriptions($filters_count) + $this->Settings_api->countSettings($filters) + $this->Psettings_api->countPsettings($filters) + $this->Categories_api->countCategories($filters) + $this->Customer_groups_api->countCustomer_groups() + $this->Payment_type_api->countPayment_type() + $this->Gl_payment_methods_api->countPayment_type() + $this->Variants_api->countVariants($filters) + $this->Units_api->countUnits($filters) + $this->Users_api->countPermissions($filters)+ $this->Promo_api->countPromo($filters) + $this->warehouses_api->countWarehouses($filters_count) );
                    $data['message'] = "Success";
                    $data['status'] = true;


                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No data were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->response([
                'message' => 'Warehouse does not exist ',
                'status'  => false,
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    } else {

            $this->set_response([
                'message' => 'No data were found.',
                'status'  => false,
            ], REST_Controller::HTTP_NOT_FOUND);

    }
    }
}
