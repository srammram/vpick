<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact</title>
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
                                <li class="active">Contact</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
         </div>
         <div id="fh5co-contact">
		<div class="container">
			<div class="row">
				<div class="col-md-7 animate-box">
					<div class="fh5co-contact-info">
						<h3>Contact Information</h3>
						<h4>India Address:</h4>
						<ul>
							<li class="address">SRAMMRAM TECHNOLOGIES AND RESOURCES (OPC) PVT, <br> H.No.75/1 (1)/102<br>TAL.MURUD-J.DIST.RAIGAD / DARBAR ROAD,<br>MAHARASHTRA - 402401.</li>
<!--							<li class="phone"><a href="tel://1234567920">+ 1235 2355 98</a></li>-->
							<li class="email">
							<a href="mailto:salesin@heyycab.com"><b>Sales Enquiry :</b> salesin@heyycab.com</a><br>
							<a href="mailto:support@heyycab.com"><b>Support :</b>support@heyycab.com</a><br>
							<a href="mailto:contact@heyycab.com"><b>General Enqueries :</b>contact@heyycab.com</a>
							</li>
							<li class="phone"><a href="tel:7611141114">+91 7611141114</a></li>
<!--							<li class="url"><a href="http://FreeHTML5.co">FreeHTML5.co</a></li>-->
						</ul>
					</div>
					<div class="divider"></div>
					<div class="fh5co-contact-info">
						
						<ul>
							<li class="address">Master Tour Organizers Pvt Ltd,<br> 1173 Flat No 5 Apartment, <br>Shubh Ashirvad Sahakari Gruharachana Sansta, <br>Vasantrao Limaye Rd,Ramashram Society,Perugate, Sadashiv Peth,Pune,<br> Maharashtra 411030.</li>
							<li class="email">
							<a href="mailto:salesin@heyycab.com"><b>Sales Enquiry :</b> salesin@heyycab.com</a><br>
							<a href="mailto:support@heyycab.com"><b>Support :</b>support@heyycab.com</a><br>
							<a href="mailto:contact@heyycab.com"><b>General Enqueries :</b>contact@heyycab.com</a>
							</li>
							<li class="phone"><a href="tel:020 4106 8999">020 4106 8999</a></li>
<!--
							<li class="email"><a href="mailto:info@yoursite.com">info@yoursite.com</a></li>
							<li class="url"><a href="http://FreeHTML5.co">FreeHTML5.co</a></li>
-->
						</ul>
					</div>
					<div class="fh5co-contact-info">
						<h4>Malyasia Address:</h4>
						<ul>
							<li class="address">SRAM & MRAM RESOURCES BERHAD, <br> Suite B-3A-9 Block B Level 3A,<br>Megan Avenue II,12, Jalan Yap Kwan Seng,<br>50450 WP Kuala Lampur,<br>Malaysia.</li>
							<li class="email">
								<a href="mailto:salesmy@heyycab.com"><b>Sales Enquiry :</b> salesmy@heyycab.com</a><br>
								<a href="mailto:support@heyycab.com"><b>Support :</b>support@heyycab.com</a><br>
								<a href="mailto:contact@heyycab.com"><b>General Enqueries :</b>contact@heyycab.com</a>
							</li>
							<li class="phone"><a href="tel:6011 39992628">+6011 39992628 </a></li>
<!--
							<li class="phone"><a href="tel://1234567920">+ 1235 2355 98</a></li>
							<li class="email"><a href="mailto:info@yoursite.com">info@yoursite.com</a></li>
							<li class="url"><a href="http://FreeHTML5.co">FreeHTML5.co</a></li>
-->
						</ul>
					</div>
					
				</div>
				<div class="col-md-5 animate-box">
					<h3>Get In Touch</h3>
					<div class="contact_status"></div>
					<form id="contact_form" name="contact_form" method="post">
						<div class="row form-group">
							<div class="col-md-12">
								<!-- <label for="fname">First Name</label> -->
								<input type="text" id="contact_name" name="contact_name" class="form-control" placeholder="Your Name">
							</div>
						</div>

						<div class="row form-group">
							<div class="col-md-12">
								<!-- <label for="email">Email</label> -->
								<input type="text" id="contact_email" name="contact_email" class="form-control" placeholder="Your email address">
							</div>
						</div>

						<div class="row form-group">
							<div class="col-md-12">
								<!-- <label for="subject">Subject</label> -->
								<input type="text" id="contact_phone" name="contact_phone" class="form-control" placeholder="Your Mobile Number">
							</div>
						</div>

						<div class="row form-group">
							<div class="col-md-12">
								<!-- <label for="message">Message</label> -->
								<textarea name="contact_message" id="contact_message" cols="30" rows="6" class="form-control" placeholder="Say something about us"></textarea>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-12">
                            	<div class="g-recaptcha" id="g-recaptcha"></div>
                            	<input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">
							</div>
						</div>
						<div class="form-group">
							<input type="submit" name="contact_submit" id="contact_submit" value="Send Message" class="btn btn-primary">
						</div>

					</form>		
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
