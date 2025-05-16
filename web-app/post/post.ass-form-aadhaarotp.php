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
if(isset($_POST['ekycNum']) && $_POST['ekycNum'] != "") {
    $safe = new Encryption();
    $plain_ekyc_num = $safe->rsa_decrypt($_POST['ekycNum']);
}

///adhar no checking --required

// $sql_exe = $main_app->sql_run("select * from cbuat.piddocs p where p.piddocs_pid_type='UID' and p.piddocs_docid_num=:AADHAAR_NUMBER", array('AADHAAR_NUMBER' => $plain_ekyc_num));
// $itemdata = $sql_exe->fetch();

// if(isset($itemdata) && $itemdata != "" || $itemdata != NULL) {
//   echo "<script> swal.fire('','Aadhaar already registered with Bank, Kindly visit nearest Branch'); loader_stop(); enable('sbt'); </script>";
//  exit();
//}



//aadhar no checking-- not required
// $ErrorMsg = "";
// $send_data = array();
// $send_data['METHOD_NAME'] = "dbLinkRespData";
// $send_data['REQUEST_FOR'] = "AADHAARVAL";
// $send_data['AADHAAR_NUMBER'] = $plain_ekyc_num;
// // $send_data['PAN_NUMBER'] = "";
// // $send_data['AADHAAR_NAME'] = "";
// // $send_data['FATHER_NAME'] = "";
// // $send_data['DISTRICT'] = "";

// try {
//     $apiConn = new ReachMobApi;
//    // $output = $apiConn->ReachMobConnect($send_data, "60");
//     $output = json_decode('{"errorMessage":"No data found","dataAvailable":"N","responseCode":"F"}', true);
//     // $output = json_decode(' {"sourceKey":"1048849","sourceTable":"INDCLIENTS","sponsorName":"","pidType":"UID","issueAuthority":"GOI","responseCode":"S","expiryDate":"","sponsorAddress1":"","invoiceNumber":"854822","sponsorAddress3":"","documentSl":"1","issueCountry":"","sponsorAddress2":"","dataAvailable":"Y","sponsorAddress5":"","docIdNumber":"846413378003","sponsorAddress4":"","issueDate":"","addressProof":"1","identityCheck":"1","cardNumber":"","issuePlace":""}', true);
    
    
// } catch(Exception $e) {  
//     error_log($e->getMessage());
//     $ErrorMsg = "Technical Error, Please try later"; //Error from Class    
// }

// if(!isset($ErrorMsg) || $ErrorMsg == "") {
       
//     if(!isset($output['responseCode']) || !isset($output['dataAvailable']) || $output['responseCode'] == "" || $output['dataAvailable'] == "") {// || ($output['responseCode'] != "S" && $output['dataAvailable'] != "Y")
//         $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
//     }

//     if($output['responseCode'] == "S" && $output['dataAvailable'] == "Y") {
//         $ErrorMsg = "Aadhaar already registered with Bank, Kindly visit nearest Branch";
//     }
    

//    // if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
//     //    $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
//    // }
// }

// if(isset($ErrorMsg) && $ErrorMsg != "") {
//     echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
//     exit();
// }


if(!isset($_POST['ekycNum']) || isset($_POST['ekycNum']) == NULL || isset($_POST['ekycNum']) == "") {
    echo "<script> swal.fire('','Enter valid Aadhaar number'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($plain_ekyc_num) || $plain_ekyc_num == false) {
    echo "<script> swal.fire('','Unable to process your request (E01)'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($plain_ass_refnum) || $plain_ass_refnum == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt'); </script>";
}    
// elseif(!isset($_SESSION['USER_REF_NUM']) || $_SESSION['USER_REF_NUM'] == NULL || $_SESSION['USER_REF_NUM'] == "") {
//     echo "<script> swal.fire('','Unable to validate your request (E03)'); loader_stop(); enable('sbt'); </script>";
// }
// elseif($plain_arn_val != $_SESSION['USER_REF_NUM']) {
//     echo "<script> swal.fire('','Unable to process your request (E04)'); loader_stop(); enable('sbt'); </script>";
// }   
else {

    //checking enterd aadhar no existing in table or not
    $totcount = $main_app->sql_fetchcolumn("SELECT count(0) FROM ASSVAL_UIDDETAILS WHERE AUTH_STATUS != 'AR' AND ASSVAL_EKYC_UID = :ASSVAL_EKYC_UID", array('ASSVAL_EKYC_UID' => $plain_ekyc_num ));
    if($totcount > 0 ) {
        echo "<script> swal.fire('','Aadhaar number you have entered, already existing in the system'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    $sql1_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_MOBILE_NUM FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $plain_ass_refnum ));//$_SESSION['USER_REF_NUM']
    $item_data = $sql1_exe->fetch();

    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R02)'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    //Aadhaar OTP Gen.
    $send_data = array();
    $send_data['METHOD_NAME'] = "getAadhaarOtp";
    $send_data['AADHAAR_NUMBER'] = $plain_ekyc_num;
    $send_data['CHANNEL_CODE'] = API_REACH_MB_CHANNEL;
    $send_data['USER_AGENT'] = $browser->getBrowser();

    try {
       $apiConn = new ReachMobApi;
       //$output = $apiConn->ReachMobConnect($send_data, "120");
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