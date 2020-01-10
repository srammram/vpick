
<script>
$('form[class="edit_from"]').bootstrapValidator({
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                   
                }
            },
           
            symbol:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The symbol'
                    }
                }
            },
			
			unicode_symbol:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The symbol'
                    }
                }
            },

            iso_code:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The iso_code'
                    }
                }
            },

            numeric_iso_code:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The numeric_iso_code'
                    }
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

                <p class="introtext"><?php echo lang('cab_type_details'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/edit_taxi_tons/".$tons->id, $attrib);
                ?>
                <div class="row">
                	
                    <div class="col-md-6">
                        <div class="form-group col-md-6 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" disabled id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $tons->is_country){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        <div class="col-md-6">
                        	<div class="form-group">
                                    <?php echo lang('cab_type', 'cab_type'); ?>
                                    <div class="controls">
                                        <input type="text" id="symbol" name="symbol" value="<?= $tons->name ?>" class="form-control" readonly     />
                                    </div>
                                </div>
                        </div>
                        
                        
                        <div class="col-md-12">  
						<h2 class="box_he_de"><?= lang('tons_details') ?></h2>
						<div id="field">
                        	
                            <div id="field0">
                            
                            
                                <div class="form-group col-md-4 col-xs-12">
                                    <?= lang('tons', 'tons'); ?>
                                    <?php echo form_input('tons[]', '', 'class="form-control num" id="tons" onkeyup="checkNum(this)" '); ?>
                                </div>
                               
                                
                                <div class="form-group col-md-4 col-xs-12">
                                    <?= lang('shift', 'shift'); ?>
                                    <?php
                                    $f['Both Shift'] = 'Both Shift';
                                    $f['Day Shift'] = 'Day Shift';
									$f['Night Shift'] = 'Night Shift';
									
                                    echo form_dropdown('shift[]', $f, '', 'class="form-control select"  id="shift" required="required"'); ?>
                                </div>
                                
                                
                                <div class="form-group col-md-4 col-xs-12">
                                    
                                    <button type="button" style="margin-top:30px;" id="add-more" name="add-more" class="btn btn-primary btn-block">Add More</button>
                                </div>
                       			
                            
                            </div>
                            <div class="clearfix"></div>
                            <?php 
							
							if($tons->pending_tons != NULL){
							$pending_tons = explode(',',$tons->pending_tons); 
							
							
							foreach($pending_tons as $ptons){
							?>
                            <div id="field0">
                            	<div class="form-group col-md-4 col-xs-12">
                                    <?= lang('tons', 'tons'); ?>
                                    <?php echo form_input('tons[]', $ptons, 'class="form-control num" readonly id="tons" onkeyup="checkNum(this)" '); ?>
                                </div>
                               
                                
                                <div class="form-group col-md-4 col-xs-12">
                                    <?= lang('shift', 'shift'); ?>
                                    <?php
                                    $f['Both Shift'] = 'Both Shift';
                                    $f['Day Shift'] = 'Day Shift';
									$f['Night Shift'] = 'Night Shift';
									
                                    echo form_dropdown('shift[]', $f, '', 'class="form-control select"  id="shift"'); ?>
                                </div>
                                 <div class="clearfix"></div>
                            </div>
                            <?php
							}
							}
							?>
                        </div>
            			</div>
                      
                    </div>
                    
                    <div class="col-md-6">
                    	<?php
						if(!empty($tons->tons)){
						?>
                    	<table class="table">
                        	<thead>
                            	<tr>
                                	<th>S.No</th>
                                	<th>Tons</th>
                                    <th>Shift</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php
								$i=1;
								foreach($tons->tons as $t){
								?>
                            	<tr>
                                	<td><?= $i ?></td>
                                    <td><?= $t->tons ?></td>
                                    <td><?= $t->shift_name ?></td>
                                </tr>
                                <?php
								$i++;
								}
								?>
                            </tbody>
                        </table>
                        <?php
						}
						?>
                    </div>
                </div>
                   

                <p><?php echo form_submit('update_tons', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    var next = 0;
	$(document).on('click', "#add-more", function(e){
 
        e.preventDefault();
        var addto = "#field";
       // var addRemove = "#field" + (next);
        next = next + 1;
        var newIn = ' <div id="field'+ next +'" name="field'+ next +'"><div class="form-group col-md-4 col-xs-12"><label>Tons</label><input type="text" name="tons[]" class="form-control num"  onkeyup="checkNum(this)" id="tons" ></div><div class="form-group col-md-4 col-xs-12"><label>Shift</label><select name="shift[]" class="form-control select"><option value="Both Shift">Both Shift</option><option value="Day Shift">Day Shift</option><option value="Night Shift">Night Shift</option></select></div><div class="form-group col-md-4 col-xs-12"><button type="button" style="margin-top:30px;"  id="remove' + (next - 1) + '" class="btn btn-danger btn-block remove-me" >Remove</button></div></div><div class="clearfix"></div>';
        var newInput = $(newIn);
        //var removeBtn = '';
       // var removeButton = $(removeBtn);
        $(addto).before(newInput);
       // $(addRemove).before(removeButton);
        $("#field" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = parseInt(this.id.charAt(this.id.length-1));
				fieldNum = parseInt(fieldNum + 1);
                var fieldID = "#field" + fieldNum;
                $(this).remove();
                $(fieldID).remove();
            });
    });


});
function checkNum(input) {
	input.value = input.value.match(/^\d+\.?\d{0,1}/);  
}

</script>
<style>
    .input-group .form-control{
        z-index:1 !important;
    }
</style>
