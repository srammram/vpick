<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>About us</title>
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
                                <li class="active">About Us</li>
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
                            <div class="desc">
                                <h2 class="text-left">About Us</h2>
                                <p>Developed by SRAM MRAM Group, Vpick will surpass being a trusted brand, becoming an evangelist for seamless in-land freight operations, changing the face of logistics industry with its innovative technology.</p>
                               <p>Vpick aims to make customers feel as good as owning their own vehicle, with responsive mobile app &24x7 call-centre. </p>
                               <p>Vpick will provide doorstep pick-up & delivery for all ranges of B2B and B2C customers. Transporting goods in-land both intercity and intracity.  </p>
                               <p>Vpick will build affinity with its target audiences viz., Drivers, Fleet Operators, Customers acrossMalaysia, India, Cambodia, Bangladesh Cyprus, Czech Republic, Malta, Cyprus, Switzerland, Austria, New Zealand and Bahrain. </p>
                                <ul>
                                    <li>Vpick :Mobile APP – for Android & IOS Platforms</li>
                                    <li>In-Land Freight Management Service – Intracity& Intercity </li>
                                    <li>DoorStep Pick-up & Delivery of Goods – listing all freight management services under one roof. </li>
                                    <li>Payment – Vpick Wallet - Convenient, Transparent, Quick</li>
                                    <li>GPS Enabled mobile app – Realtime tracking of customer and vehicle.</li>
                                    <li>CRM – Ratings of Customers & Drivers, Creating healthy competition between Fleet operators </li>
                                    <li>ROI – Helps Customers and Fleet Operators engage maximum results for the cost. </li>
                                    <li>Manage Global Presence - 5 countries </li>
                                    <li>Unlimited Fleet :Vpick - Does not own or operate its own fleet, but aggregates operators of all range, also single vehicle owners.
                                    	<ul>
                                    		<li>5000+ Vehicles (Malaysia, India, Cambodia, Bangladesh, Cyprus & Czech Republic) </li>
                                    		<li>2000+ Vehicles (Malta, Cyprus, Switzerland, Austria, New Zealand & Bahrain)</li>
                                    	</ul>
                                    
                                    </li>
                                   
                                </ul>
                            </div>
                            <div class="desc">
                                <h3>Vpick - Integrated Strategic Approach – Freight Management </h3>
                                <p>Vpick will aggregate all divisions of freight management under one roof. Seamlessly operating all range of vehicles, also single vehicle owners with its GPS enabled mobile app – real-time tracking of customer, vehicle. This will help customers to book vehicles in vicinity. It will come handy for drivers to locate and navigate to customers with ease. </p>
                                <p>Vpick is backed by an exclusive global portfolio – in-land freight management across 5 countries, both intercity and intracity operations. Its innovative technology aided service will avail cost-effective technology solution - easy to integrate with existing business – B2B & B2C. </p> 
                                
                            </div>
							<div class="desc">
								<ul>
									<li>Countries of Operations
										<p>Malaysia, India, Cambodia, Bangladesh Cyprus, Czech Republic, Malta, Cyprus, Switzerland, Austria, New Zealand and Bahrain. </p>
									</li>
									<li>Individual Customers
										<ul>
											<li><b>Families</b> – Occasional</li>
											<li><b>Working Men & Women </b> – Erratic/Last-Minute</li>
											<li><b>Young Adults </b> – In-Sync with Digital Media – Influencing decision makers with facts/info</li>
										</ul>
									</li>
									<li>Fleet Operators 
										<ul>
											<li><b>B2B </b> – Occasional</li>
											<li><b>B2C </b> Residential / Individual Goods Transport</li>
										</ul>
									</li>
									<li>Single Vehicle Owners
										<ul>
											<li>Trained Professionals with Own Vehicle - limited investments in marketing and promotions</li>
										</ul>
									</li>
									<li>Drivers
										<ul>
											<li>Trained Professionals – Execute Trips with respective payments</li>
										</ul>
									</li>
									<li>Packers
										<ul>
											<li>Trained Resources – segregating, packing and moving goods on and off the loading vehicle. </li>
										</ul>
									</li>
								</ul>
								<p>For business enquiries, you can write to us at sales@heyycab.com. Our team will get in touch with you and help you grow your business</p>
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

