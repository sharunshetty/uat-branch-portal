<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$page_table_name = "USER_ACCOUNTS_BLOCK";

$page_primary_keys = array(
    'USER_ID' => (isset($_POST['USER_ID'])) ? $main_app->strsafe_input($_POST['USER_ID']) : "",
);

/** For Update */
if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "M" && $page_primary_keys['USER_ID'] != NULL) {
	$sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
    $item_data = $sql_exe->fetch();
    if(!$item_data) { exit ("<script> swal.fire('','Invalid data for update'); loader_stop(); enable('sbt'); </script>"); }
}

// Start : Programs List
if(!isset($_POST['USER_ID']) || $main_app->valid_text($_POST['USER_ID']) == false || $main_app->wordlen($_POST['USER_ID']) < "2") {
    echo "<script> focus('USER_ID'); swal.fire('','Enter valid User ID'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['STATUS']) || $_POST['STATUS'] == NULL) {
    echo "<script> focus('STATUS'); swal.fire('','Select Status'); loader_stop(); enable('sbt'); </script>";
}
else {

    $main_app->sql_db_start(); // Start - DB Transaction
    
    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    $data = array();
    $data['USER_STATUS'] = $_POST['STATUS'];

    /** Add or Update Data */
    if(isset($_POST['OPERATION']) && $_POST['OPERATION'] == "A") {

        //New
        $data['USER_ID'] = preg_replace('/\s+/','',$_POST['USER_ID']); // Remove all whitespace (including tabs and line ends)
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