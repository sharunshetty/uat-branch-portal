<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$pgm_code = "USERPWDRESET"; // Pgm. Code
$pgm_auth_req = "1"; // Authorization Required : 1-Yes, 0-No

if(isset($_POST['id']) && $_POST['id'] != "") { $primary_value = $safe->str_decrypt($_POST['id'],$_SESSION['SAFE_KEY']); } else { $primary_value = ""; }

if(!isset($primary_value) || $primary_value == false || $primary_value == "") {
    echo "<script> swal.fire('','Invalid Request'); loader_stop(); enable('sbt2'); </script>";
    exit();
}

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS";

$page_primary_keys = array(
    'USER_ID' => (isset($primary_value)) ? $primary_value : "",
);

$sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID",$page_primary_keys);
$item_data = $sql_exe->fetch();


if(!isset($item_data['USER_ID']) || $item_data['USER_ID'] != $primary_value) {
    echo "<script> swal.fire('','Invalid User'); loader_stop(); enable('sbt2'); </script>";
}
elseif(!isset($_POST['USR_PASSWORD1']) ||  $_POST['USR_PASSWORD1'] == NULL || strlen($_POST['USR_PASSWORD1']) < "2" ) {
    echo "<script> focus('USR_PASSWORD1'); swal.fire('','Enter new password'); loader_stop(); enable('sbt2'); </script>";
}
elseif(!isset($_POST['USR_PASSWORD2']) || $_POST['USR_PASSWORD2'] == NULL || strlen($_POST['USR_PASSWORD2']) < "2") {
    echo "<script> focus('USR_PASSWORD2'); swal.fire('','Enter confirm new password'); loader_stop(); enable('sbt2'); </script>";
}
elseif($_POST['USR_PASSWORD1'] != $_POST['USR_PASSWORD2']) {
    echo "<script> focus('USR_PASSWORD2'); swal.fire('','New and Confirm Password does not match'); loader_stop(); enable('sbt2'); </script>";
}
elseif(!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=!\?]{6,20}$/',$_POST['USR_PASSWORD1']) ) {
    echo "<script> focus('USR_PASSWORD1'); swal.fire('','New Password does not meet the requirements'); loader_stop(); enable('sbt2'); </script>";
}
else {

    $main_app->sql_db_start(); // Start - DB Transaction
    
    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    $data = array();

    // //Update
    $hashed_password = password_hash($_POST['USR_PASSWORD1'], PASSWORD_DEFAULT);
    $data['USER_PASS'] = $hashed_password;
    $data['PASS_CHG_DATE'] = $sys_datetime;

    /* Auth. Required */
    if($pgm_auth_req == "1") {

        try {
            $result = $authtba2->setAuthIndex($pgm_code, "M", $page_table_name, $page_primary_keys);
            if($result) { $authtba2->pushRecordEntry($pgm_code, "M", $page_table_name, $page_primary_keys, $data); }
            if($result == false) { $updated_flag = false; }
        } catch (\Throwable $th) {
            $pkg_error_msg = $th->getMessage();
            $updated_flag = false;
        }

    }
    else {

        $main_app->sql_db_auditlog('M',$page_table_name,$data,$page_primary_keys); // Audit Log - Modify
        $db_output = $main_app->sql_update_data($page_table_name,$data,$page_primary_keys); // Update
        if($db_output == false) { $updated_flag = false; }

    }

    /** Final */
    if($updated_flag == true) {

        $go_url = ""; // Page Refresh URL
        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
        echo "<script> swal.fire({ title:'Password reset done sucessfully', text:'', icon:'success', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop(); enable('sbt2'); </script>";

    } else {

        $pkg_error_msg = ($pkg_error_msg != "") ? "Error: ". $pkg_error_msg : "Unable to update content";
        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text: decode_ajax('".$main_app->strsafe_ajax($pkg_error_msg)."'), icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt2'); </script>";

    }

}
?>