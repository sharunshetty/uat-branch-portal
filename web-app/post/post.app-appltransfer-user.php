<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS";
$page_table_name1 = "ASSREQ_MASTER";
$page_primary_keys = array(
    'USER_ID' => (isset($_POST['USER_ID'])) ? $main_app->strsafe_input($_POST['USER_ID']) : "",
);

if($page_primary_keys['USER_ID'] !='' && $page_primary_keys['USER_ID'] != NULL) {
   
    $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID and USER_STATUS = 'A' AND RESIGN_DATE IS NULL", $page_primary_keys);   
    $item_data = $sql_exe->fetch();
    if(!$item_data) { exit ("<script> swal.fire('','User Id is not active.'); loader_stop(); enable('sbt'); </script>"); }  

}else{

    echo "<script>swal.fire('','User ID is Blanked'); loader_stop(); enable('sbt'); </script>";
    exit();

}

if($page_primary_keys['USER_ID'] != NULL && $item_data['USER_ID'] !='' && $item_data['USER_ID'] != NULL) {

    if(!isset($_POST['USER_BRANCH']) || $_POST['USER_BRANCH'] == NULL || $_POST['USER_BRANCH'] == "") {
        echo "<script> focus('USER_BRANCH'); swal.fire('','Branch Code is Blanked'); loader_stop(); enable('sbt'); </script>";
    }
    elseif(!isset($_POST['TRANSFER_USER']) || $_POST['TRANSFER_USER'] == NULL || $_POST['TRANSFER_USER'] == "") {
        echo "<script> focus('TRANSFER_USER'); swal.fire('','Transfer user is Blanked'); loader_stop(); enable('sbt'); </script>";
    }
    // elseif(!isset($_POST['SELECTS']) || !is_array($_POST['SELECTS']) || $main_app->valid_array($_POST['SELECTS']) == false) {
    //     echo "<script> swal.fire('','Please select atleast one record to proceed'); loader_stop(); enable('sbt'); </script>";
    // }
    elseif(!isset($_POST['SELECTS']) || $_POST['SELECTS'] == NULL || !is_array($_POST['SELECTS']) || count($_POST['SELECTS']) < "0") {
        echo "<script> swal.fire('','Please select atleast one record to proceed'); loader_stop(); enable('sbt'); </script>";
    }
    else {

        $main_app->sql_db_start(); // Start - DB Transaction    
        $updated_flag = true;

        $sys_datetime = date("Y-m-d H:i:s");
        $data = array();

        $page_primary_keys1 = array(
            'USER_ID' => (isset($_POST['TRANSFER_USER'])) ? $main_app->strsafe_input($_POST['TRANSFER_USER']) : "",
        );

        $sql_exe1 = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID AND USER_STATUS = 'A' AND RESIGN_DATE IS NULL", $page_primary_keys1);   
        $item_data1 = $sql_exe1->fetch();
        
        if(!$item_data1) { exit ("<script> swal.fire('','Transfer User Id is not active.'); loader_stop(); enable('sbt'); </script>"); }

        
        foreach($_POST['SELECTS'] as $key => $value) {
            if($value == "Yes") {

                $page_primary_keys3 = array(
                    'ASSREQ_REF_NUM' => (isset($_POST['USER_REF_NUM'][$key])) ? $main_app->strsafe_input($_POST['USER_REF_NUM'][$key]) : "",
                );

               // 'CR_BY' => (isset($_POST['USER_ID'])) ? $main_app->strsafe_input($_POST['USER_ID']) : "",
                
                $data['TR_FROM'] = $_POST['USER_ID'];
                $data['TR_ON'] = $sys_datetime;
                $data['CR_BY'] = $_POST['TRANSFER_USER'];

                $main_app->sql_db_auditlog('M',$page_table_name1,$data,$page_primary_keys3); // Audit Log - Modify
                $db_output = $main_app->sql_update_data($page_table_name1,$data,$page_primary_keys3); // Update
                if($db_output == false) { 
                    $updated_flag = false;
                    break;
                }
            }
        }

        if($updated_flag == true) {

            $go_url = ""; // Page Refresh URL
            $main_app->sql_db_commit(); // Success - DB Transaction
            $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
            echo "<script> swal.fire({ title:'Record updated', text:'', icon:'success', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop(); enable('sbt'); </script>";
    
        } else {
    
            $main_app->sql_db_rollback(); // Fail - DB Transaction
            echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";
    
        }

    }

}else{

    $main_app->sql_db_rollback(); // Fail - DB Transaction
    echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";

}

