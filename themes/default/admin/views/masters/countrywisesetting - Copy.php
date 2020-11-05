
<script>
$(document).ready(function () {

});
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('settings'); ?></h2>
       
    </div>
    <div class="box-content">
        <div class="row">
            
            <div class="col-lg-12">
            	<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("masters/countrywisesetting", $attrib);
                ?>
                
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
                                echo form_dropdown('country_id', $c, '', 'class="form-control tip" id="country_id" style="width:100%;"');
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
									
								?>
                                <option value="<?php echo $val; ?>" ><?php echo $val; ?></option>
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
                                echo form_dropdown('login_otp_enable', $ge, (isset($_POST['login_otp_enable']) ? $_POST['login_otp_enable'] : 0), 'class="tip form-control" id="login_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('device_change_otp', 'device_change_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('device_change_otp_enable', $ge, (isset($_POST['device_change_otp_enable']) ? $_POST['device_change_otp_enable'] : 0), 'class="tip form-control" id="device_change_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('ride_otp', 'ride_otp_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('ride_otp_enable', $ge, (isset($_POST['ride_otp_enable']) ? $_POST['ride_otp_enable'] : 0), 'class="tip form-control" id="ride_otp_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('address', 'address_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('address_enable', $ge, (isset($_POST['address_enable']) ? $_POST['address_enable'] : 0), 'class="tip form-control" id="address_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('account_holder_name', 'account_holder_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('account_holder_name_enable', $ge, (isset($_POST['account_holder_name_enable']) ? $_POST['account_holder_name_enable'] : 0), 'class="tip form-control" id="account_holder_name_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('bank_name', 'bank_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('bank_name_enable', $ge, (isset($_POST['bank_name_enable']) ? $_POST['bank_name_enable'] : 0), 'class="tip form-control" id="bank_name_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('branch_name', 'branch_name_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('branch_name_enable', $ge, (isset($_POST['branch_name_enable']) ? $_POST['branch_name_enable'] : 0), 'class="tip form-control" id="branch_name_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('ifsc_code', 'ifsc_code_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('ifsc_code_enable', $ge, (isset($_POST['ifsc_code_enable']) ? $_POST['ifsc_code_enable'] : 0), 'class="tip form-control" id="ifsc_code_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('aadhaar', 'aadhaar_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('aadhaar_enable', $ge, (isset($_POST['aadhaar_enable']) ? $_POST['aadhaar_enable'] : 0), 'class="tip form-control" id="aadhaar_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('pancard', 'pancard_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('pancard_enable', $ge, (isset($_POST['pancard_enable']) ? $_POST['pancard_enable'] : 0), 'class="tip form-control" id="pancard_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('license', 'license_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('license_enable', $ge, (isset($_POST['license_enable']) ? $_POST['license_enable'] : 0), 'class="tip form-control" id="license_enable" ');
                                ?>
                            </div>
                    </div><div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('police', 'police_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('police_enable', $ge, (isset($_POST['police_enable']) ? $_POST['police_enable'] : 0), 'class="tip form-control" id="police_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('loan', 'loan_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('loan_enable', $ge, (isset($_POST['loan_enable']) ? $_POST['loan_enable'] : 0), 'class="tip form-control" id="loan_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('vendor', 'vendor_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('vendor_enable', $ge, (isset($_POST['vendor_enable']) ? $_POST['vendor_enable'] : 0), 'class="tip form-control" id="vendor_enable" ');
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
                                echo form_dropdown('cab_registration_enable', $ge, (isset($_POST['cab_registration_enable']) ? $_POST['cab_registration_enable'] : 0), 'class="tip form-control" id="cab_registration_enable" ');
                                ?>
                            </div>
                    </div>
                    
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('taxation', 'taxation_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('taxation_enable', $ge, (isset($_POST['taxation_enable']) ? $_POST['taxation_enable'] : 0), 'class="tip form-control" id="taxation_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('insurance', 'insurance_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('insurance_enable', $ge, (isset($_POST['insurance_enable']) ? $_POST['insurance_enable'] : 0), 'class="tip form-control" id="insurance_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('permit', 'permit_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('permit_enable', $ge, (isset($_POST['permit_enable']) ? $_POST['permit_enable'] : 0), 'class="tip form-control" id="permit_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('authorisation', 'authorisation_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('authorisation_enable', $ge, (isset($_POST['authorisation_enable']) ? $_POST['authorisation_enable'] : 0), 'class="tip form-control" id="authorisation_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('fitness', 'fitness_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('fitness_enable', $ge, (isset($_POST['fitness_enable']) ? $_POST['fitness_enable'] : 0), 'class="tip form-control" id="fitness_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('speed', 'speed_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('speed_enable', $ge, (isset($_POST['speed_enable']) ? $_POST['speed_enable'] : 0), 'class="tip form-control" id="speed_enable" ');
                                ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="form-group">
                                <?php echo lang('puc', 'puc_enable'); ?>
                                <?php
                                $ge[''] = array('0' => lang('No'), '1' => lang('Yes'));
                                echo form_dropdown('puc_enable', $ge, (isset($_POST['puc_enable']) ? $_POST['puc_enable'] : 0), 'class="tip form-control" id="puc_enable" ');
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
