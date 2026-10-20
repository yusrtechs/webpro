<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tailer_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countTR_Measurement($filters = [])
    {

        $this->db->from('TR_Measurement');
        return $this->db->count_all_results();
    }


	public function countTR_tailor($filters = [])
    {

        $this->db->from('TR_tailor');
        return $this->db->count_all_results();
    }

	public function countTR_Filling($filters = [])
    {

        $this->db->from('TR_Filling');
        return $this->db->count_all_results();
    }


    public function countTR_Collar($filters = [])
    {

        $this->db->from('TR_Collar');
        return $this->db->count_all_results();
    }

    public function countTR_Cuff($filters = [])
    {

        $this->db->from('TR_Cuff');
        return $this->db->count_all_results();
    }


	public function countTR_Model($filters = [])
    {

        $this->db->from('TR_Model');
        return $this->db->count_all_results();
    }


    public function countTR_Pocket($filters = [])
    {

        $this->db->from('TR_Pocket');
        return $this->db->count_all_results();
    }

    public function countTR_Chest_Pocket($filters = [])
    {

        $this->db->from('TR_Chest_Pocket');
        return $this->db->count_all_results();
    }

    public function countTR_Chest_Pocket_Padding($filters = [])
    {

        $this->db->from('TR_Chest_Pocket_Padding');
        return $this->db->count_all_results();
    }

	public function countTR_Zipper($filters = [])
    {

        $this->db->from('TR_Zipper');
        return $this->db->count_all_results();
    }

    public function countTR_Zipper_Padding($filters = [])
    {

        $this->db->from('TR_Zipper_Padding');
        return $this->db->count_all_results();
    }

    public function countTR_Embroidery($filters = [])
    {

        $this->db->from('TR_Embroidery');
        return $this->db->count_all_results();
    }

    public function countTR_Buttons($filters = [])
    {

        $this->db->from('TR_Buttons');
        return $this->db->count_all_results();
    }

    public function countTR_Sleeve($filters = [])
    {

        $this->db->from('TR_Sleeve');
        return $this->db->count_all_results();
    }

    public function getWarehouse($filters)
    {
        if (!empty($warehouses = $this->getSettings($filters))) {
            return array_values($warehouses)[0];
        }
        return false;
    }

    public function getTR_Measurement($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("*,CONCAT('{$uploads_url}', logo) as logo_image_url");
		$this->db->select("*");

        return $this->db->get('TR_Measurement')->result();
    }

    public function getTR_tailor($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        //$this->db->select("*,CONCAT('{$uploads_url}', logo) as logo_image_url");
		$this->db->select("*");

        return $this->db->get('TR_tailor')->result();
    }

    public function getTR_Filling($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Filling_Image) as Filling_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Filling')->result();
    }

    public function getTR_Collar($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Collar_Image) as Collar_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Collar')->result();
    }

    public function getTR_Cuff($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Cuff_Image) as Cuff_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Cuff')->result();
    }

    public function getTR_Model($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Model_Image) as Model_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Model')->result();
    }

    public function getTR_Pocket($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Pocket_Image) as Pocket_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Pocket')->result();
    }

    public function getTR_Chest_Pocket($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Chest_Pocket_Image) as Chest_Pocket_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Chest_Pocket')->result();
    }

    public function getTR_Chest_Pocket_Padding($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Chest_Pocket_Padding_Image) as Chest_Pocket_Padding_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Chest_Pocket_Padding')->result();
    }

    public function getTR_Zipper($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Zipper_Image) as Zipper_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Zipper')->result();
    }

    public function getTR_Zipper_Padding($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Zipper_Padding_Image) as Zipper_Padding_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Zipper_Padding')->result();
    }

    public function getTR_Embroidery($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Embroidery_Image) as Embroidery_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Embroidery')->result();
    }

    public function getTR_Buttons($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Buttons_Image) as Buttons_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Buttons')->result();
    }

    public function getTR_Sleeve($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("*,CONCAT('{$uploads_url}', Sleeve_Image) as Sleeve_Image_url");
		//$this->db->select("*");

        return $this->db->get('TR_Sleeve')->result();
    }

    public function addWarehouse_temp($data = [])
    {
        if ($this->db->insert('warehouses_temp', $data)) {
		//if ($this->db->insert('warehouses_temp', $data)) {
            return true;
        }
        return false;
    }

    public function getTR_ModelByID($Model_Type_ID)
    {

        //$this->db->select("*,CONCAT('{$uploads_url}', logo) as logo_image_url");
		$this->db->select("*");
        $this->db->where('Model_Type_ID', $Model_Type_ID);

        return $this->db->get('TR_Model')->result();
    }

    public function updateTR_Model($Model_Type_ID,$Model_Name)
    {

            if ($this->db->update('TR_Model', ['Model_Name' => $Model_Name], ['Model_Type_ID' => $Model_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Model($Model_Name)
    {

            if ($this->db->insert('TR_Model', ['Model_Name' => $Model_Name])) {
                $Model_Type_ID = $this->db->insert_id();
                return $Model_Type_ID;
            }

        return false;
    }


    public function updateTR_Collar($Collar_Type_ID,$Collar_Name)
    {

            if ($this->db->update('TR_Collar', ['Collar_Name' => $Collar_Name], ['Collar_Type_ID' => $Collar_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Collar($Collar_Name)
    {

            if ($this->db->insert('TR_Collar', ['Collar_Name' => $Collar_Name])) {
                $Collar_Type_ID = $this->db->insert_id();
                return $Collar_Type_ID;
            }

        return false;
    }

    public function updateTR_Cuff($Cuff_Type_ID,$Cuff_Name)
    {

            if ($this->db->update('TR_Cuff', ['Cuff_Name' => $Cuff_Name], ['Cuff_Type_ID' => $Cuff_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Cuff($Cuff_Name)
    {

            if ($this->db->insert('TR_Cuff', ['Cuff_Name' => $Cuff_Name])) {
                $Cuff_Type_ID = $this->db->insert_id();
                return $Cuff_Type_ID;
            }

        return false;
    }



    public function updateTR_Filling($Filling_Type_ID,$Filling_Name)
    {

            if ($this->db->update('TR_Filling', ['Filling_Name' => $Filling_Name], ['Filling_Type_ID' => $Filling_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Filling($Filling_Name)
    {

            if ($this->db->insert('TR_Filling', ['Filling_Name' => $Filling_Name])) {
                $Filling_Type_ID = $this->db->insert_id();
                return $Filling_Type_ID;
            }

        return false;
    }



    public function updateTR_Pocket($Pocket_Type_ID,$Pocket_Name)
    {

            if ($this->db->update('TR_Pocket', ['Pocket_Name' => $Pocket_Name], ['Pocket_Type_ID' => $Pocket_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Pocket($Pocket_Name)
    {

            if ($this->db->insert('TR_Pocket', ['Pocket_Name' => $Pocket_Name])) {
                $Pocket_Type_ID = $this->db->insert_id();
                return $Pocket_Type_ID;
            }

        return false;
    }



    public function updateTR_Chest_Pocket($Chest_Pocket_Type_ID,$Chest_Pocket_Name)
    {

            if ($this->db->update('TR_Chest_Pocket', ['Chest_Pocket_Name' => $Chest_Pocket_Name], ['Chest_Pocket_Type_ID' => $Chest_Pocket_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Chest_Pocket($Chest_Pocket_Name)
    {

            if ($this->db->insert('TR_Chest_Pocket', ['Chest_Pocket_Name' => $Chest_Pocket_Name])) {
                $Chest_Pocket_Type_ID = $this->db->insert_id();
                return $Chest_Pocket_Type_ID;
            }

        return false;
    }





    public function updateTR_Chest_Pocket_Padding($Chest_Pocket_Padding_Type_ID,$Chest_Pocket_Padding_Name)
    {

            if ($this->db->update('TR_Chest_Pocket_Padding', ['Chest_Pocket_Padding_Name' => $Chest_Pocket_Padding_Name], ['Chest_Pocket_Padding_Type_ID' => $Chest_Pocket_Padding_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Chest_Pocket_Padding($Chest_Pocket_Padding_Name)
    {

            if ($this->db->insert('TR_Chest_Pocket_Padding', ['Chest_Pocket_Padding_Name' => $Chest_Pocket_Padding_Name])) {
                $Chest_Pocket_Padding_Type_ID = $this->db->insert_id();
                return $Chest_Pocket_Padding_Type_ID;
            }

        return false;
    }


    public function updateTR_Zipper($Zipper_Type_ID,$Zipper_Name)
    {

            if ($this->db->update('TR_Zipper', ['Zipper_Name' => $Zipper_Name], ['Zipper_Type_ID' => $Zipper_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Zipper($Zipper_Name)
    {

            if ($this->db->insert('TR_Zipper', ['Zipper_Name' => $Zipper_Name])) {
                $Zipper_Type_ID = $this->db->insert_id();
                return $Zipper_Type_ID;
            }

        return false;
    }


    public function updateTR_Zipper_Padding($Zipper_Padding_Type_ID,$Zipper_Padding_Name)
    {

            if ($this->db->update('TR_Zipper_Padding', ['Zipper_Padding_Name' => $Zipper_Padding_Name], ['Zipper_Padding_Type_ID' => $Zipper_Padding_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Zipper_Padding($Zipper_Padding_Name)
    {

            if ($this->db->insert('TR_Zipper_Padding', ['Zipper_Padding_Name' => $Zipper_Padding_Name])) {
                $Zipper_Padding_Type_ID = $this->db->insert_id();
                return $Zipper_Padding_Type_ID;
            }

        return false;
    }




    public function updateTR_Embroidery($Embroidery_Type_ID,$Embroidery_Name)
    {

            if ($this->db->update('TR_Embroidery', ['Embroidery_Name' => $Embroidery_Name], ['Embroidery_Type_ID' => $Embroidery_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Embroidery($Embroidery_Name)
    {

            if ($this->db->insert('TR_Embroidery', ['Embroidery_Name' => $Embroidery_Name])) {
                $Embroidery_Type_ID = $this->db->insert_id();
                return $Embroidery_Type_ID;
            }

        return false;
    }




    public function updateTR_Buttons($Buttons_Type_ID,$Buttons_Name)
    {

            if ($this->db->update('TR_Buttons', ['Buttons_Name' => $Buttons_Name], ['Buttons_Type_ID' => $Buttons_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Buttons($Buttons_Name)
    {

            if ($this->db->insert('TR_Buttons', ['Buttons_Name' => $Buttons_Name])) {
                $Buttons_Type_ID = $this->db->insert_id();
                return $Buttons_Type_ID;
            }

        return false;
    }


    public function updateTR_Sleeve($Sleeve_Type_ID,$Sleeve_Name)
    {

            if ($this->db->update('TR_Sleeve', ['Sleeve_Name' => $Sleeve_Name], ['Sleeve_Type_ID' => $Sleeve_Type_ID])) {
                return true;
            }

        return false;
    }


    public function insertTR_Sleeve($Sleeve_Name)
    {

            if ($this->db->insert('TR_Sleeve', ['Sleeve_Name' => $Sleeve_Name])) {
                $Sleeve_Type_ID = $this->db->insert_id();
                return $Sleeve_Type_ID;
            }

        return false;
    }



}
