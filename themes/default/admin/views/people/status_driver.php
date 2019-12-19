<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
           	
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/driver_status/deactive/".$user_id, $attrib);
				$regset = $this->site->RegsiterSettings($driver_result->is_country);
                ?>
                
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <input type="hidden" name="bank_id" value="<?= $result_account->bank_id ?>">
                     <input type="hidden" name="address_id" value="<?= $result_address->address_id ?>">
                            <input type="hidden" name="first_name_hidden" value="<?= $result_address->first_name ?>">
                  <input type="hidden" name="document_id" value="<?= $result_document->document_id ?>" >                   
                <div class="row">
                	<div class="instance_country col-sm-12">
                	<div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $driver_result->is_country){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        </div>
                    	<div class="col-md-12">   
                        	<div class="form-group">
                            <div class="switch-field">
                    
                                <input type="radio" value="0" id="switch_left_is_approved" class="skip" name="is_approved" checked>
                                <label for="switch_left_is_approved">OFF</label>
                                <input type="radio" value="1" id="switch_right_is_approved" class="skip" name="is_approved">
                                <label for="switch_right_is_approved">ON</label>
                            </div>
                            <!--<a href="<?= admin_url('people/add_reason/'.$user_id); ?>" data-toggle="modal" data-target="#myModal"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-plus-circle"></i> <?= lang("add_reason"); ?></button></a>-->
                        </div>
                        
                        
                         	
                            <h2 class="box_he_de"><?= lang('user_details') ?></h2>  
							<div class="col-md-12">
								
							<div class="form-group col-md-4 col-xs-12">
								<div class="form-group">
									<?php echo lang('first_name', 'first_name'); ?>
									<div class="controls">
										<input type="text" id="first_name" name="first_name" onkeyup="inputFirstUpper(this)" value="<?= $driver_result->first_name ?>" class="form-control" required="required"/>
									</div>
								</div>
								<div class="form-group">
									<?php echo lang('last_name', 'last_name'); ?>
									<div class="controls">
										<input type="text" id="last_name" name="last_name" onkeyup="inputFirstUpper(this)" value="<?= $driver_result->last_name ?>" class="form-control"/>
									</div>
								</div>
								<div class="form-group">
									<?php echo lang('email', 'email'); ?>
									<div class="controls">
										<input type="text" id="email" name="email" value="<?= $driver_result->email ?>" class="form-control" required="required"/>
									</div>
									<p class="help-block-small"><?= lang('ex_email') ?></p>
								</div>
								<div class="form-group">
									<?php echo lang('gender', 'gender'); ?>
									<?php
									$ge[''] = array('male' => lang('male'), 'female' => lang('female'), 'others' => lang('others'));
									echo form_dropdown('gender', $ge, $driver_result->gender, 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
									?>
								</div>
							</div>  	
							<div class="form-group col-sm-4 col-xs-12">
								<div class="form-group">
									<?php echo lang('country_code', 'country_code'); ?>
									<?php
									$cc[''] = 'Select Country Code';
									foreach ($country_code as $cc_row) {
										$cc[$cc_row->phonecode] = '(+'.$cc_row->phonecode.') '.$cc_row->name;
									}

									echo form_dropdown('country_code', $cc, $driver_result->country_code, 'class="tip form-control" id="country_code" data-placeholder="' . lang("select") . ' ' . lang("country_code") . '" disabled');
									?>
								</div>
								<div class="form-group">
									<?php echo lang('mobile', 'mobile'); ?>
									<div class="controls">
										<input type="text" id="mobile" name="mobile" class="form-control" value="<?= $driver_result->mobile ?>" disabled/>
									</div>
								</div>
								<div class="form-group">
									<?php echo lang('dob', 'dob'); ?>
									<div class="controls">
										<input type="text" id="dob" name="dob" onkeypress="dateCheck(this);" class="form-control" value="<?= $driver_result->dob == '0000-00-00' || $driver_result->dob == NULL  ? '' : $driver_result->dob ?>" required="required"/>
									</div>
									<p class="help-block-small"><?= lang('ex_date') ?></p>
								</div>
							</div>
								<div class="form-group  col-sm-4 col-xs-12">
									<div class="form-group all">
										<?= lang("photo", "photo") ?>
										<input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
									<div class="form-group">
										<div class="col-sm-12 img_box_se_head">
											<div class="img_box_se">
												<a href="<?= $driver_result->photo_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
													<img src="<?= $driver_result->photo_img ?>" class="img"  data-large-img-url="<?= $driver_result->photo_img ?>" data-large-img-wrapper="preview"> 
												</a>
											</div>
										</div>
										
										<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
											<span class="pull-left">
											<input type="file" id="selectedFile" style="display: none;" />
											<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
										   </span> <i class="fa fa-rotate-right pull-right"></i>
										</button>
										<div class="magnifier-preview" id="preview" style="width: 300px; height:300px;position: absolute;left:-90%;"></div>
									</div>
								</div>
                            
                            
                                    
                                </div>
                            </div>
                        <div class="col-md-12">    	
                            <h2 class="box_he_de"><?= lang('bank_details') ?></h2>
                            
                            <?php if($regset->account_holder_name_enable == 1){ ?>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('account_holder_name', 'account_holder_name'); ?>
                                <div class="controls">
                                    <input type="text" id="account_holder_name" onkeyup="inputFirstUpper(this)" name="account_holder_name" value="<?= $result_account->account_holder_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('account_no', 'account_no'); ?>
                                <div class="controls">
                                    <input type="text" id="account_no" name="account_no" value="<?= $result_account->account_no ?>" class="form-control"/>
                                </div>
                            </div>
                            <?php if($regset->bank_name_enable == 1){ ?>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('bank_name', 'bank_name'); ?>
                                <div class="controls">
                                    <input type="text" id="bank_name" name="bank_name" onkeyup="inputFirstUpper(this)" value="<?= $result_account->bank_name ?>" class="form-control"/>
                                </div>
                            </div>
                             <?php } ?>
                             <?php if($regset->branch_name_enable == 1){ ?>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('branch_name', 'branch_name'); ?>
                                <div class="controls">
                                    <input type="text" id="branch_name" name="branch_name" onkeyup="inputFirstUpper(this)" value="<?= $result_account->branch_name ?>" class="form-control"/>
                                </div>
                            </div>
                             <?php } ?>
                             <?php if($regset->ifsc_code_enable == 1){ ?>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('ifsc_code', 'ifsc_code'); ?>
                                <div class="controls">
                                    <input type="text" id="ifsc_code" name="ifsc_code" onkeyup="inputUpper(this)" value="<?= $result_account->ifsc_code ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_ifsc') ?></p>
                            </div> 
                            <?php } ?>     
                          </div>    
                    	<?php if($regset->address_enable == 1){ ?>
                        <div class="col-md-6">    	
                            <h2 class="box_he_de"><?= lang('local_address') ?>
                            <div class="form-group pull-right">
                                <input type="checkbox" class="checkbox" id="same_address" name="same_address" value="1" <?= $result_address->same_address == 1 ? 'checked' : '' ?>/>
                                <label for="extras" class="padding05"><?= lang('same_address') ?></label>
                            </div>
                            </h2>
							<div class="form-group col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo lang('address', 'local_address'); ?>
									<div class="controls">
										<input type="text" id="local_address" value="<?= $result_address->local_address ?>" name="local_address" class="form-control" />
									</div>
								</div>
                          
								<div class="form-group">
									<?php echo lang('pincode', 'local_pincode'); ?>
									<div class="controls">
										<input type="text" id="local_pincode" onkeyup="inputUpper(this)" value="<?= $result_address->local_pincode ?>" name="local_pincode" class="form-control" required="required"/>
									</div>
								</div>
							</div> 
							<div class="form-group col-sm-6 col-xs-12">
								<div class="form-group all">
									<?= lang("address_doc", "local_image") ?>
									<input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div> 
								<div class="form-group">
									<div class="col-sm-12 img_box_se_head">
                            			<div class="img_box_se">
											<a href="<?= $result_address->local_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result_address->local_image_img ?>" class="img"  data-large-img-url="<?= $result_address->local_image_img ?>" data-large-img-wrapper="preview1"> 
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
                           </div>
                            
                           
                            
                                    
                          </div>
                          <div class="col-md-6">    	
                           <h2 class="box_he_de"><?= lang('permanent_address') ?></h2>
							  <div class="form-group col-sm-6 col-xs-12">
							  		<div class="form-group">
										<?php echo lang('address', 'permanent_address'); ?>
										<div class="controls">
											<input type="text" id="permanent_address" value="<?= $result_address->permanent_address ?>" name="permanent_address" class="form-control"/>
										</div>
									</div>
                            
									<div class="form-group">
										<?php echo lang('pincode', 'permanent_pincode'); ?>
										<div class="controls">
											<input type="text" id="permanent_pincode" onkeyup="inputUpper(this)" value="<?= $result_address->permanent_pincode ?>" name="permanent_pincode" class="form-control" required="required"/>
										</div>
									</div>
							  </div>
							  <div class="form-group col-sm-6 col-xs-12">
									<div class="form-group all">
										<?= lang("address_doc", "permanent_image") ?>
										<input id="permanent_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permanent_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
									<div class="form-group">
                            		<div class="col-sm-12 img_box_se_head">
                            			<div class="img_box_se">
											<a href="<?= $result_address->permanent_image_img ?>" class="without-caption image-link">
												<img src="<?= $result_address->permanent_image_img ?>" />  
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
							  </div>
								
                                    
                               
                            </div>
                       <?php } ?>     
                     <div class="col-md-12">
                            <h2 class="box_he_de"><?= lang('doc_details') ?></h2>
                           
                          <?php if($regset->aadhaar_enable == 1){ ?>   
						 <div class="form-group col-sm-3 col-xs-12">
						 	<div class="form-group">
									<?php echo lang('aadhaar_no', 'aadhaar_no'); ?>
									<div class="controls">
										<input type="text" id="aadhaar_no" name="aadhaar_no" value="<?= $result_document->aadhaar_no ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_aadhaar') ?></p>
								</div>  
								<div class="form-group all">
									<?= lang("aadhaar_doc", "aadhaar_image") ?>
									<input id="aadhaar_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="aadhaar_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                            	<div class="form-group">
                               		<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											 <a href="<?= $result_document->aadhaar_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result_document->aadhaar_image_img ?>" class="img"  data-large-img-url="<?= $result_document->aadhaar_image_img ?>" data-large-img-wrapper="preview2">  
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
                            
						 </div>
							<?php } ?>      	
                        <?php if($regset->pancard_enable == 1){ ?>
                          <div class="form-group col-sm-3 col-xs-12">
                          		<div class="form-group">
									<?php echo lang('pancard_no', 'pancard_no'); ?>
									<div class="controls">
										<input type="text" id="pancard_no" name="pancard_no" onkeyup="inputUpper(this)" value="<?= $result_document->pancard_no ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_pancard') ?></p>
								</div>  
                           		<div class="form-group all">
									<?= lang("pancard_doc", "pancard_image") ?>
									<input id="pancard_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="pancard_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>  	
                            	<div class="form-group">
                            		<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
										<a href="<?= $result_document->pancard_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result_document->pancard_image_img ?>" class="img"  data-large-img-url="<?= $result_document->pancard_image_img ?>" data-large-img-wrapper="preview3"> 
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
                            	
                          
                          </div>
                          <?php } ?> 
                          <?php if($regset->license_enable == 1){ ?>
                          <div class="form-group col-sm-3 col-xs-12">
							 	<div class="form-group">
									<?php echo lang('license_no', 'license_no'); ?>
									<div class="controls">
										<input type="text" id="license_no" onkeyup="inputUpper(this)" value="<?= $result_document->license_no ?>" name="license_no" class="form-control"/>
									</div>
									<p class="help-block-small">.</p>
								</div>
							   <div class="form-group all">
									<?= lang("license_doc", "license_image") ?>
									<input id="license_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="license_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>    	
								<div class="form-group">
							  		<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											<a href="<?= $result_document->license_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result_document->license_image_img ?>" class="img"  data-large-img-url="<?= $result->permanent_image_img ?>" data-large-img-wrapper="preview4"> 
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
                            
                            
                            <div class="form-group">
                                <?php echo lang('license_dob', 'license_dob'); ?>
                                <div class="controls">
                                    <input type="text" id="license_dob"  onkeypress="dateCheck(this);" value="<?= $result_document->license_dob == '0000-00-00' || $result_document->license_dob == NULL ? '' : $result_document->license_dob  ?>" name="license_dob" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_ward_name', 'license_ward_name'); ?>
                                <div class="controls">
                                    <input type="text" id="license_ward_name" onkeyup="inputFirstUpper(this)" value="<?= $result_document->license_ward_name ?>" name="license_ward_name" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?= lang("license_country", "license_country"); ?>
                                
                               <?php
							   $licensecon[''] = 'Select Countrys';
								foreach ($license_countrys as $license_country) {
									$licensecon[$license_country->id] = $license_country->name;
								}
                                echo form_dropdown('license_country_id', $licensecon, $result_document->license_country_id, 'class="form-control select-license-country " id="license_country_id" '); ?>
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
                                    <input type="text" id="license_issuing_authority" value="<?= $result_document->license_issuing_authority ?>" name="license_issuing_authority" class="form-control" />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_issued_on', 'license_issued_on'); ?>
                                <div class="controls">
                                    <input type="text" id="license_issued_on"  onkeypress="dateCheck(this);" value="<?= $result_document->license_issued_on == '0000-00-00' || $result_document->license_issued_on == NULL ? '' : $result_document->license_issued_on ?>" name="license_issued_on" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('license_validity', 'license_validity'); ?>
                                <div class="controls">
                                    <input type="text" id="license_validity"  onkeypress="dateCheck(this);" value="<?= $result_document->license_validity == '0000-00-00' || $result_document->license_validity == NULL ? '' : $result_document->license_validity ?>" name="license_validity" class="form-control"/>
                                </div>
                            </div>
                                  
                          </div>
                          <?php } ?> 
                          <?php if($regset->police_enable == 1){ ?>
                          <div class="form-group col-sm-3 col-xs-12">
							   <div class="form-group">
									<?php echo lang('police_on', 'police_on'); ?>
									<div class="controls">
										<input type="text" id="police_on" name="police_on"  onkeypress="dateCheck(this);" value="<?= $result_document->police_on == '0000-00-00' || $result_document->police_on == NULL ? '' : $result_document->police_on ?>" class="form-control"/>
									</div>
									<p class="help-block-small">.</p>
								</div> 
								<div class="form-group all">
									<?= lang("police_doc", "police_image") ?>
									<input id="police_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="police_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>   	
                            	<div class="form-group">
                            		<div class="col-sm-12 img_box_se_head">
                            			<div class="img_box_se">
											<a href="<?= $result_document->police_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result_document->police_image_img ?>" class="img"  data-large-img-url="<?= $result_document->police_image_img ?>" data-large-img-wrapper="preview5">  
											</a>
										</div>
									</div>
									<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
										<span class="pull-left">
										<input type="file" id="selectedFile" style="display: none;" />
										<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
									   </span> <i class="fa fa-rotate-right pull-right"></i>
									</button>
									<div class="magnifier-preview" id="preview5" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
								</div>
								<div class="form-group">
									<?php echo lang('police_til', 'police_til'); ?>
									<div class="controls">

										<input type="text" id="police_til"  onkeypress="dateCheck(this);" value="<?= $result_document->police_til == '0000-00-00' || $result_document->police_til == NULL  ? '' : $result_document->police_til ?>" name="police_til" class="form-control"/>
									</div>
								</div> 
                            	
                          </div>
                          <?php } ?> 
                     </div>
                 </div>
               <div class="col-sm-12 last_sa_se"><?php echo form_submit('driver_status', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
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
	$('.driver_and_driver').hide();
	
	
	$('#operator').change(function(){
		$('.driver_and_driver').hide();
		
		if($('#operator').val() == 'driver_and_driver') {
            $('.driver_and_driver').show();
        } else {
			
            $('.driver_and_driver').hide();
        } 
	});
	
});
<?php
if($result_address->same_address == 1){
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
</script>