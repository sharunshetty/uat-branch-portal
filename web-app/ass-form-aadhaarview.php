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

$page_title = "Aadhaar Card Data Verification";
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
    $ref_num = $_GET['ref_Num'];
    $assref_num = $safe->str_decrypt($ref_num, $_SESSION['SAFE_KEY']);
    if(!isset($assref_num) || $assref_num== false || $assref_num == "") {
        $errorMsg = "Invalid URL Request";
    } else {
        $assref_num = $main_app->strsafe_output($assref_num);
        $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_MOBILE_NUM, ASSREQ_EKYC_FLAG  FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
        $item_data = $sql_exe->fetch();  
        if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
            $errorMsg = "Unable to fetch application details";
        }  

        $sql_exe2 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM']));
        $kycDetails = $sql_exe2->fetch();

        if(!isset($kycDetails['ASSREQ_REF_NUM']) || !isset($item_data['ASSREQ_EKYC_FLAG']) || $item_data['ASSREQ_EKYC_FLAG'] != "Y") {
            header('Location: '.APP_URL.'/ass-aadhaar-details?ref_Num="'.$ref_num.'"');
            exit();
        }
        
        if(isset($kycDetails['DOC_DATA']) && $kycDetails['DOC_DATA'] != "") {
            //$kycDetails = json_decode(stream_get_contents($kycDetails['DOC_DATA']), true, JSON_UNESCAPED_SLASHES);
      
            $kycDetails = json_decode(stream_get_contents($kycDetails['DOC_DATA']), true, JSON_UNESCAPED_SLASHES);
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
                            <div class="row justify-content-center mt-4">
                                <input type="hidden" id="asnVal" value="<?php echo $safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']);?>"/>
                                <div class="col-md-12 form-group h5 txt-c1 text-center">Your Aadhaar Details</div>
                                <div class="col-md-3 form-group text-center text-lg-right">
                                    <?php if(isset($kycDetails['image']) && $kycDetails['image'] != "") {
                                        $img_data = (gettype($kycDetails['image']) == "resource") ? stream_get_contents($kycDetails['image']) : $kycDetails['image'];
                                    } ?>
                                    <img height="160" width="130" class="mr-lg-4 mb-3" src="data:image/jpeg;charset=utf-8;base64,<?php echo $img_data; ?>" />
                                </div>

                                <div class="col-md-6 form-group">
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <label class="col-md-12 label_head">Name as per Aadhaar</label>
                                            <div class="col-md-12"><?php echo isset($kycDetails['name']) ? $main_app->strsafe_output($kycDetails['name']) : ""; ?></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    
                                        <div class="col-md-6 form-group">
                                            <label class="col-md-12 label_head">Date of Birth</label>
                                            <div class="col-md-12"><?php echo isset($kycDetails['dob']) ? date('d-m-Y ',strtotime($main_app->strsafe_output($kycDetails['dob']))) : ""; ?></div>
                                        </div>
                                 
                                        <div class="col-md-6 form-group">
                                            <label class="col-md-12 label_head">Gender</label>
                                            <?php
                                                $gender = ($kycDetails['gender'] == "M") ? "Male" : (($kycDetails['gender'] == "F") ? "Female" : (($kycDetails['gender'] == "T") ? "TransGender" : ""));
                                            ?>
                                            <div class="col-md-12"><?php echo isset( $gender) ? $main_app->strsafe_output( $gender) : ""; ?></div>
                                        </div>

                                        <div class="col-md-12 form-group">
                                            <label class="col-md-12 label_head">Address</label>
                                            <div class="col-md-12">
                                                <?php
                                                    $combined_address = (isset($kycDetails['combinedAddress']) && $kycDetails['combinedAddress'] != NULL) ? $main_app->strsafe_output($kycDetails['combinedAddress']) : "";
                                                    echo $combined_address."<br/>";

                                                ?>
                                            </div>   
                                        </div>  
                                    </div>  
                                
                                    <div class="col-md-12 form-group small">
                                        <label for="DetailAgree" class="ml-1 label_head"> </label>
                                        <input type="checkbox" name="DetailAgree" id="DetailAgree"  class="form-radio checkbox"> I confirm that the above information is true.<mand>*</mand>    
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">                    
                                <div class="col-md-12 mt-3 text-center">
                                    <?php 
                                        echo '<button type="button" class="btn btn-secondary px-3 py-2"  onclick=gobackbut("'.$safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']).'");><i class="mdi mdi-arrow-left"></i>  Go Back</button>';                              
                                       echo '<button  class="btn btn-primary px-3 py-2 ml-1"  id="sbt2" name="sbt2" onclick="nextScreen();">Next  <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button> ';                               
                                    ?>
                                </div>
                              

                             </div>           
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

   function nextScreen() {
        if( $('#DetailAgree').is(':checked') ){
            var asnVal = $('#asnVal').val();
            loader_start();
            goto_url('ass-aadhaarcard-camera?ref_Num='+asnVal);
        }
        else{
            swal.fire('','Please click on checkbox');
        }
   }

   function gobackbut(ass_ref_num) {
        goto_url('ass-aadhaar-details?ref_Num='+ass_ref_num);
    }


</script>
