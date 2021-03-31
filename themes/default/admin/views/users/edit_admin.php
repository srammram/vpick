<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_admin'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('edit_admin'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("users/edit_admin/".$user_id, $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12">  
                        	
                            
                            
                            <h2 class="row">User details</h2>  
                            
                                                                              
                            <div class="form-group">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <input type="text"  onkeyup="inputFirstUpper(this)" id="first_name" name="first_name" value="<?= $result->first_name ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <input type="text"  onkeyup="inputFirstUpper(this)" id="last_name" name="last_name" value="<?= $result->last_name ?>" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="text" id="email" name="email" value="<?= $result->email ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            
                            
                            
                            <div class="form-group">
                                <?php echo lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = array('male' => lang('male'), 'female' => lang('female'), 'others' => lang('others'));
                                echo form_dropdown('gender', $ge, $result->gender, 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('dob', 'dob'); ?>
                                <div class="controls">
                                    <input type="text" id="dob" name="dob" onkeypress="dateCheck(this);" value="<?= $result->dob == '0000-00-00' || $result->dob == NULL ? '' :$result->dob ?>" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small">Example: Date formate: DD/MM/YYYY</p>
                            </div>
                            
                            <div class="form-group">
                            	<div class="form-group">
                                <a href="<?= $result->photo_img ?>" class="without-caption image-link">
                                    <img src="<?= $result->photo_img ?>" width="400" height="400" />  
                                </a>
                                <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                                </div>
                            </div>
                            
                            <div class="form-group all">
								<?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                                       <p class="help-block-small">profile image upload size 100 X 100</p>      
                            </div>
                            
                            
                            
                            
                           
                            
                        </div>
                    </div> 
                    
                   
                                      
                </div>

                <p><?php echo form_submit('edit_admin', lang('edit_admin'), 'class="btn btn-primary pull-right"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>

	
$(document).ready(function(){
	
	$('.vendor_and_driver').hide();
	
});
</script>