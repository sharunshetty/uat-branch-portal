<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @package     : LCode PHP WebFrame
 * @version     : 3.0.0
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Application Core Class */
class LCodeWebApp extends AppData {

    private $db;

    /** Class Constructor */
    public function __construct($db_conn) { 
        $this->db = $db_conn; // DB Connection
    }

    /** SQL : Run Query */
    public function sql_run($sqlQuery,$fields = null) {
        try {
            $pdoStatement = $this->db->prepare($sqlQuery);
            if(is_array($fields)) { foreach($fields as $name => $value) { $pdoStatement->bindValue(':'.$name, $value, PDO::PARAM_STR); } }
            $pdoStatement->execute();
            return $pdoStatement;
        } catch (PDOException $e) {
            //die($e->getMessage());
            error_log(basename($_SERVER['PHP_SELF']));
            error_log("SQL: ".$sqlQuery." | DATA: ".print_r($fields, true));
            error_log($e->getMessage());
            return false;
        }
    }

    /** SQL : Fetch column value */
    public function sql_fetchcolumn($sqlQuery,$fields = null) {
        try {
            $pdoStatement = $this->db->prepare($sqlQuery);
            if(is_array($fields)) { foreach($fields as $name => $value) { $pdoStatement->bindValue(':'.$name, $value, PDO::PARAM_STR); } }
            $pdoStatement->execute();
            $result = $pdoStatement->fetchColumn();
            return $result;
        } catch (PDOException $e) {
            //die($e->getMessage());
            error_log(basename($_SERVER['PHP_SELF']));
            error_log("SQL: ".$sqlQuery." | DATA: ".print_r($fields, true));
            error_log($e->getMessage());
            return false;
        }
    }

    /** TOTAL NUMBER OF ROWS FOR SQL */
    public function sql_rowcount($tableName,$condition = null) {
        try {
            if($tableName != NULL && is_array($condition)) {
                $sqlCond = array();
                foreach ($condition as $c_key => $c_value) { $sqlCond[] = "{$c_key} = :{$c_key}"; }
                $sqlQuery = "SELECT COUNT(0) FROM {$tableName} WHERE ".implode(' AND ',$sqlCond);
                $pdoStatement = $this->db->prepare($sqlQuery);
                foreach($condition as $name => $value) { $pdoStatement->bindValue(':'.$name, $value, PDO::PARAM_STR); }
                $pdoStatement->execute();
                $result = $pdoStatement->fetchColumn(); // Row Count
                return $result;
            } else {
                //die('Invalid data received for row count.');
                return false;
            }
        } catch (PDOException $e) {
            //die($e->getMessage());
            error_log(basename($_SERVER['PHP_SELF']));
            error_log("TAB: ".$tableName." | CON: ".print_r($condition, true));
            error_log($e->getMessage());
            return false;
        }
    }

        /** SQL : Insert Data Into Table */
        public function sql_insert_data($tableName,$data) {
            try {
                if($tableName != NULL && is_array($data)) {
                    $data_fields = array_keys($data);
                    $sqlQuery = "INSERT INTO {$tableName} ( ".implode(', ', $data_fields )." ) VALUES ( :".implode(', :',$data_fields)." )";
                    $pdoStatement = $this->db->prepare($sqlQuery); // Prepare PDO statement
                    //foreach($data as $Key => $Value) { $pdoStatement->bindValue(':'.$Key, $Value, PDO::PARAM_STR); }
                    $i = 1;
                    foreach($data as $Key => $Value) { 
                        ${"variable_$i"} = $Value;
                        $pdoStatement->bindParam(":".$Key, ${"variable_$i"}, PDO::PARAM_STR, strlen(${"variable_$i"}));
                        $i++;
                    }
                    $result = $pdoStatement->execute(); // Execute SQL
                    return $result;
                } else {
                    //die('Invalid data received for insert.');
                    return false;
                }
            } catch (PDOException $e) {
                //die($e->getMessage());
                error_log(basename($_SERVER['PHP_SELF']));
                error_log("TAB: ".$tableName." | SQL: ".$sqlQuery." | DATA: ".print_r($data, true));
                error_log($e->getMessage());
                return false;
            }
        }

