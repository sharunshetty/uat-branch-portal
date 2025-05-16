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
    $page_title = "";
    $page_link = "";

    $parent_page_title = "";
    $parent_page_link = "";

    /** Table Settings */
    $page_table_name = "ASSREQ_MASTER";
    $primary_key = "";

    /** Page Header */
    require( dirname(__FILE__) . '/../theme/app-header.php' );

    $errorMsg = "";

    if(!isset($_GET['ref_Num']) || $_GET['ref_Num'] == "") {
        $errorMsg = "Invalid Request";
    }else {      
        //Decode Request Data  
        $assref_num = $safe->str_decrypt($_GET['ref_Num'], $_SESSION['SAFE_KEY']);
        if(!isset($assref_num) || $assref_num== false || $assref_num == "") {
            $errorMsg = "Invalid URL Request";
        } else {
            $assref_num = $main_app->strsafe_output($assref_num);
            $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
            $item_data = $sql_exe->fetch();  
            if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
                $errorMsg = "E01 : Invalid request.";
            }
        }
    }
?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php if(isset($errorMsg) && $errorMsg != "") { ?>
                <div class="col-md-12 text-danger text-center mt-5 pt-5 h5"><?php echo $main_app->strsafe_output($errorMsg); ?></div>
            <?php } else { ?>

                <!-- Content : Start -->

                <div class="col-md-12 form-group text-center">
                    <div class="card card-outline card-brand">
	                    <div class="card-body min-high2">
                            <div class="row justify-content-center mt-4">

                                <div class="col-md-12 form-group h5 txt-c1 text-center justify-content-center d-flex align-items-center" style="min-height: 50vh;color: #EE2225;">          
                                    <span class='bank-message'>Thanking You for Applying for creating account in Capital Small Finance Bank.<br/> Your Account Application Reference No  <span class='text-primary'><?php echo $assref_num ?></span> is under Review. </span>
                                </div>
          
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-secondary px-3 py-2"  onclick="goto_url('main-assistant');">Home</button>                           
                                </div>
                            
                            </div>
                        </div>  
                    </div>
                </div>

                <!-- Content : End -->
            <?php }   ?>    
        </div> 
    </div>           
</section>

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>


