<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();
$html_op = $add_row= $add_row1 = $add_row2 ="";

/** On Change */
if($_POST['cmd2'] == "onChange" && isset($_POST['field_name']) && isset($_POST['field_val']) && isset($_POST['dest_id'])) {

    if($_POST['field_name'] == "modify" && $_POST['field_val'] != "") {

        $dest_id = (isset($_POST['dest_id']) && $_POST['dest_id'] != NULL) ? $_POST['dest_id'] : '' ;

        // $PIN_CODE = isset($_POST['field_val']) ? $_POST['field_val'] : ""; 
        // $page_table_name = "SBREQ_PINCODE_DATA";
        
        // //get branch details
        // $sql_exe = $main_app->sql_run("SELECT DISTINCT STATE_CODE, DISTRICT_CODE, BRANCH_CODE FROM $page_table_name WHERE PIN_CODE = :PIN_CODE AND STATUS = '1'", array("PIN_CODE" => $PIN_CODE));
        // $row = $sql_exe->fetch();

        // $statecode = (isset($row['STATE_CODE']) && $row['STATE_CODE'] != "") ? $row['STATE_CODE'] : NULL;
        // $districtcode = (isset($row['DISTRICT_CODE']) && $row['DISTRICT_CODE'] != "") ? $row['DISTRICT_CODE'] : NULL;
        // $branchcode = (isset($row['BRANCH_CODE']) && $row['BRANCH_CODE'] != "") ? $row['BRANCH_CODE'] : NULL;

        // $sql_exe4 = $main_app->sql_run("select * from cbuat2.mbrn WHERE MBRN_CODE='$branchcode'");
        // $row4 = $sql_exe4->fetch();

        // // $add_row.='<option value = "'.$row4['MBRN_CODE'].'">'.$row4['MBRN_CODE']. '-' .$row4['MBRN_NAME'].'</option>';
        // // $html_op .= "$('#BRANCH_CODE').html('".$add_row."');";
        // // $html_op .="enable('BRANCH_CODE'); ";
        
        // $html_op .= "$('#BRANCH_NAME').val('".$main_app->strsafe_output($row4['MBRN_CODE']). " - ".$main_app->strsafe_output($row4['MBRN_NAME'])."'); $('#BRANCH_CODE').val('".$main_app->strsafe_output($row['MBRN_CODE'])."'); ";
              
        // //using db link query get state and city details
        // $sql_exe1 = $main_app->sql_run("select distinct  d.district_code as dcode, d.district_name as dname, s.state_code as scode, s.state_name as sname
        // from cbuat2.mbrn br
        // Join cbuat2.location l on l.locn_code = br.mbrn_locn_code
        // Join cbuat2.district d on  d.district_code = l.locn_district_code and d.district_state_code = l.locn_state_code
        // Join cbuat2.state s on s.state_code = l.locn_state_code and s.state_cntry_code = l.locn_cntry_code
        // where br.mbrn_entity_num=1 and br.mbrn_admin_unit_type in ('04') and br.mbrn_closure_date is NULL
        // and br.mbrn_code = '".$row4['MBRN_CODE']."'");
        // $row1 = $sql_exe1->fetch();

        // $district_code = (isset($row1['DCODE']) && $row1['DCODE'] != "") ? $row1['DCODE'] : NULL;
        // $district_name = (isset($row1['DNAME']) && $row1['DNAME'] != "") ? $row1['DNAME'] : NULL;
        // $state_code = (isset($row1['SCODE']) && $row1['SCODE'] != "") ? $row1['SCODE'] : NULL;
        // $state_name = (isset($row1['SNAME']) && $row1['SNAME'] != "") ? $row1['SNAME'] : NULL;

        // // $add_row1.='<option value="'.$district_code.'">'.$district_name.'</option>';
        // // $html_op .= "$('#DISTRICT_CODE').html('".$add_row1."');";
        // // $html_op .="enable('DISTRICT_CODE'); ";

        // $html_op .= "$('#DISTRICT_CODE').val('".$main_app->strsafe_output($district_code)."'); $('#DISTRICT_NAME').val('".$main_app->strsafe_output($district_name)."');";
        
        
        // //list all the states

        // $totalResults1 = $main_app->sql_fetchcolumn("SELECT count(0)
        // from cbuat2.mbrn br 
        // Join cbuat2.location l on l.locn_code = br.mbrn_locn_code
        // Join cbuat2.district d on  d.district_code = l.locn_district_code and d.district_state_code = l.locn_state_code
        // Join cbuat2.state s on s.state_code = l.locn_state_code and s.state_cntry_code = l.locn_cntry_code
        // where br.mbrn_entity_num = 1 and br.mbrn_admin_unit_type in ('04') and br.mbrn_closure_date is NULL");

        // if($totalResults1 > 0){

        //     $sql_exe2 = $main_app->sql_run("select distinct s.state_code, s.state_name 
        //     from cbuat2.mbrn br 
        //     Join cbuat2.location l on l.locn_code = br.mbrn_locn_code
        //     Join cbuat2.district d on  d.district_code = l.locn_district_code and d.district_state_code = l.locn_state_code
        //     Join cbuat2.state s on s.state_code = l.locn_state_code and s.state_cntry_code = l.locn_cntry_code
        //     where br.mbrn_entity_num = 1 and br.mbrn_admin_unit_type in ('04') and br.mbrn_closure_date is NULL", "");
        //     while($row2 = $sql_exe2->fetch()){
        //         $selected ='';
        //         if($state_code == $row2['STATE_CODE']){
        //             $selected ='selected';
        //         }
        //         $add_row2.='<option value ="'.$row2['STATE_CODE'].'"  '.$selected.'>'.$row2['STATE_NAME'].'</option>';
        //     }
              
      
        //     $html_op .= "$('#STATE').html('".$add_row2."');";
        //     $html_op .="enable('STATE'); ";

        // }else{

        //     $add_row .='<option value="">-- Select --</option>';
        //     $add_row .='<option value="">-- No records found --</option>';
        //     $html_op .= "$('#STATE').html('".$add_row2."');";
        //     $html_op .="enable('STATE'); ";
        // }


        // $html_op .= "$('#STATE').val('".$main_app->strsafe_output($row3['STATE_CODE'])."'); $('#STATE_NAME').val('".$main_app->strsafe_output($row3['STATE_NAME'])."');";
                   

        //----------------------
        
        $page_table_name = "SBREQ_PINCODE_DATA";
        $page_table_name2 = "LOCATION";
        $page_table_name3 = "STATE";
        $PIN_CODE = isset($_POST['field_val']) ? $_POST['field_val'] : ""; 

        $html_op = '';

        $totalResults1 = $main_app->sql_fetchcolumn("SELECT count(0) FROM $page_table_name WHERE PIN_CODE = :PIN_CODE", array("PIN_CODE" => $PIN_CODE));
     
        if($totalResults1 > 0){
            $sql_exe = $main_app->sql_run("SELECT DISTINCT BRANCH_CODE, BRANCH_NAME, DISTRICT_CODE, STATE_CODE
            FROM $page_table_name WHERE PIN_CODE = :PIN_CODE AND STATUS = '1'", array("PIN_CODE" => $PIN_CODE));
            $row = $sql_exe->fetch();

            $district_code = $row['DISTRICT_CODE'];
            $state_code = $row['STATE_CODE'];

            //display branch code and name
            // if(isset($row['BRANCH_CODE']) && $row['BRANCH_CODE'] != NULL) {

            //     $html_op .= "$('#BRANCH_NAME').val('".$main_app->strsafe_output($row['BRANCH_CODE']). " - ".$main_app->strsafe_output($row['BRANCH_NAME'])."'); $('#BRANCH_CODE').val('".$main_app->strsafe_output($row['BRANCH_CODE'])."'); ";

            //     // $html_op .= '<option value="'.$row['BRANCH_CODE'].'">'. $row['BRANCH_NAME'] . '</option>';

            //     // $html_op .= "$('#BRANCH_CODE').val('".$main_app->strsafe_output($row['BRANCH_CODE']). ">" .$main_app->strsafe_output($row['BRANCH_NAME'])."');";

            // }else {
            //     $html_op .= "$('#BRANCH_CODE').html('No records found'); </option>;";
            // }




           // $sql_exe1 = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_susp_rel_flag=' ' and m.mbrn_code=w.user_branch_code");
           //$sql_exe1 = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_susp_rel_flag=' ' and m.user_branch_code= '".$_SESSION['BRANCH_CODE']."' ");
           	
           
           //   $sql_exe1 = $main_app->sql_run("select mbrn_name,mbrn_code from cbuat.mbrn where mbrn_code= '".$_SESSION['BRANCH_CODE']."'");
		
           $html_op1 =' <option value="">-- Select --</option>';
           $html_op1 .='<option value="s">dddeeedddddd</option>';

           $html_op1 .='<option value="1">dddddd</option>';

            // while ($row1 = $sql_exe1->fetch()) {
            //     $html_op .= '<option value="'.$row1['BRANCH_CODE'].'">'. $row1['BRANCH_CODE'] .' ['. $row1['MBRN_NAME'] . ']</option>';
            // }
        
          
            echo "<script> $('#BRANCH_CODE').html('".$main_app->jsescape($html_op1)."');</script>";
      

            //display district
            if(isset($row['DISTRICT_CODE']) && $row['DISTRICT_CODE'] != NULL) {
                $totalResults2 = $main_app->sql_fetchcolumn("SELECT count(0) FROM $page_table_name2 WHERE LOCN_CODE = '{$district_code}'");
                if($totalResults2 > 0) {

                    $sql_exe2 = $main_app->sql_run("SELECT DISTINCT LOCN_CODE, LOCN_NAME FROM $page_table_name2 WHERE LOCN_CODE = '{$district_code}'");
                    $row2 = $sql_exe2->fetch();

                    $html_op .= "$('#DISTRICT_CODE').val('".$main_app->strsafe_output($row2['LOCN_CODE'])."'); $('#DISTRICT_NAME').val('".$main_app->strsafe_output($row2['LOCN_NAME'])."');";
                    // $html_op .= '<option value="'.$row2['LOCN_CODE'].'">'. $row2['LOCN_NAME'] . '</option>';
                    // $html_op .= "$('#DISTRICT_CODE').val('".$main_app->strsafe_output($row['LOCN_CODE']). " " .$main_app->strsafe_output($row['LOCN_NAME'])."');";

    
                }
                else {
                    $html_op .= "$('#DISTRICT_CODE').html('No records found'); </option>;";
                }
            }

            //display state
            if(isset($row['STATE_CODE']) && $row['STATE_CODE'] != NULL) {
                
                $totalResults3 = $main_app->sql_fetchcolumn("SELECT count(0) FROM $page_table_name3 WHERE STATE_CODE = '{$state_code}'");
                if($totalResults3 > 0) {

                    $sql_exe3 = $main_app->sql_run("SELECT DISTINCT STATE_CODE, STATE_NAME FROM $page_table_name3 WHERE STATE_CODE = '{$state_code}'");
                    $row3 = $sql_exe3->fetch();

                    $html_op .= "$('#STATE').val('".$main_app->strsafe_output($row3['STATE_CODE'])."'); $('#STATE_NAME').val('".$main_app->strsafe_output($row3['STATE_NAME'])."');";
                    // $html_op .= '<option value="'.$row3['STATE_CODE'].'">'. $row3['STATE_NAME'] . '</option>';
                    // $html_op .= "$('#STATE').val('".$main_app->strsafe_output($row['STATE_CODE']). " " .$main_app->strsafe_output($row['STATE_NAME'])."');";

    
                }
                else {
                    $html_op .= "$('#STATE').html('No records found'); </option>;";
                }
            }
        }else{

            $html_op .= "swal.fire('', 'Branch doesnt exist for your location')";

        }

        //Print
        echo "<script> {$html_op} </script>";

    }
}

?>