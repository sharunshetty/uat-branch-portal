<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();
$html_op = $html_op1 = "";

/** On Change */
if($_POST['cmd2'] == "onChange" && isset($_POST['field_name']) && isset($_POST['field_val']) && isset($_POST['dest_id'])) {

    if($_POST['field_name'] == "modify" && $_POST['field_val'] != "") {
        echo "<script> show('resp-table'); $('#dyn_data').children('tr').remove(); </script>";
        
        $page_table_name = "ASSREQ_USER_ACCOUNTS";
        $page_primary_keys = array(
            'USER_ID' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
        );

        // $page_primary_keys1 = array(
        //     'CR_BY' => (isset($_POST['field_val'])) ? $main_app->strsafe_input($_POST['field_val']) : "",
        // );

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

            echo " <script>";
            echo " $('#USER_FULLNAME').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_FULLNAME'])."')); ";
            echo " $('#USER_BRANCH').val(decode_ajax('".$main_app->strsafe_ajax($item_data['USER_REGIONS'])."')); ";
            echo " </script>";
            $sql_exe1 = $main_app->sql_run("SELECT USER_ID,USER_FULLNAME,USER_ROLE_CODE,USER_REGIONS FROM ASSREQ_USER_ACCOUNTS WHERE USER_ID IS NOT NULL AND USER_ID != '".$item_data['USER_ID']."' AND USER_STATUS = 'A' AND RESIGN_DATE IS NULL  AND USER_REGIONS = '".$item_data['USER_REGIONS']."' ");
            $html_op .= '<option value="">-- Select --</option>';     
            while ($row = $sql_exe1->fetch() ) {
                $html_op .= '<option value="'.$row['USER_ID'].'">'. $row['USER_ID'] .' ['. $row['USER_FULLNAME'] . ']</option>';
            }
           echo "<script> $('#TRANSFER_USER').html('".$main_app->jsescape($html_op)."');</script>";


            $sql_exe2= $main_app->sql_run("SELECT ASSREQ_REF_NUM,ASSREQ_CUST_FNAME,ASSREQ_MOBILE_NUM,ASSREQ_EMAIL,APP_STATUS,CR_ON,CR_BY FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM IS NOT NULL AND CR_BY = :USER_ID AND APP_STATUS IS NULL order by CR_ON desc", $page_primary_keys);
            $users_data = $sql_exe2->fetchAll();

            $add_row = "";
            $i = 0;
            if(count($users_data) > 0){
                foreach($users_data as $udata) {
                    $i++;
                    if(isset($udata['ASSREQ_REF_NUM']) && $udata['ASSREQ_REF_NUM'] != "") {

                        $RefNum = (isset($udata['ASSREQ_REF_NUM']) && $udata['ASSREQ_REF_NUM'] != "") ? $udata['ASSREQ_REF_NUM'] : "";
                        $UserName = (isset($udata['ASSREQ_CUST_FNAME']) && $udata['ASSREQ_CUST_FNAME'] != "") ? $udata['ASSREQ_CUST_FNAME'] : "";
                        $UserMobileNum = (isset($udata['ASSREQ_MOBILE_NUM']) && $udata['ASSREQ_MOBILE_NUM'] != "") ?  $udata['ASSREQ_MOBILE_NUM'] : "";
                        $UserEmailId = (isset($udata['ASSREQ_EMAIL']) && $udata['ASSREQ_MOBILE_NUM'] != "") ?  $udata['ASSREQ_EMAIL'] : "";
                        
                        $UserStatus = ($udata['APP_STATUS'] == "") ? "Pending" : "";
                        
                        $UserCrOn = (isset($udata['CR_ON']) && $udata['CR_ON'] != "") ? $udata['CR_ON'] : "";
                        $UserCrBy = (isset($udata['CR_BY']) && $udata['CR_BY'] != "") ? $udata['CR_BY'] : "";
                            
                        $add_row.='<tr>';

                        $add_row.='<input type="hidden" name="USER_REF_NUM[]" id="USER_REF_NUM'.$i.'" class="form-control" value="'.$RefNum.'">';  
                        $add_row.='<td class="text-left">'.$main_app->strsafe_output($RefNum).'</td>';
                        $add_row.='<td class="text-left"> '.$main_app->strsafe_output($UserName).' <br/><small>'.$main_app->strsafe_output($UserMobileNum).'<br/>'.$main_app->strsafe_output($UserEmailId).'</small></td>';
                        $add_row.='<td class="text-left">'.$main_app->strsafe_output($UserStatus).'</td>';
                        $add_row.='<td class="text-left"> '.$main_app->valid_date($UserCrOn,'d-m-Y H:i:s A').' <br/><small>By:'.$main_app->strsafe_output($UserCrBy).'</small></td>';
                    
                        $add_row.='<td width="15%" class="text-center"><input type="checkbox" class="form-radio checkbox" name="SELECTS[]" id="SELECTS_'.$i.'" data-id="'.$i.'" onclick="select_user('.$i.')" value=""></td>';
                        
                        $add_row.='</tr>';

                        
                    }       
                }
                $add_row.='<tr ><td><input type="hidden" name="HIDDEN_TOTCOUNT" id="HIDDEN_TOTCOUNT" class="form-control" value="'.$i.'"></td></tr>';
                echo "<script> show('SELECT_ALL'); </script>";

            }else{

                $add_row.='<tr><td colspan="6"> No Record Found</td></tr>';
                echo "<script> hide('SELECT_ALL'); </script>";
            }

            $html_op1.= "$('#dyn_data').append('$add_row');";
            echo "<script> {$html_op1} </script>";
        }
    }
}


?>