<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Sujith Kamath
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASS_BRNUSR_ID";
$primary_value = $_POST['id']; // Don't change

/** Get Tip */
if( isset($_POST['id']) && $_POST['id'] != "" ) {
	$sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE $primary_key = :primary_value",array('primary_value' => $primary_value));
	$item_data = $sql_exe->fetch();
}
?>

<?php if(isset($item_data) && $item_data) { $ModalLabel = "Update Basic Customer Details"; } else { $ModalLabel = "Add Basic Customer Details"; } ?>

<!-- Start -->
<form id="myform" name="myform" method="post" action="javascript:void(null);" class="form-material">
	<input type="hidden" name="cmd" id="cmd" value="new-customer-account"/>
	<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['APP_TOKEN'];?>"/>

	<div class="modal-body">
		<div class="row">

			<div class="col-md-4 form-group">
				<label class="col-md-12 label_head">Customer Title <mand>*</mand></label>
				<div class="col-md-12">
					<select name="CUST_TITLE" id="CUST_TITLE" class="form-control border-input js-noSpace" autocomplete="off" >
						<option value="">-- Select --</option>
						<option value="1">MR</option>
						<option value="2">MRS</option>
						<option value="3">MISS</option>
						<option value="7">Mx</option>
					</select>
				</div>
			</div>

			<div class="col-md-4 form-group">
				<label class="col-md-12 label_head">Full Name <mand>*</mand></label>
				<div class="col-md-12">
					<input type="text" name="CUST_FULLNAME" id="CUST_FULLNAME" placeholder="Full Name" class="form-control border-input js-alphaNumericspace js-toUpper" autocomplete="off">
				</div>
			</div>

			<div class="col-md-4 form-group">
				<label class="col-md-12 label_head">Gender <mand>*</mand></label>
				<div class="col-md-12">
					<select name="CUST_GENDER" id="CUST_GENDER" class="form-control border-input js-noSpace" autocomplete="off" >
						<option value="">-- Select --</option>
						<option value="M">Male</option>
						<option value="F">Female</option>
						<option value="T">Transgender</option>
					</select>
				</div>
			</div>

			<div class="col-md-6 form-group pad-r">
				<label class="col-md-12 label_head">Mobile Number <mand>*</mand> <small class="small">[Linked with aadhar]</small></label>
				<div class="col-md-12">
					<div class="input-group">
						<input type="text" name="CUST_MOBILE" id="CUST_MOBILE" placeholder="10 Digit Number" maxlength="10" class="form-control border-input js-isNumeric" autocomplete="off">
						<div class="input-group-append">
							<!-- <div class="input-group-text font-t1 mx-auto">Send OTP</div> -->
							<button  class="btn btn-warning btn-sm" name="mobsbt" id="mobsbt" onclick="sendMobOtp(); return false;">Send OTP</button>
						</div>
					</div>
					<span id="mobError" class="text-danger" style="display:none;">Invalid Mobile Number.</span>
              	</div>			
            </div>

			<div class="col-md-6 form-group">
				<div id="MOB_OTPENTRY" style="display:none;">
					<label class="col-md-12 label_head">Please enter the OTP sent on Mobile</label>	
					<input type="text" name="MOBVALID_OTP" id="MOBVALID_OTP" placeholder=""  class="form-control border-input" maxlength="6" autocomplete="off">				
					
					<span id="MOB_OTPCOUNTER" class="label_head mt-2" style="float: left;"></span>
					<span id="MOB_RESENDOTP" class="mt-2" style="float: right;"><a href="#" class="label_head" style="text-decoration: underline;" onclick="sendMobOtp();">Resend OTP</a></span>
				</div>
            </div>

			<div class="col-md-6 form-group pad-r">
				<label class="col-md-12 label_head">Email ID <mand>*</mand></label>
				<div class="col-md-12">
					<div class="input-group">
						<input type="text" name="CUST_EMAIL" id="CUST_EMAIL"  placeholder="Email ID" class="form-control border-input bg-ip-box js-noCopy js-character" autocomplete="off">
						<div class="input-group-append">
							<!-- <div class="input-group-text font-t1 mx-auto">Send OTP</div> -->
							<button  class="btn btn-warning btn-sm" name="mailsbt" id="mailsbt" onclick="sendEmailOtp(); return false;">Send Mail</button>
						</div>
					</div>
					<span id="emailError" class="text-danger" style="display:none;">Invalid Email ID.</span>
              	</div>	
			</div>

			<div class="col-md-6 form-group">
				<div id="MAIL_OTPENTRY" style="display:none;">
					<label class="col-md-12 label_head">Please enter OTP received on Email</label>	
					<input type="text" name="MAILVALID_OTP" id="MAILVALID_OTP" placeholder=""  class="form-control border-input" maxlength="6" autocomplete="off">	

					<span id="MAIL_OTPCOUNTER" class="label_head mt-2" style="float: left;"></span>
					<span id="MAIL_RESENDOTP" class="mt-2" style="float: right;"><a href="#" class="label_head" style="text-decoration: underline;" onclick="sendEmailOtp();">Resend</a></span>
				</div>
			</div>

			<div class="col-md-6 form-group">
              <label class="col-md-12 label_head">Referral Code <span class="text-muted small">(optional)</span></label>
              <div class="col-md-12">          
                <input type="text" name="REGREFERRAL" id="REGREFERRAL" placeholder="Referral Code" maxlength="120" class="form-control border-input js-noSpace" autocomplete="off">
              </div>
            </div>


			<!-- <div style="width: 100%;">
				<div id="MOB_OTPENTRY" style="float: left;width: 350px;margin: 0 20px;">
					<label class="col-md-12 label_head">Enter Mobile No OTP Number</label>	
					<input type="text" name="MOBVALID_OTP" id="MOBVALID_OTP" placeholder=""  class="form-control border-input" autocomplete="off">				
					
					<span id="MOB_OTPCOUNTER" class="label_head mt-2" style="float: left;"></span>
					<span id="MOB_RESENDOTP" class="mt-2" style="float: right;"><a href="#" class="label_head" style="text-decoration: underline;" onclick="sendMobOtp();">Resend OTP</a></span>
				</div>


				<div id="MAIL_OTPENTRY" style="float: right;width: 350px; margin: 0px 20px;">
					<label class="col-md-12 label_head">Enter Email ID OTP Number</label>	
					<input type="text" name="MAILVALID_OTP" id="MAILVALID_OTP" placeholder=""  class="form-control border-input" autocomplete="off">	

					<span id="MAIL_OTPCOUNTER" class="label_head mt-2" style="float: left;"></span>
					<span id="MAIL_RESENDOTP" class="mt-2" style="float: right;"><a href="#" class="label_head" style="text-decoration: underline;" onclick="sendEmailOtp();">Resend</a></span>
				</div>
			</div> -->

			<!-- <div class="col-md-6 form-group">
				<label class="col-md-12 label_head">Security Check <mand>*</mand></label>
				<div class="row">	
					<div class="col-md-6">
						<img src="" id="NEW_SC_IMG" class="mr-1" alt="Loading..." height="40" width="130"> 
						<a href="#" class="p-1 btn btn-light" title="Refresh Code" id="RegCaptchaBtn" onClick="temp_disable(this.id); RegCaptcha(); return false;" tabindex="-1"><i class="mdi mdi-autorenew"></i></a><br/>
					</div>	
					<div class="col-md-6">
						<input type="text" name="RegCaptcha" id="RegCaptcha" class="form-control border-input js-alphaNumeric js-noSpace" maxlength="10" placeholder="Enter verification code" autocomplete="off" />
					</div>	
				</div>
			</div> -->

		</div>

		<div class="modal-footer mt-2">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			<button type="button" class="btn btn-primary" name="sbt" id="sbt" onclick="update_custdata(); return false;">Start</button>
		</div>
	</div>
