<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 3.0.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Database Connection */
class Database {

    /** DB Server 1 -> db_connect_master */
    private $db_master = array(
        'db_type' => 'oci', // mysql = MySQL/MariaDB, oci = Oracle, sqlsrv = Microsoft SQL
        'db_host' => '172.31.0.57', // localhost, 192.168.10.206, lcode.ddns.net
        'db_port' => '1866', // 3306 = MySQL/MariaDB, 1521 = Oracle, 1433 = Microsoft SQL, 5432 = PostgreSQL, 11521 = LCode OCI
        'db_name' => 'cbuat', // ORCL, XE // lcodeuox_savings_account
        'db_user' => 'UAT_SBPORTAL', // lcodeuox_savings
        'db_pass' => 'lcode', // lcode , lcode123 // 3YN%z4NzvJ~P
        'db_char' => 'utf8',
        'db_error' => '0'
    );

    /** Connect : DB Server 1 */
    public function db_connect_master() {
        $new_conn = $this->start_conn($this->db_master);
        if($new_conn == false) {
            die('Application connection failed, Please try again later.');
        }
        return $new_conn;
    }

    /** Connect : Multiple DB */
    public function db_connect_remote($db_type, $db_host, $db_port, $db_name, $db_user, $db_pass, $db_char = "utf8", $db_error = "1") {
        return $new_conn = $this->start_conn(array(
            'db_type' => $db_type, // oci, mysql
            'db_host' => $db_host, // localhost
            'db_port' => $db_port, // 1521, 3306
            'db_name' => $db_name, // ORCL, XE, database
            'db_user' => $db_user, // root
            'db_pass' => $db_pass,
            'db_char' => $db_char, // utf8
            'db_error' => $db_error // 1=Error, 0=NoError
        ));
    }

    /** DB Connection Method */
    private function start_conn(array $data) {
        try {

            if($data['db_type'] == "mysql") {
                //MySQL or MariaDB
                $pdo_string = "{$data['db_type']}:host={$data['db_host']};dbname={$data['db_name']};charset={$data['db_char']}";
            } elseif($data['db_type'] == "oci") {
                 //Oracle 11g
                $pdo_string = "oci:dbname=//{$data['db_host']}:{$data['db_port']}/{$data['db_name']};charset={$data['db_char']}";
            } elseif($data['db_type'] == "sqlsrv") {
                //Microsoft SQL
                $pdo_string = "sqlsrv:server={$data['db_host']},{$data['db_port']};Database={$data['db_name']};charset={$data['db_char']}";
            } elseif($data['db_type'] == "pgsql") {
                //PostgreSQL
                $pdo_string = "pgsql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_name']};charset={$data['db_char']}";
            } else {
                die('Application database not selected.');
            }

            $db_conn = new PDO($pdo_string, $data['db_user'], $data['db_pass']);
            $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // PDO to throw exceptions
            $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // PDO to throw exceptions
            $db_conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // PDO fetch method

            if($data['db_type'] != "sqlsrv") {
                $db_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Native prepare
            }

            if($data['db_type'] == "oci") {
                $datetime = $db_conn->prepare("ALTER SESSION SET NLS_DATE_FORMAT = 'yyyy-mm-dd hh24:mi:ss'");
                $datetime->execute();
            }

            return $db_conn;

        }
        catch (PDOException $e) {
            if($data['db_error'] == "1") { echo "<p>".$e->getMessage()."</p>"; }
            return false;
        }
    }

}
