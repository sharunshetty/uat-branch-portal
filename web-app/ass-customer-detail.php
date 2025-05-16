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
    $page_pgm_code = "CUSTDET";
    $page_title = "Customer Basic Details";
    $page_link = "";

    $parent_page_title = "Home";
    $parent_page_link = "./";

    /** Table Settings */
    $page_table_name = "ASSREQ_MASTER";
    $primary_key = "ASSREQ_REF_NUM";

    /** Page Header */
    require( dirname(__FILE__) . '/../theme/app-header.php' );

    /** Get Data */

    $errorMsg = "";

    if(!isset($_GET['ref_Num']) || $_GET['ref_Num'] == "") {
        $errorMsg = "Invalid Request";
    }else { 
        
        //Decode Request Data  
        $encrypt_ref_num = $main_app->strsafe_input($_GET['ref_Num']);
        $assref_num = $safe->str_decrypt($encrypt_ref_num, $_SESSION['SAFE_KEY']);
        if(!isset($assref_num) || $assref_num== false || $assref_num == "") {
            $errorMsg = "Invalid URL Request";
        } else {

            $assref_num = $main_app->strsafe_output($assref_num);
            $enc_assref_num = $safe->str_encrypt( $assref_num, $_SESSION['SAFE_KEY']);
                       
            $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
            $item_data = $sql_exe->fetch();  
            if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL) {
                $errorMsg = "E01 : Invalid request.";
            }

        }
    }

?>

<?php 
    if(isset($errorMsg) && $errorMsg == "") {
        echo "<div class='abp-heading text-muted'>Account Ref No: <span class='text-danger'>$assref_num</span></div>";
    }
?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php if(isset($errorMsg) && $errorMsg != "") { ?>
                <div class="col-md-12 text-danger text-center mt-5 pt-5 h5"><?php echo $errorMsg; ?></div>
            <?php } else { ?>
    
                <div class="col-md-12 col-lg-12 form-group" id="customer-details" >
                    <div class="card card-outline card-brand">
                        <div class="card-body min-high2">
                            <form name="myform" id="myform" method="post">
                                <input type="hidden" name="ASSREF_NUM" id="ASSREF_NUM" value="<?php echo $main_app->strsafe_input($enc_assref_num); ?>"/>
                                <input type="hidden" name="cmd" value="ass_customer_details"/>
                                <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>
                                <input type="hidden" id="req_key" value="<?php echo $safe->rsa_public_key();?>" />
          
                                <div class="modal-body">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8 col-lg-6">  
                                            <div class="row m-0">                            
                                                <div class="col-md-5 form-group">
                                                    <label class="col-md-12 label_head">Customer Title <mand>*</mand></label>
                                                    <div class="col-md-12">
                                                        <select name="CUST_TITLE" id="CUST_TITLE" class="form-control border-input js-noSpace" autocomplete="off" readonly>
                                                            <option value="">-- Select --</option>
                                                            <option value="1">MR</option>
                                                            <option value="2">MRS</option>
                                                            <option value="3">MISS</option>
                                                            <option value="7">Mx</option>
                                                        </select>
                                                    </div>
                                                </div>  
                                                                    
                                                <div class="col-md-7 form-group">
                                                    <label class="col-md-12 label_head">Full Name <mand>*</mand></label>
                                                    <div class="col-md-12">
                                                        <input type="text" name="CUST_FULLNAME" id="CUST_FULLNAME" placeholder="Full Name" class="form-control border-input js-alphaNumericspace js-toUpper" autocomplete="off">
                                                    </div>
                                                </div>  
                                            </div>

                                        </div>                           
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-8 col-lg-6">                             
                                            <div class="col-md-12 form-group">
                                                <label class="col-md-12 label_head">Gender <mand>*</mand></label>
                                                <div class="col-md-12">
                                                    <select name="CUST_GENDER" id="CUST_GENDER" class="form-control border-input js-noSpace" autocomplete="off" readonly>
                                                        <option value="">-- Select --</option>
                                                        <option value="M">Male</option>
                                                        <option value="F">Female</option>
                                                        <option value="T">Transgender</option>
                                                    </select>
                                                </div>
                                            </div>                 
                                        </div>                           
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-8 col-lg-6">                               
                                            <div class="col-md-12 form-group">
                                                <label class="col-md-12 label_head">Mobile Number <mand>*</mand> <small class="small">[Verified]</small></label>
                                                <div class="col-md-12">
                                                    <input type="text" name="CUST_MOBILE" id="CUST_MOBILE" placeholder="10 Digit Number" maxlength="10" class="form-control border-input js-isNumeric" autocomplete="off" readonly>
                                                </div>		
                                            </div>                                   
                                        </div>                             
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-8 col-lg-6">                               
                                            <div class="col-md-12 form-group">
                                                <label class="col-md-12 label_head">Email ID <mand>*</mand> <small class="small">[Verified]</small></label>
                                                <div class="col-md-12">
                                                    <input type="text" name="CUST_EMAIL" id="CUST_EMAIL"  placeholder="Email ID" class="form-control border-input bg-ip-box js-noCopy js-character" autocomplete="off" readonly>
                                                </div>	
                                            </div>                                   
                                        </div>                             
                                    </div>
                                
                                    <div class="row justify-content-center">
                                        <div class="col-md-8 col-lg-6">                               
                                            <div class="col-md-12 form-group">
                                                <label class="col-md-12 label_head">Referral Code</label>
                                                <div class="col-md-12">
                                                <input type="text" name="REGREFERRAL" id="REGREFERRAL" placeholder="Referral Code" maxlength="120" class="form-control border-input js-noSpace" autocomplete="off">
                                                </div>	
                                            </div>                                   
                                        </div>                             
                                    </div>

                                    <div class="mt-4 text-center">
                                        <button type="button" class="btn btn-secondary px-3 py-2" onclick="gobackbut();"> Cancel</button>
                                        <button type="button" class="btn btn-primary px-3 py-2" name="sbt" id="sbt" onclick="upd_custdata(); return false;">Next <i class="mdi mdi-arrow-right"></i></button>
                                    </div>
                                 </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php }  ?>
        </div>
    </div>
