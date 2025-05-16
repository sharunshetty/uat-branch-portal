<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

/** Check User Session */
require_once(dirname(__FILE__) . '/check-login.php');

/** Current Page */
$page_pgm_code = "TBAAUTH";

//check program code
/* $pgmStatus = chk_usr_program_list($page_pgm_code);

if(isset($pgmStatus) && $pgmStatus == false) {
	http_response_code(404);
	exit();
} */

$page_title = "User Authorization";
$page_link = "./ass-authorise-details";

$parent_page_title = "Go Back";

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS_DTL";
//$page_primary_key = "TBA2_REF_NUM";

/** Custom Search */
//$add_sql = "USER_ACNT_STATUS ='P'";
$q='';
$add_sql = "AUTH_REF_NUM IS NOT NULL  AND USER_ACNT_STATUS = 'P'";

if(isset($_SESSION['USER_ROLE']) && $_SESSION['USER_ROLE'] != 'ADMIN') {
  $add_sql .= " AND USER_REGIONS = '".$_SESSION['BRANCH_CODE']."'";
}

$add_data = array();
$additionalVars = "";
if (!isset($_GET['s']) || !is_numeric($_GET['s'])) {
  $start = 0;
} else {
  $start = $main_app->strsafe_input(trim($_GET['s']));
}
$limit = "20";

//Custom Search Fields
$page_filter = false;
if (isset($_GET['q'])) {
  $userid = $main_app->strsafe_input(trim($_GET['q']));
  $additionalVars .= "&q=" . $q;
  $page_filter = true;
} else {
  $userid = "";
}

if (isset($_GET['status'])) {
  $status = $main_app->strsafe_input(trim($_GET['status']));
  $additionalVars .= "&status=" . $status;
  $page_filter = true;
} else {
  $status = "";
}

// Start Program 

// Role Access
/* if($_SESSION['USER_ROLE_CODE'] != "SYSADMIN" && $_SESSION['USER_ROLE_CODE'] != "ADMIN" && $_SESSION['USER_ROLE_CODE'] != "HO") {
  $add_sql .= " AND TBA2_BRN_CODE = :TBA2_BRN_CODE";
  $add_data['TBA2_BRN_CODE'] = $_SESSION['TBA2_BRN_CODE'];
} */

//Custom Search box
if($userid) { $add_sql .= " AND (UPPER(USER_ID) LIKE UPPER(:USER_ID))"; $add_data['USER_ID'] = '%'.$userid.'%'; }
// if($q) { $add_sql .= " OR UPPER(TBA2_BRN_CODE) LIKE UPPER(:Q2))"; $add_data['Q2'] = '%'.$q.'%'; }

//Extra Filter
if($status && $status != NULL) {
  $add_sql .= " AND USER_ACNT_STATUS = :USER_ACNT_STATUS";
  $add_data['USER_ACNT_STATUS'] = $status;
}


//Page Total Results
$totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM {$page_table_name} WHERE {$add_sql}", $add_data);

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );

?>

