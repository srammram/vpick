<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
        
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("verification/taxi_status/".$id, $attrib);
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                    	<fieldset class="col-md-12">    	
                            <legend>Taxi Details</legend>
                                
                            
                            <input type="hidden" name="taxi_id" value="<?= $id ?>">
                            <input type="hidden" name="verification_first_name" value="<?= $result->first_name ?>">
                            <input type="hidden" name="reg_verify" value="1">
                            <input type="hidden" name="document_id" value="<?= $result_doc->document_id ?>">
                            
                            <div class="form-group">
                                        <?= lang("Verified", "is_verify"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left2" class="skip" name="is_verify" <?php echo ($result->is_verify==0) ? "checked" : ''; ?>>
                                            <label for="switch_left2">OFF</label>
                                            <input type="radio" value="1" id="switch_right2" class="skip" name="is_verify" <?php echo ($result->is_verify==1) ? "checked" : ''; ?>>
                                            <label for="switch_right2">ON</label>
                                            
                                        </div>
                                    </div>
                            
                            <div class="form-group">
                                <?php echo lang('name', 'name'); ?>
                                <div class="controls">
                                    <input type="text" id="name" onkeyup="inputFirstUpper(this)" name="name" value="<?= $result->name ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('model', 'model'); ?>
                                <div class="controls">
                                    <input type="text" id="model" onkeyup="inputFirstUpper(this)" name="model" value="<?= $result->model ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('number', 'number'); ?>
                                <div class="controls">
                                    <input type="text" id="number" onkeyup="inputUpper(this)" name="number" value="<?= $result->number ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('engine_number', 'engine_number'); ?>
                                <div class="controls">
                                    <input type="text" id="engine_number" onkeyup="inputUpper(this)" name="engine_number" value="<?= $result->engine_number ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('chassis_number', 'chassis_number'); ?>
                                <div class="controls">
                                    <input type="text" id="chassis_number" onkeyup="inputUpper(this)" name="chassis_number" value="<?= $result->chassis_number ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('make', 'make'); ?>
                                <div class="controls">
                                    <input type="text" id="make" name="make" onkeyup="inputFirstUpper(this)" class="form-control" value="<?= $result->make ?>" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('color', 'color'); ?>
                                <div class="controls">
                                    <input type="text" id="color" name="color" onkeyup="inputUpper(this)" class="form-control" value="<?= $result->color ?>" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('manufacture_year', 'manufacture_year'); ?>
                                <div class="controls">
                                    <input type="text" id="manufacture_year" name="manufacture_year" value="<?= $result->manufacture_year ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                           
                           
                            <div class="form-group">
                                <?php echo lang('seat_capacity', 'capacity'); ?>
                                <div class="controls row">
                                    <div class="col-lg-5"><input type="text" id="capacity_number" value="<?= $result->capacity - 1 ?>" name="capacity_number" class="form-control" required="required"/></div>
                                    <div class="col-lg-2" style="padding:5px 0px; text-align:center;"><strong> + 1</strong></div>
                                    
                                    <div class="col-lg-5"><input type="text" id="capacity" readonly name="capacity" value="<?= $result->capacity ?>" class="form-control"/></div>
                                </div>
                            </div>
                            <!--<div class="form-group">
                                <?php echo lang('ac/non_ac', 'ac'); ?>
                                <?php
                                $ac[''] = array('0' => lang('Non AC'), '1' => lang('AC'));
                                echo form_dropdown('ac', $ac, '', 'class="tip form-control" id="ac" data-placeholder="' . lang("select") . ' ' . lang("ac/non_ac") . '" required="required"');
                                ?>
                            </div>-->
                            
                            <div class="form-group">
                                <?php echo lang('type', 'type'); ?>
                                <?php
                                 
								foreach ($types as $type) {
									$t[$type->id] = $type->name;
								}
								
                                echo form_dropdown('type', $t, $result->type, 'class="tip form-control" id="type" data-placeholder="' . lang("select") . ' ' . lang("type") . '" ');
                                ?>
                            </div>
                            
                           
                            <div class="form-group">
                                <?php echo lang('fuel_type', 'fuel_type'); ?>
                                <?php
                                 
								foreach ($fuel_types as $fuel_type) {
									$ft[$fuel_type->id] = $fuel_type->name;
								}
								
                                echo form_dropdown('fuel_type', $ft, $result->fuel_type, 'class="tip form-control" id="fuel_type" data-placeholder="' . lang("select") . ' ' . lang("fuel_type") . '"  ');
                                ?>
                            </div>
                            <div class="form-group">
                            	<a href="<?= $result->photo ?>" class="without-caption image-link">
									<img src="<?= $result->photo ?>" width="400" height="400" />  
								</a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                           
                            <div class="form-group all">
								<?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>  
                            
                            <div class="form-group">
                                <?php echo lang('reg_date', 'reg_date'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_date" name="reg_date" onkeypress="dateCheck(this);" value="<?= $result->reg_date == '0000-00-00' || $result->reg_date == NULL ? '' :$result->reg_date ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('reg_due_date', 'reg_due_date'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_due_date" name="reg_due_date" onkeypress="dateCheck(this);" value="<?= $result->reg_due_date == '0000-00-00' || $result->reg_due_date == NULL ? '' :$result->reg_due_date ?>" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('reg_owner_name', 'reg_owner_name'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_owner_name" onkeyup="inputFirstUpper(this)" name="reg_owner_name" value="<?= $result_doc->reg_owner_name ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('reg_owner_address', 'reg_owner_address'); ?>
                                <div class="controls">
                                    <input type="text" id="reg_owner_address" onkeyup="inputFirstUpper(this)" name="reg_owner_address" value="<?= $result_doc->reg_owner_address ?>" class="form-control"/>
                                </div>
                            </div>        
                          </fieldset>
                         
                       
                        
                     </div>
                    
                    <div class="col-lg-6">
                    	<h2>Taxi Document</h2>
                        	<div class="form-group">
							<a href="<?= $result->reg_image ?>" class="without-caption image-link">
								<img src="<?= $result->reg_image ?>" width="400" height="400" />  
							</a>
                            <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                            </div>
                            <div class="form-group all">
								<?= lang("reg_image", "reg_image") ?>
                                <input id="reg_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="reg_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                    </div> 
                     
                 </div>
                <?php echo form_submit('taxi_status', lang('submit'), 'class="btn btn-primary"'); ?>
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

