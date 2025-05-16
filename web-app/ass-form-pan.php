<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @developer   : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

/** Check User Session */
require_once(dirname(__FILE__) . '/check-login.php');

/** Current Page */
$page_pgm_code = "";

$page_title = "PAN Verification";
$page_link = "";

$parent_page_title = "";
$parent_page_link = "";

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";

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
        
        $assref_num=$main_app->strsafe_output($assref_num);
        $enc_assref_num = $safe->str_encrypt( $assref_num, $_SESSION['SAFE_KEY']);
        
        $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_EKYC_FLAG, ASSREQ_PAN_FLAG  FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
        $item_data = $sql_exe->fetch();  
        if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
            $errorMsg = "Unable to fetch application details";
        }  

        //e-KYC not done
        if(!isset($item_data['ASSREQ_EKYC_FLAG']) || $item_data['ASSREQ_EKYC_FLAG'] != "Y") {
             header('Location: '.APP_URL.'/ass-aadhaar-details?ref_Num="'.$main_app->strsafe_input($enc_assref_num).'"');
            exit();        
        }         
    }
}

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
?>

<?php 
    if(isset($errorMsg) && $errorMsg == "") {
        echo "<div class='abp-heading text-muted'>Account Ref No: <span class='text-danger'>$assref_num</span></div>";
   }
?>

<!-- Content : Start -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php if(isset($errorMsg) && $errorMsg != "") { ?>
                <div class="col-md-12 text-danger text-center mt-5 pt-5 h5"><?php echo $main_app->strsafe_output($ErrorMsg); ?></div>
            <?php } else { ?>
                <div class="col-md-12 form-group">
                    <div class="card card-outline card-brand">
                        <div class="card-body min-high2">
                            <form name="pan-verify" id="pan-verify" method="post" action="javascript:void(0);" class="form-material">
                                <input type="hidden" id="asnVal" name="asnVal" value="<?php echo $main_app->strsafe_input($enc_assref_num); ?>" />
                                <input type="hidden" id="pKey" value="<?php echo $safe->rsa_public_key();?>" />                               
                                <div class="row justify-content-center my-4">
                                    <div class="col-md-12 form-group text-center mt-2">
                                        <div class="h5 txt-c1">Enter your PAN Card Number</div>
                                        <div class="text-muted small">PAN issued by the Income Tax Department to Indian taxpayers</div>
                                    </div>

                                    <div class="col-md-5 col-lg-4 form-group text-center">
                                        <div class="input_div">
                                            <input type="text" id="panNum" name="panNum" index="1" placeholder="X X X X X X X X X X" maxlength="10" class="form-control border-input js-noSpace js-toUpper text-center" autocomplete="off">
                                        </div>
                                    </div>
                                </div>




                                <!-- <div class="row justify-content-center my-4">   
                                    <div class="col-md-12 form-group text-center mt-2">
                                        <div class="h5 txt-c1">Enter your PAN Details</div>
                                        <div class="text-muted small">PAN issued by the Income Tax Department to Indian taxpayers</div>
                                    </div>     
                                    <div class="col-md-4 form-group">
                                        <label class="col-md-12 label_head">PAN Card Number </label>
                                        <div class="col-md-12">
                                            <input type="text" id="panNum" name="panNum" index="1" placeholder="X X X X X X X X X X" maxlength="10" class="form-control border-input js-noSpace js-toUpper text-center" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label class="col-md-12 label_head">Name </label>
                                        <div class="col-md-12">
                                            <input name="FULL_NAME" id="FULL_NAME" maxlength="" class="form-control border-input" autocomplete="off">
                                        </div>
                                    </div>
                                </div> -->

                                <!--<div class="row justify-content-center my-4">
                                    <div class="col-md-4 form-group">
                                        <label class="col-md-12 label_head">Father's Name </label>
                                        <div class="col-md-12">
                                            <input name="FATHER_NAME" id="FATHER_NAME" maxlength="" class="form-control border-input" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label class="col-md-12 label_head">Date of birth </label>
                                        <div class="col-md-12">
                                            <input type="text" name="DATE_OF_BIRTH" id="DATE_OF_BIRTH" class="form-control border-input date" autocomplete="none">
                                            </div>
                                    </div>

                                    <div class="col-md-12 form-group text-center">
                                        <div class="input_div small">
                                            <label for="PanAgree">
                                                <div class="text-muted small">Foreign Account Tax Compliance Act (FATCA)</div>
                                                <input type="checkbox" name="PanAgree" id="PanAgree" value="1" class="form-radio checkbox"> 
                                                I am an INDIAN citizen and a tax resident of India and of no other country.
                                            </label>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="mt-4 text-center">
                                        <?php echo '<button type="button" class="btn btn-secondary px-3 py-2"  onclick=gobackbut("'.$main_app->strsafe_input($enc_assref_num).'");><i class="mdi mdi-arrow-left"></i>  Go Back</button>';                                  
                                            echo'<button type="submit" class="btn btn-primary px-3 py-2 ml-1" id="sbt" name="sbt" tabindex="3">Next <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>';
                                        ?>     
                                   </div>

                                  
                                </div>-->


                                <div class="col-md-12 form-group text-center">
                                    <div class="input_div small">
                                        <label for="PanAgree">
                                            <div class="text-muted small">Foreign Account Tax Compliance Act (FATCA)</div>
                                            <input type="checkbox" name="PanAgree" id="PanAgree" value="1" class="form-radio checkbox"> 
                                            I am an INDIAN citizen and a tax resident of India and of no other country.
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <?php echo '<button type="button" class="btn btn-secondary px-3 py-2"  onclick=gobackbut("'.$main_app->strsafe_input($enc_assref_num).'");><i class="mdi mdi-arrow-left"></i>  Go Back</button>';                                  
                                        echo'<button type="submit" class="btn btn-primary px-3 py-2 ml-1" id="sbt" name="sbt" tabindex="3">Next <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>';
                                    ?>     
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php }  ?>
        </div>
    </div>
