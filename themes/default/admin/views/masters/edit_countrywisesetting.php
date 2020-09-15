

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
            	<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("masters/edit_countrywisesetting/".$id, $attrib);
                ?>
                <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" id="is_country">
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
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('user') ?></legend>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="country_id"><?= lang("driver_ride_accept"); ?></label>
                
                            <div class="controls">
                                <?php
								$c[''] = 'Select Country';
                                if(!empty($country)){
									foreach($country as $country_row){
										$c[$country_row->id] = $country_row->name;
									}
								}
                                echo form_dropdown('country_id', $c, $setting->country_id, 'class="form-control tip" id="country_id" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="timezone"><?= lang("timezone"); ?></label>
                            <?php
							$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
							?>
                            <select name="timezone" class="form-control" id="timezone" required>
                            	<?php
								foreach($tzlist as  $val){
									if($val == $setting->timezone){
										$selected = 'selected';
									}else{
										$selected = '';
									}
								?>
                                <option value="<?php echo $val; ?>" <?= $selected ?>  ><?php echo $val; ?></option>
                                <?php
								}
								?>
                            </select>
                           
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('login_otp', 'login_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('login_otp_enable', $ge, $setting->login_otp_enable, 'class="tip form-control" id="login_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('device_change_otp', 'device_change_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('device_change_otp_enable', $ge, $setting->device_change_otp_enable, 'class="tip form-control" id="device_change_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('ride_otp', 'ride_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('ride_otp_enable', $ge, $setting->ride_otp_enable, 'class="tip form-control" id="ride_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('address', 'address_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('address_enable', $ge, $setting->address_enable, 'class="tip form-control" id="address_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('account_holder_name', 'account_holder_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('account_holder_name_enable', $ge, $setting->account_holder_name_enable, 'class="tip form-control" id="account_holder_name_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('bank_name', 'bank_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('bank_name_enable', $ge, $setting->bank_name_enable, 'class="tip form-control" id="bank_name_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('branch_name', 'branch_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('branch_name_enable', $ge, $setting->branch_name_enable, 'class="tip form-control" id="branch_name_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('ifsc_code', 'ifsc_code_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('ifsc_code_enable', $ge, $setting->ifsc_code_enable, 'class="tip form-control" id="ifsc_code_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('aadhaar', 'aadhaar_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('aadhaar_enable', $ge, $setting->aadhaar_enable, 'class="tip form-control" id="aadhaar_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('pancard', 'pancard_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('pancard_enable', $ge, $setting->pancard_enable, 'class="tip form-control" id="pancard_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('license', 'license_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('license_enable', $ge, $setting->license_enable, 'class="tip form-control" id="license_enable" ');
                                ?>
                            </div>
                    </div><div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('police', 'police_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('police_enable', $ge, $setting->police_enable, 'class="tip form-control" id="police_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('loan', 'loan_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('loan_enable', $ge, $setting->loan_enable, 'class="tip form-control" id="loan_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('vendor', 'vendor_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('vendor_enable', $ge, $setting->vendor_enable, 'class="tip form-control" id="vendor_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    
                </fieldset>
                
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?= lang('cab') ?></legend>
                    
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('cab_registration', 'cab_registration_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('cab_registration_enable', $ge, $setting->cab_registration_enable, 'class="tip form-control" id="cab_registration_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('taxation', 'taxation_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('taxation_enable', $ge, $setting->taxation_enable, 'class="tip form-control" id="taxation_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('insurance', 'insurance_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('insurance_enable', $ge, $setting->insurance_enable, 'class="tip form-control" id="insurance_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('permit', 'permit_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('permit_enable', $ge, $setting->permit_enable, 'class="tip form-control" id="permit_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('authorisation', 'authorisation_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('authorisation_enable', $ge, $setting->authorisation_enable, 'class="tip form-control" id="authorisation_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('fitness', 'fitness_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('fitness_enable', $ge, $setting->fitness_enable, 'class="tip form-control" id="fitness_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('speed', 'speed_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('speed_enable', $ge, $setting->speed_enable, 'class="tip form-control" id="speed_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('puc', 'puc_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('puc_enable', $ge, $setting->puc_enable, 'class="tip form-control" id="puc_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    
                </fieldset>
                
                
                    
                <div class="cleafix"></div>
                <div class="form-group">
                    <div class="controls">
                        <?= form_submit('update_settings', lang("update_settings"), 'class="btn btn-primary"'); ?>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>

        </div>
    </div>
</div>

<style>
    .input-group .form-control{
        z-index:1 !important;
    }
</style>
