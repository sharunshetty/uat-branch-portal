<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') or exit();

/** Mode */
$page_mode = (isset($_POST['data_mode'])) ? $main_app->strsafe_input($_POST['data_mode']) : ""; // Don't change - V = View, M = Modify Data

/** Get Data */
if (isset($_POST['id']) && $_POST['id'] != NULL) {
    $primary_value = $safe->str_decrypt($_POST['id'], $_SESSION['SAFE_KEY']);
}

if (!isset($primary_value) || $primary_value == false) {
    echo "<script> swal.fire('','Unable to fetch the data'); $('#ModalWin').modal('hide'); </script>";
    exit();
}


/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS_DTL";


if (isset($primary_value)) {

    $primary_value = $main_app->strsafe_output($primary_value);

    $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE AUTH_REF_NUM = :primary_value", array('primary_value' => $primary_value));
    $item_data = $sql_exe->fetch();
}

if (!isset($item_data['AUTH_REF_NUM']) || $item_data['AUTH_REF_NUM'] == " " || $item_data['AUTH_REF_NUM'] == NULL) {
    echo "<script> swal.fire('','Unable to fetch the data'); $('#ModalWin').modal('hide'); </script>";
    exit();
}

// echo "<script>$('#authbut".$row['AUTH_REF_NUM']."').hide();   show('authbut".$row['AUTH_REF_NUM']."'); </script>";

/** Modal Title */
$ModalLabel = "Employee Details - " . $item_data['AUTH_REF_NUM'];

?>

<form id="app-form-2" name="app-form-2" method="post" action="javascript:void(null);" class="form-material">
    <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>" />
    <input type="hidden" name="id" id="id" value="<?php echo (isset($_POST['id'])) ? $_POST['id'] : ""; ?>" />
    <div class="modal-body min-div" id="load-content">
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <tbody>
                            <?php

                                echo '<tr><td>Employee ID</td><td>';
                                echo isset($item_data["USER_ID"]) ? $main_app->strsafe_output($item_data['USER_ID']) : "-NA-";
                                echo '</td></tr>'; 
                            
                                echo '<tr><td>Employee Name</td><td>';
                                echo isset($item_data["USER_FULLNAME"]) ? $main_app->strsafe_output($item_data['USER_FULLNAME']) : "-NA-";
                                echo '</td></tr>';

                                
                                // echo '<tr><td>Branch Name</td><td>';
                                // echo isset($item_data["USER_REGIONS"]) ? $main_app->strsafe_output($item_data['USER_REGIONS']) : "-NA-";
                                // //echo $main_app->getval_field('cbuat.mbrn','mbrn_name','mbrn_code',$item_data['USER_REGIONS']);
                                // echo '</td></tr>';

                                
                                echo '<tr><td>Current Branch Name</td><td>';
                                echo isset($item_data["USER_REGIONS"]) ? $main_app->strsafe_output($item_data['USER_REGIONS']) : "-NA-";
                                //echo $main_app->getval_field('cbuat.mbrn','mbrn_name','mbrn_code',$item_data['USER_REGIONS']);
                                echo '</td></tr>';


                                if($item_data["USER_ROLE_CODE"]){
                                    echo '<tr><td>Role</td><td>';
                                   echo isset($item_data["USER_ROLE_CODE"]) ?  $main_app->getval_field('ASSREQ_USER_ROLES','ROLE_DESC','ROLE_CODE',$item_data['USER_ROLE_CODE']) : "-NA-";
                                   echo '</td></tr>';
                                }
 
                                if($item_data["USER_MOBILE"]){
                                   
                                 echo '<tr><td>Mobile Number</td><td>';
                                 echo isset($item_data["USER_MOBILE"]) ? $main_app->strsafe_output($item_data['USER_MOBILE']) : "-NA-";
                                 echo '</td></tr>';
                 
                                }
 
                               if($item_data["USER_EMAIL"]){
                                     
                                  echo '<tr><td>Email Number</td><td>';
                                 echo isset($item_data["USER_EMAIL"]) ? $main_app->strsafe_output($item_data['USER_EMAIL']) : "-NA-";
                                 echo '</td></tr>';
                 
                                }
 

                                echo '<tr><td>User Status for approval</td><td>';
                               if($item_data['USER_STATUS'] == "A") {
                                    echo "<span> Active  </span>";
                                } elseif($item_data['USER_STATUS'] == "B") {
                                    echo "<span> Block</span>";
                                }elseif($item_data['USER_STATUS'] == "R") {
                                    echo "<span> Resign </span>";
                                }elseif($item_data['USER_STATUS'] == "T") {
                                    echo "<span> Transfer </span>";
                                }elseif($item_data['USER_STATUS'] == "M") {
                                    echo "<span> Modify Details </span>";
                                }
                            
                                echo '</td></tr>';

                                if($item_data['USER_STATUS'] == "T") {
                                
                                    echo '<tr><td>Transfer New Branch Name</td><td>';
                                    echo isset($item_data["TRANSFER_TOBRANCH"]) ? $main_app->strsafe_output($item_data['TRANSFER_TOBRANCH']) : "-NA-";
                                    // echo $main_app->getval_field('cbuat.mbrn','mbrn_name','mbrn_code',$item_data['TRANSFER_TOBRANCH']);
                                    echo '</td></tr>';

                                    echo '<tr><td>Transfer Date</td><td>';
                                    echo isset($item_data["TRANSFER_DATE"]) ? $main_app->valid_date($item_data['TRANSFER_DATE'],'d-m-Y') : "-NA-";
                                    echo '</td></tr>';
                                
                                }elseif($item_data['USER_STATUS'] == "R") {

                                    echo '<tr><td>Resigned Date</td><td>';
                                    echo isset($item_data["RESIGN_DATE"]) ? $main_app->valid_date($item_data['RESIGN_DATE'],'d-m-Y') : "-NA-";
                                    echo '</td></tr>';

                                }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 </form>



<script type="text/javascript">

    $(document).ready(function() {
        //$("a.single_image").fancybox();
        //#ModalLabel
        $('#ModalWin-ModalLabel').html("<?php echo $ModalLabel; ?>");

    });
    
</script>