        /** SQL : Update Data Into Table */
        public function sql_update_data($tableName,$data,$condition) {
            if($tableName != null && is_array($data) && is_array($condition)) {
                try {
                    $sql = "UPDATE {$tableName} SET";
                    foreach ($data as $p_key => $p_value) { $sql .= " {$p_key} = :{$p_key},"; }
                    $sql = rtrim($sql,',')." WHERE ";
                    foreach($condition as $key2 => $value2) { $sql .= " {$key2} = :{$key2} AND"; }
                    $sql = rtrim($sql,"AND");
                    $pdoStatement = $this->db->prepare($sql); // Prepare PDO statement
                    $all_params = array_merge($data, $condition);
                    /*
                    foreach($all_params as $key3 => $value3) {
                        $pdoStatement->bindValue(':'.$key3, $value3, PDO::PARAM_STR);
                    }
                    */
                    
                    $i = 1;
                    foreach($all_params as $Key => $Value) { 
                        ${"variable_$i"} = $Value;
                        $pdoStatement->bindParam(":".$Key, ${"variable_$i"}, PDO::PARAM_STR, strlen(${"variable_$i"}));
                        $i++;
                    }
                    
                    $result = $pdoStatement->execute(); // Execute SQL
                    return $result;
                } catch (PDOException $e) {
                    //die($e->getMessage());
                    return false;
                }
            } else {
                //die('Invalid data received for update.');
                return false;
            }
        }


    /** SQL : Delete Data From Table */
    public function sql_delete_data($tableName,$condition) {
        try {
            if($tableName != NULL && is_array($condition)) {
                $sqlCond = array();
                foreach ($condition as $c_key => $c_value) { $sqlCond[] = "{$c_key} = :{$c_key}"; }
                $sqlQuery = "DELETE FROM {$tableName} WHERE ".implode(' AND ',$sqlCond);
                $pdoStatement = $this->db->prepare($sqlQuery); // Prepare PDO statement
                foreach($condition as $key2 => $value2) { $pdoStatement->bindValue(':'.$key2, $value2, PDO::PARAM_STR); }
                $result = $pdoStatement->execute(); // Execute SQL
                return $result;
            } else {
                //die('Invalid data received for delete.');
                return false;
            }
        } catch (PDOException $e) {
            //die($e->getMessage());
            error_log(basename($_SERVER['PHP_SELF']));
            error_log("TAB: ".$tableName." | SQL: ".$sqlQuery." | CON: ".print_r($condition, true));
            error_log($e->getMessage());
            return false;
        }
    }

