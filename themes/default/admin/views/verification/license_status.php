<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
        
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("verification/license_status/".$id, $attrib);
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                    	<fieldset class="col-md-12">    	
                            <legend>Details</legend>
                                
                            <h2>License Document</h2>
                            <div class="form-group">
                            	<a href="<?= $result->license_image ?>" class="without-caption image-link">
									<img src="<?= $result->license_image ?>" width="400" height="400" />  
								</a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            <input type="hidden" name="document_id" value="<?= $result->document_id ?>">
                            <input type="hidden" name="verification_first_name" value="<?= $result->first_name ?>">
                            
                            
                            <div class="form-group">
                                        <?= lang("Verified", "license_verify"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left2" class="skip" name="license_verify" <?php echo ($result->license_verify==0) ? "checked" : ''; ?>>
                                            <label for="switch_left2">OFF</label>
                                            <input type="radio" value="1" id="switch_right2" class="skip" name="license_verify" <?php echo ($result->license_verify==1) ? "checked" : ''; ?>>
                                            <label for="switch_right2">ON</label>
                                            
                                        </div>
                                    </div>
                                    
                                 <div class="form-group">
                                <?php echo lang('license_no', 'license_no'); ?>
                                <div class="controls">
                                    <input type="text" id="license_no" onkeyup="inputUpper(this)" value="<?= $result->license_no ?>" name="license_no" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_dob', 'license_dob'); ?>
                                <div class="controls">
                                    <input type="text" id="license_dob" onkeypress="dateCheck(this);"  value="<?= $result->license_dob == '0000-00-00' || $result->license_dob == NULL ? '' :$result->license_dob ?>"  onkeypress="dateCheck(this);" name="license_dob" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_ward_name', 'license_ward_name'); ?>
                                <div class="controls">
                                    <input type="text" id="license_ward_name" onkeyup="inputFirstUpper(this)" value="<?= $result->license_ward_name ?>" name="license_ward_name" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?= lang("license_country", "license_country"); ?>
                                
                               <?php
							   $licensecon[''] = 'Select Countrys';
								foreach ($license_countrys as $license_country) {
									$licensecon[$license_country->id] = $license_country->name;
								}
                                echo form_dropdown('license_country_id', $licensecon, $result->license_country_id, 'class="form-control select-license-country " id="license_country_id" '); ?>
                            </div>
                            
                            <div class="form-group">
                                <?= lang("license_type", "license_type"); ?>
                               <select name="license_type[]" id="license_type" class="form-control select-license-type" multiple>
                                	<option value="">Select License Type</option>
                                    <?php
									foreach ($license_type as $ltype) {
										 $selected = in_array($ltype->id, json_decode($result_document->license_type)) ? " selected " : null;
									?>
                                    <option <?php echo $selected ?> value="<?= $ltype->id ?>"><?= $ltype->name ?></option>
                                    <?php
									}
									?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_issuing_authority', 'license_issuing_authority'); ?>
                                <div class="controls">
                                    <input type="text" id="license_issuing_authority" value="<?= $result->license_issuing_authority ?>" name="license_issuing_authority" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_issued_on', 'license_issued_on'); ?>
                                <div class="controls">
                                    <input type="text" id="license_issued_on"  onkeypress="dateCheck(this);" value="<?= $result->license_issued_on == '0000-00-00' || $result->license_issued_on == NULL ? '' :$result->license_issued_on ?>" name="license_issued_on" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_validity', 'license_validity'); ?>
                                <div class="controls">
                                    <input type="text" id="license_validity"  onkeypress="dateCheck(this);" value="<?= $result->license_validity == '0000-00-00' || $result->license_validity == NULL ? '' :$result->license_validity ?>" name="license_validity" class="form-control"/>
                                </div>
                            </div>
                                   
                          </fieldset>
                         
                       
                        
                     </div>
                     
                     
                 </div>
                <?php echo form_submit('license_status', lang('submit'), 'class="btn btn-primary"'); ?>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
</div>
<style>
.switch-field {
  display: inline;
}

.switch-title {
  margin-bottom: 6px;
}

.switch-field input {
    position: absolute !important;
    clip: rect(0, 0, 0, 0);
    height: 1px;
    width: 1px;
    border: 0;
    overflow: hidden;
}

.switch-field label {
  float: left;
}

.switch-field label {
  display: inline-block;
  width: 35px;
  background-color: #fffff;
  color: #000000;
  font-size: 14px;
  font-weight: normal;
  text-align: center;
  text-shadow: none;
  padding: 3px 5px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  -webkit-transition: all 0.1s ease-in-out;
  -moz-transition:    all 0.1s ease-in-out;
  -ms-transition:     all 0.1s ease-in-out;
  -o-transition:      all 0.1s ease-in-out;
  transition:         all 0.1s ease-in-out;
}

.switch-field label:hover {
	cursor: pointer;
}

.switch-field input:checked + label {
  background-color: #2489c5;
  -webkit-box-shadow: none;
  box-shadow: none;
  color: #fff;
}

.switch-field label:first-of-type {
  border-radius: 13px 0 0 13px;
}

.switch-field label:last-of-type {
  border-radius: 0 13px 13px 0;
}
</style>
<script>

	
$(document).ready(function(){
	
});
</script>

