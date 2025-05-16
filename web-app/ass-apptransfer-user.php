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
$page_pgm_code = "APPLTRANSFDTL";

$page_title = "Customer Account Transfer";
$page_link = "./ass-apptransfer-user";

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
                            <input type="hidden" name="cmd" value="app_appltransfer_user" />
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

                            <div class="row">

                                <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">Full Name <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <input type="text" name="USER_FULLNAME" id="USER_FULLNAME" placeholder="" maxlength="120" class="form-control border-input js-toUpper reset-form" autocomplete="none" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">Branch <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <select name="USER_BRANCH" id="USER_BRANCH" class="form-control border-input reset-form" autocomplete="none" readonly>
                                            <option value="">-- Select --</option>
                                            <option value="1">dddddd</option>
                                            <option value="2">dddddd1</option>
                                            <?php
                                            //$sql_exe = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_ID=:user_id and w.user_susp_rel_flag=' ' and m.mbrn_code=w.user_branch_code", array("user_id" => $_SESSION['USER_ID']));
                                            //   $sql_exe = $main_app->sql_run("select w.user_id,w.user_name,w.user_branch_code,m.mbrn_name from cbuat.users w,cbuat.mbrn m where w.user_susp_rel_flag=' ' and m.mbrn_code=w.user_branch_code");
                                            //  while ($row = $sql_exe->fetch()) {
                                            //     echo "<option value=".$row['USER_BRANCH_CODE'].">". $row['USER_BRANCH_CODE'] . '-'.$row['MBRN_NAME']. "</option>";  

                                            //   }

                                            	//   $sql_exe = $main_app->sql_run("select mbrn_name,mbrn_code from cbuat.mbrn");
                                                //  while ($row = $sql_exe->fetch()) {
                                                //     echo "<option value=".$row['MBRN_CODE'].">". $row['MBRN_CODE'] . '-'.$row['MBRN_NAME']. "</option>";  

                                                //   }
                                            ?>
                                            
                                        </select>
                                    </div>
                                </div>

                                <!-- <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">Role Code <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <select name="USER_ROLE_CODE" id="USER_ROLE_CODE" class="form-control border-input reset-form" autocomplete="off" readonly>
                                            <option value="">-- Select --</option>
                                            <?php
                                                $sql_exe = $main_app->sql_run("SELECT ROLE_CODE,ROLE_DESC FROM ASSREQ_USER_ROLES where ROLE_STATUS = '1'");
                                                while ($row = $sql_exe->fetch() ) {
                                                    echo '<option value="'.$row['ROLE_CODE'].'">'. $row['ROLE_DESC'] .' ['. $row['ROLE_CODE'] . ']</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div> -->

                                <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">Transfer User To  <mand>*</mand></label>
                                    <div class="col-md-12">
                                        <select name="TRANSFER_USER" id="TRANSFER_USER" class="form-control border-input reset-form" autocomplete="off">     
                                        </select>
                                    </div>
                                </div>

                                <!-- <div class="col-md-3 form-group">
                                    <label class="col-md-12 label_head">Transfer Role Code<mand>*</mand></label>
                                    <div class="col-md-12">
                                        <input type="text" name="TRANSFER_ROLECODE" id="TRANSFER_ROLECODE" placeholder="" maxlength="120" class="form-control border-input js-toUpper reset-form" autocomplete="none" readonly>
                                    </div>
                                </div> -->

                            </div>


                            <!-- <div class="col-md-12 col-lg-12 ml-1 mt-2 text-primary"> Application  Details</div> -->

                            <div class="col-md-12 mt-2">
                                <div class="table-responsive">
                                    <table class="app-data-table table table-bordered table-striped table-sm" width="100%" id="resp-table" style="display: none;">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Account Reference No</th>
                                                <th class="text-left">Customer Details</th>
                                                <th class="text-left">Status</th>
                                                <th class="text-left">Created On</th>
                                                <th class="text-center"><input type="checkbox" class="form-radio checkbox"  id="SELECT_ALL"  onclick="select_userall()">Select</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody id="dyn_data">

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-footer">
                                <div class="col-md-12 text-right">
                                    <button type="button" tabIndex="-1" class="btn btn-outline-secondary mr-2" onclick="$('#app-form').trigger('reset');hide('resp-table');$('.reset-form').empty();$('#dyn_data').children('tr').remove();">Reset</button>
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
    
    $(document).ready(function(){
       hide('resp-table'); 
    });

    //Modify
    $("#USER_ID").on('change', function() {
        on_change('app_appltransfer_user', 'modify', this.value);
    });

    function selec  t_user(item) {
        if($('#SELECTS_'+item).prop('checked') == true ) {
            $('#SELECTS_'+item).val('Yes');
           // $('#CNF_SELECT'+item).val('Yes');
        }
        else {
            $('#SELECTS_'+item).val('No');
          //  $('#CNF_SELECT'+item).val('');
        }

    }

    function select_userall() {
        var totcount = $('#HIDDEN_TOTCOUNT').val();
        if($('#SELECT_ALL').prop('checked') == true ) {
            for(var item=1;item<=totcount;item++){
                $('#SELECTS_'+item).val('Yes');
                $('#SELECTS_'+item).prop('checked',true); 
               // $('#CNF_SELECT'+item).val('Yes');
            }
        }else{
            for(var item=1;item<=totcount;item++){
                $('#SELECTS_'+item).val('No');
                $('#SELECTS_'+item).prop('checked',false); 
              //  $('#CNF_SELECT'+item).val('');
            }
        }
    }

    
</script>

</body>

</html>