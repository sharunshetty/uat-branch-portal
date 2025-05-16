<?php

/**
 * @copyright   : (c) 2022 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

$page_table_name = "ASSREQ_ACCOUNTDATA";
$primary_key = "ASSREQ_REF_NUM";

if(isset($_POST['asnVal']) && $_POST['asnVal'] != "") {
    $assref_num = $safe->str_decrypt($_POST['asnVal'], $_SESSION['SAFE_KEY']); 
    if($assref_num) {
        //$sql_exe = $main_app->sql_run("SELECT REKYC_REF_NUM, REKYC_CUST_ID FROM $page_table_name WHERE REKYC_REF_NUM = :REKYC_REF_NUM AND REKYC_CUST_ID = :REKYC_CUST_ID AND REKYC_STATUS = 'D'", array('REKYC_REF_NUM' => $ref_num, 'REKYC_CUST_ID' => $_SESSION['USR_ID']));
        $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM ", array('ASSREQ_REF_NUM' => $assref_num));     
        $item_data = $sql_exe->fetch();
    }
}

if(!isset($item_data) || $item_data == "" || $item_data == NULL) {
    echo "<script> swal.fire('','Unable to validate request.'); loader_stop(); enable('sbt');</script>";
    exit();
}

//validation

//get aadhaar data
$sql_exe3 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $assref_num));
$kycDetails = $sql_exe3->fetch();

//decode aadhaar data
if(isset($kycDetails['DOC_DATA']) && $kycDetails['DOC_DATA'] != "") {
$kycDetails = json_decode(stream_get_contents($kycDetails['DOC_DATA']), true, JSON_UNESCAPED_SLASHES); 
}

//make name to uppercase
if(isset($kycDetails['name']) && $kycDetails['name'] != "") {
    $aadhaar_name = strtoupper($kycDetails['name']);
}



//get PAN data
// $sql_exe4 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'PAN' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM']));
// $panDetails = $sql_exe4->fetch();
// if(isset($panDetails['DOC_DATA']) && $panDetails['DOC_DATA'] != "") {
//     $panDetails = json_decode(stream_get_contents($panDetails['DOC_DATA']), true, JSON_UNESCAPED_SLASHES); 
// }   

// //combine pan name
// $fullname = "";
// $fullname .= (isset($panDetails['firstName']) && $panDetails['firstName'] != "") ? trim($panDetails['firstName']) : "";
// $fullname .= (isset($panDetails['midName']) && $panDetails['midName'] != "") ? " ". $panDetails['midName'] : "";
// $fullname .= (isset($panDetails['lastName']) && $panDetails['lastName'] != "") ? " ". $panDetails['lastName'] : "";

// //convert name to uppercase
// $pan_full_name = strtoupper($fullname);

//validation for customer name
// if(!isset($_POST['CUST_FULL_NAME']) || $_POST['CUST_FULL_NAME'] == NULL || $_POST['CUST_FULL_NAME'] == "" || ($_POST['CUST_FULL_NAME'] != $aadhaar_name)) {
//     echo "<script> swal.fire('','Aadhaar Name does not match! please check'); loader_stop(); enable('sbt'); </script>";
//     exit();
// }

// if(!isset($_POST['CUST_FULL_NAME']) || $_POST['CUST_FULL_NAME'] == NULL || $_POST['CUST_FULL_NAME'] == "") {
//     echo "<script> swal.fire('','Name cannot be empty!'); loader_stop(); enable('sbt'); </script>";
//     exit();
// }
// elseif($_POST['CUST_FULL_NAME'] != $aadhaar_name){
//     echo "<script> swal.fire('','Customer name and Aadhar name does not match! please update Customer Name'); loader_stop(); enable('sbt'); </script>";
//     exit();
// }
/*elseif($_POST['CUST_FULL_NAME'] != $pan_full_name){
    echo "<script> swal.fire('','Customer name and PAN name does not match! please update Customer Name'); loader_stop(); enable('sbt'); </script>";
    exit();
}*/

if(!isset($_POST['CUST_FIRST_NAME']) || $_POST['CUST_FIRST_NAME'] == NULL || $_POST['CUST_FIRST_NAME'] == "") {
    echo "<script> swal.fire('','First name cannot be empty!'); loader_stop(); enable('sbt'); </script>";
    exit();
}

