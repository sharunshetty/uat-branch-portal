<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS";
$page_sub_table_name = "USER_ACCOUNTS_REGIONS";
$page_table_name1 = "ASSREQ_USER_ACCOUNTS_DTL";


$pgm_code = "USERACCOUNTS";
$pgm_auth_req = "0"; // Authorization Required : 1-Yes, 0-No

$page_primary_keys = array(
'USER_ID' => (isset($_POST['USER_ID'])) ? $main_app->strsafe_input($_POST['USER_ID']) : ""
);

// $page_primary_keys2 = array(
//     'AR_USER_ID' => (isset($_POST['USER_ID'])) ? $main_app->strsafe_input($_POST['USER_ID']) : "",
// );

/** For Update */
if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "M" && $page_primary_keys['USER_ID'] != NULL) {

    $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
    $item_data = $sql_exe->fetch();
    if(!$item_data) { exit ("<script> swal.fire('','Invalid data for update'); loader_stop(); enable('sbt'); </script>"); }
    elseif(isset($item_data['USER_ACNT_STATUS']) && $item_data['USER_ACNT_STATUS'] == 'P') {
        exit ("<script> swal.fire('','User Id is in pending list for the authorise approval.'); loader_stop(); enable('sbt'); </script>");
    }
    
    //$sql_exe1 = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID and USER_ACNT_STATUS = 'P'", $page_primary_keys);
    // $item_data1 = $sql_exe1->fetch();
    //if($item_data1) { exit ("<script> swal.fire('','User Id is in pending list for the authorise approval.'); loader_stop(); enable('sbt'); </script>"); }

}


/** For add */
if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "A" && $page_primary_keys['USER_ID'] != NULL) {
	$sql_exe1 = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
    $item_data1 = $sql_exe1->fetch();

    if(isset($item_data1['RESIGN_DATE']) && $item_data1['RESIGN_DATE'] != '') {
       exit ("<script> swal.fire('','Resign date already assigned, cannot update.'); loader_stop(); enable('sbt'); </script>");
    }elseif($item_data1) { exit ("<script> swal.fire('','User Id already registered.'); loader_stop(); enable('sbt'); </script>"); }
}


