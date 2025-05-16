<?php

/**
 * @copyright   : (c) 2022 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

if(isset($_POST['asnVal']) && $_POST['asnVal'] != "") {
    $plain_ass_refnum = $safe->str_decrypt($_POST['asnVal'], $_SESSION['SAFE_KEY']);
}

if(isset($_POST['panNum']) && $_POST['panNum'] != "") {
    $safe = new Encryption();
    $plain_panNum = $safe->rsa_decrypt($_POST['panNum']);
}


function updatePanDetails($panNum, $app_num, $outputresp, $pan_full_name) {

    global $main_app, $safe;

    $updated_flag = true;
    
    $data2 = array();
    $data2['ASSREQ_PAN_FLAG'] = "Y";
    $data2['ASSREQ_PAN_CARD'] = $safe->str_encrypt($panNum, $app_num);
    $data2['ASSREQ_PAN_NAME'] = $pan_full_name;
    $db_output2 = $main_app->sql_update_data("ASSREQ_MASTER", $data2, array('ASSREQ_REF_NUM' => $app_num )); // Update
    if($db_output2 == false) { $updated_flag = false; }

    if($updated_flag == false) {
        echo "<script> swal.fire('','Unable to update PAN details'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    $doc_sl = $main_app->sql_fetchcolumn("SELECT NVL(MAX(DOC_SL), 0) + 1 FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'PAN' ", array("ASSREQ_REF_NUM" => $app_num)); // Seq. No.
    if($doc_sl == false || $doc_sl == NULL || $doc_sl == "" || $doc_sl == "0") {
        echo "<script> swal.fire('','Unable to generate detail serial'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    // Save PAN Record
    $data = array();
    $data['ASSREQ_REF_NUM'] = $app_num;
    $data['DOC_CODE'] = 'PAN';
    $data['DOC_SL'] = $doc_sl;
    $data['DOC_DATA'] = json_encode($outputresp, true);
    $data['CR_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : NULL;
    $data['CR_ON'] = date("Y-m-d H:i:s");

    $db_output = $main_app->sql_insert_data("ASSREQ_EKYC_DOCS", $data); // Insert
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == true) {

        $data3 = array();
        $data3['ASSVAL_PAN'] = $panNum;
        $db_output = $main_app->sql_update_data("ASSVAL_UIDDETAILS", $data3, array('ASSVAL_REF_NUM' => $app_num)); // Update
       
        //  if($db_output == false) { $updated_flag = false; }

        // $data3 = array();
        // $data3['ASSVAL_REF_NUM'] = $app_num;
        // $data3['ASSVAL_PAN'] = $panNum;
        // $data3['CR_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : NULL;
        // $data3['CR_ON'] = date("Y-m-d H:i:s");
        // $data3['AUTH_STATUS'] = "P";

        // $db_output3 = $main_app->sql_insert_data("ASSVAL_UIDDETAILS", $data3); // Insert
        //if($db_output3 == false) { $updated_flag = false; }
    
    }


    if($updated_flag == false) {
        echo "<script> swal.fire('','Unable to update PAN details'); loader_stop(); enable('sbt'); </script>";
        exit();
    }
}

$name_flag = "N";

//pan no checking - required

// $sql_exe = $main_app->sql_run("select * from cbuat.piddocs p where p.piddocs_pid_type='PAN' and p.piddocs_docid_num=:PAN_NUMBER", array('PAN_NUMBER' => $plain_panNum));
// $itemdata = $sql_exe->fetch();

// //validation
// if(isset($itemdata) && $itemdata != "" || $itemdata != NULL) {
//     echo "<script> swal.fire('','PAN already registered with Bank, Kindly visit nearest Branch'); loader_stop(); enable('sbt'); </script>";
//      exit();     
//    }


//pan no checking through api method - not required
// $ErrorMsg = "";
// $send_data = array();
// $send_data['METHOD_NAME'] = "dbLinkRespData";
// $send_data['REQUEST_FOR'] = "PANVAL";
// $send_data['PAN_NUMBER'] = $plain_panNum;
// // $send_data['AADHAAR_NAME'] = "";
// // $send_data['AADHAAR_NUMBER'] = "";
// // $send_data['FATHER_NAME'] = "";
// // $send_data['DISTRICT'] = "";

// try {
//     $apiConn = new ReachMobApi;
//     // $output = $apiConn->ReachMobConnect($send_data, "60");
//     $output = json_decode('{"errorMessage":"No data found","dataAvailable":"N","responseCode":"F"}', true);
//     //$output = json_decode('{"sourceKey":"1048849","sourceTable":"INDCLIENTS","sponsorName":"","pidType":"PAN","issueAuthority":"GOI","responseCode":"S","expiryDate":"","sponsorAddress1":"","invoiceNumber":"854822","sponsorAddress3":"","documentSl":"2","issueCountry":"","sponsorAddress2":"","dataAvailable":"Y","sponsorAddress5":"","docIdNumber":"CWDPS4299G","sponsorAddress4":"","issueDate":"","addressProof":"0","identityCheck":"1","cardNumber":"","issuePlace":""}', true);

// } catch(Exception $e) {  
//     error_log($e->getMessage());
//     $ErrorMsg = "Technical Error, Please try later"; //Error from Class    
// }


// if(!isset($output['responseCode']) || !isset($output['dataAvailable']) || $output['responseCode'] == "" || $output['dataAvailable'] == "") {// || ($output['responseCode'] != "S" && $output['dataAvailable'] != "Y")
//     $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
// }

// if($output['responseCode'] == "S" && $output['dataAvailable'] == "Y") {
//     $ErrorMsg = "PAN already registered with Bank, Kindly visit nearest Branch";
// }

// // if(!isset($ErrorMsg) || $ErrorMsg == "") {
// //     if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
// //         $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
// //     }
// // }

// if(isset($ErrorMsg) && $ErrorMsg != "") {
//     echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
//     exit();
// }


if(!isset($_POST['panNum']) || isset($_POST['panNum']) == NULL || isset($_POST['panNum']) == "") {
    echo "<script> swal.fire('','Enter valid PAN Card Number'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($plain_panNum) || $plain_panNum == false) {
    echo "<script> swal.fire('','Unable to process your request (E01)'); loader_stop(); enable('sbt'); </script>";
}
elseif($main_app->valid_pancard($plain_panNum) == false) {
    echo "<script> swal.fire('','Invalid PAN format'); loader_stop(); enable('sbt'); focus('panNum'); </script>";
}
elseif(!isset($plain_ass_refnum) || $plain_ass_refnum == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt'); </script>";
}
// elseif(!isset($_POST['fullname']) || isset($_POST['fullname']) == NULL || isset($_POST['fullname']) == "") {
//     echo "<script> swal.fire('','Enter valid Name'); loader_stop(); enable('sbt'); </script>";
// }
// elseif(!isset($_POST['fathername']) || isset($_POST['fathername']) == NULL || isset($_POST['fathername']) == "") {
//     echo "<script> swal.fire('','Enter valid fathers name'); loader_stop(); enable('sbt'); </script>";
// }
// elseif(!isset($_POST['dateofbirth']) || isset($_POST['dateofbirth']) == NULL || isset($_POST['dateofbirth']) == "") {
//     echo "<script> swal.fire('','Please select valid date of birth'); loader_stop(); enable('sbt'); </script>";
// }
elseif(!isset($_POST['PanAgree']) || $_POST['PanAgree'] != "1") {
    echo "<script> swal.fire('','Please click on radio button checkbox to proceed'); loader_stop(); enable('sbt'); </script>";
}
else {

    //checking enterd PAN no existing in table or not
    $totcount = $main_app->sql_fetchcolumn("SELECT count(0) FROM ASSVAL_UIDDETAILS WHERE AUTH_STATUS != 'AR' AND ASSVAL_PAN = :ASSVAL_PAN", array('ASSVAL_PAN' => $plain_panNum ));
    if($totcount > 0 ) {
        echo "<script> swal.fire('','PAN number you have entered, already existing in the system'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    $updated_flag = true;

    $sql1_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_MOBILE_NUM, ASSREQ_EKYC_REF_NUM, ASSREQ_CUST_FNAME FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $plain_ass_refnum));
    $item_data = $sql1_exe->fetch();

    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R01)'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    //Check if pan is linked with aadhaar
    $send_data['METHOD_NAME'] = "linkedPanStatus";
    $send_data['PAN_NUMBER'] = $plain_panNum;
    try {
        $apiConn = new ReachMobApi;
        //$output = $apiConn->ReachMobConnect($send_data, "120");
        // Test Data        
        $output = json_decode('{"MESSAGE":"PAN is linked with Adhaar","responseCode":"S"}', true);
        
    } 	
    catch(Exception $e) {
        $ErrorMsg = $e->getMessage(); //Error from Class  
    }

    if(!isset($ErrorMsg) || $ErrorMsg == "") {
        if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
            $ErrorMsg = isset($output['errorMessage']) ? $output['errorMessage'] : "Unexpected API Error";
        }
    }

    if(isset($ErrorMsg) && $ErrorMsg != "") { 
        echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    //PAN Verify
    $send_data = array();
    $send_data['METHOD_NAME'] = "validatePan";
    $send_data['PAN_NUMBER'] = $plain_panNum;
    $send_data['CHANNEL_CODE'] = API_REACH_MB_CHANNEL;
    $send_data['USER_AGENT'] = $browser->getBrowser();


    
    // $pan_data = array(array('PAN_NUMBER' => $plain_panNum, 'NAME' => $_POST['fullname'], 'FATHER_NAME' => $_POST['fathername'], 'DATE_OF_BIRTH' => $con_dob));

    // //json array of pid_list
    // $pan_data_array = json_encode($pan_data, JSON_UNESCAPED_SLASHES);

    // //PAN Verify
    // $send_data = array();
    // $send_data['METHOD_NAME'] = "validatePan";
    // $send_data['PAN_LIST'] = $pan_data_array;
    // $send_data['CHANNEL_CODE'] = API_REACH_MB_CHANNEL;
    // $send_data['USER_AGENT'] = $browser->getBrowser();

    try {
        $apiConn = new ReachMobApi;
       //$output = $apiConn->ReachMobConnect($send_data, "120");
        // Test Data
        $output = json_decode('{"lastName":"","firstName":"nithin","lastUpdateOn":"31\/05\/2018","midName":"","panTitle":"Smt","responseCode":"S"}', true);
        //$output = json_decode('{"lastName":"JHUNJHUNWALA","firstName":"PRAKASH","lastUpdateOn":"31\/05\/2018","midName":"RAM","panTitle":"Smt","responseCode":"S"}', true);
        
    } catch(Exception $e) {
        $ErrorMsg = $e->getMessage(); //Error from Class
    }

    if(!isset($ErrorMsg) || $ErrorMsg == "") {
        if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
            $ErrorMsg = isset($output['errorMessage']) ? "PAN ".$output['errorMessage'] : "Unexpected API Error (E01)";
        }
    }

    if(isset($ErrorMsg) && $ErrorMsg != "") {
        echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
        exit();
    }
	



    // //aadhar & name check 
	// $sql_exe3 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM']));
    // $kycDetails = $sql_exe3->fetch();

    // if(isset($kycDetails['DOC_DATA']) && $kycDetails['DOC_DATA'] != "") {
	// 	$kycDetails = json_decode(stream_get_contents($kycDetails['DOC_DATA']), true, JSON_UNESCAPED_SLASHES); 
    //   //$kycDetails = json_decode($kycDetails['DOC_DATA'], true);
    // }

   // $pan_firstname = $output['firstName'];

    //combine pan name
    $fullname = "";

    $fullname .= (isset($output['firstName']) && $output['firstName'] != "") ? trim($output['firstName']) : "";
    $fullname .= (isset($output['midName']) && $output['midName'] != "") ? " ". $output['midName'] : "";
    $fullname .= (isset($output['lastName']) && $output['lastName'] != "") ? " ". $output['lastName'] : "";

    // $cust_name = explode(' ', $item_data['ASSREQ_CUST_FNAME']);
    // $custname1 = $cust_name[0];
    // // $custname2 = $cust_name[1];
    // // $custname3 = $cust_name[2];
	
	//  //convert name to uppercase
    // $aadhaar_name = strtoupper($kycDetails['name']);
    $pan_full_name = strtoupper($fullname);
    // $pan_custname1 = strtoupper($pan_firstname);
	
	// if($aadhaar_name != $pan_full_name) {

    //     echo "<script> swal.fire('', 'Aadhaar Name and Pan Name does not match'); loader_stop(); enable('sbt'); </script>";   
    //     exit();

    // } 
    // elseif($aadhaar_name != $item_data['ASSREQ_CUST_FNAME']) {

    //     updatePanDetails($plain_panNum, $item_data['ASSREQ_REF_NUM'], $output, $pan_full_name);

    //     echo "<script> swal.fire('', 'Registered Customer name and Name on UID does not match');loader_stop(); enable('sbt'); </script>";
       
    //     $main_app->session_set([ 'name_flag' =>  "Y"]);

    //     // Success
    //     $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    //     $sid_assref_num = $safe->str_encrypt($plain_ass_refnum, $_SESSION['SAFE_KEY']);
    //     $go_url = "ass-form-panview?ref_Num=".$sid_assref_num; // Page Refresh URL
    //     echo "<script> goto_url('" . $go_url . "');</script>";    
       
    // }
    // elseif($pan_custname1 != $custname1) {

    //     updatePanDetails($plain_panNum, $item_data['ASSREQ_REF_NUM'], $output, $pan_full_name);

    //     echo "<script> swal.fire('', 'Registered Customer name and name on PAN Card does not match'); loader_stop(); enable('sbt'); </script>";
        
    //     $main_app->session_set(['name_flag' =>  "Y"]);

    //     // Success
    //     $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    //     $sid_assref_num = $safe->str_encrypt($plain_ass_refnum, $_SESSION['SAFE_KEY']);
    //     $go_url = "ass-form-panview?ref_Num=".$sid_assref_num; // Page Refresh URL
    //     echo "<script> goto_url('" . $go_url . "');</script>";    
        
    // } 
	// else {

    updatePanDetails($plain_panNum, $item_data['ASSREQ_REF_NUM'], $output, $pan_full_name);

    // Success
    $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    $sid_assref_num = $safe->str_encrypt($plain_ass_refnum, $_SESSION['SAFE_KEY']);
    $go_url = "ass-form-panview?ref_Num=".$sid_assref_num; // Page Refresh URL
    echo "<script> goto_url('" . $go_url . "');</script>";    

   // }

    // $data2 = array();
    // $data2['ASSREQ_PAN_FLAG'] = "Y";
    // $data2['ASSREQ_PAN_CARD'] = $safe->str_encrypt($plain_panNum, $item_data['ASSREQ_REF_NUM']);;
    // $db_output2 = $main_app->sql_update_data("ASSREQ_MASTER", $data2, array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'] )); // Update
    // if($db_output2 == false) { $updated_flag = false; }

    // if($updated_flag == false) {
    //     echo "<script> swal.fire('','Unable to update PAN details'); loader_stop(); enable('sbt'); </script>";
    //     exit();
    // }

    // $doc_sl = $main_app->sql_fetchcolumn("SELECT NVL(MAX(DOC_SL), 0) + 1 FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'PAN' ", array("ASSREQ_REF_NUM" => $item_data['ASSREQ_REF_NUM'])); // Seq. No.
    // if($doc_sl == false || $doc_sl == NULL || $doc_sl == "" || $doc_sl == "0") {
    //     echo "<script> swal.fire('','Unable to generate detail serial'); loader_stop(); enable('sbt'); </script>";
    //     exit();
    // }

    // // Save PAN Record
    // $data = array();
    // $data['ASSREQ_REF_NUM'] = (isset($item_data['ASSREQ_REF_NUM']) && $item_data['ASSREQ_REF_NUM']!= "") ? $item_data['ASSREQ_REF_NUM'] : NULL;
    // $data['DOC_CODE'] = 'PAN';
    // $data['DOC_SL'] = $doc_sl;
    // $data['DOC_DATA'] = json_encode($output, true);
    // $data['CR_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : NULL;
    // $data['CR_ON'] = date("Y-m-d H:i:s");

    // $db_output = $main_app->sql_insert_data("ASSREQ_EKYC_DOCS", $data); // Insert
    // if($db_output == false) { $updated_flag = false; }

    // if($updated_flag == false) {
    //     echo "<script> swal.fire('','Unable to update PAN details'); loader_stop(); enable('sbt'); </script>";
    //     exit();
    // }
	// // Success
	// $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
	// $sid_assref_num = $safe->str_encrypt($plain_ass_refnum, $_SESSION['SAFE_KEY']);
	// $go_url = "ass-pancard-camera?ref_Num=".$sid_assref_num; // Page Refresh URL
	// echo "<script> goto_url('" . $go_url . "');</script>";    

 
}

?>
