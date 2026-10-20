<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Agents_model extends CI_Model
{
    /**
     * This function is used to add new user to system
     * @return number $insert_id : This is last inserted id
     */
    function addNewAgent($AgentInfo)
    {
        $this->db->trans_start();
        $this->db->insert('agents', $AgentInfo);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }

    function getAgents()
    {

        $this->db->select('*');
        $this->db->from('agents');
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function used to get user information by id
     * @param number $userId : This is user id
     * @return array $result : This is user information
     */
    function getAgentInfo($id)
    {
        $this->db->select('*');
        $this->db->from('agents');
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row();
    }


    /**
     * This function is used to update the user information
     * @param array $userInfo : This is users updated information
     * @param number $userId : This is user id
     */
    function editAgent($AgentInfo, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('agents', $AgentInfo);

        return TRUE;
    }

    function getAgentsWithFilters()
    {
        $this->db->select('agents.*,count(client.id) as clients_total');
        $this->db->from('agents');
        $this->db->join('client', 'client.agent_id = agents.id', 'left');
        $this->db->group_by('agents.id');
        $this->db->order_by('agents.created_at DESC');
        $query = $this->db->get();
        return $query->result();
    }

    function getAgentSubscriber($id)
    {
        $this->db->select('client.*');
        $this->db->from('agents');
        $this->db->join('client', 'client.agent_id = agents.id', 'inner');
        $this->db->where('agents.id', $id);

        $query = $this->db->get();
        return $query->result();
    }
}
