<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

/** Mode */
$dest_id = (isset($_POST['dest_id'])) ? $main_app->strsafe_input($_POST['dest_id']) : ""; // Don't change
$start = (isset($_POST['start'])) ? $main_app->strsafe_input($_POST['start']) : ""; // Don't change
$filter = (isset($_POST['filter'])) ? $main_app->strsafe_input($_POST['filter']) : ""; // Don't change
$filter_val = (isset($_POST['filter_val'])) ? $main_app->strsafe_input($_POST['filter_val']) : ""; // Don't change

/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS";
$page_primary_key = "USER_ID";

/** Custom Search */
if($dest_id =='transfer'){	
	$add_sql = "{$page_primary_key} IS NOT NULL AND USER_STATUS = 'A' AND RESIGN_DATE IS NULL AND {$page_primary_key} <> 'SYSADMIN'";
}elseif($dest_id =='block'){	
	$add_sql = "{$page_primary_key} IS NOT NULL AND USER_STATUS IN ('A','B') AND RESIGN_DATE IS NULL AND {$page_primary_key} <> 'SYSADMIN'";
}else{

	//$add_sql = "{$page_primary_key} IS NOT NULL AND USER_STATUS NOT IN ('P','R') AND {$page_primary_key} <> 'SYSADMIN'";
	$add_sql = "{$page_primary_key} IS NOT NULL AND USER_STATUS NOT IN ('P')  AND {$page_primary_key} <> 'SYSADMIN'";

}
$add_data = array();
$additionalVars = "";
$pageRecords = 0;
if(!isset($start) || !is_numeric($start) ) { $start = 0; }
$limit = "30";

/** Modal Title */
$ModalLabel = "Search : Employee Lists";

?>

