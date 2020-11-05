<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Driver with us</title>
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
                                <li class="active">Partner with us - Drive with us</li>
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

							<h3>Partner with us - Drive with us</h3>

							<p>Become an entrepreneur in a day, start your business on the same day.It takes only a few minutes to enroll as a driverpartner with us. Enroll now and become a part of the fast growing taxi hailing service. </p> 

							<p>Our driver partners are rolling in the numbers in the odometer, interacting and building memorable rides for customers across the cities of India.Reaching our values customers to their destination on time, every time. </p>

							

						</div>

						<div class="col-sm-7 col-xs-12 animate-box">

							<div class="desc">

								<h3>Why Drive With Us</h3>

								<ul>

									<li>Flexible working hours</li>

									<li>Spontaneous Business Opportunity</li>

									<li>Best Rate per KM</li>

									<li>Immediate & Transparent Payments</li>

									<li>Flexible Payment Mode</li>

									<li>GPS enabled – Navigate & Track Rides</li>

									<li>Uninterrupted Bookings - City, Outstation & Airport Rides</li>

									<li>24 X 7 Business Partner Helpline</li>

								</ul>

							</div>

							<div class="desc">

								<h3>What do we need?</h3>

								<p>All we need are the below documents while signing up business with you. </p> 

								<ul>

									<li>Driving License</li>

									<li>Aadhaar card</li>

									<li>PAN Card</li>

									<li>Cancelled Cheque or Passbook</li>

									<li>Address Proof</li>

									<li>Vehicle Documents – RC, Permit, Insurance</li>

								</ul>

							</div>

						</div>

						<div class="desc col-sm-5 col-xs-12 animate-box">

							<h3>Become our Driver Partner:</h3>

							<div class="driver_status"></div>

							<form name="driver_form" method="post" id="driver_form">

								<div class="input-group col-sm-12" style="margin-bottom: 15px">

									<input type="text" class="form-control" name="driver_name" id="driver_name" placeholder="Enter your Name*">

								</div>

								<div class="input-group col-sm-12 mobile_addon" style="margin-bottom: 15px">

									<span class="input-group-addon ">+91</span>

									<input type="text" class="form-control" name="driver_phone" id="driver_phone" placeholder="Enter 10 Digit Mobile Number*">

								</div>

								

								<div class="input-group col-sm-12" style="margin-bottom: 15px">

									<input type="email" class="form-control" name="driver_email" id="driver_email" placeholder="Enter your Email-id(Optional)">

								</div>

								<div class="input-group col-sm-12" style="margin-bottom: 15px">

<!--									<label class="control-label">Captcha Code</label>-->

									<div class="g-recaptcha" id="g-recaptcha"></div>

									<input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">

								</div>

								<div class="form-group col-sm-12">

									<input type="submit" class="btn btn-primary" name="driver_submit" id="driver_submit" value="Submit">

								</div>

							</form>	

						</div>

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
