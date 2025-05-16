<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

if(isset($_SESSION['USER_OTP_CHECK_REQ']) && $_SESSION['USER_OTP_CHECK_REQ'] == "Y") {
  header('Location: '.APP_URL.'/pages/otp-verify');
  exit();
}

/** Check User Session */
require_once(dirname(__FILE__) . '/check-login.php');

/** Current Page */
$page_pgm_code = "";

$page_title = "Dashboard";
$page_link = "./";

$parent_page_title = "";
$parent_page_link = "";

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
?>

<!-- Content : Start -->
<section class="content">
  <div class="container-fluid">
        
    <div class="card card-outline card-brand">
      <div class="card-body min-high">
        <div class="row mt-3">
       

        <?php

          $totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM ASSREQ_APP_PROGRAMS WHERE PGM_STATUS = '1' ");

          if($totalResults) {
             $sql_exe1 = $main_app->sql_run("SELECT PGM_CODE, PGM_NAME, PGM_FILE_PATH, PGM_MDI_ICON FROM ASSREQ_APP_PROGRAMS WHERE PGM_STATUS = '1' ORDER BY PGM_ORDER ASC");
            while ($row = $sql_exe1->fetch()) {
              $pgm_code = $main_app->sql_fetchcolumn("SELECT DTL_PGM_CODE FROM ASSREQ_USER_ROLES_DTL WHERE DTL_ROLE_CODE = '{$_SESSION['USER_ROLE']}' AND DTL_PGM_CODE = '{$row['PGM_CODE']}' ");

              // if(isset($pgm_code) && $pgm_code == $row['PGM_CODE'] || $_SESSION['USER_ROLE_CODE'] == "SYSADMIN" || $_SESSION['USER_ROLE_CODE'] == "BRNIP") {
              if(isset($pgm_code) && $pgm_code == $row['PGM_CODE']) {

                //Single service box
                echo '<div class="col-md-6 col-lg-3 form-group">';      
                
                  $ServiceLink = (isset($row['PGM_FILE_PATH']) && $row['PGM_FILE_PATH'] != NULL && $row['PGM_FILE_PATH'] != "") ? APP_URL."/".$row['PGM_FILE_PATH'] : "#";
                  echo "<a href='{$ServiceLink}' class='start-loader'>";
                    echo '<div class="box bg-home-menu card-hover">';
  
                      echo "<div class='icon'>";
                        if(isset($row['PGM_MDI_ICON']) && $row['PGM_MDI_ICON'] != NULL && $row['PGM_MDI_ICON'] != "") {
                          echo "<h1 class='font-light text-white'><img class='menu-icon' src='".UPLOAD_PUBLIC_CDN_URL."/pgm-icons/".$row['PGM_MDI_ICON']."' alt=''></h1>";
                        } else {
                          echo "<h1 class='font-light text-white'><img class='menu-icon' src='".UPLOAD_PUBLIC_CDN_URL."/pgm-icons/default.svg' alt=''></h1>";
                        }
                      echo "</div>";

                      echo "<div class='content'><h6 class='text-gold'>".$main_app->strsafe_output($row['PGM_NAME'])."</h6></div>";
                    echo "</div>";
                  echo "</a>";
                
                echo "</div>";

              }
            }

          } else {
            echo "<div class='col-md-12 text-center text-danger'> Error : No service is currently active </div>";
          }

        ?>
    

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