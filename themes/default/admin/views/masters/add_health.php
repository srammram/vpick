
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

                <p class="introtext"><?php echo lang('add_health'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/add_health", $attrib);
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
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        <div class="col-md-12">                                   
                           
                            
                            
                            
                            
                            <div class="form-group">
                                <?php echo lang('title', 'health_name'); ?>
                                <div class="controls">
                                    <input type="text" id="health_name" name="health_name" class="form-control" required
                                          />
                                </div>
                            </div>
                           
                           
                          
                            
                        </div>
                       
                       
                    </div>                   
                </div>

                <p><?php echo form_submit('add_health', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<script>
$(document).on('change', '#is_country', function(){
	
        var site = '<?php echo site_url() ?>';
		var is_country = $('#is_country').val();
	  window.location.href = site+"admin/masters/add_health?is_country="+is_country;
		
    
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
