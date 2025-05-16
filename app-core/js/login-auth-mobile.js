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
    // var encrypted = encrypt.encrypt($('#emp_usr_pass').val());
    // $('#emp_usr_pass').val(encrypted);
}

/** Login */
$('document').ready(function () {

    // Validation
    $("#login-form").validate({
        rules: {
            emp_usr_id: { required: true }
        },
        messages: {
            emp_usr_id: "Please enter your mobile number"
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
                if(response == "ok") {
                    $("#btn-login").html('<img src="' + loginLoader + '" /> &nbsp; Signing In');
                    window.location.href = "./";
                    setTimeout(function () { loader_start(); }, 100);
                } else {
                    if(response.search(/refresh/i) == -1) {
                        swal.fire('', response, 'warning');
                    } else {
					    swal.fire({ title: '', text: response, type: 'warning', confirmButtonText: '<i class="mdi mdi-refresh mdi-spin"></i> Refresh' });
                        setTimeout(' window.location.href = "./login"; ', 200);
                    }
                    // swal.fire('', response, 'warning');
                    // $('#emp_usr_pass').val('');
                    $("#btn-login").html('Sign In &nbsp; <span class="mdi mdi-chevron-right"></span>');
                    enable('btn-login');
                }
            },
            error: function (response) {
                alert('Error : Unable to process request, Please try again.');
                // $('#emp_usr_pass').val('');
                $("#btn-login").html('Sign In &nbsp; <span class="mdi mdi-chevron-right"></span>');
                enable('btn-login');
            }
        });
        return false;
    }

});