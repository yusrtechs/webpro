<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Samet Aydın / sametay153@gmail.com
 * @version : 1.0
 * @since : 27.02.2018
 */
class Agents extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('client_model');
        $this->load->model('agents_model');
        $this->isLoggedIn();
    }

    public function index()
    {
        $process = 'Agents';
        $processFunction = 'agents/list';

        $data['agentsRecords'] = $this->agents_model->getAgentsWithFilters($_POST);

        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Agents List';

        $this->loadViews("agents", $this->global, $data, NULL);
    }

    public function addNew()
    {
        $data['roles'] = $this->user_model->getUserRoles();

        $this->global['pageTitle'] = 'ArkanPro : Add Agents';

        $this->loadViews("addNewAgents", $this->global, $data, NULL);
    }

    public function newAgentProcess()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
        $this->form_validation->set_rules('phone', 'Phone Number', 'required|min_length[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->addNew();
        } else {
            $name = ucwords(strtolower($this->security->xss_clean($this->input->post('name'))));
            $email = $this->security->xss_clean($this->input->post('email'));
            $phone = $this->security->xss_clean($this->input->post('phone'));

            $agentInfo = array(
                'email' => $email,
                'name' => $name,
                'phone' => $phone,
                'logo' => null,
                'address' => $_POST['address'],
                'created_at' => date('Y-m-d H:i:s'),
                'country_code' => $this->input->post('country_code'),
                'city_code' => $this->input->post('city_code')
            );

            $result = $this->agents_model->addNewAgent($agentInfo);

            if ($result > 0) {
                $process = 'Add agents';
                $processFunction = 'agents/addNewAgent';
                $this->logrecord($process, $processFunction);

                $this->session->set_flashdata('success', 'Agent successfully created');
            } else {
                $this->session->set_flashdata('error', 'Agent creation failed');
            }

            redirect('agents');
        }
    }

    function edit($id)
    {
        $process = 'Agents';
        $processFunction = 'agents/edit';

        $data['agent_info'] = $this->agents_model->getAgentInfo($id);
        if (is_null($data['agent_info']))
            redirect('agents');
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Agents Edit';

        $this->loadViews("agents_edit", $this->global, $data, NULL);
    }

    function updateAgentProcess()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
        $this->form_validation->set_rules('phone', 'Phone Number', 'required|min_length[10]');


        if ($this->form_validation->run() == FALSE) {
            $this->edit($_POST['agent_id']);
        } else {
            $name = ucwords(strtolower($this->security->xss_clean($this->input->post('name'))));
            $email = $this->security->xss_clean($this->input->post('email'));
            $phone = $this->security->xss_clean($this->input->post('phone'));

            $agentInfo = array(
                'email' => $email,
                'name' => $name,
                'phone' => $phone,
                'logo' => null,
                'address' => $_POST['address'],
                'country_code' => $this->input->post('country_code'),
                'city_code' => $this->input->post('city_code')
            );

            $result = $this->agents_model->editAgent($agentInfo, $_POST['agent_id']);

            if ($result > 0) {
                $process = 'Edit agents';
                $processFunction = 'agents/EditAgent';
                $this->logrecord($process, $processFunction);

                $this->session->set_flashdata('success', 'Agent successfully created');
            } else {
                $this->session->set_flashdata('error', 'Agent creation failed');
            }

            redirect('agents');
        }
    }

    function agentHistory($id)
    {
        $process = 'Agents';
        $processFunction = 'agents/subscriber';

        $data['agent_info'] = $this->agents_model->getAgentInfo($id);
        if (is_null($data['agent_info']))
            redirect('agents');

        $data['agent_subscriber'] = $this->client_model->getClientsWithFilters(['agent_id' => $id]);

        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : Agents List';

        $this->loadViews("agents_subscriber", $this->global, $data, NULL);
    }
}
