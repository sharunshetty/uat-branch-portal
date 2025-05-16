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

    //    $sql_exe1 = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
    //    $item_data1 = $sql_exe1->fetch();

    //     if(isset($item_data1['TRANSFER_BRNCODE']) && $item_data1['TRANSFER_BRNCODE'] != ''){  //check if  future date transfer branch is there
            
    //         $TRANSFER_DATE = new DateTime($item_data1['TRANSFER_DATE']); 
    //         $CUR_DATE = new DateTime(); 
        
    //         if(isset($item_data1['TRANSFER_DATE']) && $item_data1['TRANSFER_DATE'] != '' &&  $CUR_DATE >= $TRANSFER_DATE) {
                
    //             $data1 = array();
    //             $data1['USER_REGIONS'] = $item_data1['TRANSFER_BRNCODE'];
    //             $data1['TRANSFER_BRNCODE'] = "";

    //             $main_app->sql_db_auditlog('M','ASSREQ_USER_ACCOUNTS',$data1,$page_primary_keys); // Audit Log - Modify
    //             $db_output = $main_app->sql_update_data('ASSREQ_USER_ACCOUNTS',$data1,$page_primary_keys); // Update
    //             if($db_output == false) { 
    //                 exit('Unable to Continue'); // Stop
    //             }
    //         }
    //     }




        //if(isset($item_data['PGM_CODE']) && $item_data['PGM_CODE'] != NULL) {
       
        // $STATUS = "A";    
        // if(isset($item_data['RESIGN_DATE']) && $item_data['RESIGN_DATE'] != "") {
        //     $CUR_DATE = new DateTime(); 
        //     $RESIGN_DATE = new DateTime($item_data['RESIGN_DATE']); 

        //     if($RESIGN_DATE <= $CUR_DATE) {
        //         $STATUS = "R";
        //     }
        // }


        $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $page_primary_keys);
        $item_data = $sql_exe->fetch();

        if(isset($item_data['USER_STATUS']) && $item_data['USER_STATUS'] == "A") { 
            $STATUS = "A";    
            if(isset($item_data['RESIGN_DATE']) && $item_data['RESIGN_DATE'] != "") {
                $CUR_DATE = new DateTime(); 
                $RESIGN_DATE = new DateTime($item_data['RESIGN_DATE']); 
    
                if($RESIGN_DATE <= $CUR_DATE) {
                    $STATUS = "R";
                }		
            }
        }else{
            $STATUS = $item_data['USER_STATUS'];  //blocked,transfer pending
        }


        //Update Value
        $html_op .= "$('#USER_FULLNAME').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_FULLNAME'])."'));";
        $html_op .= "$('#USER_ROLE_CODE').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_ROLE_CODE'])."'));";
        $html_op .= "$('#USER_MOBILE').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_MOBILE'])."'));";
        $html_op .= "$('#USER_EMAIL').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_EMAIL'])."'));";
        $html_op .= "$('#USER_STATUS').val(decode_ajax('".$main_app->strsafe_ajax($STATUS)."'));";       
        //$html_op .= "$('#USER_REGIONS').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_REGIONS'])."'));";   
        $html_op .= "$('#USER_REGIONS').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_REGIONS'])."'));";   
        
          // if($item_data['USER_STATUS'] =='A')  {  $html_op .= "show('transfer');"; }  
        
        // if($item_data['USER_STATUS'] =='R')  {  $html_op .= "show('status');"; }  
        
        
        //else{
                
               // $html_op .= "hide('status');"; 
                //Modify - Sub Table Details
                /* $page_sub_table_name = "USER_ACCOUNTS_REGIONS";

                $page_primary_keys2 = array(
                    'AR_USER_ID' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
                );

                $sql_exe2 = $main_app->sql_run("SELECT * FROM {$page_sub_table_name} WHERE AR_USER_ID = :AR_USER_ID", $page_primary_keys2);
                while ($row = $sql_exe2->fetch()) {
                    if(isset($row['AR_REGION_CODE']) && $row['AR_REGION_CODE'] != NULL) {
                        $html_op .= "$('#RGM_'+decode_ajax('".$main_app->strsafe_ajax($row['AR_REGION_CODE'])."')).prop('checked',true); ";
                    }
                }  */
       // }
             
        
        //} 
        
    }

    //Print
    echo "<script> {$html_op};</script>";
   // $('#USER_STATUS').attr('readonly', 'readonly');

}

?>