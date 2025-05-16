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
    $page_pgm_code = "CUSTMOBVERIFY";
    $page_title = "Mobile Number Verification";
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
            $enc_assref_num = $safe->str_encrypt( $assref_num, $_SESSION['SAFE_KEY']);

            $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
            $item_data = $sql_exe->fetch();  
            if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
                $errorMsg = "E01 : Invalid request.";
            }elseif (!isset($item_data['ASSREQ_MOBILE_NUM']) || $item_data['ASSREQ_MOBILE_NUM'] == NULL || $main_app->valid_mobile($item_data['ASSREQ_MOBILE_NUM']) == false) {
                $errorMsg = "E02 : Invalid request.";
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
                                            <div class="h5 txt-c1 mt-4">Customer Mobile Number Verification</div>              
                                        
                                            <form name="custmob-otp" id="custmob-otp" method="post" action="javascript:void(0);" class="form-material">
                                                <input type="hidden" id="asnVal" name="asnVal" value="<?php echo $main_app->strsafe_input($enc_assref_num);?>" />
                                                <input type="hidden" id="req_key" name="req_key"  value="<?php echo $safe->rsa_public_key();?>" />                
                                                <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>
                                            
                                                <div class="row justify-content-center mt-4">
                                                    <div class="col-md-4 col-lg-3 form-group">
                                                        <div class="input_div">
                                                            <input type="text" name="CUST_MOBILE" id="CUST_MOBILE" placeholder="10 Digit Number" maxlength="10" class="form-control border-input js-isNumeric" autocomplete="off" readonly>
                                                        </div>
                                                    </div>                                                 
                                                </div>

                                                <div class="mt-5 text-center">
                                                    <?php 
                                                       echo '<button type="button" class="btn btn-secondary px-3 py-2"  onclick=gobackbut("'.$main_app->strsafe_input($enc_assref_num).'");> Go Back</button>';                              
                                                      // echo '<button type="button" class="btn btn-primary px-3 py-2 ml-1" name="sbt" id="sbt"  >Next <i class="mdi mdi-arrow-right"></i></button>';                                                    
                                                       echo '<button type="submit" class="btn btn-primary px-3 py-2 ml-1"  id="sbt2" name="sbt2" onclick="sendMobOtp(); return false;">Next  <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button> ';                               
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

                                            <div class="h5 txt-c1 mt-4">Customer Mobile Number OTP Verification</div>
                                            <div class="text-muted small">Please enter OTP Code received on your registered mobile number</div>

                                            <form name="custmob-verify" id="custmob-verify" method="post" action="javascript:void(0);" class="form-material">
                                                <input type="hidden" id="asnVal2" name="asnVal2" value="<?php echo $main_app->strsafe_input($enc_assref_num); ?>" />
                                                <!-- <input type="hidden" id="req_key2" name="req_key2"  value="<?php echo $safe->rsa_public_key();?>" />  -->
                                                <input type="hidden" id="mobNum2" name="mobNum2" />                
                                                <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>
                                            
                                                <div class="row justify-content-center mt-4">
                                                    <div class="col-md-5 form-group">
                                                        <div class="input_div">
                                                            <input type="text" id="custmobOtp" name="custmobOtp" placeholder="X X X X X X" maxlength="6" class="form-control border-input js-noSpace js-isNumeric text-center" autocomplete="off">
                                                        </div>

                                                        <span id="MOB_OTPCOUNTER" class="label_head mt-2" style="float: left;"></span>
					                                    <span id="MOB_RESENDOTP" class="mt-2" style="float: right;"><a href="#" class="label_head" style="text-decoration: underline;" onclick="sendMobOtp();">Resend OTP</a></span>

                                                    </div>

                                                    <!-- <div class="col-md-12 form-group">
                                                        <button type="submit" class="btn h-btn3 m-0 px-4 py-2" id="sbt2" name="sbt2" tabindex="7">Verify OTP  <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <a href="javascript:void(0);" onclick="btnOtpScn();" class="text-danger"><i class="mdi mdi-arrow-left"></i> Go back & Regenerate OTP</a>
                                                    </div> -->
                                                </div>

                                                <div class="mt-4 text-center">
                                                    <button type="button" class="btn btn-secondary px-3 py-2" onclick="btnOtpScn();" >Go Back</button>                             
                                                    <!-- <button type="submit" class="btn btn-primary px-3 py-2"  id="sbt2" name="sbt2">Next  <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>              -->
                                                    <!-- name="mobsbt" id="mobsbt" -->
                                                    <button type="submit" class="btn btn-primary px-3 py-2"  id="sbt2" name="sbt2" >Next  <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>             
                                                </div>

                                                <!--                                                 
                                                <div class="mt-4 text-left">
                                                    <ul class="small text-danger pl-3">
                                                       <li class="mb-1">An OTP will be valid for 1 minutes after which it will expire. If you do not receive your OTP code within 2 minutes, Please click on Resend/Logout and try again.</li>
                                                    </ul>
                                                </div>
                                                -->

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


     $(document).ready(function(){

        <?php
  
            if(isset($item_data['ASSREQ_MOBILE_NUM']) && $item_data['ASSREQ_MOBILE_NUM'] != "") {
                echo "$('#CUST_MOBILE').val(deStr('".$main_app->strsafe_modal($item_data['ASSREQ_MOBILE_NUM'])."'));"; 
            } 

        ?>

    });
            

   
    // Form OTP - 1
                                                                    
    function gobackbut(ass_ref_num) {
        goto_url('ass-formfinal-detail?ref_Num='+ass_ref_num);
    }

    // function sendMobOtp() {
	// 	var mob_num = $("#CUST_MOBILE").val();
	// 	if(mob_num == "") {
	// 		swal.fire('','Mobile Number is Incorrect');
	// 		focus('CUST_MOBILE');
	// 	} else {
		
    //         //mobstartCounter();
    //         on_change('user-mob-otpsend','modify',mob_num,'MOBILE');
			
	// 	}
	// }

    //BackBtn-1
    function btnOtpScn() {
      $('#tab-nav-1').trigger('click');
    }

    function sendMobOtp() {
        var mobnum = $("#CUST_MOBILE").val();
		if(mobnum == "") {
			swal.fire('','Mobile Number is Incorrect');
			focus('CUST_MOBILE');
		} else {
            on_change('user-mob-otpsend','modify',mobnum,'CUSTMOB');
            // var asnVal = $('#asnVal').val();
            // var encrypt = new JSEncrypt();
            // encrypt.setPublicKey($('#req_key').val());
            // var safeData = {
            //     cmd : "ass_form_custmobotp",
            //     token : window.req_id,
            //     asnVal : asnVal,
            //     mobnum : encrypt.encrypt(mobnum),
            // }
            // disable('sbt2');
            // loader_start();
            // post_safe_data(safeData);

        }
    }

    // Form Verification - 2

    //Counter

	function mobstartCounter() {
		// disable('ResendmobBtn');
		$("#MOB_RESENDOTP").hide();  
		$("#MOB_OTPCOUNTER").show();  
		var mobcounter = 60;
		var mobbtn = document.getElementById("MOB_OTPCOUNTER");//ResendmobBtn
		mobtimer = setInterval(function(){
			mobcounter--;
			if(mobcounter > 1) {
				mobbtn.innerHTML = mobcounter + " seconds left";
				//disable('mobsbt');
			}
			else if(mobcounter == 1) {
				mobbtn.innerHTML = mobcounter + " second left";
				//disable('mobsbt');
			}
			else {
				clearInterval(mobtimer);
				$("#MOB_RESENDOTP").show();  
				$("#MOB_OTPCOUNTER").hide();  
				//mobbtn.innerHTML = "Resend OTP";
				//enable('mobsbt');
			}
		},"1000");
    }

   // function resendSmsOtp() {
   //     on_change('user-mob-otpsend','modify',mobnum,'CUSTMOB');
   // }

    $("#custmob-verify").submit(function(e) {
        e.preventDefault();})
        .validate({
        rules: {
            custmobOtp: { required: true, minlength: 6 }
        },
        messages: {
            custmobOtp: {
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
        submitHandler: MobOtpverify
    });

    function MobOtpverify() {
      var mobotp = $('#custmobOtp').val();
    //   var reqid = $('#req_key2').val();
      var mobNum = $('#mobNum2').val();
      var asnVal = $('#asnVal2').val();
      var encrypt = new JSEncrypt();
      encrypt.setPublicKey($('#req_key').val());
      var safeData = {
        cmd : "ass-form-custmobverify",
        token : window.req_id,
        mobotp : encrypt.encrypt(mobotp),
        asnVal : asnVal,
        mobNum : mobNum,
      }
      disable('sbt2');
      loader_start();
      post_safe_data(safeData,'sbt2');

    }



</script>

