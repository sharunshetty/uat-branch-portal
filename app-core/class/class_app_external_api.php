<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 2.3.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/*
CREATE TABLE APP_API_TEMPLATES (
  API_CHANNEL_CODE      VARCHAR(30) NOT NULL, -- CBSAPIGW | EKYC | PAN
  API_SERVICE_CODE      VARCHAR(30) NOT NULL, -- SBACCTINQ
  API_DESC              VARCHAR(250),
  API_FORMAT            VARCHAR(30), -- XML | JSON
  API_REQ_METHOD        VARCHAR(30), -- POST | GET
  API_ENDPOINT_URL      VARCHAR(500),
  API_DATA_REPLACE_FLG  CHAR(1), -- Y|N
  API_DATA_TEMPLATE     TEXT,   -- Request Template
  API_ADD_PARAMS        TEXT,   -- Additional @Params
  ENCRYPTION_REQ_FLG    CHAR(1), -- Y|N
  ENCRYPTION_METHOD     VARCHAR(30), -- XMLSecLibs
  BEARER_TOKEN_REQ_FLG  CHAR(1), -- Y|N
  BEARER_TOKEN_SCOPE    VARCHAR(30),
  BEARER_TOKEN_URL      VARCHAR(500),
  CR_BY VARCHAR(32),
  CR_ON DATETIME,
  MO_BY VARCHAR(32),
  MO_ON DATETIME,
  TBA_KEY VARCHAR(32),
  PRIMARY KEY (API_CHANNEL_CODE, API_SERVICE_CODE)
);
*/

/** External API (JSON Comm.) */
class ExternalApi {

    private $api_server;

    public function __construct() {
        $this->api_server = API_REMOTE_ADDRESS;
    }

    /** Change API IP/Host */
    public function ChangeServer($host) {
        $this->api_server = $host;
    }


