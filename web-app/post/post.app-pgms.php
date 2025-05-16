<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$page_table_name = "APP_PROGRAMS";
$page_primary_keys = array(
    'PGM_CODE' => (isset($_POST['PGM_CODE'])) ? $main_app->strsafe_input($_POST['PGM_CODE']) : "",
);

/** For Update */
if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "M" && $page_primary_keys['PGM_CODE'] != NULL) {
	$sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE PGM_CODE = :PGM_CODE", $page_primary_keys);
    $item_data = $sql_exe->fetch();
    if(!$item_data) { exit ("<script> swal.fire('','Invalid data for update'); loader_stop(); enable('sbt'); </script>"); }
}

if(isset($item_data['PGM_CODE']) && $item_data['PGM_CODE'] != NULL) {
    // Update Entry
} else {
    // New Entry
}

// Start : Programs List
if(!isset($_POST['OPERATION']) || $_POST['OPERATION'] == NULL || ($_POST['OPERATION'] != "A" && $_POST['OPERATION'] != "M")) {
    echo "<script> focus('OPERATION'); swal.fire('','Select Operation'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['PGM_CODE']) || $main_app->valid_text($_POST['PGM_CODE']) == false || $main_app->wordlen($_POST['PGM_CODE']) < "2") {
    echo "<script> focus('PGM_CODE'); swal.fire('','Enter valid program code'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['PGM_NAME']) || $main_app->valid_text($_POST['PGM_NAME']) == false || $main_app->wordlen($_POST['PGM_NAME']) < "2") {
    echo "<script> focus('PGM_NAME'); swal.fire('','Enter valid program name'); loader_stop(); enable('sbt'); </script>";
}
elseif(isset($_POST['PGM_DESC']) && $_POST['PGM_DESC'] != NULL && ($main_app->valid_text($_POST['PGM_DESC']) == false || $main_app->wordlen($_POST['PGM_DESC']) < "2")) {
    echo "<script> focus('PGM_DESC'); swal.fire('','Enter valid program description'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['PGM_FILE_PATH']) || $main_app->valid_text($_POST['PGM_FILE_PATH']) == false || $main_app->wordlen($_POST['PGM_FILE_PATH']) < "2") {
    echo "<script> focus('PGM_FILE_PATH'); swal.fire('','Enter valid program file path'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['PGM_CATEGORY']) || $_POST['PGM_CATEGORY'] == NULL) {
    echo "<script> focus('PGM_CATEGORY'); swal.fire('','Select Category'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['PGM_STATUS']) || $_POST['PGM_STATUS'] == NULL || $main_app->valid_num($_POST['PGM_STATUS']) == false) {
    echo "<script> focus('PGM_STATUS'); swal.fire('','Select status'); loader_stop(); enable('sbt'); </script>";
}
else {

    $main_app->sql_db_start(); // Start - DB Transaction
    
    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    $data = array();
    $data['PGM_NAME'] = $_POST['PGM_NAME'];
    if(isset($_POST['PGM_DESC'])) { $data['PGM_DESC'] = $_POST['PGM_DESC']; }
    if(isset($_POST['PGM_MDI_ICON'])) { $data['PGM_MDI_ICON'] = $_POST['PGM_MDI_ICON']; }
    $data['PGM_FILE_PATH'] = $_POST['PGM_FILE_PATH'];
    $data['PGM_CATEGORY'] = $_POST['PGM_CATEGORY'];
    $data['PGM_STATUS'] = $_POST['PGM_STATUS'];

    /** Add or Update Data */
    if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "A") {

        //New
        $data['PGM_CODE'] = $_POST['PGM_CODE'];
        $data['PGM_CODE'] = preg_replace('/\s+/','',$_POST['PGM_CODE']); // Remove all whitespace (including tabs and line ends)
        $data['CR_BY'] = $_SESSION['USER_ID'];
        $data['CR_ON'] = $sys_datetime;

        $main_app->sql_db_auditlog('A',$page_table_name,$data); // Audit Log - DB Transaction
        $db_output = $main_app->sql_insert_data($page_table_name,$data); // Insert
        if($db_output == false) { $updated_flag = false; }

    } else {

        //Update
        $data['MO_BY'] = $_SESSION['USER_ID'];
        $data['MO_ON'] = $sys_datetime;

        $main_app->sql_db_auditlog('M',$page_table_name,$data,$page_primary_keys); // Audit Log - Modify
        $db_output = $main_app->sql_update_data($page_table_name,$data,$page_primary_keys); // Update
        if($db_output == false) { $updated_flag = false; }

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