<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 2.2.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

// ######### App Functions ######### //

/** Browser Validation **/
$browser = new Browser();
$browser_name = $browser->getBrowser();
$browser_ver = $browser->getVersion();

if($browser_name == "Internet Explorer" && $browser_ver < "10") {
    include( DIRPATH . '/view/unsupported.php');
    exit();
}

/** Check Maintenance Mode */
if(defined('APP_MAINTENANCE') && APP_MAINTENANCE == true) {
    if(!isset($_POST['cmd']) && !isset($_GET['cmd'])) {
        require_once( DIRPATH . '/view/maintenance.php');
    }
    exit();
}

/** Stop - If Duplicate Fields in POST */
if($main_app->post_dup_fields_check() == false) {
    die('Error: Invalid Request');
}

/** CSRF Protection Token */
if(!isset($_SESSION['APP_TOKEN']) || $_SESSION['APP_TOKEN'] == NULL) {
    $main_app->session_set([ 'APP_TOKEN' => $main_app->csrf_token() ]); // New random key
}

/** Encryption Class */
if(!isset($_SESSION['SAFE_KEY']) || $_SESSION['SAFE_KEY'] == NULL) {
    $main_app->session_set([ 'SAFE_KEY' => substr(hash('sha256',mt_rand().microtime()),0,12) ]); // New random key
}

// ######### Custom Functions ######### //

/** Check User Session */
function check_usr_login() {
    //$_SESSION['USER_APP'],$_SESSION['USER_ID'],$_SESSION['USER_ROLE'],$_SESSION['USER_IP_ADD'],$_SESSION['USER_BROWSER'],$_SESSION['USER_BROWSER_VER'],$_SESSION['USER_TIMEOUT']

    global $main_app, $browser_name, $browser_ver;
    
    if(isset($_SESSION['USER_ID']) && $_SESSION['USER_ID']){
        //Check User already logged out
        $login_chk_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes")); // 10min ago time
        $add_data = array('LOGIN_USER' => $_SESSION['USER_ID']);
        $countLogins = $main_app->sql_fetchcolumn("SELECT count(0) FROM APP_LOGIN_LOGS WHERE USR_ACTIVE_FLAG = '1' AND LOGIN_TIME > '{$login_chk_date}' AND LOGIN_USER = :LOGIN_USER ",$add_data);

        if($countLogins == 0) {
            return false;
        }
    }

    if(isset($_SESSION['USER_APP']) && defined('APP_CODE') && $_SESSION['USER_APP'] == APP_CODE && isset($_SESSION['USER_ID']) && isset($_SESSION['USER_ROLE']) ) {
        if(isset($_SESSION['USER_IP_ADD']) && isset($_SESSION['USER_BROWSER']) && isset($_SESSION['USER_BROWSER_VER']) && $_SESSION['USER_IP_ADD'] == $main_app->current_ip() && $_SESSION['USER_BROWSER'] == $browser_name && $_SESSION['USER_BROWSER_VER'] == $browser_ver ) {
            if(isset($_SESSION['USER_TIMEOUT']) && defined('APP_USR_TIMEOUT') && ( time() - (int)$_SESSION['USER_TIMEOUT'] ) < APP_USR_TIMEOUT ) {
                $main_app->session_set([ 'USER_TIMEOUT' => time() ]);
                if(isset($_SESSION['USER_OTP_CHECK_REQ'])) {
                    return false;
                } else {
                    return true; // User Logged-In
                }
            }
        }
    }
    return false; // Fail
}

// function check_usr_login1() {
    
//     $userReqID = $main_app->sql_fetchcolumn("SELECT LOGIN_REQ_ID FROM APP_LOGIN_LOGS WHERE USR_ACTIVE_FLAG = '1' AND LOGIN_TIME > '{$login_chk_date}' AND LOGIN_USER = :LOGIN_USER ",$add_data);
//      $sys_datetime = date("Y-m-d H:i:s");
//         $data2 = array();
//         $data2['LOGOUT_TIME'] = $sys_datetime;
//         $data2['USR_ACTIVE_FLAG'] = "2";
//         $main_app->sql_update_data("APP_LOGIN_LOGS",$data2, array('LOGIN_REQ_ID' => $userReqID, 'LOGIN_USER' => $username));
//         //header("Location: " . APP_URL . "/login");
//         exit();

//     return false; // Fail
   
// }


/** WebApp Copyrights */
function app_copyrights() {
    return 'Copyright &copy; '.date('Y').' '.COPYRIGHT_BY;
}

/** WebApp Powered by */
function app_poweredby() {
    return '<div class="pw-link">Powered by <span class="lcode_technologies"></span> LCode</div>';
}

function display_status($StatusCode){
    if($StatusCode == "U") {
        return "<span class='badge badge-primary'>Under Review</span>";
    }
    elseif($StatusCode == "H") {
        return "<span class='badge badge-secondary'>In-Hold</span>";
    }
    elseif($StatusCode == "MH") {
        return "<span class='badge badge-secondary'>Meeting Hold</span>";
    }
    elseif($StatusCode == "M") {
        return "<span class='badge badge-warning'>Meeting Assigned</span>";
    }
    elseif($StatusCode == "P") {
        return "<span class='badge badge-info'>Approval Pending</span>";
    }
    elseif($StatusCode == "S") {
        return "<span class='badge badge-success'>Approved</span>";
    }
    elseif($StatusCode == "R") {
        return "<span class='badge badge-danger'>Rejected</span>";
    }
    else {
        return "NA";
    }
}

/** Send SMS Notification */
function send_sms($mob_num,$sms_text) {
    try {

        if(SMS_ENABLE == "YES") {

            //$mob_num = "8660677277";
            //$mob_num = "7892963815";

            //$sms_text = urlencode($sms_text); // MSG
            if(is_array($mob_num)) { $mob_num = implode(",", $mob_num); }
            //$URL = "http://bulkpush.mytoday.com/BulkSms/SingleMsgApi?senderid=UCOBNK&username=9830415156&feedid=350953&password=uco123&to=".$mob_num."&text=".$sms_text;
            $URL = "http://bulkpush.mytoday.com/BulkSms/SingleMsgApi";

            $values['senderid'] = "UCOBNK";
            $values['username'] = "9830415156";
            $values['password'] = "uco123";
            $values['feedid'] = "350953";
            $values['to'] = $mob_num;
            $values['text'] = $sms_text;

            $options = array(
                CURLOPT_RETURNTRANSFER => true,   // return web page - If you set TRUE then curl_exec returns actual result
                CURLOPT_HEADER         => false,  // Return headers
                CURLOPT_FOLLOWLOCATION => true,   // follow redirects
                CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
                CURLOPT_ENCODING       => "",     // handle compressed
                CURLOPT_USERAGENT      => "", // name of client
                CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
                CURLOPT_CONNECTTIMEOUT => 5,     // time-out on connect
                CURLOPT_TIMEOUT        => 10,     // time-out on response
                CURLOPT_SSL_VERIFYPEER => false,  // SSL verification
                CURLOPT_URL => $URL,  // URL
                CURLOPT_POST => true,  // Post method
                CURLOPT_POSTFIELDS => http_build_query($values),  // Post value
                CURLOPT_HTTPPROXYTUNNEL => 0,
                CURLOPT_PROXY => "172.19.247.10",
                CURLOPT_PROXYPORT => "8080",
            );

            $ch = curl_init(); // CURL Start
            curl_setopt_array($ch, $options); // Load CURL Options
            $response = curl_exec($ch); // Connection
            curl_close($ch); // Close CURL
            return $response;

        }

    } catch (Exception $e) {
        return false;
    }
}