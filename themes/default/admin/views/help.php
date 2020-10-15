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
		overflow-y:auto !important;
	}
	</style>

<style>
	.head_sec_h2{font-size: 20px;background-color: #F2B818;margin: 0px;padding: 15px;color: #fff;margin-bottom: 15px;
	}
	.head_sec_h3{font-size: 18px;margin-bottom: 15px;color: #999;}.head_sec_h3 b{color: #333;}
	p{color: #999;}
	.form-control{padding-left: 0px;}
	.btn-file {
  position: relative;
  overflow: hidden;
}
.btn-file input[type=file] {
  position: absolute;
  top: 0;
  right: 0;
  min-width: 100%;
  min-height: 100%;
  font-size: 100px;
  text-align: right;
  filter: alpha(opacity=0);
  opacity: 0;
  cursor: inherit;
  display: block;
}
	.login-page .btn{background-color: #F2B818;border-color: #F2B818;border-radius: 0px!important;border-bottom: none;}
.file-input-label {
	padding: 0px 10px;
	display: table-cell;
	vertical-align: middle;
  border: none;
  border-radius: 4px;
}
input[readonly] {
  background-color: white !important;
  cursor: text !important;
}
	.btn_file_group{border-bottom: 1px solid #898a8a;padding-bottom: 5px;}
/*	*/
	/* The radio */
.radio {
 
     display: block;
    position: relative;
    padding-left: 30px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 15px;
	margin-right: 15px;
	color: #999;font-weight: normal;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none
}

/* Hide the browser's default radio button */
.radio input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

/* Create a custom radio button */
.checkround {

    position: absolute;
    top: 0px;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #fff ;
    border-color:#999;
    border-style:solid;
    border-width:2px;
     border-radius: 50%;
}


/* When the radio button is checked, add a blue background */
.radio input:checked ~ .checkround {
    background-color: #fff;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkround:after {
    content: "";
    position: absolute;
    display: none;
}

/* Show the indicator (dot/circle) when checked */
.radio input:checked ~ .checkround:after {
    display: block;
}

/* Style the indicator (dot/circle) */
.radio .checkround:after {
     left: 2px;
    top: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background:#006635;
    
 
}

/* The check */
.check {
    display: block;
    position: relative;
    padding-left: 25px;
    margin-bottom: 12px;
    padding-right: 15px;
    cursor: pointer;
    font-size: 15px;
	margin-right: 15px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Hide the browser's default checkbox */
.check input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

/* Create a custom checkbox */
.checkmark_s {
    position: absolute;
    top:0px;
    left: 0;
    height: 18px;
    width: 18px;
    background-color: #fff ;
    border-color:#999;
    border-style:solid;
    border-width:2px;
}
/* When the checkbox is checked, add a blue background */
.check input:checked ~ .checkmark_s {
    background-color: #006635  ;border-color: #006635;
}

/* Create the checkmark_s/indicator (hidden when not checked) */
.checkmark_s:after {
    content: "";
    position: absolute;
    display: none;
}

/* Show the checkmark_s when checked */
.check input:checked ~ .checkmark_s:after {
    display: block;
}

/* Style the checkmark_s/indicator */
.check .checkmark_s:after {
    left: 5px;
    top: 1px;
    width: 5px;
    height: 10px;
    border: solid ;
    border-color:#000;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}

.cust-btn{
	margin-bottom: 10px;
	background-color: #006635;
	border-width: 2px;
	border-color: #006635;
	color: #fff;
}
.cust-btn:hover{
	
	border-color: #006635;
	background-color: #fff;
	color: #006635;
	border-radius: 20px;
	transform-style: 2s;

}
	.radio_btn_se{margin: 30px 0px 15px;}
	.enquiry_details .control-label{font-weight: 400;}
	.enquiry_details .form-control{font-size: 18px;font-weight:600;color: #333;padding-bottom: 0px;}
	.submit_col{color: #333!important;}
</style>
</head>

<body class="login-page " style="margin-top:30px;">
<section class="enquiry_details">
    	<div class="container-fluid">
    		<div class="row">
    			<div class="col-sm-12 col-xs-12">
    				
    				<form method="post" name="create_ticket" action="<?= site_url('main/help_form') ?>" id="create_ticket" enctype="multipart/form-data" role="form" autocomplete="off">
                    
						<div class="form-group" style="border-bottom: 1px solid #898a8a;">
							<h3 class="head_sec_h3"><b>Name :</b> <?= $customer_details->first_name ?> <?= $customer_details->booking_no ? '-'.$customer_details->booking_no : ''; ?></h3>
							<p><?= $help['sub'] ?></p>
						</div>
                        <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
                        <input type="hidden" name="ride_id" value="<?= $ride_id ?>">
                        <input type="hidden" name="help_department" id="help_department" value="<?= $help_id ?>">
                        <input type="hidden" name="help_main_id" id="help_main_id" value="<?= $help_main_id ?>">
                        <input type="hidden" name="help_sub_id" id="help_sub_id" value="<?= $help_sub_id ?>">
                        
                        <?php
						foreach($help['form'] as $key => $val){
						?>
						
						<div class="form-group">
							<label class="control-label"><?= $val->name ?></label>
							<?php
							if($val->form_type == 1){
							?>
                            <input type="text" name="<?= $val->form_name ?>" value="" class="form-control" required="required">
                            <?php
							}elseif($val->form_type == 2){
							?>
                            <textarea name="<?= $val->form_name ?>" class="form-control" required></textarea>
                            <?php
							}elseif($val->form_type == 4){
							?>
                            <input type="text" name="<?= $val->form_name ?>" value="" class="form-control date" required="required">
                            <?php
							}elseif($val->form_type == 3){
							?>
                            <div class="input-group btn_file_group">
							  <span class="input-group-btn">
								<label class="btn btn-primary btn-file" for="multiple_input_group">
								  <div class="input required"><input id="multiple_input_group" name="<?= $val->form_name ?>" type="file" multiple></div> Choose Image
								</label>
							  </span>
							  <span class="file-input-label"></span>
							</div>
                            <?php
							}
						}
							?>
                            <!--<select class="form-control">
								<option>Select Country</option>
								<option>Tamilnadu</option>
								<option>Telungana</option>
							</select>-->
						</div>
						<!--<div class="form-group">
							<label class="control-label">Drop Down</label>
							<input type="text" class="form-control" value="Telungana">
						</div>
						<div class="form-goup">
							<label class="control-label">Image Upload</label>
							 <div class="input-group btn_file_group">
							  <span class="input-group-btn">
								<label class="btn btn-primary btn-file" for="multiple_input_group">
								  <div class="input required"><input id="multiple_input_group" type="file" multiple></div> Choose Image
								</label>
							  </span>
							  <span class="file-input-label"></span>
							</div>
						</div>
						<div class="form-group">
							<h3 class="radio_btn_se"><b>Radio Button</b></h3>
							<label class="radio">Company
							  <input type="radio" checked="checked" name="is_company">
							  <span class="checkround"></span>
							</label>
							<label class="radio">Company
							  <input type="radio" name="is_company">
							  <span class="checkround"></span>
							</label>
						</div>
						<div class="form-group">
						<h3 class="radio_btn_se"><b>Check Box</b></h3>
							<label class="check ">Select
								<input type="checkbox" checked="checked" name="is_name">
							  	<span class="checkmark_s"></span>
							</label>
							<label class="check ">Unselect
								<input type="checkbox"  name="is_name">
							  <span class="checkmark_s"></span>
							</label>
						</div>-->
						<div class="form-group">
							<button type="submit" class="btn btn-primary submit_col center-block">SUBMIT</button>
						</div>
					</form>
    			</div>
    		</div>
    	</div>
    </section>

    
    
    
<script src="<?= $assets ?>js/jquery.js"></script>
<script src="<?= $assets ?>js/bootstrap.min.js"></script>
<script>
	$(document).on('change', '.btn-file :file', function() {
  var input = $(this),
      numFiles = input.get(0).files ? input.get(0).files.length : 1,
      label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
  input.trigger('fileselect', [numFiles, label]);
});

$(document).ready( function() {
	$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
		console.log("teste");
		var input_label = $(this).closest('.input-group').find('.file-input-label'),
			log = numFiles > 1 ? numFiles + ' files selected' : label;

		if( input_label.length ) {
			input_label.text(log);
		} else {
			if( log ) alert(log);
		}
	});
});
	</script>
</body>
</html>
