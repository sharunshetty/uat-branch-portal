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
$page_link = "./ac-process";

$parent_page_title = "";
$parent_page_link = "";

$ErrorMsg = "";


/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );


if(!isset($_GET['ref_Num']) || $_GET['ref_Num'] == "") {
    $ErrorMsg = "Invalid Request";
}else { 
    
  //Decode Request Data  
  $encrypt_ref_num = $main_app->strsafe_input($_GET['ref_Num']);
  $assref_num = $safe->str_decrypt($encrypt_ref_num, $_SESSION['SAFE_KEY']);

  if(!isset($assref_num) || $assref_num== false || $assref_num == "") {
    
    $ErrorMsg = "Invalid URL Request";

  } else {


     /** Steps Pending */
    $sql_exe2 = $main_app->sql_run("SELECT * FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $assref_num));
    $appData = $sql_exe2->fetch();

    if(!isset($appData['ASSREQ_REF_NUM']) || $appData['ASSREQ_REF_NUM'] == NULL || $appData['ASSREQ_REF_NUM'] == "") {
      $ErrorMsg = "Unable to fetch application details";
    }


    // if(isset($appData['ASSREQ_PAN_FLAG']) && $appData['ASSREQ_PAN_FLAG'] != NULL && $appData['ASSREQ_PAN_FLAG'] != "" && $appData['ASSREQ_PAN_FLAG'] == "Y") {
    //   //session set
    //     if(isset($_SESSION['name_flag']) && $_SESSION['name_flag'] == 'Y') {
    //       $main_app->session_remove(['name_flag']); 
    //     }

    //     $sql_exe3 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $assref_num));
    //     $kycDetails1 = $sql_exe3->fetch();

    //     if(!isset($kycDetails1) && $kycDetails1 == NULL && $kycDetails1 == "") {
    //       $ErrorMsg = "Unable to fetch application details";
    //     }

    //     if(isset($kycDetails1['DOC_DATA']) && $kycDetails1['DOC_DATA'] != "") {
    //       $kycDetails2 = json_decode(stream_get_contents($kycDetails1['DOC_DATA']), true, JSON_UNESCAPED_SLASHES);     
    //      }
  
    //     $sql_exe4 = $main_app->sql_run("SELECT * FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'PAN' ORDER BY CR_ON DESC", array('ASSREQ_REF_NUM' => $assref_num));
    //     $kycDetails3 = $sql_exe4->fetch();


    //     if(!isset($kycDetails3) && $kycDetails3 == NULL && $kycDetails3 == "") {
    //       $ErrorMsg = "Unable to fetch application details";
    //     }

     
    //     if(isset($kycDetails3['DOC_DATA']) && $kycDetails3['DOC_DATA'] != "") {
    //       $kycDetails4 = json_decode(stream_get_contents($kycDetails3['DOC_DATA']), true, JSON_UNESCAPED_SLASHES);     
    //     }
  
    //     $pan_firstname = (isset($kycDetails4['firstName']) && $kycDetails4['firstName'] != "") ? trim($kycDetails4['firstName']) : "";
    //     //combine pan name
    //     $fullname = "";
    //     $fullname .= (isset($kycDetails4['firstName']) && $kycDetails4['firstName'] != "") ? trim($kycDetails4['firstName']) : "";
    //     $fullname .= (isset($kycDetails4['midName']) && $kycDetails4['midName'] != "") ? " ". $kycDetails4['midName'] : "";
    //     $fullname .= (isset($kycDetails4['lastName']) && $kycDetails4['lastName'] != "") ? " ". $kycDetails4['lastName'] : "";
        
    //     $cust_name = explode(' ', $appData['ASSREQ_CUST_FNAME']);
    //     $custname1 = $cust_name[0];
  
    //     //convert name to uppercase
    //     $aadhaar_name = (isset($kycDetails2['name']) && $kycDetails2['name'] != "") ?  strtoupper($kycDetails2['name']) : "";
    //     $pan_full_name = strtoupper($fullname);
    //     $pan_custname1 = strtoupper($pan_firstname);
  
        
    //     if($aadhaar_name != $pan_full_name) {
    //       $ErrorMsg="Aadhaar Name and Pan Name does not match";

    //     } elseif($aadhaar_name != $appData['ASSREQ_CUST_FNAME']) {
    //       $main_app->session_set([ 'name_flag' =>  "Y"]);   
    //     }
    //     elseif($pan_custname1 != $custname1) {
    //       $main_app->session_set(['name_flag' =>  "Y"]);
    //     } 
    // }

    if(isset($ErrorMsg) && $ErrorMsg != "") {
     
      echo '<div class="row" style=" width: 100%;">';
        echo '<div class="col-md-12 form-group">';
          echo'<div class="page-card box-min-h text-center text-danger">';
            echo'<div style="font-size: 20px;" class="mt-5">'. $main_app->strsafe_output($ErrorMsg); '</div>  
        </div>'; 
      echo'</div></div>';
      exit();

    } else { 

      if(!isset($appData['ASSREQ_CUST_FLAG']) || $appData['ASSREQ_CUST_FLAG'] == NULL || $appData['ASSREQ_CUST_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-customer-detail?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_EKYC_FLAG']) || $appData['ASSREQ_EKYC_FLAG'] == NULL || $appData['ASSREQ_EKYC_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-aadhaar-details?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_AADHARFRONT_FLAG']) || $appData['ASSREQ_AADHARFRONT_FLAG'] == NULL || $appData['ASSREQ_AADHARFRONT_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-form-aadhaarview?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_AADHARBCK_FLAG']) || $appData['ASSREQ_AADHARBCK_FLAG'] == NULL || $appData['ASSREQ_AADHARBCK_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-form-aadhaarview?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_PAN_FLAG']) || $appData['ASSREQ_PAN_FLAG'] == NULL || $appData['ASSREQ_PAN_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-form-pan?ref_Num="'.$encrypt_ref_num.'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_PANIMG_FLAG']) || $appData['ASSREQ_PANIMG_FLAG'] == NULL || $appData['ASSREQ_PANIMG_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-pancard-camera?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_CAMERA_FLAG']) || $appData['ASSREQ_CAMERA_FLAG'] == NULL || $appData['ASSREQ_CAMERA_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-form-camera?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_CUSTSIGN_FLAG']) || $appData['ASSREQ_CUSTSIGN_FLAG'] == NULL || $appData['ASSREQ_CUSTSIGN_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-custsign-camera?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_BRANCH_FLAG']) || $appData['ASSREQ_BRANCH_FLAG'] == NULL || $appData['ASSREQ_BRANCH_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/form-branch-details?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_BASIC_DETAIL_FLG']) || $appData['ASSREQ_BASIC_DETAIL_FLG'] == NULL || $appData['ASSREQ_BASIC_DETAIL_FLG'] != "Y") {
        header('Location: '.APP_URL.'/form-customer-details?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_NOMINEE_FLG']) || $appData['ASSREQ_NOMINEE_FLG'] == NULL || $appData['ASSREQ_NOMINEE_FLG'] != "Y") {
        header('Location: '.APP_URL.'/form-nominee-details?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      } 
      elseif(!isset($appData['ASSREQ_CUSMOB_FLAG']) || $appData['ASSREQ_CUSMOB_FLAG'] == NULL || $appData['ASSREQ_CUSMOB_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-formfinal-detail?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_BRANCHCAMERA_FLAG']) || $appData['ASSREQ_BRANCHCAMERA_FLAG'] == NULL || $appData['ASSREQ_BRANCHCAMERA_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-formbranch-camera?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(!isset($appData['ASSREQ_CUSTLOC_FLAG']) || $appData['ASSREQ_CUSTLOC_FLAG'] == NULL || $appData['ASSREQ_CUSTLOC_FLAG'] != "Y") {
        header('Location: '.APP_URL.'/ass-formbranch-camera?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      }
      elseif(isset($appData['APP_STATUS']) && $appData['APP_STATUS'] ="AP") {
        header('Location: '.APP_URL.'/ass-branch-message?ref_Num="'.$main_app->strsafe_input($encrypt_ref_num).'"');
        exit();
      } else{
        $ErrorMsg = "Unable to fetch application details";
      }
     
    }
            
  }
}



?>

<!-- Content : Start -->

<div class="row">

  <div class="col-md-12 form-group">
    <div class="page-card box-min-h text-center text-danger">
        <div class="h5 mt-3">Error</div>
        <div class="h6"><?php echo $main_app->strsafe_output($ErrorMsg); ?></div>
    </div>
  </div>

</div>

<!-- Content : End -->

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>

