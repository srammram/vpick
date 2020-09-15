<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">


                <?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/driver_adminedit/".$user_id, $attrib);
				$regset = $this->site->RegsiterSettings($result->is_country);
                ?>
                 <input type="hidden" name="parent_id" value="<?= $result->parent_id ?>" id="parent_id">
                <div class="row">
                	<div class="instance_country col-sm-12">
                	<div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $result->is_country){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        </div>
                        <div class="col-md-12">  
                            <h2 class="box_he_de"><?= lang('user_details') ?></h2>  
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
                             
                                                                             
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <input type="text" id="first_name" onkeyup="inputFirstUpper(this)" name="first_name" value="<?= $result->first_name ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <input type="text" id="last_name" onkeyup="inputFirstUpper(this)" name="last_name" value="<?= $result->last_name ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="text" id="email" name="email" value="<?= $result->email ?>" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_email') ?></p>
                            </div>
                            
                           
                            
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = array('male' => lang('male'), 'female' => lang('female'), 'others' => lang('others'));
                                echo form_dropdown('gender', $ge, $result->gender, 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('dob', 'dob'); ?>
                                <div class="controls">
                                    <input type="text" id="dob" name="dob" onkeypress="dateCheck(this);" value="<?= $result->dob == '0000-00-00' || $result->dob == NULL ? '' : $result->dob ?>" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
								<div class="form-group all">
									<?= lang("photo", "photo") ?>
									<input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                           		<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
									   <a href="<?= $result->photo_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
                                    	<img src="<?= $result->photo_img ?>" class="img"  data-large-img-url="<?= $result->photo_img ?>" data-large-img-wrapper="preview">  
                                		</a>
									</div>
								</div>
								<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
                            	<div class="magnifier-preview" id="preview" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
                            </div>
                            <h2 class="box_he_de"><?= lang('bank_details') ?></h2>
                            <?php if($regset->account_holder_name_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('account_holder_name', 'account_holder_name'); ?>
                                <div class="controls">
                                    <input type="text" id="account_holder_name" onkeyup="inputFirstUpper(this)" name="account_holder_name" value="<?= $result->account_holder_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <?php } ?>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('account_no', 'account_no'); ?>
                                <div class="controls">
                                    <input type="text" id="account_no" name="account_no" value="<?= $result->account_no ?>" class="form-control"/>
                                </div>
                            </div>
                            <?php if($regset->bank_name_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('bank_name', 'bank_name'); ?>
                                <div class="controls">
                                    <input type="text" id="bank_name" onkeyup="inputFirstUpper(this)" name="bank_name" value="<?= $result->bank_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if($regset->branch_name_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('branch_name', 'branch_name'); ?>
                                <div class="controls">
                                    <input type="text" id="branch_name" onkeyup="inputFirstUpper(this)" name="branch_name" value="<?= $result->branch_name ?>" class="form-control"/>
                                </div>
                            </div>
                             <?php } ?>
                             <?php if($regset->ifsc_code_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('ifsc_code', 'ifsc_code'); ?>
                                <div class="controls">
                                    <input type="text" id="ifsc_code" onkeyup="inputUpper(this)" name="ifsc_code" value="<?= $result->ifsc_code ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_ifsc') ?></p>
                            </div>
                            <?php } ?>
                            
                            <h2 class="box_he_de"><?= lang('doc_details') ?></h2> 
                            <?php if($regset->aadhaar_enable == 1){ ?>
                             <div class="form-group col-sm-3 col-xs-12">
								 <div class="form-group all">
									<?= lang("aadhaar_doc", "aadhaar_image") ?>
									<input id="aadhaar_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="aadhaar_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                           		<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
									    <a href="<?= $result->aadhaar_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result->aadhaar_image_img ?>" class="img"  data-large-img-url="<?= $result->aadhaar_image_img ?>" data-large-img-wrapper="preview1">  
										</a>
									</div>
								</div>
								<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
                           		<div class="magnifier-preview" id="preview1" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('aadhaar_no', 'aadhaar_no'); ?>
                                <div class="controls">
                                    <input type="text" id="aadhaar_no" name="aadhaar_no" value="<?= $result->aadhaar_no ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_aadhaar') ?></p>
                            </div>
                            <?php } ?>
                            <?php if($regset->pancard_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
								<div class="form-group all">
									<?= lang("pancard_doc", "pancard_image") ?>
									<input id="pancard_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="pancard_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                           		<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
									   <a href="<?= $result->pancard_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result->pancard_image_img ?>" class="img"  data-large-img-url="<?= $result->pancard_image_img ?>" data-large-img-wrapper="preview2">  
										</a>
									</div>
								</div>
								<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
                           		<div class="magnifier-preview" id="preview2" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('pancard_no', 'pancard_no'); ?>
                                <div class="controls">
                                    <input type="text" id="pancard_no" onkeyup="inputUpper(this)" name="pancard_no" value="<?= $result->pancard_no ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_pancard') ?></p>
                            </div>
                            <?php } ?>
                            <?php if($regset->license_enable == 1){ ?>
                            
                            <div class="form-group col-sm-3 col-xs-12">
								<div class="form-group all ">
									<?= lang("license_doc", "license_image") ?>
									<input id="license_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="license_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                           		<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
									   	<a href="<?= $result->license_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
                                    		<img src="<?= $result->license_image_img ?>" class="img"  data-large-img-url="<?= $result->license_image_img ?>" data-large-img-wrapper="preview3">  
										</a>
									</div>
								</div>
								<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
                           		<div class="magnifier-preview" id="preview3" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
                            </div>
                            
                            
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_no', 'license_no'); ?>
                                <div class="controls">
                                    <input type="text" id="license_no" onkeyup="inputUpper(this)" value="<?= $result->license_no ?>" name="license_no" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_dob', 'license_dob'); ?>
                                <div class="controls">
                                    <input type="text" id="license_dob" name="license_dob"  onkeypress="dateCheck(this);" value="<?= $result->license_dob == '0000-00-00' || $result->license_dob == NULL  ? '' : $result->license_dob ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_ward_name', 'license_ward_name'); ?>
                                <div class="controls">
                                    <input type="text" id="license_ward_name" onkeyup="inputFirstUpper(this)" name="license_ward_name" value="<?= $result->license_ward_name ?>" class="form-control"/>
                                </div>
                            </div>
                            
                           
                           <div class="form-group col-sm-3 col-xs-12">
                                <?= lang("license_country", "license_country"); ?>
                                
                               <?php
							   $licensecon[''] = 'Select Countrys';
								foreach ($license_countrys as $license_country) {
									$licensecon[$license_country->id] = $license_country->name;
								}
                                echo form_dropdown('license_country_id', $licensecon, $result->license_country_id, 'class="form-control select-license-country " id="license_country_id" '); ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
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
                            
                        
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_issuing_authority', 'license_issuing_authority'); ?>
                                <div class="controls">
                                    <input type="text" id="license_issuing_authority" name="license_issuing_authority" value="<?= $result->license_issuing_authority ?>" class="form-control" />
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_issued_on', 'license_issued_on'); ?>
                                <div class="controls">
                                    <input type="text" id="license_issued_on"  onkeypress="dateCheck(this);" name="license_issued_on" value="<?= $result->license_issued_on == '0000-00-00' || $result->license_issued_on == NULL  ? '' : $result->license_issued_on ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_validity', 'license_validity'); ?>
                                <div class="controls">
                                    <input type="text" id="license_validity"  onkeypress="dateCheck(this);" name="license_validity" value="<?= $result->license_validity == '0000-00-00' || $result->license_validity == NULL  ? '' : $result->license_validity ?>" class="form-control"/>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if($regset->police_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
								<div class="form-group all">
									<?= lang("police_doc", "police_image") ?>
									<input id="police_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="police_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                           		<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
									    <a href="<?= $result->police_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
                                    		<img src="<?= $result->police_image_img ?>" class="img"  data-large-img-url="<?= $result->police_image_img ?>" data-large-img-wrapper="preview4">  
                                		</a>
									</div>
								</div>
								<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
                           <div class="magnifier-preview" id="preview4" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('issued_on', 'police_on'); ?>
                                <div class="controls">
                                    <input type="text" id="police_on" name="police_on"  onkeypress="dateCheck(this);" value="<?= $result->police_on == '0000-00-00' || $result->police_on == NULL  ? '' : $result->police_on ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('vaild_till', 'police_til'); ?>
                                <div class="controls">
                                    <input type="text" id="police_til" name="police_til"  onkeypress="dateCheck(this);" value="<?= $result->police_til == '0000-00-00' || $result->police_til == NULL  ? '' : $result->police_til ?>" class="form-control"/>
                                </div>
                            </div>
                             <?php } ?>
                           
                            
                        </div>
                       
                       <?php if($regset->address_enable == 1){ ?>
                        <div class="col-md-12">
                        	
                            <h2 class="box_he_de"><?= lang('local_address') ?>
                            
                            <div class="form-group pull-right">
                                <input type="checkbox" class="checkbox" id="same_address" name="same_address" value="1" <?= $result->same_address == 1 ? 'checked' : '' ?>/>
                                <label for="extras" class="padding05"><?= lang('same_address') ?></label>
                            </div>
                            
                            </h2>  
                            <div class="form-group col-sm-3 col-xs-12">
								<div class="form-group all">
									<?= lang("address_doc", "local_image") ?>
									<input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                           		<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
									   <a href="<?= $result->local_image_img ?>" class="without-caption image-link">
										<img src="<?= $result->local_image_img ?>" width="400" height="400" />  
                                		</a>
									</div>
								</div>
								<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
                            	
                            </div>
                            
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('address', 'local_address'); ?>
                                <div class="controls">
                                    <input type="text" id="local_address" name="local_address" value="<?= $result->local_address ?>" class="form-control" />
                                </div>
                            </div>
                            
                           
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('pincode', 'local_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="local_pincode" onkeyup="inputUpper(this)" value="<?= $result->local_pincode ?>" name="local_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <h2 class="box_he_de same_address"><?= lang('permanent_address') ?></h2>  
                            <div class="form-group col-sm-3 col-xs-12 same_address">
                            	<div class="form-group all">
									<?= lang("address_doc", "permanent_image") ?>
									<input id="permanent_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permanent_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                           		<div class="col-sm-12 img_box_se_head same_address">
									<div class="img_box_se">
									   	<a href="<?= $result->permanent_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
                                    		<img src="<?= $result->permanent_image_img ?>" class="img"  data-large-img-url="<?= $result->permanent_image_img ?>" data-large-img-wrapper="preview5">  
                                		</a>
									</div>
								</div>
								<button type="button" class="btn btn-info pull-right change_btn_sec same_address" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
                           		<div class="magnifier-preview" id="preview5" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12 same_address">
                                <?php echo lang('address', 'permanent_address'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_address" name="permanent_address" value="<?= $result->permanent_address ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            
                            
                            <div class="form-group col-sm-3 col-xs-12 same_address">
                                <?php echo lang('pincode', 'permanent_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_pincode" onkeyup="inputUpper(this)" value="<?= $result->permanent_pincode ?>" name="permanent_pincode" class="form-control"/>
                                </div>
                            </div>
                            
                           
                            
                            
                        </div>
                        
                         <?php } ?>
                    
                    	<?php if($complete_taxi == 0){ ?>
                        <div class="col-md-12">  
                        	<h2 class="box_he_de">Taxi details</h2>  
                            
                             <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('cab_name', 'cab_name'); ?>
                                <div class="controls">
                                    <input type="text" id="taxi_name" name="taxi_name" onkeyup="inputFirstUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
								<?php echo lang('make', 'make'); ?>
                                <?php
								$mk[] = 'Select Make';
                                foreach ($makes as $make) {
                                    $mk[$make->id] = $make->name;
                                }

                                echo form_dropdown('make', $mk, $result->make_id, 'class=" select-make select form-control" id="make" data-placeholder="' . lang("select") . ' ' . lang("make") . '" ');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('category', 'category'); ?>
                                <?php
								$cat[] = 'Select Category';
                                foreach ($categorys as $category) {
                                    $cat[$category->id] = $category->name;
                                }

                                echo form_dropdown('category', $cat, $result->category, 'class=" select-category select form-control" id="category" data-placeholder="' . lang("select") . ' ' . lang("category") . '" ');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('type', 'type'); ?>
                                <?php
								$t[] = 'Select Type';
                                foreach ($types as $type) {
                                    $t[$type->id] = $type->name;
                                }

                                echo form_dropdown('type', $t, $result->type, 'class=" select-type select form-control" id="type" data-placeholder="' . lang("select") . ' ' . lang("type") . '" ');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('model', 'model'); ?>
                                <?php
                                 
								foreach ($models as $model) {
									$mds[$model->id] = $model->name;
								}
								
                                echo form_dropdown('model', $mds, $result->model, 'class="select-model select form-control" id="model" data-placeholder="' . lang("select") . ' ' . lang("model") . '" ');
                                ?>
                               
                            </div>
                            
                            <!--<div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('model', 'model'); ?>
                                <div class="controls">
                                    <input type="text" id="model" name="model" onkeyup="inputFirstUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>-->
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('number', 'number'); ?>
                                <div class="controls">
                                    <input type="text" id="number" name="number" onkeyup="inputUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('engine_number', 'engine_number'); ?>
                                <div class="controls">
                                    <input type="text" id="engine_number" name="engine_number" onkeyup="inputUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('chassis_number', 'chassis_number'); ?>
                                <div class="controls">
                                    <input type="text" id="chassis_number" onkeyup="inputUpper(this)" name="chassis_number" class="form-control" required="required"/>
                                </div>
                            </div>
                           
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('color', 'color'); ?>
                                <div class="controls">
                                    <input type="text" id="color" name="color" onkeyup="inputUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('manufacture_year', 'manufacture_year'); ?>
                                <div class="controls">
                                    <input type="text" id="manufacture_year" name="manufacture_year" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('width', 'width'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="width" name="width" class="form-control" required="required"/>
                                    <span class="input-group-addon">
                                   
									<?php
									if($Settings->taxi_bodysize_formate == 1){
										echo 'Inch';
									}elseif($Settings->taxi_bodysize_formate == 2){
										echo 'Centimetre';
									}elseif($Settings->taxi_bodysize_formate == 3){
										echo 'Metre';
									}elseif($Settings->taxi_bodysize_formate == 4){
										echo 'Kilometre';
									}else{
										echo 'Foot';
									}
									 ?>		
									</span>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('height', 'height'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="height" name="height" class="form-control" required="required"/>
                                    <span class="input-group-addon">
                                   
									<?php
									if($Settings->taxi_bodysize_formate == 1){
										echo 'Inch';
									}elseif($Settings->taxi_bodysize_formate == 2){
										echo 'Centimetre';
									}elseif($Settings->taxi_bodysize_formate == 3){
										echo 'Metre';
									}elseif($Settings->taxi_bodysize_formate == 4){
										echo 'Kilometre';
									}else{
										echo 'Foot';
									}
									 ?>		
									</span>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('length input-group', 'length'); ?>
                                <div class="controls">
                                    <input type="text" id="length" name="length" class="form-control" required="required"/>
                                    <span class="input-group-addon">
                                   
									<?php
									if($Settings->taxi_bodysize_formate == 1){
										echo 'Inch';
									}elseif($Settings->taxi_bodysize_formate == 2){
										echo 'Centimetre';
									}elseif($Settings->taxi_bodysize_formate == 3){
										echo 'Metre';
									}elseif($Settings->taxi_bodysize_formate == 4){
										echo 'Kilometre';
									}else{
										echo 'Foot';
									}
									 ?>		
									</span>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('normal_weight', 'weight'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="weight" name="weight" class="form-control" required="required"/>
                                    <span class="input-group-addon">
									<?= $Settings->taxi_bodyweight_formate == 1 ? 'Kilogram' : 'Tonne'; ?>		
									</span>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('min_weight', 'min_weight'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="min_weight" name="min_weight" class="form-control" required="required"/>
                                    <span class="input-group-addon">
									<?= $Settings->taxi_bodyweight_formate == 1 ? 'Kilogram' : 'Tonne'; ?>		
									</span>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('max_weight', 'max_weight'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="max_weight" name="max_weight" class="form-control" required="required"/>
                                    <span class="input-group-addon">
									<?= $Settings->taxi_bodyweight_formate == 1 ? 'Kilogram' : 'Tonne'; ?>		
									</span>
                                </div>
                            </div>
                            
                            <!--<div class="form-group">
                                <?php //echo lang('ac/non_ac', 'ac'); ?>
                                <?php
                                //$ac[''] = array('0' => lang('Non AC'), '1' => lang('AC'));
                                //echo form_dropdown('ac', $ac, '', 'class="tip form-control" id="ac" data-placeholder="' . lang("select") . ' ' . lang("ac/non_ac") . '" required="required"');
                                ?>
                            </div>-->
                            
                            
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('fuel_type', 'fuel_type'); ?>
                                <?php
                                 
								foreach ($fuel_types as $fuel_type) {
									$ft[$fuel_type->id] = $fuel_type->name;
								}
								
                                echo form_dropdown('fuel_type', $ft, '', 'class="tip form-control" id="fuel_type" data-placeholder="' . lang("select") . ' ' . lang("fuel_type") . '"  ');
                                ?>
                            </div>
                            
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("taxi_photo", "taxi_photo") ?>
                                <input id="taxi_photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="taxi_photo" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                        </div>
                        
                        <div class="col-md-12">  
                        	<h2 class="box_he_de"><?= lang('cab_details') ?></h2>  
                            <?php if($regset->cab_registration_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("reg_image", "reg_image") ?>
                                <input id="reg_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="reg_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('reg_date', 'reg_date'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_date" name="reg_date" onkeypress="dateCheck(this);" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('reg_due_date', 'reg_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_due_date" onkeypress="dateCheck(this);" name="reg_due_date" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('owner_name', 'reg_owner_name'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_owner_name" onkeyup="inputFirstUpper(this)" name="reg_owner_name" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('owner_address', 'reg_owner_address'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_owner_address" onkeyup="inputFirstUpper(this)" name="reg_owner_address" class="form-control"/>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if($regset->taxation_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("taxation_image", "taxation_image") ?>
                                <input id="taxation_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="taxation_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('amount_paid', 'taxation_amount_paid'); ?>
                                <div class="controls">
                                    <input type="text" id="taxation_amount_paid" name="taxation_amount_paid" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('due_date', 'taxation_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="taxation_due_date" name="taxation_due_date" onkeypress="dateCheck(this);" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <?php } ?>
                            <?php if($regset->insurance_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("insurance_image", "insurance_image") ?>
                                <input id="insurance_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="insurance_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('policy_no', 'insurance_policy_no'); ?>
                                <div class="controls">
                                    <input type="text" id="insurance_policy_no" onkeyup="inputUpper(this)" name="insurance_policy_no" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('due_date', 'insurance_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="insurance_due_date" name="insurance_due_date" onkeypress="dateCheck(this);" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            <?php } ?>
                            <?php if($regset->permit_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("permit_image", "permit_image") ?>
                                <input id="permit_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permit_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('permit_no', 'permit_no'); ?>
                                <div class="controls">
                                    <input type="text" id="permit_no" onkeyup="inputUpper(this)" name="permit_no" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('due_date', 'permit_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="permit_due_date" onkeypress="dateCheck(this);" name="permit_due_date" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            <?php } ?>
                            <?php if($regset->authorisation_enable == 1){ ?>
                            
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("authorisation_image", "authorisation_image") ?>
                                <input id="authorisation_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="authorisation_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('authorisation_no', 'authorisation_no'); ?>
                                <div class="controls">
                                    <input type="text" id="authorisation_no" onkeyup="inputUpper(this)" name="authorisation_no" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('due_date', 'authorisation_due_date'); ?>
                                <div class="controls">
                                    <input type="text" onkeypress="dateCheck(this);" id="authorisation_due_date" name="authorisation_due_date" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            <?php } ?>
                            <?php if($regset->fitness_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("fitness_image", "fitness_image") ?>
                                <input id="fitness_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="fitness_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('due_date', 'fitness_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="fitness_due_date" name="fitness_due_date" onkeypress="dateCheck(this);" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                             <?php } ?>
                             <?php if($regset->speed_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("speed_image", "speed_image") ?>
                                <input id="speed_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="speed_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('due_date', 'speed_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="speed_due_date" name="speed_due_date" onkeypress="dateCheck(this);" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            <?php } ?>
                            <?php if($regset->puc_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("puc_image", "puc_image") ?>
                                <input id="puc_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="puc_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('due_date', 'puc_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="puc_due_date" name="puc_due_date" onkeypress="dateCheck(this);" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                          <?php } ?>
                         </div>
                        <?php } ?> 
                                    
                </div>

               <div class="col-sm-12 last_sa_se"><?php echo form_submit('edit_driver', lang('submit'), 'class="btn btn-primary  change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>

<?php
if($result->same_address == 1){
?>
$('.same_address').hide();
<?php } ?>

	
$(document).ready(function(){
	$(document).on('ifChecked', '#same_address', function (event) {
    $('.checkth, .checkft').iCheck('check');
    $('.same_address').hide();
});
$(document).on('ifUnchecked', '#same_address', function (event) {
    $('.checkth, .checkft').iCheck('uncheck');
    $('.same_address').show();
});

});
$(document).ready(function(){
	$('.select-category').change(function(){
		$(".select-type").select2("destroy");
		var category = $(this).val();
		var is_country = $('#is_country').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('taxi/getTypebycategory')?>',
			data: {category_id: category, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select type</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-type").html($option);
				$(".select-type").select2();
				
			}
		})
	});
	
	$('.select-make').change(function(){
		$(".select-model").select2("destroy");
		var make = $(this).val();
		var type = $('#type').val();
		var is_country = $('#is_country').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('taxi/getModelbymake_type')?>',
			data: {make_id: make, type_id: type, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select model</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-model").html($option);
				$(".select-model").select2();
				
			}
		})
	});
	$('.select-type').change(function(){
		$(".select-model").select2("destroy");
		var type = $(this).val();
		var make = $('#make').val();
		var is_country = $('#is_country').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('taxi/getModelbymake_type')?>',
			data: {make_id: make, type_id: type, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select model</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-model").html($option);
				$(".select-model").select2();
				
			}
		})
	});
	
});
</script>