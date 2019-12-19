<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Enquiry Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>images/favicon.ico"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/helpers/login.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/respond.min.js"></script>
    <![endif]-->

	<style>
	#map_canvas {
			 height: 100vh;
			 width:100%;
			 position:relative;
			 float:left;
		  }
	body, html
	{
		overflow-y:auto !important;background-color: #fdf59a;
	}
		.thankyou_meg{position:absolute;transform: translate(-50%,-50%);margin-right: -50%;left: 50%;top: 50%;}
		h3{font-size: 24px;font-weight: bold;margin: 15px 0px;}
		p{font-size: 16px;font-weight: bold;font-style: italic;}
	</style>


</head>

<body>
<section class="thankyou_meg">
    	<div class="container-fluid">
    		<div class="row">
    			<div class="col-sm-12 col-xs-12 text-center">
					<?php if ($Settings->logo2) {
						echo '<img src="' . base_url('assets/uploads/logo/login_logo.png') . '" alt="' . $Settings->site_name . '"  />';
					} ?>
				<h3>Thanks for contacting us!</h3>
                 <p> We will be in touch with you shortly... </p>
                 
    			</div>
    		</div>
    	</div>
    </section>

    
    
    
<script src="<?= $assets ?>js/jquery.js"></script>
<script src="<?= $assets ?>js/bootstrap.min.js"></script>

</body>
</html>
