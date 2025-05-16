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
    echo "<script> swal.fire('','Invalid Request'); $('#ModalWin').modal('hide'); </script>";
    exit();
}

if(!isset($_POST['id2']) || $primary_value != $_POST['id2']) {
	echo "<script> swal.fire('','Reference Number is not matched'); $('#ModalWin').modal('hide'); </script>";
    exit();
}

if(isset($primary_value)) {
	
	$page_primary_keys = array(
		'AUTH_REF_NUM' => (isset($primary_value)) ? $main_app->strsafe_input($primary_value) : ""
	);

	$sql_exe = $main_app->sql_run("SELECT * FROM ASSREQ_USER_ACCOUNTS_DTL WHERE AUTH_REF_NUM = :AUTH_REF_NUM", $page_primary_keys);
    $item_data = $sql_exe->fetch();
}

if(isset($item_data['AUTH_STATUS']) && $item_data['AUTH_STATUS'] != "P") {
	echo "<script> swal.fire('','Status is already updated for this request'); $('#ModalWin').modal('hide'); </script>";
    exit();
}

//if(isset($item_data['CR_BY']) && $item_data['CR_BY'] == $_SESSION['USR_ID'] && $_SESSION['USER_ROLE_CODE'] != "ADMIN") {
// 	echo "<script> swal.fire('','Maker and Authorizer cannot be same. Please try with other user.'); $('#ModalWin').modal('hide'); </script>";
//     exit();
// }

/** Modal Title */
$ModalLabel = " Authorise Detail - " . $primary_value;

?>

<form id="app-form-2" name="app-form-2" method="post" action="javascript:void(null);" class="form-material">
	<input type="hidden" name="HIDAUTHREF_NUM" id="HIDAUTHREF_NUM"  value="<?php echo $primary_value; ?>" />
	<input type="hidden" name="cmd" value="user-authorise-detail" />
	<input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>" />
	<input type="hidden" name="id" id="id" value="<?php echo (isset($_POST['id'])) ? $_POST['id'] : ""; ?>" />
	<div class="modal-body min-div" id="load-content">

		<div class="row mt-2">
			<div class="col-md-12 form-group">
				<label class="col-md-12 label_head">Next Action <mand>*</mand></label>
				<div class="col-md-12">
					<input type="radio" class="form-radio auth_status" id="AUTH_STATUS_1" name="AUTH_STATUS" value="S" autocomplete="off"><label class="font-weight-bold text-success" for="AUTH_STATUS_1"> Approve </label>
					<input type="radio" class="form-radio auth_status ml-5" id="AUTH_STATUS_2" name="AUTH_STATUS" value="R" autocomplete="off"><label class="font-weight-bold text-danger" for="AUTH_STATUS_2"> Reject </label>
				</div>
			</div>
		</div>

		<div class="row mt-2">
			<div class="col-lg-8 form-group">
				<label class="col-md-12 label_head">Remarks <mand>*</mand></label>
				<div class="col-md-12">
					<textarea name="REMARKS" id="REMARKS" maxlength="250" rows="5" class="form-control border-input js-maxCheck no-resize" autocomplete="none"></textarea>
				</div>
			</div>
		</div>

	</div>
	<div class="modal-footer p-r-30">
		<button type="button" tabIndex="-1" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-success px-4" name="sbt2" id="sbt2" onclick="send_form('app-form-2','sbt2'); return false;"> Save <i class="mdi mdi-arrow-right"></i> </button>
	</div>
</form>

<script type="text/javascript">
	
	$(document).ready(function() {

		//#ModalLabel
		$('#ModalWin-ModalLabel').html("<?php echo $ModalLabel; ?>");

	});
</script>