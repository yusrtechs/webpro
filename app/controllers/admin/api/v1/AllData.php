<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class AllData extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('Taxrates_api');
		$this->load->api_model('Users_api');
        $this->load->api_model('companies_api');
		$this->load->api_model('Settings_api');
		$this->load->api_model('Psettings_api');
		$this->load->api_model('Categories_api');
		$this->load->api_model('Units_api');
		$this->load->api_model('warehouses_api');
		$this->load->api_model('Products_All_api');
		$this->load->api_model('Comboitems_api');
		$this->load->api_model('Wproducts_api');
		$this->load->api_model('Expense_categories_api');
		$this->load->api_model('Price_groups_api');
		$this->load->api_model('Product_prices_api');
		$this->load->api_model('Resturant_emp_types_api');
		$this->load->api_model('Resturant_emp_api');
		$this->load->api_model('Dept_api');
		$this->load->api_model('Resturant_tables_api');
		$this->load->admin_model('products_model');
		
		
		
    
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
		
		
		//'group'    => $this->get('group') ? $this->get('group') : '',
            
		
        $code = $this->get('code');
		$filters_count = [
            'groupc'    => 'customer',
            'groupb'    => 'biller',
            'warehouse_code'    => $this->get('warehouse_code') ? $this->get('warehouse_code') : '',
			'warehouse_id'    => $this->get('warehouse_code') ? $this->warehouses_api->getWarehouseIdByCode($this->get('warehouse_code')) : '',
			'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],
        
        ];
		

        $count_row=0;
		$count_row=($this->Resturant_tables_api->countResturant_tables()+ $this->Dept_api->countDept() + $this->Resturant_emp_api->countResturant_emp() + $this->Resturant_emp_types_api->countResturant_emp_types() + $this->Product_prices_api->countProduct_prices() + $this->Price_groups_api->countPrice_groups() + $this->Expense_categories_api->countExpense_categories() + $this->Taxrates_api->countTaxrates() + $this->Users_api->countUsers($filters_count) + $this->companies_api->countCompanies($filters_count) + $this->Settings_api->countSettings() + $this->Psettings_api->countPsettings() + $this->Categories_api->countCategories() + $this->Units_api->countUnits() + $this->warehouses_api->countWarehouses($filters_count) + $this->Products_All_api->getProductsCount($filters_count)->count + $this->Comboitems_api->getProductsCount($filters_count)->count + $this->Wproducts_api->getWproductsCount($filters_count)->count);
		
		$filters = [
            'include'  => $this->get('include') ? explode(',', $this->get('include')) : null,
            'group'    => $this->get('group') ? $this->get('group') : '',
            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : $count_row,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'acs'],
        ];
		


        if ($code === null) {
			
			$data = [];
				
				
				
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
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
                            if ($include == 'user') {
                                $company->users = $this->companies_api->getCompanyUser($company->id);
                            }
                        }
                    }

                    $companies_arr[] = $this->setWarehouse($company);
                }
				
				$data['companies']=$companies_arr;

                
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
			
			if ($warehouses = $this->Categories_api->getCategories($filters)) {
                $categories = [];
                foreach ($warehouses as $warehouse) {
                    if (!empty($filters['include'])) {
                        foreach ($filters['include'] as $include) {
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
                    $warehouses_arr[] = $this->setWarehouse($warehouse);
                }
				$data['warehouses']=$warehouses_arr;

			}
			
			
			
			if ($products = $this->Products_All_api->getProducts($filters_count)) {
                $pr_data = [];
                foreach ($products as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'brand') {
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
                            }
                        }
                    }

                    $pr_data[] = $this->setWarehouse($product);
                }
				$data['products']=$pr_data;
				
			}
			
			
			
			if ($products = $this->Comboitems_api->getProducts($filters_count)) {
                $cpr_data = [];
                foreach ($products as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'brand') {
                                $product->brand = $this->Comboitems_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Comboitems_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Comboitems_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Comboitems_api->getSubUnits($product->unit);
                            }
                        }
                    }

                    $cpr_data[] = $this->setWarehouse($product);
                }
				$data['combo_items']=$cpr_data;
			
			}
			
			
			
            if ($products = $this->Wproducts_api->getWproducts($filters_count)) {
                $wpr_data = [];
                foreach ($products as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'brand') {
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
                            }
                        }
						
                    }
					$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));
					
					$product->warehouse_code=$this->get('warehouse_code');

                    $wpr_data[] = $this->setWarehouse($product);
                }
				$data['warehouses_products']=$wpr_data;
			}
			
			
			
			
				

			if ($price_groups = $this->Price_groups_api->getPrice_groups($filters_count)) {
                $price_groups_data = [];
                foreach ($price_groups as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'brand') {
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
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
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
                            }
                        }
						
                    }
					//$product->code=$this->products_model->getProductByID($product->product_id)->code;
					//$this->sma->print_arrays($this->getProductByID($product->id));
					
					
                    $product_prices_data[] = $this->setWarehouse($product);
                }
				$data['product_prices']=$product_prices_data;
			}
			
			
			
			if ($Resturant_emp_types = $this->Resturant_emp_types_api->getResturant_emp_types($filters_count)) {
                $Resturant_emp_types_data = [];
                foreach ($Resturant_emp_types as $product) {
                    if (!empty($filters_count['include'])) {
                        foreach ($filters_count['include'] as $include) {
                            if ($include == 'brand') {
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
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
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
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
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
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
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
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
                                $product->brand = $this->Products_All_api->getBrandByID($product->brand);
                            } elseif ($include == 'category') {
                                $product->category = $this->Products_All_api->getCategoryByID($product->category);
                            } elseif ($include == 'photos') {
                                $product->photos = $this->Products_All_api->getProductPhotos($product->id);
                            } elseif ($include == 'sub_units') {
                                $product->sub_units = $this->Products_All_api->getSubUnits($product->unit);
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
                    $data['total'] = ($this->Resturant_tables_api->countResturant_tables()+ $this->Dept_api->countDept() + $this->Resturant_emp_api->countResturant_emp() + $this->Resturant_emp_types_api->countResturant_emp_types() + $this->Product_prices_api->countProduct_prices() + $this->Price_groups_api->countPrice_groups() + $this->Expense_categories_api->countExpense_categories($filters) + $this->Taxrates_api->countTaxrates($filters) + $this->Users_api->countUsers($filters_count) + $this->companies_api->countCompanies($filters_count) + $this->Settings_api->countSettings($filters) + $this->Psettings_api->countPsettings($filters) + $this->Categories_api->countCategories($filters) + $this->Units_api->countUnits($filters) + $this->warehouses_api->countWarehouses($filters_count) + $this->Products_All_api->getProductsCount($filters_count)->count + $this->Comboitems_api->getProductsCount($filters_count)->count + $this->Wproducts_api->getWproductsCount($filters_count)->count);
               
				
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No data were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            if ($warehouse = $this->Taxrates_api->getWarehouse($filters)) {
                if (!empty($filters['include'])) {
                    foreach ($filters['include'] as $include) {
                        if ($include == 'price_group') {
                            $warehouse->price_group = $this->Taxrates_api->getPriceGroupByID($warehouse->price_group_id);
                        }
                    }
                }
                $warehouse = $this->setWarehouse($warehouse);
                $this->set_response($warehouse, REST_Controller::HTTP_OK);
            } else {
                $this->set_response([
                    'message' => 'Data could not be found for name ' . $name . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
}
