<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app_auto_load.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- ============================================================== -->
    <!-- ######### POWERED BY LCODE TECHNOLOGIES PVT. LTD. ############ -->
    <!-- ============================================================== -->

    <title>Unsupported Browser</title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="shortcut icon" href="<?php echo CDN_URL; ?>/favicon.ico" type="image/ico"/>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/css/style.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>

</head>
<body>

<div class="bg-info">
    <div class="container text-center">
        <div class="h4 text-white pt-5 pb-4">Unsupported Browser</div>
    </div>
</div>

<div class="container text-center">

    <h6 class="mt-4">
        <span class="d-lg-block">To ensure you have the best possible web experience.</span> 
        We recommend you download and install the latest version of one of the following supported browsers:
    </h6>

    <div class="row mt-5 d-flex justify-content-lg-center font-weight-bold">
        <div class="col-6 col-lg-2 form-group strong"><a href="http://www.google.com/chrome/" target="_blank" class="text-dark"><i class="mdi mdi-google-chrome h1"></i><br/>Chrome</a></div>
        <div class="col-6 col-lg-2 form-group"><a href="http://getfirefox.com" target="_blank" class="text-dark"><i class="mdi mdi-firefox h1"></i><br/>Firefox</a></div>
        <div class="col-6 col-lg-2 form-group"><a href="http://www.opera.com" target="_blank" class="text-dark"><i class="mdi mdi-opera h1"></i><br/>Opera</a></div>
        <div class="col-6 col-lg-2 form-group"><a href="http://www.apple.com/safari/" target="_blank" class="text-dark"><i class="mdi mdi-apple-safari h1"></i><br/>Safari</a></div>
        <div class="col-6 col-lg-2 form-group"><a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" target="_blank" class="text-dark"><i class="mdi mdi-internet-explorer h1"></i><br/>Internet Explorer</a></div>
    </div>

    <hr class="mt-5"/>
    <div class="mb-5"><?php echo app_poweredby(); ?></div>

</div>

</body>
</html>