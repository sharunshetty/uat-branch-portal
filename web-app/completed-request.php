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
$page_pgm_code = "REVIEWCUSTACC";
$page_title = "Completed Customer Account Request";
$page_link = "./completed-request";

$parent_page_title = "Go Back";
$parent_page_link = "./index";

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );

/** Custom Search */

$from_date=$to_date=$totalResults="";

$add_sql = "{$primary_key} IS NOT NULL AND APP_STATUS='S'";


// if(isset($_SESSION['USER_ROLE']) && $_SESSION['USER_ROLE'] != 'ADMIN') {
//   $add_sql .= " AND CR_BY='".$_SESSION['USER_ID']."'";
// }

if(isset($_SESSION['USER_ROLE']) && $_SESSION['USER_ROLE'] != 'ADMIN') {
    $add_sql .= " AND BRANCH_CODE='".$_SESSION['BRANCH_CODE']."'";
}

$add_data = array();
$additionalVars = "";
if(!isset($_GET['s']) || !is_numeric($_GET['s']) ) { $start = 0; } else { $start = $main_app->strsafe_input(trim($_GET['s'])); }
$limit = "10";

//Custom Search Fields
if(isset($_GET['abprefno'])) { $abprefno = $main_app->strsafe_input(trim($_GET['abprefno'])); $filter = true; $additionalVars .= "&abprefno=".$abprefno; } else { $abprefno = ""; }
if(isset($_GET['mobno']))    { $mobno = $main_app->strsafe_input(trim($_GET['mobno'])); $filter = true; $additionalVars .= "&mobno=".$mobno; } else { $mobno = ""; }
if(isset($_GET['fromdate'])) { $from_date = $main_app->strsafe_input(trim($_GET['fromdate'])); $filter = true; $additionalVars .= "&fromdate=".$from_date; } else { $from_date = ""; }
if(isset($_GET['todate']))   { $to_date   = $main_app->strsafe_input(trim($_GET['todate'])); $filter = true; $additionalVars .= "&todate=".$to_date; } else { $to_date = ""; }
if(isset($_GET['status']))   { $status = $main_app->strsafe_input(trim($_GET['status'])); $filter = true; $additionalVars .= "&status=".$status; } else { $status = ""; }


  // Custom Search box

    if(isset($abprefno)  && $abprefno != "") {                                    
        //$add_sql .= " AND ASSREQ_REF_NUM = :ASSREQ_REF_NUM";   $add_data['ASSREQ_REF_NUM'] = $abprefno;
        $add_sql .= " AND ASSREQ_REF_NUM LIKE :ASSREQ_REF_NUM";   $add_data['ASSREQ_REF_NUM'] = '%'.$abprefno.'%';                                                                    
    }

    if(isset($mobno)  && $mobno != "") {                                    
       // $add_sql .= " AND ASSREQ_MOBILE_NUM = :ASSREQ_MOBILE_NUM";   $add_data['ASSREQ_MOBILE_NUM'] = $mobno;
        $add_sql .= " AND ASSREQ_MOBILE_NUM LIKE :ASSREQ_MOBILE_NUM";   $add_data['ASSREQ_MOBILE_NUM'] = '%'.$mobno.'%';                                                                      
    }

    if(isset($status) && $status != "") {
        $add_sql .= " AND TRIM(AUTH_STATUS) = :AUTH_STATUS";
        $add_data['AUTH_STATUS'] = $status;
    }


    $sql_frm_date=$sql_to_date="";

    if($from_date != "") {$sql_frm_date = date('d-m-Y',strtotime($from_date)); }
    if($to_date != "")   {$sql_to_date = date('d-m-Y',strtotime($to_date)); }
  
  
    if($from_date != "" && $to_date != "" ) { $add_sql .= " AND TRUNC(CR_ON) BETWEEN TO_DATE('{$sql_frm_date}','DD-MM-YYYY') AND TO_DATE('{$sql_to_date}','DD-MM-YYYY')"; }
    elseif($from_date != "" && $to_date == "" ) { $add_sql .= " AND TRUNC(CR_ON) >= TO_DATE('{$sql_frm_date}','DD-MM-YYYY')"; }
    elseif($from_date == "" && $to_date != "" ) { $add_sql .= " AND TRUNC(CR_ON) <= TO_DATE('{$sql_to_date}','DD-MM-YYYY')"; }


