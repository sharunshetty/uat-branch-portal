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
$page_pgm_code = "APPUSERSBLK";

$page_title = "User Block/Unblock";
$page_link = "./app-user-block";

$parent_page_title = "Go Back";
$parent_page_link = "./conf-users";

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
	<input type="hidden" name="cmd" value="app_user_block"/>
	<input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>
	<input type="hidden" name="OPERATION" id="OPERATION" value=""/>

        <div class="row">

            <div class="col-md-3 form-group">
                <label class="col-md-12 label_head">User ID <mand>*</mand></label>
                <div class="col-md-12">
                    <input type="text" name="USER_ID" id="USER_ID" placeholder="" maxlength="12" class="form-control border-input" autocomplete="none" readonly>
                </div>
            </div>

            <div class="col-md-3 form-group">
                <label class="col-md-12 label_head">&nbsp;</label>
                <div class="col-md-12">
                    <button type="button" onclick="fetch_help_modal('app_user_accounts','','modify'); return false;" class="btn btn-primary btn-block py-1" name="sbt-mod" id="sbt-mod"><i class="mdi mdi-magnify"></i> Search Data</button>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-3 form-group">
                <label class="col-md-12 label_head">Status <mand>*</mand></label>
                <div class="col-md-12">
                    <select name="STATUS" id="STATUS" class="form-control border-input" autocomplete="none">
                        <option value="">-- Select --</option>
                        <option value="B">Blocked</option>
                        <option value="U">Unblocked</option>
                    </select>
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
        $("#USER_ID").focus();

        //Modify
        $("#USER_ID").on('change', function(){
            on_change('app_user_block','modify',this.value);
        });

    });

</script>

</body>
</html>