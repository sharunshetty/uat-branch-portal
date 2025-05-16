<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 1.0.0
 * @project     : Assisted Branch Module
 * @client      : Capital Small Finance Bank
 * @created     : August 2020
 * @pro_version : 1.0.2
 * @developers  : Shivananda (Madhukar)
 * Last Updated : 27/08/2020
 **/

/** Product Configuration */
define('PRODUCT_NAME', 'Branch Portal');
define('PRODUCT_VERSION', '1.0.0');

/** Application */
define('APP_CODE', 'Branch Portal');
define('APP_URL', '/uat-branch-portal/web-app');

/** Content Delivery Network */
define('CDN_URL', '/uat-branch-portal');
define('CDN_VER', '1.0.3');

/** Application Settings */
define('APP_PRODUCTION', false); // TRUE = Production Server, FALSE = UAT Server
define('APP_SSL_MODE', false); // HTTPS: TRUE - Enabled, FALSE - Disabled
define('APP_DEBUG', false); // Debug mode: TRUE - Enabled, FALSE - Disabled
define('APP_DB_AUDIT_LOG', false); // TRUE - Enabled, FALSE - Disabled
define('APP_MAINTENANCE', false);
define('DIRPATH', dirname(__FILE__)); // Core path

/** ReachAPI Remote API Server */
if(APP_PRODUCTION == true) {

    //Reach API
    //define('API_REMOTE_ADDRESS', 'https://reach.lcodetechnologies.com/ReachMB/GoldAPI');

    //Public Files (*Not Secure*)
   

    define('API_REMOTE_ADDRESS', ''); // Internal Production server
    define('API_REACH_MB_CHANNEL', 'CSFBWEB'); // Reach Channel ID
    /** AES Secret Key & IV & Random key */
    $safe_secret_key = "LCode@2012!#INTR";
    $safe_iv_key = "c664f7b3d8d9141e";
    
    // Documents Folder
    define('UPLOAD_DOCS_DIR', '/LCode/VKYC-Data');

    define('UPLOAD_PUBLIC_DIR', 'D:/UPLOADED-DATA');
    define('UPLOAD_PUBLIC_CDN_URL', '/uat-branch-portal/uploads');

} else {

    //Reach API
    //define('API_REMOTE_ADDRESS', 'https://reach.lcodetechnologies.com/ReachMB/GoldAPI');

    define('API_REMOTE_ADDRESS', 'http://172.31.0.163:18080/ReachAcOpen/OnlineAsstAcOpenApi'); // Internal Production server
    define('API_REACH_MB_CHANNEL', 'CSFBWEB'); // Reach Channel ID

    /** AES Secret Key & IV & Random key */
    $safe_secret_key = "LCode@2012!#INTR";
    $safe_iv_key = "c664f7b3d8d9141e";

     // Documents Folder
    define('UPLOAD_DOCS_DIR', '/LCode/VKYC-Data');
    
    // Public Files (*Not Secure*)
    define('UPLOAD_PUBLIC_DIR', '/var/www/html/uat-branch-portal/uploads');
    define('UPLOAD_PUBLIC_CDN_URL', '/uat-branch-portal/uploads');

}

/*
Stop direct HTTP Requests
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != "LCode") {
    die('Invalid Request');
}
*/

/** User Idle Timeout */
define('APP_USR_TIMEOUT', '3600'); // Value in seconds: 60 = 1Min

/** Start Database Connection */
require_once( DIRPATH . '/database_conn.php');

$db = new Database();
$db_master = $db->db_connect_master(true); // DB Connection

/** App Settings */
require_once( DIRPATH . '/app_settings.php');

/** Web Configuration */
require_once( DIRPATH . '/set_config.php');

/** Custom Class */
require_once( DIRPATH . '/set_custom_class.php');

$main_app = new LCodeWebApp($db_master);
$safe = new Encryption();
$authtba2 = new tba2Auth();

/** Custom Functions */
require_once( DIRPATH . '/set_custom_func.php');

// ######### Session Keys ######### //

$sess_data = array();

if(!isset($_SESSION['SAFE_KEY']) || $_SESSION['SAFE_KEY'] == NULL) {
    $sess_data['SAFE_KEY'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20); // New random key
}

/** CSRF Protection Token */
if(!isset($_SESSION['APP_TOKEN']) || $_SESSION['APP_TOKEN'] == NULL) {
    $sess_data['APP_TOKEN'] = $main_app->csrf_token(); // New random key
}

if(count($sess_data) > "0") {
    $main_app->session_set($sess_data); // Set Session
}