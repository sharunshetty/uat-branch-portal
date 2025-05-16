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
//$page_pgm_code = "APPUSERS";

$page_title = "Change Password";
$page_link = "./user-change-pass";

$parent_page_title = "Go Back";
$parent_page_link = "./";

$page_table_name = "APP_PROGRAMS";
$page_primary_key = "PGM_CODE";

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
?>

<!-- Content : Start -->
<section class="content">
<div class="container-fluid">
<div class="row">
<div class="col-md-12">
	
	<div class="card card-outline card-brand">
	<div class="card-body min-high2">
	<form id="app-form" name="app-form" method="post" action="javascript:void(null);" class="form-material">
	<input type="hidden" name="cmd" value="app_user_chg_pass"/>
	<input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>

    	<div class="row mt-3">
            <div class="col-md-6">
                <div class="row">
        
                    <div class="col-md-6 col-lg-6 form-group">
                        <div class="row">
                            <label class="col-md-12 label_head">Old Password <mand>*</mand></label>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="password" name="OLD_PASSWORD" id="OLD_PASSWORD" placeholder="" maxlength='20' class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        
                <div class="row">

                    <div class="col-md-6 col-lg-6 form-group">
                        <div class="row">
                            <label class="col-md-12 label_head">New Password <mand>*</mand></label>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="password" name="USR_PASSWORD1" id="USR_PASSWORD1" placeholder="" maxlength='20' class="form-control" autocomplete="off">
                                </div>
                                <span id="errorname" class="small text-danger"></span>
                            </div>
                        </div>
                    </div> 
                
                    <div class="col-md-6 col-lg-6 form-group">
                        <div class="row">
                            <label class="col-md-12 label_head">Confirm New Password <mand>*</mand></label>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="password" name="USR_PASSWORD2" id="USR_PASSWORD2" placeholder="" maxlength='20' class="form-control" autocomplete="off">
                                </div>
                                <span id="errorname2" class="small text-danger"></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="row justify-content-center">
                    <div class="card border mt-2">
                        <div class="card-header text-primary h6"><i class="mdi mdi-information-outline mdi-18px"></i> Password Requirements</div>
                        <div class="card-body bg-light">
                            <ul class="text-dark pl-3 pr-5">
                                <li>Password must contain at least one uppercase letter</li>
                                <li>Password should be greater than 8 characters</li>
                                <li>Password must contain at least one lowercase letter</li>
                                <li>Password must contain at least one number</li>
                                <li>Password must contain at least one special character</li>
                            </ul>
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

       $('#USR_PASSWORD1,#USR_PASSWORD2').on('change',function(){
            var pwd1 = $('#USR_PASSWORD1').val();
            var pwd2 = $('#USR_PASSWORD2').val();

            var len = $('#USR_PASSWORD1').val().length;
            upper = /[A-Z]/;
            lower = /[a-z]/;
            number = /[0-9]/;
            var regularExpression  = /[!@#$%^&*]/;
            var disply = document.getElementById('errorname');

            if( !upper.test(pwd1) ) {
                show('errorname');
                disply.innerHTML="Password must contain at least one uppercase letter!";
            }
            else if ( len < 8 ) {
                show('errorname');
                disply.innerHTML="Password should be greater than 8 characters!";
            }else if( !lower.test(pwd1) ) {
                show('errorname');
                disply.innerHTML="Password must contain at least one lowercase letter!";
            } else if( !number.test(pwd1) ) {
                show('errorname');
                disply.innerHTML="Password must contain at least one number!";
            } else if( !regularExpression.test(pwd1) ) {
                show('errorname');
                disply.innerHTML="Password must contain at least one special character!";
            } else if(pwd2!="" && pwd1!="" && pwd1 != pwd2){
                hide('errorname');
                show('errorname2');
                var disply2 = document.getElementById('errorname2');
                disply2.innerHTML="Password & Confirm password not matching!";
            } else {
                hide('errorname');
                hide('errorname2');
            }
	    });

    });

  </script>

</body>
</html>