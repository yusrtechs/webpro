<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addBrand($data)
    {
        if ($this->db->insert('brands', $data)) {

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $bid = $this->db->insert_id();
                    $data_brands_details = [
                        'brand_id'            => $bid,
                        'grpId'            => $data ['grpId'],
                        'coId'            => $data ['coId'],
                        'brId'            => $data ['brId'],



                    ];


                    if ($this->db->insert('brands_details', $data_brands_details)) {

                        //return true;
                    }



            }
            return true;
        }
        return false;
    }

    public function addBrands($data)
    {
        if ($this->db->insert_batch('brands', $data)) {
            return true;
        }
        return false;
    }

    public function addCategories($categories, $subcategories)
    {
        $result = false;
        if (!empty($categories)) {
            foreach ($categories as $category) {
                if (!is_int($category['parent_id'])) {
                    $category['parent_id'] = null;
                }
                $this->db->insert('categories', $category);
            }
            $result = true;
        }
        if (!empty($subcategories)) {
            foreach ($subcategories as $category) {
                if (is_int($category['parent_id'])) {
                    $this->db->insert('categories', $category);
                } else {
                    if ($pcategory = $this->getCategoryByCode($category['parent_id'])) {
                        $category['parent_id'] = $pcategory->id;
                        $this->db->insert('categories', $category);
                    }
                }
            }
            $result = true;
        }
        return $result;
    }

    public function addCategory($data)
    {
        if ($this->db->insert('categories', $data)) {

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                $cid = $this->db->insert_id();
                    $data_categories_details = [
                        'category_id'            => $cid,
                        'grpId'            => $data ['grpId'],
                        'coId'            => $data ['coId'],
                        'brId'            => $data ['brId'],



                    ];


                    if ($this->db->insert('categories_details', $data_categories_details)) {

                        //return true;
                    }



            }

            if(isset($this->Settings->isStore) && $this->Settings->isStore==1)
              {

                $this->load->admin_model('shop_admin_model');
                $syncSettings        = $this->shop_admin_model->getShopSettings();


        $url = $syncSettings->base_url.'/api/category/'.$syncSettings->store_id.'/store';
        $uploads_url = base_url('assets/uploads/');

        $total        = $this->shop_admin_model->getCategoriesCount()->total;
        $categories        = $this->shop_admin_model->getCategories();
        $to=0;


foreach ($categories as $cat) {

                $image=$uploads_url."".($cat->image ? ($cat->image!="no_image.png" ? $cat->image : 'placeholder.png') : 'placeholder.png');
                $name_en=(isset($cat->nameEn) ? $cat->nameEn : '');

                $ch = curl_init($url);
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $headers[] = 'Accept:application/json';
                $headers[] = "Authorization: Bearer ".$syncSettings->token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
                $data='{"name": "'.$cat->name.'","image_url": "'.$image.'","name_en": "'.$name_en.'"}';
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                /* execute request */
                $result = json_decode(curl_exec($ch));
                //$result1 = (curl_exec($ch));


                /* close cURL resource */
                curl_close($ch);


                if($result->data){


                    $dataUpdate = [
				        'shop_id'            => $result->data->id,
			        ];
                    if ($this->shop_admin_model->updateCategory($cat->id,$dataUpdate)) {
                        $to++;
                    }
                }
            }

			  }
            return true;
        }
        return false;
    }

    public function addDepartment($data)
    {
        if ($this->db->insert('dept', $data)) {
            return true;
        }
        return false;
    }

    public function addTabel($data)
    {
        if ($this->db->insert('resturant_tables', $data)) {
            return true;
        }
        return false;
    }

    public function addStuff($data)
    {
        if ($this->db->insert('resturant_emp', $data)) {
            return true;
        }
        return false;
    }

    public function addCurrency($data)
    {
        if ($this->db->insert('currencies', $data)) {

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {
                $cuid = $this->db->insert_id();
                    $data_currencies_details = [
                        'currency_id'            => $cuid,
                        'grpId'            => $data ['grpId'],
                        'coId'            => $data ['coId'],
                        'brId'            => $data ['brId'],
                    ];

                    if ($this->db->insert('currencies_details', $data_currencies_details)) {

                    }

            }

            return true;
        }
        return false;
    }

     public function addPaymentType($data)
    {
        if ($this->db->insert('payment_type', $data)) {
            return true;
        }
        return false;
    }

    public function addCustomerGroup($data)
    {
        if ($this->db->insert('customer_groups', $data)) {
            return true;
        }
        return false;
    }

    public function addExpenseCategories($data)
    {
        if ($this->db->insert_batch('expense_categories', $data)) {
            return true;
        }
        return false;
    }

    public function addExpenseCategory($data)
    {
        if ($this->db->insert('expense_categories', $data)) {
            return true;
        }
        return false;
    }

    public function addGroup($data)
    {
        if ($this->db->insert('groups', $data)) {
            $gid = $this->db->insert_id();
            $this->db->insert('permissions', ['group_id' => $gid]);
            return $gid;
        }
        return false;
    }

    public function addPriceGroup($data)
    {
        if ($this->db->insert('price_groups', $data)) {
            return true;
        }
        return false;
    }

    public function addTaxRate($data, $old_id)
    {
        if ($this->db->insert('tax_rates', $data)) {
			$gid = $this->db->insert_id();
			if($data['tax_active']==1){

			//$this->db->update('products', ['tax_rate' => $gid], ['tax_rate' => $old_id]);


			}

            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {

                        $data_tax_rate_details = [
                        'tax_rate_id'            => $gid,
                        'grpId'            => $data ['grpId'],
                        'coId'            => $data ['coId'],
                        'brId'            => $data ['brId'],

                    ];

                    if ($this->db->insert('taxs_details', $data_tax_rate_details)) {
                        //return true;
                    }

            }

            return true;
        }
        return false;
    }

    public function addUnit($data)
    {
        if ($this->db->insert('units', $data)) {

            $uid = $this->db->insert_id();
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {


                $data_units_details = [
                    'unit_id'            => $uid,
                    'grpId'            => $data ['grpId'],
                    'coId'            => $data ['coId'],
                    'brId'            => $data ['brId'],



                ];


                if ($this->db->insert('units_details', $data_units_details)) {

                    //return true;
                }



        }

            return true;
        }
        return false;
    }

    public function addVariant($data)
    {
        if ($this->db->insert('variants', $data)) {
            $Vid = $this->db->insert_id();
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {


                $data_variants_details = [
                    'variant_id'            => $Vid,
                    'grpId'            => $data ['grpId'],
                    'coId'            => $data ['coId'],
                    'brId'            => $data ['brId'],



                ];


                if ($this->db->insert('variants_details', $data_variants_details)) {

                }



        }
            return true;
        }
        return false;
    }

    public function addWarehouse($data)
    {
        if ($this->db->insert('warehouses', $data)) {
            return true;
        }
        return false;
    }

    public function addWarehouse_temp($data)
    {
        //if ($this->db->insert('warehouses_temp', ['code' => $data])) {
		if ($this->db->insert('warehouses_temp', $data)) {
            return true;
        }
        return false;
    }

    public function brandHasProducts($brand_id)
    {
        $q = $this->db->get_where('products', ['brand' => $brand_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function checkGroupUsers($id)
    {
        $q = $this->db->get_where('users', ['group_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function deleteBrand($id)
    {
        if ($this->db->delete('brands', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteCategory($id)
    {
        if ($this->db->delete('categories', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteDepartment($id)
    {
        if ($this->db->delete('dept', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteTabel($id)
    {
        if ($this->db->delete('resturant_tables', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteStuff($id)
    {
        if ($this->db->delete('resturant_emp', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteCurrency($id)
    {
        if ($this->db->delete('currencies', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteCustomerGroup($id)
    {
        if ($this->getCustomerGroupCustomer($id)) {
            return false;
        }

        if ($this->db->delete('customer_groups', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteExpenseCategory($id)
    {
        if ($this->db->delete('expense_categories', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteGroup($id)
    {
        if ($this->db->delete('groups', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteInvoiceType($id)
    {
        if ($this->db->delete('invoice_types', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deletePriceGroup($id)
    {
        if ($this->db->delete('price_groups', ['id' => $id]) && $this->db->delete('product_prices', ['price_group_id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteProductGroupPrice($product_id, $group_id,$product_unit_id)
    {
        if ($this->db->delete('product_prices', ['price_group_id' => $group_id, 'product_id' => $product_id, 'product_unit_id' => $product_unit_id])) {
            return true;
        }
        return false;
    }

    public function deleteTaxRate($id)
    {
        if ($this->db->delete('tax_rates', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteUnit($id)
    {
        if ($this->db->delete('units', ['id' => $id])) {
            $this->db->delete('units', ['base_unit' => $id]);
            return true;
        }
        return false;
    }

    public function deleteVariant($id)
    {
        if ($this->db->delete('variants', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteWarehouse($id)
    {
        if ($this->db->delete('warehouses', ['id' => $id]) && $this->db->delete('warehouses_products', ['warehouse_id' => $id])) {
            return true;
        }
        return false;
    }

    public function getAllCurrencies()
    {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCustomerGroups()
    {
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllPriceGroups($warehouse_id = null)
    {
        if($warehouse_id){
            $this->db->where('warehouse_id', $warehouse_id)->or_where('warehouse_id', 0);
        }
        $q = $this->db->get('price_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllVariants()
    {
        $q = $this->db->get('variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllWarehouses()
    {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getAllWarehouses_temp()
    {
        $this->db->select('code, name, address, map, phone, email, price_group_id');
		$q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getBrandByName($name)
    {
        $q = $this->db->get_where('brands', ['name' => $name], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCategoryByCode($code)
    {
        $q = $this->db->get_where('categories', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCategoryByID($id)
    {
        $q = $this->db->get_where('categories', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

     public function getDepartmentByID($id)
    {
        $q = $this->db->get_where('dept', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

     public function getTabelByID($id)
    {
        $q = $this->db->get_where('resturant_tables', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

     public function getStuffByID($id)
    {
        $q = $this->db->get_where('resturant_emp', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCurrencyByID($id)
    {
        $q = $this->db->get_where('currencies', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPaymentTypeByID($id)
    {
        $q = $this->db->get_where('payment_type', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCustomerGroupByID($id)
    {
        $q = $this->db->get_where('customer_groups', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getDateFormats()
    {
        $q = $this->db->get('date_format');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getExpenseCategoryByCode($code)
    {
        $q = $this->db->get_where('expense_categories', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getExpenseCategoryByID($id)
    {
        $q = $this->db->get_where('expense_categories', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getGroupByID($id)
    {
        $q = $this->db->get_where('groups', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getGroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', ['group_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getGroupPrice($group_id, $product_id , $product_unit_id= null)
    {
        if($product_unit_id)
			$q = $this->db->get_where('product_prices', ['price_group_id' => $group_id, 'product_id' => $product_id, 'product_unit_id' => $product_unit_id], 1);
        else
			$q = $this->db->get_where('product_prices', ['price_group_id' => $group_id, 'product_id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getGroups()
    {
        $this->db->where('id >', 4);
        $q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getParentCategories()
    {
        $this->db->where('parent_id', null)->or_where('parent_id', 0);
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

     public function getParentDepartments()
    {
        $this->db->where('parent', null)->or_where('parent', 0);
        $q = $this->db->get('dept');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

       public function getDepartments()
    {
        $q = $this->db->get('dept');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

       public function getStuffTypes()
    {
        $q = $this->db->get('resturant_emp_types');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPaypalSettings()
    {
        $q = $this->db->get('paypal');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPriceGroupByID($id)
    {
        $q = $this->db->get_where('price_groups', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductGroupPriceByPID($product_id, $group_id)
    {
        $pg = "(SELECT {$this->db->dbprefix('product_prices')}.price as price, {$this->db->dbprefix('product_prices')}.product_id as product_id FROM {$this->db->dbprefix('product_prices')} WHERE {$this->db->dbprefix('product_prices')}.product_id = {$product_id} AND {$this->db->dbprefix('product_prices')}.price_group_id = {$group_id}) GP";

        $this->db->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, GP.price", false)
        // ->join('products', 'products.id=product_prices.product_id', 'left')
        ->join($pg, 'GP.product_id=products.id', 'left');
        $q = $this->db->get_where('products', ['products.id' => $product_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSettingsInfo()
    {
        $this->db->select('site_name,site_ename');
		$q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSkrillSettings()
    {
        $q = $this->db->get('skrill');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTaxRateByID($id)
    {
        $q = $this->db->get_where('tax_rates', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getUnitChildren($base_unit)
    {
        $this->db->where('base_unit', $base_unit);
        $q = $this->db->get('units');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getVariantByID($id)
    {
        $q = $this->db->get_where('variants', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getWarehouseByID($id)
    {
        $q = $this->db->get_where('warehouses', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function GroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', ['group_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return false;
    }

    public function getProductPriceForPriceGroup($product_id, $group_id, $product_unit_id)
    {
        $q = $this->db->get_where('product_prices', ['price_group_id' => $group_id, 'product_id' => $product_id, 'product_unit_id' => $product_unit_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function hasExpenseCategoryRecord($id)
    {
        $this->db->where('category_id', $id);
        return $this->db->count_all_results('expenses');
    }

    public function setProductPriceForPriceGroup($product_id, $group_id, $price, $unit, $product_code)
    {
        if ($this->getGroupPrice($group_id, $product_id, $unit)) {
            if ($this->db->update('product_prices', ['price' => $price,'product_code' => $product_code], ['price_group_id' => $group_id, 'product_id' => $product_id, 'product_unit_id' => $unit])) {
                return true;
            }
        } else {
            if ($this->db->insert('product_prices', ['product_code' => $product_code, 'price' => $price, 'product_unit_id' => $unit, 'price_group_id' => $group_id, 'product_id' => $product_id])) {
                return true;
            }
        }
        return false;
    }

    public function updateBrand($id, $data = [])
    {
        if ($this->db->update('brands', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateCategory($id, $data = [])
    {
        if ($this->db->update('categories', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

     public function updateDepartment($id, $data = [])
    {
        if ($this->db->update('dept', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

      public function updateTabel($id, $data = [])
    {
        if ($this->db->update('resturant_tables', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

      public function updateStuff($id, $data = [])
    {
        if ($this->db->update('resturant_emp', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateCurrency($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('currencies', $data)) {
            return true;
        }
        return false;
    }

    public function updatePaymentType($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('payment_type', $data)) {
            return true;
        }
        return false;
    }

    public function updateCustomerGroup($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('customer_groups', $data)) {
            return true;
        }
        return false;
    }

    public function updateExpenseCategory($id, $data = [])
    {
        if ($this->db->update('expense_categories', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateGroup($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('groups', $data)) {
            return true;
        }
        return false;
    }

    public function updateGroupPrices($data = [])
    {
        foreach ($data as $row) {
            if ($this->getGroupPrice($row['price_group_id'], $row['product_id'], $row['product_unit_id'])) {
                $this->db->update('product_prices', ['price' => $row['price']], ['product_id' => $row['product_id'], 'price_group_id' => $row['price_group_id'], 'product_unit_id' => $row['product_unit_id'], 'product_code' => $row['product_code']]);
            } else {
                $this->db->insert('product_prices', $row);
            }
        }
        return true;
    }

    public function updateLoginLogo($photo)
    {
        $logo = ['logo2' => $photo];
        if ($this->db->update('settings', $logo)) {
            return true;
        }
        return false;
    }

    public function updateLogo($photo)
    {
        $logo = ['logo' => $photo];
        if ($this->db->update('settings', $logo)) {
            return true;
        }
        return false;
    }

    public function updatePaypal($data)
    {
        $this->db->where('id', '1');
        if ($this->db->update('paypal', $data)) {
            return true;
        }
        return false;
    }

    public function updatePermissions($id, $data = [])
    {
        if ($this->db->update('permissions', $data, ['group_id' => $id]) && $this->db->update('users', ['show_price' => $data['products-price'], 'show_cost' => $data['products-cost']], ['group_id' => $id])) {
            return true;
        }
        return false;
    }

    public function updatePriceGroup($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('price_groups', $data)) {
            return true;
        }
        return false;
    }

    public function updateSetting($data)
    {
        $this->db->where('setting_id', '1');
        if ($this->db->update('settings', $data)) {
            if($data['isTobacco']==0){
                $this->db->update('warehouses', ['isTobacco' => 0]);
            }
            return true;
        }
        return false;
    }

    public function updateSkrill($data)
    {
        $this->db->where('id', '1');
        if ($this->db->update('skrill', $data)) {
            return true;
        }
        return false;
    }

	public function getTaxRatesActive()
    {
        $this->db->select('*');
		$this->db->where('rate >', 0);
        $this->db->where('tax_active =', 1);
        $this->db->where('category_id =', 1);
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }else{
			return $this->getTaxRatesLast();
		}
        return false;
    }

	public function getTaxRatesLast()
    {
        $this->db->select('rate');
		$this->db->where('rate >', 0)->order_by('id', 'desc')->limit(1);
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateTaxRate($id, $data = [], $old_id)
    {
		//$this->db->where('id', $id);
        if ($this->db->update('tax_rates', $data, ['id' => $id])) {

			//$old_id=$this->getTaxRatesActive()->id;

			/*if($data['tax_active']==1){

			$this->db->update('products', ['tax_rate' => $id], ['tax_rate' => $old_id]);
			$items=$this->getProductsByTaxRate($id);
			foreach ($items as $item) {
			$this->db->update('synch_products', ['api' => 0], ['product_code' => $item->code]);
			}


			}*/
			 return true;
        }
        return false;
    }

	    public function getProductsByTaxRate($tax_rate)
    {
        $q = $this->db->get_where('products',['tax_rate' => $tax_rate]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function updateUnit($id, $data = [])
    {
        if ($this->db->update('units', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function updateVariant($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('variants', $data)) {
            return true;
        }
        return false;
    }

    public function updateWarehouse($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('warehouses', $data)) {
            return true;
        }
        return false;
    }

    public function updateTaxRateDisactive()
    {
        $data = [
                'tax_active'            => 0,
            ];
        $this->db->where('rate>', 0);
        if ($this->db->update('tax_rates', $data)) {
            return true;
        }
        return false;
    }

    public function chexckSalesPurchasesTaxRateByID($id)
    {
        $q = $this->db->get_where('sales', ['order_tax_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return true;
        }else{
		$q = $this->db->get_where('purchases', ['order_tax_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return true;
        }
		}
        return false;
    }

    public function getAllTaxcategory()
    {
        $q = $this->db->get('tax_category');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }



    public function addTaxRatesDetails($taxs = [])
    {
        $flage=0;
        foreach ($taxs as $tax) {
            if($this->db->insert('taxs_details', $tax)){
                $flage=1;
            }
        }
        if($flage==1)
            return true;
        else
            return false;
    }

    public function addCurrenciesDetails($currencies = [])
    {
        $flage=0;
        foreach ($currencies as $currency) {
            if($this->db->insert('currencies_details', $currency)){
                $flage=1;
            }
        }
        if($flage==1)
            return true;
        else
            return false;
    }

    public function checkWarehouseOperation($warehouse_id)
    {
        $q = $this->db->get_where('sales', ['warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return true;
        }else{

		$q = $this->db->get_where('purchases', ['warehouse_id' => $warehouse_id], 1);
        if ($q->num_rows() > 0) {
            return true;
        }else{


            $q = $this->db->get_where('users', ['warehouse_id' => $warehouse_id], 1);
            if ($q->num_rows() > 0) {
                return true;
            }else{


                $q = $this->db->get_where('warehouses_products', ['warehouse_id' => $warehouse_id], 1);
                if ($q->num_rows() > 0) {
                    return true;
                }
            }

        }
    }

        return false;
    }

    public function updateSmsSettings($data)
    {
        if ($this->db->update('sms_settings', $data, ['id' => 1])) {
            return true;
        }
        return false;
    }

    public function updateWhatsappSettings($data)
    {
        if ($this->db->update('whatsapp_settings', $data, ['id' => 1])) {
            return true;
        }
        return false;
    }

    public function updateWhatsappTemplates($template,$data)
    {
        if ($this->db->update('whatsapp_templates', [$template => $data], ['id' => 1])) {
            return true;
        }
        return false;
    }

    public function getCustomerGroupCustomer($id)
    {
        $this->db->where('customer_group_id', $id)->from('companies');
        return $this->db->count_all_results();
    }

    public function getAllProductsUnitPricesCount($unit)
    {
        $this->db->select('*');
		$this->db->where('product_unit_id ', $unit);
		$q = $this->db->get('product_prices');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addCategoryAPI($data)
    {
        if ($this->db->insert('categories', $data)) {
                $cid = $this->db->insert_id();
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            {


                    $data_categories_details = [
                        'category_id'            => $cid,
                        'grpId'            => $data ['grpId'],
                        'coId'            => $data ['coId'],
                        'brId'            => $data ['brId'],



                    ];


                    if ($this->db->insert('categories_details', $data_categories_details)) {

                        //return true;
                    }



            }

            if(isset($this->Settings->isStore) && $this->Settings->isStore==1)
              {

                $this->load->admin_model('shop_admin_model');
                $syncSettings        = $this->shop_admin_model->getShopSettings();


        $url = $syncSettings->base_url.'/api/category/'.$syncSettings->store_id.'/store';
        $uploads_url = base_url('assets/uploads/');

        $total        = $this->shop_admin_model->getCategoriesCount()->total;
        $categories        = $this->shop_admin_model->getCategories();
        $to=0;


foreach ($categories as $cat) {

                $image=$uploads_url."".($cat->image ? ($cat->image!="no_image.png" ? $cat->image : 'placeholder.png') : 'placeholder.png');
                $name_en=(isset($cat->nameEn) ? $cat->nameEn : '');

                $ch = curl_init($url);
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $headers[] = 'Accept:application/json';
                $headers[] = "Authorization: Bearer ".$syncSettings->token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
                $data='{"name": "'.$cat->name.'","image_url": "'.$image.'","name_en": "'.$name_en.'"}';
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


                /* execute request */
                $result = json_decode(curl_exec($ch));
                //$result1 = (curl_exec($ch));


                /* close cURL resource */
                curl_close($ch);


                if($result->data){


                    $dataUpdate = [
				        'shop_id'            => $result->data->id,
			        ];
                    if ($this->shop_admin_model->updateCategory($cat->id,$dataUpdate)) {
                        $to++;
                    }
                }
            }

			  }
            return ['cid' => $cid];
        }
        return false;
    }

    public function addUnitAPI($data)
    {
        if ($this->db->insert('units', $data)) {

            $uid = $this->db->insert_id();
            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {


                $data_units_details = [
                    'unit_id'            => $uid,
                    'grpId'            => $data ['grpId'],
                    'coId'            => $data ['coId'],
                    'brId'            => $data ['brId'],



                ];


                if ($this->db->insert('units_details', $data_units_details)) {

                    //return true;
                }



        }

            return ['uid' => $uid];
        }
        return false;
    }

    public function getUnitByCode($code)
    {
        $q = $this->db->get_where('units', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function checkCurrencyOperation($code)
    {
        $q = $this->db->get_where('sales', ['currency_code' => $code], 1);
        if ($q->num_rows() > 0) {
            return true;
        }else{

		$q = $this->db->get_where('purchases', ['currency_code' => $code], 1);
        if ($q->num_rows() > 0) {
            return true;
        }else{
            if (isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1){


            $q = $this->db->get_where('gl_entries', ['currency_code' => $code], 1);
            if ($q->num_rows() > 0) {
                return true;
            }
            }

        }
    }

        return false;
    }

}
