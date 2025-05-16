<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";

if(isset($_POST['ASSREF_NUM']) && $_POST['ASSREF_NUM'] != "") {
    $assref_num = $safe->str_decrypt($_POST['ASSREF_NUM'], $_SESSION['SAFE_KEY']); 
    if($assref_num) {
        $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM FROM $page_table_name WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM ", array('ASSREQ_REF_NUM' => $assref_num));     
        $item_data = $sql_exe->fetch();
    }
}
if(!isset($item_data) || $item_data == "" || $item_data == NULL) {
    echo "<script> swal.fire('','Unable to validate request.'); loader_stop(); enable('sbt');</script>";
    exit();
}
if(!isset($_POST['CUST_FULLNAME']) || $main_app->valid_text($_POST['CUST_FULLNAME']) == false || $main_app->wordlen($_POST['CUST_FULLNAME']) > "120") {
    echo "<script> focus('CUST_FULLNAME'); swal.fire('','Enter valid First Name'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($assref_num) || $assref_num == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt'); </script>";
}
else{

    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");
    $main_app->sql_db_start(); // Start - DB Transaction

    $data = array();
    $data['ASSREQ_CUST_FNAME'] = (isset($_POST['CUST_FULLNAME']) && $_POST['CUST_FULLNAME'] != "") ? $_POST['CUST_FULLNAME'] : NULL;
    $data['REFERRAL_CODE'] = (isset($_POST['REGREFERRAL']) && $_POST['REGREFERRAL'] != "") ? $_POST['REGREFERRAL'] : NULL;
    $data['ASSREQ_CUST_FLAG'] = "Y";
    $data['MO_BY'] = $_SESSION['USER_ID'];
    $data['MO_ON'] = date("Y-m-d H:i:s");

    $main_app->sql_db_auditlog('A',$page_table_name,$data); // Audit Log - DB Transaction
    $db_output = $main_app->sql_update_data($page_table_name,$data, array("ASSREQ_REF_NUM" => $item_data['ASSREQ_REF_NUM'])); // Update
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == true) {

        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
        $sid_assref_num = $safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']);
        //echo "<script> $('#CUST_REF_NUM').val('".$sid_assref_num."'); </script>";
        $go_url = "ass-aadhaar-details?ref_Num=".$sid_assref_num; // Page Refresh URL
        echo "<script> goto_url('" . $go_url . "');</script>";
    
    } else {

        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";
    
    }
    
}


?>