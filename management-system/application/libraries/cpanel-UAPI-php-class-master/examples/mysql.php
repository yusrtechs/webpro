<?php

/**
 * For documentation on cPanel's UAPI:
 * @see https://documentation.cpanel.net/display/SDK/UAPI+Functions
 *
 * @author N1ghteyes - www.source-control.co.uk
 * @copyright 2014 N1ghteyes
 * @license license.txt The MIT License (MIT)
 * @link https://github.com/N1ghteyes/cpanel-UAPI-php-class
 */
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

include "../cpaneluapi.class.php"; //include the class file
$uapi = new cpanelAPI('dkfhd', 'waleed@N7', 'premium212.web-hosting.com'); //instantiate the object

$database = 'dkfhd_alamal';
$databaseuser = 'dkfhd_alamal';

$chars = "abcdefghijklmnopqrstuvwxyzQWERTYUIOPASDFGHJKLZXCVBNM0123456789"; 
	    srand(microtime(true)*1000000); 
	    $i = 0; 
	    $pass = '' ; 
	
	    while ($i < 10) { 
	        $pass = $pass . $chars[rand(0,strlen($chars)-1)];
	        $i++; 
	    } 
	


$databasepass = $pass;

/**
 * Mysql - Create a database and user, then assign the user to that database.
 * For a full list of functions available for the Mysql module, see: https://documentation.cpanel.net/display/SDK/Mysql
 * Mysql requires cPanel 11.44 +
 */


//If database prefixing is enabled, this parameter must include the database prefix for the account.
//This is normally the account username, followed by an underscore. e.g. cPuser_database.
// ----
//Arguments are passed by an array, where a url parameter of ?name=database is needed, it is passed with
//the array key as the parameter e.g. array('name' => 'database').

$rez=$uapi->uapi->Mysql->create_database(array('name' => $database)); //Create the database
//echo "<strong>New database</strong> : \n",print_r($rez, true), "\n";

$uapi->uapi->Mysql->create_user(array('name' => $databaseuser, 'password' => $databasepass)); //create a user for the new database


//After you create the user, you must use the set_privileges_on_database function call to grant access to the
//user for a database.
//add the user, set all privileges - add specific privileges by comma separation. e.g. 'DELETE,UPDATE,CREATE,ALTER'
$uapi->uapi->Mysql->set_privileges_on_database(array('user' => $databaseuser, 'database' => $database, 'privileges' => 'ALL'));
