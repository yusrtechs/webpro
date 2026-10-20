<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
 *  ==============================================================================
 *  Author  : Mian Saleem
 *  Email   : saleem@tecdiary.com
 *  For     : Whatsapp Lib
 *  ==============================================================================
 */

class Whatsapp
{
    private $settings;
    private $whatsapp_settings;

    public function __construct()
    {
        $this->whatsapp_settings         = $this->site->getWhatsappSettings();
        $this->whatsapp_settings->config = json_decode($this->whatsapp_settings->config, true);
        $gateway                    = $this->whatsapp_settings->config['gateway'];
        $this->settings             = isset($this->whatsapp_settings->config[$gateway]) ? $this->whatsapp_settings->config[$gateway] : null;
        $config                     = ['gateway' => $gateway, $gateway => $this->settings];
        /*if (!empty($gateway) && !empty($this->settings)) {
            $dbName=str_replace("cpesoft_","",$this->db->database);
            $config['log'] = ['path' => APPPATH . 'logs/'.$dbName.'/sms.log', 'level' => 100];
            if (!file_exists(APPPATH . 'logs/'.$dbName)) {
                mkdir(APPPATH . 'logs/'.$dbName, 0777, true);
            }
            $this->lang->admin_load('sms', $this->Settings->language);
            $this->load->library('parser');
        }*/
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
            'grand_total'    => $this->sma->formatMoney($sale->grand_total),
        ];
        $text = $this->parser->parse_string($text, $parse_data);
        return $this->send($customer->phone, $text);
    }

    public function paymentReceived($sale_id, $reference_no, $amount)
    {
        $parse_data = [
            'payment_reference' => $reference_no,
            'amount'            => $this->sma->formatMoney($amount),
        ];
        $text = $this->parser->parse_string(lang('payment_received'), $parse_data);
        return $this->newSale($sale_id, $text);
    }

    public function send($to, $text,$customer = null,$amount = null,$uuid = null)
    {

        if (!empty($this->whatsapp_settings->config['gateway']) && !empty($this->settings)) {
            if($this->whatsapp_settings->config['gateway']=='4whatsapp'){
                $to = str_replace(" ", "", $to);
                $sent_flag=0;
                $failed_flag=0;
                $char = ",";
                // Using strpos() function
                if (strpos($to, $char) !== false) {
                    if (!is_array($to)) {
                        $to = explode(',', $to);
                    }

                    foreach ($to as $number) {
                        $recipient="";
                        $tel_no=trim($number);
                        $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient=$tel_no;
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="+".substr($tel_no, 2);
                                    }else{
                                        $recipient="+".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="+966".substr($tel_no, 1);
                                    }else{
                                        $recipient="+966".$tel_no;
                                    }
                                }
                            }

                            $message=$text;
                            $phone=$this->settings['phone'];
                            $password=$this->settings['password'];
                            $fields = array("phones"=>$phone, "account"=>"0", "doctype"=>"text", "recipient"=>$recipient, "message"=>$message, "token"=>$password);
                            $files = array();
                            $url = "http://api.4whatsapp.com/api/Agent_Client_";
                            $curl = curl_init();
                            $url_data = http_build_query($data);
                            $boundary = 'b77a5c561934e089';
                            $delimiter = '-------------' . $boundary;
                            $post_data = '';
                            $eol = "\r\n";

                            $delimiter = '-------------' . $boundary;

                            foreach ($fields as $name => $content) {
                                $post_data .= "--" . $delimiter . $eol
                                . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                                . $content . $eol;
                            }
                            $post_data .= "--" . $delimiter . "--".$eol;
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => $post_data,
                                CURLOPT_HTTPHEADER => array(
                                  //"Authorization: Bearer $TOKEN",
                                  "Content-Type: multipart/form-data; boundary=" . $delimiter,
                                  "Content-Length: " . strlen($post_data)

                                ),


                            ));


                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            $whatsappMsg="";
                            if (isset($response->data) && !empty(json_decode($response->data)) && json_decode($response->data)->status=='200'){
                                $sent_flag=1;
                                try {
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                } catch (Exception $e) {
                                }
                            }else{
                                $failed_flag=1;
                                try {
                                    $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                } catch (Exception $e) {
                                }
                            }

                        }else{
                            $failed_flag=1;
                            try {
                                $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                            } catch (Exception $e) {
                            }
                        }
                    }

                } else {
                    $recipient="";
                    $tel_no=trim($to);
                    $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient=$tel_no;
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="+".substr($tel_no, 2);
                                    }else{
                                        $recipient="+".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="+966".substr($tel_no, 1);
                                    }else{
                                        $recipient="+966".$tel_no;
                                    }
                                }
                            }

                            $message=$text;
                            $phone=$this->settings['phone'];
                            $password=$this->settings['password'];
                            $fields = array("phones"=>$phone, "account"=>"0", "doctype"=>"text", "recipient"=>$recipient, "message"=>$message, "token"=>$password);
                            $files = array();
                            $url = "http://api.4whatsapp.com/api/Agent_Client_";
                            $curl = curl_init();
                            $url_data = http_build_query($data);
                            $boundary = 'b77a5c561934e089';
                            $delimiter = '-------------' . $boundary;
                            $post_data = '';
                            $eol = "\r\n";

                            $delimiter = '-------------' . $boundary;

                            foreach ($fields as $name => $content) {
                                $post_data .= "--" . $delimiter . $eol
                                . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                                . $content . $eol;
                            }
                            $post_data .= "--" . $delimiter . "--".$eol;
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => $post_data,
                                CURLOPT_HTTPHEADER => array(
                                  //"Authorization: Bearer $TOKEN",
                                  "Content-Type: multipart/form-data; boundary=" . $delimiter,
                                  "Content-Length: " . strlen($post_data)

                                ),


                            ));


                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            $whatsappMsg="";
                            if (isset($response->data) && !empty(json_decode($response->data)) && json_decode($response->data)->status=='200'){
                                $sent_flag=1;
                                try {
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                } catch (Exception $e) {
                                }
                            }else{
                                $failed_flag=1;
                                try {
                                    $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                } catch (Exception $e) {
                                }
                            }

                        }else{
                            $failed_flag=1;
                            try {
                                $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                            } catch (Exception $e) {
                            }
                        }
                }


                if($sent_flag==1){
                    $result = ['sending' => true, 'error' => false, 'message' => $this->settings];
                    return $result;
                }else if($failed_flag==1){
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    return $result;
                }else{
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    return $result;
                }

            }else if($this->whatsapp_settings->config['gateway']=='chatberry'){
                $to = str_replace(" ", "", $to);
                $sent_flag=0;
                $failed_flag=0;
                $char = ",";
                // Using strpos() function
                if (strpos($to, $char) !== false) {
                    /*if (!is_array($to)) {
                        $to = explode(',', $to);
                    }

                    $count=0;
                    $message=$text;
                    $data='[';
                    foreach ($to as $number) {


                        $recipient="";
                        $tel_no=trim($number);
                        $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                             if($count!=0)
                                $data.=',';
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient=$tel_no;
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="+".substr($tel_no, 2);
                                    }else{
                                        $recipient="+".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="+966".substr($tel_no, 1);
                                    }else{
                                        $recipient="+966".$tel_no;
                                    }
                                }
                            }
                            if($count!=0)
                                $data .= '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"}}';
                            else
                                $data .= '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","delay": 2000,"message": {"text": "'.$message.'"}}';
                            $count++;
                        }

                        }

                        $data .= ']';




                            $authorization_token=$this->settings['authorization_token'];
                            $files = array();
                            $url = "https://dashboard.chatberry.net/api/send/template";

                            $curl = curl_init($url);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            $headers = array(
                               "Content-Type: application/json",
                               "accept: application/json",
                               "Authorization: Bearer ".$authorization_token

                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);



                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            $whatsappMsg="";
                            if (isset($response->results)){
                                $sent_flag=1;
                                try {
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Send ('.$data.') ');
                                } catch (Exception $e) {
                                }
                            }else{
                                $failed_flag=1;
                                try {
                                    $this->sma->write_log('ERROR', $resp,'whatsapp','Send ('.$data.') ');
                                } catch (Exception $e) {
                                }
                            }


                    //}*/
                    $this->sma->write_log('ERROR', '','whatsapp','Not Send ');

                } else {
                    $recipient="";
                    $tel_no=trim($to);
                    $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient=$tel_no;
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="+".substr($tel_no, 2);
                                    }else{
                                        $recipient="+".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="+966".substr($tel_no, 1);
                                    }else{
                                        $recipient="+966".$tel_no;
                                    }
                                }
                            }

                            $message=$text;
                            $authorization_token=$this->settings['authorization_token'];
                            $files = array();
                            $url = "https://dashboard.chatberry.net/api/send/template";

                            $curl = curl_init($url);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            $headers = array(
                               "Content-Type: application/json",
                               "accept: application/json",
                               "Authorization: Bearer ".$authorization_token

                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                            $data = '{"phone": "'.$recipient.'","template": {"name": "payment_successfull","language": {"code": "en_US"},"components": [{"type": "body","parameters": [{"type": "text","text": "'.$customer.'"},{"type": "text","text": "'.$amount.' SAR"}]},{"type": "button","sub_type": "url","index": "0","parameters":[{"type": "text","text": "'.$uuid.'"}]}]}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            $whatsappMsg="";
                            if (isset($response->data->success) && $response->data->success === true) {
                                $sent_flag=1;
                                try {
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.')');
                                } catch (Exception $e) {
                                }
                            }else{
                                //$failed_flag=1;
                                $sent_flag=1;
                                try {
                                    //$this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.')');
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.')');
                                } catch (Exception $e) {
                                }
                            }

                        }else{
                            $failed_flag=1;
                            try {
                                $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.')');
                            } catch (Exception $e) {
                            }
                        }
                }


                if($sent_flag==1){
                    $result = ['sending' => true, 'error' => false, 'message' => $this->settings];
                    return $result;
                }else if($failed_flag==1){
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    return $result;
                }else{
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    return $result;
                }

            }else if($this->whatsapp_settings->config['gateway']=='arkanprowhatsapp'){
                $to = str_replace(" ", "", $to);
                $sent_flag=0;
                $failed_flag=0;
                $char = ",";
                // Using strpos() function
                if (strpos($to, $char) !== false) {
                    if (!is_array($to)) {
                        $to = explode(',', $to);
                    }

                    $count=0;
                    $message=$text;
                    $data='[';
                    foreach ($to as $number) {


                        $recipient="";
                        $tel_no=trim($number);
                        $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                             if($count!=0)
                                $data.=',';
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient="".substr($tel_no, 1);
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="".substr($tel_no, 2);
                                    }else{
                                        $recipient="".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="966".substr($tel_no, 1);
                                    }else{
                                        $recipient="966".$tel_no;
                                    }
                                }
                            }
                            if($count!=0)
                                $data .= '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"}}';
                            else
                                $data .= '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","delay": 2000,"message": {"text": "'.$message.'"}}';
                            $count++;
                        }

                        }

                        $data .= ']';




                            $sessionId=$this->settings['sessionId'];
                            $files = array();
                            $url = "https://iparkanwebsitforwhats/".$sessionId."/messages/send/bulk";

                            $curl = curl_init($url);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            $headers = array(
                               "Content-Type: application/json",
                               "accept: application/json",

                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);



                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            $whatsappMsg="";
                            if (isset($response->results)){
                                $sent_flag=1;
                                try {
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Send ('.$data.') ');
                                } catch (Exception $e) {
                                }
                            }else{
                                $failed_flag=1;
                                try {
                                    $this->sma->write_log('ERROR', $resp,'whatsapp','Send ('.$data.') ');
                                } catch (Exception $e) {
                                }
                            }


                    //}

                } else {
                    $recipient="";
                    $tel_no=trim($to);
                    $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient="".substr($tel_no, 1);
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="".substr($tel_no, 2);
                                    }else{
                                        $recipient="".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="966".substr($tel_no, 1);
                                    }else{
                                        $recipient="966".$tel_no;
                                    }
                                }
                            }

                            $message=$text;
                            $sessionId=$this->settings['sessionId'];
                            $files = array();
                            $url = "https://iparkanwebsitforwhats/".$sessionId."/messages/send";

                            $curl = curl_init($url);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            $headers = array(
                               "Content-Type: application/json",
                               "accept: application/json",

                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                            $data = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            $whatsappMsg="";
                            if (isset($response->message) && $response->status=='PENDING'){
                                $sent_flag=1;
                                try {
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                } catch (Exception $e) {
                                }
                            }else{
                                $failed_flag=1;
                                try {
                                    $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                } catch (Exception $e) {
                                }
                            }

                        }else{
                            $failed_flag=1;
                            try {
                                $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                            } catch (Exception $e) {
                            }
                        }
                }


                if($sent_flag==1){
                    $result = ['sending' => true, 'error' => false, 'message' => $this->settings];
                    return $result;
                }else if($failed_flag==1){
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    return $result;
                }else{
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    return $result;
                }
            }
        }
        return ['sending' => false, 'error' => true, 'message' => 'Incorrect Whatsapp Settings'];
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }
    
    public function send_pdf($to, $pdf,$customer = null,$amount = null,$uuid = null)
    {

        if (!empty($this->whatsapp_settings->config['gateway']) && !empty($this->settings)) {


            if($this->whatsapp_settings->config['gateway']=='arkanprowhatsapp'){
                $to = str_replace(" ", "", $to);
                $sent_flag=0;
                $failed_flag=0;
                $char = ",";
                // Using strpos() function
                if (strpos($to, $char) !== false) {
                    if (!is_array($to)) {
                        $to = explode(',', $to);
                    }

                    $count=0;
                    $message=$text;
                    $data='[';
                    foreach ($to as $number) {


                        $recipient="";
                        $tel_no=trim($number);
                        $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                             if($count!=0)
                                $data.=',';
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient="".substr($tel_no, 1);
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="".substr($tel_no, 2);
                                    }else{
                                        $recipient="".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="966".substr($tel_no, 1);
                                    }else{
                                        $recipient="966".$tel_no;
                                    }
                                }
                            }
                            if($count!=0)
                                $data .= '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"document": {"url": "'.$pdf.'"},"mimetype": "application/pdf","fileName": "document.pdf"}}';
                            else
                                $data .= '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","delay": 2000,"message": {"document": {"url": "'.$pdf.'"},"mimetype": "application/pdf","fileName": "document.pdf"}}';
                            $count++;
                        }

                        }

                        $data .= ']';




                            $sessionId=$this->settings['sessionId'];
                            $files = array();
                            $url = "https://iparkanwebsitforwhats/".$sessionId."/messages/send/bulk";

                            $curl = curl_init($url);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            $headers = array(
                               "Content-Type: application/json",
                               "accept: application/json",

                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);



                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            $whatsappMsg="";
                            if (isset($response->results)){
                                $sent_flag=1;
                                try {
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Send ('.$data.') ');
                                } catch (Exception $e) {
                                }
                            }else{
                                $failed_flag=1;
                                try {
                                    $this->sma->write_log('ERROR', $resp,'whatsapp','Send ('.$data.') ');
                                } catch (Exception $e) {
                                }
                            }


                    //}

                } else {
                    $recipient="";
                    $tel_no=trim($to);
                    $len = strlen($tel_no);
                        if($len>8 && is_numeric($tel_no)){
                            if(substr($tel_no, 0, 1) === "+"){
                                $recipient="".substr($tel_no, 1);
                            }else{
                                if($len>10){
                                    if(substr($tel_no, 0, 2) === "00"){
                                        $recipient="".substr($tel_no, 2);
                                    }else{
                                        $recipient="".$tel_no;
                                    }
                                }else{
                                    if(substr($tel_no, 0, 1) === "0"){
                                        $recipient="966".substr($tel_no, 1);
                                    }else{
                                        $recipient="966".$tel_no;
                                    }
                                }
                            }

                            $message=$text;
                            $sessionId=$this->settings['sessionId'];
                            $files = array();
                            $url = "https://iparkanwebsitforwhats/".$sessionId."/messages/send";

                            $curl = curl_init($url);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            $headers = array(
                               "Content-Type: application/json",
                               "accept: application/json",

                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                            $data = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"document": {"url": "'.$pdf.'"},"mimetype": "application/pdf","fileName": "document.pdf"},"options": {}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            $whatsappMsg="";
                            if (isset($response->message) && $response->status=='PENDING'){
                                $sent_flag=1;
                                try {
                                    $this->sma->write_log('INFO', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                } catch (Exception $e) {
                                }
                            }else{
                                $failed_flag=1;
                                try {
                                    $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                                } catch (Exception $e) {
                                }
                            }

                        }else{
                            $failed_flag=1;
                            try {
                                $this->sma->write_log('ERROR', $resp,'whatsapp','Recipient ('.$recipient.') Message ('.$message.')');
                            } catch (Exception $e) {
                            }
                        }
                }


                if($sent_flag==1){
                    $result = ['sending' => true, 'error' => false, 'message' => $this->settings];
                    return $result;
                }else if($failed_flag==1){
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    return $result;
                }else{
                    $result = ['sending' => false, 'error' => true, 'message' => $this->settings];
                    return $result;
                }
            }
        }
        return ['sending' => false, 'error' => true, 'message' => 'Incorrect Whatsapp Settings'];
    }
}
