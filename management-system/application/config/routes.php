<?php
defined('BASEPATH') or exit('No direct script access allowed');


$route['default_controller'] = "login";
$route['404_override'] = 'login/error';


/*********** USER DEFINED ROUTES *******************/

$route['loginMe'] = 'login/loginMe';
$route['dashboard'] = 'user';
$route['logout'] = 'user/logout';

/*********** ADMIN CONTROLLER ROUTES *******************/
$route['noaccess'] = 'login/noaccess';
$route['userListing'] = 'admin/userListing';
$route['userListing/(:num)'] = "admin/userListing/$1";
$route['clientListing'] = 'admin/clientListing';
$route['clientListing/(:num)'] = "admin/clientListing/$1";
$route['clientListingApi/(:num)'] = "admin/clientListingApi/$1";
$route['clientListingShopApi/(:num)'] = "admin/clientListingShopApi/$1";
$route['clientListingSZatca/(:num)'] = "admin/clientListingSZatca/$1";
$route['clientListingAppVersion/(:num)'] = "admin/clientListingAppVersion/$1";
$route['clientProcedures/(:num)'] = "admin/clientProcedures/$1";
$route['addNewProcedure/(:num)'] = "admin/addNewProcedure/$1";
$route['addNewProcedures/(:num)'] = "admin/addNewProcedures/$1";
$route['addNewApi/(:num)'] = "admin/addNewApi/$1";
$route['callbackShopApi/(:num)'] = "admin/callbackShopApi/$1";
$route['addNew'] = "admin/addNew";
$route['addNewClient'] = "admin/addNewClient";
$route['addNewUser'] = "admin/addNewUser";
$route['addNewClientSubmit'] = "admin/addNewClientSubmit";
$route['editOld'] = "admin/editOld";
$route['editOld/(:num)'] = "admin/editOld/$1";
$route['editOldClient'] = "admin/editOldClient";
$route['editOldClient/(:num)'] = "admin/editOldClient/$1";
$route['editOldClientDT/(:num)'] = "admin/editOldClientDT/$1";
$route['whatsAppMessages/(:num)'] = "admin/whatsAppMessages/$1";

$route['editApiSync/(:any)/(:any)/(:any)'] = "admin/editApiSync/$1/$2/$3";
$route['editUser'] = "admin/editUser";
$route['editClient'] = "admin/editClient";
$route['addClientSubscription'] = "admin/addClientSubscription";
$route['addZatcaOTP'] = "admin/addZatcaOTP";
$route['addZatcaOTP2'] = "admin/addZatcaOTP2";
$route['addClientService'] = "admin/addClientService";
$route['deleteUser'] = "admin/deleteUser";
$route['log-history'] = "admin/logHistory";
$route['log-history-backup'] = "admin/logHistoryBackup";
$route['log-history/(:num)'] = "admin/logHistorysingle/$1";
$route['log-history/(:num)/(:num)'] = "admin/logHistorysingle/$1/$2";
$route['backupLogTable'] = "admin/backupLogTable";
$route['backupLogTableDelete'] = "admin/backupLogTableDelete";
$route['log-history-upload'] = "admin/logHistoryUpload";
$route['logHistoryUploadFile'] = "admin/logHistoryUploadFile";
$route['addClientBranch'] = "admin/addClientBranch";
$route['addClientWarehouse'] = "admin/addClientWarehouse";
$route['addClientUser'] = "admin/addClientUser";
$route['addClientNearpay'] = "admin/addClientNearpay";
$route['addElectronicContract'] = "admin/addElectronicContract";
$route['sendWhatsAppDodument/(:any)/(:num)'] = "admin/sendWhatsAppDodument/$1/$2";

$route['complaints'] = "admin/complaints";
$route['editOldComplaint/(:num)'] = "admin/editOldComplaint/$1";
$route['editComplaint'] = "admin/editComplaint";
$route['complaintsNote/(:num)'] = "admin/complaintsNote/$1";

$route['addWhatsAppSettings'] = "admin/addWhatsAppSettings";

/*********** MANAGER CONTROLLER ROUTES *******************/
$route['tasks'] = "manager/tasks";
$route['addNewTask'] = "manager/addNewTask";
$route['addNewTasks'] = "manager/addNewTasks";
$route['editOldTask/(:num)'] = "manager/editOldTask/$1";
$route['editTask'] = "manager/editTask";
$route['deleteTask/(:num)'] = "manager/deleteTask/$1";

/*********** USER CONTROLLER ROUTES *******************/
$route['loadChangePass'] = "user/loadChangePass";
$route['changePassword'] = "user/changePassword";
$route['pageNotFound'] = "user/pageNotFound";
$route['checkEmailExists'] = "user/checkEmailExists";
$route['endTask/(:num)'] = "user/endTask/$1";
$route['etasks'] = "user/etasks";
$route['userEdit'] = "user/loadUserEdit";
$route['updateUser'] = "user/updateUser";


/*********** LOGIN CONTROLLER ROUTES *******************/
$route['forgotPassword'] = "login/forgotPassword";
$route['resetPasswordUser'] = "login/resetPasswordUser";
$route['resetPasswordConfirmUser'] = "login/resetPasswordConfirmUser";
$route['resetPasswordConfirmUser/(:any)'] = "login/resetPasswordConfirmUser/$1";
$route['resetPasswordConfirmUser/(:any)/(:any)'] = "login/resetPasswordConfirmUser/$1/$2";
$route['createPasswordUser'] = "login/createPasswordUser";

/*********** Reports CONTROLLER ROUTES *******************/
$route['clientsReports'] = "reports/clients";
$route['salesReports'] = "reports/sales";


/*********** Agents CONTROLLER ROUTES *******************/
$route['agents'] = "agents";
$route['agents/(:num)'] = "agents/edit/$1";
$route['agents/new'] = "agents/addNew";
$route['agents/new/process'] = "agents/newAgentProcess";
$route['agents/update/process'] = "agents/updateAgentProcess";
$route['agents/history/(:num)'] = "agents/agentHistory/$1";


/* End of file routes.php */
/* Location: ./application/config/routes.php */