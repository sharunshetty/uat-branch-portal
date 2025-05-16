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

$errorMsg = "";

if(!isset($_GET['ref_Num']) || $_GET['ref_Num'] == "") {
    $errorMsg = "Invalid Request";
}else { 
    
    //Decode Request Data  
    //$ref_num = $_GET['ref_Num'];
    $encrypt_ref_num = $main_app->strsafe_input($_GET['ref_Num']);
    $assref_num = $safe->str_decrypt($encrypt_ref_num, $_SESSION['SAFE_KEY']);
    if(!isset($assref_num) || $assref_num== false || $assref_num == "") {
        $errorMsg = "Invalid URL Request";
    } else {
        $assref_num = $main_app->strsafe_output($assref_num);
        $enc_assref_num = $safe->str_encrypt( $assref_num, $_SESSION['SAFE_KEY']);

        $sql_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_EKYC_FLAG, ASSREQ_PAN_FLAG,ASSREQ_BRANCH_FLAG,ASSREQ_BASIC_DETAIL_FLG,ASSREQ_NOMINEE_FLG  FROM {$page_table_name} WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
        $item_data = $sql_exe->fetch();  
        if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
            $errorMsg = "Unable to fetch application details";
        }  

        //e-KYC not done
        if(!isset($item_data['ASSREQ_EKYC_FLAG']) || $item_data['ASSREQ_EKYC_FLAG'] != "Y") {
            header('Location: '.APP_URL.'/ass-aadhaar-details?ref_Num="'.$main_app->strsafe_input($enc_assref_num).'"');
            exit();
        }

        //pan not done
        if(!isset($item_data['ASSREQ_PAN_FLAG']) || $item_data['ASSREQ_PAN_FLAG'] != "Y") {
            header('Location: '.APP_URL.'/ass-form-pan?ref_Num="'.$main_app->strsafe_input($enc_assref_num).'"');
            exit();
        }

        
        if(!isset($item_data['ASSREQ_BRANCH_FLAG']) || $item_data['ASSREQ_BRANCH_FLAG'] != "Y") {
            header('Location: '.APP_URL.'/ass-form-pan?ref_Num="'.$main_app->strsafe_input($enc_assref_num).'"');
            exit();
        }

        $sql_exe1 = $main_app->sql_run("SELECT *  FROM ASSREQ_ACCOUNTDATA WHERE $primary_key = :ASSREQ_REF_NUM", array('ASSREQ_REF_NUM' => $assref_num));
        $item_data1 = $sql_exe1->fetch();  
        if(!isset($item_data1['ASSREQ_REF_NUM']) || $item_data1['ASSREQ_REF_NUM']== false || $item_data1['ASSREQ_REF_NUM'] == NULL || $item_data1['ASSREQ_REF_NUM'] == "") {
            $errorMsg = "Unable to fetch application details";                                                        
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
                            <form name="form-finalview" id="form-finalview" method="post" action="javascript:void(0);" class="form-material">
                                <input type="hidden" id="asnVal" name="asnVal" value="<?php echo $main_app->strsafe_input($enc_assref_num);?>" />
                                <input type="hidden" id="pKey" value="<?php echo $safe->rsa_public_key();?>" />                               
                                <div class="row justify-content-center my-4">
                                    <div class="col-md-8 md-offset-2 col-sm-2 form-group text-center mt-2">

                                        <div id="accordion" class="accordion">
                                            <div class="card mb-0">
                                                <div class="card-header" data-toggle="collapse" href="#collapseOne">
                                                    <a class="card-title">
                                                        <b>Branch Details</b>
                                                    </a>
                                                </div>
                                                <div id="collapseOne" class="card-body collapse show" data-parent="#accordion" >
                                                    <div class="table-responsive">
                                                        <table class="table final-view">
                                                            <thead>
                                                                <tr>
                                                                    <th></th> 
                                                                    <td>
                                                                        <?php
                                                                            echo '<a href="javascript:void(0);" onclick=branchdetails("'.$main_app->strsafe_input($enc_assref_num).'"); class="btn border btn-danger btn-sm p-2 d-block" >Edit</a>';             
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <th>State</th>                                                                        
                                                                    <td>    
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_STATE_CODE']) && $item_data1['ASSREQ_STATE_CODE'] != "") {
                                                                            $statenm = $main_app->sql_fetchcolumn("SELECT STATE_NAME FROM STATE WHERE STATE_CODE = '".$item_data1['ASSREQ_STATE_CODE']."'");
                                                                            echo (isset($statenm) && $statenm != "") ? $statenm : NULL;
                                                                            } 
                                                                        ?>
                                                                    
                                                                    </td>
                                                                
                                                                </tr>

                                                                <tr>
                                                                    <th>City</th>                                                                        
                                                                    <td>
                                                                    <?php
                                                                            if(isset($item_data1['ASSREQ_CITY_CODE']) && $item_data1['ASSREQ_CITY_CODE'] != "") {
                                                                                $citynm = $main_app->sql_fetchcolumn("SELECT LOCN_NAME FROM LOCATION WHERE LOCN_CODE = '".$item_data1['ASSREQ_CITY_CODE']."'");
                                                                                echo (isset($citynm) && $citynm != "") ? $citynm : NULL;
                                                                            } 
                                                                        ?>    
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th>Branch code & Name</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_BRANCH_CODE']) && $item_data1['ASSREQ_BRANCH_CODE'] != "") {
                                                                                $mbrnm = $main_app->sql_fetchcolumn("SELECT MBRN_NAME FROM MBRN WHERE MBRN_CODE = '".$item_data1['ASSREQ_BRANCH_CODE']."'");
                                                                                echo (isset($mbrnm) && $mbrnm != "") ? $mbrnm : NULL;
                                                                            } 
                                                                        ?>                                                                      
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th> Product  </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_PRODUCT_CODE']) && $item_data1['ASSREQ_PRODUCT_CODE'] != "") {
                                                                                $prdnm = $main_app->sql_fetchcolumn("SELECT PRODUCT_DESC FROM ASSREQ_PRODUCT_CODE WHERE PRODUCT_CODE = '".$item_data1['ASSREQ_PRODUCT_CODE']."'");
                                                                                echo (isset($prdnm) && $prdnm != "") ? $prdnm : NULL;
                                                                            } 
                                                                    ?>                                                                     
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th>Account Sub-Type  </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_ACNT_SUBTYP']) && $item_data1['ASSREQ_ACNT_SUBTYP'] != "") {
                                                                                $acntsubtyp = $main_app->sql_fetchcolumn("SELECT ACNTTYP_DESC FROM ASSREQ_ACNT_SUBTYP WHERE ACNTTYP_CODE = '".$item_data1['ASSREQ_ACNT_SUBTYP']."'");
                                                                                echo (isset($acntsubtyp) && $acntsubtyp != "") ? $acntsubtyp : NULL;
                                                                            } 
                                                                    ?>                                                                     
                                                                    </td>
                                                                </tr>

                                                            </thead>
                                                        </table>    
                                                    </div>    
                                                </div>
                                              
                                                <div class="card-header collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                                    <a class="card-title">
                                                        <b>Basic Customer Details </b>
                                                    </a>
                                                </div>
                                                <div id="collapseTwo" class="card-body collapse" data-parent="#accordion" >
                                                    <div class="table-responsive">
                                                        <table class="table final-view">
                                                            <thead>
                                                                <tr>
                                                                    <th></th> 
                                                                    <td>
                                                                        <?php
                                                                            echo '<a href="javascript:void(0);"  onclick=bcustdetails("'.$main_app->strsafe_input($enc_assref_num).'"); class="btn border btn-danger btn-sm p-2 d-block" >Edit</a>'; 
                                                                        ?>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th>Full Name</th>                                                                        
                                                                    <td>   
                                                                        <?php

                                                                            $fullname = "";
                                                                            $fullname .= (isset($item_data1['ASSREQ_CUST_FIRST_NAME']) && $item_data1['ASSREQ_CUST_FIRST_NAME'] != "") ? trim($item_data1['ASSREQ_CUST_FIRST_NAME']) : "";
                                                                            $fullname .= (isset($item_data1['ASSREQ_CUST_MIDDLE_NAME']) && $item_data1['ASSREQ_CUST_MIDDLE_NAME'] != "") ? " ". $item_data1['ASSREQ_CUST_MIDDLE_NAME'] : "";
                                                                            $fullname .= (isset($item_data1['ASSREQ_CUST_LAST_NAME']) && $item_data1['ASSREQ_CUST_LAST_NAME'] != "") ? " ". $item_data1['ASSREQ_CUST_LAST_NAME'] : "";
                                                                            $cust_full_name = strtoupper($fullname);
                                                                            
                                                                            echo (isset($cust_full_name) && $cust_full_name != "") ? $cust_full_name : NULL;
                                                                            
                                                                        ?>                                                                                                                          
                                                                    </td>
                                                                </tr>   

                                                                <tr>
                                                                    <th>Place of birth</th>                                                                        
                                                                    <td>   
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_PLACE_OF_BIRTH']) && $item_data1['ASSREQ_PLACE_OF_BIRTH'] != "") {
                                                                                $placebirth = $main_app->sql_fetchcolumn("SELECT LOCN_NAME FROM LOCATION WHERE LOCN_CODE = '".$item_data1['ASSREQ_PLACE_OF_BIRTH']."'");
                                                                                echo (isset($placebirth) && $placebirth != "") ? $placebirth : NULL;
                                                                            } 
                                                                        ?>                                                                                                                          
                                                                    </td>
                                                                </tr>   

                                                                <tr>        
                                                                    <th>Occupation</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_OCCUPATION_CODE']) && $item_data1['ASSREQ_OCCUPATION_CODE'] != "") {
                                                                                $occupation = $main_app->sql_fetchcolumn("SELECT OCCUPATIONS_DESCN FROM OCCUPATIONS WHERE OCCUPATIONS_CODE = '".$item_data1['ASSREQ_OCCUPATION_CODE']."'");
                                                                                echo (isset($occupation) && $occupation != "") ? $occupation : NULL;
                                                                            } 
                                                                        ?>                                                                        
                                                                    </td>
                                                                </tr>

                                                            
                                                                <tr>
                                                                    <th>Annual Income </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_ANNUAL_INCOME']) && $item_data1['ASSREQ_ANNUAL_INCOME'] != "") {
                                                                                $income = $main_app->sql_fetchcolumn("SELECT INCSLAB_DESCN FROM INCOMESLAB WHERE INCSLAB_CODE = '".$item_data1['ASSREQ_ANNUAL_INCOME']."'");
                                                                                echo (isset($income) && $income != "") ? $income : NULL;
                                                                            } 
                                                                        ?>                                
                                                                    </td>
                                                                </tr>    
                                                                <tr>    
                                                                    <th>Fathers name</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_FATHERSNAME']) && $item_data1['ASSREQ_FATHERSNAME'] != "") {
                                                                                echo  $main_app->strsafe_input($item_data1['ASSREQ_FATHERSNAME']);
                                                                            } 
                                                                        ?>    
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th>Mothers name</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_MOTHERSNAME']) && $item_data1['ASSREQ_MOTHERSNAME'] != "") {
                                                                                echo  $main_app->strsafe_input($item_data1['ASSREQ_MOTHERSNAME']);
                                                                            } 
                                                                        ?>    
                                                                    </td>
                                                                </tr> 
                                                                
                                                                <tr>
                                                                    <th>Date of Birth</th>                                                                        
                                                                    <td>   
                                                                        <?php
                                                                            
                                                                            if(isset($item_data1['ASSREQ_DOB']) && $item_data1['ASSREQ_DOB'] != "") {
                                                                                echo date('d-m-Y', strtotime($item_data1['ASSREQ_DOB']));
                                                                            } 
                                                                        ?>                                                                                                                          
                                                                    </td>
                                                                </tr>   
                                                                
                                                                <tr>          
                                                                    <th>Religion </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_RELIGION_CODE']) && $item_data1['ASSREQ_RELIGION_CODE'] != "") {
                                                                                echo $item_data1['ASSREQ_RELIGION_CODE'];                                                                       
                                                                                // echo $main_app->getval_field('cbuat.religion','religion_descn','religion_code',$item_data1['ASSREQ_RELIGION_CODE']);
                                                                            }
                                                                        ?>                                  
                                                                    </td>
                                                                </tr>

                                                                <tr>          
                                                                    <th>Qualification </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_QUALIFICATION']) && $item_data1['ASSREQ_QUALIFICATION'] != "") {
                                                                                //echo  $main_app->strsafe_output($item_data1['ASSREQ_QUALIFICATION']);                                                                          
                                                                                echo $main_app->getval_field('ASSREQ_QUALIFICATION','QUALIF_DESCN','QUALIF_CODE',$item_data1['ASSREQ_QUALIFICATION']);
                                                                            } 
                                                                        ?>                                  
                                                                    </td>
                                                                </tr>
                                                               
                                                                <tr>
                                                                    <th>DBT Beneficiary </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_DBTCHECK']) && $item_data1['ASSREQ_DBTCHECK'] != "") {
                                                                                if(isset($item_data1['ASSREQ_DBTCHECK']) && $item_data1['ASSREQ_DBTCHECK'] == "1"){
                                                                                    echo "Yes";
                                                                                }elseif(isset($item_data1['ASSREQ_DBTCHECK']) && $item_data1['ASSREQ_DBTCHECK'] == "0"){
                                                                                    echo "No";
                                                                                }
                                                                            } 
                                                                        ?>   
                                                                    </td>     
                                                                </tr>  
                                                               
                                                                <tr>          
                                                                    <th>Marital Status </th>                                                                        
                                                                    <td>
                                                                        <?php

                                                                        
                                                                            if(isset($item_data1['ASSREQ_MARITAL_STATUS']) && $item_data1['ASSREQ_MARITAL_STATUS'] != "") {
                                                                                if(isset($item_data1['ASSREQ_MARITAL_STATUS']) && $item_data1['ASSREQ_MARITAL_STATUS'] == "S"){
                                                                                    echo "Single";
                                                                                }elseif(isset($item_data1['ASSREQ_MARITAL_STATUS']) && $item_data1['ASSREQ_MARITAL_STATUS'] == "M"){
                                                                                    echo "Married";
                                                                                }
                                                                                // elseif(isset($item_data1['ASSREQ_MARITAL_STATUS']) && $item_data1['ASSREQ_MARITAL_STATUS'] == "3"){
                                                                                //     echo "Divorced";
                                                                                // }
                                                                            } 
                                                                        ?>   
                                                                    </td>  
                                                                </tr>

                                                                <?php
                                                                if(isset($item_data1['ASSREQ_MARITAL_STATUS']) && $item_data1['ASSREQ_MARITAL_STATUS'] == "M") {
                                                                ?>
                                                                    <tr>          
                                                                        <th>Spouse Name</th>                                                                        
                                                                        <td>
                                                                            <?php

                                                                                if(isset($item_data1['ASSREQ_SPOUSE_NAME']) && $item_data1['ASSREQ_SPOUSE_NAME'] != "") {
                                                                                    echo  $main_app->strsafe_input($item_data1['ASSREQ_SPOUSE_NAME']);
                                                                                } 
                                                                            ?>   
                                                                        </td>  
                                                                    </tr>
                                                                <?php } ?>

                                                                <?php
                                                                //fetching  editable address in table
                                                                $editaddress = json_decode($item_data1['ASSREQ_EDIT_ADDRESS'], true);
                                                                $EHOUSENUMBER =  isset($editaddress['HOUSENUMBER']) ? $editaddress['HOUSENUMBER'] : "";
                                                                $ESTREETADDRESS = isset($editaddress['STREETADDRESS']) ? $editaddress['STREETADDRESS'] : "";
                                                                $EDISTRICT = isset($editaddress['DISTRICT']) ? $editaddress['DISTRICT'] : "";
                                                                $ESTATE = isset($editaddress['STATE']) ? $editaddress['STATE'] : "";
                                                                $EPINCODE = isset($editaddress['PINCODE']) ? $editaddress['PINCODE'] : "";    
                                                                ?>

                                                                <tr>
                                                                    <th>House Number/ Address I </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($EHOUSENUMBER) && $EHOUSENUMBER != "") {
                                                                                echo  $main_app->strsafe_input($EHOUSENUMBER);
                                                                            } 
                                                                        ?>    
                                                                    </td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <th>Street/ Address II </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($ESTREETADDRESS) && $ESTREETADDRESS != "") {
                                                                                echo  $main_app->strsafe_input($ESTREETADDRESS);
                                                                            } 
                                                                        ?>    
                                                                    </td>
                                                                </tr> 

                                                                <tr>
                                                                    <th>District</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($EDISTRICT) && $EDISTRICT != "") {
                                                                                echo  $main_app->strsafe_input($EDISTRICT);
                                                                            } 
                                                                        ?>    
                                                                    </td>
                                                                </tr> 

                                                                <tr>
                                                                    <th>State</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($ESTATE) && $ESTATE != "") {
                                                                                echo  $main_app->strsafe_input($ESTATE);
                                                                            } 
                                                                        ?>    
                                                                    </td>
                                                                </tr> 

                                                                <tr>
                                                                    <th>Pincode</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($EPINCODE) && $EPINCODE != "") {
                                                                                echo  $main_app->strsafe_input($EPINCODE);
                                                                            } 
                                                                        ?>    
                                                                    </td>
                                                                </tr> 

                                                                <tr>
                                                                    <th>Weaker Section</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_WEAKER_CODE']) && $item_data1['ASSREQ_WEAKER_CODE'] != "") {
                                                                                echo $item_data1['ASSREQ_WEAKER_CODE'];                                                                       
                                                                                // echo $main_app->getval_field('cbuat.wksec','wksec_descn','wksec_code',$item_data1['ASSREQ_RELIGION_CODE']);
                                                                            }
                                                                        ?>     
                                                                    </td>
                                                                </tr> 

                                                                <tr>
                                                                    <th>Initial Deposit section</th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_INITIAL_DEPOSIT']) && $item_data1['ASSREQ_INITIAL_DEPOSIT'] != "") {
                                                                                echo $item_data1['ASSREQ_INITIAL_DEPOSIT'];                                                                       
                                                                                // echo $main_app->getval_field('cbuat.wksec','wksec_descn','wksec_code',$item_data1['ASSREQ_RELIGION_CODE']);
                                                                            }
                                                                        ?>     
                                                                    </td>
                                                                </tr> 

                                                                <tr>
                                                                    <th>Amount </th>                                                                        
                                                                    <td>
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_AMT_DEPOSIT']) && $item_data1['ASSREQ_AMT_DEPOSIT'] != "") {
                                                                                echo $main_app->money_format_INR($item_data1['ASSREQ_AMT_DEPOSIT']);                                                                       
                                                                            }
                                                                        ?>     
                                                                    </td>
                                                                </tr> 

                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="card-header collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                                    <a class="card-title">
                                                        <b> Nominee Details </b>
                                                    </a>
                                                </div>
                                                <div id="collapseThree" class="collapse" data-parent="#accordion" >
                                                    <div class="table-responsive">
                                                        <table class="table final-view">
                                                            <thead>   
                                                                <tr>
                                                                    <th></th> 
                                                                    <td>
                                                                        <?php
                                                                            echo '<a href="javascript:void(0);"  onclick=bnomineedetails("'.$main_app->strsafe_input($enc_assref_num).'"); class="btn border btn-danger btn-sm p-2 d-block" >Edit</a>'; 
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                       
                                                                <tr>
                                                                    <th>Nominee Name</th>                                                                        
                                                                    <td>   
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_NOMINEE_NAME']) && $item_data1['ASSREQ_NOMINEE_NAME'] != "") {
                                                                                echo  $main_app->strsafe_output($item_data1['ASSREQ_NOMINEE_NAME']);
                                                                            } 
                                                                        ?>                                                                                                                          
                                                                    </td>
                                                                  
                                                                </tr>
                                                                <tr>
                                                                    <th>Date of birth</th>                                                                        
                                                                        <td>   
                                                                            <?php
                                                                                if(isset($item_data1['ASSREQ_NOMINEE_DOB']) && $item_data1['ASSREQ_NOMINEE_DOB'] != "") {
                                                                                    echo date('d-m-Y', strtotime($item_data1['ASSREQ_NOMINEE_DOB']));
                                                                                } 
                                                                            ?>                                                                                                                          
                                                                    </td>
                                                                  
                                                                </tr>

                                                                <tr>
                                                                    <th>Relation to the Account holder</th>                                                                        
                                                                    <td> 
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_NOMINEE_RELATION']) && $item_data1['ASSREQ_NOMINEE_RELATION'] != "") {
                                                                                echo  $relation = $main_app->sql_fetchcolumn("SELECT RELATION_DESCN FROM RELATION WHERE RELATION_CODE = '".$item_data1['ASSREQ_NOMINEE_RELATION']."'");
                                                                            } 
                                                                        ?>          
                                                                                                                                                                                        
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Address </th>                                                                        
                                                                    <td>   
                                                                        <?php
                                                                            if(isset($item_data1['ASSREQ_NOMINEE_ADDRESS']) && $item_data1['ASSREQ_NOMINEE_ADDRESS'] != "") {
                                                                                echo  $main_app->strsafe_output($item_data1['ASSREQ_NOMINEE_ADDRESS']);
                                                                            } 
                                                                        ?>                                                                                                                          
                                                                    </td>
                                                                </tr>

                                                                <?php if(isset($item_data1['ASSREQ_MINOR_FLAG']) && $item_data1['ASSREQ_MINOR_FLAG']=='Y'){?>
                                                                    <tr>
                                                                        <th>Nature of the Guardian</th>                                                                        
                                                                        <td>   
                                                                            <?php
                                                                                if(isset($item_data1['ASSREQ_GUARDIAN_NATURE']) && $item_data1['ASSREQ_GUARDIAN_NATURE'] != "") {
                                                                                    if(isset($item_data1['ASSREQ_GUARDIAN_NATURE']) && $item_data1['ASSREQ_GUARDIAN_NATURE'] == "F"){
                                                                                        echo "Father";
                                                                                    }elseif(isset($item_data1['ASSREQ_GUARDIAN_NATURE']) && $item_data1['ASSREQ_GUARDIAN_NATURE'] == "M"){
                                                                                        echo "Mother";
                                                                                    }elseif(isset($item_data1['ASSREQ_GUARDIAN_NATURE']) && $item_data1['ASSREQ_GUARDIAN_NATURE'] == "O"){
                                                                                        echo "Others";
                                                                                    }
                                                                                } 
                                                                            
                                                                            ?>                                                                                                                          
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Guardian Name </th>                                                                        
                                                                        <td>   
                                                                            <?php
                                                                                if(isset($item_data1['ASSREQ_NOMINEE_GUARDIAN']) && $item_data1['ASSREQ_NOMINEE_GUARDIAN'] != "") {
                                                                                    echo  $main_app->strsafe_output($item_data1['ASSREQ_NOMINEE_GUARDIAN']);
                                                                                } 
                                                                            ?>                                                                                                                          
                                                                        </td>
                                                                    </tr>

                                                                <?php
                                                                }
                                                                ?>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <?php 
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




