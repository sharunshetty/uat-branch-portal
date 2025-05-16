<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS";
$page_table_name1 = "ASSREQ_USER_ACCOUNTS_DTL";

$page_primary_keys = array(
    'USER_ID' => (isset($_POST['USER_ID'])) ? $main_app->strsafe_input($_POST['USER_ID']) : "",
);


/** For add */
if($page_primary_keys['USER_ID'] !='' && $page_primary_keys['USER_ID'] != NULL) {
   

    $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
    $item_data = $sql_exe->fetch();
    if(isset($item_data['USER_ACNT_STATUS']) && $item_data['USER_ACNT_STATUS'] == 'P') {
        exit ("<script> swal.fire('','User Id is in pending list for the authorise approval.'); loader_stop(); enable('sbt'); </script>");
    }
    
    // elseif(isset($item_data['USER_STATUS']) && $item_data['USER_STATUS'] != 'A') {
    //     exit ("<script> swal.fire('','User Id is not active.'); loader_stop(); enable('sbt'); </script>");
    // }
    
    elseif(isset($item_data['RESIGN_DATE']) && $item_data['RESIGN_DATE'] != '') {
       exit ("<script> swal.fire('','Resign date already assigned, cannot update.'); loader_stop(); enable('sbt'); </script>");
    }
    
    //$sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID and (USER_STATUS = 'P' or USER_STATUS = 'F')", $page_primary_keys);
    // $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID and USER_STATUS = 'P'", $page_primary_keys);   
    // $item_data = $sql_exe->fetch();
    // if($item_data) { exit ("<script> swal.fire('','User Id is not active.'); loader_stop(); enable('sbt'); </script>"); }

    // $sql_exe1 = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID and USER_ACNT_STATUS = 'P'", $page_primary_keys);
    // $item_data1 = $sql_exe1->fetch();
    // if($item_data1) { exit ("<script> swal.fire('','User Id is in pending list for the authorise approval.'); loader_stop(); enable('sbt'); </script>"); }


}else{

    echo "<script>swal.fire('','User ID is Blanked'); loader_stop(); enable('sbt'); </script>";
    exit();
  
}

$sql_exe2 = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
$item_data2 = $sql_exe2->fetch();

