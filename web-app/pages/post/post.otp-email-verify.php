<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

//Clean Code
if( isset($_POST['LOGIN_EMAIL_OTP']) ) {
    $_POST['LOGIN_EMAIL_OTP'] = preg_replace('/\s+/', '', $_POST['LOGIN_EMAIL_OTP']);
}

/** SQL */
$page_table_name = "APP_LOGIN_LOGS";
$page_primary_keys = array(
    'LOGIN_REQ_ID' => (isset($_SESSION['USER_LOGIN_ID'])) ? $main_app->strsafe_input($_SESSION['USER_LOGIN_ID']) : "",
);

if($_SESSION['USER_LOGIN_ID'] != "" && $_POST['LOGIN_EMAIL_OTP'] != "") {
    $sql_exe = $main_app->sql_run("SELECT * FROM $page_table_name WHERE LOGIN_REQ_ID = :LOGIN_REQ_ID AND LOGIN_OTP_STATUS = '1'", array('LOGIN_REQ_ID' => $_SESSION['USER_LOGIN_ID']));
    $item_data = $sql_exe->fetch();
}

// Start : Link
if( !isset($_SESSION['APP_TOKEN']) || $_POST['token'] != $_SESSION['APP_TOKEN'] ) {
    echo "<script> swal.fire('','The request timed out'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['LOGIN_EMAIL_OTP']) || $_POST['LOGIN_EMAIL_OTP'] == NULL) {
    echo "<script> swal.fire('','Enter valid otp code'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($item_data['LOGIN_REQ_ID']) || $item_data['LOGIN_REQ_ID'] == NULL) {
    echo "<script> swal.fire('','No OTP generated'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(isset($_SESSION['LOGIN_EMAILOTP_CHECK_COUNT']) && $_SESSION['LOGIN_EMAILOTP_CHECK_COUNT'] > "3") {
    $go_url = APP_URL."/logout";
    echo "<script> swal.fire({ title:'Maximum otp attempts consumed, Please login again', text:'', icon:'info', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop(); </script>";
    exit();
}
elseif($_POST['LOGIN_EMAIL_OTP'] != $item_data['OTP_CODE']) {
    echo "<script> swal.fire('','OTP entered is incorrect'); loader_stop(); enable('sbt'); </script>";
    session_start();
    $_SESSION['LOGIN_EMAILOTP_CHECK_COUNT'] = (isset($_SESSION['LOGIN_EMAILOTP_CHECK_COUNT']) && $_SESSION['LOGIN_EMAILOTP_CHECK_COUNT'] != NULL) ? $_SESSION['LOGIN_EMAILOTP_CHECK_COUNT'] + "1" : "1";
    session_write_close();
}
else {

    $main_app->sql_db_start(); // Start - DB Transaction
    
    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    session_start();
    unset($_SESSION['USER_OTP_CHECK_REQ']); // Unset otp check
    unset($_SESSION['OTP_EMAIL_CHK']); // Unset otp check

    //unset($_SESSION['LOGIN_OTP_CHECK_COUNT']); // Unset otp count check
    //unset($_SESSION['LOGIN_EMAILOTP_CHECK_COUNT']); // Unset otp count check

    session_regenerate_id(TRUE); // Regenerate user session
    session_write_close();

    $data = array();
    $data['LOGIN_OTP_STATUS'] = "S";
    $data['MO_BY'] = $_SESSION['USER_ID'];
    $data['MO_ON'] = $sys_datetime;

    $main_app->sql_db_auditlog('M',$page_table_name,$data,$page_primary_keys); // Audit Log - Modify
    $db_output = $main_app->sql_update_data($page_table_name,$data,$page_primary_keys); // Update
    if($db_output == false) { $updated_flag = false; }

    
    /** Final */
    if($updated_flag == true) {

        $go_url = APP_URL. "/"; // Page Refresh URL
        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
        echo "<script> goto_url('" . $go_url . "'); </script>";

    } else {

        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to your process request', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";

    }

}