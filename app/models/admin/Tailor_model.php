<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tailor_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


	public function addCollar($data)
    {
        if ($this->db->insert('TR_Collar', $data)) {
            return true;
        }
        return false;
    }

    public function getCollarsByID($Collar_Type_ID)
    {
        $q = $this->db->get_where('TR_Collar', ['Collar_Type_ID' => $Collar_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateCollars($Collar_Type_ID,$data)
    {
        if ($this->db->update('TR_Collar', $data, ['Collar_Type_ID' => $Collar_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteCollar($Collar_Type_ID)
    {
        if ($this->db->delete('TR_Collar', ['Collar_Type_ID' => $Collar_Type_ID])) {
            return true;
        }
        return false;
    }






	public function addCuff($data)
    {
        if ($this->db->insert('TR_Cuff', $data)) {
            return true;
        }
        return false;
    }

    public function getCuffsByID($Cuff_Type_ID)
    {
        $q = $this->db->get_where('TR_Cuff', ['Cuff_Type_ID' => $Cuff_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateCuffs($Cuff_Type_ID,$data)
    {
        if ($this->db->update('TR_Cuff', $data, ['Cuff_Type_ID' => $Cuff_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteCuff($Cuff_Type_ID)
    {
        if ($this->db->delete('TR_Cuff', ['Cuff_Type_ID' => $Cuff_Type_ID])) {
            return true;
        }
        return false;
    }










	public function addFilling($data)
    {
        if ($this->db->insert('TR_Filling', $data)) {
            return true;
        }
        return false;
    }

    public function getFillingsByID($Filling_Type_ID)
    {
        $q = $this->db->get_where('TR_Filling', ['Filling_Type_ID' => $Filling_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateFillings($Filling_Type_ID,$data)
    {
        if ($this->db->update('TR_Filling', $data, ['Filling_Type_ID' => $Filling_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteFilling($Filling_Type_ID)
    {
        if ($this->db->delete('TR_Filling', ['Filling_Type_ID' => $Filling_Type_ID])) {
            return true;
        }
        return false;
    }









	public function addModel($data)
    {
        if ($this->db->insert('TR_Model', $data)) {
            return true;
        }
        return false;
    }

    public function getModelsByID($Model_Type_ID)
    {
        $q = $this->db->get_where('TR_Model', ['Model_Type_ID' => $Model_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateModels($Model_Type_ID,$data)
    {
        if ($this->db->update('TR_Model', $data, ['Model_Type_ID' => $Model_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteModel($Model_Type_ID)
    {
        if ($this->db->delete('TR_Model', ['Model_Type_ID' => $Model_Type_ID])) {
            return true;
        }
        return false;
    }


















	public function addPocket($data)
    {
        if ($this->db->insert('TR_Pocket', $data)) {
            return true;
        }
        return false;
    }

    public function getPocketsByID($Pocket_Type_ID)
    {
        $q = $this->db->get_where('TR_Pocket', ['Pocket_Type_ID' => $Pocket_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updatePockets($Pocket_Type_ID,$data)
    {
        if ($this->db->update('TR_Pocket', $data, ['Pocket_Type_ID' => $Pocket_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deletePocket($Pocket_Type_ID)
    {
        if ($this->db->delete('TR_Pocket', ['Pocket_Type_ID' => $Pocket_Type_ID])) {
            return true;
        }
        return false;
    }







	public function addTailor($data)
    {
        if ($this->db->insert('TR_tailor', $data)) {
            return true;
        }
        return false;
    }

    public function getTailorsByID($tailor_id )
    {
        $q = $this->db->get_where('TR_tailor', ['tailor_id ' => $tailor_id ], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateTailors($tailor_id,$data)
    {
        if ($this->db->update('TR_tailor', $data, ['tailor_id' => $tailor_id])) {
            return true;
        }
        return false;
    }

    public function deleteTailor($tailor_id)
    {
        if ($this->db->delete('TR_tailor', ['tailor_id' => $tailor_id])) {
            return true;
        }
        return false;
    }








	public function addZipper($data)
    {
        if ($this->db->insert('TR_Zipper', $data)) {
            return true;
        }
        return false;
    }

    public function getZippersByID($Zipper_Type_ID)
    {
        $q = $this->db->get_where('TR_Zipper', ['Zipper_Type_ID' => $Zipper_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateZippers($Zipper_Type_ID,$data)
    {
        if ($this->db->update('TR_Zipper', $data, ['Zipper_Type_ID' => $Zipper_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteZipper($Zipper_Type_ID)
    {
        if ($this->db->delete('TR_Zipper', ['Zipper_Type_ID' => $Zipper_Type_ID])) {
            return true;
        }
        return false;
    }



    public function addSleeve($data)
    {
        if ($this->db->insert('TR_Sleeve', $data)) {
            return true;
        }
        return false;
    }

    public function getSleevesByID($Sleeve_Type_ID)
    {
        $q = $this->db->get_where('TR_Sleeve', ['Sleeve_Type_ID' => $Sleeve_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateSleeves($Sleeve_Type_ID,$data)
    {
        if ($this->db->update('TR_Sleeve', $data, ['Sleeve_Type_ID' => $Sleeve_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteSleeve($Sleeve_Type_ID)
    {
        if ($this->db->delete('TR_Sleeve', ['Sleeve_Type_ID' => $Sleeve_Type_ID])) {
            return true;
        }
        return false;
    }


    public function addChest_Pocket($data)
    {
        if ($this->db->insert('TR_Chest_Pocket', $data)) {
            return true;
        }
        return false;
    }

    public function getChest_PocketsByID($Chest_Pocket_Type_ID)
    {
        $q = $this->db->get_where('TR_Chest_Pocket', ['Chest_Pocket_Type_ID' => $Chest_Pocket_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateChest_Pockets($Chest_Pocket_Type_ID,$data)
    {
        if ($this->db->update('TR_Chest_Pocket', $data, ['Chest_Pocket_Type_ID' => $Chest_Pocket_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteChest_Pocket($Chest_Pocket_Type_ID)
    {
        if ($this->db->delete('TR_Chest_Pocket', ['Chest_Pocket_Type_ID' => $Chest_Pocket_Type_ID])) {
            return true;
        }
        return false;
    }





    public function addChest_Pocket_Padding($data)
    {
        if ($this->db->insert('TR_Chest_Pocket_Padding', $data)) {
            return true;
        }
        return false;
    }

    public function getChest_Pocket_PaddingsByID($Chest_Pocket_Padding_Type_ID)
    {
        $q = $this->db->get_where('TR_Chest_Pocket_Padding', ['Chest_Pocket_Padding_Type_ID' => $Chest_Pocket_Padding_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateChest_Pocket_Paddings($Chest_Pocket_Padding_Type_ID,$data)
    {
        if ($this->db->update('TR_Chest_Pocket_Padding', $data, ['Chest_Pocket_Padding_Type_ID' => $Chest_Pocket_Padding_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteChest_Pocket_Padding($Chest_Pocket_Padding_Type_ID)
    {
        if ($this->db->delete('TR_Chest_Pocket_Padding', ['Chest_Pocket_Padding_Type_ID' => $Chest_Pocket_Padding_Type_ID])) {
            return true;
        }
        return false;
    }

    public function addZipper_Padding($data)
    {
        if ($this->db->insert('TR_Zipper_Padding', $data)) {
            return true;
        }
        return false;
    }

    public function getZipper_PaddingsByID($Zipper_Padding_Type_ID)
    {
        $q = $this->db->get_where('TR_Zipper_Padding', ['Zipper_Padding_Type_ID' => $Zipper_Padding_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateZipper_Paddings($Zipper_Padding_Type_ID,$data)
    {
        if ($this->db->update('TR_Zipper_Padding', $data, ['Zipper_Padding_Type_ID' => $Zipper_Padding_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteZipper_Padding($Zipper_Padding_Type_ID)
    {
        if ($this->db->delete('TR_Zipper_Padding', ['Zipper_Padding_Type_ID' => $Zipper_Padding_Type_ID])) {
            return true;
        }
        return false;
    }

    public function addEmbroidery($data)
    {
        if ($this->db->insert('TR_Embroidery', $data)) {
            return true;
        }
        return false;
    }

    public function getEmbroiderysByID($Embroidery_Type_ID)
    {
        $q = $this->db->get_where('TR_Embroidery', ['Embroidery_Type_ID' => $Embroidery_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateEmbroiderys($Embroidery_Type_ID,$data)
    {
        if ($this->db->update('TR_Embroidery', $data, ['Embroidery_Type_ID' => $Embroidery_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteEmbroidery($Embroidery_Type_ID)
    {
        if ($this->db->delete('TR_Embroidery', ['Embroidery_Type_ID' => $Embroidery_Type_ID])) {
            return true;
        }
        return false;
    }


    public function addButtons($data)
    {
        if ($this->db->insert('TR_Buttons', $data)) {
            return true;
        }
        return false;
    }

    public function getButtonssByID($Buttons_Type_ID)
    {
        $q = $this->db->get_where('TR_Buttons', ['Buttons_Type_ID' => $Buttons_Type_ID], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateButtonss($Buttons_Type_ID,$data)
    {
        if ($this->db->update('TR_Buttons', $data, ['Buttons_Type_ID' => $Buttons_Type_ID])) {
            return true;
        }
        return false;
    }

    public function deleteButtons($Buttons_Type_ID)
    {
        if ($this->db->delete('TR_Buttons', ['Buttons_Type_ID' => $Buttons_Type_ID])) {
            return true;
        }
        return false;
    }



}
