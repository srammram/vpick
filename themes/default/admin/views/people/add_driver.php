<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/add_driver", $attrib);
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
                    
                    <?php if(!empty($checkcountry)){
						$regset = $this->site->RegsiterSettings($checkcountry);
						?>
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
                                <?php echo lang('vendor', 'parent_id'); ?>
                                <?php
                                $ven[''] = 'Select Vendor';
								foreach ($vendors as $vendor) {
									$ven[$vendor->id] = $vendor->first_name.' '.$vendor->last_name;
								}
                                echo form_dropdown('parent_id', $ven, (isset($_POST['parent_id']) ? $_POST['parent_id'] : ''), 'class="tip form-control" id="parent_id" data-placeholder="' . lang("select") . ' ' . lang("vendor") . '" ');
                                ?>
                            </div>
                                                                              
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <input type="text" id="first_name" name="first_name" onkeyup="inputFirstUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <input type="text" id="last_name" name="last_name" onkeyup="inputFirstUpper(this)" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="text" id="email" name="email" class="form-control" required="required"/>
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
								
                                echo form_dropdown('country_code', $cc, $commoncountry->phonecode ? $commoncountry->phonecode : '', 'class="tip form-control" id="country_code" data-placeholder="' . lang("select") . ' ' . lang("country_code") . '" required="required"');
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
                                    <input type="text" id="dob" name="dob" onkeypress="dateCheck(this);" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
					</div>
				   <div class="col-md-12"> 
                            <h2 class="box_he_de"><?= lang('doc_details') ?></h2> 
                             <?php if($regset->aadhaar_enable == 1){ ?>
                             <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("aadhaar_doc", "aadhaar_image") ?>
                                <input id="aadhaar_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="aadhaar_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('aadhaar_no', 'aadhaar_no'); ?>
                                <div class="controls">
                                    <input type="text" id="aadhaar_no" name="aadhaar_no" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_aadhaar') ?></p>
                            </div>
                            <?php } ?>
                            <?php if($regset->pancard_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("pancard_doc", "pancard_image") ?>
                                <input id="pancard_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="pancard_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('pancard_no', 'pancard_no'); ?>
                                <div class="controls">
                                    <input type="text" id="pancard_no" name="pancard_no" onkeyup="inputUpper(this)" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_pancard') ?></p>
                            </div>
                            <?php } ?>
                            <?php if($regset->license_enable == 1){ ?>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("license_doc", "license_image") ?>
                                <input id="license_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="license_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_no', 'license_no'); ?>
                                <div class="controls">
                                    <input type="text" id="license_no"  name="license_no" onkeyup="inputUpper(this)" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_dob', 'license_dob'); ?>
                                <div class="controls">
                                    <input type="text" id="license_dob" name="license_dob"  onkeypress="dateCheck(this);" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_ward_name', 'license_ward_name'); ?>
                                <div class="controls">
                                    <input type="text" id="license_ward_name" onkeyup="inputFirstUpper(this)" name="license_ward_name" class="form-control"/>
                                </div>
                            </div>
                            
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?= lang("license_country", "license_country"); ?>
                                
                               <?php
							   $licensecon[''] = 'Select Countrys';
								foreach ($license_countrys as $license_country) {
									$licensecon[$license_country->id] = $license_country->name;
								}
                                echo form_dropdown('license_country_id', $licensecon, '', 'class="form-control select-license-country " id="license_country_id" '); ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?= lang("license_type", "license_type"); ?>
                               <?php
                                echo form_dropdown('license_type', '', '', 'class="form-control select-license-type " id="license_type" multiple '); ?>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_issuing_authority', 'license_issuing_authority'); ?>
                                <div class="controls">
                                    <input type="text" id="license_issuing_authority"  name="license_issuing_authority" class="form-control" />
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_issued_on', 'license_issued_on'); ?>
                                <div class="controls">
                                    <input type="text" id="license_issued_on" onkeypress="dateCheck(this);"  name="license_issued_on" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('license_validity', 'license_validity'); ?>
                                <div class="controls">
                                    <input type="text" id="license_validity" onkeypress="dateCheck(this);"  name="license_validity" class="form-control"/>
                                </div>
                            </div>
                             <?php } ?>
                             <?php if($regset->police_enable == 1){ ?>
                            
                            
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("police_doc", "police_image") ?>
                                <input id="police_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="police_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('issued_on', 'police_on'); ?>
                                <div class="controls">
                                    <input type="text" id="police_on" name="police_on" onkeypress="dateCheck(this);"  class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('vaild_till', 'police_til'); ?>
                                <div class="controls">
                                    <input type="text" id="police_til" name="police_til" onkeypress="dateCheck(this);"  class="form-control"/>
                                </div>
                            </div>
                           <?php } ?>
                            
                        </div>
                        <?php if($regset->address_enable == 1){ ?>
                        <div class="col-md-12">
                        	
                            <h2 class="box_he_de"><?= lang('local_address') ?></h2>  
                            
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("address_doc", "local_image") ?>
                                <input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('address', 'local_address'); ?>
                                <div class="controls">
                                    <input type="text" id="local_address" onkeyup="inputUpper(this)"  name="local_address" class="form-control" />
                                </div>
                            </div>
                            
                           
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('pincode', 'local_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="local_pincode"  name="local_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
						</div>
					   <div class="col-md-12">
                            <h2 class="box_he_de"><?= lang('permanent_address') ?></h2>  
                            
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("address_doc", "permanent_image") ?>
                                <input id="permanent_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permanent_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('address', 'permanent_address'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_address"  name="permanent_address" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('pincode', 'permanent_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_pincode" onkeyup="inputUpper(this)" name="permanent_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                           
                        </div>
                         <?php } ?>
                    
                        <div class="col-md-12">  
                        	<h2 class="box_he_de"><?= lang('bank_details') ?></h2>
                            <?php if($regset->account_holder_name_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('account_holder_name', 'account_holder_name'); ?>
                                <div class="controls">
                                    <input type="text" id="account_holder_name" onkeyup="inputFirstUpper(this)" name="account_holder_name"  class="form-control"/>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('account_no', 'account_no'); ?>
                                <div class="controls">
                                    <input type="text" id="account_no" name="account_no" class="form-control"/>
                                </div>
                            </div>
                            <?php if($regset->bank_name_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('bank_name', 'bank_name'); ?>
                                <div class="controls">
                                    <input type="text" id="bank_name" name="bank_name" onkeyup="inputFirstUpper(this)" class="form-control"/>
                                </div>
                            </div>
                             <?php } ?>
                             <?php if($regset->branch_name_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('branch_name', 'branch_name'); ?>
                                <div class="controls">
                                    <input type="text" id="branch_name" name="branch_name" onkeyup="inputFirstUpper(this)" class="form-control"/>
                                </div>
                            </div>
                             <?php } ?>
                             <?php if($regset->ifsc_code_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('ifsc_code', 'ifsc_code'); ?>
                                <div class="controls">
                                    <input type="text" id="ifsc_code" name="ifsc_code" onkeyup="inputUpper(this)" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_ifsc') ?></p>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <div class="col-md-12">  
                        	<h2 class="box_he_de">Cab details</h2>  
                          
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('cab_name', 'cab_name'); ?>
                                <div class="controls">
                                    <input type="text" id="taxi_name" name="taxi_name" onkeyup="inputFirstUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                           <div class="form-group col-sm-3 col-xs-12">
								<?php echo lang('make', 'make'); ?>
                               <!-- <a href="<?= admin_url('masters/add_taxi_make/?is_country='.$_GET['is_country']); ?>" data-toggle="modal" data-target="#newModal"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-plus-circle"></i> </button></a>-->
                                
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
                                <?php echo lang('length', 'length'); ?>
                                <div class="controls input-group">
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
								<?= lang("cab_photo", "taxi_photo") ?>
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
                                    <input type="text" id="reg_due_date" name="reg_due_date" onkeypress="dateCheck(this);" class="form-control"/>
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
                                    <input type="text" id="reg_owner_address" name="reg_owner_address" class="form-control"/>
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
                                    <input type="text" id="permit_no" name="permit_no" onkeyup="inputUpper(this)" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('due_date', 'permit_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="permit_due_date" name="permit_due_date" onkeypress="dateCheck(this);" class="form-control"/>
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
                                    <input type="text" id="authorisation_due_date" name="authorisation_due_date" onkeypress="dateCheck(this);" class="form-control"/>
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
                                    <input type="text" id="puc_due_date" name="puc_due_date" onkeypress="dateCheck(this);"  class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                             <?php } ?>
                         
                         </div>
                    <?php
					}
					?>                  
                </div>

                <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_driver', lang('submit'), 'class="btn btn-primary  change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<div class="modal fade in" id="newModal" tabindex="-1" role="dialog" aria-labelledby="newModalLabel" aria-hidden="true"></div>

<script>
$(document).ready(function(){
	$('.is_country').change(function(){
		
		var site = '<?php echo site_url() ?>';
		var id = '<?= $id ?>';
		var is_country = $(this).val();
		window.location.href = site+"admin/people/add_driver/?is_country="+is_country;
		
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