<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS";
$USER_ID = $_SESSION['USER_ID'];

$page_primary_keys = array(
    'USER_ID' => $USER_ID,
);

//Get Pass
if(isset($_SESSION['USER_ID']) && isset($_POST['OLD_PASSWORD']) && $_POST['OLD_PASSWORD'] != NULL) {
    $usrPass = $main_app->getval_field('ASSREQ_USER_ACCOUNTS','USER_PASS','USER_ID',$USER_ID);
}


if(!isset($_SESSION['USER_ID']) || $_SESSION['USER_ID'] == NULL) {
    echo "<script> swal.fire('','Unable to validate'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['OLD_PASSWORD']) || $_POST['OLD_PASSWORD'] == NULL) {
    echo "<script> focus('OLD_PASSWORD'); swal.fire('','Enter old password'); loader_stop(); enable('sbt'); </script>";
}
elseif(!$usrPass || !password_verify($_POST['OLD_PASSWORD'],$usrPass) ) {
    echo "<script> focus('OLD_PASSWORD'); swal.fire('','Invalid Old Password'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['USR_PASSWORD1']) ||  $_POST['USR_PASSWORD1'] == NULL || strlen($_POST['USR_PASSWORD1']) < "2" ) {
    echo "<script> focus('USR_PASSWORD1'); swal.fire('','Enter new password'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['USR_PASSWORD2']) || $_POST['USR_PASSWORD2'] == NULL || strlen($_POST['USR_PASSWORD2']) < "2") {
    echo "<script> focus('USR_PASSWORD2'); swal.fire('','Enter confirm new password'); loader_stop(); enable('sbt'); </script>";
}
elseif($_POST['USR_PASSWORD1'] != $_POST['USR_PASSWORD2']) {
    echo "<script> focus('USR_PASSWORD2'); swal.fire('','New and Confirm Password does not match'); loader_stop(); enable('sbt'); </script>";
}
elseif(!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=!\?]{6,20}$/',$_POST['USR_PASSWORD1']) ) {
    echo "<script> focus('USR_PASSWORD1'); swal.fire('','New Password does not meet the requirements'); loader_stop(); enable('sbt'); </script>";
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

        $main_app->sql_db_auditlog('M',$page_table_name,$data,$page_primary_keys); // Audit Log - Modify
        $db_output = $main_app->sql_update_data($page_table_name,$data,$page_primary_keys); // Update
        if($db_output == false) { $updated_flag = false; }
    

    /** Final */
    if($updated_flag == true) {

        $go_url = ""; // Page Refresh URL
        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
        echo "<script> swal.fire({ title:'Password changed sucessfully', text:'', icon:'success', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop(); enable('sbt'); </script>";

    } else {

        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";

    }

}

?>