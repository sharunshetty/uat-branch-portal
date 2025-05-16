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
    $page_pgm_code = "CUSTAADHAR";
    $page_title = "Aadhaar eKYC Verification";
    $page_link = "";

    $parent_page_title = "";
    $parent_page_link = "./main-assistant";

    /** Table Settings */
    $page_table_name = "ASSREQ_MASTER";
    $primary_key = "ASSREQ_REF_NUM";

    /** Page Header */
    require( dirname(__FILE__) . '/../theme/app-header.php' );

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
            $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
            $item_data = $sql_exe->fetch();  
            if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
                $errorMsg = "E01 : Invalid request.";
            }
        }
    }
?>

<?php 
    if(isset($errorMsg) && $errorMsg == "") {
        echo "<div class='abp-heading text-muted'>Account Ref No: <span class='text-danger'>$assref_num</span></div>";
    }
?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php if(isset($errorMsg) && $errorMsg != "") { ?>
                <div class="col-md-12 text-danger text-center mt-5 pt-5 h5"><?php echo $main_app->strsafe_output($errorMsg); ?></div>
            <?php } else { ?>

                <div class="col-md-12 col-lg-12 form-group" id="customer-details" >
                    <div class="card card-outline card-brand">
                        <div class="card-body min-high2">

                            <ul class="nav nav-tabs" id="myTab" role="tablist" style="display: none;" >
                                <li class="nav-item"><a class="nav-link active" id="tab-nav-1" data-toggle="tab" href="#tab-1" role="tab" aria-controls="tab-1" aria-selected="true">Aadhar / VID Number Details</a></li>
                                <li class="nav-item"><a class="nav-link" id="tab-nav-2" data-toggle="tab" href="#tab-2" role="tab" aria-controls="tab-2" aria-selected="false">OTP Details</a></li>
                                <li class="nav-item"><a class="nav-link" id="tab-nav-3" data-toggle="tab" href="#tab-3" role="tab" aria-controls="tab-3" aria-selected="false">Aadhaar Details</a></li>
                            </ul>

                            <div class="tab-content box-min-h w-100 mb-4" id="myTabContent"><!--py-3 -->
                            
                                <!-- Tab 1 : Start -->
                                <div class="tab-pane show active" id="tab-1" role="tabpanel" aria-labelledby="tab-1">
                                    <div class="row">

                                        <div class="col-md-12 col-lg-12 text-center my-4">
                                            <div class="h5 txt-c1 mt-4">Please enter your Aadhaar Number</div>              
                                        
                                            <form name="aadhaar-otp" id="aadhaar-otp" method="post" action="javascript:void(0);" class="form-material">
                                                <input type="hidden" id="asnVal" name="asnVal" value="<?php echo $main_app->strsafe_input($enc_assref_num);?>" />
                                               <input type="hidden" id="req_key" name="req_key"  value="<?php echo $safe->rsa_public_key();?>" />                
                                                <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>
                                            
                                                <div class="row justify-content-center mt-4">
                                                    <div class="col-md-5 col-lg-4 form-group">
                                                        <div class="input_div">
                                                            <input type="text" id="ekycNum" name="ekycNum" placeholder="12 Digit UID" maxlength="12" class="form-control border-input js-noSpace js-isNumeric" autocomplete="off">
                                                        </div>

                                                        <div class="mt-4">
                                                            <div class="col-md-12">
                                                                <label for="AadhAgree" class="ml-1 label_head"> </label>
                                                                <input type="checkbox" name="AadhAgree" id="AadhAgree"  class="form-radio checkbox"> I agree to Terms & Conditions.<mand>*</mand>    
                                                            </div>

                                                            <!-- The Disclaimer Modal -->
                                                            <div class="modal fade" id="TermsPopup" tabindex="-1" role="dialog" aria-labelledby="TermsLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h6 class="modal-title" id="NeedHelpLabel">Terms &amp; Conditions</h6>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body text-dark"> <!--max-h-280-->
                                                                            <div class="row">
                                                                                <div class="col-md-12 text-justify small">
                                                                                <p>A. I hereby provide my voluntary consent to Capital Small Finance Bank to use the Aadhaar details provided by me for authentication and agree to the terms and conditions related to Aadhaar consent and updation.</p>
                                                                                <p>B. I am aware that there are various alternate options provided by Capital Small Finance Bank (“Bank”) for establishing my iden tity/address proof for opening a Savings Account and agree and confirm that for opening the online Savings Account, I have voluntarily submitted my Aadhaar number to the Bank and hereby give my consent to the Bank:- (i) to establish my identity / address proof and verify my mobile number by Aadhaar based authentication system through biometric and/or One Time Pin (OTP) and/or Yes/No authentication and/or any other authentication mechanism) independently or verify the genuineness of the Aadhaar through such manner as set out by UIDAI or any other law from time to time; (ii) share my Aadhaar detail with UIDAI, NPCI, concerned regulatory or statutory authorities as any be required under applicable laws. (iii) to collect, store and use the Aadhaar details for the aforesaid purpose(s) and update my mobile number registered with UIDAI in the bank records for sending SMS alerts/other communications to me.</p>
                                                                                <p>C. I hereby also agree with the belo w terms pertaining to Aadhaar based authentication/verification:
                                                                                    <ol style="padding-left: 25px;">
                                                                                    <li>I have been informed that: (a) upon authentication, UIDAI may share with Capital Small Finance Bank information in nature of my demographic information including photograph, mobile number which Capital Small Finance Bank may use as an identity/address proof for the purpose of account opening;(b) my Aadhaar details (including my demographic information) shared by UIDAI will not be used for any purpose other than the purpose mentioned above or as per requirements of law; (c) my biometric information will not be stored by the Bank.</li>
                                                                                    <li>I hereby declare that all the above information voluntarily furnished by me is true, correct and complete in all respects.</li>
                                                                                    <li>I understand that Capital Small Finance Bank shall be relying upon the information received from UIDAI for processing my Savings Account opening formalities.</li>
                                                                                    </ol>
                                                                                </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button type="button" class="btn btn-sm btn-success px-3" onClick="$('#AadhAgree').prop('checked', true); $('#TermsPopup').modal('hide');">I Agree</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                       </div> 
                                                    </div>                                                 
                                                </div>

                                                <div class="text-muted small mt-3">Aadhaar 12 digit individual identification number issued by the Unique Identification Authority Of India (UIDAI) on behalf of the Government of India.</div>
                                    
                                                <div class="mt-5 text-center">
                                                    <?php 
                                                         echo '<button type="button" class="btn btn-secondary px-3 py-2"  onclick=gobackbut("'.$main_app->strsafe_input($enc_assref_num).'");><i class="mdi mdi-arrow-left"></i>  Go Back</button>';                                                                                                             
                                                         echo '<button type="submit" class="btn btn-primary px-3 py-2 ml-1"  id="sbt2" name="sbt2">Next  <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button> ';                               
                                                  ?>
                                                </div>
                                                                                    
                                            </form>    
                                        </div>            
                                    </div>
                                </div>
                                <!-- Tab 1 : End -->

                                <!-- Tab 2 : Start -->
                                <div class="tab-pane fade" id="tab-2" role="tabpanel" aria-labelledby="tab-2">
                                    <div class="row justify-content-center">

                                        <div class="col-md-7 col-lg-6 text-center my-4">

                                            <div class="h5 txt-c1 mt-4">Aadhaar OTP Verification</div>
                                            <div class="text-muted small">Please enter OTP Code received on your Aadhaar registered mobile number</div>

                                            <form name="aadhaar-verify" id="aadhaar-verify" method="post" action="javascript:void(0);" class="form-material">
                                                <input type="hidden" id="req_key2" name="req_key2" />
                                                <input type="hidden" id="ekycNum2" name="ekycNum2" />
                                                <input type="hidden" id="asnVal2" name="asnVal2" />

                                                <div class="row justify-content-center mt-4">
                                                    <div class="col-md-5 form-group">
                                                        <div class="input_div">
                                                            <input type="text" id="ekycOtp" name="ekycOtp" placeholder="X X X X X X" maxlength="6" class="form-control border-input js-noSpace js-isNumeric text-center" autocomplete="off">
                                                        </div>
                                                    </div>
                                               </div>

                                                <div class="mt-4 text-center">
                                                        <button type="button" class="btn btn-secondary px-3 py-2" onclick="btnOtpScn();" ><i class="mdi mdi-arrow-left"></i> Go Back</button>                             
                                                        <button type="submit" class="btn btn-primary px-3 py-2"  id="sbt2" name="sbt2">Next  <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>             
                                                </div>

                                            </form>

                                            <!-- <div class="text-muted small mt-3">Aadhaar 12 digit individual identification number issued by the <br/>Unique Identification Authority Of India (UIDAI) on behalf of the Government of India.</div> -->
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- Tab 2 : End -->



                            </div>  
                        </div>  
                    </div>
                </div>

            <?php }  ?>
        </div>
    </div>
