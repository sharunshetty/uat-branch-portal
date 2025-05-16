<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** On Change */
if($_POST['cmd2'] == "onChange" && isset($_POST['field_name']) && isset($_POST['field_val']) && isset($_POST['dest_id'])) {

    if($_POST['field_name'] == "modify" && $_POST['field_val'] != "") {

       // $dest_id = (isset($_POST['dest_id']) && $_POST['dest_id'] != NULL) ? $_POST['dest_id'] : '' ;

        $email = (isset($_POST['field_val']) && $_POST['field_val'] != NULL) ? $_POST['field_val'] : '' ;

        if($email){
            
            //Generate OTP
            if(APP_PRODUCTION == false) {
                $new_otp = "123123";
            } else {
                $new_otp = $main_app->get_otpcode();  
            }   
            
             //resend otp checking 
            if($email != $_SESSION['CUST_EMAIL'] ){

 		        $otpReqId = $main_app->sql_sequence("LOG_OTPREQ_SEQ","OTP"); 
		        // $otpReqId = $main_app->sql_fetchcolumn("SELECT 'OTP' || TO_CHAR(SYSDATE,'YYYYMMDD')  || APP_OTP_LOGS_SEQ.NEXTVAL FROM DUAL"); // Seq. No.
                //$otpReqId = $main_app->sql_fetchcolumn("SELECT CONCAT('OTP', DATE_FORMAT(SYSDATE(),'%Y%m%d'), nextVal(log_otpreq_seq)) FROM dual; "); 

                $data = array();
                $data['OTP_REQ_ID'] = $otpReqId;
                $data['OTP_PGMCODE'] = "N"; // New Registration
                // $data2['LOGIN_REQ_NUM'] = $LoginRefNum;
                $data['OTP_EMAIL_ID'] = $email;
                // $data['OTP_EMAIL_ID'] = "";
                $data['EMAIL_VERIFIED_FLAG'] = "P";
                $data['GEN_PLATFORM'] = $browser->getPlatform();
                $data['GEN_BROWSER_NAME'] = $browser_name;
                $data['GEN_BROWSER_VER'] = $browser_ver;
                $data['GEN_IP_ADDRESS'] = $main_app->current_ip();
                // $data['CR_BY'] = "";
                $data['CR_ON'] = date("Y-m-d H:i:s");

                $db_updated = $main_app->sql_insert_data("LOG_OTPREQ", $data);
            }else{

                $otpReqId = $_SESSION['EMAILOTP_REQ_ID']; 
                //$sql1_exe = $main_app->sql_run("SELECT EMAIL_RESENT_COUNT FROM LOG_OTPREQ WHERE OTP_EMAIL_ID = :OTP_EMAIL_ID", array( 'OTP_EMAIL_ID' =>$_SESSION['CUST_EMAIL'] ));
                $sql1_exe = $main_app->sql_run("SELECT EMAIL_RESENT_COUNT FROM LOG_OTPREQ WHERE OTP_REQ_ID = :OTP_REQ_ID", array('OTP_REQ_ID' => $otpReqId));            
                $otp_data = $sql1_exe->fetch();
                $data = array();
                $data['EMAIL_RESENT_COUNT'] = $otp_data['EMAIL_RESENT_COUNT'] + 1;
                //$db_updated = $main_app->sql_update_data("LOG_OTPREQ", $data, array('OTP_EMAIL_ID' => $_SESSION['CUST_EMAIL'])); // Update
                $db_updated = $main_app->sql_update_data("LOG_OTPREQ", $data, array('OTP_REQ_ID' => $otpReqId)); // Update
              
                
            }

            if($db_updated == false) {
                echo "Unable to generate OTP";
            } else {

                //API - SEND SMS OTP VIA MB SERVER CALL
                $send_data['METHOD_NAME'] = "notifyUser";
               // $send_data['MOBILE_NUMBER'] =  $email;
                $send_data['EMAIL_ID'] = $email;
                $send_data['OTP_EMAIL_CODE'] = $new_otp;
                $send_data['SERVICE_CODE'] = "REKYC-OTP-SMS";
                $send_data['REQ_TYPE'] = "S";
                $send_data['CHANNEL_CODE'] = API_REACH_MB_CHANNEL;
                $send_data['USER_AGENT'] = $browser->getBrowser();

                try {
                    // $apiConn = new ReachMobApi;
                    // $output = $apiConn->ReachMobConnect($send_data, "60");
                    // Test Data
                    $output = json_decode('{"responseCode":"S"}', true);

                } catch(Exception $e) {
                    error_log($e->getMessage());
                    $ErrorMsg = "Technical Error, Please try later"; //Error from Class
                }

                if(!isset($ErrorMsg) || $ErrorMsg == "") {
                    if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
                        $ErrorMsg = isset($output['errorMessage']) ? $output['errorMessage'] : "Unexpected API Error!";
                    }
                }

                if(isset($ErrorMsg) && $ErrorMsg != "") {
                    echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('btnSignIn'); </script>";
                    exit();
                }

                $data2 = array();
                //$data2['REQ_DATA'] = "";
                $data2['EMAIL_SENT_RESP'] = (isset($output['responseCode']) && $output['responseCode'] == "S") ? $output['responseCode'] : NULL;
                $main_app->sql_update_data("LOG_OTPREQ",$data2, array('OTP_REQ_ID' => $otpReqId));
                
                $OTP_RESEND_CNT = $main_app->sql_fetchcolumn("SELECT OPTION_VALUE FROM APP_DATA_SETTINGS WHERE OPTION_NAME = 'OTP_RESEND_COUNT' AND OPTION_STATUS = '1'");

                /** OTP Session */
                $main_app->session_set([
                    'USER_APP' => APP_CODE,
                    'EMAILOTP_REQ_ID' => $otpReqId, // OTP Request ID
                    'LOGIN_TYPE' => "otp",
                    'USR_ID' =>"",
                    'CUST_NAME' => "",
                    'CUST_EMAIL' => $email,
                    'LOGIN_REQ_NUM' => "",
                    'OTP_ID' => "",
                    'OTP_EMAIL_CODE' => $new_otp,
                    'OTP_TIMEOUT' => time(),
                    'OTP_RESEND_COUNT' => ($OTP_RESEND_CNT && is_numeric($OTP_RESEND_CNT)) ? $OTP_RESEND_CNT : "2",
                    'OTP_SMS_CHK_COUNT' => "1",        
                ]);

               // echo "ok";
            }
        }  else {
            echo "Unable to proceed";
        }

    }
   
}

?>