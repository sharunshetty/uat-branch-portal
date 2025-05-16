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
        $page_table_name = "";
        $page_sub_table_name = "";
        $page_primary_keys = array(
            'ACTIVITY_TYPE' => (isset($key[0])) ? $main_app->strsafe_input($key[0]) : "",
            'EFFT_DATE' => (isset($key[1])) ? $main_app->strsafe_input($key[1]) : ""
        );

        $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE ACTIVITY_TYPE = :ACTIVITY_TYPE AND EFFT_DATE = :EFFT_DATE", $page_primary_keys);
        $item_data = $sql_exe->fetch();

        if(isset($item_data['ACTIVITY_TYPE']) && $item_data['ACTIVITY_TYPE'] && isset($item_data['EFFT_DATE']) && $item_data['EFFT_DATE'] != NULL) {

            //Update Value
            //$html_op .= "$('#ENABLED').val(decode_ajax('".$main_app->strsafe_ajax($item_data['ENABLED'])."'));";
            $html_op .= "$('#ACTIVITY_TYPE').val(decode_ajax('".$main_app->strsafe_ajax($item_data['ACTIVITY_TYPE'])."'));";
            $html_op .= "$('#EFFT_DATE').val(decode_ajax('".$main_app->strsafe_ajax(date("Y-m-d",strtotime($item_data['EFFT_DATE'])))."'));";
            if(isset($item_data['ALLOWED_ON_HOLIDAY']) && $item_data['ALLOWED_ON_HOLIDAY'] == "Y") { $html_op .= "$('#ALLOWED_ON_HOLIDAY').prop('checked',true);"; } else { $html_op .= "$('#ALLOWED_ON_HOLIDAY').prop('checked',false);"; }
            $html_op .= "$('#RATE_FREEZE_TIME').val(decode_ajax('".$main_app->strsafe_ajax($item_data['RATE_FREEZE_TIME'])."'));";

            $sql_exe2 = $main_app->sql_run("SELECT SL,WEEKDAY,FROM_TIME,UPTO_TIME FROM {$page_sub_table_name} WHERE ACTIVITY_TYPE = :ACTIVITY_TYPE AND EFFT_DATE = :EFFT_DATE", $page_primary_keys);
            while ($row2 = $sql_exe2->fetch()) {
                $html_op .= "$('#DAYS_{$row2[ 'SL']}').val(decode_ajax('".$main_app->strsafe_ajax($row2['WEEKDAY'])."'));";

                if(isset($row2['FROM_TIME']) && $row2['FROM_TIME'] != NULL) {
                    $html_op .= "$('#FROM_HH_{$row2['SL']}').val(decode_ajax('".$main_app->strsafe_ajax(substr(str_pad($row2['FROM_TIME'], 4, '0', STR_PAD_LEFT),0,2))."'));";
                    $html_op .= "$('#FROM_MM_{$row2['SL']}').val(decode_ajax('".$main_app->strsafe_ajax(substr(str_pad($row2['FROM_TIME'], 4, '0', STR_PAD_LEFT),2,4))."'));";
                }

                if(isset($row2['UPTO_TIME']) && $row2['UPTO_TIME'] != NULL) {
                    $html_op .= "$('#UPTO_HH_{$row2['SL']}').val(decode_ajax('".$main_app->strsafe_ajax(substr(str_pad($row2['UPTO_TIME'], 4, '0', STR_PAD_LEFT),0,2))."'));";
                    $html_op .= "$('#UPTO_MM_{$row2['SL']}').val(decode_ajax('".$main_app->strsafe_ajax(substr(str_pad($row2['UPTO_TIME'], 4, '0', STR_PAD_LEFT),2,4))."'));";
                }
                
            }
        
        }
        else {
            //$html_op .= "$('#ENABLED').val(decode_ajax(''));";
            $html_op .= "$('#ACTIVITY_TYPE').val(decode_ajax(''));";
            $html_op .= "$('#EFFT_DATE').val(decode_ajax(''));";
            $html_op .= "$('#ALLOWED_ON_HOLIDAY').prop('checked',false);";
            $html_op .= "$('#RATE_FREEZE_TIME').val(decode_ajax(''));";
        }   
        
    }

    //Print
    echo "<script> {$html_op} </script>";

}

?>