//combine customer name
$fullname = "";
$fullname .= (isset($_POST['CUST_FIRST_NAME']) && $_POST['CUST_FIRST_NAME'] != "") ? trim($_POST['CUST_FIRST_NAME']) : "";
$fullname .= (isset($_POST['CUST_MIDDLE_NAME']) && $_POST['CUST_MIDDLE_NAME'] != "") ? " ". $_POST['CUST_MIDDLE_NAME'] : "";
$fullname .= (isset($_POST['CUST_LAST_NAME']) && $_POST['CUST_LAST_NAME'] != "") ? " ". $_POST['CUST_LAST_NAME'] : "";
//convert name to uppercase
$customer_full_name = strtoupper($fullname);


if($customer_full_name != $aadhaar_name){
    echo "<script> swal.fire('','Customer name and Aadhar name does not match! please update Customer Name'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['PLACE_OF_BIRTH']) || $_POST['PLACE_OF_BIRTH'] == NULL || $_POST['PLACE_OF_BIRTH'] == "") {
    echo "<script> swal.fire('','Please select place of birth'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['OCCUPATION']) || $_POST['OCCUPATION'] == NULL || $_POST['OCCUPATION'] == "") {
    echo "<script> swal.fire('','Please select occupation'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['ANNUAL_INCOME']) || $_POST['ANNUAL_INCOME'] == NULL || $_POST['ANNUAL_INCOME'] == "") {
    echo "<script> swal.fire('','Please select Annual Income'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['FATHERS_NAME']) || $_POST['FATHERS_NAME'] == NULL || $_POST['FATHERS_NAME'] == "") {
    echo "<script> swal.fire('','Please enter Fathers Name'); loader_stop(); enable('sbt'); </script>";
}
elseif(!isset($_POST['MOTHERS_NAME']) || $_POST['MOTHERS_NAME'] == NULL || $_POST['MOTHERS_NAME'] == "") {
    echo "<script> swal.fire('','Please enter Mothers Name'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['DOB']) || $_POST['DOB'] == NULL || $_POST['DOB'] == "") {
    echo "<script> swal.fire('','Invalid date of birth'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['MARITAL_STATUS']) || $_POST['MARITAL_STATUS'] == NULL || $_POST['MARITAL_STATUS'] == "") {
    echo "<script> swal.fire('','Please select Marital Status'); loader_stop(); enable('sbt'); </script>";
}
elseif((isset($_POST['MARITAL_STATUS']) && $_POST['MARITAL_STATUS'] == "2") && (!isset($_POST['SPOUSE_NAME']) || $_POST['SPOUSE_NAME'] == NULL || $_POST['SPOUSE_NAME'] == "")) {
    echo "<script> swal.fire('','Please enter Spouse Name'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['RELIGION']) || $_POST['RELIGION'] == NULL || $_POST['RELIGION'] == "") {
    echo "<script> swal.fire('','Please enter Religion'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['QUALIFICATION']) || $_POST['QUALIFICATION'] == NULL || $_POST['QUALIFICATION'] == "") {
    echo "<script> swal.fire('','Please enter Qualification'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['DBT_BENEFICIARY']) || $_POST['DBT_BENEFICIARY'] == NULL || $_POST['DBT_BENEFICIARY'] == "") {
    echo "<script> swal.fire('','Please select DBT Beneficiary'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['HOUSENUMBER']) || $_POST['HOUSENUMBER'] == NULL || $_POST['HOUSENUMBER'] == "") {
    echo "<script> swal.fire('','Please enter House Number/ Address I'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['STREETADDRESS']) || $_POST['STREETADDRESS'] == NULL || $_POST['STREETADDRESS'] == "") {
    echo "<script> swal.fire('','Please enter Street/ Address II'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['DISTRICT']) || $_POST['DISTRICT'] == NULL || $_POST['DISTRICT'] == "") {
    echo "<script> swal.fire('','Please enter district'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['STATE']) || $_POST['STATE'] == NULL || $_POST['STATE'] == "") {
    echo "<script> swal.fire('','Please enter state'); loader_stop(); enable('sbt'); </script>";
    exit();
}
 elseif(!isset($_POST['PINCODE']) || $_POST['PINCODE'] == NULL || $_POST['PINCODE'] == "") {
     echo "<script> swal.fire('','Please enter pincode'); loader_stop(); enable('sbt'); </script>";
     exit();
 }
 elseif(!isset($_POST['INITIAL_DEPOSIT']) || $_POST['INITIAL_DEPOSIT'] == NULL || $_POST['INITIAL_DEPOSIT'] == "") {
    echo "<script> swal.fire('','Please select inital deposit'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif(!isset($_POST['AMOUNT_DEPOSIT']) || $_POST['AMOUNT_DEPOSIT'] == NULL || $_POST['AMOUNT_DEPOSIT'] == "") {
    echo "<script> swal.fire('','Please enter amount deposit'); loader_stop(); enable('sbt'); </script>";
    exit();
}
elseif($_POST['AMOUNT_DEPOSIT'] < '1') {
    echo "<script> swal.fire('','Please enter amount more than zero'); loader_stop(); enable('sbt'); </script>";
    exit();
}