<!-- Content : Start -->
<div class="col-md-12 col-lg-12 form-group order-2 order-lg-1">
<div class="page-card">

	<div class="row">
		<div class="col-12">
			
			<div class="card card-outline card-brand">
			<div class="card-body min-high2">

        <!-- Filters : Start -->
        <form id="my-search" action="<?= $page_link ?>" method="GET" class="form-material">
        
          <div class="row">

            <div class="col-md-4 col-lg-3 form-group">
              <label class="col-md-12 label_head">Search</label>
              <div class="col-md-12">
                <input type="text" name="q" id="q" value="" placeholder="Type &amp; Press Enter" class="form-control border-input cust-input" autocomplete="off">
              </div>
            </div>

            <div class="col-md-4 col-lg-3 form-group">
              <label class="col-md-12 label_head">Status</label>
              <div class="col-md-12">
                <select name="status" id="status" onchange="this.form.submit();" class="form-control border-input" autocomplete="off">
                  <option value="">--- All ---</option>
                  <option value="P">Pending</option>
                  <option value="S">Approved</option>
                  <option value="R">Rejected</option>
                </select>
              </div>
            </div>
          </div>

        </form>
        <!-- Filters : End -->

        <div class="row">
          <div class="col-md-8 text-muted mb-2">
            <?php echo $main_app->sql_dataTable_count($totalResults, $start, $limit); ?>
          </div>
        </div>

        <!-- Data Table Start -->
        <div class="row">
          <div class="col-md-12">

            <div class="table-responsive">
              <table class="app-data-table table table-striped table-sm dataTable no-footer" id="resp-table">
                <thead>
                  <tr>
                    <th>Reference No</th>
                    <th  class='text-center'>Employee Details</th>
                    <th  class='text-center'>Employee Details</th>
                    <th>Employee Status for approval</th>
                    <th class="text-center">Authorise Status</th>
                    <th >Created</th>
                    <th class="text-center" style="width: 8%;">Action</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                   $i = 0;   
                    if($totalResults) {

                    $final_query = "SELECT * FROM {$page_table_name} WHERE {$add_sql} ORDER BY CR_ON DESC";

                    $sql_exe = $main_app->sql_dataTable($final_query, $add_data, $start, $limit);
                    while ($row = $sql_exe->fetch()) {

                      echo "<tr>";
                      
                          echo "<td>";                    
                            echo $main_app->strsafe_output($row['AUTH_REF_NUM']);
                          echo "</td>";

                          echo "<td  class='text-center'>";               
                            echo $main_app->strsafe_output($row['USER_ID'])."<br/>";
                            
                            //$userdetails = $main_app->sql_fetchcolumn("SELECT * FROM ASSREQ_USER_ACCOUNTS WHERE USER_ID:USER_ID ", $add_data['USER_ID'] = $row['USER_ID']);
                            //echo $userdetails['USER_FULLNAME'];

                            echo $main_app->getval_field('ASSREQ_USER_ACCOUNTS','USER_FULLNAME','USER_ID',$row['USER_ID'])."<br/>";
                            //echo "<small>".$main_app->getval_field('cbuat.mbrn','mbrn_name','mbrn_code',$row['USER_REGIONS'])."</small>";       
                            echo "<small>". isset($row["USER_REGIONS"]) ? $main_app->strsafe_output($row['USER_REGIONS']) : "-NA-"."</small>";
                          echo "</td>";

                          echo "<td  class='text-center'>";  
                            echo $main_app->getval_field('ASSREQ_USER_ACCOUNTS','USER_MOBILE','USER_ID',$row['USER_ID'])."<br/>";             
                            echo $main_app->getval_field('ASSREQ_USER_ACCOUNTS','USER_EMAIL','USER_ID',$row['USER_ID'])."<br/>";             
                            echo $main_app->getval_field('ASSREQ_USER_ACCOUNTS','USER_ROLE_CODE','USER_ID',$row['USER_ID'])."<br/>";             
                          echo "</td>";

                          echo "<td class='text-center'> ";
                            if($row['USER_STATUS'] == "A") {
                              echo "<span> Active  </span>";
                            } elseif($row['USER_STATUS'] == "B") {
                              echo "<span> Block</span>";
                            }elseif($row['USER_STATUS'] == "R") {
                              echo "<span> Resign </span>";
                            }elseif($row['USER_STATUS'] == "T") {
                              echo "<span> Transfer </span>";
                            }elseif($row['USER_STATUS'] == "M") {
                              echo "<span> Modify Details </span>";
                            }

                          echo "</td>";

                          echo "<td class='text-center'>";  
                            
                            if($row['USER_ACNT_STATUS'] == "P") {
                              echo "<span class='text-primary'> Pending </span>";

                            } elseif($row['USER_ACNT_STATUS'] == "S") {
                              echo "<span class='text-success'> Approved </span>";

                            } elseif($row['USER_ACNT_STATUS'] == "R") {
                              echo "<span class='text-danger'> Rejected </span>";

                            } 

                            if(isset($row['AU_ON']) && $row['AU_ON'] != NULL) {
                              echo "<br/>".$main_app->valid_date($row['AU_ON'],'d-m-Y H:i:s A')."<br/>";
                            }

                            if(isset($row['AU_BY']) && $row['AU_BY'] != NULL) {
                              echo "By: ".$main_app->strsafe_output($row['AU_BY']);
                            }

                            if(isset($row['RJ_ON']) && $row['RJ_ON'] != NULL) {
                              echo "<br/>".$main_app->valid_date($row['RJ_ON'],'d-m-Y H:i:s A')."<br/>";
                            }

                            if(isset($row['RJ_BY']) && $row['RJ_BY'] != NULL) {
                              echo "By: ".$main_app->strsafe_output($row['RJ_BY']);
                            }           
                          echo "</td>";
                        
                          echo "<td class='small'>";
                            echo $main_app->valid_date($row['CR_ON'],'d-m-Y H:i:s A')."<br/>";
                            echo "By: ".$main_app->strsafe_output($row['CR_BY'])."<br/>";
                            //echo "Branch: ".$main_app->strsafe_output($row['TBA2_BRN_CODE']);
                          echo "</td>";


                          echo "<td>";
                          // echo "<a href='javascript:void(0);' class='btn btn-primary btn-block btn-sm' data-value-1='{$safe->str_encrypt($row['AUTH_REF_NUM'],$_SESSION['SAFE_KEY'])}' onclick=fetch_modal('viewuser_auth_details','lg','',$(this).data('value-1'));>View <i class='mdi mdi-eye-outline'></i></a>";
                          echo "<a href='javascript:void(0);'class='btn btn-default btn-block btn-sm' data-value-1='{$safe->str_encrypt($row['AUTH_REF_NUM'],$_SESSION['SAFE_KEY'])}' onclick=viewdatabut('$i');fetch_modal('viewuser_auth_details','lg','',$(this).data('value-1'));>View <i class='mdi mdi-eye-outline'></i></a>";
                  

                          if ($row['USER_ACNT_STATUS'] == "P") {
                            //echo "<a href='javascript:void(0);' id='authbut".$row['AUTH_REF_NUM']."' class='btn btn-primary btn-block btn-sm auth-but' data-value-1='{$safe->str_encrypt($row['AUTH_REF_NUM'],$_SESSION['SAFE_KEY'])}' onclick=fetch_modal('user_authorise_detail','lg','',$(this).data('value-1'),'".$main_app->strsafe_output($row['AUTH_REF_NUM'])."'); >Authorise</a>";
                             echo "<a href='javascript:void(0);'  id='authviewbut$i' style='display:none;' class='btnAuthView btn btn-outline-danger btn-block btn-sm' data-value-1='{$safe->str_encrypt($row['AUTH_REF_NUM'],$_SESSION['SAFE_KEY'])}' onclick=fetch_modal('user_authorise_detail','lg','',$(this).data('value-1'),'".$main_app->strsafe_output($row['AUTH_REF_NUM'])."'); >Authorise</a>";
                            
                          }
                        echo "</td>";
                    
                      echo "</tr>";

                      $i++;
                    }
                  } else {
                    echo '<tr><td colspan="7">No records found</td></tr>';
                  }
                  ?>

                </tbody>
              </table>
            </div>

            <div class="row">
              <div class="col-md-6 text-muted">
                <?php echo $main_app->sql_dataTable_count($totalResults, $start, $limit); ?>
              </div>
              <div class="col-md-6">
                <ul class="pagination float-right">
                  <?php $main_app->page_nav($start, $limit, $totalResults, "", $additionalVars); ?>
                </ul>
              </div>
            </div>

          </div>
        </div>
        <!-- Data Table End -->

			</div>
			</div>


		</div>
	</div>
	
</div>
</div>

<!-- Content : End -->

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>

<script type="text/javascript">

  //Select Custom Search
  $('#q').val(decode_ajax('<?php echo $main_app->strsafe_ajax($q); ?>'));
  $("select[name='status'] option[value='<?php echo $status; ?>']").prop('selected', true);
  
  // View
  /* function viewAuthDetails(ref_num,pk) {

    if(!ref_num) { ref_num = ""; }
    if(!pk) { pk = ""; }
    var url = 'view-auth-details?ref_num='+ref_num+'&pk='+pk;
    
    var myWidth = screen.width;
    var myWidth = 1400;
    var myHeight = 650;
    var left = (screen.width - myWidth) / 2;
    var top = (screen.height - myHeight) / 6;

    var myWindow = window.open(url, 'View', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + myWidth + ', height=' + myHeight + ', top=' + top + ', left=' + left);

    if(window.focus) {
      myWindow.focus()
    }

  } */



  function viewdatabut(i){     
    show('authviewbut'+i);
  }

</script>

