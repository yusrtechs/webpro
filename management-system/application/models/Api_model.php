<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->config('rest');
		$this->restdb = $this->load->database($this->config->item('rest_database_group'), true);

    }


    public function addApiKey($data)
    {
        return $this->restdb->insert('api_keys', $data);
    }

    public function deleteApiKey($id)
    {
        return $this->db->delete('api_keys', ['id' => $id]);
    }

    public function generateKey()
    {
        return $this->_generate_key();
    }

    public function getApiKey($value, $field = 'key')
    {
        return  $this->db->get_where('api_keys', [$field => $value])->row();
    }

    public function getApiKeys()
    {
        return $this->db->get('api_keys')->result();
    }

	public function getCountofKey($company)
    {
        $this->restdb->select('(COALESCE(count(*), 0) + 1) as count', false)
            ->where('company', $company);
        $q = $this->restdb->get('api_keys');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getApiKeysByCompany($company)
    {
		$this->restdb->select('reference,key,company,is_private_key,date_created');
        $q=$this->restdb->get_where('api_keys',['company' =>$company]);
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;

            }
            return $data;
        }
        return false;
    }

    public function updateSynchApiKey($key, $is_private_key)
    {
        return $this->restdb->update('api_keys', ['is_private_key' => $is_private_key], ['key' => $key]);
    }

    public function getUser($value, $field = 'id')
    {
        $q = $this->db->get_where('users', [$field => $value]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateUserApiKey($user_id, $data)
    {
        return $this->db->update('api_keys', $data, ['user_id' => $user_id]);
    }

    private function _delete_key($key)
    {
        return $this->db
            ->where($this->config->item('rest_key_column'), $key)
            ->delete($this->config->item('rest_keys_table'));
    }

    private function _generate_key()
    {
        do {
            $salt = base_convert(bin2hex($this->security->get_random_bytes(64)), 16, 36);

            if ($salt === false) {
                $salt = hash('sha256', time() . mt_rand());
            }

            $new_key = substr($salt, 0, $this->config->item('rest_key_length'));
        } while ($this->_key_exists($new_key));

        return $new_key;
    }

    private function _get_key($key)
    {
        return $this->db
            ->where($this->config->item('rest_key_column'), $key)
            ->get($this->config->item('rest_keys_table'))
            ->row();
    }

    private function _insert_key($key, $data)
    {
        $data[$this->config->item('rest_key_column')] = $key;
        $data['date_created']                         = function_exists('now') ? now() : time();

        return $this->db
            ->set($data)
            ->insert($this->config->item('rest_keys_table'));
    }

    private function _key_exists($key)
    {
        return $this->restdb
            ->where($this->config->item('rest_key_column'), $key)
            ->count_all_results($this->config->item('rest_keys_table')) > 0;
    }

    private function _update_key($key, $data)
    {
        return $this->db
            ->where($this->config->item('rest_key_column'), $key)
            ->update($this->config->item('rest_keys_table'), $data);
    }
    
    public function getLatestLog($key)
    {
        $this->restdb->select("time,warehouse_code", false)
        ->where('api_key', $key)
        ->where('method', 'get');
        $this->restdb->order_by('id', 'DESC');
        $this->restdb->limit(1);
        $q = $this->restdb->get('api_logs');

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    
    public function getCountofKeyV2($company)
    {
        $this->restdb->select('(COALESCE(count(*), 0)) as count', false)
            ->where('company', $company)->where('is_private_key', 0);
        $q = $this->restdb->get('api_keys');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
}
