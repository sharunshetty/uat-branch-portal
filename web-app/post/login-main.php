<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/


/** Application Core */
require_once(dirname(__FILE__) . '/../../app-core/app_auto_load.php');


//checking any future transfer branch today/prvious date is there for emp user..if it will update to main branch column in user account table
$totalcnt = $main_app->sql_fetchcolumn("SELECT count(0) FROM ASSREQ_USER_ACCOUNTS WHERE USER_STATUS = 'A' AND TRANSFER_BRNCODE IS NOT NULL");
if($totalcnt) {

    $CUR_DATE = new DateTime(); 
    $sql_exe1 = $main_app->sql_run("SELECT * FROM ASSREQ_USER_ACCOUNTS WHERE USER_STATUS = 'A' AND TRANSFER_BRNCODE IS NOT NULL");
    while ($item_data1 = $sql_exe1->fetch()) {
        
        if($item_data1['TRANSFER_DATE'] != '' &&  $item_data1['TRANSFER_BRNCODE'] != '') {
           
            $TRANSFER_DATE = new DateTime($item_data1['TRANSFER_DATE']); 
            if($CUR_DATE >= $TRANSFER_DATE) {

                $page_primary_keys = array(
                    'USER_ID' => $item_data1['USER_ID'],
                );

                $data1 = array();
                $data1['USER_REGIONS'] = $item_data1['TRANSFER_BRNCODE'];
                $data1['TRANSFER_BRNCODE'] = "";

                $main_app->sql_db_auditlog('M','ASSREQ_USER_ACCOUNTS',$data1,$page_primary_keys); // Audit Log - Modify
                $db_output = $main_app->sql_update_data('ASSREQ_USER_ACCOUNTS',$data1,$page_primary_keys); // Update
                // if($db_output == false) { 
                //     exit('Unable to Login'); // Stop
                // }
                
            }
        }
    }
    
}

// $sql_exe1 = $main_app->sql_run("SELECT * FROM ASSREQ_USER_ACCOUNTS and ", array('LOGIN_USR_ID' => $username ));
// $item_data1 = $sql_exe1->fetch();
// if(isset($item_data1['TRANSFER_BRNCODE']) && $item_data1['TRANSFER_BRNCODE'] != ''){  //check if  future date transfer branch is there
        
//     $TRANSFER_DATE = new DateTime($item_data1['TRANSFER_DATE']); 
//     $CUR_DATE = new DateTime(); 

//     if(isset($item_data1['TRANSFER_DATE']) && $item_data1['TRANSFER_DATE'] != '' &&  $CUR_DATE >= $TRANSFER_DATE) {
        
//         $page_primary_keys = array(
//             'USER_ID' => $username,
//         );
//         $data1 = array();
//         $data1['USER_REGIONS'] = $item_data1['TRANSFER_BRNCODE'];
//         $data1['TRANSFER_BRNCODE'] = "";

//         $main_app->sql_db_auditlog('M','ASSREQ_USER_ACCOUNTS',$data1,$page_primary_keys); // Audit Log - Modify
//         $db_output = $main_app->sql_update_data('ASSREQ_USER_ACCOUNTS',$data1,$page_primary_keys); // Update
//         if($db_output == false) { 
//             exit('Unable to Login'); // Stop
//         }
//     }
// }

