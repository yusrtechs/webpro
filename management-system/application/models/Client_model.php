<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Client_model extends CI_Model
{
    /**
     * This function is used to get the user listing count
     * @param string $searchText : This is optional search text
     * @return number $count : This is row count
     */
    function clientListingCount($searchText = '')
    {
        $this->db->select('BaseTbl.id, BaseTbl.name, BaseTbl.name_en, BaseTbl.company, CType.second_name, BaseTbl.created_at, USERS.name as uname , BaseTbl.status');
        $this->db->from('client as BaseTbl');
        $this->db->join('company_type as CType', 'CType.id = BaseTbl.company_type', 'left');
        $this->db->join('tbl_users as USERS', 'USERS.id = BaseTbl.createdBy', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.name  LIKE '%" . $searchText . "%'
                            OR  BaseTbl.name_en  LIKE '%" . $searchText . "%'
                            OR  BaseTbl.company  LIKE '%" . $searchText . "%')";
            $this->db->where($likeCriteria);
        }
        //$this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();

        return $query->num_rows();
    }

    /**
     * This function is used to get the user listing count
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function clientListing($searchText = '', $page, $segment)
    {
        $this->db->select('BaseTbl.id, BaseTbl.name, BaseTbl.name_en, BaseTbl.company, CType.second_name, BaseTbl.created_at, USERS.name as uname , BaseTbl.status, BaseTbl.isStore,isSynchronizewithZATCA, Activity.ISIC_Description_6, BaseTbl.tel_no, BaseTbl.address');
        $this->db->from('client as BaseTbl');
        $this->db->join('company_type as CType', 'CType.id = BaseTbl.company_type', 'left');
        $this->db->join('tbl_users as USERS', 'USERS.id = BaseTbl.createdBy', 'left');
        $this->db->join('active_crs as Activity', 'Activity.id = BaseTbl.activity_id', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.name  LIKE '%" . $searchText . "%'
                            OR  BaseTbl.name_en  LIKE '%" . $searchText . "%'
                            OR  BaseTbl.company  LIKE '%" . $searchText . "%')";
            $this->db->where($likeCriteria);
        }
        $this->db->order_by('BaseTbl.id DESC');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    function clientListingV2($searchText = '', $page, $segment)
    {
        $this->db->select('BaseTbl.id, BaseTbl.name, BaseTbl.name_en, BaseTbl.company, CType.second_name, CONCAT(USERS.name,"<br/>",BaseTbl.created_at)  as uname , BaseTbl.status, BaseTbl.isStore,isSynchronizewithZATCA, Activity.ISIC_Description_6, BaseTbl.tel_no, BaseTbl.address');
        $this->db->from('client as BaseTbl');
        $this->db->join('company_type as CType', 'CType.id = BaseTbl.company_type', 'left');
        $this->db->join('tbl_users as USERS', 'USERS.id = BaseTbl.createdBy', 'left');
        $this->db->join('active_crs as Activity', 'Activity.id = BaseTbl.activity_id', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.name  LIKE '%" . $searchText . "%'
                            OR  BaseTbl.name_en  LIKE '%" . $searchText . "%'
                            OR  BaseTbl.company  LIKE '%" . $searchText . "%')";
            $this->db->where($likeCriteria);
        }
        $this->db->order_by('BaseTbl.id DESC');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    /**
     * This function is used to get the user roles information
     * @return array $result : This is result of the query
     */
    function getCompanyTypes()
    {
        $this->db->select('id, second_name');
        $this->db->from('company_type');
        $query = $this->db->get();

        return $query->result();
    }

    function getActivities()
    {
        $this->db->select('id, ISIC_Description_6');
        $this->db->from('active_crs');
        $query = $this->db->get();

        return $query->result();
    }
