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
$page_pgm_code = "TRANSFDTL";

$page_title = "Branch User Transfer";
$page_link = "./ass-transferuser-details";

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
                            <input type="hidden" name="cmd" value="app_user_transfer" />
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
                                        <button type="button" onclick="fetch_help_modal('app_user_accounts','','transfer'); return false;" class="btn btn-primary btn-block py-1" name="sbt-mod" id="sbt-mod"><i class="mdi mdi-magnify"></i> Search Data</button>
                                    </div>
                                </div>

                            </div>


                            <div  id="transferdiv" style="display: none;">
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
                                </div>

                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="col-md-12 label_head">Transfer From Current Branch <mand>*</mand></label>
                                        <div class="col-md-12">
                                            <select name="TRANSFER_FROMBRANCH" id="TRANSFER_FROMBRANCH" class="form-control border-input reset-transfer" autocomplete="none" readonly>
                                                <option value="">-- Select --</option>
                                                <option value="1">dddddd</option>
                                                <option value="2">dddddd1</option>
                                                <?php
                                                ////$sql_exe = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_ID=:user_id and w.user_susp_rel_flag=' ' and m.mbrn_code=w.user_branch_code", array("user_id" => $_SESSION['USER_ID']));
                                                //   $sql_exe = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_susp_rel_flag=' ' and m.mbrn_code=w.user_branch_code");
                                                
                                                //   $sql_exe = $main_app->sql_run("select mbrn_name,mbrn_code from cbuat.mbrn");
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
                                        <label class="col-md-12 label_head"> To New Branch <mand>*</mand></label>
                                        <div class="col-md-12">
                                            <select name="TRANSFER_TOBRANCH" id="TRANSFER_TOBRANCH" class="form-control border-input reset-transfer" autocomplete="none">
                                                <option value="">-- Select --</option>
                                                <option value="1">dddddd</option>
                                                <option value="2">dddddd1</option>
                                                <?php
                                               // //$sql_exe = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_ID=:user_id and w.user_susp_rel_flag=' ' and m.mbrn_code=w.user_branch_code", array("user_id" => $_SESSION['USER_ID']));
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
                                        <label class="col-md-12 label_head">Transfer Date <mand>*</mand></label>
                                        <div class="col-md-12">
                                            <input type="text" name="TRANSFER_DATE" id="TRANSFER_DATE" placeholder="" maxlength="" class="form-control border-input reset-transfer" autocomplete="none">
                                        </div>
                                    </div>


                                    <div class="col-md-3 form-group">
                                        <label class="col-md-12 label_head">&nbsp;</label>
                                        <div class="col-md-10 offset-md-2">
                                            <button type="button" onclick="viewTransferhist(); return false;" class="btn btn-primary btn-block py-1" name="sbt-mod" id="sbt-mod"><i class="mdi mdi-file"></i> View Transfer History</button>
                                        </div>
                                    </div>
                                </div>                     
                            </div>

                            <!-- <div class="row">

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

        </div> -->

                            <div class="form-footer" id="footerdiv" style="display: none;">
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
    $(document).ready(function() {
        hide('transferdiv');
        hide('footerdiv');
        //Focus & Start Operation
        $("#USER_ID").focus();

    });

    //Modify
    $("#USER_ID").on('change', function() {
        show('transferdiv');
        show('footerdiv');
        on_change('app_user_transfer', 'modify', this.value);
    });


    $("#TRANSFER_DATE").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoApply: true,
        minDate: new Date(), // Min date
        locale: {
            format: 'DD-MM-YYYY', // Format
        },
        autoUpdateInput: false,
    }, function(chosen_date) {
        $('#TRANSFER_DATE').val(chosen_date.format('DD-MM-YYYY'));
    });

    $("#TRANSFER_DATE").keypress(function(event) {
        event.preventDefault();
    });

    $('input[name="TRANSFER_DATE"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
        this.form.submit();
    });

    function viewTransferhist() {
        var USER_ID = $('#USER_ID').val();
        if (USER_ID != '') {
            fetch_help_modal('app-branchtransf-hist', 'lg', 'modify', '', '', USER_ID);
        }
    }
</script>

</body>

</html>