<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 3.0.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Remote API Connect Class */
class RemoteAPI {

    /** Single Connection */
    public function connect($url, $method = null, $values = null, $max_timeout = 10, $session = false) {
        try {

            global $main_app;

            if($max_timeout == null || !is_numeric($max_timeout)) {
                $max_timeout = 90;
            }

            ini_set('max_execution_time', $max_timeout + 30);

            // Settings
            $options = array(
                CURLOPT_RETURNTRANSFER => true,   // return web page - If you set TRUE then curl_exec returns actual result
                CURLOPT_HEADER         => true,  // Return headers
                CURLOPT_FOLLOWLOCATION => true,   // follow redirects
                CURLOPT_MAXREDIRS      => 5,     // stop after 10 redirects
                CURLOPT_ENCODING       => "",     // handle compressed
                CURLOPT_USERAGENT      => "", // name of client
                CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
                CURLOPT_CONNECTTIMEOUT => $max_timeout,     // time-out on connect
                CURLOPT_TIMEOUT        => $max_timeout,     // time-out on response
                CURLOPT_SSL_VERIFYPEER => false,  // SSL verification
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP protocol version
            );

            // CURL Session Mgt.
            if($session == true) {
                if(!isset($_SESSION['COOKIE_USER_DATA'])) { 
                    $main_app->session_set([ 'COOKIE_USER_DATA' => "" ]);
                }
                if($_SESSION['COOKIE_USER_DATA'] != NULL) {
                    $options[CURLOPT_HTTPHEADER] = array("Cookie: ".$_SESSION['COOKIE_USER_DATA']);
                }
            }

            // Method
            if($method == "post") {
                //POST
                $options[CURLOPT_URL] = $url;   // URL
                $options[CURLOPT_POST] = true; // Post method
                $options[CURLOPT_POSTFIELDS] = http_build_query($values); // Post value
            } else {
                //GET
                if(is_array($values)) {
                    $options[CURLOPT_URL] = $url."?".http_build_query($values);
                } else {
                    $options[CURLOPT_URL] = $url;
                }
            }

            $ch = curl_init(); // CURL Start
            curl_setopt_array($ch, $options); // Load CURL Options
            $response = curl_exec($ch); // Connection
            if($response) {
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($response, 0, $header_size);
                $body = substr($response, $header_size);
                $response = $body;
                // CURL Session Mgt.
                if($session == true) {
                    if(preg_match("/Set-cookie: (.*)\n/iU", $header, $matches)) {
                        $cookie = trim(substr($matches[1], strpos($matches[1],':')));
                        //$cookie = str_replace('; Path=/', '', $cookie);
                        $cookie = trim(substr($cookie, 0, strpos($cookie,';')));
                        $main_app->session_set([ 'COOKIE_USER_DATA' => $cookie ]); // Set cookie in Session
                    }
                }
            }

            $error_msg = curl_error($ch); // Error Desc.
            curl_close($ch); // Close CURL

            return $response; // Return

        } catch(Exception $e) {
            //die($e->getMessage());
            return false;
        }
    }


    /** Multiple Connection (Concurrent Requests) - Max:250 */
    public function multi_connect($url, $method = null, $values = null, $max_timeout = 10, $session = false) {
        try {

            global $main_app;

            if(!is_numeric($max_timeout)) {
                $max_timeout = 10;
            }

            ini_set('max_execution_time', $max_timeout + 30);

            // CURL Session Mgt.
            if($session == true) {
                if(!isset($_SESSION['COOKIE_USR_DATA'])) { 
                    $main_app->session_set([ 'COOKIE_USR_DATA' => "" ]);
                }
                $cookie_path = tempnam(sys_get_temp_dir(),'api');
                file_put_contents($cookie_path, $_SESSION['COOKIE_USR_DATA']);
            }

            $ch = curl_multi_init();
            $records = array();
            foreach( $values as $i => $data ) {

                $records[$i] = curl_init();

                //Settings
                $options = array(
                    CURLOPT_RETURNTRANSFER => true,   // return web page - If you set TRUE then curl_exec returns actual result
                    CURLOPT_HEADER         => false,  // Return headers
                    CURLOPT_FOLLOWLOCATION => true,   // follow redirects
                    CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
                    CURLOPT_ENCODING       => "",     // handle compressed
                    CURLOPT_USERAGENT      => "", // name of client
                    CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
                    CURLOPT_CONNECTTIMEOUT => $max_timeout,     // time-out on connect
                    CURLOPT_TIMEOUT        => $max_timeout,     // time-out on response
                    CURLOPT_SSL_VERIFYPEER => false,  // SSL verification
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP protocol version
                );

                // CURL Session Mgt.
                if($session == true) {
                    $options[CURLOPT_COOKIEFILE] = $cookie_path; // Read Cookie
                    $options[CURLOPT_COOKIEJAR] = $cookie_path; // Write Cookie
                }

                if($method == "post") {
                    //POST
                    $options[CURLOPT_URL] = $url;   // URL
                    $options[CURLOPT_POST] = true; // Post method
                    $options[CURLOPT_POSTFIELDS] = $data; // Post value
                } else {
                    //GET
                    if(is_array($data)) {
                        $options[CURLOPT_URL] = $url . "?" . http_build_query($data);
                    } else {
                        $options[CURLOPT_URL] = $url;
                    }
                }

                curl_setopt_array($records[$i],$options);
                curl_multi_add_handle($ch, $records[$i]);
                $options = array(); //reset
            }

            do {
                curl_multi_exec($ch, $running);
                curl_multi_select($ch);
            } while ($running > 0);

            $response = array();
            foreach($values as $i => $data) {
                $response[$i] = curl_multi_getcontent($records[$i]);
                curl_multi_remove_handle($ch, $records[$i]);
            }

            // CURL Session Mgt.
            if($session == true) {
                $main_app->session_set([ 'COOKIE_USR_DATA' => file_get_contents($cookie_path) ]);
                unlink($cookie_path);
            }

            //Return
            curl_multi_close($ch);
            return $response;

        } catch(Exception $e) {
            //die($e->getMessage());
            return false;
        }
    }

}
