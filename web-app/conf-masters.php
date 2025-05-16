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
$page_title = "Masters";
$page_link = "conf-masters";

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

          <div class="col-md-6 col-lg-3 form-group">
            <a href="app-pgms" class="start-loader">
              <div class="box bg-home-menu card-hover">
                  <h1 class="font-light text-white"><img class="menu-icon" src="<?php echo UPLOAD_PUBLIC_CDN_URL; ?>/pgm-icons/app-pgms.svg"></h1>
                  <h6 class="text-gold">App Programs</h6>
              </div>
            </a>
          </div>

          <!-- <div class="col-md-6 col-lg-3 form-group">
            <a href="rekyc-requests" class="start-loader">
              <div class="box bg-home-menu card-hover">
                <h1 class="font-light text-white"><img class="menu-icon" src="<?php echo UPLOAD_PUBLIC_CDN_URL; ?>/pgm-icons/app-pgms.svg"></h1>
                <h6 class="text-gold">REKYC Request</h6>
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