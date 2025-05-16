<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 1.0.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** TBA2 Auth - Maker &  */
class tba2Auth {

    private $tba2_ref_num;

    public function __construct() {
        $tba2_ref_num = NULL;
    }

    /** Insert TBA2 Primary Record - Index */
    public function setAuthIndex($pgmCode, $opFlg, $tableName, $priKeys) {

        global $main_app;

        //Check pending
        $dedup_array = array(
            "TBA2_ENTITY_NUM" => '1',
            "TBA2_PGM_ID" => $pgmCode,
            "TBA2_OPERN_FLG" => $opFlg,
            "TBA2_MAIN_TABLE" => $tableName,
            "TBA2_PRI_KEYS" => implode("|",array_values($priKeys)),
            "TBA2_AUTH_STATUS" => "P"
        );
        
        $total_results = $main_app->sql_fetchcolumn("SELECT COUNT(0) FROM TBA2_AUTH WHERE TBA2_ENTITY_NUM = :TBA2_ENTITY_NUM AND TBA2_PGM_ID = :TBA2_PGM_ID AND TBA2_OPERN_FLG = :TBA2_OPERN_FLG AND TBA2_MAIN_TABLE = :TBA2_MAIN_TABLE AND TBA2_PRI_KEYS = : TBA2_PRI_KEYS AND TBA2_AUTH_STATUS = :TBA2_AUTH_STATUS", $dedup_array);

        if($total_results && $total_results > "0") {
            throw new Exception('Similar record already pending for Authorization');
        }

        //Start
        $this->tba2_ref_num = $main_app->sql_fetchcolumn("SELECT TO_CHAR(SYSDATE,'YYYYMMDD') || TBA2_SEQ.NEXTVAL FROM DUAL");

        if($this->tba2_ref_num == false || $this->tba2_ref_num == NULL || $this->tba2_ref_num == "" || $this->tba2_ref_num == "1") {
            throw new Exception('Unable to generate TBA2 reference number');
        }
        elseif(!isset($pgmCode) || $pgmCode == null || $pgmCode == "") {
            throw new Exception('Invalid program code');
        }
        elseif(!isset($opFlg) || $opFlg == null || ($opFlg != "A" && $opFlg != "M")) {
            throw new Exception('Invalid operation flag');
        }
        elseif(!isset($tableName) || $tableName == null || $tableName == "") {
            throw new Exception('Invalid table name');
        }
        elseif(!isset($priKeys) || $priKeys == null || $priKeys == "" || !is_array($priKeys) || count($priKeys) < "1") {
            throw new Exception('Invalid primary keys');
        }
        else {

            $data = array();
            $data['TBA2_ENTITY_NUM'] = "1";
            $data['TBA2_REF_NUM'] = $this->tba2_ref_num;
            $data['TBA2_PGM_ID'] = $pgmCode;
            $data['TBA2_OPERN_FLG'] = $opFlg;
            $data['TBA2_AUTH_STATUS'] = "P";
            $data['TBA2_MAIN_TABLE'] = $tableName;
            $data['TBA2_PRI_KEYS'] = implode("|",array_values($priKeys));
            //$data['TBA2_BRN_CODE'] = isset($_SESSION['USER_BRN_CODE']) ? $_SESSION['USER_BRN_CODE'] : NULL;
            //$data['TBA2_USR_ROLE'] = isset($_SESSION['USER_ROLE_CODE']) ? $_SESSION['USER_ROLE_CODE'] : NULL;
            $data['CR_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : NULL;
            $data['CR_ON'] = date("Y-m-d H:i:s");
            $db_output = $main_app->sql_insert_data("TBA2_AUTH",$data); // Insert
            return ($db_output == false) ? false : true;
            
        }

    }

    /** Insert TBA2 Records */
    public function pushRecordEntry($pgmCode, $opFlg, $recordTableName = null, $recordPriKeys = null, $recordData = null) {

        global $main_app;

        if($this->tba2_ref_num == NULL) {
            throw new Exception('TBA2 reference number not available');
        }
        elseif(!isset($pgmCode) || $pgmCode == null || $pgmCode == "") {
            throw new Exception('Invalid program code');
        }
        elseif(!isset($opFlg) || $opFlg == null || ($opFlg != "A" && $opFlg != "M" && $opFlg != "R")) {
            throw new Exception('Invalid operation flag');
        } 
        elseif(!isset($recordTableName) || $recordTableName == null || $recordTableName == "") {
            throw new Exception('Invalid table name');
        }
        elseif($opFlg != "A" && (!isset($recordPriKeys) || $recordPriKeys == null || $recordPriKeys == "" || !is_array($recordPriKeys) || count($recordPriKeys) < "1")) {
            throw new Exception('Invalid primary keys');
        }
        elseif(!isset($recordData) || $recordData == null || $recordData == "" || !is_array($recordData) || count($recordData) < "1") {
            throw new Exception('Invalid primary keys');
        }
        else {

            $data = array();
            $data['TBA2_ENTITY_NUM'] = "1";
            $data['TBA2_REF_NUM'] = $this->tba2_ref_num;
            $data['TBA2_DTL_SL'] = $main_app->sql_fetchcolumn("SELECT NVL(MAX(TBA2_DTL_SL), 0) + 1 FROM TBA2_AUTH_DTL WHERE TBA2_REF_NUM = :TBA2_REF_NUM", array('TBA2_REF_NUM' => $this->tba2_ref_num)); // Max Sl. No.
            $data['TBA2_PGM_ID'] = $pgmCode;
            $data['TBA2_OPERN_FLG'] = $opFlg;
            $data['TBA2_TABLE_NAME'] = $recordTableName;
            $data['TBA2_TABLE_PKEYS'] = json_encode($recordPriKeys, JSON_UNESCAPED_SLASHES);
            $data['TBA2_TABLE_DATA'] = json_encode($recordData, JSON_UNESCAPED_SLASHES);
            $db_output = $main_app->sql_insert_data("TBA2_AUTH_DTL",$data); // Insert
            return ($db_output == false) ? false : true;

        }

    }

    /** Accept/Authorize TBA2 Entry & Move to Actual Tables */
    public function authorizeRecord($authRefNum) {

        global $main_app;
        $check_duplicate = array();

        $sql_exe = $main_app->sql_run("SELECT * FROM TBA2_AUTH_DTL WHERE TBA2_ENTITY_NUM = '1' AND TBA2_REF_NUM = :TBA2_REF_NUM", array('TBA2_REF_NUM' => $authRefNum));
        while ($row = $sql_exe->fetch()) {

            $json_data = json_decode(stream_get_contents($row['TBA2_TABLE_DATA']), true);

            if(isset($row['TBA2_OPERN_FLG']) && $row['TBA2_OPERN_FLG'] == "A") {

                //Add
                $db_output = $main_app->sql_insert_data($row['TBA2_TABLE_NAME'], $json_data); // Insert
                if($db_output == false) { 
                    throw new Exception('Process failed. Ref: '.$row['TBA2_DTL_SL']);
                }

            }
            else if(isset($row['TBA2_OPERN_FLG']) && $row['TBA2_OPERN_FLG'] == "M") {

                //Update
                $pri_keys = json_decode(stream_get_contents($row['TBA2_TABLE_PKEYS']), true);
                $db_output = $main_app->sql_update_data($row['TBA2_TABLE_NAME'], $json_data, $pri_keys); // Update
                if($db_output == false) { 
                    throw new Exception('Process failed. Ref: '.$row['TBA2_DTL_SL']);
                }

            }
            else if(isset($row['TBA2_OPERN_FLG']) && $row['TBA2_OPERN_FLG'] == "R") {

                //Replace
                if(!in_array($row['TBA2_TABLE_NAME'], $check_duplicate)) {
                    $check_duplicate[] = $row['TBA2_TABLE_NAME'];
                    $pri_keys = json_decode(stream_get_contents($row['TBA2_TABLE_PKEYS']), true);
                    $db_output = $main_app->sql_delete_data($row['TBA2_TABLE_NAME'],$pri_keys); // Delete
                    if($db_output == false) {
                        throw new Exception('Process failed. Ref: '.$row['TBA2_DTL_SL']);
                    }
                }

                $db_output2 = $main_app->sql_insert_data($row['TBA2_TABLE_NAME'], $json_data); // Insert
                if($db_output2 == false) {
                    throw new Exception('Process failed. Ref: '.$row['TBA2_DTL_SL']);
                }

            }

        }

    }

}