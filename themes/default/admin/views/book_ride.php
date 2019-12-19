<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Book Ride</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>images/favicon.ico"/>
    
    <?php $this->load->view($this->theme . 'frontend_css'); ?>
</head>

<body class="login-page ">
    <div class="fh5co-loader"></div>
    <div id="page">
    	<?php $this->load->view($this->theme . 'frontend_header'); ?>
        <header id="fh5co-header" class="fh5co-cover fh5co-cover-sm" role="banner">
            <div class="overlay"></div>
            <div class="container">
                <div class="row">
                </div>
            </div>
        </header>
        <div class="breadcrumb_sec">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="scp-breadcrumb">
                            <ul class="breadcrumb">
                                <li><a href="index.php">Home</a></li>
                                <li class="active">Book a Ride</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
         </div>
         
         <div id="fh5co-testimonial">

		<div class="container">

        <div class="row">

            <div class="col-md-12 text-center subscription_id">

                <h3>Download the App from</h3>

                <a href="#" target="_blank"><img src="<?= $assets ?>frontend/images/android.png" alt="android" width="200px;"></a>

                <a href=""><img src="<?= $assets ?>frontend/images/apple.png" alt="apple phone" width="200px;"></a>

            </div>

        </div>

    </div>

	</div>

	<div id="fh5co-about">

		<div class="container">

			<div class="about-content">

				<div class="row animate-box">

					<div class="col-md-12 col-xs-12 ">

						<div class="desc">

<!--

							<h3>Partner with us – Become a Franchisee</h3>

							<p>Thinking business? HeyyCab gives you an exclusive business opportunity to launch and promote the most efficient technology led taxi cab service anywhere in the world.</p> 

							<p>Become our Brand Franchisee and kick start your own business with our brand and the ‘tried&tested’ technology. Explore our exclusive business bouquet, a complete package of geographical licenses, advanced technological algorithms, business operations policies, training and 24 x 7 support. </p>

-->

							

					</div>

					

					

				</div>

			</div>

		

			

		</div>

	</div>
    
         <?php $this->load->view($this->theme . 'frontend_footer'); ?>
         
    </div>
    <?php $this->load->view($this->theme . 'frontend_js'); ?>
<!--<script src="<?= $assets ?>js/jquery.js"></script>
<script src="<?= $assets ?>js/bootstrap.min.js"></script>-->
</body>
</html>
