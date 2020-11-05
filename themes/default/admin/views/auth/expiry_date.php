<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?=admin_url()?>'; }</script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-cosmo.css"/>
    <link href="<?= base_url() ?>assets/css/custom.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/css/font-awesome.css" rel="stylesheet" type="text/css" />
     <style type="text/css">
        html{ height: 100%; }
        body { padding-bottom:40px; height:auto; background:url("<?= base_url() ?>/assets/images/bg2.png"); }
        form { margin:0; }
        
    </style>
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/respond.min.js"></script>
    <![endif]-->

</head>

<body >
<div id="install-header" class="install_header_logo text-center">
        <img src="<?= base_url() ?>assets/images/sram_logo.png" alt="reload image"/>
    </div>

        
    </div>
<div class="install" style="text-align: center;">
        <div style="width: 100%; font-size: 10em; color: #757575; text-shadow: 0 0 2px #333, 0 0 2px #333, 0 0 2px #333; text-align: center;">
        <i class="icon-lock"></i></div><h3 class="alert-text text-center">Your Project is Expired!<br><small style="color:#666;">Please contact your developer/support.</small></h3>

    </div>



    <script src="<?= $assets ?>js/jquery.js"></script>
    <script src="<?= $assets ?>js/bootstrap.min.js"></script>
    <script src="<?= $assets ?>js/jquery.cookie.js"></script>
    <script src="<?= $assets ?>js/login.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            localStorage.clear();
            var hash = window.location.hash;
            if (hash && hash != '') {
                $("#login").hide();
                $(hash).show();
            }
        });
    </script>
</body>
</html>
