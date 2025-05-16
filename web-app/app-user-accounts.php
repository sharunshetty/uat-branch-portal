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
$page_pgm_code = "APPUSERS";

$page_title = "User Accounts";
$page_link = "./app-user-accounts";

$parent_page_title = "Go Back";
$parent_page_link = "./index";


$page_table_name = "LOC_REGION_MASTER";
$page_primary_key = "REGION_CODE";

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
	<input type="hidden" name="cmd" value="app_user_accounts"/>
	<input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>

		

    	<div class="row">

			<div class="col-md-12 form-group m-0">
				<div class="row">

					<div class="col-md-3 form-group">
						<label class="col-md-12 label_head">Operation <mand>*</mand></label>
						<div class="col-md-12">
							<select name="OPERATION" id="OPERATION" class="form-control border-input" autocomplete="none" onLoad="$this.focus();">
								<option value="">-- Select --</option>
								<option value="A">Add</option>
								<option value="M">Modify</option>
							</select>
						</div>
					</div>

					<div class="col-md-9 form-group" id="add_btn" style="display: none;">
						<div class="row">
							<div class="col-md-4 form-group  m-0">
								<label class="col-md-12 label_head">Enter Employee ID <mand>*</mand></label>
								<div class="col-md-12">
								<input type="text" name="FIND_USER_ID" id="FIND_USER_ID" maxlength="30" class="form-control border-input js-noSpace js-toUpper reset-srch" autocomplete="none">
								</div>
							</div>

							<div class="col-md-6 form-group m-0">
								<label class="col-md-12 label_head">&nbsp;</label>
								<div class="col-md-6">
								<button type="button" class="btn btn-primary btn-block py-1" id="fetch_dtls">Fetch Details</button>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3 form-group" id="modify_btn" style="display: none;">
						<label class="col-md-12 label_head">&nbsp;</label>
						<div class="col-md-12">
							<button type="button" onclick="fetch_help_modal('app_user_accounts','','modify'); return false;" class="btn btn-primary btn-block py-1" name="sbt-mod" id="sbt-mod"><i class="mdi mdi-magnify"></i> Search Data</button>
						</div>
					</div>

				</div>
			</div>
			
		</div>

		<div class="row">

			<div class="col-md-12 form-group">

				<div class="row">

					<div class="col-md-3 form-group">
						<label class="col-md-12 label_head">Employee Id <mand>*</mand></label>
						<div class="col-md-12">
						<input type="text" name="USER_ID" id="USER_ID" placeholder="Unique code" maxlength="12" class="form-control border-input js-noSpace js-toUpper reset-form" autocomplete="none" readonly>
						</div>
					</div>

					<div class="col-md-3 form-group">
						<label class="col-md-12 label_head">Full Name <mand>*</mand></label>
						<div class="col-md-12">
						<input type="text" name="USER_FULLNAME" id="USER_FULLNAME" placeholder="" maxlength="120" class="form-control border-input js-toUpper reset-form" autocomplete="none">
						</div>
					</div>

					<div class="col-md-3 form-group hide_password">
						<label class="col-md-12 label_head">Password <mand>*</mand></label>
						<div class="col-md-12">
							<input type="password" name="USR_PASSWORD1" id="USR_PASSWORD1" placeholder="" maxlength='20' class="form-control reset-form" autocomplete="off">
						</div>
					</div>

					<div class="col-md-3 form-group hide_password">
						<label class="col-md-12 label_head">Confirm Password <mand>*</mand></label>
						<div class="col-md-12">
							<input type="password" name="USR_PASSWORD2" id="USR_PASSWORD2" placeholder=""  maxlength='20' class="form-control reset-form" autocomplete="off">
						</div>
					</div>

					<div class="col-md-3 form-group">
						<label class="col-md-12 label_head">Role Code <mand>*</mand></label>
						<div class="col-md-12">
							<select name="USER_ROLE_CODE" id="USER_ROLE_CODE" class="form-control border-input reset-form" autocomplete="off">
								<option value="">-- Select --</option>
								<?php
									$sql_exe = $main_app->sql_run("SELECT ROLE_CODE,ROLE_DESC FROM ASSREQ_USER_ROLES where ROLE_STATUS = '1'");
									while ($row = $sql_exe->fetch() ) {
										echo '<option value="'.$row['ROLE_CODE'].'">'. $row['ROLE_DESC'] .' ['. $row['ROLE_CODE'] . ']</option>';
									}
								?>
							</select>
						</div>
					</div>	

					<div class="col-md-3 form-group">
						<label class="col-md-12 label_head">User Email <mand>*</mand></label>
						<div class="col-md-12">
							<input type="text" name="USER_EMAIL" id="USER_EMAIL" placeholder="" maxlength="120" class="form-control border-input reset-form" autocomplete="none">
						</div>
					</div>

					<div class="col-md-3 form-group">
						<label class="col-md-12 label_head">User Mobile <mand>*</mand></label>
						<div class="col-md-12">
							<input type="tel" name="USER_MOBILE" id="USER_MOBILE" placeholder="" maxlength="10" class="form-control border-input js-isNumeric reset-form" autocomplete="none">
						</div>
					</div>

					<!-- <div class="col-md-6 form-group">
						<label class="col-md-12 label_head">Processing Region <mand>*</mand></label>
						<div class="col-md-12">
						<select name="USER_REGIONS" id="USER_REGIONS" class="form-control border-input" autocomplete="none">
							<option value="">-- Select --</option>
							<option value="A">All Region</option>
							<option value="S">Specific</option>
						</select>
						</div>
					</div> -->

					<div class="col-md-3 form-group">
						<label class="col-md-12 label_head">Bank Branch <mand>*</mand></label>
						<div class="col-md-12">
						<select name="USER_REGIONS" id="USER_REGIONS" class="form-control border-input reset-form" autocomplete="none" readonly>
							<option value="">-- Select --</option>
							<option value="1">dddddd</option>
							<option value="2">dddddd1</option>
							<?php
							  ////$sql_exe = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_ID=:user_id and w.user_susp_rel_flag=' ' and m.mbrn_code=w.user_branch_code", array("user_id" => $_SESSION['USER_ID']));
							//   $sql_exe = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_susp_rel_flag=' ' and m.mbrn_code=w.user_branch_code");
                           	//  while ($row = $sql_exe->fetch()) {
                            //     echo "<option value=".$row['USER_BRANCH_CODE'].">". $row['USER_BRANCH_CODE'] . '-'.$row['MBRN_NAME']. "</option>";  

                            //   }


							//   $sql_exe = $main_app->sql_run("select mbrn_name,mbrn_code from cbuat.mbrn");
							//  while ($row = $sql_exe->fetch()) {
                            //     echo "<option value=".$row['MBRN_CODE'].">". $row['MBRN_CODE'] . '-'.$row['MBRN_NAME']. "</option>";  

                            //   }
                            ?>
							<!-- <option value="">-- Select --</option>
							<option value="A">All Region</option>
							<option value="S">Specific</option> -->
						</select>
						</div>
					</div>

					<div class="col-md-3 form-group">
						<label class="col-md-12 label_head">User Status <mand>*</mand></label>
						<div class="col-md-12">
						<select name="USER_STATUS" id="USER_STATUS" class="form-control border-input reset-form" autocomplete="none">
							<option value="">-- Select --</option>
							<option value="A">Active</option>
							<option value="R" class="d-none">Resigned</option>
							<option value="B" class="d-none">Blocked</option>	
							<option value="T" class="d-none">Transfer Branch Pending</option>			
						</select>
						<!-- <option value="F">Inactive</option>				<option value="T">Transfer of User </option> -->
						</div>
					</div>

				</div>

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
		$("#OPERATION").focus();
		$("#OPERATION").on('change', function(){
			$('.reset-form,.reset-srch').val('');
			var op = $("#OPERATION").val();
			if(op == "M") {

				show('modify_btn');
				hide('add_btn');
				//$("#USER_ID").attr("readonly", "readonly");
				$(".hide_password").hide();
				$('#USER_STATUS').attr('readonly', true);

			} else {

				hide('modify_btn');
				show('add_btn');
				//$("#USER_ID").prop("readonly", false);
				$(".hide_password").show();
				$('#USER_STATUS').attr('readonly', false);
			
			}
		});

		//Modify
		$("#USER_ID").on('change', function(){
			var op = $("#OPERATION").val();
			if(op == "M") {
				on_change('app_user_accounts','modify',this.value);
			}
		});

		
		// $("#USER_REGIONS").on('change', function(){
		// 	var val = $("#USER_REGIONS").val();
		// 	if(val == "S") {
		// 	show('REGION_HIDE');
		// 	$('input[type=checkbox]').prop('checked',false);
		// 	} else {
		// 	hide('REGION_HIDE');
		// 	}
		// });


    });

	//fetch details
	$("#fetch_dtls").on('click', function() {
		$('.reset-form').val('');
		var USER_ID = $("#FIND_USER_ID").val();
		if(USER_ID == "") {
			swal.fire('','Enter User id for search');
		} else {
			on_change('userid_fetch_details','modify',USER_ID);
		}
	});


  </script>

</body>
</html>