</section>

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>


  <script type="text/javascript">
   
    // Form OTP - 1
    $("#aadhaar-otp").submit(function(e) {
        e.preventDefault();
    }).validate({
        rules: {
        ekycNum: { required: true, minlength: 12 },
        AadhAgree :   { required : true }
        },
        messages: {
        ekycNum: {
            minlength: "Enter valid 12 digit Aadhaar number"
        },
        AadhAgree : {
            required : "Please accept the Terms & Conditions."
        }
        },
        errorPlacement: function (error, element) {
            if(element.closest('.input_div').length) {
                error.insertAfter(element.closest('.input_div'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: ekycOtp
    });
                                                                            

    function gobackbut(ass_ref_num) {
        goto_url('ass-customer-detail?ref_Num='+ass_ref_num);
    }

    //BackBtn-1
    function btnOtpScn() {
      $('#tab-nav-1').trigger('click');
      $('#ekycOtp').val('');
      $('#AadhAgree').prop('checked', false);
      
    }
    function ekycOtp() {
        var ekycNum = $('#ekycNum').val();
        var asnVal = $('#asnVal').val();
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('#req_key').val());
        var safeData = {
            cmd : "ass_form_aadhaarotp",
            token : window.req_id,
            asnVal : asnVal,
            ekycNum : encrypt.encrypt(ekycNum),
        }
        disable('sbt');
        loader_start();
        post_safe_data(safeData);

    }

      // Form Verification - 2
    $("#aadhaar-verify").submit(function(e) {
        e.preventDefault();})
        .validate({
        rules: {
          ekycOtp: { required: true, minlength: 6 }
        },
        messages: {
          ekycOtp: {
              required: "Please enter OTP Code",
              minlength: "Enter valid 6 digit OTP Code"
          },
        },
        errorPlacement: function (error, element) {
            if(element.closest('.input_div').length) {
                error.insertAfter(element.closest('.input_div'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: ekycVerify
    });

    function ekycVerify() {
      var ekycOtp = $('#ekycOtp').val();
      var reqid = $('#req_key2').val();
      var ekycNum2 = $('#ekycNum2').val();
      var asnVal = $('#asnVal2').val();
      var encrypt = new JSEncrypt();
      encrypt.setPublicKey($('#req_key').val());
      var safeData = {
        cmd : "ass_form_aadhaarverify",
        token : window.req_id,
        ekycOtp : encrypt.encrypt(ekycOtp),
        reqid : reqid,
        asnVal : asnVal,
        ekycNum : ekycNum2,
      }
      disable('sbt2');
      loader_start();
      post_safe_data(safeData,'sbt2');
    }

   
    $('#AadhAgree').on('click', function(e){
      if(e.target.checked) {
        e.preventDefault();
        $('#TermsPopup').appendTo("body").modal({ show: true, backdrop: 'static', keyboard: true, show: true });
      }
    });

</script>

