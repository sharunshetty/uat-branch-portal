<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- ============================================================== -->
    <!-- ######### POWERED BY LCODE TECHNOLOGIES PVT. LTD. ############ -->
    <!-- ============================================================== -->

    <title><?php echo (isset($page_title) && $page_title != NULL) ? $page_title : BRAND_SHORT_NAME." - ".APP_NAME; ?></title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="shortcut icon" href="<?php echo CDN_URL; ?>/favicon.ico" type="image/ico"/>
    <link rel="manifest" href="<?php echo CDN_URL; ?>/manifest.json">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo CDN_URL; ?>/theme/css/style.css?v=<?php echo CDN_VER; ?>" type="text/css" media="screen"/>
  
    <?php
    /** Extra Headers */
    echo (isset($page_headers)) ? $page_headers : "";
    ?>

</head>
<body class="sidebar-mini layout-fixed text-sm no-scroll">
<div class="wrapper">

  <!-- Navbar : Start -->
  <nav class="main-header navbar navbar-expand navbar-light brand-navbar">

    <ul class="navbar-nav">
    <li class="nav-item push_hide">
      <a class="nav-link" data-widget="pushmenu" href="javascript:void(0)"><i class="mdi mdi-menu h5"></i></a>
    </li>
    <li class="nav-item">
      <span class="nav-link rem-09"><?php echo APP_NAME; ?></span>
    </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      
      <li class="nav-item">
        <!-- <a href="javascript:void(0)" class="nav-link"><i class="mdi mdi-autorenew"></i> Change</a> -->
      </li>

      <li class="nav-item dropdown">
      <a class="nav-link" style="margin-top:-3px;" data-toggle="dropdown" href="javascript:void(0)"><i class="mdi mdi-dots-horizontal h4"></i></a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- <a href="<?php echo APP_URL; ?>/user-profile" class="dropdown-item start-loader"><i class="mdi mdi-account h6 mr-1"></i> My Profile</a> -->
          <!-- <a href="<?php echo APP_URL; ?>/user-change-pass" class="dropdown-item start-loader"><i class="mdi mdi-lock-outline h6 mr-1"></i> Change Password</a> -->
          <a href="<?php echo APP_URL; ?>/logout" class="dropdown-item start-loader"><i class="mdi mdi-power h6 mr-1"></i> Logout</a>
      </div>
      </li>
    
    </ul>

  </nav>
  <!-- Navbar : End -->

  <!-- Main Sidebar Container : Start -->
  <aside class="main-sidebar sidebar-dark-primary brand-bg elevation-4">

    <div class="brand-link text-center mb-3">
      <span class="brand-text font-weight-light">
        <img src="<?php echo CDN_URL; ?>/theme/img/brand-logo.png" alt="<?php echo BRAND_NAME; ?>" class="brand-logo">
      </span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar no-scroll">

      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info text-white">

          <?php 
            echo (isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] != "") ? "EMP ID : ".$main_app->strsafe_output($_SESSION['USER_ID'])."<br/>" : "";
            // echo (isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] != "") ? "EMP Name : ".$main_app->getval_field('ASSREQ_USER_ACCOUNTS','USER_FULLNAME','USER_ID',$_SESSION['USER_ID'])."<br/>" : "";
            echo (isset($_SESSION['BRANCH_CODE']) && $_SESSION['BRANCH_CODE'] != "") ? "Branch : ". $main_app->getval_field('cbuat.mbrn','mbrn_name','mbrn_code',$_SESSION['BRANCH_CODE'])."<br/>" : "";
            echo (isset($_SESSION['USER_ROLE']) && $_SESSION['USER_ROLE'] != "") ? "Role : ". $main_app->getval_field('ASSREQ_USER_ROLES','ROLE_DESC','ROLE_CODE',$_SESSION['USER_ROLE'])."<br/>" : "";

          ?>
           
        </div>
      </div>
 
      <!-- Sidebar Menu -->
      <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-legacy" data-widget="treeview" role="menu" data-accordion="false">

          <!-- Menu -->
          <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/" class="nav-link start-loader"><i class="nav-icon mdi mdi-apps"></i><p>Home</p></a>
          </li>

      </ul>
      </nav>

    </div>

  </aside>
  <!-- Main Sidebar Container : End -->

  <!-- Content Wrapper -->
  <div class="content-wrapper">

  <!-- Content Title -->
  <section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 mt-1">
        <?php
          if(isset($page_link) && $page_link != "") {
            echo "<a href='".APP_URL."/".$page_link."' class='page-h-title start-loader'>{$page_title}</a>";
          } else {
            echo "<a href='javascript:void(0);' class='page-h-title'>{$page_title}</a>";
          }

          if(isset($parent_page_title) && $parent_page_title != NULL) {
            if(isset($parent_page_link) && $parent_page_link != NULL) {
              echo "<div><a href='".APP_URL."/{$parent_page_link}' class='text-muted start-loader'><i class='mdi mdi-arrow-left'></i> {$parent_page_title}</a></div>";
            } else {
              echo "<div><a href='javascript:void(0);' onclick='history.go(-1);return false;' class='text-muted'><i class='mdi mdi-arrow-left'></i> {$parent_page_title}</a></div>";
            }
          }

        ?>
      </div>
    </div>
  </div>
  </section>
