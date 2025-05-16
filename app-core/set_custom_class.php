<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 2.2.0
 **/

/** No Direct Access */
defined('DIRPATH') OR exit();

/** Load Plugin Class */
require_once( DIRPATH . '/class/class_mod_browser.php'); // Browser Detection Class
// require_once( DIRPATH . '/class/class_mod_mail.php'); // PHPMailer Class
// require_once( DIRPATH . '/class/class_mod_captcha.php'); // Text & Captcha

/** Load Application Class */
require_once( DIRPATH . '/class/class_app_data.php'); // User Interface Class
require_once( DIRPATH . '/class/class_app_encrypt.php'); // Encryption & Decryption Class
require_once( DIRPATH . '/class/class_app_external_api.php'); // ReachWeb API (JSON Comm.)
require_once( DIRPATH . '/class/class_app_reach_mb.php'); // Prosper API (JSON Comm.)
require_once( DIRPATH . '/class/class_app_tba2auth.php'); // Prosper Authorization Class
require_once( DIRPATH . '/class/class_app_reach2api.php'); // ReachWeb API (JSON Comm.)
require_once( DIRPATH . '/class/class_app_remote.php'); // Remote API Connect Class
require_once( DIRPATH . '/class/class_app_core.php'); // App Core Class
