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
$page_pgm_code = "USERPWDRESET";

$page_title = "Password Reset";
$page_link = "./app-user-pwd-reset";

$parent_page_title = "Go Back";
$parent_page_link = "./conf-users";

$page_table_name = "ASSREQ_USER_ACCOUNTS";
$page_primary_key = "USER_ID";

/** Custom Search */
$add_sql = "{$page_primary_key} IS NOT NULL AND {$page_primary_key} <> 'SUPERADMIN'";
$add_data = array();
$additionalVars = "";
if (!isset($_GET['s']) || !is_numeric($_GET['s'])) {
  $start = 0;
} else {
  $start = $main_app->strsafe_input(trim($_GET['s']));
}
$limit = "10";

$page_filter = false;
if (isset($_GET['q'])) {
  $q = $main_app->strsafe_input(trim($_GET['q']));
  $additionalVars .= "&q=" . $q;
  $page_filter = true;
} else {
  $q = "";
}

if (isset($_GET['frm_date'])) {
  $frm_date = $main_app->strsafe_input(trim($_GET['frm_date']));
  $additionalVars .= "&frm_date=" . $frm_date;
  $page_filter = true;
} else {
  $frm_date = "";
}

if (isset($_GET['upto_date'])) {
  $upto_date = $main_app->strsafe_input(trim($_GET['upto_date']));
  $additionalVars .= "&upto_date=" . $upto_date;
  $page_filter = true;
} else {
  $upto_date = "";
}

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
?>

