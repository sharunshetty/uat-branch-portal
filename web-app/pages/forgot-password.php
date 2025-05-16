<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../../app-core/app_auto_load.php');

/** Login CSRF Token */
if(!isset($_SESSION['LOGIN_TOKEN'])) {
  $main_app->session_set([ 'LOGIN_TOKEN' => $main_app->csrf_token() ]);
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
    
    <!-- <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/vkyc-assets/easyrtc.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/vkyc-assets/client_recording.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/> -->
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
  <form id="forgot-form" method="post" class="form-material">
    <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>" />
    <input type="hidden" id="data_key" value="<?php echo $safe->rsa_public_key();?>" />
    <input type="hidden" name="USER_ID" id="HID_USRID" value="" />
    <input type="hidden" name="MOBILE_NUM" id="HID_MOBILE_NUM" value="" />

    <div class="row d-flex justify-content-center">
      <div class="col-md-5">
        <div class="row">

          <div class="col-md-12 text-center mt-3 mb-3">
            <span class="h5">Forgot Password</span>
          </div>

          <div class="col-md-12 text-center mt-2 mb-2">
            <div id="error"></div>
          </div>

          <div class="col-md-12 form-group mt-3">
            <div class="row">
                <label class="col-md-6">User ID <span class="text-danger">*</span></label>
                <div class="col-md-6">
                  <input type="text" id="USER_ID" placeholder="" class="form-control border-input" autocomplete="off">     
                </div>
            </div>
          </div>

          <div class="col-md-12 form-group">
            <div class="row">
                <label class="col-md-6">Mobile Number <span class="text-danger">*</span></label>
                <div class="col-md-6">
                  <input type="text" id="MOBILE_NUM" placeholder="" maxlength='10' class="form-control border-input js-Numeric" autocomplete="off">     
                </div>
            </div>
          </div>

          <div class="col-md-12 form-group">
            <div class="row">
                <label class="col-md-6">Security Code <span class="text-danger">*</span></label>
                <div class="col-md-6">
                  <input type="text" name="SC_CODE" id="SC_CODE" placeholder="" maxlength="10" class="form-control border-input js-alphaNumeric" autocomplete="off">     
                </div>
            </div>
          </div>

          <div class="col-md-12 form-group">
            <div class="row mb-2">
              <img src="../../captcha?data=<?php echo (isset($_SESSION['LOGIN_TOKEN'])) ? $_SESSION['LOGIN_TOKEN'] : ""; ?>" id="captcha_code" alt="Loading..." height="40" width="130"> &nbsp; 
              <a href="#" class="p-2 btn btn-light" title="Refresh Code" id="refresh" onclick="refreshCaptcha(); temp_disable('refresh'); return false;" tabindex="-1"><i class="mdi mdi-autorenew"></i></a>
            </div>
          </div>

          <div class="col-md-12 col-sm-12 form-group">
            <div class="row">
              
              <div class="col-md-12 text-right">
                <a href="<?php echo APP_URL; ?>/logout" class="btn btn-light">Cancel</a>    
                <button type="submit" class="btn btn-success" id="btn-forgot">Submit</button> 
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
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/form-validation.js?v=<?php echo CDN_VER; ?>"></script>

    <!-- <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/vkyc-assets/socket.io.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/vkyc-assets/easyrtc.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/vkyc-assets/easyrtc_recorder.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/vkyc-assets/client-kyc.js?v=<?php echo CDN_VER; ?>"></script> -->
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/forgot-pass-auth.js?v=<?php echo CDN_VER; ?>"></script>

<script>

  function refreshCaptcha() {
    $("#captcha_code").attr("src","../../captcha?data=<?php echo (isset($_SESSION['LOGIN_TOKEN'])) ? $_SESSION['LOGIN_TOKEN'] : ""; ?>&seq="+new Date().getTime());
  }

    $(document).ready(function() {
        
    });

</script>

</body>
</html>