</section>

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>

<script type="text/javascript">	

    function gobackbut() {
        goto_url('main-assistant');
    }

    function upd_custdata(){
        var key = $('#req_key').val();
        if(key && key != "") {    
            send_form('myform','sbt');
        } else {
            alert('Invalid request key');
        }
    }
    
    $(document).ready(function(){

        <?php
  
            if(isset($item_data['ASSREQ_CUST_TITLE']) && $item_data['ASSREQ_CUST_TITLE'] != "") {
                echo "$('#CUST_TITLE').val(deStr('".$main_app->strsafe_modal($item_data['ASSREQ_CUST_TITLE'])."'));"; 
            } 

           if(isset($item_data['ASSREQ_CUST_FNAME']) && $item_data['ASSREQ_CUST_FNAME'] != "") {
                echo "$('#CUST_FULLNAME').val(deStr('".$main_app->strsafe_modal($item_data['ASSREQ_CUST_FNAME'])."'));"; 
            } 

            if(isset($item_data['ASSREQ_CUST_GENDER']) && $item_data['ASSREQ_CUST_GENDER'] != "") {
                echo "$('#CUST_GENDER').val(deStr('".$main_app->strsafe_modal($item_data['ASSREQ_CUST_GENDER'])."'));"; 
            } 
        
            if(isset($item_data['ASSREQ_MOBILE_NUM']) && $item_data['ASSREQ_MOBILE_NUM'] != "") {
                echo "$('#CUST_MOBILE').val(deStr('".$main_app->strsafe_modal($item_data['ASSREQ_MOBILE_NUM'])."'));"; 
            } 

            if(isset($item_data['ASSREQ_EMAIL']) && $item_data['ASSREQ_EMAIL'] != "") {
                echo "$('#CUST_EMAIL').val(deStr('".$main_app->strsafe_modal($item_data['ASSREQ_EMAIL'])."'));"; 
            } 

            if(isset($item_data['REFERRAL_CODE']) && $item_data['REFERRAL_CODE'] != "") {
                echo "$('#REGREFERRAL').val(deStr('".$main_app->strsafe_modal($item_data['REFERRAL_CODE'])."'));"; 
            }
 
        ?>

    });


</script>

