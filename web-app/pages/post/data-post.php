<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../../../app-core/app_auto_load.php');

/** Check Post CMD*/
if(!isset($_POST['cmd'])) {
    http_response_code(404);
    exit();
}

/** Check Data */
foreach($_POST as $key => $value) {
    $_POST[$key] = $main_app->strsafe_input($value);
}

/** Check Command & CSRF Token */
if(isset($_POST['cmd']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['APP_TOKEN']) {
	$cmd = $main_app->strsafe_input($_POST['cmd']);
} else {
    echo "<script> sess_error('Unable to process your request'); </script>";
	exit();
}

/* ########## START ########## */

$cmd = str_replace('_','-',$cmd); // Access File
$cmd = str_replace('@','/post.',$cmd); // Access Folder

// Check for Sub-File
if(file_exists(dirname(__FILE__).'/post.'.$cmd.'.php')) {
    require_once(dirname(__FILE__).'/post.'.$cmd.'.php'); //Success
} else {
    http_response_code(404); //File not found
}
