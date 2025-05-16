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

    // onChange : VENDOR_TYPE
    if($_POST['field_name'] == "STATE_CODE") {
        $field_output = $main_app->getval_field('LOC_STATE_MASTER','STATE_NAME','STATE_CODE',$_POST['field_val']);
        if($field_output) { $field_output = "<span class='tsuccess'>{$field_output}</span>"; } 
        else { $field_output = "<span class='tdanger'>Invalid State Code</span>"; }
        $html_op .= "$('#'+decode_ajax('".$main_app->strsafe_ajax($_POST['dest_id'])."')).html(decode_ajax('".$main_app->strsafe_ajax($field_output)."'));";
    } 
    else if($_POST['field_name'] == "REGION_CODE") {
        $field_output = $main_app->getval_field('LOC_REGION_MASTER','REGION_NAME','REGION_CODE',$_POST['field_val']);
        if($field_output) { $field_output = "<span class='tsuccess'>{$field_output}</span>"; } 
        else { $field_output = "<span class='tdanger'>Invalid Region Code</span>"; }
        $html_op .= "$('#'+decode_ajax('".$main_app->strsafe_ajax($_POST['dest_id'])."')).html(decode_ajax('".$main_app->strsafe_ajax($field_output)."'));";
    }
     else if($_POST['field_name'] == "CITY_CODE") {
        
        $page_primary_keys = array(
            'CITY_CODE' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
        );

        $sql_exe = $main_app->sql_run("SELECT * FROM LOC_CITY_MASTER WHERE CITY_CODE = :CITY_CODE", $page_primary_keys);
        $item_data = $sql_exe->fetch();

        $field_output = isset($item_data['CITY_NAME']) ? $item_data['CITY_NAME'] : '';
        if($field_output) { $field_output = "<span class='tsuccess'>{$field_output}</span>"; } 
        else { $field_output = "<span class='tdanger'>Invalid City Code</span>"; }
        $html_op .= "$('#'+decode_ajax('".$main_app->strsafe_ajax($_POST['dest_id'])."')).html(decode_ajax('".$main_app->strsafe_ajax($field_output)."'));";
        $html_op .= "$('#STATE_CODE').val(decode_ajax('".$main_app->strsafe_ajax($item_data['CITY_STATE_CODE'])."'));";
        $field_output2 = $main_app->getval_field('LOC_STATE_MASTER','STATE_NAME','STATE_CODE',$item_data['CITY_STATE_CODE']);
        $field_output2 = "<span class='tsuccess'>{$field_output2}</span>";        
        $html_op .= "$('#STATE_CODE_VAL').html(decode_ajax('".$main_app->strsafe_ajax($field_output2)."'));";
        
    }  
    else if($_POST['field_name'] == "CHANNEL_ID") {
        $field_output = $main_app->getval_field('VKYC_MAST_CHANNELS','CHANNEL_NAME','CHANNEL_ID',$_POST['field_val']);
        if($field_output) { $field_output = "<span class='tsuccess'>{$field_output}</span>"; } 
        else { $field_output = "<span class='tdanger'>Invalid Channel Code</span>"; }
        $html_op .= "$('#'+decode_ajax('".$main_app->strsafe_ajax($_POST['dest_id'])."')).html(decode_ajax('".$main_app->strsafe_ajax($field_output)."'));";
    }  
    else if($_POST['field_name'] == "SERVICE_CODE") {
        $field_output = $main_app->getval_field('VKYC_MAST_SERVICES','SERVICE_DESC','SERVICE_CODE',$_POST['field_val']);
        if($field_output) { $field_output = "<span class='tsuccess'>{$field_output}</span>"; } 
        else { $field_output = "<span class='tdanger'>Invalid Services Code</span>"; }
        $html_op .= "$('#'+decode_ajax('".$main_app->strsafe_ajax($_POST['dest_id'])."')).html(decode_ajax('".$main_app->strsafe_ajax($field_output)."'));";
    }  
    
    //
    // else if($_POST['field_name'] == "BRANCH_CODE") {
    //     $field_output = $main_app->getval_field('PSD_BRANCH','BRN_NAME','BRN_CD',$_POST['field_val']);
    //     if($field_output) { $field_output = "<span class='tsuccess'>{$field_output}</span>"; } 
    //     else { $field_output = "<span class='tdanger'>Invalid Branch Code</span>"; }
    //     $html_op .= "$('#'+decode_ajax('".$main_app->strsafe_ajax($_POST['dest_id'])."')).html(decode_ajax('".$main_app->strsafe_ajax($field_output)."'));";
    // }

    //Print
    echo "<script> {$html_op} </script>";

}

?>