/** Login */
if(isset($_POST['emp_usr_id']) && isset($_POST['emp_usr_pass']) && isset($_POST['login_token'])) {

	// User inputs
	$username = $main_app->strsafe_input(trim(strtoupper($_POST['emp_usr_id'])));
    $username = preg_replace("/[^a-zA-Z0-9]+/","",$username);
	$user_password = $main_app->strsafe_input($_POST['emp_usr_pass']);
    $user_token = $main_app->strsafe_input($_POST['login_token']);

	/** Password Decryption */
	$safe = new Encryption();
    $user_password = $safe->rsa_decrypt($user_password);

    //Check Login Attempts
    $ChkFailedLogins = true;
    $LoginIp = $main_app->current_ip();
    $LoginUser = $username;
    $LoginTime = time();
    $LoginDiff = (time() - 120); // Here 600 mean 10 minutes 10*60

    if($ChkFailedLogins == true) {
        $sql_exe52 = $main_app->sql_run("SELECT COUNT(0) FROM APP_LOGIN_ATTEMPTS WHERE TRY_IP_ADD = :TRY_IP_ADD AND TRY_USER_ID = :TRY_USER_ID AND TRY_TIME > :LOGIN_DIFF_TIME", array( 'TRY_IP_ADD' => $LoginIp, 'TRY_USER_ID' => $LoginUser,'LOGIN_DIFF_TIME' => $LoginDiff ));
        $LoginAttempts = $sql_exe52->fetchColumn();
        if($LoginAttempts && is_numeric($LoginAttempts) && $LoginAttempts >= "5") {
            exit('You are allowed 5 attempts in 10 minutes'); // Stop
        }
    }

//     //Check login count - NEWLY ADDED
//     $login_chk_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes")); // 10min ago time
//     $add_data = array('LOGIN_USER' => $username);
//     $countLogins = $main_app->sql_fetchcolumn("SELECT count(0) FROM APP_LOGIN_LOGS WHERE USR_ACTIVE_FLAG = '1' AND LOGIN_TIME > '{$login_chk_date}' AND LOGIN_USER = :LOGIN_USER ",$add_data);
//    // $userReqID = $main_app->sql_fetchcolumn("SELECT LOGIN_REQ_ID FROM APP_LOGIN_LOGS WHERE USR_ACTIVE_FLAG = '1' AND LOGIN_TIME > '{$login_chk_date}' AND LOGIN_USER = :LOGIN_USER ",$add_data);

//     if($countLogins > 0) {
       
//        // echo "exist|".$username;
//         echo "exist";
//         exit();
//         // $sys_datetime = date("Y-m-d H:i:s");
//         // $data2 = array();
//         // $data2['LOGOUT_TIME'] = $sys_datetime;
//         // $data2['USR_ACTIVE_FLAG'] = "2";
//         // $main_app->sql_update_data("APP_LOGIN_LOGS",$data2, array('LOGIN_REQ_ID' => $userReqID, 'LOGIN_USER' => $username));
//         // //header("Location: " . APP_URL . "/login");
//         // exit();
//     }

    /** Login */
     if($username == false || $user_password == false || !isset($_SESSION['LOGIN_TOKEN']) || $user_token != $_SESSION['LOGIN_TOKEN'] ) {
        
        echo "Invalid security token, Please refresh page";

    } else { 
        //Get User data

        $sql_exe = $main_app->sql_run("SELECT * FROM ASSREQ_USER_ACCOUNTS WHERE USER_ID = :LOGIN_USR_ID", array('LOGIN_USR_ID' => $username ));
        $user = $sql_exe->fetch();

        if(isset($user['USER_ID']) && $username == $user['USER_ID'] && $user['USER_PASS'] && password_verify($user_password, $user['USER_PASS'])) {

            if(isset($user['USER_ACNT_STATUS']) && $user['USER_ACNT_STATUS'] == "P" && $user['USER_STATUS'] == "P") {
                exit('User account creation is pending for Authorisation'); // Stop
            }
    
            // if(isset($user['USER_ACNT_STATUS']) && $user['USER_ACNT_STATUS'] == "R" && $user['USER_STATUS'] == "F") {
            //     exit('User details Authorisation is failed'); // Stop
            // }
       
            if(isset($user['USER_STATUS']) && $user['USER_STATUS'] == "B") {
                exit('User account has been blocked'); // Stop
            }

            if(isset($user['USER_STATUS']) && $user['USER_STATUS'] == "T") {
                exit('User Branch Transfer pending for Authorisation'); // Stop
            }

            // if(isset($user['USER_STATUS'])  && $user['USER_STATUS'] == "R") {
            //     exit('User has been Resigned'); // Stop
            // }

            if(isset($user['RESIGN_DATE']) && $user['RESIGN_DATE'] != ''){
                
                $RESIGN_DATE = new DateTime($user['RESIGN_DATE']); 
                $CUR_DATE = new DateTime(); 

                if(isset($user['RESIGN_DATE']) && $user['RESIGN_DATE'] != '' &&  $CUR_DATE >= $RESIGN_DATE) {
                    exit('User has been Resigned'); // Stop
                }
            }

            if(!isset($user['USER_STATUS']) || $user['USER_STATUS'] != "A") {
                exit('User is not active'); // Stop
            }

               //Check login count - NEWLY ADDED
            $login_chk_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes")); // 10min ago time
            $add_data = array('LOGIN_USER' => $username);
            $countLogins = $main_app->sql_fetchcolumn("SELECT count(0) FROM APP_LOGIN_LOGS WHERE USR_ACTIVE_FLAG = '1' AND LOGIN_TIME > '{$login_chk_date}' AND LOGIN_USER = :LOGIN_USER ",$add_data);
            
            if($countLogins > 0) {
            
            // echo "exist|".$username;
                echo "exist";
                exit();
            }


            // if(isset($user['USER_ACNT_STATUS']) && $user['USER_ACNT_STATUS'] == "S" && $user['USER_STATUS'] == "B") {
            //     exit('User is Blocked'); // Stop
            // }

            // if(isset($user['USER_ACNT_STATUS']) && $user['USER_ACNT_STATUS'] == "S" && $user['USER_STATUS'] == "R") {
            //     exit('User has Resigned'); // Stop
            // }

           
            //Get User Block/Unblock Status
            // $sql_exe2 = $main_app->sql_run("SELECT USER_STATUS FROM USER_ACCOUNTS_BLOCK WHERE USER_ID = :LOGIN_USR_ID", array('LOGIN_USR_ID' => $username ));
            // $user_block = $sql_exe2->fetch();

            // if(isset($user_block['USER_STATUS']) && $user_block['USER_STATUS'] != "U") {
            //     exit('User is blocked. Kindly contact User Administrator.'); // Stop
            // }

            // OTP Verification
            if(isset($user['USER_MOBILE']) && $user['USER_MOBILE'] != NULL && $user['USER_MOBILE'] != "") {

                //$SmsTemplate = $main_app->sql_fetchcolumn("SELECT SMSTPL_TEXT FROM VKYC_SMS_TEMPLATES WHERE SMSTPL_STATUS_CODE = 'OTP' AND SMSTPL_ENABLE = 'Y'");
                //if($SmsTemplate && $SmsTemplate != NULL && $SmsTemplate != "") {
                    
                    if(APP_PRODUCTION == false) { $otpCode = "123123"; } 
                    else {
                        $otpCode = $main_app->get_otpcode(); //get otp
                    }
                    
                    $ShortTags = array( '{{OTP}}' => isset($otpCode) ? $otpCode : "" );
                    /* $SmsTemplate = strtr($SmsTemplate, $ShortTags);
                    $sms_sentlog = send_sms($user['USER_MOBILE'],$SmsTemplate); */
                //}

            } else {
                exit('Mobile number is not available for OTP generation'); // Stop
            }

            // Login Log
            $sys_datetime = date("Y-m-d H:i:s");
            $loginReqId = $main_app->sql_fetchcolumn("SELECT NVL(MAX(LOGIN_REQ_ID), 0) + 1 FROM APP_LOGIN_LOGS"); // Seq. No.

            if($loginReqId) {
                $data['LOGIN_REQ_ID'] = $loginReqId;
                $data['LOGIN_USER'] = $username;
                $data['LOGIN_ROLE'] = $user['USER_ROLE_CODE'];
                $data['USR_ACTIVE_FLAG'] = "1";
                $data['LOGIN_OTP_STATUS'] = "2";
                $data['OTP_CODE'] = (isset($otpCode) && $otpCode != NULL) ? $otpCode : "";
                $data['SMS_SENT_LOG'] = (isset($sms_sentlog) && $sms_sentlog) ? substr($sms_sentlog,0,2000) : NULL;
                $data['LOGIN_TIME'] = $sys_datetime;
                $data['LOGOUT_TIME'] = NULL;
                $data['LOGIN_IP'] = $main_app->current_ip();
                $data['LOGIN_BROWSER'] = $browser_name;
                $data['LOGIN_BROWSER_VER'] = $browser_ver;
                $data['CR_BY'] = $username;
                $data['CR_ON'] = $sys_datetime;
                $loginLog = $main_app->sql_insert_data("APP_LOGIN_LOGS", $data); // User log
            }


            if($loginReqId && isset($loginLog) && $loginLog) {

                // User Logged-In
                session_start();
                unset($_SESSION['LOGIN_TOKEN']); // Unset CSRF Token
                session_regenerate_id(TRUE); // Regenerate user session

                echo "ok"; // Success

                $_SESSION['USER_APP'] = APP_CODE;
                $_SESSION['USER_ID'] = $user['USER_ID'];
                $_SESSION['USER_ROLE'] = $user['USER_ROLE_CODE'];
                $_SESSION['BRANCH_CODE'] = $user['USER_REGIONS'];
                // $_SESSION['USER_BRNACCESS'] = $user['USER_BRN_ACCESS'];
                $_SESSION['USER_IP_ADD'] = $main_app->current_ip();
                $_SESSION['USER_BROWSER'] = $browser_name;
                $_SESSION['USER_BROWSER_VER'] = $browser_ver;
                $_SESSION['USER_TIMEOUT'] = time();
                $_SESSION['USER_LOGIN_ID'] = $loginReqId;

                $_SESSION['USER_OTP_CHECK_REQ'] = "Y"; // OTP Verification Required
                $_SESSION['OTP_EMAIL_CHK'] = "Y"; // OTP Verification Required

                session_write_close();

            } else {
                echo "Unable to Login";
            }

        } else {

            echo "Invalid User ID / Password";

            // Login Fail Entry
            if($ChkFailedLogins == true) {
            $try_id = $main_app->sql_fetchcolumn("SELECT NVL(MAX(TRY_ID), 0) + 1 FROM APP_LOGIN_ATTEMPTS"); // Seq. No.
                if($try_id) {
                    $fail_data = array();
                    $fail_data['TRY_ID'] = $try_id;
                    $fail_data['TRY_IP_ADD'] = $LoginIp;
                    $fail_data['TRY_USER_ID'] = $LoginUser;
                    $fail_data['TRY_TIME'] = $LoginTime;
                    $fail_data['CR_ON'] = date("Y-m-d H:i:s");
                    $main_app->sql_insert_data("APP_LOGIN_ATTEMPTS", $fail_data); // log fail
                }
            } 

        }

    }

} else {
	echo "Invalid request";
}

?>