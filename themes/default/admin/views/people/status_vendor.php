<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
           	
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/vendor_status/deactive/".$user_id, $attrib);
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
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $vendor_result->is_country){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        </div>
                    	<div class="col-md-12">    	
                           	<h2 class="box_he_de"><?= lang('user_details') ?></h2>
                                <input type="hidden" name="user_id" value="<?= $user_id ?>">	
							<div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <input type="text" id="first_name" onkeyup="inputFirstUpper(this)" name="first_name" value="<?= $vendor_result->first_name ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <input type="text" id="last_name" onkeyup="inputFirstUpper(this)" name="last_name" value="<?= $vendor_result->last_name ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="text" id="email" name="email" value="<?= $vendor_result->email ?>" class="form-control" required="required"/>
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
								
                                echo form_dropdown('country_code', $cc, $vendor_result->country_code, 'class="tip form-control" id="country_code" data-placeholder="' . lang("select") . ' ' . lang("country_code") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('mobile', 'mobile'); ?>
                                <div class="controls">
                                    <input type="text" id="mobile" name="mobile" class="form-control" value="<?= $vendor_result->mobile ?>" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = array('male' => lang('male'), 'female' => lang('female'), 'others' => lang('others'));
                                echo form_dropdown('gender', $ge, $vendor_result->gender, 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('dob', 'dob'); ?>
                                <div class="controls">
                                    <input type="text" id="dob" name="dob" onkeypress="dateCheck(this);" class="form-control" value="<?= $vendor_result->dob == '0000-00-00' || $vendor_result->dob == NULL ? '' : $vendor_result->dob ?>" required="required"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            
                            
                            <input type="hidden" name="vendor_id" value="<?= $vendor_personal_result->vendor_id ?>" >
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('gst', 'gst'); ?>
                                <div class="controls">
                                    <input type="text" id="gst" name="gst" onkeyup="inputUpper(this)" class="form-control" value="<?= $vendor_personal_result->gst ?>" required="required"/>
                                </div>
                                <p class="help-block-small">Example: GST: 22AAAAA0000A1Z1</p>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('telephone_number', 'telephone_number'); ?>
                                <div class="controls">
                                    <input type="text" id="telephone_number" name="telephone_number" class="form-control" value="<?= $vendor_personal_result->telephone_number ?>" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('legal_entity', 'legal_entity'); ?>
                                <div class="controls">
                                    <input type="text" id="legal_entity" onkeyup="inputUpper(this)" name="legal_entity" class="form-control" value="<?= $vendor_personal_result->legal_entity ?>" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
								<div class="form-group all">
									<?= lang("photo", "photo") ?>
									<input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                            	<div class="form-group">
									<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											<a href="<?= $vendor_result->photo_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $vendor_result->photo_img ?>" class="img"  data-large-img-url="<?= $vendor_result->photo_img ?>" data-large-img-wrapper="preview" />  
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
                            </div>
							<div class="form-group col-sm-3 col-xs-12">
								<?= lang("approved", "approved"); ?><br>
								<div class="switch-field">

									<input type="radio" value="0" id="switch_left_is_approved" class="skip" name="is_approved" <?php echo ($vendor_result->is_approved==0) ? "checked" : ''; ?>>
									<label for="switch_left_is_approved">OFF</label>
									<input type="radio" value="1" id="switch_right_is_approved" class="skip" name="is_approved" <?php echo ($vendor_result->is_approved==1) ? "checked" : ''; ?>>
									<label for="switch_right_is_approved">ON</label>

								</div>
							</div>
                            </div>
                            
                    	
                        <div class="col-md-6">  
                           <h2 class="box_he_de"><?= lang('local_address') ?></h2>  
									
								<div class="form-group col-sm-6 col-xs-12">
									<div class="form-group all">
										<?= lang("local_address_doc", "local_image") ?>
										<input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
									<div class="col-sm-12 img_box_se_head">
                            			<div class="img_box_se">
											<a href="<?= $result_address->local_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result_address->local_image_img ?>" class="img"  data-large-img-url="<?= $result->permanent_image_img ?>" data-large-img-wrapper="preview1">  
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
                            
                            <input type="hidden" name="address_id" value="<?= $result_address->address_id ?>">
                            <input type="hidden" name="first_name_hidden" value="<?= $result_address->first_name ?>">
                           
                           
                            
                            <div class="form-group col-sm-6 col-xs-12">
                                <?php echo lang('address', 'local_address'); ?>
                                <div class="controls">
                                    <input type="text" id="local_address" value="<?= $result_address->local_address ?>" name="local_address" class="form-control" />
                                </div>
                            </div>
                            
                           
                            <div class="form-group col-sm-6 col-xs-12">
                                <?php echo lang('local_pincode', 'local_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="local_pincode" onkeyup="inputUpper(this)" value="<?= $result_address->local_pincode ?>" name="local_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                        <?= lang("Verified", "local_verify"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left_local_verify" class="skip" name="local_verify" <?php echo ($result_address->local_verify==0) ? "checked" : ''; ?>>
                                            <label for="switch_left_local_verify">OFF</label>
                                            <input type="radio" value="1" id="switch_right_local_verify" class="skip" name="local_verify" <?php echo ($result_address->local_verify==1) ? "checked" : ''; ?>>
                                            <label for="switch_right_local_verify">ON</label>
                                            
                                        </div>
                                    </div>
                                    
                          </div>
                          <div class="col-md-6">
                           	<h2 class="box_he_de"><?= lang('permanent_address') ?></h2>     	
                            	<div class="form-group col-sm-6 col-xs-12">
									<div class="form-group all">
										<?= lang("permanent_address_doc", "permanent_image") ?>
										<input id="permanent_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permanent_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
                            		<div class="col-sm-12 img_box_se_head">
                            			<div class="img_box_se">
										<a href="<?= $result_address->permanent_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result_address->permanent_image_img ?>" class="img"  data-large-img-url="<?= $result_address->permanent_image_img ?>" data-large-img-wrapper="preview3">  
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
                            
                            
                            
                            
                            <div class="form-group col-sm-6 col-xs-12">
                                <?php echo lang('address', 'permanent_address'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_address" value="<?= $result_address->permanent_address ?>" name="permanent_address" class="form-control"/>
                                </div>
                            </div>
                           
                            <div class="form-group col-sm-6 col-xs-12">
                                <?php echo lang('permanent_pincode', 'permanent_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_pincode" onkeyup="inputUpper(this)" value="<?= $result_address->permanent_pincode ?>" name="permanent_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                           
							<div class="form-group col-sm-6 col-xs-12">
								<?= lang("Verified", "permanent_verify"); ?><br>
								<div class="switch-field">

									<input type="radio" value="0" id="switch_left_permanent_verify" class="skip" name="permanent_verify" <?php echo ($result_address->permanent_verify==0) ? "checked" : ''; ?>>
									<label for="switch_left_permanent_verify">OFF</label>
									<input type="radio" value="1" id="switch_right_permanent_verify" class="skip" name="permanent_verify" <?php echo ($result_address->permanent_verify==1) ? "checked" : ''; ?>>
									<label for="switch_right_permanent_verify">ON</label>

								</div>
							</div>
                               
                            </div>
    
						<div class="col-md-12">  
						<h2 class="box_he_de"><?= lang('bank_details') ?></h2>  	
						<input type="hidden" name="bank_id" value="<?= $result_account->bank_id ?>">
							<div class="form-group col-sm-12 col-xs-12">
								<?= lang("Verified", "is_verify"); ?><br>
								<div class="switch-field">

									<input type="radio" value="0" id="switch_left_is_verify" class="skip" name="is_verify" <?php echo ($result_account->is_verify==0) ? "checked" : ''; ?>>
									<label for="switch_left_is_verify">OFF</label>
									<input type="radio" value="1" id="switch_right_is_verify" class="skip" name="is_verify" <?php echo ($result_account->is_verify==1) ? "checked" : ''; ?>>
									<label for="switch_right_is_verify">ON</label>

								</div>
							</div>

							<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('account_holder_name', 'account_holder_name'); ?>
							<div class="controls">
								<input type="text" id="account_holder_name" onkeyup="inputFirstUpper(this)" name="account_holder_name" value="<?= $result_account->account_holder_name ?>" class="form-control"/>
							</div>
							</div>
							<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('account_no', 'account_no'); ?>
							<div class="controls">
								<input type="text" id="account_no" name="account_no" value="<?= $result_account->account_no ?>" class="form-control"/>
							</div>
							</div>
							<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('bank_name', 'bank_name'); ?>
							<div class="controls">
								<input type="text" id="bank_name" onkeyup="inputFirstUpper(this)" name="bank_name" value="<?= $result_account->bank_name ?>" class="form-control"/>
							</div>
							</div>
							<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('branch_name', 'branch_name'); ?>
							<div class="controls">
								<input type="text" id="branch_name" onkeyup="inputFirstUpper(this)" name="branch_name" value="<?= $result_account->branch_name ?>" class="form-control"/>
							</div>
							</div>
							<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('ifsc_code', 'ifsc_code'); ?>
							<div class="controls">
								<input type="text" id="ifsc_code" onkeyup="inputUpper(this)" name="ifsc_code" value="<?= $result_account->ifsc_code ?>" class="form-control"/>
							</div>
							<p class="help-block-small"><?= lang('ex_ifsc') ?></p>
							</div>      
						</div>
                          
						  <div class="col-md-12"> 
						  	<h2 class="box_he_de"><?= lang('aadhaar_details') ?></h2>   	
							 <input type="hidden" name="document_id" value="<?= $result_document->document_id ?>" >
								<div class="form-group col-sm-3 col-xs-12">
								 	<div class="form-group all">
										<?= lang("aadhaar_doc", "aadhaar_image") ?>
										<input id="aadhaar_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="aadhaar_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
								<div class="col-sm-12 img_box_se_head">
                            			<div class="img_box_se">
											<a href="<?= $result_document->aadhaar_image ?>" class="without-caption image-link">
												<img src="<?= $result_document->aadhaar_image ?>" />  
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
								<?php echo lang('aadhaar_no', 'aadhaar_no'); ?>
								<div class="controls">
									<input type="text" id="aadhaar_no" name="aadhaar_no" value="<?= $result_document->aadhaar_no ?>" class="form-control"/>
								</div>
								<p class="help-block-small"><?= lang('ex_aadhaar') ?></p>
							</div> 
							<div class="form-group col-sm-12 col-xs-12">
										<?= lang("Verified", "aadhar_verify"); ?><br>
										<div class="switch-field">

											<input type="radio" value="0" id="switch_left_aadhar_verify" class="skip" name="aadhar_verify" <?php echo ($result_document->aadhar_verify==0) ? "checked" : ''; ?>>
											<label for="switch_left_aadhar_verify">OFF</label>
											<input type="radio" value="1" id="switch_right_aadhar_verify" class="skip" name="aadhar_verify" <?php echo ($result_document->aadhar_verify==1) ? "checked" : ''; ?>>
											<label for="switch_right_aadhar_verify">ON</label>

										</div>
									</div>
						
						  </div>
                          
                          <div class="col-md-12">
							  <h2 class="box_he_de">Pancard Document</h2>
                            	<div class="form-group col-sm-3 col-xs-12">
								   <div class="form-group all">
										<?= lang("pancard_doc", "pancard_image") ?>
										<input id="pancard_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="pancard_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
										<div class="col-sm-12 img_box_se_head">
											<div class="img_box_se">
											  <a href="<?= $result_document->pancard_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result_document->pancard_image_img ?>" class="img"  data-large-img-url="<?= $result_document->pancard_image_img ?>" data-large-img-wrapper="preview4">  
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
									<?php echo lang('pancard_no', 'pancard_no'); ?>
									<div class="controls">
										<input type="text" id="pancard_no" onkeyup="inputUpper(this)" name="pancard_no" value="<?= $result_document->pancard_no ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_pancard') ?></p>
								</div> 
                            
                            	<div class="form-group">
                                        <?= lang("Verified", "pancard_verify"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left_pancard_verify" class="skip" name="pancard_verify" <?php echo ($result_document->pancard_verify==0) ? "checked" : ''; ?>>
                                            <label for="switch_left_pancard_verify">OFF</label>
                                            <input type="radio" value="1" id="switch_right_pancard_verify" class="skip" name="pancard_verify" <?php echo ($result_document->pancard_verify==1) ? "checked" : ''; ?>>
                                            <label for="switch_right_pancard_verify">ON</label>
                                            
                                        </div>
                                    </div>
                           
                          </div>
                          
                          <div class="col-md-12">    	
                            <h2 class="box_he_de">Loan Details</h2>
                            	<div class="form-group col-sm-3 col-xs-12">
                              		<div class="form-group all">
										<?= lang("loan_doc", "loan_doc") ?>
										<input id="loan_doc" type="file" data-browse-label="<?= lang('browse'); ?>" name="loan_doc" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
                               		<div class="col-sm-12 img_box_se_head">
                            			<div class="img_box_se">
											<a href="<?= $result_document->loan_doc_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result_document->loan_doc_img ?>" class="img"  data-large-img-url="<?= $result_document->loan_doc_img ?>" data-large-img-wrapper="preview5">  
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
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('loan_information', 'loan_information'); ?>
									<div class="controls">
										<input type="text" id="loan_information" onkeyup="inputFirstUpper(this)" name="loan_information" value="<?= $result_document->loan_information ?>" class="form-control"/>
									</div>
								</div> 
                            <div class="form-group">
								<?= lang("Verified", "loan_verify"); ?><br>
								<div class="switch-field">

									<input type="radio" value="0" id="switch_left_loan_verify" class="skip" name="loan_verify" <?php echo ($result_document->loan_verify==0) ? "checked" : ''; ?>>
									<label for="switch_left_loan_verify">OFF</label>
									<input type="radio" value="1" id="switch_right_loan_verify" class="skip" name="loan_verify" <?php echo ($result_document->loan_verify==1) ? "checked" : ''; ?>>
									<label for="switch_right_loan_verify">ON</label>

								</div>
							</div>
                            
                          </div>
                         
                 </div>
                <div class="col-sm-12 last_sa_se"><?php echo form_submit('vendor_status', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
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
	
	$('.vendor_and_driver').hide();
	
	
	$('#operator').change(function(){
		$('.vendor_and_driver').hide();
		
		if($('#operator').val() == 'vendor_and_driver') {
            $('.vendor_and_driver').show();
        } else {
			
            $('.vendor_and_driver').hide();
        } 
	});
	
});
</script>