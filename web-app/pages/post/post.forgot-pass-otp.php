<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

//Clean Code
if( isset($_POST['FP_OTP']) ) {
    $_POST['FP_OTP'] = preg_replace('/\s+/', '', $_POST['FP_OTP']);
}

/** Password Decryption */
$safe = new Encryption();

$fp_otp = $safe->rsa_decrypt($_POST['FP_OTP']);
$user_oldpassword = $safe->rsa_decrypt($_POST['USR_PASSWORD1']);
$user_newpass = $safe->rsa_decrypt($_POST['USR_PASSWORD2']);

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS";
$page_primary_keys = array(
    'USER_ID' => (isset($_SESSION['FP_USER_ID'])) ? $main_app->strsafe_input($_SESSION['FP_USER_ID']) : "",
);

$page_table_name2 = "APP_FORGOTPASS_LOGS";
$page_primary_keys2 = array(
    'FP_USER_ID' => (isset($_SESSION['FP_USER_ID'])) ? $main_app->strsafe_input($_SESSION['FP_USER_ID']) : "",
    'FP_DTL_SL' => (isset($_SESSION['FP_USER_DTL_SL'])) ? $main_app->strsafe_input($_SESSION['FP_USER_DTL_SL']) : "",
);

if($_SESSION['FP_USER_ID'] != "" && $_SESSION['FP_USER_DTL_SL'] != "" && $fp_otp != false) {
    $sql_exe = $main_app->sql_run("SELECT * FROM $page_table_name2 WHERE FP_USER_ID = :FP_USER_ID AND FP_DTL_SL = :FP_DTL_SL AND FP_OTP_STATUS = '2'", array('FP_USER_ID' => $_SESSION['FP_USER_ID'], 'FP_DTL_SL' => $_SESSION['FP_USER_DTL_SL']));
    $item_data = $sql_exe->fetch();
}

// Start : Link
if( !isset($_SESSION['APP_TOKEN']) || $_POST['token'] != $_SESSION['APP_TOKEN'] ) {
    echo "<script> swal.fire('','The request timed out'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($fp_otp) || $fp_otp == false) {
    echo "<script> swal.fire('','Enter valid otp code'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($item_data['FP_DTL_SL']) || $item_data['FP_DTL_SL'] == NULL) {
    echo "<script> swal.fire('','No OTP generated'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(isset($_SESSION['FP_OTP_CHECK_COUNT']) && $_SESSION['FP_OTP_CHECK_COUNT'] > "3") {
    $go_url = APP_URL."/logout";
    echo "<script> swal.fire({ title:'Maximum otp attempts consumed, Please try again', text:'', icon:'info', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop(); </script>";
    exit();
}
elseif($fp_otp != $item_data['FP_OTP_CODE']) {
    echo "<script> swal.fire('','OTP entered is incorrect'); loader_stop(); enable('sbt'); </script>";
    session_start();
    $_SESSION['FP_OTP_CHECK_COUNT'] = (isset($_SESSION['FP_OTP_CHECK_COUNT']) && $_SESSION['FP_OTP_CHECK_COUNT'] != NULL) ? $_SESSION['FP_OTP_CHECK_COUNT'] + "1" : "1";
    session_write_close();
}
elseif(!isset($user_oldpassword) ||  $user_oldpassword == false || strlen($user_oldpassword) < "2" ) {
    echo "<script> focus('USR_PASSWORD1'); swal.fire('','Enter new password'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($user_newpass) || $user_newpass == false || strlen($user_newpass) < "2") {
    echo "<script> focus('USR_PASSWORD2'); swal.fire('','Enter confirm new password'); loader_stop(); enable('sbt'); </script>";
}
elseif($user_oldpassword != $user_newpass) {
    echo "<script> focus('USR_PASSWORD2'); swal.fire('','New and Confirm Password does not match'); loader_stop(); enable('sbt'); </script>";
}
elseif(!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=!\?]{6,20}$/',$user_oldpassword) ) {
    echo "<script> focus('USR_PASSWORD1'); swal.fire('','New Password does not meet the requirements'); loader_stop(); enable('sbt'); </script>";
}
else {

    $main_app->sql_db_start(); // Start - DB Transaction
    
    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    session_start();
    unset($_SESSION['FP_OTP_CHECK_REQ']); // Unset otp check
    session_regenerate_id(TRUE); // Regenerate user session
    session_write_close();

    $data = array();

    //Update Password
    $hashed_password = password_hash($user_oldpassword, PASSWORD_DEFAULT);
    $data['USER_PASS'] = $hashed_password;
    $data['PASS_CHG_DATE'] = $sys_datetime;

    $main_app->sql_db_auditlog('M',$page_table_name,$data,$page_primary_keys); // Audit Log - Modify
    $db_output = $main_app->sql_update_data($page_table_name,$data,$page_primary_keys); // Update
    if($db_output == false) { $updated_flag = false; }

    if($hashed_password && $updated_flag == true) {

        //Update Forgot Log
        $data2 = array();
        $data2['FP_OTP_STATUS'] = "1";
        $data2['MO_ON'] = $sys_datetime;

        $main_app->sql_db_auditlog('M',$page_table_name2,$data2,$page_primary_keys2); // Audit Log - Modify
        $db_output = $main_app->sql_update_data($page_table_name2,$data2,$page_primary_keys2); // Update
        if($db_output == false) { $updated_flag = false; }

    } else {
        $updated_flag = false;
    }

    /** Final */
    if($updated_flag == true) {

        $go_url = APP_URL."/logout"; // Page Refresh URL
        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
        echo "<script> swal.fire({ title:'Password Changed', text:'', icon:'success', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop(); </script>";

    } else {

        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to your process request', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";

    }

}