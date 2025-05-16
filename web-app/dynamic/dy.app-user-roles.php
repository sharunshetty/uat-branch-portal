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
        $page_table_name = "ASSREQ_USER_ROLES";
        $page_primary_keys = array(
            'ROLE_CODE' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
        );

        $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE ROLE_CODE = :ROLE_CODE", $page_primary_keys);
        $item_data = $sql_exe->fetch();

        if(isset($item_data['ROLE_CODE']) && $item_data['ROLE_CODE'] != NULL) {

            //Update Value
            $html_op .= "$('#ROLE_CODE').val(decode_ajax('".$main_app->strsafe_ajax($item_data['ROLE_CODE'])."'));";
    	    $html_op .= "$('#ROLE_DESC').val(decode_ajax('".$main_app->strsafe_ajax($item_data['ROLE_DESC'])."'));";
            $html_op .= "$('#ROLE_STATUS').val(decode_ajax('".$main_app->strsafe_ajax($item_data['ROLE_STATUS'])."'));";
        
        } 

        //Modify - Sub Table Details
        $page_sub_table_name = "ASSREQ_USER_ROLES_DTL";

        $page_primary_keys2 = array(
            'DTL_ROLE_CODE' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
        );

        $sql_exe2 = $main_app->sql_run("SELECT * FROM {$page_sub_table_name} WHERE DTL_ROLE_CODE = :DTL_ROLE_CODE", $page_primary_keys2);
        while ($row = $sql_exe2->fetch()) {
            if(isset($row['DTL_PGM_CODE']) && $row['DTL_PGM_CODE'] != NULL) {
                $html_op .= "$('#PGM_'+decode_ajax('".$main_app->strsafe_ajax($row['DTL_PGM_CODE'])."')).prop('checked',true); ";
            }
        }       
        
    }

    //Print
    echo "<script> {$html_op}</script>";

}

?>