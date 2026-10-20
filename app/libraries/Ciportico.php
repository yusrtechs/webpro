<?php
defined('BASEPATH') || exit;
/**
 * Reportico for CodeIgniter
 *
 * Based on Reportico 4
 *
 * @author vikseriq
 * @license MIT
 */

$reportico_file = __DIR__.'/reportico/reportico.php';
if (!file_exists($reportico_file))
	exit('Reportico library not installed');
include_once $reportico_file;

/**
 * Class Ciportico
 *
 * Reportico wrapper for CodeIgniter 2.x/3.x
 *
 * For embedding reports into pages use
 * $this->ciportico->reportico->embedded_report = true;
 */
class Ciportico {

	/**
	 * CI Singleton
	 * @var CI_Controller
	 */
	protected $CI;

	/**
	 * Reportico engine
	 * @var reportico
	 */
	public $reportico;

	public function __construct($config = array()){
		$this->CI =& get_instance();
		$this->CI->load->helper('url');
		$base_url = base_url($config['ciportico_base']);

		$this->reportico = new Reportico();
		$config['reportico_url_path'] = $base_url;
		$config['reportico_ajax_script_url'] = $base_url;
		$config['url_path_to_calling_script'] = $base_url;
		$config['url_path_to_assets'] = base_url($config['url_path_to_assets']);
		$config['templates_folder'] = APPPATH.$config['templates_folder'];
		$config['compiled_templates_folder'] = APPPATH.$config['compiled_templates_folder'];

		foreach ($config as $key => $val){
			if (isset($this->reportico->$key)){
				$method = 'set_'.$key;
				if (method_exists($this, $method)){
					$this->reportico->$method($val);
				} else {
					$this->reportico->$key = $val;
				}
			}
		}
	}

}