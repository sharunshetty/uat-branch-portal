<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$page_table_name = "ASSREQ_USER_ROLES";
$page_primary_keys = array(
    'ROLE_CODE' => (isset($_POST['ROLE_CODE'])) ? $main_app->strsafe_input($_POST['ROLE_CODE']) : "",
);

$page_primary_keys2 = array(
    'DTL_ROLE_CODE' => (isset($_POST['ROLE_CODE'])) ? $main_app->strsafe_input($_POST['ROLE_CODE']) : "",
);

$page_sub_table_name = "ASSREQ_USER_ROLES_DTL";
$primary_key = "ROLE_CODE";
$primary_value = isset($_POST['ROLE_CODE']) ? $_POST['ROLE_CODE'] : ''; // Don't change
$primary_key_2 = "DTL_ROLE_CODE";
$primary_value_2 = isset($_POST['PGM_CODE']) ? $_POST['PGM_CODE'] : ''; // Don't change

/** For Update */
if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "M" && $page_primary_keys['ROLE_CODE'] != NULL) {
	$sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE ROLE_CODE = :ROLE_CODE", $page_primary_keys);
    $item_data = $sql_exe->fetch();
    if(!$item_data) { exit ("<script> swal.fire('','Invalid data for update'); loader_stop(); enable('sbt'); </script>"); }
}

if(isset($item_data['ROLE_CODE']) && $item_data['ROLE_CODE'] != NULL) {
    // Update Entry
} else {
    // New Entry
}

// Start : Programs List
if(!isset($_POST['OPERATION']) || $_POST['OPERATION'] == NULL || ($_POST['OPERATION'] != "A" && $_POST['OPERATION'] != "M")) {
    echo "<script> focus('OPERATION'); swal.fire('','Select Operation'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['ROLE_CODE']) || $main_app->valid_text($_POST['ROLE_CODE']) == false || $main_app->wordlen($_POST['ROLE_CODE']) < "2") {
    echo "<script> focus('ROLE_CODE'); swal.fire('','Enter valid role code'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['ROLE_DESC']) || $main_app->valid_text($_POST['ROLE_DESC']) == false || $main_app->wordlen($_POST['ROLE_DESC']) < "2") {
    echo "<script> focus('ROLE_DESC'); swal.fire('','Enter valid role name'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['ROLE_STATUS']) || $_POST['ROLE_STATUS'] == NULL || $main_app->valid_num($_POST['ROLE_STATUS']) == false) {
    echo "<script> focus('ROLE_STATUS'); swal.fire('','Select status'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['DTL_PGM_CODE']) || $_POST['DTL_PGM_CODE'] == NULL || !is_array($_POST['DTL_PGM_CODE']) || count($_POST['DTL_PGM_CODE']) < "0") {
    echo "<script> focus('DTL_PGM_CODE'); swal.fire('','Select any program code'); loader_stop(); enable('sbt'); </script>";
}

else {

    $main_app->sql_db_start(); // Start - DB Transaction

    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    $data = array();
    $data['ROLE_DESC'] = $_POST['ROLE_DESC'];
    $data['ROLE_STATUS'] = $_POST['ROLE_STATUS'];

    /** Add or Update Data */
    if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "A") {

        //New
        $data['ROLE_CODE'] = $_POST['ROLE_CODE'];
        $data['CR_BY'] = $_SESSION['USER_ID'];
        $data['CR_ON'] = $sys_datetime;

        $main_app->sql_db_auditlog('A',$page_table_name,$data); // Audit Log - DB Transaction
        $db_output = $main_app->sql_insert_data($page_table_name,$data); // Insert
        if($db_output == false) { $updated_flag = false; }

    } else {

        //Update
        $data['MO_BY'] = $_SESSION['USER_ID'];
        $data['MO_ON'] = $sys_datetime;

        $main_app->sql_db_auditlog('M',$page_table_name,$data,$page_primary_keys); // Audit Log - DB Transaction = Modify
        $db_output = $main_app->sql_update_data($page_table_name,$data,$page_primary_keys); // Update
        if($db_output == false) { $updated_flag = false; }

    }

    //Sub Table : Roles Details - Delete Existing Data
    if($updated_flag == true && isset($_POST['OPERATION']) && $_POST['OPERATION'] == "M") {

        $main_app->sql_db_auditlog('D',$page_sub_table_name,'',$page_primary_keys2); // Audit Log - DB Transaction = Delete
        $db_output = $main_app->sql_delete_data($page_sub_table_name,$page_primary_keys2); // Update
        if($db_output == false) { $updated_flag = false; }

    }

    //Sub Table : Insert
    if($updated_flag == true && isset($_POST['DTL_PGM_CODE']) && is_array($_POST['DTL_PGM_CODE']) && count($_POST['DTL_PGM_CODE']) > "0") {

        foreach ($_POST['DTL_PGM_CODE'] as $key => $value ) {
            $data2 = array();
            $data2['DTL_ROLE_CODE'] = $_POST['ROLE_CODE'];
            $data2['DTL_PGM_CODE'] = $value;

            $main_app->sql_db_auditlog('A',$page_sub_table_name,$data2); // Audit Log - DB Transaction
            $db_output2 = $main_app->sql_insert_data($page_sub_table_name,$data2); // Insert
            if($db_output2 == false) { $updated_flag = false; }
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