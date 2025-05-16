<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : karthik
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();
/** SQL */
$page_table_name = "ASSREQ_USER_ACCOUNTS";

// $html_op = "";

/** On Change */
if($_POST['cmd2'] == "onChange" && isset($_POST['field_name']) && isset($_POST['field_val']) && isset($_POST['dest_id'])) {

    if($_POST['field_name'] == "modify" && $_POST['field_val'] != "") {
		
        $add_keys = array('USER_ID' => $_POST['field_val']);
       
        /** For add */

        $sql_exe1 = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID and USER_STATUS = 'P'", $add_keys);
        $item_data1 = $sql_exe1->fetch();
        if($item_data1) { exit ("<script> swal.fire('','User Id is in pending list for the authorise approval.'); loader_stop(); enable('sbt'); </script>"); }

        
        // $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $add_keys);
        // $item_data = $sql_exe->fetch();
        // if($item_data) { exit ("<script> swal.fire('','User Id already registered.'); loader_stop(); enable('sbt'); </script>"); }
       
        $sql_exe = $main_app->sql_run("SELECT * FROM {$page_table_name} WHERE USER_ID = :USER_ID", $add_keys);
        $item_data = $sql_exe->fetch();

        if(isset($item_data['RESIGN_DATE']) && $item_data['RESIGN_DATE'] != '') {
        exit ("<script> swal.fire('','Resign date already assigned, cannot update.'); loader_stop(); enable('sbt'); </script>");
        }elseif($item_data) { exit ("<script> swal.fire('','User Id already registered.'); loader_stop(); enable('sbt'); </script>"); }

        try {
            //updated query on 24-10-2024
            // $sql_exe = $main_app->sql_run("select e.memp_num,e.memp_name,nvl(e.memp_email_id_1,e.memp_email_id_2) as menp_email, e.memp_gsm_num, case when br.mbrn_code<'1000' then br.mbrn_code end  as Branchcode,case when br.mbrn_code<'1000' then br.mbrn_code||'-'||br.mbrn_name end  as BranchName,
            // case when br.mbrn_code like '800%' then to_char( br.mbrn_code||'-'||br.mbrn_name) when br.mbrn_parent_admin_code like '800%' then to_char(br.mbrn_parent_admin_code||'-'||brc.mbrn_name)
            // end as clustercode,case when br.mbrn_code like '850%' then to_char(br.mbrn_code||'-'||br.mbrn_name) when br.mbrn_parent_admin_code like '850%' then to_char(br.mbrn_parent_admin_code||'-'||brc1.mbrn_name) 
            // when brc.mbrn_parent_admin_code like '850%' then to_char(brc.mbrn_parent_admin_code||'-'||brro.mbrn_name) end as rocode from cbuat.memp e join cbuat.users u on u.user_id=e.memp_num 
            // join cbuat.mbrn br on br.mbrn_entity_num='1' and br.mbrn_code=u.user_branch_code left join cbuat.mbrn brc on brc.mbrn_entity_num='1' and brc.mbrn_code=br.mbrn_parent_admin_code and br.mbrn_parent_admin_code like '800%' 
            // left join cbuat.mbrn brc1 on brc1.mbrn_entity_num='1' and brc1.mbrn_code=br.mbrn_parent_admin_code and br.mbrn_parent_admin_code like '850%' left join cbuat.mbrn brro on brro.mbrn_entity_num='1' and brro.mbrn_code=brc.mbrn_parent_admin_code and brc.mbrn_parent_admin_code like '850%'
            // left join  cbuat.empsusprel er on er.empsusprel_user_id=e.memp_num where er.empsusprel_user_id is null and e.memp_num = :USER_ID", $add_keys);   //10360110022774 
           
            $item_data = array("MEMP_NUM"=>"1389876883","MEMP_NAME"=>"kumar1","MEMP_GSM_NUM" => "8765564444","MENP_EMAIL" => "SSD@gmail.com", "MBRN_NAME" => "2","BRANCHCODE" => "2");
             //  $item_data = $sql_exe->fetch();
            
        } catch (Error $e) {
            //die($e->getMessage());
            echo "<script> swal.fire('','Unable to get data from CBS DB LINK');$('#app-form').trigger('reset'); $('#sbt').prop('disabled', true); loader_stop(); </script>";
            exit();
        }
        


        if(!isset($item_data) || $item_data == "" || $item_data == NULL) { 
            echo "<script> swal.fire('','Invalid user');$('#app-form').trigger('reset'); $('#sbt').prop('disabled', true); loader_stop(); 
            hide('add_btn');
            </script>";
        }
	   
        if(isset($item_data['MEMP_NUM']) && $item_data['MEMP_NUM'] != NULL) {

            echo "<script>";
                echo " $('#USER_ID').val(decode_ajax('".$main_app->strsafe_ajax($item_data['MEMP_NUM'])."')); ";
                echo " $('#USER_FULLNAME').val(decode_ajax('".$main_app->strsafe_ajax($item_data['MEMP_NAME'])."')); ";
                echo " $('#USER_EMAIL').val(decode_ajax('".$main_app->strsafe_ajax($item_data['MENP_EMAIL'])."')); ";
                echo " $('#USER_MOBILE').val(decode_ajax('".$main_app->strsafe_ajax($item_data['MEMP_GSM_NUM'])."')); ";
                echo " $('#USER_REGIONS').val(decode_ajax('".$main_app->strsafe_ajax($item_data['BRANCHCODE'])."')); ";
                echo " $('#TRANSFER_FROMBANK').val(decode_ajax('".$main_app->strsafe_ajax($item_data['BRANCHCODE'])."')); ";
                echo " $('#sbt').prop('disabled', false); loader_stop(); ";
            echo "</script>";
            

        } 

    }

}
