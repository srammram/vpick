<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?= admin_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
   
    <title><?= $page_title ?> - <?= $Settings->site_name ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/favicon.ico"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
   
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
 <link href="<?= $assets ?>styles/helpers/bootstrap.min.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/select2.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.dtFilter.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/app_custom.js"></script>
    
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/jquery.js"></script>
    
    <![endif]-->
    <noscript><style type="text/css">#loading { display: none; }</style></noscript>
    <?php if ($Settings->user_rtl) { ?>
        <link href="<?= $assets ?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () { $('.pull-right, .pull-left').addClass('flip'); });
        </script>
    <?php } ?>
    <script type="text/javascript">
        $(window).load(function () {
            $("#loading").fadeOut("slow");
        });
    </script>
  
</head>

<body>
<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>
<div id="loading"></div>
<div id="app_wrapper">

    <div class="container" id="container">
        <div class="row" id="main-con">
        <table class="lt"><tr>
        
        <td class="content-con">
            <div id="content">
             
                <div class="row">
                    <div class="col-lg-12">
                        
                        <div class="alerts-con"></div>
<style>
 .main-menu .mm_reports a.submenu{
    height: 20px !important;
    background-color: transparent !important;
    color: #696969 !important;
    padding-top: 3px !important;
    padding-left: 16px !important;
    border-bottom: none !important;
}
.main-menu .mm_reports a.submenu span{
    white-space: normal;
   
    display: inline-block;
    width: 190px;
}
.mm_reports ul li ul li{
    background : none !important;
}
</style>