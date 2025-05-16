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

$page_title = "User Block/Unblock/Resign";
$page_link = "./app-user-block";

$parent_page_title = "Go Back";
$parent_page_link = "";

/** Page Header */
require(dirname(__FILE__) . '/../theme/app-header.php');
?>

<!-- Content : Start -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card card-outline card-brand">
                    <div class="card-body min-high2">
                        <form id="app-form" name="app-form" method="post" action="javascript:void(null);" class="form-material">
                            <input type="hidden" name="cmd" value="app_user_block" />
                            <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>" />


                            <div class="row">

                                <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">Employee ID <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <input type="text" name="USER_ID" id="USER_ID" placeholder="" maxlength="12" class="form-control border-input" autocomplete="none" readonly>
                                    </div>
                                </div>


                                <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">&nbsp;</label>
                                    <div class="col-md-12">
                                        <button type="button" onclick="fetch_help_modal('app_user_accounts','','block'); return false;" class="btn btn-primary btn-block py-1" name="sbt-mod" id="sbt-mod"><i class="mdi mdi-magnify"></i> Search Data</button>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                               
                                <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">Full Name <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <input type="text" name="USER_FULLNAME" id="USER_FULLNAME" placeholder="" maxlength="120" class="form-control border-input js-toUpper reset-form" autocomplete="none" readonly>
                                    </div>
                                </div>


                                <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">User Mobile <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <input type="tel" name="USER_MOBILE" id="USER_MOBILE" placeholder="" maxlength="10" class="form-control border-input js-isNumeric reset-form" autocomplete="none" readonly>
                                    </div>
                                </div>

                                

                                <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">Status <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <select name="STATUS" id="STATUS" class="form-control border-input" autocomplete="none">
                                            <option value="">-- Select --</option>
                                            <option value="B">Blocked</option>
                                            <option value="A">Activated</option>
                                            <option value="R">Resigned</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3 form-group" id="resign" style="display: none;">
                                    <label class="col-md-12 label_head">Resign Date <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control border-input reset-form reset-transfer date" autocomplete="none" id="RESIGN_DATE" name="RESIGN_DATE" autocomplete="off">

                                        <!-- <input type="date" name="RESIGN_DATE" id="RESIGN_DATE" placeholder=""  maxlength="" class="form-control border-input reset-form reset-transfer" autocomplete="none"> -->
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
require(dirname(__FILE__) . '/../theme/app-footer.php');
?>

<script type="text/javascript">
    $("#STATUS").on('change', function() {
        var status = $("#STATUS").val();
        if (status == "R") {
            show('resign');
        } else {
            hide('resign');
        }
    });

    //Modify
    $("#USER_ID").on('change', function() {
        on_change('app_user_block', 'modify', this.value);
    });

    $("#RESIGN_DATE").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoApply: true,
        // maxDate: moment().add(0, 'days').format('DD-MM-YYYY'), // Max date
        minDate: new Date(), // Min date
        locale: {
            format: 'DD-MM-YYYY', // Format
        },
        autoUpdateInput: false,
    }, function(chosen_date) {
        $('#RESIGN_DATE').val(chosen_date.format('DD-MM-YYYY'));
    });

    $("#RESIGN_DATE").keypress(function(event) {
        event.preventDefault();
    });

    $('input[name="RESIGN_DATE"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
        // this.form.submit();
    });
</script>

</body>

</html>