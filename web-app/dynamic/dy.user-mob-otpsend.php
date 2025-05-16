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

        $dest_id = (isset($_POST['dest_id']) && $_POST['dest_id'] != NULL) ? $_POST['dest_id'] : '' ;

        $mobno = (isset($_POST['field_val']) && $_POST['field_val'] != NULL) ? $_POST['field_val'] : '' ;

        if($mobno){

            //new customer entry mobile no verify
            if(isset($dest_id) && $dest_id =='MOBILE') {


                // if(isset($_SESSION['OTP_REQ_ID'])) {
                    
                //     $sql1_exe = $main_app->sql_run("SELECT  SMS_RESENT_COUNT FROM LOG_OTPREQ WHERE OTP_REQ_ID = :OTP_REQ_ID", array( 'OTP_REQ_ID' =>$_SESSION['OTP_REQ_ID'] ));
                //     $otp_data = $sql1_exe->fetch();

                //     if(!isset($otp_data['SMS_RESENT_COUNT']) || $otp_data['SMS_RESENT_COUNT'] >= "3") {
                //         echo "<script> swal.fire('','You have reached maximum OTP resend limit'); disable('ResendBtn'); </script>";
                //         exit(); 
                //     }
                // }

                //checking mobile exist in ASSREQ_MASTER table or NOT
                $mobnochck = $main_app->sql_fetchcolumn("SELECT count(0) FROM ASSREQ_MASTER WHERE ASSREQ_MOBILE_NUM = :ASSREQ_MOBILE_NUM and AUTH_STATUS != 'AR'",array('ASSREQ_MOBILE_NUM' => $mobno));
                if($mobnochck>=1){
                    echo "<script> focus('CUST_MOBILE');swal.fire('','Mobile No already exists for the customer. Please change the Mobile No '); loader_stop('myform'); enable('sbt'); 
                    hide('MOB_OTPENTRY'); 
                    $('#CUST_MOBILE,#mobsbt').prop('readonly', false);
                    </script>";         
                }
                
                //Generate OTP
                if(APP_PRODUCTION == false) {
                    $new_otp = "123123";
                } else {
                    $new_otp = $main_app->get_otpcode();  
                }     

                // if(!isset($_SESSION['OTP_REQ_ID']) || $_SESSION['OTP_REQ_ID'] == NULL || $_SESSION['OTP_REQ_ID'] == '') {
            
                //resend otp checking
                // fail case
                if($mobno != $_SESSION['CUST_MOBILE'] ){
                
                    $otpReqId = $main_app->sql_sequence("LOG_OTPREQ_SEQ","OTP"); 
               
                    //$otpReqId = $main_app->sql_sequence("LOG_OTPREQ_SEQ","OTP"); 
                    //$otpReqId = $main_app->sql_fetchcolumn("SELECT 'OTP' || TO_CHAR(SYSDATE,'YYYYMMDD')  || APP_OTP_LOGS_SEQ.NEXTVAL FROM DUAL"); // Seq. No.
                    //$otpReqId = $main_app->sql_fetchcolumn("SELECT CONCAT('OTP', DATE_FORMAT(SYSDATE(),'%Y%m%d'), nextVal(log_otpreq_seq)) FROM dual; "); 
                    // $otpReqId='123456';

                    if($otpReqId == false || $otpReqId == "" || $otpReqId == "1") {
                       // echo "Unable to generate OTP Reference ID";
                        echo "<script> swal.fire('','Unable to generate OTP Reference ID'); loader_stop(); </script>";
                    } else {
                        $data = array();
                        $data['OTP_REQ_ID'] = $otpReqId;
                        $data['OTP_PGMCODE'] = "N"; // New Registration
                        // $data2['LOGIN_REQ_NUM'] = $LoginRefNum;
                        $data['OTP_MOBILE_NUM'] = $mobno;
                        // $data['OTP_EMAIL_ID'] = "";
                        $data['SMS_VERIFIED_FLAG'] = "P";
                        $data['GEN_PLATFORM'] = $browser->getPlatform();
                        $data['GEN_BROWSER_NAME'] = $browser_name;
                        $data['GEN_BROWSER_VER'] = $browser_ver;
                        $data['GEN_IP_ADDRESS'] = $main_app->current_ip();
                        // $data['CR_BY'] = "";
                        $data['CR_ON'] = date("Y-m-d H:i:s");
                        $db_updated = $main_app->sql_insert_data("LOG_OTPREQ", $data);
                    }
                
                }else{
                    // resend otp
                    $otpReqId = $_SESSION['MOBOTP_REQ_ID']; 
                    // $sql1_exe = $main_app->sql_run("SELECT SMS_RESENT_COUNT FROM LOG_OTPREQ WHERE OTP_MOBILE_NUM = :OTP_MOBILE_NUM", array( 'OTP_MOBILE_NUM' =>$_SESSION['CUST_MOBILE'] ));
                    $sql1_exe = $main_app->sql_run("SELECT SMS_RESENT_COUNT FROM LOG_OTPREQ WHERE OTP_REQ_ID = :OTP_REQ_ID", array('OTP_REQ_ID' => $otpReqId));             
                    $otp_data = $sql1_exe->fetch();
                    $data = array();
                    $data['SMS_RESENT_COUNT'] = $otp_data['SMS_RESENT_COUNT'] + 1;
                    // $db_updated = $main_app->sql_update_data("LOG_OTPREQ", $data, array('OTP_MOBILE_NUM' => $_SESSION['CUST_MOBILE'])); // Update
                    $db_updated = $main_app->sql_update_data("LOG_OTPREQ", $data, array('OTP_REQ_ID' => $otpReqId)); // Update
                
                }

                if($db_updated == false) {
                    //echo "Unable to generate OTP";
                    echo "<script> swal.fire('','Unable to generate OTP'); loader_stop(); </script>";
                    exit();
                } else {

                    //API - SEND SMS OTP VIA MB SERVER CALL
                    $send_data['METHOD_NAME'] = "notifyUser";
                    $send_data['MOBILE_NUMBER'] =  $mobno;
                    $send_data['EMAIL_ID'] = "";
                    $send_data['OTP_SMS_CODE'] = $new_otp;
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
                    $data2['SMS_SENT_RESP'] = (isset($output['responseCode']) && $output['responseCode'] == "S") ? $output['responseCode'] : NULL;
                    $main_app->sql_update_data("LOG_OTPREQ",$data2, array('OTP_REQ_ID' => $otpReqId));
                    
                    $OTP_RESEND_CNT = $main_app->sql_fetchcolumn("SELECT OPTION_VALUE FROM APP_DATA_SETTINGS WHERE OPTION_NAME = 'OTP_RESEND_COUNT' AND OPTION_STATUS = '1'");

                    /** OTP Session */
                    $main_app->session_set([
                        'USER_APP' => APP_CODE,
                        'MOBOTP_REQ_ID' => $otpReqId, // OTP Request ID
                        'LOGIN_TYPE' => "otp",
                        'USR_ID' =>"",
                        'CUST_NAME' => "",
                        'CUST_MOBILE' => $mobno,
                        'LOGIN_REQ_NUM' => "",
                        'OTP_ID' => "",
                        'OTP_SMS_CODE' => $new_otp,
                        'OTP_TIMEOUT' => time(),
                        'OTP_RESEND_COUNT' => ($OTP_RESEND_CNT && is_numeric($OTP_RESEND_CNT)) ? $OTP_RESEND_CNT : "2",
                        'OTP_SMS_CHK_COUNT' => "1",        
                    ]);

                // echo "ok";
                }
            }

            elseif(isset($dest_id) && $dest_id == 'CUSTMOB') {    //one more time customer verification
                //Generate OTP
                if(APP_PRODUCTION == false) {
                    $new_otp = "123123";
                } else {
                    $new_otp = $main_app->get_otpcode();  
                }  

               $otpReqId = $main_app->sql_sequence("LOG_OTPREQ_SEQ","OTP"); 

                //$otpReqId = $main_app->sql_fetchcolumn("SELECT CONCAT('OTP', DATE_FORMAT(SYSDATE(),'%Y%m%d'), nextVal(LOG_OTPREQ_SEQ)) FROM dual; "); // Seq. No.

                if($otpReqId == false || $otpReqId == "" || $otpReqId == "1") {
                    echo "<script> swal.fire('','Unable to generate OTP Reference ID'); loader_stop(); </script>";
                } else {

                    $data = array();
                    $data['OTP_REQ_ID'] = $otpReqId;
                    $data['OTP_PGMCODE'] = "A"; 
                    // $data2['LOGIN_REQ_NUM'] = $LoginRefNum;
                    $data['OTP_MOBILE_NUM'] = $mobno;
                    // $data['OTP_EMAIL_ID'] = "";
                    $data['SMS_VERIFIED_FLAG'] = "P";
                    $data['GEN_PLATFORM'] = $browser->getPlatform();
                    $data['GEN_BROWSER_NAME'] = $browser_name;
                    $data['GEN_BROWSER_VER'] = $browser_ver;
                    $data['GEN_IP_ADDRESS'] = $main_app->current_ip();
                    // $data['CR_BY'] = "";
                    $data['CR_ON'] = date("Y-m-d H:i:s");
                    $db_updated = $main_app->sql_insert_data("LOG_OTPREQ", $data);

                    if($db_updated == false) {
                        //echo "Unable to generate OTP";
                        echo "<script> swal.fire('','Unable to generate OTP'); loader_stop(); </script>";
                        exit();
                    } else {

                        //API - SEND SMS OTP VIA MB SERVER CALL
                        $send_data['METHOD_NAME'] = "notifyUser";
                        $send_data['MOBILE_NUMBER'] =  $mobno;
                        $send_data['EMAIL_ID'] = "";
                        $send_data['OTP_SMS_CODE'] = $new_otp;
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
                        $data2['SMS_SENT_RESP'] = (isset($output['responseCode']) && $output['responseCode'] == "S") ? $output['responseCode'] : NULL;
                        $main_app->sql_update_data("LOG_OTPREQ",$data2, array('OTP_REQ_ID' => $otpReqId));


                        /** OTP Session */
                        $main_app->session_set([
                            'USER_APP' => APP_CODE,
                            'MOBOTP_REQ_ID' => $otpReqId, // OTP Request ID
                            'LOGIN_TYPE' => "otp",
                            'CUST_MOBILE' => $mobno,
                            'OTP_SMS_CODE' => $new_otp,
                            'OTP_TIMEOUT' => time(),    
                        ]);

                        //$base_ekyc_num = base64_encode($plain_ekyc_num);
                        // $requestid= $output['requestId'];

                        // Success
                        echo "<script>";
                            
                            // $asnNum = $safe->str_encrypt($item_data['ASSREQ_REF_NUM'], $_SESSION['SAFE_KEY']);
                            // echo " $('#asnVal2').val(deStr('".$main_app->strsafe_modal($asnNum)."')); ";

                            // $reqId = $safe->str_encrypt($requestid, $_SESSION['SAFE_KEY']);
                            // echo " $('#req_key2').val(deStr('".$main_app->strsafe_modal($reqId)."')); ";
                          
                            $enc_mobNum = $safe->str_encrypt($mobno, $_SESSION['SAFE_KEY']);
                            echo " $('#mobNum2').val(deStr('".$main_app->strsafe_modal($enc_mobNum)."')); ";

                            echo " $('#tab-nav-2').trigger('click'); loader_stop(); enable('sbt');  mobstartCounter(); ";
                          
                        echo "</script>";
                    
                    }
                }

            }
           
        }  else {
            echo "Unable to proceed";
        }
    }

}

?>