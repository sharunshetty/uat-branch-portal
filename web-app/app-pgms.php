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
$page_pgm_code = "APPPG";

$page_title = "Programs List";
$page_link = "./app-pgms";

$parent_page_title = "Go Back";
$parent_page_link = "./conf-masters";

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
	<form id="app-form" name="app-form" method="post" action="javascript:void(null);" class="form-material" autocomplete="off">
	<input type="hidden" name="cmd" value="app_pgms"/>
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
					<button type="button" onclick="fetch_help_modal('app_pgms','','modify'); return false;" class="btn btn-primary btn-block py-1" name="sbt-mod" id="sbt-mod"><i class="mdi mdi-magnify"></i> Search Data</button>
				</div>
			</div>

		</div>
		<div class="row">

			<div class="col-md-4 col-lg-3 form-group">
				<label class="col-md-12 label_head">Program Code <mand>*</mand></label>
				<div class="col-md-12">
				<input type="text" name="PGM_CODE" id="PGM_CODE" placeholder="Unique code" maxlength="20" class="form-control border-input js-noSpace js-toUpper" autocomplete="none">
				</div>
			</div>

			<div class="col-md-4 col-lg-3 form-group">
				<label class="col-md-12 label_head">Program Name <mand>*</mand></label>
				<div class="col-md-12">
				<input type="text" name="PGM_NAME" id="PGM_NAME" placeholder="" maxlength="150" class="form-control border-input" autocomplete="none">
				</div>
			</div>

			<div class="col-md-4 col-lg-3 form-group">
				<label class="col-md-12 label_head">Program File Path <mand>*</mand></label>
				<div class="col-md-12">
				<input type="text" name="PGM_FILE_PATH" id="PGM_FILE_PATH" placeholder="" maxlength="60" class="form-control border-input" autocomplete="none">
				</div>
			</div>
			
			<div class="col-md-4 col-lg-3 form-group">
				<label class="col-md-12 label_head">MDI Icon Code </label>
				<div class="col-md-12">
				<input type="text" name="PGM_MDI_ICON" id="PGM_MDI_ICON" placeholder="" maxlength="60" class="form-control border-input" autocomplete="none">
				</div>
			</div>

		</div>
		<div class="row">

			<div class="col-md-6">
			<div class="row">

				<div class="col-md-12 form-group">
					<label class="col-md-12 label_head">Description</label>
					<div class="col-md-12">
					<textarea name="PGM_DESC" id="PGM_DESC" maxlength="250" rows="5" class="form-control border-input js-maxCheck no-resize" autocomplete="none"></textarea>
					</div>
				</div>

			</div>
			</div>
			<div class="col-md-6">
			<div class="row">

				<div class="col-md-6 col-lg-6 form-group">
					<label class="col-md-12 label_head">Program Category <mand>*</mand></label>
					<div class="col-md-12">
					<select name="PGM_CATEGORY" id="PGM_CATEGORY" class="form-control border-input" autocomplete="none">
						<option value="">-- Select --</option>
						<option value="A">Admin</option>
						<option value="U">User</option>
					</select>
					</div>
				</div>

				<div class="col-md-6 col-lg-6 form-group">
					<label class="col-md-12 label_head">Program Status <mand>*</mand></label>
					<div class="col-md-12">
					<select name="PGM_STATUS" id="PGM_STATUS" class="form-control border-input" autocomplete="none">
						<option value="">-- Select --</option>
						<option value="1">Enable</option>
						<option value="2">Disable</option>
					</select>
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

      //Focus & Start Operation
	  $("#OPERATION").focus();
	  $("#OPERATION").on('change', function(){
		  var op = $("#OPERATION").val();
		  if(op == "M") {
			show('modify_btn');
			//$("#PGM_CODE").attr("readonly", "readonly");
		  } else {
			hide('modify_btn');
			//$("#PGM_CODE").prop("readonly", false);
		  }
	  }); 

	  //Modify
	  $("#PGM_CODE").on('change', function(){
		  var op = $("#OPERATION").val();
		  if(op == "M") {
			on_change('app_pgms','modify',this.value);
		  }
	  });

	});

  </script>

</body>
</html>