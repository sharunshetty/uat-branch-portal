<?php

    /**
     * @copyright   : (c) 2020 Copyright by LCode Technologies
     * @author      : Shivananda Shenoy (Madhukar)
     **/

    /** Application Core */
    require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

    /** Check User Session */
    require_once(dirname(__FILE__) . '/check-login.php');

    /** SQL */
    $page_table_name='ASSREQ_MASTER';
    $add_sql = "ASSREQ_REF_NUM IS NOT NULL AND APP_STATUS='S'";
    $add_data = array();
    $additionalVars = "";

    if(isset($_GET['from_date'])) { $from_date = $main_app->strsafe_input(trim($_GET['from_date'])); $filter = true; $additionalVars .= "&from_date=".$from_date; } else { $from_date = ""; }
    if(isset($_GET['to_date']))   { $to_date = $main_app->strsafe_input(trim($_GET['to_date'])); $filter = true; $additionalVars .= "&to_date=".$to_date; } else { $to_date = ""; }
    if(isset($_GET['status']))    { $status = $main_app->strsafe_input(trim($_GET['status'])); $filter = true; $additionalVars .= "&status=".$status; } else { $status = ""; }
    
    $file_name = time(); // File Name
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=csfb-account-request'.$file_name.'.csv');
    $fp = fopen('php://output', 'w');
    
    // if(isset($status) && $status != NULL) {
    //     $add_sql .= " AND REKYC_STATUS = :REKYC_STATUS";
    //     $add_data['REKYC_STATUS'] = $status;
    // }

    if(isset($status) && $status != "") {
        $add_sql .= " AND TRIM(AUTH_STATUS) = :AUTH_STATUS";
        $add_data['AUTH_STATUS'] = $status;
    }


    $sql_frm_date=$sql_to_date='';
    if($from_date != "") {$sql_frm_date = date('d-m-Y',strtotime($from_date)); }
    if($to_date != "")   {$sql_to_date = date('d-m-Y',strtotime($to_date)); }
  
  
    if($from_date != "" && $to_date != "" ) { $add_sql .= " AND TRUNC(CR_ON) BETWEEN TO_DATE('{$sql_frm_date}','DD-MM-YYYY') AND TO_DATE('{$sql_to_date}','DD-MM-YYYY')"; }
    elseif($from_date != "" && $to_date == "" ) { $add_sql .= " AND TRUNC(CR_ON) >= TO_DATE('{$sql_frm_date}','DD-MM-YYYY')"; }
    elseif($from_date == "" && $to_date != "" ) { $add_sql .= " AND TRUNC(CR_ON) <= TO_DATE('{$sql_to_date}','DD-MM-YYYY')"; }

    $headers = array('Account Reference No','Customer Name','Mobile No.','Status','Created On'); 

    fputcsv($fp, $headers);

    $final_query = "SELECT * FROM $page_table_name WHERE {$add_sql} order by CR_ON desc";
    $sql_exe = $main_app->sql_run("$final_query",$add_data);
    $i=1;
    while($item_data = $sql_exe->fetch()){

        $REF_NUM = $item_data['ASSREQ_REF_NUM'];
        $CUST_NAME = $item_data['ASSREQ_CUST_FNAME'];
        $CUST_MOBILE  = $item_data['ASSREQ_MOBILE_NUM'];
        $CR_ON = $item_data['CR_ON'];

        $STATUS_TYPE ='';
        if(isset($item_data['AUTH_STATUS']) &&  TRIM($item_data['AUTH_STATUS']) == "AS") {
            $STATUS_TYPE = "APPROVED";              
        } elseif(isset($item_data['AUTH_STATUS']) &&  TRIM($item_data['AUTH_STATUS']) == "AR") {        
            $STATUS_TYPE = "REJECTED";
         }

        $push_data = array( $REF_NUM, $CUST_NAME, $CUST_MOBILE, $STATUS_TYPE, $CR_ON );         
        fputcsv($fp, $push_data);
        $i++;
    }
    
    fclose($fp);

    ?>
   