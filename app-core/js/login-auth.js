/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @version     : 2.2.0
 **/

const loginLoader = "data:image/gif;base64,R0lGODlhEAALAPQAAP///wAAANra2tDQ0Orq6gYGBgAAAC4uLoKCgmBgYLq6uiIiIkpKSoqKimRkZL6+viYmJgQEBE5OTubm5tjY2PT09Dg4ONzc3PLy8ra2tqCgoMrKyu7u7gAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCwAAACwAAAAAEAALAAAFLSAgjmRpnqSgCuLKAq5AEIM4zDVw03ve27ifDgfkEYe04kDIDC5zrtYKRa2WQgAh+QQJCwAAACwAAAAAEAALAAAFJGBhGAVgnqhpHIeRvsDawqns0qeN5+y967tYLyicBYE7EYkYAgAh+QQJCwAAACwAAAAAEAALAAAFNiAgjothLOOIJAkiGgxjpGKiKMkbz7SN6zIawJcDwIK9W/HISxGBzdHTuBNOmcJVCyoUlk7CEAAh+QQJCwAAACwAAAAAEAALAAAFNSAgjqQIRRFUAo3jNGIkSdHqPI8Tz3V55zuaDacDyIQ+YrBH+hWPzJFzOQQaeavWi7oqnVIhACH5BAkLAAAALAAAAAAQAAsAAAUyICCOZGme1rJY5kRRk7hI0mJSVUXJtF3iOl7tltsBZsNfUegjAY3I5sgFY55KqdX1GgIAIfkECQsAAAAsAAAAABAACwAABTcgII5kaZ4kcV2EqLJipmnZhWGXaOOitm2aXQ4g7P2Ct2ER4AMul00kj5g0Al8tADY2y6C+4FIIACH5BAkLAAAALAAAAAAQAAsAAAUvICCOZGme5ERRk6iy7qpyHCVStA3gNa/7txxwlwv2isSacYUc+l4tADQGQ1mvpBAAIfkECQsAAAAsAAAAABAACwAABS8gII5kaZ7kRFGTqLLuqnIcJVK0DeA1r/u3HHCXC/aKxJpxhRz6Xi0ANAZDWa+kEAA7AAAAAAAAAAAA";

/** Encrypt. */
function data_encrypt() {
    var encrypt = new JSEncrypt();
    encrypt.setPublicKey($('#data_key').val());
    var encrypted = encrypt.encrypt($('#emp_usr_pass').val());
    $('#emp_usr_pass').val(encrypted);
}

/** Login */
$('document').ready(function () {

    // Validation
    $("#login-form").validate({
        rules: {
            emp_usr_id: { required: true },
            emp_usr_pass: { required: true },
        },
        messages: {
            emp_usr_id: "Please enter your username",
            emp_usr_pass: "Please enter your password",
        },
        errorPlacement: function (error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: submitForm
    });

    // Login submit
    function submitForm() {
        data_encrypt(); // Encrypt
        var data = $("#login-form").serialize();
         $.ajax({
            type: 'POST',
            url: 'post/login-main',
            data: data,
            beforeSend: function () {
                disable('btn-login');
                $("#btn-login").html('<span class="mdi mdi-swap-horizontal"></span> &nbsp; Validating');
            },
            success: function (response) {
              
                // if (response.includes("|")) {
                //     var parts = response.split("|");  
                //     if(parts.length > 1) {
                //         var uid = parts[1];
                //         swal.fire({
                //             title: '',
                //             text: 'An active session already exists. Continuing will terminate the previous session. Are you sure to continue ?',
                //             showCancelButton: true,
                //             confirmButtonText: 'Yes',
                //             cancelButtonText: 'No'
                //         }).then((result) => {
                //             if (result.value === true) {
                //              //goto_url("<?php echo APP_URL; ?>/logout"); loader_start();
                //             }
                //         });

                //         // Send AJAX POST request to logout
                //         $.ajax({
                //             url: './logout',
                //             method: 'POST',
                //             data: { uid: uid },
                //             success: function (logoutResponse) {
                //                 // Redirect or handle post-logout logic
                //                 window.location.href = "./login";
                //             },
                //             error: function () {
                //                 alert('Error : Logout failed.');
                //             }
                //         });
                //     }
                // }
                if(response == "exist") {          
                    swal.fire({
                        title: '',
                        text: 'An active session already exists. Continuing will terminate the previous session. Are you sure to continue ?',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.value === true) {
                            //window.location.href = '../sessionlogs-remove?id='+user_id;
                            //  window.location.href = "./sessionlogs-remove?id=".user_id;
                            check_sessionlogs_data(); 
                        }else{
                            window.location.href = "./"; //login page
                        }
                    });            
                } 
                else if(response == "ok") {
                    $("#btn-login").html('<img src="' + loginLoader + '" /> &nbsp; Sign In');
                    window.location.href = "./"; //dashbard page
                    setTimeout(function () { loader_start(); }, 100); 
                } else {
                    swal.fire('', response, 'warning');
                    loader_stop();
                    $('#emp_usr_pass').val('');
                    $("#btn-login").html('Sign In &nbsp; <span class="mdi mdi-chevron-right"></span>');
                    enable('btn-login');
                }
            },
            error: function (response) {
                loader_stop();
                alert('Error : Unable to process request, Please try again.');
                $('#emp_usr_pass').val('');
                $("#btn-login").html('Sign In &nbsp; <span class="mdi mdi-chevron-right"></span>');
                enable('btn-login');
            }
        });
        return false;
    }

    function check_sessionlogs_data(){
        var user_id = $("#emp_usr_id").val();
        var token = $("#app_token").val();
        var formData = new FormData();
        formData.append('cmd','ass_sessionlogs_data');
        // formData.append('sessflag','1');
        formData.append('token',token);
        formData.append('user_id',user_id);
        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            url: "post/data-post",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                if(response == "ok") {
                    $("#btn-login").html('<img src="' + loginLoader + '" /> &nbsp; Sign In');
                    window.location.href = "./";//login page
                    setTimeout(function () { loader_start(); }, 100);
                 }else {
                    swal.fire('', response, 'warning');
                    loader_stop();
                    $('#emp_usr_pass').val('');
                    $("#btn-login").html('Sign In &nbsp; <span class="mdi mdi-chevron-right"></span>');
                    enable('btn-login');
                }
            },
            error: function (result) {
                loader_stop();
                alert('Error : Unable to process request, Please try again.');
                $('#emp_usr_pass').val('');
                $("#btn-login").html('Sign In &nbsp; <span class="mdi mdi-chevron-right"></span>');
                enable('btn-login');
            }
        });
    }
    
    // $("#CUST_MOBILE").validate({
        
	// 	rules: {
	// 		CUST_MOBILE: {
	// 			required: true,
	// 			minlength: 10
	// 		},
	// 	},
	// 	messages: {
	// 		LoginMob: {
	// 			CUST_MOBILE: "Please enter mobile number",
	// 			minlength: "Please enter 10 digit number"
	// 		},
	// 	},
	// 	errorPlacement: function (error, element) {
	// 		if(element.closest('.input_div').length) {
	// 			error.insertAfter(element.closest('.input_div'));
	// 		} else {
	// 			error.insertAfter(element);
	// 		}
	// 	},
	// 	//submitHandler: SignInSubmit
	// });


});