<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Franchisee</title>
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
                                <li class="active">Partner with us – Become a Franchisee</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
         </div>
         
         <div id="fh5co-about">

		<div class="container">

			<div class="about-content">

				<div class="row animate-box">

					<div class="col-md-12 col-xs-12 ">

						<div class="desc col-md-12 col-xs-12">

							<h3>Partner with us – Become a Franchisee </h3>

							<p>Already running a business and want to join Heyycab fleet? Grab this exclusive business opportunity to launch, list, manage and promote your vehicles and professional drivers using the most efficient technology led taxi cab service anywhere in the world.</p> 

							<p>Become our Brand Franchisee and sky-rocket your business with our brand and the ‘tried & tested’ technology. Explore our exclusive business bouquet, a complete package of geographical licenses, advanced technological algorithms, business operations policies, training and 24 x 7 support. </p>

							

						</div>

						<div class="col-sm-7 col-xs-12 animate-box">

							<div class="desc">

								<h3>Why become a Business Partner/Franchisee</h3>

								<ul>

									<li>List all your vehicles and drivers</li>

									<li>Live management of your fleet</li>

									<li>Track fleet’s performance</li>

									<li>Access Vital Business Reports of Total Earnings, Driver Login, Trip Details, Earningsetc</li>

									<li>24 x 7 live support team at your disposal</li>

								</ul>

							</div>

							

						</div>

						<div class="desc col-sm-5 col-xs-12 animate-box">

							<h3>Enroll As Business Partner/Franchisee</h3>

							<div class="driver_status"></div>

							<form name="partner_form" method="post" id="partner_form">

								<div class="input-group col-sm-12" style="margin-bottom: 15px">

									<input type="text" class="form-control" name="partner_name" id="partner_name" placeholder="Enter your Name*">

								</div>

								<div class="input-group col-sm-12 mobile_addon" style="margin-bottom: 15px">

									<span class="input-group-addon ">+91</span>

									<input type="text" class="form-control" name="partner_phone" id="partner_phone" placeholder="Enter 10 Digit Mobile Number*">

								</div>

							

								<div class="input-group col-sm-12" style="margin-bottom: 15px">

									<input type="email" class="form-control" name="driver_email" id="partner_email" placeholder="Enter your Email-id (Optional)">

								</div>

								<div class="input-group col-sm-12" style="margin-bottom: 15px">

<!--									<label class="control-label">Captcha Code</label>-->

									<div class="g-recaptcha" id="g-recaptcha"></div>

									<input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">

								</div>

								<div class="form-group col-sm-12">

									<input type="submit" class="btn btn-primary" name="partner_submit" id="partner_submit" value="Submit">

								</div>

							</form>	

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
