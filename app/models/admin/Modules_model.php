<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Modules_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }



    public function addModule($data)
    {
        if ($this->db->insert('modules', $data)) {
            return true;
        }
        return false;
    }


    public function getModuleByID($id)
    {
        $q = $this->db->get_where('modules', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function updateModule($id, $data = [])
    {
        if ($this->db->update('modules', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteModule($id)
    {
        if ($this->db->delete('modules', ['id' => $id])) {
            return true;
        }
        return false;
    }



    public function getAllModules()
    {
        $q = $this->db->get('modules');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }





    public function addObject($data)
    {
        if ($this->db->insert('objects', $data)) {
            return true;
        }
        return false;
    }


    public function getObjectByID($id)
    {
        $q = $this->db->get_where('objects', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function updateObject($id, $data = [])
    {
        if ($this->db->update('objects', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteObject($id)
    {
        if ($this->db->delete('objects', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getObjectsByModID($module_id,$type = null)
    {
        $this->db->where('status', 1);
        $this->db->where('module_id', $module_id);
        if($type){
            $this->db->where('type', $type);
        }
        $q = $this->db->get('objects');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }






}