/*
    function getAPIElectronicLinking()
    {
        $this->db->select('id, ename');
        $this->db->from('electronic_linking');
        $query = $this->db->get();

        return $query->result();
    }
*/
    /**
     * This function is used to check whether email id is already exist or not
     * @param {string} $email : This is email id
     * @param {number} $userId : This is user id
     * @return {mixed} $result : This is searched result
     */
    function checkEmailExists($email, $userId = 0)
    {
        $this->db->select("email");
        $this->db->from("tbl_users");
        $this->db->where("email", $email);
        $this->db->where("isDeleted", 0);
        if ($userId != 0) {
            $this->db->where("userId !=", $userId);
        }
        $query = $this->db->get();

        return $query->result();
    }


    /**
     * This function is used to add new user to system
     * @return number $insert_id : This is last inserted id
     */
    function addNewClient($clientInfo)
    {
        $this->db->trans_start();
        $this->db->insert('client', $clientInfo);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }

    /**
     * This function used to get user information by id
     * @param number $userId : This is user id
     * @return array $result : This is user information
     */
    function getClientInfo($id)
    {
        $this->db->select('id, name, name_en, company, company_type, DATE_FORMAT(renewal_date,"%Y-%m-%d") as renewal_date, DATE_ADD(renewal_date, INTERVAL 1 YEAR) as orginalrenewal_date,status,isAccounting,isPOS,automatedEntries,isManufacturing,automatedEntriesOffline,syncEveryMinues,agent_id,isFoodics,isFixedAsset,isStore,isSynchronizewithZATCA,siteurl,tel_no,activity_id,address,isGrouping,isAutoSynchronizeZATCA,isHR,reason,inv_no,isAutoSynchronizeZATCAPOS,isQuroosh,isNearpay');
        $this->db->from('client');
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row();
    }


    function getClientInfoByCopmany($company)
    {
        $this->db->select('id, name, name_en, company, company_type, DATE_FORMAT(renewal_date,"%Y-%m-%d") as renewal_date, DATE_ADD(renewal_date, INTERVAL 1 YEAR) as orginalrenewal_date,status,isAccounting,isPOS,automatedEntries,isManufacturing,automatedEntriesOffline,syncEveryMinues,agent_id,isFoodics,isFixedAsset,isStore,isSynchronizewithZATCA,siteurl,tel_no,activity_id,address,isGrouping,isAutoSynchronizeZATCA,isHR,inv_no,isAutoSynchronizeZATCAPOS,isQuroosh,isNearpay');
        $this->db->from('client');
        $this->db->where('company', $company);
        $query = $this->db->get();

        return $query->row();
    }


    /**
     * This function is used to update the user information
     * @param array $userInfo : This is users updated information
     * @param number $userId : This is user id
     */
    function editClient($clientInfo, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('client', $clientInfo);

        return TRUE;
    }



    /**
     * This function is used to delete the user information
     * @param number $userId : This is user id
     * @return boolean $result : TRUE / FALSE
     */
    function deleteUser($userId, $userInfo)
    {
        $this->db->where('id', $userId);
        $this->db->update('tbl_users', $userInfo);

        return $this->db->affected_rows();
    }


    /**
     * This function is used to match users password for change password
     * @param number $userId : This is user id
     */
    function matchOldPassword($userId, $oldPassword)
    {
        $this->db->select('userId, password');
        $this->db->where('id', $userId);
        $this->db->where('isDeleted', 0);
        $query = $this->db->get('tbl_users');

        $user = $query->result();

        if (!empty($user)) {
            if (verifyHashedPassword($oldPassword, $user[0]->password)) {
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * This function is used to change users password
     * @param number $userId : This is user id
     * @param array $userInfo : This is user updation info
     */
    function changePassword($userId, $userInfo)
    {
        $this->db->where('id', $userId);
        $this->db->where('isDeleted', 0);
        $this->db->update('tbl_users', $userInfo);

        return $this->db->affected_rows();
    }


    /**
     * This function is used to get user log history count
     * @param number $userId : This is user id
     */

    function logHistoryCount($userId)
    {
        $this->db->select('*');
        $this->db->from('tbl_log as BaseTbl');

        if ($userId == NULL) {
            $query = $this->db->get();
            return $query->num_rows();
        } else {
            $this->db->where('BaseTbl.id', $userId);
            $query = $this->db->get();
            return $query->num_rows();
        }
    }

    /**
     * This function is used to get user log history
     * @param number $userId : This is user id
     * @return array $result : This is result
     */
    function logHistory($userId)
    {
        $this->db->select('*');
        $this->db->from('tbl_log as BaseTbl');

        if ($userId == NULL) {
            $this->db->order_by('BaseTbl.createdDtm', 'DESC');
            $query = $this->db->get();
            $result = $query->result();
            return $result;
        } else {
            $this->db->where('BaseTbl.id', $userId);
            $this->db->order_by('BaseTbl.createdDtm', 'DESC');
            $query = $this->db->get();
            $result = $query->result();
            return $result;
        }
    }

    /**
     * This function used to get user information by id
     * @param number $userId : This is user id
     * @return array $result : This is user information
     */
    function getUserInfoById($userId)
    {
        $this->db->select('userId, name, email, mobile, roleId');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', 0);
        $this->db->where('id', $userId);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function is used to get tasks
     */
    function getTasks()
    {
        $this->db->select('*');
        $this->db->from('tbl_task as TaskTbl');
        $this->db->join('tbl_users as Users', 'USERS.id = TaskTbl.createdBy');
        $this->db->join('tbl_roles as Roles', 'Roles.roleId = Users.roleId');
        $this->db->join('tbl_tasks_situations as Situations', 'Situations.statusId = TaskTbl.statusId');
        $this->db->join('tbl_tasks_prioritys as Prioritys', 'Prioritys.priorityId = TaskTbl.priorityId');
        $this->db->order_by('TaskTbl.statusId ASC, TaskTbl.priorityId');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    /**
     * This function is used to get task prioritys
     */
    function getTasksPrioritys()
    {
        $this->db->select('*');
        $this->db->from('tbl_tasks_prioritys');
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to get task situations
     */
    function getTasksSituations()
    {
        $this->db->select('*');
        $this->db->from('tbl_tasks_situations');
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to add a new task
     */
    function addNewTasks($taskInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_task', $taskInfo);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }

    /**
     * This function used to get task information by id
     * @param number $taskId : This is task id
     * @return array $result : This is task information
     */
    function getTaskInfo($taskId)
    {
        $this->db->select('*');
        $this->db->from('tbl_task');
        $this->db->join('tbl_tasks_situations as Situations', 'Situations.statusId = tbl_task.statusId');
        $this->db->join('tbl_tasks_prioritys as Prioritys', 'Prioritys.priorityId = tbl_task.priorityId');
        $this->db->where('id', $taskId);
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to edit tasks
     */
    function editTask($taskInfo, $taskId)
    {
        $this->db->where('id', $taskId);
        $this->db->update('tbl_task', $taskInfo);

        return $this->db->affected_rows();
    }

    /**
     * This function is used to delete tasks
     */
    function deleteTask($taskId)
    {
        $this->db->where('id', $taskId);
        $this->db->delete('tbl_task');
        return TRUE;
    }

    /**
     * This function is used to return the size of the table
     * @param string $tablename : This is table name
     * @param string $dbname : This is database name
     * @return array $return : Table size in mb
     */
    function gettablemb($tablename, $dbname)
    {
        $this->db->select('round(((data_length + index_length)/1024/1024),2) as total_size');
        $this->db->from('information_schema.tables');
        $this->db->where('table_name', $tablename);
        $this->db->where('table_schema', $dbname);
        $query = $this->db->get($tablename);

        return $query->row();
    }

    /**
     * This function is used to delete tbl_log table records
     */
    function clearlogtbl()
    {
        $this->db->truncate('tbl_log');
        return TRUE;
    }

    /**
     * This function is used to delete tbl_log_backup table records
     */
    function clearlogBackuptbl()
    {
        $this->db->truncate('tbl_log_backup');
        return TRUE;
    }

    /**
     * This function is used to get user log history
     * @return array $result : This is result
     */
    function logHistoryBackup()
    {
        $this->db->select('*');
        $this->db->from('tbl_log_backup as BaseTbl');
        $this->db->order_by('BaseTbl.createdDtm', 'DESC');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    /**
     * This function is used to complete tasks
     */
    function endTask($taskId, $taskInfo)
    {
        $this->db->where('id', $taskId);
        $this->db->update('tbl_task', $taskInfo);

        return $this->db->affected_rows();
    }

    /**
     * This function is used to get the tasks count
     * @return array $result : This is result
     */
    function tasksCount()
    {
        $this->db->select('*');
        $this->db->from('tbl_task as BaseTbl');
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * This function is used to get the finished tasks count
     * @return array $result : This is result
     */
    function finishedTasksCount()
    {
        $this->db->select('*');
        $this->db->from('tbl_task as BaseTbl');
        $this->db->where('BaseTbl.statusId', 2);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * This function is used to get the logs count
     * @return array $result : This is result
     */
    function logsCount()
    {
        $this->db->select('*');
        $this->db->from('tbl_log as BaseTbl');
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * This function is used to get the users count
     * @return array $result : This is result
     */
    function usersCount()
    {
        $this->db->select('*');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->where('isDeleted', 0);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function clientsCount()
    {
        $this->db->select('*');
        $this->db->from('client as BaseTbl');
        //   $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function activateClientsCount()
    {
        $this->db->select('*');
        $this->db->from('client as BaseTbl');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getUserStatus($userId)
    {
        $this->db->select('BaseTbl.status');
        $this->db->where('BaseTbl.id', $userId);
        $this->db->limit(1);
        $query = $this->db->get('tbl_users as BaseTbl');

        return $query->row();
    }

    function getClientsWithFilters($filters = [])
    {
        $sql = "SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));";
        $this->db->query($sql);


        $this->db->select('BaseTbl.id, BaseTbl.name, BaseTbl.name_en, BaseTbl.company, CType.second_name, BaseTbl.created_at, USERS.name as uname,DATE_FORMAT(BaseTbl.renewal_date,"%Y-%m-%d") as renewal_date , BaseTbl.status,count(cTrans.id) as num_of_payment');
        $this->db->from('client as BaseTbl');
        $this->db->join('client_transactions as cTrans', 'cTrans.client_id = BaseTbl.id', 'inner');
        $this->db->join('company_type as CType', 'CType.id = BaseTbl.company_type', 'left');
        $this->db->join('tbl_users as USERS', 'USERS.id = BaseTbl.createdBy', 'left');

        if (isset($filters['company_type']) && $filters['company_type'] != -99)
            $this->db->where('BaseTbl.company_type', $filters['company_type']);
        if (isset($filters['status']) && $filters['status'] != -99)
            $this->db->where('BaseTbl.status', $filters['status']);
        if (isset($filters['isAccounting']))
            $this->db->where('BaseTbl.isAccounting', 1);
        if (isset($filters['automatedEntries']))
            $this->db->where('BaseTbl.automatedEntries', 1);
        if (isset($filters['automatedEntriesOffline']))
            $this->db->where('BaseTbl.automatedEntriesOffline', 1);
        if (isset($filters['isFoodics']))
            $this->db->where('BaseTbl.isFoodics', 1);
        if (isset($filters['isFixedAsset']))
            $this->db->where('BaseTbl.isFixedAsset', 1);
        if (isset($filters['isGrouping']))
            $this->db->where('BaseTbl.isGrouping', 1);
        if (isset($filters['isManufacturing']))
            $this->db->where('BaseTbl.isManufacturing', 1);
        if (isset($filters['isHR']))
            $this->db->where('BaseTbl.isHR', 1);
        if (isset($filters['isStore']))
            $this->db->where('BaseTbl.isStore', 1);
        if (isset($filters['isSynchronizewithZATCA']))
            $this->db->where('BaseTbl.isSynchronizewithZATCA', 1);
        if (isset($filters['isPOS']))
            $this->db->where('BaseTbl.isPOS', 1);
        if (isset($filters['agent_id']))
            $this->db->where('BaseTbl.agent_id', $filters['agent_id']);
        $this->db->group_by('BaseTbl.id');
        $this->db->order_by('BaseTbl.id DESC');

        $query = $this->db->get();
        return $query->result();
    }

    function getLatestSale($company,$start_date = null,$end_date = null)
    {

            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = 'cpesoft_'.$company;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $company;
            $config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = 'sma_';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = FALSE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = '';
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = FALSE;

            $CI = &get_instance();
            $data = $CI->load->database($config, TRUE);
			$this->mynewdb =$data;

/*
            $isFoodics = $this->mynewdb->query("SELECT isFoodics FROM sma_settings")->result_array()[0]['isFoodics'];
            if($isFoodics){

                $latest_date = $this->mynewdb->query("SELECT DATE_FORMAT(latest_sync_order, '%Y-%m-%d') as latest_date FROM sma_foodics_settings")->result_array()[0]['latest_date'];
                    if(!$latest_date){
                        $latest_date ='';
                    }

            }else{
*/


            if($start_date && $end_date)
                $latest_date = $this->mynewdb->query("SELECT DATE_FORMAT(date, '%Y-%m-%d') as latest_date FROM sma_sales where date between '".$start_date."' and '".$end_date."'  ORDER BY sma_sales.date DESC LIMIT 1")->result_array()[0]['latest_date'];
            else
                $latest_date = $this->mynewdb->query("SELECT DATE_FORMAT(date, '%Y-%m-%d') as latest_date FROM sma_sales ORDER BY sma_sales.date DESC LIMIT 1")->result_array()[0]['latest_date'];

                if(!$latest_date){
                        $latest_date ='';
                    }
                //}

                if($start_date && $end_date)
                    $latest_login = $this->mynewdb->query("SELECT DATE_FORMAT(time, '%Y-%m-%d') as latest_login FROM sma_user_logins where time between '".$start_date."' and '".$end_date."' ORDER BY sma_user_logins.time DESC LIMIT 1")->result_array()[0]['latest_login'];
                else
                    $latest_login = $this->mynewdb->query("SELECT DATE_FORMAT(time, '%Y-%m-%d') as latest_login FROM sma_user_logins ORDER BY sma_user_logins.time DESC LIMIT 1")->result_array()[0]['latest_login'];

                if(!$latest_login){
                        $latest_login ='';
                    }



        return array(
            'latest_date' => $latest_date, 'latest_login' =>$latest_login);
    }

    function createTransaction($data)
    {
        $this->db->trans_start();
        $this->db->insert('client_transactions', $data);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }
    function getProceduresTypes()
    {
        $this->db->select('*');
        $this->db->from('tbl_procedure_type');
        $this->db->order_by('sort_ord ASC');
        $query = $this->db->get();

        return $query->result();
    }

    function addNewProcedures($procedureInfo)
    {
        $this->db->insert('tbl_procedure', $procedureInfo);

        $insert_id = $this->db->insert_id();


        return $insert_id;
    }

    function getProcedures($company)
    {
        $this->db->select('ProcedureTbl.request_by,ProcedureTypesTbl.procedure_type,ProcedureTbl.comment,ProcedureTbl.copied_company_name,ProcedureTbl.copy_to_warehouse_code,Users.name,ProcedureTbl.createdDtm');
        $this->db->from('tbl_procedure as ProcedureTbl');
        $this->db->join('tbl_users as Users','Users.id = ProcedureTbl.createdBy');
        $this->db->join('tbl_procedure_type as ProcedureTypesTbl','ProcedureTypesTbl.ptId = ProcedureTbl.ptId');
        $this->db->where('ProcedureTbl.company', $company);
        $this->db->order_by('ProcedureTbl.createdDtm DESC');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function getShopApiKeysByClientId($client_id)
    {
		$this->db->select('key_id,consumer_key,consumer_secret,permissions');
        $q=$this->db->get_where('shop_api_keys',['client_id' =>$client_id]);
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;

            }
            return $data;
        }
        return false;
    }

    public function addShopApiKey($data)
    {
        return $this->db->insert('shop_api_keys', $data);
    }

    function renewClientsWhatsApp($day)
    {
        $this->db->select('id,name,name_en,company,DATEDIFF(DATE_ADD(Date(renewal_date), INTERVAL 1 YEAR),CURDATE()) as renewal_date,tel_no');
        $this->db->where('status', 1);
        $this->db->where('LENGTH(tel_no) >',  8);
        $this->db->where('DATEDIFF(DATE_ADD(Date(renewal_date), INTERVAL 1 YEAR),CURDATE()) =',  $day);

        $q=$this->db->get('client');
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;

            }
            return $data;
        }
        return false;
    }

    function checkWhatsAppMessagesInfo($company)
    {
        $this->db->select('Date(date) as date');
        $this->db->where('company', $company);
        $this->db->where('DATEDIFF(Date(date),CURDATE()) =', 0);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('whatsapp_messages');

        return $query->row();
    }

    function createElectronicContract($data)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_electronic_contract', $data);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }

    function getElectronicContract($company)
    {
        $this->db->select('*');
        $this->db->from('tbl_electronic_contract');
        $this->db->where('company', $company);
        $query = $this->db->get();
        return $query->row() ? $query->row() : false;
    }

    function updateElectronicContract($company, $data)
    {
        $this->db->where('company', $company);
        $this->db->update('tbl_electronic_contract', $data);

        return $this->db->affected_rows();
    }

    function getSalesWithFilters($filters = [])
    {
        $sql = "SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));";
        $this->db->query($sql);


        $this->db->select('BaseTbl.id, BaseTbl.name, BaseTbl.name_en, BaseTbl.company, CType.second_name, BaseTbl.created_at, USERS.name as uname,DATE_FORMAT(DATE_ADD(BaseTbl.renewal_date, INTERVAL 1 YEAR),"%Y-%m-%d") as renewal_date , BaseTbl.status,count(cTrans.id) as num_of_payment,BaseTbl.isAccounting,BaseTbl.isManufacturing,BaseTbl.isFixedAsset,BaseTbl.isGrouping,BaseTbl.isHR,BaseTbl.tel_no,BaseTbl.reason,BaseTbl.updated_at,BaseTbl.updatedBy');
        $this->db->from('client as BaseTbl');
        $this->db->join('client_transactions as cTrans', 'cTrans.client_id = BaseTbl.id', 'inner');
        $this->db->join('company_type as CType', 'CType.id = BaseTbl.company_type', 'left');
        $this->db->join('tbl_users as USERS', 'USERS.id = BaseTbl.createdBy', 'left');

        if (isset($filters['company_type']) && $filters['company_type'] != -99)
            $this->db->where('BaseTbl.company_type', $filters['company_type']);
        if (isset($filters['status']) && $filters['status'] != -99)
            $this->db->where('BaseTbl.status', $filters['status']);
        if (isset($filters['isAccounting']))
            $this->db->where('BaseTbl.isAccounting', 1);
        if (isset($filters['automatedEntries']))
            $this->db->where('BaseTbl.automatedEntries', 1);
        if (isset($filters['automatedEntriesOffline']))
            $this->db->where('BaseTbl.automatedEntriesOffline', 1);
        if (isset($filters['isFoodics']))
            $this->db->where('BaseTbl.isFoodics', 1);
        if (isset($filters['isFixedAsset']))
            $this->db->where('BaseTbl.isFixedAsset', 1);
        if (isset($filters['isGrouping']))
            $this->db->where('BaseTbl.isGrouping', 1);
        if (isset($filters['isManufacturing']))
            $this->db->where('BaseTbl.isManufacturing', 1);
        if (isset($filters['isHR']))
            $this->db->where('BaseTbl.isHR', 1);
        if (isset($filters['isStore']))
            $this->db->where('BaseTbl.isStore', 1);
        if (isset($filters['isSynchronizewithZATCA']))
            $this->db->where('BaseTbl.isSynchronizewithZATCA', 1);
        if (isset($filters['isPOS']))
            $this->db->where('BaseTbl.isPOS', 1);
        if (isset($filters['agent_id']))
            $this->db->where('BaseTbl.agent_id', $filters['agent_id']);
        if ($filters['start_date'] && $filters['end_date']){
            $start_date=$filters['start_date'].' 00:00:00';
            $end_date=$filters['end_date'].' 23:59:59';
            $this->db->where("(BaseTbl.created_at  between '" . $start_date . "' and '" . $end_date . "')");
            //$this->db->where("(BaseTbl.renewal_date  between '" . $start_date . "' and '" . $end_date . "')");
        }
        $this->db->group_by('BaseTbl.id');
        $this->db->order_by('BaseTbl.id DESC');

        $query = $this->db->get();
        return $query->result();
    }

    function getCountOfWarehouses($company)
    {
            $count =0;


            $config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = 'cpesoft_'.$company;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $company;
            $config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = 'sma_';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = FALSE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = '';
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = FALSE;

            $CI = &get_instance();
            $data = $CI->load->database($config, TRUE);
			$this->mynewdb =$data;


            $count = $this->mynewdb->query("SELECT (COALESCE(count(*), 0)) as count FROM sma_warehouses")->result_array()[0]['count'];


        return $count;
    }

}
