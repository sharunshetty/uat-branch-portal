<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../../app-core/app_auto_load.php');

/** Check Post CMD*/
if(!isset($_POST['cmd'])) {
    http_response_code(404);
    exit();
}

/** Check User Session */
if(check_usr_login() == false) {
    echo "<script> $('#ModalWin-Help').modal('hide'); sess_error('Session expired. Please login again.'); </script>";
    exit();
}

/** Check Data */
foreach($_POST as $key => $value) {
    $_POST[$key] = $main_app->strsafe_input($value);
}

/** Check Command & CSRF Token */
if(isset($_POST['cmd']) && isset($_POST['req_token']) && $_POST['req_token'] == $_SESSION['APP_TOKEN']) {
	$cmd = $main_app->strsafe_input($_POST['cmd']);
} else {
    echo "<script> $('#ModalWin-Help').modal('hide'); sess_error('Unable to process your request'); </script>";
	exit();
}

/* ########## START ########## */

$cmd = str_replace('_','-',$cmd); // Access File
$cmd = str_replace('@','/show.',$cmd); // Access Folder

// Check for Sub-File
if(file_exists(dirname(__FILE__).'/show.'.$cmd.'.php')) {
    require_once(dirname(__FILE__).'/show.'.$cmd.'.php'); //Success
} else {
    http_response_code(404); //File not found
}
