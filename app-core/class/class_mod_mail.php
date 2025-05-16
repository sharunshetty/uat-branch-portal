<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 2.2.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Inc: PHPMailer Class */
require_once(dirname(__FILE__) . '/PHPMailer/Exception.php');
require_once(dirname(__FILE__) . '/PHPMailer/PHPMailer.php');
require_once(dirname(__FILE__) . '/PHPMailer/SMTP.php');
$mail = new PHPMailer\PHPMailer\PHPMailer;
