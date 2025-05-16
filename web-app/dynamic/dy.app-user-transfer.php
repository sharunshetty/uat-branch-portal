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
        $page_table_name = "ASSREQ_USER_ACCOUNTS";
        $page_primary_keys = array(
            'USER_ID' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
        );


        // $sql_exe1 = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
        //  $item_data1 = $sql_exe1->fetch();

        // if(isset($item_data1['TRANSFER_BRNCODE']) && $item_data1['TRANSFER_BRNCODE'] != ''){  //check if  future date transfer branch is there
            
        //     $TRANSFER_DATE = new DateTime($item_data1['TRANSFER_DATE']); 
        //     $CUR_DATE = new DateTime(); 
        
        //     if(isset($item_data1['TRANSFER_DATE']) && $item_data1['TRANSFER_DATE'] != '' &&  $CUR_DATE >= $TRANSFER_DATE) {
                
        //         $data1 = array();
        //         $data1['USER_REGIONS'] = $item_data1['TRANSFER_BRNCODE'];
        //         $data1['TRANSFER_BRNCODE'] = "";

        //         $main_app->sql_db_auditlog('M','ASSREQ_USER_ACCOUNTS',$data1,$page_primary_keys); // Audit Log - Modify
        //         $db_output = $main_app->sql_update_data('ASSREQ_USER_ACCOUNTS',$data1,$page_primary_keys); // Update
        //         // if($db_output == false) { 
        //         //     exit('Unable to Login'); // Stop
        //         // }
        //     }
        // }


        $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
        $item_data = $sql_exe->fetch();

        if(isset($item_data['USER_ID'])) {
            
            $html_op .= "$('#USER_FULLNAME').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_FULLNAME'])."'));";
            $html_op .= "$('#USER_MOBILE').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_MOBILE'])."'));";  
            $html_op .= "$('#TRANSFER_FROMBRANCH').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_REGIONS'])."'));";   
        
       
        }
        // else {
        //     $html_op .= "$('#OPERATION').val(decode_ajax('".$main_app->strsafe_ajax('A')."')); $('#STATUS').val(''); focus('STATUS');";
        // }
        
    }

    //Print
    echo "<script> {$html_op} </script>";

}

?>