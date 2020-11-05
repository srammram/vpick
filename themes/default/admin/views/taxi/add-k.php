<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('add_driver'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("drivers/add", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <div class="form-group">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <?php echo form_input('first_name', '', 'class="form-control" id="first_name" required="required" pattern=".{3,10}"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <?php echo form_input('last_name', '', 'class="form-control" id="last_name" '); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = array('male' => lang('male'), 'female' => lang('female'));
                                echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : ''), 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
                                ?>
                            </div>
                            <div class="form-group">
                                <?php echo lang('dob', 'dob'); ?>
                                <div class="controls">
                                    <?php echo form_input('dob', '', 'class="form-control" id="dob" required="required"'); ?>
   
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('contact_number', 'contact_number'); ?>
                                <div class="controls">
                                    <?php echo form_input('contact_number', '', 'class="form-control" id="phone" required="required" maxlength="15" onkeydown="return(event.which == 8 && event.which == 0 || (event.charCode >= 48 || event.which <= 57))"'); ?>
   
                                </div>
                            </div>
                             <div class="form-group">
                                <?php echo lang('alternate_number', 'alternate_number'); ?>
                                <div class="controls">
                                    <?php echo form_input('alternate_number', '', 'class="form-control" id="alternate-number" maxlength="15" onkeydown="return(event.which == 8 && event.which == 0 || (event.charCode >= 48 || event.which <= 57))"'); ?>
   
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="email" id="email" name="email" class="form-control"
                                           required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('username', 'username'); ?>
                                <div class="controls">
                                    <input type="text" id="username" name="username" class="form-control"
                                           required="required" pattern=".{4,20}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('password', 'password'); ?>
                                <div class="controls">
                                    <?php echo form_password('password', '', 'class="form-control tip" id="password" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" data-bv-regexp-message="'.lang('pasword_hint').'"'); ?>
                                    <span class="help-block"><?= lang('pasword_hint') ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('confirm_password', 'confirm_password'); ?>
                                <div class="controls">
                                    <?php echo form_password('confirm_password', '', 'class="form-control" id="confirm_password" required="required" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-5 col-md-offset-1">

                            <div class="form-group">
                                <?= lang('status', 'status'); ?>
                                <?php
                                $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="status" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>
                            <div class="form-group">
                                <?php echo lang('license_number', 'license_number'); ?>
                                <div class="controls">
                                    <?php echo form_input('license_number', '', 'class="form-control" id="license_number" '); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('license_valid_from', 'license_valid_from'); ?>
                                <div class="controls">
                                    <?php echo form_input('license_valid_from', '', 'class="form-control" id="license_valid_from" '); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('license_expiry', 'license_expiry'); ?>
                                <div class="controls">
                                    <?php echo form_input('license_expiry', '', 'class="form-control" id="license_expiry" '); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-md-offset-1">
                                <div class="form-group all">
                                <?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" accept="image/*" class="form-control file">
                            </div>
                        </div>
                        <div class="col-md-5 col-md-offset-1">
                            <div class="form-group all">
                                <?= lang("license", "license_images") ?>
                                <input id="driver_license" type="file" data-browse-label="<?= lang('browse'); ?>" name="driver_license[]" multiple data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="image/*">
                            </div>
                        </div>
                    <div id="img-details"></div>
                    </div>
                </div>

                <p><?php echo form_submit('add_driver', lang('add_driver'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8">
   $(document).ready(function(){
    $('#dob').datepicker({
		dateFormat: "dd/mm/yy" ,
        dateFormat: "yy-mm-dd" ,
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+0'    
    });
    $('#license_valid_from').datepicker({
		dateFormat: "dd/mm/yy" ,
        dateFormat: "yy-mm" ,
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+0'    
    });
    $('#license_expiry').datepicker({
		dateFormat: "dd/mm/yy" ,
        dateFormat: "yy-mm" ,
        changeMonth: true,
        changeYear: true,
        yearRange: '-0:+30'    
    });

   })
</script>
<style>
    .input-group .form-control{
        z-index:1 !important;
    }
</style>
