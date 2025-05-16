<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') or exit();

/** Mode */
$page_mode = (isset($_POST['data_mode'])) ? $main_app->strsafe_input($_POST['data_mode']) : ""; // Don't change - V = View, M = Modify Data

/** Get Data */
if (isset($_POST['id']) && $_POST['id'] != NULL) {
    $primary_value = $safe->str_decrypt($_POST['id'], $_SESSION['SAFE_KEY']);
}

if (!isset($primary_value) || $primary_value == false) {
    echo "<script> swal.fire('','Unable to fetch the data'); $('#ModalWin').modal('hide'); </script>";
    exit();
}

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$page_sub_table_name = "ASSREQ_ACCOUNTDATA";
$primary_key = "ASSREQ_REF_NUM";
$doc_table_name = "ASSREQ_CUST_DATA";
$branch_table_name = "ASSREQ_BRANCH_DETAILS";

if (isset($primary_value)) {

    $primary_value = $main_app->strsafe_output($primary_value);

    $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE $primary_key = :primary_value", array('primary_value' => $primary_value));
    $item_data = $sql_exe->fetch();

    $sql_exe2 = $main_app->sql_run("SELECT * FROM {$page_sub_table_name} WHERE $primary_key = :primary_value", array('primary_value' => $primary_value));
    $item_data2 = $sql_exe2->fetch();

    $sql_exe3 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $primary_value));
    $kycDetails = $sql_exe3->fetch();

    if (isset($kycDetails['DOC_DATA']) && $kycDetails['DOC_DATA'] != "") {
        $kycDetails = json_decode(stream_get_contents($kycDetails['DOC_DATA']), true, JSON_UNESCAPED_SLASHES);
        //$kycDetails = json_decode($kycDetails['DOC_DATA'], true);
    }

    //Master Details
    //$brn_name = $main_app->getval_field('LOC_BRN_MASTER', 'BRN_NAME', 'BRN_CODE', isset($item_data['VKYC_BRN_CODE']) ? $item_data['VKYC_BRN_CODE'] : "");

}

if (!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL) {
    echo "<script> swal.fire('','Unable to fetch the data'); $('#ModalWin').modal('hide'); </script>";
    exit();
}

//if(!isset($item_data2['ASSREQ_REF_NUM']) || $item_data2['ASSREQ_REF_NUM'] == NULL) {
//   echo "<script> swal.fire('','Unable to fetch the data'); $('#ModalWin').modal('hide'); </script>";
//    exit();
//}
/** Modal Title */
$ModalLabel = "Account Request - " . $item_data['ASSREQ_REF_NUM'];

?>

