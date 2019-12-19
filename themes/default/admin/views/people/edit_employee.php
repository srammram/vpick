<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    
    
    <div class="box-content">
    
        <div class="row">
            <div class="col-lg-12">
                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/employee_edit/".$user_id, $attrib);
                ?>
                <div class="row">
                <div class="instance_country col-sm-12">
                	<div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select  class="form-control select" name="is_country" id="is_country">
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
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <input type="text" id="first_name" onkeyup="inputFirstUpper(this)" name="first_name" value="<?= $result->first_name ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <input type="text" id="last_name" onkeyup="inputFirstUpper(this)" name="last_name" value="<?= $result->last_name ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="text" id="email" name="email" value="<?= $result->email ?>" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_email') ?></p>
                            </div>
                            
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('country_code', 'country_code'); ?>
                                <?php
                                $cc[''] = 'Select Country Code';
								foreach ($country_code as $cc_row) {
									$cc[$cc_row->phonecode] = '(+'.$cc_row->phonecode.') '.$cc_row->name;
								}
								
                                echo form_dropdown('country_code', $cc, $result->country_code, 'class="tip form-control" id="country_code" data-placeholder="' . lang("select") . ' ' . lang("country_code") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('mobile', 'mobile'); ?>
                                <div class="controls">
                                    <input type="text" id="mobile" name="mobile" value="<?= $result->mobile ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = array('male' => lang('male'), 'female' => lang('female'), 'others' => lang('others'));
                                echo form_dropdown('gender', $ge, $result->gender, 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('dob', 'dob'); ?>
                                <div class="controls">
                                    <input type="text" id="dob" name="dob" onkeypress="dateCheck(this);" value="<?= $result->dob == '0000-00-00' || $result->dob == NULL ? '' : $result->dob ?>" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            <div class="form-group col-sm-4 col-xs-12">
								<div class="form-group all">
									<?= lang("photo", "photo") ?>
									<input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                                <div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
										<a href="<?= $result->photo_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result->photo_img ?>" class="img"  data-large-img-url="<?= $result->photo_img ?>" data-large-img-wrapper="preview1" >  
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

                            <h2 class="box_he_de"><?= lang('bank_details') ?></h2>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('account_holder_name', 'account_holder_name'); ?>
                                <div class="controls">
                                    <input type="text" id="account_holder_name" onkeyup="inputFirstUpper(this)" name="account_holder_name" value="<?= $result->account_holder_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('account_no', 'account_no'); ?>
                                <div class="controls">
                                    <input type="text" id="account_no" name="account_no" value="<?= $result->account_no ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('bank_name', 'bank_name'); ?>
                                <div class="controls">
                                    <input type="text" id="bank_name" onkeyup="inputFirstUpper(this)" name="bank_name" value="<?= $result->bank_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('branch_name', 'branch_name'); ?>
                                <div class="controls">
                                    <input type="text" id="branch_name" onkeyup="inputFirstUpper(this)" name="branch_name" value="<?= $result->branch_name ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-4 col-xs-12">
                                <?php echo lang('ifsc_code', 'ifsc_code'); ?>
                                <div class="controls">
                                    <input type="text" id="ifsc_code" onkeyup="inputUpper(this)" name="ifsc_code" value="<?= $result->ifsc_code ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_ifsc') ?></p>
                            </div>
                            
                            <h2 class="box_he_de"><?= lang('doc_details') ?></h2> 
                             
                           <div class="form-group col-sm-4 col-xs-12">
							   <div class="form-group all">
									<?= lang("aadhaar_doc", "aadhaar_image") ?>
									<input id="aadhaar_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="aadhaar_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
								<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
										<a href="<?= $result->aadhaar_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result->aadhaar_image_img ?>" class="img"  data-large-img-url="<?= $result->aadhaar_image_img ?>" data-large-img-wrapper="preview">  
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
                           		<div class="form-group">
									<?php echo lang('aadhaar_no', 'aadhaar_no'); ?>
									<div class="controls">
										<input type="text" id="aadhaar_no" name="aadhaar_no" value="<?= $result->aadhaar_no ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_aadhaar') ?></p>
                            	</div>
                            </div>
                            
                            <div class="form-group col-sm-4 col-xs-12">
								<div class="form-group all ">
									<?= lang("pancard_doc", "pancard_image") ?>
									<input id="pancard_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="pancard_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
                           		<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
										<a href="<?= $result->pancard_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result->pancard_image_img ?>" class="img"  data-large-img-url="<?= $result->pancard_image_img ?>" data-large-img-wrapper="preview3">  
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
                            	<div class="form-group">
                                <?php echo lang('pancard_no', 'pancard_no'); ?>
                                <div class="controls">
                                    <input type="text" id="pancard_no" onkeyup="inputUpper(this)" name="pancard_no" value="<?= $result->pancard_no ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_pancard') ?></p>
                            </div>
                            </div>
                    </div> 
                    
					<div class="col-md-12">
						<h2 class="box_he_de"><?= lang('local_address') ?></h2>  
						<div class="form-group col-sm-4 col-xs-12">
							<div class="form-group all">
								<?= lang("address_doc", "local_image") ?>
								<input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
									   data-show-preview="false" class="form-control file" accept="im/*">
							</div>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $result->local_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
										<img src="<?= $result->local_image_img ?>" class="img"  data-large-img-url="<?= $result->local_image_img ?>" data-large-img-wrapper="preview4">  
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
							<div class="form-group">
								<?php echo lang('address', 'local_address'); ?>
								<div class="controls">
									<input type="text" id="local_address" name="local_address" value="<?= $result->local_address ?>" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<?php echo lang('pincode', 'local_pincode'); ?>
								<div class="controls">
									<input type="text" id="local_pincode" onkeyup="inputUpper(this)" value="<?= $result->local_pincode ?>" name="local_pincode" class="form-control" required="required"/>
								</div>
							</div>
						</div>
						<h2 class="box_he_de"><?= lang('permanent_address') ?></h2>  
						<div class="form-group col-sm-4 col-xs-12">
							<div class="form-group all">
								<?= lang("address_doc", "permanent_image") ?>
								<input id="permanent_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permanent_image" data-show-upload="false"
									   data-show-preview="false" class="form-control file" accept="im/*">
							</div>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $result->permanent_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
										<img src="<?= $result->permanent_image_img ?>" class="img"  data-large-img-url="<?= $result->permanent_image_img ?>" data-large-img-wrapper="preview5">  
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
							<div class="form-group">
								<?php echo lang('address', 'permanent_address'); ?>
								<div class="controls">
									<input type="text" id="permanent_address" name="permanent_address" value="<?= $result->permanent_address ?>" class="form-control"/>
								</div>
							</div>
							<div class="form-group">
								<?php echo lang('pincode', 'permanent_pincode'); ?>
								<div class="controls">
									<input type="text" id="permanent_pincode" onkeyup="inputUpper(this)" value="<?= $result->permanent_pincode ?>" name="permanent_pincode" class="form-control" required="required"/>
								</div>
							</div>
						</div>
					</div>
                                      
                </div>
                <?php if($view == ''){ ?>
                <div class="col-sm-12 last_sa_se"><?php echo form_submit('edit_employee', lang('submit'), 'class="btn btn-primary  change_btn_save center-block"'); ?></div>
				<?php } ?>
                
				
                

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>

	
$(document).ready(function(){
	
	
});
</script>