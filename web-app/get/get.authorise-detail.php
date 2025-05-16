<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') or exit();

/** Mode */
$page_mode = (isset($_POST['data_mode'])) ? $main_app->strsafe_input($_POST['data_mode']) : ""; // Don't change - V = View, M = Modify Data

/** Get Data */
if (isset($_POST['id']) && $_POST['id'] != NULL) {
	$primary_value = $safe->str_decrypt($_POST['id'], $_SESSION['SAFE_KEY']);
}

if(!isset($primary_value) || $primary_value == false) {
    echo "<script> swal.fire('','Unable to fetch the data'); $('#ModalWin').modal('hide'); </script>";
    exit();
}

$primary_value = $main_app->strsafe_output($primary_value);

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";


/** Modal Title */
$ModalLabel = "Authorise Detail - " . $primary_value;

?>

	<form id="authorise-request" name="authorise-request" method="post" action="javascript:void(null);" class="form-material">
	<input type="hidden" name="cmd" value="authorise_details" />
	<input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>" />
	<input type="hidden" name="id" id="id" value="<?php echo (isset($_POST['id'])) ? $_POST['id'] : ""; ?>" />

		<div class="modal-body min-div" id="load-content">

			<div class="row mt-2">
				<div class="col-md-12 form-group">
					<label class="col-md-12 label_head">Next Action <mand>*</mand></label>
					<div class="col-md-12">
						<input type="radio" class="form-radio auth_status" id="AUTH_STATUS_1" name="AUTH_STATUS" value="AS" autocomplete="off"><label class="font-weight-bold text-success" for="AUTH_STATUS_1"> Approve </label>
						<input type="radio" class="form-radio auth_status ml-5" id="AUTH_STATUS_2" name="AUTH_STATUS" value="AR" autocomplete="off"><label class="font-weight-bold text-danger" for="AUTH_STATUS_2"> Reject </label>
					</div>
				</div>
			</div>

			<div class="row mt-2">
				<div class="col-md-12 col-lg-8 form-group">
					<label class="col-md-12 label_head">Remarks <mand>*</mand></label>
					<div class="col-md-12">
						<textarea name="REMARKS" id="REMARKS" maxlength="250" rows="6" class="form-control border-input js-maxCheck js-noEnter no-resize" autocomplete="none"></textarea>
					</div>
				</div>
			</div>

		</div>

		<div class="modal-footer p-r-30">
			<button type="button" tabIndex="-1" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-success px-4" name="sbt" id="sbt" onclick="send_form('authorise-request','sbt'); return false;"> Submit <i class="mdi mdi-arrow-right"></i> </button>
		</div>

	</form>

<script type="text/javascript">
	
	$(document).ready(function() {

		//#ModalLabel
		$('#ModalWin-ModalLabel').html("<?php echo $ModalLabel; ?>");

	});

</script>