<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_driver'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('edit_driver'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("users/edit_driver/".$user_id, $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12">  
                        	
                            
                            
                            <h2 class="row">User details</h2>  
                            <?php 
							
							if($result->parent_id != 0){ ?> 
                             <!--<div class="form-group">
                                <?php /*?><?php echo lang('vendor', 'parent_id'); ?>
                                <?php
								
                                $ven[''] = 'Select Vendor';
								foreach ($vendors as $vendor) {
									$ven[$vendor->id] = $vendor->first_name.' '.$vendor->last_name;
								}
                                echo form_dropdown('parent_id', $ven, $result->parent_id, 'class="tip form-control" id="parent_id" data-placeholder="' . lang("select") . ' ' . lang("vendor") . '" ');
                                ?><?php */?>
                            </div>-->
                            <?php } ?>
                             
                             <input type="hidden" name="parent_id" value="<?= $result->parent_id ?>" id="parent_id">                                                 
                            <div class="form-group">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <input type="text" id="first_name"  onkeyup="inputFirstUpper(this)" name="first_name" value="<?= $result->first_name ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <input type="text" id="last_name"  onkeyup="inputFirstUpper(this)" name="last_name" value="<?= $result->last_name ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="text" id="email" name="email" value="<?= $result->email ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('country_code', 'country_code'); ?>
                                <?php
                                $cc[''] = 'Select Country Code';
								foreach ($country_code as $cc_row) {
									$cc[$cc_row->phonecode] = '(+'.$cc_row->phonecode.') '.$cc_row->name;
								}
								
                                echo form_dropdown('country_code', $cc, $result->country_code, 'class="tip form-control" id="country_code" data-placeholder="' . lang("select") . ' ' . lang("country_code") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('mobile', 'mobile'); ?>
                                <div class="controls">
                                    <input type="text" id="mobile" name="mobile" value="<?= $result->mobile ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            
                            <div class="form-group">
                                <?php echo lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = array('male' => lang('male'), 'female' => lang('female'), 'others' => lang('others'));
                                echo form_dropdown('gender', $ge, $result->gender, 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('dob', 'dob'); ?>
                                <div class="controls">
                                    <input type="text" id="dob" name="dob" onkeypress="dateCheck(this);" value="<?= $result->dob == '0000-00-00' || $result->dob == NULL ? '' :$result->dob ?>" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small">Example: Date formate: DD/MM/YYYY</p>
                            </div>
                            
                            <div class="form-group">
                            	<div class="form-group">
                                <a href="<?= $result->photo_img ?>" class="without-caption image-link">
                                    <img src="<?= $result->photo_img ?>" width="400" height="400" />  
                                </a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <h2 class="row">Bank Details</h2>
                            <div class="form-group">
                                <?php echo lang('account_holder_name', 'account_holder_name'); ?>
                                <div class="controls">
                                    <input type="text"  onkeyup="inputFirstUpper(this)" id="account_holder_name" name="account_holder_name" value="<?= $result->account_holder_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('account_no', 'account_no'); ?>
                                <div class="controls">
                                    <input type="text" id="account_no" name="account_no" value="<?= $result->account_no ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('bank_name', 'bank_name'); ?>
                                <div class="controls">
                                    <input type="text" id="bank_name" name="bank_name" value="<?= $result->bank_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('branch_name', 'branch_name'); ?>
                                <div class="controls">
                                    <input type="text"  onkeyup="inputFirstUpper(this)" id="branch_name" name="branch_name" value="<?= $result->branch_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('ifsc_code', 'ifsc_code'); ?>
                                <div class="controls">
                                    <input type="text"  onkeyup="inputUpper(this)" id="ifsc_code" name="ifsc_code" value="<?= $result->ifsc_code ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <h2 class="row">Document details</h2> 
                             
                             <div class="form-group">
                            	<div class="form-group">
                                <a href="<?= $result->aadhaar_image_img ?>" class="without-caption image-link">
                                    <img src="<?= $result->aadhaar_image_img ?>" width="400" height="400" />  
                                </a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            </div>
                             <div class="form-group all">
								<?= lang("aadhaar_doc", "aadhaar_image") ?>
                                <input id="aadhaar_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="aadhaar_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('aadhaar_no', 'aadhaar_no'); ?>
                                <div class="controls">
                                    <input type="text" id="aadhaar_no" name="aadhaar_no" value="<?= $result->aadhaar_no ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                            	<div class="form-group">
                                <a href="<?= $result->pancard_image_img ?>" class="without-caption image-link">
                                    <img src="<?= $result->pancard_image_img ?>" width="400" height="400" />  
                                </a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("pancard_doc", "pancard_image") ?>
                                <input id="pancard_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="pancard_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('pancard_no', 'pancard_no'); ?>
                                <div class="controls">
                                    <input type="text"  onkeyup="inputUpper(this)" id="pancard_no" name="pancard_no" value="<?= $result->pancard_no ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                            	<div class="form-group">
                                <a href="<?= $result->license_image_img ?>" class="without-caption image-link">
                                    <img src="<?= $result->license_image_img ?>" width="400" height="400" />  
                                </a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("license_doc", "license_image") ?>
                                <input id="license_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="license_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_dob', 'license_dob'); ?>
                                <div class="controls">
                                    <input type="text" id="license_dob" name="license_dob"  onkeypress="dateCheck(this);" value="<?= $result->license_dob ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_ward_name', 'license_ward_name'); ?>
                                <div class="controls">
                                    <input type="text"  onkeyup="inputFirstUpper(this)" id="license_ward_name" name="license_ward_name" value="<?= $result->license_ward_name ?>" class="form-control"/>
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
                                    <input type="text" id="license_issuing_authority" name="license_issuing_authority" value="<?= $result->license_issuing_authority ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_issued_on', 'license_issued_on'); ?>
                                <div class="controls">
                                    <input type="text" id="license_issued_on" name="license_issued_on"  onkeypress="dateCheck(this);" value="<?= $result->license_issued_on == '0000-00-00' ? '' :$result->license_issued_on ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_validity', 'license_validity'); ?>
                                <div class="controls">
                                    <input type="text" id="license_validity" name="license_validity"  onkeypress="dateCheck(this);" value="<?= $result->license_validity == '0000-00-00' ? '' :$result->license_validity ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                            	<div class="form-group">
                                <a href="<?= $result->police_image_img ?>" class="without-caption image-link">
                                    <img src="<?= $result->police_image_img ?>" width="400" height="400" />  
                                </a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("police_doc", "police_image") ?>
                                <input id="police_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="police_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('police_on', 'police_on'); ?>
                                <div class="controls">
                                    <input type="text" id="police_on" name="police_on"  onkeypress="dateCheck(this);" value="<?= $result->police_on == '0000-00-00' ? '' :$result->police_on ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('police_til', 'police_til'); ?>
                                <div class="controls">
                                    <input type="text" id="police_til" name="police_til"  onkeypress="dateCheck(this);" value="<?= $result->police_til == '0000-00-00' ? '' :$result->police_til ?>" class="form-control"/>
                                </div>
                            </div>
                           
                            
                        </div>
                    </div> 
                    
                    <div class="col-md-6">
                    	<div class="col-md-12">
                        	
                            <h2 class="row">Local Address</h2>  
                            <div class="form-group">
                            	<div class="form-group">
                                <a href="<?= $result->local_image_img ?>" class="without-caption image-link">
                                    <img src="<?= $result->local_image_img ?>" width="400" height="400" />  
                                </a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            </div>
                            <div class="form-group all">
								<?= lang("local_address_doc", "local_image") ?>
                                <input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('address', 'local_address'); ?>
                                <div class="controls">
                                    <input type="text" id="local_address" name="local_address" value="<?= $result->local_address ?>" class="form-control" />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('local_pincode', 'local_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="local_pincode" onkeyup="inputUpper(this)" value="<?= $result->local_pincode ?>" name="local_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                           
                            
                            <h2 class="row">Permanent Address</h2>  
                            <div class="form-group">
                            	<div class="form-group">
                                <a href="<?= $result->permanent_image_img ?>" class="without-caption image-link">
                                    <img src="<?= $result->permanent_image_img ?>" width="400" height="400" />  
                                </a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            </div>
                            <div class="form-group all">
								<?= lang("permanent_address_doc", "permanent_image") ?>
                                <input id="permanent_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permanent_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('address', 'permanent_address'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_address" name="permanent_address" value="<?= $result->permanent_address ?>" class="form-control"/>
                                </div>
                            </div>
                            
                             <div class="form-group">
                                <?php echo lang('permanent_pincode', 'permanent_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_pincode" onkeyup="inputUpper(this)" value="<?= $result_address->permanent_pincode ?>" name="permanent_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                           
                            
                            
                        </div>
                    </div>
                                      
                </div>

                <p><?php echo form_submit('edit_driver', lang('edit_driver'), 'class="btn btn-primary pull-right"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>

	
$(document).ready(function(){
	
	$('.vendor_and_driver').hide();
	
});
</script>