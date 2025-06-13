<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

if(isset($_POST['asnVal']) && $_POST['asnVal'] != "") {
    $plain_ass_refnum = $safe->str_decrypt($_POST['asnVal'], $_SESSION['SAFE_KEY']);
}
if(!isset($plain_ass_refnum) || $plain_ass_refnum == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('nextBtn'); </script>";
}


// File Configs
$allowed_ext = ['jpg','jpeg'];
$allowed_mimes = ['image/jpg','image/jpeg'];
$allowed_size = "550000"; //Bytes // - 2097152 = 2MB // 550000 = 550KB // 350000 = 350 KB
$Extension = "";

if(!empty($_FILES['UPLOAD_FILE']) && $_FILES['UPLOAD_FILE']['error'] == 0) {

    $dataName = $_FILES['UPLOAD_FILE']['name'];
    $dataSize = $_FILES['UPLOAD_FILE']['size'];
    $dataTmpName  = $_FILES['UPLOAD_FILE']['tmp_name'];
    $dataType = $_FILES['UPLOAD_FILE']['type'];
    $dataTmp = explode('.', $dataName);
    $Extension = strtolower(end($dataTmp));
    $fileName = $plain_ass_refnum.'-'.$dataName;

}

if(!isset($_FILES['UPLOAD_FILE']) || empty($_FILES['UPLOAD_FILE']) || $_FILES['UPLOAD_FILE']['error'] != 0) {
    echo "<script> swal.fire('','W04: Unable to upload image'); loader_stop(); enable('nextBtn'); </script>";
    exit();
}
elseif(empty($_FILES['UPLOAD_FILE']['name'] || $_FILES['UPLOAD_FILE']['tmp_name'] == NULL)) {
    echo "<script> swal.fire('','W05: file not selected'); loader_stop(); enable('nextBtn'); </script>";
    exit();
}
elseif($_FILES['UPLOAD_FILE']['error'] != 0 || !is_uploaded_file($_FILES['UPLOAD_FILE']['tmp_name'])) {
    echo "<script> swal.fire('','W06: file not uploaded'); loader_stop(); enable('nextBtn'); </script>";
    exit();
}
elseif(!in_array($Extension,$allowed_ext) || !in_array($dataType,$allowed_mimes)) {
    echo "<script> swal.fire('','W07: Unsupported file format'); loader_stop(); enable('nextBtn'); </script>";
    exit();
}
elseif(!empty($_FILES['UPLOAD_FILE']['name']) && $dataSize > $allowed_size) {
    echo "<script> swal.fire('','W08: Maximum file size 500KB'); loader_stop(); enable('nextBtn'); </script>";
    exit();
}
elseif(!isset($_POST['STAFF_GEO_LAT']) || $_POST['STAFF_GEO_LAT'] == NULL || $_POST['STAFF_GEO_LAT'] == "") {
    echo "<script> swal.fire('','Unable to fetch Geo coordinates'); loader_stop(); enable('nextBtn'); </script>";
}
elseif(!isset($_POST['STAFF_GEO_LONG']) || $_POST['STAFF_GEO_LONG'] == NULL || $_POST['STAFF_GEO_LONG'] == "") {
    echo "<script> swal.fire('','Unable to fetch Geo coordinates'); loader_stop(); enable('nextBtn'); </script>";
}else{

    $data = file_get_contents($dataTmpName); //Live Photo 
    $data_file_enc = base64_encode($data);
    $data_file = base64_encode($fileName);

    if(!isset($data_file_enc) || $data_file_enc == "" || $data_file_enc == false)  {
        echo "<script> loader_stop(); swal.fire('','Invalid upload file'); </script>";
        exit();
    }

    $updated_flag = true;
    
    $sql1_exe = $main_app->sql_run("SELECT * FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $plain_ass_refnum));
    $item_data = $sql1_exe->fetch();

    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R01)'); loader_stop(); enable('nextBtn'); </script>";
        exit();
    }

    $data1 = array();
    $data1['ASSREQ_BRANCHCAMERA_FLAG'] = "Y";
    $data1['ASSREQ_CUSTLOC_FLAG'] = "Y";
    $data1['APP_STATUS'] = "AP";
    $data1['STAFF_GEO_LAT']  = (isset($_POST['STAFF_GEO_LAT']) && $_POST['STAFF_GEO_LAT'] != "") ? base64_decode($_POST['STAFF_GEO_LAT']) : NULL;
    $data1['STAFF_GEO_LONG'] = (isset($_POST['STAFF_GEO_LONG']) && $_POST['STAFF_GEO_LONG'] != "") ? base64_decode($_POST['STAFF_GEO_LONG']) : NULL;
    $data1['CUST_IP'] = $main_app->current_ip();

    $db_output1 = $main_app->sql_update_data("ASSREQ_MASTER", $data1, array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'] )); // Update
    if($db_output1 == false) { $updated_flag = false; }

    if($updated_flag == true) {  
        
        $doc_sl = $main_app->sql_fetchcolumn("SELECT NVL(MAX(DOC_SL), 0) + 1 FROM ASSREQ_BRANCH_DETAILS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array("ASSREQ_REF_NUM" => $item_data['ASSREQ_REF_NUM'])); // Seq. No.
        
        if($doc_sl == false || $doc_sl == NULL || $doc_sl == "" || $doc_sl == "0") {
            echo "<script> swal.fire('','Unable to generate detail serial'); loader_stop(); enable('nextBtn'); </script>";
            exit();
        }

        $data2 = array();
        $data2['ASSREQ_REF_NUM'] = $item_data['ASSREQ_REF_NUM'];
        $data2['DOC_SL'] = $doc_sl;
        $data2['FILE_NAME'] =  $fileName;
        $data2['FILE_EXT'] = $Extension;
        $data2['DOC_PATH'] = $data_file_enc;
        $data2['CR_BY']    =  $_SESSION['USER_ID'];
        $data2['CR_ON']    = date("Y-m-d H:i:s");
        $main_app->sql_db_auditlog('A','ASSREQ_BRANCH_DETAILS',$data2); // Audit Log - DB Transaction
        $db_output2 = $main_app->sql_insert_data("ASSREQ_BRANCH_DETAILS", $data2); // Insert
        if($db_output2 == false) { $updated_flag = false; }
    }

    if($updated_flag == false) {
        echo "<script> swal.fire('','Unable to process your request.'); loader_stop(); enable('nextBtn'); </script>";
        exit();
    }

    // Success
    
    $main_app->sql_db_commit(); // Success - DB Transaction
    $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    $sid_ref_num = $safe->str_encrypt($plain_ass_refnum, $_SESSION['SAFE_KEY']);
    $go_url = "ass-branch-message?ref_Num=".$sid_ref_num; // Page Refresh URL
    echo "<script> goto_url('" . $go_url . "');</script>";
 
 
}


   

?>