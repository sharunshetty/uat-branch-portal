<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../../app-core/app_auto_load.php');


//Check User already logged out
$login_chk_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes")); // 10min ago time  
$add_data = array('LOGIN_REQ_ID' => $_SESSION['USER_LOGIN_ID']);  
$countLogins = $main_app->sql_fetchcolumn("SELECT count(0) FROM APP_LOGIN_LOGS WHERE USR_ACTIVE_FLAG = '1' AND LOGIN_TIME > '{$login_chk_date}' AND LOGIN_REQ_ID = :LOGIN_REQ_ID ",$add_data);

if($countLogins == 0) {
  header('Location: '.APP_URL.'/logout'); //?sess=expired
  exit();
}


//if login not available or OTP verify not required

if(!isset($_SESSION['USER_APP']) || !isset($_SESSION['USER_ID']) || !isset($_SESSION['USER_OTP_CHECK_REQ']) || $_SESSION['USER_OTP_CHECK_REQ'] != "Y") {
  header('Location: '.APP_URL.'/');
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
    <input type="hidden" name="cmd" value="otp_verify" />
    <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>" />

    <div class="row d-flex justify-content-center">
      <div class="col-md-5">
        <div class="row">

          <div class="col-md-12 text-center mt-3 mb-3">
            <span class="h5">Authenticate using One-Time Password (OTP)</span>
          </div>

          <div class="col-md-12 text-dark form-group">
            <div class="alert alert-block alert-info"> Please enter the 6 digit OTP that has been sent to your registered phone number for verification.<br/> </div>
          </div>

          <div class="col-md-12 col-lg-6 form-group text-center form-group">
            <input type="password" name="LOGIN_OTP" id="LOGIN_OTP" placeholder="Enter one time password" maxlength='6' class="form-control border-input js-isNumeric" autocomplete="off">
          </div>

          <div class="col-md-12 col-sm-12 form-group">
            <div class="row">
              <div class="col-md-4">
              <button type="submit" class="btn btn-primary" name="sbt" id="sbt" onclick="send_form(); return false;">Continue</button> 
              </div>

              <div class="col-md-8 text-right">
              <a href="<?php echo APP_URL; ?>/logout" class="btn btn-danger">Cancel</a>    
              </div>
            </div> 
          </div>

          <div class="col-md-12 form-group text-danger text-justify small">
            An OTP will be valid for 5 minutes after which it will expire.
            If you do not receive your OTP code within 2 minute, Please click on cancel and try again.<br/>
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

<script>

    $(document).ready(function() {
        
    });

</script>

</body>
</html>