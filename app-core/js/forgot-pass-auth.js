/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 * @version     : 2.2.0
 **/

/** Encrypt. */
function data_encrypt() {
    var encrypt = new JSEncrypt();
    encrypt.setPublicKey($('#data_key').val());
    var cust_id = encrypt.encrypt($('#USER_ID').val());
    var mob_num = encrypt.encrypt($('#MOBILE_NUM').val());
    $('#HID_USRID').val(cust_id);
    $('#HID_MOBILE_NUM').val(mob_num);
}

/** Login */
$('document').ready(function () {

    // Validation
    $("#forgot-form").validate({
        groups: {
            ac_num: "USER_ID MOBILE_NUM SC_CODE"
        },

        rules: {
            USER_ID: {
                required: true,
            },
            MOBILE_NUM: {
                required: true,
            },
            SC_CODE: {
                required: true,
            }
        },
        messages: {
            USER_ID: {
                required: "Please enter your user id",
            },
            MOBILE_NUM: {
                required: "Please enter your mobile number"
            },
            SC_CODE: {
                required: "Please enter security code"
            },
        },

        success: function (element) {
            $("#error").html('');
        },
        errorPlacement: function (error, element) {
            $("#error").html('<div class="alert alert-danger small py-1"> <span class="mdi mdi-information-outline"></span> &nbsp; Please fill empty fields! </div>');
        },
        submitHandler: submitForm
    });

    // Forgot Password submit
    function submitForm() {
        data_encrypt(); // Encrypt
        var data = $("#forgot-form").serialize();
        $.ajax({
            type: 'POST',
            url: 'post/forgot-pass',
            data: data,
            beforeSend: function () {
                disable('btn-forgot');
            },
            success: function (response) {
                if (response == "ok") {
                    window.location.href = "forgot-pass-otp";
                    setTimeout(function () { loader_start(); }, 100);
                } else {
                    swal.fire('', response, 'warning');
                    //$('#USER_ID').val('');
                    //$('#MOBILE_NUM').val('');
                    $('#SC_CODE').val('');
                    $('#HID_USRID').val('');
                    $('#HID_MOBILE_NUM').val('');
                    enable('btn-forgot');
                    refreshCaptcha();
                }
            },
            error: function (response) {
                alert('Error : Unable to process request, Please try again.');
                //$('#USER_ID').val('');
                //$('#MOBILE_NUM').val('');
                $('#SC_CODE').val('');
                $('#HID_USRID').val('');
                $('#HID_MOBILE_NUM').val('');
                enable('btn-forgot');
            }
        });
        return false;
    }

});