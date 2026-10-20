<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class Runner extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
		if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        
        $this->load->library('reportico');
    }

    public function index()
    {
        //$this->sma->print_arrays($_POST);
		//$this->reportico->reportico_ajax_mode = true;
        //$this->reportico->reportico_ajax_script_url = $_SERVER["SCRIPT_NAME"] . '/runner';
        //$this->reportico->execute();
    }
}

?>