</section>


<!-- Content : End -->

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>

<script type="text/javascript">

    // Form PAN
    $("#pan-verify").submit(function(e) {
        e.preventDefault();
    }).validate({
        rules: {
            panNum: { required: true, minlength: 10 },
            PanAgree: { required: true }, 
            //CHK_DL: { required: true }, 
        },
        messages: {
            panNum: {
                required: "Enter your PAN",
                minlength: "Please enter valid PAN"
            },
            PanAgree: {
                required: "Please click on checkbox"
            }, 
            /*CHK_DL: {
                required: "Please select Do you have driving licence?"
            },*/
        },
        errorPlacement: function (error, element) {
            if(element.closest('.input_div').length) {
                error.insertAfter(element.closest('.input_div'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: PanValidate
    });

    function PanValidate() {
        var panNum = $('#panNum').val();
        var PanAgree = $('#PanAgree').val();
        var asnVal = $('#asnVal').val();
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('#pKey').val());
        var safeData = {
        cmd : "ass_form_panvalidate",
        token : window.req_id,  
        asnVal : asnVal,
        PanAgree : PanAgree,
        panNum : encrypt.encrypt(panNum),
        }
        disable('sbt');
        loader_start();
        post_safe_data(safeData);
    }



    $("#DATE_OF_BIRTH").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    //timePicker: true,
    autoApply: true,
    //startDate: moment().format('DD-MM-YYYY hh:mm A'), // Current
    //minDate: moment().format('DD-MM-YYYY'), // Min date
    //maxDate: moment().add(30, 'days').format('DD-MM-YYYY'), // Max date
    locale: {
      format: 'DD-MM-YYYY', // Format
    },
    autoUpdateInput: false,
  }, function(chosen_date) {
    $('#DATE_OF_BIRTH').val(chosen_date.format('DD-MM-YYYY'));
  });

  $('input[name="DATE_OF_BIRTH"]').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('DD-MM-YYYY'));
    this.form.submit();
  });

//     // Form PAN
//     $("#pan-verify").submit(function(e) {
//       e.preventDefault();
//     }).validate({
//         rules: {
//           panNum: { required: true, minlength: 10 },
//           FULL_NAME: { required: true},
//           FATHER_NAME: { required: true},
//           DATE_OF_BIRTH: { required: true},
//           PanAgree: { required: true }, 
//           //CHK_DL: { required: true }, 
//         },
//         messages: {
//           panNum: {
//               required: "Enter your PAN",
//               minlength: "Please enter valid PAN"
//           },
//           FULL_NAME: {
//             required: "Enter your Full name on PAN Card",
//           },
//           FATHER_NAME: {
//             required: "Enter your Father's name",
//           },
//           DATE_OF_BIRTH: {
//             required: "Enter your Date of birth",
//           },
//           PanAgree: {
//               required: "Please click on checkbox"
//           }, 
//         },
//         errorPlacement: function (error, element) {
//             if(element.closest('.input_div').length) {
//                 error.insertAfter(element.closest('.input_div'));
//             } else {
//                 error.insertAfter(element);
//             }
//         },
//         submitHandler: PanValidate
//     });

//     function PanValidate() {
//       var panNum = $('#panNum').val();
//       var PanAgree = $('#PanAgree').val();
//       var asnVal = $('#asnVal').val();
//       var FULL_NAME = $('#FULL_NAME').val();
//       var FATHER_NAME = $('#FATHER_NAME').val();
//       var DATE_OF_BIRTH = $('#DATE_OF_BIRTH').val();
//       var encrypt = new JSEncrypt();
//       encrypt.setPublicKey($('#pKey').val());
//       var safeData = { 
//         cmd : "ass_form_panvalidate",
//         token : window.req_id,
//         asnVal : asnVal,
//         PanAgree : PanAgree,
//         panNum : encrypt.encrypt(panNum),
//         fullname : FULL_NAME,
//         fathername : FATHER_NAME,
//         dateofbirth : DATE_OF_BIRTH,
//       }
//       disable('sbt');
//       loader_start();
//       post_safe_data(safeData);
//     }

    function gobackbut(ass_ref_num) {
        goto_url('ass-aadhaarcard-camera?ref_Num='+ass_ref_num);
    }

</script>

