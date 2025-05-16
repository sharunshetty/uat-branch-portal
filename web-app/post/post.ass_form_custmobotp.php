<?php

/**
 * @copyright   : (c) 2022 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

//Decrypt Application reference number
if(isset($_POST['asnVal']) && $_POST['asnVal'] != "") {
    $plain_ass_refnum = $safe->str_decrypt($_POST['asnVal'], $_SESSION['SAFE_KEY']);
}

//decrypt aadhaar number
if(isset($_POST['mobnum']) && $_POST['mobnum'] != "") {
    $safe = new Encryption();
    $plain_mob_num = $safe->rsa_decrypt($_POST['mobnum']);
}

if(!isset($_POST['mobnum']) || isset($_POST['mobnum']) == NULL || isset($_POST['mobnum']) == "") {
    echo "<script> swal.fire('','Mobile No is Incorrect'); loader_stop(); enable('sbt2'); </script>";
}
elseif(!isset($plain_mob_num) || $plain_mob_num == false) {
    echo "<script> swal.fire('','Unable to process your request (E01)'); loader_stop(); enable('sbt2'); </script>";
}
elseif(!isset($plain_ass_refnum) || $plain_ass_refnum == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt2'); </script>";
}   
// elseif(!isset($_SESSION['USER_REF_NUM']) || $_SESSION['USER_REF_NUM'] == NULL || $_SESSION['USER_REF_NUM'] == "") {
//     echo "<script> swal.fire('','Unable to validate your request (E03)'); loader_stop(); enable('sbt'); </script>";
// }
// elseif($plain_arn_val != $_SESSION['USER_REF_NUM']) {
//     echo "<script> swal.fire('','Unable to process your request (E04)'); loader_stop(); enable('sbt'); </script>";
// }   
else {

    $updated_flag = true;

    $sql1_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_MOBILE_NUM FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $plain_ass_refnum ));//$_SESSION['USER_REF_NUM']
    $item_data = $sql1_exe->fetch();

    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R02)'); loader_stop(); enable('sbt2'); </script>";
        exit();
    }

    //Generate OTP
    if(APP_PRODUCTION == false) {
        $new_otp = "123123";
    } else {
        $new_otp = $main_app->get_otpcode();  
    }    

    $send_data = array();
    $send_data['METHOD_NAME'] = "getAadhaarOtp";
    $send_data['AADHAAR_NUMBER'] = $plain_ekyc_num;
    $send_data['CHANNEL_CODE'] = API_REACH_MB_CHANNEL;
    $send_data['USER_AGENT'] = $browser->getBrowser();

    try {
       // $apiConn = new ReachMobApi;
        //$output = $apiConn->ReachMobConnect($send_data, "40");
        $output = json_decode('{"errorMessage":"","responseCode":"S","requestId":"73cdbde2-80f7-11e7-8f0c-e7e769f70bd1","successMessage":"OTP sent to registered mobile number"}', true);

    } catch(Exception $e) {  
        error_log($e->getMessage());
        $ErrorMsg = "Technical Error, Please try later"; //Error from Class    
    }

    if(!isset($ErrorMsg) || $ErrorMsg == "") {
        if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
            $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
        }
    }

    if(isset($ErrorMsg) && $ErrorMsg != "") {
        echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    //$base_ekyc_num = base64_encode($plain_ekyc_num);
    $requestid= $output['requestId'];

    // Success
    echo "<script>";
    
    $asnNum = $safe->str_encrypt($item_data['ASSREQ_REF_NUM'], $_SESSION['SAFE_KEY']);
    echo " $('#asnVal2').val(deStr('".$main_app->strsafe_modal($asnNum)."')); ";

    $reqId = $safe->str_encrypt($requestid, $_SESSION['SAFE_KEY']);
    echo " $('#req_key2').val(deStr('".$main_app->strsafe_modal($reqId)."')); ";

    $enc_ekycnum = $safe->str_encrypt($plain_ekyc_num, $_SESSION['SAFE_KEY']);
    echo " $('#ekycNum2').val(deStr('".$main_app->strsafe_modal($enc_ekycnum)."')); ";

    echo " $('#tab-nav-2').trigger('click'); loader_stop(); enable('sbt'); ";
    echo "</script>";

}
?>