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

$page_title = "Customer View Details";
$page_link = "";

$parent_page_title = "";
$parent_page_link = "";

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";

$ErrorMsg = "";

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
        $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_EKYC_FLAG, ASSREQ_PAN_FLAG,ASSREQ_BRANCH_FLAG,ASSREQ_BASIC_DETAIL_FLG,ASSREQ_NOMINEE_FLG  FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
        $item_data = $sql_exe->fetch();  
        if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
            $ErrorMsg = "Unable to fetch application details";
        }  

        //e-KYC not done
        if(!isset($item_data['ASSREQ_EKYC_FLAG']) || $item_data['ASSREQ_EKYC_FLAG'] != "Y") {
            header('Location: '.APP_URL.'/ass-aadhaar-details?ref_Num="'.$ref_num.'"');
            exit();
        }

        //pan not done
        if(!isset($item_data['ASSREQ_PAN_FLAG']) || $item_data['ASSREQ_PAN_FLAG'] != "Y") {
            header('Location: '.APP_URL.'/ass-form-pan?ref_Num="'.$ref_num.'"');
            exit();
        }

        
        if(!isset($item_data['ASSREQ_BRANCH_FLAG']) || $item_data['ASSREQ_BRANCH_FLAG'] != "Y") {
            header('Location: '.APP_URL.'/ass-form-pan?ref_Num="'.$ref_num.'"');
            exit();
        }
               
    }
}

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
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
                            <form name="form-finalview" id="form-finalview" method="post" action="javascript:void(0);" class="form-material">
                                <input type="hidden" id="asnVal" name="asnVal" value="<?php echo $safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']);?>" />
                                <input type="hidden" id="pKey" value="<?php echo $safe->rsa_public_key();?>" />                               
                                <div class="row justify-content-center my-4">
                                    <div class="col-md-8 md-offset-2 col-sm-2 form-group text-center mt-2">
                                        <div class="table-responsive">
                                            <table class="table final-view">
                                                <thead>
                                                  
                                                    <tr>
                                                        <th>Branch Details	</th>                                                                        
                                                        <th>
                                                            <?php
                                                            echo '<a  href="javascript:void(0);" onclick=branchdetails("'.$safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']).'"); class="btn border btn-danger btn-sm" >Edit</a>';             
                                                          ?>
                                                        </th>
                                                    </tr>

                                                    <tr>
                                                        <th>Basic Customer Details	</th>                                                                        
                                                        <th>
                                                            <?php
                                                             echo '<a  href="javascript:void(0);" onclick=bcustdetails("'.$safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']).'"); class="btn border btn-danger btn-sm" >Edit</a>';             
                                                            ?>
                                                        </th>
                                                    </tr>

                                                    <tr>
                                                        <th>Nominee Details	</th>                                                                        
                                                        <th>
                                                            <?php
                                                             echo '<a  href="javascript:void(0);" onclick=bnomineedetails("'.$safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']).'"); class="btn border btn-danger btn-sm" >Edit</a>';             
                                                            ?>
                                                        </th>
                                                    </tr>
                                                </thead>

                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <?php 
                                        echo '<button type="button" class="btn btn-secondary px-3 py-2"  onclick=bnomineedetails("'.$safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']).'","'.$safe->str_encrypt($item_data['ASSREQ_NOMINEE_FLG'], $_SESSION['SAFE_KEY']).'");><i class="mdi mdi-arrow-left"></i>  Go Back</button>';                                  
                                        echo'<button type="submit" class="btn btn-primary px-3 py-2 ml-1" id="sbt" name="sbt" tabindex="3"  onclick=formnextbut("'.$safe->str_encrypt($assref_num, $_SESSION['SAFE_KEY']).'");>Next <span class="mdi mdi-arrow-right" aria-hidden="true"></span></button>';
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

    function branchdetails(ass_ref_num) {
        goto_url('form-branch-details?ref_Num='+ass_ref_num);
    }

    function bcustdetails(ass_ref_num) {
        goto_url('form-customer-details?ref_Num='+ass_ref_num);
    }

    function bnomineedetails(ass_ref_num) {
        goto_url('form-nominee-details?ref_Num='+ass_ref_num);
    }

    function formnextbut(ass_ref_num) {
        goto_url('ass-cust-mobverify?ref_Num='+ass_ref_num);
    }


</script>

