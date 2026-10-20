<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
 *  ==============================================================================
 *  Author  : Mian Saleem
 *  Email   : saleem@tecdiary.com
 *  For     : SMS Lib
 *  ==============================================================================
 */

class Sms
{
    private $settings;
    private $sms_settings;

    public function __construct()
    {
        $this->sms_settings         = $this->site->getSmsSettings();
        $this->lang->admin_load('sms', $this->Settings->language);
        $this->sms_settings->config = json_decode($this->sms_settings->config, true);
        $gateway                    = $this->sms_settings->config['gateway'];
        $this->settings             = isset($this->sms_settings->config[$gateway]) ? $this->sms_settings->config[$gateway] : null;
        $config                     = ['gateway' => $gateway, $gateway => $this->settings];
        if (!empty($gateway) && !empty($this->settings)) {
            $dbName=str_replace("cpesoft_","",$this->db->database);
            $config['log'] = ['path' => APPPATH . 'logs/'.$dbName.'/sms.log', 'level' => 100];
            if (!file_exists(APPPATH . 'logs/'.$dbName)) {
                mkdir(APPPATH . 'logs/'.$dbName, 0777, true);
            }

            $this->load->library('tec_sms', $config);
            $this->load->library('parser');
        }
    }

    public function custom($to, $text)
    {
        return $this->send($to, $text);
    }

    public function delivering($sale_id, $reference_no)
    {
        $parse_data = [
            'delivery_reference' => $reference_no,
        ];
        $text = $this->parser->parse_string(lang('delivering'), $parse_data);
        return $this->newSale($sale_id, $text);
    }

    public function newSale($sale_id, $text = null)
    {
        if (!$text) {
            $text = lang('new_sale');
        }
        $sale       = $this->site->getSaleByID($sale_id);
        $customer   = $this->site->getCompanyByID($sale->customer_id);
        $parse_data = [
            'customer'       => $customer->company && $customer->company != '-' ? $customer->company : $customer->name,
            'sale_reference' => $sale->reference_no,
            'grand_total'    => $this->sma->formatDecimal($sale->grand_total),
        ];
        $text = $this->parser->parse_string($text, $parse_data);
        return $this->send($customer->phone, $text);
    }

    public function paymentReceived($sale_id, $reference_no, $amount)
    {
        $parse_data = [
            'payment_reference' => $reference_no,
            'amount'            => $this->sma->formatDecimal($amount),
        ];
        $text = $this->parser->parse_string(lang('payment_received'), $parse_data);
        return $this->newSale($sale_id, $text);
    }

