<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 2.2.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

try {

    //Loan Application Settings
    $settings = $db_master->prepare("SELECT OPTION_NAME, OPTION_VALUE FROM ASSAPP_DATA_SETTINGS WHERE OPTION_STATUS = 1");
    $settings->execute();

    while($sdata = $settings->fetch()) {
        if(!defined($sdata['OPTION_NAME'])) {
            define($sdata['OPTION_NAME'], $sdata['OPTION_VALUE']);
        }
    }

} catch (PDOException $e) {
    die('Unable to load application, Please try again later.');
    //die($e->getMessage());
}

