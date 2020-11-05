<?php

		preg_match("/[^\/]+$/", basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), $matches);

		$last_word = $matches[0]; 
		//echo $_SERVER['REQUEST_URI'];

	?>

	<nav class="fh5co-nav" role="navigation">

		<div class="container">

			<div class="row">

				<div class="left-menu text-right menu-1">

					<ul>

						<li <?php if($last_word == 'index'){ ?> class="active" <?php } ?>><a href="<?= site_url(); ?>">HOME</a></li>

						<li <?php if($last_word == 'aboutus'){ ?> class="active" <?php } ?>><a href="<?= site_url('aboutus'); ?>" >ABOUT US</a></li>

						

						<li class="has-dropdown">

							<a href="javascript:void(0)">PARTNER WITH US</a>

							<ul class="dropdown">

								<li><a href="<?= site_url('drivewithus'); ?>">Drive with us</a></li>

								<li><a href="<?= site_url('franchisee'); ?>">Become a Franchisee </a></li>

							</ul>

						</li>

					</ul>

				</div>

				<div class="logo text-center">

					<div id="fh5co-logo"><a href="<?= site_url(); ?>"><img src="<?= $assets ?>frontend/images/logo.png" alt="logo"></a></div>

				</div>

				<div class="right-menu text-left menu-1">

					<ul>

						<li <?php if($last_word == 'book_ride'){ ?> class="active" <?php } ?>><a href="<?= site_url('book_ride'); ?>">BOOK A RIDE</a></li>

						<li <?php if($last_word == 'faq'){ ?> class="active" <?php } ?>><a href="<?= site_url('faq'); ?>" >FAQ</a></li>

						<li <?php if($last_word == 'contact'){ ?> class="active" <?php } ?>><a href="<?= site_url('contact'); ?>" >CONTACT</a></li>

						<li class="has-dropdown login_s">

							<a href="javascript:void(0)"><i class="fa fa-user"></i> LOGIN</a>

							<ul class="dropdown">

								<li><a href="<?= site_url(); ?>/admin/login?group=5">Customer Login</a></li>

								<li><a href="<?= site_url(); ?>/admin/login?group=4">Driver Login</a></li>

							</ul>

						</li>

					</ul>

				</div>

				

			</div>

			

		</div>

	</nav>



	