<div class="modal-body py-1" id="load-content">

	<!-- Filters -->
	<div class="row">

		<div class="col-md-3 form-group">
			<label class="col-md-12 label_head">Search Type</label>
			<div class="col-md-12">
			<select name="filter" id="filter" class="form-control border-input cust-input" autocomplete="none">
			<option value="">--- Choose ---</option>
			<option value="USER_ID">Employee ID</option>
			<option value="USER_FULLNAME">Employee Name</option>
			</select>
			</div>
		</div>

		<div class="col-md-4 form-group">
			<label class="col-md-12 label_head">Search</label>
			<div class="col-md-12">
			<input type="text" name="filter_val" id="filter_val" class="form-control border-input cust-input" autocomplete="none">
			</div>
		</div>

		<div class="col-md-4 form-group">
			<label class="col-md-12 label_head"><br/></label>
			<div class="col-md-12">
				<?php

				$sbtn_html = "fetch_help_modal(";
				$sbtn_html .= "decode_ajax('".$main_app->strsafe_ajax($_POST['cmd'])."'),";
				$sbtn_html .= "decode_ajax('".$main_app->strsafe_ajax($_POST['mSize'])."'),";
				$sbtn_html .= "decode_ajax('".$main_app->strsafe_ajax($dest_id)."'),";
				$sbtn_html .= "decode_ajax('".$main_app->strsafe_ajax('0')."'),";
				$sbtn_html .= "$('#filter').val(),";
				$sbtn_html .= "$('#filter_val').val()";
				$sbtn_html .= ");";

				?>
			<button type="button" onclick="<?php echo $sbtn_html; ?>" class="btn btn-primary px-4 py-1" name="sbt3" id="sbt3"><i class="mdi mdi-magnify"></i> Search</button>
			</div>
		</div>

	</div>


	<!-- Data -->
	<div class="row">
		<div class="col-md-12">

			<table class="app-help-table table table-striped table-sm" id="help-table">
			<thead class="th-help">
				<tr>
					<th>Employee Id</th>
					<th>Employee Name</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>

			
			<?php

			//Custom Search box
			if ($filter && $filter != NULL && $filter_val && $filter_val != NULL) {
				# code...
				$add_sql .= " AND UPPER({$filter}) LIKE UPPER(:{$filter})"; $add_data[$filter] = '%'.$filter_val.'%'; 
			}
			elseif ((!$filter || $filter == NULL) && $filter_val && $filter_val != NULL) {
				//Filter not set, But value sent
				$add_sql .= " AND (UPPER(USER_ID) LIKE UPPER(:USER_ID)"; $add_data['USER_ID'] = '%'.$filter_val.'%';
				$add_sql .= " OR UPPER(USER_FULLNAME) LIKE UPPER(:USER_FULLNAME))"; $add_data['USER_FULLNAME'] = '%'.$filter_val.'%';
			}


			//Total Results
			$totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM {$page_table_name} WHERE {$add_sql}",$add_data);

				if($totalResults) {
					$pageRecords = "0";
					$final_query = "SELECT * FROM {$page_table_name} WHERE {$add_sql} ORDER BY CR_ON DESC";
					$sql_exe = $main_app->sql_dataTable($final_query,$add_data,$start,$limit); 
					while ($row = $sql_exe->fetch()) {

						echo "<tr>";
						echo "<td>".$main_app->strsafe_output($row['USER_ID'])."</td>";
						echo "<td>".$main_app->strsafe_output($row['USER_FULLNAME'])."</td>";
						//if(isset($row['USER_STATUS']) && $row['USER_STATUS'] == "1") { echo "<td>".$main_app->strsafe_output("Enabled")."</td>"; } else { echo "<td>".$main_app->strsafe_output("Disabled")."</td>"; }
						

						if(isset($row['USER_STATUS']) && $row['USER_STATUS'] == "A") { 
							// echo "<td>".$main_app->strsafe_output("Active")."</td>";
							$STATUS = "Active";    
							if(isset($row['RESIGN_DATE']) && $row['RESIGN_DATE'] != "") {
								$CUR_DATE = new DateTime(); 
								$RESIGN_DATE = new DateTime($row['RESIGN_DATE']); 
					
								if($RESIGN_DATE <= $CUR_DATE) {
									$STATUS = "Resigned";
								}		
							}
							echo "<td>".$main_app->strsafe_output($STATUS)."</td>";
						}elseif(isset($row['USER_STATUS']) && $row['USER_STATUS'] == "B") { 
							echo "<td>".$main_app->strsafe_output("Blocked")."</td>"; 
						}elseif(isset($row['USER_STATUS']) && $row['USER_STATUS'] == "T") { 
							echo "<td>".$main_app->strsafe_output("Transfer Branch Pending")."</td>"; 
						}else{ 
							echo "<td></td>"; 
						}
						
						// elseif(isset($row['USER_STATUS']) && $row['USER_STATUS'] == "F") { 
						// 	echo "<td>".$main_app->strsafe_output("Inactive")."</td>"; 
						// }
						
						echo "</tr>";

						$pageRecords = $pageRecords + 1;

					}
				} else {
					echo '<tr><td colspan="3">No records found</td></tr>'; 
				}
				?>
			</tbody>
			</table>

			<div class="row">
				<div class="col-md-6 form-group text-muted small">
					<?php echo $main_app->sql_dataTable_count($totalResults,$start,$limit); ?>
				</div>
				<div class="col-md-6 form-group text-right">
					<?php echo $main_app->page_nav_helpbox($_POST['cmd'],$_POST['mSize'],$dest_id,$totalResults,$start,$limit,$pageRecords,$filter,$filter_val); ?>
				</div>
			</div>

		</div>
	</div>

</div>    
<div class="modal-footer p-r-30">
	<button type="button" tabIndex="-1" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Cancel</button>
</div>

<script type="text/javascript">


	$(document).ready(function(){

		//Select Custom Search
		<?php if($filter && $filter != NULL) { ?>
		$("select[name='filter'] option[value='"+decode_ajax('<?php echo $main_app->strsafe_ajax($filter); ?>')+"']").prop("selected", true);
		<?php } ?>
		<?php if($filter_val && $filter_val != NULL) { ?>
		$('#filter_val').val(decode_ajax('<?php echo $main_app->strsafe_ajax($filter_val); ?>'));
		<?php } ?>

		<?php if(isset($pageRecords) && $pageRecords > "0") { ?>

		var table = $('#help-table').DataTable({ 
			'pageLength': "<?php echo $pageRecords; ?>",
			'ordering': false, 'bPaginate': false, 'info': false, 'bFilter': false,
		});

		$('#help-table tbody').on('click', 'tr', function () {
			var cursor = table.row($(this));
			var data = cursor.data();
			var dest_id = "<?php echo $dest_id; ?>";

			$('input[name="USER_ID"]').val(data[0]).trigger('change');

			$('#ModalWin-Help').modal('hide');
			//$('input[name='+dest_id+']').trigger('change');
			//$('#CUSTOMER_ID_NAME').html(data[1]);
		});
		<?php } ?>

    	//#ModalLabel
		$('#ModalWin-Help-ModalLabel').html("<?php echo $ModalLabel; ?>");

	});


</script>