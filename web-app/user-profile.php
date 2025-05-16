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
$page_title = "My Profile";
$page_link = "user-profile";

$parent_page_title = "";
$parent_page_link = "";

$page_table_name ='ASSREQ_USER_ACCOUNTS';

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );

$sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = '{$_SESSION['USER_ID']}'","");
	$item_data = $sql_exe->fetch();
?>

<!-- Content : Start -->
<section class="content">
<div class="container-fluid">

<div class="row">
<div class="col-md-12">

  <div class="card card-outline card-brand">
	<div class="card-body min-high2">

    <table class="table table-bordered table-striped table-hover">
    <tbody>

      <?php
        echo "<tr><td width='40%'>Full Name</td><td>".$main_app->strsafe_output($item_data['USER_FULLNAME'])."</td></tr>";
        echo "<tr><td>App Role</td><td>".$main_app->getval_field('ASSREQ_USER_ROLES','ROLE_DESC','ROLE_CODE',$item_data['USER_ROLE_CODE'])."</td></tr>";
        echo "<tr><td>Mobile</td><td>".$main_app->strsafe_output($item_data['USER_MOBILE'])."</td></tr>";
        echo "<tr><td>Email</td><td>".$main_app->strsafe_output($item_data['USER_EMAIL'])."</td></tr>";
        
        if($item_data['USER_REGIONS'] == 'A') { $RVAL = "All Regions"; }
        elseif($item_data['USER_REGIONS'] == 'S') { $RVAL = "Specific Regions"; }
        else { $RVAL = "-"; }

        echo "<tr><td>Processing </td><td>".$RVAL."</td></tr>";

      ?>

    </tbody>
    </table>

    <?php if($item_data['USER_REGIONS'] == 'S') { ?>

      <br/>
      <table class="table table-bordered table-hover">
      <thead class="thead-dark">
      <tr>
        <th width='40%'>Code</th>
        <th>Region / Centre Name</th>
      </tr>
      </thead>
      <tbody id="tbody">

        <?php
        
        //Total Results
        $totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM user_accounts_regions ,loc_region_master WHERE AR_REGION_CODE = REGION_CODE AND AR_USER_ID = '{$_SESSION['USER_ID']}'","");

          if($totalResults) {
            $sql_exe = $main_app->sql_run("SELECT AR_REGION_CODE,REGION_NAME FROM user_accounts_regions ,loc_region_master WHERE AR_REGION_CODE = REGION_CODE AND AR_USER_ID = '{$_SESSION['USER_ID']}'");
            while ($row = $sql_exe->fetch()) {
              echo "<tr class='no_records' id='no_record'>";
              echo "<td>".$main_app->strsafe_output($row['AR_REGION_CODE'])."</td>";
              echo "<td>".$main_app->strsafe_output($row['REGION_NAME'])."</td>";
              echo "</tr>";
            }
          } else {
            echo '<tr><td colspan="2">No regions assigned</td></tr>'; 
          }
        ?>

      </tbody>
      </table>

    <?php } ?>

  </div>
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