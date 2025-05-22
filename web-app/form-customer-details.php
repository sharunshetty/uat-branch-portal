<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @developer   : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

/** Check User Session */
require_once(dirname(__FILE__) . '/check-login.php');

/** Current Page */
$page_pgm_code = "";

$page_title = "Customer Details";
$page_link = "";

$parent_page_title = "";
$parent_page_link = "";

$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";

$errorMsg = "";

if(!isset($_GET['ref_Num']) || $_GET['ref_Num'] == "") {
  $errorMsg = "Invalid Request";
}else { 
  
  //Decode Request Data  
  $encrypt_ref_num = $main_app->strsafe_input($_GET['ref_Num']);
  $assref_num = $safe->str_decrypt($encrypt_ref_num, $_SESSION['SAFE_KEY']);
  
  if(!isset($assref_num) || $assref_num== false || $assref_num == "") {
      $errorMsg = "Invalid URL Request";
  } else {

    $assref_num = $main_app->strsafe_output($assref_num);
    $enc_assref_num = $safe->str_encrypt( $assref_num, $_SESSION['SAFE_KEY']);
    
    $sql_exe = $main_app->sql_run("SELECT ASSREQ_CUST_FNAME, ASSREQ_REF_NUM, ASSREQ_EKYC_FLAG, ASSREQ_PAN_FLAG, ASSREQ_BASIC_DETAIL_FLG FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
    $item_data = $sql_exe->fetch();  
    
    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
      $errorMsg = "Unable to fetch application details";
    }  

    //e-KYC not done
    if(!isset($item_data['ASSREQ_EKYC_FLAG']) || $item_data['ASSREQ_EKYC_FLAG'] != "Y") {
      header('Location: '.APP_URL.'/ass-aadhaar-details?ref_Num="'.$main_app->strsafe_input($enc_assref_num).'"');
      exit();
    }

      //pan not done
    if(!isset($item_data['ASSREQ_PAN_FLAG']) || $item_data['ASSREQ_PAN_FLAG'] != "Y") {
      header('Location: '.APP_URL.'/ass-form-pan?ref_Num="'.$main_app->strsafe_input($enc_assref_num).'"');
      exit();
    }

    if(isset($item_data['ASSREQ_BASIC_DETAIL_FLG']) || $item_data['ASSREQ_BASIC_DETAIL_FLG']== "Y") {
 	  $sql_exe1 = $main_app->sql_run("SELECT *  FROM ASSREQ_ACCOUNTDATA WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
      $item_data1 = $sql_exe1->fetch();  
      if(!isset($item_data1['ASSREQ_REF_NUM']) || $item_data1['ASSREQ_REF_NUM']== false || $item_data1['ASSREQ_REF_NUM'] == NULL || $item_data1['ASSREQ_REF_NUM'] == "") {
        $errorMsg = "Unable to fetch application details";
      }  
    }  

       
    $sql_exe3 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM']));
    $kycDetails = $sql_exe3->fetch();

    if(isset($kycDetails['DOC_DATA']) && $kycDetails['DOC_DATA'] != "") {
      $kycDetails = json_decode(stream_get_contents($kycDetails['DOC_DATA']), true, JSON_UNESCAPED_SLASHES); 
    }
    //convert name to uppercase
    //$aadhaar_name = strtoupper($kycDetails['name']);  
    $first_name = $middle_name = $last_name ='';
    if(isset($kycDetails['name']) && $kycDetails['name'] != "") {
      $name = explode(' ', strtoupper($kycDetails['name']));
      $first_name = $name[0];
      $middle_name = isset($name[1]) ? $name[1] : '' ;
      $last_name = isset($name[2]) ? $name[2] : '';
    }
  }
}

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
?>

<?php 
    if(isset($errorMsg) && $errorMsg == "") {
        echo "<div class='abp-heading text-muted'>Account Ref No: <span class='text-danger'>$assref_num</span></div>";
   }
?>

<!-- Content : Start -->

<section class="content">
  <div class="container-fluid">
    <div class="row">

      <?php if(isset($errorMsg) && $errorMsg != "") { ?>
        <div class="col-md-12 text-danger text-center mt-5 pt-5 h5"><?php echo $main_app->strsafe_output($errorMsg); ?></div>
      <?php } else { ?> 

        <div class="col-md-12 form-group">
          <div class="page-card box-min-h">
            
            <div class="card card-outline card-brand">
              <div class="card-body min-high2">
                <form name="form-customer-details" id="form-customer-details" method="post" action="javascript:void(0);" class="form-material">
                  <input type="hidden" name="cmd" value="form_customer_details">
                  <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>
                  <input type="hidden" id="asnVal" name="asnVal" value="<?php echo $main_app->strsafe_input($enc_assref_num);?>"/>
                  <input type="hidden" id="pKey" value="<?php echo $safe->rsa_public_key();?>" />
                  <!-- <div class="row justify-content-center my-4"> -->

                  <div class="row mt-3 mb-4">

                  <!--  <?php 
                      if(isset($_SESSION['name_flag']) && $_SESSION['name_flag'] == "Y") { ?>
                        <div class="col-md-4 form-group">
                          <label class="col-md-12 label_head">Please enter Customer Full name <mand>*</mand></label>
                          <div class="col-md-12">
                            <input type="text" name="CUST_FULL_NAME" id="CUST_FULL_NAME" maxlength="" class="form-control border-input reset-form js-toUpper" autocomplete="none" value= "<?php echo $main_app->strsafe_input($item_data['ASSREQ_CUST_FNAME']); ?>" >
                           <span id="name_change" class="text-danger small"><small>* Your name is different than UID and PAN in the PID. Kindly update the name to proceed for account opening. </small></span>
                          </div>
                        </div>
                    <?php } ?>-->


                    <!-- <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Customer Name  (As in Aadhaar Card)<mand>*</mand></label>
                      <div class="col-md-12">
                        <input type="text" name="CUST_FULL_NAME" id="CUST_FULL_NAME" maxlength="" class="form-control border-input reset-form js-toUpper js-alphaNumericspace" autocomplete="none" value= "<?php echo $main_app->strsafe_input($aadhaar_name); ?>" >
                       </div>
                    </div> -->

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">First Name  (As in Aadhaar Card)<mand>*</mand></label>
                      <div class="col-md-12">
                        <input type="text" name="CUST_FIRST_NAME" id="CUST_FIRST_NAME" maxlength="" class="form-control border-input reset-form js-toUpper js-alphaNumericspace" autocomplete="none" value= "<?php echo $main_app->strsafe_input($first_name); ?>" >
                       </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Middle Name  (As in Aadhaar Card)</label>
                      <div class="col-md-12">
                        <input type="text" name="CUST_MIDDLE_NAME" id="CUST_MIDDLE_NAME" maxlength="" class="form-control border-input reset-form js-toUpper js-alphaNumericspace" autocomplete="none" value= "<?php echo $main_app->strsafe_input($middle_name); ?>" >
                       </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Last Name  (As in Aadhaar Card)</label>
                      <div class="col-md-12">
                        <input type="text" name="CUST_LAST_NAME" id="CUST_LAST_NAME" maxlength="" class="form-control border-input reset-form js-toUpper js-alphaNumericspace" autocomplete="none" value= "<?php echo $main_app->strsafe_input($last_name); ?>" >
                       </div>
                    </div>
                      
                      
                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Place of birth <mand>*</mand></label>
                      <div class="col-md-12">
                        <select name="PLACE_OF_BIRTH" id="PLACE_OF_BIRTH" class="form-control border-input text-uppercase" autocomplete="none">
                        <option value="">-- Select --</option>
                        <?php
                          $sql_exe = $main_app->sql_run("SELECT DISTINCT LOCN_CODE, LOCN_NAME FROM LOCATION WHERE LOCN_CNTRY_CODE='IN' ORDER BY LOCN_NAME ASC");
                          while ($row = $sql_exe->fetch()) {
                              echo "<option value=".$row['LOCN_CODE'].">".$row['LOCN_NAME']."</option>";
                          }
                        ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Occupation <mand>*</mand></label>
                      <div class="col-md-12">
                        <select name="OCCUPATION" id="OCCUPATION" class="form-control border-input" autocomplete="none">
                          <option value="">-- Select --</option>
                          <option value="1">h</option>
                          <?php

                            // $sql_exe = $main_app->sql_run("select w.occupations_code,w.occupations_descn from occupations@cbsdblink w where w.occupations_descn  not like '%NTBU%' order by 1");
                            // while ($row = $sql_exe->fetch() ) {
                            //   echo "<option value=".$row['OCCUPATIONS_CODE'].">".$row['OCCUPATIONS_DESCN']."</option>";
                            // }  

                          ?>
                        </select>
                      </div>
                    </div> 

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Annual Income <mand>*</mand></label>
                      <div class="col-md-12">
                        <select name="ANNUAL_INCOME" id="ANNUAL_INCOME" class="form-control border-input" autocomplete="none">
                        <option value="">-- Select --</option>
                        <?php
                          $sql_exe = $main_app->sql_run("SELECT INCSLAB_CODE, INCSLAB_DESCN FROM INCOMESLAB ORDER BY INCSLAB_DESCN ASC");
                          while ($row = $sql_exe->fetch()) {
                              echo "<option value=".$row['INCSLAB_CODE'].">".$row['INCSLAB_DESCN']."</option>";
                          }
                        ?>
                        </select>
                      </div>
                    </div>
                                        
                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Fathers name <mand>*</mand></label>
                      <div class="col-md-12">
                        
                        <!-- <?php if(isset($kycDetails['fatherName']) && $kycDetails['fatherName'] !="") { ?>
                          <input type="text" name="FATHERS_NAME" id="FATHERS_NAME"  value="<?php echo $kycDetails['fatherName']; ?>" class="form-control border-input reset-form" autocomplete='none' readonly>
                        <?php } else { ?>
                          <input type="text" name="FATHERS_NAME" id="FATHERS_NAME"  value="" class="form-control border-input reset-form" autocomplete="none">
                        <?php } ?>  -->

                        <?php
                        $fathername = '';
                        if(isset($kycDetails['fatherName']) && $kycDetails['fatherName'] !=""){
                          $fathername = $kycDetails['fatherName'];
                        }?>
                        
                        <input type="text" name="FATHERS_NAME" id="FATHERS_NAME"  value="<?php echo $fathername; ?>" class="form-control border-input reset-form js-alphaNumericspace" autocomplete='none'>
                        
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Mothers name <mand>*</mand></label>
                      <div class="col-md-12">
                      <input type="text" name="MOTHERS_NAME" id="MOTHERS_NAME" maxlength="" class="form-control border-input reset-form js-alphaNumericspace" autocomplete="off">
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Date of birth <mand>*</mand></label>
                      <div class="col-md-12">
                      <input type="text" name="DOB" id="DOB" maxlength=""  value="<?php echo isset($kycDetails['dob']) ? date('d-m-Y ',strtotime($kycDetails['dob'])) : ""; ?>" class="form-control border-input reset-form" autocomplete="none" readonly>
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Marital Status <mand>*</mand></label>
                      <?php
                      if((isset($kycDetails['gender']) && $kycDetails['gender'] == "F") && (isset($kycDetails['husbandName']) && $kycDetails['husbandName'] !="") ) { ?>
                        <div class="col-md-12">
                          <select name="MARITAL_STATUS" id="MARITAL_STATUS" class="form-control border-input" autocomplete="off" readonly>
                            <option value="M"> Married </option>
                          </select>
                        </div>
                      <?php } else { ?>
                        <div class="col-md-12">
                          <select name="MARITAL_STATUS" id="MARITAL_STATUS" class="form-control border-input" autocomplete="off">
                            <option value="">-- Select --</option>
                            <option value="S"> Single</option>
                            <option value="M"> Married </option>
                            <!-- <option value="D"> Divorced </option> -->
                          </select>
                        </div>
                      <?php } ?>
                    </div>
                    
                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Religion<mand>*</mand></label>
                      <div class="col-md-12">
                        <select name="RELIGION" id="RELIGION" class="form-control border-input" autocomplete="off">
                          <option value="">-- Select --</option>
                          <option value="1">h</option>
                          <?php
                            // $sql_exe = $main_app->sql_run("select * from cbuat.religion r where r.religion_code<>90");
                            // while ($row = $sql_exe->fetch() ) {
                            //     echo "<option value=".$row['RELIGION_CODE'].">".$row['RELIGION_DESCN']."</option>";
                            // }
                          ?>
                        </select>

                        </div>
                    </div>
                  
                    <?php
              
                    if(isset($kycDetails['husbandName']) && $kycDetails['husbandName'] !="") { ?>
                      <div class="col-md-4 form-group">
                          <label class="col-md-12 label_head">Spouse Name <mand>*</mand></label>
                          <div class="col-md-12">
                            <input type="text" name="SPOUSE_NAME" id="SPOUSE_NAME" maxlength="" value="<?php echo $kycDetails['husbandName'] ?>" class="form-control border-input reset-form js-alphaNumericspace" autocomplete="none" readonly>
                          </div>
                      </div>
                      <?php } else { ?>
                        <div class="col-md-4 form-group" id="spouse_name">
                          <label class="col-md-12 label_head">Spouse Name <mand>*</mand></label>
                          <div class="col-md-12">
                            <input type="text" name="SPOUSE_NAME" id="SPOUSE_NAME" maxlength="" value="" class="form-control border-input reset-form" autocomplete="none">
                          </div>
                      </div>
                    <?php } ?>

                    <?php if(isset($kycDetails['relativeName']) && $kycDetails['relativeName'] !="") { ?>
                      <div class="col-md-4 form-group" id="rel_name">
                          <label class="col-md-12 label_head">Relative Name <mand>*</mand></label>
                          <div class="col-md-12">
                              <input type="text" name="RELATIVE_NAME" id="RELATIVE_NAME" maxlength="" value="<?php echo $kycDetails['relativeName'] ?>" class="form-control border-input reset-form" autocomplete="none">
                          </div>
                      </div>
                    <?php } ?>
                    
                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Qualification <mand>*</mand></label>
                      <div class="col-md-12">             
                        <select name="QUALIFICATION" id="QUALIFICATION" class="form-control border-input" autocomplete="off">
                          <option value="">-- Select --</option>
                          <?php
                            $sql_exe = $main_app->sql_run("SELECT QUALIF_CODE, QUALIF_DESCN FROM ASSREQ_QUALIFICATION WHERE QUALIF_STATUS = '1' ORDER BY QUALIF_DESCN ASC");
                            while ($row = $sql_exe->fetch()) {
                                echo "<option value=".$row['QUALIF_CODE'].">".$row['QUALIF_DESCN']."</option>";
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                  
                    <!-- <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Designation<mand>*</mand></label>
                      <div class="col-md-12">
                        <input type="text" name="DESIGNATION_CODE" id="DESIGNATION_CODE" maxlength="" class="form-control border-input reset-form" autocomplete="off" readonly>
                      </div>
                    </div> -->


                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">DBT Beneficiary <mand>*</mand></label>
                      <div class="col-md-12">
                        <select name="DBT_BENEFICIARY" id="DBT_BENEFICIARY" class="form-control border-input" autocomplete="off">
                          <option value="">-- Select --</option>
                          <option value="1">Yes</option>
                          <option value="0"> No </option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4 form-group d-none" id="spouse_name">
                        <label class="col-md-12 label_head">Nationality <mand>*</mand></label>
                        <div class="col-md-12">
                          <input type="text" name="NATIONALITY" id="NATIONALITY" maxlength="" class="form-control border-input reset-form" value="<?php echo isset($kycDetails['country']) ? $kycDetails['country'] : NULL; ?>" autocomplete="none" readonly>
                        </div>
                    </div>
                   

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">House Number/ Address I <mand>*</mand></label>
                      <div class="col-md-12">
                        <?php

                          $houseNumber='';
                          if(isset($item_data['ASSREQ_BASIC_DETAIL_FLG']) == ""  && $item_data['ASSREQ_BASIC_DETAIL_FLG'] == "") {
                            $houseNumber = (isset($kycDetails['houseNumber']) && $kycDetails['houseNumber'] != NULL) ? $main_app->strsafe_output($kycDetails['houseNumber']) : NULL;
                          }
                          echo '<input type="text" name="HOUSENUMBER" id="HOUSENUMBER" maxlength="" class="form-control border-input reset-form js-alphaNumericspace" value="'.$houseNumber.'" autocomplete="none" >';
                        
                        ?>
                    
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Street/ Address II <mand>*</mand></label>
                      <div class="col-md-12">
                        <?php

                          $streetNumber='';
                          if(isset($item_data['ASSREQ_BASIC_DETAIL_FLG']) == ""  && $item_data['ASSREQ_BASIC_DETAIL_FLG'] == "") {
                            $streetNumber = (isset($kycDetails['street']) && $kycDetails['street'] != NULL) ? $main_app->strsafe_output($kycDetails['street']) : NULL;
                          }
                          echo '<input type="text" name="STREETADDRESS" id="STREETADDRESS" maxlength="" class="form-control border-input reset-form js-alphaNumericspace" value="'.$streetNumber.'" autocomplete="none" >';
                        
                        ?>
                    
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">District <mand>*</mand></label>
                      <div class="col-md-12">
                        <?php

                          $district='';
                          if(isset($item_data['ASSREQ_BASIC_DETAIL_FLG']) == ""  && $item_data['ASSREQ_BASIC_DETAIL_FLG'] == "") {
                            $district = (isset($kycDetails['district']) && $kycDetails['district'] != NULL) ? $main_app->strsafe_output($kycDetails['district']) : NULL;
                          }
                          echo '<input type="text" name="DISTRICT" id="DISTRICT" maxlength="" class="form-control border-input reset-form js-alphaNumericspace" value="'.$district.'" autocomplete="none" >';
                        
                        ?>
                    
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">State <mand>*</mand></label>
                      <div class="col-md-12">
                        <?php

                          $state='';
                          if(isset($item_data['ASSREQ_BASIC_DETAIL_FLG']) == ""  && $item_data['ASSREQ_BASIC_DETAIL_FLG'] == "") {
                            $state = (isset($kycDetails['state']) && $kycDetails['state'] != NULL) ? $main_app->strsafe_output($kycDetails['state']) : NULL;
                          }
                          echo '<input type="text" name="STATE" id="STATE" maxlength="" class="form-control border-input reset-form js-alphaNumericspace" value="'.$state.'" autocomplete="none" >';
                        
                        ?>
                    
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Pincode <mand>*</mand></label>
                      <div class="col-md-12">
                        <?php

                          $pincode='';
                          if(isset($item_data['ASSREQ_BASIC_DETAIL_FLG']) == ""  && $item_data['ASSREQ_BASIC_DETAIL_FLG'] == "") {
                            $pincode = (isset($kycDetails['pincode']) && $kycDetails['pincode'] != NULL) ? $main_app->strsafe_output($kycDetails['pincode']) : NULL;
                          }
                          echo '<input type="text" name="PINCODE" id="PINCODE" maxlength="" class="form-control border-input reset-form js-isNumeric" value="'.$pincode.'" autocomplete="none" >';
                        
                        ?>
                    
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Address <mand>*</mand></label>
                      <div class="col-md-12">
                        <?php

                          //if basic details  updated in table then display otherwise aadhar api stored display data
                          $combined_address='';
                          //if(isset($item_data['ASSREQ_BASIC_DETAIL_FLG']) && $item_data['ASSREQ_BASIC_DETAIL_FLG']== "") {
                          if(isset($item_data['ASSREQ_BASIC_DETAIL_FLG']) == ""  && $item_data['ASSREQ_BASIC_DETAIL_FLG'] == "") {
                            $combined_address = (isset($kycDetails['combinedAddress']) && $kycDetails['combinedAddress'] != NULL) ? $main_app->strsafe_output($kycDetails['combinedAddress']) : NULL;
                          }

                          echo '<textarea name="CUST_ADDRESS" id="CUST_ADDRESS" class="form-control border-input" rows="5" autocomplete="none" readonly>'.$combined_address.'</textarea>';

                        ?>
                    
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Weaker Section Code</label>
                      <div class="col-md-12">
                     
                        <select name="WEAKER_SEC_CODE" id="WEAKER_SEC_CODE" class="form-control border-input" autocomplete="off">
                          <option value="">-- Select --</option>
                          <option value="1">123</option>
                          <?php
                            // $sql_exe = $main_app->sql_run("select * from cbuat.wksec w where w.wksec_code<>'99'");
                            // while ($row = $sql_exe->fetch() ) {
                            //   echo '<option value="'.$row['WKSEC_CODE'].'">'. $row['WKSEC_DESCN'] .'</option>';
                            // }
                          ?>
                        </select>

                      </div>
                    </div>

                   
                    <div class="col-md-4 form-group">
                      
                      <label class="col-md-12 label_head">Source Employee ID </label>
                      <div class="col-md-12">
                     
                        <select name="SOURCE_EMPID" id="SOURCE_EMPID" class="form-control border-input" autocomplete="off">
                          <option value="">-- Select --</option>
                          <option value="1">123</option>
                          <?php


                            // $sql_exe1 = $main_app->sql_run("select e.memp_num,e.memp_name,br.mbrn_code||'-'||br.mbrn_name  as Branchcode from cbuat.memp e 
                            // join cbuat.users u on u.user_id=e.memp_num join cbuat.mbrn br on br.mbrn_entity_num='1' and br.mbrn_code=u.user_branch_code
                            // left join cbuat.empsusprel er on er.empsusprel_user_id=e.memp_num where er.empsusprel_user_id is null and  br.mbrn_code= '".$_SESSION['BRANCH_CODE']."' ");
                            // while ($row1 = $sql_exe1->fetch() ) {
                            //   echo '<option value="'.$row1['MEMP_NUM'].'">'. $row1['MEMP_NUM'] .' ['. $row1['MEMP_NAME'] . ']</option>';
                            // }

                          ?>
                        </select>
                      </div>

                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Initial Deposit section <mand>*</mand></label>
                      <div class="col-md-12">
                        <select name="INITIAL_DEPOSIT" id="INITIAL_DEPOSIT" class="form-control border-input" autocomplete="none">
                        <option value="">-- Select --</option>
                        <option value="Cash">Cash</option>
                        <option value="NEFT/RTGS">NEFT/RTGS</option>
                        <option value="UPI">UPI</option>
                        <option value="IMPS">IMPS</option>
                        <!-- <?php
                          $sql_exe = $main_app->sql_run("SELECT INCSLAB_CODE, INCSLAB_DESCN FROM INCOMESLAB ORDER BY INCSLAB_DESCN ASC");
                          while ($row = $sql_exe->fetch()) {
                              echo "<option value=".$row['INCSLAB_CODE'].">".$row['INCSLAB_DESCN']."</option>";
                          }
                        ?> -->
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4 form-group">
                      <label class="col-md-12 label_head">Amount <mand>*</mand></label>
                      <div class="col-md-12">
                        <?php

                          echo '<input type="text" name="AMOUNT_DEPOSIT" id="AMOUNT_DEPOSIT" maxlength="11" class="form-control border-input reset-form js-isnodecNumeric" autocomplete="none" >';
                        
                        ?>
                    
                      </div>
                    </div>

                  </div>
                    
                  <div class="col-md-12 form-group text-center mt-1">

                   <?php
                    echo '<button type="button" class="btn btn-secondary px-3 py-2" onclick=gobackbut("'.$main_app->strsafe_input($enc_assref_num).'");> Go Back</button>'; 
                    ?>
                    <button type="submit" class="btn btn-primary h-btn3 m-0 px-4 py-2" id="sbt" name="sbt" tabindex="3" onclick="send_form('form-customer-details', 'sbt');">Next <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>

                  </div>

                <!-- </div> -->
                </form>
              </div>
            </div>

          </div>
        </div>
      <?php }  ?>                             
    </div>
  </div>
  
</section>


<!-- Content : End -->

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>

  <script type="text/javascript">

     function gobackbut(ass_ref_num) {
      goto_url('form-branch-details?ref_Num='+ass_ref_num);
    }

    
    $("#MARITAL_STATUS").on('change', function(){
      var mar_status = $('#MARITAL_STATUS').val();
      if(mar_status == "M") {
        show('spouse_name');
      } else {
        $('#SPOUSE_NAME').val('');
        hide('spouse_name');
      }
    });


  $(document).on("change", "#AMOUNT_DEPOSIT", function () {
      var inputVal = $(this).val();
      // if(parseInt(inputVal) == 0) {
      //   swal.fire('', 'Invalid Request');
      // }
      $(this).val(inrFormat(inputVal));
  });

    // Convert to INR format
    function inrFormat(val) {
        var x = val;
        //var x = parseFloat(val).toFixed(2);
        x = x.toString();
        var afterPoint = '';
        if (x.indexOf('.') > 0)
            afterPoint = x.substring(x.indexOf('.'), x.length);
        x = Math.floor(x);
        x = x.toString();
        var lastThree = x.substring(x.length - 3);
        var otherNumbers = x.substring(0, x.length - 3);
        if (otherNumbers != '')
            lastThree = ',' + lastThree;
        var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;
        return res;
    }


    $(document).ready(function(){

      hide('spouse_name');
      <?php
        if(isset($item_data1['ASSREQ_CUST_FIRST_NAME']) && $item_data1['ASSREQ_CUST_FIRST_NAME'] != ""  && $item_data1['ASSREQ_PLACE_OF_BIRTH'] != "") {
          echo "$('#CUST_FIRST_NAME').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_CUST_FIRST_NAME'])."'));"; 
        } 

        if(isset($item_data1['ASSREQ_CUST_MIDDLE_NAME']) && $item_data1['ASSREQ_CUST_MIDDLE_NAME'] != ""  && $item_data1['ASSREQ_PLACE_OF_BIRTH'] != "") {
          echo "$('#CUST_MIDDLE_NAME').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_CUST_MIDDLE_NAME'])."'));"; 
        } 

        if(isset($item_data1['ASSREQ_CUST_LAST_NAME']) && $item_data1['ASSREQ_CUST_LAST_NAME'] != ""  && $item_data1['ASSREQ_PLACE_OF_BIRTH'] != "") {
          echo "$('#CUST_LAST_NAME').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_CUST_LAST_NAME'])."'));"; 
        } 

        if(isset($item_data1['ASSREQ_PLACE_OF_BIRTH']) && $item_data1['ASSREQ_PLACE_OF_BIRTH'] != ""  && $item_data1['ASSREQ_PLACE_OF_BIRTH'] != "") {
          echo "$('#PLACE_OF_BIRTH').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_PLACE_OF_BIRTH'])."'));"; 
        } 

        if(isset($item_data1['ASSREQ_OCCUPATION_CODE']) && $item_data1['ASSREQ_OCCUPATION_CODE'] != "") {
          echo "$('#OCCUPATION').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_OCCUPATION_CODE'])."'));"; 
        } 

        if(isset($item_data1['ASSREQ_ANNUAL_INCOME']) && $item_data1['ASSREQ_ANNUAL_INCOME'] != "") {
          echo "$('#ANNUAL_INCOME').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_ANNUAL_INCOME'])."'));"; 
        }

	      if(isset($item_data1['ASSREQ_FATHERSNAME']) && $item_data1['ASSREQ_FATHERSNAME'] != "") {
          echo "$('#FATHERS_NAME').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_FATHERSNAME'])."'));"; 
        }

        if(isset($item_data1['ASSREQ_MOTHERSNAME']) && $item_data1['ASSREQ_MOTHERSNAME'] != "") {
          echo "$('#MOTHERS_NAME').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_MOTHERSNAME'])."'));"; 
        }

        if(isset($item_data1['ASSREQ_MARITAL_STATUS']) && $item_data1['ASSREQ_MARITAL_STATUS'] != "") {
          echo "$('#MARITAL_STATUS').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_MARITAL_STATUS'])."'));"; 
        }

        if(isset($item_data1['ASSREQ_RELIGION_CODE']) && $item_data1['ASSREQ_RELIGION_CODE'] != "") {
          echo "$('#RELIGION').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_RELIGION_CODE'])."'));"; 
        }
        
        if(isset($item_data1['ASSREQ_QUALIFICATION']) && $item_data1['ASSREQ_QUALIFICATION'] != "") {
          echo "$('#QUALIFICATION').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_QUALIFICATION'])."'));"; 
        }

        if(isset($item_data1['ASSREQ_DBTCHECK']) && $item_data1['ASSREQ_DBTCHECK'] != "") {
          echo "$('#DBT_BENEFICIARY').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_DBTCHECK'])."'));"; 
        }

        if(isset($item_data1['ASSREQ_ADDRESS']) && $item_data1['ASSREQ_ADDRESS'] != "") {
          echo "$('#CUST_ADDRESS').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_ADDRESS'])."'));"; 
        }

       
        if(isset($item_data1['ASSREQ_EDIT_ADDRESS']) && $item_data1['ASSREQ_EDIT_ADDRESS'] != "") {
          
          $editaddress = json_decode($item_data1['ASSREQ_EDIT_ADDRESS'], true);
          $EHOUSENUMBER =  isset($editaddress['HOUSENUMBER']) ? $editaddress['HOUSENUMBER'] : "";
          $ESTREETADDRESS = isset($editaddress['STREETADDRESS']) ? $editaddress['STREETADDRESS'] : "";
          $EDISTRICT = isset($editaddress['DISTRICT']) ? $editaddress['DISTRICT'] : "";
          $ESTATE = isset($editaddress['STATE']) ? $editaddress['STATE'] : "";
          $EPINCODE = isset($editaddress['PINCODE']) ? $editaddress['PINCODE'] : "";
  
           
          echo "$('#HOUSENUMBER').val(deStr('".$main_app->strsafe_modal($EHOUSENUMBER)."'));"; 
          echo "$('#STREETADDRESS').val(deStr('".$main_app->strsafe_modal($ESTREETADDRESS)."'));"; 
          echo "$('#DISTRICT').val(deStr('".$main_app->strsafe_modal($EDISTRICT)."'));"; 
          echo "$('#STATE').val(deStr('".$main_app->strsafe_modal($ESTATE)."'));"; 
          echo "$('#PINCODE').val(deStr('".$main_app->strsafe_modal($EPINCODE)."'));"; 
 
        }
 
        if(isset($item_data1['ASSREQ_WEAKER_CODE']) && $item_data1['ASSREQ_WEAKER_CODE'] != "") {
          echo "$('#WEAKER_SEC_CODE').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_WEAKER_CODE'])."'));"; 
        }

        if(isset($item_data1['ASSREQ_SOURCE_EMPID']) && $item_data1['ASSREQ_SOURCE_EMPID'] != "") {
          echo "$('#SOURCE_EMPID').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_SOURCE_EMPID'])."'));"; 
        }

        if(isset($item_data1['ASSREQ_INITIAL_DEPOSIT']) && $item_data1['ASSREQ_INITIAL_DEPOSIT'] != "") {
          echo "$('#INITIAL_DEPOSIT').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_INITIAL_DEPOSIT'])."'));"; 
        }

        if(isset($item_data1['ASSREQ_AMT_DEPOSIT']) && $item_data1['ASSREQ_AMT_DEPOSIT'] != "") {
          echo "$('#AMOUNT_DEPOSIT').val(deStr('".$main_app->strsafe_modal($main_app->money_format_INR($item_data1['ASSREQ_AMT_DEPOSIT']))."'));"; 
        }

       // if(isset($item_data1['ASSREQ_MARITAL_STATUS']) && $item_data1['ASSREQ_MARITAL_STATUS'] == "M" && isset($kycDetails['husbandName']) =="" ) { 
        if(isset($item_data1['ASSREQ_MARITAL_STATUS']) && $item_data1['ASSREQ_MARITAL_STATUS'] == "M") {   
          if(isset($item_data1['ASSREQ_SPOUSE_NAME']) && $item_data1['ASSREQ_SPOUSE_NAME'] != "") {
            echo "$('#SPOUSE_NAME').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_SPOUSE_NAME'])."'));"; 
          } ?>
          show('spouse_name');
        <?php } ?>

    });

  </script>