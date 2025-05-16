<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 2.2.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** RSA, AES - Encryption & Decryption */
class Encryption {

/*
-----BEGIN PUBLIC KEY-----
MIIBITANBgkqhkiG9w0BAQEFAAOCAQ4AMIIBCQKCAQBdLUf+QQXRnr5Fa7IjMMe7
gwLsZGTQmOEpSHfjOPl8atyT3QSI9xQB/2AOZnlo9psLBumYZpMhZNMt7SLnFqK5
EnYiVmL+wRi9LB27/raGn3G5h3Fm33g1js6zhNIgKaepWW79qw8vkfNX9DbZ/CEr
0UQFnq9gvBlu/j4SzrmvD+lS3X1BGd3RdbXcCmZnGY+O8lOxgR8vxjXXvfTJ4T4h
gDMD6+rRxTHKJPQmpYpigQqQJM6idTCWQqYGp75uNNO47rSWit8TfA7qYAzrTlZh
0drErVMWQXmGbEMOQ5IIRBuaH2ND19pERIzt2FDKDhckVVAoX36pZrsPwUNONktR
AgMBAAE=
-----END PUBLIC KEY-----
*/

public $public_key = "MIIBITANBgkqhkiG9w0BAQEFAAOCAQ4AMIIBCQKCAQBdLUf+QQXRnr5Fa7IjMMe7gwLsZGTQmOEpSHfjOPl8atyT3QSI9xQB/2AOZnlo9psLBumYZpMhZNMt7SLnFqK5EnYiVmL+wRi9LB27/raGn3G5h3Fm33g1js6zhNIgKaepWW79qw8vkfNX9DbZ/CEr0UQFnq9gvBlu/j4SzrmvD+lS3X1BGd3RdbXcCmZnGY+O8lOxgR8vxjXXvfTJ4T4hgDMD6+rRxTHKJPQmpYpigQqQJM6idTCWQqYGp75uNNO47rSWit8TfA7qYAzrTlZh0drErVMWQXmGbEMOQ5IIRBuaH2ND19pERIzt2FDKDhckVVAoX36pZrsPwUNONktRAgMBAAE=";

private $private_key = "-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQBdLUf+QQXRnr5Fa7IjMMe7gwLsZGTQmOEpSHfjOPl8atyT3QSI
9xQB/2AOZnlo9psLBumYZpMhZNMt7SLnFqK5EnYiVmL+wRi9LB27/raGn3G5h3Fm
33g1js6zhNIgKaepWW79qw8vkfNX9DbZ/CEr0UQFnq9gvBlu/j4SzrmvD+lS3X1B
Gd3RdbXcCmZnGY+O8lOxgR8vxjXXvfTJ4T4hgDMD6+rRxTHKJPQmpYpigQqQJM6i
dTCWQqYGp75uNNO47rSWit8TfA7qYAzrTlZh0drErVMWQXmGbEMOQ5IIRBuaH2ND
19pERIzt2FDKDhckVVAoX36pZrsPwUNONktRAgMBAAECggEATltLBMUXqJ1vcx/L
MY7PGKBnL/aJVWaLS1VXBK2gGQIgtvQR4UCvAbMJy3JfvWZNIivQ+8mfB2Erup6G
UIGXnD2rPNsa6xPlwwg7hSIByxqA373IHvPkul169DrJHiUJiv/bt9VHswBw/NEx
44UFPD0CkBtPbvEgDlgeSWCxhEbAcarIn2EQLHk52RoWQSPCBXm9fPnrBad9x9yw
JBp98eyNDQbF1M01O+mS/wG5H2MoNjsQJEjXdCj1nVh8x+iCGf3z9W+ZcguuASmb
irdUMDJucNrRAPKivqjK4CE7JJrMTzJg/wVkSt6CqtbEqUosFXOVIAqf81zVHl7T
vsbwcQKBgQCbuB4r2rk9vkUtk4WL7hx6IR3F8OnulnTePBAD6ze9QLLjePrBvMC0
fRh+9BSGNLx90jA4WKK3IXN39V19ETj9YClzIC5e13DMdTzLzxM1j+v6482L8yES
sLnTyUHJEp0o0TYdUUCae8Wczl8wlGAHQ5YNqJxuJoxHn0m7fVgBrQKBgQCZLm3y
Crp2MqE2xqGNn+i1wTyvf+NyZIeQ4k32EYkwG+ElHgR+Xvc94wgy4f2tHyOtN9M3
NByLe8i/Z1HV24Hyn4qhoXNO9RYa/o6oTol1ErrhzzU/H5RXphxx7ncnVTWbKCiP
fEWCdNJa5o+AzC70VuYgX72Qmg5dqtFTwa0MtQKBgEZzx9rJi7PeqQfUzcAgi+vJ
neHXfM+AbQG7JPfQ7RgH9KesLa+HyZwfWPzfS4XoyDsY5M4pc/zs+oQUlCaoqyMf
5cD7l187lAI9LLN9TdCW/Ao4FOAzsQv2vyyNyuDBNi+ocBZVk6gRRbgLOtAM4WGH
95TYa2X+tMsWy1IzhKTFAoGBAIGvwluiY50AXmbeohYiZUXD9RnsX2cQ34l1X2XO
EOTPJb1j/Y/z3MTjeqSBmmDAtVbIpaTeFLCuuxX5Zlp1vj3oftk6tEIL04xFKggq
fcvFcL0OzdjEZrYSJ5D5wJ1nUbwrsrNQFhVNzG4zNxlnRPWOwMR16isLktAQd6q+
lUDlAoGBAINHlLkHhV54YVZp7zvaCldtss+8n8OMuWsFmQFypwl0yzdYDZXc8/sE
ZYucxVxXefDVVK8MQzipncSOzQ7mR02+aHKUYhpbd2EGCywLhJoj1RyiAJRQkC+u
9DNKF3g6c/8U5AvCI6Lbh11eZnEPnLgo7vFTEhFEyBPbJUW+q6pm
-----END RSA PRIVATE KEY-----"; // 2048-bits Key


