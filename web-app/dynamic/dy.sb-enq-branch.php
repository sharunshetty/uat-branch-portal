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

    if($_POST['field_name'] == "modify") {

        if($_POST['field_val'] != "") {
  
            $key = explode('|', $_POST['field_val']);

            $send_data = array();
            $send_data['METHOD_NAME'] = "getInstaBrnDtls";
            $send_data['STATE_CODE'] = $key[1];
            $send_data['CITY_CODE'] = $key[0];

            try {
                $apiConn = new ReachMobApi;
                //$output = $apiConn->ReachMobConnect($send_data, "60");
                 $output = json_decode('{"responseCode":"S","brnList":[{"brnAddrs":"Hmt Estate Hmt Post,Office Jalahalli,Bangaluru,Karnataka,PinCode-560031","brnCode":"0202","brnName":"Jalahalli-Bangalore"}]}', true);

            } catch(Exception $e) {
                $ErrorMsg = $e->getMessage(); //Error from Class
            }

            if(!isset($ErrorMsg) || $ErrorMsg == "") {
                if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
                    $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
                }
            }

            if(isset($ErrorMsg) && $ErrorMsg != "") {
                // echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop();  </script>";
                $html_op .= '<option value="">'.$ErrorMsg.'</option>';
                echo "<script> $('#BRN_SOL').html('".$main_app->strsafe_output($html_op)."'); enable('BRN_SOL'); </script>";

                exit();
            }

            if(!isset($output['brnList']) || $output['brnList'] == "") {
                echo "<script> swal.fire('','Unable to process API response (A01)'); loader_stop(); </script>";
                exit();
            }


            if(isset($output['brnList']) && $output['brnList'] >0) {

                $html_op .= '<option value="">-- Select --</option>';
                foreach ($output['brnList'] as $outputdata) {
                    if(isset($outputdata['brnCode']) && $outputdata['brnCode'] != NULL) {
                        $html_op .= '<option value="'.$outputdata['brnCode'].'">'. $outputdata['brnName'] . '</option>';
                    }
                }
            }
            else {
                $html_op .= '<option value="'.$ErrorMsg.'"</option>';
            }

            //Print
            echo "<script> $('#BRN_SOL').html('".$main_app->jsescape($html_op)."'); enable('BRN_SOL'); </script>";
        
        } else {
            
            $html_op .= '<option value="">-- Select --</option>';

            //Print
            echo "<script> $('#BRN_SOL').html('".$main_app->jsescape($html_op)."'); enable('BRN_SOL'); </script>";

        }

    }

    

}

?>