// Start : Programs List
if(!isset($_POST['OPERATION']) || $_POST['OPERATION'] == NULL || ($_POST['OPERATION'] != "A" && $_POST['OPERATION'] != "M")) {
    echo "<script> focus('OPERATION'); swal.fire('','Select Operation'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['USER_ID']) || $main_app->valid_text($_POST['USER_ID']) == false || $main_app->wordlen($_POST['USER_ID']) < "2") {
    echo "<script> focus('USER_ID'); swal.fire('','Enter valid User Id'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['USER_FULLNAME']) || $main_app->valid_text($_POST['USER_FULLNAME']) == false || $main_app->wordlen($_POST['USER_FULLNAME']) < "2") {
    echo "<script> focus('USER_FULLNAME'); swal.fire('','Enter valid Full Name'); loader_stop(); enable('sbt'); </script>";
}
elseif($_POST['OPERATION'] != "M" && (!isset($_POST['USR_PASSWORD1']) || $_POST['USR_PASSWORD1'] == NULL)) {
    echo "<script> focus('USR_PASSWORD1'); swal.fire('','Enter Valid Password'); loader_stop(); enable('sbt'); </script>";
}
elseif( $_POST['OPERATION'] != "M" && (!isset($_POST['USR_PASSWORD2']) ||  $_POST['USR_PASSWORD2'] == NULL)) {
    echo "<script> focus('USR_PASSWORD2'); swal.fire('','Enter Valid Confirm Password'); loader_stop(); enable('sbt'); </script>";
}
elseif(  $_POST['OPERATION'] != "M" &&  ($_POST['USR_PASSWORD1'] != $_POST['USR_PASSWORD2'])) {
    echo "<script> focus('USR_PASSWORD2'); swal.fire('','Password is Mismatch'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['USER_ROLE_CODE'])  || $_POST['USER_ROLE_CODE'] == NULL) {
    echo "<script> focus('USER_ROLE_CODE'); swal.fire('','Select User Role Code'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['USER_EMAIL']) || $_POST['USER_EMAIL'] == NULL || $main_app->valid_email($_POST['USER_EMAIL']) == false) {
    echo "<script> focus('USER_EMAIL'); swal.fire('','Enter valid email id'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['USER_MOBILE']) || $_POST['USER_MOBILE'] == NULL || $main_app->valid_mobile($_POST['USER_MOBILE']) == false) {
    echo "<script> focus('USER_MOBILE'); swal.fire('','Enter valid mobile number'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['USER_REGIONS']) || $_POST['USER_REGIONS'] == NULL) {
    echo "<script> focus('USER_REGIONS'); swal.fire('','Select Processing Region'); loader_stop(); enable('sbt'); </script>";
}
// elseif($_POST['USER_REGIONS'] =='S'  && (!isset($_POST['AR_REGION_CODE']) || $_POST['AR_REGION_CODE'] == NULL || !is_array($_POST['AR_REGION_CODE']) || count($_POST['AR_REGION_CODE']) < "0")) {
//     echo "<script> focus('AR_REGION_CODE'); swal.fire('','Select any Region code'); loader_stop(); enable('sbt'); </script>";
// }
elseif(!isset($_POST['USER_STATUS']) || $_POST['USER_STATUS'] == NULL) {
    echo "<script> focus('USER_STATUS'); swal.fire('','Select status'); loader_stop(); enable('sbt'); </script>";
}
// elseif(!isset($_POST['USER_STATUS']) || $_POST['USER_STATUS'] == NULL || $main_app->valid_num($_POST['USER_STATUS']) == false) {
//     echo "<script> focus('USER_STATUS'); swal.fire('','Select status'); loader_stop(); enable('sbt'); </script>";
// }
else {

    $main_app->sql_db_start(); // Start - DB Transaction
    
    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    $data = array();
    // $data['USER_FULLNAME'] = $_POST['USER_FULLNAME'];
    // $data['USER_ROLE_CODE'] = $_POST['USER_ROLE_CODE'];
    // $data['USER_MOBILE'] = $_POST['USER_MOBILE'];
    // $data['USER_EMAIL'] = $_POST['USER_EMAIL'];
    // $data['USER_STATUS'] = 'P'; //$_POST['USER_STATUS'];
    //$data['USER_ACNT_STATUS'] = 'P';
    // $data['USER_REGIONS'] = $_POST['USER_REGIONS'];


   // $data['TRANSFER_FLAG'] = (isset($_POST['TRANSFERDEGREE']) && $_POST['TRANSFERDEGREE'] == "1") ? $_POST['TRANSFERDEGREE'] : "";

    /** Add or Update Data */
    if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "A") {

        //New
        $data['USER_ID'] = preg_replace('/\s+/','',$_POST['USER_ID']); // Remove all whitespace (including tabs and line ends)
        $hashed_password = password_hash($_POST['USR_PASSWORD2'], PASSWORD_DEFAULT);
        $data['USER_PASS'] = $hashed_password;
        $data['CR_BY'] = $_SESSION['USER_ID'];
        $data['CR_ON'] = $sys_datetime;

        $data['USER_FULLNAME'] = $_POST['USER_FULLNAME'];
        $data['USER_ROLE_CODE'] = $_POST['USER_ROLE_CODE'];
        $data['USER_MOBILE'] = $_POST['USER_MOBILE'];
        $data['USER_EMAIL'] = $_POST['USER_EMAIL'];
        $data['USER_STATUS'] = 'P'; //$_POST['USER_STATUS'];
        $data['USER_ACNT_STATUS'] = 'P';
        $data['USER_REGIONS'] = $_POST['USER_REGIONS'];

        $main_app->sql_db_auditlog('A',$page_table_name,$data); // Audit Log - DB Transaction
        $db_output = $main_app->sql_insert_data($page_table_name,$data); // Insert
        if($db_output == false) { $updated_flag = false; }

    }    
    //else {

    //    $main_app->sql_db_auditlog('M',$page_table_name,$data,$page_primary_keys); // Audit Log - Modify
    //    $db_output = $main_app->sql_update_data($page_table_name,$data,$page_primary_keys); // Update
    //    if($db_output == false) { $updated_flag = false; }

   // }

    if($updated_flag == true){

       // $auth_ref_num = $main_app->sql_fetchcolumn("SELECT 'Q' || TO_CHAR(SYSDATE,'YYYYMMDD') || REG_CORP_COMMONAUTH_SEQ.NEXTVAL FROM DUAL","");
        $auth_ref_num = $main_app->sql_sequence("COMMONAUTH_SEQ");
        
        if($auth_ref_num) {
            $data2 = array();
            $data2['AUTH_REF_NUM'] = $auth_ref_num;
            $data2['USER_ID'] = preg_replace('/\s+/','',$_POST['USER_ID']); // Remove all whitespace (including tabs and line ends)
            $data2['USER_FULLNAME'] = $_POST['USER_FULLNAME'];
            $data2['CR_BY'] = $_SESSION['USER_ID'];
            $data2['CR_ON'] = $sys_datetime;
            $data2['USER_ACNT_STATUS'] = 'P';
            $data2['USER_STATUS'] = $_POST['OPERATION'];
          
            // if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "A") {
            //     $data2['USER_STATUS'] = $_POST['USER_STATUS']; //new data 
            // }else{
            //     $data2['USER_STATUS'] = "M";//new modify data
            // }
        
            $data2['USER_REGIONS'] = $_POST['USER_REGIONS'];
            $data2['USER_ROLE_CODE'] = $_POST['USER_ROLE_CODE'];
            $data2['USER_MOBILE'] = $_POST['USER_MOBILE'];
            $data2['USER_EMAIL'] = $_POST['USER_EMAIL'];

            $main_app->sql_db_auditlog('A',$page_table_name1,$data2); // Audit Log - DB Transaction
            $db_output = $main_app->sql_insert_data($page_table_name1,$data2); // Insert

            if($db_output == false) { $updated_flag = false; }
        
        }else {
            $updated_flag = false;
        }
    
    }    

    /** Final */
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

?>