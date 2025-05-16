<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 2.2.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Reach MB API (For jBoss Comm.) */
class ReachMobApi {

    private $api_server;

private $public_key = "-----BEGIN CERTIFICATE-----
MIIG+TCCBeGgAwIBAgIMIsEBKXo0rVjq2SjgMA0GCSqGSIb3DQEBCwUAMGIxCzAJ
BgNVBAYTAkJFMRkwFwYDVQQKExBHbG9iYWxTaWduIG52LXNhMTgwNgYDVQQDEy9H
bG9iYWxTaWduIEV4dGVuZGVkIFZhbGlkYXRpb24gQ0EgLSBTSEEyNTYgLSBHMzAe
Fw0yMTA5MjIwODMxMDRaFw0yMjEwMjQwODMxMDRaMIHpMR0wGwYDVQQPDBRQcml2
YXRlIE9yZ2FuaXphdGlvbjEeMBwGA1UEBRMVVTY1MTEwUEIxOTk5UExDMDIyNjM0
MRMwEQYLKwYBBAGCNzwCAQMTAklOMRcwFQYLKwYBBAGCNzwCAQITBlB1bmphYjEL
MAkGA1UEBhMCSU4xDzANBgNVBAgTBlB1bmphYjESMBAGA1UEBxMJSmFsYW5kaGFy
MSswKQYDVQQKEyJDQVBJVEFMIFNNQUxMIEZJTkFOQ0UgQkFOSyBMSU1JVEVEMRsw
GQYDVQQDExJ1YXQuY2FwaXRhbGJhbmsuaW4wggEiMA0GCSqGSIb3DQEBAQUAA4IB
DwAwggEKAoIBAQC6UvU7wNZXSJRjHJqNfV8hWQciVcZYBG/lHXVDFE3kSGBpben9
Mj2v1bPS843AEbc4MT9pl62PX2TBZUiUEkne/GhYo2eK62ArS5ff7VRqILB7mD8N
1nTDFXnyaVp3zUVvvtadm7F8+bibiPQL0aE7CiJyLfvWm9Tddh/f9qXxvKe4L5Rq
daoIPvunOEm5kt4Nje/HQuY9JkTn3Rc3amPjd+OFJgJfDFKFDufwsIzN5+JfZmin
drBzN8P3recxRAIIAYIAYbBx6B9km8m8v5YwGnx3vFn2laeLTkRO4mtj6wkqcA3P
VBCb+bWNPMLn1trn1EzWQYwcAukijj1iYnVPAgMBAAGjggMlMIIDITAOBgNVHQ8B
Af8EBAMCBaAwgZYGCCsGAQUFBwEBBIGJMIGGMEcGCCsGAQUFBzAChjtodHRwOi8v
c2VjdXJlLmdsb2JhbHNpZ24uY29tL2NhY2VydC9nc2V4dGVuZHZhbHNoYTJnM3Iz
LmNydDA7BggrBgEFBQcwAYYvaHR0cDovL29jc3AyLmdsb2JhbHNpZ24uY29tL2dz
ZXh0ZW5kdmFsc2hhMmczcjMwVQYDVR0gBE4wTDBBBgkrBgEEAaAyAQEwNDAyBggr
BgEFBQcCARYmaHR0cHM6Ly93d3cuZ2xvYmFsc2lnbi5jb20vcmVwb3NpdG9yeS8w
BwYFZ4EMAQEwCQYDVR0TBAIwADA1BgNVHREELjAsghJ1YXQuY2FwaXRhbGJhbmsu
aW6CFnd3dy51YXQuY2FwaXRhbGJhbmsuaW4wHQYDVR0lBBYwFAYIKwYBBQUHAwEG
CCsGAQUFBwMCMB8GA1UdIwQYMBaAFN2z522oLujFTm7PdOZ1PJQVzugdMB0GA1Ud
DgQWBBT6bQM/AllHI4ZwEvpgO0oas3IfFzCCAXwGCisGAQQB1nkCBAIEggFsBIIB
aAFmAHUAb1N2rDHwMRnYmQCkURX/dxUcEdkCwQApBo2yCJo32RMAAAF8DKE9IAAA
BAMARjBEAiBTK4RIZ6NR6FgYIY2941ktSJWBFiiU8DCstZO7BuTrGQIgf7ax7KYN
Sl4gfdz4HYtfIoOUctE/2lEb5IYPFbZoSzIAdgApeb7wnjk5IfBWc59jpXflvld9
nGAK+PlNXSZcJV3HhAAAAXwMoT0VAAAEAwBHMEUCIAMwPQ3lKAgxaFW+1Z+hfqr6
IpIIXpCBHuAIeijdwbfJAiEA6FBiMvTkOm6VizJq6qrNATteKdZ0xpE0g+6OwNQp
F8UAdQBRo7D1/QF5nFZtuDd4jwykeswbJ8v3nohCmg3+1IsF5QAAAXwMoT1XAAAE
AwBGMEQCIFlrkQWrgkLS9X4r/fbD9cyX5Ngx8dOK9d1DsVBF3li+AiAbbweeSEXi
ByhSSfGQ4/9h0W4mdF6oQNTdn+2IE2nMyTANBgkqhkiG9w0BAQsFAAOCAQEAdEzX
OW9Cejo86Es85H/7KX3EYy2q44eti8HveA5+mlRErXz3I3ROBy7S4AZB+Zc5U5/j
MlvwH1/T/NHeVx3kiVqjfcneTfsbktmjNrZ42lu/ZRqv4MTjBC5GdFqDMzWOAh8w
e+35xXb/ZnqV8OgFBEKikXoR5eGL+wxUCnygiEbybnltSVQ4GF4CFScJQiIvEv6R
gEYfIFYKR6ETka4tiHv3lwi+66n6zJDFdPoHjUeW6U85HHV56qBRrVzOaPtr9mFx
FmBxMEMlNbSlIMQQF7co5CghgIiLMogbZE0Of4D7N0NNtZkgKNZaN4nq/Ecw9lrT
RymVLaV/VCbInrBUNQ==
-----END CERTIFICATE-----";

private $private_key = "-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC6UvU7wNZXSJRj
HJqNfV8hWQciVcZYBG/lHXVDFE3kSGBpben9Mj2v1bPS843AEbc4MT9pl62PX2TB
ZUiUEkne/GhYo2eK62ArS5ff7VRqILB7mD8N1nTDFXnyaVp3zUVvvtadm7F8+bib
iPQL0aE7CiJyLfvWm9Tddh/f9qXxvKe4L5RqdaoIPvunOEm5kt4Nje/HQuY9JkTn
3Rc3amPjd+OFJgJfDFKFDufwsIzN5+JfZmindrBzN8P3recxRAIIAYIAYbBx6B9k
m8m8v5YwGnx3vFn2laeLTkRO4mtj6wkqcA3PVBCb+bWNPMLn1trn1EzWQYwcAuki
jj1iYnVPAgMBAAECggEAAk5CgKHLRTs9sthIxVV21ufOIcuj77TmWZR6vgDDUf7f
So60pZB4mPNxDTw8VdMiTEy3tyvMVwjcK1SAiGyNg3fmJqqm/l4ZS4/c6kV6lPGg
nXbt2NL9kOh0IN7S4sakhFR9PWgO0zgX47zXIEpQz3U5peYRxr8Y7ECo9lGMDii9
EJi6kU3hIpoJSuSU3intCAQ6sYdBRKqR7E3vY4a+gka+evzpGVXoUHE/DHxBH3Zg
O4FPefrNljt0yGCsPbqX89bRUbZv5YENL7ozNh8xhUg6lg1PYg6EZU06cAy62V+s
3sWuGaNjqO59qUNTVPMZnSzkbmkpZHcGPpIqaD69WQKBgQDwHUPFfp478MYNYeuv
dzAdjpzJOherIUukRjfvS+C2JE0KBQPN00HPojs+PDo93bKZu4rUlHaut3LQAz9b
TJ8PIDrVWHziyQ24O+iG0ARtOwLVaRMqzfCbd/WkLzL6yNH5PWezazgomB1Xxsvx
N9fF2MR8GRZ0kriHlW/7gNE8wwKBgQDGpqpqNySgwtJLunLfeBzt+BKzulMRd3JG
E6SfGAD8p7qNMgrRqrlCslqcN11+sTwCSRePP2t3x29niekki640HD+FqYyKwh9Q
gFTmBOi6U43AOK0mhSoc3NsTQHYFkIP+bGKsC+KthEllvo2sCYHnM4S7z0oWtQkh
mZ15n4xMhQKBgAUTCUCeOWfY4cacoCd4JFsrjWKvSJrEPF3/YeU7vEb9I2enzXnH
Eif6LLhW+4AdNE4NaZSugoeUtudweiFK0D24l6W0lgcQ6qpPEc3vjXhle9zYuJ/5
9DDE6zsKCFDGd7GFu7BN/sTU2iHlwJ+bZ4L13skkmpXiF+Y9oT02brWTAoGAXxex
RB8ZfYtFN9MM4kSRNKkn6rkqwpW3Mix9BVtsPYXdG3H1E+DxxieykE1AiEE3RLDQ
glYNj8z8W0f9Pu1Oest67PT4vgOnEdYcze9TqL4OgRGgcWwVeLOQe2rEFG3VgZlI
sqsN25oTuIy7LLtwX8idpXnZMX1/3YG3jFz/wDECgYEA4gUo9rwIeBxr8He+0Z4v
QTvvNcqQ3m6y1z3YB754A7r4rfGHnAwvQL2c2fvKRoxiOhKXJTzzb7zcUMmQP1iQ
aCI5cPlpRDtbMrFOOoR7Q1GBToIe4qhkBe0XPFmhTKvrBkkmWygkhr4UEbzxoA+D
KR6sCvfuhJYGVa8ICweaPso=
-----END PRIVATE KEY-----"; // 2048-bits Key

