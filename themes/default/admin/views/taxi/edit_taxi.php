<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("taxi/edit_taxi/".$id, $attrib);
				$regset = $this->site->RegsiterSettings($result->is_country);
                ?>
                 <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $result->vendor_id ? $result->vendor_id : 0 ?>" class="form-control" />
                <div class="row">
                <div class="form-group col-lg-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country" >
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
                        <div class="col-md-12">  
                        	<h2 class="box_he_de"><?= lang('cab_details') ?></h2>  
                            
                            <div class="form-group col-sm-3 col-xs-12">
                            	<div class="form-group">
									<?php echo lang('name', 'name'); ?>
									<div class="controls">
										<input type="text" id="taxi_name" name="taxi_name" onkeyup="inputFirstUpper(this)" class="form-control" value="<?= $result->name ?>" required="required"/>
									</div>
								</div>
                                
                            	<div class="form-group">
									<?php echo lang('make', 'make'); ?>
									<?php

									foreach ($makes as $make) {
										$mk[$make->id] = $make->name;
									}

									echo form_dropdown('make', $mk, $result->make_id, 'class=" select-make select form-control" id="make" data-placeholder="' . lang("select") . ' ' . lang("make") . '" ');
									?>
								</div>
                                
                               <div class="form-group">
                                <?php echo lang('category', 'category'); ?>
                                <?php
								$cat[] = 'Select Category';
                                foreach ($categorys as $category) {
                                    $cat[$category->id] = $category->name;
                                }

                                echo form_dropdown('category', $cat, $result->category, 'class=" select-category select form-control" id="category" data-placeholder="' . lang("select") . ' ' . lang("category") . '" ');
                                ?>
                            </div>
                            
                             <div class="form-group ">
                                <?php echo lang('type', 'type'); ?>
                                <?php
                                 
								foreach ($types as $type) {
									$t[$type->id] = $type->name;
								}
								
                                echo form_dropdown('type', $t, $result->type, 'class="select-type select form-control" id="type" data-placeholder="' . lang("select") . ' ' . lang("type") . '" ');
                                ?>
                            </div>
								<!--<div class="form-group">
									<?php echo lang('name', 'name'); ?>
									<div class="controls">
										<input type="text" id="taxi_name" name="taxi_name" onkeyup="inputFirstUpper(this)" value="<?= $result->name ?>" class="form-control" required="required"/>
									</div>
								</div>-->
								<div class="form-group">
									<?php echo lang('model', 'model'); ?>
                                    <?php
								foreach ($models as $model) {
									$mds[$model->id] = $model->name;
								}
								
                                echo form_dropdown('model', $mds, $result->model, 'class="select-model select form-control" id="model" data-placeholder="' . lang("select") . ' ' . lang("model") . '" '); ?>
									<!--<div class="controls">
										<input type="text" id="model" name="model" onkeyup="inputFirstUpper(this)" value="<?= $result->model ?>" class="form-control" required="required"/>
									</div>-->
								</div>
								 <div class="form-group">
									<?php echo lang('vehicle_number', 'vehicle_number'); ?>
									<div class="controls">
										<input type="text" id="number" name="number" onkeyup="inputUpper(this)" value="<?= $result->number ?>" class="form-control" required="required"/>
									</div>
								</div>
								<div class="form-group">
									<?php echo lang('engine_number', 'engine_number'); ?>
									<div class="controls">
										<input type="text" id="engine_number" onkeyup="inputUpper(this)" name="engine_number" value="<?= $result->engine_number ?>" class="form-control" required="required"/>
									</div>
								</div>
							</div>
                            
							<div class="form-group col-sm-3 col-xs-12">
								<div class="form-group">
									<?php echo lang('chassis_number', 'chassis_number'); ?>
									<div class="controls">
										<input type="text" id="chassis_number" onkeyup="inputUpper(this)" name="chassis_number" value="<?= $result->chassis_number ?>" class="form-control" required="required"/>
									</div>
								</div>
								
								<div class="form-group">
									<?php echo lang('color', 'color'); ?>
									<div class="controls">
										<input type="text" id="color" name="color" onkeyup="inputUpper(this)" class="form-control" value="<?= $result->color ?>" required="required"/>
									</div>
								</div>
								<div class="form-group ">
									<?php echo lang('manufacture_year', 'manufacture_year'); ?>
									<div class="controls">
										<input type="text" id="manufacture_year" name="manufacture_year" value="<?= $result->manufacture_year ?>" class="form-control" required="required"/>
									</div>
								</div>
                                
                                <div class="form-group">
                                <?php echo lang('width', 'width'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="width" name="width" class="form-control" value="<?= $result->width ?>" required="required"/>
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
                            <div class="form-group">
                                <?php echo lang('height', 'height'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="height" name="height" class="form-control" value="<?= $result->height ?>" required="required"/>
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
                            <div class="form-group">
                                <?php echo lang('length', 'length'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="length" name="length" class="form-control" value="<?= $result->length ?>" required="required"/>
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
                            <div class="form-group">
                                <?php echo lang('normal_weight', 'weight'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="weight" name="weight" class="form-control" value="<?= $result->weight ?>" required="required"/>
                                    <span class="input-group-addon">
									<?= $Settings->taxi_bodyweight_formate == 1 ? 'Kilogram' : 'Tonne'; ?>		
									</span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('min_weight', 'min_weight'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="min_weight" name="min_weight" value="<?= $result->min_weight ?>" class="form-control" required="required"/>
                                    <span class="input-group-addon">
									<?= $Settings->taxi_bodyweight_formate == 1 ? 'Kilogram' : 'Tonne'; ?>		
									</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('max_weight', 'max_weight'); ?>
                                <div class="controls input-group">
                                    <input type="text" id="max_weight" name="max_weight" value="<?= $result->max_weight ?>" class="form-control" required="required"/>
                                    <span class="input-group-addon">
									<?= $Settings->taxi_bodyweight_formate == 1 ? 'Kilogram' : 'Tonne'; ?>		
									</span>
                                </div>
                            </div>
                            
							</div>
                            
                            
							<div class="form-group col-sm-3 col-xs-12">
								
                               
                                
								
								
								<div class="form-group">
									<?php echo lang('fuel_type', 'fuel_type'); ?>
									<?php

									foreach ($fuel_types as $fuel_type) {
										$ft[$fuel_type->id] = $fuel_type->name;
									}

									echo form_dropdown('fuel_type', $ft, $result->fuel_type, 'class="tip form-control" id="fuel_type" data-placeholder="' . lang("select") . ' ' . lang("fuel_type") . '"  ');
									?>
								</div>
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
											<img src="<?= $result->photo_img  ?>" class="img"  data-large-img-url="<?= $result->photo_img  ?>" data-large-img-wrapper="preview">  
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
                    	<div class="col-md-12">  
                        	<h2 class="box_he_de"><?= lang('cab_document') ?></h2>  
                            <?php if($regset->cab_registration_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
								<div class="form-group all">
									<?= lang("registration_document", "reg_image") ?>
									<input id="reg_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="reg_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
								<div class="form-group">
									<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											<a href="<?= $result->reg_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result->reg_image_img  ?>" class="img"  data-large-img-url="<?= $result->reg_image_img  ?>" data-large-img-wrapper="preview1">    
											</a>
										</div>
									</div>
									<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
										<span class="pull-left">
										<input type="file" id="selectedFile" style="display: none;" />
										<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
									   </span> <i class="fa fa-rotate-right pull-right"></i>
									</button>
									<div class="magnifier-preview" id="preview1" style="width: 300px; height:300px;position: absolute;right: -90%;"></div>
								</div>
								
								<div class="form-group">
									<?php echo lang('registration_date', 'reg_date'); ?>
									<div class="controls">
										<input type="text" id="reg_date" name="reg_date" onkeypress="dateCheck(this);" value="<?=$result->reg_date ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_date') ?></p>
								</div>
								 <div class="form-group">
									<?php echo lang('registration_due_date', 'reg_due_date'); ?>
									<div class="controls">
										<input type="text" id="reg_due_date" name="reg_due_date" onkeypress="dateCheck(this);" value="<?=$result->reg_due_date ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_date') ?></p>
								</div>
								
							</div>
                            <?php } ?>
                            
                            <?php if($regset->insurance_enable == 1){ ?>
							<div class="form-group col-sm-3 col-xs-12">
								
								<div class="form-group">
								 	<div class="form-group all">
										<?= lang("insurance_document", "insurance_image") ?>
										<input id="insurance_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="insurance_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
									<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											<a href="<?= $result->insurance_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result->insurance_image_img  ?>" class="img"  data-large-img-url="<?= $result->insurance_image_img  ?>" data-large-img-wrapper="preview2"> 
											</a>
										</div>
									</div>
									<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
										<span class="pull-left">
										<input type="file" id="selectedFile" style="display: none;" />
										<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
									   </span> <i class="fa fa-rotate-right pull-right"></i>
									</button>
									<div class="magnifier-preview" id="preview2" style="width: 300px; height:300px;position: absolute;right: -90%;"></div>
								</div>
                            
								<div class="form-group">
									<?php echo lang('policy_no', 'insurance_policy_no'); ?>
									<div class="controls">
										<input type="text" id="insurance_policy_no" onkeyup="inputUpper(this)" name="insurance_policy_no" value="<?= $result->insurance_policy_no ?>" class="form-control"/>
									</div>
								</div>
                            
								
							</div>
                            <?php } ?>
                            
                            <?php if($regset->authorisation_enable == 1){ ?>
							<div class="form-group col-sm-3 col-xs-12">
								<div class="form-group">
									<div class="form-group all">
										<?= lang("authorisation_document", "authorisation_image") ?>
										<input id="authorisation_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="authorisation_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
									<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											<a href="<?= $result->authorisation_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result->authorisation_image_img  ?>" class="img"  data-large-img-url="<?= $result->authorisation_image_img  ?>" data-large-img-wrapper="preview3">  
											</a>
										</div>
									</div>
									<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
										<span class="pull-left">
										<input type="file" id="selectedFile" style="display: none;" />
										<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
									   </span> <i class="fa fa-rotate-right pull-right"></i>
									</button>
									<div class="magnifier-preview" id="preview3" style="width: 300px; height:300px;position: absolute;right:-90%;"></div>
								</div>
                            <div class="form-group">
                                <?php echo lang('authorisation_no', 'authorisation_no'); ?>
                                <div class="controls">
                                    <input type="text" id="authorisation_no" onkeyup="inputUpper(this)" name="authorisation_no" value="<?= $result->authorisation_no ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'authorisation_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="authorisation_due_date" name="authorisation_due_date" onkeypress="dateCheck(this);" value="<?=$result->authorisation_due_date ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
							</div>
                            <?php } ?>
                            
<?php if($regset->puc_enable == 1){ ?>
                            <div class="form-group col-sm-3 col-xs-12">
								
								<div class="form-group">
									<div class="form-group all">
										<?= lang("puc_document", "puc_image") ?>
										<input id="puc_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="puc_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
									<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											 <a href="<?= $result->puc_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result->puc_image_img  ?>" class="img"  data-large-img-url="<?= $result->permanent_image_img ?>" data-large-img-wrapper="preview4">  
											</a>
										</div>
									</div>
									<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
										<span class="pull-left">
										<input type="file" id="selectedFile" style="display: none;" />
										<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
									   </span> <i class="fa fa-rotate-right pull-right"></i>
									</button>
									<div class="magnifier-preview" id="preview4" style="width: 300px; height:300px;position: absolute;left:-90%;"></div>
								</div>
								
								<div class="form-group">
									<?php echo lang('due_date', 'puc_due_date'); ?>
									<div class="controls">
										<input type="text" id="puc_due_date" name="puc_due_date" onkeypress="dateCheck(this);" value="<?=$result->puc_due_date ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_date') ?></p>
								</div>
								 
                         </div>
                          <?php } ?>
                          
					<div class="col-md-12">
                    <?php if($regset->taxation_enable == 1){ ?>
						<div class="form-group col-sm-3 col-xs-12">
							<div class="form-group">
									<div class="form-group all">
										<?= lang("taxation_document", "taxation_image") ?>
										<input id="taxation_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="taxation_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
									<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											<a href="<?= $result->taxation_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result->taxation_image_img  ?>" class="img"  data-large-img-url="<?= $result->taxation_image_img  ?>" data-large-img-wrapper="preview5">   
											</a>
										</div>
									</div>
									<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
										<span class="pull-left">
										<input type="file" id="selectedFile" style="display: none;" />
										<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
									   </span> <i class="fa fa-rotate-right pull-right"></i>
									</button>
									<div class="magnifier-preview" id="preview5" style="width: 300px; height:300px;position: absolute;right:-90%;"></div>
								</div>
								<div class="form-group">
									<?php echo lang('owner_name', 'reg_owner_name'); ?>
									<div class="controls">
										<input type="text" id="reg_owner_name" onkeyup="inputFirstUpper(this)" name="reg_owner_name" value="<?= $result->reg_owner_name ?>" class="form-control"/>
									</div>
								</div>
								<div class="form-group">
									<?php echo lang('owner_address', 'reg_owner_address'); ?>
									<div class="controls">
										<input type="text" id="reg_owner_address" onkeyup="inputFirstUpper(this)" name="reg_owner_address" value="<?= $result->reg_owner_address ?>" class="form-control"/>
									</div>
								</div>
								<div class="form-group">
									<?php echo lang('amount_paid', 'taxation_amount_paid'); ?>
									<div class="controls">
										<input type="text" id="taxation_amount_paid" name="taxation_amount_paid" value="<?= $result->taxation_amount_paid ?>" class="form-control"/>
									</div>
								</div>
								<div class="form-group">
									<?php echo lang('due_date', 'taxation_due_date'); ?>
									<div class="controls">
										<input type="text" id="taxation_due_date" name="taxation_due_date" onkeypress="dateCheck(this);" value="<?=$result->taxation_due_date ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_date') ?></p>
								</div>
						</div>
                        <?php } ?>
                        
                        <?php if($regset->fitness_enable == 1){ ?>
						<div class="form-group col-sm-3 col-xs-12">
							<div class="form-group">
								<div class="form-group all">
									<?= lang("fitness_document", "fitness_image") ?>
									<input id="fitness_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="fitness_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
								<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
										<a href="<?= $result->fitness_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result->fitness_image_img  ?>" class="img"  data-large-img-url="<?= $result->fitness_image_img  ?>" data-large-img-wrapper="preview6">  
										</a>
									</div>
								</div>
                                <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
								<div class="magnifier-preview" id="preview6" style="width: 300px; height:300px;position: absolute;right:-90%;"></div>
							</div>
                            <div class="form-group">
                                <?php echo lang('due_date', 'fitness_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="fitness_due_date" name="fitness_due_date" onkeypress="dateCheck(this);" value="<?=$result->fitness_due_date ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                             
						</div>
                        
                        <?php } ?>
                        <?php if($regset->permit_enable == 1){ ?>
						<div class="form-group col-sm-3 col-xs-12">
							<div class="form-group">
									<div class="form-group all">
										<?= lang("permit_document", "permit_image") ?>
										<input id="permit_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permit_image" data-show-upload="false"
											   data-show-preview="false" class="form-control file" accept="im/*">
									</div>
									<div class="col-sm-12 img_box_se_head">
										<div class="img_box_se">
											<a href="<?= $result->permit_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
												<img src="<?= $result->permit_image_img  ?>" class="img"  data-large-img-url="<?= $result->permanent_image_img ?>" data-large-img-wrapper="preview7">  
											</a>
										</div>
									</div>
									<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
										<span class="pull-left">
										<input type="file" id="selectedFile" style="display: none;" />
										<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
									   </span> <i class="fa fa-rotate-right pull-right"></i>
									</button>
									<div class="magnifier-preview" id="preview7" style="width: 300px; height:300px;position: absolute;right: -90%;"></div>
								</div>
                            
								<div class="form-group">
									<?php echo lang('due_date', 'insurance_due_date'); ?>
									<div class="controls">
										<input type="text" id="insurance_due_date" name="insurance_due_date" onkeypress="dateCheck(this);" value="<?=$result->insurance_due_date ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_date') ?></p>
								</div>
                            
                            
                            <div class="form-group">
                                <?php echo lang('permit_no', 'permit_no'); ?>
                                <div class="controls">
                                    <input type="text" id="permit_no" onkeyup="inputUpper(this)" name="permit_no" value="<?= $result->permit_no ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'permit_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="permit_due_date" name="permit_due_date" onkeypress="dateCheck(this);" value="<?= $result->permit_due_date == '0000-00-00' || $result->permit_due_date == NULL ? '' :$result->permit_due_date ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
						</div>
                        
                         <?php } ?>
                         <?php if($regset->speed_enable == 1){ ?>
						<div class="form-group col-sm-3 col-xs-12">
						<div class="form-group">
								 <div class="form-group all">
									<?= lang("speed_document", "speed_image") ?>
									<input id="speed_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="speed_image" data-show-upload="false"
										   data-show-preview="false" class="form-control file" accept="im/*">
								</div>
								<div class="col-sm-12 img_box_se_head">
									<div class="img_box_se">
										 <a href="<?= $result->speed_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
											<img src="<?= $result->speed_image_img  ?>" class="img"  data-large-img-url="<?= $result->speed_image_img  ?>" data-large-img-wrapper="preview8">   
										</a>
									</div>
								</div>
                                <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
									<span class="pull-left">
									<input type="file" id="selectedFile" style="display: none;" />
									<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
								   </span> <i class="fa fa-rotate-right pull-right"></i>
								</button>
								<div class="magnifier-preview" id="preview8" style="width: 300px; height:300px;position: absolute;left: -90%;"></div>
							</div>
                            <div class="form-group">
									<?php echo lang('due_date', 'speed_due_date'); ?>
									<div class="controls">
										<input type="text" id="speed_due_date" name="speed_due_date" onkeypress="dateCheck(this);" value="<?=$result->speed_due_date ?>" class="form-control"/>
									</div>
									<p class="help-block-small"><?= lang('ex_date') ?></p>
								</div>
							<div class="form-group">
									<?= lang("cityrides", "cityrides"); ?><br>
									<div class="switch-field">


										<input type="radio" value="0" id="switch_left_is_daily" class="skip" name="is_daily" <?php echo ($result->is_daily==0) ? "checked" : ''; ?>>
										<label for="switch_left_is_daily">OFF</label>
										<input type="radio" value="1" id="switch_right_is_daily" class="skip" name="is_daily" <?php echo ($result->is_daily==1) ? "checked" : ''; ?>>
										<label for="switch_right_is_daily">ON</label>

									</div>
								</div>
								<div class="clearfix"></div>
								<!--<div class="form-group">
									<?= lang("rental", "Rental"); ?><br>
									<div class="switch-field">

										<input type="radio" value="0" id="switch_left_is_rental" class="skip" name="is_rental" <?php echo ($result->is_rental==0) ? "checked" : ''; ?>>
										<label for="switch_left_is_rental">OFF</label>
										<input type="radio" value="1" id="switch_right_is_rental" class="skip" name="is_rental" <?php echo ($result->is_rental==1) ? "checked" : ''; ?>>
										<label for="switch_right_is_rental">ON</label>

									</div>
								</div>-->
								<div class="clearfix"></div>
                            		<div class="form-group">
                                        <?= lang("outstation", "Outstation"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left_is_outstation" class="skip" name="is_outstation" <?php echo ($result->is_outstation==0) ? "checked" : ''; ?>>
                                            <label for="switch_left_is_outstation">OFF</label>
                                            <input type="radio" value="1" id="switch_right_is_outstation" class="skip" name="is_outstation" <?php echo ($result->is_outstation==1) ? "checked" : ''; ?>>
                                            <label for="switch_right_is_verify">ON</label>
                                            
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                          <!--<div class="form-group">
                                        <?= lang("Hiring", "Hiring"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left_is_hiring" class="skip" name="is_hiring" <?php echo ($result->is_hiring==0) ? "checked" : ''; ?>>
                                            <label for="switch_left_is_hiring">OFF</label>
                                            <input type="radio" value="1" id="switch_right_is_hiring" class="skip" name="is_hiring" <?php echo ($result->is_hiring==1) ? "checked" : ''; ?>>
                                            <label for="switch_right_is_hiring">ON</label>
                                            
                                        </div>
                                    </div>-->
                            
                          
                            </div>
                            <?php } ?>
                            
						</div>
					</div>
                </div>
				<?php if($view == ''){ ?>
                <div class="col-sm-12 last_sa_se"><?php echo form_submit('edit_taxi', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
                <?php } ?>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<style>
<style>
.switch-field {
  position: absolute;
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
</style>
<script>

	
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