// elseif(!isset($_POST['WEAKER_SEC_CODE']) || $_POST['WEAKER_SEC_CODE'] == NULL || $_POST['WEAKER_SEC_CODE'] == "") {
//     echo "<script> swal.fire('','Please select Weaker section code'); loader_stop(); enable('sbt'); </script>";
//     exit();
// }
elseif(!isset($assref_num) || $assref_num == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt'); </script>";
    exit();
}
else {
      
    $updated_flag = true;
    $sql1_exe = $main_app->sql_run("SELECT * FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $assref_num ));
    $item_data1 = $sql1_exe->fetch();

    if(!isset($item_data1['ASSREQ_REF_NUM']) || $item_data1['ASSREQ_REF_NUM'] == NULL || $item_data1['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R01)'); loader_stop(); enable('sbt2'); </script>";
        exit();
    }

    $data2 = array();
    $data2['ASSREQ_BASIC_DETAIL_FLG'] = 'Y';
    $data2['ASSREQ_CUST_FNAME'] = $customer_full_name;  //aadhar name updated in basic details table
    $data2['ASSREQ_CUST_SFNAME'] = $item_data1['ASSREQ_CUST_FNAME'];//name u entered  in basic page saved if aadhar nane not match in basic details table
   
    //$data2 ['ASSREQ_CUST_FNAME'] = isset($_POST['CUST_FULL_NAME']) ? $_POST['CUST_FULL_NAME'] :  $item_data1['ASSREQ_CUST_FNAME'];

    $db_output2 = $main_app->sql_update_data("ASSREQ_MASTER", $data2, array('ASSREQ_REF_NUM' => $assref_num)); // Update
    if($db_output2 == false) { $updated_flag = false; }
    if($updated_flag == false) {
        echo "<script> swal.fire('','Unable to process your request'); loader_stop(); enable('sbt'); </script>";
        exit();
    }

    $data = array(); 
    $data['ASSREQ_CUST_FIRST_NAME'] = (isset($_POST['CUST_FIRST_NAME']) && $_POST['CUST_FIRST_NAME'] != "") ? trim($_POST['CUST_FIRST_NAME']) : NULL;
    $data['ASSREQ_CUST_MIDDLE_NAME'] = (isset($_POST['CUST_MIDDLE_NAME']) && $_POST['CUST_MIDDLE_NAME'] != "") ? $_POST['CUST_MIDDLE_NAME'] : NULL;
    $data['ASSREQ_CUST_LAST_NAME'] = (isset($_POST['CUST_LAST_NAME']) && $_POST['CUST_LAST_NAME'] != "") ? $_POST['CUST_LAST_NAME'] : NULL;
    $data['ASSREQ_PLACE_OF_BIRTH'] = $_POST['PLACE_OF_BIRTH'];
    // $data['ASSREQ_LANGUAGE_CODE'] = $_POST['LANGUAGE'];
    $data['ASSREQ_OCCUPATION_CODE'] = $_POST['OCCUPATION'];
    // $data['ASSREQ_COMPANY_CODE'] = $_POST['COMPANY_CODE'];
    $data['ASSREQ_ANNUAL_INCOME'] = $_POST['ANNUAL_INCOME'];
    // $data['ASSREQ_TYPEOF_ACCOMODATION'] = $_POST['TYPE_OF_ACCOMODATION'];
    // $data['ASSREQ_INUSURANCE_INFO'] = $_POST['INSULRANCE_POLICY']; 
    // $data['ASSREQ_FATHERSNAME'] = isset($_POST['FATHERS_NAME']) ? $_POST['FATHERS_NAME'] : NULL; 
    $data['ASSREQ_FATHERSNAME'] = $_POST['FATHERS_NAME']; 
    $data['ASSREQ_MOTHERSNAME'] = $_POST['MOTHERS_NAME']; 
    //$data['ASSREQ_DOB'] =$_POST['DOB']; // $_POST['DOB']; 
    $inputDate= new dateTime($_POST['DOB']);
    $data['ASSREQ_DOB']= $inputDate->format('Y-m-d');
    $data['ASSREQ_MARITAL_STATUS'] = $_POST['MARITAL_STATUS'];
    $data['ASSREQ_RELATIVENAME'] = isset($_POST['RELATIVE_NAME']) ? $_POST['RELATIVE_NAME'] : NULL;
    $data['ASSREQ_SPOUSE_NAME'] = isset($_POST['SPOUSE_NAME']) ? $_POST['SPOUSE_NAME'] : NULL;  
    //$data['ASSREQ_DESIGNATION_CODE'] = $_POST['DESIGNATION_CODE'];
    $data['ASSREQ_RELIGION_CODE'] = $_POST['RELIGION'];
    $data['ASSREQ_QUALIFICATION'] = $_POST['QUALIFICATION']; 
    $data['ASSREQ_DBTCHECK'] = $_POST['DBT_BENEFICIARY']; 
    $data['ASSREQ_ADDRESS'] = $_POST['CUST_ADDRESS']; 
    // $data['HOUSENUMBER'] = $_POST['HOUSENUMBER']; 
    // $data['STREETADDRESS'] = $_POST['STREETADDRESS']; 
    // $data['DISTRICT'] = $_POST['DISTRICT']; 
    // $data['STATE'] = $_POST['STATE']; 
    // $data['PINCODE'] = $_POST['PINCODE']; 
    $data['ASSREQ_WEAKER_CODE'] = $_POST['WEAKER_SEC_CODE']; 
    $data['ASSREQ_SOURCE_EMPID'] = $_POST['SOURCE_EMPID']; 
    $data['ASSREQ_INITIAL_DEPOSIT'] = $_POST['INITIAL_DEPOSIT']; 
    $data['ASSREQ_AMT_DEPOSIT'] = isset($_POST['AMOUNT_DEPOSIT']) ? $main_app->amount_convert($_POST['AMOUNT_DEPOSIT']) : "";
    
    $address_data = array("HOUSENUMBER" => $_POST['HOUSENUMBER'], "STREETADDRESS" => $_POST['STREETADDRESS'],"DISTRICT" => $_POST['DISTRICT'], "STATE" => $_POST['STATE'], "PINCODE" => $_POST['PINCODE']);
    $data['ASSREQ_EDIT_ADDRESS'] = json_encode($address_data, true);
    
    $data['MO_BY'] = $_SESSION['USER_ID'];
    $data['MO_ON'] = date("Y-m-d H:i:s");

    $main_app->sql_db_auditlog('M',$page_table_name,$data); // Audit Log - DB Transaction
    $db_output = $main_app->sql_update_data("ASSREQ_ACCOUNTDATA",$data, array('ASSREQ_REF_NUM' => $assref_num)); // update account data table
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == false) {
       echo "<script> swal.fire('','Unable to process your request.'); loader_stop(); enable('sbt'); </script>";
       exit();
    }

    // $flagdata = array();
    // $flagdata['ASSREQ_BASIC_DETAIL_FLG'] = 'Y';
   
    // $db_output2 = $main_app->sql_update_data('ASSREQ_MASTER', $flagdata, array( 'ASSREQ_REF_NUM' => $assref_num));
    // if($db_output2 == false) { $updated_flag = false; }

    // if($updated_flag == false) {
    //     echo "<script> swal.fire('','Unable to process your request'); loader_stop(); enable('sbt'); </script>";
    //     exit();
    // }


    $main_app->sql_db_commit(); // Success - DB Transaction
    $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    $sid_assref_num = $safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']);
    
    $go_url = "form-nominee-details?ref_Num=".$sid_assref_num; // Page Refresh URL
    
    echo "<script> goto_url('" . $go_url . "');</script>";
    
   
}
