<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../../../app-core/app_auto_load.php');

/** Forgot Password */
if(isset($_POST['USER_ID']) && isset($_POST['MOBILE_NUM']) && isset($_POST['token'])) {

    // User inputs
    $user_token = $main_app->strsafe_input($_POST['token']);

    /** Password Decryption */
    $safe = new Encryption();
    
    $username = $safe->rsa_decrypt($_POST['USER_ID']);
    $user_mobile = $safe->rsa_decrypt($_POST['MOBILE_NUM']);

    if($username) {
        $username = $main_app->strsafe_input(trim(strtoupper($username)));
    }
    
    if($user_mobile) {
        $user_mobile = $main_app->strsafe_input(trim($user_mobile));
    }

    if($username == false || $user_mobile == false || !isset($_SESSION['APP_TOKEN']) || $user_token != $_SESSION['APP_TOKEN'] ) {
        
        echo "Invalid security token, Please refresh page";

    } elseif(!isset($_SESSION['SECURITY_CODE'])) {

        echo "Invalid security code, Please refresh page";
        
    } elseif(!isset($_POST['SC_CODE']) || $_POST['SC_CODE'] == NULL || $_POST['SC_CODE'] == "") {
        
        echo "Enter valid security code";

    } elseif($_POST['SC_CODE'] != $_SESSION['SECURITY_CODE']) {
        
        echo "Security code does not match";
    }
    else {

        //Get User data
        $sql_exe = $main_app->sql_run("SELECT USER_ID, USER_ROLE_CODE, USER_MOBILE, USER_ACNT_STATUS,USER_STATUS FROM ASSREQ_USER_ACCOUNTS WHERE USER_ID = :LOGIN_USR_ID AND USER_MOBILE = :USER_MOBILE", array('LOGIN_USR_ID' => $username, 'USER_MOBILE' => $user_mobile )); // AND USER_STATUS = 'A'
        $user = $sql_exe->fetch();

        if(!isset($user['USER_ID']) || $user['USER_ID'] == NULL || $username != $user['USER_ID']) {
            exit('User details unavailable'); // Stop
        }

        if(!isset($user['USER_MOBILE']) || $user['USER_MOBILE'] =="") {
            exit('Mobile number not available'); // Stop
        }

        
        if(isset($user['USER_ACNT_STATUS']) && $user['USER_ACNT_STATUS'] == "P" && $user['USER_STATUS'] == "P") {
            exit('User account creation is pending for Authorisation, cannot change password'); // Stop
        }

        if(isset($user['USER_ACNT_STATUS']) && $user['USER_ACNT_STATUS'] == "R" && $user['USER_STATUS'] == "F") {
            exit('User details Authorisation is failed, cannot change password'); // Stop
        }
        if(isset($user['USER_STATUS']) && $user['USER_STATUS'] == "B") {
            exit('User account has been blocked'); // Stop
        }

        if(isset($user['USER_STATUS'])  && $user['USER_STATUS'] == "R") {
            exit('User has been Resigned'); // Stop
        }

        if(!isset($user['USER_STATUS']) || $user['USER_STATUS'] != "A") {
            exit('User is not active'); // Stop
        }

        // OTP Verification
        $SmsTemplate = $main_app->sql_fetchcolumn("SELECT SMSTPL_TEXT FROM VKYC_SMS_TEMPLATES WHERE SMSTPL_STATUS_CODE = 'FPO' AND SMSTPL_ENABLE = 'Y'");
        if($SmsTemplate && $SmsTemplate != NULL && $SmsTemplate != "") {
            if(APP_PRODUCTION == false) { $otpCode = "123123"; } 
            else { 
                $otpCode = $main_app->get_otpcode(); //get otp 
            }
           
            $ShortTags = array('{{OTP}}' => isset($otpCode) ? $otpCode : "",);
            $SmsTemplate = strtr($SmsTemplate, $ShortTags);
            $sms_sentlog = send_sms($user['USER_MOBILE'],$SmsTemplate);
        } else {
            exit('OTP not enabled'); // Stop
        }

        // Login Log
        $sys_datetime = date("Y-m-d H:i:s");
        $forgotReqId = $main_app->sql_fetchcolumn("SELECT NVL(MAX(FP_DTL_SL), 0) + 1 FROM APP_FORGOTPASS_LOGS WHERE FP_USER_ID = :FP_USER_ID", array('FP_USER_ID' => $user['USER_ID'])); // Seq. No.

        if($forgotReqId && isset($user['USER_ID'])) {
            $data['FP_USER_ID'] = $user['USER_ID'];
            $data['FP_DTL_SL'] = $forgotReqId;
            $data['FP_USER_MOBILE'] = $user_mobile;
            $data['FP_USER_ROLE'] = $user['USER_ROLE_CODE'];
            $data['FP_OTP_STATUS'] = "2";
            $data['FP_OTP_CODE'] = (isset($otpCode) && $otpCode != NULL) ? $otpCode : "";
            $data['FP_SMS_SENT_LOG'] = (isset($sms_sentlog) && $sms_sentlog) ? substr($sms_sentlog,0,2000) : NULL;
            $data['FP_IP'] = $main_app->current_ip();
            $data['FP_PLATFORM'] = $browser->getPlatform();
            $data['FP_BROWSER'] = $browser_name;
            $data['FP_BROWSER_VER'] = $browser_ver;
            $data['CR_ON'] = $sys_datetime;
            $forgotLog = $main_app->sql_insert_data("APP_FORGOTPASS_LOGS", $data); // User log
        }

        if($forgotReqId && isset($forgotLog) && $forgotLog) {

            // Forgot Screen Logged-In
            session_start();
            unset($_SESSION['APP_TOKEN']); // Unset CSRF Token
            session_regenerate_id(TRUE); // Regenerate user session
            echo "ok"; // Success

            $_SESSION['FP_USER_APP'] = APP_CODE;
            $_SESSION['FP_USER_ID'] = $user['USER_ID'];
            $_SESSION['FP_USER_TIMEOUT'] = time();
            $_SESSION['FP_USER_DTL_SL'] = $forgotReqId;
            $_SESSION['FP_OTP_CHECK_REQ'] = "Y"; // OTP Verification Required
            
            session_write_close();

        } else {
            echo "Unable to process forgot password";
        }
        
    }
}

