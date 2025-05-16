<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();
$html_op = "";

/** On Change */
if($_POST['cmd2'] == "onChange" && isset($_POST['field_name']) && isset($_POST['field_val']) && isset($_POST['dest_id'])) {

    if($_POST['field_name'] == "modify" && $_POST['field_val'] != "") {

        //Modify
        $page_table_name = "USER_ACCOUNTS_BLOCK";
        $page_primary_keys = array(
            'USER_ID' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
        );

        $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
        $item_data = $sql_exe->fetch();

        if(isset($item_data['USER_ID'])) {
            $html_op .= "$('#OPERATION').val(decode_ajax('".$main_app->strsafe_ajax('M')."')); $('#STATUS').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_STATUS'])."')); focus('STATUS');";
        }
        else {
            $html_op .= "$('#OPERATION').val(decode_ajax('".$main_app->strsafe_ajax('A')."')); $('#STATUS').val(''); focus('STATUS');";
        }
        
    }

    //Print
    echo "<script> {$html_op} </script>";

}

?>