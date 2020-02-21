<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VPick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>frontend/images/favicon.ico"/>
    
    <?php $this->load->view($this->theme . 'frontend_css'); ?>
    <style>
		.fh5co-cover{
			background:url(<?= $assets ?>/frontend/images/banner_car.png) #efb71c;
		}
	</style>
</head>

<body class="login-page ">
    <div class="fh5co-loader"></div>
    <div id="page">
    	<?php $this->load->view($this->theme . 'frontend_header'); ?>
        <header id="fh5co-header" class="fh5co-cover" role="banner">
            
            <div class="container">
                <div class="row">
                </div>
            </div>
        </header>
        
        <div id="fh5co-services" class="fh5co-bg-section">

		<div class="container">

			<div class="row">

				<div class="col-md-12 col-sm-12 col-xs-12 text-center">

					<div class="feature-center animate-box" data-animate-effect="fadeIn">

						<span class="chains">

							<img src="<?= $assets ?>frontend/images/chains.png" alt="chains">

						</span>

						<h3>About VP<span style="text-transform: lowercase">ick</span></h3>

						<p>Developed by SRAM MRAM Group, Vpick will surpass being a trusted brand, becoming an evangelist for seamless in-land freight operations, changing the face of logistics industry with its innovative technology.Vpick aims to make customers feel as good as owning their own vehicle, with responsive mobile app &24x7 call-centre.Vpick will provide doorstep pick-up & delivery for all ranges of B2B and B2C customers. Transporting goods in-land both intercity and intracity.Vpick will build affinity with its target audiences viz., Drivers, Fleet Operators, Customers acrossMalaysia, India, Cambodia, Bangladesh Cyprus, Czech Republic, Malta, Cyprus, Switzerland, Austria, New Zealand and Bahrain.  </p>

					</div>

				</div>

			</div>

		</div>

	</div>

	

	<div id="fh5co-project" class="taxi_car_cab">

		<div class="container-fluid">

			<div class="row">

				<div class="col-md-8 col-sm-8 col-sm-12 fh5co-project animate-box" data-animate-effect="fadeIn">

					<img src="<?= $assets ?>frontend/images/cab/cab1.jpg" alt="Taxi Cab" class="img-responsive service_cab_img">

				</div>

				<div class="col-md-4 col-sm-6 fh5co-project animate-box" data-animate-effect="fadeIn">

					<div class="col-sm-12 inner_se">

						<h3>CITY DRIVE</h3>

						<p>Now it’s easy to plan your routine day to day travel. VPick is luxury at your fingertips round the clock. We are here to drive you to your destination on-time every-time. Book in an instant, choose the cab of your choice and enjoy the affordable comfortable ride.</p></div>

						<img src="<?= $assets ?>frontend/images/cab/city_taxi2.jpg" alt="taxi cab" class="img-responsive service_cab_img">

				</div>

			</div>

			<div class="row">

				<div class="col-md-4 col-sm-6 fh5co-project animate-box" data-animate-effect="fadeIn">

					<div class="col-sm-12 inner_se sec_inner">

						<span><img src="<?= $assets ?>frontend/images/cab/outstation_icon.jpg" class="center-block"></span>

						<h3 class="text-center">OUTSTATION</h3>

						<p>Intercity travel made simple. Our travel partners ensure your one-way or two-way round about trips and tours with family & friends is filled with memories. VPick builds life long memories and becomes a vital part of your travelogues.</p></div>

						<img src="<?= $assets ?>frontend/images/cab/outstation3.jpg" alt="taxi cab" class="img-responsive service_cab_img">

				</div>

				<div class="col-md-8 col-sm-8 col-sm-12 fh5co-project animate-box" data-animate-effect="fadeIn">

					<img src="<?= $assets ?>frontend/images/cab/outstation4.jpg" alt="Taxi Cab" class="img-responsive service_cab_img">

				</div>

			</div>

			<div class="row">

				<div class="col-md-4 col-sm-4 col-sm-12 fh5co-project animate-box" data-animate-effect="fadeIn">

					<img src="<?= $assets ?>frontend/images/cab/rental1.jpg" alt="Taxi Cab" class="img-responsive service_cab_img">

				</div>

				<div class="col-md-4 col-sm-6 fh5co-project animate-box" data-animate-effect="fadeIn">

					<div class="col-sm-12 inner_se thi_inner">

						<span><img src="<?= $assets ?>frontend/images/cab/rental_icon.jpg" class="center-block"></span>

						<h3 class="text-center">RENTALS</h3>

						<p>Plan your last minute travel itinerary with poise. Book a cab that suits your business needs or the long pending family outing. Customer-friendly Professional Drivers and Comfortable Cabs at Pocket friendly rates. VPick makes your travel hassle-free. </p></div>

						<img src="<?= $assets ?>frontend/images/cab/rental2.jpg" alt="taxi cab" class="img-responsive service_cab_img">

				</div>

				<div class="col-md-4 col-sm-4 col-sm-12 fh5co-project animate-box" data-animate-effect="fadeIn">

					<img src="<?= $assets ?>frontend/images/cab/rental3.jpg" alt="Taxi Cab" class="img-responsive service_cab_img">

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

	

	<div id="fh5co-started">

		<div class="container-fluid">

			<div class="row animate-box">

				<div class="col-md-12 text-center">

					<img src="<?= $assets ?>frontend/images/cab/booking_banner.jpg" class="img-responsive" style="width: 100%;" alt="booking Banner">

				</div>

			</div>

			<div class="row animate-box booking_btn_s">

				<div class="col-md-12">

					<form class="form-inline">

						<div class="col-md-6 col-md-offset-3 col-sm-6 text-center">

							<a href="book_ride.php"><button type="button" class="btn btn-default">BOOK NOW</button></a>

						</div>

					</form>

				</div>

			</div>

			<div class="row">

				<div class="col-sm-12 col-xs-12 text-center subscription_email">

					<img src="<?= $assets ?>frontend/images/yellow_logo.png" alt="yellow logo">

					<div class="contact_status"></div>

					<form name="contact-form" method="post" id="contact-form">

						<div class="input-group col-sm-4 col-sm-offset-4 col-xs-12  text-center">

							<input class="form-control" name="email" id="email" type="email" placeholder="Enter Your Email" required>

							 <div class="input-group-append">

								 <button class="btn btn-info btn-sm" id="submit_contact" name="submit_contact" type="submit">GO</button>

							 </div>

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
