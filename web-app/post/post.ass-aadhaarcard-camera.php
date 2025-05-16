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


if(isset($dataName) && $dataName != "") {
    if($dataName == "AADHAAR1F.jpg") {
        $file = 'AADHAAR1F';
    } elseif($dataName == "AADHAAR2B.jpg") {
        $file = 'AADHAAR2B';
    }
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
}else{

    $data = file_get_contents($dataTmpName); //Live Photo 
    $data_file_enc = base64_encode($data);
    $data_file = base64_encode($fileName);

    if(!isset($data_file_enc) || $data_file_enc == "" || $data_file_enc == false)  {
        echo "<script> loader_stop(); swal.fire('','Invalid upload file'); </script>";
        exit();
    }

    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    $sql1_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $plain_ass_refnum));
    $item_data = $sql1_exe->fetch();

    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R01)'); loader_stop(); enable('nextBtn'); </script>";
        exit();
    }

    //Max. Sl.
    
     $doc_sl = $main_app->sql_fetchcolumn("SELECT NVL(MAX(DOC_SL), 0) + 1 FROM ASSREQ_CUST_DATA WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = '$file' ", array("ASSREQ_REF_NUM" => $item_data['ASSREQ_REF_NUM'])); // Seq. No.
      if($doc_sl == false || $doc_sl == NULL || $doc_sl == "" || $doc_sl == "0") {
        echo "<script> swal.fire('','Unable to generate detail serial'); loader_stop(); enable('nextBtn'); </script>";
        exit();
    }
    
    // Save aadhar card camera photo
    $data = array();
    $data['ASSREQ_REF_NUM'] = (isset($item_data['ASSREQ_REF_NUM']) && $item_data['ASSREQ_REF_NUM'] != "") ? $item_data['ASSREQ_REF_NUM'] : NULL;
    $data['DOC_CODE'] = $file;
    $data['DOC_SL'] = $doc_sl;
    $data['FILE_NAME'] =  $fileName;
    $data['FILE_EXT'] = $Extension;
    $data['DOC_PATH'] = $data_file_enc;
    $data['CR_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : NULL;
    $data['CR_ON'] = date("Y-m-d H:i:s");
   
    $db_output = $main_app->sql_insert_data("ASSREQ_CUST_DATA", $data); // Update
    if($db_output == false) { $updated_flag = false; }
    
   if($updated_flag == true) {
        $data1 = array();
        if($file == "AADHAAR1F") {
            $data1['ASSREQ_AADHARFRONT_FLAG'] = "Y";
        }else{
            $data1['ASSREQ_AADHARBCK_FLAG'] = "Y";
        }
        $db_output1 = $main_app->sql_update_data("ASSREQ_MASTER", $data1, array( 'ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'] )); // Update
        if($db_output1 == false) { $updated_flag = false; }
    }

    if($updated_flag == true) {  
               
        // $('#sbt-cust-img-front').hide();
        // $('#sbt-cust-img-back').show();
        if($file == "AADHAAR2B") {
            $sid_ref_num = $safe->str_encrypt($plain_ass_refnum, $_SESSION['SAFE_KEY']);
            $go_url = "ass-form-pan?ref_Num=".$sid_ref_num; // Page Refresh URL
            echo "<script> goto_url('" . $go_url . "');</script>";
            exit();
        }
       
        //Success
         echo "<script> 
            $('#Labelimg').html('Back');
            loader_stop(); $('#WEBCAM_IMAGE').val('');
            setup();  show('my_camera');
            hide('results');show('CAPTURE_PHOTO');
            hide('tryPhoto'); hide('try_caption');
            $('#sbt-cust-img-front').hide();
            
        </script>";
            

    } else {

        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('nextBtn'); </script>";
    
    }

}


?>