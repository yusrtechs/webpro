<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->admin_model('cron_model');
        $this->Settings = $this->cron_model->getSettings();
    }

    public function index()
    {
        show_404();
    }

    public function run()
    {
        //ini_set('display_errors', 1);
        if ($m = $this->cron_model->run_cronV2()) {
            //$this->sma->print_arrays($m);
            if ($this->input->is_cli_request()) {
                foreach ($m as $msg) {
                    echo $msg . "\n";
                }
            } else {
                echo '<!doctype html><html><head><title>Cron Job</title><style>p{background:#F5F5F5;border:1px solid #EEE; padding:15px;}</style></head><body>';
                echo '<p>Corn job successfully run.</p>';
                foreach ($m as $msg) {
                    echo '<p>' . $msg . '</p>';
                }
                echo '</body></html>';
            }
        }
    }

    public function downloadXMLUBL($file)
    {
        if (file_exists('./assets/xml/' . $file)) {
            $this->load->helper('download');
            force_download('./assets/xml/' . $file, null);
            exit();
        }
        $this->session->set_flashdata('error', lang('file_x_exist'));
        redirect($_SERVER['HTTP_REFERER']);
    }
}
