<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?= admin_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product->name ?> - <?= $Settings->site_name ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">
	<link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet" type="text/css"  media="screen, print" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
   
</head>
<body>
<div class="row">
    <div class="col-lg-12">
        <?php /*?><?php
        $path = base_url().'themes/default/admin/assets/images/logo.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        ?>
        <div class="text-center" style="margin-bottom:20px;">
            <img src="<?= $base64; ?>">
        </div>

        <div class="clearfix"></div>

        <div class="clearfix"></div>
<?php */?>
	<table class="invoice-wrapper" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<table class="invoice-wrapper_ct" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td align="center">
					<table class="invoice-content" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td>
							<?php
								$path = base_url().'themes/default/admin/assets/images/logo.png';
								$type = pathinfo($path, PATHINFO_EXTENSION);
								$data = file_get_contents($path);
								$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
								?>
								<div class="text-left" style="margin-bottom:20px;">
									<img src="<?= $base64; ?>">
								</div>
							</td>
							<td align="right">
								<p><b>Booking Number : <?= $rides->booking_no ?></b></p>
								<p><b>Date: <?= date('Y-m-d') ?></b></p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr style="background-color:#EFB71C;">
							<td align="center">
								<div class="box_bg">
									<h3>Heyy Cab</h3>
									<p>Thank For Using Heyy Cab</p>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table class="total_amt" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center">
								<div class="total_amt1">
									<h3>Total Amout</h3>
									<h1>$808</h1>
									<h3>Total Distance: 39.84Kms</h3>
									<h3>Total Wait Time: 43 Mins</h3>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
									<tr align="center">
										<td>
											<p>Heyy Cab Money Deducted</p>
											<p><b>$0.00</b></p>
										</td>
										<td>
											<p>Discount</p>
											<p><b>$0.00</b></p>
										</td>
										<td>
											<p>Payable Amount</p>
											<p><b>$808</b></p>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table class="breakup" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="50%">
								<h4 align="center">FARE BREAKUP</h4>
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td>Base Fare for 30kms:</td>
										<td><b>$540</b></td>
									</tr>
									<tr>
										<td>Rate for 9.64kms:</td>
										<td><b>$125.32</b></td>
									</tr>
									<tr>
										<td>Wait time Charge for 33min:</td>
										<td><b>$66.32</b></td>
									</tr>
									<tr>
										<td>Peak time charge:</td>
										<td><b>$0.0</b></td>
									</tr>
									<tr>
										<td>Toll Charges:</td>
										<td><b>$75</b></td>
									</tr>
								</table>
							</td>
							<td width="50%">
								<h4 align="center">FARE BREAKUP</h4>
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td>Total Payable Bill</td>
										<td><b>$808</b></td>
									</tr>
									<tr>
										<td colspan="2">TAX BREAKUP</td>
									</tr>
									<tr>
										<td>Service Tax</td>
										<td><b>$33.53</b></td>
									</tr>
									<tr>
										<td>Education Cess</td>
										<td><b>$0.67</b></td>
									</tr>
									<tr>
										<td>SHE Cess</td>
										<td><b>$0.34</b></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<p class="bok_de" align="center">Booking Details</p>
					<table cellpadding="0" cellspacing="0" width="100%">
						
						<tr>
							<td>Booking Date</td>
							<td><b><?= $rides->booking_timing ?></b></td>
						</tr>
						<tr>
							<td>Pickup Date</td>
							<td><b><?= $rides->ride_timing ?></b></td>
						</tr>
						<tr>
							<td>Booking Customer</td>
							<td><b><?= $rides->cfname ?> <?= $rides->clname ?></b></td>
						</tr>
					</table>
					<hr>
				</td>
			</tr>
			<tr>
				<td>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Placeat vitae sint sunt soluta eius veniam consequatur dolor fugit vel officiis, a culpa illum nesciunt quod, eveniet optio dolore tempora obcaecati.</p>
					
				</td>
			</tr>
			<tr>
				<td align="center">
					<div class="foot_bg">
						<p>NO:6 ,North Street,chennai,Tamilnadu</p>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Placeat vitae sint </p>
						<p>Tel:8778465464, Fax:022 03515156 </p>
						<p>© 2019 Heyycab. All Rights Reserved.</p>
					</div>
				</td>
			</tr>
		</table>
			</td>
		</tr>
	</table>
		

    </div>
</div>
</body>
</html>
