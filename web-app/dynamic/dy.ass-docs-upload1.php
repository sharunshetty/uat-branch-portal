<?php
/**
 * POWERED BY 	: LCODE TECHNOLOGIES PVT. LTD.
 * DEVELOPER  	: SHIVANANDA SHENOY (MB)
 **/

/** NO DIRECT ACCESS **/
defined('PRODUCT_NAME') OR exit();

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";

if(!isset($_POST['FILE_CODE']) || $_POST['FILE_CODE'] == NULL || ($_POST['FILE_CODE'] != "PAN" && $_POST['FILE_CODE'] != "DL1F" && $_POST['FILE_CODE'] != "DL2B" && $_POST['FILE_CODE'] != "AADHAAR1F" && $_POST['FILE_CODE'] != "AADHAAR2B" && $_POST['FILE_CODE'] != "VID1F" && $_POST['FILE_CODE'] != "VID2B" && $_POST['FILE_CODE'] != "PPT1F" && $_POST['FILE_CODE'] != "PPT2B")) {
    echo "<script> swal.fire('','E002: Invalid Request'); loader_stop(); enable('sbt'); </script>";
    exit();
}

if(isset($_POST['ASS_REF_NUM']) && $_POST['ASS_REF_NUM'] != "") {
    $assref_num = $safe->str_decrypt($_POST['ASS_REF_NUM'], $_SESSION['SAFE_KEY']); 
    if($assref_num) {
        //$sql_exe = $main_app->sql_run("SELECT REKYC_REF_NUM, REKYC_CUST_ID FROM MOD_REKYC WHERE REKYC_REF_NUM = :REKYC_REF_NUM AND REKYC_STATUS = 'D'", array('REKYC_REF_NUM' => $ref_num));
        $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
        $item_data = $sql_exe->fetch();
    }
}

// File Configs
$allowed_ext = ['jpeg','jpg','png'];
$allowed_mimes = ['image/jpg','image/jpeg','image/png'];
$allowed_size = "500000"; //Bytes // - 2097152 = 2MB // 550000 = 550KB // 350000 = 350 KB  500000 = 500 KB
$Extension = "";

function is_image($path) {
    //ini_set('gd.jpeg_ignore_warning', 0);
    try{
        if(@imagecreatefromjpeg($path) || @imagecreatefrompng($path)) {
            $a = getimagesize($path);
            if(isset($a[2])) {
                $image_type = $a[2];
                //IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP
                if(in_array($image_type , array(IMAGETYPE_JPEG ,IMAGETYPE_PNG))) {
                    return true;
                }
            }
        }
        return false;
    }
    catch(Exception $e) {
        return false;
    }
}

if(isset($_FILES['UPLOAD_FILE']) && !empty($_FILES['UPLOAD_FILE']) && $_FILES['UPLOAD_FILE']['error'] == 0) {

    $dataName = $_FILES['UPLOAD_FILE']['name'];
    $dataSize = $_FILES['UPLOAD_FILE']['size'];
    $dataTmpName  = $_FILES['UPLOAD_FILE']['tmp_name'];
    $dataType = $_FILES['UPLOAD_FILE']['type'];
    $dataTmp = explode('.', $dataName);
    $Extension = strtolower(end($dataTmp));
    $fileName = $_SESSION['USER_ID'].'-'.$dataName;

}

