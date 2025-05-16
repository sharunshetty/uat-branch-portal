<?php
/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

$page_table_name = "ASSREQ_ACCOUNTDATA";
$primary_key = "ASSREQ_REF_NUM";

if(isset($_POST['asnVal']) && $_POST['asnVal'] != "") {
    $assref_num = $safe->str_decrypt($_POST['asnVal'], $_SESSION['SAFE_KEY']); 
    if($assref_num) {
        //$sql_exe = $main_app->sql_run("SELECT REKYC_REF_NUM, REKYC_CUST_ID FROM $page_table_name WHERE REKYC_REF_NUM = :REKYC_REF_NUM AND REKYC_CUST_ID = :REKYC_CUST_ID AND REKYC_STATUS = 'D'", array('REKYC_REF_NUM' => $ref_num, 'REKYC_CUST_ID' => $_SESSION['USR_ID']));
        $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM ", array('ASSREQ_REF_NUM' => $assref_num));     
        $item_data = $sql_exe->fetch();
    }
}

if(!isset($item_data) || $item_data == "" || $item_data == NULL) {
    echo "<script> swal.fire('','Unable to validate request.'); loader_stop(); enable('sbt');</script>";
    exit();
}
if(!isset($_POST['STATE']) || $main_app->valid_text($_POST['STATE']) == false || $main_app->wordlen($_POST['STATE']) > "120") {
    echo "<script> focus('STATE'); swal.fire('','Select valid state'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['DISTRICT_CODE']) || $_POST['DISTRICT_CODE'] == NULL || $_POST['DISTRICT_CODE'] == "") {
    echo "<script> focus('DISTRICT_CODE');swal.fire('','Select valid district'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['BRANCH_CODE']) || $_POST['BRANCH_CODE'] == NULL || $_POST['BRANCH_CODE'] == "") {
    echo "<script> focus('BRANCH_CODE');swal.fire('','Select valid branch'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['PRODUCT_CODE']) || $_POST['PRODUCT_CODE'] == NULL || $_POST['PRODUCT_CODE'] == "") {
    echo "<script> focus('PRODUCT_CODE');swal.fire('','Please select valid product'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['ACNT_SUBTYP']) || $_POST['ACNT_SUBTYP'] == NULL || $_POST['ACNT_SUBTYP'] == "") {
    echo "<script> focus('ACNT_SUBTYP');swal.fire('','Please select account subtype'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($assref_num) || $assref_num == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt'); </script>";
    exit();
}
else{

    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");
    $main_app->sql_db_start(); // Start - DB Transaction

    $data = array();  
    $data['ASSREQ_STATE_CODE'] = $_POST['STATE'];
    $data['ASSREQ_CITY_CODE'] = $_POST['DISTRICT_CODE'];
    $data['ASSREQ_BRANCH_CODE'] = $_POST['BRANCH_CODE'];
    $data['ASSREQ_PRODUCT_CODE'] = $_POST['PRODUCT_CODE'];
    $data['ASSREQ_ACNT_SUBTYP'] = $_POST['ACNT_SUBTYP'];
   
    $main_app->sql_db_auditlog('A',$page_table_name,$data); // Audit Log - DB Transaction

   // $brnFlg='';
    if(isset($_POST['brnFlg']) && $_POST['brnFlg'] != "") {
        $brnFlg = $safe->str_decrypt($_POST['brnFlg'], $_SESSION['SAFE_KEY']); 
    }

    if(isset($brnFlg)=='Y'){//update

        $data['MO_BY'] =  $_SESSION['USER_ID'];
        $data['MO_ON'] = date("Y-m-d H:i:s");
        $db_output = $main_app->sql_update_data('ASSREQ_ACCOUNTDATA', $data, array('ASSREQ_REF_NUM' => $assref_num));
  
    }else{     

        $data['ASSREQ_REF_NUM'] = $item_data['ASSREQ_REF_NUM'];
        $data['CR_BY'] =  $_SESSION['USER_ID'];
        $data['CR_ON'] = date("Y-m-d H:i:s");
        $db_output = $main_app->sql_insert_data("ASSREQ_ACCOUNTDATA",$data); // Insert

    }   
   
    if($db_output == false) { $updated_flag = false; }

    //update main table
    $flagdata = array();
    $flagdata['ASSREQ_BRANCH_FLAG'] = 'Y';

    $db_output = $main_app->sql_update_data('ASSREQ_MASTER', $flagdata, array( 'ASSREQ_REF_NUM' => $assref_num));
    
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == false) {
        echo "<script> swal.fire('','Unable to update branch details'); loader_stop(); enable('sbt'); </script>";
        exit();
    }
    $main_app->sql_db_commit(); // Success - DB Transaction
    $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    $sid_assref_num = $safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']);
    $go_url = "form-customer-details?ref_Num=".$sid_assref_num; // Page Refresh URL
    echo "<script> goto_url('" . $go_url . "');</script>";         
}

?>