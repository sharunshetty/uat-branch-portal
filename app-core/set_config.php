<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 2.2.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

// ######### Custom Configurations ######### //

/** Default Timezone */
date_default_timezone_set('Asia/Calcutta');

/** Runtime Errors */
if(APP_DEBUG == true) {
    ini_set('display_errors', 'On'); // Enable or Disable public display of errors (use 'On' or 'Off')
    ini_set('log_errors', 'Off'); // Enable or Disable PHP error logging (use 'On' or 'Off')
} else {
    ini_set('display_errors', 'Off'); // Production
    ini_set('log_errors', 'On');
}

/** Session Settings */
//ini_set('session.cookie_domain', '.lcodetechnologies.com');
ini_set('session.cookie_lifetime', 60*60*24*1); // Cookie expiry - 1 day
ini_set('session.gc_maxlifetime', 60*60*24*1); // Session expiry - 1 day
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
if(APP_PRODUCTION == true) { ini_set('session.cookie_httponly', 1); }
if(APP_SSL_MODE == true) { ini_set('session.cookie_secure', 1); } // For HTTPS Only

/** Disabling Click Hijacking & X-XSS Attack */
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('X-Powered-By: LCode Technologies');
//header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval'; img-src * 'self' data: http: https:;");
//header('X-Content-Type-Options: nosniff');

/** Start Session **/
session_name('LCode-WebApp');
session_start([
    'read_and_close' => true,
]);