?>

<!-- Content : Start -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card card-outline card-brand">
					<div class="card-body min-high2">
						
			            <form id="my-search" action="<?=$_SERVER['REQUEST_URI']?>" onsubmit="loader_start();" method="GET" class="form-material">
                            <div class="row">

                                <div class="col-md-4 col-lg-2 form-group">
                                    <div class="sub-lbl">Account Reference No</div>
                                    <input type="text" name="abprefno" id="abprefno" value="" placeholder="Account Reference No" class="form-control" autocomplete="off">
                                </div>


                                <div class="col-md-4 col-lg-2 form-group">
                                    <div class="sub-lbl">Mobile No</div>
                                    <input type="text" name="mobno" id="mobno" value="" placeholder=" Mobile No" class="form-control js-isNumeric" autocomplete="off">
                                </div>


                                <div class="col-md-4 col-lg-2 form-group">
                                    <div class="sub-lbl">From Date</div>
                                    <input type="date" name="fromdate" id="fromdate" value="" placeholder="From Date" class="form-control" autocomplete="off">
                                </div>

                                <div class="col-md-4 col-lg-2 form-group">
                                    <div class="sub-lbl">To Date</div>
                                    <input type="date" name="todate" id="todate" value="" placeholder="To Date" class="form-control" autocomplete="off">
                                </div>

                                <div class="col-md-4 col-lg-2 form-group">
                                    <div class="sub-lbl">Status</div>
                                    <select name="status" id="status" class="form-control border-input" autocomplete="off">
                                        <option value="">--- ALL ---</option>
                                        <option value="AS">Approved</option>
                                        <option value="AR">Rejected</option>
                                    </select>                                  
                                </div>

				                <div class="col-md-4 col-lg-2 form-group d-flex align-items-end">
                                    <button type="submit" class="btn btn-warning btn-rounded btn-sm ml-0" value="Search" data-toggle="tooltip" title="Apply Filters"><i class="mdi mdi-filter"></i> Filter</button>
                                    <?php if(isset($filter) && $filter != "") { ?>
                                        <a href="<?php echo APP_URL . "/" . $page_link; ?>" class="btn btn-secondary btn-rounded btn-sm ml-1" data-toggle="tooltip" title="Clear all Filters"><i class="mdi mdi-filter-remove"></i> Clear</a>
                                    <?php } ?>
                                </div>                        
                            </div>

                             <div class="row">            
                                <div class="col-md-12 form-group text-right m-0">
                                    <a href="export-account-requests?from_date=<?php echo $from_date;?>&to_date=<?php echo $to_date;?>&status=<?php echo $status;?>"  class="btn btn-success btn-rounded btn-sm ml-0" title="Excel" target="_blank"><i class="mdi mdi-file-excel"></i> Download Excel</a>                                 
                                </div>
                            </div>
                        </form>

                        <!-- <div class="col-md-12 text-md-right form-group">
                            <div class="col-md-12">
                                <a href="export-rekyc-details?from_date=<?php echo $fromdate;?>&to_date=<?php echo $todate;?>&status=<?php echo $status;?>"  class="btn btn-success btn-rounded btn-sm ml-0" title="Excel" target="_blank"><i class="mdi mdi-file-excel"></i> Download Excel</a>                              
                            </div>
                        </div> -->

                        <div class="row">
                            <div class="col-md-8 text-muted mb-2 pl-md-4">
                                <?php echo $main_app->sql_dataTable_count($totalResults, $start, $limit); ?>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-md-12 ml-1">

                                <!-- Data Table Start -->

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="mast-tab">
                                    <thead>
                                        <tr>
                                           <th> Account Reference No</th>                                        
                                            <th>Customer Details</th>
                                            <th class="text-center">Status</th>	
                                            <th>Created</th>	
                                            <th  class="text-center">Action</th>                                        
                                         </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                   $totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM {$page_table_name} WHERE {$add_sql}",$add_data);


                                    if($totalResults) {
										
					
	                                $final_query = "SELECT * FROM {$page_table_name} WHERE {$add_sql} order by CR_ON desc";
                                        $sql_exe = $main_app->sql_dataTable($final_query, $add_data, $start, $limit);
                            
                                        while ($row = $sql_exe->fetch()) {
                                             echo "<tr>";
                                         
                                            echo "<td>". $main_app->strsafe_output($row['ASSREQ_REF_NUM']) ."</td>";
                                           
                                            echo "<td>{$row['ASSREQ_CUST_FNAME']}<br/>
                                                <span class='small'>DOB : </span> {$main_app->strsafe_output(date('d-m-Y',strtotime($row['ASSREQ_CUST_DOB'])))}<br/>
                                                <span class='small'>Mobile No : </span>{$row['ASSREQ_MOBILE_NUM']}
                                            </td>";
                                         
                                           echo "<td class='text-center'>";  
                                                if(isset($row['AUTH_STATUS']) && trim($row['AUTH_STATUS']) == "AS") {
                                                    echo "<span class='badge badge-success'> Approved </span>";
                                                   
                                                    if(isset($row['AU_ON']) && $row['AU_ON'] != NULL) {
                                                    echo "<br/>".$main_app->valid_date($row['AU_ON'],'d-m-Y H:i:s A')."<br/>";
                                                    }
                                                    if(isset($row['AU_BY']) && $row['AU_BY'] != NULL) {
                                                    echo "By: ".$main_app->strsafe_output($row['AU_BY']);
                                                    }

                                                } elseif(isset($row['AUTH_STATUS']) && trim($row['AUTH_STATUS']) == "AR") {
                                                    
                                                    echo "<span class='badge badge-danger'> Rejected </span>";
                                                    if(isset($row['REJ_ON']) && $row['REJ_ON'] != NULL) {
                                                        echo "<br/>".$main_app->valid_date($row['REJ_ON'],'d-m-Y H:i:s A')."<br/>";
                                                    }
                                                    if(isset($row['REJ_BY']) && $row['REJ_BY'] != NULL) {
                                                        echo "By: ".$main_app->strsafe_output($row['REJ_BY']);
                                                    }  

                                                }
                                            echo "</td>";

                                            echo "<td class='small'>";
                                                echo $main_app->valid_date($row['CR_ON'],'d-m-Y H:i:s A')."<br/>";
                                                echo "By: ".$main_app->strsafe_output($row['CR_BY'])."<br/>";
                                             echo "</td>";
  
 					     echo "<td>";                                  
                                                echo "<a href='javascript:void(0);' class='btn btn-danger btn-block btn-sm' data-value-1='{$safe->str_encrypt($row['ASSREQ_REF_NUM'],$_SESSION['SAFE_KEY'])}' onclick=fetch_modal('customer_review_request','xl','',$(this).data('value-1'));>View <i class='mdi mdi-eye-outline'></i></a>";                                          
                                            echo "</td>";                                            
             
                                        }
                                     } else { 
                                        echo '<tr><td colspan="12">No records found</td></tr>'; 
                                     }

                                    ?>

                                    </tbody>
                                    </table>
                                </div>

          
                                <div class="row mt-2">
                                    <div class="col-md-6 small text-muted">
                                  <?php echo $main_app->sql_dataTable_count($totalResults, $start, $limit); ?>
                                    </div>
                                    <div class="col-md-6">
                                    <ul class="pagination float-right">
                                    <?php $main_app->page_nav($start,$limit,$totalResults,"",$additionalVars); ?>
                                    </ul>
                                    </div>
                                </div>
                               <!-- Data Table End -->

                            </div>
                        </div>
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

<script type="text/javascript">


 
    $(document).ready(function(){
	//Select Custom Search
        $('#abprefno').val('<?php echo $abprefno; ?>');
        $('#mobno').val('<?php echo $mobno; ?>');
	    $('#fromdate').val('<?php echo $from_date; ?>');
        $('#todate').val('<?php echo $to_date; ?>');
        $('#status').val('<?php echo $status; ?>');
    
	});


    
</script>

