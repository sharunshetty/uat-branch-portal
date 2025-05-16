<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

//Logout Time
if(isset($_SESSION['USER_APP']) && $_SESSION['USER_APP'] == APP_CODE && isset($_SESSION['USER_LOGIN_ID']) && isset($_SESSION['USER_ID'])) {
    if($_SESSION['USER_LOGIN_ID'] != NULL && $_SESSION['USER_ID'] != NULL) {
        $sys_datetime = date("Y-m-d H:i:s");
        $data2 = array();
        $data2['LOGOUT_TIME'] = $sys_datetime;
        $data2['USR_ACTIVE_FLAG'] = "2";
        // $data2['MO_BY'] = $_SESSION['USER_ID'];
        // $data2['MO_ON'] = $sys_datetime;
        $main_app->sql_update_data("APP_LOGIN_LOGS",$data2, array('LOGIN_REQ_ID' => $_SESSION['USER_LOGIN_ID'], 'LOGIN_USER' => $_SESSION['USER_ID']));
    }
}

// Closing User Session
session_start();
$_SESSION = array();
session_regenerate_id(true); // Re-Generating New Session
session_destroy(); // Logout done
session_write_close();

/** Redirect */
header("Location: " . APP_URL . "/login");
