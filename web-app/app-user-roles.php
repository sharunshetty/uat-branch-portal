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
$page_pgm_code = "APPROLES";

$page_title = "User Roles";
$page_link = "./app-user-roles";

$parent_page_title = "Go Back";
$parent_page_link = "./";

$page_table_name = "ASSREQ_APP_PROGRAMS";
$page_primary_key = "PGM_CODE";

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
?>

<!-- Content : Start -->
<section class="content">
<div class="container-fluid">
<div class="row">
<div class="col-12">
	
	<div class="card card-outline card-brand">
	<div class="card-body min-high2">
	<form id="app-form" name="app-form" method="post" action="javascript:void(null);" class="form-material">
	<input type="hidden" name="cmd" value="app_user_roles"/>
	<input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>

    	<div class="row">
		
			<div class="col-md-4 col-lg-3 form-group">
				<label class="col-md-12 label_head">Operation <mand>*</mand></label>
				<div class="col-md-12">
					<select name="OPERATION" id="OPERATION" class="form-control border-input" autocomplete="none" onLoad="$this.focus();">
						<option value="">-- Select --</option>
						<option value="A">Add</option>
						<option value="M">Modify</option>
					</select>
				</div>
			</div>

			<div class="col-md-4 col-lg-3 form-group" id="modify_btn" style="display: none;">
				<label class="col-md-12 label_head">&nbsp;</label>
				<div class="col-md-12">
					<button type="button" onclick="fetch_help_modal('app_user_roles','','modify'); return false;" class="btn btn-primary btn-block py-1" name="sbt-mod" id="sbt-mod"><i class="mdi mdi-magnify"></i> Search Data</button>
				</div>
			</div>

		</div>
		<div class="row">

			<div class="col-md-4 col-lg-3 form-group">
				<label class="col-md-12 label_head">Role Code <mand>*</mand></label>
				<div class="col-md-12">
				<input type="text" name="ROLE_CODE" id="ROLE_CODE" placeholder="Unique code" maxlength="12" class="form-control border-input js-noSpace js-toUpper reset-form" autocomplete="none">
				</div>
			</div>

			<div class="col-md-4 col-lg-3 form-group">
				<label class="col-md-12 label_head">Role Name <mand>*</mand></label>
				<div class="col-md-12">
				<input type="text" name="ROLE_DESC" id="ROLE_DESC" placeholder="" maxlength="120" class="form-control border-input js-toUpper reset-form" autocomplete="none">
				</div>
			</div>

			<div class="col-md-4 col-lg-3 form-group">
				<label class="col-md-12 label_head">Role Status <mand>*</mand></label>
				<div class="col-md-12">
				<select name="ROLE_STATUS" id="ROLE_STATUS" class="form-control border-input reset-form" autocomplete="none">
					<option value="">-- Select --</option>
					<option value="1">Enable</option>
					<option value="2">Disable</option>
				</select>
				</div>
			</div>

		</div>

		<div class="col-md-12 col-lg-12 px-3 mt-2 text-primary">Program Details</div>

		<div class="col-md-12 px-3 mt-2">
          <div class="table-responsive">
            <table class="app-data-table table table-bordered table-striped table-sm" id="resp-table">
            <thead>
              <tr>
                <th>Sl No.</th>
                <th>Program Code</th>
                <th>Program Name</th>
                <th class='text-center'>Select</th>
              </tr>
            </thead>
            </tbody id="tbody">

				<?php
				
				//Total Results
				$totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM {$page_table_name}","");

					if($totalResults) {
						$sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name}");
						$i = 1;
						while ($row = $sql_exe->fetch()) {
							echo "<tr class='no_records' id='no_record'>";
							echo "<td>".$i."</td>";
							echo "<td>".$main_app->strsafe_output($row['PGM_CODE'])."</td>";
							echo "<td>".$main_app->strsafe_output($row['PGM_NAME'])."</td>";
							echo "<td class='text-center'><input type='checkbox' class='form-radio checkbox PGM_CODE' name='DTL_PGM_CODE[]' id='PGM_".$main_app->strsafe_output($row['PGM_CODE'])."' value='".$main_app->strsafe_output($row['PGM_CODE'])."'></td>";
							echo "</tr>";
							$i++;
						}
					} else {
						echo '<tr><td colspan="4">No records found</td></tr>'; 
					}
				?>

			</tbody>
            </table>
          </div>
        </div>
			
		<div class="form-footer">
			<div class="col-md-12 text-right">
				<button type="button" tabIndex="-1" class="btn btn-outline-secondary mr-2" onclick="$('#app-form').trigger('reset');$('.reset-form').empty();">Reset</button>
				<button type="button" class="btn btn-success px-4" name="sbt" id="sbt" onclick="send_form(); return false;">Submit</button>
			</div>
		</div>

	</form>
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

      //Focus & Start Operation
	  $("#OPERATION").focus();
	  $("#OPERATION").on('change', function(){
		  var op = $("#OPERATION").val();
		  $('.reset-form').val('');
		  if(op == "M") {
			show('modify_btn');
			$("#ROLE_CODE").attr("readonly", "readonly");
		  } else {
			hide('modify_btn');
			$("#ROLE_CODE").prop("readonly", false);
		  }
	  });

	  //Modify
	  $("#ROLE_CODE").on('change', function(){
		  var op = $("#OPERATION").val();
		  if(op == "M") {
			on_change('app_user_roles','modify',this.value);
		  }
	  });

    });

</script>

</body>
</html>