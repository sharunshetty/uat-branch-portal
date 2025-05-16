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

if(isset($_POST['NOMINEE_NAME']) && $_POST['NOMINEE_NAME'] == "" && isset($_POST['NOMINEE_DOB']) && $_POST['NOMINEE_DOB'] == "" && isset($_POST['NOMINEE_RELATION']) && $_POST['NOMINEE_RELATION'] == "" && isset($_POST['NOMINEE_ADDRESS']) && $_POST['NOMINEE_ADDRESS'] == "")  {
  
  $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
  $sid_assref_num = $safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']);
  $go_url = "ass-formfinal-detail?ref_Num=".$sid_assref_num; // Page Refresh URL
  echo "<script> goto_url('" . $go_url . "');</script>";

}else {

	if(!isset($_POST['NOMINEE_NAME']) || $main_app->valid_text($_POST['NOMINEE_NAME']) == false || $main_app->wordlen($_POST['NOMINEE_NAME']) > "120") {
    echo "<script> focus('NOMINEE_NAME'); swal.fire('','Please enter nominee name'); loader_stop(); enable('sbt'); </script>";
    exit();
  }
  elseif(!isset($_POST['NOMINEE_DOB']) || $_POST['NOMINEE_DOB'] == NULL || $_POST['NOMINEE_DOB'] == "") {
    echo "<script> focus('NOMINEE_DOB');swal.fire('','Please enter nominee DOB'); loader_stop(); enable('sbt'); </script>";
    exit();
  }
  elseif(!isset($_POST['NOMINEE_RELATION']) || $_POST['NOMINEE_RELATION'] == NULL || $_POST['NOMINEE_RELATION'] == "") {
    echo "<script> focus('NOMINEE_RELATION');swal.fire('','Please select nominee relation'); loader_stop(); enable('sbt'); </script>";
    exit();
  }
  elseif(!isset($_POST['NOMINEE_ADDRESS']) || $_POST['NOMINEE_ADDRESS'] == NULL || $_POST['NOMINEE_ADDRESS'] == "") {
    echo "<script> focus('NOMINEE_ADDRESS');swal.fire('','Please enter nominee address'); loader_stop(); enable('sbt'); </script>";
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
    $data['ASSREQ_NOMINEE_NAME'] = $_POST['NOMINEE_NAME'];
    $data['ASSREQ_NOMINEE_DOB'] = $_POST['NOMINEE_DOB'];
    $data['ASSREQ_NOMINEE_ADDRESS'] = $_POST['NOMINEE_ADDRESS'];
    $data['ASSREQ_NOMINEE_RELATION'] = $_POST['NOMINEE_RELATION'];
    $data['ASSREQ_MINOR_FLAG'] = isset($_POST['NOMINEE_HIDDENAFLG']) ? $_POST['NOMINEE_HIDDENAFLG'] : NULL;  
    $data['ASSREQ_GUARDIAN_NATURE'] = isset($_POST['NOMINEE_NATURE']) ? $_POST['NOMINEE_NATURE'] : NULL;  
    $data['ASSREQ_NOMINEE_GUARDIAN'] = isset($_POST['NOMINEE_GUARDIAN']) ? $_POST['NOMINEE_GUARDIAN'] : NULL;  

    $data['MO_BY'] =  $_SESSION['USER_ID'];
    $data['MO_ON'] = date("Y-m-d H:i:s");

    $main_app->sql_db_auditlog('M',$page_table_name,$data); // Audit Log - DB Transaction
    $db_output = $main_app->sql_update_data("ASSREQ_ACCOUNTDATA",$data, array('ASSREQ_REF_NUM' => $assref_num)); // update account data table
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == false) {
      echo "<script> swal.fire('','Unable to process your request.'); loader_stop(); enable('sbt'); </script>";
      exit();
    }

    $flagdata = array();
    $flagdata['ASSREQ_NOMINEE_FLG'] = 'Y';

    $db_output2 = $main_app->sql_update_data('ASSREQ_MASTER', $flagdata, array( 'ASSREQ_REF_NUM' => $assref_num));
    if($db_output2 == false) { $updated_flag = false; }

    if($updated_flag == false) {
      echo "<script> swal.fire('','Unable to process your request'); loader_stop(); enable('sbt'); </script>";
      exit();
    }
    $main_app->sql_db_commit(); // Success - DB Transaction 
    $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    $sid_assref_num = $safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']);
    $go_url = "ass-formfinal-detail?ref_Num=".$sid_assref_num; // Page Refresh URL
    echo "<script> goto_url('" . $go_url . "');</script>";

        
  }
}



?>