    public function __construct() {
        $this->api_server = API_REMOTE_ADDRESS;
    }

    /** Change API IP/Host */
    public function ChangeServer($host) {
        $this->api_server = $host;
    }

    /**  AES Encryption : AES/ECB/PKCS5PADDING */
    public function aes_encrypt($str, $key) {
        $data = openssl_encrypt($str, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return base64_encode($data);
    }

    /** AES Decryption : AES/ECB/PKCS5PADDING */
    public function aes_decrypt($str, $key) {
        $data = openssl_decrypt(base64_decode($str), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return $data;
    }

    /** RSA Encryption : RSA/ECB/PKCS1Padding */
    public function rsa_encrypt($data) {
        if(openssl_public_encrypt($data, $encrypted, $this->public_key)) {
            return base64_encode($encrypted);
        }
        return false;
    }

    /** RSA Decryption : RSA/ECB/PKCS1Padding */
    public function rsa_decrypt($data) {
        if(openssl_private_decrypt(base64_decode($data), $decrypted, $this->private_key)) {
            return $decrypted;
        }
        return false;
    }

    /** API Connection */
    public function ReachMobConnect($data, $max_timeout = null) {

        global $main_app;
        $api_channel_id = API_REACH_MB_CHANNEL;

        if(!isset($_SESSION['SAFE_AES128_KEY']) || $_SESSION['SAFE_AES128_KEY'] == NULL) {
            $main_app->session_set([ 'SAFE_AES128_KEY' => substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 16) ]); // New random key
        }
        
        $build_data = array();
        $build_data['CHANNEL_ID'] = $api_channel_id;
        $build_data['ENC_DATA'] = $this->aes_encrypt(json_encode($data, JSON_UNESCAPED_SLASHES), $_SESSION['SAFE_AES128_KEY']);
        $build_data['ENC_KEY'] = $this->rsa_encrypt($_SESSION['SAFE_AES128_KEY']);

        if($build_data['ENC_DATA'] == false) {
            throw new Exception('A01: Unable to build API request');
        }
        elseif($build_data['ENC_KEY'] == false) {
            throw new Exception('A02: Unable to build API request');
        }

        //Log API Create
        $ApiLogId = $main_app->sql_sequence("LOG_EXTAPI_SENT_SEQ"); // Seq. No.
       
        if($ApiLogId == false || $ApiLogId == "" || $ApiLogId == "1" || $ApiLogId == NULL) {
            throw new Exception('Unable to build API reference number');
        }

        $api_log = array();
        $api_log['REQ_NUM'] = $ApiLogId;
        $api_log['REQ_CHANNEL_CODE'] = $api_channel_id;
        $api_log['REQ_SERVICE_CODE'] = isset($data['METHOD_NAME']) ? $data['METHOD_NAME'] : NULL;
        $api_log['REQ_RAW_DATA'] = is_array($data) ? json_encode($data) : $data;
        if($data != $build_data) {
            $api_log['REQ_DATA'] = is_array($build_data) ? json_encode($build_data) : $build_data;
        }
        $api_log['CR_ON'] = date("Y-m-d H:i:s");
        $api_log['CR_BY'] = isset($_SESSION['USER_REF_NUM']) ? $_SESSION['USER_REF_NUM'] : NULL;
        $api_sent_logs = $main_app->sql_insert_data("LOG_EXTAPI_SENT", $api_log); // API Log

        //Send request to API
        $conn = new RemoteAPI;
        $response = $conn->connect( $this->api_server, 'post', $build_data, $max_timeout);
        $raw_resp = $response;

        try {

            if($response && $response != "") {

                $resp_data = json_decode($response, true);

                if(!isset($resp_data['CHANNEL_ID']) || $resp_data['CHANNEL_ID'] == "") {
                    if(isset($resp_data['responseCode']) && $resp_data['responseCode'] != "") {
                        return $resp_data;
                    }
                    throw new Exception('A03: Invalid response from API server');
                }
                elseif ($resp_data['CHANNEL_ID'] != $api_channel_id) {
                    throw new Exception('A04: Invalid API Channel');
                }
                elseif (!isset($resp_data['ENC_DATA']) || $resp_data['ENC_DATA'] == "") {
                    throw new Exception('A05: Invalid response from API server');
                }
                elseif (!isset($resp_data['ENC_KEY']) || $resp_data['ENC_KEY'] == "") {
                    throw new Exception('A06: Invalid response from API server');
                }
                else {

                    $resp_aes_key = $this->rsa_decrypt($resp_data['ENC_KEY']);

                    if($resp_aes_key == false) {
                        throw new Exception('A07: Invalid key from API server');
                    }
                    elseif (strlen($resp_aes_key) != "16") {
                        throw new Exception('A08: Invalid key length from API server');
                    }
                    else {

                        $decoded_data = $this->aes_decrypt($resp_data['ENC_DATA'], $resp_aes_key);
                        $response = ($decoded_data) ? json_decode($decoded_data, true) : false;

                        if($response == false || !is_array($response)) {
                            throw new Exception('A09: Invalid data from API server');
                        }

                    }

                }

            } else {
                throw new Exception('Unable to connect API server');
            }

        } catch(Exception $e) {
            $ErrorMsg = $e->getMessage(); //Error from section
        }

        //Log API Response
        if($api_sent_logs) {
            $jsonData = json_decode($raw_resp, true);
            $logData = array();
            $logData['RESP_RAW_DATA'] = ($jsonData && is_array($jsonData)) ? $raw_resp : substr($raw_resp,0,500);
                $logData['RESP_DATA'] = ($response == $raw_resp) ? NULL : (is_array($response) ? json_encode($response) : substr($response,0,500));
			$logData['MO_ON'] = date("Y-m-d H:i:s");
            $result = $main_app->sql_update_data("LOG_EXTAPI_SENT", $logData, array('REQ_NUM' => $ApiLogId));
		}

        // Throw Final Error
        if(isset($ErrorMsg) && $ErrorMsg != "") {
            throw new Exception($ErrorMsg);
        }

        return $response;

    }

}