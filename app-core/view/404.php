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

    <title>Page Not Found</title>
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
        <div class="display-4 text-white pt-5 pb-3">404!</div>
    </div>
</div>

<div class="container text-center">

    <div class="my-5 pb-5">
        <h4 class="mt-4">Page Not Found</h4>
        <h6 class="mt-2">The requested URL was not found on this server. </h6>
        <button class="btn btn-dark btn-sm mt-4 small" onclick="history.go(-1);return false;"><i class="mdi mdi-arrow-left"></i> Go back to previous page</button>
    </div>

    <hr class="mt-5"/>
    <div class="mb-5"><?php echo app_poweredby(); ?></div>

</div>

</body>
</html>