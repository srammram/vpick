<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('add_cab'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'add_from',  'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("taxi/add_taxi", $attrib);
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
                    <div class="col-md-5">
                        <div class="col-md-12">  
                        	<h2 class="row"><?= lang('cab_details') ?></h2>  
                            
                            <div class="form-group">
                                <?php echo lang('vendor', 'vendor'); ?>
                                <?php
                                $cc[''] = 'Select Vendor';
								foreach ($vendors as $vendor) {
									$cc[$vendor->id] = $vendor->first_name.' '.$vendor->last_name;
								}
								
                                echo form_dropdown('vendor_id', $cc, '', 'class="tip form-control" id="vendor_id" data-placeholder="' . lang("select") . ' ' . lang("vendor") . 'required="required" ');
                                ?>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('name', 'name'); ?>
                                <div class="controls">
                                    <input type="text" id="taxi_name" name="taxi_name" onkeyup="inputFirstUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('model', 'model'); ?>
                                <div class="controls">
                                    <input type="text" id="model" name="model" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('vehicle_number', 'vehicle_number'); ?>
                                <div class="controls">
                                    <input type="text" id="number" name="number" onkeyup="inputUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('engine_number', 'engine_number'); ?>
                                <div class="controls">
                                    <input type="text" id="engine_number" name="engine_number" onkeyup="inputUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('chassis_number', 'chassis_number'); ?>
                                <div class="controls">
                                    <input type="text" id="chassis_number" onkeyup="inputUpper(this)" name="chassis_number" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('make', 'make'); ?>
                                <div class="controls">
                                    <input type="text" id="make" name="make" onkeyup="inputFirstUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('color', 'color'); ?>
                                <div class="controls">
                                    <input type="text" id="color" name="color" onkeyup="inputUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('manufacture_year', 'manufacture_year'); ?>
                                <div class="controls">
                                    <input type="text" id="manufacture_year" name="manufacture_year" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('width', 'width'); ?>
                                <div class="controls">
                                    <input type="text" id="width" name="width" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('height', 'height'); ?>
                                <div class="controls">
                                    <input type="text" id="height" name="height" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('length', 'length'); ?>
                                <div class="controls">
                                    <input type="text" id="length" name="length" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('normal_weight', 'weight'); ?>
                                <div class="controls">
                                    <input type="text" id="weight" name="weight" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('min_weight', 'min_weight'); ?>
                                <div class="controls">
                                    <input type="text" id="min_weight" name="min_weight" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('max_weight', 'max_weight'); ?>
                                <div class="controls">
                                    <input type="text" id="max_weight" name="max_weight" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('capacity', 'capacity'); ?>
                                <div class="controls">
                                    <input type="text" id="capacity" name="capacity" class="form-control" required="required"/>
                                </div>
                            </div>
                            <!--<div class="form-group">
                                <?php //echo lang('ac/non_ac', 'ac'); ?>
                                <?php
                                //$ac[''] = array('0' => lang('Non AC'), '1' => lang('AC'));
                                //echo form_dropdown('ac', $ac, '', 'class="tip form-control" id="ac" data-placeholder="' . lang("select") . ' ' . lang("ac/non_ac") . '" required="required"');
                                ?>
                            </div>-->
                            
                            <div class="form-group">
                                <?php echo lang('type', 'type'); ?>
                                <?php
                                 
								foreach ($types as $type) {
									$t[$type->id] = $type->name;
								}
								
                                echo form_dropdown('type', $t, '', 'class="tip form-control" id="type" data-placeholder="' . lang("select") . ' ' . lang("type") . '" ');
                                ?>
                            </div>
                            
                            
                            <div class="form-group">
                                <?php echo lang('fuel_type', 'fuel_type'); ?>
                                <?php
                                 
								foreach ($fuel_types as $fuel_type) {
									$ft[$fuel_type->id] = $fuel_type->name;
								}
								
                                echo form_dropdown('fuel_type', $ft, '', 'class="tip form-control" id="fuel_type" data-placeholder="' . lang("select") . ' ' . lang("fuel_type") . '"  ');
                                ?>
                            </div>
                            
                            
                            <div class="form-group all">
								<?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                        </div>
                    </div> 
                    
                    <div class="col-md-5 col-md-offset-1">
                    	<div class="col-md-12">  
                        	<h2 class="row"><?= lang('cab_document') ?></h2>  
                            
                            <div class="form-group all">
								<?= lang("registration_document", "reg_image") ?>
                                <input id="reg_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="reg_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('registration_date', 'reg_date'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_date" name="reg_date" onkeypress="dateCheck(this);" class="form-control" />
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('registration_due_date', 'reg_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_due_date" name="reg_due_date" onkeypress="dateCheck(this);" class="form-control" />
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('owner_name', 'reg_owner_name'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_owner_name" onkeyup="inputFirstUpper(this)" name="reg_owner_name" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('owner_address', 'reg_owner_address'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_owner_address" onkeyup="inputFirstUpper(this)" name="reg_owner_address" class="form-control"/>
                                </div>
                            </div>
                           
                            <div class="form-group all">
								<?= lang("taxation_document", "taxation_image") ?>
                                <input id="taxation_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="taxation_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('taxation_amount', 'taxation_amount_paid'); ?>
                                <div class="controls">
                                    <input type="text" id="taxation_amount_paid" name="taxation_amount_paid" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'taxation_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="taxation_due_date" name="taxation_due_date"   onkeypress="dateCheck(this);" class="form-control" />
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("insurance_document", "insurance_image") ?>
                                <input id="insurance_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="insurance_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('policy_no', 'insurance_policy_no'); ?>
                                <div class="controls">
                                    <input type="text" id="insurance_policy_no" onkeyup="inputUpper(this)" name="insurance_policy_no" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'insurance_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="insurance_due_date" name="insurance_due_date" onkeypress="dateCheck(this);" class="form-control" />
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("permit_document", "permit_image") ?>
                                <input id="permit_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permit_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('permit_no', 'permit_no'); ?>
                                <div class="controls">
                                    <input type="text" id="permit_no" name="permit_no" onkeyup="inputUpper(this)" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'permit_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="permit_due_date" name="permit_due_date" onkeypress="dateCheck(this);" class="form-control" />
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            
                            <div class="form-group all">
								<?= lang("authorisation_document", "authorisation_image") ?>
                                <input id="authorisation_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="authorisation_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('authorisation_number', 'authorisation_no'); ?>
                                <div class="controls">
                                    <input type="text" id="authorisation_no" onkeyup="inputUpper(this)" name="authorisation_no" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'authorisation_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="authorisation_due_date" name="authorisation_due_date" onkeypress="dateCheck(this);" class="form-control" />
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("fitness_document", "fitness_image") ?>
                                <input id="fitness_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="fitness_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'fitness_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="fitness_due_date" name="fitness_due_date" onkeypress="dateCheck(this);" class="form-control" />
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("speed_document", "speed_image") ?>
                                <input id="speed_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="speed_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'speed_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="speed_due_date" name="speed_due_date" onkeypress="dateCheck(this);" class="form-control" />
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("puc_document", "puc_image") ?>
                                <input id="puc_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="puc_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('due_date', 'puc_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="puc_due_date" name="puc_due_date" onkeypress="dateCheck(this);" class="form-control" />
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
                            <div class="form-group">
                                        <?= lang("rental", "Rental"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left_is_rental" class="skip" name="is_rental" <?php echo ($result->is_rental==0) ? "checked" : ''; ?>>
                                            <label for="switch_left_is_rental">OFF</label>
                                            <input type="radio" value="1" id="switch_right_is_rental" class="skip" name="is_rental" <?php echo ($result->is_rental==1) ? "checked" : ''; ?>>
                                            <label for="switch_right_is_rental">ON</label>
                                            
                                        </div>
                                    </div>
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
                         <!-- <div class="form-group">
                                        <?= lang("Hiring", "Hiring"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left_is_hiring" class="skip" name="is_hiring" <?php echo ($result->is_hiring==0) ? "checked" : ''; ?>>
                                            <label for="switch_left_is_hiring">OFF</label>
                                            <input type="radio" value="1" id="switch_right_is_hiring" class="skip" name="is_hiring" <?php echo ($result->is_hiring==1) ? "checked" : ''; ?>>
                                            <label for="switch_right_is_hiring">ON</label>
                                            
                                        </div>
                                    </div>-->
                            
                         </div>
                    </div>
                                      
                </div>

                <p><?php echo form_submit('add_taxi', lang('submit'), 'class="btn btn-primary pull-right"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>

	
$(document).ready(function(){
	$('.is_country').change(function(){
		
		var site = '<?php echo site_url() ?>';
		var id = '<?= $id ?>';
		var is_country = $(this).val();
		window.location.href = site+"admin/taxi/add_taxi/"?is_country="+is_country;
		
	});
});
	
</script>