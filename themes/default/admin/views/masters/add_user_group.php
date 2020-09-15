
<script>
$('form[class="add_from"]').bootstrapValidator({
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                    
                }
            },
            
            
        },
        submitButtons: 'input[type="submit"]'
    });
    </script>










<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('add_user_group'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/add_user_group", $attrib);
                ?>
                <div class="row">
                <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" id="is_country">
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
                    <div class="col-md-6">
                        <div class="col-md-12">                                   
                            <div class="form-group">
                                <?php echo lang('group_name', 'group_name'); ?>
                                <div class="controls">
                                    <input type="text" id="name" name="name" onkeyup="inputFirstUpper(this)" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('description', 'description'); ?>
                                <div class="controls">
                                    <textarea id="description" name="description" class="form-control"></textarea>
                                </div>
                            </div> 
                        </div>
                       
                       
                    </div>                   
                </div>

                <p><?php echo form_submit('add_user_group', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function() { 
	$("#is_country").select2(); 
});
</script>
