<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/add_customer", $attrib);
                ?>
                <div class="row">
                <div class="instance_country col-sm-12">
                <div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        </div>
					<div class="col-md-12">  
						<h2 class="box_he_de"><?= lang('login_details') ?></h2>  
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('password', 'password'); ?>
							<div class="controls">
								<?php echo form_password('password', '', 'class="form-control tip" id="password" required="required" '); ?>
							</div>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('confirm_password', 'confirm_password'); ?>
							<div class="controls">
								<?php echo form_password('confirm_password', '', 'class="form-control" id="confirm_password" required="required" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<h2 class="box_he_de"><?= lang('user_details') ?></h2> 
                        <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('reference_no', 'reference_no'); ?>
							<div class="controls">
								<input type="text" id="reference_no" name="reference_no" class="form-control" />
							</div>
						</div>
                         
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('first_name', 'first_name'); ?>
							<div class="controls">
								<input type="text" id="first_name" onkeyup="inputFirstUpper(this)" name="first_name" class="form-control" required="required"/>
							</div>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('last_name', 'last_name'); ?>
							<div class="controls">
								<input type="text" id="last_name" onkeyup="inputFirstUpper(this)" name="last_name" class="form-control"/>
							</div>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('email', 'email'); ?>
							<div class="controls">
								<input type="text" id="email" name="email" class="form-control"/>
							</div>
							<p class="help-block-small"><?= lang('ex_email') ?></p>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('country_code', 'country_code'); ?>
							<?php
							$cc[''] = 'Select Country Code';
							foreach ($country_code as $cc_row) {
								$cc[$cc_row->phonecode] = '(+'.$cc_row->phonecode.') '.$cc_row->name;
							}

							echo form_dropdown('country_code', $cc, $commoncountry->phonecode, 'class="tip form-control" id="country_code" data-placeholder="' . lang("select") . ' ' . lang("country_code") . '" required="required"');
							?>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('mobile', 'mobile'); ?>
							<div class="controls">
								<input type="text" id="mobile" name="mobile" class="form-control" required="required"/>
							</div>
						</div>


						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('gender', 'gender'); ?>
							<?php
							$ge[''] = array('male' => lang('male'), 'female' => lang('female'), 'others' => lang('others'));
							echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : ''), 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
							?>
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('dob', 'dob'); ?>
							<div class="controls">
								<input type="text" id="dob" name="dob"  onkeypress="dateCheck(this);" class="form-control" required="required"/>
							</div>
							<p class="help-block-small"><?= lang('ex_date') ?></p>
						</div>

						<div class="form-group all col-sm-3 col-xs-12">
							<?= lang("photo", "photo") ?>
							<input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false" data-show-preview="false" class="form-control file" accept="im/*">
							<p class="help-block-small"><?= lang('image_size') ?></p>
						</div>

					</div>
					<div class="col-md-12">
						<h2 class="box_he_de">Address</h2>  
						<div class="form-group all col-sm-3 col-xs-12">
							<?= lang("address_doc", "local_image") ?>
							<input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
								   data-show-preview="false" class="form-control file" accept="im/*">
						</div>

						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('address', 'local_address'); ?>
							<div class="controls">
								<input type="text" id="local_address" onkeyup="inputUpper(this)" name="local_address" class="form-control"/>
							</div>
						</div>
                        <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('pincode', 'local_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="local_pincode"  name="local_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
					</div>
                </div>

                <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_customer', lang('add_customer'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	$('.is_country').change(function(){
		
		var site = '<?php echo site_url() ?>';
		var id = '<?= $id ?>';
		var is_country = $(this).val();
		window.location.href = site+"admin/people/add_customer/?is_country="+is_country;
		
	});
});
</script>