<form id="app-form-2" name="app-form-2" method="post" action="javascript:void(null);" class="form-material">
    <input type="hidden" name="cmd" value="mem_family_create" />
    <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>" />
    <input type="hidden" name="id" id="id" value="<?php echo (isset($_POST['id'])) ? $_POST['id'] : ""; ?>" />
    <div class="modal-body min-div" id="load-content">

        <ul class="nav nav-tabs mx-3 mb-3" id="myTab" role="tablist">
            
            <?php if (isset($item_data['AUTH_STATUS']) && trim($item_data['AUTH_STATUS']) != '') { ?>
                <li class="nav-item"><a class="nav-link active" id="tab-nav-4" data-toggle="tab" href="#tab-4" role="tab" aria-controls="tab-4" aria-selected="true">Authorise User Details</a></li>
                <li class="nav-item"><a class="nav-link" id="tab-nav-1" data-toggle="tab" href="#tab-1" role="tab" aria-controls="tab-1" aria-selected="true">Customer Details</a></li>
            <?php } else { ?>

                <li class="nav-item"><a class="nav-link active" id="tab-nav-1" data-toggle="tab" href="#tab-1" role="tab" aria-controls="tab-1" aria-selected="true">Customer Details</a></li>
            <?php } ?>
            <li class="nav-item"><a class="nav-link" id="tab-nav-2" data-toggle="tab" href="#tab-2" role="tab" aria-controls="tab-2" aria-selected="false">Docs Uploaded</a></li>
            <li class="nav-item"><a class="nav-link" id="tab-nav-3" data-toggle="tab" href="#tab-3" role="tab" aria-controls="tab-3" aria-selected="false">Branch User Details</a></li>

        </ul>
        <div class="tab-content mx-3" id="myTabContent">
            <?php if (isset($item_data['AUTH_STATUS']) && $item_data['AUTH_STATUS'] != '') { ?><!--for authorise user-->

                <!-- T4 : Authorise Details -->
                <div class="tab-pane fade show active" id="tab-4" role="tabpanel" aria-labelledby="tab-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <tbody>
                                        <?php
                                        if (isset($item_data['AUTH_STATUS']) &&  trim($item_data['AUTH_STATUS']) == "AS") {

                                            echo '<tr><td style="width: 20%;">Authorise Status</td><td>';
                                            echo "<span class='text text-success text-bold'> Approved </span>";
                                            echo '</td></tr>';

                                            echo '<tr><td>Approved Remarks</td><td>';
                                            echo isset($item_data['AUTH_REMARKS']) ? $main_app->strsafe_output($item_data['AUTH_REMARKS']) : "-NA-";
                                            echo '</td></tr>';

                                            echo '<tr><td>Approved By</td><td>';
                                            if (isset($item_data['AU_BY']) && $item_data['AU_BY'] != NULL) {
                                                echo "User ID: " . $main_app->strsafe_output($item_data['AU_BY']) . "<br/>";
                                                echo "User Name: " . $main_app->getval_field('ASSREQ_USER_ACCOUNTS', 'USER_FULLNAME', 'USER_ID', $item_data['AU_BY']);
                                                //echo $main_app->strsafe_output($item_data['AU_BY']);      
                                            }
                                            echo '</td></tr>';

                                            echo '<tr><td>Approved On</td><td>';
                                            if (isset($item_data['AU_ON']) && $item_data['AU_ON'] != NULL) {
                                                echo $main_app->valid_date($item_data['AU_ON'], 'd-m-Y H:i:s A');
                                            }
                                            echo '</td></tr>';
                                        } elseif (isset($item_data['AUTH_STATUS']) &&  trim($item_data['AUTH_STATUS']) == "AR") {

                                            echo '<tr><td style="width: 20%;">Authorise Status</td><td>';
                                            echo "<span class='text text-danger text-bold'> Rejected </span>";
                                            echo '</td></tr>';

                                            echo '<tr><td>Rejected Remarks</td><td>';
                                            echo isset($item_data['AUTH_REMARKS']) ? $main_app->strsafe_output($item_data['AUTH_REMARKS']) : "-NA-";
                                            echo '</td></tr>';

                                            echo '<tr><td>Rejected By</td><td>';
                                            if (isset($item_data['REJ_BY']) && $item_data['REJ_BY'] != NULL) {
                                                echo "User ID: " . $main_app->strsafe_output($item_data['REJ_BY']) . "<br/>";
                                                echo "User Name: " . $main_app->getval_field('ASSREQ_USER_ACCOUNTS', 'USER_FULLNAME', 'USER_ID', $item_data['REJ_BY']);
                                            }
                                            echo '</td></tr>';

                                            echo '<tr><td>Rejected On</td><td>';
                                            if (isset($item_data['REJ_ON']) && $item_data['REJ_ON'] != NULL) {
                                                echo $main_app->valid_date($item_data['REJ_ON'], 'd-m-Y H:i:s A');
                                            }
                                            echo '</td></tr>';
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- T1 : Details -->
                <div class="tab-pane" id="tab-1" role="tabpanel" aria-labelledby="tab-1">
                <?php } else { ?><!--for branch user-->
                    <!-- T1 : Details -->
                    <div class="tab-pane active" id="tab-1" role="tabpanel" aria-labelledby="tab-1">
                <?php } ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <tbody>
                                        <?php
                                        echo '<tr><td>Account Ref. No</td><td>';
                                        echo isset($item_data["ASSREQ_REF_NUM"]) ? $item_data["ASSREQ_REF_NUM"] : "-NA-";   
                                        echo '</td></tr>';

                                        if (isset($item_data['CBS_CUST_ID']) &&  $item_data['CBS_CUST_ID'] != "") {
                                            echo '<tr><td>Customer ID</td><td>';
                                            echo isset($item_data["CBS_CUST_ID"]) ? $item_data["CBS_CUST_ID"] : "-NA-";
                                            echo '</td></tr>'; 
                                        }
                                        
                                        if (isset($item_data['CBS_ACC_NUM']) &&  $item_data['CBS_ACC_NUM'] != "") {
                                            echo '<tr><td>Account No</td><td>';
                                            echo isset($item_data["CBS_ACC_NUM"]) ? $item_data["CBS_ACC_NUM"] : "-NA-";
                                            echo '</td></tr>'; 
                                        }
                                        //;name
                                      
                                        echo '<tr><td>Customer Name</td><td>';
                                        $fullname = "";
                                        $fullname .= (isset($item_data2['ASSREQ_CUST_FIRST_NAME']) && $item_data2['ASSREQ_CUST_FIRST_NAME'] != "") ? trim($item_data2['ASSREQ_CUST_FIRST_NAME']) : "";
                                        $fullname .= (isset($item_data2['ASSREQ_CUST_MIDDLE_NAME']) && $item_data2['ASSREQ_CUST_MIDDLE_NAME'] != "") ? " ". $item_data2['ASSREQ_CUST_MIDDLE_NAME'] : "";
                                        $fullname .= (isset($item_data2['ASSREQ_CUST_LAST_NAME']) && $item_data2['ASSREQ_CUST_LAST_NAME'] != "") ? " ". $item_data2['ASSREQ_CUST_LAST_NAME'] : "";
                                        $cust_full_name = strtoupper($fullname);
                                                                                
                                        echo (isset($cust_full_name) && $cust_full_name != "") ? $cust_full_name : NULL;

                                        echo '</td></tr>';
                                        echo '<tr><td> Name(As in Aadhar Card)</td><td>';
                                        echo (isset($kycDetails['name']) && $kycDetails['name'] != NULL) ? $main_app->strsafe_output($kycDetails['name']) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Gender</td><td>';
                                        $gender = ($item_data['ASSREQ_CUST_GENDER'] == "M") ? "Male" : (($item_data['ASSREQ_CUST_GENDER'] == "F") ? "Female" : (($item_data['ASSREQ_CUST_GENDER'] == "T") ? "TransGender" : ""));
                                        echo isset($gender) ? $gender : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>DOB</td><td>';
                                       // echo isset($item_data["ASSREQ_CUST_DOB"]) ? date('d-m-Y', strtotime($item_data['ASSREQ_CUST_DOB'])) : "-NA-";
                                        echo isset($item_data2["ASSREQ_DOB"]) ? date('d-m-Y', strtotime($item_data2['ASSREQ_DOB'])) : "-NA-";
                                        
                                        echo '</td></tr>';

                                        if (isset($item_data['AUTH_STATUS']) &&  trim($item_data['AUTH_STATUS']) != "") {
                                            
                                            $mobno = isset($item_data['ASSREQ_MOBILE_NUM']) ? $main_app->mask_text($item_data['ASSREQ_MOBILE_NUM']) : "-NA-";
                                            $emailid = isset($item_data['ASSREQ_EMAIL']) ? $main_app->mask_text($item_data['ASSREQ_EMAIL']) : "-NA-";
                                            
                                        }else{

                                            $mobno = isset($item_data['ASSREQ_MOBILE_NUM']) ? $item_data['ASSREQ_MOBILE_NUM'] : "-NA-";
                                            $emailid = isset($item_data['ASSREQ_EMAIL']) ? $item_data['ASSREQ_EMAIL'] : "-NA-";
                                       
                                        }

                                        echo '<tr><td>Mobile No</td><td>';
                                        echo $mobno;
                                        echo '</td></tr>';
                                        echo '<tr><td>Email ID</td><td>';
                                        echo $emailid;
                                        echo '</td></tr>';
                                        echo '<tr><td>Qualification</td><td>';
                                        
                                        //$qualif = ($item_data2['ASSREQ_QUALIFICATION'] == "DH") ? "Diploma Holder" : (($item_data2['ASSREQ_QUALIFICATION'] == "G") ? " Graduate" : (($item_data2['ASSREQ_QUALIFICATION'] == "HS") ? " High School" : ""));
                                        // echo isset($qualif) ? $qualif : "-NA-";
                                        
                                        if (isset($item_data2['ASSREQ_QUALIFICATION']) &&  $item_data2['ASSREQ_QUALIFICATION'] == "DH") {
                                            $qualif = "Diploma Holder";
                                        } elseif (isset($item_data2['ASSREQ_QUALIFICATION']) &&  $item_data2['ASSREQ_QUALIFICATION'] == "G") {
                                            $qualif = "Graduate";
                                        } elseif (isset($item_data2['ASSREQ_QUALIFICATION']) &&  $item_data2['ASSREQ_QUALIFICATION'] == "HS") {
                                            $qualif = "High School";
                                        } elseif (isset($item_data2['ASSREQ_QUALIFICATION']) &&  $item_data2['ASSREQ_QUALIFICATION'] == "I") {
                                            $qualif = "Illitrate";
                                        } elseif (isset($item_data2['ASSREQ_QUALIFICATION']) &&  $item_data2['ASSREQ_QUALIFICATION'] == "PG") {
                                            $qualif = "Post Graduate";
                                        } elseif (isset($item_data2['ASSREQ_QUALIFICATION']) &&  $item_data2['ASSREQ_QUALIFICATION'] == "P") {
                                            $qualif = "Professional";
                                        } elseif (isset($item_data2['ASSREQ_QUALIFICATION']) &&  $item_data2['ASSREQ_QUALIFICATION'] == "UG") {
                                            $qualif = "Under Graduate";
                                        } else {
                                            $qualif = "-NA-";
                                        }
                                        echo isset($qualif) ? $main_app->strsafe_output($qualif) : "-NA-";
                                           
                                        echo '</td></tr>';
                                       
                                        if (isset($item_data['AUTH_STATUS']) &&  trim($item_data['AUTH_STATUS']) != "") {
                                            
                                            $aadharno = isset($item_data['ASSREQ_EKYC_UID']) ? $main_app->mask_text($safe->str_decrypt($item_data['ASSREQ_EKYC_UID'], $item_data['ASSREQ_REF_NUM'])) : "-NA-";
                                            $panno = isset($item_data['ASSREQ_PAN_CARD']) ? $main_app->mask_text($safe->str_decrypt($item_data['ASSREQ_PAN_CARD'], $item_data['ASSREQ_REF_NUM'])) : "-NA-";
                                            
                                        }else{

                                            $aadharno = isset($item_data['ASSREQ_EKYC_UID']) ? $safe->str_decrypt($item_data['ASSREQ_EKYC_UID'], $item_data['ASSREQ_REF_NUM']) : "-NA-";
                                            $panno = isset($item_data['ASSREQ_PAN_CARD']) ?$safe->str_decrypt($item_data['ASSREQ_PAN_CARD'], $item_data['ASSREQ_REF_NUM']) : "-NA-";
                                       
                                        }
                                       
                                        echo '<tr><td>Aadhaar No.</td><td>';
                                        echo $aadharno;
                                        echo '</td></tr>';
                                        echo '<tr><td>PAN No.</td><td>';
                                        echo $panno;
                                        echo '</td></tr>';
                                        echo '<tr><td> Place Of Birth</td><td>';
                                        echo isset($item_data2['ASSREQ_PLACE_OF_BIRTH']) ? $main_app->getval_field('LOCATION', 'LOCN_NAME', 'LOCN_CODE', $item_data2['ASSREQ_PLACE_OF_BIRTH']) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Religion</td><td>';
                                        echo isset($item_data2['ASSREQ_RELIGION_CODE']) ? $main_app->getval_field('RELIGION', 'RELIGION_DESCN', 'RELIGION_CODE', $item_data2['ASSREQ_RELIGION_CODE']) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Occupation</td><td>';
                                        echo isset($item_data2['ASSREQ_OCCUPATION_CODE']) ? $main_app->getval_field('OCCUPATIONS', 'OCCUPATIONS_DESCN', 'OCCUPATIONS_CODE', $item_data2['ASSREQ_OCCUPATION_CODE']) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Language</td><td>';
                                        echo isset($item_data2['ASSREQ_LANGUAGE_CODE']) ? $main_app->getval_field('LANGUAGES', 'LANGUAGE_NAME', 'LANGUAGE_CODE', $item_data2['ASSREQ_LANGUAGE_CODE']) : "-NA-";
                                        echo '</td></tr>';

                                        //fetching  editable address in table
                                        $editaddress = json_decode($item_data2['ASSREQ_EDIT_ADDRESS'], true);
                                        $EHOUSENUMBER =  isset($editaddress['HOUSENUMBER']) ? $editaddress['HOUSENUMBER'] : "-NA-";
                                        $ESTREETADDRESS = isset($editaddress['STREETADDRESS']) ? $editaddress['STREETADDRESS'] : "-NA-";
                                        $EDISTRICT = isset($editaddress['DISTRICT']) ? $editaddress['DISTRICT'] : "-NA-";
                                        $ESTATE = isset($editaddress['STATE']) ? $editaddress['STATE'] : "-NA-";
                                        $EPINCODE = isset($editaddress['PINCODE']) ? $editaddress['PINCODE'] : "-NA-";    

                                        echo '<tr><td>House Number and Name</td><td>';
                                        echo $EHOUSENUMBER;
                                        echo '</td></tr>';
                                        echo '<tr><td>Street</td><td>';
                                        echo $ESTREETADDRESS;
                                        echo '</td></tr>';
                                        echo '<tr><td>District</td><td>';
                                        echo $EDISTRICT;
                                        echo '</td></tr>';
                                        echo '<tr><td>State</td><td>';
                                        echo  $ESTATE;
                                        echo '</td></tr>';
                                        echo '<tr><td>Pincode</td><td>';
                                        echo $EPINCODE;
                                        echo '</td></tr>';

                                        echo '<tr><td >Address</td><td>';
                                        // $combined_address = (isset($kycDetails['combinedAddress']) && $kycDetails['combinedAddress'] != NULL) ? $main_app->strsafe_output($kycDetails['combinedAddress']) : "-NA-";
                                        // echo $combined_address . "<br/>";
                                        echo isset($item_data2["ASSREQ_ADDRESS"]) ? $item_data2["ASSREQ_ADDRESS"] : "-NA-"."<br/>"; 
                                        echo '</td></tr>';

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <tbody>
                                        <?php
                                        echo '<tr><td>Income</td><td>';
                                        echo isset($item_data2['ASSREQ_ANNUAL_INCOME']) ? $main_app->getval_field('INCOMESLAB', 'INCSLAB_DESCN', 'INCSLAB_CODE', $item_data2['ASSREQ_ANNUAL_INCOME']) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Company</td><td>';
                                        echo isset($item_data2['ASSREQ_COMPANY_CODE']) ? $item_data2['ASSREQ_COMPANY_CODE'] : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Designation </td><td>';
                                        echo isset($item_data2['ASSREQ_DESIGNATION_CODE']) ? $item_data2['ASSREQ_DESIGNATION_CODE'] : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Type of Accomodation </td><td>';

                                        if (isset($item_data2['ASSREQ_TYPEOF_ACCOMODATION']) &&  $item_data2['ASSREQ_TYPEOF_ACCOMODATION'] == "1") {
                                            $accomod = "Own Independent house";
                                        } elseif (isset($item_data2['ASSREQ_TYPEOF_ACCOMODATION']) &&  $item_data2['ASSREQ_TYPEOF_ACCOMODATION'] == "2") {
                                            $accomod = "Own Flat";
                                        } elseif (isset($item_data2['ASSREQ_TYPEOF_ACCOMODATION']) &&  $item_data2['ASSREQ_TYPEOF_ACCOMODATION'] == "3") {
                                            $accomod = "On Rental";
                                        } elseif (isset($item_data2['ASSREQ_TYPEOF_ACCOMODATION']) &&  $item_data2['ASSREQ_TYPEOF_ACCOMODATION'] == "4") {
                                            $accomod = "Company Provided";
                                        } elseif (isset($item_data2['ASSREQ_TYPEOF_ACCOMODATION']) &&  $item_data2['ASSREQ_TYPEOF_ACCOMODATION'] == "5") {
                                            $accomod = "Joint Family";
                                        } elseif (isset($item_data2['ASSREQ_TYPEOF_ACCOMODATION']) &&  $item_data2['ASSREQ_TYPEOF_ACCOMODATION'] == "6") {
                                            $accomod = "Others";
                                        } else {
                                            $accomod = "-NA-";
                                        }
                                        echo isset($accomod) ? $main_app->strsafe_output($accomod) : "-NA-";

                                        echo '</td></tr>';
                                        echo '<tr><td>Insurance Policy Information </td><td>';
                                        if (isset($item_data2['ASSREQ_INUSURANCE_INFO']) && $item_data2['ASSREQ_INUSURANCE_INFO']) {
                                            if(isset($item_data2['ASSREQ_INUSURANCE_INFO']) && $item_data2['ASSREQ_INUSURANCE_INFO'] == "1") {
                                                $insinfo = "Insurance Policy Available";
                                            }elseif($item_data2['ASSREQ_INUSURANCE_INFO'] == "0") {
                                                $insinfo = "Plan to take New Policy in the near Future";
                                            }
                                        }
                                        echo isset($insinfo) ? $main_app->strsafe_output($insinfo) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>DBT Beneficiary</td><td>';

                                        if(isset($item_data2['ASSREQ_DBTCHECK']) && $item_data2['ASSREQ_DBTCHECK'] == "1"){
                                            $benef = "Yes";
                                        }elseif(isset($item_data2['ASSREQ_DBTCHECK']) && $item_data2['ASSREQ_DBTCHECK'] == "0"){
                                            $benef = "No";
                                        }

                                        echo isset($benef) ? $main_app->strsafe_output($benef) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Fathers Name</td><td>';
                                        echo isset($item_data2['ASSREQ_FATHERSNAME']) ? $item_data2['ASSREQ_FATHERSNAME'] : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Mothers Name</td><td>';
                                        echo isset($item_data2['ASSREQ_MOTHERSNAME']) ? $item_data2['ASSREQ_MOTHERSNAME'] : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Martial Status</td><td>';

                                        if (isset($item_data2['ASSREQ_MARITAL_STATUS']) &&  $item_data2['ASSREQ_MARITAL_STATUS'] == "S") {
                                            $mstatus = "Single";
                                        } elseif (isset($item_data2['ASSREQ_MARITAL_STATUS']) &&  $item_data2['ASSREQ_MARITAL_STATUS'] == "M") {
                                            $mstatus = "Married";
                                        }
                                        // elseif (isset($item_data2['ASSREQ_MARITAL_STATUS']) &&  $item_data2['ASSREQ_MARITAL_STATUS'] == "3") {
                                        //    $mstatus = "Divorced";
                                       // }
                                         else {
                                            $mstatus = "-NA-";
                                        }
                                        echo isset($mstatus) ? $main_app->strsafe_output($mstatus) : "-NA-";
                                        echo '</td></tr>';

                                        if (isset($item_data2['ASSREQ_MARITAL_STATUS']) &&  $item_data2['ASSREQ_MARITAL_STATUS'] == "M") {
                                            echo '<tr><td>Spouse Name</td><td>';
                                            echo isset($item_data2['ASSREQ_SPOUSE_NAME']) ? $item_data2['ASSREQ_SPOUSE_NAME'] : "-NA-";
                                            echo '</td></tr>';
                                        }

                                        echo '<tr><td>City</td><td>';
                                        echo isset($item_data2['ASSREQ_CITY_CODE']) ? $main_app->getval_field('LOCATION', 'LOCN_NAME', 'LOCN_CODE', $item_data2['ASSREQ_CITY_CODE']) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>State</td><td>';
                                        echo isset($item_data2['ASSREQ_STATE_CODE']) ? $main_app->getval_field('STATE', 'STATE_NAME', 'STATE_CODE', $item_data2['ASSREQ_STATE_CODE']) : "-NA-";
                                        echo '</td></tr>';

                                        echo '<tr><td>Branch Name</td><td>';
                                        echo isset($item_data2['ASSREQ_BRANCH_CODE']) ? $main_app->getval_field('MBRN', 'MBRN_NAME', 'MBRN_CODE', $item_data2['ASSREQ_BRANCH_CODE']) : "-NA-";
                                        echo '</td></tr>';

                                        echo '<tr><td>Account Product </td><td>';
                                        echo isset($item_data2['ASSREQ_PRODUCT_CODE']) ? $main_app->getval_field('ASSREQ_PRODUCT_CODE', 'PRODUCT_DESC', 'PRODUCT_CODE', $item_data2['ASSREQ_PRODUCT_CODE']) : "-NA-";
                                        echo '</td></tr>';

                                        echo '<tr><td>Account Sub-Type </td><td>';
                                        echo isset($item_data2['ASSREQ_ACNT_SUBTYP']) ? $main_app->getval_field('ASSREQ_ACNT_SUBTYP', 'ACNTTYP_DESC', 'ACNTTYP_CODE', $item_data2['ASSREQ_ACNT_SUBTYP']) : "-NA-";
                                        echo '</td></tr>';

                                        echo '<tr><td>Nationality</td><td>';
                                        echo $nationality = (isset($kycDetails['country']) && $kycDetails['country'] != NULL) ? $main_app->strsafe_output($kycDetails['country']) : "-NA-";
                                        echo '</td></tr>';

                                        echo '<tr><td>Weaker Section </td><td>';  
                                        echo $weakersec = "1";
                                        // echo $weakersec = (isset($item_data2['ASSREQ_WEAKER_CODE']) && $item_data2['ASSREQ_WEAKER_CODE'] != NULL) ? $main_app->getval_field('cbuat.wksec','wksec_descn','wksec_code',$item_data2['ASSREQ_WEAKER_CODE']) : "-NA-";
                                        echo '</td></tr>';

                                        echo '<tr><td>Source Employee ID </td><td>';  
                                        echo $empid = (isset($item_data2['ASSREQ_SOURCE_EMPID']) && $item_data2['ASSREQ_SOURCE_EMPID'] != NULL) ? $item_data2['ASSREQ_SOURCE_EMPID'] : "-NA-";              
                                        echo '</td></tr>';

                                        echo '<tr><td>Source Employee Name </td><td>';  
                                            echo $empnm = '1';
                                          //  echo $empnm = (isset($item_data2['ASSREQ_SOURCE_EMPID']) && $item_data2['ASSREQ_SOURCE_EMPID'] != NULL) ? $main_app->getval_field('cbuat.memp','memp_name','memp_num',$item_data2['ASSREQ_SOURCE_EMPID']) : "-NA-";              
                                        echo '</td></tr>';

                                        echo '<tr><td>Initial Deposit section </td><td>';  
                                        echo isset($item_data2['ASSREQ_INITIAL_DEPOSIT']) ? $item_data2['ASSREQ_INITIAL_DEPOSIT'] : "-NA-";
                                        echo '</td></tr>';

                                        echo '<tr><td>Amount</td><td>';  
                                        echo isset($item_data2['ASSREQ_AMT_DEPOSIT']) ? $main_app->money_format_INR($item_data2['ASSREQ_AMT_DEPOSIT']) : "-NA-";
                                        echo '</td></tr>';

                                        echo '<tr><td>Account Sub-Type</td><td>';  
                                        echo isset($item_data2['ASSREQ_AMT_DEPOSIT']) ? $main_app->money_format_INR($item_data2['ASSREQ_AMT_DEPOSIT']) : "-NA-";
                                        echo '</td></tr>';
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <tbody>
                                        <?php

                                        echo '<tr><td>Nominee Name</td><td>';
                                        echo isset($item_data2['ASSREQ_NOMINEE_NAME']) ? $item_data2['ASSREQ_NOMINEE_NAME'] : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Nominee DOB</td><td>';
                                        echo isset($item_data2['ASSREQ_NOMINEE_DOB']) ? date("d-m-Y", strtotime($item_data2['ASSREQ_NOMINEE_DOB'])) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Nominee Relation</td><td>';
                                        echo isset($item_data2['ASSREQ_NOMINEE_RELATION']) ? $main_app->getval_field('RELATION', 'RELATION_DESCN', 'RELATION_CODE', $item_data2['ASSREQ_NOMINEE_RELATION']) : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Nominee Address</td><td>';
                                        echo isset($item_data2['ASSREQ_NOMINEE_ADDRESS']) ? $item_data2['ASSREQ_NOMINEE_ADDRESS'] : "-NA-";
                                        echo '</td></tr>';
                                        echo '<tr><td>Minor</td><td>';
                                        if (isset($item_data2['ASSREQ_MINOR_FLAG']) && $item_data2['ASSREQ_MINOR_FLAG']) {
                                            if ($item_data2['ASSREQ_MINOR_FLAG'] == "Y") {
                                                $minor = "Yes";
                                            } else if ($item_data2['ASSREQ_MINOR_FLAG'] == "N") {
                                                $minor = "No";
                                            }
                                        }
                                        echo isset($minor) ? $main_app->strsafe_output($minor) : "-NA-";
                                        echo '</td></tr>';

                                        if (!isset($item_data2["ASSREQ_MINOR_FLAG"]) || $item_data2["ASSREQ_MINOR_FLAG"] != "N") {

                                            echo '<tr><td>Guardian Name</td><td>';

                                            if (isset($item_data2['ASSREQ_GUARDIAN_NATURE']) &&  $item_data2['ASSREQ_GUARDIAN_NATURE'] == "F") {
                                                $guardnature = "Father";
                                            } elseif (isset($item_data2['ASSREQ_GUARDIAN_NATURE']) &&  $item_data2['ASSREQ_GUARDIAN_NATURE'] == "M") {
                                                $guardnature = "Mother";
                                            } elseif (isset($item_data2['ASSREQ_GUARDIAN_NATURE']) &&  $item_data2['ASSREQ_GUARDIAN_NATURE'] == "O") {
                                                $guardnature = "Others";
                                            } else {
                                                $guardnature = "-NA-";
                                            }
                                            echo $guardnature;
                                            //echo isset($item_data2['ASSREQ_GUARDIAN_NATURE']) ? $item_data2['ASSREQ_GUARDIAN_NATURE'] : "-NA-";
                                            echo '</td></tr>';
                                            echo '<tr><td>Guardian Relation</td><td>';
                                            echo isset($item_data2['ASSREQ_NOMINEE_GUARDIAN']) ? $item_data2['ASSREQ_NOMINEE_GUARDIAN'] : "-NA-";
                                            echo '</td></tr>';
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- T2 : Docs Uploaded -->
                    <div class="tab-pane fade" id="tab-2" role="tabpanel" aria-labelledby="tab-2">
                                        
                        <div class="row py-3">

                            <?php
                           
                            //Total Results
                            $totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM {$doc_table_name} WHERE $primary_key = :primary_value", array('primary_value' => $primary_value));
                            if ($totalResults) {
                                
                                $sql_exe4 = $main_app->sql_run("select max(DOC_SL) as DOCSL,DOC_CODE from {$doc_table_name}  WHERE $primary_key = :primary_value GROUP BY DOC_CODE", array('primary_value' => $primary_value));
                                while ($row_doc4 = $sql_exe4->fetch()) {
                                  
                                    $MAX_DOCSL = $row_doc4['DOCSL'];
                                    $DOC_CODE = $row_doc4['DOC_CODE'];
                                    if($MAX_DOCSL !='' && $DOC_CODE !='') {
                                        $sql_exe5 = $main_app->sql_run("SELECT  * FROM {$doc_table_name} WHERE $primary_key = :primary_value AND DOC_CODE = '$DOC_CODE' and DOC_SL = '$MAX_DOCSL' ", array('primary_value' => $primary_value)); // GROUP BY DOC_CODE ORDER BY DOC_SL DESC
                                        $row_doc5 = $sql_exe5->fetch();
    
                                        echo "<div class='col-md-4 form-group'>";
                                            echo "<div class='card'>";
    
                                                $docname = "";
                                                if (isset($row_doc5["DOC_CODE"]) && $row_doc5["DOC_CODE"] == "AADHAAR1F") {
                                                    $docname = "Aadhaar Card Front Photo";
                                                }elseif(isset($row_doc5["DOC_CODE"]) && $row_doc5["DOC_CODE"] == "AADHAAR2B") {
                                                    $docname = "Aadhaar Card Back Photo";
                                                }elseif(isset($row_doc5["DOC_CODE"]) && $row_doc5["DOC_CODE"] == "PANIMG") {
                                                    $docname = "Pan Card Photo";
                                                }elseif(isset($row_doc5["DOC_CODE"]) && $row_doc5["DOC_CODE"] == "CUSTIMG") {
                                                    $docname = "Customer Photo";
                                                }elseif(isset($row_doc5["DOC_CODE"]) && $row_doc5["DOC_CODE"] == "CUSTSIGNIMG") {
                                                    $docname = "Customer Sign Photo";
                                                }
                                                echo "<div class='card-header'><strong>{$docname}</strong><br/><span class='text-muted small'>" . $main_app->strsafe_output($row_doc5["FILE_NAME"]) . "</span></div>";
    
    
                                                //echo "<div class='card-header'>{$row_doc3['DOC_CODE']}<br/><span class='text-muted small'>" . $main_app->strsafe_output($Detail4["FILE_NAME"]) . "</span></div>";
                                                echo "<div class='card-body d-flex justify-content-center'>";
    
                                                    if (isset($row_doc5["DOC_PATH"]) && ($row_doc5["DOC_PATH"] != NULL || $row_doc3["DOC_PATH"] != "")) {
    
                                                        $img_data = stream_get_contents($row_doc5['DOC_PATH']);
                                                        echo '<div class="imgfill-box"><img src="data:image/jpeg;charset=utf-8;base64,' . $img_data . '" height="auto" width="auto" /></div>';
                                                    }
                                                echo "</div>
    
                                            </div>
                                        </div>";
                                    }
                                }
                            } else {
                                echo '<div class="col-md-12 text-center text-danger py-3">No records found.</div>';
                            }
                            ?>

                        </div>

                        <?php /* ?>
				<div class="table-responsive">
					<table class="app-data-table table table-striped table-sm dataTable no-footer" id="resp-table">
						<thead>
							<tr>
								<th width="20%">Document Type</th>
								<th class="text-center">File Name</th>
								<th width="20%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							//Total Results
							$totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM {$doc_table_name} WHERE $primary_doc_key = :primary_doc_value", array('primary_doc_value' => $primary_value));
							if ($totalResults) {
								$sql_exe3 = $main_app->sql_run("SELECT * FROM {$doc_table_name} WHERE $primary_doc_key = :primary_doc_value", array('primary_doc_value' => $primary_value));
								while ($row_doc = $sql_exe3->fetch()) {
									$FILE_PATH = UPLOAD_DOCS_DIR . $main_app->strsafe_output($row_doc["DOCS_FILEPATH"]) . $main_app->strsafe_output($row_doc["DOCS_FILE_NAME"]);

									echo '<tr>';
									echo '<td>' . $main_app->strsafe_output($row_doc["DOCS_TYPE_CODE"]) . '</td>';
									echo '<td class="text-center">' . $main_app->strsafe_output($row_doc["DOCS_FILE_NAME"]) . '</td>';
									echo '<td><a href="javascript:void(0);" data-val="' . $safe->str_encrypt($FILE_PATH, $_SESSION['SAFE_KEY']) . '" class="text-primary" onclick=open_document($(this).data("val"));><i class="mdi mdi-eye"></i> View</a></td>';
									echo '</tr>';

								}
							} else {
								echo '<tr><td colspan="3" class="text-center">No records found.</td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<?php */ ?>

                    </div>

                    <!-- T3 : Branch Details -->
                    <div class="tab-pane fade" id="tab-3" role="tabpanel" aria-labelledby="tab-3">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm">
                                        <tbody>
                                            <?php
                                            echo '<tr><td style="width: 20%;">Branch User ID</td><td>';
                                            echo isset($item_data["CR_BY"]) ? $item_data["CR_BY"] : "-NA-";
                                            echo '</td></tr>';
                                            echo '<tr><td>Branch User Name</td><td>';
                                            echo isset($item_data["CR_BY"]) ? $main_app->getval_field('ASSREQ_USER_ACCOUNTS', 'USER_FULLNAME', 'USER_ID', $item_data['CR_BY']) : "-NA-";
                                            echo '</td></tr>';
                                            echo '<tr><td>Branch User Gelocation</td><td>';
                                            echo isset($item_data["STAFF_GEO_LAT"]) ? 'Longitude : ' . $item_data["STAFF_GEO_LAT"] : "-NA-";
                                            echo isset($item_data["STAFF_GEO_LONG"]) ? '&nbsp;, Latitude : ' . $item_data["STAFF_GEO_LONG"] : "-NA-";
                                            echo '</td></tr>';

                                            echo '<tr><td>Branch User Photo</td><td>';

                                            $sql_exe6 = $main_app->sql_run("SELECT * FROM {$branch_table_name} WHERE $primary_key = :primary_value", array('primary_value' => $primary_value));
                                            $row_doc6 = $sql_exe6->fetch();
                                            if (isset($row_doc6["DOC_PATH"]) && ($row_doc6["DOC_PATH"] != NULL || $row_doc4["DOC_PATH"] != "")) {

                                                $img_data = stream_get_contents($row_doc6['DOC_PATH']);
                                                echo '<div class="imgfill-box"><img src="data:image/jpeg;charset=utf-8;base64,' . $img_data . '" height="auto" width="auto" /></div>';
                                            }

                                            echo '</td></tr>';


                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer p-r-30">
            <button type="button" tabIndex="-1" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            <!-- <button type="button" class="btn btn-success px-4" name="sbt2" id="sbt2" onclick="send_form('app-form-2','sbt2'); return false;"> Save <i class="mdi mdi-arrow-right"></i> </button> -->
        </div>
</form>


<script type="text/javascript">
    function open_document(file) {
        if (file) {
            var url = "view-document?FILE=" + file;
            var myWidth = screen.width;
            var myWidth = 1400;
            var myHeight = 650;
            var left = (screen.width - myWidth) / 2;
            var top = (screen.height - myHeight) / 6;
            var myWindow = window.open(url, 'Document Details', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + myWidth + ', height=' + myHeight + ', top=' + top + ', left=' + left);

            if (window.focus) {
                myWindow.focus()
            }
        }

        return false;
    }

    $(document).ready(function() {
        //$("a.single_image").fancybox();

        //#ModalLabel
        $('#ModalWin-ModalLabel').html("<?php echo $ModalLabel; ?>");

    });
</script>