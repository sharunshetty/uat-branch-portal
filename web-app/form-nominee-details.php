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

$page_title = "Nominee Details";
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
    $enc_assref_num = $safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']);

    $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_EKYC_FLAG, ASSREQ_PAN_FLAG, ASSREQ_NOMINEE_FLG  FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
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

    if(isset($item_data['ASSREQ_NOMINEE_FLG']) || $item_data['ASSREQ_NOMINEE_FLG'] == "Y") {
      $sql_exe1 = $main_app->sql_run("SELECT *  FROM ASSREQ_ACCOUNTDATA WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
      $item_data1 = $sql_exe1->fetch();  
      if(!isset($item_data1['ASSREQ_REF_NUM']) || $item_data1['ASSREQ_REF_NUM']== false || $item_data1['ASSREQ_REF_NUM'] == NULL || $item_data1['ASSREQ_REF_NUM'] == "") {
        $errorMsg = "Unable to fetch application details";
      }
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
              <div class="col-md-12 col-lg-12 form-group" id="customer-details" >
                <div class="card card-outline card-brand">
                  <div class="card-body min-high2">
        
                    <form name="form-nominee-details" id="form-nominee-details" method="post" action="javascript:void(0);" class="form-material">
                      <input type="hidden" name="cmd" value="form_nominee_details">
                      <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>
                       <input type="hidden" id="asnVal" name="asnVal" value="<?php echo $main_app->strsafe_input($enc_assref_num);?>"/>
                       <!-- <input type="hidden" id="nomFlg"  name="nomFlg" value="<?php echo (isset($nomFlg)) ? $nomFlg : ""; ?>"/>             -->
                      <input type="hidden" id="pKey" value="<?php echo $safe->rsa_public_key();?>" />
                      <!-- <div class="row justify-content-center my-4"> -->

                      <div class="row mt-3 mb-4 mx-auto d-flex flex-column align-items-center justify-content-center">

                        <div class="col-md-5 col-lg-4 form-group my-2">
                          <label class="col-md-12 label_head">Nominee Name </label>
                          <div class="col-md-12">
                            <input type="text" name="NOMINEE_NAME" id="NOMINEE_NAME" class="form-control border-input reset-form js-alphaNumericspace" autocomplete="off">
                          </div>
                        </div>

                        <div class="col-md-5 col-lg-4 form-group my-2">
                          <label class="col-md-12 label_head">Date of birth </label>
                          <div class="col-md-12">
                            <?php
                              $date = date('Y-m-d');
                              echo'<input type="date" name="NOMINEE_DOB" id="NOMINEE_DOB"  max="'.$date.'" class="form-control border-input reset-form" autocomplete="none">';
                            ?>
                          </div>
                        </div>
                      
                        <div class="col-md-5 col-lg-4 form-group my-2">
                          <label class="col-md-12 label_head">Relation to the Account holder </label>
                          <div class="col-md-12">
                            <select name="NOMINEE_RELATION" id="NOMINEE_RELATION" class="form-control border-input" autocomplete="none">
                              <option value="">-- Select --</option>
                              <?php
                              $sql_exe = $main_app->sql_run("SELECT RELATION_CODE, RELATION_DESCN FROM RELATION ORDER BY RELATION_DESCN ASC");
                                while ($row = $sql_exe->fetch()) {
                                  echo "<option value=".$row['RELATION_CODE'].">".$row['RELATION_DESCN']."</option>";
                                }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-5 col-lg-4 form-group my-2">
                          <label class="col-md-12 label_head">Address </label>
                          <div class="col-md-12">
                            <textarea name="NOMINEE_ADDRESS" id="NOMINEE_ADDRESS" maxlength="250" rows="4" class="form-control border-input js-maxCheck js-noEnter no-resize js-alphaNumericspace" autocomplete="none"></textarea>
                          </div>
                        </div>
                      
                        <input type="hidden" name="NOMINEE_HIDDENAFLG" id="NOMINEE_HIDDENAFLG"/>

                        <div class="row" id="guarddetails" style="display: none;">
                          <div class="col-md-6 form-group my-2">
                            <label class="col-md-12 label_head">Nature of the Guardian</label>
                            <div class="col-md-12">
                              <select name="NOMINEE_NATURE" id="NOMINEE_NATURE" class="form-control border-input" autocomplete="off">
                                <option value="">-- Select --</option>
                                <option value="F"> Father</option>
                                <option value="M"> Mother </option>
                                <option value="O"> Others </option>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 form-group my-2">
                            <label class="col-md-12 label_head">Guardian Name </label>
                            <div class="col-md-12">
                              <input type="text" name="NOMINEE_GUARDIAN" id="NOMINEE_GUARDIAN" class="form-control border-input reset-form js-alphaNumericspace" autocomplete="off">
                            </div>
                          </div>

                        </div>

                      </div>

                      <div class="col-md-12 form-group text-center mt-1">

                                               
                         <?php   
                          echo '<button type="button" class="btn btn-secondary px-3 py-2" onclick=gobackbut("'.$main_app->strsafe_input($enc_assref_num).'");> Go Back </button>'                     
                        ?>
                        <button type="submit" class="btn btn-primary h-btn3 m-0 px-4 py-2" id="sbt" name="sbt" tabindex="3" onclick="send_form('form-nominee-details', 'sbt');">Next <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>
                     
                      </div>          
                      <!-- </div> -->
                    </form>
                 </div>
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
    goto_url('form-customer-details?ref_Num='+ass_ref_num);
  }  

  $("#MARITAL_STATUS").on('change', function(){
    var mar_status = $('#MARITAL_STATUS').val();
    if(mar_status == "2") {
      show('spouse_name');
    } else {
      hide('spouse_name');
    }
  });


  // $("#NOMINEE_DOB").load(function(){
  //   var nom_value = $('#NOMINEE_HIDDENAFLG').val();

  //   if(nom_value=='Y'){
  //     show('guarddetails');
  //   }else{
  //     hide('guarddetails');  
  //   }

  // });


  // CALCULATE NOMINEE AGE
  $("#NOMINEE_DOB").on('change', function(){
    
    var nom_age = $('#NOMINEE_DOB').val();
    
    if(nom_age!="") {
      var now = new Date();
      var nomage = new Date(nom_age);

      var nowYear = now.getFullYear();
      var nomAge = nomage.getFullYear();
      var age = nowYear - nomAge;
  
      if (age >= 18) {
        $('#NOMINEE_HIDDENAFLG').val('N');
        $('#NOMINEE_NATURE,#NOMINEE_GUARDIAN').val('');
        hide('guarddetails');  
      
      } else {
        $('#NOMINEE_HIDDENAFLG').val('Y');
        show('guarddetails');
      
      }
    }
    
  });

  $(document).ready(function(){

    // $("#OCCUPATIONS").val('').select2();
    // hide('spouse_name');
    hide('guarddetails');

    <?php

      if(isset($item_data1['ASSREQ_NOMINEE_NAME']) && $item_data1['ASSREQ_NOMINEE_NAME'] != "") {
        echo "$('#NOMINEE_NAME').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_NOMINEE_NAME'])."'));"; 
      } 

      if(isset($item_data1['ASSREQ_NOMINEE_DOB']) && $item_data1['ASSREQ_NOMINEE_DOB'] != "") {
        echo "$('#NOMINEE_DOB').val(deStr('".$main_app->strsafe_modal(date('Y-m-d',strtotime($item_data1['ASSREQ_NOMINEE_DOB'])))."'));"; 
      } 


      if(isset($item_data1['ASSREQ_NOMINEE_ADDRESS']) && $item_data1['ASSREQ_NOMINEE_ADDRESS'] != "") {
        echo "$('#NOMINEE_ADDRESS').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_NOMINEE_ADDRESS'])."'));"; 
      } 

      if(isset($item_data1['ASSREQ_NOMINEE_RELATION']) && $item_data1['ASSREQ_NOMINEE_RELATION'] != "") {
        echo "$('#NOMINEE_RELATION').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_NOMINEE_RELATION'])."'));"; 
      } 

      if(isset($item_data1['ASSREQ_GUARDIAN_NATURE']) && $item_data1['ASSREQ_GUARDIAN_NATURE'] != "") {
        echo "$('#NOMINEE_NATURE').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_GUARDIAN_NATURE'])."'));"; 
      } 

      if(isset($item_data1['ASSREQ_MINOR_FLAG']) && $item_data1['ASSREQ_MINOR_FLAG'] != "") {
        echo "$('#NOMINEE_HIDDENAFLG').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_MINOR_FLAG'])."'));"; 
      } 

      if(isset($item_data1['ASSREQ_NOMINEE_GUARDIAN']) && $item_data1['ASSREQ_NOMINEE_GUARDIAN'] != "") {
        echo "$('#NOMINEE_GUARDIAN').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_NOMINEE_GUARDIAN'])."'));"; 
      } 
      
      if(isset($item_data1['ASSREQ_MINOR_FLAG']) && $item_data1['ASSREQ_MINOR_FLAG']=='Y'){
    ?>
      show('guarddetails');

    <?php } ?>


  });

</script> 