    /** SQL : Transaction Start */
    public function sql_db_start() {
        try {
            $this->db->beginTransaction();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function sql_db_auditlog($log_mode,$table_name,$data,$table_pri_keys = null) {
        try {
            //Audit Module : A = New Data, M = Update, D = Delete
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /** SQL : Transaction Commit */
    public function sql_db_commit() {
        try {
            return $this->db->commit();
        } catch (PDOException $e) {
            error_log(basename($_SERVER['PHP_SELF']));
            error_log($e->getMessage());
            return false;
        }
    }

    /** SQL : Transaction Rollback */
    public function sql_db_rollback() {
        try {
            $this->db->rollback();
        } catch (PDOException $e) {
            return false;
        }
    }

    /** SQL : Get Column Value - Where field = % */
    public function getval_field($tableName,$column,$field,$value) {
        try {
            $pdoStatement = $this->db->prepare("SELECT {$column} FROM {$tableName} WHERE {$field} = :field_value");
            $pdoStatement->execute(array(':field_value' => $value));
            return $pdoStatement->fetchColumn();
        } catch (PDOException $e) {
            //die($e->getMessage());
            return false;
        }
    }

    /** SQL : Get Column Value - With Condition */
    public function getval_field_where($tableName,$column,$condition,$fields = null) {
        try {
            $sql = "SELECT {$column} FROM {$tableName} WHERE {$condition}";
            $pdoStatement = $this->db->prepare($sql); // Prepare PDO statement
            if(is_array($fields)) { foreach($fields as $key => $value) { $pdoStatement->bindValue(':'.$key, $value, PDO::PARAM_STR); } }
            $result = $pdoStatement->execute(); // Execute SQL
            return $pdoStatement->fetchColumn(); //$result;
        } catch (PDOException $e) {
            //die($e->getMessage());
            return false;
        }
    }

    /** SQL : Get Column Value - With Condition */
    public function getval_count_where($tableName,$column,$condition,$fields = null) {
        try {
            $pdoStatement = $this->db->prepare("SELECT count({$column}) FROM {$tableName} WHERE {$condition}");
            if(is_array($fields)) { foreach($fields as $name => $value) { $pdoStatement->bindValue(':'.$name, $value, PDO::PARAM_STR); } }
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn();
        } catch (PDOException $e) {
            //die($e->getMessage());
            return false;
        }
    }

    /** SQL : Data Table - Run Query : with Pagination */
    public function sql_dataTable($final_query,$fields = null,$start = null,$limit = null) {
        try {
            $db_type = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            if($db_type == "oci") {
                // Oracle
                $sqlQuery = "SELECT * FROM (SELECT ROWNUM RNUM, A.* FROM ({$final_query}) A) WHERE RNUM BETWEEN {$start} + 1 AND {$start} + {$limit}";
            } elseif($db_type == "mysql") {
                //MySQL or MariaDB
                $sqlQuery = "{$final_query} LIMIT {$start}, {$limit}";
            } else {
                $sqlQuery = $final_query; //Other
            }
            $pdoStatement = $this->db->prepare($sqlQuery);
            if($fields != null) { foreach($fields as $name => $value) { $pdoStatement->bindValue(':'.$name, $value, PDO::PARAM_STR); } }
            $pdoStatement->execute();
            return $pdoStatement;
        } catch (PDOException $e) {
            //die($e->getMessage());
            error_log(basename($_SERVER['PHP_SELF']));
            error_log("SQL: ".$sqlQuery." | DATA: ".print_r($fields, true)." | START: {$start} LIMIT: {$limit}");
            error_log($e->getMessage());
            return false;
        }
    }

    /** Pagination No. of Records */
    public function sql_dataTable_count($totalResults,$start,$limit) {
        $html_output = "";
        $db_type = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        if($totalResults > "0") {
            if($db_type == "oci") {
                // Oracle
                $start_count = $start + 1;
                $end_count = $start + $limit;
            } else {
                // MySQL or MariaDB
                $start_count = $start + 1;
                $end_count = $start + $limit;
                if($end_count > $totalResults) { $end_count = $totalResults; }
            }
            $html_output = "Showing {$start_count} to {$end_count} of {$totalResults} records.";
        }
        return $html_output;
    }

    /** Sequence Next Value */
    public function sql_sequence($SeqName, $Prefix = "", $DateAppend = true) {
        $SqlQuery = "'{$Prefix}' || ";
        $db_type = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        if($db_type == "oci") {
            $SqlQuery .= ($DateAppend == true) ? "TO_CHAR(SYSDATE,'YYYYMMDD') || " : "";
            $SqlQuery .= "{$SeqName}.NEXTVAL FROM DUAL";
        } else {
            $SqlQuery .= ($DateAppend == true) ? "DATE_FORMAT(SYSDATE(),'%Y%m%d') || " : "";
            $SqlQuery .= "NEXTVAL({$SeqName}) FROM DUAL";
        }
        return $this->sql_fetchcolumn("SELECT ".$SqlQuery); // Seq. No.
    }

    /** SQL : Fetch column MAX value for DTL SL */
    public function sql_max_value($tableName, $columnName = null, $fields = null) {
        try {
            if($tableName == NULL || $tableName == "" || $columnName == NULL || $columnName == "") {
                return false;
            }
            $db_type = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            if($db_type == "oci") {
                $sqlQuery = "SELECT NVL(MAX({$columnName}), 0) + 1 FROM {$tableName}";
            } else {
                $sqlQuery = "SELECT IFNULL(MAX({$columnName}), 0) + 1 FROM {$tableName}";
            }
            if(is_array($fields) && count($fields) > "0") {
                $extraSql = array();
                $list = array_keys($fields);
                foreach($list as $list_column) {
                    $extraSql[] = "{$list_column} = :{$list_column}";
                }
                $sqlQuery .= " WHERE ".implode(" AND ", $extraSql);
            }
            $pdoStatement = $this->db->prepare($sqlQuery);
            if(is_array($fields)) { foreach($fields as $name => $value) { $pdoStatement->bindValue(':'.$name, $value, PDO::PARAM_STR); } }
            $pdoStatement->execute();
            $result = $pdoStatement->fetchColumn();
            return $result;
        } catch (PDOException $e) {
            //die($e->getMessage());
            error_log(basename($_SERVER['PHP_SELF']));
            error_log("SQL: ".$sqlQuery." | DATA: ".print_r($fields, true));
            error_log($e->getMessage());
            return false;
        }
    }

    /** Class Destructor */
    public function __destruct() {
        $this->db = null; // DB Connection Closing
    }

}
