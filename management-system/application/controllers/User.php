<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Samet Aydın / sametay153@gmail.com
 * @version : 1.0
 * @since : 27.02.2018
 */
class User extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('client_model');
        $this->isLoggedIn();
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'ArkanPro : Home';

        $data['complaintsCount'] = $this->user_model->complaintsCount();
        $data['suggestionsCount'] = $this->user_model->suggestionsCount();
        $data['requestsCount'] = $this->user_model->requestsCount();
        $data['tasksCount'] = $this->user_model->tasksCount();
        $data['finishedTasksCount'] = $this->user_model->finishedTasksCount();
        $data['logsCount'] = $this->user_model->logsCount();
        $data['usersCount'] = $this->user_model->usersCount();
        $data['clientsCount'] = $this->client_model->clientsCount();
        $data['activateClientsCount'] = $this->client_model->activateClientsCount();

        //$renewClientsWhatsApp = $this->client_model->renewClientsWhatsApp(30);
        foreach ($renewClientsWhatsApp as $client) {

            $checkWhatsAppMessagesInfo=$this->client_model->checkWhatsAppMessagesInfo($client->company);
            if($checkWhatsAppMessagesInfo){

            }else{


            $recipient="";
            $sessionId="sessionId-user";
            $tel_no=$client->tel_no;
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

                $message="مرحبا  ".$client->name."\\n\\nنشكر لكم اختياركم شركة أركان الاحتراف للاتصالات وتقنية المعلومات.\\n\\nنود أن نلفت انتباهكم إلى أنه قد تبقى 30 يوماً فقط على انتهاء اشتراككم الحالي في برنامج CPE.\\n\\nللدخول إلى حسابكم وتجديد الاشتراك، يرجى استخدام المعلومات التالية:\\n\\nالرابط:\\nhttps://www.domain1.com\\nاسم الشركة: ".$client->company."\\n\\nنتمنى لكم تجربة ممتعة ومثمرة مع برنامجنا.\\n\\nإذا كان لديكم أي استفسارات أو تحتاجون إلى دعم، لا تترددوا في التواصل معنا.\\n\\nللدعم الفني، نرجو التواصل على الرقم التالي: 0503088707 أو زيارة الرابط https://www.companypro.com\\n\\nنشكركم على ثقتكم بنا، ونتطلع إلى خدمتكم دائمًا.\\n\\nمع خالص التحيات،\\nشركة أركان الاحتراف للاتصالات وتقنية المعلومات\\n\\nDear ".($client->name_en ? $client->name_en : $client->name).",\\n\\nThank you for choosing Arkan Al-Ihtiraf Communications and Information Technology.\\n\\nWe would like to inform you that your current subscription to the CPE Program will expire in 30 days.\\n\\nTo access your account and renew your subscription, please use the following information:\\n\\nLink:\\nhttps://www.domain1.com\\nCompany: ".$client->company."\\n\\nWe hope you have an enjoyable and productive experience with our program.\\n\\nIf you have any questions or need support, please do not hesitate to contact us.\\n\\nFor technical support, please call 0503088707 or visit https://www.companypro.com\\n\\nThank you for your trust in us, and we look forward to serving you always.\\n\\nBest regards,\\nArkan Al-Ihtiraf Communications and Information Technology";
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

                            $data1 = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data1);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            //print_r($response);

                            $whatsappMsg="";
                            if (isset($response->message) && $response->status=='PENDING'){





                    $userInfo = $this->user_model->getUserInfoById($this->vendorId);
                    $date=date('Y-m-d H:i:s');

                    $messageInfo = array(
                        'client_name' => $userInfo->name,
                        'company' => $client->company,
                        'date' => $date,
                        'title' => $message,
                        'user_id' => $this->vendorId
                    );

                    $message_id = $this->user_model->newWhatsAppMessage($messageInfo);
                }

            }
        }

        }

        //$renewClientsWhatsApp20 = $this->client_model->renewClientsWhatsApp(20);
        foreach ($renewClientsWhatsApp20 as $client) {

            $checkWhatsAppMessagesInfo=$this->client_model->checkWhatsAppMessagesInfo($client->company);
            if($checkWhatsAppMessagesInfo){

            }else{


            $recipient="";
            $sessionId="sessionId-user";
            $tel_no=$client->tel_no;
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

                $message="مرحبا  ".$client->name."\\n\\nنشكر لكم اختياركم شركة أركان الاحتراف للاتصالات وتقنية المعلومات.\\n\\nنود أن نلفت انتباهكم إلى أنه قد تبقى 20 يوماً فقط على انتهاء اشتراككم الحالي في برنامج CPE.\\n\\nللدخول إلى حسابكم وتجديد الاشتراك، يرجى استخدام المعلومات التالية:\\n\\nالرابط:\\nhttps://www.domain1.com\\nاسم الشركة: ".$client->company."\\n\\nنتمنى لكم تجربة ممتعة ومثمرة مع برنامجنا.\\n\\nإذا كان لديكم أي استفسارات أو تحتاجون إلى دعم، لا تترددوا في التواصل معنا.\\n\\nللدعم الفني، نرجو التواصل على الرقم التالي: 0503088707 أو زيارة الرابط https://www.companypro.com\\n\\nنشكركم على ثقتكم بنا، ونتطلع إلى خدمتكم دائمًا.\\n\\nمع خالص التحيات،\\nشركة أركان الاحتراف للاتصالات وتقنية المعلومات\\n\\nDear ".($client->name_en ? $client->name_en : $client->name).",\\n\\nThank you for choosing Arkan Al-Ihtiraf Communications and Information Technology.\\n\\nWe would like to inform you that your current subscription to the CPE Program will expire in 20 days.\\n\\nTo access your account and renew your subscription, please use the following information:\\n\\nLink:\\nhttps://www.domain1.com\\nCompany: ".$client->company."\\n\\nWe hope you have an enjoyable and productive experience with our program.\\n\\nIf you have any questions or need support, please do not hesitate to contact us.\\n\\nFor technical support, please call 0503088707 or visit https://www.companypro.com\\n\\nThank you for your trust in us, and we look forward to serving you always.\\n\\nBest regards,\\nArkan Al-Ihtiraf Communications and Information Technology";
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

                            $data1 = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data1);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            //print_r($response);

                            $whatsappMsg="";
                            if (isset($response->message) && $response->status=='PENDING'){

                    $userInfo = $this->user_model->getUserInfoById($this->vendorId);
                    $date=date('Y-m-d H:i:s');

                    $messageInfo = array(
                        'client_name' => $userInfo->name,
                        'company' => $client->company,
                        'date' => $date,
                        'title' => $message,
                        'user_id' => $this->vendorId
                    );

                    $message_id = $this->user_model->newWhatsAppMessage($messageInfo);
                }

            }
        }

        }

        //$renewClientsWhatsApp10 = $this->client_model->renewClientsWhatsApp(10);
        foreach ($renewClientsWhatsApp10 as $client) {

            $checkWhatsAppMessagesInfo=$this->client_model->checkWhatsAppMessagesInfo($client->company);
            if($checkWhatsAppMessagesInfo){

            }else{


            $recipient="";
            $sessionId="sessionId-user";
            $tel_no=$client->tel_no;
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

                $message="مرحبا  ".$client->name."\\n\\nنشكر لكم اختياركم شركة أركان الاحتراف للاتصالات وتقنية المعلومات.\\n\\nنود أن نلفت انتباهكم إلى أنه قد تبقى 10 أيام فقط على انتهاء اشتراككم الحالي في برنامج CPE.\\n\\nللدخول إلى حسابكم وتجديد الاشتراك، يرجى استخدام المعلومات التالية:\\n\\nالرابط:\\nhttps://www.domain1.com\\nاسم الشركة: ".$client->company."\\n\\nنتمنى لكم تجربة ممتعة ومثمرة مع برنامجنا.\\n\\nإذا كان لديكم أي استفسارات أو تحتاجون إلى دعم، لا تترددوا في التواصل معنا.\\n\\nللدعم الفني، نرجو التواصل على الرقم التالي: 0503088707 أو زيارة الرابط https://www.companypro.com\\n\\nنشكركم على ثقتكم بنا، ونتطلع إلى خدمتكم دائمًا.\\n\\nمع خالص التحيات،\\nشركة أركان الاحتراف للاتصالات وتقنية المعلومات\\n\\nDear ".($client->name_en ? $client->name_en : $client->name).",\\n\\nThank you for choosing Arkan Al-Ihtiraf Communications and Information Technology.\\n\\nWe would like to inform you that your current subscription to the CPE Program will expire in 10 days.\\n\\nTo access your account and renew your subscription, please use the following information:\\n\\nLink:\\nhttps://www.domain1.com\\nCompany: ".$client->company."\\n\\nWe hope you have an enjoyable and productive experience with our program.\\n\\nIf you have any questions or need support, please do not hesitate to contact us.\\n\\nFor technical support, please call 0503088707 or visit https://www.companypro.com\\n\\nThank you for your trust in us, and we look forward to serving you always.\\n\\nBest regards,\\nArkan Al-Ihtiraf Communications and Information Technology";
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

                            $data1 = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data1);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            //print_r($response);

                            $whatsappMsg="";
                            if (isset($response->message) && $response->status=='PENDING'){

                    $userInfo = $this->user_model->getUserInfoById($this->vendorId);
                    $date=date('Y-m-d H:i:s');

                    $messageInfo = array(
                        'client_name' => $userInfo->name,
                        'company' => $client->company,
                        'date' => $date,
                        'title' => $message,
                        'user_id' => $this->vendorId
                    );

                    $message_id = $this->user_model->newWhatsAppMessage($messageInfo);
                }

            }
        }

        }

        //$renewClientsWhatsApp1 = $this->client_model->renewClientsWhatsApp(1);
        foreach ($renewClientsWhatsApp1 as $client) {

            $checkWhatsAppMessagesInfo=$this->client_model->checkWhatsAppMessagesInfo($client->company);
            if($checkWhatsAppMessagesInfo){

            }else{


            $recipient="";
            $sessionId="sessionId-user";
            $tel_no=$client->tel_no;
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

                $message="مرحبا  ".$client->name."\\n\\nنشكر لكم اختياركم شركة أركان الاحتراف للاتصالات وتقنية المعلومات.\\n\\nنود أن نلفت انتباهكم إلى أنه قد تبقى يوم واحد فقط على انتهاء اشتراككم الحالي في برنامج CPE.\\n\\nللدخول إلى حسابكم وتجديد الاشتراك، يرجى استخدام المعلومات التالية:\\n\\nالرابط:\\nhttps://www.domain1.com\\nاسم الشركة: ".$client->company."\\n\\nنتمنى لكم تجربة ممتعة ومثمرة مع برنامجنا.\\n\\nإذا كان لديكم أي استفسارات أو تحتاجون إلى دعم، لا تترددوا في التواصل معنا.\\n\\nللدعم الفني، نرجو التواصل على الرقم التالي: 0503088707 أو زيارة الرابط https://www.companypro.com\\n\\nنشكركم على ثقتكم بنا، ونتطلع إلى خدمتكم دائمًا.\\n\\nمع خالص التحيات،\\nشركة أركان الاحتراف للاتصالات وتقنية المعلومات\\n\\nDear ".($client->name_en ? $client->name_en : $client->name).",\\n\\nThank you for choosing Arkan Al-Ihtiraf Communications and Information Technology.\\n\\nWe would like to inform you that your current subscription to the CPE Program will expire in 1 day.\\n\\nTo access your account and renew your subscription, please use the following information:\\n\\nLink:\\nhttps://www.domain1.com\\nCompany: ".$client->company."\\n\\nWe hope you have an enjoyable and productive experience with our program.\\n\\nIf you have any questions or need support, please do not hesitate to contact us.\\n\\nFor technical support, please call 0503088707 or visit https://www.companypro.com\\n\\nThank you for your trust in us, and we look forward to serving you always.\\n\\nBest regards,\\nArkan Al-Ihtiraf Communications and Information Technology";
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

                            $data1 = '{"jid": "'.$recipient.'@s.whatsapp.net","type": "number","message": {"text": "'.$message.'"},"options": {}}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $data1);

                            //for debug only!
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



                            //$response = json_decode(curl_exec($curl));
                            //curl_close($curl);
                            $resp = curl_exec($curl);
                            $response = json_decode($resp);
                            curl_close($curl);

                            //print_r($response);

                            $whatsappMsg="";
                            if (isset($response->message) && $response->status=='PENDING'){

                    $userInfo = $this->user_model->getUserInfoById($this->vendorId);
                    $date=date('Y-m-d H:i:s');

                    $messageInfo = array(
                        'client_name' => $userInfo->name,
                        'company' => $client->company,
                        'date' => $date,
                        'title' => $message,
                        'user_id' => $this->vendorId
                    );

                    $message_id = $this->user_model->newWhatsAppMessage($messageInfo);
                }

            }
        }

        }


        if ($this->getUserStatus() == TRUE) {
            $this->session->set_flashdata('error', 'Please change your password first for your security.');
            redirect('loadChangePass');
        }

        $this->loadViews("dashboard", $this->global, $data, NULL);
    }

    /**
     * This function is used to check whether email already exist or not
     */
    function checkEmailExists()
    {
        $userId = $this->input->post("userId");
        $email = $this->input->post("email");

        if (empty($userId)) {
            $result = $this->user_model->checkEmailExists($email);
        } else {
            $result = $this->user_model->checkEmailExists($email, $userId);
        }

        if (empty($result)) {
            echo ("true");
        } else {
            echo ("false");
        }
    }

    /**
     * This function is used to load edit user view
     */
    function loadUserEdit()
    {
        $this->global['pageTitle'] = 'ArkanPro : Account settings';

        $data['userInfo'] = $this->user_model->getUserInfo($this->vendorId);

        $this->loadViews("userEdit", $this->global, $data, NULL);
    }

    /**
     * This function is used to update the of the user info
     */
    function updateUser()
    {
        $this->load->library('form_validation');

        $userId = $this->input->post('id');

        $this->form_validation->set_rules('fname', 'Full Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
        $this->form_validation->set_rules('oldpassword', 'Old password', 'max_length[20]');
        $this->form_validation->set_rules('cpassword', 'Password', 'matches[cpassword2]|max_length[20]');
        $this->form_validation->set_rules('cpassword2', 'Confirm Password', 'matches[cpassword]|max_length[20]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|min_length[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->loadUserEdit();
        } else {
            $name = $this->security->xss_clean($this->input->post('fname'));
            $email = $this->security->xss_clean($this->input->post('email'));
            $password = $this->input->post('cpassword');
            $mobile = $this->security->xss_clean($this->input->post('mobile'));
            $oldPassword = $this->input->post('oldpassword');

            $userInfo = array();

            if (empty($password)) {
                $userInfo = array(
                    'email' => $email, 'name' => $name,
                    'mobile' => $mobile, 'status' => 1, 'updatedBy' => $this->vendorId, 'updatedDtm' => date('Y-m-d H:i:s')
                );
            } else {
                $resultPas = $this->user_model->matchOldPassword($this->vendorId, $oldPassword);

                if (empty($resultPas)) {
                    $this->session->set_flashdata('nomatch', 'Your old password is not correct');
                    redirect('userEdit');
                } else {
                    $userInfo = array(
                        'email' => $email, 'password' => getHashedPassword($password),
                        'name' => ucwords($name), 'mobile' => $mobile, 'status' => 1, 'updatedBy' => $this->vendorId,
                        'updatedDtm' => date('Y-m-d H:i:s')
                    );
                }
            }

            $result = $this->user_model->editUser($userInfo, $userId);

            if ($result == true) {
                $process = 'Account Settings Update';
                $processFunction = 'User/updateUser';
                $this->logrecord($process, $processFunction);

                $this->session->set_flashdata('success', 'Your Account Settings have been successfully updated');
            } else {
                $this->session->set_flashdata('error', 'Failed to update Account Settings');
            }

            redirect('userEdit');
        }
    }



    /**
     * This function is used to load the change password view
     */
    function loadChangePass()
    {
        $this->global['pageTitle'] = 'ArkanPro : Change Password';

        $this->loadViews("changePassword", $this->global, NULL, NULL);
    }


    /**
     * This function is used to change the password of the user
     */
    function changePassword()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('oldPassword', 'Old password', 'required|max_length[20]');
        $this->form_validation->set_rules('newPassword', 'New password', 'required|max_length[20]');
        $this->form_validation->set_rules('cNewPassword', 'Confirm new password', 'required|matches[newPassword]|max_length[20]');

        if ($this->form_validation->run() == FALSE) {
            $this->loadChangePass();
        } else {
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            $resultPas = $this->user_model->matchOldPassword($this->vendorId, $oldPassword);

            if (empty($resultPas)) {
                $this->session->set_flashdata('nomatch', 'Your old password is not correct');
                redirect('loadChangePass');
            } else {
                $usersData = array(
                    'password' => getHashedPassword($newPassword), 'status' => 1, 'updatedBy' => $this->vendorId,
                    'updatedDtm' => date('Y-m-d H:i:s')
                );

                $result = $this->user_model->changePassword($this->vendorId, $usersData);

                if ($result > 0) {

                    $process = 'Change Password';
                    $processFunction = 'User/changePassword';
                    $this->logrecord($process, $processFunction);

                    $this->session->set_flashdata('success', 'Password change successful');
                } else {
                    $this->session->set_flashdata('error', 'Password change failed');
                }

                redirect('loadChangePass');
            }
        }
    }

    /**
     * This function is used to open 404 view
     */
    function pageNotFound()
    {
        $this->global['pageTitle'] = 'ArkanPro : 404 - Page Not Found';

        $this->loadViews("404", $this->global, NULL, NULL);
    }

    /**
     * This function is used to finish tasks.
     */
    function endTask($taskId)
    {
        $taskInfo = array('statusId' => 2, 'endDtm' => date('Y-m-d H:i:s'));

        $result = $this->user_model->endTask($taskId, $taskInfo);

        if ($result > 0) {
            $process = 'Task Completion';
            $processFunction = 'User/endTask';
            $this->logrecord($process, $processFunction);
            $this->session->set_flashdata('success', 'Task successfully completed');
            if ($this->role != ROLE_EMPLOYEE) {
                redirect('tasks');
            } else {
                redirect('etasks');
            }
        } else {
            $this->session->set_flashdata('error', 'Task completion failed');
            if ($this->role != ROLE_EMPLOYEE) {
                redirect('tasks');
            } else {
                redirect('etasks');
            }
        }
    }

    /**
     * This function is used to open the tasks page for users (no edit/delete etc)
     */
    function etasks()
    {
        $data['taskRecords'] = $this->user_model->getTasks();

        $process = 'User All tasks';
        $processFunction = 'User/etasks';
        $this->logrecord($process, $processFunction);

        $this->global['pageTitle'] = 'ArkanPro : All Tasks';

        $this->loadViews("etasks", $this->global, $data, NULL);
    }
}
