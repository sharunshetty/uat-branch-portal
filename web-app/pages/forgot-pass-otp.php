<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../../app-core/app_auto_load.php');

//if login not available or OTP verify not required

if(!isset($_SESSION['FP_USER_APP']) || !isset($_SESSION['FP_USER_ID']) || !isset($_SESSION['FP_USER_TIMEOUT']) || !isset($_SESSION['FP_OTP_CHECK_REQ']) || $_SESSION['FP_OTP_CHECK_REQ'] != "Y") {
  header('Location: '.APP_URL.'/');
  exit();
}

if(( time() - (int)$_SESSION['FP_USER_TIMEOUT']) > "1200") {
  header('Location: '.APP_URL.'/logout');
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

	<title><?php echo BRAND_SHORT_NAME . " " . APP_SHORT_NAME; ?></title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="shortcut icon" href="<?php echo CDN_URL; ?>/favicon.ico" type="image/ico"/>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/css/style.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/css/login.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>
    
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/vkyc-assets/easyrtc.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/vkyc-assets/client_recording.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/vkyc-assets/client-kyc.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>

</head>
<body class="no-skin bg-white">


<div class="page-header-div">
<div class="container">
	<div class="row">
		<div class="col-6">
        <img src="<?php echo CDN_URL; ?>/theme/img/brand-logo.png" alt="<?php echo BRAND_NAME; ?>" class="page-header-logo">
		</div>
		<div class="col-6 text-right"><span class="navbar-brand small text-white pull-right"><?php echo APP_SHORT_NAME; ?></span></div>
	</div>
</div>
</div>

<div class="main-container ace-save-state" id="main-container">
<div class="container mt-4 mb-5 min-high">
  <form id="app-form" name="app-form" method="post" action="javascript:void(null);" class="form-material">
    <input type="hidden" name="cmd" value="forgot_pass_otp" />
    <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>" />
    <input type="hidden" id="data_key" value="<?php echo $safe->rsa_public_key();?>" />

    <div class="row d-flex justify-content-center">
      <div class="col-md-12 mt-3">
        <div class="row">

          <div class="col-md-6">
            <div class="row">
    
              <div class="col-md-6 col-lg-6 form-group">
                <div class="row">
                  <label class="col-md-12 label_head">Enter Received OTP <mand>*</mand></label>
                  <div class="col-md-12">
                    <input type="password" name="FP_OTP" id="FP_OTP" placeholder="" maxlength='6' class="form-control border-input js-isNumeric" autocomplete="off">
                  </div>
                </div>
              </div>

            </div>
      
            <div class="row">

              <div class="col-md-6 col-lg-6 form-group">
                <div class="row">
                  <label class="col-md-12 label_head">New Password <mand>*</mand></label>
                  <div class="col-md-12">
                    <input type="password" name="USR_PASSWORD1" id="USR_PASSWORD1" placeholder="" maxlength='20' class="form-control" autocomplete="off">
                    <span id="errorname" class="small text-danger"></span>
                  </div>
                </div>
              </div> 
            
              <div class="col-md-6 col-lg-6 form-group">
                <div class="row">
                  <label class="col-md-12 label_head">Confirm New Password <mand>*</mand></label>
                  <div class="col-md-12">
                    <input type="password" name="USR_PASSWORD2" id="USR_PASSWORD2" placeholder="" maxlength='20' class="form-control" autocomplete="off">
                    <span id="errorname2" class="small text-danger"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-12 col-sm-12 form-group mt-3">
                <div class="row">
                  <div class="col-6">
                    <button type="submit" class="btn btn-primary" name="sbt" id="sbt" onclick="forgotPassword(); return false;">Continue</button> 
                  </div>

                  <div class="col-6 text-right">
                    <a href="<?php echo APP_URL; ?>/logout" class="btn btn-light">Cancel</a>  
                  </div>
                </div> 
              </div>

              <div class="col-md-12 form-group text-danger small">
                An OTP will be valid for 5 minutes after which it will expire.<br/>
                If you do not receive your OTP code within 2 minute, Please click on cancel and try again.<br/>
              </div>

            </div>

          </div>

          <div class="col-md-6">
            <div class="row justify-content-center">
              <div class="card border mt-2">
                <div class="card-header text-primary h6"><i class="mdi mdi-information-outline mdi-18px"></i> Password Requirements</div>
                <div class="card-body bg-light">
                  <ul class="text-dark pl-3 pr-5">
                    <li>Password must contain at least one uppercase letter</li>
                    <li>Password should be greater than 8 characters</li>
                    <li>Password must contain at least one lowercase letter</li>
                    <li>Password must contain at least one number</li>
                    <li>Password must contain at least one special character</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          
          
        </div>
      </div>
    </div>

  </form>

</div>
</div>

<!-- Results -->
<div id="result"></div>
<div id="result2"></div>
<div id="result3"></div>

<div class="container">
<footer class="main-footer no-print ml-0">
    <div class="row">
    <div class="col-md-6 text-center text-md-left"><?php echo app_copyrights(); ?></div>
    <div class="col-md-6 text-center text-md-right"><?php echo app_poweredby(); ?></div>
    </div>
</footer>
</div>

    <!-- JavaScripts -->
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/jq.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/jq-ui.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/ie.promise.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/framework.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/alerts.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/select2.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/loader.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/OverlayScrollbars.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/moment.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/daterangepicker.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/jq.dataTables.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/dataTables.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/theme.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/clipboard.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/app.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/jsencrypt.js?v=<?php echo CDN_VER; ?>"></script>

    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/vkyc-assets/socket.io.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/vkyc-assets/easyrtc.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/vkyc-assets/easyrtc_recorder.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/vkyc-assets/client-kyc.js?v=<?php echo CDN_VER; ?>"></script>

<script>

    //Submit
    function forgotPassword() {
      var encrypt = new JSEncrypt();
      encrypt.setPublicKey($('#data_key').val());
      var otp = encrypt.encrypt($('#FP_OTP').val());
      console.log(otp);
      var old_pass = encrypt.encrypt($('#USR_PASSWORD1').val());
      var new_pass = encrypt.encrypt($('#USR_PASSWORD2').val());

      $('#FP_OTP').val(otp);
      $('#USR_PASSWORD1').val(old_pass);
      $('#USR_PASSWORD2').val(new_pass);

      if(otp != '' && old_pass != '' && new_pass != '') {
        send_form();
      } else {
        $('#FP_OTP').val('');
        $('#USR_PASSWORD1').val('');
        $('#USR_PASSWORD2').val('');
        swal.fire('', 'Invalid Request');
      }

    }

    $(document).ready(function(){

      $('#USR_PASSWORD1,#USR_PASSWORD2').on('change',function(){
        var pwd1 = $('#USR_PASSWORD1').val();
        var pwd2 = $('#USR_PASSWORD2').val();

        var len = $('#USR_PASSWORD1').val().length;
        upper = /[A-Z]/;
        lower = /[a-z]/;
        number = /[0-9]/;
        var regularExpression  = /[!@#$%^&*]/;
        var disply = document.getElementById('errorname');

        if( !upper.test(pwd1) ) {
          show('errorname');
          disply.innerHTML="Password must contain at least one uppercase letter!";
        }
        else if ( len < 8 ) {
          show('errorname');
          disply.innerHTML="Password should be greater than 8 characters!";
        }else if( !lower.test(pwd1) ) {
          show('errorname');
          disply.innerHTML="Password must contain at least one lowercase letter!";
        } else if( !number.test(pwd1) ) {
          show('errorname');
          disply.innerHTML="Password must contain at least one number!";
        } else if( !regularExpression.test(pwd1) ) {
          show('errorname');
          disply.innerHTML="Password must contain at least one special character!";
        } else if(pwd2!="" && pwd1!="" && pwd1 != pwd2){
          hide('errorname');
          show('errorname2');
          var disply2 = document.getElementById('errorname2');
          disply2.innerHTML="Password & Confirm password not matching!";
        } else {
          hide('errorname');
          hide('errorname2');
        }
	    });

    });

</script>

</body>
</html>