    public function send($to, $text)
    {
        if (!empty($this->sms_settings->config['gateway']) && !empty($this->settings)) {

            if($this->sms_settings->config['gateway']=='Oursms'){
                $to = str_replace(" ", "", $to);
                $char = ",";
                // Using strpos() function
                if (strpos($to, $char) !== false) {
                    if (!is_array($to)) {
                        $to = explode(',', $to);
                    }
                    $numbers="";
                    foreach ($to as $number) {
                        $number=trim($number);
                        $len = strlen($number);
                        if($len>8){
                            if($numbers!=""){
                                $numbers=$numbers.'"'.''.", \n";
                            }
                            if($len>10){
                                if(substr($number, 0, 1) === "+"){
                                    $number=substr($number, 1);
                                }
                                if(substr($number, 0, 2) === "00"){
                                    $rec=substr($number, 2);
                                }else{
                                    $rec=$number;
                                }
                            }else{
                                if(substr($number, 0, 1) === "0"){
                                    $rec="966".substr($number, 1);
                                }else{
                                    $rec="966".$number;
                                }
                            }
                            if($numbers==""){
                                $numbers=$rec;
                            }else{
                                $numbers=$numbers.'    "'.$rec;
                            }
                        }
                    }
                    $recipient=$numbers;
                } else {
                    $to=trim($to);
                    $len = strlen($to);
                    if($len>8){
                        if($len>10){
                            if(substr($to, 0, 1) === "+"){
                                $to=substr($to, 1);
                            }
                            if(substr($to, 0, 2) === "00"){
                                $recipient=substr($to, 2);
                            }else{
                                $recipient=$to;
                            }
                        }else{
                            if(substr($to, 0, 1) === "0"){
                                $recipient="966".substr($to, 1);
                            }else{
                                $recipient="966".$to;
                            }
                        }
                    }else{
                        $recipient="";
                    }
                }

                $len = strlen($recipient);
                if($len>8){
                $url = "https://api.oursms.com/msgs/sms";

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $headers = array(
                   "Authorization: Bearer ".$this->settings['apiKey'],
                   "Content-Type: application/json",
                   "Accept: application/json",
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $src=$this->settings['src'];
$data = <<<DATA
{
  "src": "$src",
  "dests": [
    "$recipient"
  ],
  "body": "$text",
  "priority": 0,
  "delay": 0,
  "validity": 0,
  "maxParts": 0,
  "dlr": false,
  "prevDups": 0,
  "msgClass": "transactional",
  "secure": false,
  "groups": null,
  "contacts": null
}
DATA;
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($curl);
                curl_close($curl);
                if(json_decode($resp)->jobId){
                    $response=($resp);
                    try {
                        $result_log = $this->tec_sms->set_log($response, 'info');
                    } catch (Exception $e) {
                    }
                     $result = ['sending' => true, 'error' => false, 'message' => $this->settings];
                    return $result;
                }else{
                    $response=($resp);
                    try {
                        $result_log = $this->tec_sms->set_log($response, 'error');
                    } catch (Exception $e) {
                    }
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                }

            }else{

                $result = ['sending' => false, 'error' => true, 'message' => 'Invalid Number'];
                try {
                    $result_log = $this->tec_sms->set_log($result, 'error');
                } catch (Exception $e) {
                }
            }

            }else if($this->sms_settings->config['gateway']=='Mobishatra'){
                $to = str_replace(" ", "", $to);
                $char = ",";
                // Using strpos() function
                if (strpos($to, $char) !== false) {
                    $url = "https://saudi.mshastra.com/sendurlcomma.aspx";
                    if (!is_array($to)) {
                        $to = explode(',', $to);
                    }
                    $numbers="";
                    foreach ($to as $number) {
                        $number=trim($number);
                        $len = strlen($number);
                        if($len>8){
                            if($len>10){
                                if(substr($number, 0, 1) === "+"){
                                    $number=substr($number, 1);
                                }
                                if(substr($number, 0, 2) === "00"){
                                    $rec=substr($number, 2);
                                }else{
                                    $rec=$number;
                                }
                            }else{
                                if(substr($number, 0, 1) === "0"){
                                    $rec="966".substr($number, 1);
                                }else{
                                    $rec="966".$number;
                                }
                            }
                            if($numbers==""){
                                $numbers=$rec;
                            }else{
                                $numbers=$numbers.','.$rec;
                            }
                        }
                    }
                    $recipient=$numbers;
                } else {//
                    $url = "https://saudi.mshastra.com/sendurl.aspx";
                    $to=trim($to);
                    $len = strlen($to);
                    if($len>8){
                        if($len>10){
                            if(substr($to, 0, 1) === "+"){
                                $to=substr($to, 1);
                            }
                            if(substr($to, 0, 2) === "00"){
                                $recipient=substr($to, 2);
                            }else{
                                $recipient=$to;
                            }
                        }else{
                            if(substr($to, 0, 1) === "0"){
                                $recipient="966".substr($to, 1);
                            }else{
                                $recipient="966".$to;
                            }
                        }
                    }else{
                        $recipient="";
                    }
                }

                $len = strlen($recipient);
                if($len>8){
                    $user=$this->settings['user'];
                    $pwd=$this->settings['passwd'];
                    $senderid=$this->settings['sid'];
                    $url = $url."?user=".$user."&pwd=".$pwd."&senderid=".$senderid."&mobileno=".$recipient."&msgtext=".urlencode($text)."&CountryCode=ALL&ShowError=C";

                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    //for debug only!
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    $resp = curl_exec($curl);
                    curl_close($curl);
                    $resp=trim($resp);

                //if($resp=="Send Successful"){
                if($resp=="Send Successful" || $resp=="000"){
                        $response="Send Successful";
                    try {
                        $result_log = $this->tec_sms->set_log($response, 'info');
                    } catch (Exception $e) {
                    }
                     $result = ['sending' => true, 'error' => false, 'message' => $this->settings];
                    return $result;
                }else{
                    $response=($resp);
                    try {
                        $result_log = $this->tec_sms->set_log($response, 'error');
                    } catch (Exception $e) {
                    }
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                }

            }else{

                $result = ['sending' => false, 'error' => true, 'message' => 'Invalid Number'];
                try {
                    $result_log = $this->tec_sms->set_log($result, 'error');
                } catch (Exception $e) {
                }
            }

            }else{


                try {
                    $result = $this->tec_sms->send($to, $text);
                } catch (Exception $e) {
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    try {
                        $result_log = $this->tec_sms->set_log($e->getMessage(), 'error');
                    } catch (Exception $e) {
                    }

                }
                return $result;
            }
        }
        return ['sending' => false, 'error' => true, 'message' => 'Incorrect SMS Settings'];
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }

    public function invSaleText($sale_id, $dbName = null)
    {

        $text = $this->site->getWhatsappTemplates()->sales;
        $sale       = $this->site->getSaleByID($sale_id);
        $customer   = $this->site->getCompanyByID($sale->customer_id);
        $parse_data = [
            'customer'       => $customer->company && $customer->company != '-' ? $customer->company : $customer->name,
            'sale_reference' => $sale->reference_no,
            'grand_total'    => $this->sma->formatDecimal($sale->grand_total),
            'order_link'       => 'https://tinyurl.com/47f8rxxc/'.$dbName.'/'.$sale->uuid,
        ];
         $text = $this->parser->parse_string($text, $parse_data);
         return $text;
    }

    public function invQuoteText($quote_id, $dbName = null)
    {

        $text = $this->site->getWhatsappTemplates()->quotes;
        $quote       = $this->site->getQuoteByID($quote_id);
        $customer   = $this->site->getCompanyByID($quote->customer_id);
        $parse_data = [
            'customer'       => $customer->company && $customer->company != '-' ? $customer->company : $customer->name,
            'quote_reference' => $quote->reference_no,
            'grand_total'    => $this->sma->formatDecimal($quote->grand_total),
            'order_link'       => 'https://tinyurl.com/3mefvpse/'.$dbName.'/'.$quote->uuid,
        ];
         $text = $this->parser->parse_string($text, $parse_data);
         return $text;
    }

    public function send_org($to, $text)
    {
        if (!empty($this->sms_settings->config['gateway']) && !empty($this->settings)) {

            if($this->sms_settings->config['gateway']=='Oursms'){

                $len = strlen($to);
                if($len>8){
                    if($len>10){

                            $recipient=$to;

                    }else{
                        if(substr($to, 0, 1) === "0"){
                            $recipient="966".substr($to, 1);
                        }else{
                            $recipient="966".$to;
                        }
                    }

                $url = "https://api.oursms.com/msgs/sms";

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $headers = array(
                   "Authorization: Bearer ".$this->settings['apiKey'],
                   "Content-Type: application/json",
                   "Accept: application/json",
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $src=$this->settings['src'];
$data = <<<DATA
{
  "src": "$src",
  "dests": [
    "$recipient"
  ],
  "body": "$text",
  "priority": 0,
  "delay": 0,
  "validity": 0,
  "maxParts": 0,
  "dlr": false,
  "prevDups": 0,
  "msgClass": "transactional",
  "secure": false,
  "groups": null,
  "contacts": null
}
DATA;
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($curl);
                curl_close($curl);
                if(json_decode($resp)->jobId){
                    $response=($resp);
                    try {
                        $result_log = $this->tec_sms->set_log($response, 'info');
                    } catch (Exception $e) {
                    }
                     $result = ['sending' => true, 'error' => false, 'message' => $this->settings];
                    return $result;
                }else{
                    $response=($resp);
                    try {
                        $result_log = $this->tec_sms->set_log($response, 'error');
                    } catch (Exception $e) {
                    }
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                }

            }else{

                $result = ['sending' => false, 'error' => true, 'message' => 'Invalid Number'];
                try {
                    $result_log = $this->tec_sms->set_log($result, 'error');
                } catch (Exception $e) {
                }
            }

            }else{


                try {
                    $result = $this->tec_sms->send($to, $text);
                } catch (Exception $e) {
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    try {
                        $result_log = $this->tec_sms->set_log($e->getMessage(), 'error');
                    } catch (Exception $e) {
                    }

                }
                return $result;
            }
        }
        return ['sending' => false, 'error' => true, 'message' => 'Incorrect SMS Settings'];
    }

    public function check_balance()
    {
        if (!empty($this->sms_settings->config['gateway']) && !empty($this->settings)) {
            if($this->sms_settings->config['gateway']=='Oursms'){
                $url = "https://api.oursms.com/billing/credits";

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                //curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $headers = array(
                   "Authorization: Bearer ".$this->settings['apiKey'],
                   "Content-Type: application/json",
                   "Accept: application/json",
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($curl);
                curl_close($curl);
                if(json_decode($resp)->credits){
                    $result=json_decode($resp)->credits;
                    return $result;
                }else{
                    return false;
                }

            }else if($this->sms_settings->config['gateway']=='Mobishatra'){
                $url = "https://saudi.mshastra.com/balance.aspx";


                    $user=$this->settings['user'];
                    $pwd=$this->settings['passwd'];
                    $url = $url."?user=".$user."&pwd=".$pwd;

                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    //for debug only!
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    $resp = curl_exec($curl);
                    curl_close($curl);
                    $resp=trim($resp);

                if (strpos($resp, '=') !== false) {
                    return $resp;
                }else{
                    return false;
                }
            }
        }
        return false;
    }

    public function invAwardPointsText($customer_id, $points, $award_points,$reverse_award_points = null,$site_name = null,$default_currency = null)
    {
        if($reverse_award_points){
            $text = str_replace("\\n","\n", ($this->site->getWhatsappTemplates()->award_points) . "");//$this->site->getWhatsappTemplates()->award_points;
        }else{
            $text = $this->site->getWhatsappTemplates()->reverse_award_points;
        }
        $customer   = $this->site->getCompanyByID($customer_id);
        $parse_data = [
            'customer'       => $customer->company && $customer->company != '-' ? $customer->company : $customer->name,
            'points' => $points,
            'award_points'    => $award_points,
            'reverse_award_points'    => $reverse_award_points,
            'site_name'    => $site_name,
            'default_currency'    => $default_currency,
        ];
         $text = $this->parser->parse_string($text, $parse_data);
         return $text;
    }

    public function invAwardPointsTextV2($customer, $points, $award_points)
    {

        $text = $this->site->getWhatsappTemplates()->reverse_award_points;
        //$customer   = $this->site->getCompanyByID($customer_id);
        $parse_data = [
            'customer'       => $customer,
            'points' => $points,
            'award_points'    => $award_points,
        ];
         $text = $this->parser->parse_string($text, $parse_data);
         return $text;
    }

}
