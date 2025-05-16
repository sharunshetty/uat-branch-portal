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

            $send_data = array();
            $send_data['METHOD_NAME'] = "getInstaBrnCity";
            $send_data['STATE_CODE'] = $_POST['field_val'];

            try {
                $apiConn = new ReachMobApi;
                //$output = $apiConn->ReachMobConnect($send_data, "60");
                $output = json_decode('{"responseCode":"S","cityList":[{"cityCode":"BANGA","cityDesc":"Bangaluru"}]}', true);
            } catch(Exception $e) {
                $ErrorMsg = $e->getMessage(); //Error from Class
            }

            if(!isset($ErrorMsg) || $ErrorMsg == "") {
                if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
                    $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
                }
            }

            if(isset($ErrorMsg) && $ErrorMsg != "") {

                // $html_op .= '<option value="'.$ErrorMsg.'">'. $ErrorMsg . '</option>';
                $html_op .= '<option value="">'.$ErrorMsg.'</option>';
                echo "<script> $('#CITY_CODE').html('".$main_app->strsafe_output($html_op)."'); enable('CITY_CODE'); </script>";
                exit();
            }

            if(!isset($output['cityList']) || $output['cityList'] == "" || !is_array($output['cityList'])) {
                echo "<script> swal.fire('','Unable to process API response (A01)'); loader_stop(); </script>";
                exit();
            }

            if(isset($output['cityList']) && count($output['cityList']) > "0") {

                $html_op .= '<option value="">-- Select --</option>';
                foreach ($output['cityList'] as $outputdata) {
                    if(isset($outputdata['cityCode']) && $outputdata['cityCode'] != NULL) {
                        $html_op .= '<option value="'.$outputdata['cityCode'].'">'. $outputdata['cityDesc'] . '</option>';
                    }
                }
            }
            else {
                    $html_op .= '<option value="'.$ErrorMsg.'"</option>';
            }

            echo "<script> $('#CITY_CODE').html('".$main_app->jsescape($html_op)."'); enable('CITY_CODE'); </script>";

        }
         else {

            $html_op .= '<option value="">-- Select --</option>';

            //Print
            echo "<script> $('#CITY_CODE').html('".$main_app->strsafe_output($html_op)."'); enable('CITY_CODE'); </script>";

        }


    }

}

?>