<!-- Content : Start -->
<section class="content">
<div class="container-fluid">
<div class="row">
<div class="col-lg-12">
	
	<div class="card card-outline card-brand">
	<div class="card-body min-high2">
            
        <!-- Filters : Start -->
        <form id="my-search" action="<?= $_SERVER['REQUEST_URI'] ?>" method="GET" class="form-material">
        <div class="row">

            <div class="col-md-3 col-lg-3 form-group">
              <label class="col-md-12 label_head">Search User ID</label>
              <div class="col-md-12">
                <input type="text" name="q" id="q" value="" placeholder="Type &amp; Press Enter" onchange="this.form.submit();" class="form-control border-input cust-input" autocomplete="off">
              </div>
            </div>

            <div class="col-md-3 col-lg-3 form-group">
            <label class="col-md-12 label_head">From Date</label>
            <div class="col-md-12">
                <input type="text" name="frm_date" id="frm_date" class="form-control border-input date" autocomplete="off">
            </div>
            </div>

            <div class="col-md-3 col-lg-3 form-group">
            <label class="col-md-12 label_head">Upto Date</label>
            <div class="col-md-12">
                <input type="text" name="upto_date" id="upto_date" class="form-control border-input date" autocomplete="off">
            </div>
            </div>

            <?php if (isset($page_filter) && $page_filter == true) { ?>
            <div class="col-md-3 col-lg-3 form-group">
                <label class="col-md-12 label_head">&nbsp;</label>
                <div class="col-md-12">
                <a href="<?php echo APP_URL . '/' . $page_link; ?>" class="btn btn-light btn-sm small"><i class="mdi mdi-filter-remove"></i> Clear Filters</a>
                </div>
            </div>
            <?php } ?>
            
        </div>
        </form>
        <!-- Filters : End -->
        
        <!-- Data Table Start -->
        <div class="row">

        <div class="col-md-12">

            <div class="table-responsive">
            <table class="app-data-table table table-striped table-sm dataTable no-footer" id="resp-table">
                <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Mobile No.</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php

                //Custom Search box
                if ($q) {
                    $add_sql .= " AND (UPPER(USER_ID) LIKE UPPER(:Q1))";
                    $add_data['Q1'] = '%' . $q . '%';
                }

                if($frm_date != "") { $sql_frm_date = date('d-m-Y',strtotime($frm_date)); }
                if($upto_date != "") { $sql_upto_date = date('d-m-Y',strtotime($upto_date)); }
                
                if($frm_date != "" && $upto_date != "" ) { $add_sql .= " AND TRUNC(CR_ON) BETWEEN TO_DATE('{$sql_frm_date}','DD-MM-YYYY') AND TO_DATE('{$sql_upto_date}','DD-MM-YYYY')"; }
                elseif($frm_date != "" && $upto_date == "" ) { $add_sql .= " AND TRUNC(CR_ON) >= TO_DATE('{$sql_frm_date}','DD-MM-YYYY')"; }
                elseif($frm_date == "" && $upto_date != "" ) { $add_sql .= " AND TRUNC(CR_ON) <= TO_DATE('{$sql_upto_date}','DD-MM-YYYY')"; }

                //Total Results
                $totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM {$page_table_name} WHERE {$add_sql}", $add_data);

                if ($totalResults) {
                    $final_query = "SELECT * FROM {$page_table_name} WHERE {$add_sql} ORDER BY CR_ON DESC";
                    $sql_exe = $main_app->sql_dataTable($final_query, $add_data, $start, $limit);
            
                    $i = 1;
                    while ($row = $sql_exe->fetch()) {

                        $enc_usr_id = isset($row['USER_ID']) ? $safe->str_encrypt($row['USER_ID'],$_SESSION['SAFE_KEY']) : "";

                        echo "<tr>";
                        echo "<td>" .$row['USER_ID']. "</td>";
                        echo "<td>" .$row['USER_FULLNAME']."<br/><span class='small'>Role : ".$row['USER_ROLE_CODE']."</span>";
                        echo "</td>";
                        echo "<td>" .$row['USER_MOBILE']. "</td>";
                        if(isset($row['USER_STATUS']) && $row['USER_STATUS'] == "A") {
                            echo "<td><span class='text-success'>Active</span></td>";
                        } else {
                            echo "<td><span class='text-danger'>Inactive</span></td>";
                        }
                        echo "<td class='text-center'><a href='javascript:void(0)' data-value='{$enc_usr_id}' onclick=fetch_modal('reset_user_password','lg','',$(this).data('value')); class='edit-link'>Reset Password <i class='mdi mdi-square-edit-outline'></i></a></td>";
                        echo "</tr>";
                        $i++;
                    }
                    
                } else {
                    echo '<tr><td colspan="6">No records found</td></tr>';
                }
                ?>

                </tbody>
            </table>
            </div>

        </div>
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
        <!-- Data Table End -->
       
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

  <script>
    
    $("#frm_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        //timePicker: true,
        autoApply: true,
        //startDate: moment().format('DD-MM-YYYY hh:mm A'), // Current
        //minDate: moment().format('DD-MM-YYYY'), // Min date
        //maxDate: moment().add(30, 'days').format('DD-MM-YYYY'), // Max date
        locale: {
        format: 'DD-MM-YYYY', // Format
        },
        autoUpdateInput: false,
        }, function(chosen_date) {
            $('#frm_date').val(chosen_date.format('DD-MM-YYYY'));
    });

    $('input[name="frm_date"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
        this.form.submit();
    });

    $("#upto_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        //timePicker: true,
        autoApply: true,
        //startDate: moment().format('DD-MM-YYYY hh:mm A'), // Current
        //minDate: moment().format('DD-MM-YYYY'), // Min date
        //maxDate: moment().add(30, 'days').format('DD-MM-YYYY'), // Max date
        locale: {
        format: 'DD-MM-YYYY', // Format
        },
        autoUpdateInput: false,
        }, function(chosen_date) {
            $('#upto_date').val(chosen_date.format('DD-MM-YYYY'));
    });

    $('input[name="upto_date"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
        this.form.submit();
    });

    $(document).ready(function(){

        //Select Custom Search
        $('#frm_date').val('<?php echo $frm_date; ?>');
        $('#upto_date').val('<?php echo $upto_date; ?>');
        $('#q').val('<?php echo $q; ?>');
    });

  </script>

</body>
</html>