<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cars_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }



    public function addBrand($data)
    {
        if ($this->db->insert('cars_brands', $data)) {
            return true;
        }
        return false;
    }


    public function getBrandByID($id)
    {
        $q = $this->db->get_where('cars_brands', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function updateBrand($id, $data = [])
    {
        if ($this->db->update('cars_brands', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteBrand($id)
    {
        if ($this->db->delete('cars_brands', ['id' => $id])) {
            return true;
        }
        return false;
    }



    public function getAllBrands()
    {
        $q = $this->db->get('cars_brands');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }









    public function addModel($data)
    {
        if ($this->db->insert('cars_models', $data)) {
            return true;
        }
        return false;
    }



    public function getModelByID($id)
    {
        $q = $this->db->get_where('cars_models', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function updateModel($id, $data = [])
    {
        if ($this->db->update('cars_models', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }


    public function deleteModel($id)
    {
        if ($this->db->delete('cars_models', ['id' => $id])) {
            return true;
        }
        return false;
    }


    public function getAllModels()
    {
        $q = $this->db->get('cars_models');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }




    public function addCategory($data)
    {
        if ($this->db->insert('cars_categories', $data)) {
            return true;
        }
        return false;
    }





    public function getCategoryByID($id)
    {
        $q = $this->db->get_where('cars_categories', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function updateCategory($id, $data = [])
    {
        if ($this->db->update('cars_categories', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }


    public function deleteCategory($id)
    {
        if ($this->db->delete('cars_categories', ['id' => $id])) {
            return true;
        }
        return false;
    }






    public function getModelsByBrandId($brand_id)
    {
        $this->db->select('id as id, model_name as text')
        ->where('brand_id', $brand_id);//->order_by('model_name');
        $q = $this->db->get('cars_models');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCategoryByBrandIdModelId($brand_id,$model_id)
    {
        $this->db->select('id as id, categ_name as text')
        ->where('brand_id', $brand_id)->where('model_id', $model_id);//->order_by('model_name');
        $q = $this->db->get('cars_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function addVehicleMainData($data)
    {
        if ($this->db->insert('cars_vehicles', $data)) {
            $vehicle_id = $this->db->insert_id();
            $this->db->insert('warehouses_products', ['product_id' => $vehicle_id, 'warehouse_id' => $data['showroom_id'], 'avg_cost' => $data['cost'] , 'quantity' => 1]);


            return $vehicle_id;
        }
        return false;
    }

    public function addVehicleMainDataFPO($data)
    {
        if ($this->db->insert('cars_vehicles', $data)) {
            $vehicle_id = $this->db->insert_id();
            return $this->getVehicleByID($vehicle_id);

        }
        return false;
    }

    public function updateVehicleMainData($vehicle_id, $data = [])
    {
        if ($this->db->update('cars_vehicles', $data, ['vehicle_id' => $vehicle_id])) {
            $this->db->update('warehouses_products', ['avg_cost' => $data['cost']],['product_id' => $vehicle_id, 'warehouse_id' => $data['showroom_id']]);

            return true;
        }
        return false;
    }

    public function updateVehicleFeatureByAjax($vehicle_id, $data = [])
    {
        if ($this->db->update('cars_vehicles', $data, ['vehicle_id' => $vehicle_id])) {
            return true;
        }
        return false;
    }


    public function getAllVehicles()
    {
        $this->db->select('cars_vehicles.*, cars_showrooms.name as showroom_name, cars_brands.brand_name as brand_name, cars_models.model_name as model_name, cars_categories.categ_name as categ_name')
            ->join('cars_showrooms', 'cars_showrooms.id=cars_vehicles.showroom_id', 'left')
            ->join('cars_brands', 'cars_brands.id=cars_vehicles.brand_id', 'left')
            ->join('cars_models', 'cars_models.id=cars_vehicles.model_id', 'left')
            ->join('cars_categories', 'cars_categories.id=cars_vehicles.category_id', 'left');
            //->group_by('cars_vehicles.id')
            //->order_by('cars_vehicles.id', 'asc');
	    $q = $this->db->get('cars_vehicles');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getVehicleByID($vehicle_id)
    {
        $q = $this->db->get_where('cars_vehicles', ['vehicle_id' => $vehicle_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }




    public function deleteVehicle($vehicle_id)
    {
        if ($this->db->delete('cars_vehicles', ['vehicle_id' => $vehicle_id])) {
            return true;
        }
        return false;
    }



    // ----------------------------------------   Feature  -------------------------

    public function addFeature($data)
    {
        if ($this->db->insert('cars_features', $data)) {
            return true;
        }
        return false;
    }


    public function getFeatureByID($id)
    {
        $q = $this->db->get_where('cars_features', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
      public function updateFeature($id, $data = [])
    {
        if ($this->db->update('cars_features', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteFeature($id)
    {
        if ($this->db->delete('cars_features', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getFeatureByType($type)
    {
        $q = $this->db->get_where('cars_features', ['type' => $type]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCarOwnerSuggestions($term, $limit = 10)
    {
        $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as value, phone", false);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', ['group_name' => 'carowner'], $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }


//-------------------------showroom -----------------------


public function getShowroomByID($id)
{
    $q = $this->db->get_where('cars_showrooms', ['id' => $id], 1);
    if ($q->num_rows() > 0) {
        return $q->row();
    }
    return false;
}


public function addShowroom($data)
{

    $dataSup = [
        'company'   => $data['name'],
        'name'   => $data['name'],
        'group_id'    => '4',
        'group_name'  => 'supplier',
        'co_type'  => '1',


       ];

       if ($this->db->insert('companies', $dataSup)) {
        $default_supplier = $this->db->insert_id();
       }
       $data['default_supplier']=$default_supplier;
    if ($this->db->insert('cars_showrooms', $data)) {
        return true;
    }
    return false;
}

public function addShowroom_temp($data)
{
    //if ($this->db->insert('warehouses_temp', ['code' => $data])) {
    if ($this->db->insert('showrooms_temp', $data)) {
        return true;
    }
    return false;
}

public function getShowroomAutoCode()
{

    $this->db->select('COALESCE(max(id+1),1) as id');

    $q = $this->db->get('cars_showrooms');
    if ($q->num_rows() > 0) {
        return $q->row();
    }
    return false;
}



public function getAllShowrooms()
{
    $q = $this->db->get('cars_showrooms');
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
            $data[] = $row;
        }
        return $data;
    }
    return false;
}



public function updateShowroom($id, $data = [])
    {
        //$this->db->where('id', $id);

        $this->db->update('companies', ['company' => $data['name']], ['id' => $this->getShowroomByID($id)->default_supplier]);


        if ($this->db->update('cars_showrooms', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteShowroom($id)
{
    if ($this->db->delete('cars_showrooms', ['id' => $id]) && $this->db->delete('cars_vehicles', ['showroom_id' => $id])) {
        return true;
    }
    return false;
}



// ----------------------------------Services -----------------


public function addService($data)
{
    if ($this->db->insert('cars_services', $data)) {
        return true;
    }
    return false;
}




public function getServiceByID($id)
{
    $q = $this->db->get_where('cars_services', ['id' => $id], 1);
    if ($q->num_rows() > 0) {
        return $q->row();
    }
    return false;
}



public function updateService($id, $data = [])
    {
        if ($this->db->update('cars_services', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }



public function deleteService($id)
{
    if ($this->db->delete('cars_services', ['id' => $id])) {
        return true;
    }
    return false;
}

public function getVehiclesByChesis($chesis_no)
    {
        $q = $this->db->get_where('cars_vehicles', ['chesis_no' => $chesis_no], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTaxRateByCode($code)
    {
        $q = $this->db->get_where('tax_rates', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getCarsExpenses($vehicle_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('expenses', ['vehicle_id' => $vehicle_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getAllWarehousesWithPQ($product_id)
    {
        /*$this->db->select('' . $this->db->dbprefix('warehouses') . '.id as id1,' . $this->db->dbprefix('warehouses') . '.name as name,' . $this->db->dbprefix('warehouses') . '.code as code, ' . $this->db->dbprefix('warehouses_products') . '.quantity as quantity, ' . $this->db->dbprefix('warehouses_products') . '.rack as rack, '. $this->db->dbprefix('warehouses_products') . '.avg_cost as avg_cost,'.$this->db->dbprefix('warehouses_products') . '.id as id2')
        //$this->db->select('warehouses.id,warehouses.name,warehouses.code, warehouses_products.quantity, warehouses_products.rack, warehouses_products.avg_cost')
        //$this->db->select('warehouses.*, warehouses_products.id')
          //$this->db->select('*')
            ->join('warehouses_products', 'warehouses_products.warehouse_id=warehouses.id', 'left')
            ->where('warehouses_products.product_id', $product_id)
            ->group_by('warehouses.id');
        $q = $this->db->get('warehouses');*/

		$myQuery = "SELECT w.*,wp.quantity,wp.rack,wp.avg_cost from  " . $this->db->dbprefix('cars_showrooms') . " w," . $this->db->dbprefix('warehouses_products') . " wp where wp.warehouse_id=w.id and wp.product_id='".$product_id."'";
        $q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function updateStatus($vehicle_id, $status)
    {
        if ($this->db->update('cars_vehicles', ['status' => $status], ['vehicle_id' => $vehicle_id])) {
            return true;
        }
        return false;
    }


}