    /** RSA Get Public Key */
    public function rsa_public_key() {
        return $this->public_key;
    }

    /** RSA Encryption */
    public function rsa_encrypt($data) {
        if(openssl_public_encrypt($data, $encrypted, $this->public_key)) {
            return base64_encode($encrypted);
        }
        return false;
    }

    /** RSA Decryption */
    public function rsa_decrypt($data) {
        if(openssl_private_decrypt(base64_decode($data), $decrypted, $this->private_key)) {
            return $decrypted;
        }
        return false;
    }

    /** AES Encryption */
    public static function str_encrypt($data,$user_key) {
        try {
            $encrypt_method = "AES-256-CBC";
            $hash_key = hash('sha256', $user_key);
            $iv = substr(hash('sha256', $user_key), 0, 16);
            return base64_encode(openssl_encrypt($data, $encrypt_method, $hash_key, 0, $iv));
        } catch(Exception $e) {
            //die($e->getMessage());
            return false;
        }
    }

    /** AES Decryption */
    public static function str_decrypt($data,$user_key) {
        try {
            $encrypt_method = "AES-256-CBC";
            $hash_key = hash('sha256', $user_key);
            $iv = substr(hash('sha256', $user_key), 0, 16);
            return openssl_decrypt(base64_decode($data), $encrypt_method, $hash_key, 0, $iv);
        } catch(Exception $e) {
            //die($e->getMessage());
            return false;
        }
    }
    
    /*
        $data = "Hi";
        $encrypted = Encryption::api_encrypt($key, $iv, $data);
        $decryptedPayload = Encryption::api_decrypt($key, $encrypted);
    */

    /*
    public static function api_encrypt($key, $iv, $data, $CipherName = "aes-128-cbc", $CipherKeyLen = 16) {
        //$iv = 'fedcba9876543210'; #Same as in JAVA
        //$key = '0123456789abcdef'; #Same as in JAVA
        if (strlen($key) < $CipherKeyLen) { 
            $key = str_pad("$key", $CipherKeyLen, "0"); //0 pad to len 16
        } else if (strlen($key) > $CipherKeyLen) {
            $key = substr($str, 0, $CipherKeyLen); //truncate to 16 bytes
        }
        $encodedEncryptedData = base64_encode(openssl_encrypt($data, $CipherName, $key, OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData.";".$encodedIV;
        return $encryptedPayload;
    }

    public static function api_decrypt($key, $data, $CipherName = "aes-128-cbc", $CipherKeyLen = 16) {
        if (strlen($key) < $CipherKeyLen) {
            $key = str_pad("$key", $CipherKeyLen, "0"); //0 pad to len 16
        } else if (strlen($key) > $CipherKeyLen) {
            $key = substr($str, 0, $CipherKeyLen); //truncate to 16 bytes
        }
        $parts = explode(';', $data); //Separate Encrypted data from iv.
        $decryptedData = openssl_decrypt(base64_decode($parts[0]), $CipherName, $key, OPENSSL_RAW_DATA, base64_decode($parts[1]));
        return $decryptedData;
    }

    // Prosper API
    public static function prosper_encrypt($str, $key) {
        $data = openssl_encrypt($str, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $data = base64_encode($data);
        return bin2hex($data);
    }

    public static function prosper_decrypt($str, $key) {
        $data = openssl_decrypt(base64_decode(hex2bin($str)), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return $data;
    }

    */

}
