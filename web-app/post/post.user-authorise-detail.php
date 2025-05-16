<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Get Data */
if (isset($_POST['id']) && $_POST['id'] != NULL) {
    $primary_value = $safe->str_decrypt($_POST['id'], $_SESSION['SAFE_KEY']);
}


/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS_DTL";
$page_table_name2 = "ASSREQ_USER_ACCOUNTS";


$page_primary_keys = array(
    'AUTH_REF_NUM' => (isset($primary_value)) ? $main_app->strsafe_input($primary_value) : "",
);

if(isset($primary_value) && $primary_value != "") {
    $sql_exe = $main_app->sql_run("SELECT * FROM ASSREQ_USER_ACCOUNTS_DTL WHERE AUTH_REF_NUM = :AUTH_REF_NUM AND USER_ACNT_STATUS = 'P'",$page_primary_keys);
    $item_data = $sql_exe->fetch();
}

// Start : Review
if(!isset($_POST['AUTH_STATUS']) || $_POST['AUTH_STATUS'] == NULL) {
    echo "<script> focus('AUTH_STATUS'); swal.fire('','Select status'); loader_stop(); enable('sbt2'); </script>";
}
// elseif(!isset($_POST['REMARKS']) || $main_app->valid_text($_POST['REMARKS']) == false || $main_app->wordlen($_POST['REMARKS']) < "2") {
//     echo "<script> focus('REMARKS'); swal.fire('','Enter remarks'); loader_stop(); enable('sbt2'); </script>";
// }
elseif(isset($item_data['USER_ACNT_STATUS']) && $item_data['USER_ACNT_STATUS'] != "P") {
    echo "<script> swal.fire('','This Account registration is not pending for approval'); loader_stop(); enable('sbt'); </script>";
}
// elseif(!isset($item_data['AUTH_PGM_PRIKEYS']) || $item_data['AUTH_PGM_PRIKEYS'] == NULL || $item_data['AUTH_PGM_PRIKEYS'] == "") {
//     echo "<script> swal.fire('','Invalid keys for data'); loader_stop(); enable('sbt'); </script>";
// }
elseif(isset($_POST['AUTH_STATUS']) && ($_POST['AUTH_STATUS'] != "S" && $_POST['AUTH_STATUS'] != "R")) {
    echo "<script> focus('AUTH_STATUS'); swal.fire('','Invalid Status'); loader_stop(); enable('sbt2'); </script>";
}
elseif(isset($_POST['HIDAUTHREF_NUM']) && ($_POST['HIDAUTHREF_NUM'] == "" && $_POST['HIDAUTHREF_NUM'] == "NULL")) {
    echo "<script> swal.fire('','Cannot Processed..'); loader_stop(); enable('sbt2'); </script>";
}
elseif(isset($_SESSION['USER_ID']) && $_SESSION['USER_ROLE'] != 'ADMIN' && ($_SESSION['USER_ID'] ==  $item_data['CR_BY'])) {
    echo "<script> swal.fire('','Same Entry user cannot authorise the data'); loader_stop(); enable('sbt2'); </script>";
}
else {


    $user_id = $item_data['USER_ID'];
    //$user_acnt_status = $item_data['USER_ACNT_STATUS'];
    $user_status = $item_data['USER_STATUS'];

    $page_primary_keys2 = array(
        'USER_ID' => (isset($user_id)) ? $main_app->strsafe_input($user_id) : "",
    );
    

    $main_app->sql_db_start(); // Start - DB Transaction

    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    $data = array();
    $data['USER_ACNT_STATUS'] = $_POST['AUTH_STATUS'];
    $data['AUTH_REMARKS'] = $_POST['REMARKS'];

    if (isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "R") {
        $data['RJ_BY'] = $_SESSION['USER_ID'];
        $data['RJ_ON'] = $sys_datetime;
    } elseif(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "S") {
        $data['AU_BY'] = $_SESSION['USER_ID'];
        $data['AU_ON'] = $sys_datetime;
    }

    $main_app->sql_db_auditlog('M',$page_table_name,$data,$page_primary_keys); // Audit Log - Modify
    $db_output = $main_app->sql_update_data($page_table_name,$data,$page_primary_keys); // Update
    if($db_output == false) { $updated_flag = false; }


    // if($updated_flag == true && isset($_POST['TRANSFERDEGREE']) && $_POST['TRANSFERDEGREE'] == "1") {

    //     // $data3 = array();
    //     // $data3['USER_REGIONS'] = $_POST['TRANSFER_TOBRANCH'];
        
    //     // $main_app->sql_db_auditlog('M',$page_table_name,$data3,$page_primary_keys); // Audit Log - Modify
    //     // $db_output = $main_app->sql_update_data($page_table_name,$data3,$page_primary_keys); // Update
    //     // if($db_output == false) { $updated_flag = false; }


    if($updated_flag == true){

        if(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "R" &&  $user_status == "A") {
            
            // $data2['USER_STATUS'] = 'F';//for reject user if user registration is failed to approval
        
            $main_app->sql_db_auditlog('D',$page_table_name2,'',$page_primary_keys2); // Audit Log - DB Transaction = Delete
            $db_output = $main_app->sql_delete_data($page_table_name2,$page_primary_keys2); // Update
            if($db_output == false) { $updated_flag = false; }
        
        }else{

            $data2 = array();
            $data2['USER_ACNT_STATUS'] = $_POST['AUTH_STATUS'];
        
            // if(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "S" &&  $user_status == "T") {    
            //     $data2['USER_REGIONS'] = $item_data['TRANSFER_TOBRANCH']; //if transfer approval update branch code in user account table
            // }
 
            if(isset($user_status) && $user_status == "T") {    
                
                $data2['USER_STATUS'] = "A"; //change from T to A
                if(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "S"){
                   
                    //  $data2['USER_REGIONS'] = $item_data['TRANSFER_TOBRANCH']; //if transfer approval update branch code in user account table                    $TRANSFER_DATE = new DateTime($item_data['TRANSFER_DATE']); 
                    $CUR_DATE = new DateTime(); 

                    if($CUR_DATE >= $TRANSFER_DATE) {
                       $data2['USER_REGIONS'] = $item_data['TRANSFER_TOBRANCH']; //if transfer approval update branch code in user account table if current date less than equal to transfer date
                       $data2['TRANSFER_BRNCODE'] = '';    //if any future transfer brnch & date exist in table then again transfer date is changed then clear date if date is less than equal to  cuurent date
                    }else{
                       $data2['TRANSFER_BRNCODE'] = $item_data['TRANSFER_TOBRANCH'];    //updating future transfer branch in table  if  transfer date greater than cur date 
                    }
                    $data2['TRANSFER_DATE'] = $item_data['TRANSFER_DATE'];

                }else{

                    //if any future brnch & date tranfer exist in table then clear data in case of approval rejected
                    $data2['TRANSFER_BRNCODE'] = '';     
                    $data2['TRANSFER_DATE'] = '';

                }
            }elseif(isset($user_status) && $user_status == "R") {    
                
                if(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "R"){
                    $data2['RESIGN_DATE'] = "";
                }elseif(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "S"){
                    $data2['RESIGN_DATE'] = $item_data['RESIGN_DATE'];
                }
            }

            // elseif(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "R" &&  $user_status == "R") {    
                
                
            //     $data2['USER_STATUS'] = "A"; //if resign reject need to update account user status update from R to A,resigned date to be null
            //     $data2['RESIGN_DATE'] = "";
            
            //    // if()
            
            // }

            elseif(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "S"){
                
                if(isset($user_status) && $user_status == "M"){//only modify data is updated if auth approved
                    $data2['USER_FULLNAME'] = $item_data['USER_FULLNAME'];
                    $data2['USER_MOBILE'] = $item_data['USER_MOBILE'];
                    $data2['USER_EMAIL'] = $item_data['USER_EMAIL'];
                    $data2['USER_ROLE_CODE'] = $item_data['USER_ROLE_CODE'];
                }else{
                    $data2['USER_STATUS'] = $user_status;//B-block and A-new register user is updated if auth approved
                    //$data2['RESIGN_DATE'] = "";
                }    
                               
            }

            // if(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "S" &&  $user_status != "T") {    
            //     $data2['USER_STATUS'] = $user_status;
            // } elseif(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "S" &&  $user_status == "T") {    
            //     $data2['USER_REGIONS'] = $item_data['TRANSFER_TOBRANCH']; //if transfer approval update branch code in user account table
            // }
        
            $main_app->sql_db_auditlog('M',$page_table_name2,$data2,$page_primary_keys2); // Audit Log - Modify
            $db_output = $main_app->sql_update_data($page_table_name2,$data2,$page_primary_keys2); // Update
            if($db_output == false) { $updated_flag = false; }


        }
    }


    // if(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "R") { 
    //     $data2['USER_STATUS'] =  $_POST['HIDUSERSTATUS'];
    // }elseif(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "S") {
    //     $data2['USER_STATUS'] =  $_POST['HIDUSERSTATUS'];
    // }

    // $main_app->sql_db_auditlog('M',$page_table_name2,$data2,$page_primary_keys2); // Audit Log - Modify
    // $db_output = $main_app->sql_update_data($page_table_name2,$data2,$page_primary_keys2); // Update
    // if($db_output == false) { $updated_flag = false; }


    if($updated_flag == true) {
    
        $go_url = ""; // Page Refresh URL
        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
        $message = $_POST['AUTH_STATUS'] == "R" ? "Record Rejected" : "Record Approved";
        echo "<script> swal.fire({ title:'Record updated', text:'{$message}', icon:'success', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop(); enable('sbt2'); </script>";
     
    } else {

        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt2'); </script>";

    }

}

?>