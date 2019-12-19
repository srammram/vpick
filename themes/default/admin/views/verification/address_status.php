<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
   
    <div class="box-content">
        <div class="row">
        
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("verification/address_status/".$id, $attrib);
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                    	<fieldset class="col-md-12">    	
                            <legend>Local Address</legend>
                                
                            
                            <input type="hidden" name="address_id" value="<?= $result->address_id ?>">
                            <input type="hidden" name="verification_first_name" value="<?= $result->first_name ?>">
                            
                            <div class="form-group all">
								<?= lang("local_address_doc", "local_image") ?>
                                <input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('address', 'local_address'); ?>
                                <div class="controls">
                                    <input type="text" id="local_address" value="<?= $result->local_address ?>" name="local_address" class="form-control" />
                                </div>
                            </div>
                            
                            
                            
                            <div class="form-group">
                                <?php echo lang('local_pincode', 'local_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="local_pincode" onkeyup="inputUpper(this)" value="<?= $result->local_pincode ?>" name="local_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                        <?= lang("Verified", "local_verify"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left2" class="skip" name="local_verify" <?php echo ($result->local_verify==0) ? "checked" : ''; ?>>
                                            <label for="switch_left2">OFF</label>
                                            <input type="radio" value="1" id="switch_right2" class="skip" name="local_verify" <?php echo ($result->local_verify==1) ? "checked" : ''; ?>>
                                            <label for="switch_right2">ON</label>
                                            
                                        </div>
                                    </div>
                                    
                          </fieldset>
                          <fieldset class="col-md-12">    	
                            <legend>Permanent Address</legend>
                            
                            
                            <div class="form-group all">
								<?= lang("permanent_address_doc", "permanent_image") ?>
                                <input id="permanent_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permanent_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('address', 'permanent_address'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_address" value="<?= $result->permanent_address ?>" name="permanent_address" class="form-control"/>
                                </div>
                            </div>
                            
                            
                            
                            <div class="form-group">
                                <?php echo lang('permanent_pincode', 'permanent_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_pincode" onkeyup="inputUpper(this)" value="<?= $result->permanent_pincode ?>" name="permanent_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            
                                    <div class="form-group">
                                        <?= lang("Verified", "permanent_verify"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left1" class="skip" name="permanent_verify" <?php echo ($result->permanent_verify==0) ? "checked" : ''; ?>>
                                            <label for="switch_left1">OFF</label>
                                            <input type="radio" value="1" id="switch_right1" class="skip" name="permanent_verify" <?php echo ($result->permanent_verify==1) ? "checked" : ''; ?>>
                                            <label for="switch_right1">ON</label>
                                            
                                        </div>
                                    </div>
                               
                            </fieldset>
                            
                    	
                       
                        
                     </div>
                     
                     <div class="col-md-6">
                     	<h2>Local Address Document</h2>
                        <div class="form-group">
							<a href="<?= $result->local_image ?>" class="without-caption image-link">
								<img src="<?= $result->local_image ?>" width="400" height="400" />  
							</a>
                            <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                            </div>
                        <h2>Permanent Address Document</h2>
							<div class="form-group">
                            <a href="<?= $result->permanent_image ?>" class="without-caption image-link">
								<img src="<?= $result->permanent_image ?>" width="400" height="400" />  
							</a>
                            <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                            </div>
                     </div>
                 </div>
                <?php echo form_submit('vendor_status', lang('submit'), 'class="btn btn-primary"'); ?>
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

