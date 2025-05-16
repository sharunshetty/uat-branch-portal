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
        $page_table_name = "APP_PROGRAMS";
        $page_primary_keys = array(
            'PGM_CODE' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
        );

        $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE PGM_CODE = :PGM_CODE", $page_primary_keys);
        $item_data = $sql_exe->fetch();

        //if(isset($item_data['PGM_CODE']) && $item_data['PGM_CODE'] != NULL) {

            //Update Value
    	    $html_op .= "$('#PGM_NAME').val(decode_ajax('".$main_app->strsafe_ajax($item_data['PGM_NAME'])."'));";
    	    $html_op .= "$('#PGM_DESC').val(decode_ajax('".$main_app->strsafe_ajax($item_data['PGM_DESC'])."'));";
            $html_op .= "$('#PGM_FILE_PATH').val(decode_ajax('".$main_app->strsafe_ajax($item_data['PGM_FILE_PATH'])."'));";
            $html_op .= "$('#PGM_MDI_ICON').val(decode_ajax('".$main_app->strsafe_ajax($item_data['PGM_MDI_ICON'])."'));";
            $html_op .= "$('#PGM_CATEGORY').val(decode_ajax('".$main_app->strsafe_ajax($item_data['PGM_CATEGORY'])."'));";
            $html_op .= "$('#PGM_STATUS').val(decode_ajax('".$main_app->strsafe_ajax($item_data['PGM_STATUS'])."'));";
        
        //} 
        
    }

    //Print
    echo "<script> {$html_op} </script>";

}

?>