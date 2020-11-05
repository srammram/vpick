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
                                <h2 class="text-left">News</h2>
                                <p>Heyycab, an online e-hailing platform developed by SRAM & MRAM Group, a global conglomerate with 8 international alliances, 10 companies, spanning 5 continents and 35+ locations, 300+ employees with a sales turnaround of USD 800 Million (FY 2017-2018) specifically for the global community! 
    We are a fast growing e-hailing service aggregator pioneering in B2B & B2C services, delivering seamless cab/taxi booking for city, outstation and hourly rentals. Our verified business partners list their vehicles and trusted drivers for our customer’s delight. More than 1 Lakh cabs waiting to give the best travel experience to the international traveller. GPS enabled rides with SOS & 24x7 support features.</p>
                                <p>HeyyCab, a journey of innovation started for you, our loyal customers, delivering the much awaited Trustworthy, Safe and Transparent transport solutions for all segments of B2B and B2C clientele across the globe.</p> 
                                <p>Heyycab is a fast growing e-hailing service aggregator pioneering in Business-to-Business (B2B)& Business-to-Customer (B2C) services, delivering seamless cab/taxi booking for city, outstation and hourly rentals. We are the host of handpicked and verified business partners, listing their comfortable vehicles and trusted drivers to ensure customer’s delight. We have listed more than 1 Lakh cabs to give the best uninterrupted travel experience to both B2B and B2C customers. </p>
                                <p>Hooked onto travelogues of globe trotters and driven by commitment to provide seamless mobility of a billions. Empowering self-made entrepreneurs who are tirelessly driving the nation ahead. Yielding comfort to customers with city and outstation rides. Yardmaster of taxi fleets greeting you on time and each time. Connecting cities and hearts of customers alike. App that is ahead of time, technology for everyone, anywhere. Building a brand that’s all about loyal customers and reliable partner base.  </p>
                                <ul>
                                    <li>Made in India for the global community on the go!  </li>
                                    <li>Easy cab booking, round the clock, on-time every-time</li>
                                    <li>Taxi booking made simple and affordable</li>
                                    <li>Pocket-Friendly & Flexible Payment Mode</li>
                                    <li>Travel with the comfort of transparent rides led by technology </li>
                                    <li>100% automated GPS GPRS based booking system</li>
                                    <li>Accurate Trip tracker service </li>
                                    <li>SOS alert in mobile app</li>
                                </ul>
                            </div>
                            <div class="desc">
                                <h3>Brains Behind HeyyCab!</h3>
                                <p>We at Master tour organisers pvt ltd, since 1995 have been offering international and domestic travel services to customers and reputed business associates across the globe. We are familiar with the benchmark, diverse and global market expectation of travel industry. Futuristic technology connecting people and cities, HeyyCab, launched first in Pune, is an 100% authentic amalgamation of happy clients and committed partners. </p> 
                                
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

