<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/


/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

// if(!isset($_POST['sessflag']) || $_POST['sessflag'] != "1"|| $_POST['sessflag']== "" ) {
//     echo "Invalid request";
// }
// else

if(!isset($_POST['token']) ||  $_POST['token']== "" ) {
    echo "Invalid request";
}
elseif(!isset($_POST['user_id']) ||  $_POST['user_id']== "" ) {
    echo "Invalid request";
}else{
    
    $user_id = $main_app->strsafe_input(trim(strtoupper($_POST['user_id'])));
    $user_id = preg_replace("/[^a-zA-Z0-9]+/","",$user_id);

    $updated_flag = true;
    $sys_datetime = date("Y-m-d H:i:s");
    $data2 = array();
    $data2['LOGOUT_TIME'] = $sys_datetime;
    $data2['USR_ACTIVE_FLAG'] = "2";
    $db_output = $main_app->sql_update_data("APP_LOGIN_LOGS",$data2, array('LOGIN_USER' => $user_id));
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == true) {   

        echo "ok"; // Success

    } else {

        echo "Invalid request";
    
    }


}

?>