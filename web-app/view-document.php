<?php

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

/** Check User Session */
require_once(dirname(__FILE__) . '/check-login.php');

if(isset($_REQUEST['FILE'])) { $file = $main_app->strsafe_input($_REQUEST['FILE']); } else { $file = ""; }

if(isset($file) && $file != "") {
    $FILE_PATH = $safe->str_decrypt($file,$_SESSION['SAFE_KEY']);
}

if(!isset($FILE_PATH) || $FILE_PATH == false || $FILE_PATH == "") {
    echo "<font style='color:red;'>Error : Invalid data.</font>";
}
else {

    $file_extn = explode(".", $FILE_PATH);
    $file_extn = strtolower(end($file_extn));

    if($file_extn == "jpg") { $content_type = "image/jpeg"; }
    elseif($file_extn == "jpeg") { $content_type = "image/jpeg"; }
    elseif($file_extn == "png") { $content_type = "image/png"; }
    elseif($file_extn == "pdf") { $content_type = "application/pdf"; }
    elseif($file_extn == "webm") { $content_type = "video/webm"; }
    elseif($file_extn == "mp4") { $content_type = "video/mp4"; }
    else { $content_type = ""; }

    if(file_exists($FILE_PATH)) {
        header("Content-Description: File Transfer"); 
        header("Content-Type:". $content_type);
        //header("Content-Disposition: attachment; filename=" . basename($FILE_PATH));
        readfile($FILE_PATH);
    } 
    else {
        echo "<font style='color:red;'>Error : File not found.</font>";
        //echo $FILE_PATH;
    }

}

?>