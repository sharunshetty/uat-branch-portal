<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS_DTL";

/** Modal Title */
$ModalLabel = "View : Transfer Branch History";

$page_primary_keys = array(
    'USER_ID' => (isset($_POST['filter_val']) && $_POST['filter_val'] != NULL) ? $_POST['filter_val'] : ''
);

?>

<div class="modal-body py-1" id="load-content">

	<!-- Data -->
	<div class="row">
		<div class="col-md-12">

			<table class="app-help-table table table-striped table-sm" id="help-table">
                <thead class="th-help">
                    <tr>
                        <th>Employee Id</th>
                        <th>Employee Name</th>
                        <th>Transfer From Branch</th>
                        <th>Transfer To Branch</th>
                        <th>Transfer Date</th>
                        <th>Approval Status</th>
                    </tr>
                </thead>
                <tbody>

                
                    <?php

                        //Total Results
                        $totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM {$page_table_name} WHERE USER_ID = :USER_ID and USER_STATUS = 'T'", $page_primary_keys);

                        if($totalResults) {
                            $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID and USER_STATUS = 'T' order by CR_ON DESC", $page_primary_keys);
                             while ($row = $sql_exe->fetch()) {

                                echo "<tr>";
                                echo "<td>".$main_app->strsafe_output($row['USER_ID'])."</td>";
                                echo "<td>".$main_app->strsafe_output($row['USER_FULLNAME'])."</td>";

                                // echo "<td>". $main_app->getval_field('cbuat.mbrn','mbrn_name','mbrn_code',$row['TRANSFER_FROMBRANCH'])."</td>";

                                // echo "<td>". $main_app->getval_field('cbuat.mbrn','mbrn_name','mbrn_code',$row['TRANSFER_TOBRANCH'])."</td>";
                        
                                echo "<td>".$main_app->strsafe_output($row['TRANSFER_FROMBRANCH'])."</td>";
                                echo "<td>".$main_app->strsafe_output($row['TRANSFER_TOBRANCH'])."</td>";

                                $transferdate = date("d-m-Y", strtotime($row['TRANSFER_DATE']));
                                echo "<td>".$main_app->strsafe_output($transferdate)."</td>";
                               
                                if(isset($row['USER_ACNT_STATUS']) && $row['USER_ACNT_STATUS'] == "P") { 
                                    echo "<td>".$main_app->strsafe_output("Pending for approval")."</td>";
                                }elseif(isset($row['USER_ACNT_STATUS']) && $row['USER_ACNT_STATUS'] == "S") { 
                                    echo "<td>".$main_app->strsafe_output("Approved")."</td>"; 
                                }elseif(isset($row['USER_ACNT_STATUS']) && $row['USER_ACNT_STATUS'] == "R") { 
                                    echo "<td>".$main_app->strsafe_output("Rejected")."</td>"; 
                                }else{ 
                                    echo "<td></td>"; 
                                }
                                echo "</tr>";

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
<div class="modal-footer p-r-30">
	<button type="button" tabIndex="-1" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Cancel</button>
</div>


<script>

$(document).ready(function(){

//#ModalLabel
$('#ModalWin-Help-ModalLabel').html("<?php echo $ModalLabel; ?>");

});

</script>