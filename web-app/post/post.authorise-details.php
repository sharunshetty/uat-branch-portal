<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Get Data */
if (isset($_POST['id']) && $_POST['id'] != NULL) {
    $primary_value = $safe->str_decrypt($_POST['id'], $_SESSION['SAFE_KEY']);
}

// $page_primary_keys = array(
//     'USER_ID' => (isset($primary_value)) ? $main_app->strsafe_input($primary_value) : "",
// );

// if(isset($primary_value) && $primary_value != "") {
//     $sql_user = $main_app->sql_run("SELECT * FROM ASSREQ_USER_ACCOUNTS WHERE USER_ID = :USER_ID",$page_primary_keys);
//     $item_user = $sql_user->fetch();
// }

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";


// Start : Review
if(!isset($_POST['AUTH_STATUS']) || $_POST['AUTH_STATUS'] == NULL) {
    echo "<script> focus('AUTH_STATUS'); swal.fire('','Select status'); loader_stop(); enable('sbt'); </script>";
    exit();
}
// elseif(!isset($_POST['REMARKS']) || $main_app->valid_text($_POST['REMARKS']) == false || $main_app->wordlen($_POST['REMARKS']) < "2") {
//     echo "<script> focus('REMARKS'); swal.fire('','Enter remarks'); loader_stop(); enable('sbt'); </script>";
// }

