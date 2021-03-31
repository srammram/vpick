
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

                <p class="introtext"><?php echo lang('edit_discount'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/edit_discount/".$discount->id, $attrib);
                ?>
                <div class="row">
                	
                    <div class="col-md-6">
                    	<div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $discount->is_country){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        <div class="col-md-12">                                   
                            <div class="form-group">
                                <?php echo lang('apply_type', 'discount_apply_type'); ?>
                                <?php
                               // $a[''] = array('0' => lang('all'), '1' => lang('individual'));
							    $a[''] = array('0' => lang('all'));
                                echo form_dropdown('discount_apply_type', $a, $discount->discount_apply_type, 'class="wallettype form-control" id="discount_apply_type" data-placeholder="' . lang("select") . ' ' . lang("apply_type") . '" ');
                                ?>
                            </div>
                            
                           <div class="form-group users" style="display:none">
							<?php echo lang('users', 'user_ids'); ?>
                            <select class="form-control select user_ids"  name="user_ids" id="user_ids" multiple>
                                
                                <?php
                                foreach($AllUsers as $Users){
                                ?>
                                <option value="<?= $Users->id ?>"><?= $Users->first_name ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            </div>
                            
                            
                            
                            
                            <div class="form-group">
                                <?php echo lang('title', 'discount_name'); ?>
                                <div class="controls">
                                    <input type="text" id="discount_name" name="discount_name" value="<?= $discount->discount_name ?>" class="form-control" required
                                          />
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('percentage', 'discount_percentage'); ?>
                                <div class="controls">
                                    <input type="text" id="discount_percentage" name="discount_percentage" value="<?= $discount->discount_percentage ?>" class="form-control" required
                                          />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?php echo lang('discount_type', 'discount_type'); ?>
                                <?php
                                $t[''] = array('0' => lang('All Days'), '1' => lang('Particular Days'), '2' => lang('Dates'));
                                echo form_dropdown('discount_type', $t, $discount->discount_type, 'class=" form-control" id="discount_type" data-placeholder="' . lang("select") . ' ' . lang("discount_type") . '" ');
                                ?>
                            </div>
                            
                            
                            <div class="form-group days"  <?php if($discount->discount_type != 1){ ?>style="display:none" <?php } ?>>
                                <?php echo lang('days', 'days'); ?>
                                <?php
                                $d[''] = array('Saturday' => 'Saturday','Sunday' => 'Sunday','Monday' => 'Monday','Tuesday' => 'Tuesday','Wednesday' => 'Wednesday','Thursday' => 'Thursday','Friday' => 'Friday');
                                echo form_dropdown('days', $d, $discount->days, 'class=" form-control" id="days" data-placeholder="' . lang("select") . ' ' . lang("days") . '"  ');
                                ?>
                            </div>
                            
                            <div class="form-group dates"  <?php if($discount->discount_type != 2){ ?>style="display:none" <?php } ?>>
								<?php echo lang('start_date', 'Start Date'); ?>
                                <div class="controls">
                                    <input type="text" id="start_date" value="<?= $discount->start_date ?>" name="start_date" class="form-control" onkeypress="dateCheck(this);" />
                                </div>
                         	</div>
                            <div class="form-group dates"  <?php if($discount->discount_type != 2){ ?>style="display:none" <?php } ?>>
								<?php echo lang('end_date', 'End Date'); ?>
                                <div class="controls">
                                    <input type="text" id="end_date" value="<?= $discount->end_date ?>"  name="end_date" class="form-control" onkeypress="dateCheck(this);"  />
                                </div>
                            </div>
                          
                            
                        </div>
                       
                       
                    </div>   
                </div>
                   

                <p><?php echo form_submit('update_discount', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<style>
    .input-group .form-control{
        z-index:1 !important;
    }
</style>
<script>
$(document).on('change', '#is_country', function(){
	
        var site = '<?php echo site_url() ?>';
		var is_country = $('#is_country').val();
	  window.location.href = site+"admin/masters/add_discount?is_country="+is_country;
		
    
});
</script>

<script>
$(document).on('change', '#discount_apply_type', function(){
	var discount_apply_type = $('#discount_apply_type').val();
	alert(discount_apply_type);
	if(discount_apply_type == 1){
		$('.users').show();
	}else{
		$('.users').hide();
	}
});
$(document).on('change', '#discount_type', function(){
	var discount_type = $('#discount_type').val();
	if(discount_type == 1){
		$('.days').show();
		$('.dates').hide();
	}else if(discount_type == 2){
		$('.dates').show();
		$('.days').hide();
	}else{
		$('.days').hide();
		$('.dates').hide();
	}
});
$(document).ready(function(){
	
	
	
	var dateFormat =  "dd/mm/yy";
		
	var start_date = $("#start_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: true,
		changeYear: true,
		
		minDate: 0,
		numberOfMonths: 1,
		yearRange: '-100:+0',
		
	})
	.on("change", function() {
		end_date.datepicker("option", "maxDate", getDate(this));
	});
	
	var end_date = $("#end_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: true,
		changeYear: true,
		
		minDate: 0,
		numberOfMonths: 1
	})
	.on("change", function() {
		start_date.datepicker("option", "maxDate", getDate(this));
	});
	

});

</script>