    /** Get bearer token */
    function getApiToken($ScopeId, $apiServerPath) {
        $req_data = API_GATEWAY_CONFIG;
        $req_data['grant_type'] = "client_credentials";
        $req_data['scope'] = $ScopeId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiServerPath);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "5");
        curl_setopt($ch, CURLOPT_TIMEOUT, "5");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req_data));
        $data = curl_exec($ch);
        return $data;
    }


    /** Reach API Connection */
    public function sendTemplate($channelId, $serviceCode, $send_data, $max_timeout = "10", $session = false, $headers = null) {

        global $main_app;
        $raw_req = $send_data;

        if($channelId == null || $channelId == "") {
            throw new Exception('Channel Id missing');
        }
        elseif($serviceCode == null || $serviceCode == "") {
            throw new Exception('Service code missing');
        }
        elseif($send_data == null || ($send_data == "" || !is_array($send_data))) {
            throw new Exception('Data missing');
        }
        
        $sql_exe = $main_app->sql_run("SELECT * FROM APP_API_TEMPLATES WHERE API_CHANNEL_CODE = :API_CHANNEL_CODE AND API_SERVICE_CODE = :API_SERVICE_CODE", array( 'API_CHANNEL_CODE' => $channelId, 'API_SERVICE_CODE' => $serviceCode ));
        $item_data = $sql_exe->fetch();

        if(!isset($item_data['API_CHANNEL_CODE']) || !isset($item_data['API_SERVICE_CODE']) || $item_data['API_CHANNEL_CODE'] != $channelId || $item_data['API_SERVICE_CODE'] != $serviceCode) {
            throw new Exception('Unable to fetch API template');
        }
        elseif(!isset($item_data['API_REQ_METHOD']) || ($item_data['API_REQ_METHOD'] != "POST" && $item_data['API_REQ_METHOD'] != "GET" && $item_data['API_REQ_METHOD'] != "POST2")) {
            throw new Exception('Req. Method missing in API template');
        }
        elseif(!isset($item_data['API_ENDPOINT_URL']) || $item_data['API_ENDPOINT_URL'] == NULL || $item_data['API_ENDPOINT_URL'] == "") {
            throw new Exception('Endpoint missing in API template');
        }

        //Replace Data with Template
        if(isset($item_data['API_DATA_REPLACE_FLG']) && $item_data['API_DATA_REPLACE_FLG'] == "Y") {
            $ApiTemplateTxt = isset($item_data['API_DATA_TEMPLATE']) ? $item_data['API_DATA_TEMPLATE'] : "";
            $replaceTxt = is_array($send_data) ? $send_data : array($send_data => $send_data);
            $send_data = strtr($ApiTemplateTxt, $replaceTxt);
            $raw_req = $send_data;
        }

        //Encrypt Request
        if(isset($item_data['ENCRYPTION_REQ_FLG']) && $item_data['ENCRYPTION_REQ_FLG'] == "Y") {
            if($item_data['ENCRYPTION_METHOD'] == "XMLSecLibs") {
                require_once( DIRPATH . '/class/class_mod_xmlseclibs.php');
                $send_data = XmlEncrypt($send_data);
            }
        }

        // API Bearer Token Generating
        if(isset($item_data['BEARER_TOKEN_REQ_FLG']) && $item_data['BEARER_TOKEN_REQ_FLG'] == "Y") {
            $output = $this->getApiToken($item_data['BEARER_TOKEN_SCOPE'], $item_data['BEARER_TOKEN_URL']);
            if($output) { $tokenData = json_decode($output, true); }

            if($output == false) {
                throw new Exception('API Token request timed out');
            }
            elseif($output == NULL || $output == "" || !isset($tokenData) || $tokenData == false) {
                throw new Exception('Invalid response from API Token server');
            }
            elseif(!isset($tokenData['token_type']) || $tokenData['token_type'] != "Bearer") {
                throw new Exception('Invalid API Token data');
            }
            elseif(!isset($tokenData['access_token']) || $tokenData['access_token'] == "") {
                throw new Exception('API Token data missing');
            } 
            else {
                $Bearer = array("Authorization: Bearer ".$tokenData['access_token']);
                $headers = (is_array($headers) && count($headers) > "0") ? array_merge($headers,$Bearer) : $Bearer;
            }
        }

        //Log API Create
        $ApiLogId = $main_app->sql_sequence("LOG__SENT_SEQ"); // Seq. No.

        if($ApiLogId == false || $ApiLogId == "" || $ApiLogId == "1" || $ApiLogId == NULL) {
            throw new Exception('Unable to build API reference number');
        }

        $api_log = array();
        $api_log['REQ_NUM'] = $ApiLogId;
        $api_log['REQ_CHANNEL_CODE'] = $item_data['API_CHANNEL_CODE'];
        $api_log['REQ_SERVICE_CODE'] = $item_data['API_SERVICE_CODE'];
        $api_log['REQ_RAW_DATA'] = is_array($raw_req) ? json_encode($raw_req) : $raw_req;
        if($raw_req != $send_data) {
            $api_log['REQ_DATA'] = is_array($send_data) ? json_encode($send_data) : $send_data;
        }
        $api_log['CR_ON'] = date("Y-m-d H:i:s");
        $api_log['CR_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : "SYSTEM";
        $api_sent_logs = $main_app->sql_insert_data("LOG_EXTAPI_SENT", $api_log); // API Log


        // **** Final API Call **** //
        $conn = new RemoteAPI;
        $response = $conn->connect($item_data['API_ENDPOINT_URL'], $item_data['API_REQ_METHOD'], $send_data, $max_timeout, $session, $headers);
        $raw_resp = $response;

        //Encrypt Response
        if(isset($item_data['ENCRYPTION_REQ_FLG']) && $item_data['ENCRYPTION_REQ_FLG'] == "Y") {
            if($response != false && $response != NULL || $response != "") {
                if($item_data['ENCRYPTION_METHOD'] == "XMLSecLibs") {
                    $response = XmlDecrypt($response);
                }
            }
        }   

        //Log API Response
        if($api_sent_logs) {
            $logData = array();
            $logData['RESP_RAW_DATA'] = $raw_resp;
            $logData['RESP_DATA'] = ($response != $raw_resp) ? $response : NULL;
			$logData['MO_ON'] = date("Y-m-d H:i:s");
            $result = $main_app->sql_update_data("LOG_EXTAPI_SENT", $logData, array('REQ_NUM' => $ApiLogId));
		}

        if($response == false) {
            throw new Exception('API request timed out');
        }
        elseif($response == NULL || $response == "") {
            
            throw new Exception('Invalid response from API server');
        }

        //Change Format
        if(isset($item_data['API_FORMAT']) && $item_data['API_FORMAT'] == "XML" && $response != "") {
            $xml_object = simplexml_load_string($response);
            if($xml_object == false || $xml_object == NULL) {
                throw new Exception('API response conversion failed');
            }
            $response = json_decode(json_encode($xml_object), true);
        }
        elseif(isset($item_data['API_FORMAT']) && $item_data['API_FORMAT'] == "JSON" && $response != "") {
            $response = json_decode($response, true);
            if($response == false || $response == NULL) {
                throw new Exception('Invalid output from API server');
            }
        }

        return $response;

    }

}