if($page_primary_keys['USER_ID'] != NULL && $item_data2['USER_ID'] !='' && $item_data2['USER_ID'] != NULL) {
    
    if(isset($item_data2['RESIGN_DATE']) && $item_data2['RESIGN_DATE'] != '') {
        echo "<script> swal.fire('','Resign date already assigned, cannot update.'); loader_stop(); enable('sbt'); </script>";
    }
    elseif(!isset($_POST['STATUS']) || $_POST['STATUS'] == NULL) {
        echo "<script> focus('STATUS'); swal.fire('','Select Status'); loader_stop(); enable('sbt'); </script>";
    }
    elseif(isset($_POST['STATUS']) && $_POST['STATUS'] == "R" && $_POST['RESIGN_DATE'] == "") {
        echo "<script> focus('STATUS'); swal.fire('','Select date of resign'); loader_stop(); enable('sbt'); </script>";
    }
    // elseif(isset($_POST['STATUS']) && $_POST['STATUS'] == "R" && $item_data2['RESIGN_DATE'] !='') {
    //     echo "<script> focus('STATUS'); swal.fire('','Already Status is assigned'); loader_stop(); enable('sbt'); </script>";
    // }
    elseif(isset($_POST['STATUS']) && $_POST['STATUS'] == $item_data2['USER_STATUS']) {
        echo "<script> focus('STATUS'); swal.fire('','Already Status is assigned'); loader_stop(); enable('sbt'); </script>";
    }
    else {


        $cust_count = $main_app->sql_fetchcolumn("SELECT COUNT(0) FROM ASSREQ_MASTER WHERE APP_STATUS IS NULL AND  CR_BY = :LOGIN_USR_ID",  array('LOGIN_USR_ID' => $_POST['USER_ID']));
        if(isset($cust_count) && $cust_count > "0") {
            echo "<script> swal.fire('','Account opening for customers is still pending, cannot update status'); loader_stop(); enable('sbt'); </script>";
            exit();
        }

        // $auth_ref_num = $main_app->sql_fetchcolumn("SELECT 'Q' || TO_CHAR(SYSDATE,'YYYYMMDD') || REG_CORP_COMMONAUTH_SEQ.NEXTVAL FROM DUAL","");
        $auth_ref_num = $main_app->sql_sequence("COMMONAUTH_SEQ");
        
        if($auth_ref_num) {

            $main_app->sql_db_start(); // Start - DB Transaction
        
            $updated_flag = true;
            $sys_datetime = date("Y-m-d H:i:s");
            $data = array();
        
            $data['AUTH_REF_NUM'] = $auth_ref_num;
            $data['USER_ID'] = preg_replace('/\s+/','',$_POST['USER_ID']); // Remove all whitespace (including tabs and line ends)
            $data['USER_FULLNAME'] = $_POST['USER_FULLNAME'];
            $data['USER_STATUS'] = $_POST['STATUS'];
            $data['USER_ACNT_STATUS'] = "P";
             //$resigndate = date("d-m-Y", strtotime($_POST['RESIGN_DATE']));
            //$data['RESIGN_DATE'] = (isset($resigndate) && $resigndate != "") ? $resigndate : "";
            if(isset($_POST['STATUS']) && $_POST['STATUS'] =='R') {
                $data['RESIGN_DATE'] = (isset($_POST['RESIGN_DATE']) && $_POST['RESIGN_DATE'] !='') ? date("Y-m-d", strtotime($_POST['RESIGN_DATE'])) : "";
            }
            $data['USER_REGIONS'] = $item_data2['USER_REGIONS'];
            $data['CR_BY'] = $_SESSION['USER_ID'];
            $data['CR_ON'] = $sys_datetime;

            $main_app->sql_db_auditlog('A',$page_table_name1,$data); // Audit Log - DB Transaction
            $db_output = $main_app->sql_insert_data($page_table_name1,$data); // Insert

            if($db_output == false) { $updated_flag = false; }

            if($updated_flag = true){
                $data1 = array();
                $data1['USER_ACNT_STATUS'] = 'P';

                if(isset($_POST['STATUS']) && $_POST['STATUS'] == "R"){

                    $RESIGN_DATE = new DateTime($_POST['RESIGN_DATE']); 
                    $CUR_DATE = new DateTime(); 

                    if($RESIGN_DATE <= $CUR_DATE) {
                       // $data1['USER_STATUS'] = $_POST['STATUS'];
                       $data1['RESIGN_DATE'] = (isset($_POST['RESIGN_DATE']) && $_POST['RESIGN_DATE'] !='') ? date("Y-m-d", strtotime($_POST['RESIGN_DATE'])) : "";
                    }

                    // $data1['USER_STATUS'] = $_POST['STATUS'];
                    // $data1['RESIGN_DATE'] = (isset($_POST['RESIGN_DATE']) && $_POST['RESIGN_DATE'] !='') ? date("Y-m-d", strtotime($_POST['RESIGN_DATE'])) : "";
                }


                $main_app->sql_db_auditlog('M',$page_table_name,$data1,$page_primary_keys); // Audit Log - Modify
                $db_output = $main_app->sql_update_data($page_table_name,$data1,$page_primary_keys); // Update
                if($db_output == false) { $updated_flag = false; }
            }


            // if($updated_flag = true && isset($_POST['STATUS']) && $_POST['STATUS'] == "R"){
            //     $data1 = array();
            //     $data1['USER_ACNT_STATUS'] = 'P';
            //     $data1['USER_STATUS'] = $_POST['STATUS'];
            //     $data1['RESIGN_DATE'] = (isset($_POST['RESIGN_DATE']) && $_POST['RESIGN_DATE'] !='') ? date("Y-m-d", strtotime($_POST['RESIGN_DATE'])) : "";
            //     $main_app->sql_db_auditlog('M',$page_table_name,$data1,$page_primary_keys); // Audit Log - Modify
            //     $db_output = $main_app->sql_update_data($page_table_name,$data1,$page_primary_keys); // Update
            //     if($db_output == false) { $updated_flag = false; }
            // }

        }else {
        
            $updated_flag = false;
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

}else {

    $main_app->sql_db_rollback(); // Fail - DB Transaction
    echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";

}


?>