</form>
<!-- // End -->


<script type="text/javascript">	


	// function RegCaptcha() {
	// 	$('#RegCaptcha').val('');
	// 	$("#NEW_SC_IMG").attr("src","../app-core/view/captcha?data=" + req_id + "&seq="+new Date().getTime());
	// 	//$("#NEW_SC_IMG").attr("src","<?php echo CDN_URL;?>/app-core/view/captcha?data=" + req_id + "&seq="+new Date().getTime());
	// }

	
	//On Mobile SMS
	function sendMobOtp() {
		// $("#CUST_MOBILE").validate();
		$("#mobError").hide();  
		var mob_num = $("#CUST_MOBILE").val();
		if(mob_num == "") {
			swal.fire('','Please enter Mobile Number');
			focus('CUST_MOBILE');
		} else {
			var mobnumchck = /^(0|91)?[6-9][0-9]{9}$/;
            if (mobnumchck.test(mob_num)) {
				$("#MOB_OTPENTRY").show();
				$( "#CUST_MOBILE,#mobsbt").prop( "readonly", true );
				mobstartCounter();
				on_change('user-mob-otpsend','modify',mob_num,'MOBILE');
			}else{
				$("#mobError").show();
			}
		}
	}

	function sendEmailOtp() {
		// $("#CUST_MOBILE").validate();
		$("#emailError").hide();  
		var email_num = $("#CUST_EMAIL").val();
		if(email_num == "") {
			swal.fire('','Please enter Email Id');
			focus('CUST_EMAIL');
		} else {
			var emailchck = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;;
            if (emailchck.test(email_num)) {
				$("#MAIL_OTPENTRY").show();
				$("#CUST_EMAIL,#mailsbt").prop( "readonly", true );
				mailstartCounter();
				on_change('user-email-otpsend','modify',email_num,'EMAIL');
				
			}else{
				$("#emailError").show();
			}	
			// $("#MAIL_OTPENTRY").show();
			// on_change('user-email-otpsend','modify',email_num,'EMAIL');
		}
	}

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

	function mailstartCounter() {
		// disable('ResendmailBtn');
		$("#MAIL_RESENDOTP").hide();  
		$("#MAIL_OTPCOUNTER").show();  
		var mailcounter = 60;
		var mailbtn = document.getElementById("MAIL_OTPCOUNTER");//ResendmailBtn
		mailtimer = setInterval(function(){
			mailcounter--;
			if(mailcounter > 1) {
				mailbtn.innerHTML = mailcounter + " seconds left";
				//disable('mailsbt');
			}
			else if(mailcounter == 1) {
				mailbtn.innerHTML = mailcounter + " second left";
				//disable('mailsbt');
			}
			else {
				clearInterval(mailtimer);
				$("#MAIL_RESENDOTP").show();  
				$("#MAIL_OTPCOUNTER").hide();  
				//mailbtn.innerHTML = "Resend OTP";
				//enable('mailsbt');
			}
		},"1000");
    }

	function update_custdata() {
        disable('sbt'); loader_start('myform'); post_data('myform');
    }

	$("#CUST_TITLE").change(function() {  
        var srchtyp = $("#CUST_TITLE").val();
        if(srchtyp == "1") {
           $('#CUST_GENDER').val('M');        
        }else if(srchtyp == "3") {
            $('#CUST_GENDER').val('F');        
        }else{
			$('#CUST_GENDER').val('');
		}
    });

	$(document).ready(function() {	
		//#ModalLabel
		$('#ModalWin-ModalLabel').html("<?php echo $ModalLabel; ?>");
	});

</script>