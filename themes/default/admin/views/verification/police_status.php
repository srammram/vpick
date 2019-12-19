<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
        
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("verification/police_status/".$id, $attrib);
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                    	<fieldset class="col-md-12">    	
                            <legend>Details</legend>
                                
                            <h2>Police Document</h2>
                            <div class="form-group">
                            	<a href="<?= $result->police_image ?>" class="without-caption image-link">
									<img src="<?= $result->police_image ?>" width="400" height="400" />  
								</a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            <input type="hidden" name="document_id" value="<?= $result->document_id ?>">
                            <input type="hidden" name="verification_first_name" value="<?= $result->first_name ?>">
                            
                            
                            <div class="form-group">
                                        <?= lang("Verified", "police_verify"); ?><br>
                                      	<div class="switch-field">
                                
                                            <input type="radio" value="0" id="switch_left2" class="skip" name="police_verify" <?php echo ($result->police_verify==0) ? "checked" : ''; ?>>
                                            <label for="switch_left2">OFF</label>
                                            <input type="radio" value="1" id="switch_right2" class="skip" name="police_verify" <?php echo ($result->police_verify==1) ? "checked" : ''; ?>>
                                            <label for="switch_right2">ON</label>
                                            
                                        </div>
                                    </div>
                            <div class="form-group">
                                <?php echo lang('police_on', 'police_on'); ?>
                                <div class="controls">
                                    <input type="text" id="police_on" onkeypress="dateCheck(this);"  name="police_on" value="<?= $result->police_on == '0000-00-00' || $result->police_on == NULL ? '' :$result->police_on ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('police_til', 'police_til'); ?>
                                <div class="controls">
                                    <input type="text" id="police_til" onkeypress="dateCheck(this);"  value="<?= $result->police_til == '0000-00-00' || $result->police_til == NULL ? '' :$result->police_til ?>" name="police_til" class="form-control"/>
                                </div>
                            </div>       
                          </fieldset>
                         
                       
                        
                     </div>
                     
                     
                 </div>
                <?php echo form_submit('police_status', lang('submit'), 'class="btn btn-primary"'); ?>
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