if(!isset($_FILES['UPLOAD_FILE']['name']) || empty($_FILES['UPLOAD_FILE']['name'] || $_FILES['UPLOAD_FILE']['tmp_name'] == NULL)) {
    echo "<script> swal.fire('','file not selected'); enable('sbt'); loader_stop(); </script>";
}
elseif($_FILES['UPLOAD_FILE']['error'] != 0 || !is_uploaded_file($_FILES['UPLOAD_FILE']['tmp_name'])) {
    echo "<script> swal.fire('','file not uploaded'); enable('sbt'); loader_stop(); </script>";
}
elseif(!in_array($Extension,$allowed_ext) || !in_array($dataType,$allowed_mimes)) {
    echo "<script> swal.fire('','Unsupported file format'); enable('sbt'); loader_stop();</script>";
}
elseif(!empty($_FILES['UPLOAD_FILE']['name']) && $dataSize > $allowed_size) {
    echo "<script> swal.fire('','Maximum file size 500 KB'); enable('sbt'); loader_stop(); </script>";
}
elseif(is_image($_FILES['UPLOAD_FILE']['tmp_name']) == false) {
    echo "<script> swal.fire('','W09: Invalid file type uploaded'); enable('sbt'); loader_stop(); </script>";
}
else {

    $data = file_get_contents($dataTmpName); //Files 
    // $data_file = base64_encode($data);
    $data_file = base64_encode($fileName);

    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");

    if(isset($dataTmpName) && $dataTmpName != "") { 

        $req_year = date("Y", strtotime($sys_datetime));
        $req_month = date("m", strtotime($sys_datetime));
        $req_date = date("d", strtotime($sys_datetime));
    
        //image path
        $path_prefix =  $req_year . "/" . $req_month . "/" . $req_date . "/" . $item_data['ASSREQ_REF_NUM'] . "/";
        $file_dir = UPLOAD_DOCS_DIR . $path_prefix;
    
        //Creating folder if not exists.
        if ($file_dir && !is_dir($file_dir)) {
            mkdir($file_dir, 755, true);
        }
    
       // $file_name = $item_data['REKYC_CUST_ID'] . '-' .$_POST['FILE_CODE'] . '-' .$item_data['ASSREQ_REF_NUM'] . '.jpg';
    
        $file_name = $_POST['FILE_CODE'] . '-' .$item_data['ASSREQ_REF_NUM'] . '.jpg';
    
        // $a = $ab['FILE_NAME'];
        $file_path = $file_dir . $file_name;
        $success = move_uploaded_file($dataTmpName, $file_dir . $file_name);

        if(!$success) { $updated_flag = false; }
    } 

    // if($updated_flag == true) {
        
    //     $data2 = array();
    //     $data2['REKYC_REF_NUM'] = $assref_num;
    //     $data2['DTL_SL'] = $main_app->sql_fetchcolumn("SELECT NVL(MAX(DTL_SL), 0) + 1 FROM MOD_REKYC_UPLOADS WHERE REKYC_REF_NUM = :REKYC_REF_NUM", array("REKYC_REF_NUM" => $item_data['REKYC_REF_NUM'])); // Seq. No.
    //     $data2['CUST_ID'] = $item_data['REKYC_CUST_ID'];
    //     $data2['DOC_TYPE_CODE'] = $_POST['FILE_CODE'];
    //     $data2['DMS_NODE_REF_ID'] = "";
    //     $data2['FILE_NAME'] = $file_name;
    //     $data2['FILE_EXT'] = $Extension;
    //     $data2['STATUS'] = "1";
    //     $data2['DOC_PATH'] = $path_prefix;

    //     $data2['MO_BY'] = $_SESSION['USR_ID'];
    //     $data2['MO_ON'] = $sys_datetime;
        
    //     $db_output = $main_app->sql_insert_data("MOD_REKYC_UPLOADS",$data2, array("REKYC_REF_NUM" => $item_data['REKYC_REF_NUM'])); // Update
    //     if($db_output == false) { $updated_flag = false; }

    // }

    echo "<script> loader_stop(); hide('".$_POST['FILE_CODE']."_BTN'); </script>";
    $filecode = $_POST['FILE_CODE'];
    $add_row ="";
    $add_row .= '<a class="font-weight-bold text-success">Uploaded <i class="mdi mdi-check"></i></a>';

    echo "<script> swal.fire({ title:'File Uploaded Successfully', text:'', icon:'success', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";
    echo "<script>  $('#".$filecode."_FILE_REF_NO').val(deStr('".$main_app->strsafe_modal($data_file)."')); enable('sbt'); hide('sbt'); $('#".$filecode."_upld_id').html('".$add_row."'); loader_stop(); </script>";
}



?>