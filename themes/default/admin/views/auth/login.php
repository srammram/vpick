<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?=admin_url()?>'; }</script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>images/favicon.ico"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
    <link href="<?= $assets ?>styles/helpers/login.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/respond.min.js"></script>
    <![endif]-->

</head>

<body class="login-page">
    <noscript>
        <div class="global-site-notice noscript">
            <div class="notice-inner">
                <p>
                    <strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                    your browser to utilize the functionality of this website.
                </p>
            </div>
        </div>
    </noscript>
    <div class="page-back">
        

        <div id="login">
        		<ul class="login_lang_s">
        			<li class="dropdown hidden-xs">
                        <a class="btn btn-default tip" title="<?= lang('language') ?>" data-placement="bottom" data-toggle="dropdown"
                           href="#">
                            <img src="<?= base_url('assets/images/' . $Settings->user_language . '.png'); ?>" alt="">
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <?php $scanned_lang_dir = array_map(function ($path) {
                                return basename($path);
                            }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
							//print_r($scanned_lang_dir);
                            foreach ($scanned_lang_dir as $entry) { 
							//if($entry == 'english' || $entry == 'khmer'){
								?>
                                <li>
                                    <a href="<?= admin_url('auth/language/' . $entry); ?>">
                                        <img src="<?= base_url('assets/images/'.$entry.'.png'); ?>" class="language-img">
                                        &nbsp;&nbsp;<?= ucwords($entry); ?>
                                    </a>
                                </li>
                            <?php //} 
							} ?>
                            
                        </ul>
                    </li>
        		</ul>
               	

            <div class=" container">

                <div class="login-form-div">
                    <div class="login-content">
                        <div class="text-center">
           
                            <?php if ($Settings->logo2) {
                                echo '<img src="' . base_url('assets/uploads/logo/login_logo.png') . '" alt="' . $Settings->site_name . '"  />';
                            } ?>
                        </div>
                        <?php if ($Settings->mmode) { ?>
                            <div class="alert alert-warning">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= lang('site_offline') ?>
                            </div>
                            <?php
                        }
                        if ($error) {
                            ?>
                            <div class="alert alert-danger">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <ul class="list-group"><?= $error; ?></ul>
                            </div>
                            <?php
                        }
                        if ($message) {
                            ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <ul class="list-group"><?= $message; ?></ul>
                            </div>
                            <?php
                        }
                        ?>
                        <?php echo admin_form_open("auth/login", 'class="login" data-toggle="validator"'); ?>
                        <div class="div-title col-sm-12" style="text-align: center;">
                            <h3>
                            
                           <?= lang('login_to_your_account') ?>
                            </h3>
                        </div>
                        <div class="col-sm-12 login_sec_s">
                        	<div class="textbox-wrap form-group">
                            	<div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-users" aria-hidden="true"></i></span>
                                    <select required class="form-control select" name="group_id" id="group_id">
                                    	<option value="">Select Group</option>
                                        <?php
										foreach($groups as $group){
										?>
                                        <option value="<?= $group->id ?>"><?= $group->name ?></option>
                                        <?php
										}
										?>
                                    </select>
                                    
                                </div>
                              </div>
                              <div class="textbox-wrap form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-flag-o" aria-hidden="true"></i></span>
                                    <select required class="form-control select" name="country_code" id="country_code">
                                    	<option value="">Select Phone code</option>
                                        <?php
										foreach($countrys as $country){
										?>
                                        <option value="<?= $country->phonecode ?>"><?= '(+'.$country->phonecode.') '.$country->name ?></option>
                                        <?php
										}
										?>
                                    </select>
                                    
                                </div>
                            </div>
                            
                            <div class="textbox-wrap form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-mobile" aria-hidden="true"></i></span>
                                    <input type="text" value="" required="required" class="form-control" name="identity"
                                    placeholder="<?php /*?><?= lang('mobile') ?><?php */?> Enter your Mobile no"/>
                                </div>
                            </div>
                            <div class="textbox-wrap form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                                    <input type="password" value="" required="required" class="form-control " name="password"
                                    placeholder="<?php /*?><?= lang('pw') ?><?php */?> Enter your Password"/>
                                </div>
                            </div>
                        </div>
                      

                        <div class="form-action col-sm-12">
                           <?php /*<div class="checkbox pull-left">
                                <div class="custom-checkbox">
                                    <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"'); ?>
                                </div>
                                <span class="checkbox-text pull-left"><label for="remember"><?= lang('remember_me') ?></label></span>
                            </div>*/?>
                            <button type="submit" class="btn btn-success center-block"><?= lang('login') ?> &nbsp; </button>
                        </div>
                        <?php echo form_close(); ?>
                        <div class="clearfix"></div>
                    </div>
					
                    <!--<div class="login-form-links link2">
                        <h4 class="text-danger"><a href="#forgot_password" class="text-danger forgot_password_link"><?= lang('forgot_your_password') ?></a></h4>
                        
                    </div>-->
                    
                </div>
            </div>
        </div>
		        <div id="forgot_password" style="display: none;">
            <div class=" container">
                <div class="login-form-div">
                    <div class="login-content">
                        <?php
                        if ($error) {
                            ?>
                            <div class="alert alert-danger">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <ul class="list-group"><?= $error; ?></ul>
                            </div>
                            <?php
                        }
                        if ($message) {
                            ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <ul class="list-group"><?= $message; ?></ul>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="div-title col-sm-12">
                            <h3 class="text-primary"><?= lang('forgot_password') ?></h3>
                        </div>
                        <?php echo admin_form_open("auth/forgot_password", 'class="login" data-toggle="validator"'); ?>
                        <div class="col-sm-12">
                            <p>
                                <?= lang('type_email_to_reset'); ?>
                            </p>
                            <div class="textbox-wrap form-group">
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-envelope"></i></span>
                                    <input type="email" name="forgot_email" class="form-control "
                                    placeholder="<?= lang('email_address') ?>" required="required"/>
                                </div>
                            </div>
                            <div class="form-action">
                                <a class="btn btn-success pull-left login_link" href="#login">
                                    <i class="fa fa-chevron-left"></i> <?= lang('back') ?>
                                </a>
                                <button type="submit" class="btn btn-primary pull-right">
                                    <?= lang('submit') ?> &nbsp;&nbsp; <i class="fa fa-envelope"></i>
                                </button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        
        <?php
       // if ($Settings->allow_reg) {
            ?>
            <div id="register">
                <div class="container">
                    <div class="registration-form-div reg-content">
                        <?php echo admin_form_open("auth/register", 'class="login" data-toggle="validator"'); ?>
                        <div class="div-title col-sm-12">
                            <h3 class="text-primary"><?= lang('register_account_heading') ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('first_name', 'first_name'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-user"></i></span>
                                    <input type="text" name="first_name" class="form-control " placeholder="<?= lang('first_name') ?>" required="required"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('last_name', 'last_name'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-user"></i></span>
                                    <input type="text" name="last_name" class="form-control " placeholder="<?= lang('last_name') ?>" required="required"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('company', 'company'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-building"></i></span>
                                    <input type="text" name="company" class="form-control " placeholder="<?= lang('company') ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('phone', 'phone'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-phone-square"></i></span>
                                    <input type="text" name="phone" class="form-control " placeholder="<?= lang('phone') ?>" required="required"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('username', 'username'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-user"></i></span>
                                    <input type="text" name="username" class="form-control " placeholder="<?= lang('username') ?>" required="required"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('email', 'email'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control " placeholder="<?= lang('email_address') ?>" required="required"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?php echo lang('password', 'password1'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-key"></i></span>
                                    <?php echo form_password('password', '', 'class="form-control tip" id="password1" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" data-bv-regexp-message="'.lang('pasword_hint').'"'); ?>
                                </div>
                                <span class="help-block"><?= lang('pasword_hint') ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?php echo lang('confirm_password', 'confirm_password'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon "><i class="fa fa-key"></i></span>
                                    <?php echo form_password('confirm_password', '', 'class="form-control" id="confirm_password" required="required" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <a href="#login" class="btn btn-success pull-left login_link">
                                <i class="fa fa-chevron-left"></i> <?= lang('back') ?>
                            </a>
                            <button type="submit" class="btn btn-primary pull-right">
                                <?= lang('register_now') ?> <i class="fa fa-user"></i>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        <?php
       // }
        ?>
    </div>

    <script src="<?= $assets ?>js/jquery.js"></script>
    <script src="<?= $assets ?>js/bootstrap.min.js"></script>
    <script src="<?= $assets ?>js/jquery.cookie.js"></script>
    <script src="<?= $assets ?>js/login.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script>
		$(document).ready(function() {
    $('#country_code').select2();
});
		</script> 
		<script>
		$(document).ready(function() {
    $('#group_id').select2();
});
		</script>
    <script type="text/javascript">
        $(document).ready(function () {
            localStorage.clear();
            var hash = window.location.hash;
            if (hash && hash != '') {
                $("#login").hide();
                $(hash).show();
            }
			
			
        });
		
    </script>
    
</body>
</html>
