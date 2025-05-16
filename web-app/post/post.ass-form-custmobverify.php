<?php

/**
 * @copyright   : (c) 2022 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

if(isset($_POST['mobNum']) && $_POST['mobNum'] != "") {
    $mobNum = $safe->str_decrypt($_POST['mobNum'], $_SESSION['SAFE_KEY']);
}

// if(isset($_POST['reqid']) && $_POST['reqid'] != "") {
//     $requestID = $safe->str_decrypt($_POST['reqid'], $_SESSION['SAFE_KEY']);
// }

if(isset($_POST['asnVal']) && $_POST['asnVal'] != "") {
    $ass_ref_num = $safe->str_decrypt($_POST['asnVal'], $_SESSION['SAFE_KEY']);
}

if(isset($_POST['mobotp']) && $_POST['mobotp'] != "") {
    $safe = new Encryption();
    $mobotp = $safe->rsa_decrypt($_POST['mobotp']);
}

if(!isset($_POST['mobotp']) || isset($_POST['mobotp']) == NULL || isset($_POST['mobotp']) == "") {
    echo "<script> swal.fire('','Enter valid OTP Code'); loader_stop(); enable('sbt2'); </script>";
    exit();
}
elseif(!isset($mobNum) || $mobNum == false) {
    echo "<script> swal.fire('','Unable to process your request (E01)'); loader_stop(); enable('sbt2'); </script>";
    exit();
}
elseif(!isset($ass_ref_num) || $ass_ref_num == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt2'); </script>";
    exit();
}
// elseif(!isset($requestID) || $requestID == false) {
//     echo "<script> swal.fire('','Unable to process your request (E03)'); loader_stop(); enable('sbt2'); </script>";
// }
elseif(!isset($_SESSION['CUST_MOBILE']) || $_SESSION['CUST_MOBILE'] == NULL || $_SESSION['CUST_MOBILE'] == "") {
    echo "<script> sess_error('Session expired. Please try again.'); loader_stop(); enable('sbt2');</script>";
    exit();
}
elseif(!isset($_SESSION['OTP_SMS_CODE']) || $_SESSION['OTP_SMS_CODE'] == NULL || $_SESSION['OTP_SMS_CODE'] == "") {
    echo "<script> sess_error('Session expired. Please try again.'); loader_stop();enable('sbt2'); </script>";
    exit();
}
elseif($mobotp != $_SESSION['OTP_SMS_CODE']) {
    // Update Session
    echo "<script> swal.fire('','Mobile No OTP entered is incorrect'); loader_stop(''); enable('sbt2'); </script>";
    echo "<script> $('#custmobOtp').val('');</script> ";
    //echo "<script> $('#tab-nav-2').trigger('click'); loader_stop(); enable('sbt2');</script>";
    exit();
}

// elseif(!isset($_SESSION['USER_REF_NUM']) || $_SESSION['USER_REF_NUM'] == NULL || $_SESSION['USER_REF_NUM'] == "") {
//     echo "<script> swal.fire('','Unable to validate your request (E04)'); loader_stop(); enable('sbt2'); </script>";
// }
// elseif($plain_arn_val != $_SESSION['USER_REF_NUM']) {
//     echo "<script> swal.fire('','Unable to process your request (E05)'); loader_stop(); enable('sbt2'); </script>";
// }
else {

    $updated_flag = true;

    $sql1_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_MOBILE_NUM FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $ass_ref_num));//$_SESSION['USER_REF_NUM']
    $item_data = $sql1_exe->fetch();

    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R01)'); loader_stop(); enable('sbt2'); </script>";
        exit();
    }

    // Update DB MOB
    $data1 = array();
    $data1['SMS_VERIFIED_FLAG'] = "S";
    $data1['SMS_VERIFIED_ON'] = date("Y-m-d H:i:s");
    $db_output1 = $main_app->sql_update_data("LOG_OTPREQ", $data1, array('OTP_REQ_ID' => $_SESSION['MOBOTP_REQ_ID'])); // Update
    if($db_output1 == false) {
        echo "<script> swal.fire('','Unable to process your request (D01)'); loader_stop(); </script>";
        exit();
    }

    
    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");
    
    $data2 = array();
    $data2['ASSREQ_CUSMOB_FLAG'] = "Y";
    $db_output2 = $main_app->sql_update_data("ASSREQ_MASTER", $data2, array( 'ASSREQ_REF_NUM' => $ass_ref_num)); // Update
    if($db_output2 == false) { $updated_flag = false; }
   
    // if($updated_flag == false) {
    //     echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt2'); </script>";
    //     exit();
    // }

    // Success
   // $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    // $sid_assref_num = $safe->str_encrypt($ass_ref_num, $_SESSION['SAFE_KEY']);
    // $go_url = "ass-form-camera?ref_Num=".$sid_assref_num; // Page Refresh URL
    // echo "<script> goto_url('" . $go_url . "');</script>";

    if($updated_flag == true) {

        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
        $sid_assref_num = $safe->str_encrypt($ass_ref_num, $_SESSION['SAFE_KEY']);
        //echo "<script> $('#CUST_REF_NUM').val('".$sid_assref_num."'); </script>";
        $go_url = "ass-formbranch-camera?ref_Num=".$sid_assref_num; // Page Refresh URL
        echo "<script> goto_url('" . $go_url . "');</script>";

    } else {

        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt2'); </script>";

    }

}

?>