else {

    $updated_flag = true;
    $sql1_exe = $main_app->sql_run("SELECT *  FROM {$page_table_name} WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $primary_value ));
    $item_data = $sql1_exe->fetch();

    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R01)'); loader_stop(); enable('sbt'); </script>";
        exit();
    }
    elseif(isset($_SESSION['USER_ID']) && $_SESSION['USER_ROLE'] != 'ADMIN' && ($_SESSION['USER_ID'] ==  $item_data['CR_BY'])) {
        echo "<script> swal.fire('','Same Entry user cannot authorise the data'); loader_stop(); enable('sbt'); </script>";
        exit();
    }
    

    $main_app->sql_db_start(); // Start - DB Transaction
    $sys_datetime = date("Y-m-d H:i:s");

    $data = array();
   
    if(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "AR") {

        $data['APP_STATUS'] = "S";
        $data['AUTH_STATUS'] = $_POST['AUTH_STATUS'];
        $data['AUTH_REMARKS'] = $_POST['REMARKS'];
        $data['REJ_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : '';
        $data['REJ_ON'] = $sys_datetime;

        $db_output = $main_app->sql_update_data("$page_table_name", $data, array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'])); // Update
        if($db_output == false) { $updated_flag = false; }

    } elseif(isset($_POST['AUTH_STATUS']) && $_POST['AUTH_STATUS'] == "AS") {

        if(isset($item_data['ASSREQ_EKYC_UID']) && $item_data['ASSREQ_EKYC_UID'] != "") {
            $aadhaarNo = $safe->str_decrypt($item_data['ASSREQ_EKYC_UID'], $item_data['ASSREQ_REF_NUM']);
	        //$aadhaarNo="436545406119";
        }
    
        if(isset($item_data['ASSREQ_PAN_CARD']) && $item_data['ASSREQ_PAN_CARD'] != "") {
           $panNo = $safe->str_decrypt($item_data['ASSREQ_PAN_CARD'], $item_data['ASSREQ_REF_NUM']);
           // $panNo="AUVPS1179J";
        }
       
        $pid_data = array(array('PID_TYPE' => "UID", 'PID_NUM' => $aadhaarNo, 'CARD_NUM' => '', 'DATE_OF_ISSUE' => '', 'PLACE_OF_ISSUE' => '', 'ISSUING_AUTH' => 'GOI', 'ISSUING_COUNTRY' => '', 'EXPIRY_DATE' => '', 'USEDOF_ADDRESS_PF' => '1', 'USEDOF_IDENTITY_CHK' => '1' ),
        array('PID_TYPE' => "PAN", 'PID_NUM' => $panNo, 'CARD_NUM' => '', 'DATE_OF_ISSUE' => '', 'PLACE_OF_ISSUE' => '', 'ISSUING_AUTH' => 'GOI', 'ISSUING_COUNTRY' => '', 'EXPIRY_DATE' => '', 'USEDOF_ADDRESS_PF' => '0', 'USEDOF_IDENTITY_CHK' => '1' ));
        
	    //if(!empty($dl_array)) {
            //array_push($pid_data, $dl_array);
        //}

        //json array of pid_list
        $pid_data_array = json_encode($pid_data, true);

        $sql_exe3 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM']));
        $ekyc_docsdata = $sql_exe3->fetch();

        if(!isset($ekyc_docsdata['DOC_DATA']) && $ekyc_docsdata['DOC_DATA'] == "") {
            echo "<script> swal.fire('','Unable to validate your request..'); loader_stop(); enable('sbt'); </script>";
            exit();    
        }

        if(isset($ekyc_docsdata['DOC_DATA']) && $ekyc_docsdata['DOC_DATA'] != "") {
            $aadhaardetails = json_decode(stream_get_contents($ekyc_docsdata['DOC_DATA']), true, JSON_UNESCAPED_SLASHES);
           // $aadhaardetails = json_decode($ekyc_docsdata['DOC_DATA'], true);
        }


        // if(!isset($aadhaardetails['name']) || isset($aadhaardetails['name']) == NULL || isset($aadhaardetails['name']) == "") {
        //     echo "<script> swal.fire('','Name from Aadhar is Blanked'); loader_stop(); enable('sbt'); </script>";
        // } elseif(!isset($aadhaardetails['fatherName']) || isset($aadhaardetails['fatherName']) == NULL || isset($aadhaardetails['fatherName']) == "") {
        //     echo "<script> swal.fire('','Father Name from Aadhar is Blanked'); loader_stop(); enable('sbt'); </script>";
        // }elseif(!isset($aadhaardetails['district']) || isset($aadhaardetails['district']) == NULL || isset($aadhaardetails['district']) == "") {
        //     echo "<script> swal.fire('','District Name from Aadhar is Blanked'); loader_stop(); enable('sbt'); </script>";
        // }

       
        // if(isset($aadhaardetails['name']) && $aadhaardetails['name'] != "") {
        //     $name = explode(' ', $aadhaardetails['name']);
        //     $first_name = $name[0];
        //     $middle_name = isset($name[1]) ? $name[1] : NULL ;
        //     $last_name = isset($name[2]) ? $name[2] : NULL;
        // }
    
        //get pan data
        // $sql_exe_pan = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'PAN' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM']));
        // $pan_docsdata = $sql_exe_pan->fetch();

        // if(isset($pan_docsdata['DOC_DATA']) && $pan_docsdata['DOC_DATA'] != "") {
        //     $pan_details = json_decode(stream_get_contents($pan_docsdata['DOC_DATA']), true, JSON_UNESCAPED_SLASHES);
        // }

        // if(isset($pan_details['firstName']) && $pan_details['firstName'] != "") {
        //     $first_name = $pan_details['firstName'];
        // }

        // if(isset($pan_details['midName']) && $pan_details['midName'] != "") { 
        //     $middle_name = $pan_details['midName'];
        // }

        // if(isset($pan_details['lastName']) && $pan_details['lastName'] != "") { 
        //     $last_name = $pan_details['lastName'];
        // }

        // //take last name from pan response if first name is not present(rare case)
        // if(isset($pan_details['firstName']) && $pan_details['firstName'] == "") {
        //     if(isset($pan_details['lastName']) && $pan_details['lastName'] != "") {
        //         $first_name = $pan_details['lastName'];
        //         $last_name = "";
        //     }
        // }

          //calculate age
        $dob = $aadhaardetails['dob'];
        $diff = (date('Y') - date('Y',strtotime($dob)));
        if($diff >= "60") {
           // $ac_sub_type = "03";
            $it_status_code = "IS";
        } else {
           // $ac_sub_type = "01";
            $it_status_code = "I";
        }
        
        $dob = trim($aadhaardetails['dob']);

        //implode dob aadhaar
        if(isset($aadhaardetails['dob']) && $aadhaardetails['dob'] != ""){
            $dobaadhaar = date('d-m-Y',strtotime($aadhaardetails['dob']));
            $birth_date = explode('-', $dobaadhaar);
            $dateofbirth = $birth_date[0].''.$birth_date[1].''.$birth_date[2];
        }
       
        //Fetch all basic details of customer
       // $sql_exe4 = $main_app->sql_run("SELECT * FROM ASSREQ_ACCOUNTDATA WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM ORDER BY MO_ON DESC", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM']));
        $sql_exe4 = $main_app->sql_run("SELECT ASSREQ_REF_NUM,ASSREQ_EDIT_ADDRESS,ASSREQ_PRODUCT_CODE,ASSREQ_ACNT_SUBTYP,ASSREQ_BRANCH_CODE,ASSREQ_RELIGION_CODE,ASSREQ_WEAKER_CODE,ASSREQ_PLACE_OF_BIRTH,ASSREQ_OCCUPATION_CODE,ASSREQ_CITY_CODE,ASSREQ_STATE_CODE,ASSREQ_ANNUAL_INCOME,
        ASSREQ_CUST_FIRST_NAME,ASSREQ_CUST_MIDDLE_NAME,ASSREQ_CUST_LAST_NAME,ASSREQ_FATHERSNAME,ASSREQ_MARITAL_STATUS,ASSREQ_SPOUSE_NAME,ASSREQ_NOMINEE_NAME,ASSREQ_NOMINEE_DOB,ASSREQ_NOMINEE_RELATION,
        ASSREQ_NOMINEE_ADDRESS,ASSREQ_NOMINEE_GUARDIAN,ASSREQ_GUARDIAN_NATURE FROM ASSREQ_ACCOUNTDATA WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM ORDER BY MO_ON DESC", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM']));
       
        $cust_details = $sql_exe4->fetch(); 
        
        if(!isset($cust_details['ASSREQ_REF_NUM']) || $cust_details['ASSREQ_REF_NUM'] == NULL || $cust_details['ASSREQ_REF_NUM'] == "") {
            echo "<script> swal.fire('','Unable to validate your Request.'); loader_stop(); enable('sbt'); </script>";
            exit();
        }


        //date of birth conversion to format like 09-Mar-1998

        //$date= date_create($aadhaardetails['dob']);    
       // $dob_aadhaar = date_format($date,"d-M-Y");
       
       // $splitAddress = $aadhaardetails['street'] . ", " . $aadhaardetails['district'] . ", " . $aadhaardetails['vtcName'] . ", " . $aadhaardetails['postOffice'] . "," . $aadhaardetails['state'];

        /*
        
        $sql_exeq = $main_app->sql_run("select  
        wm_concat(Distinct cic.indclient_code||' | '||cc.clients_home_brn_code ||' | '||trim(cc.clients_name)||' | '||to_char(cic.indclient_birth_date, 'DD-Mon-YYYY')||' | '||cic.indclient_father_name||chr(10)) dup_client
        from cbuat.indclients cic 
        Join cbuat.clients cc on cc.clients_code = cic.indclient_code
        Left Join cbuat.clntmergseldtl cmc on cmc.cmseld_entity_num=1 and cmc.cmseld_existing_clientnum = cic.indclient_code
        where cmc.cmseld_merge_sl is NULL 
        and Upper(Replace(cc.clients_name,' ','')) = Upper(replace('{$aadhaardetails['name']}',' ','')) 
        and Upper(Replace(cic.indclient_father_name,' ','')) = Upper(replace('{$aadhaardetails['fatherName']}',' ','')) 
        and utl_match.edit_distance_similarity(Upper(Replace(cc.clients_addr1||cc.clients_addr2||cc.clients_addr3||cc.clients_addr4||cc.clients_addr5,' ','')), 
        Upper(replace('{$aadhaardetails['district']}',' ',''))) >= 50");

        $dedupdata = $sql_exeq->fetch();

        if(isset($dedupdata) && ($dedupdata['DUP_CLIENT'] != "" || $dedupdata['DUP_CLIENT'] != NULL)) {
            echo "<script> swal.fire('','Details are already present in Bank, Kindly visit nearest Branch'); loader_stop(); enable('sbt'); </script>";
            exit();
        }
        
        */


        //DEDUP FOR VALIDATING IF ACCOUNT IS PRESENT IN CBS 
       
       /* $ErrorMsg = "";
    
        $send_data = array();
        $send_data['METHOD_NAME'] = "dbLinkRespData";
        $send_data['REQUEST_FOR'] = "DEDUP";
        // $send_data['PAN_NUMBER'] = "";
        // $send_data['AADHAAR_NUMBER'] = "";
        $send_data['AADHAAR_NAME'] = $aadhaardetails['name'];
        $send_data['FATHER_NAME'] = $aadhaardetails['fatherName'];
        $send_data['DISTRICT'] = $aadhaardetails['district'];

        try {
            $apiConn = new ReachMobApi;
            // $output = $apiConn->ReachMobConnect($send_data, "60");
            $output = json_decode('{"dataAvailable":"Y","responseCode":"S"}', true);

        } catch(Exception $e) {  
            error_log($e->getMessage());
            $ErrorMsg = "Technical Error, Please try later"; //Error from Class    
        }

        if(!isset($ErrorMsg) || $ErrorMsg == "") {
    
            if(!isset($output['responseCode']) || !isset($output['dataAvailable']) || $output['responseCode'] == "" || $output['dataAvailable'] == "") {// || ($output['responseCode'] != "S" && $output['dataAvailable'] != "Y")
                $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
            }
        
            if($output['responseCode'] == "S" && $output['dataAvailable'] == "Y") {
                $ErrorMsg = "Details are already present in Bank, Kindly visit nearest Branch";
            }
            
        
            // if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
            //    $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
            // }
        }

        if(isset($ErrorMsg) && $ErrorMsg != "") {
            echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
            exit();
        }*/
        

        
        //take address for sending address 1 in ISO
        // if(isset($aadhaardetails['houseNumber']) && $aadhaardetails['houseNumber'] != "") {
        //     $address1 = $aadhaardetails['houseNumber'];
        // } elseif(isset($aadhaardetails['postOffice']) && $aadhaardetails['postOffice'] != "") {
        //     $address1 = $aadhaardetails['postOffice'];
        // } elseif(isset($aadhaardetails['vtcName']) && $aadhaardetails['vtcName'] != ""){
        //     $address1 = $aadhaardetails['vtcName'];
        // } 

      
        //take address from aadhaar response for sending address 1 in ISO
        if(isset($aadhaardetails['houseNumber']) && $aadhaardetails['houseNumber'] != "" && $aadhaardetails['houseNumber'] != " " && $aadhaardetails['houseNumber'] != " " && $aadhaardetails['houseNumber'] != "null" && $aadhaardetails['houseNumber'] != "NULL"){
            $address1 = $aadhaardetails['houseNumber'];
        } else {
            if(isset($aadhaardetails['postOffice']) && $aadhaardetails['postOffice'] != "" && $aadhaardetails['postOffice'] != " " && $aadhaardetails['postOffice'] != "null" && $aadhaardetails['postOffice'] != "NULL") {
                $address1 = $aadhaardetails['postOffice'];
            } else {
                if(isset($aadhaardetails['vtcName']) && $aadhaardetails['vtcName'] != "" && $aadhaardetails['vtcName'] != " " && $aadhaardetails['vtcName'] != "null" && $aadhaardetails['vtcName'] != "NULL") {
                    $address1 = $aadhaardetails['vtcName'];
                }
                else {
                    if(isset($aadhaardetails['subdistrict']) && $aadhaardetails['subdistrict'] != "" && $aadhaardetails['subdistrict'] != " " && $aadhaardetails['subdistrict'] != "null" && $aadhaardetails['subdistrict'] != "NULL") {
                        $address1 = $aadhaardetails['subdistrict'];
                    }
                    else {
                        if(isset($aadhaardetails['district']) && $aadhaardetails['district'] != "" && $aadhaardetails['district'] != " " && $aadhaardetails['district'] != "null" && $aadhaardetails['district'] != "NULL") {
                            $address1 = $aadhaardetails['district'];

                        }
                    }
                }
            }
        }

        //trim address1 if length is greater than 35( For virtual debit card generation)
        if(isset($address1) && $address1 != "") {
            $addresslength = strlen($address1);
            if($addresslength > 35) {
                $Address1 = substr($address1, 0, 35);
            } else {
                $Address1  = $address1;
            }
        }

        //trim address2 if length is greater than 35( For virtual debit card generation)
        if(isset($aadhaardetails['street']) && $aadhaardetails['street'] != "" && $aadhaardetails['street'] != "null") {
            $addresslength = strlen($aadhaardetails['street']);
            if($addresslength > 35) {
                $Address2 = substr($aadhaardetails['street'], 0, 35);
            } else {
                $Address2  = $aadhaardetails['street'];
            }
        }

        //trim address3 if length is greater than 35( For virtual debit card generation)

        if(isset($aadhaardetails['district']) && $aadhaardetails['district'] != "" && $aadhaardetails['district'] != "null") {
            $addresslength = strlen($aadhaardetails['district']);
            if($addresslength > 35) {
                $Address3 = substr($aadhaardetails['district'], 0, 35);
            } else {
                $Address3  = $aadhaardetails['district'];
            }
        }

        if(isset($aadhaardetails['state']) && $aadhaardetails['state'] != "" && $aadhaardetails['state'] != "null") {  
            $state  = $aadhaardetails['state'];
        }

        if(isset($aadhaardetails['pincode']) && $aadhaardetails['pincode'] != "" && $aadhaardetails['pincode'] != "null") {  
            $pincode  = $aadhaardetails['pincode'];
        }


        //fetching  editable address in table
        $editaddress = json_decode($cust_details['ASSREQ_EDIT_ADDRESS'], true);
        $EHOUSENUMBER =  isset($editaddress['HOUSENUMBER']) ? substr($editaddress['HOUSENUMBER'], 0, 35) : "";
        $ESTREETADDRESS = isset($editaddress['STREETADDRESS']) ? substr($editaddress['STREETADDRESS'], 0, 35) : "";
        $EDISTRICT = isset($editaddress['DISTRICT']) ? substr($editaddress['DISTRICT'], 0, 35) : "";
        $ESTATE = isset($editaddress['STATE']) ? $editaddress['STATE'] : "";
        $EPINCODE = isset($editaddress['PINCODE']) ? $editaddress['PINCODE'] : "";


        //trim address4 for state if length is greater than 25( For virtual debit card generation)
        /*if(isset($aadhaardetails['state']) && $aadhaardetails['state'] != "" && $aadhaardetails['state'] != "null") {
            $addresslength = strlen($aadhaardetails['state']);
            if($addresslength > 25) {
                $Address4 = substr($aadhaardetails['state'], 0, 25);
            } else {
                $Address4  = $aadhaardetails['state'];
            }
        }*/



        //$accountdetail = $main_app->sql_run("SELECT CBS_USER_ID, USERCRE_RESP_CODE, CBS_ACNT_NUMBER, ACCOUNT_RESP_CODE FROM PORTALONLINEACCOPEN WHERE ENTITY_NUM = '1' AND APP_NUMBER = :APP_NUMBER", array( 'APP_NUMBER' => $item_data['ASSREQ_REF_NUM']));
       // $item_data_ac = $accountdetail->fetch();
    
        $accountdetail = $main_app->sql_run("SELECT CBS_USER_ID, USERCRE_RESP_CODE, CBS_ACNT_NUMBER, ACCOUNT_RESP_CODE FROM PORTALONLINEACCOPEN WHERE ENTITY_NUM = '1' AND APP_NUMBER = :APP_NUMBER", array( 'APP_NUMBER' => $item_data['ASSREQ_REF_NUM']));
        //$item_data_ac = $accountdetail->fetch();

        if(isset($item_data_ac) && $item_data_ac != "" && $item_data_ac['USERCRE_RESP_CODE'] == "S" && $item_data_ac['ACCOUNT_RESP_CODE'] == "S") {
    
            $output['responseCode'] = "S";
            $output['userId'] = $item_data_ac['CBS_USER_ID'];
            $output['acntNumber'] = $item_data_ac['CBS_ACNT_NUMBER'];

            $output['userResp'] = $item_data_ac['USERCRE_RESP_CODE'];
            $output['accResp'] = $item_data_ac['ACCOUNT_RESP_CODE'];
        
        } else {

            //UPDATING AU_BY BEFORE createAccount API BECAUSE IN API PASSING AU_BY value through bank form name displaying 
           // $data = array();
           // $data['AU_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : '';   
          //  $main_app->sql_db_auditlog('A',$page_table_name, $data); // Audit Log - DB Transaction
           // $db_output = $main_app->sql_update_data("$page_table_name", $data, array( 'ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'])); // Update
          
            // Account Opening Verify 
            $send_data['METHOD_NAME'] = "createAccount"; 
            $send_data['PRODUCT_CODE'] = $cust_details['ASSREQ_PRODUCT_CODE'];
            $send_data['ACC_TYPE'] = $main_app->getval_field('SBREQ_ACCOUNT_TYPE','ACCOUNT_TYPE_CODE','ACCOUNT_TYPE_PROD',$cust_details['ASSREQ_PRODUCT_CODE']);
            //$send_data['ACC_SUB_TYPE'] = $ac_sub_type;
            $send_data['ACC_SUB_TYPE'] = $cust_details['ASSREQ_ACNT_SUBTYP'];
            $send_data['BRANCH_CODE'] = $cust_details['ASSREQ_BRANCH_CODE'];
            $send_data['TITLE_CODE'] = $item_data['ASSREQ_CUST_TITLE'];
            $send_data['CONST_CODE'] = "1";
            //$send_data['RELIGION_CODE'] = "99";
            $send_data['RELIGION_CODE'] = $cust_details['ASSREQ_RELIGION_CODE'];
            $send_data['NATIONALITY_CODE'] = "IN";
            // $send_data['WEAKER_SEC_CODE'] = "99";
            $send_data['WEAKER_SEC_CODE'] =  isset($cust_details['ASSREQ_WEAKER_CODE']) ? $cust_details['ASSREQ_WEAKER_CODE'] : "";
            $send_data['CUST_CATCODE'] = "1";
            $send_data['CUST_SUB_CATCODE'] = "0"; //to be shared by bank
            $send_data['CUST_SEGMENT_CODE'] = "99999";
            $send_data['BUSINESS_DIVCODE'] = "99"; //change during live
            $send_data['POB_LOC_CODE'] = $cust_details['ASSREQ_PLACE_OF_BIRTH'];
            $send_data['LANGUAGE_CODE'] = "04";
            $send_data['OCCUPATION_CODE'] = $cust_details['ASSREQ_OCCUPATION_CODE'];
            $send_data['COMPANY_CODE'] = "OTHERS";
            $send_data['DESIGN_CODE'] = "";
            $send_data['CITY_CODE'] = $cust_details['ASSREQ_CITY_CODE']; //change
            $send_data['COUNTRY_CODE'] = "0091";
            $send_data['STATE_CODE'] = $cust_details['ASSREQ_STATE_CODE']; //change
            $send_data['ANNUAL_INSLAB'] = $cust_details['ASSREQ_ANNUAL_INCOME'];
            $send_data['IT_STATUS_CODE'] = $it_status_code; //I / IS
            $send_data['IT_SUBSTATUS_CODE'] = "0"; //to be shared by bank
            $send_data['CUST_ARM_CODE'] = "1";
            $send_data['TYPEOF_CUST'] = "R";
            $send_data['RISK_CAT'] = "3"; //to be clarified bank
            $send_data['TYPEOF_ACCOMODATION'] = "6";
            $send_data['INSUPOLICY_INFO'] = "2"; 
            $send_data['CURRENCY_CODE'] = "INR"; 
            $send_data['ANNUAL_INCOME'] = "100000";
            $send_data['TAX_TIN_NUMBER'] = "0";
            // $send_data['FIRST_NAME'] = $first_name;
            // $send_data['MID_NAME'] = isset($middle_name) ? $middle_name : NULL;
            // $send_data['LAST_NAME'] = isset($last_name) ? $last_name : NULL;

            $send_data['FIRST_NAME'] =  isset($cust_details['ASSREQ_CUST_FIRST_NAME']) ? $cust_details['ASSREQ_CUST_FIRST_NAME'] : "";       
            $send_data['MID_NAME'] =  isset($cust_details['ASSREQ_CUST_MIDDLE_NAME']) ? $cust_details['ASSREQ_CUST_MIDDLE_NAME'] : ""; 
            $send_data['LAST_NAME'] =  isset($cust_details['ASSREQ_CUST_LAST_NAME']) ? $cust_details['ASSREQ_CUST_LAST_NAME'] : "";
           
            // $send_data['FATHER_NAME'] = isset($aadhaardetails['fatherName']) ? $aadhaardetails['fatherName'] : NULL ;
            $send_data['FATHER_NAME'] = isset($cust_details['ASSREQ_FATHERSNAME']) ? $cust_details['ASSREQ_FATHERSNAME'] : $aadhaardetails['fatherName'];
            
            $send_data['DOB'] = $dateofbirth;
            $send_data['GENDER'] = isset($aadhaardetails['gender']) ? $aadhaardetails['gender'] : NULL ;
            $send_data['UNDER_POVERTY'] = "";
            $send_data['RESIDENT_STATUS'] = "R";
            $send_data['MARITAL_STATUS'] = $cust_details['ASSREQ_MARITAL_STATUS'];
            $send_data['BANK_RELATION'] = "N";
            $send_data['EMPNO'] = "";
            $send_data['COMPANY_NAME'] = "";

            // $send_data['ADDRESS1'] = isset($aadhaardetails['houseNumber']) ? $aadhaardetails['houseNumber'] : NULL ;
            // $send_data['ADDRESS1'] = isset($address1) ? $address1 : $EHOUSENUMBER;
            // $send_data['ADDRESS2'] = isset($Address2) ? $Address2 : $ESTREETADDRESS;
            // $send_data['ADDRESS3'] = isset($Address3) ? $Address3 : $EDISTRICT;
            // $send_data['ADDRESS4'] = isset($state) ? $state : $ESTATE;
            // $send_data['ADDRESS5'] = isset($pincode) ? $pincode : $EPINCODE;

            $send_data['ADDRESS1'] = isset($EHOUSENUMBER) ? $EHOUSENUMBER : $Address1;
            $send_data['ADDRESS2'] = isset($ESTREETADDRESS) ? $ESTREETADDRESS : $Address2;
            $send_data['ADDRESS3'] = isset($EDISTRICT) ? $EDISTRICT : $Address3;
            $send_data['ADDRESS4'] = isset($ESTATE) ? $ESTATE : $state;
            $send_data['ADDRESS5'] = isset($EPINCODE) ? $EPINCODE : $pincode;
           
            //substr($EHOUSENUMBER,0,36)
            //substr($Address1,0,36)

            // $send_data['ADDRESS1'] = $address1;
            // $send_data['ADDRESS2'] = isset($Address2) ? $Address2 : NULL ;
            // $send_data['ADDRESS3'] = isset($Address3) ? $Address3 : NULL ;
            // $send_data['ADDRESS4'] = isset($state) ? $state : NULL ;
            // $send_data['ADDRESS5'] = isset($pincode) ? $pincode : NULL ;


            //$send_data['ADDRESS4'] = isset($aadhaardetails['state']) ? $aadhaardetails['state'] : NULL ;
           // $send_data['ADDRESS5'] = isset($aadhaardetails['pincode']) ? $aadhaardetails['pincode'] : NULL ;


            // $send_data['ADDRESS2'] = isset($aadhaardetails['street']) ? $aadhaardetails['street'] : NULL ;
            // $send_data['ADDRESS3'] = isset($aadhaardetails['district']) ? $aadhaardetails['district'] : NULL ;
            $send_data['POSTBOX_NUM'] = "";
            $send_data['MOBILE_NUMBER'] = $item_data['ASSREQ_MOBILE_NUM'];
            $send_data['EMIAL_ID'] = $item_data['ASSREQ_EMAIL'];
            $send_data['OFFICE_PHONE_NUM'] = "";
            $send_data['FAX_NUM'] = "";
            $send_data['ALTERNATE_CONTACT_NUM'] = "";
            $send_data['PERSON_NAME'] = "";
            $send_data['RESIDENT_NO'] = "";
            $send_data['OFFICE_NO'] = "";
            $send_data['EXTENSION_NO'] = "";
            $send_data['AUTH_CAPITAL'] = "";
            $send_data['ISSUED_CAPITAL'] = "";
            $send_data['PAID_UP_CAPITAL'] = "";
            $send_data['NETWORTH'] = "";
            $send_data['DATE_OF_INCORP'] = "";
            $send_data['COUNTRY_OF_INCORP'] = "";
            $send_data['REGISTRATION_NUM'] = "";
            $send_data['REGISTRATION_DATE'] = "";
            $send_data['REGISTRATION_AUTH'] = "";
            $send_data['YEARS_IN_BUSSINESS'] = "";
            $send_data['GROSS_TURNOVER'] = "";
            $send_data['NO_OF_EMP'] = "";
            $send_data['NO_OF_BRANCH'] = "";
            $send_data['INDUSTRY_CODE'] = "";
            $send_data['SUB_INDUSTRY_CODE'] = "";
            $send_data['PUBLIC_SEC_ENTP'] = "";
            $send_data['REGISTRATION_EXPIRY_DATE'] = "";
            $send_data['REGISTRATION_OFFC_ADDRESS'] = "";
            $send_data['CLIENT_TYPE'] = "I";
            $send_data['BANK_EMP_CODE'] = "I";
            $send_data['TYPE_OF_EMPLOYMENT'] = "N";
            $send_data['WORK_FROM_ADTE'] = "";
            $send_data['CURRENT_ADDRESS'] = "1";
            $send_data['PERMANENT_ADDRESS'] = "1";
            $send_data['COMMUNICATION_ADDRESS'] = "1";
            $send_data['RESIDENT_PHONE_NUM'] = "";
            $send_data['AADHAAR_REF_NUMBER'] = "";
            $send_data['CLIENT_REF_NUMBER'] = $item_data['ASSREQ_REF_NUM'];
            $send_data['SPOUSE_NAME'] = isset($cust_details['ASSREQ_SPOUSE_NAME']) ? $cust_details['ASSREQ_SPOUSE_NAME'] : "";
            $send_data['REFER_BY'] = "";
            $send_data['AUTH_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : '';
            $send_data['PID_LIST'] = $pid_data_array;
        
            try {
                $apiConn = new ReachMobApi;
                //$output = $apiConn->ReachMobConnect($send_data, "120");
                // Test Data
                $output = json_decode('{"smsResp":"S","userResp":"S","accResp":"S","successMessage":"Thank you opening the account with CSF bank.We welcome you to digital platform","emailResp":"S","userId":"909005","acntNumber":"062205001531","responseCode":"S"}', true);   

            } catch(Exception $e) {
                error_log($e->getMessage());
                $ErrorMsg = "We are unable to process your request, Please try after some time."; //Error from Class    
            }

            //Skip Dedupe Error
            //if(isset($output['errorMessage']) && strpos($output['errorMessage'], "User already created and account not created for the application number. Please try again.") !== false || strpos($output['errorMessage'], "User and account already created for the application number.") !== false || strpos($output['errorMessage'], "User not created for the application number. Please try again / Visit branch.")) {
            if(isset($output['responseCode']) && $output['responseCode'] == "S") {

               // $accountdetail2  = $main_app->sql_run("SELECT CBS_USER_ID, USERCRE_RESP_CODE, CBS_ACNT_NUMBER, ACCOUNT_RESP_CODE FROM PORTALONLINEACCOPEN WHERE ENTITY_NUM = '1' AND APP_NUMBER = :APP_NUMBER", array( 'APP_NUMBER' => $item_data['ASSREQ_REF_NUM']));
              //  $item_data_ac2  = $accountdetail2 ->fetch();

                $accountdetail2 = $main_app->sql_run("SELECT CBS_USER_ID, USERCRE_RESP_CODE, CBS_ACNT_NUMBER, ACCOUNT_RESP_CODE FROM PORTALONLINEACCOPEN WHERE ENTITY_NUM = '1' AND APP_NUMBER = :APP_NUMBER", array( 'APP_NUMBER' => $item_data['ASSREQ_REF_NUM']));
                //$item_data_ac2 = $accountdetail2->fetch();
                $output['responseCode'] = "S";     
                // $output['userId'] = $item_data_ac2['CBS_USER_ID'];
                // $output['acntNumber'] = $item_data_ac2['CBS_ACNT_NUMBER'];
                // $output['userResp'] = $item_data_ac2['USERCRE_RESP_CODE'];
                // $output['accResp'] = $item_data_ac2['ACCOUNT_RESP_CODE'];
                $output['userId'] = '123';
                $output['acntNumber'] = '456777777777';
            }
            if(!isset($ErrorMsg) || $ErrorMsg == "") {
                if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
                    $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
                }
            }
            if(isset($ErrorMsg) && $ErrorMsg != "") {
                echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
                exit();
            }

            if(!isset($output['acntNumber']) || $output['acntNumber'] == "") {
                echo "<script> swal.fire('','Unable to process API response.'); loader_stop(); enable('sbt'); </script>";
                exit();
            }

            if(!isset($output['userId']) || $output['userId'] == "") {
                echo "<script> swal.fire('','Unable to process API response'); loader_stop(); enable('sbt'); </script>";
                exit();
            }
            
        }

        if(isset($output['responseCode']) && $output['responseCode'] == "S") {
            
            $sql1_exe = $main_app->sql_run("SELECT * FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'] ));
            $item_data = $sql1_exe->fetch();
            
            
            $data = array();
            $data['CBS_ACC_NUM'] = isset($output['acntNumber']) ? $output['acntNumber'] : NULL;
            $data['CBS_CUST_ID'] = $output['userId'];
            $data['CUST_IP'] = $main_app->current_ip();

            // $data2['EMPID_AUTHORISER'] = $_SESSION['USER_ID'];
 	        $data['APP_STATUS'] = "S";
            $data['AUTH_STATUS'] = $_POST['AUTH_STATUS'];
            $data['AUTH_REMARKS'] = $_POST['REMARKS'];

            $data['AU_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : '';
            $data['AU_ON'] = $sys_datetime;

            $main_app->sql_db_auditlog('A',$page_table_name, $data); // Audit Log - DB Transaction
            $db_output = $main_app->sql_update_data("$page_table_name", $data, array( 'ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'])); // Update
            if($db_output == false) { $updated_flag = false; }

        }
        
        //else{
            
        //    $data = array();
        //    $data['AU_BY'] = '';
        //    $main_app->sql_db_auditlog('A',$page_table_name, $data); // Audit Log - DB Transaction
        //    $db_output = $main_app->sql_update_data("$page_table_name", $data, array( 'ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'])); // Update
        //    if($db_output == false) { $updated_flag = false; }
        //}
        
        if(isset($output['responseCode']) && $output['responseCode'] == "S" && $output['userResp'] == "S" && $output['accResp'] == "S" ) {

            //send AOF to customer via mail
            $send_data['METHOD_NAME'] = "AcopenFormReq";
            $send_data['KEY_NAME'] = "ASSISTANT";
            $send_data['APP_NUMBER'] = $item_data['ASSREQ_REF_NUM'];

            try {
                $apiConn = new ReachMobApi;
                //  $output = $apiConn->ReachMobConnect($send_data, "60");
                $output =  json_decode('{"responseCode":"S"}', true);   

            } catch(Exception $e) {
                error_log($e->getMessage());
                $ErrorMsg = "Technical Error, Please try later"; //Error from Class
            }

            if(!isset($ErrorMsg) || $ErrorMsg == "") {
                if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
                    $ErrorMsg = isset($output['errorMessage']) ? $output['errorMessage'] : "Unexpected API Error!";
                }
            }

            if(isset($ErrorMsg) && $ErrorMsg != "") {
                echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
                exit();
            }

        }



         //send SMS
        /*  if(isset($output['responseCode']) && $output['responseCode'] == "S" && $output['userResp'] == "S" && $output['accResp'] == "S" ) {

                $data = array($item_data['SBREQ_MOBILE_NUM'], $item_data['SBREQ_EMAIL_ID'], "");
                $smsdata = implode("|", $data);

                //call sms API 
                $send_data2 = array();
                $send_data2['METHOD_NAME'] = "smsEmailNotify"; 
                $send_data2['REQ_TYPE'] =  "S";
                $send_data2['SERVICE_CODE'] =  "SMS-NEW-AC";
                $send_data2['REQ_DATA'] =  $smsdata;

                try {
                    $apiConn = new ReachMobApi;
                    // $output = $apiConn->ReachMobConnect($send_data2, "120");
                    // Test Data
                    $output = json_decode('{"smsResp":"S","userResp":"S","accResp":"S","successMessage":"Thank you opening the account with CSF bank.We welcome you to digital platform","emailResp":"S","userId":"909005","acntNumber":"062205001531","responseCode":"S"}', true);   

                } catch(Exception $e) {
                    error_log($e->getMessage());
                    $ErrorMsg = "We are unable to process your request, Please try after some time."; //Error from Class    
                }

                if(!isset($ErrorMsg) || $ErrorMsg == "") {
                    if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
                        $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
                    }
                }
            
                if(isset($ErrorMsg) && $ErrorMsg != "") {
                    echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
                    exit();
                }
                
             //call EMAIL API 

            $data2 = array($item_data['SBREQ_MOBILE_NUM'], $item_data['SBREQ_EMAIL_ID'], $item_data['SBREQ_APP_NUM']);
            $emaildata = implode("|", $data2);

            $send_data3 = array();
            $send_data3['METHOD_NAME'] = "smsEmailNotify"; 
            $send_data3['REQ_TYPE'] =  "E";
            $send_data3['SERVICE_CODE'] =  "EMAIL-AC-DETAILS";
            $send_data3['REQ_DATA'] =  $emaildata;

            try {
                $apiConn = new ReachMobApi;
                 //$output = $apiConn->ReachMobConnect($send_data3, "120");
                // Test Data
                $output = json_decode('{"smsResp":"S","userResp":"S","accResp":"S","successMessage":"Thank you opening the account with CSF bank.We welcome you to digital platform","emailResp":"S","userId":"909005","acntNumber":"062205001531","responseCode":"S"}', true);   

            } catch(Exception $e) {
                error_log($e->getMessage());
                $ErrorMsg = "We are unable to process your request, Please try after some time."; //Error from Class    
            }

            if(!isset($ErrorMsg) || $ErrorMsg == "") {
                if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
                    $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
                }
            }
        
            if(isset($ErrorMsg) && $ErrorMsg != "") {
                echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt'); </script>";
                exit();
            }
        }
         */   
        

        if(isset($cust_details['ASSREQ_NOMINEE_NAME']) && $cust_details['ASSREQ_NOMINEE_NAME'] != ""  && $cust_details['ASSREQ_NOMINEE_NAME'] != NULL){
        

            //implode date of birth for nominee
            if(isset($cust_details['ASSREQ_NOMINEE_DOB']) && $cust_details['ASSREQ_NOMINEE_DOB'] != ""){
                $nomineedob = date('d-m-Y',strtotime($cust_details['ASSREQ_NOMINEE_DOB']));
                $birth_date = explode('-', $nomineedob);
                $dateofbirthnom = $birth_date[0].''.$birth_date[1].''.$birth_date[2];
            }


            //to fetch customer id and account number
            $sql2_exe = $main_app->sql_run("SELECT * FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'] ));
            $item_data2 = $sql2_exe->fetch();

            //call Nominee API on successful account opening
            $send_data = array();
            $send_data['METHOD_NAME'] = "addNominee"; 
            $send_data['CUSTOMER_CODE'] = "";
            $send_data['CUSTOMER_NAME'] = $cust_details['ASSREQ_NOMINEE_NAME'];
            $send_data['DOB'] = isset($dateofbirthnom) ? $dateofbirthnom : "";
            $send_data['RELATION_TO_ACC_HOLDER'] = isset($cust_details['ASSREQ_NOMINEE_RELATION']) ? $cust_details['ASSREQ_NOMINEE_RELATION'] : ""; 
            $send_data['NOMINEE_ADDRESS'] = isset($cust_details['ASSREQ_NOMINEE_ADDRESS']) ? $cust_details['ASSREQ_NOMINEE_ADDRESS'] : ""; 
            $send_data['GUARDIAN_CUST_CODE'] = "";
            $send_data['GUARDIAN_NAME'] = isset($cust_details['ASSREQ_NOMINEE_GUARDIAN']) ? $cust_details['ASSREQ_NOMINEE_GUARDIAN'] : "";
            $send_data['NATURE_OF_GUARDIAN'] = isset($cust_details['ASSREQ_GUARDIAN_NATURE']) ? $cust_details['ASSREQ_GUARDIAN_NATURE'] : "";
            $send_data['USER_ID'] = isset($item_data2['CBS_CUST_ID']) ? $item_data2['CBS_CUST_ID'] : "";
            $send_data['ACCOUNT_NUMBER'] = isset($item_data2['CBS_ACC_NUM']) ? $item_data2['CBS_ACC_NUM'] : "";
            $send_data['CLIENT_REF_NUMBER'] = $item_data2['ASSREQ_REF_NUM'];

            try {
                $apiConn = new ReachMobApi;
                //$output_2 = $apiConn->ReachMobConnect($send_data, "60");
                // Test Data
                $output_2 = json_decode('{"successMessage":"Nominee details updated successfully.","responseCode":"S"}', true);
            } catch(Exception $e) {
                error_log($e->getMessage());
                $ErrorMsg = "Technical Error, Please try later"; //Error from Class    
            }
        
            $data2 = array();
            $data2['ASSREQ_NOMINEE_STATUS'] = $output_2['responseCode'];
            $db_output2 = $main_app->sql_update_data("ASSREQ_MASTER", $data2, array( 'ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'] )); // Update
            if($db_output2 == false) { $updated_flag = false; }  

        }

    }

    //insert logs for account upgrade
    /*$data3= array();
    $data3['APP_REF_NUM'] = $item_data['SBREQ_APP_NUM'];
    $data3['APP_STATUS'] = $output['responseCode'];
    $data3['APP_STATUS_UPDATED_ON'] = $sys_datetime;
    $data3['REVIEWER_STATUS'] = "";
    $data3['REVIEWER_STATUS_UPDATED_ON'] = "";
    $data3['CR_BY'] = $item_data['SBREQ_APP_NUM'];
    $data3['CR_ON'] = $sys_datetime;

    $db_output = $main_app->sql_insert_data("LOGS_SBREQ_ACUPGRADE",$data3); // Insert
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == false) {
        echo "<script> swal.fire('','Unable to update Log Data.'); loader_stop(); enable('sbt'); </script>";
        exit();
    }*/


    if($updated_flag == true) {             
        
        $data2 = array();
        $data2['AUTH_STATUS'] = $_POST['AUTH_STATUS'];
        $db_output = $main_app->sql_update_data("ASSVAL_UIDDETAILS", $data2, array('ASSVAL_REF_NUM' => $item_data['ASSREQ_REF_NUM'])); // Update
       //if($db_output == false) { $updated_flag = false; }

    }

    if($updated_flag == true) {
    
        $go_url = ""; // Page Refresh URL
        $main_app->sql_db_commit(); // Success - DB Transaction
        $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
        $message = $_POST['AUTH_STATUS'] == "AR" ? "Record Rejected" : "Record Approved";
        echo "<script> swal.fire({ title:'Record updated', text:'{$message}', icon:'success', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { goto_url('" . $go_url . "'); } }); loader_stop(); enable('sbt'); </script>";

    } else {

        $main_app->sql_db_rollback(); // Fail - DB Transaction
        echo "<script> swal.fire({ title:'Error', text:'Unable to update content', icon:'error', allowOutsideClick:false, confirmButtonText:'OK' }).then(function (result) { if (result.value) { } }); loader_stop(); enable('sbt'); </script>";

    }
  
}
 

?>
