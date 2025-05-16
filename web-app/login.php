<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

/** Check User Session */
if(check_usr_login() == true) {
    header('Location: ./');
    exit();
}

/** Login CSRF Token */
if(!isset($_SESSION['LOGIN_TOKEN'])) {
    $main_app->session_set([ 'LOGIN_TOKEN' => $main_app->csrf_token() ]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- ============================================================== -->
    <!-- ######### POWERED BY LCODE TECHNOLOGIES PVT. LTD. ############ -->
    <!-- ============================================================== -->

    <title>Login | <?php echo BRAND_SHORT_NAME . " " . APP_SHORT_NAME; ?></title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="shortcut icon" href="<?php echo CDN_URL; ?>/favicon.ico" type="image/ico"/>
    <link rel="manifest" href="<?php echo CDN_URL; ?>/manifest.json">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/css/style.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/css/login.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>

</head>
<body class="hold-transition login-page" id="particles-js">

    <div class="container">
    <div class="row">
        <div class="col-lg-12 from-group d-flex justify-content-center pb-5 mb-5">
        <div class="login-box">

            <!-- login-box :start -->
            <div class="login-logo d-flex justify-content-center">
                <div class="brand-icon w-100">
                <img src="<?php echo CDN_URL; ?>/theme/img/brand-logo.png" alt="<?php echo APP_CODE; ?>" class="brand-logo-2">
                </div>
            </div>

            <div class="card">
            <div class="card-body login-card-body">

                <h5 class="text-center brand-font mb-3"><?php echo APP_CODE; ?> (Assisted Module) </h5>
                <div id="login-error"></div>

                <form class="form-signin" method="post" id="login-form">
                <input type="hidden"  id="login_token" name="login_token" value="<?php echo $_SESSION['LOGIN_TOKEN'];?>" />
                <input type="hidden"  id="app_token" name="app_token" value="<?php echo $_SESSION['APP_TOKEN'];?>" />       
                <input type="hidden" id="data_key" value="<?php echo $safe->rsa_public_key();?>" />
                    <label class="label_head">USER ID</label>
                    <div class="input-group">
                        <input type="text" id="emp_usr_id" name="emp_usr_id" class="form-control js-alphaNumeric js-toUpper" placeholder="" autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <span class="mdi mdi-account"></span>
                            </div>
                        </div>
                    </div>
                    <label class="label_head mt-2">PASSWORD</label>
                    <div class="input-group">
                        <input type="password" id="emp_usr_pass" name="emp_usr_pass" class="form-control" placeholder="" autocomplete="new-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <span class="mdi mdi-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-6">
                            <a href="<?php echo APP_URL; ?>/pages/forgot-password" class="text-muted small">Forgot Password ?</a>
                        </div>
                        <div class="col-12 text-right">
                            <button type="submit"id="btn-login" class="btn login-btn">Sign In <span class="mdi mdi-chevron-right"></span></button>
                        </div>
                    </div>
                    <div class="mt-2 small"><?php echo "App Version - ".PRODUCT_VERSION; ?></div>
                    <hr/>
                    <div class="mt-2 text-center"><?php echo app_poweredby(); ?></div>
                </form>

            </div>
            </div>
            
            <!-- login-box :end -->

        </div>
        </div>

    </div>
    </div>

    <!-- JavaScripts -->
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/jq.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/ie.promise.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/framework.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/alerts.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/select2.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/loader.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/daterangepicker.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/jq.dataTables.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/dataTables.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/jq.lazy.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/app.js?v=<?php echo CDN_VER; ?>"></script>

    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/jsencrypt.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/form-validation.js?v=<?php echo CDN_VER; ?>"></script>
    <script type="text/javascript" src="<?php echo CDN_URL; ?>/login-auth.js?v=<?php echo CDN_VER; ?>"></script>

    <script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/particles.js?v=<?php echo CDN_VER; ?>"></script>

    <script type="text/javascript">

        particlesJS.load('particles-js', '../theme/js/particles.json', function() {
            //console.log('particles.js loaded');
        });

        $(document).ready(function () {
            //console.log('Login');
        });

    </script>

</body>
</html>