<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

/** Check User Session */
require_once(dirname(__FILE__) . '/check-login.php');

/** Current Page */
$page_title = "Users Configuration";
$page_link = "conf-users";

$parent_page_title = "Go Back";
$parent_page_link = "./";

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
?>

<!-- Content : Start -->
<section class="content">
<div class="container-fluid">
      
  <div class="card card-outline card-brand">
  <div class="card-body min-high">
  <div class="row">

    <!-- <div class="col-md-6 col-lg-3 form-group">
    <a href="app-user-roles" class="start-loader">
      <div class="box bg-home-menu card-hover">
          <h1 class="font-light text-white"><img class="menu-icon" src="<?php echo UPLOAD_PUBLIC_CDN_URL; ?>/pgm-icons/app-user-roles.svg"></h1>
          <h6 class="text-gold">User Roles</h6>
      </div>
    </a>
    </div>

    <div class="col-md-6 col-lg-3 form-group">
    <a href="app-user-accounts" class="start-loader">
      <div class="box bg-home-menu card-hover">
          <h1 class="font-light text-white"><img class="menu-icon" src="<?php echo UPLOAD_PUBLIC_CDN_URL; ?>/pgm-icons/app-user-accounts.svg"></h1>
          <h6 class="text-gold">User Accounts</h6>
      </div>
    </a>
    </div>

    <div class="col-md-6 col-lg-3 form-group">
    <a href="app-user-block" class="start-loader">
      <div class="box bg-home-menu card-hover">
          <h1 class="font-light text-white"><i class="menu-icon mdi mdi-account-remove-outline mdi-48px text-warning"></i></h1>
          <h6 class="text-gold">User Block/Unblock</h6>
      </div>
    </a>
    </div>

    <div class="col-md-6 col-lg-3 form-group">
    <a href="app-user-pwd-reset" class="start-loader">
      <div class="box bg-home-menu card-hover">
          <h1 class="font-light text-white"><i class="menu-icon mdi mdi-lock-reset mdi-48px text-warning"></i></h1>
          <h6 class="text-gold">User Password Reset</h6>
      </div>
    </a>
    </div> -->

  </div>
  </div>
  </div>

</div>
</section>
<!-- Content : End -->

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>

</body>
</html>