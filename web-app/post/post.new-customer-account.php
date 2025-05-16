<?php

/**
 * @copyright   : (c) 2018 Copyright by LCode Technologies
 * @author      : Sujith Kamath
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$pgm_code = "CUSTOMERS"; // Pgm. Code

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASS_BRNUSR_ID";

if(!isset($_POST['CUST_TITLE']) || $_POST['CUST_TITLE'] == NULL || $_POST['CUST_TITLE'] == "") {
    echo "<script> focus('CUST_TITLE'); swal.fire('','Please select valid Customer Title'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['CUST_FULLNAME']) || $main_app->valid_text($_POST['CUST_FULLNAME']) == false || $main_app->wordlen($_POST['CUST_FULLNAME']) > "120") {
    echo "<script> focus('CUST_FULLNAME'); swal.fire('','Enter valid First Name'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['CUST_GENDER']) || $_POST['CUST_GENDER'] == NULL || $_POST['CUST_GENDER'] == "") {
    echo "<script> focus('CUST_GENDER'); swal.fire('','Please select valid Customer Gender'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}
elseif (!isset($_POST['CUST_MOBILE']) || $_POST['CUST_MOBILE'] == NULL || $main_app->valid_mobile($_POST['CUST_MOBILE']) == false) {
    echo "<script> focus('CUST_MOBILE'); swal.fire('','Please enter the valid Mobile Number'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['MOBVALID_OTP']) || $_POST['MOBVALID_OTP'] == NULL ||$_POST['MOBVALID_OTP'] == "") {
    echo "<script> swal.fire('','Please enter the OTP sent on Mobile'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['CUST_EMAIL']) || $_POST['CUST_EMAIL'] == NULL || $main_app->valid_email($_POST['CUST_EMAIL']) == false ) {
    echo "<script> focus('CUST_EMAIL'); swal.fire('','Please enter the valid Email ID'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['MAILVALID_OTP']) || $_POST['MAILVALID_OTP'] == NULL || $_POST['MAILVALID_OTP'] == "") {
    echo "<script> swal.fire('','Please enter OTP received on Email ID'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}
//Check OTP Session
elseif(!isset($_SESSION['USER_APP']) || $_SESSION['USER_APP'] != APP_CODE || !isset($_SESSION['MOBOTP_REQ_ID'])|| !isset($_SESSION['EMAILOTP_REQ_ID'])) {
    echo "<script> sess_error('Session expired. Please try again.'); loader_stop('myform'); </script>";
    exit();
}
elseif(!isset($_SESSION['CUST_MOBILE']) || $_SESSION['CUST_MOBILE'] == NULL || $_SESSION['CUST_MOBILE'] == "") {
    echo "<script> sess_error('Session expired. Please try again.'); loader_stop('myform'); </script>";
    exit();
}
elseif(!isset($_SESSION['CUST_EMAIL']) || $_SESSION['CUST_EMAIL'] == NULL || $_SESSION['CUST_EMAIL'] == "") {
    echo "<script> sess_error('Session expired. Please try again.'); loader_stop('myform'); </script>";
    exit();
}
elseif(!isset($_SESSION['OTP_SMS_CODE']) || $_SESSION['OTP_SMS_CODE'] == NULL || $_SESSION['OTP_SMS_CODE'] == "") {
    echo "<script> sess_error('Session expired. Please try again.'); loader_stop('myform'); </script>";
    exit();
}
elseif(!isset($_SESSION['OTP_EMAIL_CODE']) || $_SESSION['OTP_EMAIL_CODE'] == NULL || $_SESSION['OTP_EMAIL_CODE'] == "") {
    echo "<script> sess_error('Session expired. Please try again.'); loader_stop('myform'); </script>";
    exit();
}
elseif($_POST['MOBVALID_OTP'] != $_SESSION['OTP_SMS_CODE']) {
    // Update Session
    echo "<script> swal.fire('','Mobile No OTP entered is incorrect'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}
elseif($_POST['MAILVALID_OTP'] != $_SESSION['OTP_EMAIL_CODE']) {
    // Update Session
    echo "<script> swal.fire('','Email ID OTP entered is incorrect'); loader_stop('myform'); enable('sbt'); </script>";
    exit();
}else {

    $AppRefNum = $main_app->sql_sequence("SBREQ_MASTER_SEQ","ABP"); // Seq. No.  
    if(!$AppRefNum || $AppRefNum == false || $AppRefNum == "1") {
        echo "<script> swal.fire('','An error has occurred: Unable to generate reference number'); loader_stop('myform'); enable('sbt'); </script>";
        exit();
    }

    // Update DB MOB
    $data1 = array();
    $data1['SMS_VERIFIED_FLAG'] = "S";
    $data1['SMS_VERIFIED_ON'] = date("Y-m-d H:i:s");
    $db_output1 = $main_app->sql_update_data("LOG_OTPREQ", $data1, array('OTP_REQ_ID' => $_SESSION['MOBOTP_REQ_ID'])); // Update
    if($db_output1 == false) {
        echo "<script> swal.fire('','Unable to process your request (D01)'); loader_stop('myform'); </script>";
        exit();
    }

    // Update DB EMAIL
    $data2 = array();
    $data2['EMAIL_VERIFIED_FLAG'] = "S";
    $data2['EMAIL_VERIFIED_ON'] = date("Y-m-d H:i:s");
    $db_output2 = $main_app->sql_update_data("LOG_OTPREQ", $data2, array('OTP_REQ_ID' => $_SESSION['EMAILOTP_REQ_ID'])); // Update
    if($db_output2 == false) {
        echo "<script> swal.fire('','Unable to process your request (D01)'); loader_stop('myform'); </script>";
        exit();
    }

    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");
    
    $data = array();
    $data['ASSREQ_REF_NUM'] = $AppRefNum;
    $data['ASSREQ_CUST_TITLE'] = (isset($_POST['CUST_TITLE']) && $_POST['CUST_TITLE'] != "") ? $_POST['CUST_TITLE'] : NULL;
    $data['ASSREQ_CUST_FNAME'] = (isset($_POST['CUST_FULLNAME']) && $_POST['CUST_FULLNAME'] != "") ? $_POST['CUST_FULLNAME'] : NULL;
    $data['ASSREQ_CUST_GENDER'] = (isset($_POST['CUST_GENDER']) && $_POST['CUST_GENDER'] != "") ? $_POST['CUST_GENDER'] : NULL;
    $data['ASSREQ_MOBILE_NUM'] = (isset($_POST['CUST_MOBILE']) && $_POST['CUST_MOBILE'] != "") ? $_POST['CUST_MOBILE'] : NULL;
    $data['ASSREQ_EMAIL'] = (isset($_POST['CUST_EMAIL']) && $_POST['CUST_EMAIL'] != "") ? $_POST['CUST_EMAIL'] : NULL;
    $data['REFERRAL_CODE'] = (isset($_POST['REGREFERRAL']) && $_POST['REGREFERRAL'] != "") ? $_POST['REGREFERRAL'] : NULL;
    $data['MOBILE_SMS_STATUS']= 'S';
    $data['MOBILE_OTP_STATUS']= 'V';
    $data['EMAIL_STATUS']= 'S';
    $data['EMAIL_OTP_STATUS']= 'V';
    $data['CR_BY'] = $_SESSION['USER_ID'];
    $data['CR_ON'] = $sys_datetime;
    $data['BRANCH_CODE'] = $_SESSION['BRANCH_CODE'];
    
   
	$main_app->sql_db_auditlog('A',$page_table_name,$data); // Audit Log - DB Transaction
    $db_output = $main_app->sql_insert_data($page_table_name,$data); // Insert
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == true) {
        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']);
        $main_app->session_remove([ 'OTP_SMS_CODE', 'OTP_EMAIL_CODE']);
        $main_app->session_remove([ 'CUST_MOBILE', 'CUST_EMAIL']);

        $sid_assref_num = $safe->str_encrypt($AppRefNum, $_SESSION['SAFE_KEY']);
        $go_url = "ass-customer-detail?ref_Num=".$sid_assref_num; // Page Refresh URL

     	echo "<script> swal.fire({ title:'Record Updated', text:'', icon:'success', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop('myform'); enable('sbt'); </script>";
    } else {
        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop('myform'); enable('sbt'); </script>";    
    }
  
}
?>