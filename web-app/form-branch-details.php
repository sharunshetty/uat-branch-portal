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

  $page_title = "Branch Details";
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
   
    //final view edit part in form final details page
  

    if(!isset($assref_num) || $assref_num== false || $assref_num == "") {
        $errorMsg = "Invalid URL Request";
    } else {

      $assref_num = $main_app->strsafe_output($assref_num);
      $enc_assref_num = $safe->str_encrypt( $assref_num, $_SESSION['SAFE_KEY']);
      
      $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_EKYC_FLAG, ASSREQ_PAN_FLAG,ASSREQ_BRANCH_FLAG FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
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
      

      if(isset($item_data['ASSREQ_BRANCH_FLAG']) || $item_data['ASSREQ_BRANCH_FLAG'] == "Y") {
        
        $sql_exe1 = $main_app->sql_run("SELECT *  FROM ASSREQ_ACCOUNTDATA WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
        $item_data1 = $sql_exe1->fetch();  
        if(!isset($item_data1['ASSREQ_REF_NUM']) || $item_data1['ASSREQ_REF_NUM']== false || $item_data1['ASSREQ_REF_NUM'] == NULL || $item_data1['ASSREQ_REF_NUM'] == "") {
          $errorMsg = "Unable to fetch application details";
        }  

      } 
      $sql_exe3 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' =>$assref_num));
      $kycDetails = $sql_exe3->fetch();

      if(isset($kycDetails['DOC_DATA']) && $kycDetails['DOC_DATA'] != "") {
       $kycDetails = json_decode(stream_get_contents($kycDetails['DOC_DATA']), true, JSON_UNESCAPED_SLASHES); 
      }
       $pincode = $kycDetails['pincode'];
       $pincode  = 144040;
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
                
                  <form name="form-branch-details" id="form-branch-details" method="post" action="javascript:void(0);" class="form-material">
                    <input type="hidden" name="cmd" value="form_branch_details">
                    <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>         
                    <input type="hidden" id="asnVal" name="asnVal" value="<?php echo $main_app->strsafe_input($enc_assref_num);?>"/>
                    <input type="hidden" id="brnFlg"  name="brnFlg" value="<?php echo (isset($item_data['ASSREQ_BRANCH_FLAG']) == 'Y') ? $safe->str_encrypt($item_data['ASSREQ_BRANCH_FLAG'], $_SESSION['SAFE_KEY']) : ""; ?>"/>   
                    <input type="hidden" id="pKey" value="<?php echo $safe->rsa_public_key();?>" />
 	                  <input type="hidden" id="pCode" value="<?php echo $pincode;?>" />

                    <div class="row mt-3 mb-4 d-flex flex-column align-items-center justify-content-center">

                      <div class="col-md-5 col-lg-4 form-group my-2">
                        <label class="col-md-12 label_head">State <mand>*</mand></label>
                        <div class="col-md-12">
                           <input type="text" name="STATE_NAME" id="STATE_NAME" class="form-control border-input text-uppercase" autocomplete="none" readonly>
                          <input type="hidden" name="STATE" id="STATE" value="">
                        </div>
                      </div> 

                      <div class="col-md-5 col-lg-4 form-group my-2">
                        <label class="col-md-12 label_head">City <mand>*</mand></label>
                        <div class="col-md-12">   
                           <input type="text" name="DISTRICT_NAME" id="DISTRICT_NAME" class="form-control border-input text-uppercase" autocomplete="off" readonly>
                           <input type="hidden" name="DISTRICT_CODE" id="DISTRICT_CODE" value="">                                             
                       </div>
                      </div>

            
                      <div class="col-md-5 col-lg-4 form-group my-2">
                        <label class="col-md-12 label_head">Branch code & Name<mand>*</mand></label>
                        <div class="col-md-12">
                           <!-- <input type="text" name="BRANCH_NAME" id="BRANCH_NAME" class="form-control border-input text-uppercase" autocomplete="off" readonly>
                          <input type="hidden" name="BRANCH_CODE" id="BRANCH_CODE" value="">  
                           -->
                          <select name="BRANCH_CODE" id="BRANCH_CODE" class="form-control border-input reset-form" autocomplete="off">     
                          <option value="1">dddddd</option>
                          </select>     
                        </div>
                      </div>

                      <div class="col-md-5 col-lg-4 form-group my-2">
                        <label class="col-md-12 label_head">Account Product <mand>*</mand></label>
                        <div class="col-md-12">
                          <select name="PRODUCT_CODE" id="PRODUCT_CODE" class="form-control border-input" autocomplete="none">
                            <option>--select--</option>
                            <?php
                              $sql_exe = $main_app->sql_run("SELECT PRODUCT_CODE, PRODUCT_DESC FROM ASSREQ_PRODUCT_CODE WHERE PRODUCT_STATUS = '1' ORDER BY PRODUCT_DESC ASC");
                              while ($row = $sql_exe->fetch()) {
                                echo "<option value=".$row['PRODUCT_CODE'].">". $row['PRODUCT_CODE'] . '-'.$row['PRODUCT_DESC']. "</option>";  

                              }
                            ?>
                          </select>
                          <a href="https://www.capitalbank.co.in/home/accounts/savings/normal-savings-account" target="_blank">Click here for Product Details</a>
                        </div>
                      </div>

                      <div class="col-md-5 col-lg-4 form-group my-2">
                        <label class="col-md-12 label_head">Account Sub-Type <mand>*</mand></label>
                        <div class="col-md-12">
                          <select name="ACNT_SUBTYP" id="ACNT_SUBTYP" class="form-control border-input" autocomplete="none">
                            <option>--select--</option>
                            <?php
                              $sql_exe = $main_app->sql_run("SELECT ACNTTYP_CODE, ACNTTYP_DESC FROM ASSREQ_ACNT_SUBTYP WHERE ACNTTYP_STATUS = '1' ORDER BY ACNTTYP_DESC ASC");
                              while ($row = $sql_exe->fetch()) {
                                echo "<option value=".$row['ACNTTYP_CODE'].">". $row['ACNTTYP_DESC'] ."</option>";  

                              }
                            ?>
                          </select>
                         </div>
                      </div>

                    </div>

                    <div class="col-md-12 form-group text-center mt-1">

                     <?php
                     
                      echo '<button type="button" class="btn btn-secondary px-3 py-2" onclick=gobackbut("'.$main_app->strsafe_input($enc_assref_num).'");> Go Back</button>'; 
                      ?>
                      <button type="submit" class="btn btn-primary h-btn3 m-0 px-4 py-2" id="sbt" name="sbt" tabindex="3" onclick="send_form('form-branch-details', 'sbt');">Next <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>
          
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
      goto_url('ass-custsign-camera?ref_Num='+ass_ref_num);
  }

  $(document).ready(function(){

    var pincode_val = $('#pCode').val();
    on_change('sb-enq-district','modify',pincode_val);

    <?php

      if(isset($item_data1['ASSREQ_PRODUCT_CODE']) && $item_data1['ASSREQ_PRODUCT_CODE'] != "") {
          echo "$('#PRODUCT_CODE').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_PRODUCT_CODE'])."'));"; 
      } 

      if(isset($item_data1['ASSREQ_ACNT_SUBTYP']) && $item_data1['ASSREQ_ACNT_SUBTYP'] != "") {
        echo "$('#ACNT_SUBTYP').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_ACNT_SUBTYP'])."'));"; 
    } 

      // if(isset($item_data1['ASSREQ_BRANCH_CODE']) && $item_data1['ASSREQ_BRANCH_CODE'] != "") {
      //   echo "$('#BRANCH_CODE').val(deStr('".$main_app->strsafe_modal($item_data1['ASSREQ_BRANCH_CODE'])."'));"; 
      // }

     
    ?> 
   
      
  });


    
</script>

